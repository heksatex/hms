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
}
