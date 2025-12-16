<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require_once APPPATH . '/third_party/vendor/autoload.php';
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Bukupenjualan
 *
 * @author RONI
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Bukupenjualan_1 extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
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
            $tanggal = $this->input->post("tanggal");
            $customer = $this->input->post("customer");
            $uraian = $this->input->post("uraian");
            $fakturPajak = $this->input->post("faktur");
            $tanggals = explode(" - ", $tanggal);
            $model = new $this->m_global;
            $model->setTables("acc_faktur_penjualan fp")
                    ->setJoins("acc_faktur_penjualan_detail fpd", "fp.id = faktur_id")
                    ->setJoins("currency_kurs ck", "ck.id = fp.kurs")
                    ->setJoins("acc_coa coa","fpd.no_acc = coa.kode_coa","left")
                    ->setOrder(["fp.tanggal" => "asc"])->setSelects(["no_faktur_internal,no_faktur_pajak,fp.partner_nama,nominal_diskon,tax_value"])
                    ->setSelects(["fpd.*,concat(fpd.uraian,' ',fpd.warna) as uraian,fp.kurs", "fp.no_sj,fp.tanggal", "ck.currency as nama_curr"])
                    ->setSelects(["fp.diskon_ppn as total_diskon_ppn", "fp.diskon as dpp_diskon","coa.nama as nama_coa"])
                    ->setWheres(["date(fp.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(fp.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1])), "fp.status" => "confirm"]);
            if ($uraian !== "") {
                $model->setWhereRaw("concat(uraian,' ',warna) LIKE '%{$uraian}%'");
            }
            if (!empty($customer)) {
                $model->setWheres(["partner_id" => $customer]);
            }
            if (!empty($fakturPajak)) {
                if ($fakturPajak === 'ada') {
                    $model->setWheres(["no_faktur_pajak <>" => ""]);
                } else {
                    $model->setWheres(["no_faktur_pajak" => ""]);
                }
            }
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function index() {
        $data['id_dept'] = 'RBPJ';
        $this->load->view('report/acc/v_buku_penjulan_1', $data);
    }

    public function search() {
        try {
            $model = $this->_query();
            $count = $model->getDataCountFiltered();
            $data["data"] = $model->getData();
            $model = new $this->m_global;
            $data["coa_ppn_diskon"] = $model->setTables("setting")->setJoins("acc_coa c","c.kode_coa = value","left")->setSelects(["c.nama","value"])
                    ->setWheres(["setting_name" => "coa_penjualan_ppn_diskon"])->getDetail();
            $data["coa_dpp_diskon"] = $model->setTables("setting")->setJoins("acc_coa c","c.kode_coa = value","left")->setSelects(["c.nama","value"])
                    ->setWheres(["setting_name" => "coa_penjualan_dpp_diskon"])->getDetail();
            $html = $this->load->view("report/acc/v_buku_penjulan_detail_1", $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html, "jumlah" => $count)));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function export() {
        try {
            $model = $this->_query();
            $data = $model->getData();

            $tanggal = $this->input->post("tanggal");
            $customer = $this->input->post("customer");
            $uraian = $this->input->post("uraian");
            $fakturPajak = $this->input->post("faktur");
            $filter = "";
            if ($uraian !== "") {
                $filter .= "Uraian : {$uraian}, ";
            }
            if (!empty($customer)) {
                $filter .= "Customer : " . $data[0]->partner_nama ?? '' . ", ";
            }
            if (!empty($fakturPajak)) {
                $filter .= "Mempunyai Faktur Pajak : {$fakturPajak}";
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue("A1", 'Periode');
            $sheet->setCellValue("B1", $tanggal);
            $sheet->setCellValue("A2", 'Filter : ');
            $sheet->setCellValue("B2", $filter);
            $row = 4;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'No Faktur');
            $sheet->setCellValue("C{$row}", 'No SJ');
            $sheet->setCellValue("D{$row}", 'Tanggal');
            $sheet->setCellValue("E{$row}", 'Uraian');
            $sheet->setCellValue("F{$row}", 'Customer');
            $sheet->setCellValue("G{$row}", 'Qty');
            $sheet->setCellValue("H{$row}", 'Uom');
            $sheet->setCellValue("I{$row}", 'Currency');
            $sheet->setCellValue("J{$row}", 'Kurs');
            $sheet->setCellValue("K{$row}", 'Harga');
            $sheet->setCellValue("L{$row}", 'DPP');
            $sheet->setCellValue("M{$row}", 'Diskon');
            $sheet->setCellValue("N{$row}", 'PPN');
            $sheet->setCellValue("O{$row}", 'No Faktur Pajak');
            $no = 0;
            foreach ($data as $key => $value) {
                $row++;
                $no++;
                $harga = $value->harga * $value->kurs;
                $dpp = $value->jumlah * $value->kurs;
                $pajak = $value->pajak * $value->kurs;
                $diskon = $value->diskon * $value->kurs;

                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $value->no_faktur_internal);
                $sheet->setCellValue("C{$row}", $value->no_sj);
                $sheet->setCellValue("D{$row}", $value->tanggal);
                $sheet->setCellValue("E{$row}", $value->uraian);
                $sheet->setCellValue("F{$row}", $value->partner_nama);
                $sheet->setCellValue("G{$row}", $value->qty);
                $sheet->setCellValue("H{$row}", $value->uom);
                $sheet->setCellValue("I{$row}", $value->nama_curr);
                $sheet->setCellValue("J{$row}", $value->kurs);
                $sheet->setCellValue("K{$row}", $harga);
                $sheet->setCellValue("L{$row}", $dpp);
                $sheet->setCellValue("M{$row}", $diskon);
                $sheet->setCellValue("N{$row}", $pajak);
                $sheet->setCellValue("O{$row}", $value->no_faktur_pajak);
            }
            $writer = new Xlsx($spreadsheet);
            $filename = "Buku Penjualan {$tanggal}";
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
