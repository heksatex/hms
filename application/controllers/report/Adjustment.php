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
		$dataRecord2 = [];
		$dataItems   = [];

		if($load == 'header'){
			$head = $this->m_reportAdjustment->get_list_group_nama_produk_adj_in_by_kode($kode_lokasi,$tgldari,$tglsampai)->result();
			$total_adj_in = 0;
			foreach ($head as $val) {
				$dataRecord[] = array(

									  'kode_produk' => $val->kode_produk,
									  'nama_produk' => '['.$val->kode_produk.'] '.$val->nama_produk,
									  'tot_lot'     => $val->tot_lot,
									  'qty_stock'   => number_format($val->tot_qty_stock,2),
									  'qty'         => number_format($val->tot_qty1_adj,2),
									  'uom'         => $val->uom,
									  'qty2_stock'  => number_format($val->tot_qty2_stock,2),
									  'qty2'        => number_format($val->tot_qty2_adj,2),
									  'uom2'        => $val->uom_2,
									  'qty_move'    => number_format($val->tot_qty_move,2),
									  'qty_move2'   => number_format($val->tot_qty2_move,2)
							);
				$total_adj_in = $total_adj_in + $val->tot_lot;

			}

			$head = $this->m_reportAdjustment->get_list_group_nama_produk_adj_out_by_kode($kode_lokasi,$tgldari,$tglsampai)->result();
			$total_adj_out = 0;
			foreach ($head as $val) {
				$dataRecord2[] = array(

									  'kode_produk' => $val->kode_produk,
									  'nama_produk' => '['.$val->kode_produk.'] '.$val->nama_produk,
									  'tot_lot'     => $val->tot_lot,
									  'qty_stock'   => number_format($val->tot_qty_stock,2),
									  'qty'         => number_format($val->tot_qty1_adj,2),
									  'uom'         => $val->uom,
									  'qty2_stock'  => number_format($val->tot_qty2_stock,2),
									  'qty2'        => number_format($val->tot_qty2_adj,2),
									  'uom2'        => $val->uom_2,
									  'qty_move'    => number_format($val->tot_qty_move,2),
									  'qty_move2'   => number_format($val->tot_qty2_move,2)
							);
				$total_adj_out = $total_adj_out +  $val->tot_lot;

			}
			
			//$total = $this->m_reportAdjustment->get_jml_item_adjustment_by_kode($kode_lokasi,$tgldari,$tglsampai);
			
			$callback = array('record'   			=> $dataRecord,
							  'record2' 			=> $dataRecord2, 
							  'total_lot_adj_in' 	=>	'<label>Total Lot: '.$total_adj_in.'</label>',  
							  'total_lot_adj_out' 	=>	'<label>Total Lot: '.$total_adj_out.'</label>');

		}else{
			$data_isi    = $this->input->post('data_isi');
			$view        = $this->input->post('view');// in /out

			if($view == 'in'){
				$where_adj  = " AND (b.qty2_move > 0 or b.qty_move > 0) ";
			}else{
				$where_adj  = " AND (b.qty2_move < 0 or b.qty_move < 0) ";
			}

			$items = $this->m_reportAdjustment->get_list_item_adjustment_by_kode($kode_lokasi,$tgldari,$tglsampai,$data_isi,$where_adj)->result();
			foreach ($items as $val) {
				# code...
				$dataItems[] = array( 'kode_adjustment' => $val->kode_adjustment,
									  'tanggal'     => $val->create_date,
									  'nama_produk' => '['.$val->kode_produk.'] '.$val->nama_produk,
									  'lot'     	=> $val->lot,
									  'qty_stock'   => number_format($val->qty_data,2),
									  'qty'         => number_format($val->qty_adjustment,2),
									  'uom'         => $val->uom,
									  'qty2_stock'  => number_format($val->qty_data2,2),
									  'qty2'        => number_format($val->qty_adjustment2,2),
									  'uom2'        => $val->uom2,
									  'qty_move'    => number_format($val->qty_move,2),
									  'qty_move2'   => number_format($val->qty2_move,2),
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
		$tgldari   = date('Y-m-d 00:00:00',strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d 23:59:59',strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('departemen');
		$dept    = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();
		$kode_lokasi = $dept['stock_location'];// example WRD/Stock

		$object = new PHPExcel();
    	//$object->setActiveSheetIndex(0);
		$object->createSheet();
		$sheet1 = $object->setActiveSheetIndex(0);
		$sheet1->setTitle('Adjustment IN');
		
		$sheet2 = $object->setActiveSheetIndex(1);
		$sheet2->setTitle('Adjustment OUT');

		$loop  = 2;

		for($a=0; $a<$loop; $a++){
			if($a == 0){
				$sheet = $sheet1;
				$adj   = " IN ";
				$where_adj  = " AND (b.qty2_move > 0 or b.qty_move > 0) ";
				$head = $this->m_reportAdjustment->get_list_group_nama_produk_adj_in_by_kode($kode_lokasi,$tgldari,$tglsampai)->result();
				$total 	= $this->m_reportAdjustment->get_jml_item_adjustment_in_by_kode($kode_lokasi,$tgldari,$tglsampai);
			}else{
				$sheet = $sheet2;
				$adj   = " OUT ";
				$where_adj  = " AND (b.qty2_move < 0 or b.qty_move < 0) ";
				$head 	= $this->m_reportAdjustment->get_list_group_nama_produk_adj_out_by_kode($kode_lokasi,$tgldari,$tglsampai)->result();
				$total 	= $this->m_reportAdjustment->get_jml_item_adjustment_out_by_kode($kode_lokasi,$tgldari,$tglsampai);
			}

			// SET JUDUL
			$sheet->SetCellValue('A1', 'Laporan Adjustment '.$adj);
			$sheet->getStyle('A1')->getAlignment()->setIndent(1);
			$sheet->mergeCells('A1:C1');
			// $sheet->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


			// set Departemen
			$sheet->SetCellValue('A3', 'Departemen');
			$sheet->mergeCells('A3:B3');
			$sheet->SetCellValue('C3', ': '.$dept['nama']);
			$sheet->mergeCells('C3:D3');

			// set periode
			$sheet->SetCellValue('A4', 'Periode');
			$sheet->mergeCells('A4:B4');
			$sheet->SetCellValue('C4', ': '.tgl_indo(date('d-m-Y',strtotime($tgldari))).' - '.tgl_indo(date('d-m-Y',strtotime($tglsampai)) ));
			$sheet->mergeCells('C4:F4');

			// set total lot
			$sheet->SetCellValue('A5', 'Total Lot');
			$sheet->mergeCells('A5:B5');
			$sheet->SetCellValue('C5', ': '.$total);
			$sheet->mergeCells('C5:D5');

			//bold huruf
			$sheet->getStyle("A1:O7")->getFont()->setBold(true);

			// Border 
			$styleArray = array(
				'borders' => array(
					'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);	

			$column = 0;
			$table_head_columns = array('No','Kode Adjustment','Tanggal','Product','Lot','Qty Stock','Qty Adj','UoM','Qty2 Stock','Qty2 Adj','UoM2','Qty Move','Qty Move2','User','Notes');

			foreach ($table_head_columns as $field) {
				# code...
				$sheet->setCellValueByColumnAndRow($column, 7, $field);  
				$column++;
			}

			// set width column
			$sheet->getColumnDimension('A')->setAutoSize(true);
			$sheet->getColumnDimension('B')->setAutoSize(true);
			$sheet->getColumnDimension('D')->SetWidth(18);
			$sheet->getColumnDimension('C')->SetWidth(20);
			$sheet->getColumnDimension('D')->setAutoSize(true);
			$sheet->getColumnDimension('E')->SetWidth(21);
			$sheet->getColumnDimension('F')->SetWidth(15);
			$sheet->getColumnDimension('G')->SetWidth(15);
			$sheet->getColumnDimension('H')->SetWidth(10);
			$sheet->getColumnDimension('I')->SetWidth(15);
			$sheet->getColumnDimension('J')->SetWidth(15);
			$sheet->getColumnDimension('K')->SetWidth(10);
			$sheet->getColumnDimension('L')->SetWidth(15);
			$sheet->getColumnDimension('M')->SetWidth(15);
			$sheet->getColumnDimension('N')->SetWidth(17);
			$sheet->getColumnDimension('O')->SetWidth(17);


			// set border
			$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
			// set border header
			foreach ($index_header as $val) {
				$sheet->getStyle($val.'7')->applyFromArray($styleArray);
				$sheet->getStyle($val.'7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			}

			$tgl_dari   = date('Y-m-d 00:00:00',strtotime($tgldari));
			$tgl_sampai = date('Y-m-d 23:59:59',strtotime($tglsampai));
			$rowCount   = 8;
			$no         = 1;

			foreach ($head as $hd) {
					# code...
				$kode_produk  =  $hd->kode_produk;
				$nama_produk = '['.$hd->kode_produk.'] '.$hd->nama_produk;
				$tot_lot     = $hd->tot_lot;
				$tot_qty_stock= $hd->tot_qty_stock;
				$tot_qty     = $hd->tot_qty1_adj;
				$tot_qty2_stock    = $hd->tot_qty2_stock;
				$tot_qty2    = $hd->tot_qty2_adj;
				$uom         = $hd->uom;
				$uom2        = $hd->uom_2;
				$tot_qty_move= $hd->tot_qty_move;
				$tot_qty2_move= $hd->tot_qty2_move;


				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($no));
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $nama_produk);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $tot_lot);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $tot_qty_stock);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $tot_qty);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $uom);
				$object->getActiveSheet()->SetCellValue('I'.$rowCount, $tot_qty2_stock);
				$object->getActiveSheet()->SetCellValue('J'.$rowCount, $tot_qty2);
				$object->getActiveSheet()->SetCellValue('K'.$rowCount, $uom2);
				$object->getActiveSheet()->SetCellValue('L'.$rowCount, $tot_qty_move);
				$object->getActiveSheet()->SetCellValue('M'.$rowCount, $tot_qty2_move);


				// set bold
				$object->getActiveSheet()->getStyle("A".$rowCount)->getFont()->setBold(TRUE);
				$object->getActiveSheet()->getStyle("D".$rowCount)->getFont()->setBold(TRUE);
				$object->getActiveSheet()->getStyle("E".$rowCount)->getFont()->setBold(TRUE);
				$object->getActiveSheet()->getStyle("F".$rowCount)->getFont()->setBold(TRUE);
				$object->getActiveSheet()->getStyle("G".$rowCount)->getFont()->setBold(TRUE);
				$object->getActiveSheet()->getStyle("H".$rowCount)->getFont()->setBold(TRUE);
				$object->getActiveSheet()->getStyle("I".$rowCount)->getFont()->setBold(TRUE);
				$object->getActiveSheet()->getStyle("J".$rowCount)->getFont()->setBold(TRUE);
				$object->getActiveSheet()->getStyle("K".$rowCount)->getFont()->setBold(TRUE);
				$object->getActiveSheet()->getStyle("L".$rowCount)->getFont()->setBold(TRUE);
				$object->getActiveSheet()->getStyle("M".$rowCount)->getFont()->setBold(TRUE);

				// set align enter
			    $object->getActiveSheet()->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			    $object->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			    $object->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

				$rowCount++;


				$items = $this->m_reportAdjustment->get_list_item_adjustment_by_kode($kode_lokasi,$tgldari,$tglsampai,$kode_produk,$where_adj)->result();

				foreach ($items as $val) {
					# code...
					$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->kode_adjustment);
					$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->create_date);
					$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->lot);
					$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->qty_data);
					$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->qty_adjustment);
					$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->uom);
					$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->qty_data2);
					$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->qty_adjustment2);
					$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->uom2);
					$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->qty_move);
					$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->qty2_move);
					$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->nama_user);
					$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->note);

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
					$sheet->getStyle($val.''.$rowCount2)->applyFromArray($styleArray);
				}

				$rowCount2++;
			}

		}

    	$object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename=Laporan Adjustment '.$dept['nama'].' .xlsx '); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');	
    

	}


}