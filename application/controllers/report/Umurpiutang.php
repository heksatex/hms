<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require FCPATH . 'vendor/autoload.php';
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
use Mpdf\Mpdf;

class Umurpiutang extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }

    public function index() {
        $id_dept = 'RUP';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/acc/v_umur_piutang', $data);
    }

    protected function _month() {
        // Nama-nama bulan
        $bulanNames = [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember"
        ];

        $bulanSekarang = date('n'); // 1-12
        $tahunSekarang = date('Y');

        // Buat array nama bulan + tahun mundur 4 bulan ke belakang
        $bulanLabels = [];
        for ($i = 0; $i < 4; $i++) {
            $bulanIndex = $bulanSekarang - $i;
            $tahun = $tahunSekarang;

            // Kalau mundur ke tahun sebelumnya
            if ($bulanIndex <= 0) {
                $bulanIndex += 12;
                $tahun -= 1;
            }

            $bulanLabels[] = $bulanNames[$bulanIndex - 1] . " " . $tahun;
        }

        // Label terakhir untuk “lebih dari 3 bulan”
        $bulanIndexLebih3 = $bulanSekarang - 3;
        $tahunLebih3 = $tahunSekarang;
        if ($bulanIndexLebih3 <= 0) {
            $bulanIndexLebih3 += 12;
            $tahunLebih3 -= 1;
        }
        $bulanLabels[] = "> " . $bulanNames[$bulanIndexLebih3 - 1] . " " . $tahunLebih3;

        return $bulanLabels;
    }

    protected function _query() {
        try {
            $customer = $this->input->post("customer");
            $model = new $this->m_global;
            $model->setTables("acc_faktur_penjualan fp")->setWheres(["fp.status" => "confirm", "fp.lunas" => 0])
                    ->setOrder(["partner_nama"=>"asc"])
                    ->setSelects(["partner_id, partner_nama,SUM(piutang_rp) AS total_piutang"])
                    ->setSelects(["SUM(
                            CASE 
                                WHEN DATE_FORMAT(tanggal, '%Y-%m') = DATE_FORMAT(CURDATE(), '%Y-%m')
                                THEN piutang_rp ELSE 0 
                            END
                        ) AS piutang_bulan_ini"])
                    ->setSelects(["SUM(
                            CASE 
                                WHEN DATE_FORMAT(tanggal, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 1 MONTH), '%Y-%m')
                                THEN piutang_rp ELSE 0 
                            END
                        ) AS piutang_bulan_1"])
                    ->setSelects(["SUM(
                            CASE 
                                WHEN DATE_FORMAT(tanggal, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), '%Y-%m')
                                THEN piutang_rp ELSE 0 
                            END
                        ) AS piutang_bulan_2"])
                    ->setSelects(["SUM(
                            CASE 
                                WHEN DATE_FORMAT(tanggal, '%Y-%m') = DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), '%Y-%m')
                                THEN piutang_rp ELSE 0 
                            END
                        ) AS piutang_bulan_3"])
                    ->setSelects(["SUM(
                            CASE 
                                WHEN tanggal < DATE_FORMAT(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), '%Y-%m-01')
                                THEN piutang_rp ELSE 0 
                            END
                        ) AS piutang_lebih_dari_3_bulan"]);
            if (!empty($customer)) {
                $model->setWheres(["partner_id" => $customer]);
            }
            return $model->setGroups(["partner_id"]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function generate() {
        try {
            $data["head"] = $this->_month();
            $data["body"] = $this->_query()->getData();
            $html = $this->load->view('report/acc/v_umur_piutang_detail', $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function pdf() {
        try {
            $data["head"] = $this->_month();
            $data["body"] = $this->_query()->getData();
            $data["customer"] = $this->input->post("customer");
            $html = $this->load->view('report/acc/v_umur_piutang_pdf', $data, true);
            $url = "dist/storages/print/umurpiutang";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $tanggal = date("Y-m-d");
            $mpdf = new Mpdf(['tempDir' => FCPATH . 'tmp']);
            $mpdf->WriteHTML($html);
            $filename = $tanggal;
            $pathFile = "{$url}/umur piutang pertanggal {$filename}.pdf";
            $mpdf->Output(FCPATH . $pathFile, "F");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("url" => base_url($pathFile))));
        } catch (Exception $ex) {
            log_message("error", json_encode($ex));
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function export() {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $row = 1;
            $column = ["D", "E", "F", "G", "H"];

            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'Customer');
            $sheet->setCellValue("C{$row}", 'Total Piutang');
            $month = $this->_month();
            foreach ($month as $key => $value) {
                $sheet->setCellValue("{$column[$key]}{$row}", $value);
            }
            $data = $this->_query()->getData();

            $no = 0;
            $totalPiutang = 0;
            $piutang_bulan_ini = 0;
            $piutang_bulan_1 = 0;
            $piutang_bulan_2 = 0;
            $piutang_bulan_3 = 0;
            $piutang_lebih_dari_3_bulan = 0;

            foreach ($data as $key => $value) {
                $row += 1;
                $no++;
                $totalPiutang += $value->total_piutang;
                $piutang_bulan_ini += $value->piutang_bulan_ini;
                $piutang_bulan_1 += $value->piutang_bulan_1;
                $piutang_bulan_2 += $value->piutang_bulan_2;
                $piutang_bulan_3 += $value->piutang_bulan_3;
                $piutang_lebih_dari_3_bulan += $value->piutang_lebih_dari_3_bulan;
                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $value->partner_nama);
                $sheet->setCellValue("C{$row}", $value->total_piutang);
                $sheet->setCellValue("D{$row}", $value->piutang_bulan_ini);
                $sheet->setCellValue("E{$row}", $value->piutang_bulan_1);
                $sheet->setCellValue("F{$row}", $value->piutang_bulan_2);
                $sheet->setCellValue("G{$row}", $value->piutang_bulan_3);
                $sheet->setCellValue("H{$row}", $value->piutang_lebih_dari_3_bulan);
            }
            if ($no > 0) {
                $row += 2;
                $sheet->setCellValue("A{$row}", "Total");
                $sheet->mergeCells("A{$row}:B{$row}");
                $sheet->setCellValue("C{$row}", $totalPiutang);
                $sheet->setCellValue("D{$row}", $piutang_bulan_ini);
                $sheet->setCellValue("E{$row}", $piutang_bulan_1);
                $sheet->setCellValue("F{$row}", $piutang_bulan_2);
                $sheet->setCellValue("G{$row}", $piutang_bulan_3);
                $sheet->setCellValue("H{$row}", $piutang_lebih_dari_3_bulan);

                $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal('right');
                $sheet->getStyle("A{$row}:H{$row}")->getFont()->setBold(true);
            }
            $tanggal = date("Y-m-d");
            $writer = new Xlsx($spreadsheet);
            $filename = "Umur Piutang Pertanggal {$tanggal}";
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
