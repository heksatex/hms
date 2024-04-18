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

    function stockbylokasi()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $this->load->view('report/v_marketing_view_by_lokasi', $data);
    }

    function stockbylokasiitems()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $data['lokasi']= $this->input->get('lokasi');
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

    function stockhistorygjd()
    {
        $id_dept        = 'RMKT';
        $data['id_dept']= $id_dept;
        $this->load->view('report/v_marketing_stock_history', $data);
    }

    function get_data_stock_history()
    {
        $tgldari   = date("Y-m-d H:i:s", strtotime($this->input->post('tgl_dari')));
        $tglsampai = date("Y-m-d 23:59:59", strtotime($this->input->post('tgl_sampai')));

        $get_mkt = $this->m_marketing->get_list_mst_sales_group();
        $data_stock_history = [];
        $tmp_tgl = "";
        foreach($get_mkt as $val){

            $data_stock = $this->m_marketing->get_data_stock_by_mkt($tgldari,$tglsampai,$val->nama_sales_group);
            $tmp_stock  = "";
            foreach($data_stock as $st){
                $tmp_stock .= floatval($st->l_stock).", ";
                $tmp_tgl   .= date('d F', strtotime($st->tanggal)).", ";
            }
            $tmp_stock = rtrim($tmp_stock, ', ');
            $arr_data  = [];
            $data_stock_history[] = array(
                                    "name" => $val->nama_sales_group,
                                    "data" => ($tmp_stock),
            );
            
        }

        // $tmp_tgl = "";
        // $tgldari   = date("Y-m-d", strtotime($this->input->post('tgl_dari')));
        // $tglsampai = date("Y-m-d", strtotime($this->input->post('tgl_sampai')));
        // while ($tgldari <= $tglsampai) {
        //     $tmp_tgl .= date('d F', strtotime($tgldari)).", ";
        //     $tgldari = date('Y-m-d', strtotime('+1 days', strtotime($tgldari)));
        // }
        $callback  = array('status'=>'success', 'result'=>$data_stock_history, 'periode'=>$tmp_tgl);
        echo json_encode($callback);


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

}