<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Goodstopush
 *
 * @author RONI
 */
require_once APPPATH . '/third_party/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Settings;
use Cache\Adapter\Apcu\ApcuCachePool;
use Cache\Bridge\SimpleCache\SimpleCacheBridge;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Goodstopush extends MY_Controller {

    //put your code here

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("m_gtp");
    }

    public function index() {
        $data['id_dept'] = 'RMKT';
        $sales = new $this->m_gtp;
        $dates = clone $sales;
        $data['sales'] = $sales->setTables("mst_sales_group")->setOrder(["nama_sales_group" => "asc"])->setWheres(["view" => "1"])->setSelects(["nama_sales_group"])->getData();
        $_POST["length"] = 10;
        $_POST["start"] = 0;
        $data["dates"] = $dates->setSelects(["DATE(report_date) as dt"])->setGroups(["DATE(report_date)"])->setOrder(["dt" => "DESC"])->getData();
        $this->load->view('report/v_gtp', $data);
    }

    public function search() {
        try {
            $sales = $this->input->post("sales");
            $reportDate = $this->input->post("report_date");
            $datas = new $this->m_gtp;
            if ($sales !== "") {
                $datas->setWheres(["nama_sales_group" => $sales]);
            }
            $data["data"] = $datas->setOrder(["report_date" => "asc"])->setWheres(["DATE(report_date)" => $reportDate])->getData();
            $content = $this->load->view("report/v_gtp_detail", $data, true);

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'type' => 'success', "content" => $content)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data Tidak ditemukan', 'icon' => 'fa fa-check', 'type' => 'danger', "content" => [])));
        }
    }

    public function details() {
        try {
            $data["corak"] = $this->input->post("corak");
            $data["date"] = $this->input->post("date");
            $data["sales"] = $this->input->post("sales");
            $data["lokasi"] = $this->input->post("lokasi");
            $data["kategori"] = $this->input->post("kategori");
            $data["lebar"] = $this->input->post("lebar");
            $data["buyer"] = $this->input->post("buyer");
            if ($data["lokasi"] === "GRG/Stock") {
                $content = $this->load->view("report/v_gtp_detail_data_grg", $data, true);
            } else {
                $content = $this->load->view("report/v_gtp_detail_data", $data, true);
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'type' => 'success', "content" => $content)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Data Tidak ditemukan', 'icon' => 'fa fa-check', 'type' => 'danger', "content" => [])));
        }
    }

    public function data() {
        try {
            $sales = $this->input->post("sales");
            $report_date = $this->input->post("report_date");
            $lokasi = $this->input->post("lokasi");
            $data = array();
            $datas = new $this->m_gtp;
            $list = $datas->setOrders([null, "corak", "category", "jml_warna", "lot", "qty", "qty2", "lebar_jadi", "customer_name", "lokasi"])
                            ->setOrder(["lokasi" => "asc", "category" => "asc", "qty" => "desc", "corak,uom" => "asc"])
                            ->setSearch(["corak", "customer_name"])->setWheres(["date(report_date)" => $report_date, "qty >=" => 50]);
            if ($sales !== "") {
                $list->setWheres(["nama_sales_group" => $sales]);
            }
            if ($lokasi !== "") {
                $list->setWheres(["lokasi" => $lokasi]);
            }
            $no = $_POST['start'];
            foreach ($list->getData() as $key => $field) {
                $no++;
                $data [] = [
                    $no,
                    "<a class='detail' href='#' data-sales='{$field->nama_sales_group}' data-date='{$field->report_date}' "
                    . "data-corak='{$field->corak}' data-lokasi='{$field->lokasi}' data-kategori='{$field->category}' "
                    . "data-lebar='{$field->lebar_jadi}' data-buyer='{$field->customer_name}'>{$field->corak}</a>",
                    $field->category,
                    $field->jml_warna,
                    $field->lot,
                    $field->qty . ' ' . $field->uom,
                    $field->qty2 . ' ' . $field->uom2,
                    $field->lebar_jadi,
                    $field->customer_name,
                    $field->lokasi
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll(),
                "recordsFiltered" => $list->getDataCountFiltered(),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }

    public function detail_data() {
        try {
            $corak = $this->input->post("corak");
            $sales = $this->input->post("sales");
            $report_date = $this->input->post("report_date");
            $date = date("Y-m-d", strtotime($report_date));
            $lokasi = $this->input->post("lokasi");
            $kategori = $this->input->post("kategori");
            $lebar = $this->input->post("lebar");
            $buyer = $this->input->post("buyer");
            $data = array();
            $detail = new $this->m_gtp;
            $list = $detail->setTables('goods_to_push_detail')->setOrders([null, "kode_produk", "nama_produk", "lot", "nama_grade", null, null, null, null, "lokasi_fisik", "lebar_jadi"])
                    ->setSearch(["kode_produk", "nama_produk", "lot", "lokasi_fisik", "lebar_jadi", "customer_name"])
                    ->setWheres(["nama_sales_group" => $sales, "date(report_date)" => $date, "lokasi" => $lokasi, 'customer_name' => $buyer]);
            switch ($kategori) {
                case "14d":
                    $list->setWheres(["umur >=" => 14, "umur <=" => 30]);
                    break;
                case "30d":
                    $list->setWheres(["umur >" => 30, "umur <=" => 90]);
                    break;
                case "90d":
                    $list->setWheres(["umur >" => 90]);
                    break;
            }
            $no = $_POST['start'];
            if ($lokasi === "GRG/Stock") {
                $list->setOrder(["qty" => "desc", "nama_produk,uom" => "desc"])->setWheres(["nama_produk" => $corak]);
                foreach ($list->getData() as $key => $field) {
                    $no++;
                    $data [] = [
                        $no,
                        $field->lot,
                        $field->nama_grade,
                        $field->qty . ' ' . $field->uom,
                        $field->qty2 . ' ' . $field->uom2,
                        $field->lokasi_fisik,
                        $field->sales_order,
                        $field->customer_name,
                        $kategori,
                        $field->umur
                    ];
                }
            } else {
                $list->setOrder(["qty" => "desc", "corak_remark,warna_remark" => "asc"])->setWheres(["corak_remark" => $corak, 'concat(lebar_jadi," ",uom_lebar_jadi) = ' => $lebar]);
                foreach ($list->getData() as $key => $field) {
                    $no++;
                    $data [] = [
                        $no,
                        $field->warna_remark,
                        $field->lot,
                        $field->nama_grade,
                        $field->qty_jual . ' ' . $field->uom_jual,
                        $field->qty2_jual . ' ' . $field->uom2_jual,
                        $field->lebar_jadi . ' ' . $field->uom_lebar_jadi,
                        $field->lokasi_fisik,
                        $field->sales_order,
                        $field->customer_name,
                        $kategori,
                        $field->umur
                    ];
                }
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll(),
                "recordsFiltered" => $list->getDataCountFiltered(),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }

    public function excel() {
        try {
            $sales = $this->input->post("sales");
            $report_date = $this->input->post("report_date");
            $lokasi = $this->input->post("lokasi");
            $model = new $this->m_gtp;

            $model->setTables("goods_to_push")->setOrder(["lokasi" => "asc", "category" => "asc", "qty" => "desc", "corak,uom" => "asc"])
                    ->setWheres(["qty >=" => 50, "DATE(report_date)" => $report_date]);

            if ($sales !== "") {
                $model->setWheres(["nama_sales_group" => $sales]);
            }
            if ($lokasi !== "") {
                $model->setWheres(["lokasi" => $lokasi]);
            }
            $list = $model->getData();
            if (count($list) < 1) {
                throw new \Exception("Data Tidak ditemukan", 500);
            }
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1', 'No');
            $sheet->setCellValue('B1', 'Kategori');
            $sheet->setCellValue('c1', 'Corak');
            $sheet->setCellValue('d1', 'Jumlah Warna');
            $sheet->setCellValue('e1', 'Jumlah LOT');
            $sheet->setCellValue('f1', 'QTY');
            $sheet->setCellValue('g1', 'Satuan');
            $sheet->setCellValue('h1', 'QTY 2');
            $sheet->setCellValue('i1', 'Satuan 2');
            $sheet->setCellValue('j1', 'Lebar Jadi');
            $sheet->setCellValue('k1', 'Lokasi');
            $sheet->setCellValue('l1', 'Customer');
            $sheet->setCellValue('m1', 'Sales');
            $rowStartData = 1;
            $no = 0;
            foreach ($list as $key => $value) {
                $rowStartData++;
                $no++;
                $sheet->setCellValue("A" . $rowStartData, $no);
                $sheet->setCellValue("B" . $rowStartData, $value->category);
                $sheet->setCellValue("c" . $rowStartData, $value->corak);
                $sheet->setCellValue("d" . $rowStartData, $value->jml_warna);
                $sheet->setCellValue('e' . $rowStartData, $value->lot);
                $sheet->setCellValue('f' . $rowStartData, $value->qty);
                $sheet->setCellValue('g' . $rowStartData, $value->uom);
                $sheet->setCellValue('h' . $rowStartData, $value->qty2);
                $sheet->setCellValue('i' . $rowStartData, $value->uom2);
                $sheet->setCellValue('j' . $rowStartData, $value->lebar_jadi);
                $sheet->setCellValue('k' . $rowStartData, $value->lokasi);
                $sheet->setCellValue('l' . $rowStartData, $value->customer_name);
                $sheet->setCellValue('m' . $rowStartData, $value->nama_sales_group);
            }
            $writer = new Xlsx($spreadsheet);
            $filename = "gtp_report_date_{$report_date}";
            $url = "dist/storages/report/gtp/excel";
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
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }
}
