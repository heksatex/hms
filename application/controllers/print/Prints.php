<?php
defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Prints
 *
 * @author RONI
 */
class Prints extends MY_Controller {

    //put your code here

    public function __construct() {
        parent::__construct();
//        $this->is_loggedin();
        $this->load->library('prints');
        $this->load->library('barcode');
    }

    public function check() {
        
//        $code = new Code\Code128();
//        $code->setData("12312312");
//        $code->setDimensions(250, 100);
//        $code->setQuality(100);
//        $code->draw();
//        $gen_code = $code->base64();
//        $data['image'] = ['airtex' => ['title' => 'LOGO', 'path' => base_url('dist/img/static/airteks.jpg')], 'barcode' => ['title' => 'LOGO', 'path' => base_url('dist/img/static/url_brcd.jpg')]];
////        $data['data']=['pattern'=>['value'=>'isi patern'],'isi_color'=>['value'=>'waran waeawen aa n awe ae nw']];
//        $data['data'] = ['pattern' => 'Ini Pattern', 'isi_color' => 'warna kuning saidi', 'isi_satuan_lebar' => 'WIDTH (cm)', 'isi_lebar' => '250x128',
//            'isi_satuan_qty1' => 'QTY Pnl', 'isi_qty1' => 16, 'isi_satuan_qty2' => 'QTY kg', 'isi_qty2' => 85, 'barcode' => $gen_code, 'barcode_id' => 12312312, 'tanggal_buat' => date('y m d'), 'no_pack_brc' => 12312312];
        return $this->load->view('print/a1');
    }

    public function test() {
        try {
            $code = new Code\Code128New();
            $gen_code = $code->generate("A123456789", "", 60, "vertical");
            $this->prints->setView('print/e');
            $this->prints->addData('pattern', 'Test Printed');
            $this->prints->addData('isi_color', 'warna kuning matahari');
            $this->prints->addData('isi_satuan_lebar', 'WIDTH (cm)');
            $this->prints->addData('isi_lebar', '250x128');
            $this->prints->addData('isi_satuan_qty1', 'QTY Pnl');
            $this->prints->addData('isi_qty1', 16);
            $this->prints->addData('isi_satuan_qty2', 'QTY kg');
            $this->prints->addData('isi_qty2', 85);
            $this->prints->addData('barcode_id', 12312312);
            $this->prints->addData('tanggal_buat', date('y-m-d'));
            $this->prints->addData('no_pack_brc', 12312312);
            $this->prints->addData('barcode', $gen_code);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $this->prints->generate())));
        } catch (Exception $ex) {
            
        }
    }

//    public function test() {
//        try {
//            $code = new Code\Code128New();
//            $gen_code = $code->generate("A123456789","",60);
//            
//            $path = "dist/img/static/heksatex_c.jpg";
//            $info = pathinfo($path, PATHINFO_EXTENSION);
//            $datas = file_get_contents($path);
//            $base64 = 'data:image/' . $info . ';base64,' . base64_encode($datas);
//
//            $this->prints->setView('print/c');
//            $this->prints->addImage('logo', $base64, 'LOGO');
//            $this->prints->addImage('barcode', base_url('dist/img/static/url_brcd_c.jpg'), 'Barcode');
//            $this->prints->addData('pattern', 'Test Printed');
//            $this->prints->addData('isi_color', 'warna kuning matahari');
//            $this->prints->addData('isi_satuan_lebar', 'WIDTH (cm)');
//            $this->prints->addData('isi_lebar', '250x128');
//            $this->prints->addData('isi_satuan_qty1', 'QTY Pnl');
//            $this->prints->addData('isi_qty1', 16);
//            $this->prints->addData('isi_satuan_qty2', 'QTY kg');
//            $this->prints->addData('isi_qty2', 85);
//            $this->prints->addData('barcode_id', 12312312);
//            $this->prints->addData('tanggal_buat', date('y-m-d'));
//            $this->prints->addData('no_pack_brc', 12312312);
//            $this->prints->addData('barcode', $gen_code);
////        echo $this->prints->generate();
//
//            $this->output->set_status_header(200)
//                    ->set_content_type('application/json', 'utf-8')
//                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $this->prints->generate())));
//        } catch (Exception $ex) {
//            
//        }
//    }
}
