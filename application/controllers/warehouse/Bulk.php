<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class Bulk extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_Picklist");
        $this->load->model("m_PicklistDetail");
        $this->load->model("m_bulk");
        $this->load->model("m_bulkdetail");
        $this->load->model("m_accessmenu");
        $this->load->library('prints');
        $this->load->library('barcode');
        $this->load->library("token");
    }

    public function index() {
        try {
            $data['id_dept'] = 'BULK';
            $this->load->view('warehouse/v_bulk', $data);
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function add() {
        $data['id_dept'] = 'BULK';
        $this->load->view('warehouse/v_bulk_scan', $data);
    }

    public function edit($id = null) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['id_dept'] = 'BULK';
            $data["ids"] = $id;
            $data['picklist'] = $this->m_Picklist->getDataByID(['picklist.no' => $kode_decrypt, 'status' => 'validasi', 'type_bulk_id' => 1]);

            $this->load->view('warehouse/v_bulk_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function data_bulking() {
        try {
            $data = array();

            $nopl = $this->input->post("no_pl");
            $condition = ['bulk.no_pl' => $nopl];
            $list = $this->m_bulk->listBulkDetail($condition);
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array(
                    $no,
                    $field->no_bulk,
                    $field->jumlah_qty ?? 0,
                    $field->total_qty ?? 0,
                    "<button type='button' class='btn btn-default btn-sm print-ballid' data-id='" . $field->no_bulk . "'><i class='fa fa-print'></i></button>"
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_bulk->getCountAllDataBulk($condition),
                "recordsFiltered" => $this->m_bulk->getCountDataFilteredBulk($condition),
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

    public function data() {
        try {
            $data = array();
            $condition = ['status' => 'validasi', 'type_bulk_id' => 1];
            $list = $this->m_Picklist->getData(false, $condition);
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->no);
                $no++;
                $row = array(
                    $no,
                    '<a href="' . base_url('warehouse/bulk/edit/' . $kode_encrypt) . '">' . $field->no . '</a>',
                    $field->nama,
                    $field->tanggal_input,
                    $field->jenis_jual,
                    $field->keterangan,
                    $field->sales_nama,
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_Picklist->getCountAllData($condition),
                "recordsFiltered" => $this->m_Picklist->getCountDataFiltered($condition),
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

    public function data_bulk() {
        try {
            $data = array();
            $condition = ['no_pl' => $this->input->post("pl")];
            $list = $this->m_bulk->getData($condition);
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
            }
        } catch (Exception $ex) {
            
        }
    }

    public function add_bulk() {
        try {
            $data["pl"] = $this->input->post("pl");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $this->load->view('modal/v_bulk_add', $data, true)]));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function save_add_bulk() {
        try {
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);
            $data = array(
                'no_pl' => $this->input->post('pl'),
                'tanggal_input' => date('Y-m-d H:i:s'),
                'user' => ($users["nama"] ?? $username)
            );
            if (!$no_bulk = $this->token->noUrut('bulk', date('ym'), true)->generate('', '%03d')->get()) {
                throw new \Exception("No Bulk tidak terbuat", 500);
            }
            $data['no_bulk'] = $no_bulk;
            $this->_module->startTransaction();
            $insert = $this->m_bulk->save($data);
            if (!empty($insert)) {
                throw new Exception("Gagal Membuat BAL ", 500);
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history($sub_menu, $data['no_bulk'], 'create', ($users["nama"] ?? $username) . ' Menambahkan bal / Bulk.', $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Bal / Bulk berhasil ditambahkan', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function bulking() {
        try {
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);

            $status = $this->input->post('status');
            $nopl = $this->input->post('pl');
            $value = $this->input->post('search');
            $bulk = $this->input->post('no_bulk');
            $data = null;
            $message = "Berhasil";
            switch ($status) {
                case "pl":
                    $data = $this->m_Picklist->getDataByID(['picklist.no' => $value, 'type_bulk_id' => 1]);
                    if (empty($data) || is_null($data)) {
                        throw new Exception("No Picklist " . $value . "  Tidak Ditemukan", 500);
                    }
                    if ($data->status !== 'validasi') {
                        throw new Exception("No Picklist " . $value . " Belum tervalidasi", 500);
                    }
                    $message = "Berhasil, No Picklist ditemukan";
                    break;
                case "bulk":
                    if (empty($nopl)) {
                        throw new Exception("Tentukan Dulu No Picklist" . $nopl, 500);
                    }
                    $data = $this->m_bulk->getDataDetail(['no_pl' => $nopl, 'no_bulk' => $value]);
                    if (empty($data) || is_null($data)) {
                        throw new Exception("Bulk Tidak Ditemukan di " . $nopl, 500);
                    }
                    $message = "Berhasil, Bulk ditemukan";
                    break;
                case "item":
                    $check = $this->m_PicklistDetail->detailData(['no_pl' => $nopl, 'barcode_id' => $value]);
                    if (empty($check) || is_null($check)) {
                        throw new Exception("Barcode Tidak Ditemukan di " . $nopl, 500);
                    }
                    if ($check->valid !== "validasi") {
                        throw new Exception("Barcode belum divalidasi", 500);
                    }
                    $users = $this->session->userdata('nama');
                    $sub_menu = $this->uri->segment(2);
                    $dataInsert = [
                        'bulk_no_bulk' => $bulk,
                        'barcode' => $value,
                        'tanggal_input' => date('Y-m-d H:i:s'),
                        'user' => $users["nama"] ?? ""
                    ];

                    $checkExist = $this->m_bulkdetail->getDataDetail(['barcode' => $value]);
                    if ($checkExist) {
                        if ($checkExist->bulk_no_bulk === $bulk) {
                            throw new Exception("Barcode " . $value . " Duplikat", 500);
                        }
                        $insrt = $this->m_bulkdetail->updateBulkDetail(['barcode' => $value], ['bulk_no_bulk' => $bulk]);
                        $message = "Berhasil Pindah Item Pada BAL / Bulk";
                    } else {
                        $insrt = $this->m_bulkdetail->insert($dataInsert);
                        $message = "Berhasil Tambah Item Pada BAL / Bulk";
                    }

                    if (!empty($insrt)) {
                        if (strpos($insrt, 'Duplicate') !== false) {
                            $insrt = "Barcode " . $value . " duplikat.";
                        }
                        throw new \Exception($insrt, 500);
                    }

                    $this->_module->gen_history($sub_menu, $bulk, 'edit', 'Menambahkan Barcode ' . $value, $users["nama"] ?? "");
                    break;
                case "cancel":
                    if (empty($nopl) || is_null($nopl)) {
                        throw new Exception("Silahkan Pilih dulu no Picklist", 500);
                    }
                    if (empty($bulk) || is_null($bulk)) {
                        throw new Exception("Silahkan Pilih dulu no Bulk", 500);
                    }
                    $query = ["barcode" => $value, "bulk_no_bulk" => $bulk];
                    $check = $this->m_bulkdetail->getDataDetail($query);
                    if (empty($check) || is_null($check)) {
                        throw new Exception("Data Barcode " . $value . " tidak ditemulan dibulk " . $bulk, 500);
                    }
                    $this->m_bulkdetail->delete($query);
                    $message = "Berhasil Membatalkan Barcode " . $value . " Pada Bulk " . $bulk;
                    $this->_module->gen_history($sub_menu, $bulk, 'edit', ($users["nama"] ?? "") . ' Membatalkan Barcode ' . $value . ' Pada Bulk ' . $bulk, $username);
                    break;
                default:
                    break;
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $message, 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $data, 'status' => $status)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function show_net_gross() {
        try {
            $pl = $this->input->post("pl");
            $data["data"] = $this->m_bulk->listBulkDetail(['bulk.no_pl' => $pl]);
            $view = $this->load->view('warehouse/v_bulk_net_gross', $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $view]));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function net_gross() {
        try {
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);

            $net = $this->input->post("net") ?? [];
            $gross = $this->input->post("gross") ?? [];
            $this->_module->startTransaction();
            foreach ($net as $key => $value) {
                foreach ($value as $keys => $values) {
                    $update = [
                        "net_weight" => $values,
                        "gross_weight" => $gross[$key][$keys]
                    ];
                    $this->m_bulk->updateNetGross(
                            ["no_bulk" => $key],
                            $update
                    );

                    $this->_module->gen_history($sub_menu, $key, 'edit', ($users["nama"] ?? "") . ' Mengubah Net Gross Pada Bulk ' . $key, $username);
                }
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil update net dan gross weight', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function bulking_data() {
        try {
            $pl = $this->input->post('pl');
            $data['data'] = $this->m_bulkdetail->getDataListBulk(['b.no_pl' => $pl]);
            $pers = $this->load->view('warehouse/v_bulk_scan_table', $data);
            echo json_encode($pers);
        } catch (Exception $ex) {
            
        }
    }

    protected function getNoPl($nopl) {
        try {
            $data = $this->m_Picklist->getDataByID(['picklist.no' => $nopl, 'status' => 'validasi', 'type_bulk_id' => 1]);
            return $data;
        } catch (Exception $ex) {
            
        }
    }
}
