<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Pembelian
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

class Purchaseorder extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
        $this->load->model("m_user");

        $this->load->library('pagination');
    }

    public function index() {
        $username = $this->session->userdata('username');
        $data['id_dept'] = 'RPO';
        $data['user'] = $this->m_user->get_user_by_username($username);
        $data['date'] = date('Y-m-d', strtotime("-1 month", strtotime(date("Y-m-d")))) . ' - ' . date('Y-m-d');
        $data['warehouse'] = $this->_module->get_list_departement();
        $this->load->view('report/v_purchase_order', $data);
    }

    protected function getdata() {
        $supplier = $this->input->post("supplier");
        $warehouse = $this->input->post("warehouse");
        $jenis = $this->input->post("jenis");
        $group = $this->input->post("group");
        $periode = $this->input->post("periode");
        $period = explode(" - ", $periode);
        if (count($period) < 2) {
            throw new \Exception("Tentukan dahulu periodenya", 500);
        }

        $tanggalAwal = date("Y-m-d H:i:s", strtotime($period[0] . " 00:00:00"));
        $tanggalAkhir = date("Y-m-d H:i:s", strtotime($period[1] . " 23:59:59"));

        $model = new $this->m_global;
        $model->setTables("purchase_order po")->setJoins("purchase_order_detail pod", "pod.po_id = po.id")
                ->setJoins("currency_kurs", "currency_kurs.id = po.currency", "left")
                ->setJoins("partner", "partner.id = po.supplier", "left")
                ->SetJoins("tax", "tax.id = pod.tax_id", "left")
                ->setJoins("departemen", "departemen.kode = pod.warehouse", "left")
                ->setSelects(["pod.*,nilai_currency,IF(jenis = 'rfq','PO','FPT') as jenis", "partner.nama as nama_supp",
                    "coalesce(tax.amount,0) as amount_tax,tax.dpp as dpp_tax,coalesce(tax.tax_lain_id,0) as tax_lain_id",
                    "departemen.nama as gudang",
                    "po.order_date", "currency_kurs.currency as nama_curr"])->setOrder(["order_date" => "asc"])
                ->setWheres(["po.order_date >=" => $tanggalAwal, "po.order_date <=" => $tanggalAkhir])->setWhereIn("po.status", ["purchase_confirmed", "done"]);
        if ($jenis !== "") {
            $model->setWheres(["po.jenis" => $jenis]);
        }
        if (is_array($supplier) && count($supplier) > 0) {
            $model->setWhereIn("po.supplier", $supplier);
        }
        if (is_array($warehouse) && count($warehouse) > 0) {
            $model->setWhereIn("pod.warehouse", $warehouse);
        }
        if ($group !== "") {
            if($group=== "po.supplier") {
            $model->setGroups(["partner.nama", "kode_produk", "uom_beli", "harga_per_uom_beli", "tax_id","po.currency"])->setOrder(["partner.nama" => "asc", "order_date" => "asc"]);
            }
            else {
                 $model->setGroups(["departemen.nama", "kode_produk", "uom_beli", "harga_per_uom_beli", "tax_id","po.currency"])->setOrder(["departemen.nama" => "asc", "order_date" => "asc"]);
            }
        }

        return $model;
    }

    public function search() {
        try {
            $groups = "";
            $group = $this->input->post("group");
            if ($group !== "") {
                if ($group === "po.supplier") {
                    $groups = "nama_supp";
                } else {
                    $groups = "gudang";
                }
            }
            $model = $this->getdata();
            $model3 = new $this->m_global;
            $setDpp = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            $data['data'] = $this->load->view('report/v_purchase_order_detail', ['data' => $model->getData(), 'group' => $groups, 'dpp' => $setDpp], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data tidak ditemukan', 'icon' => 'fa fa-check', 'type' => 'danger', "data" => [])));
        }
    }

    public function export() {
        try {
            $groups = "";
            $group = $this->input->post("group");
            $periode = $this->input->post("periode");
            $period = explode(" - ", $periode);
            if (count($period) < 2) {
                throw new \Exception("Tentukan dahulu periodenya", 500);
            }
            if ($group !== "") {
                if ($group === "po.supplier") {
                    $groups = "nama_supp";
                } else {
                    $groups = "gudang";
                }
            }
            $model = $this->getdata();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'No PO');
            $sheet->setCellValue('C1', 'Supplier');
            $sheet->setCellValue('D1', 'Gudang');
            $sheet->setCellValue('E1', 'Tanggal Order');
            $sheet->setCellValue('F1', 'Jenis');
            $sheet->setCellValue('G1', 'Kode Produk');
            $sheet->setCellValue('H1', 'Nama Produk');
            $sheet->setCellValue('I1', 'Qty Beli');
            $sheet->setCellValue('J1', 'Uom Beli');
            $sheet->setCellValue('K1', 'Mata Uang');
            $sheet->setCellValue('L1', 'Kurs');
            $sheet->setCellValue('M1', 'Harga perQty');
            $sheet->setCellValue('N1', 'Diskon');
            $sheet->setCellValue('O1', 'Pajak');
            $sheet->setCellValue('P1', 'Subtotal');

            $no = 1;
            $total_group = 0;
            $row = 2;
            $data = $model->getData();
            $model3 = new $this->m_global;
            $models = clone $model3;
            $models->setTables("tax");
            $setDpp = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            foreach ($data as $key => $value) {
//                $harga =  $value->harga_per_uom_beli;
//                $diskon =  $value->diskon;
//                $subsubtotal = ($value->qty_beli * $harga) - $diskon;
//                
//                if ($setDpp !== null && $value->dpp_tax !== "1") {
//                    $pajak = (($subsubtotal * 11) / 12) * $value->amount_tax;
//                } else {
//                    $pajak = $subsubtotal * $value->amount_tax;
//                }
//                
//                 if ($value->tax_lain_id !== "0") {
//                     $dataTax = $models->setWhereIn("id", explode(",", $value->tax_lain_id), true)->setSelects(["amount,dpp"])->setOrder(["id"])->getData();
//                     foreach ($dataTax as $kkk => $datas) {
//                        if ($setDpp !== null && $datas->dpp === "1") {
//                            $pajak += (($subsubtotal * 11) / 12) * $datas->amount;
//                            continue;
//                        }
//                        $pajak += $subsubtotal * $datas->amount;
//                    }
//                 }

//                $pajak = $subsubtotal * $value->amount_tax;
                $total_group += $value->total;

                $sheet->setCellValue('A' . $row, $no);
                $sheet->setCellValue('B' . $row, $value->po_no_po);
                $sheet->setCellValue('C' . $row, $value->nama_supp);
                $sheet->setCellValue('D' . $row, $value->gudang);
                $sheet->setCellValue('E' . $row, $value->order_date);
                $sheet->setCellValue('F' . $row, $value->jenis);
                $sheet->setCellValue('G' . $row, "{$value->kode_produk}");
                $sheet->setCellValue('H' . $row, $value->nama_produk);
                $sheet->setCellValue('I' . $row, number_format($value->qty_beli, 2));
                $sheet->setCellValue('J' . $row, $value->uom_beli);
                $sheet->setCellValue('K' . $row, $value->nama_curr);
                $sheet->setCellValue('L' . $row, (string) $value->nilai_currency);
                $sheet->setCellValue('M' . $row, number_format($value->harga_per_uom_beli, 2));
                $sheet->setCellValue('N' . $row, number_format($value->diskon, 2));
                $sheet->setCellValue('O' . $row, number_format($value->pajak, 2));
                $sheet->setCellValue('P' . $row, number_format($value->total, 2));
                $row++;
                if ($groups !== "") {
                    $cek1 = (array) $value;
                    $cek2 = (array) ($data[$key + 1] ?? [$groups => "--"]);
                    if ($cek1[$groups] !== $cek2[$groups]) {
                        $sheet->setCellValue('O' . $row, "Total");
                        $sheet->setCellValue('P' . $row, number_format($total_group, 2));
                        $total_group = 0;
                        $row++;
                    }
                }
                $no++;
            }
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setVisible(false);
            $writer = new Xlsx($spreadsheet);
            $filename = "purchase_order_periode {$period[0]} {$period[1]}";
            $url = "dist/storages/report/po";
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
}
