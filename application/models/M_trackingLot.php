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
                        WHERE smi.lot LIKE '$lot%' ")->result();
    }


    function get_penerimaan_barang_by_lot($lot)
    {
        return $this->db->query("SELECT pb.kode, pb.tanggal_transaksi, pb.lokasi_dari, pb.lokasi_tujuan, smi.status, ms.nama_status
                        FROM penerimaan_barang pb
                        INNER JOIN stock_move_items smi ON pb.move_id = smi.move_id
                        INNER JOIN mst_status ms ON smi.status = ms.kode
                        WHERE smi.lot LIKE '$lot%' ")->result();
    }

    function get_mrp_by_lot($lot){
        return $this->db->query("SELECT fg.kode, fg.create_date, fg.kode_produk, fg.nama_produk, fg.lokasi, d.nama as nama_dept, fg.nama_user
                                FROM mrp_production_fg_hasil fg
                                INNER JOIN mrp_production mrp ON fg.kode = mrp.kode
                                INNER JOIN departemen d ON mrp.dept_id = d.kode
                                WHERE fg.lot LIKE '$lot%' ORDER by fg.create_date asc ")->result();
    }

    function get_mrp_cons_by_lot($lot){
        return $this->db->query("SELECT rm.kode, rm.kode_produk, rm.nama_produk, d.nama as nama_dept, smi.tanggal_transaksi
                                FROM mrp_production_rm_hasil rm
                                INNER JOIN mrp_production mrp ON rm.kode = mrp.kode
                                INNER JOIN stock_move_items smi ON smi.move_id = rm.move_id AND smi.quant_id = rm.quant_id
                                INNER JOIN departemen d ON mrp.dept_id = d.kode
                                WHERE rm.lot LIKE '$lot%' 
                                GROUP BY rm.kode, rm.lot
                                ORDER by smi.tanggal_transaksi desc ")->result();
    }

    function get_mrp_cons_target_by_lot($lot){
        return $this->db->query("SELECT rm.kode, rm.kode_produk, rm.nama_produk, d.nama as nama_dept, smi.tanggal_transaksi, smi.status, ms.nama_status
                                FROM mrp_production_rm_target rm
                                INNER JOIN mrp_production mrp ON rm.kode = mrp.kode
                                INNER JOIN stock_move_items smi ON smi.move_id = rm.move_id
                                INNER JOIN departemen d ON mrp.dept_id = d.kode
                                INNER JOIN mst_status ms ON smi.status = ms.kode
                                WHERE smi.lot LIKE '$lot%' AND smi.status NOT IN ('done') 
                                GROUP BY rm.kode, smi.lot
                                ORDER by smi.tanggal_transaksi asc
                                ")->result();
    }

    function get_transfer_lokasi_by_lot($lot){
        return $this->db->query("SELECT tl.kode_tl, tl.tanggal_transfer, tl.lokasi_tujuan, d.nama as departemen, tl.nama_user, tli.lot, tli.lokasi_asal, ms.nama_status
                                FROM transfer_lokasi tl
                                INNER JOIN transfer_lokasi_items tli ON tl.kode_tl = tli.kode_tl 
                                INNER JOIN departemen d ON d.kode = tl.dept_id 
                                INNER JOIN mst_status ms ON tl.status = ms.kode
                                WHERE tli.lot LIKE '$lot%' AND tl.status = 'done'  ")->result();
    }

    function get_adjustment_by_lot($lot){
        return $this->db->query("SELECT adj.kode_adjustment, adj.create_date, adj.lokasi_adjustment, adj.note, adj.nama_user,
                                adji.lot, adji.qty_move, adji.qty_data, adji.qty_adjustment,adji.uom, 
                                ms.nama_status
                                FROM adjustment adj
                                INNER JOIN adjustment_items adji ON adj.kode_adjustment = adji.kode_adjustment 
                                INNER JOIN mst_status ms ON adj.status = ms.kode
                                WHERE adji.lot LIKE '$lot%' AND adj.status = 'done'  ")->result();
    }

    function get_reproses_by_lot($lot){
        return $this->db->query("SELECT r.kode_reproses, r.tanggal, r.id_jenis, r.nama_user,
                                ri.lot, ri.lot_new, ms.nama_status, rj.nama_jenis,ri.lokasi_asal
                                FROM reproses r
                                INNER JOIN reproses_items ri ON r.kode_reproses = ri.kode_reproses
                                INNER JOIN mst_status ms ON r.status = ms.kode
                                INNER JOIN reproses_jenis rj ON r.id_jenis = rj.id
                                WHERE ri.lot LIKE '$lot%' AND r.status = 'done'  ")->result();
    }


    function get_split_by_lot($lot){
        return $this->db->query("SELECT s.kode_split, s.tanggal, s.dept_id, s.lot,s.nama_user,
                                (select count(*) as jml FROM split_items WHERE s.kode_split = kode_split) as total_split, d.nama as nama_departemen
                                FROM split s
                                INNER JOIN departemen d ON d.kode = s.dept_id
                                WHERE s.lot LIKE '$lot%'   ")->result();
    }

    function get_hasil_split_by_lot($lot){
        return $this->db->query("SELECT s.kode_split, s.tanggal, s.dept_id, s.lot  as lot_asal ,s.nama_user,d.nama as nama_departemen                         
                                FROM split s
                                INNER JOIN split_items si ON s.kode_split = si.kode_split
                                INNER JOIN departemen d ON d.kode = s.dept_id
                                WHERE si.lot_baru LIKE '$lot%'   ")->result();
    }

    
    function get_join_by_lot($lot){
        return $this->db->query("SELECT j.kode_join, j.tanggal_transaksi, j.dept_id, j.lot, j.nama_user,ms.nama_status, d.nama as nama_departemen,
                                (SELECT GROUP_CONCAT(lot SEPARATOR ' + ') FROM join_lot_items jli WHERE  jli.kode_join = j.kode_join) as lot_asal
                                FROM join_lot j
                                INNER JOIN departemen d ON d.kode = j.dept_id
                                INNER JOIN mst_status ms ON j.status = ms.kode
                                WHERE j.lot LIKE '$lot%' 
                                GROUP BY j.kode_join  ")->result();
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

    function get_inlet_by_Lot($lot){
        return $this->db->query("SELECT mrpin.id, mrpin.lot, mrpin.tanggal, mrpin.nama_user, ms.nama_status
                                FROM mrp_inlet mrpin
                                INNER JOIN mst_status ms ON mrpin.status = ms.kode
                                WHERE mrpin.lot LIKE '$lot%'  ")->result();
    }

    function get_hph_inlet_by_lot($lot){
        return $this->db->query("SELECT fg.kode,fg.lot, fg.create_date, fg.nama_user, fg.id_inlet
                                FROM mrp_production_fg_hasil fg
                                INNER JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
                                WHERE fg.lot LIKE '$lot%' ")->result();
    }

    function get_barcode_manual_by_lot($lot){
        return $this->db->query("SELECT mm.kode, mm.tanggal_transaksi,  mm.nama_user, ms.nama_status
                                FROM mrp_manual mm
                                INNER JOIN mrp_manual_batch_items mbi ON mm.kode = mbi.kode
                                INNER JOIN mst_status ms ON mm.status = ms.kode
                                WHERE mbi.lot LIKE '$lot%' ")->result();
    }


    function get_picklist_by_Lot($lot){
        return $this->db->query("SELECT pl.no_pl, pl.barcode_id, pl.tanggal_masuk, ms.nama_status
                                FROM picklist_detail pl
                                INNER JOIN mst_status ms ON pl.valid = ms.kode
                                WHERE pl.barcode_id LIKE '$lot%'  ")->result();
    }


    function get_delivery_by_Lot($lot){
        return $this->db->query("SELECT do.no, do.no_sj, do.tanggal_buat, do.user as nama_user, ms.nama_status
                                FROM delivery_order do
                                INNER JOIN delivery_order_detail as dod ON do.id = dod.do_id
                                INNER JOIN mst_status ms ON dod.status = ms.kode
                                WHERE dod.barcode_id LIKE '$lot%'  ")->result();
    }


    
}

