<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Warehouse extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("_module");//load modul global

	}

	public function index()
	{
		$kode_sub   = 'mm_warehouse';
		$username	= $this->session->userdata('username');
		$row 		= $this->_module->sub_menu_default($kode_sub,$username)->row_array();
		redirect($row['link_menu']);
	}
	
}


?>