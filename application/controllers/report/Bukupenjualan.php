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
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

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
            $coaPemb = $model->setTables("setting")->setWheres(["setting_name" => "selisih_pembulatan_penjualan"])->setSelects(["value"])->getDetail();

            if ($posisi === "bks") {
                $model->setTables("acc_faktur_penjualan fp")
                        ->setJoins("acc_faktur_penjualan_detail fjd", "fp.id = fjd.faktur_id")
                        ->setJoins("faktur_jurnal fj", "fj.faktur_detail_id = fjd.id", "left")
                        ->setJoins("acc_jurnal_entries_items jei", "(jei.kode = fj.jurnal_kode and jei.row_order = jurnal_order)", "left")
                        ->setWhereRaw("jei.kode_coa REGEXP '^[4,8]'");
            } else {
                $model->setTables("acc_faktur_penjualan fp")
                        ->setJoins("acc_jurnal_entries je", "je.kode = fp.jurnal")
                        ->setJoins("acc_jurnal_entries_items jei", "jei.kode = je.kode")
                        ->setJoins("faktur_jurnal", "(jei.kode = jurnal_kode and jurnal_order = jei.row_order)", "left")
                        ->setJoins("acc_faktur_penjualan_detail fjd", "fjd.id = faktur_jurnal.faktur_detail_id", "left")
                        ->setWheres(["jei.posisi" => strtoupper($posisi)]);
            }
            $model->setJoins("acc_coa ac", "ac.kode_coa = jei.kode_coa", "left")
                    ->setSelects(["fp.no_faktur_internal,fp.no_faktur_pajak,no_sj,fp.tanggal", "partner_nama", "jei.*", "ac.nama as coa", "fjd.harga,fjd.qty,fjd.uom,jenis_ppn,uraian,warna,fjd.pajak,no_inv_ekspor", ""])
                    ->setWheres(["date(fp.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(fp.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1])), "fp.status" => "confirm"
                            ,])
                    ->setOrder(["jei.kode_coa" => "asc", "no_faktur_internal" => "asc", "uraian" => "asc"]);

            if ($uraian !== "") {
                $model->setWhereRaw("(uraian LIKE '%{$uraian}%' or warna LIKE '%{$uraian}%')");
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
            if ($coaPemb) {
                $model->setWheres(["jei.kode_coa <>" => $coaPemb->value]);
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
            $data["posisi"] = $this->input->post("posisi");
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
            $posisi = $this->input->post("posisi");
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
            $posisis = ($posisi === "bks") ? "BK - Sales" : "";
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue("A1", 'Periode');
            $sheet->setCellValue("B1", $tanggal);
            $sheet->setCellValue("A2", 'Filter : ');
            $sheet->setCellValue("B2", $filter);
            $row = 4;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'No Faktur');
            $sheet->setCellValue("C{$row}", 'No Inv Eks');
            $sheet->setCellValue("D{$row}", 'No SJ');
            $sheet->setCellValue("E{$row}", 'Faktur Pajak');
            $sheet->setCellValue("F{$row}", 'Tanggal');
            $sheet->setCellValue("G{$row}", 'Nama');
            $sheet->setCellValue("H{$row}", 'Customer');
            $sheet->setCellValue("i{$row}", 'Coa');
            $sheet->setCellValue("j{$row}", 'Nama Coa');
            $sheet->setCellValue("k{$row}", 'Jenis Ppn');
            $sheet->setCellValue("l{$row}", 'Curr');
            $sheet->setCellValue("m{$row}", 'Kurs');
            $sheet->setCellValue("n{$row}", 'Qty');
            $sheet->setCellValue("o{$row}", 'Uom');
            if ($posisi !== "bks") {
                $sheet->setCellValue("p{$row}", 'Harga');
                $sheet->setCellValue("q{$row}", 'Total Harga');
            } else {
                $sheet->setCellValue("p{$row}", 'Harga Vls');
                $sheet->setCellValue("q{$row}", 'Harga Rp');
                $sheet->setCellValue("r{$row}", 'Jumlah Vls');
                $sheet->setCellValue("s{$row}", 'Jumlah Rp');
                $sheet->setCellValue("t{$row}", 'Ppn');
                $sheet->setCellValue("u{$row}", 'Total Rp');
            }
            $no = 0;
            $grandTotal = 0;
            $total = 0;
            $totalHarga = 0;
            $totalHargaValas = 0;
            $totalPpn = 0;
            $GrandtotalRp = 0;

            foreach ($data as $key => $value) {
                $harga = ($value->harga * $value->qty) * $value->kurs;
                $row++;
                $no++;
                $qty = explode("/ ", $value->nama);
                $q = "";
                $u = "";
                $nama = "";
                if (count($qty) > 1) {
                    $qtys = trim(end($qty));
                    $qu = explode(" ", $qtys);
                    $q = $qu[0] ?? "";
                    $u = $qu[1] ?? "";
                    $nama = "{$value->uraian}";
                    $nama .= ($value->warna === "") ? "" : "/{$value->warna}";
                }
                $hargaRp = 0;
                $hargaValas = 0;
                $JumlahValas = 0;
                $ppn = $value->pajak * $value->kurs;
                $totalPpn += $ppn;
                $TotalRp = $harga + $ppn;
                $GrandtotalRp += $harga;
                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $value->no_faktur_internal);
                $sheet->setCellValue("c{$row}", $value->no_inv_ekspor);
                $sheet->setCellValue("d{$row}", $value->no_sj);
                $sheet->setCellValue("e{$row}", $value->no_faktur_pajak);
                $sheet->setCellValue("f{$row}", $value->tanggal);
                $sheet->setCellValue("g{$row}", $nama);
                $sheet->setCellValue("h{$row}", $value->partner_nama);
                $sheet->setCellValue("i{$row}", $value->kode_coa);
                $sheet->setCellValue("j{$row}", $value->coa);
                $sheet->setCellValue("k{$row}", $value->jenis_ppn);
                $sheet->setCellValue("l{$row}", $value->kode_mua);
                $sheet->setCellValue("m{$row}", $value->kurs);
                $sheet->setCellValue("n{$row}", ($value->qty) ? $value->qty : $q);
                $sheet->setCellValue("o{$row}", ($value->qty) ? $value->uom : $u);
                if ($posisi !== "bks") {
                    $sheet->setCellValue("p{$row}", $value->harga);
                    $sheet->setCellValue("q{$row}", ($value->qty) ? $harga : $value->nominal);
                    $hargaRp = $value->harga;
                } else {
                    if ($value->kurs > 1) {
                        $hargaValas = $value->harga;
                        $JumlahValas = $value->qty * $value->harga;
                        $totalHargaValas += $JumlahValas;
                    } else {
                        $hargaRp = $value->harga;
                    }
                    $totalHarga += $harga;
                    $grandTotal += $TotalRp;
                    $total += $TotalRp;
                    $sheet->setCellValue("p{$row}", $hargaValas);
                    $sheet->setCellValue("q{$row}", $hargaRp);
                    $sheet->setCellValue("r{$row}", $JumlahValas);
                    $sheet->setCellValue("s{$row}", $harga);
                    $sheet->setCellValue("t{$row}", $ppn);
                    $sheet->setCellValue("u{$row}", $TotalRp);
                }
                if (isset($data[$key + 1])) {
                    if ($value->kode_coa !== $data[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("i{$row}", $value->kode_coa);
                        $sheet->setCellValue("j{$row}", "Total {$value->coa}");
                        if ($posisi !== "bks") {
                            $sheet->setCellValue("q{$row}", ($value->qty) ? $totalHarga : $total);
                        } else {
                            $sheet->setCellValue("r{$row}", $JumlahValas);
                            $sheet->setCellValue("s{$row}", $hargaRp);
                            $sheet->setCellValue("t{$row}", $ppn);
                            $sheet->setCellValue("u{$row}", $total);
                        }
                        $total = 0;
                        $totalHarga = 0;
                        $totalHargaValas = 0;
                        $JumlahValas = 0;
                        $JumlahRp = 0;
                        $totalPpn = 0;
                    }
                } else {
                    $row += 1;
                    $sheet->setCellValue("i{$row}", $value->kode_coa);
                    $sheet->setCellValue("j{$row}", "Total {$value->coa}");
                    if ($posisi !== "bks") {
                        $sheet->setCellValue("q{$row}", ($value->qty) ? $totalHarga : $total);
                    } else {
                        $sheet->setCellValue("r{$row}", $JumlahValas);
                        $sheet->setCellValue("s{$row}", $hargaRp);
                        $sheet->setCellValue("t{$row}", $ppn);
                        $sheet->setCellValue("u{$row}", $total);
                    }
                    $total = 0;
                    $totalHarga = 0;
                    $totalHargaValas = 0;
                    $JumlahValas = 0;
                    $JumlahRp = 0;
                    $totalPpn = 0;
                }
            }

            if ($GrandtotalRp > 0) {
                $row += 1;
                $sheet->setCellValue("j{$row}", "Grand Total Rp");
                if ($posisi === "bks")
                    $sheet->setCellValue("u{$row}", $GrandtotalRp);
                else
                    $sheet->setCellValue("q{$row}", $GrandtotalRp);
            }
            $sheet->getStyle("d2:d{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle("m2:m{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("n2:n{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("p2:p{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("q2:q{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            if ($posisi === "bks") {
                $sheet->getStyle("r2:r{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle("s2:s{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle("t2:t{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle("u2:u{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }

//            $writer = new Xlsx($spreadsheet);
            $filename = "Buku Penjualan {$tanggal} {$posisis}";
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
