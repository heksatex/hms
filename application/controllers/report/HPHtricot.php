<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class HPHtricot extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        $this->load->model('m_HPHtricot');
        $this->load->model('m_produksiTricot');
	}


	public function index()
	{
		$id_dept        = 'HPHTRI';
        $data['id_dept']= $id_dept;

		$this->load->view('report/v_hph_tricot', $data);
	}

	function loadData()
	{

		$tgldari   = $this->input->post('tgldari');
		$tglsampai = $this->input->post('tglsampai');
		$corak     = $this->input->post('corak');
		$mc        = $this->input->post('mc');
		$lot       = $this->input->post('lot');
		$user      = $this->input->post('user');
		$jenis     = $this->input->post('jenis');
		$shift_arr = $this->input->post('shift');// array shift pagi/siang/malam
		$id_dept   = 'TRI';
		$where_date = '';
		$loop       = 1;
		$condition_OR = '';
        	
       	// cari selisih periode tangal
        $diff    = strtotime($tglsampai) - strtotime($tgldari);
        $hasil   = floor($diff / (60 * 60 * 24));
      
		// cek tgl dari dan tgl sampai
		if(strtotime($tglsampai) < strtotime($tgldari) ){
			$callback = array('status' => 'failed', 'message' => 'Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );  

		}else if(count($shift_arr) > 0 AND $hasil > 30){ // cek maksimal 30 hari  jika shift di ceklis 
			$callback = array('status' => 'failed', 'message' => 'Maaf, Jika Shift di Ceklist (v) maka Periode Tanggal tidak boleh lebih dari 30 hari !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 

		}else{

			if(count($shift_arr) > 0){

				$tgldari    = date('Y-m-d', strtotime($tgldari));
				$tglsampai  = date('Y-m-d', strtotime($tglsampai));
				$i = 0;
				while($i<=30){

					$tgldari_    = strtotime($tgldari);
					$tglsampai_  = strtotime($tglsampai);

					foreach ($shift_arr as $val) {
						if($loop > 1){
							$condition_OR = ' OR ';
						}
						# code...
						if($val == 'Pagi'){
							$jam_dari    = '07:00:00';
							$jam_sampai  = '14:29:59';
						}else if($val == 'Siang'){
							$jam_dari    = '14:30:00';
							$jam_sampai  = '22:59:59';
						}else if($val == 'Malam'){
							$jam_dari    = '23:00:00';
							$jam_sampai  = '06:59:59';
						}

						if($val == 'Malam'){
							$tglsampai_2 = date('Y-m-d', strtotime('+1 day',$tgldari_));
						}else{
							$tglsampai_2 = $tgldari;
						}

						$tgldari_2 = $tgldari;

						$where_date .= $condition_OR." ( mpfg.create_date >='".$tgldari_2." ".$jam_dari."' AND mpfg.create_date <='".$tglsampai_2." ".$jam_sampai."' ) ";
						$loop++;
					}
		

					if($tgldari_ == $tglsampai_){
						break;
					}else{
						if($loop == 2){
							$where_date = $where_date.' OR ';
						}
						$tgldari = date('Y-m-d', strtotime('+1 day',$tgldari_));
					}

					$loop = 1;
					$i++;
				}

				if(count($shift_arr) == 1){
					$where_date = rtrim($where_date, ' OR ');
				}

				$where_date = '( '.$where_date.' )';

			}else{
				$tgldari    = date('Y-m-d H:i:s', strtotime($tgldari));
				$tglsampai  = date('Y-m-d H:i:s', strtotime($tglsampai));

				$where_date  = "( mpfg.create_date >= '".$tgldari."' AND mpfg.create_date <= '".$tglsampai."') ";
			}


			// get location by jenis (HPH=stock, Waste)
			$cek = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();

			if($jenis == 'HPH'){
				$where_jenis = "AND mpfg.lokasi = '".$cek['stock_location']."' ";
			}else if($jenis == 'Waste'){
				$where_jenis = "AND mpfg.lokasi = '".$cek['waste_location']."' ";
			}else{
				$where_jenis = '';
			}


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

			$dataRecord= [];

			$lbr_jadi       = '';
	        $lbr_greige     = '';
	        $stitch         = '';
	        $rpm            = '';

			$where     = "WHERE mp.dept_id = '".$id_dept."' AND ".$where_date." ".$where_mc." ".$where_lot." ".$where_corak." ".$where_user." ".$where_jenis." ";

			$items = $this->m_HPHtricot->get_list_HPH_tricot_by_kode($where);
			foreach ($items as $val) {

				// explode origin 
				$exp   = explode('|', $val->origin);
				$no    = 0;
				foreach ($exp as $exps) {
					if($no == 0){
						$sc  = trim($exps);
						$mkt = $this->m_produksiTricot->get_marketing_by_kode($sc);
					}
					$no++;
				}

				// explode reff_note
				$exp2  = explode('|', $val->reff_note);
				$a     = 0;
				foreach ($exp2 as $exps2) {
					# code...
					if($a == 7 ){// l.greige
	                    $ex2 = explode('=', $exps2);
	                    $lbr_greige = trim($ex2[1]);
	                }
	                if($a == 8){ // l.jadi
	                    $ex2 = explode('=', $exps2);
	                    $lbr_jadi = trim($ex2[1]);
	                }

	                if($a == 11){ // stitch
	                    $ex2 = explode('=', $exps2);
	                    $stitch = trim($ex2[1]);
	                }
	                if($a == 13){ // rpm
	                    $ex2 = explode('=', $exps2);
	                    $rpm = trim($ex2[1]);
	                }
	                $a++;
				}


				$dataRecord[] = array('kode' => $val->kode,
									  'nama_mesin' => $val->nama_mesin,
									  'sc'     => $sc,
									  'tgl_hph'    => $val->tgl_hph,
									  'kode_produk'=> $val->kode_produk,
									  'nama_produk'=> $val->nama_produk,
									  'lot'        => $val->lot,
									  'qty1'       => $val->qty,
									  'uom1'	   => $val->uom,
									  'qty2'	   => $val->qty2,
									  'uom2'       => $val->uom2,
									  'grade'      => $val->nama_grade,
									  'lbr_greige' => $lbr_greige,
									  'lbr_jadi'   => $lbr_jadi,
									  'rpm'        => $rpm,
									  'stitch'     => $stitch,
									  'marketing'  => $mkt,
									  'nama_user'  => $val->nama_user,
									  'reff_note'  => $val->reff_note_sq,
									  'lokasi'     => $val->lokasi
									);
				$lbr_jadi       = '';
		        $lbr_greige     = '';
		        $stitch         = '';
		        $rpm            = '';
			}

			$allcount           = $this->m_HPHtricot->get_record_hph_tricot($where);
	        $total_record       = 'Total Data : '. number_format($allcount);

			$callback = array('record' => $dataRecord, 'total_record' => $total_record);

		} //else if validasi

		echo json_encode($callback);
	}

	public function export_excel_hph()
	{
		
		$this->load->library('excel');
		$tgldari   = $this->input->post('tgldari');
		$tglsampai = $this->input->post('tglsampai');
		$corak     = $this->input->post('corak');
		$mc        = $this->input->post('mc');
		$lot       = $this->input->post('lot');
		$user      = $this->input->post('user');
		$jenis     = $this->input->post('jenis');
		$shift_arr = $this->input->post('shift[]');
		$id_dept   = 'TRI';
		$where_date = '';
		$loop       = 1;
		$condition_OR = '';

		$tgldari_capt  = $this->input->post('tgldari');
		$tglsampai_capt = $this->input->post('tglsampai');

			if(count($shift_arr) > 0){

				$tgldari    = date('Y-m-d', strtotime($tgldari));
				$tglsampai  = date('Y-m-d', strtotime($tglsampai));
				$i = 0;
				while($i<=30){

					$tgldari_    = strtotime($tgldari);
					$tglsampai_  = strtotime($tglsampai);

					foreach ($shift_arr as $val) {
						if($loop > 1){
							$condition_OR = ' OR ';
						}
						# code...
						if($val == 'Pagi'){
							$jam_dari    = '07:00:00';
							$jam_sampai  = '14:29:59';
						}else if($val == 'Siang'){
							$jam_dari    = '14:30:00';
							$jam_sampai  = '22:59:59';
						}else if($val == 'Malam'){
							$jam_dari    = '23:00:00';
							$jam_sampai  = '06:59:59';
						}

						if($val == 'Malam'){
							$tglsampai_2 = date('Y-m-d', strtotime('+1 day',$tgldari_));
						}else{
							$tglsampai_2 = $tgldari;
						}

						$tgldari_2 = $tgldari;

						$where_date .= $condition_OR." ( mpfg.create_date >='".$tgldari_2." ".$jam_dari."' AND mpfg.create_date <='".$tglsampai_2." ".$jam_sampai."' ) ";
						$loop++;
					}
		

					if($tgldari_ == $tglsampai_){
						break;
					}else{
						if($loop == 2){
							$where_date = $where_date.' OR ';
						}
						$tgldari = date('Y-m-d', strtotime('+1 day',$tgldari_));
					}

					$loop = 1;
					$i++;
				}

				if(count($shift_arr) == 1){
					$where_date = rtrim($where_date, ' OR ');
				}

				$where_date = '( '.$where_date.' )';

			}else{
				$tgldari    = date('Y-m-d H:i:s', strtotime($tgldari));
				$tglsampai  = date('Y-m-d H:i:s', strtotime($tglsampai));

				$where_date  = "( mpfg.create_date >= '".$tgldari."' AND mpfg.create_date <= '".$tglsampai."') ";
			}


		
		// get location by jenis (HPH=stock, Waste)
		$cek = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();

		if($jenis == 'HPH'){
			$where_jenis = "AND mpfg.lokasi = '".$cek['stock_location']."' ";
		}else if($jenis == 'Waste'){
			$where_jenis = "AND mpfg.lokasi = '".$cek['waste_location']."' ";
		}else{
			$where_jenis = '';
		}
		

		$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan HPH');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');
		//$object->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// set Departemen
 		$object->getActiveSheet()->SetCellValue('A2', 'Departemen');
		$object->getActiveSheet()->mergeCells('A2:B2');
 		$object->getActiveSheet()->SetCellValue('C2', ': Tricot');
		$object->getActiveSheet()->mergeCells('C2:D2');


		// set periode
 		$object->getActiveSheet()->SetCellValue('A3', 'Periode');
		$object->getActiveSheet()->mergeCells('A3:B3');
 		$object->getActiveSheet()->SetCellValue('C3', ': '.tgl_indo(date('d-m-Y',strtotime($tgldari_capt))).' - '.tgl_indo(date('d-m-Y',strtotime($tglsampai_capt)) ));
		$object->getActiveSheet()->mergeCells('C3:F3');

		if(count($shift_arr) > 0 ){
			$caption_shift = '';
			foreach ($shift_arr as $val) {
				$caption_shift .= $val.', ' ;
			}

			$caption_shift = rtrim($caption_shift, ', ');
		}else{
			$caption_shift = 'All';
		}

		// shift 
		$object->getActiveSheet()->SetCellValue('A4', 'Shift');
		$object->getActiveSheet()->mergeCells('A4:B4');
		$object->getActiveSheet()->SetCellValue('C4', ': '.$caption_shift);
		$object->getActiveSheet()->mergeCells('C4:F4');


 		//bold huruf
		$object->getActiveSheet()->getStyle("A1:T7")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	


		// header table
    	$table_head_columns  = array('No', 'MO', 'No Mesin', 'SC', 'Tgl HPH', 'Kode Produk', 'Nama Produk', 'Lot', 'Qty1', 'Uom1','Qty2', 'Uom2','Grade','Lebar','Greige','Jadi','RPM','Stitch','Marketing','Reff Note','Lokasi');

    	$column = 0;
    	$merge  = TRUE;
    	$columns = '';
        $count_merge = 0; // untuk jml yg di merge
    	foreach ($table_head_columns as $field) {

    		if($column <= 12 OR $column >= 16){
    			$columns = $column-$count_merge;
	    		$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 6, $field);  
    	        $object->getActiveSheet()->mergeCellsByColumnAndRow($columns, 6, $columns, 7);
    		}

    		if($column >= 13 AND $column <= 15){
    			if($merge == true){
	    			$columns = $column;
		    		$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 6, $field);  
	                $object->getActiveSheet()->mergeCells('N6:O6');// merge cell lebar
	                $count_merge++;
    			}else if($merge == false){
  					$columns = $column-$count_merge;
                    $object->getActiveSheet()->setCellValueByColumnAndRow($columns, 6, $field);  
    			}
                
                $merge= false;
    		}

    	
    		$column++;
    	}

    	// set wraptext
        $object->getActiveSheet()->getStyle('F6:F'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 


    	// set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T');
    	$loop = 0;
    	foreach ($index_header as $val) {
    		
    		$object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);
            $object->getActiveSheet()->getStyle($val.'7')->applyFromArray($styleArray);

            if($loop <= 1 OR $loop == 7){
				$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A, B, 
            }else if($loop ==2){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(19); // index C
            }else if($loop == 4){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(12); // index E
            }else if($loop == 6 ){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(31); // index G
            }else if($loop == 5 OR  ($loop >= 8 AND $loop <= 16)){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(9); // index F, I - Q
            }else if($loop == 17){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(17); // index R
            }else if($loop == 18){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(18); // index S
            }else if($loop == 19){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(14); // index T
            }

           	$object->getActiveSheet()->getStyle($val.'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );

            $object->getActiveSheet()->getStyle($val.'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );


            $loop++;
    	}

        $rowCount = 8;
        $lbr_jadi       = '';
        $lbr_greige     = '';
        $stitch         = '';
        $rpm            = '';

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

    	//tbody
		$where     = "WHERE mp.dept_id = '".$id_dept."' AND ".$where_date." ".$where_mc." ".$where_lot." ".$where_corak." ".$where_user." ".$where_jenis." ";
    	$items = $this->m_HPHtricot->get_list_HPH_tricot_by_kode($where);
    	$num   = 1;
		foreach ($items as $val) {

			// explode origin 
			$exp   = explode('|', $val->origin);
			$no    = 0;
			foreach ($exp as $exps) {
				if($no == 0){
					$sc  = trim($exps);
					$mkt = $this->m_produksiTricot->get_marketing_by_kode($sc);
				}
				$no++;
			}

			// explode reff_note
			$exp2  = explode('|', $val->reff_note);
			$a     = 0;
			foreach ($exp2 as $exps2) {
				# code...
				if($a == 7 ){// l.greige
                    $ex2 = explode('=', $exps2);
                    $lbr_greige = trim($ex2[1]);
                }
                if($a == 8){ // l.jadi
                    $ex2 = explode('=', $exps2);
                    $lbr_jadi = trim($ex2[1]);
                }

                if($a == 11){ // stitch
                    $ex2 = explode('=', $exps2);
                    $stitch = trim($ex2[1]);
                }
                if($a == 13){ // rpm
                    $ex2 = explode('=', $exps2);
                    $rpm = trim($ex2[1]);
                }
                $a++;
			}

			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->kode);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->nama_mesin);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $sc);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->tgl_hph);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->kode_produk);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->nama_produk);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->qty);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->uom);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->qty2);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->uom2);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->nama_grade);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $lbr_greige);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $lbr_jadi);
			$object->getActiveSheet()->SetCellValue('P'.$rowCount, $rpm);
			$object->getActiveSheet()->SetCellValue('Q'.$rowCount, $stitch);
			$object->getActiveSheet()->SetCellValue('R'.$rowCount, $mkt);
			$object->getActiveSheet()->SetCellValue('S'.$rowCount, $val->reff_note_sq);
			$object->getActiveSheet()->SetCellValue('T'.$rowCount, $val->lokasi);

           	// set align
            $object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $object->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
            // set wrapText
            $object->getActiveSheet()->getStyle('C'.$rowCount.':C'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
            $object->getActiveSheet()->getStyle('E'.$rowCount.':E'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 


            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('P'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('Q'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('R'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('S'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('T'.$rowCount)->applyFromArray($styleArray);

		
			$lbr_jadi       = '';
	        $lbr_greige     = '';
	        $stitch         = '';
	        $rpm            = '';
	        $rowCount++;
		}


    	$object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="HPH Tricot.xls"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');
    }

}