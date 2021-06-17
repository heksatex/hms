<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller 
{
  public function __construct()
  {
    parent::__construct();
    $this->load->library('session');
    $this->load->model(array('m_menu'));
    $this->load->model(array('m_button'));
    $this->load->helper(array('url'));
    $this->load->library("form_validation");
    $this->load->library('encryption');//untuk enkrip url

  }

  public function is_loggedin()
  {
    if (!$this->session->userdata('status')) {
       // user belum login
     redirect(base_url());
    }
  }

  
}