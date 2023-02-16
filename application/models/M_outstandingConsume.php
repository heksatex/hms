<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_outstandingConsume extends CI_Model
{

    public function get_list_outstanding_con_by_kode($dept_id,$kode,$corak,$lot)
    {
      
        $kode_mo = '';
        if(!empty($kode)){
            $kode_mo     .= " AND mrp.kode LIKE '%".$kode."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND mrp.nama_produk LIKE '%".$corak."%' ";
        }

        $kp = '';
        if(!empty($lot)){
            $kp     .= " AND fg.lot LIKE '%".$lot."%' ";
        }

        return $this->db->query("SELECT mrp.kode, mrp.tanggal, mrp.origin, mrp.kode_produk, mrp.nama_produk, mrp.qty, mrp.uom, 
                                fg.lot, fg.qty as qty1, fg.uom as uom1, fg.qty2, fg.uom2, fg.nama_grade, sq.reff_note
                                FROM mrp_production mrp
                                INNER JOIN mrp_production_fg_hasil fg ON mrp.kode = fg.kode
                                INNER JOIN stock_quant sq ON fg.quant_id = sq.quant_id 
                                WHERE mrp.dept_id = '$dept_id' AND (fg.consume = 'no' AND mrp.status not in  ('done','cancel') ) $kode_mo $nama_produk $kp 
                                order by mrp.tanggal, fg.create_date asc")->result();

    }


    public function get_list_outstanding_con_by_kode_group($dept_id,$kode,$corak,$lot)
    {

        $kode_mo = '';
        if(!empty($kode)){
            $kode_mo     .= " AND mrp.kode LIKE '%".$kode."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND mrp.nama_produk LIKE '%".$corak."%' ";
        }

        $kp = '';
        if(!empty($lot)){
            $kp     .= " AND fg.lot LIKE '%".$lot."%' ";
        }

        return $this->db->query("SELECT mrp.kode, mrp.tanggal, mrp.origin, mrp.kode_produk, mrp.nama_produk, mrp.qty, mrp.uom, count(fg.lot) as total_lot
                                FROM mrp_production mrp
                                INNER JOIN mrp_production_fg_hasil fg ON mrp.kode = fg.kode
                                WHERE mrp.dept_id = '$dept_id' AND  (fg.consume = 'no' AND  mrp.status not IN  ('done','cancel') ) $kode_mo $nama_produk $kp 
                                GROUP BY mrp.kode
                                order by mrp.tanggal asc")->result();
    }


}