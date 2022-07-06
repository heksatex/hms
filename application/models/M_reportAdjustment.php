<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_reportAdjustment extends CI_Model
{
	
	public function get_list_group_nama_produk_by_kode($lokasi, $tgl_dari, $tgl_sampai)
	{
		return $this->db->query("SELECT b.kode_produk, mp.nama_produk, count(lot) as tot_lot, IFNULL(sum(qty_move),0) as tot_qty_move , IFNULL(sum(qty_adjustment2),0) tot_qty2, mp.uom,mp.uom_2
								FROM adjustment_items b 
								INNER JOIN adjustment a ON b.kode_adjustment = a.kode_adjustment
								INNER JOIN mst_produk mp ON b.kode_produk = mp.kode_produk
								WHERE a.kode_lokasi = '$lokasi' AND a.create_date >= '$tgl_dari' AND a.create_date <= '$tgl_sampai' AND a.status = 'done'
								GROUP BY b.kode_produk
								ORDER BY mp.nama_produk asc	");
	}

	public function get_list_item_adjustment_by_kode($kode_lokasi,$tgldari,$tglsampai,$data_isi)
	{
		return $this->db->query("SELECT b.kode_adjustment, a.create_date, b.kode_produk, mp.nama_produk, 
											 b.lot, b.qty_move, b.uom, b.qty_adjustment2, b.uom2, a.nama_user, a.note
								FROM adjustment_items b 
								INNER JOIN adjustment a ON b.kode_adjustment = a.kode_adjustment
								INNER JOIN mst_produk mp ON b.kode_produk = mp.kode_produk
								WHERE a.kode_lokasi = '$kode_lokasi' AND a.create_date >= '$tgldari' AND a.create_date <= '$tglsampai' AND b.kode_produk = '$data_isi' AND a.status = 'done'
								ORDER BY mp.nama_produk asc");
	}

	public function get_jml_item_adjustment_by_kode($lokasi, $tgl_dari, $tgl_sampai)
	{
		$query =  $this->db->query("SELECT  count(lot) as tot_lot
								FROM adjustment_items b 
								INNER JOIN adjustment a ON b.kode_adjustment = a.kode_adjustment
								INNER JOIN mst_produk mp ON b.kode_produk = mp.kode_produk
								WHERE a.kode_lokasi = '$lokasi' AND a.create_date >= '$tgl_dari' AND a.create_date <= '$tgl_sampai' AND a.status = 'done'")->row_array();
		return $query['tot_lot'];
	}

}