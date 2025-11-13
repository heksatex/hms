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

class Bukukas extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
        $this->load->library('periodesaldo');
    }

    public function index() {
        $data['id_dept'] = 'BACK';
        $model = new $this->m_global;
        $data["coa"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "kas"])->setOrder(["kode_coa"])->getData();
        $this->load->view('report/acc/v_buku_kas', $data);
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
                    'label' => 'Kas',
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
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);
            $model = new $this->m_global;
            $model->setTables("acc_kas_masuk km")->setJoins("acc_kas_masuk_detail kmd", "kas_masuk_id = km.id")
                    ->setJoins("currency_kurs ck", "ck.id = currency_id", "left")
                    ->setSelects(["km.no_km as no_bukti,date(km.tanggal) as tanggal,kmd.uraian,'D' as posisi,nominal,kmd.kode_coa,partner_nama,lain2,ck.currency as nama_curr,kmd.kurs"])
                    ->setWheres(["status" => "confirm"]);
            if (count($tanggals) > 1) {
                $model->setWheres(["date(km.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(km.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
            }
            if ($coa !== "") {
                $model->setWheres(["km.kode_coa" => $coa]);
            }
            $queryKasMasuk = $model->getQuery();

            $model->setTables("acc_kas_keluar kk")->setJoins("acc_kas_keluar_detail kkd", "kas_keluar_id = kk.id")
                    ->setJoins("currency_kurs ck", "ck.id = currency_id", "left")
                    ->setSelects(["kk.no_kk as no_bukti,date(kk.tanggal) as tanggal,kkd.uraian,'K' as posisi,nominal,kkd.kode_coa,partner_nama,lain2,ck.currency as nama_curr,kkd.kurs"])
                    ->setWheres(["status" => "confirm"]);
            if (count($tanggals) > 1) {
                $model->setWheres(["date(kk.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(kk.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
            }
            if ($coa !== "") {
                $model->setWheres(["kk.kode_coa" => $coa]);
            }
            $queryKasKeluar = $model->getQuery();

            $table = "({$queryKasMasuk} union all {$queryKasKeluar}) as kas";
            $model->setTables($table)->setJoins("acc_coa", "acc_coa.kode_coa = kas.kode_coa", "left")
                    ->setSelects(["no_bukti,tanggal,uraian,posisi,nominal,concat(kas.kode_coa,'-',acc_coa.nama) as coa", "partner_nama,lain2,nama_curr,kurs"]);
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    protected function _getSaldoAwal() {
        try {
            $coa = $this->input->post("kode_coa");
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);
            $model = new $this->m_global;
            $entries = $model->setTables("acc_jurnal_entries_items jei")->setJoins("acc_jurnal_entries je", 'je.kode = jei.kode')
                            ->setWheres(["date(je.tanggal_dibuat) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(je.tanggal_dibuat) <=" => date("Y-m-d", strtotime($tanggals[1])),
                                'je.status' => 'posted'])
                            ->setSelects(["jei.posisi, jei.kode_coa,  IFNULL(SUM(CASE WHEN jei.posisi = 'D' THEN jei.nominal ELSE 0 END),0) AS total_debit,   IFNULL(SUM(CASE WHEN jei.posisi = 'C' THEN jei.nominal ELSE 0 END),0) AS total_credit"])
                            ->setGroups(["jei.kode_coa"])->setWheres(["jei.kode_coa" => $coa])->getQuery();

            //saldodebet
            $start = $this->periodesaldo->get_start_periode();
            $tgl_dari = date("Y-m-d 00:00:00", strtotime($start));
            $tgl_sampai = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($tanggals[0])));
            $saldoDebet = $model->setTables("acc_jurnal_entries je")->setJoins('acc_jurnal_entries_items jei', 'jei.kode = je.kode')->setGroups(["jei.kode_coa"])
                    ->setSelects(["jei.kode_coa, SUM(jei.nominal) as total_debit"])
                    ->setWheres(['je.tanggal_dibuat >= ' => $tgl_dari, 'je.tanggal_dibuat <= ' => $tgl_sampai, 'je.status' => 'posted', 'jei.posisi' => "D"])
                    ->getQuery();

            $saldoKredit = $model->setTables("acc_jurnal_entries je")->setJoins('acc_jurnal_entries_items jei', 'jei.kode = je.kode')->setGroups(["jei.kode_coa"])
                    ->setSelects(["jei.kode_coa, SUM(jei.nominal) as total_credit"])
                    ->setWheres(['je.tanggal_dibuat >= ' => $tgl_dari, 'je.tanggal_dibuat <= ' => $tgl_sampai, 'je.status' => 'posted', 'jei.posisi' => "C"])
                    ->getQuery();
            //Kredit
            $model->setTables("acc_coa coa")->setJoins("({$saldoDebet}) as debit_sbl", "debit_sbl.kode_coa = coa.kode_coa", "left")
                    ->setJoins("({$saldoKredit}) as credit_sbl", "credit_sbl.kode_coa = coa.kode_coa", "left")
                    ->setJoins("({$entries}) as jr ", "jr.kode_coa = coa.kode_coa", "left")
                    ->setOrder(["coa.kode_coa" => "asc"])->setWheres(["coa.kode_coa" => $coa])
                    ->setSelects(["coa.kode_coa, coa.nama as nama_coa,coa.saldo_normal,coa.saldo_awal,COALESCE(debit_sbl.total_debit, 0) as total_debit_sbl",
                        "COALESCE(credit_sbl.total_credit, 0) as total_credit_sbl", "COALESCE(jr.total_debit, 0) as total_debit", "COALESCE(jr.total_credit, 0) as total_credit",
                        "CASE 
                                WHEN coa.saldo_normal = 'D' THEN 
                                    (coa.saldo_awal + COALESCE(debit_sbl.total_debit, 0) - COALESCE(credit_sbl.total_credit, 0))
                                WHEN coa.saldo_normal = 'C' THEN 
                                    (coa.saldo_awal + COALESCE(credit_sbl.total_credit, 0) - COALESCE(debit_sbl.total_debit, 0))
                                ELSE coa.saldo_awal
                            END AS saldo_awal_final"]);
            return $model->getDetail();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function search() {
        try {
            $coa = $this->input->post("coa");
            if (strpos(strtolower($coa), "valas") !== false) {
                $valas = true;
            } else {
                $valas = false;
            }
            $data["valas"] = $valas;
            $model = $this->_query();
            $data["saldo"] = $this->_getSaldoAwal();
            $data["data"] = $model->getData();
            $html = $this->load->view('report/acc/v_buku_kas_detail', $data, true);
            $thead = $this->load->view('report/acc/v_buku_kas_valas_header', ["valas" => $valas], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html, "head" => $thead)));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function export() {
        try {
            $coa = $this->input->post("coa");
            if (strpos(strtolower($coa), "valas") !== false) {
                $valas = true;
            } else {
                $valas = false;
            }

            $model = $this->_query();
            $data = $model->getData();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $saldos = 0;
            $row = 1;
            if ($valas) {
                $sheet->setCellValue("A{$row}", 'No');
                $sheet->mergeCells("A{$row}:A2");
                $sheet->setCellValue("B{$row}", 'Tanggal');
                $sheet->mergeCells("B{$row}:B2");
                $sheet->setCellValue("C{$row}", 'No Bukti');
                $sheet->mergeCells("C{$row}:C2");
                $sheet->setCellValue("D{$row}", 'Uraian');
                $sheet->mergeCells("D{$row}:D2");
                $sheet->setCellValue("E{$row}", 'No Acc');
                $sheet->mergeCells("E{$row}:E2");
                $sheet->setCellValue("F{$row}", 'USD');
                $sheet->mergeCells("F{$row}:G{$row}");
                $sheet->setCellValue("H{$row}", 'EURO');
                $sheet->mergeCells("H{$row}:I{$row}");
                $sheet->setCellValue("J{$row}", 'Saldo');
                $sheet->mergeCells("J{$row}:J2");
                $row += 1;
                $sheet->setCellValue("F{$row}", 'Debet');
                $sheet->setCellValue("G{$row}", 'Kredit');
                $sheet->setCellValue("H{$row}", 'Debet');
                $sheet->setCellValue("I{$row}", 'Kredit');
                if (count($data) > 0) {
                    $data_saldo = $this->_getSaldoAwal();
                    $saldos = floatval($data_saldo->saldo_awal_final);
                    $row += 1;
                    $sheet->setCellValue("D{$row}", "Saldo Awal");
                    $sheet->setCellValue("F{$row}", '0');
                    $sheet->setCellValue("G{$row}", '0');
                    $sheet->setCellValue("H{$row}", '0');
                    $sheet->setCellValue("I{$row}", '0');
                    $sheet->setCellValue("J{$row}", $saldos);
                }
            } else {
                $sheet->setCellValue("A{$row}", 'No');
                $sheet->setCellValue("B{$row}", 'Tanggal');
                $sheet->setCellValue("C{$row}", 'No Bukti');
                $sheet->setCellValue("D{$row}", 'Uraian');
                $sheet->setCellValue("E{$row}", 'No Acc');
                $sheet->setCellValue("F{$row}", 'Debet');
                $sheet->setCellValue("G{$row}", 'Kredit');
                $sheet->setCellValue("H{$row}", 'Saldo');

                if (count($data) > 0) {
                    $data_saldo = $this->_getSaldoAwal();
                    $saldos = floatval($data_saldo->saldo_awal_final);
                    $row += 1;
                    $sheet->setCellValue("D{$row}", "Saldo Awal");
                    $sheet->setCellValue("F{$row}", 0);
                    $sheet->setCellValue("G{$row}", 0);
                    $sheet->setCellValue("H{$row}", $saldos);
                }
            }

            
            $kredits = 0;
            $debets = 0;
            $temp = "";
            $noUrut = 0;
            $valass = ["usd" => ["debit" => 0, "kredit" => 0], "euro" => ["debit" => 0, "kredit" => 0]];
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
                $saldo = 0;
                $debet = 0;
                $kredit = 0;
                if ($value->posisi === "D") {
                    $debet = $value->nominal;
                    $debets += $debet;
                } else {
                    $kredit = $value->nominal;
                    $kredits += $kredit;
                }
                $saldos += ($debet - $kredit) * $value->kurs;
                if ($valas) {
                    $sheet->setCellValue("A{$row}", $showUrut);
                    $sheet->setCellValue("B{$row}", $dt);
                    $sheet->setCellValue("C{$row}", $no_bukti);
                    $sheet->setCellValue("D{$row}", $value->uraian);
                    $sheet->setCellValue("E{$row}", $value->coa);
                    if (strtolower($value->nama_curr) === "usd") {
                        $valass["usd"]["debit"] += $debet;
                        $valass["usd"]["kredit"] += $kredit;
                        $sheet->setCellValue("F{$row}", $debet);
                        $sheet->setCellValue("G{$row}", $kredit);
                    } else {
                        $valass["euro"]["debit"] += $debet;
                        $valass["euro"]["kredit"] += $kredit;
                        $sheet->setCellValue("H{$row}", $debet);
                        $sheet->setCellValue("I{$row}", $kredit);
                    }
                    $sheet->setCellValue("J{$row}", $saldos);
                } else {
                    $sheet->setCellValue("A{$row}", $showUrut);
                    $sheet->setCellValue("B{$row}", $dt);
                    $sheet->setCellValue("C{$row}", $no_bukti);
                    $sheet->setCellValue("D{$row}", $value->uraian);
                    $sheet->setCellValue("E{$row}", $value->coa);
                    $sheet->setCellValue("F{$row}", $debet);
                    $sheet->setCellValue("G{$row}", $kredit);
                    $sheet->setCellValue("H{$row}", $saldos);
                }


                $temp = $value->no_bukti;
            }
            if (count($data) > 0) {
                $row += 1;
                $sheet->setCellValue("D{$row}", "Saldo Akhir");
                if ($valas) {
                    $sheet->setCellValue("F{$row}", $valass["usd"]["debit"]);
                    $sheet->setCellValue("G{$row}", $valass["usd"]["kredit"]);
                    $sheet->setCellValue("H{$row}", $valass["euro"]["debit"]);
                    $sheet->setCellValue("I{$row}", $valass["euro"]["kredit"]);
                    $sheet->setCellValue("J{$row}", $saldos);
                } else {
                    $sheet->setCellValue("F{$row}", $debets);
                    $sheet->setCellValue("G{$row}", $kredits);
                    $sheet->setCellValue("H{$row}", $saldos);
                }
            }

            $tanggal = $this->input->post("tanggal");
            $writer = new Xlsx($spreadsheet);
            $filename = "Buku Kas {$tanggal}";
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
