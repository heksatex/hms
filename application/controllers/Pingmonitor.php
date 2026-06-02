<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Pingmonitor
 *
 * @author RONI
 */
class Pingmonitor extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->load->model("_module"); //load modul global
        $this->load->model("m_global");
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    public function index() {
        $ip = $_SERVER['REMOTE_ADDR']; // Mengambil IP pengunjung

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $data["ip_socket"] = "http://157.20.244.218:8989";
        } else {
            $data["ip_socket"] = "ws://10.10.0.17:8989";
        }

        $model = new $this->m_global;
        $model->setTables("mst_ip")->setOrder(["ip"]);
        $ip = [];
        foreach ($model->getData() as $key => $value) {
            $ips = explode(".", $value->ip);
            $ip[(string)end($ips)] = (object) $value;
        }
        $data["ip"] = json_encode($ip);
        $this->load->view('v_ipmonitoring', $data);
    }
}
