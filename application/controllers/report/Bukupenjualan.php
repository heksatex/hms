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
            $model->setTables("acc_faktur_penjualan fp")
//                    ->setJoins("acc_faktur_penjualan_detail fpd", "fp.id = faktur_id")
                    ->setJoins("acc_jurnal_entries je", "je.kode = fp.jurnal")
                    ->setJoins("acc_jurnal_entries_items jei", "jei.kode = je.kode")
                    ->setJoins("faktur_jurnal", "(jei.kode = jurnal_kode and jurnal_order = jei.row_order)", "left")
                    ->setJoins("acc_faktur_penjualan_detail fjd", "fjd.id = faktur_jurnal.faktur_detail_id", "left")
                    ->setJoins("acc_coa ac", "ac.kode_coa = jei.kode_coa", "left")
                    ->setSelects(["fp.no_faktur_internal,fp.no_faktur_pajak,no_sj,fp.tanggal", "partner_nama", "jei.*", "ac.nama as coa", "fjd.harga,fjd.qty,fjd.uom,jenis_ppn,uraian,warna,fjd.pajak", ""])
                    ->setWheres(["date(fp.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(fp.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1])), "fp.status" => "confirm"
                            ,])
                    ->setOrder(["jei.kode_coa" => "asc", "no_faktur_internal" => "asc", "uraian" => "asc"]);
            if ($posisi === "bks") {
                $model->setWhereRaw("jei.kode_coa REGEXP '^[4,8]'");
            } else {
                $model->setWheres(["jei.posisi" => strtoupper($posisi)]);
            }

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
            $sheet->setCellValue("D{$row}", 'Faktur Pajak');
            $sheet->setCellValue("E{$row}", 'Tanggal');
            $sheet->setCellValue("F{$row}", 'Nama');
            $sheet->setCellValue("G{$row}", 'Customer');
            $sheet->setCellValue("H{$row}", 'Coa');
            $sheet->setCellValue("i{$row}", 'Nama Coa');
            $sheet->setCellValue("j{$row}", 'Jenis Ppn');
            $sheet->setCellValue("k{$row}", 'Curr');
            $sheet->setCellValue("l{$row}", 'Kurs');
            $sheet->setCellValue("m{$row}", 'Qty');
            $sheet->setCellValue("n{$row}", 'Uom');
            if ($posisi !== "bks") {
                $sheet->setCellValue("o{$row}", 'Harga');
                $sheet->setCellValue("p{$row}", 'Total Harga');
            } else {
                $sheet->setCellValue("o{$row}", 'Harga $');
                $sheet->setCellValue("p{$row}", 'Harga Rp');
                $sheet->setCellValue("q{$row}", 'Jumlah $');
                $sheet->setCellValue("r{$row}", 'Jumlah Rp');
                $sheet->setCellValue("s{$row}", 'Ppn');
                $sheet->setCellValue("t{$row}", 'Total Rp');
            }
            $no = 0;
            $grandTotal = 0;
            $total = 0;
            $totalHarga = 0;
            $totalHargaValas = 0;
            $totalPpn = 0;
            $GrandtotalPpn = 0;

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
                    $nama = "{$value->uraian} {$value->warna}";
                }
                $hargaRp = 0;
                $hargaValas = 0;
                $JumlahValas = 0;
                $ppn = $value->pajak * $value->kurs;
                $totalPpn += $ppn;
                $TotalRp = $harga + $ppn;
                $GrandtotalPpn += $ppn;
                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $value->no_faktur_internal);
                $sheet->setCellValue("C{$row}", $value->no_sj);
                $sheet->setCellValue("d{$row}", $value->no_faktur_pajak);
                $sheet->setCellValue("e{$row}", $value->tanggal);
                $sheet->setCellValue("f{$row}", $nama);
                $sheet->setCellValue("g{$row}", $value->partner_nama);
                $sheet->setCellValue("h{$row}", $value->kode_coa);
                $sheet->setCellValue("i{$row}", $value->coa);
                $sheet->setCellValue("j{$row}", $value->jenis_ppn);
                $sheet->setCellValue("k{$row}", $value->kode_mua);
                $sheet->setCellValue("l{$row}", $value->kurs);
                $sheet->setCellValue("m{$row}", ($value->qty) ? $value->qty : $q);
                $sheet->setCellValue("n{$row}", ($value->qty) ? $value->uom : $u);
                if ($posisi !== "bks") {
                    $sheet->setCellValue("o{$row}", $value->harga);
                    $sheet->setCellValue("p{$row}", ($value->qty) ? $harga : $value->nominal);
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
                    $sheet->setCellValue("o{$row}", $hargaValas);
                    $sheet->setCellValue("p{$row}", $hargaRp);
                    $sheet->setCellValue("q{$row}", $JumlahValas);
                    $sheet->setCellValue("r{$row}", $harga);
                    $sheet->setCellValue("s{$row}", $ppn);
                    $sheet->setCellValue("t{$row}", $TotalRp);
                }
                if (isset($data[$key + 1])) {
                    if ($value->kode_coa !== $data[$key + 1]->kode_coa) {
                        $row += 1;
                        $sheet->setCellValue("h{$row}", $value->kode_coa);
                        $sheet->setCellValue("i{$row}", "Total {$value->coa}");
                        if ($posisi !== "bks") {
                            $sheet->setCellValue("p{$row}", ($value->qty) ? $totalHarga : $total);
                        } else {
                            $sheet->setCellValue("q{$row}", $JumlahValas);
                            $sheet->setCellValue("r{$row}", $hargaRp);
                            $sheet->setCellValue("s{$row}", $ppn);
                            $sheet->setCellValue("t{$row}", $total);
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
                    $sheet->setCellValue("h{$row}", $value->kode_coa);
                    $sheet->setCellValue("i{$row}", "Total {$value->coa}");
                    if ($posisi !== "bks") {
                        $sheet->setCellValue("p{$row}", ($value->qty) ? $totalHarga : $total);
                    } else {
                        $sheet->setCellValue("q{$row}", $JumlahValas);
                        $sheet->setCellValue("r{$row}", $hargaRp);
                        $sheet->setCellValue("s{$row}", $ppn);
                        $sheet->setCellValue("t{$row}", $total);
                    }
                    $total = 0;
                    $totalHarga = 0;
                    $totalHargaValas = 0;
                    $JumlahValas = 0;
                    $JumlahRp = 0;
                    $totalPpn = 0;
                }
            }
            $sheet->getStyle("C2:C{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
            $sheet->getStyle("L2:L{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("m2:m{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("o2:o{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            $sheet->getStyle("p2:p{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            if ($posisi === "bks") {
                $sheet->getStyle("q2:q{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle("r2:r{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle("s2:s{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                $sheet->getStyle("t2:t{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }

//            $writer = new Xlsx($spreadsheet);
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
