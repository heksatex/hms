<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class Bulk extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
        $this->load->model("m_Picklist");
        $this->load->model("m_bulk");
        $this->load->model("m_accessmenu");
        $this->load->library('prints');
        $this->load->library('barcode');
        $this->load->library("token");
    }

    public function index() {
        try {
            $data['id_dept'] = 'BULK';
            $this->load->view('warehouse/v_bulk', $data);
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function edit($id = null) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['id_dept'] = 'BULK';
            $data["ids"] = $id;
            $data['picklist'] = $this->m_Picklist->getDataByID(['picklist.no' => $kode_decrypt, 'status' => 'validasi', 'type_bulk_id' => 1]);
            $this->load->view('warehouse/v_bulk_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function data() {
        try {
            $data = array();
            $condition = ['status' => 'validasi', 'type_bulk_id' => 1];
            $list = $this->m_Picklist->getData(false, $condition);
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->no);
                $no++;
                $row = array(
                    $no,
                    '<a href="' . base_url('warehouse/bulk/edit/' . $kode_encrypt) . '">' . $field->no . '</a>',
                    $field->nama,
                    $field->tanggal_input,
                    $field->jenis_jual,
                    $field->keterangan,
                    $field->sales_nama,
                );
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_Picklist->getCountAllData($condition),
                "recordsFiltered" => $this->m_Picklist->getCountDataFiltered($condition),
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

    public function data_bulk() {
        try {
            $data = array();
            $condition = ['no_pl' => $this->input->post("pl")];
            $list = $this->m_bulk->getData($condition);
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                
            }
        } catch (Exception $ex) {
            
        }
    }

    public function add_bulk() {
        try {
            $data["pl"] = $this->input->post("pl");
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(['data' => $this->load->view('modal/v_bulk_add', $data, true)]));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function save_add_bulk() {
        try {
            $users = $this->session->userdata('nama');
            $sub_menu = $this->uri->segment(2);
            $data = array(
                'no_pl' => $this->input->post('pl'),
                'tanggal_input' => date('Y-m-d H:i:s'),
                'user' => ($users["nama"] ?? "")
            );
            if (!$no_bulk = $this->token->noUrut('bulk', date('ym'), true)->generate('', '%03d')->get()) {
                throw new \Exception("No Bulk tidak terbuat", 500);
            }
            $data['no_bulk'] = $no_bulk;
            $this->_module->startTransaction();
            $insert = $this->m_bulk->save($data);
            if (!empty($insert)) {
                throw new Exception("Gagal Membuat BAL ", 500);
            }
            if (!$this->_module->finishTransaction()) {
                throw new \Exception('Gagal Menyimpan Data', 500);
            }
            $this->_module->gen_history($sub_menu, $data['no_bulk'], 'create', 'Menambahkan bal / Bulk.', ($users["nama"] ?? $users["username"]));
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Bal / Bulk berhasil ditambahkan', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->_module->finishTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}
