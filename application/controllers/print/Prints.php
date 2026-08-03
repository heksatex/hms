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
require FCPATH . 'vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

class Prints extends MY_Controller {

    //put your code here
    protected $CI;

    public function __construct() {
        parent::__construct();
//        $this->CI =& get_instance();
//        $this->is_loggedin();
////        $this->load->library('prints');
//        $this->load->library('barcode');
        $this->config->load('additional');
//        $this->load->library('session');
        $this->load->library('tspl/warpingprint');
    }

    public function network() {
        try {
            $connector = new NetworkPrintConnector("192.168.10.20", 8000);
            $printer = new Printer($connector);
            $printer->text("Item: Box Label #123\n");
            $printer->barcode("12345678", Printer::BARCODE_CODE39);
            $printer->cut();
        } catch (Exception $ex) {
            
        } finally {
            $printer->close();
        }
    }

    public function index() {
        try {
            $wprint = new $this->warpingprint;
            $lots = ["WRD/0726/21/2610_1/MC22-TEST-DARI-IT-INFO-PRINT", "WRD/0726/21/2610_1/MC22-TEST-DARI-IT-INFO-PRINT", "WRD/0726/21/2610_1/MC22-TEST-DARI-IT-INFO-PRINT"];
            $reff = ["WRD/OUT/260700002/TRI/IN/260700002", "WRD/OUT/260700002/TRI/IN/260700002", "WRD/OUT/260700002/TRI/IN/260700002"];
            $prod = ["BD[PH-0013]POLYFOY30-1-210 BO/155", "BD[PH-0013]POLYFOY30-1-210 BO/155", "BD[PH-0013]POLYFOY30-1-210 BO/155"];
            $notes = ["UA:1400 UI:945 W:9695", "UA:1400 UI:945 W:9695", "UA:1400 UI:945 W:9695"];
            $wprint->setup("3.14", "2.36", "0.11, 0");
            foreach ($lots as $key => $value) {
                $wprint->setLot($value)->setReffPicking($reff[$key])->setProduk($prod[$key])->setNote($notes[$key]);
            }
            $resp = $wprint->print();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success',"resp"=>$resp)));
            
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function check() {

        try {

            $printers = $this->CI->session->userdata('printer');
            if ($printers === null) {
                throw new \exception("Printer Direct belum ditentukan, silakan pilih pada tab atas", 500);
            }
            $printers = json_decode($printers);

            $connector = new DummyPrintConnector();
            $printer = new Printer($connector);
            $buff = $printer->getPrintConnector();
            $printer->feed();
            $buff->write("\x1bE" . chr(1));
            $printer->text(str_pad("BUKTI KAS MASUK (BKM)"));
            $buff->write("\x1bF" . chr(0));
            $printer->feed();
            $datas = $connector->getData();
            $printer->close();
            $client = new GuzzleHttp\Client();
            $resp = $client->request("POST", $this->CI->config->item('url_web_print'), [
                "form_params" => [
                    "data" => $datas,
                    "printer" => "\\\\{$printers->ip_share}\\{$printers->nama_printer_share}"
                ]
            ]);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $printer->close();
        }

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
//        return $this->load->view('print/a1');
    }

    public function test() {
        try {
            $code = new Code\Code128New();
            $gen_code = $code->generate("A123456789", "", 60, "vertical");
            $this->prints->setView('print/t');
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

    public function print_server() {
        
    }
}
