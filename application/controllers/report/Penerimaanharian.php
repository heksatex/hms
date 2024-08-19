<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Penerimaanharian extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('m_inout');
		$this->load->model('_module');
	}

    public function index()
	{
		$id_dept        = 'RINH';
        $data['id_dept']= $id_dept;
		$this->load->view('report/v_penerimaan_harian', $data);
	}
    
	public function get_departement_select2()
	{
		$nama  = addslashes($this->input->post('nama'));
   		$callback = $this->m_inout->get_list_departement_select2($nama);
        echo json_encode($callback);
	}
    

	function loadData()
	{
		$tgldari     = date('Y-m-d H:i:s', strtotime($this->input->post('tgldari')));
		$tglsampai   = date('Y-m-d H:i:s', strtotime($this->input->post('tglsampai')));
		$departemen  = addslashes($this->input->post('departemen'));
		$dept_dari   = addslashes($this->input->post('dept_dari'));
		$kode        = addslashes($this->input->post('kode'));
		$corak  	 = addslashes($this->input->post('corak'));
		$status_arr  = $this->input->post('status_arr');
		$view_arr  	 = $this->input->post('view_arr');
		$dataRecord = [];

		$status      = '';
		foreach($status_arr as $val){
				$status .= "'".$val."', ";
		}
		$status = rtrim($status, ', ');// pb.status in ('done','ready')

		$view ='Global';
		foreach($view_arr as $val2){
			$view = $val2;
			break;
		}

		if($view == "Detail"){

			$list  = $this->m_inout->get_list_penerimaan_harian_by_kode($tgldari,$tglsampai,$departemen,$dept_dari,$status,$kode,$corak);
			$total = 0;
			foreach($list as $row){
				$kode_encrypt = encrypt_url($row->kode);
				$dataRecord[] = array('kode' 		=> $row->kode,
									'kode_enc'      => $kode_encrypt,
									'tgl_kirim' 	=> $row->tanggal_transaksi,
									'origin'		=> $row->origin,
									'reff_picking' 	=> $row->reff_picking,
									'kode_produk'	=> $row->kode_produk,
									'nama_produk'	=> $row->nama_produk,
									'lot'			=> $row->lot,
									'qty1'			=> number_format($row->qty,2).' '.$row->uom,
									'qty2'			=> number_format($row->qty2,2).' '.$row->uom2,
									'status'		=> $row->nama_status,
									'reff_note'		=> $row->reff_note,
									'in'           => 'Yes',
									'lot_adj'		=> $row->lot_adj

				);
				$total++;
			}

			$list  = $this->m_inout->get_list_pengiriman_harian_by_kode_get_in($tgldari,$tglsampai,$departemen,$dept_dari,$status,$kode,$corak);
			foreach($list as $row){
				$kode_encrypt = encrypt_url($row->kode);
				$dataRecord[] = array('kode' 		=> $row->kode,
									'kode_enc'      => $kode_encrypt,
									'tgl_kirim' 	=> $row->tanggal_transaksi,
									'origin'		=> $row->origin,
									'reff_picking' 	=> $row->reff_picking,
									'kode_produk'	=> $row->kode_produk,
									'nama_produk'	=> $row->nama_produk,
									'lot'			=> $row->lot,
									'qty1'			=> number_format($row->qty,2).' '.$row->uom,
									'qty2'			=> number_format($row->qty2,2).' '.$row->uom2,
									'status'		=> $row->nama_status,
									'reff_note'		=> $row->reff_note,
									'in'           => 'No',
									'lot_adj'		=> $row->lot_adj

				);
				$total++;
			}
		}else{
			$list  = $this->m_inout->get_list_penerimaan_harian_by_kode_group($tgldari,$tglsampai,$departemen,$dept_dari,$status,$kode,$corak);
			$total = 0;
			foreach($list as $row){
				$kode_encrypt = encrypt_url($row->kode);
				$dataRecord[] = array('kode' 		=> $row->kode,
									'kode_enc'      => $kode_encrypt,
									'tgl_kirim' 	=> $row->tanggal_transaksi,
									'origin'		=> $row->origin,
									'reff_picking' 	=> $row->reff_picking,
									'kode_produk'	=> $row->kode_produk,
									'nama_produk'	=> $row->nama_produk,
									'lot'			=> $row->tot_lot,
									'qty1'			=> number_format($row->tot_qty,2).' ',
									'qty2'			=> number_format($row->tot_qty2,2).' ',
									'status'		=> $row->nama_status,
									'reff_note'		=> $row->reff_note,
									'in'           => 'Yes',
									'lot_adj'		=> '',
									
				);
				$total++;
			}

			$list  = $this->m_inout->get_list_pengiriman_harian_by_kode_get_in_group($tgldari,$tglsampai,$departemen,$dept_dari,$status,$kode,$corak);
			foreach($list as $row){
				$kode_encrypt = encrypt_url($row->kode);
				$dataRecord[] = array('kode' 		=> $row->kode,
									'kode_enc'      => $kode_encrypt,
									'tgl_kirim' 	=> $row->tanggal_transaksi,
									'origin'		=> $row->origin,
									'reff_picking' 	=> $row->reff_picking,
									'kode_produk'	=> $row->kode_produk,
									'nama_produk'	=> $row->nama_produk,
									'lot'			=> $row->tot_lot,
									'qty1'			=> number_format($row->tot_qty,2).' ',
									'qty2'			=> number_format($row->tot_qty2,2).' ',
									'status'		=> $row->nama_status,
									'reff_note'		=> $row->reff_note,
									'in'           => 'No',
									'lot_adj'		=> '',
				);
				$total++;
			}

		}
	
		$callback = array('record' => $dataRecord, 'total_record' => 'Total Data : '.number_format($total), 'view' => $view);

		echo json_encode($callback);

	}

	function export_excel_in()
	{

		$this->load->library('excel');
		ob_start();
		$tgldari     = date('Y-m-d H:i:s', strtotime($this->input->post('tgldari')));
		$tglsampai   = date('Y-m-d H:i:s', strtotime($this->input->post('tglsampai')));
		$departemen  = addslashes($this->input->post('departemen'));
		$dept_dari   = addslashes($this->input->post('dept_dari'));
		$kode 		 = addslashes($this->input->post('kode'));
		$corak       = addslashes($this->input->post('corak'));
		$status_arr  = $this->input->post('status_arr');
		$view_arr  	 = $this->input->post('view_arr');
		$dataRecord  = [];

		$dept    = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
		$dept_dr = $this->_module->get_nama_dept_by_kode($dept_dari)->row_array();
		
		$status      = '';
		$status_capt = ''; // info status untuk di header
		foreach($status_arr as $val){
			$status .= "'".$val."',";
			$rs = $this->_module->get_mst_status_by_kode($val);
			$status_capt .= $rs.', ';
		}
		$status      = rtrim($status, ', ');// pb.status in ('done','ready')
		$status_capt = rtrim($status_capt, ', ');// Done,Ready;

		$view ='Global';
		foreach($view_arr as $val2){
			$view = $val2;
			break;
		}

		$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Penerimaan Harian');
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

		// set Departemen dari
		$object->getActiveSheet()->SetCellValue('A4', 'Dari');
		$object->getActiveSheet()->mergeCells('A4:B4');
		$object->getActiveSheet()->SetCellValue('C4', ': '.$dept_dr['nama']);
		$object->getActiveSheet()->mergeCells('C4:D4');

		// view
		$object->getActiveSheet()->SetCellValue('A5', 'View');
		$object->getActiveSheet()->mergeCells('A5:B5');
		$object->getActiveSheet()->SetCellValue('C5', ': '.$view);
		$object->getActiveSheet()->mergeCells('C5:D5');

		// Status
		$object->getActiveSheet()->SetCellValue('A6', 'Status');
		$object->getActiveSheet()->mergeCells('A6:B6');
		$object->getActiveSheet()->SetCellValue('C6', ': '.$status_capt);
		$object->getActiveSheet()->mergeCells('C6:D6');

		//bold huruf
		$object->getActiveSheet()->getStyle("A1:N8")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

		$styleArrayColor = array(
			'font'  => array(
				'bold'  => true,
				'color' => array('rgb' => 'FF0000'),
			),
			'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			)
	  	);	

		// header table
		if($view == 'Detail'){
			$table_head_columns  = array('No', 'kode','Tgl Kirim','Origin','Reff Picking','Kode Produk','Nama Produk','Lot','Qty1','Uom1','Qty2','Uom2','Status','Reff Note');
			$column = 0;
			foreach ($table_head_columns as $field) {
				$object->getActiveSheet()->setCellValueByColumnAndRow($column, 8, $field);  
				$column++;
			}
	
			// set width and border
			$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
			$loop = 0;
			foreach ($index_header as $val) {
				$object->getActiveSheet()->getStyle($val.'8')->applyFromArray($styleArray);
				if($loop == 0  OR $loop >=12){
					$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A,M,N
				}else if($loop == 5 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(15); // index F
				}else if( ($loop >= 8 AND $loop <=11 )){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(14); // index I,J,K,L
				}else if( $loop == 6 OR $loop ==4 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(25); // index G,E
				}else if($loop == 1 or  $loop == 2 OR $loop == 7 or $loop ==3){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(20); // index B,C,D,H
				}
	
				$loop++;
			}
		}else{
			$table_head_columns  = array('No', 'kode','Tgl Kirim','Origin','Reff Picking','Kode Produk','Nama Produk','Total Lot',' Total Qty1', 'Total Qty2','Status','Reff Note');
			$column = 0;
			foreach ($table_head_columns as $field) {
				$object->getActiveSheet()->setCellValueByColumnAndRow($column, 8, $field);  
				$column++;
			}
	
			// set width and border
			$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L');
			$loop = 0;
			foreach ($index_header as $val) {
				$object->getActiveSheet()->getStyle($val.'8')->applyFromArray($styleArray);
				if($loop == 0  OR $loop >=10){
					$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A,K,L
				}else if($loop == 5 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(15); // index F
				}else if( ($loop == 8 AND $loop <=9 )){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(14); // index I,J
				}else if( $loop == 6 OR $loop ==4 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(25); // index G,E
				}else if(($loop >= 1 AND  $loop <= 3) OR $loop == 7 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(20); // index B,C,D,H
				}
	
				$loop++;
			}
		}


		if($view == "Detail"){

			// tbody 1
			$list  = $this->m_inout->get_list_penerimaan_harian_by_kode($tgldari,$tglsampai,$departemen,$dept_dari,$status,$kode,$corak);
			$num   = 1;
			$rowCount = 9;
			foreach($list as $row){

				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $row->kode);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $row->tanggal_transaksi);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $row->origin);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row->reff_picking);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $row->kode_produk);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $row->nama_produk);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row->lot);
				$object->getActiveSheet()->SetCellValue('I'.$rowCount, $row->qty);
				$object->getActiveSheet()->SetCellValue('J'.$rowCount, $row->uom);
				$object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->qty2);
				$object->getActiveSheet()->SetCellValue('L'.$rowCount, $row->uom2);
				$object->getActiveSheet()->SetCellValue('M'.$rowCount, $row->nama_status);
				$object->getActiveSheet()->SetCellValue('N'.$rowCount, $row->reff_note);

				// set wrapText
				$object->getActiveSheet()->getStyle('B'.$rowCount.':B'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				$object->getActiveSheet()->getStyle('C'.$rowCount.':C'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				// $object->getActiveSheet()->getStyle('E'.$rowCount.':E'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				$object->getActiveSheet()->getStyle('G'.$rowCount.':G'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				$object->getActiveSheet()->getStyle('N'.$rowCount.':N'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 

				if($row->lot_adj != ''){
					$styleCell = $styleArrayColor;
				}else{
					$styleCell = $styleArray;
				}

				//set border true
				$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleCell);

				$rowCount++;
			}

			// tbody 2
			$list  = $this->m_inout->get_list_pengiriman_harian_by_kode_get_in($tgldari,$tglsampai,$departemen,$dept_dari,$status,$kode,$corak);
			foreach($list as $row){

				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $row->kode);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $row->tanggal_transaksi);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $row->origin);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row->reff_picking);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $row->kode_produk);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $row->nama_produk);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row->lot);
				$object->getActiveSheet()->SetCellValue('I'.$rowCount, $row->qty);
				$object->getActiveSheet()->SetCellValue('J'.$rowCount, $row->uom);
				$object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->qty2);
				$object->getActiveSheet()->SetCellValue('L'.$rowCount, $row->uom2);
				$object->getActiveSheet()->SetCellValue('M'.$rowCount, $row->nama_status);
				$object->getActiveSheet()->SetCellValue('N'.$rowCount, $row->reff_note);

				// set wrapText
				$object->getActiveSheet()->getStyle('B'.$rowCount.':B'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				$object->getActiveSheet()->getStyle('C'.$rowCount.':C'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				// $object->getActiveSheet()->getStyle('E'.$rowCount.':E'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				$object->getActiveSheet()->getStyle('G'.$rowCount.':G'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				$object->getActiveSheet()->getStyle('N'.$rowCount.':N'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 

				if($row->lot_adj != ''){
					$styleCell = $styleArrayColor;
				}else{
					$styleCell = $styleArray;
				}

				//set border true
				$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleCell);
				$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleCell);

				$rowCount++;
			}
		}else{
			// tbody 1
			$list  = $this->m_inout->get_list_penerimaan_harian_by_kode_group($tgldari,$tglsampai,$departemen,$dept_dari,$status,$kode,$corak);
			$num   = 1;
			$rowCount = 9;
			foreach($list as $row){

				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $row->kode);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $row->tanggal_transaksi);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $row->origin);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row->reff_picking);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $row->kode_produk);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $row->nama_produk);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row->tot_lot);
				$object->getActiveSheet()->SetCellValue('I'.$rowCount, $row->tot_qty);
				$object->getActiveSheet()->SetCellValue('J'.$rowCount, $row->tot_qty2);
				$object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->nama_status);
				$object->getActiveSheet()->SetCellValue('L'.$rowCount, $row->reff_note);

				// set wrapText
				$object->getActiveSheet()->getStyle('B'.$rowCount.':B'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				$object->getActiveSheet()->getStyle('C'.$rowCount.':C'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				// $object->getActiveSheet()->getStyle('E'.$rowCount.':E'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				$object->getActiveSheet()->getStyle('G'.$rowCount.':G'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 

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
				$rowCount++;
			}

			// tbody 2
			$list  = $this->m_inout->get_list_pengiriman_harian_by_kode_get_in_group($tgldari,$tglsampai,$departemen,$dept_dari,$status,$kode,$corak);
			foreach($list as $row){

				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $row->kode);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $row->tanggal_transaksi);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $row->origin);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row->reff_picking);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $row->kode_produk);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $row->nama_produk);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row->tot_lot);
				$object->getActiveSheet()->SetCellValue('I'.$rowCount, $row->tot_qty);
				$object->getActiveSheet()->SetCellValue('J'.$rowCount, $row->tot_qty2);
				$object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->nama_status);
				$object->getActiveSheet()->SetCellValue('L'.$rowCount, $row->reff_note);

				// set wrapText
				$object->getActiveSheet()->getStyle('B'.$rowCount.':B'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				$object->getActiveSheet()->getStyle('C'.$rowCount.':C'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				// $object->getActiveSheet()->getStyle('E'.$rowCount.':E'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
				$object->getActiveSheet()->getStyle('G'.$rowCount.':G'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 

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

				$rowCount++;
			}
		}

		$object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file ='Penerimaan Harian '.$dept['nama'].'.xlsx';
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

	}

}