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

 
	public function get_list_mrp_by_tgl($mc_id, $id_dept, $tglDari, $tglSampai)
	{
		$tgldari = date('Y-m-d 07:00:00', strtotime($tglDari));
		$tglsampai = date('Y-m-d 06:59:59', strtotime('+1 days', strtotime($tglSampai)));
	
		return $this->db->query("SELECT mp.nama_produk, ms.nama_mesin, mp.target_efisiensi, IFNULL(sum(mpfg.qty),0) as tot_mtr, IFNULL(sum(mpfg.qty2),0) as tot_kg, IFNULL(count(mpfg.lot),0) as tot_gl,
										(SELECT count(sq.nama_grade) as tot FROM mrp_production_fg_hasil sq where sq.kode = mp.kode AND sq.nama_grade = 'A'  AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai'  LIMIT 1) as grade_A,
										(SELECT count(sq.nama_grade) as tot FROM mrp_production_fg_hasil sq where sq.kode = mp.kode AND sq.nama_grade = 'B'  AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai'  LIMIT 1) as grade_B,
										(SELECT count(sq.nama_grade) as tot FROM mrp_production_fg_hasil sq where sq.kode = mp.kode AND sq.nama_grade = 'C'  AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai'  LIMIT 1) as grade_C
								FROM mrp_production mp 
								INNER JOIN mesin ms ON mp.mc_id = ms.mc_id
								INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode
								WHERE mp.dept_id = '$id_dept' AND ms.mc_id = '$mc_id' AND mpfg.create_date >= '$tgldari' AND mpfg.create_date <= '$tglsampai' 
								GROUP BY mp.kode
								ORDER BY mp.tanggal ")->result();
								 
	}


	public function get_list_produk_by_tgl($mc_id, $id_dept, $tglDari, $tglSampai, $jmlHari)
	{	
		$tgldari = date('Y-m-d 07:00:00', strtotime($tglDari));
		$tglsampai = date('Y-m-d 06:59:59', strtotime('+1 days', strtotime($tglSampai)));

		$query = $this->db->query("SELECT  mp.nama_produk, ms.nama_mesin, IFNULL(sum(mpfg.qty),0) as tot_mtr, IFNULL(sum(mpfg.qty2),0) as tot_kg, IFNULL(count(mpfg.lot),0) as tot_gl
									,(SELECT sum(sq.target_efisiensi) FROM mrp_production sq WHERE sq.mc_id = mp.mc_id AND sq.kode IN (SELECT kode FROM mrp_production_fg_hasil mpfg WHERE mpfg.create_date >= '$tgldari' AND mpfg.create_date <= '$tglsampai' ) AND sq.kode_produk = mp.kode_produk ) as target_ef,
									
									(SELECT sum(sq.target_efisiensi)*24*$jmlHari FROM mrp_production sq WHERE sq.mc_id = mp.mc_id AND sq.kode IN (SELECT kode FROM mrp_production_fg_hasil mpfg WHERE mpfg.create_date >= '$tgldari' AND mpfg.create_date <= '$tglsampai' ) AND sq.kode_produk = mp.kode_produk ) as target_periode,
									
									(SELECT count(sq.kode) FROM mrp_production sq WHERE sq.mc_id = mp.mc_id AND sq.kode IN (SELECT kode FROM mrp_production_fg_hasil sq3 WHERE sq3.create_date >= '$tgldari' AND sq3.create_date <= '$tglsampai' ) AND sq.kode_produk = mp.kode_produk ) as tot_mo,
									
									(SELECT count(sq.nama_grade) FROM mrp_production_fg_hasil sq WHERE sq.nama_grade = 'A' AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai'  AND kode IN (SELECT sq2.kode FROM mrp_production sq2 WHERE sq2.mc_id = mp.mc_id AND sq2.dept_id = mp.dept_id AND sq2.kode_produk = mp.kode_produk)  ) as grade_A,

									(SELECT count(sq.nama_grade) FROM mrp_production_fg_hasil sq WHERE sq.nama_grade = 'B' AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai'  AND kode IN (SELECT sq2.kode FROM mrp_production sq2 WHERE sq2.mc_id = mp.mc_id AND sq2.dept_id = mp.dept_id AND sq2.kode_produk = mp.kode_produk)  ) as grade_B,

									(SELECT count(sq.nama_grade) FROM mrp_production_fg_hasil sq WHERE sq.nama_grade = 'C' AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai'  AND kode IN (SELECT sq2.kode FROM mrp_production sq2 WHERE sq2.mc_id = mp.mc_id AND sq2.dept_id = mp.dept_id AND sq2.kode_produk = mp.kode_produk)  ) as grade_C
							
							FROM mrp_production mp
							INNER JOIN mesin ms ON mp.mc_id = ms.mc_id
							INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode
							WHERE mp.dept_id = '$id_dept' AND ms.mc_id = '$mc_id' AND mpfg.create_date >= '$tgldari' AND mpfg.create_date <= '$tglsampai'
							GROUP BY mp.kode_produk
							");
		return $query->result();

	}

	/*

	public function get_list_hph_grade_by_date($kode, $tgl, $grade)
	{
		$tgldari = date('Y-m-d 07:00:00', strtotime($tgl));
		$tglsampai = date('Y-m-d 06:59:59', strtotime('+1 days', strtotime($tgl)));

		$query = $this->db->query("SELECT IFNULL(count(lot),0) as tot_grade
								FROM mrp_production_fg_hasil
								WHERE kode = '$kode' AND create_date >= '$tgldari' AND create_date <= '$tglsampai' AND nama_grade = '$grade' ");
		$result = $query->result_array();
		return  $result[0]['tot_grade'];
	}


	SELECT  mp.kode, mp.nama_produk, ms.nama_mesin, mp.target_efisiensi, IFNULL(sum(mpfg.qty),0) as tot_mtr, IFNULL(sum(mpfg.qty2),0) as tot_kg, IFNULL(count(mpfg.lot),0) as tot_gl, count(mp.kode) as tot_Mo 
				,
	
				(SELECT count(sq.nama_grade) as tot FROM mrp_production_fg_hasil sq where sq.kode = mp.kode AND sq.nama_grade = 'A'  AND sq.create_date >= '2021-08-18 07:00:00' AND sq.create_date <= '2021-08-19 06:59:59'  LIMIT 1) as gradeA,
				(SELECT count(sq.nama_grade) as tot FROM mrp_production_fg_hasil sq where sq.kode = mp.kode AND sq.nama_grade = 'B'  AND sq.create_date >= '2021-08-18 07:00:00' AND sq.create_date <= '2021-08-19 06:59:59'  LIMIT 1) as gradeB,
				(SELECT count(sq.nama_grade) as tot FROM mrp_production_fg_hasil sq where sq.kode = mp.kode AND sq.nama_grade = 'C'  AND sq.create_date >= '2021-08-18 07:00:00' AND sq.create_date <= '2021-08-19 06:59:59'  LIMIT 1) as gradeC

FROM mrp_production mp 
INNER JOIN mesin ms ON mp.mc_id = ms.mc_id
INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode
WHERE mp.dept_id = 'TRI' AND ms.mc_id = 'mc27' AND mpfg.create_date >= '2021-08-18 07:00:00' AND mpfg.create_date <= '2021-08-19 06:59:59' 
GROUP BY mp.kode_produk,mp.mc_id
ORDER BY mp.tanggal


SELECT sum(mp.target_efisiensi) as tot_ef, mp.kode_produk, mp.nama_produk 
FROM mrp_production mp 
INNER JOIN mesin ms ON mp.mc_id = ms.mc_id
WHERE mp.dept_id = 'TRI' AND ms.mc_id = 'mc27' 
			AND mp.kode IN (SELECT kode FROM mrp_production_fg_hasil mpfg WHERE  mpfg.create_date >= '2021-06-01 07:00:00' AND mpfg.create_date <= '2021-08-26 06:59:59' )
GROUP BY mp.kode_produk

									
									
									

	*/

}