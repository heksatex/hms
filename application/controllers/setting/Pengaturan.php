<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Pengaturan
 *
 * @author RONI
 */
class Pengaturan extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model("_module");
    }

    public function index() {
        $data['id_dept'] = 'STP';
        $this->load->view('setting/v_pengaturan', $data);
    }

    public function get_data() {
        try {
            $data = [];
            $no = $_POST['start'];
            $list = new $this->m_global;
            $list->setTables("setting")->setOrders(["setting_name", "value", "status"])->setOrder(["setting_name"]);
            foreach ($list->getData() as $field) {
                $no++;
                $data [] = [
                    $no++,
                    $field->setting_name,
                    $field->value,
                    ($field->status === "1") ? "Aktif" : "Tidak Aktif",
                    "<button class='btn btn-default btn-sm edit_item' data-setting='{$field->setting_name}' data-id='{$field->id}' data-value='{$field->value}' "
                    . "data-status='{$field->status}'><i class='fa fa-edit'></i> Edit</button>"
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
                "recordsTotal" => $recordsTotal,
                "recordsFiltered" => $recordsFiltered,
                "data" => $data,
            ));
        }
    }

    public function save() {
        try {

            $settingName = $this->input->post("setting_name");
            $value = $this->input->post("value");
            $status = $this->input->post("status");
            $ids = $this->input->post("ids");
            $model = new $this->m_global;

            if ($this->input->post("posisi") !== '') {
                $model2 = clone $model;
                $cek = $model2->setTables("setting")->setWheres(["setting_name" => $settingName])->setWhereRaw("id not in ('{$ids}')")->getDetail();

                if ($cek !== null) {
                    throw new Exception("setting name sudah terpakai", 500);
                }

                $responIns = $model->setTables("setting")->setWheres(["id" => $ids])->update(["setting_name" => $settingName, "value" => $value, "status" => $status]);
            } else {
                $responIns = $model->setTables("setting")->save(["setting_name" => $settingName, "value" => $value, "status" => $status]);
            }

            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}
