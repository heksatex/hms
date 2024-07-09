<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_reportAdjustment extends CI_Model
{

	function craete_where($kode_adjustment,$nama_produk,$type_adjustment,$user,$notes,$lot)
	{
		$where = '';
		if($kode_adjustment){
			$where .= " AND b.kode_adjustment LIKE '%".$kode_adjustment."%'  " ;
		}
		if($nama_produk){
			$where .= " AND mp.nama_produk LIKE '%".$nama_produk."%' ";
		}
		if($type_adjustment){
			$where .= " AND a.id_type_adjustment = '".$type_adjustment."'  ";
		}
		if($user){
			$where .= " AND a.nama_user LIKE '%".$user."%'  ";
		}
		if($notes){
			$where .= " AND a.note LIKE '%".$notes."%'  ";
		}
		if($lot){
			$where .= " AND b.lot LIkE '%".$lot."%'" ;
		}

		return $where;
	}
	
	public function get_list_group_nama_produk_adj_in_by_kode($lokasi, $tgl_dari, $tgl_sampai,$kode_adjustment,$nama_produk,$type_adjustment,$user,$notes,$lot)
	{
		$where = $this->craete_where($kode_adjustment,$nama_produk,$type_adjustment,$user,$notes,$lot);
		return $this->db->query("SELECT b.kode_produk, mp.nama_produk, count(lot) as tot_lot, IFNULL(sum(b.qty_move),0) as tot_qty_move , IFNULL(sum(b.qty_data),0) tot_qty_stock, IFNULL(sum(b.qty_adjustment),0) tot_qty1_adj,  IFNULL(sum(b.qty_data2),0) tot_qty2_stock, IFNULL(sum(b.qty_adjustment2),0) tot_qty2_adj, mp.uom,mp.uom_2, IFNULL(sum(b.qty2_move),0) as tot_qty2_move 
								FROM adjustment_items b 
								INNER JOIN adjustment a ON b.kode_adjustment = a.kode_adjustment
								INNER JOIN mst_produk mp ON b.kode_produk = mp.kode_produk
								INNER JOIN stock_move sm ON b.move_id = sm.move_id
								WHERE a.kode_lokasi = '$lokasi' AND a.create_date >= '$tgl_dari' AND a.create_date <= '$tgl_sampai' AND a.status = 'done' AND sm.lokasi_dari LIKE '%/Adj%' AND sm.lokasi_tujuan LIKE '%/Stock%' $where
								GROUP BY b.kode_produk
								ORDER BY mp.nama_produk asc	");
	}

	public function get_list_group_nama_produk_adj_out_by_kode($lokasi, $tgl_dari, $tgl_sampai,$kode_adjustment,$nama_produk,$type_adjustment,$user,$notes,$lot)
	{
		$where = $this->craete_where($kode_adjustment,$nama_produk,$type_adjustment,$user,$notes,$lot);
		return $this->db->query("SELECT b.kode_produk, mp.nama_produk, count(lot) as tot_lot, IFNULL(sum(b.qty_move),0) as tot_qty_move , IFNULL(sum(b.qty_data),0) tot_qty_stock, IFNULL(sum(b.qty_adjustment),0) tot_qty1_adj,  IFNULL(sum(b.qty_data2),0) tot_qty2_stock, IFNULL(sum(b.qty_adjustment2),0) tot_qty2_adj, mp.uom,mp.uom_2, IFNULL(sum(b.qty2_move),0) as tot_qty2_move 
								FROM adjustment_items b 
								INNER JOIN adjustment a ON b.kode_adjustment = a.kode_adjustment
								INNER JOIN mst_produk mp ON b.kode_produk = mp.kode_produk
								INNER JOIN stock_move sm ON b.move_id = sm.move_id
								WHERE a.kode_lokasi = '$lokasi' AND a.create_date >= '$tgl_dari' AND a.create_date <= '$tgl_sampai' AND a.status = 'done' AND sm.lokasi_dari LIKE '%/Stock%' AND sm.lokasi_tujuan LIKE '%/ADJ%' $where
								GROUP BY  b.kode_produk
								ORDER BY mp.nama_produk asc	");
	}

	public function get_list_item_adjustment_by_kode($kode_lokasi,$tgldari,$tglsampai,$data_isi,$where_adj,$kode_adjustment,$nama_produk,$type_adjustment,$user,$notes,$lot)
	{
		$where = $this->craete_where($kode_adjustment,$nama_produk,$type_adjustment,$user,$notes,$lot);
		return $this->db->query("SELECT b.kode_adjustment, a.create_date, b.kode_produk, mp.nama_produk, 
											 b.lot, b.qty_data, b.qty_move, b.uom, b.qty_adjustment, b.qty_data2, b.qty_adjustment2, b.uom2, a.nama_user, a.note, b.qty2_move, a.id_type_adjustment, mta.name_type
								FROM adjustment_items b 
								INNER JOIN adjustment a ON b.kode_adjustment = a.kode_adjustment
								INNER JOIN mst_produk mp ON b.kode_produk = mp.kode_produk
								INNER JOIN stock_move sm ON b.move_id = sm.move_id
								LEFT JOIN mst_type_adjustment mta ON a.id_type_adjustment = mta.id
								WHERE a.kode_lokasi = '$kode_lokasi' AND a.create_date >= '$tgldari' AND a.create_date <= '$tglsampai' AND b.kode_produk = '$data_isi' AND a.status = 'done' $where $where_adj
								ORDER BY mp.nama_produk asc");
	}

	public function get_jml_item_adjustment_in_by_kode($lokasi, $tgl_dari, $tgl_sampai,$kode_adjustment,$nama_produk,$type_adjustment,$user,$notes)
	{
		$where = $this->craete_where($kode_adjustment,$nama_produk,$type_adjustment,$user,$notes,$lot);
		$query =  $this->db->query("SELECT  count(lot) as tot_lot
								FROM adjustment_items b 
								INNER JOIN adjustment a ON b.kode_adjustment = a.kode_adjustment
								INNER JOIN mst_produk mp ON b.kode_produk = mp.kode_produk
								INNER JOIN stock_move sm ON b.move_id = sm.move_id
								WHERE a.kode_lokasi = '$lokasi' AND a.create_date >= '$tgl_dari' AND a.create_date <= '$tgl_sampai' AND a.status = 'done'  AND sm.lokasi_dari LIKE '%/Adj%' AND sm.lokasi_tujuan LIKE '%/Stock%' $where")->row_array();
		return $query['tot_lot'];
	}

	public function get_jml_item_adjustment_out_by_kode($lokasi, $tgl_dari, $tgl_sampai,$kode_adjustment,$nama_produk,$type_adjustment,$user,$notes)
	{
		$where = $this->craete_where($kode_adjustment,$nama_produk,$type_adjustment,$user,$notes,$lot);
		$query =  $this->db->query("SELECT  count(lot) as tot_lot
								FROM adjustment_items b 
								INNER JOIN adjustment a ON b.kode_adjustment = a.kode_adjustment
								INNER JOIN mst_produk mp ON b.kode_produk = mp.kode_produk
								INNER JOIN stock_move sm ON b.move_id = sm.move_id
								WHERE a.kode_lokasi = '$lokasi' AND a.create_date >= '$tgl_dari' AND a.create_date <= '$tgl_sampai' AND a.status = 'done' AND sm.lokasi_dari LIKE '%/Stock%' AND sm.lokasi_tujuan LIKE '%/ADJ%' $where")->row_array();
		return $query['tot_lot'];
	}

}