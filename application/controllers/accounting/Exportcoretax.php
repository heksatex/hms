<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Jurnalmemorial
 *
 * @author RONI
 */
require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Exportcoretax extends MY_Controller {

    //put your code here
    protected $data;

    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->data = new $this->m_global;
    }

    protected function getData() {
        $validation = [
            [
                'field' => 'customer',
                'label' => 'Customer',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                ]
            ],
            [
                'field' => 'periode',
                'label' => 'periode',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                ]
            ]
        ];
        try {
            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }

            $periode = $this->input->post("periode");
            $customer = $this->input->post("customer");

            $periodes = explode(" - ", $periode);
            if (count($periodes) < 2) {
                throw new \Exception("Tentukan dahulu tanggalnya", 500);
            }
            $tanggalAwal = date("Y-m-d", strtotime($periodes[0]));
            $tanggalAkhir = date("Y-m-d", strtotime($periodes[1]));
            $wheres = ["tanggal >=" => $tanggalAwal, "tanggal <=" => $tanggalAkhir, "partner_id" => $customer, "status" => "confirm"];
            $this->data->setTables("acc_faktur_penjualan fp")->setJoins("acc_faktur_penjualan_detail fpd", "fp.id = faktur_id")
                    ->setOrder(["no_sj" => "asc"])->setWheres($wheres)
                    ->setSelects(["no_sj", "tanggal", "tax_value", "kurs_nominal", "fpd.*"]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function index() {
        $data['id_dept'] = '';
        $this->load->view('report/acc/v_export_coretax', $data);
    }

    public function search() {
        try {
            $this->getData();
            $data["data"] = $this->data->getData();
            $html = $this->load->view('report/acc/v_export_coretax_detail', $data, true);
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
            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0);
            $FWorksheet = new Worksheet($spreadsheet, 'Faktur');
            $FDWorksheet = new Worksheet($spreadsheet, 'DetailFaktur');
            $spreadsheet->addSheet($FWorksheet);
            $spreadsheet->addSheet($FDWorksheet);
            $sheetF = $spreadsheet->setActiveSheetIndex(0);
            $rowF = 1;

            $model = new $this->m_global();
            $npwp = $model->setTables("setting")->setWheres(["setting_name" => "npwp_fp"], true)->getDetail();
            $vNpwp = str_replace(" ","" , ($npwp->value ?? ""));
            $sheetF->setCellValue("A{$rowF}", "NPWP PENJUAL");
            $sheetF->mergeCells("A{$rowF}:B{$rowF}");
            $sheetF->setCellValue("C{$rowF}", $vNpwp);
            $rowF += 2;

            $sheetF->setCellValue("A{$rowF}", "Baris");
            $sheetF->setCellValue("b{$rowF}", "Tanggal Faktur");
            $sheetF->setCellValue("c{$rowF}", "Jenis Faktur");
            $sheetF->setCellValue("d{$rowF}", "Kode Transaksi");
            $sheetF->setCellValue("e{$rowF}", "Keterangan Tambahan");
            $sheetF->setCellValue("f{$rowF}", "Dokumen Pendukung");
            $sheetF->setCellValue("g{$rowF}", "Period Dok Pendukung");
            $sheetF->setCellValue("h{$rowF}", "Referensi");
            $sheetF->setCellValue("i{$rowF}", "Cap Fasilitas");
            $sheetF->setCellValue("j{$rowF}", "ID TKU Penjual");
            $sheetF->setCellValue("k{$rowF}", "NPWP/NIK Pembeli");
            $sheetF->setCellValue("l{$rowF}", "Jenis ID Pembeli");
            $sheetF->setCellValue("m{$rowF}", "Negara Pembeli");
            $sheetF->setCellValue("n{$rowF}", "Nomor Dokumen Pembeli");
            $sheetF->setCellValue("o{$rowF}", "Nama Pembeli");
            $sheetF->setCellValue("p{$rowF}", "Alamat Pembeli");
            $sheetF->setCellValue("q{$rowF}", "Email Pembeli");
            $sheetF->setCellValue("r{$rowF}", "ID TKU Pembeli");
            $this->getData();
            $data = $this->data->setJoins("partner p", "p.id = partner_id", "left")
                    ->setSelects(["fp.*", "p.npwp,p.invoice_street,p.invoice_city,p.invoice_state,p.invoice_country,p.invoice_zip,p.email"], true)
                    ->setGroups(["faktur_id"])->getData();
            $no = 0;
            $namaPembeli = "";
            foreach ($data as $key => $value) {
                $namaPembeli = $value->partner_nama;
                $npwpp = $value->npwp ?? "";
                $rowF += 1;
                $no += 1;
                $sheetF->setCellValue("A{$rowF}", $no);
                $sheetF->setCellValue("B{$rowF}", date("n/j/Y", strtotime($value->tanggal)));
                $sheetF->setCellValue("c{$rowF}", "Normal");
                $sheetF->setCellValue("D{$rowF}", "04");
                $sheetF->setCellValue("H{$rowF}", $value->no_sj);
                $sheetF->setCellValue("j{$rowF}", "{$vNpwp}000000");
                $sheetF->setCellValue("K{$rowF}", $npwpp);
                $sheetF->setCellValue("L{$rowF}", ($npwpp === "")?"":"TIN");
                $sheetF->setCellValue("M{$rowF}", "IDN");
                $sheetF->setCellValue("o{$rowF}", $value->partner_nama);
                $sheetF->setCellValue("P{$rowF}", $value->invoice_street);
                $sheetF->setCellValue("Q{$rowF}", $value->email);
                $npwpp = ($npwpp === "") ? "":"{$npwpp}000000";
                $sheetF->setCellValue("R{$rowF}", $npwpp);
            }
            if (count($data) > 0) {
                $rowF += 1;
                $sheetF->setCellValue("A{$rowF}", "END");
            }
            $sheetFD = $spreadsheet->setActiveSheetIndex(1);
            $rowFD = 1;
            $sheetFD->setCellValue("A{$rowFD}", "Baris");
            $sheetFD->setCellValue("b{$rowFD}", "Barang/Jasa");
            $sheetFD->setCellValue("c{$rowFD}", "Kode Barang/Jasa");
            $sheetFD->setCellValue("d{$rowFD}", "Nama Barang/Jasa");
            $sheetFD->setCellValue("e{$rowFD}", "Nama Satuan Ukur");
            $sheetFD->setCellValue("F{$rowFD}", "Harga Satuan");
            $sheetFD->setCellValue("g{$rowFD}", "Jumlah Barang Jasa");
            $sheetFD->setCellValue("h{$rowFD}", "Total Diskon");
            $sheetFD->setCellValue("i{$rowFD}", "DPP");
            $sheetFD->setCellValue("j{$rowFD}", "DPP Nilai Lain");
            $sheetFD->setCellValue("k{$rowFD}", "Tarif PPN");
            $sheetFD->setCellValue("l{$rowFD}", "PPN");
            $sheetFD->setCellValue("m{$rowFD}", "Tarif PPnBM");
            $sheetFD->setCellValue("n{$rowFD}", "PPnBM");
            $data = $this->data->setJoins("uom uo","uo.short = fpd.uom","left")->setSelects(["no_sj", "tanggal", "tax_value", "kurs_nominal", "fpd.*","satuan_ukur"],true)->setGroups(["fpd.id"])->getData();
            $tempSj = "";
            $baris = 0;
            foreach ($data as $key => $value) {
                $dpplainDikon = ($value->diskon * 11 / 12);
                $rowFD += 1;
                $baris = ($tempSj === $value->no_sj) ? $baris : $baris + 1;
                $sheetFD->setCellValue("A{$rowFD}", $baris);
                $sheetFD->setCellValue("b{$rowFD}", "A");
                $sheetFD->setCellValue("d{$rowFD}", $value->uraian);
                $sheetFD->setCellValue("e{$rowFD}", $value->satuan_ukur);
                $sheetFD->setCellValue("f{$rowFD}", ($value->harga * $value->kurs_nominal));
                $sheetFD->setCellValue("g{$rowFD}", $value->qty);
                $sheetFD->setCellValue("h{$rowFD}", ($value->diskon * $value->kurs_nominal));
                $sheetFD->setCellValue("i{$rowFD}", (($value->jumlah - $value->diskon) * $value->kurs_nominal));
                $sheetFD->setCellValue("j{$rowFD}", ($value->dpp_lain - $dpplainDikon) * $value->kurs_nominal);
                $sheetFD->setCellValue("k{$rowFD}", ($value->tax_value * 100));
                $sheetFD->setCellValue("l{$rowFD}", ($value->pajak - $value->diskon_ppn) * $value->kurs_nominal);
                $sheetFD->setCellValue("m{$rowFD}", 0.00);
                $sheetFD->setCellValue("n{$rowFD}", 0.00);

                $tempSj = $value->no_sj;
            }
            if(count($data) > 1) {
                $rowFD += 1;
                $sheetFD->setCellValue("A{$rowFD}", "END");
            }
            $periode = $this->input->post("periode");
            $nm = str_replace(" ", "_", $namaPembeli);
            $filename = "coretax_{$nm}_{$periode}";
            $url = "dist/storages/report/fakturpenjualan";
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
