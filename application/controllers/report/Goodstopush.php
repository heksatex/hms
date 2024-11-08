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
        $data['id_dept'] = 'RGTP';
        $sales = new $this->m_gtp;
        $data['sales'] = $sales->setTables("mst_sales_group")->setOrder(["nama_sales_group" => "asc"])->setWheres(["view" => "1"])->setSelects(["nama_sales_group"])->getData();

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
            $content = $this->load->view("report/v_gtp_detail_data", $data, true);
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
            $list = $datas->setOrders([null, "nama_sales_group", "report_date", "corak", "customer_name", "jml_warna", "lot", null, null, "category"])
                            ->setSearch(["corak", "customer_name"])->setWheres(["nama_sales_group" => $sales, "date(report_date)" => $report_date]);
            $no = $_POST['start'];
            foreach ($list->getData() as $key => $field) {
                $no++;
                $data [] = [
                    $no,
                    $field->nama_sales_group,
                    $field->report_date,
                    "<a class='detail' href='#' data-sales='{$field->nama_sales_group}' data-date='{$field->report_date}' data-corak='{$field->corak}' data-lokasi='{$field->lokasi}'>{$field->corak}</a>",
                    $field->customer_name,
                    $field->jml_warna,
                    $field->lot,
                    $field->qty . ' ' . $field->uom,
                    $field->qty2 . ' ' . $field->uom2,
                    $field->category,
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
            $lokasi = $this->input->post("lokasi");
            $data = array();
            $detail = new $this->m_gtp;
            $list = $detail->setTables('goods_to_push_detail')->setOrders([null, "kode_produk", "nama_produk", "lot", "nama_grade", null, null, null, null, "lokasi_fisik", "lebar_jadi"])
                    ->setSearch(["kode_produk", "nama_produk", "lot", "lokasi_fisik", "lebar_jadi"])
                    ->setOrder(['create_date' => 'desc'])
                    ->setWheres(["nama_sales_group" => $sales, "date(report_date)" => $report_date,"lokasi"=>$lokasi]);
            if ($lokasi === "GRG/Stock") {
                $list->setWheres(["nama_produk"=>$corak]);
            } else {
                $list->setWheres(["corak_remark"=>$corak]);
            }
//                    ->setWhereRaw("( nama_produk='{$corak}' or corak_remark = '{$corak}')");
            $no = $_POST['start'];
            foreach ($list->getData() as $key => $field) {
                $no++;
                $data [] = [
                    $no,
                    $field->kode_produk,
                    $field->nama_produk,
                    $field->lot,
                    $field->nama_grade,
                    $field->qty . ' ' . $field->uom,
                    $field->qty2 . ' ' . $field->uom2,
                    $field->qty_jual . ' ' . $field->uom_jual,
                    $field->qty2_jual . ' ' . $field->uom2_jual,
                    $field->lokasi,
                    $field->lebar_jadi,
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
}
