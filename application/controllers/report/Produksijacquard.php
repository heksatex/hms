<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Produksijacquard extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('_module');
		$this->load->model('m_produksiJacquard');
	}

    public function index()
	{	
        $id_dept	     ='PRODJAC';
        $data['id_dept'] = $id_dept;

        $type_condition = $this->_module->get_first_type_conditon($id_dept);
        $data['mstFilter']      = $this->_module->get_list_mst_filter($id_dept);
        $data['type_condition'] = $type_condition;

		$this->load->view('report/v_produksi_jacquard', $data);
	}

	public function conditionFilter()
    {
        $kode_element = $_POST['element'];
        $id_dept      = $_POST['id_dept'];

        $type_condition = $this->_module->get_type_conditon($id_dept,$kode_element);
        $callback = array('type_condition'=>$type_condition);
        echo json_encode($callback);

    }


	public function loadData($record=0)
    {
    
 		$data_filter  = $this->input->post('data_filter'); 
        $data_filter_table= $this->input->post('data_filter_table'); //data filter dari table filer advanced

        $type_filter  = $this->input->post('type_filter');
        $id_dept      = $this->input->post('id_dept');
        $id_dept_filter = 'PRODJAC';
        $no           = 1;
        $dataRecord   = [];
        $where        = "";
        $whereAll     = "";
        $dataArr      = [];
        $where_table  = "";
        $where_df     = "";

        //data_filter_table
        if(!empty($data_filter_table)){

            $whereAll = "";  
            $caption  = "";
            $loop_for = 1;
            $tmp_nama_field = "";
            $tmp_caption    = "";
            $loop     = false;

            // data_filter_tabel dari table filter advanved
            foreach ($data_filter_table as $row) {
                $loop = true;
           
                $condition = 'OR';

                // operator ex and, like, =, =>, ect
                if($row['operator'] == 'LIKE'){
                    $isi = "LIKE '%".addslashes($row['isi'])."%' ";
                    $operator = 'LIKE';
                   // $whereAll .= "LIKE '%".$row['isi']."%' ".$condition;
                }else{
                    $isi = $row['operator']." '".addslashes($row['isi'])."' ";
                    $operator = $row['operator'];
                    //$whereAll .= $row['operator']." '".$row['isi']."' ".$condition;
                }

                
                $qry_where = $this->declaration_name_field($row['nama_field']);
                if(!empty($qry_where)){
                    $nama_field = $qry_where;
                    $tmp_nama_field .= $row['nama_field'].'^-|'.$operator.'^-|'.$row['isi'].'^-|,';
                }else{
                    break;
                }
                

                if($loop_for == 1 ){
                    $where .= $nama_field.' '.$isi.' ';
                }else{
                    $where .= $condition.' '.$nama_field.' '.$isi;
                }

                $nama_element = $this->_module->get_nama_element_by_kode($row['nama_field'],$id_dept_filter);  
                $tmp_caption  .= $nama_element.' '.$operator.' '.$row['isi'].' '.$condition.' ';  

                $loop_for++;

            }// end foreach data_filter_table
                
            if($loop == true){

                $tmp_caption = rtrim($tmp_caption, $condition.' ');
                $tmp_nama_field = rtrim($tmp_nama_field, '^-|,');
                //buat ke arr
                $dataArr[] = array('caption'     => $tmp_caption, // ex Nama Produk LIKE FOY
                                    'nama_field' => $tmp_nama_field, // ex nama_produk LIKE '%FOY%'
                                    'operator'   => 'kosong',
                                    'isi'        => 'kosong',
                                    'condition'  => 'OR');
            }

            //untuk menambahkan tanda ( )
            if($loop_for  > 1 AND $loop == true){
                $where  = ' ( '.$where.' ) ';
            }

            $where_table .= $where;
            $where       = '';

        }// end if data_filter_table


        // data_filter
        if(!empty($data_filter)){
            $loop_data_ex = 1;
            $loop     = 1;
            $type_table = false;
            $type_textfield = false;
            $before   = '';
            $before2  = '';
            $after    = '';           
            $loop_text= 1;
            foreach ($data_filter as $row) {
                # code... 

                /*
                if(!empty($data_filter_table) AND $loop == 1){
                    $after = ' AND ';
                    $where = $where.' '.$after;
                }
                */

                if($row['type'] == 'table'){ //jika bukan dari favorite

                    $type_table = true;

                    $data_ex = explode("^-|,", $row['nama_field']); // ex nama_produk^-|LIKE^-|foy^-|,kode_produk^-|LIKE^-|0206^-|,

                    if($type_textfield == true){
                        $before = ' AND ';
                        $where  .= $before;
                    }

                    if($type_textfield == false AND $loop > 1){
                        $before = ' AND ';
                        $where  .= $before;
                    }
                   
                    foreach($data_ex as $row1) {
                        $data    = explode("^-|",$row1);
                        //$nama_field = $data[0];
                        $qry_where = $this->declaration_name_field($data[0]);
                        if(!empty($qry_where)){
                            $nama_field = $qry_where;
                        }else{
                            break;
                        } 

                        $data_ke = 0;
                        foreach ($data as $data1) {
                            # code...
                            if($data_ke == 1){
                                $operator  = $data1;
                            }
                            if($data_ke == 2){
                                $isi = ($data1);
                            }

                            $data_ke++;
                        }

                        if($operator == 'LIKE'){
                            $isi_ = "LIKE '%".addslashes($isi)."%' ";
                            // $operator = 'LIKE';
                        }else{
                            $isi_ = $operator." '".addslashes($isi)."' ";
                            // $operator = $row['operator'];
                        }

                        $where .= $nama_field.' '.$isi_.' '.$row['condition'].' ';// condition = OR
                        $loop_data_ex++;
                    }

                    if(!empty($where)){
                        $where = rtrim($where, $row['condition'].' ');
                        if($loop_data_ex > 2){
                            $where = ' ( '.$where.' ) ';
                        }else{
                            $where = $where;
                        }
                    }

                    $type_textfield = false;

                  
                }


                if($row['type'] == 'textfield'){

                    
                    $type_textfield = true;

                    if($type_table == true){
                        $before = ' AND ';
                    }

                    if($type_table == false AND $loop > 1){
                        $before = ' AND ';
                    }
                    

                    $qry_where = $this->declaration_name_field($row['nama_field']);
                    if(!empty($qry_where)){
                        $nama_field = $qry_where;
                    }else{
                        break;
                    } 

                    if($row['operator'] == 'LIKE'){
                        $isi = "LIKE '%".addslashes($row['isi'])."%' ";
                        $operator = 'LIKE';
                    }else{
                        $isi = $row['operator']." '".addslashes($row['isi'])."' ";
                        $operator = $row['operator'];
                    }

              

                    if(empty($data_filter_table) AND $type_table == false AND $loop == 1 ){
                        $condition = 'AND ';
                        $where .= $nama_field.' '.$isi;
                        //$where .= $nama_field.' '.$isi.' '.$condition;
                    }else{
                        //$where .= $before.' '.$nama_field.' '.$isi.' '.$after;
                        $where .= $before.' '.$nama_field.' '.$isi;

                    }

                    $type_table = false;

                }


                $loop++;
            }// end foreach data_filter

            $where_df = $where;

            /*
            if(empty($data_filter_table) AND $type_table == false){
                $where = rtrim($where, $condition);
            }

            if($loop > 1 AND ($type_textfield == true OR $type_textfield == false)){
                $where = rtrim($where, $after);
                 $where = ltrim($where, $before);

            }
            */

        }// end if data_filter

        if(!empty($where_table) OR !empty($where_df)){
            if(!empty($where_table)){
                if(!empty($where_df)){
                    $where_table = $where_table.' AND ';
                }
            }

            $where = $where_table.' '.$where_df;

        }

        $sales_contract = '';
        $pd         	= '';
        $mkt            = '';
        $gl             = '';
        $mtr_gl         = '';
        $lbr_jadi       = '';
        $lbr_greige     = '';
        $pcs            = '';
        $gauge          = '';
        $stitch         = '';
        $rpm            = '';
        $courses        = '';
        $gb             = '';
        $run_in         = '';


        if(!empty($where)){
	        $where ="where mp.dept_id ='".$id_dept."' AND mp.status NOT IN ('cancel', 'done')  AND".$where;
        }else{
	        $where ="where mp.dept_id ='".$id_dept."' AND mp.status NOT IN ('cancel', 'done') ";
        }

        $items = $this->m_produksiJacquard->get_list_produksi_jacquard_by_kode($where,$id_dept);
 
        foreach ($items as $row) {

        	$mo   = $row->kode;
        	$tgl_mo =  tgl_indo2(date('d-m-y',strtotime($row->tanggal)));
        	$nama_mesin  = $row->nama_mesin;
        	$nama_produk = $row->nama_produk;
        	$start_time  = tgl_indo2(date('d-m-y H:i:s',strtotime($row->start_time)));
        	$finish_time = tgl_indo2(date('d-m-y H:i:s',strtotime($row->finish_time)));
        	$meter       = $row->meter;
        	$hph_qty1    = $row->hph_qty1;
			$hph_qty2    = $row->hph_qty2;
        	$gulung      = $row->gulung;
			$sisa_target = $row->sisa_target;
      		$status      = $row->status;

        	// explode origin
        	$ex = explode('|', $row->origin);
        	$i  = 0;
        	foreach($ex as $exs){

        		if($i== 0){ // SC
        			$sales_contract = trim($exs);
        			$mkt = $this->m_produksiJacquard->get_marketing_by_kode($sales_contract);
        		}else if($i == 1){ // PD
        			$pd = trim($exs);
        		}

	        	$i++;
        	}

        	// explode reff_note
/*
        	$ex2 = explode('|', $row->reff_note);
        	$a   = 0;
        	foreach ($ex2 as $exs2) {

                if($a == 5){ // Gl
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $gl  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 6){ // mtr/gl
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $mtr_gl  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 7 ){// l.greige
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $lbr_greige  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 8){ // l.jadi
                    $exp = explode('=', $exs2);
                     $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $lbr_jadi  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 9){ // pcs
                    $exp = explode('=', $exs2);
                     $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $pcs  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 10){ // gauge
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $gauge  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 11){ // stitch
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $stitch  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 12){ // courses
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $courses  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 13){ // rpm
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $rpm  = trim($exps);
                        }
                        $b++;
                    }
                }


        	 	$a++;
        	} 
*/

        	$getBoM = $this->m_produksiJacquard->get_list_bahan_baku_by_kode($row->kode);
        	$boM    = 1;
        	foreach ($getBoM as $val) {
        		# code...
	        	if($boM > 1){
	        		$mo          = '';
		        	$tgl_mo      = '';
		        	//$nama_mesin  = '';
		        	$nama_produk = '';
		        	$start_time  = '';
		        	$finish_time = '';
		        	$meter       = '';
		        	$hph_qty1    = '';
					$hph_qty2    = '';
		        	$gulung      = '';
					$sisa_target = '';
		      		$status      = '';
		      		$sales_contract = '';
			        $pd         	= '';
			        $mkt            = '';
			        $gl             = '';
			        $mtr_gl         = '';
			        $lbr_jadi       = '';
			        $lbr_greige     = '';
			        $pcs            = '';
			        $gauge          = '';
			        $stitch         = '';
			        $rpm            = '';
			        $courses        = '';
			        $gb             = '';
			        $run_in         = '';
	        	}

	        	// explode reff
	        	$exp = explode('|', $val->reff);
	        	$ke  = 0;
	        	foreach ($exp as $exps) {
	        		if($ke == 0){ // GB
	        			$gb = trim($exps);
	        		}
	        		if($ke == 1){ // run in
	        			$run_in = trim($exps);
	        		}
	        		$ke++;
	        	}

	        	// buat array dataRecord
	        	$dataRecord[] = array('kode'           => $mo, 
	        						  'tgl_mo' 		   => $tgl_mo, 
	        						  'mc' 			   => $nama_mesin,
	        						  'sc'             => $sales_contract,
	        						  'pd'			   => $pd,
	        						  'marketing'	   => $mkt,
	        						  'corak'          => $nama_produk,
	        						  'lbr_greige'     => $lbr_greige,
	        						  'lbr_jadi'       => $lbr_jadi,
	        						  'start_produksi' => $start_time,
	        						  'finish_produksi'=> $finish_time,
	        						  'meter'          => $meter,
									  /*
	        						  'gulung'         => $gl,
	        						  'mtr_gl'	       => $mtr_gl,
	        						  'pcs'            => $pcs,
	        						  'gauge'          => $gauge,
	        						  'stitch'         => $stitch,
	        						  'courses'        => $courses,
	        						  'rpm'            => $rpm,
	        						  'gb'             => $gb,
									  */
	        						  'rm'             => $val->nama_produk,
	        						  'target_qty'     => $val->target_qty,
	        						  'run_in'         => $run_in,
	        						  'qty1'	       => $hph_qty1,
	        						  'qty2'           => $hph_qty2,
	        						  'h_gulung'	   => $gulung,
	        						  'sisa'           => $sisa_target,
	        						  'status'         => $status

	        						 );
        		$boM++;
        	}

        	$sales_contract = '';
	        $pd 		 	= '';
	        $mkt            = '';
	        $gl             = '';
	        $mtr_gl         = '';
	        $lbr_jadi       = '';
	        $lbr_greige     = '';
	        $pcs            = '';
	        $gauge          = '';
	        $stitch         = '';
	        $rpm            = '';
        	$courses        = '';
        	$gb             = '';
        	$run_in         = '';

        }

        $allcount           = $this->m_produksiJacquard->get_record_count_jacquard($where);
        $total_record       = 'Total Data : '. number_format($allcount);

        $callback  = array('record' => $dataRecord, 'total_record'=>$total_record, 'dataArr' => $dataArr, 'query' => $where);

        echo json_encode($callback);
    }


    function declaration_name_field($nama_field)
    {
    	
    	if($nama_field == 'kode' OR $nama_field ==  'nama_produk' OR $nama_field == 'tanggal' OR $nama_field == 'status' OR $nama_field == 'origin' ){
    		$where = 'mp.'.$nama_field;
    	}else if($nama_field =='nama_mesin'){
    		$where = 'ms.'.$nama_field;
    	}else{
    		$where='';
    	}

        return $where;
    }



}