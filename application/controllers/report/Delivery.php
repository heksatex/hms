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
            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
            $data = [];
            $condition = ['ddo.status' => 'done', 'dod.status' => 'done'];
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
//            $_POST['start'] = $page ?? 1;
//            $_POST['length'] = 1000;
            $list = $this->m_deliveryorder->getDataReport($condition, $order, $rekap);
            $countAll = $this->m_deliveryorder->getDataReportTotal($condition, $order, $rekap);
            $totalPaging = 0;
            $paging = [
                'total' => $countAll
            ];
            $data["paging"] = $paging;

//            $config['base_url'] = "#";
//            $config['use_page_numbers'] = TRUE;
//            $config['total_rows'] = $countAll;
//            $config['per_page'] = $_POST['length'];
            //$config['first_link']     = FALSE;
            //$config['last_link']      = FALSE;
//            $config['num_links'] = 1;
//            $config['next_link'] = '>';
//            $config['prev_link'] = '<';
//            $config['attributes'] = array('class' => 'paging-report');
//            $this->pagination->initialize($config);
//            $pagination = $this->pagination->create_links();
//            $data['pagination'] = $pagination;
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
            $tgl_buat = $this->input->post("tgl_buat");
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
            $sheet->setCellValue('K1', 'Lebar');
            $sheet->setCellValue('L1', 'Warna');
            $sheet->setCellValue('M1', 'Qty HPH');
            $sheet->setCellValue('N1', 'Uom');
            $sheet->setCellValue('O1', 'Qty 2 HPH');
            $sheet->setCellValue('P1', 'Uom 2');
            $sheet->setCellValue('Q1', 'Qty Jual');
            $sheet->setCellValue('R1', 'Uom Jual');
            $sheet->setCellValue('S1', 'Qty 2 Jual');
            $sheet->setCellValue('T1', 'Uom 2Jual');
            $sheet->setCellValue('U1', 'Lot');
            $sheet->setCellValue('V1', 'User');
            $sheet->setCellValue('W1', 'Catatan');
            $sheet->setCellValue('X1', 'Marketing');
            $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
            $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));
            $data = [];
            $condition = ['ddo.status' => 'done', 'dod.status' => 'done'];
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
            $list = $this->m_deliveryorder->getDataReport($condition, $order, $rekap);
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
                $sheet->setCellValue('M' . $rowStartData, $value->total_qty);
                $sheet->setCellValue('N' . $rowStartData, $value->uom);
                $sheet->setCellValue('O' . $rowStartData, $value->total_qty2);
                $sheet->setCellValue('P' . $rowStartData, $value->uom2);
                $sheet->setCellValue('Q' . $rowStartData, $value->total_qty_jual);
                $sheet->setCellValue('R' . $rowStartData, $value->uom_jual);
                $sheet->setCellValue('S' . $rowStartData, $value->total_qty2_jual);
                $sheet->setCellValue('T' . $rowStartData, $value->uom2_jual);
                $sheet->setCellValue('U' . $rowStartData, $value->total_lot);
                $sheet->setCellValue('V' . $rowStartData, $value->user);
                $sheet->setCellValue('W' . $rowStartData, $value->note);
                $sheet->setCellValue('X' . $rowStartData, $value->marketing ?? "-");
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
                            $sheet->setCellValue('L' . $rowStartData, "SUM : " . $value->no_sj);
                            $sheet->setCellValue('M' . $rowStartData, number_format($sum["total_qty"], 2));
                            $sheet->setCellValue('N' . $rowStartData, $sumUom["uom"]);
                            $sheet->setCellValue('O' . $rowStartData, number_format($sum["total_qty2"], 2));
                            $sheet->setCellValue('P' . $rowStartData, $sumUom["uom2"]);
                            $sheet->setCellValue('Q' . $rowStartData, number_format($sum["total_qty_jual"], 2));
                            $sheet->setCellValue('R' . $rowStartData, $sumUom["uom_jual"]);
                            $sheet->setCellValue('S' . $rowStartData, number_format($sum["total_qty2_jual"], 2));
                            $sheet->setCellValue('T' . $rowStartData, $sumUom["uom2_jual"]);
                            $sheet->setCellValue('U' . $rowStartData, $sum["total_lot"]);
                            $sheet->setCellValue('V' . $rowStartData, $value->user);
                            $sheet->setCellValue('W' . $rowStartData, $value->note);
                            $sheet->setCellValue('X' . $rowStartData, $value->marketing ?? "-");

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
                        $sheet->setCellValue('L' . $rowStartData, "SUM : " . $value->no_sj);
                        $sheet->setCellValue('M' . $rowStartData, number_format($sum["total_qty"], 2));
                        $sheet->setCellValue('N' . $rowStartData, $sumUom["uom"]);
                        $sheet->setCellValue('O' . $rowStartData, number_format($sum["total_qty2"], 2));
                        $sheet->setCellValue('P' . $rowStartData, $sumUom["uom2"]);
                        $sheet->setCellValue('Q' . $rowStartData, number_format($sum["total_qty_jual"], 2));
                        $sheet->setCellValue('R' . $rowStartData, $sumUom["uom_jual"]);
                        $sheet->setCellValue('S' . $rowStartData, number_format($sum["total_qty2_jual"], 2));
                        $sheet->setCellValue('T' . $rowStartData, $sumUom["uom2_jual"]);
                        $sheet->setCellValue('U' . $rowStartData, $sum["total_lot"]);
                        $sheet->setCellValue('V' . $rowStartData, $value->user);
                        $sheet->setCellValue('W' . $rowStartData, $value->note);
                        $sheet->setCellValue('X' . $rowStartData, $value->marketing ?? "-");
                    }
                }
                $tempid = $value->no_sj;
            }
            if (!$qtyHph) {
                $spreadsheet->getActiveSheet()->removeColumn("M", 4);
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
