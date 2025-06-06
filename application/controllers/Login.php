<?php

defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Login extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('m_login');
        $this->load->model('m_global');
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
                $modelMenu = new $this->m_global;
                $menu = $modelMenu->setTables("user_priv")->setJoins("main_menu_sub", "kode = main_menu_sub_kode")
                                ->setWheres(["username" => $username])->setSelects(["inisial_class"])
                                ->setOrder(["row_order" => "asc"])->getData();
                $text = serialize($menu);
                $data_session = array(
                    'username' => $username,
                    'nama' => $row,
                    'status' => "login",
                    'menu' => encrypt_url($text)
                );
                $this->session->set_userdata($data_session);
                $this->output->set_status_header(200)
                        ->set_content_type('application/json', 'utf-8')
                        ->set_output(json_encode(array('message' => 'Berhasil', 'icon' => 'fa fa-check', 'type' => 'success')));
            } catch (Exception $ex) {
                $this->output->set_status_header(500)
                        ->set_content_type('application/json', 'utf-8')
                        ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
            }
            return;
        }
        if (!empty($cek)) {
            if (!$cek->aktif) {
                $this->session->set_flashdata('gagal', 'Status User Tidak aktif');
                redirect(base_url("login"));
            }
            //login berhasil
            $row = $this->m_login->cek_nama($username)->row_array(); //mengambil data nama 
            $modelMenu = new $this->m_global;
            $menu = $modelMenu->setTables("user_priv")->setJoins("main_menu_sub", "kode = main_menu_sub_kode")
                            ->setWheres(["username" => $username])->setSelects(["inisial_class"])
                            ->setOrder(["row_order" => "asc"])->getData();
            $text = serialize($menu);
            $data_session = array(
                'username' => $username,
                'nama' => $row,
                'status' => "login",
                'menu' => encrypt_url($text)
            );
            $row = $this->m_login->cek_menu_default($username)->row_array(); //mengambil data menu default

            $this->session->set_userdata($data_session);

            redirect(base_url($row['inisial_class']));
        } else {
            //login gagal;
            $this->session->set_flashdata('gagal', 'Username atau Password Salah !');
            redirect(base_url("login"));
        }
    }

    function logout() {
        $this->session->sess_destroy();
        redirect(base_url('login'));
    }
}

?>