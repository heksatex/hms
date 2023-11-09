<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_doneMO extends CI_Model
{

    function get_data_mrp_by_kode($dept,$tgl_dari,$tgl_sampai) // done MO realtime
    {
        return $this->db->query("SELECT mrp.kode, mrp.tanggal, d.nama as departemen, mrp.status
                        FROM mrp_production mrp
                        LEFT JOIN departemen d ON mrp.dept_id = d.kode
                        WHERE mrp.dept_id = '$dept' AND mrp.tanggal >= '$tgl_dari' AND mrp.tanggal <= '$tgl_sampai' AND mrp.status = 'done' ")->result();
    }

    function get_data_mrp_done_by_kode($dept,$tgl_dari,$tgl_sampai)
    {
        return $this->db->query("SELECT d.nama as departemen, mrp.kode, mrp.tanggal, mrp.dept_id, mrp.con_mtr, mrp.con_kg, mrp.prod_mtr, mrp.prod_kg, mrp.waste_mtr, mrp.waste_kg, mrp.adj_mtr, mrp.adj_kg, mrp.status
                                FROM mrp_production_done mrp 
                                INNER JOIN departemen d ON mrp.dept_id = d.kode 
                                WHERE mrp.dept_id = '$dept' AND mrp.tanggal >= '$tgl_dari' AND mrp.tanggal <= '$tgl_sampai' ")->result();
    }

    public function get_sum_qty_fg_adj_update($kode)
    {
        // tipe adjustment
		// 1=Koreksi MO, 2=Koreksi Salah INput User
        return $this->db->query("SELECT sum(mtr) as mtr, sum(kg) as kg
                            from (
                            SELECT smi.kode_produk, smi.nama_produk, 
                                        sum(if(mp.uom = 'Mtr',smi.qty,'')) as mtr,
                                        sum(if(mp.uom = 'kg', smi.qty, smi.qty2)) as kg
                            from mrp_production_fg_target  fgt
                            INNER JOIN mrp_production_fg_hasil fg ON fg.kode = fgt.kode AND fg.kode_produk = fgt.kode_produk
                            INNER JOIN adjustment_items adji ON fg.quant_id = adji.quant_id
                            INNER JOIN adjustment adj ON adji.kode_adjustment = adj.kode_adjustment
                            INNER JOIN stock_move_items smi ON adji.quant_id = smi.quant_id AND adji.move_id = smi.move_id
                            INNER JOIN mst_produk mp ON smi.kode_produk = mp.kode_produk
                            INNER JOIN mrp_production_done mrp_done ON fgt.kode = mrp_done.kode
                            WHERE fg.kode ='$kode' AND smi.status = 'done'  AND fg.lokasi LIKE '%stock%' AND adj.status = 'done'  AND adj.id_type_adjustment IN ('1','2')
                            AND adj.create_date > mrp_done.tanggal 
                            GROUP BY smi.kode_produk
                            ) as gp");
    }

}