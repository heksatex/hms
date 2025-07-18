<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_stock extends CI_Model
{

	public function get_list_stock_by($where_lokasi,$where,$order_by,$rowno,$recordPerPage)
	{
		return $this->db->query("SELECT sq.quant_id, sq.create_date, sq.kode_produk, sq.nama_produk, sq.lot, sq.nama_grade, sq.qty, sq.uom, sq.qty2, sq.uom2, sq.lokasi_fisik, sq.lokasi, sq.reff_note, sq.move_date, sq.lebar_greige, sq.uom_lebar_greige, sq.lebar_jadi, sq.uom_lebar_jadi, (datediff(now(), sq.create_date) ) as umur , sq.sales_order, sq.sales_group, sg.nama_sales_group, sq.qty_opname, sq.uom_opname, cat.nama_category, sq.corak_remark, sq.warna_remark, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual, pl.no_pl,kp_lot.lot as lot_asal
							
								FROM stock_quant sq
								LEFT JOIN mst_sales_group sg ON sq.sales_group = sg.kode_sales_group
								INNER JOIN mst_produk mp ON sq.kode_produk = mp.kode_produk
								LEFT JOIN (SELECT spl.lot, spli.quant_id_baru as quant_id FROM split spl INNER JOIN split_items spli ON spl.kode_split = spli.kode_split
											UNION SELECT (SELECT GROUP_CONCAT(lot) as lot FROM join_lot_items where kode_join = jl.kode_join) as lot, jl.quant_id
											FROM join_lot jl WHERE jl.status = 'done' 
											UNION SELECT mrpin.lot, fg.quant_id FROM mrp_production_fg_hasil fg INNER JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
											UNION SELECT lot, quant_id FROM stock_kain_jadi_migrasi ) as  kp_lot ON kp_lot.quant_id = sq.quant_id
								LEFT JOIN mst_category cat ON mp.id_category = cat.id
								LEFT JOIN (SELECT no_pl, quant_id FROM picklist_detail where valid NOT IN ('cancel') ) pl ON pl.quant_id = sq.quant_id
								WHERE $where_lokasi $where $order_by LIMIT $rowno, $recordPerPage  ");
	}


	public function get_list_stock_by_noLimit($where,$order_by)
	{
		return $this->db->query("SELECT sq.quant_id, sq.create_date, sq.kode_produk, sq.nama_produk, sq.lot, sq.nama_grade, sq.qty, sq.uom, sq.qty2, sq.uom2, sq.lokasi_fisik, sq.lokasi, sq.reff_note, sq.move_date, sq.lebar_greige, sq.uom_lebar_greige, sq.lebar_jadi, sq.uom_lebar_jadi, (datediff(now(), sq.create_date) ) as umur , sq.sales_order, sq.sales_group, sg.nama_sales_group, sq.qty_opname, sq.uom_opname, cat.nama_category, sq.corak_remark, sq.warna_remark,sq.warna_remark, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual, pl.no_pl,kp_lot.lot as lot_asal
							
								FROM stock_quant sq
								LEFT JOIN mst_sales_group sg ON sq.sales_group = sg.kode_sales_group
								INNER JOIN mst_produk mp ON sq.kode_produk = mp.kode_produk
								LEFT JOIN (SELECT spl.lot, spli.quant_id_baru as quant_id FROM split spl INNER JOIN split_items spli ON spl.kode_split = spli.kode_split
											UNION SELECT (SELECT GROUP_CONCAT(lot) as lot FROM join_lot_items where kode_join = jl.kode_join) as lot, jl.quant_id
											FROM join_lot jl	WHERE jl.status = 'done' 
											UNION SELECT mrpin.lot, fg.quant_id FROM mrp_production_fg_hasil fg INNER JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
											UNION SELECT lot, quant_id FROM stock_kain_jadi_migrasi ) as  kp_lot ON kp_lot.quant_id = sq.quant_id
								LEFT JOIN mst_category cat ON mp.id_category = cat.id
								LEFT JOIN (SELECT no_pl, quant_id FROM picklist_detail where valid NOT IN ('cancel') ) pl ON pl.quant_id = sq.quant_id
								WHERE  $where $order_by ");
	}
	/*
	SELECT COUNT(barcode_id) as 'jml', (((datediff(now(), tgl) DIV 30) + 1) * 30) as timegroup FROM stock_kain_finish WHERE kode_lokasi<>'VR.INS' AND kode_lokasi<>'ADJ' GROUP BY timegroup
	*/

	public function get_list_stock_grouping($where_lokasi,$groupBy,$where,$order_by_in_group,$rowno, $recordPerPage)
	{
		return $this->db->query("SELECT $groupBy as nama_field, concat($groupBy,' (',count(*),')') as grouping, sum(qty) as tot_qty, sum(qty2) as tot_qty2
								FROM stock_quant as sq 
								LEFT JOIN  mst_sales_group sg ON sq.sales_group = sg.kode_sales_group 
								INNER JOIN mst_produk mp ON sq.kode_produk = mp.kode_produk
								LEFT JOIN (SELECT spl.lot, spli.quant_id_baru as quant_id FROM split spl INNER JOIN split_items spli ON spl.kode_split = spli.kode_split
											UNION SELECT (SELECT GROUP_CONCAT(lot) as lot FROM join_lot_items where kode_join = jl.kode_join) as lot, jl.quant_id
											FROM join_lot jl	WHERE jl.status = 'done' 
											UNION SELECT mrpin.lot, fg.quant_id FROM mrp_production_fg_hasil fg INNER JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
											UNION SELECT lot, quant_id FROM stock_kain_jadi_migrasi ) as  kp_lot ON kp_lot.quant_id = sq.quant_id
								LEFT JOIN mst_category cat ON mp.id_category = cat.id
								LEFT JOIN (SELECT no_pl, quant_id FROM picklist_detail where valid NOT IN ('cancel') ) pl ON pl.quant_id = sq.quant_id
								WHERE $where_lokasi $where 
								group by $groupBy $order_by_in_group
								LIMIT $rowno, $recordPerPage ")->result();
	}

	public function get_record_list_stock_grouping($where_lokasi,$groupBy,$where)
	{
		$query = $this->db->query("SELECT count(count1) as allcount 
								FROM ( SELECT count(sq.quant_id) as count1
									FROM stock_quant as sq 
									LEFT JOIN  mst_sales_group sg ON sq.sales_group = sg.kode_sales_group 
									INNER JOIN mst_produk mp ON sq.kode_produk = mp.kode_produk
									LEFT JOIN (SELECT spl.lot, spli.quant_id_baru as quant_id FROM split spl INNER JOIN split_items spli ON spl.kode_split = spli.kode_split
												UNION SELECT (SELECT GROUP_CONCAT(lot) as lot FROM join_lot_items where kode_join = jl.kode_join) as lot, jl.quant_id
												FROM join_lot jl	WHERE jl.status = 'done' 
												UNION SELECT mrpin.lot, fg.quant_id FROM mrp_production_fg_hasil fg INNER JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
												UNION SELECT lot, quant_id FROM stock_kain_jadi_migrasi ) as  kp_lot ON kp_lot.quant_id = sq.quant_id
									LEFT JOIN mst_category cat ON mp.id_category = cat.id
									LEFT JOIN (SELECT no_pl, quant_id FROM picklist_detail where valid NOT IN ('cancel') ) pl ON pl.quant_id = sq.quant_id
									WHERE $where_lokasi $where 
									group by $groupBy
								) gp
								 ");
		$result = $query->result_array();      
		return $result[0]['allcount'];
	}

	public function get_record_stock($where_lokasi,$where)
	{
		$query  = $this->db->query("SELECT count(*) as allcount FROM stock_quant as sq 
									LEFT JOIN  mst_sales_group sg ON sq.sales_group = sg.kode_sales_group 
									INNER JOIN mst_produk mp ON sq.kode_produk = mp.kode_produk
									LEFT JOIN (SELECT spl.lot, spli.quant_id_baru as quant_id FROM split spl INNER JOIN split_items spli ON spl.kode_split = spli.kode_split
											UNION SELECT (SELECT GROUP_CONCAT(lot) as lot FROM join_lot_items where kode_join = jl.kode_join) as lot, jl.quant_id
											FROM join_lot jl	WHERE jl.status = 'done' 
											UNION SELECT mrpin.lot, fg.quant_id FROM mrp_production_fg_hasil fg INNER JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
											UNION SELECT lot, quant_id FROM stock_kain_jadi_migrasi ) as  kp_lot ON kp_lot.quant_id = sq.quant_id
									LEFT JOIN mst_category cat ON mp.id_category = cat.id
									LEFT JOIN (SELECT no_pl, quant_id FROM picklist_detail where valid NOT IN ('cancel') ) pl ON pl.quant_id = sq.quant_id
									WHERE $where_lokasi $where ");
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
	}

	public function get_list_departement_stock()
	{
		return $this->db->query("SELECT DISTINCT stock_location  FROM departemen WHERE show_dept = 'true' ORDER BY stock_location ")->result();
	}

	public function get_list_departemen_outputlocation()
	{
		return $this->db->query("SELECT DISTINCT output_location FROM departemen WHERE show_dept = 'true'  ORDER BY output_location")->result();
	}

	

}