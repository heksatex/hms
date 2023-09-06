<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class WaGroup extends MY_Controller {

    protected $valForm = array(
        [
            'field' => 'wa_group',
            'label' => 'Wa group',
            'rules' => ['trim', 'required', 'max_length[25]'],
            'errors' => ['required' => '{field} Harus Diisi',
                'max_length' => '{field} maksimal {param} karakter.',
            ]
        ]
    );

    public function __construct() {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("m_WaGroup"); //load model m_Group
        $this->load->model("_module");
//        $this->load->library('response');
    }

    public function index() {
        $data['id_dept'] = 'MUSR';
        return $this->load->view('setting/v_wa_group', $data);
    }

    public function add() {
        $data['id_dept'] = 'MUSR';
        return $this->load->view('setting/v_wa_group_add', $data);
    }

    public function edit($id = null) {
        try {
//            $id ?? show_404();

            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['wa'] = $this->m_WaGroup->getDataByID($kode_decrypt);
            $data["id"] = $id;
            $data['id_dept'] = 'MUSR';
            $data['mms'] = $this->_module->get_data_mms_for_log_history($data['id_dept']);
            return $this->load->view('setting/v_wa_group_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function simpan() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis');
            }
            $this->load->library('form_validation');
            array_push($this->valForm[0]['rules'], "is_unique[wa_group.wa_group]");
            $this->valForm[0]['errors'] = array_merge($this->valForm[0]['errors'], ['is_unique' => '{field} sudah terdaftar']);
            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0]);
            }
            $wagroup = $this->input->post("wa_group");
            $this->m_WaGroup->simpan(addslashes($wagroup));
            $this->_module->gen_history($sub_menu, $wagroup, 'create', 'Membuat WA Group ' . $wagroup, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil')));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function update() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis');
            }
            $kode_decrypt = decrypt_url($this->input->post("id"));
            if (!$kode_decrypt) {
                throw new \Exception("data tidak ditemukan");
            }
            $this->load->library('form_validation');
            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0]);
            }
            $wagroup = $this->input->post("wa_group");
            if (!$this->m_WaGroup->update($kode_decrypt, addslashes($wagroup))) {
                throw new \Exception("Gagal Merubah Data");
            }
            $this->_module->gen_history($sub_menu, $wagroup, 'Update', 'Edit WA Group ' . $wagroup, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil')));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function getData() {
        try {
            $data = array();
            $list = $this->m_WaGroup->getData();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->id);
                $no++;
                $row = array(
                    $no,
                    '<a href="' . base_url('setting/wa_group/edit/' . $kode_encrypt) . '">' . $field->wa_group . '</a>',
                    date('D m, Y H:i:s', strtotime($field->created_at)),
                );
                $data[] = $row;
            }

            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
        }
    }
}
