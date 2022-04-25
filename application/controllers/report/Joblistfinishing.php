<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Joblistfinishing extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        //$this->load->library('pagination');
        $this->load->model('m_joblistfinishing');
        $this->load->model('m_mo');
	}

	public function index()
	{
		$id_dept        = 'JLFIN';
        $data['id_dept']= $id_dept;
        $data['id_dept_asli'] = 'FIN' ;
		$this->load->view('report/v_job_list_finishing', $data);
	}
	

	public function get_data()
	{

        $id_dept  = $this->input->post('id_dept');
	    $sub_menu = 'pengirimanbarang';
        $kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();

        $list = $this->m_joblistfinishing->get_datatables($id_dept,$kode['kode']);
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
            $category_kain  = " AND mc.nama_category LIKE '%kain hasil%' ";
            $status_kain    = $this->cek_status_kain($field->kode,$category_kain);
            $category_resep = " AND mc.nama_category IN ('DYE','AUX') ";
            $status_resep   = $this->cek_status_kain($field->kode,$category_resep);

            $method         = $id_dept.'|IN';
            $kode_in_kain   = $this->get_kode_in_kain($field->origin,$method);
            $link_kain      = '<a href="'.base_url('warehouse/penerimaanbarang/edit/'.$kode_in_kain).'" target="_blank">'.$status_kain.'</a>';


            $kode_mrp       = $field->kode;
            $kode_produk    = $field->kode_produk;
            $nama_produk    = $field->nama_produk;
            $origin         = $field->origin;
            $qty1_std       = $field->qty1_std;
            $qty2_std       = $field->qty2_std;
            $lot_prefix     = $field->lot_prefix;
            $lot_prefix_waste = $field->lot_prefix_waste;
            /*
            $total_fg  = $this->m_mo->get_total_fg($kode_mrp);
            $sisa_qty       = $field->qty - $total_fg;
            $uom            = $field->uom;
            $move_id1 = $this->m_mo->get_move_id_rm_target_by_kode($kode)->row_array();
            $move_id_rm     = $move_id1['move_id'];
            $move_id2 = $this->m_mo->get_move_id_fg_target_by_kode($kode)->row_array();  
            $move_id_fg     = $move_id2['move_id'];
            */

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'" target="_blank">'.$kode_mrp.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->origin;
            $row[] = $nama_produk;
            $row[] = $link_kain;
            $row[] = $status_resep;
            $row[] = $nama_status;
            $row[] = "Produksi >>";

            //$row[] = '<a href="#" onclick=kirim_barang("'.$field->kode.'","'.$field->move_id.'","'.$field->method.'","'.$field->origin.'","'.$id_dept.'","'.$field->status.'")> kirim>> </a>';
 
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_joblistfinishing->count_all($id_dept,$kode['kode']),
            "recordsFiltered" => $this->m_joblistfinishing->count_filtered($id_dept,$kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}


    function cek_status_kain($kode,$category)
    {

        $rs = $this->m_joblistfinishing->cek_item_rm_target($kode,$category);
        $status = "Ready";
        $loop   = false;
        foreach($rs as $row){
            $loop = true;
            // cek jml yang sudah ada di stock_move_items berdasarkan produk
            $jml = $row->jml;

            if($row->jml == 0 ){ // jika == 0 bearti draft
                $status = "Draft";
            }
        }

        if($loop== true){
            $status = $status;
        }else{
            $status = "";
        }

        return $status;

    }

    function get_kode_in_kain($origin,$method)
    {

        // get kode in 
        $kode     = $this->m_joblistfinishing->get_link_kain_by_kode($origin,$method)->row_array();
    
        $kode_in  = $kode['kode'];
        $kode_encrypt = encrypt_url($kode_in);
        return $kode_encrypt;

    }

}