<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_produksiWarpingPanjang extends CI_Model
{

	public function get_list_produksi_wrp_by_dept($id_dept,$where)
	{
		return $this->db->query("SELECT mp.kode, mp.tanggal, mp.nama_produk, mp.qty as qty_target, mp.reff_note, mp.status,ms.nama_mesin,
										(SELECT IFNULL(sum(mpfg.qty),0) as total_qty1
												 FROM mrp_production_fg_hasil mpfg
												 WHERE mpfg.kode = mp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = '$id_dept')) as hph_qty1,
												 
										(SELECT IFNULL(sum(mpfg.qty2),0) as total_qty2
												 FROM mrp_production_fg_hasil mpfg
												 WHERE mpfg.kode = mp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = '$id_dept')) as hph_qty2,
												 
										(SELECT IFNULL(mp.qty - IFNULL(sum(mpfg.qty),0),0) 
												 FROM mrp_production_fg_hasil mpfg
												 WHERE mpfg.kode = mp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = '$id_dept')) as sisa_target
								 		  
								FROM mrp_production mp
								LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id
								$where ORDER BY mp.tanggal asc")->result();
	}



	public function get_record_count_wrp($where){
		$query  = $this->db->query("SELECT count(*) as allcount 
									FROM mrp_production mp
									LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id
									  $where ");
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
	}

}