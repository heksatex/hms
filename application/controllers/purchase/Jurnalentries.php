<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Jurnal
 *
 * @author RONI
 */
class Jurnalentries extends MY_Controller {

    //put your code here
    public function __construct() {
        parent:: __construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model('_module');
        $this->config->load('additional');
    }

    public function index() {
        $data['id_dept'] = 'JNE';
        $this->load->view('purchase/v_jurnal_entries', $data);
    }

    public function data() {
        try {
            $data = array();
            $list = new $this->m_global;
            $no = $_POST['start'];

            $list->setTables("jurnal_entries")->setOrder(["tanggal_dibuat"])
                    ->setJoins("mst_status", "mst_status.kode = jurnal_entries.status", "left")
                    ->setSearch(["kode", "periode", "origin"])
                    ->setOrders([null, "kode", "tanggal_dibuat", "tanggal_posting", "periode", "origin", "reff_note", "status"])
                    ->setSelects(["jurnal_entries.*", "nama_status"]);
            foreach ($list->getData() as $key => $field) {
                $kode_encrypt = encrypt_url($field->kode);
                $no++;
                $data [] = array(
                    $no,
                    '<a href="' . base_url('purchase/jurnalentries/edit/' . $kode_encrypt) . '">' . $field->kode . '</a>',
                    $field->tanggal_dibuat,
                    $field->tanggal_posting,
                    $field->periode,
                    $field->origin,
                    $field->nama_status ?? $field->status,
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
            $data['id_dept'] = 'JNE';
            $data["id"] = $id;
            $head = new $this->m_global;
            $detail = clone $head;

            $data["jurnal"] = $head->setTables("jurnal_entries")->setWheres(["kode" => $kode_decrypt])->getDetail();
            if ($data["jurnal"] === null) {
                throw new \Exception();
            }
            $data["detail"] = $detail->setTables("jurnal_entries_items jei")->setOrder(["jei.row_order"])
                            ->setJoins("partner", "partner.id = jei.partner", "left")
                            ->setJoins("coa", "coa.kode_coa = jei.kode_coa", "left")
                            ->setSelects(["jei.*", "partner.nama as supplier", "coa.nama as account"])
                            ->setWheres(["kode" => $kode_decrypt])->getData();
            $this->load->view('purchase/v_jurnal_entries_edit', $data);
        } catch (Exception $ex) {
            return show_404();
        }
    }

    public function getcoa() {
        try {
            $search = $this->input->post("search");
            $coa = new $this->m_global;
            $_POST['search'] = array(
                'value' => $search
            );
            $_POST['length'] = 20;
            $_POST['start'] = 0;

            $data = $coa->setTables("coa")->setSearch(["kode_coa", "nama"])->setOrder(['nama'])->setSelects(['kode_coa', 'nama'])->getData();
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'success', 'icon' => 'fa fa-warning', 'type' => 'danger', 'data' => $data)));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger', "data" => [])));
        }
    }
}
