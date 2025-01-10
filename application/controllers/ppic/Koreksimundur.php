<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');
/**
 * 
 */
class Koreksimundur extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
		$this->load->model('m_koreksi');
		$this->load->model('m_mo');
	}

	public function index()
	{	
        $data['id_dept'] = 'KRM';
		$this->load->view('ppic/v_koreksi_mundur', $data);
	}

	public function add()
    {
        $data['id_dept']  ='KRM';
        return $this->load->view('ppic/v_koreksi_mundur_add', $data);
    }

	public function edit($id = null)
	{
		if(!isset($id)) show_404();
        $kode_koreksi_decrypt = decrypt_url($id);
        $data['id_dept'] ='KRM';
        $data['koreksi'] = $this->m_koreksi->get_data_koreksi_by_kode($kode_koreksi_decrypt);
		$data['mms']     = $this->_module->get_data_mms_for_log_history('KRM');
        $data['details'] = '';
        if(empty($data["koreksi"])){
          show_404();
        }else{
          return $this->load->view('ppic/v_koreksi_mundur_edit',$data);
        }
	}

	public function get_departement_select2()
	{
		$nama  = addslashes($this->input->post('nama'));
   		$callback = $this->m_koreksi->get_list_departement($nama);
        echo json_encode($callback);
	}



	function get_data()
	{
		$sub_menu  = $this->uri->segment(2);
		$kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
		$list = $this->m_koreksi->get_datatables($kode['kode']);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {
				$kode_encrypt = encrypt_url($field->kode_koreksi);
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = '<a href="'.base_url('ppic/koreksimundur/edit/'.$kode_encrypt).'">'.$field->kode_koreksi.'</a>';
				$row[] = $field->tanggal_dibuat;
				$row[] = $field->tanggal_transaksi;
				$row[] = $field->nama_status;
				$row[] = $field->note;
				$data[] = $row;
		}
	
		$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->m_koreksi->count_all($kode['kode']),
				"recordsFiltered" => $this->m_koreksi->count_filtered($kode['kode']),
				"data" => $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}


	public function simpan()
	{
		try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{
 				$kode_koreksi   = ($this->input->post('kode_koreksi'));
 				$note   		= ($this->input->post('note'));
				$tanggal        = date("Y-m-d H:i:s");

				$sub_menu  = $this->uri->segment(2);
                $username  = addslashes($this->session->userdata('username'));
                $nu        = $this->_module->get_nama_user($username)->row_array(); 
                $nama_user = addslashes($nu['nama']);

				if(isset($kode_koreksi)){//update

					$km = $this->m_koreksi->get_data_koreksi_by_kode($kode_koreksi);
					if(empty($km)){
						throw new \Exception('Data Koreksi tidak ditemukan !', 200);
					}
					
					if($km->status == 'cancel'){
 						$callback = array('status' => 'failed', 'message' => 'Data tidak bisa Disimpan, status sudah Cancel', 'icon' =>'fa fa-warning', 'type' => 'danger');
					}else if($km->status == 'done'){
						$callback = array('status' => 'failed', 'message' => 'Data tidak bisa Disimpan, status sudah Done ', 'icon' =>'fa fa-warning', 'type' => 'danger');
					}else{

						$data_koreksi = array('note' => $note);
						$this->m_koreksi->update_data_koreksi($kode_koreksi,$data_koreksi);

						$jenis_log = "edit";
						$note_log  = $kode_koreksi. " | ".$note;
						$data_history = array(
											'datelog'   => date("Y-m-d H:i:s"),
											'kode'      => $kode_koreksi,
											'jenis_log' => $jenis_log,
											'note'      => $note_log  );
								
							
						$this->_module->gen_history_ip($sub_menu,$username,$data_history);
								
						$callback = array('status' => 'succes', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-success', 'type' => 'success', );

					}

				}else{// simpan
						
					// get kode_koreksi
					$kode_koreksi = '';
					$kode_koreksi   = $this->m_koreksi->get_kode_koreksi();      
					$kode_koreksi   = substr("0000" . $kode_koreksi,-4);                  
					$kode_koreksi   = "KM/".date("y") . '/' .  date("m") . '/' . $kode_koreksi;

					$data_koreksi = array(	
										'kode_koreksi' => $kode_koreksi,
										'tanggal_dibuat' => $tanggal,
										'tanggal_transaksi' => $tanggal,
										'note'		   => $note,
										'nama_user'    => $nama_user
					);

					$this->m_koreksi->save_data_koreksi($data_koreksi);

					$jenis_log = "create";
                    $note_log  = $kode_koreksi. " | ".$note;
                    $data_history = array(
                                        'datelog'   => date("Y-m-d H:i:s"),
                                        'kode'      => $kode_koreksi,
                                        'jenis_log' => $jenis_log,
                                        'note'      => $note_log  );
                            
                        
                    $this->_module->gen_history_ip($sub_menu,$username,$data_history);
                	$kode_koreksi_encr = encrypt_url($kode_koreksi);
                    $callback = array('status' => 'succes', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-success', 'type' => 'success','isi' => $kode_koreksi_encr, );

				}

				//unlock table
      			$this->_module->unlock_tabel();

				$this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
			}

		} catch(Exception $ex){
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        } finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
	}


	function get_kode_transaksi() 
	{
		$koreksi_apa = ($this->input->post('koreksi_apa'));
		$departemen  = ($this->input->post('departemen'));
		$param       = ($this->input->post('nama'));
		$callback    = array();
		if($koreksi_apa == 'mo'){
			$callback = $this->m_koreksi->get_list_mrp_production($departemen,$param);
		}else if($koreksi_apa == 'in'){
			$callback = $this->m_koreksi->get_list_penerimaan_barang($departemen,$param);
		}else{
			$callback = $this->m_koreksi->get_list_pengiriman_barang($departemen,$param);
		}

        echo json_encode($callback);

	}

	public function cari_produk()
  	{
      $kode_koreksi  = $this->input->post('kode_koreksi');
      $data['kode_koreksi'] = $kode_koreksi;
      return $this->load->view('modal/v_koreksi_mundur_add_produk_modal',$data);
  	}

	public function simpan_lot()
	{
		try {
			if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{

				

				$callback = array('status' => 'success', 'message' => 'Data Berhasil disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');

				$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

			}
		} catch (Exception $ex) {
			$this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('status'=>'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
		} finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
	}

	public function search_produk_koreksi()
	{
		$filter  = $this->input->post('f_filter');
		$arr_where = array();
		$koreksi_apa = "";
		$tipe        = "";
		foreach($filter as $val){
			$arr_where = array('a.dept_id' => $val['departemen'],
								'a.kode'	=> $val['kode']);
			$koreksi_apa = $val['koreksi_apa'];
			$tipe        = $val['tipe'];
		}

		$list = $this->m_koreksi->get_datatables2($arr_where,$koreksi_apa,$tipe);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {
			$no++;
			$row = array();
			$row[] = $no.".";
			$row[] = $field->kode_produk;
			$row[] = $field->nama_produk;
			$row[] = $field->lot;
			$row[] = number_format($field->qty,2)." ".$field->uom;
			$row[] = number_format($field->qty2,2)." ".$field->uom2;
			$row[] = $field->nama_grade;
			$row[] = $field->reff_note;
			$row[] = $field->status;
			// $row[] = array("quant_id" => $field->quant_id, "move_id"=>$field->move_id);
			$row[] = $field->quant_id."|".$field->move_id;
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_koreksi->count_all2($arr_where,$koreksi_apa,$tipe),
			"recordsFiltered" => $this->m_koreksi->count_filtered2($arr_where,$koreksi_apa,$tipe),
			"data" => $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}


	function add_batch_modal()
    {
        $kode               = $this->input->post('kode');
        $data['kode']       = $kode;
        return $this->load->view('modal/v_koreksi_mundur_add_batch_modal',$data);
    }


	public function get_data_batch()
	{
		$kode  = $this->input->post('kode');
		$arr_where = array('km.kode_koreksi' => $kode);
		$list = $this->m_koreksi->get_datatables3($arr_where);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {
			$no++;
			$row = array();
			$row[] = $no.".";
			$row[] = $field->no_batch;
			$row[] = $field->nama_departemen;
			$row[] = $field->koreksi;
			$row[] = $field->tipe;
			$row[] = $field->koreksi_lebih_kurang;
			$row[] = $field->kode_transaksi;
			$row[] = $field->kode_produk;
			$row[] = $field->nama_produk;
			$row[] = number_format($field->koreksi_qty1,2);
			$row[] = number_format($field->koreksi_qty2,2);
			$row[] = $field->status;
			// $row[] = $field->no_batch."|".$field->row_order;
			if($field->status == 'draft' | $field->status == 'process'){
				$row[] = '<button type="button" class="btn btn-primary btn-xs koreksi_batch width-btn" data-row="' . $field->row_order . '" data-batch ="'.$field->no_batch.'" data-title="Koreksi">Koreksi</button> <button type="button" class="btn btn-danger btn-xs delete_batch width-btn" data-row="' . $field->row_order . '" data-batch ="'.$field->no_batch.'" data-title="Hapus">Hapus</button>';
			}else{
				$row[] = "";
			}
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_koreksi->count_all3($arr_where),
			"recordsFiltered" => $this->m_koreksi->count_filtered3($arr_where),
			"data" => $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}


	public function get_data_batch_items()
	{
		$kode  = $this->input->post('kode');
		$arr_where = array('kode_koreksi' => $kode);
		$list = $this->m_koreksi->get_datatables4($arr_where);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {
			$no++;
			$row = array();
			$row[] = $no.".";
			$row[] = $field->no_batch;
			$row[] = $field->kode_produk;
			$row[] = $field->nama_produk;
			$row[] = $field->grade;
			$row[] = $field->lot;
			$row[] = number_format($field->qty,2)." ".$field->uom;
			$row[] = number_format($field->qty2,2)." ".$field->uom2;
			$row[] = $field->qty_move;
			$row[] = $field->qty2_move;
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_koreksi->count_all4($arr_where),
			"recordsFiltered" => $this->m_koreksi->count_filtered4($arr_where),
			"data" => $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}


	public function get_data_koreksi_mutasi()
	{
		$kode  = $this->input->post('kode');
		$arr_where = array('km.kode_koreksi' => $kode);
		$list = $this->m_koreksi->get_datatables5($arr_where);
		$data = array();
		$no = $_POST['start'];
		foreach ($list as $field) {
			$no++;
			$row = array();
			$row[] = $no.".";
			$row[] = $field->nama_departemen;
			$row[] = $field->tahun;
			$row[] = $field->bln;
			$row[] = $field->no_batch;
			$row[] = $field->tanggal_proses_mutasi;
			$row[] = $field->status;
			if($field->status == 'draft' | $field->status == 'process'){
				$row[] = '<button type="button" class="btn btn-primary btn-xs proses_mutasi width-btn" data-row="' . $field->id . '"  data-title="Proses Mutasi">Proses</button>';
			}else{
				$row[] = "";
			}
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsTotal" => $this->m_koreksi->count_all5($arr_where),
			"recordsFiltered" => $this->m_koreksi->count_filtered5($arr_where),
			"data" => $data,
		);
		//output dalam format JSON
		echo json_encode($output);
	}

	public function simpan_produk_koreksi_modal()
	{
		try{
            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
            }else{	

				$sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 

				$kode_koreksi  = $this->input->post('kode_koreksi');
                $qty1_koreksi  = $this->input->post('qty1_koreksi');
                $qty2_koreksi  = $this->input->post('qty2_koreksi');
                $filter        = $this->input->post('filter');
                $arr_data      = $this->input->post('arr_data');
                $koreksi_lebih_kurang      = $this->input->post('koreksi_lebih_kurang');
                $tgl           = date('Y-m-d H:i:s'); 

				$koreksi = $this->m_koreksi->get_data_koreksi_by_kode($kode_koreksi);
				if(empty($koreksi)){
                    throw new \Exception('Data Koreksi tidak ditemukan !', 200);
				}else if($koreksi->status == 'done'){
                    throw new \Exception('Maaf, Data Tidak Bisa Disimpan, Status Sudah Done !', 200);
				}else if($koreksi->status == 'cancel'){
                    throw new \Exception('Maaf, Data Tidak Bisa Disimpan, Status Cancel !', 200);
				}else if(empty($qty1_koreksi) AND empty($qty2_koreksi)){
                    throw new \Exception(' Koreksi Qty1 atau  Koreksi Qty2 Harus Diisi / tidak boleh 0 !', 200);
				}else if(($qty1_koreksi < 0) AND ($qty2_koreksi < 0)){
                    throw new \Exception(' Koreksi Qty1 atau  Koreksi Qty2 Harus lebih dari 0 !', 200);
				}else if(empty($koreksi_lebih_kurang)){
                    throw new \Exception('Koreksi Lebih / Kurang harus diisi !', 200);
				}else{

					$arr_batch 		 = array();
					$arr_batch_items = array();
					$kode_produk_tmp = "";
					$lot_tmp 		 = "";
					$row_order       = 1;
					$note_log_batch = "";
					$note_log_batch_items = "";
					$kode_substr    = substr($kode_koreksi,3);// example KM/24/07/0001 => 23110020
                    $kode_reverse   = strrev($kode_substr);

					if($koreksi_lebih_kurang == 'kurang'){
						if(count($arr_data) > 1){
                    		throw new \Exception('Data untuk mengoreksi kurang, Hanya boleh memilih 1 KP/Lot  !', 200);
						}
					}

					// get row batch
					$last_row   = $this->m_koreksi->get_row_order_by_kode($kode_koreksi);
					$counter    = sprintf("%02d",$last_row);
                    $no_batch   = $kode_reverse.'/'.$counter;
					foreach($arr_data as $dsm){
							$dsm_ex = explode('|', $dsm);
							$quant_id = $dsm_ex[0] ?? "";
							$move_id  = $dsm_ex[1] ?? "";

							// cek stock_move_items
							$get = $this->m_koreksi->get_stock_move_items_by_kode($move_id,$quant_id);
							if(empty($get)){
  								throw new \Exception('Data Stock Move tidak ditemukan !', 200);
								break;
							}else{
								// cek quant_id / lot sudah di input atau belum
								$cek_quant = $this->m_koreksi->cek_lot_quant_input_by_kode($kode_koreksi,$quant_id,$move_id);

								if(!empty($cek_quant)){
									$tmp_produk = $get->nama_produk." ".$get->lot;
									throw new \Exception("Produk sudah diinput <br> ".$tmp_produk, 200);
									break;
								}

								if($kode_produk_tmp !== ""){
									if($kode_produk_tmp !== $get->kode_produk){
										throw new \Exception("Nama Produk dipilih ada yang tidak sama ", 200);
									}
								}

								if($lot_tmp !== ""){
									if($lot_tmp !== $get->lot){
										throw new \Exception("Lot dipilih ada yang tidak sama ", 200);
									}
								}

								// cek lot sudah masih proses koreksi atau blm
								$cek_lot = $this->m_koreksi->cek_lot_proses_koreksi_mundur($get->lot,$kode_koreksi);
								if(!empty($cek_lot)){
									throw new \Exception("Lot sedang terpakai Proses Koreksi Mundur di No. ".$cek_lot->kode_koreksi, 200);
								}

								$kode_produk_tmp = $get->kode_produk;
								$lot_tmp 		 = $get->lot;
							
								//
								$arr_batch_items[] = array(
														"kode_koreksi" 	=> $kode_koreksi,
														'no_batch'		=> $no_batch,
														'move_id'		=> $move_id,
														'quant_id'		=> $quant_id,
														'tanggal_move'  => $get->tanggal_transaksi,
														'kode_produk'	=> $get->kode_produk,
														'nama_produk'	=> $get->nama_produk,
														'grade'			=> $get->nama_grade,
														'lot'			=> $get->lot,
														'qty'			=> $get->qty,
														'uom'			=> $get->uom,
														'qty2'			=> $get->qty2,
														'uom2'			=> $get->uom2,
														'row_order'		=> $row_order,
								);

								$kode_produk = $get->kode_produk;
								$nama_produk = $get->nama_produk;

								$note_log_batch_items .= $row_order.". ".$nama_produk." ".$get->nama_grade." ".$get->lot." ".$get->qty." ".$get->uom." ".$get->qty2." ".$get->uom2." <br>";

								$row_order++;
							}
					}

					$koreksi  = "";
					$tipe     = "";
					$dept_id  = "";
					$kode_transaksi = "";
					$row_order_batch  = $last_row;

					foreach($filter as $df){
						$koreksi  	= $df['koreksi_apa'];
						$tipe  	  	= $df['tipe'];
						$dept_id 	= $df['departemen'];
						$kode_transaksi = $df['kode'];
					}
					
					$cek_dept = $this->cek_dept_mutasi($dept_id);
					if($cek_dept == false){
						$get_dept  = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
                    	throw new \Exception("Departemen ".$get_dept['nama']." belum bisa melakukan Koreksi Mundur", 200);
					}

					$arr_batch[] = array(
								"kode_koreksi"	=> $kode_koreksi,
								"no_batch"		=> $no_batch,
								"koreksi"		=> $koreksi,
								"tipe"			=> $tipe,
								"dept_id"		=> $dept_id,
								"kode_transaksi"=> $kode_transaksi,
								'kode_produk'	=> $kode_produk,
								"nama_produk"	=> $nama_produk,
								"koreksi_qty1"	=> $qty1_koreksi,
								"koreksi_qty2"	=> $qty2_koreksi,
								'status'	    => 'draft',
								"row_order"		=> $row_order_batch,
								'koreksi_lebih_kurang' => $koreksi_lebih_kurang
					);


					$get_nm = $this->_module->get_nama_dept_by_kode($dept_id)->row();

					$note_log_batch = $get_nm->nama ?? ''." ".$koreksi." ".$tipe." ".$kode_transaksi." ".$nama_produk." ".$qty1_koreksi." ".$qty2_koreksi;

					if(count($arr_batch_items) > 0){

						//insert head
                        $insert_batch = $this->m_koreksi->insert_data_batch($arr_batch);
						if(!empty($insert_batch)){
                            throw new \Exception('Gagal Simpan Data Produk Batch' , 200);
                        }
						// insert item lot
                        $insert_batch_items = $this->m_koreksi->insert_data_batch_items($arr_batch_items);
						if(!empty($insert_batch_items)){
                            throw new \Exception('Gagal Simpan Data Produk Batch Items' , 200);
                        }
					}

					//log history
					$jenis_log = "edit";
                    $note_log  = "Tambah Batch <br> ".$note_log_batch." <br> ".$note_log_batch_items;
                	$data_history = array(
                                        'datelog'   => date("Y-m-d H:i:s"),
                                        'kode'      => $kode_koreksi,
                                        'jenis_log' => $jenis_log,
                                        'note'      => $note_log  );
                            
                    // load in library
                    $this->_module->gen_history_ip($sub_menu,$username,$data_history);

					$callback = array('message' => 'Data Berhasil Disimpan', 'icon' => 'fa fa-check', 'type' => 'success', );
				}
			
				
				$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
			}
		}catch(Exception $ex){
            // $this->_module->finishRollBack();
            // $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally {
            // unlock table
            // $this->_module->unlock_tabel();
        }
	}


	public function hapus_batch()
	{
		try {
			if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
			} else {

				$sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 

				$kode   = $this->input->post('kode');
                $row  	= $this->input->post('row');
                $batch  = $this->input->post('batch');

				$tgl           = date('Y-m-d H:i:s'); 

				$koreksi = $this->m_koreksi->get_data_koreksi_by_kode($kode);
				if(empty($koreksi)){
                    throw new \Exception('Data Koreksi tidak ditemukan !', 200);
				}else if($koreksi->status == 'done'){
                    throw new \Exception('Maaf, Data Tidak Bisa dihapus, Status Sudah Done !', 200);
				}else if($koreksi->status == 'cancel'){
                    throw new \Exception('Maaf, Data Tidak Bisa dihapus, Status Cancel !', 200);
				}else{

					// cek data batch
					$get_batch = $this->m_koreksi->get_data_koreksi_batch_by_kode($kode,$batch,$row);
					if(empty($get_batch)){
                    	throw new \Exception('Data Koreksi Batch tidak ditemukan !', 200);
					}else if($get_batch->status == 'done'){
                    	throw new \Exception('Maaf, Data Tidak Bisa dihapus, Status Sudah Done !', 200);
					}else if($get_batch->status == 'process'){
                    	throw new \Exception('Maaf, Data Tidak Bisa dihapus, Status Sudah Process !', 200);
					}else if($get_batch->status == 'cancel'){
                    	throw new \Exception('Maaf, Data Tidak Bisa dihapus, Status Cancel !', 200);
					}
					
					$note_log_batch = $get_batch->nama_departemen." ".$get_batch->koreksi." ".$get_batch->tipe." ".$get_batch->kode_transaksi." ".$get_batch->nama_produk." ".$get_batch->koreksi_qty1." ".$get_batch->koreksi_qty2;
					
					$get_batch_items = $this->m_koreksi->get_data_koreksi_batch_items_by_kode($kode,$batch)->result();
					$note_log_batch_items = "";
					$no                   = 1;
					foreach($get_batch_items as $val){
						$note_log_batch_items .= $no.". ".$val->nama_produk." ".$val->grade." ".$val->lot." ".$val->qty." ".$val->uom." ".$val->qty2." ".$val->uom2." <br>";
						$no++;
					}

					// delete batch
					$delete = $this->m_koreksi->delete_data_batch($kode,$batch,$row);
                    if(!empty($delete)){
                        throw new \Exception('Gagal Menghapus data ', 500);
                    }

					// delete batch items
					$delete2 = $this->m_koreksi->delete_data_batch_items($kode,$batch);
                    if(!empty($delete2)){
                        throw new \Exception('Gagal Menghapus data ', 500);
                    }

						//log history
					$jenis_log = "edit";
                    $note_log  = "Hapus Batch <br> ".$note_log_batch." <br> ".$note_log_batch_items;
                	$data_history = array(
                                        'datelog'   => date("Y-m-d H:i:s"),
                                        'kode'      => $kode,
                                        'jenis_log' => $jenis_log,
                                        'note'      => $note_log  );
                            
                    // load in library
                    $this->_module->gen_history_ip($sub_menu,$username,$data_history);

					$callback = array('message' => 'Data Berhasil dihapus', 'icon' => 'fa fa-check', 'type' => 'success', );
				}

				$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

			}
		}catch(Exception $ex){
            // $this->_module->finishRollBack();
            // $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally {
            // unlock table
            // $this->_module->unlock_tabel();
        }
	}


	public function koreksi_batch()
	{
		try {

			if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
			} else {
					
				$sub_menu  = $this->uri->segment(2);
                $username  = addslashes($this->session->userdata('username')); 
				$nu        = $this->_module->get_nama_user($username)->row_array(); 
            	$nama_user = addslashes($nu['nama']);
				$ip        = $this->input->ip_address();

				$kode   = $this->input->post('kode');
                $row  	= $this->input->post('row');
                $batch  = $this->input->post('batch');

				$tgl           = date('Y-m-d H:i:s'); 

				// start transaction
                $this->_module->startTransaction();

				// lock table
                $this->_module->lock_tabel("stock_quant WRITE,koreksi_mundur WRITE, stock_move_items WRITE, acc_stock_move_items WRITE, acc_stock_quant_eom WRITE, koreksi_mundur_batch as kmb WRITE, koreksi_mundur_batch_items as a WRITE, koreksi_mundur_batch_items as b WRITE,  koreksi_mundur_batch_items WRITE, koreksi_mutasi WRITE, user WRITE ,main_menu_sub WRITE,log_history WRITE, mrp_production_rm_hasil WRITE, mrp_production_fg_hasil WRITE, koreksi_mundur_batch WRITE, departemen as d WRITE, mrp_production WRITE");

				$koreksi = $this->m_koreksi->get_data_koreksi_by_kode($kode);
				if(empty($koreksi)){
                    throw new \Exception('Data Koreksi tidak ditemukan !', 200);
				}else if($koreksi->status == 'done'){
                    throw new \Exception('Maaf, Data Tidak Bisa diKoreksi, Status Sudah Done !', 200);
				}else if($koreksi->status == 'cancel'){
                    throw new \Exception('Maaf, Data Tidak Bisa diKoreksi, Status Cancel !', 200);
				}else{

					// cek status batch
					$cek_batch= $this->m_koreksi->cek_data_koreksi_batch_by_kode($kode,$batch);
					if(empty($cek_batch)){
                    	throw new \Exception('Data Batch '.$batch.' tidak ditemukan !', 200);
					}else if($cek_batch->status == 'done'){
                    	throw new \Exception('Maaf, Data Tidak Bisa diKoreksi, Status Batch Sudah Done !', 200);
					}else if($cek_batch->status == 'process'){
                    	throw new \Exception('Maaf, Data Tidak Bisa diKoreksi, Status Batch Sudah Process !', 200);
					}else if($cek_batch->status == 'cancel'){
                    	throw new \Exception('Maaf, Data Tidak Bisa diKoreksi, Status Batch Sudah Cancel !', 200);
					}else if($cek_batch->total_qty1 < $cek_batch->koreksi_qty1 AND $cek_batch->koreksi_qty1 >= 0 AND $cek_batch->koreksi_lebih_kurang == 'lebih'){
                    	throw new \Exception('Maaf, Qty1 Koreksi kurang dari Qty yang akan di Koreksi !', 200);
					}else if($cek_batch->total_qty2 < $cek_batch->koreksi_qty2 AND $cek_batch->koreksi_qty2 >= 0  AND $cek_batch->koreksi_lebih_kurang == 'lebih'){
                    	throw new \Exception('Maaf, Qty2 Koreksi kurang dari Qty2 yang akan di Koreksi !', 200);
					}else{

						$dept_id = $cek_batch->dept_id;
						$cek_dept = $this->cek_dept_mutasi($dept_id);
						if($cek_dept == false){
							$get_dept  = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
							throw new \Exception("Departemen ".$get_dept['nama']." belum bisa melakukan Koreksi Mundur", 200);
						}
						
						// get location stock
						$get_dept  = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
						$stock_location = $get_dept['stock_location'];
						$kode_transaksi  = $cek_batch->kode_transaksi;
						if($cek_batch->koreksi == 'mo'){
							$type   = $cek_batch->tipe; // prod, con
						}else{
							$type   = $cek_batch->koreksi; // in, out
						}

						$kebutuhan_qty1 = $cek_batch->koreksi_qty1;
						$kebutuhan_qty2 = $cek_batch->koreksi_qty2;
						$koreksi_lk     = $cek_batch->koreksi_lebih_kurang;

						$data_update_smi = [];
						$data_update_acc_smi = [];
						$data_insert_mutasi  = [];
						$cek_arr_tmp         = [];
						$data_update_koreksi_mutasi = [];
						$tmp_update_move	 = [];

						$case = "";
						$case2 = "";
						$case_sq = "";
						$case2_sq = "";
						$case_mrp = "";
						$case3  = "";
						$case33  = "";
						$where3 = "";
						$where3_date = "";
						$where = "";
						$where_move = "";
						$where_lot  = "";
						$where_dept = "";
						$where_type = "";
						$where_kode_trans = "";
						$qty_move   = 0;
						$qty2_move  = 0;
						$qty_new    = 0;
						$qty2_new   = 0;

						$num_tmp    = 1;
						$tmp_produk_lot = "";
						$note_log_d = "";
						$loop_items = false;
						// cek acc_stock_move_items
						// $cek = $this->m_koreksi->cek_acc_stock_move_items($move_id,$quant_id,$kode_transaksi,'');
						$get_batch_items = $this->m_koreksi->get_data_koreksi_batch_items_by_kode($kode,$batch)->result();
						if($koreksi_lk == 'lebih'){
							foreach($get_batch_items as $val){
								$loop_items = true;
								// cek_stock_quant
								$cek_sq = $this->_module->get_stock_quant_by_id($val->quant_id)->row();
								$tglnow = date("Y-m-d", strtotime($tgl));
								$tgl_move = date("Y-m-d", strtotime($cek_sq->move_date));

								// acc stock move items
								$cek_acc = $this->m_koreksi->cek_acc_stock_move_items($dept_id,$type,$val->quant_id,$val->lot,$kode_transaksi);
								if(empty($cek_acc) AND ($tglnow != $tgl_move) ){
									$data_sm = $val->nama_produk." ".$val->lot;
									throw new \Exception('Data Mutasi Stock Move tidak ditemukan ! <br> '.$data_sm, 200);
								}else if((round($cek_acc->qty ?? 0,2) != round($val->qty ?? 0,2)) AND ($tglnow != $tgl_move) ){
									$data_sm = $val->nama_produk." ".$val->lot." ".$cek_acc->qty." ".$cek_acc->uom." ".$cek_acc->qty2." ".$cek_acc->uom2;
									throw new \Exception('Data Qty Mutasi Stock Move tidak Sama  ! <br> '.$data_sm, 200);
								}else if((round($cek_acc->qty2 ?? 0,2) != round($val->qty2 ?? 0,2)) AND ($tglnow != $tgl_move) ){
									$data_sm = $val->nama_produk." ".$val->lot." ".$cek_acc->qty." ".$cek_acc->uom." ".$cek_acc->qty2." ".$cek_acc->uom2;
									throw new \Exception('Data Qty2 Mutasi Stock Move tidak Sama  ! <br> '.$data_sm, 200);
								}else if(empty($cek_sq)){
									$data_sq = $val->nama_produk." ".$val->lot;
									throw new \Exception('Data Stock Quants tidak ditemukan ! <br> '.$data_sq, 200);	
								}else if($kebutuhan_qty1 <= 0 AND $kebutuhan_qty2 <= 0){
									$data_sq = $val->nama_produk." ".$val->lot;
									throw new \Exception('Data Qty Koreksi sudah terpenuhi. Silahkan Pilih Lot Kembali untuk memproses Koreksi ! <br> '.$data_sq, 200);							
								}else{
									$qty_sq_same = true;
									$qty2_sq_same = true;
									if(round($cek_sq->qty,2) != round($val->qty,2)){
										$qty_sq_same = false;
										// $data_sq = $val->nama_produk." ".$val->lot." ".$cek_sq->qty." ".$cek_sq->uom." ".$cek_sq->qty2." ".$cek_sq->uom2;
										// throw new \Exception('Data Qty Stock Quants tidak Sama  ! <br> '.$data_sq, 200);
									}
									
									if(round($cek_sq->qty2,2) != round($val->qty2,2)){
										$qty2_sq_same = false;
										// $data_sq = $val->nama_produk." ".$val->lot." ".$cek_sq->qty." ".$cek_sq->uom." ".$cek_sq->qty2." ".$cek_sq->uom2;
										// throw new \Exception('Data Qty2 Stock Quants tidak Sama  ! <br> '.$data_sq, 200);
									}

									$lokasi_not_valid = false;
									if (strpos(strtoupper($cek_sq->lokasi), 'ADJ') !== false || strpos(strtoupper($cek_sq->lokasi), 'WASTE') !== false ){// adj/ waste
										$lokasi_not_valid = true;
										throw new \Exception('Lokasi Stock tidak Valid, Lokasi Stock berada di '.$cek_sq->lokasi.' !', 200);	
									}

									if(round($kebutuhan_qty1,2) >= round($val->qty,2)){
										if(round($val->qty,2) > 0 ){
											$qty_move = $val->qty;
										}else{
											$qty_move = $val->qty;
										}
										$qty_new  = 0;
										// update qty jadi 0 
										// update qty, update2 table smi
										$case  .= "when quant_id = '".$val->quant_id."' then '".$qty_new."'";
										$where .= "'".$val->quant_id."',"; 
										$where_move .= "'".$val->move_id."',"; 
										$where_lot  .= "'".$val->lot."',"; 

										if($qty_sq_same == true AND $lokasi_not_valid == false){
											$case_sq  .= "when quant_id = '".$val->quant_id."' then '".$qty_new."'";
										}

										// update  qty, update2 table acc_smi 
										$where_dept .= "'".$cek_batch->dept_id."',";
										$where_type .= "'".$cek_batch->tipe."',";
										$where_kode_trans .= "'".$cek_batch->kode_transaksi."',";

										$kebutuhan_qty1 = round($kebutuhan_qty1,2) - round($val->qty,2);

										if($kebutuhan_qty2 > 0){
											if(round($kebutuhan_qty2,2) >= round($val->qty2,2)){
												if(round($val->qty2,2) > 0 ){
													$qty2_move = $val->qty2;
												}else{
													$qty2_move = $val->qty2;
												}
												$qty2_new  = 0;
												$case2 			.= "when quant_id =  '".$val->quant_id."' then '".$qty2_new."' ";
												$kebutuhan_qty2  = round($kebutuhan_qty2,2) - round($val->qty2,2);

												if($qty2_sq_same == true AND $lokasi_not_valid == false){
													$case2_sq  .= "when quant_id = '".$val->quant_id."' then '".$qty2_new."' ";
												}

											}else if(round($kebutuhan_qty2,2) < round($val->qty2,2)){
												$qty2_move  = $kebutuhan_qty2;
												$qty2_smi_update = round($val->qty2,2) - round($kebutuhan_qty2,2);
												$qty2_new   = $qty2_smi_update;
												$case2  		.= "when quant_id = '".$val->quant_id."' then '".$qty2_smi_update."'";
												$kebutuhan_qty2  = 0;
												if($qty2_sq_same == true AND $lokasi_not_valid == false){
													$case2_sq  .= "when quant_id = '".$val->quant_id."' then '".$qty2_smi_update."'";
												}
											}
										}else{
											$case2     .= "when quant_id = '".$val->quant_id."' then '".$val->qty2."'";
											$case2_sq  .= "when quant_id = '".$val->quant_id."' then '".$val->qty2."'";

										}

										//update qty move
										$tmp_update_move[]  = array(
															'row_order'	=> $val->row_order,
															'qty_move'	=> $qty_move,
															'qty2_move'	=> $qty2_move,
										);
										

									}else if(round($kebutuhan_qty1,2) < round($val->qty,2)){
										$qty_move = $kebutuhan_qty1;
										// $kebutuhan_qty1 = round($kebutuhan_qty1,2) - round($val->qty,2);
										$qty1_smi_update = round($val->qty,2) - round($kebutuhan_qty1,2);
										$qty_new  = $qty1_smi_update;
									
										// update  qty, update2 table smi 
										$case  .= "when quant_id = '".$val->quant_id."' then '".$qty1_smi_update."'";
										$where .= "'".$val->quant_id."',"; 
										$where_move .= "'".$val->move_id."',"; 
										$where_lot  .= "'".$val->lot."',"; 
										$kebutuhan_qty1 = 0;

										if($qty_sq_same == true AND $lokasi_not_valid == false){
											$case_sq  .= "when quant_id = '".$val->quant_id."' then '".$qty1_smi_update."'";
										}

										// update  qty, update2 table acc_smi 
										$where_dept .= "'".$cek_batch->dept_id."',";
										$where_type .= "'".$cek_batch->tipe."',";
										$where_kode_trans .= "'".$cek_batch->kode_transaksi."',";

										if($kebutuhan_qty2 > 0){
											if(round($kebutuhan_qty2,2) >= round($val->qty2,2)){
												if(round($val->qty2,2) > 0 ){
													$qty2_move = $val->qty2;
												}else{
													$qty2_move = $val->qty2;
												}
												$qty2_new  = 0;
												$case2 			.= "when quant_id =  '".$val->quant_id."' then '0' ";
												$kebutuhan_qty2  = round($kebutuhan_qty2,2) - round($val->qty2,2);
												if($qty2_sq_same == true AND $lokasi_not_valid == false){
													$case2_sq  .= "when quant_id = '".$val->quant_id."' then '0'";
												}

											}else if(round($kebutuhan_qty2,2) < round($val->qty2,2)){
												$qty2_move  = $kebutuhan_qty2;
												$qty2_smi_update = round($val->qty2,2) - round($kebutuhan_qty2,2);
												$qty2_new   = $qty2_smi_update;
												$case2  		.= "when quant_id = '".$val->quant_id."' then '".$qty2_smi_update."'";
												$kebutuhan_qty2  = 0;
												if($qty2_sq_same == true AND $lokasi_not_valid == false){
													$case2_sq  .= "when quant_id = '".$val->quant_id."' then '".$qty2_smi_update."'";
												}
											}
										}else{
											$case2     .= "when quant_id = '".$val->quant_id."' then '".$val->qty2."'";
											$case2_sq  .= "when quant_id = '".$val->quant_id."' then '".$val->qty2."'";

										}

										//update qty move
										$tmp_update_move[]  = array(
															'row_order'	=> $val->row_order,
															'qty_move'	=> $qty_move,
															'qty2_move'	=> $qty2_move,
										);
										
									}
									$qty_move   = 0;
									$qty2_move  = 0;


									// // cek qty acc_stock_quant_eom
									// $cek_sq = $this->m_koreksi->cek_acc_stock_quant_eom($val->quant_id,$stock_location);
									// if(!empty($cek_sq)){
									// 	$kebutuhan_qty1_cq = $cek_batch->koreksi_qty1;
									// 	$kebutuhan_qty2_cq = $cek_batch->koreksi_qty2;
									// 	// if($cek_sq->qty < $cek_batch->koreksi_qty1){
									// 	// 	throw new \Exception('Data Qty Stock Akhir bulan kurang dari Qty yang akan dikoreksi ! <br> '.$data_sm, 200);
									// 	// }
									// 	foreach($cek_sq as $cq){
									// 		if(round($kebutuhan_qty1_cq,2) >= round($cq->qty,2)){

									// 			$case3  .= "when quant_id =  '".$cq->quant_id."' then '0' ";
									// 			$where3 .= "'".$val->quant_id."',"; 
									// 			$where3_date .= "'".date("Y-m", strtotime($cq->tanggal))."',";

									// 			if($kebutuhan_qty2_cq > 0){
									// 				if(round($kebutuhan_qty2_cq,2) >= round($cq->qty2,2)){
									// 					$case33 		.= "when quant_id =  '".$val->quant_id."' then '0' ";
									// 					$kebutuhan_qty2_cq  = round($kebutuhan_qty2_cq,2) - round($cq->qty2,2);

									// 				}else if(round($kebutuhan_qty2_cq,2) < round($cq->qty2,2)){
									// 					$qty2_sq_update = round($cq->qty2,2) - round($kebutuhan_qty2_cq,2);
									// 					$case33  		.= "when quant_id = '".$val->quant_id."' then '".$qty2_sq_update."'";
									// 					$kebutuhan_qty2_cq  = 0;
									// 				}
									// 			}

									// 		}else if(round($kebutuhan_qty1_cq,2) < round($cq->qty,2)){
									// 			$qty1_sq_update = round($cq->qty,2) - round($kebutuhan_qty1_cq,2);
									// 			// update  qty, update2 table acc_sq_eom 
									// 			$case3  .= "when quant_id = '".$val->quant_id."' then '".$qty1_sq_update."'";
									// 			$where3 .= "'".$val->quant_id."',"; 
									// 			// $where3_date .= "'".date("Y-m", strtotime($cq->tanggal))."',";
									// 			$where3_date .= "'".$cq->tanggal."',";

									// 			if($kebutuhan_qty2_cq > 0){
									// 				if(round($kebutuhan_qty2_cq,2) >= round($cq->qty2,2)){
									// 					$case33 		.= "when quant_id =  '".$val->quant_id."' then '0' ";
									// 					$kebutuhan_qty2_cq  = round($kebutuhan_qty2_cq,2) - round($cq->qty2,2);

									// 				}else if(round($kebutuhan_qty2_cq,2) < round($cq->qty2,2)){
									// 					$qty2_sq_update = round($cq->qty2,2) - round($kebutuhan_qty2_cq,2);
									// 					$case33  		.= "when quant_id = '".$val->quant_id."' then '".$qty2_sq_update."'";
									// 					$kebutuhan_qty2_cq  = 0;
									// 				}
									// 			}

									// 		}
									// 	}
										
									// }
									if(!empty($cek_acc->periode_th) AND !empty($cek_acc->periode_bln)){
										$date_db 	  = $cek_acc->periode_th.'-'.$cek_acc->periode_bln;
										$date_db_str  = strtotime($date_db);
										$date_now 	  = strtotime(date("Y-m", strtotime("-1 month")));
										
										while($date_now >= $date_db_str){

											$tahun = date("Y", $date_db_str);
											$bln = date("n", $date_db_str);
											$cek_b_km2 = $this->m_koreksi->cek_koreksi_mutasi_by_batch($kode,$dept_id,$tahun,$bln,'draft');
											$date_same = false;
											// cek tgl di array saat looping
											foreach($cek_arr_tmp as $cek_arr){
													if($cek_arr['dept_id'] == $dept_id AND $cek_arr['tahun'] == $tahun AND $cek_arr['bln'] == $bln){
															$date_same = true;
													}
											}

											$cek_arr_tmp[]        = array("dept_id"=>$dept_id, "tahun" => $tahun, "bln"	=> $bln);

													
											if(!empty($cek_b_km2)){// update
												$new_no_batch = $cek_b_km2->no_batch.",".$val->no_batch."(".$cek_batch->koreksi.")";
												$data_update_koreksi_mutasi[] = array(
																						"id"=>$cek_b_km2->id,
																						"no_batch" => $new_no_batch,
														);
											}else if($date_same == false){
															
												$data_insert_mutasi[] = array(
																						"kode_koreksi" 		=> $kode,
																						"tanggal_dibuat" 	=> $tgl,																					"dept_id"			=> $dept_id,
																						"tahun"				=> $tahun,
																						"bln"				=> $bln,
																						"no_batch"			=> $val->no_batch."(".$cek_batch->koreksi.")",
																						"status"			=> 'draft'
												);
											}
											$date_db_str = date("Y-m", strtotime("+1 month", $date_db_str));
											$date_db_str = strtotime($date_db_str);
										}
									}

									if(empty((float)$cek_batch->koreksi_qty1)){
										$qty_new = $val->qty;
									}

									if(empty((float)$cek_batch->koreksi_qty2)){
										$qty2_new = $val->qty2;
									}

									$tmp_produk_lot .= "(".$num_tmp++.") ".$val->nama_produk." ".$val->lot." ".$val->qty." ".$val->uom." ".$val->qty2." ".$val->uom2;
									$tmp_produk_lot .= " -> ".$val->nama_produk." ".$val->lot." ".$qty_new." ".$val->uom." ".$qty2_new." ".$val->uom2;
									$tmp_produk_lot .= "<br>";
								}

							}
						}

						if($koreksi_lk == 'kurang'){
							$get_total_items = $this->m_koreksi->get_data_koreksi_batch_items_by_kode($kode,$batch)->num_rows();
							if($get_total_items > 1){
                    			throw new \Exception('Data untuk mengoreksi kurang, Hanya boleh memilih 1 KP/Lot  !', 200);
							}
							foreach($get_batch_items as $val2){
								$loop_items = true;
								// cek_stock_quant
								$cek_sq = $this->_module->get_stock_quant_by_id($val2->quant_id)->row();
								$tglnow = date("Y-m-d", strtotime($tgl));
								$tgl_move = date("Y-m-d", strtotime($cek_sq->move_date));

								// acc stock move items
								$cek_acc = $this->m_koreksi->cek_acc_stock_move_items($dept_id,$type,$val2->quant_id,$val2->lot,$kode_transaksi);
								if(empty($cek_acc) AND ($tglnow != $tgl_move) ){
									$data_sm = $val2->nama_produk." ".$val2->lot;
									throw new \Exception('Data Mutasi Stock Move tidak ditemukan ! <br> '.$data_sm, 200);
								}else if((round($cek_acc->qty ?? 0,2) != round($val2->qty ?? 0,2)) AND ($tglnow != $tgl_move) ){
									$data_sm = $val2->nama_produk." ".$val2->lot." ".$cek_acc->qty." ".$cek_acc->uom." ".$cek_acc->qty2." ".$cek_acc->uom2;
									throw new \Exception('Data Qty Mutasi Stock Move tidak Sama  ! <br> '.$data_sm, 200);
								}else if((round($cek_acc->qty2 ?? 0,2) != round($val2->qty2 ?? 0,2)) AND ($tglnow != $tgl_move) ){
									$data_sm = $val2->nama_produk." ".$val2->lot." ".$cek_acc->qty." ".$cek_acc->uom." ".$cek_acc->qty2." ".$cek_acc->uom2;
									throw new \Exception('Data Qty2 Mutasi Stock Move tidak Sama  ! <br> '.$data_sm, 200);
								}else if(empty($cek_sq)){
									$data_sq = $val2->nama_produk." ".$val2->lot;
									throw new \Exception('Data Stock Quants tidak ditemukan ! <br> '.$data_sq, 200);	
								}else if($kebutuhan_qty1 <= 0 AND $kebutuhan_qty2 <= 0){
									$data_sq = $val2->nama_produk." ".$val2->lot;
									throw new \Exception('Data Qty Koreksi sudah terpenuhi. Silahkan Pilih Lot Kembali untuk memproses Koreksi ! <br> '.$data_sq, 200);							
								}else{

									$qty_sq_same = true;
									$qty2_sq_same = true;
									if(round($cek_sq->qty,2) != round($val2->qty,2)){
										$qty_sq_same = false;
									} 
									if(round($cek_sq->qty2,2) != round($val2->qty2,2)){
										$qty2_sq_same = false;
									}

									$lokasi_not_valid = false;
									if (strpos(strtoupper($cek_sq->lokasi), 'ADJ') !== false || strpos(strtoupper($cek_sq->lokasi), 'WASTE') !== false ){// adj/ waste
										$lokasi_not_valid = true;
										throw new \Exception('Lokasi Stock tidak Valid, Lokasi Stock berada di '.$cek_sq->lokasi.' !', 200);	
									}


									if(round($kebutuhan_qty1,2) > 0 || round($kebutuhan_qty2,2) > 0){
										$qty_move = $kebutuhan_qty1;
										$qty_new  = round($val2->qty,2) + round($kebutuhan_qty1,2);
										// update qty jadi 0 
										// update qty, update2 table smi
										$case  .= "when quant_id = '".$val2->quant_id."' then '".$qty_new."'";
										$where .= "'".$val2->quant_id."',"; 
										$where_move .= "'".$val2->move_id."',"; 
										$where_lot  .= "'".$val2->lot."',"; 

										if($qty_sq_same == true AND $lokasi_not_valid == false){
											$case_sq  .= "when quant_id = '".$val2->quant_id."' then '".$qty_new."'";
										}

										// update  qty, update2 table acc_smi 
										$where_dept .= "'".$cek_batch->dept_id."',";
										$where_type .= "'".$cek_batch->tipe."',";
										$where_kode_trans .= "'".$cek_batch->kode_transaksi."',";

										$kebutuhan_qty1 = 0;

										if($kebutuhan_qty2 > 0){
											$qty2_move = $kebutuhan_qty2;
											$qty2_new  = round($val2->qty2,2) + round($kebutuhan_qty2,2);
											$case2 			.= "when quant_id =  '".$val2->quant_id."' then '".$qty2_new."' ";
											$kebutuhan_qty2  = 0;

											if($qty2_sq_same == true AND $lokasi_not_valid == false){
												$case2_sq  .= "when quant_id = '".$val2->quant_id."' then '".$qty2_new."' ";
											}
										}

										//update qty move
										$tmp_update_move[]  = array(
															'row_order'	=> $val2->row_order,
															'qty_move'	=> $qty_move,
															'qty2_move'	=> $qty2_move,
										);

										$qty_move   = 0;
										$qty2_move  = 0;

										if(!empty($cek_acc->periode_th) AND !empty($cek_acc->periode_bln)){
											$date_db 	  = $cek_acc->periode_th.'-'.$cek_acc->periode_bln;
											$date_db_str  = strtotime($date_db);
											$date_now 	  = strtotime(date("Y-m", strtotime("-1 month")));

											while($date_now >= $date_db_str){

												$tahun = date("Y", $date_db_str);
												$bln = date("n", $date_db_str);
												$cek_b_km2 = $this->m_koreksi->cek_koreksi_mutasi_by_batch($kode,$dept_id,$tahun,$bln,'draft');
												$date_same = false;
												// cek tgl di array saat looping
												foreach($cek_arr_tmp as $cek_arr){
														if($cek_arr['dept_id'] == $dept_id AND $cek_arr['tahun'] == $tahun AND $cek_arr['bln'] == $bln){
																$date_same = true;
														}
												}

												$cek_arr_tmp[]        = array("dept_id"=>$dept_id, "tahun" => $tahun, "bln"	=> $bln);

														
												if(!empty($cek_b_km2)){// update
													$new_no_batch = $cek_b_km2->no_batch.",".$val2->no_batch."(".$cek_batch->koreksi.")";
													$data_update_koreksi_mutasi[] = array(
																							"id"=>$cek_b_km2->id,
																							"no_batch" => $new_no_batch,
															);
												}else if($date_same == false){
																
													$data_insert_mutasi[] = array(
																							"kode_koreksi" 		=> $kode,
																							"tanggal_dibuat" 	=> $tgl,																					"dept_id"			=> $dept_id,
																							"tahun"				=> $tahun,
																							"bln"				=> $bln,
																							"no_batch"			=> $val2->no_batch."(".$cek_batch->koreksi.")",
																							"status"			=> 'draft'
													);
												}
												$date_db_str = date("Y-m", strtotime("+1 month", $date_db_str));
												$date_db_str = strtotime($date_db_str);
											}
										}

										if(empty((float)$cek_batch->koreksi_qty1)){
											$qty_new = $val2->qty;
										}

										if(empty((float)$cek_batch->koreksi_qty2)){
											$qty2_new = $val2->qty2;
										}

										$tmp_produk_lot .= "(".$num_tmp++.") ".$val2->nama_produk." ".$val2->lot." ".$val2->qty." ".$val2->uom." ".$val2->qty2." ".$val2->uom2;
										$tmp_produk_lot .= " -> ".$val2->nama_produk." ".$val2->lot." ".$qty_new." ".$val2->uom." ".$qty2_new." ".$val2->uom2;
										$tmp_produk_lot .= "<br>";
										
									}
									
								}

							}
						}

						if(!empty($get_batch_items) AND $loop_items == true){
							if(!empty($case)){
                                $where = rtrim($where, ',');
                                $where_move = rtrim($where_move, ',');
                                $where_lot = rtrim($where_lot, ',');
                                $where_dept = rtrim($where_dept, ',');
                                $where_type = rtrim($where_type, ',');
                                $where_kode_trans = rtrim($where_kode_trans, ',');

								if(!empty($case2)){
									$qty2_query = " , qty2 = (case ".$case2." end) ";
								}else{
									$qty2_query = "";
								}

								$sql_update_qty_smi  = "UPDATE stock_move_items SET qty =(case ".$case." end) ".$qty2_query." WHERE  quant_id in (".$where.") AND move_id in (".$where_move.") AND lot IN (".$where_lot.") ";
                                $this->_module->update_perbatch($sql_update_qty_smi);
								// $this->m_koreksi->update_stock_move_items($data_update_smi);

								if($type == 'con' or $type == 'prod'){
									$where_type = " AND type IN (".$where_type.") " ;
								}else{
									$where_type = "";
								}

								$sql_update_qty_acc_smi  = "UPDATE acc_stock_move_items SET qty =(case ".$case." end)  ".$qty2_query."  WHERE  quant_id in (".$where.") AND lot IN (".$where_lot.")  ".$where_type."   AND kode_transaksi IN (".$where_kode_trans.") ";
                                $this->_module->update_perbatch($sql_update_qty_acc_smi);

								if(!empty($case_sq) or !empty($case2_sq)){

									if(!empty($case_sq)){
										$qty_sq_query = " qty = (case ".$case_sq." end), ";
									}else{
										$qty_sq_query = "";
									}

									if(!empty($case2_sq)){
										$qty2_sq_query = " qty2 = (case ".$case2_sq." end) ";
									}else{
										$qty2_sq_query = "";
									}

									if(!empty($qty_sq_query) || !empty($qty2_sq_query)){
										if(empty($qty2_sq_query)){
											$qty_sq_query = rtrim($qty_sq_query, ', ');
										}
										$sql_update_qty_stock_quant = "UPDATE stock_quant SET ".$qty_sq_query." ".$qty2_sq_query." WHERE  quant_id in (".$where.")" ;
										$this->_module->update_perbatch($sql_update_qty_stock_quant);
									}

								}
								
								// if mo con
								if($type == 'con'){
									$sql_update_qty_rm_hasil = "UPDATE mrp_production_rm_hasil SET qty =(case ".$case." end) WHERE  quant_id in (".$where.") AND kode = '".$kode_transaksi."' " ;
									$this->_module->update_perbatch($sql_update_qty_rm_hasil);
								}

								// if mo prod
								if($type == 'prod'){
									$sql_update_qty_fg_hasil = "UPDATE mrp_production_fg_hasil SET qty =(case ".$case." end) ".$qty2_query." WHERE  quant_id in (".$where.") AND kode = '".$kode_transaksi."' " ;
									$this->_module->update_perbatch($sql_update_qty_fg_hasil);
								}

								// if(!empty($case3)){

  								// 	$where3 = rtrim($where3, ',');
  								// 	$where3_date = rtrim($where3_date, ',');

								// 	if(!empty($case33)){
								// 		$qty2_sq_query = " , qty2 = (case ".$case33." end) ";
								// 	}else{
								// 		$qty2_sq_query = "";
								// 	}
								// 	$sql_update_qty_acc_stock_quant = "UPDATE acc_stock_quant_eom SET qty =(case ".$case3." end) ".$qty2_sq_query." WHERE  quant_id in (".$where3.") AND tanggal in (".$where3_date.") AND lokasi = '".$stock_location."' " ;
								// 	$this->_module->update_perbatch($sql_update_qty_acc_stock_quant);
								// }

							}

							$status_batch = 'done';

							if(!empty($data_update_koreksi_mutasi)){
								$status_batch = 'process';
								$this->m_koreksi->update_koreksi_mutasi($data_update_koreksi_mutasi);
							}


							if(!empty($tmp_update_move)){
								$this->m_koreksi->update_koreksi_batch_items($tmp_update_move,$kode,$batch);
							}

							if(!empty($data_insert_mutasi)){
								$status_batch = 'process';
								$this->m_koreksi->insert_koreksi_mutasi($data_insert_mutasi);
							}


							// update status 
							$result_updt = $this->m_koreksi->update_status_batch($kode,$batch,$status_batch);
							// if(!empty($result_updt)){
                    		// 	throw new \Exception('Gagal Koreksi, Gagal Update Status ! ', 200);
							// }
							$data_koreksi = array('status' => 'process', 'tanggal_transaksi' => date("Y-m-d H:i:s"));
							$result_updt = $this->m_koreksi->update_data_koreksi($kode,$data_koreksi);
							// if(!empty($result_updt)){
                    		// 	throw new \Exception('Gagal Koreksi, Gagal Update Status ! ', 200);
							// }

							$type   = $cek_batch->tipe; // prod, con
							$type   = $cek_batch->koreksi; //
							$tipe_koreksi_mo =  "";

							if($cek_batch->koreksi == 'mo'){
								$inisial_class = 'mO';
								if($cek_batch->tipe == 'con'){
									$tipe_koreksi_mo = "Bahan Baku";
								}else{
									$tipe_koreksi_mo = "Barang Jadi";
								}
								// jika koreksi Qty2 maka status MO jadi Ready
								if((float)$cek_batch->koreksi_qty2 > 0 ){
		                            $cek_status_mrp = $this->m_mo->cek_status_mrp_production($kode_transaksi,'')->row_array();
									if($cek_status_mrp['status'] == 'done'){
										$this->m_mo->update_status_mrp_production($kode_transaksi,'ready');
									}
								}

							}else if($cek_batch->koreksi == 'out'){
								$inisial_class = 'pengirimanbarang';
							}else{
								$inisial_class = 'penerimaanbarang';
							}

							// get kode menu 
							$mms = $this->_module->get_kode_sub_menu_deptid($inisial_class,$dept_id)->row_array();
							if(!empty($mms['kode'])){
								$mms_kode = $mms['kode'];
							}else{
								$mms_kode = '';
							}

							 //create log history pengiriman_barang
                            $note_log_d .= "Dokumen Ini telah di Koreksi Melalui Proses Mundur ".$kode.'<br>';
							$note_log_d .= "Koreksi ".$tipe_koreksi_mo." <br>";
							$note_log_d .= $tmp_produk_lot;
                            $insert_log[] = array(
                                              'datelog'   => date('Y-m-d H:i:s'),
                                              'main_menu_sub_kode'    => $mms_kode,
                                              'kode'                  => $kode_transaksi ?? '',
                                              'jenis_log'             => 'edit',
                                              'note'                  => $note_log_d,
                                              'nama_user'             => $nama_user ?? '',
                                              'ip_address'            => $ip);

							//create log kode di MO/IN/OUT
							if(!empty($insert_log)){
								$this->_module->simpan_log_history_batch_2($insert_log);
							}

							$jenis_log = "edit";
							$note_log  = $kode. " | Koreksi batch ".$batch;
							$data_history = array(
												'datelog'   => date("Y-m-d H:i:s"),
												'kode'      => $kode,
												'jenis_log' => $jenis_log,
												'note'      => $note_log  );
							$this->_module->gen_history_ip($sub_menu,$username,$data_history);
								

							$callback = array('message' => 'Data Berhasil di Koreksi ', 'icon' => 'fa fa-check', 'type' => 'success', );

						}else{
                    		throw new \Exception('Koreksi Gagal. Data Batch Items Kosong / tidak valid !', 200);
						}
					}

				}

				if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal Menyimpan Data', 500);
                }

				$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
			}
			
		}catch(Exception $ex){
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
	}


	public function proses_mutasi()
	{
		try {

			if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
			} else {
					
				$sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 

				$kode   = $this->input->post('kode');
                $id  	= $this->input->post('id');

				$tgl           = date('Y-m-d H:i:s'); 

				// // start transaction
                // $this->_module->startTransaction();

				// // lock table
                // $this->_module->lock_tabel("stock_quant WRITE,koreksi_mundur WRITE, stock_move_items WRITE, acc_stock_move_items WRITE, acc_stock_quant_eom WRITE, koreksi_mundur_batch as kmb WRITE, koreksi_mundur_batch_items as a WRITE, koreksi_mundur_batch_items as b WRITE,  koreksi_mundur_batch_items WRITE, koreksi_mutasi WRITE, user WRITE ,main_menu_sub WRITE,log_history WRITE, departemen as d WRITE, koreksi_mundur_batch as km WRITE, koreksi_mundur_batch_items as kmbs WRITE, koreksi_mundur_batch as kmbc WRITE, koreksi_mundur_batch_items as kmbic WRITE, koreksi_mutasi as kmc WRITE ");

				$koreksi = $this->m_koreksi->get_data_koreksi_by_kode($kode);
				if(empty($koreksi)){
                    throw new \Exception('Data Koreksi tidak ditemukan !', 200);
				}else if($koreksi->status == 'done'){
                    throw new \Exception('Maaf, Data Tidak Bisa diKoreksi, Status Sudah Done !', 200);
				}else if($koreksi->status == 'cancel'){
                    throw new \Exception('Maaf, Data Tidak Bisa diKoreksi, Status Cancel !', 200);
				}else{

					// cek status batch
					$cek_proses_mutasi = $this->m_koreksi->cek_proses_mutasi_by_kode($kode,$id);
					if(empty($cek_proses_mutasi)){
                    	throw new \Exception('Data Proses Mutasi tidak ditemukan !', 200);
					}else if($cek_proses_mutasi->status == 'done'){
                    	throw new \Exception('Maaf, Data Tidak Bisa diKoreksi, Status  Sudah Done !', 200);
					}else if($cek_proses_mutasi->status == 'cancel'){
                    	throw new \Exception('Maaf, Data Tidak Bisa diKoreksi, Status Batch Sudah Cancel !', 200);
					}else{	

						$dept_id 	= $cek_proses_mutasi->dept_id;
						$thn 		= $cek_proses_mutasi->tahun;
						$bln 		= $cek_proses_mutasi->bln;
						$no_batch 	= $cek_proses_mutasi->no_batch;
						
						$cek_dept = $this->cek_dept_mutasi($dept_id);
						if($cek_dept == false){
							$get_dept  = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
                    		throw new \Exception("Departemen ".$get_dept['nama']." belum bisa melakukan Koreksi Mundur", 200);
						}

						if($dept_id == 'DYE'){
							$get_dept  = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
                    		throw new \Exception("Departemen ".$get_dept['nama']." belum bisa melakukan Koreksi Mundur", 200);
						}
						
						// log_message('info', 'tes');

						$periode_koreksi = $thn."-".$bln;
						$dateKoreksi     = date('Y-m-t', strtotime($periode_koreksi));

						$date_now    = new DateTime("now");
						$periode_koreksi = new DateTime($dateKoreksi);
						$interval 	= $date_now->diff($periode_koreksi);
						$jml_hari   = $interval->format('%a');
						$currDate_jml = "(CURDATE(),".$jml_hari.")";

						$get_dept  = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
						$stock_location = $get_dept['stock_location'];


						//ubah saldo awal bulan / eom $bln
						$var_ex = explode(',',$no_batch); // example :  1000/90/42/01(mo),1000/90/42/02(out)
						$tmp_lot= "";
						$tmp_koreksi_lk= "";
						$loop   = 1;
						foreach($var_ex as $val ){
							// update status 
							$var2 = explode('(',$val); // example :  1000/90/42/01(mo)
							$no_batch_var = $var2[0] ?? '';

							// $result_updt = $this->m_koreksi->update_status_batch($kode,$var2[0] ?? '','done');
							$get_dbi = $this->m_koreksi->get_data_koreksi_batch_items_by_kode_group($kode,$no_batch_var);
							$kode_produk = "";
							$nama_produk = "";
							$lot    	 = "";
							$qty         = 0;
							$qty2        = 0;
							$koreksi_lebih_kurang = "";
							$status_km  = "";
							foreach($get_dbi as $gd){
									$kode_produk = $gd->kode_produk;
									$nama_produk = $gd->nama_produk;
									$lot		 = $gd->lot;
									$qty         = $qty + $gd->tot_qty_move;
									$qty2        = $qty2 + $gd->tot_qty2_move;
									$koreksi_lebih_kurang = $gd->koreksi_lebih_kurang;

									$cek_status = $this->m_koreksi->cek_lot_update_eom($dept_id,$lot,$thn,$bln,$kode);
									if(!empty($cek_status)){
										$status_km = $cek_status->status;
									}
							}
							// log_message('info', $get_dbi);
							// var_dump($get_dbi);

							if(!empty($get_dbi)  AND (($loop == 1  AND  $tmp_lot == "") OR ($loop > 1 AND $tmp_lot != $lot )) AND $status_km == 'draft'){
								$get_eom   = $this->m_koreksi->get_data_eom_by_produk($thn,$bln,$kode_produk,$nama_produk,$lot,$stock_location);
								$koreksi_qty1 = $qty;
								$koreksi_qty2 = $qty2;
								$case         = "";
								$where        = "";
								$case2         = "";
								$where        = "";
								$where_tgl        = "";
								foreach($get_eom as $eom){

									if($koreksi_lebih_kurang == 'lebih'){

										if(round($eom->qty,2) >= round($koreksi_qty1,2)){
											$qty_new  = round($eom->qty,2) - round($koreksi_qty1,2);
											$koreksi_qty1 = 0;
											$case  .= "when quant_id =  '".$eom->quant_id."' then '".$qty_new."' ";
											$where .= "'".$eom->quant_id."',"; 
											$where_tgl .= "'".$eom->tanggal."',"; 
										}else if(round($eom->qty,2) < round($koreksi_qty1,2)){
											$qty_new  = 0;
											$koreksi_qty1 = round($koreksi_qty1,2) - round($eom->qty,2);
											$case  .= "when quant_id =  '".$eom->quant_id."' then '".$qty_new."' ";
											$where .= "'".$eom->quant_id."',"; 
											
											$where_tgl .= "'".$eom->tanggal."',"; 
										}

										if(round($eom->qty2,2) >= round($koreksi_qty2,2)){
											$qty2_new  = round($eom->qty2,2) - round($koreksi_qty2,2);
											$koreksi_qty2 = 0;
											$case2  .= "when quant_id =  '".$eom->quant_id."' then '".$qty2_new."' ";
											$where .= "'".$eom->quant_id."',"; 
											$where_tgl .= "'".$eom->tanggal."',"; 
										}else if(round($eom->qty2,2) < round($koreksi_qty2,2)){
											$qty2_new  = 0; 
											$koreksi_qty2 = round($koreksi_qty2,2) - round($eom->qty2,2);
											$case2  .= "when quant_id =  '".$eom->quant_id."' then '".$qty2_new."' ";
											$where .= "'".$eom->quant_id."',"; 
											$where_tgl .= "'".$eom->tanggal."',"; 
										}
									}

									if($koreksi_lebih_kurang == 'kurang'){
										if(round($koreksi_qty1,2) > 0 || round($koreksi_qty2, 2) > 0 ){
											$qty_new  = round($eom->qty,2) + round($koreksi_qty1,2);
											$koreksi_qty1 = 0;
											$case  .= "when quant_id =  '".$eom->quant_id."' then '".$qty_new."' ";
											$where .= "'".$eom->quant_id."',"; 
											$where_tgl .= "'".$eom->tanggal."',"; 

											if(round($koreksi_qty2, 2) > 0){
												$qty2_new  = round($eom->qty2,2) + round($koreksi_qty2,2);
												$koreksi_qty2 = 0;
												$case2  .= "when quant_id =  '".$eom->quant_id."' then '".$qty2_new."' ";
											}
										}
									}

									if(empty(round($koreksi_qty1,2)) AND empty(round($koreksi_qty2,2))){
										break;
									}
								}

								if(!empty($case)){
									$where = rtrim($where, ',');
									$where_tgl = rtrim($where_tgl, ',');

									if(!empty($case2)){
										$qty2_query = " , qty2 = (case ".$case2." end) ";
									}else{
										$qty2_query = "";
									}

									$sql_update_eom  = "UPDATE acc_stock_quant_eom SET qty =(case ".$case." end) ".$qty2_query." WHERE  quant_id in (".$where.") AND tanggal in (".$where_tgl.") AND lot IN ('".$lot."') ";
									$this->_module->update_perbatch($sql_update_eom);
									// $this->m_koreksi->update_stock_move_items($data_update_smi);
								}
							}

							$tmp_lot = $lot;
							$tmp_koreksi_lk= $koreksi_lebih_kurang;
							$loop++;

						}
			            // $this->_module->unlock_tabel();

						for($loop=0;$loop<=4;$loop++){	

							$get_dept  = $this->_module->get_nama_dept_by_kode($dept_id);
                			$type_dept = $get_dept->row_array();

							// mutasi detail
							if(($type_dept['type_dept'] == 'manufaktur' or $dept_id == 'GJD' or $dept_id == 'INS2' ) AND ($loop == 0)){


								if($dept_id == 'FIN' or $dept_id == 'DYE' or $dept_id == 'INS2'){
									// detail
									// produk / polos
									// global
									// if($dept_id == 'DYE'){
									// 	$table  = 'acc_mutasi_'.strtolower($dept_id).'_rm_detail.sh';
									// 	$this->change_variable_and_run($table,$currDate_jml);
									// 	// $table2 = 'acc_mutasi_'.strtolower($dept_id).'_fg_detail.sh';
									// }
								}else{	
										
									$table  = 'acc_mutasi_'.strtolower($dept_id).'_rm_detail.sh';
									$this->change_variable_and_run($table,$currDate_jml);
									$table2 = 'acc_mutasi_'.strtolower($dept_id).'_fg_detail.sh';
									$this->change_variable_and_run($table2,$currDate_jml);

								}

							}else if($type_dept['type_dept'] == 'gudang' AND ($loop == 0)){
								$table = 'acc_mutasi_'.strtolower($dept_id).'_detail.sh';
								$this->change_variable_and_run($table,$currDate_jml);
							}

							// detail datar
							$cek_dd = $this->cek_dept_mutasi_detail_produk_datar($dept_id);
							if($cek_dd == true AND $loop == 1){
								$table       = 'acc_mutasi_'.strtolower($dept_id).'_detail_datar.sh';
								$this->change_variable_and_run($table,$currDate_jml);
							}

							// mutasi produk / polos
							if(($type_dept['type_dept'] == 'manufaktur' or $dept_id == 'GJD') AND ($loop == 2)){

								$table  = 'acc_mutasi_'.strtolower($dept_id).'_rm.sh';
								$this->change_variable_and_run($table,$currDate_jml);
								$table2 = 'acc_mutasi_'.strtolower($dept_id).'_fg.sh';
								$this->change_variable_and_run($table2,$currDate_jml);

							}else if($type_dept['type_dept'] == 'gudang' AND ($loop == 2)){
								$table = 'acc_mutasi_'.strtolower($dept_id).'.sh';
								$this->change_variable_and_run($table,$currDate_jml);
							}


							// global
							if(($type_dept['type_dept'] == 'manufaktur' or $dept_id == 'GJD' ) AND ($loop == 3)){

								$table  = 'acc_mutasi_'.strtolower($dept_id).'_rm_global.sh';
								$this->change_variable_and_run($table,$currDate_jml);
								$table2 = 'acc_mutasi_'.strtolower($dept_id).'_fg_global.sh';
								$this->change_variable_and_run($table2,$currDate_jml);

							}else if($type_dept['type_dept'] == 'gudang' AND ($loop == 3)){
								$table = 'acc_mutasi_'.strtolower($dept_id).'_global.sh';
								$this->change_variable_and_run($table,$currDate_jml);
							}
						
						}

						$var_ex = explode(',',$no_batch);
						foreach($var_ex as $val ){
							// update status 
							$var2 = explode('(',$val);
							$result_updt = $this->m_koreksi->update_status_batch($kode,$var2[0] ?? '','done');
						}

						// update koreksi mutasi
						$data_koreksi = array('status' => 'done', 'tanggal_proses_mutasi' => date("Y-m-d H:i:s"),'curr_date'=>$jml_hari);
						$result_updt = $this->m_koreksi->update_data_koreksi_mutasi($kode,$id,$data_koreksi);
						// if(!empty($result_updt)){
                    	// 	throw new \Exception('Gagal Koreksi, Gagal Update Status ! ', 200);
						// }

						// update koreksi
						$note_log_m   = "Tahun=".$thn." Bulan=".$bln." Batch=".$no_batch;
						$jenis_log    = "edit";
						$note_log     = "Proses Mutasi Batch : <br>".$note_log_m;
						$data_history = array(
												'datelog'   => date("Y-m-d H:i:s"),
												'kode'      => $kode,
												'jenis_log' => $jenis_log,
												'note'      => $note_log  );
						$this->_module->gen_history_ip($sub_menu,$username,$data_history);

						$callback = array('message' => 'Proses Mutasi Berhasil di Lakukan', 'icon' => 'fa fa-check', 'type' => 'success', );
					} 

				}

				// if (!$this->_module->finishTransaction()) {
                //     throw new \Exception('Gagal Menyimpan Data', 500);
                // }

				$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));
			}
		}catch(Exception $ex){
            // $this->_module->finishRollBack();
            // $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally {
            // unlock table
            // $this->_module->unlock_tabel();
        }
	}

	function change_variable_and_run($table_name, $currDate_jml)
	{

		$default = '$replace_currDate';
		$parse   = $currDate_jml;

		$fname = "script/".$table_name;
		$fhandle = fopen($fname,"r");
		$content = fread($fhandle,filesize($fname));
		$content = str_replace($default, $parse, $content);

		$fhandle = fopen($fname,"w");
		fwrite($fhandle,$content);
		fclose($fhandle);

		$output = shell_exec('sh '.$fname); 


		$fhandle = fopen($fname,"r");
		$content = fread($fhandle,filesize($fname));
		$content = str_replace($parse, $default, $content);

		$fhandle = fopen($fname,"w");
		fwrite($fhandle,$content);
		fclose($fhandle);

		// sleep(10);

		return;
	}


	function cek_dept_mutasi($dept)
    {
        $list_dept_mutasi = array('WRD','TWS','WRP','TRI','JAC','CS','INS1','GRG', 'DYE');
        $dept_status      = false;
        foreach($list_dept_mutasi as $list){
            if($list == $dept){
                $dept_status = true;
                break;
            }
        }
        return $dept_status;
    }

	function cek_dept_mutasi_detail_produk_datar($dept)
    {
        $list_dept_mutasi = array('GRG');
        $dept_status      = false;
        foreach($list_dept_mutasi as $list){
            if($list == $dept){
                $dept_status = true;
                break;
            }
        }
        return $dept_status;
    }


	function done_koreksi_mundur() 
	{
		try {

			if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
			} else {
				
				$sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 

				$kode   = $this->input->post('kode');
				$tgl    = date('Y-m-d H:i:s'); 

				// start transaction
                $this->_module->startTransaction();

				// lock table
				$this->_module->lock_tabel("koreksi_mundur WRITE, koreksi_mundur_batch WRITE, koreksi_mundur_batch_items WRITE, koreksi_mutasi WRITE, user WRITE ,main_menu_sub WRITE,log_history WRITE");

				$koreksi = $this->m_koreksi->get_data_koreksi_by_kode($kode);
				if(empty($koreksi)){
                    throw new \Exception('Data Koreksi tidak ditemukan !', 200);
				}else if($koreksi->status == 'done'){
                    throw new \Exception('Maaf, Data Tidak Bisa di done kan , Status Sudah Done !', 200);
				}else if($koreksi->status == 'cancel'){
                    throw new \Exception('Maaf, Data Tidak Bisa di done kan, Status Cancel !', 200);
				}else{

					// cek batch
					$cek_batch = $this->m_koreksi->get_koreksi_batch_by_kode($kode,'');

					$cek_batch2 = $this->m_koreksi->get_koreksi_batch_by_kode($kode,'draft');

					// cek status koreksi_mutasi
					$cek_km = $this->m_koreksi->get_koreksi_mutasi_by_kode($kode,'draft');

					if(empty($cek_batch)){
                    	throw new \Exception('Data yang akan dikoreksi masih kosong !', 200);
					}else if(!empty($cek_batch2)){
                    	throw new \Exception('Masih Terdapat Data yang akan dikoreksi status nya Draft !', 200);
					}else if(!empty($cek_km)){
                    	throw new \Exception('Koreksi Mutasi belum selesai. Masih terdapat Status Draft !', 200);
					}else{
						$data_koreksi = array('status' => 'done', 'tanggal_transaksi' => date("Y-m-d H:i:s"));
						$result_updt = $this->m_koreksi->update_data_koreksi($kode,$data_koreksi);
						// update koreksi
						$jenis_log    = "edit";
						$note_log     = "Done Koreksi Mundur";
						$data_history = array(
													'datelog'   => date("Y-m-d H:i:s"),
													'kode'      => $kode,
													'jenis_log' => $jenis_log,
													'note'      => $note_log  );
						$this->_module->gen_history_ip($sub_menu,$username,$data_history);
	
						$callback = array('message' => 'Koresi Mundur Berhasil di Selesaikan / Done', 'icon' => 'fa fa-check', 'type' => 'success', );
					}

					
				}


				if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal Menyimpan Data', 500);
                }

				$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

			}
		
		}catch(Exception $ex){
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
	}


	function cancel_koreksi_mundur() 
	{
		try {

			if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new \Exception('Waktu Anda Telah Habis', 401);
			} else {
				
				$sub_menu  = $this->uri->segment(2);
                $username = addslashes($this->session->userdata('username')); 

				$kode   = $this->input->post('kode');
				$tgl    = date('Y-m-d H:i:s'); 

				// start transaction
                $this->_module->startTransaction();

				// lock table
				$this->_module->lock_tabel("koreksi_mundur WRITE, koreksi_mundur_batch WRITE, koreksi_mundur_batch_items WRITE, koreksi_mutasi WRITE, user WRITE ,main_menu_sub WRITE,log_history WRITE");

				$koreksi = $this->m_koreksi->get_data_koreksi_by_kode($kode);
				if(empty($koreksi)){
                    throw new \Exception('Data Koreksi tidak ditemukan !', 200);
				}else if($koreksi->status == 'done'){
                    throw new \Exception('Maaf, Data Tidak Bisa dibatalkan, Status Sudah Done !', 200);
				}else if($koreksi->status == 'cancel'){
                    throw new \Exception('Maaf, Data Tidak Bisa dibatalkan, Status Cancel !', 200);
				}else if($koreksi->status == 'process'){
                    throw new \Exception('Maaf, Data Tidak Bisa dibatalkan, Status Process !', 200);
				}else{

					// cek batch
					$cek_batch = $this->m_koreksi->get_koreksi_batch_by_kode($kode,'draft');

					// cek status koreksi_mutasi
					$cek_km = $this->m_koreksi->get_koreksi_mutasi_by_kode($kode,'draft');

					if(!empty($cek_batch)){
                    	throw new \Exception('Masih Terdapat Data yang akan dikoreksi status nya Draft !', 200);
					}else if(!empty($cek_km)){
                    	throw new \Exception('Koreksi Mutasi belum selesai. Masih terdapat Status Draft !', 200);
					}else{
						$data_koreksi = array('status' => 'cancel', 'tanggal_transaksi' => date("Y-m-d H:i:s"));
						$result_updt = $this->m_koreksi->update_data_koreksi($kode,$data_koreksi);
						// update koreksi
						$jenis_log    = "cancel";
						$note_log     = "Batal Koreksi Mundur";
						$data_history = array(
													'datelog'   => date("Y-m-d H:i:s"),
													'kode'      => $kode,
													'jenis_log' => $jenis_log,
													'note'      => $note_log  );
						$this->_module->gen_history_ip($sub_menu,$username,$data_history);
	
						$callback = array('message' => 'Koresi Mundur Berhasil di Batalkan / Cancel', 'icon' => 'fa fa-check', 'type' => 'success', );
					}

					
				}


				if (!$this->_module->finishTransaction()) {
                    throw new \Exception('Gagal Menyimpan Data', 500);
                }

				$this->output->set_status_header(200)->set_content_type('application/json', 'utf-8')->set_output(json_encode($callback));

			}
		
		}catch(Exception $ex){
            $this->_module->finishRollBack();
            $this->_module->rollbackTransaction();
            $this->output->set_status_header($ex->getCode() ?? 500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }finally {
            // unlock table
            $this->_module->unlock_tabel();
        }
	}
	
}