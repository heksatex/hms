<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Produksitricot extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('_module');
        $this->load->model('m_produksiTricot');
        $this->load->library('pagination');
	}

	public function index()
	{	
        $id_dept	     ='PRODTRI';
        $data['id_dept'] = $id_dept;

        $type_condition = $this->_module->get_first_type_conditon($id_dept);
        $data['mstFilter']      = $this->_module->get_list_mst_filter($id_dept);
        $data['type_condition'] = $type_condition;

		$this->load->view('report/v_produksi_tricot', $data);
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

        $recordPerPage = 100;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }
    
 		$data_filter  = $this->input->post('data_filter'); 
        $data_filter_table= $this->input->post('data_filter_table'); //data filter dari table filer advanced

        $type_filter  = $this->input->post('type_filter');
        $id_dept      = $this->input->post('id_dept');
        $id_dept_filter = 'PRODTRI';
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
                if($row['operator'] == 'LIKE' OR $row['operator'] == 'NOT LIKE'){
                    $isi = " ".$row['operator']." '%".addslashes($row['isi'])."%' ";
                    $operator = $row['operator'] ;
                   // $whereAll .= "LIKE '%".$row['isi']."%' ".$condition;
                }else if($row['operator'] == 'is'){
                    if($row['isi'] == 'Empty'){
                        $isi_  = ' IS NULL';
                    }else{
                        $isi_  = ' IS NOT NULL';
                    }
                    
                    $isi =  $isi_;
                    $operator = $row['operator'];
                }else{
                    $isi = $row['operator']." '".addslashes($row['isi'])."' ";
                    $operator = $row['operator'];
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

                        if($operator == 'LIKE' OR $operator == 'NOT LIKE'){
                            $isi_   = " ".$operator." '%".addslashes($isi)."%' ";
                            $where .= '  '.$nama_field.' '.$isi_.'   '.$row['condition'].' ';// condition = OR
                            // $operator = 'LIKE';
                        }else if($operator == 'is'){
                            // $isi_   = "  '".addslashes($isi)."' ";
                            if($isi == 'Empty'){
                                $isi_  = ' IS NULL';
                            }else{
                                $isi_  = ' IS NOT NULL';
                            }
                            $where .= '  '.$nama_field.' '.$isi_.'   '.$row['condition'].' ';// condition = OR
                        }else{
                            $isi_   = $operator." '".addslashes($isi)."' ";
                            // $operator = $row['operator'];
                            $where .= '  '.$nama_field.' '.$isi_.'   '.$row['condition'].' ';// condition = OR
                        }

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
        $status_sc      = '';
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
        $ket            = '';
        $tc             = '';


        if(!empty($where)){
	        // $where ="where mp.dept_id ='".$id_dept."' AND mp.status NOT IN ('cancel', 'done')  AND".$where;
	        $where ="WHERE sc.nama_produk LIKE '%Tricot%' AND ".$where;
        }else{
	        $where ="WHERE sc.nama_produk LIKE '%Tricot%'  ";
        }

        $items = $this->m_produksiTricot->get_list_produksi_tricot_by_kode($where,$record,$recordPerPage);
 
        foreach ($items as $row) {

        	$mo          = $row->kode;
            if($row->tanggal != ''){
                $tgl_mo      =  tgl_indo2(date('d-m-y',strtotime($row->tanggal)));
            }else{
                $tgl_mo      =  "";
            }
        	$nama_mesin  = $row->nama_mesin;
        	$nama_produk = $row->nama_produk;
            if($row->start_time != '' ){
                $start_time  = tgl_indo2(date('d-m-y H:i:s',strtotime($row->start_time)));
            }else{
                $start_time  = "";
            }
            if($row->finish_time!= ''){
                $finish_time = tgl_indo2(date('d-m-y H:i:s',strtotime($row->finish_time)));
            }else{
                $finish_time  = "";
            }
        	$target_pd   = $row->target_pd;
        	$hph_qty1    = $row->hph_qty1;
			$hph_qty2    = $row->hph_qty2;
        	$gulung      = $row->gulung;
			$sisa_target = $row->sisa_target;
      		$status      = $row->status;
            $lbr_greige  = $row->lebar_greige.' '.$row->uom_lebar_greige;
            $lbr_jadi    = $row->lebar_jadi.' '.$row->uom_lebar_jadi;
            $mkt         = $row->nama_sales_group;
            $pd          = $row->kode_prod;
            $sales_contract = $row->sales_order;
            $status_sc   = $row->status_sc;

        	// explode origin
        	// $ex = explode('|', $row->origin);
        	// $i  = 0;
        	// foreach($ex as $exs){

        	// 	if($i== 0){ // SC
        	// 		$sales_contract = trim($exs);
        	// 		$mkt = $this->m_produksiTricot->get_marketing_by_kode($sales_contract);
        	// 	}else if($i == 1){ // PD
        	// 		$pd = trim($exs);
        	// 	}

	        // 	$i++;
        	// }

        	// explode reff_note

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
                /*
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
                */

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

                if($a == 14){ // ket jdwal
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $ket  = trim($exps);
                        }
                        $b++;
                    }
                }

                if($a == 15){ // tc
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $tc  = trim($exps);
                        }
                        $b++;
                    }
                }


        	 	$a++;
        	} 

        	$getBoM = $this->m_produksiTricot->get_list_bahan_baku_by_kode($row->kode);
        	$boM    = 1;
        	foreach ($getBoM as $val) {
        		# code...
	        	if($boM > 1){
	        		$mo          = '';
		        	$tgl_mo      = '';
		        	$nama_mesin  = '';
		        	$nama_produk = '';
		        	$start_time  = '';
		        	$finish_time = '';
		        	$target_pd   = '';
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
                    $ket            = '';
                    $tc             = '';
	        	}

	        	// explode reff
	        	$exp = explode('|', $val->reff_note);
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

        		$boM++;
                if($target_pd!= ''){
                    $target_pd = number_format($target_pd,2);
                }

                if($hph_qty1 != ''){
                    $hph_qty1 = number_format($hph_qty1,2);
                }

                if($hph_qty2 != ''){
                    $hph_qty2 = number_format($hph_qty2,2);
                }
                if($sisa_target != ''){
                    $sisa_target = number_format($sisa_target,2);
                }
                // buat array dataRecord
                $dataRecord[] = array('kode'           => $mo, 
                                      'tgl_mo' 		   => $tgl_mo, 
                                      'mc' 			   => $nama_mesin,
                                      'sc'             => $sales_contract,
                                      'status_sc'      => $status_sc,
                                      'pd'			   => $pd,
                                      'marketing'	   => $mkt,
                                      'corak'          => $nama_produk,
                                      'lbr_greige'     => $lbr_greige,
                                      'lbr_jadi'       => $lbr_jadi,
                                      'start_produksi' => $start_time,
                                      'finish_produksi'=> $finish_time,
                                      'target_pd'      => $target_pd,
                                      'gulung'         => $gl,
                                      'mtr_gl'	       => $mtr_gl,
                                      'pcs'            => $pcs,
                                      'gauge'          => $gauge,
                                      'stitch'         => $stitch,
                                      'courses'        => $courses,
                                      'rpm'            => $rpm,
                                      'gb'             => $gb,
                                      'bd'             => $val->nama_produk,
                                      'target_qty'     => number_format($val->target_qty,2),
                                      'run_in'         => $run_in,
                                      'qty1'	       => $hph_qty1,
                                      'qty2'           => $hph_qty2,
                                      'h_gulung'	   => $gulung,
                                      'sisa'           => $sisa_target,
                                      'ket'            => $ket,
                                      'tc'             => $tc,
                                      'status'         => $status
                                     );
        	}

            if($row->kode == ''){
                // buat array dataRecord
                $dataRecord[] = array('kode'         => $mo, 
                                    'tgl_mo' 		 => $tgl_mo, 
                                    'mc' 			 => $nama_mesin,
                                    'sc'             => $sales_contract,
                                    'status_sc'      => $status_sc,
                                    'pd'			 => $pd,
                                    'marketing'	     => $mkt,
                                    'corak'          => $nama_produk,
                                    'lbr_greige'     => $lbr_greige,
                                    'lbr_jadi'       => $lbr_jadi,
                                    'start_produksi' => $start_time,
                                    'finish_produksi'=> $finish_time,
                                    'target_pd'      => $target_pd,
                                    'gulung'         => $gl,
                                    'mtr_gl'	     => $mtr_gl,
                                    'pcs'            => $pcs,
                                    'gauge'          => $gauge,
                                    'stitch'         => $stitch,
                                    'courses'        => $courses,
                                    'rpm'            => $rpm,
                                    'gb'             => $gb,
                                    'bd'             => "",
                                    'target_qty'     => "",
                                    'run_in'         => $run_in,
                                    'qty1'	         => $hph_qty1,
                                    'qty2'           => $hph_qty2,
                                    'h_gulung'	     => $gulung,
                                    'sisa'           => $sisa_target,
                                    'ket'            => $ket,
                                    'tc'             => $tc,
                                    'status'         => $status

                );
            }


        	$sales_contract = '';
        	$status_sc      = '';
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
            $ket            = '';
            $tc             = '';

        }

        $allcount           = $this->m_produksiTricot->get_record_count_tricot($where);
        $total_record       = 'Total Data : '. number_format($allcount);


        $config['base_url']         = base_url().'report/produksitricot/loadData';
        $config['use_page_numbers'] = TRUE;
        $config['total_rows']       = $allcount;
        $config['per_page']         = $recordPerPage;
        $config['num_links']        = 1;
        $config['next_link']        = '>';
        $config['prev_link']        = '<';
        $this->pagination->initialize($config);
        $pagination         = $this->pagination->create_links();

        $callback  = array('record' => $dataRecord, 'total_record'=>$total_record, 'pagination'=>$pagination, 'dataArr' => $dataArr, 'query' => $where);

        echo json_encode($callback);
    }


    function declaration_name_field($nama_field)
    {
    	
    	if($nama_field == 'kode' OR $nama_field == 'tanggal' OR $nama_field == 'status' ){
    		$where = 'mrp.'.$nama_field;
    	}else if($nama_field =='nama_produk' OR $nama_field == 'sales_order' OR $nama_field == 'status_sc'){
            if($nama_field == 'status_sc'){
                $where = 'sc.nama_status';
            }else{
                $where = 'sc.'.$nama_field;
            }
    	}else if($nama_field =='kode_prod'){
            $where = 'po.'.$nama_field;
        }else if($nama_field == 'nama_sales_group'){
            $where = 'sg.'.$nama_field;
        }else if($nama_field =='nama_mesin'){
    		$where = 'ms.'.$nama_field;
    	}else{
    		$where='';
    	}

        return $where;
    }



    function export_excel()
    {

        $this->load->library('excel');
        ob_start();
        
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);

        $where = $this->input->post('filter');

        $object->getActiveSheet()->SetCellValue('A1', 'Jadwal Produksi Tricot');
        $object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
        $object->getActiveSheet()->mergeCells('A1:S1');

        // table header
        $table_head_columns  = array('No', 'MO', 'Tgl.MO', 'MC', 'SC','Status SC', 'PD', 'Marketing','Corak', 'Lebar', 'Greige','Jadi', 'Start Produksi', 'Finish Produksi', 'Total Order',  'Meter', 'Gulung', 'Mtr/Gl', 'Pcs', 'Gauge','Stitch/Cm' , 'Courses', 'RPM', 'GB', 'BD', 'Target Qty', 'RUN IN','Hasil Produksi', 'Qty1','Qty2', 'Gulung','Sisa Qty1','Keterangan','TC','Status MO');

        $column = 0;
        $merge  = TRUE;
        $columns= '';
        $count_merge = 0; // untuk jml yg di merge
        foreach ($table_head_columns as $field) {

            // merge cell baris ke 3-4
            if($column < 9 OR ($column >= 12 AND $column <= 13) OR ($column >=18 AND $column <=26 ) OR $column >= 31){

                if($column < 9 ){
                    $columns = $column;
                }else if(($column >= 12 AND $column <= 13) OR ($column >=18 AND $column <=26) OR $column >=31) {
                    $columns = $column-$count_merge;
                }

                $object->getActiveSheet()->setCellValueByColumnAndRow($columns, 3, $field);  
                $object->getActiveSheet()->mergeCellsByColumnAndRow($columns, 3, $columns, 4);
            }


            // merge cell lebar greige, lebar jadi
            if(($column >= 9 AND $column <=11) OR ( $column >= 14 AND $column <=17) OR ($column >= 27 AND $column <=31)){
                if($column == 14 OR $column == 27){$merge = TRUE;}

                if($merge == TRUE){
                    $columns = $column-$count_merge;
                    $object->getActiveSheet()->setCellValueByColumnAndRow($columns, 3, $field);  
                    if($column == 9){
                        $object->getActiveSheet()->mergeCells('J3:K3');// merge cell lebar
                    }elseif($column == 14){
                        $object->getActiveSheet()->mergeCells('N3:P3');// megre cell total order
                    }elseif($column == 27){
                        $object->getActiveSheet()->mergeCells('Z3:AB3');// megre cell hasil produksi
                    }
                    $count_merge++;

                }else if($merge == false){
                    $columns = $column-$count_merge;
                    $object->getActiveSheet()->setCellValueByColumnAndRow($columns, 4, $field);    
                }

                $merge = FALSE;
            }  

            $column++;

        } // end foreach


        // setting align header
        $object->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->getStyle('N3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->getStyle('U3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // setting huruf tebal
        $object->getActiveSheet()->getStyle("A1:AF4")->getFont()->setBold(true);


        //Border 
        $styleArray = array(
              'borders' => array(
                'allborders' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
        );  

        $styleArray_left = array(
              'borders' => array(
                'left' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
        );  

        $styleArray_right = array(
              'borders' => array(
                'right' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
        );  

        $styleArray_top = array(
              'borders' => array(
                'top' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
        );  

        $styleArray_bottom = array(
              'borders' => array(
                'bottom' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
        );  
    
        // set column heeader
        $index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF');
        $loop = 1;
        // set border, align center, set size width
        foreach ($index_header as $val) {
        
            $object->getActiveSheet()->getStyle($val.'3')->applyFromArray($styleArray);
            $object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray);
            //$object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray_bottom);
            //$object->getActiveSheet()->getStyle($val.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $object->getActiveSheet()->getStyle($val.'3:'.$val.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);

            if($loop <= 3 OR ($loop >= 5 AND $loop <= 7) OR $loop == 22){ // index A, B, C, E, F, X
                $object->getSheet(0)->getColumnDimension($val)->setAutoSize(true);
            }else if($loop == 4){ // index D
                $object->getSheet(0)->getColumnDimension($val)->setWidth(19);
            }else if($loop == 8){ // index G
                $object->getSheet(0)->getColumnDimension($val)->setWidth(27);
            }else if($loop == 9){// index H
                $object->getSheet(0)->getColumnDimension($val)->setWidth(17);
            }else if($loop== 10 OR $loop == 11 OR ($loop >= 12 AND $loop <=21) OR ($loop >=24 AND $loop <=  32) ){// index I, J,M,N,O, P, Q, R, S, T, W, X, Y, Z, AA, AB, AC, AD, AE
                $object->getSheet(0)->getColumnDimension($val)->setWidth(9);
            }else if($loop == 12 OR $loop == 13){// index K, L
                $object->getSheet(0)->getColumnDimension($val)->setWidth(10);
            }else if($loop == 23){// index V
                $object->getSheet(0)->getColumnDimension($val)->setWidth(22);
            }

            $object->getActiveSheet()->getStyle($val.'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );

            $object->getActiveSheet()->getStyle($val.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );

            $loop++;
        }

        // set wrap text
        $object->getActiveSheet()->getStyle('L3:L'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);  
        $object->getActiveSheet()->getStyle('L3:M'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);

        // diatas column A5 tidak bergerak
        //$object->getActiveSheet()->freezePane('A5'); 

        // >> BODY

        $items = $this->m_produksiTricot->get_list_produksi_tricot_by_kode_no_limit($where);
        $no    = 1;
        $rowCount = 5;
        $sales_contract = '';
        $status_sc      = '';
        $pd             = '';
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
        $ket            = '';
        $tc             = '';
 
        foreach ($items as $row) {

            $mo          = $row->kode;
            if($row->tanggal != ''){
                $tgl_mo      =  tgl_indo2(date('d-m-y',strtotime($row->tanggal)));
            }else{
                $tgl_mo      =  "";
            }
        	$nama_mesin  = $row->nama_mesin;
        	$nama_produk = $row->nama_produk;
            if($row->start_time != '' ){
                $start_time  = tgl_indo2(date('d-m-y H:i:s',strtotime($row->start_time)));
            }else{
                $start_time  = "";
            }
            if($row->finish_time!= ''){
                $finish_time = tgl_indo2(date('d-m-y H:i:s',strtotime($row->finish_time)));
            }else{
                $finish_time  = "";
            }
        	$target_pd   = $row->target_pd;
        	$hph_qty1    = $row->hph_qty1;
			$hph_qty2    = $row->hph_qty2;
        	$gulung      = $row->gulung;
			$sisa_target = $row->sisa_target;
      		$status      = $row->status;
      		$status_sc   = $row->status_sc;
            $lbr_greige  = $row->lebar_greige.' '.$row->uom_lebar_greige;
            $lbr_jadi    = $row->lebar_jadi.' '.$row->uom_lebar_jadi;
            $mkt         = $row->nama_sales_group;
            $pd          = $row->kode_prod;
            $sales_contract = $row->sales_order;

            // explode reff_note
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
                /*
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
                */
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
                if($a == 14){ // ket jdwal
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $ket  = trim($exps);
                        }
                        $b++;
                    }
                }

                if($a == 15){ // tc
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $tc  = trim($exps);
                        }
                        $b++;
                    }
                }


                $a++;
            } 

            $getBoM = $this->m_produksiTricot->get_list_bahan_baku_by_kode($row->kode);
            $boM    = 1;
            foreach ($getBoM as $val) {
                # code...
                $num = $no;
                if($boM > 1){
                    $mo          = '';
                    $tgl_mo      = '';
                    $nama_mesin  = '';
                    $nama_produk = '';
                    $start_time  = '';
                    $finish_time = '';
                    $target_pd   = '';
                    $hph_qty1    = '';
                    $hph_qty2    = '';
                    $gulung      = '';
                    $sisa_target = '';
                    $status      = '';
                    $status_sc    = '';
                    $sales_contract = '';
                    $pd             = '';
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
                    $ket            = '';
                    $tc             = '';
                    $num            = '';
                }

                // explode reff
                $exp = explode('|', $val->reff_note);
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

                $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num));
                $object->getActiveSheet()->SetCellValue('B'.$rowCount, $mo);
                $object->getActiveSheet()->SetCellValue('C'.$rowCount, $tgl_mo);
                $object->getActiveSheet()->SetCellValue('D'.$rowCount, $nama_mesin);
                $object->getActiveSheet()->SetCellValue('E'.$rowCount, $sales_contract);
                $object->getActiveSheet()->SetCellValue('F'.$rowCount, $status_sc);
                $object->getActiveSheet()->SetCellValue('G'.$rowCount, $pd);
                $object->getActiveSheet()->SetCellValue('H'.$rowCount, $mkt);
                $object->getActiveSheet()->SetCellValue('I'.$rowCount, $nama_produk);
                $object->getActiveSheet()->SetCellValue('J'.$rowCount, $lbr_greige);
                $object->getActiveSheet()->SetCellValue('K'.$rowCount, $lbr_jadi);
                $object->getActiveSheet()->SetCellValue('L'.$rowCount, $start_time);
                $object->getActiveSheet()->SetCellValue('M'.$rowCount, $finish_time);
                $object->getActiveSheet()->SetCellValue('N'.$rowCount, $target_pd);
                $object->getActiveSheet()->SetCellValue('O'.$rowCount, $gl);
                $object->getActiveSheet()->SetCellValue('P'.$rowCount, $mtr_gl);
                $object->getActiveSheet()->SetCellValue('Q'.$rowCount, $pcs);
                $object->getActiveSheet()->SetCellValue('R'.$rowCount, $gauge);
                $object->getActiveSheet()->SetCellValue('S'.$rowCount, $stitch);
                $object->getActiveSheet()->SetCellValue('T'.$rowCount, $courses);
                $object->getActiveSheet()->SetCellValue('U'.$rowCount, $rpm);
                $object->getActiveSheet()->SetCellValue('V'.$rowCount, $gb);
                $object->getActiveSheet()->SetCellValue('W'.$rowCount, $val->nama_produk);
                $object->getActiveSheet()->SetCellValue('X'.$rowCount, $val->target_qty);
                $object->getActiveSheet()->SetCellValue('Y'.$rowCount, $run_in);
                $object->getActiveSheet()->SetCellValue('Z'.$rowCount, $hph_qty1);
                $object->getActiveSheet()->SetCellValue('AA'.$rowCount, $hph_qty2);
                $object->getActiveSheet()->SetCellValue('AB'.$rowCount, $gulung);
                $object->getActiveSheet()->SetCellValue('AC'.$rowCount, $sisa_target);
                $object->getActiveSheet()->SetCellValue('AD'.$rowCount, $ket);
                $object->getActiveSheet()->SetCellValue('AE'.$rowCount, $tc);
                $object->getActiveSheet()->SetCellValue('AF'.$rowCount, $status);

                // set align 
                $object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('C'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('F'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('G'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('M'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('N'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('V'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('AE'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
                // // set wrapText
                $object->getActiveSheet()->getStyle('D'.$rowCount.':D'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
                $object->getActiveSheet()->getStyle('H'.$rowCount.':H'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
                $object->getActiveSheet()->getStyle('I'.$rowCount.':I'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
                $object->getActiveSheet()->getStyle('L'.$rowCount.':L'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
                $object->getActiveSheet()->getStyle('M'.$rowCount.':M'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);  
                $object->getActiveSheet()->getStyle('N'.$rowCount.':N'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);  
                $object->getActiveSheet()->getStyle('W'.$rowCount.':W'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);  


                $object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('P'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('Q'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('R'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('S'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('T'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('U'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('V'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('W'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('X'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('Y'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('Z'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AA'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AB'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AC'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AD'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AE'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AF'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AG'.$rowCount)->applyFromArray($styleArray_left);
             

                $boM++;
                $rowCount++;
            }

            if($row->kode == ''){

                $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($no));
                $object->getActiveSheet()->SetCellValue('B'.$rowCount, $mo);
                $object->getActiveSheet()->SetCellValue('C'.$rowCount, $tgl_mo);
                $object->getActiveSheet()->SetCellValue('D'.$rowCount, $nama_mesin);
                $object->getActiveSheet()->SetCellValue('E'.$rowCount, $sales_contract);
                $object->getActiveSheet()->SetCellValue('F'.$rowCount, $status_sc);
                $object->getActiveSheet()->SetCellValue('G'.$rowCount, $pd);
                $object->getActiveSheet()->SetCellValue('H'.$rowCount, $mkt);
                $object->getActiveSheet()->SetCellValue('I'.$rowCount, $nama_produk);
                $object->getActiveSheet()->SetCellValue('J'.$rowCount, $lbr_greige);
                $object->getActiveSheet()->SetCellValue('K'.$rowCount, $lbr_jadi);
                $object->getActiveSheet()->SetCellValue('L'.$rowCount, $start_time);
                $object->getActiveSheet()->SetCellValue('M'.$rowCount, $finish_time);
                $object->getActiveSheet()->SetCellValue('N'.$rowCount, $target_pd);
                $object->getActiveSheet()->SetCellValue('O'.$rowCount, $gl);
                $object->getActiveSheet()->SetCellValue('P'.$rowCount, $mtr_gl);
                $object->getActiveSheet()->SetCellValue('Q'.$rowCount, $pcs);
                $object->getActiveSheet()->SetCellValue('R'.$rowCount, $gauge);
                $object->getActiveSheet()->SetCellValue('S'.$rowCount, $stitch);
                $object->getActiveSheet()->SetCellValue('T'.$rowCount, $courses);
                $object->getActiveSheet()->SetCellValue('U'.$rowCount, $rpm);
                $object->getActiveSheet()->SetCellValue('V'.$rowCount, $gb);
                $object->getActiveSheet()->SetCellValue('W'.$rowCount, "");
                $object->getActiveSheet()->SetCellValue('X'.$rowCount, "");
                $object->getActiveSheet()->SetCellValue('Y'.$rowCount, "");
                $object->getActiveSheet()->SetCellValue('Z'.$rowCount, "");
                $object->getActiveSheet()->SetCellValue('AA'.$rowCount, "");
                $object->getActiveSheet()->SetCellValue('AB'.$rowCount, "");
                $object->getActiveSheet()->SetCellValue('AC'.$rowCount, "");
                $object->getActiveSheet()->SetCellValue('AD'.$rowCount, $ket);
                $object->getActiveSheet()->SetCellValue('AE'.$rowCount, $tc);
                $object->getActiveSheet()->SetCellValue('AF'.$rowCount, $status);

                // set align 
                $object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('C'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('F'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('G'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('M'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('N'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('V'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('AE'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
                // // set wrapText
                $object->getActiveSheet()->getStyle('D'.$rowCount.':D'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
                $object->getActiveSheet()->getStyle('H'.$rowCount.':H'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
                $object->getActiveSheet()->getStyle('I'.$rowCount.':I'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
                $object->getActiveSheet()->getStyle('L'.$rowCount.':L'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
                $object->getActiveSheet()->getStyle('M'.$rowCount.':M'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);  
                $object->getActiveSheet()->getStyle('N'.$rowCount.':N'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);  
                $object->getActiveSheet()->getStyle('W'.$rowCount.':W'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);   


                $object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('P'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('Q'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('R'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('S'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('T'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('U'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('V'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('W'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('X'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('Y'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('Z'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AA'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AB'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AC'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AD'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AE'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AF'.$rowCount)->applyFromArray($styleArray_left);
                $object->getActiveSheet()->getStyle('AG'.$rowCount)->applyFromArray($styleArray_left);

                $rowCount++;
            }

                $object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('P'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('Q'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('R'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('S'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('T'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('U'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('V'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('W'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('X'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('Y'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('Z'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('AA'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('AB'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('AC'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('AD'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('AE'.$rowCount)->applyFromArray($styleArray_top);
                $object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray_top);


            $no++;

            $sales_contract = '';
            $status_sc      = '';
            $pd             = '';
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
            $ket            = '';
            $tc             = '';

        }   

        // << BODY

        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');

		$xlsData = ob_get_contents();
		ob_end_clean();

		$response =  array(
				'op'        => 'ok',
				'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
				'filename'  => "Jadwal Produksi Tricot.xlsx"
		);

		die(json_encode($response));

    }


}