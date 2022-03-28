<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_produksiTricot extends CI_Model
{

	public function get_list_produksi_tricot_by_kode($where)
	{
		return $this->db->query("SELECT mp.kode, mp.tanggal, mp.origin, mp.nama_produk, mp.start_time, mp.finish_time, mp.qty as meter, mp.reff_note, mp.status, mp.lebar_greige, mp.uom_lebar_greige, mp.lebar_jadi, mp.uom_lebar_jadi,
										(SELECT IFNULL(sum(mpfg.qty),0) as total_qty1
												 FROM mrp_production_fg_hasil mpfg
												 WHERE mpfg.kode = mp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI')) as hph_qty1,
												 
										(SELECT IFNULL(sum(mpfg.qty2),0) as total_qty2
												 FROM mrp_production_fg_hasil mpfg
												 WHERE mpfg.kode = mp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI')) as hph_qty2,
 	   								    (SELECT count(mpfg.lot) as jml_lot 
													FROM mrp_production_fg_hasil mpfg													
													WHERE mpfg.kode = mp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI') ) as gulung,
													
										(SELECT IFNULL(mp.qty - IFNULL(sum(mpfg.qty),0),0) 
												 FROM mrp_production_fg_hasil mpfg
												 WHERE mpfg.kode = mp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI')) as sisa_target,
										 
												ms.nama_mesin
								FROM mrp_production mp
								LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id
								$where ORDER BY mp.tanggal asc ")->result();
	}

	public function get_recoord_count_tricot($where)
	{
		$query  = $this->db->query("SELECT count(*) as allcount 
									FROM mrp_production mp
									LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id
									  $where ");
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
	}

	public function get_marketing_by_kode($sc)
	{
		$query =  $this->db->query("SELECT mst.nama_sales_group as sales_group 
									FROM sales_contract sc 
									INNER JOIN mst_sales_group mst ON sc.sales_group =mst.kode_sales_group
									Where sc.sales_order = '$sc'");
		$result = $query->result_array();
		return $result[0]['sales_group'];
	}

	public function get_list_bahan_baku_by_kode($kode)
	{
		return $this->db->query("SELECT rm.nama_produk,rm.qty as target_qty,
									(SELECT bi.note FROM bom_items bi WHERE bi.kode_produk = rm.kode_produk AND bi.row_order = rm.row_order AND bi.kode_bom = mrp.kode_bom ) as reff
								FROM mrp_production_rm_target rm 
								INNER JOIN mst_produk mp ON mp.kode_produk = rm.kode_produk 
								INNER JOIN mrp_production mrp ON mrp.kode = rm.kode
								WHERE rm.kode = '$kode' AND mp.id_category NOT IN ('11','12') AND mp.type = 'stockable' ORDER BY  rm.row_order" )->result();
	}


}