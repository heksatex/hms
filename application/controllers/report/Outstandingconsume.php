<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Outstandingconsume extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('m_outstandingConsume');
        $this->load->model('_module');
	}

    public function index()
	{
		$id_dept        = 'OTSCON';
        $data['id_dept']= $id_dept;
        $data['warehouse']  = $this->_module->get_list_departement();
		$this->load->view('report/v_outstanding_consume', $data);
	}


    function loadData()
	{
        $departemen     = addslashes($this->input->post('departemen'));
		$kode   		= addslashes($this->input->post('kode'));
		$corak  		= addslashes($this->input->post('corak'));
		$lot		    = addslashes($this->input->post('lot'));
		$view_arr  		= $this->input->post('view_arr');
		$dataRecord = [];

		if(empty($view_arr)){
			$callback = array('status' => 'failed', 'message' => 'View Harus diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
		}else{

			$view ='Global';
			if(count($view_arr)>0){
				foreach($view_arr as $val2){
					$view = $val2;
					break;
				}
			}

			if($view == 'Detail'){

				$list  = $this->m_outstandingConsume->get_list_outstanding_con_by_kode($departemen,$kode,$corak,$lot);
				$total = 0;
				foreach($list as $row){
					$kode_encrypt = encrypt_url($row->kode);
					$dataRecord[] = array('kode' 		=> $row->kode,
										'kode_enc'      => $kode_encrypt,
										'tgl_mo' 	    => $row->tanggal,
										'origin'		=> $row->origin,
										'kode_produk'	=> $row->kode_produk,
										'nama_produk'	=> $row->nama_produk,
										'qty'	        => number_format($row->qty,2).' '.$row->uom,
										'lot'			=> $row->lot,
										'qty1'			=> number_format($row->qty1,2).' '.$row->uom1,
										'qty2'			=> number_format($row->qty2,2).' '.$row->uom2,
										'grade'		    => $row->nama_grade,
										'reff_note'		=> $row->reff_note,

					);
					$total++;
				}
			}else{

				$list  = $this->m_outstandingConsume->get_list_outstanding_con_by_kode_group($departemen,$kode,$corak,$lot);
				$total = 0;
				foreach($list as $row){

					$kode_encrypt = encrypt_url($row->kode);
					$dataRecord[] = array('kode' 		=> $row->kode,
										'kode_enc'      => $kode_encrypt,
										'tanggal' 	    => $row->tanggal,
										'origin'		=> $row->origin,
										'kode_produk'	=> $row->kode_produk,
										'nama_produk'	=> $row->nama_produk,
										'qty'	        => number_format($row->qty,2).' '.$row->uom,
										'lot'			=> $row->total_lot,

					);
					$total++;
				}

			}

			$callback = array('record' => $dataRecord, 'total_record' => 'Total Data : '.number_Format($total), 'view' => $view);
		}

		echo json_encode($callback);

	}

	function export_excel()
    {

		$departemen     = addslashes($this->input->post('departemen'));
		$kode   		= addslashes($this->input->post('kode'));
		$corak  		= addslashes($this->input->post('corak'));
		$view_arr  		= $this->input->post('view_arr');


		if(empty($view_arr)){
			$callback = array('status' => 'failed', 'message' => 'View Harus diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
			die(json_encode($callback));
		}else{

			$view ='Global';
			if(count($view_arr)>0){
				foreach($view_arr as $val2){
					$view = $val2;
					break;
				}
			}
		
			$this->load->library('excel');
			ob_start();
			
			$object = new PHPExcel();
			$object->setActiveSheetIndex(0);

			$dept      = $this->_module->get_nama_dept_by_kode($departemen)->row_array();
			
			// SET JUDUL
			$object->getActiveSheet()->SetCellValue('A1', 'Oustanding Consume');
			$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
			$object->getActiveSheet()->mergeCells('A1:L1');

			// set Departemen
			$object->getActiveSheet()->SetCellValue('A2', 'Departemen');
			$object->getActiveSheet()->mergeCells('A2:B2');
			$object->getActiveSheet()->SetCellValue('C2', ': '.$dept['nama']);
			$object->getActiveSheet()->mergeCells('C2:H2');

			// set View
			$object->getActiveSheet()->SetCellValue('A3', 'View');
			$object->getActiveSheet()->mergeCells('A3:B3');
			$object->getActiveSheet()->SetCellValue('C3', ': '.$view);
			$object->getActiveSheet()->mergeCells('C3:D3');

			
			//bold huruf
			$object->getActiveSheet()->getStyle("A1:W5")->getFont()->setBold(true);

			// Border 
			$styleArray = array(
				'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
				)
			);

			// header table
			$table_head_columns  = array('No', 'Kode','Origin', 'Kode Produk', 'Nama Produk', 'Target Qty','uom','Total Lot');

			$column = 0;
			foreach ($table_head_columns as $judul) {
				# code...
				$object->getActiveSheet()->setCellValueByColumnAndRow($column, 5, $judul);  
				$column++;
			}
	
			// set with and border
			$index_header = array('A','B','C','D','E','F','G','H');
			$loop = 0;
			foreach ($index_header as $val) {
				$object->getActiveSheet()->getStyle($val.'5')->applyFromArray($styleArray);
			}
			

			//body
			$num      = 1;
			$rowCount = 6;
			$list  	  = $this->m_outstandingConsume->get_list_outstanding_con_by_kode_group($departemen,$kode,$corak,'');
			foreach ($list as $val) {
				# code...
				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->kode);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->origin);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->kode_produk);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->nama_produk);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, ($val->qty));
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->uom);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->total_lot);

				//set border true
				$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
				$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);


				$rowCount++;
			}


			
			$object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
			$object->save('php://output');

			$xlsData = ob_get_contents();
			ob_end_clean();

			$response =  array(
				'op'        => 'ok',
				'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
				'filename'  => "Outstanding Consume ".$dept['nama'].".xlsx"
			);
		
			die(json_encode($response));
		}
    }
}