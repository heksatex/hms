<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/**
 * 
 */
class Billofmaterials extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("_module");//load model global
		$this->load->model("m_bom");
	}

	
	public function index()
	{
		$data['id_dept'] ='BOM';
        $this->load->view('ppic/v_bill_of_materials',$data);
	}


	public function get_data()
	{
		if(isset($_POST['start']) && isset($_POST['draw'])){
			$list = $this->m_bom->get_datatables();
			$data = array();
			$no = $_POST['start'];
			foreach ($list as $field) {
				$kode_encrypt = $this->encryption->encrypt($field->kode_bom);
				$kode_encrypt = encrypt_url($field->kode_bom);
				$no++;
				$row = array();
				$row[] = $no;
				$row[] = '<a href="'.base_url('ppic/billofmaterials/edit/'.$kode_encrypt).'">'.$field->kode_bom.'</a>';
				$row[] = $field->tanggal;
				$row[] = $field->nama_bom;
				$row[] = $field->kode_produk;
				$row[] = $field->nama_produk;
				$row[] = $field->qty;
				$row[] = $field->uom;
				$row[] = $field->qty2;
				$row[] = $field->uom2;
				$row[] = $field->nama_status;
				$data[] = $row;
			}
	
			$output = array(
				"draw" => $_POST['draw'],
				"recordsTotal" => $this->m_bom->count_all(),
				"recordsFiltered" => $this->m_bom->count_filtered(),
				"data" => $data,
			);
			//output dalam format JSON
			echo json_encode($output);
		}else{
            die();
        }
	}

	public function add()
    {
        $data['id_dept']  ='BOM';
        $data['list_uom'] = $this->_module->get_list_uom();
        return $this->load->view('ppic/v_bill_of_materials_add', $data);
    }

    public function edit($id = null)
    {   
        if(!isset($id)) show_404();
        $kode_decrypt      = decrypt_url($id);
        $data['id_dept']   ='BOM';
        $data["head"]      = $this->m_bom->get_list_bom($kode_decrypt);
        $data["items"]     = $this->m_bom->get_list_bom_items($kode_decrypt);
        //$data['list_uom'] = $this->_module->get_list_uom();

        if(empty($data["head"])){
          show_404();
        }else{
          return $this->load->view('ppic/v_bill_of_materials_edit',$data);
        }
    }

	function view_tab_body()
	{   
		  $kode_bom  = $this->input->post('kode_bom');
		  $data["items"]     = $this->m_bom->get_list_bom_items($kode_bom);
		  return $this->load->view('ppic/v_bill_of_materials_edit_tab', $data);
	}


    public function get_produk_bom_select2()
    {
	    $prod = addslashes($this->input->post('prod'));
   		$callback = $this->m_bom->get_list_produk_select2_by_prod($prod);
        echo json_encode($callback);
    }

    public function get_uom_select2()
    {
	    $prod = addslashes($this->input->post('prod'));
   		$callback = $this->m_bom->get_list_uom_select2_by_prod($prod);
        echo json_encode($callback);
    }


    public function get_prod_by_id()
    {
	    $kode_produk = addslashes($this->input->post('kode_produk'));
   		$result      = $this->m_bom->get_produk_by_kode($kode_produk)->row_array();
        $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'], 'qty' => '1000', 'uom2'=>$result['uom_2']);
        echo json_encode($callback);
        
    }

	public function get_bom_items_for_edit()
  	{
        $kode_bom  = $this->input->post('kode_bom');
        $items     = $this->m_bom->get_list_bom_items($kode_bom);
        
        $callback = array('status' => 'success', 'record1' => $items,);

        echo json_encode($callback);
	} 


    public function simpan()
    {

    	if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    	}else{

    		$username  = addslashes($this->session->userdata('username')); 
    		$sub_menu  = $this->uri->segment(2);

    		$kode_bom  = addslashes($this->input->post('kode_bom'));
    		$nama_bom  = addslashes($this->input->post('nama_bom'));
    		$kode_produk = addslashes($this->input->post('kode_produk'));
    		$nama_produk = addslashes($this->input->post('nama_produk'));
    		$qty         = $this->input->post('qty');
    		$uom         = $this->input->post('uom');
			$qty2        = $this->input->post('qty2');
    		$uom2        = $this->input->post('uom2');
    		$status      = $this->input->post('status');
			$tanggal     = date('Y-m-d H:i:s');

			// get_nama_category_by_kode_produk
			$get_cat = $this->m_bom->get_nama_category_by_kode_produk($kode_produk)->row_array();

    		if(empty($nama_bom)){
                $callback = array('status' => 'failed', 'field' => 'nama_bom', 'message' => 'Nama BOM Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    
          	}else if(empty($nama_produk) OR empty($kode_produk)){
          		 $callback = array('status' => 'failed', 'field' => 'nama_produk', 'message' => 'Nama Produk Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
          	}else if(empty((double)$qty)){
          		$callback = array('status' => 'failed', 'field' => 'qty', 'message' => 'Qty Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
          	}else if(empty($uom)){
          		$callback = array('status' => 'failed', 'field' => 'sel2_uom2', 'message' => 'Uom Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
			}else if(empty((double)$qty2) AND (strpos($get_cat['nama_category'], "Tricot") !== FALSE or strpos($get_cat['nama_category'],"Jacquard") !== FALSE) ){
				$callback = array('status' => 'failed', 'field' => 'qty2', 'message' => 'Qty2 Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
			}else if(empty($uom2) AND (strpos($get_cat['nama_category'], "Tricot") !== FALSE or strpos($get_cat['nama_category'],"Jacquard") !== FALSE) ){
				$callback = array('status' => 'failed', 'field' => 'sel2_uom2', 'message' => 'Uom2 Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
          	}else{


          		if(empty($kode_bom)){//jika kode bom kosong, maka simpan data

          			//cek apa nama bom sudah pernah diinput  by kode_produk ?
          			$cek = $this->m_bom->cek_bom_by_kode_produk($kode_produk,$nama_bom)->row_array();

          			if(!empty($cek['kode_bom'])){//jika sudah ada produk bom

          				$callback = array('status' => 'failed', 'message' => 'Maaf, Nama Bom Sudah Pernah diinput', 'icon' =>'fa fa-warning', 'type' => 'danger' );    

          			}else{// insert data

	          			//lock table
	          			$this->_module->lock_tabel('bom WRITE');

	          			//get last number kode_bom
	          			$last_bom   = $this->_module->get_kode_bom();
	          			$kode_bom   = 'BM'.$last_bom;
	          			$this->m_bom->save_bom($kode_bom,$tanggal,$nama_bom,$kode_produk,$nama_produk,$qty,$uom,$qty2,$uom2);

	          			//unlock table
    	            	$this->_module->unlock_tabel();

	                   	$kode_encrypt    = encrypt_url($kode_bom);
	          			$jenis_log = "create";
		                $note_log  = $kode_bom." | ".$nama_bom." | ".$kode_produk." | ".$nama_produk." | ".$qty." | ".$uom." | ".$qty2." | ".$uom2;
		                $this->_module->gen_history($sub_menu, $kode_bom, $jenis_log, $note_log, $username);
		                $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success', 'kode_encrypt' => $kode_encrypt, 'isi' => $kode_bom);
          			}


          		}else{//update data

					$sql_bom_items  = '';
					$note_items     = '';
					$note_items_before = '';
					$row  			= 1;
					$kg_bom_head    = 0;
					// cek kg bom header
					// $get_kg_bom = $this->m_bom->get_kg_bom_by_kode($kode_bom)->row_array();
					
					$array_item    = json_decode($this->input->post('arr_item'),true); 
					$empty_items   = true;
					$kg_bom_same   = true;
					$total_kg_items = 0;
					foreach($array_item as $items){  
						$empty_items = false;
						// cek KG an
						if((strpos($get_cat['nama_category'], "Tricot") !== FALSE or strpos($get_cat['nama_category'],"Jacquard") !== FALSE)){
							if($items['uom'] == 'Kg'){
								$total_kg_items  = $total_kg_items + $items['qty']; 
							}
							
							if($items['uom2'] == 'Kg'){
								$total_kg_items  = $total_kg_items + $items['qty2']; 
							}
						}

						$sql_bom_items .= "('".$kode_bom."','".addslashes($items['kode_produk'])."','".addslashes($items['nama_produk'])."','".$items['qty']."','".addslashes($items['uom'])."','".$items['qty2']."','".addslashes($items['uom2'])."','".addslashes($items['reff_note'])."', '".$row."'), ";

						$note_items .= '('.$row.') '.addslashes($items['kode_produk']).' '.addslashes($items['nama_produk']).' '.$items['qty'].'  '.$items['uom'].' '.$items['qty2'].'  '.$items['uom2'].' '.addslashes($items['reff_note']). '<br> ';

						$row++;
					}

					if((strpos($get_cat['nama_category'], "Tricot") !== FALSE or strpos($get_cat['nama_category'],"Jacquard") !== FALSE)){
						if($uom2 == 'Kg' ){
							$kg_bom_head  = $qty2;
						}

						if(round($kg_bom_head,2) != round($total_kg_items,2) AND $empty_items == false){
							$kg_bom_same   = false;
						}
					}

					// if($empty_items == true){
          			// 	$this->m_bom->update_bom($kode_bom,$nama_bom,$kode_produk,$nama_produk,$qty,$uom,$qty2,$uom2,$status);
					// }

					if($kg_bom_same == false AND (strpos($get_cat['nama_category'], "Tricot") !== FALSE or strpos($get_cat['nama_category'],"Jacquard") !== FALSE) ){
						$callback = array('status' => 'failed','field' => 'qty2', 'message' => 'Maaf, Qty (kg) dan Qty (kg) items Harus Sama !', 'icon' =>'fa fa-warning', 'type' => 'danger');    
					}else{

						// note before
						$head_before = $this->m_bom->get_list_bom($kode_bom);
						$status_bom_head_before = $this->_module->get_mst_status_by_kode($head_before->status_bom);

						$note_head_before = $head_before->kode_bom." | ".$head_before->nama_bom." | ".$head_before->kode_produk." | ".$head_before->nama_produk." | ".$head_before->qty." | ".$head_before->uom." | ".$head_before->qty2." | ".$head_before->uom2." | ".$status_bom_head_before;

						$items_before= $this->m_bom->get_list_bom_items($kode_bom);
						$num         = 1;
						foreach($items_before as $ib){
							$note_items_before .= '('.$num.') '.addslashes($ib->kode_produk).' '.addslashes($ib->nama_produk).' '.$ib->qty.'  '.$ib->uom.' '.$ib->qty2.'  '.$ib->uom2.' '.addslashes($ib->note). '<br> ';
							$num++;
						}

						// delete bom items
						$this->m_bom->delete_bom_items_all_by_kode($kode_bom);

						$this->m_bom->update_bom($kode_bom,$nama_bom,$kode_produk,$nama_produk,$qty,$uom,$qty2,$uom2,$status);

						if(!empty($sql_bom_items)){
							$sql_bom_items = rtrim($sql_bom_items, ', ');
							$this->m_bom->simpan_bom_items_batch($sql_bom_items);               
							
						}
						//get status aktif by kode f/t
						$status_bom = $this->_module->get_mst_status_by_kode($status);

						$jenis_log = "edit";
						$note_log  = $note_head_before." <br> ".$note_items_before." -> <br>". $kode_bom." | ".$nama_bom." | ".$kode_produk." | ".$nama_produk." | ".$qty." | ".$uom." | ".$qty2." | ".$uom2." | ".$status_bom."<br> ".$note_items;
						$this->_module->gen_history($sub_menu, $kode_bom, $jenis_log, $note_log, $username);
						$callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');
					}

          		}

          	}

    	}

    	echo json_encode($callback);
    }

    public function simpan_bom_items()
    {

    	if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    	}else{

    		$username  = addslashes($this->session->userdata('username')); 
    		$sub_menu  = $this->uri->segment(2);

    		$kode_bom    = addslashes($this->input->post('kode'));
    		$kode_produk = addslashes($this->input->post('kode_produk'));
    		$nama_produk = addslashes($this->input->post('nama_produk'));
    		$qty         = $this->input->post('qty');
    		$uom         = addslashes($this->input->post('uom'));
    		$note        = addslashes($this->input->post('note'));
    		$ro          = $this->input->post('row_order');
    		$row         = explode('^|',$ro);
    		$row_order   = $row[0];
               

    		if(empty($kode_produk) or empty($nama_produk)){
    			$callback = array('status' => 'failed', 'message' => 'Nama Produk Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
    		}else if(empty($qty)){
    			$callback = array('status' => 'failed', 'message' => 'Qty Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
    		}else if(empty($uom)){
    			$callback = array('status' => 'failed', 'message' => 'Uom Produk Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
    		}else{

    			if(empty($row_order)){ //simpan data 

    				//get last row_order_by kode_bom
    				$last_row = $this->m_bom->get_last_row_order_bom_items_by_kode($kode_bom);

    				$this->m_bom->save_bom_items($kode_bom,$kode_produk,$nama_produk,$qty,$uom,$note,$last_row);

    				$jenis_log = "create";
		            $note_log  =  "Tambah data Details | ".$kode_bom." | ".$kode_produk." | ".$nama_produk." | ".$qty." | ".$uom." | ".$note." | ".$last_row;
		            $this->_module->gen_history($sub_menu, $kode_bom, $jenis_log, $note_log, $username);
		            $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');
		            
    				//$callback = array('status' => 'success', 'message' => $kode_bom." | ".$kode_produk." | ".$nama_produk." | ".$qty." | ".$uom." | ".$note." | ".$row_order, 'icon' =>'fa fa-check', 'type' => 'success');

    			}else{ // update data

                	$this->m_bom->update_bom_items($kode_bom,$kode_produk,$nama_produk,$qty,$uom,$note,$row_order);

                	$jenis_log = "edit";
			        $note_log  = "Edit data Details | ".$kode_bom." | ".$kode_produk." | ".$nama_produk." | ".$qty." | ".$uom." | ".$note." | ".$row_order;
			        $this->_module->gen_history($sub_menu, $kode_bom, $jenis_log, $note_log, $username);

			        $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');

    			}

    		}
    	}

    	echo json_encode($callback);
    }


	public function hapus_bom_items()
    {

    	if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    	}else{

    		$username  = addslashes($this->session->userdata('username')); 
    		$sub_menu  = $this->uri->segment(2);

    		$kode_bom = $this->input->post('kode');
    		$ro  = addslashes($this->input->post('row_order'));
    		$row = explode('^|',$ro);
    		$row_order   = $row[0];
    		$kode_produk = $row[1];
    		$nama_produk = $row[2];
    		$qty = $row[3];
    		$uom = $row[4];

    		//cek produk serta row order yg sama 
            $cek_produk = $this->m_bom->cek_bom_items_by_row($kode_bom,$kode_produk,$row_order)->row_array(); 

            if(empty($kode_bom) or empty($ro) ){
          		$callback = array('status' => 'success','message' => 'Data Gagal Dihapus !', 'icon' =>'fa fa-warning', 'type' => 'danger');
        	
            }else if(empty($cek_produk['kode_produk'])){
                $callback = array('status' => 'failed','message' => 'Maaf, Produk Kosong  atau sudah dihapus !', 'icon' =>'fa fa-check', 'type' => 'danger');

            }else{

            	$this->m_bom->delete_bom_items($kode_bom,$kode_produk,$row_order);
            	$jenis_log   = "cancel";
                $note_log    = "Hapus data Details | ".$kode_bom." | ".$kode_produk." | ".$nama_produk." | ".$qty." | ".$uom." | ".$row_order;
                $this->_module->gen_history($sub_menu, $kode_bom, $jenis_log, $note_log, $username);

                $callback = array('status' => 'success','message' => 'Data Berhasil Dihapus !', 'icon' =>'fa fa-check', 'type' => 'success');
            }

    	}
    	echo json_encode($callback);
    }


}