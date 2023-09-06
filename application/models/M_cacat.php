<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_cacat extends CI_Model
{

	public function get_list_departement_select2($nama)
	{
		return $this->db->query("SELECT kode,nama FROM departemen  WHERE show_dept = 'true' AND nama LIKE '%$nama%' ORDER BY nama ")->result();
	}


	public function get_list_mrp_select2($dept_id, $kode)
	{
		return $this->db->query("SELECT kode FROM mrp_production WHERE kode LIKE '%$kode%' AND dept_id = '$dept_id' ORDER BY tanggal desc LIMIT 100 ")->result();
	}

	public function get_list_lot_select2($mo,$lot)
	{
		return $this->db->query("SELECT kode,lot,quant_id FROM mrp_production_fg_hasil WHERE kode = '$mo' AND lot LIKE '%$lot%' ")->result();
	}


	public function get_mrp_production_by_kode($kode)
	{
		return $this->db->query("SELECT mrp.kode, mrp.nama_produk, mrp.origin, mrp.reff_note, m.nama_mesin
								 FROM mrp_production mrp
								 LEFT JOIN mesin m ON mrp.mc_id = m.mc_id AND mrp.dept_id = m.dept_id
								 WHERE mrp.kode = '$kode' ");
	}

	public function get_item_by_kode($kode,$quant_id)
	{
		return $this->db->query("SELECT fg.kode,  fg.nama_produk, fg.lot, fg.nama_grade, fg.qty, fg.uom, sq.reff_note, 
									sq.create_date
								FROM mrp_production_fg_hasil fg 
								INNER JOIN stock_quant sq ON fg.quant_id =  sq.quant_id
								WHERE fg.kode = '".$kode."' AND fg.quant_id = '".$quant_id."' ");
	}

	public function get_mrp_production_cacat_by_kode($kode,$quant_id)
	{
		return $this->db->query("SELECT kode_cacat, point_cacat FROM mrp_production_cacat WHERE kode = '$kode' AND quant_id = '$quant_id'")->result();
	}


	public function get_list_cacat_by_dept($dept_id)
	{
		return $this->db->query("SELECT kode_cacat, nama_cacat FROM mst_cacat WHERE dept_id = '$dept_id' order by id")->result();
	}

}
