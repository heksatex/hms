<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_outstandingIn extends CI_Model
{

    public function get_list_outstanding_in_by_kode($dept_id,$dept_dari,$kode,$corak)
    {
        $reff_picking = '';
        if(!empty($dept_dari)){
            $reff_picking .= " AND SUBSTRING_INDEX(pb.reff_picking, '|',1) LIKE '%$dept_dari%' ";
        }
        $kode_in = '';
        if(!empty($kode)){
            $kode_in     .= " AND pb.kode LIKE '%".$kode."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk,
                             smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, sq.reff_note, smi.status,ms.nama_status
                            FROM penerimaan_barang pb
                            INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                            INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                            INNER JOIN stock_move sm ON pb.move_id = sm.move_id
                            INNER JOIN mst_status ms ON smi.status = ms.kode
                            WHERE pb.dept_id = '$dept_id' AND sm.lokasi_dari LIKE '%Transit%' AND pb.status IN ('ready') 
                            $reff_picking $kode_in $nama_produk 
                            ORDER BY pb.kode,smi.kode_produk, smi.row_order")->result();

    }

    public function get_list_outstanding_in_by_kode_group($dept_id,$dept_dari,$kode,$corak)
    {
        $reff_picking = '';
        if(!empty($dept_dari)){
            $reff_picking .= " AND SUBSTRING_INDEX(pb.reff_picking, '|',1) LIKE '%$dept_dari%' ";
        }
        $kode_in = '';
        if(!empty($kode)){
            $kode_in     .= " AND pb.kode LIKE '%".$kode."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk,  
        count(smi.lot) as tot_lot, sum(smi.qty) as tot_qty, smi.uom, sum(smi.qty2) as tot_qty2, smi.uom2, pb.reff_note,  pb.status,ms.nama_status
                            FROM penerimaan_barang pb
                            INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                            INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                            INNER JOIN stock_move sm ON pb.move_id = sm.move_id
                            INNER JOIN mst_status ms ON pb.status = ms.kode
                            WHERE pb.dept_id = '$dept_id' AND sm.lokasi_dari LIKE '%Transit%' AND pb.status IN ('ready') 
                            $reff_picking $kode_in $nama_produk 
                            GROUP BY pb.kode
                            ORDER BY pb.tanggal_transaksi
                            ")->result();

    }

}