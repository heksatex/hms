<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_HPHdf extends CI_Model
{	

	public function get_list_HPH_df_by_kode($where)
	{	
		$ip  = $this->input->ip_address();
		return $this->db->query("SELECT '$ip' as ip, mp.kode, mp.origin, mpfg.kode_produk, mpfg.nama_produk,mp.reff_note,
								mpfg.lot, mpfg.qty, mpfg.uom, mpfg.qty2, mpfg.uom2, mpfg.create_date as tgl_hph, mpfg.nama_grade,
								ms.nama_mesin, mpfg.nama_user,mpfg.lokasi,sq.reff_note as reff_note_sq, sq.lebar_greige, sq.uom_lebar_greige, sq.lebar_jadi, sq.uom_lebar_jadi, mpfg.sales_group,  mpfg.sales_order, mg.nama_sales_group, pb.kode as no_go, mp.id_warna, w.nama_warna, mp.dept_id

								FROM mrp_production mp
								INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode
								INNER JOIN stock_quant sq ON mpfg.quant_id = sq.quant_id								
								INNER JOIN warna w ON w.id = mp.id_warna
								LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id 
								LEFT JOIN mst_sales_group mg ON mpfg.sales_group = mg.kode_sales_group
								LEFT JOIN pengiriman_barang pb ON mp.origin = pb.origin AND (pb.dept_id = 'GRG' OR pb.dept_id = 'GRG-R') AND pb.status ='done'
								$where ORDER BY mpfg.kode asc, mpfg.create_date asc
								")->result();
	}



	public function get_record_hph_df($where)
	{	
		$ip  = $this->input->ip_address();
		$query = $this->db->query("SELECT '$ip' as ip, count(*) as allcount
								FROM mrp_production mp
								INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode
								INNER JOIN stock_quant sq ON mpfg.quant_id = sq.quant_id								
								INNER JOIN warna w ON w.id = mp.id_warna
								LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id 
								LEFT JOIN mst_sales_group mg ON mpfg.sales_group = mg.kode_sales_group
								LEFT JOIN pengiriman_barang pb ON mp.origin = pb.origin AND (pb.dept_id = 'GRG' OR pb.dept_id = 'GRG-R')  AND pb.status ='done'
								$where
								");
		$result = $query->result_array();      
      	return $result[0]['allcount'];
	}

}