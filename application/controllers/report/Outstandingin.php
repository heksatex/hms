<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Outstandingin extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('m_outstandingIn');
        $this->load->model('_module');
	}

    public function index()
	{
		$id_dept        = 'OTSIN';
        $data['id_dept']= $id_dept;
        $data['warehouse']  = $this->_module->get_list_departement();
		$this->load->view('report/v_outstanding_in', $data);
	}

    function loadData()
	{
        $departemen     = addslashes($this->input->post('departemen'));
		$dept_dari		= addslashes($this->input->post('dept_dari'));
		$kode   		= addslashes($this->input->post('kode'));
		$corak  		= addslashes($this->input->post('corak'));
		$view_arr  		= $this->input->post('view_arr');

		$dataRecord = [];

		$view ='Global';
		foreach($view_arr as $val2){
			$view = $val2;
			break;
		}

		if($view == 'Detail'){

			$list  = $this->m_outstandingIn->get_list_outstanding_in_by_kode($departemen,$dept_dari,$kode,$corak);
			$total = 0;
			foreach($list as $row){

				$dataRecord[] = array('kode' 		=> $row->kode,
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

				);
				$total++;
			}
		}else{

			$list  = $this->m_outstandingIn->get_list_outstanding_in_by_kode_group($departemen,$dept_dari,$kode,$corak);
			$total = 0;
			foreach($list as $row){

				$kode_encrypt = encrypt_url($row->kode);
				$dataRecord[] = array('kode' 		=> $row->kode,
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

				);
				$total++;
			}

		}

		$callback = array('record' => $dataRecord, 'total_record' => 'Total Data : '.$total, 'view' => $view);

		echo json_encode($callback);

	}

    function export_excel()
	{

		$this->load->library('excel');

		
		$departemen     = addslashes($this->input->post('departemen'));
		$dept_dari      = addslashes($this->input->post('dept_dari'));
		$kode   		= addslashes($this->input->post('kode'));
		$corak  		= addslashes($this->input->post('corak'));
		$view_arr  		= $this->input->post('view[]');

		$view ='Global';
		foreach($view_arr as $val2){
			$view = $val2;
			break;
		}

		$dept      = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
		$dept_dari = $this->_module->get_nama_dept_by_kode($dept_dari)->row_array();
		
		$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Outstanding IN ');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

		// set Departemen
		$object->getActiveSheet()->SetCellValue('A2', 'Departemen');
		$object->getActiveSheet()->mergeCells('A2:B2');
 		$object->getActiveSheet()->SetCellValue('C2', ': '.$dept['nama']);
		$object->getActiveSheet()->mergeCells('C2:H2');

		// set Departemen dari
 		$object->getActiveSheet()->SetCellValue('A3', 'Dept Dari');
		$object->getActiveSheet()->mergeCells('A3:B3');
 		$object->getActiveSheet()->SetCellValue('C3', ': '.$dept_dari['nama']);
		$object->getActiveSheet()->mergeCells('C3:D3');

		// set View
		$object->getActiveSheet()->SetCellValue('A4', 'View');
		$object->getActiveSheet()->mergeCells('A4:B4');
		$object->getActiveSheet()->SetCellValue('C4', ': '.$view);
		$object->getActiveSheet()->mergeCells('C4:D4');

		//bold huruf
		$object->getActiveSheet()->getStyle("A1:O6")->getFont()->setBold(true);

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
			$table_head_columns  = array('No', 'kode','Tgl Kirim','Origin','Reff Picking','Kode Produk','Nama Produk','Total Lot',' Total Qty1','Total Qty2','Status','Reff Note');

			$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L');
			$loop = 0;
			foreach ($index_header as $val) {
				$object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);
				if($loop == 0 OR  $loop == 5  ){
					$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A,F

				}else if(($loop >= 1 AND $loop <= 3)){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(19); // index B,C,D
				}else if($loop >= 7 AND $loop <=10 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(14); // index I,J,K
				}else if($loop == 6 or $loop == 12 or $loop == 11){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(25); // index G,H,M,L
				}else if($loop == 4 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(20); // index E
				}

				$loop++;
			}

		}else{
			$table_head_columns  = array('No', 'kode','Tgl Kirim','Origin','Reff Picking','Kode Produk','Nama Produk','Lot','Qty1','Uom1','Qty2','Uom2','Status','Reff Note');

			$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
			$loop = 0;
			foreach ($index_header as $val) {
				$object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);
				if($loop == 0 OR  $loop == 5 ){
					$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A,F

				}else if(($loop >= 1 AND $loop <= 3) OR $loop == 8){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(19); // index B,C,D
				}else if( ($loop >= 9 AND $loop <=13 )){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(14); // index I,J,K,L
				}else if($loop == 6 OR $loop == 7 or $loop == 14){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(25); // index G,H,M
				}else if($loop == 4 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(20); // index E
				}

				$loop++;
			}

		
		}

    	$column = 0;
    	foreach ($table_head_columns as $field) {
	    	$object->getActiveSheet()->setCellValueByColumnAndRow($column, 6, $field);  
    		$column++;
    	}
    	

		if($view == 'Global'){

			// tbody
			$list  = $this->m_outstandingIn->get_list_outstanding_in_by_kode_group($departemen,$dept_dari,$kode,$corak);
			$num   = 1;
			$rowCount = 7;
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

		}else{

			// tbody
			$list  = $this->m_outstandingIn->get_list_outstanding_in_by_kode($departemen,$dept_dari,$kode,$corak);
			$num   = 1;
			$rowCount = 7;
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

				$rowCount++;
			}
		}

		
		$object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  

		$name_file ='Outstading IN '.$dept['nama'].'.xls';

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$name_file.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');

	}
}