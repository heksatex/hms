<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Bankmasuk
 *
 * @author RONI
 */
require FCPATH . 'vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;

class Bankkeluar extends MY_Controller {

    //put your code here
    protected $valForm = [
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
        [
            'field' => 'jenis_transaksi',
            'label' => 'Jenis Transaksi',
            'rules' => ['required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ]
    ];
    protected $jenisTransaksi = [
        "transfer" => "Transfer",
        "inkaso" => "Inkaso",
        "kliring" => "kliring",
        "lain-lain" => "Lain-Lain"
    ];

    public function __construct() {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load modul global
        $this->config->load('additional');
        $this->load->model("m_global");
        $this->load->library("token");
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    public function check_partner_lain(): bool {
        if (empty($this->input->post('partner')) && empty($this->input->post('lain_lain'))) {
            $this->form_validation->set_message('check_partner_lain', 'false');
            return false;
        }
        return true;
    }

    public function index() {
        $data['id_dept'] = 'ACCBK';
        $this->load->view('accounting/v_bank_keluar', $data);
    }

    public function list_data() {
        try {
            $data = array();
            $list = new $this->m_global;
            $list->setTables("acc_bank_keluar")->setOrder(["acc_bank_keluar.tanggal" => "desc"])
                    ->setJoins("acc_coa", "acc_coa.kode_coa = acc_bank_keluar.kode_coa", "left")
                    ->setJoins("mst_status", "mst_status.kode = acc_bank_keluar.status", "left")
                    ->setSearch(["no_bk", "acc_coa.kode_coa", "partner_nama", "lain2", "transinfo"])
                    ->setOrders([null, "no_bk", "partner_nama", "acc_bank_keluar.tanggal", null, null, "total_rp"])
                    ->setSelects(["acc_bank_keluar.*", "acc_coa.nama as nama_coa", "nama_status as status"]);

            $no = $_POST['start'];
            $tanggal = $this->input->post("tanggal");
            $nobukti = $this->input->post("no_bukti");
            $customer = $this->input->post("customer");
            $uraian = $this->input->post("uraian");

            if ($tanggal !== "") {
                $tanggals = explode(" - ", $tanggal);
                $list->setWheres(["date(acc_bank_keluar.tanggal) >=" => $tanggals[0], "date(acc_bank_keluar.tanggal) <=" => $tanggals[1]]);
            }
            if ($nobukti !== "") {
                $list->setWheres(["acc_bank_keluar.no_bk LIKE" => "%{$nobukti}%"]);
            }
            if ($customer !== "") {
                $list->setWheres(["partner_nama LIKE" => "%{$customer}%"]);
            }
            if ($uraian !== "") {
                $list->setJoins("acc_bank_keluar_detail abkd", "abkd.bank_keluar_id = acc_bank_keluar.id")
                        ->setGroups(["bank_keluar_id"])->setWheres(["abkd.uraian LIKE" => "%{$uraian}%"]);
            }
            foreach ($list->getData() as $field) {
                $kode_encrypt = encrypt_url($field->no_bk);
                $no++;
                $data [] = [
                    $no,
                    "<a href='" . base_url("accounting/bankkeluar/edit/{$kode_encrypt}") . "'>{$field->no_bk}</a>",
                    ($field->partner_nama === "") ? $field->lain2 : $field->partner_nama,
                    date("Y-m-d", strtotime($field->tanggal)),
                    $field->kode_coa . " - " . $field->nama_coa,
                    $field->transinfo,
                    number_format($field->total_rp, 2),
                    $field->status
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll("acc_bank_keluar.id"),
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
        $data['id_dept'] = 'ACCBK';
        $model = new $this->m_global;
        $data["coas"] = $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
                        ->setWheres(["level" => 5])->setOrder(["kode_coa" => "asc"])->getData();
        $data["coa"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "bank"])->setOrder(["nama"=>"asc"])->getData();
        $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
        $this->load->view('accounting/v_bank_keluar_add', $data);
    }

    public function get_view_bukti_giro() {
        $no = $this->input->post("no");
        $view = $this->load->view('accounting/modal/v_bukti_giro_2', ["no" => json_encode($no, true)], true);
        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['data' => $view]));
    }

    public function list_bukti_giro() {
        try {
            $nos = $this->input->post("no");
            $data = array();
            $list = new $this->m_global;
            $now = date("Y-m-d");
            $days90 = date("Y-m-d", strtotime("-90 days", strtotime($now)));
            $list->setTables("acc_giro_keluar agk")
                    ->setJoins("acc_giro_keluar_detail agkd", "agk.no_gk = agkd.no_gk")
                    ->setJoins("acc_coa", "acc_coa.kode_coa = agkd.kode_coa")
                    ->setOrder(["agk.partner_nama" => "asc", "agkd.bank" => "asc", "agkd.nominal" => "asc", "agkd.no_bg" => "asc"])
                    ->setSelects(["agkd.*,partner_nama,partner_id,lain2", "acc_coa.nama coa_nama"])
                    ->setSearch(["agkd.bank", "agkd.no_rek", "agkd.no_bg", "agkd.no_gk", "lain2"])
                    ->setWheres(["agkd.tgl_jt >=" => "{$days90} 00:00:00", "agkd.tgl_jt <=" => "{$now} 23:59:59"])
                    ->setWheres(["agkd.nominal >" => 0, "agkd.cair" => 0])
                    ->setWhereRaw("agkd.id not IN (select giro_keluar_detail_id from acc_bank_keluar_detail gmd join acc_bank_keluar gm on (gm.id = bank_keluar_id and gm.status <> 'cancel'))");
            if ($nos !== "null") {
                $nos = json_decode($nos, true);
                $nos = implode("','", $nos);
                $list->setWhereRaw("agkd.id not in('{$nos}')");
            }
            $no = $_POST['start'];
            foreach ($list->getData() as $field) {
                $no++;
                $data [] = [
                    $field->id,
                    $field->no_gk,
                    ($field->partner_nama === "") ? $field->lain2 : $field->partner_nama,
                    $field->bank,
                    $field->no_bg,
                    date("Y-m-d", strtotime($field->tgl_jt)),
                    number_format($field->nominal, 2)
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

    public function simpan() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $val = [
                [
                    'field' => 'tanggal',
                    'label' => 'Tanggal',
                    'rules' => ['trim', 'required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih'
                    ]
                ]
            ];
            $kodeCoa = $this->input->post("kode_coa");
            if (count($kodeCoa) > 0) {
                $val = array_merge($val, [
                    [
                        'field' => 'bank[]',
                        'label' => 'Bank',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ],
                    [
                        'field' => 'norek[]',
                        'label' => 'No Rek',
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
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*\.?\d*$/]'],
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

            $tanggal = $this->input->post("tanggal");
            $this->_module->lock_tabel("token_increment WRITE,acc_bank_keluar WRITE,log_history WRITE,main_menu_sub READ,acc_bank_keluar_detail WRITE");
            if (!$nobk = $this->token->noUrut('bank_keluar', date('ym', strtotime($tanggal)), true)->generate('BBKH', '/%03d')
                            ->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . getRomawi(date('m', strtotime($tanggal)) . "/"))->get()) {
                throw new \Exception("No Bank Keluar tidak terbuat", 500);
            }
            $now = date("Y-m-d H:i:s");
            $header = [
                "no_bk" => $nobk,
                "create_date" => $now,
                "tanggal" => $tanggal,
                "kode_coa" => $this->input->post("no_acc"),
                "partner_id" => $this->input->post("partner"),
                "partner_nama" => $this->input->post("partner_name"),
                "lain2" => $this->input->post("lain_lain"),
                "transinfo" => $this->input->post("transaksi"),
                "jenis_transaksi" => $this->input->post("jenis_transaksi"),
                "total_rp" => 0
            ];
            $detail = [];
            $model = new $this->m_global;
            $headID = $model->setTables("acc_bank_keluar")->save($header);
            if (count($kodeCoa) > 0) {
                $kurs = $this->input->post("kurs");
                $curr = $this->input->post("curr");
                $nominal = $this->input->post("nominal");
                $nobg = $this->input->post("nobg");
                $bank = $this->input->post("bank");
                $norek = $this->input->post("norek");
                $tglcair = $this->input->post("tglcair");
                $uraian = $this->input->post("uraian");
                $giroID = $this->input->post("giro_keluar_detail");
                $totalRp = 0;

                foreach ($this->input->post("tgljt") as $key => $value) {
                    $totalRp += $nominal[$key];
                    $detail [] = [
                        "no_bk" => $nobk,
                        "uraian" => $uraian[$key],
                        "bank_keluar_id" => $headID,
                        "tgl_cair" => $tglcair[$key],
                        "tgl_jt" => $value,
                        "kode_coa" => $kodeCoa[$key],
                        "kurs" => $kurs[$key],
                        "currency_id" => $curr[$key],
                        "nominal" => $nominal[$key],
                        "no_bg" => $nobg[$key],
                        "bank" => $bank[$key],
                        "no_rek" => $norek[$key],
                        "tanggal" => $tanggal,
                        "giro_keluar_detail_id" => $giroID[$key],
                        "row_order" => ($key + 1)
                    ];
                }
                $model->setTables("acc_bank_keluar")->setWheres(["id" => $headID])->update(["total_rp" => $totalRp]);
                $model->setTables("acc_bank_keluar_detail")->saveBatch($detail);
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $nobk, 'create', "DATA -> " . logArrayToString("; ", $header) . "\n Detail -> " . logArrayToString("; ", $detail), $username);
            $url = site_url("accounting/bankkeluar/edit/" . encrypt_url($nobk));
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

    public function bg() {
        try {
            $no = $this->input->post("no");
            $model = new $this->m_global;

            $data = $model->setTables("acc_giro_keluar_detail agkd")->setJoins("acc_giro_keluar agk", "agkd.no_gk = agk.no_gk")
                            ->setJoins("currency_kurs", "currency_kurs.id = agkd.currency_id")
                            ->setSelects(["agkd.*"])
                            ->setSelects(["currency_kurs.currency as curr"])
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

    public function edit($id) {
        try {
            $data["user"] = (object) $this->session->userdata('nama');
            $data["id"] = $id;
            $data["jenis_transaksi"] = $this->jenisTransaksi;
            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $data['datas'] = $model->setTables("acc_bank_keluar")->setWheres(["no_bk" => $kode])
                            ->setOrder(["tanggal" => "desc"])->getDetail();
            if (!$data['datas']) {
                show_404();
            }
            $data['data_detail'] = $model->setTables("acc_bank_keluar_detail akmd")->setWheres(["no_bk" => $kode])
                    ->setJoins("acc_coa", "acc_coa.kode_coa = akmd.kode_coa")
                    ->setJoins("currency_kurs", "currency_kurs.id = currency_id")
                    ->setOrder(["tanggal" => "desc", "row_order" => "asc"])
                    ->setSelects(["akmd.no_bk,akmd.tanggal,akmd.kode_coa,akmd.bank,akmd.no_rek,akmd.no_bg,akmd.kurs,akmd.currency_id,akmd.nominal,tgl_cair,tgl_jt,uraian"])
                    ->setSelects(["acc_coa.nama as nama_coa", "currency_kurs.currency as curr", "giro_keluar_detail_id"])
                    ->getData();
            $data["coas"] = $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
                            ->setWheres(["level" => 5])->setOrder(["kode_coa" => "asc"])->getData();
            $data["coa"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "bank"])->setOrder(["nama"=>"asc"])->getData();
            $data['id_dept'] = 'ACCBK';
            $data["jurnal"] = $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $data['datas']->jurnal])->getDetail();
            $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
            $this->load->view('accounting/v_bank_keluar_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function update($id) {
        $pin = false;
        try {
            $kode = decrypt_url($id);
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $users = (object) $this->session->userdata('nama');
            $kodeCoa = $this->input->post("kode_coa");

            if (count($kodeCoa) > 0) {
                $this->valForm = array_merge($this->valForm, [
                    [
                        'field' => 'bank[]',
                        'label' => 'Bank',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ],
                    [
                        'field' => 'norek[]',
                        'label' => 'No Rek',
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
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*\.?\d*$/]'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ]
                ]);
            }
            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            if ($this->input->post('partner') === "" && $this->input->post('lain_lain') === "") {
                throw new \Exception("Kepada / Lain - Lain Harus diisi salah satu", 500);
            }
            $tanggal = $this->input->post("tanggal");
            $model = new $this->m_global;
            $dt = $model->setTables("acc_bank_keluar")->setWheres(["no_bk" => $kode])->getDetail();
            if (!$dt) {
                throw new \Exception("Data Tidak ditemukan", 500);
            }
            if ($dt->status != "draft") {
                throw new \Exception("Status Harus Dalam Posisi Draft", 500);
            }

            //validasi
            $blnDok = date("n", strtotime($dt->tanggal));
            $blnform = date("n", strtotime($tanggal));
            if ($blnform != $blnDok) {
                throw new \Exception("Edit Tidak bisa dilakukan karena berbeda Bulan", 500);
            }
//            $this->validasiPin($pin, "Edit Data Hanya bisa dilakukan Oleh Supervisor", $dt->tanggal);

            $ids = $this->input->post("ids");
            $header = [
                "tanggal" => $tanggal,
                "kode_coa" => $this->input->post("no_acc"),
                "partner_id" => $this->input->post("partner"),
                "partner_nama" => $this->input->post("partner_name"),
                "lain2" => $this->input->post("lain_lain"),
                "transinfo" => $this->input->post("transaksi"),
                "jenis_transaksi" => $this->input->post("jenis_transaksi")
            ];
            $this->_module->startTransaction();
            $nobg = $this->input->post("nobg");
            $model = new $this->m_global;

            $model->setTables("acc_bank_keluar_detail")->setWheres(["bank_keluar_id" => $ids])->delete();
            $model->setTables("acc_bank_keluar")->setWheres(["no_bk" => $kode])->update($header);
            $detail = [];
            if (count($kodeCoa) > 0) {
                $kurs = $this->input->post("kurs");
                $curr = $this->input->post("curr");
                $nominal = $this->input->post("nominal");

                $bank = $this->input->post("bank");
                $norek = $this->input->post("norek");
                $tgljt = $this->input->post("tgljt");
                $uraian = $this->input->post("uraian");
                $giroID = $this->input->post("giro_keluar_detail");
                $totalRp = 0;
                foreach ($this->input->post("tglcair") as $key => $value) {
                    $totalRp += $nominal[$key];
                    $detail [] = [
                        "no_bk" => $kode,
                        "bank_keluar_id" => $ids,
                        "uraian" => $uraian[$key],
                        "tgl_cair" => $value,
                        "tgl_jt" => $tgljt[$key],
                        "kode_coa" => $kodeCoa[$key],
                        "kurs" => $kurs[$key],
                        "currency_id" => $curr[$key],
                        "nominal" => $nominal[$key],
                        "no_bg" => $nobg[$key],
                        "bank" => $bank[$key],
                        "no_rek" => $norek[$key],
                        "tanggal" => $tanggal,
                        "giro_keluar_detail_id" => $giroID[$key],
                        "row_order" => ($key + 1)
                    ];
                }
                $header["total_rp"] = $totalRp;
                $model->setTables("acc_bank_keluar")->setWheres(["no_bk" => $kode])->update($header);
                $model->setTables("acc_bank_keluar_detail")->saveBatch($detail);
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }

            $log = "Asal Data : DATA -> " . logArrayToString("; ", json_decode($this->input->post("head"), true));
            $log .= "\nDETAIL -> " . logArrayToString("; ", json_decode($this->input->post("detail"), true));
            $log .= "\n";
            $log .= "Perubahan : DATA -> " . logArrayToString("; ", $header);
            $log .= "\nDETAIL -> " . logArrayToString("; ", $detail);

            $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);
            $url = site_url("accounting/bankkeluar/edit/{$id}");
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
            $head = $model->setTables("acc_bank_keluar")->setJoins("acc_coa", "acc_coa.kode_coa = acc_bank_keluar.kode_coa")
                            ->setSelects(["acc_bank_keluar.*", "acc_coa.nama as nama_coa"])
                            ->setWheres(["no_bk" => $kode])->getDetail();
            if (!$head) {
                throw new \exception("Data No Bank Keluar {$kode} tidak ditemukan", 500);
            }
            $buff = $printer->getPrintConnector();
            $buff->write("\x1bC" . chr(34));
            $buff->write("\x1bM");
            $tanggal = date("d-m-Y", strtotime($head->tanggal));
            $printer->text(str_pad("Tanggal : {$tanggal}", 67));

            $printer->text(str_pad("No : {$head->no_bk}", 21));
            $printer->selectPrintMode();
            $printer->feed();
            $printer->feed();
            $printer->text(str_pad("", 25));
            $buff->write("\x1bE" . chr(1));
            $printer->text(str_pad("BUKTI BANK KELUAR (BBK)", 20));
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
            $printer->text(str_pad("No Acc (Kredit) : {$head->kode_coa}", 30));
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
            $detail = $model->setTables("acc_bank_keluar_detail")
                            ->setJoins("currency_kurs", "currency_kurs.id = currency_id")
                            ->setWheres(["bank_keluar_id" => $head->id])
                            ->setSelects(["acc_bank_keluar_detail.*", "currency_kurs.currency as curr"])->getData();
            $printer->selectPrintMode();
            $buff->write("\x1bX" . chr(15));
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad("No", 3));
            $printer->text(str_pad("Bank", 13, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("No Rek", 20, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("No Cek/BG", 20, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("Tgl.JT", 13, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("Tgl.Cair", 13, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("No Acc(Debet)", 15, " ", STR_PAD_BOTH));
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
                $line = str_pad($no, 3);
                $line .= str_pad($values->bank, 13);
                $line .= str_pad($values->no_rek, 20);
                $line .= str_pad($values->no_bg, 20);
                $line .= str_pad(date("d-m-Y", strtotime($values->tgl_jt)), 13);
                $line .= str_pad(date("d-m-Y", strtotime($values->tgl_cair)), 13);
                $line .= str_pad($values->kode_coa, 15, " ", STR_PAD_BOTH);
                $line .= str_pad(number_format($values->kurs, 2), 10, " ", STR_PAD_BOTH);
                $line .= str_pad($values->curr, 10, " ", STR_PAD_BOTH);
                $line .= str_pad(number_format($values->nominal, 2), 20, " ", STR_PAD_LEFT);
                $printer->text($line . "\n");
            }
            $printer->feed();

            $printer->text(str_pad("", 97));
            $printer->text(str_pad("Total", 10, " ", STR_PAD_BOTH));
            $printer->text(str_pad(number_format($totals, 2), 30, " ", STR_PAD_LEFT));
            $printer->feed();
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad("", 137));
            $printer->setUnderline(Printer::UNDERLINE_NONE);
            $printer->feed();
            $printer->feed();
            $printer->selectPrintMode();
            $buff->write("\x1bM");
            $printer->text(str_pad("Diinput oleh:", 19, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Diterima oleh:", 19, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Disetujui oleh:", 19, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Mengetahui:", 19, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Dikeluarkan oleh:", 19, " ", STR_PAD_BOTH));
            $printer->feed();
            $printer->feed();
            $printer->feed();
            $printer->feed();
            $printer->text(str_pad("(___________)", 19, " ", STR_PAD_BOTH));
            $printer->text(str_pad("(___________)", 19, " ", STR_PAD_BOTH));
            $printer->text(str_pad("(___________)", 19, " ", STR_PAD_BOTH));
            $printer->text(str_pad("(___________)", 19, " ", STR_PAD_BOTH));
            $printer->text(str_pad("(___________)", 19, " ", STR_PAD_BOTH));
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
            $head = $model->setTables("acc_bank_keluar")->setJoins("acc_bank_keluar_detail", "acc_bank_keluar.id = bank_keluar_id", "left")
                            ->setJoins("currency_kurs", "currency_kurs.id = currency_id", "left")
                            ->setSelects(["acc_bank_keluar.*", "currency_kurs.currency,currency_kurs.kurs", "bank_keluar_id"])
                            ->setWheres(["acc_bank_keluar.no_bk" => $kode])->getDetail();

            if (!$head) {
                throw new \exception("Data No Bank Keluar {$kode} tidak ditemukan", 500);
            }
            if (!$head->bank_keluar_id) {
                throw new \exception("Data Detail Harus Terisi", 500);
            }
            $this->_module->startTransaction();
            $this->_module->lock_tabel("token_increment WRITE,acc_bank_keluar WRITE,acc_bank_keluar_detail WRITE,log_history WRITE,acc_kas_masuk_detail READ"
                    . ",main_menu_sub READ,acc_jurnal_entries_items WRITE,acc_jurnal_entries WRITE,currency_kurs READ,acc_giro_keluar_detail WRITE,setting READ");
            $model->update(["status" => $status]);
            switch ($status) {
                case "confirm":
                    if ($head->status !== "draft") {
                        throw new \exception("Data No Bank Keluar {$kode} dalam status {$head->status}", 500);
                    }

                    $jurnalDB = new $this->m_global;
                    $model = clone $jurnalDB;
                    $getGiroId = $model->setTables("acc_bank_keluar_detail")->setSelects(["GROUP_CONCAT(giro_keluar_detail_id) as gids"])->setWheres(["bank_keluar_id" => $head->id])
                            ->getDetail();
                    if ($getGiroId->gids !== null) {
                        $checkGiroCair = $model->setTables("acc_giro_keluar_detail")->setWhereRaw("id in ({$getGiroId->gids})")
                                        ->setWheres(["cair" => 1])->getDetail();
                        if ($checkGiroCair) {
                            throw new \exception("Data Giro dipilih sudah dalam status Cair", 500);
                        }
                    }
                    $giro = [];
                    $items = $model->setTables("acc_bank_keluar_detail")->setJoins("currency_kurs", "currency_kurs.id = currency_id", "left")
                                    ->setSelects(["acc_bank_keluar_detail.*", "currency_kurs.currency"])
                                    ->setWheres(["bank_keluar_id" => $head->id])->getData();

                    if ($head->jurnal !== "") {
                        $jurnal = $head->jurnal;
                        $stt = "edit";
                    } else {
                        if (!$jurnal = $this->token->noUrut("jurnal_acc_bk", date('y', strtotime($head->tanggal)) . '/' . date('m', strtotime($head->tanggal)), true)
                                        ->generate("BK/", '/%05d')->get()) {
                            throw new \Exception("No jurnal tidak terbuat", 500);
                        }
                        $stt = "create";
                    }

                    $partner = (strlen($head->partner_nama) > 1) ? $head->partner_nama : $head->lain2;
                    $jurnalData = ["kode" => $jurnal, "periode" => date("Y/m", strtotime($head->tanggal)),
                        "origin" => "{$kode}", "status" => "posted", "tanggal_dibuat" => $head->tanggal, "tipe" => "BK",
                        "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => "{$partner}"];

                    $jurnalItems = [];
                    foreach ($items as $key => $item) {
                        $giro[] = $item->giro_keluar_detail_id;
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "{$item->bank} {$item->no_rek}",
                            "reff_note" => "",
                            "partner" => ($head->partner_id ?? ""),
                            "kode_coa" => $item->kode_coa,
                            "posisi" => "D",
                            "nominal_curr" => $item->nominal,
                            "kurs" => $item->kurs,
                            "kode_mua" => $item->currency,
                            "nominal" => ($item->nominal * $item->kurs),
                            "row_order" => ( $key + 1)
                        );
                    }

                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "{$head->transinfo}",
                        "reff_note" => "",
                        "partner" => ($head->partner_id ?? ""),
                        "kode_coa" => $head->kode_coa,
                        "posisi" => "C",
                        "nominal_curr" => $head->total_rp,
                        "kurs" => $items[0]->kurs,
                        "kode_mua" => $head->currency,
                        "nominal" => ($head->total_rp * $items[0]->kurs),
                        "row_order" => (count($jurnalItems) + 1)
                    );
                    if ($head->jurnal !== "") {
                        $jurnalDB->setTables("acc_jurnal_entries")->setWheres(["kode" => $jurnal])->update($jurnalData);
                        $jurnalDB->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $jurnal])->delete();
                    } else {
                        $jurnalDB->setTables("acc_jurnal_entries")->save($jurnalData);
                        $model->setTables("acc_bank_keluar")->setWheres(["id" => $head->id])->update(["jurnal" => $jurnal]);
                        $this->_module->gen_history_new($sub_menu, $kode, 'edit', "No Jurnal : {$jurnal}", $username);
                    }
                    $model->setTables("acc_giro_keluar_detail")->setWhereIn("id", $giro)->update(["cair" => 1]);
                    $jurnalDB->setTables("acc_jurnal_entries_items")->saveBatch($jurnalItems);
                    $log = "Header -> " . logArrayToString("; ", $jurnalData);
                    $log .= "\nDETAIL -> " . logArrayToString("; ", $jurnalItems);
                    $this->_module->gen_history_new("jurnal_entries", $jurnal, "{$stt}", $log, $username);

                    //update giro keluar cair
                    $model->setTables("acc_giro_keluar_detail")->setWhereRaw("id in (select giro_keluar_detail_id from acc_bank_keluar_detail where bank_keluar_id = '{$head->id}')")
                            ->update(["cair" => 1]);
                    break;

                case "draft":
                    if ($head->status !== "cancel") {
                        throw new \exception("Data No Bank Keluar {$kode} dalam status {$head->status}", 500);
                    }

                    $this->validasiPin($pin, "Simpan Draft Hanya bisa dilakukan Oleh Supervisor", $head->tanggal);

                    break;

                default:
                    $this->validasiPin($pin, "Batal / Cancel Data Hanya bisa dilakukan Oleh Supervisor", $head->tanggal);

                    $item = $model->setTables("acc_bank_keluar_detail")->setWheres(["bank_keluar_id" => $head->id, "giro_keluar_detail_id <>" => '0'])
                                    ->setSelects(["GROUP_CONCAT(giro_keluar_detail_id) as gids"])->getDetail();
                    if ($item->gids !== null) {
                        $cekKas = $model->setTables("acc_kas_masuk_detail")->setWhereRaw("giro_keluar_detail_id in ({$item->gids})")->getDetail();
                        if ($cekKas) {
                            throw new \exception("Data Giro Sudah ada Pada Kas Masuk {$cekKas->no_km}", 500);
                        }
                        $model->setTables("acc_giro_keluar_detail")->setWheres(["cair" => 1])->setWhereRaw("id in ({$item->gids})")
                                ->update(["cair" => 0]);
                    }
                    $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $head->jurnal])->update(["status" => "unposted"]);
                    $this->_module->gen_history_new("jurnal_entries", $head->jurnal, 'edit', "Merubah Status Ke unposted dari Kas Bank Keluar", $username);
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
            $this->output->set_status_header($ex->getCode())
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
    }
}
