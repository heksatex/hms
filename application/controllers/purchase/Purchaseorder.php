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
    }

//4833 2650
    protected $status = [
        'purchase_confirmed' => "Draft",
        'cancel' => "Cancel",
        'done' => 'Done'
    ];

    public function index() {
        $data['id_dept'] = 'PO';
        $this->load->view('purchase/v_po', $data);
    }

    public function edit($id) {
        try {
            $username = $this->session->userdata('username');
            $kode_decrypt = decrypt_url($id);
            $data['id'] = $id;
            $data['id_dept'] = 'PO';
            $model1 = new $this->m_po;
            $model2 = new $this->m_po;
            $model3 = clone $model2;
            $data["setting"] = $model3->setTables("setting")->setWheres(["setting_name"=>"dpp_lain","status"=>"1"])->setSelects(["value"])->getDetail();
            $data['user'] = $this->m_user->get_user_by_username($username);
            $data["po"] = $model1->setTables("purchase_order po")->setJoins("partner p", "p.id = po.supplier")
                    ->setJoins("currency_kurs","currency_kurs.id = po.currency","left")
                    ->setJoins("currency","currency.nama = currency_kurs.currency","left")
                            ->setSelects(["po.*", "p.nama as supp","currency.symbol"])
                    ->setWheres(["po.no_po" => $kode_decrypt])
                    ->setWhereRaw("po.status in ('done','cancel','purchase_confirmed')")->getDetail();
            if (!$data["po"]) {
                throw new \Exception('Data PO tidak ditemukan', 500);
            }
            $data["po_items"] = $model2->setTables("purchase_order_detail pod")->setWheres(["po_no_po" => $kode_decrypt])->setOrder(["id" => "asc"])
                            ->setJoins('tax', "tax.id = tax_id", "left")
                            ->setJoins('mst_produk', "mst_produk.kode_produk = pod.kode_produk")
                            ->setJoins('nilai_konversi nk', "pod.id_konversiuom = nk.id", "left")
                            ->setJoins('(select kode_produk as kopro,GROUP_CONCAT(catatan SEPARATOR "#") as catatan from mst_produk_catatan where jenis_catatan = "pembelian" group by kode_produk) as catatan', "catatan.kopro = pod.kode_produk", "left")
                            ->setSelects(["pod.*", "COALESCE(tax.amount,0) as amount_tax", "catatan.catatan", "mst_produk.image", "nk.dari,nk.ke,nk.catatan as catatan_nk"])->getData();
            $data["uom_beli"] = $this->m_produk->get_list_uom(['beli' => 'yes']);
            $data["tax"] = $this->m_po->setTables("tax")->setOrder(["id" => "asc"])->getData();
            $data["kurs"] = $this->m_po->setTables("currency_kurs")->setOrder(["id" => "asc"])->getData();
            $data["status"] = $this->status;
            $this->load->view('purchase/v_po_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function list_data() {
        try {
            $data = array();
            $list = $this->m_po->setTables("purchase_order po")->setOrders([null, "no_po", "nama_supplier", "order_date", "create_date", "status"])
                    ->setSelects(["po.*", "p.nama as nama_supplier", "nama_status"])->setOrder(['create_date' => 'desc'])
                    ->setSearch(["p.nama", "no_po", "prioritas", "status"])
                    ->setJoins("partner p", "(p.id = po.supplier and p.supplier = 1)")
                    ->setJoins("mst_status", "mst_status.kode = po.status", "left")
                    ->setWhereRaw("status in ('done','cancel','purchase_confirmed') and jenis <>'FPT'");

            $no = $_POST['start'];
            foreach ($list->getData() as $field) {
                $no++;
                $data [] = [
                    $no,
                    '<a href="' . base_url('purchase/purchaseorder/edit/' . encrypt_url($field->no_po)) . '">' . $field->no_po . '</a>',
                    $field->nama_supplier,
                    $field->order_date,
                    $field->create_date,
                    $field->total,
                    $field->nama_status ?? $field->status
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
            $kode_decrypt = decrypt_url($id);
            $status = $this->input->post("status");
            $totalItem = $this->input->post("item");
            $checkData = new $this->m_po;
            $data = $checkData->setWheres(["no_po" => $kode_decrypt])->getDetail();
            if (!$data) {
                throw new \Exception('Data PO tidak ditemukan', 500);
            }
            if ($status !== "cancel") {
                if ($data->currency === null) {
                    throw new \Exception('Mata Uang Belum diperbaharui ', 500);
                }
            }
            $this->_module->startTransaction();
            $lockTable = "user WRITE, main_menu_sub WRITE, log_history WRITE,mst_produk WRITE,cfb_items write,cfb write,procurement_purchase_items write,"
                    . "purchase_order_detail write,purchase_order write,penerimaan_barang WRITE,penerimaan_barang_items WRITE";
            if ($status === 'done') {
                $lockTable .= ",stock_move_produk WRITE,stock_move WRITE,token_increment WRITE,nilai_konversi nk WRITE";
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

                        foreach ($listCfb->setTables('purchase_order_detail')->setOrder(["id" => "asc"])->setWheres(["po_no_po" => $kode_decrypt])->getData() as $key => $value) {
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
            }
            $po = new $this->m_po;
            $pod = clone $po;
            $po->setWheres(["no_po" => $kode_decrypt])->update(["status" => $status]);
            if (count($updateDataDetail) > 0) {
                $pod->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt])->updateBatch($updateDataDetail, "id");
            }

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update status', 500);
            }
            $this->_module->gen_history($sub_menu, $data->no_po, 'edit', "update status ke " . $status, $username);
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
            $qty_beli = $this->input->post("qty_beli");
            $oldTotal = $this->input->post("totals");
            $currency = $this->input->post("currency");
            $nilai_currency = $this->input->post("nilai_currency");
            $status = "purchase_confirmed";
            $data = [];
            $log_update = [];
            $no = 0;
            $totals = 0;
            $diskons = 0;
            $taxes = 0;
            $idDetail = [];
//            $newTotal = 0;
            foreach ($harga as $key => $value) {
                $no++;
                $taxs = explode("|", $tax[$key]);
                $checkKonversi = $this->m_konversiuom->wheres(["id" => $id_konversiuom[$key], "ke" => $uom_jual[$key], "dari" => $uom_beli[$key]])->getDetail();
                if (!$checkKonversi) {
                    throw new \Exception("<strong>Data No {$no}, Uom dan Uom Beli Tidak ada dalam tabel konversi</strong>", 500);
                }
                $idDetail[] = $key;
                $log_update ["item ke " . $no] = logArrayToString(";", ['harga_per_uom_beli' => $value, 'uom_beli' => $uom_beli[$key], 'diskon' => $diskon[$key]]);
                $data[] = ['id' => $key, 'harga_per_uom_beli' => $value, 'uom_beli' => $uom_beli[$key], 'tax_id' => $taxs[0] ?? null, 'diskon' => $diskon[$key], 'id_konversiuom' => $id_konversiuom[$key]];
                $total = ($qty_beli[$key] * ($value * $nilai_currency));
                $totals += $total;
                $diskons += (($diskon[$key] ?? 0) * $nilai_currency);
                $taxes += $total * $taxs[1] ?? 0;
            }
            $newTotal = ($totals - $diskons) + $taxes;
            if ($oldTotal !== $newTotal) {
                if ($newTotal > 10000000) {
                    $status = "waiting_approval";
                }
            }

            $this->_module->lock_tabel("user WRITE, main_menu_sub WRITE, log_history WRITE,mst_produk WRITE,"
                    . "purchase_order_detail write,purchase_order write");
            $this->m_po->setTables("purchase_order_detail")->updateBatch($data, 'id');
            $po = new $this->m_po;
            $po2 = clone $po;
            $po2->setTables('purchase_order_detail')->setWhereIn("id", $idDetail)->update(['status' => $status]);
            $po->setWheres(["no_po" => $kode_decrypt])->update(["currency" => $currency, "nilai_currency" => $nilai_currency, 'status' => $status]);
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', logArrayToString('; ', $log_update, " : "), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'status' => $status)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function print() {
        try {
            $id = $this->input->post("id");
            $kode_decrypt = decrypt_url($id);
            $model1 = new $this->m_po;
            $model2 = clone $model1;
            $data["po"] = $model1->setTables("purchase_order po")->setJoins("partner p", "p.id = po.supplier")
                            ->setJoins("currency_kurs", "currency_kurs.id = po.currency", "left")
                            ->setSelects(["po.*", "p.nama as supp,concat(delivery_street,' ',delivery_city) as alamat_kirim", "currency_kurs.currency as matauang"])
                            ->setWheres(["po.no_po" => $kode_decrypt])->setWhereRaw("po.status in ('done','cancel','purchase_confirmed')")->getDetail();
            $data["po_items"] = $model2->setTables("purchase_order_detail pod")->setWheres(["po_no_po" => $kode_decrypt])->setOrder(["id" => "asc"])
                            ->setJoins('tax', "tax.id = tax_id", "left")
                            ->setJoins('mst_produk', "mst_produk.kode_produk = pod.kode_produk")
                            ->setJoins('nilai_konversi nk', "pod.id_konversiuom = nk.id", "left")
                            ->setJoins('(select kode_produk as kopro,GROUP_CONCAT(catatan SEPARATOR "#") as catatan from mst_produk_catatan where jenis_catatan = "pembelian" group by kode_produk) as catatan', "catatan.kopro = pod.kode_produk", "left")
                            ->setSelects(["pod.*", "COALESCE(tax.amount,0) as amount_tax,tax.nama as tax_name", "catatan.catatan", "mst_produk.image", "nk.dari,nk.ke,nk.catatan as catatan_nk"])->getData();

            $url = "dist/storages/print/po";
            if (!is_dir(FCPATH . $url)) {
                mkdir(FCPATH . $url, 0775, TRUE);
            }
            ini_set("pcre.backtrack_limit", "50000000");
            $html = $this->load->view("print/purchase_order", $data, true);
            $mpdf = new Mpdf(['tempDir' => FCPATH . '/tmp']);

            $footer = "<table name='footer' width=\"1000\">
           <tr>
             <td style='border-top: 1px solid black; width: 100%;font-size: 15px; padding-bottom: 20px;' align=\"center\">Email : mail@heksatex.co.id   Website: http://www.heksatex.co.id</td>
           </tr>
         </table>";

            $mpdf->WriteHTML($html);
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
                            ->setWheres(['status <>' => 'cancel',"no_po"=>$kode_decrypt])->getDataCountAll();

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
                            ->setWheres(['status <>' => 'cancel'])
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
            if (!$kode_decrypt) {
                throw new \Exception('', 500);
            }
            $rcv = new $this->m_po;
            $inshipment = $rcv->setTables('invoice')
                    ->setWheres(['no_po' => $kode_decrypt,"status <>"=>"cancel"])->setOrder(["order_date" => "asc"])
                    ->setJoins("mst_status","mst_status.kode = invoice.status","left")
                    ->setSelects(["invoice.*","coalesce(mst_status.nama_status,status) as status"])
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
