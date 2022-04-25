<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Dti extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("m_lab");//load model m_lab
		$this->load->model("_module");
	}

	public function index()
	{
		$data['id_dept']='DTI';
		$this->load->view('lab/v_dti', $data);
	}

	  function get_data()
    {
        $sub_menu  = $this->uri->segment(2);
        $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
        $list = $this->m_lab->get_datatables($kode['kode']);
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $field) {
            $kode_encrypt = encrypt_url($field->id);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('lab/dti/edit/'.$kode_encrypt).'">'.$field->nama_warna.'</a>';
            $row[] = $field->tanggal;
            $row[] = $field->nama_status;
            $row[] = $field->notes;
 
            $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_lab->count_all($kode['kode']),
            "recordsFiltered" => $this->m_lab->count_filtered($kode['kode']),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
    }

  public function add()
  {	
    $data['id_dept']  ='DTI';
    return $this->load->view('lab/v_dti_add', $data);
  }

  public function simpan()
	{
    //$warna_encr="";
    
    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
      // session habis
      $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{

      $sub_menu   = $this->uri->segment(2);
      $username   = $this->session->userdata('username'); 

      $warna      = addslashes($this->input->post('warna'));
			$id         = $this->input->post('id');
			$tanggal    = $this->input->post('tanggal');
      $notes      = addslashes($this->input->post('note'));
      $kode_warna = addslashes($this->input->post('kode_warna'));
			$status     = addslashes($this->input->post('status'));
      $status2    = $this->input->post('status2');// status head draft/requested

			     if(empty($warna) AND empty($id)){
                $callback = array('status' => 'failed', 'field' => 'warna', 'message' => 'Warna Harus Diisi !'.$id, 'icon' =>'fa fa-warning', 
                  'type' => 'danger'  );    
          	}else if(empty($notes)){
              $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Notes Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger'  );    
          	}else{
            	//cek warna apa sudah ada apa belum
            	$cek = $this->m_lab->cek_color_by_color($warna)->row_array();
              if(!empty($cek['nama_warna']) AND $status == 'tambah'){
                  $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Warna Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    

              }else if(!empty($cek['nama_warna'])){
              		//update notes
                  $this->m_lab->update_color($id,$notes,$kode_warna);
                  $jenis_log   = "edit";
                  $note_log    = $warna." | ".$notes." | ".$kode_warna;
                  $this->_module->gen_history($sub_menu, $id, $jenis_log, $note_log, $username);
                  $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
                  
             	}else{
                
                if($status2 =='requested'){
                  $status_head = $status2; 
                }else{
                  $status_head = 'draft'; 
                }

                // lock tabel
                $this->_module->lock_tabel('warna WRITE');

                $last_id = $this->m_lab->get_last_id_warna();

          			//insert warna
          			$this->m_lab->save_color($warna,$tanggal,$notes,$status_head,$kode_warna);

                // unlock warna
                $this->_module->unlock_tabel();

           			$id_encr     = encrypt_url($last_id);
                $jenis_log   = "create";
                $note_log    = $warna." | ".$notes." | ".$kode_warna;
                $this->_module->gen_history($sub_menu, $last_id, $jenis_log, $note_log, $username);
                $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $id_encr, 'icon' =>'fa fa-check', 'type' => 'success');
                
          		}

          	}
		}

		echo json_encode($callback);
	}

	public function edit($id = null)
	{
		if(!isset($id)) show_404();
        $kode_decrypt  = decrypt_url($id);
        $data['id_dept']  = 'DTI';
        $data['mms']      = $this->_module->get_data_mms_for_log_history('DTI');// get mms by dept untuk menu yg beda-beda
        $data['color']    = $this->m_lab->get_data_color_by_code($kode_decrypt);
        $data['dyest']    = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'DYE');
        $data['aux']      = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'AUX');
        return $this->load->view('lab/v_dti_edit',$data);
	}

  public function tambah_dyeing_stuff_modal()
  {
      $data['id_warna']  = $this->input->post('id_warna');
      $data['warna']     = $this->input->post('warna');
      $data['tipe_obat'] = $this->input->post('tipe_obat');
      return $this->load->view('modal/v_dyeing_stuff_tambah_modal',$data);
  }

  public function tambah_aux_modal()
  {
      $data['id_warna']  = $this->input->post('id_warna');
      $data['warna']     = $this->input->post('warna');
      $data['tipe_obat'] = $this->input->post('tipe_obat');
      return $this->load->view('modal/v_aux_tambah_modal',$data);
  }

  public function simpan_dyestuff_aux_modal()
  {
    $sub_menu  = $this->uri->segment(2);
    $username = $this->session->userdata('username'); 

      if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis' );
      }else{

        $id_warna  = addslashes($this->input->post('id_warna'));
        $warna     = addslashes($this->input->post('warna'));
        $kode      = addslashes($this->input->post('txtKode'));
        $product   = addslashes($this->input->post('txtProduct'));
        $qty       = $this->input->post('txtQty');
        $uom       = addslashes($this->input->post('txtUom'));
        $reff_note = addslashes($this->input->post('reff_note'));
        $tipe_obat = addslashes($this->input->post('tipe_obat'));

        $cek_prod  = $this->m_lab->cek_prod($id_warna,$kode)->row_array();

        if(empty($product)){
           $callback = array('message' => 'Product Harus Diisi !',  'status' => 'failed' );
        }elseif(empty($qty)){
          $callback = array('message' => 'qty Harus Diisi !',  'status' => 'failed' );
        }elseif(empty($uom)){
          $callback = array('message' => 'Uom Harus Diisi !',  'status' => 'failed' );
        }elseif(empty($reff_note)){
          $callback = array('message' => 'Reff Note Harus Diisi !',  'status' => 'failed' );
        }elseif(!empty($cek_prod['nama_produk'])){
          $callback = array('message' => 'Maaf, Product "'.$product.'" sudah diinput !',  'status' => 'failed' );
        }else{
          $this->m_lab->save_dye_aux($id_warna,$kode,$product,$qty,$uom,$reff_note,$tipe_obat);
          $callback    = array('status'=>'success', 'message' => 'Data Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success');
          $jenis_log   = "edit";
          $note_log    = "Tambah Data"." | ".$product." | ".$qty." ".$uom." | ".$reff_note ;
          $this->_module->gen_history($sub_menu, $id_warna, $jenis_log, $note_log, $username);
        }

      }
      echo json_encode($callback);
  }

  public function hapus_dye_aux()
  { 
    $sub_menu  = $this->uri->segment(2);
    $username = $this->session->userdata('username'); 

      if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
      }else{
        $id_warna  = addslashes($this->input->post('id_warna'));
        $row_order = $this->input->post('row_order');
        $type_obat = $this->input->post('type_obat');
        $product   = addslashes($this->input->post('nama_produk'));

        $this->m_lab->delete_dye_aux($id_warna,$row_order,$type_obat);
        $callback    = array('status'=>'success', 'message' => 'Data Berhasil Dihapus !',  'icon' =>'fa fa-check', 'type' => 'success');

        $jenis_log   = "cancel";
        $note_log    = "Hapus Data"." | ".$product ;
        $this->_module->gen_history($sub_menu, $id_warna, $jenis_log, $note_log, $username);

      }

      echo json_encode($callback);
  }
  
  public function get_list_dye()
  {
    $prod      = addslashes($this->input->post('prod'));
    $tipe_obat = 'DYE';
    $callback  = $this->m_lab->get_list_dye_by_name($prod,$tipe_obat);
    echo json_encode($callback);
  }

  public function get_data_dye()
  {
    $kode_produk = addslashes($this->input->post('kode_produk'));
    $tipe_obat   = 'DYE';
    $result      = $this->m_lab->get_data_dye_by_kode($kode_produk,$tipe_obat)->row_array();
    $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'] );

    echo json_encode($callback);
  }

  public function get_list_aux()
  {
    $prod     = addslashes($this->input->post('prod'));
    $tipe_obat= addslashes('AUX');
    $callback = $this->m_lab->get_list_aux_by_name($prod,$tipe_obat);
    echo json_encode($callback);
  }

  public function get_data_aux()
  {
    $kode_produk = addslashes($this->input->post('kode_produk'));
    $tipe_obat   = addslashes('AUX');
    $result      = $this->m_lab->get_data_aux_by_kode($kode_produk,$tipe_obat)->row_array();
    $callback    = array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'] );

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

          $id_warna   = addslashes($this->input->post('id_warna'));
          $this->m_lab->update_status_warna($id_warna,'ready');
          $callback    = array('status'=>'success', 'message' => 'Generate Warna Berhasil !',  'icon' =>'fa fa-check', 'type' => 'success');
           
          $jenis_log   = "ready";
          $note_log    = "Generated" ;
          $this->_module->gen_history($sub_menu, $id_warna, $jenis_log, $note_log, $username);
      }

      echo json_encode($callback);
  }

}