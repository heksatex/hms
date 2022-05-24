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
	    $sub_menu = 'mO';
        $kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();

        $list = $this->m_joblistfinishing->get_datatables($id_dept,$kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        	$kode_encrypt = encrypt_url($field->kode);
            $kode_mrp       = $field->kode;
            $nama_produk    = $field->nama_produk;

            if($field->status == 'draft' || $field->status == 'cancel'){
                $nama_status ="<font color='red' >".$field->nama_status."</font>";
            }else if($field->status == 'ready' || $field->status == 'done'){
                $nama_status ="<font color='green' >".$field->nama_status."</font>";
            }else{
                $nama_status = $field->status;
            }
            $category_kain  = " AND mc.nama_category LIKE '%kain hasil%' ";
            $status_kain    = $this->cek_status_kain($field->kode,$category_kain);
            $category_resep = " AND mc.nama_category IN ('DYE','AUX') ";
            $status_resep   = $this->cek_status_kain($field->kode,$category_resep);

            if($status_kain == 'Draft'  || $status_kain == 'Cancel'){
                $color      = "style='color: red' !important";
                $alias_stat_kain = 'Belum Tersedia';
            }else if($status_kain == 'Ready'|| $status_kain == 'Done'){
                $color      = "style='color: green' !important";
                $alias_stat_kain = 'Tersedia';
            }else{
                $color = '';
                $alias_stat_kain = '';
            }

            $method         = $id_dept.'|IN';
            $kode_in_kain   = $this->get_kode_in_kain($field->origin,$method);
            $kode_in_kain_enc = encrypt_url($kode_in_kain);
            $link_kain      = '<a href="'.base_url('warehouse/penerimaanbarang/edit/'.$kode_in_kain_enc).'" target="_blank" data-toggle="tooltip" title="No Dye IN :'.$kode_in_kain.'"" '.$color.'>'.$alias_stat_kain.'</a>';
            
            if($nama_status == 'Draft'){
                $color3      = "style='color: red' !important";
            }else if($nama_status == 'Ready'){
                $color3      = "style='color: green' !important";
            }else{
                $color3 = '';
            }

            $status_mg     = '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'" target="_blank" data-toggle="tooltip" title="MG Fin '.$kode_mrp.'" '.$color3.'>'.$nama_status.'</a>';

            $method          = $id_dept.'|OUT';
            $out_mg          = $this->get_kode_out_mg($field->origin,$method);
            $status_out      = $out_mg['1'];

            if($status_out == 'Draft'|| $status_out == 'Cancel'){
                $color4      = "style='color: red' !important";
            }else if($status_out == 'Ready' || $status_out == 'Done'){
                $color4      = "style='color: green' !important";
            }else{
                $color4 = '';
            }

            $kode_out_mg_enc = encrypt_url($out_mg[0]);
            $link_out      = '<a href="'.base_url('warehouse/pengirimanbarang/edit/'.$kode_out_mg_enc).'" target="_blank" data-toggle="tooltip" title="No Dye Out :'.$out_mg[0].'"" '.$color4.'>'.$status_out.'</a>';

            /*
            $kode_produk    = $field->kode_produk;
            $origin         = $field->origin;
            $qty1_std       = $field->qty1_std;
            $qty2_std       = $field->qty2_std;
            $lot_prefix     = $field->lot_prefix;
            $lot_prefix_waste = $field->lot_prefix_waste;
            $total_fg  = $this->m_mo->get_total_fg($kode_mrp);
            $sisa_qty       = $field->qty - $total_fg;
            $uom            = $field->uom;
            $move_id1 = $this->m_mo->get_move_id_rm_target_by_kode($kode)->row_array();
            $move_id_rm     = $move_id1['move_id'];
            */
            $move_id2 = $this->m_mo->get_move_id_fg_target_by_kode($field->kode)->row_array();  
            $move_id_fg     = $move_id2['move_id'];

            $checkbox      = '';
            $checkbox_2     = '';
            
            // get_ move id out
            $mv = $this->m_joblistfinishing->get_move_id_by_sourve_move($move_id_fg)->row_array();
            $move_id =  $mv['move_id'];
            //get move_id out by move_id prod
            $outs = $this->m_joblistfinishing->get_pengiriman_barang_by_move_id($move_id)->row_array();
            $kode_out  = $outs['kode'];
            $qc1_out   = $outs['qc_1'];
            $qc2_out   = $outs['qc_2'];
            $qc            = $this->m_joblistfinishing->get_quality_control_by_kode($kode_out,$field->dept_id)->row();

            if(!empty($qc->qc_1)){
                $qc_1 = $qc->qc_1;
                $check_qc_1 = "";
                if($qc1_out == 'true'){
                    $check_qc_1 = 'checked';
                }    
                $checkbox = '<input type="checkbox"  name="qc_2" id="qc_2" '.$check_qc_1.' onclick="return false;"> '.$qc_1 .'<br>';
            }

            if(!empty($qc->qc_2)){
              $qc_2 = $qc->qc_2;
              $check_qc_2 = "";
              if($qc2_out == 'true'){
                $check_qc_2 = 'checked';
                }    
                $checkbox_2 = '<input type="checkbox"  name="qc_2" id="qc_2" '.$check_qc_2.' onclick="return false;"> '.$qc_2 ;
            }
            $checkbox_qc = $checkbox.' '.$checkbox_2;

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'" target="_blank" data-toggle="tooltip" title="Lihat MG">'.$kode_mrp.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->origin;
            $row[] = $nama_produk;
            $row[] = $link_kain;
            $row[] = '';
            $row[] = $status_mg;
            $row[] = $checkbox_qc;
            $row[] = $link_out;

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
        //$kode_encrypt = encrypt_url($kode_in);
        return $kode_in;

    }

    function get_kode_out_mg($origin,$method)
    {
        // get kode outm mg 
        $kode     = $this->m_joblistfinishing->get_link_out_by_kode($origin,$method)->row_array();
        $kode_out = $kode['kode'];
        $status   = $kode['nama_status'];
        return array($kode_out,$status);

    }

}