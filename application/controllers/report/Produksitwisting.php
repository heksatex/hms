<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Produksitwisting extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        $this->load->model('m_produksiTwisting');
        $this->load->library("Excel");

	}

	public function index()
	{	
		$id_dept        = 'PRODTWS';
        $data['id_dept']= $id_dept;

        $type_condition = $this->_module->get_first_type_conditon($id_dept);
        $data['mstFilter']      = $this->_module->get_list_mst_filter($id_dept);
        $data['type_condition'] = $type_condition;

		$this->load->view('report/v_produksi_twisting', $data);
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
    	$recordPerPage = 30;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }
 		$data_filter  = $this->input->post('data_filter'); 
        $data_filter_table= $this->input->post('data_filter_table'); //data filter dari table filer advanced
        $data_grouping = $this->input->post('data_grouping');
/*
        $kolom_order   = $this->input->post('nama_kolom');
        $order         = $this->input->post('order');
        if(!empty($kolom_order)){
            $kolom_order = "ORDER BY ".$kolom_order;
        }else{
            $kolom_order = "ORDER BY create_date";
            $order       = "desc";
        }
*/

        $type_filter  = $this->input->post('type_filter');
        $id_dept      = $this->input->post('id_dept');
        $id_dept_filter = 'PRODTWS';
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
                                $isi = addslashes($data1);
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

       if(!empty($where)){

	        $where ="where mp.dept_id ='".$id_dept."' AND mp.status NOT IN ('cancel', 'done')  AND".$where;
        }else{
	        $where ="where mp.dept_id ='".$id_dept."' AND mp.status NOT IN ('cancel', 'done') ";
        }

        $items = $this->m_produksiTwisting->get_list_produksi_by_dept($where);
 
        foreach ($items as $row) {

        	$ex 			= explode('|', $row->reff_note);
        	$i=0;
        	foreach($ex as $exs){

	        	if($i == 0){
	        		$sales_contract = trim($exs);
	        	}
	        	$i++;
        	}

        	/*
			*/
        	$dataRecord[] = array('kode'           => $row->kode, 
        						  'tgl_mo' 		   => tgl_indo2(date('d-m-y',strtotime($row->tanggal))), 
        						  'mc' 			   => $row->nama_mesin,
        						  'product'        => $row->nama_produk,
        						  'sales_contract' => $sales_contract,
        						  'origin'    => $row->origin, 
        						  'target'         => $row->qty_target,
        						  'qty1'           => $row->hph_qty1,
        						  'qty2'		   => $row->hph_qty2,
        						  'sisa'           => $row->sisa_target,
        						  'status'         => $row->status );

        	$sales_contract = '';
        }

        $allcount           = $this->m_produksiTwisting->getRecordCount($where);
        $total_record       = 'Total Data : '. number_format($allcount);

        $callback  = array('record' => $dataRecord, 'total_record'=>$total_record, 'dataArr' => $dataArr, 'query' => $where);

        echo json_encode($callback);
    }


    function declaration_name_field($nama_field)
    {
    	
    	if($nama_field == 'kode' OR $nama_field ==  'nama_produk' OR $nama_field == 'reff_note' OR $nama_field == 'status' ){
    		$where = 'mp.'.$nama_field;
    	}else if($nama_field =='nama_mesin'){
    		$where = 'ms.'.$nama_field;
    	}else{
    		$where = '';
    	}

        return $where;
    }


    function export()
    {
    	$id_dept  = 'WRD';
			
		$this->export_excel();

    }


    function export_excel()
    {	

    	$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	$where = $this->input->post('query');

 		$object->getActiveSheet()->SetCellValue('A1', 'Jadwal Produksi Twisting');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

		// header table
    	$table_head_columns  = array('No', 'MO', 'Tgl.MO', 'MC', 'Product', 'Sales Contract', 'Origin', 'Produksi Twisiting', 'Target', 'HPH/Qty1','HPH/Qty2', 'Sisa','Status');

    	$column = 0;
    	$merge  = TRUE;
    	foreach ($table_head_columns as $field) {
    		
    		// merge cell baris ke 3-4
    		if($column < 7  ){
    			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $field);	
				$object->getActiveSheet()->mergeCellsByColumnAndRow($column, 3, $column, 4);
    		}

    		if($column == 7){
    			$merge = TRUE;
    		}
    	
    		// merge cell produksi twisting
    		if($column >=7 AND $column <= 11){
    			if($column == 7 AND $merge == TRUE){
	    			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $field);	
					$object->getActiveSheet()->mergeCells('H3:K3');	
    			}elseif($merge == FALSE){
	    			$object->getActiveSheet()->setCellValueByColumnAndRow($column-1, 4, $field);	
    			}

    			$merge = FALSE;
    		}

    		if($column == 12){
                $object->getActiveSheet()->setCellValueByColumnAndRow($column-1, 3, $field);	
				$object->getActiveSheet()->mergeCellsByColumnAndRow($column-1, 3, $column-1, 4);
    		}

    		$column++;
    	}

		//Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

		// set column
		$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L');

		$object->getActiveSheet()->getStyle("A1:L4")->getFont()->setBold(true);
		$object->getSheet(0)->getColumnDimension('A')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('B')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('D')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('C')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('D')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('E')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('F')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('G')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('H')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('I')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('J')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('K')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('L')->setAutoSize(true);

		// set border header
		foreach ($index_header as $val) {
		
			$object->getActiveSheet()->getStyle($val.'3')->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray);
			//$object->getActiveSheet()->getStyle($val.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$object->getActiveSheet()->getStyle($val.'3:'.$val.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);


		}

		// body table
        $body = $this->m_produksiTwisting->get_list_produksi_by_dept($where);
        $no   = 1;
    	$rowCount = 5;

	    $sales_contract = '';

    	foreach ($body as $row) {

    		$ex  =  explode('|', $row->reff_note);
        	$i   = 0;
        	foreach($ex as $exs){

	        	if($i == 0){
	        		$sales_contract = trim($exs);
	        	}
	        	
	        	$i++;
        	}


			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($no));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, mb_strtoupper($row->kode,'UTF-8'));
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, tgl_indo2(date('d-m-y', strtotime($row->tanggal))));
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, mb_strtoupper($row->nama_mesin,'UTF-8'));
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row->nama_produk);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, mb_strtoupper($sales_contract,'UTF-8'));
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $row->origin);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row->qty_target);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $row->hph_qty1);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $row->hph_qty2);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->sisa_target);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $row->status);

			//align center
			$object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//set border body
			$object->getActiveSheet()->getStyle('A'.$rowCount.':B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount.':D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount.':F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount.':H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount.':J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount.':L'.$rowCount)->applyFromArray($styleArray);

			$no=$no+1;
            $rowCount++;
            
            $sales_contract = '';
    	}


        $object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="Jadwal Produksi Twisiting.xls"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');
    	


    }

}