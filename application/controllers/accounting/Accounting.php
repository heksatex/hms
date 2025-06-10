<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of Accounting
 *
 * @author RONI
 */
class Accounting extends MY_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
	}

	public function index()
	{
		$kode_sub         = 'mm_accounting';
		$username		  = $this->session->userdata('username');
		$sub_menu_default = $this->db->query("SELECT mms.link_menu FROM user_priv up
							INNER JOIN main_menu_sub mms ON up.main_menu_sub_kode=mms.kode
							WHERE username='{$username}' AND main_menu_kode='{$kode_sub}'
							ORDER by mms.row_order LIMIT 1");

		foreach ($sub_menu_default->result_array() as $row) {
			redirect($row['link_menu']);
		}

	}
}
