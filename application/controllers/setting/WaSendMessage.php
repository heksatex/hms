<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class WaSendMessage extends MY_Controller {

    protected $valForm = array(
        [
            'field' => 'pesan',
            'label' => 'Pesan',
            'rules' => ['trim', 'required', 'min_length[10]'],
            'errors' => [
                'required' => '{field} Harus Diisi',
                'min_length' => 'Pesan minimal {param} karakter.',
            ]
        ],
        [
            'field' => 'group[]',
            'label' => 'Group',
            'rules' => ['required'],
            'errors' => [
                'required' => '{field} Harus Diisi',
            ]
        ]
    );

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("m_WaSendMessage");
        $this->load->library("wa_message");
        $this->load->model("m_WaGroup");
        $this->load->model("m_WaTemplate");
        $this->load->model("_module");
        $this->load->library('barcode');
    }

    public function index() {
        $data['id_dept'] = 'MWSM';
        return $this->load->view('setting/v_wa_send_message', $data);
    }

    public function add() {
        $data['id_dept'] = 'MWSM';
        $data['group'] = $this->m_WaGroup->getDataQuery();
        $data['template_footer'] = $this->m_WaTemplate->getFooterTemplate();

        return $this->load->view('setting/v_wa_send_message_add', $data);
    }

    public function getListUser() {
        $search = $this->input->post('search') ?? null;
        $data = $this->m_WaSendMessage->getUser($search);
        $result = [];
        $temp = [];
        foreach ($data as $key => $value) {

            if (in_array($value->dept, $temp)) {

                array_push($result[array_search($value->dept, $temp)]["children"], ['text' => $value->nama, 'id' => $value->telepon_wa]);
            } else {
                array_push($result, [
                    'text' => $value->dept,
                    'children' => [
                        array(
                            'id' => $value->telepon_wa,
                            'text' => $value->nama,
                            'disabled' => $value->telepon_wa ? false : true
                        )
                    ]
                ]);

                $temp[] = $value->dept;
            }
        }
        echo json_encode($result);
    }

    public function kirim() {
        try {
            if (empty($this->session->userdata('status'))) {
                throw new \Exception('Waktu Anda Telah Habis', 410);
            }
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');
            $this->form_validation->set_rules($this->valForm);
            if ($this->form_validation->run() == FALSE) {
                throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
            }
            $pesan = $this->input->post('pesan');
            $group = $this->input->post('group');
            $mention = $this->input->post('mention') ?? [];
            $footer = $this->input->post('footer');

            $send = $this->wa_message->setMessageNoTemplate($pesan)->setMentions($mention)->setFooter($footer)->sendMessageToGroup('', [], $group)->send();
            $this->_module->gen_history($sub_menu, 'wa_send_message', 'create', 'Kirim Pesan ' . $pesan, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function getData() {
        try {
            $data = array();
            $list = $this->m_WaSendMessage->getData();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->id);
                $no++;
                $row = array(
                    $no,
                    $field->message,
                    $field->touser,
                    $field->togroup,
                    $field->status == 0 ? '<span class="text-success">Success</span>' : ($field->status == 1 ? '<span class="text-danger">Failed</span>&nbsp<a href="#" data-id="' . $kode_encrypt . '" class="text-default resend">Resend</a>' : '<span class="text-warning">Menunggu</span>'),
                    date('D d m, Y H:i:s', strtotime($field->created_at)),
                );
                $data[] = $row;
            }

            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_WaSendMessage->getCountAllData(),
                "recordsFiltered" => $this->m_WaSendMessage->getCountDataFiltered(),
                "data" => $data,
            ));
            exit();
        } catch (Exception $ex) {
            log_message('error', $ex->getMessage());
        }
    }

    public function resend() {
        try {
            $id = decrypt_url($this->input->post('id'));
            $condition = [
                "id" => $id
            ];
            $this->m_WaSendMessage->update($condition, ["status" => 2]);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
        } catch (Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}
