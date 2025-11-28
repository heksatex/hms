<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require FCPATH . 'vendor/autoload.php';
require_once APPPATH . '/third_party/vendor/autoload.php';
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Outstandingfaktur
 *
 * @author RONI
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Mpdf\Mpdf;

class Outstandingfaktur extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }

    public function index() {
        $partner = $this->input->get("partner");
        $id_dept = 'ROUTSFAK';
        if ($partner !== "") {
            $model = new $this->m_global;
            $data["partner"] = $model->setTables("partner")->setSelects(["id", "nama"])->setWheres(["id" => $partner])->getDetail();
        }
        $data['id_dept'] = $id_dept;
        $this->load->view('report/acc/v_outstanding_faktur', $data);
    }

    protected function _query_customer_faktur() {
        try {
            $customer = $this->input->post("customer");
            $model = new $this->m_global;
            $model->setTables("acc_faktur_penjualan")->setOrder(["partner_nama" => "asc"])
                    ->setWheres(["lunas" => 0, "status" => "confirm"])->setGroups(["partner_id"]);
            if (!empty($customer)) {
                $model->setWheres(["partner_id" => $customer]);
            }
            return $model->setSelects(["partner_nama,partner_id"]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    protected function _query_data() {
        try {
            $model = new $this->m_global;
            $model->setTables("acc_faktur_penjualan")->setOrder(["tanggal" => "asc", "no_faktur_internal" => "asc"])
                    ->setSelects(["acc_faktur_penjualan.*", "DATEDIFF(CURDATE(), tanggal) AS hari"]);
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function generate() {
        try {
            $partner = $this->_query_customer_faktur()->getData();
            $lData = $this->_query_data();
            $dt = [];
            foreach ($partner as $key => $value) {
                $datas = $lData->setWheres(["lunas" => 0, "status" => "confirm", "partner_id" => $value->partner_id], true)->getData();
                $data = [];
                foreach ($datas as $keys => $values) {
                    $data[] = $values;
                }
                $dt[$value->partner_nama] = $data;
            }
            $data["data"] = $dt;
            $html = $this->load->view('report/acc/v_outstanding_faktur_detail', $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function pdf() {
        try {
            $partner = $this->_query_customer_faktur()->getData();
            $lData = $this->_query_data();
            $dt = [];
            foreach ($partner as $key => $value) {
                $datas = $lData->setWheres(["lunas" => 0, "status" => "confirm", "partner_id" => $value->partner_id], true)->getData();
                $data = [];
                foreach ($datas as $keys => $values) {
                    $data[] = $values;
                }
                $dt[$value->partner_nama] = $data;
            }
            $data["data"] = $dt;

            $html = $this->load->view('report/acc/v_outstanding_faktur_pdf', $data, true);
            $url = "dist/storages/print/outstandingfaktur";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $tanggal = date("Y-m-d");
            $mpdf = new Mpdf(['tempDir' => FCPATH . 'tmp']);
            $mpdf->WriteHTML($html);
            $filename = $tanggal;
            $pathFile = "{$url}/outstanding faktur pertanggal {$filename}.pdf";
            $mpdf->Output(FCPATH . $pathFile, "F");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("url" => base_url($pathFile))));
        } catch (Exception $ex) {
            log_message("error", json_encode($ex));
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function export() {
        try {

            $partner = $this->_query_customer_faktur()->getData();
            $lData = $this->_query_data();
            $dt = [];
            foreach ($partner as $key => $value) {
                $datas = $lData->setWheres(["lunas" => 0, "status" => "confirm", "partner_id" => $value->partner_id], true)->getData();
                $data = [];
                foreach ($datas as $keys => $values) {
                    $data[] = $values;
                }
                $dt[$value->partner_nama] = $data;
            }
            $customer = $this->input->post("customer");
            $filter = "";
            if (!empty($customer)) {
                $filter .= "Customer : " . $partner[0]->partner_nama ?? '' . ", ";
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue("A1", 'Pertanggal');
            $sheet->setCellValue("B1", date("Y-m-d"));
            $sheet->setCellValue("A2", 'Filter : ');
            $sheet->setCellValue("B2", $filter);
            $row = 4;

            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'No Faktur');
            $sheet->setCellValue("C{$row}", 'No SJ');
            $sheet->setCellValue("d{$row}", 'Tanggal');
            $sheet->setCellValue("e{$row}", 'Total Piutang (Rp)');
            $sheet->setCellValue("f{$row}", 'Sisa Puitang (Rp)');
            $sheet->setCellValue("g{$row}", 'Total Piutang (Valas)');
            $sheet->setCellValue("h{$row}", 'Sisa Piutang (Valas)');
            $sheet->setCellValue("i{$row}", 'Payment Term (Hari)');
            $sheet->setCellValue("j{$row}", 'Umur (Hari)');
            foreach ($dt as $key => $value) {
                $row += 1;
                $no = 1;
                $total_piutang_rp = 0;
                $piutang_rp = 0;
                $total_piutang_valas = 0;
                $piutang_valas = 0;
                $sheet->setCellValue("A{$row}", "CUSTOMER : {$key}");
                $sheet->mergeCells("A{$row}:J{$row}");
                foreach ($value as $keys => $values) {
                    $row += 1;
                    $total_piutang_rp += $values->total_piutang_rp;
                    $piutang_rp += $values->piutang_rp;
                    $total_piutang_valas += $values->total_piutang_valas;
                    $piutang_valas += $values->piutang_valas;
                    $sheet->setCellValue("A{$row}", $no);
                    $sheet->setCellValue("B{$row}", $values->no_faktur_internal);
                    $sheet->setCellValue("C{$row}", $values->no_sj);
                    $sheet->setCellValue("d{$row}", $values->tanggal);
                    $sheet->setCellValue("e{$row}", $values->total_piutang_rp);
                    $sheet->setCellValue("f{$row}", $values->piutang_rp);
                    $sheet->setCellValue("g{$row}", $values->total_piutang_valas);
                    $sheet->setCellValue("h{$row}", $values->piutang_rp);
                    $sheet->setCellValue("i{$row}", $values->payment_term);
                    $sheet->setCellValue("j{$row}", $values->hari);
                    $no++;
                }
                $row += 1;
                $sheet->setCellValue("A{$row}", "Total : {$key}");
                $sheet->mergeCells("A{$row}:D{$row}");
                $sheet->setCellValue("e{$row}", $total_piutang_rp);
                $sheet->setCellValue("f{$row}", $piutang_rp);
                $sheet->setCellValue("g{$row}", $total_piutang_valas);
                $sheet->setCellValue("h{$row}", $piutang_valas);
            }
            $tanggal = date("Y-m-d");
            $writer = new Xlsx($spreadsheet);
            $filename = "Outstanding faktur Pertanggal {$tanggal}";
            $url = "dist/storages/report/outstandingfaktur";
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
