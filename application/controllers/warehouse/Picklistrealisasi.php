<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Picklistrealisasi
 *
 * @author RONI
 */
class Picklistrealisasi extends MY_Controller {

    //put your code here

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_Picklist");
        $this->load->model("m_PicklistDetail");
        $this->load->helper('array');
    }

    protected $valid = [
        'realisasi' => [
            'before' => 'draft',
            'after' => 'validasi'
        ],
        'validasi' => [
            'before' => 'realisasi',
            'after' => 'delivery'
        ],
    ];

    protected function statusColor($status) {
        $data = [
            'draft' => 'Yellow',
            'realisasi' => 'Blue',
            'validasi' => 'Green'
        ];
        return $data[$status] ?? 'red';
    }

    public function index() {
        $data['id_dept'] = 'PLR';
        $data['submenu'] = 'picklistrealisasi';
        $this->load->view('warehouse/v_picklist_realisasi', $data);
    }

    public function data() {
        try {
            $data = array();
            $list = $this->m_Picklist->getData(true);
            $no = $_POST['start'];
            $submenu = $_POST['submenu'];
            foreach ($list as $field) {

                $kode_encrypt = encrypt_url($field->id);
                $no++;
                $realisasi = $this->_persentase($field->total_item ?? 0, $field->st, ['realisasi', 'validasi']);
                $validasi = $this->_persentase($field->total_item ?? 0, $field->st, 'validasi');
                $row = array(
                    $no,
                    '<a href="' . base_url('warehouse/' . $submenu . '/edit/' . $kode_encrypt) . '">' . $field->no . '</a>',
                    $field->tanggal_input,
                    $field->jenis_jual,
                    $field->bulk_nama,
                    $field->keterangan,
                    $field->sales_nama,
                    $field->total_item,
                    $realisasi[0] . ' (' . number_format($realisasi[1], 1) . '%)',
                    $validasi[0] . ' (' . number_format($validasi[1], 1) . '%)',
//                    '<div class="miniBar">' . $this->_persentase($field->total_item ?? 0, $field->st) . '</div>'
//                    $this->m_PicklistDetail->getCountDataFiltered(['no_pl'=>$field->no])
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_Picklist->getCountAllData(),
                "recordsFiltered" => $this->m_Picklist->getCountDataFiltered(),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
        }
    }

    public function data_detail() {
        try {
            $pl = $this->input->post('filter');

            $condition = ['no_pl' => $pl];
            $list = $this->m_PicklistDetail->getData($condition);
            $no = $_POST['start'];
            $data = [];
            foreach ($list as $field) {
                $no++;
                $row = array(
                    $no,
                    $field->barcode_id,
                    $field->corak_remark,
                    $field->warna_remark,
                    $field->qty . " " . $field->uom,
                    $field->qty2 . " " . $field->uom2,
                    $field->lokasi_fisik,
                    $field->valid,
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_PicklistDetail->getCountAllData($condition),
                "recordsFiltered" => $this->m_PicklistDetail->getCountDataFiltered($condition),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }

    public function update() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $nama = $this->session->userdata('nama');
            $pl = $this->input->post('pl');
            $barcode = $this->input->post('search');
            $menu = $this->input->post('on_menu');
            $valid_date = $this->input->post('valid_date');
            $statusWhere = ['no_pl' => $pl, 'barcode_id' => $barcode];

            $this->_module->startTransaction();

            $item = $this->m_PicklistDetail->detailData($statusWhere, true);
            if (empty($item)) {
                throw new Exception('Data Barcode Tidak Ditemukan', 500);
            }
            if ($item->valid !== $this->valid[$menu]['before']) {
                throw new Exception('Data Barcode ' . $barcode . ' pada PL ' . $pl . ' tidak dalam status ' . $this->valid[$menu]['before'], 500);
            }
            $update = ['valid' => $menu, 'valid_date' => $valid_date];
            $result = $this->m_PicklistDetail->updateStatus($statusWhere, $update);
            if (!empty($result)) {
                throw new \Exception($result, 500);
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->m_Picklist->update(['status' => $menu], ['no' => $pl]);
            $this->_module->gen_history($sub_menu, $pl, 'edit', ($nama["nama"] ?? "") . ' Melakukan Realisasi barcode ' . $barcode, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => [])));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function edit($id = null) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['id_dept'] = 'PLR';
            $data["ids"] = $id;
            $data['picklist'] = $this->m_Picklist->getDataByID(['picklist.id' => $kode_decrypt, 'status !=' => 'cancel']);
            if (is_null($data["picklist"])) {
                throw new Exception();
            }
            $data['view_cancel'] = $this->load->view('modal/v_picklist_item_cancel', [], true);
            $this->load->view('warehouse/v_picklist_realisasi_proses', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function update_status() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $pl = $this->input->post('pl');
            $barcode = $this->input->post('barcode');
            $menu = $this->input->post('on_menu');

            $condition = ['no_pl' => $pl, 'barcode_id' => $barcode];
            $item = $this->m_PicklistDetail->detailData($condition);
            if (empty($item)) {
                throw new Exception('Data Barcode Tidak Ditemukan', 500);
            }
            if ($item->valid !== $menu) {
                throw new Exception('Data Barcode ' . $barcode . ' pada PL ' . $pl . ' tidak dalam status ' . $menu, 500);
            }

            $status = $this->m_PicklistDetail->updateStatus($condition, ['valid' => $this->valid[$menu]['before']]);
            if ($status !== "") {
                throw new Exception('Gagal Cancel Item Realisasi', 500);
            }
            $this->_module->gen_history($sub_menu, $pl, 'edit', logArrayToString('; ', array_merge($condition, ['to' => $this->valid[$menu]['before']])), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function persentase() {
        try {
            $data = $this->m_PicklistDetail->statusCount(['no_pl' => $this->input->post('pl')]);

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode([$this->persentaseCount($data, 'draft'), $this->persentaseCount($data, 'realisasi'), $this->persentaseCount($data, 'validasi')]));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    protected function persentaseCount(array $data, $keys): int {

        foreach ($data as $key => $value) {
            if ($value->valid === $keys) {

                return (int) $value->cnt;
            }
        }
        return 0;
    }

    protected function _persentase($total_item, $value, $onlyStatus = null): array {
        $data = explode("|", $value);
        $result = [0, 0];
        if ($data < 1) {
            return $result;
        }
        foreach ($data as $key => $values) {
            $list = explode(',', trim($values));
            if (is_string($onlyStatus)) {
                if ($onlyStatus === $list[0]) {
                    return [trim($list[1]), number_format((trim($list[1]) / $total_item) * 100, 1)];
                }
            } else if (is_array($onlyStatus)) {
                foreach ($onlyStatus as $value) {
                    if ($value === $list[0]) {
                        $result[0] += (int) trim($list[1]);
                        $result[1] += (trim($list[1]) / $total_item) * 100;
                    }
                }
            }
        }
        return $result;
    }

//    protected function _persentase($total_item, $value, $onlyStatus = null): array {
//        
//        
//        $data = explode("|", $value);
//        $result = [0, 0];
//        $persentase = 0.0;
//        if ($data < 1) {
////            return '<div class="miniBarProgress" style="left: 0; width: 0%; background-color: red;"><span class="tooltiptext">0</span></div>';
//            return $result;
//        }
//        foreach ($data as $key => $values) {
//            $list = explode(',', trim($values));
////            $_persen = $persentase;
////            $persentase += ((trim($list[1]) / $total_item) * 100);
//            if ($onlyStatus === $list[0]) {
//                return [trim($list[1]), number_format((trim($list[1]) / $total_item) * 100, 1)];
//            }
////            $result .= '<div class="miniBarProgress" style="left: ' . $_persen . '%; width: ' . ($persentase - $_persen) . '%; background-color: ' . $this->statusColor($list[0]) . ';"><span class="tooltiptext">' . $list[0] . ' ' . $list[1] . '</span></div>';
//        }
//        return $result;
//    }
}
