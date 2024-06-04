<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_HPHgudangjadi extends CI_Model
{	

    public function get_list_hph_by_kode($where)
	{	
            $ip  = $this->input->ip_address();
            return $this->db->query("SELECT '$ip' as ip, mrpin.kode_mrp as no_hph, mrpin.lot, fg.kode_produk, fg.nama_produk, mrpin.qty as mtr_prod, mrpin.uom, mrpin.qty2 as kg_prod, 
                                mq.nama as nama_quality, mrpin.lebar_jadi, mrpin.uom2, mrpin.uom_lebar_jadi, m.nama_mesin, 
                                fg.lot as lot_gjd, fg.create_date as tgl_hph, mp.origin, SUBSTRING_INDEX(mp.origin,'|',1) as sc, SUBSTRING_INDEX(SUBSTRING_INDEX(mp.origin,'|',2),'|',-1) as co, 
                                sq.warna_remark, sq.corak_remark, 
                                fg.qty as qty1_hph, fg.uom as uom1_hph, fg.qty2 as qty2_hph, fg.uom2 as uom2_hph, fg.nama_grade,
                                mjk.nama_jenis_kain,
                                mrpin.gramasi, mrpin.berat, mrpin.benang, mrpin.mc_id,
                                mrpin.id_jenis_kain,
                                sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual,
                                msg.nama_sales_group, mrpin.operator,
                                fg.lokasi,
                                fg.nama_user
                                FROM mrp_inlet mrpin
                                INNER JOIN mrp_production_fg_hasil fg ON mrpin.id = fg.id_inlet
                                INNER JOIN mrp_production mp ON fg.kode = mp.kode
                                INNER JOIN stock_quant sq ON fg.quant_id = sq.quant_id
                                LEFT JOIN mst_jenis_kain mjk on mrpin.id_jenis_kain = mjk.id
                                LEFT JOIN mst_sales_group msg on mrpin.sales_group = msg.kode_sales_group
                                LEFT JOIN mst_quality mq ON mrpin.id_quality = mq.id
                                LEFT JOIN mesin m ON mrpin.mc_id = m.mc_id
                                 $where
                                ORDER BY fg.create_date desc
								")->result();
	}

    public function get_list_split_by_kode($where)
    {
            $ip  = $this->input->ip_address();
            return $this->db->query("SELECT '$ip' as ip, hfg.gramasi, hfg.operator, hfg.benang, hfg.berat, hfg.co, hfg.sc, hfg.nama_quality, IF(hfg.id_jenis_kain != null,  hfg.nama_jenis_kain, mjk.nama_jenis_kain) as nama_jenis_kain, spl.kode_split as no_hph, spl.tanggal, spl.kode_produk, spl.nama_produk, spl.qty as qty_awal, spl.qty2  as qty2_awal, spl.uom as uom_awal, spl.uom2 as uom2_awal,
                                                spl.lot, 
                                                sq.lot as lot_gjd,
                                                sq.lebar_jadi, sq.uom_lebar_jadi, sq.corak_remark, sq.warna_remark, 
                                                sq.qty, sq.uom, sq.qty2, sq.uom2, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual,
                                                spl.nama_user,
                                        		msg.nama_sales_group, sq.nama_grade
                                    FROM split spl
                                    INNER JOIN split_items spli ON spl.kode_split = spli.kode_split
                                    INNER JOIN stock_quant sq ON sq.quant_id = spli.quant_id_baru
                                    LEFT JOIN mst_sales_group msg ON sq.sales_group = msg.kode_sales_group
                                    LEFT JOIN 
                                        (SELECT SUBSTRING_INDEX(mp.origin,'|',1) as sc, SUBSTRING_INDEX(SUBSTRING_INDEX(mp.origin,'|',2),'|',-1) as co, mp.dept_id, fg.id_inlet, fg.quant_id, mrpin.gramasi,mrpin.benang, mq.nama as nama_quality, mjk.nama_jenis_kain, mrpin.operator, mrpin.berat, mrpin.id_quality,mrpin.id_jenis_kain
                                            FROM mrp_production mp
                                            INNER JOIN mrp_production_fg_hasil fg ON mp.kode = fg.kode
                                            INNER JOIN mrp_inlet  mrpin ON fg.id_inlet = mrpin.id
                                            LEFT JOIN mst_quality mq ON mrpin.id_quality = mq.id						
                                            LEFT JOIN mst_jenis_kain mjk on mrpin.id_jenis_kain = mjk.id
                                            WHERE mp.dept_id = 'GJD') as hfg ON hfg.quant_id = spl.quant_id 
                                    INNER JOIN mst_produk mp ON spl.kode_produk =  mp.kode_produk
                                    LEFT JOIN mst_jenis_kain mjk on mp.id_jenis_kain = mjk.id                         
                                    $where
                                    ORDER BY spl.tanggal desc ")->result();
    }



    public function get_list_join_by_kode($where)
    {
            $ip  = $this->input->ip_address();
            return $this->db->query("SELECT '$ip' as ip, hfg.gramasi, hfg.berat, hfg.operator, hfg.benang, hfg.nama_quality,  IF(hfg.id_jenis_kain != null,  hfg.nama_jenis_kain, mjk.nama_jenis_kain) as nama_jenis_kain,hfg.co, hfg.sc, jl.kode_join as no_hph, jl.tanggal_transaksi, jl.kode_produk, jl.nama_produk, jl.corak_remark, 
                                            jl.warna_remark, jl.lot as lot_gjd, jl.qty, jl.uom, jl.qty2, jl.uom2, 
                                            jl.qty_jual, jl.uom_jual, jl.qty2_jual, jl.uom2_jual, jl.lebar_jadi, jl.uom_lebar_jadi, jl.grade, jl.nama_user,
                                            msg.nama_sales_group,
                                            (SELECT GROUP_CONCAT(lot SEPARATOR ' + ') FROM join_lot_items jli WHERE  jli.kode_join = jl.kode_join) as lot_asal,
                                            sum(jli.qty) as tot_qty1, jli.uom as uom_tot, sum(jli.qty2) as tot_qty2, jli.uom2 as uom2_tot
                                    FROM join_lot jl
                                    INNER JOIN join_lot_items jli ON jl.kode_join = jli.kode_join
                                    LEFT JOIN mst_sales_group msg ON jl.sales_group = msg.kode_sales_group      
                                    LEFT JOIN (SELECT SUBSTRING_INDEX(mp.origin,'|',1) as sc, SUBSTRING_INDEX(SUBSTRING_INDEX(mp.origin,'|',2),'|',-1) as co, mp.dept_id, fg.id_inlet, fg.quant_id, mrpin.gramasi,mrpin.benang, mq.nama as nama_quality, mjk.nama_jenis_kain, mrpin.operator, mrpin.berat, mrpin.id_quality, mrpin.id_jenis_kain, mp.mc_id
                                            FROM mrp_production mp
                                            INNER JOIN mrp_production_fg_hasil fg ON mp.kode = fg.kode
                                            INNER JOIN mrp_inlet  mrpin ON fg.id_inlet = mrpin.id
                                            LEFT JOIN mst_quality mq ON mrpin.id_quality = mq.id						
                                            LEFT JOIN mst_jenis_kain mjk on mrpin.id_jenis_kain = mjk.id
                                            WHERE mp.dept_id = 'GJD') as hfg ON hfg.quant_id = jli.quant_id
                                    INNER JOIN mst_produk mp ON jl.kode_produk =  mp.kode_produk
                                    LEFT JOIN mst_jenis_kain mjk on mp.id_jenis_kain = mjk.id                         
                                    $where 
                                    GROUP BY jl.kode_join
                                    order by jl.tanggal_transaksi desc")->result();
    }

    public function get_list_barcode_manual_by_kode($where)
    {
            $ip  = $this->input->ip_address();
            return $this->db->query("SELECT '$ip' as ip, mm.kode as no_hph, mm.tanggal_transaksi,
                                                    msg.nama_sales_group,
                                                    mbi.kode_produk, mbi.nama_produk, mbi.lot, mbi.corak_remark, mbi.warna_remark,  mbi.qty, mbi.uom, mbi.qty2, mbi.uom2, mbi.qty_jual, mbi.uom_jual,
                                                    mbi.qty2_jual, mbi.uom2_jual,mbi.lebar_jadi, mbi.uom_lebar_jadi,
                                                    mb.id_quality,
                                                    mb.grade, 
                                                    mq.nama as nama_quality,
                                                    mm.nama_user,
                                                    mjk.nama_jenis_kain
                                    FROM mrp_manual mm
                                    INNER JOIN mrp_manual_batch mb ON mm.kode = mb.kode
                                    INNER JOIN mrp_manual_batch_items mbi ON mb.kode =mbi.kode AND mb.no_batch = mbi.no_batch
                                    INNER JOIN mst_produk mp ON mbi.kode_produk =  mp.kode_produk
                                    LEFT JOIN mst_jenis_kain mjk on mp.id_jenis_kain = mjk.id
                                    LEFT JOIN mst_quality mq ON mb.id_quality = mq.id
                                    LEFT JOIN mst_sales_group msg ON mm.sales_group = msg.kode_sales_group                              
                                    $where 
                                    order by mm.tanggal_transaksi desc")->result();
    }

}