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
		$departemen  = $this->input->post('departemen');
		$dept_dari   = $this->input->post('dept_dari');
		$status_arr  = $this->input->post('status_arr');
		$dataRecord = [];

		$status      = '';
		foreach($status_arr as $val){
				$status .= "'".$val."',";
		}
		$status = rtrim($status, ', ');// pb.status in ('done','ready')

		$list  = $this->m_inout->get_list_penerimaan_harian_by_kode($tgldari,$tglsampai,$departemen,$dept_dari,$status);
		$total = 0;
		foreach($list as $row){
			$nama_status = $this->_module->get_mst_status_by_kode($row->status);
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
								'status'		=> $nama_status,
								'reff_note'		=> $row->reff_note

			);
			$total++;
		}
	
		$callback = array('record' => $dataRecord, 'total_record' => 'Total Lot : '.$total);

		echo json_encode($callback);

	}

	function export_excel_in()
	{

		$this->load->library('excel');

		$tgldari     = date('Y-m-d H:i:s', strtotime($this->input->post('tgldari')));
		$tglsampai   = date('Y-m-d H:i:s', strtotime($this->input->post('tglsampai')));
		$departemen  = $this->input->post('departemen');
		$dept_dari   = $this->input->post('dari');
		$status_arr  = $this->input->post('status[]');
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

		// set Departemen tujuan
		$object->getActiveSheet()->SetCellValue('A4', 'Tujuan');
		$object->getActiveSheet()->mergeCells('A4:B4');
		$object->getActiveSheet()->SetCellValue('C4', ': '.$dept_dr['nama']);
		$object->getActiveSheet()->mergeCells('C4:D4');

		// Status
		$object->getActiveSheet()->SetCellValue('A5', 'Status');
		$object->getActiveSheet()->mergeCells('A5:B5');
		$object->getActiveSheet()->SetCellValue('C5', ': '.$status_capt);
		$object->getActiveSheet()->mergeCells('C5:D5');

		//bold huruf
		$object->getActiveSheet()->getStyle("A1:N7")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

		// header table
    	$table_head_columns  = array('No', 'kode','Tgl Kirim','Origin','Reff Picking','Kode Produk','Nama Produk','Lot','Qty1','Uom1','Qty2','Uom2','Status','Reff Note');
    	$column = 0;
    	foreach ($table_head_columns as $field) {
	    	$object->getActiveSheet()->setCellValueByColumnAndRow($column, 7, $field);  
    		$column++;
    	}

    	// set width and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
		$loop = 0;
    	foreach ($index_header as $val) {
			$object->getActiveSheet()->getStyle($val.'7')->applyFromArray($styleArray);
			if($loop == 0  AND $loop >=12){
				$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A,M,N
			}else if(($loop == 3 OR $loop ==4) OR $loop == 7){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(19); // index D,E,H
			}else if( $loop == 2 OR $loop == 5 AND ($loop >= 8 AND $loop <=11 )){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(14); // index C,F,I,J,K,L
			}else if($loop == 6){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(25); // index G
			}else if($loop == 1){
				$object->getSheet(0)->getColumnDimension($val)->setWidth(10); // index B
			}

			$loop++;
		}

		// tbody
		$list  = $this->m_inout->get_list_penerimaan_harian_by_kode($tgldari,$tglsampai,$departemen,$dept_dari,$status);
		$num   = 1;
        $rowCount = 8;
		foreach($list as $row){
			$nama_status = $this->_module->get_mst_status_by_kode($row->status);

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
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $nama_status);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $row->reff_note);

			// set wrapText
			$object->getActiveSheet()->getStyle('B'.$rowCount.':B'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
			$object->getActiveSheet()->getStyle('C'.$rowCount.':C'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
			$object->getActiveSheet()->getStyle('E'.$rowCount.':E'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
			$object->getActiveSheet()->getStyle('G'.$rowCount.':G'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 
			$object->getActiveSheet()->getStyle('N'.$rowCount.':N'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true); 

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

		$object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  

		$name_file ='Penerimaan Harian '.$dept['nama'].'.xls';

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$name_file.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');

	}



}