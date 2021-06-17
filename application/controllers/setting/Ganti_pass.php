<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Ganti_pass extends MY_Controller
{
  public function __construct()
  {
    parent::__construct();
    $this->is_loggedin();//cek apakah user sudah login
    $this->load->model("m_gantiPass");//load model
    $this->load->model("_module");
  }

  public function index()
  {
    $data['id_dept']='CHPASS';    
    redirect('setting/ganti_pass/edit');
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
      //$this->_module->lock_tabel('mst_produk WRITE, mst_category WRITE, user WRITE, main_menu_sub WRITE, log_history WRITE');      
                
      $login                = addslashes($this->input->post('login'));
      $passwordlama         = addslashes($this->input->post('passwordlama'));
      $passwordbaru         = addslashes($this->input->post('passwordbaru'));
      $ulangipasswordbaru   = addslashes($this->input->post('ulangipasswordbaru'));
      $passwordbaruencr     = md5($passwordbaru);
      $status               = addslashes($this->input->post('status'));
      $masihcekvaliadasi    = true;
      $pjg_password_baru    = strlen($passwordbaru); // minimal 6 karakter

      if(empty($passwordlama) and $masihcekvaliadasi == true){
        $callback = array('status' => 'failed', 'field' => 'password_lama', 'message' => 'Password Lama Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
        $masihcekvaliadasi    = false;
      }

      if(empty($passwordbaru) and $masihcekvaliadasi == true){
        $callback = array('status' => 'failed', 'field' => 'password_baru', 'message' => 'Password Baru Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
        $masihcekvaliadasi    = false;
      }

      if($pjg_password_baru < 6 and $masihcekvaliadasi == true){
        $callback = array('status' => 'failed', 'field' => 'password_baru', 'message' => 'Panjang Password Baru minimal 6 Karakter !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
        $masihcekvaliadasi    = false;
      }

      if(empty($ulangipasswordbaru) and $masihcekvaliadasi == true){
        $callback = array('status' => 'failed', 'field' => 'ulangi_password_baru', 'message' => 'Ulangi Password Baru Harus Diisi !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
        $masihcekvaliadasi    = false;
      }      

      if(($passwordbaru <> $ulangipasswordbaru) and $masihcekvaliadasi == true){
        $callback = array('status' => 'failed', 'field' => 'ulangi_password_baru', 'message' => 'Ulangi Password Baru Salah !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );
        $masihcekvaliadasi    = false;
      }      

      if ($masihcekvaliadasi == true){
        $where    =array(
          'username' => $login,
          'password' => md5($passwordlama)
        );
        $cek =$this->m_gantiPass->cek_login("user",$where)->num_rows();//cek apa username dan password sama
        if($cek>0){
          //update password
          $this->m_gantiPass->update_password($login,$passwordbaruencr);

          $login_encr = encrypt_url($login);          
          $jenis_log   = "edit";          
          $note_log    = $login."|".'Rubah Pasword';
          $this->_module->gen_history($sub_menu, $login, $jenis_log, $note_log, $username);          
          $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' =>'fa fa-check', 'type' => 'success');
        }else{
          //password lama salah;
          $callback = array('status' => 'failed', 'field' => 'namaproduk', 'message' => 'Password Lama Salah !', 'icon' =>'fa fa-warning', 
                'type' => 'danger'  );
        }
      }
  

      /*
      if(empty($namauser)){
        $callback = array('status' => 'failed', 'field' => 'namaproduk', 'message' => 'Nama User Harus Diisi !', 'icon' =>'fa fa-warning', 
              'type' => 'danger'  );             
      }else if(empty($login)){
        $callback = array('status' => 'failed', 'field' => 'kodeproduk', 'message' => 'Login Name Harus Diisi !', 'icon' =>'fa fa-warning', 
              'type' => 'danger'  );      
      }else{

        //cek login user apa sudah ada apa belum        
        $cek = $this->m_user->cek_user_by_login($login)->row_array();

        if(!empty($cek['username']) AND $status == 'edit'){
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
          $note_log    = $login."|".$tanggaldibuat;          
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
          $note_log    = '';
          $this->_module->gen_history($sub_menu, $login, $jenis_log, $note_log, $username);          
          $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $login_encr, 'icon' =>'fa fa-check', 'type' => 'success');
        }
      }

      //unlock table
      //$this->_module->unlock_tabel();
      */
    }

    echo json_encode($callback);

  }

  public function edit()
  {
    $data['id_dept']='CHPASS';
    $data['user']   = $this->m_gantiPass->get_user_by_username($this->session->userdata('username'));
    return $this->load->view('setting/v_gantipass', $data);
  }

}