<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Outstandingow extends MY_Controller
{

    public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
		$this->load->model('m_outstandingOW');
	}

    public function index()
	{
		$id_dept        = 'OTSOW';
        $data['id_dept']= $id_dept;
		$data['mst_sales_group'] = $this->_module->get_list_sales_group();
		$this->load->view('report/v_outstanding_ow', $data);
	}

    public function get_data()
	{   

        $list = $this->m_outstandingOW->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        
            if($field->status_scl == 't'){
                $status_scl = 'Aktif';
            }else if($field->status_scl == 'ng'){
                $status_scl = 'Not Good';
            }else{
                $status_scl = 'Tidak Aktif';
            }

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->sales_order;
            $row[] = $field->nama_sales_group;
            $row[] = $field->ow;
            $row[] = $field->tanggal_ow;
            $row[] = $status_scl;
            $row[] = $field->nama_produk;
            $row[] = $field->nama_warna;
            $row[] = number_format($field->qty,2).' '.$field->uom;
            $row[] = $field->reff_notes;
         
            $data[] = $row;
            
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_outstandingOW->count_all(),
            "recordsFiltered" => $this->m_outstandingOW->count_filtered(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}

    public function export_excel()
    {   

		$sc             = $this->input->post('sc');
		$sales_group    = $this->input->post('sales_group');
		$ow             = $this->input->post('ow');
		$produk         = $this->input->post('produk');
		$warna          = $this->input->post('warna');
		$status_ow      = $this->input->post('status_ow');

        $this->load->library('excel');
		ob_start();
        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

        // SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Outstanding OW');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        //bold huruf
		$object->getActiveSheet()->getStyle("A1:T3")->getFont()->setBold(true);

        // header table
    	$table_head_columns  = array('No', 'No.SC', 'Kode MKT', 'No.OW', 'Tgl OW', 'Status OW', 'Nama Produk', 'Warna', 'Qty', 'Uom','Reff Note');
        $column = 0;
        foreach ($table_head_columns as $field) {
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $field);  
    		$column++;
    	}

        $num   = 1;
        $rowCount = 4;
        $list = $this->m_outstandingOW->get_list_ow_by_kode($sc,$sales_group,$ow,$produk,$warna,$status_ow);
        foreach($list as $val){

            if($val->status_scl == 't'){
                $status_scl = 'Aktif';
            }else if($val->status_scl == 'ng'){
                $status_scl = 'Not Good';
            }else{
                $status_scl = 'Tidak Aktif';
            }

            $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->sales_order);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->nama_sales_group);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->ow);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->tanggal_ow);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $status_scl);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->nama_produk);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->nama_warna);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->qty);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->uom);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->reff_notes);
	        $rowCount++;
        }

      
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = "Outstanding OW.xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));
    }
}