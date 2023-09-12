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
        $data['id_dept'] = 'MUSR';
//       $das = $this->Send_Message_WA->sendMessageToUser('tester',['{saidi}'=>'sadidi'],'achramdan');
        $this->wa_message->sendMessageToGroupByDepth('tester',['{saidi}'=>'Tester'],['FIN','CST']);
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
                    $field->touser ? '<a href="' . base_url('setting/wa_send_message/edit/' . $kode_encrypt) . '">' . $field->touser . '</a>' : '',
                    $field->togroup ? '<a href="' . base_url('setting/wa_send_message/edit/' . $kode_encrypt) . '">' . $field->togroup . '</a>' : '',
                    $field->status == 0 ? '<button type="button" class="btn btn-outline-success">Success</button>' : $field->status == 1 ? '<button type="button" class="btn btn-outline-danger">Failed</button>' : '<button type="button" class="btn btn-outline-info">Menunggu</button>',
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
