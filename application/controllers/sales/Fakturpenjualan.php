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
require_once APPPATH . '/third_party/vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\DummyPrintConnector;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
        $this->load->library("dompdflib");
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
    protected $jenisPPN = [
        "kbn" => "KBN",
        "non_kbn" => "non KBN",
        "tunai" => "Tunai",
        "ekspor" => "Ekspor",
        "sampel" => "Sampel"
    ];

    public function index() {
        $data['id_dept'] = 'ACCFPJ';
        $model = new $this->m_global;
        $data["tipe"] = $this->tipe;
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
            $data["detail"] = $model->setTables("acc_faktur_penjualan_detail fjd")->setOrder(["uraian" => "asc", "warna" => "asc"])
                            ->setJoins("acc_coa", "kode_coa = no_acc", "left")
                            ->setWheres(["faktur_id" => $data['datas']->id])
                            ->setSelects(["fjd.*", "acc_coa.nama as coa_nama"])->getData();

            $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
            $data["taxs"] = $model->setTables("tax")->setWheres(["type_inv" => "sale"])->setOrder(["nama" => "asc"])->getData();
            $data["uom"] = $model->setTables("uom")->setSelects(["short"])->setWheres(["jual" => "yes"])->getData();
            $data["jenisppn"] = $this->jenisPPN;
            $this->load->view('sales/v_faktur_penjualan_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    protected function _listData() {
        try {
            $model = new $this->m_global;
            $model->setTables("acc_faktur_penjualan")->setJoins("mst_status", "mst_status.kode = acc_faktur_penjualan.status", "left")
                    ->setOrder(["acc_faktur_penjualan.tanggal" => "desc"])->setSearch(["no_faktur", "no_faktur_pajak", "no_sj", "partner_nama", "no_faktur_internal"])
                    ->setOrders([null, "no_faktur", "no_faktur_pajak", "tanggal", "no_sj", "tipe", "marketing_nama", "partner_nama"])
                    ->setSelects(["acc_faktur_penjualan.*", "nama_status"])
                    ->setSelects(["CASE When (status <> 'confirm') then 'Belum Lunas' "
                        . "WHEN (piutang_rp = 0) then 'Lunas' "
                        . "When (piutang_rp = total_piutang_rp) then 'Belum Lunas' "
                        . "Else 'Lunas Sebagian' End as lunas"]);
            $tanggal = $this->input->post("tanggal");
            $marketing = $this->input->post("marketing");
            $tipe = $this->input->post("tipe");
            $customer = $this->input->post("customer");
            if ($tanggal !== "") {
                $tanggals = explode(" - ", $tanggal);
                $model->setWheres(["date(tanggal) >=" => $tanggals[0], "date(tanggal) <=" => $tanggals[1]]);
            }
            if ($marketing !== "") {
                $model->setWheres(["marketing_kode" => $marketing]);
            }
            if ($tipe !== "") {
                $model->setWheres(["tipe" => $tipe]);
            }
            if ($customer !== "") {
                $model->setWheres(["partner_id" => $customer]);
            }
            return $model;
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function list_data() {
        try {
            $data = array();
            $model = $this->_listData();
            $no = $_POST['start'];
            foreach ($model->getData() as $key => $value) {
                $no += 1;
                $kode_encrypt = encrypt_url($value->no_faktur);
                $fk = ($value->no_faktur_internal === '') ? 'Belum diisi' : $value->no_faktur_internal;
                $data [] = [
                    $no,
                    "<a href='" . base_url("sales/fakturpenjualan/edit/{$kode_encrypt}") . "'>{$fk}</a>",
                    $value->no_faktur_pajak,
                    $value->tanggal,
                    $value->no_sj,
                    $value->tipe,
                    $value->marketing_nama,
                    $value->partner_nama,
                    number_format($value->total_piutang_rp, 2),
                    $value->lunas,
                    $value->nama_status,
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

    public function export() {
        try {
            $model = $this->_listData();
            $data = $model->getData();

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $tanggal = $this->input->post("tanggal");
            $marketing = $this->input->post("marketing");
            $tipe = $this->input->post("tipe");
            $customer = $this->input->post("customer");
            $filter = "";
            if (count($data) > 0) {
                if ($marketing !== "") {
                    $filter .= "Marketing : {$data[0]->marketing_nama}, ";
                }
                if ($tipe !== "") {
                    $filter .= "Tipe : {$tipe}, ";
                }
                if ($customer !== "") {
                    $filter .= "Customer  : {$data[0]->partner_nama}, ";
                }
            }
            $sheet->setCellValue("A1", 'Periode');
            $sheet->setCellValue("B1", $tanggal);
            $sheet->setCellValue("A2", 'Filter : ');
            $sheet->setCellValue("B2", $filter);

            $row = 4;
            $sheet->setCellValue("A{$row}", 'No');
            $sheet->setCellValue("B{$row}", 'No Faktur');
            $sheet->setCellValue("C{$row}", 'No Faktur Pajak');
            $sheet->setCellValue("D{$row}", 'Tanggal');
            $sheet->setCellValue("E{$row}", 'No SJ');
            $sheet->setCellValue("F{$row}", 'Tipe');
            $sheet->setCellValue("g{$row}", 'Marketing');
            $sheet->setCellValue("h{$row}", 'Customer');
            $sheet->setCellValue("i{$row}", 'Total');
            $sheet->setCellValue("j{$row}", 'Pelunasan');
            $no = 0;
            foreach ($data as $key => $value) {
                $row++;
                $no++;
                $sheet->setCellValue("A{$row}", $no);
                $sheet->setCellValue("B{$row}", $value->no_faktur_internal);
                $sheet->setCellValue("C{$row}", $value->no_faktur_pajak);
                $sheet->setCellValue("D{$row}", $value->tanggal);
                $sheet->setCellValue("E{$row}", $value->no_sj);
                $sheet->setCellValue("F{$row}", $value->tipe);
                $sheet->setCellValue("G{$row}", $value->marketing_nama);
                $sheet->setCellValue("H{$row}", $value->partner_nama);
                $sheet->setCellValue("I{$row}", $value->total_piutang_rp);
                $sheet->setCellValue("j{$row}", $value->lunas);
            }
            $writer = new Xlsx($spreadsheet);
            $filename = "Faktur Penjualan per tanggal " . date("Y-m-d");
            $url = "dist/storages/report/fakturpenjualan";
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
                    ->setOrders([null, "do.no_sj", "do.no_picklist", "do.tanggal_dokumen", "pr.nama", "msg.nama_sales_group"])
                    ->setOrder(["do.tanggal_dokumen" => "asc"])->setWheres(["do.status" => "done", "faktur" => 0])
                    ->setSelects(["do.no_sj,do.no_picklist,tanggal_dokumen", "pr.nama as buyer", "msg.nama_sales_group as marketing"]);
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
                    date("Y-m-d", strtotime($field->tanggal_dokumen)),
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
                            ->setSelects(["p.keterangan,date(tanggal_dokumen) as tanggal_dokumen"])->setWheres(["do.status" => "done", "faktur" => 0, "do.no_sj" => $sj])->getDetail();

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
            $dariSJ = "0";
            $model = new $this->m_global;
            $this->_module->startTransaction();
            $lock = "token_increment WRITE,main_menu_sub READ, log_history WRITE,delivery_order do WRITE,delivery_order_detail dod READ,picklist_detail pd READ,acc_faktur_penjualan WRITE, acc_faktur_penjualan_detail WRITE";
            $this->_module->lock_tabel($lock);
            $checkSJ = $model->setTables("delivery_order do")->setJoins("delivery_order_detail dod", "(do.id = do_id and dod.status = 'done')", "left")
                            ->setJoins("picklist_detail pd", "picklist_detail_id = pd.id")->setWheres(["do.no_sj" => $nosj])
                            ->setGroups(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom"])
                            ->setSelects(["pd.corak_remark", "pd.warna_remark", "lebar_jadi", "uom_lebar_jadi", "uom", "faktur"])->setOrder(["pd.corak_remark" => "asc", "pd.warna_remark" => "asc"])
                            ->setSelects(["count(dod.barcode_id) as total_lot", "sum(pd.qty) as total_qty"])->getData();
            if (count($checkSJ) > 0) {
                if ((string) $checkSJ[0]->faktur === "1") {
                    throw new \Exception("SJ {$nosj} Sudah masuk faktur penjualan", 500);
                }
                $dariSJ = "1";
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
                "create_date" => date("Y-m-d H:i:s"),
                "dari_sj" => $dariSJ
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
//            $model->setTables("delivery_order do")->setWheres(["no_sj" => $nosj])->update(["faktur" => 1]);
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
            $tanggal = $this->input->post("tanggal");
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
                    ]
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
//                    [
//                        'field' => 'warna[]',
//                        'label' => 'Warna',
//                        'rules' => ['trim', 'required'],
//                        'errors' => [
//                            'required' => '{field} Pada Item harus diisi'
//                        ]
//                    ],
//                    [
//                        'field' => 'uomlot[]',
//                        'label' => 'Uom Lot',
//                        'rules' => ['trim', 'required'],
//                        'errors' => [
//                            'required' => '{field} Pada Item harus dipilih'
//                        ]
//                    ],
//                    [
//                        'field' => 'noacc[]',
//                        'label' => 'No Acc',
//                        'rules' => ['trim', 'required'],
//                        'errors' => [
//                            'required' => '{field} Pada Item harus dipilih'
//                        ]
//                    ],
                    [
                        'field' => 'harga[]',
                        'label' => 'Harga',
                        'rules' => ['trim', 'required', 'regex_match[/^-?\d*(,\d{3})*\.?\d*$/]'], ///^-?\d*\.?\d*$/
                        'errors' => [
                            'required' => '{field} Pada Item harus diisi',
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
                    [
                        'field' => 'qty[]',
                        'label' => 'QTY Uom',
                        'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
                        'errors' => [
                            "regex_match" => "{field} harus berupa number / desimal"
                        ]
                    ],
//                    [
//                        'field' => 'qtylot[]',
//                        'label' => 'QTY Lot',
//                        'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
//                        'errors' => [
//                            "regex_match" => "{field} harus berupa number / desimal"
//                        ]
//                    ],
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
            $uom = $this->input->post("uom");
            $nosjold = $this->input->post("no_sj_old");
            $noFakturInternal = $this->input->post("no_faktur_internal");
            $noInvEks = $this->input->post("no_inv_ekspor");
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
                "no_faktur_pajak" => $this->input->post("no_faktur_pajak"),
                "no_faktur_internal" => $noFakturInternal,
                "kurs" => $this->input->post("kurs"),
                "kurs_nominal" => $this->input->post("kurs_nominal"),
                "tipe_diskon" => $tipediskon,
                "nominal_diskon" => $nominalDiskon,
                "tax_id" => $this->input->post("tax"),
                "tax_value" => $taxVal,
                "dpp_lain" => 0,
                "ppn" => 0,
                "tanggal" => $tanggal,
                "final_total" => 0,
                "payment_term" => $this->input->post("payment_term"),
                "foot_note" => $this->input->post("footnote"),
                "no_inv_ekspor" => $noInvEks,
                "jenis_ppn" => $this->input->post("jenis_ppn")
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
                        $jumlah = round($qty[$key] * $hrg, 2);
                        $grandTotal += $jumlah;
                        $ddskon = round(($tipediskon === "%") ? ($jumlah * ($nominalDiskon / 100)) : $nominalDiskon, 2);
                        $grandDiskon += $ddskon;
                        $dpp = round(($jumlah - $ddskon) * 11 / 12, 2);
                        if (!$dppSet) {
                            $pajak = round(($jumlah - $ddskon) * $taxVal, 2);
                            $ppn_diskon = round(($ddskon) * $taxVal, 2);
                        } else {
                            $pajak = round($dpp * $taxVal, 2);
                            $dppDikson = round(($ddskon * 11) / 12, 2);
                            $ppn_diskon = round($dppDikson * $taxVal, 2);
                        }

                        $grandDiskonPpn += $ppn_diskon;
                        $totalHarga = round(($jumlah - $ddskon) + $pajak, 2);
//                        $header["ppn"] += $pajak;
//                        $header["dpp_lain"] += $dpp;
//                        $header["final_total"] += $totalHarga;

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
                            "diskon_ppn" => $ppn_diskon,
                            "uom" => $uom[$key],
                            "qty" => $qty[$key]
                        ];
                    }

                    if ($header["kurs_nominal"] > 1) {

                        $header["total_piutang_valas"] = round($header["final_total"], 2);
                        $header["piutang_valas"] = round($header["final_total"], 2);
                        $header["grand_total"] = round($grandTotal, 2);
                        $alldiskon = round(($tipediskon === "%") ? ($header["grand_total"] * ($nominalDiskon / 100)) : $nominalDiskon, 2);
                        $header["diskon"] = $alldiskon;
                        $dpp = round(($header["grand_total"] - $header["diskon"]) * 11 / 12, 2);
                        if (!$dppSet) {
                            $pajak = round(($header["grand_total"] - $header["diskon"]) * $taxVal, 2);
                            $ppn_diskon = round(($header["diskon"]) * $taxVal, 2);
                        } else {
                            $pajak = round($dpp * $taxVal, 2);
                            $dppDikson = round(($header["diskon"] * 11) / 12, 2);
                            $ppn_diskon = round($dppDikson * $taxVal, 2);
                        }
                        $header["dpp_lain"] = $dpp;
                        $header["diskon_ppn"] = round($ppn_diskon, 2);
                        $header["ppn"] = round($pajak, 2);
                        $header["final_total"] = round(($header["grand_total"] - $header["diskon"]) + $header["ppn"], 2);
                    } else {
                        $header["grand_total"] = round($grandTotal);
                        $alldiskon = round(($tipediskon === "%") ? ($header["grand_total"] * ($nominalDiskon / 100)) : $nominalDiskon);
                        $header["diskon"] = $alldiskon;
                        $dpp = round(($header["grand_total"] - $header["diskon"]) * 11 / 12);
                        if (!$dppSet) {
                            $pajak = round(($header["grand_total"] - $header["diskon"]) * $taxVal);
                            $ppn_diskon = round(($header["diskon"]) * $taxVal);
                        } else {
                            $pajak = round($dpp * $taxVal);
                            $dppDikson = round(($header["diskon"] * 11) / 12);
                            $ppn_diskon = round($dppDikson * $taxVal);
                        }
                        $header["diskon_ppn"] = $ppn_diskon;
                        $header["dpp_lain"] = $dpp;
                        $header["ppn"] = $pajak;
                        $header["final_total"] = round(($header["grand_total"] - $header["diskon"]) + $header["ppn"]);
                    }
                    $header["total_piutang_rp"] = round($header["final_total"] * $header["kurs_nominal"]);
                    $header["piutang_rp"] = round($header["final_total"] * $header["kurs_nominal"]);

                    $model->setTables("acc_faktur_penjualan_detail")->updateBatch($detail, "id");
                }
            }
            if ($new) {
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
                    $header["dari_sj"] = "1";
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
            $status = strtolower($status);
            $this->_module->startTransaction();
            $lock = "main_menu_sub READ, log_history WRITE,delivery_order do WRITE,delivery_order WRITE,acc_faktur_penjualan WRITE,"
                    . "acc_faktur_penjualan_detail WRITE,token_increment WRITE,acc_jurnal_entries_items WRITE,acc_jurnal_entries WRITE,"
                    . "setting READ,faktur_jurnal WRITE";
            $this->_module->lock_tabel($lock);
            $updateHead = ["status" => $status];
            switch ($status) {
                case "confirm":
                    if ($data->no_faktur_internal === "") {
                        throw new \Exception("No Faktur Internal Harus Terisi", 500);
                    }
                    if ($data->status !== "draft") {
                        throw new \Exception("Faktur Harus dalam status Draft", 500);
                    }
                    $dataDetailHarga = $model->setTables("acc_faktur_penjualan_detail")->setWheres(["faktur_no" => $kode, "harga < " => 0])->getDetail();
                    if ($dataDetailHarga) {
                        throw new \Exception("Harga Untuk Uraian {$dataDetailHarga->uraian} masih 0", 500);
                    }

                    $CheckCoa = $model->setWheres(["no_acc" => "", "faktur_no" => $kode], true)->getDetail();
                    if ($CheckCoa) {
                        throw new \Exception("No ACC Harap diisi terlebih dahulu", 500);
                    }
                    $getCoaDefault = $model->setTables("setting")->setWheres(["setting_name" => "coa_penjualan_{$data->tipe}"])->getDetail();
                    if (!$getCoaDefault)
                        throw new \Exception("Coa Penjualan {$data->tipe} belum ditentukan", 500);

                    $ceksSj = $model->setTables("delivery_order")->setWheres(["no_sj" => $data->no_sj, "status" => "done", "faktur" => 1])->getDetail();

                    if ($ceksSj)
                        throw new \Exception("No Surat jalan Sudah masuk faktur", 500);

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
                        "origin" => "{$data->no_faktur_internal}", "status" => "posted", "tanggal_dibuat" => $data->tanggal, "tipe" => "PJ",
                        "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => "{$data->partner_nama}"];

                    $detail = $model->setTables("acc_faktur_penjualan_detail")->setWheres(["faktur_no" => $kode])->getData();
                    $jurnalItems = [];
                    $fakturJurnal = [];
                    $sjs = explode("/", $data->no_sj);
                    $totalC = 0;
                    $totalD = 0;
                    if (in_array($sjs[0], ["SJM", "SAMPLE"])) {
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "Piutang",
                            "reff_note" => "",
                            "partner" => $data->partner_id,
                            "kode_coa" => $getCoaDefault->value,
                            "posisi" => "D",
                            "nominal_curr" => 0,
                            "kurs" => $data->kurs_nominal,
                            "kode_mua" => $data->nama_kurs,
                            "nominal" => 0,
                            "row_order" => 1
                        );
                        foreach ($detail as $key => $value) {
                            $warna = ($value->warna === "") ? "" : " / {$value->warna}";
                            $jurnalItems[] = array(
                                "kode" => $jurnal,
                                "nama" => "{$value->uraian}{$warna} / {$value->qty} {$value->uom}",
                                "reff_note" => "",
                                "partner" => $data->partner_id,
                                "kode_coa" => $value->no_acc,
                                "posisi" => "C",
                                "nominal_curr" => 0,
                                "kurs" => $data->kurs_nominal,
                                "kode_mua" => $data->nama_kurs,
                                "nominal" => 0,
                                "row_order" => (count($jurnalItems) + 1)
                            );
                        }
                    } else {
                        $piutang = round($data->final_total * $data->kurs_nominal);
                        $totalD += $piutang;
                        $jurnalItems[] = array(
                            "kode" => $jurnal,
                            "nama" => "Piutang",
                            "reff_note" => "",
                            "partner" => $data->partner_id,
                            "kode_coa" => $getCoaDefault->value,
                            "posisi" => "D",
                            "nominal_curr" => ($data->final_total),
                            "kurs" => $data->kurs_nominal,
                            "kode_mua" => $data->nama_kurs,
                            "nominal" => $piutang,
                            "row_order" => 1
                        );

                        $getCoaDefaultPpnDisc = $model->setTables("setting")->setWheres(["setting_name" => "coa_penjualan_ppn_diskon"])->getDetail();
                        if ($data->diskon_ppn > 0) {
                            $totalD += round($data->diskon_ppn * $data->kurs_nominal, 2);
                            $jurnalItems[] = array(
                                "kode" => $jurnal,
                                "nama" => "PPN Diskon",
                                "reff_note" => "",
                                "partner" => $data->partner_id,
                                "kode_coa" => $getCoaDefaultPpnDisc->value,
                                "posisi" => "D",
                                "nominal_curr" => $data->diskon_ppn,
                                "kurs" => $data->kurs_nominal,
                                "kode_mua" => $data->nama_kurs,
                                "nominal" => round($data->diskon_ppn * $data->kurs_nominal, 2),
                                "row_order" => (count($jurnalItems) + 1)
                            );
                        }
                        $getCoaDefaultDppDisc = $model->setTables("setting")->setWheres(["setting_name" => "coa_penjualan_dpp_diskon"])->getDetail();
                        if ($data->diskon > 0) {
                            $totalD += round($data->diskon * $data->kurs_nominal, 2);
                            $jurnalItems[] = array(
                                "kode" => $jurnal,
                                "nama" => "DPP Diskon",
                                "reff_note" => "",
                                "partner" => $data->partner_id,
                                "kode_coa" => $getCoaDefaultDppDisc->value,
                                "posisi" => "D",
                                "nominal_curr" => $data->diskon,
                                "kurs" => $data->kurs_nominal,
                                "kode_mua" => $data->nama_kurs,
                                "nominal" => round($data->diskon * $data->kurs_nominal, 2),
                                "row_order" => (count($jurnalItems) + 1)
                            );
                        }
                        $allDiskon = $data->diskon + $data->diskon_ppn;
                        if ($allDiskon > 0) {
                            $totalC += round($allDiskon * $data->kurs_nominal, 2);
                            $jurnalItems[] = array(
                                "kode" => $jurnal,
                                "nama" => "Diskon",
                                "reff_note" => "",
                                "partner" => $data->partner_id,
                                "kode_coa" => $getCoaDefault->value,
                                "posisi" => "C",
                                "nominal_curr" => $allDiskon,
                                "kurs" => $data->kurs_nominal,
                                "kode_mua" => $data->nama_kurs,
                                "nominal" => round($allDiskon * $data->kurs_nominal, 2),
                                "row_order" => (count($jurnalItems) + 1)
                            );
                        }
                        if ($data->ppn > 0) {
                            $totalC += round($data->ppn * $data->kurs_nominal, 2);
                            $jurnalItems[] = array(
                                "kode" => $jurnal,
                                "nama" => "PPN",
                                "reff_note" => "",
                                "partner" => $data->partner_id,
                                "kode_coa" => $getCoaDefaultPpnDisc->value,
                                "posisi" => "C",
                                "nominal_curr" => $data->ppn,
                                "kurs" => $data->kurs_nominal,
                                "kode_mua" => $data->nama_kurs,
                                "nominal" => round($data->ppn * $data->kurs_nominal, 2),
                                "row_order" => (count($jurnalItems) + 1)
                            );
                        }

                        foreach ($detail as $key => $value) {
                            $warna = ($value->warna === "") ? "" : " / {$value->warna}";
                            $totalC += round(($value->jumlah - $value->diskon) * $data->kurs_nominal, 2);
//                            $totalPiutang += round($value->jumlah * $data->kurs_nominal, 2);
//                            $totalPiutangCurr += $value->jumlah;
                            $rowOrder = (count($jurnalItems) + 1);
                            $jurnalItems[] = array(
                                "kode" => $jurnal,
                                "nama" => "{$value->uraian}{$warna} / {$value->qty} {$value->uom}",
                                "reff_note" => "",
                                "partner" => $data->partner_id,
                                "kode_coa" => $value->no_acc,
                                "posisi" => "C",
                                "nominal_curr" => $value->jumlah - $value->diskon,
                                "kurs" => $data->kurs_nominal,
                                "kode_mua" => $data->nama_kurs,
                                "nominal" => round(($value->jumlah - $value->diskon) * $data->kurs_nominal, 2),
                                "row_order" => $rowOrder
                            );
                            $fakturJurnal[] = array(
                                "no_faktur" => $value->faktur_no,
                                "faktur_detail_id" => $value->id,
                                "jurnal_kode" => $jurnal,
                                "jurnal_order" => $rowOrder
                            );
                        }
                        $hsl = round($totalC - $totalD, 2);
                        if ($hsl !== (double) 0) {
                            $coaSelisih = $model->setTables("setting")->setWheres(["setting_name" => "selisih_pembulatan_penjualan"])->getDetail();
                            $jurnalItems[] = array(
                                "kode" => $jurnal,
                                "nama" => "Selisih Pembulatan Penjualan",
                                "reff_note" => "",
                                "partner" => $data->partner_id,
                                "kode_coa" => $coaSelisih->value,
                                "posisi" => ($hsl > 0) ? "D" : "C",
                                "nominal_curr" => abs($hsl),
                                "kurs" => $data->kurs_nominal,
                                "kode_mua" => $data->nama_kurs,
                                "nominal" => round(abs($hsl), 2),
                                "row_order" => (count($jurnalItems) + 1)
                            );
                        }
//                        $jurnalItems[0]["nominal_curr"] = ($totalPiutangCurr + $data->ppn);
//                        $jurnalItems[0]["nominal"] = $totalPiutang + ($data->ppn * $data->kurs_nominal);
                    }

                    if ($data->jurnal !== "") {
                        $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $jurnal])->update($jurnalData);
                        $model->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $jurnal])->delete();
                        $model->setTables("faktur_jurnal")->setWheres(["jurnal_kode" => $jurnal])->delete();
                    } else {
                        $model->setTables("acc_jurnal_entries")->save($jurnalData);
                        $model->setTables("acc_faktur_penjualan")->setWheres(["id" => $data->id])->update(["jurnal" => $jurnal]);
                        $this->_module->gen_history_new($sub_menu, $kode, 'edit', "No Jurnal : {$jurnal}", $username);
                    }

                    $model->setTables("delivery_order")->setWheres(["no_sj" => $data->no_sj, "status" => "done"])->update(["faktur" => 1]);
                    $model->setTables("acc_jurnal_entries_items")->saveBatch($jurnalItems);
                    $model->setTables("faktur_jurnal")->saveBatch($fakturJurnal);
                    $log = "Header -> " . logArrayToString("; ", $jurnalData);
                    $log .= "\nDETAIL -> " . logArrayToString("; ", $jurnalItems);
                    $this->_module->gen_history_new("jurnal_entries", $jurnal, "{$stt}", $log, $username);

                    if ((double) $data->total_piutang_rp === 0.0000) {
                        $updateHead = array_merge($updateHead, ["lunas" => 1]);
                    }
                    break;

                case "draft":
                    if ($data->status !== "cancel") {
                        throw new \exception("Data Faktur Penjualan {$kode} dalam status {$data->status}", 500);
                    }
                    break;
                default:
                    if ($data->lunas == 1) {
                        throw new \exception("Data Faktur Penjualan {$kode} sudah masuk pada pelunasan", 500);
                    }
                    $fin = $data->final_total * $data->kurs_nominal;
                    if ((double) round($fin) !== (double) $data->piutang_rp) {
                        throw new \exception("Data Faktur Penjualan {$kode} sudah masuk pada pelunasan.", 500);
                    }

                    $model->setTables("acc_jurnal_entries")->setWheres(["kode" => $data->jurnal])->update(["status" => "unposted"]);
                    $model->setTables("delivery_order")->setWheres(["no_sj" => $data->no_sj, "status" => "done"])->update(["faktur" => 0]);
                    $this->_module->gen_history_new("jurnal_entries", $data->jurnal, 'edit', "Merubah Status Ke unposted dari penjualan", $username);
                    break;
            }
            $model->setTables("acc_faktur_penjualan")->setWheres(["no_faktur" => $kode])->update($updateHead);
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
            $model->setTables("acc_faktur_penjualan")->setWheres(["no_faktur" => $kode])->update($update);

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
            $detail = $model->setTables("acc_faktur_penjualan_detail fjd")
                            ->setJoins("acc_coa", "kode_coa = no_acc", "left")
                            ->setSelects(["fjd.*", "acc_coa.nama as coa_nama"])
                            ->setWheres(["id" => $ids])->getDetail();
            if (!$detail) {
                throw new \Exception('Data Item tidak ditemukan', 500);
            }
            $uom = $model->setTables("uom")->setSelects(["short"])->setWheres(["jual" => "yes"])->getData();
            $html = $this->load->view('sales/modal/v_split_item_fp', ["data" => $detail, "id" => $id, "uomLot" => $this->uomLot, "uom" => $uom], true);
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
            $lock = "acc_faktur_penjualan READ, acc_faktur_penjualan_detail WRITE,user READ, main_menu_sub READ, log_history WRITE,setting READ,uom read";
            $this->_module->lock_tabel($lock);
            $model = new $this->m_global;
            $getDetail = $model->setTables("acc_faktur_penjualan_detail")->setJoins("acc_faktur_penjualan", "faktur_id = acc_faktur_penjualan.id")
                            ->setSelects(["acc_faktur_penjualan_detail.*", "nominal_diskon,tipe_diskon,tax_value"])
                            ->setWheres(["acc_faktur_penjualan_detail.id" => $ids])->getDetail();
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

            $jumlah = round($hasilKurang * $getDetail->harga, 2);
            $ddskon = round(($getDetail->tipe_diskon === "%") ? ($jumlah * ($getDetail->nominal_diskon / 100)) : $getDetail->nominal_diskon, 2);
            $dpp = round((($jumlah - $ddskon) * 11) / 12, 2);
            if (!$dppSet) {
                $pajak = round(($jumlah - $ddskon) * $getDetail->tax_value, 2);
                $ppn_diskon = round($ddskon * $getDetail->tax_value, 2);
            } else {
                $pajak = round($dpp * $getDetail->tax_value, 2);
                $dppDis = round($dpp = (($ddskon) * 11) / 12, 2);
                $ppn_diskon = round($dppDis * $getDetail->tax_value, 2);
            }
            $totalHarga = round(($jumlah - $ddskon) + ($pajak), 2);
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

            $jumlah = round($qty * $getDetail->harga, 2);
            $ddskon = round(($getDetail->tipe_diskon === "%") ? ($jumlah * ($getDetail->nominal_diskon / 100)) : $getDetail->nominal_diskon, 2);
            $dpp = round((($jumlah - $ddskon) * 11) / 12, 2);
            if (!$dppSet) {
                $pajak = round(($jumlah - $ddskon) * $getDetail->tax_value, 2);
                $ppn_diskon = round($ddskon * $getDetail->tax_value, 2);
            } else {
                $pajak = round($dpp * $getDetail->tax_value, 2);
                $dppDis = round((($ddskon) * 11) / 12, 2);
                $ppn_diskon = round($dppDis * $getDetail->tax_value, 2);
            }
            $totalHarga = round(($jumlah - $ddskon) + ($pajak), 2);
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
            $idnew = $model->setTables("acc_faktur_penjualan_detail")->save($split);
            $model->setWheres(["id" => $ids])->update($updateSpilit);

            $data["uom"] = $model->setTables("uom")->setSelects(["short"])->setWheres(["jual" => "yes"])->getData();
            $data["uomLot"] = $this->uomLot;
            $data["data"] = $model->setTables("acc_faktur_penjualan_detail")->setWhereIn("id", [$idnew, $ids])->getData();
            $html = $this->load->view('sales/modal/v_split_join_tr', $data, true);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            $log = "update spilit uraian = {$getDetail->uraian} , warna = {$getDetail->warna} " . logArrayToString("; ", $updateSpilit);
            $log .= "\nhasil split " . logArrayToString("; ", $split);
            $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
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

            $check->final_total -= ($check->kurs_nominal > 1) ? $finalTotal : round($finalTotal);
            $check->diskon_ppn -= ($check->kurs_nominal > 1) ? $ppnDiskon : round($ppnDiskon);
            $check->diskon -= ($check->kurs_nominal > 1) ? $diskon : round($diskon);
            $check->grand_total -= ($check->kurs_nominal > 1) ? $total : round($total);
            $check->ppn -= ($check->kurs_nominal > 1) ? $pajak : round($pajak);
            $check->dpp_lain -= ($check->kurs_nominal > 1) ? $dpp : round($dpp);

            if ($check->kurs_nominal > 1) {
                $check->total_piutang_valas = round($check->final_total, 2);
                $check->piutang_valas = round($check->final_total, 2);
            }
            $check->total_piutang_rp = round($check->final_total * $check->kurs_nominal);
            $check->piutang_rp = round($check->final_total * $check->kurs_nominal);

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
            $lock = "acc_faktur_penjualan_detail WRITE,user READ, main_menu_sub READ, log_history WRITE,UOM read";
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
            $html = "";
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
                $ids = $model->setTables("acc_faktur_penjualan_detail")->save($data);

                $log .= "Join Item Data : " . logArrayToString("; ", (array) $datas);
                $log .= "\nhasil split " . logArrayToString("; ", $data);

                $data["uom"] = $model->setTables("uom")->setSelects(["short"])->setWheres(["jual" => "yes"])->getData();
                $data["uomLot"] = $this->uomLot;
                $data["data"] = $model->setTables("acc_faktur_penjualan_detail")->setWhereIn("id", [$ids])->getData();
                $html = $this->load->view('sales/modal/v_split_join_tr', $data, true);
            }


            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            if ($log !== "")
                $this->_module->gen_history_new($sub_menu, $kode, "edit", $log, $username);

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
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
//            [
//                'field' => 'qty_lot',
//                'label' => 'Qty LOT',
//                'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
//                'errors' => [
//                    'required' => '{field} Harus diisi',
//                    "regex_match" => "{field} harus berupa number / desimal"
//                ]
//            ],
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
                'field' => 'uom',
                'label' => 'Uom',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                ]
            ],
//            [
//                'field' => 'uom_lot',
//                'label' => 'Uom Lot',
//                'rules' => ['required'],
//                'errors' => [
//                    'required' => '{field} Harus dipilih',
//                ]
//            ],
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
            $lock = "acc_faktur_penjualan_detail WRITE,acc_faktur_penjualan WRITE, main_menu_sub READ, log_history WRITE,setting READ";
            $this->_module->lock_tabel($lock);
            $dppSet = $model->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();

            $harga = $nom = str_replace(",", "", $this->input->post("harga"));
            $qty = $this->input->post("qty");
            $jumlah = round($harga * $qty, 2);
            $ddskon = round(($check->tipe_diskon === "%") ? ($jumlah * ($check->nominal_diskon / 100)) : $check->nominal_diskon, 2);
            $dpp = round(($jumlah - $ddskon) * 11 / 12, 2);
            if (!$dppSet) {
                $pajak = round(($jumlah - $ddskon) * $check->tax_value, 2);
                $ppn_diskon = round($ddskon * $check->tax_value, 2);
            } else {
                $pajak = round($dpp * $check->tax_value, 2);
                $dppDiskon = round(($ddskon * 11) / 12, 2);
                $ppn_diskon = round($dppDiskon * $check->tax_value, 2);
            }
            $totalHarga = (($jumlah - $ddskon) + $pajak);

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

            if ($check->kurs_nominal > 1) {
                $check->dpp_lain += $dpp;
                $check->ppn += $pajak;
                $check->grand_total += $jumlah;
                $check->final_total += $totalHarga;
                $check->total_piutang_valas = round($check->final_total, 2);
                $check->piutang_valas = round($check->final_total, 2);
                $check->diskon_ppn += $ppn_diskon;
            } else {
                $check->dpp_lain += round($dpp);
                $check->ppn += round($pajak);
                $check->grand_total += round($jumlah);
                $check->final_total += round($totalHarga);
                $check->diskon_ppn += round($ppn_diskon);
            }
            $check->total_piutang_rp = round($check->final_total * $check->kurs_nominal);
            $check->piutang_rp = round($check->final_total * $check->kurs_nominal);

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
                            ->setSelects(["acc_faktur_penjualan.*", 'invoice_street as alamat',
                                "tax.nama as nama_tax"])->setWheres(["no_faktur" => $kode])->getDetail();
            if (!$data["head"]) {
                throw new \exception("Data Faktur Penjualan {$kode} tidak ditemukan", 500);
            }
            $data["alamat"] = $model->setTables("setting")->setWheres(["setting_name" => "alamat_fp"])->getDetail();
            $data["npwp"] = $model->setWheres(["setting_name" => "npwp_fp"], true)->getDetail();
            $data["detail"] = $model->setTables("acc_faktur_penjualan_detail")->setWheres(["faktur_no" => $kode])->setOrder(["uraian" => "asc", "warna" => "asc"])->getData();
            if ($data["head"]->kurs_nominal > 1) {
                $data["curr"] = $model->setTables("currency_kurs")->setWheres(["currency_kurs.id" => $data["head"]->kurs])
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
            $mpdf->autoPageBreak = true;
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

    protected function hitungLinesPrint(Printer $printer, &$lines, &$halaman) {
        $lines += 1;
        if ($halaman === 1 && $lines === 64) {
            $buff = $printer->getPrintConnector();
            $buff->write("\x0c");
            $halaman += 1;

            $buff->write("\x1b" . chr(2));
            $buff->write("\x1bC" . chr(66));
            $buff->write("\x1bO");
        } else {
            $printer->feed();
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
                            ->setSelects(["acc_faktur_penjualan.*", 'invoice_street as alamat',
                                "tax.nama as nama_tax"])->setWheres(["no_faktur" => $kode])->getDetail();
            if (!$head) {
                throw new \exception("Data Faktur Penjualan {$kode} tidak ditemukan", 500);
            }
            $alamat = $model->setTables("setting")->setWheres(["setting_name" => "alamat_fp"])->getDetail();
            $npwp = $model->setWheres(["setting_name" => "npwp_fp"], true)->getDetail();
            $buff = $printer->getPrintConnector();

            $buff->write("\x1bg" . chr(1));
            $alamat = str_split(trim(preg_replace('/\s+/', ' ', $alamat->value)), 40);
            foreach ($alamat as $key => $value) {
                $printer->text(trim($value));
                if ($key === 0) {
                    $printer->text(str_pad("", 35));
                    $printer->text(str_pad("Bandung, " . date("d-m-Y", strtotime($head->tanggal)), 30, " ", STR_PAD_LEFT));
                }
                $printer->text("\n");
//                $this->hitungLinesPrint($printer, $lines, $halaman);
            }
            $printer->text("NPWP : " . ($npwp->value ?? ""));
            $printer->feed();
//            $this->hitungLinesPrint($printer, $lines, $halaman);
            $dataPrint[] = (object) ["img" => "logo300x50px.prn", "data" => serialize($connector->getData())];
            $connector->clear();
            $printer->close();
            $printer = new Printer($connector);
            $buff = $printer->getPrintConnector();

            $buff->write("\x1b" . chr(2));
            $buff->write("\x1bC" . chr(58));
            $buff->write("\x1bO");
//            $buff->write("\x1bN" . chr(4));

            $buff->write("\x1bg" . chr(1));
            $lines = 8;
            $halaman = 1;
            $printer->text(str_pad("No. Faktur", 15));
            $printer->text(str_pad(": {$head->no_faktur_internal}", 30));
            $printer->text(str_pad("", 5));
            $printer->text(str_pad("", 19));
            $printer->text("Kepada Yth.,");
//            $printer->feed();
            $this->hitungLinesPrint($printer, $lines, $halaman);
            $printer->text(str_pad("No. Surat Jalan", 15));
            $printer->text(str_pad(": {$head->no_sj}", 30));
            $printer->text(str_pad("", 24));
            $kpd = str_split($head->partner_nama, 40);
            $buff->write("\x1bE" . chr(1));
            foreach ($kpd as $key => $value) {
                if ($key > 0) {
                    $printer->text(str_pad("", 90));
                }
                $printer->text(str_pad(trim($value), 40));
            }
            $buff->write("\x1bF" . chr(0));
            $buff->write("\x1bg" . chr(1));
            $this->hitungLinesPrint($printer, $lines, $halaman);
            $printer->text(str_pad(" ", 69));
            $printer->text("Alamat 1 :");
            $this->hitungLinesPrint($printer, $lines, $halaman);
            $alm = preg_replace('/\s\s+/', '*#*', "{$head->alamat}");
            $alm = explode("*#*", str_replace(array("\n", "\r"), "*#*", $alm));
            foreach ($alm as $key => $value) {
                $line = str_pad("", 69);
                $line .= str_pad(trim($value), 50);
                $printer->text($line);
                $this->hitungLinesPrint($printer, $lines, $halaman);
            }

            $detail = $model->setTables("acc_faktur_penjualan_detail")->setWheres(["faktur_no" => $kode])->setOrder(["uraian" => "asc", "warna" => "asc"])->getData();
            $printer->selectPrintMode();
            $buff->write("\x1bX" . chr(15));
            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
            $printer->text(str_pad(" ", 137));
            $printer->setUnderline(Printer::UNDERLINE_NONE);
            if ($head->kurs_nominal > 1) {
//                valas
                $curr = $model->setTables("currency_kurs")->setWheres(["currency_kurs.id" => $head->kurs])
                                ->setJoins("currency", "currency.nama = currency_kurs.currency", "left")
                                ->setSelects(["currency.*,ket"])->getDetail();
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad("No", 3));
                $printer->text(str_pad("Jenis Barang / Uraian", 40, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Quantity", 30, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Harga Satuan", 28, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Jumlah", 36, " ", STR_PAD_BOTH));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad(" ", 43));
                $printer->text(str_pad("Gul/PCS", 15, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Satuan", 15, " ", STR_PAD_BOTH));
                $printer->text(str_pad($curr->nama, 11, " ", STR_PAD_BOTH));
                $printer->text(str_pad("IDR", 17, " ", STR_PAD_BOTH));
                $printer->text(str_pad($curr->nama, 16, " ", STR_PAD_BOTH));
                $printer->text(str_pad("IDR", 20, " ", STR_PAD_BOTH));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $subtotal = 0;
                $subtotalValas = 0;
                $totalQty = 0;
                $totalQtyLot = 0;
                $uomLot = "";
                $uom = "";
                foreach ($detail as $key => $value) {
                    $subtotal += round($value->jumlah * $head->kurs_nominal);
                    $subtotalValas += $value->jumlah;
                    $totalQty += $value->qty;
                    $totalQtyLot += $value->qty_lot;
                    $uomLot = $value->lot;
                    $uom = $value->uom;

                    $no = str_split(($key + 1), 3);
                    foreach ($no as $k => $vls) {
                        $vls = trim($vls);
                        $no[$k] = $vls;
                    }
                    $warna = ($value->warna === "") ? "" : " / {$value->warna}";
                    $uraian = str_split($value->uraian . $warna, 39);
                    foreach ($uraian as $k => $vls) {
                        $vls = trim($vls);
                        $uraian[$k] = $vls;
                    }
                    if ($value->no_po != "") {
                        $np = preg_replace('/\s\s+/', '*#*', "No.PO :{$value->no_po}");
//                        $nopo = str_split($np, 49);
                        $nopo = explode("*#*", str_replace(array("\n", "\r"), "*#*", $np));
                        foreach ($nopo as $k => $vls) {
                            $vls = trim($vls);
                            $uraian[] = $vls;
                        }
                    }

                    $qtylot = str_split(number_format($value->qty_lot, 2) . " {$value->lot}", 14);
                    foreach ($qtylot as $k => $vls) {
                        $vls = trim($vls);
                        $qtylot[$k] = $vls;
                    }
                    $qtyuom = str_split(number_format($value->qty, 2) . " {$value->uom}", 14);
                    foreach ($qtyuom as $k => $vls) {
                        $vls = trim($vls);
                        $qtyuom[$k] = $vls;
                    }
                    $symbol = str_split(" {$curr->symbol}", 4);
                    foreach ($symbol as $k => $vls) {
                        $symbol[$k] = $vls;
                    }
                    $valHarga = str_split(number_format($value->harga, 4), 7);
                    foreach ($valHarga as $k => $vls) {
                        $vls = trim($vls);
                        $valHarga[$k] = $vls;
                    }
                    $symbolRp = str_split(" Rp.", 4);
                    foreach ($symbolRp as $k => $vls) {
                        $symbolRp[$k] = $vls;
                    }
                    $harga = str_split(number_format(($value->harga * $head->kurs_nominal), 2), 13);
                    foreach ($harga as $k => $vls) {
                        $vls = trim($vls);
                        $harga[$k] = $vls;
                    }
                    $valJumlah = str_split(number_format($value->jumlah, 2), 12);
                    foreach ($valJumlah as $k => $vls) {
                        $vls = trim($vls);
                        $valJumlah[$k] = $vls;
                    }
                    $jumlah = str_split(number_format(($value->jumlah * $head->kurs_nominal), 2), 16);
                    foreach ($jumlah as $k => $vls) {
                        $vls = trim($vls);
                        $jumlah[$k] = $vls;
                    }

                    $counter = 0;
                    $temp = [];
                    $temp[] = count($no);
                    $temp[] = count($uraian);
                    $temp[] = count($qtylot);
                    $temp[] = count($qtyuom);
                    $temp[] = count($symbol);
                    $temp[] = count($symbolRp);
                    $temp[] = count($valHarga);
                    $temp[] = count($harga);
                    $temp[] = count($valJumlah);
                    $temp[] = count($jumlah);
                    $counter = max($temp);
                    for ($i = 0; $i < $counter; $i++) {
                        if (($counter - 1) === $i) {
                            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                        }
                        $line = (isset($no[$i])) ? str_pad($no[$i], 3) : str_pad("", 3);
                        $line .= (isset($uraian[$i])) ? str_pad($uraian[$i], 40, " ", STR_PAD_RIGHT) : str_pad("", 40, " ", STR_PAD_RIGHT);
                        $line .= (isset($qtylot[$i])) ? str_pad("{$qtylot[$i]} ", 15, " ", STR_PAD_LEFT) : str_pad("", 15, " ", STR_PAD_LEFT);
                        $line .= (isset($qtyuom[$i])) ? str_pad("{$qtyuom[$i]} ", 15, " ", STR_PAD_LEFT) : str_pad("", 15, " ", STR_PAD_LEFT);
                        $line .= (isset($valHarga[$i])) ? str_pad("{$symbol[$i]} {$valHarga[$i]}", 11, " ", STR_PAD_LEFT) : str_pad("", 11, " ", STR_PAD_LEFT);
                        $line .= (isset($harga[$i])) ? str_pad("{$symbolRp[$i]} {$harga[$i]}", 17, " ", STR_PAD_LEFT) : str_pad("", 17, " ", STR_PAD_LEFT);
                        $line .= (isset($valJumlah[$i])) ? str_pad("{$symbol[$i]} {$valJumlah[$i]}", 16, " ", STR_PAD_LEFT) : str_pad("", 16, " ", STR_PAD_LEFT);
                        $line .= (isset($jumlah[$i])) ? str_pad("{$symbolRp[$i]} {$jumlah[$i]}", 20, " ", STR_PAD_LEFT) : str_pad("", 20, " ", STR_PAD_LEFT);
                        $printer->text($line);
                        $this->hitungLinesPrint($printer, $lines, $halaman);
                        if (($counter - 1) === $i) {
                            $printer->setUnderline(Printer::UNDERLINE_NONE);
                        }
                    }
                }
                $totalQtyLot = number_format($totalQtyLot, 2);
                $totalQty = number_format($totalQty, 2);
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->text(str_pad("Total Qty : ", 43, " ", STR_PAD_LEFT));
                $printer->text(str_pad("{$totalQtyLot} {$uomLot} ", 15, " ", STR_PAD_LEFT));
                $printer->text(str_pad("{$totalQty} {$uom} ", 15, " ", STR_PAD_LEFT));
                $printer->text(str_pad("", 64));
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->setUnderline(Printer::UNDERLINE_NONE);
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $diskonValas = number_format(round($head->diskon), 2, ".", ",");
                $diskon = number_format(round($head->diskon * $head->kurs_nominal), 2, ".", ",");
                $ppnValas = number_format($head->ppn - $head->diskon_ppn, 2, ".", ",");
                $subtotal2 = (round($head->grand_total * $head->kurs_nominal) - round($head->diskon * $head->kurs_nominal));
                $ppn = number_format(round($subtotal2), 2, ".", ",");
                $finalTotalValas = number_format(round($head->final_total, 2), 2, ".", ",");
                $finalTotal = number_format(round($head->final_total * $head->kurs_nominal), 2, ".", ",");
                $totals = explode(".", round($head->final_total, 2));

                $terbilang = Kwitansi($totals[0]);
                $terbilang2 = "";
                if (isset($totals[1])) {
                    if ($totals[1] > 0) {
                        $terbilang2 .= " Koma";
                        $terbilang2 .= KwitansiDesimal($totals[1]);
                    }
                }
                $spltTbl = str_split(trim($terbilang) . "{$terbilang2} {$curr->ket}", 72);

                $printer->text(str_pad("(*)Kurs : Rp. " . number_format($head->kurs_nominal, 2), 89, " "));
                $printer->text(str_pad("Subtotal", 8, " ", STR_PAD_RIGHT));
                $printer->text(str_pad("{$curr->symbol} " . number_format($subtotalValas, 2), 16, " ", STR_PAD_LEFT));
                $printer->text(str_pad("Rp. " . number_format((round($head->grand_total * $head->kurs_nominal)), 2, ".", ","), 21, " ", STR_PAD_LEFT));
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->text(str_pad(" Terbilang : " . ($spltTbl[0] ?? " "), 89, " "));
                $printer->text(str_pad("Discount", 8, " ", STR_PAD_RIGHT));
                $printer->text(str_pad("{$curr->symbol} " . number_format(round($diskonValas), 2), 16, " ", STR_PAD_LEFT));
                $printer->text(str_pad("Rp. " . number_format(round($head->diskon * $head->kurs_nominal), 2, ".", ","), 21, " ", STR_PAD_LEFT));
//                $printer->feed();
                $trblng = "";
                if ($head->tax_id > 0) {
                    $this->hitungLinesPrint($printer, $lines, $halaman);
                    $printer->text(str_pad(" Terbilang : " . ($spltTbl[1] ?? " "), 89, " "));
                    $printer->text(str_pad("PPN", 8, " ", STR_PAD_RIGHT));
                    $printer->text(str_pad("{$curr->symbol} " . number_format($ppnValas, 2), 16, " ", STR_PAD_LEFT));
                    $printer->text(str_pad("Rp. " . $ppn, 21, " ", STR_PAD_LEFT));
                } else {
                    $trblng = ($spltTbl[1] ?? " ");
                }
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->text(str_pad(" ", 12));
                $printer->text(str_pad(($trblng), 77, " "));
                $printer->text(str_pad("TOTAL", 8, " ", STR_PAD_RIGHT));
                $printer->text(str_pad("{$curr->symbol} " . $finalTotalValas, 16, " ", STR_PAD_LEFT));
                $printer->text(str_pad("Rp. " . $finalTotal, 21, " ", STR_PAD_LEFT));
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad(" ", 137));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
//                $printer->feed();
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $this->hitungLinesPrint($printer, $lines, $halaman);
            } else {

                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad("No", 3));
                $printer->text(str_pad("Jenis Barang / Uraian", 50, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Quantity", 39, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Harga Satuan", 20, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Jumlah", 25, " ", STR_PAD_BOTH));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad(" ", 53));
                $printer->text(str_pad("Gul/PCS", 19, " ", STR_PAD_BOTH));
                $printer->text(str_pad("Satuan", 20, " ", STR_PAD_BOTH));
                $printer->text(str_pad(" ", 45));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $subtotal = 0;
                $totalQty = 0;
                $totalQtyLot = 0;
                $uomLot = "";
                $uom = "";
                foreach ($detail as $key => $value) {
                    $subtotal += $value->jumlah;
                    $totalQty += $value->qty;
                    $totalQtyLot += $value->qty_lot;
                    $uomLot = $value->lot;
                    $uom = $value->uom;

                    $no = str_split(($key + 1), 3);
                    foreach ($no as $k => $vls) {
                        $vls = trim($vls);
                        $no[$k] = $vls;
                    }
                    $warna = ($value->warna === "") ? "" : " / {$value->warna}";
                    $uraian = str_split($value->uraian . $warna, 50);
                    foreach ($uraian as $k => $vls) {
                        $vls = trim($vls);
                        $uraian[$k] = $vls;
                    }
                    if ($value->no_po != "") {
                        $np = preg_replace('/\s\s+/', '*#*', "No.PO :{$value->no_po}");
                        $nopo = explode("*#*", str_replace(array("\n", "\r"), "*#*", $np));
                        foreach ($nopo as $k => $vls) {
                            $vls = trim($vls);
                            $uraian[] = $vls;
                        }
                    }

                    $qtylot = str_split(number_format($value->qty_lot, 2) . " {$value->lot}", 18);
                    foreach ($qtylot as $k => $vls) {
                        $vls = trim($vls);
                        $qtylot[$k] = $vls;
                    }
                    $qtyuom = str_split(number_format($value->qty, 2) . " {$value->uom}", 19);
                    foreach ($qtyuom as $k => $vls) {
                        $vls = trim($vls);
                        $qtyuom[$k] = $vls;
                    }

                    $harga = str_split(number_format(($value->harga * $head->kurs_nominal), 2), 15);
                    foreach ($harga as $k => $vls) {
                        $vls = trim($vls);
                        $harga[$k] = $vls;
                    }
                    $jumlah = str_split(number_format(($value->jumlah * $head->kurs_nominal), 2), 20);
                    foreach ($jumlah as $k => $vls) {
                        $vls = trim($vls);
                        $jumlah[$k] = $vls;
                    }
                    $counter = 0;
                    $temp = [];
                    $temp[] = count($no);
                    $temp[] = count($uraian);
                    $temp[] = count($qtylot);
                    $temp[] = count($qtyuom);
                    $temp[] = count($harga);
                    $temp[] = count($jumlah);
                    $counter = max($temp);
                    for ($i = 0; $i < $counter; $i++) {
                        if (($counter - 1) === $i) {
                            $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                        }
                        $line = (isset($no[$i])) ? str_pad($no[$i], 3) : str_pad("", 3);
                        $line .= (isset($uraian[$i])) ? str_pad($uraian[$i], 50, " ", STR_PAD_RIGHT) : str_pad("", 50, " ", STR_PAD_RIGHT);
                        $line .= (isset($qtylot[$i])) ? str_pad("{$qtylot[$i]} ", 19, " ", STR_PAD_LEFT) : str_pad("", 19, " ", STR_PAD_LEFT);
                        $line .= (isset($qtyuom[$i])) ? str_pad("{$qtyuom[$i]} ", 20, " ", STR_PAD_LEFT) : str_pad("", 20, " ", STR_PAD_LEFT);
                        $line .= (isset($harga[$i])) ? str_pad(" Rp. {$harga[$i]}", 20, " ", STR_PAD_LEFT) : str_pad("", 20, " ", STR_PAD_LEFT);
                        $line .= (isset($jumlah[$i])) ? str_pad(" Rp. {$jumlah[$i]}", 25, " ", STR_PAD_LEFT) : str_pad("", 25, " ", STR_PAD_LEFT);
                        $printer->text($line);
                        $this->hitungLinesPrint($printer, $lines, $halaman);
                        if (($counter - 1) === $i) {
                            $printer->setUnderline(Printer::UNDERLINE_NONE);
                        }
                    }
                }
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $totalQtyLot = number_format($totalQtyLot, 2);
                $totalQty = number_format($totalQty, 2);
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->text(str_pad("Total Qty : ", 53, " ", STR_PAD_LEFT));
                $printer->text(str_pad("{$totalQtyLot} {$uomLot} ", 19, " ", STR_PAD_LEFT));
                $printer->text(str_pad("{$totalQty} {$uom} ", 20, " ", STR_PAD_LEFT));
                $printer->text(str_pad("", 45));
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->setUnderline(Printer::UNDERLINE_NONE);
//                $printer->feed();
//
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $subtotal2 = (round($head->grand_total * $head->kurs_nominal) - round($head->diskon * $head->kurs_nominal));
                $dpp = number_format(round($subtotal2 * 11 / 12), 2, ".", ",");
                $diskon = number_format(round($head->diskon), 2, ".", ",");
                $ppn = number_format(round($head->ppn - $head->diskon_ppn), 2, ".", ",");
                $finalTotal = number_format(round($head->final_total * $head->kurs_nominal), 2, ".", ",");
                $terbilang = Kwitansi($head->final_total);
                $spltTbl = str_split(trim($terbilang) . " Rupiah", 73);
                $printer->text(str_pad(" Terbilang : ", 13));
                $printer->text(str_pad($spltTbl[0] ?? " ", 79));
                $printer->text(str_pad("Subtotal", 20, " ", STR_PAD_RIGHT));
                $printer->text(str_pad("Rp. " . number_format(round($head->grand_total * $head->kurs_nominal), 2, ".", ","), 25, " ", STR_PAD_LEFT));
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $spltnm = 1;
                if (strtolower($head->jenis_ppn) !== "kbn") {
                    $printer->text(str_pad(" ", 13));
                    $printer->text(str_pad(($spltTbl[$spltnm] ?? " "), 79));
                    $printer->text(str_pad("Dpp Nilai Lain", 20, " ", STR_PAD_RIGHT));
                    $printer->text(str_pad("Rp. " . $dpp, 25, " ", STR_PAD_LEFT));
//                    $printer->feed();
                    $this->hitungLinesPrint($printer, $lines, $halaman);
                    $spltnm++;
                }

                $printer->text(str_pad(" ", 13));
                $printer->text(str_pad(($spltTbl[$spltnm] ?? " "), 79));
                $printer->text(str_pad("Discount", 20, " ", STR_PAD_RIGHT));
                $spltnm++;
                $printer->text(str_pad("Rp. " . number_format(round($head->diskon * $head->kurs_nominal), 2, ".", ","), 25, " ", STR_PAD_LEFT));
//                $printer->feed();
                $trblng = "";
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->text(str_pad(" ", 13));
                $printer->text(str_pad(($spltTbl[$spltnm] ?? " "), 79));
                $printer->text(str_pad("PPN ", 20, " ", STR_PAD_RIGHT));
//                $printer->text(str_pad(" Rp.", 4));
                $ppns = 0;
                if ($head->tax_id > 0) {
                    $ppns = $head->ppn * $head->kurs_nominal;
                }
                $printer->text(str_pad("Rp. " . number_format(round($ppns), 2, ".", ","), 25, " ", STR_PAD_LEFT));
//                $printer->feed();


                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->text(str_pad(" ", 13));
                $printer->text(str_pad($trblng, 79));
                $printer->text(str_pad("TOTAL", 20, " ", STR_PAD_RIGHT));
//                $printer->text(str_pad(" Rp.", 4));
                $printer->text(str_pad("Rp. " . $finalTotal, 25, " ", STR_PAD_LEFT));
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $printer->setUnderline(Printer::UNDERLINE_SINGLE);
                $printer->text(str_pad(" ", 137));
                $printer->setUnderline(Printer::UNDERLINE_NONE);
//                $printer->feed();
//                $printer->feed();
                $this->hitungLinesPrint($printer, $lines, $halaman);
                $this->hitungLinesPrint($printer, $lines, $halaman);
            }

            $fn = preg_replace('/\s\s+/', '*#*', "{$head->foot_note}");
            $fn = explode("*#*", str_replace(array("\n", "\r"), "*#*", $fn));
            foreach ($fn as $key => $value) {
                $line = str_pad(trim($value), 130);
                $printer->text($line);
                $this->hitungLinesPrint($printer, $lines, $halaman);
            }
//            $printer->text("{$head->foot_note} \n");
//            $printer->feed();
//            $printer->feed();
            $this->hitungLinesPrint($printer, $lines, $halaman);
            $this->hitungLinesPrint($printer, $lines, $halaman);
            $buff->write("\x1bg" . chr(1));
            $printer->text(str_pad(" ", 6));
            $printer->text(str_pad("Penerima :", 20, " ", STR_PAD_BOTH));
            $printer->text(str_pad(" ", 72));
            $printer->text(str_pad("Hormat Kami :", 20, " ", STR_PAD_BOTH));
//            $printer->feed();
//            $printer->feed();
            $this->hitungLinesPrint($printer, $lines, $halaman);
            $this->hitungLinesPrint($printer, $lines, $halaman);
            $printer->text(str_pad(" ", 26));
            $printer->text(str_pad("Pengaduan/Klaim melebihi 7 hari dari tanggal pengiriman barang,", 72, " ", STR_PAD_BOTH));
//            $printer->feed();
            $this->hitungLinesPrint($printer, $lines, $halaman);
            $printer->text(str_pad(" ", 26));
            $printer->text(str_pad("tidak akan kami layani", 82, " ", STR_PAD_BOTH));
//            $printer->feed();
//            $printer->feed();
            $this->hitungLinesPrint($printer, $lines, $halaman);
            $this->hitungLinesPrint($printer, $lines, $halaman);
            $printer->text(str_pad(" ", 6));
            $printer->text(str_pad("(__________________)", 20, " ", STR_PAD_BOTH));
            $printer->text(str_pad(" ", 72));
            $printer->text(str_pad("(__________________)", 20, " ", STR_PAD_BOTH));
//            $printer->feed();
//            $this->hitungLinesPrint($printer, $lines, $halaman);
            $buff->write("\x0c");
//            $datas = $connector->getData();
//            log_message("error", $connector->getData());
            $dataPrint[] = (object) ["img" => "fp.prn", "data" => serialize($connector->getData())];
            $printer->close();
            $client = new GuzzleHttp\Client();
            $resp = $client->request("POST", $this->config->item('url_web_print_w_logo_multi'), [
                "form_params" => [
                    "data" => json_encode($dataPrint),
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
