<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Purchaseorder
 *
 * @author RONI
 */
require FCPATH . 'vendor/autoload.php';

use Mpdf\Mpdf;

class Purchaseorder extends MY_Controller {

    //put your code here.
    protected $table_po, $fields_po;

    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model('m_po');
        $this->load->model("m_user");
        $this->load->model('m_cfb');
        $this->load->model('m_produk');
        $this->load->model('_module');
        $this->load->model('m_konversiuom');
        $this->load->library("token");
        $this->config->load('additional');
        $this->load->model("m_global");
        $this->load->library('database/purchase_order', null, 'db_purchase_order');
        $this->table_po = $this->db_purchase_order::table;
        $this->fields_po = $this->db_purchase_order::fields;
    }

//4833 2650
    protected $status = [
        'purchase_confirmed' => "Draft",
        'cancel' => "Cancel",
        'done' => 'Done'
    ];

    public function index() {
        $data['id_dept'] = 'PO';
        $data['user'] = (object) $this->session->userdata('nama');
        $this->load->view('purchase/v_po', $data);
    }

    public function edit($id) {
        try {
            $username = $this->session->userdata('username');
            $kode_decrypt = decrypt_url($id);
            $data['id'] = $id;
            $data['id_dept'] = 'PO';
            $model1 = new $this->m_po;
            $model2 = clone $model1;
            $model3 = clone $model2;
            $model4 = clone $model3;
            $model5 = clone $model4;
            $data["setting"] = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            $data['user'] = $this->m_user->get_user_by_username($username);
            $data["po"] = $model1->setTables($this->table_po . " po")->setJoins("partner p", "p.id = po.supplier")
                            ->setJoins("currency_kurs", "currency_kurs.id = po." . $this->fields_po["currency"], "left")
                            ->setJoins("currency", "currency.nama = currency_kurs.currency", "left")
                            ->setJoins("purchase_order_edited poe", "(po." . $this->fields_po["noPo"] . " = poe.po_id and poe.status not in ('done','cancel') )", "left")
                            ->setSelects(["po.*", "p.nama as supp", "currency.symbol,currency.nama as curr_name", "poe.status as edited_status,poe.alasan"])
                            ->setWheres(["po." . $this->fields_po["noPo"] => $kode_decrypt])
                            ->setWhereRaw("po." . $this->fields_po["status"] . " in ('done','cancel','purchase_confirmed','exception')")->getDetail();
            if (!$data["po"]) {
                throw new \Exception('Data PO tidak ditemukan', 500);
            }
            $nextPage = $model1->setWheres(["po.id >" => $data["po"]->id, "jenis" => "rfq", "po.supplier" => $data["po"]->supplier], true)
                            ->setWhereIn("po.status", ["done", "purchase_confirmed", "exception"], true)
                            ->setOrder(['po.create_date' => 'asc'])->setSelects(["po.no_po"])->getDetail();
            if ($nextPage) {
                $data["next_page"] = base_url("purchase/purchaseorder/edit/" . encrypt_url($nextPage->no_po));
            }
            $prevPage = $model1->setWheres(["po.id <" => $data["po"]->id, "jenis" => "rfq", "po.supplier" => $data["po"]->supplier], true)
                            ->setWhereIn("po.status", ["done", "purchase_confirmed", "exception"], true)
                            ->setOrder(['po.create_date' => 'desc'])->setSelects(["po.no_po"])->getDetail();
            if ($prevPage) {
                $data["prev_page"] = base_url("purchase/purchaseorder/edit/" . encrypt_url($prevPage->no_po));
            }

            $data["po_items"] = $model2->setTables("purchase_order_detail pod")->setWheres(["po_no_po" => $kode_decrypt])->setOrder(["id" => "asc"])
                            ->setJoins('tax', "tax.id = tax_id", "left")
                            ->setJoins('mst_produk', "mst_produk.kode_produk = pod.kode_produk")
                            ->setJoins('nilai_konversi nk', "pod.id_konversiuom = nk.id", "left")
                            ->setJoins('(select kode_produk as kopro,GROUP_CONCAT(catatan SEPARATOR "#") as catatan from mst_produk_catatan where jenis_catatan = "pembelian" group by kode_produk) as catatan', "catatan.kopro = pod.kode_produk", "left")
                            ->setSelects(["pod.*", "COALESCE(tax.amount,0) as amount_tax,tax.dpp as dpp_tax,coalesce(tax.tax_lain_id,0) as tax_lain_id", "catatan.catatan", "mst_produk.image", "nk.dari,nk.ke,nk.catatan as catatan_nk"])->getData();
            $data["po_retur"] = $model4->setTables("purchase_order_retur por")
                            ->setJoins("purchase_order_detail pod", "pod.id = pod_id")
                            ->setWheres(["pod.po_no_po" => $kode_decrypt])
                            ->setSelects(["por.*", "kode_produk,nama_produk,deskripsi"])->setOrder(["por.retur_date" => "desc"])->getData();
//            $data["uom_beli"] = $this->m_produk->get_list_uom(['beli' => 'yes']);
            $data["tax"] = $model5->setTables("tax")->setWheres(["type_inv" => "purchase"])->setOrder(["id" => "asc"])->getData();
            $data["kurs"] = $this->m_po->setTables("currency_kurs")->setOrder(["id" => "asc"])->getData();
            $data["status"] = $this->status;
            $getSetting = new m_global;
            $defautTotals = $getSetting->setTables("setting")->setWheres(["setting_name" => "limit_approve_{$data["po"]->curr_name}", "status" => 1])->setSelects(["value"])->getDetail();
            $data["default_total"] = (int) ($defautTotals->value ?? 0);
            $this->load->view('purchase/v_po_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function list_data() {
        try {
            $data = array();
            $status = $this->input->post("status");
            $nama_produk = $this->input->post("nama_produk");
            $level = $this->input->post("level");
            $showCancel = $this->input->post("statusCancel");
            $list = $this->m_po->setTables("purchase_order po")->setOrders([null, "no_po", "nama_supplier", "order_date", "create_date", "po.status"])
                    ->setSelects(["po.*", "p.nama as nama_supplier", "nama_status", "ck.currency as curr_kode", "poe.status as poe_status"])->setOrder(['create_date' => 'desc'])
                    ->setSearch(["p.nama", "no_po", "prioritas", "po.status"])
                    ->setJoins("currency_kurs ck", "ck.id = po.currency", "left")
                    ->setJoins("partner p", "(p.id = po.supplier and p.supplier = 1)")
                    ->setJoins("mst_status", "mst_status.kode = po.status", "left")
                    ->setJoins("purchase_order_edited poe", "(poe.po_id = po.no_po and poe.status not in ('cancel','done'))", "left");
//            if (strtolower($level) === 'direksi')
//                $list->setWhereRaw("jenis <>'FPT'");
//            else
            $list->setWhereRaw("jenis <>'FPT'");

            $no = $_POST['start'];

            if (gettype($status) === 'string')
                $list->setWhereRaw("po.status in ('done','cancel','purchase_confirmed','exception')");
            else
            if (count($status) > 0)
                $list->setWhereIn("po.status", $status);

            if ($nama_produk !== "")
                $list->setWhereRaw("po.no_po in (select po_no_po from purchase_order_detail where nama_produk LIKE '%{$nama_produk}%')");


            foreach ($list->getData() as $field) {
                $no++;
                $status = ($field->nama_status ?? $field->status);
                if (strtolower($status) === 'exception') {
                    $status .= " ({$field->poe_status})";
                }
                $data [] = [
                    $no,
                    '<a href="' . base_url('purchase/purchaseorder/edit/' . encrypt_url($field->no_po)) . '">' . $field->no_po . '</a>',
                    $field->nama_supplier,
                    $field->order_date,
                    $field->create_date,
                    number_format($field->total, 2) . " " . ( ($field->total === null) ? "" : $field->curr_kode),
                    $status
                ];
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $list->getDataCountAll(),
                "recordsFiltered" => $list->getDataCountFiltered(),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }

    public function update_status($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');
            $kode_decrypt = decrypt_url($id);
            $status = $this->input->post("status");
            $totalItem = $this->input->post("item");
            $default_total = $this->input->post("default_total");
            $totals = $this->input->post("totals");
            $checkData = new $this->m_po;
            $listLog = [];
            $data = $checkData->setWheres(["no_po" => $kode_decrypt])->setJoins("purchase_order_edited poe", "(poe.po_id = no_po and poe.status not in ('cancel','done'))", "left")
                            ->setSelects(["purchase_order.*", "poe.status as poe_status"])->getDetail();
            if (!$data) {
                throw new \Exception('Data PO tidak ditemukan', 500);
            }
//            if ($data->poe_status === "waiting_approve")
//                throw new \Exception('PO dalam status WAITING APPROVE', 500);

            if ($status !== "cancel") {
                if ($data->currency === null) {
                    throw new \Exception('Mata Uang Belum diperbaharui ', 500);
                }
            }
            $kodes = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
            $this->_module->startTransaction();
            $lockTable = "user WRITE, main_menu_sub READ, log_history WRITE,mst_produk WRITE,cfb_items write,cfb write,procurement_purchase_items write,"
                    . "purchase_order_detail write,purchase_order write,penerimaan_barang WRITE,penerimaan_barang_items WRITE,setting READ";
            if ($status === 'done' || $status === "purchase_confirmed") {
                $lockTable .= ",stock_move_produk WRITE,stock_move WRITE,token_increment WRITE,nilai_konversi nk WRITE,invoice WRITE,invoice_detail write,acc_jurnal_entries WRITE"
                        . ",acc_jurnal_entries_items WRITE,currency write,currency_kurs write,tax write,partner write,purchase_order_edited WRITE";
            }
            $this->_module->lock_tabel($lockTable);
            $updateDataDetail = [];
            switch ($status) {
                case "cancel":
                    $podd = new $this->m_po;
                    $rcv = clone $podd;

                    $inshipment = $rcv->setTables('penerimaan_barang')
                                    ->setJoins("penerimaan_barang_items", "penerimaan_barang.kode = penerimaan_barang_items.kode")
                                    ->setWheres(['status_barang <>' => 'cancel'])
                                    ->setWhereRaw("origin like '{$kode_decrypt}%'")->getDataCountAll();
                    if ((int) $inshipment > 0) {
                        throw new \Exception("Produk Pada RCV In dengan Origin {$kode_decrypt} Tidak dalam status <strong>CANCEL</strong> semua.", 500);
                    }

                    $podd = $podd->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt])
                                    ->setSelects(['group_CONCAT("\'",cfb_items_id,"\'") as items', 'group_CONCAT("\'",kode_cfb,"\'") cfb'])->getDetail();
                    if ($podd && $podd->cfb !== null) {
                        $cfbDetail = new $this->m_cfb;
                        $cfb = clone $cfbDetail;
                        $listCfb = clone $cfbDetail;
                        $cfb->setWhereRaw("kode_cfb in ({$podd->cfb})")->update(["status" => "confirm"]);
                        $cfbDetail->setTables("cfb_items")->setWhereRaw("id in ({$podd->items})")->update(["status" => "confirm"]);
//                        $pod = 
                        foreach ($listCfb->setTables('purchase_order_detail')->setOrder(["id" => "asc"])->setWhereRaw("status not in('cancel','retur')")
                                ->setWheres(["po_no_po" => $kode_decrypt])->getData() as $key => $value) {
                            $updatePP = new $this->m_po;
                            $updateDataDetail[] = ['id' => $value->id, 'status' => $status];
                            $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "confirm"]);
                        }
                    }
                    break;
                case "done":
                    $rcv = new $this->m_po;
                    $listCfb = clone $rcv;
                    $inshipment = $rcv->setTables('penerimaan_barang')
                                    ->setJoins("penerimaan_barang_items", "penerimaan_barang.kode = penerimaan_barang_items.kode")
                                    ->setWheres(['status_barang' => 'done'])
                                    ->setWhereRaw("origin like '{$kode_decrypt}%'")->getDataCountAll();
                    if ((int) $inshipment !== (int) $totalItem) {
                        throw new \Exception("Produk Pada RCV In dengan Origin {$kode_decrypt} Tidak dalam status <strong>DONE</strong> semua.", 500);
                    }
                    break;
                case "purchase_confirmed" :
                    $status_ = 'done';
                    $modelInv = new $this->m_global;
                    if ($totals >= $default_total) {
                        $status_ = "waiting_approve";
                        $status = "exception";
                    } else {
                        $cekInv = $modelInv->setTables("invoice")->setWheres(["no_po" => $kode_decrypt, "status <>" => "cancel"])->getDetail();
                        if ($cekInv) {
                            $modelPO = new $this->m_global;
                            $modelJurnal = clone $modelPO;
                            $dataDetail = $modelPO->setTables("purchase_order_detail")->setJoins("tax", "tax_id = tax.id", "left")
                                            ->setSelects(["purchase_order_detail.*", "tax.amount as tax_amount,coalesce(tax.tax_lain_id,0) as tax_lain_id,tax.dpp as dpp_tax"])
                                            ->setWheres(["po_no_po" => $kode_decrypt])->setOrder(["po_no_po"])->getData();
                            $query = [];
                            foreach ($dataDetail as $key => $value) {
                                $query [] = "update invoice_detail set harga_satuan='{$value->harga_per_uom_beli}',tax_id='{$value->tax_id}',amount_tax='{$value->tax_amount}',diskon='{$value->diskon}' "
                                        . " where invoice_id={$cekInv->id} and kode_produk='{$value->kode_produk}' and reff_note='{$value->reff_note}'";
                                $logInvDetail [] = "kode produk {$value->kode_produk}, harga satuan " . number_format($value->harga_per_uom_beli, 4) . "Nilai Pajak " . ($value->tax_amount * 100) . "% ,"
                                        . "diskon {$value->diskon}";
                            }
                            if (count($query) > 0) {
                                $cekUpdateIn = $modelPO->query($query);
                                if ($cekUpdateIn !== "") {
                                    throw new \Exception("Update pada data invoice gagal.", 500);
                                }
                                $modelInv->update(["total" => $data->total, "dpp_lain" => $data->dpp_lain]);
                                $this->_module->gen_history("invoice", $cekInv->id, 'edit',
                                        "update dpp lain " . number_format($data->dpp_lain, 4) . ", total " . number_format($data->total, 4) . ", " . logArrayToString(";", $logInvDetail),
                                        $username);
                            }
                            $cekJurnal = $modelJurnal->setTables("acc_jurnal_entries")->setWheres(["origin LIKE" => "{$cekInv->no_invoice}|%", "status <>" => 'cancel'])->getDetail();
                            if ($cekJurnal !== null) {
                                $items = new $this->m_global;
                                $dataItems = $items->setTables("invoice_detail")->setWheres(["invoice_id" => $cekInv->id])
                                                ->setJoins("invoice", "invoice.id = invoice_detail.invoice_id")
                                                ->setJoins("tax", "tax.id = invoice_detail.tax_id", "left")
                                                ->setJoins("partner", "partner.id = invoice.id_supplier", "left")
                                                ->setJoins("currency_kurs", "currency_kurs.id = invoice.matauang", "left")
                                                ->setJoins("currency", "currency_kurs.currency = currency.nama", "left")
                                                ->setSelects(["invoice_detail.*", "invoice.id_supplier,invoice.journal as jurnal,dpp_lain,nilai_matauang,coalesce(tax.tax_lain_id,0) as tax_lain_id",
                                                    "tax.dpp as dpp_tax,tax.ket,tax.nama as tax_nama,currency_kurs.currency,currency_kurs.kurs,currency.nama as name_curr",
                                                    "COALESCE(tax.amount,0) as tax_amount,tax.nama as tax_nama", "partner.nama as nama_supp"])
                                                ->setOrder(["invoice_id"])->getData();
                                $updateQueryJurnal = [];
                                $tax = 0;
                                $taxLain = 0;
                                $totalNominal = 0;
                                $pajakLain = [];
                                $modelJurnal = new $this->m_global;
                                $modelJurnal->setTables("acc_jurnal_entries_items");
                                $jurnal_items = [];
                                $rowCount = 0;
                                foreach ($dataItems as $key => $value) {
                                    $rowCount++;
                                    $nominal = ($value->harga_satuan * $value->qty_beli) - $value->diskon;
                                    $ttls = ($nominal * $value->nilai_matauang);
                                    $nm = "[{$value->kode_produk}] {$value->nama_produk} (" . number_format($value->qty_beli, 2) . " {$value->uom_beli})";
//                                    $updateQueryJurnal[] = "update acc_jurnal_entries_items set nominal_curr ='{$nominal}',nominal='{$ttls}' where kode = '{$cekJurnal->kode}' and nama LIKE '{$nm}'";
//                                    $tax += $nominal * $value->tax_amount;
                                    $totalNominal += $nominal;
                                    $logJurnal [] = "nominal Kurs {$nominal} nominal {$ttls} untuk produk {$value->kode_produk} {$value->nama_produk}";
                                    $jurnalItems[] = array(
                                        "kode" => $cekJurnal->kode,
                                        "nama" => $nm,
                                        "reff_note" => $value->reff_note,
                                        "partner" => $value->id_supplier,
                                        "kode_coa" => $value->account,
                                        "posisi" => "D",
                                        "nominal_curr" => $nominal,
                                        "kurs" => $value->kurs,
                                        "kode_mua" => $value->name_curr,
                                        "nominal" => $ttls,
                                        "row_order" => ($key + 1)
                                    );
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
                                $checkDpp = $dataItems[0]->dpp_lain > 0;
                                $modelSetting = new $this->m_global;
                                $model2 = clone $modelSetting;
                                $modelSetting->setTables("setting");
                                if (count($pajakLain) > 0) {
                                    $rowCount++;

                                    if (count($pajakLain) > 0) {
                                        $dataPajakLain = [];
                                        $model2->setTables("tax");
                                        foreach ($pajakLain as $kk => $v) {
                                            $value = (object) $v;
                                            $taxx = 0;
                                            $base = 0;
                                            if ($checkDpp && $value->dpp_tax === "1") {
                                                $base = (($value->nominal * 11) / 12);
                                                $taxx = $base * $value->amount;
                                            } else {
                                                $base = $value->nominal;
                                                $taxx = $base * $value->amount;
                                            }
                                            $taxName = explode(",", $value->tax_nama);
                                            $taxNominal = ($taxx * $dataItems[0]->nilai_matauang);
                                            $defaultPpn = $modelSetting->setWheres(["setting_name" => "pajak_" . str_replace(" ", "", $taxName[0])], true)->setSelects(["value"])->getDetail();

                                            $taxLain += $taxNominal;
                                            if (isset($dataPajakLain[$taxName[0]])) {
                                                $dataPajakLain[$taxName[0]]["nominal_curr"] += $taxx;
                                                $dataPajakLain[$taxName[0]]["nominal"] += $taxNominal;
                                            } else {
                                                $dataPajakLain[$taxName[0]] = [
                                                    "nominal_curr" => $taxx,
                                                    "nominal" => $taxNominal,
                                                    "kode" => $cekJurnal->kode,
                                                    "nama" => $taxName[0],
                                                    "reff_note" => "",
                                                    "partner" => $dataItems[0]->id_supplier,
                                                    "kode_coa" => ($defaultPpn->value ?? 0),
                                                    "posisi" => "D",
                                                    "kurs" => $dataItems[0]->kurs,
                                                    "kode_mua" => $dataItems[0]->name_curr,
                                                    "row_order" => $rowCount
                                                ];
                                            }

                                            if ($value->tax_lain !== "0") {
                                                $dataTax = $model2->setWhereIn("id", explode(",", $value->tax_lain), true)->setOrder(["id"])->getData();
                                                foreach ($dataTax as $kkk => $datass) {
                                                    $rowCount++;
                                                    $taxx = 0;
                                                    $base = 0;
                                                    if ($checkDpp && $datass->dpp === "1") {
                                                        $base = (($value->nominal * 11) / 12);
                                                        $taxx = $base * $datass->amount;
                                                    } else {
                                                        $base = $value->nominal;
                                                        $taxx = $base * $datass->amount;
                                                    }
                                                    $taxName = explode(",", $datass->nama);
                                                    $taxNominal = ($taxx * $dataItems[0]->nilai_matauang);
                                                    $taxLain += $taxNominal;
                                                    if (isset($dataPajakLain[$taxName[0]])) {
                                                        $dataPajakLain[$taxName[0]]["nominal_curr"] += $taxx;
                                                        $dataPajakLain[$taxName[0]]["nominal"] += $taxNominal;
                                                    } else {
                                                        $defaultPpn = $modelSetting->setWheres(["setting_name" => "pajak_" . str_replace(" ", "", $taxName[0])], true)->setSelects(["value"])->getDetail();
                                                        $dataPajakLain[$taxName[0]] = [
                                                            "nominal_curr" => $taxx,
                                                            "nominal" => $taxNominal,
                                                            "kode" => $cekJurnal->kode,
                                                            "nama" => $taxName[0],
                                                            "reff_note" => "",
                                                            "partner" => $dataItems[0]->id_supplier,
                                                            "kode_coa" => ($defaultPpn->value ?? 0),
                                                            "posisi" => "D",
                                                            "kurs" => $dataItems[0]->kurs,
                                                            "kode_mua" => $dataItems[0]->name_curr,
                                                            "row_order" => $rowCount
                                                        ];
                                                    }
                                                }
                                            }
                                        }
                                        foreach ($dataPajakLain as $kk => $valueee) {
//                                            $updateQueryJurnal[] = "update acc_jurnal_entries_items set nominal_curr ='{$valueee['nominal_curr']}', nominal='{$valueee['nominal']}' where kode='{$cekJurnal->kode}' and nama='{$kk}'";
                                            $jurnalItems[] = $valueee;
                                            $logJurnal [] = "{$kk} update nominal Kurs {$valueee['nominal_curr']} nominal {$valueee['nominal']}";
                                        }
                                    }
                                }

                                if (count($jurnalItems) > 0) {
                                    $defaultPpn = $modelSetting->setWheres(["setting_name" => "pajak_hutang_dagang_lokal"], true)->setSelects(["value"])->getDetail();
                                    $ttmax = $totalNominal + $tax + $taxLain;
                                    $nttmax = $ttmax * $dataItems[0]->nilai_matauang;
//                                    $updateQueryJurnal[] = "update acc_jurnal_entries_items set nominal_curr ='{$ttmax}',nominal='{$nttmax}' where kode = '{$cekJurnal->kode}' and kode_coa = '{$defaultPpn->value}'";
                                    $jurnalItems[] = [
                                        "nominal_curr" => $ttmax,
                                        "nominal" => $nttmax,
                                        "kode" => $cekJurnal->kode,
                                        "nama" => "",
                                        "reff_note" => "",
                                        "partner" => $dataItems[0]->id_supplier,
                                        "kode_coa" => ($defaultPpn->value ?? 0),
                                        "posisi" => "C",
                                        "kurs" => $dataItems[0]->kurs,
                                        "kode_mua" => $dataItems[0]->name_curr,
                                        "row_order" => $rowCount + 1
                                    ];
                                    $modelJurnal->setTables("acc_jurnal_entries_items")->setWheres(["kode" => $cekJurnal->kode], true)->delete();
                                    $cekUpdateIn = $modelJurnal->saveBatch($jurnalItems);
                                    $logJurnal [] = "nominal Kurs {$ttmax} nominal {$nttmax} Untuk Hutang Dagang";
//                                    $cekUpdateIn = $modelPO->query($updateQueryJurnal);
//                                    if ($cekUpdateIn !== "") {
//                                        throw new \Exception(json_encode($cekUpdateIn), 500);
//                                    }
                                    $listLog[] = logArrayToString("; ", ["datelog" => date("Y-m-d H:i:s"), "kode" => $cekJurnal->kode, "main_menu_sub_kode" => ($kodes["kode"] ?? ""),
                                        "jenis_log" => "edit", "note" => logArrayToString(";", $logInvDetail), "nama_user" => $users["nama"], "ip_address" => ""]);
                                }
                            }
                        }
                    }

                    $model = new $this->m_global;
                    $model->setTables("purchase_order_edited")->setWheres(["po_id" => $kode_decrypt])
                            ->setWhereRaw("status not in('cancel','retur','done')")->update(["status" => $status_]);
                    $listLog[] = logArrayToString("; ", ["datelog" => date("Y-m-d H:i:s"), "kode" => $kode_decrypt,
                        "jenis_log" => "edit", "note" => "Permintaan Untuk Edit PO status -> {$status_}", "nama_user" => $users["nama"], "ip_address" => ""]);
                    break;
            }
            $po = new $this->m_po;
            $pod = clone $po;
            $po->setWheres(["no_po" => $kode_decrypt])->setWhereRaw("status not in ('cancel','retur')")->update(["status" => $status]);
            if (count($updateDataDetail) > 0) {
                $pod->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt])->setWhereRaw("status not in ('cancel','retur')")->updateBatch($updateDataDetail, "id");
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            if (count($listLog) > 0) {
                $this->m_global->setTables("log_history")->saveBatch($listLog);
            }
            $this->_module->gen_history($sub_menu, $data->no_po, 'edit', "update status ke " . $status, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(($ex->getCode() ?? 500))
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function update($id) {
        $validation = [
            [
                'field' => 'harga[]',
                'label' => 'Harga',
                'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                    "regex_match" => "{field} harus berupa number / desimal"
                ]
            ]
        ];
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $kode_decrypt = decrypt_url($id);
            $harga = $this->input->post("harga");
            $qty_beli = $this->input->post("qty_beli");
            $amount_tax = $this->input->post("amount_tax");
            $tax = $this->input->post("tax");
            $dsk = $this->input->post("diskon");
            $dpplain = $this->input->post("dpplain");
            $dppTax = $this->input->post("dpp_tax");
            $foot_note = $this->input->post("foot_note");
            $default_total = $this->input->post("default_total");
            $taxLain = $this->input->post("tax_lain_id");
            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $data = [];
            $log_update = [];
            $no = 0;

            $totals = 0.00;
            $diskons = 0.00;
            $taxes = 0.00;
            $nilaiDppLain = 0;
            $log_update["Foot Note "] = $foot_note;
            $models = clone $this->m_global;
            $models->setTables("tax");
            foreach ($harga as $key => $value) {
                $no++;
                $log_update ["item ke " . $no] = logArrayToString("; ", ['harga' => $value, 'diskon' => $dsk[$key], "pajak" => ($amount_tax[$key] * 100) . "%"]);
                $data[] = ['id' => $key, 'harga_per_uom_beli' => $value, 'tax_id' => $tax[$key], 'diskon' => $dsk[$key]];

                $total = ($qty_beli[$key] * $value);
                $totals += $total;
                $diskon = ($dsk[$key] ?? 0);
                $diskons += $diskon;
                $taxe = 0;
                if ($dpplain === "1" && $dppTax[$key] === "1") {
                    $taxe += ((($total - $diskon) * 11) / 12) * $amount_tax[$key];
                    $nilaiDppLain += (($total - $diskon) * 11) / 12;
                } else {
                    $taxe += ($total - $diskon) * $amount_tax[$key];
                }

                if ($taxLain[$key] !== "0") {
                    $dataTax = $models->setWhereIn("id", explode(",", $taxLain[$key]), true)->setSelects(["amount,dpp"])->setOrder(["id"])->getData();
                    foreach ($dataTax as $kkk => $datas) {
                        if ($dpplain === "1" && $datas->dpp === "1") {
                            $taxe += ((($total - $diskon) * 11) / 12) * $datas->amount;
                            $nilaiDppLain += (($total - $diskon) * 11) / 12;
//                            continue;
                        } else {
                            $taxe += ($total - $diskon) * $datas->amount;
                        }
                    }
                }
                $taxes += $taxe;
            }
//            if ($dpplain === "1") {
//                $nilaiDppLain = (($totals - $diskons) * 11) / 12;
//            }
            $grandTotal = ($totals - $diskons) + $taxes;
            $this->_module->startTransaction();
            $this->_module->lock_tabel("user WRITE, main_menu_sub READ, log_history WRITE,mst_produk WRITE,"
                    . "purchase_order_detail write,purchase_order write,purchase_order_edited WRITE");
            $this->m_po->setTables("purchase_order_detail")->updateBatch($data, 'id');
            $po = new $this->m_po;
            $po->setWheres(["no_po" => $kode_decrypt])->update(["total" => $grandTotal, 'dpp_lain' => $nilaiDppLain, "foot_note" => $foot_note]);
//            if ($grandTotal >= $default_total) {
//                $poe = new $this->m_po;
//                $poe->setTables("purchase_order_edited")->setWhereRaw("po_id = '{$kode_decrypt}' and status not in ('cancel','done')")->update(['status' => "waiting_approve"]);
//            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', logArrayToString('; ', $log_update, " : "), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function request_edit() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $ids = $this->input->post("ids");
            $status = $this->input->post("status");
            $alasan = $this->input->post("alasan");
            $kode_decrypt = decrypt_url($ids);
            if (!$kode_decrypt) {
                throw new \Exception('Tidak dapat Dilakukan', 500);
            }
            $this->_module->startTransaction();
            $model = new $this->m_global;
            $model2 = clone $model;
            $whereStatus = "'done','cancel'";
            $cek = $model->setTables("purchase_order_edited")
                    ->setJoins("mst_status", "mst_status.kode = status", "LEFT")
                    ->setSelects(["purchase_order_edited.*", "nama_status"])
                    ->setWheres(["po_id" => $kode_decrypt])->setWhereRaw("status not in ({$whereStatus})")
                    ->getDetail();
            if ($cek !== null) {
                $update = false;
                switch ($status) {
                    case "cancel":
                        $update = true;
                        $model2->setTables("purchase_order")->setWheres(["no_po" => $kode_decrypt, "status" => "exception"])->update(["status" => "purchase_confirmed"]);
                        break;
                    case "approve":
                        $update = true;
                        break;
                }
                if ($update) {
                    $model->update(["status" => $status]);
                    $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', "Permintaan Untuk {$status} Edit Harga", $username);
                    if (!$this->_module->finishTransaction()) {
                        throw new \Exception('Gagal update status', 500);
                    }
                    throw new \Exception("Berhasil", 200);
                }
                throw new \Exception("PO Sedang Dalam Status " . (($cek->nama_status === 'null') ? $cek->nama_status : $cek->status), 500);
            }
            $model->save(["po_id" => $kode_decrypt, "status" => "{$status}", "created_at" => date("Y-m-d H:i:s"), "alasan" => $alasan]);
            $model2->setTables("purchase_order")->setWheres(["no_po" => $kode_decrypt])->update(["status" => "exception"]);
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', "Permintaan Untuk {$status} Edit Harga ({$alasan})", $username);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(($ex->getCode() ?? 500))
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function get_view_retur() {
        try {
            $items = $this->input->post("items");
            $id = $this->input->post("ids");
            $kode_decrypt = decrypt_url($id);
            $model = new $this->m_global;
            $getItems = $model->setTables("purchase_order_detail")
                            ->setWhereIn("id", $items)->setWheres(["po_no_po" => $kode_decrypt])->setOrder(["created_at" => "asc"])->getData();

            $view = $this->load->view("purchase/v_form_retur", ["data" => $getItems, "id" => $id], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-warning', 'type' => 'danger', 'data' => $view)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', 'data' => "")));
        }
    }

    public function retur() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $validation = [
                [
                    'field' => 'qty_beli[]',
                    'label' => 'Qty',
                    'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
                    'errors' => [
                        'required' => '{field} Harus dipilih',
                        "regex_match" => "{field} harus berupa number / desimal"
                    ]
                ]
            ];
            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }

            $items = $this->input->post("item");
            $qtys = $this->input->post("qty_beli");
            $id = $this->input->post("ids");
            $kode_decrypt = decrypt_url($id);

            $model = new $this->m_global;
            $cekPenerimaan = clone $model;
            if ($cekPenerimaan->setTables("penerimaan_barang")->setWheres(["status" => "done", "origin" => $kode_decrypt])->getDetail() === null) {
                throw new \Exception("Penerimaan Barang <strong>{$kode_decrypt}</strong> harus dalam status Terkirim", 500);
            }
            $this->_module->startTransaction();
            $locktabel = "purchase_order_detail Write, purchase_order WRITE, purchase_order_edited WRITE,"
                    . "user WRITE, main_menu_sub READ, log_history WRITE,purchase_order_retur WRITE,tax WRITE,"
                    . "acc_jurnal_entries write,token_increment WRITE,nilai_konversi WRITE";
            $this->_module->lock_tabel($locktabel);
            $logProduk = [];
            $dataRetur = [];

            foreach ($items as $key => $value) {
                $model1 = clone $model;
                $checkData = $model1->setTables("purchase_order_detail")
                        ->setJoins("purchase_order_retur", "(purchase_order_detail.id = pod_id and purchase_order_retur.status <> 'cancel')", "left")
                        ->setJoins("nilai_konversi", "nilai_konversi.id = id_konversiuom", "left")
                        ->setWheres(["purchase_order_detail.po_no_po" => $kode_decrypt, "purchase_order_detail.id" => $value], true)
                        ->setGroups(["purchase_order_detail.id"])
                        ->setSelects(["COALESCE(SUM(qty_beli_retur),0) as total_retur", "purchase_order_detail.*", "nilai_konversi.nilai as konversi",
                            "konversi_aktif", "pembilang", "penyebut"])
                        ->getDetail();
                if ($checkData === null) {
                    throw new \Exception("Produk Item Produk tidak ditemukan", 500);
                }
                if ($checkData->qty_beli < ($qtys[$key] + $checkData->total_retur)) {
                    throw new \Exception("Retur Produk [{$checkData->kode_produk}] {$checkData->nama_produk} Melebihi Qty Beli", 500);
                }
                if ($checkData->konversi_aktif === "1") {
                    $qtyRetur = ($checkData->pembilang * $checkData->penyebut) / $qtys[$key];
                } else {
                    $qtyRetur = $qtys[$key] * $checkData->konversi;
                }
                $dataRetur[] = [
                    "pod_id" => $value,
                    "po_no_po" => $kode_decrypt,
                    "qty_beli_retur" => $qtys[$key],
                    "uom_beli_retur" => $checkData->uom_beli,
                    "qty_retur" => $qtyRetur,
                    "uom_retur" => $checkData->uom,
                    "konversi_beli_stok" => $checkData->konversi,
                    "retur_date" => date("Y-m-d H:i:s")
                ];
                $checkData->qty_beli = $qtys[$key];
                $logProduk[] = $checkData->kode_produk . " " . $checkData->nama_produk . " Sebanyak {$qtys[$key]} $checkData->uom_beli";
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            $model->setTables("purchase_order_retur")->saveBatch($dataRetur);

            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', "Retur Untuk Produk " . logArrayToString("; ", $logProduk, ":"), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(($ex->getCode() ?? 500))
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function update_retur_status() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $status = $this->input->post("status");
            $po = $this->input->post("po");
            $items = $this->input->post("items");
            $model = new $this->m_global;

            $this->_module->startTransaction();
            $locktabel = "purchase_order_detail pod Write, purchase_order WRITE,"
                    . "user WRITE, main_menu_sub READ, log_history WRITE,purchase_order_retur por WRITE";
            $this->_module->lock_tabel($locktabel);

            $checkData = $model->setTables("purchase_order_retur por")
                    ->setJoins("purchase_order_detail pod", "pod.id = pod_id", "left")
                    ->setWhereIn("por.id", $items)->setWheres(["por.status <>" => 'cancel'])
                    ->setSelects(["kode_produk", "nama_produk", "por.id", "por.status"])
                    ->getData();
            if (count($checkData) < 1) {
                throw new \Exception("Produk Item tidak ditemukan", 500);
            }
            $logs = [];
            foreach ($checkData as $key => $value) {
                if ($value->status !== 'draft')
                    throw new \Exception("Produk {$value->kode_produk} - {$value->nama_produk} tidak dalam status draft", 500);

                $logs [] = "Retur Produk {$value->kode_produk} - {$value->nama_produk} dibatalkan";
            }

            $model->setTables("purchase_order_retur por")
                    ->setWhereIn("por.id", $items)->setWheres(["por.status" => 'draft'])->update(["status" => "cancel"]);
            $this->_module->gen_history($sub_menu, $po, 'edit', logArrayToString("; ", $logs, ":"), $username);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(($ex->getCode() ?? 500))
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function confirm_retur() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $nopo = $this->input->post("ids");
            $model = new $this->m_global;

            $checkData = $model->setTables("purchase_order_retur por")
                    ->setJoins("purchase_order_detail pod", "pod.id = pod_id", "left")
                    ->setJoins("mst_produk", "mst_produk.kode_produk = pod.kode_produk")
                    ->setJoins("mst_category", "mst_category.id = mst_produk.id_category")
                    ->setJoins("tax", "tax.id = pod.tax_id", "left")
                    ->setWheres(["por.po_no_po" => $nopo, "por.status" => "draft"])
                    ->setOrder(["pod_id" => "asc"])
                    ->setSelects(["pod.*", "por.qty_retur,por.uom_retur,por.qty_beli_retur,por.uom_beli_retur", "COALESCE(tax.amount,0) as tax_amount,tax.nama as tax_nama", "mst_category.dept_id",
                        "tax.dpp as dpp_tax,coalesce(tax.tax_lain_id,0) as tax_lain_id"])
                    ->getData();
            if (count($checkData) < 1) {
                throw new \Exception("Produk Item Produk tidak ditemukan Atau tidak dalam status Draft", 500);
            }

            $modelInvoice = new $this->m_global;
            $models = clone $modelInvoice;
            $checkInv = $modelInvoice->setTables("invoice")->setJoins("invoice_detail", "invoice.id = invoice_id", "left")
                            ->setJoins("tax", "invoice_detail.tax_id = tax.id", "left")
                            ->setWheres(["no_po" => $nopo, "status <>" => "cancel"])
                            ->setSelects(["invoice.*", "COALESCE(tax.amount,0) as tax_amount,tax.nama as tax_nama,tax.dpp as dpp_tax,coalesce(tax.tax_lain_id,0) as tax_lain_id"])->getDetail();
            if ($checkInv !== null) {
                $now = date("Y-m-d H:i:s");
                if (!$noDeb = $this->token->noUrut("invoice_pembelian_retur", date('y', strtotime($now)) . '/' . date('m', strtotime($now)), true)
                                ->generate("PBINVR/", '/%05d')->get()) {
                    throw new \Exception("No Debit Note tidak terbuat", 500);
                }

                $this->_module->startTransaction();
                $locktabel = "purchase_order_detail pod Write, purchase_order WRITE,"
                        . "user WRITE, main_menu_sub READ, log_history WRITE,purchase_order_retur por WRITE,tax WRITE,invoice write,"
                        . "invoice_detail write,token_increment WRITE,invoice_retur WRITE,invoice_retur_detail WRITE,"
                        . "stock_move WRITE, stock_move_produk WRITE, departemen d WRITE,"
                        . "pengiriman_barang WRITE, pengiriman_barang_items WRITE, departemen WRITE, mst_produk WRITE";
                $this->_module->lock_tabel($locktabel);
                $invRetur = [
                    "no_inv_retur" => $noDeb,
                    "id_supplier" => $checkInv->id_supplier,
                    "no_invoice_supp" => $checkInv->no_invoice_supp,
                    "tanggal_invoice_supp" => $checkInv->tanggal_invoice_supp,
                    "no_sj_supp" => $checkInv->no_sj_supp,
                    "no_po" => $checkInv->no_po,
                    "order_date" => $checkInv->order_date,
                    "journal" => "RPB",
                    "matauang" => $checkInv->matauang,
                    "nilai_matauang" => $checkInv->nilai_matauang,
                    "origin" => $checkInv->origin,
                    "total" => 0,
                    "dpp_lain" => 0,
                    "created_at" => date("Y-m-d H:i:s"),
                    "tanggal_sj" => $checkInv->tanggal_sj,
                    "status" => "draft"
                ];

                $move_id = "";
                $pengirimanHead = [];
                $pengirimanItem = [];
                $pengirimanHeadCek = [];
                $stmvProduk = [];
                $totals = 0.00;
                $diskons = 0.00;
                $taxes = 0.00;
                $dpp = 0;
                $idInsert = $modelInvoice->setTables("invoice_retur")->save($invRetur);
                $model2 = new $this->m_global;
                $logProduk = [];
                $stmv = [];
                $countItems = 0;
                $last_move = $this->_module->get_kode_stock_move();
                $models->setTables("tax");
                foreach ($checkData as $key => $value) {
                    $taxe = 0;
                    $countItems++;
                    $dataInv = $model2->setTables("invoice_detail")->setWheres(["invoice_id" => $checkInv->id, "kode_produk" => $value->kode_produk], true)->getDetail();
                    $invoiceReturDetail[] = [
                        'invoice_retur_id' => $idInsert,
                        'nama_produk' => $value->nama_produk,
                        'kode_produk' => $value->kode_produk,
                        'qty_beli' => $value->qty_beli_retur,
                        'uom_beli' => $value->uom_beli_retur,
                        'deskripsi' => $value->deskripsi,
                        'reff_note' => $value->reff_note,
                        'account' => $dataInv->account,
                        'harga_satuan' => $value->harga_per_uom_beli,
                        'tax_id' => $value->tax_id,
                        'diskon' => $value->diskon,
                        "amount_tax" => $value->tax_amount
                    ];
                    $total = ($value->qty_retur * $value->harga_per_uom_beli);
                    $totals += $total;
                    $diskon = ($value->diskon ?? 0);
                    $diskons += $diskon;
                    if ($checkInv->dpp_lain > 0 && $value->dpp_tax === "1") {
                        $taxe = ((($total - $diskon) * 11) / 12) * $value->tax_amount;
                        $dpp += ((($total - $diskon) * 11) / 12);
                    } else {
                        $taxe = ($total - $diskon) * $value->tax_amount;
                    }
                    if ($value->tax_lain_id !== "0") {
                        $dataTax = $models->setWhereIn("id", explode(",", $value->tax_lain_id), true)->setSelects(["amount,dpp"])->setOrder(["id"])->getData();
                        foreach ($dataTax as $kkk => $datas) {
                            if ($checkInv->dpp_lain > 0 && $datas->dpp === "1") {
                                $taxe += ((($total - $diskon) * 11) / 12) * $datas->amount;
                                $dpp += ((($total - $diskon) * 11) / 12);
                            } else {
                                $taxe += ($total - $diskon) * $datas->amount;
                            }
                        }
                    }
                    $logProduk[] = $value->kode_produk . " " . $value->nama_produk;
                    $taxes += $taxe;
                    $head = $value->dept_id;
                    if (!isset($pengirimanHeadCek[$head])) {
                        $countItems = 1;
                        $move_id = "SM" . $last_move; //Set kode stock_move
                        $stmv [] = [
                            "move_id" => $move_id,
                            "create_date" => $now,
                            "origin" => $nopo . "|" . $noDeb,
                            "method" => "{$head}|OUT",
                            "lokasi_dari" => "{$head}/Stock",
                            "lokasi_tujuan" => "SUP/Stock",
                            "status" => "draft",
                            "row_order" => count($pengirimanHeadCek) + 1
                        ];
                        $getCounter = $this->_module->get_kode_pengiriman($head);
                        $dgt = substr("00000" . $getCounter, -5);
                        $kode_out = $head . "/OUT/" . date("y") . date("m") . $dgt;
                        $pengirimanHeadCek[$head] = ["kode" => $kode_out];
                        $pengirimanHead[] = [
                            "kode" => $kode_out,
                            "tanggal" => $now,
                            "tanggal_transaksi" => $now,
                            "tanggal_jt" => $now,
                            "origin" => $nopo . "|" . $noDeb,
                            "move_id" => $move_id,
                            "lokasi_dari" => "{$head}/Stock",
                            "lokasi_tujuan" => "SUP/Stock",
                            "status" => "draft",
                            "dept_id" => $head,
                            "reff_picking" => "{$kode_out}|SUP"
                        ];
                        $last_move++;
                    }
                    $pengirimanItem[] = [
                        "kode" => $pengirimanHeadCek[$head]["kode"],
                        'nama_produk' => $value->nama_produk,
                        'kode_produk' => $value->kode_produk,
                        'qty' => $value->qty_retur,
                        'uom' => $value->uom_retur,
                        'status_barang' => "draft",
                        "row_order" => $countItems,
                        "origin_prod" => $value->kode_produk . "_" . $countItems
                    ];
                    $countSmP = count($stmvProduk);
                    $stmvProduk[] = [
                        "move_id" => $move_id,
                        "kode_produk" => $value->kode_produk,
                        "nama_produk" => $value->nama_produk,
                        "qty" => $value->qty_retur,
                        "uom" => $value->uom_retur,
                        "origin_prod" => $value->kode_produk . "_" . ($countSmP + 1),
                        "row_order" => $countSmP + 1,
                        "status" => "draft",
                    ];
                }

                if (count($pengirimanHead) > 0) {
                    $models = new $this->m_global;
                    $models->setTables("pengiriman_barang")->saveBatch($pengirimanHead);
                    $models->setTables("pengiriman_barang_items")->saveBatch($pengirimanItem);
                    $models->setTables("stock_move")->saveBatch($stmv);
                    $models->setTables("stock_move_produk")->saveBatch($stmvProduk);
                }

                $grandTotal = ($totals - $diskons) + $taxes;
                $modelInvoice->setTables("invoice_retur_detail")->saveBatch($invoiceReturDetail);
                $modelInvoice->setTables("invoice_retur")->setWheres(["id" => $idInsert], true)->update(["total" => $grandTotal, "dpp_lain" => $dpp]);
                $model2->setTables("purchase_order_retur por")->setWheres(["po_no_po" => $nopo, "status" => "draft"], true)->update(["status" => "confirm"]);
                $this->_module->gen_history('debitnote', $noDeb, 'create', logArrayToString(";", $invRetur), $username);
                $this->_module->gen_history($sub_menu, $nopo, 'edit', "Konfirmasi Retur Untuk Produk " . logArrayToString("; ", $logProduk, ":"), $username);
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->_module->rollbackTransaction();
            $this->output->set_status_header(($ex->getCode() ?? 500))
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function print() {
        try {
            $id = $this->input->post("id");
            $form = $this->input->post("form");
            $kode_decrypt = decrypt_url($id);
            $model1 = new $this->m_po;
            $model2 = clone $model1;
            $model3 = clone $model1;
            $data["setting"] = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            $data["po"] = $model1->setTables("purchase_order po")->setJoins("partner p", "p.id = po.supplier")
                            ->setJoins("currency_kurs", "currency_kurs.id = po.currency", "left")
                            ->setSelects(["po.*", "p.nama as supp,concat(delivery_street,' ',delivery_city) as alamat_kirim", "currency_kurs.currency as matauang"])
                            ->setWheres(["po.no_po" => $kode_decrypt])->setWhereRaw("po.status in ('done','cancel','purchase_confirmed')")->getDetail();
            $data["po_items"] = $model2->setTables("purchase_order_detail pod")->setWheres(["po_no_po" => $kode_decrypt])->setWhereRaw("status not in ('cancel','retur')")->setOrder(["id" => "asc"])
                            ->setJoins('tax', "tax.id = tax_id", "left")
                            ->setJoins('mst_produk', "mst_produk.kode_produk = pod.kode_produk")
                            ->setJoins('nilai_konversi nk', "pod.id_konversiuom = nk.id", "left")
                            ->setJoins('(select kode_produk as kopro,GROUP_CONCAT(catatan SEPARATOR "#") as catatan from mst_produk_catatan where jenis_catatan = "pembelian" group by kode_produk) as catatan', "catatan.kopro = pod.kode_produk", "left")
                            ->setSelects(["pod.*", "tax.nama as tax_name,COALESCE(tax.amount,0) as amount_tax,tax.dpp as dpp_tax,coalesce(tax.tax_lain_id,0) as tax_lain_id", "catatan.catatan", "mst_produk.image", "nk.dari,nk.ke,nk.catatan as catatan_nk"])->getData();

            $url = "dist/storages/print/po";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            ini_set("pcre.backtrack_limit", "50000000");
            $html = $this->load->view("print/{$form}", $data, true);
            $mpdf = new Mpdf(['tempDir' => FCPATH . '/tmp']);

            $footer = "<table name='footer' width=\"1000\">
           <tr>
             <td style='border-top: 1px solid black; width: 100%;font-size: 15px; padding-bottom: 20px;' align=\"center\">Email : mail@heksatex.co.id   Website: http://www.heksatex.co.id</td>
           </tr>
         </table>";

            $mpdf->WriteHTML($html);
            if ($form !== 'fpt')
                $mpdf->SetHTMLFooter($footer);
            $pathFile = $url . "/" . str_replace("/", "_", $data["po"]->no_po) . ".pdf";
            $mpdf->Output(FCPATH . $pathFile, "F");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array("url" => base_url($pathFile))));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            ini_set("pcre.backtrack_limit", "1000000");
        }
    }

    public function get_rcv($id) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception('', 500);
            }
            $rcvDone = new $this->m_po;
            $inInv = 0;
            $inInv = $rcvDone->setTables('invoice')
                            ->setWheres(['status <>' => 'cancel', "no_po" => $kode_decrypt])->getDataCountAll();

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'in_inv' => $inInv)));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function get_shipment($id) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception('', 500);
            }
            $rcv = new $this->m_po;
            $inshipment = $rcv->setTables('penerimaan_barang pb')
//                            ->setWheres(['status <>' => 'cancel'])
                            ->setSelects(["pb.*"])->setOrder(["tanggal" => "asc"])
                            ->setWhereRaw("origin like '{$kode_decrypt}%'")->getData();

            $dataView = $this->load->view('purchase/v_po_shp_data', ["data" => $inshipment], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('data' => $dataView)));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function get_invoice($id) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt)
                throw new \Exception('', 500);

            $rcv = new $this->m_po;
            $inshipment = $rcv->setTables('invoice')
                    ->setWheres(['no_po' => $kode_decrypt, "status <>" => "cancel"])->setOrder(["order_date" => "asc"])
                    ->setJoins("mst_status", "mst_status.kode = invoice.status", "left")
                    ->setSelects(["invoice.*", "coalesce(mst_status.nama_status,status) as status"])
                    ->getData();
            $dataView = $this->load->view('purchase/v_po_inv_data', ["inv" => $inshipment], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('data' => $dataView)));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}
