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
use PhpOffice\PhpSpreadsheet\Settings;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Jurnalmemorial extends MY_Controller {

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
//        try {
        $periode = $this->input->post("periode");
        $jurnal = $this->input->post("jurnal");
        $filter = $this->input->post("filter");
        $tanggal_postings = $this->input->post("tanggal_posting");
        if ($filter === "1") {
            $tanggal_posting = explode(" - ", $tanggal_postings);
            if (count($tanggal_posting) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($tanggal_posting[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($tanggal_posting[1] . " 23:59:59"));
            $where = ["tanggal_posting >=" => $tanggalAwal, "tanggal_posting <=" => $tanggalAkhir];
        } else {
            $where = ["je.periode" => $periode];
        }

        $this->data->setTables("acc_jurnal_entries je")
                ->setJoins("acc_jurnal_entries_items jei", "je.kode = jei.kode")
                ->setJoins("mst_jurnal mj", "mj.kode = je.tipe", "left")
                ->setJoins("acc_coa", "jei.kode_coa = acc_coa.kode_coa", "left")
                ->setJoins("partner", "partner.id = jei.partner", "left")
                ->setOrder(["jei.posisi" => "desc", "jei.kode_coa" => "asc"])
                ->setWheres(array_merge(["je.status" => "posted", "je.tipe" => $jurnal], $where))
                ->setSelects(["mj.nama as nama_jurnal", "acc_coa.nama as nama_coa", "je.periode,je.reff_note,je.tipe", "jei.*", "partner.nama as nama_partner",
                    "origin,tanggal_posting"]);
//        } catch (Exception $ex) {
//            
//        }
    }

    public function check_berdasarkan($str, $periode): bool {
        if (empty($str) && empty($this->input->post($periode))) {
            $this->form_validation->set_message('check_berdasarkan', 'Pilih Salah satu Tanggal Posting / Periode');
            return false;
        }
        return true;
    }

    public function index() {
        $data['id_dept'] = 'ACCJM';
        $data['date'] = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d")))) . ' - ' . date('Y-m-d');
        $model = new $this->m_global;
        $model2 = clone $model;
        $data['jurnal'] = $model->setTables("mst_jurnal")->setOrder(["nama" => "asc"])->getData();
        $data["periode"] = $model2->setTables("acc_periode")->setOrder(["tahun_fiskal" => "desc", "periode" => "asc"])->getData();
        $this->load->view('accounting/v_rpt_jurnal_memorial', $data);
    }

    public function search() {
        $validation = [
            [
                'field' => 'jurnal',
                'label' => 'Jurnal',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                ]
            ],
            [
                'field' => 'periode',
                'label' => 'periode',
                'rules' => ['callback_check_berdasarkan[tanggal_posting]'],
            ]
        ];
        try {
            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $detail = $this->input->post("detail");
            $this->getData();
            if ($detail === "0") {
                $this->data->setGroups(["jei.kode_coa", "jei.posisi"])->setSelects(["sum(jei.nominal) as nominal"]);
            }
            $data["data"] = $this->data->getData();
            $html = $this->load->view('accounting/v_rpt_jurnal_memorial_detail', $data, true);
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
            $validation = [
                [
                    'field' => 'jurnal',
                    'label' => 'Jurnal',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih',
                    ]
                ],
                [
                    'field' => 'periode',
                    'label' => 'periode',
                    'rules' => ['callback_check_berdasarkan[tanggal_posting]'],
                ]
            ];

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $detail = $this->input->post("detail") ?? "0";
            $jurnal_nm = $this->input->post("jurnal_nm");
            $periodes = $this->input->post("periode");
            $tanggal = $this->input->post("tanggal_posting");
            $filter = $this->input->post("filter");

            $this->getData();
            if ($detail === "0") {
                $this->data->setGroups(["jei.kode_coa", "jei.posisi"])->setSelects(["sum(jei.nominal) as nominal"]);
//                  $reloadedSheet = $spreadsheet->getActiveSheet();
//                  $reloadedSheet->removeColumnByIndex(4, 7);
//                $spreadsheet->getActiveSheet()->removeColumn('D');
//                $spreadsheet->getActiveSheet()->removeColumn('E');
//                $spreadsheet->getActiveSheet()->removeColumn('F');
//                $spreadsheet->getActiveSheet()->removeColumn('G');
            }
            $data = $this->data->getData();
            $sheet->setCellValue("A1", "Jurnal Memorial {$jurnal_nm}");
            if ($filter === "1") {
                $sheet->setCellValue("A2", "Tanggal Posting {$tanggal}");
            }
            else {
                $sheet->setCellValue("A2", "Periode {$periodes}");
            }
            $row = 4;

            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'Periode');
            $sheet->setCellValue("C{$row}", 'Tanggal Posting');
            $sheet->setCellValue("D{$row}", 'No Bukti');
            $sheet->setCellValue("E{$row}", 'Origin');
            $sheet->setCellValue("F{$row}", 'Kode ACC');
            $sheet->setCellValue("G{$row}", 'Nama ACC');
            $sheet->setCellValue("H{$row}", 'Keterangan');
            $sheet->setCellValue("I{$row}", 'Reff Note');
            $sheet->setCellValue("J{$row}", 'Partner');
            $sheet->setCellValue("K{$row}", 'Debit');
            $sheet->setCellValue("L{$row}", 'Credit');

            $no = 0;
            $kredits = 0;
            $debets = 0;
            foreach ($data as $key => $value) {
                $no++;
                $row++;
                $debet = 0;
                $kredit = 0;
                if ($value->posisi === "D") {
                    $debet = $value->nominal;
                    $debets += $debet;
                } else {
                    $kredit = $value->nominal;
                    $kredits += $kredit;
                }

                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $value->periode);
                $sheet->setCellValue("C{$row}", $value->tanggal_posting);
                $sheet->setCellValue("D{$row}", $value->kode);
                $sheet->setCellValue("E{$row}", $value->origin);
                $sheet->setCellValue("F{$row}", $value->kode_coa);
                $sheet->setCellValue("G{$row}", $value->nama_coa);
                $sheet->setCellValue("H{$row}", $value->nama);
                $sheet->setCellValue("I{$row}", $value->reff_note);
                $sheet->setCellValue("J{$row}", $value->nama_partner);
                $sheet->setCellValue("K{$row}", $debet);
                $sheet->setCellValue("L{$row}", $kredit);
            }

            if ($no > 0) {
                $row += 2;
                $sheet->setCellValue("K{$row}", $debets);
                $sheet->setCellValue("L{$row}", $kredits);
            }
//            $periodes = $this->input->post("periode");

            if ($detail === "0") {
                $reloadedSheet = $spreadsheet->getActiveSheet();
                $reloadedSheet->removeColumnByIndex(3, 3);
                $reloadedSheet->removeColumnByIndex(4, 3);
            }
            $nm = $periodes ?? $tanggal;
            $nm = str_replace("/", "_", $nm);
            $writer = new Xlsx($spreadsheet);
            $filename = "jurnal_memorial_{$nm}";
            $url = "dist/storages/report/jurnal_memorial";
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
