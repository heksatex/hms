<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/**
 * 
 */
class Receivinginmanual extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("_module");//load model global
		$this->load->model("m_receivinginmanual");

	}

	public function index()
	{
		$data['id_dept'] ='RCVM';
        $this->load->view('warehouse/v_receiving_in_manual',$data);
	}

	public function get_data()
    {

        $sub_menu = $this->uri->segment(2);
        //$id_dept  = $this->input->post('id_dept');
        //$kode     = $this->_module->get_kode_sub_menu_deptid($sub_menu,$id_dept)->row_array();
        $list = $this->m_receivinginmanual->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = $this->encryption->encrypt($field->kode);
            $kode_encrypt = encrypt_url($field->kode);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('warehouse/receivinginmanual/edit/'.$kode_encrypt).'">'.$field->kode.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->creation_date;
            $row[] = $field->source_document;
            $row[] = $field->lokasi_tujuan;
            $row[] = $field->note;
            $row[] = $field->status;
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_receivinginmanual->count_all(),
            "recordsFiltered" => $this->m_receivinginmanual->count_filtered(),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }


    public function add()
    {
        $data['id_dept']  ='RCVM';
        return $this->load->view('warehouse/v_receiving_in_manual_add', $data);
    }


    public function get_receiving_in_by_kode()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
        }else{

    		$no_rcv_in   = $this->input->post('no_rcv_in');

    		//$head        = $this->m_receivinginmanual->get_list_receiving_by_kode($no_rcv_in)->result_array();
    		$items       = $this->m_receivinginmanual->get_list_receiving_by_kode($no_rcv_in)->result_array();
    		$empty       = TRUE;
    		$not_done    = '';
    		$state       = '';

    		foreach ($items as $row) {
    			$empty  = FALSE;
    			$creation_date = $row['date'];
		    	$source_doc    = $row['origin'];
		    	$state = $row['state'];
		    	$note  = $row['note_purc'];
		    	//cek jika status ada yang bukan done
		    	if($state != 'done' ){
    				$note_done = $state;
		    	}
    		}

    		if(!empty($note_done)){
    			$status = $note_done;
    		}else{
    			$status = $state;
    		}

    		if($empty == TRUE){
    			$callback = array('status' => 'failed', 'field' => 'rcv_in', 'message' => 'No Receiving IN Tidak ditemukan !', 'icon' =>'fa fa-warning', 'type' => 'danger' );
    		}else{
    			$callback = array('status' => 'success', 'field' => 'rcv_in', 'message' => 'No Receiving IN Berhasil ditemukan !',
    												'icon' =>'fa fa-check', 'type' => 'success',
    												'no_receiving' => $no_rcv_in,
    												'creation_date' => $creation_date, 'source_doc' => $source_doc,
    												'status' => $status, 'note' => $note, 'items' => $items);
    		}

    	}

    	echo json_encode($callback);
    }


    public function simpan()
    {

    	if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    	}else{
    		$username = addslashes($this->session->userdata('username')); 
    		$sub_menu  = $this->uri->segment(2);

    		$kode     = addslashes($this->input->post('kode'));
    		$creation_date = ($this->input->post('creation_date'));
    		$source_doc= addslashes($this->input->post('source_doc'));
    		$state     = addslashes($this->input->post('status'));
    		$note      = addslashes($this->input->post('note'));
    		$tanggal   = date('Y-m-d H:i:s');

    		$sql_simpan_penerimaan_barang = "";
    		$sql_simpan_penerimaan_barang_items = "";
    		$sql_simpan_mst_produk  = "";
    		$sql_simpan_stock_quant = "";

    		$sql_simpan_mst_produk  = "";
    		$kode_empty   = TRUE;
    		$row_order    = 1;
    		$not_done     = FALSE;

    		//get list product by kode
    		$items    = $this->m_receivinginmanual->get_list_receiving_by_kode($kode)->result_array();

    		//lock table
    		$this->_module->lock_tabel('stock_quant WRITE, penerimaan_barang_m WRITE, penerimaan_barang_m_items WRITE, mst_produk WRITE, z_trans_uom WRITE, z_trans_category WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE' );

    		//cek no receiving apa sudah pernah di input !
    		$cek_kode = $this->m_receivinginmanual->get_penerimaan_barang_m_by_kode($kode)->row_array();

    		if(!empty($cek_kode['kode'])){
    			$kode_empty = FALSE;

    		}else{

	    		//get last quant id
	         	$start = $this->_module->get_last_quant_id();
	    		
	    		//get last number kode produk
	    		$last_number  = $this->_module->get_kode_product();

	    		foreach ($items as $row) {
	    			# code...
	    			if($row['state'] !='done'){
	    				$not_done = TRUE;
	    				break; //keluar looping
	    			}

	    			$ads_prod = addslashes($row['name_template']);

	    			$get = $this->m_receivinginmanual->get_produk_by_nama($ads_prod)->row_array();
	    		
	    			$create_date  = $row['create_date'];
	    			$lot          = $row['lot'];


	    			if(empty($get['nama_produk'])){//jika nama produk tidak ada

	    				
	    				//get uom translate
	    				$uom_odoo = addslashes($row['uom']);
		    			$get_uom = $this->m_receivinginmanual->get_uom_by_uom_odoo($uom_odoo)->row_array();
		    			if(!empty($get_uom['uom'])){
		    				$uom  = ($get_uom['uom']);
		    			}else{
		    				$uom  = '';
		    			}

		    			//get route/category odoo translate
		    			$route_odoo = addslashes($row['route']);
		    			$get_route  = $this->m_receivinginmanual->get_route_by_category_odoo($route_odoo)->row_array();
		    			if(!empty($get_route['route_produksi'])){
		    				$route = ($get_route['route_produksi']);
		    			}else{
		    				$route = '';
		    			}

		    			if($row['type'] == 'product'){
		    				$type = 'stockable';
		    			}else if($row['type'] == 'consu'){
		    				$type = 'consumable';
		    			}else{
		    				$type = 'service';
		    			}

		    			if(empty($row['default_code'])){
		    				$kode_produk  = 'MF'.$last_number;
		    				//lat number + 1 kode_produk ex MF..
		    				$last_number++;
		    			}else{
		    				$kode_produk = ($row['default_code']);
		    			}

		    			$nama_produk  = ($row['name_template']);
		    			
	    				//insert ke mst_produk
	    				$sql_simpan_mst_produk .= "('".addslashes($kode_produk)."','".addslashes($nama_produk)."','".addslashes($uom)."','".$create_date."','".addslashes($route)."','".addslashes($type)."'), ";

	    				
	    			}else{

	    				$kode_produk  = ($get['kode_produk']);
	    				$nama_produk  = ($get['nama_produk']);
	    				$uom          = ($get['uom']);

	    			}
	    			$qty          = $row['qty'];
	    			$status       = ($row['state']);
	    			//insert ke penerimaan barang m items
	    			$sql_simpan_penerimaan_barang_items .= "('".$kode."','".addslashes($kode_produk)."','".addslashes($nama_produk)."','".$lot."','".$qty."','".addslashes($uom)."','".$status."','".$row_order."'), ";
	    			$row_order++;

	    			//insert ke stock quant
	    			$sql_simpan_stock_quant .= "('".$start."','".$tanggal."', '".addslashes($kode_produk)."', '".addslashes($nama_produk)."','".$lot."','','".$qty."','".addslashes($uom)."','','','RCV/Stock','".addslashes($note)."','',''), ";
	    			$start++;


	    		}//end foreach items

    		}

    			if($kode_empty == FALSE){
    				$callback = array('status' => 'failed', 'field' => 'rcv_in', 'message' => 'No Receiving IN Sudah Pernah diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger' );

    			}else if($not_done == TRUE ){
    				//$callback = array();
    				$callback = array('status' => 'failed', 'field' => 'rcv_in', 'message' => 'No Receiving IN Belum ditransfer !', 'icon' =>'fa fa-warning', 'type' => 'danger' );
    			}else{

					if(!empty($sql_simpan_penerimaan_barang_items)){
    					//insert ke penerimaan barang m
						$sql_simpan_penerimaan_barang = "('".$kode."','".$tanggal."','".$creation_date."','".$source_doc."','RCV/Stock','".$note."','done')";
                        $sql_simpan_penerimaan_barang = rtrim($sql_simpan_penerimaan_barang, ', ');
                        $this->m_receivinginmanual->save_penerimaan_manual($sql_simpan_penerimaan_barang);

                        $sql_simpan_penerimaan_barang_items = rtrim($sql_simpan_penerimaan_barang_items, ', ');
                        $this->m_receivinginmanual->save_penerimaan_manual_items_batch($sql_simpan_penerimaan_barang_items);
                    }

                    if(!empty($sql_simpan_stock_quant)){
                    	$sql_simpan_stock_quant = rtrim($sql_simpan_stock_quant, ', ');
                    	$this->_module->simpan_stock_quant_batch($sql_simpan_stock_quant);
                    }

                    if(!empty($sql_simpan_mst_produk)){
                    	$sql_simpan_mst_produk = rtrim($sql_simpan_mst_produk, ', ');
                    	$this->m_receivinginmanual->save_produk_manual_batch($sql_simpan_mst_produk);
                    }

                    $jenis_log = "create";
                	$note_log  = $kode." | ".$source_doc." | ".$note;
                    $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !','icon' =>'fa fa-check', 'type' => 'success');
    			}

    		//unlock table
            $this->_module->unlock_tabel();   

    	}

    	echo json_encode($callback);
    }

    public function edit($id = null)
    {   
        if(!isset($id)) show_404();
        $kode_decrypt      = decrypt_url($id);
        $data['id_dept']   ='RCVM';
        $data["head"]      = $this->m_receivinginmanual->get_penerimaan_barang_m_by_kode($kode_decrypt)->row();
        $data["items"]     = $this->m_receivinginmanual->get_penerimaan_barang_m_items_by_kode($kode_decrypt);

        if(empty($data["head"])){
          show_404();
        }else{
          return $this->load->view('warehouse/v_receiving_in_manual_edit',$data);
        }

    }

}