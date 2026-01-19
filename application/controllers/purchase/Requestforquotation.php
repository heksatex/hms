<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of order
 *
 * @author RONI
 */
class Requestforquotation extends MY_Controller {

    //put your code here

    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model('m_po');
        $this->load->model('m_user');
        $this->load->model('m_cfb');
        $this->load->model('m_produk');
        $this->load->model('_module');
        $this->load->model("m_konversiuom");
        $this->load->library("token");
        $this->load->model("m_global");
//        $this->config->load('additional');
    }

    public function index() {
        $data['id_dept'] = 'RFQ';
        $data['level'] = $this->session->userdata('nama')['level'] ?? "";
        $this->load->view('purchase/v_order', $data);
    }

    public function add() {
        $data['id_dept'] = 'RFQ';
        $data['jenis'] = 'RFQ';
        $this->load->view('purchase/v_order_add', $data);
    }

    public function edit($id) {
        try {
            $level = $this->session->userdata('nama')['level'] ?? "";
            $kode_decrypt = decrypt_url($id);
            $data['id'] = $id;
            $data['id_dept'] = 'RFQ';
            $model1 = new $this->m_po;
            $model2 = clone $model1;
            $model3 = clone $model2;
            $model4 = clone $model2;
            $data["setting"] = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            $data['user'] = (object) $this->session->userdata('nama');
            $model1->setTables("purchase_order po")
                    ->setJoins("partner p", "p.id = po.supplier")
                    ->setJoins("currency_kurs", "currency_kurs.id = po.currency", "left")
                    ->setJoins("currency", "currency.nama = currency_kurs.currency", "left")
                    ->setJoins("purchase_order_edited poe", "(poe.po_id = po.no_po and poe.status not in ('cancel','done'))", "left")
                    ->setSelects(["po.*", "p.nama as supp", "currency.symbol,currency.nama as curr_name", "poe.status as poe_status"])
                    ->setWheres(["po.no_po" => $kode_decrypt, "po.jenis" => "RFQ"]);
            if (strtolower($level) === "direksi") {
                $model1->setWhereRaw("(po.status in ('waiting_approval','exception') or poe.status in ('waiting_approve'))");
            } else {
                $model1->setWhereRaw("po.status in ('draft','rfq','waiting_approval','exception')");
            }
            $data["po"] = $model1->getDetail();
            if (!$data["po"]) {
                throw new \Exception('Data tidak ditemukan', 500);
            }
            $prd = ($_GET["produk"] ?? "");
            $stt = ($_GET["stt"] ?? '');
            $spl = ($_GET["supplier"] ?? '');
            $model1->setWheres(["po.id >" => $data["po"]->id, "jenis" => "rfq"], true)
                    ->setOrder(['po.create_date' => 'asc'])->setSelects(["po.no_po"]);
            if ($prd !== "") {
                $model1->setWhereRaw("po.no_po in (select po_no_po from purchase_order_detail where nama_produk LIKE '%{$prd}%')");
            }
            if ($stt !== "") {
                $model1->setWhereIn("po.status", explode(",", $stt));
            }
            if ($spl !== "") {
                $model1->setWheres(["po.supplier" => $spl]);
            }
            if (strtolower($level) === "direksi") {
                $model1->setWhereRaw("(po.status in ('waiting_approval','exception') or poe.status in ('waiting_approve'))");
            } else {
                $model1->setWhereRaw("po.status in ('draft','rfq','waiting_approval','exception')");
            }

            $nextPage = $model1->getDetail();
            if ($nextPage) {
                $data["next_page"] = base_url("purchase/requestforquotation/edit/" . encrypt_url($nextPage->no_po) . "?produk={$prd}&stt={$stt}&supplier={$spl}");
            }

            $model1->setWheres(["po.id <" => $data["po"]->id, "jenis" => "rfq"], true)
                    ->setOrder(['po.create_date' => 'desc'])->setSelects(["po.no_po"]);
            if ($prd !== "") {
                $model1->setWhereRaw("po.no_po in (select po_no_po from purchase_order_detail where nama_produk LIKE '%{$prd}%')");
            }
            if ($stt !== "") {
                $model1->setWhereIn("po.status", explode(",", $stt));
            }
            if ($spl !== "") {
                $model1->setWheres(["po.supplier" => $spl]);
            }

            if (strtolower($level) === "direksi") {
                $model1->setWhereRaw("(po.status in ('waiting_approval','exception') or poe.status in ('waiting_approve'))");
            } else {
                $model1->setWhereRaw("po.status in ('draft','rfq','waiting_approval','exception')");
            }

            $prevPage = $model1->getDetail();

            if ($prevPage) {
                $data["prev_page"] = base_url("purchase/requestforquotation/edit/" . encrypt_url($prevPage->no_po) . "?produk={$prd}&stt={$stt}&supplier={$spl}");
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

            $this->load->view('purchase/v_order_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function list_data() {
        try {
            $level = $this->session->userdata('nama')['level'] ?? "";
            $jenis = $this->input->post("jenis");
            $status = $this->input->post("status");
            $supplier = $this->input->post("supplier");
            $nama_produk = $this->input->post("nama_produk");
            $statuss = "";
            $data = array();
            $list = $this->m_po->setTables("purchase_order po")->setOrders([null, "no_po", "nama_supplier", "create_date", "order_date", "po.status"])
                    ->setSelects(["po.*", "p.nama as nama_supplier", "nama_status", "ck.currency as curr_kode", "coalesce(poe.status,'') as poe_status"])->setOrder(['create_date' => 'desc'])
                    ->setSearch(["p.nama", "no_po", "po.status", "note"])
                    ->setJoins("currency_kurs ck", "ck.id = po.currency", "left")
                    ->setJoins("partner p", "(p.id = po.supplier and p.supplier = 1)")
                    ->setJoins("mst_status", "mst_status.kode = po.status", "left")
                    ->setJoins("purchase_order_edited poe", "(poe.po_id = po.no_po and poe.status not in ('cancel','done'))", "left")
                    ->setWheres(["jenis" => $jenis]);
            if (strtolower($level) === "direksi") {
                $list->setWhereRaw("(po.status in ('waiting_approval','exception') or poe.status in ('waiting_approve'))");
            } else {
                if ($jenis !== "FPT")
                    $list->setWhereRaw("po.status in ('draft','rfq','waiting_approval','exception')");
                if (gettype($status) === 'string')
                    $list->setWhereRaw("po.status in ('draft','waiting_approval','exception')  or poe.status in ('waiting_approve') ");
                else
                if (count($status) > 0) {
                    $list->setWhereIn("po.status", $status);
                    $statuss = implode(",", $status);
                }
            }

            if ($supplier !== "")
                $list->setWheres(["po.supplier" => $supplier]);

            if ($nama_produk !== "")
                $list->setWhereRaw("po.no_po in (select po_no_po from purchase_order_detail where nama_produk LIKE '%{$nama_produk}%')");


            $no = $_POST['start'];
            $sub = (($jenis === "FPT") ? "fpt" : "requestforquotation");
            foreach ($list->getData() as $field) {
                $no++;
                $status = ($field->nama_status ?? $field->status);
                if (strtolower($status) === 'exception') {
                    $status .= " ({$field->poe_status})";
                }
                if (strtolower($level) === "direksi") {
                    if (strpos(strtolower($status), "waiting") !== false) {
                        
                    } else {
                        continue;
                    }
                    $poenc = encrypt_url($field->no_po);
                    $no = "{$field->id}|{$poenc}|{$field->status}|{$field->poe_status}";
                }

                $data [] = [
                    $no,
                    '<a href="' . base_url('purchase/' . $sub . '/edit/' . encrypt_url($field->no_po)) . '?produk=' . $nama_produk . '&stt=' . $statuss . '&supplier=' . $supplier . '">' . $field->no_po . '</a>',
                    $field->nama_supplier,
                    $field->create_date,
                    number_format($field->total, 4) . " " . ( ($field->total === null) ? "" : $field->curr_kode),
                    $status,
                    $field->note,
                    $field->poe_status
                ];
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

    public function save() {
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

        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $supp = $this->input->post("supplier");
            $prio = $this->input->post("prio");
            $order_date = $this->input->post("order_date");
            $note = $this->input->post("note");
            $cfb = $this->input->post("cfb");
            $kod_pro = $this->input->post("kod_pro");
            $nm_pro = $this->input->post("nm_pro");
            $qty = $this->input->post("qty");
            $uom = $this->input->post("uom");
            $jenis = $this->input->post("jenis");
            $harga = $this->input->post("harga");
            $id_cfb = $this->input->post("id_cfb");
            $uom_beli = $this->input->post("uom_beli");
            $qty_beli = $this->input->post("qty_beli");
            $id_konversiuom = $this->input->post("id_konversiuom");
            $cfb_manual = $this->input->post("cfb_manual");
            $novalue = $this->input->post("no_value") ?? "0";
            $warehouse = $this->input->post("warehouse");
            $reffNotes = $this->input->post("reff_note");
            $createDokumen = date("Y-m-d H:i:s");
            $nilai = $this->input->post("nilai");
            $schedule_date = $this->input->post("schedule_date");
            if (count($kod_pro) < 1) {
                throw new \Exception("Item Produk Belum dipilih", 500);
            }

            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
//
//            $tempKodPro = [];
//            foreach ($kod_pro as $key => $value) {
//                if (isset($tempKodPro[$value])) {
//                    throw new \Exception("Tidak Boleh Ada Produk Yang sama", 500);
//                }
//                $tempKodPro[$value] = "";
//            }
            $order_date .= " " . date("H:i:s");

            $this->_module->startTransaction();
            $this->_module->lock_tabel("user WRITE, main_menu_sub READ, log_history WRITE,mst_produk WRITE,token_increment WRITE,cfb WRITE,cfb_items WRITE,procurement_purchase_items WRITE,"
                    . "purchase_order_detail write,purchase_order write,nilai_konversi WRITE");
            if ($jenis === "RFQ") {
                $nv = "";
                if ($novalue === "1") {
                    $nv = "NV/";
                }
                if (!$nopo = $this->token->noUrut('purchase_order', date('y', strtotime($order_date)) . '/' . date('m', strtotime($order_date)), true)
                                ->generate("PO/{$nv}", '/%05d')->get()) {
                    throw new \Exception("No PO tidak terbuat", 500);
                }
            } else {
                if (!$nopo = $this->token->noUrut('purchase_order_fpt', date('y', strtotime($order_date)) . '/' . date('m', strtotime($order_date)), true)
                                ->generate('FPT/', '/%05d')->get()) {
                    throw new \Exception("No PO tidak terbuat", 500);
                }
            }

            $dataPO = ["no_po" => $nopo, 'supplier' => $supp, 'note' => $note, 'order_date' => $order_date, 'create_date' => $createDokumen, 'status' => 'draft', "jenis" => $jenis];
            $id_rfq = $this->m_po->save(array_merge($dataPO, ['cfb_manual' => ($cfb_manual ?? '0'), 'no_value' => $novalue]));
            if (is_null($id_rfq)) {
                throw new \Exception('Gagal Menyimpan ' . $jenis, 500);
            }
            $items = [];

            $kode_cfb = [];

            foreach ($cfb as $key => $value) {
                if ($qty_beli[$key] <= 0) {
                    $no = $key + 1;
                    throw new \Exception("Data Ke ({$no}) Qty Harus Lebih Dari 0", 500);
                }
                if (!isset($id_konversiuom[$key]) || is_null($id_konversiuom[$key]) || $id_konversiuom[$key] === "") {
                    $datakonversi = ["ke" => $uom[$key], "dari" => $uom[$key], "nilai" => 1];
                    $getDataKonv = $this->m_konversiuom->wheres($datakonversi)->getDetail();
                    $uom_beli[$key] = $uom[$key];
                    if (!$getDataKonv) {
                        $this->m_konversiuom->save(array_merge($datakonversi, ["catatan" => "1:1"]));
                        $getDataKonv = $this->m_konversiuom->wheres($datakonversi)->getDetail();
                        $id_konversiuom[$key] = $getDataKonv->id;
                    }
                    $id_konversiuom[$key] = $getDataKonv->id;
                }
                $v = explode(".", $value);
                $kode_cfb[] = $v[0] ?? 0;
                if ($qty[$key] === "") {
                    $qty[$key] = $qty_beli[$key] * ($nilai[$key] ?? 1);
                }
                $items [] = array(
                    "po_id" => $id_rfq,
                    "po_no_po" => $nopo,
                    "cfb_items_id" => $id_cfb[$key] ?? 0,
                    "kode_cfb" => $v[0] ?? 0,
                    "kode_produk" => html_entity_decode($kod_pro[$key]),
                    "nama_produk" => html_entity_decode($nm_pro[$key]),
                    "qty" => $qty[$key],
                    "uom" => $uom[$key],
                    "qty_beli" => $qty_beli[$key],
                    "uom_beli" => $uom_beli[$key],
                    'id_konversiuom' => $id_konversiuom[$key],
                    "pritoritas" => $prio[$key],
                    "status" => "draft",
                    "kode_pp" => $v[1] ?? 0,
                    'harga_per_uom_beli' => ($novalue === "0") ? ($harga[$key] ?? 0) : 0,
                    "created_at" => $createDokumen,
                    "deskripsi" => html_entity_decode($nm_pro[$key]),
                    "reff_note" => html_entity_decode($reffNotes[$key]),
                    "warehouse" => $warehouse[$key],
                    "schedule_date" => $schedule_date[$key] ?? ""
                );
                $updatePP = new $this->m_po;
                $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => ($v[1] ?? 0), "kode_produk" => $kod_pro[$key]])->update(["status" => "cfb"]);
            }
            if (count($id_cfb) > 0) {
                $checkItems = new $this->m_po;
                $dataItems = $checkItems->setTables("purchase_order_detail")->setWhereRaw("cfb_items_id in (" . implode(',', ($id_cfb ?? 0)) . ")")->setWheres(['status <>' => 'cancel'])->getDetail();
                if ($dataItems) {
                    throw new \Exception("Produk {$dataItems->kode_produk} - {$dataItems->nama_produk} dengan kode CFB {$dataItems->kode_cfb} sudah masuk {$jenis}", 500);
//                    throw new \Exception('Produk ' . $dataItems->kode_produk . ' - ' . $dataItems->nama_produk . ' Sudah Masuk ' . $jenis, 500);
                }
            }
            $this->m_po->setTables("purchase_order_detail")->saveBatch($items);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            unset($dataPO['supplier']);
            $this->_module->gen_history(($jenis === "RFQ" ? $sub_menu : "FPT"), $nopo, 'create', logArrayToString('; ', array_merge($dataPO, ["kode_cfb" => implode(",", $kode_cfb)])), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success',
                        'url' => base_url('purchase/' . ($jenis === "RFQ" ? "requestforquotation" : "fpt") . '/edit/' . encrypt_url($nopo)))));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
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
            $username = $this->session->userdata('username');

            $kode_decrypt = decrypt_url($id);
            $ids = $this->input->post("ids");

            $model = new $this->m_global;
            $model1 = clone $model;
            $model2 = clone $model;

            $check = $model->setTables("purchase_order_detail")->setWheres(["id" => $ids, "po_no_po" => $kode_decrypt])->getDetail();
            if (!$check) {
                throw new \Exception('Gagal hapus item', 500);
            }
            $this->_module->startTransaction();

            $model->delete();
            $spltkodePP = explode("_", $check->kode_cfb);
            if (isset($spltkodePP[1])) {
                $model1->setTables("cfb_items")->setWheres(["cfb_items.id" => $check->cfb_items_id])->update(["status" => "confirm"]);
                $model2->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $spltkodePP[1], "kode_produk" => $check->kode_produk])->update(["status" => "generated"]);
            }
            $datas = $model->setWheres(["purchase_order_detail.status" => "draft", "po_no_po" => $kode_decrypt], true)
//                            ->setJoins("purchase_order", "purchase_order_detail.po_id = purchase_order.id")
                            ->setJoins("tax", "tax_id = tax.id", "left")
                            ->setSelects(["purchase_order_detail.*", "coalesce(tax.amount,0) as amount_tax,tax.dpp as dpp_tax", "coalesce(tax.tax_lain_id,0) as tax_lain_id"])->getData();
            $totals = 0.00;
            $diskons = 0.00;
            $taxes = 0.00;
            $nilaiDppLain = 0;
            $model3 = new $this->m_global;
            $models = clone $model3;
            $setDpp = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
            $models->setTables("tax");
            foreach ($datas as $key => $value) {
                $total = ($value->qty_beli * $value->harga_per_uom_beli);
                $diskon = ($value->diskon ?? 0);
                $totals += $total;
                $diskons += $diskon;
                $taxe = 0;
                if ($setDpp !== null && $value->dpp_tax === "1") {
                    $taxe += ((($total - $diskon) * 11) / 12) * $value->amount_tax;
                    $nilaiDppLain += ((($total - $diskon) * 11) / 12);
                } else {
                    $taxe += ($total - $diskon) * $value->amount_tax;
                }

                if ($value->tax_lain_id !== "0") {
                    $dataTax = $models->setWhereIn("id", explode(",", $value->tax_lain_id), true)->setSelects(["amount,dpp"])->setOrder(["id"])->getData();
                    foreach ($dataTax as $kkk => $data) {
                        if ($setDpp !== null && $data->dpp === "1") {
                            $taxe += ((($total - $diskon) * 11) / 12) * $data->amount;
                            continue;
                            $taxe += ($total - $diskon) * $data->amount;
                            continue;
                        }
                        $taxe += ($total - $diskon) * $data->amount;
                    }
                }

                $taxes += $taxe;
            }
            $grandTotal = ($totals - $diskons) + $taxes;
            $po = new $this->m_po;
            $update = ["total" => $grandTotal, 'dpp_lain' => $nilaiDppLain];
            $po->setWheres(["no_po" => $kode_decrypt])->update($update);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update Data', 500);
            }
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', "Hapus Item dengan kode CFB {$check->kode_cfb}", $username);
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

    public function update($id) {
        $validation = [
            [
                'field' => 'qty_beli[]',
                'label' => 'Qty',
                'rules' => ['required', 'regex_match[/^\d*\.?\d*$/]'],
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
            $noVal = $this->input->post("no_value");
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
                if ($noVal === "0") {
                    $checkKonversi = $this->m_konversiuom->wheres(["id" => $id_konversiuom[$key], "ke" => $uom_jual[$key], "dari" => $uom_beli[$key]])->getDetail();
                    if (!$checkKonversi) {
                        throw new \Exception("<strong>Data No {$no}, Uom dan Uom Beli Tidak ada dalam tabel konversi</strong>", 500);
                    }
                } else {
                    $value = 0;
                    $diskon[$key] = 0;
                    $tax[$key] = null;
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
                            $nilai_dpp += ((($total - $diskon) * 11) / 12);
                            $taxe += ((($total - $diskon) * 11) / 12) * $datas->amount;
                        } else {
                            $taxe += ($total - $diskon) * $datas->amount;
                        }
                    }
                }
                $taxes += $taxe;
                $total = ($total - $diskon) + $taxe;
                $nilaiDppLain += $nilai_dpp;
                $log_update ["item ke " . $no] = logArrayToString(";", ['harga' => $value, 'uom beli' => $uom_beli[$key],
                    'diskon' => $dsk[$key], 'Pajak' => ($amount_tax[$key] ?? 0) * 100, 'deskripsi' => html_entity_decode($deskripsi[$key]),
                    "diskon" => $dsk[$key]]);
                $data[] = ['id' => $key, 'harga_per_uom_beli' => $value, 'uom_beli' => $uom_beli[$key], 'deskripsi' => html_entity_decode($deskripsi[$key]),
                    'tax_id' => $tax[$key], 'diskon' => $dsk[$key], 'id_konversiuom' => $id_konversiuom[$key], "pajak" => $taxe, "total" => $total, "nilai_dpp" => $nilai_dpp];
            }
//            if ($dpplain === "1") {
//                $nilaiDppLain = (($totals - $diskons) * 11) / 12;
//            }
            $grandTotal = ($totals - $diskons) + $taxes;
            $this->_module->startTransaction();
            $this->_module->lock_tabel("user WRITE, main_menu_sub READ, log_history WRITE,mst_produk WRITE,"
                    . "purchase_order_detail write,purchase_order write");
            $this->m_po->setTables("purchase_order_detail")->updateBatch($data, 'id');
            $po = new $this->m_po;
            $update = ["currency" => $currency, "nilai_currency" => $nilai_currency, 'note' => $note,
                "no_value" => $noVal, "total" => $grandTotal, 'dpp_lain' => $nilaiDppLain, "order_date" => $order_date, 'foot_note' => $foot_note, 'supplier' => $supplier];
            $po->setWheres(["no_po" => $kode_decrypt])->update($update);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal update Data', 500);
            }
            $this->_module->gen_history($sub_menu, $kode_decrypt, 'edit', "Header -> " . logArrayToString('; ', $update, " : ") . "<br> Detail -> " . logArrayToString('; ', $log_update, " : "), $username);
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

    public function update_status_exception($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
//            $users = $this->session->userdata('nama');
            $kode_decrypt = decrypt_url($id);
            $checkData = new $this->m_global;
            if ($checkData->setTables("purchase_order_edited")->setWhereRaw("po_id = '{$kode_decrypt}' and status not in ('cancel','done')")->getDetail() === null) {
                throw new \Exception('Data tidak ditemukan', 500);
            }
            $checkData->update(["status" => "approve"]);
            $this->_module->gen_history("purchaseorder", $kode_decrypt, 'edit', "Appoved Perubahan Harga", $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function update_status($id) {
        try {
            $listStatus = [
                "confirm_order" => "waiting_approval",
                'approval' => 'purchase_confirmed',
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
                            ->setJoins("currency_kurs", "currency_kurs.id = purchase_order.currency", "left")
                            ->setSelects(["purchase_order.*", "partner.nama as nama_supp", "currency_kurs.currency as curr_name"])->getDetail();
            if (!$data) {
                throw new \Exception('Data RFQ tidak ditemukan', 500);
            }
            if ($status !== "cancel") {
                if ($data->currency === null) {
                    throw new \Exception('Mata Uang Belum diperbaharui ', 500);
                }
            }

            if ($data->no_value === "1") {
                $status = "approval";
            }

            $this->_module->startTransaction();
            $lockTable = "user WRITE, main_menu_sub READ, log_history WRITE,mst_produk WRITE,cfb_items write,cfb write,procurement_purchase_items write,"
                    . "purchase_order_detail write,purchase_order write,setting WRITE";
//            if ($status === 'approval' || $status === 'confirm_rfq') {
            $lockTable .= ",penerimaan_barang WRITE,penerimaan_barang_items WRITE,stock_move_produk WRITE,stock_move WRITE,nilai_konversi nk WRITE";
            $lockTable .= ",mst_produk_coa WRITE";
//            }
            $this->_module->lock_tabel($lockTable);
            switch ($status) {
                case "confirm_order":
                    $podd = new $this->m_po;
                    $checkPod = clone $podd;

                    $checkPod_data = $checkPod->setTables("purchase_order_detail")->setWheres(["po_no_po" => $kode_decrypt, "status <>" => "cancel"])
                                    ->setWhereRaw("(harga_per_uom_beli < 0 or qty_beli <= 0)")->getDetail();
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
                            $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "rfq"]);
                        }
                    }
                    $totals = $this->input->post("totals");
                    //tambahan
                    if ($totals < 1) {
                        throw new \Exception('Harga belum ditentukan', 500);
                    }
                    $getSetting = new m_global;
                    $defautTotals = $getSetting->setTables("setting")->setWheres(["setting_name" => "limit_approve_{$data->curr_name}", "status" => 1])->setSelects(["value"])->getDetail();
                    $defautTotal = (int) ($defautTotals->value ?? 0);

                    if ($totals < $defautTotal) {
                        $listStatus[$status] = "purchase_confirmed";
                    }
                    break;

                case "confirm_rfq":
                    break;
                case "approval":

                    break;

                case "done":
                    break;
                case "cancel":

                    $podd = new $this->m_po;
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
                            $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "generated"]);
                        }
                    }
                    break;
                default:
                    break;
            }
            $updatePO = ["status" => $listStatus[$status]];
            if ($listStatus[$status] === "purchase_confirmed") {
                $redirect = $id;
                $orderDate = date("Y-m-d H:i:s");
                $listCfb = new $this->m_po;
                $dataItemOrder = $listCfb->setTables('purchase_order_detail')
                                ->setJoins("mst_produk_coa", "mst_produk_coa.kode_produk = purchase_order_detail.kode_produk", "left")
                                ->setJoins('mst_produk', "purchase_order_detail.kode_produk = mst_produk.kode_produk", "left")
                                ->setJoins('nilai_konversi nk', "id_konversiuom = nk.id", "left")
//                                ->setJoins('nilai_konversi nk', "id_konversiuom = mst_produk.uom_beli", "left")
                                ->setWheres(["po_no_po" => $kode_decrypt, "status <>" => "cancel"])->setOrder(["id" => "asc"])
                                ->setSelects(["purchase_order_detail.*", "nk.nilai", "kode_coa", "mst_produk.uom as uom_stock",
                                    "konversi_aktif", "pembilang", "penyebut"])->getData();
                $produk = [];
                $inserInvoice = new $this->m_global;
                $checkInvoice = clone $inserInvoice;
                if ($checkInvoice->setTables("penerimaan_barang")->setWheres(["origin" => $kode_decrypt, 'dept_id' => "RCV", 'status <>' => 'cancel'])->getDataCountAll() > 0) {
                    throw new \Exception("No {$kode_decrypt} sudah terbentuk Dokumen Penerimaan", 500);
                }
                foreach ($dataItemOrder as $key => $value) {
                    $row = count($produk) + 1;
                    $updateDataDetail[] = ['id' => $value->id, 'status' => $status, 'nilai_konversi' => $value->nilai];
                    if ($value->konversi_aktif === "1") {
                        $qtyy = ($value->qty_beli / $value->pembilang) * $value->penyebut;
                    } else {
                        $qtyy = $value->qty_beli * $value->nilai;
                    }
                    $produk[] = [
                        'nama_produk' => $value->nama_produk,
                        'kode_produk' => $value->kode_produk,
                        'qty' => $qtyy,
                        'uom' => $value->uom_stock,
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

                    if ($data->cfb_manual === "0") {
                        $updatePP = new $this->m_po;
                        $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => $value->kode_pp, "kode_produk" => $value->kode_produk])->update(["status" => "po"]);
                    }
                }
                $method_dept = "RCV";
                $kode_ = $this->_module->get_kode_penerimaan($method_dept);
                $get_kode_in = $kode_;

                $dgt = substr("00000" . $get_kode_in, -5);
                $kodeRcv = $method_dept . "/" . "IN" . "/" . date("y") . date("m") . $dgt;

                $sm = "SM" . $this->_module->get_kode_stock_move();
                $insertPenerimaan = new $this->m_po;
                $insertPenerimaanDetail = clone $insertPenerimaan;
                $dataPenerimaan = [
                    'kode' => $kodeRcv,
                    'tanggal' => date("Y-m-d H:i:s"),
                    'tanggal_transaksi' => date("Y-m-d H:i:s"),
                    'tanggal_jt' => date("Y-m-d H:i:s"),
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

                $smdata = "('" . $sm . "','" . date("Y-m-d H:i:s") . "','" . $kodeRcv . "|1','RCV|IN','SUP/Stock','RCV/Stock','ready','1','')";
                $this->_module->create_stock_move_batch($smdata);
                $insertSMProduk = new $this->m_global;
                $insertSMProduk->setTables('stock_move_produk')->saveBatch($smProduk);
                $this->_module->gen_history('penerimaanbarang', $kodeRcv, 'create', logArrayToString(";", $dataPenerimaan), $username);
                if ($data->order_date === null || $data->order_date === "0000-00-00 00:00:00") {
                    $updatePO = array_merge($updatePO, ["order_date" => $orderDate]);
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
            $this->output->set_status_header(($ex->getCode() ?? 500))
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
            $model = new $this->m_global;
            $warehouse = $model->setTables("departemen")->setSelects(["kode", "nama"])->setOrder(["nama"])->setWheres(["type_dept" => "gudang", "show_dept" => true])->getData();
//            $data['warehouse'] = $this->_module->get_list_departement();
            $data['warehouse'] = $warehouse;
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

    public function tambahkan_item() {
        try {
            $id = $this->input->post("id");
            $html = $this->load->view("purchase/v_order_add_item_modal", ["id" => $id], true);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', "data" => $html)));
        } catch (Exception $ex) {
            
        }
    }

    public function add_item_rfq($id) {
        try {
            $sub_menu = $this->uri->segment(2);
            $users = $this->session->userdata('nama');
            $kode_decrypt = decrypt_url($id);
            $datas = $this->input->post("data");
            $dd = json_decode($datas);
            $model = new $this->m_global;
            $model2 = clone $model;
            $model3 = clone $model;

            $checkPO = $model->setTables("purchase_order")->setWheres(["no_po" => $kode_decrypt, "status" => "draft"])->getDetail();
            if (!$checkPO) {
                throw new \Exception('Data PO tidak ada atau tidak dalam status draft', 500);
            }
            $kodes = $this->_module->get_kode_sub_menu($sub_menu)->row_array();

            $this->_module->startTransaction();
            $this->_module->lock_tabel("mst_produk WRITE,cfb_items WRITE,mst_produk_harga mph read,nilai_konversi nk read,"
                    . "nilai_konversi read,log_history WRITE,main_menu_sub READ,purchase_order WRITE,purchase_order_detail WRITE,procurement_purchase_items WRITE");

            $checkPOD = $model3->setTables("purchase_order_detail")->setWhereIn("cfb_items_id", $dd->ids)->setWheres(["status <>" => 'cancel'])->getDetail();
            if ($checkPOD) {
                throw new \Exception("Kode CFB <strong>{$checkPOD->kode_cfb}</strong> sudah masuk pada PO / FPT {$checkPOD->po_no_po}", 500);
            }

            $checkStatus = $model2->setTables("cfb_items")->setSelects(["GROUP_CONCAT(id) as ids,GROUP_CONCAT(kode_cfb) as kode"])->setWhereIn("id", $dd->ids)->setWheres(["status" => "draft"])->getDetail();
            $listLog = [];
            if ($checkStatus) {
                $model2->setWhereIn("id", explode(",", $checkStatus->ids), true)->setWheres(["status <>" => "cancel"], true)->update(["status" => "confirm"]);
                foreach (explode(",", $checkStatus->kode) as $key => $value) {
                    $listLog[] = ["datelog" => date("Y-m-d H:i:s"), "kode" => $value, "main_menu_sub_kode" => ($kodes["kode"] ?? ""),
                        "jenis_log" => "edit", "note" => "Merubah Ke status confirm", "nama_user" => $users["nama"], "ip_address" => ""];
                }
                if (count($listLog) > 0) {
                    $model2->setTables("log_history")->saveBatch($listLog);
                }
            }
            $produk = $this->m_cfb->setTables("mst_produk")->setSelects(["mst_produk.kode_produk,nama_produk,uom_beli,mph.harga,nilai,dari,catatan,uom"])
                    ->setJoins("mst_produk_harga mph", "(mph.kode_produk = mst_produk.kode_produk and mph.jenis = 'pembelian')", "left")
                    ->setJoins("nilai_konversi nk", "nk.id = uom_beli", "left");
            $items = [];
            $temid = [];

//            $listLog = [];
            foreach ($dd->data as $key => $value) {
                $datas_ = explode("|^", $value);
                if (isset($temid[$datas_[0]])) {
                    continue;
                }
                if (count($datas_) > 1) {
                    $prod = $produk->setWheres(["mst_produk.kode_produk" => $datas_[2]], true)->getDetail();
                    if ($prod) {
                        if (is_null($prod->uom_beli) || ($prod->uom !== $datas_[4])) {
                            $dari = $datas_[4];
                            $nilai = 1;
                            $ke = $datas_[4];
                            $catatan = "1:1";
                            $datakonversi = ["ke" => $ke, "dari" => $dari, "nilai" => $nilai];
                            $getDataKonv = $this->m_konversiuom->wheres($datakonversi)->getDetail();
                            if (!$getDataKonv) {
                                $this->m_konversiuom->save(array_merge($datakonversi, ["catatan" => $catatan]));
                                $getDataKonv = $this->m_konversiuom->wheres($datakonversi)->getDetail();
                                $uom_beli = $getDataKonv->id;
                            } else {
                                $uom_beli = $getDataKonv->id;
                                $dari = $getDataKonv->dari;
                                $nilai = $getDataKonv->nilai;
                                $catatan = $getDataKonv->catatan;
                            }
                        } else {
                            $uom_beli = $prod->uom_beli;
                            $dari = $prod->dari;
                            $nilai = ($prod->nilai ?? 1);
                            $catatan = $prod->catatan;
                        }
                        $kode_cfbs = explode(".", $datas_[1]);
                        $qtyBeli = $datas_[3] / $nilai;
                        $item = array(
                            "po_id" => $checkPO->id,
                            "po_no_po" => $checkPO->no_po,
                            "cfb_items_id" => $datas_[0] ?? 0,
                            "kode_cfb" => ($kode_cfbs[0] ?? 0),
                            "kode_produk" => html_entity_decode($datas_[2]),
                            "nama_produk" => html_entity_decode($prod->nama_produk),
                            "qty" => $datas_[3],
                            "uom" => $datas_[4],
                            "qty_beli" => $qtyBeli,
                            "uom_beli" => $dari,
                            'id_konversiuom' => $uom_beli,
                            "pritoritas" => $datas_[5],
                            "status" => "draft",
                            "kode_pp" => ($kode_cfbs[1] ?? 0),
                            'harga_per_uom_beli' => 0,
                            "created_at" => date("Y-m-d H:i:s"),
                            "deskripsi" => html_entity_decode($prod->nama_produk),
                            "reff_note" => html_entity_decode($datas_[6]),
                            "warehouse" => $datas_[7],
                            "schedule_date" => $datas_[8] ?? ""
                        );
                        $items[] = $item;
                        $updatePP = new $this->m_po;
                        $updatePP->setTables("procurement_purchase_items")->setWheres(["kode_pp" => ($kode_cfbs[1] ?? 0), "kode_produk" => $datas_[2]])->update(["status" => "cfb"]);

                        $listLog[] = ["datelog" => date("Y-m-d H:i:s"), "kode" => $checkPO->no_po, "main_menu_sub_kode" => $sub_menu,
                            "jenis_log" => "edit", "note" => "Menambal Item " . logArrayToString(":", $item), "nama_user" => $users["nama"], "ip_address" => ""];
                    }
                }
            }
            if (count($items) > 0) {
                $model3->saveBatch($items);
                $model2->setTables("log_history")->saveBatch($listLog);
            }
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

    public function get_kurs() {
        try {
            $kurs = $this->m_po->setTables("currency_kurs")->setOrder(["id" => "asc"])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($kurs));
        } catch (Exception $ex) {
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode([]));
        }
    }

    public function get_tax() {
        try {
            $kurs = $this->m_po->setTables("tax")->setWheres(["type_inv" => "purchase"])->setOrder(["id" => "asc"])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($kurs));
        } catch (Exception $ex) {
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode([]));
        }
    }

    public function get_supp() {
        $search = $this->input->get("search");
        $where = ["supplier" => 1];
        if ($search !== "") {
            $where = array_merge($where, ["nama LIKE" => '%' . $search . '%']);
        }
        $datas = $this->m_po->getSuppliers($where);
        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['data' => $datas]));
    }

    public function get_produk() {
        $search = $this->input->get("search");
        $datas = new $this->m_global;
        if ($search !== "") {
//            $datas = $datas->setWhereRaw("kode_produk LIKE '%{$search}%' or nama_produk LIKE '%{$search}%'");
            $_POST['search']['value'] = $search;
        }
        $_POST['length'] = 50;
        $_POST['start'] = 0;
        $datas = $datas->setTables("mst_produk")->setSelects(["kode_produk,nama_produk,uom"])
                        ->setSearch(["kode_produk", "nama_produk"])
                        ->setJoins("mst_category", "id_category = mst_category.id")
                        ->setJoins("departemen", "(departemen.kode = mst_category.dept_id and departemen.type_dept='gudang')")
                        ->setJoins("nilai_konversi nk", "nk.id = uom_beli", "left")
                        ->setGroups(["mst_produk.kode_produk"])
                        ->setSelects(["mst_produk.*", "coalesce(dari,'') as dari,nk.id as dari_id,coalesce(nilai,'1') as nilai"])
                        ->setOrder(["kode_produk" => "asc"])->setWheres(["status_produk" => "t"])->getData();
        $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(['data' => $datas]));
    }

//    public function hitung_total_detail() {
//        try {
//            $model = new $this->m_global;
//            $model2 = clone $model;
//            $model3 = clone $model;
//            $setDpp = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
//            $datas = $model->setTables("purchase_order_detail")->setJoins("tax", "tax_id = tax.id", "left")
//                            ->setSelects(["purchase_order_detail.*", "coalesce(tax.amount,0) as amount_tax,tax.dpp as dpp_tax"])->getData();
//            $update = [];
//            foreach ($datas as $key => $value) {
//                $total = ($value->qty_beli * $value->harga_per_uom_beli);
//                $diskon = ($value->diskon ?? 0);
//                $taxes = 0;
//                $nilaiDppLain = 0;
//                if ($setDpp !== null) {
//                    $taxes += ((($total - $diskon) * 11) / 12) * $value->amount_tax;
//                    $nilaiDppLain += ((($total - $diskon) * 11) / 12);
//                } else {
//                    $taxes += ($total - $diskon) * $value->amount_tax;
//                }
//                $total = ($total - $diskon) + $taxes;
//                $update[] = ["id" => $value->id, "pajak" => $taxes, "nilai_dpp" => $nilaiDppLain, "total" => $total];
//            }
//            if (count($update) > 0) {
//                $model2->setTables("purchase_order_detail")->updateBatch($update, "id");
//            }
//        } catch (Exception $ex) {
//            
//        }
//    }
//    public function hitung_total() {
//        try {
//            $model = new $this->m_global;
//            $model2 = clone $model;
//            $model3 = clone $model;
//            $setDpp = $model3->setTables("setting")->setWheres(["setting_name" => "dpp_lain", "status" => "1"])->setSelects(["value"])->getDetail();
//
//            $headers = $model->setTables("purchase_order")->getData();
//            foreach ($headers as $key => $values) {
//                $model2 = new $this->m_global;
//                $totals = 0.00;
//                $diskons = 0.00;
//                $taxes = 0.00;
//                $nilaiDppLain = 0;
//                $body = $model2->setTables("purchase_order_detail")->setWheres(["po_id" => $values->id], true)
//                                ->setJoins("tax", "tax_id = tax.id", "left")
//                                ->setSelects(["purchase_order_detail.*", "coalesce(tax.amount,0) as amount_tax,tax.dpp as dpp_tax"])->getData();
//                foreach ($body as $key => $value) {
//                    $total = ($value->qty_beli * $value->harga_per_uom_beli);
//                    $diskon = ($value->diskon ?? 0);
//                    $totals += $total;
//                    $diskons += $diskon;
//                    if ($setDpp !== null) {
//                        $taxes += ((($total - $diskon) * 11) / 12) * $value->amount_tax;
//                        $nilaiDppLain += ((($total - $diskon) * 11) / 12);
//                    } else {
//                        $taxes += ($total - $diskon) * $value->amount_tax;
//                    }
//                }
//                $grandTotal = ($totals - $diskons) + $taxes;
//                $model->setWheres(["id" => $values->id], true)->update(["total" => $grandTotal, "dpp_lain" => $nilaiDppLain]);
//            }
//        } catch (Exception $ex) {
//            
//        }
//    }
}
