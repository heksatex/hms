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
require_once APPPATH . '/third_party/vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class Bankmasuk extends MY_Controller {

    //put your code here

    protected $jenisTransaksi = [
        "transfer" => "Transfer",
        "inkaso" => "Inkaso",
        "kliring" => "kliring",
        "lain-lain" => "Lain-Lain"
    ];
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

    public function __construct() {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load modul global
        $this->config->load('additional');
        $this->load->model("m_global");
        $this->load->library("token");
        $this->load->driver('cache', array('adapter' => 'file'));
    }

    public function add($depth = 'ACCBM') {
        $data['id_dept'] = $depth;
        $data["jenis_transaksi"] = $this->jenisTransaksi;
        $data["class"] = $this->uri->segment(1);
        $model = new $this->m_global;
//        $data["coas"] = $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
//                        ->setWheres(["level" => 5])->setOrder(["kode_coa" => "asc"])->getData();
        $data["coa"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "bank"])->setOrder(["nama" => "asc"])->getData();
        $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();

        $this->load->view('accounting/v_bank_masuk_add', $data);
    }

    public function index($depth = 'ACCBM') {
        $data['id_dept'] = $depth;
        $data["class"] = $this->uri->segment(1);
        $this->load->view('accounting/v_bank_masuk', $data);
    }

    public function ekspor() {
        try {
            $tanggal = $this->input->post("tanggal");
            $nobukti = $this->input->post("no_bukti");
            $customers = $this->input->post("customer");
            $uraian = $this->input->post("uraian");
            $filter = "Filter : ";
            if ($tanggal !== "") {
                $filter .= "Tanggal : {$tanggal}; ";
            }
            if ($nobukti !== "") {
                $filter .= "No Bukti : {$nobukti}; ";
            }
            if ($customers !== "") {
                $filter .= "Customer : {$customers}; ";
            }
            if ($uraian !== "") {
                $filter .= "Uraian : {$uraian}; ";
            }

            $data = $this->_list_data();
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue("A1", $filter);
            $row = 3;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'No Bukti');
            $sheet->setCellValue("C{$row}", 'Customer');
            $sheet->setCellValue("D{$row}", 'Tanggal');
            $sheet->setCellValue("E{$row}", 'No Acc');
            $sheet->setCellValue("F{$row}", 'Transinfo');
            $sheet->setCellValue("G{$row}", 'Total');
            $sheet->setCellValue("H{$row}", 'Status');
            $noUrut = 0;
            foreach ($data->getData() as $key => $field) {
                $row += 1;
                $noUrut += 1;
                $customer = ($field->partner_nama === "") ? $field->lain2 : $field->partner_nama;
                $sheet->setCellValue("A{$row}", $noUrut);
                $sheet->setCellValue("B{$row}", $field->no_bm);
                $sheet->setCellValue("C{$row}", $customer);
                $sheet->setCellValue("D{$row}", date("Y-m-d", strtotime($field->tanggal)));
                $sheet->setCellValue("E{$row}", $field->kode_coa);
                $sheet->setCellValue("F{$row}", $field->transinfo);
                $sheet->setCellValue("G{$row}", $field->total_rp);
                $sheet->setCellValue("H{$row}", $field->status);
            }
            if ($noUrut > 0) {
                $sheet->getStyle("G4:G{$row}")->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
            }
            $filename = "Bank Masuk " . date("Y-m-d");
            $url = "dist/storages/report/bankgirokas";
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

    protected function _list_data() {
        try {
            $model = new $this->m_global;
            $model->setTables("acc_bank_masuk")->setOrder(["acc_bank_masuk.create_date" => "desc"])
                    ->setJoins("acc_coa", "acc_coa.kode_coa = acc_bank_masuk.kode_coa", "left")
                    ->setJoins("mst_status", "mst_status.kode = acc_bank_masuk.status", "left")
                    ->setSearch(["no_bm", "acc_coa.kode_coa", "partner_nama", "lain2", "transinfo"])
                    ->setOrders([null, "no_bm", "partner_nama", "acc_bank_masuk.tanggal", null, null, "total_rp"])
                    ->setSelects(["acc_bank_masuk.*", "acc_coa.nama as nama_coa", "nama_status as status"]);

            $tanggal = $this->input->post("tanggal");
            $nobukti = $this->input->post("no_bukti");
            $customer = $this->input->post("customer");
            $uraian = $this->input->post("uraian");
            $status = $this->input->post("status"); 
            if ($status !== "") {
                $model->setWheres(["acc_bank_masuk.status" => "{$status}"]);
            }
            if ($tanggal !== "") {
                $tanggals = explode(" - ", $tanggal);
                $model->setWheres(["date(acc_bank_masuk.tanggal) >=" => $tanggals[0], "date(acc_bank_masuk.tanggal) <=" => $tanggals[1]]);
            }
            if ($nobukti !== "") {
                $model->setWheres(["acc_bank_masuk.no_bm LIKE" => "%{$nobukti}%"]);
            }
            if ($customer !== "") {
                $model->setWhereRaw("(partner_nama LIKE '%{$customer}%' or lain2 LIKE '%{$customer}%')");
            }
            if ($uraian !== "") {
                $model->setJoins("acc_bank_masuk_detail abkd", "abkd.bank_masuk_id = acc_bank_masuk.id")
                        ->setGroups(["bank_masuk_id"])->setWheres(["abkd.uraian LIKE" => "%{$uraian}%"]);
            }

            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function list_data() {
        try {
            $data = array();
            $no = $_POST['start'];
            $list = $this->_list_data();
            $class = $this->uri->segment(1);
            foreach ($list->getData() as $field) {
                $kode_encrypt = encrypt_url($field->no_bm);
                $no++;
                $data [] = [
                    $no,
                    "<a href='" . base_url("{$class}/bankmasuk/edit/{$kode_encrypt}") . "'>{$field->no_bm}</a>",
                    ($field->partner_nama === "") ? $field->lain2 : $field->partner_nama,
                    date("Y-m-d", strtotime($field->tanggal)),
                    $field->kode_coa . " - " . $field->nama_coa,
                    $field->transinfo,
                    number_format($field->total_rp, 2),
                    $field->status
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll("acc_bank_masuk.id"),
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

    public function get_view_bukti_giro() {
        $no = $this->input->post("no");
        $view = $this->load->view('accounting/modal/v_bukti_giro', ["no" => json_encode($no, true)], true);
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
            $list->setTables("acc_giro_masuk")->setJoins("acc_giro_masuk_detail agmd", "giro_masuk_id=acc_giro_masuk.id")
                    ->setOrder(["partner_nama" => "asc", "bank" => "asc", "nominal" => "asc", "no_bg" => "asc"])
                    ->setJoins("acc_coa", "acc_coa.kode_coa = acc_giro_masuk.kode_coa")
                    ->setSearch(["agmd.no_gm", "acc_giro_masuk.kode_coa", "partner_nama", "lain2", "transinfo"])
                    ->setOrders([null, "agmd.no_gm", "partner_nama", null, "tanggal", "nominal"])
                    ->setSelects(["acc_giro_masuk.*", "acc_coa.nama as nama_coa", "nominal,agmd.id as dtl_id"])
                    ->setWheres(["acc_giro_masuk.status" => "confirm", "agmd.tgl_jt >=" => "{$days90} 00:00:00",
                        "agmd.tgl_jt <=" => "{$now} 23:59:59", "agmd.nominal >" => 0, "agmd.cair" => 0])
                    ->setWhereRaw("agmd.id not IN (select giro_masuk_detail_id from acc_bank_masuk_detail gmd join acc_bank_masuk gm on (gm.id = bank_masuk_id and gm.status <> 'cancel'))");

            $no = $_POST['start'];
            if ($nos !== "null") {
                $nos = json_decode($nos, true);
                $nos = implode("','", $nos);
                $list->setWhereRaw("agmd.id not in('{$nos}')");
            }
            foreach ($list->getData() as $field) {
                $no++;
                $data [] = [
                    $field->dtl_id,
                    $field->no_gm,
                    $field->partner_nama,
                    $field->lain2,
                    date("Y-m-d", strtotime($field->tanggal)),
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

    public function bg() {
        try {
            $no = $this->input->post("no");
            $model = new $this->m_global;

            $data = $model->setTables("acc_giro_masuk_detail agkd")->setJoins("acc_giro_masuk agk", "agkd.giro_masuk_id = agk.id")
                            ->setJoins("currency_kurs", "currency_kurs.id = agkd.currency_id")
                            ->setSelects(["agkd.*", "partner_id,partner_nama,agk.lain2 as lain,agk.transinfo"])
                            ->setSelects(["currency_kurs.currency as curr"])
                            ->setWhereIn("agkd.id", $no)->setOrder(["agkd.no_gm" => "asc"])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function simpan() {
        try {
            $sub_menu = $this->uri->segment(2);
            $class = $this->uri->segment(1);
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
//                    [
//                        'field' => 'norek[]',
//                        'label' => 'No Rek',
//                        'rules' => ['trim', 'required'],
//                        'errors' => [
//                            'required' => '{field} Pada Item harus diisi'
//                        ]
//                    ],
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
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
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
            $tanggal = $this->input->post("tanggal");
            $this->_module->lock_tabel("token_increment WRITE,acc_bank_masuk WRITE,log_history WRITE,main_menu_sub READ,acc_bank_masuk_detail WRITE");
            if (!$nobm = $this->token->noUrut('bank_masuk', date('ym', strtotime($tanggal)), true)->generate('BBMH', '/%03d')
                            ->prefixAdd("/" . date("y", strtotime($tanggal)) . "/" . getRomawi(date('m', strtotime($tanggal)) . "/"))->get()) {
                throw new \Exception("No Bank Masuk tidak terbuat", 500);
            }
            $now = date("Y-m-d H:i:s");

            $header = [
                "no_bm" => $nobm,
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
            $headID = $model->setTables("acc_bank_masuk")->save($header);

            if (count($kodeCoa) > 0) {

                $kurs = $this->input->post("kurs");
                $curr = $this->input->post("curr");
                $nominal = $this->input->post("nominal");
                $nobg = $this->input->post("nobg");
                $bank = $this->input->post("bank");
                $norek = $this->input->post("norek");
                $uraian = $this->input->post("uraian");
                $giroID = $this->input->post("giro_masuk_detail");
                $totalRp = 0;
                foreach ($this->input->post("tglcair") as $key => $value) {
                    $nom = str_replace(",", "", $nominal[$key]);
                    $totalRp += $nom;
                    $detail [] = [
                        "no_bm" => $nobm,
                        "uraian" => $uraian[$key],
                        "bank_masuk_id" => $headID,
                        "tgl_cair" => $value,
                        "kode_coa" => $kodeCoa[$key],
                        "kurs" => $kurs[$key],
                        "currency_id" => $curr[$key],
                        "nominal" => $nom,
                        "no_bg" => $nobg[$key],
                        "bank" => $bank[$key],
                        "no_rek" => $norek[$key],
                        "tanggal" => $tanggal,
                        "giro_masuk_detail_id" => $giroID[$key],
                        "row_order" => ($key + 1)
                    ];
                }
                $model->setTables("acc_bank_masuk")->setWheres(["id" => $headID])->update(["total_rp" => $totalRp]);
                $model->setTables("acc_bank_masuk_detail")->saveBatch($detail);
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $nobm, 'create', "DATA -> " . logArrayToString("; ", $header) . "\n Detail -> " . logArrayToString("; ", $detail), $username);
            $url = site_url("{$class}/bankmasuk/edit/" . encrypt_url($nobm));
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

    public function edit($id,$depth = "ACCBM") {
        try {
            $data["user"] = (object) $this->session->userdata('nama');
            $data["id"] = $id;
            $data["class"] = $this->uri->segment(1);
            $data["jenis_transaksi"] = $this->jenisTransaksi;
            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $data['datas'] = $model->setTables("acc_bank_masuk")->setWheres(["no_bm" => $kode])
                            ->setOrder(["tanggal" => "desc"])->getDetail();
            if (!$data['datas']) {
                show_404();
            }
            $data['data_detail'] = $model->setTables("acc_bank_masuk_detail akmd")->setWheres(["no_bm" => $kode])
                    ->setJoins("acc_coa", "acc_coa.kode_coa = akmd.kode_coa")
                    ->setJoins("currency_kurs", "currency_kurs.id = currency_id")
                    ->setOrder(["tanggal" => "desc", "row_order" => "asc"])
                    ->setSelects(["akmd.no_bm,akmd.tanggal,akmd.kode_coa,akmd.bank,akmd.no_rek,akmd.no_bg,akmd.kurs,akmd.currency_id,akmd.nominal,tgl_cair,uraian"])
                    ->setSelects(["acc_coa.nama as nama_coa", "currency_kurs.currency as curr", "giro_masuk_detail_id"])
                    ->getData();
//            $data["coas"] = $model->setTables("acc_coa")->setSelects(["kode_coa", "nama"])
//                            ->setWheres(["level" => 5])->setOrder(["kode_coa" => "asc"])->getData();
            $data["coa"] = $model->setTables("acc_coa")->setWheres(["jenis_transaksi" => "bank"])->setOrder(["nama" => "asc"])->getData();
            $data['id_dept'] = $depth;
            $data["jurnal"] = $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $data['datas']->jurnal])->getDetail();
            $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
            $this->load->view('accounting/v_bank_masuk_edit', $data);
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
//                    [
//                        'field' => 'norek[]',
//                        'label' => 'No Rek',
//                        'rules' => ['trim', 'required'],
//                        'errors' => [
//                            'required' => '{field} Pada Item harus diisi'
//                        ]
//                    ],
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
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
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
            $dt = $model->setTables("acc_bank_masuk")->setWheres(["no_bm" => $kode])->getDetail();
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
            $model = new $this->m_global;
            $nobg = $this->input->post("nobg");
            $asalDetail = $model->setTables("acc_bank_masuk_detail")->setWheres(["bank_masuk_id" => $ids])->getData();
            $model->delete();
            $asal = $model->setTables("acc_bank_masuk")->setWheres(["no_bm" => $kode])->getDetail();
            $model->update($header);
            $detail = [];
            $this->_module->startTransaction();
            if (count($kodeCoa) > 0) {
                $kurs = $this->input->post("kurs");
                $curr = $this->input->post("curr");
                $nominal = $this->input->post("nominal");
                $bank = $this->input->post("bank");
                $norek = $this->input->post("norek");
                $uraian = $this->input->post("uraian");
                $giroID = $this->input->post("giro_masuk_detail");
                $totalRp = 0;
                foreach ($this->input->post("tglcair") as $key => $value) {
                    $nom = str_replace(",", "", $nominal[$key]);
                    $totalRp += $nom;
                    $detail [] = [
                        "no_bm" => $kode,
                        "uraian" => $uraian[$key],
                        "bank_masuk_id" => $ids,
                        "tgl_cair" => $value,
                        "kode_coa" => $kodeCoa[$key],
                        "kurs" => $kurs[$key],
                        "currency_id" => $curr[$key],
                        "nominal" => $nom,
                        "no_bg" => $nobg[$key],
                        "bank" => $bank[$key],
                        "no_rek" => $norek[$key],
                        "tanggal" => $tanggal,
                        "giro_masuk_detail_id" => $giroID[$key],
                        "row_order" => ($key + 1)
                    ];
                }

                $header["total_rp"] = $totalRp;
                $model->setTables("acc_bank_masuk")->setWheres(["no_bm" => $kode])->update($header);
                $model->setTables("acc_bank_masuk_detail")->saveBatch($detail);
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $log = "";
            $log .= "Asal Data : DATA -> " . logArrayToString("; ", (array) $asal);
            $log .= "\nDETAIL -> " . logArrayToString("; ", $asalDetail);
            $log .= "\n";
            $log .= "Perubahan : DATA -> " . logArrayToString("; ", $header);
            $log .= "\nDETAIL -> " . logArrayToString("; ", $detail);
            $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);
            $url = site_url("accounting/bankmasuk/edit/{$id}");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'url' => $url)));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "pin" => $pin)));
        }
    }

    public function print_pdf() {
        try {
            $id = $this->input->post("id");
            $kode = decrypt_url($id);
            $model = new $this->m_global;

            $head = $model->setTables("acc_bank_masuk")->setJoins("acc_coa", "acc_coa.kode_coa = acc_bank_masuk.kode_coa")
                            ->setSelects(["acc_bank_masuk.*", "acc_coa.nama as nama_coa", "date(tanggal) as tanggal"])
                            ->setWheres(["no_bm" => $kode])->getDetail();
            if (!$head) {
                throw new \exception("Data No Bank Masuk {$kode} tidak ditemukan", 500);
            }
            $data["detail"] = $model->setTables("acc_bank_masuk_detail")
                            ->setJoins("currency_kurs", "currency_kurs.id = currency_id")
                            ->setWheres(["bank_masuk_id" => $head->id])
                            ->setSelects(["acc_bank_masuk_detail.*", "currency_kurs.currency as curr"])->getData();
            $data["head"] = $head;
            $html = $this->load->view("print/acc/v_bank_masuk_print", $data, true);
            $url = "dist/storages/print/bank";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $mpdf = new Mpdf(['tempDir' => FCPATH . 'tmp']);
            $mpdf->autoPageBreak = true;
            $mpdf->WriteHTML($html);
            $filename = str_replace("/", "-", $data["head"]->no_bm);
            $pathFile = "{$url}/{$filename}.pdf";
            $mpdf->Output(FCPATH . $pathFile, "F");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("url" => base_url($pathFile))));
        } catch (Exception $ex) {
            log_message("error", json_encode($ex));
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
            $head = $model->setTables("acc_bank_masuk")->setJoins("acc_coa", "acc_coa.kode_coa = acc_bank_masuk.kode_coa")
                            ->setSelects(["acc_bank_masuk.*", "acc_coa.nama as nama_coa"])
                            ->setWheres(["no_bm" => $kode])->getDetail();
            if (!$head) {
                throw new \exception("Data No Bank Masuk {$kode} tidak ditemukan", 500);
            }

            $buff = $printer->getPrintConnector();
            $buff->write("\x1bO");
            $buff->write("\x1b" . chr(2));
            $buff->write("\x1bC" . chr(33));
            $buff->write("\x1bN" . chr(4));
            $buff->write("\x1bM");
            $tanggal = date("d-m-Y", strtotime($head->tanggal));
            $printer->text(str_pad("Tanggal : {$tanggal}", 67));

            $printer->text(str_pad("No : {$head->no_bm}", 21));
            $printer->selectPrintMode();
            $printer->feed();
            $printer->feed();
            $printer->text(str_pad("", 25));
            $buff->write("\x1bE" . chr(1));
            $printer->text(str_pad("BUKTI BANK MASUK (BBM)", 20));
            $buff->write("\x1bF" . chr(0));
            $printer->feed();
            $printer->text(str_pad("", 25));
            $buff->write("\x1bg" . chr(1));
            $printer->text(str_pad($head->nama_coa, 45, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("", 1));
            $customer = str_split(trim(preg_replace('/\s+/', ' ', "Dari : {$head->partner_nama}")), 33);
            foreach ($customer as $key => $value) {
                if ($key > 0) {
                    $printer->text(str_pad("", 86));
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
            $detail = $model->setTables("acc_bank_masuk_detail")
                            ->setJoins("currency_kurs", "currency_kurs.id = currency_id")
                            ->setWheres(["bank_masuk_id" => $head->id])
                            ->setSelects(["acc_bank_masuk_detail.*", "currency_kurs.currency as curr"])->getData();
            $printer->selectPrintMode();
            $buff->write("\x1bX" . chr(15));
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad("No", 5));
            $printer->text(str_pad("uraian", 40, " ", STR_PAD_RIGHT));
//            $printer->text(str_pad("No Rek", 20, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("No Cek/BG", 20, " ", STR_PAD_RIGHT));
//            $printer->text(str_pad("Tgl.Cair", 15, " ", STR_PAD_RIGHT));
            $printer->text(str_pad("No Acc(Kredit)", 20, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Kurs", 10, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Curr", 10, " ", STR_PAD_BOTH));
            $printer->text(str_pad("Nominal", 30, " ", STR_PAD_LEFT));
            $printer->feed();
            $printer->setUnderline(Printer::UNDERLINE_NONE);
            $totals = 0;
            $no = 0;
            foreach ($detail as $keys => $value) {
                $totals += $value->nominal;
                $no = str_split(($keys + 1), 4);
                foreach ($no as $k => $vls) {
                    $vls = trim($vls);
                    $no[$k] = $vls;
                }
                $uraian = str_split($value->uraian, 39);
                foreach ($uraian as $k => $vls) {
                    $vls = trim($vls);
                    $uraian[$k] = $vls;
                }
                $nobg = str_split($value->no_bg, 19);
                foreach ($nobg as $k => $vls) {
                    $vls = trim($vls);
                    $nobg[$k] = $vls;
                }
                $coa = str_split($value->kode_coa, 19);
                foreach ($coa as $k => $vls) {
                    $vls = trim($vls);
                    $coa[$k] = $vls;
                }
                $kurs = str_split(number_format($value->kurs, 2), 9);
                foreach ($kurs as $k => $vls) {
                    $vls = trim($vls);
                    $kurs[$k] = $vls;
                }
                $curr = str_split("{$value->curr}", 9);
                foreach ($curr as $k => $vls) {
                    $vls = trim($vls);
                    $curr[$k] = $vls;
                }
                $nominal = str_split(number_format($value->nominal, 2), 29);
                foreach ($nominal as $k => $vls) {
                    $vls = trim($vls);
                    $nominal[$k] = $vls;
                }

                $counter = 0;
                $temp = [];
                $temp[] = count($no);
                $temp[] = count($uraian);
                $temp[] = count($coa);
                $temp[] = count($kurs);
                $temp[] = count($curr);
                $temp[] = count($nobg);
                $counter = max($temp);
                for ($i = 0; $i < $counter; $i++) {
                    $line = (isset($no[$i])) ? str_pad($no[$i], 5) : str_pad("", 5);
                    $line .= (isset($uraian[$i])) ? str_pad($uraian[$i], 40, " ", STR_PAD_RIGHT) : str_pad("", 40, " ", STR_PAD_RIGHT);
                    $line .= (isset($nobg[$i])) ? str_pad($nobg[$i], 20, " ", STR_PAD_RIGHT) : str_pad("", 20, " ", STR_PAD_RIGHT);
                    $line .= (isset($coa[$i])) ? str_pad($coa[$i], 20, " ", STR_PAD_BOTH) : str_pad("", 20, " ", STR_PAD_BOTH);
                    $line .= (isset($kurs[$i])) ? str_pad("{$kurs[$i]}", 10, " ", STR_PAD_BOTH) : str_pad("", 10, " ", STR_PAD_BOTH);
                    $line .= (isset($curr[$i])) ? str_pad("{$curr[$i]}", 10, " ", STR_PAD_BOTH) : str_pad("", 10, " ", STR_PAD_BOTH);
                    $line .= (isset($nominal[$i])) ? str_pad($nominal[$i], 30, " ", STR_PAD_LEFT) : str_pad("", 30, " ", STR_PAD_LEFT);
                    $printer->text($line . "\n");
                }
            }
            $printer->feed();

            $printer->text(str_pad("", 97));
            $printer->text(str_pad("Total", 10, " ", STR_PAD_BOTH));
            $printer->text(str_pad(number_format($totals, 2), 28, " ", STR_PAD_LEFT));
            $printer->feed();
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad("", 137));
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
            $head = $model->setTables("acc_bank_masuk")->setJoins("acc_bank_masuk_detail", "acc_bank_masuk.id = bank_masuk_id", "left")
                            ->setJoins("currency_kurs", "currency_kurs.id = currency_id", "left")
                            ->setSelects(["acc_bank_masuk.*", "currency_kurs.currency,currency_kurs.kurs", "bank_masuk_id"])
                            ->setWheres(["acc_bank_masuk.no_bm" => $kode])->getDetail();

            if (!$head) {
                throw new \exception("Data No Bank Masuk {$kode} tidak ditemukan", 500);
            }
            if (!$head->bank_masuk_id) {
                throw new \exception("Data Detail Harus Terisi", 500);
            }

            $this->_module->startTransaction();
            $this->_module->lock_tabel("token_increment WRITE,acc_bank_masuk WRITE,acc_bank_masuk_detail WRITE,log_history WRITE"
                    . ",main_menu_sub READ,acc_jurnal_entries_items WRITE,acc_jurnal_entries WRITE,currency_kurs READ,acc_giro_masuk_detail WRITE,setting READ");

            $model->update(["status" => $status]);
            switch ($status) {
                case "confirm":
                    if ($head->status !== "draft") {
                        throw new \exception("Data No Bank Masuk {$kode} dalam status {$head->status}", 500);
                    }

                    $jurnalDB = new $this->m_global;
                    $model = clone $jurnalDB;
                    $getGiroId = $model->setTables("acc_bank_masuk_detail")->setSelects(["GROUP_CONCAT(giro_masuk_detail_id) as gids"])->setWheres(["bank_masuk_id" => $head->id])
                            ->getDetail();
                    if ($getGiroId->gids !== null) {
                        $checkGiroCair = $model->setTables("acc_giro_masuk_detail")->setWhereRaw("id in ({$getGiroId->gids})")
                                        ->setWheres(["cair" => 1])->getDetail();
                        if ($checkGiroCair) {
                            throw new \exception("Data Giro dipilih sudah dalam status Cair", 500);
                        }
                    }
                    $giro = [];
                    $items = $model->setTables("acc_bank_masuk_detail")->setJoins("currency_kurs", "currency_kurs.id = currency_id", "left")
                                    ->setSelects(["acc_bank_masuk_detail.*", "currency_kurs.currency"])
                                    ->setWheres(["bank_masuk_id" => $head->id])->getData();

                    if ($head->jurnal !== "") {
                        $jurnal = $head->jurnal;
                        $stt = "edit";
                    } else {
                        if (!$jurnal = $this->token->noUrut("jurnal_acc_bm", date('y', strtotime($head->tanggal)) . '/' . date('m', strtotime($head->tanggal)), true)
                                        ->generate("BM/", '/%05d')->get()) {
                            throw new \Exception("No jurnal tidak terbuat", 500);
                        }
                        $stt = "create";
                    }
                    $partner = (strlen($head->partner_nama) > 1) ? $head->partner_nama : $head->lain2;

                    $jurnalData = ["kode" => $jurnal, "periode" => date("Y/m", strtotime($head->tanggal)),
                        "origin" => "{$kode}", "status" => "posted", "tanggal_dibuat" => $head->tanggal, "tipe" => "BM",
                        "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => "{$partner}"];

                    $jurnalItems = [];
                    $nominal_rp = 0;

                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "{$head->transinfo}",
                        "reff_note" => "",
                        "partner" => ($head->partner_id ?? ""),
                        "kode_coa" => $head->kode_coa,
                        "posisi" => "D",
                        "nominal_curr" => 0,
                        "kurs" => $items[0]->kurs ?? 1,
                        "kode_mua" => "IDR",
                        "nominal" => 0,
                        "row_order" => 1
                    );
                    $nominal_curr = 0;
                    $curr = "IDR";
                    foreach ($items as $key => $item) {
                        $giro[] = $item->giro_masuk_detail_id;
                        $uraian = $item->uraian;
                        $uraian .= ($item->bank !== "") ? " - {$item->bank}" : "";
                        $uraian .= ($item->no_rek !== "") ? " - {$item->no_rek}" : "";
                        $uraian .= ($item->no_bg !== "") ? " - {$item->no_bg}" : "";
                        $nominal_rp += ($item->nominal * $item->kurs);
                        $nominal_curr += $item->nominal;
                        $curr = $item->currency;
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "{$uraian}",
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
                    $jurnalItems[0]["nominal_curr"] = $nominal_curr;
                    $jurnalItems[0]["nominal"] = $nominal_rp;
                    $jurnalItems[0]["kode_mua"] = $curr;
                    if ($head->jurnal !== "") {
                        $jurnalDB->setTables("acc_jurnal_entries")->setWheres(["kode" => $jurnal])->update($jurnalData);
                        $jurnalDB->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $jurnal])->delete();
                    } else {
                        $jurnalDB->setTables("acc_jurnal_entries")->save($jurnalData);
                        $model->setTables("acc_bank_masuk")->setWheres(["id" => $head->id])->update(["jurnal" => $jurnal]);
                        $this->_module->gen_history_new($sub_menu, $kode, 'edit', "No Jurnal : {$jurnal}", $username);
                    }
                    $model->setTables("acc_giro_masuk_detail")->setWhereIn("id", $giro)->update(["cair" => 1]);

                    $jurnalDB->setTables("acc_jurnal_entries_items")->saveBatch($jurnalItems);
                    $log = "Header -> " . logArrayToString("; ", $jurnalData);
                    $log .= "\nDETAIL -> " . logArrayToString("; ", $jurnalItems);
                    $this->_module->gen_history_new("jurnal_entries", $jurnal, $stt, $log, $username);
                    break;

                case "draft":
                    if ($head->status !== "cancel") {
                        throw new \exception("Data No Bank Masuk {$kode} dalam status {$head->status}", 500);
                    }
                    $this->validasiPin($pin, "Simpan Draft Hanya bisa dilakukan Oleh Supervisor", $head->tanggal);
                    break;

                default:
                    $this->validasiPin($pin, "Batal / Cancel Data Hanya bisa dilakukan Oleh Supervisor", $head->tanggal);

                    $item = $model->setTables("acc_bank_masuk_detail")->setWheres(["bank_masuk_id" => $head->id])
                                    ->setSelects(['GROUP_CONCAT(giro_masuk_detail_id) as gids'])->getDetail();
                    if ($item->gids !== null) {
                        $model->setTables("acc_giro_masuk_detail")->setWheres(["cair" => 1])->setWhereRaw("id in ({$item->gids})")
                                ->update(["cair" => 0]);
                    }

                    $lunas = $model->setTables("acc_bank_masuk_detail")->setWheres(["bank_masuk_id" => $head->id, "lunas" => 1])->getDetail();

                    if ($lunas) {
                        throw new \exception("Tidak Bisa Cancel / Batal. Item sudah sudah masuk pelunasan", 500);
                    }

                    $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $head->jurnal])->update(["status" => "unposted"]);
                    $this->_module->gen_history_new("jurnal_entries", $head->jurnal, 'edit', "Merubah Status Ke unposted dari Kas Bank Masuk", $username);
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
    }
}
