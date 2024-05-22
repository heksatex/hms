<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Produksiwarpingpanjang extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        $this->load->model('m_produksiWarpingPanjang');
	}


	public function index()
	{	
		$id_dept        = 'PRODWRP';
        $data['id_dept']= $id_dept;

        $type_condition = $this->_module->get_first_type_conditon($id_dept);
        $data['mstFilter']      = $this->_module->get_list_mst_filter($id_dept);
        $data['type_condition'] = $type_condition;

		$this->load->view('report/v_produksi_warping_panjang', $data);	}


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

        $type_filter  = $this->input->post('type_filter');
        $id_dept      = $this->input->post('id_dept');
        $id_dept_filter = 'PRODWRP';
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
        $mo_knitting 	= '';
        $mc_knitting    = '';
        $corak          = '';
        $jns_bng        = '';
       

       if(!empty($where)){

	        $where ="where mp.dept_id ='".$id_dept."' AND mp.status NOT IN ('cancel', 'done')  AND ".$where;
        }else{
	        $where ="where mp.dept_id ='".$id_dept."' AND mp.status NOT IN ('cancel', 'done') ";
        }

        $items = $this->m_produksiWarpingPanjang->get_list_produksi_wrp_by_dept($id_dept,$where);
 
        foreach ($items as $row) {

        	$ex 			= explode('|', $row->reff_note);
        	$i=0;
        	foreach($ex as $exs){

	        	if($i == 0){
	        		$sales_contract = trim($exs);
	        	}
	        	if($i == 1){
	        		$mo_knitting    = trim($exs);
	        	}
	        	if($i == 2){
	        		$mc_knitting    = trim($exs);
	        	}
	        	if($i == 3){
	        		$corak          = trim($exs);
	        	}
	        	if($i == 4){
	        		$jns_bng        = trim($exs);
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
        						  'mo_knitting'    => $mo_knitting, 
        						  'mc_knitting'    => $mc_knitting,
        						  'corak'          => $corak,
        						  'jns_bng'        => $jns_bng, 
        						  'target'         => number_format($row->qty_target,2),
        						  'qty1'           => number_format($row->hph_qty1,2),
        						  'qty2'		   => $row->hph_qty2,
        						  'sisa'           => number_format($row->sisa_target,2),
        						  'status'         => $row->status );

        	$sales_contract = '';
	        $mo_knitting 	= '';
	        $mc_knitting    = '';
	        $corak          = '';
	        $jns_bng        = '';

        }

        $allcount           = $this->m_produksiWarpingPanjang->get_record_count_wrp($where);
        $total_record       = 'Total Data : '. number_format($allcount);

        $callback  = array('record' => $dataRecord, 'total_record'=>$total_record, 'dataArr' => $dataArr, 'query' => $where);

        echo json_encode($callback);
    }

    function declaration_name_field($nama_field)
    {
    	
    	if($nama_field == 'kode' OR $nama_field ==  'nama_produk' OR $nama_field == 'tanggal'  OR $nama_field == 'reff_note' OR $nama_field == 'status' ){
    		$where = 'mp.'.$nama_field;
    	}else if($nama_field =='nama_mesin'){
    		$where = 'ms.'.$nama_field;
    	}else{
    		$where =  '';
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

 		$object->getActiveSheet()->SetCellValue('A1', 'Jadwal Produksi Warping Panjang');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:O1');

		// header table
    	$table_head_columns  = array('No', 'MO', 'Tgl.MO', 'MC', 'Product', 'Sales Contract', 'MO Knitting', 'MC Knitting', 'Corak', 'Jenis Benang','Produksi Warping', 'Target', 'HPH/Qty1','HPH/Qty2', 'Sisa','Status');

    	$table_head_columns2 = array();
    	$column = 0;
    	$merge  = TRUE;
    	foreach ($table_head_columns as $field) {
    		
    		// merge cell baris ke 3-4
    		if($column < 10  ){
    			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $field);	
				$object->getActiveSheet()->mergeCellsByColumnAndRow($column, 3, $column, 4);
    		}

    		// merge cell BEAM
    		if($column >= 10 AND $column <=14){
    			if($column == 10 AND $merge == TRUE){
	    			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $field);	
					$object->getActiveSheet()->mergeCells('K3:N3');
    			}elseif($merge == false){
	    			$object->getActiveSheet()->setCellValueByColumnAndRow($column-1, 4, $field);	
    			}

    			$merge = FALSE;
    		}
    		
    		if($column == 15){
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

		$object->getActiveSheet()->getStyle('E4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    	// align center column ke J3,O3
		$object->getActiveSheet()->getStyle('J3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$object->getActiveSheet()->getStyle('O3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// set column
		$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');

		$object->getActiveSheet()->getStyle("A1:S4")->getFont()->setBold(true);
		$object->getSheet(0)->getColumnDimension('A')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('B')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('D')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('C')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('D')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('E')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('F')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('G')->SetWidth(14);
		$object->getSheet(0)->getColumnDimension('H')->SetWidth(14);
		$object->getSheet(0)->getColumnDimension('I')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('J')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('K')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('L')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('M')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('N')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('O')->setAutoSize(true);
	

		// set border header
		foreach ($index_header as $val) {
		
			$object->getActiveSheet()->getStyle($val.'3')->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray);
			//$object->getActiveSheet()->getStyle($val.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$object->getActiveSheet()->getStyle($val.'3:'.$val.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);


		}

		// body table
		$id_dept = 'WRP';
        $body = $this->m_produksiWarpingPanjang->get_list_produksi_wrp_by_dept($id_dept,$where);
        $no   = 1;
    	$rowCount = 5;

	   	$sales_contract = '';
	    $mo_knitting 	= '';
	    $mc_knitting    = '';
	    $corak          = '';
	    $jns_bng        = '';

    	foreach ($body as $row) {

    		$ex  =  explode('|', $row->reff_note);
        	$i   = 0;
        	foreach($ex as $exs){

	        	if($i == 0){
	        		$sales_contract = trim($exs);
	        	}
	        	if($i == 1){
	        		$mo_knitting    = trim($exs);
	        	}
	        	if($i == 2){
	        		$mc_knitting    = trim($exs);
	        	}
	        	if($i == 3){
	        		$corak          = trim($exs);
	        	}
	        	if($i == 4){
	        		$jns_bng        = trim($exs);
	        	}
	       
	        	$i++;
        	}

			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($no));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, mb_strtoupper($row->kode,'UTF-8'));
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, tgl_indo2(date('d-m-y', strtotime($row->tanggal))));
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, mb_strtoupper($row->nama_mesin,'UTF-8'));
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row->nama_produk);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, mb_strtoupper($sales_contract,'UTF-8'));
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $mo_knitting);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $mc_knitting);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $corak);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $jns_bng);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->qty_target);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $row->hph_qty1);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $row->hph_qty2);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $row->sisa_target);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $row->status);


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
			$object->getActiveSheet()->getStyle('M'.$rowCount.':N'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray);

			$no=$no+1;
			$rowCount++;

			$sales_contract = '';
		    $mo_knitting 	= '';
		    $mc_knitting    = '';
		    $corak          = '';
		    $jns_bng        = '';
    	}


        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = "Jadwal Produksi Warping Panjang.xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);

		die(json_encode($response));


    }


}


