<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Invoice
 *
 * @author RONI
 */
require FCPATH . 'vendor/autoload.php';

use Mpdf\Mpdf;

class Invoice extends MY_Controller {

    //put your code here
    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->config->load('additional');
        $this->load->library("token");
    }

    public function index() {
        $data['id_dept'] = 'PINV';
        $this->load->view('purchase/v_invoice', $data);
    }

    public function data() {
        try {
            $data = array();
            $list = new $this->m_global;
            $status = $this->input->post("status");
            $supplier = $this->input->post("supplier");
            $list->setTables("invoice")
                    ->setOrders([null, "no_invoice", "partner.nama", "no_invoice_supp", "tanggal_invoice_supp", "no_sj_supp", "no_po", "order_date", "status"])
                    ->setSearch(["partner.nama", "no_invoice_supp", "no_sj_supp", "no_po", "status", "no_invoice", "origin"])
                    ->setJoins("partner", "partner.id = invoice.id_supplier", "left")
                    ->setSelects(["invoice.*", "partner.nama as supplier"])->setOrder(['created_at' => 'desc']);

            $no = $_POST['start'];
            if ($status !== "")
                $list->setWheres(["status" => $status]);

            if (is_array($supplier))
                $list->setWhereIn("id_supplier", $supplier);

            foreach ($list->getData() as $field) {
                $kode_encrypt = encrypt_url($field->id);
                $no++;
                $data [] = array(
                    $no,
                    '<a href="' . base_url('purchase/invoice/edit/' . $kode_encrypt) . '">' . $field->no_invoice . '</a>',
                    $field->supplier,
                    $field->no_invoice_supp,
                    $field->tanggal_invoice_supp,
                    $field->no_sj_supp,
                    $field->no_po,
                    $field->order_date,
                    $field->status,
                );
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

    public function edit($id) {
        try {
            $kode_decrypt = decrypt_url($id);
            $data['id_dept'] = 'PINV';
            $data["id"] = $id;
            $head = new $this->m_global;
            $detail = clone $head;
            $tax = clone $head;
            $model3 = clone $head;
            $model4 = clone $head;
            $data["setting"] = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            $datas = $head->setTables("invoice")->setJoins("partner", "partner.id = id_supplier", "left")
                            ->setJoins("currency_kurs", "currency_kurs.id = matauang", "left")
                            ->setJoins("currency", "currency.nama = currency_kurs.currency", "left")
                            ->setWheres(["invoice.id" => $kode_decrypt])
                            ->setSelects(["invoice.*", "partner.nama as supplier", "currency as mata_uang", "currency.symbol"])->getDetail();
            if ($datas === null) {
                throw new \Exception();
            }
            $data["taxss"] = $tax->setTables("tax")->setWheres(["type_inv" => "purchase"])->setOrder(["id" => "asc"])->getData();
            $data["inv"] = $datas;
            $data['mms'] = $this->_module->get_data_mms_for_log_history('PINV');
            $data["invDetail"] = $detail->setTables("invoice_detail")->setWheres(["invoice_id" => $kode_decrypt])
                            ->setJoins("tax", "tax.id = tax_id", "left")
                            ->setJoins("acc_coa", "acc_coa.kode_coa = account", "left")
                            ->setSelects(["invoice_detail.*", "tax.id as tax_id,tax.nama as pajak,tax.ket as pajak_ket,amount,coalesce(tax.tax_lain_id,0) as tax_lain_id,tax.dpp as dpp_tax",
                                "kode_coa,acc_coa.nama as nama_coa", "coalesce(tax_id,'0') as tax_id"])
                            ->setOrder(["id"])->getData();
            $data["periode"] = $model4->setTables("acc_periode")->setWheres(["status" => "open"], true)->setOrder(["tahun_fiskal" => "desc", "periode" => "asc"])->getData();
            $this->load->view('purchase/v_invoice_edit', $data);
        } catch (Exception $ex) {
            return show_404();
        }
    }

//    public function data_detail() {
//        try {
//            $id = $this->input->post("id");
//            $data = array();
//            $list = new $this->m_global;
//            $kode_decrypt = decrypt_url($id);
//            $list->setTables("invoice_detail")->setWheres(["invoice_id" => $kode_decrypt])->setOrder(["id"])
//                    ->setOrders([null, "kode_produk", null, null, "account", "qty_beli", "harga_satuan"]);
//            $no = $_POST['start'];
//            foreach ($list->getData() as $field) {
//                $no++;
//                $data [] = array(
//                    $no,
//                    $field->kode_produk . " - " . $field->nama_produk,
//                    $field->deskripsi,
//                    $field->reff_note,
//                    $field->account,
//                    $field->qty_beli . "  " . $field->uom_beli,
//                    $field->harga_satuan,
//                );
//            }
//            echo json_encode(array("draw" => $_POST['draw'],
//                "recordsTotal" => $list->getDataCountAll(),
//                "recordsFiltered" => $list->getDataCountFiltered(),
//                "data" => $data,
//            ));
//            exit();
//        } catch (Exception $ex) {
//            echo json_encode(array("draw" => $_POST['draw'],
//                "recordsTotal" => 0,
//                "recordsFiltered" => 0,
//                "data" => [],
//            ));
//        }
//    }

    public function update($id) {
        $val = [
            [
                'field' => 'nilai_matauang',
                'label' => 'Kurs',
                'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                    "regex_match" => "{field} harus berupa number / desimal"
                ]
            ],
            [
                'field' => 'periode',
                'label' => 'Periode ACC',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih'
                ]
            ]
                ]
        ;
        try {

            $this->form_validation->set_rules($val);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }

            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode_decrypt = decrypt_url($id);
            $noInvSupp = $this->input->post("no_invoice_supp");
            $tglInvSupp = $this->input->post("tanggal_invoice_supp");
            $noSjSupp = $this->input->post("no_sj_supp");

            $harga = $this->input->post("harga_satuan");
            $coa = $this->input->post("kode_coa");
            $amount_tax = $this->input->post("amount_tax");
            $tax = $this->input->post("tax");
            $dpplain = $this->input->post("dpplain");
            $qty_beli = $this->input->post("qty_beli");
            $dsk = $this->input->post("diskon");
            $matauang = $this->input->post("nilai_matauang");
            $tanggal_sj = $this->input->post("tanggal_sj");
            $tax_lain = $this->input->post("tax_lain_id");
            $periode = $this->input->post("periode");

            $item = [];
            $totals = 0.00;
            $diskons = 0.00;
            $taxes = 0.00;
            $nilaiDppLain = 0;

            $model = new $this->m_global;
            $model->setTables("tax");
            foreach ($harga as $key => $value) {
                $item[] = ["id" => $key, "harga_satuan" => $value, "account" => $coa[$key], "tax_id" => $tax[$key], 'amount_tax' => $amount_tax[$key], "diskon" => $dsk[$key]];
                $total = ($qty_beli[$key] * $value);
                $totals += $total;
                $diskon = ($dsk[$key] ?? 0);
                $diskons += $diskon;
                $taxe = 0;
                if ($dpplain === "1" && $tax_lain[$key] === "1") {
                    $taxe += ((($total - $diskon) * 11) / 12) * $amount_tax[$key];
                } else {
                    $taxe += ($total - $diskon) * $amount_tax[$key];
                }

                if ($tax_lain[$key] !== "0") {
                    $dataTax = $model->setWhereIn("id", explode(",", $tax_lain[$key]), true)->setSelects(["amount,dpp"])->setOrder(["id"])->getData();
                    foreach ($dataTax as $kkk => $datas) {
                        if ($dpplain === "1" && $datas->dpp === "1") {
                            $taxe += ((($total - $diskon) * 11) / 12) * $datas->amount;
                            continue;
                        }
                        $taxe += ($total - $diskon) * $datas->amount;
                    }
                }
                $taxes += $taxe;
            }
            if ($dpplain === "1") {
                $nilaiDppLain = (($totals - $diskons) * 11) / 12;
            }
            $grandTotal = ($totals - $diskons) + $taxes;
            $head = new $this->m_global;
            $bd = clone $head;
            $dataUpdate = ["no_sj_supp" => $noSjSupp, "no_invoice_supp" => $noInvSupp, "tanggal_invoice_supp" => $tglInvSupp, 'dpp_lain' => $nilaiDppLain,
                'total' => $grandTotal, 'nilai_matauang' => $matauang, "tanggal_sj" => $tanggal_sj, "periode" => $periode];
            $head->setTables('invoice')->setWheres(["id" => $kode_decrypt])
                    ->update($dataUpdate);
            $bd->setTables("invoice_detail")->updateBatch($item, 'id');
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', logArrayToString('; ', $dataUpdate), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function update_status() {
        $val = [
            [
                'field' => 'periode',
                'label' => 'Periode ACC',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Belum diupdate'
                ]
            ]
                ]
        ;
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));
            $users = $this->session->userdata('nama');

            $kodeJurnal = $this->input->post("jurnal");
            $status = $this->input->post("status");
            $id = $this->input->post("id");
            $inv = $this->input->post("inv");
            $origin = $this->input->post("origin");
            $periode = $this->input->post("periode");
            
            $kode_decrypt = decrypt_url($id);
            $head = new $this->m_global;
            $this->_module->startTransaction();
            $lock = "invoice WRITE,acc_jurnal_entries WRITE,acc_jurnal_entries_items WRITE,token_increment WRITE,partner READ,"
                    . "currency_kurs READ,currency READ,tax READ,invoice_detail WRITE,user READ, main_menu_sub READ, log_history WRITE,setting READ";
            $this->_module->lock_tabel($lock);
            if ($status === 'cancel') {
                $cekJurnal = clone $head;

                if ($cekJurnal->setTables("acc_jurnal_entries")->setWheres(["origin" => "{$inv}|{$origin}", "status <>" => $status])->getDetail() !== "null") {
                    throw new \Exception('Jurnal Tidak Ada', 500);
                }
            } else if ($status === 'done') {
                $this->form_validation->set_rules($val);
                if ($this->form_validation->run() == FALSE) {
                    throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                }
                $now = date("Y-m-d H:i:s");
                if ($kodeJurnal === "") {
                    throw new \Exception('Jurnal Tidak Ada', 500);
                }

                $jurnalDB = new $this->m_global;
                $items = clone $jurnalDB;
                if (!$jurnal = $this->token->noUrut("jurnal_{$kodeJurnal}", date('y', strtotime($now)) . '/' . date('m', strtotime($now)), true)
                                ->generate("{$kodeJurnal}/", '/%05d')->get()) {
                    throw new \Exception("No jurnal tidak terbuat", 500);
                }
                $kodes = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
                $dataItems = $items->setTables("invoice_detail")->setWheres(["invoice_id" => $kode_decrypt])
                                ->setJoins("invoice", "invoice.id = invoice_detail.invoice_id")
                                ->setJoins("tax", "tax.id = invoice_detail.tax_id", "left")
                                ->setJoins("partner", "partner.id = invoice.id_supplier", "left")
//                                ->setJoins("mst_produk_coa", "mst_produk_coa.kode_produk = invoice_detail.kode_produk", "left")
                                ->setJoins("currency_kurs", "currency_kurs.id = invoice.matauang", "left")
                                ->setJoins("currency", "currency_kurs.currency = currency.nama", "left")
                                ->setSelects(["invoice_detail.*", "invoice.id_supplier,invoice.journal as jurnal,dpp_lain,nilai_matauang", "currency_kurs.currency,currency_kurs.kurs,currency.nama as name_curr",
                                    "COALESCE(tax.amount,0) as tax_amount,tax.nama as tax_nama,tax.ket, coalesce(tax.tax_lain_id,0) as tax_lain_id,tax.dpp as dpp_tax",
                                    "partner.nama as nama_supp,coalesce(tax_id,'0') as tax_id"])
                                ->setOrder(["invoice_id"])->getData();

                $jurnalData = ["kode" => $jurnal, "periode" => $periode,
                    "origin" => "{$inv}|{$origin}", "status" => "posted", "tanggal_dibuat" => date("Y-m-d H:i:s"), "tipe" => ($dataItems[0]->jurnal ?? ""),
                    "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => ($dataItems[0]->nama_supp ?? "")];
                $jurnalDB->setTables("acc_jurnal_entries")->save($jurnalData);

                $jurnalDB->setTables("log_history")->save(
                        [
                            "datelog" => date("Y-m-d H:i:s"),
                            "kode" => $jurnal,
                            "main_menu_sub_kode" => ($kodes["kode"] ?? ""),
                            "jenis_log" => "create",
                            "note" => logArrayToString(";", $jurnalData),
                            "nama_user" => $users["nama"],
                            "ip_address" => ""
                        ]
                );
                $jurnalItems = [];
                $jurnalItemsLog = [];
                $tax = 0;
                $totalNominal = 0;
                $pajakLain = [];
                $checkDpp = $dataItems[0]->dpp_lain > 0;
                foreach ($dataItems as $key => $value) {
                    if ($value->account === null) {
                        throw new \Exception("Jurnal Account Belum diisi", 500);
                    }
                    $nominal = ($value->harga_satuan * $value->qty_beli) - $value->diskon;
                    $item = array(
                        "kode" => $jurnal,
                        "nama" => "[{$value->kode_produk}] {$value->nama_produk} (" . number_format($value->qty_beli, 2) . " {$value->uom_beli})",
                        "reff_note" => $value->reff_note,
                        "partner" => $value->id_supplier,
                        "kode_coa" => $value->account,
                        "posisi" => "D",
                        "nominal_curr" => $nominal,
                        "kurs" => $value->nilai_matauang,
                        "kode_mua" => $value->name_curr,
                        "nominal" => ($nominal * $value->nilai_matauang),
                        "row_order" => ($key + 1)
                    );
                    $jurnalItems[] = $item;
//                    $tax += $nominal * $value->tax_amount;
                    $totalNominal += $nominal;
                    if ($value->tax_id !== "0") {
                        $pajakLain[] = array(
                            "nominal" => $nominal,
                            "tax_lain" => $value->tax_lain_id,
                            "tax" => $value->tax_id,
                            "dpp_tax" => $value->dpp_tax,
                            "ket_tax" => $value->ket,
                            "amount" => $value->tax_amount,
                            "tax_nama" => $value->tax_nama
                        );
                    }
                    $jurnalItemsLog[] = [
                        "datelog" => date("Y-m-d H:i:s"),
                        "kode" => $jurnal,
                        "main_menu_sub_kode" => ($kodes["kode"] ?? ""),
                        "jenis_log" => "edit",
                        "note" => logArrayToString(";", $item),
                        "nama_user" => $users["nama"],
                        "ip_address" => ""
                    ];
                }

                $model = new $this->m_global;
                $model2 = clone $model;
                $model2->setTables("tax");
                $rowCount = count($dataItems);
                $model->setTables("setting");
                $defaultPpn = $model->setTables("setting")->setWheres(["setting_name" => "pajak_default_ppn"], true)->setSelects(["value"])->getDetail();
                if (count($pajakLain) > 0) {
                    $dataPajak = [];
                    foreach ($pajakLain as $kk => $value) {
                        $value = (object) $value;
                        $rowCount++;
                        $taxx = 0;
                        $base = 0;
                        if ($checkDpp && $value->dpp_tax === "1") {
                            $base = (($value->nominal * 11) / 12);
                            $taxx = $base * $value->amount;
                        } else {
                            $base = $value->nominal;
                            $taxx = $base * $value->amount;
                        }
                        $taxNominal = ($taxx * $dataItems[0]->nilai_matauang);
                        $taxName = explode(",", $value->tax_nama);
                        if (isset($dataPajak[$value->ket_tax])) {
                            $dataPajak[$value->ket_tax]["nominal_curr"] += $taxx;
                            $dataPajak[$value->ket_tax]["nominal"] += $taxNominal;
                        } else {
                            $coaPpn = $model->setWheres(["setting_name" => "pajak_" . str_replace(" ", "", $taxName[0])], true)->setSelects(["value"])->getDetail();
                            $dataPajak[$value->ket_tax] = [
                                "kode" => $jurnal,
                                "nama" => $taxName[0],
                                "reff_note" => "",
                                "partner" => $dataItems[0]->id_supplier,
                                "kode_coa" => ($coaPpn->value ?? $defaultPpn->value),
                                "posisi" => "D",
                                "nominal_curr" => $taxx,
                                "kurs" => $dataItems[0]->nilai_matauang,
                                "kode_mua" => $dataItems[0]->name_curr,
                                "nominal" => $taxNominal,
                                "row_order" => $rowCount
                            ];
                            $rowCount++;
                        }
                        $tax += $taxNominal;
                        if ($value->tax_lain !== "0") {
                            $dataTax = $model2->setWhereIn("id", explode(",", $value->tax_lain), true)->setOrder(["id"])->getData();
                            foreach ($dataTax as $kkk => $datass) {
                                $taxx = 0;
                                $base = 0;
                                if ($checkDpp && $datass->dpp === "1") {
                                    $base = (($value->nominal * 11) / 12);
                                    $taxx = $base * $datass->amount;
                                } else {
                                    $base = $value->nominal;
                                    $taxx = $base * $datass->amount;
                                }

                                $taxNominal = ($taxx * $dataItems[0]->nilai_matauang);
                                if (isset($dataPajak[$datass->ket])) {
                                    $dataPajak[$datass->ket]["nominal_curr"] += $taxx;
                                    $dataPajak[$datass->ket]["nominal"] += $taxNominal;
                                } else {
                                    $taxName = explode(",", $datass->nama);
                                    $coaPpn = $model->setWheres(["setting_name" => "pajak_" . str_replace(" ", "", $taxName[0])], true)->setSelects(["value"])->getDetail();
                                    $dataPajak[$datass->ket] = [
                                        "kode" => $jurnal,
                                        "nama" => $taxName[0],
                                        "reff_note" => "",
                                        "partner" => $dataItems[0]->id_supplier,
                                        "kode_coa" => ($coaPpn->value ?? $defaultPpn->value),
                                        "posisi" => "D",
                                        "nominal_curr" => $taxx,
                                        "kurs" => $dataItems[0]->nilai_matauang,
                                        "kode_mua" => $dataItems[0]->name_curr,
                                        "nominal" => $taxNominal,
                                        "row_order" => $rowCount
                                    ];
                                    $rowCount++;
                                }
                            }
                            $tax += $taxNominal;
                        }
                    }

                    foreach ($dataPajak as $a => $vv) {
                        $jurnalItems[] = $vv;
                        $jurnalItemsLog[] = [
                            "datelog" => date("Y-m-d H:i:s"),
                            "kode" => $jurnal,
                            "main_menu_sub_kode" => ($kodes["kode"] ?? ""),
                            "jenis_log" => "create",
                            "note" => logArrayToString(";", $vv),
                            "nama_user" => $users["nama"],
                            "ip_address" => ""
                        ];
                    }
                }
                $defaultPpn = $model->setTables("setting")->setWheres(["setting_name" => "pajak_hutang_dagang_lokal"], true)->setSelects(["value"])->getDetail();
                $item = array(
                    "kode" => $jurnal,
                    "nama" => "",
                    "reff_note" => "",
                    "partner" => $dataItems[0]->id_supplier,
                    "kode_coa" => ($defaultPpn->value ?? 0),
                    "posisi" => "C",
                    "nominal_curr" => $totalNominal + $tax,
                    "kurs" => $dataItems[0]->kurs,
                    "kode_mua" => $dataItems[0]->name_curr,
                    "nominal" => ($totalNominal + $tax) * $dataItems[0]->nilai_matauang,
                    "row_order" => count($jurnalItems) + 1
                );
                $jurnalItems[] = $item;
                $jurnalDBItems = new $this->m_global;
                $jurnalDBItems->setTables("acc_jurnal_entries_items")->saveBatch($jurnalItems);
                $jurnalItemsLog[] = [
                    "datelog" => date("Y-m-d H:i:s"),
                    "kode" => $jurnal,
                    "main_menu_sub_kode" => ($kodes["kode"] ?? ""),
                    "jenis_log" => "edit",
                    "note" => logArrayToString(";", $item),
                    "nama_user" => $users["nama"],
                    "ip_address" => ""
                ];
                $jurnalDB->setTables("log_history")->saveBatch($jurnalItemsLog);
            }
            $head->setTables("invoice")->setWheres(["id" => $kode_decrypt])->update(["status" => $status]);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }

            $this->_module->gen_history($sub_menu, $kode_decrypt, 'update', logArrayToString('; ', ["status" => $status]), $username);
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

    public function print($id) {
        try {
            $kode_decrypt = decrypt_url($id);
            $head = new $this->m_global;
            $detail = clone $head;
            $model3 = clone $head;
            $data["setting"] = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            $data["inv"] = $head->setTables("invoice")->setJoins("partner", "partner.id = id_supplier", "left")
                            ->setJoins("currency_kurs", "currency_kurs.id = matauang", "left")->setWheres(["invoice.id" => $kode_decrypt])
                            ->setSelects(["invoice.*", "partner.nama as supplier,delivery_street,delivery_city", "currency as mata_uang"])->getDetail();

            $data["invDetail"] = $detail->setTables("invoice_detail")->setWheres(["invoice_id" => $kode_decrypt])
                            ->setJoins("tax", "tax.id = tax_id", "left")->setSelects(["invoice_detail.*", "tax.nama as pajak,tax.ket as pajak_ket,amount,coalesce(tax.tax_lain_id,0) as tax_lain_id,tax.dpp as dpp_tax"])
                            ->setOrder(["id"])->getData();

            $url = "dist/storages/print/inv";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            ini_set("pcre.backtrack_limit", "50000000");
            $html = $this->load->view('print/purchase_invoice', $data, true);
            $mpdf = new Mpdf(['tempDir' => FCPATH . '/tmp']);

            $mpdf->WriteHTML($html);
            $pathFile = $url . "/" . str_replace("/", "_", $data["inv"]->no_po) . ".pdf";
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

    public function split($id) {
        try {
            $model = new $this->m_global;
            $ids = $this->input->post("ids");
            $detail = $model->setTables("invoice_detail")->setWheres(["invoice_detail.id" => $ids])
                            ->setJoins("tax", "tax.id = tax_id", "left")
                            ->setSelects(["invoice_detail.*", "tax.nama as tax_nama"])->getDetail();
            if ($detail === null) {
                throw new Exception('Data Tidak ditemukan', 500);
            }
            $html = $this->load->view('purchase/v_invoice_split_item', ["data" => $detail, "id" => $id], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
        } catch (Exception $ex) {
            
        }
    }

    public function save_split($id) {
        $validation = [
            [
                'field' => 'qty',
                'label' => 'Qty',
                'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                    "regex_match" => "{field} harus berupa number / desimal"
                ]
            ]
        ];

        try {

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }


            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode_decrypt = decrypt_url($id);

            $ids = $this->input->post("ids");
            $qty = $this->input->post("qty");

            $this->_module->startTransaction();
            $lock = "invoice_detail WRITE,user READ, main_menu_sub READ, log_history WRITE";
            $this->_module->lock_tabel($lock);
            $model = new $this->m_global;
            $getDetail = $model->setTables("invoice_detail")->setWheres(["id" => $ids, "invoice_id" => $kode_decrypt])->getDetail();
            if ($getDetail === null) {
                throw new Exception('Data Tidak ditemukan', 500);
            }
            $hasilKurang = $getDetail->qty_beli - $qty;
            if ($hasilKurang < 1) {
                throw new Exception('Hasil Split QTY Kurang dari 1', 500);
            }
            $model->update(["qty_beli" => $hasilKurang]);
            unset($getDetail->id);
            $getDetail->qty_beli = $qty;
            $model->save((array) $getDetail);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }

            $this->_module->gen_history($sub_menu, $kode_decrypt, 'update', "Split produk {$getDetail->kode_produk} {$getDetail->nama_produk} QTY : {$qty}", $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }
}
