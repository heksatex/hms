<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Fpt
 *
 * @author RONI
 */
class Fpt extends MY_Controller {

    //put your code here
    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model('m_po');
        $this->load->model('m_global');
        $this->load->model('m_user');
        $this->load->model('m_cfb');
        $this->load->model('m_produk');
        $this->load->model('_module');
        $this->load->model("m_konversiuom");
        $this->load->library("token");
    }

    public function index() {
        $data['id_dept'] = 'FPT';
        $this->load->view('purchase/v_fpt', $data);
    }

    public function add() {
//        $data['id_dept'] = 'FPT';
        $data['warehouse'] = $this->_module->get_list_departement();
        $data['id_dept'] = 'FPT';
        $data['jenis'] = 'FPT';
        $this->load->view('purchase/v_order_add', $data);
    }

    public function edit($id) {
        try {
            $username = $this->session->userdata('username');
            $kode_decrypt = decrypt_url($id);
            $data['id'] = $id;
            $data['id_dept'] = 'FPT';
            $model1 = new $this->m_po;
            $model2 = clone $model1;
            $model3 = clone $model2;
            $model4 = clone $model2;
            $data["setting"] = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            $data['user'] = $this->m_user->get_user_by_username($username);
            $data["po"] = $model1->setTables("purchase_order po")->setJoins("partner p", "p.id = po.supplier")
                            ->setJoins("currency_kurs", "currency_kurs.id = po.currency", "left")
                            ->setJoins("currency", "currency.nama = currency_kurs.currency", "left")
                            ->setSelects(["po.*", "p.nama as supp", "currency.symbol"])
                            ->setWheres(["po.no_po" => $kode_decrypt, "po.jenis" => "FPT"])->getDetail();
            if (!$data["po"]) {
                throw new \Exception('Data PO tidak ditemukan', 500);
            }
            $nextPage = $model1->setWheres(["po.id >" => $data["po"]->id, "jenis" => "FPT", "po.supplier" => $data["po"]->supplier], true)
                            ->setOrder(['po.create_date' => 'asc'])->setSelects(["po.no_po"])->getDetail();
            if ($nextPage) {
                $data["next_page"] = base_url("purchase/fpt/edit/" . encrypt_url($nextPage->no_po));
            }

            $prevPage = $model1->setWheres(["po.id <" => $data["po"]->id, "jenis" => "FPT", "po.supplier" => $data["po"]->supplier], true)
                            ->setOrder(['po.create_date' => 'desc'])->setSelects(["po.no_po"])->getDetail();
            if ($prevPage) {
                $data["prev_page"] = base_url("purchase/fpt/edit/" . encrypt_url($prevPage->no_po));
            }

            $data["po_items"] = $model2->setTables("purchase_order_detail pod")->setWheres(["po_no_po" => $kode_decrypt])->setOrder(["id" => "asc"])
                            ->setJoins('tax', "tax.id = tax_id", "left")
                            ->setJoins('mst_produk', "mst_produk.kode_produk = pod.kode_produk")
                            ->setJoins('nilai_konversi nk', "pod.id_konversiuom = nk.id", "left")
                            ->setJoins('(select kode_produk as kopro,GROUP_CONCAT(catatan SEPARATOR "#") as catatan from mst_produk_catatan where jenis_catatan = "pembelian" group by kode_produk) as catatan', "catatan.kopro = pod.kode_produk", "left")
                            ->setSelects(["pod.*", "COALESCE(tax.amount,0) as amount_tax,tax.dpp as dpp_tax,coalesce(tax.tax_lain_id,0) as tax_lain_id", "catatan.catatan", "mst_produk.image", "nk.dari,nk.ke,nk.catatan as catatan_nk"])->getData();
//        $data["uom_beli"] = $this->m_produk->get_list_uom(['beli' => 'yes']);
            $data["tax"] = $model4->setTables("tax")->setWheres(["type_inv" => "purchase"])->setOrder(["id" => "asc"])->getData();
            $data["kurs"] = $this->m_po->setTables("currency_kurs")->setOrder(["id" => "asc"])->getData();
            $this->load->view('purchase/v_fpt_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function update($id) {
        $validation = [
            [
                'field' => 'qty_beli[]',
                'label' => 'Qty',
                'rules' => ['regex_match[/^\d*\.?\d*$/]'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                    "regex_match" => "{field} harus berupa number / desimal"
                ]
            ],
            [
                'field' => 'harga[]',
                'label' => 'Harga Satuan Beli',
                'rules' => ['regex_match[/^\d*\.?\d*$/]'],
                'errors' => [
                    'required' => '{field} Harus dipilih',
                    "regex_match" => "{field} harus berupa number / desimal"
                ]
            ],
            [
                'field' => 'diskon[]',
                'label' => 'Diskon',
                'rules' => ['regex_match[/^\d*\.?\d*$/]'],
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
            $uom_beli = $this->input->post("uom_beli");
            $qty_beli = $this->input->post("qty_beli");
            $tax = $this->input->post("tax");
            $dsk = $this->input->post("diskon");
            $id_konversiuom = $this->input->post("id_konversiuom");
            $uom_jual = $this->input->post("uom_jual");
            $note = $this->input->post("note");
            $deskripsi = $this->input->post("deskripsi");
            $order_date = $this->input->post("order_date");
            $amount_tax = $this->input->post("amount_tax");
            $currency = $this->input->post("currency");
            $nilai_currency = $this->input->post("nilai_currency");
            $tax_lain = $this->input->post("tax_lain_id");
            $foot_note = $this->input->post("foot_note");
            $supplier = $this->input->post("supplier");
            $dppTax = $this->input->post("dpp_tax");

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
            $model3 = new $this->m_global;
            $models = clone $model3;
            $models->setTables("tax");
            $setDpp = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            foreach ($harga as $key => $value) {
                $no++;
                $checkKonversi = $this->m_konversiuom->wheres(["id" => $id_konversiuom[$key], "ke" => $uom_jual[$key], "dari" => $uom_beli[$key]])->getDetail();
                if (!$checkKonversi) {
                    throw new \Exception("<strong>Data No {$no}, Uom dan Uom Beli Tidak ada dalam data konversi</strong>", 500);
                }

                $total = ($qty_beli[$key] * $value);
                $totals += $total;
                $diskon = ($dsk[$key] ?? 0);
                $diskons += $diskon;
                $taxe = 0;
                $nilai_dpp = 0;
                if ($setDpp !== null && $dppTax[$key] === "1") {
                    $taxe += ((($total - $diskon) * 11) / 12) * $amount_tax[$key];
                    $nilai_dpp = ((($total - $diskon) * 11) / 12);
                } else {
                    $taxe += ($total - $diskon) * $amount_tax[$key];
                }

                if ($tax_lain[$key] !== "0") {
                    $dataTax = $models->setWhereIn("id", explode(",", $tax_lain[$key]), true)->setSelects(["amount,dpp"])->setOrder(["id"])->getData();
                    foreach ($dataTax as $kkk => $datas) {
                        if ($setDpp !== null && $datas->dpp === "1") {
                            $taxe += ((($total - $diskon) * 11) / 12) * $datas->amount;
                            $nilai_dpp += ((($total - $diskon) * 11) / 12);
                        }
                        else {
                        $taxe += ($total - $diskon) * $datas->amount;
                        }
                    }
                }

                $taxes += $taxe;
                $total = ($total - $diskon) + $taxe;
                $nilaiDppLain += $nilai_dpp;
                $log_update ["item ke " . $no] = logArrayToString(";", ['harga_per_uom_beli' => $value, 'uom_beli' => $uom_beli[$key], 'diskon' => $dsk[$key], 'deskripsi' => html_entity_decode($deskripsi[$key]),]);
                $data[] = ['id' => $key, 'harga_per_uom_beli' => $value, 'uom_beli' => $uom_beli[$key], 'deskripsi' => html_entity_decode($deskripsi[$key]),
                    'tax_id' => $tax[$key], 'diskon' => $dsk[$key], 'id_konversiuom' => $id_konversiuom[$key], "pajak" => $taxe, "total" => $total, "nilai_dpp" => $nilai_dpp];
            }
//            if ($dpplain === "1") {
//                $nilaiDppLain = (($totals - $diskons) * 11) / 12;
//            }
            $grandTotal = ($totals - $diskons) + $taxes;
            $this->_module->lock_tabel("user WRITE, main_menu_sub READ, log_history WRITE,mst_produk WRITE,"
                    . "purchase_order_detail write,purchase_order write");
            $this->m_po->setTables("purchase_order_detail")->updateBatch($data, 'id');
            $po = new $this->m_po;
            $update = ["currency" => $currency, "nilai_currency" => $nilai_currency, 'foot_note' => $foot_note,
                'note' => $note, "total" => $grandTotal, 'dpp_lain' => $nilaiDppLain, "order_date" => $order_date, "supplier" => $supplier];
            $po->setWheres(["no_po" => $kode_decrypt])->update($update);
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', "Header -> " . logArrayToString('; ', $update, " : ") . "<br> Detail -> " . logArrayToString('; ', $log_update, " : "), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function update_status($id) {
        try {
            $listStatus = [
                "approval" => "purchase_confirmed",
                "done" => "done",
                "cancel" => "cancel",
            ];
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $users = $this->session->userdata('nama');

            $totalItem = $this->input->post("item");
            $kode_decrypt = decrypt_url($id);
            $status = $this->input->post("status");
            $redirect = "";
            $checkData = new $this->m_po;
            $data = $checkData->setWheres(["no_po" => $kode_decrypt])->setJoins('partner', "purchase_order.supplier = partner.id", "left")
                            ->setSelects(["purchase_order.*", "partner.nama as nama_supp"])->getDetail();
            if (!$data) {
                throw new \Exception('Data FPT tidak ditemukan', 500);
            }
            if ($status !== "cancel") {
                if ($data->currency === null) {
                    throw new \Exception('Mata Uang Belum diperbaharui ', 500);
                }
            }

            $this->_module->startTransaction();
            $lockTable = "user WRITE, main_menu_sub READ, log_history WRITE,mst_produk WRITE,cfb_items write,cfb write,procurement_purchase_items write,"
                    . "purchase_order_detail write,purchase_order write, penerimaan_barang WRITE, penerimaan_barang_items WRITE";
            if ($listStatus[$status] === 'purchase_confirmed') {
                $lockTable .= ",stock_move_produk WRITE,stock_move WRITE,nilai_konversi WRITE";
                $lockTable .= ",mst_produk_coa WRITE,nilai_konversi nk WRITE";
            }
            $this->_module->lock_tabel($lockTable);
            $updatePO = ["status" => $listStatus[$status]];
            switch ($listStatus[$status]) {

                case "purchase_confirmed":
                    $podd = new $this->m_po;
                    $checkPod = clone $podd;

                    $checkPod_data = $checkPod->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt, "status <>" => "cancel"])
                                    ->setWhereRaw("(harga_per_uom_beli <=0 or qty_beli <= 0)")->getDetail();
                    if ($checkPod_data) {
                        throw new \Exception('Harga satuan / QTY beli belum ditentukan', 500);
                    }
                    $podd = $podd->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt, "status <>" => "cancel"])
                                    ->setSelects(['group_CONCAT("\'",cfb_items_id,"\'") as items', 'group_CONCAT("\'",kode_cfb,"\'") cfb'])->getDetail();
                    if ($podd && $podd->cfb !== null) {
                        $cfbDetail = new $this->m_cfb;
                        $cfb = clone $cfbDetail;
                        $listCfb = clone $cfbDetail;
                        $cfb->setWhereRaw("kode_cfb in ({$podd->cfb})")->update(["status" => "done"]);
                        $cfbDetail->setTables("cfb_items")->setWhereRaw("id in ({$podd->items})")->update(["status" => "done"]);

                        $produk = [];
                        $dataItemOrder = $listCfb->setTables('purchase_order_detail')->setOrder(["id" => "asc"])->setWheres(["po_no_po" => $kode_decrypt, "status <>" => "cancel"])->getData();
                        foreach ($dataItemOrder as $key => $value) {
                            $updatePP = new $this->m_po;
                            $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "fpt"]);
                        }
                    }

                    //RCV IN

                    break;

                case "done":
//                    
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
                case "cancel":

                    $podd = new $this->m_po;
                    $rcv = clone $podd;
                    $model1 = clone $rcv;

                    $check = $model1->setTables("purchase_order")->setWheres(["no_po" => $kode_decrypt, "payment" => "1"])->getDetail();
                    if ($check) {
                        throw new \Exception("PO {$kode_decrypt} Sudah dibayarkan.", 500);
                    }

                    $inshipment = $rcv->setTables('penerimaan_barang')
                                    ->setJoins("penerimaan_barang_items", "penerimaan_barang.kode = penerimaan_barang_items.kode")
                                    ->setWheres(['status_barang <>' => 'cancel'])
                                    ->setWhereRaw("origin like '{$kode_decrypt}%'")->getDataCountAll();
                    if ((int) $inshipment > 0) {
                        throw new \Exception("Produk Pada RCV In dengan Origin {$kode_decrypt} Tidak dalam status <strong>CANCEL</strong> semua.", 500);
                    }


                    $podd = $podd->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt, "status <>" => "cancel"])
                                    ->setSelects(['group_CONCAT("\'",cfb_items_id,"\'") as items', 'group_CONCAT("\'",kode_cfb,"\'") cfb'])->getDetail();
                    if ($podd && $podd->cfb !== null) {
                        $cfbDetail = new $this->m_cfb;
                        $cfb = clone $cfbDetail;
                        $listCfb = clone $cfbDetail;
                        $cfb->setWhereRaw("kode_cfb in ({$podd->cfb})")->update(["status" => "confirm"]);
                        $cfbDetail->setTables("cfb_items")->setWhereRaw("id in ({$podd->items})")->update(["status" => "confirm"]);

                        foreach ($listCfb->setTables('purchase_order_detail')->setOrder(["id" => "asc"])->setWheres(["po_no_po" => $kode_decrypt, "status <>" => "cancel"])->getData() as $key => $value) {
                            $updatePP = new $this->m_po;
                            $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "confirm"]);
                        }
                    }
                    break;
                default:
                    break;
            }

            if ($listStatus[$status] === "purchase_confirmed") {
//                $orderDate = date("Y-m-d H:i:s");
                $listCfb = new $this->m_po;
                $dataItemOrder = $listCfb->setTables('purchase_order_detail')
                                ->setJoins('nilai_konversi nk', "id_konversiuom = nk.id", "left")->setOrder(["id" => "asc"])
                                ->setJoins("mst_produk_coa", "mst_produk_coa.kode_produk = purchase_order_detail.kode_produk", "left")
                                ->setWheres(["po_no_po" => $kode_decrypt, "status <>" => "cancel"])
                                ->setSelects(["purchase_order_detail.*", "nk.nilai", "kode_coa","konversi_aktif", "pembilang", "penyebut"])->getData();
                $produk = [];

                $inserInvoice = new $this->m_global;
                $checkInvoice = clone $inserInvoice;
                if ($checkInvoice->setTables("penerimaan_barang")->setWheres(["origin" => $kode_decrypt, 'dept_id' => "RCV", 'status <>' => 'cancel'])->getDataCountAll() > 0) {
                    throw new \Exception("No {$kode_decrypt} sudah terbentuk Dokumen Penerimaan", 500);
                }

                foreach ($dataItemOrder as $key => $value) {
                    $row = count($produk) + 1;
                    $updateDataDetail[] = ['id' => $value->id, 'status' => $status, 'nilai_konversi' => $value->nilai];
                    if($value->konversi_aktif === "1") {
                        $qtyy = ($value->qty_beli / $value->pembilang) * $value->penyebut;
                    }
                    else {
                        $qtyy = $value->qty_beli * $value->nilai;
                    }
                    if (!isset($produk[$value->kode_produk])) {
                        $produk[$value->kode_produk] = [
                            'nama_produk' => $value->nama_produk,
                            'kode_produk' => $value->kode_produk,
                            'qty' => $qtyy,
                            'uom' => $value->uom,
                            'status' => 'ready',
                            'origin_prod' => $value->kode_produk . "_" . $row,
                            'row_order' => $row,
                            'kode_pp' => $value->kode_pp,
                            'qty_beli' => $value->qty_beli,
                            'uom_beli' => $value->uom_beli,
                            'id_konversiuom' => $value->id_konversiuom,
                            'nilai_konversiuom' => $value->nilai,
                            "reff_note" => $value->reff_note
                        ];
                    } else {
                        $produk[$value->kode_produk]["qty"] += ($value->qty_beli * $value->nilai);
                    }
//                    if ($data->cfb_manual === "0") {
//                        $updatePP = new $this->m_po;
//                        $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "po"]);
//                    }
                }

                $method_dept = "RCV";
                $kode_ = $this->_module->get_kode_penerimaan($method_dept);
                $get_kode_in = $kode_;

                $dgt = substr("00000" . $get_kode_in, -5);
                $kodeRcv = $method_dept . "/" . "IN" . "/" . date("y") . date("m") . $dgt;

                $sm = "SM" . $this->_module->get_kode_stock_move();
                $insertPenerimaan = new $this->m_po;
                $insertPenerimaanDetail = clone $insertPenerimaan;
                $isDate = date("Y-m-d H:i:s");
                $dataPenerimaan = [
                    'kode' => $kodeRcv,
                    'tanggal' => $isDate,
                    'tanggal_transaksi' => $isDate,
                    'tanggal_jt' => $isDate,
                    'origin' => $kode_decrypt,
                    'move_id' => $sm,
                    'lokasi_dari' => 'SUP/Stock',
                    'lokasi_tujuan' => 'RCV/Stock',
                    'reff_picking' => 'SUP|' . $kodeRcv,
                    'reff_note' => $data->note,
                    'status' => 'ready',
                    'dept_id' => 'RCV',
                    'partner_id' => $data->supplier,
                    'nama_partner' => $data->nama_supp
                ];
                $insertPenerimaan->setTables("penerimaan_barang")->save($dataPenerimaan);
                $smProduk = [];
                $detailProduk = [];
                foreach ($produk as $keys => $values) {
                    $clone = $values;
                    unset($clone["kode_pp"], $clone["qty_beli"], $clone["uom_beli"], $clone["id_konversiuom"], $clone["nilai_konversiuom"], $clone["reff_note"]);
                    $smProduk[] = array_merge($clone, ['move_id' => $sm, 'origin_prod' => $values["origin_prod"]]);
                    unset($values["status"]);
                    $detailProduk[] = array_merge($values, ['kode' => $kodeRcv, 'lot' => '', 'status_barang' => 'ready']);
                }

                $insertPenerimaanDetail->setTables('penerimaan_barang_items')->saveBatch($detailProduk);

                $smdata = "('" . $sm . "','" . $isDate . "','" . $kodeRcv . "|1','RCV|IN','SUP/Stock','RCV/Stock','ready','1','')";
                $this->_module->create_stock_move_batch($smdata);
                $insertSMProduk = new $this->m_po;
                $insertSMProduk->setTables('stock_move_produk')->saveBatch($smProduk);

                $redirect = base_url('purchase/purchaseorder/edit/' . $id);
                $this->_module->gen_history('penerimaanbarang', $kodeRcv, 'create', logArrayToString(";", $dataPenerimaan), $username);
                if ($data->order_date === null || $data->order_date === "0000-00-00 00:00:00") {
                    $updatePO = array_merge($updatePO, ["order_date" => date("Y-m-d H:i:s")]);
                }
                $updatePO = array_merge($updatePO, ["validated_by" => $users["nama"]]);
            }
            $po = new $this->m_po;
            $pod = clone $po;
            $po->setWheres(["no_po" => $kode_decrypt])->update($updatePO);
            $pod->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt])->setWhereRaw("status not in ('cancel','retur')")->update(["status" => $listStatus[$status]]);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            $this->_module->gen_history($sub_menu, $data->no_po, 'edit', "update status ke " . str_replace("_", " ", $listStatus[$status]), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'redirect' => $redirect)));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            log_message('error', json_encode($ex));
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function add_item() {
        try {
            $data["index"] = $this->input->post("index");
            $data["uom_jual"] = $this->m_produk->get_list_uom(['jual' => 'yes']);
            $item = $this->load->view('purchase/v_order_item_manual', $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $item]));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode([]));
        }
    }

    public function add_layanan() {
        try {
            $model = new $this->m_global;
            $data["index"] = $this->input->post("index");
            $data["uom_juals"] = $this->m_produk->get_list_uom(['jual' => 'yes']);
            $data["po"] = $this->input->post("po");
            $data["taxss"] = $model->setTables("tax")->setWheres(["type_inv" => "purchase"])->setOrder(["id" => "asc"])->getData();
            $item = $this->load->view('purchase/v_order_add_layanan', $data, true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $item]));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode([]));
        }
    }

    public function get_produk_layanan() {
        try {
            $search = $this->input->get("search");
            $datas = new $this->m_global;
            if ($search !== "") {
//                $datas = $datas->setWhereRaw("kode_produk LIKE '%{$search}%' or nama_produk LIKE '%{$search}%'");
                $_POST['search']['value'] = $search;
            }
            $_POST['length'] = 50;
            $_POST['start'] = 0;
            $datas = $datas->setTables("mst_produk")->setSelects(["kode_produk,nama_produk,uom"])
                            ->setSearch(["kode_produk", "nama_produk"])
                            ->setJoins("mst_category", "id_category = mst_category.id")
//                            ->setJoins("departemen", "(departemen.kode = mst_category.dept_id and departemen.type_dept='gudang')")
                            ->setJoins("nilai_konversi nk", "nk.id = uom_beli", "left")
                            ->setGroups(["mst_produk.kode_produk"])
                            ->setSelects(["mst_produk.*", "coalesce(dari,'') as dari,nk.id as dari_id,coalesce(nilai,'1') as nilai", "mst_category.dept_id as wrhs"])
                            ->setOrder(["kode_produk" => "asc"])
                            ->setWheres(["status_produk" => "t", "type" => "consumable"])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $datas]));
        } catch (Exception $ex) {
            
        }
    }

    public function save_layanan() {
        try {
            $validation = [
                [
                    'field' => 'kode_produk',
                    'label' => 'Produk',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Harus dipilih'
                    ]
                ],
                [
                    'field' => 'harga',
                    'label' => 'Harga Satuan Beli',
                    'rules' => ['regex_match[/^\d*\.?\d*$/]', 'required'],
                    'errors' => [
                        'required' => '{field} Harus diisi',
                        "regex_match" => "{field} harus berupa number / desimal"
                    ]
                ],
                [
                    'field' => 'qty_beli',
                    'label' => 'Qty',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => '{field} Harus diisi'
                    ]
                ],
                [
                    'field' => 'id_konversi',
                    'label' => 'Konversi QTY',
                    'rules' => ['required'],
                    'errors' => [
                        'required' => 'Konversi QTY Belum ditentukan dimaster produk',
                    ]
                ]
            ];

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }

            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $amount_tax = $this->input->post("amount_tax");
            $deskripsi = $this->input->post("deskripsi");
            $id_konversi = $this->input->post("id_konversi");
            $kode_produk = $this->input->post("kode_produk");
            $nama_produk = $this->input->post("nama_produk");
            $nilai = $this->input->post("nilai");
            $qty_beli = $this->input->post("qty_beli");
            $reff_note = $this->input->post("reff_note");
            $schedule_date = $this->input->post("schedule_date");
            $tax = $this->input->post("tax");
            $dppTax = $this->input->post("dpp_tax");
            $uom = $this->input->post("uom");
            $uom_qty_beli = $this->input->post("uom_qty_beli");
            $warehouse = $this->input->post("warehouse");
            $harga = $this->input->post("harga");
            $id = $this->input->post("po");
            $kode_decrypt = decrypt_url($id);

            $model = new $this->m_global;
            $setDpp = $model->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->getDetail();

            $this->_module->lock_tabel("user WRITE, main_menu_sub READ, log_history WRITE,mst_produk WRITE,"
                    . "purchase_order_detail write,purchase_order write");
            $getPo = $model->setTables("purchase_order")->setWheres(["no_po" => $kode_decrypt, "status" => "draft"], true)->getDetail();
            if (!$getPo) {
                throw new \Exception("Data PO Tidak ditemukan dalam status draft", 500);
            }
            $total = ($qty_beli * $harga);
            $nilai_dpp = 0;
            $pajak = 0;
            if ($setDpp !== null && $dppTax === "1") {
                $pajak = (($total * 11) / 12) * $amount_tax;
                $nilai_dpp = (($total * 11) / 12);
            } else {
                $pajak = $total * $amount_tax;
            }
            $total += $pajak;
            $grandTotal = $getPo->total + $total;
            $grandDpp = $getPo->dpp_lain + $nilai_dpp;

            $insert = [
                "po_id" => $getPo->id,
                "po_no_po" => $kode_decrypt,
                "cfb_items_id" => 0,
                "kode_cfb" => "",
                "kode_produk" => html_entity_decode($kode_produk),
                "nama_produk" => html_entity_decode($nama_produk),
                "qty" => $qty_beli * $nilai,
                "uom" => $uom,
                "qty_beli" => $qty_beli,
                "uom_beli" => $uom_qty_beli,
                'id_konversiuom' => $id_konversi,
                "pritoritas" => "Normal",
                "status" => "draft",
                "kode_pp" => 0,
                'harga_per_uom_beli' => $harga,
                "created_at" => date("Y-m-d H:i:s"),
                "deskripsi" => html_entity_decode($deskripsi),
                "reff_note" => html_entity_decode($reff_note),
                "warehouse" => $warehouse,
                "schedule_date" => $schedule_date,
                "tax_id" => $tax,
                "total" => $total,
                "pajak" => $pajak,
                "nilai_dpp" => $nilai_dpp,
                "deleting" => 1
            ];
            $this->_module->startTransaction();
            $model->update(["total" => $grandTotal, "dpp_lain" => $grandDpp]);
            $model->setTables("purchase_order_detail")->save($insert);
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', "tambah Layanan -> " . logArrayToString('; ', $insert, " : "), $username);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Tambah layanan', 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            log_message('error', json_encode($ex));
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function delete_layanan($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $ids = $this->input->post("ids");
            $kode_decrypt = decrypt_url($id);
            $model = new $this->m_global;
            $this->_module->lock_tabel("user WRITE, main_menu_sub READ, log_history WRITE,mst_produk WRITE,"
                    . "purchase_order_detail write,purchase_order write");

            $getPO = $model->setTables("purchase_order")->setWheres(["no_po" => $kode_decrypt], true)->getDetail();
            if (!$getPO) {
                throw new \Exception("Data PO Tidak ditemukan dalam status draft", 500);
            }
            $getPOD = $model->setTables("purchase_order_detail")->setWheres(["id" => $ids, "po_no_po" => $getPO->no_po, "deleting" => "1"], true)->getDetail();

            if (!$getPOD) {
                throw new \Exception("Data Item PO Tidak ditemukan dalam status draft", 500);
            }

            $grandTotal = $getPO->total - $getPOD->total;
            $grandDpp = $getPO->dpp_lain - $getPOD->nilai_dpp;

            $this->_module->startTransaction();
            $model->delete();
            $model->setTables("purchase_order")->setWheres(["no_po" => $kode_decrypt], true)->update(["total" => $grandTotal, "dpp_lain" => $grandDpp]);
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', "hapus Layanan -> " . logArrayToString('; ', (array) $getPOD, " : "), $username);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Tambah layanan', 500);
            }
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            log_message('error', json_encode($ex));
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }
}
