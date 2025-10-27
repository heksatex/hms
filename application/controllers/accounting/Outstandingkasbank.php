<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

class Outstandingkasbank extends MY_Controller
{

    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load modul global
        $this->load->model("m_outstandingkasbank"); 
    }


    public function index()
    {
        $data['id_dept'] = 'ACCOKU';
        $this->load->view('accounting/v_outstanding_kas_bank', $data);
    }


    public function list_data_kas_bank()
    {
        try {

            if (isset($_POST['start']) && isset($_POST['draw'])) {
                $list = $this->m_outstandingkasbank->get_datatables();
                $data = array();
                $no = $_POST['start'];
                foreach ($list as $field) {
                    $no++;
                    $row = array();
                    $row[] = $no;
                    $row[] = $field->no_bukti;
                    $row[] = $field->partner_nama;
                    $row[] = date('Y-m-d', strtotime($field->tanggal));
                    $row[] = $field->currency;
                    $row[] = $field->kurs;
                    $row[] = number_format($field->total_rp, 4);
                    $row[] = number_format($field->total_valas, 4);
                    $data[] = $row;
                }

                $output = array(
                    "draw" => $_POST['draw'],
                    "recordsTotal" => $this->m_outstandingkasbank->count_all(),
                    "recordsFiltered" => $this->m_outstandingkasbank->count_filtered(),
                    "data" => $data,
                );
                //output dalam format JSON
                echo json_encode($output);
            } else {
                die();
            }

            exit();
        } catch (Exception $ex) {
            echo json_encode(array(
                "draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }


}