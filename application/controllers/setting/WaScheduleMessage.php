<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class WaScheduleMessage extends MY_Controller {

    protected $valForm = array(
        [
            'field' => 'pesan',
            'label' => 'Pesan',
            'rules' => ['trim', 'required', 'min_length[10]'],
            'errors' => [
                'required' => '{field} Harus Diisi',
                'min_length' => 'Pesan minimal {param} karakter.',
            ]
        ],
        [
            'field' => 'nama',
            'label' => 'Nama',
            'rules' => ['required', 'max_length[100]'],
            'errors' => [
                'required' => '{field} Harus Diisi',
                'max_length' => '{field} Maksimal {param} karakter.',
            ]
        ],
        [
            'field' => 'waktu_kirim',
            'label' => 'Waktu Kirim',
            'rules' => ['required', 'regex_match[#^[01]?[0-9]|2[0-3]:[0-5][0-9](:[0-5][0-9])?$#]'],
            'errors' => [
                'required' => 'Waktu Kirim Harus Diisi',
                'regex_match' => 'Format {field} Harus HH:MM'
            ]
        ],
        [
            'field' => 'group[]',
            'label' => 'Group',
            'rules' => ['required'],
            'errors' => [
                'required' => '{field} Harus Diisi',
            ]
        ],
        [
            'field' => 'hari[]',
            'label' => 'Hari',
            'rules' => ['callback_check_berdasarkan[tanggal]'],
        ]
    );

    public function check_berdasarkan($str, $tanggal): bool {
        if (empty($str) && empty($this->input->post($tanggal)) && empty($this->input->post('custom'))) {
            $this->form_validation->set_message('check_berdasarkan', 'Hari / Tanggal / Custom harap diisi salah satu');
            return false;
        }
        return true;
    }

    protected $days = array(
        'Monday' => 'Monday',
        'Tuesday' => 'Tuesday',
        'Wednesday' => 'Wednesday',
        'Thursday' => 'Thursday',
        'Friday' => 'Friday',
        'Saturday' => 'Saturday',
        'Sunday' => 'Sunday'
    );
    protected $customSchedule = array(
        'last_month' => 'last_month'
    );

    protected function dates(): array {
        $arr = [];
        for ($i = 1; $i <= 31; $i++) {
            $arr[$i] = $i;
        }

        return $arr;
    }

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("m_WaScheduleMessage");
        $this->load->model("_module");
        $this->load->model("m_WaGroup");
        $this->load->model("m_WaTemplate");
    }

    public function index() {
        $data['id_dept'] = 'MWSSM';
        return $this->load->view('setting/v_wa_schedule', $data);
    }

    public function add() {
        $data['id_dept'] = 'MWSSM';
        $data['group'] = $this->m_WaGroup->getDataQuery();
        $data['days'] = $this->days;
        $data['customSchedule'] = $this->customSchedule;
        $data['template_footer'] = $this->m_WaTemplate->getFooterTemplate();
        return $this->load->view('setting/v_wa_schedule_add', $data);
    }

    public function edit($id = null) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['group'] = $this->m_WaGroup->getDataQuery();
            $data['days'] = $this->days;
            $data["id"] = $id;
            $data['datas'] = $this->m_WaScheduleMessage->getDataByID($kode_decrypt);
            $data['customSchedule'] = $this->customSchedule;
            $data['template_footer'] = $this->m_WaTemplate->getFooterTemplate();
            if (!is_null($data['datas']->groupid)) {
                $data['datas']->groupid = explode(',', $data['datas']->groupid);
            }
            if (!is_null($data['datas']->day)) {
                $data['datas']->day = explode(',', $data['datas']->day);
            }
            $data['id_dept'] = 'MWSSM';
            return $this->load->view('setting/v_wa_schedule_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function simpan() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }

            $nama = $this->input->post('nama');
            $pesan = $this->input->post('pesan');
            $waktu = $this->input->post('waktu_kirim');
            $group = $this->input->post('group');
            $hari = (array) $this->input->post('hari');
            $footer = $this->input->post('footer');
            $setLastExe = ($waktu <= date("H:i:s")) ? false : true;
            $berdasarkan = $this->data_berdasarkan([$hari, $this->input->post('tanggal'), $this->input->post('custom')]);
            $this->_module->startTransaction();
            if ($status = $this->m_WaScheduleMessage->simpan($pesan, $waktu, $nama, $setLastExe, $footer)) {
                foreach ($berdasarkan as $key => $value) {
                    if (!$this->m_WaScheduleMessage->simpanDays($status, $value)) {
                        throw new Exception('Gagal Menyimpan Data,Cek Hari Yang Dipilih', 500);
                        break;
                    }
                }
                foreach ($this->input->post('group') as $key => $value) {
                    if (!$this->m_WaScheduleMessage->simpanGroup($status, $value)) {
                        throw new Exception('Gagal Menyimpan Data,Cek Group Yang Dipilih', 500);
                        break;
                    }
                }
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history($sub_menu, 'wa_schedule', 'create', 'Membuat Pesan Schedule ' . $pesan, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function update() {
        try {

            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kode_decrypt = decrypt_url($this->input->post('id'));
            if (!$kode_decrypt) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $nama = $this->input->post('nama');
            $pesan = $this->input->post('pesan');
            $waktu = $this->input->post('waktu_kirim');
            $waktu_sblm = $this->input->post('waktu_kirim_sblm');
            $group = $this->input->post('group');
            $hari = (array) $this->input->post('hari');
            $footer = $this->input->post('footer');
            $berdasarkan = $this->data_berdasarkan([$hari, $this->input->post('tanggal'), $this->input->post('custom')]);
            $this->_module->startTransaction();
            $setLastExe = ($waktu <= $waktu_sblm) ? false : true;
            if (!$this->m_WaScheduleMessage->update($kode_decrypt, $pesan, $waktu, $nama, $setLastExe, $footer)) {
                throw new \Exception("Gagal Mengubah Data", 500);
            }
            $this->m_WaScheduleMessage->deleteDays($kode_decrypt);

            foreach ($berdasarkan as $key => $value) {
                if (!$this->m_WaScheduleMessage->simpanDays($kode_decrypt, $value)) {
                    throw new Exception('Gagal Mengubah Data,Cek Hari Yang Dipilih', 500);
                    break;
                }
            }
            $this->m_WaScheduleMessage->deleteGroup($kode_decrypt);
            foreach ($group as $key => $value) {
                if (!$this->m_WaScheduleMessage->simpanGroup($kode_decrypt, $value)) {
                    throw new Exception('Gagal Mengubah Data,Cek Group Yang Dipilih', 500);
                    break;
                }
            }


            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Mengubah Data', 500);
            }
            $this->_module->gen_history($sub_menu, 'wa_schedule', 'Edit', 'Mengubah Pesan Schedule ' . $pesan, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function delete() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kode_decrypt = decrypt_url($this->input->post('id'));
            if (!$kode_decrypt) {
                throw new \Exception('Gagal Menghapus Data', 500);
            }

            $this->_module->startTransaction();
            $this->m_WaScheduleMessage->deleteSchedule($kode_decrypt);
            $this->m_WaScheduleMessage->deleteDays($kode_decrypt);
            $this->m_WaScheduleMessage->deleteGroup($kode_decrypt);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Mengubah Data', 500);
            }

            $this->_module->gen_history($sub_menu, 'wa_schedule', 'DELETE', 'Menghapus Pesan Schedule ID:' . $kode_decrypt, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function disablePesan() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kode_decrypt = decrypt_url($this->input->post('id'));
            $stt = $this->input->post("status");
            if (!$kode_decrypt) {
                throw new \Exception('Gagal Mengubah Data', 500);
            }
            $this->_module->startTransaction();
            $this->m_WaScheduleMessage->updates(["disabled" => ($stt ? "0" : "!")], ["id" => $kode_decrypt]);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Mengubah Data', 500);
            }
            $this->_module->gen_history($sub_menu, 'wa_schedule', 'update', 'Mengubah Status Pesan Schedule ', $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function getData() {
        try {
            $data = array();
            $list = $this->m_WaScheduleMessage->getData();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->id);
                $no++;
                $nama = $field->nama ?? "Default";
                $field->day = $this->orderDays($field->day);
                $disable = '<button type="button" data-toggle="tooltip" data-placement="top" title="' . ($field->disabled ? "Enable" : "Disable") . '" class="btn btn-default btn-sm disabled-sch" data-id="' . $kode_encrypt . '" data-status="' . $field->disabled . '"><i class="fa ' . ($field->disabled ? "fa-check" : "fa-close") . '"></i></button>';
                $row = array(
                    $no,
                    '<a href="' . base_url('setting/wa_schedule/edit/' . $kode_encrypt) . '">' . $nama . '</a>',
                    $field->message,
                    $field->footer,
                    $field->groupname,
                    str_replace("_", " ", $field->day),
                    $field->send_time,
                    $disable . '&nbsp' .
                    '<button type="button" class="btn btn-danger btn-sm btn-delete-doc" data-id="' . $kode_encrypt . '"><i class="fa fa-trash"></i></button> '
                );
                $data[] = $row;
            }
            $this->orderDays([]);
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_WaScheduleMessage->getCountAllData(),
                "recordsFiltered" => $this->m_WaScheduleMessage->getCountDataFiltered(),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
        }
    }

    protected function data_berdasarkan(array $val): array {
        $rst = [];
        foreach ($val as $key => $value) {
            if (is_array($value)) {
                $rst = array_merge($rst, $value);
            }
        }
        return $rst;
    }

    protected function orderDays($day): string {
        $data = array_merge($this->days, $this->customSchedule, $this->dates());
        if (is_array($day)) {
            return implode(',', $day);
        }
        $days = explode(',', $day);
        return implode(',', array_intersect($data, $days));
        ;
//        return json_encode(array_intersect($data, $days));
    }
}
