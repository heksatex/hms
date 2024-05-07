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
        $this->load->model("m_deliveryorder");
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
            $data['picklist'] = $this->m_bulk->getDataByIDPicklist(['picklist.no' => $kode_decrypt, 'status !=' => 'cancel', 'type_bulk_id' => 1]);

            $this->load->view('warehouse/v_bulk_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function data_bulking() {
        try {
            $data = array();

            $nopl = $this->input->post("no_pl");
            $condition = ['no_pl' => $nopl];
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
            log_message('error', $this->m_bulk->getCountAllDataBulk($condition));
            log_message('error', $this->m_bulk->getCountDataFilteredBulk($condition));
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
            $condition = ['type_bulk_id' => 1, 'status !=' => 'cancel'];
            $list = $this->m_bulk->getDataPicklist($condition);
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
                    "<button type='button' class='btn btn-default btn-sm print-ballid' data-id='" . $field->no . "'><i class='fa fa-print'></i><span class='tooltiptext'>Cetak Barcode Bulk</span></button>"
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_bulk->getCountAllDataPicklist($condition),
                "recordsFiltered" => $this->m_bulk->getCountDataFilteredPicklist($condition),
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

    public function print_bulk() {
        try {
            $pl = $this->input->post("pl");
            $mode = $this->input->post("print_mode");
            $condition = ['no_pl' => $pl];
            if (!is_null($this->input->post("type"))) {
                $bulk = $this->input->post("bulk");
                $condition = ['no_bulk' => $bulk];
            }
            $list = $this->m_bulk->getDatas($condition);
            $code = new Code\Code128New();
            $this->prints->setView('print/bulk/' . $mode);
            $gen_code = [];
            $codes = [];
            foreach ($list as $key => $value) {
                $codes[$key] = $value->no_bulk;
                $gen_code[$key] = $code->generate($value->no_bulk, "", 50, ($mode === "v" ? "horizontal" : "vertical"));
                $data = [
                    'barcode_id' => $codes[$key],
                    'barcode' => $gen_code[$key],
                    'pl' => $pl
                ];
                $this->prints->addDatas($data);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $this->prints->generate())));
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
            $total = $this->input->post('total') ?? 1;
            $no_bulk = "";
            $blk = $this->m_bulk->getDataDetail(['no_pl' => $this->input->post('pl')]);
            $this->_module->startTransaction();
            $check_bulk_count = 1;
            if (!empty($blk)) {
                if (strlen($blk->no_bulk) === 9) {
                    if (!$no_bulk = $this->token->noUrut('bulk', date('ym'), true)->generate('BL', '%01d')->prefixAdd("")->get()) {
                        throw new \Exception("No Bulk tidak terbuat", 500);
                    }
                } else {
                    $chk = substr($blk->no_bulk, 0, 2);
                    if ($chk === "BL") {
                        $nourut = substr($blk->no_bulk, -3);
                        switch (strlen($blk->no_bulk)) {
                            case 8:
                                $no_bulk = substr($blk->no_bulk, 0, 5);
                                break;
                            case 7:
                                $no_bulk = substr($blk->no_bulk, 0, 4);
                                break;
                            case 6:
                                $no_bulk = substr($blk->no_bulk, 0, 3);
                                break;
                            default :
                                $no_bulk = substr($blk->no_bulk, 0, 2);
                        }

                        $check_bulk_count += (int) ($nourut);
                    } else {
                        if (!$no_bulk = $this->token->noUrut('bulk', date('ym'), true)->generate('BL', '%01d')->prefixAdd("")->get()) {
                            throw new \Exception("No Bulk tidak terbuat", 500);
                        }
                    }
                }
            } else {
                if (!$no_bulk = $this->token->noUrut('bulk', date('ym'), true)->generate('BL', '%01d')->prefixAdd("")->get()) {
                    throw new \Exception("No Bulk tidak terbuat", 500);
                }
            }
            $data = [];
            for ($i = 0; $i < $total; $i++) {
                $data[] = [
                    'no_pl' => $this->input->post('pl'),
                    'tanggal_input' => date('Y-m-d H:i:s'),
                    'user' => ($users["nama"] ?? $username),
                    'no_bulk' => sprintf($no_bulk . '%03d', ($check_bulk_count + $i))
                ];
            }
            $insert = $this->m_bulk->saveBatch($data);
            if (!empty($insert)) {
                throw new Exception("Gagal Membuat BAL ", 500);
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history($sub_menu, $this->input->post('pl'), 'create', ($users["nama"] ?? $username) . ' Menambahkan bal / Bulk.', $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Bal / Bulk berhasil ditambahkan', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
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
            $do = $this->input->post('doid');
            $data = null;
            $message = "Berhasil";
            switch ($status) {
                case "pl":
                    $data = $this->m_Picklist->getDataByID(['picklist.no' => $value, 'type_bulk_id' => 1], "DO");
                    if (empty($data) || is_null($data)) {
                        throw new Exception("No Picklist " . $value . "  Tidak Ditemukan", 500);
                    }
                    if ($data->status !== 'validasi') {
                        throw new Exception("No Picklist " . $value . " Dalam Status " . $data->status, 500);
                    }
                    $message = "Berhasil, No Picklist ditemukan";
                    break;
                case "bulk":
                    if (empty($nopl) || is_null($nopl)) {
                        throw new Exception("Tentukan Dulu No Picklist" . $nopl, 500);
                    }
                    if (!empty($do)) {
//                        throw new Exception("No " . $nopl . ' Sudah Masuk Delivery Order', 500);
                    }
                    $data = $this->m_bulk->getDataDetail(['no_pl' => $nopl, 'no_bulk' => $value]);
                    if (empty($data) || is_null($data)) {
                        throw new Exception("Bulk Tidak Ditemukan di " . $nopl, 500);
                    }
                    $data->total_item = $this->m_bulkdetail->getTotalItem(['bulk_no_bulk' => $value]);
                    $message = "Berhasil, Bulk ditemukan";
                    break;
                case "item":
                    if (!empty($do)) {
                        throw new Exception("No " . $nopl . ' Sudah Masuk Delivery Order', 500);
                    }
                    $check = $this->m_PicklistDetail->detailData(['no_pl' => $nopl, 'barcode_id' => $value, 'valid !=' => 'cancel']);
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
                        'picklist_detail_id' => $check->id,
                        'barcode' => $value,
                        'tanggal_input' => date('Y-m-d H:i:s'),
                        'user' => $users["nama"] ?? ""
                    ];

                    $checkExist = $this->m_bulkdetail->getDataDetail(['barcode' => $value]);
                    if ($checkExist) {
                        if ($checkExist->bulk_no_bulk === $bulk) {
                            throw new Exception("Barcode " . $value . " Duplikat", 505);
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
                        throw new \Exception($insrt, 505);
                    }
                    $this->_module->gen_history($sub_menu, $bulk, 'edit', 'Menambahkan Barcode ' . $value, $users["nama"] ?? "");
                    break;
                case "cancel":
                    if (!empty($do)) {
                        throw new Exception("No " . $nopl . ' Sudah Masuk Delivery Order', 500);
                    }
                    $data = new \stdClass();
                    if (empty($nopl) || is_null($nopl)) {
                        throw new Exception("Silahkan Pilih dulu no Picklist", 500);
                    }
//                    if (empty($bulk) || is_null($bulk)) {
//                        throw new Exception("Silahkan Pilih dulu no Bulk", 500);
//                    }
                    $query = ["barcode" => $value];
                    $check = $this->m_bulkdetail->getDataDetail(array_merge($query, ["no_pl" => $nopl]), true);
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
            $error_type = "danger";
            switch ($ex->getCode()) {
                case 505:
                    $error_type = 'warning';

                    break;

                default:
                    break;
            }
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => $error_type)));
        }
    }

    public function show_net_gross() {
        try {
            $pl = $this->input->post("pl");
            $data["data"] = $this->m_bulk->listBulkDetail(['no_pl' => $pl]);
            $data["pl"] = $pl;
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
            $pl = $this->input->post("pl");
            $dataPl = $this->m_deliveryorder->getDataDetail(['a.no_picklist' => $pl]);
            if (!empty($dataPl)) {
//                throw new Exception("No " . $pl . ' Sudah Masuk Delivery Order', 500);
            }
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
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function bulking_data() {
        try {
            $pl = $this->input->post('pl');
            $bulk = $this->input->post('bulk');
            $condition = ['b.no_pl' => $pl];
            $data["totalan"] = $this->m_bulkdetail->getTotalItemBulk($condition);
//            $data["total_item"] = $this->m_bulkdetail->getTotalItem($condition);
            $data["total_item_bulk"] = $this->m_bulkdetail->getTotalItem(array_merge($condition, ['no_bulk' => $bulk]));
            $data["bulk"] = $bulk;
            $pers = [];
            if (empty($bulk) || is_null($bulk)) {
                $data['data'] = $this->m_bulkdetail->getDataListBulk($condition);
                $pers = $this->load->view('warehouse/v_bulk_scan_table', $data);
            } else {
                $data['data'] = $this->m_bulkdetail->getDataListBulk(array_merge($condition, ['no_bulk' => $bulk]), true);
                $pers = $this->load->view('warehouse/v_bulk_scan_table', $data);
            }

            echo json_encode($pers);
        } catch (Exception $ex) {
            
        }
    }

    //    public function save_add_bulk_() {
//        try {
//            $username = $this->session->userdata('username');
//            $users = $this->session->userdata('nama');
//            $sub_menu = $this->uri->segment(2);
//            $total = $this->input->post('total') ?? 1;
//            $this->_module->startTransaction();
////            for ($i = 0; $i < $total; $i++) {
////                $data = array(
////                    'no_pl' => $this->input->post('pl'),
////                    'tanggal_input' => date('Y-m-d H:i:s'),
////                    'user' => ($users["nama"] ?? $username)
////                );
////                if (!$no_bulk = $this->token->noUrut('bulk', date('ym'), true)->generate('BL', '%03d')->get()) {
////                    throw new \Exception("No Bulk tidak terbuat", 500);
////                }
////                $data['no_bulk'] = $no_bulk;
////                $insert = $this->m_bulk->save($data);
////                if (!empty($insert)) {
////                    throw new Exception("Gagal Membuat BAL ", 500);
////                }
////            }
//            //new generate
//
//            $check_bulk_count = 1;
//            $blk = $this->m_bulk->getDataDetail(['no_pl' => $this->input->post('pl')]);
////            $check_bulk_count = $this->m_bulk->getCountAllData(['no_pl' => $this->input->post('pl')]);
//            $format = "";
//            if (!empty($blk)) {
//                $chk = substr($blk->no_bulk, 0, 2);
//                if ($chk === "BL") {
//                    
//                } else {
//                    $nourut = substr($blk->no_bulk, -3);
//                    switch (strlen($chk)) {
//                        case 7:
//                            $format = substr($blk->no_bulk, 0, 4);
//                            break;
//                        case 6:
//                            $format = substr($blk->no_bulk, 0, 3);
//                            break;
//                        case 5:
//                            $format = substr($blk->no_bulk, 0, 2);
//                            break;
//                        default :
//                            $format = substr($blk->no_bulk, 0, 1);
//                    }
//                }
////                $dtExbulk = str_split($blk->no_bulk, 9);
////                $no_bulk = $dtExbulk[0];
////                $check_bulk_count += (int) ($dtExbulk[1] ?? 0);
//            } else {
//                if (!$no_bulk = $this->token->noUrut('bulk', date('ym'), true)->generate('BL', '%01d')->get()) {
//                    throw new \Exception("No Bulk tidak terbuat", 500);
//                }
//            }
//            $data = [];
//            for ($i = 0; $i < $total; $i++) {
//                $data[] = [
//                    'no_pl' => $this->input->post('pl'),
//                    'tanggal_input' => date('Y-m-d H:i:s'),
//                    'user' => ($users["nama"] ?? $username),
//                    'no_bulk' => sprintf($no_bulk . '%03d', ($check_bulk_count + $i))
//                ];
//            }
//            $insert = $this->m_bulk->saveBatch($data);
//            if (!empty($insert)) {
//                throw new Exception("Gagal Membuat BAL ", 500);
//            }
//
//            if (!$this->_module->finishTransaction()) {
//                throw new \Exception('Gagal Menyimpan Data', 500);
//            }
//            $this->_module->gen_history($sub_menu, $this->input->post('pl'), 'create', ($users["nama"] ?? $username) . ' Menambahkan bal / Bulk.', $username);
//            $this->output->set_status_header(200)
//                    ->set_content_type('application/json', 'utf-8')
//                    ->set_output(json_encode(array('message' => 'Bal / Bulk berhasil ditambahkan', 'icon' => 'fa fa-check', 'type' => 'success')));
//        } catch (Exception $ex) {
//            $this->_module->rollbackTransaction();
//            $this->output->set_status_header($ex->getCode() ?? 500)
//                    ->set_content_type('application/json', 'utf-8')
//                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
//        }
//    }
}
