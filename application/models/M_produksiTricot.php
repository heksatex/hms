<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_produksiTricot extends CI_Model
{

	public function get_list_produksi_tricot_by_kode($where, $rowno, $recordPerPage)
	{
		return $this->db->query("SELECT ms.nama_mesin, mrp.kode, mrp.tanggal, pd.sales_order, pd.kode_prod, pd.nama_produk, pd.nama_sales_group, mrp.lebar_greige, mrp.uom_lebar_greige, 
								mrp.lebar_jadi, mrp.uom_lebar_jadi, mrp.start_time, mrp.finish_time, mrp.qty as target_mo, mrp.reff_note, mrp.status,
										(SELECT IFNULL(sum(mpfg.qty),0) as total_qty1
																				FROM mrp_production_fg_hasil mpfg
																				WHERE mpfg.kode = mrp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI')) as hph_qty1,
										(SELECT IFNULL(sum(mpfg.qty2),0) as total_qty2
												 FROM mrp_production_fg_hasil mpfg
												 WHERE mpfg.kode = mrp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI')) as hph_qty2,
 	   								    (SELECT count(mpfg.lot) as jml_lot 
													FROM mrp_production_fg_hasil mpfg													
													WHERE mpfg.kode = mrp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI') ) as gulung,
													
										(SELECT IFNULL(mrp.qty - IFNULL(sum(mpfg.qty),0),0) 
												 FROM mrp_production_fg_hasil mpfg
												 WHERE mpfg.kode = mrp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI')) as sisa_target
																				
								FROM  (select a.sales_order,a.mc_id, a.kode_produk, a.nama_produk, d.nama_sales_group, c.kode_prod, e.row_order
										FROM (SELECT sales_order, kode_produk, nama_produk, mc_id 
													FROM sales_contract_items WHERE nama_produk LIKE '%tricot%'
													) as a
										INNER JOIN sales_contract b ON a.sales_order = b.sales_order
										LEFT JOIN mst_sales_group d ON b.sales_group = d.kode_sales_group
										LEFT JOIN production_order c ON b.sales_order = c.sales_order
										LEFT JOIN production_order_items e ON c.kode_prod = e.kode_prod
										WHERE b.order_production = 'true' AND a.nama_produk LIKE '%tricot%' 
										GROUP BY a.sales_order, a.kode_produk
										) pd
								LEFT JOIN mrp_production mrp ON CONCAT(pd.sales_order,'|',pd.kode_prod)  = SUBSTRING_INDEX(mrp.origin,'|',2) AND pd.kode_produk = mrp.kode_produk
								LEFT JOIN mesin ms ON  ms.mc_id = IFNULL(mrp.mc_id,pd.mc_id)
								$where 
								ORDER BY ms.row_order asc, mrp.tanggal asc, cast(mid(pd.sales_order,3,(length(pd.sales_order))-2) as unsigned) asc
								LIMIT $rowno, $recordPerPage
								")->result();
	}

	public function get_list_produksi_tricot_by_kode_no_limit($where)
	{
		return $this->db->query("SELECT ms.nama_mesin, mrp.kode, mrp.tanggal, pd.sales_order, pd.kode_prod, pd.nama_produk, pd.nama_sales_group, mrp.lebar_greige, mrp.uom_lebar_greige, 
								mrp.lebar_jadi, mrp.uom_lebar_jadi, mrp.start_time, mrp.finish_time, mrp.qty as target_mo, mrp.reff_note, mrp.status,
										(SELECT IFNULL(sum(mpfg.qty),0) as total_qty1
																				FROM mrp_production_fg_hasil mpfg
																				WHERE mpfg.kode = mrp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI')) as hph_qty1,
										(SELECT IFNULL(sum(mpfg.qty2),0) as total_qty2
												FROM mrp_production_fg_hasil mpfg
												WHERE mpfg.kode = mrp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI')) as hph_qty2,
											(SELECT count(mpfg.lot) as jml_lot 
													FROM mrp_production_fg_hasil mpfg													
													WHERE mpfg.kode = mrp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI') ) as gulung,
													
										(SELECT IFNULL(mrp.qty - IFNULL(sum(mpfg.qty),0),0) 
												FROM mrp_production_fg_hasil mpfg
												WHERE mpfg.kode = mrp.kode AND mpfg.lokasi NOT IN (SELECT d.waste_location FROM departemen d WHERE d.kode = 'TRI')) as sisa_target
																				
								FROM  (select a.sales_order,a.mc_id, a.kode_produk, a.nama_produk, d.nama_sales_group, c.kode_prod, e.row_order
										FROM (SELECT sales_order, kode_produk, nama_produk, mc_id 
													FROM sales_contract_items WHERE nama_produk LIKE '%tricot%'
													) as a
										INNER JOIN sales_contract b ON a.sales_order = b.sales_order
										LEFT JOIN mst_sales_group d ON b.sales_group = d.kode_sales_group
										LEFT JOIN production_order c ON b.sales_order = c.sales_order
										LEFT JOIN production_order_items e ON c.kode_prod = e.kode_prod
										WHERE b.order_production = 'true' AND a.nama_produk LIKE '%tricot%' 
										GROUP BY a.sales_order, a.kode_produk
										) pd
								LEFT JOIN mrp_production mrp ON CONCAT(pd.sales_order,'|',pd.kode_prod)  = SUBSTRING_INDEX(mrp.origin,'|',2) AND pd.kode_produk = mrp.kode_produk
								LEFT JOIN mesin ms ON  ms.mc_id = IFNULL(mrp.mc_id,pd.mc_id)
								$where 
								ORDER BY ms.row_order asc, mrp.tanggal asc, cast(mid(pd.sales_order,3,(length(pd.sales_order))-2) as unsigned) asc ")->result();
	}

	public function get_record_count_tricot($where)
	{
		$query  = $this->db->query("SELECT count(pd.sales_order) as allcount
																					
									FROM  (select a.sales_order,a.mc_id, a.kode_produk, a.nama_produk, d.nama_sales_group, c.kode_prod, e.row_order
										FROM (SELECT sales_order, kode_produk, nama_produk, mc_id 
													FROM sales_contract_items WHERE nama_produk LIKE '%tricot%'
													) as a
										INNER JOIN sales_contract b ON a.sales_order = b.sales_order
										LEFT JOIN mst_sales_group d ON b.sales_group = d.kode_sales_group
										LEFT JOIN production_order c ON b.sales_order = c.sales_order
										LEFT JOIN production_order_items e ON c.kode_prod = e.kode_prod
										WHERE b.order_production = 'true' AND a.nama_produk LIKE '%tricot%' 
										GROUP BY a.sales_order, a.kode_produk
											) pd
									LEFT JOIN mrp_production mrp ON CONCAT(pd.sales_order,'|',pd.kode_prod)  = SUBSTRING_INDEX(mrp.origin,'|',2) AND pd.kode_produk = mrp.kode_produk
									LEFT JOIN mesin ms ON  ms.mc_id = IFNULL(mrp.mc_id,pd.mc_id)
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
		return $this->db->query("SELECT rm.nama_produk,rm.qty as target_qty, rm.reff_note
									-- (SELECT bi.note FROM bom_items bi WHERE bi.kode_produk = rm.kode_produk AND bi.row_order = rm.row_order AND bi.kode_bom = mrp.kode_bom ) as reff
								FROM mrp_production_rm_target rm 
								INNER JOIN mst_produk mp ON mp.kode_produk = rm.kode_produk 
								INNER JOIN mrp_production mrp ON mrp.kode = rm.kode
								WHERE rm.kode = '$kode' AND mp.id_category NOT IN ('11','12') AND mp.type = 'stockable' ORDER BY  rm.row_order" )->result();
	}


}