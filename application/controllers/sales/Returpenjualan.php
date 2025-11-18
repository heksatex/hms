<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Fakturpenjualan
 *
 * @author RONI
 */
require FCPATH . 'vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mpdf\Mpdf;

class Returpenjualan extends MY_Controller {

    protected $tipe = [
        "lokal" => "Lokal",
        "ekspor" => "Ekspor",
        "lain-lain" => "Lain - Lain",
        "makloon" => "Makloon"
    ];
    protected $sj_tipe = [
        "ekspor" => [
            "SJ/HI/03"
        ],
        "lokal" => [
            "SJ/HI/07",
            "SAMPLE/HI",
            "SJM/HI/07",
            "SJT/HI/07",
            "MAKLOON/HI"
        ],
        "lain-lain" => [
            "SJ/HI/P/00"
        ],
        "makloon" => []
    ];
    protected $uomLot = [
        "gul" => "Gulung",
        "pcs" => "Pcs",
        "roll" => "Roll",
        "ikat" => "Ikat",
        "bks" => "Bungkus",
        "box" => "Box"
    ];
    protected $valForm = [
        [
            'field' => 'tipe',
            'label' => 'Tipe Penjualan',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
        [
            'field' => 'no_sj',
            'label' => 'No SJ',
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
            'field' => 'kurs',
            'label' => 'Kurs',
            'rules' => ['required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
        [
            'field' => 'kurs_nominal',
            'label' => 'Kurs Nominal',
            'rules' => ['trim', 'required', 'regex_match[/^\d*\.?\d*$/]'],
            'errors' => [
                'required' => '{field} Harus dipilih',
                "regex_match" => "{field} harus berupa number / desimal"
            ]
        ]
    ];
    protected $paymentTerm = [
        ["kode" => 0,
            "nama" => "0"],
        ["kode" => 7,
            "nama" => "7"],
        ["kode" => 14,
            "nama" => "14"],
        ["kode" => 30,
            "nama" => "30"],
        ["kode" => 45,
            "nama" => "45"],
        ["kode" => 60,
            "nama" => "60"],
        ["kode" => 90,
            "nama" => "90"],
        ["kode" => 120,
            "nama" => "120"],
    ];

    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->load->driver('cache', array('adapter' => 'file'));
        $this->config->load('additional');
        $this->load->library("token");
    }

    public function index() {
        $data['id_dept'] = 'ACCRPJ';
        $model = new $this->m_global;
        $data["sales"] = $model->setTables("mst_sales_group")->setWheres(["view" => "1"])->setSelects(["kode_sales_group", "nama_sales_group"])
                        ->setOrder(["kode_sales_group"])->getData();
        $this->load->view('sales/v_retur_penjualan', $data);
    }

    public function add() {
        $model = new $this->m_global;
        $data['id_dept'] = 'ACCRPJ';
        $data["tipe"] = $this->tipe;
        $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
        $this->load->view('sales/v_retur_penjualan_add', $data);
    }

    public function edit($id) {
        try {
            $data["user"] = (object) $this->session->userdata('nama');
            $data["id"] = $id;
            $data['id_dept'] = 'ACCRPJ';
            $data["tipe"] = $this->tipe;
            $data["uomLot"] = $this->uomLot;
            $data["payment_term"] = $this->paymentTerm;
            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $data["sales"] = $model->setTables("mst_sales_group")->setWheres(["view" => "1"])->setSelects(["kode_sales_group", "nama_sales_group"])
                            ->setOrder(["kode_sales_group"])->getData();
            $data["datas"] = $model->setTables("acc_retur_penjualan fj")
                            ->setWheres(["no_retur" => $kode])->getDetail();
            if (!$data['datas']) {
                show_404();
            }
            $data["jurnal"] = $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $data['datas']->jurnal])->getDetail();
            $data["detail"] = $model->setTables("acc_retur_penjualan_detail fjd")->setOrder(["fjd.uraian"=>"asc"])
                            ->setJoins("acc_coa", "kode_coa = no_acc", "left")
                            ->setWheres(["retur_id" => $data['datas']->id])
                            ->setSelects(["fjd.*", "acc_coa.nama as coa_nama"])->getData();

            $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
            $data["taxs"] = $model->setTables("tax")->setWheres(["type_inv" => "sale"])->setOrder(["nama" => "asc"])->getData();
            $this->load->view('sales/v_retur_penjualan_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function get_view_sj() {
        $tipe = $this->input->post("tipe");
        $view = $this->load->view('sales/modal/v_list_sj_retur', ["tipe" => $tipe], true);
        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['data' => $view]));
    }

    public function list_sj() {
        try {
            $tipe = $this->input->post("tipe");
            $model = new $this->m_global;
            $model->setTables("delivery_order do")->setJoins("delivery_order_detail dod", "(do.id = do_id and dod.status = 'retur')")
                    ->setJoins("picklist p", "p.no = do.no_picklist")
                    ->setJoins("mst_sales_group msg", "msg.kode_sales_group = p.sales_kode", "left")
                    ->setJoins("partner pr", "pr.id = p.customer_id", "left")
                    ->setSearch(["do.no_sj", "do.no_picklist", "pr.nama", "msg.nama_sales_group"])
                    ->setOrders([null, "do.no_sj", "do.no_picklist", "pr.nama", "msg.nama_sales_group"])
                    ->setGroups(["do.no_sj"])->setOrder(["do.tanggal_dokumen" => "desc"])
                    ->setSelects(["do.no_sj,do.no_picklist", "pr.nama as buyer", "msg.nama_sales_group as marketing"])
                    ->setWheres(["do.status" => "done"])
                    ->setWhereRaw("do.no_sj not in (select no_sj from acc_retur_penjualan)");
            $exp = implode("|", ($this->sj_tipe[$tipe] ?? []));
            switch ($tipe) {
                case "makloon":
//                    $model->setWheres(["no_sj REGEXP" => $exp]);
                    break;
                case "":
                    throw new Exception("", 500);
                    break;
                default:
                    $model->setWheres(["no_sj REGEXP" => $exp]);
                    break;
            }
            $data = [];
            $no = $_POST['start'];
            foreach ($model->getData() as $field) {
                $no += 1;
                $data[] = [
                    $no,
                    $field->no_sj,
                    $field->no_picklist,
                    $field->buyer,
                    $field->marketing,
                    "<button type='button' class='btn btn-success btn-sm pilih-sj' data-sj='{$field->no_sj}'>Pilih</button>"
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $model->getDataCountAll("do.no_sj"),
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

    public function list_data() {
        try {
            $data = array();
            $model = new $this->m_global;
            $model->setTables("acc_retur_penjualan")->setJoins("mst_status", "mst_status.kode = acc_retur_penjualan.status", "left")
                    ->setOrder(["acc_retur_penjualan.tanggal" => "desc"])->setSearch(["no_retur", "no_faktur_pajak", "no_sj", "partner_nama", "no_retur_internal"])
                    ->setOrders([null, "no_retur", "no_faktur_pajak", "tanggal", "no_sj", "marketing_nama", "partner_nama"])->setSelects(["acc_retur_penjualan.*", "nama_status"]);
            $no = $_POST['start'];
            $tanggal = $this->input->post("tanggal");
            $marketing = $this->input->post("marketing");
            if ($tanggal !== "") {
                $tanggals = explode(" - ", $tanggal);
                $model->setWheres(["date(tanggal) >=" => $tanggals[0], "date(tanggal) <=" => $tanggals[1]]);
            }
            if ($marketing !== "") {
                $model->setWheres(["marketing_kode" => $marketing]);
            }
            foreach ($model->getData() as $key => $value) {
                $no += 1;
                $kode_encrypt = encrypt_url($value->no_retur);
                $fk = ($value->no_retur_internal === '') ? $value->no_retur : $value->no_retur_internal;
                $data [] = [
                    $no,
                    "<a href='" . base_url("sales/returpenjualan/edit/{$kode_encrypt}") . "'>{$fk}</a>",
                    $value->no_faktur_pajak,
                    $value->tanggal,
                    $value->no_sj,
                    $value->marketing_nama,
                    $value->partner_nama,
                    $value->nama_status
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $model->getDataCountAll("id"),
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

    public function addsj() {
        try {
            $sj = $this->input->post("no");
            $model = new $this->m_global;
            $data = $model->setTables("delivery_order do")->setJoins("picklist p", "p.no = do.no_picklist")
                            ->setJoins("mst_sales_group msg", "msg.kode_sales_group = p.sales_kode", "left")
                            ->setJoins("partner pr", "pr.id = p.customer_id", "left")
                            ->setSelects(["customer_id,pr.nama as customer", "p.sales_kode,msg.nama_sales_group as sales_nama"])
                            ->setSelects(["p.keterangan"])->setWheres(["do.status" => "done", "do.no_sj" => $sj])->getDetail();
            if (!$data) {
                throw new \Exception('Data SJ tidak ditemukan', 500);
            }
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
            $username = $this->session->userdata('username');

            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $tipe = $this->input->post("tipe");
            $nosj = $this->input->post("no_sj");
            $poCust = $this->input->post("po_cust");
            $marKode = $this->input->post("marketing_kode");
            $marNama = $this->input->post("marketing_nama");
            $tanggal = $this->input->post("tanggal");
            $customer = $this->input->post("customer");
            $customerNama = $this->input->post("customer_nama");
            $noReturInternal = $this->input->post("no_retur_internal");
            $noFakturPajak = $this->input->post("no_faktur_pajak");
            $kurs = $this->input->post("kurs");
            $kursNominal = $this->input->post("kurs_nominal");
            $dariSJ = "0";
            $model = new $this->m_global;
            $this->_module->startTransaction();
            $lock = "token_increment WRITE,main_menu_sub READ, log_history WRITE,delivery_order do WRITE,delivery_order_detail dod READ,picklist_detail pd READ,"
                    . "acc_retur_penjualan WRITE, acc_retur_penjualan_detail WRITE";
            $this->_module->lock_tabel($lock);
            $checkSJ = $model->setTables("delivery_order do")->setJoins("delivery_order_detail dod", "(do.id = do_id and dod.status = 'retur')", "left")
                            ->setJoins("picklist_detail pd", "picklist_detail_id = pd.id")->setWheres(["do.no_sj" => $nosj])
                            ->setGroups(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom"])
                            ->setSelects(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom", "faktur"])
                            ->setSelects(["count(dod.barcode_id) as total_lot", "sum(pd.qty) as total_qty"])->getData();
            if (count($checkSJ) > 0) {
                if ((string) $checkSJ[0]->faktur === "0") {
                    throw new \Exception("SJ {$nosj} Belum masuk Faktur penjualan", 500);
                }
                $dariSJ = "1";
            }
            if ($noReturInternal !== "") {
                $fk = $model->setTables("acc_retur_penjualan")->setWheres(["no_retur_internal" => $noReturInternal])->getDetail();
                if ($fk) {
                    throw new \Exception("No Retur Internal sudah terpakai", 500);
                }
            }

            if (!$noRetur = $this->token->noUrut('returpenjualan', date('y', strtotime($tanggal)) . '/' . getRomawi(date('m', strtotime($tanggal))), true)
                            ->generate('RP/', '/%04d')->get()) {
                throw new \Exception("No Retur tidak terbuat", 500);
            }

            $header = [
                "no_retur" => $noRetur,
                "no_retur_internal" => $noReturInternal,
                "tanggal" => $tanggal,
                "tipe" => $tipe,
                "no_sj" => $nosj,
                "po_cust" => $poCust,
                "marketing_kode" => $marKode,
                "marketing_nama" => $marNama,
                "partner_id" => $customer,
                "partner_nama" => $customerNama,
                "no_faktur_pajak" => $noFakturPajak,
                "kurs" => $kurs,
                "kurs_nominal" => $kursNominal,
                "create_date" => date("Y-m-d H:i:s"),
                "dari_sj" => $dariSJ
            ];

            $idRetur = $model->setTables("acc_retur_penjualan")->save($header);
            $detail = [];
            foreach ($checkSJ as $key => $value) {
                $detail[] = [
                    "retur_id" => $idRetur,
                    "retur_no" => $noRetur,
                    "uraian" => "{$value->corak_remark} / {$value->lebar_jadi} {$value->uom_lebar_jadi}",
                    "warna" => $value->warna_remark,
                    "qty_lot" => $value->total_lot,
                    "lot" => "roll",
                    "qty" => $value->total_qty,
                    "uom" => $value->uom
                ];
            }
            $model->setTables("acc_retur_penjualan_detail")->saveBatch($detail);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $noRetur, 'create', "DATA -> " . logArrayToString("; ", $header), $username);

            $url = site_url("sales/returpenjualan/edit/" . encrypt_url($noRetur));
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

    public function update($id) {
        try {
            $kode = decrypt_url($id);
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $users = (object) $this->session->userdata('nama');
            $noAcc = $this->input->post("noacc");
            $nominalDiskon = $this->input->post("nominaldiskon");
            unset($this->valForm[2]); //unset validation  no faktur
            if ($nominalDiskon !== "") {
                $this->valForm = array_merge($this->valForm, [
                    [
                        'field' => 'nominaldiskon',
                        'label' => 'Nominal Diskon',
                        'rules' => ['trim', 'regex_match[/^\d*\.?\d*$/]'],
                        'errors' => [
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                ]);
            }
            if (count($noAcc) > 0) {
                $this->valForm = array_merge($this->valForm, [
                    [
                        'field' => 'uraian[]',
                        'label' => 'Uraian',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi'
                        ]
                    ],
                    [
                        'field' => 'uomlot[]',
                        'label' => 'Uom Lot',
                        'rules' => ['trim', 'required'],
                        'errors' => [
                            'required' => '{field} Pada Item harus dipilih'
                        ]
                    ],
                    [
                        'field' => 'harga[]',
                        'label' => 'Harga',
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'], ///^-?\d*\.?\d*$/
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
            $taxVal = $this->input->post("tax_value");
            $nominalDiskon = $this->input->post("nominaldiskon");
            $tipediskon = $this->input->post("tipediskon");
            $nosj = $this->input->post("no_sj");
            $ids = $this->input->post("ids");
            $nosjold = $this->input->post("no_sj_old");
            $noReturInternal = $this->input->post("no_retur_internal");
            $model = new $this->m_global;
            if ($noReturInternal !== "") {
                $fk = $model->setTables("acc_retur_penjualan")->setWheres(["no_retur_internal" => $noReturInternal, "id <>" => $ids])->getDetail();
                if ($fk) {
                    throw new \Exception("No Retur Internal sudah terpakai", 500);
                }
            }
            $header = [
                "tipe" => $this->input->post("tipe"),
                "no_sj" => $nosj,
                "po_cust" => $this->input->post("po_cust"),
                "marketing_kode" => $this->input->post("marketing_kode"),
                "marketing_nama" => $this->input->post("marketing_nama"),
                "partner_id" => $this->input->post("customer"),
                "partner_nama" => $this->input->post("customer_nama"),
                "no_faktur_pajak" => $this->input->post("no_faktur_pajak"),
                "no_retur_internal" => $noReturInternal,
                "kurs" => $this->input->post("kurs"),
                "kurs_nominal" => $this->input->post("kurs_nominal"),
                "tipe_diskon" => $tipediskon,
                "nominal_diskon" => $nominalDiskon,
                "tax_id" => $this->input->post("tax"),
                "tax_value" => $taxVal,
                "dpp_lain" => 0,
                "ppn" => 0,
                "final_total" => 0,
                "payment_term" => $this->input->post("payment_term"),
                "foot_note" => $this->input->post("footnote")
            ];
            $ppns = 0;
            $detail = [];
            $this->_module->startTransaction();
            $new = true;
            if ($nosj === $nosjold) {
                if (count($noAcc) > 0) {
                    $new = false;
                    $ppns = 0;
                    $grandDiskon = 0;
                    $grandTotal = 0;
                    $grandDiskonPpn = 0;
                    $qty = $this->input->post("qty");
                    $harga = $this->input->post("harga");
                    $dppSet = $model->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
                    foreach ($noAcc as $key => $value) {
                        $hrg = str_replace(",", "", $harga[$key]);
                        $jumlah = $qty[$key] * $hrg;
                        $grandTotal += $jumlah;
                        $ddskon = ($tipediskon === "%") ? ($jumlah * ($nominalDiskon / 100)) : $nominalDiskon;
                        $grandDiskon += $ddskon;
                        $dpp = ($jumlah * 11) / 12;
                        if (!$dppSet) {
                            $pajak = ($jumlah) * $taxVal;
                            $ppn_diskon = ($ddskon) * $taxVal;
                        } else {
                            $pajak = $dpp * $taxVal;
                            $dppDikson = ($ddskon * 11) / 12;
                            $ppn_diskon = $dppDikson * $taxVal;
                        }
                        $header["ppn"] += ($header["kurs_nominal"] > 1) ? $pajak : round($pajak);
                        $header["dpp_lain"] += ($header["kurs_nominal"] > 1) ? $dpp : round($dpp);
                        $grandDiskonPpn += $ppn_diskon;
                        $totalHarga = (($jumlah - $ddskon) + ($pajak - $ppn_diskon));
                        $header["final_total"] += ($header["kurs_nominal"] > 1) ? $totalHarga : round($totalHarga);
                        $detail[] = [
                            "uraian" => $this->input->post("uraian")[$key],
                            "warna" => $this->input->post("warna")[$key],
                            "no_po" => $this->input->post("nopo")[$key],
                            "qty_lot" => $this->input->post("qtylot")[$key],
                            "qty" => $this->input->post("qty")[$key],
                            "lot" => $this->input->post("uomlot")[$key],
                            "harga" => $hrg,
                            "no_acc" => $value,
                            "jumlah" => $jumlah,
                            "pajak" => $pajak,
                            "total_harga" => $totalHarga,
                            "dpp_lain" => $dpp,
                            "diskon" => $ddskon,
                            "id" => $this->input->post("detail_id")[$key],
                            "diskon_ppn" => $ppn_diskon
                        ];
                    }
                    if ($header["kurs_nominal"] > 1) {
                        $header["total_piutang_valas"] = $header["final_total"];
                        $header["piutang_valas"] = $header["final_total"];
                    }
                    $header["total_piutang_rp"] = round($header["final_total"] * $header["kurs_nominal"]);
                    $header["piutang_rp"] = round($header["final_total"] * $header["kurs_nominal"]);

                    $header["diskon"] = ($header["kurs_nominal"] > 1) ? $grandDiskon : round($grandDiskon);
                    $header["diskon_ppn"] = $grandDiskonPpn;
                    $header["grand_total"] = ($header["kurs_nominal"] > 1) ? $grandTotal : round($grandTotal);

                    $model->setTables("acc_retur_penjualan_detail")->updateBatch($detail, "id");
                }
            } 
            if ($new)  {
                $header["grand_total"] = 0;
                $header["ppn"] = 0;
                $header["dpp_lain"] = 0;
                $header["diskon"] = 0;
                $header["final_total"] = 0;
                $header["diskon_ppn"] = 0;
                $header["total_piutang_valas"] = 0;
                $header["piutang_valas"] = 0;
                $header["total_piutang_rp"] = 0;
                $header["piutang_rp"] = 0;
                $lock = "main_menu_sub READ, log_history WRITE,delivery_order do WRITE,delivery_order_detail dod READ,picklist_detail pd READ,acc_retur_penjualan WRITE, acc_retur_penjualan_detail WRITE";
                $this->_module->lock_tabel($lock);

                $checkSJ = $model->setTables("delivery_order do")->setJoins("delivery_order_detail dod", "(do.id = do_id and dod.status = 'retur')", "left")
                                ->setJoins("picklist_detail pd", "picklist_detail_id = pd.id")->setWheres(["do.no_sj" => $nosj])
                                ->setGroups(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom"])->setOrder(["pd.corak_remark"=>"asc","pd.warna_remark"=>"asc"])
                                ->setSelects(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom", "faktur"])
                                ->setSelects(["count(dod.barcode_id) as total_lot", "sum(pd.qty) as total_qty"])->getData();
                if (count($checkSJ) > 0) {
                    if ((string) $checkSJ[0]->faktur === "0") {
                        throw new \Exception("SJ {$nosj} belum masuk faktur penjualan", 500);
                    }
                    $header["dari_sj"] = "1";
                }

                $detail = [];
                foreach ($checkSJ as $key => $value) {
                    $detail[] = [
                        "retur_id" => $ids,
                        "retur_no" => $kode,
                        "uraian" => "{$value->corak_remark} / {$value->lebar_jadi} {$value->uom_lebar_jadi}",
                        "warna" => $value->warna_remark,
                        "qty_lot" => $value->total_lot,
                        "lot" => "roll",
                        "qty" => $value->total_qty,
                        "uom" => $value->uom
                    ];
                }
                $model->setTables("acc_retur_penjualan_detail")->setWheres(["retur_id" => $ids])->delete();
                if (count($detail) > 0)
                    $model->setTables("acc_retur_penjualan_detail")->saveBatch($detail);
            }

            $model->setTables("acc_retur_penjualan")->setWheres(["no_retur" => $kode])->update($header);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $log = "DATA -> " . logArrayToString("; ", $header);
            $log .= "\n";
            $log .= "\nDETAIL -> " . logArrayToString("; ", $detail);
            $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function update_status($id) {
        try {
            $kode = decrypt_url($id);
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $status = $this->input->post("status");
            $model = new $this->m_global;
            $data = $model->setTables("acc_retur_penjualan")->setWheres(["no_retur" => $kode])
                            ->setJoins("currency_kurs", "currency_kurs.id = acc_retur_penjualan.kurs", "left")
                            ->setSelects(["acc_retur_penjualan.*", "currency_kurs.currency as nama_kurs"])->getDetail();
            if (!$data) {
                throw new \Exception("Data Retur tidak ditemukan", 500);
            }
            $status = strtolower($status);
            $this->_module->startTransaction();
            $lock = "main_menu_sub READ, log_history WRITE,delivery_order do WRITE,delivery_order WRITE,acc_retur_penjualan WRITE,"
                    . "acc_retur_penjualan_detail WRITE,token_increment WRITE,acc_jurnal_entries_items WRITE,acc_jurnal_entries WRITE,"
                    . "setting READ";
            $this->_module->lock_tabel($lock);
            switch ($status) {
                case "confirm":
                    if ($data->status !== "draft") {
                        throw new \Exception("Retur Harus dalam status Draft", 500);
                    }
                    $dataDetailHarga = $model->setTables("acc_retur_penjualan_detail")->setWheres(["retur_no" => $kode, "harga < " => 0])->getDetail();
                    if ($dataDetailHarga) {
                        throw new \Exception("Harga Untuk Uraian {$dataDetailHarga->uraian} masih 0", 500);
                    }

                    $CheckCoa = $model->setWheres(["no_acc" => "", "retur_no" => $kode], true)->getDetail();
                    if ($CheckCoa) {
                        throw new \Exception("No ACC Harap diisi terlebih dahulu", 500);
                    }
                    $getCoaDefault = $model->setTables("setting")->setWheres(["setting_name" => "coa_penjualan_{$data->tipe}"])->getDetail();
                    if (!$getCoaDefault)
                        throw new \Exception("Coa Penjualan {$data->tipe} belum ditentukan", 500);

                    $ceksSj = $model->setTables("acc_retur_penjualan")->setWheres(["no_sj" => $data->no_sj, "status" => "confirm"])->getDetail();

                    if ($ceksSj)
                        throw new \Exception("No Surat jalan selesai melakukan retur", 500);

                    if ($data->jurnal !== "") {
                        $jurnal = $data->jurnal;
                        $stt = "edit";
                    } else {
                        if (!$jurnal = $this->token->noUrut("penjualan_", date('y', strtotime($data->tanggal)) . '/' . date('m', strtotime($data->tanggal)), true)
                                        ->generate("PJ/", '/%05d')->get()) {
                            throw new \Exception("No jurnal tidak terbuat", 500);
                        }
                        $stt = "create";
                    }

                    $jurnalData = ["kode" => $jurnal, "periode" => date("Y/m", strtotime($data->tanggal)),
                        "origin" => "{$data->no_retur_internal}", "status" => "posted", "tanggal_dibuat" => $data->tanggal, "tipe" => "RPJ",
                        "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => "{$data->partner_nama}"];

                    $detail = $model->setTables("acc_retur_penjualan_detail")->setWheres(["retur_no" => $kode])->getData();
                    $jurnalItems = [];

                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "Hutamg",
                        "reff_note" => "",
                        "partner" => $data->partner_id,
                        "kode_coa" => $getCoaDefault->value,
                        "posisi" => "C",
                        "nominal_curr" => ($data->grand_total + $data->ppn),
                        "kurs" => $data->kurs_nominal,
                        "kode_mua" => $data->nama_kurs,
                        "nominal" => round(($data->grand_total + $data->ppn) * $data->kurs_nominal),
                        "row_order" => 1
                    );

                    $getCoaDefaultPpnDisc = $model->setTables("setting")->setWheres(["setting_name" => "coa_penjualan_ppn_diskon"])->getDetail();
                    if ($data->diskon_ppn > 0) {
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "PPN Diskon",
                            "reff_note" => "",
                            "partner" => $data->partner_id,
                            "kode_coa" => $getCoaDefaultPpnDisc->value,
                            "posisi" => "C",
                            "nominal_curr" => $data->diskon_ppn,
                            "kurs" => $data->kurs_nominal,
                            "kode_mua" => $data->nama_kurs,
                            "nominal" => round($data->diskon_ppn * $data->kurs_nominal),
                            "row_order" => (count($jurnalItems) + 1)
                        );
                    }
                    $getCoaDefaultDppDisc = $model->setTables("setting")->setWheres(["setting_name" => "coa_penjualan_dpp_diskon"])->getDetail();
                    if ($data->diskon > 0) {
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "DPP Diskon",
                            "reff_note" => "",
                            "partner" => $data->partner_id,
                            "kode_coa" => $getCoaDefaultDppDisc->value,
                            "posisi" => "C",
                            "nominal_curr" => $data->diskon,
                            "kurs" => $data->kurs_nominal,
                            "kode_mua" => $data->nama_kurs,
                            "nominal" => round($data->diskon * $data->kurs_nominal),
                            "row_order" => (count($jurnalItems) + 1)
                        );
                    }
                    $allDiskon = $data->diskon_ppn + $data->diskon;
                    if ($allDiskon > 0) {
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "Diskon",
                            "reff_note" => "",
                            "partner" => $data->partner_id,
                            "kode_coa" => $getCoaDefault->value,
                            "posisi" => "D",
                            "nominal_curr" => $allDiskon,
                            "kurs" => $data->kurs_nominal,
                            "kode_mua" => $data->nama_kurs,
                            "nominal" => round($allDiskon * $data->kurs_nominal),
                            "row_order" => (count($jurnalItems) + 1)
                        );
                    }
                    if ($data->ppn > 0) {
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "PPN",
                            "reff_note" => "",
                            "partner" => $data->partner_id,
                            "kode_coa" => $getCoaDefaultPpnDisc->value,
                            "posisi" => "D",
                            "nominal_curr" => $data->ppn,
                            "kurs" => $data->kurs_nominal,
                            "kode_mua" => $data->nama_kurs,
                            "nominal" => round($data->ppn * $data->kurs_nominal),
                            "row_order" => (count($jurnalItems) + 1)
                        );
                    }
                    foreach ($detail as $key => $value) {
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "{$value->uraian} {$value->warna} {$value->qty} {$value->uom}",
                            "reff_note" => "",
                            "partner" => $data->partner_id,
                            "kode_coa" => $value->no_acc,
                            "posisi" => "D",
                            "nominal_curr" => $value->jumlah,
                            "kurs" => $data->kurs_nominal,
                            "kode_mua" => $data->nama_kurs,
                            "nominal" => round($value->jumlah * $data->kurs_nominal),
                            "row_order" => (count($jurnalItems) + 1)
                        );
                    }

                    if ($data->jurnal !== "") {
                        $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $jurnal])->update($jurnalData);
                        $model->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $jurnal])->delete();
                    } else {
                        $model->setTables("acc_jurnal_entries")->save($jurnalData);
                        $model->setTables("acc_retur_penjualan")->setWheres(["id" => $data->id])->update(["jurnal" => $jurnal]);
                        $this->_module->gen_history_new($sub_menu, $kode, 'edit', "No Jurnal : {$jurnal}", $username);
                    }

//                    $model->setTables("delivery_order")->setWheres(["no_sj" => $data->no_sj, "status" => "done"])->update(["faktur" => 1]);
                    $model->setTables("acc_jurnal_entries_items")->saveBatch($jurnalItems);
                    $log = "Header -> " . logArrayToString("; ", $jurnalData);
                    $log .= "\nDETAIL -> " . logArrayToString("; ", $jurnalItems);
                    $this->_module->gen_history_new("jurnal_entries", $jurnal, "{$stt}", $log, $username);
                    break;

                case "draft":
                    if ($data->status !== "cancel") {
                        throw new \exception("Data Retur Penjualan {$kode} dalam status {$data->status}", 500);
                    }
                    break;
                default:
                    if ($data->lunas == 1) {
                        throw new \exception("Data Retur Penjualan {$kode} sudah masuk pada pelunasan", 500);
                    }
                    $finalTotal = $data->final_total * $data->kurs_nominal;
                    if ($finalTotal !== $data->piutang_rp) {
                        throw new \exception("Data Retur Penjualan {$kode} sudah masuk pada pelunasan.", 500);
                    }
                    $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $data->jurnal])->update(["status" => "unposted"]);
//                    $model->setTables("delivery_order")->setWheres(["no_sj" => $data->no_sj, "status" => "done"])->update(["faktur" => 0]);
                    $this->_module->gen_history_new("jurnal_entries", $data->jurnal, 'edit', "Merubah Status Ke unposted dari penjualan", $username);
                    break;
            }
            $model->setTables("acc_retur_penjualan")->setWheres(["no_retur" => $kode])->update(["status" => strtolower($status)]);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $kode, "edit", "Update status ke {$status}", $username);

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function update_faktur($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            $model = new $this->m_global;
            $kode = decrypt_url($id);
            $pajak = $this->input->post("pajak");
            $update = [
                "no_faktur_pajak" => $pajak
            ];
            $model->setTables("acc_retur_penjualan")->setWheres(["no_retur" => $kode])->update($update);

            $log = "Update " . logArrayToString("; ", $update);
            $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            log_message("error", json_encode($ex));
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function split($id) {
        try {
            $model = new $this->m_global;
            $ids = $this->input->post("ids");
            $detail = $model->setTables("acc_retur_penjualan_detail fjd")
                            ->setJoins("acc_coa", "kode_coa = no_acc", "left")
                            ->setSelects(["fjd.*", "acc_coa.nama as coa_nama"])
                            ->setWheres(["id" => $ids])->getDetail();
            if (!$detail) {
                throw new \Exception('Data Item tidak ditemukan', 500);
            }
            $html = $this->load->view('sales/modal/v_split_item_fp', ["data" => $detail, "id" => $id, "uomLot" => $this->uomLot], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => "")));
        }
    }

    public function save_split($id) {
        try {
            $validation = [
                [
                    'field' => 'qty',
                    'label' => 'Qty',
                    'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
                    'errors' => [
                        'required' => '{field} Harus diisi',
                        "regex_match" => "{field} harus berupa number / desimal"
                    ]
                ],
                [
                    'field' => 'qty_lot',
                    'label' => 'Qty LOT',
                    'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
                    'errors' => [
                        'required' => '{field} Harus diisi',
                        "regex_match" => "{field} harus berupa number / desimal"
                    ]
                ]
            ];

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode = decrypt_url($id);
            $ids = $this->input->post("ids");
            $qty = $this->input->post("qty");
            $qtyLot = $this->input->post("qty_lot");
            $uomLot = $this->input->post("uom_lot");
            $noAcc = $this->input->post("no_acc");

            $this->_module->startTransaction();
            $lock = "acc_retur_penjualan READ, acc_retur_penjualan_detail WRITE,user READ, main_menu_sub READ, log_history WRITE,setting READ";
            $this->_module->lock_tabel($lock);
            $model = new $this->m_global;
            $getDetail = $model->setTables("acc_retur_penjualan_detail")->setJoins("acc_retur_penjualan", "retur_id = acc_retur_penjualan.id")
                            ->setSelects(["acc_retur_penjualan_detail.*", "nominal_diskon,tipe_diskon,tax_value"])
                            ->setWheres(["acc_retur_penjualan_detail.id" => $ids])->getDetail();
            if ($getDetail === null) {
                throw new Exception('Data Tidak ditemukan', 500);
            }
            $hasilKurang = $getDetail->qty - $qty;
            if ($hasilKurang <= 0) {
                throw new Exception('Hasil Split QTY Kurang dari 0', 500);
            }
            $hasilKurangLot = $getDetail->qty_lot - $qtyLot;
            if ($hasilKurangLot <= 0) {
                throw new Exception('Hasil Split QTY LOT Kurang dari 0', 500);
            }
            $dppSet = $model->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();

            $jumlah = $hasilKurang * $getDetail->harga;
            $ddskon = ($getDetail->tipe_diskon === "%") ? ($jumlah * ($getDetail->nominal_diskon / 100)) : $getDetail->nominal_diskon;
            $dpp = (($jumlah) * 11) / 12;
            if (!$dppSet) {
                $pajak = $jumlah * $getDetail->tax_value;
                $ppn_diskon = $ddskon * $getDetail->tax_value;
            } else {
                $pajak = $dpp * $getDetail->tax_value;
                $dppDis = $dpp = (($ddskon) * 11) / 12;
                $ppn_diskon = $dppDis * $getDetail->tax_value;
            }
            $totalHarga = (($jumlah - $ddskon) + ($pajak - $ppn_diskon));
            $updateSpilit = [
                "qty_lot" => $hasilKurangLot,
                "qty" => $hasilKurang,
                "jumlah" => $jumlah,
                "pajak" => $pajak,
                "diskon" => $ddskon,
                "diskon_ppn" => $ppn_diskon,
                "total_harga" => $totalHarga,
                "dpp_lain" => $dpp,
            ];

            $jumlah = $qty * $getDetail->harga;
            $ddskon = ($getDetail->tipe_diskon === "%") ? ($jumlah * ($getDetail->nominal_diskon / 100)) : $getDetail->nominal_diskon;
            $dpp = (($jumlah) * 11) / 12;
            if (!$dppSet) {
                $pajak = $jumlah * $getDetail->tax_value;
                $ppn_diskon = $ddskon * $getDetail->tax_value;
            } else {
                $pajak = $dpp * $getDetail->tax_value;
                $dppDis = $dpp = (($ddskon) * 11) / 12;
                $ppn_diskon = $dppDis * $getDetail->tax_value;
            }
            $totalHarga = (($jumlah - $ddskon) + ($pajak - $ppn_diskon));
            $split = [
                "retur_id" => $getDetail->retur_id,
                "retur_no" => $getDetail->retur_no,
                "uraian" => $getDetail->uraian,
                "warna" => $getDetail->warna,
                "harga" => $getDetail->harga,
                "qty_lot" => $qtyLot,
                "lot" => $uomLot,
                "qty" => $qty,
                "uom" => $getDetail->uom,
                "no_acc" => $noAcc,
                "jumlah" => $jumlah,
                "pajak" => $pajak,
                "diskon" => $ddskon,
                "total_harga" => $totalHarga,
                "dpp_lain" => $dpp,
                "diskon_ppn" => $ppn_diskon,
                "total_harga" => $totalHarga,
            ];
            $model->setTables("acc_retur_penjualan_detail")->save($split);
            $model->setWheres(["id" => $ids])->update($updateSpilit);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            $log = "update spilit uraian = {$getDetail->uraian} , warna = {$getDetail->warna} " . logArrayToString("; ", $updateSpilit);
            $log .= "\nhasil split " . logArrayToString("; ", $split);
            $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            log_message("error", json_encode($ex));
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function join($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            $kode = decrypt_url($id);
            $ids = $this->input->post("ids");

            $dids = explode(",", $ids);
            $this->_module->startTransaction();
            $lock = "acc_retur_penjualan_detail WRITE,user READ, main_menu_sub READ, log_history WRITE";
            $this->_module->lock_tabel($lock);
            $model = new $this->m_global;
            $getData = $model->setTables("acc_retur_penjualan_detail")
                            ->setWhereIn("acc_retur_penjualan_detail.id", $dids)->getData();
            $data = [];
            $qtyLots = 0;
            $qtys = 0;
            $jumlahs = 0;
            $pajaks = 0;
            $dpps = 0;
            $diskons = 0;
            $diskons_ppn = 0;
            $log = "";
            $datas = [];
            foreach ($getData as $key => $value) {
                $datas[] = logArrayToString("; ", (array) $value);
                $qtys += $value->qty;
                $qtyLots += $value->qty_lot;
                $jumlahs += $value->jumlah;
                $diskons += $value->diskon;
                $dpps += $value->dpp_lain;
                $pajaks += $value->pajak;
                $diskons_ppn += $value->diskon_ppn;

                $data = [
                    "retur_id" => $value->retur_id,
                    "retur_no" => $value->retur_no,
                    "uraian" => $value->uraian,
                    "warna" => $value->warna,
                    "lot" => $value->lot,
                    "uom" => $value->uom,
                    "no_acc" => $value->no_acc,
                    "harga" => $value->harga,
                ];
            }
            if (count($data) > 0) {
                $data["qty_lot"] = $qtyLots;
                $data["qty"] = $qtys;
                $data["jumlah"] = $jumlahs;
                $data["pajak"] = $pajaks;
                $data["diskon"] = $diskons;
                $data["total_harga"] = ($jumlahs - $diskons + $pajaks);
                $data["dpp_lain"] = $dpps;
                $data["diskon_ppn"] = $diskons_ppn;

                $model->setTables("acc_retur_penjualan_detail")->setWhereIn("acc_retur_penjualan_detail.id", $dids)->delete();
                $model->setTables("acc_retur_penjualan_detail")->save($data);

                $log .= "Join Item Data : " . logArrayToString("; ", (array) $datas);
                $log .= "\nhasil split " . logArrayToString("; ", $data);
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            if ($log !== "")
                $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            log_message("error", json_encode($ex));
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }
}
