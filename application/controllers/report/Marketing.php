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

}