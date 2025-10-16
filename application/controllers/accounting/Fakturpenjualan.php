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
class Fakturpenjualan extends MY_Controller {

    //put your code here
    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->load->driver('cache', array('adapter' => 'file'));
//        $this->config->load('additional');
//        $this->load->library("token");
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
            'field' => 'no_faktur',
            'label' => 'No Faktur Internal',
            'rules' => ['trim', 'required', 'is_unique[acc_faktur_penjualan.no_faktur]'],
            'errors' => [
                'required' => '{field} Harus diisi',
                'is_unique' => "No faktur Sudah dipakai"
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

    public function index() {
        $data['id_dept'] = 'ACCFPJ';
        $model = new $this->m_global;
        $data["sales"] = $model->setTables("mst_sales_group")->setWheres(["view" => "1"])->setSelects(["kode_sales_group", "nama_sales_group"])
                        ->setOrder(["kode_sales_group"])->getData();
        $this->load->view('accounting/v_faktur_penjualan', $data);
    }

    public function add() {
        $model = new $this->m_global;
        $data['id_dept'] = 'ACCFPJ';
        $data["tipe"] = $this->tipe;
        $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
        $this->load->view('accounting/v_faktur_penjualan_add', $data);
    }

    public function edit($id) {
        try {
            $data["user"] = (object) $this->session->userdata('nama');
            $data["id"] = $id;
            $data['id_dept'] = 'ACCFPJ';
            $data["tipe"] = $this->tipe;
            $data["uomLot"] = $this->uomLot;
            $kode = decrypt_url($id);
            $model = new $this->m_global;
            $data["sales"] = $model->setTables("mst_sales_group")->setWheres(["view" => "1"])->setSelects(["kode_sales_group", "nama_sales_group"])
                            ->setOrder(["kode_sales_group"])->getData();
            $data["datas"] = $model->setTables("acc_faktur_penjualan fj")
                            ->setWheres(["no_faktur" => $kode])->getDetail();
            if (!$data['datas']) {
                show_404();
            }
            $data["detail"] = $model->setTables("acc_faktur_penjualan_detail fjd")
                            ->setJoins("acc_coa", "kode_coa = no_acc", "left")
                            ->setWheres(["faktur_id" => $data['datas']->id])
                            ->setSelects(["fjd.*", "acc_coa.nama as coa_nama"])->getData();

            $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
            $data["taxs"] = $model->setTables("tax")->setWheres(["type_inv" => "sale"])->setOrder(["nama" => "asc"])->getData();
            $this->load->view('accounting/v_faktur_penjualan_edit', $data);
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
                $data [] = [
                    $no,
                    "<a href='" . base_url("accounting/fakturpenjualan/edit/{$kode_encrypt}") . "'>{$value->no_faktur}</a>",
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
        $view = $this->load->view('accounting/modal/v_list_sj', ["tipe" => $tipe], true);
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
            $noFaktur = $this->input->post("no_faktur");
            $noFakturPajak = $this->input->post("no_faktur_pajak");
            $kurs = $this->input->post("kurs");
            $kursNominal = $this->input->post("kurs_nominal");
            $model = new $this->m_global;
            $this->_module->startTransaction();
            $lock = "main_menu_sub READ, log_history WRITE,delivery_order do WRITE,delivery_order_detail dod READ,picklist_detail pd READ,acc_faktur_penjualan WRITE, acc_faktur_penjualan_detail WRITE";
            $this->_module->lock_tabel($lock);
            $checkSJ = $model->setTables("delivery_order do")->setJoins("delivery_order_detail dod", "(do.id = do_id and dod.status = 'done')", "left")
                            ->setJoins("picklist_detail pd", "picklist_detail_id = pd.id")->setWheres(["do.no_sj" => $nosj])
                            ->setGroups(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom"])
                            ->setSelects(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom", "faktur"])
                            ->setSelects(["count(dod.barcode_id) as total_lot", "sum(pd.qty) as total_qty"])->getData();
            if (count($checkSJ) < 1) {
                throw new \Exception("Data SJ Tidak ditemukan", 500);
            }
            if ((string) $checkSJ[0]->faktur === "1") {
                throw new \Exception("SJ {$nosj} Sudah masuk faktur penjualan", 500);
            }

            $header = [
                "no_faktur" => $noFaktur,
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

            $url = site_url("accounting/fakturpenjualan/edit/" . encrypt_url($noFaktur));
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
            $header = [
                "tipe" => $this->input->post("tipe"),
                "no_sj" => $this->input->post("no_sj"),
                "po_cust" => $this->input->post("po_cust"),
                "marketing_kode" => $this->input->post("marketing_kode"),
                "marketing_nama" => $this->input->post("marketing_nama"),
                "partner_id" => $this->input->post("customer"),
                "partner_nama" => $this->input->post("customer_nama"),
                "no_faktur_pajak" => $this->input->post("no_faktur_pajak"),
                "kurs" => $this->input->post("kurs"),
                "kurs_nominal" => $this->input->post("kurs_nominal"),
                "tipe_diskon" => $tipediskon,
                "nominal_diskon" => $nominalDiskon,
                "tax_id" => $this->input->post("tax"),
                "tax_value" => $taxVal,
                "dpp_lain" => 0
            ];
            $ppns = 0;
            $model = new $this->m_global;
            $detail = [];
            $this->_module->startTransaction();
            if (count($noAcc) > 0) {
                $ppns = 0;
                $grandTotal = 0;
                $qty = $this->input->post("qty");
                $harga = $this->input->post("harga");
                foreach ($noAcc as $key => $value) {
                    $hrg = str_replace(",", "", $harga[$key]);
                    $jumlah = $qty[$key] * $hrg;
                    $grandTotal += $jumlah;
                    $ddskon = ($tipediskon === "%") ? ($jumlah * ($nominalDiskon / 100)) : $nominalDiskon;
                    $pajak = ($jumlah - $ddskon) * $taxVal;
                    $dpp = (($jumlah - $ddskon) * 11) / 12;

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
                        "total_harga" => ($jumlah - $ddskon + $pajak),
                        "dpp_lain" => $dpp,
                        "diskon" => $ddskon,
                        "id" => $this->input->post("detail_id")[$key]
                    ];
                }
                if ($tipediskon === "%") {
                    $diskon = $grandTotal * ($nominalDiskon / 100);
                    $ppns = ($grandTotal - $diskon) * $taxVal;
                    $header["dpp_lain"] = (($grandTotal - $diskon) * 11) / 12;
                    $header["diskon"] = $diskon;
                } else {
                    $ppns = ($grandTotal - $nominalDiskon) * $taxVal;
                    $header["dpp_lain"] = (($grandTotal - $nominalDiskon) * 11) / 12;
                    $header["diskon"] = $nominalDiskon;
                }
                $header["grand_total"] = $grandTotal;
                $header["ppn"] = $ppns;
                $model->setTables("acc_faktur_penjualan_detail")->updateBatch($detail, "id");
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
            $html = $this->load->view('accounting/modal/v_split_item_fp', ["data" => $detail, "id" => $id, "uomLot" => $this->uomLot], true);
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
            $dpp = (($jumlah - $ddskon) * 11) / 12;
            $pajak = ($jumlah - $ddskon) * $getDetail->tax_value;
            $updateSpilit = [
                "qty_lot" => $hasilKurangLot,
                "qty" => $hasilKurang,
                "jumlah" => $jumlah,
                "pajak" => $pajak,
                "diskon" => $ddskon,
                "total_harga" => ($jumlah - $ddskon + $pajak),
                "dpp_lain" => $dpp,
            ];

            $jumlah = $qty * $getDetail->harga;
            $ddskon = ($getDetail->tipe_diskon === "%") ? ($jumlah * ($getDetail->nominal_diskon / 100)) : $getDetail->nominal_diskon;
            $dpp = (($jumlah - $ddskon) * 11) / 12;
            $pajak = ($jumlah - $ddskon) * $getDetail->tax_value;

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
                "total_harga" => ($jumlah - $ddskon + $pajak),
                "dpp_lain" => $dpp,
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
            $log = "";
            $datas = [];
            foreach ($getData as $key => $value) {
                $datas[] = logArrayToString("; ",(array)$value);
                $qtys += $value->qty;
                $qtyLots += $value->qty_lot;
                $jumlahs += $value->jumlah;
                $diskons += $value->diskon;
                $dpps += $value->dpp_lain;
                $pajaks += $value->pajak;

                $data = [
                    "faktur_id" => $value->faktur_id,
                    "faktur_no" => $value->faktur_no,
                    "uraian" => $value->uraian,
                    "warna" => $value->warna,
                    "lot" => $value->lot,
                    "uom" => $value->uom,
                    "no_acc" => $value->no_acc,
                    "harga" => $value->harga
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

                $model->setTables("acc_faktur_penjualan_detail")->setWhereIn("acc_faktur_penjualan_detail.id", $dids)->delete();
                $model->setTables("acc_faktur_penjualan_detail")->save($data);

                $log .= "Join Item Data : " . logArrayToString("; ", (array)$datas);
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
