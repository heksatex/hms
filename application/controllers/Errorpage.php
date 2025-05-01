<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Error
 *
 * @author RONI
 */
class Errorpage extends MY_Controller {

    //put your code here

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        $data["id_dept"] = "EP";
        $data["heading"] = "asdas";
        $data["message"] = "Halaman Tidak Ditemukan";
        echo $this->load->view("errors/v_custom_errors", $data, true);
    }
}
