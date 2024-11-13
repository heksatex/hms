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
                    ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'type' => 'danger', "content" => [])));
        }
    }

    public function details() {
        try {
            $data["corak"] = $this->input->post("corak");
            $data["date"] = $this->input->post("date");
            $data["sales"] = $this->input->post("sales");
            $data["lokasi"] = $this->input->post("lokasi");
            $data["kategori"] = $this->input->post("kategori");
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
                    ->set_output(json_encode(array('message' => 'Data ditemukan', 'icon' => 'fa fa-check', 'type' => 'danger', "content" => [])));
        }
    }

    public function data() {
        try {
            $sales = $this->input->post("sales");
            $report_date = $this->input->post("report_date");
            $data = array();
            $datas = new $this->m_gtp;
            $list = $datas->setOrders([null, "corak", "category", "jml_warna", "lot", "qty", "qty2", "lebar_jadi", "customer_name","lokasi"])->setOrder(["lokasi,category,corak,uom" => "asc"])
                            ->setSearch(["corak", "customer_name"])->setWheres(["date(report_date)" => $report_date]);
            if ($sales !== "") {
                $list->setWheres(["nama_sales_group" => $sales]);
            }
            $no = $_POST['start'];
            foreach ($list->getData() as $key => $field) {
                $no++;
                $data [] = [
                    $no,
                    "<a class='detail' href='#' data-sales='{$field->nama_sales_group}' data-date='{$field->report_date}' "
                    . "data-corak='{$field->corak}' data-lokasi='{$field->lokasi}' data-kategori='{$field->category}'>{$field->corak}</a>",
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
            $data = array();
            $detail = new $this->m_gtp;
            $list = $detail->setTables('goods_to_push_detail')->setOrders([null, "kode_produk", "nama_produk", "lot", "nama_grade", null, null, null, null, "lokasi_fisik", "lebar_jadi"])
                    ->setSearch(["kode_produk", "nama_produk", "lot", "lokasi_fisik", "lebar_jadi","customer_name"])->setWheres(["nama_sales_group" => $sales, "date(report_date)" => $date, "lokasi" => $lokasi]);
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
                $list->setOrder(['corak,uom' => 'asc'])->setWheres(["nama_produk" => $corak]);
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
                $list->setOrder(['corak_remark,warna_remark' => 'asc'])->setWheres(["corak_remark" => $corak]);
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
}
