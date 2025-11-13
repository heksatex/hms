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
            $data["detail"] = $model->setTables("acc_retur_penjualan_detail fjd")
                            ->setJoins("acc_coa", "kode_coa = no_acc", "left")
                            ->setWheres(["retur_id" => $data['datas']->id])
                            ->setSelects(["fjd.*", "acc_coa.nama as coa_nama"])->getData();

            $data["curr"] = $model->setTables("currency_kurs")->setSelects(["id", "currency"])->getData();
            $data["taxs"] = $model->setTables("tax")->setWheres(["type_inv" => "sale"])->setOrder(["nama" => "asc"])->getData();
            $this->load->view('sales/v_faktur_penjualan_edit', $data);
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
            $model->setTables("delivery_order do")->setJoins("delivery_order_detail dod","(do.id = do_id and dod.status = 'retur')")
                    ->setJoins("picklist p", "p.no = do.no_picklist")
                    ->setJoins("mst_sales_group msg", "msg.kode_sales_group = p.sales_kode", "left")
                    ->setJoins("partner pr", "pr.id = p.customer_id", "left")
                    ->setSearch(["do.no_sj", "do.no_picklist", "pr.nama", "msg.nama_sales_group"])
                    ->setOrders([null, "do.no_sj", "do.no_picklist", "pr.nama", "msg.nama_sales_group"])
                    ->setGroups(["do.no_sj"])
                    ->setOrder(["do.tanggal_dokumen" => "desc"])->setWheres(["do.status" => "done", "faktur" => 1])
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
    
    public function list_data() {
        try {
            $data = array();
            $model = new $this->m_global;
            $model->setTables("acc_retur_penjualan")->setJoins("mst_status", "mst_status.kode = acc_retur_penjualan.status", "left")
                    ->setOrder(["acc_retur_penjualan.tanggal" => "desc"])->setSearch(["no_retur", "no_faktur_pajak", "no_sj", "partner_nama","no_retur_internal"])
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
                "dari_sj"=>$dariSJ
            ];
            
            
        } catch (Exception $ex) {
            
        }
    }
}