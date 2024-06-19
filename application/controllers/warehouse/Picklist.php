<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class Picklist extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_Picklist");
        $this->load->model("m_deliveryorder");
        $this->load->model("m_PicklistDetail");
        $this->load->library("token");
        $this->load->model("m_Pickliststockquant");
        $this->load->library('prints');
        $this->load->library("wa_message");
        $this->load->model("m_user");
    }

    protected $val_form = array(
        [
            'field' => 'bulk',
            'label' => 'Type Bulk',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
        [
            'field' => 'sales',
            'label' => 'Sales',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus dipilih'
            ]
        ],
        [
            'field' => 'jenis_jual',
            'label' => 'Jenis Jual',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{label} Harus dipilih'
            ]
        ],
        [
            'field' => 'customer',
            'label' => 'Customer',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{label} Harus dipilih'
            ]
        ]
    );

    public function index() {
        $data['id_dept'] = 'PL';
        $this->load->view('warehouse/v_picklist', $data);
    }

    public function add() {
        $data['id_dept'] = 'PL';
        $data['bulk'] = $this->m_Picklist->getTypeBulk();
        $data['sales'] = $this->m_Picklist->getSales();
        $this->load->view('warehouse/v_picklist_add', $data);
    }

    public function get_cust() {
        $search = $this->input->post('search') ?? '';
        $data = $this->m_Picklist->getCustomer($search);
        echo json_encode($data);
    }

    public function edit($id = null) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
//            $sub_menu = $this->uri->segment(2);
//            $data["mms"] = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
            $data['id_dept'] = 'PL';
            $data["ids"] = $id;
            $data['picklist'] = $this->m_Picklist->getDataByID(['picklist.no' => $kode_decrypt], "DO", "count_detail");
            $data['bulk'] = $this->m_Picklist->getTypeBulk();
            $data['sales'] = $this->m_Picklist->getSales();
            $data['do'] = $this->m_deliveryorder->getDataDetail(['no_picklist' => $kode_decrypt]);
            $this->load->view('warehouse/v_picklist_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function get_total() {
        try {
            $no = $this->input->post("pl");
            $data = $this->m_Picklist->getDataByID(['picklist.no' => $no], "", "count_detail");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode($data));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode([[]]));
        }
    }

    public function data() {
        try {
            $data = array();
            $list = $this->m_Picklist->getData(false, [], ["count_detail"]);
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->no);
                $no++;
                $row = array(
                    $no,
                    '<a href="' . base_url('warehouse/picklist/edit/' . $kode_encrypt) . '">' . $field->no . '</a>',
                    $field->nama,
                    $field->tanggal_input,
                    $field->jenis_jual,
                    $field->bulk_nama,
                    $field->sales_nama,
                    $field->keterangan,
                    $field->status,
                    $field->pcs_qty ?? 0,
                    $field->tot_qty ?? 0,
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_Picklist->getCountAllData(),
                "recordsFiltered" => $this->m_Picklist->getCountDataFiltered(),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
        }
    }

    public function item_manual() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $data["pl"] = $this->input->post("pl");
            $data["ids"] = $this->input->post("ids");
            $data['sales'] = $this->m_Picklist->getSales();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $this->load->view('modal/v_picklist_item_manual_modal', $data, true)]));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function item_scan() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $data["pl"] = $this->input->post("pl");
            $data["ids"] = $this->input->post("ids");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $this->load->view('modal/v_picklist_item_scan_modal', $data, true)]));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function add_list_item() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $data = array();
            $condition = [];
            $in = [];
            $marketing = $this->input->post('marketing');
            $form = $this->input->post("form") ?? "";
//            log_message('error', json_encode($form));
            if ($this->input->post('filter') !== "" && $_POST["search"]["value"] !== "") {
                $condition = array_merge($condition, ['stock_quant.' . $this->input->post('filter') . " LIKE" => '%' . $_POST["search"]["value"] . '%']);
            }
            if (!is_string($marketing)) {
                if (trim($marketing[0]) !== "")
                    $in = array_merge($in, ['`stock_quant`.`sales_group`' => $marketing]);
            }
            $keyFilter = "";
            $valSearch = "";
            if (!is_string($form)) {
                foreach ($form as $values) {
                    if (!is_array($values)) {
                        continue;
                    }
                    foreach ($values as $value) {
                        if (is_array($value)) {
                            foreach ($values as $key => $filter) {
                                if (key($filter) === "filter") {
                                    $keyFilter = "stock_quant." . $filter[key($filter)] . ' LIKE';
                                } else if (key($filter) === "search") {
                                    $valSearch = "%" . $filter[key($filter)] . '%';
                                } else if (key($filter) === "remove") {
                                    $keyFilter = "";
                                    $valSearch = "";
                                }
                            }
                            if ($keyFilter !== "" && $valSearch !== "") {
                                $condition = array_merge($condition, [$keyFilter => $valSearch]);
                                $keyFilter = "";
                                $valSearch = "";
                            }
                        }
                    }
                }
            }
//            log_message('error', json_encode($condition));
            $list = $this->m_Pickliststockquant->getDataItemPicklist($condition, $in, true);
            $no = $_POST['start'];
            foreach ($list as $field) {
                $field->status = 'draft';
                $no++;
                $button = is_null($field->no_pl) ? "<button class='btn btn-success btn-sm status_item' data-quantid ='" . $field->quant_id . "' data-id='" . $field->barcode . "' data-pl='" . $field->no_pl . "'><fa class='fa fa-plus'></fa></button>" : '';
                $row = array(
                    $field->quant_id . '-' . $field->barcode . '-' . $field->no_pl,
                    $field->barcode,
                    $field->kode_produk,
                    $field->nama_produk,
                    $field->corak_remark,
                    $field->warna_remark,
                    $field->qty_jual . " " . $field->uom_jual,
                    $field->qty2_jual . " " . $field->uom2_jual,
                    $field->lokasi_fisik,
                    $field->lebar_jadi . ' ' . (($field->lebar_jadi === "-") ? "" : $field->uom_lebar_jadi),
                    null, //json_encode($field),
                    $field->no_pl,
                    $button
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_Pickliststockquant->count_all($condition, $in),
                "recordsFiltered" => $this->m_Pickliststockquant->count_filteredItemPicklist($condition, $in),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function add_list_item_scan() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $barcode = $this->input->post('search');
            $condition = ["lot" => $barcode];
            $list = $this->m_Pickliststockquant->getDataItemPicklistScan(array_merge($condition, ['stock_quant.lokasi' => 'GJD/Stock', 'id_category' => 21]), true);
            if (!is_null($list)) {
                if (strlen($list->no_pl) > 4) {
                    throw new \Exception('Barcode ' . $list->barcode . ' sudah ada pada ' . $list->no_pl, 500);
                }
            } else {
                $list = $this->m_Pickliststockquant->getDataItemPicklistScanDetail($condition, true);
                if (empty($list)) {
                    throw new \Exception('Barcode Tidak ditemukan', 500);
                }
                switch (true) {

                    case (int) $list->id_category !== 21:
                        throw new \Exception("Kategori Produk Tidak Valid (" . $list->nama_category . ")", 500);
                    case $list->reserve_move !== "":
                        throw new \Exception("Barcode " . $barcode . " reserve move " . $list->reserve_move, 500);

                    case in_array(strtoupper($list->lokasi_fisik), ["PORT", "XPD"]) :
                        throw new \Exception("Lokasi Tidak Valid (" . $list->lokasi_fisik . ")", 500);

                    case strtoupper($list->lokasi) !== 'GJD/STOCK':
                        throw new \Exception("Lokasi Tidak Valid (" . $list->lokasi . ")", 500);
                    default :
                        throw new \Exception('Barcode Tidak ditemukan', 500);
                }
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Barcode ditemukan', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => [$list])));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function list_item($page = null) {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $pl = $this->input->post('filter');
            $condition = ["no_pl" => $pl, 'valid !=' => 'cancel'];
            $list = $this->m_PicklistDetail->getData($condition);
            $no = $_POST['start'];
            $data = array();

            foreach ($list as $field) {
//                $status = (strcasecmp($field->valid, 'draft') == 0) ? "<button class='btn btn-danger status_item' data-id='" . $field->barcode_id . "' data-pl='" . $field->no_pl . "'><fa class='fa fa-trash'></fa></button>" : '';
                $status = "";
                $no++;
                switch (true) {
                    case ($field->dodstatus != 'done'):
                        $status = "<button class='btn btn-danger btn-sm status_item' data-status='" . $field->valid . "' data-id='" . $field->barcode_id . "' data-pl='" . $field->no_pl . "'><fa class='fa fa-trash'></fa></button>";
                        break;
                    case (is_null($field->dod)):
                        $status = "<button class='btn btn-danger btn-sm status_item' data-status='" . $field->valid . "' data-id='" . $field->barcode_id . "' data-pl='" . $field->no_pl . "'><fa class='fa fa-trash'></fa></button>";
                        break;
                    default:
//                        $status = "<button class='btn btn-danger btn-sm status_item' data-status='" . $field->valid . "' data-id='" . $field->barcode_id . "' data-pl='" . $field->no_pl . "'><fa class='fa fa-trash'></fa></button>";
                        break;
                }
                $row = array(
                    $no,
                    $field->barcode_id,
                    $field->corak_remark,
                    $field->warna_remark,
                    $field->qty . " " . $field->uom,
                    $field->qty2 . " " . $field->uom2,
                    $field->lokasi_fisik,
                    $field->lebar_jadi . ' ' . (($field->lebar_jadi === "-") ? "" : $field->uom_lebar_jadi),
                    $field->valid,
                    $field->valid_date,
                    $status
//                    (!is_null($field->bulk) ? "" : (!is_null($field->dod) ? "" : $status))
                );
                $data[] = $row;
            }
            echo json_encode(
                    array("draw" => $_POST['draw'],
                        "recordsTotal" => $this->m_PicklistDetail->getCountAllData($condition),
                        "recordsFiltered" => $this->m_PicklistDetail->getCountDataFiltered($condition),
                        "data" => $data,
                    )
            );
            exit();
        } catch (Exception $exc) {
            
        }
    }

    public function add_item() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $nama = $this->session->userdata('nama');
            $pl = $this->input->post('pl');
            $datas = $this->input->post('item');
            if (count($datas) < 1) {
                
            }
//            $ids = $this->input->post("ids");
            $data = json_decode($datas);
            $this->_module->startTransaction();
            $this->_module->lock_tabel("picklist_detail WRITE,picklist_detail pd READ,stock_quant READ,stock_quant sq WRITE,partner Write,type_bulk as tb WRITE,"
                    . " user WRITE, main_menu_sub WRITE, log_history WRITE,picklist WRITE,mst_sales_group msg WRITE");
            $dataLastDetail = $this->m_PicklistDetail->detailData(['no_pl' => $pl, 'valid !=' => "cancel"], false, "row_order");

            $rowOrder = (isset($dataLastDetail->row_order)) ? ($dataLastDetail->row_order + 1) : 1;
            $status = "";
            $datainsert = [];
            $barcodeInput = [];
            $picklist = $this->m_Picklist->getDataPicklist(["no" => $pl, "status <>" => "cancel"]);
            $pcsTotal = 0;
            $qtyTotal = 0.0;
            foreach ($data as $key => $value) {
                $check = $this->m_Pickliststockquant->checkItemAvailable(['stock_quant.quant_id' => $value]);
                if (in_array($check->lot, $barcodeInput)) {
                    throw new \Exception("ada Duplikat Barcode di Picklist", 500);
                }
                switch (true) {
                    case $check->reserve_move !== "":
                        throw new \Exception("Barcode " . $check->lot . " reserve move " . $check->reserve_move, 500);
                        break;
                    case count($check) < 1 :
                        throw new \Exception("Barcode " . $check->lot . " Tidak Ditemukan di lokasi fisik", 500);
                        break;
                    case strtoupper($check->lokasi) !== 'GJD/STOCK':
                        throw new \Exception("Barcode " . $check->lot . " Tidak pada lokasi GJD/STOK", 500);
                        break;
                    case $check->qty_jual === 0.00:
                        throw new \Exception("Barcode " . $check->lot . " QTY Jual 0.00", 500);
                        break;
                    case $check->valid != null :
                        if ($check->no_pl === $pl) {
                            throw new \Exception("Barcode " . $check->lot . " sudah ada dipicklist.", 500);
                        }
                        throw new \Exception("Barcode " . $check->lot . " sudah ada dipicklist lain.", 500);
                        break;
                }

                $sc = $check->sales_order;
                $barcodeInput[] = $check->lot;
                $datainsert[] = [
                    "id" => null,
                    "barcode_id" => $check->lot,
                    "quant_id" => $value,
                    "no_pl" => $pl,
                    'kode_produk' => $check->kode_produk,
                    'nama_produk' => $check->nama_produk,
                    'warna_remark' => $check->warna_remark,
                    'corak_remark' => $check->corak_remark,
                    'lebar_jadi' => $check->lebar_jadi,
                    'uom_lebar_jadi' => $check->uom_lebar_jadi,
                    'qty' => $check->qty_jual,
                    'qty2' => $check->qty2_jual ?? 0.00,
                    'uom' => $check->uom_jual,
                    'uom2' => $check->uom2_jual,
                    'sales_order' => $sc,
                    'lokasi_fisik' => $check->lokasi_fisik,
                    'tanggal_masuk' => date('Y-m-d H:i:s'),
                    'valid' => $this->input->post('status') ?? "draft",
                    'row_order' => $key + $rowOrder,
                    'qty_hph' => $check->qty,
                    'uom_hph' => $check->uom,
                    'qty2_hph' => $check->qty2,
                    'uom2_hph' => $check->uom2,
                    'lebar_greige' => $check->lebar_greige ?? null,
                    'uom_lebar_greige' => $check->uom_lebar_greige ?? null
                ];
                $status = (($status === "") ? ($check->status ?? "") : $status);

//                if (!empty($insrt)) {
//                    if (strpos($insrt, 'Duplicate') !== false) {
//                        $insrt = "Barcode " . $value->barcode . " sudah ada dipicklist lain.";
//                    }
//                    throw new \Exception($insrt, 500);
//                }
                $pcsTotal += 1;
                $qtyTotal += $check->qty_jual;
            }
            $this->m_PicklistDetail->insertBatch($datainsert);
            $sc = $this->m_PicklistDetail->getSc(["no_pl" => $pl, 'valid <>' => "cancel", "sales_order <>" => "", "sales_order is NOT NULL" => null]);
            $picklist->pcs_qty += $pcsTotal;
            $picklist->tot_qty += $qtyTotal;
            $this->m_Picklist->update(['sc' => ($sc->sc ?? ""), 'tot_qty' => $picklist->tot_qty, "pcs_qty" => $picklist->pcs_qty], ['no' => $pl]);

            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            if ($this->input->post('status') !== "") {
                if (!is_null($this->m_Picklist->getDataByID(['no' => $pl, 'status' => 'draft']))) {
                    $this->m_Picklist->update(['status' => $this->input->post('status')], ['no' => $pl]);
                }
            }
            $this->_module->gen_history($sub_menu, $pl, 'edit', 'Menambahkan Barcode ' . implode(',', $barcodeInput), $username);
            if ($this->input->post('status') === "realisasi") {
                $this->_module->gen_history($sub_menu, $pl, 'edit', ($nama["nama"] ?? "") . ' Melakukan Realisasi barcode ' . implode(',', $barcodeInput), $username);
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Barcode berhasil ditambahkan', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function delete_item() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $id = $this->input->post('id');
            $pl = $this->input->post('pl');
            $status = $this->input->post('status');
            $user = $this->m_user->get_user_by_username($username);
            if (in_array($user->level, ["Entry Data", ""])) {
                $conditions = ["lot" => $id];
                $list = $this->m_Pickliststockquant->getDataItemPicklistScan(array_merge($conditions, ['id_category' => 21]), false, []);
                if (count($list) < 1) {
                    throw new \Exception('Data Tidak ditemukan', 500);
                }
                if ($list->lokasi_fisik === "XPD") {
                    throw new \Exception('Akses tidak diijinkan, Silahkan Hubungi supervisor', 500);
                }
//                throw new \Exception('Akses tidak diijinkan', 500);
            }

            $this->_module->startTransaction();
            $this->_module->lock_tabel("picklist_detail WRITE,picklist WRITE,stock_quant WRITE,user WRITE, main_menu_sub WRITE, log_history WRITE,mst_produk mp WRITE");
            $picklist = $this->m_Picklist->getDataPicklist(["no" => $pl, 'barcode_id' => $id, 'valid !=' => "cancel"], "*", "detail");
            $this->m_PicklistDetail->updateStatus(['barcode_id' => $id, 'valid !=' => "cancel"], ['valid' => "cancel"]);
            if (strtolower($status) === 'validasi') {
                $this->m_Pickliststockquant->update(["lokasi_fisik" => "PORT"], ["lot" => $id]);
            }
            $sc = $this->m_PicklistDetail->getSc(["no_pl" => $pl, 'valid <>' => "cancel", "sales_order <>" => "", "sales_order is NOT NULL" => null]);
            $pcs = $picklist->pcs_qty - 1;
            $qty = $picklist->tot_qty - $picklist->qty;
            $this->m_Picklist->update(['sc' => ($sc->sc ?? ""), "pcs_qty" => $pcs, "tot_qty" => $qty], ['no' => $pl]);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menghapus Data', 500);
            }
            $this->_module->gen_history($sub_menu, $pl, 'cancel', 'Menghapus barcode ' . $id, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function update() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $kode_decrypt = decrypt_url($this->input->post('ids'));
            if (!$kode_decrypt) {
                throw new \Exception('Data Tidak ditemukan', 500);
            }
            $this->form_validation->set_rules($this->val_form);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
//            $check = 0;
//            $existing = json_decode($this->input->post('existsing'));
//            switch (true) {
//            case $this->input->post('bulk'):
//
//
//            break;
//
//            default:
//            break;
//            }
            $this->_module->startTransaction();
            $input = array(
                'type_bulk_id' => $this->input->post('bulk'),
                'sales_kode' => $this->input->post('sales'),
                'jenis_jual' => $this->input->post('jenis_jual'),
                'customer_id' => $this->input->post('customer'),
                'keterangan' => $this->input->post('ket'),
                'sc' => $this->input->post('sc'),
                'alamat_kirim' => $this->input->post('alamat')
            );

            $this->m_Picklist->update($input, ['no' => $kode_decrypt]);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history($sub_menu, $this->input->post('no_pl'), 'edit', logArrayToString('; ', $input), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $this->input->post('ids'))));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function save() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $this->form_validation->set_rules($this->val_form);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $this->_module->startTransaction();
            $this->_module->lock_tabel("picklist write,token_increment WRITE,token_increment it READ,user WRITE, main_menu_sub WRITE, log_history WRITE");
            if (!$nopl = $this->token->noUrut('picklist', date('ym'), true)->generate('PL', '%04d')->get()) {
                throw new \Exception("No Picklist tidak terbuat", 500);
            }
            $input = array(
                'id' => null,
                'no' => $nopl,
                'type_bulk_id' => $this->input->post('bulk'),
                'sales_kode' => $this->input->post('sales'),
                'jenis_jual' => $this->input->post('jenis_jual'),
                'customer_id' => $this->input->post('customer'),
                'keterangan' => $this->input->post('ket'),
                'tanggal_input' => date('Y-m-d H:i:s'),
                'nama_user' => $this->session->userdata('nama')['nama'] ?? "",
                'status' => 'draft',
                'sc' => $this->input->post('sc'),
                'alamat_kirim' => $this->input->post("alamat")
            );
            $id = $this->m_Picklist->save($input);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history($sub_menu, $input["no"], 'create', logArrayToString('; ', $input), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => encrypt_url($nopl))));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->unlock_tabel();
        }
    }

    public function batal_picklist() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }

            $user = $this->m_user->get_user_by_username($username);
            if (in_array($user->level, ["Entry Data", ""])) {
                throw new \Exception('Akses tidak diijinkan', 500);
            }

            $pl = $this->input->post('pl');
            $check = $this->m_Picklist->checkExists(['a.no' => $pl], ["DO"]);
            if (empty($check)) {
                throw new \Exception('Data PL tidak ditemukan', 500);
            }
            if (!empty($check->doid)) {
                throw new \Exception('Picklist sudah masuk delivery order', 500);
            }
//            if(!empty($check->no_bulk)) {
//                throw new \Exception('Picklist sudah masuk delivery order', 500);
//            }
            $this->_module->startTransaction();
            $this->_module->lock_tabel("picklist_detail WRITE,stock_quant WRITE,picklist write,user WRITE, main_menu_sub WRITE, log_history WRITE,mst_produk mp WRITE");
            $this->m_Picklist->update(['status' => 'cancel'], ['no' => $pl]);
//            $this->m_PicklistDetail->updateStatus(['no_pl' => $pl], ["valid" => "cancel"]);
            $datas = $this->m_PicklistDetail->detailReportQty(['no_pl' => $pl, 'valid !=' => 'cancel'], "quant_id,valid");
            $withStatus = [];
            $noStatus = [];
            foreach ($datas as $value) {
                if ($value->valid === 'validasi') {
                    $withStatus[] = $value->quant_id;
                    continue;
                }
                $noStatus[] = $value->quant_id;
            }
            if (count($datas) > 0) {
                $this->m_PicklistDetail->updateStatusWin(['valid !=' => 'cancel', 'no_pl' => $pl], ['valid ' => 'cancel'], ['quant_id' => array_merge($withStatus, $noStatus)]);

                if (!empty($withStatus)) {
                    $this->m_Pickliststockquant->updateWin(["lokasi_fisik" => "PORT"], ["lot !=" => ''], ['quant_id' => $withStatus]);
                }
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Membatalkan Picklist', 500);
            }

            $this->_module->gen_history($sub_menu, $pl, 'cancel', logArrayToString('; ', ['no' => $pl, 'status' => 'batal']), $username);
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

    public function update_status() {
        try {
            $pl = $this->input->post('pl');
            $status = $this->input->post('status');
            $condition = [];
            $updates = [];
            switch ($status) {
                case "draft":
                    $condition = ['no_pl' => $pl, 'valid' => $status];
                    $updates = ['status' => 'realisasi'];
                    break;
                case "realisasi":
                    $condition = ['no_pl' => $pl, 'valid' => $status];
                    $updates = ['status' => 'validasi'];
                    break;
            }
            $dt = $this->m_PicklistDetail->getCountAllData($condition);
            if ($dt > 0) {
                throw new Exception('Item di Picklist ' . $pl . ' masih ada yang status ' . strtoupper($status), 500);
            }
            $this->_module->startTransaction();
            $this->m_Picklist->update($updates, ['no' => $pl]);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
//            $this->_module->gen_history($sub_menu, $input["no"], 'create', logArrayToString('=', $input), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function update_note() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            $pl = $this->input->post('pl');
            $ket = $this->input->post('keterangan');
            $this->m_Picklist->update(['keterangan' => $ket], ['no' => $pl]);

            $this->_module->gen_history($sub_menu, $pl, 'edit', "Update NOTE di DO : " . $ket, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function print() {
        $pl = $this->input->get('nopl');
        $this->load->library('dompdflib');
        $this->load->library('barcode');
        $code = new Code\Code128New();
        $gen_code = $code->generate($pl, "");
        $logo_path = file_get_contents(FCPATH . 'dist/img/static/heksatex_c.jpg');
        $logo_type = pathinfo(FCPATH . 'dist/img/static/heksatex_c.jpg', PATHINFO_EXTENSION);
        $logo = base64_encode($logo_path);
        $data['picklist'] = $this->m_Picklist->getDataReportPL(['no' => $pl]);
        $data['picklist_detail'] = $this->m_PicklistDetail->detailReport(['no_pl' => $pl, 'valid !=' => 'cancel'], ['warna_remark', 'corak_remark', 'uom']);
        $data['nopl'] = $pl;
        $data['logo'] = 'data:image/' . $logo_type . ';base64,' . $logo;
        $data['barcode'] = 'data:image/' . $logo_type . ';base64,' . $gen_code;
        $cnt = $this->load->view('report/html_to_pdf/picklist_detail', $data, true);
//        $this->load->view('report/html_to_pdf/picklist_detail', $data);
        $this->dompdflib->generate($cnt);
    }

    public function broadcast() {
        try {
            $pl = $this->input->post("pl");
            $data = $this->m_Picklist->getDataByID(['picklist.no' => $pl]);

            $dataPesan = [
                "{no_pl}" => $pl,
                "{customer}" => $data->nama,
                "{marketing}" => $data->sales,
                "{jenis_jual}" => $data->jenis_jual,
                "{bulking}" => ($data->type_bulk_id === "1") ? "BAL" : "LOOSE PACKING"
            ];

            $this->m_Picklist->update([
                "notifikasi" => 1,
                    ], ['no' => $pl]);
            $mention = [];
            $getMention = $this->m_Picklist->getUserBC(['nama' => $data->nama_user, 'username' => 'prianto', 'username' => 'fmuharam', 'username' => 'amunandar', 'username' => 'mfachril']);
            foreach ($getMention as $key => $value) {
                if (is_null($value->telepon_wa) || empty($value->telepon_wa)) {
                    continue;
                }
                $mention[] = $value;
            }

            $this->wa_message->sendMessageToGroup('new_picklist', $dataPesan, ['WAREHOUSE 24JAM'])->setMentions($mention)->setFooter('footer_hms')->send();

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function lokasi_fisik() {
        try {
            $no = $this->input->get('no');
            $data = $this->m_PicklistDetail->reportLokasiFisikRak(['no_pl' => $no, 'valid !=' => 'cancel']);
            if (empty($data)) {
                throw new Exception("", 500);
            }
            $tempLokasi = [];
            $lisdata = [];
            foreach ($data as $value) {
                $tempLokasi[] = $value->lokasi_fisik;
            }
            $datas = $this->m_PicklistDetail->reportLokasiFisik(['no_pl' => $no, 'valid !=' => 'cancel'], ['sq.lokasi_fisik' => $tempLokasi]);
            if (empty($datas)) {
                throw new Exception("", 500);
            }
            $totalItem = 0;
            foreach ($datas as $key => $value) {
                $totalItem++;
                if (isset($lisdata[$value->lokasi_fisik])) {
                    $lisdata[$value->lokasi_fisik][] = $value;
                } else {
                    $lisdata[$value->lokasi_fisik] = [];
                    $lisdata[$value->lokasi_fisik][] = $value;
                }
            }
            $this->load->view('print/picklist/printpl', ['pl' => $no, 'data' => $lisdata, 'total' => $totalItem]);
        } catch (Exception $ex) {
            
        }
    }
}
