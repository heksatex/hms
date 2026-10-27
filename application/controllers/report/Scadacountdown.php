<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Scadacountdown
 *
 * @author RONI
 */
require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Scadacountdown extends MY_Controller {

//put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module"); //load modul global
        $this->load->model("m_global");
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    protected $state = [
        '1' => [
            "status" => "Running",
            "warna" => "#198754"
        ],
        '2' => [
            "status" => "No Response",
            "warna" => "#dc3545"
        ],
        '3' => [
            "status" => "Ganti Lembar",
            "warna" => "#1B13F5"
        ],
        '4' => [
            "status" => "Putus / Problem",
            "warna" => "#B29E1E"
        ],
        '5' => [
            "status" => "Bongkar Pasang",
            "warna" => "#484858"
        ],
        '6' => [
            "status" => "Nyucuk",
            "warna" => "#B27272"
        ]
    ];

    public function index($dept = "TRI") {
        $ip = $_SERVER['REMOTE_ADDR']; // Mengambil IP pengunjung
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $data["ip_socket"] = "http://157.20.244.218:8889";
        } else {
            $data["ip_socket"] = "ws://10.10.0.17:8889";
        }
        $data["state"] = $this->state;
        $data["dept"] = $dept;
        $model = new $this->m_global;
        $data["departmen"] = $model->setTables("departemen")->setWheres(["kode" => $dept])->getDetail();
        $data["msn"] = $model->setTables("mesin")->setWheres(["status_aktif" => "t", "dept_id" => $dept, "devid_esp >" => 0])->setOrder(["row_order"])->getData();
        $this->load->view("report/v_scada_countdown_timer", $data);
    }

    protected function _listData() {
        try {
            $dept = $this->input->get("dept");
            $model = new $this->m_global;
            $model->setTables("mesin mst")->setWheres(["status_aktif" => "t", "dept_id" => $dept, "estimasi_menit_habis > " => 0])
                    ->setJoins("(select mrp.kode,mrp.mc_id, start_time, estimasi_menit_habis,GROUP_CONCAT(mrprt.nama_produk) as nama_produk,
GROUP_CONCAT(mrprt.reff_note) as reff_note,estimasi_tgl_habis
from mrp_production_rm_target mrprt
join mrp_production mrp on (mrp.kode = mrprt.kode and mrp.status in ('ready','draft'))
where dept_id = '{$dept}' 
GROUP BY mrprt.kode order by start_time desc) mrp", "(mst.mc_id = mrp.mc_id and mst.dept_id = '{$dept}')", "LEFT")
                    ->setOrder(["start_time" => "asc"])
                    ->setSelects(["mst.mc_id", "mst.no_mesin", "kode", "nama_produk", "reff_note", "start_time", "estimasi_menit_habis", "mst.devid_esp", "estimasi_tgl_habis", "nama_mesin"]);
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function list_data() {
        try {
            $model = $this->_listData();
//        ->setGroups(["mrp.mc_id"])

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('data' => $model->getData())));
        } catch (Exception $ex) {
            
        }
    }

    public function download() {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $row = 1;
            $model = $this->_listData();

            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'Sisa Waktu');
            $sheet->setCellValue("C{$row}", 'Mesin Hilir');
            $sheet->setCellValue("D{$row}", 'MO Hilir');
            $sheet->setCellValue("E{$row}", 'Spesifikasi BD');
            $sheet->setCellValue("F{$row}", 'Reff');
            $sheet->setCellValue("G{$row}", 'Urgensi');

            $data = [];
            $now = new DateTime(date("Y-m-d H:i:s"));

            foreach ($model->getData() as $key => $value) {
                if (empty($value->nama_produk))
                    continue;
                $targetTime = date("Y-m-d H:i:s", strtotime($value->estimasi_tgl_habis));
                $ttm = new DateTime($targetTime);
                $data[] = [
                    "id" => $value->kode,
                    "mo" => $value->kode,
                    "bd" => explode(",", $value->nama_produk),
                    "reff" => explode(",", $value->reff_note),
                    "nama_mesin" => $value->nama_mesin,
                    "sisa" => $now->getTimestamp() - $ttm->getTimestamp()
                ];
            }

            usort($data, function ($a, $b) {
                return $a["sisa"] <=> $b["sisa"];
            });

            foreach ($data as $k => $val) {
                $row++;
                $sheet->setCellValue("A{$row}", ($k + 1));
                $sheet->setCellValue("B{$row}", $this->_convertMinute($val["sisa"]));
                $sheet->setCellValue("C{$row}", $val["nama_mesin"]);
                $sheet->setCellValue("D{$row}", $val["mo"]);
                $sheet->setCellValue("E{$row}", implode("\n", $val["bd"]));
                $sheet->setCellValue("F{$row}", implode("\n", $val["reff"]));
                $sheet->setCellValue("G{$row}", $this->_statusBeam($val["sisa"]));
            }

            $filename = "Scada Count Down ". $now->format("Y-m-d H i");
            $url = "dist/storages/export/scada";
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

    protected function _statusBeam($second) {
        $status = "AMAN";
        switch ($second) {
            case $second < 0:
                $status = "BEAM HABIS";
                break;
            case $second < 10800:
                $status = "WARNING";
                break;
            case $second <= 21600:
                $status = "SEGERA SIAPKAN";
                break;
        }
        return $status;
    }

    protected function _convertMinute($second) {
        // 1. Cek apakah nilai negatif
        $isNegative = $second < 0;

        // 2. Ambil nilai absolut (positif) untuk perhitungan
        $value = abs($second);

        $units = [
            "hour" => 60 * 60, // 3600 detik
            "min" => 60, // 60 detik
            "sec" => 1             // 1 detik
        ];
        $result = [];

        foreach ($units as $name => $div) {
            $p = floor($value / $div);

            if ($p == 1) {
                $result[] = $p . " " . $name;
            } elseif ($p >= 2) {
                $result[] = $p . " " . $name . "s";
            }

            $value %= $div;
        }

        // Jika nilai 0 detik
        if (empty($result)) {
            $result[] = "0 secs";
        }

        // 3. Jika aslinya negatif, tambahkan tanda minus di elemen pertama array
        if ($isNegative && !empty($result)) {
            $result[0] = "-" . $result[0];
        }

        return implode(" ", $result);
    }
}
