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

class Kinerjamesin extends MY_Controller {

    //put your code here

    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
    }

    public function index($id_depth = "KNM", $depth = "WRD") {
        $model = new $this->m_global;
        $data['id_dept'] = $id_depth;
        $model->setTables("mesin")->setWheres(["dept_id" => $depth, 'devid_esp > ' => 0])->setSelects(["nama_mesin", "devid_esp"]);
        $data["mesin"] = $model->getData();
        $this->load->view('report/v_kinerja_mesin', $data);
    }

    public function get_grafiks() {
        try {

            $mesin = $this->input->post("mesin");
            $tanggal = $this->input->post("tanggal");
            $tanggals = explode(" - ", $tanggal);

            $model = new $this->m_global;
            $model->setTables("log_mesin")
//                ->setSelects(["DATE(DATE_SUB(timelog, INTERVAL 7 HOUR)) AS tanggal_kerja"])
                    ->setSelects([
                        'CASE 
        WHEN TIME(timelog) >= "07:00:00" AND TIME(timelog) < "15:00:00" THEN "pagi"
        WHEN TIME(timelog) >= "15:00:00" AND TIME(timelog) < "23:00:00" THEN "siang"
        ELSE "malam"
    END AS shift_range'
                    ])
                    ->setSelects([
                        "COUNT(*) AS total_log",
                        "COUNT(IF(state = '1', 1, NULL)) as running",
                        "COUNT(IF(state = '2', 1, NULL)) as noresp",
                        "COUNT(IF(state = '3', 1, NULL)) as benang",
                        "COUNT(IF(state = '4', 1, NULL)) as problem",
                        "COUNT(IF(state = '5', 1, NULL)) as noorder"
                    ])
                    ->setWheres([
                        "DATE(DATE_SUB(timelog, INTERVAL 7 HOUR)) >=" => $tanggals[0],
                        "DATE(DATE_SUB(timelog, INTERVAL 7 HOUR)) <=" => $tanggals[1]
                    ])
                    ->setGroups(["shift_range"]);
            if (!empty($mesin)) {
                $model->setWheres(["devid" => $mesin]);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $model->getData())));
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
            $drawing->setHeight(200); // Set your desired display height in pixels

            $drawing->setCoordinates('B2');
            $drawing->setWorksheet($sheet);

            $row = 13;
            $sheet->setCellValue("A{$row}", 'Shift Name');
            $sheet->setCellValue("B{$row}", 'Running (Hrs)');
            $sheet->setCellValue("C{$row}", 'No Response (Hrs)');
            $sheet->setCellValue("D{$row}", 'Ganti Benang (Hrs)');
            $sheet->setCellValue("E{$row}", 'Putus/Problem (Hrs)');
            $sheet->setCellValue("F{$row}", 'No Order (Hrs)');
            $sheet->setCellValue("G{$row}", 'Total Capacity');
            $sheet->setCellValue("H{$row}", 'Prod. Efficiency');

            foreach ($tbl as $key => $value) {
                $row++;
                $sheet->setCellValue("A{$row}", $key);
                $sheet->setCellValue("B{$row}", $this->con_min_days($value["running"]));
                $sheet->setCellValue("C{$row}", $this->con_min_days($value["noresp"]));
                $sheet->setCellValue("D{$row}", $this->con_min_days($value["benang"]));
                $sheet->setCellValue("E{$row}", $this->con_min_days($value["problem"]));
                $sheet->setCellValue("F{$row}", $this->con_min_days($value["noorder"]));
                $sheet->setCellValue("G{$row}", $value["total"]);
                $sheet->setCellValue("H{$row}", round($value["efficiency"], 2) . " %");
            }

            $filename = "report kinerja {$tanggal}";
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

    protected function con_min_days($mins) {

        $hours = str_pad(floor($mins / 60), 2, "0", STR_PAD_LEFT);
        $mins = str_pad($mins % 60, 2, "0", STR_PAD_LEFT);

        if ((int) $hours === 0) {
            return "{$mins} Min";
        }
        return "{$hours} Hours, {$mins} Min";
    }
}
