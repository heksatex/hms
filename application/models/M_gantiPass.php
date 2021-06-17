<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_gantiPass extends CI_Model
{

	public function get_user_by_username($username)
	{
		return $this->db->query("SELECT * FROM user where username = '$username' ")->row();
	}

	function cek_login($table,$where)
	{		
		return $this->db->get_where($table,$where);
	}

	public function update_password($username,$passwordbaru)
	{
		return $this->db->query("UPDATE user set password = '$passwordbaru'WHERE username = '$username' ");
	}

}