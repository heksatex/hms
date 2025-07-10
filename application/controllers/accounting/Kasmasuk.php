<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Kasmasuk
 *
 * @author RONI
 */
require FCPATH . 'vendor/autoload.php';

class Kasmasuk extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load modul global
        $this->config->load('additional');
    }
    
    public function add() {
        $data['id_dept'] = 'ACCKM';
        $this->load->view('accounting/v_kas_masuk_add', $data);
    }
}
