<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class ListOW extends MY_Controller
{

    public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
		$this->load->model('m_listOW');
	}

    public function index()
	{
		$id_dept        = 'RLOW';
        $data['id_dept']= $id_dept;
		$data['mst_sales_group'] = $this->_module->get_list_sales_group();
		$this->load->view('report/v_list_ow', $data);
	}


    public function get_data()
	{

        $list = $this->m_listOW->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        	$kode_co_encrypt = encrypt_url($field->kode_co);
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
            $row[] = number_format($field->tot_qty1,2);
            $row[] = $field->gramasi;
            $row[] = $field->nama_handling;
            $row[] = $field->nama_route;
            $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
            $row[] = $field->nama_status;
            $row[] = $field->piece_info;
            $row[] = $field->reff_notes;
            //$row[] = $field->kode_co;
            $row[] = '<a href="'.base_url('ppic/colororder/edit/'.$kode_co_encrypt).'" target="_blank" data-togle="tooltip" title="Lihat Color Order">'.$field->kode_co.'</a>';
            if(!empty($field->kode_co)){
                $row[] = '<a href="javascript:void(0)" data-toggle="tooltip" title="Tracking OW" onclick=view_detail("'.$field->kode_co.'","'.$field->sales_order.'","'.$field->ow.'","'.$field->id_warna.'")> <span class="glyphicon  glyphicon-share"></span></a>';
            }else{
                $row[] = '';
            }
            
            $data[] = $row;
            
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_listOW->count_all(),
            "recordsFiltered" => $this->m_listOW->count_filtered(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}

    public function view_detail_items()// view detail item modal
    {
        $kode_co     = $this->input->post('kode_co');
        $sales_order = $this->input->post('sales_order');
        $id_warna    = $this->input->post('id_warna');
        $ow          = $this->input->post('ow');

        // get sales_color_line by kode
        $get    = $this->m_listOW->get_sales_color_line_by_kode($sales_order,$ow)->row_array();

        $data['sales_order'] = $sales_order;
        $data['kode_co']     = $kode_co;
        $data['nama_produk'] = $get['nama_produk'];
        $data['ow']          = $ow;
        $data['nama_warna']  = $get['nama_warna'];
        $data['qty']         = number_format($get['qty'],2).' '.$get['uom'];

        // get color order by ow
        $data['list']       = $this->m_listOW->get_color_order_detail_by_OW($kode_co,$ow,$sales_order)->result();
        //$data['route']      = $this->m_pengirimanBarang->get_route_by_origin($origin);

        return $this->load->view('modal/v_tracking_list_ow_modal', $data);
    }

    function view_detail_items_panel()
    {   

        $kode_co     = $this->input->post('kode_co');
        $row_order   = $this->input->post('row');
        $sales_order = $this->input->post('sales_order');
        $ow          = $this->input->post('ow');

        // create origin OW
        $origin   = $sales_order.'|'.$kode_co.'|'.$row_order.'|'.$ow;
        $data['origin']     = $origin;
        $data['route']      = $this->m_listOW->get_route_by_origin($origin);

        return $this->load->view('modal/v_tracking_list_ow_modal_panel_body', $data);

    }


    public function export_excel()
    {   

        $tgldari    = $this->input->post('tgldari');
		$tglsampai  = $this->input->post('tglsampai');
		$sc         = $this->input->post('sc');
		$sales_group    = $this->input->post('sales_group');
		$ow             = $this->input->post('ow');
		$produk         = $this->input->post('produk');
		$warna          = $this->input->post('warna');
		$status_ow      = $this->input->post('status_ow');
		$no_ow          = $this->input->post('no_ow');

        $this->load->library('excel');
        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

        // SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'List OW');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        $tgldari_capt    = $this->input->post('tgldari');
		$tglsampai_capt  = $this->input->post('tglsampai');

        // set periode
 		$object->getActiveSheet()->SetCellValue('A3', 'Periode');
        $object->getActiveSheet()->mergeCells('A3:B3');
        $object->getActiveSheet()->SetCellValue('C3', ': '.tgl_indo(date('d-m-Y',strtotime($tgldari_capt))).' - '.tgl_indo(date('d-m-Y',strtotime($tglsampai_capt)) ));
        $object->getActiveSheet()->mergeCells('C3:F3');

        //bold huruf
		$object->getActiveSheet()->getStyle("A1:S5")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

        // header table
    	$table_head_columns  = array('No', 'No.SC', 'Kode MKT', 'No.OW', 'Tgl OW', 'Status OW', 'Nama Produk', 'Warna', 'Qty', 'Uom','Stock GRG[Qty1]', 'Gramasi','Finishing', 'Route', 'L.Jadi','DTI','Piece Info','Reff Notes','CO');
        $column = 0;
        foreach ($table_head_columns as $field) {
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 5, $field);  
    		$column++;
    	}

        $num   = 1;
        $rowCount = 6;
        $list = $this->m_listOW->get_list_ow_by_kode($tgldari,$tglsampai,$sc,$sales_group,$ow,$produk,$warna,$status_ow,$no_ow);
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
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->tot_qty1);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->gramasi);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->nama_handling);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->nama_route);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->lebar_jadi.' '.$val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('P'.$rowCount, $val->nama_status);
			$object->getActiveSheet()->SetCellValue('Q'.$rowCount, $val->piece_info);
			$object->getActiveSheet()->SetCellValue('R'.$rowCount, $val->reff_notes);
			$object->getActiveSheet()->SetCellValue('S'.$rowCount, $val->kode_co);
            $rowCount++;
        }

      
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  

        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="List OW.xls"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');
    }

}