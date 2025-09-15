<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Kaskeluar
 *
 * @author RONI
 */
require FCPATH . 'vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;

class Kaskeluar extends MY_Controller {

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
        $data['id_dept'] = 'ACCKK';
        $this->load->view('accounting/v_kas_keluar', $data);
    }

    public function add() {
        $data['id_dept'] = 'ACCKK';
        $model = new $this->m_global;
        $data["coas"] = $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
                        ->setWheres(["level" => 5])->setOrder(["kode_coa" => "asc"])->getData();
        $data["coa"] = $model->setWheres(["jenis_transaksi" => "kas"])->getData();
        $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
        $this->load->view('accounting/v_kas_keluar_add', $data);
    }

    public function list_data() {
        try {
            $data = array();
            $list = new $this->m_global;
            $list->setTables("acc_kas_keluar")->setOrder(["acc_kas_keluar.tanggal" => "desc"])
                    ->setJoins("acc_coa", "acc_coa.kode_coa = acc_kas_keluar.kode_coa", "left")
                    ->setJoins("mst_status", "mst_status.kode = acc_kas_keluar.status", "left")
                    ->setSearch(["no_kk", "acc_kas_keluar.kode_coa", "partner_nama", "lain2", "transinfo", "acc_kas_keluar.status"])
                    ->setOrders([null, "no_kk", "partner_nama", "acc_kas_keluar.tanggal", null, "total_rp", "acc_kas_keluar.status"])
                    ->setSelects(["acc_kas_keluar.*", "acc_coa.nama as nama_coa", "nama_status as status"]);
            $no = $_POST['start'];
            $tanggal = $this->input->post("tanggal");
            $nobukti = $this->input->post("no_bukti");
            $customer = $this->input->post("customer");
            $uraian = $this->input->post("uraian");

            if ($tanggal !== "") {
                $tanggals = explode(" - ", $tanggal);
                $list->setWheres(["date(acc_kas_keluar.tanggal) >=" => $tanggals[0], "date(acc_kas_keluar.tanggal) <=" => $tanggals[1]]);
            }
            if ($nobukti !== "") {
                $list->setWheres(["acc_kas_keluar.no_kk LIKE" => "%{$nobukti}%"]);
            }
            if ($customer !== "") {
                $list->setWheres(["partner_nama LIKE" => "%{$customer}%"]);
            }
            if ($uraian !== "") {
                $list->setJoins("acc_kas_keluar_detail abkd", "abkd.kas_keluar_id = acc_kas_keluar.id")
                        ->setGroups(["kas_keluar_id"])->setWheres(["abkd.uraian LIKE" => "%{$uraian}%"]);
            }
            foreach ($list->getData() as $field) {
                $kode_encrypt = encrypt_url($field->no_kk);
                $no++;
                $data [] = [
                    $no,
                    "<a href='" . base_url("accounting/kaskeluar/edit/{$kode_encrypt}") . "'>{$field->no_kk}</a>",
                    ($field->partner_nama === "") ? $field->lain2 : $field->partner_nama,
                    date("Y-m-d", strtotime($field->tanggal)),
                    $field->kode_coa . " - " . $field->nama_coa,
                    number_format($field->total_rp, 2),
                    $field->status
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll("acc_kas_keluar.id"),
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

    public function edit($id) {
        try {
            $data["user"] = (object) $this->session->userdata('nama');
            $data["id"] = $id;
            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $data['datas'] = $model->setTables("acc_kas_keluar acd")->setWheres(["no_kk" => $kode])
//                            ->setSelects(["acd.no_kk,acd.tanggal,acd.kode_coa,acd.partner_id,acd.partner_nama,acd.lain2,acd.transinfo,acd.total_rp,status,id"])
                            ->setOrder(["tanggal" => "desc"])->getDetail();
            if (!$data['datas']) {
                show_404();
            }
            $data['data_detail'] = $model->setTables("acc_kas_keluar_detail acd")->setWheres(["no_kk" => $kode])
                    ->setJoins("acc_coa", "acc_coa.kode_coa = acd.kode_coa")
                    ->setJoins("currency_kurs", "currency_kurs.id = currency_id")
                    ->setJoins("purchase_order_detail pod", "pod.id = po_detail_id", "left")
                    ->setOrder(["tanggal" => "desc", "row_order" => "asc"])
                    ->setSelects(["acd.no_kk,acd.tanggal,acd.kode_coa,acd.uraian,acd.kurs,acd.currency_id,acd.nominal"])
                    ->setSelects(["acc_coa.nama as nama_coa", "currency_kurs.currency as curr", "po_no_po"])
                    ->getData();
            $data["coas"] = $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
                            ->setWheres(["level" => 5])->setOrder(["kode_coa" => "asc"])->getData();
            $data["coa"] = $model->setWheres(["jenis_transaksi" => "kas"])->getData();
            $data["jurnal"] = $model->setTables("acc_jurnal_entries")->setWheres(["origin" => $kode])->getDetail();
            $data['id_dept'] = 'ACCKK';
            $data["jurnal"] = $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $data['datas']->jurnal])->getDetail();
            $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
            $this->load->view('accounting/v_kas_keluar_edit', $data);
        } catch (Exception $ex) {
            
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
                        'rules' => ['trim', 'required','regex_match[/^\d*\.?\d*$/]'],
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
                        'rules' => ['trim', 'required','regex_match[/^\d*\.?\d*$/]'],
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
            $dt = $model->setTables("acc_kas_keluar")->setWheres(["no_kk" => $kode])->getDetail();
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
            $this->_module->startTransaction();

            $model->setTables("acc_kas_keluar")->setWheres(["no_kk" => $kode])->update($header);
            $model->setTables("acc_kas_keluar_detail")->setWheres(["no_kk" => $kode])->delete();
            $detail = [];
            if (count($kodeCoa) > 0) {
                $kurs = $this->input->post("kurs");
                $curr = $this->input->post("curr");
                $nominal = $this->input->post("nominal");
                $po = $this->input->post("po_detail");
                $totalRp = 0;
                foreach ($this->input->post("uraian") as $key => $value) {
                    $totalRp += $nominal[$key];
                    $detail [] = [
                        "kas_keluar_id" => $this->input->post("ids"),
                        "tanggal" => $this->input->post("tanggal"),
                        "no_kk" => $kode,
                        "uraian" => $value,
                        "kode_coa" => $kodeCoa[$key],
                        "kurs" => $kurs[$key],
                        "currency_id" => $curr[$key],
                        "nominal" => $nominal[$key],
                        "po_detail_id" => $po[$key],
                        "row_order" => ($key + 1)
                    ];
                }

                $header["total_rp"] = $totalRp;
                $model->setTables("acc_kas_keluar")->setWheres(["no_kk" => $kode])->update($header);
                $model->setTables("acc_kas_keluar_detail")->saveBatch($detail);
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
            $url = site_url("accounting/kaskeluar/edit/{$id}");
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
                        'rules' => ['trim', 'required','regex_match[/^\d*\.?\d*$/]'],
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
                        'rules' => ['trim', 'required','regex_match[/^\d*\.?\d*$/]'],
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
            $this->_module->lock_tabel("token_increment WRITE,acc_kas_keluar WRITE,log_history WRITE,main_menu_sub READ,acc_kas_keluar_detail WRITE");
            $tanggal = $this->input->post("tanggal");
            $coaName = $this->input->post("coa_name");
            if (strtolower($coaName) === 'kas valas') {
                if (!$nokk = $this->token->noUrut('kas_keluar_valas', date('ym', strtotime($tanggal)), true)->generate('KKVH', '/%03d')->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . getRomawi(date('m', strtotime($tanggal)) . "/"))->get()) {
                    throw new \Exception("No Kas Keluar Valas tidak terbuat", 500);
                }
            } else {
                if (!$nokk = $this->token->noUrut('kas_keluar', date('ym', strtotime($tanggal)), true)->generate('KKBRH', '/%03d')->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . getRomawi(date('m', strtotime($tanggal)) . "/"))->get()) {
                    throw new \Exception("No Kas Keluar tidak terbuat", 500);
                }
            }

            $now = date("Y-m-d H:i:s");
            $header = [
                "no_kk" => $nokk,
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
            $headID = $model->setTables("acc_kas_keluar")->save($header);
            $detail = [];
            if (count($kodeCoa) > 0) {
                $kurs = $this->input->post("kurs");
                $curr = $this->input->post("curr");
                $nominal = $this->input->post("nominal");
                $po = $this->input->post("po_detail");
                $totalRp = 0;
                foreach ($this->input->post("uraian") as $key => $value) {
                    $totalRp += $nominal[$key];
                    $detail [] = [
                        "kas_keluar_id" => $headID,
                        "tanggal" => $tanggal,
                        "no_kk" => $nokk,
                        "uraian" => $value,
                        "kode_coa" => $kodeCoa[$key],
                        "kurs" => $kurs[$key],
                        "currency_id" => $curr[$key],
                        "nominal" => $nominal[$key],
                        "po_detail_id" => $po[$key],
                        "row_order" => ($key + 1)
                    ];
                }
                $model->setTables("acc_kas_keluar")->setWheres(["id" => $headID])->update(["total_rp" => $totalRp]);
                $model->setTables("acc_kas_keluar_detail")->saveBatch($detail);
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }

            $this->_module->gen_history_new($sub_menu, $nokk, 'create', "DATA -> " . logArrayToString("; ", $header) . "\n Detail -> " . logArrayToString("; ", $detail), $username);
            $url = site_url("accounting/kaskeluar/edit/" . encrypt_url($nokk));
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

    public function get_currency() {
        try {
            $model = new $this->m_global;
            $model->setTables("currency_kurs")->setSelects(["id", "currency"]);
            if ($this->input->get('search') !== "") {
                $model->setWheres(["currency LIKE" => "%{$this->input->get('search')}%"]);
            }
            $_POST['length'] = 50;
            $_POST['start'] = 0;
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => $model->getData())));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("message" => $ex->getMessage())));
        }
    }

    public function get_partner() {
        try {
            $model = new $this->m_global;
            $model->setTables("partner")->setSelects(["id", "nama"])->setOrder(["nama" => "asc"]);
            if ($this->input->get('search') !== "") {
                $model->setWheres(["nama LIKE" => "%{$this->input->get('search')}%"]);
            }
            $_POST['length'] = 50;
            $_POST['start'] = 0;
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => $model->getData())));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("message" => $ex->getMessage())));
        }
    }

    public function get_view_fpt() {
        $fpt = $this->input->post("trx");
        $view = $this->load->view('accounting/modal/v_fpt', ["fpt" => $fpt], true);
        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['data' => $view]));
    }

    public function list_data_fpt() {
        try {
            $jenis = $this->input->post("jenis");
            $fpt = $this->input->post("fpt");
            $fpt = explode(",", $fpt);
            $data = array();
            $model = new $this->m_global;
            $list = $model->setTables("purchase_order po")->setOrders([null, "no_po", "nama_supplier", "create_date", "order_date", "po.status"])
                    ->setSelects(["po.*", "p.nama as nama_supplier", "nama_status", "ck.currency as curr_kode", "coalesce(poe.status,'') as poe_status"])
                    ->setOrder(['create_date' => 'desc'])->setSearch(["p.nama", "no_po", "po.status", "note"])
                    ->setJoins("currency_kurs ck", "ck.id = po.currency", "left")
                    ->setJoins("partner p", "(p.id = po.supplier and p.supplier = 1)")
                    ->setJoins("mst_status", "mst_status.kode = po.status", "left")
                    ->setJoins("purchase_order_edited poe", "(poe.po_id = po.no_po and poe.status not in ('cancel','done'))", "left")
                    ->setWheres(["jenis" => $jenis, "payment" => 0])
                    ->setWhereIn("po.status", ["purchase_confirmed", "done"]);
            if (count($fpt) > 0) {
                $ff = implode("','", $fpt);
                $list->setWhereRaw("po.no_po not in ('{$ff}')");
            }
            $no = $_POST['start'];
            foreach ($list->getData() as $field) {
                $no++;
                $data [] = [
                    $field->no_po,
                    $field->no_po,
                    $field->nama_supplier,
                    $field->create_date,
                    number_format($field->total, 4) . " " . ( ($field->total === null) ? "" : $field->curr_kode),
                    $field->note
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

    public function add_data_from_fpt() {
        try {
            $no = $this->input->post("no");
            $model = new $this->m_global;
            $data = $model->setTables("purchase_order_detail")->setJoins("purchase_order", "purchase_order.id = po_id")
                            ->setJoins("currency_kurs", "currency_kurs.id = purchase_order.currency")
                            ->setSelects(["purchase_order_detail.total,purchase_order_detail.deskripsi,purchase_order_detail.id,purchase_order_detail.reff_note",
                                "purchase_order.nilai_currency", "purchase_order.no_po"])
                            ->setSelects(["purchase_order.currency as po_curr", "currency_kurs.currency as curr"])
                            ->setWhereIn("po_no_po", $no)->setOrder(["po_no_po" => "asc"])->getData();

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $data)));
        } catch (Exception $ex) {

            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
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
            $head = $model->setTables("acc_kas_keluar")->setJoins("acc_coa", "acc_coa.kode_coa = acc_kas_keluar.kode_coa")
                            ->setSelects(["acc_kas_keluar.*", "acc_coa.nama as nama_coa"])
                            ->setWheres(["no_kk" => $kode])->getDetail();
            if (!$head) {
                throw new \exception("Data No Kas Keluar {$kode} tidak ditemukan", 500);
            }
            $buff = $printer->getPrintConnector();
            $buff->write("\x1bC" . chr(34));
            $buff->write("\x1bM");
            $tanggal = date("Y-m-d", strtotime($head->tanggal));
            $printer->text(str_pad("Tanggal : {$tanggal}", 67));

            $printer->text(str_pad("No : {$head->no_kk}", 21));
            $printer->selectPrintMode();
            $printer->feed();
            $printer->feed();
            $printer->text(str_pad("", 25));
            $buff->write("\x1bE" . chr(1));
            $printer->text(str_pad("BUKTI KAS KELUAR (BKK)", 20));
            $buff->write("\x1bF" . chr(0));
            $printer->feed();
            $printer->text(str_pad("", 25));
            $buff->write("\x1bg" . chr(1));
            $printer->text(str_pad($head->nama_coa, 45, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("", 1));
            $customer = str_split(trim(preg_replace('/\s+/', ' ', "Kepada : {$head->partner_nama}")), 33);
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
//                $buff->write("\x1bE" . chr(1));
                if ($key > 0) {
                    $printer->text(str_pad("", 86));
                }
                $printer->text(str_pad(trim($value), 33, " ", STR_PAD_RIGHT));
//                $buff->write("\x1bF" . chr(0));
            }

            $printer->feed();
//            $printer->selectPrintMode();
            $printer->feed();
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad("Untuk transaksi : {$head->transinfo}", 120));
            $printer->setUnderline(Printer::UNDERLINE_NONE);
            $printer->feed();
            $detail = $model->setTables("acc_kas_keluar_detail")
                            ->setJoins("currency_kurs", "currency_kurs.id = currency_id")
                            ->setWheres(["kas_keluar_id" => $head->id])
                            ->setSelects(["acc_kas_keluar_detail.*", "currency_kurs.currency as curr"])->getData();
            $printer->selectPrintMode();
            $buff->write("\x1bX" . chr(15));
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad("No", 5));
            $printer->text(str_pad("Uraian", 70, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("No Acc(Debet)", 20, " ", STR_PAD_BOTH));
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

//            $lain2= 
//            $printer->text(str_pad("LAIN-LAIN : ", 23, " ", STR_PAD_RIGHT));


            $this->_module->gen_history_new($sub_menu, $kode, "edit", "Melakukan Print Dokumen.", $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (\Exception $ex) {
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
            $head = $model->setTables("acc_kas_keluar")->setJoins("acc_kas_keluar_detail", "acc_kas_keluar.id = kas_keluar_id", "left")
                            ->setJoins("currency_kurs", "currency_kurs.id = currency_id", "left")
                            ->setSelects(["acc_kas_keluar.*", "currency_kurs.currency,currency_kurs.kurs"])
                            ->setWheres(["acc_kas_keluar.no_kk" => $kode])->getDetail();
            if (!$head) {
                throw new \exception("Data No Kas Keluar {$kode} tidak ditemukan", 500);
            }
            if ($head->total_rp < 1) {
                throw new \exception("Data Detail Harus Terisi", 500);
            }
            $this->_module->startTransaction();
            $this->_module->lock_tabel("token_increment WRITE,acc_kas_keluar WRITE,acc_kas_keluar_detail WRITE,log_history WRITE,setting READ"
                    . ",main_menu_sub READ,acc_jurnal_entries_items WRITE,acc_jurnal_entries WRITE,currency_kurs READ,purchase_order_detail WRITE, purchase_order WRITE");
            $model->update(["status" => $status]);
            switch ($status) {
                case "confirm":
                    if ($head->status !== "draft") {
                        throw new \exception("Data No Kas Keluar {$kode} dalam status {$head->status}", 500);
                    }
                    $jurnalDB = new $this->m_global;
                    $model = clone $jurnalDB;
                    $poId = $model->setTables("acc_kas_keluar_detail")->setSelects(["GROUP_CONCAT(po_detail_id) as gids"])->setWheres(["kas_keluar_id" => $head->id])
                            ->getDetail();

                    if ($poId->gids !== null) {
                        $checkPayment = $model->setTables("purchase_order_detail")->setJoins("purchase_order", "purchase_order_detail.po_id = purchase_order.id")
                                        ->setWhereRaw("purchase_order_detail.id in ({$poId->gids})")->setWheres(["payment" => 1])->getDetail();
                        if ($checkPayment) {
                            throw new \exception("Data PO / FPT {$checkPayment->po_no_po} Sudah diBayar", 500);
                        }
                    }


                    if ($head->jurnal !== "") {
                        $jurnal = $head->jurnal;
                        $stt = "edit";
                    } else {
                        if (!$jurnal = $this->token->noUrut("jurnal_acc_kk", date('y', strtotime($head->tanggal)) . '/' . date('m', strtotime($head->tanggal)), true)
                                        ->generate("KK/", '/%05d')->get()) {
                            throw new \Exception("No jurnal tidak terbuat", 500);
                        }
                        $stt = "create";
                    }
                    $jurnalItems = [];
                    $poid = [];
                    $partner = (strlen($head->partner_nama) > 1) ? $head->partner_nama : $head->lain2;

                    $jurnalData = ["kode" => $jurnal, "periode" => date("Y/m", strtotime($head->tanggal)),
                        "origin" => "{$kode}", "status" => "posted", "tanggal_dibuat" => $head->tanggal, "tipe" => "KK",
                        "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => "{$partner}"];

                    $items = $model->setTables("acc_kas_keluar_detail")->setJoins("currency_kurs", "currency_kurs.id = currency_id", "left")
                                    ->setSelects(["acc_kas_keluar_detail.*", "currency_kurs.currency"])
                                    ->setWheres(["kas_keluar_id" => $head->id])->getData();

                    $valas = false;
                    if (strpos($kode, "KKVH") !== false)
                        $valas = true;

                    foreach ($items as $key => $item) {
                        $textKurs = "";
                        if ($valas)
                            $textKurs = " ({$item->nominal}{$item->currency} kurs : " . number_format($item->kurs, 2) . ")";

                        $poid [] = $item->po_detail_id;
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "{$item->uraian}{$textKurs}",
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
                    $textKurs = "";
                    if ($valas)
                        $textKurs = " ({$head->total_rp}{$head->currency} kurs : " . number_format($items[0]->kurs, 2) . ")";
                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "{$head->transinfo}{$textKurs}",
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
                        $model->setTables("acc_kas_keluar")->setWheres(["id" => $head->id])->update(["jurnal" => $jurnal]);
                        $this->_module->gen_history_new($sub_menu, $kode, 'edit', "No Jurnal : {$jurnal}", $username);
                    }
                    $po = implode("','", $poid);
                    $model->setTables("purchase_order")->setWhereRaw("id in (select po_id from purchase_order_detail where id in ('{$po}'))")->update(["payment" => 1]);

                    $jurnalDB->setTables("acc_jurnal_entries_items")->saveBatch($jurnalItems);
                    $log = "Header -> " . logArrayToString("; ", $jurnalData);
                    $log .= "\nDETAIL -> " . logArrayToString("; ", $jurnalItems);
                    $this->_module->gen_history_new("jurnal_entries", $jurnal, $stt, $log, $username);
                    break;

                case "draft":
                    if ($head->status !== "cancel") {
                        throw new \exception("Data No Kas Keluar {$kode} dalam status {$head->status}", 500);
                    }
                    $this->validasiPin($pin, "Simpan Draft Hanya bisa dilakukan Oleh Supervisor", $head->tanggal);
                    break;

                default:
                    $this->validasiPin($pin, "Batal / Cancel Data Hanya bisa dilakukan Oleh Supervisor", $head->tanggal);
                    $poId = $model->setTables("acc_kas_keluar_detail")->setSelects(["GROUP_CONCAT(po_detail_id) as gids"])->setWheres(["kas_keluar_id" => $head->id])
                            ->getDetail();
                    if ($poId->gids !== null) {
                        $model->setTables("purchase_order")->setWhereRaw("id in (select po_id from purchase_order_detail where id in ({$poId->gids}))")->update(["payment" => 0]);
                    }
                    $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $head->jurnal])->update(["status" => "unposted"]);
                    $this->_module->gen_history_new("jurnal_entries", $head->jurnal, 'edit', "Merubah Status Ke unposted dari Kas Keluar", $username);

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
            if (date("j") >= (int)$pinDate->value) {

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
//        if ($bbln === 0) {
//            if (date("j", strtotime($tanggalDok)) <= 10) {
//
//                if (!in_array($users->level, ["Super Administrator", "Supervisor"])) {
//                    throw new \Exception("{$pesanError}", 500);
//                }
//                $pin = $this->session->userdata('pin');
//                if (!$pin) {
//                    $pin = true;
//                    throw new \Exception("masukan pin", 200);
//                }
//                $this->session->unset_userdata('pin');
//            }
//        } else if ($bbln > 0) {
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
