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
        $this->load->model("m_Picklist");
    }

    public function index() {
        $data['id_dept'] = 'RDO';
        $data['sales'] = $this->m_Picklist->getSales();
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
            $marketing = $this->input->post("marketing");
            $corak = $this->input->post("corak");
            $order = $this->input->post("order");
//            $summary = $this->input->post("summary");
            $rekap = $this->input->post("rekap");
            $customer = $this->input->post("customer");
            $period = explode(" - ", $periode);
            log_message('error', "ini " . $marketing);
            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
            $data = [];
            $condition = ['ddo.tanggal_dokumen >=' => $tanggalAwal, 'ddo.tanggal_dokumen <=' => $tanggalAkhir, 'ddo.status' => 'done', 'pd.valid' => 'done'];
            if ($customer !== null || $customer !== "") {
                $condition = array_merge($condition, ['pr.nama LIKE' => '%' . $customer . '%']);
            }
            if ($corak !== null || $corak !== "") {
                $condition = array_merge($condition, ['pd.corak_remark LIKE' => '%' . $corak . '%']);
            }
            if ($marketing !== "") {
                $condition = array_merge($condition, ['p.sales_kode' => $marketing]);
            }
            $list = $this->m_deliveryorder->getDataReport($condition, $order, $rekap);
            $no = $_POST['start'];
            foreach ($list as $value) {
                $no++;
                $row = array(
                    $no,
                    $value->no,
                    $value->no_sj,
                    $value->tanggal_buat,
                    $value->tanggal_dokumen,
                    strtoupper($value->jenis_jual),
                    $value->no_picklist,
                    $value->nama,
                    $value->alamat,
                    $value->corak_remark,
                    $value->warna_remark,
                    $value->total_qty . ' ' . $value->uom,
                    $value->total_qty2 . ' ' . $value->uom2,
                    $value->total_qty_jual . ' ' . $value->uom_jual,
                    $value->total_qty2_jual . ' ' . $value->uom2_jual,
                    $value->total_lot,
                    $value->note,
                    $value->marketing,
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
            $marketing = $this->input->post("marketing");
            $period = explode(" - ", $periode);

            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }


            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'DO');
            $sheet->setCellValue('C1', 'No SJ');
            $sheet->setCellValue('D1', 'Tanggal dibuat');
            $sheet->setCellValue('E1', 'Tanggal dikirim');
            $sheet->setCellValue('F1', 'Tipe');
            $sheet->setCellValue('G1', 'No.Picklist');
            $sheet->setCellValue('H1', 'Buyer');
            $sheet->setCellValue('I1', 'Alamat');
            $sheet->setCellValue('J1', 'Corak');
            $sheet->setCellValue('K1', 'Warna');
            $sheet->setCellValue('L1', 'Qty');
            $sheet->setCellValue('M1', 'Uom');
            $sheet->setCellValue('N1', 'Qty 2');
            $sheet->setCellValue('O1', 'Uom 2');
            $sheet->setCellValue('P1', 'Qty Jual');
            $sheet->setCellValue('Q1', 'Uom Jual');
            $sheet->setCellValue('R1', 'Qty 2 Jual');
            $sheet->setCellValue('S1', 'Uom 2Jual');
            $sheet->setCellValue('T1', 'Lot');
            $sheet->setCellValue('U1', 'User');
            $sheet->setCellValue('V1', 'Catatan');
            $sheet->setCellValue('W1', 'Marketing');
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
            $condition = ['ddo.tanggal_dokumen >=' => $tanggalAwal, 'ddo.tanggal_dokumen <=' => $tanggalAkhir, 'ddo.status' => 'done', 'pd.valid' => 'done'];
            if ($customer !== null || $customer !== "") {
                $condition = array_merge($condition, ['pr.nama LIKE' => '%' . $customer . '%']);
            }
            if ($corak !== null || $corak !== "") {
                $condition = array_merge($condition, ['pd.corak_remark LIKE' => '%' . $corak . '%']);
            }
            if ($marketing !== "") {
                $condition = array_merge($condition, ['p.sales_kode' => $marketing]);
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
                $sheet->setCellValue('D' . $rowStartData, date('Y-m-d', strtotime($value->tanggal_buat)));
                $sheet->setCellValue('E' . $rowStartData, date('Y-m-d', strtotime($value->tanggal_dokumen)));
                $sheet->setCellValue('F' . $rowStartData, strtoupper($value->jenis_jual));
                $sheet->setCellValue('G' . $rowStartData, $value->no_picklist);
                $sheet->setCellValue('H' . $rowStartData, $value->nama);
                $sheet->setCellValue('I' . $rowStartData, $value->alamat);
                $sheet->setCellValue('J' . $rowStartData, $value->corak_remark);
                $sheet->setCellValue('K' . $rowStartData, $value->warna_remark);
                $sheet->setCellValue('L' . $rowStartData, $value->total_qty);
                $sheet->setCellValue('M' . $rowStartData, $value->uom);
                $sheet->setCellValue('N' . $rowStartData, $value->total_qty2);
                $sheet->setCellValue('O' . $rowStartData, $value->uom2);
                $sheet->setCellValue('P' . $rowStartData, $value->total_qty_jual);
                $sheet->setCellValue('Q' . $rowStartData, $value->uom_jual);
                $sheet->setCellValue('R' . $rowStartData, $value->total_qty2_jual);
                $sheet->setCellValue('S' . $rowStartData, $value->uom2_jual);
                $sheet->setCellValue('T' . $rowStartData, $value->total_lot);
                $sheet->setCellValue('U' . $rowStartData, $value->user);
                $sheet->setCellValue('V' . $rowStartData, $value->note);
                $sheet->setCellValue('W' . $rowStartData, $value->marketing ?? "-");
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
