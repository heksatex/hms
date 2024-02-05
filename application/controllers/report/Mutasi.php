<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Mutasi extends MY_Controller
{

    public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('m_mutasi');
		$this->load->model('_module');
		$this->load->model('m_produk');
        $this->load->library('pagination');
	}

    public function index()
	{
		$id_dept        = 'MTSI';
        $data['id_dept']= $id_dept;
        $data['jenis_kain'] = $this->m_produk->get_list_jenis_kain();    
        $data['route']      = $this->m_mutasi->get_list_route_co();
		$this->load->view('report/v_mutasi', $data);
	}

    function loadData($record=0)
    {

        $table_view     = $this->input->post('table');
        $arr_filter     = $this->input->post('arr_filter');
        $tanggal        = '';
        $departemen     = '';
        $kode_produk     = '';
        $nama_produk    = '';
        $kode_transaksi = '';
        $reff_note      = '';
        $reff_picking   = '';
        $lot            = '';
        $parent         = '';
        $warna          = '';
        $no_go          = '';
        $jenis_kain     = '';
        $route          = '';
        $view           = 'Global';
        $empty          = false;
        foreach($arr_filter as $filter){

            if($filter['tanggal'] != ''){
                $tanggal     = $filter['tanggal'];
            }else{
                $empty       = true;
                $name_field  = "Periode";
            }   
            
            if($filter['departemen']!= ''){
                $departemen  = $filter['departemen'];
            }else{
                $empty       = true;
                $name_field  = "Departemen";
            }

            if($filter['kode_produk'] != ''){
                $kode_produk = $filter['kode_produk'];
            }

            if($filter['nama_produk'] != ''){
                $nama_produk = $filter['nama_produk'];
            }

            if($filter['kode_transaksi'] != ''){
                $kode_transaksi = $filter['kode_transaksi'];
            }

            if($filter['lot'] != ''){
                $lot = $filter['lot'];
            }

            if($filter['reff_picking'] != ''){
                $reff_picking = $filter['reff_picking'];
            }

            if($filter['reff_note'] != ''){
                $reff_note = $filter['reff_note'];
            }

            if($filter['parent'] != ''){
                $parent = $filter['parent'];
            }

            if($filter['warna'] != ''){
                $warna = $filter['warna'];
            }

            if($filter['no_go'] != ''){
                $no_go = $filter['no_go'];
            }

            if($filter['route'] != ''){
                $route = $filter['route'];
            }

            if($filter['jenis_kain'] != ''){
                $jenis_kain = $filter['jenis_kain'];
            }


            foreach($filter['view_arr'] as $val2){
                $view = $val2;
                break;
            }
        }

        $tahun      = date('Y', strtotime($tanggal)); // example 2022
        $bulan      = date('n', strtotime($tanggal)); // example 8
        $table_mutasi      = [];

        $recordPerPage = 100;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }

        //cek departemen yg ada mutasi
        $result = $this->cek_dept_mutasi($departemen);
        if($result == true){
            // cek departemen detail produk data
            $result_2 = $this->cek_dept_mutasi_detail_produk_datar($departemen);
            if($view == "DetailLotDatar" AND $result_2 == false){
                $get_dept  = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
                $callback = array('status'=>'failed', 'view' => $view, 'message'=> "Departemen ".$get_dept['nama']." belum terdapat Laporan Mutasi dengan View Detail Datar", 'result'=>$table_mutasi);
            }else if($departemen == 'DYE2'or $departemen == 'FIN' OR $departemen == 'DF'){
                
                if($view == "Global"){ 
                    $result_where = $this->create_where2($parent,'','','','','',$jenis_kain);
                }else if($view == "DetailProduk"){
                    $result_where = $this->create_where2($parent,$nama_produk,'','','','',$jenis_kain);
                }else{
                    $result_where = $this->create_where2($parent,$nama_produk,$warna,$lot,$no_go,$route,$jenis_kain);
                }
                $result = $this->create_header_detail2($view,false,$departemen);
                $r_result     = $result[0];// judul tabel
                $table_field  = $result[1];// field dan nama tabel
                $table_field_view  = $result[2];// field view

                $acc_mutasi_rm  = $this->get_acc_mutasi_by_kode2($view,$table_field,$tahun,$bulan,$result_where,$record,$recordPerPage,false);
                $pagination     = $this->get_pagination($acc_mutasi_rm[2],$recordPerPage);

                $table_mutasi[] = array('table_1'       => 'Yes',
                                        'head_table1'   => $r_result,
                                        'pagination'    => $pagination,
                                        'record'        => $acc_mutasi_rm[0],
                                        'count_record'  => number_format($acc_mutasi_rm[2]),
                                        'field_view'    => $table_field_view
                                );

                $callback = array('status'=>'succes','view' =>$view, 'format' => '2','result'=>$table_mutasi);

            }else{
                // cek tipe departemen
                $get_dept  = $this->_module->get_nama_dept_by_kode($departemen);
                $type_dept = $get_dept->row_array();

                if($view == "Global" or $view == "DetailProduk"){

                    if($type_dept['type_dept'] == 'manufaktur'){
                        if($table_view == 'rm' or $table_view == 'all'){
                        // rm
                        $mutasi_dept_rm = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'rm')->result();
                        if($view == "Global"){
                            $table          = 'acc_mutasi_'.strtolower($departemen).'_rm_global';
                            $result_where   = $this->create_where('',$nama_produk,'','','','');
                        }else{ // detailproduk
                            $table          = 'acc_mutasi_'.strtolower($departemen).'_rm';
                            $result_where   = $this->create_where($kode_produk,$nama_produk,'','','','');
                        }
                        $result         = $this->create_header($view,$mutasi_dept_rm,false);

                        $rm_field          = $result[0];
                        $rm_head_table1    = $result[1];
                        $rm_head_table2    = $result[2];
                        $rm_jml_in         = $result[3];
                        $rm_jml_out        = $result[4];
                        $acc_mutasi_rm     = $this->get_acc_mutasi_by_kode($view,$table,$tahun,$bulan,$rm_field,$result_where);

                        $table_mutasi[] = array('table_1'       => 'Yes',
                                                'record'        => $acc_mutasi_rm[0], 
                                                'count_record'  => number_format($acc_mutasi_rm[1]),
                                                'head_table1'   => $rm_head_table1, 
                                                'head_table2'   => $rm_head_table2, 
                                                'count_in'      => $rm_jml_in, 
                                                'count_out'     => $rm_jml_out,
                                                'pagination'    => '');
                        }

                        if($table_view == 'fg' or $table_view == 'all'){

                        // fg
                        $mutasi_dept_fg = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'fg')->result();
                        if($view == "Global"){
                            $table2         = 'acc_mutasi_'.strtolower($departemen).'_fg_global';
                            $result_where   = $this->create_where('',$nama_produk,'','','','');
                        }else{ // detailproduk
                            $table2         = 'acc_mutasi_'.strtolower($departemen).'_fg';
                            $result_where   = $this->create_where($kode_produk,$nama_produk,'','','','');
                        }
                        $result2        = $this->create_header($view,$mutasi_dept_fg,false);
                        $fg_field          = $result2[0];
                        $fg_head_table1    = $result2[1];
                        $fg_head_table2    = $result2[2];
                        $fg_jml_in         = $result2[3];
                        $fg_jml_out        = $result2[4];
                        $acc_mutasi_fg  = $this->get_acc_mutasi_by_kode($view,$table2,$tahun,$bulan,$fg_field,$result_where);

                        $table_mutasi[]    = array('table_2'       => 'Yes',
                                                    'record'        => $acc_mutasi_fg[0],
                                                    'count_record'  => number_format($acc_mutasi_fg[1]),
                                                    'head_table1'   => $fg_head_table1, 
                                                    'head_table2'   => $fg_head_table2, 
                                                    'count_in'      => $fg_jml_in, 
                                                    'count_out'     => $fg_jml_out,
                                                    'pagination'    => '');
                        }

                    }else{

                        if($table_view == 'rm' or $table_view == 'all' ){

                            $mutasi_dept = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'')->result();
                            if($view == "Global"){
                                $table       = 'acc_mutasi_'.strtolower($departemen).'_global';
                                $result_where= $this->create_where('',$nama_produk,'','','','');                          
                            }else{ // detailproduk
                                $table       = 'acc_mutasi_'.strtolower($departemen);
                                $result_where= $this->create_where($kode_produk,$nama_produk,'','','','');
                            }
                            $result      = $this->create_header($view,$mutasi_dept,false);

                            $field       = $result[0];
                            $head_table1 = $result[1];
                            $head_table2 = $result[2];
                            $jml_in      = $result[3];
                            $jml_out     = $result[4];

                            // $acc_mutasi  = $this->m_mutasi->acc_mutasi_by_kode($table,$tahun,$bulan,$field)->result();
                            $acc_mutasi  = $this->get_acc_mutasi_by_kode($view,$table,$tahun,$bulan,$field,$result_where);
                            $table_mutasi[]    = array('table_1'       => 'Yes',
                                                        'record'        => $acc_mutasi[0], 
                                                        'count_record'  => number_format($acc_mutasi[1]),
                                                        'head_table1'   => $head_table1, 
                                                        'head_table2'   => $head_table2, 
                                                        'count_in'      => $jml_in, 
                                                        'count_out'     => $jml_out,
                                                        'pagination'    => '');
                            $table_mutasi[]    = array('table_2'       => 'No',
                                                        'record'        => '', 
                                                        'count_record'  => '',
                                                        'head_table1'   => '', 
                                                        'head_table2'   => '', 
                                                        'count_in'      => '', 
                                                        'count_out'     => '',
                                                        'pagination'    => '');
                        }

                    }
                    $callback = array('status'=>'success', 'view' => $view, 'format' => '1', 'result'=>$table_mutasi);

                }else{ // Detail

                    if($type_dept['type_dept'] == 'manufaktur'){

                        if($table_view == 'rm' or $table_view == 'all'){

                            $table          = 'acc_mutasi_'.strtolower($departemen).'_rm_detail';
                            $result         = $this->create_header_detail(false);
                            $result_where   = $this->create_where($kode_produk,$nama_produk,$kode_transaksi,$lot,$reff_picking,$reff_note);
                            $acc_mutasi_rm  = $this->get_acc_mutasi_detail_by_kode($table,$tahun,$bulan,$result_where,$record,$recordPerPage,false);
                            $pagination_rm  = $this->get_pagination($acc_mutasi_rm[1],$recordPerPage);
                            $table_mutasi[] =  array('table_1'          => 'Yes',
                                                    'head_table'        => $result,
                                                    'record'            => $acc_mutasi_rm[0],
                                                    'count_record'      => number_format($acc_mutasi_rm[1]),
                                                    'pagination'        => $pagination_rm);
                        }

                        if($table_view == 'fg' or $table_view == 'all'){

                            $table          = 'acc_mutasi_'.strtolower($departemen).'_fg_detail';
                            $result         = $this->create_header_detail(false);
                            $result_where   = $this->create_where($kode_produk,$nama_produk,$kode_transaksi,$lot,$reff_picking,$reff_note);
                            $acc_mutasi_fg  = $this->get_acc_mutasi_detail_by_kode($table,$tahun,$bulan,$result_where,$record,$recordPerPage,false);
                            $pagination_fg  = $this->get_pagination($acc_mutasi_fg[1],$recordPerPage);

                            $table_mutasi[] =  array('table_2'          => 'Yes',
                                                    'head_table'        => $result,
                                                    'record'            => $acc_mutasi_fg[0],
                                                    'count_record'      => number_format($acc_mutasi_fg[1]),
                                                    'pagination'        => $pagination_fg);
                        }
                                                
                    }else{// gudang

                        if($table_view == 'rm' or $table_view == 'all'){

                            if($view == "DetailLotDatar"){
                                $mutasi_dept = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'')->result();
                                $table       = 'acc_mutasi_'.strtolower($departemen).'_detail_datar';
                                $result_where= $this->create_where3($kode_produk,$nama_produk,$lot,$parent,$jenis_kain);
                                $result      = $this->create_header($view,$mutasi_dept,false);
                                $field       = $result[0];
                                $head_table1 = $result[1];
                                $head_table2 = $result[2];
                                $jml_in      = $result[3];
                                $jml_out     = $result[4];
                                $jml_type_in = $result[5];
                                $jml_type_out = $result[6];
                                $acc_mutasi  = $this->get_acc_mutasi_detail_datar_by_kode($table,$tahun,$bulan,$field,$result_where,$record,$recordPerPage,false);
                                $pagination = $this->get_pagination($acc_mutasi[1],$recordPerPage);

                                $table_mutasi[]    = array('table_1'       => 'Yes',
                                                                'record'        => $acc_mutasi[0], 
                                                                'count_record'  => number_format($acc_mutasi[1]),
                                                                'head_table1'   => $head_table1, 
                                                                'head_table2'   => $head_table2, 
                                                                'count_in'      => $jml_in, 
                                                                'count_out'     => $jml_out,
                                                                'count_type_in' => $jml_type_in,
                                                                'count_type_out'=> $jml_type_out,
                                                                'pagination'    => $pagination);
                                $table_mutasi[]    = array('table_2'       => 'No',
                                                            'record'        => '', 
                                                            'count_record'  => '',
                                                            'head_table1'   => '', 
                                                            'head_table2'   => '', 
                                                            'count_in'      => '', 
                                                            'count_out'     => '',
                                                            'count_type_in' => '',
                                                            'count_type_out'=> '',
                                                            'pagination'    => '');
                            }else {
                                $table       = 'acc_mutasi_'.strtolower($departemen).'_detail';
                                $result      = $this->create_header_detail(false);
                                $result_where= $this->create_where($kode_produk,$nama_produk,$kode_transaksi,$lot,$reff_picking,$reff_note);
                                $acc_mutasi  = $this->get_acc_mutasi_detail_by_kode($table,$tahun,$bulan,$result_where,$record,$recordPerPage,false);
                                $head_table1 = "";
                                $head_table2 = "";
                                $jml_in      = "";
                                $jml_out     = "";
                                $pagination = $this->get_pagination($acc_mutasi[1],$recordPerPage);
                                $table_mutasi[] =  array('table_1'          => 'Yes',
                                                        'head_table'        => $result,
                                                        'record'            => $acc_mutasi[0],
                                                        'count_record'      => number_format($acc_mutasi[1]),
                                                        'pagination'        => $pagination);
    
                                $table_mutasi[] =  array('table_2'          => 'No',
                                                        'head_table'        => '',
                                                        'record'            => '',
                                                        'count_record'      => '',
                                                        'pagination'        => '');
                            }
                            

                            
                            
                        }

                    }

                    $callback = array('status'=>'succes','view' =>$view, 'format' => '1','result'=>$table_mutasi);

                }
            }

        }else if($empty == true){
            $callback = array('status'=>'failed', 'view' => $view, 'message'=> $name_field." Harus diisi !", 'result'=>$table_mutasi);
        }else{
            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
            $callback = array('status'=>'failed', 'view' => $view, 'message'=> "Departemen ".$get_dept['nama']." belum terdapat Laporan Mutasi", 'result'=>$table_mutasi);
        }

        echo json_encode($callback);
    }

    function cek_dept_mutasi($dept)
    {
        $list_dept_mutasi = array('GDB','WRD','TWS','WRP','TRI','JAC','CS','INS1','GRG','DYE','GOB','DYE2','FIN','DF');
        $dept_status      = false;
        foreach($list_dept_mutasi as $list){
            if($list == $dept){
                $dept_status = true;
                break;
            }
        }
        return $dept_status;
    }

    function cek_dept_mutasi_bap($dept)
    {
        $list_dept_mutasi = array('GDB','WRD','TWS','WRP','TRI','JAC','CS','INS1','GRG','DYE','GOB');
        $dept_status      = false;
        foreach($list_dept_mutasi as $list){
            if($list == $dept){
                $dept_status = true;
                break;
            }
        }
        return $dept_status;
    }

    function cek_dept_mutasi_detail_produk_datar($dept)
    {
        $list_dept_mutasi = array('GRG');
        $dept_status      = false;
        foreach($list_dept_mutasi as $list){
            if($list == $dept){
                $dept_status = true;
                break;
            }
        }
        return $dept_status;
    }

    function list_dept_mutasi($search)
    {
        $list_dept_mutasi = array(
            array('kode' => 'GDB', 'nama'=>'Gudang Benang'),
            array('kode' => 'WRD', 'nama'=>'Warping Dasar'),
            array('kode' => 'WRP', 'nama'=>'Warping Panjang'),
            array('kode' => 'TWS', 'nama'=>'Twisting'),
            array('kode' => 'TRI', 'nama'=>'Tricot'),
            array('kode' => 'JAC', 'nama'=>'Jacquard'),
            array('kode' => 'CS',  'nama'=>'Cutting Shearing'),
            array('kode' => 'INS1','nama'=>'Inspecting '),
            array('kode' => 'GRG', 'nama'=>'Greige'),
            array('kode' => 'DYE', 'nama'=>'Dyeing'),
            array('kode' => 'DYE2','nama'=>'Dyeing 2'),
            array('kode' => 'FIN', 'nama'=>'Finishing'),
            array('kode' => 'DF',  'nama'=>'Dyeing Finishing'),
        );


        usort($list_dept_mutasi, $this->build_sorter('nama'));
        
        if(!empty($search)){
            $list  = array();           
            foreach($list_dept_mutasi as $dept){
                if(strpos(strtolower($dept['nama']), $search) !== FALSE) {
                    $list[] = (object) array('kode'=>$dept['kode'], 'nama'=>$dept['nama']);
                }
            }
            return $list;
        }else{
            return $list_dept_mutasi;
        }
    }

    function build_sorter($key) {
        return function ($a, $b) use ($key) {
            return strnatcmp($a[$key], $b[$key]);
        };
    }

    function get_departement_mutasi_select2()
	{
		$nama  = addslashes($this->input->post('nama'));
   		$callback = $this->list_dept_mutasi($nama);
        echo json_encode($callback);
	}

    function get_pagination($allcount,$recordPerPage)
    {
        $config['base_url']         = base_url().'report/mutasi/loadData';
        $config['use_page_numbers'] = TRUE;
        $config['total_rows']       = $allcount;
        $config['per_page']         = $recordPerPage;
                                
        //$config['first_link']     = FALSE;
        //$config['last_link']      = FALSE;
        $config['num_links']        = 1;
        $config['next_link']        = '>';
        $config['prev_link']        = '<';
        $this->pagination->initialize($config);
        $pagination         = $this->pagination->create_links();

        return $pagination;
    }

    function get_acc_mutasi_by_kode($view,$table,$tahun,$bulan,$field,$where)
    {
        if($view == "Global"){
            $query      = $this->m_mutasi->acc_mutasi_by_kode($table,$tahun,$bulan,$field,$where);
        }else{ // detailproduk
            $query      = $this->m_mutasi->acc_mutasi_by_kode2($table,$tahun,$bulan,$field,$where); // kategori
        }
        $result     = $query->result_array();
        $result2    = $query->num_rows();
        return array($result,$result2);
    }

    function get_acc_mutasi_by_kode2($view,$table,$tahun,$bulan,$where,$record,$recordPerPage,$excel)
    {
        $result     = "";
        $result2    = "";

        if(!empty($table)){
            
            $field  = '';
            $nama_tabel   = '';
            foreach($table as $tables){
                $field          = $tables['field'];
                $nama_tabel  = $tables['nama_tabel'];
            }
            if($view == 'Global'){
                $order = " ORDER BY product_parent asc ";
            }else if($view == "DetailProduk"){
                $order = " ORDER BY nama_produk asc ";
            }else{
                $order = " ORDER BY nama_produk, lot asc ";
            }
            if($excel == false){
                $query = $this->m_mutasi->acc_mutasi_df_by_kode($field,$nama_tabel,$tahun,$bulan,$where,$order,$record,$recordPerPage);
                $result = $query->result_array();
            }
            $query2 = $this->m_mutasi->acc_mutasi_df_by_kode_no_limit($field,$nama_tabel,$tahun,$bulan,$where,$order);
            $result2    = $query2->result_array();
            $result3    = $query2->num_rows();
        }

        return array($result,$result2,$result3);
    }

    function get_acc_mutasi_detail_by_kode($table,$tahun,$bulan,$where,$record,$recordPerPage,$excel)
    {
        $result  = '';
        if($excel == false){
            $query  = $this->m_mutasi->acc_mutasi_detail_by_kode($table,$tahun,$bulan,$where,$record,$recordPerPage);
            $result = $query->result();
        }
        $query2  = $this->m_mutasi->acc_mutasi_detail_by_kode_no_limit($table,$tahun,$bulan,$where);
        $result2 = $query2->num_rows();
        $result3 = $query2->result();
        return array($result,$result2,$result3);
    }

    function get_acc_mutasi_detail_datar_by_kode($table,$tahun,$bulan,$field,$where,$record,$recordPerPage,$excel)
    {
        $result  = '';
        if($excel == false){
            $query  = $this->m_mutasi->acc_mutasi_by_kode_datar_limit($table,$field,$tahun,$bulan,$where,$record,$recordPerPage);
            $result = $query->result();
        }
        $query2  = $this->m_mutasi->acc_mutasi_by_kode_datar($table,$field,$tahun,$bulan,$where);
        $result2 = $query2->num_rows();
        $result3 = $query2->result_array();
        return array($result,$result2,$result3);
    }
    

    function create_where($kode_produk,$nama_produk,$kode_transaksi,$lot,$reff_picking,$reff_note)
    {   
        $where = '';
        if($kode_produk != ''){
            $where  .= " AND kode_produk LIKE '%".addslashes($kode_produk)."%' ";
        }
        if($nama_produk != ''){
            $where  .= " AND nama_produk LIKE '%".addslashes($nama_produk)."%' ";
        }
        if($kode_transaksi != ''){
            $where  .= " AND m.kode_transaksi LIKE '%".addslashes($kode_transaksi)."%' ";
        }
        if($lot != ''){
            $where  .= " AND m.lot LIKE '%".addslashes($lot)."%' ";
        }
        if($reff_picking != ''){
            $where  .= " AND m.reff_picking LIKE '%".addslashes($reff_picking)."%' ";
        }
        if($reff_note != ''){
            $where  .= " AND m.reff_note LIKE '%".addslashes($reff_note)."%' ";
        }

        return $where;
    }


    function create_where2($parent,$nama_produk,$warna,$lot,$no_go,$route,$jenis_kain)
    {   
        $where = '';
        if($parent != ''){
            $where  .= " AND product_parent LIKE '%".addslashes($parent)."%' ";
        }
        if($nama_produk != ''){
            $where  .= " AND nama_produk LIKE '%".addslashes($nama_produk)."%' ";
        }
        if($warna != ''){
            $where  .= " AND warna LIKE '%".addslashes($warna)."%' ";
        }
        if($lot != ''){
            $where  .= " AND lot LIKE '%".addslashes($lot)."%' ";
        }
        if($no_go != ''){
            $where  .= " AND no_go LIKE '%".addslashes($no_go)."%' ";
        }
        if($route != ''){
            $where  .= " AND route LIKE '%".addslashes($route)."%' ";
        }
        if($jenis_kain != ''){
            $where  .= " AND nama_jenis_kain LIKE '%".addslashes($jenis_kain)."%' ";
        }

        return $where;
    }

    function create_where3($kode_produk,$nama_produk,$lot,$parent,$jenis_kain)
    {   
        $where = '';
        if($kode_produk != ''){
            $where  .= " AND kode_produk LIKE '%".addslashes($kode_produk)."%' ";
        }
        if($lot != ''){
            $where  .= " AND lot LIKE '%".addslashes($lot)."%' ";
        }
        if($parent != ''){
            $where  .= " AND product_parent LIKE '%".addslashes($parent)."%' ";
        }
        if($nama_produk != ''){
            $where  .= " AND nama_produk LIKE '%".addslashes($nama_produk)."%' ";
        }
        if($jenis_kain != ''){
            $where  .= " AND nama_jenis_kain LIKE '%".addslashes($jenis_kain)."%' ";
        }

        return $where;
    }

    function create_header($view,$mutasi_dept,$excel)
    {
        $field    = '';
        $no_in    = 1;
        $no_out   = 1;
        $head_table1        = [];
        $head_table2        = [];
        $head_table1_in     = [];
        $head_table1_out    = [];
        $head_table2_in     = [];
        $head_table2_out    = [];
        $head_table2_awal   = [];
        $head_table2_akhir  = [];
        // field saldo awal
        if($view == "Global"){
            // $field   .= 'nama_produk, s_awal_lot, s_awal_qty1, s_awal_qty1_uom, s_awal_qty2,  s_awal_qty2_uom, s_awal_qty_opname, s_awal_qty_opname_uom, '; 
            $field   .= 'nama_produk, s_awal_lot, s_awal_mtr, s_awal_kg, '; 
            $field   .= 's_akhir_lot, s_akhir_mtr, s_akhir_kg, '; 
            $field   .= 'adj_in_lot, adj_in_mtr, adj_in_kg, '; 
            $field   .= 'adj_out_lot, adj_out_mtr, adj_out_kg, '; 

            $head_table2_awal[]  = array('Lot','Mtr','Kg');
            $head_table2_adj[]   = array('Lot','Mtr','Kg');

        }else{
            if($view =="DetailLotDatar"){
                $field   .= 'lot,nama_jenis_kain, ';
            }
            $field   .= 'nama_category, id_category, kode_produk, nama_produk, product_parent, s_awal_lot, s_awal_qty1, s_awal_qty1_uom, s_awal_qty2,  s_awal_qty2_uom, s_awal_qty_opname, s_awal_qty_opname_uom, '; 
            // field saldo akhir
            $field   .= 's_akhir_lot, s_akhir_qty1, s_akhir_qty1_uom, s_akhir_qty2, s_akhir_qty2_uom, s_akhir_qty_opname, s_akhir_qty_opname_uom, ';
            // field adj in 
            $field  .= 'adj_in_lot, adj_in_qty1, adj_in_qty1_uom, adj_in_qty2, adj_in_qty2_uom, adj_in_qty_opname, adj_in_qty_opname_uom, ';
            // field adj out
            $field  .= 'adj_out_lot, adj_out_qty1, adj_out_qty1_uom, adj_out_qty2, adj_out_qty2_uom, adj_out_qty_opname, adj_out_qty_opname_uom, ';

            // $head_table2_awal[]  = array('S Awal Lot','S Awal Qty1','S Awal Qty2','S Awal Qty Opname');
            if($excel == true){
                $head_table2_awal[]  = array('Lot','Qty1','Uom1','Qty2','Uom2','Qty Opname','Uom Opname');
            }else{
                $head_table2_awal[]  = array('Lot','Qty1','Qty2','Qty Opname');
            }
            // $head_table2_akhir[] = array('S Akhir Lot','S Akhir Qty1','S Akhir Qty2','S Akhir Qty Opname');
            if($excel == true){
                $head_table2_adj[]   = array(' Adj Lot',' Adj Qty1','Uom1',' Adj Qty2', 'Uom2',' Adj Qty Opname','Uom Opname');
            }else{
                $head_table2_adj[]   = array(' Adj Lot',' Adj Qty1',' Adj Qty2',' Adj Qty Opname');
            }
            // $head_table2_adj[]   = array('S Adj Lot','S Adj Qty1','S Adj Qty2','S Adj Qty Opname');
        }


        $jml_in   = 0;
        $jml_out  = 0;

        foreach($mutasi_dept as $mts){
                if (strpos($mts->seq, 'in') !== FALSE) {
                    if($view == "Global"){
                        $in_lot         = 'in'.$no_in.'_lot';
                        $in_mtr         = 'in'.$no_in.'_mtr';
                        $in_kg          = 'in'.$no_in.'_kg';
    
                        $field .=  $in_lot.', '.$in_mtr.', '.$in_kg.', ';

                    }else{
                        $in_lot         = 'in'.$no_in.'_lot';
                        $in_qty1        = 'in'.$no_in.'_qty1';
                        $in_qty1_uom    = 'in'.$no_in.'_qty1_uom';
                        $in_qty2        = 'in'.$no_in.'_qty2';
                        $in_qty2_uom    = 'in'.$no_in.'_qty2_uom';
                        $in_opname      = 'in'.$no_in.'_qty_opname';
                        $in_opname_uom  = 'in'.$no_in.'_qty_opname_uom';
    
                        $field .=  $in_lot.', '.$in_qty1.', '.$in_qty1_uom.', '.$in_qty2.', '.$in_qty2_uom.', '.$in_opname.', '.$in_opname_uom.', ';
                    }
                    $no_in++;
                    if($view == "Global"){
                        $head_table2_in[]  = array('Lot','Mtr','Kg');
                    }else{

                        if($excel == true){
                            $head_table2_in[]  = array('Lot','Qty1','Uom1','Qty2','Uom2','Qty Opname','Uom Opname');
                        }else{
                            $head_table2_in[] = array('Lot','Qty1','Qty2', 'Qty Opname');
                        }
                    }
                    $jml_in++;
                    if($mts->type == 'prod'){
                        if(strpos($mts->dept_id_tujuan, 'Waste') !== FALSE){
                            $judul_column_group_in = 'Penerimaan dari '.$mts->dept_id_tujuan;
                        }else{
                            $judul_column_group_in = 'Hasil Produksi '.$mts->dept_id_dari.'';   
                        }
                    }else{
                        $judul_column_group_in = 'Penerimaan dari '.$mts->dept_id_dari;
                    }
                    $head_table1_in[] = array($judul_column_group_in);
                    
                }

                if (strpos($mts->seq, 'out') !== FALSE) {

                    if($view == "Global"){
                        $out_lot         = 'out'.$no_out.'_lot';
                        $out_mtr         = 'out'.$no_out.'_mtr';
                        $out_kg          = 'out'.$no_out.'_kg';
    
                        $field .=  $out_lot.', '.$out_mtr.', '.$out_kg.', ';
                    }else{
                        $out_lot         = 'out'.$no_out.'_lot';
                        $out_qty1        = 'out'.$no_out.'_qty1';
                        $out_qty1_uom    = 'out'.$no_out.'_qty1_uom';
                        $out_qty2        = 'out'.$no_out.'_qty2';
                        $out_qty2_uom    = 'out'.$no_out.'_qty2_uom';
                        $out_opname      = 'out'.$no_out.'_qty_opname';
                        $out_opname_uom  = 'out'.$no_out.'_qty_opname_uom';
                        
                        $field .=  $out_lot.', '.$out_qty1.', '.$out_qty1_uom.', '.$out_qty2.', '.$out_qty2_uom.', '.$out_opname.', '.$out_opname_uom.', ';
                    }

                    $no_out++;

                    if($view == "Global"){
                        $head_table2_out[]  = array('Lot','Mtr','Kg');
                    }else{
                        if($excel == true){
                            $head_table2_out[]  = array('Lot','Qty1','Uom1','Qty2','Uom2','Qty Opname','Uom Opname');
                        }else{
                            $head_table2_out[] = array('Lot','Qty1','Qty2', 'Qty Opname');
                        }
                    }

                    $jml_out++;
                    if($mts->type == 'con'){
                        $judul_column_group_out = 'Bahan Baku Dikomsumsi '.$mts->dept_id_dari.'';
                    }else{
                        $judul_column_group_out = 'Pengiriman ke '.$mts->dept_id_tujuan;
                    }
                    $head_table1_out[] = array($judul_column_group_out);
                }
        }
        $head_in   = array();
        $head_out  = array();
        $field_in  = array();
        $count_type_in = 0;
        $count_type_out = 0;
        if($view == "DetailLotDatar"){


            $list_type = $this->m_mutasi->get_list_type_adjustment();
            $num       = 2;
           

            if($excel == true){
                $child  = array('Lot','Qty1','Uom1','Qty2','Uom2','Qty Opname','Uom Opname');
                $count_child_head = 7;
            }else{
                $child = array('Lot','Qty1','Qty2', 'Qty Opname');
                $count_child_head = 4;
            }
            
            // IN
            // for($loop=1; $loop<=$num; $loop){
                foreach($list_type as $lt){
                    $type_in_lot         = $lt->id.'_in_lot';
                    $type_in_qty1        = $lt->id.'_in_qty1';
                    $type_in_qty1_uom    = $lt->id.'_in_qty1_uom';
                    $type_in_qty2        = $lt->id.'_in_qty2';
                    $type_in_qty2_uom    = $lt->id.'_in_qty2_uom';
                    $type_in_opname      = $lt->id.'_in_qty_opname';
                    $type_in_opname_uom  = $lt->id.'_in_qty_opname_uom';
                    $field .=  $type_in_lot.', '.$type_in_qty1.', '.$type_in_qty1_uom.', '.$type_in_qty2.', '.$type_in_qty2_uom.', '.$type_in_opname.', '.$type_in_opname_uom.', ';

                    $head_in_dept[]   = array('nama'=> $lt->name_type,'child' => $child, );
                    $count_type_in++;
                    if($count_type_in == 7){
                        break;
                    }
                }
            // }

            $head_in[] = array('nama' => 'Type ADJ IN', 'child' => $head_in_dept, 'count_child'=>$count_child_head*$count_type_in,'row'=>3);

            // OUT
            $count_type_out = 0;
            foreach($list_type as $lt){
                $type_out_lot         = $lt->id.'_out_lot';
                $type_out_qty1        = $lt->id.'_out_qty1';
                $type_out_qty1_uom    = $lt->id.'_out_qty1_uom';
                $type_out_qty2        = $lt->id.'_out_qty2';
                $type_out_qty2_uom    = $lt->id.'_out_qty2_uom';
                $type_out_opname      = $lt->id.'_out_qty_opname';
                $type_out_opname_uom  = $lt->id.'_out_qty_opname_uom';
                $field .=  $type_out_lot.', '.$type_out_qty1.', '.$type_out_qty1_uom.', '.$type_out_qty2.', '.$type_out_qty2_uom.', '.$type_out_opname.', '.$type_out_opname_uom.', ';
                $head_out_dept[]   = array('nama'=> $lt->name_type,'child' => $child, );
                $count_type_out++;
                if($count_type_out == 7){
                    break;
                }
            }

            $head_out[] = array('nama' => 'Type ADJ OUT', 'child' => $head_out_dept, 'count_child'=>$count_child_head*$count_type_out,'row'=>3);
          
        }

        $field = rtrim($field,', ');
     
        if($view == "Global"){
            $head_table1_info[]     = array('No.','Nama Produk');
        }else{
            if($view =="DetailLotDatar"){
                $head_table1_info[]     = array('No.','Kategori','Kode Produk', 'Nama Produk','Product Parent','Lot','Jenis Kain');
            }else{
                $head_table1_info[]     = array('No.','Kategori','Kode Produk', 'Nama Produk','Product Parent');
            }
        }
        $head_table1_awal[]     = array('Saldo Awal');
        $head_table1_akhir[]    = array('Saldo Akhir');
        $head_table1_adj_in[]   = array('Adjustment IN');
        $head_table1_adj_out[]  = array('Adjustment OUT');
        $head_table1_total_in[] = array('Total Penerimaan');
        $head_table1_total_out[]= array('Total Pengiriman');

        $head_table1[] = array('info'   => $head_table1_info,
                                'awal'  => $head_table1_awal,
                                'in'    => $head_table1_in,
                                'adj_in'=> $head_table1_adj_in,
                                'type_adj_in' => $head_in,
                                'count_in'=> $head_table1_total_in,
                                'out'   => $head_table1_out,
                                'adj_out'=>  $head_table1_adj_out,
                                'type_adj_out' => $head_out,
                                'count_out'=> $head_table1_total_out,
                                'akhir' => $head_table1_akhir,
                                );

        // header table
        $head_table2[] = array( 'awal'    => $head_table2_awal, 
                                'in'      => $head_table2_in,
                                'adj_in'  => $head_table2_adj, 
                                'type_adj_in' => $head_in,
                                'count_in'=> $head_table2_awal, 
                                'out'     => $head_table2_out, 
                                'adj_out' => $head_table2_adj,
                                'type_adj_out' => $head_out,
                                'count_out'=> $head_table2_awal,
                                'akhir'   => $head_table2_awal);
      
       return array($field,$head_table1,$head_table2,$jml_in,$jml_out,$count_type_in,$count_type_out);

    }

    function create_header_detail($excel)
    {   
        $header  = [];
        if($excel == true){
            $header  = array('No', 'Posisi Mutasi','Departemen','Tipe','Kode Transaksi','Tanggal Transaksi','Kode Produk','Nama Produk', 'Kategori','Lot','Qty','Uom1','Qty2','Uom2','Qty Opname','Uom Opname','Origin','Method', 'SC','MKT', 'Reff Note');
        }else{
            $header  = array('No', 'Posisi Mutasi','Departemen','Tipe','Kode Transaksi','Tanggal Transaksi','Kode Produk','Nama Produk', 'Kategori','Lot','Qty','Qty2','Qty Opname','Origin','Method', 'SC','MKT','Reff Note' );

        }

        return $header;
    }

    function create_header_detail2($view,$excel,$departemen)
    {

        $s_awal         = [];
        $head_table     = [];
        $field_table    = [];
        $field_view     = [];
        $head_utama     = [];
        $s_awal         = [];
        $head_in        = [];
        $head_adj_in    = [];
        $head_prod      = [];
        $head_out       = [];
        $head_con       = [];
        $head_adj_out   = [];
        $head_process   = [];
        $s_akhir        = [];
        $field          = '';
        $table_view     = '';
        $mutasi_dept    = '';
        $field_dept_in  = [];
        $field_dept_out = [];
        $field_dept_process = [];

        
        if($view == "Global"){
            $head_utama[]      = array('No.','Parent','Jenis Kain');
            $table_view        = 'global';
            $field            .= 'product_parent,nama_jenis_kain,';
            $field            .= 's_awal_lot,con_lot,adj_in_lot,prod_lot,adj_out_lot,s_akhir_lot,'; // lot

            if($excel == true){
                $child             = array('Proses','Lot','Qty1','Uom1','Qty2','Uom2','Qty Opname','Uom Opname');
                $count_child       = 8;
            }else{
                $child             = array('Proses','Lot','Qty1', 'Qty2','Qty Opname');
                $count_child       = 5;
            }

            $s_awal[]          = array('nama' => "Saldo Awal", 'child' => $child, 'count_child'=>$count_child, 'row'=>2);
            // consume 
            $head_con[]        = array('nama' => 'Consume ','child'=> $child, 'count_child'=>$count_child, 'row'=>2);
            
            // ADJ IN  
            $head_adj_in[]     = array('nama' => 'ADJ IN ','child'=> $child, 'count_child'=>$count_child, 'row'=>2);
              
            // Produce
            $head_prod[]       = array('nama' => 'Produce', 'child' => $child, 'count_child'=>$count_child,'row'=>2);
      
            // ADJ out
            $head_adj_out[]    = array('nama' => 'ADJ OUT ','child'=> $child, 'count_child'=>$count_child,'row'=>2);
              
            // s akhir
            $s_akhir[]         = array('nama' => "Saldo Akhir", 'child' => $child, 'count_child'=>$count_child,'row'=>2);

        }else if($view == "DetailProduk"){
            $head_utama[]       = array('No.','Nama Produk','Parent','Jenis Kain');
            $table_view         = 'produk';
            $field             .= 'nama_produk,product_parent,nama_jenis_kain,';
            $field             .= 's_awal_lot,con_lot,adj_in_lot,prod_lot,adj_out_lot,s_akhir_lot,'; // lot

            if($excel == true){
                $child             = array('Proses','Lot','Qty1','Uom1','Qty2','Uom2','Qty Opname','Uom Opname');
                $count_child       = 8;
            }else{
                $child             = array('Proses','Lot','Qty1', 'Qty2','Qty Opname');
                $count_child       = 5;
            }

            $s_awal[]           = array('nama' => "Saldo Awal", 'child' => $child, 'count_child'=>$count_child, 'row'=>2);
            // consume 
            $head_con[]         = array('nama' => 'Consume ','child'=> $child, 'count_child'=>$count_child, 'row'=>2);
            
            // ADJ IN  
            $head_adj_in[]      = array('nama' => 'ADJ IN ','child'=> $child, 'count_child'=>$count_child, 'row'=>2);
             
            // Produce
            $head_prod[]        = array('nama' => 'Produce', 'child' => $child, 'count_child'=>$count_child,'row'=>2);
     
            // ADJ out
            $head_adj_out[]     = array('nama' => 'ADJ OUT ','child'=> $child, 'count_child'=>$count_child,'row'=>2);
             
            // s akhir
            $s_akhir[]          = array('nama' => "Saldo Akhir", 'child' => $child, 'count_child'=>$count_child,'row'=>2);

        }else{
            if($excel == true){
                $head_utama[]       = array('No.','LOT', 'Qty1 (Mtr)','Uom1', 'Qty2 (Kg)','Uom2','GO','Route','Nama Produk','Warna','Parent','Jenis Kain');
                $child              = array('Proses','Lot','Qty1','Uom1','Qty2','Uom2','Qty Opname','Uom Opname');
                $count_child        = 8;
            }else{
                $head_utama[]       = array('No.','LOT', 'Qty1 (Mtr)', 'Qty2 (Kg)','GO','Route','Nama Produk','Warna','Parent','Jenis Kain');
                $child              = array('Proses','Lot','Qty1', 'Qty2','Qty Opname');
                $count_child        = 5;
            }

            $field             .= 'lot,go_qty1,go_qty1_uom,go_qty2,go_qty2_uom,go_qty_opname,go_qty_opname_uom,no_go,route,kode_produk,nama_produk,warna,product_parent,nama_jenis_kain,';
            $table_view         = 'detail';

            $s_awal[]           = array('nama' => "Saldo Awal", 'child' => $child, 'count_child'=>$count_child, 'row'=>2);
            // consume 
            $head_con[]        = array('nama' => 'Consume ','child'=> $child, 'count_child'=>$count_child, 'row'=>2);
            
            // ADJ IN  
            $head_adj_in[]     = array('nama' => 'ADJ IN ','child'=> $child, 'count_child'=>$count_child, 'row'=>2);
            
            // Produce
            $head_prod[]       = array('nama' => 'Produce', 'child' => $child, 'count_child'=>$count_child,'row'=>2);
    
            // ADJ out
            $head_adj_out[]    = array('nama' => 'ADJ OUT ','child'=> $child, 'count_child'=>$count_child,'row'=>2);
            
            // s akhir
            $s_akhir[]         = array('nama' => "Saldo Akhir", 'child' => $child, 'count_child'=>$count_child,'row'=>2);

        }
            
            $field .= 's_awal_lot,s_awal_proses,s_awal_qty1,s_awal_qty1_uom,s_awal_qty2,s_awal_qty2_uom,s_awal_qty_opname,s_awal_qty_opname_uom,';
            $field .= 'con_lot,con_proses,con_qty1,con_qty1_uom,con_qty2,con_qty2_uom,con_qty_opname,con_qty_opname_uom,';
            $field .= 'adj_in_lot,adj_in_proses,adj_in_qty1,adj_in_qty1_uom,adj_in_qty2,adj_in_qty2_uom,adj_in_qty_opname,adj_in_qty_opname_uom,';
            $field .= 'prod_lot,prod_proses,prod_qty1,prod_qty1_uom,prod_qty2,prod_qty2_uom,prod_qty_opname,prod_qty_opname_uom,';
            $field .= 'adj_out_lot,adj_out_proses,adj_out_qty1,adj_out_qty1_uom,adj_out_qty2,adj_out_qty2_uom,adj_out_qty_opname,adj_out_qty_opname_uom,';
            $field .= 's_akhir_lot,s_akhir_proses,s_akhir_qty1,s_akhir_qty1_uom,s_akhir_qty2,s_akhir_qty2,s_akhir_qty2_uom,s_akhir_qty_opname,s_akhir_qty_opname_uom,';

        if($departemen == 'DYE2'){
            if($view == 'Global' || $view == 'DetailProduk'){
                if($excel == true){
                    $count_child_head  = 16;
                }else{
                    $count_child_head  = 10;
                }
            }else{
                if($excel == true){
                    $count_child_head  = 16;
                }else{
                    $count_child_head  = 10;
                }
            }
            // penerimaan
            $head_in_dept[]   = array('nama'=> 'GRG','child' => $child, );
            $head_in_dept[]   = array('nama'=> 'SET (FIN)','child' => $child, );
            $head_in[]        = array('nama' => 'IN (Penerimaan dari) ','child'=> $head_in_dept, 'count_child'=>$count_child_head,'row'=>3);
            
            // pengiriman
            $head_out_dept[]   = array('nama'=> 'FIN','child' => $child, );
            $head_out_dept[]   = array('nama'=> 'GRG (Retur)','child' => $child, );
            $head_out[]        = array('nama' => 'OUT (Pengiriman Ke)', 'child' => $head_out_dept, 'count_child'=>$count_child_head,'row'=>3);

            $field .= 'in_grg_lot,in_set_lot,out_fin_lot,out_grg_lot,';
            $field .= 'in_grg_proses,in_grg_qty1,in_grg_qty1_uom,in_grg_qty2,in_grg_qty2_uom,in_grg_qty_opname,in_grg_qty_opname_uom,';
            $field .= 'in_set_proses,in_set_qty1,in_set_qty1_uom,in_set_qty2,in_set_qty2_uom,in_set_qty_opname,in_set_qty_opname_uom,';

            $field .= 'out_fin_proses,out_fin_qty1,out_fin_qty1_uom,out_fin_qty2,out_fin_qty2_uom,out_fin_qty_opname,out_fin_qty_opname_uom,';
            $field .= 'out_grg_proses,out_grg_qty1,out_grg_qty1_uom,out_grg_qty2,out_grg_qty2_uom,out_grg_qty_opname,out_grg_qty_opname_uom';

            $mutasi_dept = 'acc_mutasi_dye_1_';

            // untuk looping view
            $field_dept_in[]  = array('grg','set');
            $field_dept_out[] = array('fin','grg');
            
        }else if($departemen == 'FIN'){

            if($view == 'Global' || $view == 'DetailProduk'){
                if($excel == true){
                    $count_child_head1  = 16; // jml kolom penerimaan
                    $count_child_head2  = 20; // jml kolom pengiriman
                }else{
                    $count_child_head1  = 10; // jml kolom penerimaan
                    $count_child_head2  = 15; // jml kolom pengiriman
                }
            }else{
                $field .= 'hph_set_lot, hph_set_proses, hph_set_qty1, hph_set_qty1_uom, hph_set_qty2, hph_set_qty2_uom, hph_set_qty_opname, hph_set_qty_opname_uom,';
                $field .= 'hph_fin_lot, hph_fin_proses, hph_fin_qty1, hph_fin_qty1_uom, hph_fin_qty2, hph_fin_qty2_uom, hph_fin_qty_opname, hph_fin_qty_opname_uom,';
                $field .= 'hph_brs_lot, hph_brs_proses, hph_brs_qty1, hph_brs_qty1_uom, hph_brs_qty2, hph_brs_qty2_uom, hph_brs_qty_opname, hph_brs_qty_opname_uom,';
                $field .= 'hph_fbr_lot, hph_fbr_proses, hph_fbr_qty1, hph_fbr_qty1_uom, hph_fbr_qty2, hph_fbr_qty2_uom, hph_fbr_qty_opname, hph_fbr_qty_opname_uom,';
                $field .= 'hph_pad_lot, hph_pad_proses, hph_pad_qty1, hph_pad_qty1_uom, hph_pad_qty2, hph_pad_qty2_uom, hph_pad_qty_opname, hph_pad_qty_opname_uom,';
                if($excel == true){
                    $count_child_head1  = 16; // jml kolom penerimaan
                    $count_child_head2  = 20; // jml kolom pengiriman
                }else{
                    $count_child_head1  = 10; // jml kolom penerimaan
                    $count_child_head2  = 15;// jml kolom pengiriman
                }
                // detail proses
                $head_process_dept[]   = array('nama'=> 'SET','child' => $child, );
                $head_process_dept[]   = array('nama'=> 'FIN','child' => $child, );
                $head_process_dept[]   = array('nama'=> 'BRS','child' => $child, );
                $head_process_dept[]   = array('nama'=> 'FBR','child' => $child, );
                $head_process_dept[]   = array('nama'=> 'PAD','child' => $child, );
                if($excel == true){
                    $count_childx = 30;
                }else{
                    $count_childx = 25;
                }
                $head_process[]        = array('nama' => 'Detail Process', 'child' => $head_process_dept, 'count_child'=>$count_childx,'row'=>3);
            }

            // penerimaan
            $head_in_dept[]   = array('nama'=> 'GRG','child' => $child, );
            $head_in_dept[]   = array('nama'=> 'DYE','child' => $child, );
            $head_in[]        = array('nama' => 'IN (Penerimaan dari) ','child'=> $head_in_dept, 'count_child'=>$count_child_head1,'row'=>3);

    
            // pengiriman
            $head_out_dept[]   = array('nama'=> 'INS2','child' => $child, );
            $head_out_dept[]   = array('nama'=> 'GRG','child' => $child, );
            $head_out_dept[]   = array('nama'=> 'DYE','child' => $child, );
            $head_out[]        = array('nama' => 'OUT (Pengiriman Ke)', 'child' => $head_out_dept, 'count_child'=>$count_child_head2,'row'=>3);

            $field .= 'in_grg_lot,in_dye_lot,out_ins2_lot,out_grg_lot,out_dye_lot,';

            $field .= 'in_grg_proses, in_grg_qty1,in_grg_qty1_uom,in_grg_qty2,in_grg_qty2_uom,in_grg_qty_opname,in_grg_qty_opname_uom,';
            $field .= 'in_dye_proses, in_dye_qty1,in_dye_qty1_uom,in_dye_qty2,in_dye_qty2_uom,in_dye_qty_opname,in_dye_qty_opname_uom,';

            $field .= 'out_ins2_proses, out_ins2_qty1,out_ins2_qty1_uom,out_ins2_qty2,out_ins2_qty2_uom,out_ins2_qty_opname,out_ins2_qty_opname_uom,';
            $field .= 'out_dye_proses, out_dye_qty1,out_dye_qty1_uom,out_dye_qty2,out_dye_qty2_uom,out_dye_qty_opname,out_dye_qty_opname_uom,';
            $field .= 'out_grg_proses, out_grg_qty1,out_grg_qty1_uom,out_grg_qty2,out_grg_qty2_uom,out_grg_qty_opname,out_grg_qty_opname_uom';

            $mutasi_dept = 'acc_mutasi_fin_1_';

            // untuk looping view
            $field_dept_in[]  = array('grg','dye');
            $field_dept_out[] = array('ins2','grg','dye');
            $field_dept_process[] = array('set','fin','brs','fbr','pad');
        
        }else{// DF

            if($view == 'Global' || $view == 'DetailProduk'){
                if($excel == true){
                    $count_child_head1  = 7;
                    $count_child_head2  = 14;
                }else{
                    $count_child_head1  = 5;
                    $count_child_head2  = 10;
                }
            }else{
                if($excel == true){
                    $count_child_head1  = 6;
                    $count_child_head2  = 9;
                }else{
                    $count_child_head1  = 5; // jml kolom penerimaan
                    $count_child_head2  = 10; // jml kolom pengiriman
                }

                $field .= 'hph_dye_lot, hph_dye_proses, hph_dye_qty1, hph_dye_qty1_uom, hph_dye_qty2, hph_dye_qty2_uom, hph_dye_qty_opname, hph_dye_qty_opname_uom,';
                $field .= 'hph_set_lot, hph_set_proses, hph_set_qty1, hph_set_qty1_uom, hph_set_qty2, hph_set_qty2_uom, hph_set_qty_opname, hph_set_qty_opname_uom,';
                $field .= 'hph_fin_lot, hph_fin_proses, hph_fin_qty1, hph_fin_qty1_uom, hph_fin_qty2, hph_fin_qty2_uom, hph_fin_qty_opname, hph_fin_qty_opname_uom,';
                $field .= 'hph_brs_lot, hph_brs_proses, hph_brs_qty1, hph_brs_qty1_uom, hph_brs_qty2, hph_brs_qty2_uom, hph_brs_qty_opname, hph_brs_qty_opname_uom,';
                $field .= 'hph_fbr_lot, hph_fbr_proses, hph_fbr_qty1, hph_fbr_qty1_uom, hph_fbr_qty2, hph_fbr_qty2_uom, hph_fbr_qty_opname, hph_fbr_qty_opname_uom,';
                $field .= 'hph_pad_lot, hph_pad_proses, hph_pad_qty1, hph_pad_qty1_uom, hph_pad_qty2, hph_pad_qty2_uom, hph_pad_qty_opname, hph_pad_qty_opname_uom,';
                
                // detail proses
                $head_process_dept[]   = array('nama'=> 'DYE','child' => $child, );
                $head_process_dept[]   = array('nama'=> 'SET','child' => $child, );
                $head_process_dept[]   = array('nama'=> 'FIN','child' => $child, );
                $head_process_dept[]   = array('nama'=> 'BRS','child' => $child, );
                $head_process_dept[]   = array('nama'=> 'FBR','child' => $child, );
                $head_process_dept[]   = array('nama'=> 'PAD','child' => $child, );
                if($excel == true){
                    $count_childx = 30;
                }else{
                    $count_childx = 30;
                }
                $head_process[]        = array('nama' => 'Detail Process', 'child' => $head_process_dept, 'count_child'=>$count_childx,'row'=>3);

            }

            // penerimaan
            $head_in_dept[]   = array('nama'=> 'GRG','child' => $child, );
            $head_in[]        = array('nama' => 'IN (Penerimaan dari) ','child'=> $head_in_dept, 'count_child'=>$count_child_head1,'row'=>3);


            // pengiriman
            $head_out_dept[]   = array('nama'=> 'INS2','child' => $child, );
            $head_out_dept[]   = array('nama'=> 'GRG','child' => $child, );
            $head_out[]        = array('nama' => 'OUT (Pengiriman Ke)', 'child' => $head_out_dept, 'count_child'=>$count_child_head2,'row'=>3);

            $field .= 'in_grg_lot,out_ins2_lot,out_grg_lot,';
            $field .= 'in_grg_proses, in_grg_qty1,in_grg_qty1_uom,in_grg_qty2,in_grg_qty2_uom,in_grg_qty_opname,in_grg_qty_opname_uom,';

            $field .= 'out_ins2_proses, out_ins2_qty1,out_ins2_qty1_uom,out_ins2_qty2,out_ins2_qty2_uom,out_ins2_qty_opname,out_ins2_qty_opname_uom,';
            $field .= 'out_grg_proses, out_grg_qty1,out_grg_qty1_uom,out_grg_qty2,out_grg_qty2_uom,out_grg_qty_opname,out_grg_qty_opname_uom';

            $mutasi_dept = 'acc_mutasi_df_1_';

            // untuk looping view
            $field_dept_in[]  = array('grg');
            $field_dept_out[] = array('ins2','grg');
            $field_dept_process[] = array('dye','set','fin','brs','fbr','pad');
            

        }

        $head_table[] = array( 'info'    => $head_utama,
                                'awal'    => $s_awal, 
                                'in'      => $head_in,
                                'con'     => $head_con,
                                'adj_in'  => $head_adj_in, 
                                'prod'    => $head_prod,
                                'process' => $head_process,                                   
                                'out'     => $head_out, 
                                'adj_out' => $head_adj_out,                                   
                                'akhir'   => $s_akhir);
                                   
        $field_table[] = array('nama_tabel' => $mutasi_dept.''.$table_view,
                               'field' => $field );
        $field_view[] = array('in' => $field_dept_in,
                              'out'=> $field_dept_out,
                              'process'  => $field_dept_process);

        return array($head_table,$field_table,$field_view);      
    }

    function cek_column_excel($index)
    {
        $max    = 1000; 
        $result = 'A';
        for ($l = 'A', $i = 1; $i < $max; $l++, $i++) {
            if($i == $index){
                $result = $l;
                break;
            }
        }
        return $result;
    }

    function export_excel_mutasi()
    {

        $arr_filter     = $this->input->post('arr_filter');
        $tanggal        = '';
        $departemen     = '';
        $kode_produk     = '';
        $nama_produk    = '';
        $kode_transaksi = '';
        $reff_note      = '';
        $reff_picking   = '';
        $lot            = '';
        $parent         = '';
        $warna          = '';
        $no_go          = '';
        $jenis_kain     = '';
        $route          = '';
        $view           = 'Global';
        foreach($arr_filter as $filter){

            if($filter['tanggal'] != ''){
                $tanggal     = $filter['tanggal'];
            } 
            
            if($filter['departemen']!= ''){
                $departemen  = $filter['departemen'];
            }

            if($filter['kode_produk'] != ''){
                $kode_produk = $filter['kode_produk'];
            }

            if($filter['nama_produk'] != ''){
                $nama_produk = $filter['nama_produk'];
            }

            if($filter['kode_transaksi'] != ''){
                $kode_transaksi = $filter['kode_transaksi'];
            }

            if($filter['lot'] != ''){
                $lot = $filter['lot'];
            }

            if($filter['reff_picking'] != ''){
                $reff_picking = $filter['reff_picking'];
            }

            if($filter['reff_note'] != ''){
                $reff_note = $filter['reff_note'];
            }

            
            if($filter['parent'] != ''){
                $parent = $filter['parent'];
            }

            if($filter['warna'] != ''){
                $warna = $filter['warna'];
            }

            if($filter['no_go'] != ''){
                $no_go = $filter['no_go'];
            }

            if($filter['route'] != ''){
                $route = $filter['route'];
            }

            if($filter['jenis_kain'] != ''){
                $jenis_kain = $filter['jenis_kain'];
            }

            foreach($filter['view_arr'] as $val2){
                $view = $val2;
                break;
            }
        }

        if(empty($arr_filter) or empty($tanggal) or empty($departemen) or empty($view)){

            $response =  array(
                'status'    => 'failed',
                'message'   => "Silahkan Generate terlebih dahulu !",
            );
        
            die(json_encode($response));

        }else{

            if($departemen == 'DYE2'or $departemen == 'FIN' OR $departemen == 'DF'){
                $this->export_excel_mutasi_format2($view,$tanggal,$departemen,$parent,$nama_produk,$warna,$lot,$no_go,$route,$jenis_kain);
            }else{

                if($view == "Global" || $view == "DetailProduk"){
                    $this->export_excel_mutasi_global($view,$tanggal,$departemen,$kode_produk,$nama_produk);
                }else if($view == "DetailLotDatar"){
                    $this->export_excel_mutasi_detail_datar($view,$tanggal,$departemen,$parent,$kode_produk,$nama_produk,$lot,$jenis_kain);
 
                }else{
                    $this->export_excel_mutasi_detail($tanggal,$departemen,$kode_produk,$nama_produk,$kode_transaksi,$lot,$reff_picking,$reff_note);
                }

            }
        }
        
    }

    function export_excel_mutasi_format2($view,$tanggal,$departemen,$parent,$nama_produk,$warna,$lot,$no_go,$route,$jenis_kain)
    {
        $result = $this->cek_dept_mutasi($departemen);
        if($result == true){

            $this->load->library('excel');
            $tahun      = date('Y', strtotime($tanggal)); // example 2023
            $bulan      = date('n', strtotime($tanggal)); // example 7
            if($departemen == "DYE2"){
                $dept = "Dyeing (2) ";
            }else if($departemen == "DF"){
                $dept = "Dyeing Finishing";
            }else{
                $dept = "Finishing";
            }

            ob_start();
            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);

            if($view == "Global"){ 
                $result_where = $this->create_where2($parent,'','','','','',$jenis_kain);
            }else if($view == "DetailProduk"){
                $result_where = $this->create_where2($parent,$nama_produk,'','','','',$jenis_kain);
            }else{
                $result_where = $this->create_where2($parent,$nama_produk,$warna,$lot,$no_go,$route,$jenis_kain);
            }

            $result = $this->create_header_detail2($view,true,$departemen);
            $r_result     = $result[0];// judul tabel
            $table_field  = $result[1];// field dan nama tabel
            $table_field_view  = $result[2];// field view

            $acc_mutasi_rm  = $this->get_acc_mutasi_by_kode2($view,$table_field,$tahun,$bulan,$result_where,'','',true);

            $table_mutasi[] = array('judul'       => 'Laporan Mutasi',
                                    'head_table'   => $r_result,
                                    'record'        => $acc_mutasi_rm[1],
                                    'count_record'  => $acc_mutasi_rm[2],
                                    'field_view'    => $table_field_view
                            );

            $sheet = $object->setActiveSheetIndex(0);
            $sheet->getStyle("A1:U6")->getFont()->setBold(true);

            $rowCount = 1;
            $loop     = 1;
            $out_or_in = false;
            $info     = false;
            foreach($table_mutasi as $tm){
                $rowCountFirst = $rowCount;
                
                // SET JUDUL
                $sheet->SetCellValue('A'.$rowCount, $tm['judul']);
                $sheet->getStyle('A'.$rowCount)->getAlignment()->setIndent(1);
                $sheet->mergeCells('A'.$rowCount.':N'.$rowCount);
 
                // set Departemen
                $rowCount = $rowCount+1;
                $sheet->SetCellValue('A'.$rowCount, 'Departemen');
                $sheet->mergeCells('A'.$rowCount.':B'.$rowCount);
                $sheet->SetCellValue('C'.$rowCount, ': '.$dept);
                $sheet->mergeCells('C'.$rowCount.':D'.$rowCount);
 
                // set periode
                $rowCount = $rowCount+1;
                $sheet->SetCellValue('A'.$rowCount, 'Periode');
                $sheet->mergeCells('A'.$rowCount.':B'.$rowCount);
                $sheet->SetCellValue('C'.$rowCount, ': '.bln_indo(date('d-m-Y',strtotime($tanggal))) );
                $sheet->mergeCells('C'.$rowCount.':F'.$rowCount);

                // header table
                $column     = 0;
                $column_e   = 1;
                $rowCount   = $rowCount + 4;
               foreach ($tm['head_table'] as $field) {
                    foreach($field as $field2 => $field22){
                        if($field2 == "info"){
                            for (  $i = 0, $l = count($field22); $i < $l; $i++ ) {
                                foreach($field22[$i] as $field3 => $field33){
                                    $sheet->setCellValueByColumnAndRow($column, $rowCount, $field33); 
                                    $column_excel  = $this->cek_column_excel($column_e);
                                    $rowCountMerge = $rowCount + 2;
                                    $sheet->mergeCells($column_excel.''.$rowCount.':'.$column_excel.''.$rowCountMerge);
                                    $sheet->getStyle($column_excel.''.$rowCount)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                                    $sheet->getStyle($column_excel.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
                                    $column++;
                                    $column_e++;
                                }
                            }
                            $info = true;
                            $out_or_in = true;
                        }else if($field2 == 'awal' || $field2 == 'con' || $field2== 'akhir'  || $field2== 'adj_in' || $field2== 'adj_out' || $field2== 'prod' ){
                            if($out_or_in == false  AND $info == true){
                                $column_e  = $column_e - 1 ;
                            }
                            
                            foreach($field22 as $field3){
                                $sheet->setCellValueByColumnAndRow($column, $rowCount, $field3['nama']);
                                $rowCountMerge = $rowCount + 1;
                                if(count($field3['child']) > 0){
                                    $column_child = $column;
                                    $column_excel_start  = $this->cek_column_excel($column_e);
                                    foreach ($field3['child'] as $field4){
                                        $sheet->setCellValueByColumnAndRow($column_child, $rowCount+2, $field4);
                                        $column_child++;
                                    }
                                    if($view == "Global" or $view == "DetailProduk"){
                                        $column_e = $column_e + 8;
                                        $column   = $column + 8;
                                    }else{
                                        $column_e = $column_e + 8;
                                        $column   = $column + 8;
                                    }
                                    $column_excel_finish = $this->cek_column_excel($column_e-1);
                                    if($field2 == 'awal'){
                                    }
                                    $sheet->mergeCells($column_excel_start.''.$rowCount.':'.$column_excel_finish.''.$rowCountMerge);
                                    $sheet->getStyle($column_excel_start.''.$rowCount)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                                    $sheet->getStyle($column_excel_start.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
                                }
                            }
                            $column_e++;
                            $out_or_in = false;
                            // $info = false;
                        }else if($field2 == 'in' || $field2 == 'out' || $field2 == 'process'){
                            if($out_or_in == true AND  $info == true){
                                $column_e  = $column_e+1 ;
                            }
                            if(count($field22) > 0){

                                foreach($field22 as $field3){
                                    $sheet->setCellValueByColumnAndRow($column, $rowCount, $field3['nama']);
                                    // $column++;
                                    $rowCountMerge = $rowCount + 1;

                                    if(count($field3['child']) > 0){
                                        $column_child = $column;
                                        $column_child2 = $column;
                                        $column_excel_start  = $this->cek_column_excel($column_e-1);
                                        $column_e2 = $column_e-1;
                                        foreach ($field3['child'] as $field4){// departemen
                                            
                                            $column_excel_start2  = $this->cek_column_excel($column_e2);
                                            $sheet->setCellValueByColumnAndRow($column_child, $rowCount+1, $field4['nama']);
                                            if($view == "Global" or $view == "DetailProduk"){
                                                $column_e2 = $column_e2 + 8;
                                                $column_child = $column_child + 8;
                                            }else{
                                                $column_e2 = $column_e2 + 8;
                                                $column_child = $column_child + 8;
                                            }
                                            $column_excel_finish2 = $this->cek_column_excel($column_e2-1);
                                            $sheet->mergeCells($column_excel_start2.''.$rowCountMerge.':'.$column_excel_finish2.''.$rowCountMerge);
                                            $sheet->getStyle($column_excel_start2.''.$rowCountMerge)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                                            $sheet->getStyle($column_excel_start2.''.$rowCountMerge)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

                                            foreach($field4['child'] as $field5){// qty1 qty2 dll
                                                $sheet->setCellValueByColumnAndRow($column_child2, $rowCount+2, $field5);
                                                $column_child2++;
                                            }
                                        }
                                        $column_excel_finish = $this->cek_column_excel($column_e2-1);
                                        $sheet->mergeCells($column_excel_start.''.$rowCount.':'.$column_excel_finish.''.$rowCount);
                                        $sheet->getStyle($column_excel_start.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

                                        $column = $column_child2;
                                    
                                    }
                                }
                                $column_e  = $column_child2 + 1;
                                $out_or_in = true;
                                $info = true;
                               
                            }

                        }
                    }
                }

                // bold header table
                $column_excel  = $this->cek_column_excel($column);
                $rowCountMerge = $rowCountMerge + 1;
                $sheet->getStyle("A".$rowCountFirst.":".$column_excel."".$rowCountMerge)->getFont()->setBold(true);

                // body
                $no    = 1;
                $rowCount = $rowCount + 3;
                foreach ($tm['record'] as $row) {
                    $sheet->SetCellValue('A'.$rowCount, ($no++));
                    $column_awal = 2;
                    if($view == "Global"){
                        $column_excel  = $this->cek_column_excel($column_awal);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['product_parent']);
                        $column_excel  = $this->cek_column_excel($column_awal+1);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_jenis_kain']);
                        $column_excel  = $this->cek_column_excel($column_awal+2);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_proses']);
                        $column_excel  = $this->cek_column_excel($column_awal+3);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_lot']);
                        $column = 6;
                    }else if($view == "DetailProduk"){
                        $column_excel  = $this->cek_column_excel($column_awal);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_produk']);
                        $column_excel  = $this->cek_column_excel($column_awal+1);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['product_parent']);
                        $column_excel  = $this->cek_column_excel($column_awal+2);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_jenis_kain']);
                        $column_excel  = $this->cek_column_excel($column_awal+3);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_proses']);
                        $column_excel  = $this->cek_column_excel($column_awal+4);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_lot']);
                        $column = 7;
                    }else{ // detailLot

                        $column_excel  = $this->cek_column_excel($column_awal);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['lot']);
                        $column_excel  = $this->cek_column_excel($column_awal+1);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['go_qty1']);
                        $column_excel  = $this->cek_column_excel($column_awal+2);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['go_qty1_uom']);
                        $column_excel  = $this->cek_column_excel($column_awal+3);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['go_qty2']);
                        $column_excel  = $this->cek_column_excel($column_awal+4);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['go_qty2_uom']);
                        $column_excel  = $this->cek_column_excel($column_awal+5);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['no_go']);
                        $column_excel  = $this->cek_column_excel($column_awal+6);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['route']);
                        $column_excel  = $this->cek_column_excel($column_awal+7);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_produk']);
                        $column_excel  = $this->cek_column_excel($column_awal+8);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['warna']);
                        $column_excel  = $this->cek_column_excel($column_awal+9);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['product_parent']);
                        $column_excel  = $this->cek_column_excel($column_awal+10);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_jenis_kain']);
                        $column_excel  = $this->cek_column_excel($column_awal+11);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_proses']);
                        $column_excel  = $this->cek_column_excel($column_awal+12);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_lot']);
                        $column = 15;
                    }
                
                    // SALDO AWAL
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty1']);
                    $column_excel  = $this->cek_column_excel($column+1);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty1_uom']);
                    $column_excel  = $this->cek_column_excel($column+2);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty2']);
                    $column_excel  = $this->cek_column_excel($column+3);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty2_uom']);
                    $column_excel  = $this->cek_column_excel($column+4);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty_opname']);
                    $column_excel  = $this->cek_column_excel($column+5);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty_opname_uom']);

                    // PENERIMAAN / IN 
                    $column = $column + 6;
                    $ke = 0;
                    foreach($tm['field_view'] as $fview ){
                        for ( $i = 0, $l = count($fview['in'][$ke]); $i<$l; $i++){
                            foreach($fview['in'] as $fviews ){
                                $in_proses    = $row['in_'.$fviews[$i].'_proses'];
                                $column_excel  = $this->cek_column_excel($column);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $in_proses); 
                                $column++;
                                // if($view == "Global" || $view == "DetailProduk" ){
                                    $in_lot = $row['in_'.$fviews[$i].'_lot'];
                                    $column_excel  = $this->cek_column_excel($column);
                                    $sheet->SetCellValue($column_excel.''.$rowCount, $in_lot);
                                    $column++;
                                // }
                                $in_qty1      = $row['in_'.$fviews[$i].'_qty1'];
                                $in_qty1_uom  = $row['in_'.$fviews[$i].'_qty1_uom'];
                                $in_qty2      = $row['in_'.$fviews[$i].'_qty2'];
                                $in_qty2_uom  = $row['in_'.$fviews[$i].'_qty2_uom'];
                                $in_qty_op    = $row['in_'.$fviews[$i].'_qty_opname'];
                                $in_qty1_op_uom  = $row['in_'.$fviews[$i].'_qty_opname_uom'];
                                $column_excel  = $this->cek_column_excel($column);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $in_qty1);
                                $column_excel  = $this->cek_column_excel($column+1);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $in_qty1_uom);
                                $column_excel  = $this->cek_column_excel($column+2);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $in_qty2);
                                $column_excel  = $this->cek_column_excel($column+3);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $in_qty2_uom);
                                $column_excel  = $this->cek_column_excel($column+4);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $in_qty_op);
                                $column_excel  = $this->cek_column_excel($column+5);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $in_qty1_op_uom);
                                $column = $column + 6;
                            }
                        }
                        $ke++;
                    }

                    // consume
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['con_proses']);
                    $column++;
                    // if($view == "Global" || $view == "DetailProduk" ){
                        $column_excel  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['con_lot']);
                        $column++;
                    // }

                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['con_qty1']);
                    $column_excel  = $this->cek_column_excel($column+1);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['con_qty1_uom']);
                    $column_excel  = $this->cek_column_excel($column+2);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['con_qty2']);
                    $column_excel  = $this->cek_column_excel($column+3);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['con_qty2_uom']);
                    $column_excel  = $this->cek_column_excel($column+4);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['con_qty_opname']);
                    $column_excel  = $this->cek_column_excel($column+5);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['con_qty_opname_uom']);
                    $column = $column + 6;

                    // ADJ IN 
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_in_proses']);
                    $column++;
                    // if($view == "Global" || $view == "DetailProduk" ){
                        $column_excel  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_in_lot']);
                        $column++;
                    // }
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_in_qty1']);
                    $column_excel  = $this->cek_column_excel($column+1);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_in_qty1_uom']);
                    $column_excel  = $this->cek_column_excel($column+2);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_in_qty2']);
                    $column_excel  = $this->cek_column_excel($column+3);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_in_qty2_uom']);
                    $column_excel  = $this->cek_column_excel($column+4);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_in_qty_opname']);
                    $column_excel  = $this->cek_column_excel($column+5);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_in_qty_opname_uom']);
                    $column = $column + 6;
                   
                    // produce
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['prod_proses']);
                    $column++;
                    // if($view == "Global" || $view == "DetailProduk" ){
                        $column_excel  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['prod_lot']);
                        $column++;
                    // }
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['prod_qty1']);
                    $column_excel  = $this->cek_column_excel($column+1);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['prod_qty1_uom']);
                    $column_excel  = $this->cek_column_excel($column+2);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['prod_qty2']);
                    $column_excel  = $this->cek_column_excel($column+3);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['prod_qty2_uom']);
                    $column_excel  = $this->cek_column_excel($column+4);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['prod_qty_opname']);
                    $column_excel  = $this->cek_column_excel($column+5);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['prod_qty_opname_uom']);
                    $column = $column + 6;

                    // process
                    if($view == "DetailLot" && ($departemen == 'FIN' || $departemen == 'DF')){
                        $ke = 0;
                        foreach($tm['field_view'] as $fview ){
                            for ( $i = 0, $l = count($fview['process'][$ke]); $i<$l; $i++){
                                foreach($fview['process'] as $fviews ){
                                   
                                    $hph_lot       = $row['hph_'.$fviews[$i].'_lot'];
                                    $hph_proses    = $row['hph_'.$fviews[$i].'_proses'];
                                    $hph_qty1      = $row['hph_'.$fviews[$i].'_qty1'];
                                    $hph_qty1_uom  = $row['hph_'.$fviews[$i].'_qty1_uom'];
                                    $hph_qty2      = $row['hph_'.$fviews[$i].'_qty2'];
                                    $hph_qty2_uom  = $row['hph_'.$fviews[$i].'_qty2_uom'];
                                    $hph_qty_op    = $row['hph_'.$fviews[$i].'_qty_opname'];
                                    $hph_qty1_op_uom  = $row['hph_'.$fviews[$i].'_qty_opname_uom'];
                                    $column_excel  = $this->cek_column_excel($column);
                                    $sheet->SetCellValue($column_excel.''.$rowCount, $hph_proses);
                                    $column_excel  = $this->cek_column_excel($column+1);
                                    $sheet->SetCellValue($column_excel.''.$rowCount, $hph_lot);
                                    $column_excel  = $this->cek_column_excel($column+2);
                                    $sheet->SetCellValue($column_excel.''.$rowCount, $hph_qty1);
                                    $column_excel  = $this->cek_column_excel($column+3);
                                    $sheet->SetCellValue($column_excel.''.$rowCount, $hph_qty1_uom);
                                    $column_excel  = $this->cek_column_excel($column+4);
                                    $sheet->SetCellValue($column_excel.''.$rowCount, $hph_qty2);
                                    $column_excel  = $this->cek_column_excel($column+5);
                                    $sheet->SetCellValue($column_excel.''.$rowCount, $hph_qty2_uom);
                                    $column_excel  = $this->cek_column_excel($column+6);
                                    $sheet->SetCellValue($column_excel.''.$rowCount, $hph_qty_op);
                                    $column_excel  = $this->cek_column_excel($column+7);
                                    $sheet->SetCellValue($column_excel.''.$rowCount, $hph_qty1_op_uom);
                                    $column = $column + 8;
                                }
                            }
                            $ke++;
                        }
                    }

                    $ke = 0;
                    foreach($tm['field_view'] as $fview ){
                        for ( $i = 0, $l = count($fview['out'][$ke]); $i<$l; $i++){
                            foreach($fview['out'] as $fviews ){
                                $out_proses    = $row['out_'.$fviews[$i].'_proses'];
                                $column_excel  = $this->cek_column_excel($column);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $out_proses);
                                $column++;
                                // if($view == "Global" || $view == "DetailProduk" ){
                                    $out_lot = $row['out_'.$fviews[$i].'_lot'];
                                    $column_excel  = $this->cek_column_excel($column);
                                    $sheet->SetCellValue($column_excel.''.$rowCount, $out_lot);
                                    $column++;
                                // }
                                
                                $out_qty1      = $row['out_'.$fviews[$i].'_qty1'];
                                $out_qty1_uom  = $row['out_'.$fviews[$i].'_qty1_uom'];
                                $out_qty2      = $row['out_'.$fviews[$i].'_qty2'];
                                $out_qty2_uom  = $row['out_'.$fviews[$i].'_qty2_uom'];
                                $out_qty_op    = $row['out_'.$fviews[$i].'_qty_opname'];
                                $out_qty1_op_uom  = $row['out_'.$fviews[$i].'_qty_opname_uom'];
                         
                                $column_excel  = $this->cek_column_excel($column);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $out_qty1);
                                $column_excel  = $this->cek_column_excel($column+1);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $out_qty1_uom);
                                $column_excel  = $this->cek_column_excel($column+2);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $out_qty2);
                                $column_excel  = $this->cek_column_excel($column+3);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $out_qty2_uom);
                                $column_excel  = $this->cek_column_excel($column+4);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $out_qty_op);
                                $column_excel  = $this->cek_column_excel($column+5);
                                $sheet->SetCellValue($column_excel.''.$rowCount, $out_qty1_op_uom);
                                $column = $column + 6;
                            }
                        }
                        $ke++;
                    }

                    // ADJ OUT
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_out_proses']);
                    $column++;
                    // if($view == "Global" || $view == "DetailProduk" ){
                        $column_excel  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_out_lot']);
                        $column++;
                    // }
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_out_qty1']);
                    $column_excel  = $this->cek_column_excel($column+1);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_out_qty1_uom']);
                    $column_excel  = $this->cek_column_excel($column+2);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_out_qty2']);
                    $column_excel  = $this->cek_column_excel($column+3);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_out_qty2_uom']);
                    $column_excel  = $this->cek_column_excel($column+4);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_out_qty_opname']);
                    $column_excel  = $this->cek_column_excel($column+5);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['adj_out_qty_opname_uom']);
                    $column = $column + 6;
                            
                    // saldo akhir
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_akhir_proses']);
                    $column++;
                    // if($view == "Global" || $view == "DetailProduk" ){
                        $column_excel  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_akhir_lot']);
                        $column++;
                    // }
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_akhir_qty1']);
                    $column_excel  = $this->cek_column_excel($column+1);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_akhir_qty1_uom']);
                    $column_excel  = $this->cek_column_excel($column+2);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_akhir_qty2']);
                    $column_excel  = $this->cek_column_excel($column+3);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_akhir_qty2_uom']);
                    $column_excel  = $this->cek_column_excel($column+4);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_akhir_qty_opname']);
                    $column_excel  = $this->cek_column_excel($column+5);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_akhir_qty_opname_uom']);

                    $rowCount++;

                }


            }

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
            $object->save('php://output');
    
            $xlsData = ob_get_contents();
            ob_end_clean();
            if($view == "Global"){
                $name_file = 'Mutasi Global '.$dept.' '.bln_indo(date('d-m-Y',strtotime($tanggal))).'.xlsx';
            }else if($view == "DetailProduk"){
                $name_file = 'Mutasi Detail Produk '.$dept.' '.bln_indo(date('d-m-Y',strtotime($tanggal))).'.xlsx';
            }else{
                $name_file = 'Mutasi Detail Lot '.$dept.' '.bln_indo(date('d-m-Y',strtotime($tanggal))).'.xlsx';
            }
            $response =  array(
                'op'        => 'ok',
                'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
                'filename'  => $name_file
            );
        
            die(json_encode($response));

        }else{

            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen)->row_array();

            $response =  array(
                'status'    => 'failed',
                'message'   => "Departemen ".$get_dept['nama']." belum terdapat Laporan Mutasi",
            );

            die(json_encode($response));

        }

    }


    function export_excel_mutasi_detail($tanggal,$departemen,$kode_produk,$nama_produk,$kode_transaksi,$lot,$reff_picking,$reff_note)
    {

        $result = $this->cek_dept_mutasi($departemen);
        if($result == true){
            
            $this->load->library('excel');
            $tahun      = date('Y', strtotime($tanggal)); // example 2022
            $bulan      = date('n', strtotime($tanggal)); // example 8
            $dept       = $this->_module->get_nama_dept_by_kode($departemen)->row_array();

            ob_start();
            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);
            
            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen);
            $type_dept = $get_dept->row_array();
            if($type_dept['type_dept'] == 'manufaktur'){

                $table          = 'acc_mutasi_'.strtolower($departemen).'_rm_detail';
                $result         = $this->create_header_detail(true);
                $result_where   = $this->create_where($kode_produk,$nama_produk,$kode_transaksi,$lot,$reff_picking,$reff_note);
                $acc_mutasi_rm  = $this->get_acc_mutasi_detail_by_kode($table,$tahun,$bulan,$result_where,'','',true);
                $table_mutasi[] =  array('judul'            => "Laporan Mutasi Bahan Baku",
                                        'table_1'           => 'Yes',
                                        'head_table'        => $result,
                                        'record'            => $acc_mutasi_rm[2],
                                        'count_record'      => ($acc_mutasi_rm[1]));

                $table          = 'acc_mutasi_'.strtolower($departemen).'_fg_detail';
                $result         = $this->create_header_detail(true);
                $result_where   = $this->create_where($kode_produk,$nama_produk,$kode_transaksi,$lot,$reff_picking,$reff_note);
                $acc_mutasi_fg  = $this->get_acc_mutasi_detail_by_kode($table,$tahun,$bulan,$result_where,'','',true);

                $table_mutasi[] =  array('judul'            => "Laporan Mutasi Barang Jadi",
                                        'table_2'           => 'Yes',
                                        'head_table'        => $result,
                                        'record'            => $acc_mutasi_fg[2],
                                        'count_record'      => ($acc_mutasi_fg[1]));
                                        
            }else{

                $table       = 'acc_mutasi_'.strtolower($departemen).'_detail';
                $result      = $this->create_header_detail(true);
                $result_where= $this->create_where($kode_produk,$nama_produk,$kode_transaksi,$lot,$reff_picking,$reff_note);
                $acc_mutasi  = $this->get_acc_mutasi_detail_by_kode($table,$tahun,$bulan,$result_where,'','',true);
                
                $table_mutasi[] =  array('judul'            => "Laporan Mutasi",
                                        'table_1'           => 'Yes',
                                        'head_table'        => $result,
                                        'record'            => $acc_mutasi[2],
                                        'count_record'      => ($acc_mutasi[1]));
            }
            
            // array judul WorkSheet
            if($type_dept['type_dept'] == 'manufaktur'){// two table
                $object->createSheet();
                $sheet1 = $object->setActiveSheetIndex(0);
                $sheet1->setTitle('Bahan Baku');
                $sheet1->getStyle("A1:U6")->getFont()->setBold(true);

                $sheet2 = $object->setActiveSheetIndex(1);
                $sheet2->setTitle('Barang Jadi');
                $sheet2->getStyle("A1:U6")->getFont()->setBold(true);

            }else{// one table
                $sheet1 = $object->setActiveSheetIndex(0);
                $sheet1->getStyle("A1:U6")->getFont()->setBold(true);
            }


            $rowCount = 1;
            $loop     = 1;
            foreach($table_mutasi as $tm){

                if($loop == 1){
                    $sheet = $sheet1;
                }else{
                    $sheet    = $sheet2;
                    $rowCount = 1;
                }

                // SET JUDUL
                $sheet->SetCellValue('A'.$rowCount, $tm['judul']);
                $sheet->getStyle('A'.$rowCount)->getAlignment()->setIndent(1);
                $sheet->mergeCells('A'.$rowCount.':N'.$rowCount);
 
                // set Departemen
                $rowCount = $rowCount+1;
                $sheet->SetCellValue('A'.$rowCount, 'Departemen');
                $sheet->mergeCells('A'.$rowCount.':B'.$rowCount);
                $sheet->SetCellValue('C'.$rowCount, ': '.$dept['nama']);
                $sheet->mergeCells('C'.$rowCount.':D'.$rowCount);
 
                // set periode
                $rowCount = $rowCount+1;
                $sheet->SetCellValue('A'.$rowCount, 'Periode');
                $sheet->mergeCells('A'.$rowCount.':B'.$rowCount);
                $sheet->SetCellValue('C'.$rowCount, ': '.bln_indo(date('d-m-Y',strtotime($tanggal))) );
                $sheet->mergeCells('C'.$rowCount.':F'.$rowCount);

                // header table
                $column     = 0;
                $rowCount   = $rowCount + 3;
                foreach ($tm['head_table'] as $field) {
        	    		$object->getActiveSheet()->setCellValueByColumnAndRow($column, $rowCount, $field);  
                        $column++;
                }
                

                $no    = 1;
                $rowCount = $rowCount + 1;
                foreach($tm['record'] as $row){

                    if($row->type == 'in'){
                        $depart  = $row->dept_id_dari;
                    }else{ 
                        $depart  = $row->dept_id_tujuan;
                    }

                    $sheet->SetCellValue('A'.$rowCount, ($no++));
                    $sheet->SetCellValue('B'.$rowCount, $row->posisi_mutasi);
                    $sheet->SetCellValue('C'.$rowCount, $depart);
                    $sheet->SetCellValue('D'.$rowCount, $row->type);
                    $sheet->SetCellValue('E'.$rowCount, $row->kode_transaksi);
                    $sheet->SetCellValue('F'.$rowCount, $row->tanggal_transaksi);
                    $sheet->SetCellValue('G'.$rowCount, $row->kode_produk);
                    $sheet->SetCellValue('H'.$rowCount, $row->nama_produk);
                    $sheet->SetCellValue('I'.$rowCount, $row->nama_category);
                    $sheet->SetCellValue('J'.$rowCount, $row->lot);
                    $sheet->SetCellValue('K'.$rowCount, $row->qty);
                    $sheet->SetCellValue('L'.$rowCount, $row->uom);
                    $sheet->SetCellValue('M'.$rowCount, $row->qty2);
                    $sheet->SetCellValue('N'.$rowCount, $row->uom2);
                    $sheet->SetCellValue('O'.$rowCount, $row->qty_opname);
                    $sheet->SetCellValue('P'.$rowCount, $row->uom_opname);
                    $sheet->SetCellValue('Q'.$rowCount, $row->origin);
                    $sheet->SetCellValue('R'.$rowCount, $row->method);
                    $sheet->SetCellValue('S'.$rowCount, $row->sc);
                    $sheet->SetCellValue('T'.$rowCount, $row->nama_sales_group);
                    $sheet->SetCellValue('U'.$rowCount, $row->reff_note);
                    if($rowCount == 10000){
                        // break;
                    }
                    $rowCount++;
                }


                $loop++;
                $rowCount++;
            }
        
            $name_file ='Mutasi Detail Lot '.$dept['nama'].' '.bln_indo(date('d-m-Y',strtotime($tanggal))).'.xlsx';

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
            $object->save('php://output');
    
            $xlsData = ob_get_contents();
            ob_end_clean();
    
            $response =  array(
                'status'    => 'ok',
                'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
                'filename'  => $name_file
            );
        
            die(json_encode($response));
        
        }else{
            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen)->row_array();

            // echo "<script>alert('Departemen ".$get_dept['nama']." belum terdapat Laporan Mutasi');location.replace(history.back())</script>";
            $response =  array(
                'status'    => 'failed',
                'message'   => "Departemen ".$get_dept['nama']." belum terdapat Laporan Mutasi",
            );
        
            die(json_encode($response));
        }

    }

    function export_excel_mutasi_global($view,$tanggal,$departemen,$kode_produk,$nama_produk)
    {

        $result = $this->cek_dept_mutasi($departemen);
        if($result == true){

            $this->load->library('excel');
            $tahun      = date('Y', strtotime($tanggal)); // example 2022
            $bulan      = date('n', strtotime($tanggal)); // example 8
            $dept       = $this->_module->get_nama_dept_by_kode($departemen)->row_array();

            ob_start();
            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);

            // cek tipe departemen
            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen);
            $type_dept = $get_dept->row_array();
            if($type_dept['type_dept'] == 'manufaktur'){
                // rm
                $mutasi_dept_rm = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'rm')->result();
                if($view == "Global"){
                    $table          = 'acc_mutasi_'.strtolower($departemen).'_rm_global';
                    $result_where   = $this->create_where('',$nama_produk,'','','','');
                }else{ // detailproduk
                    $table          = 'acc_mutasi_'.strtolower($departemen).'_rm';
                    $result_where   = $this->create_where($kode_produk,$nama_produk,'','','','');
                }
                $result            = $this->create_header($view,$mutasi_dept_rm,true);
                $rm_field          = $result[0];
                $rm_head_table1    = $result[1];
                $rm_head_table2    = $result[2];
                $rm_jml_in         = $result[3];
                $rm_jml_out        = $result[4];
                $acc_mutasi_rm     = $this->get_acc_mutasi_by_kode($view,$table,$tahun,$bulan,$rm_field,$result_where);

                $table_mutasi[] = array('judul'         => "Laporan Mutasi Bahan Baku",
                                        'record'        => $acc_mutasi_rm[0], 
                                        'count_record'  => $acc_mutasi_rm[1],
                                        'head_table1'   => $rm_head_table1, 
                                        'head_table2'   => $rm_head_table2, 
                                        'count_in'      => $rm_jml_in, 
                                        'count_out'     => $rm_jml_out);

                // fg
                $mutasi_dept_fg = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'fg')->result();
                if($view == "Global"){
                    $table2         = 'acc_mutasi_'.strtolower($departemen).'_fg_global';
                    $result_where    = $this->create_where('',$nama_produk,'','','','');
                }else{
                    $table2         = 'acc_mutasi_'.strtolower($departemen).'_fg';
                    $result_where   = $this->create_where($kode_produk,$nama_produk,'','','','');
                }
                $result2           = $this->create_header($view,$mutasi_dept_fg,true);
                $fg_field          = $result2[0];
                $fg_head_table1    = $result2[1];
                $fg_head_table2    = $result2[2];
                $fg_jml_in         = $result2[3];
                $fg_jml_out        = $result2[4];
                $acc_mutasi_fg     = $this->get_acc_mutasi_by_kode($view,$table2,$tahun,$bulan,$fg_field,$result_where);

                $table_mutasi[]    = array('judul'         => "Laporan Mutasi Barang Jadi",
                                            'record'        => $acc_mutasi_fg[0],
                                            'count_record'  => $acc_mutasi_fg[1],
                                            'head_table1'   => $fg_head_table1, 
                                            'head_table2'   => $fg_head_table2, 
                                            'count_in'      => $fg_jml_in, 
                                            'count_out'     => $fg_jml_out);

            }else{

                $mutasi_dept = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'')->result();
                if($view == "Global"){
                    $table       = 'acc_mutasi_'.strtolower($departemen).'_global';
                    $result_where   = $this->create_where('',$nama_produk,'','','','');
                }else{
                    $table       = 'acc_mutasi_'.strtolower($departemen);
                    $result_where   = $this->create_where($kode_produk,$nama_produk,'','','','');
                }
                $result      = $this->create_header($view,$mutasi_dept,true);

                $field       = $result[0];
                $head_table1 = $result[1];
                $head_table2 = $result[2];
                $jml_in      = $result[3];
                $jml_out     = $result[4];

                // $acc_mutasi  = $this->m_mutasi->acc_mutasi_by_kode($table,$tahun,$bulan,$field)->result();
                $acc_mutasi     = $this->get_acc_mutasi_by_kode($view,$table,$tahun,$bulan,$field,$result_where);
                $table_mutasi[]    = array( 'judul'         => "Laporan Mutasi",
                                            'record'        => $acc_mutasi[0], 
                                            'count_record'  => $acc_mutasi[1],
                                            'head_table1'   => $head_table1, 
                                            'head_table2'   => $head_table2, 
                                            'count_in'      => $jml_in, 
                                            'count_out'     => $jml_out);

            }

            // array judul WorkSheet
            if($type_dept['type_dept'] == 'manufaktur'){// two table
                $object->createSheet();
                $sheet1 = $object->setActiveSheetIndex(0);
                $sheet1->setTitle('Bahan Baku');

                $sheet2 = $object->setActiveSheetIndex(1);
                $sheet2->setTitle('Barang Jadi');
                $sheet2->getStyle("A1:T6")->getFont()->setBold(true);

            }else{// one table
                $sheet1 = $object->setActiveSheetIndex(0);
            }

            $rowCount = 1;
            $loop     = 1;

            foreach($table_mutasi as $tm){

                if($loop == 1){
                    $sheet    = $sheet1;
                }else{
                    $sheet    = $sheet2;
                    $rowCount = 1;
                }
                $rowCountFirst = $rowCount;

                // SET JUDUL
                $sheet->SetCellValue('A'.$rowCount, $tm['judul']);
                $sheet->getStyle('A'.$rowCount)->getAlignment()->setIndent(1);
                $sheet->mergeCells('A'.$rowCount.':N'.$rowCount);

                // set Departemen
                $rowCount = $rowCount+1;
                $sheet->SetCellValue('A'.$rowCount, 'Departemen');
                $sheet->mergeCells('A'.$rowCount.':B'.$rowCount);
                $sheet->SetCellValue('C'.$rowCount, ': '.$dept['nama']);
                $sheet->mergeCells('C'.$rowCount.':D'.$rowCount);

                // set periode
                $rowCount = $rowCount+1;
                $sheet->SetCellValue('A'.$rowCount, 'Periode');
                $sheet->mergeCells('A'.$rowCount.':B'.$rowCount);
                $sheet->SetCellValue('C'.$rowCount, ': '.bln_indo(date('d-m-Y',strtotime($tanggal))) );
                $sheet->mergeCells('C'.$rowCount.':F'.$rowCount);

                // header table
                $column   = 0; // A,B,C,D dst
                $column_e = 1;
                $rowCount   = $rowCount + 4;
                foreach ($tm['head_table1'] as $field) {
                    foreach($field as $field2 => $field22){
                        //var_dump($field);
                        if($field2 == 'info'){
                            for (  $i = 0, $l = count($field22); $i < $l; $i++ ) {
                                foreach($field22[$i] as $field3 => $field33){
                                    $sheet->setCellValueByColumnAndRow($column, $rowCount, $field33); 
                                    $column_excel  = $this->cek_column_excel($column_e);
                                    $rowCountMerge = $rowCount + 1;
                                    $sheet->mergeCells($column_excel.''.$rowCount.':'.$column_excel.''.$rowCountMerge);
                                    $sheet->getStyle($column_excel.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                                    $column++;
                                    $column_e++;
                                }
                            }
                        }else if($field2 == 'awal' || $field2 == 'akhir' || $field2 == 'in' || $field2 == 'out' || $field2 == 'adj_in' || $field2 == 'adj_out' || $field2 == 'count_in' || $field2 == 'count_out'  ){

                            for ( $i = 0, $l = count($field22); $i < $l; $i++ ) {
                                $column_excel_start  = $this->cek_column_excel($column_e);
                                $sheet->getStyle($column_excel_start.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                foreach($field22[$i] as $field3 => $field33){
                                    
                                    $sheet->setCellValueByColumnAndRow($column, $rowCount, $field33); 
                                    // $sheet->mergeCells($column_excel_start.'7:'.$column_excel_finish.'7');
                                    if($view == "Global"){
                                        $column_e = $column_e + 3;
                                        $column = $column + 3;
                                    }else{
                                        $column_e = $column_e + 7;
                                        $column = $column + 7;
                                    }
                                }
                                $column_excel_finish = $this->cek_column_excel($column_e-1);
                                $sheet->mergeCells($column_excel_start.''.$rowCount.':'.$column_excel_finish.''.$rowCount);

                            }
                            
                        }
                    
                    }
                }

                // head table 2
                if($view == "Global"){
                    $column   = 2; // A,B, dst
                }else{
                    $column   = 5; // A,B,C,D,E dst
                }
                $column_e = 1;
                $rowCount = $rowCount + 1;
                foreach ($tm['head_table2'] as $field) {
                    foreach($field as $field2 => $field22){
                            for (  $i = 0, $l = count($field22); $i < $l; $i++ ) {
                                foreach($field22[$i] as $field3 => $field33){
                                    $sheet->setCellValueByColumnAndRow($column, $rowCount, $field33); 
                                    $column++;
                                    $column_e++;
                                }
                            }
                    }
                }

                // bold header table
                $column_excel  = $this->cek_column_excel($column);
                $sheet->getStyle("A".$rowCountFirst.":".$column_excel."".$rowCountMerge)->getFont()->setBold(true);

                //body
                $no    = 1;
                $rowCount = $rowCount + 1;
                $id_category   = '';
                $nama_category = '';
                $cat_total_lot_s_awal = 0;
                $cat_total_qty_s_awal = 0;
                $cat_total_qty2_s_awal = 0;
                $cat_total_qty_opname_s_awal = 0;

                $cat_total_lot_s_akhir = 0;
                $cat_total_qty_s_akhir = 0;
                $cat_total_qty2_s_akhir = 0;
                $cat_total_qty_opname_s_akhir = 0;

                $cat_total_lot_adj_in    = 0;
                $cat_total_qty_adj_in    = 0;
                $cat_total_qty2_adj_in   = 0;
                $cat_total_qty_opname_adj_in  =  0;

                $cat_total_lot_adj_out    = 0;
                $cat_total_qty_adj_out    = 0;
                $cat_total_qty2_adj_out   = 0;
                $cat_total_qty_opname_adj_out  =  0;
             
                $cat_qty1_uom_s_awal    = '';
                $cat_qty2_uom_s_awal    = '';
                $cat_opname_uom_s_awal  = '';

                $cat_qty1_uom_s_akhir    = '';
                $cat_qty2_uom_s_akhir    = '';
                $cat_opname_uom_s_akhir  = '';

                $cat_qty1_uom_adj_in   = '';
                $cat_qty2_uom_adj_in   = '';
                $cat_opname_uom_adj_in = '';

                $cat_qty1_uom_adj_out   = '';
                $cat_qty2_uom_adj_out   = '';
                $cat_opname_uom_adj_out = '';

                $cat_qty1_uom_s_akhir   = '';
                $cat_qty2_uom_s_akhir   = '';
                $cat_opname_uom_s_akhir = '';

                $arr_in      = [];
                $arr_in2     = [];
                $arr_out     = [];
                $arr_out2    = [];
                $arr_tot_in            = [];
                $arr_tot_out           = [];

                foreach ($tm['record'] as $row) {
                    
                    $in_total_lot         = 0;
                    $in_total_qty1        = 0;
                    $in_total_qty2        = 0;
                    $in_total_qty_opname  = 0;
                    $out_total_lot         = 0;
                    $out_total_qty1        = 0;
                    $out_total_qty2        = 0;
                    $out_total_qty_opname  = 0;

                    $in_total_mtr         = 0;
                    $in_total_kg          = 0;

                    $out_total_mtr         = 0;
                    $out_total_kg          = 0;

                    if($view == "DetailProduk"){
                        if($row['id_category'] != $id_category AND $id_category != '' AND $view == "DetailProduk"){
                            $no = 1;

                            $this->create_row_total($sheet,$rowCount,$nama_category,$cat_total_lot_s_awal,$cat_total_qty_s_awal,$cat_qty1_uom_s_awal,$cat_total_qty2_s_awal,$cat_qty2_uom_s_awal,$cat_total_qty_opname_s_awal,$cat_opname_uom_s_awal,$tm['count_in'],$arr_in2,$cat_total_lot_adj_in,$cat_total_qty_adj_in,$cat_qty1_uom_adj_in,$cat_total_qty2_adj_in,$cat_qty2_uom_adj_in,$cat_total_qty_opname_adj_in,$cat_opname_uom_adj_in,$arr_tot_in,$tm['count_out'],$arr_out2, $cat_total_lot_adj_out, $cat_total_qty_adj_out, $cat_qty1_uom_adj_out, $cat_total_qty2_adj_out, $cat_qty2_uom_adj_out, $cat_total_qty_opname_adj_out, $cat_opname_uom_adj_out,$arr_tot_out,$cat_total_lot_s_akhir,$cat_total_qty_s_akhir,$cat_qty1_uom_s_akhir,$cat_total_qty2_s_akhir,$cat_qty2_uom_s_akhir,$cat_total_qty_opname_s_akhir,$cat_opname_uom_s_akhir);

                            $cat_total_lot_s_awal = 0;
                            $cat_total_qty_s_awal = 0;
                            $cat_total_qty2_s_awal = 0;
                            $cat_total_qty_opname_s_awal = 0;
                    
                            $cat_total_lot_s_akhir = 0;
                            $cat_total_qty_s_akhir = 0;
                            $cat_total_qty2_s_akhir = 0;
                            $cat_total_qty_opname_s_akhir = 0;
                    
                            $cat_total_lot_adj_in    = 0;
                            $cat_total_qty_adj_in    = 0;
                            $cat_total_qty2_adj_in   = 0;
                            $cat_total_qty_opname_adj_in  =  0;
                    
                            $cat_total_lot_adj_out    = 0;
                            $cat_total_qty_adj_out    = 0;
                            $cat_total_qty2_adj_out   = 0;
                            $cat_total_qty_opname_adj_out  =  0;
                    
                            $cat_total_lot_in    = 0;
                            $cat_total_qty_in    = 0;
                            $cat_total_qty2_in   = 0;
                            $cat_total_qty_opname_in  =  0;
                    
                            $cat_total_lot_out    = 0;
                            $cat_total_qty_out    = 0;
                            $cat_total_qty2_out   = 0;
                            $cat_total_qty_opname_out  =  0;
                    
                            $cat_qty1_uom_s_awal = '';
                            $cat_qty2_uom_s_awal = '';
                            $cat_opname_uom_s_awal = '';
                    
                            $cat_qty1_uom_s_akhir = '';
                            $cat_qty2_uom_s_akhir = '';
                            $cat_opname_uom_s_akhir = '';
                    
                            $cat_qty1_uom_adj_in   = '';
                            $cat_qty2_uom_adj_in   = '';
                            $cat_opname_uom_adj_in = '';
                    
                            $cat_qty1_uom_adj_out   = '';
                            $cat_qty2_uom_adj_out   = '';
                            $cat_opname_uom_adj_out = '';
                    
                            $cat_qty1_uom_s_akhir   = '';
                            $cat_qty2_uom_s_akhir   = '';
                            $cat_opname_uom_s_akhir = '';
                    
                            $arr_in2     = [];
                            $arr_out2    = [];
                            $arr_tot_in  = [];
                            $arr_tot_out = [];
                                                
                            $rowCount = $rowCount + 1;
                        }
                    }

                    if($view == "DetailProduk"){
                        $id_category   = $row['id_category'];
                        $nama_category = $row['nama_category'];
                        //total saldo awal
                        $cat_total_lot_s_awal = $cat_total_lot_s_awal + $row['s_awal_lot'];
                        $cat_total_qty_s_awal = $cat_total_qty_s_awal + $row['s_awal_qty1'];
                        $cat_total_qty2_s_awal = $cat_total_qty2_s_awal + $row['s_awal_qty2'];
                        $cat_total_qty_opname_s_awal = $cat_total_qty_opname_s_awal + $row['s_awal_qty_opname'];
    
                        //total saldo akhir
                        $cat_total_lot_s_akhir = $cat_total_lot_s_akhir + $row['s_akhir_lot'];
                        $cat_total_qty_s_akhir = $cat_total_qty_s_akhir + $row['s_akhir_qty1'];
                        $cat_total_qty2_s_akhir = $cat_total_qty2_s_akhir + $row['s_akhir_qty2'];
                        $cat_total_qty_opname_s_akhir = $cat_total_qty_opname_s_akhir + $row['s_akhir_qty_opname'];
     
                        //total adj in
                        $cat_total_lot_adj_in     = $cat_total_lot_adj_in + $row['adj_in_lot'];
                        $cat_total_qty_adj_in     = $cat_total_qty_adj_in + $row['adj_in_qty1'];
                        $cat_total_qty2_adj_in    = $cat_total_qty2_adj_in + $row['adj_in_qty2'];
                        $cat_total_qty_opname_adj_in = $cat_total_qty_opname_adj_in + $row['adj_in_qty_opname'];
    
                        //total adj out
                        $cat_total_lot_adj_out     = $cat_total_lot_adj_out + $row['adj_out_lot'];
                        $cat_total_qty_adj_out     = $cat_total_qty_adj_out + $row['adj_out_qty1'];
                        $cat_total_qty2_adj_out    = $cat_total_qty2_adj_out + $row['adj_out_qty2'];
                        $cat_total_qty_opname_adj_out = $cat_total_qty_opname_adj_out + $row['adj_out_qty_opname'];
                    }else{
                        $id_category   = "";
                        $nama_category = "";
                    }
                    
                    
                    $sheet->SetCellValue('A'.$rowCount, ($no++));
                    $column_awal = 2;
                    $column = 6;
                    if($view == "DetailProduk"){
                        $column_excel  = $this->cek_column_excel($column_awal);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_category']);
                        $column_excel  = $this->cek_column_excel($column_awal+1);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['kode_produk']);
                        $column = 13;
                        $column_awal = 4;

                        $column_excel  = $this->cek_column_excel($column_awal);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_produk']);
                        $column_excel  = $this->cek_column_excel($column_awal+1);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['product_parent']);
                        $column_excel  = $this->cek_column_excel($column_awal+2);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_lot']);
                        $column_excel  = $this->cek_column_excel($column_awal+3);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty1']);
                        $column_excel  = $this->cek_column_excel($column_awal+4);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty1_uom']);
                        $column_excel  = $this->cek_column_excel($column_awal+5);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty2']);
                        $column_excel  = $this->cek_column_excel($column_awal+6);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty2_uom']);
                        $column_excel  = $this->cek_column_excel($column_awal+7);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty_opname']);
                        $column_excel  = $this->cek_column_excel($column_awal+8);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty_opname_uom']);

                         // info uom
                        if($row['s_awal_qty1_uom'] != ''){// jik ada
                            $qty1_uom        = $row['s_awal_qty1_uom'];
                            $cat_qty1_uom_s_awal   = $row['s_awal_qty1_uom'];
                        }else{
                            $qty1_uom        = '';
                        }
                        if($row['s_awal_qty2_uom'] != ''){
                            $qty2_uom        = $row['s_awal_qty2_uom'];
                            $cat_qty2_uom_s_awal   = $row['s_awal_qty2_uom'];
                        }else{
                            $qty2_uom        = '';
                        }
                        if($row['s_awal_qty_opname_uom'] != ''){
                            $qty_opname_uom  = $row['s_awal_qty_opname_uom'];
                            $cat_opname_uom_s_awal   = $row['s_awal_qty_opname_uom'];
                        }else{
                            $qty_opname_uom  = '';
                        }

                    }else{// global

                        $column_excel  = $this->cek_column_excel($column_awal);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_produk']);
                        $column_excel  = $this->cek_column_excel($column_awal+1);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_lot']);
                        $column_excel  = $this->cek_column_excel($column_awal+2);

                        if($row['s_awal_mtr'] == null){
                            $sAwalMtr = 0;
                        }else{
                            $sAwalMtr = $row['s_awal_mtr'];
                        }

                        $sheet->SetCellValue($column_excel.''.$rowCount, $sAwalMtr);

                        $column_excel  = $this->cek_column_excel($column_awal+3);
                        $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_kg']);
                    }
                   
                    // IN
                    $no_in  = 1;
                    $n      = $tm['count_in'];
                    for ( $d = 0; $d < $n; $d++) {

                        if($view =="DetailProduk"){

                            $in_lot         = $row['in'.$no_in.'_lot'];
                            $in_qty1        = $row['in'.$no_in.'_qty1'];
                            $in_qty1_uom    = $row['in'.$no_in.'_qty1_uom'];
                            $in_qty2        = $row['in'.$no_in.'_qty2'];
                            $in_qty2_uom    = $row['in'.$no_in.'_qty2_uom'];
                            $in_opname      = $row['in'.$no_in.'_qty_opname'];
                            $in_opname_uom  = $row['in'.$no_in.'_qty_opname_uom'];
                            $column_excel  = $this->cek_column_excel($column);
                            $sheet->SetCellValue($column_excel.''.$rowCount, $in_lot);
                            $column_excel1  = $this->cek_column_excel($column+1);
                            $sheet->SetCellValue($column_excel1.''.$rowCount, $in_qty1);
                            $column_excel2  = $this->cek_column_excel($column+2);
                            $sheet->SetCellValue($column_excel2.''.$rowCount, $in_qty1_uom);
                            $column_excel3  = $this->cek_column_excel($column+3);
                            $sheet->SetCellValue($column_excel3.''.$rowCount, $in_qty2);
                            $column_excel4  = $this->cek_column_excel($column+4);
                            $sheet->SetCellValue($column_excel4.''.$rowCount, $in_qty2_uom);
                            $column_excel5  = $this->cek_column_excel($column+5);
                            $sheet->SetCellValue($column_excel5.''.$rowCount, $in_opname);
                            $column_excel6  = $this->cek_column_excel($column+6);
                            $sheet->SetCellValue($column_excel6.''.$rowCount, $in_opname_uom);

                            $in_total_lot         = $in_total_lot+($in_lot);
                            $in_total_qty1        = $in_total_qty1+($in_qty1);
                            $in_total_qty2        = $in_total_qty2+($in_qty2);
                            $in_total_qty_opname  = $in_total_qty_opname+($in_opname);

                            $arr_in[] = array( 'in_lot'     => $in_lot, 
                                            'in_qty1'    => $in_qty1,
                                            'in_qty1_uom'=> $in_qty1_uom,
                                            'in_qty2'    => $in_qty2,
                                            'in_qty2_uom'=> $in_qty2_uom,
                                            'in_opname'  => $in_opname,
                                            'in_opname_uom'=> $in_opname_uom
                            );

                            // info uom
                            if($in_qty1_uom != ''){
                                $qty1_uom        = $in_qty1_uom;
                            }
                            if($in_qty2_uom != ''){
                                $qty2_uom        = $in_qty2_uom;
                            }
                            if($in_opname_uom != ''){
                                $qty_opname_uom  = $in_opname_uom;
                            }
                            $column = $column + 7;

                        }else{

                            $in_lot         = $row['in'.$no_in.'_lot'];
                            $in_mtr         = $row['in'.$no_in.'_mtr'];
                            $in_kg          = $row['in'.$no_in.'_kg'];
                            $column_excel  = $this->cek_column_excel($column);
                            $sheet->SetCellValue($column_excel.''.$rowCount, $in_lot);
                            $column_excel1  = $this->cek_column_excel($column+1);
                            if($in_mtr == null){
                                $INMtr = 0;
                            }else{
                                $INMtr = $in_mtr;
                            }
                            $sheet->SetCellValue($column_excel1.''.$rowCount, $INMtr);
                            $column_excel2  = $this->cek_column_excel($column+2);
                            $sheet->SetCellValue($column_excel2.''.$rowCount, $in_kg);

                            $in_total_lot       = $in_total_lot+($in_lot);
                            $in_total_mtr       = $in_total_mtr+($INMtr);
                            $in_total_kg        = $in_total_kg+($in_kg);

                            $column = $column + 3;

                        }

                        $no_in++;
                    }
                    
                    $arr_in2[] = $arr_in;
                    $arr_in    = [];

                    if($view == "DetailProduk"){

                        // ADJ IN
                        $column_excel_adj  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_lot']);
                        $column_excel_adj  = $this->cek_column_excel($column+1);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty1']);
                        $column_excel_adj  = $this->cek_column_excel($column+2);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty1_uom']);
                        $column_excel_adj  = $this->cek_column_excel($column+3);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty2']);
                        $column_excel_adj  = $this->cek_column_excel($column+4);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty2_uom']);
                        $column_excel_adj  = $this->cek_column_excel($column+5);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty_opname']);
                        $column_excel_adj  = $this->cek_column_excel($column+6);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty_opname_uom']);

                        $in_total_lot         = $in_total_lot+($row['adj_in_lot']);
                        $in_total_qty1        = $in_total_qty1+($row['adj_in_qty1']);
                        $in_total_qty2        = $in_total_qty2+($row['adj_in_qty2']);
                        $in_total_qty_opname  = $in_total_qty_opname+($row['adj_in_qty_opname']);

                        // info uom
                        if($row['adj_in_qty1_uom'] != ''){
                            $qty1_uom        = $row['adj_in_qty1_uom'];
                            $cat_qty1_uom_adj_in   =  $row['adj_in_qty1_uom'];
                        }
                        if($row['adj_in_qty2_uom'] != ''){
                            $qty2_uom        = $row['adj_in_qty2_uom'];
                            $cat_qty2_uom_adj_in   =  $row['adj_in_qty2_uom'];
                        }
                        if($row['adj_in_qty_opname_uom'] != ''){
                            $qty_opname_uom  = $row['adj_in_qty_opname_uom'];
                            $cat_opname_uom_adj_in   = $row['adj_in_qty_opname_uom'];
                        }

                        $column = $column + 7;

                    }else{// global
                        $column_excel_adj  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_lot']);
                        $column_excel_adj  = $this->cek_column_excel($column+1);
                        if($row['adj_in_mtr'] == null){
                            $adjINMtr = 0;
                        }else{
                            $adjINMtr = $row['adj_in_mtr'];
                        }
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $adjINMtr);
                        $column_excel_adj  = $this->cek_column_excel($column+2);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_kg']);

                        $in_total_lot       = $in_total_lot+($row['adj_in_lot']);
                        $in_total_mtr       = $in_total_mtr+($adjINMtr);
                        $in_total_kg        = $in_total_kg+($row['adj_in_kg']);

                        $column = $column + 3;

                    }

                    if($view =="DetailProduk"){

                        // Total IN
                        $column_excel_tot_in  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_lot);
                        $column_excel_tot_in  = $this->cek_column_excel($column+1);
                        $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_qty1);
                        $column_excel_tot_in  = $this->cek_column_excel($column+2);
                        $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $qty1_uom);
                        $column_excel_tot_in  = $this->cek_column_excel($column+3);
                        $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_qty2);
                        $column_excel_tot_in  = $this->cek_column_excel($column+4);
                        $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $qty2_uom);
                        $column_excel_tot_in  = $this->cek_column_excel($column+5);
                        $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_qty_opname);
                        $column_excel_tot_in  = $this->cek_column_excel($column+6);
                        $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $qty_opname_uom);

                        $column = $column + 7;

                        $arr_tot_in[] = array("tot_lot"     => $in_total_lot,
                                            "tot_qty1"    => $in_total_qty1,
                                            "qty1_uom"    => $qty1_uom,
                                            "tot_qty2"    => $in_total_qty2,
                                            "qty2_uom"    => $qty2_uom,
                                            "tot_qty_opname"    => $in_total_qty_opname,
                                            "qty_opname_uom"    => $qty_opname_uom,
                        );
                    }else{
                        // Total IN
                        $column_excel_tot_in  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_lot);
                        $column_excel_tot_in  = $this->cek_column_excel($column+1);
                        $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_mtr);
                        $column_excel_tot_in  = $this->cek_column_excel($column+2);
                        $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_kg);

                        $column = $column + 3;

                    }


                    // OUT
                    $no_out = 1;
                    $n      = $tm['count_out'];
                    for ( $d = 0; $d < $n; $d++) {

                        if($view == "DetailProduk"){

                            $out_lot         = $row['out'.$no_out.'_lot'];
                            $out_qty1        = $row['out'.$no_out.'_qty1'];
                            $out_qty1_uom    = $row['out'.$no_out.'_qty1_uom'];
                            $out_qty2        = $row['out'.$no_out.'_qty2'];
                            $out_qty2_uom    = $row['out'.$no_out.'_qty2_uom'];
                            $out_opname      = $row['out'.$no_out.'_qty_opname'];
                            $out_opname_uom  = $row['out'.$no_out.'_qty_opname_uom'];
                            $column_excel  = $this->cek_column_excel($column);
                            $sheet->SetCellValue($column_excel.''.$rowCount, $out_lot);
                            $column_excel1  = $this->cek_column_excel($column+1);
                            $sheet->SetCellValue($column_excel1.''.$rowCount, $out_qty1);
                            $column_excel2  = $this->cek_column_excel($column+2);
                            $sheet->SetCellValue($column_excel2.''.$rowCount, $out_qty1_uom);
                            $column_excel3  = $this->cek_column_excel($column+3);
                            $sheet->SetCellValue($column_excel3.''.$rowCount, $out_qty2);
                            $column_excel4  = $this->cek_column_excel($column+4);
                            $sheet->SetCellValue($column_excel4.''.$rowCount, $out_qty2_uom);
                            $column_excel5  = $this->cek_column_excel($column+5);
                            $sheet->SetCellValue($column_excel5.''.$rowCount, $out_opname);
                            $column_excel6  = $this->cek_column_excel($column+6);
                            $sheet->SetCellValue($column_excel6.''.$rowCount, $out_opname_uom);
            
                            $out_total_lot         = $out_total_lot+($out_lot);
                            $out_total_qty1        = $out_total_qty1+($out_qty1);
                            $out_total_qty2        = $out_total_qty2+($out_qty2);
                            $out_total_qty_opname  = $out_total_qty_opname+($out_opname);

                            $arr_out[] = array( 'out_lot'     => $out_lot, 
                                                'out_qty1'    => $out_qty1,
                                                'out_qty1_uom'=> $out_qty1_uom,
                                                'out_qty2'    => $out_qty2,
                                                'out_qty2_uom'=> $out_qty2_uom,
                                                'out_opname'  => $out_opname,
                                                'out_opname_uom'=> $in_opname_uom
                            );
            
                            // info uom
                            if($out_qty1_uom != ''){
                                $qty1_uom        = $out_qty1_uom;
                            }
                            if($out_qty2_uom != ''){
                                $qty2_uom        = $out_qty2_uom;
                            }
                            if($out_opname_uom != ''){
                                $qty_opname_uom  = $out_opname_uom;
                            }
            
                            $column = $column + 7;

                        }else{
                            $out_lot         = $row['out'.$no_out.'_lot'];
                            $out_mtr         = $row['out'.$no_out.'_mtr'];
                            $out_kg          = $row['out'.$no_out.'_kg'];
                            $column_excel  = $this->cek_column_excel($column);
                            $sheet->SetCellValue($column_excel.''.$rowCount, $out_lot);
                            $column_excel1  = $this->cek_column_excel($column+1);
                            if($out_mtr == null){
                                $OUTmtr = 0;
                            }else{
                                $OUTmtr = $out_mtr;
                            }
                            $sheet->SetCellValue($column_excel1.''.$rowCount, $OUTmtr);
                            $column_excel2  = $this->cek_column_excel($column+2);
                            $sheet->SetCellValue($column_excel2.''.$rowCount, $out_kg);

                            $out_total_lot       = $out_total_lot+($out_lot);
                            $out_total_mtr       = $out_total_mtr+($OUTmtr);
                            $out_total_kg        = $out_total_kg+($out_kg);

                            $column = $column + 3;

                        }

                        $no_out++;
                    }

                    $arr_out2[] = $arr_out;
                    $arr_out    = [];

                    if($view == "DetailProduk"){
                        // ADJ OUT
                        $column_excel_adj  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_lot']);
                        $column_excel_adj  = $this->cek_column_excel($column+1);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty1']);
                        $column_excel_adj  = $this->cek_column_excel($column+2);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty1_uom']);
                        $column_excel_adj  = $this->cek_column_excel($column+3);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty2']);
                        $column_excel_adj  = $this->cek_column_excel($column+4);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty2_uom']);
                        $column_excel_adj  = $this->cek_column_excel($column+5);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty_opname']);
                        $column_excel_adj  = $this->cek_column_excel($column+6);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty_opname_uom']);

                        $out_total_lot         = $out_total_lot+($row['adj_out_lot']);
                        $out_total_qty1        = $out_total_qty1+($row['adj_out_qty1']);
                        $out_total_qty2        = $out_total_qty2+($row['adj_out_qty2']);
                        $out_total_qty_opname  = $out_total_qty_opname+($row['adj_out_qty_opname']);

                        // info uom
                        if($row['adj_out_qty1_uom'] != ''){
                            $qty1_uom        = $row['adj_out_qty1_uom'];
                            $cat_qty1_uom_adj_out   =  $row['adj_out_qty1_uom'];
                        }
                        if($row['adj_out_qty2_uom'] != ''){
                            $qty2_uom        = $row['adj_out_qty2_uom'];
                            $cat_qty2_uom_adj_out   =  $row['adj_out_qty2_uom'];
                        }
                        if($row['adj_out_qty_opname_uom'] != ''){
                            $qty_opname_uom  = $row['adj_out_qty_opname_uom'];
                            $cat_opname_uom_adj_out   =  $row['adj_out_qty_opname_uom'];
                        }

                        $column = $column + 7;

                    }else{

                        // ADJ OUT
                        $column_excel_adj  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_lot']);
                        $column_excel_adj  = $this->cek_column_excel($column+1);
                        if($row['adj_out_mtr'] == null){
                            $adjOUTmtr = 0;
                        }else{
                            $adjOUTmtr = $row['adj_out_mtr'];
                        }
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $adjOUTmtr);
                        $column_excel_adj  = $this->cek_column_excel($column+2);
                        $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_kg']);

                        $out_total_lot       = $out_total_lot+($row['adj_out_lot']);
                        $out_total_mtr       = $out_total_mtr+($adjOUTmtr);
                        $out_total_kg        = $out_total_kg+($row['adj_out_kg']);

                        $column = $column + 3;
                    }

                    if($view == "DetailProduk"){
                        // Total OUT
                        $column_excel_tot_out  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_lot);
                        $column_excel_tot_out  = $this->cek_column_excel($column+1);
                        $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_qty1);
                        $column_excel_tot_out  = $this->cek_column_excel($column+2);
                        $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $qty1_uom);
                        $column_excel_tot_out  = $this->cek_column_excel($column+3);
                        $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_qty2);
                        $column_excel_tot_out  = $this->cek_column_excel($column+4);
                        $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $qty2_uom);
                        $column_excel_tot_out  = $this->cek_column_excel($column+5);
                        $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_qty_opname);
                        $column_excel_tot_out  = $this->cek_column_excel($column+6);
                        $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $qty_opname_uom);

                        $arr_tot_out[] = array("tot_lot"        => $out_total_lot,
                                            "tot_qty1"          => $out_total_qty1,
                                            "qty1_uom"          => $qty1_uom,
                                            "tot_qty2"          => $out_total_qty2,
                                            "qty2_uom"          => $qty2_uom,
                                            "tot_qty_opname"    => $out_total_qty_opname,
                                            "qty_opname_uom"    => $qty_opname_uom,
                        );

                        $column = $column + 7;
                    }else{

                        // Total OUT
                        $column_excel_tot_out  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_lot);
                        $column_excel_tot_out  = $this->cek_column_excel($column+1);
                        $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_mtr);
                        $column_excel_tot_out  = $this->cek_column_excel($column+2);
                        $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_kg);

                        $column = $column + 3;
                    }

                    if($view == "DetailProduk"){
                        // Saldo Akhir
                        $column_excel_akhir  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_lot']);
                        $column_excel_akhir  = $this->cek_column_excel($column+1);
                        $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty1']);
                        $column_excel_akhir  = $this->cek_column_excel($column+2);
                        $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty1_uom']);
                        $column_excel_akhir  = $this->cek_column_excel($column+3);
                        $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty2']);
                        $column_excel_akhir  = $this->cek_column_excel($column+4);
                        $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty2_uom']);
                        $column_excel_akhir  = $this->cek_column_excel($column+5);
                        $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty_opname']);
                        $column_excel_akhir  = $this->cek_column_excel($column+6);
                        $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty_opname_uom']);

                        if($row['s_akhir_qty1_uom'] != ''){
                            $cat_qty1_uom_s_akhir   = $row['s_akhir_qty1_uom'];
                        }
                        if($row['s_akhir_qty2_uom'] != ''){
                            $cat_qty2_uom_s_akhir   = $row['s_akhir_qty2_uom'];
                        }
                        if($row['s_akhir_qty_opname_uom'] != ''){
                            $cat_opname_uom_s_akhir   = $row['s_akhir_qty_opname_uom'];
                        }

                    }else{

                        $column_excel_akhir  = $this->cek_column_excel($column);
                        $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_lot']);
                        $column_excel_akhir  = $this->cek_column_excel($column+1);
                        $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_mtr']);
                        $column_excel_akhir  = $this->cek_column_excel($column+2);
                        $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_kg']);
                            
                    }

                    $rowCount++;
                }

                if(count($tm['record']) > 0 AND $view == "DetailProduk"){
                    $this->create_row_total($sheet,$rowCount,$nama_category,$cat_total_lot_s_awal,$cat_total_qty_s_awal,$cat_qty1_uom_s_awal,$cat_total_qty2_s_awal,$cat_qty2_uom_s_awal,$cat_total_qty_opname_s_awal,$cat_opname_uom_s_awal,$tm['count_in'],$arr_in2,$cat_total_lot_adj_in,$cat_total_qty_adj_in,$cat_qty1_uom_adj_in,$cat_total_qty2_adj_in,$cat_qty2_uom_adj_in,$cat_total_qty_opname_adj_in,$cat_opname_uom_adj_in,$arr_tot_in,$tm['count_out'],$arr_out2, $cat_total_lot_adj_out, $cat_total_qty_adj_out, $cat_qty1_uom_adj_out, $cat_total_qty2_adj_out, $cat_qty2_uom_adj_out, $cat_total_qty_opname_adj_out, $cat_opname_uom_adj_out,$arr_tot_out,$cat_total_lot_s_akhir,$cat_total_qty_s_akhir,$cat_qty1_uom_s_akhir,$cat_total_qty2_s_akhir,$cat_qty2_uom_s_akhir,$cat_total_qty_opname_s_akhir,$cat_opname_uom_s_akhir);
                }

                $rowCount = $rowCount + 3;
                $loop++;
            }


            if($view == "Global"){
                $name_file ='Mutasi Global '.$dept['nama'].' '.bln_indo(date('d-m-Y',strtotime($tanggal))).'.xlsx';
            }else{ // detail lot
                $name_file ='Mutasi Detail Produk '.$dept['nama'].' '.bln_indo(date('d-m-Y',strtotime($tanggal))).'.xlsx';
            }

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
            $object->save('php://output');
    
            $xlsData = ob_get_contents();
            ob_end_clean();
    
            $response =  array(
                'op'        => 'ok',
                'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
                'filename'  => $name_file
            );
        
            die(json_encode($response));
        
        }else{
            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
            // echo "<script>alert('Departemen ".$get_dept['nama']." belum terdapat Laporan Mutasi');location.replace(history.back())</script>";
            $response =  array(
                'status'    => 'failed',
                'message'   => "Departemen ".$get_dept['nama']." belum terdapat Laporan Mutasi",
            );
        
            die(json_encode($response));
            
        }

    }

    function export_excel_mutasi_detail_datar($view,$tanggal,$departemen,$parent,$kode_produk,$nama_produk,$lot,$jenis_kain){
        $result = $this->cek_dept_mutasi($departemen);
        $result_2 = $this->cek_dept_mutasi_detail_produk_datar($departemen);
        if($result == true AND $result_2 == true){

            $this->load->library('excel');
            $tahun      = date('Y', strtotime($tanggal)); // example 2022
            $bulan      = date('n', strtotime($tanggal)); // example 8
            $dept       = $this->_module->get_nama_dept_by_kode($departemen)->row_array();

            ob_start();
            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);
            ini_set('memory_limit', '4096M');
            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen);
            $type_dept = $get_dept->row_array();
            if($type_dept['type_dept'] == 'manufaktur'){
            }else{
                $mutasi_dept = $this->m_mutasi->acc_dept_mutasi_by_kode($departemen,'')->result();
                $table       = 'acc_mutasi_'.strtolower($departemen).'_detail_datar';
                $result_where= $this->create_where3($kode_produk,$nama_produk,$lot,$parent,$jenis_kain);
                $result      = $this->create_header($view,$mutasi_dept,true);
                $field       = $result[0];
                $head_table1 = $result[1];
                $head_table2 = $result[2];
                $jml_in      = $result[3];
                $jml_out     = $result[4];
                $jml_type_in = $result[5];
                $jml_type_out = $result[6];
            
                $acc_mutasi  = $this->get_acc_mutasi_detail_datar_by_kode($table,$tahun,$bulan,$field,$result_where,'','',true);                

                $table_mutasi[]    = array( 'judul'         => "Laporan Mutasi",
                                            'record'        => $acc_mutasi[2], 
                                            'count_record'  => $acc_mutasi[1],
                                            'head_table1'   => $head_table1, 
                                            'head_table2'   => $head_table2, 
                                            'count_in'      => $jml_in, 
                                            'count_out'     => $jml_out,
                                            'count_type_in' => $jml_type_in,
                                            'count_type_out'=> $jml_type_out);
            }

            // array judul WorkSheet
            if($type_dept['type_dept'] == 'manufaktur'){// two table
                $object->createSheet();
                $sheet1 = $object->setActiveSheetIndex(0);
                $sheet1->setTitle('Bahan Baku');
                $sheet1->getStyle("A1:U6")->getFont()->setBold(true);

                $sheet2 = $object->setActiveSheetIndex(1);
                $sheet2->setTitle('Barang Jadi');
                $sheet2->getStyle("A1:U6")->getFont()->setBold(true);

            }else{// one table
                $sheet1 = $object->setActiveSheetIndex(0);
                $sheet1->getStyle("A1:U6")->getFont()->setBold(true);
            }

            $rowCount = 1;
            $loop     = 1;
            // $out_or_in = false;
            // $info      = false;
            foreach($table_mutasi as $tm){
                if($loop == 1){
                    $sheet = $sheet1;
                }else{
                    $sheet    = $sheet2;
                    $rowCount = 1;
                }
                $rowCountFirst = $rowCount;
                 
                // SET JUDUL
                $sheet->SetCellValue('A'.$rowCount, $tm['judul']);
                $sheet->getStyle('A'.$rowCount)->getAlignment()->setIndent(1);
                $sheet->mergeCells('A'.$rowCount.':N'.$rowCount);
  
                // set Departemen
                $rowCount = $rowCount+1;
                $sheet->SetCellValue('A'.$rowCount, 'Departemen');
                $sheet->mergeCells('A'.$rowCount.':B'.$rowCount);
                $sheet->SetCellValue('C'.$rowCount, ': '.$dept['nama']);
                $sheet->mergeCells('C'.$rowCount.':D'.$rowCount);
  
                // set periode
                $rowCount = $rowCount+1;
                $sheet->SetCellValue('A'.$rowCount, 'Periode');
                $sheet->mergeCells('A'.$rowCount.':B'.$rowCount);
                $sheet->SetCellValue('C'.$rowCount, ': '.bln_indo(date('d-m-Y',strtotime($tanggal))) );
                $sheet->mergeCells('C'.$rowCount.':F'.$rowCount);
                
                $column     = 0;
                $column_e   = 1;
                $rowCount   = $rowCount + 4;
                foreach ($tm['head_table1'] as $field) {
                    foreach($field as $field2 => $field22){
                        if($field2 == 'count_out'){
                            // break;
                        }
                        if($field2 == 'info'){
                            for (  $i = 0, $l = count($field22); $i < $l; $i++ ) {
                                foreach($field22[$i] as $field3 => $field33){
                                    $sheet->setCellValueByColumnAndRow($column, $rowCount, $field33); 
                                    $column_excel  = $this->cek_column_excel($column_e);
                                    $rowCountMerge = $rowCount + 2;
                                    $sheet->mergeCells($column_excel.''.$rowCount.':'.$column_excel.''.$rowCountMerge);
                                    $sheet->getStyle($column_excel.''.$rowCount)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                                    $sheet->getStyle($column_excel.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
                                    $column++;
                                    $column_e++;
                                }
                            }
                        }else if($field2 == 'awal' || $field2 == 'akhir' || $field2 == 'in' || $field2 == 'out' || $field2 == 'adj_in' || $field2 == 'adj_out' || $field2 == 'count_in' || $field2 == 'count_out'){
                            
                            for ( $i = 0, $l = count($field22); $i < $l; $i++ ) {
                                $column_excel_start  = $this->cek_column_excel($column_e);
                                foreach($field22[$i] as $field3 => $field33){
                                    
                                    $sheet->setCellValueByColumnAndRow($column, $rowCount, $field33); 
                                    // $sheet->mergeCells($column_excel_start.'7:'.$column_excel_finish.'7');
                                    if($view == "Global"){
                                        $column_e = $column_e + 3;
                                        $column = $column + 3;
                                    }else{
                                        $column_e = $column_e + 7;
                                        $column = $column + 7;
                                    }
                                }
                                // merge 7 column 3 baris
                                $rowCountMerge = $rowCount + 1;
                                $column_excel_finish = $this->cek_column_excel($column_e-1);
                                $sheet->mergeCells($column_excel_start.''.$rowCount.':'.$column_excel_finish.''.$rowCountMerge);
                                $sheet->getStyle($column_excel_start.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                                $sheet->getStyle($column_excel_start.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                            }
                            
                        }else if($field2 == 'type_adj_in' || $field2 == 'type_adj_out'){
                                if(count($field22) > 0){
                                    $rowCountMerge = $rowCount + 1;
                                    foreach($field22 as $field3){
                                        // Judul 
                                        $column_excel  = $this->cek_column_excel($column_e);
                                        $sheet->setCellValueByColumnAndRow($column, $rowCount, $field3['nama']); 
                                        $column_e2 = $column_e - 1;
                                        if(count($field3['child']) > 0){
                                            $column_child = $column;
                                            // type ADJ 
                                            foreach ($field3['child'] as $field4){
                                                $column_excel_start2  = $this->cek_column_excel($column_e2 + 1);
                                                $sheet->setCellValueByColumnAndRow($column_child, $rowCount+1, $field4['nama']);
                                                $column_child = $column_child + 7;
                                                $column_e2 = $column_e2 + 7;
                                                $column_excel_finish2  = $this->cek_column_excel($column_e2);
                                                $sheet->mergeCells($column_excel_start2.''.$rowCountMerge.':'.$column_excel_finish2.''.$rowCountMerge);
                                                $sheet->getStyle($column_excel_start2.''.$rowCountMerge)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                                                $sheet->getStyle($column_excel_start2.''.$rowCountMerge)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                            }
                                        }
                                        $column_excel_finish  = $this->cek_column_excel(($column_e-1)+$field3['count_child']);
                                        $sheet->mergeCells($column_excel.''.$rowCount.':'.$column_excel_finish.''.$rowCount);
                                        $sheet->getStyle($column_excel.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::VERTICAL_CENTER );
                                        $sheet->getStyle($column_excel.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                        // $column++;
                                        $column_e++;
                                    }
                                    $column = $column_child;
                                    $column_e = $column_e + $field3['count_child'] - 1;
                                }
                        }
                     
                    }
                }

               

                // head table 2
                $column   = 7; // A,B,C,D,E,F,G dst
                $column_e = 1;
                $rowCount = 9;
                foreach ($tm['head_table2'] as $field) {
                    foreach($field as $field2 => $field22){
                            if($field2 == 'type_adj_in' || $field2 == 'type_adj_out'){
                                if(count($field22) > 0){
                                        
                                    foreach($field22 as $field3 ){
                                 
                                        if(count($field3['child']) > 0){
                                            foreach ($field3['child'] as $field4){
                                                
                                                if(count($field4['child']) > 0){
                                                        foreach($field4['child'] as $field5){
                                                            $sheet->setCellValueByColumnAndRow($column, $rowCount, $field5); 
                                                            $column++;
                                                            $column_e++;
                                                        }
                                                }
                                            }
                                        }
                                    }
                                }
                            }else{

                                for (  $i = 0, $l = count($field22); $i < $l; $i++ ) {
                                    foreach($field22[$i] as $field3 => $field33){
                                        $sheet->setCellValueByColumnAndRow($column, $rowCount, $field33); 
                                        $column++;
                                        $column_e++;
                                    }
                                }
                            }
                    }
                }

            }

            $column_excel  = $this->cek_column_excel($column);
            $rowCountMerge = $rowCountMerge + 1;
            $sheet->getStyle("A".$rowCountFirst.":".$column_excel."".$rowCountMerge)->getFont()->setBold(true);

            //body/
            $no    = 1;
            $rowCount = $rowCount + 1;
            
            foreach ($tm['record'] as $row) {

                $in_total_lot         = 0;
                $in_total_qty1        = 0;
                $in_total_qty2        = 0;
                $in_total_qty_opname  = 0;
                $out_total_lot         = 0;
                $out_total_qty1        = 0;
                $out_total_qty2        = 0;
                $out_total_qty_opname  = 0;

                $sheet->SetCellValue('A'.$rowCount, ($no++));
                $column_awal = 2;
                // $column = 6;

                $column_excel  = $this->cek_column_excel($column_awal);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_category']);
                $column_excel  = $this->cek_column_excel($column_awal+1);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['kode_produk']);
                $column_excel  = $this->cek_column_excel($column_awal+2);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_produk']);
                $column_excel  = $this->cek_column_excel($column_awal+3);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['product_parent']);
                $column_excel  = $this->cek_column_excel($column_awal+4);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['lot']);
                $column_excel  = $this->cek_column_excel($column_awal+5);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['nama_jenis_kain']);
                $column_awal = 8;

                // SALDO AWAL
                $column_excel  = $this->cek_column_excel($column_awal);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_lot']);
                $column_excel  = $this->cek_column_excel($column_awal+1);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty1']);
                $column_excel  = $this->cek_column_excel($column_awal+2);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty1_uom']);
                $column_excel  = $this->cek_column_excel($column_awal+3);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty2']);
                $column_excel  = $this->cek_column_excel($column_awal+4);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty2_uom']);
                $column_excel  = $this->cek_column_excel($column_awal+5);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty_opname']);
                $column_excel  = $this->cek_column_excel($column_awal+7);
                $sheet->SetCellValue($column_excel.''.$rowCount, $row['s_awal_qty_opname_uom']);

                // info uom
                 if($row['s_awal_qty1_uom'] != ''){// jik ada
                    $qty1_uom        = $row['s_awal_qty1_uom'];
                }else{
                    $qty1_uom        = '';
                }
                if($row['s_awal_qty2_uom'] != ''){
                    $qty2_uom        = $row['s_awal_qty2_uom'];
                }else{
                    $qty2_uom        = '';
                }
                if($row['s_awal_qty_opname_uom'] != ''){
                    $qty_opname_uom  = $row['s_awal_qty_opname_uom'];
                }else{
                    $qty_opname_uom  = '';
                }

                $column = 15;
                // IN
                $no_in  = 1;
                $n      = $tm['count_in'];
                for ( $d = 0; $d < $n; $d++) {
                    $in_lot         = $row['in'.$no_in.'_lot'];
                    $in_qty1        = $row['in'.$no_in.'_qty1'];
                    $in_qty1_uom    = $row['in'.$no_in.'_qty1_uom'];
                    $in_qty2        = $row['in'.$no_in.'_qty2'];
                    $in_qty2_uom    = $row['in'.$no_in.'_qty2_uom'];
                    $in_opname      = $row['in'.$no_in.'_qty_opname'];
                    $in_opname_uom  = $row['in'.$no_in.'_qty_opname_uom'];
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $tm['count_in']);
                    $column_excel1  = $this->cek_column_excel($column+1);
                    $sheet->SetCellValue($column_excel1.''.$rowCount, $in_qty1);
                    $column_excel2  = $this->cek_column_excel($column+2);
                    $sheet->SetCellValue($column_excel2.''.$rowCount, $in_qty1_uom);
                    $column_excel3  = $this->cek_column_excel($column+3);
                    $sheet->SetCellValue($column_excel3.''.$rowCount, $in_qty2);
                    $column_excel4  = $this->cek_column_excel($column+4);
                    $sheet->SetCellValue($column_excel4.''.$rowCount, $in_qty2_uom);
                    $column_excel5  = $this->cek_column_excel($column+5);
                    $sheet->SetCellValue($column_excel5.''.$rowCount, $in_opname);
                    $column_excel6  = $this->cek_column_excel($column+6);
                    $sheet->SetCellValue($column_excel6.''.$rowCount, $in_opname_uom);
                    
                    $in_total_lot         = $in_total_lot+($in_lot);
                    $in_total_qty1        = $in_total_qty1+($in_qty1);
                    $in_total_qty2        = $in_total_qty2+($in_qty2);
                    $in_total_qty_opname  = $in_total_qty_opname+($in_opname);
                    
                    // info uom
                    if($in_qty1_uom != ''){
                        $qty1_uom        = $in_qty1_uom;
                    }
                    if($in_qty2_uom != ''){
                        $qty2_uom        = $in_qty2_uom;
                    }
                    if($in_opname_uom != ''){
                        $qty_opname_uom  = $in_opname_uom;
                    }
                    $column = $column + 7;

                    $no_in++;
                }

                // ADJ IN
                $column_excel_adj  = $this->cek_column_excel($column);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_lot']);
                $column_excel_adj  = $this->cek_column_excel($column+1);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty1']);
                $column_excel_adj  = $this->cek_column_excel($column+2);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty1_uom']);
                $column_excel_adj  = $this->cek_column_excel($column+3);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty2']);
                $column_excel_adj  = $this->cek_column_excel($column+4);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty2_uom']);
                $column_excel_adj  = $this->cek_column_excel($column+5);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty_opname']);
                $column_excel_adj  = $this->cek_column_excel($column+6);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_in_qty_opname_uom']);
                $column = $column + 7;

                $in_total_lot         = $in_total_lot+($row['adj_in_lot']);
                $in_total_qty1        = $in_total_qty1+($row['adj_in_qty1']);
                $in_total_qty2        = $in_total_qty2+($row['adj_in_qty2']);
                $in_total_qty_opname  = $in_total_qty_opname+($row['adj_in_qty_opname']);


                // type_ajd in
                $no_type_in = 1;
                $n      = $tm['count_type_in'];
                for ( $d = 0; $d < $n; $d++) {
                    $type_in_lot         = $row[''.$no_type_in.'_in_lot'];
                    $type_in_qty1        = $row[''.$no_type_in.'_in_qty1'];
                    $type_in_qty1_uom    = $row[''.$no_type_in.'_in_qty1_uom'];
                    $type_in_qty2        = $row[''.$no_type_in.'_in_qty2'];
                    $type_in_qty2_uom    = $row[''.$no_type_in.'_in_qty2_uom'];
                    $type_in_opname      = $row[''.$no_type_in.'_in_qty_opname'];
                    $type_in_opname_uom  = $row[''.$no_type_in.'_in_qty_opname_uom'];
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $type_in_lot);
                    $column_excel1  = $this->cek_column_excel($column+1);
                    $sheet->SetCellValue($column_excel1.''.$rowCount, $type_in_qty1);
                    $column_excel2  = $this->cek_column_excel($column+2);
                    $sheet->SetCellValue($column_excel2.''.$rowCount, $type_in_qty1_uom);
                    $column_excel3  = $this->cek_column_excel($column+3);
                    $sheet->SetCellValue($column_excel3.''.$rowCount, $type_in_qty2);
                    $column_excel4  = $this->cek_column_excel($column+4);
                    $sheet->SetCellValue($column_excel4.''.$rowCount, $type_in_qty2_uom);
                    $column_excel5  = $this->cek_column_excel($column+5);
                    $sheet->SetCellValue($column_excel5.''.$rowCount, $type_in_opname);
                    $column_excel6  = $this->cek_column_excel($column+6);
                    $sheet->SetCellValue($column_excel6.''.$rowCount, $type_in_opname_uom);

                    $column = $column + 7;
                    $no_type_in++;
                }

                // info uom
                if($row['adj_in_qty1_uom'] != ''){
                    $qty1_uom        = $row['adj_in_qty1_uom'];
                    $cat_qty1_uom_adj_in   =  $row['adj_in_qty1_uom'];
                }
                if($row['adj_in_qty2_uom'] != ''){
                    $qty2_uom        = $row['adj_in_qty2_uom'];
                    $cat_qty2_uom_adj_in   =  $row['adj_in_qty2_uom'];
                }
                if($row['adj_in_qty_opname_uom'] != ''){
                    $qty_opname_uom  = $row['adj_in_qty_opname_uom'];
                    $cat_opname_uom_adj_in   = $row['adj_in_qty_opname_uom'];
                }


                // Total IN
                $column_excel_tot_in  = $this->cek_column_excel($column);
                $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_lot);
                $column_excel_tot_in  = $this->cek_column_excel($column+1);
                $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_qty1);
                $column_excel_tot_in  = $this->cek_column_excel($column+2);
                $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $qty1_uom);
                $column_excel_tot_in  = $this->cek_column_excel($column+3);
                $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_qty2);
                $column_excel_tot_in  = $this->cek_column_excel($column+4);
                $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $qty2_uom);
                $column_excel_tot_in  = $this->cek_column_excel($column+5);
                $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $in_total_qty_opname);
                $column_excel_tot_in  = $this->cek_column_excel($column+6);
                $sheet->SetCellValue($column_excel_tot_in.''.$rowCount, $qty_opname_uom);
                $column = $column + 7;


                // OUT
                $no_out = 1;
                $n      = $tm['count_out'];
                for ( $d = 0; $d < $n; $d++) {
                    $out_lot         = $row['out'.$no_out.'_lot'];
                    $out_qty1        = $row['out'.$no_out.'_qty1'];
                    $out_qty1_uom    = $row['out'.$no_out.'_qty1_uom'];
                    $out_qty2        = $row['out'.$no_out.'_qty2'];
                    $out_qty2_uom    = $row['out'.$no_out.'_qty2_uom'];
                    $out_opname      = $row['out'.$no_out.'_qty_opname'];
                    $out_opname_uom  = $row['out'.$no_out.'_qty_opname_uom'];
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $out_lot);
                    $column_excel1  = $this->cek_column_excel($column+1);
                    $sheet->SetCellValue($column_excel1.''.$rowCount, $out_qty1);
                    $column_excel2  = $this->cek_column_excel($column+2);
                    $sheet->SetCellValue($column_excel2.''.$rowCount, $out_qty1_uom);
                    $column_excel3  = $this->cek_column_excel($column+3);
                    $sheet->SetCellValue($column_excel3.''.$rowCount, $out_qty2);
                    $column_excel4  = $this->cek_column_excel($column+4);
                    $sheet->SetCellValue($column_excel4.''.$rowCount, $out_qty2_uom);
                    $column_excel5  = $this->cek_column_excel($column+5);
                    $sheet->SetCellValue($column_excel5.''.$rowCount, $out_opname);
                    $column_excel6  = $this->cek_column_excel($column+6);
                    $sheet->SetCellValue($column_excel6.''.$rowCount, $out_opname_uom);

                    $out_total_lot         = $out_total_lot+($out_lot);
                    $out_total_qty1        = $out_total_qty1+($out_qty1);
                    $out_total_qty2        = $out_total_qty2+($out_qty2);
                    $out_total_qty_opname  = $out_total_qty_opname+($out_opname);

                    // info uom
                    if($out_qty1_uom != ''){
                        $qty1_uom        = $out_qty1_uom;
                    }
                    if($out_qty2_uom != ''){
                        $qty2_uom        = $out_qty2_uom;
                    }
                    if($out_opname_uom != ''){
                        $qty_opname_uom  = $out_opname_uom;
                    }
                    
                    $column = $column + 7;

                    $no_out++;
                }
                

                // ADJ OUT
                $column_excel_adj  = $this->cek_column_excel($column);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_lot']);
                $column_excel_adj  = $this->cek_column_excel($column+1);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty1']);
                $column_excel_adj  = $this->cek_column_excel($column+2);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty1_uom']);
                $column_excel_adj  = $this->cek_column_excel($column+3);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty2']);
                $column_excel_adj  = $this->cek_column_excel($column+4);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty2_uom']);
                $column_excel_adj  = $this->cek_column_excel($column+5);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty_opname']);
                $column_excel_adj  = $this->cek_column_excel($column+6);
                $sheet->SetCellValue($column_excel_adj.''.$rowCount, $row['adj_out_qty_opname_uom']);

                $out_total_lot         = $out_total_lot+($row['adj_out_lot']);
                $out_total_qty1        = $out_total_qty1+($row['adj_out_qty1']);
                $out_total_qty2        = $out_total_qty2+($row['adj_out_qty2']);
                $out_total_qty_opname  = $out_total_qty_opname+($row['adj_out_qty_opname']);

                // info uom
                if($row['adj_out_qty1_uom'] != ''){
                    $qty1_uom        = $row['adj_out_qty1_uom'];
                    $cat_qty1_uom_adj_out   =  $row['adj_out_qty1_uom'];
                }
                if($row['adj_out_qty2_uom'] != ''){
                    $qty2_uom        = $row['adj_out_qty2_uom'];
                    $cat_qty2_uom_adj_out   =  $row['adj_out_qty2_uom'];
                }
                if($row['adj_out_qty_opname_uom'] != ''){
                    $qty_opname_uom  = $row['adj_out_qty_opname_uom'];
                    $cat_opname_uom_adj_out   =  $row['adj_out_qty_opname_uom'];
                }
                $column = $column + 7;

                // type_ajd out
                $no_type_out = 1;
                $n      = $tm['count_type_out'];
                for ( $d = 0; $d < $n; $d++) {
                    $type_out_lot         = $row[''.$no_type_out.'_out_lot'];
                    $type_out_qty1        = $row[''.$no_type_out.'_out_qty1'];
                    $type_out_qty1_uom    = $row[''.$no_type_out.'_out_qty1_uom'];
                    $type_out_qty2        = $row[''.$no_type_out.'_out_qty2'];
                    $type_out_qty2_uom    = $row[''.$no_type_out.'_out_qty2_uom'];
                    $type_out_opname      = $row[''.$no_type_out.'_out_qty_opname'];
                    $type_out_opname_uom  = $row[''.$no_type_out.'_out_qty_opname_uom'];
                    $column_excel  = $this->cek_column_excel($column);
                    $sheet->SetCellValue($column_excel.''.$rowCount, $type_out_lot);
                    $column_excel1  = $this->cek_column_excel($column+1);
                    $sheet->SetCellValue($column_excel1.''.$rowCount, $type_out_qty1);
                    $column_excel2  = $this->cek_column_excel($column+2);
                    $sheet->SetCellValue($column_excel2.''.$rowCount, $type_out_qty1_uom);
                    $column_excel3  = $this->cek_column_excel($column+3);
                    $sheet->SetCellValue($column_excel3.''.$rowCount, $type_out_qty2);
                    $column_excel4  = $this->cek_column_excel($column+4);
                    $sheet->SetCellValue($column_excel4.''.$rowCount, $type_out_qty2_uom);
                    $column_excel5  = $this->cek_column_excel($column+5);
                    $sheet->SetCellValue($column_excel5.''.$rowCount, $type_out_opname);
                    $column_excel6  = $this->cek_column_excel($column+6);
                    $sheet->SetCellValue($column_excel6.''.$rowCount, $type_out_opname_uom);

                    $column = $column + 7;
                    $no_type_out++;
                }

                // Total OUT
                $column_excel_tot_out  = $this->cek_column_excel($column);
                $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_lot);
                $column_excel_tot_out  = $this->cek_column_excel($column+1);
                $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_qty1);
                $column_excel_tot_out  = $this->cek_column_excel($column+2);
                $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $qty1_uom);
                $column_excel_tot_out  = $this->cek_column_excel($column+3);
                $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_qty2);
                $column_excel_tot_out  = $this->cek_column_excel($column+4);
                $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $qty2_uom);
                $column_excel_tot_out  = $this->cek_column_excel($column+5);
                $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $out_total_qty_opname);
                $column_excel_tot_out  = $this->cek_column_excel($column+6);
                $sheet->SetCellValue($column_excel_tot_out.''.$rowCount, $qty_opname_uom);
                $column = $column + 7;

                // Saldo Akhir
                $column_excel_akhir  = $this->cek_column_excel($column);
                $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_lot']);
                $column_excel_akhir  = $this->cek_column_excel($column+1);
                $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty1']);
                $column_excel_akhir  = $this->cek_column_excel($column+2);
                $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty1_uom']);
                $column_excel_akhir  = $this->cek_column_excel($column+3);
                $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty2']);
                $column_excel_akhir  = $this->cek_column_excel($column+4);
                $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty2_uom']);
                $column_excel_akhir  = $this->cek_column_excel($column+5);
                $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty_opname']);
                $column_excel_akhir  = $this->cek_column_excel($column+6);
                $sheet->SetCellValue($column_excel_akhir.''.$rowCount, $row['s_akhir_qty_opname_uom']);

                if($row['s_akhir_qty1_uom'] != ''){
                    $cat_qty1_uom_s_akhir   = $row['s_akhir_qty1_uom'];
                }
                if($row['s_akhir_qty2_uom'] != ''){
                    $cat_qty2_uom_s_akhir   = $row['s_akhir_qty2_uom'];
                }
                if($row['s_akhir_qty_opname_uom'] != ''){
                    $cat_opname_uom_s_akhir   = $row['s_akhir_qty_opname_uom'];
                }

                $rowCount++;


            }

            $name_file ='Mutasi Detail Datar '.$dept['nama'].' '.bln_indo(date('d-m-Y',strtotime($tanggal))).'.xlsx';

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
            $object->save('php://output');
    
            $xlsData = ob_get_contents();
            ob_end_clean();
    
            $response =  array(
                'status'    => 'ok',
                'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
                'filename'  => $name_file
            );
        
            die(json_encode($response));

        }else{
            $get_dept  = $this->_module->get_nama_dept_by_kode($departemen)->row_array();

            $response =  array(
                'status'    => 'failed',
                'message'   => "Departemen ".$get_dept['nama']." belum terdapat Laporan Mutasi dengan View Detail Datar",
            );
        
            die(json_encode($response));
        }
    }

    function create_row_total($sheet,$rowCount,$nama_category,$cat_total_lot_s_awal,$cat_total_qty_s_awal,$cat_qty1_uom_s_awal,$cat_total_qty2_s_awal,$cat_qty2_uom_s_awal,$cat_total_qty_opname_s_awal,$cat_opname_uom_s_awal,$count_in,$arr_in2,$cat_total_lot_adj_in,$cat_total_qty_adj_in,$cat_qty1_uom_adj_in,$cat_total_qty2_adj_in,$cat_qty2_uom_adj_in,$cat_total_qty_opname_adj_in,$cat_opname_uom_adj_in,$arr_tot_in,$count_out,$arr_out2, $cat_total_lot_adj_out, $cat_total_qty_adj_out, $cat_qty1_uom_adj_out, $cat_total_qty2_adj_out, $cat_qty2_uom_adj_out, $cat_total_qty_opname_adj_out, $cat_opname_uom_adj_out,$arr_tot_out,$cat_total_lot_s_akhir,$cat_total_qty_s_akhir,$cat_qty1_uom_s_akhir,$cat_total_qty2_s_akhir,$cat_qty2_uom_s_akhir,$cat_total_qty_opname_s_akhir,$cat_opname_uom_s_akhir)
    {
        $cat_total_lot_in    = 0;
        $cat_total_qty_in    = 0;
        $cat_total_qty2_in   = 0;
        $cat_total_qty_opname_in  =  0;

        $cat_total_lot_out    = 0;
        $cat_total_qty_out    = 0;
        $cat_total_qty2_out   = 0;
        $cat_total_qty_opname_out  =  0;

        $sheet->SetCellValue('B'.$rowCount, "Total Kategori ".$nama_category);
        $sheet->mergeCells('B'.$rowCount.':E'.$rowCount);
        $sheet->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $sheet->SetCellValue('F'.$rowCount, $cat_total_lot_s_awal);
        $sheet->SetCellValue('G'.$rowCount, $cat_total_qty_s_awal);
        $sheet->SetCellValue('H'.$rowCount, $cat_qty1_uom_s_awal);
        $sheet->SetCellValue('I'.$rowCount, $cat_total_qty2_s_awal);
        $sheet->SetCellValue('J'.$rowCount, $cat_qty2_uom_s_awal);
        $sheet->SetCellValue('K'.$rowCount, $cat_total_qty_opname_s_awal);
        $sheet->SetCellValue('L'.$rowCount, $cat_opname_uom_s_awal);
              
        $num_in = 0;
        $column_total = 13;
        for ($cin =  $count_in; $num_in < $cin; $num_in++ ) {
            $tot_lot_in  = 0;
            $tot_qty1_in = 0;
            $tot_qty2_in = 0;
            $tot_qty_opname_in = 0;
            $uom1_in = '';
            $uom2_in = '';
            $uom_opname_in = '';
            for (  $xx = 0, $l = count($arr_in2); $xx < $l; $xx++ ) {
                    $tot_lot_in  = $tot_lot_in + ($arr_in2[$xx][$num_in]['in_lot']);
                    $tot_qty1_in = $tot_qty1_in + ($arr_in2[$xx][$num_in]['in_qty1']);
                    $tot_qty2_in = $tot_qty2_in + ($arr_in2[$xx][$num_in]['in_qty2']);
                    $tot_qty_opname_in = $tot_qty_opname_in + ($arr_in2[$xx][$num_in]['in_opname']);

                    if($arr_in2[$xx][$num_in]['in_qty1_uom'] != ''){
                        $uom1_in = $arr_in2[$xx][$num_in]['in_qty1_uom'];
                    }
                    if($arr_in2[$xx][$num_in]['in_qty2_uom'] != ''){
                        $uom2_in = $arr_in2[$xx][$num_in]['in_qty2_uom'];
                    }
                    if($arr_in2[$xx][$num_in]['in_opname_uom'] != ''){
                        $uom_opname_in = $arr_in2[$xx][$num_in]['in_opname_uom'];
                    }
            }
            $column_excel_total  = $this->cek_column_excel($column_total);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $tot_lot_in);

            $column_excel_total  = $this->cek_column_excel($column_total+1);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $tot_qty1_in);
            $column_excel_total  = $this->cek_column_excel($column_total+2);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $uom1_in);
            
            $column_excel_total  = $this->cek_column_excel($column_total+3);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $tot_qty2_in);
            $column_excel_total  = $this->cek_column_excel($column_total+4);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $uom2_in);

            $column_excel_total  = $this->cek_column_excel($column_total+5);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $tot_qty_opname_in);
            $column_excel_total  = $this->cek_column_excel($column_total+6);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $uom_opname_in);
           
            $column_total = $column_total + 7;
        }

       
        // ADJ IN
        $column_excel_total  = $this->cek_column_excel($column_total);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_lot_adj_in);

        $column_excel_total  = $this->cek_column_excel($column_total+1);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty_adj_in);
        $column_excel_total  = $this->cek_column_excel($column_total+2);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_qty1_uom_adj_in);
        
        $column_excel_total  = $this->cek_column_excel($column_total+3);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty2_adj_in);
        $column_excel_total  = $this->cek_column_excel($column_total+4);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_qty2_uom_adj_in);

        $column_excel_total  = $this->cek_column_excel($column_total+5);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty_opname_adj_in);
        $column_excel_total  = $this->cek_column_excel($column_total+6);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_opname_uom_adj_in);

        $column_total = $column_total + 7;

       
        $qty1_uom_tot_in   = '';
        $qty2_uom_tot_in   = '';
        $opname_uom_tot_in = '';
        // total 
        foreach($arr_tot_in as $in){
            $cat_total_lot_in   = $cat_total_lot_in + $in['tot_lot'];
            $cat_total_qty_in   = $cat_total_qty_in +  $in['tot_qty1'];
            $cat_total_qty2_in  = $cat_total_qty2_in +  $in['tot_qty2'];
            $cat_total_qty_opname_in  = $cat_total_qty_opname_in +  $in['tot_qty_opname'];

            if($in['qty1_uom'] != ''){
                $qty1_uom_tot_in = $in['qty1_uom'];
            }
            if($in['qty2_uom'] != ''){
               $qty2_uom_tot_in = $in['qty2_uom'];
            }
            if($in['qty_opname_uom'] != ''){
               $opname_uom_tot_in = $in['qty_opname_uom'];
            }
        }
      
        $column_excel_total  = $this->cek_column_excel($column_total);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_lot_in);

        $column_excel_total  = $this->cek_column_excel($column_total+1);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty_in);
        $column_excel_total  = $this->cek_column_excel($column_total+2);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $qty1_uom_tot_in);
         
        $column_excel_total  = $this->cek_column_excel($column_total+3);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty2_in);
        $column_excel_total  = $this->cek_column_excel($column_total+4);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $qty2_uom_tot_in);

        $column_excel_total  = $this->cek_column_excel($column_total+5);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty_opname_in);
        $column_excel_total  = $this->cek_column_excel($column_total+6);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $opname_uom_tot_in);

        $column_total = $column_total + 7;
       
        $num_out = 0;
        for ($cot =  $count_out; $num_out < $cot; $num_out++ ) {
            $tot_lot_out  = 0;
            $tot_qty1_out  = 0;
            $tot_qty2_out  = 0;
            $tot_qty_opname_out  = 0;
            $uom1_out  = '';
            $uom2_out  = '';
            $uom_opname_out  = '';
            for (  $xx = 0, $l = count($arr_out2); $xx < $l; $xx++ ) {
                    $tot_lot_out   = $tot_lot_out  + ($arr_out2[$xx][$num_out]['out_lot']);
                    $tot_qty1_out  = $tot_qty1_out  + ($arr_out2[$xx][$num_out]['out_qty1']);
                    $tot_qty2_out  = $tot_qty2_out  + ($arr_out2[$xx][$num_out]['out_qty2']);
                    $tot_qty_opname_out  = $tot_qty_opname_out  + ($arr_out2[$xx][$num_out ]['out_opname']);

                    if($arr_out2[$xx][$num_out]['out_qty1_uom'] != ''){
                        $uom1_out  = $arr_out2[$xx][$num_out]['out_qty1_uom'];
                    }
                    if($arr_out2[$xx][$num_out]['out_qty2_uom'] != ''){
                        $uom2_out  = $arr_out2[$xx][$num_out]['out_qty2_uom'];
                    }
                    if($arr_out2[$xx][$num_out]['out_opname_uom'] != ''){
                        $uom_opname_out  = $arr_out2[$xx][$num_out]['out_opname_uom'];
                    }
            }
            
            $column_excel_total  = $this->cek_column_excel($column_total);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $tot_lot_out);

            $column_excel_total  = $this->cek_column_excel($column_total+1);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $tot_qty1_out);
            $column_excel_total  = $this->cek_column_excel($column_total+2);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $uom1_out);
            
            $column_excel_total  = $this->cek_column_excel($column_total+3);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $tot_qty2_out);
            $column_excel_total  = $this->cek_column_excel($column_total+4);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $uom2_out);

            $column_excel_total  = $this->cek_column_excel($column_total+5);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $tot_qty_opname_out);
            $column_excel_total  = $this->cek_column_excel($column_total+6);
            $sheet->SetCellValue($column_excel_total.''.$rowCount, $uom_opname_out);
           
            $column_total = $column_total + 7;
        }
      
        // ADJ OUT
        $column_excel_total  = $this->cek_column_excel($column_total);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_lot_adj_out);

        $column_excel_total  = $this->cek_column_excel($column_total+1);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty_adj_out);
        $column_excel_total  = $this->cek_column_excel($column_total+2);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_qty1_uom_adj_out);
          
        $column_excel_total  = $this->cek_column_excel($column_total+3);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty2_adj_out);
        $column_excel_total  = $this->cek_column_excel($column_total+4);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_qty2_uom_adj_out);

        $column_excel_total  = $this->cek_column_excel($column_total+5);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty_opname_adj_out);
        $column_excel_total  = $this->cek_column_excel($column_total+6);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_opname_uom_adj_out);

        $column_total = $column_total + 7;

        $qty1_uom_tot_out   = '';
        $qty2_uom_tot_out   = '';
        $opname_uom_tot_out = '';
        // total 
        foreach($arr_tot_out as $out){
            $cat_total_lot_out   = $cat_total_lot_out + $out['tot_lot'];
            $cat_total_qty_out   = $cat_total_qty_out +  $out['tot_qty1'];
            $cat_total_qty2_out  = $cat_total_qty2_out +  $out['tot_qty2'];
            $cat_total_qty_opname_out  = $cat_total_qty_opname_out +  $out['tot_qty_opname'];

            if($out['qty1_uom'] != ''){
                $qty1_uom_tot_out = $out['qty1_uom'];
            }
            if($out['qty2_uom'] != ''){
               $qty2_uom_tot_out = $out['qty2_uom'];
            }
            if($out['qty_opname_uom'] != ''){
               $opname_uom_tot_out = $out['qty_opname_uom'];
            }
        }

       
        $column_excel_total  = $this->cek_column_excel($column_total);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_lot_out);

        $column_excel_total  = $this->cek_column_excel($column_total+1);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty_out);
        $column_excel_total  = $this->cek_column_excel($column_total+2);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $qty1_uom_tot_out);
         
        $column_excel_total  = $this->cek_column_excel($column_total+3);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty2_out);
        $column_excel_total  = $this->cek_column_excel($column_total+4);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $qty2_uom_tot_out);

        $column_excel_total  = $this->cek_column_excel($column_total+5);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty_opname_out);
        $column_excel_total  = $this->cek_column_excel($column_total+6);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $opname_uom_tot_out);

        $column_total = $column_total + 7;
       
        $column_excel_total  = $this->cek_column_excel($column_total);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_lot_s_akhir);
        $column_excel_total  = $this->cek_column_excel($column_total+1);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty_s_akhir);
        $column_excel_total  = $this->cek_column_excel($column_total+2);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_qty1_uom_s_akhir);
        $column_excel_total  = $this->cek_column_excel($column_total+3);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty2_s_akhir);
        $column_excel_total  = $this->cek_column_excel($column_total+4);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_qty2_uom_s_akhir);
        $column_excel_total  = $this->cek_column_excel($column_total+5);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_total_qty_opname_s_akhir);
        $column_excel_total  = $this->cek_column_excel($column_total+6);
        $sheet->SetCellValue($column_excel_total.''.$rowCount, $cat_opname_uom_s_akhir);

        $sheet->getStyle("B".$rowCount.":".$column_excel_total."".$rowCount)->getFont()->setBold(true);

        return;
    }

    function print_bap_mutasi()
    {
  		$this->load->library('Pdf');//load library pdf
        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();
        $pdf->SetMargins(7,7,5);
         
        $tanggal    = $this->input->get('tanggal');
        $departemen = $this->input->get('departemen');

        $tahun      = date('Y', strtotime($tanggal)); // example 2022
        $bulan      = date('n', strtotime($tanggal)); // example 8
        $dept       = $this->_module->get_nama_dept_by_kode($departemen)->row_array();

        //cek departemen yg ada mutasi
        $result = $this->cek_dept_mutasi_bap($departemen);
        if($result == true){

            if($dept['type_dept'] == 'manufaktur'){

                $table     = 'acc_mutasi_'.strtolower($departemen).'_rm';
                $table2    = 'acc_mutasi_'.strtolower($departemen).'_fg';
                
            }else{// gudang
                $table     = 'acc_mutasi_'.strtolower($departemen);
            }
            
            $pdf->SetFont('Arial','',8,'C');
            $pdf->setXY(143, 5);
            $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
            $pdf->Multicell(60,4, 'Tgl.Cetak : '.$tgl_now, 0,'R');

            $pdf->setTitle('BAP Mutasi '.$dept['nama']);

            // setting jenis font yang akan digunakan
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(195,5,'BERITA ACARA PEMERIKSAAN',0,1,'C');
            $pdf->Cell(195,5,'MUTASI ADJUSTMENT',0,1,'C');
            

            $pdf->SetFont('Arial','B',9,'C');
            // Caption kiri
            $pdf->setXY(7,22);
            $pdf->Multicell(25, 4, 'Periode ', 0, 'L');
            $pdf->setXY(7,26);
            $pdf->Multicell(25, 4, 'Departemen ', 0, 'L');

            $pdf->setXY(29, 22);
            $pdf->Multicell(25, 4, ':', 0, 'L');
            $pdf->setXY(29, 26);
            $pdf->Multicell(25, 4, ':', 0, 'L');
        
            // isi kiri
            $pdf->SetFont('Arial','',9,'C');
            $pdf->setXY(31,22);
            $pdf->Multicell(50, 4,bln_indo(date('d-m-Y',strtotime($tanggal))), 0, 'L');
            $pdf->setXY(31,26);
            $pdf->Multicell(63, 4, $dept['nama'], 0, 'L');
            
            $pdf->SetFont('Arial','B',9,'C');

            if($dept['type_dept'] == 'manufaktur'){
                $ket_rm  = "Total Adjustment Bahan Baku" ;
                $set     = 2;
            }else{
                $ket_rm  = "Total Adjustment" ;
                $set     = 1;
            }

            $y         = 35;
            $cellWidth = 0;
            
            $yPos_out  = $pdf->GetY()+5;       
            $yPos      = $pdf->GetY()+5;       
            $xPos      = $pdf->GetX();
            $line      = 0;
            $cellHeight= 0;
            $yPos_no_data = 0;
            
            for($a=0; $a<$set; $a++){

                $pdf->SetFont('Arial','B',9,'C');
                if($a == 1){
                    // table
                    $ket_rm  = "Total Adjustment Barang Jadi" ;
                    $table   = $table2;                    
                }
                
                $pdf->setXY(7,$yPos);
                $pdf->Multicell(50, 5, $ket_rm, 0, 'L');

                $pdf->setXY(7,$yPos+5);         
                $pdf->Multicell(30, 5, 'Adjustment IN', 0, 'L');
                
                // head
                $pdf->setXY(7,$yPos+10);
                $pdf->Cell(35, 5, 'Kategori ', 1, 0, 'C');
                $pdf->Cell(15, 5, 'Total Lot', 1, 0, 'C');
                $pdf->Cell(25, 5, 'Total Qty 1 ', 1, 0, 'C');
                $pdf->Cell(20, 5, 'Total Qty 2', 1, 0, 'C');

                //body
                $pdf->SetFont('Arial','',9,'C');
                $result1 = $this->m_mutasi->get_total_adjustment_in($table,$tahun,$bulan)->result();
                $restul1_empty = true;
                $pdf->setXY(7,$yPos+15);
                $line_tmp  = 1;
                foreach($result1 as $row ){
                    $restul1_empty = false;

                    $cellWidth =35; //lebar sel
                    $cellHeight=5; //tinggi sel satu baris normal
                    $kategori = $row->nama_category;

                    if($pdf->GetStringWidth($kategori) < $cellWidth){
                        // jika tidak
                        $line =1;
                    }else{

                        $textLength =strlen($kategori);	//total panjang teks
                        $errMargin  =5;		//margin kesalahan lebar sel, untuk jaga-jaga
                        $startChar  =0;		//posisi awal karakter untuk setiap baris
                        $maxChar    =0;			//karakter maksimum dalam satu baris, yang akan ditambahkan nanti
                        $textArray  =array();	//untuk menampung data untuk setiap baris
                        $tmpString  ="";		//untuk menampung teks untuk setiap baris (sementara)
                        
                        while($startChar < $textLength){ //perulangan sampai akhir teks
                            //perulangan sampai karakter maksimum tercapai
                            while( 
                            $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
                            ($startChar+$maxChar) < $textLength ) {
                            $maxChar++;
                            $tmpString=substr($kategori,$startChar,$maxChar);
                            }
                            //pindahkan ke baris berikutnya
                            $startChar=$startChar+$maxChar;
                            //kemudian tambahkan ke dalam array sehingga kita tahu berapa banyak baris yang dibutuhkan
                            array_push($textArray,$tmpString);
                            //reset variabel penampung
                            $maxChar  =0;
                            $tmpString='';
                            
                        }
                        //dapatkan jumlah baris
                        $line=count($textArray);                    
                    }
                    
                    $yPos=$pdf->GetY();
                    $xPos=$pdf->GetX();
                    $pdf->Multicell($cellWidth,$cellHeight,$kategori,1,1);

                    $pdf->SetXY($xPos + $cellWidth , $yPos);
                    $pdf->Multicell(15,($line * $cellHeight),$row->total_lot_in,1,'C');
                    $pdf->SetXY($xPos + 15 +  $cellWidth , $yPos);
                    $pdf->Multicell(25,($line * $cellHeight),number_format($row->total_qty1_in,2).' '.$row->adj_in_qty1_uom,1,'R');
                    $pdf->SetXY($xPos + 40 +$cellWidth , $yPos);
                    $pdf->Multicell(20,($line * $cellHeight),number_format($row->total_qty2_in,2).' '.$row->adj_in_qty2_uom,1,'R');
                    $line_tmp = $line_tmp + $line;
                }

                if($restul1_empty == true){
                    $pdf->Cell(95, 5, 'Tidak Ada Data', 1, 0, 'C');
                    $yPos_no_data = 5;
                }

                $pdf->SetFont('Arial','B',9,'C');
                $pdf->setXY(108,$yPos_out+5);
                $pdf->Multicell(30, 5, 'Adjustment OUT ', 0, 'L');

                // head
                $pdf->setXY(108,$yPos_out+10);
                $pdf->Cell(35, 5, 'Kategori ', 1, 0, 'C');
                $pdf->Cell(15, 5, 'Total Lot', 1, 0, 'C');
                $pdf->Cell(25, 5, 'Total Qty 1 ', 1, 0, 'C');
                $pdf->Cell(20, 5, 'Total Qty 2', 1, 0, 'C');
                //body
                $pdf->SetFont('Arial','',9,'C');
                $result2 = $this->m_mutasi->get_total_adjustment_out($table,$tahun,$bulan)->result();
                $yPos_out = $yPos_out + 15;
                $restul2_empty = true;
                $x_out    = 108;
                foreach($result2 as $row ){
                    $restul2_empty = false;

                    $cellWidth =35; //lebar sel
                    $cellHeight=5; //tinggi sel satu baris normal
                    $kategori = $row->nama_category;

                    if($pdf->GetStringWidth($kategori) < $cellWidth){
                        // jika tidak
                        $line =1;
                    }else{

                        $textLength =strlen($kategori);	//total panjang teks
                        $errMargin  =5;		//margin kesalahan lebar sel, untuk jaga-jaga
                        $startChar  =0;		//posisi awal karakter untuk setiap baris
                        $maxChar    =0;			//karakter maksimum dalam satu baris, yang akan ditambahkan nanti
                        $textArray  =array();	//untuk menampung data untuk setiap baris
                        $tmpString  ="";		//untuk menampung teks untuk setiap baris (sementara)
                        
                        while($startChar < $textLength){ //perulangan sampai akhir teks
                            //perulangan sampai karakter maksimum tercapai
                            while( 
                            $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) &&
                            ($startChar+$maxChar) < $textLength ) {
                            $maxChar++;
                            $tmpString=substr($kategori,$startChar,$maxChar);
                            }
                            //pindahkan ke baris berikutnya
                            $startChar=$startChar+$maxChar;
                            //kemudian tambahkan ke dalam array sehingga kita tahu berapa banyak baris yang dibutuhkan
                            array_push($textArray,$tmpString);
                            //reset variabel penampung
                            $maxChar  =0;
                            $tmpString='';
                            
                        }
                        //dapatkan jumlah baris
                        $line=count($textArray);                    
                    }
                    $pdf->setXY($x_out,$yPos_out);
                    $pdf->Multicell($cellWidth,$cellHeight,$kategori,1,1);
                    $pdf->SetXY($x_out + $cellWidth , $yPos_out);
                    $pdf->Multicell(15,($line * $cellHeight),$row->total_lot_out,1,'C');
                    $pdf->SetXY($x_out + 15 +  $cellWidth , $yPos_out);
                    $pdf->Multicell(25,($line * $cellHeight),number_format($row->total_qty1_out,2).' '.$row->adj_out_qty1_uom,1,'R');
                    $pdf->SetXY($x_out + 40 +  $cellWidth , $yPos_out);
                    $pdf->Multicell(20,($line * $cellHeight),number_format($row->total_qty2_out,2).' '.$row->adj_out_qty2_uom,1,'R');
                    
                    $yPos_out = $yPos_out + 5;

                }

                if($restul2_empty == true){
                    $pdf->setXY(108,$yPos_out);
                    $pdf->Cell(95, 5, 'Tidak Ada Data', 1, 0, 'C');
                }

                $yPos       = $pdf->GetY()+5+$yPos_no_data;
                $yPos_out   = $pdf->GetY()+5+$yPos_no_data;
                $xPos       = $pdf->GetX();
            }

            $pdf->SetFont('Arial','B',9,'C');

            //alasan
            $pdf->setXY(7,$yPos);
            $pdf->Multicell(25, 5, 'Alasan ', 0, 'L');

            $pdf->setXY(7,$yPos+5);
            $pdf->Multicell(195, 30, ' ', 1, 'L');

            $username  = addslashes($this->session->userdata('username')); 
            $nu        = $this->_module->get_nama_user($username)->row_array();
            $nama_user = $nu['nama'];

            // ttd
            $pdf->setXY(20, $yPos+40);
            $pdf->Multicell(23, 4, 'PPIC', 0, 'C');
            $pdf->setXY(20, $yPos+65);
            $pdf->Multicell(23, 4, '( '.$nama_user.' )', 0, 'C');

            $pdf->setXY(80,  $yPos+40);
            $pdf->Multicell(23, 4, $dept['nama'], 0, 'C');
            $pdf->setXY(80, $yPos+65);
            $pdf->Multicell(23, 4, '( ', 0, 'L');
            $pdf->setXY(80, $yPos+65);
            $pdf->Multicell(23, 4, ' )', 0, 'R');
        }else{
            $pdf->SetFont('Arial','B',10);
  		    $pdf->Cell(195,5,"  Departemen ".$dept['nama']." belum terdapat Laporan Mutasi",0,1,'L');
        }

  		$pdf->Output();

    }
}