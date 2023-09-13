<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of WaTemplate
 *
 * @author RONI
 */
class WaTemplate extends MY_Controller {

    //put your code here

    protected $valForm = array(
        [
            'field' => 'nama',
            'label' => 'Nama',
            'rules' => ['trim', 'required', 'max_length[100]','regex_match[/^[a-zA-Z0-9_]+$/]'],
            'errors' => [
                'required' => '{field} Harus Diisi',
                'max_length' => '{field} maksimal {param} karakter.',
                'regex_match' => '{field} Hanya Alpha Numeric'
            ]
        ],
        [
            'field' => 'template',
            'label' => 'Template',
            'rules' => ['required'],
            'errors' => [
                'required' => '{field} Harus Diisi',
                
            ]
        ]
    );

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("m_WaTemplate");
        $this->load->model("_module");
    }

    public function index() {
        $data['id_dept'] = 'MUSR';
        return $this->load->view('setting/v_wa_template', $data);
    }

    public function add() {
        $data['id_dept'] = 'MUSR';
        return $this->load->view('setting/v_wa_template_add', $data);
    }
    
    public function edit($id = null) {
        try {
            $kode_decrypt = decrypt_url($id);
            if (!$kode_decrypt) {
                throw new \Exception();
            }
            $data['template'] = $this->m_WaTemplate->getDataByID($kode_decrypt);
            $data["id"] = $id;
            $data['id_dept'] = 'MUSR';
            $data['mms'] = $this->_module->get_data_mms_for_log_history($data['id_dept']);
            return $this->load->view('setting/v_wa_template_edit', $data);
        } catch (Exception $ex) {
            show_404();
        }
    }

    public function simpan() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $this->load->library('form_validation');
            array_push($this->valForm[0]['rules'], "is_unique[wa_template.nama]");
            $this->valForm[0]['errors'] = array_merge($this->valForm[0]['errors'], ['is_unique' => '{field} sudah terdaftar']);
            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                log_message('error', array_values($this->form_validation->error_array())[0]);
                throw new \Exception(array_values($this->form_validation->error_array())[0],500);
            }
            $nama = $this->input->post("nama");
            $template = $this->input->post("template");
            if ($status = $this->m_WaTemplate->simpan($nama, $template)) {
                if (!$status)
                    throw new \Exception("Gagal Input Data Template",500);
            }
            $this->_module->gen_history($sub_menu, $nama, 'create', 'Membuat Template WA ' . $nama . ' | ' . $template, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil')));
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
    
    public function update() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            
            $kode_decrypt = decrypt_url($this->input->post("id"));
            if (!$kode_decrypt) {
                throw new \Exception("data tidak ditemukan",500);
            }
            $this->load->library('form_validation');
            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0]);
            }
            $nama = $this->input->post("nama");
            $template = $this->input->post("template");
            
            if (!$this->m_WaTemplate->update(['id'=>$kode_decrypt], ['nama'=>$nama,'template'=>$template])) {
                throw new \Exception("Gagal Merubah Data template",500);
            }
            $this->_module->gen_history($sub_menu, $nama, 'Edit', 'Mengubah Template WA ' . $nama . ' | ' . $template, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function getData() {
        try {
            $data = array();
            $list = $this->m_WaTemplate->getData();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->id);
                $no++;
                $row = array(
                    $no,
                    '<a href="' . base_url('setting/wa_template/edit/' . $kode_encrypt) . '">' . $field->nama . '</a>',
                    $field->template,
                    date('D m, Y H:i:s', strtotime($field->created_at)),
                );
                $data[] = $row;
            }

            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
        }
    }
}
