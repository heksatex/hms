<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

require FCPATH . 'vendor/autoload.php';
require_once APPPATH . '/third_party/vendor/autoload.php';
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Outstandingfaktur
 *
 * @author RONI
 */
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Mpdf\Mpdf;

class Outstandingfaktur extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }

    public function index() {
        $partner = $this->input->get("partner");
        $id_dept = 'ROUTSFP';
        if ($partner !== "") {
            $model = new $this->m_global;
            $data["partner"] = $model->setTables("partner")->setSelects(["id", "nama"])->setWheres(["id" => $partner])->getDetail();
        }
        $data['id_dept'] = $id_dept;
        $this->load->view('report/acc/v_outstanding_faktur', $data);
    }

    protected function _query_customer_faktur() {
        try {
            $customer = $this->input->post("customer");
            $model = new $this->m_global;
            $model->setTables("acc_faktur_penjualan")->setOrder(["partner_nama" => "asc"])
                    ->setWheres(["lunas" => 0, "status" => "confirm"])->setGroups(["partner_id"]);
            if (!empty($customer)) {
                $model->setWheres(["partner_id" => $customer]);
            }
            return $model->setSelects(["partner_nama,partner_id"]);
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    protected function _query_data() {
        try {
            $model = new $this->m_global;
            $model->setTables("acc_faktur_penjualan")->setOrder(["tanggal" => "asc", "no_faktur_internal" => "asc"])
                    ->setSelects(["acc_faktur_penjualan.*", "DATEDIFF(CURDATE(), tanggal) AS hari"]);
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function generate() {
        try {
            $partner = $this->_query_customer_faktur()->getData();
            $lData = $this->_query_data();
            $dt = [];
            foreach ($partner as $key => $value) {
                $datas = $lData->setWheres(["lunas" => 0, "status" => "confirm", "partner_id" => $value->partner_id], true)->getData();
                $data = [];
                foreach ($datas as $keys => $values) {
                    $data[] = $values;
                }
                $dt[$value->partner_nama] = $data;
            }
            $data["data"] = $dt;
            $html = $this->load->view('report/acc/v_outstanding_faktur_detail', $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}
