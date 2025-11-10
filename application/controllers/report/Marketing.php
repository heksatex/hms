<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');

/**
 * 
 */
class Marketing extends MY_Controller
{

    public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
		$this->load->model('m_marketing');
        $this->load->library('hanger');
	}

    protected $val_form = array(
        [
            'field' => 'product',
            'lable' => 'Product/Corak',
            'rules' => 'required',
            'errors'=> [
                        'required' => '{field} Harus diisi !'
            ]
        ]
    );

    public function index()
	{
		$id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
		$this->load->view('report/v_marketing', $data);
	}


    function stockbyproduct()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
		$data['mst_sales_group'] = $this->_module->get_list_sales_group();
		$this->load->view('report/v_marketing_view_by_product', $data);
    }

    function stockbyproductgroup()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;

        $data['product']        = $this->input->get('product');
        $data['color']          = $this->input->get('color');
        $data['mkt']            = $this->input->get('cmbMarketing');
        $data['nama_mkt']       = $this->_module->get_nama_sales_Group_by_kode($this->input->get('cmbMarketing')) ?? 'All';
        $this->load->view('report/v_marketing_view_by_product_group', $data);
    }

    function stockbyproductitems()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;

        $data['product']    = $this->input->get('id');
        $data['color']      = $this->input->get('color');
        $data['mkt']        = $this->input->get('cmbMarketing');
        $data['uom_jual']   = $this->input->get('uom');
        $data['lebar_jadi'] = $this->input->get('lebar_jadi');
        $data['nama_mkt']   = $this->_module->get_nama_sales_Group_by_kode($this->input->get('cmbMarketing')) ?? 'All';
        $this->load->view('report/v_marketing_view_by_product_items', $data);
    }


    function get_data_stock_by_product_group()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables();
            $product =$this->input->post('product');
            $color  = $this->input->post('color');
            $mkt    = $this->input->post('marketing');
            $get_data_current = '&product='.urlencode($product).'&color='.urlencode($color).'&cmbMarketing='.urlencode($mkt);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('report/marketing/stockbyproductitems?id='.urlencode($field->corak_remark)).'&lebar_jadi='.urlencode($field->lebar_jadi).'&uom='.urlencode($field->uom_jual).''.$get_data_current.'">'.$field->corak_remark.'</a>';
                // $row[] = '<a href="'.$url_current.'">'.$field->corak_remark.'</a>';
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->gl;
                $row[] = $field->qty1;
                $row[] = $field->uom_jual;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all(),
                "recordsFiltered" => $this->m_marketing->count_filtered(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }


    function get_data_stock_by_product_items()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables2();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $image = "/upload/product/" . $field->kode_produk . ".jpg";
                if (is_file(FCPATH . $image)) {
                    // $link  = is_file(FCPATH . $imageThumb) ? base_url($imageThumb) : base_url($image);
                    $link  = base_url($image);
                }else{
                    $link  = base_url("/upload/product/default.jpg");
                }
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $link;
                $row[] = $field->kode_produk;
                $row[] = $field->lot;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->qty_jual.' '.$field->uom_jual;
                $row[] = $field->qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->lokasi_fisik;
                $row[] = $field->lot_asal;
                $row[] = $field->sales_order;
                $row[] = $field->no_pl;
                $row[] = $field->umur;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all2(),
                "recordsFiltered" => $this->m_marketing->count_filtered2(),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }

    function export_excel_view_by_product()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_datatables2_excel();

        $product    = $this->input->post('product');
        $color      = $this->input->post('color');
        $marketing  = $this->input->post('marketing'); // MKT001
        $lebar_jadi = $this->input->post('lebar_jadi');
        $uom_jual   = $this->input->post('uom_jual');

        if($marketing!= 'All'){
            $marketing = $this->_module->get_nama_sales_Group_by_kode($marketing);
        }else{
            $marketing = 'All';
        }

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Report Marketing View By Product');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        // SET Filter
 		$object->getActiveSheet()->SetCellValue('A3', 'Product / Corak');
 		$object->getActiveSheet()->SetCellValue('B3', ': '.$product);
		$object->getActiveSheet()->mergeCells('B3:D3');

        $object->getActiveSheet()->SetCellValue('A4', 'Warna');
 		$object->getActiveSheet()->SetCellValue('B4', ': '.$color);
		$object->getActiveSheet()->mergeCells('B4:D4');

        $object->getActiveSheet()->SetCellValue('A5', 'Marketing');
 		$object->getActiveSheet()->SetCellValue('B5', ': '.$marketing);
		$object->getActiveSheet()->mergeCells('B5:D5');

        $object->getActiveSheet()->SetCellValue('G3', 'Lebar Jadi');
 		$object->getActiveSheet()->SetCellValue('H3', ': '.$lebar_jadi);
		$object->getActiveSheet()->mergeCells('H3:I3');

        $object->getActiveSheet()->SetCellValue('G4', 'Uom');
 		$object->getActiveSheet()->SetCellValue('H4', ': '.$uom_jual);
		$object->getActiveSheet()->mergeCells('H4:I4');

       //bold huruf
		$object->getActiveSheet()->getStyle("A1:O7")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Lot', 'Corak' , 'Warna', 'Lebar Jadi', 'Uom lebar', 'Qty1 [Jual]', 'Uom1 [JUAL]', 'Qty2 [JUAL]', 'Uom2 [JUAL]', 'Lokasi Fisik / Rak ', 'Lok / KP', 'SO / SC', 'Picklist (PL)', 'Umur (Hari)');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 7, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
    	$loop = 0;
    	foreach ($index_header as $val) {
    		
            $object->getActiveSheet()->getStyle($val.'7')->applyFromArray($styleArray);
        }

        $rowCount  = 8;
        $num       = 1;
        foreach ($get_data as $val) {

			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->corak_remark);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->warna_remark);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->lebar_jadi);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->qty_jual);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->uom_jual);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->qty2_jual);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->uom2_jual);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->lokasi_fisik);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->lot_asal);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->sales_order);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->no_pl);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->umur);

            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray);
		
	        $rowCount++;
		}


        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = "Report Marketing View By Product.xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }

    function stockbylokasi()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['mst_sales_group'] = $this->_module->get_list_sales_group();
        $this->load->view('report/v_marketing_view_by_lokasi', $data);
    }

    function stockbylokasiitems()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['lokasi']= $this->input->get('lokasi');
        $data['cmbMarketing']= $this->input->get('cmbMarketing');
        $data['nama_mkt']   = $this->_module->get_nama_sales_Group_by_kode($this->input->get('cmbMarketing')) ?? 'All';
        $this->load->view('report/v_marketing_view_by_lokasi_items', $data);
        
    }


    function get_data_stock_by_lokasi()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables3();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->lot;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->qty_jual.' '.$field->uom_jual;
                $row[] = $field->qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->lokasi_fisik;
                $row[] = $field->lot_asal;
                $row[] = $field->sales_order;
                $row[] = $field->nama_sales_group;
                $row[] = $field->no_pl;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all3(),
                "recordsFiltered" => $this->m_marketing->count_filtered3(),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
    }

    function export_excel_view_by_lokasi()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_datatables3_excel();

        $lokasi          = $this->input->post('lokasi');
        $cmbMarketing    = $this->input->post('cmbMarketing');

        $nama_mkt        = $this->_module->get_nama_sales_Group_by_kode($cmbMarketing) ?? 'All';


        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Report Marketing View By Lokasi');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        // SET Filter
 		$object->getActiveSheet()->SetCellValue('A3', 'Lokasi');
 		$object->getActiveSheet()->SetCellValue('B3', ': '.$lokasi);
		$object->getActiveSheet()->mergeCells('B3:D3');

        $object->getActiveSheet()->SetCellValue('A4', 'Marketing');
 		$object->getActiveSheet()->SetCellValue('B4', ': '.$nama_mkt);
		$object->getActiveSheet()->mergeCells('B4:D4');

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
        $table_head_columns  = array('No', 'Lot', 'Corak' , 'Warna', 'Lebar Jadi', 'Uom lebar', 'Qty1 [Jual]', 'Uom1 [JUAL]', 'Qty2 [JUAL]', 'Uom2 [JUAL]', 'Lokasi Fisik / Rak ', 'Lok / KP', 'SO / SC','Marketing','Picklist (PL)');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 6, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
    	$loop = 0;
    	foreach ($index_header as $val) {
    		
            $object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);
        }

        $rowCount  = 7;
        $num       = 1;
        foreach ($get_data as $val) {

			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->corak_remark);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->warna_remark);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->lebar_jadi);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->qty_jual);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->uom_jual);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->qty2_jual);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->uom2_jual);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->lokasi_fisik);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->lot_asal);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->sales_order);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->nama_sales_group);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->no_pl);

            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray);
		
	        $rowCount++;
		}


        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = "Report Marketing View By Lokasi.xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }

    function gradeexpiredgjd()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
		$data['mst_sales_group'] = $this->_module->get_list_sales_group();
		$data['mst_grade']       = $this->_module->get_list_grade();
        $this->load->view('report/v_marketing_grade_expired_gjd', $data);
        
    }


    function gradeexpiredgjdgroup()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;

        $data['product']        = $this->input->get('product');
        $data['color']          = $this->input->get('color');
        $data['mkt']            = $this->input->get('cmbMarketing');
        $data['grade']          = $this->input->get('cmbGrade');
        $data['expired']        = $this->input->get('cmbExpired');
        $data['nama_mkt']       = $this->_module->get_nama_sales_Group_by_kode($this->input->get('cmbMarketing')) ?? 'All';
        $this->load->view('report/v_marketing_grade_expired_gjd_group', $data);
    }

    function get_data_stock_expired_grade_group()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables4();
            $product =$this->input->post('product');
            $color  = $this->input->post('color');
            $mkt    = $this->input->post('marketing');
            $grade    = $this->input->post('grade');
            $expired  = $this->input->post('expired');
            $get_data_current = '&color='.urlencode($color).'&cmbMarketing='.urlencode($mkt).'&cmbGrade='.urlencode($grade).'&cmbExpired='.urlencode($expired);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('report/marketing/gradeexpiredgjditems?id='.urlencode($field->corak_remark)).'&lebar_jadi='.urlencode($field->lebar_jadi).'&uom='.urlencode($field->uom_jual).''.$get_data_current.'">'.$field->corak_remark.'</a>';
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->gl;
                $row[] = $field->qty1;
                $row[] = $field->uom_jual;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all4(),
                "recordsFiltered" => $this->m_marketing->count_filtered4(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group4()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }


    function gradeexpiredgjditems()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;

        $data['product']        = $this->input->get('id');
        $data['color']          = $this->input->get('color');
        $data['mkt']            = $this->input->get('cmbMarketing');
        $data['uom_jual']       = $this->input->get('uom');
        $data['lebar_jadi']     = $this->input->get('lebar_jadi');
        $data['grade']          = $this->input->get('cmbGrade');
        $data['expired']        = $this->input->get('cmbExpired');
        $data['nama_mkt']       = $this->_module->get_nama_sales_Group_by_kode($this->input->get('cmbMarketing')) ?? 'All';
        $this->load->view('report/v_marketing_grade_expired_gjd_items', $data);
    }


    function get_data_stock_expired_grade_items()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables5();
            $data = array();
            $no = $_POST['start'];
            $tgl_sekarang = date('Y-m-d');
            $tgl_sebelum = date('Y-m-d', strtotime('-3 month', strtotime($tgl_sekarang)));
            foreach ($list as $field) {

                if(date('Y-m-d', strtotime($field->create_date)) < $tgl_sebelum){
                    $ket_kain = 'Expired';
                }else{
                    $ket_kain = '';
                }

                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->create_date;
                $row[] = $field->lot;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->qty_jual.' '.$field->uom_jual;
                $row[] = $field->qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->lokasi_fisik;
                $row[] = $field->lot_asal;
                $row[] = $field->sales_order;
                $row[] = $field->no_pl;
                $row[] = $field->umur;
                $row[] = $ket_kain;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all5(),
                "recordsFiltered" => $this->m_marketing->count_filtered5(),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }

    function export_excel_grade_expired()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_datatables5_excel();

        $product    = $this->input->post('product');
        $color      = $this->input->post('color');
        $marketing  = $this->input->post('marketing'); // MKT001
      ;

        $product        = $this->input->post('product');
        $color          = $this->input->post('color');
        $mkt            = $this->input->post('marketing');
        $lebar_jadi     = $this->input->post('lebar_jadi');
        $uom_jual       = $this->input->post('uom_jual');
        $grade          = $this->input->post('grade');
        $expired        = $this->input->post('expired');
        $nama_mkt       = $this->_module->get_nama_sales_Group_by_kode($this->input->post('mkt')) ?? 'All';

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Report Grade & Expired');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        // SET Filter
 		$object->getActiveSheet()->SetCellValue('A3', 'Product / Corak');
 		$object->getActiveSheet()->SetCellValue('B3', ': '.$product);
		$object->getActiveSheet()->mergeCells('B3:D3');

        $object->getActiveSheet()->SetCellValue('A4', 'Warna');
 		$object->getActiveSheet()->SetCellValue('B4', ': '.$color);
		$object->getActiveSheet()->mergeCells('B4:D4');

        $object->getActiveSheet()->SetCellValue('A5', 'Marketing');
 		$object->getActiveSheet()->SetCellValue('B5', ': '.$marketing);
		$object->getActiveSheet()->mergeCells('B5:D5');

        $object->getActiveSheet()->SetCellValue('A6', 'Grade');
 		$object->getActiveSheet()->SetCellValue('B6', ': '.$grade);
		$object->getActiveSheet()->mergeCells('B6:D6');

        $object->getActiveSheet()->SetCellValue('A7', 'Expired');
 		$object->getActiveSheet()->SetCellValue('B7', ': '.$expired);
		$object->getActiveSheet()->mergeCells('B7:D7');

        $object->getActiveSheet()->SetCellValue('G3', 'Lebar Jadi');
 		$object->getActiveSheet()->SetCellValue('H3', ': '.$lebar_jadi);
		$object->getActiveSheet()->mergeCells('H3:I3');

        $object->getActiveSheet()->SetCellValue('G4', 'Uom');
 		$object->getActiveSheet()->SetCellValue('H4', ': '.$uom_jual);
		$object->getActiveSheet()->mergeCells('H4:I4');

       //bold huruf
		$object->getActiveSheet()->getStyle("A1:Q9")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Tanggal dibuat' ,'Lot', 'Corak' , 'Warna', 'Lebar Jadi', 'Uom lebar', 'Qty1 [Jual]', 'Uom1 [JUAL]', 'Qty2 [JUAL]', 'Uom2 [JUAL]', 'Lokasi Fisik / Rak ', 'Lok / KP', 'SO / SC','Picklist (PL)','Umur (Hari)','Keterangan');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 9, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q');
    	$loop = 0;
    	foreach ($index_header as $val) {
    		
            $object->getActiveSheet()->getStyle($val.'9')->applyFromArray($styleArray);
        }
        $tgl_sekarang = date('Y-m-d');
        $tgl_sebelum = date('Y-m-d', strtotime('-3 month', strtotime($tgl_sekarang)));
        $rowCount  = 10;
        $num       = 1;
        foreach ($get_data as $val) {

            if(date('Y-m-d', strtotime($val->create_date)) < $tgl_sebelum){
                $ket_kain = 'Expired';
            }else{
                $ket_kain = '';
            }

			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->create_date);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->corak_remark);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->warna_remark);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->lebar_jadi);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->qty_jual);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->uom_jual);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->qty2_jual);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->uom2_jual);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->lokasi_fisik);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->lot_asal);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->sales_order);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->no_pl);
			$object->getActiveSheet()->SetCellValue('P'.$rowCount, $val->umur);
			$object->getActiveSheet()->SetCellValue('Q'.$rowCount, $ket_kain);

            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('P'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('Q'.$rowCount)->applyFromArray($styleArray);
		
	        $rowCount++;
		}


        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = "Report Grade & Expired.xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }



    function listwarnabyproduct()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $this->load->view('report/v_marketing_warna_by_product', $data);
        
    }


    function warnabyproductgroup()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['product']= $this->input->get('product');
        $this->load->view('report/v_marketing_warna_by_product_group', $data);
        
    }

    function warnabyproductgroupwarna()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['product']= $this->input->get('id');
        $this->load->view('report/v_marketing_warna_by_product_warna_group', $data);
        
    }

    function warnabyproductitems()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['product']= $this->input->get('product');
        $data['color']  = $this->input->get('id');
        $this->load->view('report/v_marketing_warna_by_product_items', $data);
        
    }

    function get_data_stock_by_warna_group()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables6();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('report/marketing/warnabyproductgroupwarna?id='.urlencode($field->corak_remark)).'">'.$field->corak_remark.'</a>';
                $row[] = $field->gl;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all6(),
                "recordsFiltered" => $this->m_marketing->count_filtered6(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group6()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }


    function get_data_stock_by_warna_product_group()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $product =$this->input->post('product');
            $get_data_current = '&product='.urlencode($product);
            $list = $this->m_marketing->get_datatables7();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('report/marketing/warnabyproductitems?id='.urlencode($field->warna_remark)).''.$get_data_current.'">'.$field->warna_remark.'</a>';
                $row[] = $field->gl;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all7(),
                "recordsFiltered" => $this->m_marketing->count_filtered7(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group7()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }


    function get_data_stock_by_warna_product_items()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables8();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->lot;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->lebar_jadi.' '.$field->uom_lebar_jadi;
                $row[] = $field->qty_jual.' '.$field->uom_jual;
                $row[] = $field->qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->lokasi_fisik;
                $row[] = $field->lot_asal;
                $row[] = $field->sales_order;
                $row[] = $field->no_pl;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all8(),
                "recordsFiltered" => $this->m_marketing->count_filtered8(),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }

     function export_excel_view_by_warna()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_datatables8_excel();

        $product    = $this->input->post('product');
        $color    = $this->input->post('color');


        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Report Marketing Warna By Product');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        // SET Filter
 		$object->getActiveSheet()->SetCellValue('A3', 'Product / Corak');
 		$object->getActiveSheet()->SetCellValue('B3', ': '.$product);
		$object->getActiveSheet()->mergeCells('B3:D3');

        $object->getActiveSheet()->SetCellValue('A4', 'Warna');
 		$object->getActiveSheet()->SetCellValue('B4', ': '.$color);
		$object->getActiveSheet()->mergeCells('B4:D4');

       //bold huruf
		$object->getActiveSheet()->getStyle("A1:N6")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Lot', 'Corak' , 'Warna', 'Lebar Jadi', 'Uom lebar', 'Qty1 [Jual]', 'Uom1 [JUAL]', 'Qty2 [JUAL]', 'Uom2 [JUAL]', 'Lokasi Fisik / Rak ', 'Lok / KP', 'SO / SC', 'Picklist (PL)');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 6, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
    	$loop = 0;
    	foreach ($index_header as $val) {
    		
            $object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);
        }

        $rowCount  = 7;
        $num       = 1;
        foreach ($get_data as $val) {

			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->corak_remark);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->warna_remark);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->lebar_jadi);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->qty_jual);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->uom_jual);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->qty2_jual);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->uom2_jual);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->lokasi_fisik);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->lot_asal);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->sales_order);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->no_pl);

            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
		
	        $rowCount++;
		}


        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = "Report Marketing Warna By Product.xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }

    function stockhistorygjd()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $this->load->view('report/v_marketing_stock_history', $data);
    }

    // function get_data_stock_history()
    // {
    //     $tgldari   = date("Y-m-d H:i:s", strtotime($this->input->post('tgl_dari')));
    //     $tglsampai = date("Y-m-d 23:59:59", strtotime($this->input->post('tgl_sampai')));

    //     $get_mkt = $this->m_marketing->get_list_mst_sales_group();
    //     $data_stock_history = [];
    //     $tmp_tgl = "";
    //     foreach($get_mkt as $val){

    //         $data_stock = $this->m_marketing->get_data_stock_by_mkt($tgldari,$tglsampai,$val->nama_sales_group);
    //         $tmp_stock  = "";
    //         foreach($data_stock as $st){
    //             $tmp_stock .= floatval($st->l_stock).", ";
    //             $tmp_tgl   .= date('d F Y', strtotime($st->tanggal)).", ";
    //         }
    //         $tmp_stock = rtrim($tmp_stock, ', ');
    //         $arr_data  = [];
    //         $data_stock_history[] = array(
    //                                 "name" => $val->nama_sales_group,
    //                                 "data" => ($tmp_stock),
    //         );
            
    //     }

        
    //     $callback  = array('status'=>'success', 'result'=>$data_stock_history, 'periode'=>$tmp_tgl);
    //     echo json_encode($callback);


    // }


    function get_data_stock_history()
    {
        try {
            $tgldari   = date("Y-m-d 00:00:00", strtotime($this->input->post('tgl_dari')));
            $tglsampai = date("Y-m-d 23:59:59", strtotime($this->input->post('tgl_sampai')));

            $get_mkt = $this->m_marketing->get_list_mst_sales_group();

            $data_stock_history = [];
            $periode_tanggal = [];

            // Loop untuk setiap marketing group
            foreach ($get_mkt as $val) {
                $data_stock = $this->m_marketing->get_data_stock_by_mkt($tgldari, $tglsampai, $val->nama_sales_group);

                $tmp_stock = [];
                if (empty($periode_tanggal)) {
                    // Ambil tanggal hanya sekali dari marketing pertama
                    foreach ($data_stock as $st) {
                        $periode_tanggal[] = date('d F Y', strtotime($st->tanggal));
                    }
                }

                foreach ($data_stock as $st) {
                    $tmp_stock[] = floatval($st->l_stock);
                }

                $data_stock_history[] = [
                    "name" => $val->nama_sales_group,
                    "data" => $tmp_stock
                ];
            }

            $callback = [
                'status'  => 'success',
                'result'  => $data_stock_history,
                'periode' => $periode_tanggal
            ];

            $this->output
                ->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($callback));
        } catch (Exception $e) {
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode([
                    'status'  => 'error',
                    'message' => $e->getMessage()
                ]));
        }
    }


    function get_dataTable_stock_history()
    {
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables9();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->tanggal;
                $row[] = number_format($field->hen);
                $row[] = number_format($field->mei);
                $row[] = number_format($field->ts);
                $row[] = number_format($field->vi);
                $row[] = number_format($field->al);
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all9(),
                "recordsFiltered" => $this->m_marketing->count_filtered9(),
                "data" => $data,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }

    }

    function export_excel_stock_history()
    {
        $this->load->library('excel');
		ob_start();

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Stock History (GJD)');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        $tgldari = $this->input->post('tgldari');
        $tglsampai = $this->input->post('tglsampai');

        $tgldari_capt  = $tgldari;
		$tglsampai_capt = $tglsampai;

        // set periode
 		$object->getActiveSheet()->SetCellValue('A3', 'Periode');
		$object->getActiveSheet()->mergeCells('A3:B3');
 		$object->getActiveSheet()->SetCellValue('C3', ': '.tgl_indo(date('d-m-Y',strtotime($tgldari_capt))).' - '.tgl_indo(date('d-m-Y',strtotime($tglsampai_capt)) ));
		$object->getActiveSheet()->mergeCells('C3:F3');


        //bold huruf
		$object->getActiveSheet()->getStyle("A1:U6")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

        // header table
    	$table_head_columns  = array('No', 'Tanggal','NMBB', 'NMBL','TMBX','TMBL', 'ALL');

        $column = 0;
    	foreach ($table_head_columns as $field) {
	    	$object->getActiveSheet()->setCellValueByColumnAndRow($column, 6, $field);  
    		$column++;
    	}

        $items = $this->m_marketing->query_9_excel();
    	$num   = 1;
        $rowCount = 7;
		foreach ($items as $val) {
            $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->tanggal);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->hen);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->mei);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->ts);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->vi);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->al);
            $rowCount++;
        }

        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = "Stock History GJD.xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));
    }


    function readygoodsgroup()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['title']  = 'View Product Ready Goods (GJD)';
        $data['proofing'] = 'no';
        $this->load->view('report/v_marketing_view_ready_goods_group', $data);
    }


    function get_data_ready_goods_group()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables10();
            $proofing = $this->input->post('proofing');
            if($proofing == 'yes'){
                $link = "groupcolourproofing";
            }else{
                $link = "readygoodsgroupcolour";
            }
            // $get_data_current = '&product='.urlencode($product).'&color='.urlencode($color).'&cmbMarketing='.urlencode($mkt);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('report/marketing/'.$link.'?id='.urlencode($field->corak_remark)).'&lebar_jadi='.urlencode($field->lebar_jadi).'&uom_lebar_jadi='.urlencode($field->uom_lebar_jadi).'&uom_jual='.urlencode($field->uom_jual).'&uom2_jual='.urlencode($field->uom2_jual).'">'.$field->corak_remark.'</a>';
                $row[] = $field->total_warna;
                $row[] = $field->lebar_jadi_merge;
                $row[] = $field->total_qty_jual.' '.$field->uom_jual;
                $row[] = $field->total_qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->gl;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all10(),
                "recordsFiltered" => $this->m_marketing->count_filtered10(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group10()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }


    function readygoodsgroupcolour()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['product']= $this->input->get('id');
        $data['lebar_jadi']= $this->input->get('lebar_jadi');
        $data['uom_lebar_jadi']= $this->input->get('uom_lebar_jadi');
        $data['uom_jual']= $this->input->get('uom_jual');
        $data['uom2_jual']= $this->input->get('uom2_jual');
        $data['title']    = 'View Product Ready Goods (GJD)';
        $data['proofing'] = 'no';
        $this->load->view('report/v_marketing_view_ready_goods_group_colour', $data);
    }

    function groupcolourproofing()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['product']= $this->input->get('id');
        $data['lebar_jadi']= $this->input->get('lebar_jadi');
        $data['uom_lebar_jadi']= $this->input->get('uom_lebar_jadi');
        $data['uom_jual']= $this->input->get('uom_jual');
        $data['uom2_jual']= $this->input->get('uom2_jual');
        $data['title']    = 'View Product Proofing (GJD)';
        $data['proofing'] = 'yes';
        $this->load->view('report/v_marketing_view_ready_goods_group_colour', $data);
    }
    
    function get_data_ready_goods_group_colour()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $proofing = $this->input->post('proofing');
            if($proofing == 'yes'){
                $link = "itemsproofing";
            }else{
                $link = "readygoodsitems";
            }
            $list = $this->m_marketing->get_datatables11();
            // $get_data_current = '&product='.urlencode($product).'&color='.urlencode($color).'&cmbMarketing='.urlencode($mkt);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('report/marketing/'.$link.'?id='.urlencode($field->corak_remark)).'&warna_remark='.urlencode($field->warna_remark).'&lebar_jadi='.urlencode($field->lebar_jadi).'&uom_lebar_jadi='.urlencode($field->uom_lebar_jadi).'&uom_jual='.urlencode($field->uom_jual).'&uom2_jual='.urlencode($field->uom2_jual).'">'.$field->corak_remark.'</a>';
                $row[] = $field->warna_remark;
                $row[] = $field->lebar_jadi_merge;
                $row[] = $field->total_qty_jual.' '.$field->uom_jual;
                $row[] = $field->total_qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->gl;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all11(),
                "recordsFiltered" => $this->m_marketing->count_filtered11(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group11()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
    }


    function readygoodsitems()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['product']= $this->input->get('id');
        $data['color']  = $this->input->get('warna_remark');
        $data['lebar_jadi']   = $this->input->get('lebar_jadi');
        $data['uom_lebar_jadi']   = $this->input->get('uom_lebar_jadi');
        $data['uom_jual']    = $this->input->get('uom_jual');
        $data['uom2_jual']   = $this->input->get('uom2_jual');
        $data['title']    = 'View Product Ready Goods (GJD)';
        $data['proofing'] = 'no';
        $this->load->view('report/v_marketing_view_ready_goods_items', $data);
    }

    function itemsproofing()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['product']= $this->input->get('id');
        $data['color']  = $this->input->get('warna_remark');
        $data['lebar_jadi']   = $this->input->get('lebar_jadi');
        $data['uom_lebar_jadi']   = $this->input->get('uom_lebar_jadi');
        $data['uom_jual']    = $this->input->get('uom_jual');
        $data['uom2_jual']   = $this->input->get('uom2_jual');
        $data['title']    = 'View Product Proofing (GJD)';
        $data['proofing'] = 'yes';
        $this->load->view('report/v_marketing_view_ready_goods_items', $data);
    }


    function get_data_ready_goods_items()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables12();
            $data = array();
            $no = $_POST['start'];
            $link  = '';
            $gmbr  = '';
            foreach ($list as $field) {
                $image = "/upload/product/" . $field->kode_produk . ".jpg";
                $imageThumb = "/upload/product/thumb-" . $field->kode_produk . ".jpg";
                if (is_file(FCPATH . $image)) {
                    // $link  = is_file(FCPATH . $imageThumb) ? base_url($imageThumb) : base_url($image);
                    $link  = base_url($image);
                }else{
                    $link  = base_url("/upload/product/default.jpg");
                }
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $link;
                $row[] = $field->kode_produk;
                $row[] = $field->create_date;
                $row[] = $field->lot;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->lebar_jadi." ".$field->uom_lebar_jadi;
                $row[] = $field->qty_jual." ".$field->uom_jual;
                $row[] = $field->qty2_jual." ".$field->uom2_jual;
                $row[] = $field->lokasi;
                $row[] = $field->lokasi_fisik;
                $row[] = $field->umur;
                $data[] = $row;

            }
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all12(),
                "recordsFiltered" => $this->m_marketing->count_filtered12(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group12()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
    }

    function download_image()
    {
        // ob_start();
        $data_produk = $this->input->post('produk');
        $caption     = $this->input->post('caption');

        if (strpos($data_produk, 'jpg') !== false) {
            $name_image = $data_produk;
        } else {
            $name_image = $data_produk.".jpg";
        }
        
        $sourceImage = FCPATH.'/upload/product/'.$name_image;
        $font        = FCPATH.'/font/arial-narrow-7.ttf';

        // Create the size of image or blank image 
        $image = $sourceImage; 
        // Set the background color of image 
        // $background_color = imagecolorallocate($image, 0, 153, 0); 
        // Set the text color of image 
        list ($width, $height) = getimagesize($image);
        $imageProperties = imagecreatetruecolor($width, $height);
        $targetLayer = imagecreatefromjpeg($image);
        imagecopyresampled($imageProperties, $targetLayer, 0, 0, 0, 0, $width, $height, $width, $height);
        $text_color = imagecolorallocate($imageProperties, 255, 255, 255); 
        $img_w = $width;
        $width1 = imagefontwidth(15*($width / 600)) * strlen($caption);
        // Function to create image which contains string. 
        // imagestring($targetLayer, 5, ($img_w/2)-($width1/2), $height/2,  $caption, $text_color); 
        // $font = './arial.ttf';
        // imagettftext($targetLayer, 10, 45, $x, $y, $text_color, $font, $caption);
        // imagestring($image, 3, 160, 120,  "A computer science portal", $text_color); 
        $bbox = imagettfbbox(15*($width / 600), 0, $font, $caption);
        $x = $bbox[0] + (imagesx($targetLayer) / 2) - ($bbox[4] / 2) + 10; 
        $y = $bbox[1] + (imagesy($targetLayer) / 2) - ($bbox[5] / 2) - 5;
        $x2 = $bbox[2] - $bbox[0];
        $y2 = $bbox[5] - $bbox[3];
        $red = imagecolorallocate($targetLayer, 0, 0, 0);
        // imageline($targetLayer, $x - 10, $y - 40, $x - 10, $y, $text_color); // RIGHT
        // imageline($targetLayer, $x - 10, $y - 40, $x - 10, $y + $width1 * 2, $text_color); // UP
        // // imageline($targetLayer, $x + $x + 10, $y - 40, $x, $y, $text_color);
        // imageline($targetLayer, $x + $width1 * 2 , $y - 40, $x + $width1 * 2, $y, $text_color); // LEFT
        imagefilledrectangle($targetLayer, $x, $y, $x+$x2, $y+$y2, $red);
        imagettftext($targetLayer, 15*($width / 600), 0, $x, $y, $text_color, $font, $caption);
        
        $data = ob_get_clean();
        header("Content-Type: image/jpg"); 
        
        imagejpeg($targetLayer); 
        imagedestroy($targetLayer); 
    }


    function export_excel_ready_goods_group()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_datatables10_excel();

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);
        $proofing = $this->input->post('proofing');
        if($proofing == 'yes') {
            $title = 'Report Proofing (GJD)';
        }else{
            $title = 'Report Ready Goods (GJD)';
        }
        
    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1',$title);
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

       //bold huruf
		$object->getActiveSheet()->getStyle("A1:Q3")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Corak' , 'Jml Warna', 'Lebar Jadi', 'Uom lebar', 'Qty1 [JUAL]', 'Uom1 [JUAL]', 'Qty2 [JUAL]', 'Uom2 [JUAL]',  'Gl/Lot');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J');
    	$loop = 0;
    	foreach ($index_header as $val) {
            $object->getActiveSheet()->getStyle($val.'3')->applyFromArray($styleArray);
        }
        $rowCount  = 4;
        $num       = 1;
        foreach ($get_data as $val) {
			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->corak_remark);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->total_warna);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->lebar_jadi);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->total_qty_jual);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->uom_jual);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->total_qty2_jual);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->uom2_jual);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->gl);

            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
		
	        $rowCount++;
		}
        
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = $title.".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }


    function export_excel_ready_goods()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_datatables12_excel();

        $product    = $this->input->post('product');
        $color      = $this->input->post('color');
        $lebar_jadi = $this->input->post('lebar_jadi');
        $uom_lebar_jadi      = $this->input->post('uom_lebar_jadi');
        $uom_jual      = $this->input->post('uom_jual');
        $uom2_jual      = $this->input->post('uom2_jual');

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

        $proofing = $this->input->post('proofing');
        if($proofing == 'yes') {
            $title = 'Report Proofing (GJD)';
        }else{
            $title = 'Report Ready Goods (GJD)';
        }

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', $title);
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        // SET Filter
 		$object->getActiveSheet()->SetCellValue('A3', 'Product / Corak');
 		$object->getActiveSheet()->SetCellValue('B3', ': '.$product);
		$object->getActiveSheet()->mergeCells('B3:D3');

        $object->getActiveSheet()->SetCellValue('A4', 'Warna');
 		$object->getActiveSheet()->SetCellValue('B4', ': '.$color);
		$object->getActiveSheet()->mergeCells('B4:D4');

        // $object->getActiveSheet()->SetCellValue('A5', 'Lebar Jadi');
 		// $object->getActiveSheet()->SetCellValue('B5', ': '.$uom);
		// $object->getActiveSheet()->mergeCells('B5:D5');

       //bold huruf
		$object->getActiveSheet()->getStyle("A1:Q7")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Tanggal dibuat' ,'Lot', 'Corak' , 'Warna', 'Lebar Jadi', 'Uom lebar', 'Qty1 [JUAL]', 'Uom1 [JUAL]', 'Qty2 [JUAL]', 'Uom2 [JUAL]', 'Lokasi', 'Lokasi Fisik / Rak ', 'Umur (Hari)');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 7, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
    	$loop = 0;
    	foreach ($index_header as $val) {
            $object->getActiveSheet()->getStyle($val.'7')->applyFromArray($styleArray);
        }
        $rowCount  = 8;
        $num       = 1;
        foreach ($get_data as $val) {

			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->create_date);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->corak_remark);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->warna_remark);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->lebar_jadi);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->qty_jual);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->uom_jual);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->qty2_jual);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->uom2_jual);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->lokasi);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->lokasi_fisik);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->umur);

            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
		
	        $rowCount++;
		}
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = $title.".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }


    function readygoodscategory()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $this->load->view('report/v_marketing_view_ready_goods_category', $data);
    }


    function get_data_ready_goods_category()
    {
  
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables13();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                // $image = "/upload/product/" . $field->file_name;
                // if (is_file(FCPATH . $image)) {
                //     $link  = base_url($image);
                // }else{
                //     $link  = base_url("/upload/product/default.jpg");
                // }

                $no++;
                $row = array();
                $row[] = $no;
                // $row[] = $link;
                // $row[] = $field->file_name;
                $row[] = $field->cat_id;
                $row[] = $field->corak;
                $row[] = $field->warna;
                $row[] = $field->lebar_Jadi." ".$field->uom_lebar_jadi;
                $row[] = $field->qty_jual.' '.$field->uom_jual;
                $row[] = $field->qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->jumlah_lot;
                $row[] = $field->corak.",".$field->warna.",".$field->lebar_Jadi." ".$field->uom_lebar_jadi;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all13(),
                "recordsFiltered" => $this->m_marketing->count_filtered13(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group13(),
                "date_history"=>$this->m_marketing->get_last_date_history()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        } 
        
    }

    function get_data_ready_goods_category_changed()
    {
        if(isset($_POST['start']) && isset($_POST['draw'])){

            // delete table
            $this->m_marketing->delete_table();
            // get_past 2 tanggal
            $limit = '1,2';
            $past_date = $this->m_marketing->get_last_date_history_2();
            $tmp_insert = [];
            $get_data_ = $this->m_marketing->get_data_all_13($past_date);
            foreach($get_data_ as $val2){
                $tmp_insert[] = array(
                            'tanggal'       => $val2->tanggal,
                            'cat_id'        => $val2->cat_id,
                            'corak'         => $val2->corak,
                            'warna'         => $val2->warna,
                            'lebar_Jadi'    => $val2->lebar_Jadi,
                            'uom_lebar_jadi'=> $val2->uom_lebar_jadi,
                );
            }

            if($tmp_insert){
                $this->m_marketing->insert_data_last_date($tmp_insert);
            }

            $tmp_update = array();
            $tmp_insert2 = array();
            $where_update = array();
            // get date last
            $last_date = $this->m_marketing->get_last_date_history();
            $get_data2_ = $this->m_marketing->get_data_all_13($last_date);
            foreach($get_data2_ as $pd){

                // cek data in table
                $cek_dt = $this->m_marketing->cek_data_in_table($pd->cat_id,$pd->corak,$pd->warna,$pd->lebar_Jadi,$pd->uom_lebar_jadi);
                if($cek_dt){// update
                    $tmp_update = array(
                                'cat_id_last'     =>$pd->cat_id,
                                'corak_last'      =>$pd->corak,
                                'warna_last'      =>$pd->warna,
                                'lebar_jadi_last' =>$pd->lebar_Jadi,
                                'uom_lebar_jadi_last' =>$pd->uom_lebar_jadi
                    );
                    $where_update[] = array(
                                'corak'      =>$pd->corak,
                                'warna'      =>$pd->warna,
                                'lebar_jadi' =>$pd->lebar_Jadi,
                                'uom_lebar_jadi' =>$pd->uom_lebar_jadi
                    );
                    $this->m_marketing->update_table_changed($tmp_update,$pd->corak,$pd->warna,$pd->lebar_Jadi,$pd->uom_lebar_jadi);
                }else{
                    //insert
                    $tmp_insert2[] = array(
                                'cat_id_last'     =>$pd->cat_id,
                                'corak_last'      =>$pd->corak,
                                'warna_last'      =>$pd->warna,
                                'lebar_jadi_last' =>$pd->lebar_Jadi,
                                'uom_lebar_jadi_last' =>$pd->uom_lebar_jadi,
                                'action'          => 'ADD'
                    );
                    // $this->m_marketing->insert_table_changed($tmp_insert2);
                    // $tmp_update = array(
                    //                 'action' => 'REMOVE'
                    // );
                    // $this->m_marketing->update_table_changed($tmp_update,$pd->corak,$pd->warna,$pd->lebar_Jadi,$pd->uom_lebar_jadi);

                }
            }

            if(!empty($tmp_update)) {
                // $this->m_marketing->update_table_changed($tmp_update,$where_update);
            }

            if(!empty($tmp_insert2)) {
                $this->m_marketing->insert_data_last_date($tmp_insert2);

            }

            // get data remove 
            $where_  = array('cat_id_last'=>'');
            $tmp_id  = array();
            $get_dt_remove = $this->m_marketing->get_data_table_changed_all($where_);
            foreach($get_dt_remove as $gtr){
                $tmp_id = array('action'=>"REMOVE");
                $this->m_marketing->update_table_changed2($gtr->id, $tmp_id);
            }


            $total_remove = $this->m_marketing->get_total_action('REMOVE');
            $total_add    = $this->m_marketing->get_total_action('ADD');

            $list = $this->m_marketing->get_datatables14();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->cat_id;
                $row[] = $field->corak;
                $row[] = $field->warna;
                $row[] = $field->lebar_Jadi." ".$field->uom_lebar_jadi;
                $row[] = $field->action;
                $row[] = $field->id;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all14(),
                "recordsFiltered" => $this->m_marketing->count_filtered14(),
                "data" => $data,
                "past_date" => $past_date,
                "last_date" => $last_date,
                'total_add' => $total_add,
                'total_remove' => $total_remove,
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }

    public function print_category() 
    {

        $this->load->library('Pdf');//load library pdf
        
        $pdf       = new PDF_Pagegroup('P','mm',array(210,297));// A4
        // $pdf       = new PDF_Code128('P','mm',array(215,330));// F4

        // $category  = ['Q9','Q50','Q250','Q500','Q750','Q1000','QX'];
        $category  = ['Q1','Q2','Q3'];
        $date_last = $this->m_marketing->get_last_date_history();

        $cat_id    = "";

        foreach($category as $cat){
            
            if(!empty($cat_id) AND $cat_id != $cat){
                $pdf->StartPageGroup();
            // $pdf->AddPage();
            // $pdf->AliasNbPages('{totalPages}');

            }
            $cat_id = $cat;
            $pdf->SetMargins(0,0,0);
            $pdf->SetAutoPageBreak(False);
            $pdf->StartPageGroup();
            $pdf->AddPage();
            $pdf->setTitle('Ready Goods Category');

            $pdf->SetFont('Arial','B',14,'C');
            $pdf->Cell(0,20,'Ready Goods Category',0,0,'C');
            
            $pdf->SetFont('Arial','',7,'C');

            $pdf->setXY(5,7);
            $pdf->AliasNbPages('{totalPages}');
            // $pdf->Multicell(30,4, "Page " . $pdf->PageNo(2) . "/{totalPages}", 0,'L');
            $pdf->Multicell(30,4, "Page " . $pdf->GroupPageNo() . "/".$pdf->PageGroupAlias(), 0,'L');

            $pdf->setXY(160,7);
            $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
            $pdf->Multicell(50,4, 'Tgl.Cetak : '.$tgl_now, 0,'C');

            $pdf->SetFont('Arial','B',8,'C');
        
            $pdf->setXY(5,15);
            $pdf->Multicell(17,4,'Category ',0,'L');
            $pdf->setXY(32, 15);
            $pdf->Multicell(5, 4, ':', 0, 'L');
            $pdf->setXY(33,15);
            $pdf->Multicell(40,4,$cat,0,'L');

            $pdf->setXY(5,20);
            $pdf->Multicell(30,4,'Data Per Tanggal ',0,'L');
            $pdf->setXY(32, 20);
            $pdf->Multicell(5, 4, ':', 0, 'L');
            $pdf->setXY(33,20);
            $pdf->Multicell(40,4,$date_last,0,'L');
            
            $no   = 1;
            $y    = 20;   
            $column2 = 0;
            $loop = 0;

            $pdf->SetFont('Arial','B',8,'C');
            // get
            $data_cat = $this->m_marketing->get_query_13_print($cat);
            $pdf->setXY(5,$y+5);
            $pdf->Cell(10, 5, 'No.', 1, 0, 'L');
            $pdf->Cell(80, 5, 'Article', 1, 0, 'L');
            $pdf->Cell(50, 5, 'Color', 1, 0, 'L');
            $pdf->Cell(30, 5, 'Size', 1, 0, 'L');
            $pdf->Cell(25, 5, 'Qty', 1, 1, 'R');
            $pdf->SetFont('Arial','',7,'C');
            foreach($data_cat as $row){

                $cellWidth =80; //lebar sel
                $cellHeight=3; //tinggi sel satu baris normal
                $corak = $row->corak;
                if($pdf->GetStringWidth( $corak ) <  $cellWidth  ){
                    // jika tidak
                    $line =1;
                }else{
                    //jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
                    //dengan memisahkan teks agar sesuai dengan lebar sel
                    //lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
                    // $plus_length  = round($pdf->GetStringWidth( strtoupper($corak) )) - strlen($corak);
                    $textLength =strlen($corak) ;	//total panjang teks
                    $errMargin  =7;		//margin kesalahan lebar sel, untuk jaga-jaga
                    $startChar  =0;		//posisi awal karakter untuk setiap baris
                    $maxChar    =0;			//karakter maksimum dalam satu baris, yang akan ditambahkan nanti
                    $textArray  =array();	//untuk menampung data untuk setiap baris
                    $tmpString  ="";		//untuk menampung teks untuk setiap baris (sementara)
                    $tmpString2  ="";		//untuk menampung teks untuk setiap baris (sementara)
                        
                    while($startChar < $textLength){ //perulangan sampai akhir teks
                        //perulangan sampai karakter maksimum tercapai
                        while( $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) && ($startChar+$maxChar) < $textLength ) {
                            $maxChar++;
                            $tmpString=substr($corak,$startChar,$maxChar);
                        }
                        //pindahkan ke baris berikutnya
                        $startChar=$startChar+$maxChar;
                        //kemudian tambahkan ke dalam array sehingga kita tahu berapa banyak baris yang dibutuhkan
                        array_push($textArray,$tmpString);
                        //reset variabel penampung
                        $maxChar  =0;
                        $tmpString='';
                    }
                    //dapatkan jumlah baris
                    $line=count($textArray);
                }

                //tulis cellnya
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(5,($line * $cellHeight),'',0,0,'',true); //sesuaikan ketinggian dengan jumlah garis
                $pdf->Cell(10,($line * $cellHeight),$no,'L,B',0,'L'); 

                $xPos=$pdf->GetX();
                $yPos=$pdf->GetY();
                $pdf->Multicell($cellWidth,$cellHeight,$corak,'B','L');

                $pdf->SetXY($xPos + $cellWidth , $yPos);
                $pdf->Multicell(50,($line * $cellHeight),$row->warna,'B','L');

                $pdf->SetXY($xPos + 50 + $cellWidth , $yPos);
                $pdf->Multicell(30,($line * $cellHeight),$row->lebar_Jadi.' '.$row->uom_lebar_jadi,'B','R');

                $pdf->SetXY($xPos + 80 + $cellWidth , $yPos);
                $pdf->Multicell(25,($line * $cellHeight),number_format($row->qty_jual,2).' '.$row->uom_jual,'B,R','R');
                
                $no++;
                // $gulung++;

                if($pdf->GetY() > 280){
                        $pdf->SetMargins(0,0,0);
                        $pdf->SetAutoPageBreak(False);
                        // $pdf->StartPageGroup();
                        $pdf->AddPage();
                        $pdf->setTitle('Ready Goods Category');

                        $pdf->SetFont('Arial','B',14,'C');
                        $pdf->Cell(0,20,'Ready Goods Category',0,0,'C');
                        
                        $pdf->SetFont('Arial','',7,'C');

                        $pdf->setXY(5,7);
                        // $pdf->AliasNbPages('{totalPages}');
                        // $pdf->Multicell(30,4, "Page " . $pdf->PageNo() . "/{totalPages}", 0,'L');
                        $pdf->Multicell(30,4, "Page " . $pdf->GroupPageNo() . "/".$pdf->PageGroupAlias(), 0,'L');

                        $pdf->setXY(160,7);
                        $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
                        $pdf->Multicell(50,4, 'Tgl.Cetak : '.$tgl_now, 0,'C');

                        $pdf->SetFont('Arial','B',8,'C');
                    
                        $pdf->setXY(5,15);
                        $pdf->Multicell(17,4,'Category ',0,'L');
                        $pdf->setXY(32, 15);
                        $pdf->Multicell(5, 4, ':', 0, 'L');
                        $pdf->setXY(33,15);
                        $pdf->Multicell(40,4,$cat,0,'L');

                        $pdf->setXY(5,20);
                        $pdf->Multicell(30,4,'Data Per Tanggal ',0,'L');
                        $pdf->setXY(32, 20);
                        $pdf->Multicell(5, 4, ':', 0, 'L');
                        $pdf->setXY(33,20);
                        $pdf->Multicell(40,4,$date_last,0,'L');

                        $y    = 20;   
                        $column2 = 0;

                        $pdf->setXY(5,$y+5);
                        $pdf->Cell(10, 5, 'No.', 1, 0, 'L');
                        $pdf->Cell(80, 5, 'Article', 1, 0, 'L');
                        $pdf->Cell(50, 5, 'Color', 1, 0, 'L');
                        $pdf->Cell(30, 5, 'Size', 1, 0, 'L');
                        $pdf->Cell(25, 5, 'Qty', 1, 1, 'R');
                        $pdf->SetFont('Arial','',7,'C');
                
                }

                $loop++;

            }


        }

        $pdf->Output();
    }


    public function print_category1() {
        try {
            
            // $data_print_array[] = array(
            //                 'corak'   => 'J-JM882095FSR-NCE(AK)-126" (Inspecting)',
                           
            //     );
            // $data_print_array[] = array(
            //                 'corak'   => 'J-5P162SR-NB(NX 420 BRT)-126" (Inspecting)',
                         
            //     );
            $data_print_array = $this->m_marketing->goods_to_push();
            // var_dump($data_print_array);
            $this->load->view('print/readygoods/printcategory', ['data' => $data_print_array]);
            // $this->load->view('print/picklist/printpl', ['pl' => $no, 'data' => $lisdata, 'total' => $totalItem]);

        } catch (Exception $ex) {
            
        }
    }

    function readygoodscategorychanged()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $this->load->view('report/v_marketing_view_ready_goods_category_changed', $data);
    }

    function print_category_tag() {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $changed     = $this->input->post('changed'); 
                $data_print  = json_decode($this->input->post('data_print'),true); 

                if(empty($data_print)){
                    throw new \Exception('Data Print tidak ditemukan !', 500);
                }else{
                    
                    $data_prints = $this->print_hanger($changed,$data_print);
                    if(empty($data_prints)){
                        throw new \Exception('Data Print tidak ditemukan !', 500);
                    }
                    $callback = array('status' => 'success', 'message' => 'Print Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success', 'data_print' =>$data_prints);
                }
                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')
                        ->set_output(json_encode($callback));
            }

        }catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }


    function print_hanger($changed,$data_print)
    {
        $data_print_array = array();
        
        if($changed == 'true'){
            foreach ($data_print as $dp){
                $id = $dp['rowId'];
                $get = $this->m_marketing->get_data_changed_all($id);
                $lebar_jadi  = $get->lebar_Jadi;
                $uom_lebar_jadi  = $get->uom_lebar_jadi;
                $data_print_array[] = array(
                                'article'   => $get->corak ?? '',
                                'color'     => $get->warna ?? '',
                                'size'      => $lebar_jadi.' '.$uom_lebar_jadi,
                );
            }

        }else{
            foreach ($data_print as $dp){
                foreach($dp as $val){
                    $dp_ex = explode(",",$val);
                    $data_print_array[] = array(
                                'article'   => $dp_ex[0] ?? '',
                                'color'     => $dp_ex[1] ?? '',
                                'size'      => $dp_ex[2] ?? '',
                    );
                }
            }
        }
        $this->hanger->addDatas($data_print_array);
       
        return $this->hanger->generate();
    }

    public function goodstopush() {
        $this->load->model("m_gtp");
        $data['id_dept'] = 'RMKT';
        $sales = new $this->m_gtp;
        $dates = clone $sales;
        $data['sales'] = $sales->setTables("mst_sales_group")->setOrder(["nama_sales_group" => "asc"])->setWheres(["view" => "1"])->setSelects(["nama_sales_group"])->getData();
        $_POST["length"] = 10;
        $_POST["start"] = 0;
        $data["dates"] = $dates->setSelects(["DATE(report_date) as dt"])->setGroups(["DATE(report_date)"])->setOrder(["dt" => "DESC"])->getData();
        $this->load->view('report/v_gtp', $data);
    }

    function groupproofing()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['title']  = 'View Product Proofing (GJD)';
        $data['proofing'] = 'yes';
        $this->load->view('report/v_marketing_view_ready_goods_group', $data);
    }


    function readygoodsgroupgrg()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['title']  = 'View Product Ready Goods (GRG)';
        $this->load->view('report/v_marketing_view_ready_goods_grg_group', $data);
    }

    function get_data_ready_goods_grg_group()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables15();
            $link = "readygoodsgrgitems";
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('report/marketing/'.$link.'?id='.urlencode($field->nama_produk)).'">'.$field->nama_produk.'</a>';
                $row[] = $field->total_qty;
                $row[] = $field->total_qty2;
                $row[] = $field->gl;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all15(),
                "recordsFiltered" => $this->m_marketing->count_filtered15(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group15()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }

    function export_excel_ready_goods_grg_group()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_datatables15_excel();

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);
        $title = 'Report Ready Goods (GRG)';
        
        
    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1',$title);
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

       //bold huruf
		$object->getActiveSheet()->getStyle("A1:Q3")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Nama Produk' , 'Total Qty1', 'Totak Qty2','Gl/Lot');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E');
    	$loop = 0;
    	foreach ($index_header as $val) {
            $object->getActiveSheet()->getStyle($val.'3')->applyFromArray($styleArray);
        }
        $rowCount  = 4;
        $num       = 1;
        foreach ($get_data as $val) {
			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->nama_produk);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->total_qty);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->total_qty2);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->gl);
			
            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
		
	        $rowCount++;
		}
        
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = $title.".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }


    function readygoodsgrgitems()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['product']= $this->input->get('id');
        $data['title']    = 'View Product Ready Goods (GRG)';
        $this->load->view('report/v_marketing_view_ready_goods_grg_items', $data);
    }


    function get_data_ready_goods_grg_items()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables16();
            $data = array();
            $no = $_POST['start'];
            $link  = '';
            $gmbr  = '';
            foreach ($list as $field) {
               
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $field->create_date;
                $row[] = $field->lot;
                $row[] = $field->nama_grade;
                $row[] = $field->nama_produk;
                $row[] = $field->qty." ".$field->uom;
                $row[] = $field->qty2." ".$field->uom2;
                $row[] = $field->lebar_greige." ".$field->uom_lebar_greige;
                $row[] = $field->lebar_jadi." ".$field->uom_lebar_jadi;
                $row[] = $field->lokasi_fisik;
                $row[] = $field->umur;
                $data[] = $row;

            }
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all16(),
                "recordsFiltered" => $this->m_marketing->count_filtered16(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group16()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
    }


    function export_excel_ready_goods_grg()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_datatables16_excel();

        $product    = $this->input->post('product');

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

        $title = 'Report Ready Goods (GRG)';

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', $title);
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        // SET Filter
 		$object->getActiveSheet()->SetCellValue('A3', 'Product / Corak');
 		$object->getActiveSheet()->SetCellValue('B3', ': '.$product);
		$object->getActiveSheet()->mergeCells('B3:D3');

        // $object->getActiveSheet()->SetCellValue('A5', 'Lebar Jadi');
 		// $object->getActiveSheet()->SetCellValue('B5', ': '.$uom);
		// $object->getActiveSheet()->mergeCells('B5:D5');

       //bold huruf
		$object->getActiveSheet()->getStyle("A1:Q7")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Tanggal dibuat' ,'Lot', 'Grade', 'Nama Produk' , 'Qty1', 'Uom1', 'Qty2', 'Uom2', 'Lebar Greige', 'Uom lebar Greige', 'Lebar Jadi', 'Uom lebar Jadi', 'Lokasi Fisik / Rak ', 'Umur (Hari)');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 7, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
    	$loop = 0;
    	foreach ($index_header as $val) {
            $object->getActiveSheet()->getStyle($val.'7')->applyFromArray($styleArray);
        }
        $rowCount  = 8;
        $num       = 1;
        foreach ($get_data as $val) {

			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->create_date);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->nama_grade);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->nama_produk);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->qty);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->uom);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->qty2);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->uom2);
            $object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->lebar_greige);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->uom_lebar_greige);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->lebar_jadi);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->lokasi_fisik);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->umur);

            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('O'.$rowCount)->applyFromArray($styleArray);
		
	        $rowCount++;
		}
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = $title.".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }


    function export_excel_ready_goods_category()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_data_ready_goods_category();
        $get_last_date = $this->m_marketing->get_last_date_history();

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);
        $title = 'Report Ready Goods Category';
        
        
    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1',$title);
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        // SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A2','Data Per Tanggal');
 		$object->getActiveSheet()->getStyle('A2')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A2:D2');

 		$object->getActiveSheet()->SetCellValue('E2',": ".$get_last_date);
 		$object->getActiveSheet()->getStyle('E2')->getAlignment()->setIndent(1);


       //bold huruf
		$object->getActiveSheet()->getStyle("A1:Q4")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Category' , 'Article', 'Color', 'Size', 'Uom Size', 'Qty', 'Uom', 'Qty2', 'Uom2','Gl/Lot');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 4, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K');
    	$loop = 0;
    	foreach ($index_header as $val) {
            $object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray);
        }
        $rowCount  = 5;
        $num       = 1;
        foreach ($get_data as $val) {
			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->cat_id);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->corak);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->warna);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->lebar_Jadi);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->qty_jual);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->uom_jual);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->qty2_jual);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->uom2_jual);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->jumlah_lot);
			
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
		
	        $rowCount++;

		}
        
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = $title.".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }



    function readygoodsgroupnmb()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['title']  = 'View Product Ready Goods NMB (GJD)';
        $data['proofing'] = 'no';
        $this->load->view('report/v_marketing_view_ready_goods_group_nmb', $data);
    }


    function get_data_ready_goods_group_nmb()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables17();
            $proofing = $this->input->post('proofing');
            if($proofing == 'yes'){
                $link = "groupcolourproofing";
            }else{
                $link = "readygoodsgroupcolournmb";
            }
            // $get_data_current = '&product='.urlencode($product).'&color='.urlencode($color).'&cmbMarketing='.urlencode($mkt);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('report/marketing/'.$link.'?id='.urlencode($field->corak_remark)).'&lebar_jadi='.urlencode($field->lebar_jadi).'&uom_lebar_jadi='.urlencode($field->uom_lebar_jadi).'&uom_jual='.urlencode($field->uom_jual).'">'.$field->corak_remark.'</a>';
                $row[] = $field->total_warna;
                $row[] = $field->lebar_jadi_merge;
                $row[] = $field->total_qty_jual.' '.$field->uom_jual;
                // $row[] = $field->total_qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->gl;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all17(),
                "recordsFiltered" => $this->m_marketing->count_filtered17(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group17()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
        
    }

     function export_excel_ready_goods_group_nmb()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_datatables17_excel();

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);
        $proofing = $this->input->post('proofing');
        if($proofing == 'yes') {
            $title = 'Report Proofing NMB (GJD)';
        }else{
            $title = 'Report Ready Goods NMB (GJD)';
        }
        
    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1',$title);
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

       //bold huruf
		$object->getActiveSheet()->getStyle("A1:Q3")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Corak' , 'Jml Warna', 'Lebar Jadi', 'Uom lebar', 'Qty1 [JUAL]', 'Uom1 [JUAL]', 'Gl/Lot');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 3, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H');
    	$loop = 0;
    	foreach ($index_header as $val) {
            $object->getActiveSheet()->getStyle($val.'3')->applyFromArray($styleArray);
        }
        $rowCount  = 4;
        $num       = 1;
        foreach ($get_data as $val) {
			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->corak_remark);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->total_warna);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->lebar_jadi);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->total_qty_jual);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->uom_jual);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->gl);

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
		$name_file = $title.".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));
    }

    function readygoodsgroupcolournmb()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['product']= $this->input->get('id');
        $data['lebar_jadi']= $this->input->get('lebar_jadi');
        $data['uom_lebar_jadi']= $this->input->get('uom_lebar_jadi');
        $data['uom_jual']= $this->input->get('uom_jual');
        // $data['uom2_jual']= $this->input->get('uom2_jual');
        $data['title']    = 'View Product Ready Goods NMB (GJD)';
        $data['proofing'] = 'no';
        $this->load->view('report/v_marketing_view_ready_goods_group_colour_nmb', $data);
    }

    
    function get_data_ready_goods_group_colour_nmb()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $proofing = $this->input->post('proofing');
            if($proofing == 'yes'){
                $link = "itemsproofing";
            }else{
                $link = "readygoodsitemsnmb";
            }
            $list = $this->m_marketing->get_datatables18();
            // $get_data_current = '&product='.urlencode($product).'&color='.urlencode($color).'&cmbMarketing='.urlencode($mkt);
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = '<a href="'.base_url('report/marketing/'.$link.'?id='.urlencode($field->corak_remark)).'&warna_remark='.urlencode($field->warna_remark).'&lebar_jadi='.urlencode($field->lebar_jadi).'&uom_lebar_jadi='.urlencode($field->uom_lebar_jadi).'&uom_jual='.urlencode($field->uom_jual).'&uom2_jual='.urlencode($field->uom2_jual).'">'.$field->corak_remark.'</a>';
                $row[] = $field->warna_remark;
                $row[] = $field->lebar_jadi_merge;
                $row[] = $field->total_qty_jual.' '.$field->uom_jual;
                $row[] = $field->total_qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->gl;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all18(),
                "recordsFiltered" => $this->m_marketing->count_filtered18(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group18()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
    }

     function readygoodsitemsnmb()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['product']= $this->input->get('id');
        $data['color']  = $this->input->get('warna_remark');
        $data['lebar_jadi']   = $this->input->get('lebar_jadi');
        $data['uom_lebar_jadi']   = $this->input->get('uom_lebar_jadi');
        $data['uom_jual']    = $this->input->get('uom_jual');
        $data['uom2_jual']   = $this->input->get('uom2_jual');
        $data['title']    = 'View Product Ready Goods NMB (GJD)';
        $data['proofing'] = 'no';
        $this->load->view('report/v_marketing_view_ready_goods_items_nmb', $data);
    }

    function get_data_ready_goods_items_nmb()
    {

        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables19();
            $data = array();
            $no = $_POST['start'];
            $link  = '';
            $gmbr  = '';
            foreach ($list as $field) {
                $image = "/upload/product/" . $field->kode_produk . ".jpg";
                $imageThumb = "/upload/product/thumb-" . $field->kode_produk . ".jpg";
                if (is_file(FCPATH . $image)) {
                    // $link  = is_file(FCPATH . $imageThumb) ? base_url($imageThumb) : base_url($image);
                    $link  = base_url($image);
                }else{
                    $link  = base_url("/upload/product/default.jpg");
                }
                $no++;
                $row = array();
                $row[] = $no;
                $row[] = $link;
                $row[] = $field->kode_produk;
                $row[] = $field->create_date;
                $row[] = $field->lot;
                $row[] = $field->corak_remark;
                $row[] = $field->warna_remark;
                $row[] = $field->lebar_jadi." ".$field->uom_lebar_jadi;
                $row[] = $field->qty_jual." ".$field->uom_jual;
                $row[] = $field->qty2_jual." ".$field->uom2_jual;
                $row[] = $field->lokasi;
                $row[] = $field->lokasi_fisik;
                $row[] = $field->umur;
                $data[] = $row;

            }
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all19(),
                "recordsFiltered" => $this->m_marketing->count_filtered19(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group19()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        }
    }

     function export_excel_ready_goods_nmb()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_datatables19_excel();

        $product    = $this->input->post('product');
        $color      = $this->input->post('color');
        $lebar_jadi = $this->input->post('lebar_jadi');
        $uom_lebar_jadi      = $this->input->post('uom_lebar_jadi');
        $uom_jual      = $this->input->post('uom_jual');
        $uom2_jual      = $this->input->post('uom2_jual');

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

        $proofing = $this->input->post('proofing');
        if($proofing == 'yes') {
            $title = 'Report Proofing NMB (GJD)';
        }else{
            $title = 'Report Ready Goods NMB (GJD)';
        }

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', $title);
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        // SET Filter
 		$object->getActiveSheet()->SetCellValue('A3', 'Product / Corak');
 		$object->getActiveSheet()->SetCellValue('B3', ': '.$product);
		$object->getActiveSheet()->mergeCells('B3:D3');

        $object->getActiveSheet()->SetCellValue('A4', 'Warna');
 		$object->getActiveSheet()->SetCellValue('B4', ': '.$color);
		$object->getActiveSheet()->mergeCells('B4:D4');

        // $object->getActiveSheet()->SetCellValue('A5', 'Lebar Jadi');
 		// $object->getActiveSheet()->SetCellValue('B5', ': '.$uom);
		// $object->getActiveSheet()->mergeCells('B5:D5');

       //bold huruf
		$object->getActiveSheet()->getStyle("A1:Q7")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Tanggal dibuat' ,'Lot', 'Corak' , 'Warna', 'Lebar Jadi', 'Uom lebar', 'Qty1 [JUAL]', 'Uom1 [JUAL]', 'Qty2 [JUAL]', 'Uom2 [JUAL]', 'Lokasi', 'Lokasi Fisik / Rak ', 'Umur (Hari)');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 7, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
    	$loop = 0;
    	foreach ($index_header as $val) {
            $object->getActiveSheet()->getStyle($val.'7')->applyFromArray($styleArray);
        }
        $rowCount  = 8;
        $num       = 1;
        foreach ($get_data as $val) {

			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->create_date);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->corak_remark);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->warna_remark);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->lebar_jadi);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->qty_jual);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->uom_jual);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->qty2_jual);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->uom2_jual);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->lokasi);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->lokasi_fisik);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->umur);

            //set border true
			$object->getActiveSheet()->getStyle('A'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('B'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('C'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('D'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('E'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('F'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('G'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('H'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('I'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('J'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('K'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('L'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('M'.$rowCount)->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle('N'.$rowCount)->applyFromArray($styleArray);
		
	        $rowCount++;
		}
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = $title.".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }

    function readygoodscategorynmb()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $this->load->view('report/v_marketing_view_ready_goods_category_nmb', $data);
    }


    function get_data_ready_goods_category_nmb()
    {
  
        if(isset($_POST['start']) && isset($_POST['draw'])){
            $list = $this->m_marketing->get_datatables20();
            $data = array();
            $no = $_POST['start'];
            foreach ($list as $field) {
                $no++;
                $row = array();
                $row[] = $no;
                // $row[] = $link;
                // $row[] = $field->file_name;
                $row[] = $field->cat_id;
                $row[] = $field->corak;
                $row[] = $field->warna;
                $row[] = $field->lebar_Jadi." ".$field->uom_lebar_jadi;
                $row[] = $field->qty_jual.' '.$field->uom_jual;
                $row[] = $field->qty2_jual.' '.$field->uom2_jual;
                $row[] = $field->jumlah_lot;
                $row[] = $field->corak.",".$field->warna.",".$field->lebar_Jadi." ".$field->uom_lebar_jadi;
                $data[] = $row;
            }
    
            $output = array(
                "draw" => $_POST['draw'],
                "recordsTotal" => $this->m_marketing->count_all20(),
                "recordsFiltered" => $this->m_marketing->count_filtered20(),
                "data" => $data,
                "total_lot"=>$this->m_marketing->count_all_no_group20(),
                "date_history"=>$this->m_marketing->get_last_date_history()
            );
            //output dalam format JSON
            echo json_encode($output);

        }else{
            die();
        } 
        
    }


    public function print_category_nmb() 
    {

        $this->load->library('Pdf');//load library pdf
        
        $pdf       = new PDF_Pagegroup('P','mm',array(210,297));// A4
        // $pdf       = new PDF_Code128('P','mm',array(215,330));// F4

        // $category  = ['Q9','Q50','Q250','Q500','Q750','Q1000','QX'];
        $category  = ['Q1','Q2','Q3'];
        $date_last = $this->m_marketing->get_last_date_history();

        $cat_id    = "";

        foreach($category as $cat){
            
            if(!empty($cat_id) AND $cat_id != $cat){
                $pdf->StartPageGroup();
            // $pdf->AddPage();
            // $pdf->AliasNbPages('{totalPages}');

            }
            $cat_id = $cat;
            $pdf->SetMargins(0,0,0);
            $pdf->SetAutoPageBreak(False);
            $pdf->StartPageGroup();
            $pdf->AddPage();
            $pdf->setTitle('Ready Goods Category NMB');

            $pdf->SetFont('Arial','B',14,'C');
            $pdf->Cell(0,20,'Ready Goods Category NMB',0,0,'C');
            
            $pdf->SetFont('Arial','',7,'C');

            $pdf->setXY(5,7);
            $pdf->AliasNbPages('{totalPages}');
            // $pdf->Multicell(30,4, "Page " . $pdf->PageNo(2) . "/{totalPages}", 0,'L');
            $pdf->Multicell(30,4, "Page " . $pdf->GroupPageNo() . "/".$pdf->PageGroupAlias(), 0,'L');

            $pdf->setXY(160,7);
            $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
            $pdf->Multicell(50,4, 'Tgl.Cetak : '.$tgl_now, 0,'C');

            $pdf->SetFont('Arial','B',8,'C');
        
            $pdf->setXY(5,15);
            $pdf->Multicell(17,4,'Category ',0,'L');
            $pdf->setXY(32, 15);
            $pdf->Multicell(5, 4, ':', 0, 'L');
            $pdf->setXY(33,15);
            $pdf->Multicell(40,4,$cat,0,'L');

            $pdf->setXY(5,20);
            $pdf->Multicell(30,4,'Data Per Tanggal ',0,'L');
            $pdf->setXY(32, 20);
            $pdf->Multicell(5, 4, ':', 0, 'L');
            $pdf->setXY(33,20);
            $pdf->Multicell(40,4,$date_last,0,'L');
            
            $no   = 1;
            $y    = 20;   
            $column2 = 0;
            $loop = 0;

            $pdf->SetFont('Arial','B',8,'C');
            // get
            $data_cat = $this->m_marketing->get_query_13_print($cat);
            $pdf->setXY(5,$y+5);
            $pdf->Cell(10, 5, 'No.', 1, 0, 'L');
            $pdf->Cell(80, 5, 'Article', 1, 0, 'L');
            $pdf->Cell(50, 5, 'Color', 1, 0, 'L');
            $pdf->Cell(30, 5, 'Size', 1, 0, 'L');
            $pdf->Cell(25, 5, 'Qty', 1, 1, 'R');
            $pdf->SetFont('Arial','',7,'C');
            foreach($data_cat as $row){

                $cellWidth =80; //lebar sel
                $cellHeight=3; //tinggi sel satu baris normal
                $corak = $row->corak;
                if($pdf->GetStringWidth( $corak ) <  $cellWidth  ){
                    // jika tidak
                    $line =1;
                }else{
                    //jika ya, maka hitung ketinggian yang dibutuhkan untuk sel akan dirapikan
                    //dengan memisahkan teks agar sesuai dengan lebar sel
                    //lalu hitung berapa banyak baris yang dibutuhkan agar teks pas dengan sel
                    // $plus_length  = round($pdf->GetStringWidth( strtoupper($corak) )) - strlen($corak);
                    $textLength =strlen($corak) ;	//total panjang teks
                    $errMargin  =7;		//margin kesalahan lebar sel, untuk jaga-jaga
                    $startChar  =0;		//posisi awal karakter untuk setiap baris
                    $maxChar    =0;			//karakter maksimum dalam satu baris, yang akan ditambahkan nanti
                    $textArray  =array();	//untuk menampung data untuk setiap baris
                    $tmpString  ="";		//untuk menampung teks untuk setiap baris (sementara)
                    $tmpString2  ="";		//untuk menampung teks untuk setiap baris (sementara)
                        
                    while($startChar < $textLength){ //perulangan sampai akhir teks
                        //perulangan sampai karakter maksimum tercapai
                        while( $pdf->GetStringWidth( $tmpString ) < ($cellWidth-$errMargin) && ($startChar+$maxChar) < $textLength ) {
                            $maxChar++;
                            $tmpString=substr($corak,$startChar,$maxChar);
                        }
                        //pindahkan ke baris berikutnya
                        $startChar=$startChar+$maxChar;
                        //kemudian tambahkan ke dalam array sehingga kita tahu berapa banyak baris yang dibutuhkan
                        array_push($textArray,$tmpString);
                        //reset variabel penampung
                        $maxChar  =0;
                        $tmpString='';
                    }
                    //dapatkan jumlah baris
                    $line=count($textArray);
                }

                //tulis cellnya
                $pdf->SetFillColor(255,255,255);
                $pdf->Cell(5,($line * $cellHeight),'',0,0,'',true); //sesuaikan ketinggian dengan jumlah garis
                $pdf->Cell(10,($line * $cellHeight),$no,'L,B',0,'L'); 

                $xPos=$pdf->GetX();
                $yPos=$pdf->GetY();
                $pdf->Multicell($cellWidth,$cellHeight,$corak,'B','L');

                $pdf->SetXY($xPos + $cellWidth , $yPos);
                $pdf->Multicell(50,($line * $cellHeight),$row->warna,'B','L');

                $pdf->SetXY($xPos + 50 + $cellWidth , $yPos);
                $pdf->Multicell(30,($line * $cellHeight),$row->lebar_Jadi.' '.$row->uom_lebar_jadi,'B','R');

                $pdf->SetXY($xPos + 80 + $cellWidth , $yPos);
                $pdf->Multicell(25,($line * $cellHeight),number_format($row->qty_jual,2).' '.$row->uom_jual,'B,R','R');
                
                $no++;
                // $gulung++;

                if($pdf->GetY() > 280){
                        $pdf->SetMargins(0,0,0);
                        $pdf->SetAutoPageBreak(False);
                        // $pdf->StartPageGroup();
                        $pdf->AddPage();
                        $pdf->setTitle('Ready Goods Category NMB');

                        $pdf->SetFont('Arial','B',14,'C');
                        $pdf->Cell(0,20,'Ready Goods Category NMB',0,0,'C');
                        
                        $pdf->SetFont('Arial','',7,'C');

                        $pdf->setXY(5,7);
                        // $pdf->AliasNbPages('{totalPages}');
                        // $pdf->Multicell(30,4, "Page " . $pdf->PageNo() . "/{totalPages}", 0,'L');
                        $pdf->Multicell(30,4, "Page " . $pdf->GroupPageNo() . "/".$pdf->PageGroupAlias(), 0,'L');

                        $pdf->setXY(160,7);
                        $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
                        $pdf->Multicell(50,4, 'Tgl.Cetak : '.$tgl_now, 0,'C');

                        $pdf->SetFont('Arial','B',8,'C');
                    
                        $pdf->setXY(5,15);
                        $pdf->Multicell(17,4,'Category ',0,'L');
                        $pdf->setXY(32, 15);
                        $pdf->Multicell(5, 4, ':', 0, 'L');
                        $pdf->setXY(33,15);
                        $pdf->Multicell(40,4,$cat,0,'L');

                        $pdf->setXY(5,20);
                        $pdf->Multicell(30,4,'Data Per Tanggal ',0,'L');
                        $pdf->setXY(32, 20);
                        $pdf->Multicell(5, 4, ':', 0, 'L');
                        $pdf->setXY(33,20);
                        $pdf->Multicell(40,4,$date_last,0,'L');

                        $y    = 20;   
                        $column2 = 0;

                        $pdf->setXY(5,$y+5);
                        $pdf->Cell(10, 5, 'No.', 1, 0, 'L');
                        $pdf->Cell(80, 5, 'Article', 1, 0, 'L');
                        $pdf->Cell(50, 5, 'Color', 1, 0, 'L');
                        $pdf->Cell(30, 5, 'Size', 1, 0, 'L');
                        $pdf->Cell(25, 5, 'Qty', 1, 1, 'R');
                        $pdf->SetFont('Arial','',7,'C');
                
                }

                $loop++;

            }


        }

        $pdf->Output();
    }

    function print_category_tag_nmb() {
        try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

                $changed     = $this->input->post('changed'); 
                $data_print  = json_decode($this->input->post('data_print'),true); 

                if(empty($data_print)){
                    throw new \Exception('Data Print tidak ditemukan !', 500);
                }else{
                    
                    $data_prints = $this->print_hanger_nmb($changed,$data_print);
                    if(empty($data_prints)){
                        throw new \Exception('Data Print tidak ditemukan !', 500);
                    }
                    $callback = array('status' => 'success', 'message' => 'Print Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success', 'data_print' =>$data_prints);
                }
                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')
                        ->set_output(json_encode($callback));
            }

        }catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function print_hanger_nmb($changed,$data_print)
    {
        $data_print_array = array();
        
        if($changed == 'true'){
            foreach ($data_print as $dp){
                $id = $dp['rowId'];
                $get = $this->m_marketing->get_data_changed_all($id);
                $lebar_jadi  = $get->lebar_Jadi;
                $uom_lebar_jadi  = $get->uom_lebar_jadi;
                $data_print_array[] = array(
                                'article'   => $get->corak ?? '',
                                'color'     => $get->warna ?? '',
                                'size'      => $lebar_jadi.' '.$uom_lebar_jadi,
                );
            }

        }else{
            foreach ($data_print as $dp){
                foreach($dp as $val){
                    $dp_ex = explode(",",$val);
                    $data_print_array[] = array(
                                'article'   => $dp_ex[0] ?? '',
                                'color'     => $dp_ex[1] ?? '',
                                'size'      => $dp_ex[2] ?? '',
                    );
                }
            }
        }
        $this->hanger->addDatas($data_print_array);
       
        return $this->hanger->generate();
    }


    function export_excel_ready_goods_category_nmb()
    {

        $this->load->library('excel');
		ob_start();
        $get_data = $this->m_marketing->get_data_ready_goods_category_nmb();
        $get_last_date = $this->m_marketing->get_last_date_history_nmb();

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);
        $title = 'Report Ready Goods Category NMB';
        
        
    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1',$title);
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        // SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A2','Data Per Tanggal');
 		$object->getActiveSheet()->getStyle('A2')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A2:D2');

 		$object->getActiveSheet()->SetCellValue('E2',": ".$get_last_date);
 		$object->getActiveSheet()->getStyle('E2')->getAlignment()->setIndent(1);


       //bold huruf
		$object->getActiveSheet()->getStyle("A1:Q4")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

         // header table
        $table_head_columns  = array('No', 'Category' , 'Article', 'Color', 'Size', 'Uom Size', 'Qty', 'Uom', 'Qty2', 'Uom2','Gl/Lot');

        $column = 0;
        foreach ($table_head_columns as $judul) {
            # code...
            $object->getActiveSheet()->setCellValueByColumnAndRow($column, 4, $judul);  
            $column++;
        }

        // set with and border
    	$index_header = array('A','B','C','D','E','F','G','H','I','J','K');
    	$loop = 0;
    	foreach ($index_header as $val) {
            $object->getActiveSheet()->getStyle($val.'4')->applyFromArray($styleArray);
        }
        $rowCount  = 5;
        $num       = 1;
        foreach ($get_data as $val) {
			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->cat_id);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->corak);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->warna);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->lebar_Jadi);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->uom_lebar_jadi);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->qty_jual);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->uom_jual);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, $val->qty2_jual);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->uom2_jual);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->jumlah_lot);
			
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
		
	        $rowCount++;

		}
        
        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file = $title.".xlsx";
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));

    }


}