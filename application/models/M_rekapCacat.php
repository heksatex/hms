<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_rekapCacat extends CI_Model
{

	public function get_list_rekap_cacat_by_dept($where)
	{
		return $this->db->query("SELECT  mp.kode, mp.tanggal, mp.origin, mp.kode_produk, mp.nama_produk, mpfg.quant_id, ms.nama_mesin, mpfg.create_date, mpfg.lot, mpfg.qty, mpfg.uom, mpfg.qty2, mpfg.uom2, mpfg.nama_grade
								 FROM mrp_production mp 
								 LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id
								 INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode
								 $where
								 order by mpfg.create_date asc
								 ")->result();
	}

	public function get_list_cacat_by_kode($kode,$lot,$quant_id)
	{
		return $this->db->query("SELECT  mpc.point_cacat, mpc.kode_cacat, mc.nama_cacat, mpc.nama_user
								FROM mrp_production_cacat mpc
								LEFT JOIN mst_cacat mc ON mpc.kode_cacat = mc.kode_cacat AND mpc.dept_id = mc.dept_id
								WHERE mpc.kode = '$kode' AND mpc.lot = '$lot' AND mpc.quant_id = '$quant_id' order by mpc.row_order asc ")->result();
	}


}