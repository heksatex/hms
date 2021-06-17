<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/**
 * 
 */
class Stockmoves extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("_module");//load model global
		$this->load->model("m_stockMoves");

	}

	public function index()
	{
		$data['id_dept'] ='SM';
		//$data['tbody1'] = $this->m_stockQuants->get_list_stock_quant_grouping(); 
        $this->load->view('warehouse/v_stock_moves',$data);
	}


    public function get_data()
    {

        $sub_menu = $this->uri->segment(2);
        //$id_dept  = $this->input->post('id_dept');
        //$kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();
        $list = $this->m_stockMoves->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            //$kode_encrypt = $this->encryption->encrypt($field->kode);
            //$kode_encrypt = encrypt_url($field->kode);
            $no++;
            $row = array();
            $row[] = $field->create_date;
            $row[] = $field->move_id;
            $row[] = $field->origin;
            $row[] = $field->lokasi_dari;
            $row[] = $field->lokasi_tujuan;
            $row[] = $field->picking;
            $row[] = $field->kode_produk;
            $row[] = $field->nama_produk;
            $row[] = $field->lot;
            $row[] = $field->qty;
            $row[] = $field->uom;
            $row[] = $field->qty;
            $row[] = $field->uom2;
            $row[] = $field->status;
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_stockMoves->count_all(),
            "recordsFiltered" => $this->m_stockMoves->count_filtered(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

}