<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of debitnote
 *
 * @author RONI
 */
class Debitnote extends MY_Controller {

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
        $data['id_dept'] = 'PINVR';
        $this->load->view('purchase/v_invoice_retur', $data);
    }

    public function data() {
        try {
            $data = array();
            $list = new $this->m_global;
            $status = $this->input->post("status");
            $supplier = $this->input->post("supplier");
            $list->setTables("invoice_retur")
                    ->setOrders([null, "no_inv_retur", "partner.nama", "no_invoice_supp", "tanggal_invoice_supp", "no_sj_supp", "no_po", "order_date", "status"])
                    ->setSearch(["partner.nama", "no_invoice_supp", "no_sj_supp", "no_po", "status", "no_inv_retur"])
                    ->setJoins("partner", "partner.id = invoice_retur.id_supplier", "left")
                    ->setSelects(["invoice_retur.*", "partner.nama as supplier"])->setOrder(['created_at' => 'desc']);

            $no = $_POST['start'];
            if ($status !== "")
                $list->setWheres(["status" => $status]);

            if (is_array($supplier))
                $list->setWhereIn("id_supplier", $supplier);

            foreach ($list->getData() as $field) {
                $kode_encrypt = encrypt_url($field->no_inv_retur);
                $no++;
                $data [] = array(
                    $no,
                    '<a href="' . base_url('purchase/debitnote/edit/' . $kode_encrypt) . '">' . $field->no_inv_retur . '</a>',
                    $field->supplier,
                    $field->no_invoice_supp,
                    $field->tanggal_invoice_supp,
                    $field->no_sj_supp,
                    $field->no_po,
                    $field->origin,
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
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['id_dept'] = 'PINVR';
            $data["id"] = $id;
            $head = new $this->m_global;
            $detail = clone $head;
            $tax = clone $head;
            $model3 = clone $head;
            $model4 = clone $head;
            $data["setting"] = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            $datas = $head->setTables("invoice_retur")->setJoins("partner", "partner.id = id_supplier", "left")
                            ->setJoins("currency_kurs", "currency_kurs.id = matauang", "left")
                            ->setJoins("currency", "currency.nama = currency_kurs.currency", "left")
                            ->setWheres(["no_inv_retur" => $kode_decrypt])
                            ->setSelects(["invoice_retur.*", "partner.nama as supplier", "currency as mata_uang", "currency.symbol"])->getDetail();
            if ($datas === null) {
                throw new \Exception();
            }
            $data["taxss"] = $tax->setTables("tax")->setOrder(["id" => "asc"])->getData();
            $data["inv"] = $datas;
            $data["invDetail"] = $detail->setTables("invoice_retur_detail")->setWheres(["invoice_retur_id" => $datas->id])
                            ->setJoins("tax", "tax.id = tax_id", "left")
                            ->setJoins("acc_coa", "acc_coa.kode_coa = account", "left")
                            ->setSelects(["invoice_retur_detail.*", "tax.nama as pajak,tax.ket as pajak_ket,amount,coalesce(tax.tax_lain_id,0) as tax_lain_id,tax.dpp as dpp_tax", "kode_coa,acc_coa.nama as nama_coa"])
                            ->setOrder(["id"])->getData();
            $data["periode"] = $model4->setTables("acc_periode")->setWheres(["status" => "open"], true)->setOrder(["tahun_fiskal" => "desc", "periode" => "asc"])->getData();
            $this->load->view('purchase/v_invoice_retur_edit', $data);
        } catch (Exception $ex) {
            return show_404();
        }
    }

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
                if ($dpplain === "1") {
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
            if ($matauang > 1) {
                $hutangrp = $grandTotal * $matauang;
                $hutangvalas = $grandTotal;
                $totalrp = $grandTotal * $matauang;
                $dpprp = $nilaiDppLain * $matauang;
                $dppvalas = $nilaiDppLain;
                $totalValas = $grandTotal;
            } else {
                $hutangrp = $grandTotal;
                $hutangvalas = 0;
                $totalrp = $grandTotal;
                $dpprp = $nilaiDppLain;
                $dppvalas = 0;
                $totalValas = 0;
            }

            $head = new $this->m_global;
            $bd = clone $head;
            $dataUpdate = [
                "no_sj_supp" => $noSjSupp, "no_invoice_supp" => $noInvSupp, "tanggal_invoice_supp" => $tglInvSupp, 'dpp_lain' => $nilaiDppLain,
                'total' => $grandTotal, 'nilai_matauang' => $matauang, "tanggal_sj" => $tanggal_sj, "periode" => $periode,
                "dpp_lain_valas" => $dppvalas,
                "dpp_lain_rp" => round($nilaiDppLain),
                "total_rp" => round($hutangrp),
                "hutang_valas" => $hutangvalas,
                "hutang_rp" => round($hutangrp),
                "total_valas" => $grandTotal
            ];
            $head->setTables('invoice_retur')->setWheres(["no_inv_retur" => $kode_decrypt])->update($dataUpdate);
            $bd->setTables("invoice_retur_detail")->updateBatch($item, 'id');
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', logArrayToString('; ', $dataUpdate), $username);
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

            $kodeJurnal = $this->input->post("jurnal");
            $status = $this->input->post("status");
            $id = $this->input->post("id");
            $inv = $this->input->post("inv");
            $origin = $this->input->post("origin");
            $periode = $this->input->post("periode");
            $kode_decrypt = decrypt_url($id);
            $updateInv = ["status" => $status];
            $head = new $this->m_global;
            $this->_module->startTransaction();
            $lock = "invoice WRITE,acc_jurnal_entries WRITE,acc_jurnal_entries_items WRITE,token_increment WRITE,partner WRITE,invoice_retur WRITE,setting read,"
                    . "currency_kurs WRITE,currency WRITE,tax WRITE,invoice_retur_detail WRITE,user READ, main_menu_sub READ, log_history WRITE";
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
                $dataItems = $items->setTables("invoice_retur_detail")->setWheres(["no_inv_retur" => $kode_decrypt])
                                ->setJoins("invoice_retur", "invoice_retur.id = invoice_retur_detail.invoice_retur_id")
                                ->setJoins("tax", "tax.id = invoice_retur_detail.tax_id", "left")
                                ->setJoins("partner", "partner.id = invoice_retur.id_supplier", "left")
//                                ->setJoins("mst_produk_coa", "mst_produk_coa.kode_produk = invoice_detail.kode_produk", "left")
                                ->setJoins("currency_kurs", "currency_kurs.id = invoice_retur.matauang", "left")
                                ->setJoins("currency", "currency_kurs.currency = currency.nama", "left")
                                ->setSelects(["invoice_retur_detail.*", "invoice_retur.id_supplier,invoice_retur.journal as jurnal,dpp_lain,nilai_matauang",
                                    "currency_kurs.currency,currency_kurs.kurs,currency.nama as name_curr",
                                    "COALESCE(tax.amount,0) as tax_amount,tax.nama as tax_nama,coalesce(tax.tax_lain_id,0) as tax_lain_id,tax.dpp as dpp_tax",
                                    "partner.nama as nama_supp,tax.ket", "invoice_retur.created_at as invoice_retur_create,invoice_retur.total as total_invoice"])
                                ->setOrder(["invoice_retur_id"])->getData();

                $jurnalData = ["kode" => $jurnal, "periode" => $periode,
                    "origin" => "{$inv}|{$origin}", "status" => "posted",
                    "tanggal_dibuat" => ($dataItems[0]->invoice_retur_create ?? date("Y-m-d H:i:s")), "tipe" => ($dataItems[0]->jurnal ?? ""),
                    "tanggal_posting" => date("Y-m-d H:i:s"), "reff_note" => ($dataItems[0]->nama_supp ?? "")];
                $jurnalDB->setTables("acc_jurnal_entries")->save($jurnalData);
                $pajakLain = [];

                $jurnalItems = [];
                $tax = 0;
                $totalNominal = 0;
                if (count($dataItems) > 0) {
                    $updateInv["hutang_rp"] = $dataItems[0]->total_invoice * $dataItems[0]->nilai_matauang;
                    $updateInv["total_rp"] = $dataItems[0]->total_invoice * $dataItems[0]->nilai_matauang;
                    $updateInv["dpp_lain_rp"] = $dataItems[0]->dpp_lain * $dataItems[0]->nilai_matauang;
                    if ($dataItems[0]->nilai_matauang > 1) {
                        $updateInv["total_valas"] = $dataItems[0]->total_invoice; //
                        $updateInv["hutang_valas"] = $updateInv["total_valas"];
                        $updateInv["dpp_lain_valas"] = $dataItems[0]->dpp_lain;
                    }
                }
                foreach ($dataItems as $key => $value) {
                    if ($value->account === null) {
                        throw new \Exception("Jurnal Account Belum diisi", 500);
                    }
                    $nominal = ($value->harga_satuan * $value->qty_beli) - $value->diskon;
                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "[{$value->kode_produk}] {$value->nama_produk} (" . number_format($value->qty_beli, 2) . " {$value->uom_beli})",
                        "reff_note" => $value->reff_note,
                        "partner" => $value->id_supplier,
                        "kode_coa" => $value->account,
                        "posisi" => "C",
                        "nominal_curr" => ($nominal / $value->kurs),
                        "kurs" => $value->kurs,
                        "kode_mua" => $value->name_curr,
                        "nominal" => ($nominal * $value->nilai_matauang),
                        "row_order" => ($key + 1)
                    );
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
                }

                $model = new $this->m_global;
                $model2 = clone $model;
                $model2->setTables("tax");
                $rowCount = count($dataItems);
                $checkDpp = $dataItems[0]->dpp_lain > 0;
                $model->setTables("setting");
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
                                "kode_coa" => ($coaPpn->value ?? 0),
                                "posisi" => "C",
                                "nominal_curr" => $taxx,
                                "kurs" => $dataItems[0]->kurs,
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
                                        "kode_coa" => ($coaPpn->value ?? 0),
                                        "posisi" => "C",
                                        "nominal_curr" => $taxx,
                                        "kurs" => $dataItems[0]->kurs,
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
                    }
                }
                $defaultPpn = $model->setWheres(["setting_name" => "pajak_hutang_dagang_lokal"], true)->setSelects(["value"])->getDetail();
                $jurnalItems[] = array(
                    "kode" => $jurnal,
                    "nama" => "",
                    "reff_note" => "",
                    "partner" => $dataItems[0]->id_supplier,
                    "kode_coa" => ($defaultPpn->value ?? 0),
                    "posisi" => "D",
                    "nominal_curr" => ($totalNominal + $tax) / $dataItems[0]->kurs,
                    "kurs" => $dataItems[0]->kurs,
                    "kode_mua" => $dataItems[0]->name_curr,
                    "nominal" => ($totalNominal + $tax) * $dataItems[0]->nilai_matauang,
                    "row_order" => count($jurnalItems) + 1
                );
                $jurnalDBItems = new $this->m_global;
                $jurnalDBItems->setTables("acc_jurnal_entries_items")->saveBatch($jurnalItems);

                $log = "Header -> " . logArrayToString("; ", $jurnalData);
                $log .= "\nDETAIL -> " . logArrayToString("; ", $jurnalItems);
                $this->_module->gen_history("debitnote", $jurnal, 'create', $log, $username);
                $updateInv = array_merge($updateInv, ["jurnal_retur" => $jurnal]);
            }
            $head->setTables("invoice_retur")->setWheres(["no_inv_retur" => $kode_decrypt])->update($updateInv);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'update', "status menjadi {$status}", $username);
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
}
