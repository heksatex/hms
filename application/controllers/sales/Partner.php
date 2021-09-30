<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Partner extends MY_Controller
{

	public function __construct()
  	{
  		parent:: __construct();
  		$this->is_loggedin();//cek apakah user sudah login
     	$this->load->model('m_partner');
  		$this->load->model('_module');
  	}

  	public function index()
  	{	
      	$data['id_dept']='PRT';
  		$this->load->view('sales/v_partner', $data);
  	}


  	function get_data()
    {	
    	$sub_menu  = $this->uri->segment(2);
    	$kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $list = $this->m_partner->get_datatables($kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->id);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('sales/partner/edit/'.$kode_encrypt).'">'.$field->nama.'</a>';
            $row[] = $field->buyer_code;
            $row[] = $field->invoice_street;
            $row[] = $field->invoice_city;
            $row[] = $field->invoice_state;
            $row[] = $field->invoice_country;
            $row[] = $field->invoice_zip;
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_partner->count_all($kode['kode']),
            "recordsFiltered" => $this->m_partner->count_filtered($kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }
  	

    public function add()
    {
        $data['id_dept']  ='PRT';
        //$data["currency"]  = $this->m_sales->get_list_currency();
        return $this->load->view('sales/v_partner_add', $data);
    }


    public function edit($id = null)
    {   
        if(!isset($id)) show_404();
        $kode_decrypt      = decrypt_url($id);
        $data['id_dept']   = 'PRT';
        $partner           = $this->m_partner->get_partner_by_kode($kode_decrypt);
        $data['partner']   = $partner;

        // >> invoice
        //get nama country by id country
        $data['inv_id_country'] = $partner->invoice_country;
        $nama_country           = $this->m_partner->get_name_country_by_id($partner->invoice_country)->row_array();
        $data['inv_nm_country'] = $nama_country['name'];

        $data['inv_id_state']   = $partner->invoice_state;
        $nama_state             = $this->m_partner->get_name_state_by_id($partner->invoice_state)->row_array();
        $data['inv_nm_state']   = $nama_state['name'];
        // << invoice 

        // >> delivery
        //get nama country by id country
        $data['dv_id_country'] = $partner->invoice_country;
        $nama_country           = $this->m_partner->get_name_country_by_id($partner->delivery_country)->row_array();
        $data['dv_nm_country'] = $nama_country['name'];

        $data['dv_id_state']   = $partner->invoice_state;
        $nama_state             = $this->m_partner->get_name_state_by_id($partner->delivery_state)->row_array();
        $data['dv_nm_state']   = $nama_state['name'];
        // << delivery 
       
        if(empty($data["partner"])){
          show_404();
        }else{
          return $this->load->view('sales/v_partner_edit',$data);
        }
    }

    public function get_states_select2()
    {
	    $id   = ($this->input->post('id'));
	    $name = addslashes($this->input->post('name'));
   		$callback = $this->m_partner->get_list_states_select2_by_country($id,$name);
        echo json_encode($callback);
    }


    public function get_country_select2()
    {
	    $name = addslashes($this->input->post('name'));
   		$callback = $this->m_partner->get_list_country_select2($name);
        echo json_encode($callback);
    }


    public function simpan()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
	        $sub_menu  = $this->uri->segment(2);
	        $username  = addslashes($this->session->userdata('username')); 
	        $tanggal   = date('Y-m-d H:i:s');

	        $id        = $this->input->post('id');
	        $name      = addslashes($this->input->post('name'));
	        $invoice_street = addslashes($this->input->post('invoice_street'));
	        $invoice_city   = addslashes($this->input->post('invoice_city'));
	        $invoice_state  = addslashes($this->input->post('invoice_state'));
	        $invoice_country= addslashes($this->input->post('invoice_country'));
	        $invoice_zip    = addslashes($this->input->post('invoice_zip'));

	        $buyer_code     = addslashes($this->input->post('buyer_code'));
	        $website        = addslashes($this->input->post('website'));
	        $tax_name       = addslashes($this->input->post('tax_name'));
	        $tax_address    = addslashes($this->input->post('tax_address'));
	        $tax_city       = addslashes($this->input->post('tax_city'));
	        $npwp           = addslashes($this->input->post('npwp'));

	        $contact_person = addslashes($this->input->post('contact_person'));
	        $phone          = addslashes($this->input->post('phone'));
	        $mobile         = addslashes($this->input->post('mobile'));
	        $fax            = addslashes($this->input->post('fax'));
	        $email 			= addslashes($this->input->post('email'));

	        $delivery_street= addslashes($this->input->post('delivery_street'));
	        $delivery_city  = addslashes($this->input->post('delivery_city'));
	        $delivery_state = addslashes($this->input->post('delivery_state'));
	        $delivery_country = addslashes($this->input->post('delivery_country'));
	        $delivery_zip   = addslashes($this->input->post('delivery_zip'));

	        $check_customer = $this->input->post('check_customer');
	        $check_supplier = $this->input->post('check_supplier');
			$status_simpan  = $this->input->post('status');

	        if(empty($name)){
	        	$callback = array('status' => 'failed', 'field' => 'name', 'message' => 'Name Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    
	        }else if(empty($invoice_street)){
				$callback = array('status' => 'failed', 'field' => 'invoice_street', 'message' => 'Invoice Street Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	        }else if(empty($invoice_country)){
	        	$callback = array('status' => 'failed', 'field' => 'invoice_country', 'message' => 'Invoice Country Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	        }else if(empty($invoice_state)){
	        	$callback = array('status' => 'failed', 'field' => 'invoice_state', 'message' => 'Invoice State Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	        }else if(empty($invoice_city)){
	        	$callback = array('status' => 'failed', 'field' => 'invoice_city', 'message' => 'Invoice City Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 

	        }else if(empty($buyer_code)){
	        	$callback = array('status' => 'failed', 'field' => 'buyer_code', 'message' => 'Buyer Code Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 

	        }else if(empty($delivery_street)){
	        	$callback = array('status' => 'failed', 'field' => 'delivery_street', 'message' => 'Delivery Street Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	        }else if(empty($delivery_country)){
	        	$callback = array('status' => 'failed', 'field' => 'delivery_country', 'message' => 'Delivery Country Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	        }else if(empty($delivery_state)){
	        	$callback = array('status' => 'failed', 'field' => 'delivery_state', 'message' => 'Delivery State Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	        }else if(empty($delivery_city)){
	        	$callback = array('status' => 'failed', 'field' => 'delivery_city', 'message' => 'Delivery Street Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	        }else{

				// cek nama partner sudah ada atau belum ?
				$cek = $this->m_partner->cek_partner_by_nama($name)->row_array();

   			    // cek apa nama_partner tidak ada 
				if(empty($cek['nama'])){
					$nama_double = FALSE;
			    }else{
					$nama_double = TRUE;
				}

				if($status_simpan == 'edit' AND ($cek['id'] != $id)){

					$callback = array('status' => 'failed', 'field' => 'name', 'message' => 'Name Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    

				}else if($nama_double == TRUE AND $status_simpan == 'tambah'){
					$callback = array('status' => 'failed', 'field' => 'name', 'message' => 'Name Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    

				}else{

	        	//lock tabel
	        	$this->_module->lock_tabel('partner WRITE, log_history WRITE, main_menu_sub WRITE, user WRITE');

	        	if(empty($id)){ // jika id kosong maka simpan data

	        		$this->m_partner->save_partner($name,$tanggal,$invoice_street,$invoice_city,$invoice_state,$invoice_country,$invoice_zip,$buyer_code,$website,$tax_name,$tax_address,$tax_city,$npwp,$contact_person,$phone,$mobile,$fax,$email,$delivery_street,$delivery_city,$invoice_state,$delivery_country,$delivery_zip,$check_customer,$check_supplier);

	        		//get max id partner
	        		$last_id = $this->m_partner->get_last_id_partner();

	        		$id_encrypt = encrypt_url($last_id);

	                $jenis_log = "create";
	                $note_log  = $last_id." | ".$name." | ".$invoice_street." | ".$invoice_city." | ".$invoice_zip." | ".$buyer_code;
	                $this->_module->gen_history($sub_menu, $last_id, $jenis_log, $note_log, $username);

	        		$callback = array('status' => 's],uccess', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success', 'kode_encrypt' => $id_encrypt);

	        	}else{ // jik id terisi maka update data partner

	        		$this->m_partner->update_partner($name,$invoice_street,$invoice_city,$invoice_state,$invoice_country,$invoice_zip,$buyer_code,$website,$tax_name,$tax_address,$tax_city,$npwp,$contact_person,$phone,$mobile,$fax,$email,$delivery_street,$delivery_city,$delivery_state,$delivery_country,$delivery_zip,$check_customer,$check_supplier,$id);

	                $jenis_log = "edit";
	                $note_log  = $id." | ".$name." | ".$invoice_street." | ".$invoice_city." | ".$invoice_zip." | ".$buyer_code;
	                $this->_module->gen_history($sub_menu, $id, $jenis_log, $note_log, $username);

	        		$callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');

	        	}

	        	//unlock tabel
	        	$this->_module->unlock_tabel();

				}

	        }


        }


        echo json_encode($callback);

    }
}