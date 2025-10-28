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

class Fakturpenjualan extends MY_Controller {

    //put your code here
    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->load->driver('cache', array('adapter' => 'file'));
        $this->config->load('additional');
        $this->load->library("token");
    }

    protected $tipe = [
        "lokal" => "Lokal",
        "ekspor" => "Ekspor",
        "lain-lain" => "Lain - Lain",
        "makloon" => "Makloon"
    ];
    protected $uomLot = [
        "gul" => "Gulung",
        "pcs" => "Pcs",
        "roll" => "Roll",
        "ikat" => "Ikat",
        "bks" => "Bungkus",
        "box" => "Box"
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

    public function index() {
        $data['id_dept'] = 'ACCFPJ';
        $model = new $this->m_global;
        $data["sales"] = $model->setTables("mst_sales_group")->setWheres(["view" => "1"])->setSelects(["kode_sales_group", "nama_sales_group"])
                        ->setOrder(["kode_sales_group"])->getData();
        $this->load->view('sales/v_faktur_penjualan', $data);
    }

    public function add() {
        $model = new $this->m_global;
        $data['id_dept'] = 'ACCFPJ';
        $data["tipe"] = $this->tipe;
        $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
        $this->load->view('sales/v_faktur_penjualan_add', $data);
    }

    public function edit($id) {
        try {
            $data["user"] = (object) $this->session->userdata('nama');
            $data["id"] = $id;
            $data['id_dept'] = 'ACCFPJ';
            $data["tipe"] = $this->tipe;
            $data["uomLot"] = $this->uomLot;
            $data["payment_term"] = $this->paymentTerm;
            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $data["sales"] = $model->setTables("mst_sales_group")->setWheres(["view" => "1"])->setSelects(["kode_sales_group", "nama_sales_group"])
                            ->setOrder(["kode_sales_group"])->getData();
            $data["datas"] = $model->setTables("acc_faktur_penjualan fj")
                            ->setWheres(["no_faktur" => $kode])->getDetail();
            if (!$data['datas']) {
                show_404();
            }
            $data["jurnal"] = $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $data['datas']->jurnal])->getDetail();
            $data["detail"] = $model->setTables("acc_faktur_penjualan_detail fjd")
                            ->setJoins("acc_coa", "kode_coa = no_acc", "left")
                            ->setWheres(["faktur_id" => $data['datas']->id])
                            ->setSelects(["fjd.*", "acc_coa.nama as coa_nama"])->getData();

            $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
            $data["taxs"] = $model->setTables("tax")->setWheres(["type_inv" => "sale"])->setOrder(["nama" => "asc"])->getData();
            $this->load->view('sales/v_faktur_penjualan_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function list_data() {
        try {
            $data = array();
            $model = new $this->m_global;
            $model->setTables("acc_faktur_penjualan")->setJoins("mst_status", "mst_status.kode = acc_faktur_penjualan.status", "left")
                    ->setOrder(["tanggal" => "asc"])->setSearch(["no_faktur", "no_faktur_pajak", "no_sj", "partner_nama"])
                    ->setOrders([null, "no_faktur", "no_faktur_pajak", "tanggal", "no_sj", "marketing_nama", "partner_nama"])->setSelects(["acc_faktur_penjualan.*", "nama_status"]);
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
                $kode_encrypt = encrypt_url($value->no_faktur);
                $fk = ($value->no_faktur_internal === '') ? $value->no_faktur : $value->no_faktur_internal;
                $data [] = [
                    $no,
                    "<a href='" . base_url("sales/fakturpenjualan/edit/{$kode_encrypt}") . "'>{$fk}</a>",
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

    public function get_view_sj() {
        $tipe = $this->input->post("tipe");
        $view = $this->load->view('sales/modal/v_list_sj', ["tipe" => $tipe], true);
        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['data' => $view]));
    }

    public function list_sj() {
        try {
            $tipe = $this->input->post("tipe");
            $model = new $this->m_global;
            $model->setTables("delivery_order do")->setJoins("picklist p", "p.no = do.no_picklist")
                    ->setJoins("mst_sales_group msg", "msg.kode_sales_group = p.sales_kode", "left")
                    ->setSearch(["do.no_sj", "do.no_picklist", "pr.nama", "msg.nama_sales_group"])
                    ->setJoins("partner pr", "pr.id = p.customer_id", "left")
                    ->setOrders([null, "do.no_sj", "do.no_picklist", "pr.nama", "msg.nama_sales_group"])
                    ->setOrder(["do.tanggal_dokumen" => "desc"])->setWheres(["do.status" => "done", "faktur" => 0])
                    ->setSelects(["do.no_sj,do.no_picklist", "pr.nama as buyer", "msg.nama_sales_group as marketing"]);
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

    public function addsj() {
        try {
            $sj = $this->input->post("no");
            $model = new $this->m_global;
            $data = $model->setTables("delivery_order do")->setJoins("picklist p", "p.no = do.no_picklist")
                            ->setJoins("mst_sales_group msg", "msg.kode_sales_group = p.sales_kode", "left")
                            ->setJoins("partner pr", "pr.id = p.customer_id", "left")
                            ->setSelects(["customer_id,pr.nama as customer", "p.sales_kode,msg.nama_sales_group as sales_nama"])
                            ->setSelects(["p.keterangan"])->setWheres(["do.status" => "done", "faktur" => 0, "do.no_sj" => $sj])->getDetail();

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
            $noFakturInternal = $this->input->post("no_faktur_internal");
            $noFakturPajak = $this->input->post("no_faktur_pajak");
            $kurs = $this->input->post("kurs");
            $kursNominal = $this->input->post("kurs_nominal");
            $model = new $this->m_global;
            $this->_module->startTransaction();
            $lock = "token_increment WRITE,main_menu_sub READ, log_history WRITE,delivery_order do WRITE,delivery_order_detail dod READ,picklist_detail pd READ,acc_faktur_penjualan WRITE, acc_faktur_penjualan_detail WRITE";
            $this->_module->lock_tabel($lock);
            $checkSJ = $model->setTables("delivery_order do")->setJoins("delivery_order_detail dod", "(do.id = do_id and dod.status = 'done')", "left")
                            ->setJoins("picklist_detail pd", "picklist_detail_id = pd.id")->setWheres(["do.no_sj" => $nosj])
                            ->setGroups(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom"])
                            ->setSelects(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom", "faktur"])
                            ->setSelects(["count(dod.barcode_id) as total_lot", "sum(pd.qty) as total_qty"])->getData();
            if (count($checkSJ) > 0) {
                if ((string) $checkSJ[0]->faktur === "1") {
                    throw new \Exception("SJ {$nosj} Sudah masuk faktur penjualan", 500);
                }
            }
            if ($noFakturInternal !== "") {
                $fk = $model->setTables("acc_faktur_penjualan")->setWheres(["no_faktur_internal" => $noFakturInternal])->getDetail();
                if ($fk) {
                    throw new \Exception("No Faktur Internal sudah terpakai", 500);
                }
            }

            if (!$noFaktur = $this->token->noUrut('fakturpenjualan', date('y', strtotime($tanggal)) . '/' . getRomawi(date('m', strtotime($tanggal))), true)
                            ->generate('FP/', '/%04d')->get()) {
                throw new \Exception("No Faktur tidak terbuat", 500);
            }

            $header = [
                "no_faktur" => $noFaktur,
                "no_faktur_internal" => $noFakturInternal,
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
                "create_date" => date("Y-m-d H:i:s")
            ];

            $idFaktur = $model->setTables("acc_faktur_penjualan")->save($header);
            $detail = [];
            foreach ($checkSJ as $key => $value) {
                $detail[] = [
                    "faktur_id" => $idFaktur,
                    "faktur_no" => $noFaktur,
                    "uraian" => "{$value->corak_remark} / {$value->lebar_jadi} {$value->uom_lebar_jadi}",
                    "warna" => $value->warna_remark,
                    "qty_lot" => $value->total_lot,
                    "lot" => "roll",
                    "qty" => $value->total_qty,
                    "uom" => $value->uom
                ];
            }
            $model->setTables("acc_faktur_penjualan_detail")->saveBatch($detail);
            $model->setTables("delivery_order do")->setWheres(["no_sj" => $nosj])->update(["faktur" => 1]);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history_new($sub_menu, $noFaktur, 'create', "DATA -> " . logArrayToString("; ", $header), $username);

            $url = site_url("sales/fakturpenjualan/edit/" . encrypt_url($noFaktur));
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
                        'field' => 'warna[]',
                        'label' => 'Warna',
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
                        'field' => 'noacc[]',
                        'label' => 'No Acc',
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
            $noFakturInternal = $this->input->post("no_faktur_internal");
            $model = new $this->m_global;
            if ($noFakturInternal !== "") {
                $fk = $model->setTables("acc_faktur_penjualan")->setWheres(["no_faktur_internal" => $noFakturInternal, "id <>" => $ids])->getDetail();
                if ($fk) {
                    throw new \Exception("No Faktur Internal sudah terpakai", 500);
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
                "no_faktur_pajak" => $noFakturInternal,
                "no_faktur_internal" => $this->input->post("no_faktur_internal"),
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
            if ($nosj === $nosjold) {
                if (count($noAcc) > 0) {
                    $ppns = 0;
                    $grandDiskon = 0;
                    $grandTotal = 0;
                    $grandDiskonPpn = 0;
                    $qty = $this->input->post("qty");
                    $harga = $this->input->post("harga");
                    foreach ($noAcc as $key => $value) {
                        $hrg = str_replace(",", "", $harga[$key]);
                        $jumlah = $qty[$key] * $hrg;
                        $grandTotal += $jumlah;
                        $ddskon = ($tipediskon === "%") ? ($jumlah * ($nominalDiskon / 100)) : $nominalDiskon;
                        $grandDiskon += $ddskon;
                        $pajak = ($jumlah) * $taxVal;
                        $header["ppn"] += round($pajak);
                        $dpp = (($jumlah) * 11) / 12;
                        $header["dpp_lain"] += $dpp;
                        $ppn_diskon = ($ddskon) * $taxVal;
                        $grandDiskonPpn += $ppn_diskon;
                        $totalHarga = (($jumlah - $ddskon) + ($pajak - $ppn_diskon));
                        $header["final_total"] += $totalHarga;
                        $detail[] = [
                            "uraian" => $this->input->post("uraian")[$key],
                            "warna" => $this->input->post("warna")[$key],
                            "no_po" => $this->input->post("nopo")[$key],
                            "qty_lot" => $this->input->post("qtylot")[$key],
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
                    $header["diskon"] = $grandDiskon;
                    $header["diskon_ppn"] = round($grandDiskonPpn);
                    $header["grand_total"] = $grandTotal;

                    $model->setTables("acc_faktur_penjualan_detail")->updateBatch($detail, "id");
                }
            } else {
                $header["grand_total"] = 0;
                $header["ppn"] = 0;
                $header["dpp_lain"] = 0;
                $header["diskon"] = 0;
                $header["final_total"] = 0;
                $header["diskon_ppn"] = 0;
                $lock = "main_menu_sub READ, log_history WRITE,delivery_order do WRITE,delivery_order_detail dod READ,picklist_detail pd READ,acc_faktur_penjualan WRITE, acc_faktur_penjualan_detail WRITE";
                $this->_module->lock_tabel($lock);

                $checkSJ = $model->setTables("delivery_order do")->setJoins("delivery_order_detail dod", "(do.id = do_id and dod.status = 'done')", "left")
                                ->setJoins("picklist_detail pd", "picklist_detail_id = pd.id")->setWheres(["do.no_sj" => $nosj])
                                ->setGroups(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom"])
                                ->setSelects(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom", "faktur"])
                                ->setSelects(["count(dod.barcode_id) as total_lot", "sum(pd.qty) as total_qty"])->getData();
                if (count($checkSJ) > 0) {
                    if ((string) $checkSJ[0]->faktur === "1") {
                        throw new \Exception("SJ {$nosj} Sudah masuk faktur penjualan", 500);
                    }
                }

                $detail = [];
                foreach ($checkSJ as $key => $value) {
                    $detail[] = [
                        "faktur_id" => $ids,
                        "faktur_no" => $kode,
                        "uraian" => "{$value->corak_remark} / {$value->lebar_jadi} {$value->uom_lebar_jadi}",
                        "warna" => $value->warna_remark,
                        "qty_lot" => $value->total_lot,
                        "lot" => "roll",
                        "qty" => $value->total_qty,
                        "uom" => $value->uom
                    ];
                }
                $model->setTables("acc_faktur_penjualan_detail")->setWheres(["faktur_id" => $ids])->delete();
                if (count($detail) > 0)
                    $model->setTables("acc_faktur_penjualan_detail")->saveBatch($detail);
            }

            $model->setTables("acc_faktur_penjualan")->setWheres(["no_faktur" => $kode])->update($header);
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
            $data = $model->setTables("acc_faktur_penjualan")->setWheres(["no_faktur" => $kode])
                            ->setJoins("currency_kurs", "currency_kurs.id = acc_faktur_penjualan.kurs", "left")
                            ->setSelects(["acc_faktur_penjualan.*", "currency_kurs.currency as nama_kurs"])->getDetail();
            if (!$data) {
                throw new \Exception("Data Faktur tidak ditemukan", 500);
            }
            $this->_module->startTransaction();
            switch ($status) {
                case "confirm":
                    if ($data->status !== "draft") {
                        throw new \Exception("Faktur Harus dalam status Draft", 500);
                    }
                    $dataDetailHarga = $model->setTables("acc_faktur_penjualan_detail")->setWheres(["faktur_no" => $kode, "harga <=" => 0])->getDetail();
                    if ($dataDetailHarga) {
                        throw new \Exception("Harga Untuk Uraian {$dataDetailHarga->uraian} masih 0", 500);
                    }

                    $getCoaDefault = $model->setTables("setting")->setWheres(["setting_name" => "coa_penjualan_{$data->tipe}"])->getDetail();
                    if (!$getCoaDefault)
                        throw new \Exception("Coa Penjualan {$data->tipe} belum ditentukan", 500);

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
                        "origin" => "{$data->no_faktur}", "status" => "posted", "tanggal_dibuat" => $data->tanggal, "tipe" => "PJ",
                        "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => "{$data->partner_nama}"];

                    $detail = $model->setTables("acc_faktur_penjualan_detail")->setWheres(["faktur_no" => $kode])->getData();
                    $jurnalItems = [];

                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "Piutang",
                        "reff_note" => "",
                        "partner" => $data->partner_nama,
                        "kode_coa" => $getCoaDefault->value,
                        "posisi" => "D",
                        "nominal_curr" => ($data->grand_total + $data->ppn),
                        "kurs" => $data->kurs_nominal,
                        "kode_mua" => $data->nama_kurs,
                        "nominal" => (($data->grand_total + $data->ppn) * $data->kurs_nominal),
                        "row_order" => 1
                    );
                    $getCoaDefaultPpnDisc = $model->setTables("setting")->setWheres(["setting_name" => "coa_penjualan_ppn_diskon"])->getDetail();
                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "PPN Diskon",
                        "reff_note" => "",
                        "partner" => $data->partner_nama,
                        "kode_coa" => $getCoaDefaultPpnDisc->value,
                        "posisi" => "D",
                        "nominal_curr" => $data->diskon_ppn,
                        "kurs" => $data->kurs_nominal,
                        "kode_mua" => $data->nama_kurs,
                        "nominal" => ($data->diskon_ppn * $data->kurs_nominal),
                        "row_order" => (count($jurnalItems) + 1)
                    );
                    $getCoaDefaultDppDisc = $model->setTables("setting")->setWheres(["setting_name" => "coa_penjualan_dpp_diskon"])->getDetail();
                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "DPP Diskon",
                        "reff_note" => "",
                        "partner" => $data->partner_nama,
                        "kode_coa" => $getCoaDefaultDppDisc->value,
                        "posisi" => "D",
                        "nominal_curr" => $data->diskon,
                        "kurs" => $data->kurs_nominal,
                        "kode_mua" => $data->nama_kurs,
                        "nominal" => ($data->diskon * $data->kurs_nominal),
                        "row_order" => (count($jurnalItems) + 1)
                    );
                    $allDiskon = $data->diskon_ppn + $data->diskon;
                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "Diskon",
                        "reff_note" => "",
                        "partner" => $data->partner_nama,
                        "kode_coa" => $getCoaDefault->value,
                        "posisi" => "C",
                        "nominal_curr" => $allDiskon,
                        "kurs" => $data->kurs_nominal,
                        "kode_mua" => $data->nama_kurs,
                        "nominal" => ($allDiskon * $data->kurs_nominal),
                        "row_order" => (count($jurnalItems) + 1)
                    );

                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "PPN",
                        "reff_note" => "",
                        "partner" => $data->partner_nama,
                        "kode_coa" => $getCoaDefaultDppDisc->value,
                        "posisi" => "C",
                        "nominal_curr" => $data->ppn,
                        "kurs" => $data->kurs_nominal,
                        "kode_mua" => $data->nama_kurs,
                        "nominal" => ($data->ppn * $data->kurs_nominal),
                        "row_order" => (count($jurnalItems) + 1)
                    );
                    foreach ($detail as $key => $value) {
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => $value->uraian,
                            "reff_note" => "",
                            "partner" => $data->partner_nama,
                            "kode_coa" => $value->no_acc,
                            "posisi" => "C",
                            "nominal_curr" => $value->jumlah,
                            "kurs" => $data->kurs_nominal,
                            "kode_mua" => $data->nama_kurs,
                            "nominal" => ($value->jumlah * $data->kurs_nominal),
                            "row_order" => (count($jurnalItems) + 1)
                        );
                    }

                    if ($data->jurnal !== "") {
                        $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $jurnal])->update($jurnalData);
                        $model->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $jurnal])->delete();
                    } else {
                        $model->setTables("acc_jurnal_entries")->save($jurnalData);
                        $model->setTables("acc_faktur_penjualan")->setWheres(["id" => $data->id])->update(["jurnal" => $jurnal]);
                        $this->_module->gen_history_new($sub_menu, $kode, 'edit', "No Jurnal : {$jurnal}", $username);
                    }

                    $model->setTables("delivery_order")->setWheres(["no_sj" => $data->no_sj, "status" => "done"])->update(["faktur" => 1]);
                    $model->setTables("acc_jurnal_entries_items")->saveBatch($jurnalItems);
                    $log = "Header -> " . logArrayToString("; ", $jurnalData);
                    $log .= "\nDETAIL -> " . logArrayToString("; ", $jurnalItems);
                    $this->_module->gen_history_new("jurnal_entries", $jurnal, "{$stt}", $log, $username);

                    break;

                case "draft":
                    if ($data->status !== "cancel") {
                        throw new \exception("Data Faktur Penjualan {$kode} dalam status {$data->status}", 500);
                    }
                    break;
                default:
                    $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $data->jurnal])->update(["status" => "unposted"]);
                    $model->setTables("delivery_order")->setWheres(["no_sj" => $data->no_sj, "status" => "done"])->update(["faktur" => 0]);
                    $this->_module->gen_history_new("jurnal_entries", $data->jurnal, 'edit', "Merubah Status Ke unposted dari penjualan", $username);
                    break;
            }
            $model->setTables("acc_faktur_penjualan")->setWheres(["no_faktur" => $kode])->update(["status" => strtolower($status)]);
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
        }
    }

    public function split($id) {
        try {
            $model = new $this->m_global;
            $ids = $this->input->post("ids");
            $detail = $model->setTables("acc_faktur_penjualan_detail fjd")
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
                ],
                [
                    'field' => 'no_acc',
                    'label' => 'No ACC',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih',
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
            $lock = "acc_faktur_penjualan READ, acc_faktur_penjualan_detail WRITE,user READ, main_menu_sub READ, log_history WRITE";
            $this->_module->lock_tabel($lock);
            $model = new $this->m_global;
            $getDetail = $model->setTables("acc_faktur_penjualan_detail")->setJoins("acc_faktur_penjualan", "faktur_id = acc_faktur_penjualan.id")
                            ->setSelects(["acc_faktur_penjualan_detail.*", "nominal_diskon,tipe_diskon,tax_value"])
                            ->setWheres(["acc_faktur_penjualan_detail.id" => $ids])->getDetail();
            if ($getDetail === null) {
                throw new Exception('Data Tidak ditemukan', 500);
            }
            $hasilKurang = $getDetail->qty - $qty;
            if ($hasilKurang < 1) {
                throw new Exception('Hasil Split QTY Kurang dari 1', 500);
            }
            $hasilKurangLot = $getDetail->qty_lot - $qtyLot;
            if ($hasilKurangLot < 1) {
                throw new Exception('Hasil Split QTY LOT Kurang dari 1', 500);
            }

            $jumlah = $hasilKurang * $getDetail->harga;
            $ddskon = ($getDetail->tipe_diskon === "%") ? ($jumlah * ($getDetail->nominal_diskon / 100)) : $getDetail->nominal_diskon;
            $dpp = (($jumlah) * 11) / 12;
            $pajak = ($jumlah) * $getDetail->tax_value;
            $ppn_diskon = ($ddskon) * $getDetail->tax_value;
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
            $pajak = ($jumlah) * $getDetail->tax_value;
            $ppn_diskon = ($ddskon) * $getDetail->tax_value;
            $totalHarga = (($jumlah - $ddskon) + ($pajak - $ppn_diskon));
            $split = [
                "faktur_id" => $getDetail->faktur_id,
                "faktur_no" => $getDetail->faktur_no,
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
            $model->setTables("acc_faktur_penjualan_detail")->save($split);
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

    public function delete_item($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            $kode = decrypt_url($id);
            $ids = $this->input->post("ids");

            $dids = explode(",", $ids);
            $this->_module->startTransaction();
            $lock = "acc_faktur_penjualan_detail WRITE,acc_faktur_penjualan WRITE,user READ, main_menu_sub READ, log_history WRITE";
            $this->_module->lock_tabel($lock);
            $model = new $this->m_global;

            $check = $model->setTables("acc_faktur_penjualan")->setWheres(["no_faktur" => $kode])->getDetail();
            if (!$check) {
                throw new Exception('Data Faktur Tidak ditemukan', 500);
            }
            if ($check->status !== "draft") {
                throw new Exception('Data Faktur harus dalam status draft', 500);
            }

            $getData = $model->setTables("acc_faktur_penjualan_detail")
                            ->setWhereIn("acc_faktur_penjualan_detail.id", $dids)->getData();

            $ppnDiskon = 0;
            $diskon = 0;
            $total = 0;
            $finalTotal = 0;
            $pajak = 0;
            $dpp = 0;
            $val = array();
            foreach ($getData as $key => $value) {
                $val [] = (array) $value;
                $ppnDiskon += $value->diskon_ppn;
                $diskon += $value->diskon;
                $total += $value->jumlah;
                $finalTotal += $value->total_harga;
                $pajak += $value->pajak;
                $dpp += $value->dpp_lain;
            }

            $check->final_total -= $finalTotal;
            $check->diskon_ppn -= round($ppnDiskon);
            $check->diskon -= $diskon;
            $check->grand_total -= $total;
            $check->ppn -= round($pajak);
            $check->dpp_lain -= ($dpp * 11) / 12;

            $idss = $check->id;
            unset($check->id);
            $model->setTables("acc_faktur_penjualan")->setWheres(["id" => $idss])->update((array) $check);
            $model->setTables("acc_faktur_penjualan_detail")->setWhereIn("acc_faktur_penjualan_detail.id", $dids)->delete();
            $log = "delete Item Data : " . logArrayToString("; ", $val);
            $log .= "\n Header Update " . logArrayToString("; ", (array) $check);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
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
            $lock = "acc_faktur_penjualan_detail WRITE,user READ, main_menu_sub READ, log_history WRITE";
            $this->_module->lock_tabel($lock);
            $model = new $this->m_global;
            $getData = $model->setTables("acc_faktur_penjualan_detail")
                            ->setWhereIn("acc_faktur_penjualan_detail.id", $dids)->getData();
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
                    "faktur_id" => $value->faktur_id,
                    "faktur_no" => $value->faktur_no,
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

                $model->setTables("acc_faktur_penjualan_detail")->setWhereIn("acc_faktur_penjualan_detail.id", $dids)->delete();
                $model->setTables("acc_faktur_penjualan_detail")->save($data);

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

    public function save_item($id) {
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
            ],
            [
                'field' => 'no_acc',
                'label' => 'No ACC',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                ]
            ],
            [
                'field' => 'uraian',
                'label' => 'Uraian',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus diisi',
                ]
            ],
            [
                'field' => 'warna',
                'label' => 'Warna',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus diisi',
                ]
            ],
            [
                'field' => 'uom',
                'label' => 'Uom',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                ]
            ],
            [
                'field' => 'uom_lot',
                'label' => 'Uom Lot',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                ]
            ],
            [
                'field' => 'harga',
                'label' => 'Harga',
                'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'],
                'errors' => [
                    'required' => '{field} Pada Item harus diisi',
                    "regex_match" => "{field} harus berupa number / desimal"
                ]
            ]
        ];
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            $kode = decrypt_url($id);

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $model = new $this->m_global;
            $check = $model->setTables("acc_faktur_penjualan")->setWheres(["no_faktur" => $kode])->getDetail();
            if (!$check) {
                throw new Exception('Data Faktur Tidak ditemukan', 500);
            }
            if ($check->status !== "draft") {
                throw new Exception('Data Faktur harus dalam status draft', 500);
            }
            $this->_module->startTransaction();
            $lock = "acc_faktur_penjualan_detail WRITE,acc_faktur_penjualan WRITE, main_menu_sub READ, log_history WRITE";
            $this->_module->lock_tabel($lock);

            $harga = $nom = str_replace(",", "", $this->input->post("harga"));
            $qty = $this->input->post("qty");
            $jumlah = $harga * $qty;
            $pajak = ($jumlah) * $check->tax_value;
            $ddskon = ($check->tipe_diskon === "%") ? ($jumlah * ($check->nominal_diskon / 100)) : $check->nominal_diskon;
            $ppn_diskon = ($ddskon) * $check->tax_value;
            $dpp = (($jumlah) * 11) / 12;
            $totalHarga = (($jumlah - $ddskon) + ($pajak - $ppn_diskon));

            $item = [
                "faktur_id" => $check->id,
                "faktur_no" => $kode,
                "uraian" => $this->input->post("uraian"),
                "warna" => $this->input->post("warna"),
                "no_po" => $this->input->post("no_po"),
                "qty_lot" => $this->input->post("qty_lot"),
                "lot" => $this->input->post("uom_lot"),
                "qty" => $qty,
                "uom" => $this->input->post("uom"),
                "harga" => $harga,
                "no_acc" => $this->input->post("no_acc"),
                "jumlah" => $jumlah,
                "dpp_lain" => $dpp,
                "pajak" => $pajak,
                "diskon" => $ddskon,
                "diskon_ppn" => $ppn_diskon,
                "total_harga" => $totalHarga,
            ];
            $model->setTables("acc_faktur_penjualan_detail")->save($item);
            $check->dpp_lain += $dpp;
            $check->diskon_ppn += round($ppn_diskon);
            $check->ppn += round($pajak);
            $check->grand_total += $jumlah;
            $check->final_total += $totalHarga;
            $ids = $check->id;
            unset($check->id);
            $model->setTables("acc_faktur_penjualan")->setWheres(["id" => $ids])->update((array) $check);
            $log = "save Item Data : " . logArrayToString("; ", $item);
            $log .= "\n Header Update " . logArrayToString("; ", (array) $check);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            log_message("error", json_encode($ex));
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function get_satuan() {
        try {
            $model = new $this->m_global;
            $model->setTables("uom")->setSelects(["short", "nama"])->setWheres(["jual" => "yes"])->setSearch(["short", "nama"])->setOrder(["short" => "asc"]);
            $_POST['length'] = 20;
            $_POST['start'] = 0;
            if ($this->input->get('search') !== "") {
                $_POST['search']['value'] = $this->input->get('search');
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("data" => $model->getData())));
        } catch (Exception $ex) {
            
        }
    }

    public function print_pdf() {
        try {
            $kode = decrypt_url($this->input->post("id"));
            $model = new $this->m_global;

            $data["head"] = $model->setTables("acc_faktur_penjualan")
                            ->setJoins("partner", "partner.id = partner_id", "left")
                            ->setJoins("tax", "tax.id = tax_id", "left")
                            ->setSelects(["acc_faktur_penjualan.*", 'CONCAT(invoice_street," ",invoice_city," ",invoice_zip," ",invoice_state," ",invoice_country) as alamat',
                                "tax.nama as nama_tax"])->setWheres(["no_faktur" => $kode])->getDetail();
            if (!$data["head"]) {
                throw new \exception("Data Faktur Penjualan {$kode} tidak ditemukan", 500);
            }
            $data["alamat"] = $model->setTables("setting")->setWheres(["setting_name" => "alamat_fp"])->getDetail();
            $data["npwp"] = $model->setWheres(["setting_name" => "npwp_fp"], true)->getDetail();
            $data["detail"] = $model->setTables("acc_faktur_penjualan_detail")->setWheres(["faktur_no" => $kode])->setOrder(["uraian" => "asc"])->getData();
            if ($data["head"]->kurs_nominal > 1) {
                $data["curr"] = $curr = $model->setTables("currency_kurs")->setWheres(["currency_kurs.id" => $data["head"]->kurs])
                                ->setJoins("currency", "currency.nama = currency_kurs.currency", "left")
                                ->setSelects(["currency.*,ket"])->getDetail();
                $view = 'sales/v_faktur_penjualan_print_valas';
            } else {
                $view = 'sales/v_faktur_penjualan_print';
            }
            $html = $this->load->view($view, $data, true);

            $url = "dist/storages/print/fakturpenjulan";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            $mpdf = new Mpdf(['tempDir' => FCPATH . 'tmp']);
            $mpdf->WriteHTML($html);
            $filename = str_replace("/", "-", $data["head"]->no_faktur_internal);
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

            $head = $model->setTables("acc_faktur_penjualan")
                            ->setJoins("partner", "partner.id = partner_id", "left")
                            ->setJoins("tax", "tax.id = tax_id", "left")
                            ->setSelects(["acc_faktur_penjualan.*", 'CONCAT(invoice_street," ",invoice_city," ",invoice_zip," ",invoice_state," ",invoice_country) as alamat',
                                "tax.nama as nama_tax"])->setWheres(["no_faktur" => $kode])->getDetail();
            if (!$head) {
                throw new \exception("Data Faktur Penjualan {$kode} tidak ditemukan", 500);
            }
            $alamat = $model->setTables("setting")->setWheres(["setting_name" => "alamat_fp"])->getDetail();
            $npwp = $model->setWheres(["setting_name" => "npwp_fp"], true)->getDetail();

            $buff = $printer->getPrintConnector();

            $buff->write("\x1bM");
            $buff->write("\x1bE" . chr(1));
            $printer->text("Faktur Penjualan");
            $buff->write("\x1bF" . chr(0));
            $printer->text(str_pad("", 40));
            $printer->text(str_pad("Bandung," . date("d-m-Y"), 36, " ", STR_PAD_LEFT));
            $printer->feed();

            $buff->write("\x1bg" . chr(1));
            $alamat = str_split($alamat->value, 40);
            foreach ($alamat as $key => $value) {
                $printer->text(trim($value));
                $printer->text("\n");
            }
            $printer->text("NPWP : " . ($npwp->value ?? ""));
            $printer->feed();
            $printer->feed();
            $printer->text(str_pad("No. Faktur", 15));
            $printer->text(str_pad(": {$head->no_faktur_internal}", 30));
            //
            $printer->text(str_pad("", 5));
            $printer->text(str_pad("No. PO", 7));
            $printer->text(str_pad(": {$head->po_cust}", 30));
            //
            $printer->text(str_pad("", 5));
            $printer->text("Kepada Yth.,");
            $printer->feed();

            $printer->text(str_pad("No. Surat Jalan", 15));
            $printer->text(str_pad(": {$head->no_sj}", 30));

            $printer->text(str_pad("", 47));

            $kpd = str_split($head->partner_nama, 25);
            foreach ($kpd as $key => $value) {
                if ($key > 0) {
                    $printer->text(str_pad("", 95));
                }
                $printer->text(str_pad(trim($value), 25));
            }
            $printer->feed();
            $alm = str_split("Alamat 1 : {$head->alamat}", 25);
            foreach ($alm as $key => $value) {
                $line = str_pad("", 92);
                $line .= str_pad(trim($value), 25);
                $printer->text($line . "\n");
            }
//            

            $detail = $model->setTables("acc_faktur_penjualan_detail")->setWheres(["faktur_no" => $kode])->setOrder(["uraian" => "asc"])->getData();
            $printer->selectPrintMode();
            $buff->write("\x1bX" . chr(15));
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad(" ", 137));
            $printer->setUnderline(Printer::UNDERLINE_NONE);
            if ($head->kurs_nominal > 1) {
                //valas
                $curr = $model->setTables("currency_kurs")->setWheres(["currency_kurs.id" => $head->kurs])
                                ->setJoins("currency", "currency.nama = currency_kurs.currency", "left")
                                ->setSelects(["currency.*,ket"])->getDetail();
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad("No", 3));
                $printer->text(str_pad("Jenis Barang / Uraian", 35, " ", STR_PAD_BOTH));
                $printer->text(str_pad("No.PO", 24, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Quantity", 26, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Harga Satuan", 24, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Jumlah", 24, " ", STR_PAD_BOTH));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
                $printer->feed();
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad(" ", 62));
                $printer->text(str_pad("Gul/PCS", 13, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Satuan", 13, " ", STR_PAD_BOTH));
                $printer->text(str_pad($curr->nama, 12, " ", STR_PAD_BOTH));
                $printer->text(str_pad("IDR", 12, " ", STR_PAD_BOTH));
                $printer->text(str_pad($curr->nama, 12, " ", STR_PAD_BOTH));
                $printer->text(str_pad("IDR", 12, " ", STR_PAD_BOTH));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
                $printer->feed();
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $subtotal = 0;
                $subtotalValas = 0;
                $totalQty = 0;
                $totalQtyLot = 0;
                $uomLot = "";
                $uom = "";
                foreach ($detail as $key => $value) {
                    $subtotal += ($value->jumlah * $head->kurs_nominal);
                    $subtotalValas += $value->jumlah;
                    $totalQty += $value->qty;
                    $totalQtyLot += $value->qty_lot;
                    $uomLot = $value->lot;
                    $uom = $value->uom;
                    $line = str_pad($key + 1, 3);
                    $line .= str_pad($value->uraian, 35, " ", STR_PAD_RIGHT);
                    $line .= str_pad($value->no_po, 24, " ", STR_PAD_RIGHT);
                    $line .= str_pad("{$value->qty_lot} {$value->lot}", 16, " ", STR_PAD_BOTH);
                    $line .= str_pad("{$value->qty} {$value->uom}", 16, " ", STR_PAD_BOTH);

                    $line .= str_pad(" {$curr->symbol}", 4);
                    $line .= str_pad(number_format($value->harga, 2), 8, " ", STR_PAD_LEFT);
                    $line .= str_pad(" Rp.", 4);
                    $line .= str_pad(number_format(($value->harga * $head->kurs_nominal), 2), 8, " ", STR_PAD_LEFT);

                    $line .= str_pad(" {$curr->symbol}", 4);
                    $line .= str_pad(number_format($value->jumlah, 2), 8, " ", STR_PAD_LEFT);
                    $line .= str_pad(" Rp.", 4);
                    $line .= str_pad(number_format(($value->jumlah * $head->kurs_nominal), 2), 8, " ", STR_PAD_LEFT);
                    $printer->text($line . "\n");
                }
                $printer->text(str_pad("", 62));
                $printer->text(str_pad("{$totalQtyLot} {$uomLot}", 16));
                $printer->text(str_pad("{$totalQty} {$uom}", 16));
                $printer->feed();
                $printer->setUnderline(Printer::UNDERLINE_NONE);
                $printer->feed();
                $diskonValas = number_format($head->diskon, 2, ".", ",");
                $diskon = number_format(($head->diskon * $head->kurs_nominal), 2, ".", ",");
                $ppnValas = number_format($head->ppn - $head->diskon_ppn, 2, ".", ",");
                $ppn = number_format(($head->ppn - $head->diskon_ppn) * $head->kurs_nominal, 2, ".", ",");
                $finalTotalValas = number_format(($head->final_total), 2, ".", ",");
                $finalTotal = number_format(($head->final_total * $head->kurs_nominal), 2, ".", ",");
                $terbilang = Kwitansi($head->final_total);
                $spltTbl = str_split($terbilang . " {$curr->ket}", 73);

                $printer->text(str_pad("(*)Kurs : Rp. {$head->kurs_nominal}", 100, " "));
                $printer->text(str_pad("Subtotal", 12, " ", STR_PAD_RIGHT));
                $printer->text(str_pad(" {$curr->symbol}", 4));
                $printer->text(str_pad(number_format($subtotalValas, 2), 8, " ", STR_PAD_LEFT));
                $printer->text(str_pad(" Rp.", 4));
                $printer->text(str_pad(number_format(($subtotal), 2), 8, " ", STR_PAD_LEFT));
                $printer->feed();
                $printer->text(str_pad("", 100));
                $printer->text(str_pad("Discount", 12, " ", STR_PAD_RIGHT));
                $printer->text(str_pad(" {$curr->symbol}", 4));
                $printer->text(str_pad(number_format($diskonValas, 2), 8, " ", STR_PAD_LEFT));
                $printer->text(str_pad(" Rp.", 4));
                $printer->text(str_pad(number_format(($diskon * $head->kurs_nominal), 2), 8, " ", STR_PAD_LEFT));
                $printer->feed();
                $printer->text(str_pad(" Terbilang : ", 13));
                $printer->text(str_pad(($spltTbl[0] ?? " "), 87, " "));
                $printer->text(str_pad("Ppn", 12, " ", STR_PAD_RIGHT));
                $printer->text(str_pad(" {$curr->symbol}", 4));
                $printer->text(str_pad(number_format($ppnValas, 2), 8, " ", STR_PAD_LEFT));
                $printer->text(str_pad(" Rp.", 4));
                $printer->text(str_pad($ppn, 8, " ", STR_PAD_LEFT));
                $printer->feed();
                $printer->text(str_pad(" ", 13));
                $printer->text(str_pad(($spltTbl[1] ?? " "), 87, " "));
                $printer->text(str_pad("Total", 12, " ", STR_PAD_RIGHT));
                $printer->text(str_pad(" {$curr->symbol}", 4));
                $printer->text(str_pad($finalTotalValas, 8, " ", STR_PAD_LEFT));
                $printer->text(str_pad(" Rp.", 4));
                $printer->text(str_pad($finalTotal, 8, " ", STR_PAD_LEFT));
                $printer->feed();
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad(" ", 137));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
                $printer->feed();
                $printer->feed();
                $printer->text(" No Rekenning : {$head->no_rekening} \n");
                $printer->feed();
                $printer->feed();
            } else {

                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad("No", 3));
                $printer->text(str_pad("Jenis Barang / Uraian", 57, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Quantity", 32, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Harga Satuan", 20, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Jumlah", 25, " ", STR_PAD_BOTH));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
                $printer->feed();
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad(" ", 60));
                $printer->text(str_pad("Gul/PCS", 16, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Satuan", 16, " ", STR_PAD_BOTH));
                $printer->text(str_pad(" ", 45));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
                $printer->feed();
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $subtotal = 0;
                foreach ($detail as $key => $value) {
                    $subtotal += $value->jumlah;
                    $line = str_pad($key + 1, 3);
                    $line .= str_pad($value->uraian, 57, " ", STR_PAD_RIGHT);
                    $line .= str_pad("{$value->qty_lot} {$value->lot}", 16, " ", STR_PAD_BOTH);
                    $line .= str_pad("{$value->qty} {$value->uom}", 16, " ", STR_PAD_BOTH);
                    $line .= str_pad(" Rp.", 4);
                    $line .= str_pad(number_format($value->harga, 2), 16, " ", STR_PAD_LEFT);
                    $line .= str_pad(" Rp.", 4);
                    $line .= str_pad(number_format($value->jumlah, 2), 21, " ", STR_PAD_LEFT);
                    $printer->text($line . "\n");
                }
                $printer->setUnderline(Printer::UNDERLINE_NONE);
                $printer->feed();
                $dpp = number_format(($head->grand_total - $head->diskon) * 11 / 12, 2, ".", ",");
                $diskon = number_format($head->diskon, 2, ".", ",");
                $ppn = number_format($head->ppn - $head->diskon_ppn, 2, ".", ",");
                $finalTotal = number_format(($head->final_total), 2, ".", ",");
                $terbilang = Kwitansi($head->final_total);
                $spltTbl = str_split($terbilang . " Rupiah", 73);
                //isi terbilang
                $printer->text(str_pad(" Terbilang : ", 13));
                $printer->text(str_pad($spltTbl[0] ?? " ", 79));
                $printer->text(str_pad("Subtotal", 20, " ", STR_PAD_RIGHT));
                $printer->text(str_pad(" Rp.", 4));
                $printer->text(str_pad(number_format($subtotal, 2), 21, " ", STR_PAD_LEFT));
                $printer->feed();
                $printer->text(str_pad(" ", 13));
                $printer->text(str_pad(($spltTbl[1] ?? " "), 79));
                $printer->text(str_pad("Dpp Nilai Lain", 20, " ", STR_PAD_RIGHT));
                $printer->text(str_pad(" Rp.", 4));
                $printer->text(str_pad($dpp, 21, " ", STR_PAD_LEFT));
                $printer->feed();
                $printer->text(str_pad(" ", 13));
                $printer->text(str_pad(($spltTbl[2] ?? " "), 79));
                $printer->text(str_pad("Discount", 20, " ", STR_PAD_RIGHT));
                $printer->text(str_pad(" Rp.", 4));
                $printer->text(str_pad($diskon, 21, " ", STR_PAD_LEFT));
                $printer->feed();
                $printer->text(str_pad(" ", 13));
                $printer->text(str_pad(" ", 79));
                $printer->text(str_pad($head->nama_tax ?? "Ppn ", 20, " ", STR_PAD_RIGHT));
                $printer->text(str_pad(" Rp.", 4));
                $printer->text(str_pad($ppn, 21, " ", STR_PAD_LEFT));
                $printer->feed();
                $printer->text(str_pad(" ", 13));
                $printer->text(str_pad(" ", 79));
                $printer->text(str_pad("TOTAL", 20, " ", STR_PAD_RIGHT));
                $printer->text(str_pad(" Rp.", 4));
                $printer->text(str_pad($finalTotal, 21, " ", STR_PAD_LEFT));
                $printer->feed();
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad(" ", 137));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
                $printer->feed();
                $printer->feed();
            }

            $buff->write("\x1bg" . chr(1));
            $printer->text(str_pad(" ", 6));
            $printer->text(str_pad("Penerima :", 20, " ", STR_PAD_BOTH));
            $printer->text(str_pad(" ", 72));
            $printer->text(str_pad("Hormat Kami :", 20, " ", STR_PAD_BOTH));
            $printer->feed();
            $printer->feed();
            $printer->text(str_pad(" ", 26));
            $printer->text(str_pad("Pengaduan/Klaim melebihi 7 hari dari tanggal pengiriman barang,", 72, " ", STR_PAD_BOTH));
            $printer->feed();
            $printer->text(str_pad(" ", 26));
            $printer->text(str_pad("tidak akan kami layani", 82, " ", STR_PAD_BOTH));
            $printer->feed();
            $printer->feed();
            $printer->text(str_pad(" ", 6));
            $printer->text(str_pad("(__________________)", 20, " ", STR_PAD_BOTH));
            $printer->text(str_pad(" ", 72));
            $printer->text(str_pad("(__________________)", 20, " ", STR_PAD_BOTH));

            $printer->feed();
            $datas = $connector->getData();
            $printer->close();
            $client = new GuzzleHttp\Client();
            $resp = $client->request("POST", $this->config->item('url_web_print_w_logo'), [
                "form_params" => [
                    "data" => $datas,
                    "logo" => "",
                    "printer" => "\\\\{$printers->ip_share}\\{$printers->nama_printer_share}"
                ]
            ]);
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
}
