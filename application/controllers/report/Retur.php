<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Retur
 *
 * @author RONI
 */
defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Settings;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Retur extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_deliveryorder");
        $this->load->model("m_deliveryretur");
        $this->load->model("m_Picklist");
    }

    public function index() {
        $data['id_dept'] = 'RRDO';
        $data['sales'] = $this->m_Picklist->getSales();
        $data['date'] = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d")))) . ' - ' . date('Y-m-d');
        $this->load->view('report/v_retur_delivery', $data);
    }

    public function search() {
        try {
            $periode = $this->input->post("periode");
            $marketing = $this->input->post("marketing");
            $order = $this->input->post("order");
            $rekap = $this->input->post("rekap");
            $status = $this->input->post("status");
            $summary = $this->input->post("summary");
            $period = explode(" - ", $periode);
            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
            $data = [];
            $condition = [];
            if ($marketing !== "") {
                $condition = array_merge($condition, ['p.sales_kode' => $marketing]);
            }

            if ($status === "cancel") {
                $condition = array_merge($condition, ['ddo.tanggal_batal >=' => $tanggalAwal, 'ddo.tanggal_batal <=' => $tanggalAkhir, 'dod.status' => $status]);
                $list = $this->m_deliveryretur->getDataReport($condition, $order, $rekap);
                $countAll = $this->m_deliveryretur->getDataReportTotal($condition, $order, $rekap);
            } else if ($status === "retur") {
                $condition = array_merge($condition, ['dod.tanggal_retur >=' => $tanggalAwal, 'dod.tanggal_retur <=' => $tanggalAkhir, 'dod.status' => $status]);
                $list = $this->m_deliveryretur->getDataReport($condition, $order, $rekap);
                $countAll = $this->m_deliveryretur->getDataReportTotal($condition, $order, $rekap);
            } else {

                $queryCancel = $this->m_deliveryretur->getDataReport(array_merge($condition, ['ddo.tanggal_batal >=' => $tanggalAwal, 'ddo.tanggal_batal <=' => $tanggalAkhir, 'dod.status' => 'cancel']),
                        $order, $rekap, true);
                $queryRetur = $this->m_deliveryretur->getDataReport(array_merge($condition, ['dod.tanggal_retur >=' => $tanggalAwal, 'dod.tanggal_retur <=' => $tanggalAkhir, 'dod.status' => 'retur']),
                        $order, $rekap, true);
                $list = $this->m_deliveryretur->getDataReportUnion([$queryCancel, $queryRetur], $order, $rekap);
                $countAll = $this->m_deliveryretur->getDataReportTotalUnion([$queryCancel, $queryRetur], $rekap);
            }


            $data["total"] = $countAll;
            $data['data'] = $this->load->view('report/v_retur_delivery_detail', ['list' => $list, 'rekap' => $rekap, 'summary' => $summary, 'rekap' => $rekap, 'status' => $status], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $data)));
        } catch (Exception $ex) {
            
        }
    }

    public function export() {
        try {
            $periode = $this->input->post("periode");
            $marketing = $this->input->post("marketing");
            $order = $this->input->post("order");
            $rekap = $this->input->post("rekap");
            $status = $this->input->post("status");
            $summary = $this->input->post("summary");
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
            $sheet->setCellValue('F1', 'Status');
            $sheet->setCellValue("G1", "Tanggal " . str_replace("_", " ", $status));
            $sheet->setCellValue('H1', 'Tipe');
            $sheet->setCellValue('I1', 'No.Picklist');
            $sheet->setCellValue('J1', 'Buyer');
            $sheet->setCellValue('K1', 'Alamat');
            $sheet->setCellValue('L1', 'Corak');
            $sheet->setCellValue('M1', 'Lebar');
            $sheet->setCellValue('N1', 'Warna');
            $sheet->setCellValue('O1', 'Qty HPH');
            $sheet->setCellValue('P1', 'Uom');
            $sheet->setCellValue('Q1', 'Qty 2 HPH');
            $sheet->setCellValue('R1', 'Uom 2');
            $sheet->setCellValue('S1', 'Qty Jual');
            $sheet->setCellValue('T1', 'Uom Jual');
            $sheet->setCellValue('U1', 'Qty 2 Jual');
            $sheet->setCellValue('V1', 'Uom 2Jual');
            $sheet->setCellValue('W1', 'Lot');
            $sheet->setCellValue('X1', 'User');
            $sheet->setCellValue('Y1', 'Catatan');
            $sheet->setCellValue('Z1', 'Marketing');
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
            $data = [];
            $condition = [];
            if ($marketing !== "") {
                $condition = array_merge($condition, ['p.sales_kode' => $marketing]);
            }
            if ($status === "cancel") {
                $condition = array_merge($condition, ['ddo.tanggal_batal >=' => $tanggalAwal, 'ddo.tanggal_batal <=' => $tanggalAkhir, 'ddo.status' => $status]);
                $list = $this->m_deliveryretur->getDataReport($condition, $order, $rekap);
            } else if ($status === "retur") {
                $condition = array_merge($condition, ['dod.tanggal_retur >=' => $tanggalAwal, 'dod.tanggal_retur <=' => $tanggalAkhir, 'dod.status' => $status, 'ddo.status' => 'done']);
                $list = $this->m_deliveryretur->getDataReport($condition, $order, $rekap);
            } else {
                $queryCancel = $this->m_deliveryretur->getDataReport(array_merge($condition, ['ddo.tanggal_batal >=' => $tanggalAwal, 'ddo.tanggal_batal <=' => $tanggalAkhir, 'ddo.status' => 'cancel']),
                        $order, $rekap, true);
                $queryRetur = $this->m_deliveryretur->getDataReport(array_merge($condition, ['dod.tanggal_retur >=' => $tanggalAwal, 'dod.tanggal_retur <=' => $tanggalAkhir, 'dod.status' => 'retur']),
                        $order, $rekap, true);
                $list = $this->m_deliveryretur->getDataReportUnion([$queryCancel, $queryRetur], $order, $rekap);
            }

            if (count($list) < 1) {
                throw new \Exception("Data tidak ditemukan", 500);
            }
            $pool = new ApcuCachePool();
            $sCache = new SimpleCacheBridge($pool);
            Settings::setCache($sCache);
            $no = 1;
            $rowStartData = 1;
            $tempid = "";
            $sumDef = array(
                'total_qty' => (float) 0,
                'total_qty2' => (float) 0,
                'total_qty_jual' => (float) 0,
                'total_qty2_jual' => (float) 0,
                'total_lot' => 0,
            );
            $sumUomDef = array(
                'uom' => "",
                'uom2' => "",
                'uom_jual' => "",
                'uom2_jual' => "",
            );
            $sum = $sumDef;
            $sumUom = $sumUomDef;

            foreach ($list as $key => $value) {

                $rowStartData++;
                $sum["total_qty"] += $value->total_qty;
                $sum["total_qty2"] += $value->total_qty2;
                $sum["total_qty_jual"] += $value->total_qty_jual;
                $sum["total_qty2_jual"] += $value->total_qty2_jual;
                $sumUom["uom"] = $value->uom;
                $sumUom["uom2"] = $value->uom2;
                $sumUom["uom_jual"] = $value->uom_jual;
                $sumUom["uom2_jual"] = $value->uom2_jual;
                if ($rekap !== "barcode") {
                    $sum["total_lot"] += $value->total_lot;
                } else {
                    $sum["total_lot"] = $value->total_lot;
                }

                $sheet->setCellValue("A" . $rowStartData, $no++);
                $sheet->setCellValue('B' . $rowStartData, $value->no);
                $sheet->setCellValue('C' . $rowStartData, $value->no_sj);
                $sheet->setCellValue('D' . $rowStartData, date('Y-m-d', strtotime($value->tanggal_buat)));
                $sheet->setCellValue('E' . $rowStartData, date('Y-m-d', strtotime($value->tanggal_dokumen)));
                $sheet->setCellValue('F' . $rowStartData, $value->status ?? "");
                $sheet->setCellValue("G" . $rowStartData, ($status === "cancel") ? $value->tanggal_batal : ($status === "retur") ? $value->tanggal_retur : ($value->tanggal_batal ?? $value->tanggal_retur));
                $sheet->setCellValue('H' . $rowStartData, strtoupper($value->jenis_jual));
                $sheet->setCellValue('I' . $rowStartData, $value->no_picklist);
                $sheet->setCellValue('J' . $rowStartData, $value->nama);
                $sheet->setCellValue('K' . $rowStartData, $value->alamat_kirim ?? $value->alamat);
                $sheet->setCellValue('L' . $rowStartData, ($rekap === "global") ? "" : $value->corak_remark);
                $sheet->setCellValue('M' . $rowStartData, ($rekap === "global") ? "" : (($value->lebar_jadi === "-" || is_null($value->lebar_jadi)) ? "" : ($value->lebar_jadi . " " . $value->uom_lebar_jadi)));
                $sheet->setCellValue('N' . $rowStartData, ($rekap === "global") ? "" : $value->warna_remark);
                $sheet->setCellValue('O' . $rowStartData, $value->total_qty);
                $sheet->setCellValue('P' . $rowStartData, $value->uom);
                $sheet->setCellValue('Q' . $rowStartData, $value->total_qty2);
                $sheet->setCellValue('R' . $rowStartData, $value->uom2);
                $sheet->setCellValue('S' . $rowStartData, $value->total_qty_jual);
                $sheet->setCellValue('T' . $rowStartData, $value->uom_jual);
                $sheet->setCellValue('U' . $rowStartData, $value->total_qty2_jual);
                $sheet->setCellValue('V' . $rowStartData, $value->uom2_jual);
                $sheet->setCellValue('W' . $rowStartData, $value->total_lot);
                $sheet->setCellValue('X' . $rowStartData, $value->user);
                $sheet->setCellValue('Y' . $rowStartData, $value->note);
                $sheet->setCellValue('Z' . $rowStartData, $value->marketing ?? "-");

                if ($summary === "1") {
                    if (isset($list[$key + 1])) {
                        if ($value->no_sj !== $list[$key + 1]->no_sj) {
                            $rowStartData++;
                            $sheet->setCellValue("A" . $rowStartData, "");
                            $sheet->setCellValue('B' . $rowStartData, "");
                            $sheet->setCellValue('C' . $rowStartData, "");
                            $sheet->setCellValue('D' . $rowStartData, "");
                            $sheet->setCellValue('E' . $rowStartData, "");
                            $sheet->setCellValue('F' . $rowStartData, "");
                            $sheet->setCellValue('G' . $rowStartData, "");
                            $sheet->setCellValue('H' . $rowStartData, "");
                            $sheet->setCellValue('I' . $rowStartData, "");
                            $sheet->setCellValue('J' . $rowStartData, "");
                            $sheet->setCellValue('K' . $rowStartData, "");
                            $sheet->setCellValue('L' . $rowStartData, "");
                            $sheet->setCellValue('M' . $rowStartData, "");
                            $sheet->setCellValue('N' . $rowStartData, "SUM : " . $value->no_sj);
                            $sheet->setCellValue('O' . $rowStartData, $sum["total_qty"]);
                            $sheet->setCellValue('P' . $rowStartData, $sumUom["uom"]);
                            $sheet->setCellValue('Q' . $rowStartData, $sum["total_qty2"]);
                            $sheet->setCellValue('R' . $rowStartData, $sumUom["uom2"]);
                            $sheet->setCellValue('S' . $rowStartData, $sum["total_qty_jual"]);
                            $sheet->setCellValue('T' . $rowStartData, $sumUom["uom_jual"]);
                            $sheet->setCellValue('U' . $rowStartData, $sum["total_qty2_jual"]);
                            $sheet->setCellValue('V' . $rowStartData, $sumUom["uom2_jual"]);
                            $sheet->setCellValue('W' . $rowStartData, $sum["total_lot"]);
                            $sheet->setCellValue('X' . $rowStartData, $value->user);
                            $sheet->setCellValue('Y' . $rowStartData, $value->note);
                            $sheet->setCellValue('Z' . $rowStartData, $value->marketing ?? "-");

                            $rowStartData++;
                            $sheet->setCellValue("A" . $rowStartData, "");
                            $sheet->setCellValue('B' . $rowStartData, "");
                            $sheet->setCellValue('C' . $rowStartData, "");
                            $sheet->setCellValue('D' . $rowStartData, "");
                            $sheet->setCellValue('E' . $rowStartData, "");
                            $sheet->setCellValue('F' . $rowStartData, "");
                            $sheet->setCellValue('G' . $rowStartData, "");
                            $sheet->setCellValue('H' . $rowStartData, "");
                            $sheet->setCellValue('I' . $rowStartData, "");
                            $sheet->setCellValue('J' . $rowStartData, "");
                            $sheet->setCellValue('K' . $rowStartData, "");
                            $sheet->setCellValue('L' . $rowStartData, "");
                            $sheet->setCellValue('M' . $rowStartData, "");
                            $sheet->setCellValue('N' . $rowStartData, "");
                            $sheet->setCellValue('O' . $rowStartData, "");
                            $sheet->setCellValue('P' . $rowStartData, "");
                            $sheet->setCellValue('Q' . $rowStartData, "");
                            $sheet->setCellValue('R' . $rowStartData, "");
                            $sheet->setCellValue('S' . $rowStartData, "");
                            $sheet->setCellValue('T' . $rowStartData, "");
                            $sheet->setCellValue('U' . $rowStartData, "");
                            $sheet->setCellValue('V' . $rowStartData, "");
                            $sheet->setCellValue('W' . $rowStartData, "");
                            $sheet->setCellValue('X' . $rowStartData, "");
                            $sheet->setCellValue('Y' . $rowStartData, "");
                            $sheet->setCellValue('Z' . $rowStartData, "");

                            $sum = $sumDef;
                            $sumUom = $sumUomDef;
                        }
                    } else {
                        $rowStartData++;
                        $sheet->setCellValue("A" . $rowStartData, "");
                        $sheet->setCellValue('B' . $rowStartData, "");
                        $sheet->setCellValue('C' . $rowStartData, "");
                        $sheet->setCellValue('D' . $rowStartData, "");
                        $sheet->setCellValue('E' . $rowStartData, "");
                        $sheet->setCellValue('F' . $rowStartData, "");
                        $sheet->setCellValue('G' . $rowStartData, "");
                        $sheet->setCellValue('H' . $rowStartData, "");
                        $sheet->setCellValue('I' . $rowStartData, "");
                        $sheet->setCellValue('J' . $rowStartData, "");
                        $sheet->setCellValue('K' . $rowStartData, "");
                        $sheet->setCellValue('L' . $rowStartData, "");
                        $sheet->setCellValue('M' . $rowStartData, "");
                        $sheet->setCellValue('N' . $rowStartData, "SUM : " . $value->no_sj);
                        $sheet->setCellValue('O' . $rowStartData, $sum["total_qty"]);
                        $sheet->setCellValue('P' . $rowStartData, $sumUom["uom"]);
                        $sheet->setCellValue('Q' . $rowStartData, $sum["total_qty2"]);
                        $sheet->setCellValue('R' . $rowStartData, $sumUom["uom2"]);
                        $sheet->setCellValue('S' . $rowStartData, $sum["total_qty_jual"]);
                        $sheet->setCellValue('T' . $rowStartData, $sumUom["uom_jual"]);
                        $sheet->setCellValue('U' . $rowStartData, $sum["total_qty2_jual"]);
                        $sheet->setCellValue('V' . $rowStartData, $sumUom["uom2_jual"]);
                        $sheet->setCellValue('W' . $rowStartData, $sum["total_lot"]);
                        $sheet->setCellValue('X' . $rowStartData, $value->user);
                        $sheet->setCellValue('Y' . $rowStartData, $value->note);
                        $sheet->setCellValue('Z' . $rowStartData, $value->marketing ?? "-");
                    }
                }
                $tempid = $value->no_sj;
            }
//            if (!$qtyHph) {
//                $spreadsheet->getActiveSheet()->removeColumn("M", 4);
//            }
            $writer = new Xlsx($spreadsheet);
            $filename = $status . "_delivery_" . $rekap . ' periode ' . $period[0] . ' - ' . $period[1];
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
