<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Report extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
	}

	public function index()
	{
		$kode_sub         = 'mm_report';
		$username		  = $this->session->userdata('username');
		$sub_menu_default = $this->db->query("SELECT mms.link_menu FROM user_priv up
							INNER JOIN main_menu_sub mms ON up.main_menu_sub_kode=mms.kode
							WHERE username='".$username."' AND main_menu_kode='".$kode_sub."' AND mms.link_menu LIKE '%/%'
							ORDER by mms.row_order LIMIT 1");
		$sub_menu_default2 = $this->db->query("SELECT mms.link_menu FROM user_priv up
							INNER JOIN main_menu_sub mms ON up.main_menu_sub_kode=mms.kode
							WHERE username='".$username."' AND main_menu_kode='".$kode_sub."' AND mms.is_menu_sub != ''
							ORDER by mms.row_order LIMIT 1");							

		foreach ($sub_menu_default->result_array() as $row) {			
			redirect($row['link_menu']);
		}

		if(empty($sub_menu_default->result_array())){
			foreach ($sub_menu_default2->result_array() as $row2) {
				redirect($row2['link_menu']);
			}
		}
	}
}


?>