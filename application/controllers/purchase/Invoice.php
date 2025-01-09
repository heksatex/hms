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
            $list->setTables("invoice")
                    ->setOrders([null, "partner.nama", "no_invoice_supp", "tanggal_invoice_supp", "no_sj_supp", "no_po", "order_date", "status"])
                    ->setSearch(["partner.nama", "no_invoice_supp", "no_sj_supp", "no_po", "status"])
                    ->setJoins("partner", "partner.id = invoice.id_supplier", "left")
                    ->setSelects(["invoice.*", "partner.nama as supplier"])->setOrder(['create_date' => 'desc']);

            $no = $_POST['start'];
            foreach ($list->getData() as $field) {
                $kode_encrypt = encrypt_url($field->id);
                $no++;
                $data [] = array(
                    $no,
                    '<a href="' . base_url('purchase/invoice/edit/' . $kode_encrypt) . '">' . $field->supplier . '</a>',
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
            $datas = $head->setTables("invoice")->setJoins("partner", "partner.id = id_supplier", "left")
                            ->setJoins("currency_kurs", "currency_kurs.id = matauang", "left")
                    ->setJoins("currency","currency.nama = currency_kurs.currency","left")
                    ->setWheres(["invoice.id" => $kode_decrypt])
                            ->setSelects(["invoice.*", "partner.nama as supplier", "currency as mata_uang","currency.symbol"])->getDetail();
            if ($datas === null) {
                throw new \Exception();
            }
            $data["inv"] = $datas;
            $data['mms'] = $this->_module->get_data_mms_for_log_history('PINV');
            $data["invDetail"] = $detail->setTables("invoice_detail")->setWheres(["invoice_id" => $kode_decrypt])
                            ->setJoins("tax", "tax.id = tax_id", "left")
                            ->setJoins("coa", "coa.kode_coa = account", "left")
                            ->setSelects(["invoice_detail.*", "tax.nama as pajak,tax.ket as pajak_ket,amount", "kode_coa,coa.nama as nama_coa"])
                            ->setOrder(["id"])->getData();
            $this->load->view('purchase/v_invoice_edit', $data);
        } catch (Exception $ex) {
            return show_404();
        }
    }

    public function data_detail() {
        try {
            $id = $this->input->post("id");
            $data = array();
            $list = new $this->m_global;
            $kode_decrypt = decrypt_url($id);
            $list->setTables("invoice_detail")->setWheres(["invoice_id" => $kode_decrypt])->setOrder(["id"])
                    ->setOrders([null, "kode_produk", null, null, "account", "qty_beli", "harga_satuan"]);
            $no = $_POST['start'];
            foreach ($list->getData() as $field) {
                $no++;
                $data [] = array(
                    $no,
                    $field->kode_produk . " - " . $field->nama_produk,
                    $field->deskripsi,
                    $field->reff_note,
                    $field->account,
                    $field->qty_beli . "  " . $field->uom_beli,
                    $field->harga_satuan,
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

    public function update($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kode_decrypt = decrypt_url($id);
            $noInvSupp = $this->input->post("no_invoice_supp");
            $tglInvSupp = $this->input->post("tanggal_invoice_supp");
            $noSjSupp = $this->input->post("no_sj_supp");

            $harga = $this->input->post("harga_satuan");
            $item = [];
            foreach ($harga as $key => $value) {
                $item[] = ["id" => $key, "harga_satuan" => $value];
            }
            $head = new $this->m_global;
            $bd = clone $head;
            $dataUpdate = ["no_sj_supp" => $noSjSupp, "no_invoice_supp" => $noInvSupp, "tanggal_invoice_supp" => $tglInvSupp];
            $head->setTables('invoice')->setWheres(["id" => $kode_decrypt])
                    ->update($dataUpdate);
            $bd->setTables("invoice_detail")->updateBatch($item, 'id');
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'update', logArrayToString('; ', $dataUpdate), $username);
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
        try {
            $sub_menu = $this->uri->segment(2);
            $username = addslashes($this->session->userdata('username'));

            $kodeJurnal = $this->input->post("jurnal");
            $status = $this->input->post("status");
            $id = $this->input->post("id");
            $inv = $this->input->post("inv");
            $kode_decrypt = decrypt_url($id);
            $head = new $this->m_global;
            $this->_module->startTransaction();
            $lock = "invoice WRITE,jurnal_entries WRITE,jurnal_entries_items WRITE,token_increment WRITE,"
                    . "currency_kurs WRITE,currency WRITE,tax WRITE,invoice_detail WRITE,user WRITE, main_menu_sub WRITE, log_history WRITE";
            $this->_module->lock_tabel($lock);
            if ($status === 'cancel') {
            $cekJurnal = clone $head;
            
                if ($cekJurnal->setTables("jurnal_entries")->setWheres(["origin" => $inv, "status <>" => $status])->getDetail() !== "null") {
                    throw new \Exception('Jurnal Tidak Ada', 500);
                }
            } else if ($status === 'done') {
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
                $jurnalData = ["kode" => $jurnal, "periode" => date('y', strtotime($now)) . '/' . date('m', strtotime($now)),
                    "origin" => $inv, "status" => "draft", "tanggal_dibuat" => date("Y-m-d H:i:s")];
                $jurnalDB->setTables("jurnal_entries")->save($jurnalData);
                $dataItems = $items->setTables("invoice_detail")->setWheres(["invoice_id" => $kode_decrypt])
                                ->setJoins("invoice", "invoice.id = invoice_detail.invoice_id")
                                ->setJoins("tax", "tax.id = invoice_detail.tax_id", "left")
//                                ->setJoins("mst_produk_coa", "mst_produk_coa.kode_produk = invoice_detail.kode_produk", "left")
                                ->setJoins("currency_kurs", "currency_kurs.id = invoice.matauang", "left")
                                ->setJoins("currency", "currency_kurs.currency = currency.nama", "left")
                                ->setSelects(["invoice_detail.*", "invoice.id_supplier", "currency_kurs.currency,currency_kurs.kurs,currency.symbol", "COALESCE(tax.amount,0) as tax_amount"])
                                ->setOrder(["invoice_id"])->getData();
                $jurnalItems = [];
                $tax = 0;
                foreach ($dataItems as $key => $value) {
                    $nominal = $value->harga_satuan * $value->qty_beli;
                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => $value->kode_produk . " " . $value->nama_produk,
                        "reff_note" => $value->reff_note,
                        "partner" => $value->id_supplier,
                        "kode_coa" => $value->account,
                        "posisi" => "D",
                        "nominal_curr" => $value->kurs,
                        "kurs" => $value->currency,
                        "kode_mua" => $value->symbol,
                        "nominal" => $nominal * $value->kurs,
                        "row_order" => ($key + 1)
                    );
                    $tax += $nominal * $value->tax_amount;
                }
                if ($tax > 0) {
                    $jurnalItems[] = array(
                        "kode" => $jurnal,
                        "nama" => "",
                        "reff_note" => "",
                        "partner" => $dataItems[0]->id_supplier,
                        "kode_coa" => "2112.01",
                        "posisi" => "K",
                        "nominal_curr" => $dataItems[0]->kurs,
                        "kurs" => $dataItems[0]->currency,
                        "kode_mua" => $dataItems[0]->symbol,
                        "nominal" => $tax,
                        "row_order" => count($dataItems) + 1
                    );
                }
                $jurnalDBItems = new $this->m_global;
                $jurnalDBItems->setTables("jurnal_entries_items")->saveBatch($jurnalItems);
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
            $data["inv"] = $head->setTables("invoice")->setJoins("partner", "partner.id = id_supplier", "left")
                            ->setJoins("currency_kurs", "currency_kurs.id = matauang", "left")->setWheres(["invoice.id" => $kode_decrypt])
                            ->setSelects(["invoice.*", "partner.nama as supplier,delivery_street,delivery_city", "currency as mata_uang"])->getDetail();

            $data["invDetail"] = $detail->setTables("invoice_detail")->setWheres(["invoice_id" => $kode_decrypt])
                            ->setJoins("tax", "tax.id = tax_id", "left")->setSelects(["invoice_detail.*", "tax.nama as pajak,tax.ket as pajak_ket,amount"])
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
}
