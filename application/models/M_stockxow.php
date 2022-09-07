<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_stockxow extends CI_Model
{

	public function get_list_stock_by($where_lokasi,$where,$order_by,$rowno,$recordPerPage)
	{
		return $this->db->query("SELECT sq.quant_id, sq.create_date, sq.kode_produk, sq.nama_produk, sq.lot, sq.nama_grade, sq.qty, sq.uom, sq.qty2, sq.uom2, sq.lokasi_fisik, sq.lokasi, sq.reff_note, sq.move_date, sq.lebar_greige, sq.uom_lebar_greige, sq.lebar_jadi, sq.uom_lebar_jadi, (datediff(now(), sq.move_date) ) as umur , sq.sales_order, sq.sales_group, sg.nama_sales_group, sq.qty_opname, sq.uom_opname
								FROM stock_quant sq
								LEFT JOIN mst_sales_group sg ON sq.sales_group = sg.kode_sales_group
								WHERE $where_lokasi $where $order_by LIMIT $rowno, $recordPerPage  ");
	}


	public function get_list_stock_by_noLimit($where,$order_by)
	{
		return $this->db->query("SELECT sq.quant_id, sq.create_date, sq.kode_produk, sq.nama_produk, sq.lot, sq.nama_grade, sq.qty, sq.uom, sq.qty2, sq.uom2, sq.lokasi_fisik, sq.lokasi, sq.reff_note, sq.move_date, sq.lebar_greige, sq.uom_lebar_greige, sq.lebar_jadi, sq.uom_lebar_jadi, (datediff(now(), sq.move_date) ) as umur , sq.sales_order, sq.sales_group, sg.nama_sales_group, sq.qty_opname, sq.uom_opname
								FROM stock_quant sq
								LEFT JOIN mst_sales_group sg ON sq.sales_group = sg.kode_sales_group
								WHERE  $where $order_by ");
	}
	/*
	SELECT COUNT(barcode_id) as 'jml', (((datediff(now(), tgl) DIV 30) + 1) * 30) as timegroup FROM stock_kain_finish WHERE kode_lokasi<>'VR.INS' AND kode_lokasi<>'ADJ' GROUP BY timegroup
	*/

	public function get_list_stock_grouping($where_lokasi,$groupBy,$where,$order_by_in_group,$rowno, $recordPerPage)
	{
		return $this->db->query("SELECT $groupBy as nama_field, concat($groupBy,' (',count(*),')') as grouping, sum(qty) as tot_qty, sum(qty2) as tot_qty2, count(*) as total_items
								FROM stock_quant as sq 
								LEFT JOIN mst_sales_group sg ON sq.sales_group = sg.kode_sales_group 
								WHERE $where_lokasi $where 
								group by $groupBy $order_by_in_group
								LIMIT $rowno, $recordPerPage ")->result();
	}

	public function get_record_stock($where_lokasi,$where)
	{
		$query  = $this->db->query("SELECT count(*) as allcount FROM stock_quant as sq LEFT JOIN  mst_sales_group sg ON sq.sales_group = sg.kode_sales_group  WHERE $where_lokasi $where ");
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
	}

	public function get_record_stock_group($where_lokasi,$where,$groupBy)
	{
		$query  = $this->db->query("SELECT count(count1) as allcount 
									FROM 
										(SELECT count(*) as count1 
										FROM stock_quant as sq LEFT JOIN  mst_sales_group sg ON sq.sales_group = sg.kode_sales_group  
										WHERE $where_lokasi $where 
										GROUP BY $groupBy) gg	");
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
	}

	public function get_record_stock_all($where_lokasi,$where)
	{
		$query  = $this->db->query("SELECT sum(count1) as tot_lot, sum(tot_qty) as tot_all_qty, sum(tot_qty2) as tot_all_qty2 
									FROM 
										(SELECT count(*) as count1, sum(qty) as tot_qty, sum(qty2) as tot_qty2
										FROM stock_quant as sq LEFT JOIN  mst_sales_group sg ON sq.sales_group = sg.kode_sales_group  
										WHERE $where_lokasi $where 
										) gg	");
      	$result = $query->row_array();      
      	return $result;
	}

	public function get_list_departement_stock()
	{
		return $this->db->query("SELECT DISTINCT stock_location  FROM departemen ORDER BY stock_location ")->result();
	}

    public function get_list_greige_out($where,$order_by, $rowno, $recordPerPage, $groupBy1, $groupBy2)
    {

		if(!empty($groupBy1)){

			return $this->db->query("SELECT $groupBy2 as nama_field,  sum(tot_qty_planning)as  tot_qty_planning, sum(tot_qty) as tot_qty, sum(tot_qty2) as tot_qty2, 
									concat($groupBy2,' (',count(kode),')') as grouping
									FROM (
										SELECT  pb.kode, pb.origin, pbi.kode_produk, pbi.nama_produk,sum(pbi.qty) as tot_qty_planning,
										(select IFNULL(sum(qty),0) FROM stock_move_items WHERE move_id = pb.move_id AND kode_produk = pbi.kode_produk) tot_qty,
										(select IFNULL(sum(qty2),0) FROM stock_move_items WHERE move_id = pb.move_id AND kode_produk = pbi.kode_produk) tot_qty2
										FROM pengiriman_barang pb
										INNER JOIN pengiriman_barang_items pbi ON pb.kode = pbi.kode
										$where
										GROUP BY $groupBy1
										$order_by
									) pbi
									GROUP BY $groupBy2
									$order_by
									LIMIT $rowno, $recordPerPage");
		}else{
			return $this->db->query("SELECT  $groupBy2 as nama_field, pb.kode, pb.origin, pbi.kode_produk, pbi.nama_produk,sum(pbi.qty) as tot_qty_planning,
										(select IFNULL(sum(qty),0) FROM stock_move_items WHERE move_id = pb.move_id AND kode_produk = pbi.kode_produk) tot_qty,
										(select IFNULL(sum(qty2),0) FROM stock_move_items WHERE move_id = pb.move_id AND kode_produk = pbi.kode_produk) tot_qty2,
										concat(pb.kode,' (',(select count(lot) FROM stock_move_items WHERE move_id = pb.move_id AND kode_produk = pbi.kode_produk),')') as grouping,
										(select count(lot) FROM stock_move_items WHERE move_id = pb.move_id AND kode_produk = pbi.kode_produk) as tot_items
										FROM pengiriman_barang pb
										INNER JOIN pengiriman_barang_items pbi ON pb.kode = pbi.kode
										$where
										GROUP BY $groupBy2
										$order_by 
										LIMIT $rowno, $recordPerPage
									");

		}

        
    }

    public function get_record_list_greige_out($where,$groupBy)
	{
		$query  = $this->db->query("SELECT count(count1) as allcount
									FROM 
									(SELECT count(pbi.nama_produk) as count1
									FROM pengiriman_barang pb
									INNER JOIN pengiriman_barang_items pbi ON pb.kode = pbi.kode
									$where
									GROUP BY $groupBy
									) sas");
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
	}
	
	public function get_record_list_greige_out_all($where)
	{
		$query =  $this->db->query("SELECT sum(qty_planning) as tot_all_plan, sum(tot_qty) as tot_all_qty, sum(tot_qty2) as tot_all_qty2, sum(tot_lot) as tot_all_lot
								FROM
									(SELECT  pb.kode, pb.origin, pbi.kode_produk, pbi.nama_produk,sum(pbi.qty) as qty_planning,
										(select IFNULL(sum(qty),0) FROM stock_move_items WHERE move_id = pb.move_id AND kode_produk = pbi.kode_produk) tot_qty,
										(select IFNULL(sum(qty2),0) FROM stock_move_items WHERE move_id = pb.move_id AND kode_produk = pbi.kode_produk) tot_qty2,
										(select count(lot) FROM stock_move_items WHERE move_id = pb.move_id AND kode_produk = pbi.kode_produk) tot_lot
									FROM pengiriman_barang pb
									INNER JOIN pengiriman_barang_items pbi ON pb.kode = pbi.kode
									$where
									GROUP by pb.move_id
									) gp1");
		$result = $query->row_array();      
		return $result;
	}

	public function get_list_items_smi_greige_out($where,$order_by,$rowno, $recordPerPage)
	{
		return $this->db->query("SELECT pb.kode, pb.origin, pbi.kode_produk, pbi.nama_produk, pbi.qty as qty_plan, pbi.uom as uom_plan, smi.lot, smi.qty, smi.uom, sq.nama_grade, smi.qty2, smi.uom2
								FROM pengiriman_barang pb
								INNER JOIN pengiriman_barang_items pbi ON pb.kode = pbi.kode
								INNER JOIN stock_move_items smi ON pb.move_id = smi.move_id
								INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
								$where $order_by
								LIMIT $rowno, $recordPerPage")->result();
	}

	public function get_record_list_items_smi_greige_out($where)
	{
		$query  = $this->db->query("SELECT count(smi.lot) as allcount
									FROM pengiriman_barang pb
									INNER JOIN pengiriman_barang_items pbi ON pb.kode = pbi.kode
									INNER JOIN stock_move_items smi ON pb.move_id = smi.move_id
									INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
									$where 
									");
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
	}
	

}