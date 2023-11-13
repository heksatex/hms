<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class Picklistvalidasi extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_Picklist");
        $this->load->model("m_PicklistDetail");
        $this->load->model("m_accessmenu");
//        $this->load->library('prints');
//        $this->load->library('barcode');
    }

    public function index() {
        $data['id_dept'] = 'PLV';
        $data['submenu'] = 'picklistvalidasi';
        $this->load->view('warehouse/v_picklist_realisasi', $data);
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
            $data['picklist'] = $this->m_Picklist->getDataByID($kode_decrypt);
            $data['view_cancel'] = $this->load->view('modal/v_picklist_item_cancel', [], true);
            $this->load->view('warehouse/v_picklist_validasi_proses', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

//    public function check() {
//
//        return $this->load->view('print/a1');
//    }
//
//    public function test() {
//        try {
//            $code = new Code\Code128New();
//            $gen_code = $code->generate("A123456789", "", 65, "vertical");
//            $this->prints->setView('print/x2');
//            $this->prints->addData('pattern', 'Test Printed');
//            $this->prints->addData('isi_color', 'warna kuning matahari warna kuning matahari');
//            $this->prints->addData('isi_satuan_lebar', 'WIDTH (cm)');
//            $this->prints->addData('isi_lebar', '250x128');
//            $this->prints->addData('isi_satuan_qty1', 'QTY Pnl');
//            $this->prints->addData('isi_qty1', 16);
//            $this->prints->addData('isi_satuan_qty2', 'QTY kg');
//            $this->prints->addData('isi_qty2', 85);
//            $this->prints->addData('barcode_id', 'C1234567910');
//            $this->prints->addData('tanggal_buat', date('y-m-d'));
//            $this->prints->addData('no_pack_brc', 12312312);
//            $this->prints->addData('barcode', $gen_code);
//            $this->prints->addData('k3l', date('Y-m-d'));
//            $this->output->set_status_header(200)
//                    ->set_content_type('application/json', 'utf-8')
//                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $this->prints->generate())));
//        } catch (Exception $ex) {
//            
//        }
//    }
}
