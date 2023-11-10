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
        $this->load->model("m_PicklistDetail");
        $this->load->library("token");
        $this->load->model("m_Pickliststockquant");
        $this->load->library('prints');
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
            $data['id_dept'] = 'PL';
            $data["ids"] = $id;
            $data['picklist'] = $this->m_Picklist->getDataByID($kode_decrypt);
            $data['bulk'] = $this->m_Picklist->getTypeBulk();
            $data['sales'] = $this->m_Picklist->getSales();
//            log_message('error', $_SERVER['REMOTE_ADDR']);
            $this->load->view('warehouse/v_picklist_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function data() {
        try {
            $data = array();
            $list = $this->m_Picklist->getData();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->id);
                $no++;
                $row = array(
                    $no,
                    '<a href="' . base_url('warehouse/picklist/edit/' . $kode_encrypt) . '">' . $field->no . '</a>',
                    $field->tanggal_input,
                    $field->jenis_jual,
                    $field->bulk_nama,
                    $field->keterangan,
                    $field->sales_nama,
                    $field->status,
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
            if ($this->input->post('filter') !== "" && $_POST["search"]["value"] !== "") {
                $condition = array_merge($condition, ['stock_quant.' . $this->input->post('filter') . " LIKE" => '%' . $_POST["search"]["value"] . '%']);
            }
            if (!is_string($this->input->post('marketing'))) {
                $in = array_merge($in, ['`stock_quant`.`sales_group`' => $this->input->post('marketing')]);
            }
            $list = $this->m_Pickliststockquant->getDataItemPicklist($condition, $in);
            $no = $_POST['start'];
            foreach ($list as $field) {
                $field->status = 'draft';
                $no++;
                $row = array(
                    $no,
                    $field->barcode,
                    $field->kode_produk,
                    $field->nama_produk,
                    $field->corak_remark,
                    $field->warna_remark,
                    $field->qty_jual . " " . $field->uom_jual,
                    $field->qty2_jual . " " . $field->uom2_jual,
                    $field->lokasi_fisik,
                    json_encode($field)
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_Pickliststockquant->count_all($condition),
                "recordsFiltered" => $this->m_Pickliststockquant->count_filteredItemPicklist($condition),
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
            $condition = ["lot" => $this->input->post('search')];
            $list = $this->m_Pickliststockquant->getDataItemPicklistScan($condition);
            if (count($list) !== 1) {
                throw new \Exception('Jumlah atau Barcode Tidak ditemukan', 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $list)));
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
            $condition = ["no_pl" => $pl];
            $list = $this->m_PicklistDetail->getData($condition);
            $no = $_POST['start'];
            $data = array();
            
            foreach ($list as $field) {
                $status = (strcasecmp($field->valid, 'draft') == 0) ? "<button class='btn btn-danger status_item' data-id='" . $field->barcode_id . "' data-pl='" . $field->no_pl . "'><fa class='fa fa-trash'></fa></button>" : '';
                $no++;
                $row = array(
                    $no,
                    $field->barcode_id,
                    $field->corak_remark,
                    $field->warna_remark,
                    $field->qty . " " . $field->uom,
                    $field->qty2 . " " . $field->uom2,
                    $field->lokasi_fisik,
                    $field->valid,
                    $field->valid_date,
                    $status
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_PicklistDetail->getCountAllData($condition),
                "recordsFiltered" => $this->m_PicklistDetail->getCountDataFiltered($condition),
                "data" => $data,
            ));
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
            $pl = $this->input->post('pl');
            $datas = $this->input->post('item');
//            $ids = $this->input->post("ids");
            $data = json_decode($datas);
            $this->_module->startTransaction();
            foreach ($data as $key => $value) {
                $value = json_decode($value);
                $check = $this->m_Pickliststockquant->checkItemAvailable($value->quant_id);
                switch (true) {
                    case $check->reserve_move !== "":
                        throw new \Exception("Barcode " . $value->barcode . " reserve move " . $value->reserve_move, 500);
                        break;
                    case count($check) < 1 :
                        throw new \Exception("Barcode " . $value->barcode . " Tidak Ditemukan di lokasi fisik", 500);
                        break;
                    case strtoupper($check->lokasi) !== 'GJD/STOCK':
                        throw new \Exception("Barcode " . $value->barcode . " Tidak pada lokasi GJD/STOK", 500);
                        break;
                    case $value->qty_jual === 0.00:
                        throw new \Exception("Barcode " . $value->barcode . " QTY Jual 0.00", 500);
                        break;
                }

                $sc = explode("|", $value->reserve_origin);
                $datainsert = [
                    "id" => null,
                    "barcode_id" => $value->barcode,
                    "quant_id" => $value->quant_id,
                    "no_pl" => $pl,
                    'kode_produk' => $value->kode_produk,
                    'nama_produk' => $value->nama_produk,
                    'warna_remark' => $value->warna_remark,
                    'corak_remark' => $value->corak_remark,
                    'lebar_jadi' => $value->lebar_jadi,
                    'uom_lebar_jadi' => $value->uom_lebar_jadi,
                    'qty' => $value->qty_jual,
                    'qty2' => $value->qty2_jual ?? 0.00,
                    'uom' => $value->uom_jual,
                    'uom2' => $value->uom2_jual,
                    'sales_order' => isset($sc[0]) ? $sc[0] : "",
                    'lokasi_fisik' => $value->lokasi_fisik,
                    'tanggal_masuk' => date('Y-m-d H:i:s'),
                    'valid' => $value->status ?? "draft",
                    'row_order' => $key + 1];

                $insrt = $this->m_PicklistDetail->insertItem($datainsert);

                if (!empty($insrt)) {
                    throw new \Exception($insrt, 500);
                }
                $this->_module->gen_history($sub_menu, $pl, 'create', logArrayToString('=', $datainsert), $username);
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
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
            $this->_module->startTransaction();
            $this->m_PicklistDetail->deleteItem(['barcode_id' => $id]);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menghapus Data', 500);
            }
            $this->_module->gen_history($sub_menu, $pl, 'delete', 'Menghapus barcode ' . $id, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
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
            $this->_module->startTransaction();
            $input = array(
                'type_bulk_id' => $this->input->post('bulk'),
                'sales_kode' => $this->input->post('sales'),
                'jenis_jual' => $this->input->post('jenis_jual'),
                'customer_id' => $this->input->post('customer'),
                'keterangan' => $this->input->post('ket')
            );

            $this->m_Picklist->update($input, ['id' => $kode_decrypt]);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history($sub_menu, $this->input->post('no_pl'), 'edit', logArrayToString('=', $input), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => $this->input->post('ids'))));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
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
                'status' => 'draft'
            );
            $id = $this->m_Picklist->save($input);
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }

            $this->_module->gen_history($sub_menu, $input["no"], 'create', logArrayToString('=', $input), $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'data' => encrypt_url($id))));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
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
            $this->_module->finishTransaction();
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
        $data['picklist_detail'] = $this->m_PicklistDetail->detailReport(['no_pl' => $pl]);
        $data['nopl'] = $pl;
        $data['logo'] = 'data:image/' . $logo_type . ';base64,' . $logo;
        $data['barcode'] = 'data:image/' . $logo_type . ';base64,' . $gen_code;
        $cnt = $this->load->view('report/html_to_pdf/picklist_detail', $data, true);
//        $this->load->view('report/html_to_pdf/picklist_detail', $data);
        $this->dompdflib->generate($cnt);
    }
}
