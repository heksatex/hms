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
        $this->load->model("m_user");
        $this->load->library('pagination');
    }

    public function index() {
        $username = $this->session->userdata('username');
        $data['id_dept'] = 'RDO';
        $data['sales'] = $this->m_Picklist->getSales();
        $data['user'] = $this->m_user->get_user_by_username($username);
        $data['date'] = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d")))) . ' - ' . date('Y-m-d');
        $this->load->view('report/v_delivery_new', $data);
    }

    public function search($page = 1) {
        try {
            $periode = $this->input->post("periode");
            $marketing = $this->input->post("marketing");
            $corak = $this->input->post("corak");
            $order = $this->input->post("order");
            $summary = $this->input->post("summary");
            $rekap = $this->input->post("rekap");
            $customer = $this->input->post("customer");
            $tgl_buat = $this->input->post("tgl_buat");
            $period = explode(" - ", $periode);
            $returbatal = $this->input->post("returbatal");
            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
            $data = [];
            $condition = [];
            if ($tgl_buat === "0") {
                $condition = array_merge($condition, ['ddo.tanggal_dokumen >=' => $tanggalAwal, 'ddo.tanggal_dokumen <=' => $tanggalAkhir]);
            } else {
                $condition = array_merge($condition, ['ddo.tanggal_buat >=' => $tanggalAwal, 'ddo.tanggal_buat <=' => $tanggalAkhir]);
            }
            if ($customer !== null || $customer !== "") {
                $condition = array_merge($condition, ['pr.nama LIKE' => '%' . $customer . '%']);
            }
            if ($corak !== null || $corak !== "") {
                $condition = array_merge($condition, ['pd.corak_remark LIKE' => '%' . $corak . '%']);
            }
            if ($marketing !== "") {
                $condition = array_merge($condition, ['p.sales_kode' => $marketing]);
            }
            if ($returbatal === "0") {
                $condition = array_merge($condition, ['ddo.status' => 'done', 'dod.status' => 'done', 'pd.valid' => 'done']);
                $list = $this->m_deliveryorder->getDataReport($condition, $order, $rekap, $returbatal);
                $countAll = $this->m_deliveryorder->getDataReportTotal($condition, $order, $rekap, $returbatal);
            } else {
                $doneCondition = array_merge($condition, ['ddo.status' => 'done', 'dod.status' => 'done', 'pd.valid' => 'done']);
                $done = $this->m_deliveryorder->getDataReport($doneCondition, $order, $rekap, "0", true);
                $donecountAll = $this->m_deliveryorder->getDataReportTotal($doneCondition, $order, $rekap, "0", true);

                unset($condition["ddo.tanggal_dokumen >="], $condition["ddo.tanggal_dokumen <="], $condition["ddo.tanggal_buat >="], $condition["ddo.tanggal_buat <="]);
                $returkondisi = array_merge($condition, ['dod.tanggal_retur >=' => $tanggalAwal, 'dod.tanggal_retur <=' => $tanggalAkhir, 'dod.status' => 'retur']);
                $retur = $this->m_deliveryorder->getDataReport($returkondisi, $order, $rekap, "1", true);
                $returcountAll = $this->m_deliveryorder->getDataReportTotal($returkondisi, $order, $rekap, "1", true);

                $cancelkondisi = array_merge($condition, ['ddo.tanggal_batal >=' => $tanggalAwal, 'ddo.tanggal_batal <=' => $tanggalAkhir, 'dod.status' => 'cancel']);
                $cancel = $this->m_deliveryorder->getDataReport($cancelkondisi, $order, $rekap, "1", true);
                $cancelcountAll = $this->m_deliveryorder->getDataReportTotal($cancelkondisi, $order, $rekap, "1", true);

                $list = $this->m_deliveryorder->getDataReportUnion([$done, $retur, $cancel], $order);
                $countAll = $this->m_deliveryorder->getDataReportTotalUnion([$donecountAll, $returcountAll, $cancelcountAll]);
            }
            $totalPaging = 0;
            $paging = [
                'total' => $countAll
            ];
            $data["paging"] = $paging;

            $data['data'] = $this->load->view('report/v_delivery_new_detail', ['list' => $list, 'rekap' => $rekap, 'summary' => $summary, 'rekap' => $rekap], true);
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
            $corak = $this->input->post("corak");
            $order = $this->input->post("order");
            $summary = $this->input->post("summary") ?? "0";
            $rekap = $this->input->post("rekap");
            $customer = $this->input->post("customer");
            $period = explode(" - ", $periode);
            $qtyHph = $this->input->post("qtyhph");
            $cintern = $this->input->post("cintern");
            $tgl_buat = $this->input->post("tgl_buat") ?? "0";
            $returbatal = $this->input->post("returbatal") ?? "0";
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
            $sheet->setCellValue('J1', 'Corak Jual');
            $sheet->setCellValue('K1', 'Lebar');
            $sheet->setCellValue('L1', 'Warna');
            $sheet->setCellValue('M1', 'Corak Intern');
            $sheet->setCellValue('N1', 'Qty HPH');
            $sheet->setCellValue('O1', 'Uom');
            $sheet->setCellValue('P1', 'Qty 2 HPH');
            $sheet->setCellValue('Q1', 'Uom 2');
            $sheet->setCellValue('R1', 'Qty Jual');
            $sheet->setCellValue('S1', 'Uom Jual');
            $sheet->setCellValue('T1', 'Qty 2 Jual');
            $sheet->setCellValue('U1', 'Uom 2Jual');
            $sheet->setCellValue('V1', 'Lot');
            $sheet->setCellValue('W1', 'User');
            $sheet->setCellValue('X1', 'Catatan');
            $sheet->setCellValue('Y1', 'Marketing');
            $sheet->setCellValue('Z1', 'Status');
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
            $data = [];
            $condition = [];
            if ($tgl_buat === "0") {
                $condition = array_merge($condition, ['ddo.tanggal_dokumen >=' => $tanggalAwal, 'ddo.tanggal_dokumen <=' => $tanggalAkhir]);
            } else {
                $condition = array_merge($condition, ['ddo.tanggal_buat >=' => $tanggalAwal, 'ddo.tanggal_buat <=' => $tanggalAkhir]);
            }
            if ($customer !== null || $customer !== "") {
                $condition = array_merge($condition, ['pr.nama LIKE' => '%' . $customer . '%']);
            }
            if ($corak !== null || $corak !== "") {
                $condition = array_merge($condition, ['pd.corak_remark LIKE' => '%' . $corak . '%']);
            }
            if ($marketing !== "") {
                $condition = array_merge($condition, ['p.sales_kode' => $marketing]);
            }
            if ($returbatal === "0") {
                $condition = array_merge($condition, ['ddo.status' => 'done', 'dod.status' => 'done', 'pd.valid' => 'done']);
                $list = $this->m_deliveryorder->getDataReport($condition, $order, $rekap, $returbatal);
//                $countAll = $this->m_deliveryorder->getDataReportTotal($condition, $order, $rekap, $returbatal);
            } else {
                $doneCondition = array_merge($condition, ['ddo.status' => 'done', 'dod.status' => 'done', 'pd.valid' => 'done']);
                $done = $this->m_deliveryorder->getDataReport($doneCondition, $order, $rekap, "0", true);
//                $donecountAll = $this->m_deliveryorder->getDataReportTotal($doneCondition, $order, $rekap, "0", true);

                unset($condition["ddo.tanggal_dokumen >="], $condition["ddo.tanggal_dokumen <="], $condition["ddo.tanggal_buat >="], $condition["ddo.tanggal_buat <="]);
                $returkondisi = array_merge($condition, ['dod.tanggal_retur >=' => $tanggalAwal, 'dod.tanggal_retur <=' => $tanggalAkhir, 'dod.status' => 'retur']);
                $retur = $this->m_deliveryorder->getDataReport($returkondisi, $order, $rekap, "1", true);
//                $returcountAll = $this->m_deliveryorder->getDataReportTotal($returkondisi, $order, $rekap, "1", true);

                $cancelkondisi = array_merge($condition, ['ddo.tanggal_batal >=' => $tanggalAwal, 'ddo.tanggal_batal <=' => $tanggalAkhir, 'dod.status' => 'cancel']);
                $cancel = $this->m_deliveryorder->getDataReport($cancelkondisi, $order, $rekap, "1", true);
//                $cancelcountAll = $this->m_deliveryorder->getDataReportTotal($cancelkondisi, $order, $rekap, "1", true);

                $list = $this->m_deliveryorder->getDataReportUnion([$done, $retur, $cancel], $order);
//                $countAll = $this->m_deliveryorder->getDataReportTotalUnion([$donecountAll, $returcountAll, $cancelcountAll]);
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
            if (count($list) < 1) {
                throw new \Exception("Data tidak ditemukan", 500);
            }

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
                $sheet->setCellValue('F' . $rowStartData, strtoupper($value->jenis_jual));
                $sheet->setCellValue('G' . $rowStartData, $value->no_picklist);
                $sheet->setCellValue('H' . $rowStartData, $value->nama);
                $sheet->setCellValue('I' . $rowStartData, $value->alamat_kirim ?? $value->alamat);
                $sheet->setCellValue('J' . $rowStartData, ($rekap === "global") ? "" : $value->corak_remark);
                $sheet->setCellValue('K' . $rowStartData, ($rekap === "global") ? "" : (($value->lebar_jadi === "-" || is_null($value->lebar_jadi)) ? "" : ($value->lebar_jadi . " " . $value->uom_lebar_jadi)));
                $sheet->setCellValue('L' . $rowStartData, ($rekap === "global") ? "" : $value->warna_remark);
                $sheet->setCellValue('M' . $rowStartData, ($rekap === "global") ? "" : $value->nama_produk);
                $sheet->setCellValue('N' . $rowStartData, $value->total_qty);
                $sheet->setCellValue('o' . $rowStartData, $value->uom);
                $sheet->setCellValue('p' . $rowStartData, $value->total_qty2);
                $sheet->setCellValue('q' . $rowStartData, $value->uom2);
                $sheet->setCellValue('r' . $rowStartData, $value->total_qty_jual);
                $sheet->setCellValue('s' . $rowStartData, $value->uom_jual);
                $sheet->setCellValue('t' . $rowStartData, $value->total_qty2_jual);
                $sheet->setCellValue('u' . $rowStartData, $value->uom2_jual);
                $sheet->setCellValue('v' . $rowStartData, $value->total_lot);
                $sheet->setCellValue('w' . $rowStartData, $value->user);
                $sheet->setCellValue('x' . $rowStartData, $value->note);
                $sheet->setCellValue('y' . $rowStartData, $value->marketing ?? "-");
                $sheet->setCellValue('z' . $rowStartData, $value->dod_status);
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
                            $sheet->setCellValue('l' . $rowStartData, "SUM : " . $value->no_sj);
                            $sheet->setCellValue('m' . $rowStartData, "");
                            $sheet->setCellValue('n' . $rowStartData, $sum["total_qty"]);
                            $sheet->setCellValue('o' . $rowStartData, $sumUom["uom"]);
                            $sheet->setCellValue('p' . $rowStartData, $sum["total_qty2"]);
                            $sheet->setCellValue('q' . $rowStartData, $sumUom["uom2"]);
                            $sheet->setCellValue('r' . $rowStartData, $sum["total_qty_jual"]);
                            $sheet->setCellValue('s' . $rowStartData, $sumUom["uom_jual"]);
                            $sheet->setCellValue('t' . $rowStartData, $sum["total_qty2_jual"]);
                            $sheet->setCellValue('u' . $rowStartData, $sumUom["uom2_jual"]);
                            $sheet->setCellValue('v' . $rowStartData, $sum["total_lot"]);
                            $sheet->setCellValue('w' . $rowStartData, $value->user);
                            $sheet->setCellValue('x' . $rowStartData, $value->note);
                            $sheet->setCellValue('y' . $rowStartData, $value->marketing ?? "-");
                            $sheet->setCellValue('Z' . $rowStartData, "");

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
                        $sheet->setCellValue('l' . $rowStartData, "SUM : " . $value->no_sj);
                        $sheet->setCellValue('m' . $rowStartData, "");
                        $sheet->setCellValue('n' . $rowStartData, $sum["total_qty"]);
                        $sheet->setCellValue('o' . $rowStartData, $sumUom["uom"]);
                        $sheet->setCellValue('p' . $rowStartData, $sum["total_qty2"]);
                        $sheet->setCellValue('q' . $rowStartData, $sumUom["uom2"]);
                        $sheet->setCellValue('r' . $rowStartData, $sum["total_qty_jual"]);
                        $sheet->setCellValue('s' . $rowStartData, $sumUom["uom_jual"]);
                        $sheet->setCellValue('t' . $rowStartData, $sum["total_qty2_jual"]);
                        $sheet->setCellValue('u' . $rowStartData, $sumUom["uom2_jual"]);
                        $sheet->setCellValue('v' . $rowStartData, $sum["total_lot"]);
                        $sheet->setCellValue('w' . $rowStartData, $value->user);
                        $sheet->setCellValue('x' . $rowStartData, $value->note);
                        $sheet->setCellValue('y' . $rowStartData, $value->marketing ?? "-");
                        $sheet->setCellValue('Z' . $rowStartData, "");
                    }
                }
                $tempid = $value->no_sj;
            }
            if (!$qtyHph) {
                $spreadsheet->getActiveSheet()->removeColumn("N", 4);
            }
            if (!$cintern) {
                $spreadsheet->getActiveSheet()->removeColumn("M", 1);
            }
            $writer = new Xlsx($spreadsheet);
            $filename = "delivery_" . $rekap . ' periode ' . $period[0] . ' - ' . $period[1];
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
