<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Adjustment extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model("_module");
        $this->load->model("m_reportAdjustment");
	}


	public function index()
	{	
        $data['id_dept']='RADJ';
		$this->load->view('report/v_adjustment', $data);
	}


	function loadData()
	{
		$tgldari   = date('Y-m-d 00:00:00',strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d 23:59:59',strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('id_dept');
		$load      = $this->input->post('load');

		$dp = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();
		$kode_lokasi = $dp['stock_location'];// example WRD/Stock
		$dataRecord  = [];
		$dataItems   = [];

		if($load == 'header'){
			$head = $this->m_reportAdjustment->get_list_group_nama_produk_by_kode($kode_lokasi,$tgldari,$tglsampai)->result();
			foreach ($head as $val) {
				# code...
				$dataRecord[] = array(

									  'kode_produk' => $val->kode_produk,
									  'nama_produk' => '['.$val->kode_produk.'] '.$val->nama_produk,
									  'tot_lot'     => $val->tot_lot,
									  'qty'         => $val->tot_qty_move,
									  'uom'         => $val->uom,
									  'qty2'        => $val->tot_qty2,
									  'uom2'        => $val->uom_2
							);

			}

			$callback = array('record'   => $dataRecord);
		}else{
			$data_isi    = $this->input->post('data_isi');

			$items = $this->m_reportAdjustment->get_list_item_adjustment_by_kode($kode_lokasi,$tgldari,$tglsampai,$data_isi)->result();
			foreach ($items as $val) {
				# code...
				$dataItems[] = array( 'kode_adjustment' => $val->kode_adjustment,
									  'tanggal'     => $val->create_date,
									  'nama_produk' => '['.$val->kode_produk.'] '.$val->nama_produk,
									  'lot'     => $val->lot,
									  'qty'         => $val->qty_move,
									  'uom'         => $val->uom,
									  'qty2'        => $val->qty_adjustment2,
									  'uom2'        => $val->uom2,
									  'user'        => $val->nama_user,
									  'note'        => $val->note

									);
			}
			$callback = array('item'   => $dataItems);

		}

		echo json_encode($callback);

	}

	function export_excel()
    {

    	$this->load->library('excel');
		$tgldari   = date('Y-m-d',strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d',strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('departemen');
		$dept    = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();
		$kode_lokasi = $dept['stock_location'];// example WRD/Stock

		$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Adjustment');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:K1');
		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


		// set Departemen
 		$object->getActiveSheet()->SetCellValue('A3', 'Departemen');
		$object->getActiveSheet()->mergeCells('A3:B3');
 		$object->getActiveSheet()->SetCellValue('C3', ': '.$dept['nama']);
		$object->getActiveSheet()->mergeCells('C3:D3');

		// set periode
 		$object->getActiveSheet()->SetCellValue('A4', 'Periode');
		$object->getActiveSheet()->mergeCells('A4:B4');
 		$object->getActiveSheet()->SetCellValue('C4', ': '.tgl_indo(date('d-m-Y',strtotime($tgldari))).' - '.tgl_indo(date('d-m-Y',strtotime($tglsampai)) ));
		$object->getActiveSheet()->mergeCells('C4:F4');

 		//bold huruf
		$object->getActiveSheet()->getStyle("A1:K6")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

		$column = 0;
		$table_head_columns = array('No','Kode Adjustment','Tanggal','Product','Lot','Qty','UoM','Qty2','UoM2','User','Notes');

		foreach ($table_head_columns as $field) {
			# code...
	    	$object->getActiveSheet()->setCellValueByColumnAndRow($column, 6, $field);  
	    	$column++;
		}

		// set width column
		$object->getSheet(0)->getColumnDimension('A')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('B')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('D')->SetWidth(18);
		$object->getSheet(0)->getColumnDimension('C')->SetWidth(20);
		$object->getSheet(0)->getColumnDimension('D')->setAutoSize(true);
		$object->getSheet(0)->getColumnDimension('E')->SetWidth(21);
		$object->getSheet(0)->getColumnDimension('F')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('G')->SetWidth(10);
		$object->getSheet(0)->getColumnDimension('H')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('I')->SetWidth(10);
		$object->getSheet(0)->getColumnDimension('J')->SetWidth(15);
		$object->getSheet(0)->getColumnDimension('K')->SetWidth(17);


		// set border
		$index_header = array('A','B','C','D','E','F','G','H','I','J','K');
		// set border header
		foreach ($index_header as $val) {
			$object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);
	        $object->getActiveSheet()->getStyle($val.'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		}

		$tgl_dari   = date('Y-m-d 00:00:00',strtotime($tgldari));
		$tgl_sampai = date('Y-m-d 23:59:59',strtotime($tglsampai));
		$rowCount   = 7;
		$no         = 1;

		$head = $this->m_reportAdjustment->get_list_group_nama_produk_by_kode($kode_lokasi,$tgldari,$tglsampai)->result();
		foreach ($head as $hd) {
				# code...
			$kode_produk  =  $hd->kode_produk;
			$nama_produk = '['.$hd->kode_produk.'] '.$hd->nama_produk;
			$tot_lot     = $hd->tot_lot;
			$tot_qty     = $hd->tot_qty_move;
			$tot_qty2    = $hd->tot_qty2;
			$uom         = $hd->uom;
			$uom2        = $hd->uom_2;


			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($no));
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $nama_produk);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $tot_lot);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $tot_qty);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $uom);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $tot_qty2);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $uom2);


			// set bold
			$object->getActiveSheet()->getStyle("A".$rowCount)->getFont()->setBold(TRUE);
			$object->getActiveSheet()->getStyle("D".$rowCount)->getFont()->setBold(TRUE);
			$object->getActiveSheet()->getStyle("E".$rowCount)->getFont()->setBold(TRUE);
			$object->getActiveSheet()->getStyle("F".$rowCount)->getFont()->setBold(TRUE);
			$object->getActiveSheet()->getStyle("G".$rowCount)->getFont()->setBold(TRUE);
			$object->getActiveSheet()->getStyle("H".$rowCount)->getFont()->setBold(TRUE);
			$object->getActiveSheet()->getStyle("I".$rowCount)->getFont()->setBold(TRUE);

			// set align enter
	        $object->getActiveSheet()->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $object->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $object->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $object->getActiveSheet()->getStyle('F'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        $object->getActiveSheet()->getStyle('H'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$rowCount++;


			$items = $this->m_reportAdjustment->get_list_item_adjustment_by_kode($kode_lokasi,$tgl_dari,$tgl_sampai,$kode_produk)->result();

			foreach ($items as $val) {
				# code...
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->kode_adjustment);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->create_date);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->lot);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->qty_move);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->uom);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->qty_adjustment2);
				$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->uom2);
				$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->nama_user);
				$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->note);

				// set align
		        $object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		        $object->getActiveSheet()->getStyle('C'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	        	$object->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

				$rowCount++;
			}

			$no++;

		}

		// set border
		$rowCount2  = 7;
		while ($rowCount2 <= $rowCount-1 ) {

			foreach ($index_header as $val) {
	        	$object->getActiveSheet()->getStyle($val.''.$rowCount2)->applyFromArray($styleArray);
			}

			$rowCount2++;
		}

    	$object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename=Laporan Adjustment '.$dept['nama'].' .xls '); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');	
    

	}


}