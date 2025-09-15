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

class Bukubank extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }

    public function index() {
        $data['id_dept'] = 'BACB';
        $model = new $this->m_global;
        $data["coa"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "bank"])->getData();
        $this->load->view('report/acc/v_buku_bank', $data);
    }

    protected function _query() {
        try {
            $this->form_validation->set_rules([
                [
                    'field' => 'tanggal',
                    'label' => 'Periode',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih'
                    ]
                ],
                [
                    'field' => 'kode_coa',
                    'label' => 'Bank',
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
            $model->setTables("acc_bank_masuk km")->setJoins("acc_bank_masuk_detail kmd", "bank_masuk_id = km.id")
                    ->setSelects(["km.no_bm as no_bukti,date(km.tanggal) as tanggal,'D' as posisi,nominal,kmd.kode_coa"])
                    ->setSelects(["if(kmd.uraian = '',transinfo,kmd.uraian) as uraian"])->setWheres(["status"=>"confirm"]);
            if (count($tanggals) > 1) {
                $model->setWheres(["date(km.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(km.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
            }
            if ($coa !== "") {
                $model->setWheres(["km.kode_coa" => $coa]);
            }
            $queryKasMasuk = $model->getQuery();

            $model->setTables("acc_bank_keluar kk")->setJoins("acc_bank_keluar_detail kkd", "bank_keluar_id = kk.id")
                    ->setSelects(["kk.no_bk as no_bukti,date(kk.tanggal) as tanggal,'K' as posisi,nominal,kkd.kode_coa"])
                    ->setSelects(["if(kkd.uraian = '',transinfo,kkd.uraian) as uraian"])->setWheres(["status"=>"confirm"]);
            if (count($tanggals) > 1) {
                $model->setWheres(["date(kk.tanggal) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(kk.tanggal) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
            }
            if ($coa !== "") {
                $model->setWheres(["kk.kode_coa" => $coa]);
            }
            $queryKasKeluar = $model->getQuery();

            $table = "({$queryKasMasuk} union all {$queryKasKeluar}) as kas";
            $model->setTables($table)->setJoins("acc_coa", "acc_coa.kode_coa = kas.kode_coa", "left")
                    ->setSelects(["no_bukti,tanggal,uraian,posisi,nominal,concat(kas.kode_coa,'-',acc_coa.nama) as coa"]);
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function search() {
        try {
            $model = $this->_query();
            $data["data"] = $model->getData();
            $html = $this->load->view('report/acc/v_buku_bank_detail', $data, true);
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

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $row = 1;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'Tanggal');
            $sheet->setCellValue("C{$row}", 'No Bukti');
            $sheet->setCellValue("D{$row}", 'Uraian');
            $sheet->setCellValue("E{$row}", 'No Acc');
            $sheet->setCellValue("F{$row}", 'Debit');
            $sheet->setCellValue("G{$row}", 'Kredit');
            $sheet->setCellValue("H{$row}", 'Saldo');

            if (count($data) > 0) {
                $row += 1;
                $sheet->setCellValue("D{$row}", "Saldo Awal");
                $sheet->setCellValue("F{$row}", 0);
                $sheet->setCellValue("G{$row}", 0);
                $sheet->setCellValue("H{$row}", 0);
            }

            $kredits = 0;
            $debets = 0;
            $saldos = 0;
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

                $sheet->setCellValue("A{$row}", $showUrut);
                $sheet->setCellValue("B{$row}", $dt);
                $sheet->setCellValue("C{$row}", $no_bukti);
                $sheet->setCellValue("D{$row}", $value->uraian);
                $sheet->setCellValue("E{$row}", $value->coa);
                $sheet->setCellValue("F{$row}", $debet);
                $sheet->setCellValue("G{$row}", $kredit);
                $sheet->setCellValue("H{$row}", $saldo);

                $temp = $value->no_bukti;
            }
            if (count($data) > 0) {
                $row += 1;
                $sheet->setCellValue("D{$row}", "Saldo Akhir");
                $sheet->setCellValue("F{$row}", $debets);
                $sheet->setCellValue("G{$row}", $kredits);
                $sheet->setCellValue("H{$row}", $saldos);
            }

            $tanggal = $this->input->post("tanggal");
            $writer = new Xlsx($spreadsheet);
            $filename = "Buku Bank {$tanggal}";
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
