<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Joblistqcfinishing extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        $this->load->model('m_joblistqcfinishing');
        $this->load->model('m_mo');
	}

	public function index()// joblist untuk dept id nya (SET,BRS,PAD,FIN,FBR)
	{
		$id_dept        = 'JLQCFIN';
        $data['id_dept']= $id_dept;
        $data['id_dept_asli'] = 'FIN' ;
		$this->load->view('report/v_job_list_qc_finishing', $data);
	}
	

	public function get_data()
	{

        $id_dept  = $this->input->post('id_dept');
	    $sub_menu = 'mO';
        $kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();

        $id_dept_all = "('SET','PAD','BRS','FIN','FBR')";
        $list = $this->m_joblistqcfinishing->get_datatables($id_dept_all,$kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        	$kode_encrypt = encrypt_url($field->kode);
            $kode_mrp     = $field->kode;
            $nama_produk  = $field->nama_produk;
            $pb_dept      = $field->pb_dept;
            $mrp_dept     = $field->pb_dept;

            if($field->status == 'draft' || $field->status == 'cancel'){
                $nama_status ="<font color='red' >".$field->nama_status."</font>";
            }else if($field->status == 'ready'|| $field->status == 'done'){
                $nama_status ="<font color='green' >".$field->nama_status."</font>";
            }else{
                $nama_status = $field->status;
            }
            
            if($nama_status == 'Draft'){
                $color3      = "style='color: red' !important";
            }else if($nama_status == 'Ready' ){
                $color3      = "style='color: green' !important";
            }else{
                $color3 = '';
            }

            $status_mg     = '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'" target="_blank" data-toggle="tooltip" title="MG '.$mrp_dept.' : '.$kode_mrp.'" '.$color3.'>'.$nama_status.'</a>';

            $method          = $pb_dept.'|OUT';
            //$out_mg          = $this->get_kode_out_mg($field->origin,$method);
            //$kode_out_mg     = $out_mg['0'];
            //$status_out      = $out_mg['1'];

            $kode_out_mg     = "";
            $status_out      = "";

            // bandingin kode Out asli dan dari out_mg
            if($kode_out_mg != $field->pb_kode){
                $info_backorder = ' (Backorder)';
            }else{
                $info_backorder = '';
            }

            if($status_out == 'Draft'|| $status_out == 'Cancel'){
                $color4      = "style='color: red' !important";
            }else if($status_out == 'Ready' || $status_out == 'Done'){
                $color4      = "style='color: green' !important";
            }else{
                $color4 = '';
            }

            $kode_out_mg_enc = encrypt_url($kode_out_mg);
            $link_out        = '<a href="'.base_url('warehouse/pengirimanbarang/edit/'.$kode_out_mg_enc).'" target="_blank" data-toggle="tooltip" title="No '.$pb_dept.' Out :'.$kode_out_mg.'"" '.$color4.'>'.$status_out.'</a> '.$info_backorder;

            // cek departemen yg pakai qc
            $cek_qc_dept = $this->m_joblistqcfinishing->cek_quality_control_by_dept($pb_dept)->num_rows();

            if($cek_qc_dept > 0){ // jika ada qc

                $move_id2       = $this->m_mo->get_move_id_fg_target_by_kode($field->kode)->row_array();  
                $move_id_fg     = $move_id2['move_id'];
                $checkbox       = '';
                $checkbox_2     = '';
                
                // get_ move id out
                $mv = $this->m_joblistqcfinishing->get_move_id_by_sourve_move($move_id_fg)->row_array();
                $move_id =  $mv['move_id'];
                //get move_id out by move_id prod
                $outs = $this->m_joblistqcfinishing->get_pengiriman_barang_by_move_id($move_id)->row_array();
                $kode_out  = $outs['kode'];
                $qc1_out   = $outs['qc_1'];
                $qc2_out   = $outs['qc_2'];
                $qc        = $this->m_joblistqcfinishing->get_quality_control_by_kode($kode_out,$field->dept_id)->row();

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
            }else{
                $checkbox_qc = '';
            }
            


            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->nama_departemen;
            $row[] = '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'" target="_blank" data-toggle="tooltip" title="Lihat MG Dye">'.$kode_mrp.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->origin;
            $row[] = $nama_produk;
            $row[] = $status_mg;
            $row[] = $checkbox_qc;
            $row[] = $link_out;
            $data[] = $row;

            $link_out = '';
            
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_joblistqcfinishing->count_all($id_dept_all,$kode['kode']),
            "recordsFiltered" => $this->m_joblistqcfinishing->count_filtered($id_dept_all,$kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}


    function cek_status_kain($kode,$category)
    {

        $rs = $this->m_joblistqcfinishing->cek_item_rm_target($kode,$category);
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


    function cek_status_obat($kode,$category)
    {

        $rs = $this->m_joblistqcfinishing->cek_item_rm_target($kode,$category);
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


    function get_kode_out_mg($origin,$method)
    {
        // get kode outm mg 
        $kode     = $this->m_joblistqcfinishing->get_link_out_by_kode($origin,$method)->row_array();
        $kode_out = $kode['kode'];
        $status   = $kode['nama_status'];
        return array($kode_out,$status);

    }

}