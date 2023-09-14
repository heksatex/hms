<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_login');
    }

    function index() {
        $username = $this->session->userdata('username');
        if (!empty($this->session->userdata('status'))) {
            // user sudah login
            $row = $this->m_login->cek_menu_default($username)->row_array(); //mengambil data menu default
            redirect(base_url(($row['inisial_class'])));
        } else {
            // user belum login
            $this->load->view('v_login');
        }
    }

    function aksi_login() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $where = array(
            'username' => $username,
            'password' => md5($password)
        );
        $cek = $this->m_login->cek_login("user", $where)->row(); //cek apa username dan password sama

        if ($this->input->is_ajax_request()) {
            try {
                if (empty($cek)) {
                    throw new Exception("username dan password tidak cocok");
                }
                if (!$cek->aktif) {
                    throw new Exception("Status User Tidak aktif");
                }
                $row = $this->m_login->cek_nama($username)->row_array();
                $data_session = array(
                    'username' => $username,
                    'nama' => $row,
                    'status' => "login"
                );
                $this->session->set_userdata($data_session);
                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')
                        ->set_output(json_encode(array('message' => 'Berhasil','icon' => 'fa fa-check', 'type' => 'success')));
            } catch (Exception $ex) {
                $this->output->set_status_header(500)
                        ->set_content_type('application/json', 'utf-8')
                        ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
            }
            return;
        }

    }

    function logout() {
        $this->session->sess_destroy();
        redirect(base_url('login'));
    }
}

?>