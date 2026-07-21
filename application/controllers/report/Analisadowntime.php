<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Kinerjamesin
 *
 * @author RONI
 */
require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;

class Analisadowntime extends MY_Controller {

    //put your code here
    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
    }

    public function index($depth = "WRD") {
        $model = new $this->m_global;
        $data['id_dept'] = 'ADM';
        $model->setTables("mesin")->setWheres(["dept_id" => $depth, 'devid_esp > ' => 0])->setSelects(["nama_mesin", "devid_esp"]);
        $data["mesin"] = $model->getData();
        $this->load->view('report/v_analisa_downtime', $data);
    }

    public function get_grafiks() {
        try {
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);
            $mesin = $this->input->post("mesin");

            $model = new $this->m_global;

            $model->setTables("log_mesin")
                    ->setSelects([
                        "COUNT(DISTINCT devid) as count_mesin",
                        "DATE_FORMAT(timelog, '%e %M') as dt",
                        "COUNT(*) AS total_log",
                        "COUNT(IF(state = '1', 1, NULL)) as running",
                        "COUNT(IF(state = '2', 1, NULL)) as noresp",
                        "COUNT(IF(state = '3', 1, NULL)) as benang",
                        "COUNT(IF(state = '4', 1, NULL)) as problem",
                        "COUNT(IF(state = '5', 1, NULL)) as noorder"
                    ])->setWheres([
                        "DATE(timelog) >=" => $tanggals[0],
                        "DATE(timelog) <=" => $tanggals[1]
                    ])
                    ->setGroups(["dt"])->setOrder(["date(timelog)"]);
            if (!empty($mesin)) {
                $model->setWheres(["devid" => $mesin]);
            }
            $date1 = date_create($tanggals[0]);
            $date2 = date_create($tanggals[1]);
            $interval = date_diff($date1, $date2);

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $model->getData(), 'day' => $interval->days)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function export() {
        try {
            $tanggal = $this->input->post("tanggal");
            $imageData = $this->input->post("img");
            $tbl = $this->input->post("tbl");

            if (preg_match('/^data:image\/(\w+);base64,/', $imageData)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
            }

            $imageData = base64_decode($imageData);
            if ($imageData === false) {
                throw new Exception('Invalid Base64 string provided.');
            }

            $imageResource = imagecreatefromstring($imageData);
            if (!$imageResource) {
                throw new Exception('Failed to create image resource from data.');
            }

            // 5. Initialize the Spreadsheet object
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

// 6. Instantiate and configure the MemoryDrawing object
            $drawing = new MemoryDrawing();
            $drawing->setImageResource($imageResource);
            $drawing->setRenderingFunction(MemoryDrawing::RENDERING_PNG);
            $drawing->setMimeType(MemoryDrawing::MIMETYPE_DEFAULT);
            $drawing->setHeight(400); // Set your desired display height in pixels

            $drawing->setCoordinates('J2');
            $drawing->setWorksheet($sheet);

            $row = 2;
            $sheet->setCellValue("A{$row}", 'tanggal');
            $sheet->setCellValue("B{$row}", 'Running (Hrs)');
            $sheet->setCellValue("C{$row}", 'No Response (Hrs)');
            $sheet->setCellValue("D{$row}", 'Ganti Benang (Hrs)');
            $sheet->setCellValue("E{$row}", 'Putus/Problem (Hrs)');
            $sheet->setCellValue("F{$row}", 'No Order (Hrs)');
            $sheet->setCellValue("G{$row}", 'Total Capacity');
            $trun = 0;
            $noTresp = 0;
            $Tbenang = 0;
            $Tproblem = 0;
            $Toff = 0;
            $Tcps = 0;
            foreach ($tbl["tgl"] as $key => $value) {
                $row++;
                $run = $tbl["run"][$key];
                $noresp = $tbl["noResp"][$key];
                $benang = $tbl["benang"][$key];
                $problem = $tbl["problem"][$key];
                $off = $tbl["off"][$key];
                $cps = $tbl["cps"][$key];
                $trun += $run;
                $noTresp += $noresp;
                $Tbenang += $benang;
                $Tproblem += $problem;
                $Toff += $off;
                $Tcps += $cps;
                $sheet->setCellValue("A{$row}", $value);
                $sheet->setCellValue("B{$row}", $run);
                $sheet->setCellValue("c{$row}", $noresp);
                $sheet->setCellValue("d{$row}", $benang);
                $sheet->setCellValue("e{$row}", $problem);
                $sheet->setCellValue("f{$row}", $off);
                $sheet->setCellValue("g{$row}", $cps);
            }
            $row++;
            $sheet->setCellValue("A{$row}", "TOTAL MTD");
            $sheet->setCellValue("B{$row}", $trun);
            $sheet->setCellValue("c{$row}", $noTresp);
            $sheet->setCellValue("d{$row}", $Tbenang);
            $sheet->setCellValue("e{$row}", $Tproblem);
            $sheet->setCellValue("f{$row}", $Toff);
            $sheet->setCellValue("g{$row}", $Tcps);

            $filename = "report analisadowntime {$tanggal}";
            $url = "dist/storages/report/kinerjamesin";
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
            
        }
    }
}
