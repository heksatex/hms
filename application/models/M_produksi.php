<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_produksi extends CI_Model
{
    public function cek_departemen_by_user($where)
    {
        if($where){
            $this->db->where($where);
        }
        return  $this->db->get('user');
        
    }

    public function cek_dept_by_kode($where)
    {
        if($where){
            $this->db->where($where);
        }
        return  $this->db->get('mst_departemen_all');
    }

    public function cek_dept_produksi_by_kode($where)
    {
        if($where){
            $this->db->where($where);
        }
        return  $this->db->get('departemen');
        
    }

    var $column_search = array('mp.kode','mp.origin','mp.nama_produk','mp.reff_note');

    public function get_list_mo(array $where = [], string $keyword = '', string $progress = 'show')
    {   
        if($progress == 'hide') {
            $this->db->having('IFNULL(ROUND(SUM(fg.qty) / mp.qty * 100),0) <= 95');
        }

        if($where){
            $this->db->where($where);
        }

        if ($keyword !== '') {

            $this->db->group_start();

            foreach ($this->column_search as $i => $item) {

                if ($i === 0) {
                    $this->db->like($item, $keyword);
                } else {
                    $this->db->or_like($item, $keyword);
                }

            }

            $this->db->group_end();
        }

        $this->db->order_by('start_time', 'asc');
        $this->db->select("mp.kode, mp.tanggal, mp.origin, mp.kode_produk, mp.nama_produk, mp.qty, mp.uom, mp.status, ms.nama_status, mp.reff_note, mp.responsible, mp.mc_id, COALESCE(SUM(fg.qty), 0) AS qty_produced, mp.lot_prefix, mp.qty1_std, mp.qty2_std, prod.uom, prod.uom_2");
		$this->db->from("mrp_production mp");
        $this->db->JOIN("product_planning as pp", "mp.kode = pp.kode", "INNER");
		$this->db->join("mst_status ms", "ms.kode=mp.status", "inner");
        $this->db->JOIN("mst_produk prod", "mp.kode_produk=prod.kode_produk", "left");
        $this->db->JOIN("mrp_production_fg_hasil fg", "mp.kode = fg.kode", "left");
        $this->db->group_by("mp.kode");
        return  $this->db->get();
    }

    public function cek_mesin_by_kode($where)
    {
        if($where){
            $this->db->where($where);
        }
        return  $this->db->get('mesin');
    }


    public function cek_mrp_production(array $where = [])
	{
        if($where){
            $this->db->where($where);
        }
        $this->db->order_by('tanggal','asc');
        $result = $this->db->get('mrp_production');
        return $result;
	}

    function save_batch($table, $data)
    {   
       
        $result = $this->db->insert_batch($table, $data);

        if ($result === false) {
            return false;
        }

        $error = $this->db->error();

        return empty($error['code']);

    }

    public function startTransaction()
    {
        $this->db->trans_begin();
    }

    public function finishTransaction()
    {
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_commit();
        return true;
    }

    public function rollbackTransaction()
    {
        $this->db->trans_rollback();
    }


    public function get_list_barang_jadi_hasil($kode,$lokasi_waste)
	{
		// tipe adjustment
		// 1=Koreksi MO, 2=Koreksi Salah INput User
		return $this->db->query("SELECT fg.kode, fg.create_date, fg.move_id, fg.quant_id, fg.kode_produk, fg.kode_produk, fg.nama_produk, 
										fg.lot, fg.nama_grade, fg.qty, fg.uom, fg.row_order, sq.reff_note, fg.qty2, fg.uom2, fg.lebar_greige, fg.uom_lebar_greige, fg.lebar_jadi, fg.uom_lebar_jadi,(SELECT lot FROM adjustment_items adji 
									INNER JOIN adjustment adj ON adji.kode_adjustment = adj.kode_adjustment
									where adj.status = 'done' AND adji.quant_id = fg.quant_id AND adj.id_type_adjustment IN ('1','2') limit 1 ) as lot_adj, mrpin.lot as lot_asal
								FROM mrp_production_fg_hasil fg 
								INNER JOIN stock_quant sq ON fg.quant_id =  sq.quant_id
								LEFT JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
								WHERE fg.kode = '".$kode."' AND fg.lokasi NOT IN ('".$lokasi_waste."') ORDER BY fg.row_order desc")->result();
	}

    var $column_search2 = array('no_mesin', 'nama_mesin');

    public function get_list_mesin(array $where = [], string $keyword)
	{
        
        if($where){
            $this->db->where($where);
        }

        if ($keyword !== '') {

            $this->db->group_start();

            foreach ($this->column_search2 as $i => $item) {

                if ($i === 0) {
                    $this->db->like($item, $keyword);
                } else {
                    $this->db->or_like($item, $keyword);
                }

            }

            $this->db->group_end();
        }
        $this->db->select('m.nama_mesin, m.mc_id,  COUNT(mp.mc_id) AS total_mo');
        $this->db->from('mesin m');
        // $this->db->join("mrp_production as mp", "mp.dept_id = m.dept_id    AND mp.mc_id = m.mc_id    AND mp.status = 'ready'", "left");
        // $this->db->join("product_planning as pp", "pp.kode = mp.kode", "LEFT");
        $this->db->JOIN("(select mp.mc_id, mp.status, mp.kode, mp.dept_id
                            FROM mrp_production mp 
                            inner JOIN product_planning as pp ON mp.kode = pp.kode
                            WHERE mp.status NOT IN ('cancel','done','draft') ) as mp ", "mp.dept_id = m.dept_id  AND mp.mc_id = m.mc_id", "LEFT");
        $this->db->group_by("m.mc_id");
        // $this->db->select("m.nama_mesin, m.mc_id,
        //         (SELECT count(*) as total FROM mrp_production WHERE status = 'ready' AND dept_id = m.dept_id AND mc_id = m.mc_id)  as total_mo");
        // $this->db->from('mesin as m');
        $this->db->order_by('m.row_order');
        $query = $this->db->get();

        return $query;
        
	}

    public function get_data_fg_hasil_by_kode($kode,$quant_id)
    {
            return $this->db->query("SELECT fg.create_date,fg.kode_produk, fg.nama_produk, fg.lot, fg.qty, fg.uom, fg.qty2, fg.uom2, sq.reff_note, mph.reff_note as note_head, sq.nama_grade
                                    FROM mrp_production_fg_hasil fg
                                    LEFT JOIN stock_quant sq ON fg.quant_id = sq.quant_id
                                    LEFT Join mrp_production mph ON fg.kode = mph.kode
                                    where fg.kode = '$kode' AND fg.quant_id = '$quant_id' ");
    }


    public function get_move_id_rm_target_by_kode($kode)
	{
		// get move id rm yg category produk nya tidak 11(aux) dan 12 (DYE)
		return $this->db->query("SELECT DISTINCT(rm.move_id) as move_id 
								FROM mrp_production_rm_target as rm 
								INNER JOIN mst_produk mprod ON rm.kode_produk = mprod.kode_produk 
								WHERE rm.kode = '$kode' AND mprod.id_category NOT IN ('11','12') AND rm.move_id != '' 
								ORDER BY mid(rm.move_id,3,(length(rm.move_id))-2) asc" );
	}

    public function get_konsumsi_bahan($kode,$status)
	{
		return $this->db->query("SELECT smi.move_id, smi.quant_id,smi.kode_produk, smi.nama_produk, 
								smi.lot, smi.qty, smi.uom,smi.origin_prod,smi.qty2,smi.uom2, rm.qty as qty_rm,rm.additional, sq.reff_note,sq.nama_grade,mprod.type,
								(SELECT count(kode_produk) as jml_prod FROM stock_move_items smi2 WHERE 
									smi2.kode_produk = smi.kode_produk AND smi2.move_id = smi.move_id AND smi2.origin_prod = smi.origin_prod AND smi2.status = '$status' ) as jml_produk,
								smi.lebar_greige, smi.uom_lebar_greige, smi.lebar_jadi, smi.uom_lebar_jadi, sq.sales_order, sq.sales_group
								FROM stock_move_items smi
								INNER JOIN mrp_production_rm_target rm ON smi.origin_prod = rm.origin_prod AND rm.move_id = smi.move_id
								INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id 
								INNER JOIN mst_produk mprod ON rm.kode_produk = mprod.kode_produk
								where rm.kode = '$kode' AND smi.status = '$status' AND mprod.id_category NOT IN ('11','12') order by smi.nama_produk,smi.lot ")->result();
	
	}

}


