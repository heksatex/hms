<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class Picklistvalidasi extends MY_Controller {

    protected $menu;

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_Picklist");
        $this->load->model("m_PicklistDetail");
        $this->load->model("m_accessmenu");
        $this->load->library('prints');
        $this->load->library('barcode');
        $this->load->model('m_Pickliststockquant');
    }

    public function add() {
        $data['id_dept'] = 'PLV';
        $data['submenu'] = 'picklistvalidasi';
        $this->load->view('warehouse/v_picklist_realisasi', $data);
    }

    public function index() {
        $data['id_dept'] = 'PLV';
        $sub_menu = $this->uri->segment(2);

        $this->menu = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $data['access'] = $this->m_accessmenu->getDetail(['access_only' => getClientIP(), 'menu' => $this->menu['kode']]);
        $this->load->view('warehouse/v_picklist_validasi_add', $data);
    }

    public function edit($id = null) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['id_dept'] = 'PLV';
            $data['access'] = $this->m_accessmenu->getDetail(['access_only' => getClientIP(), 'menu' => $this->uri->segment(2)]);
            $data["ids"] = $id;
            $data['picklist'] = $this->m_Picklist->getDataByID(['picklist.id' => $kode_decrypt], '', 'delivery');
            $data['view_cancel'] = $this->load->view('modal/v_picklist_item_cancel', [], true);
            $this->load->view('warehouse/v_picklist_validasi_proses', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function update() {
        $errorCode = 0;
        $barcode = "";
        try {
            $username = $this->session->userdata('username');
            $nama = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);

            $pl = "";
            $picklist = null;
            $barcode = $this->input->post('search');
            $item = null;
            $access = $this->input->post("access");
            if (preg_match("/PL/i", $barcode)) {
                $pl = $barcode;
                $dataPl = $this->m_Picklist->getDataByID(['picklist.no' => $pl], '', 'validasi');
                if (is_null($dataPl)) {
                    throw new Exception("No Picklist Tidak ditemukan", 500);
                }
                if ($dataPl->status === "cancel") {
                    throw new Exception("No Picklist dibatalkan", 500);
                }
                if (empty($access)) {
                    throw new Exception("Invalid tipe picklist", 500);
                }
                if (($dataPl->type_bulk_id === "1") && ($access === "LOOSE_PACKING")) {
                    throw new Exception("Invalid tipe picklist", 500);
                }
                if (($dataPl->type_bulk_id === "2") && ($access === "BAL")) {
                    throw new Exception("Invalid tipe picklist", 500);
                }
//                if ($dataPl->status !== 'validasi') {
//                    throw new Exception("No Picklist belum dalam status Validasi", 500);
//                }
                $picklist = $dataPl;
                $picklist->total_lot = $this->m_PicklistDetail->getCountAllData(['no_pl' => $picklist->no, 'valid !=' => 'cancel']);
                $picklist->total_validasi = $this->m_PicklistDetail->getCountAllData(['no_pl' => $picklist->no, 'valid' => 'validasi']) ?? 0;
                if ($picklist->total_lot < 1) {
                    throw new Exception("Tidak ada barcode pada Picklist " . $dataPl->no, 500);
                }
            } else {

                $pl = $this->input->post('pl');
                if (empty($pl)) {
                    throw new Exception("Tentukan dulu no picklist", 500);
                }
//                $this->_module->startTransaction();
                $item = $this->m_PicklistDetail->detailData(['no_pl' => $pl, "barcode_id" => $barcode, 'valid !=' => 'cancel']);

                if (is_null($item)) {
                    $errorCode = 11;
                    throw new Exception("Barcode " . $barcode . " Tidak Ada Dalam No PL " . $pl, 500);
                }
                $cond = ['lot' => $barcode];

                $check = $this->m_Pickliststockquant->getDataItemPicklistScan(array_merge($cond, ['stock_quant.lokasi' => 'GJD/Stock', 'id_category' => 21]));

                if ($item->valid === 'validasi') {
                    $errorCode = 12;
                    throw new Exception("Barcode " . $barcode . " sudah valid", 500);
                }
                if (is_null($check)) {
                    $errorCode = 12;
                    throw new Exception("Scan barcode invalid", 500);
//                    $list = $this->m_Pickliststockquant->getDataItemPicklistScanDetail($cond, true);
//                    if (empty($list)) {
//                        throw new \Exception('Barcode Tidak ditemukan', 500);
//                    }
//                    switch (true) {
//
//                        case (int) $list->id_category !== 21:
//                            throw new \Exception("Kategori Produk Tidak Valid (" . $list->nama_category . ")", 500);
//                        case $list->reserve_move !== "":
//                            throw new \Exception("Barcode " . $barcode . " reserve move " . $list->reserve_move, 500);
//
//                        case in_array(strtoupper($list->lokasi_fisik), ["PORT", "XPD"]) :
//                            throw new \Exception("Lokasi Tidak Valid (" . $list->lokasi_fisik . ")", 500);
//
//                        case strtoupper($list->lokasi) !== 'GJD/STOCK':
//                            throw new \Exception("Lokasi Tidak Valid (" . $list->lokasi . ")", 500);
//                        default :
//                            throw new \Exception('Barcode Tidak ditemukan', 500);
//                    }
                }
//                if ($item->valid !== 'realisasi') {
//                    $errorCode = 12;
//                    throw new Exception("Barcode " . $barcode . " Dalam Status " . $item->valid, 500);
//                }

                $update = ['valid' => 'validasi', 'valid_date' => date('Y-m-d H:i:s')];
                $condition = ['no_pl' => $pl, 'barcode_id' => $item->barcode_id, 'valid !=' => 'cancel'];
                $sts = $this->m_PicklistDetail->updateStatus($condition, $update);
                $this->m_Picklist->update(['status' => 'validasi'], ['no' => $pl]);
                if (!empty($sts)) {
                    throw new Exception($sts, 500);
                }
                $this->m_Pickliststockquant->update(["move_date" => date('Y-m-d H:i:s'), "lokasi_fisik" => "XPD"], ["lot" => $barcode, 'quant_id' => $item->quant_id]);
//                $this->_module->gen_history($sub_menu, $pl, 'edit', logArrayToString('; ', array_merge($condition, $update)), $username);
                $this->_module->gen_history($sub_menu, $pl, 'edit', ($nama["nama"] ?? "") . ' Melakukan validasi barcode ' . $barcode, $username);
                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal validasi data', 500);
                }
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-check', 'type' => 'success', 'picklist' => $picklist, 'item' => $item)));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', 'error_code' => $errorCode, 'barcode' => $barcode)));
        }
    }

    public function data_detail() {
        try {
            $pl = $this->input->post('filter');

            $condition = ['no_pl' => $pl, 'valid !=' => 'cancel'];
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

    public function check() {
        return $this->load->view('print/a1');
    }

//
    public function test() {
        try {
            $code = new Code\Code128New();
            $datsa = $this->m_PicklistDetail->contoh(60, 40);
            $this->prints->setView('print/a');
            foreach ($datsa as $key => $value) {
                $gen_code = $code->generate($value->barcode_id, "", 50, "vertical");
                $this->prints->addDatas([
                    'pattern' => $value->nama_produk,
                    'isi_color' => $value->corak_remark,
                    'isi_satuan_lebar' => 'WIDTH [' . $value->uom_lebar_jadi . ']',
                    'isi_lebar' => $value->lebar_jadi,
                    'isi_satuan_qty1' => 'QTY [' . $value->uom . ']',
                    'isi_qty1' => $value->qty,
                    'barcode_id' => $value->barcode_id,
                    'tanggal_buat' => date('ymd'),
                    'no_pack_brc' => "MG" . ($key + 1),
                    'barcode' => $gen_code,
                    'k3l' => "20-D-001737"
                ]);
            }

//            $gen_code = $code->generate($text, "", 50);
//            $this->prints->addDatas([
//                'barcode_id' => $text,
//                'barcode' => $gen_code,
//                'pl' => "PL200312323"
//            ]);
//            $this->prints->addDatas([
//                'barcode_id' => $text,
//                'barcode' => $gen_code,
//                'pl' => "PL200312323"
//            ]);
//            for ($index = 0; $index < 6; $index++) {
//                $text = "1234567890-" . $index;
//                $gen_code = $code->generate($text, "", 50, "vertical");
//                $this->prints->addDatas([
//                    'pattern' => 'Test Printed ' . $index,
//                    'isi_color' => 'warna kuning matahari warna kuning matahari',
//                    'isi_satuan_lebar' => 'WIDTH (cm)',
//                    'isi_lebar' => '250x128',
//                    'isi_satuan_qty1' => 'QTY [Pnl]',
//                    'isi_qty1' => 16,
//                    'isi_satuan_qty2' => 'QTY [kg]',
//                    'isi_qty2' => 85,
//                    'barcode_id' => $text,
//                    'tanggal_buat' => date('ymd'),
//                    'no_pack_brc' => "MG312312" . $index,
//                    'barcode' => $gen_code,
//                    'k3l' => "20-D-001737"
//                ]);
//            }
//            
//            $this->prints->addData('pattern', 'Test Printed');
//            $this->prints->addData('isi_color', 'warna kuning matahari warna kuning matahari');
//            $this->prints->addData('isi_satuan_lebar', 'WIDTH (cm)');
//            $this->prints->addData('isi_lebar', '250x128');
//            $this->prints->addData('isi_satuan_qty1', 'QTY [Pnl]');
//            $this->prints->addData('isi_qty1', 16);
//            $this->prints->addData('isi_satuan_qty2', 'QTY [kg]');
//            $this->prints->addData('isi_qty2', 85);
//            $this->prints->addData('barcode_id', $text);
//            $this->prints->addData('tanggal_buat', date('ymd'));
//            $this->prints->addData('no_pack_brc', "MG312312");
//            $this->prints->addData('barcode', $gen_code);
//            $this->prints->addData('k3l', date('Ymd'));
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $this->prints->generate())));
        } catch (Exception $ex) {
            
        }
    }

    public function check_error() {
        try {
            $barcode = $this->input->post("barcode");
            $pl = $this->input->post("pl");
            $message = "";
            $cond = ['lot' => $barcode];
            $list = $this->m_Pickliststockquant->getDataItemPicklistScanDetail($cond, true);
            if (empty($list)) {
                $message = "Barcode Tidak ditemukan";
                throw new \Exception('Barcode Tidak ditemukan', 200);
            }
            switch (true) {

                case (int) $list->id_category !== 21:
                    $message = "Kategori Produk Tidak Valid (" . $list->nama_category . ")";
//                    throw new \Exception("Kategori Produk Tidak Valid (" . $list->nama_category . ")", 500);
                case $list->reserve_move !== "":
//                    throw new \Exception("Barcode " . $barcode . " reserve move " . $list->reserve_move, 500);
                    $message = "Barcode " . $barcode . " reserve move " . $list->reserve_move;
                case in_array(strtoupper($list->lokasi_fisik), ["PORT", "XPD"]) :
//                    throw new \Exception("Lokasi Tidak Valid (" . $list->lokasi_fisik . ")", 500);
                    $message = "Lokasi Tidak Valid (" . $list->lokasi_fisik . ")";
                case strtoupper($list->lokasi) !== 'GJD/STOCK':
//                    throw new \Exception("Lokasi Tidak Valid (" . $list->lokasi . ")", 500);
                    $message = "Lokasi Tidak Valid (" . $list->lokasi_fisik . ")";
                default :
//                    throw new \Exception('Barcode Tidak ditemukan', 500);
                    $message = "Barcode Tidak ditemukan";
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $message, 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function show_error() {

        $barcode = $this->input->post("barcode");
        $data = json_decode($barcode);
        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['data' => $this->load->view('warehouse/v_picklist_validasi_show_error', ['data' => $data], true)]));
    }
}
