<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Loglogin
 *
 * @author RONI
 */
class Loglogin extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_global");
    }

    public function index() {
        $data['id_dept'] = 'RLLGN';
        $this->load->view('setting/v_log_login', $data);
    }

    public function get_data() {
        try {
            $data = [];
            $no = $_POST['start'];
            $model = new $this->m_global;
            $model->setTables("log_login")->setOrders([null, "username", "ip", "created_at"])
                    ->setSearch(["username", "ip", "note"])
                    ->setOrder(["created_at" => "desc"]);
            foreach ($model->getData() as $field) {
                $no++;
                $data [] = [
                    $no++,
                    $field->username,
                    $field->ip,
                    $field->created_at,
                    $field->note
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $model->getDataCountAll(),
                "recordsFiltered" => $model->getDataCountFiltered(),
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
