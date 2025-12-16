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

class Bukupenjualan extends MY_Controller {

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
            $posisi = $this->input->post("posisi");
            $tanggals = explode(" - ", $tanggal);
            $model = new $this->m_global;
            $model->setTables("acc_faktur_penjualan fp")
//                    ->setJoins("acc_faktur_penjualan_detail fpd", "fp.id = faktur_id")
                    ->setJoins("acc_jurnal_entries je", "je.kode = fp.jurnal")
                    ->setJoins("acc_jurnal_entries_items jei", "jei.kode = je.kode")
                    ->setJoins("acc_coa ac", "ac.kode_coa = jei.kode_coa", "left")
                    ->setSelects(["fp.no_faktur_internal,no_sj,fp.tanggal", "partner_nama", "jei.*", "ac.nama as coa"])
                    ->setWheres(["date(fp.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(fp.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1])), "fp.status" => "confirm"
                        , "jei.posisi" => strtoupper($posisi)])
                    ->setOrder(["jei.kode_coa" => "asc"]);
            if ($uraian !== "") {
                $model->setWhereRaw("jei.nama LIKE '%{$uraian}%'");
            }
            if (!empty($customer)) {
                $model->setWheres(["fp.partner_id" => $customer]);
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
        $this->load->view('report/acc/v_buku_penjulan', $data);
    }

    public function search() {
        try {
            $model = $this->_query();
            $count = $model->getDataCountFiltered();
            $data["data"] = $model->getData();
            $html = $this->load->view("report/acc/v_buku_penjulan_detail", $data, true);
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
            $sheet->setCellValue("G{$row}", 'Coa');
            $sheet->setCellValue("H{$row}", 'Nama Coa');
            $sheet->setCellValue("I{$row}", 'Qty');
            $sheet->setCellValue("J{$row}", 'Uom');
            $sheet->setCellValue("K{$row}", 'Total Nominal');
            $no = 0;
            $grandTotal = 0;
            $total = 0;
            foreach ($data as $key => $value) {
                $grandTotal += $value->nominal;
                $total += $value->nominal;
                $row++;
                $no++;
                $qty = explode("/ ", $value->nama);
                $q = "";
                $u = "";
                if (count($qty) > 1) {
                    $qtys = trim(end($qty));
                    $qu = explode(" ", $qtys);
                    $q = $qu[0] ?? "";
                    $u = $qu[1] ?? "";
                }

                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $value->no_faktur_internal);
                $sheet->setCellValue("C{$row}", $value->no_sj);
                $sheet->setCellValue("D{$row}", $value->tanggal);
                $sheet->setCellValue("E{$row}", $value->nama);
                $sheet->setCellValue("F{$row}", $value->partner_nama);
                $sheet->setCellValue("G{$row}", $value->kode_coa);
                $sheet->setCellValue("H{$row}", $value->coa);
                $sheet->setCellValue("I{$row}", $q);
                $sheet->setCellValue("J{$row}", $u);
                $sheet->setCellValue("K{$row}", $value->nominal);
                if (isset($data[$key + 1])) {
                    if ($value->kode_coa !== $data[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("G{$row}", $value->kode_coa);
                        $sheet->setCellValue("H{$row}", "Total {$value->coa}");
                        $sheet->setCellValue("K{$row}", $total);
                        $total = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("G{$row}", $value->kode_coa);
                    $sheet->setCellValue("H{$row}", "Total {$value->coa}");
                    $sheet->setCellValue("K{$row}", $total);
                    $total = 0;
                }
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
