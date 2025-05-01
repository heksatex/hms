<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Konversiuom
 *
 * @author RONI
 */
class Konversiuom extends MY_Controller {

    //put your code here

    protected $val_form = array(
        [
            'field' => 'dari',
            'label' => 'Dari UOM Beli',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus diisi'
            ]
        ],
        [
            'field' => 'ke',
            'label' => 'Ke UOM Stok',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus diisi'
            ]
        ],
        [
            'field' => 'nilai',
            'label' => 'Nilai Konversi',
            'rules' => ['trim', 'required', 'regex_match[/^\d*\.?\d*$/]'],
            'errors' => [
                'required' => '{field} Harus diisi',
                "regex_match" => "{field} harus berupa number / desimal"
            ]
        ]
    );

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("m_konversiuom");
        $this->load->model("_module");
    }

    public function index() {
        $data['id_dept'] = 'KUOM';
        $data['uom'] = $this->_module->get_list_uom();
        $this->load->view('warehouse/v_konversi_uom', $data);
    }

    public function save() {
        try {
            $this->form_validation->set_rules($this->val_form);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $dari = $this->input->post("dari");
            $ke = $this->input->post("ke");
            $nilai = $this->input->post("nilai");
            $catatan = $this->input->post("catatan");

            if ($this->input->post("posisi") !== '') {
                $insert = $this->m_konversiuom->wheres(["id" => $this->input->post("ids")])->update(["dari" => $dari, "ke" => $ke, "nilai" => $nilai,"catatan" => $catatan]);
            } else {
                $insert = $this->m_konversiuom->save(["dari" => $dari, "ke" => $ke, "nilai" => $nilai,"catatan" => $catatan]);
            }
            if ($insert !== "") {
                throw new \Exception($insert, 500);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function get_data() {
        $recordsTotal = 0;
        $recordsFiltered = 0;
        $data = [];
        try {
            $no = $_POST['start'];
            $list = $this->m_konversiuom->getData();
            $recordsTotal = $this->m_konversiuom->getDataCountAll();
            $recordsFiltered = $this->m_konversiuom->getDataCountFiltered();

            foreach ($list as $value) {
                $no++;
                $data[] = array(
                    $no,
                    $value->dari,
                    $value->ke,
                    $value->nilai,
                    $value->catatan,
                    "<button class='btn btn-default btn-sm edit_item' data-nilai='{$value->nilai}' data-id='{$value->id}' data-ke='{$value->ke}' "
                    . "data-dari='{$value->dari}' data-catatan='{$value->catatan}'><i class='fa fa-edit'></i> Edit</button>"
                );
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data,
            ));
        }
    }
}
