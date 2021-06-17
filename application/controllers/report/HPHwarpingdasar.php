<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class HPHwarpingdasar extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        $this->load->model('m_HPHwarpingdasar');
	}

	public function index()
	{

		$id_dept        = 'HPHWRD';
        $data['id_dept']= $id_dept;

		$this->load->view('report/v_hph_warping_dasar', $data);
	}

	public function loadData()
	{

		$tgldari   = $this->input->post('tgldari');
		$tglsampai = $this->input->post('tglsampai');
		$nama_produk = $this->input->post('nama_produk');
		$mc        = $this->input->post('mc');
		$lot       = $this->input->post('lot');
		$user      = $this->input->post('user');
		$jenis     = $this->input->post('jenis');
		$shift_arr = $this->input->post('shift');// array shift pagi/siang/malam
		$id_dept   = 'WRD';
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

			if(!empty($nama_produk)){
				$where_nama  = "AND mpfg.nama_produk LIKE '%".addslashes($nama_produk)."%' ";
			}else{
				$where_nama  = '';
			}

			if(!empty($user)){
				$where_user  = "AND mpfg.nama_user LIKE '%".addslashes($user)."%' ";
			}else{
				$where_user  = '';
			}

			$dataRecord= [];

			$where     = "WHERE mp.dept_id = '".$id_dept."' AND ".$where_date." ".$where_mc." ".$where_lot." ".$where_nama." ".$where_user." ".$where_jenis."  ";

			$items = $this->m_HPHwarpingdasar->get_list_HPH_by_dept($where);
			foreach ($items as $val) {
				$dataRecord[] = array('kode' => $val->kode,
									  'nama_mesin' => $val->nama_mesin,
									  'origin'     => $val->origin,
									  'tgl_hph'    => $val->tgl_hph,
									  'kode_produk'=> $val->kode_produk,
									  'nama_produk'=> $val->nama_produk,
									  'lot'        => $val->lot,
									  'qty1'       => $val->qty,
									  'uom1'	   => $val->uom,
									  'qty2'	   => $val->qty2,
									  'uom2'       => $val->uom2,
									  'nama_user'  => $val->nama_user,
									  'reff_note'  => $val->reff_note,
									  'lokasi'     => $val->lokasi
									);
			}

			$allcount           = $this->m_HPHwarpingdasar->getRecordCountHPH($where);
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
		$nama_produk = $this->input->post('nama_produk');
		$mc        = $this->input->post('mc');
		$lot       = $this->input->post('lot');
		$user      = $this->input->post('user');
		$jenis     = $this->input->post('jenis');
		$shift_arr = $this->input->post('shift[]');
		$id_dept   = 'WRD';
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
		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// set Departemen
 		$object->getActiveSheet()->SetCellValue('A2', 'Departemen');
		$object->getActiveSheet()->mergeCells('A2:B2');
 		$object->getActiveSheet()->SetCellValue('C2', ': Warping Dasar');
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
		$object->getActiveSheet()->getStyle("A1:N6")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	


		// header table
    	$table_head_columns  = array('No', 'MO', 'No Mesin', 'Origin', 'Tgl HPH', 'Kode Produk', 'Nama Produk', 'Lot', 'Qty1', 'Uom1','Qty2', 'Uom2','Reff Note','Lokasi');


    	$column = 0;
    	$merge  = TRUE;
    	foreach ($table_head_columns as $field) {

    		$object->getActiveSheet()->setCellValueByColumnAndRow($column, 6, $field);	
    		$column++;
    	}

    	// set lebar column header
		$index_header = array('A','B','D','E');
		
		foreach ($index_header as $val) {
			# code...
			$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true);
		}
			$object->getSheet(0)->getColumnDimension('C')->SetWidth(12);
			$object->getSheet(0)->getColumnDimension('F')->SetWidth(17);
			$object->getSheet(0)->getColumnDimension('G')->SetWidth(42);
			$object->getSheet(0)->getColumnDimension('H')->SetWidth(20);
			$object->getSheet(0)->getColumnDimension('I')->SetWidth(10);
			$object->getSheet(0)->getColumnDimension('J')->SetWidth(9);
			$object->getSheet(0)->getColumnDimension('K')->SetWidth(10);
			$object->getSheet(0)->getColumnDimension('L')->SetWidth(9);
			$object->getSheet(0)->getColumnDimension('M')->SetWidth(15);
			$object->getSheet(0)->getColumnDimension('N')->SetWidth(12);

		// set border header column
    	$index_header2 = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
    	foreach ($index_header2 as $val) {
			$object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);
           	
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

		if(!empty($nama_produk)){
			$where_nama  = "AND mpfg.nama_produk LIKE '%".addslashes($nama_produk)."%' ";
		}else{
			$where_nama  = '';
		}

		if(!empty($user)){
			$where_user  = "AND mpfg.nama_user LIKE '%".addslashes($user)."%' ";
		}else{
			$where_user  = '';
		}

    	// tbody
		$where = "WHERE mp.dept_id = '".$id_dept."' AND ".$where_date." ".$where_mc." ".$where_lot." ".$where_nama." ".$where_user." ".$where_jenis."  ";
		$items = $this->m_HPHwarpingdasar->get_list_HPH_by_dept($where);
		$no    = 1;
		$rowCount = 7;
    	foreach ($items as $row) {
    		
    		if($rowCount > 6){

	    		$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($no++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, mb_strtoupper($row->kode,'UTF-8'));
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $row->nama_mesin);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $row->origin);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row->tgl_hph);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $row->kode_produk);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $row->nama_produk);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row->lot);
				$object->getActiveSheet()->SetCellValue('I'.$rowCount, $row->qty);
				$object->getActiveSheet()->SetCellValue('J'.$rowCount, $row->uom);
				$object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->qty2);
				$object->getActiveSheet()->SetCellValue('L'.$rowCount, $row->uom2);
				$object->getActiveSheet()->SetCellValue('M'.$rowCount, $row->reff_note);
				$object->getActiveSheet()->SetCellValue('N'.$rowCount, $row->lokasi);

    		}
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

			
			$rowCount++;

    	}

        $object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="HPH Warping Dasar.xls"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');
			
	}

	
}