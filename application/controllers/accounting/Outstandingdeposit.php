<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

class Outstandingdeposit extends MY_Controller
{

    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load modul global
        $this->load->model("m_outstandingdeposit");
    }


    public function index()
    {
        $data['id_dept'] = 'ACCODEP';
        $this->load->view('accounting/v_outstanding_deposit', $data);
    }

    public function list_data_deposit()
    {
        try {

            if (isset($_POST['start']) && isset($_POST['draw'])) {
                $list = $this->m_outstandingdeposit->get_datatables();
                $data = array();
                $no = $_POST['start'];
                foreach ($list as $field) {
                    $no++;
                    $row = array();
                    $row[] = $no;
                    $row[] = $field->no_pelunasan;
                    $row[] = $field->partner_nama;
                    $row[] = date('Y-m-d', strtotime($field->tanggal_transaksi));
                    $row[] = $field->currency;
                    $row[] = $field->kurs;
                    $row[] = number_format($field->total_rp, 2);
                    $row[] = number_format($field->total_valas, 2);
                    $row[] = $field->id;
                    $data[] = $row;
                }

                $output = array(
                    "draw" => $_POST['draw'],
                    "recordsTotal" => $this->m_outstandingdeposit->count_all(),
                    "recordsFiltered" => $this->m_outstandingdeposit->count_filtered(),
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


    public function nonaktif()
    {
        $id  = $this->input->post('id');
        $no  = $this->input->post('no_pelunasan');

        if (!$id || !$no) {
            echo json_encode([
                'status' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
            return;
        }

        $this->db->where('id', $id)
            ->where('no_pelunasan', $no)
            ->update('acc_pelunasan_piutang_summary_koreksi', [
                'lunas' => 3,
            ]);

        echo json_encode(['status' => true]);
    }
}
