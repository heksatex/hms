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
                    ->setOrder(["fp.tanggal" => "asc", "no_faktur_internal" => "asc"])
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
            return $model;
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
}
