<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_efisiensi extends CI_Model
{

	public function get_list_departement_select2($nama)
	{
		return $this->db->query("SELECT kode,nama FROM departemen  WHERE  show_dept = 'true' AND  nama LIKE '%$nama%' ORDER BY nama ")->result();
	}

	public function get_list_mrp_by_tgl($mc_id, $id_dept, $tgl)
	{
		$tgldari = date('Y-m-d 07:00:00', strtotime($tgl));
		$tglsampai = date('Y-m-d 06:59:59', strtotime('+1 days', strtotime($tgl)));
		/*
		return $this->db->query("SELECT  mp.kode, mp.tanggal, mp.origin, mp.nama_produk,mp.reff_note, mp.status, ms.nama_mesin, mp.target_efisiensi
								 FROM mrp_production mp 
								 INNER JOIN mesin ms ON mp.mc_id = ms.mc_id
								 WHERE mp.dept_id = '$id_dept' AND mp.tanggal >= '$tgldari' AND mp.tanggal <= '$tglsampai'")->result();
								 */
		return $this->db->query("SELECT  mp.kode, mp.tanggal, mp.origin, mp.kode_produk, mp.nama_produk,mp.reff_note, mp.status, ms.nama_mesin, mp.target_efisiensi
								 FROM mrp_production mp 
								 LEFT JOIN mesin ms ON mp.mc_id = ms.mc_id
								 INNER JOIN mrp_production_fg_hasil mpfg ON mp.kode = mpfg.kode AND mp.kode_produk = mpfg.kode_produk
								 INNER JOIN stock_quant sq ON mpfg.quant_id = sq.quant_id
								 WHERE mp.dept_id = '$id_dept' AND mp.mc_id = '$mc_id' AND sq.create_date >= '$tgldari' AND sq.create_date <= '$tglsampai' 
								 GROUP BY mp.kode
								 ORDER BY mp.tanggal ")->result();
								 
	}

	public function get_list_hph_by_date($kode, $tgl, $kode_produk, $shift)
	{	

		if($shift == 'pagi'){
			$tgldari   = date('Y-m-d 07:00:00', strtotime($tgl));
			$tglsampai = date('Y-m-d 14:59:59', strtotime($tgl));
		}else if($shift == 'siang'){
			$tgldari   = date('Y-m-d 15:00:00', strtotime($tgl));
			$tglsampai = date('Y-m-d 22:59:59', strtotime($tgl));
		}else{
			$tgldari   = date('Y-m-d 23:00:00', strtotime($tgl));
			$tglsampai = date('Y-m-d 06:59:59', strtotime('+1 days', strtotime($tgl)));
		}

		/*
		$query = $this->db->query("SELECT IFNULL(sum(mp.qty),0) as tot_qty 
								FROM  mrp_production_fg_hasil mp 
								WHERE mp.kode = '$kode' AND mp.kode_produk = '$kode_produk' AND mp.create_date >= '$tgldari' AND mp.create_date <='$tglsampai' ");
		*/

		$query = $this->db->query("SELECT IFNULL(sum(mpfg.qty),0) as tot_qty, 
											(SELECT IFNULL(sum(adji.qty_move),0) FROM adjustment_items adji 
											INNER JOIN adjustment adj ON adji.kode_adjustment = adj.kode_adjustment
											WHERE adj.status='done' AND adj.create_date >= '$tgldari' AND adj.create_date <= '$tglsampai' AND 
													adj.kode_lokasi = mp.destination_location AND adji.kode_produk = mpfg.kode_produk AND adji.mrp_kode = mp.kode ) as tot_adj
									FROM  mrp_production_fg_hasil mpfg 
									INNER JOIN mrp_production mp ON mpfg.kode = mp.kode
									WHERE mp.kode = '$kode' AND mpfg.kode_produk = '$kode_produk' AND mpfg.create_date >= '$tgldari' AND
										 mpfg.create_date <= '$tglsampai' ");
		$result = $query->row_array();      
      	return $result;


	}

	public function get_list_mesin($dept_id)
	{
		return $this->db->query("SELECT * FROM mesin WHERE dept_id = '$dept_id' AND status_aktif = 't' order by row_order ")->result();
	}


}