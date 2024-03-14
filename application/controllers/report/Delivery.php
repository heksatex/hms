<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */
defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Settings;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Delivery extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_deliveryorder");
    }

    public function index() {
        $data['id_dept'] = 'RDO';
        $data['date'] = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d")))) . ' - ' . date('Y-m-d');
        $this->load->view('report/v_delivery', $data);
    }

    public function get_rekap() {
        try {
            $rekap = $this->input->post("rekap");
            $data['detail'] = $this->load->view('report/v_delivery_' . $rekap, [], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }

    public function search() {
        try {
            $periode = $this->input->post("periode");
            $customer = $this->input->post("customer");
            $corak = $this->input->post("corak");
            $order = $this->input->post("order");
//            $summary = $this->input->post("summary");
            $rekap = $this->input->post("rekap");
            $period = explode(" - ", $periode);

            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }

            $data = [];
            $condition = ['ddo.tanggal_dokumen >=' => $period[0], 'ddo.tanggal_dokumen <=' => $period[1], 'ddo.status' => 'done'];
            if ($customer !== null || $customer !== "") {
                $condition = array_merge($condition, ['pr.nama LIKE' => '%' . $customer . '%']);
            }
            if ($corak !== null || $corak !== "") {
                $condition = array_merge($condition, ['corak_remark LIKE' => '%' . $corak . '%']);
            }
            $list = $this->m_deliveryorder->getDataReport($condition, $order, $rekap);
            $no = $_POST['start'];
            foreach ($list as $value) {
                $no++;
                $row = array(
                    $no,
                    $value->no,
                    $value->no_sj,
                    $value->tanggal_dokumen,
                    strtoupper($value->jenis_jual),
                    $value->no_picklist,
                    $value->nama,
                    $value->alamat,
                    $value->corak_remark,
                    $value->warna_remark,
                    $value->total_qty . ' ' . $value->uom,
                    $value->total_lot
                );
                $data[] = $row;
            }

            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_deliveryorder->getDataReportTotalAll($condition, $order, $rekap),
                "recordsFiltered" => $this->m_deliveryorder->getDataReportTotal($condition, $order, $rekap),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }

    public function export() {
        try {
            $periode = $this->input->post("periode");
            $customer = $this->input->post("customer");
            $corak = $this->input->post("corak");
            $order = $this->input->post("order");
            $rekap = $this->input->post("rekap");
            $period = explode(" - ", $periode);

            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }


            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'DO');
            $sheet->setCellValue('C1', 'No SJ');
            $sheet->setCellValue('D1', 'Tanggal');
            $sheet->setCellValue('E1', 'Tipe');
            $sheet->setCellValue('F1', 'No.Picklist');
            $sheet->setCellValue('G1', 'Buyer');
            $sheet->setCellValue('H1', 'Alamat');
            $sheet->setCellValue('I1', 'Corak');
            $sheet->setCellValue('J1', 'Warna');
            $sheet->setCellValue('K1', 'Qty / PCS');
            $sheet->setCellValue('L1', 'Lot');
            $sheet->setCellValue('M1', 'Satuan');
            $sheet->setCellValue('N1', 'User');
            $sheet->setCellValue('O1', 'Catatan');
            $sheet->setCellValue('p1', 'Marketing');
            $condition = ['ddo.tanggal_dokumen >=' => $period[0], 'ddo.tanggal_dokumen <=' => $period[1], 'ddo.status' => 'done'];
            if ($customer !== null || $customer !== "") {
                $condition = array_merge($condition, ['pr.nama LIKE' => '%' . $customer . '%']);
            }
            if ($corak !== null || $corak !== "") {
                $condition = array_merge($condition, ['corak_remark LIKE' => '%' . $corak . '%']);
            }
            $list = $this->m_deliveryorder->getDataReport($condition, $order, $rekap);
            if (empty($list)) {
                throw new Exception("data tidak ditemukan", 500);
            }
            $pool = new ApcuCachePool();
            $sCache = new SimpleCacheBridge($pool);
            Settings::setCache($sCache);
            $no = 1;
            $rowStartData = 1;
            foreach ($list as $value) {
                $rowStartData++;
                $sheet->setCellValue("A" . $rowStartData, $no++);
                $sheet->setCellValue('B' . $rowStartData, $value->no);
                $sheet->setCellValue('C' . $rowStartData, $value->no_sj);
                $sheet->setCellValue('D' . $rowStartData, date('Y-m-d', strtotime($value->tanggal_dokumen)));
                $sheet->setCellValue('E' . $rowStartData, strtoupper($value->jenis_jual));
                $sheet->setCellValue('F' . $rowStartData, $value->no_picklist);
                $sheet->setCellValue('G' . $rowStartData, $value->nama);
                $sheet->setCellValue('H' . $rowStartData, $value->alamat);
                $sheet->setCellValue('I' . $rowStartData, $value->corak_remark);
                $sheet->setCellValue('J' . $rowStartData, $value->warna_remark);
                $sheet->setCellValue('K' . $rowStartData, $value->total_qty);
                $sheet->setCellValue('L' . $rowStartData, $value->total_lot);
                $sheet->setCellValue('M' . $rowStartData, $value->uom);
                $sheet->setCellValue('N' . $rowStartData, $value->user);
                $sheet->setCellValue('O' . $rowStartData, $value->note);
                $sheet->setCellValue('P' . $rowStartData, $value->marketing ?? "-");
            }
            $writer = new Xlsx($spreadsheet);
            $filename = $rekap . ' periode ' . $period[0] . ' - ' . $period[1];
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(FCPATH . 'dist/storages/report/suratjalan/' . $filename . '.xlsx');
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil Export', 'icon' => 'fa fa-check', 'text_name' => $filename,
                        'type' => 'success', "data" => base_url('dist/storages/report/suratjalan/' . $filename . '.xlsx'))));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }
}
