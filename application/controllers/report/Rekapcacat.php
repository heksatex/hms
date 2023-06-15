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
	}

	public function index()
    {
    	$data['id_dept']    = 'RCCT';
        $data['list_grade'] = $this->_module->get_list_grade();
    	$this->load->view('report/v_rekap_cacat', $data);
    }

    public function get_departement_select2()
	{
		$nama  = addslashes($this->input->post('nama'));
   		$callback = $this->m_efisiensi->get_list_departement_select2($nama);
        echo json_encode($callback);
	}


	function loadData()
	{
		$tgldari   = date('Y-m-d 00:00:00',strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d 23:59:59',strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('id_dept');
		$corak     = $this->input->post('corak');
		$mc        = $this->input->post('mc');
		$lot       = $this->input->post('lot');
		$user      = $this->input->post('user');
		$grade     = $this->input->post('grade');

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

		if($grade == 'All'){
			$where_grade  = '';

		}else{
			$where_grade  = "AND mpfg.nama_grade = '".addslashes($grade)."' ";
		}

		$where     = "WHERE mp.dept_id = '".$id_dept."' AND mpfg.create_date >='".$tgldari."' AND mpfg.create_date <='".$tglsampai."' ".$where_mc." ".$where_lot." ".$where_corak." ".$where_user." ".$where_grade." ";

		$items    = $this->m_rekapCacat->get_list_rekap_cacat_by_dept($where);
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

		}

		$callback = array('record' => $dataRecord);

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

		if($grade == 'All'){
			$where_grade  = '';
		}else{
			$where_grade  = "AND mpfg.nama_grade = '".addslashes($grade)."' ";
		}

		$where     = "WHERE mp.dept_id = '".$id_dept."' AND mpfg.create_date >='".$tgldari."' AND mpfg.create_date <='".$tglsampai."' ".$where_mc." ".$where_lot." ".$where_corak." ".$where_user." ".$where_grade ." ";


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
    	$table_head_columns  = array('No', 'MO', 'No Mesin', 'SC', 'Tgl HPH', 'Kode Produk', 'Nama Produk', 'Lot', 'Qty1', 'Uom1','Qty2', 'Uom2', 'Grade', 'Point Cacat', 'Kode Cacat', 'Nama Cacat', 'Nama User');
		
		$column = 0;
    	$merge  = TRUE;
    	foreach ($table_head_columns as $field) {

    		$object->getActiveSheet()->setCellValueByColumnAndRow($column, 5, $field);	
    		$column++;
    	}

    	// index column
    	$index_column = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q');
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
		$items    = $this->m_rekapCacat->get_list_rekap_cacat_by_dept($where);
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
	    			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $lot);
	    			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $qty);
	    			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $uom);
	    			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $qty2);
	    			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $uom2);
	    			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $nama_grade);
	    			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->point_cacat);
	    			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->kode_cacat);
	    			$object->getActiveSheet()->SetCellValue('P'.$rowCount, $val->nama_cacat);
	    			$object->getActiveSheet()->SetCellValue('Q'.$rowCount, $val->nama_user);

	    			// wrap text
        			$object->getActiveSheet()->getStyle('C'.$rowCount.':C'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
        			$object->getActiveSheet()->getStyle('E'.$rowCount.':E'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
        			$object->getActiveSheet()->getStyle('G'.$rowCount.':G'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
					$object->getActiveSheet()->getStyle('P'.$rowCount.':P'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);
					
					// set align 
					$object->getActiveSheet()->getStyle('N'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);


        			// set border left
        			foreach ($index_column as $field) {
		                $object->getActiveSheet()->getStyle($field.$rowCount)->applyFromArray($styleArray_left);
        			}
	             	$object->getActiveSheet()->getStyle('R'.$rowCount)->applyFromArray($styleArray_left);
	                	

	    			$rowCount++;
					$loop2++;

				}// end looping cacat
				
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
		$name_file = "Rekap Caca ".$departemen.".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));
	}

}