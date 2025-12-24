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

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;

class Kasmasuk extends MY_Controller {

    //put your code here

    protected $val_form = array(
        [
            'field' => 'no_acc',
            'label' => 'No ACC',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
        [
            'field' => 'tanggal',
            'label' => 'Tanggal',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
    );

    public function __construct() {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load modul global
        $this->config->load('additional');
        $this->load->model("m_global");
        $this->load->library("token");
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    public function index() {
        $data['id_dept'] = 'ACCKM';
        $this->load->view('accounting/v_kas_masuk', $data);
    }

    public function list_data() {
        try {
            $data = array();
            $list = new $this->m_global;
            $list->setTables("acc_kas_masuk")->setOrder(["acc_kas_masuk.tanggal" => "desc"])
                    ->setJoins("acc_coa", "acc_coa.kode_coa = acc_kas_masuk.kode_coa", "left")
                    ->setJoins("mst_status", "mst_status.kode = acc_kas_masuk.status", "left")
                    ->setSearch(["no_km", "acc_coa.kode_coa", "partner_nama", "lain2", "transinfo", "acc_kas_masuk.status"])
                    ->setOrders([null, "no_km", "partner_nama", "acc_kas_masuk.tanggal", null, "total_rp", "acc_kas_masuk.status"])
                    ->setSelects(["acc_kas_masuk.*", "acc_coa.nama as nama_coa", "nama_status as status"]);
            $no = $_POST['start'];
            $tanggal = $this->input->post("tanggal");
            $nobukti = $this->input->post("no_bukti");
            $customer = $this->input->post("customer");
            $uraian = $this->input->post("uraian");

            if ($tanggal !== "") {
                $tanggals = explode(" - ", $tanggal);
                $list->setWheres(["date(acc_kas_masuk.tanggal) >=" => $tanggals[0], "date(acc_kas_masuk.tanggal) <=" => $tanggals[1]]);
            }
            if ($nobukti !== "") {
                $list->setWheres(["acc_kas_masuk.no_km LIKE" => "%{$nobukti}%"]);
            }
            if ($customer !== "") {
                $list->setWheres(["partner_nama LIKE" => "%{$customer}%"]);
            }
            if ($uraian !== "") {
                $list->setJoins("acc_kas_masuk_detail abkd", "abkd.kas_masuk_id = acc_kas_masuk.id")
                        ->setGroups(["kas_masuk_id"])->setWheres(["abkd.uraian LIKE" => "%{$uraian}%"]);
            }

            foreach ($list->getData() as $field) {
                $kode_encrypt = encrypt_url($field->no_km);
                $no++;
                $data [] = [
                    $no,
                    "<a href='" . base_url("accounting/kasmasuk/edit/{$kode_encrypt}") . "'>{$field->no_km}</a>",
                    ($field->partner_nama === "") ? $field->lain2 : $field->partner_nama,
                    date("Y-m-d", strtotime($field->tanggal)),
                    $field->kode_coa . " - " . $field->nama_coa,
                    number_format($field->total_rp, 2),
                    $field->status
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll("acc_kas_masuk.id"),
                "recordsFiltered" => $list->getDataCountFiltered(),
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

    public function add() {
        $data['id_dept'] = 'ACCKM';
        $model = new $this->m_global;
//        $data["coas"] = $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
//                        ->setWheres(["level" => 5])->setOrder(["kode_coa" => "asc"])->getData();
        $data["coa"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "kas"])->setOrder(["kode_coa" => "asc"])->getData();
        $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
        $this->load->view('accounting/v_kas_masuk_add', $data);
    }

    public function simpan() {

        try {

            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kodeCoa = $this->input->post("kode_coa");
            $val = $this->val_form;
            if (count($kodeCoa) > 0) {
                $val = array_merge($val, [
                    [
                        'field' => 'uraian[]',
                        'label' => 'Uraian',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ],
                    [
                        'field' => 'kode_coa[]',
                        'label' => 'No ACC',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ],
                    [
                        'field' => 'kurs[]',
                        'label' => 'Kurs',
                        'rules' => ['trim', 'required', 'regex_match[/^\d*\.?\d*$/]'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                    [
                        'field' => 'curr[]',
                        'label' => 'Currency',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ],
                    [
                        'field' => 'nominal[]',
                        'label' => 'Nominal',
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'], ///^-?\d*\.?\d*$/
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ]
                ]);
            }
            $this->form_validation->set_rules($val);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $this->_module->startTransaction();
            $this->_module->lock_tabel("token_increment WRITE,log_history WRITE,main_menu_sub READ,acc_kas_masuk WRITE ,acc_kas_masuk_detail WRITE");
            $coaName = $this->input->post("coa_name");
            $tanggal = $this->input->post("tanggal");
            if (strtolower($coaName) === 'kas valas') {
                if (!$nokm = $this->token->noUrut('kas_masuk_valas', date('ym', strtotime($tanggal)), true)->generate('MKVH', '/%03d')->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . getRomawi(date('m', strtotime($tanggal)) . "/"))->get()) {
                    throw new \Exception("No Kas Masuk Valas tidak terbuat", 500);
                }
            } else {
                if (!$nokm = $this->token->noUrut('kas_masuk', date('ym', strtotime($tanggal)), true)->generate('MKBRH', '/%03d')->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . getRomawi(date('m', strtotime($tanggal)) . "/"))->get()) {
                    throw new \Exception("No Kas Masuk tidak terbuat", 500);
                }
            }


            $now = date("Y-m-d H:i:s");
            $header = [
                "no_km" => $nokm,
                "create_date" => $now,
                "tanggal" => $tanggal,
                "kode_coa" => $this->input->post("no_acc"),
                "partner_id" => $this->input->post("partner"),
                "partner_nama" => $this->input->post("partner_name"),
                "lain2" => $this->input->post("lain_lain"),
                "transinfo" => $this->input->post("transaksi"),
                "total_rp" => 0,
            ];
            $model = new $this->m_global;
            $headID = $model->setTables("acc_kas_masuk")->save($header);
            $detail = [];
            if (count($kodeCoa) > 0) {
                $kurs = $this->input->post("kurs");
                $curr = $this->input->post("curr");
                $nominal = $this->input->post("nominal");
                $giro = $this->input->post("giro_keluar_detail");
                $totalRp = 0;
                foreach ($this->input->post("uraian") as $key => $value) {
                    $nom = str_replace(",", "", $nominal[$key]);
                    $totalRp += $nom;
                    $detail [] = [
                        "kas_masuk_id" => $headID,
                        "tanggal" => $this->input->post("tanggal"),
                        "no_km" => $nokm,
                        "uraian" => $value,
                        "kode_coa" => $kodeCoa[$key],
                        "kurs" => $kurs[$key],
                        "currency_id" => $curr[$key],
                        "nominal" => $nom,
                        "giro_keluar_detail_id" => $giro[$key] ?? 0,
                        "row_order" => ($key + 1)
                    ];
                }
                $model->setTables("acc_kas_masuk")->setWheres(["id" => $headID])->update(["total_rp" => $totalRp]);
                $model->setTables("acc_kas_masuk_detail")->saveBatch($detail);
            }


            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }

            $this->_module->gen_history_new($sub_menu, $nokm, 'create', "DATA -> " . logArrayToString("; ", $header) . "\n Detail -> " . logArrayToString("; ", $detail), $username);
            $url = site_url("accounting/kasmasuk/edit/" . encrypt_url($nokm));
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'url' => $url)));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function edit($id) {
        try {
            $data["user"] = (object) $this->session->userdata('nama');
            $data["id"] = $id;
            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $data['datas'] = $model->setTables("acc_kas_masuk akm")->setWheres(["no_km" => $kode])
//                            ->setSelects(["akm.no_km,akm.tanggal,akm.kode_coa,akm.partner_id,akm.partner_nama,akm.lain2,akm.transinfo,akm.total_rp,id,status"])
                            ->setOrder(["tanggal" => "desc"])->getDetail();
            if (!$data['datas']) {
                show_404();
            }
            $data['data_detail'] = $model->setTables("acc_kas_masuk_detail akmd")->setWheres(["no_km" => $kode])
                    ->setJoins("acc_coa", "acc_coa.kode_coa = akmd.kode_coa")
                    ->setJoins("currency_kurs", "currency_kurs.id = currency_id")
                    ->setOrder(["tanggal" => "desc", "row_order" => "asc"])
                    ->setSelects(["akmd.no_km,akmd.tanggal,akmd.kode_coa,akmd.uraian,akmd.kurs,akmd.currency_id,akmd.nominal,giro_keluar_detail_id"])
                    ->setSelects(["acc_coa.nama as nama_coa", "currency_kurs.currency as curr"])
                    ->getData();
//            $data["coas"] = $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
//                            ->setWheres(["level" => 5])->setOrder(["kode_coa" => "asc"])->getData();
            $data["coa"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "kas"])->setOrder(["kode_coa" => "asc"])->getData();
            $data['id_dept'] = 'ACCKM';
            $data["jurnal"] = $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $data['datas']->jurnal])->getDetail();
            $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
            $this->load->view('accounting/v_kas_masuk_edit', $data);
        } catch (Exception $ex) {
            
        }
    }

    public function get_view_tukar_tunai() {
        $giro = $this->input->post("trx");
        $view = $this->load->view('accounting/modal/v_tukar_tunai', ["giro" => $giro], true);
        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['data' => $view]));
    }

    public function list_tarik_tunai() {
        try {

            $giro = $this->input->post("giro");
            $giro = explode(",", $giro);

            $data = array();
            $model = new $this->m_global;
            $now = date("Y-m-d H:i:s");
            $days90 = date("Y-m-d", strtotime("-90 days", strtotime($now)));
            $model->setTables("acc_giro_keluar agk")->setJoins("acc_giro_keluar_detail agkd", "agk.no_gk = agkd.no_gk")
                    ->setOrders([null, "agk.no_gk", "partner_nama", "agkd.tgl_jt", "nominal"])
                    ->setSearch(["agk.no_gk", "partner_nama", "transinfo", "bank", "no_bg", "agk.lain2"])
                    ->setWheres([
                        "agkd.nominal >" => 0,
                        "agk.no_bk2" => '',
                        "agk.pindah" => 1,
                        "agkd.cair" => 1,
                        "agkd.tgl_jt >=" => $days90,
                        "agkd.tgl_jt <=" => date("Y-m-d H:i:s"),
                    ])
                    ->setOrder(["agk.partner_nama" => "asc", "agkd.bank" => "asc", "agkd.nominal" => "asc", "agkd.no_bg" => "asc"])
            ;
            if (count($giro) > 0) {
                $ff = implode("','", $giro);
                $model->setWhereRaw("agkd.id not in ('{$ff}')");
            }
            $list = $model->setSelects(["agk.no_gk,agk.partner_nama,transinfo", "bank", "no_bg", "nominal", "tgl_jt", "agkd.id", "if(partner_nama = '',lain2,partner_nama) as partner"]);
            $no = $_POST['start'];
            foreach ($list->getData() as $field) {
                $no++;
                $data[] = [
                    $field->id,
                    $field->no_gk,
                    $field->partner,
                    $field->tgl_jt,
                    $field->nominal
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll(),
                "recordsFiltered" => $list->getDataCountFiltered(),
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

    public function add_data_from_tarik_tunai() {
        try {
            $no = $this->input->post("no");
            $model = new $this->m_global;

            $data = $model->setTables("acc_giro_keluar_detail agkd")->setJoins("acc_giro_keluar agk", "agkd.no_gk = agk.no_gk")
                            ->setJoins("currency_kurs", "currency_kurs.id = agkd.currency_id")
                            ->setSelects(["agkd.nominal,agkd.no_gk,agkd.kode_coa", "agkd.id,if(partner_nama = '',agk.lain2,partner_nama) as lain"])
                            ->setSelects(["agkd.currency_id as agk_curr,agkd.kurs", "currency_kurs.currency as curr", "transinfo"])
                            ->setWhereIn("agkd.id", $no)->setOrder(["agkd.no_gk" => "asc"])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function update($id) {
        $pin = false;
        try {
            $kode = decrypt_url($id);
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $kodeCoa = $this->input->post("kode_coa");
            if (count($kodeCoa) > 0) {
                $this->val_form = array_merge($this->val_form, [
                    [
                        'field' => 'uraian[]',
                        'label' => 'Uraian',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ],
                    [
                        'field' => 'kode_coa[]',
                        'label' => 'No ACC',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ],
                    [
                        'field' => 'kurs[]',
                        'label' => 'Kurs',
                        'rules' => ['trim', 'required', 'regex_match[/^\d*\.?\d*$/]'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                    [
                        'field' => 'curr[]',
                        'label' => 'Currency',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ],
                    [
                        'field' => 'nominal[]',
                        'label' => 'Nominal',
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'], ///^-?\d*\.?\d*$/
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ]
                ]);
            }
            $this->form_validation->set_rules($this->val_form);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            if ($this->input->post('partner') === "" && $this->input->post('lain_lain') === "") {
                throw new \Exception("Kepada / Lain - Lain Harus diisi salah satu", 500);
            }
            $tanggal = $this->input->post("tanggal");
            $model = new $this->m_global;
            $dt = $model->setTables("acc_kas_masuk")->setWheres(["no_km" => $kode])->getDetail();
            if (!$dt) {
                throw new \Exception("Data Tidak ditemukan", 500);
            }
            if ($dt->status != "draft") {
                throw new \Exception("Status Harus Dalam Posisi Draft", 500);
            }
            //validasi
            $blnDok = date("n", strtotime($dt->tanggal));
            $blnform = date("n", strtotime($tanggal));
            $blnskrg = date("n");
            $bbln = $blnskrg - $blnDok;
            if ($blnform != $blnDok) {
                throw new \Exception("Edit Tidak bisa dilakukan karena berbeda Bulan", 500);
            }
//            $this->validasiPin($pin, "Edit Data Hanya bisa dilakukan Oleh Supervisor", $dt->tanggal);

            $header = [
                "tanggal" => $tanggal,
                "kode_coa" => $this->input->post("no_acc"),
                "partner_id" => $this->input->post("partner"),
                "partner_nama" => $this->input->post("partner_name"),
                "lain2" => $this->input->post("lain_lain"),
                "transinfo" => $this->input->post("transaksi"),
                "total_rp" => $this->input->post("total_nominal")
            ];
            $detail = [];
            $this->_module->startTransaction();
            $model = new $this->m_global;
            $model->setTables("acc_kas_masuk")->setWheres(["no_km" => $kode])->update($header);
            $model->setTables("acc_kas_masuk_detail")->setWheres(["no_km" => $kode])->delete();

            if (count($kodeCoa) > 0) {


                $ids = $this->input->post("ids");
                $kurs = $this->input->post("kurs");
                $curr = $this->input->post("curr");
                $nominal = $this->input->post("nominal");
                $giro = $this->input->post("giro_keluar_detail");
                $totalRp = 0;

                foreach ($this->input->post("uraian") as $key => $value) {
                    $nom = str_replace(",", "", $nominal[$key]);
                    $totalRp += $nom;
                    $detail [] = [
                        "kas_masuk_id" => $ids,
                        "tanggal" => $this->input->post("tanggal"),
                        "no_km" => $kode,
                        "uraian" => $value,
                        "kode_coa" => $kodeCoa[$key],
                        "kurs" => $kurs[$key],
                        "currency_id" => $curr[$key],
                        "nominal" => $nom,
                        "giro_keluar_detail_id" => $giro[$key] ?? 0,
                        "row_order" => ($key + 1)
                    ];
                }
                $header["total_rp"] = $totalRp;
                $model->setTables("acc_kas_masuk")->setWheres(["no_km" => $kode])->update($header);
                $model->setTables("acc_kas_masuk_detail")->saveBatch($detail);
                $nogk = explode(",", $this->input->post("trx"));
                $model->setTables("acc_giro_keluar")->setWhereIn("no_gk", $nogk)->update(["no_bk2" => $kode]);
            }
            $log = [
                "asal_data" => [
                    "DATA" => json_decode($this->input->post("head"), true),
                    "DETAIL" => json_decode($this->input->post("detail"), true)
                ],
                "perubahan" => [
                    "DATA" => $header,
                    "DETAIL" => $detail
                ]
            ];
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }

            $log = "Asal Data : DATA -> " . logArrayToString("; ", json_decode($this->input->post("head"), true));
            $log .= "\nDETAIL -> " . logArrayToString("; ", json_decode($this->input->post("detail"), true));
            $log .= "\n";
            $log .= "Perubahan : DATA -> " . logArrayToString("; ", $header);
            $log .= "\nDETAIL -> " . logArrayToString("; ", $detail);

            $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);
            $url = site_url("accounting/kasmasuk/edit/{$id}");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'url' => $url)));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "pin" => $pin)));
        }
    }

    public function print() {
        try {
            $id = $this->input->post("no");
            $kode = decrypt_url($id);
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $users = $this->session->userdata('nama');
            $connector = new DummyPrintConnector();
            $printer = new Printer($connector);

            $printers = $this->session->userdata('printer');
            if ($printers === null) {
                throw new \exception("Printer Direct belum ditentukan, silakan pilih pada tab atas", 500);
            }
            $printers = json_decode($printers);

            $model = new $this->m_global;
            $head = $model->setTables("acc_kas_masuk")->setJoins("acc_coa", "acc_coa.kode_coa = acc_kas_masuk.kode_coa")
                            ->setSelects(["acc_kas_masuk.*", "acc_coa.nama as nama_coa"])
                            ->setWheres(["no_km" => $kode])->getDetail();
            if (!$head) {
                throw new \exception("Data No Kas Keluar {$kode} tidak ditemukan", 500);
            }

            $buff = $printer->getPrintConnector();
            $buff->write("\x1bC" . chr(34));
            $buff->write("\x1bM");
            $tanggal = date("d-m-Y", strtotime($head->tanggal));
            $printer->text(str_pad("Tanggal : {$tanggal}", 67));

            $printer->text(str_pad("No : {$head->no_km}", 21));
            $printer->selectPrintMode();
            $printer->feed();
            $printer->feed();
            $printer->text(str_pad("", 25));
            $buff->write("\x1bE" . chr(1));
            $printer->text(str_pad("BUKTI KAS MASUK (BKM)", 20));
            $buff->write("\x1bF" . chr(0));
            $printer->feed();
            $printer->text(str_pad("", 25));
            $buff->write("\x1bg" . chr(1));
            $printer->text(str_pad($head->nama_coa, 45, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("", 1));
            $customer = str_split(trim(preg_replace('/\s+/', ' ', "Dari : {$head->partner_nama}")), 33);
            foreach ($customer as $key => $value) {
                if ($key > 0) {
                    $printer->text(str_pad("", 84));
                }
                $printer->text(str_pad(trim($value), 33, " ", STR_PAD_RIGHT));
            }
            $printer->feed();
            $buff->write("\x1bM");
            $printer->text(str_pad("", 30));
            $buff->write("\x1bg" . chr(1));
            $printer->text(str_pad("No Acc (Debet) : {$head->kode_coa}", 30));
            $printer->text(str_pad("", 16));
            $lain2 = str_split(trim(preg_replace('/\s+/', ' ', "LAIN-LAIN :{$head->lain2}")), 33);
            foreach ($lain2 as $key => $value) {
                if ($key > 0) {
                    $printer->text(str_pad("", 86));
                }
                $printer->text(str_pad(trim($value), 33, " ", STR_PAD_RIGHT));
            }
            $printer->feed();
            $printer->feed();
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad("Untuk transaksi : {$head->transinfo}", 120));
            $printer->setUnderline(Printer::UNDERLINE_NONE);
            $printer->feed();
            $detail = $model->setTables("acc_kas_masuk_detail")
                            ->setJoins("currency_kurs", "currency_kurs.id = currency_id")
                            ->setWheres(["kas_masuk_id" => $head->id])
                            ->setSelects(["acc_kas_masuk_detail.*", "currency_kurs.currency as curr"])->getData();
            $printer->selectPrintMode();
            $buff->write("\x1bX" . chr(15));
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad("No", 5));
            $printer->text(str_pad("Uraian", 70, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("No Acc(Kredit)", 20, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Kurs", 10, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Curr", 10, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Nominal", 20, " ", STR_PAD_LEFT));
            $printer->feed();
            $printer->setUnderline(Printer::UNDERLINE_NONE);
            $totals = 0;
            $no = 0;

            foreach ($detail as $keys => $values) {
                $no += 1;
                $totals += $values->nominal;
                $noo = array($no);
                $kodecoa = array($values->kode_coa);
                $kurs = array($values->kurs);
                $curr = array($values->curr);
                $nominal = array($values->nominal);
                $uraian = str_split($values->uraian, 70);
                foreach ($uraian as $key => $value) {
                    $value = trim($value);
                    $uraian[$key] = $value;
                }

                $jumlahBaris = count($uraian);
                for ($i = 0; $i < $jumlahBaris; $i++) {
                    $line = (isset($noo[$i])) ? str_pad($noo[$i], 5) : str_pad("", 5);
                    $line .= (isset($uraian[$i])) ? str_pad($uraian[$i], 70, " ", STR_PAD_RIGHT) : str_pad("", 70, " ", STR_PAD_RIGHT);
                    $line .= (isset($kodecoa[$i])) ? str_pad($kodecoa[$i], 20, " ", STR_PAD_BOTH) : str_pad("", 20, " ", STR_PAD_BOTH);
                    $line .= (isset($kurs[$i])) ? str_pad(number_format($kurs[$i], 2), 10, " ", STR_PAD_BOTH) : str_pad("", 10, " ", STR_PAD_BOTH);
                    $line .= (isset($curr[$i])) ? str_pad($curr[$i], 10, " ", STR_PAD_BOTH) : str_pad("", 10, " ", STR_PAD_BOTH);
                    $line .= (isset($nominal[$i])) ? str_pad(number_format($nominal[$i], 2), 20, " ", STR_PAD_LEFT) : str_pad("", 20, " ", STR_PAD_LEFT);
                    $printer->text($line . "\n");
                }
            }
            $printer->feed();
            $printer->text(str_pad("", 95));
            $printer->text(str_pad("Total", 10, " ", STR_PAD_BOTH));
            $printer->text(str_pad(number_format($totals, 2), 30, " ", STR_PAD_LEFT));
            $printer->feed();
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad("", 135));
            $printer->setUnderline(Printer::UNDERLINE_NONE);
            $printer->feed();
            $printer->feed();
            $printer->selectPrintMode();
            $buff->write("\x1bM");

            $printer->text(str_pad("Diinput oleh:", 31, " ", STR_PAD_LEFT));
            $printer->text(str_pad("Mengetahui:", 31, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Diterima oleh:", 31, " ", STR_PAD_RIGHT));
            $printer->feed();
            $printer->feed();
            $printer->feed();
            $printer->feed();
            $printer->text(str_pad("(___________)", 31, " ", STR_PAD_LEFT));
            $printer->text(str_pad("(___________)", 31, " ", STR_PAD_BOTH));
            $printer->text(str_pad("(___________)", 31, " ", STR_PAD_RIGHT));

            $buff->write("\x0c");
            $datas = $connector->getData();
            $printer->close();
            $client = new GuzzleHttp\Client();
            $resp = $client->request("POST", $this->config->item('url_web_print'), [
                "form_params" => [
                    "data" => $datas,
                    "printer" => "\\\\{$printers->ip_share}\\{$printers->nama_printer_share}"
                ]
            ]);

            $this->_module->gen_history_new($sub_menu, $kode, "edit", "Melakukan Print Dokumen.", $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $printer->close();
        }
    }

    public function update_status($id) {
        $pin = false;
        try {
            $kode = decrypt_url($id);
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $status = $this->input->post("status");
            $status = strtolower($status);
            $model = new $this->m_global;
            $head = $model->setTables("acc_kas_masuk")->setJoins("acc_kas_masuk_detail", "acc_kas_masuk.id = kas_masuk_id", "left")
                            ->setJoins("currency_kurs", "currency_kurs.id = currency_id", "left")
                            ->setSelects(["acc_kas_masuk.*", "currency_kurs.currency,currency_kurs.kurs", "kas_masuk_id"])
                            ->setWheres(["acc_kas_masuk.no_km" => $kode])->getDetail();

            if (!$head) {
                throw new \exception("Data No Kas Masuk {$kode} tidak ditemukan", 500);
            }
            if (!$head->kas_masuk_id) {
                throw new \exception("Data Detail Harus Terisi", 500);
            }

            $this->_module->startTransaction();
            $this->_module->lock_tabel("token_increment WRITE,acc_kas_masuk WRITE,acc_kas_masuk_detail WRITE,log_history WRITE,setting READ"
                    . ",main_menu_sub READ,acc_jurnal_entries_items WRITE,acc_jurnal_entries WRITE,currency_kurs READ,acc_giro_keluar_detail WRITE, acc_giro_keluar WRITE");
            $model->update(["status" => $status]);
            switch ($status) {
                case "confirm":
                    if ($head->status !== "draft") {
                        throw new \exception("Data No Kas Masuk {$kode} dalam status {$head->status}", 500);
                    }
                    $jurnalDB = new $this->m_global;
                    $model = clone $jurnalDB;

                    $poId = $model->setTables("acc_kas_masuk_detail")->setSelects(["GROUP_CONCAT(giro_keluar_detail_id) as gids"])->setWheres(["kas_masuk_id" => $head->id, "giro_keluar_detail_id <>" => "0"])
                            ->getDetail();
                    $nogk = [];
                    if ($poId->gids !== null) {
                        $checkGiro = $model->setTables("acc_giro_keluar")->setJoins("acc_giro_keluar_detail", "acc_giro_keluar.id = giro_keluar_id")
                                        ->setWhereRaw("acc_giro_keluar_detail.id in ({$poId->gids})")
                                        ->setSelects(["acc_giro_keluar_detail.nominal,acc_giro_keluar.no_bk2,acc_giro_keluar.pindah,acc_giro_keluar_detail.cair",
                                            "acc_giro_keluar.no_gk"])->getData();

                        foreach ($checkGiro as $key => $value) {
                            $nogk[] = $value->no_gk;
                            switch (true) {
                                case $value->no_bk2 != "":
                                    throw new \exception("Data Giro {$value->no_gk} Sudah mempunyai no Kas Masuk {$value->no_bk2}", 500);
                                    break;
                                case $value->nominal < 1:
                                    throw new \exception("Data Giro {$value->no_gk} Belum Memiliki Nominal", 500);
                                    break;
                                case $value->cair == 0:
                                    throw new \exception("Data Giro {$value->no_gk} Belum Cair", 500);
                                    break;
                                case $value->pindah != "1":
                                    throw new \exception("Data Giro {$value->no_gk} Belum Pindah", 500);
                                    break;
                            }
                        }
                    }

                    if ($head->jurnal !== "") {
                        $jurnal = $head->jurnal;
                        $stt = "edit";
                    } else {
                        if (!$jurnal = $this->token->noUrut("jurnal_acc_km", date('y', strtotime($head->tanggal)) . '/' . date('m', strtotime($head->tanggal)), true)
                                        ->generate("KM/", '/%05d')->get()) {
                            throw new \Exception("No jurnal tidak terbuat", 500);
                        }
                        $stt = "create";
                    }
                    $partner = (strlen($head->partner_nama) > 1) ? $head->partner_nama : $head->lain2;
                    $jurnalItems = [];

                    $jurnalData = ["kode" => $jurnal, "periode" => date("Y/m", strtotime($head->tanggal)),
                        "origin" => "{$kode}", "status" => "posted", "tanggal_dibuat" => $head->tanggal, "tipe" => "KM",
                        "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => "{$partner}"];

                    $items = $model->setTables("acc_kas_masuk_detail")->setJoins("currency_kurs", "currency_kurs.id = currency_id", "left")
                                    ->setSelects(["acc_kas_masuk_detail.*", "currency_kurs.currency"])
                                    ->setWheres(["kas_masuk_id" => $head->id])->getData();
                    $textKurs = "";
                    $valas = false;
                    if (strpos($kode, "MKVH") !== false) {
                        $valas = true;
                        $textKurs = " ({$head->total_rp}{$head->currency} kurs : " . number_format($items[0]->kurs, 2) . ")";
                    }
                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "{$head->transinfo}{$textKurs}",
                        "reff_note" => "",
                        "partner" => ($head->partner_id ?? ""),
                        "kode_coa" => $head->kode_coa,
                        "posisi" => "D",
                        "nominal_curr" => $head->total_rp,
                        "kurs" => $items[0]->kurs,
                        "kode_mua" => $head->currency,
                        "nominal" => ($head->total_rp * $items[0]->kurs),
                        "row_order" => 1
                    );

                    foreach ($items as $key => $item) {
                        $textKurs = "";
                        if ($valas) {
                            $textKurs = " ({$item->nominal}{$item->currency} kurs : " . number_format($item->kurs, 2) . ")";
                        }
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "{$item->uraian}{$textKurs}",
                            "reff_note" => "",
                            "partner" => ($head->partner_id ?? ""),
                            "kode_coa" => $item->kode_coa,
                            "posisi" => "C",
                            "nominal_curr" => $item->nominal,
                            "kurs" => $item->kurs,
                            "kode_mua" => $item->currency,
                            "nominal" => ($item->nominal * $item->kurs),
                            "row_order" => (count($jurnalItems) + 1)
                        );
                    }

                    if ($head->jurnal !== "") {
                        $jurnalDB->setTables("acc_jurnal_entries")->setWheres(["kode" => $jurnal])->update($jurnalData);
                        $jurnalDB->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $jurnal])->delete();
                    } else {
                        $jurnalDB->setTables("acc_jurnal_entries")->save($jurnalData);
                        $model->setTables("acc_kas_masuk")->setWheres(["id" => $head->id])->update(["jurnal" => $jurnal]);
                        $this->_module->gen_history_new($sub_menu, $kode, 'edit', "No Jurnal : {$jurnal}", $username);
                    }

                    if (count($nogk) > 0) {
                        $model->setTables("acc_giro_keluar")->setWhereIn("no_gk", $nogk)->update(["no_bk2" => $head->no_km]);
                    }

                    $jurnalDB->setTables("acc_jurnal_entries_items")->saveBatch($jurnalItems);
                    $log = "Header -> " . logArrayToString("; ", $jurnalData);
                    $log .= "\nDETAIL -> " . logArrayToString("; ", $jurnalItems);
                    $this->_module->gen_history_new("jurnal_entries", $jurnal, $stt, $log, $username);

                    break;

                case "draft":
                    if ($head->status !== "cancel") {
                        throw new \exception("Data No Kas Masuk {$kode} dalam status {$head->status}", 500);
                    }
                    $this->validasiPin($pin, "Simpan Draft Hanya bisa dilakukan Oleh Supervisor", $head->tanggal);
                    break;

                default:
                    $this->validasiPin($pin, "Batal / Cancel Data Hanya bisa dilakukan Oleh Supervisor", $head->tanggal);

                    $poId = $model->setTables("acc_kas_masuk_detail")->setSelects(["GROUP_CONCAT(giro_keluar_detail_id) as gids"])->setWheres(["kas_masuk_id" => $head->id])
                            ->getDetail();
                    if ($poId->gids !== null) {
                        $model->setTables("acc_giro_keluar")->setWhereRaw("id in (select giro_keluar_id from acc_giro_keluar_detail where id in ({$poId->gids}))")->update(["no_bk2" => ""]);
                    }

                    $lunas = $model->setTables("acc_kas_masuk_detail")->setWheres(["kas_masuk_id" => $head->id, "lunas" => 1])
                            ->getDetail();
                    if ($lunas) {
                        throw new \exception("Tidak Bisa Cancel / Batal. Item sudah sudah masuk pelunasan", 500);
                    }

                    $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $head->jurnal])->update(["status" => "unposted"]);
                    $this->_module->gen_history_new("jurnal_entries", $head->jurnal, 'edit', "Merubah Status Ke unposted dari Kas Masuk", $username);

                    break;
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            $this->_module->gen_history_new($sub_menu, $kode, 'edit', "status menjadi {$status}", $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "pin" => $pin)));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    protected function validasiPin(&$pin, $pesanError, $tanggalDok) {
        $users = (object) $this->session->userdata('nama');
        $blnDok = date("n", strtotime($tanggalDok));
        $blnskrg = date("n");
        $bbln = $blnskrg - $blnDok;
        if ($bbln === 1) {
            $model = new $this->m_global();
            $pinDate = $model->setTables("setting")->setWheres(["setting_name" => "pin_date_acc", "status" => "1"])->setSelects(["value"])->getDetail();
            if (date("j") >= (int) $pinDate->value) {

                if (!in_array($users->level, ["Super Administrator", "Supervisor"])) {
                    throw new \Exception("{$pesanError}", 500);
                }
                $pin = $this->session->userdata('pin');
                if (!$pin) {
                    $pin = true;
                    throw new \Exception("masukan pin", 200);
                }
                $this->session->unset_userdata('pin');
            }
        } else if ($bbln > 1) {
            if (!in_array($users->level, ["Super Administrator", "Supervisor"])) {
                throw new \Exception("{$pesanError}", 500);
            }
            $pin = $this->session->userdata('pin');
            if (!$pin) {
                $pin = true;
                throw new \Exception("masukan pin", 200);
            }
            $this->session->unset_userdata('pin');
        }
//        else if ($bbln > 1) {
//            if (!in_array($users->level, ["Super Administrator", "Supervisor"])) {
//                throw new \Exception("{$pesanError}", 500);
//            }
//            $pin = $this->session->userdata('pin');
//            if (!$pin) {
//                $pin = true;
//                throw new \Exception("masukan pin", 200);
//            }
//            $this->session->unset_userdata('pin');
//        }
    }
}
