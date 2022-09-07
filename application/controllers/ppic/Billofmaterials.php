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
			$row[] = $field->nama_bom;
            $row[] = $field->kode_produk;
            $row[] = $field->nama_produk;
            $row[] = $field->qty;
            $row[] = $field->uom;
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
        $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'], 'qty' => '1000');
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
			$tanggal     = date('Y-m-d H:i:s');

    		if(empty($nama_bom)){
                $callback = array('status' => 'failed', 'field' => 'nama_bom', 'message' => 'Nama BOM Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    
          	}else if(empty($nama_produk) OR empty($kode_produk)){
          		 $callback = array('status' => 'failed', 'field' => 'nama_produk', 'message' => 'Nama Produk Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
          	}else if(empty($qty)){
          		$callback = array('status' => 'failed', 'field' => 'qty', 'message' => 'Qty Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
          	}else if(empty($uom)){
          		$callback = array('status' => 'failed', 'field' => 'uom', 'message' => 'UOM Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
          	}else{


          		if(empty($kode_bom)){//jika kode bom kosong, maka simpan data

          			//cek apa nama bom sudah pernah diinput  by kode_produk ?
          			$cek = $this->m_bom->cek_bom_by_kode_produk($kode_produk,$nama_bom)->row_array();

          			if(!empty($cek['kode_bom'])){//jika sudah ada produk bom

          				$callback = array('status' => 'failed', 'message' => 'Maaf, Nama Bom Sudah Pernah diinput', 'icon' =>'fa fa-warning', 'type' => 'danger' );    

          			}else{// update data

	          			//lock table
	          			$this->_module->lock_tabel('bom WRITE');

	          			//get last number kode_bom
	          			$last_bom   = $this->_module->get_kode_bom();
	          			$kode_bom   = 'BM'.$last_bom;
	          			$this->m_bom->save_bom($kode_bom,$tanggal,$nama_bom,$kode_produk,$nama_produk,$qty,$uom);

	          			//unlock table
    	            	$this->_module->unlock_tabel();

	                   	$kode_encrypt    = encrypt_url($kode_bom);
	          			$jenis_log = "create";
		                $note_log  = $kode_bom." | ".$nama_bom." | ".$kode_produk." | ".$nama_produk." | ".$qty." | ".$uom;
		                $this->_module->gen_history($sub_menu, $kode_bom, $jenis_log, $note_log, $username);
		                $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success', 'kode_encrypt' => $kode_encrypt, 'isi' => $kode_bom);
          			}


          		}else{//update data
          			
          			$this->m_bom->update_bom($kode_bom,$nama_bom,$kode_produk,$nama_produk,$qty,$uom);

          			$jenis_log = "edit";
		            $note_log  = $kode_bom." | ".$nama_bom." | ".$kode_produk." | ".$nama_produk." | ".$qty." | ".$uom;
		            $this->_module->gen_history($sub_menu, $kode_bom, $jenis_log, $note_log, $username);
		            $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');

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