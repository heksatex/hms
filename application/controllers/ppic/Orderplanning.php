<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Orderplanning extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('m_orderPlanning');
        $this->load->model('m_sales');
		$this->load->model('_module');
	}

	public function index()
	{	
        $data['id_dept']='OP';
		$this->load->view('ppic/v_order_planning', $data);
	}

	function get_data()
    {	
    	$sub_menu  = $this->uri->segment(2);
    	$kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $list = $this->m_orderPlanning->get_datatables($kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->sales_order);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('ppic/Orderplanning/edit/'.$kode_encrypt).'">'.$field->sales_order.'</a>';
            $row[] = $field->create_date;
            $row[] = $field->buyer_code;
            $row[] = $field->nama_status;
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_orderPlanning->count_all($kode['kode']),
            "recordsFiltered" => $this->m_orderPlanning->count_filtered($kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


    public function edit($id = null)
    {   
        if(!isset($id)) show_404();
        $kode_decrypt      = decrypt_url($id);
        $id_dept           = 'OP';
        $data['id_dept']   = $id_dept;
        $data['mms']       = $this->_module->get_data_mms_for_log_history($id_dept);// get mms by dept untuk menu yg beda-beda
        $data["salescontract"] = $this->m_sales->get_data_by_kode($kode_decrypt);
        $data["details"]   = $this->m_sales->get_data_detail_by_kode($kode_decrypt);
        $data["currency"]  = $this->m_sales->get_list_currency();
        if(empty($data["salescontract"])){
          show_404();
        }else{
          return $this->load->view('ppic/v_order_planning_edit',$data);
        }
    }


    public function edit_details_modal()
    {
        $sales_order = $this->input->post('sales_order');
        $row_order   = $this->input->post('row_order');
        $data['row'] = $this->m_orderPlanning->get_data_detail($sales_order,$row_order);
        return $this->load->view('modal/v_order_planning_edit_modal', $data);
    }

    public function update_due_date()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 

            $sales_order = $this->input->post('sales_order');
            $row_order   = $this->input->post('row_order');
            $due_date    = $this->input->post('due_date');
            $kode_produk    = $this->input->post('kode_produk');
            $nama_produk    = addslashes($this->input->post('nama_produk'));
            $desc           = addslashes($this->input->post('desc'));

            ///lock table
            $this->_module->lock_tabel('sales_contract WRITE, sales_contract_items WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE, sales_color_line WRITE');

            // cek kode produk by row 
            $cek_items = $this->m_orderPlanning->cek_sales_contract_items_by_kode($sales_order,$kode_produk,$row_order);
            if(empty($cek_items['kode_produk'])){

                $callback = array('status' => 'failed','message' => 'Maaf, Data Gagal Disimpan, Data Produk Tidak ditemukan', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else{

                $this->m_orderPlanning->save_due_date($sales_order,$kode_produk,$row_order,$due_date);
                $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                $jenis_log   = "edit";
                $note_log    = "Add Due Date | ".$sales_order." | ".$due_date." | ".$kode_produk." | ".$nama_produk." | ".$desc." | ".$row_order;
                $this->_module->gen_history($sub_menu, $sales_order, $jenis_log, $note_log, $username);
            }

            
            //unlock table
            $this->_module->unlock_tabel();

        }
        echo json_encode($callback);
    }

    public function confirm_date()
    {
        $sales_order = $this->input->post('sales_order');

        $sub_menu = $this->uri->segment(2);
        $username = $this->session->userdata('username'); 

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            
            ///lock table
            $this->_module->lock_tabel('sales_contract WRITE, sales_contract_items WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE, sales_color_line WRITE');

            //cek_items sales contract
            $cek_details = $this->m_sales->cek_sales_contract_items_by_kode($sales_order)->num_rows();

            $status     = "status IN ('draft')";
            $cek_status = $this->m_sales->cek_status_sales_contract($sales_order,$status)->row_array();

            $status2     = "status NOT IN ('waiting_date')";
            $cek_status2 = $this->m_sales->cek_status_sales_contract($sales_order,$status2)->row_array();

            if($cek_details == 0 ){//jika contract line masih kosong
                $callback = array('status' => 'failed','message' => 'Contract Line Items Masing Kosong !'.$cek_details, 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if(!empty($cek_status['sales_order'])){// jika statusnya masih draft
                $callback = array('status' => 'failed','message' => 'Status Contract Line Masih Draft !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if(!empty($cek_status2['sales_order'])){
                $callback = array('status' => 'failed','message' => 'Maaf, Confirm Date sudah Dilakukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else{
                $cek_dd = $this->m_orderPlanning->cek_due_date_sales_conctract_items_by_kode($sales_order)->num_rows();
                if($cek_dd>0){
                    $callback = array('status' => 'failed','message' => 'Due Date Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{
                    $status = 'date_assigned';
                    $this->m_sales->update_status_sales_contract($sales_order,$status);
                    $callback = array('status' => 'success','message' => 'Confirm Date Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success');

                    $jenis_log   = "edit";
                    $note_log    = $sales_order.' -> Confirm Date';
                    $this->_module->gen_history($sub_menu, $sales_order, $jenis_log, $note_log, $username);
                
                }
            }
            //unlock table
            $this->_module->unlock_tabel();

        }

        echo json_encode($callback);
    }


}