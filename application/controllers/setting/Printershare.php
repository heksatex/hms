<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Printershare
 *
 * @author RONI
 */
class Printershare extends MY_Controller {

    //put your code here
    protected $val_form = array(
        [
            'field' => 'nama_printer_share',
            'label' => 'Nama Printer',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus diisi'
            ]
        ],
        [
            'field' => 'ip_share',
            'label' => 'Alamat IP',
            'rules' => ['trim', 'required'],
            'errors' => [
                'required' => '{field} Harus diisi'
            ]
        ]
    );

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("m_global");
        $this->load->model("_module");
    }

    public function index() {
        $user = $this->session->userdata('nama');
        if($user["level"] !== "Super Administrator"){
            return redirect(base_url('setting/printershare/view'));
        }
        $data['id_dept'] = 'SPRS';
        $this->load->view('setting/v_print_share', $data);
    }

    public function view() {
        $data['id_dept'] = 'SPRS';
        $data["printer"] = $this->m_global->setTables("share_printer")->setOrder(["id"])->getData();
        $data["priterDefault"] = $this->session->userdata('printer');
        $this->load->view('setting/v_print_view', $data);
    }

    public function save() {
        try {
            $this->form_validation->set_rules($this->val_form);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $printer = $this->input->post("nama_printer_share");
            $ip = $this->input->post("ip_share");

            if ($this->input->post("posisi") !== '') {
                $this->m_global->setTables("share_printer")->setWheres(["id" => $this->input->post("ids")])->update(["nama_printer_share" => $printer, "ip_share" => $ip]);
            } else {
                $this->m_global->setTables("share_printer")->save(["nama_printer_share" => $printer, "ip_share" => $ip]);
            }
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            log_message("error", $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function get_data() {
        try {
            $list = $this->m_global->setTables("share_printer")->setOrder(["id" => "asc"]);
            $no = $_POST['start'];
            $data = array();
            foreach ($list->getData() as $field) {
                $no++;
                $data [] = [
                    $no,
                    $field->nama_printer_share,
                    $field->ip_share,
                    "<button class='btn btn-default btn-sm edit_item' data-print='{$field->nama_printer_share}' data-id='{$field->id}' data-ip='{$field->ip_share}' "
                    . "><i class='fa fa-edit'></i> Edit</button>"
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

    public function set() {
        try {
            $data = $this->input->post("data");
            $this->session->set_userdata(["printer" => $data]);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (\Exception $ex) {
            log_message("error", $ex->getMessage());
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}
