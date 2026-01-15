<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Rekapgiro
 *
 * @author RONI
 */
defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Settings;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Rekapgiro extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }

    public function index() {
        $data['id_dept'] = 'RACG';
        $this->load->view('report/acc/v_rekap_giro', $data);
    }

    protected function _query() {
        try {
            $this->form_validation->set_rules([
                [
                    'field' => 'tanggal',
                    'label' => 'Tanggal',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih'
                    ]
                ]
            ]);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $id = "no_gm";
            $giro = $this->input->post("giro");
            if ($giro === "keluar")
                $id = "no_gk";
            $nobg = $this->input->post("no_bg");
            $customer = $this->input->post("customer");
            $uraian = $this->input->post("uraian");
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);

            $model = new $this->m_global;
            $model->setTables("acc_giro_{$giro} ag")->setJoins("acc_giro_{$giro}_detail agd", "giro_{$giro}_id = ag.id")->setWheres(["status" => "confirm"]);
            if (count($tanggals) > 1) {
                $model->setWheres(["date(ag.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(ag.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
            }
            if ($nobg !== "") {
                $model->setWheres(["agd.{$id} LIKE" => "%{$nobg}%"]);
            }
            if ($uraian !== "") {
                $model->setWheres(["ag.transinfo LIKE" => "%{$uraian}%"]);
            }
            if ($customer !== "") {
                $model->setWhereRaw("(partner_nama LIKE '%{$customer}%' or lain2 like '%{$customer}%')");
            }
            return $model;
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function search() {
        try {
            $giro = $this->input->post("giro");
            $model = $this->_query();
            $data["data"] = $model->setSelects(["agd.*,transinfo,partner_nama,lain2"])->getData();
            $data["giro"] = $giro;
            $html = $this->load->view('report/acc/v_rekap_giro_detail', $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function export() {
        try {
            $giro = $this->input->post("giro");
            $model = $this->_query();
            $data = $model->setSelects(["agd.*,transinfo,partner_nama,lain2"])->getData();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $row = 1;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'No Bukti');
            $sheet->setCellValue("C{$row}", 'Giro');
            $sheet->setCellValue("D{$row}", 'Tanggal');
            $sheet->setCellValue("E{$row}", 'Bank');
            $sheet->setCellValue("F{$row}", 'No Cek/BG');
            $sheet->setCellValue("G{$row}", 'Tgl.JT');
            $sheet->setCellValue("H{$row}", 'Kpd/Dari');
            $sheet->setCellValue("I{$row}", 'Uraian');
            $sheet->setCellValue("J{$row}", 'No Coa');
            $sheet->setCellValue("K{$row}", 'Nominal');
            $no = 0;
            $total = 0;
            foreach ($data as $key => $value) {
                $total += $value->nominal;
                $row += 1;
                $no += 1;
                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $value->no_gm ?? $value->no_gk);
                $sheet->setCellValue("C{$row}", $giro);
                $sheet->setCellValue("D{$row}", date("Y-m-d",strtotime($value->tanggal)));
                $sheet->setCellValue("E{$row}", $value->bank);
                $sheet->setCellValue("F{$row}", $value->no_bg);
                $sheet->setCellValue("G{$row}", date("Y-m-d",strtotime($value->tgl_jt)));
                $sheet->setCellValue("H{$row}", ($value->partner_nama === "") ? $value->lain2 : $value->partner_nama);
                $sheet->setCellValue("I{$row}", $value->transinfo);
                $sheet->setCellValue("J{$row}", $value->kode_coa);
                $sheet->setCellValue("K{$row}", $value->nominal);
            }
            if($total > 0 ) {
                $row += 1;
                $sheet->setCellValue("J{$row}", "Total");
                $sheet->setCellValue("K{$row}", $total);
            }
            $sheet->getStyle("K2:K{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);
            $writer = new Xlsx($spreadsheet);
            $filename = "Rekap Giro {$giro} {$tanggals[0]} {$tanggals[1]}";
            $url = "dist/storages/report/acc";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(FCPATH . $url . '/' . $filename . '.xlsx');
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil Export', 'icon' => 'fa fa-check', 'text_name' => $filename,
                        'type' => 'success', "data" => base_url($url . '/' . $filename . '.xlsx'))));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }
}
