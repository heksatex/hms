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
            $kode_encrypt = encrypt_url($field->kode_warna);
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = '<a href="'.base_url('lab/dti/edit/'.$kode_encrypt).'">'.$field->kode_warna.'</a>';
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
		$sub_menu  = $this->uri->segment(2);
    $username = $this->session->userdata('username'); 
    $warna_encr="";

    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
        // session habis
        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{

			$warna = $this->input->post('warna');
			$tanggal = $this->input->post('tanggal');
      $notes = $this->input->post('note');
			$status = $this->input->post('status');

			     if(empty($warna)){
                $callback = array('status' => 'failed', 'field' => 'warna', 'message' => 'Warna Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger'  );    
          	}else if(empty($notes)){
              $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Notes Harus Diisi !', 'icon' =>'fa fa-warning', 
                  'type' => 'danger'  );    
          	}else{
            	//cek warna apa sudah ada apa belum
            	$cek = $this->m_lab->cek_color_by_color($warna)->row_array();
              if(!empty($cek['kode_warna']) AND $status == 'tambah'){
                  $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Warna Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 
                      'type' => 'danger'  );    
              }else if(!empty($cek['kode_warna'])){
            		//update notes
                $this->m_lab->update_color($warna,$notes);
                $jenis_log   = "create";
                $note_log    = $warna."|".$notes;
                $this->_module->gen_history($sub_menu, $warna, $jenis_log, $note_log, $username);
                $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $warna_encr, 'icon' =>'fa fa-check', 'type' => 'success');
             	}else{
          			//insert warna
          			$this->m_lab->save_color($warna,$tanggal,$notes,'draft');
           			$warna_encr = encrypt_url($warna);
                $jenis_log   = "edit";
                $note_log    = "->".$warna."|".$notes;
                $this->_module->gen_history($sub_menu, $warna, $jenis_log, $note_log, $username);
                $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $warna_encr, 'icon' =>'fa fa-check', 'type' => 'success');
          		}

          	}
		}

		echo json_encode($callback);
	}

	public function edit($id = null)
	{
		if(!isset($id)) show_404();
        $kode_decrypt  = decrypt_url($id);
        $data['id_dept']  ='DTI';
        $data['color']    = $this->m_lab->get_data_color_by_code($kode_decrypt);
        $data['dyest']    = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'DYE');
        $data['aux']      = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'AUX');
        return $this->load->view('lab/v_dti_edit',$data);
	}

  public function tambah_dyeing_stuff_modal()
  {
      $data['warna']     = $this->input->post('warna');
      $data['tipe_obat'] = $this->input->post('tipe_obat');
      return $this->load->view('modal/v_dyeing_stuff_tambah_modal',$data);
  }

  public function tambah_aux_modal()
  {
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

        $warna     = $this->input->post('warna');
        $kode      = $this->input->post('txtKode');
        $product   = $this->input->post('txtProduct');
        $qty       = $this->input->post('txtQty');
        $uom       = $this->input->post('txtUom');
        $reff_note = $this->input->post('reff_note');
        $tipe_obat = $this->input->post('tipe_obat');

        $cek_prod  = $this->m_lab->cek_prod($warna,$kode)->row_array();

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
          $this->m_lab->save_dye_aux($warna,$kode,$product,$qty,$uom,$reff_note,$tipe_obat);
          $callback    = array('status'=>'success', 'message' => 'Data Berhasil Ditambahkan !',  'icon' =>'fa fa-check', 'type' => 'success');
          $jenis_log   = "edit";
          $note_log    = "Tambah Data"." |".$product."|".$qty." ".$uom."|".$reff_note ;
          $this->_module->gen_history($sub_menu, $warna, $jenis_log, $note_log, $username);
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
        $warna     = $this->input->post('warna');
        $row_order = $this->input->post('row_order');
        $type_obat = $this->input->post('type_obat');
        $product   = $this->input->post('nama_produk');

        $this->m_lab->delete_dye_aux($warna,$row_order,$type_obat);
        $callback    = array('status'=>'success', 'message' => 'Data Berhasil Dihapus !',  'icon' =>'fa fa-check', 'type' => 'success');

        $jenis_log   = "cancel";
        $note_log    = "Hapus Data"." |".$product ;
        $this->_module->gen_history($sub_menu, $warna, $jenis_log, $note_log, $username);

      }

      echo json_encode($callback);
  }
  
  public function get_list_dye()
  {
    $prod = $this->input->post('prod');
    $callback = $this->m_lab->get_list_dye_by_name($prod);
    echo json_encode($callback);
  }

  public function get_data_dye()
  {
    $kode_produk = $this->input->post('kode_produk');
    $result = $this->m_lab->get_data_dye_by_kode($kode_produk)->row_array();
    $callback= array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'] );

    echo json_encode($callback);

  }

  public function get_list_aux()
  {
    $prod = $this->input->post('prod');
    $callback = $this->m_lab->get_list_aux_by_name($prod);
    echo json_encode($callback);
  }

  public function get_data_aux()
  {
    $kode_produk = $this->input->post('kode_produk');
    $result = $this->m_lab->get_data_aux_by_kode($kode_produk)->row_array();
    $callback= array('kode_produk'=>$result['kode_produk'],'nama_produk'=>$result['nama_produk'],'uom'=>$result['uom'] );

    echo json_encode($callback);

  }

  public function generate()
  {
    $sub_menu  = $this->uri->segment(2);
    $username = $this->session->userdata('username'); 

      if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
          // session habis
          $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
      }else{
         $warna     = $this->input->post('warna');
         $this->m_lab->update_status_warna($warna,'ready');
         $callback    = array('status'=>'success', 'message' => 'Generate Warna Berhasil !',  'icon' =>'fa fa-check', 'type' => 'success');
         
         $jenis_log   = "ready";
         $note_log    = $warna." - Generated" ;
         $this->_module->gen_history($sub_menu, $warna, $jenis_log, $note_log, $username);
      }
    echo json_encode($callback);
  }

}