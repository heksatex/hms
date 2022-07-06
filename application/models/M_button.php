<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_button extends CI_Model
{
	public function view_button($username, $inisial_class,$dept_id)
	{
		$query= $this->db->query("SELECT mmsb.*
							FROM user_priv up
							INNER JOIN main_menu_sub mms ON up.main_menu_sub_kode=mms.kode
							INNER JOIN main_menu_sub_button mmsb ON mms.kode = mmsb.main_menu_sub_kode
							WHERE up.username = '".$username."' AND mms.inisial_class = '".$inisial_class."' 
								  AND mmsb.kategori_button = 'view_button' AND mms.dept_id = '".$dept_id."'
							ORDER BY mmsb.row_order ");
		return $query->result();
	}

	public function form_button($username, $inisial_class,$dept_id)
	{
		$query= $this->db->query("SELECT mmsb.*
							FROM user_priv up
							INNER JOIN main_menu_sub mms ON up.main_menu_sub_kode=mms.kode
							INNER JOIN main_menu_sub_button mmsb ON mms.kode = mmsb.main_menu_sub_kode
							WHERE up.username = '".$username."' AND mms.inisial_class = '".$inisial_class."' 
								  AND mmsb.kategori_button = 'form_button' AND mms.dept_id = '".$dept_id."'
							GROUP BY mmsb.jenis_button
							ORDER BY mmsb.row_order ");
		return $query->result();
	}


	public function form_button_print($inisial_class,$dept_id)
	{
		$query= $this->db->query("SELECT mmsb.*
							FROM user_priv up
							INNER JOIN main_menu_sub mms ON up.main_menu_sub_kode=mms.kode
							INNER JOIN main_menu_sub_button mmsb ON mms.kode = mmsb.main_menu_sub_kode
							WHERE mms.inisial_class = '".$inisial_class."' 
								  AND mmsb.kategori_button = 'form_button' AND mms.dept_id = '".$dept_id."' AND mmsb.jenis_button = 'print'
							GROUP BY mmsb.jenis_button
							ORDER BY mmsb.row_order ");
		return $query->result();
	}
	
}

?>