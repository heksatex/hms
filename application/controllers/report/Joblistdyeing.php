<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Joblistdyeing extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        //$this->load->library('pagination');
        $this->load->model('m_joblistdyeing');
        $this->load->model('m_mo');
	}

	public function index()
	{
		$id_dept        = 'JLDYE';
        $data['id_dept']= $id_dept;
        $data['id_dept_asli'] = 'DYE' ;
		$this->load->view('report/v_job_list_dyeing', $data);
	}
	

	public function get_data()
	{

        $id_dept  = $this->input->post('id_dept');
	    $sub_menu = 'mO';
        $kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();

        $list = $this->m_joblistdyeing->get_datatables($id_dept,$kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        	$kode_encrypt = encrypt_url($field->kode);
            $kode_mrp       = $field->kode;
            $nama_produk    = $field->nama_produk;

            if($field->status == 'draft' || $field->status == 'cancel'){
                $nama_status ="<font color='red' >".$field->nama_status."</font>";
            }else if($field->status == 'ready'|| $field->status == 'done'){
                $nama_status ="<font color='green' >".$field->nama_status."</font>";
            }else{
                $nama_status = $field->status;
            }

            // get Link and status Kain
            //$category_kain  = " AND mc.nama_category LIKE '%kain hasil%' ";
            //$status_kain    = $this->cek_status_kain($field->kode,$category_kain);
            $method         = $id_dept.'|IN';
            $kain           = $this->get_kode_in_kain($field->origin,$method);
            $kode_in_kain   = $kain[0];
            $status_kain     = $kain[1];
            $kode_in_kain_enc = encrypt_url($kode_in_kain);

            if($status_kain == 'draft'  || $status_kain == 'cancel'){
                $color      = "style='color: red' !important";
                $alias_stat_kain = 'Belum Tersedia';
            }else if($status_kain == 'ready'){
                $color      = "style='color: blue' !important";
                $alias_stat_kain = 'Harus Diterima';
            }else if( $status_kain == 'done'){
                $color      = "style='color: Green' !important";
                $alias_stat_kain = 'Tersedia';
            }else{
                $color = '';
                $alias_stat_kain = '';
            }

            $link_kain      = '<a href="'.base_url('warehouse/penerimaanbarang/edit/'.$kode_in_kain_enc).'" target="_blank" data-toggle="tooltip" title="No Dye IN :'.$kode_in_kain.'"" '.$color.'>'.$alias_stat_kain.'</a>';


            // get Link and status Obat
            //$category_resep = " AND mc.nama_category IN ('DYE','AUX') ";
            //$status_resep   = $this->cek_status_obat($field->kode,$category_resep);

            $origin_obat    = $field->origin.'|'.$field->kode;  
            $method_out     = 'GOB|OUT';
            $obat           = $this->get_kode_out_obat($origin_obat,$method_out);
            $kode_out_obat   = $obat[0];
            $status_resep   = $obat[1];
            $kode_out_obat_enc = encrypt_url($kode_out_obat);

            if($status_resep == 'Draft' || $status_resep == 'Cancel'){
                $color2      = "style='color: red' !important";
                $alias_stat_resep = 'Belum Tersedia';
            }else if($status_resep == 'Ready'){
                $color2      = "style='color: blue' !important";
                $alias_stat_resep = 'Harus Dikirim';
            }else if($status_resep == 'Done'){
                $color2      = "style='color: green' !important";
                $alias_stat_resep = 'Tersedia';
            }else{
                $color2 = '';
                $alias_stat_resep = '';
            }

            //$link_obat        = '<div '.$color2.'>'.$alias_stat_resep.'</div>';
            $link_obat      = '<a href="'.base_url('warehouse/pengirimanbarang/edit/'.$kode_out_obat_enc).'" target="_blank" data-toggle="tooltip" title="No GOB Out :'.$kode_out_obat.'"" '.$color2.'>'.$alias_stat_resep.'</a>';


            if($nama_status == 'Draft'){
                $color3      = "style='color: red' !important";
            }else if($nama_status == 'Ready' ){
                $color3      = "style='color: green' !important";
            }else{
                $color3 = '';
            }

            $status_mg     = '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'" target="_blank" data-toggle="tooltip" title="MG Dye : '.$kode_mrp.'" '.$color3.'>'.$nama_status.'</a>';

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

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'" target="_blank" data-toggle="tooltip" title="Lihat MG Dye">'.$kode_mrp.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->origin;
            $row[] = $nama_produk;
            $row[] = $link_kain;
            $row[] = $link_obat;
            $row[] = $status_mg;
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_joblistdyeing->count_all($id_dept,$kode['kode']),
            "recordsFiltered" => $this->m_joblistdyeing->count_filtered($id_dept,$kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}


    function cek_status_kain($kode,$category)
    {

        $rs = $this->m_joblistdyeing->cek_item_rm_target($kode,$category);
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

        $rs = $this->m_joblistdyeing->cek_item_rm_target($kode,$category);
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
        $kode     = $this->m_joblistdyeing->get_link_kain_by_kode($origin,$method)->row_array();
        $kode_in  = $kode['kode'];
        $status_in = $kode['status'];
        return array($kode_in,$status_in);

    }

    function get_kode_out_obat($origin,$method)
    {
        // get kode in 
        $kode     = $this->m_joblistdyeing->get_link_out_by_kode($origin,$method)->row_array();
        $kode_in  = $kode['kode'];
        $status   = $kode['nama_status'];
        return array($kode_in,$status);

    }

    function get_kode_out_mg($origin,$method)
    {
        // get kode outm mg 
        $kode     = $this->m_joblistdyeing->get_link_out_by_kode($origin,$method)->row_array();
        $kode_out = $kode['kode'];
        $status   = $kode['nama_status'];
        return array($kode_out,$status);

    }

}