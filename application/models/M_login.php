<?php 
defined('BASEPATH') OR exit ('No direct Script Acces allowed');

class M_login extends CI_Model{	
	function cek_login($table,$where)
	{		
		return $this->db->get_where($table,$where);
	}

	function cek_nama($username)
	{
		return $query = $this->db->query("SELECT * FROM user WHERE username  = '".$username."'");
	}	

    function cek_menu_default($username)
	{
		return $query = $this->db->query("SELECT mm.inisial_class
                              FROM main_menu mm 
                              INNER JOIN user_priv up ON up.main_menu_kode=mm.kode 
							  WHERE up.username='".$username."' ORDER BY mm.row_order LIMIT 1");


	}

}


?>