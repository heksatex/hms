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

          //cek status sales contract
          $status     = "status IN ('done')";
          $cek_status = $this->m_sales->cek_status_sales_contract($sales_order,$status)->row_array();

          $status2     = "status IN ('cancel')";
          $cek_status2 = $this->m_sales->cek_status_sales_contract($sales_order,$status2)->row_array();

          $status3     = "status NOT IN ('draft','waiting_date','date_assigned')";
          $cek_status3 = $this->m_sales->cek_status_sales_contract($sales_order,$status3)->row_array();

          // cek sales_person
          $sg = $this->m_sales->cek_sales_group_by_username($username)->row_array();

          if(!empty($cek_status['sales_order'])){
            $callback = array('status' => 'failed','alert2' => 'yes','message' => 'Maaf, Data tidak bisa di Simpan !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');

          }elseif(!empty($cek_status2['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes', 'message' => 'Maaf, Data tidak bisa di Simpan !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

          }elseif(!empty($cek_status3['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes','message' => 'Maaf, Data tidak bisa di Simpan !, Cek Status Sales Contract !', 'icon' =>'fa fa-warning', 'type' => 'danger');
       
          }elseif(empty($customer)){
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

    public function batal()
    {
      if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
      }else{

          $sub_menu  = $this->uri->segment(2);
          $username  = addslashes($this->session->userdata('username')); 

          $sales_order  = addslashes($this->input->post('sales_order'));

          //cek status sales contract
          $status     = "status IN ('done')";
          $cek_status = $this->m_sales->cek_status_sales_contract($sales_order,$status)->row_array();
  
          $status2     = "status IN ('cancel')";
          $cek_status2 = $this->m_sales->cek_status_sales_contract($sales_order,$status2)->row_array();
  
          $status3     = "status NOT IN ('draft','waiting_date')";
          $cek_status3 = $this->m_sales->cek_status_sales_contract($sales_order,$status3)->row_array();

          
          if(!empty($cek_status['sales_order'])){
            $callback = array('status' => 'failed','alert2' => 'yes','message' => 'Maaf, Data tidak bisa dibatalkan !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');

          }elseif(!empty($cek_status2['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes', 'message' => 'Maaf, Data tidak bisa dibatalkan !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

          }elseif(!empty($cek_status3['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes','message' => 'Maaf, Data tidak bisa dibatalkan !, Cek Status Sales Contract !', 'icon' =>'fa fa-warning', 'type' => 'danger');

          }else{

            $this->m_sales->update_status_sales_contract($sales_order,'cancel');

            $jenis_log   = "cancel";
            $note_log    = "Batal SC | ".$sales_order;
            $this->_module->gen_history($sub_menu, $sales_order, $jenis_log, $note_log, $username);

            $callback = array('status' => 'success','message' => 'Sales Contract Berhasil dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'success');

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
        $data["details"]       = $this->m_sales->get_data_detail_by_kode($kode_decrypt);
        $data["details_color_lines"] = $this->m_sales->get_data_color_line_by_kode($kode_decrypt);
        $data["incoterm"]      = $this->m_sales->get_list_incoterm();
        $data["paymentterm"]   = $this->m_sales->get_list_paymentterm();
        $data["tax"]           = $this->m_sales->get_list_tax();
        $data["list_uom"]      = $this->_module->get_list_uom();
        $data['handling']      = $this->_module->get_list_handling();
        $data['route']         = $this->_module->get_list_route_co();
        $data['list_status_ow'] = $this->list_status_ow();

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
      $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'], 'lebar_jadi'=>$result['lebar_jadi'], 'uom_lebar_jadi'=>$result['uom_lebar_jadi'] );
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
          $status     = "status IN ('done')";
          $cek_status = $this->m_sales->cek_status_sales_contract($kode,$status)->row_array();
 
          $status2     = "status IN ('cancel')";
          $cek_status2 = $this->m_sales->cek_status_sales_contract($kode,$status2)->row_array();
 
          $status3     = "status NOT IN ('draft','waiting_date')";
          $cek_status3 = $this->m_sales->cek_status_sales_contract($kode,$status3)->row_array();
 
 
          if(!empty($cek_status['sales_order'])){
            $callback = array('status' => 'failed','alert2' => 'yes','message' => 'Maaf, Data tidak bisa di Simpan !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');
 
          }elseif(!empty($cek_status2['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes', 'message' => 'Maaf, Data tidak bisa di Simpan !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
 
          }elseif(!empty($cek_status3['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes','message' => 'Maaf, Data tidak bisa di Simpan !, Cek Status Sales Contract !', 'icon' =>'fa fa-warning', 'type' => 'danger');
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
            $status     = "status IN ('done')";
            $cek_status = $this->m_sales->cek_status_sales_contract($kode,$status)->row_array();
  
            $status2     = "status IN ('cancel')";
            $cek_status2 = $this->m_sales->cek_status_sales_contract($kode,$status2)->row_array();
  
            $status3     = "status IN ('draft','waiting_date')";
            $cek_status3 = $this->m_sales->cek_status_sales_contract($kode,$status3)->row_array();
  
  
            if(!empty($cek_status['sales_order'])){
              $callback = array('status' => 'failed','alert2' => 'yes','message' => 'Maaf, Data tidak bisa di Hapus !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');
  
            }elseif(!empty($cek_status2['sales_order'])){
              $callback = array('status' => 'failed' ,'alert2' => 'yes', 'message' => 'Maaf, Data tidak bisa di Hapus !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
  
            }elseif(empty($cek_status3['sales_order'])){
              $callback = array('status' => 'failed' ,'alert2' => 'yes','message' => 'Maaf, Data tidak bisa di Hapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');

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
          $status     = "status IN ('done')";
          $cek_status = $this->m_sales->cek_status_sales_contract($sales_order,$status)->row_array();
  
          $status2     = "status IN ('cancel')";
          $cek_status2 = $this->m_sales->cek_status_sales_contract($sales_order,$status2)->row_array();
  
          $status3     = "status IN ('draft')";
          $cek_status3 = $this->m_sales->cek_status_sales_contract($sales_order,$status3)->row_array();
  
  
          if(!empty($cek_status['sales_order'])){
            $callback = array('status' => 'failed','alert2' => 'yes','message' => 'Maaf, Data tidak bisa di Confirm !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');
  
          }elseif(!empty($cek_status2['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes', 'message' => 'Maaf, Data tidak bisa di Confirm !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
  
          }elseif(empty($cek_status3['sales_order'])){
             $callback = array('status' => 'failed' ,'alert2' => 'yes','message' => 'Maaf, Confirm Contract Sudah Dilakukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

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
          $status     = "status IN ('done')";
          $cek_status = $this->m_sales->cek_status_sales_contract($sales_order,$status)->row_array();
    
          $status2     = "status IN ('cancel')";
          $cek_status2 = $this->m_sales->cek_status_sales_contract($sales_order,$status2)->row_array();
    
          $status3     = "status NOT IN ('date_assigned')";
          $cek_status3 = $this->m_sales->cek_status_sales_contract($sales_order,$status3)->row_array();
    
          if(!empty($cek_status['sales_order'])){
            $callback = array('status' => 'failed','alert2' => 'yes','message' => 'Maaf, Data tidak bisa di Approve !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');
    
          }elseif(!empty($cek_status2['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes', 'message' => 'Maaf, Data tidak bisa di Approve !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
    
          }elseif(!empty($cek_status3['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes','message' => 'Maaf, Approve Contract Sudah Dilakukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');


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
          $status     = "status IN ('done')";
          $cek_status = $this->m_sales->cek_status_sales_contract($sales_order,$status)->row_array();
    
          $status2     = "status IN ('cancel')";
          $cek_status2 = $this->m_sales->cek_status_sales_contract($sales_order,$status2)->row_array();
    
          $status3     = "status IN ('waiting_color')";
          $cek_status3 = $this->m_sales->cek_status_sales_contract($sales_order,$status3)->row_array();
    
          if(!empty($cek_status['sales_order'])){
            $callback = array('status' => 'failed','alert2' => 'yes','message' => 'Maaf, Data tidak bisa di Approve !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');
    
          }elseif(!empty($cek_status2['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes', 'message' => 'Maaf, Data tidak bisa di Approve !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
    
          }elseif(empty($cek_status3['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes','message' => 'Maaf, Approve Color Sudah Dilakukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

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

          $kode         = $this->input->post('kode');
          $kode_prod    = addslashes($this->input->post('kode_prod'));
          $prod         = addslashes($this->input->post('prod'));
          $desc         = addslashes($this->input->post('desc'));
          $color        = addslashes($this->input->post('color'));
          $color_name   = addslashes($this->input->post('color_name'));
          $qty          = $this->input->post('qty');
          $uom          = addslashes($this->input->post('uom'));
          $piece_info   = addslashes($this->input->post('piece_info'));
          $row          = $this->input->post('row_order');
          $handling     = $this->input->post('handling');
          $route_co     = $this->input->post('route_co');
          $gramasi      = $this->input->post('gramasi');
          $lebar_jadi   = $this->input->post('lebar_jadi');
          $uom_lebar_jadi   = $this->input->post('uom_lebar_jadi');
          $reff_note    = addslashes($this->input->post('reff_note'));
          $delivery_date= addslashes($this->input->post('delivery_date'));
          $status_ow    = addslashes($this->input->post('status_ow'));
          $date         = date('Y-m-d H:i:s');

          if(!empty($handling)){
            $hd = $this->_module->get_handling_by_id($handling)->row_array();
            $nama_handling = $hd['nama_handling'];
          }else{
            $nama_handling = '';
          }

          if(!empty($color)){
            $wr = $this->_module->get_warna_by_id($color)->row_array();
            $nama_warna = $wr['nama_warna'];
          }else{
            $nama_warna = '';
          }

          //cek status sales contract
          $status     = "status IN ('done')";
          $cek_status = $this->m_sales->cek_status_sales_contract($kode,$status)->row_array();
      
          $status2     = "status IN ('cancel')";
          $cek_status2 = $this->m_sales->cek_status_sales_contract($kode,$status2)->row_array();
      
          $status3     =  "status NOT IN ('waiting_color', 'product_generated')";
          $cek_status3 = $this->m_sales->cek_status_sales_contract($kode,$status3)->num_rows();
      
          if(!empty($cek_status['sales_order'])){
            $callback = array('status' => 'failed','alert2' => 'yes','message' => 'Maaf, Color Lines tidak bisa disimpan !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');
      
          }elseif(!empty($cek_status2['sales_order'])){
            $callback = array('status' => 'failed' ,'alert2' => 'yes', 'message' => 'Maaf, Color Lines tidak bisa disimpan !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
      
          }elseif($cek_status3 > 0){
              $callback = array('status' => 'failed' ,'alert2' => 'yes','message' => 'Maaf, Color Lines tidak bisa disimpan, Status Sales Contract tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger');

          }else{

            if(!empty($route_co)){
                // get nama route co
                $route = $this->_module->get_nama_route_by_kode($route_co)->row_array();
                $nama_route = $route['nama'];
            }else{
                $nama_route = '';
            }

            // cek uom contract line by product tidak sama dengan Mtr
            $cek_uom = $this->m_sales->cek_uom_contract_line_by_produk($kode,$kode_prod,'Mtr')->num_rows();

            // cek qty by produk qty contract line
            $cq_contract_lines = $this->m_sales->cek_qty_contract_lines_by_produk($kode,$kode_prod);

            // cek qty by produk qty color line
            $cq_color_lines = $this->m_sales->cek_qty_color_lines_by_produk($kode,$kode_prod);

            foreach($this->list_status_ow() as $st){
                if($status_ow == $st['kode']){
                  $status_ow_ = $st['nama'];
                  break;
                }
            }

            if(!empty($row)){//update details

              // cek apakah sudah terbentuk OW atau belum 
              $cek_item = $this->m_sales->cek_item_color_lines_by_kode($kode,$row)->row_array();
              
              if(!empty($cek_item['ow'])){
                $callback = array('status' => 'failed','message' => 'Color Lines tidak disimpan, OW Sudah dibuat !', 'icon' =>'fa fa-warning', 'type' => 'danger');

              }else{

                $qty_now = $cek_item['qty'];
                // qty by produk setelah dikurang 
                $tot_qty_color_line_new = ($cq_color_lines - $qty_now ) + $qty;
                
                if($tot_qty_color_line_new <= $cq_contract_lines OR $cek_uom > 0){
                  
                  $this->m_sales->update_color_lines_detail($kode,$desc,$color, $color_name,$qty,$piece_info,$row,$handling,$gramasi,$lebar_jadi,$uom_lebar_jadi,$route_co,$reff_note,$delivery_date);
                  $jenis_log   = "edit";
                  $note_log    = "Edit data Details Color Lines | ".$kode." | ".$desc."| ".$nama_warna."| ".$color_name."| ".$nama_handling."| ".$nama_route." | ".$gramasi." | ".$qty." | ".$lebar_jadi." | ".$uom_lebar_jadi." | ".$piece_info." | ".$reff_note." | ".$delivery_date;
                  $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);
                  $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                }else{
                  
                  $sisa_qty_insert_color_lines = $cq_contract_lines - $tot_qty_color_line_new;// cek sisa qty yang bisa insert ke color lines
                  $uom     = $cek_item['uom'];
                  
                  if($sisa_qty_insert_color_lines < 0 ){
                    $message = "Qty Color Line melebihi dari target Contract Lines, Qty yang diinputkan ke Color Line lebih ".-1*$sisa_qty_insert_color_lines." ".$uom." ";
                  }else{
                    $message = "Qty Color Line melebihi dari target Contract Lines, Sisa Qty yang bisa diinputkan ke Color Line tersisa ".$sisa_qty_insert_color_lines." ".$uom." lagi";
                  }

                  $callback = array('status' => 'failed','message' => $message, 'icon' =>'fa fa-warning', 'type' => 'danger');
                }

              }
                
            }else{//simpan data baru

              if($status_ow == 't' or $status_ow == 'ng'){

                
                if($cq_color_lines <= $cq_contract_lines OR $cek_uom > 0 ){

                  $tot_qty_color_line_new = $cq_color_lines + $qty;

                  if($tot_qty_color_line_new <= $cq_contract_lines OR $cek_uom > 0){

                    $ro        = $this->m_sales->get_row_order_sales_color_lines($kode)->row_array();
                    $row_order = $ro['row_order']+1;
                    $this->m_sales->save_color_lines($date,$kode_prod,$prod,$kode,$desc,$color,$color_name,$qty,$uom,$piece_info,$row_order,$gramasi,$handling,$lebar_jadi,$uom_lebar_jadi,$route_co,$reff_note,$delivery_date, $status_ow);

                    // cek status sales_contract
                    $is_approve_null = $this->m_sales->cek_color_lines_is_approve_null($kode);
      
                    if($is_approve_null > 0){
                      $this->m_sales->update_status_sales_contract($kode,'waiting_color');
                    }

                    $jenis_log   = "edit";
                    $note_log    = "Tambah data Details Color Lines | ".$kode." | ".$prod." | ".$desc."| ".$nama_warna."| ".$color_name."| ".$nama_handling." | ".$nama_route." | ".$gramasi." | ".$qty." | ".$uom." | ".$lebar_jadi." | ".$uom_lebar_jadi." | ".$piece_info." | ".$reff_note." | ".$delivery_date." | ".$status_ow_;
                    $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);
                    $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');

                  }else{

                    $sisa_qty_insert_color_lines = $cq_contract_lines - $tot_qty_color_line_new;// cek sisa qty yang bisa insert ke color lines
                    if($sisa_qty_insert_color_lines < 0 ){
                      $message = "Qty Color Line melebihi dari target Contract Lines, Qty yang diinputkan ke Color Line lebih ".-1*$sisa_qty_insert_color_lines." ".$uom." ";
                    }else{
                      $message = "Qty Color Line melebihi dari target Contract Lines, Sisa Qty yang bisa diinputkan ke Color Line tersisa ".$sisa_qty_insert_color_lines." ".$uom." lagi";
                    }

                    $callback = array('status' => 'failed','message' => $message, 'icon' =>'fa fa-warning', 'type' => 'danger');

                    }
                  
                }else{
                  $callback = array('status' => 'failed','message' => 'Qty Color Line Sudah Melebihi dari Target Contract Lines', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }
                
              }else{// status ow tidak aktif / reproses

                // cek status sales_contract
                $is_approve_null = $this->m_sales->cek_color_lines_is_approve_null($kode);
      
                if($is_approve_null > 0){
                  $this->m_sales->update_status_sales_contract($kode,'waiting_color');
                }

                $ro        = $this->m_sales->get_row_order_sales_color_lines($kode)->row_array();
                $row_order = $ro['row_order']+1;
                $this->m_sales->save_color_lines($date,$kode_prod,$prod,$kode,$desc,$color,$color_name,$qty,$uom,$piece_info,$row_order,$gramasi,$handling,$lebar_jadi,$uom_lebar_jadi,$route_co,$reff_note,$delivery_date,$status_ow);

                $jenis_log   = "edit";
                $note_log    = "Tambah data Details Color Lines | ".$kode." | ".$prod." | ".$desc."| ".$nama_warna."| ".$color_name."| ".$nama_handling." | ".$nama_route." | ".$gramasi." | ".$qty." | ".$uom." | ".$lebar_jadi." | ".$uom_lebar_jadi." | ".$piece_info." | ".$reff_note." | ".$delivery_date." | ".$status_ow_;
                $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);
                $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');

              }
                      
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

            //cek status sales contract
            $status     = "status IN ('done')";
            $cek_status = $this->m_sales->cek_status_sales_contract($kode,$status)->row_array();
        
            $status2     = "status IN ('cancel')";
            $cek_status2 = $this->m_sales->cek_status_sales_contract($kode,$status2)->row_array();
            
            //check is Approve
            $check_approve = $this->m_sales->check_is_approve($kode,$row)->row_array();
            
            if(!empty($cek_status['sales_order'])){
              $callback = array('status' => 'failed','alert2' => 'yes','message' => 'Maaf, Color Lines tidak bisa Dihapus !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');
        
            }elseif(!empty($cek_status2['sales_order'])){
              $callback = array('status' => 'failed' ,'alert2' => 'yes', 'message' => 'Maaf, Color Lines tidak bisa Dihapus !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if($check_approve['is_approve'] == 't' || $check_approve['is_approve'] == 'f'){
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

    public function create_OW()
    {

        try {
          //code...
          if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                // $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis ', 200);
          }else{
              $this->load->library('wa_message');

              $sub_menu  = $this->uri->segment(2);
              $username  = addslashes($this->session->userdata('username')); 

              $kode = addslashes($this->input->post('kode'));
              $row  = $this->input->post('row_order');
              $tgl  = date('Y-m-d H:i:s');

              // start transaction
              $this->_module->startTransaction();

              // lock tabel
              $this->_module->lock_tabel('sales_color_line WRITE,  log_history WRITE, main_menu_sub WRITE, user WRITE, sales_contract WRITE, wa_template WRITE, wa_group WRITE, wa_send_message WRITE, sales_contract as sc WRITE, mst_sales_group as mst WRITE, mst_sales_group WRITE, wa_group as a WRITE, wa_group_departemen as b WRITE, sales_color_line as scl WRITE, mst_status as mst_stat WRITE, mst_sales_group as msg WRITE, warna as w WRITE, mst_handling as hdl WRITE, route_co as rc WRITE, mst_status as ms WRITE, job_list_lab WRITE');

              //cek status sales contract
              $status     = "status IN ('done')";
              $cek_status = $this->m_sales->cek_status_sales_contract($kode,$status)->row_array();
            
              $status2     = "status IN ('cancel')";
              $cek_status2 = $this->m_sales->cek_status_sales_contract($kode,$status2)->row_array();

              // cek validasi item apakah ada atau tidak
              // cek validasi apakah items tersebut sudah terbuat OW atau belum
              $items = $this->m_sales->cek_item_color_lines_by_kode($kode,$row)->row_array();

              if(!empty($cek_status['sales_order'])){
                $callback = array('status' => 'failed','alert2' => 'yes','message' => 'Maaf, Create OW tidak Bisa dilakukan !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');
          
              }elseif(!empty($cek_status2['sales_order'])){
                $callback = array('status' => 'failed' ,'alert2' => 'yes', 'message' => 'Maaf, Create OW tidak Bisa dilakukan !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

              }else  if(empty($items['sales_order'])){
                $callback = array('status' => 'failed','message' => 'Data Items Color Line tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

              }else if(!empty($items['ow'])){
                $callback = array('status' => 'failed','message' => 'Data Color Lines ini sudah dibuat OW !', 'icon' =>'fa fa-warning', 'type' => 'danger');

              }else{

                // get last no ow
                $no_ow = $this->m_sales->no_OW();
          
                // update tangal ow dan no ow
                $this->m_sales->simpan_no_ow_sales_color_line($kode,$row,$no_ow,$tgl);

                $jenis_log   = "edit";
                $note_log    = "Membuat OW | ".$no_ow." | ". $row;
                $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);

                // insert into job list Lab
                $items_2 = $this->m_sales->get_item_color_lines_by_ow($kode,$no_ow);

                
                if($items_2){

                  $data_array[] = array(
                                  "id_sales_color_line"=> $items_2->id,
                                  "sales_order"       => $items_2->sales_order,
                                  "tanggal_buat"      => $tgl,
                                  "kode_sales_group"  => $items_2->sales_group,
                                  "nama_sales_group"  => $items_2->nama_sales_group,
                                  "no_ow"             => $items_2->ow,
                                  "tgl_ow"            => $items_2->tanggal_ow,
                                  // "status_ow"         => $status_ow,
                                  "kode_produk"       => $items_2->kode_produk,
                                  "nama_produk"       => $items_2->nama_produk,
                                  "warna_id"          => $items_2->id_warna,
                                  "nama_warna"        => $items_2->nama_warna,
                                  "gramasi"           => $items_2->gramasi,
                                  "id_handling"       => $items_2->id_handling,
                                  "nama_handling"     => $items_2->nama_handling,
                                  "route"             => $items_2->route_co,
                                  "nama_route"        => $items_2->nama_route,
                                  "lebar_jadi"        => $items_2->lebar_jadi,
                                  "uom_lebar_jadi"    => $items_2->uom_lebar_jadi,
                                  // "status_dti"        => $status_dti,                                
                                  "reff_note"         => $items_2->reff_notes,
                                  "delivery_date"     => $items_2->delivery_date,
                                  "status_resep"      => 'draft',
                                  "tgl_selesai_resep" => '',
                  );
                } else {
                  throw new \Exception('Data OW tidak ditemukan ', 200);
                }


                $this->m_sales->save_job_list_lab_by_ow($data_array);

                // SEND WA MESSAGE  -->>
                $data_head = $this->m_sales->get_data_by_kode($kode);
                $kode_mkt  = $data_head->sales_group ?? '';
                $reff_note = addslashes($items['reff_notes']);
                $status_ow = $items['nama_status'];// Aktif, Tidak Aktif, Not Good, Reproses
                $nama_mkt  = $this->_module->get_nama_sales_Group_by_kode($kode_mkt);
                $template_name = 'create_ow';
                $list_value = array(
                              '{no_sc}'     => $kode,
                              '{mkt}'       => $nama_mkt,
                              '{no_ow}'     => $no_ow,
                              '{reff_note}' => $reff_note,
                              '{status_ow}' => $status_ow
                              );
                $list_dept = array('GRG','LAB','PPIC-DF');

                $wa_send = $this->wa_message->sendMessageToGroupByDepth($template_name,$list_value,$list_dept);

                // mention
                $list_number  = $this->_module->get_list_number_user_by_dept($list_dept);
                $list_numbers = array_column($list_number,'telepon_wa');
                $telp_me      = $this->_module->cek_telepon_wa_by_user($username);
                $telp_mes     = array_column($telp_me,'telepon_wa');
                $list_number_group = array_merge($list_numbers,$telp_mes);
                $this->wa_message->setMentions($list_number_group);
                
                //footer
                $default_footer_wa = 'footer_hms';
                $wa_send->setFooter($default_footer_wa);;
                
                $wa_send->send();
                // SEND WA MESSAGE  <<--              

                $callback = array('status' => 'success','message' => ' OW telah berhasil dibuat !', 'icon' =>'fa fa-check', 'type' => 'success');
              }

              // unlock tabel
              $this->_module->unlock_tabel();

          }

          if (!$this->_module->finishTransaction()) {
            throw new \Exception('Create OW Gagal', 500);
          }

          $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

        } catch (Exception $ex) {
          //throw $th;

          $this->output->set_status_header($ex->getCode() ?? 500)
          ->set_content_type('application/json', 'utf-8')
          ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
          $this->_module->rollbackTransaction();
          // unlock table
          $this->_module->unlock_tabel();
        }

    }

    function list_status_ow()
    {
        $list = array(
            array('kode' => 't', 'nama'=>'Aktif'),
            array('kode' => 'f', 'nama'=>'Tidak Aktif'),
            array('kode' => 'ng', 'nama'=>'Not Good'),
            array('kode' => 'r', 'nama'=>'Reproses'),
        );

       
        return $list;
    }


    public function update_status_color_lines()
    {

      try {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $this->load->library('wa_message');
            
            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 

            $sales_order  = addslashes($this->input->post('sales_order'));
            $row_order    = $this->input->post('row_order');
            $value        = $this->input->post('value');
            $ow           = $this->input->post('ow');
            $kode_produk  = $this->input->post('kode_produk');
            $qty          = $this->input->post('qty');
            $approve      = $this->input->post('approve');
            $tgl          = date('Y-m-d H:i:s');

            // start transaction
            $this->_module->startTransaction();

            $this->_module->lock_tabel("sales_contract_items WRITE, sales_contract WRITE, sales_color_line WRITE, sales_color_line as scl WRITE, mst_status as mst_stat WRITE, log_history WRITE, user WRITE, main_menu_sub WRITE, sales_contract as sc WRITE, mst_sales_group WRITE, mst_sales_group as mst WRITE, wa_template WRITE, wa_group as  a WRITE, wa_group_departemen as b WRITE, wa_send_message WRITE, color_order as co WRITE, color_order_detail as cod WRITE");

            $items = $this->m_sales->cek_item_color_lines_by_kode($sales_order,$row_order)->row_array();

            //cek status sales contract
            $status     = "status IN ('done')";
            $cek_status = $this->m_sales->cek_status_sales_contract($sales_order,$status)->row_array();
              
            $status2     = "status IN ('cancel')";
            $cek_status2 = $this->m_sales->cek_status_sales_contract($sales_order,$status2)->row_array();

            if(!empty($cek_status['sales_order'])){
              $callback = array('status' => 'failed','status2'=>'failed','alert2' => 'yes','message' => 'Maaf, Data tidak bisa Disimpan !, Status Sales Contract Sudah Selesai !', 'icon' =>'fa fa-warning', 'type' => 'danger');
        
            }elseif(!empty($cek_status2['sales_order'])){
              $callback = array('status' => 'failed' ,'status2'=>'failed','alert2' => 'yes', 'message' => 'Maaf, Data tidak bisa Disimpan !, Status Sales Contract dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }elseif(empty($items['sales_order'])){
              $callback = array('status' => 'failed','status2'=>'failed','message' => 'Data Items Color Line tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else{

              $status_ow_lama = $items['nama_status'];

              $status_ow_baru = "";
              foreach($this->list_status_ow() as $st){
                  if($value == $st['kode']){
                    $status_ow_baru = $st['nama'];
                    break;
                  }
              }

              if($value == 'f' and $approve == 'no' and !empty($ow) ) {
                // cek ow sudah terbentuk color order atau belum
                $cek_cod = $this->m_sales->cek_color_order_by_ow($sales_order,$ow);
                if($cek_cod > 0){
                  throw new \Exception('Sales Contract OW ini sudah terbentuk Color Order !', 200);
                }
              }

              $lebih_target = false;
              if($value == 't' or $value =='ng' ){
                // cek qty by produk qty contract line
                $cq_contract_lines = $this->m_sales->cek_qty_contract_lines_by_produk($sales_order,$kode_produk);
  
                // cek qty by produk qty color line tidak sama dengan baris yg akan diubah
                $cq_color_lines = $this->m_sales->cek_qty_color_lines_by_produk_2($sales_order,$kode_produk,$row_order);
  
                if($cq_color_lines > $cq_contract_lines){
                  $lebih_target = true;  
                  $callback = array('status' => 'failed', 'status2'=>'failed','message' => 'OW tidak bisa di aktifkan, Karena Qty Color Line Sudah Melebihi dari Target Contract Lines', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }

                // tambah qty yg akan di aktifkan
                $tot_qty_color_line_new = $cq_color_lines + $qty;
                if($tot_qty_color_line_new > $cq_contract_lines ){ 
                  $lebih_target = true;  
                  $callback = array('status' => 'failed','message' => 'OW tidak bisa di aktifkan, Karena Qty Color Line Sudah Melebihi dari Target Contract Lines', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }

              }
              if($lebih_target == false ){
                // update status sales Color Lines
                $this->m_sales->update_status_color_line_by_row($sales_order,$row_order,$value,$ow);

                $note_status= $ow.' '.$status_ow_lama.' -> '.$status_ow_baru;

                $jenis_log   = "edit";
                $note_log    = "Update Status | ".$note_status;
                $this->_module->gen_history($sub_menu, $sales_order, $jenis_log, $note_log, $username);
                $callback = array('status' => 'success','message' => ' Status Berhasil di Rubah!', 'icon' =>'fa fa-check', 'type' => 'success');

                if($ow != ''){
                  // SEND WA MESSAGE  -->>
                  $data_head = $this->m_sales->get_data_by_kode($sales_order);
                  $kode_mkt  = $data_head->sales_group ?? '';
                  $reff_note = addslashes($items['reff_notes']);
                  $status_ow = $items['nama_status'];// Aktif, Tidak Aktif, Not Good, Reproses
                  $nama_mkt  = $this->_module->get_nama_sales_Group_by_kode($kode_mkt);
                  $template_name = 'edit_status_ow';
                  $list_value = array(
                                '{no_sc}'     => $sales_order,
                                '{mkt}'       => $nama_mkt,
                                '{no_ow}'     => $ow,
                                '{reff_note}' => $reff_note,
                                '{status_ow_lama}' => $status_ow_lama,
                                '{status_ow_baru}' => $status_ow_baru
                                );
                  $list_dept = array('GRG','LAB','PPIC-DF');

                  $wa_send = $this->wa_message->sendMessageToGroupByDepth($template_name,$list_value,$list_dept);

                  // mention
                  $list_number  = $this->_module->get_list_number_user_by_dept($list_dept);
                  $list_numbers = array_column($list_number,'telepon_wa');
                  $telp_me      = $this->_module->cek_telepon_wa_by_user($username);
                  $telp_mes     = array_column($telp_me,'telepon_wa');
                  $list_number_group = array_merge($list_numbers,$telp_mes);
                  $this->wa_message->setMentions($list_number_group);
                  
                  //footer
                  $default_footer_wa = 'footer_hms';
                  $wa_send->setFooter($default_footer_wa);;
                  
                  $wa_send->send();
                  // SEND WA MESSAGE  <<-- 
                }

              }
            }

        }

        if (!$this->_module->finishTransaction()) {
          throw new \Exception('Create OW Gagal', 500);
        }

        $this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

      } catch (Exception $ex) {
        $this->output->set_status_header($ex->getCode() ?? 500)->set_content_type('application/json', 'utf-8')->set_output(json_encode(array('status'=>'failed','status2'=>'','message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
      } finally {
        $this->_module->rollbackTransaction();
        // unlock table
        $this->_module->unlock_tabel();
      }


       
    }

    /* Finish COLOR LINES */


    /* Start Modal modal*/

    public function create_color_modal()
    {
      if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
        // session habis
        print_r('Waktu And Telah Habis, Silahkan Login Kembali !');
      }else{
        $username  = addslashes($this->session->userdata('username')); 
        $sales = $this->m_sales->cek_sales_group_by_username($username)->row_array();
        $data['sales_group'] = $sales['sales_group'];
        return $this->load->view('modal/v_sales_contract_create_color_modal',$data);
        
      }
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
        $state   = $this->m_sales->get_partner_states_by_kode($cust->invoice_city)->row_array();// id_state
        $country = $this->m_sales->get_partner_country_by_kode($cust->invoice_city)->row_array();// id_country
        $inisial_sales_group = $this->_module->get_inisial_sales_Group_by_kode($sc->sales_group);

    		$pdf = new FPDF('p','mm','legal');
    		// membuat halaman baru
    		$pdf->AddPage();
    		$pdf->SetMargins(13,20,10);
    		$pdf->Cell(10,65,'',0,1);//Buat Jarak ke bawah


    		// setting jenis font yang akan digunakan
    		$pdf->SetFont('Arial','B',10);
    		// mencetak string
    		$pdf->Cell(185,7,'Faktur dan Alamat Pengiriman',0,1,'L');
    		$pdf->SetFont('Arial','',10);
    		$pdf->Cell(100,5,$sc->customer_name,0,1,'L');
        $pdf->Multicell(100,4,$cust->invoice_street,0, 'L');
    		$pdf->Cell(190,5,$country['name']." ".$cust->invoice_zip,0,1,'L');
    		$pdf->Cell(190,5,$state['name'],0,1,'L');

    		$pdf->Cell(10,7,'',0,1);//Buat Jarak ke bawah

    		$pdf->SetFont('Arial','b',20);
    		$pdf->Cell(190,7,'Order '.$sc->sales_order,0,1,'L');

    		$pdf->SetFont('Arial','B',10);
    		$pdf->Cell(10,7,'',0,1);//Buat Jarak ke bawah
    		if(!empty($sc->reference)){
         
    			$pdf->Cell(47,0.5,'Referensi :',0,0, 'L');
    		}
    		$pdf->Cell(49,0.5,'Tanggal Pembelian :',0,0, 'L');
    		$pdf->Cell(15,0.5,' Salesperson :',0,0, 'L');
    		$pdf->Cell(10,4,'',0,1);//Buat Jarak ke bawah

    		$pdf->SetFont('Arial','',10);
    		$tgl_trans  =  date('d-m-Y ', strtotime($sc->create_date)); 
    		if(!empty($sc->reference)){
    			$pdf->Cell(47,0.5,$sc->reference,0,0, 'L');
    		}
        $tgl_idn = tgl_indo($tgl_trans);
    		$pdf->Cell(50,0.5,$tgl_idn,0,0, 'L');
    		$pdf->Cell(15,0.5,$inisial_sales_group,0,0, 'L');
    		$pdf->Cell(10,6,'',0,1);//Buat Jarak ke bawah

    		$pdf->SetFont('Arial','',10);
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
          $pdf->Cell(30,6,number_format($row->price,2),0,0,'R');
          $pdf->Cell(35,6,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format(($row->qty*$row->price),2),0,1,'R'); 
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
		    $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->untaxed_value,2),0,0,'R'); 

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
		    $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->tax_value,2),0,0,'R'); 

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
		    $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->total_value,2),0,0,'R'); 


  		// Memberikan space kebawah agar tidak terlalu rapat
  		$pdf->Cell(10,10,'',0,1);
  		//FOOTER
  		$pdf->SetFont('Arial','B',10);
  		$pdf->Cell(37,0.5,'Tanggal Pengiriman :',0,0, 'L');
  		$pdf->SetFont('Arial','',9);
  		$pdf->Cell(15,0.5,$sc->delivery_date,0,0, 'L');
  		$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah

      if(!empty($pay->nama)){
  		$pdf->SetFont('Arial','B',10);
  		$pdf->Cell(37,0.5,'Metode Pembayaran :',0,0, 'L');
  		$pdf->SetFont('Arial','',10);
  		$pdf->Cell(15,0.5,$pay->nama,0,0, 'L');
  		$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
      }

  		$pdf->SetFont('Arial','B',10);
  		$pdf->Cell(38,0.5,'Bank :',0,0, 'L');
  		$pdf->Cell(10,5,'',0,1);
  		$pdf->SetFont('Arial','',10);
  		
  		$breaks = array("<br />","<br>","<br/>"); 
      $bank  = str_ireplace($breaks, "\n", nl2br(htmlspecialchars($sc->bank)));
  		$pdf->Multicell(0,2,$bank,0, 'L');
  		$pdf->Cell(10,5,'',0,1);//ENTER ke bawah
	  	
	  	if(!empty($sc->clause)){
  			$pdf->SetFont('Arial','B',10);  
  			$pdf->Cell(13,0.5,'Clause :',0,0, 'L');
  			$pdf->SetFont('Arial','',9);
  			$pdf->Cell(15,0.5,$sc->clause,0,0, 'L');
  			$pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
	  	}

	  	if(!empty($sc->note)){
  			$pdf->SetFont('Arial','B',10);  
  			$pdf->Cell(14,4,'Catatan :',0,0, 'L');
  			$pdf->SetFont('Arial','',10);
        $pdf->Multicell(0,4,$sc->note,0, 'L');
  			//$pdf->cell(15,0.5,$sc->note,0,0, 'L');
  			$pdf->Cell(10,15,'',0,1);//Buat Jarak ke bawah
	   	}
       
  		$pdf->Cell(10,5,'',0,1);//ENTER ke bawah
       
  		$pdf->SetFont('Arial','B',10);  
  		$pdf->Cell(10,0,'');
  		$pdf->Cell(60,0.5,'Menyatakan Setuju, ',0,0, 'c');
  		$pdf->Cell(15,0,'');
  		$pdf->Cell(55,0.5,'Direksi',0,0, 'c');
  		$pdf->Cell(60,0.5,' PT.Heksatex Indah,  ',0,0, 'c');
  		$pdf->Cell(10,17,'',0,1);//Buat Jarak ke bawah

  		$pdf->Cell(15,4,'',0,1);
  		$pdf->Cell(5,0,'');
  		$pdf->Cell(3,0.5,'(',0,0, 'L');
  		$pdf->Cell(40,1,'','B',0,'L');
  		$pdf->Cell(25,0.5,')',0,0, 'L');

  		$pdf->Cell(3,0.5,'(',0,0, 'L');
  		$pdf->Cell(40,1,'','B',0,'L');
  		$pdf->Cell(13,0.5,')',0,0, 'L');

	    if(empty($sc->sales_group)){
  			$pdf->Cell(25,0,'');
  			$pdf->Cell(3,0.5,'(',0,0, 'L');
  			$pdf->Cell(50,1,'','B',0,'L');
  			$pdf->Cell(28,0.5,')',0,1, 'L');
  		}

	   	if(!empty($sc->sales_group)){

		  	$pdf->SetFont('Arial','B',10);
		    $pdf->Cell(60,0,'('.$inisial_sales_group.")",0,0,'C'); 
  			$pdf->SetFont('Arial','',10);
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
        $state   = $this->m_sales->get_partner_states_by_kode($cust->invoice_city)->row_array();// id_state
        $country = $this->m_sales->get_partner_country_by_kode($cust->invoice_city)->row_array();// id_country
        $inisial_sales_group = $this->_module->get_inisial_sales_Group_by_kode($sc->sales_group);


        $pdf = new FPDF('p','mm','legal');
        // membuat halaman baru
        $pdf->AddPage();
        $pdf->SetMargins(13,20,10);
        $pdf->Cell(10,65,'',0,1);//Buat Jarak ke bawah


        // setting jenis font yang akan digunakan
        $pdf->SetFont('Arial','B',10);
        // mencetak string
        $pdf->Cell(185,7,'Invoice',0,1,'L');
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(100,5,$sc->customer_name,0,1,'L');
        $pdf->Multicell(100,4,$cust->invoice_street,0, 'L');
        $pdf->Cell(190,5,$country['name']." ".$cust->invoice_zip,0,1,'L');
        $pdf->Cell(190,5,$state['name'],0,1,'L');

        $pdf->Cell(10,7,'',0,1);//Buat Jarak ke bawah

        $pdf->SetFont('Arial','b',20);
        $pdf->Cell(190,7,'Order '.$sc->sales_order,0,1,'L');

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(10,7,'',0,1);//Buat Jarak ke bawah
        if(!empty($sc->reference)){
          $pdf->Cell(47,0.5,'Reference :',0,0, 'L');
        }
        $pdf->Cell(49,0.5,'Date Ordered :',0,0, 'L');
        $pdf->Cell(15,0.5,' Salesperson :',0,0, 'L');
        $pdf->Cell(10,4,'',0,1);//Buat Jarak ke bawah

        $pdf->SetFont('Arial','',10);
        $tgl_trans  =  date('d-m-Y ', strtotime($sc->create_date)); 
        if(!empty($sc->reference)){
          $pdf->Cell(47,0.5,$sc->reference,0,0, 'L');
        }
        $tgl_eng = tgl_eng($tgl_trans);
        $pdf->Cell(50,0.5,$tgl_eng,0,0, 'L');
        $pdf->Cell(15,0.5,$inisial_sales_group,0,0, 'L');
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
          $pdf->Cell(30,6,number_format($row->price,2),0,0,'R');
          $pdf->Cell(35,6,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format(($row->qty*$row->price),2),0,1,'R'); 
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
          $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->untaxed_value,2),0,0,'R'); 

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
          $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->tax_value,2),0,0,'R'); 

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
          $pdf->Cell(35,0,iconv('utf-8', 'cp1252', $sc->currency_symbol)." ".number_format($sc->total_value,2),0,0,'R'); 


          // Memberikan space kebawah agar tidak terlalu rapat
          $pdf->Cell(10,10,'',0,1);
          //FOOTER
          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(25,0.5,'Delivery Date :',0,0, 'L');
          $pdf->SetFont('Arial','',10);
          $pdf->Cell(15,0.5,$sc->delivery_date,0,0, 'L');
          $pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah

          if(!empty($pay->nama)){          
          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(28,0.5,'Payment Term :',0,0, 'L');
          $pdf->SetFont('Arial','',10);
          $pdf->Cell(15,0.5,$pay->nama,0,0, 'L');
          $pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
          }


          $pdf->SetFont('Arial','B',10);
          $pdf->Cell(38,0.5,'Bank :',0,0, 'L');
          $pdf->Cell(10,5,'',0,1);
          $pdf->SetFont('Arial','',10);
          
          $breaks = array("<br />","<br>","<br/>"); 
          $bank  = str_ireplace($breaks, "\n", nl2br(htmlspecialchars($sc->bank)));
          $pdf->Multicell(0,2,$bank,0, 'L');
          $pdf->Cell(10,5,'',0,1);//ENTER ke bawah
            
          if(!empty($sc->clause)){
            $pdf->SetFont('Arial','B',10);  
            $pdf->Cell(13,0.5,'Clause :',0,0, 'L');
            $pdf->SetFont('Arial','',10);
            $pdf->Cell(15,0.5,$sc->clause,0,0, 'L');
            $pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
            }

          if(!empty($sc->note)){
            $pdf->SetFont('Arial','B',10);  
            $pdf->Cell(10,4,'Note :',0,0, 'L');
            $pdf->SetFont('Arial','',10);
            //$pdf->cell(15,0.5,$sc->note,0,0, 'L');
            $pdf->Multicell(0,4,$sc->note,0, 'L');
            $pdf->Cell(10,15,'',0,1);//Buat Jarak ke bawah
          }

          $pdf->Cell(10,5,'',0,1);//ENTER ke bawah

          $pdf->SetFont('Arial','B',10);  
          $pdf->Cell(5,0,'');
          $pdf->Cell(50,0.5,'We hereby confirm and accept ',0,1, 'C');
          $pdf->Cell(5,0,'');
          $pdf->Cell(50,5,'this contract of sales, ',0,0, 'C');
          $pdf->Cell(10,0,'');
          $pdf->Cell(60,0.5,'Directors,  ',0,0, 'C');
          $pdf->Cell(3,0,'');
          $pdf->Cell(60,0.5,' PT.Heksatex Indah,  ',0,0, 'C');
          $pdf->Cell(10,17,'',0,1);//Buat Jarak ke bawah

          $pdf->Cell(10,4,'',0,1);
          $pdf->Cell(1,0,'');
          $pdf->Cell(3,0.5,'(',0,0, 'L');
          $pdf->Cell(50,1,'','B',0,'L');
          $pdf->Cell(15,0.5,')',0,0, 'L');

          $pdf->Cell(3,0.5,'(',0,0, 'L');
          $pdf->Cell(50,1,'','B',0,'L');
          $pdf->Cell(15,0.5,')',0,0, 'L');

          if(empty($sc->sales_group)){
            $pdf->Cell(3,0.5,'(',0,0, 'L');
            $pdf->Cell(40,1,'','B',0,'L');
            $pdf->Cell(28,0.5,')',0,1, 'L');
          }

          if(!empty($sc->sales_group)){
            $pdf->SetFont('Arial','B',10);
            $pdf->Cell(40,0,'('.$inisial_sales_group.")",0,0,'C'); 
            $pdf->SetFont('Arial','',10);
          }

          $pdf->Cell(10,5,'',0,1);//Buat Jarak ke bawah
          $pdf->Cell(10,0,'');
          $pdf->Cell(28,0.5,'Please return one copy ',0,1, 'L');
          $pdf->Cell(10,0,'');
          $pdf->Cell(28,7,'with your due signature',0,1, 'L');

          // Geser Ke Kanan 35mm
          $pdf->Output();
    }

}