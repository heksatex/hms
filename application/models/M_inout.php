<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_inout extends CI_Model
{

    public function get_list_departement_select2($nama)
	{
		return $this->db->query("SELECT kode,nama FROM departemen  WHERE nama LIKE '%$nama%' ORDER BY nama ")->result();
	}


    public function get_list_penerimaan_harian_by_kode($tgldari,$tglsampai,$dept_id,$dept_dari,$status1)
    {
        $reff_picking = '';
        if(!empty($dept_dari)){
            $reff_picking .= " AND pb.reff_picking LIKE '%$dept_dari%' ";
        }
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, sq.reff_note, smi.status
                                FROM penerimaan_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND pb.dept_id = '$dept_id' $reff_picking $status
                                ORDER BY pb.kode,smi.kode_produk, smi.row_order")->result();

    }

    public function get_list_pengiriman_harian_by_kode($tgldari,$tglsampai,$dept_id,$dept_tujuan,$status1)
    {
        $reff_picking = '';
        if(!empty($dept_tujuan)){
            $reff_picking .= " AND pb.reff_picking LIKE '%$dept_tujuan%' ";
        }
        $status     = '';
        if(!empty($status1)){
            $status     .= " AND pb.status IN (".$status1.") ";
        }

        return $this->db->query("SELECT pb.kode, pb.origin, pb.reff_picking,pb.tanggal_transaksi, pb.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, sq.reff_note,  smi.status
                                FROM pengiriman_barang pb
                                INNER JOIN stock_move_items smi ON smi.move_id = pb.move_id
                                INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
                                WHERE pb.tanggal_transaksi >= '$tgldari' AND pb.tanggal_transaksi <= '$tglsampai'
                                AND pb.dept_id = '$dept_id' $reff_picking $status
                                ORDER BY pb.kode,smi.kode_produk, smi.row_order")->result();

    }

}