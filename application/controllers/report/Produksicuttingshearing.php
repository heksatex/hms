<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Produksicuttingshearing extends MY_Controller
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
        $id_dept	     ='PRODCS';
        $data['id_dept'] = $id_dept;

        $type_condition         = $this->_module->get_first_type_conditon($id_dept);
        $data['mstFilter']      = $this->_module->get_list_mst_filter($id_dept);
        $data['type_condition'] = $type_condition;

		$this->load->view('report/v_produksi_cuttingshearing', $data);
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
        $id_dept_filter = 'PRODCS';
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

        $items = $this->m_produksiJacquard->get_list_produksi_jacquard_by_kode_2($where,$id_dept);
 
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
            $lbr_greige  = $row->lebar_greige.' '.$row->uom_lebar_greige;
            $lbr_jadi    = $row->lebar_jadi.' '.$row->uom_lebar_jadi;

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

        	$ex2 = explode('|', $row->reff_note);
        	$a   = 0;
        	foreach ($ex2 as $exs2) {

                if($a == 7){ // Gl
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $gl  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 8){ // mtr/gl
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
                if($a == 9 ){// l.greige
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $lbr_greige  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 10){ // l.jadi
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
                if($a == 11){ // pcs
                    $exp = explode('=', $exs2);
                     $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $pcs  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 12){ // gauge
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $gauge  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 13){ // stitch
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $stitch  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 14){ // courses
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $courses  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 15){ // rpm
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


        	$getBoM = $this->m_produksiJacquard->get_list_bahan_baku_by_kode($row->kode);
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

                if($meter!= ''){
                    $meter = number_format($meter,2);
                }
                
                if($hph_qty1!= ''){
                    $hph_qty1 = number_format($hph_qty1,2);
                }

                if($hph_qty2!= ''){
                    $hph_qty2 = number_format($hph_qty2,2);
                }

                if($sisa_target!= ''){
                    $sisa_target = number_format($sisa_target,2);
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
	        						  'gulung'         => $gl,
	        						  'mtr_gl'	       => $mtr_gl,
	        						  'pcs'            => $pcs,
	        						  'gauge'          => $gauge,
	        						  'stitch'         => $stitch,
	        						  'courses'        => $courses,
	        						  'rpm'            => $rpm,
	        						  'gb'             => $gb,
	        						  'rm'             => $val->nama_produk,
	        						  'target_qty'     => number_format($val->target_qty,2),
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

        $allcount           = $this->m_produksiJacquard->get_record_count_jacquard_2($where);
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


    function export_excel()
    {

        $this->load->library("excel");
        ob_start();
        $object = new PHPExcel();
        $object->setActiveSheetIndex(0);

        $where = $this->input->post('query');
        $id_dept = 'CS';

        $object->getActiveSheet()->SetCellValue('A1', 'Jadwal Produksi Cutting Shearing');
        $object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
        $object->getActiveSheet()->mergeCells('A1:S1');

        // table header
        $table_head_columns  = array('No', 'MO', 'Tgl.MO', 'MC', 'SC', 'PD', 'Corak','Marketing', 'Lebar', 'Greige','Jadi', 'Start Produksi', 'Finish Produksi', 'Total Order',  'Meter', 'Gulung', 'Mtr/Gl', 'Pcs', 'Bahan Baku', 'Target Qty', 'Hasil Produksi', 'HPH/Qty1','HPH/Qty2', 'Gulung','Sisa/Qty','Status');

        $column = 0;
        $merge  = TRUE;
        $columns= '';
        $count_merge = 0; // untuk jml yg di merge
        foreach ($table_head_columns as $field) {

            if($column < 8 OR ($column >= 11 AND $column <= 12) OR ($column >=17 AND $column <=19 ) OR $column == 25){
                if($column < 8 ){
                    $columns = $column;
                }else if(($column >= 11 AND $column <= 12) OR ($column >=17 AND $column <=19) OR $column == 25) {
                    $columns = $column-$count_merge;
                }

                $object->getActiveSheet()->setCellValueByColumnAndRow($columns, 3, $field);  
                $object->getActiveSheet()->mergeCellsByColumnAndRow($columns, 3, $columns, 4);
            }


            // merge cell lebar greige, lebar jadi
            if(($column >= 8 AND $column <=10) OR ( $column >= 13 AND $column <=16) OR ($column >= 20 AND $column <=24)){
                if($column == 13 OR $column == 20){$merge = TRUE;}

                if($merge == TRUE){
                    $columns = $column-$count_merge;
                    $object->getActiveSheet()->setCellValueByColumnAndRow($columns, 3, $field);  
                    if($column == 8){
                        $object->getActiveSheet()->mergeCells('I3:J3');// merge cell lebar
                    }elseif($column == 13){
                        $object->getActiveSheet()->mergeCells('M3:O3');// megre cell total order
                    }elseif($column == 20){
                        $object->getActiveSheet()->mergeCells('S3:V3');// megre cell hasil produksi
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
        $object->getActiveSheet()->getStyle('I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->getStyle('M3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->getStyle('Y3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // setting huruf tebal
        $object->getActiveSheet()->getStyle("A1:X4")->getFont()->setBold(true);


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

        $styleArray_top = array(
              'borders' => array(
                'top' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
        );  

    
        // set column heeader
        $index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W');
        $loop = 1;
        // set border, align center, set size width
        foreach ($index_header as $val) {
        
            $object->getActiveSheet()->getStyle($val.'3')->applyFromArray($styleArray);
            $object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray);
            $object->getActiveSheet()->getStyle($val.'3:'.$val.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);

            if($loop <= 3 OR ($loop >= 5 AND $loop <= 6)){ // index A, B, C, E, F, U
                $object->getSheet(0)->getColumnDimension($val)->setAutoSize(true);
            }else if($loop == 4){ // index D
                $object->getSheet(0)->getColumnDimension($val)->setWidth(10);
            }else if($loop == 7){ // index G
                $object->getSheet(0)->getColumnDimension($val)->setWidth(27);
            }else if($loop == 8){// index H
                $object->getSheet(0)->getColumnDimension($val)->setWidth(17);
            }else if($loop== 9 OR $loop == 10 OR ($loop >= 13 AND $loop <=16) OR ($loop >=18 AND $loop <=  24) ){// index I, J, M, N, O, P, R, S, T, W, X,
                $object->getSheet(0)->getColumnDimension($val)->setWidth(9);
            }else if($loop == 11 OR $loop == 12){// index K, L
                $object->getSheet(0)->getColumnDimension($val)->setWidth(10);
            }else if($loop == 17){// index Q
                $object->getSheet(0)->getColumnDimension($val)->setWidth(22);
            }

            $object->getActiveSheet()->getStyle($val.'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );

            $object->getActiveSheet()->getStyle($val.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );

            $loop++;
        }

        // set wrap text
        $object->getActiveSheet()->getStyle('K3:K'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
        $object->getActiveSheet()->getStyle('L3:L'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);  

        // diatas column A5 tidak bergerak
        //$object->getActiveSheet()->freezePane('A5'); 

        // >> BODY

        $items = $this->m_produksiJacquard->get_list_produksi_jacquard_by_kode_2($where,$id_dept);
        $no    = 1;
        $rowCount = 5;
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
            $lbr_greige  = $row->lebar_greige.' '.$row->uom_lebar_greige;
            $lbr_jadi    = $row->lebar_jadi.' '.$row->uom_lebar_jadi;

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

            $ex2 = explode('|', $row->reff_note);
            $a   = 0;
            foreach ($ex2 as $exs2) {

                if($a == 7){ // Gl
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $gl  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 8){ // mtr/gl
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
                if($a == 9 ){// l.greige
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $lbr_greige  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 10){ // l.jadi
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
                if($a == 11){ // pcs
                    $exp = explode('=', $exs2);
                     $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $pcs  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 12){ // gauge
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $gauge  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 13){ // stitch
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $stitch  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 14){ // courses
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        if($b == 2){
                            $courses  = trim($exps);
                        }
                        $b++;
                    }
                }
                if($a == 15){ // rpm
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

            $getBoM = $this->m_produksiJacquard->get_list_bahan_baku_by_kode($row->kode);
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
                    $meter       = '';
                    $hph_qty1    = '';
                    $hph_qty2    = '';
                    $gulung      = '';
                    $sisa_target = '';
                    $status      = '';
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
                $object->getActiveSheet()->SetCellValue('F'.$rowCount, $pd);
                $object->getActiveSheet()->SetCellValue('G'.$rowCount, $nama_produk);
                $object->getActiveSheet()->SetCellValue('H'.$rowCount, $mkt);
                $object->getActiveSheet()->SetCellValue('I'.$rowCount, $lbr_greige);
                $object->getActiveSheet()->SetCellValue('J'.$rowCount, $lbr_jadi);
                $object->getActiveSheet()->SetCellValue('K'.$rowCount, $start_time);
                $object->getActiveSheet()->SetCellValue('L'.$rowCount, $finish_time);
                $object->getActiveSheet()->SetCellValue('M'.$rowCount, $meter);
                $object->getActiveSheet()->SetCellValue('N'.$rowCount, $gl);
                $object->getActiveSheet()->SetCellValue('O'.$rowCount, $mtr_gl);
                $object->getActiveSheet()->SetCellValue('P'.$rowCount, $pcs);
                $object->getActiveSheet()->SetCellValue('Q'.$rowCount, $val->nama_produk);
                $object->getActiveSheet()->SetCellValue('R'.$rowCount, $val->target_qty);
                $object->getActiveSheet()->SetCellValue('S'.$rowCount, $hph_qty1);
                $object->getActiveSheet()->SetCellValue('T'.$rowCount, $hph_qty2);
                $object->getActiveSheet()->SetCellValue('U'.$rowCount, $gulung);
                $object->getActiveSheet()->SetCellValue('V'.$rowCount, $sisa_target);
                $object->getActiveSheet()->SetCellValue('W'.$rowCount, $status);

                // set align 
                $object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('C'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('F'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('K'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $object->getActiveSheet()->getStyle('L'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
                // set wrapText
                $object->getActiveSheet()->getStyle('D'.$rowCount.':D'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
                $object->getActiveSheet()->getStyle('G'.$rowCount.':G'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
                $object->getActiveSheet()->getStyle('H'.$rowCount.':H'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
                $object->getActiveSheet()->getStyle('K'.$rowCount.':K'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
                $object->getActiveSheet()->getStyle('L'.$rowCount.':L'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);  
                $object->getActiveSheet()->getStyle('Q'.$rowCount.':Q'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);  


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

                $boM++;
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

            $no++;

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

        }   

        // << BODY
     
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = "Jadwal Produksi Cutting Shearing.xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);

		die(json_encode($response));

    }

}