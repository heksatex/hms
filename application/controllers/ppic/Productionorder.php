<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');
/**
 * 
 */
class Productionorder extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('m_productionOrder');
		$this->load->model('_module');
		
	}

	public function index()
	{	
        $data['id_dept']='PRD';
		$this->load->view('ppic/v_production_order', $data);
	}

	function get_data()
    {	
    	$sub_menu  = $this->uri->segment(2);
    	$kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $list = $this->m_productionOrder->get_datatables($kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->kode_prod);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('ppic/productionorder/edit/'.$kode_encrypt).'">'.$field->kode_prod.'</a>';
            $row[] = $field->create_date;
            $row[] = $field->schedule_date;
            $row[] = $field->sales_order;
            $row[] = $field->priority;
            $row[] = $field->nama_dept;
            $row[] = $field->notes;
            $row[] = $field->nama_status;
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_productionOrder->count_all($kode['kode']),
            "recordsFiltered" => $this->m_productionOrder->count_filtered($kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

    public function add()
    {
        $data['id_dept']   = 'PRD';
        $data['warehouse'] = $this->_module->get_list_departement();
    	return $this->load->view('ppic/v_production_order_add', $data);
    }

    public function list_sales_order_modal()
    {
        return $this->load->view('modal/v_sales_order_modal');
    }

    public function get_data_sales_contract_modal()
    {
        $list = $this->m_productionOrder->get_datatables2();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $field->sales_order .' <a href="#" class="pilih" sales_order="'.$field->sales_order.'" ><span class="glyphicon glyphicon-check"></span></a>';
            $row[] = $field->buyer_code;
            $row[] = '<a href="#" class="pilih" sales_order="'.$field->sales_order.'">'.$field->sales_group.'</a>';
 
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_productionOrder->count_all2(),
            "recordsFiltered" => $this->m_productionOrder->count_filtered2(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


    public function view_detail_items()
    {
        $kode        = $this->input->post('kode');
        $sales_order = $this->input->post('sales_order');
        $kode_produk = $this->input->post('kode_produk');
        $nama_produk = $this->input->post('nama_produk');
        $row_order   = $this->input->post('row_order');
        $origin      = addslashes($sales_order.'|'.$kode.'|'.$row_order);
        $kode_bom    = $this->input->post('kode_bom');
        $data['kode_bom'] = $kode_bom;
        $bom         = $this->_module->cek_bom_by_kode_bom($kode_bom)->row_array();
        $data['nama_bom'] = $bom['nama_bom'];

        $data['kode_produk'] = $kode_produk;
        $data['nama_produk'] = $nama_produk;
        $data['kode']        = $kode;
        $data['row_order']   = $row_order;
        $data['origin']      = $origin;
        $data['penerimaan']  = $this->_module->get_detail_items_penerimaan($origin);
        $data['pengiriman']  = $this->_module->get_detail_items_pengiriman($origin);
        $data['mo']          = $this->_module->get_detail_items_mo($origin);
        //$data['detail_items']= $this->m_mo->view_detail_items($origi);
        return $this->load->view('modal/v_production_order_detail_items_modal', $data);
    }


    public function simpan()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            //session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 

        	$kode_prod   = addslashes($this->input->post('kode_prod'));
            $tgl         = $this->input->post('tgl');
            $note        = addslashes($this->input->post('note'));
            $sales_order = addslashes($this->input->post('sales_order'));
            $priority    = addslashes($this->input->post('priority'));
            $warehouse   = addslashes($this->input->post('warehouse'));

            if(empty($tgl)){
                $callback = array('status' => 'failed', 'field' => 'tgl', 'message' => 'Create Date Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    
            }elseif(empty($note)){
                $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Notes Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
            }elseif(empty($sales_order)){
                $callback = array('status' => 'failed', 'field' => 'sales_order', 'message' => 'Sales Order Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );    
            }elseif(empty($warehouse)){
                $callback = array('status' => 'failed', 'field' => 'warehouse', 'message' => 'Departement Tujuan Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger' );  

            }elseif(empty($priority)){
                $callback = array('status' => 'failed', 'field' => 'priority', 'message' => 'Priority Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger' );    
            }else{

                if(empty($kode_prod)){//jika kode production order kosong, aksinya simpan data

                  //cek  sales order apa sudah pernah diinput ?
                  $cek_so = $this->m_productionOrder->cek_production_order_by_sales_order($sales_order)->num_rows();
                  if($cek_so != 0){
                    $callback = array('status' => 'failed', 'field' => 'sales_order', 'message' => 'Maaf, Sales Order sudah pernah diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger' );  
                  }else{

                    $kode['kode_prod'] =  $this->m_productionOrder->kode_prod();//get no production order
                    $kode_encrypt    = encrypt_url($kode['kode_prod']);
                    $tgl_buat        = date('Y-m-d H:i:s');
                    $this->m_productionOrder->simpan($kode['kode_prod'], $tgl_buat, $tgl, $note, $sales_order, $priority, $warehouse, 'draft');

                    $callback = array('status' => 'success', 'field' => 'kode_prod' , 'message' => 'Data Berhasil Disimpan !', 'isi'=> $kode['kode_prod'], 'icon' =>'fa fa-check', 'type' => 'success', 'kode_encrypt' => $kode_encrypt);
                      
                    $jenis_log = "create";
                    $note_log  =$kode['kode_prod']." | ".$tgl." | ".$note." | ".$sales_order." | ".$priority." | ".$warehouse;
                    $this->_module->gen_history($sub_menu, $kode['kode_prod'], $jenis_log, $note_log, $username);
                  }

				
                }else{//jika kode production order ada, aksinya update data

                  //cek status detail apa sudah generate ?
                  $where_status       = "AND status IN ('generated')";
                  $cek_details_status = $this->m_productionOrder->cek_status_production_order_items($kode_prod,$where_status)->num_rows();
                  $detail_generate    = false;
                  $ubah_warehouse     = false;

                  if($cek_details_status > 0){
                    $detail_generate = true;
                    //cek warehouse by production order
                    $cek_warehouse = $this->m_productionOrder->cek_warehouse_production_order_by_kode($kode_prod)->row_array();
                    if($warehouse != $cek_warehouse['warehouse']){
                        $ubah_warehouse = true;
                    }else{
                        $ubah_warehouse = false;
                    }
                  }else{
                    $detail_generate = false;
                  }


                  if($detail_generate == true AND $ubah_warehouse == true){
                    $callback = array('status' => 'failed', 'field' => 'warehouse','message' => 'Maaf, Warehouse tidak Bisa diubah !', 'icon' =>'fa fa-warning','type' => 'danger' );  

                  }else{
                    $this->m_productionOrder->ubah($kode_prod, $tgl, $note, $sales_order, $priority, $warehouse);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');
                  
                    $jenis_log = "edit";
                    $note_log  = $kode_prod." | ".$tgl." | ".$note." | ".$sales_order." | ".$priority." | ".$warehouse;
                    $this->_module->gen_history($sub_menu, $kode_prod, $jenis_log, $note_log, $username);

                  }

                }
            }
        }

        echo json_encode($callback);
    }


    public function edit($id = null)
    {   
        if(!isset($id)) show_404();
        $kode_decrypt      = decrypt_url($id);
        $data['id_dept']   ='PRD';
        $data["productionorder"] = $this->m_productionOrder->get_data_by_code($kode_decrypt);
        $data['details']    = $this->m_productionOrder->get_data_detail_by_code($kode_decrypt);
        $data['warehouse']  = $this->_module->get_list_departement();
        $data['uom']        = $this->_module->get_list_uom();

        if(empty($data["productionorder"])){
          show_404();
        }else{
          return $this->load->view('ppic/v_production_order_edit',$data);
        }
    }

    public function get_produk_select2_so()
    {
	    $prod        = addslashes($this->input->post('prod'));
	    $sales_order = addslashes($this->input->post('sales_order'));
   		$callback    = $this->m_productionOrder->get_list_produk_by_so($prod,$sales_order);
        echo json_encode($callback);
    }

    public function get_prod_by_id_so()
    {
	    $kode_produk = addslashes($this->input->post('kode_produk'));
	    $sales_order = addslashes($this->input->post('sales_order'));
   		$result      = $this->m_productionOrder->get_produk_byid_so($kode_produk,$sales_order)->row_array();
        $result2     = $this->_module->get_uom_by_kode_produk($kode_produk)->row_array();
        //cek nama_produk by kode_produk
        $cp          = $this->m_productionOrder->cek_nama_produk_by_kode($kode_produk)->row_array();
        $nama_produk = $cp['nama_produk'];
        if(stripos($nama_produk, "Inspecting") !== FALSE){
            $prod_exp = explode('"',$nama_produk);
            $nama_produk = $prod_exp[0].'" (Jacquard)';
        }else{
            $nama_produk = $nama_produk;
        }

        $result3     = $this->m_productionOrder->get_bom_by_nama_produk($nama_produk)->row_array();
        $result4     = $this->m_productionOrder->get_lebar_by_kode_produk($kode_produk)->row_array();

        $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result2['uom'], 'qty'=>$result['qty'], 'kode_bom'=>$result3['kode_bom'], 'nama_bom'=>$result3['nama_bom'], 'lebar_greige' => $result4['lebar_greige'], 'uom_lebar_greige' => $result4['uom_lebar_greige'], 'lebar_jadi' => $result4['lebar_jadi'], 'uom_lebar_jadi' => $result4['uom_lebar_jadi'] );
        echo json_encode($callback);
    }

    public function get_bom_select2_by_produk()
    {
        $bom         = addslashes($this->input->post('bom'));
        $kode_produk = addslashes($this->input->post('kode_produk'));
        //cek nama_produk by kode_produk
        $cp          = $this->m_productionOrder->cek_nama_produk_by_kode($kode_produk)->row_array();
        $nama_produk = $cp['nama_produk'];
        if(stripos($nama_produk, "Inspecting") !== FALSE){
            $prod_exp = explode('"',$nama_produk);
            $nama_produk = $prod_exp[0].'" (Jacquard)';
        }else{
            $nama_produk = $nama_produk;
        }

        $callback    = $this->m_productionOrder->get_list_bom_by_nama_produk($bom,$nama_produk);
        echo json_encode($callback);
    }

    public function simpan_detail_production_order()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

        	$sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 

        	$kode        = $this->input->post('kode'); 
            $kode_produk = addslashes($this->input->post('kode_produk')); 
            $produk      = addslashes($this->input->post('produk')); 
            $kode_bom    = addslashes($this->input->post('kode_bom')); 
            $tgl         = $this->input->post('tgl'); 
            $qty         = $this->input->post('qty'); 
            $uom         = addslashes($this->input->post('uom')); 
            $lebar_greige= addslashes($this->input->post('lebar_greige')); 
            $uom_lebar_greige   = addslashes($this->input->post('uom_lebar_greige')); 
            $lebar_jadi  = addslashes($this->input->post('lebar_jadi')); 
            $uom_lebar_jadi     = addslashes($this->input->post('uom_lebar_jadi')); 
            $reff        = addslashes($this->input->post('reff')); 
            $row         = $this->input->post('row_order'); 

	        if(!empty($row)){//update details
	        	  
                $ex_row = explode("^|",$row);
                $row_order_ex_row   = $ex_row[0];
                $kode_produk_ex_row = addslashes($ex_row[1]);

                //cek status produk, dan cek apa produk masih ada ?
                $cek_status = $this->m_productionOrder->cek_status_production_order_items_by_row($kode,$kode_produk_ex_row,$row_order_ex_row)->row_array(); 

                if(empty($cek_status['kode_produk'])){
                    $callback = array('status' => 'failed','message' => 'Maaf, Produk Kosong !', 'icon' =>'fa fa-check', 'type' => 'danger');

                }else if($cek_status['status'] == 'generated'){
                    $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Diubah, Status Product Sudah Generated !', 'icon' =>'fa fa-check', 'type' => 'danger');

                }else{

    				$this->m_productionOrder->update_production_order_items($kode,$tgl,$kode_produk_ex_row,$kode_bom,$qty,$reff,$lebar_greige,$uom_lebar_greige,$lebar_jadi,$uom_lebar_jadi,$row_order_ex_row);
                    
                    $jenis_log   = "edit";
                    $note_log    = "Edit data Details | ".$kode." | ".$tgl." | ".$kode_produk_ex_row." | ".$kode_bom." | ".$qty." | ".$lebar_greige." ".$uom_lebar_greige." | ".$lebar_jadi." ".$uom_lebar_jadi." | ".$reff." | ".$row_order_ex_row;
                    $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);

                    $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                }
			}else{//simpan data baru

		        $ro  = $this->m_productionOrder->get_row_order_production_order_items($kode)->row_array();
		        $row_order = $ro['row_order']+1;
		        $status  = 'draft';
	            $this->m_productionOrder->save_production_order_items($kode,$kode_produk,$produk,$kode_bom,$tgl,$qty,$uom,$lebar_greige,$uom_lebar_greige,$lebar_jadi,$uom_lebar_jadi,$reff,$status,$row_order);
	           
	            $cek_details = $this->m_productionOrder->cek_status_production_order_items($kode,'')->num_rows(); 

                $where_status       = "AND status NOT IN ('generated')";
                $cek_details_status = $this->m_productionOrder->cek_status_production_order_items($kode,$where_status)->num_rows();
	        	
				if($cek_details == 0  ){
	        		$this->m_productionOrder->update_status_production_order($kode,'draft');
	        	}else if($cek_details > 0){
	        		if($cek_details_status == 0){
	        			$this->m_productionOrder->update_status_production_order($kode,'done');
	        		}else{
	        			$this->m_productionOrder->update_status_production_order($kode,'draft');
	        		}	
	        	}

                //get nama_bom 
                $bm = $this->m_productionOrder->get_nama_bom_by_kode_bom($kode_bom)->row_array();
                $nama_bom  = $bm['nama_bom'];
				
	            $jenis_log   = "edit";
                $note_log    = "Tambah data Details | ".$kode." | ".$produk." | ".$kode_bom." | ".$nama_bom."| ".$tgl." | ".$qty." | ".$uom." | ".$lebar_greige." ".$uom_lebar_greige." | ".$lebar_jadi." ".$uom_lebar_jadi." | ".$reff;
                $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                $callback = array('status' => 'success','message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                
			}


	        echo json_encode($callback);
        }
    }


    public function hapus_production_order_items()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
        	$sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username')); 

        	$kode      = $this->input->post('kode');
        	$row       = $this->input->post('row_order');
            $data      = explode("^|",$row);
        	$row_order = $data[0];
        	$kode_produk = addslashes($data[1]);

            // get data procurement order by row
            $get = $this->m_productionOrder->get_data_production_order_items_by_kode($kode,addslashes($kode_produk),$row_order)->row_array();

        	$nama_produk = addslashes($get['nama_produk']);
        	$qty         = $get['qty'];
        	$uom         = addslashes($get['uom']);
        	$schedule_date = $get['schedule_date'];


            $cek_status = $this->m_productionOrder->cek_status_production_order_items_by_row($kode,$kode_produk,$row_order)->row_array(); 
        	if(empty($kode) && empty($row) ){
          		$callback = array('status' => 'success','message' => 'Data Gagal Dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');
        	
            }else if(empty($cek_status['kode_produk'])){
                $callback = array('status' => 'failed','message' => 'Maaf, Produk Kosong  atau sudah dihapus !', 'icon' =>'fa fa-check', 'type' => 'danger');

            }else if($cek_status['status'] == 'generated'){
                $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Dihapus, Status Product Sudah Generated !', 'icon' =>'fa fa-check', 'type' => 'danger');

            }else{ 
        		$this->m_productionOrder->delete_production_order_items($kode,$row_order);
                $callback = array('status' => 'success','message' => 'Data Berhasil Dihapus !', 'icon' =>'fa fa-check', 'type' => 'success');

                $cek_details = $this->m_productionOrder->cek_status_production_order_items($kode,'')->num_rows(); 

                $where_status = "AND status NOT IN ('generated') ";
                $cek_details_status = $this->m_productionOrder->cek_status_production_order_items($kode,$where_status)->num_rows();

                $where_status2       = "AND status NOT IN ('generated','cancel') ";
                $cek_details_status2 = $this->m_productionOrder->cek_status_production_order_items($kode,$where_status2)->num_rows();

				if($cek_details == 0  ){
	        		$this->m_productionOrder->update_status_production_order($kode,'draft');
	        	}else if($cek_details > 0){
	        		if($cek_details_status == 0){
	        			$this->m_productionOrder->update_status_production_order($kode,'done');
	        		}else if($cek_details_status2 == 0){
	        			$this->m_productionOrder->update_status_production_order($kode,'cancel');
	        		}else{
                        $this->m_productionOrder->update_status_production_order($kode,'draft');
                    }
	        	}
                
                $jenis_log   = "cancel";
                $note_log    = "Hapus data Details | ".$kode." | ".$schedule_date." | ".$kode_produk." | ".$nama_produk." | ".$qty."|".$uom." | ".$row_order;
                $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                
        	}

	        echo json_encode($callback);
        }
    }


    public function generate_detail_production_order()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
        	$sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username'));
            $nu        = $this->_module->get_nama_user($username)->row_array(); 
            $nama_user = addslashes($nu['nama']);

        	$kode = $this->input->post('kode');
        	$row  = $this->input->post('row_order');
        	$data = explode("^|",$row);
        	$row_order   = $data[0];
        	$kode_produk = ($data[1]);

            // get data procurement order by row(
            $get = $this->m_productionOrder->get_data_production_order_items_by_kode($kode,addslashes($kode_produk),$row_order)->row_array();
            
        	$nama_produk   = $get['nama_produk'];//ex.. J-5P143SR-126" (Inspecting)
        	$prod_exp      = explode('"',$nama_produk);
        	$nama_produk2  = ($prod_exp[0]); //ex J-5P143SR-126
        	$qty           = $get['qty'];
        	$uom           = $get['uom'];
        	$reff_notes    = $get['reff_notes'];
        	$schedule_date = $get['schedule_date'];
        	$sales_order   = $get['sales_order'];
        	$warehouse     = $get['warehouse'];
            $kode_bom_set  = $get['kode_bom'];
            $lebar_greige  = $get['lebar_greige'];
            $uom_lebar_greige = $get['uom_lebar_greige'];
            $lebar_jadi    = $get['lebar_jadi'];
            $uom_lebar_jadi= $get['uom_lebar_jadi'];
        	$status        = "generated"; 
        	

            $sm_row           = 1;
            $source_move      = "";
	        $sql_stock_move_batch        = "";
	        $sql_stock_move_produk_batch = "";
	        $sql_out_batch       = "";
	        $sql_out_items_batch = "";
	        $sql_in_batch        = "";
	        $sql_in_items_batch  = "";
	        $sql_mrp_prod_batch  = "";
	        $sql_mrp_prod_rm_batch="";
	        $sql_mrp_prod_fg_batch="";

            $reff_picking_in  = "";
            $reff_picking_out = "";
            $move_id_rm       = "";
            $move_id_fg       = "";
            $case ="";
            $where="";
            $i           = 1; //set count kode in/out
            $kode_in     = "";
            $kode_out    = "";
            $kode_bom    = "";
            $sql_log_history_mo = "";
            $sql_log_history_in = "";
            $sql_log_history_out= "";
            $arr_kode           = [];

            $cek_status = $this->m_productionOrder->cek_status_production_order_items_by_row($kode,addslashes($kode_produk),$row_order)->row_array(); 

            if($cek_status['status'] == 'generated'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Product Sudah Generated !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if(empty($cek_status['kode_produk'])){
                $callback = array('status' => 'failed','message' => 'Maaf, Data yang akan Di Generate Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                
            }else if($qty == 0){
                $callback = array('status' => 'failed','message' => 'Maaf, Qty tidak boleh 0 !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else{           

                //lock table
                $this->_module->lock_tabel('mst_produk WRITE, mst_produk mp WRITE, mrp_route WRITE, mrp_route as mr WRITE, departemen WRITE, departemen as d WRITE,  stock_move WRITE, stock_move_produk WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, mrp_production WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, bom WRITE, bom_items bi WRITE, bom_items  WRITE, production_order WRITE, production_order_items WRITE,  log_history WRITE,user WRITE,main_menu_sub WRITE');

                /*--Get ROUTE produk by kode_produk--*/
            	$jen_route  = $this->_module->get_jenis_route_product(addslashes($kode_produk))->row_array();

                $stat_produk = $this->_module->get_status_aktif_by_produk(addslashes($kode_produk))->row_array();// status produk aktif/tidak
                
                $produk_empty       = FALSE;
                $bom_empty          = FALSE;
                $generate_produk    = FALSE;
                $produk_tidak_aktif = FALSE;
                $nama_produk_tidak_aktif = '';
                $produk_bom_tidak_aktif  = FALSE;
                $nama_produk_arr_bi     = '';
                $produk_bom_item_tidak_aktif = FALSE;
                $nama_produk_arr_bi2    = '';
                $nama_bom           = '';
                $nama_produk_empty  = '';

            	if(empty($jen_route['route_produksi'])){//cek route produksi apakah ada ?

            		$callback = array('status' => 'success','message' => 'Maaf, Route Produksi Prduk Kosong', 'icon' =>'fa fa-warning', 'type' => 'danger');
      				
      				//unlock table
    	            $this->_module->unlock_tabel();
            	}else if($stat_produk['status_produk']!= 't'){
                    $callback = array('status' => 'success','message' => 'Maaf, Status Produk tidak aktif', 'icon' =>'fa fa-warning', 'type' => 'danger');
      				
                    //unlock table
                    $this->_module->unlock_tabel();
                }else{

    	        	$last_move   = $this->_module->get_kode_stock_move();
    	            $move_id     = "SM".$last_move; //Set kode stock_move

    	            $last_mo     = $this->_module->get_kode_mo();
    	            $dgt         = substr("00000" . $last_mo,-5);            
    	            $kode_mo     = "MO".date("y") .  date("m"). $dgt;

    	            $route_prod = $this->_module->get_route_product($jen_route['route_produksi']);

    	            //get total leadtime by route
    	            $total_ld = $this->_module->get_total_leadtime($jen_route['route_produksi']);
    	            $leadtime = $total_ld;
    	            $leadtime_dept =  $leadtime;
                    $loop     = 1;
    	            foreach ($route_prod as $rp) {

    	                //get semua product
    	                $tgl           = date('Y-m-d H:i:s');
    	                $mthd          = explode('|',$rp->method);
    	                $method_dept   = trim($mthd[0]);
    	                $method_action = trim($mthd[1]);
                        $dept_id_dari  = $rp->dept_id_dari;

    	                $nama_dept        = $this->_module->get_nama_dept_by_kode($method_dept)->row_array();
    	                $product_dept     = ($nama_dept['nama']);
    	                $product_fullname = ($nama_produk2.'" ('.$product_dept.")");
    	                 
    	                $cek_prod2 = $this->_module->cek_nama_product(addslashes($product_fullname))->row_array();//get kode_produk

    	                if(!empty($cek_prod2['nama_produk'])){
                            if($cek_prod2['status_produk'] == 't'){

                                $kode_prod = addslashes($cek_prod2['kode_produk']);

                                if(stripos($product_fullname, "Jacquard") !== FALSE OR stripos($product_fullname, "Tricot") !== FALSE ){

                                    //cek bom by kode_bom
                                    $cek_bom_set = $this->_module->cek_bom_by_kode_bom($kode_bom_set)->row_array();
                                    if(!empty($cek_bom_set['kode_bom'])){
                                        $qty_bom_set = $cek_bom_set['qty'];

                                        $bi = $this->_module->get_bom_items_by_kode($cek_bom_set['kode_bom'],$qty_bom_set,$qty);
                                        $arr_bi = $bi->result_array();

                                        $bi2 = $this->_module->get_bom_items_all_by_kode($cek_bom_set['kode_bom'],$qty_bom_set,$qty);
                                        $arr_bi2 = $bi2->result_array();

                                    
                                        if(empty($arr_bi) or empty($arr_bi2)){
                                            $bom_empty = TRUE;
                                        }  

                                    }else{
                                        // cek bom = 1 atau 0
                                        // cek apa produk harus ada bom atau tidak ?
                                        $bom_required  = $this->_module->cek_required_bom_by_kode_produk($kode_prod)->row_array();
                                    
                                        if($bom_required['bom'] == 1){ // cek jika bom = 1 atau harus ada bom
                                            $bom_empty = TRUE;
                                        }
                                    }

                                }else{

                                    //-> start untuk bom yg bukan dalam kurung jacquard/tricot
                                    /*
                                    cek bom berdasarkan kode_prod
                                    jika ada
                                        ambil produk di bom items untuk dijadikan kode_prod_rm
                                    */ 
                                    $cek_bom = $this->_module->cek_bom($kode_prod)->row_array();
                                    if(!empty($cek_bom['kode_bom'])){
                                        $qty_bom = $cek_bom['qty'];

                                        $bi = $this->_module->get_bom_items_by_kode($cek_bom['kode_bom'],$qty_bom,$qty);
                                        $arr_bi = $bi->result_array();

                                        $bi2 = $this->_module->get_bom_items_all_by_kode($cek_bom['kode_bom'],$qty_bom,$qty);
                                        $arr_bi2 = $bi2->result_array();

                                        if(empty($arr_bi) or empty($arr_bi2)){
                                            $bom_empty = TRUE;
                                        }  
                                                                
                                    }else{
                                        // cek bom = 1 atau 0
                                        // cek apa produk harus ada bom atau tidak ?
                                        $bom_required  = $this->_module->cek_required_bom_by_kode_produk($kode_prod)->row_array();
                                        
                                        if($bom_required['bom'] == 1){ // cek jika bom = 1 atau harus ada bom
                                            $bom_empty = TRUE;
                                        }
                                    }
                                }

                                // end --<

                                $kode_prod_rm = $kode_prod;
                                $nama_prod_rm = $product_fullname;

                                if(!empty($arr_bi) ){

                                    foreach($arr_bi as $arr_bis){ // cek apakah terdapat produk yang tidak aktif
                                        $stat_produk_bi = $this->_module->get_status_aktif_by_produk(addslashes($arr_bis['kode_produk']))->row_array();
                                        if($stat_produk_bi['status_produk'] != 't'){
                                            //$produk_bom_tidak_aktif = TRUE;
                                            $nama_produk_arr_bi    .= $arr_bis['nama_produk'].', ';
                                        }
                                    }
                                }

                                if(!empty($arr_bi2)){

                                    foreach($arr_bi2 as $arr_bi2s){ // cek apakah terdapat produk yang tidak aktif
                                        $stat_produk_bi2 = $this->_module->get_status_aktif_by_produk(addslashes($arr_bi2s['kode_produk']))->row_array();
                                        if($stat_produk_bi2['status_produk'] != 't'){
                                            $produk_bom_item_tidak_aktif = TRUE;
                                            $nama_produk_arr_bi2    .= $arr_bi2s['nama_produk'].', ';
                                        }
                                    }
                                }


                            }else{
                                $produk_tidak_aktif = TRUE;
                            }

    	                }else{
                            $produk_empty        = TRUE;
                            $generate_produk     = FALSE;
                            $nama_produk_empty  .= $product_fullname.', ';
                            break;
                        }

                        // jika produk tidak aktif 
                        if($produk_tidak_aktif == TRUE){
                            $nama_produk_tidak_aktif .= $product_fullname.', ';
                            break;
                        }

                        // jika bom nya kosong 
                        if($bom_empty == TRUE){
                            $generate_produk  = FALSE;
                            $nama_bom        .= $nama_prod_rm.', ';
                            break;
                        }

                        // jika produk bom / bom items  tidak aktif
                        if($produk_bom_tidak_aktif == TRUE || $produk_bom_item_tidak_aktif == TRUE){
                            break;
                        }


                        //jalankan jika produk dan bom nya ada
                        if($produk_empty == FALSE AND $bom_empty == FALSE AND $produk_tidak_aktif == FALSE AND $produk_bom_tidak_aktif == FALSE AND $produk_bom_item_tidak_aktif == FALSE){

                        $generate_produk = TRUE;
    	           
    	                /*----------------------------------
    	                    Generate Stock Moves
    	                ----------------------------------*/

    	                $origin = $sales_order.'|'.$kode.'|'.$row_order; 
    	                $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$rp->method."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."','draft','".$sm_row."','".$source_move."'), ";
    	                
    	                $sm_row = $sm_row + 1;

    	                if($method_action == 'OUT'){//Generate Pengiriman
    	          
    	                  if($i=="1"){
    	                    $arr_kode[$rp->method]= $this->_module->get_kode_pengiriman($method_dept);
    	                  }else{
    	                    $arr_kode[$rp->method]= $arr_kode[$rp->method] + 1;
    	                  }
    	                  $dgt=substr("00000" . $arr_kode[$rp->method],-5);            
    	                  $kode_out = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;

    	                  $tgl_jt  =  date('Y-m-d H:i:s', strtotime(-$leadtime_dept.' days', strtotime($schedule_date)));
    	                    
    	                  $sql_out_batch  .= "('".$kode_out."','".$tgl."','".$tgl."','".$tgl_jt."','".addslashes($reff_notes)."','draft','".$method_dept."','".$origin."','".$move_id."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."'), ";
    	                  $sql_out_items_batch .= "('".$kode_out."','".addslashes($kode_prod_rm)."','".addslashes($nama_prod_rm)."','".$qty."','".addslashes($uom)."','draft','1',''), ";
    	                
    	                  //simpan ke stock move produk 
    	                  $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($kode_prod_rm)."','".addslashes($nama_prod_rm)."','".$qty."','".addslashes($uom)."','draft','1',''), ";

    	                  $source_move = $move_id;

                          //get mms kode berdasarkan dept_id
                          $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang',$method_dept)->row_array();
                          if(!empty($mms['kode'])){
                            $mms_kode = $mms['kode'];
                          }else{
                            $mms_kode = '';
                          }

                          //create log history pengiriman_barang
                          $note_log = $kode_out.' | '.$origin;
                          $date_log = date('Y-m-d H:i:s');
                          $sql_log_history_out .= "('".$date_log."','".$mms_kode."','".$kode_out."','create','".$note_log."','".$nama_user."'), ";
    	                 
    	                }elseif($method_action == 'IN'){//Generete Penerimaan
    	            
    	                  if($i=="1"){
    	                    $arr_kode[$rp->method]= $this->_module->get_kode_penerimaan($method_dept);
    	                  }else{
    	                    $arr_kode[$rp->method]= $arr_kode[$rp->method] + 1;
    	                  }
    	                  $dgt     =substr("00000" . $arr_kode[$rp->method],-5);            
    	                  $kode_in = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;

    	                  $tgl_jt  =  date('Y-m-d H:i:s', strtotime(-$leadtime_dept.' days', strtotime($schedule_date)));

    	                  $reff_picking_in = $kode_out."|".$kode_in;
    	                  $sql_in_batch   .= "('".$kode_in."','".$tgl."','".$tgl."','".$tgl_jt."','".addslashes($reff_notes)."','draft','".$method_dept."','".$origin."','".$move_id."','".$reff_picking_in."','".$rp->lokasi_dari."','".$rp->lokasi_tujuan."'), "; 

    	                  $in_row=1;
    	                  foreach ($arr_bi as $in) {
    	                  	$sql_in_items_batch   .= "('".$kode_in."','".addslashes($in['kode_produk'])."','".addslashes($in['nama_produk'])."','".$in['qty_bom_items']."','".addslashes($in['uom'])."','draft','".$in_row."'), ";

    	                  	//simpan ke stock move produk 
    	                  	$sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($in['kode_produk'])."','".addslashes($in['nama_produk'])."','".$in['qty_bom_items']."','".addslashes($in['uom'])."','draft','".$in_row."',''), ";
    	                  	$in_row = $in_row + 1; 
    	                  }

    	                  $reff_picking_out = $kode_out."|".$kode_in;
    	                  $case  .= "when kode = '".$kode_out."' then '".$reff_picking_out."'";
    	                  $where .= "'".$kode_out."',";

    	                  $kode_out    = "";
    	                  $source_move = $move_id;

                           //get mms kode berdasarkan dept_id
                          $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                          if(!empty($mms['kode'])){
                            $mms_kode = $mms['kode'];
                          }else{
                            $mms_kode = '';
                          }

                          //create log history penerimaan_barang
                          $note_log = $kode_in.' | '.$origin;
                          $date_log = date('Y-m-d H:i:s');
                          $sql_log_history_in .= "('".$date_log."','".$mms_kode."','".$kode_in."','create','".$note_log."','".$nama_user."'), ";

    	                }elseif($method_action == 'CON'){
    	                  $source_move = "";
    	                 
    	                  //get move id rm target
    	                  $move_id_rm = $move_id;
    	                  $kode_prod_rm_target = $kode_prod_rm;
    	                  $nama_prod_rm_target = $nama_prod_rm;
    	                  $qty_rm_target       = $qty;
    	                  $uom_rm_target       = $uom;

    	                  $con_row = 1;
    	                  foreach ($arr_bi2 as $con) {
    	                    //simpan ke stock move produk 
                            $origin_prod = $con['kode_produk'].'_'.$con_row;
    	              		$sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($con['kode_produk'])."','".addslashes($con['nama_produk'])."','".addslashes($con['qty_bom_items'])."','".addslashes($con['uom'])."','draft','".$con_row."','".addslashes($origin_prod)."'), ";
    	              		$con_row = $con_row  + 1;
    	                  }

    	                }elseif($method_action == 'PROD'){// generate mo/mg
    	                  $source_move = $move_id;

    	                  /*----------------------------------
    	                      Generate MO / MG
    	                  ----------------------------------*/

    	                  $move_id_fg = $move_id;
    	                  $kode_prod_fg_target = $kode_prod_rm;
    	                  $nama_prod_fg_target = $nama_prod_rm;
    	                  $qty_fg_target       = $qty;
    	                  $uom_fg_target       = $uom;

                          if($loop == 2){ // jika  loop ke 2 saat PROD maka kode_bom nya ambil yang sesuai dengan PD
                              $kode_bom = $kode_bom_set;
                          }else{
        	                  $cek_bom = $this->_module->cek_bom($kode_prod_rm)->row_array();
        	                  $kode_bom = $cek_bom['kode_bom'];
                          }


    	                  $ld_dept = $this->_module->get_leadtime_by_dept($method_dept)->row_array();
    	                  $leadtime = $leadtime - $ld_dept['manf_leadtime'];
    	                  $leadtime_dept =  $leadtime;

    	                  $tgl_jt  =  date('Y-m-d H:i:s', strtotime(-$leadtime_dept.' days', strtotime($schedule_date)));

    	                  //$source_location = $method_dept."/Stock";
                          $loc      = $this->_module->get_nama_dept_by_kode($method_dept)->row_array();
                          $location = $loc['stock_location'];
    	                  //sql simpan mrp_production
    	                  $sql_mrp_prod_batch .= "('".$kode_mo."','".$tgl."','".$origin."','".addslashes($kode_prod_rm)."','".addslashes($nama_prod_rm)."','".$qty."','".addslashes($uom)."','".$tgl_jt."','".addslashes($reff_notes)."','".$kode_bom."','".$tgl."','".$tgl."','".$location."','".$location."','".$method_dept."','draft','','".$nama_user."','".addslashes($lebar_greige)."','".addslashes($uom_lebar_greige)."','".addslashes($lebar_jadi)."','".addslashes($uom_lebar_jadi)."'), ";

                           //get mms kode berdasarkan dept_id
                          $mms = $this->_module->get_kode_sub_menu_deptid('mO',$method_dept)->row_array();
                          if(!empty($mms['kode'])){
                            $mms_kode = $mms['kode'];
                          }else{
                            $mms_kode = '';
                          }
                          
                          //create log history MO
                          $note_log = $kode_mo.' | '.$nama_prod_rm.' | '.$qty.' '.$uom;
                          $date_log = date('Y-m-d H:i:s');
                          $sql_log_history_mo .= "('".$date_log."','".$mms_kode."','".$kode_mo."','create','".addslashes($note_log)."','".$nama_user."'), ";

    	 				  $rm_row = 1;
    	                  foreach ($arr_bi2 as $rm) {
    	                    //sql simpan mrp production rm target
                            $origin_prod = $rm['kode_produk'].'_'.$rm_row;
    	                    $sql_mrp_prod_rm_batch .= "('".$kode_mo."','".$move_id_rm."','".addslashes($rm['kode_produk'])."','".addslashes($rm['nama_produk'])."','".$rm['qty_bom_items']."','".addslashes($rm['uom'])."','".$rm_row."','".addslashes($origin_prod)."','draft','".addslashes($rm['note'])."'), "; 
    	              		$rm_row = $rm_row  + 1;
    	                  }

    	                  //sql simpan mrp production fg target
    	                  $sql_mrp_prod_fg_batch .= "('".$kode_mo."','".$move_id_fg."','".addslashes($kode_prod_fg_target)."','".addslashes($nama_prod_fg_target)."','".$qty_fg_target."','".addslashes($uom_fg_target)."','1','draft'), "; 

                          //sql simpan stock move produk
                          $sql_stock_move_produk_batch .= "('".$move_id_fg."','".addslashes($kode_prod_fg_target)."','".addslashes($nama_prod_fg_target)."','".$qty_fg_target."','".addslashes($uom_fg_target)."','draft','1',''), ";
    	                  
    	                 // $last_bom  = $last_bom + 1;
    	                  $last_mo   = $last_mo + 1;    	                 
    	                }
    	                
    	                $dgt       = substr("00000" . $last_mo,-5);            
    	                $kode_mo   = "MO".date("y") .  date("m"). $dgt;

    	                $last_move = $last_move + 1;
    	                $move_id   = "SM".$last_move;
                        
                        }//end if produk dan bom nya ada	                

                        $loop++;

                        $arr_bi  = array();
                        $arr_bi2 = array();

    	            }//end route product


                    //jika GENERATE produk TRUE 
                    if($generate_produk == TRUE){
    	            
    	            if(!empty($sql_stock_move_batch)){
    	              $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
    	              $this->_module->create_stock_move_batch($sql_stock_move_batch);

    	              $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
    	              $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
    	            }

    	            if(!empty($sql_out_batch)){
    	              $sql_out_batch = rtrim($sql_out_batch, ', ');
    	              $this->_module->simpan_pengiriman_batch($sql_out_batch);

    	              $sql_out_items_batch = rtrim($sql_out_items_batch, ', ');
    	              $this->_module->simpan_pengiriman_items_batch($sql_out_items_batch);

                      $sql_log_history_out = rtrim($sql_log_history_out, ', ');
                      $this->_module->simpan_log_history_batch($sql_log_history_out);
    	            }

    	            if(!empty($sql_in_batch)){
    	              $sql_in_batch = rtrim($sql_in_batch, ', ');
    	              $this->_module->simpan_penerimaan_batch($sql_in_batch);

    	              $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
    	              $this->_module->simpan_penerimaan_items_batch($sql_in_items_batch);

    	              $where = rtrim($where, ',');
    	              $sql_update_reff_out_batch  = "UPDATE pengiriman_barang SET reff_picking =(case ".$case." end) WHERE  kode in (".$where.") ";
    	              $this->_module->update_reff_batch($sql_update_reff_out_batch);

                      $sql_log_history_in = rtrim($sql_log_history_in, ', ');
                      $this->_module->simpan_log_history_batch($sql_log_history_in);
    	            }

    	            if(!empty($sql_mrp_prod_batch)){
    	              $sql_mrp_prod_batch = rtrim($sql_mrp_prod_batch, ', ');
    	              $this->_module->simpan_mrp_production_batch($sql_mrp_prod_batch);

    	              $sql_mrp_prod_rm_batch = rtrim($sql_mrp_prod_rm_batch, ', ');
    	              $this->_module->simpan_mrp_production_rm_target_batch($sql_mrp_prod_rm_batch);

    	              $sql_mrp_prod_fg_batch = rtrim($sql_mrp_prod_fg_batch, ', ');
    	              $this->_module->simpan_mrp_production_fg_target_batch($sql_mrp_prod_fg_batch);

                      $sql_log_history_mo = rtrim($sql_log_history_mo, ', ');
                      $this->_module->simpan_log_history_batch($sql_log_history_mo);
    	            }

    	            	//Start Method IN baru setelah route produksi di atas

    	               	$sql_stock_move_batch        = "";
    			        $sql_stock_move_produk_batch = "";			     
    			        $sql_in_batch        = "";
    			        $sql_in_items_batch  = "";
                        $where         = '';
                        $case          = '';
                        $sql_log_history_in = "";

    	            	$last_move   = $this->_module->get_kode_stock_move();
    	            	$move_id     = "SM".$last_move; //Set kode stock_move

    	            	$method_dept = $warehouse;//WRD
    	            	$nama_dept        = $this->_module->get_nama_dept_by_kode($method_dept)->row_array();
    	            	$product_fullname = ($nama_produk);
    	                $cek_prod2 = $this->_module->cek_nama_product(addslashes($product_fullname))->row_array();//get kode_produk

    	                if(!empty($cek_prod2['nama_produk'])){
    	                   $kode_produk = ($cek_prod2['kode_produk']);	            	                   
    	                   $kode_prod_rm = ($kode_prod);
    	                   $nama_prod_rm = ($product_fullname); 
    	                }

    	                /*----------------------------------
    	                    Generate Stock Moves
    	                ----------------------------------*/
    	                $method_action = 'IN';
    	                $method        = $warehouse.'|'.$method_action;
    	                //$lokasi_dari   = 'Transit Location';
    	                //$lokasi_tujuan = $warehouse.'/Stock';
    	                $method_dept   = $warehouse; 

                        $output_location = $this->_module->get_output_location_by_kode($dept_id_dari)->row_array();
                        $lokasi_dari   = $output_location['output_location'];// ex : Transit Location GRG
                        $stock_location = $this->_module->get_nama_dept_by_kode($warehouse)->row_array(); // ex : warehouse/stock
    	                $lokasi_tujuan = $stock_location['stock_location'];


    	                $origin = $sales_order.'|'.$kode.'|'.$row_order; 

    	                $sql_stock_move_batch .= "('".$move_id."','".$tgl."','".$origin."','".$method."','".$lokasi_dari."','".$lokasi_tujuan."','draft','".$sm_row."','".$source_move."'), ";	                
    	             	
    					  if($i=="1"){
    	                    $arr_kode[$rp->method]= $this->_module->get_kode_penerimaan($method_dept);
    	                  }else{
    	                    $arr_kode[$rp->method]= $arr_kode[$rp->method] + 1;
    	                  }
    	                  $dgt     =substr("00000" . $arr_kode[$rp->method],-5);            
    	                  $kode_in = $method_dept."/".$method_action."/".date("y").  date("m").$dgt;

                          $ld_dept = $this->_module->get_leadtime_by_dept($method_dept)->row_array();
                          $leadtime_dept = $ld_dept['manf_leadtime'];

    	                  $tgl_jt  =  date('Y-m-d H:i:s', strtotime(-$leadtime_dept.' days', strtotime($schedule_date)));

                          if(empty($kode_out)){// jika terdapat route out nya ga ada maka In terakhir di reff_picking ditambahkan dept_id departemen sebelumnya (MO) contoh route Tricot
                            $kode_out_asli = $dept_id_dari;
                          }else{
                            $kode_out_asli = $kode_out;
                          }

    	                  $reff_picking_in = $kode_out_asli."|".$kode_in;
    	                  $sql_in_batch   .= "('".$kode_in."','".$tgl."','".$tgl."','".$tgl_jt."','".addslashes($reff_notes)."','draft','".$method_dept."','".$origin."','".$move_id."','".$reff_picking_in."','".$lokasi_dari."','".$lokasi_tujuan."'), "; 

    	                  $in_row=1;
    	                  $sql_in_items_batch   .= "('".$kode_in."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','".$qty."','".addslashes($uom)."','draft','".$in_row."'), ";

                          //get mms kode berdasarkan dept_id
                          $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                          if(!empty($mms['kode'])){
                            $mms_kode = $mms['kode'];
                          }else{
                            $mms_kode = '';
                          }

                          //create log history penerimaan_barang
                          $note_log = $kode_in.' | '.$origin;
                          $date_log = date('Y-m-d H:i:s');
                          $sql_log_history_in .= "('".$date_log."','".$mms_kode."','".$kode_in."','create','".$note_log."','".$nama_user."'), ";

    	                  //simpan ke stock move produk 
    	                  $sql_stock_move_produk_batch .= "('".$move_id."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','".$qty."','".addslashes($uom)."','draft','".$in_row."',''), ";
    	                  $in_row = $in_row + 1; 
    	                

    	                  $reff_picking_out = $kode_out."|".$kode_in;
    	                  $case  .= "when kode = '".$kode_out."' then '".$reff_picking_out."'";
    	                  $where .= "'".$kode_out."',";

    	                if(!empty($sql_stock_move_batch)){
    		              $sql_stock_move_batch = rtrim($sql_stock_move_batch, ', ');
    		              $this->_module->create_stock_move_batch($sql_stock_move_batch);

    		              $sql_stock_move_produk_batch = rtrim($sql_stock_move_produk_batch, ', ');
    		              $this->_module->create_stock_move_produk_batch($sql_stock_move_produk_batch);
    		            }

    		            if(!empty($sql_in_batch)){
    		              $sql_in_batch = rtrim($sql_in_batch, ', ');
    		              $this->_module->simpan_penerimaan_batch($sql_in_batch);

    		              $sql_in_items_batch = rtrim($sql_in_items_batch, ', ');
    		              $this->_module->simpan_penerimaan_items_batch($sql_in_items_batch);

    		              $where = rtrim($where, ',');
    		              $sql_update_reff_out_batch  ="UPDATE pengiriman_barang SET reff_picking =(case ".$case." end) WHERE  kode in (".$where.") ";
    		              $this->_module->update_reff_batch($sql_update_reff_out_batch);

                          $sql_log_history_in = rtrim($sql_log_history_in, ', ');
                          $this->_module->simpan_log_history_batch($sql_log_history_in);
    		            }
    		            //finish method IN baru
    		        
    		        //update detail items jadi generate
    		        $this->m_productionOrder->update_status_production_order_items($kode,$row_order,$status);

       				$cek_details = $this->m_productionOrder->cek_status_production_order_items($kode,'')->num_rows(); 

                    $where_status       = "AND status NOT IN ('generated','cancel')";
                    $cek_details_status = $this->m_productionOrder->cek_status_production_order_items($kode,$where_status)->num_rows();

    	        	if($cek_details == 0  ){
    	        		$this->m_productionOrder->update_status_production_order($kode,'draft');
    	        	}else if($cek_details > 0){
    	        		if($cek_details_status == 0){
    	        			$this->m_productionOrder->update_status_production_order($kode,'done');
    	        		}else{
    	        			$this->m_productionOrder->update_status_production_order($kode,'draft');
    	        		}	
    	        	}

    	            
    	            $jenis_log   = "generated";
    	            $note_log    = "Generated | ".$kode." | ".$nama_produk." | ".$row_order;
    	            $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);            
    					
    	            $callback = array('status' => 'success','message' => 'Generate Data Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success');

                    }// end if cek produk generate

                    if($produk_empty == TRUE OR $bom_empty == TRUE OR $generate_produk == FALSE OR $produk_tidak_aktif == TRUE OR $produk_bom_tidak_aktif == TRUE OR $produk_bom_item_tidak_aktif == TRUE ){
                        if($produk_empty == TRUE){
                            $nama_produk_empty = rtrim($nama_produk_empty, ', ');
                            $callback = array('status' => 'failed','message' => 'Maaf, Produk '.$nama_produk_empty.' Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }else if($produk_tidak_aktif == TRUE){
                            $nama_produk_tidak_aktif  = rtrim($nama_produk_tidak_aktif,', ');
                            $callback = array('status' => 'failed','message' => 'Maaf, Produk '.$nama_produk_tidak_aktif.' Tidak Aktif !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }else if($bom_empty == TRUE){
                            $nama_bom  = rtrim($nama_bom,', ');
                            $callback = array('status' => 'failed','message' => 'Maaf, Bill of Materials (BOM) '.$nama_bom.' Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');

                        }else if($produk_bom_tidak_aktif == TRUE){
                            $nama_produk_arr_bi  = rtrim($nama_produk_arr_bi,', ');
                            $callback = array('status' => 'failed','message' =>'Maaf, Produk BOM '.$nama_produk_arr_bi.' Tidak Aktif !', 'icon' =>'fa fa-warning', 'type' => 'danger');

                        }else if($produk_bom_item_tidak_aktif == TRUE){
                            $nama_produk_arr_bi2  = rtrim($nama_produk_arr_bi2,', ');
                            $callback = array('status' => 'failed','message' => 'Maaf, Produk BOM Items '.$nama_produk_arr_bi2.' Tidak Aktif !', 'icon' =>'fa fa-warning', 'type' => 'danger');

                        }else{
                            $callback = array('status' => 'failed','message' => 'Maaf, Generate Data Gagal !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                        }
                    }

    	            //unlock table
    	            $this->_module->unlock_tabel();

    	        }//else if cek route produksi

            }//else cek status 
            
	    }	
        echo json_encode($callback);
    }

    public function batal_detail_production_order()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username'));
            $nu        = $this->_module->get_nama_user($username)->row_array(); 
            $nama_user = addslashes($nu['nama']);

            $kode   = $this->input->post('kode');
            $row    = $this->input->post('row_order');
            $data   = explode("^|",$row);
            $row_order   = $data[0];
            $kode_produk = ($data[1]);

            // get data procurement order by row
            $get = $this->m_productionOrder->get_data_production_order_items_by_kode($kode,addslashes($kode_produk),$row_order)->row_array();

            $nama_produk = $get['nama_produk'];//ex.. J-5P143SR-126" (Inspecting)
            $sales_order = $get['sales_order'];
            $origin      = $sales_order.'|'.$kode.'|'.$row_order;
            /*
            $prod_exp = explode('"',$nama_produk);
            $nama_produk2= ($prod_exp[0]); //ex J-5P143SR-126
            $qty = $data[3];
            $uom = ($data[4]);
            $reff_notes    = ($data[5]);
            $schedule_date = $data[6];
            $warehouse     = ($data[8]);
            $status = "generated"; 
            */
            $cek_status = $this->m_productionOrder->cek_status_production_order_items_by_row($kode,addslashes($kode_produk),$row_order)->row_array(); 

            if($cek_status['status'] == 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Product Sudah Batal !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else if(empty($cek_status['kode_produk'])){
                $callback = array('status' => 'failed','message' => 'Maaf, Data yang akan Di Batalkan Kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger');
            }else{     
                //lock table
                $this->_module->lock_tabel('production_order WRITE, production_order_items WRITE, stock_move WRITE, stock_move_items WRITE, stock_move_produk WRITE, mrp_production WRITE, mrp_production_rm_hasil WRITE, mrp_production_rm_target WRITE, mrp_production_fg_target WRITE, mrp_production_fg_hasil WRITE, pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE');
                
                    //select stock_move by origin row order move id
                    //$mrp = true;
                    $update_stock_move = false;
                    $batal_item    = false;
                    $status_cancel = "cancel";
                    //mrp_production
                    $case  = "";
                    $where = "";
                    //pengiriman_barang
                    $case2  = "";
                    $where2 = "";
                    //penerimaan_barang
                    $case3  = "";
                    $where3 = "";
                    //stock move, stock_move_items, stock_move_produk
                    $case4  = "";
                    $where4 = "";
                    $date_log = date('Y-m-d H:i:s');
                    $sql_log_history = "";


                    $list_sm    = $this->_module->get_list_stock_move_by_origin($origin);
                    foreach($list_sm as $row){

                        $batal_item = true;

                        $ex_mt = explode('|',$row->method);
                        $method_dept = $ex_mt[0];
                        $method_action  = $ex_mt[1]; //ex CON/PROD/OUT/IN
                        $origin  = $row->origin;
                        $move_id = $row->move_id;

                        if(( $method_action == 'CON' OR $method_action == 'PROD') ){

                            $log_mrp = false;
                            // cek status mrp_production ?
                            $status = "AND status NOT IN ('done','cancel')";
                            $cek_mrp = $this->_module->cek_status_mrp_productin_by_origin($origin,$method_dept,$status)->result_array();
                            foreach($cek_mrp as $mrp){

                                if(!empty($mrp['kode'])){//bearti status MO = ready/draft
        
                                    //update status = cancel mrp_production, mrp_production_rm_target, mrp_production_fg_target
                                    $case  .= "when kode = '".$mrp['kode']."' then '".$status_cancel."'";
                                    $where .= "'".$mrp['kode']."',";

                                    $log_mrp = true;
                                    $update_stock_move = true;
                                    $kode_mrp = $mrp['kode'];
                                }
                            }
                                if($log_mrp == true){

                                    //get mms kode berdasarkan dept_id
                                     $mms = $this->_module->get_kode_sub_menu_deptid('mO',$method_dept)->row_array();
                                    if(!empty($mms['kode'])){
                                       $mms_kode = $mms['kode'];
                                    }else{
                                       $mms_kode = '';
                                    }

                                    // create log history mrp_production
                                    $note_log         = 'Batal MO '.$method_action.' | '.$kode_mrp;
                                    $sql_log_history .= "('".$date_log."','".$mms_kode."','".$kode_mrp."','cancel','".$note_log."','".$nama_user."'), ";
                                }

                        }elseif($method_action == 'OUT'){

                            // cek status pengiriman barang
                            $status  = "AND status NOT IN ('done','cancel')";
                            $cek_out = $this->_module->cek_status_pengiriman_barang_by_move_id($origin,$move_id,$status)->row_array();
                            
                            if(!empty($cek_out['kode'])){//bearti pengiriman_barang = ready/draft

                                //update status = cancel pengiriman_barang, pengiriman_barang_items
                                $case2  .= " when kode = '".$cek_out['kode']."' then '".$status_cancel."'";
                                $where2 .= "'".$cek_out['kode']."',";             

                                //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang',$method_dept)->row_array();
                                if(!empty($mms['kode'])){
                                    $mms_kode = $mms['kode'];
                                }else{
                                   $mms_kode = '';
                                }
                                
                                // create log history pengiriman_barang
                                $note_log         = 'Batal Pengiriman Barang | '.$cek_out['kode'];
                                $sql_log_history .= "('".$date_log."','".$mms_kode."','".$cek_out['kode']."','cancel','".$note_log."','".$nama_user."'), ";

                                $update_stock_move = true;
                            }

                        }elseif($method_action == 'IN'){
                            
                            // cek status penerimaan barang
                            $status  = "AND status NOT IN ('done','cancel')";
                            $cek_in  = $this->_module->cek_status_penerimaan_barang_by_move_id($origin,$move_id,$status)->row_array();
                            
                            if(!empty($cek_in['kode'])){//bearti penerimaan_barang = ready/draft

                                //update status = cancel penerimaan_barang, penerimaan_barang_items
                                $case3  .= " when kode = '".$cek_in['kode']."' then '".$status_cancel."'";
                                $where3 .= "'".$cek_in['kode']."',";  

                                //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                                if(!empty($mms['kode'])){
                                  $mms_kode = $mms['kode'];
                                }else{
                                  $mms_kode = '';
                                }           
                                
                                // create log history penerimaan barang
                                $note_log         = 'Batal Penerimaan Barang | '.$cek_in['kode'];
                                $sql_log_history .= "('".$date_log."','".$mms_kode."','".$cek_in['kode']."','cancel','".$note_log."','".$nama_user."'), ";

                                $update_stock_move = true;
                            }
                        }

                        if($update_stock_move == true){
                                        
                            //update status = cancel stock move, stock_move_items, stock_move_produk
                            $case4  .= " when move_id = '".$move_id."' then '".$status_cancel."'";
                            $where4 .= "'".$move_id."',";
                        }

                        $update_stock_move = false;

                    }// end foreach stock_move

                    if($batal_item == true){

                       //update mrp_production, mrp_production_rm_target, mrp_production_fg_target
                       if(!empty($case) AND !empty($where)){
                            
                            // update mrp_production
                            $where = rtrim($where, ',');
                            $sql_update_mrp_production = "UPDATE mrp_production SET status =(case ".$case." end) WHERE  kode in (".$where.") ";
                            $this->_module->update_reff_batch($sql_update_mrp_production);

                            // update mrp_production_rm_target
                            $sql_update_mrp_production_rm_target = "UPDATE mrp_production_rm_target SET status =(case ".$case." end) WHERE  kode in (".$where.") AND status NOT IN ('done') ";
                            $this->_module->update_reff_batch($sql_update_mrp_production_rm_target);

                            // update mrp_production_fg_target 
                            $sql_update_mrp_production_fg_target = "UPDATE mrp_production_fg_target SET status =(case ".$case." end) WHERE  kode in (".$where.") AND status NOT IN ('done') ";
                            $this->_module->update_reff_batch($sql_update_mrp_production_fg_target);


                       }

                       //update pengiriman_barang, pengiriman_barang_items
                       if(!empty($case2) AND !empty($where2)){
                        
                            //update pengiriman_barang
                            $where2 = rtrim($where2, ',');
                            $sql_update_pengiriman_barang = "UPDATE pengiriman_barang SET status =(case ".$case2." end) WHERE  kode in (".$where2.") ";
                            $this->_module->update_reff_batch($sql_update_pengiriman_barang);

                            // update pengiriman_barang_items
                            $sql_update_pengiriman_barang_items = "UPDATE pengiriman_barang_items SET status_barang =(case ".$case2." end) WHERE  kode in (".$where2.") ";
                            $this->_module->update_reff_batch($sql_update_pengiriman_barang_items);

                        
                       }
                       
                       //update penerimaan_barang, penerimaan_barang_items
                       if(!empty($case3) AND !empty($where3)){

                           //update penerimaan_barang
                            $where3 = rtrim($where3, ',');
                            $sql_update_penerimaan_barang = "UPDATE penerimaan_barang SET status =(case ".$case3." end) WHERE  kode in (".$where3.") ";
                            $this->_module->update_reff_batch($sql_update_penerimaan_barang);

                            // update penerimaan_barang_items
                            $sql_update_penerimaan_barang_items = "UPDATE penerimaan_barang_items SET status_barang =(case ".$case3." end) WHERE  kode in (".$where3.") ";
                            $this->_module->update_reff_batch($sql_update_penerimaan_barang_items);
                            

                       }

                       //update stock move, stock_move_items, stock_move_produk
                       if(!empty($case4) AND !empty($where4)){

                            // update stock_move
                            $where4 = rtrim($where4, ',');
                            $sql_update_stock_move = "UPDATE stock_move SET status =(case ".$case4." end) WHERE  move_id in (".$where4.") ";
                            $this->_module->update_reff_batch($sql_update_stock_move);

                            // update stock_move_items
                            $sql_update_stock_move_items = "UPDATE stock_move_items SET status =(case ".$case4." end) WHERE  move_id in (".$where4.") AND status NOT IN ('done') ";
                            $this->_module->update_reff_batch($sql_update_stock_move_items);

                            // update stock_move_produk
                            $sql_update_stock_move_produk = "UPDATE stock_move_produk SET status =(case ".$case4." end) WHERE  move_id in (".$where4.") AND status NOT IN ('done')  ";
                            $this->_module->update_reff_batch($sql_update_stock_move_produk);
                            

                       }

                        $jenis_log   = "cancel";
                        $note_log    = "Batal Items | ".$kode." | ".$nama_produk." | ".$row_order;
                        $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);

                       //create log history setiap yg batal
                       if(!empty($sql_log_history)){
                          $sql_log_history = rtrim($sql_log_history, ', ');
                          $this->_module->simpan_log_history_batch($sql_log_history);
                       }


                        //update detail items jadi cancel
                        $this->m_productionOrder->update_status_production_order_items($kode,$row_order,$status_cancel);

                        $cek_details = $this->m_productionOrder->cek_status_production_order_items($kode,'')->num_rows(); 

                        $where_status       = "AND status NOT IN ('cancel')";
                        $cek_details_status = $this->m_productionOrder->cek_status_production_order_items($kode,$where_status)->num_rows();

                        $where_status2       = "AND status NOT IN ('generated','cancel')";
                        $cek_details_status2 = $this->m_productionOrder->cek_status_production_order_items($kode,$where_status2)->num_rows();

                        if($cek_details > 0){
                            if($cek_details_status == 0){
                                $this->m_productionOrder->update_status_production_order($kode,'cancel');
                            }else if($cek_details_status2 > 0){
                                $this->m_productionOrder->update_status_production_order($kode,'draft');
                            }  
                        }

                    }//end if batal_items == true

                    if($batal_item == false){
                        $callback = array('status' => 'failed', 'message' => 'Production Order Items Gagal Dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                    }else{
                        $callback = array('status' => 'success', 'message' => 'Production Order Items Berhasil Dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'success');
                    }

                //unlock table
                $this->_module->unlock_tabel();

            }//else 

        }// else session ada

        echo json_encode($callback);
    }


    
    public function batal_waste_production_order()
    {
        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{
            $sub_menu  = $this->uri->segment(2);
            $username  = addslashes($this->session->userdata('username'));
            $nu        = $this->_module->get_nama_user($username)->row_array(); 
            $nama_user = addslashes($nu['nama']);

            $kode      = $this->input->post('kode');
            $kode_produk =  $this->input->post('kode_produk');
            $row_order = $this->input->post('row_order');
            $origin    = $this->input->post('origin');


            $cek_status = $this->m_productionOrder->cek_status_production_order_items_by_row($kode,addslashes($kode_produk),$row_order)->row_array(); 

            if($cek_status['status'] == 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Product Sudah Batal !', 'icon' =>'fa fa-warning', 'type' => 'danger');

            }else{

                // lock tabel
                $this->_module->lock_tabel('pengiriman_barang WRITE, pengiriman_barang_items WRITE, penerimaan_barang WRITE, penerimaan_barang_items WRITE, stock_move WRITE, stock_move_items WRITE, stock_move_produk WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE');

                //$mrp = true;
                $update_stock_move = false;
                $batal_item    = false;
                $status_cancel = "cancel";
                //pengiriman_barang
                $case  = "";
                $where = "";
                //penerimaan_barang
                $case2  = "";
                $where2 = "";
                //stock move, stock_move_items, stock_move_produk
                $case3  = "";
                $where3 = "";
              
                $date_log = date('Y-m-d H:i:s');
                $sql_log_history = "";


                //get list stock_move_by_origin
                $list_sm  = $this->_module->get_list_stock_move_by_origin($origin);
                foreach($list_sm as $row){


                    $ex_mt = explode('|', $row->method);
                    $method_dept   = $ex_mt[0];
                    $method_action = $ex_mt[1]; // CON/PROD/OUT/IN
                    $origin        = $row->origin;
                    $move_id       = $row->move_id;

                    if($method_action == 'OUT'){

                        // cek status pengiriman barang
                        $status  = "AND status NOT IN ('done','cancel')";
                        $cek_out = $this->_module->cek_status_pengiriman_barang_by_move_id($origin,$move_id,$status)->row_array();
                            
                        if(!empty($cek_out['kode'])){

                            // cek qty_target
                            $qty_target = $this->_module->get_qty_target_pengiriman_barang_by_kode($cek_out['kode'])->row_array();

                            //cek qty_tersedia
                            $qty_tersedia = $this->_module->get_qty_tersedia_stock_move_items_by_move_id($move_id)->row_array();

                            if($qty_target['qty_target'] > $qty_tersedia['qty_tersedia']){
                               
                                $batal_item = true;
                                
                                //update status = cancel pengiriman_barang, pengiriman_barang_items
                                $case  .= " when kode = '".$cek_out['kode']."' then '".$status_cancel."'";
                                $where .= "'".$cek_out['kode']."',";  

                                  //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('pengirimanbarang',$method_dept)->row_array();
                                if(!empty($mms['kode'])){
                                   $mms_kode = $mms['kode'];
                                }else{
                                   $mms_kode = '';
                                }           
                                            
                                // create log history pengiriman_barang
                                $note_log         = 'Batal Waste Pengiriman Barang | '.$cek_out['kode'];
                                $sql_log_history .= "('".$date_log."','".$mms_kode."','".$cek_out['kode']."','cancel','".$note_log."','".$nama_user."'), ";

                                $update_stock_move = true;
                            }

                        }

                    }elseif($method_action == 'IN'){


                        // cek status penerimaan barang
                        $status  = "AND status NOT IN ('done','cancel')";
                        $cek_in  = $this->_module->cek_status_penerimaan_barang_by_move_id($origin,$move_id,$status)->row_array();
                            
                        if(!empty($cek_in['kode'])){

                            // cek qty_target
                            $qty_target = $this->_module->get_qty_target_penerimaan_barang_by_kode($cek_in['kode'])->row_array();

                            //cek qty_tersedia
                            $qty_tersedia = $this->_module->get_qty_tersedia_stock_move_items_by_move_id($move_id)->row_array();

                            //$qty_in = $qty_target['qty_target'].' - '.$qty_tersedia['qty_tersedia'];

                            if($qty_target['qty_target'] > $qty_tersedia['qty_tersedia']){
                                
                                $batal_item = true;
                                
                                //update status = cancel penerimaan_barang, penerimaan_barang_items
                                $case2  .= " when kode = '".$cek_in['kode']."' then '".$status_cancel."'";
                                $where2 .= "'".$cek_in['kode']."',";   

                                //get mms kode berdasarkan dept_id
                                $mms = $this->_module->get_kode_sub_menu_deptid('penerimaanbarang',$method_dept)->row_array();
                                if(!empty($mms['kode'])){
                                   $mms_kode = $mms['kode'];
                                }else{
                                   $mms_kode = '';
                                }             
                                    
                                // create log history penerimaan_barang
                                $note_log         = 'Batal Waste Penerimaan Barang | '.$cek_in['kode'];
                                $sql_log_history .= "('".$date_log."','".$mms_kode."','".$cek_in['kode']."','cancel','".$note_log."','".$nama_user."'), ";

                                $update_stock_move = true;
                            }
                        }
                    }


                    if($update_stock_move == true){
                        //update status = cancel stock move, stock_move_items, stock_move_produk
                        $case3  .= " when move_id = '".$move_id."' then '".$status_cancel."'";
                        $where3 .= "'".$move_id."',";

                    }

                    $update_stock_move = false;

                } // end foreach list_sm

                if($batal_item ==  true){

                    //update pengiriman_barang, pengiriman_barang_items
                    if(!empty($case) AND !empty($where)){
                        
                        //update pengiriman_barang
                        $where = rtrim($where, ',');
                        $sql_update_pengiriman_barang = "UPDATE pengiriman_barang SET status =(case ".$case." end) WHERE  kode in (".$where.") ";
                        $this->_module->update_reff_batch($sql_update_pengiriman_barang);

                        // update pengiriman_barang_items
                        $sql_update_pengiriman_barang_items = "UPDATE pengiriman_barang_items SET status_barang =(case ".$case." end) WHERE  kode in (".$where.") ";
                        $this->_module->update_reff_batch($sql_update_pengiriman_barang_items);
                       
                    }
                       
                       
                    //update penerimaan_barang, penerimaan_barang_items
                    if(!empty($case2) AND !empty($where2)){

                        //update penerimaan_barang
                        $where2 = rtrim($where2, ',');
                        $sql_update_penerimaan_barang = "UPDATE penerimaan_barang SET status =(case ".$case2." end) WHERE  kode in (".$where2.") ";
                        $this->_module->update_reff_batch($sql_update_penerimaan_barang);

                        // update penerimaan_barang_items
                        $sql_update_penerimaan_barang_items = "UPDATE penerimaan_barang_items SET status_barang =(case ".$case2." end) WHERE  kode in (".$where2.") ";
                        $this->_module->update_reff_batch($sql_update_penerimaan_barang_items);
                            
                    }

                       
                    //update stock move, stock_move_items, stock_move_produk
                    if(!empty($case3) AND !empty($where3)){

                        // update stock_move
                        $where3 = rtrim($where3, ',');
                        $sql_update_stock_move = "UPDATE stock_move SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") ";
                        $this->_module->update_reff_batch($sql_update_stock_move);

                        // update stock_move_items
                        $sql_update_stock_move_items = "UPDATE stock_move_items SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") ";
                        $this->_module->update_reff_batch($sql_update_stock_move_items);

                        // update stock_move_produk
                        $sql_update_stock_move_produk = "UPDATE stock_move_produk SET status =(case ".$case3." end) WHERE  move_id in (".$where3.") ";
                        $this->_module->update_reff_batch($sql_update_stock_move_produk);
                            

                    }


                    $jenis_log   = "cancel";
                    $note_log    = "Batal Waste Items | ".$kode." | ".$kode_produk." | ".$row_order;
                    $this->_module->gen_history($sub_menu, $kode, $jenis_log, addslashes($note_log), $username);

                    //create log history setiap yg batal
                    if(!empty($sql_log_history)){
                        $sql_log_history = rtrim($sql_log_history, ', ');
                        $this->_module->simpan_log_history_batch($sql_log_history);
                    }

                } // end if $batal_item = true;

                if($batal_item == false){
                    $callback = array('status' => 'failed', 'message' => 'Batal Produk Waste Gagal Dibatalkan !', 'icon' =>'fa fa-warning', 'type' => 'danger');
                }else{
                    $callback = array('status' => 'success', 'message' => 'Batal Produk Waste Berhasil Dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'success');
                }


                //unlock table 
                $this->_module->unlock_tabel();

            }

        }

        echo json_encode($callback);

    }

}
