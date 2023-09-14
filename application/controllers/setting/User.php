<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class User extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("m_user"); //load model m_user
        $this->load->model("_module");
    }

    public function index() {
        $data['id_dept'] = 'MUSR';
        $this->load->view('setting/v_user', $data);
    }

    function get_data() {
        $sub_menu = $this->uri->segment(2);
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
            $row[] = '<a href="' . base_url('setting/user/edit/' . $kode_encrypt) . '">' . $field->username . '</a>';
            $row[] = $field->level;
            $row[] = $field->nama_departemen;
            $row[] = $field->telepon_wa;
            $row[] = $field->aktif ? '<input class="switch_aktif switch-state-' . $field->username . '" type="checkbox" name="switch" value="' . $field->aktif . '" checked>' : '<input  class="switch_aktif switch-state-' . $field->username . '" type="checkbox" value="' . $field->aktif . '" name="switch">';
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

    public function add() {
        $data['id_dept'] = 'MUSR';
        $data['mst_sales_group'] = $this->_module->get_list_sales_group();
        $data['level_akses'] = $this->_module->get_list_level_akses();
        $data['departemen'] = $this->_module->get_list_departemen_all();

        $data['sales'] = $this->m_user->get_list_menu_by_link_menu('sales');
        $data['count_sales'] = $this->m_user->get_jml_list_menu_by_link_menu('sales');

        $data['ppic'] = $this->m_user->get_list_menu_by_link_menu('ppic');
        $data['count_ppic'] = $this->m_user->get_jml_list_menu_by_link_menu('ppic');

        $data['mo'] = $this->m_user->get_list_menu_by_link_menu('manufacturing');
        $data['count_mo'] = $this->m_user->get_jml_list_menu_by_link_menu('manufacturing');

        $data['warehouse'] = $this->m_user->get_list_menu_by_link_menu('warehouse');
        $data['count_warehouse'] = $this->m_user->get_jml_list_menu_by_link_menu('warehouse');

        $data['lab'] = $this->m_user->get_list_menu_by_link_menu('lab');
        $data['count_lab'] = $this->m_user->get_jml_list_menu_by_link_menu('lab');

        $data['report'] = $this->m_user->get_list_menu_by_link_menu('report');
        $data['count_report'] = $this->m_user->get_jml_list_menu_by_link_menu('report');

        $data['setting'] = $this->m_user->get_list_menu_by_link_menu('setting');
        $data['count_setting'] = $this->m_user->get_jml_list_menu_by_link_menu('setting');

        return $this->load->view('setting/v_user_add', $data);
    }

    public function simpan() {
        $sub_menu = $this->uri->segment(2);
        $username = $this->session->userdata('username');

        if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
            // session habis
            $callback = array('message' => 'Waktu Anda Telah Habis', 'sesi' => 'habis');
        } else {

            //lock table
            $this->_module->lock_tabel('user WRITE, user_priv WRITE, main_menu_rel WRITE, main_menu_sub WRITE, log_history WRITE, mst_sales_group WRITE, mst_departemen_all  WRITE');

            $namauser = addslashes($this->input->post('namauser'));
            $password = md5('123');
            $login = addslashes($this->input->post('login'));
            $tanggaldibuat = $this->input->post('tanggaldibuat');
            $arrchkakses = addslashes($this->input->post('arrchkakses'));
            $status = addslashes($this->input->post('status'));
            $departemen = addslashes($this->input->post('departemen'));
            $level = addslashes($this->input->post('level'));
            $sales_group = addslashes($this->input->post('sales_group'));
            $telepon_wa = addslashes($this->input->post('telepon_wa'));

            if (empty($namauser)) {
                $callback = array('status' => 'failed', 'field' => 'namauser', 'message' => 'Nama User Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else if (empty($login)) {
                $callback = array('status' => 'failed', 'field' => 'login', 'message' => 'Login Name Harus Diisi !', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else if (empty($departemen)) {
                $callback = array('status' => 'failed', 'field' => 'departemen', 'message' => 'Departemen Harus Diisi ! ', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else if (empty($level)) {
                $callback = array('status' => 'failed', 'field' => 'level', 'message' => 'Level Harus Diisi ! ', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else if (empty($arrchkakses)) {
                $callback = array('status' => 'failed', 'message' => 'Pilih Hak Akses Minimal 1 ! ', 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else {

                //cek login user apa sudah ada apa belum        
                $cek = $this->m_user->cek_user_by_login($login)->row_array();
                $nama_sales_group = $this->_module->get_nama_sales_Group_by_kode($sales_group);
                $nama_departemen = $this->m_user->get_nama_departemen_all_by_kode($departemen);

                if (!empty($cek['username']) AND $status == 'tambah') {
                    $callback = array('status' => 'failed', 'field' => 'note', 'message' => 'Login Name sudah dipakai !', 'icon' => 'fa fa-warning',
                        'type' => 'danger');
                } else if (!empty($cek['username'])) {
                    //update/edit user
                    $this->m_user->update_user($login, $namauser, $departemen, $level, $sales_group, $telepon_wa);

                    //delete user_priv
                    $this->m_user->delete_user_priv($login);

                    $arr_chk = explode(',', $arrchkakses);

                    //insert user_priv
                    foreach ($arr_chk as $value) {
                        $this->m_user->save_user_priv($login, $value, '1');
                    }
                    $nama_sales_group = $this->_module->get_nama_sales_Group_by_kode($sales_group);
                    $login_encr = encrypt_url($login);
                    $jenis_log = "edit";
                    $note_log = $login . " | " . $departemen . " | " . $level . " | " . $nama_sales_group;
                    $this->_module->gen_history($sub_menu, $login, $jenis_log, $note_log, $username);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'icon' => 'fa fa-check', 'type' => 'success');
                } else {
                    //insert/add user
                    $this->m_user->save_user($login, $password, $namauser, $tanggaldibuat, $departemen, $level, $sales_group, $telepon_wa);

                    //delete user_priv
                    $this->m_user->delete_user_priv($login);

                    $arr_chk = explode(',', $arrchkakses);

                    //insert user_priv
                    foreach ($arr_chk as $value) {
                        $this->m_user->save_user_priv($login, $value, '1');
                    }

                    $login_encr = encrypt_url($login);
                    $jenis_log = "create";
                    $note_log = $namauser . " | " . $departemen . " | " . $level . " | " . $nama_sales_group;
                    $this->_module->gen_history($sub_menu, $login, $jenis_log, $note_log, $username);
                    $callback = array('status' => 'success', 'message' => 'Data Berhasil Disimpan !', 'isi' => $login_encr, 'icon' => 'fa fa-check', 'type' => 'success');
                }
            }

            //unlock table
            $this->_module->unlock_tabel();
        }

        echo json_encode($callback);
    }

    public function edit($id = null) {
        if (!isset($id))
            show_404();
        $kode_decrypt = decrypt_url($id);
        $id_dept = 'MUSR';
        $priv_record = $this->m_user->get_priv_by_username($kode_decrypt);
        $data['id_dept'] = $id_dept;
        $data['mms'] = $this->_module->get_data_mms_for_log_history($id_dept); // get mms by dept untuk menu yg beda-beda
        $list_priv = '';
        foreach ($priv_record as $value) {
            if ($list_priv == '') {
                $list_priv = '###' . $value->main_menu_sub_kode . ',';
            } else {
                $list_priv = $list_priv . $value->main_menu_sub_kode . ',';
            }
        }
        $data['mst_sales_group'] = $this->_module->get_list_sales_group();
        $data['level_akses'] = $this->_module->get_list_level_akses();
        $data['departemen'] = $this->_module->get_list_departemen_all();

        $data['user'] = $this->m_user->get_user_by_username($kode_decrypt);
        $data['priv'] = $list_priv;

        $data['sales'] = $this->m_user->get_list_menu_by_link_menu('sales');
        $data['count_sales'] = $this->m_user->get_jml_list_menu_by_link_menu('sales');

        $data['ppic'] = $this->m_user->get_list_menu_by_link_menu('ppic');
        $data['count_ppic'] = $this->m_user->get_jml_list_menu_by_link_menu('ppic');

        $data['mo'] = $this->m_user->get_list_menu_by_link_menu('manufacturing');
        $data['count_mo'] = $this->m_user->get_jml_list_menu_by_link_menu('manufacturing');

        $data['warehouse'] = $this->m_user->get_list_menu_by_link_menu('warehouse');
        $data['count_warehouse'] = $this->m_user->get_jml_list_menu_by_link_menu('warehouse');

        $data['lab'] = $this->m_user->get_list_menu_by_link_menu('lab');
        $data['count_lab'] = $this->m_user->get_jml_list_menu_by_link_menu('lab');

        $data['report'] = $this->m_user->get_list_menu_by_link_menu('report');
        $data['count_report'] = $this->m_user->get_jml_list_menu_by_link_menu('report');

        $data['setting'] = $this->m_user->get_list_menu_by_link_menu('setting');
        $data['count_setting'] = $this->m_user->get_jml_list_menu_by_link_menu('setting');

        return $this->load->view('setting/v_user_edit', $data);
    }

    public function set_aktif() {
        try {
            $sub_menu = $this->uri->segment(2);
            $username = $this->session->userdata('username');

            if (empty($this->session->userdata('status'))) {//cek apakah session masih ada
                // session habis
                throw new Exception("Waktu Anda Telah Habis");
            }
            $users = explode('-', $this->input->post('users'));
            $val = $this->input->post('aktif');
            if (!$this->m_user->set_aktif_user(end($users), $val)) {
                throw new \Exception("Gagal Merubah Aktif User");
            }
            $jenis_log = "edit";

            $note_log = end($users) . "| ";
            $note_log .= $val ? 'Set Aktif' : 'Set Non Aktif';
            $this->_module->gen_history($sub_menu, end($users), $jenis_log, $note_log, $username);
            $this->output->set_status_header(200)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
         
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                    ->set_content_type('application/json', 'utf-8')
                    ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}
