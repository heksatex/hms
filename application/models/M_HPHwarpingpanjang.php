<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_HPHwarpingpanjang extends CI_Model
{

	public function get_list_WRP_tricot_by_kode($where)
	{	
		return $this->db->query("SELECT mp.kode, mp.origin, mpfg.kode_produk, mpfg.nama_produk, mp.origin,sq.reff_note,
								 mpfg.lot, mpfg.qty, mpfg.uom, mpfg.qty2, mpfg.uom2, mpfg.create_date as tgl_hph, mpfg.nama_grade,
								 ms.nama_mesin, mpfg.nama_user,mpfg.lokasi,sq.reff_note as reff_note_sq,adj.lot as lot_adj

								FROM mrp_production mp
								INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode
								INNER JOIN stock_quant sq ON mpfg.quant_id = sq.quant_id
								LEFT JOIN (SELECT IFNULL(adji.lot,'') as lot ,adji.quant_id FROM adjustment_items adji 
									INNER JOIN adjustment adj ON adji.kode_adjustment = adj.kode_adjustment
									where adj.status = 'done'   ) as adj ON adj.quant_id = mpfg.quant_id 
								LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id 
								$where ORDER BY mpfg.create_date asc
								")->result();
	}



	public function get_record_hph_wrp($where)
	{	
		$query = $this->db->query("SELECT count(*) as allcount
								FROM mrp_production mp
								INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode
								INNER JOIN stock_quant sq ON mpfg.quant_id = sq.quant_id
								LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id 
								$where
								");
		$result = $query->result_array();      
      	return $result[0]['allcount'];
	}

}