<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Joblistgobout extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        //$this->load->library('pagination');
        $this->load->model('m_joblistgobout');
	}
	

	public function index()
	{
	 	$id_dept         ='JLGOB';
        $data['id_dept'] = $id_dept;
        $data['id_dept_asli'] = 'GOB' ;
        $this->load->view('report/v_job_list_gob_out', $data);
	}



	public function get_data()
	{

        $id_dept  = $this->input->post('id_dept');
	    $sub_menu = 'pengirimanbarang';
        $kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();

        $list = $this->m_joblistgobout->get_datatables($id_dept,$kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        	$kode_encrypt = encrypt_url($field->kode);

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('warehouse/pengirimanbarang/edit/'.$kode_encrypt).'">'.$field->kode.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->origin;
            $row[] = $field->reff_picking;
            $row[] = $field->nama_status;
            $row[] = '<a href="#" onclick=kirim_barang("'.$field->kode.'","'.$field->move_id.'","'.$field->method.'","'.$field->origin.'","'.$id_dept.'","'.$field->status.'")> kirim>> </a>';
 
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_joblistgobout->count_all($id_dept,$kode['kode']),
            "recordsFiltered" => $this->m_joblistgobout->count_filtered($id_dept,$kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}


}