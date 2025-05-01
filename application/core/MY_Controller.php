<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model(array('m_menu'));
        $this->load->model(array('m_button'));
        $this->load->helper(array('url'));
        $this->load->library("form_validation");
        $this->load->library('encryption'); //untuk enkrip url
    }

    public function is_loggedin() {
        if (!$this->session->userdata('status')) {
            // user belum login
            if ($this->input->is_ajax_request() || $this->input->get_request_header('_request')) {
                $this->output->set_status_header(401)->set_content_type('application/json', 'utf-8');
            } else {
                redirect(base_url());
            }
        } else {
            $level = $this->session->userdata('nama')['level'] ?? "";
            if (!in_array(strtolower($level), ["super administrator"])) {
                $sub_menu = $this->uri->segment(2);
                if ($sub_menu !== null) {
                    $text = $this->session->userdata('menu');
                    $menu = decrypt_url($text);
                    $privilage = 0;
                    foreach (unserialize($menu) as $key => $value) {
                        if (strtolower($sub_menu) === strtolower($value->inisial_class)) {
                            $privilage++;
                            break;
                        }
                    }
                    if ($privilage === 0) {
                        if ($this->input->is_ajax_request() || $this->input->get_request_header('_request')) {
//                            $this->output->set_status_header(403)->set_content_type('application/json', 'utf-8')
//                                    ->set_output(json_encode(array('message' => 'Akses Dilarang', 'icon' => 'fa fa-warning', 'type' => 'danger', 'data' => [])));
                        } else {
                            show_404();
                        }
                    }
                }
            }
        }
    }
}
