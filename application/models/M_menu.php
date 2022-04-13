<?php 
defined('BASEPATH') OR exit ('No direct Script Acces allowed');

/**
 * 
 */
class M_menu extends CI_Model
{
	
	public function main_menu($username)
	{
		$query= $this->db->query("SELECT DISTINCT(mm.nama), mm.kode, mm.inisial_class
                              FROM main_menu mm 
                              INNER JOIN user_priv up ON up.main_menu_kode=mm.kode 
							  WHERE up.username='".$username."' ORDER BY mm.row_order");

		return $query->result();
	}

	public function sub_main_menu($username,$main_menu)
	{
		$query = $this->db->query("SELECT mms.kode, mms.nama, mms.link_menu, mms.inisial_class, mms.ikon, mms.status_bar, mms.dept_id
                            FROM main_menu_sub mms
                            INNER JOIN user_priv up ON up.main_menu_sub_kode=mms.kode
                            INNER JOIN main_menu mm ON up.main_menu_kode = mm.kode
                            WHERE up.username= '".$username."' AND mm.inisial_class = '".$main_menu."' and mms.is_menu_sub = ''
                            ORDER BY mms.row_order");
		return $query->result();
	}


	public function status_bar($sub_menu)
	{
		$query = $this->db->query("SELECT status_bar FROM main_menu_sub WHERE inisial_class = '".$sub_menu."' ");
		return $query;
	}

	public function jenis_status_bar($sub_menu)
	{
		$query = $this->db->query("SELECT mmss.jenis_status, mmss.nama_status
									FROM main_menu_sub_status mmss
									INNER JOIN main_menu_sub mms ON mmss.main_menu_sub_kode = mms.kode
									Where mms.inisial_class = '".$sub_menu."'
									ORDER BY mmss.row_order ");
		return $query->result();

	}

	public function jenis_status_bar_deptid($sub_menu,$dept_id)
	{
		$query = $this->db->query("SELECT mmss.jenis_status, mmss.nama_status
									FROM main_menu_sub_status mmss
									INNER JOIN main_menu_sub mms ON mmss.main_menu_sub_kode = mms.kode
									Where mms.inisial_class = '".$sub_menu."' AND mms.dept_id = '$dept_id'
									ORDER BY mmss.row_order ");
		return $query->result();

	}

	/*
	public function log_history ($sub_menu, $kode)
	{
		$query = $this->db->query("SELECT lh.datelog, lh.jenis_log, lh.note, lh.nama_user
									FROM log_history lh
									INNER JOIN main_menu_sub mms ON lh.main_menu_sub_kode = mms.kode
									Where mms.inisial_class = '".$sub_menu."' AND lh.kode = '".$kode."' 
									ORDER BY lh.datelog desc");
		return $query->result();

	}
	*/

	public function log_history_new ($kode,$mms)
	{
		$query = $this->db->query("SELECT lh.datelog, lh.jenis_log, lh.note, lh.nama_user
									FROM log_history lh 
									WHERE lh.kode = '".$kode."' AND lh.main_menu_sub_kode = '".$mms."'
									ORDER BY lh.datelog desc");
		return $query->result();
	}

	public function log_history ($kode)
	{
		$query = $this->db->query("SELECT lh.datelog, lh.jenis_log, lh.note, lh.nama_user
									FROM log_history lh 
									WHERE lh.kode = '".$kode."' 
									ORDER BY lh.datelog desc");
		return $query->result();
	}


	public function get_inisial($id_dept, $inisial_menu, $inisial_menu_sub)
	{
		return $this->db->query("SELECT mms.inisial_class,mms.is_menu_sub  from main_menu_sub mms
								inner join main_menu_rel mmr on mms.kode=mmr.main_menu_sub_kode
								INNER JOIN main_menu mm ON mmr.main_menu_kode = mm.kode
								where mms.dept_id='".$id_dept."' and mm.inisial_class='".$inisial_menu."' AND mms.inisial_class = '".$inisial_menu_sub."'");
	}

}




 ?>