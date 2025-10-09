<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require_once APPPATH . '/third_party/vendor/autoload.php';
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Bukukas
 *
 * @author RONI
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Giromundur extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }

    public function index() {
        $data['id_dept'] = 'ACGM';
        $model = new $this->m_global;
        $data["coa"] = $model->setTables("acc_coa")->setWhereIn("jenis_transaksi", ["piutang_giro", "utang_giro"])->setOrder(["kode_coa"])->getData();
        $this->load->view('report/acc/v_giro_mundur', $data);
    }

    protected function _saldo($giro = "masuk") {
        try {
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);
            $coa = $this->input->post("kode_coa");
            $saldo = 0;
            $model = new $this->m_global;
            $dt = $model->setTables("acc_giro_{$giro} gm")->setJoins("acc_giro_{$giro}_detail gmd", "giro_{$giro}_id = gm.id")
                            ->setJoins("acc_bank_{$giro}_detail bmd", "(gmd.id = bmd.giro_{$giro}_detail_id and bmd.giro_{$giro}_detail_id <> 0)", "left")
                            ->setSelects(["sum(gmd.nominal) as total"])->setWheres(["status" => "confirm", "date(gm.tanggal) < " => $tanggals[0], "gm.kode_coa" => $coa])
                            ->setWhereRaw("(cair = 0 or date(bmd.tanggal) >= '{$tanggals[0]}')")->getDetail();
            if ($dt) {
                $saldo = $dt->total;
            }
            return $saldo;
        } catch (Exception $ex) {
            throw $ex;
        }
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
                    'field' => 'kode_coa',
                    'label' => 'Laporan',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih'
                    ]
                ]
            ]);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $coa = $this->input->post("kode_coa");
            $jenis_coa = $this->input->post("jenis_coa");
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);
            $model = new $this->m_global;

            if ($jenis_coa === "utang_giro") {
                //giro keluar
                $model->setTables("acc_giro_keluar gm")->setJoins("acc_giro_keluar_detail gmd", "giro_keluar_id = gm.id")
                        ->setSelects(["gm.no_gk as no_bukti,date(gm.tanggal) as tanggal,if(partner_nama = '',lain2,partner_nama) as uraian,'C' as posisi,nominal,gmd.kode_coa,no_bg"])
                        ->setWheres(["status" => "confirm"]);
                if (count($tanggals) > 1) {
                    $model->setWheres(["date(gm.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(gm.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
                }
                if ($coa !== "") {
                    $model->setWheres(["gm.kode_coa" => $coa]);
                }
                $queryGiroMasuk = $model->getQuery();
                //bank keluar
                $model->setTables("acc_bank_keluar bm")->setJoins("acc_bank_keluar_detail bmd", "bank_keluar_id = bm.id")
                        ->setSelects(["bm.no_bk as no_bukti,date(bm.tanggal) as tanggal,if(partner_nama = '',lain2,partner_nama) as uraian,'D' as posisi,nominal,bmd.kode_coa,no_bg"])
                        ->setWheres(["status" => "confirm"]);
                if (count($tanggals) > 1) {
                    $model->setWheres(["date(bm.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(bm.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1])),
                        "bmd.no_bg <>" => ""]);
                }
                if ($coa !== "") {
                    $model->setWheres(["bmd.kode_coa" => $coa]);
                }
                $queryBankMasuk = $model->setOrder(["bm.tanggal" => "asc"])->getQuery();

                //giro masuk
                $model->setTables("acc_giro_masuk gk")->setJoins("acc_giro_masuk_detail gkd", "giro_masuk_id = gk.id")
                        ->setSelects(["gk.no_gm as no_bukti,date(gk.tanggal) as tanggal,if(partner_nama = '',lain2,partner_nama) as uraian,'D' as posisi,nominal,gkd.kode_coa,no_bg"])
                        ->setWheres(["status" => "confirm"]);
                if (count($tanggals) > 1) {
                    $model->setWheres(["date(gk.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(gk.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
                }
                if ($coa !== "") {
                    $model->setWheres(["gk.kode_coa" => $coa]);
                }
                $queryGiroKeluar = $model->getQuery();

                $table = "(({$queryGiroMasuk}) union all ({$queryBankMasuk}) union all ({$queryGiroKeluar})) as giromundur";
            } else {
                //giro masuk
                $model->setTables("acc_giro_masuk gm")->setJoins("acc_giro_masuk_detail gmd", "giro_masuk_id = gm.id")
                        ->setSelects(["gm.no_gm as no_bukti,date(gm.tanggal) as tanggal,if(partner_nama = '',lain2,partner_nama) as uraian,'C' as posisi,nominal,gmd.kode_coa,no_bg"])
                        ->setWheres(["status" => "confirm"]);
                if (count($tanggals) > 1) {
                    $model->setWheres(["date(gm.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(gm.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
                }
                if ($coa !== "") {
                    $model->setWheres(["gm.kode_coa" => $coa]);
                }
                $queryGiroMasuk = $model->getQuery();

                //bank masuk
                $model->setTables("acc_bank_masuk bm")->setJoins("acc_bank_masuk_detail bmd", "bank_masuk_id = bm.id")
                        ->setSelects(["bm.no_bm as no_bukti,date(bm.tanggal) as tanggal,if(partner_nama = '',lain2,partner_nama) as uraian,'D' as posisi,nominal,bmd.kode_coa,no_bg"])
                        ->setWheres(["status" => "confirm"]);
                if (count($tanggals) > 1) {
                    $model->setWheres(["date(bm.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(bm.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1])),
                        "bmd.no_bg <>" => ""]);
                }
                if ($coa !== "") {
                    $model->setWheres(["bmd.kode_coa" => $coa]);
                }
                $queryBankMasuk = $model->setOrder(["bm.tanggal" => "asc"])->getQuery();

                //giro keluar
                $model->setTables("acc_giro_keluar gk")->setJoins("acc_giro_keluar_detail gkd", "giro_keluar_id = gk.id")
                        ->setSelects(["gk.no_gk as no_bukti,date(gk.tanggal) as tanggal,if(partner_nama = '',lain2,partner_nama) as uraian,'D' as posisi,nominal,gkd.kode_coa,no_bg"])
                        ->setWheres(["status" => "confirm"]);
                if (count($tanggals) > 1) {
                    $model->setWheres(["date(gk.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(gk.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
                }
                if ($coa !== "") {
                    $model->setWheres(["gk.kode_coa" => $coa]);
                }
                $queryGiroKeluar = $model->getQuery();

                $table = "(({$queryGiroMasuk}) union all ({$queryBankMasuk}) union all ({$queryGiroKeluar})) as giromundur";
            }
            $model->setTables($table)->setJoins("acc_coa", "acc_coa.kode_coa = giromundur.kode_coa", "left")
                    ->setOrder(["uraian" => "asc", "no_bg" => "asc", "tanggal" => "asc",])
                    ->setSelects(["no_bukti,tanggal,uraian,posisi,nominal,concat(giromundur.kode_coa,'-',acc_coa.nama) as coa,no_bg"]);
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function search() {
        try {
            $model = $this->_query();
            $jenis_coa = $this->input->post("jenis_coa");
            $data["data"] = $model->getData();
            $data["saldo"] = ($jenis_coa === "utang_giro") ? $this->_saldo("keluar") : $this->_saldo();
            $html = $this->load->view('report/acc/v_giro_mundur_detail', $data, true);
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
            $model = $this->_query();
            $data = $model->getData();
            $jenis_coa = $this->input->post("jenis_coa");

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $row = 1;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'Tanggal');
            $sheet->setCellValue("C{$row}", 'No Bukti');
            $sheet->setCellValue("D{$row}", 'Uraian');
            $sheet->setCellValue("E{$row}", 'No Cek / BG');
            $sheet->setCellValue("F{$row}", 'Baru');
            $sheet->setCellValue("G{$row}", 'Cair');
            $sheet->setCellValue("H{$row}", 'Saldo');
            $saldo = ($jenis_coa === "utang_giro") ? $this->_saldo("keluar") : $this->_saldo();
            if (count($data) > 0) {
                $row += 1;
                $sheet->setCellValue("D{$row}", "Saldo Awal");
                $sheet->setCellValue("F{$row}", 0);
                $sheet->setCellValue("G{$row}", 0);
                $sheet->setCellValue("H{$row}", $saldo);
            }

            $kredits = 0;
            $debets = 0;
            $temp = "";
            $noUrut = 0;
            foreach ($data as $key => $value) {
                $row += 1;
                $showUrut = "";
                $no_bukti = "";
                $dt = "";
                if ($value->no_bukti !== $temp) {
                    $noUrut++;
                    $showUrut = $noUrut;
                    $no_bukti = $value->no_bukti;
                    $dt = $value->tanggal;
                }
                $debet = 0;
                $kredit = 0;
                if ($value->posisi === "D") {
                    $debet = $value->nominal;
                    $debets += $debet;
                    $saldo -= $debet;
                } else {
                    $kredit = $value->nominal;
                    $kredits += $kredit;
                    $saldo += $kredit;
                }

                $sheet->setCellValue("A{$row}", $showUrut);
                $sheet->setCellValue("B{$row}", $dt);
                $sheet->setCellValue("C{$row}", $no_bukti);
                $sheet->setCellValue("D{$row}", $value->uraian);
                $sheet->setCellValue("E{$row}", $value->no_bg);
                $sheet->setCellValue("F{$row}", $kredit);
                $sheet->setCellValue("G{$row}", $debet);
                $sheet->setCellValue("H{$row}", $saldo);

                $temp = $value->no_bukti;
            }
            if (count($data) > 0) {
                $row += 1;
                $sheet->setCellValue("D{$row}", "Saldo Akhir");
                $sheet->setCellValue("F{$row}", $kredits);
                $sheet->setCellValue("G{$row}", $debets);
                $sheet->setCellValue("H{$row}", $saldo);
            }

            $tanggal = $this->input->post("tanggal");
//            $writer = new Xlsx($spreadsheet);
            $filename = "Giro Mundur {$tanggal}";
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
