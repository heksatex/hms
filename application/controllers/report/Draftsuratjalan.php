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
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;

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

    public function export() {
        try {

            $style = [
                'font' => [
                    'bold' => true
                ]
            ];

            $nopl = $this->input->post("no_pl");
            $pkl = $this->m_Picklist->draftSuratJalan(['picklist.no' => $nopl]);
            $picklist = $pkl;
            if (empty($pkl)) {
                throw new Exception("No Picklist Tidak ditemukan", 500);
            }
            if (!in_array($pkl->status, ['done', 'validasi'])) {
                throw new Exception("No Picklist Dalam Status " . $pkl->status, 500);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'No SJ');
            $sheet->setCellValue('B1', $pkl->no_sj ?? "");
            $sheet->setCellValue('C1', 'Customer    ');
            $sheet->setCellValue('D1', $pkl->nama ?? "");

            $sheet->setCellValue('A2', "Pack");
            $sheet->setCellValue('B2', $pkl->no ?? "");
            $sheet->setCellValue('C2', "Alamat");
            $sheet->setCellValue('D2', $pkl->alamat_kirim ?? $pkl->alamat);

            $sheet->setCellValue('A3', "Tanggal");
            $sheet->setCellValue('B3', $pkl->tanggal_input ?? "");

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
            $sheet->setCellValue('O5', 'Total PCS');
            $sheet->setCellValue('P5', 'Total Qty');
            $sheet->setCellValue('Q5', 'UOM');
            if ($picklist->type_bulk_id === "1") {
                $sheet->setCellValue('R5', 'N.W[KGS]');
                $sheet->setCellValue('S5', 'G.W[KGS]');
            }
            $no = 0;
            $jml_qty = 0;
            $total_qty = 0;
            $id = null;
            $satuan = '';
            $nourut = 0;
            $total_net = 0;
            $total_groos = 0;
            $tempBulk = null;
            $join = [];
            $where = [];
            if ($pkl->type_bulk_id === "1") {
                $picklist_detail = $this->m_PicklistDetail->detailDraftReport(
                        ['picklist_detail.no_pl' => $nopl, 'picklist_detail.valid !=' => 'cancel'],
                        $nopl, ["bbd.no_bulk", 'warna_remark', 'corak_remark', 'uom', 'lebar_jadi', 'uom_lebar_jadi'], ["BULK"]
                );
            } else {
                $picklist_detail = $this->m_PicklistDetail->detailDraftReport(
                        ['picklist_detail.no_pl' => $nopl, 'picklist_detail.valid !=' => 'cancel'],
                        $nopl, ['warna_remark', 'corak_remark', 'uom', 'lebar_jadi', 'uom_lebar_jadi'], []
                );
            }

            $rowStartData = 5;
            foreach ($picklist_detail as $key => $value) {
                $no++;
                $where = [];
                $jml_qty += $value->jml_qty;
                $total_qty += $value->total_qty;
                if ($picklist->type_bulk_id === "1") {
                    $where = array_merge($where, ["bd.bulk_no_bulk" => $value->no_bulk]);
                    $join = ["BULK"];
                }
                $wheres = array_merge($where, ['valid !=' => 'cancel', 'corak_remark' => $value->corak_remark, 'warna_remark' => $value->warna_remark,
                    'uom' => $value->uom, 'no_pl' => $value->no_pl, 'uom_lebar_jadi' => $value->uom_lebar_jadi, 'lebar_jadi' => $value->lebar_jadi]);

                $detailQty = $this->m_PicklistDetail->detailReportQty($wheres, "qty,uom", $join);
                $perpage = 10;
                $totalData = count($detailQty);
                $totalPage = ceil($totalData / $perpage);
                if ($picklist->type_bulk_id === "1") {

                    for ($nn = 0; $nn < $totalPage; $nn++) {
                        $page = $nn * $perpage;
                        $satuan = $detailQty[0]->uom;
                        $tempID = $value->no_bulk . $value->warna_remark . $value->corak_remark . $value->uom . $value->uom_lebar_jadi . $value->lebar_jadi;
                        $showNoUrut = "";
                        $showNet = "";
                        $showGross = "";
                        if ($tempBulk !== $value->no_bulk) {
                            $nourut++;
                            $total_net += $value->net_weight;
                            $total_groos += $value->gross_weight;

                            $showGross = $value->gross_weight;
                            $showNet = $value->net_weight;
                            $showNoUrut = $nourut;
                        }

                        $sheet->setCellValue("A" . $rowStartData, $showNoUrut);
                        $sheet->setCellValue('B' . $rowStartData, ($tempBulk === $value->no_bulk) ? '' : $value->no_bulk);
                        $sheet->setCellValue("C" . $rowStartData, ($id === $tempID) ? '' : str_replace('|', ' ', $value->corak_remark . ' ' . $value->lebar_jadi . ' ' . $value->uom_lebar_jadi));
                        $sheet->setCellValue("D" . $rowStartData, ($id === $tempID) ? '' : str_replace('|', ' ', $value->warna_remark));
                        $sheet->setCellValue("E" . $rowStartData, isset($detailQty[$page + 0]) ? (float) $detailQty[$page + 0]->qty : "");
                        $sheet->setCellValue("F" . $rowStartData, isset($detailQty[$page + 1]) ? (float) $detailQty[$page + 1]->qty : "");
                        $sheet->setCellValue("G" . $rowStartData, isset($detailQty[$page + 2]) ? (float) $detailQty[$page + 2]->qty : "");
                        $sheet->setCellValue("H" . $rowStartData, isset($detailQty[$page + 3]) ? (float) $detailQty[$page + 3]->qty : "");
                        $sheet->setCellValue("I" . $rowStartData, isset($detailQty[$page + 4]) ? (float) $detailQty[$page + 4]->qty : "");
                        $sheet->setCellValue("J" . $rowStartData, isset($detailQty[$page + 5]) ? (float) $detailQty[$page + 5]->qty : "");
                        $sheet->setCellValue("K" . $rowStartData, isset($detailQty[$page + 6]) ? (float) $detailQty[$page + 6]->qty : "");
                        $sheet->setCellValue("L" . $rowStartData, isset($detailQty[$page + 7]) ? (float) $detailQty[$page + 7]->qty : "");
                        $sheet->setCellValue("M" . $rowStartData, isset($detailQty[$page + 8]) ? (float) $detailQty[$page + 8]->qty : "");
                        $sheet->setCellValue("N" . $rowStartData, isset($detailQty[$page + 9]) ? (float) $detailQty[$page + 9]->qty : "");

                        $sheet->setCellValue('O' . $rowStartData, ($id === $tempID) ? '' : $value->jml_qty);
                        $sheet->setCellValue('P' . $rowStartData, ($id === $tempID) ? '' : $value->total_qty);
                        $sheet->setCellValue('Q' . $rowStartData, ($id === $tempID) ? '' : $satuan);
                        $sheet->setCellValue('R' . $rowStartData, $showNet);
                        $sheet->setCellValue('S' . $rowStartData, $showGross);

                        $id = $tempID;
                        $tempBulk = $value->no_bulk;
                        $rowStartData++;
                    }
                } else {
                    for ($nn = 0; $nn < $totalPage; $nn++) {
                        $page = $nn * $perpage;
                        $satuan = $detailQty[0]->uom;
                        $tempID = $value->warna_remark . $value->corak_remark . $value->uom . $value->uom_lebar_jadi . $value->lebar_jadi;

                        $sheet->setCellValue("A" . $rowStartData, ($id === $tempID) ? '' : $no);
                        $sheet->setCellValue('B' . $rowStartData, "");
                        $sheet->setCellValue("C" . $rowStartData, ($id === $tempID) ? '' : str_replace('|', ' ', $value->corak_remark . ' ' . $value->lebar_jadi . ' ' . $value->uom_lebar_jadi));
                        $sheet->setCellValue("D" . $rowStartData, ($id === $tempID) ? '' : str_replace('|', ' ', $value->warna_remark));
                        $sheet->setCellValue("E" . $rowStartData, isset($detailQty[$page + 0]) ? (float) $detailQty[$page + 0]->qty : "");
                        $sheet->setCellValue("F" . $rowStartData, isset($detailQty[$page + 1]) ? (float) $detailQty[$page + 1]->qty : "");
                        $sheet->setCellValue("G" . $rowStartData, isset($detailQty[$page + 2]) ? (float) $detailQty[$page + 2]->qty : "");
                        $sheet->setCellValue("H" . $rowStartData, isset($detailQty[$page + 3]) ? (float) $detailQty[$page + 3]->qty : "");
                        $sheet->setCellValue("I" . $rowStartData, isset($detailQty[$page + 4]) ? (float) $detailQty[$page + 4]->qty : "");
                        $sheet->setCellValue("J" . $rowStartData, isset($detailQty[$page + 5]) ? (float) $detailQty[$page + 5]->qty : "");
                        $sheet->setCellValue("K" . $rowStartData, isset($detailQty[$page + 6]) ? (float) $detailQty[$page + 6]->qty : "");
                        $sheet->setCellValue("L" . $rowStartData, isset($detailQty[$page + 7]) ? (float) $detailQty[$page + 7]->qty : "");
                        $sheet->setCellValue("M" . $rowStartData, isset($detailQty[$page + 8]) ? (float) $detailQty[$page + 8]->qty : "");
                        $sheet->setCellValue("N" . $rowStartData, isset($detailQty[$page + 9]) ? (float) $detailQty[$page + 9]->qty : "");

                        $sheet->setCellValue('O' . $rowStartData, ($id === $tempID) ? '' : $value->jml_qty);
                        $sheet->setCellValue('P' . $rowStartData, ($id === $tempID) ? '' : $value->total_qty);
                        $sheet->setCellValue('Q' . $rowStartData, ($id === $tempID) ? '' : $satuan);

                        $id = $tempID;
                        $rowStartData++;
                    }
                }
            }
            
            $carton = "";
            if ($picklist->type_bulk_id === "1") {
                $carton = "TOTAL : {$nourut} CARTONS";
            }

            $sheet->setCellValue("C" . ($rowStartData + 2), $carton);
            $sheet->setCellValue('O' . ($rowStartData + 2), $jml_qty);
            $sheet->setCellValue('P' . ($rowStartData + 2), $total_qty);
            if ($picklist->type_bulk_id === "1") {
                $sheet->setCellValue('R' . ($rowStartData + 2), $total_net);
                $sheet->setCellValue('S' . ($rowStartData + 2), $total_groos);
            }

            $spreadsheet->getActiveSheet()->getStyle("B1")->applyFromArray($style);
            $spreadsheet->getActiveSheet()->getStyle("B2")->applyFromArray($style);
            $spreadsheet->getActiveSheet()->getStyle("B3")->applyFromArray($style);

            $spreadsheet->getActiveSheet()->getStyle("D1")->applyFromArray($style);
            $spreadsheet->getActiveSheet()->getStyle("D2")->applyFromArray($style);
            $writer = new Xlsx($spreadsheet);
            $filename = 'darat_surat_jalan_' . $nopl;

//        header('Content-Type: application/vnd.ms-excel');
//        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
//        header('Cache-Control: max-age=0');

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(FCPATH . 'dist/storages/report/suratjalan/' . $filename . '.xlsx');

//        $writer->save('php://output');
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'text_name' => $filename,
                        'type' => 'success', "data" => base_url('dist/storages/report/suratjalan/' . $filename . '.xlsx'))));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
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
            if ($pkl->type_bulk_id === "1") {
                $datas['picklist_detail'] = $this->m_PicklistDetail->detailDraftReport(
                        ['picklist_detail.no_pl' => $nopl, 'picklist_detail.valid !=' => 'cancel'],
                        $nopl, ["bbd.no_bulk", 'warna_remark', 'corak_remark', 'uom', 'lebar_jadi', 'uom_lebar_jadi'], ["BULK"]
                );
            } else {
                $datas['picklist_detail'] = $this->m_PicklistDetail->detailDraftReport(
                        ['picklist_detail.no_pl' => $nopl, 'picklist_detail.valid !=' => 'cancel'],
                        $nopl, ['warna_remark', 'corak_remark', 'uom', 'lebar_jadi', 'uom_lebar_jadi'], []
                );
            }

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
