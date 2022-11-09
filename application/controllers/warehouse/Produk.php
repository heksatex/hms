<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Produk extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->is_loggedin();//cek apakah user sudah login
    $this->load->model("m_produk");//load model m_lab
    $this->load->model("_module");
  }

  public function index()
  {
    $data['id_dept']='MPROD';
    $data['category'] = $this->m_produk->get_list_category();
    $data['route']    = $this->m_produk->get_list_route();
    $this->load->view('warehouse/v_produk', $data);
  }

  function get_data()
  {
      $sub_menu  = $this->uri->segment(2);
      $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
      $list = $this->m_produk->get_datatables();
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
          $kode_encrypt = encrypt_url($field->id);
          $no++;
          $row = array();
          $row[] = $no;
          $row[] = '<a href="'.base_url('warehouse/produk/edit/'.$kode_encrypt).'">'.$field->kode_produk.'</a>';
          $row[] = $field->nama_produk;
          $row[] = $field->create_date;
          $row[] = $field->uom;
          $row[] = $field->uom_2;
          $row[] = $field->nama_category;
          $row[] = $field->route_produksi;
          $row[] = $field->type;
          $row[] = $field->nama_status;

          $data[] = $row;
      }
      
      $output = array(
          "draw" => $_POST['draw'],
          "recordsTotal" => $this->m_produk->count_all(),
          "recordsFiltered" => $this->m_produk->count_filtered(),
          "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
  }

  public function add()
  { 
    $data['id_dept']  ='MPROD';
    $data['uom']      = $this->_module->get_list_uom();
    $data['category'] = $this->m_produk->get_list_category();
    $data['route']    = $this->m_produk->get_list_route();        
    return $this->load->view('warehouse/v_produk_add', $data);
  }

  function get_product_parent_select2()
	{
		  $nama     = addslashes($this->input->post('nama'));
   		$callback = $this->m_produk->get_list_product_parent($nama);
      echo json_encode($callback);
	}

  public function simpan()
  {
    $sub_menu  = $this->uri->segment(2);
    $username = $this->session->userdata('username'); 

    if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
        // session habis
        $callback = array('message' => 'Waktu Anda Telah Habis',  'sesi' => 'habis' );
    }else{
      
      //lock table
      $this->_module->lock_tabel('mst_produk WRITE, mst_category WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE, mst_status WRITE, mst_produk_parent WRITE');

      //cek auto generate kode produk atau input sendiri
      $autogenerate = $this->input->post('autogenerate');
      if ($autogenerate == 0){
        $kodeproduk   = addslashes($this->input->post('kodeproduk'));
      }else{
        $kodeproduk  = $this->_module->get_kode_product();
        $kodeproduk = 'MF'.$kodeproduk;
      }
      
      $id             = $this->input->post('id');//id produk auto increment
      $namaproduk     = addslashes($this->input->post('namaproduk'));
      $uomproduk      = addslashes($this->input->post('uomproduk'));
      $uomproduk2     = addslashes($this->input->post('uomproduk2'));
      $routeproduksi  = addslashes($this->input->post('routeproduksi'));
      $typeproduk     = addslashes($this->input->post('typeproduk'));
      $bom            = $this->input->post('bom');
      $dapatdijual    = $this->input->post('dapatdijual');
      $dapatdibeli    = $this->input->post('dapatdibeli');
      $kategoribarang = $this->input->post('kategoribarang');
      $note           = $this->input->post('note');
      $lebargreige    = addslashes($this->input->post('lebargreige'));
      $uom_lebargreige= addslashes($this->input->post('uom_lebargreige'));
      $lebarjadi      = addslashes($this->input->post('lebarjadi'));
      $uom_lebarjadi  = addslashes($this->input->post('uom_lebarjadi'));
      $product_parent = addslashes($this->input->post('product_parent'));
      $statusproduk   = $this->input->post('statusproduk');

      
      //get id kategori barang
      $nmKategori = $this->m_produk->get_nama_category_by_id($kategoribarang)->row_array();
      
      $tanggaldibuat = $this->input->post('tanggaldibuat'); 
      $status = addslashes($this->input->post('status'));

      if(empty($namaproduk)){
        $callback = array('status' => 'failed', 'field' => 'namaproduk', 'message' => 'Nama Produk Harus Diisi !', 'icon' =>'fa fa-warning', 
              'type' => 'danger'  );             
      //}else if($kodeproduk == ''){
      }else if(empty($kodeproduk)){
        $callback = array('status' => 'failed', 'field' => 'kodeproduk', 'message' => 'Kode Produk Harus Diisi !', 'icon' =>'fa fa-warning', 
              'type' => 'danger'  );
      }else if(empty($uomproduk)){
        $callback = array('status' => 'failed', 'field' => 'uomproduk', 'message' => 'UOM/Satuan Harus Diisi !', 'icon' =>'fa fa-warning', 
              'type' => 'danger'  );
      }else if(empty($kategoribarang)){
        $callback = array('status' => 'failed', 'field' => 'kategoribarang', 'message' => 'Kategori Barang Harus Diisi !', 'icon' =>'fa fa-warning', 
              'type' => 'danger'  );
      }else if(!empty($lebargreige) AND empty($uom_lebargreige)){
        $callback = array('status' => 'failed', 'field' => 'uom_lebargreige', 'message' => 'Uom Lebar Greige Harus Diisi !', 'icon' =>'fa fa-warning', 
              'type' => 'danger'  );
      }else if(!empty($lebarjadi) AND empty($uom_lebarjadi)){
        $callback = array('status' => 'failed', 'field' => 'uom_lebarjadi', 'message' => 'Uom Lebar Jadi Harus Diisi !', 'icon' =>'fa fa-warning', 
              'type' => 'danger'  );
      }else{
        //cek kode produk apa sudah ada apa belum
        $cek = $this->m_produk->cek_produk_by_kode($kodeproduk)->row_array();
        // cek nama sudah ada atau belum ?
        $cek2 = $this->m_produk->cek_produk_by_nama($kodeproduk,$namaproduk)->row_array();

        // cek apa nama_produk tidak ada 
        if(empty($cek2['nama_produk'])){
          $nama_double = FALSE;
        }else{
          $nama_double = TRUE;
        }

        if($bom == 1){
          $log_bom = 'True';
        }else{
          $log_bom =  'False';
        }
        
        // cek mst parent by id
        $parent = $this->m_produk->get_mst_parent_produk_by_id($product_parent)->row_array();

        //get status aktif by kode f/t
        $status_aktif = $this->_module->get_mst_status_by_kode($statusproduk);

        if(!empty($cek['kode_produk']) AND $status == 'tambah'){
            $callback = array('status' => 'failed', 'field' => 'kodeproduk', 'message' => 'Kode Produk ini Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    
        }elseif($nama_double == TRUE){
            $callback = array('status' => 'failed', 'field' => 'namaproduk', 'message' => 'Nama Produk ini Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );   
        }else if(!empty($cek['kode_produk'])){
          //update/edit produk
          $this->m_produk->update_produk($id,$namaproduk,$uomproduk,$uomproduk2,$routeproduksi,$typeproduk,$dapatdibeli,$dapatdijual,$kategoribarang,$note,$bom,$lebargreige,$uom_lebargreige,$lebarjadi,$uom_lebarjadi,$statusproduk,$product_parent);
         
          $jenis_log   = "edit";
          $note_log    = $kodeproduk." | ".$namaproduk." | ".$uomproduk." | ".$uomproduk2." | ".$lebargreige." ".$uom_lebargreige." | ".$lebarjadi." ".$uom_lebarjadi." | ".$routeproduksi." | ".$typeproduk." | ".$dapatdibeli." | ".$dapatdijual." | ".$nmKategori['nama_category']." | ".$log_bom." | ".$parent['nama']." |".$status_aktif;
          $this->_module->gen_history($sub_menu, $kodeproduk, $jenis_log, $note_log, $username);
          $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
        }else{
          //insert/add produk
          $id_new = $this->m_produk->get_last_id_mst_produk();

          $this->m_produk->save_produk($kodeproduk,$namaproduk,$uomproduk,$uomproduk2,$tanggaldibuat,$routeproduksi,$typeproduk,$dapatdibeli,$dapatdijual,$kategoribarang,$note,$bom,$lebargreige,$uom_lebargreige,$lebarjadi,$uom_lebarjadi,$statusproduk,$product_parent);
          $kodeproduk_encr = encrypt_url($id_new);

          $jenis_log   = "create";
          $note_log    = $kodeproduk." | ".$namaproduk." | ".$uomproduk." | ".$uomproduk2." | ".$lebargreige." ".$uom_lebargreige." | ".$lebarjadi." ".$uom_lebarjadi." | ".$routeproduksi." | ".$typeproduk." | ".$dapatdibeli." | ".$dapatdijual." | ".$nmKategori['nama_category']." | ".$log_bom." | ".$parent['nama']." |".$status_aktif;
          $this->_module->gen_history($sub_menu, $kodeproduk, $jenis_log, $note_log, $username);
          $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $kodeproduk_encr, 'icon' =>'fa fa-check', 'type' => 'success');
        }
      }

      //unlock table
      $this->_module->unlock_tabel();
    }

    echo json_encode($callback);
  }

  public function get_coa_list()
  {
    $coa = $this->input->post('glacc');
    $callback = $this->m_produk->get_list_coa($coa);
    echo json_encode($callback);
  }

  public function edit($id = null)
  {
    if(!isset($id)) show_404();
        $kode_decrypt     = decrypt_url($id);
        $data['id_dept']  ='MPROD';
        $data['mms']      = $this->_module->get_data_mms_for_log_history('MPROD');// get mms by dept untuk log history
        $produk           = $this->m_produk->get_produk_by_kode($kode_decrypt);//id auto increment
        $data['produk']   = $produk;
        
        $data['uom']      = $this->m_produk->get_list_uom();
        $data['category'] = $this->m_produk->get_list_category();
        $data['route']    = $this->m_produk->get_list_route();

        //get data untuk glyphicon
        $data['onhand']   = $this->m_produk->get_qty_onhand($produk->kode_produk);
        $data['moves']    = $this->m_produk->get_jml_moves($produk->kode_produk);
        $data['bom']      = $this->m_produk->get_jml_bom($produk->kode_produk);
        $data['mo']       = $this->m_produk->get_jml_mo($produk->kode_produk);

        //$data['dyest']    = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'DYE');
        //$data['aux']      = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'AUX');
        if(empty($data["produk"])){
          show_404();
        }else{
          return $this->load->view('warehouse/v_produk_edit',$data);
        }
  }

  function view_list_bom_produk_modal()
  {
    $kode_produk= $this->input->post('kode_produk');

    $data['kode_produk'] = $kode_produk;
    return $this->load->view('modal/v_produk_list_bom_modal', $data);
  }


  function get_data_list_bom_produk()
  {
      $kode_produk  = addslashes($this->input->post('kode_produk'));
      $list = $this->m_produk->get_datatables2($kode_produk);
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
          $kode_encrypt = $this->encryption->encrypt($field->kode_bom);
          $kode_encrypt = encrypt_url($field->kode_bom);
          $no++;
          $row = array();
          $row[] = $no;
          $row[] = '<a href="'.base_url('ppic/billofmaterials/edit/'.$kode_encrypt).'" target="_blank">'.$field->kode_bom.'</a>';
          $row[] = $field->nama_bom;
          $row[] = $field->kode_produk;
          $row[] = $field->nama_produk;
          $row[] = $field->qty;
          $row[] = $field->uom;
          $data[] = $row;
      }

      $output = array(
          "draw" => $_POST['draw'],
          "recordsTotal" => $this->m_produk->count_all2($kode_produk),
          "recordsFiltered" => $this->m_produk->count_filtered2($kode_produk),
          "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
  }

  function view_list_mo_produk_modal()
  {
    $kode_produk= $this->input->post('kode_produk');

    $data['kode_produk'] = $kode_produk;
    return $this->load->view('modal/v_produk_list_mo_modal', $data);
  }


  function get_data_list_mo_produk()
  {
      $kode_produk  = addslashes($this->input->post('kode_produk'));
      $list = $this->m_produk->get_datatables3($kode_produk);
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
          $kode_encrypt = encrypt_url($field->kode);
          $no++;
          $row = array();
          $row[] = $no;
          $row[] = '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'">'.$field->kode.'</a>';
          $row[] = $field->tanggal;
          $row[] = $field->departemen;
          $row[] = $field->nama_produk;
          $row[] = $field->qty;
          $row[] = $field->uom;
          $row[] = $field->nama_status;
          $data[] = $row;
        }
 
        $output = array(
            "draw" => $_POST['draw'],
            "recordsTotal" => $this->m_produk->count_all3($kode_produk),
            "recordsFiltered" => $this->m_produk->count_filtered3($kode_produk),
            "data" => $data,
        );
        //output dalam format JSON
        echo json_encode($output);
  }





  

}