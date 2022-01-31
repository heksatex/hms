<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_penerimaanBarang extends CI_Model
{
	
	//var $table 		  = 'penerimaan_barang';
	var $column_order = array(null, 'kode', 'tanggal', 'tanggal_transaksi', 'tanggal_jt', 'lokasi_tujuan','reff_picking','reff_note', 'nama_status');
	var $column_search= array( 'kode', 'tanggal', 'tanggal_transaksi', 'tanggal_jt',  'lokasi_tujuan','reff_picking','reff_note', 'nama_status');
	var $order  	  = array('kode' => 'desc');

	var $table3  	    = 'stock_quant';
	var $column_order3  = array(null, 'nama_produk', 'lot', 'qty', 'qty2', 'reff_note');
	var $column_search3 = array('nama_produk', 'lot', 'qty', 'qty2', 'reff_note');
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

		$this->db->select("pb.kode, pb.tanggal, pb.tanggal_transaksi, pb.tanggal_jt, pb.lokasi_tujuan, pb.reff_picking, pb.status, pb.reff_note, mmss.nama_status");
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
								(SELECT sum(smi.qty) FROM stock_move_items smi 	WHERE  smi.move_id = pb.move_id And smi.kode_produk = pbi.kode_produk) as sum_qty
								FROM penerimaan_barang_items pbi
							    INNER JOIN penerimaan_barang pb ON pbi.kode = pb.kode
								WHERE pbi.kode = '$kode' ORDER BY row_order")->result();
	}

	public function get_list_penerimaan_barang_print($kode,$dept_id)
	{
		return $this->db->query("SELECT pbi.lot,pbi.nama_produk, pbi.qty, pbi.kode_produk, pbi.nama_produk, pbi.uom, pbi.qty, pbi.status_barang, pbi.origin_prod,
								(SELECT sum(smi.qty) FROM stock_move_items smi 	WHERE  smi.move_id = pb.move_id And smi.kode_produk = pbi.kode_produk) as sum_qty
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
		return $this->db->query("SELECT smi.quant_id, smi.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, smi.row_order, sq.reff_note
								 FROM stock_move_items smi 
								 INNER JOIN penerimaan_barang pb ON smi.move_id = pb.move_id
								 INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
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

	public function update_penerimaan_barang($kode,$tgl_transaksi,$reff_note)
	{
		return $this->db->query("UPDATE penerimaan_barang set tanggal_transaksi = '$tgl_transaksi', reff_note = '$reff_note' WHERE kode = '$kode'");
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

	public function get_kode_mo_penerimaan_barang_by_move_id($move_id)
	{	
		return $this->db->query("SELECT sm.move_id, rm.move_id,rm.kode
								FROM stock_move sm
								INNER JOIN mrp_production_rm_target rm ON sm.move_id =rm.move_id
								WHERE sm.source_move = '$move_id' LIMIT 1");
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

}


?>