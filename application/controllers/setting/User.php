<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class User extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->is_loggedin();//cek apakah user sudah login
    $this->load->model("m_user");//load model m_user
    $this->load->model("_module");
  }

  public function index()
  {
    $data['id_dept']='MUSR';
    $this->load->view('setting/v_user', $data);
  }

  function get_data()
  {
      $sub_menu  = $this->uri->segment(2);
      $kode = $this->_module->get_kode_sub_menu($sub_menu)->row_array();
      $list = $this->m_user->get_datatables();
      $data = array();
      $no = $_POST['start'];
      foreach ($list as $field) {
          $kode_encrypt = encrypt_url($field->username);
          $no++;
          $row = array();
          $row[] = $no;
          $row[] = $field->nama;
          $row[] = '<a href="'.base_url('setting/user/edit/'.$kode_encrypt).'">'.$field->username.'</a>';
          $row[] = $field->level;

          $data[] = $row;
      }
      
      $output = array(
          "draw" => $_POST['draw'],
          "recordsTotal" => $this->m_user->count_all(),
          "recordsFiltered" => $this->m_user->count_filtered(),
          "data" => $data,
      );
      //output dalam format JSON
      echo json_encode($output);
  }

  public function add()
  { 
    $data['id_dept']  ='MUSR';
    //$data['uom'] = $this->_module->get_list_uom();
    //$data['category'] = $this->m_produk->get_list_category();
    //$data['route']    = $this->m_produk->get_list_route();        
    return $this->load->view('setting/v_user_add', $data);
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
      $this->_module->lock_tabel('user WRITE, user_priv WRITE, main_menu_rel WRITE, main_menu_sub WRITE, log_history WRITE');
      
      $namauser       = addslashes($this->input->post('namauser'));
      $password       = md5('123');
      $login          = addslashes($this->input->post('login'));
      $tanggaldibuat  = $this->input->post('tanggaldibuat'); 
      $arrchkakses    = addslashes($this->input->post('arrchkakses'));
      $status = addslashes($this->input->post('status'));

      if(empty($namauser)){
        $callback = array('status' => 'failed', 'field' => 'namauser', 'message' => 'Nama User Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );             
      }else if(empty($login)){
        $callback = array('status' => 'failed', 'field' => 'login', 'message' => 'Login Name Harus Diisi !', 'icon' =>'fa fa-warning','type' => 'danger'  );      
      }else if(empty($arrchkakses)){
         $callback = array('status' => 'failed', 'message' => 'Pilih Minimal 1 Hak Akses ! ', 'icon' =>'fa fa-warning','type' => 'danger'  );      
      }else{

        //cek login user apa sudah ada apa belum        
        $cek = $this->m_user->cek_user_by_login($login)->row_array();

        if(!empty($cek['username']) AND $status == 'tambah'){
            $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Login Name sudah dipakai !', 'icon' =>'fa fa-warning', 
                'type' => 'danger'  );    
        }else if(!empty($cek['username'])){
          //update/edit user
          $this->m_user->update_user($login,$namauser);

          //delete user_priv
          $this->m_user->delete_user_priv($login);
          
          $arr_chk= explode(',', $arrchkakses);
          
          //insert user_priv
          foreach ($arr_chk  as $value) {
            $this->m_user->save_user_priv($login,$value,'1');
          }

          $login_encr = encrypt_url($login);          
          $jenis_log   = "edit";          
          $note_log    = $login." | ".date('Y-m-d H:i:s');          
          $this->_module->gen_history($sub_menu, $login, $jenis_log, $note_log, $username);          
          $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
        }else{
          //insert/add user
          $this->m_user->save_user($login,$password,$namauser,$tanggaldibuat);
          
          //delete user_priv
          $this->m_user->delete_user_priv($login);

          $arr_chk= explode(',', $arrchkakses);

          //insert user_priv
          foreach ($arr_chk  as $value) {
            $this->m_user->save_user_priv($login,$value,'1');
          }

          $login_encr = encrypt_url($login);
          $jenis_log   = "create";
          $note_log    = $namauser.' | '.$tanggaldibuat;
          $this->_module->gen_history($sub_menu, $login, $jenis_log, $note_log, $username);          
          $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $login_encr, 'icon' =>'fa fa-check', 'type' => 'success');
        }
      }

      //unlock table
      $this->_module->unlock_tabel();
    }

    echo json_encode($callback);

  }

  public function edit($id = null)
  {
    if(!isset($id)) show_404();
      $kode_decrypt       = decrypt_url($id);
      $data['id_dept']    ='MUSR';
      $priv_record        = $this->m_user->get_priv_by_username($kode_decrypt);
      $list_priv          = '';
      foreach ($priv_record as $value) {
        if ($list_priv == ''){
          $list_priv ='###' . $value->main_menu_sub_kode . ',';
        }else{
          $list_priv = $list_priv . $value->main_menu_sub_kode . ',';
        }
      }
      $data['user']       = $this->m_user->get_user_by_username($kode_decrypt);
      $data['priv']       = $list_priv;
      //$data['category'] = $this->m_produk->get_list_category();
      //$data['route']    = $this->m_produk->get_list_route();        
      //$data['dyest']    = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'DYE');
      //$data['aux']      = $this->m_lab->get_data_dye_aux_by_code($kode_decrypt,'AUX');
      return $this->load->view('setting/v_user_edit',$data);
  }



  public function help_mo_done()
  {
    
    $list = $this->m_user->get_list_mo_correction()->result();
    foreach($list as $val){
      $kode_mo = $val->mo;
      $mv = $this->m_user->get_move_id_by_kode($kode_mo)->row_array();
      $move_id =$mv['move_id'];

      $dept = $this->m_user->get_dept_by_kode($kode_mo)->row_array();
      $dept_id =$dept['dept_id'];
      $status  = $dept['status'];

      $rm = $this->m_user->cek_stock_move_items($move_id)->row_array();
      if($rm['move_id'] == ''){
        $bahan_baku = 'FALSE';
      }else{
        $bahan_baku = 'TRUE';
      }
      
      $sql_updt_move = "UPDATE mo_correction SET move_id = '".$move_id."', dept_id = '".$dept_id."', rm = '".$bahan_baku."', status = '".$status."' WHERE mo = '".$kode_mo."' ";

      $this->_module->update_perbatch($sql_updt_move);
    }

    $callback = array('status' => 'success', 'message' => 'Berhasil !', 'icon' =>'fa fa-check', 'type' => 'success');

    echo json_encode($callback);
    
  }

}