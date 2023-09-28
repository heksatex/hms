<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class WaSendMessage extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("m_WaSendMessage");
        $this->load->library("wa_message");
//        $this->load->model("_module");
    }

    public function index() {
        $data['id_dept'] = 'MWSM';
//        $this->wa_message->sendMessageToUser('saidi', ['{saidi}' => 'Satu'], 'achramdan')->setFooter('footer_default')->send();
        return $this->load->view('setting/v_wa_send_message', $data);
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
                    $field->status == 0 ? '<span class="text-success">Success</span>' : ($field->status == 1 ? '<span class="text-danger">Failed</span>' : '<span class="text-warning">Menunggu</span>'),
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
}
