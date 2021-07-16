<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Transferlokasi extends MY_Controller
{
  public function __construct()
  {
	   parent::__construct();
	   $this->is_loggedin();//cek apakah user sudah login
	   $this->load->model("_module");
	   $this->load->model("m_transferLokasi");
  }


  public function index()
  {
   		$data['id_dept']  ='TL';
   	 	$data['warehouse'] = $this->_module->get_list_departement();
   	 	$this->load->view('warehouse/v_transferlokasi', $data);
  }



   public function get_data()
   {	
   		$sub_menu = $this->uri->segment(2);
   		$id_dept  = 'TL';
        $kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();
        $list = $this->m_transferLokasi->get_datatables($kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
        	$kode_encrypt = encrypt_url($field->kode_tl);
        
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('warehouse/transferlokasi/edit/'.$kode_encrypt).'">'.$field->kode_tl.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->departemen;
            $row[] = $field->lokasi_tujuan;
            $row[] = $field->total_lot_item;
            $row[] = $field->note;
            $row[] = $field->nama_status;
 
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_transferLokasi->count_all($kode['kode']),
            "recordsFiltered" => $this->m_transferLokasi->count_filtered($kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
	}


	public function add()
	{ 
	    $data['id_dept']       = 'TL';
   	 	$data['warehouse']     = $this->_module->get_list_departement();
	    return $this->load->view('warehouse/v_transferlokasi_add', $data);
	}

	public function edit($id = null)
  	{

	    if(!isset($id)) show_404();
	    $kode_decrypt      = decrypt_url($id);
	    $id_dept   		   = 'TL';
	    $data['id_dept']   = $id_dept;
        $data['mms']       = $this->_module->get_data_mms_for_log_history($id_dept);// get mms by dept untuk menu yg beda-beda
	    $data['tl']        = $this->m_transferLokasi->get_transfer_lokasi_by_kode($kode_decrypt);
	    $data['tli']       = $this->m_transferLokasi->get_transfer_lokasi_items_by_kode($kode_decrypt);

	    if(empty($data["tl"])){
            show_404();
        }else{
          	return $this->load->view('warehouse/v_transferlokasi_edit',$data);
        }
  	}


	public function simpan()
  	{

	    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
	        // session habis
	        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
	    }else{
		  	$sub_menu = $this->uri->segment(2);
		    $username = $this->session->userdata('username'); 

	    	$nama_user = $this->_module->get_nama_user($username)->row_array();
	    	$kode_tl  = addslashes($this->input->post('kode'));
	      	$dept_id  = addslashes($this->input->post('departemen'));
	      	$note     = addslashes($this->input->post('note'));
	      	$lokasi_tujuan = addslashes($this->input->post('lokasi_tujuan'));
	      	$tgl     = date('Y-m-d H:i:s');
	      	$status  = 'draft';

	      	// validasi aktif/ tidak lokasi rak/tujuan
	      	$ck  = $this->m_transferLokasi->cek_status_aktif_lokasi_rak_by_kode($dept_id,$lokasi_tujuan)->row_array();

	      	// cek status 
	    	if(!empty($kode_tl)){
	    		$tl  = $this->m_transferLokasi->get_transfer_lokasi_by_kode($kode_tl);
	    		$status_tl = $tl->status;
	    	}else{
	    		$status_tl = '';
	    	}

	    	if($status_tl == 'done' AND $kode_tl != ''){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Transfer Lokasi Sudah Done !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else if($status_tl == 'cancel' AND $kode_tl != ''){
                $callback = array('status' => 'failed','message' => 'Maaf, Transfer Lokasi telah dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'danger');
	      	}else if(empty($dept_id)){
	      		$callback = array('status' => 'failed', 'field' => 'departemen', 'message' => 'Departemen Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      	}else if($ck['status_aktif'] == 'f'){
	      		$callback = array('status' => 'failed', 'field' => 'lokasi_tujuan', 'message' => 'Lokasi Tujuan Sudah Tidak Aktif !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      	}else if(empty($lokasi_tujuan)){
	      		$callback = array('status' => 'failed', 'field' => 'lokasi_tujuan', 'message' => 'Lokasi Tujuan Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      	}else{

	      		 // cek lokasi tujuan apa valid  berdasarkan lokasi departemen
	      		$validLokasiTujuan = $this->m_transferLokasi->valid_lokasi_tujuan_by_dept($dept_id,$lokasi_tujuan);

	      		if(!$validLokasiTujuan){
	      			$callback = array('status' => 'failed', 'field' => 'lokasi_tujuan', 'message' => 'Lokasi Tujuan Tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      		}else{
					if(empty($kode_tl)){
						// create transfer lokasi

						$kode = $this->m_transferLokasi->get_kode_tl();
	          			$last_id_encr = encrypt_url($kode);
						$this->m_transferLokasi->save_transfer_lokasi($kode,$tgl,$note,$dept_id,$lokasi_tujuan,$nama_user['nama'],$status);

						$jenis_log   = "create";
			            $note_log    = $dept_id." | ".$lokasi_tujuan." | ".$note;
			          	$this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
			          	$callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $last_id_encr, 'icon' =>'fa fa-check', 'type' => 'success');

					}else{
						// update transfer lokasi

						// cek apa suda ada item 
						$itemsEmpty = $this->m_transferLokasi->cek_transfer_lokasi_items_by_kode($kode_tl);
						// get lokasi_tujuan 
	    				$tl         = $this->m_transferLokasi->get_transfer_lokasi_by_kode($kode_tl);

						if($itemsEmpty AND ($tl->lokasi_tujuan != $lokasi_tujuan)){
							$callback = array('status' => 'failed', 'field' => 'lokasi_tujuan', 'message' => 'Lokasi Tujuan tidak bisa dirubah !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
						}else{

							$this->m_transferLokasi->update_transfer_lokasi($kode_tl,$lokasi_tujuan,$note);

							$jenis_log   = "edit";
				            $note_log    = $dept_id." | ".$lokasi_tujuan." | ".$note;
				          	$this->_module->gen_history($sub_menu, $kode_tl, $jenis_log, $note_log, $username);
							$callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !',  'icon' =>'fa fa-check', 'type' => 'success');
						}

					}

	      		}

	      
	      	}

		}

	  	echo json_encode($callback);

	}



	public function scan_barcode()
	{
	    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
	        // session habis
	        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
	    }else{
			$sub_menu = $this->uri->segment(2);
		    $username = $this->session->userdata('username'); 

	    	$kode_tl  = addslashes($this->input->post('kode'));
	    	$dept_id  = addslashes($this->input->post('dept_id'));
	      	$lokasi_tujuan = addslashes($this->input->post('lokasi_tujuan'));
	      	$barcode_id    = addslashes($this->input->post('barcode_id'));

	      	// validasi aktif/ tidak lokasi rak/tujuan
	      	$ck  = $this->m_transferLokasi->cek_status_aktif_lokasi_rak_by_kode($dept_id,$lokasi_tujuan)->row_array();

	      	 // cek status 
	    	$tl  = $this->m_transferLokasi->get_transfer_lokasi_by_kode($kode_tl);

	    	if($tl->status == 'done'){
                $callback = array('status' => 'failed', 'field' => 'barcode_id', 'alert' => 'modal', 'message' => 'Maaf, Status Transfer Lokasi Sudah Done !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else if($tl->status == 'cancel'){
                $callback = array('status' => 'failed', 'field' => 'barcode_id', 'alert' => 'modal', 'message' => 'Maaf, Transfer Lokasi telah dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'danger');
	      	}else if(empty($lokasi_tujuan)){
	      		$callback = array('status' => 'failed', 'field' => 'lokasi_tujuan', 'message' => 'Lokasi Tujuan Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      	}else if($ck['status_aktif'] == 'f'){
	      		$callback = array('status' => 'failed', 'field' => 'lokasi_tujuan', 'message' => 'Lokasi Tujuan Sudah Tidak Aktif !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      	}else if(empty($barcode_id)){
	      		$callback = array('status' => 'failed', 'field' => 'barcode_id', 'message' => 'Barcode Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      	}else{
	      		// get location stock by dept
	      		$ld            = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
                $lokasi_stock   = $ld['stock_location'];

	      		// cek lokasi tujuan berdasarkan kode_tl
	      		$validLokasiTujuanBy = $this->m_transferLokasi->is_valid_lokasi_tujuan_by_kode($kode_tl,$lokasi_tujuan);

	      		// cek lokasi tujuan berdasarkan departemen
	      		$validLokasiTujuan = $this->m_transferLokasi->valid_lokasi_tujuan_by_dept($dept_id,$lokasi_tujuan);

	      		// 
	      		$validBarcode = $this->m_transferLokasi->verified_barcode_by_dept($barcode_id);

	      		// cek lokasi barcode by dept
	      		$validBarcodeDepartement = $this->m_transferLokasi->is_valid_lokasi_barcode_by_dept($lokasi_stock,$barcode_id);

	      		if(!$validLokasiTujuanBy){
	      			$callback = array('status' => 'failed', 'field' => 'lokasi_tujuan', 'message' => 'Lokasi Tujuan Tidak sama !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      		}else if(!$validLokasiTujuan){
	      			$callback = array('status' => 'failed', 'field' => 'lokasi_tujuan', 'message' => 'Lokasi Tujuan Tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      		}else if(!$validBarcode){
	      			$callback = array('status' => 'failed', 'field' => 'barcode_id', 'message' => 'Barcode tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      		}else if(!$validBarcodeDepartement){
	      			$callback = array('status' => 'failed', 'field' => 'barcode_id', 'message' => 'Lokasi Barcode tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 

	      		}else{

	      			// cek duplicate barcode
	      			$cb = $this->m_transferLokasi->cek_transfer_lokasi_items_by_barcode($kode_tl,$barcode_id)->row_array();

	      			if(!empty($cb['kode_tl'])){
	      				$callback = array('status' => 'failed', 'field' => 'barcode_id', 'message' => 'Barcode Id '.$barcode_id.' Sudah diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
	      			}else{

		      			//get last row_order
		      			$ro = $this->m_transferLokasi->get_row_order_transfer_lokasi_item_by_kode($kode_tl);
		      			$count = 0;
		      			// get barcode di stock_quant
		      			$items = $this->m_transferLokasi->get_list_stock_quant_by_kode($lokasi_stock,$barcode_id);
		      			foreach ($items as $val) {
		      				# code...
		      				$quant_id     = addslashes($val->quant_id);
		      				$kode_produk  = addslashes($val->kode_produk);
		      				$nama_produk  = addslashes($val->nama_produk);
		      				$lokasi_asal  = addslashes($val->lokasi_rak);
		      				$lot          = addslashes($val->lot);
		      				$qty          = addslashes($val->qty);
		      				$uom          = addslashes($val->uom);

		      				//insert into transfer_lokasi_items
		      				$this->m_transferLokasi->save_transfer_lokasi_items($kode_tl,$quant_id,$kode_produk,$nama_produk,$lokasi_asal,$lot,$qty,$uom,$ro);
		      				$ro++;
		      				$count++;
		      			}

		      			// cek transfer_lokasi_items
			   			$tli       = $this->m_transferLokasi->cek_transfer_lokasi_items_by_kode($kode_tl);
			   			if($tli){
							// update status transfer lokasi
							$status = 'ready';
							$this->m_transferLokasi->update_status_transfer_lokasi($kode_tl,$status);	   				
			   			}

		      			$jenis_log   = "edit";
					    $note_log    = "Scan Barcode ". $barcode_id." | jml = ".$count;
					    $this->_module->gen_history($sub_menu, $kode_tl, $jenis_log, $note_log, $username);

		      			$callback = array('status' => 'success', 'message' => 'Scan Barcode Berhasil', 'icon' =>'fa fa-check', 'type' => 'success'  ); 

	      			}

	      		}

	      	}


	    }

	    echo json_encode($callback);
	}


	public function hapus_items_barcode()
	{
		
		if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
	        // session habis
	        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
	    }else{

	    	$sub_menu = $this->uri->segment(2);
		    $username = $this->session->userdata('username'); 

	    	$kode_tl  = addslashes($this->input->post('kode'));
	    	$dept_id  = addslashes($this->input->post('dept_id'));
	      	$barcode_id    = addslashes($this->input->post('barcode_id'));
	      	$nama_produk   = addslashes($this->input->post('nama_produk'));
	      	$quant_id = $this->input->post('quant_id');
	      	$row_order = $this->input->post('row_order');


	      	// cek barcode di transfer_lokasi_items
	      	$cek_ = $this->m_transferLokasi->cek_barcode_transfer_lokasi_items_by_kode($kode_tl,$barcode_id,$row_order)->row_array();

	      	// cek status 
	    	$tl  = $this->m_transferLokasi->get_transfer_lokasi_by_kode($kode_tl);

	      	if(empty($kode_tl) && empty($row_order) ){
          		$callback = array('status' => 'failed','message' => 'Data Gagal Dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');
        	
            }else if(empty($cek_['kode_produk'])){
                $callback = array('status' => 'failed','message' => 'Maaf, Produk/Lot Kosong  atau sudah dihapus !', 'icon' =>'fa fa-check', 'type' => 'danger');

            }else if($tl->status == 'done'){
                $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Dihapus, Transfer Lokasi Sudah Done !', 'icon' =>'fa fa-check', 'type' => 'danger');

            }else if($tl->status == 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Data tidak bisa Dihapus, Transfer Lokasi Batal !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else{

            	// delete items  transfer lokasi items
            	$this->m_transferLokasi->delete_transfer_lokasi_items($kode_tl,$barcode_id,$row_order);

            	// cek transfer_lokasi_items
	   			$tli       = $this->m_transferLokasi->cek_transfer_lokasi_items_by_kode($kode_tl);
			   	if(!$tli){
					// update status transfer lokasi
					$status = 'draft';
					$this->m_transferLokasi->update_status_transfer_lokasi($kode_tl,$status);	   				
	   			}

            	$jenis_log   = "cancel";
				$note_log    = "Hapus Items ". $nama_produk." | ".$barcode_id." | ".$quant_id;
				$this->_module->gen_history($sub_menu, $kode_tl, $jenis_log, $note_log, $username);

	      		$callback = array('status' => 'success', 'message' => 'Data Berhasil Dihapus !', 'icon' =>'fa fa-check', 'type' => 'success'  ); 
            }

	   	}

		echo json_encode($callback);
	}


	public function generate()
	{
		
		if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
	        // session habis
	        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
	    }else{

	    	$sub_menu = $this->uri->segment(2);
		    $username = $this->session->userdata('username'); 

	    	$kode_tl  = addslashes($this->input->post('kode'));
	    	$dept_id  = addslashes($this->input->post('dept_id'));

	    	$ld            = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
            $lokasi_stock   = $ld['stock_location'];
            $tgl           = date('Y-m-d H:i:s');

            $validBarcode = TRUE;
            $sameLokasi   = TRUE;
            $sameQty      = TRUE;

            $validBarcode_ = '';
            $sameLokasi_   = '';
            $sameQty_      = '';
            $data          = [];
            $data_tmp      = [];

            $case          = '';
            $where         = '';


	    	$tl       = $this->m_transferLokasi->get_transfer_lokasi_by_kode($kode_tl);
	    	$lokasi_tujuan = addslashes($tl->lokasi_tujuan);

	   		$tli       = $this->m_transferLokasi->cek_transfer_lokasi_items_by_kode($kode_tl);

	   		// cek status 
	    	$tl  = $this->m_transferLokasi->get_transfer_lokasi_by_kode($kode_tl);

	    	if($tl->status == 'done'){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Transfer Lokasi Sudah Done !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else if($tl->status == 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Transfer Lokasi telah dibatalkan !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else if(!$tli){
				$callback = array('status' => 'failed','message' => 'Barcode yang akan di Transfer Lokasi masih kosong !', 'icon' =>'fa fa-check', 'type' => 'danger');
	   		}else{

		    	//lock table
	            $this->_module->lock_tabel('transfer_lokasi WRITE, transfer_lokasi_items WRITE, stock_quant WRITE, log_history WRITE, main_menu_sub WRITE, user WRITE');

		    	// foreach list transfer lokasi items
		    	$tli = $this->m_transferLokasi->get_transfer_lokasi_items_by_kode($kode_tl);
		    	$count = 0;
		    	foreach ($tli as $val) {
		    		// cek lokasi barcode
		      		$validBarcodeDepartement = $this->m_transferLokasi->is_valid_lokasi_barcode_by_dept($lokasi_stock,addslashes($val->lot));

		    		// cek lokasi_asal barcode and qty
		    		$validSame = $this->m_transferLokasi->is_same_valid_lokasi_asal_by_quant_id($val->quant_id)->row_array();

		      		if(!$validBarcodeDepartement){
		      			$validBarcode = FALSE;
		      			$validBarcode_ = $val->lot.', ';

		      		}else if($validSame['lokasi_rak'] != $val->lokasi_asal){ // lokasi asal != lokasi rak
	            		$sameLokasi   = FALSE;
	            		$sameLokasi_   = $val->lot.', ';

		      		}else if($validSame['qty'] != $val->qty){ // qty stock quant != qty transfer
	            		$sameQty      = FALSE;
	            		$sameQty_     = $val->lot.', ';

		      		}else{
		      			// sql batch update stock_quant
		      			$case  .= "when quant_id = '".$val->quant_id."' then '".$lokasi_tujuan."'";
	    	            $where .= "'".$val->quant_id."',";

		      		}

		      		$count++;

		    	}

		    	if($validBarcode == FALSE){
			    	$validBarcode_ = rtrim($validBarcode_, ', ');
		    		$callback = array('status' => 'failed', 'message' => 'Lokasi Barcode '.$validBarcode_.' tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 

		    	}else if($sameLokasi == FALSE){
		    		$sameLokasi_ = rtrim($sameLokasi_, ', ');
		    		$callback = array('status' => 'failed', 'message' => 'Lokasi Asal Barcode '.$sameLokasi_.' tidak sesuai !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );

		    	}else if($sameQty == FALSE){
		    		$sameQty_ = rtrim($sameQty_, ', ');
		    		$callback = array('status' => 'failed', 'message' => 'Qty Stock Barcode '.$sameQty_.' tidak sesuai !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
		    	}else{

			    	if(!empty($case) AND !empty($where)){
			    		
				    	$where = rtrim($where, ',');
			    	    $sql_update = "UPDATE stock_quant SET lokasi_rak =(case ".$case." end) WHERE  quant_id in (".$where.") ";
			    	    $this->_module->update_reff_batch($sql_update);

			    	    // update total lot, tanggal genrate
			    	    $sql_update2 = "UPDATE transfer_lokasi set total_lot= '$count', tanggal_generate = '$tgl' WHERE kode_tl = '$kode_tl' ";
			    	    $this->_module->update_reff_batch($sql_update2);

		    		}

		    		$status = 'done';
					$this->m_transferLokasi->update_status_transfer_lokasi($kode_tl,$status);	   				

	            	$jenis_log   = "generate";
					$note_log    = "Generated ".$kode_tl;
					$this->_module->gen_history($sub_menu, $kode_tl, $jenis_log, $note_log, $username);

		    		$callback = array('status' => 'success', 'message' => 'Generate Data Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success'  ); 
		    	}

		    	// unlock tabel
	            $this->_module->unlock_tabel();

           	}

	   	}

	   	echo json_encode($callback);

	}

	public function batal_transfer_lokasi()
	{

		if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
	        // session habis
	        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
	    }else{

	    	$sub_menu = $this->uri->segment(2);
		    $username = $this->session->userdata('username');

	    	$kode_tl  = addslashes($this->input->post('kode'));

		    // cek status 
	    	$tl  = $this->m_transferLokasi->get_transfer_lokasi_by_kode($kode_tl);
	    	// cek items
	   		$tli       = $this->m_transferLokasi->cek_transfer_lokasi_items_by_kode($kode_tl);


	    	if($tl->status == 'done'){
                $callback = array('status' => 'failed','message' => 'Maaf, Transfer Lokasi tidak bisa dibatalkan,  Status Transfer Lokasi Sudah Done !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else if($tl->status == 'cancel'){
                $callback = array('status' => 'failed','message' => 'Maaf, Transfer Lokasi sudah dibatalkan sebelumnya !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else if($tl->status == 'ready' or $tli ){
                $callback = array('status' => 'failed','message' => 'Maaf, Status Transfer Lokasi sudah Ready atau Ada Barcode yang sudah di Scan !', 'icon' =>'fa fa-check', 'type' => 'danger');
            }else{

            	$status = 'cancel';
				$this->m_transferLokasi->update_status_transfer_lokasi($kode_tl,$status);	   				

	            $jenis_log   = "cancel";
				$note_log    = "Batal Transfer Lokasi | ".$kode_tl;
				$this->_module->gen_history($sub_menu, $kode_tl, $jenis_log, $note_log, $username);

		    	$callback = array('status' => 'success', 'message' => 'Generate Data Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success'  ); 

            }

		}

		echo json_encode($callback);
	}


}