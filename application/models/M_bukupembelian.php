<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */

class M_bukupembelian extends CI_Model
{
  	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

    var $column_where_like = array('invd.kode_produk','invd.nama_produk');

    function get_list_buku_pembelian(array $where = [], array $where_or = [])
    {

        if(count($where_or) > 0){
            $this->db->group_start();
            $i = 0;
            foreach($this->column_where_like as $col){
                foreach($where_or as $field) {
                    $clean = $this->db->escape_like_str($field);
                    if($i == 0){
                        $this->db->like($col,$clean,'both', FALSE );
                    } else {
                        $this->db->or_like($col,$clean,'both', FALSE );
                    }
                    $i++;
                }
            }
            $this->db->group_end();
        }

        if(count($where) > 0){
            $this->db->where($where);
        }

        $this->db->order_by("created_at", "asc");
        $this->db->SELECT(" inv.no_invoice,
                            inv.created_at,
                            inv.origin,
                            inv.no_po,
                            pod.warehouse,
                            d.nama as departemen,
                            p.nama as nama_partner,
                            CONCAT('[', invd.kode_produk, '] ', invd.nama_produk) AS uraian,
                            invd.qty_beli,
                            invd.uom_beli,
                            c.currency,
                            inv.nilai_matauang,
                            invd.harga_satuan,
                            IFNULL(invd.qty_beli * invd.harga_satuan * inv.nilai_matauang, 0) AS dpp,
                            IFNULL((invd.qty_beli * invd.harga_satuan * inv.nilai_matauang) * 11/12 * invd.amount_tax, 0) AS ppn,
                            
                            inv.no_faktur_pajak,
                            inv.tanggal_fk");
        $this->db->from("invoice inv");
        $this->db->JOIN("invoice_detail invd", "inv.id = invd.invoice_id","INNER");
        $this->db->JOIN("currency_kurs c", "inv.matauang = c.id", "LEFT");
        $this->db->JOIN("partner p","p.id  = inv.id_supplier","LEFT");
        $this->db->JOIN("(
                        SELECT
                            po_no_po, 
                            kode_produk, 
                            GROUP_CONCAT(DISTINCT warehouse SEPARATOR ', ') AS warehouse
                        FROM purchase_order_detail
                        GROUP BY po_no_po, kode_produk
                    ) pod", "pod.po_no_po = inv.no_po AND pod.kode_produk = invd.kode_produk", "LEFT");
        $this->db->JOIN("departemen as d", "pod.warehouse = d.kode","LEFT");
        $query = $this->db->get();
        return $query->result();
	}


    
}