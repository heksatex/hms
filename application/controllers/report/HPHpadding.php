<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class HPHpadding extends MY_Controller
{
    public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        $this->load->model('m_HPHdf');
	}

    public function index()
	{
		$id_dept        = 'HPHPAD';
        $data['id_dept']= $id_dept;
		$data['mesin']  = $this->_module->get_list_mesin_report('PAD');
		$data['mst_sales_group'] = $this->_module->get_list_sales_group();
		$this->load->view('report/v_hph_padding', $data);
	}

    function loadData()
	{
		$tgldari   = $this->input->post('tgldari');
		$tglsampai = $this->input->post('tglsampai');
		$mo        = $this->input->post('mo');
		$corak     = $this->input->post('corak');
		$mc        = $this->input->post('mc');
		$lot       = $this->input->post('lot');
		$user      = $this->input->post('user');
		$jenis     = $this->input->post('jenis');
		$no_go     = $this->input->post('no_go');
		$warna     = $this->input->post('warna');
		$sales_group  = $this->input->post('sales_group');
		$sales_order  = $this->input->post('sales_order');
		$reproses  = $this->input->post('reproses');
		$shift_arr = $this->input->post('shift');// array shift pagi/siang/malam
		$id_dept   = 'PAD';
		$id_dept_r = 'PAD-R';
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
							$jam_sampai  = '14:59:59';
						}else if($val == 'Siang'){
							$jam_dari    = '15:00:00';
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
			$cek2 = $this->_module->get_nama_dept_by_kode($id_dept_r)->row_array();

			if($jenis == 'HPH'){
				$where_jenis = "AND (mpfg.lokasi = '".$cek['stock_location']."'  OR mpfg.lokasi = '".$cek2['stock_location']."')";
			}else if($jenis == 'Waste'){
				$where_jenis = "AND (mpfg.lokasi = '".$cek['waste_location']."' or mpfg.lokasi = '".$cek2['waste_location']."') ";
			}else{
				$where_jenis = '';
			}

			if(!empty($mo)){
				$where_mo  = "AND mpfg.kode LIKE '%".addslashes($mo)."%' ";
			}else{
				$where_mo  = '';
			}

			if(!empty($mc)){
				$where_mc  = "AND ms.nama_mesin = '".addslashes($mc)."' ";
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

			if(!empty($sales_order)){
				$where_sales_order  = "AND mpfg.sales_order LIKE '%".addslashes($sales_order)."%' ";
			}else{
				$where_sales_order  = '';
			}

			if(!empty($sales_group)){
				$where_sales_group  = "AND mpfg.sales_group LIKE '%".addslashes($sales_group)."%' ";
			}else{
				$where_sales_group  = '';
			}


			if(!empty($no_go)){
				$where_no_go  = "AND pb.kode LIKE '%".addslashes($no_go)."%' ";
			}else{
				$where_no_go  = '';
			}


			if(!empty($warna)){
				$where_warna  = "AND w.nama_warna  LIKE '%".addslashes($warna)."%' ";
			}else{
				$where_warna  = '';
			}
			
			if($reproses == 'false'){
				$where_reproses  = "AND  mp.dept_id = '".$id_dept."' ";
			}else{
				$where_reproses  = '';
			}

			$dataRecord= [];

			$lbr_jadi       = '';
	        $lbr_greige     = '';
	        $stitch         = '';
	        $rpm            = '';

			$where     = "WHERE mp.dept_id IN ('".$id_dept."', '".$id_dept_r."') AND ".$where_date." ".$where_mc." ".$where_lot." ".$where_corak." ".$where_user." ".$where_jenis." ".$where_mo."  ".$where_sales_order." ".$where_sales_group." ".$where_no_go." ".$where_warna." ".$where_reproses;


			$items = $this->m_HPHdf->get_list_HPH_df_by_kode($where);
			foreach ($items as $val) {

				$mkt = $val->nama_sales_group;
				$sc  = $val->sales_order;
				if($val->dept_id == $id_dept_r ){
					$reproses = 'Reproses';
				}else{
					$reproses = '';
				}
					
				$dataRecord[] = array('kode' 	   => $val->kode,
									  'nama_mesin' => $val->nama_mesin,
									  'sc'     	   => $sc,
									  'tgl_hph'    => $val->tgl_hph,
									  'kode_produk'=> $val->kode_produk,
									  'nama_produk'=> $val->nama_produk,
									  'lot'        => $val->lot,
									  'qty1'       => $val->qty,
									  'uom1'	   => $val->uom,
									  'qty2'	   => $val->qty2,
									  'uom2'       => $val->uom2,
									  'grade'      => $val->nama_grade,
									  'lbr_greige' => $val->lebar_greige.' '.$val->uom_lebar_greige,
									  'lbr_jadi'   => $val->lebar_jadi.' '.$val->uom_lebar_jadi,
									  'rpm'        => $rpm,
									  'stitch'     => $stitch,
									  'marketing'  => $mkt,
									  'nama_user'  => $val->nama_user,
									  'reff_note'  => $val->reff_note_sq,
									  'no_go'  	   => $val->no_go,
									  'lokasi'     => $val->lokasi,
									  'gl'		   => 1,
									  'nama_warna' => $val->nama_warna,
									  'keterangan' => $reproses
									);
				$lbr_jadi       = '';
		        $lbr_greige     = '';
		        $stitch         = '';
		        $rpm            = '';
			}

			$allcount           = $this->m_HPHdf->get_record_hph_df($where);
	        $total_record       = 'Total Data : '. number_format($allcount);

			$callback = array('record' => $dataRecord, 'total_record' => $total_record);

		} //else if validasi

		echo json_encode($callback);
	}

	public function export_excel_hph()
	{
		
		$this->load->library('excel');
		ob_start();
		$tgldari   = $this->input->post('tgldari');
		$tglsampai = $this->input->post('tglsampai');
		$mo        = $this->input->post('mo');
		$corak     = $this->input->post('corak');
		$mc        = $this->input->post('mc');
		$lot       = $this->input->post('lot');
		$user      = $this->input->post('user');
		$jenis     = $this->input->post('jenis');
		$no_go     = $this->input->post('no_go');
		$warna     = $this->input->post('warna');
		$sales_group  = $this->input->post('sales_group');
		$sales_order  = $this->input->post('sales_order');
		$reproses  = $this->input->post('reproses');
		$shift_arr = $this->input->post('shift');// array shift pagi/siang/malam
		$id_dept   = 'PAD';
		$id_dept_r = 'PAD-R';
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
							$jam_sampai  = '14:59:59';
						}else if($val == 'Siang'){
							$jam_dari    = '15:00:00';
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
		$cek2 = $this->_module->get_nama_dept_by_kode($id_dept_r)->row_array();

		if($jenis == 'HPH'){
			$where_jenis = "AND (mpfg.lokasi = '".$cek['stock_location']."'  OR mpfg.lokasi = '".$cek2['stock_location']."')";
		}else if($jenis == 'Waste'){
			$where_jenis = "AND (mpfg.lokasi = '".$cek['waste_location']."' or mpfg.lokasi = '".$cek2['waste_location']."') ";
		}else{
			$where_jenis = '';
		}
		

		$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan HPH');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

		// set Departemen
 		$object->getActiveSheet()->SetCellValue('A2', 'Departemen');
		$object->getActiveSheet()->mergeCells('A2:B2');
 		$object->getActiveSheet()->SetCellValue('C2', ': '.$cek['nama']);
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
		$object->getActiveSheet()->getStyle("A1:W7")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);

		$styleArray2 = array(
			'borders' => array(
			  'left' => array(
				'style' => PHPExcel_Style_Border::BORDER_THIN
			  )
			)
	  	);	


		// header table
		$table_head_columns  = array('No', 'Tgl HPH', 'Kode Produk', 'Nama Produk', 'Warna', 'SC', 'MG', 'No Greige Out', 'No Mesin', 'Lot','GL','Qty1', 'Uom1','Qty2','Uom2', 'Grade', 'Lebar', 'Greige','Jadi','Marketing','Reff Note','Lokasi','User','Keterangan');

    	$column = 0;
    	$merge  = TRUE;
    	$columns = '';
        $count_merge = 0; // untuk jml yg di merge
    	foreach ($table_head_columns as $field) {

    		if($column <= 15 OR $column >= 19){
    			$columns = $column-$count_merge;
	    		$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 6, $field);  
    	        $object->getActiveSheet()->mergeCellsByColumnAndRow($columns, 6, $columns, 7);
    		}

    		if($column >= 16 AND $column <= 18){
    			if($merge == true){
	    			$columns = $column;
		    		$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 6, $field);  
	                $object->getActiveSheet(0)->mergeCells('Q6:R6');// merge cell lebar
	                $count_merge++;
    			}else if($merge == false){
  					$columns = $column-$count_merge;
                    $object->getActiveSheet()->setCellValueByColumnAndRow($columns, 7, $field);  
    			}
                
                $merge= false;
    		}

			
    		$column++;
    	}


    	// set wraptext
		$object->getActiveSheet()->getStyle('C6:C'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 


    	// set wdith and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W');
    	$loop = 0;
    	foreach ($index_header as $val) {
    		
    		$object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);
            $object->getActiveSheet()->getStyle($val.'7')->applyFromArray($styleArray);

            if($loop <= 0 or $loop == 5 or $loop == 6 or $loop == 7 or $loop == 9){
				$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A,F,G,H,J
            }else if($loop == 1){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(20); // index B
            }else if($loop == 2){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(9); // index C
			}else if($loop == 3 or $loop == 4){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(40); // index D,E
			}else if($loop == 8){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(10); // index I
			}else if($loop >= 10 AND $loop <=17 ){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(10); // index K,L,M,N,O,P,Q
			}else if($loop >=18 ){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(18); // index R
            }

           	$object->getActiveSheet()->getStyle($val.'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );

            $loop++;
    	}

        $rowCount = 8;
        $lbr_jadi       = '';
        $lbr_greige     = '';
        $stitch         = '';
        $rpm            = '';

		if(!empty($mo)){
			$where_mo  = "AND mpfg.kode LIKE '%".addslashes($mo)."%' ";
		}else{
			$where_mo  = '';
		}

		if(!empty($mc)){
			$where_mc  = "AND ms.nama_mesin = '".addslashes($mc)."' ";
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

		if(!empty($sales_order)){
			$where_sales_order  = "AND mpfg.sales_order LIKE '%".addslashes($sales_order)."%' ";
		}else{
			$where_sales_order  = '';
		}

		if(!empty($sales_group)){
			$where_sales_group  = "AND mpfg.sales_group LIKE '%".addslashes($sales_group)."%' ";
		}else{
			$where_sales_group  = '';
		}

		if(!empty($no_go)){
			$where_no_go  = "AND pb.kode LIKE '%".addslashes($no_go)."%' ";
		}else{
			$where_no_go  = '';
		}


		if(!empty($warna)){
			$where_warna  = "AND w.nama_warna LIKE '%".addslashes($warna)."%' ";
		}else{
			$where_warna  = '';
		}

		if($reproses == 'false'){
			$where_reproses  = "AND  mp.dept_id = '".$id_dept."' ";
		}else{
			$where_reproses  = '';
		}


    	//tbody
		$where     = "WHERE mp.dept_id IN ('".$id_dept."', '".$id_dept_r."') AND ".$where_date." ".$where_mc." ".$where_lot." ".$where_corak." ".$where_user." ".$where_jenis." ".$where_mo."  ".$where_sales_order." ".$where_sales_group." ".$where_no_go." ".$where_warna." ".$where_reproses;
    	$items = $this->m_HPHdf->get_list_HPH_df_by_kode($where);
    	$num   = 1;
		$temp_mg 	= '';
		$sum_mg  	= 0;
		$sum_qty 	= 0;
		$sum_qty2 	= 0;
		foreach ($items as $val) {

			$mkt = $val->nama_sales_group;
			$sc  = $val->sales_order;
			if($val->dept_id == $id_dept_r ){
				$reproses = 'Reproses';
			}else{
				$reproses = '';
			}

			if($temp_mg != '' && $temp_mg != $val->kode){
				$this->total_group($object->getActiveSheet(),$temp_mg,$sum_mg,$sum_qty,$sum_qty2,$rowCount,$styleArray2);
				$sum_mg = 0;
				$sum_qty = 0;
				$sum_qty2 = 0;
				$num = 1;
				$rowCount++;
			}

			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->tgl_hph);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->kode_produk);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->nama_produk);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->nama_warna);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $sc);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->kode);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->no_go);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->nama_mesin);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, 1);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->qty);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->uom);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->qty2);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->uom2);
			$object->getActiveSheet()->SetCellValue('P'.$rowCount, $val->nama_grade);
			$object->getActiveSheet()->SetCellValue('Q'.$rowCount, $val->lebar_greige.' '.$val->uom_lebar_greige);
			$object->getActiveSheet()->SetCellValue('R'.$rowCount, $val->lebar_jadi.' '.$val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('S'.$rowCount, $mkt);
			$object->getActiveSheet()->SetCellValue('T'.$rowCount, $val->reff_note_sq);
			$object->getActiveSheet()->SetCellValue('U'.$rowCount, $val->lokasi);
			$object->getActiveSheet()->SetCellValue('V'.$rowCount, $val->nama_user);
			$object->getActiveSheet()->SetCellValue('W'.$rowCount, $reproses);

			// set align
			$object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $object->getActiveSheet()->getStyle('G'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $object->getActiveSheet()->getStyle('H'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $object->getActiveSheet()->getStyle('I'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $object->getActiveSheet()->getStyle('p'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            
          
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
			$object->getActiveSheet()->getStyle('U'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('V'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('W'.$rowCount)->applyFromArray($styleArray);

		
			$lbr_jadi       = '';
	        $lbr_greige     = '';
	        $stitch         = '';
	        $rpm            = '';
			$temp_mg 		= $val->kode;
            $sum_qty 		= $sum_qty + ($val->qty);
            $sum_qty2 		= $sum_qty2 + ($val->qty2);
            $sum_mg++;
	        $rowCount++;
		}

		if(!empty($items)){
			$this->total_group($object->getActiveSheet(),$temp_mg,$sum_mg,$sum_qty,$sum_qty2,$rowCount,$styleArray2);
		}

		$object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = "HPH ".$cek['nama'].".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));
    }

	function total_group($sheet,$mg,$sum_mg,$sum_qty,$sum_qty2,$rowCount,$styleArray2)
  	{ 

		$sheet->SetCellValue('A'.$rowCount, '');
		$sheet->SetCellValue('F'.$rowCount, 'TOTAL '.$mg);
		$sheet->mergeCells('F'.$rowCount.':G'.$rowCount);

		$sheet->SetCellValue('K'.$rowCount, $sum_mg);
		$sheet->SetCellValue('L'.$rowCount, $sum_qty);
		$sheet->SetCellValue('N'.$rowCount, $sum_qty2);
		$sheet->getStyle('A'.$rowCount)->applyFromArray($styleArray2);
		$sheet->getStyle('W'.$rowCount)->applyFromArray($styleArray2);

		$sheet->getStyle("A".$rowCount.":V".$rowCount)->getFont()->setBold(true);
     
      	return;
  	}

}