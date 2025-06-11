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
        $periodes = $this->input->post("periode");
        $jurnal = $this->input->post("jurnal");

        $periode = explode(" - ", $periodes);
        if (count($periode) < 2) {
            throw new \Exception("Tentukan dahulu periodenya", 500);
        }
        $tanggalAwal = date("Y-m-d H:i:s", strtotime($periode[0] . " 00:00:00"));
        $tanggalAkhir = date("Y-m-d H:i:s", strtotime($periode[1] . " 23:59:59"));

        $this->data->setTables("jurnal_entries je")
                ->setJoins("jurnal_entries_items jei", "je.kode = jei.kode")
                ->setJoins("mst_jurnal mj", "mj.kode = je.tipe", "left")
                ->setJoins("coa", "jei.kode_coa = coa.kode_coa", "left")
                ->setJoins("partner", "partner.id = jei.partner", "left")
                ->setOrder(["jei.posisi"=>"desc","jei.kode_coa"=>"asc"])
                ->setWheres(["tanggal_posting >=" => $tanggalAwal, "tanggal_posting <=" => $tanggalAkhir, "je.status" => "posted","je.tipe"=>$jurnal])
                ->setSelects(["mj.nama as nama_jurnal", "coa.nama as nama_coa", "je.periode,je.reff_note,je.tipe", "jei.*", "partner.nama as nama_partner",
                    "origin,tanggal_posting"]);
//        } catch (Exception $ex) {
//            
//        }
    }

    public function index() {
        $data['id_dept'] = 'ACCJM';
        $data['date'] = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d")))) . ' - ' . date('Y-m-d');
        $model = new $this->m_global;
        $data['jurnal'] = $model->setTables("mst_jurnal")->setOrder(["nama" => "asc"])->getData();
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
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih',
                    ]
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
            $sheet->setCellValue("A2", "Periode {$periodes}");
            $row = 4;
            
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'Tanggal Posting');
            $sheet->setCellValue("C{$row}", 'No Bukti');
            $sheet->setCellValue("D{$row}", 'Origin');
            $sheet->setCellValue("E{$row}", 'Kode ACC');
            $sheet->setCellValue("F{$row}", 'Nama ACC');
            $sheet->setCellValue("G{$row}", 'Keterangan');
            $sheet->setCellValue("H{$row}", 'Reff Note');
            $sheet->setCellValue("I{$row}", 'Partner');
            $sheet->setCellValue("J{$row}", 'Debet');
            $sheet->setCellValue("K{$row}", 'Kredit');

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
            $sheet->setCellValue("B{$row}", $value->tanggal_posting);
            $sheet->setCellValue("C{$row}", $value->kode);
            $sheet->setCellValue("D{$row}", $value->origin);
            $sheet->setCellValue("E{$row}", $value->kode_coa);
            $sheet->setCellValue("F{$row}", $value->nama_coa);
            $sheet->setCellValue("G{$row}", $value->nama);
            $sheet->setCellValue("H{$row}", $value->reff_note);
            $sheet->setCellValue("I{$row}", $value->nama_partner);
            $sheet->setCellValue("J{$row}", $debet);
            $sheet->setCellValue("K{$row}", $kredit);
            }
            
            if($no > 0) {
                $row += 2;
                $sheet->setCellValue("J{$row}", $debets);
                $sheet->setCellValue("K{$row}", $kredits);
            }
//            $periodes = $this->input->post("periode");
            
            if ($detail === "0") {
                  $reloadedSheet = $spreadsheet->getActiveSheet();
                  $reloadedSheet->removeColumnByIndex(2, 3);
                  $reloadedSheet->removeColumnByIndex(4, 3);
            }
            $writer = new Xlsx($spreadsheet);
            $filename = "jurnal_memorial_{$periodes}";
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
