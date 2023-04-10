<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Downtime extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model("_module");
        $this->load->model("m_downtime");
	}


	public function index()
	{	
        $data['id_dept']='RDT';
		$this->load->view('report/v_downtime', $data);
	}


    public function loadData()
	{
		date_default_timezone_set('Asia/Jakarta');

		$tgldari     = addslashes($this->input->post('tgldari'));
		$tglsampai   = addslashes($this->input->post('tglsampai'));
		$id_dept     = addslashes($this->input->post('id_dept'));
		
		$list_data   = [];
		
		$tglawal     = date('Y-m-d H:i:s', strtotime($tgldari));
		$tglakhir    = date('Y-m-d H:i:s', strtotime($tglsampai));

        $get_data   = $this->m_downtime->get_down_up_time($id_dept,$tglawal,$tglakhir);
		foreach ($get_data as $val) {
            $list_data[]   = array(
									'mc_id'		   => $val->mc_id,
                                    'nama_mesin'   => $val->nama_mesin,
                                    'downtime'     => $val->down,
                                    'downtime_2'   => $val->downtime,
                                    'uptime'       => $val->up,
                                    'uptime_2'     => $val->uptime,
                                    'dc'       	   => $val->dc,
                                    'dct'   	   => $val->dct,
                                    'dcr'	       => $val->dcr,
            );
        }

        $callback = array('success' => 'Yes', 'record' => $list_data);

		echo json_encode($callback);

    }

	function view_detail_downtime()
    {
        $mc_id    	 = addslashes($this->input->post("id"));// mc
		$tgldari     = addslashes($this->input->post('tgldari'));
		$tglsampai   = addslashes($this->input->post('tglsampai'));
		$id_dept     = addslashes($this->input->post('id_dept'));

		$tglawal     = date('Y-m-d H:i:s', strtotime($tgldari));
		$tglakhir    = date('Y-m-d H:i:s', strtotime($tglsampai));

		$get_nm		  = $this->m_downtime->get_nama_mesin_by_kode($mc_id)->row_array();
		$data['list'] = $this->m_downtime->get_log_by_mc($id_dept,$tglawal,$tglakhir,$mc_id);
        $data['mc']   = $mc_id;
		$data['nama_mesin'] = $get_nm['nama_mesin'];
        return $this->load->view('modal/v_detail_downtime_modal',$data);
    }

	function shortcut_get()
	{
		$btn = $this->input->post('shortcut');
		$jam = date('H:i:s');
		// $jam = strtotime('11:59:59');
		if($btn == 'now'){

			// get shift apa skrng
			$shift  = $this->m_downtime->get_shift_now($jam);
			$result = $this->get_jam_shift($shift);
			// $tgl_dari   = date('d M Y H:i:s',$result[0]);
			$tgl_dari1   = date('d-F-Y');
			$tgl_dari   = $tgl_dari1." ".$result[0];

			$date1 		= new DateTime();
			$tgl_sampai = $date1->format('d-F-Y H:i:s');

		}else if($btn == '1shift-before'){
			$shift  = $this->m_downtime->get_shift_now($jam);
			// $shift  = 3;

			$day 	 	  = '';
			if($shift == '1'){
				$shift_before = '3';
				$day 	 	  = '-1 day';
			}else if($shift == '2'){
				$shift_before = '1';
			}else{// shift 3
				$shift_before = '2';
			}

			$shift 		= $shift_before;
			$result 	= $this->get_jam_shift($shift);
			if(!empty($day)){
				$tgl1  = date('d-F-Y', strtotime($day));
				$tgl_dari   = $tgl1." ".$result[0];
			}else{
				$tgl1  = date('d-F-Y');
				$tgl_dari  = $tgl1.' '.$result[0];
			}
			$tgl2  = date('d-F-Y');
			$tgl_sampai = $tgl2." ".$result[1];

		}else if($btn == "24hours-before"){
			$date = new DateTime();
			$date->modify('-24 hours');
			$tgl_dari = $date->format('d-F-Y H:i:s');

			$date2 = new DateTime();
			// $date2->modify('-1hours');
			$tgl_sampai = $date2->format('d-F-Y H:i:s');

		}else if($btn == "7days-before"){

			$date = new DateTime();
			$date->modify('-7 Days');
			$tgl_dari = $date->format('d-F-Y H:i:s');

			$tgl_sampai = date('d-F-Y H:i:s');

		}else{// 30 days before

			$date = new DateTime();
			$date->modify('-30 Days');
			$tgl_dari = $date->format('d-F-Y H:i:s');

			$tgl_sampai = date('d-F-Y H:i:s');
		}

        $callback = array('success' => 'Yes', 'tgl_dari' => $tgl_dari,  'tgl_sampai'=> $tgl_sampai);
		echo json_encode($callback);
	}

	function get_jam_shift($shift)
	{	
		if($shift == '1'){
			$jam_mulai 		= "07:00:00"; 
			$jam_selesai 	= '14:59:59';
		}else if($shift == '2'){
			$jam_mulai 		= "15:00:00";
			$jam_selesai 	= '22:59:59';
		}else{
			$jam_mulai 		= '23:00:00';
			$jam_selesai 	= '06:59:59';
		}

		return array($jam_mulai,$jam_selesai);

	}


    function export_excel()
    {
		
		$arr_filter  = $this->input->post('arr_filter');

		$tglawal   	 = '';
		$tglakhir    = '';
		$id_dept     = '';

		if(!empty($arr_filter)){
			
			foreach($arr_filter as $filter){
				
				if(!empty($filter['tgldari'])){
					$tglawal  = date('Y-m-d H:i:s', strtotime($filter['tgldari']));
				}
				if(!empty($filter['tglsampai'])){
					$tglakhir  = date('Y-m-d H:i:s', strtotime($filter['tglsampai']));
				} 
				if(!empty($filter['id_dept'])){
					$id_dept 	= $filter['id_dept'];
				}
				
			}
		}

		if(empty($arr_filter) or empty($tglawal) OR empty($tglakhir) Or empty($id_dept)){
			$response = array('status' => 'failed', 'message' => 'Filter Kosong', 'icon' =>'fa fa-warning', 'type' => 'danger');
		}else{

			$dept     	 = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();

			$this->load->library('excel');
			ob_start();
			
			$object = new PHPExcel();
			$object->setActiveSheetIndex(0);

			// SET JUDUL
			$object->getActiveSheet()->SetCellValue('A1', 'Report Downtime');
			$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
			$object->getActiveSheet()->mergeCells('A1:E1');

			// set Departemen
			$object->getActiveSheet()->SetCellValue('A2', 'Departemen');
			$object->getActiveSheet()->mergeCells('A2:B2');
			$object->getActiveSheet()->SetCellValue('C2', ': ' . $dept['nama']);
			$object->getActiveSheet()->mergeCells('C2:D2');

			//bold huruf
			$object->getActiveSheet()->getStyle("A1:I5")->getFont()->setBold(true);

			// Border 
			$styleArray = array(
				'borders' => array(
					'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
					)
				)
			);	


			// header table
			$table_head_columns  = array('No','Nama Mesin','Downtime(min)','Downtime(%)','Uptime(min)','Uptime(%)','dc','dct','dcr');

			$column = 0;
			foreach ($table_head_columns as $field) {
				$object->getActiveSheet()->setCellValueByColumnAndRow($column, 5, $field);  
				$column++;
			}

			// set with and border
			$index_header = array('A','B','C','D','E','F','G','H','I');
			$loop = 0;
			foreach ($index_header as $val) {
				
				if($loop < 1){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(10);
				}else if($loop ==1 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(30);
				}else if($loop >1){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(20);
				}
				$loop++;
			}

		
			$rowCount   = 6;
			$num        = 1;
			$get_data   = $this->m_downtime->get_down_up_time($id_dept,$tglawal,$tglakhir);
			foreach ($get_data as $val) {

				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->nama_mesin);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->down);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->downtime);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->up);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->uptime);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->dc);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->dct);
				$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->dcr);
				$rowCount++;
			}



			$object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
			$object->save('php://output');

			$xlsData = ob_get_contents();
			ob_end_clean();

			$response =  array(
					'status'    => 'success',
					'op'        => 'ok',
					'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
					'filename'  => 'Report Downtime ' . $dept['nama'] . ' .xlsx'
			);
		}

		die(json_encode($response));
    }

}