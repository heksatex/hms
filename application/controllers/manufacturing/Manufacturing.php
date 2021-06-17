<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Manufacturing extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("m_mo");//load query" di model m_mo
		$this->load->model("_module");//load model umum

	}

	public function index()
	{
		$kode_sub   = 'mm_manufacturing';
		$username	= $this->session->userdata('username');
		$row 		= $this->_module->sub_menu_default($kode_sub,$username)->row_array();
		redirect($row['link_menu']);
	}
	
}


?>