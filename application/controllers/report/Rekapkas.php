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

class Rekapkas extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }

    public function index() {
        $data['id_dept'] = 'RACK';
        $model = new $this->m_global;
        $data["coa"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "kas"])->getData();
        $this->load->view('report/acc/v_rekap_kas', $data);
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
            $id = "no_km";
            $kas = $this->input->post("kas");
            if ($kas === "keluar")
                $id = "no_kk";
            $nobukti = $this->input->post("no_bukti");
            $coa = $this->input->post("kode_coa");
            $customer = $this->input->post("customer");
            $uraian = $this->input->post("uraian");
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);
            $model = new $this->m_global;
            $model->setTables("acc_kas_{$kas} ag")->setJoins("acc_kas_{$kas}_detail agd", "kas_{$kas}_id = ag.id")->setWheres(["status" => "confirm"]);
            if (count($tanggals) > 1) {
                $model->setWheres(["date(ag.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(ag.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
            }
            if ($uraian !== "") {
                $model->setWheres(["agd.uraian LIKE" => "%{$uraian}%"]);
            }
            if ($nobukti !== "") {
                $model->setWheres(["agd.{$id} LIKE" => "%{$nobukti}%"]);
            }
            if ($coa !== "") {
                $model->setWheres(["ag.kode_coa" => $coa]);
            }
            if ($customer !== "") {
                $model->setWhereRaw("(partner_nama LIKE '%{$customer}%' or lain2 like '%{$customer}%')");
            }
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function search() {
        try {
            $id = "no_km";
            $kas = $this->input->post("kas");
            if ($kas === "keluar")
                $id = "no_kk";
            $model = $this->_query();
            $data["data"] = $model->setSelects(["agd.*,transinfo,partner_nama,lain2"])->getData();
            $data["kas"] = $kas;
            $html = $this->load->view('report/acc/v_rekap_kas_detail', $data, true);
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
            $id = "no_km";
            $kas = $this->input->post("kas");
            if ($kas === "keluar")
                $id = "no_kk";
            $model = $this->_query();
            $data = $model->setSelects(["agd.*,transinfo,partner_nama,lain2"])->getData();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $row = 1;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'No Bukti');
            $sheet->setCellValue("C{$row}", 'Kas');
            $sheet->setCellValue("D{$row}", 'Tanggal');
            $sheet->setCellValue("E{$row}", 'Kepada');
            $sheet->setCellValue("F{$row}", 'Uraian');
            $sheet->setCellValue("G{$row}", 'Total');
            $no = 0;
            $total = 0;
            foreach ($data as $key => $value) {
                $total += $value->nominal;
                $row += 1;
                $no += 1;
                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $value->no_kk ?? $value->no_km);
                $sheet->setCellValue("C{$row}", $kas);
                $sheet->setCellValue("D{$row}", date("Y-m-d", strtotime($value->tanggal)));
                $sheet->setCellValue("E{$row}", ($value->partner_nama === "") ? $value->lain2 : $value->partner_nama);
                $sheet->setCellValue("F{$row}", ($value->uraian === "") ? $value->transinfo : $value->uraian);
                $sheet->setCellValue("G{$row}", $value->nominal);
            }
            if ($total > 0) {
                $row += 1;
                $sheet->setCellValue("F{$row}", "Total");
                $sheet->setCellValue("G{$row}", $total);
            }
            $tanggal = $this->input->post("tanggal");
            $writer = new Xlsx($spreadsheet);
            $filename = "Rekap Kas {$kas} {$tanggal}";
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
