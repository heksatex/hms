<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_trackinglot extends CI_Model
{


    function get_data_info_by_lot($lot)
    {
        $this->db->where('lot',$lot);
		$this->db->order_by('create_date','desc');
		$query = $this->db->get('stock_quant');
		return $query->row_array();
    }

    function get_count_data_info_by_lot($lot)
    {
        $this->db->where('lot',$lot);
		$this->db->order_by('quant_id','asc');
		$query = $this->db->get('stock_quant');
		return $query->num_rows();
    }

    function get_pengiriman_barang_by_lot($lot)
    {
        return $this->db->query("SELECT pb.kode, pb.tanggal_transaksi, pb.lokasi_dari, pb.lokasi_tujuan, smi.status, ms.nama_status
                        FROM pengiriman_barang pb
                        INNER JOIN stock_move_items smi ON pb.move_id = smi.move_id
                        INNER JOIN mst_status ms ON smi.status = ms.kode
                        WHERE smi.lot ='$lot' ")->result();
    }


    function get_penerimaan_barang_by_lot($lot)
    {
        return $this->db->query("SELECT pb.kode, pb.tanggal_transaksi, pb.lokasi_dari, pb.lokasi_tujuan, smi.status, ms.nama_status
                        FROM penerimaan_barang pb
                        INNER JOIN stock_move_items smi ON pb.move_id = smi.move_id
                        INNER JOIN mst_status ms ON smi.status = ms.kode
                        WHERE smi.lot ='$lot' ")->result();
    }

    function get_mrp_by_lot($lot){
        return $this->db->query("SELECT fg.kode, fg.create_date, fg.kode_produk, fg.nama_produk, fg.lokasi, d.nama as nama_dept, fg.nama_user
                                FROM mrp_production_fg_hasil fg
                                INNER JOIN mrp_production mrp ON fg.kode = mrp.kode
                                INNER JOIN departemen d ON mrp.dept_id = d.kode
                                WHERE fg.lot = '$lot' ORDER by fg.create_date asc ")->result();
    }

    function get_transfer_lokasi_by_lot($lot){
        return $this->db->query("SELECT tl.kode_tl, tl.tanggal_transfer, tl.lokasi_tujuan, d.nama as departemen, tl.nama_user, tli.lot, tli.lokasi_asal, ms.nama_status
                                FROM transfer_lokasi tl
                                INNER JOIN transfer_lokasi_items tli ON tl.kode_tl = tli.kode_tl 
                                INNER JOIN departemen d ON d.kode = tl.dept_id 
                                INNER JOIN mst_status ms ON tl.status = ms.kode
                                WHERE tli.lot = '$lot' AND tl.status = 'done'  ")->result();
    }


    public function insert_tmp_tracking_lot_batch($sql)
	{
			return $this->db->query("INSERT INTO tmp_tracking_lot  (lot,tanggal,kode,keterangan,status,user,link) values $sql ");
	}

    public function delete_tmp_tracking_lot_by_lot($lot)
	{
            $this->db->delete('tmp_tracking_lot', array('lot' => $lot)); 
	}

    public function get_tmp_tracking_lot_by_lot($lot)
    {
        $this->db->where('lot',$lot);
		$this->db->order_by('tanggal','asc');
		$this->db->order_by('status','desc');
        $query = $this->db->get('tmp_tracking_lot');
		return $query->result();
    }


    
}

