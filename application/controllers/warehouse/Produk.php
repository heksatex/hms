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
          $kode_encrypt = encrypt_url($field->kode_produk);
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
    $data['uom'] = $this->_module->get_list_uom();
    $data['category'] = $this->m_produk->get_list_category();
    $data['route']    = $this->m_produk->get_list_route();        
    return $this->load->view('warehouse/v_produk_add', $data);
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
      $this->_module->lock_tabel('mst_produk WRITE, mst_category WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE');

      //cek auto generate kode produk atau input sendiri
      $autogenerate = $this->input->post('autogenerate');
      if ($autogenerate == 0){
        $kodeproduk   = addslashes($this->input->post('kodeproduk'));
      }else{
        $kodeproduk  = $this->_module->get_kode_product();
        $kodeproduk = 'MF'.$kodeproduk;
      }
      
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
      $lebarjadi      = $this->input->post('lebarjadi');

      
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


        if(!empty($cek['kode_produk']) AND $status == 'tambah'){
            $callback = array('status' => 'failed', 'field' => 'kodeproduk', 'message' => 'Kode Produk ini Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );    
        }elseif($nama_double == TRUE){
            $callback = array('status' => 'failed', 'field' => 'namaproduk', 'message' => 'Nama Produk ini Sudah Pernah Diinput !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );   
        }else if(!empty($cek['kode_produk'])){
          //update/edit produk
          $this->m_produk->update_produk($kodeproduk,$namaproduk,$uomproduk,$uomproduk2,$routeproduksi,$typeproduk,$dapatdibeli,$dapatdijual,$kategoribarang,$note,$bom,$lebarjadi);
          $kodeproduk_encr = encrypt_url($kodeproduk);

          if($bom == 1){
            $log_bom = 'True';
          }else{
            $log_bom =  'False';
          }

          $jenis_log   = "edit";
          $note_log    = $kodeproduk." | ".$namaproduk." | ".$uomproduk." | ".$uomproduk2." | ".$lebarjadi." | ".$routeproduksi." | ".$typeproduk." | ".$dapatdibeli." | ".$dapatdijual." | ".$nmKategori['nama_category']." | ".$log_bom;
          $this->_module->gen_history($sub_menu, $kodeproduk, $jenis_log, $note_log, $username);
          $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
        }else{
          //insert/add produk
          $this->m_produk->save_produk($kodeproduk,$namaproduk,$uomproduk,$uomproduk2,$tanggaldibuat,$routeproduksi,$typeproduk,$dapatdibeli,$dapatdijual,$kategoribarang,$note,$bom,$lebarjadi);
          $kodeproduk_encr = encrypt_url($kodeproduk);

          if($bom == 1){
            $log_bom = 'True';
          }else{
            $log_bom =  'False';
          }

          $jenis_log   = "create";
          $note_log    = $kodeproduk." | ".$namaproduk." | ".$uomproduk." | ".$uomproduk2." | ".$lebarjadi." | ".$routeproduksi." | ".$typeproduk." | ".$dapatdibeli." | ".$dapatdijual." | ".$nmKategori['nama_category']." | ".$log_bom;
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
        $data['produk']   = $this->m_produk->get_produk_by_kode($kode_decrypt);
        $data['uom']      = $this->m_produk->get_list_uom();
        $data['category'] = $this->m_produk->get_list_category();
        $data['route']    = $this->m_produk->get_list_route();

        //get data untuk glyphicon
        $data['onhand']   = $this->m_produk->get_qty_onhand($kode_decrypt);
        $data['moves']    = $this->m_produk->get_jml_moves($kode_decrypt);
        $data['bom']      = $this->m_produk->get_jml_bom($kode_decrypt);
        $data['mo']       = $this->m_produk->get_jml_mo($kode_decrypt);

        //$data['dyest']    = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'DYE');
        //$data['aux']      = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'AUX');
        if(empty($data["produk"])){
          show_404();
        }else{
          return $this->load->view('warehouse/v_produk_edit',$data);
        }
  }

}