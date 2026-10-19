<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Scadacountdown
 *
 * @author RONI
 */
class Scadacountdown extends MY_Controller {

    //put your code here
    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module"); //load modul global
        $this->load->model("m_global");
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    protected $state = [
        '1' => [
            "status" => "Running",
            "warna" => "#198754"
        ],
        '2' => [
            "status" => "No Response",
            "warna" => "#dc3545"
        ],
        '3' => [
            "status" => "Ganti Lembar",
            "warna" => "#1B13F5"
        ],
        '4' => [
            "status" => "Putus / Problem",
            "warna" => "#B29E1E"
        ],
        '5' => [
            "status" => "Bongkar Pasang",
            "warna" => "#484858"
        ],
        '6' => [
            "status" => "Nyucuk",
            "warna" => "#B27272"
        ]
    ];

    public function index($dept = "TRI") {
        $ip = $_SERVER['REMOTE_ADDR']; // Mengambil IP pengunjung
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $data["ip_socket"] = "http://157.20.244.218:8889";
        } else {
            $data["ip_socket"] = "ws://10.10.0.17:8889";
        }
        $data["state"] = $this->state;
        $data["dept"] = $dept;
        $model = new $this->m_global;
        $data["departmen"] = $model->setTables("departemen")->setWheres(["kode" => $dept])->getDetail();
        $data["msn"] = $model->setTables("mesin")->setWheres(["status_aktif" => "t", "dept_id" => $dept, "devid_esp >" => 0])->setOrder(["row_order"])->getData();
        $this->load->view("report/v_scada_countdown_timer", $data);
    }

    public function list_data() {
        try {
            $dept = $this->input->get("dept");
            $model = new $this->m_global;
            $model->setTables("mesin mst")->setWheres(["status_aktif" => "t", "dept_id" => $dept, "devid_esp >" => 0, "start_time <=" => date("Y-m-d H:i"), "estimasi_menit_habis > " => 0])
                    ->setJoins("(select mrp.kode,mrp.mc_id, start_time, estimasi_menit_habis,GROUP_CONCAT(mrprt.nama_produk) as nama_produk,
GROUP_CONCAT(mrprt.reff_note) as reff_note,estimasi_tgl_habis
from mrp_production_rm_target mrprt
join mrp_production mrp on (mrp.kode = mrprt.kode and mrp.status in ('ready','draft'))
where dept_id = '{$dept}' 
GROUP BY mrprt.kode order by start_time desc) mrp", "(mst.mc_id = mrp.mc_id and mst.dept_id = '{$dept}')", "LEFT")
                    ->setOrder(["start_time" => "asc"])
//        ->setGroups(["mrp.mc_id"])
                    ->setSelects(["mst.mc_id", "mst.no_mesin", "kode", "nama_produk", "reff_note", "start_time", "estimasi_menit_habis", "mst.devid_esp","estimasi_tgl_habis"]);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('data' => $model->getData())));
        } catch (Exception $ex) {
            
        }
    }
}
