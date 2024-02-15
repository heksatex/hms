<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class Helpers extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model("_module");
    }

    public function history_stock_move_data_table() {

        try {
            $this->load->model("m_stock_move");

            $condition = json_decode($this->input->post("condition"));
            $list = $this->m_stock_move->sm_get_datatables((array) $condition);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $value) {
                $no++;
                $row = [
                    $no,
                    $value->move_id,
                    $value->create_date,
                    $value->origin,
                    $value->method,
                    $value->lokasi_dari,
                    $value->lokasi_tujuan,
                    $value->status,
                    "-"
                ];
                $data[] = $row;
            }
            echo json_encode(array("draw" => $_POST['draw'],
                "recordsTotal" => $this->m_stock_move->sm_count_all((array) $condition),
                "recordsFiltered" => $this->m_stock_move->sm_count_filtered((array) $condition),
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
}
