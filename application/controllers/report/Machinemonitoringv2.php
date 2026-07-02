<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

class Machinemonitoringv2 extends MY_Controller {

    //put your code here

    protected $waktuShip1 = "07:00:00";
    protected $waktuShip2 = "15:00:00";
    protected $waktuShip3 = "23:00:00";
    protected $warnaStatus = [
        '#198754',
        '#dc3545',
        '#1B13F5',
        '#B29E1E',
        '#484858'
    ];
    protected $state = [
        '1' => 0,
        '2' => 0,
        '3' => 0,
        '4' => 0,
        '5' => 0
    ];
    protected $status = [
        '1' => [
            'stt' => 'Running',
            'warna' => '',
            'jumlah' => 0
        ],
        '2' => [
            'stt' => 'No Response',
            'warna' => '',
            'jumlah' => 0
        ],
        '3' => [
            'stt' => 'Ganti Benang',
            'warna' => '',
            'jumlah' => 0
        ],
        '4' => [
            'stt' => 'Putus / Problem',
            'warna' => '',
            'jumlah' => 0
        ],
        '5' => [
            'stt' => 'No Order',
            'warna' => '',
            'jumlah' => 0
        ]
    ];

//($value->state == 0) ? '#198754' : '#dc3545'
    public function __construct() {
        parent::__construct();
        $this->load->model("_module"); //load modul global
        $this->load->model("m_global");
        $this->load->driver('cache', array('adapter' => 'file'));

        $this->status["1"]["warna"] = $this->warnaStatus[0];
        $this->status["2"]["warna"] = $this->warnaStatus[1];
        $this->status["3"]["warna"] = $this->warnaStatus[2];
        $this->status["4"]["warna"] = $this->warnaStatus[3];
        $this->status["5"]["warna"] = $this->warnaStatus[4];
    }

    public function index($dept = "WRD") {
//        $data["dept_id"] = $depth;
//        $dept = $_GET["dept"] ?? "";
//        $dept_nm = $_GET["nm"] ?? "";
        $data["dept"] = $dept;
        $data["dept_nm"] = "";
        $data["class"] = $this->uri->segment(1);
        $ip = $_SERVER['REMOTE_ADDR']; // Mengambil IP pengunjung

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $data["ip_socket"] = "http://157.20.244.218:8889";
        } else {
            $data["ip_socket"] = "ws://10.10.0.17:8889";
        }

        $model = new $this->m_global;
        $data["allMesin"] = $model->setTables("mesin")
                        ->setJoins("departemen", "departemen.kode = dept_id", "left")->setWheres(["mesin.status_aktif" => "t", "devid_esp > " => 0])
                        ->setSelects(["dept_id", "departemen.nama"])->setGroups(["dept_id"])->getData();

        if (date("H:i:s") >= $this->waktuShip1 && date("H:i:s") < $this->waktuShip2) {
            $mulai = date("Y-m-d {$this->waktuShip1}");
        } else if (date("H:i:s") >= $this->waktuShip2 && date("H:i:s") < $this->waktuShip3) {
            $mulai = date("Y-m-d {$this->waktuShip2}");
        } else {
            $mulai = date("Y-m-d {$this->waktuShip3}");
        }
        $sampai = date("Y-m-d H:i:s");
        $data["times"] = $sampai;
        $data["ship"] = $mulai;

        $model->setTables("mesin mst")
                ->setJoins("log_mesin log", "mst.devid_esp=log.devid")
                ->setWheres(["timelog >=" => $mulai, "timelog <=" => $sampai])->setWheres(["status_aktif" => "t"])
                ->setSelects(["nama_mesin,count(state) as total,state,devid,no_mesin,dept_id,mc_id"])
                ->setSelects(["COUNT(log.state)*SUM(log.state<>1) as downtime"])
                ->setSelects(["COUNT(log.state)*SUM(log.state=1) as uptime"])
                ->setGroups(["devid"])->setOrder(["CAST(SUBSTR(nama_mesin FROM 3) AS UNSIGNED)" => "asc", "MAX(timelog)" => "desc"]);
        $model->setWheres(["dept_id" => $dept]);
        $data["mesin"] = $model->getData();
        $data["count_mesin"] = count($data["mesin"]);
        $mulai = date("Y-m-d H:i:s", strtotime("-3 day", strtotime($sampai)));
        $model->setTables("mesin mst")
                ->setJoins("log_mesin log", "mst.devid_esp=log.devid")->setWheres(["status_aktif" => "t"])
                ->setWheres(["timelog >=" => $mulai, "timelog <=" => $sampai])
                ->setSelects(["COUNT(log.state)*SUM(log.state<>1) as downtime"])
                ->setSelects(["COUNT(log.state)*SUM(log.state=1) as uptime"])
                ->setSelects(["nama_mesin,count(state) as total,state,devid,no_mesin,dept_id,mc_id,max(timelog) as last_time"])
                ->setGroups(["devid", "state"])->setOrder(["nama_mesin" => "asc", "MAX(timelog)" => "desc"]);
        $model->setWheres(["dept_id" => $dept]);

        $durasis = $model->getData();
        $durasi = [];
        foreach ($durasis as $key => $value) {
            $nm = "d{$value->devid}";
            if (isset($durasi[$nm])) {
                $durasi[$nm]->downtime += $value->downtime;
                $durasi[$nm]->uptime += $value->uptime;
//                $durasi[$nm]->total_up = $value->uptime / $value->total;
//                $durasi[$nm]->total_down = $value->downtime / $value->total;
                $datetime1 = strtotime($durasi[$nm]->last_time);
                $datetime2 = strtotime($value->last_time);
                $difference_in_seconds = abs($datetime2 - $datetime1);
                $durasi[$nm]->time_running = round($difference_in_seconds / 60);
//                $this->status[$durasi[$nm]->state]["jumlah"] += 1;
                if ($durasi[$nm]->state == "1") {
                    $durasi[$nm]->total_up = 0;
                    $durasi[$nm]->total_down = $durasi[$nm]->time_running;
                    $durasi[$nm]->total_down_text = $this->con_min_days($durasi[$nm]->time_running);
                    $this->status[$durasi[$nm]->state]["jumlah"] += 1;
                } else {
                    $durasi[$nm]->total_down = 0;
                    $durasi[$nm]->total_up = $durasi[$nm]->time_running;
                    $durasi[$nm]->total_up_text = $this->con_min_days($durasi[$nm]->time_running);
                    $this->status["2"]["jumlah"] += 1;
                }
                continue;
            }
            $value->status =$this->status[$value->state]["stt"];
            $value->total_up = $value->uptime / $value->total;
            $value->total_down = $value->downtime / $value->total;
            $value->total_up_text = $this->con_min_days($value->total_up);
            $value->total_down_text = $this->con_min_days($value->total_down);
            $value->time_running = $value->total_up + $value->total_down;
            $durasi[$nm] = $value;
        }
        $data["durasi"] = $durasi;
        $data["status"] = $this->status;
        $data["warnaStatus"] = json_encode($this->warnaStatus);
        $data["state"] = json_encode($this->state);
        $data["departmen"] = $model->setTables("departemen")->setWheres(["kode" => $dept])->getDetail();
//        $this->load->view('report/v_machine_monitoring_v2_1', $data);
        //cobav2_2
        $this->load->view('report/v_machine_monitoring_v2_2', $data);
    }

    public function detail($dept = "WRD") {
        $model = new $this->m_global;
        $dt = $this->input->get("date") ?? date("d M");
        $data["msn"] = $this->input->get("mesin") ?? "";
        $data["warnaStatus"] = json_encode($this->warnaStatus);
        $dateObj = DateTime::createFromFormat("d M Y", "{$dt} " . date("Y"));
        $data["date"] = $dateObj->format("Y-m-d");
        $data["mesin"] = $model->setTables("mesin mst")->setWheres(["devid_esp >" => 0, "dept_id" => $dept])->getData();
        $data["departmen"] = $model->setTables("departemen")->setWheres(["kode" => $dept])->getDetail();
        $this->load->view('report/v_machine_monitoring_v2_2_detail', $data);
    }

    public function get_items() {
        try {
            $model = new $this->m_global;
            $dep = $this->input->post("dept");
            $items = $model->setTables("log_mc_timeline")->setWheres(["dept_id" => $dep])->setOrder(["CAST(SUBSTR(nama_mesin FROM 3) AS UNSIGNED)" => "desc"])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $items)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function get_grafiks() {
        try {
            $model = new $this->m_global;
            $dep = $this->input->post("dept");
            $day = $this->input->post("days");
            $sampai = date("Y-m-d");
            $mulai = date("Y-m-d", strtotime("{$day} day", strtotime($sampai)));

            $list = $model->setTables("mesin mst")
                            ->setJoins("log_mesin log", "mst.devid_esp=log.devid")->setWheres(["status_aktif" => "t"])
                            ->setWheres(["date(timelog) >=" => $mulai, "date(timelog) <=" => $sampai, "dept_id" => $dep])
                            ->setSelects(["date(timelog) as tgl,count(state) as total"])
                            ->setSelects(["COUNT(log.state)*SUM(log.state=1) as running"])
                            ->setGroups(["date(timelog)"])->setOrder(["date(timelog)" => "asc"])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $list)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function ins_timeline() {
        try {
            $model = new $this->m_global;
            $mesin = $model->setTables("mesin mst")->setWheres(["status_aktif" => "t", "devid_esp > " => 0])
                            ->setOrder(["nama_mesin" => "asc"])->getData();
            $mesins = [];
            foreach ($mesin as $key => $value) {
                $nm = "d{$value->devid_esp}";
                $mesins [] = [
                    "id" => $nm,
                    "content" => $value->nama_mesin,
                    "value" => ($key + 1)
                ];
            }


            $sampai = date("Y-m-d H:i:s");
            $mulai = date("Y-m-d H:i:s", strtotime("-24 hours"));
            $lists = $model->setTables("mesin mst")
                            ->setJoins("log_mesin log", "mst.devid_esp=log.devid")->setWheres(["status_aktif" => "t"])
                            ->setWheres(["timelog >=" => $mulai, "timelog <=" => $sampai, "state <>" => 0])
                            ->setOrder(["timelog" => "asc"])
                            ->setSelects(["state,devid,no_mesin,dept_id,mc_id,timelog"])->getData();

            $items = [];
            foreach ($lists as $key => $value) {
                $nm = "d{$value->devid}";
                if (isset($items[$nm])) {
                    $items[$nm][] = [
                        "start" => $value->timelog,
                        "end" => $value->timelog,
                        "status" => $this->status[$value->state]["warna"],
                        "dept_id" => $value->dept_id,
                        "state" => $value->state
                    ];
                } else {
                    $items[$nm] = [];
                    $items[$nm][] = [
                        "start" => $value->timelog,
                        "end" => $value->timelog,
                        "status" => $this->status[$value->state]["warna"],
                        "dept_id" => $value->dept_id,
                        "state" => $value->state
                    ];
                }
            }
            $insert = [];
            foreach ($mesins as $key => $value) {
                if (isset($items[$value["id"]])) {
                    $name = $value["content"];
                    $loop = 0;
                    $tempStt = "";
                    $tempStar = "";
                    $tempEnd = "";
                    $temDept = "";
                    $states = "";

                    foreach ($items[$value["id"]] as $k => $val) {
                        if ($tempStt !== $this->status[$val["state"]]["warna"]) {
                            if ($tempStt !== "") {
                                $insert [] = ["nama_mesin" => $name, "warna_status" => $tempStt, "start" => $tempStar, "end" => $tempEnd, "dept_id" => $temDept, "status" => $states];
                            }
                            $tempStt = $this->status[$val["state"]]["warna"];
                            $tempStar = $val["start"];
                            $tempEnd = $val["end"];
                            $temDept = $val["dept_id"];
                            $states = $val["state"];
                        } else {
//                            if($name == "MC1"){
                            $date1 = strtotime($tempEnd);
                            $date2 = strtotime($val["start"]);
                            $interval = $date2 - $date1;
//                            log_message("error",$tempEnd." - ".$val["start"]." = ".$interval->s);
//                            }
                            if ($interval > 100) {
                                $insert [] = ["nama_mesin" => $name, "warna_status" => $tempStt, "start" => $tempStar, "end" => $tempEnd, "dept_id" => $temDept, "status" => $states];
                                $tempStt = $this->status[$val["state"]]["warna"];
                                $tempStar = $val["start"];
                                $tempEnd = $val["end"];
                                $temDept = $val["dept_id"];
                                $states = $val["state"];
//                                continue;
                            } else {
                                $tempEnd = $val["end"];
                            }
                        }
                        if (!isset($items[$value["id"]][$k + 1])) {
                            if ($tempStt !== "")
                                $insert [] = ["nama_mesin" => $name, "warna_status" => $tempStt, "start" => $tempStar, "end" => $tempEnd, "dept_id" => $temDept, "status" => $states];
                        }
                    }

//                    foreach ($items[$value["id"]] as $k => $val) {
//                        $loop += 1;
//                        if ($tempStt === "") {
//                            $tempStt = $this->status[$val["state"]]["warna"];
//                            $tempStar = $val["start"];
//                            $tempEnd = $val["end"];
//                            $temDept = $val["dept_id"];
//                        } else {
//                            if ($val["status"] === $tempStt) {
//                                $tempEnd = $val["end"];
//                            } else {
//                                $insert [] = ["nama_mesin" => $name, "warna_status" => $tempStt, "start" => $tempStar, "end" => $tempEnd, "dept_id" => $temDept, "status" => $val["state"]];
//                                $tempStt = $this->status[$val["state"]]["warna"];
//                                $tempStar = $val["start"];
//                                $tempEnd = $val["end"];
//                                $temDept = $val["dept_id"];
//                            }
//                        }
//
//                        if ($loop === 120) {
//                            $loop = 0;
//                            $insert [] = ["nama_mesin" => $name, "warna_status" => $tempStt, "start" => $tempStar, "end" => $tempEnd, "dept_id" => $temDept, "status" => $val["state"]];
//                            $tempStt = "";
//                            $tempStar = "";
//                            $tempEnd = "";
//                            $temDept = "";
//                        }
//                        if (!isset($items[$value["id"]][$k + 1])) {
//                            if ($tempStt !== "")
//                                $insert [] = ["nama_mesin" => $name, "warna_status" => $tempStt, "start" => $tempStar, "end" => $tempEnd, "dept_id" => $temDept, "status" => $val["state"]];
//                        }
//                    }
                }
            }
            if (isset($insert[0])) {
                $model->excQuery("LOCK TABLES log_mc_timeline WRITE;");
                $model->excQuery("truncate log_mc_timeline;");
                $model->setTables("log_mc_timeline")->saveBatch($insert);
                $model->excQuery("UNLOCK TABLES;");
            }
        } catch (Exception $ex) {
            log_message("error", json_encode($ex));
        }
    }

    protected function detailQuery() {
        try {
            $mesin = $this->input->post("mesin");
            $tanggal = $this->input->post("date");
            $tanggals = explode(" - ", $tanggal);

            $model = new $this->m_global;

            $model->setTables("log_mesin")
                    ->setJoins("mesin", "(mesin.devid_esp = log_mesin.devid and mesin.devid_esp > 0)")
                    ->setSelects(["COUNT(IF(state = '1', 1, NULL)) as running"])
                    ->setSelects(["COUNT(IF(state = '2', 1, NULL)) as noresp"])
                    ->setSelects(["COUNT(IF(state = '3', 1, NULL)) as benang"])
                    ->setSelects(["COUNT(IF(state = '4', 1, NULL)) as problem"])
                    ->setSelects(["COUNT(IF(state = '5', 1, NULL)) as noorder"])
                    ->setSelects(["mesin.nama_mesin as mesin,state,date(timelog) as tgl"])
                    ->setSearch(["nama_mesin"])
                    ->setGroups(["devid"])
                    ->setWheres(["date(timelog) >=" => date("Y-m-d", strtotime($tanggals[0])), "date(timelog) <=" => date("Y-m-d", strtotime($tanggals[1]))]);
            if (!empty($mesin))
                $model->setWheres(["devid" => $mesin]);

            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function detail_table() {
        try {
            $model = $this->detailQuery();
            $data = array();
            $no = $_POST['start'];
            foreach ($model->getData() as $field) {
                $no++;
                $data[] = [
                    $no,
                    $field->mesin,
                    $field->running,
                    $field->noresp,
                    $field->benang,
                    $field->problem,
                    $field->noorder
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

    public function get_graph() {
        try {
            $model = $this->detailQuery()->setGroups(["devid", "date(timelog)"], true);
            $list = [];
            $mesin = [];
            $date = [];
            foreach ($model->getData() as $key => $value) {

                if (!in_array($value->tgl, $date))
                    $date[] = $value->tgl;
                if (!$arr = self::in_array_r($value->mesin, $list)) {
                    $mesin[] = $value->mesin;
                    $list[] = [
                        "name" => $value->mesin,
                        "type" => "line",
                        "stack" => "total",
                        "data" => [$value->running]
                    ];
                    continue;
                }

//                log_message("error",array_keys($arr));
                array_push($list[array_keys($arr)[0]]["data"], $value->running);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $list, "mesin" => $mesin, "date" => $date)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
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

    protected function in_array_r($needle, $haystack, $strict = false) {
        foreach ($haystack as $key => $item) {
            if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && self::in_array_r($needle, $item, $strict))) {
                return [$key => $item];
            }
        }

        return false;
    }
}
