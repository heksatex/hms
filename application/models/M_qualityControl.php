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


    public function get_list_hph_qc_by_date($kode, $tgl, $shift)
	{	

		if($shift == 'pagi'){
			$tgldari   = date('Y-m-d 07:00:00', strtotime($tgl));
			$tglsampai = date('Y-m-d 14:29:59', strtotime($tgl));
		}else if($shift == 'siang'){
			$tgldari   = date('Y-m-d 14:30:00', strtotime($tgl));
			$tglsampai = date('Y-m-d 22:59:59', strtotime($tgl));
		}else{
			$tgldari   = date('Y-m-d 23:00:00', strtotime($tgl));
			$tglsampai = date('Y-m-d 06:59:59', strtotime('+1 days', strtotime($tgl)));
		}

		$query = $this->db->query("SELECT IFNULL(sum(mp.qty),0) as tot_qty, IFNULL(sum(mp.qty2),0) as tot_kg, IFNULL(count(mp.lot),0) as tot_gl
								FROM  mrp_production_fg_hasil mp 
								WHERE mp.kode = '$kode' AND mp.create_date >= '$tgldari' AND mp.create_date <='$tglsampai'  
								and mp.nama_grade IN(SELECT nama_grade FROM mst_grade)");
		$result = $query->row_array();      
      	return $result;
	  
	}

	public function get_list_mrp_by_tgl($mc_id, $id_dept, $tgl)
	{
		$tgldari = date('Y-m-d 07:00:00', strtotime($tgl));
		$tglsampai = date('Y-m-d 06:59:59', strtotime('+1 days', strtotime($tgl)));
	
		return $this->db->query("SELECT  mp.kode, mp.nama_produk, ms.nama_mesin, mp.target_efisiensi
									FROM mrp_production mp 
									INNER JOIN mesin ms ON mp.mc_id = ms.mc_id
									INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode
									WHERE mp.dept_id = '$id_dept' AND mp.mc_id = '$mc_id' AND mpfg.create_date >= '$tgldari' AND mpfg.create_date <= '$tglsampai' 
									GROUP BY mp.kode
									ORDER BY mp.tanggal ")->result();
								 
	}


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

	/*

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
									
									
									

	*/

}