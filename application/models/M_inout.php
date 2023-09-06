<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_inout extends CI_Model
{

    public function get_list_departement_select2($nama)
	{
		return $this->db->query("SELECT kode,nama FROM departemen  WHERE show_dept = 'true' AND   nama LIKE '%$nama%' ORDER BY nama ")->result();
	}


    public function get_list_penerimaan_harian_by_kode($tgldari,$tglsampai,$dept_id,$dept_dari,$status1,$kode_in,$corak)
    {
        $reff_picking = '';
        if(!empty($dept_dari)){
            $reff_picking .= " AND pb.reff_picking LIKE '%$dept_dari%' ";
        }
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }

        $kode = '';
        if(!empty($kode_in)){
            $kode     .= " AND pb.kode LIKE '%".$kode_in."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, sq.reff_note, smi.status, ms.nama_status
                                FROM penerimaan_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                INNER JOIN mst_status ms ON smi.status = ms.kode
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND pb.dept_id = '$dept_id' $reff_picking $status $kode $nama_produk
                                ORDER BY pb.kode,smi.kode_produk, smi.row_order")->result();

    }

    public function get_list_penerimaan_harian_by_kode_group($tgldari,$tglsampai,$dept_id,$dept_dari,$status1,$kode_in,$corak)
    {
        $reff_picking = '';
        if(!empty($dept_dari)){
            $reff_picking .= " AND pb.reff_picking LIKE '%$dept_dari%' ";
        }
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }

        $kode = '';
        if(!empty($kode_in)){
            $kode     .= " AND pb.kode LIKE '%".$kode_in."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, count(smi.lot) as tot_lot, sum(smi.qty) as tot_qty, smi.uom, sum(smi.qty2) as tot_qty2, smi.uom2, pb.reff_note, pb.status, ms.nama_status
                                FROM penerimaan_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                INNER JOIN mst_status ms ON pb.status = ms.kode
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND pb.dept_id = '$dept_id' $reff_picking $status $kode $nama_produk
                                GROUP BY pb.kode
                                ORDER BY pb.tanggal_transaksi ")->result();

    }

    public function get_list_pengiriman_harian_by_kode($tgldari,$tglsampai,$dept_id,$dept_tujuan,$status1,$kode_out,$corak)
    {
        $reff_picking = '';
        if(!empty($dept_tujuan)){
            $reff_picking .= " AND pb.reff_picking LIKE '%$dept_tujuan%' ";
        }
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }

        $kode = '';
        if(!empty($kode_out)){
            $kode     .= " AND pb.kode LIKE '%".$kode_out."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, sq.reff_note,  smi.status, ms.nama_status
                                FROM pengiriman_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                INNER JOIN mst_status ms ON smi.status = ms.kode
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND pb.dept_id = '$dept_id' $reff_picking $status $kode $nama_produk
                                ORDER BY pb.kode,smi.kode_produk, smi.row_order")->result();

    }

    public function get_list_pengiriman_harian_by_kode_group($tgldari,$tglsampai,$dept_id,$dept_tujuan,$status1,$kode_out,$corak)
    {
        $reff_picking = '';
        if(!empty($dept_tujuan)){
            $reff_picking .= " AND pb.reff_picking LIKE '%$dept_tujuan%' ";
        }
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }

        $kode = '';
        if(!empty($kode_out)){
            $kode     .= " AND pb.kode LIKE '%".$kode_out."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, count(smi.lot) as tot_lot, sum(smi.qty) as tot_qty, smi.uom, sum(smi.qty2) as tot_qty2, smi.uom2, pb.reff_note,  pb.status, ms.nama_status
                                FROM pengiriman_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                INNER JOIN mst_status ms ON pb.status = ms.kode
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND pb.dept_id = '$dept_id' $reff_picking $status $kode $nama_produk
                                GROUP BY pb.kode
                                ORDER BY pb.tanggal_transaksi")->result();

    }

    public function get_list_pengiriman_greige_by_kode($tgldari,$tglsampai,$dept_id,$dept_tujuan,$status1,$kode,$warna,$corak,$sales_group)
    {
        $reff_picking = '';
        if(!empty($dept_tujuan)){
            $reff_picking .= " AND SUBSTRING_INDEX(pb.reff_picking, '|',-1) LIKE '%$dept_tujuan%' ";
        }
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }
        $kode_out = '';
        if(!empty($kode)){
            $kode_out     .= " AND pb.kode LIKE '%".$kode."%' ";
        }

        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        $nama_warna = '';
        if(!empty($warna)){
            $nama_warna     .= " AND dti.nama_warna LIKE '%".$warna."%' ";
        }

        $mkt = '';
        if(!empty($sales_group)){
            $mkt     .= " AND sg.kode_sales_group LIKE '%".$sales_group."%' ";
        }
        

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, 
                                    sq.reff_note,  smi.status, sg.nama_sales_group as mkt,dti.nama_warna
                                
                                FROM pengiriman_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                INNER JOIN (SELECT msg.nama_sales_group, msg.kode_sales_group, sc.sales_order FROM sales_contract as sc
                                            INNER JOIN  mst_sales_group as msg ON sc.sales_group = msg.kode_sales_group ) as sg ON sg.sales_order = SUBSTRING_INDEX(pb.origin,'|',1)
                                INNER JOIN (SELECT w.nama_warna, cod.kode_co, cod.row_order FROM color_order_detail as cod
                                            INNER JOIN  warna as w ON cod.id_warna = w.id ) as dti ON dti.kode_co = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(origin,'|',3) ,'|',-2),'|',1) AND dti.row_order = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(origin,'|',3) ,'|',-2),'|',-1)
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND pb.dept_id = '$dept_id'  $reff_picking $status $kode_out $nama_produk $nama_warna $mkt
                                ORDER BY pb.tanggal_transaksi, smi.row_order")->result();

    }


    public function get_list_pengiriman_greige_by_group($tgldari,$tglsampai,$dept_id,$dept_tujuan,$status1,$kode,$warna,$corak,$sales_group)
    {
        $reff_picking = '';
        if(!empty($dept_tujuan)){
            $reff_picking .= " AND SUBSTRING_INDEX(pb.reff_picking, '|',-1) LIKE '%$dept_tujuan%' ";
        }
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }
        $kode_out = '';
        if(!empty($kode)){
            $kode_out     .= " AND pb.kode LIKE '%".$kode."%' ";
        }

        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        $nama_warna = '';
        if(!empty($warna)){
            $nama_warna     .= " AND dti.nama_warna LIKE '%".$warna."%' ";
        }

        $mkt = '';
        if(!empty($sales_group)){
            $mkt     .= " AND sg.kode_sales_group LIKE '%".$sales_group."%' ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk,  
                                        count(smi.lot) as tot_lot, sum(smi.qty) as tot_qty, smi.uom, sum(smi.qty2) as tot_qty2, smi.uom2, pb.reff_note,  pb.status,
                                sg.nama_sales_group as mkt,dti.nama_warna
                                
                                FROM pengiriman_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                INNER JOIN (SELECT msg.nama_sales_group, msg.kode_sales_group, sc.sales_order FROM sales_contract as sc
                                            INNER JOIN  mst_sales_group as msg ON sc.sales_group = msg.kode_sales_group ) as sg ON sg.sales_order = SUBSTRING_INDEX(pb.origin,'|',1)
                                INNER JOIN (SELECT w.nama_warna, cod.kode_co, cod.row_order FROM color_order_detail as cod
                                            INNER JOIN  warna as w ON cod.id_warna = w.id ) as dti ON dti.kode_co = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(origin,'|',3) ,'|',-2),'|',1) AND dti.row_order = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(origin,'|',3) ,'|',-2),'|',-1)
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND pb.dept_id = '$dept_id' $reff_picking $status $kode_out $nama_produk $nama_warna $mkt
                                GROUP BY pb.kode
                                ORDER BY pb.tanggal_transaksi")->result();

    }


    public function get_list_pengiriman_harian_by_kode_get_in($tgldari,$tglsampai,$dept_id,$dept_tujuan,$status1,$kode_out,$corak)
    {
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }
        $where_dept = '';
        if(!empty($dept_tujuan)){
            $where_dept = " AND SUBSTRING_INDEX(pb.reff_picking,'|',1) LIKE '%$dept_tujuan%' ";
        }

        $kode = '';
        if(!empty($kode_out)){
            $kode     .= " AND pb.kode LIKE '%".$kode_out."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, sq.reff_note,  smi.status, ms.nama_status
                                FROM pengiriman_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                INNER JOIN mst_status ms ON smi.status = ms.kode
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND SUBSTRING_INDEX(pb.reff_picking,'|',-1) = '$dept_id'  $where_dept  $status $kode $nama_produk
                                ORDER BY pb.kode,smi.kode_produk, smi.row_order")->result();

    }

    public function get_list_pengiriman_harian_by_kode_get_in_group($tgldari,$tglsampai,$dept_id,$dept_tujuan,$status1,$kode_out,$corak)
    {
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }
        $where_dept = '';
        if(!empty($dept_tujuan)){
            $where_dept = " AND SUBSTRING_INDEX(pb.reff_picking,'|',1) LIKE '%$dept_tujuan%' ";
        }

        $kode = '';
        if(!empty($kode_out)){
            $kode    .= " AND pb.kode LIKE '%".$kode_out."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }


        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, count(smi.lot) as tot_lot, sum(smi.qty) as tot_qty, smi.uom, sum(smi.qty2) as tot_qty2, smi.uom2, pb.reff_note,  pb.status, ms.nama_status
                                FROM pengiriman_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                INNER JOIN mst_status ms ON pb.status = ms.kode
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND SUBSTRING_INDEX(pb.reff_picking,'|',-1) = '$dept_id'  $where_dept  $status  $kode $nama_produk
                                GROUP BY pb.kode
                                ORDER BY pb.tanggal_transaksi  ")->result();

    }


    public function get_list_penerimaan_harian_by_kode_get_out($tgldari,$tglsampai,$dept_id,$dept_dari,$status1,$kode_in,$corak)
    {
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }
        $where_dept = '';
        if(!empty($dept_dari)){
            $where_dept = " AND SUBSTRING_INDEX(pb.reff_picking,'|',-1) LIKE '%$dept_dari%' ";
        }

        $kode = '';
        if(!empty($kode_in)){
            $kode    .= " AND pb.kode LIKE '%".$kode_in."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, sq.reff_note,  smi.status, ms.nama_status
                                FROM penerimaan_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                INNER JOIN mst_status ms ON smi.status = ms.kode
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND SUBSTRING_INDEX(pb.reff_picking,'|',1) = '$dept_id'  $where_dept  $status $kode $nama_produk
                                ORDER BY pb.kode,smi.kode_produk, smi.row_order")->result();

    }

    public function get_list_penerimaan_harian_by_kode_get_out_group($tgldari,$tglsampai,$dept_id,$dept_dari,$status1,$kode_in,$corak)
    {
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }
        $where_dept = '';
        if(!empty($dept_dari)){
            $where_dept = " AND SUBSTRING_INDEX(pb.reff_picking,'|',-1) LIKE '%$dept_dari%' ";
        }

        $kode = '';
        if(!empty($kode_in)){
            $kode    .= " AND pb.kode LIKE '%".$kode_in."%' ";
        }
        $nama_produk = '';
        if(!empty($corak)){
            $nama_produk     .= " AND smi.nama_produk LIKE '%".$corak."%' ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, count(smi.lot) as tot_lot, sum(smi.qty) as tot_qty, smi.uom, sum(smi.qty2) as tot_qty2, smi.uom2, pb.reff_note,  pb.status, ms.nama_status
                                FROM penerimaan_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                INNER JOIN mst_status ms ON smi.status = ms.kode
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND SUBSTRING_INDEX(pb.reff_picking,'|',1) = '$dept_id'  $where_dept  $status $kode $nama_produk
                                GROUP BY pb.kode
                                ORDER BY pb.tanggal_transaksi")->result();

    }

}