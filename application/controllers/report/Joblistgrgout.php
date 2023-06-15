<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Joblistgrgout extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        $this->load->model('m_joblistgrgout');
	}


	public function index()
	{
		$id_dept        = 'JLGRG';
        $data['id_dept']= $id_dept;
        $data['id_dept_asli'] = 'GRG' ;
		$this->load->view('report/v_job_list_greige_out', $data);
	}

	public function get_data()
	{

        $id_dept  = $this->input->post('id_dept');
	    $sub_menu = 'pengirimanbarang';
        if(isset($_POST['start']) && isset($_POST['draw'])){

            $kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();
            $list = $this->m_joblistgrgout->get_datatables($id_dept,$kode['kode']);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $kode_encrypt = encrypt_url($field->kode);

                if($field->status == 'draft'){
                    $nama_status ="<font color='red' >".$field->nama_status."</font>";
                }else if($field->status == 'ready'){
                    $nama_status ="<font color='green' >".$field->nama_status."</font>";
                }else{
                    $nama_status = $field->status;
                }

                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('warehouse/pengirimanbarang/edit/'.$kode_encrypt).'" target="_blank">'.$field->kode.'</a>';
                $row[] = $field->tanggal;
                $row[] = $field->origin;
                $row[] = $field->reff_picking;
                $row[] = $field->nama_produk;
                $row[] = $field->nama_warna;
                $row[] = $field->target_mtr;
                $row[] = $field->mtr;
                $row[] = $field->kg;
                $row[] = $field->gl;
                $row[] = $nama_status;
                /*
                $row[] = '<a href="#" onclick=kirim_barang("'.$field->kode.'","'.$field->move_id.'","'.$field->method.'","'.$field->origin.'","'.$id_dept.'","'.$field->status.'")> kirim>> </a>';
                */
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_joblistgrgout->count_all($id_dept,$kode['kode']),
                "recordsFiltered" => $this->m_joblistgrgout->count_filtered($id_dept,$kode['kode']),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
	}


	

	

}