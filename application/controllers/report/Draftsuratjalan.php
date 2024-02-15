<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Settings;

class Draftsuratjalan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_Picklist");
        $this->load->model("m_PicklistDetail");
    }

    public function index() {
        $data['id_dept'] = 'DSJ';
        $this->load->view('report/v_draft_surat_jalan', $data);
    }

    public function test() {

        $style = [
            'font' => [
                'bold' => true
            ]
        ];
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'No SJ');
        $sheet->setCellValue('B1', 'MAKLOON/HI/24/II/0001');
        $sheet->setCellValue('C1', 'Customer    ');
        $sheet->setCellValue('D1', 'KORIN JUNG WOO (MAKLOON)');

        $sheet->setCellValue('A2', "Pack");
        $sheet->setCellValue('B2', "PL24020002");
        $sheet->setCellValue('C2', "Alamat");
        $sheet->setCellValue('D2', "Jl. Raya Bandung Garut Km 25 Kebon Suuk KP. 02/09");

        $sheet->setCellValue('A3', "Tanggal");
        $sheet->setCellValue('B3', "2/13/2024 8:54");

        $sheet->setCellValue('A5', 'No Urut');
        $sheet->setCellValue('B5', 'CTH No');
        $sheet->setCellValue('C5', 'Design No.');
        $sheet->setCellValue('D5', 'Color');
        $sheet->setCellValue('E5', 'F1');
        $sheet->setCellValue('F5', 'F2');
        $sheet->setCellValue('G5', 'F3');
        $sheet->setCellValue('H5', 'F4');
        $sheet->setCellValue('I5', 'F5');
        $sheet->setCellValue('J5', 'F6');
        $sheet->setCellValue('K5', 'F7');
        $sheet->setCellValue('L5', 'F8');
        $sheet->setCellValue('M5', 'F9');
        $sheet->setCellValue('N5', 'F10');

        $spreadsheet->getActiveSheet()->getStyle("B1")->applyFromArray($style);
        $spreadsheet->getActiveSheet()->getStyle("B2")->applyFromArray($style);
        $spreadsheet->getActiveSheet()->getStyle("B3")->applyFromArray($style);
        
        $spreadsheet->getActiveSheet()->getStyle("D1")->applyFromArray($style);
        $spreadsheet->getActiveSheet()->getStyle("D2")->applyFromArray($style);
        $writer = new Xlsx($spreadsheet);
        $filename = 'excel-report';

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    }

    public function checking() {
        try {
            $nopl = $this->input->post("no_pl");
            $pkl = $this->m_Picklist->draftSuratJalan(['picklist.no' => $nopl]);
            if (empty($pkl)) {
                throw new Exception("No Picklist Tidak ditemukan", 500);
            }
            if (!in_array($pkl->status, ['done', 'validasi'])) {
                throw new Exception("No Picklist Dalam Status " . $pkl->status, 500);
            }
            $data['picklist'] = $pkl;

            $datas['picklist'] = $pkl;
            $datas['picklist_detail'] = $this->m_PicklistDetail->detailDraftReport(
                    ['picklist_detail.no_pl' => $nopl, 'picklist_detail.valid !=' => 'cancel'],
                    $nopl, ['warna_remark', 'corak_remark', 'uom'], ["BULK"]
            );
            $data['detail'] = $this->load->view('report/v_draft_surat_jalan_detail', $datas, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }
}
