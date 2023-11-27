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
            $data['picklist'] = $this->m_Picklist->getDataByID(['picklist.id' => $kode_decrypt]);
            $data['view_cancel'] = $this->load->view('modal/v_picklist_item_cancel', [], true);
            $this->load->view('warehouse/v_picklist_validasi_proses', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function update() {
        $errorCode = 0;
        try {
            $username = $this->session->userdata('username');
            $nama = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);
            $pl = "";
            $picklist = null;
            $barcode = $this->input->post('search');
            $item = null;
            if (preg_match("/PL/i", $barcode)) {
                $pl = $barcode;
                $dataPl = $this->m_Picklist->getDataByID(['picklist.no' => $pl]);
                if (is_null($dataPl)) {
                    throw new Exception("No Picklist Tidak ditemukan", 500);
                }
                if ($dataPl->status === "cancel") {
                    throw new Exception("No Picklist dibatalkan", 500);
                }
//                if ($dataPl->status !== 'validasi') {
//                    throw new Exception("No Picklist belum dalam status Validasi", 500);
//                }
                $picklist = $dataPl;
                $picklist->total_lot = $this->m_PicklistDetail->getCountAllData(['no_pl' => $picklist->no]);
                $picklist->total_realisasi = $this->m_PicklistDetail->getCountAllData(['no_pl' => $picklist->no,'valid'=>'realisasi']);
                if ($picklist->total_lot < 1) {
                    throw new Exception("Tidak ada barcode pada Picklist " . $dataPl->no, 500);
                }
            } else {
                $pl = $this->input->post('pl');
                if (empty($pl)) {
                    throw new Exception("Tentukan dulu no picklist", 500);
                }
                $item = $this->m_PicklistDetail->detailData(['no_pl' => $pl, "barcode_id" => $barcode]);
                if (is_null($item)) {
                    $errorCode = 11;
                    throw new Exception("Barcode " . $barcode . " Tidak Ada Dalam No PL " . $pl, 500);
                }
                if ($item->valid === 'validasi') {
                    $errorCode = 12;
                    throw new Exception("Barcode " . $barcode . " Duplikat scan", 500);
                }
//                if ($item->valid !== 'realisasi') {
//                    $errorCode = 12;
//                    throw new Exception("Barcode " . $barcode . " Dalam Status " . $item->valid, 500);
//                }
                $update = ['valid' => 'validasi', 'valid_date' => date('Y-m-d H:i:s')];
                $condition = ['no_pl' => $pl, 'barcode_id' => $item->barcode_id];
                $sts = $this->m_PicklistDetail->updateStatus($condition, $update);
                if (!empty($sts)) {
                    throw new Exception($sts, 500);
                }
                $this->m_Picklist->update(['status' => 'validasi'], ['no' => $pl]);
//                $this->_module->gen_history($sub_menu, $pl, 'edit', logArrayToString('; ', array_merge($condition, $update)), $username);
                $this->_module->gen_history($sub_menu, $pl, 'edit',  ($nama["nama"] ?? "") . ' Melakukan validasi barcode ' . $barcode, $username);
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-check', 'type' => 'success', 'picklist' => $picklist, 'item' => $item)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', 'error_code' => $errorCode)));
        }
    }

    public function check() {
//        $path = "dist/img/static/Logo AX Hitam.png";
//        $info = pathinfo($path, PATHINFO_EXTENSION);
//        $datas = file_get_contents($path);
//        $base64 = 'data:image/' . $info . ';base64,' . base64_encode($datas);
//        log_message('error', $base64);
        return $this->load->view('print/a1');
    }

//
    public function test() {
        try {
            $code = new Code\Code128New();
            $text  = "123456789012";
            $gen_code = $code->generate($text, "", 65, "vertical");
            $this->prints->setView('print/d');
            $this->prints->addData('pattern', 'Test Printed');
            $this->prints->addData('isi_color', 'warna kuning matahari warna kuning matahari');
            $this->prints->addData('isi_satuan_lebar', 'WIDTH (cm)');
            $this->prints->addData('isi_lebar', '250x128');
            $this->prints->addData('isi_satuan_qty1', 'QTY [Pnl]');
            $this->prints->addData('isi_qty1', 16);
            $this->prints->addData('isi_satuan_qty2', 'QTY [kg]');
            $this->prints->addData('isi_qty2', 85);
            $this->prints->addData('barcode_id', $text);
            $this->prints->addData('tanggal_buat', date('ymd'));
            $this->prints->addData('no_pack_brc', "MG312312");
            $this->prints->addData('barcode', $gen_code);
            $this->prints->addData('k3l', date('Ymd'));
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $this->prints->generate())));
        } catch (Exception $ex) {
            
        }
    }
}
