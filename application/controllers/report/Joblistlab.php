<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Joblistlab extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        $this->load->model('m_joblistlab');
	}

	public function index()
	{
		$id_dept        = 'JLLAB';
        $data['id_dept']= $id_dept;
		$data['mst_sales_group'] = $this->_module->get_list_sales_group();
		$this->load->view('report/v_job_list_lab', $data);
	}

    public function get_data()
	{
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $check_stock_grg = $this->input->post('check_stock');

            $list = $this->m_joblistlab->get_datatables();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                // $kode_co_encrypt = encrypt_url($field->kode_co);
                if($field->status_ow == 't'){
                    $status_ow = 'Aktif';
                }else if($field->status_ow == 'ng'){
                    $status_ow = 'Not Good';
                }else if($field->status_ow == 'r'){
                    $status_ow = 'Reproses';
                }else{
                    $status_ow = 'Tidak Aktif';
                }

                $stock_grg =  number_format($field->tot_qty1,2);
                // if($check_stock_grg == 'true'){
                // }else{
                //     $stock_grg = 'NA';
                // }

                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->sales_order;
                $row[] = $field->nama_sales_group;
                $row[] = $field->no_ow;
                $row[] = $field->tgl_ow;
                $row[] = $status_ow;
                $row[] = $field->nama_produk;
                $row[] = $field->nama_warna;
                $row[] = $stock_grg;
                $row[] = $field->gramasi;
                $row[] = $field->nama_handling;
                $row[] = $field->nama_route;
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->status_dti;
                $row[] = $field->reff_note;
                $row[] = $field->delivery_date;
                $row[] = ucwords($field->status_resep);
                $row[] = $field->tgl_selesai_resep;
                if($field->status_resep == 'draft' AND $field->status_ow != 'f'){
                    $row[] = '<button type="button" class="btn btn-primary btn-xs" id="btn-done" name="done_resep" onclick=doneResep(this,"'.$field->id.'","'.$field->no_ow.'","'.$field->status_resep.'")>Done Resep</button>';
                }else{
                    $row[] = '';
                }
                $row[] = $field->status_ow;
                $data[] = $row;
                
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_joblistlab->count_all(),
                "recordsFiltered" => $this->m_joblistlab->count_filtered(),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);
        }else{
            die();
        }
	}

    public function done_resep() 
    {
        try {
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $sub_menu  = $this->uri->segment(2);
                $username  = addslashes($this->session->userdata('username')); 

                $id = $this->input->post('id');
                $ow = $this->input->post('ow');
                $tgl = date("Y-m-d H:i:s");

                // start transaction
                $this->_module->startTransaction();

                //lock table
                $this->_module->lock_tabel('job_list_lab WRITE,  log_history WRITE, user WRITE, main_menu_sub WRITE');

                //cek job list by id
                $cek_jb = $this->m_joblistlab->get_data_joblist_lab_by_id($id);
                if($cek_jb->status_resep == 'done') {
                    throw new \Exception('Data Resep untuk '.$ow.' sudah Done !', 200);
                } else {

                    $data_update = array('status_resep' => 'done', 'tgl_selesai_resep'=>$tgl);
                    $this->m_joblistlab->update_joblistlab_by_id($id,$data_update);

                }

                if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Done Resep Gagal', 500);
                }

                $jenis_log   = "edit";
                $note_log    = "Update Status Done Resep ".$ow;
                $this->_module->gen_history($sub_menu, $id, $jenis_log, $note_log, $username);

                $callback = array('status' => 'success', 'message' => 'Resep berhasil di Selesaikan  !', 'icon' => 'fa fa-check', 'type' => 'success');
                $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

            }
        } catch (\Exception $ex) {
            $this->output->set_status_header($ex->getCode() ?? 500)
                            ->set_content_type('application/json', 'utf-8')
                            ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            $this->_module->rollbackTransaction();
            // unlock table
            $this->_module->unlock_tabel();
        } 
    }


    public function export_excel()
    {   



		$sc         = $this->input->post('sc');
		$sales_group    = $this->input->post('sales_group');
		$ow             = $this->input->post('ow');
		$produk         = $this->input->post('produk');
		$warna          = $this->input->post('warna');
		$status_ow      = $this->input->post('status_ow');
		$status_dti     = $this->input->post('status_dti');
		$status_resep   = $this->input->post('status_resep');

        $this->load->library('excel');
        ob_start();
        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

        // SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Jolist Lab');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        //bold huruf
		$object->getActiveSheet()->getStyle("A1:T5")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

        // header table
    	$table_head_columns  = array('No', 'No.SC', 'MKT', 'No.OW', 'Tgl OW', 'Status OW', 'Nama Produk', 'Warna', 'Stock GRG[Mtr]', 'Gramasi','Finishing', 'Route', 'L.Jadi','DTI','Reff Notes','Delivery Date','Status Resep', 'Tgl Selesai Resep');
        $column = 0;
        foreach ($table_head_columns as $field) {
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 5, $field);  
    		$column++;
    	}

        $num   = 1;
        $rowCount = 6;
        $list = $this->m_joblistlab->get_data_excel();
        foreach($list as $val){

            if($val->status_ow == 't'){
                $status_ow = 'Aktif';
            }else if($val->status_ow == 'ng'){
                $status_ow = 'Not Good';
            }else if($val->status_ow == 'r'){
                $status_ow = 'Reproses';
            }else{
                $status_ow = 'Tidak Aktif';
            }

            $stock_grg = $val->tot_qty1;

            $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->sales_order);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->nama_sales_group);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->no_ow);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->tgl_ow);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $status_ow);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->nama_produk);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->nama_warna);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $stock_grg);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->gramasi);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->nama_handling);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->nama_route);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->lebar_jadi.' '.$val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->status_dti);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->reff_note);
			$object->getActiveSheet()->SetCellValue('P'.$rowCount, $val->delivery_date);
			$object->getActiveSheet()->SetCellValue('Q'.$rowCount, ucwords($val->status_resep));
			$object->getActiveSheet()->SetCellValue('R'.$rowCount, $val->tgl_selesai_resep);
            $rowCount++;
        }

      
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = "Joblist Lab.xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));
    }

}