<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_stock extends CI_Model
{

	public function get_list_stock_by($where_lokasi,$where,$order_by,$rowno,$recordPerPage)
	{
		return $this->db->query("SELECT quant_id, create_date, kode_produk, nama_produk, lot, nama_grade, qty, uom, qty2, uom2, lokasi_fisik, lokasi, reff_note, move_date, lebar_greige, uom_lebar_greige, lebar_jadi, uom_lebar_jadi, (datediff(now(), move_date) ) as umur FROM stock_quant WHERE $where_lokasi $where $order_by LIMIT $rowno, $recordPerPage  ");
	}


	public function get_list_stock_by_noLimit($where,$order_by)
	{
		return $this->db->query("SELECT quant_id, create_date, kode_produk, nama_produk, lot, nama_grade, qty, uom, qty2, uom2, lokasi_fisik, lokasi, reff_note, move_date, lebar_greige, uom_lebar_greige, lebar_jadi, uom_lebar_jadi, (datediff(now(), move_date) ) as umur FROM stock_quant WHERE  $where $order_by ");
	}
	/*
	SELECT COUNT(barcode_id) as 'jml', (((datediff(now(), tgl) DIV 30) + 1) * 30) as timegroup FROM stock_kain_finish WHERE kode_lokasi<>'VR.INS' AND kode_lokasi<>'ADJ' GROUP BY timegroup
	*/

	public function get_list_stock_grouping($where_lokasi,$groupBy,$where)
	{
		return $this->db->query("SELECT $groupBy as nama_field, concat($groupBy,' (',count(*),')') as grouping, sum(qty) as 'tqty'  from stock_quant WHERE $where_lokasi $where group by $groupBy ")->result();
	}

	public function get_record_stock($where_lokasi,$where)
	{
		$query  = $this->db->query("SELECT count(*) as allcount FROM stock_quant WHERE $where_lokasi $where ");
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
	}

	public function get_list_departement_stock()
	{
		return $this->db->query("SELECT DISTINCT stock_location  FROM departemen ORDER BY nama ")->result();
	}

}