<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Productionplanning
 *
 * @author RONI
 */
require_once APPPATH . '/third_party/vendor/autoload.php';
require FCPATH . 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Mpdf\Mpdf;

class Productionplanning extends MY_Controller {

    //put your code here
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

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module"); //load modul global
        $this->load->model("m_global");
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    public function index($view = "", $dep = "WRD") {
        $ip = $_SERVER['REMOTE_ADDR']; // Mengambil IP pengunjung
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            $data["ip_socket"] = "http://157.20.244.218:8889";
        } else {
            $data["ip_socket"] = "ws://10.10.0.17:8889";
        }
        $data["dep"] = $dep;
        $data["state"] = $this->state;
//        $model = new $this->m_global;
        $this->load->view("ppic/v_production_plan{$view}", $data);
    }

    public function get_mesin() {
        try {
            $dep = $this->input->get("dept");
            $model = new $this->m_global;
            $model->setTables("mesin mst")->setWheres(["status_aktif" => "t", "dept_id" => $dep])
                    ->setJoins("product_planning_mesin ppm", "ppm.mc_id = mst.mc_id", "left")
                    ->setOrder(["CAST(SUBSTR(nama_mesin FROM 3) AS UNSIGNED)" => "asc"])
                    ->setSelects(["mst.*", "coalesce(benang,'') as benang", "coalesce(time,now()) as time", "coalesce(qty,0) as qty", "coalesce(estimasi,now()) as est"])
                    ->setSelects(["CONCAT('brk-',ppm.id) as ids"])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('data' => $model->getData())));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function get_mo() {
        try {
            $dep = $this->input->get("dept");
            $search = $this->input->get("value");
            $status = $this->input->get("status");
            $mesin = $this->input->get("mesin");
            $model = new $this->m_global;

            $model->setTables("mrp_production mp");
            if ($status === "" && $mesin !== "") {
                $model->setWhereRaw("(mp.mc_id = '{$mesin}' or pp.mc = '{$mesin}')");
            }
            if ($status === "unassigned") {
                $model->setWhereRaw("not EXISTS  (select 1 from product_planning pn where mp.kode = pn.kode and nama = '')");
                if ($mesin !== "")
                    $model->setWheres(["mp.mc_id" => $mesin]);
            }
            if ($status === "assigned") {
                $model->setWheres(["pp.total_minute >" => "0", "pp.nama" => '']);
                if ($mesin !== "")
                    $model->setWheres(["pp.mc" => $mesin]);
            }
            $model->setJoins("mrp_production_rm_target mpt", "(mpt.kode = mp.kode and mpt.status <> 'cancel')", "left")
                    ->setJoins("product_planning pp", "(pp.kode = mp.kode)", "left")
                    ->setWheres(["dept_id" => $dep])->setWhereIn("mp.status", ["ready", "draft", "hold"])
                    ->setOrder(["tanggal" => "desc"])->setGroups(["mp.kode"]);
            if (!empty($search)) {
                $_POST['search']['value'] = $search;
                $model->setSearch(["mp.kode", "origin", "mp.nama_produk", "mc_id", "mp.reff_note"]);
            }

            $model->setSelects(["mp.kode", "mp.nama_produk", "COALESCE(start_time,'') as start_time", "COALESCE(finish_time,'') as finish_time", "mp.status"]);
            $model->setSelects(["COALESCE(total_minute,0) as total_minute", "COALESCE(mc_id,'') as mc_id", "COALESCE(pp.mc,'') as mc", "mp.qty", "mp.uom", "mp.reff_note"])
                    ->setSelects(["sum(mpt.qty) as qty_target"]);
            $count = $model->getDataCountFiltered();
            $_POST['length'] = 20;
            $_POST['start'] = 0;
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('data' => $model->getData(), 'count' => $count)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function rmv_plan() {
        try {
            $kode = $this->input->post("kode");
            $model = new $this->m_global;
            $this->_module->startTransaction();
            $check = $model->setTables("product_planning")->setWhereIn("kode", $kode)->getData();
            if (count($check) < 1) {
                throw new \Exception('Tidak Ditemukan', 500);
            }
            $model->delete();
            $update = [];
            foreach ($check as $key => $value) {
                $update[] = [
                    "kode" => $value->kode,
                    "start_time" => $value->tanggal_start,
                    "finish_time" => $value->tanggal_finish,
                    "mc_id" => $value->mc ?? ''
                ];
            }
//            $model->setTables("mrp_production")->setWheres(["kode" => $kode])->update(["start_time" => $check->tanggal_start, "finish_time" => $check->tanggal_finish, "mc_id" => $check->mc]);
            $checkMrp = $model->setTables("mrp_production")->setWhereIn("kode", $kode)->setWheres(["status" => 'done'])->getData();
            if (count($checkMrp) > 0) {
                throw new \Exception("MO dalam status 'Done'", 500);
            }
            $model->updateBatch($update, "kode");

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('pesan' => "Berhasil")));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function update_plan() {
        try {
            $model = new $this->m_global;
            $this->_module->startTransaction();
            $dt = $this->input->post("dt");
            foreach ($dt as $key => $value) {
                $value = (object)$value;
                $kode = $value->kode;
                $mc = $value->group;
                $start = $value->start_time;
                $finish = $value->finish_time;
                $minute = $value->total_minute;
                $tipe = $value->tipe;
                $check = $model->setTables("product_planning")->setWheres(["kode" => $kode],true)->getDetail();
                if (!$check) {
                    throw new \Exception('Data Tidak Ditemukan', 500);
                }if ($tipe === "box") {
                    $model->update(["total_minute" => $minute]);
                    $checkStt = $model->setTables("mrp_production")->setWheres(["kode" => $kode, "status" => "done"],true)->getDetail();
                    if ($checkStt) {
                        throw new \Exception("MO dalam status 'Done'", 500);
                    }
                    $model->setWheres(["kode" => $kode], true)->update([
                        "start_time" => $start,
                        "finish_time" => $finish,
                        "mc_id" => $mc
                    ]);
                } else {
                    $model->update([
                        "total_minute" => $minute,
                        "tanggal_start" => $start,
                        "tanggal_finish" => $finish,
                        "mc" => $mc
                    ]);
                }
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('pesan' => "Berhasil")));
        } catch (Exception $ex) {
            log_message("error", json_encode($ex));
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function save_plan() {
        try {
            $kode = $this->input->post("kode");
            $mc = $this->input->post("mcid");
            $start = $this->input->post("start");
            $finish = $this->input->post("finish");
            $mc_old = $this->input->post("mcid_old");
            $start_old = $this->input->post("start_old");
            $finish_old = $this->input->post("finish_old");
            $minute = $this->input->post("total_minute");
            $model = new $this->m_global;
            $this->_module->startTransaction();
            $tipe = $this->input->post("tipe");
            $nama = $this->input->post("nama");
            $enckode = "";
            if ($tipe === "box") {
                $kodes = implode("','", $kode);
                //production plan untuk menampung asal nilai mrp production selain total_minute
                $check = $model->setTables("product_planning")->setWhereRaw("kode in ('{$kodes}')")->getData();

                $insert = [];
                $update = [];
                $checksKode = [];

                foreach ($check as $k => $val) {
                    $checksKode[$val->kode] = "1";
                }

                foreach ($kode as $key => $value) {
                    $update[] = [
                        "kode" => $value,
                        "start_time" => $start[$key],
                        "finish_time" => $finish[$key],
                        "mc_id" => $mc[$key]
                    ];
                    if (isset($checksKode[$value])) {
                        continue;
                    }
                    $enckode = encrypt_url($value);
                    $insert [] = [
                        "kode" => $value,
                        "enc_kode" => $enckode,
                        "mc" => $mc_old[$key],
                        "tanggal_start" => $start_old[$key],
                        "tanggal_finish" => $finish_old[$key],
                        "total_minute" => $minute[$key]
                    ];
                }
                $model->saveBatch($insert);
                $model->setTables("mrp_production")->updateBatch($update, "kode");
            } else {
                $model->setTables("product_planning")->save([
                    "kode" => $kode[0],
                    "tanggal_start" => $start[0],
                    "tanggal_finish" => $finish[0],
                    "mc" => $mc[0],
                    "total_minute" => $minute[0],
                    "nama" => $nama[0]
                ]);
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('pesan' => "Berhasil", "enc" => $enckode)));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    //

    protected function _getItems() {
        try {
            $dep = $this->input->post("dept");
            $start = $this->input->post("start");
            $end = $this->input->post("end");
            $start .= " 00:00:01";
            $end .= " 59:59:59";
            $model = new $this->m_global;
            $model->setTables("mrp_production mp")
                    ->setJoins("mrp_production_rm_target mpt", "(mpt.kode = mp.kode and mpt.status <> 'cancel')", "left")
                    ->setJoins("(select mph.kode,sum(mph.qty) as qty from mrp_production_fg_hasil mph join product_planning pp on pp.kode = mph.kode GROUP BY mph.kode) mph", "mph.kode = mp.kode", "left")
//                     ->setJoins("mrp_production_rm_hasil mph", "(mph.kode = mp.kode)", "left")
                    ->setJoins("product_planning pp", "pp.kode = mp.kode")
                    ->setGroups(["mp.kode"])
                    ->setWheres(["dept_id" => $dep, "mp.status" => 'ready'])->setWhereRaw("(start_time >= '{$start}' and start_time < '{$end}') or (finish_time >= '{$start}' and finish_time < '{$end}')");
            $model->setSelects(["mp.kode", "mp.nama_produk", "total_minute", "mp.qty", "mp.uom", "start_time", "finish_time", "mc_id", "'box' as tipe", "enc_kode"])
                    ->setSelects(["sum(mpt.qty) as qty_target", "sum(mph.qty) as qty_hasil", "mp.status"]);
            $boxQuery = $model->getQuery();
            $model->setTables("product_planning pp")->setSelects(["pp.kode", "nama as nama_produk", "total_minute", "'0' as qty", "'' as uom"])
                    ->setSelects(["tanggal_start as start_time", "tanggal_finish as finish_time", "mc as mc_id", "'break' as tipe", "0 as qty_target", "enc_kode", "0 as qty_hasil", "'' as status"])
                    ->setWhereRaw("((tanggal_start >= '{$start}' and tanggal_start < '{$end}') or (tanggal_finish >= '{$start}' and tanggal_finish < '{$end}'))")
//                    ->setWhereRaw("not EXISTS  (select 1 from mrp_production mp where mp.kode = pp.kode)");
                    ->setWheres(["enc_kode" => ""]);
            $breakQuery = $model->getQuery();
            $table = "({$boxQuery} union all {$breakQuery}) as tbl";
            $model->setTables($table)->setOrder(["start_time"]);
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function get_items() {
        try {
            $model = $this->_getItems();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('data' => $model->getData())));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function export_excel() {
        try {
            $model = $this->_getItems();
            $start = $this->input->post("start");
            $end = $this->input->post("end");
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $row = 1;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'Mesin');
            $sheet->setCellValue("C{$row}", 'MO');
            $sheet->setCellValue("D{$row}", 'Produk');
            $sheet->setCellValue("E{$row}", 'QTY UOM');
            $sheet->setCellValue("F{$row}", 'Start');
            $sheet->setCellValue("G{$row}", 'Finish');
            $sheet->setCellValue("H{$row}", 'Durasi (Menit)');
            $model->setOrder(["mc_id", "start_time"]);
            foreach ($model->getData() as $key => $value) {
                $row++;
                $sheet->setCellValue("A{$row}", ($key + 1));
                $sheet->setCellValue("B{$row}", $value->mc_id);
                $sheet->setCellValue("C{$row}", ($value->qty > 0) ? $value->kode : '');
                $sheet->setCellValue("D{$row}", $value->nama_produk);
                $sheet->setCellValue("E{$row}", number_format($value->qty, 2) . " {$value->uom}");
                $sheet->setCellValue("F{$row}", $value->start_time);
                $sheet->setCellValue("G{$row}", $value->finish_time);
                $sheet->setCellValue("H{$row}", $value->total_minute);
            }
            $filename = "Product Plan {$start} - {$end}";
            $url = "dist/storages/export/ppic";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save(FCPATH . $url . '/' . $filename . '.xlsx');
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil Export', 'icon' => 'fa fa-check', 'text_name' => $filename,
                        'type' => 'success', "data" => base_url($url . '/' . $filename . '.xlsx'))));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }

    public function export_pdf() {
        try {
            $model = $this->_getItems();
            $model->setOrder(["mc_id", "start_time"]);
            $start = $this->input->post("start");
            $end = $this->input->post("end");
            $data["data"] = $model->getData();

            $url = "dist/storages/print/ppic";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            ini_set("pcre.backtrack_limit", "50000000");
            $html = $this->load->view('print/ppic/product_plan', $data, true);
            $mpdf = new Mpdf(['tempDir' => FCPATH . '/tmp']);
            $filename = "Product Plan {$start} - {$end}.pdf";
            $mpdf->WriteHTML($html);
            $pathFile = "{$url}/{$filename}";
            $mpdf->Output(FCPATH . $pathFile, "F");

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("url" => base_url($pathFile))));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            ini_set("pcre.backtrack_limit", "1000000");
        }
    }

    public function updatemesin() {
        try {
            $this->form_validation->set_rules([
                [
                    'field' => 'frm-bng[]',
                    'label' => 'Kode Benang',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Pada Item harus diisi'
                    ],
                    [
                        'field' => 'frm-time[]',
                        'label' => 'Waktu Naik Benang',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ],
                    [
                        'field' => 'frm-qty[]',
                        'label' => 'Stok Benang',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ]
                ]
            ]);
//            if ($this->form_validation->run() == FALSE) {
//                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
//            }
            $mc = $this->input->post("mc");
            $bng = $this->input->post("frm-bng");
            $tm = $this->input->post("frm-time");
            $qty = $this->input->post("frm-qty");

            $model = new $this->m_global;
//            $check = $model->setTables("product_planning_mesin")->setWheres(["mc_id" => $mc])->getDetail();
            $check = $model->setTables("product_planning_mesin")->setWhereIn("mc_id", $mc)->getData();
            $insert = [];
            $update = [];
            $checksKode = [];
            foreach ($check as $k => $val) {
                $checksKode[$val->mc_id] = "1";
            }

            foreach ($mc as $key => $value) {
                $update[] = [
                    "benang" => $bng[$key],
                    "time" => $tm[$key],
                    "qty" => $qty[$key],
                    "mc_id" => $value
                ];
                if (isset($checksKode[$value])) {
                    continue;
                }
                if ($qty[$key] <= 0)
                    continue;
                $insert[] = [
                    "benang" => $bng[$key],
                    "time" => $tm[$key],
                    "qty" => $qty[$key],
                    "mc_id" => $value
                ];
            }
            if (count($insert) > 0)
                $model->setTables("product_planning_mesin")->saveBatch($insert);
            $model->setTables("product_planning_mesin")->updateBatch($update, "mc_id");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-success', 'type' => 'success')));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}

//