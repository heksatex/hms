<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');
/**
 * 
 */
class Rekapcacat extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('m_rekapCacat');
		$this->load->model('_module');
		$this->load->library('pagination');
		$this->load->model('m_produk');
	}

	public function index()
    {
    	$data['id_dept']    = 'RCCT';
        $data['list_grade'] = $this->_module->get_list_grade();
		$data['jenis_kain'] = $this->m_produk->get_list_jenis_kain();
    	$this->load->view('report/v_rekap_cacat', $data);
    }

    public function get_departement_select2()
	{
		$nama  = addslashes($this->input->post('nama'));
   		$callback = $this->m_efisiensi->get_list_departement_select2($nama);
        echo json_encode($callback);
	}


	function loadData($record=0)
	{
		
        $recordPerPage = 100;
        if($record != 0){
           $record = ($record-1) * $recordPerPage;
        }
		
		$tgldari   = date('Y-m-d 00:00:00',strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d 23:59:59',strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('id_dept');
		$corak     = $this->input->post('corak');
		$mc        = $this->input->post('mc');
		$lot       = $this->input->post('lot');
		$user      = $this->input->post('user');
		$grade     = $this->input->post('grade');
		$jenis_kain     = $this->input->post('jenis_kain');
		$show_hph  = $this->input->post('show_hph');

		$dataRecord = [];
		$sc         = '';

		if(!empty($mc)){
			$where_mc  = "AND ms.nama_mesin LIKE '%".addslashes($mc)."%' ";
		}else{
			$where_mc  = '';
		}

		if(!empty($lot)){
			$where_lot  = "AND mpfg.lot LIKE '%".addslashes($lot)."%' ";
		}else{
			$where_lot  = '';
		}

		if(!empty($corak)){
			$where_corak  = "AND mpfg.nama_produk LIKE '%".addslashes($corak)."%' ";
		}else{
			$where_corak  = '';
		}

		if(!empty($user)){
			$where_user  = "AND mpfg.nama_user LIKE '%".addslashes($user)."%' ";
		}else{
			$where_user  = '';
		}

		if(!empty($grade)){
			$where_grade  = '';
			$list_grade  = '';
            foreach($grade as $gd){
                $list_grade .= "'$gd', ";
            }
            $list_grade = rtrim($list_grade, ', ');

            if(!empty($list_grade)){
                   
				$where_grade  = "AND mpfg.nama_grade IN (".$list_grade.") ";
            }
		}else{
			$where_grade  = '';
		}

		if(!empty($jenis_kain)){
			$where_jenis_kain = '';
			$list_jenis_kain  = '';
			foreach($jenis_kain as $jk){
				$list_jenis_kain .= "'$jk', ";
			}
			$list_jenis_kain = rtrim($list_jenis_kain, ', ');

			if(!empty($list_jenis_kain)){
				$where_jenis_kain = "AND mst.id_jenis_kain IN (".$list_jenis_kain.") ";
			}
		}else{
			$where_jenis_kain ='';
		}

		$where     = "WHERE mp.dept_id = '".$id_dept."' AND mpfg.create_date >='".$tgldari."' AND mpfg.create_date <='".$tglsampai."' ".$where_mc." ".$where_lot." ".$where_corak." ".$where_user." ".$where_grade." ".$where_jenis_kain;

		$items    = $this->m_rekapCacat->get_list_rekap_cacat_by_dept($where,$record,$recordPerPage,$show_hph);
		foreach ($items as $row) {
					
				// explode origin
				$exp = explode('|', $row->origin);
				$loop = 1;
				foreach ($exp as $exps) {
					# code...
					if($loop == 1){
						$sc = trim($exps);
					}
					$loop++;
				}

				$kode       = $row->kode;
				$nama_mesin = $row->nama_mesin;
				$sc         = $sc;
				$create_date = $row->create_date;
				$kode_produk = $row->kode_produk;
				$nama_produk = $row->nama_produk;
				$nama_jenis_kain = $row->nama_jenis_kain;
				$lot         = $row->lot;
				$qty         = $row->qty;
				$uom         = $row->uom;
				$qty2        = $row->qty2;
				$uom2        = $row->uom2;
				$nama_grade  = $row->nama_grade;

				// get list cacat
				$list_cacat = $this->m_rekapCacat->get_list_cacat_by_kode($row->kode,$row->lot,$row->quant_id);
				$loop =1;
				foreach ($list_cacat as $val) {
					# code...
					if($loop > 1){
						$kode       = '';
						$nama_mesin =  '';
						$sc         = '';
						$create_date = '';
						$kode_produk = '';
						$nama_produk = '';
						$nama_jenis_kain = '';
						$lot         = '';
						$qty         = '';
						$uom         = '';
						$qty2        = '';
						$uom2        = '';
						$nama_grade  = '';
					}
					$dataRecord[] =  array('kode' => $kode,
										   'nama_mesin' => $nama_mesin,
										   'sc'   => $sc,
										   'tgl_hph'    => $create_date,
										   'kode_produk'=> $kode_produk,
										   'nama_produk'=> $nama_produk,
										   'nama_jenis_kain'=> $nama_jenis_kain,
										   'lot'        => $lot,
										   'qty1'       => $qty,
										   'uom1'       => $uom,
										   'qty2'       => $qty2,
										   'uom2'       => $uom2,
										   'grade'      => $nama_grade,
										   'point_cacat'=> $val->point_cacat,
										   'kode_cacat' => $val->kode_cacat,
										   'nama_cacat' => $val->nama_cacat,
										   'nama_user'  => $val->nama_user
										   );
					$loop++;
				}

				if(empty($list_cacat) AND $show_hph == 'true'){
					$dataRecord[] =  array('kode' => $kode,
										   'nama_mesin' => $nama_mesin,
										   'sc'   		=> $sc,
										   'tgl_hph'    => $create_date,
										   'kode_produk'=> $kode_produk,
										   'nama_produk'=> $nama_produk,
										   'nama_jenis_kain'=> $nama_jenis_kain,
										   'lot'        => $lot,
										   'qty1'       => $qty,
										   'uom1'       => $uom,
										   'qty2'       => $qty2,
										   'uom2'       => $uom2,
										   'grade'      => $nama_grade,
										   'point_cacat'=> "",
										   'kode_cacat' => "",
										   'nama_cacat' => "",
										   'nama_user'  => "",
										   );
				}

		}

		$allcount           = $this->m_rekapCacat->get_count_list_rekap_cacat_by_dept($where,$show_hph);
        $total_record       = 'Total Data : '. number_format($allcount);

		$config['base_url']         = base_url().'report/rekapcacat/loadData';
        $config['use_page_numbers'] = TRUE;
        $config['total_rows']       = $allcount;
        $config['per_page']         = $recordPerPage;
        $config['num_links']        = 1;
        $config['next_link']        = '>';
        $config['prev_link']        = '<';
        $this->pagination->initialize($config);
        $pagination         = $this->pagination->create_links();

		$callback = array('record' => $dataRecord,'pagination'=>$pagination, 'total_record'=>$total_record);

		echo json_encode($callback);
	}



	function export_excel()
    {

    	$this->load->library('excel');
		$tgldari   = date('Y-m-d H:i:s',strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d 23:59:59',strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('id_dept');
		$dept      = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();

		$corak     = $this->input->post('corak');
		$mc        = $this->input->post('mc');
		$lot       = $this->input->post('lot');
		$user      = $this->input->post('user');
		$grade     = $this->input->post('grade');
		$show_hph  = $this->input->post('show_hph');

		if(!empty($mc)){
			$where_mc  = "AND ms.nama_mesin LIKE '%".addslashes($mc)."%' ";
		}else{
			$where_mc  = '';
		}

		if(!empty($lot)){
			$where_lot  = "AND mpfg.lot LIKE '%".addslashes($lot)."%' ";
		}else{
			$where_lot  = '';
		}

		if(!empty($corak)){
			$where_corak  = "AND mpfg.nama_produk LIKE '%".addslashes($corak)."%' ";
		}else{
			$where_corak  = '';
		}

		if(!empty($user)){
			$where_user  = "AND mpfg.nama_user LIKE '%".addslashes($user)."%' ";
		}else{
			$where_user  = '';
		}

		if(!empty($grade)){
			$where_grade  = '';
			$list_grade  = '';
            foreach($grade as $gd){
                $list_grade .= "'$gd', ";
            }
            $list_grade = rtrim($list_grade, ', ');

            if(!empty($list_grade)){
                   
				$where_grade  = "AND mpfg.nama_grade IN (".$list_grade.") ";
            }
		}else{
			$where_grade  = '';
		}

		if(!empty($jenis_kain)){
			$where_jenis_kain = '';
			$list_jenis_kain  = '';
			foreach($jenis_kain as $jk){
				$list_jenis_kain .= "'$jk', ";
			}
			$list_jenis_kain = rtrim($list_jenis_kain, ', ');

			if(!empty($list_jenis_kain)){
				$where_jenis_kain = "AND mst.id_jenis_kain IN (".$list_jenis_kain.") ";
			}
		}else{
			$where_jenis_kain ='';
		}

		$where     = "WHERE mp.dept_id = '".$id_dept."' AND mpfg.create_date >='".$tgldari."' AND mpfg.create_date <='".$tglsampai."' ".$where_mc." ".$where_lot." ".$where_corak." ".$where_user." ".$where_grade ." ".$where_jenis_kain;


		$object = new PHPExcel();
		ob_start();
    	$object->setActiveSheetIndex(0);

    	    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Cacat');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');
		//$object->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// set Departemen
 		$object->getActiveSheet()->SetCellValue('A2', 'Departemen');
		$object->getActiveSheet()->mergeCells('A2:B2');
 		$object->getActiveSheet()->SetCellValue('C2', ': '.$dept['nama']);
		$object->getActiveSheet()->mergeCells('C2:D2');


		// set periode
 		$object->getActiveSheet()->SetCellValue('A3', 'Periode');
		$object->getActiveSheet()->mergeCells('A3:B3');
 		$object->getActiveSheet()->SetCellValue('C3', ': '.tgl_indo(date('d-m-Y',strtotime($tgldari))).' - '.tgl_indo(date('d-m-Y',strtotime($tglsampai)) ));
		$object->getActiveSheet()->mergeCells('C3:F3');

 		//bold huruf
		$object->getActiveSheet()->getStyle("A1:Q5")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);

		// border left
		$styleArray_left = array(
              'borders' => array(
                'left' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
        );  

		// border top
        $styleArray_top = array(
              'borders' => array(
                'top' => array(
                  'style' => PHPExcel_Style_Border::BORDER_THIN
                )
              )
        );

    
		// header table
    	$table_head_columns  = array('No', 'MO', 'No Mesin', 'SC', 'Tgl HPH', 'Kode Produk', 'Nama Produk', 'Jenis Kain', 'Lot', 'Qty1', 'Uom1','Qty2', 'Uom2', 'Grade', 'Point Cacat', 'Kode Cacat', 'Nama Cacat', 'Nama User');
		
		$column = 0;
    	$merge  = TRUE;
    	foreach ($table_head_columns as $field) {

    		$object->getActiveSheet()->setCellValueByColumnAndRow($column, 5, $field);	
    		$column++;
    	}

    	// index column
    	$index_column = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R');
    	$indexKe  = 1;

    	foreach ($index_column as $val) {

           	// set border header 
            $object->getActiveSheet()->getStyle($val.'5')->applyFromArray($styleArray);

    		# code...
    		if($indexKe  < 3 ){ // index A, B,
				$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true);
    		}
    		if($indexKe == 3){// index C
				$object->getSheet(0)->getColumnDimension($val)->setWidth(30);
    		}
    		if($indexKe == 4  OR ($indexKe >= 9 AND $indexKe <= 13)){// index D, F, I-M
				$object->getSheet(0)->getColumnDimension($val)->setWidth(10);
    		}
    		if($indexKe == 5){ // index E
				$object->getSheet(0)->getColumnDimension($val)->setWidth(12);
    		}
    		if($indexKe == 6 OR $indexKe == 14 OR $indexKe == 15){ // index N,O
				$object->getSheet(0)->getColumnDimension($val)->setWidth(12);
    		}
    		if($indexKe == 16 OR $indexKe == 7 OR $indexKe == 17){ // index G,P,Q
				$object->getSheet(0)->getColumnDimension($val)->setWidth(26);
    		}
    		if($indexKe == 8){ // index H
				$object->getSheet(0)->getColumnDimension($val)->setWidth(20);
    		}

    		$indexKe++;
    	}


    	// tbody
		$items    = $this->m_rekapCacat->get_list_rekap_cacat_by_dept_no_limit($where,$show_hph);
		$number   = 1;
		$rowCount = 6;
    	foreach ($items as $row) {
    		
    		// explode origin
				$exp = explode('|', $row->origin);
				$loop = 1;
				foreach ($exp as $exps) {
					# code...
					if($loop == 1){
						$sc = trim($exps);
					}
					$loop++;
				}

				$kode       = $row->kode;
				$nama_mesin = $row->nama_mesin;
				$sc         = $sc;
				$create_date = $row->create_date;
				$kode_produk = $row->kode_produk;
				$nama_produk = $row->nama_produk;
				$nama_jenis_kain = $row->nama_jenis_kain;
				$lot         = $row->lot;
				$qty         = $row->qty;
				$uom         = $row->uom;
				$qty2        = $row->qty2;
				$uom2        = $row->uom2;
				$nama_grade  = $row->nama_grade;

				// get list cacat
				$list_cacat = $this->m_rekapCacat->get_list_cacat_by_kode($row->kode,$row->lot,$row->quant_id);
				$loop2 = 1;
				$cacat = false;

				foreach ($list_cacat as $val) {

					$cacat = true;
					$num  = $number;
					if($loop2 > 1){
						$num = '';
						$kode       = '';
						$nama_mesin =  '';
						$sc         = '';
						$create_date = '';
						$kode_produk = '';
						$nama_produk = '';
						$nama_jenis_kain = '';
						$lot         = '';
						$qty         = '';
						$uom         = '';
						$qty2        = '';
						$uom2        = '';
						$nama_grade  = '';
					}

	    			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num));
	    			$object->getActiveSheet()->SetCellValue('B'.$rowCount, ($kode));
	    			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $nama_mesin);
	    			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $sc);
	    			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $create_date);
	    			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $kode_produk);
	    			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $nama_produk);
	    			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $nama_jenis_kain);
	    			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $lot);
	    			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $qty);
	    			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $uom);
	    			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $qty2);
	    			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $uom2);
	    			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $nama_grade);
	    			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->point_cacat);
	    			$object->getActiveSheet()->SetCellValue('P'.$rowCount, $val->kode_cacat);
	    			$object->getActiveSheet()->SetCellValue('Q'.$rowCount, $val->nama_cacat);
	    			$object->getActiveSheet()->SetCellValue('R'.$rowCount, $val->nama_user);

	    			// wrap text
        			$object->getActiveSheet()->getStyle('C'.$rowCount.':C'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
        			$object->getActiveSheet()->getStyle('E'.$rowCount.':E'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
        			$object->getActiveSheet()->getStyle('G'.$rowCount.':G'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
					$object->getActiveSheet()->getStyle('Q'.$rowCount.':Q'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
					
					// set align 
					$object->getActiveSheet()->getStyle('O'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


        			// set border left
        			foreach ($index_column as $field) {
		                $object->getActiveSheet()->getStyle($field.$rowCount)->applyFromArray($styleArray_left);
        			}
	             	$object->getActiveSheet()->getStyle('S'.$rowCount)->applyFromArray($styleArray_left);
	                	

	    			$rowCount++;
					$loop2++;

				}// end looping cacat

				if(empty($list_cacat)){ // lot yang tidak ada cacat

					$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($number++));
	    			$object->getActiveSheet()->SetCellValue('B'.$rowCount, ($kode));
	    			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $nama_mesin);
	    			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $sc);
	    			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $create_date);
	    			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $kode_produk);
	    			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $nama_produk);
	    			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $nama_jenis_kain);
	    			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $lot);
	    			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $qty);
	    			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $uom);
	    			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $qty2);
	    			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $uom2);
	    			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $nama_grade);
	    			$object->getActiveSheet()->SetCellValue('O'.$rowCount, "");
	    			$object->getActiveSheet()->SetCellValue('P'.$rowCount, "");
	    			$object->getActiveSheet()->SetCellValue('Q'.$rowCount, "");
	    			$object->getActiveSheet()->SetCellValue('R'.$rowCount, "");

	    			// wrap text
        			$object->getActiveSheet()->getStyle('C'.$rowCount.':C'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
        			$object->getActiveSheet()->getStyle('E'.$rowCount.':E'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
        			$object->getActiveSheet()->getStyle('G'.$rowCount.':G'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
					$object->getActiveSheet()->getStyle('Q'.$rowCount.':Q'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
					
					// set align 
					$object->getActiveSheet()->getStyle('O'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


        			// set border left
        			foreach ($index_column as $field) {
		                $object->getActiveSheet()->getStyle($field.$rowCount)->applyFromArray($styleArray_left);
        			}
	             	$object->getActiveSheet()->getStyle('S'.$rowCount)->applyFromArray($styleArray_left);
	                	
	    			$rowCount++;
				}
				
				if($cacat == true){
					$number = $number + 1;
				}

                //set  border top
                foreach ($index_column as $field) {
	                $object->getActiveSheet()->getStyle($field.$rowCount)->applyFromArray($styleArray_top);
                }

    	}// end looping mrp_cacat

		$object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$departemen = $dept['nama'];
		$name_file = "Rekap Cacat ".$departemen.".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));
	}

}