<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/*
  defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Machinemonitoring
 *
 * @author RONI
 */
class Machinemonitoring extends MY_Controller {

    //put your code here

    protected $waktuShip1 = "07:00:00";
    protected $waktuShip2 = "15:00:00";
    protected $waktuShip3 = "23:00:00";

    public function __construct() {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load modul global
        $this->load->model("m_global");
        $this->load->library("token");
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    public function index($depth = 'RPTMM') {
        $dept = $_GET["dept"] ?? "";
        $dept_nm = $_GET["nm"] ?? "";
        $data["dept"] = $dept;
        $data["dept_nm"] = $dept_nm;

        $ip = $_SERVER['REMOTE_ADDR']; // Mengambil IP pengunjung

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $data["ip_socket"] = "http://157.20.244.218:8889";
        } else {
            $data["ip_socket"] = "ws://10.10.0.17:8889";
        }

        $data['id_dept'] = $depth;
        $data["class"] = $this->uri->segment(1);
        $model = new $this->m_global;
        if (date("H:i:s") >= $this->waktuShip1 && date("H:i:s") < $this->waktuShip2) {
            $mulai = date("Y-m-d {$this->waktuShip1}");
        } else if (date("H:i:s") >= $this->waktuShip2 && date("H:i:s") < $this->waktuShip3) {
            $mulai = date("Y-m-d {$this->waktuShip2}");
        } else {
            $mulai = date("Y-m-d {$this->waktuShip3}");
        }
        $sampai = date("Y-m-d H:i:s");
//        $mulai = date("Y-m-d H:i:s", strtotime("-15 minute", strtotime($sampai)));
        $data["times"] = $sampai;
        $data["ship"] = $mulai;
        $model->setTables("mesin mst")
                ->setJoins("log_mc log", "mst.devid_ard=log.devid AND mst.port_ard=log.port")->setWheres(["status_aktif" => "t", "port_ard > " => 0])
                ->setWheres(["timelog >=" => $mulai, "timelog <=" => $sampai])
                ->setSelects(["nama_mesin,count(state) as total,state,port,devid,no_mesin,dept_id,mc_id"])
                ->setSelects(["COUNT(log.state)*SUM(log.state=1) as downtime"])
                ->setSelects(["COUNT(log.state)*SUM(log.state=0) as uptime"])
                ->setGroups(["devid", "port"])->setOrder(["nama_mesin" => "asc", "MAX(timelog)" => "desc"]);
//        if ($dept !== "") {
            $model->setWheres(["dept_id" => $dept]);
//        }
        $data["mesin"] = $model->getData();
        $mulai = date("Y-m-d H:i:s", strtotime("-3 day", strtotime($sampai)));
        $model->setTables("mesin mst")
                ->setJoins("log_mc log", "mst.devid_ard=log.devid AND mst.port_ard=log.port")->setWheres(["status_aktif" => "t", "port_ard > " => 0])
                ->setWheres(["timelog >=" => $mulai, "timelog <=" => $sampai])
                ->setSelects(["COUNT(log.state)*SUM(log.state=1) as downtime"])
                ->setSelects(["COUNT(log.state)*SUM(log.state=0) as uptime"])
                ->setSelects(["nama_mesin,count(state) as total,state,port,devid,no_mesin,dept_id,mc_id,max(timelog) as last_time"])
                ->setGroups(["devid", "port", "state"])->setOrder(["nama_mesin" => "asc", "MAX(timelog)" => "desc"]);
//        if ($dept !== "") {
            $model->setWheres(["dept_id" => $dept]);
//        }
        $durasis = $model->getData();
        $durasi = [];
        foreach ($durasis as $key => $value) {
            $nm = "d{$value->devid}p{$value->port}";
            if (isset($durasi[$nm])) {
                $durasi[$nm]->downtime += $value->downtime;
                $durasi[$nm]->uptime += $value->uptime;
//                $durasi[$nm]->total_up = $value->uptime / $value->total;
//                $durasi[$nm]->total_down = $value->downtime / $value->total;
                $datetime1 = strtotime($durasi[$nm]->last_time);
                $datetime2 = strtotime($value->last_time);
                $difference_in_seconds = abs($datetime2 - $datetime1);
                $durasi[$nm]->time_running = round($difference_in_seconds / 60);
                if ($durasi[$nm]->state == "1") {
                    $durasi[$nm]->total_up = 0;
                    $durasi[$nm]->total_down = $durasi[$nm]->time_running;
                    $durasi[$nm]->total_down_text = $this->con_min_days($durasi[$nm]->time_running);
                } else {
                    $durasi[$nm]->total_down = 0;
                    $durasi[$nm]->total_up = $durasi[$nm]->time_running;
                    $durasi[$nm]->total_up_text = $this->con_min_days($durasi[$nm]->time_running);
                }
                continue;
            }
            $value->total_up = $value->uptime / $value->total;
            $value->total_down = $value->downtime / $value->total;
            $value->total_up_text = $this->con_min_days($value->total_up);
            $value->total_down_text = $this->con_min_days($value->total_down);
            $value->time_running = $value->total_up + $value->total_down;
            $durasi[$nm] = $value;
        }
        $data["durasi"] = $durasi;

        $data["allMesin"] = $model->setTables("mesin")
                        ->setJoins("departemen", "departemen.kode = dept_id", "left")->setWheres(["mesin.status_aktif" => "t", "port_ard > " => 0])
                        ->setSelects(["dept_id", "departemen.nama"])->setGroups(["dept_id"])->getData();
//        log_message("error",json_encode($durasi));
        $this->load->view('report/v_machine_monitoring', $data);
    }

    public function update() {
        try {
            $mulai = $this->input->post("times");
            $model = new $this->m_global;
            $sampai = date("Y-m-d H:i:s");
            $data = $model->setTables("mesin mst")
                            ->setJoins("log_mc log", "mst.devid_ard=log.devid AND mst.port_ard=log.port")->setWheres(["status_aktif" => "t", "port_ard > " => 0])
                            ->setWheres(["timelog >=" => $mulai, "timelog <=" => $sampai])
                            ->setSelects(["COUNT(log.state)*SUM(log.state=1) as downtime"])
                            ->setSelects(["COUNT(log.state)*SUM(log.state=0) as uptime"])
                            ->setSelects(["nama_mesin,count(state) as total,state,port,devid,no_mesin,dept_id,mc_id"])
                            ->setGroups(["devid", "port"])->setOrder(["nama_mesin" => "asc", "MAX(timelog)" => "desc"])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $data, "times" => $sampai)));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    protected function con_min_days($mins) {

        $hours = str_pad(floor($mins / 60), 2, "0", STR_PAD_LEFT);
        $mins = str_pad($mins % 60, 2, "0", STR_PAD_LEFT);
        $days = 0;
        if ((int) $hours > 24) {
            $days = str_pad(floor($hours / 24), 2, "0", STR_PAD_LEFT);
            $hours = str_pad($hours % 24, 2, "0", STR_PAD_LEFT);
        }
        if ($days > 0) {

            return $days . " Days Ago";
        }
        if ((int) $hours === 0) {
            return "{$mins} Min";
        }
        return "{$hours} Hours, {$mins} Min";
    }
}
