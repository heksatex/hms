<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_efisiensi extends CI_Model
{

	public function get_list_departement_select2($nama)
	{
		return $this->db->query("SELECT kode,nama FROM departemen  WHERE nama LIKE '%$nama%' ORDER BY nama ")->result();
	}

	public function get_list_mrp_by_tgl($id_dept, $tgl)
	{
		$tgldari = date('Y-m-d 00:00:00', strtotime($tgl));
		$tglsampai = date('Y-m-d 23:59:59', strtotime('+1 days', strtotime($tgl)));
		/*
		return $this->db->query("SELECT  mp.kode, mp.tanggal, mp.origin, mp.nama_produk,mp.reff_note, mp.status, ms.nama_mesin, mp.target_efisiensi
								 FROM mrp_production mp 
								 INNER JOIN mesin ms ON mp.mc_id = ms.mc_id
								 WHERE mp.dept_id = '$id_dept' AND mp.tanggal >= '$tgldari' AND mp.tanggal <= '$tglsampai'")->result();
								 */
		return $this->db->query("SELECT  mp.kode, mp.tanggal, mp.origin, mp.nama_produk,mp.reff_note, mp.status, ms.nama_mesin, mp.target_efisiensi
								 FROM mrp_production mp 
								 LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id
								 INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode
								 INNER JOIN stock_quant sq ON mpfg.quant_id = sq.quant_id
								 WHERE mp.dept_id = '$id_dept' AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai' 
								 GROUP BY mp.kode")->result();
								 
	}

	public function get_list_hph_by_date($kode, $tgl, $shift)
	{	

		if($shift == 'pagi'){
			$tgldari   = date('Y-m-d 07:00:00', strtotime($tgl));
			$tglsampai = date('Y-m-d 15:00:00', strtotime($tgl));
		}else if($shift == 'siang'){
			$tgldari   = date('Y-m-d 15:00:00', strtotime($tgl));
			$tglsampai = date('Y-m-d 23:00:00', strtotime($tgl));
		}else{
			$tgldari   = date('Y-m-d 23:00:00', strtotime($tgl));
			$tglsampai = date('Y-m-d 07:00:00', strtotime('+1 days', strtotime($tgl)));
		}

		$query = $this->db->query("SELECT IFNULL(sum(mp.qty),0) as tot_qty 
								FROM  mrp_production_fg_hasil mp 
								INNER JOIN stock_quant sq ON mp.quant_id = sq.quant_id 
								WHERE mp.kode = '$kode' AND sq.create_date >= '$tgldari' AND sq.create_date <='$tglsampai' ");
		$result = $query->result_array();      
      	return $result[0]['tot_qty'];


	}

}