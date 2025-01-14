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
//        $this->load->view('purchase/v_fpt_add', $data);
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
            $data["setting"] = $model3->setTables("setting")->setWheres(["setting_name"=>"dpp_lain","status"=>"1"])->setSelects(["value"])->getDetail();
            $data['user'] = $this->m_user->get_user_by_username($username);
            $data["po"] = $model1->setTables("purchase_order po")->setJoins("partner p", "p.id = po.supplier")
                    ->setJoins("currency_kurs","currency_kurs.id = po.currency","left")
                    ->setJoins("currency","currency.nama = currency_kurs.currency","left")
                            ->setSelects(["po.*", "p.nama as supp","currency.symbol"])
                    ->setWheres(["po.no_po" => $kode_decrypt, "po.jenis" => "FPT"])->getDetail();
            if (!$data["po"]) {
                throw new \Exception('Data PO tidak ditemukan', 500);
            }
            $data["po_items"] = $model2->setTables("purchase_order_detail pod")->setWheres(["po_no_po" => $kode_decrypt])->setOrder(["id" => "asc"])
                            ->setJoins('tax', "tax.id = tax_id", "left")
                            ->setJoins('mst_produk', "mst_produk.kode_produk = pod.kode_produk")
                            ->setJoins('nilai_konversi nk', "pod.id_konversiuom = nk.id", "left")
                            ->setJoins('(select kode_produk as kopro,GROUP_CONCAT(catatan SEPARATOR "#") as catatan from mst_produk_catatan where jenis_catatan = "pembelian" group by kode_produk) as catatan', "catatan.kopro = pod.kode_produk", "left")
                            ->setSelects(["pod.*", "COALESCE(tax.amount,0) as amount_tax", "catatan.catatan", "mst_produk.image", "nk.dari,nk.ke,nk.catatan as catatan_nk"])->getData();
//        $data["uom_beli"] = $this->m_produk->get_list_uom(['beli' => 'yes']);
            $data["tax"] = $this->m_po->setTables("tax")->setOrder(["id" => "asc"])->getData();
            $data["kurs"] = $this->m_po->setTables("currency_kurs")->setOrder(["id" => "asc"])->getData();
            $this->load->view('purchase/v_fpt_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function update($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $kode_decrypt = decrypt_url($id);
            $harga = $this->input->post("harga");
            $uom_beli = $this->input->post("uom_beli");
            $tax = $this->input->post("tax");
            $diskon = $this->input->post("diskon");
            $id_konversiuom = $this->input->post("id_konversiuom");
            $uom_jual = $this->input->post("uom_jual");
            $note = $this->input->post("note");
            $deskripsi = $this->input->post("deskripsi");
            $totals = $this->input->post("totals");
            
            $currency = $this->input->post("currency");
            $nilai_currency = $this->input->post("nilai_currency");

            $data = [];
            $log_update = [];
            $no = 0;
            foreach ($harga as $key => $value) {
                $no++;
                $checkKonversi = $this->m_konversiuom->wheres(["id" => $id_konversiuom[$key], "ke" => $uom_jual[$key], "dari" => $uom_beli[$key]])->getDetail();
                if (!$checkKonversi) {
                    throw new \Exception("<strong>Data No {$no}, Uom dan Uom Beli Tidak ada dalam data konversi</strong>", 500);
                }

                $log_update ["item ke " . $no] = logArrayToString(";", ['harga_per_uom_beli' => $value, 'uom_beli' => $uom_beli[$key], 'diskon' => $diskon[$key], 'deskripsi' => $deskripsi[$key],]);
                $data[] = ['id' => $key, 'harga_per_uom_beli' => $value, 'uom_beli' => $uom_beli[$key], 'deskripsi' => $deskripsi[$key],
                    'tax_id' => $tax[$key], 'diskon' => $diskon[$key], 'id_konversiuom' => $id_konversiuom[$key]];
            }
            $this->_module->lock_tabel("user WRITE, main_menu_sub WRITE, log_history WRITE,mst_produk WRITE,"
                    . "purchase_order_detail write,purchase_order write");
            $this->m_po->setTables("purchase_order_detail")->updateBatch($data, 'id');
            $po = new $this->m_po;
            $po->setWheres(["no_po" => $kode_decrypt])->update(["currency" => $currency, "nilai_currency" => $nilai_currency, 'note' => $note,"total"=>$totals]);
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', logArrayToString('; ', $log_update, " : "), $username);
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
            $lockTable = "user WRITE, main_menu_sub WRITE, log_history WRITE,mst_produk WRITE,cfb_items write,cfb write,procurement_purchase_items write,"
                    . "purchase_order_detail write,purchase_order write";
            if ($listStatus[$status] === 'purchase_confirmed') {
                $lockTable .= ",penerimaan_barang WRITE,penerimaan_barang_items WRITE,stock_move_produk WRITE,stock_move WRITE,token_increment WRITE,nilai_konversi WRITE";
                $lockTable .= ",mst_produk_coa WRITE,nilai_konversi nk WRITE";
            }
            $this->_module->lock_tabel($lockTable);
            $updatePO = ["status" => $listStatus[$status]];
            switch ($listStatus[$status]) {

                case "purchase_confirmed":
                    $podd = new $this->m_po;
                    $checkPod = clone $podd;

                    $checkPod_data = $checkPod->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt])
                                    ->setWhereRaw("(harga_per_uom_beli <=0 or qty_beli <= 0)")->getDetail();
                    if ($checkPod_data) {
                        throw new \Exception('Harga satuan / QTY beli belum ditentukan', 500);
                    }
                    $podd = $podd->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt])
                                    ->setSelects(['group_CONCAT("\'",cfb_items_id,"\'") as items', 'group_CONCAT("\'",kode_cfb,"\'") cfb'])->getDetail();
                    if ($podd && $podd->cfb !== null) {
                        $cfbDetail = new $this->m_cfb;
                        $cfb = clone $cfbDetail;
                        $listCfb = clone $cfbDetail;
                        $cfb->setWhereRaw("kode_cfb in ({$podd->cfb})")->update(["status" => "done"]);
                        $cfbDetail->setTables("cfb_items")->setWhereRaw("id in ({$podd->items})")->update(["status" => "done"]);

                        $produk = [];
                        $dataItemOrder = $listCfb->setTables('purchase_order_detail')->setOrder(["id" => "asc"])->setWheres(["po_no_po" => $kode_decrypt])->getData();
                        foreach ($dataItemOrder as $key => $value) {
                            $updatePP = new $this->m_po;
                            $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "fpt"]);
                        }
                    }

                    //RCV IN

                    break;

                case "done":
//                    $listCfb = new $this->m_po;
//                    $dataItemOrder = $listCfb->setTables('purchase_order_detail')->setOrder(["id" => "asc"])->setWheres(["po_no_po" => $kode_decrypt])->getData();
//                    $produk = [];
//                    foreach ($dataItemOrder as $key => $value) {
//                        if (!isset($produk[$value->kode_produk])) {
//                            $produk[$value->kode_produk] = [
//                                'nama_produk' => $value->nama_produk,
//                                'kode_produk' => $value->kode_produk,
//                                'qty' => $value->qty,
//                                'uom' => $value->uom,
//                                'status' => 'ready',
//                                'origin_prod' => '',
//                                'row_order' => count($produk) + 1
//                            ];
//                        } else {
//                            $produk[$value->kode_produk][qty] += $value->qty;
//                        }
//                        $updatePP = new $this->m_po;
//                        $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "po"]);
//                    }
//                    $redirect = base_url('purchase/purchaseorder/edit/' . $id);
                    break;
                case "cancel":

                    $podd = new $this->m_po;
                    $podd = $podd->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt])
                                    ->setSelects(['group_CONCAT("\'",cfb_items_id,"\'") as items', 'group_CONCAT("\'",kode_cfb,"\'") cfb'])->getDetail();
                    if ($podd && $podd->cfb !== null) {
                        $cfbDetail = new $this->m_cfb;
                        $cfb = clone $cfbDetail;
                        $listCfb = clone $cfbDetail;
                        $cfb->setWhereRaw("kode_cfb in ({$podd->cfb})")->update(["status" => "confirm"]);
                        $cfbDetail->setTables("cfb_items")->setWhereRaw("id in ({$podd->items})")->update(["status" => "confirm"]);

                        foreach ($listCfb->setTables('purchase_order_detail')->setOrder(["id" => "asc"])->setWheres(["po_no_po" => $kode_decrypt])->getData() as $key => $value) {
                            $updatePP = new $this->m_po;
                            $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "confirm"]);
                        }
                    }
                    break;
                default:
                    break;
            }

            if ($listStatus[$status] === "purchase_confirmed") {
                $orderDate = date("Y-m-d H:i:s");
                $listCfb = new $this->m_po;
                $dataItemOrder = $listCfb->setTables('purchase_order_detail')
                                ->setJoins('nilai_konversi nk', "id_konversiuom = nk.id", "left")->setOrder(["id" => "asc"])
                                ->setJoins("mst_produk_coa", "mst_produk_coa.kode_produk = purchase_order_detail.kode_produk", "left")
                                ->setWheres(["po_no_po" => $kode_decrypt])
                                ->setSelects(["purchase_order_detail.*", "nk.nilai", "kode_coa"])->getData();
                $produk = [];

//                $invoiceDetail = [];
//                $dataInvoice = ["id_supplier" => $data->supplier, "no_po" => $kode_decrypt, "order_date" => $orderDate,
//                    "created_at" => date("Y-m-d H:i:s"), "matauang" => $data->currency, 'nilai_matauang' => $data->nilai_currency];
                $inserInvoice = new $this->m_global;
                $checkInvoice = clone $inserInvoice;
                if ($checkInvoice->setTables("penerimaan_barang")->setWheres(["origin" => $kode_decrypt, 'dept_id' => "RCV", 'status <>' => 'cancel'])->getDataCountAll() > 0) {
                    throw new \Exception("No {$kode_decrypt} sudah terbentuk Dokumen Penerimaan", 500);
                }

                foreach ($dataItemOrder as $key => $value) {
                    $row = count($produk) + 1;
                    $updateDataDetail[] = ['id' => $value->id, 'status' => $status, 'nilai_konversi' => $value->nilai];
                    if (!isset($produk[$value->kode_produk])) {
                        $produk[$value->kode_produk] = [
                            'nama_produk' => $value->nama_produk,
                            'kode_produk' => $value->kode_produk,
                            'qty' => ($value->qty_beli * $value->nilai),
                            'uom' => $value->uom,
                            'status' => 'ready',
                            'origin_prod' => $value->kode_produk . "_" . $row,
                            'row_order' => $row
                        ];
                    } else {
                        $produk[$value->kode_produk]["qty"] += ($value->qty_beli * $value->nilai);
                    }
                    if ($data->cfb_manual === "1") {
                        $updatePP = new $this->m_po;
                        $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "po"]);
                    }
                }

                if (!$kodeRcv = $this->token->noUrut('receive_in', date('ym'), true)->generate('RCV/IN/', '%05d')->get())
                    throw new \Exception("No Receive IN tidak terbuat", 500);


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
                    'reff_note' => '',
                    'status' => 'ready',
                    'dept_id' => 'RCV',
                    'partner_id' => $data->supplier,
                    'nama_partner' => $data->nama_supp
                ];
                $insertPenerimaan->setTables("penerimaan_barang")->save($dataPenerimaan);
                $smProduk = [];
                $detailProduk = [];
                foreach ($produk as $keys => $values) {
                    $smProduk[] = array_merge($values, ['move_id' => $sm, 'origin_prod' => $values["origin_prod"]]);
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
                $updatePO = array_merge($updatePO, ["order_date" => date("Y-m-d H:i:s"), "validated_by" => $users["nama"]]);

                //create Invoice_detail
//                $inserInvoice->setTables("invoice_detail")->saveBatch($invoiceDetail);
//                $this->_module->gen_history('invoice', $idInsert, 'create', logArrayToString(";", $dataInvoice), $username);
            }
            $po = new $this->m_po;
            $pod = clone $po;
            $po->setWheres(["no_po" => $kode_decrypt])->update($updatePO);
            $pod->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt])->update(["status" => $listStatus[$status]]);
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
}
