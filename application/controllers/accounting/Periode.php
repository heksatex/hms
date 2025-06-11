<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Periode
 *
 * @author RONI
 */
class Periode extends MY_Controller {

    //put your code here
    protected $table, $fields;

    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->load->library('database/acc_periode', null, 'db_periode');
        $this->table = $this->db_periode::table;
        $this->fields = (object) $this->db_periode::fields;
    }

    public function index() {
        $data['id_dept'] = 'ACCPRD';
        $this->load->view('accounting/v_periode', $data);
    }

    public function add() {
        $data['id_dept'] = 'ACCPRD';
        $this->load->view('accounting/v_periode_add', $data);
    }

    public function list_data() {
        try {
            $data = array();
            $model = new $this->m_global;
            $datas = $model->setTables($this->table)
//                    ->setGroups([$this->fields->tahunFiskal])
                            ->setOrders([null, $this->fields->tahunFiskal, $this->fields->periode,$this->fields->status])
                            ->setSearch([$this->fields->tahunFiskal, $this->fields->periode])->setOrder([$this->fields->createdAt => "asc"]);
            $no = $_POST['start'];
            foreach ($datas->getData() as $field) {
                $no++;
//                $uri = encrypt_url($field->id);
                $field = (array) $field;
                $data [] = [
                    $no,
                    $field[$this->fields->tahunFiskal],
                    $field[$this->fields->periode],
                    $field[$this->fields->status],
                    ""
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $datas->getDataCountAll(),
                "recordsFiltered" => $datas->getDataCountFiltered(),
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

    public function generate() {
        try {
            $tahun = $this->input->post("tahun");
            $model = new $this->m_global;
            $check = $model->setTables($this->table)->setWheres([$this->fields->tahunFiskal => $tahun])->getDetail();
            if ($check) {
                throw new \Exception("Periode tahun {$tahun} sudah terbentuk", 500);
            }

            $startDate = "{$tahun}-01-01";
            $endDate = "{$tahun}-12-31";
            $startYear = date("Y", strtotime($startDate));
            $list = [];
            $list[] = array(
                $this->fields->periode => $startYear . "/00",
                $this->fields->startPeriode => $startDate,
                $this->fields->endPeriode => $startDate,
                $this->fields->tahunFiskal => $tahun,
                $this->fields->status => "open",
                $this->fields->createdAt => date("Y-m-d H:i:s")
            );

            for ($i = 1; $i <= 12; $i++) {
                $bulan = sprintf("%02d", $i);
                $startPeriode = "{$tahun}-{$bulan}-01";
                $list[] = [
                    $this->fields->periode => sprintf($startYear . "/%02d", $i),
                    $this->fields->startPeriode => $startPeriode,
                    $this->fields->endPeriode => date("Y-m-t", strtotime($startPeriode)),
                    $this->fields->tahunFiskal => $tahun,
                    $this->fields->status => "open",
                    $this->fields->createdAt => date("Y-m-d H:i:s")
                ];
            }
            $html = $this->load->view("accounting/v_periode_generate", ["data" => $list], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => $html, "start" => $startDate, "end" => $endDate, "list" => json_encode($list))));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode() ?? 500))
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function save() {
        try {
            $list = $this->input->post("list") ?? "";
            $model = new $this->m_global;
            $data = json_decode($list);
            if (!is_array($data)) {
                throw new \Exception('Silahkan Generate terlebih dahulu', 500);
            }
            $model->setTables($this->table)->saveBatch($data);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array()));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode() ?? 500))
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}
