<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Pengirimangreige extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
		$this->load->model('m_inout');
	}

    public function index()
	{
		$id_dept        = 'ROUTGRG';
        $data['id_dept']= $id_dept;
		$data['mst_sales_group'] = $this->_module->get_list_sales_group();
        $data['warehouse']  = $this->_module->get_list_departement();
		$this->load->view('report/v_pengiriman_greige', $data);
	}


	function loadData()
	{
		$tgldari     = date('Y-m-d H:i:s', strtotime($this->input->post('tgldari')));
		$tglsampai   = date('Y-m-d H:i:s', strtotime($this->input->post('tglsampai')));
		$departemen  = 'GRG';
		$dept_tujuan = $this->input->post('tujuan');
		$kode   		= addslashes($this->input->post('kode'));
		$warna   		= addslashes($this->input->post('warna'));
		$corak  		= addslashes($this->input->post('corak'));
		$sales_group  	= $this->input->post('sales_group');
		$view_arr  		= $this->input->post('view_arr');
		$status_arr 	= $this->input->post('status_arr');

		$dataRecord = [];

		$status      = '';
		foreach($status_arr as $val){
				$status .= "'".$val."',";
		}
		$status = rtrim($status, ', ');// pb.status in ('done','ready')

		$view ='Global';
		foreach($view_arr as $val2){
			$view = $val2;
			break;
		}

		if($view == 'Detail'){

			$list  = $this->m_inout->get_list_pengiriman_greige_by_kode($tgldari,$tglsampai,$departemen,$dept_tujuan,$status,$kode,$warna,$corak,$sales_group);
			$total = 0;
			foreach($list as $row){

				$nama_status = $this->_module->get_mst_status_by_kode($row->status);

				$kode_encrypt = encrypt_url($row->kode);
				$dataRecord[] = array('kode' 		=> $row->kode,
									'kode_enc'      => $kode_encrypt,
									'tgl_kirim' 	=> $row->tanggal_transaksi,
									'marketing'		=> $row->mkt,
									'origin'		=> $row->origin,
									'reff_picking' 	=> $row->reff_picking,
									'kode_produk'	=> $row->kode_produk,
									'nama_produk'	=> $row->nama_produk,
									'nama_warna'	=> $row->nama_warna,
									'lot'			=> $row->lot,
									'qty1'			=> number_format($row->qty,2).' '.$row->uom,
									'qty2'			=> number_format($row->qty2,2).' '.$row->uom2,
									'status'		=> $nama_status,
									'reff_note'		=> $row->reff_note,

				);
				$total++;
			}
		}else{

			$list  = $this->m_inout->get_list_pengiriman_greige_by_group($tgldari,$tglsampai,$departemen,$dept_tujuan,$status,$kode,$warna,$corak,$sales_group);
			$total = 0;
			foreach($list as $row){

				$nama_status = $this->_module->get_mst_status_by_kode($row->status);

				$kode_encrypt = encrypt_url($row->kode);
				$dataRecord[] = array('kode' 		=> $row->kode,
									'kode_enc'      => $kode_encrypt,
									'tgl_kirim' 	=> $row->tanggal_transaksi,
									'marketing'		=> $row->mkt,
									'origin'		=> $row->origin,
									'reff_picking' 	=> $row->reff_picking,
									'kode_produk'	=> $row->kode_produk,
									'nama_produk'	=> $row->nama_produk,
									'nama_warna'	=> $row->nama_warna,
									'lot'			=> $row->tot_lot,
									'qty1'			=> number_format($row->tot_qty,2),
									'qty2'			=> number_format($row->tot_qty2,2),
									'status'		=> $nama_status,
									'reff_note'		=> $row->reff_note,

				);
				$total++;
			}

		}

		$callback = array('record' => $dataRecord, 'total_record' => 'Total Data : '.number_format($total), 'view' => $view);

		echo json_encode($callback);

	}

	function export_excel()
	{

		$this->load->library('excel');

		$tgldari     = date('Y-m-d H:i:s', strtotime($this->input->post('tgldari')));
		$tglsampai   = date('Y-m-d H:i:s', strtotime($this->input->post('tglsampai')));
		$departemen  = 'GRG';
		$dept_tujuan = $this->input->post('tujuan');
		$kode   		= addslashes($this->input->post('kode'));
		$warna   		= addslashes($this->input->post('warna'));
		$corak  		= addslashes($this->input->post('corak'));
		$sales_group  	= $this->input->post('sales_group');
		$view_arr  		= $this->input->post('view[]');
		$status_arr 	= $this->input->post('status[]');

		$dataRecord = [];

		$status      = '';
		foreach($status_arr as $val){
				$status .= "'".$val."',";
		}
		$status = rtrim($status, ', ');// pb.status in ('done','ready')

		$view ='Global';
		foreach($view_arr as $val2){
			$view = $val2;
			break;
		}


		$dept = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
		$dept_tuj = $this->_module->get_nama_dept_by_kode($dept_tujuan)->row_array();
		
		$status      = '';
		$status_capt = ''; // info status untuk di header
		foreach($status_arr as $val){
			$status .= "'".$val."',";
			$rs = $this->_module->get_mst_status_by_kode($val);
			$status_capt .= $rs.', ';
		}
		$status      = rtrim($status, ', ');// pb.status in ('done','ready')
		$status_capt = rtrim($status_capt, ', ');// Done,Ready;

		$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Pengiriman ');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

		// set Periode tgl
		$object->getActiveSheet()->SetCellValue('A2', 'Tanggal');
		$object->getActiveSheet()->mergeCells('A2:B2');
 		$object->getActiveSheet()->SetCellValue('C2', ': '.tgl_indo(date('d-m-Y H:i:s',strtotime($tgldari))).' - '.tgl_indo(date('d-m-Y H:i:s',strtotime($tglsampai)) ));
		$object->getActiveSheet()->mergeCells('C2:H2');

		// set Departemen
 		$object->getActiveSheet()->SetCellValue('A3', 'Departemen');
		$object->getActiveSheet()->mergeCells('A3:B3');
 		$object->getActiveSheet()->SetCellValue('C3', ': '.$dept['nama']);
		$object->getActiveSheet()->mergeCells('C3:D3');

		// set Departemen tujuan
		$object->getActiveSheet()->SetCellValue('A4', 'Tujuan');
		$object->getActiveSheet()->mergeCells('A4:B4');
		$object->getActiveSheet()->SetCellValue('C4', ': '.$dept_tuj['nama']);
		$object->getActiveSheet()->mergeCells('C4:D4');

		// Status
		$object->getActiveSheet()->SetCellValue('A5', 'View');
		$object->getActiveSheet()->mergeCells('A5:B5');
		$object->getActiveSheet()->SetCellValue('C5', ': '.$view);
		$object->getActiveSheet()->mergeCells('C5:D5');

		//bold huruf
		$object->getActiveSheet()->getStyle("A1:O7")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

		// header table
		if($view == 'Global'){
			$table_head_columns  = array('No', 'kode','Tgl Kirim','Origin','Marketing','Kode Produk','Nama Produk','Warna','Total Lot',' Total Qty1','Total Qty2','Status','Reff Note');

			$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M');
			$loop = 0;
			foreach ($index_header as $val) {
				$object->getActiveSheet()->getStyle($val.'7')->applyFromArray($styleArray);
				if($loop == 0  ){
					$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A,

				}else if(($loop >= 1 AND $loop <= 3)){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(19); // index B,C,D
				}else if( $loop == 5 AND ($loop >= 8 AND $loop <=11 )){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(14); // index F,I,J,K,L
				}else if($loop == 6 OR $loop == 7 or $loop == 12){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(25); // index G,H,M
				}else if($loop == 4 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(10); // index E
				}

				$loop++;
			}

		}else{
			$table_head_columns  = array('No', 'kode','Tgl Kirim','Origin','Marketing','Kode Produk','Nama Produk','Warna','Lot','Qty1','Uom1','Qty2','Uom2','Status','Reff Note');

			$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
			$loop = 0;
			foreach ($index_header as $val) {
				$object->getActiveSheet()->getStyle($val.'7')->applyFromArray($styleArray);
				if($loop == 0  ){
					$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A,

				}else if(($loop >= 1 AND $loop <= 3) OR $loop == 8){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(19); // index B,C,D
				}else if( $loop == 5 AND ($loop >= 9 AND $loop <=13 )){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(14); // index F,I,J,K,L
				}else if($loop == 6 OR $loop == 7 or $loop == 14){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(25); // index G,H,M
				}else if($loop == 4 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(10); // index E
				}

				$loop++;
			}

		
		}

    	$column = 0;
    	foreach ($table_head_columns as $field) {
	    	$object->getActiveSheet()->setCellValueByColumnAndRow($column, 7, $field);  
    		$column++;
    	}

    	// set width and border
		/*
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
		$loop = 0;
    	foreach ($index_header as $val) {
			$object->getActiveSheet()->getStyle($val.'7')->applyFromArray($styleArray);
			if($loop == 0  AND $loop >=12){
				$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A,M,N
			}else if(($loop == 3 OR $loop ==4) OR $loop == 7){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(19); // index D,E,H
			}else if( $loop == 2 OR $loop == 5 AND ($loop >= 8 AND $loop <=11 )){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(14); // index C,F,I,J,K,L,
			}else if($loop == 6){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(25); // index G
			}else if($loop == 1){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(10); // index B
			}

			$loop++;
		}
		*/

		if($view == 'Global'){

			// tbody
			$list  = $this->m_inout->get_list_pengiriman_greige_by_group($tgldari,$tglsampai,$departemen,$dept_tujuan,$status,$kode,$warna,$corak,$sales_group);
			$num   = 1;
			$rowCount = 8;
			foreach($list as $row){

				$nama_status = $this->_module->get_mst_status_by_kode($row->status);

				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $row->kode);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $row->tanggal_transaksi);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $row->origin);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row->mkt);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $row->kode_produk);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $row->nama_produk);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row->nama_warna);
				$object->getActiveSheet()->SetCellValue('I'.$rowCount, $row->tot_lot);
				$object->getActiveSheet()->SetCellValue('J'.$rowCount, $row->tot_qty);
				$object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->tot_qty2);
				$object->getActiveSheet()->SetCellValue('L'.$rowCount, $nama_status);
				$object->getActiveSheet()->SetCellValue('M'.$rowCount, $row->reff_note);

				//set border true
				$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);

				$rowCount++;
			}

		}else{

			// tbody
			$list  = $this->m_inout->get_list_pengiriman_greige_by_kode($tgldari,$tglsampai,$departemen,$dept_tujuan,$status,$kode,$warna,$corak,$sales_group);
			$num   = 1;
			$rowCount = 8;
			foreach($list as $row){

				$nama_status = $this->_module->get_mst_status_by_kode($row->status);

				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $row->kode);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $row->tanggal_transaksi);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $row->origin);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row->mkt);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $row->kode_produk);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $row->nama_produk);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row->nama_warna);
				$object->getActiveSheet()->SetCellValue('I'.$rowCount, $row->lot);
				$object->getActiveSheet()->SetCellValue('J'.$rowCount, $row->qty);
				$object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->uom);
				$object->getActiveSheet()->SetCellValue('L'.$rowCount, $row->qty2);
				$object->getActiveSheet()->SetCellValue('M'.$rowCount, $row->uom2);
				$object->getActiveSheet()->SetCellValue('N'.$rowCount, $nama_status);
				$object->getActiveSheet()->SetCellValue('O'.$rowCount, $row->reff_note);

				//set border true
				$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray);

				$rowCount++;
			}
		}

		
		$object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  

		$name_file ='Pengiriman '.$dept['nama'].'.xls';

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$name_file.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');

	}

}