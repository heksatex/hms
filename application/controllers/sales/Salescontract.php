<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');
/**
 * 
 */
class Salescontract extends MY_Controller
{
  	public function __construct()
  	{
  		parent:: __construct();
  		$this->is_loggedin();//cek apakah user sudah login
      $this->load->model('m_sales');
  		$this->load->model('_module');
  		$this->load->library('Pdf');//load library pdf
  	}

  	public function index()
  	{	
      $data['id_dept']='SC';
  		$this->load->view('sales/v_sales_contract', $data);
  	}

	  function get_data()
    { 
      $sub_menu  = $this->uri->segment(2);
      $username  = addslashes($this->session->userdata('username')); 
      $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();

      $sales = $this->m_sales->cek_sales_group_by_username($username)->row_array();
      $sales_group = $sales['sales_group'];

        $list = $this->m_sales->get_datatables($kode['kode'],$sales_group);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->sales_order);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('sales/salescontract/edit/'.$kode_encrypt).'">'.$field->sales_order.'</a>';
            $row[] = $field->create_date;
            $row[] = $field->customer_name;
            $row[] = $field->nama_sales_group;
            $row[] = $field->nama_status;
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_sales->count_all($kode['kode'],$sales_group),
            "recordsFiltered" => $this->m_sales->count_filtered($kode['kode'],$sales_group),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


    public function add()
    {
        $data['id_dept']  ='SC';
        $data['warehouse'] = $this->m_sales->get_list_departement();
        $data["currency"]  = $this->m_sales->get_list_currency();
        return $this->load->view('sales/v_sales_contract_add', $data);
    }

    public function list_customer_modal()
    {
        return $this->load->view('modal/v_customer_modal');
    }

    public function get_data_customer()
    {
        $list = $this->m_sales->get_datatables2();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
          $no++;
          $row = array();
          $row[] = $no;
          $row[] = ' <a href="#" class="pilih" id="'.$field->id.'" name="'.$field->nama.'" buyer-code="'.$field->buyer_code.'" invoice-address="'.$field->invoice_street.' '.$field->invoice_city.' '.$field->invoice_state.' '.$field->invoice_country.' '.$field->invoice_zip.'" delivery-address="'.$field->delivery_street.' '.$field->delivery_city.' '.$field->delivery_state.' '.$field->delivery_country.' '.$field->delivery_zip.'">'.$field->nama.' <span class="glyphicon glyphicon-check"></span></a>';
          $row[] = $field->buyer_code;
          $row[] = $field->invoice_street.' '.$field->invoice_city.' '.$field->invoice_state.' '.$field->invoice_country.' '.$field->invoice_zip;
          $row[] = $field->delivery_street.' '.$field->delivery_city.' '.$field->delivery_state.' '.$field->delivery_country.' '.$field->delivery_zip;

          $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_sales->count_all2(),
            "recordsFiltered" => $this->m_sales->count_filtered2(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function simpan()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
          $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username')); 

          $sales_order     = $this->input->post('sales_order');
          $cust_id         = $this->input->post('cust_id');
          $customer        = addslashes($this->input->post('customer'));
          $invoice_address = addslashes($this->input->post('invoice_address'));
          $delivery_address= addslashes($this->input->post('delivery_address'));
          $buyer_code      = addslashes($this->input->post('buyer_code'));
          $type            = addslashes($this->input->post('type'));
          $order_production= $this->input->post('order_production');
          $tgl             = $this->input->post('tgl');
          $reference       = addslashes($this->input->post('reference'));
          $warehouse       = addslashes($this->input->post('warehouse'));
          $currency        = addslashes($this->input->post('currency'));
          $delivery_date   = addslashes($this->input->post('delivery_date'));
          $time_ship       = addslashes($this->input->post('time_ship')); // dikosongkan
          $note_head       = addslashes($this->input->post('note_head'));

          $incoterm        = addslashes($this->input->post('incoterm'));
          $paymentterm     = addslashes($this->input->post('paymentterm'));
          $destination     = addslashes($this->input->post('destination'));
          $bank            = addslashes($this->input->post('bank'));
          $clause          = addslashes($this->input->post('clause'));
          $note            = addslashes($this->input->post('note'));

          /*
          if(!isset($order_prod)){
            $order_production = 'false';
          }else{
            $order_production = 'true';
          }
          */

          // cek sales_person
          $sg = $this->m_sales->cek_sales_group_by_username($username)->row_array();

          if(empty($customer)){
              $callback = array('status' => 'failed', 'field' => 'customer', 'message' => 'Customer Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    
          }elseif(empty($invoice_address)){
              $callback = array('status' => 'failed', 'field' => 'invoice_address', 'message' => 'Invoice Address Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
          }elseif(empty($delivery_address)){
              $callback = array('status' => 'failed', 'field' => 'delivery_address', 'message' => 'Delivery Address Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
          }elseif(empty($buyer_code)){
              $callback = array('status' => 'failed', 'field' => 'buyer_code', 'message' => 'Buyer Code Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
          }elseif(empty($type)){
              $callback = array('status' => 'failed', 'field' => 'type', 'message' => 'Type Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
          }elseif(empty($tgl)){
              $callback = array('status' => 'failed', 'field' => 'tgl', 'message' => 'Tanggal Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
          }elseif(empty($reference)){
              $callback = array('status' => 'failed', 'field' => 'reference', 'message' => 'Reference/Description Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
          }elseif(empty($warehouse)){
              $callback = array('status' => 'failed', 'field' => 'warehouse', 'message' => 'Warehouse Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
          }elseif(empty($currency)){
              $callback = array('status' => 'failed', 'field' => 'currency', 'message' => 'Currency Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
          }elseif(empty($delivery_date)){
              $callback = array('status' => 'failed', 'field' => 'delivery_date', 'message' => 'Delivery Date Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
          }elseif(empty($note_head)){
              $callback = array('status' => 'failed', 'field' => 'note_head', 'message' => 'Note Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
          }elseif(empty($sg['sales_group'])){
            $callback = array('status' => 'failed', 'field' => 'note_head', 'message' => 'Sales Person tidak ada, Silahkan tentukan Sales Person nya terlebih dahulu !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
          }else{

              if(empty($sales_order)){//jika sales order kosong, aksinya simpan data
                  $kode['sales_order'] =  $this->m_sales->get_kode_sales_order();
                  $kode_encrypt        = encrypt_url($kode['sales_order']);
                  $tgl    			       = date('Y-m-d H:i:s');
                  $sales_group         = $this->m_sales->get_sales_group_by_user($username)->row_array();
                  $curr_simbol         = $this->m_sales->get_symbol_currency_by_nama($currency)->row_array();

                  $this->m_sales->simpan($kode['sales_order'], $tgl, $cust_id, $customer, $invoice_address, $delivery_address, $buyer_code, $type, $reference, $warehouse, $currency, addslashes($curr_simbol['symbol']), $delivery_date, $time_ship, $order_production,addslashes($sales_group['sales_group']),'draft',$note_head);

                  $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi'=> $kode['sales_order'], 'icon' =>'fa fa-check', 'type' => 'success', 'kode_encrypt' => $kode_encrypt);

                  $jenis_log = "create";
                  $note_log  = $kode['sales_order']." | ".$customer." | ".$buyer_code." | ".$type." | ".$warehouse." | ".$currency." | ".$delivery_date." | ".$note_head;
                  $this->_module->gen_history($sub_menu, $kode['sales_order'], $jenis_log, $note_log, $username);

              }else{//jika sales order ada, aksinya update data
              	  $curr_simbol         = $this->m_sales->get_symbol_currency_by_nama($currency)->row_array();
                  $this->m_sales->ubah($sales_order, $reference, $warehouse, $currency, addslashes($curr_simbol['symbol']), $delivery_date, $time_ship, $note_head, $incoterm, $paymentterm, $destination, $bank, $clause, $note);
                  $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');

                  $jenis_log   = "edit";
                  $note_log    = $sales_order." | ".$reference." | ".$warehouse." | ".$currency." | ".$delivery_date." | ".$note_head;
                  $this->_module->gen_history($sub_menu, $sales_order, $jenis_log, $note_log, $username);

              }
          }

        }

        echo json_encode($callback);
    }

    public function edit($id = null)
    {   
        if(!isset($id)) show_404();
        $kode_decrypt      = decrypt_url($id);
        $id_dept           = 'SC';
        $data['id_dept']   = $id_dept;
        $data['mms']       = $this->_module->get_data_mms_for_log_history($id_dept);// get mms by dept untuk menu yg beda-beda
        $data['warehouse'] = $this->m_sales->get_list_departement();
        $data["currency"]  = $this->m_sales->get_list_currency();
        $data["salescontract"] = $this->m_sales->get_data_by_kode($kode_decrypt);
        $data["details"]   = $this->m_sales->get_data_detail_by_kode($kode_decrypt);
        $data["details_color_lines"] = $this->m_sales->get_data_color_line_by_kode($kode_decrypt);
        $data["incoterm"]  = $this->m_sales->get_list_incoterm();
        $data["paymentterm"]  = $this->m_sales->get_list_paymentterm();
        $data["tax"]       = $this->m_sales->get_list_tax();
        $data["list_uom"]  = $this->_module->get_list_uom();

        if(empty($data["salescontract"])){
          show_404();
        }else{
          return $this->load->view('sales/v_sales_contract_edit',$data);
        }


    }


    public function get_produk_select2()
    {
	    $prod = addslashes($this->input->post('prod'));
   		$callback = $this->m_sales->get_list_produk_by_name($prod);
      echo json_encode($callback);
    }


    public function get_taxes_select2()
    {
	    $prod = addslashes($this->input->post('prod'));
   		$callback = $this->m_sales->get_list_taxes_by_name($prod);
        echo json_encode($callback);
    }

    public function get_prod_by_id()
    {
	    $kode_produk = addslashes($this->input->post('kode_produk'));
   		$result      = $this->m_sales->get_produk_byid($kode_produk)->row_array();
      $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'] );
        echo json_encode($callback);
        
    }

    public function simpan_detail()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

      	  $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username')); 

  	    	$kode = $this->input->post('kode');
  	    	$kode_prod = addslashes($this->input->post('kode_prod'));
  	    	$prod  = addslashes($this->input->post('prod'));
	        $desc  = addslashes($this->input->post('desc'));
	        $qty   = $this->input->post('qty');
	        $uom   = addslashes($this->input->post('uom'));
	        $roll  = addslashes($this->input->post('roll'));
	        $price = $this->input->post('price');
	        $tax_id = $this->input->post('taxes');
	        $row    = $this->input->post('row_order');

          ////lock table
          $this->_module->lock_tabel('sales_contract WRITE, sales_contract_items WRITE, sales_contract_items as sci WRITE,  tax WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE');

          //cek status sales contract
          $status     = "status NOT IN ('draft','waiting_date')";
          $cek_status = $this->m_sales->cek_status_sales_contract($kode,$status)->row_array();
          
          if(!empty($cek_status['sales_order'])){
            $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa di Simpan !, Cek Status Sales Order !', 'icon' =>'fa fa-warning', 'type' => 'danger');

          }else{

            if(!empty($row)){//update details
              $tax = $this->m_sales->get_data_tax_by_kode($tax_id)->row_array();
              $this->m_sales->update_contract_lines_detail($kode,$kode_prod,$prod,$desc,$qty,$uom,$roll,$price,$tax_id,addslashes($tax['nama']),$row);
          
              //update total di tabel sales_contract
              $total = $this->m_sales->get_total_untaxed($kode)->row_array(); 
              $total_val = $total['total_untaxed']+$total['total_tax'];
              $this->m_sales->update_total_sales_contract($kode,$total['total_untaxed'],$total['total_tax'],$total_val);

              $jenis_log   = "edit";
              $note_log    = "Edit data Details | ".$kode." | ".$kode_prod." | ".$prod." | ".$desc." | ".$qty." | ".$uom." | ".$roll." | ".$price." | ".addslashes($tax['nama']);;
              $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);

              $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                  
            }else{//simpan data baru

                $ro  = $this->m_sales->get_row_order_sales_contract_items($kode)->row_array();
                $row_order = $ro['row_order']+1;
                $tax = $this->m_sales->get_data_tax_by_kode($tax_id)->row_array();
                $this->m_sales->save_contract_lines_detail($kode,$kode_prod,$prod,$desc,$qty,$uom,$roll,$price,$tax_id,addslashes($tax['nama']),$row_order);
                    
                //update total di tabel sales_contract
                $total = $this->m_sales->get_total_untaxed($kode)->row_array(); 
                $total_val = $total['total_untaxed']+$total['total_tax'];
                $this->m_sales->update_total_sales_contract($kode,$total['total_untaxed'],$total['total_tax'],$total_val);

                $jenis_log   = "edit";
                $note_log    = "Tambah data Details | ".$kode." | ".$kode_prod." | ".$prod." | ".$desc." | ".$qty." | ".$uom." | ".$roll." | ".$price." | ".addslashes($tax['nama']);
                $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);

                $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
              
              }
          }
          //unlock table
          $this->_module->unlock_tabel();

	        echo json_encode($callback);
        }
    }


    public function hapus_detail()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
      	  $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username')); 

        	$kode = $this->input->post('kode');
        	$row  = $this->input->post('row_order');

        	if(empty($kode) && empty($row) ){
          		$callback = array('status' => 'success','message' => 'Data Gagal Dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');
        	}else{

            //cek status sales contract
            $status     = "status IN ('draft','waiting_date')";
            $cek_status = $this->m_sales->cek_status_sales_contract($kode,$status)->row_array();

            if(empty($cek_status['sales_order'])){
              $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa di Hapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else{
          		$this->m_sales->delete_contract_lines_detail($kode,$row);
              $callback = array('status' => 'success','message' => 'Data Berhasil Dihapus !', 'icon' =>'fa fa-check', 'type' => 'success');
                  
              //update total di tabel sales_contract
  				    $total = $this->m_sales->get_total_untaxed($kode)->row_array(); 
  				    $total_val = $total['total_untaxed']+$total['total_tax'];
  				    $this->m_sales->update_total_sales_contract($kode,$total['total_untaxed'],$total['total_tax'],$total_val);

              //cek_sales_contract
              $cek_details = $this->m_sales->cek_sales_contract_items_by_kode($kode)->num_rows();

              if($cek_details==0){
                $status = 'draft';
                $this->m_sales->update_status_sales_contract($kode,$status);
              }

              $jenis_log   = "cancel";
              $note_log    = "Hapus data Details | ".$kode." | ".$row;
              $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);

            }
                
        	}

	        echo json_encode($callback);
        }
    }

    public function confirm_contract()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{	
    		  $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username'));

        	$sales_order = $this->input->post('sales_order');
        
          ///lock table
          $this->_module->lock_tabel('sales_contract WRITE, sales_contract_items WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE');

          //cek status sales contract
          $status     = "status IN ('draft')";
          $cek_status  = $this->m_sales->cek_status_sales_contract($sales_order,$status)->row_array();

          if(empty($cek_status['sales_order'])){
             $callback = array('status' => 'failed','message' => 'Maaf, Confirm Contract Sudah Dilakukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

          }else{
            $cek_details = $this->m_sales->cek_sales_contract_items_by_kode($sales_order)->num_rows();

          	if($cek_details > 0){
          		$status = 'waiting_date';
          		$this->m_sales->update_status_sales_contract($sales_order,$status);
          		$callback = array('status' => 'success','message' => 'Confirm Contract Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success');
          		
  	        	$jenis_log   = "edit";
  	          $note_log    = $sales_order.' -> Confirm Contract';
  	          $this->_module->gen_history($sub_menu, $sales_order, $jenis_log, $note_log, $username);
  	            
          	}else{
          		$callback = array('status' => 'failed','message' => 'Contract Line Items Masing Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
          	}
          }
           //unlock table
           $this->_module->unlock_tabel();
        	
        }

        echo json_encode($callback);
    }

    public function approve_contract()
    {

        if(empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{	
    	    $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username')); 

        	$sales_order = $this->input->post('sales_order');

           ///lock table
           $this->_module->lock_tabel('sales_contract WRITE, sales_contract_items WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE');

          //cek status sales contract
          $status     = "status NOT IN ('date_assigned')";
          $cek_status = $this->m_sales->cek_status_sales_contract($sales_order,$status)->row_array();

          if(!empty($cek_status['sales_order'])){
             $callback = array('status' => 'failed','message' => 'Maaf, Approve Contract Sudah Dilakukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

          }else{
          	$cek_details = $this->m_sales->cek_sales_contract_items_by_kode($sales_order)->num_rows();
          	if($cek_details > 0){
          		$status = 'waiting_color';
          		$this->m_sales->update_status_sales_contract($sales_order,$status);
          		$callback = array('status' => 'success','message' => 'Aprove Contract Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success');
          		
  	        	$jenis_log   = "edit";
  	          $note_log    = $sales_order.' -> Approve Contract';
  	          $this->_module->gen_history($sub_menu, $sales_order, $jenis_log, $note_log, $username);
  	            
          	}else{
          		$callback = array('status' => 'failed','message' => 'Contract Lines Items Masih Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
          	}
          } 

          // unclock table
          $this->_module->unlock_tabel();
        }

        echo json_encode($callback);
    }


    public function approve_color()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{  
          $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username'));

          $sales_order = $this->input->post('sales_order');

          ///lock table
          $this->_module->lock_tabel('sales_contract WRITE, sales_contract_items WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE, sales_color_line WRITE');

          //cek status sales contract
          $status     = "status IN ('waiting_color')";
          $cek_status = $this->m_sales->cek_status_sales_contract($sales_order,$status)->row_array();

          if(empty($cek_status['sales_order'])){
             $callback = array('status' => 'failed','message' => 'Maaf, Approve Color Sudah Dilakukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else{

            $cek_details = $this->m_sales->cek_sales_color_line_by_kode($sales_order)->num_rows();

            if($cek_details > 0){
              $status = 'product_generated';
              $is_approve = 'f';
              //update status sales contract
              $this->m_sales->update_status_sales_contract($sales_order,$status);
              //update is_approve color lines
              $this->m_sales->update_is_approve_color_lines($sales_order,$is_approve);
              $callback = array('status' => 'success','message' => 'Aprove Color Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success');
              
              $jenis_log   = "edit";
              $note_log    = $sales_order.' -> Approve Color';
              $this->_module->gen_history($sub_menu, $sales_order, $jenis_log, $note_log, $username);
                
            }else{
              $callback = array('status' => 'failed','message' => 'Color Line Masih Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }
            
          }
          //unlock table
          $this->_module->unlock_tabel();
        }

        echo json_encode($callback);
    }


    public function get_uom_select2()
    {
      $prod = addslashes($this->input->post('prod'));
      $callback = $this->m_sales->get_list_uom_select2_by_prod($prod);
      echo json_encode($callback);
    }


    /* Start COLOR LINES */

    public function get_produk_color_select2()
    {
      $prod = addslashes($this->input->post('prod'));
      $kode = $this->input->post('kode');
      $callback = $this->m_sales->get_list_produk_color_by_name($kode,$prod);
      echo json_encode($callback);
    }


    public function get_color_select2()
    {
      $prod = addslashes($this->input->post('prod'));
      $callback = $this->m_sales->get_list_color_by_name($prod);
      echo json_encode($callback);
    }


    public function simpan_detail_color_lines()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

          $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username'));

          $kode = $this->input->post('kode');
          $kode_prod = addslashes($this->input->post('kode_prod'));
          $prod  = addslashes($this->input->post('prod'));
          $desc  = addslashes($this->input->post('desc'));
          $color = addslashes($this->input->post('color'));
          $color_name = addslashes($this->input->post('color_name'));
          $qty   = $this->input->post('qty');
          $uom   = addslashes($this->input->post('uom'));
          $piece_info  = $this->input->post('piece_info');
          $row = $this->input->post('row_order');
          $date = date('Y-m-d H:i:s');

          if(!empty($row)){//update details
            $this->m_sales->update_color_lines_detail($kode,$desc,$color_name,$qty,$uom,$piece_info,$row);
            $jenis_log   = "edit";
            $note_log    = "Edit data Details Color Lines | ".$kode." | ".$desc." | ".$qty." | ".$uom." | ".$piece_info;
            $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
            $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');

          }else{//simpan data baru

            //check warna yang sama 
            $check_dcl = $this->m_sales->check_details_color_lines($kode,$kode_prod,$color)->row_array();
            if(empty($check_dcl['sales_order'])){
              $ro  = $this->m_sales->get_row_order_sales_color_lines($kode,$kode_prod)->row_array();
              $row_order = $ro['row_order']+1;
              $this->m_sales->save_color_lines($date,$kode_prod,$prod,$kode,$desc,$color,$color_name,$qty,$uom,$piece_info,$row_order);

              $jenis_log   = "edit";
              $note_log    = "Tambah data Details Color Lines | ".$kode." | ".$prod." | ".$desc."| ".$color."| ".$color_name." | ".$qty." | ".$uom." | ".$piece_info;
              $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
              $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
            }else{
              $callback = array('status' => 'failed','message' => 'Maaf, Product sudah pernah diinput !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }
                    
          }

          echo json_encode($callback);
        }
    }


    public function hapus_detail_color_lines()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

          $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username')); 

          $kode = $this->input->post('kode');
          $row  = $this->input->post('row_order');

          if(empty($kode) && empty($row) ){
              $callback = array('status' => 'failed','message' => 'Data Gagal Dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');
          }else{
            //check is Approve
            $check_approve = $this->m_sales->check_is_approve($kode,$row)->row_array();

            if($check_approve['is_approve'] == 't' || $check_approve['is_approve'] == 'f'){
              $callback = array('status' => 'failed',  'message' => 'Data Tidak bisa Dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
            }else{
              $this->m_sales->delete_color_lines_detail($kode,$row);
              $callback = array('status' => 'success','message' => 'Data Berhasil Dihapus !', 'icon' =>'fa fa-check', 'type' => 'success');
                  
              $jenis_log   = "cancel";
              $note_log    = "Hapus data Details Color Lines | ".$kode." | ".$row;
              $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
            }
                
          }

          echo json_encode($callback);
        }
    }

    /* Finish COLOR LINES */


    /* Start Modal modal*/

    public function create_color_modal()
    {
        return $this->load->view('modal/v_sales_contract_create_color_modal');
    }

    public function mode_print_modal()
    {
    	  $so   = $this->input->post('so');
        $data['so'] = array('so' => $so);
        return $this->load->view('modal/v_sales_contract_mode_print_modal', $data);
    }

    /*FInish Modal Modal*/

    public function print_view_idn()
    {

    	  $kode = $this->input->get('so');
        $so   = decrypt_url($kode);
        $sc   = $this->m_sales->get_data_by_kode($so);
        $pay  = $this->m_sales->get_data_paymentterm_by_kode($sc->paymentterm_id);
        $cust = $this->m_sales->get_data_customer_by_kode($sc->customer_id);
        $nama_sales_group = $this->_module->get_nama_sales_Group_by_kode($sc->sales_group);

    		$pdf = new FPDF('p','mm','legal');
    		// membuat halaman baru
    		$pdf->AddPage();
    		$pdf->SetMargins(13,20,10);
    		$pdf->Cell(10,65,'',0,1);//Buat Jarak ke bawah


    		// setting jenis font yang akan digunakan
    		$pdf->SetFont('Arial','B',10);
    		// mencetak string
    		$pdf->Cell(185,7,'Faktur dan Alamat Pengiriman',0,1,'L');
    		$pdf->SetFont('Arial','',9);
    		$pdf->Cell(100,5,$sc->customer_name,0,1,'L');
    		$pdf->Cell(100,5,$cust->invoice_street,0,1,'L');
    		$pdf->Cell(190,5,$cust->invoice_city." ".$cust->invoice_zip,0,1,'L');
    		$pdf->Cell(190,5,$cust->invoice_state,0,1,'L');

    		$pdf->Cell(10,7,'',0,1);//Buat Jarak ke bawah

    		$pdf->SetFont('Arial','b',20);
    		$pdf->Cell(190,7,'Order '.$sc->sales_order,0,1,'L');

    		$pdf->SetFont('Arial','B',9);
    		$pdf->Cell(10,7,'',0,1);//Buat Jarak ke bawah
    		if(!empty($sc->reference)){
    			$pdf->Cell(47,0.5,'Referensi :',0,0, 'L');
    		}
    		$pdf->Cell(49,0.5,'Tanggal Pembelian :',0,0, 'L');
    		$pdf->Cell(15,0.5,' Salesperson :',0,0, 'L');
    		$pdf->Cell(10,4,'',0,1);//Buat Jarak ke bawah

    		$pdf->SetFont('Arial','',9);
    		$tgl_trans  =  date('d-m-Y ', strtotime($sc->create_date)); 
    		if(!empty($sc->reference)){
    			$pdf->Cell(47,0.5,$sc->reference,0,0, 'L');
    		}
        $tgl_idn = tgl_indo($tgl_trans);
    		$pdf->Cell(50,0.5,$tgl_idn,0,0, 'L');
    		$pdf->Cell(15,0.5,$nama_sales_group,0,0, 'L');
    		$pdf->Cell(10,6,'',0,1);//Buat Jarak ke bawah

    		$pdf->SetFont('Arial','',9);
    		$pdf->Cell(10,6,'',0,1);//Buat Jarak ke bawah

    		$pdf->Cell(60,0.5,'Dengan Hormat, ',0,0, 'L');
    		$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
    		$pdf->Cell(60,0.5,'Dengan ini telah disetujui bersama untuk memesan barang-barang yang tertera sebagai berikut: ',0,0, 'L');


    		// Memberikan space kebawah agar tidak terlalu rapat
    		$pdf->Cell(10,4,'',0,1);
    		$pdf->Cell(177,1,'','B',0,'C');//garis atas header
    		$pdf->Cell(10,1,'',0,1);

    		$pdf->SetFont('Arial','B',9,0,1);
    		$pdf->Cell(7,6,'No',0,0);
    		$pdf->Cell(65,6,'Produk',0,0);
    		$pdf->Cell(20,6,'Jumlah',0,0,'R');
    		$pdf->Cell(20,6,'Satuan',0,0);
    		$pdf->Cell(30,6,'Harga Satuan',0,0,'R');
    		$pdf->Cell(35,6,'Sub total',0,1,'R');

    		$pdf->Cell(177,1,'','B',0,'C');//garis bawah header
    		$pdf->Cell(10,1,'',0,1);

    		$pdf->SetFont('Arial','',9);
		
        $list   = $this->m_sales->get_data_detail_by_kode($so);
        $no     = 1;
        foreach ($list as $row) {
        	$pdf->Cell(7,6,$no++,0,0,'LR');
        	$pdf->Cell(65,6,$row->description,0,0,'LR');
		    $pdf->Cell(20,6,number_format($row->qty,2),0,0,'R');
		    $pdf->Cell(20,6,$row->uom,0,0);
		    $pdf->Cell(30,6,number_format($row->price,4),0,0,'R');
		    $pdf->Cell(35,6,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format(($row->qty*$row->price),4),0,1,'R'); 
        }
				
			  $pdf->Cell(10,2,'',0,1);
			  $pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		    $pdf->SetFont('Arial','B',9);
		    $pdf->Cell(60,1,'','B',0,'C');//garis atas sub total
			
			  $pdf->Cell(10,4,'',0,1);

		    $pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		    $pdf->SetFont('Arial','B',9);
		    $pdf->Cell(25,0,'Sub Total',0,'L');
			  $pdf->SetFont('Arial','',9);
		    $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->untaxed_value,4),0,0,'R'); 

		  	$pdf->Cell(10,2,'',0,1);
		    $pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		    $pdf->SetFont('Arial','B',9);
		    $pdf->Cell(60,1,'','B',0,'C');//garis atas pajak
			
		  	$pdf->Cell(10,4,'',0,1);

        // get PPN by code
        $ppn = $this->m_sales->get_ppn_by_sc($so)->row_array();
        if(!empty($ppn['tax_id'])){
          $info_ppn= 'Pajak ('.$ppn['ket'].')';
        }else{
          $info_ppn= 'Pajak ';
        }
			
			  $pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		  	$pdf->SetFont('Arial','',9);
		    $pdf->Cell(25,0,$info_ppn,0,0);
		    $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->tax_value,4),0,0,'R'); 

		  	$pdf->Cell(10,2,'',0,1);
		  	$pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		    $pdf->SetFont('Arial','B',9);
		    $pdf->Cell(60,1,'','B',0,'C');//garis atas  total
			
			  $pdf->Cell(10,4,'',0,1);
		    $pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		  	$pdf->SetFont('Arial','B',9);
		    $pdf->Cell(25,0,'Total',0,0);
		  	$pdf->SetFont('Arial','',9);
		    $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->total_value,4),0,0,'R'); 


  		// Memberikan space kebawah agar tidak terlalu rapat
  		$pdf->Cell(10,10,'',0,1);
  		//FOOTER
  		$pdf->SetFont('Arial','B',9);
  		$pdf->Cell(33,0.5,'Tanggal Pengiriman :',0,0, 'L');
  		$pdf->SetFont('Arial','',9);
  		$pdf->Cell(15,0.5,$sc->delivery_date,0,0, 'L');
  		$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah

      if(!empty($pay->nama)){
  		$pdf->SetFont('Arial','B',9);
  		$pdf->Cell(33,0.5,'Metode Pembayaran :',0,0, 'L');
  		$pdf->SetFont('Arial','',9);
  		$pdf->Cell(15,0.5,$pay->nama,0,0, 'L');
  		$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
      }


  		$pdf->SetFont('Arial','B',9);
  		$pdf->Cell(38,0.5,'Bank :',0,0, 'L');
  		$pdf->Cell(10,5,'',0,1);
  		$pdf->SetFont('Arial','',9);
  		
  		$breaks = array("<br />","<br>","<br/>"); 
      $bank  = str_ireplace($breaks, "\n", nl2br(htmlspecialchars($sc->bank)));
  		$pdf->Multicell(0,2,$bank,0, 'L');
  		$pdf->Cell(10,5,'',0,1);//ENTER ke bawah
	  	
	  	if(!empty($sc->clause)){
  			$pdf->SetFont('Arial','B',9);  
  			$pdf->Cell(13,0.5,'Clause :',0,0, 'L');
  			$pdf->SetFont('Arial','',9);
  			$pdf->Cell(15,0.5,$sc->clause,0,0, 'L');
  			$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
	  	}

	  	if(!empty($sc->note)){
  			$pdf->SetFont('Arial','B',9);  
  			$pdf->Cell(14,4,'Catatan :',0,0, 'L');
  			$pdf->SetFont('Arial','',9);
        $pdf->Multicell(0,4,$sc->note,0, 'L');
  			//$pdf->cell(15,0.5,$sc->note,0,0, 'L');
  			$pdf->Cell(10,15,'',0,1);//Buat Jarak ke bawah
	   	}
  		$pdf->SetFont('Arial','B',9);  
  		$pdf->Cell(15,0,'');
  		$pdf->Cell(60,0.5,'Menyatakan Setuju, ',0,0, 'c');
  		$pdf->Cell(50,0,'');
  		$pdf->Cell(60,0.5,' PT.Heksatex Indah,  ',0,0, 'c');
  		$pdf->Cell(10,17,'',0,1);//Buat Jarak ke bawah

  		$pdf->Cell(10,4,'',0,1);
  		$pdf->Cell(6,0,'');
  		$pdf->Cell(3,0.5,'(',0,0, 'L');
  		$pdf->Cell(50,1,'','B',0,'L');
  		$pdf->Cell(28,0.5,')',0,0, 'L');

	    if(empty($sc->sales_group)){
  			$pdf->Cell(25,0,'');
  			$pdf->Cell(3,0.5,'(',0,0, 'L');
  			$pdf->Cell(50,1,'','B',0,'L');
  			$pdf->Cell(28,0.5,')',0,1, 'L');
  		}

	   	if(!empty($sc->sales_group)){

        $nama = $this->_module->get_nama_sales_Group_by_kode($sc->sales_group);
		    $pdf->Cell(5,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		  	$pdf->SetFont('Arial','B',9);
		    $pdf->Cell(60,0,'('.$nama.")",0,0,'C'); 
  			$pdf->SetFont('Arial','',9);
  		}

  		// Geser Ke Kanan 35mm
  		$pdf->Output();
      }

  public function print_view_eng()
  {

    	$kode = $this->input->get('so');
      $so   = decrypt_url($kode);
      $sc   = $this->m_sales->get_data_by_kode($so);
      $pay  = $this->m_sales->get_data_paymentterm_by_kode($sc->paymentterm_id);
      $cust = $this->m_sales->get_data_customer_by_kode($sc->customer_id);
      $nama_sales_group = $this->_module->get_nama_sales_Group_by_kode($sc->sales_group);


    	$pdf = new FPDF('p','mm','legal');
  		// membuat halaman baru
  		$pdf->AddPage();
  		$pdf->SetMargins(13,20,10);
  		$pdf->Cell(10,65,'',0,1);//Buat Jarak ke bawah


  		// setting jenis font yang akan digunakan
  		$pdf->SetFont('Arial','B',10);
  		// mencetak string
  		$pdf->Cell(185,7,'Invoice',0,1,'L');
  		$pdf->SetFont('Arial','',9);
  		$pdf->Cell(100,5,$sc->customer_name,0,1,'L');
  		$pdf->Cell(100,5,$cust->invoice_street,0,1,'L');
  		$pdf->Cell(190,5,$cust->invoice_city." ".$cust->invoice_zip,0,1,'L');
  		$pdf->Cell(190,5,$cust->invoice_state,0,1,'L');

  		$pdf->Cell(10,7,'',0,1);//Buat Jarak ke bawah

  		$pdf->SetFont('Arial','b',20);
  		$pdf->Cell(190,7,'Order '.$sc->sales_order,0,1,'L');

  		$pdf->SetFont('Arial','B',9);
  		$pdf->Cell(10,7,'',0,1);//Buat Jarak ke bawah
  		if(!empty($sc->reference)){
  			$pdf->Cell(47,0.5,'Reference :',0,0, 'L');
  		}
  		$pdf->Cell(49,0.5,'Date Ordered :',0,0, 'L');
  		$pdf->Cell(15,0.5,' Salesperson :',0,0, 'L');
  		$pdf->Cell(10,4,'',0,1);//Buat Jarak ke bawah

  		$pdf->SetFont('Arial','',9);
  		$tgl_trans  =  date('d-m-Y ', strtotime($sc->create_date)); 
  		if(!empty($sc->reference)){
  			$pdf->Cell(47,0.5,$sc->reference,0,0, 'L');
  		}
      $tgl_eng = tgl_eng($tgl_trans);
  		$pdf->Cell(50,0.5,$tgl_eng,0,0, 'L');
  		$pdf->Cell(15,0.5,$nama_sales_group,0,0, 'L');
  		$pdf->Cell(10,6,'',0,1);//Buat Jarak ke bawah

  		// Memberikan space kebawah agar tidak terlalu rapat
  		$pdf->Cell(10,4,'',0,1);
  		$pdf->Cell(177,1,'','B',0,'C');//garis atas header
  		$pdf->Cell(10,1,'',0,1);

  		$pdf->SetFont('Arial','B',9,0,1);
  		$pdf->Cell(7,6,'No',0,0);
  		$pdf->Cell(65,6,'Poduct Name',0,0);
  		$pdf->Cell(20,6,'Quantiy',0,0,'R');
  		$pdf->Cell(20,6,'UoM',0,0);
  		$pdf->Cell(30,6,'Unit Price',0,0,'R');
  		$pdf->Cell(35,6,'Amount',0,1,'R');

  		$pdf->Cell(177,1,'','B',0,'C');//garis bawah header
  		$pdf->Cell(10,1,'',0,1);

	   	$pdf->SetFont('Arial','',9);
		
        $list   = $this->m_sales->get_data_detail_by_kode($so);
        $no     = 1;
        foreach ($list as $row) {
        	$pdf->Cell(7,6,$no++,0,0,'LR');
        	$pdf->Cell(65,6,$row->description,0,0,'LR');
		    $pdf->Cell(20,6,number_format($row->qty,2),0,0,'R');
		    $pdf->Cell(20,6,$row->uom,0,0);
		    $pdf->Cell(30,6,number_format($row->price,4),0,0,'R');
		    $pdf->Cell(35,6,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format(($row->qty*$row->price),4),0,1,'R'); 
        }
				
	  		$pdf->Cell(10,2,'',0,1);
		  	$pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		    $pdf->SetFont('Arial','B',9);
		    $pdf->Cell(60,1,'','B',0,'C');//garis atas sub total
			
	   		$pdf->Cell(10,4,'',0,1);

		    $pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		    $pdf->SetFont('Arial','B',9);
		    $pdf->Cell(25,0,'Sub Total',0,'L');
	   		$pdf->SetFont('Arial','',9);
		    $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->untaxed_value,4),0,0,'R'); 

		  	$pdf->Cell(10,2,'',0,1);
		    $pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		    $pdf->SetFont('Arial','B',9);
		    $pdf->Cell(60,1,'','B',0,'C');//garis atas pajak
			
	   		$pdf->Cell(10,4,'',0,1);

         // get PPN by code
        $ppn = $this->m_sales->get_ppn_by_sc($so)->row_array();
        if(!empty($ppn['tax_id'])){
          $info_ppn= 'Taxes ('.$ppn['ket'].')';
        }else{
          $info_ppn= 'Taxes ';
        }
			
		  	$pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
	   		$pdf->SetFont('Arial','',9);
		    $pdf->Cell(25,0,$info_ppn,0,0);
		    $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->tax_value,4),0,0,'R'); 

		  	$pdf->Cell(10,2,'',0,1);
	   		$pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		    $pdf->SetFont('Arial','B',9);
		    $pdf->Cell(60,1,'','B',0,'C');//garis atas  total
			
	   		$pdf->Cell(10,4,'',0,1);
		    $pdf->Cell(60,0,'',0,0);
		    $pdf->Cell(37,0,'',0,0);
		    $pdf->Cell(20,0,'',0,0);
		  	$pdf->SetFont('Arial','B',9);
		    $pdf->Cell(25,0,'Total',0,0);
		  	$pdf->SetFont('Arial','',9);
		    $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->total_value,4),0,0,'R'); 


    		// Memberikan space kebawah agar tidak terlalu rapat
    		$pdf->Cell(10,10,'',0,1);
    		//FOOTER
    		$pdf->SetFont('Arial','B',9);
    		$pdf->Cell(23,0.5,'Delivery Date :',0,0, 'L');
    		$pdf->SetFont('Arial','',9);
    		$pdf->Cell(15,0.5,$sc->delivery_date,0,0, 'L');
    		$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah

        if(!empty($pay->nama)){          
    		$pdf->SetFont('Arial','B',9);
    		$pdf->Cell(24,0.5,'Payment Term :',0,0, 'L');
    		$pdf->SetFont('Arial','',9);
    		$pdf->Cell(15,0.5,$pay->nama,0,0, 'L');
    		$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
        }


    		$pdf->SetFont('Arial','B',9);
    		$pdf->Cell(38,0.5,'Bank :',0,0, 'L');
    		$pdf->Cell(10,5,'',0,1);
    		$pdf->SetFont('Arial','',9);
    		
    		$breaks = array("<br />","<br>","<br/>"); 
        $bank  = str_ireplace($breaks, "\n", nl2br(htmlspecialchars($sc->bank)));
    		$pdf->Multicell(0,2,$bank,0, 'L');
    		$pdf->Cell(10,5,'',0,1);//ENTER ke bawah
    	  	
    	  if(!empty($sc->clause)){
    			$pdf->SetFont('Arial','B',9);  
    			$pdf->Cell(13,0.5,'Clause :',0,0, 'L');
    			$pdf->SetFont('Arial','',9);
    			$pdf->Cell(15,0.5,$sc->clause,0,0, 'L');
    			$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
    	  	}

    	  if(!empty($sc->note)){
    			$pdf->SetFont('Arial','B',9);  
    			$pdf->Cell(10,4,'Note :',0,0, 'L');
    			$pdf->SetFont('Arial','',9);
    			//$pdf->cell(15,0.5,$sc->note,0,0, 'L');
          $pdf->Multicell(0,4,$sc->note,0, 'L');
    			$pdf->Cell(10,15,'',0,1);//Buat Jarak ke bawah
    		}
    		$pdf->SetFont('Arial','B',9);  
    		$pdf->Cell(60,0.5,'We hereby confirm and accept this contract of sales, ',0,0, 'c');
    		$pdf->Cell(65,0,'');
    		$pdf->Cell(60,0.5,' PT.Heksatex Indah,  ',0,0, 'c');
    		$pdf->Cell(10,17,'',0,1);//Buat Jarak ke bawah

    		$pdf->Cell(10,4,'',0,1);
    		$pdf->Cell(7,0,'');
    		$pdf->Cell(3,0.5,'(',0,0, 'L');
    		$pdf->Cell(50,1,'','B',0,'L');
    		$pdf->Cell(28,0.5,')',0,0, 'L');

    		if(empty($sc->sales_group)){
    			$pdf->Cell(25,0,'');
    			$pdf->Cell(3,0.5,'(',0,0, 'L');
    			$pdf->Cell(50,1,'','B',0,'L');
    			$pdf->Cell(28,0.5,')',0,1, 'L');
    		}

    		if(!empty($sc->sales_group)){

    		  $pdf->Cell(5,0,'',0,0);
    		  $pdf->Cell(20,0,'',0,0);
    			$pdf->SetFont('Arial','B',9);
    		  $pdf->Cell(60,0,'('.$nama_sales_group.")",0,0,'C'); 
    			$pdf->SetFont('Arial','',9);
    		}

    		$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
    		$pdf->Cell(1,0,'');
    		$pdf->Cell(28,0.5,'Please return one copy with your due signature',0,1, 'L');

    		// Geser Ke Kanan 35mm
    		$pdf->Output();
        }

}