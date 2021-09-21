<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_qualityControl extends CI_Model
{

    public function get_list_departement_select2($nama)
	{
		return $this->db->query("SELECT kode,nama FROM departemen  WHERE nama LIKE '%$nama%' ORDER BY nama ")->result();
	}

    public function get_list_mesin($dept_id)
	{
		return $this->db->query("SELECT * FROM mesin WHERE dept_id = '$dept_id' AND status_aktif = 't' order by row_order ")->result();
	}

	public function get_list_produk_by_tgl($mc_id, $id_dept, $tglDari, $tglSampai, $jmlHari)
	{	
		$tgldari = date('Y-m-d 07:00:00', strtotime($tglDari));
		$tglsampai = date('Y-m-d 06:59:59', strtotime('+1 days', strtotime($tglSampai)));

		$query = $this->db->query("SELECT  mp.kode_produk, mp.nama_produk, mp.mc_id, ms.nama_mesin, IFNULL(sum(mpfg.qty),0) as tot_mtr, IFNULL(sum(mpfg.qty2),0) as tot_kg, IFNULL(count(mpfg.lot),0) as tot_gl
									,(SELECT sum(sq.target_efisiensi) FROM mrp_production sq WHERE sq.mc_id = mp.mc_id AND sq.kode IN (SELECT kode FROM mrp_production_fg_hasil sq3 WHERE sq3.create_date >= '$tgldari' AND sq3.create_date <= '$tglsampai'  AND sq3.kode_produk = mp.kode_produk  ) AND sq.kode_produk = mp.kode_produk ) as target_ef,
									
									(SELECT sum(sq.target_efisiensi)*24*$jmlHari FROM mrp_production sq WHERE sq.mc_id = mp.mc_id AND sq.kode IN (SELECT kode FROM mrp_production_fg_hasil sq3 WHERE sq3.create_date >= '$tgldari' AND sq3.create_date <= '$tglsampai' AND sq3.kode_produk = mp.kode_produk  ) AND sq.kode_produk = mp.kode_produk ) as target_periode,
									
									(SELECT count(sq.kode) FROM mrp_production sq WHERE sq.mc_id = mp.mc_id AND sq.kode IN (SELECT kode FROM mrp_production_fg_hasil sq3 WHERE sq3.create_date >= '$tgldari' AND sq3.create_date <= '$tglsampai' AND sq3.kode_produk = mp.kode_produk  ) AND sq.kode_produk = mp.kode_produk ) as tot_mo,
									
									(SELECT count(sq.nama_grade) FROM mrp_production_fg_hasil sq WHERE sq.nama_grade = 'A' AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai'  AND sq.kode_produk = mp.kode_produk AND kode IN (SELECT sq2.kode FROM mrp_production sq2 WHERE sq2.mc_id = mp.mc_id AND sq2.dept_id = mp.dept_id AND sq2.kode_produk = mp.kode_produk)  ) as grade_A,

									(SELECT count(sq.nama_grade) FROM mrp_production_fg_hasil sq WHERE sq.nama_grade = 'B' AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai' AND sq.kode_produk = mp.kode_produk  AND kode IN (SELECT sq2.kode FROM mrp_production sq2 WHERE sq2.mc_id = mp.mc_id AND sq2.dept_id = mp.dept_id AND sq2.kode_produk = mp.kode_produk)  ) as grade_B,

									(SELECT count(sq.nama_grade) FROM mrp_production_fg_hasil sq WHERE sq.nama_grade = 'C' AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai' AND sq.kode_produk = mp.kode_produk AND kode IN (SELECT sq2.kode FROM mrp_production sq2 WHERE sq2.mc_id = mp.mc_id AND sq2.dept_id = mp.dept_id AND sq2.kode_produk = mp.kode_produk)  ) as grade_C,

									(SELECT IFNULL(sum(adji.qty_move),0) FROM mrp_production_fg_hasil sq 
										INNER JOIN adjustment_items adji ON sq.kode_produk = adji.kode_produk AND sq.kode = adji.mrp_kode AND sq.quant_id = adji.quant_id
										INNER JOIN adjustment adj ON adji.kode_adjustment = adj.kode_adjustment
										INNER JOIN mrp_production mp2 ON sq.kode = mp2.kode
										WHERE adj.`status` = 'done' AND sq.create_date >= '$tgldari' 
													AND sq.create_date <= '$tglsampai'  AND adj.create_date >= '$tgldari' 
													AND adj.create_date <= '$tglsampai' AND sq.kode_produk = mp.kode_produk  
													AND mp.destination_location = adj.kode_lokasi AND mp.mc_id = mp2.mc_id ) as tot_qty_adj,
										
									(SELECT count(adji.lot) FROM mrp_production_fg_hasil sq 
										INNER JOIN adjustment_items adji ON sq.kode_produk = adji.kode_produk AND sq.kode = adji.mrp_kode AND sq.quant_id = adji.quant_id
										INNER JOIN adjustment adj ON adji.kode_adjustment = adj.kode_adjustment
										INNER JOIN mrp_production mp2 ON sq.kode = mp2.kode
										WHERE adj.`status` = 'done' AND sq.create_date >= '$tgldari'
													AND sq.create_date <= '$tglsampai' AND adj.create_date >= '$tgldari'
													AND adj.create_date <='$tglsampai' AND sq.kode_produk = mp.kode_produk  
													AND mp.destination_location = adj.kode_lokasi AND mp.mc_id = mp2.mc_id ) as tot_gl_adj
							
							FROM mrp_production mp
							INNER JOIN mesin ms ON mp.mc_id = ms.mc_id
							INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode AND mp.kode_produk = mpfg.kode_produk
							WHERE mp.dept_id = '$id_dept' AND ms.mc_id = '$mc_id' AND mpfg.create_date >= '$tgldari' AND mpfg.create_date <= '$tglsampai'
							GROUP BY mp.kode_produk
							");
		return $query->result();

	}


	public function get_ef_hari_by_produk($mc_id, $id_dept, $tgldari, $kode_produk)
	{	
		$tgldari = date('Y-m-d 07:00:00', strtotime($tgldari));
		$tglsampai = date('Y-m-d 06:59:59', strtotime('+1 days', strtotime($tgldari)));

		$query = $this->db->query("SELECT  mp.mc_id, mp.kode_produk, mp.target_efisiensi, sum(mpfg.qty) as tot_mtr,
										 (SELECT IFNULL(sum(adji.qty_move),0) FROM adjustment_items adji
											INNER JOIN adjustment adj ON adji.kode_adjustment  = adj.kode_adjustment 
											INNER JOIN mrp_production_fg_hasil mpfg2 ON adji.quant_id = mpfg2.quant_id  AND adji.mrp_kode = mpfg2.kode AND adji.kode_produk = mpfg2.kode_produk
											INNER JOIN mrp_production mp2 ON mpfg2.kode = mp2.kode
											WHERE adj.status = 'done' AND adj.create_date >= '$tgldari' AND adj.create_date <= '$tglsampai'  
												AND mpfg2.create_date >= '$tgldari' AND mpfg2.create_date <= '$tglsampai' 
												AND mpfg.kode_produk = adji.kode_produk AND adj.kode_lokasi = mp.destination_location 
												AND mp.mc_id  = mp2.mc_id) as tot_qty_adj
									FROM mrp_production mp
									INNER JOIN mrp_production_fg_hasil mpfg ON  mp.kode = mpfg.kode AND mp.kode_produk = mpfg.kode_produk

									WHERE mp.dept_id = '$id_dept' AND mp.mc_id ='$mc_id' and mp.kode_produk = '$kode_produk' 
										AND  mpfg.create_date >= '$tgldari' AND mpfg.create_date <= '$tglsampai'
								");
		return $query->row_array();

	}

}