<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_printMO extends CI_Model
{

	public function get_mrp_production_by_kode($kode)
	{
		return $this->db->query("SELECT mrp.tanggal, mrp.origin, mrp.kode, mrp.nama_produk, mrp.origin, mrp.reff_note, m.nama_mesin, mrp.lot_prefix, mrp.qty, mrp.uom, mrp.target_efisiensi
								 FROM mrp_production mrp
								 LEFT JOIN mesin m ON mrp.mc_id = m.mc_id AND mrp.dept_id = m.dept_id
								 WHERE mrp.kode = '$kode' ");
	}

	public function get_list_cacat_by_dept($dept_id)
	{
		return $this->db->query("SELECT kode_cacat, nama_cacat FROM mst_cacat WHERE dept_id = '$dept_id' order by id")->result();
	}
}