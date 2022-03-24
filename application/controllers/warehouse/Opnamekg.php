<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Opnamekg extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();//cek apakah user sudah login
        $this->load->model("_module");
        $this->load->model("m_opnameKg");
    }


    public function index()
    {
        $data['id_dept']   = 'OPN';
        $data['warehouse'] = $this->_module->get_list_departement();
        $this->load->view('warehouse/v_opname_kg', $data);
    }


    public function get_data()
    {	
        $list = $this->m_opnameKg->get_datatables();
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
             $kode_encrypt = encrypt_url($field->kode_opname);
         
             $no++;
             $row = array();
             $row[] = $no;
             $row[] = '<a href="'.base_url('warehouse/opnamekg/edit/'.$kode_encrypt).'">'.$field->kode_opname.'</a>';
             $row[] = $field->tanggal;
             $row[] = $field->departemen;
             $row[] = $field->lokasi_fisik;
             $row[] = $field->nama_produk;
             $row[] = $field->lot;
             $row[] = $field->qty_opname.' '.$field->uom_opname;
  
             $data[] = $row;
        }
  
        $output = array(
             "draw" => $_POST['draw'],
             "recordsTotal" => $this->m_opnameKg->count_all(),
             "recordsFiltered" => $this->m_opnameKg->count_filtered(),
             "data" => $data,
        );
         //output dalam format JSON
        echo json_encode($output);
     }
 


    public function add()
	{ 
	    $data['id_dept']       = 'OPN';
        $data['kode_opname']   = $this->m_opnameKg->get_kode_opname();
        $data['warehouse']     = $this->_module->get_list_departement();
        $data['uom']           = $this->_module->get_list_uom();
	    return $this->load->view('warehouse/v_opname_kg_add', $data);
	}

    public function edit($id = null)
    {
        if(!isset($id)) show_404();
        $kode_decrypt     = decrypt_url($id);
        $id_dept   		  = 'OPN';
	    $data['id_dept']  = $id_dept;
        $data['mms']      = $this->_module->get_data_mms_for_log_history($id_dept);// get mms by dept untuk menu yg beda-beda
        $data['opn']      = $this->m_opnameKg->get_data_opname_by_kode($kode_decrypt);

        if(empty($data["opn"])){
          show_404();
        }else{
          return $this->load->view('warehouse/v_opname_kg_edit',$data);
        }
    }


    public function search_barcode()
    {

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
	        // session habis
	        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
	    }else{
	    	$lot      = addslashes($this->input->post('txtlot'));
	    	$dept_id  = addslashes($this->input->post('departemen'));

            if(empty($dept_id)){
                $callback = array('status' => 'failed', 'field' => 'departemen', 'message' => 'Departemen tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
                
            }else if(empty($lot)){
                $callback = array('status' => 'failed', 'field' => 'txtlot', 'message' => 'Barcode / Lot tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 

            }else{
                // get lokasi stock by departemen
                $lokasi_stock = $this->m_opnameKg->get_lokasi_stock_by_dept($dept_id);

                // validasi barcode
	      		$validBarcode = $this->m_opnameKg->verified_barcode_by_dept($lot);

	      		// cek lokasi barcode by dept
	      		$validBarcodeDepartement = $this->m_opnameKg->is_valid_lokasi_barcode_by_dept($lokasi_stock,$lot);
    
                if(!$validBarcode){
                    $callback = array('status' => 'failed', 'field' => 'txtlot', 'message' => 'Barcode / Lot ( '.$lot.' ) tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );

                }else if($validBarcodeDepartement['lokasi'] != $lokasi_stock){
                    $callback = array('status' => 'failed', 'field' => 'txtlot', 'message' => 'Lokasi Barcode / Lot ( '.$lot.' ) tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );

                }else{
                    // get data di stockquant by lot and lokasi
                    $result = $this->m_opnameKg->get_data_stock_quant_by_kode($lokasi_stock,$lot);
                    $callback = array('status' => 'success', 'message' => 'Scan Barcode Berhasil', 'icon' =>'fa fa-check', 'type' => 'success', 'result' => $result  ); 
                }
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

            $username = $this->session->userdata('username'); 
	    	$nama_user  = $this->_module->get_nama_user($username)->row_array();
            $sub_menu = $this->uri->segment(2);

            $quant_id      = addslashes($this->input->post('quant_id'));
	    	$lot           = addslashes($this->input->post('lot'));
	    	$kode_produk   = addslashes($this->input->post('kode_produk'));
	    	$nama_produk   = addslashes($this->input->post('nama_produk'));
	    	$lokasi_fisik  = addslashes($this->input->post('lokasi_fisik'));
	    	$qty_opname    = addslashes($this->input->post('qty_opname'));
	    	$uom_opname    = addslashes($this->input->post('uom_opname'));
	    	$qty           = addslashes($this->input->post('qty'));
	    	$uom_qty       = addslashes($this->input->post('uom_qty'));
	    	$qty2          = addslashes($this->input->post('qty2'));
	    	$uom_qty2      = addslashes($this->input->post('uom_qty2'));
            $dept_id       = addslashes($this->input->post('departemen'));
            $tanggal       = date('Y-m-d H:i:s');

            
            if(empty($dept_id)){
                $callback = array('status' => 'failed', 'field' => 'departemen', 'message' => 'Departemen tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
                
            }else if(empty($kode_produk) || empty($lot)){
                $callback = array('status' => 'failed', 'field' => 'txtlot', 'message' => 'Lot / Produk tidak boleh kosong !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 
            
            }else if(!preg_match("/^.[0-9.]/",$qty_opname)){
                $callback = array('status' => 'failed', 'field' => 'qty_opname', 'message' => 'Qty Opname harus berupa Angka !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 

            }else{

                // get lokasi stock by departemen
                $lokasi_stock = $this->m_opnameKg->get_lokasi_stock_by_dept($dept_id);

                // validasi barcode
                $validBarcode = $this->m_opnameKg->verified_barcode_by_dept($lot);
 
                // cek lokasi barcode by dept
                $validBarcodeDepartement = $this->m_opnameKg->is_valid_lokasi_barcode_by_dept($lokasi_stock,$lot);
     
                if(!$validBarcode){
                    $callback = array('status' => 'failed', 'field' => 'txtlot', 'message' => 'Barcode / Lot ( '.$lot.' ) tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
 
                }else if($validBarcodeDepartement['lokasi'] != $lokasi_stock){
                    $callback = array('status' => 'failed', 'field' => 'txtlot', 'message' => 'Lokasi Barcode / Lot ( '.$lot.' ) tidak Valid !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );

                }else{
                    // get kode opname kg
                    $kode = $this->m_opnameKg->get_kode_opname();

                    // insert opname kg ke tbl opname_kg
                    $this->m_opnameKg->save_opname_kg($kode, $tanggal, $quant_id, $kode_produk, $nama_produk, $lot, $qty_opname, $uom_opname,$qty, $uom_qty, $qty2, $uom_qty2, $dept_id, $lokasi_fisik, $nama_user['nama'] );

                    // update qty opname, uom_opname di stock_quant
                    $this->m_opnameKg->update_qty_opname_stock_quant_by_quant_id($quant_id,$qty_opname,$uom_opname);
                    
                    $dept = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
                    
                    $jenis_log   = "create";
			        $note_log    = $dept['nama']." | ".$nama_produk." | ".$lot." | ".$qty_opname." ".$uom_opname;
			        $this->_module->gen_history($sub_menu, $kode, $jenis_log, $note_log, $username);
                    
                    $kode = $this->m_opnameKg->get_kode_opname();
                    $callback = array('status' => 'success', 'message' => 'Opname Kg Berhasil Disimpan ', 'icon' =>'fa fa-check', 'type' => 'success', 'kode_opname'=>$kode ); 
                }

            }

        }

        echo json_encode($callback);
    }


}