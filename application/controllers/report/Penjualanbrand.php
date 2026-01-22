<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Penjualanbrand
 *
 * @author RONI
 */
defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Penjualanbrand extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }

    public function index() {
        $data['id_dept'] = 'RPJB';
        $this->load->view('report/acc/v_penjualan_brand', $data);
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
                ],
                [
                    'field' => 'customer',
                    'label' => 'Customer',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih'
                    ]
                ]
            ]);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $customer = $this->input->post("customer");
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);
            $model = new $this->m_global;
            $model->setTables("acc_faktur_penjualan fp")->setJoins("acc_faktur_penjualan_detail fpd", "faktur_id = fp.id")
                    ->setJoins("currency_kurs ck", "ck.id = fp.kurs", "left")
                    ->setJoins("delivery_order do", "(do.no_sj = fp.no_sj and do.status = 'done')")
                    ->setWheres(["fp.status" => "confirm", "partner_id" => $customer])
                    ->setWheres(["date(fp.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(fp.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))])
                    ->setOrder(["fp.tanggal" => "asc", "fp.no_sj" => "asc", "fpd.id" => "asc"])
                    ->setSelects(["fpd.*", "fp.no_sj,no_faktur_pajak,no_faktur_internal,fp.tanggal,kurs_nominal", "ck.currency as curr", "tanggal_dokumen"]);
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function search() {
        try {
            $model = $this->_query();
            $data["data"] = $model->getData();
            $html = $this->load->view('report/acc/v_penjualan_brand_detail', $data, true);
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
            $customer = $this->input->post("customer_name");
            $tanggal = $this->input->post("tanggal");
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue("A1", 'Laporan Penjualan (KGM)');
            $sheet->setCellValue("A2", "Periode : {$tanggal}");
            $sheet->setCellValue("A3", "Customer : {$customer}");

            $row = 5;
            $sheet->setCellValue("A{$row}", 'Delivery Date');
            $sheet->setCellValue("B{$row}", "Delivery No#");
            $sheet->setCellValue("C{$row}", "Purchase Order#");
            $sheet->setCellValue("D{$row}", 'Invoice No#');
            $sheet->setCellValue("E{$row}", "VAT No#");
            $sheet->setCellValue("F{$row}", "Invoice Date");
            $sheet->setCellValue("G{$row}", 'Item');
            $sheet->setCellValue("H{$row}", "Qty");
            $sheet->setCellValue("I{$row}", "Unit");
            $sheet->setCellValue("J{$row}", "Price");
            $sheet->setCellValue("K{$row}", "Kurs");
            $sheet->setCellValue("L{$row}", "Curr");
            $sheet->setCellValue("M{$row}", "Amount (Rp)");
            $sheet->setCellValue("N{$row}", "Remark");
            $sheet->setCellValue("O{$row}", "Receipt Date");
            $sheet->setCellValue("P{$row}", "Due Date");
            $total = 0;
            $model = $this->_query();
            foreach ($model->getData() as $key => $value) {
                $row += 1;
                $total += $value->jumlah * $value->kurs_nominal;
                $item = ($value->warna !== "") ? "{$value->uraian} / {$value->warna}" : "{$value->uraian}";
                $sheet->setCellValue("A{$row}", date("Y-m-d", strtotime($value->tanggal_dokumen)));
                $sheet->setCellValue("B{$row}", $value->no_sj);
                $sheet->setCellValue("C{$row}", $value->no_faktur_internal);
                $sheet->setCellValue("D{$row}", $value->no_po);
                $sheet->setCellValue("E{$row}", $value->no_faktur_pajak);
                $sheet->setCellValue("F{$row}", $value->tanggal);
                $sheet->setCellValue("G{$row}", $item);
                $sheet->setCellValue("H{$row}", $value->qty);
                $sheet->setCellValue("I{$row}", $value->uom);
                $sheet->setCellValue("J{$row}", $value->harga);
                $sheet->setCellValue("K{$row}", $value->kurs_nominal);
                $sheet->setCellValue("L{$row}", $value->curr);
                $sheet->setCellValue("M{$row}", $value->jumlah * $value->kurs_nominal);
            }
            if ($total > 0) {
                $row += 2;
                $sheet->setCellValue("G{$row}", "Total (Rp)");
                $sheet->setCellValue("M{$row}", $total);
                $sheet->getStyle("J6:J{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle("K6:K{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle("M6:M{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }

            $filename = "Penjualan KGM {$tanggal}";
            $url = "dist/storages/report/penjualan";
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
