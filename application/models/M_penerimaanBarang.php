<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_penerimaanBarang extends CI_Model
{
	
	//var $table 		  = 'penerimaan_barang';
	var $column_order = array(null, 'kode', 'tanggal', 'tanggal_transaksi', 'origin', 'lokasi_tujuan','reff_picking','reff_note', 'nama_status');
	var $column_search= array( 'kode', 'tanggal', 'tanggal_transaksi', 'origin',  'lokasi_tujuan','reff_picking','reff_note', 'nama_status');
	var $order  	  = array('kode' => 'desc');

	var $table3  	    = 'stock_quant';
	var $column_order3  = array(null, 'kode_produk', 'nama_produk', 'lot', 'qty', 'qty2', 'reff_note');
	var $column_search3 = array('kode_produk','nama_produk', 'lot', 'qty', 'qty2', 'reff_note');
	var $order3  	    = array('create_date' => 'asc');

	private function _get_datatables_query()
	{
		//add custom filter here
        if($this->input->post('kode'))
        {
            $this->db->like('kode', $this->input->post('kode'));
        }
        if($this->input->post('status'))
        {
            $this->db->like('status', $this->input->post('status'));
        }
        if($this->input->post('reff'))
        {
            $this->db->like('reff_note', $this->input->post('reff'));
        }
        if($this->input->post('reff_picking'))
        {
            $this->db->like('reff_picking', $this->input->post('reff_picking'));
        }

		//$this->db->from($this->table);

		$this->db->select("pb.kode, pb.tanggal, pb.tanggal_transaksi, pb.tanggal_jt, pb.lokasi_tujuan, pb.reff_picking, pb.status, pb.reff_note, mmss.nama_status, pb.origin");
		$this->db->from("penerimaan_barang pb");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pb.status", "inner");

		$i = 0;
	
		foreach ($this->column_search as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables($id_dept,$mmss)
	{
		$this->_get_datatables_query();
		$this->db->where('dept_id',$id_dept);
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($id_dept,$mmss)
	{
		$this->db->where('dept_id',$id_dept);
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_dept,$mmss)
	{
		//$this->db->from($this->table);
		$this->db->select("pb.kode, pb.tanggal, pb.tanggal_transaksi, pb.tanggal_jt, pb.lokasi_tujuan, pb.reff_picking, pb.status, pb.reff_note, mmss.nama_status");
		$this->db->from("penerimaan_barang pb");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pb.status", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->db->where('dept_id',$id_dept);
		return $this->db->count_all_results();
	}


	private function _get_datatables3_query()
	{
		$this->db->from($this->table3);

		$i = 0;
	
		foreach ($this->column_search3 as $item) // loop column 
		{
			if($_POST['search']['value']) // if datatable send POST for search
			{
				
				if($i===0) // first loop
				{
					$this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
					$this->db->like($item, $_POST['search']['value']);
				}
				else
				{
					$this->db->or_like($item, $_POST['search']['value']);
				}

				if(count($this->column_search3) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order3[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order3))
		{
			$order = $this->order3;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables3($kode_produk,$lokasi,$origin,$dept_id)
	{
		$this->_get_datatables3_query();
		$this->db->where('kode_produk',$kode_produk );
		$this->db->where('lokasi', $lokasi);
		$this->db->where('reserve_move','');
		$this->db->where_not_in('qty','0');
		//cek type departement
		$cek_dept = $this->_module->cek_departement_by_kode($dept_id)->row_array();
		if($cek_dept['type_dept'] == 'manufaktur'){
			$this->db->where('reserve_origin',$origin);	
		}
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered3($kode_produk,$lokasi,$origin,$dept_id)
	{
		$this->db->where('kode_produk',$kode_produk );
		$this->db->where('reserve_move','');
		$this->db->where('lokasi', $lokasi);
		$this->db->where_not_in('qty','0');
		//cek type departement
		$cek_dept = $this->_module->cek_departement_by_kode($dept_id)->row_array();
		if($cek_dept['type_dept'] == 'manufaktur'){
			$this->db->where('reserve_origin',$origin);	
		}
		$this->_get_datatables3_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all3($kode_produk,$lokasi,$origin,$dept_id)
	{
		$this->db->where('kode_produk',$kode_produk );
		$this->db->where('reserve_move','');
		$this->db->where('lokasi', $lokasi);
		$this->db->where_not_in('qty','0');
		//cek type departement
		$cek_dept = $this->_module->cek_departement_by_kode($dept_id)->row_array();
		if($cek_dept['type_dept'] == 'manufaktur'){
			$this->db->where('reserve_origin',$origin);	
		}
		$this->db->from($this->table3);
		return $this->db->count_all_results();
	}


	public function get_data_by_code($kode)
	{
		$query = $this->db->query("SELECT * FROM penerimaan_barang where kode = '".$kode."' ");
		return $query->row();
	}

	public function get_data_by_code_print($kode,$dept_id)
	{
		$query = $this->db->query("SELECT * FROM penerimaan_barang where kode = '".$kode."' AND dept_id  = '".$dept_id."' ");
		return $query->row();
	}


	public function get_list_penerimaan_barang($kode)
	{
		return $this->db->query("SELECT pbi.lot,pbi.nama_produk, pbi.qty, pbi.kode_produk, pbi.nama_produk, pbi.uom, pbi.qty, pbi.status_barang, pbi.origin_prod,
								(SELECT sum(smi.qty) FROM stock_move_items smi 	WHERE  smi.move_id = pb.move_id And smi.kode_produk = pbi.kode_produk AND smi.origin_prod = pbi.origin_prod) as sum_qty
								FROM penerimaan_barang_items pbi
							    INNER JOIN penerimaan_barang pb ON pbi.kode = pb.kode
								WHERE pbi.kode = '$kode' ORDER BY row_order")->result();
	}

	public function get_list_penerimaan_barang_print($kode,$dept_id)
	{
		return $this->db->query("SELECT pbi.lot,pbi.nama_produk, pbi.qty, pbi.kode_produk, pbi.nama_produk, pbi.uom, pbi.qty, pbi.status_barang, pbi.origin_prod,
								(SELECT sum(smi.qty) FROM stock_move_items smi 	WHERE  smi.move_id = pb.move_id And smi.kode_produk = pbi.kode_produk AND smi.origin_prod = pbi.origin_prod) as sum_qty
								FROM penerimaan_barang_items pbi
							    INNER JOIN penerimaan_barang pb ON pbi.kode = pb.kode
								WHERE pbi.kode = '$kode' AND pb.dept_id = '$dept_id' ORDER BY row_order")->result();
	}

	public function get_stock_move_by_kode($kode)
	{
		return $this->db->query("SELECT sm.move_id, sm.create_date,sm.origin, sm.method, sm.lokasi_dari, sm.lokasi_tujuan, sm.status
								 FROM stock_move sm
								 INNER JOIN penerimaan_barang pb ON sm.move_id = pb.move_id 
								 WHERE pb.kode = '$kode' ");
	}

	public function get_stock_move_items_by_kode($kode)
	{
		return $this->db->query("SELECT smi.quant_id, smi.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, smi.row_order, sq.reff_note, tmp.valid, smi.lebar_greige,smi.uom_lebar_greige, smi.lebar_jadi, smi.uom_lebar_jadi
								FROM stock_move_items smi 
								INNER JOIN penerimaan_barang pb ON smi.move_id = pb.move_id
								INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
								LEFT JOIN penerimaan_barang_tmp tmp ON pb.kode = tmp.kode AND smi.lot = tmp.lot
								Where pb.kode = '$kode' 
								ORDER BY smi.row_order")->result();
	}


	public function get_stock_move_items_by_kode_print($kode,$dept_id)
	{
		return $this->db->query("SELECT smi.quant_id, smi.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, smi.row_order, sq.reff_note
								 FROM stock_move_items smi 
								 INNER JOIN penerimaan_barang pb ON smi.move_id = pb.move_id
								 INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
								 Where pb.kode = '$kode'  AND pb.dept_id = '$dept_id'
								 ORDER BY smi.row_order")->result();
	}

	public function cek_status_barang($kode)
	{
		return $this->db->query("SELECT status FROM penerimaan_barang where kode = '$kode'");
	}

	public function update_penerimaan_barang($kode,$reff_note)
	{
		return $this->db->query("UPDATE penerimaan_barang set reff_note = '$reff_note' WHERE kode = '$kode'");
	}

	public function cek_status_barang_penerimaan_barang_items($kode,$status)
	{
		return $this->db->query("SELECT status_barang FROm penerimaan_barang_items where kode = '$kode' AND status_barang = '$status'");
	}

	public function get_location_by_move_id($move_id)
	{
		return $this->db->query("SELECT lokasi_dari, lokasi_tujuan From stock_move where move_id = '$move_id'");
	}

	public function update_status_penerimaan_barang($kode,$status)
	{
		return $this->db->query("UPDATE penerimaan_barang SET status = '$status' WHERE kode = '$kode'");
	}

	public function update_tgl_kirim_penerimaan_barang($kode,$tgl)
	{
		return $this->db->query("UPDATE penerimaan_barang SET tanggal_transaksi = '$tgl' WHERE kode = '$kode'");
	}

	public function update_status_penerimaan_barang_items_full($kode,$status)
	{
		return $this->db->query("UPDATE penerimaan_barang_items SET status_barang = '$status' WHERE kode = '$kode' ");
	}

	public function update_perbatch($sql)
	{
	 	return $this->db->query(" $sql ");
	}

	public function get_move_id_by_kode($kode)
	{
		return $this->db->query("SELECT pb.move_id,  pb.status, pb.dept_id, sm.method, pb.origin
								 FROM stock_move sm
								 INNER JOIN penerimaan_barang pb ON sm.move_id = pb.move_id 
								 WHERE pb.kode = '$kode'");

	}

	/*
	public function get_kode_mo_penerimaan_barang_by_move_id($move_id)
	{	
		return $this->db->query("SELECT sm.move_id, rm.move_id,rm.kode
								FROM stock_move sm
								INNER JOIN mrp_production_rm_target rm ON sm.move_id =rm.move_id
								WHERE sm.source_move LIKE '%$move_id%' LIMIT 1");
	}
	*/
	
	public function cek_move_id_by_kode($origin,$method)
	{
		return $this->db->query("SELECT move_id FROM stock_move WHERE origin = '$origin' AND method = '$method'");
	}
	public function get_kode_mrp_by_move_id($move_id)
	{
		return $this->db->query("SELECT DISTINCT rm.kode, mrp.dept_id, d.nama
								FROM mrp_production_rm_target as rm
								INNER JOIN mrp_production as mrp ON rm.kode= mrp.kode
								INNER JOIN departemen as d ON mrp.dept_id = d.kode
								where move_id = '$move_id'");
	}


	public function get_qty_penerimaan_barang_items_by_kode($kode,$kode_produk)
    {
   		return $this->db->query("SELECT qty FROM penerimaan_barang_items WHERE kode = '$kode' AND kode_produk = '$kode_produk'");
    }

    public function update_status_penerimaan_barang_items($kode,$kode_produk,$status)
	{
		return $this->db->query("UPDATE penerimaan_barang_items SET status_barang = '$status' WHERE kode = '$kode' AND kode_produk = '$kode_produk' ");
	}
	
	public function cek_status_penerimaan_barang($kode)
	{
		return $this->db->query("SELECT status FROM penerimaan_barang WHERE kode = '$kode'");
	}

	public function get_list_penerimaan_barang_items($kode)
    {
   	    return $this->db->query("SELECT * FROM penerimaan_barang_items WHERE kode = '$kode' order by row_order")->result();
	}
	
	public function get_origin_prod_mrp_production_by_kode($move_id,$kode_produk)
	{
		return $this->db->query("SELECT * FROM mrp_production_rm_target where move_id = '$move_id' AND kode_produk = '$kode_produk' order by row_order ");
	}

	public function cek_scan_by_lot($kode,$lot)
	{
		return $this->db->query("SELECT lot FROM penerimaan_barang_tmp WHERE kode = '$kode' AND lot = '$lot'");
	}

	public function get_list_stock_move_items_by_lot($move_id,$lot,$status)
	{
		return $this->db->query("SELECT * FROM stock_move_items where move_id = '$move_id' AND lot = '$lot' AND status = '$status'")->result();
	}

	public function simpan_penerimaan_barang_tmp($kode,$quant_id,$move_id,$kode_produk,$lot,$valid,$tgl)
	{
		return $this->db->query("INSERT INTO penerimaan_barang_tmp (kode,quant_id,move_id,kode_produk,lot,valid,valid_date) VALUES ('$kode','$quant_id','$move_id','$kode_produk','$lot','$valid','$tgl')");
	}

	public function get_count_valid_scan_by_kode($kode)
	{
		$this->db->SELECT('kode');
		$this->db->FROM('penerimaan_barang_tmp');
		$this->db->where('kode', $kode);
		$this->db->where('valid', 't');
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function get_count_all_scan_by_kode($move_id)
	{
		$this->db->SELECT('move_id');
		$this->db->FROM('stock_move_items');
		$this->db->where('move_id', $move_id);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function get_stock_move_items_by_move_id_partial_in($move_id)
	{
		return $this->db->query("SELECT  smi.move_id, smi.quant_id, smi.tanggal_transaksi, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, smi.origin_prod, smi.row_order, tmp.valid, smi.lokasi_fisik, smi.lebar_greige, smi.uom_lebar_greige, smi.lebar_jadi, smi.uom_lebar_jadi
								FROM stock_move_items  smi
								INNER JOIN penerimaan_barang_tmp tmp ON smi.move_id = tmp.move_id AND smi.lot = tmp.lot
								WHERE smi.move_id = '$move_id' 
								order by smi.row_order ")->result();
	}

	/*
	public function get_qty_stock_move_items_partial_by_kode($move_id,$kode_produk)
    {
   		return $this->db->query("SELECT sum(smi.qty) as sum_qty 
								FROM stock_move_items  smi
								INNER JOIN penerimaan_barang_tmp tmp ON smi.move_id = tmp.move_id AND smi.lot = tmp.lot
								WHERE  smi.move_id = '$move_id' And smi.kode_produk = '$kode_produk'  ");
    }
	*/

	public function get_stock_move_items_not_penerimaan_barang_tmp($move_id)
    {
   		return $this->db->query("SELECT  smi.move_id, smi.quant_id, smi.tanggal_transaksi, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, smi.origin_prod, smi.row_order, tmp.valid, smi.lokasi_fisik, smi.lebar_greige, smi.uom_lebar_greige, smi.lebar_jadi, smi.uom_lebar_jadi
							FROM stock_move_items  smi
							LEFT JOIN penerimaan_barang_tmp tmp ON smi.move_id = tmp.move_id AND smi.lot = tmp.lot
							WHERE smi.move_id = '$move_id' AND smi.lot NOT IN (SELECT lot FROM penerimaan_barang_tmp WHERE move_id = '$move_id')
							order by smi.row_order ")->result();
    }

	public function cek_penerimaan_barang_tmp_by_kode($kode)
	{
		$this->db->where('kode', $kode);
		$query = $this->db->get('penerimaan_barang_tmp');
		return $query->num_rows();
		
	}

	public function get_type_mo_dept_id_mrp_production_by_kode($kode_mo)
	{
		$query = $this->db->query("SELECT mrp.dept_id, dept.type_mo
									FROM mrp_production mrp 
									INNER JOIN departemen dept ON mrp.dept_id =dept.kode 
									where mrp.kode in ($kode_mo) ")->row_array();
		return $query;
	}

	public function cek_mrp_production_rm_target_by_kode($kode_mo)
	{
		return $this->db->query("SELECT status FROM mrp_production_rm_target where kode in ($kode_mo) AND status IN ('draft','cancel') ");
	}

	public function cek_stock_move_items_penerimaan_barang_by_move_id($move_id)
	{
		$this->db->where('move_id', $move_id);
		$query = $this->db->get('stock_move_items');
		return $query->num_rows();
	}

	public function cek_jml_produk_sama_penerimaan_barang_by_kode($kode,$kode_produk)
	{
		return $this->db->query("SELECT count(kode_produk) as tot 
								FROM penerimaan_barang_items   
								where kode = '$kode' AND  kode_produk = '$kode_produk'
								GROUP BY kode_produk
								having COUNT(kode_produk) > 1 ");
	}

	public function get_qty_produk_penerimaan_by_kode_origin($kode,$kode_produk,$origin_prod)
	{
		$query =  $this->db->query("SELECT qty FROM penerimaan_barang_items where kode = '$kode' AND kode_produk = '$kode_produk' AND origin_prod = '$origin_prod'")->row_array()
		;
		return $query['qty'];
	}

	public function get_qty_produk_penerimaan_by_kode($kode,$kode_produk)
	{
		$query =  $this->db->query("SELECT qty FROM penerimaan_barang_items where kode = '$kode' AND kode_produk = '$kode_produk'")->row_array()
		;
		return $query['qty'];
	}

 

}


?>