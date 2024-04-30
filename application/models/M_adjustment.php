<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_adjustment extends CI_Model
{
	var $column_order = array(null, 'kode_adjustment', 'create_date', 'lokasi_adjustment', 'kode_lokasi','name_type', 'note', 'status');
	var $column_search= array('kode_adjustment', 'create_date', 'lokasi_adjustment', 'kode_lokasi', 'name_type', 'note', 'status');
	var $order  	  = array('create_date' => 'desc');

	var $table2  	    = 'stock_quant';
	var $column_order2  = array(null, 'kode_produk', 'nama_produk', 'lot', 'qty', 'qty2', 'nama_grade', 'reff_note', 'reserve_move');
	var $column_search2 = array('kode_produk','nama_produk', 'lot', 'qty', 'qty2', 'nama_grade', 'reff_note', 'reserve_move');
	var $order2  	    = array('nama_produk' => 'asc');

	private function _get_datatables_query()
	{
		$this->db->select("adj.kode_adjustment,adj.create_date,adj.lokasi_adjustment,adj.kode_lokasi,adj.note,adj.status, mta.id, mta.name_type");
		$this->db->from("adjustment adj");
		$this->db->join("mst_type_adjustment mta", "adj.id_type_adjustment = mta.id","left");

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

	function get_datatables()
	{
		$this->_get_datatables_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->select("adj.kode_adjustment,adj.create_date,adj.lokasi_adjustment,adj.kode_lokasi,adj.note,adj.status, mta.id, mta.name_type");
		$this->db->from("adjustment adj");
		$this->db->join("mst_type_adjustment mta", "adj.id_type_adjustment = mta.id","left");
		return $this->db->count_all_results();
	}

	private function _get_datatables2_query()
	{
		$this->db->from($this->table2);

		$i = 0;
	
		foreach ($this->column_search2 as $item) // loop column 
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

				if(count($this->column_search2) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order2[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order2))
		{
			$order = $this->order2;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables2($kode_lokasi)
	{
		$this->_get_datatables2_query();		
		$this->db->where('lokasi', $kode_lokasi);
		//$this->db->where('reserve_move','');
		// $this->db->where_not_in('qty','0');
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2($kode_lokasi)
	{
		//$this->db->where('reserve_move','');
		$this->db->where('lokasi', $kode_lokasi);
		// $this->db->where_not_in('qty','0');
		$this->_get_datatables2_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($kode_lokasi)
	{
		//$this->db->where('reserve_move','');
		$this->db->where('lokasi', $kode_lokasi);
		// $this->db->where_not_in('qty','0');
		$this->db->from($this->table2);
		return $this->db->count_all_results();
	}

	public function get_stock_location_by_departemen($kode_departemen)
	{
		return $this->db->query("SELECT stock_location FROM departemen WHERE kode = '$kode_departemen'");
	}

	public function get_nama_departemen_by_kode($kode)
	{
		return $this->db->query("SELECT nama, stock_location FROM departemen WHERE kode = '$kode'");
	}

	public function cek_adjustment_by_kode($kode_adjustment)
	{
		return $this->db->query("SELECT kode_adjustment FROM adjustment where kode_adjustment = '$kode_adjustment'");
	}

	public function save_header_adjustment($kode_adjustment, $create_date, $lokasi_adjustment, $kode_lokasi, $note, $status, $nama_user, $type_adjustment)
	{
		return $this->db->query("INSERT INTO adjustment (kode_adjustment,create_date,lokasi_adjustment,kode_lokasi,note,status,nama_user,id_type_adjustment) values ('$kode_adjustment','$create_date','$lokasi_adjustment','$kode_lokasi','$note','$status','$nama_user','$type_adjustment')");
	}

	public function get_adjustment_by_code($kode)
	{
		$query = $this->db->query("SELECT * FROM adjustment where kode_adjustment = '".$kode."' ");
		return $query->row();
	}

	public function get_adjustment_detail_by_code($kode_adjustment)
	{
		$query = $this->db->query("SELECT ai.kode_adjustment, ai.quant_id, ai.kode_produk, mp.nama_produk, ai.lot, ai.uom, ai.qty_data , ai.qty_adjustment, ai.uom2, ai.qty_data2 , ai.qty_adjustment2, ai.move_id, ai.row_order, ai.qty_move, ai.qty2_move, sq.lokasi_fisik, sq.lebar_greige, sq.uom_lebar_greige, sq.lebar_jadi, sq.uom_lebar_jadi, sq.sales_order, sq.sales_group
									FROM adjustment_items ai 
									INNER JOIN mst_produk mp ON ai.kode_produk = mp.kode_produk 
									LEFT JOIN stock_quant sq ON ai.quant_id = sq.quant_id WHERE ai.kode_adjustment = '".$kode_adjustment."' ORDER BY ai.row_order");
		return $query->result();
	}

	public function get_produk_by_id($kode_produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom, uom_2 FROM  mst_produk WHERE kode_produk = '$kode_produk' AND type = 'stockable' ");
	}

	public function get_list_produk_adjustment($name)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom, uom_2 FROM  mst_produk 	WHERE CONCAT(kode_produk,nama_produk)  LIKE '%$name%'  and type = 'stockable' AND status_produk = 't' ORDER BY bom,nama_produk LIMIT 50  ")->result_array();
	}

	public function get_total_qty_stock_quant_by_produk_lokasi($kode_produk,$kode_lokasi)
	{
		return $this->db->query("SELECT COALESCE(SUM(qty),0) as 'total' FROM stock_quant WHERE kode_produk = '$kode_produk' and lokasi = '$kode_lokasi'")->row();
	}	

	public function cek_status_adjustment($kode_adjustment, $status)
	{
		if(empty($status)){
			return $this->db->query("SELECT status FROM adjustment WHERE kode_adjustment = '$kode_adjustment'");
		}else{
			return $this->db->query("SELECT status FROM adjustment WHERE kode_adjustment = '$kode_adjustment' AND status = '$status'");
		}
	}

	public function simpan_adjustment_items_batch($sql)
	{
		return $this->db->query("INSERT INTO adjustment_items (kode_adjustment, quant_id, kode_produk, lot, uom, qty_data, qty_adjustment, uom2, qty_data2, qty_adjustment2, mrp_kode, row_order) values $sql ");
	}

	public function cek_quant_adjustment_items($kode_adjustment, $quant_id)
	{
		return $this->db->query("SELECT kode_adjustment FROM adjustment_items WHERE kode_adjustment = '$kode_adjustment' AND quant_id = '$quant_id'");
	}

	public function update_adjustment_items($kode_adjustment,$row_order,$qty_adjustment,$qty_adjustment2)
  	{
    return $this->db->query("UPDATE adjustment_items SET qty_adjustment = '$qty_adjustment', qty_adjustment2 = '$qty_adjustment2' WHERE kode_adjustment = '$kode_adjustment' AND row_order = '$row_order'");
  	}

	public function update_adjustment_items2($kode_adjustment,$kode_produk, $lot, $uom,$qty_adjustment,$uom2,$qty_adjustment2,$row_order)
  	{
    return $this->db->query("UPDATE adjustment_items SET kode_produk = '$kode_produk', lot = '$lot', uom = '$uom', qty_adjustment = '$qty_adjustment',
							 uom2 = '$uom2', qty_adjustment2 = '$qty_adjustment2' 
							WHERE kode_adjustment = '$kode_adjustment' AND row_order = '$row_order'");
  	}


  	public function delete_adjustment_items($kode_adjustment,$row_order)
	{
		return $this->db->query("DELETE FROM adjustment_items WHERE kode_adjustment = '$kode_adjustment' AND row_order = '$row_order'");
	}

	public function get_row_order_adjustment_items($kode_adjustment)
	{
		return $this->db->query("SELECT row_order  FROM adjustment_items WHERE kode_adjustment = '$kode_adjustment' order by row_order desc");
	}

	public function save_adjustment_items($kode_adjustment,$kode_produk,$lot,$uom,$qty_data,$qty_adjustment,$uom2,$qty_data2,$qty_adjustment2,$row_order)
	{
		return $this->db->query("INSERT INTO adjustment_items (kode_adjustment,kode_produk,lot,uom,qty_data,qty_adjustment,uom2,qty_data2,qty_adjustment2,row_order) values ('$kode_adjustment','$kode_produk','$lot','$uom','$qty_data','$qty_adjustment','$uom2','$qty_data2','$qty_adjustment2','$row_order')");	
	}

	public function get_list_adjustment_items_by_code($kode_adjustment)
	{
		return $this->db->query("SELECT * FROM adjustment a INNER JOIN adjustment_items ai ON a.kode_adjustment=ai.kode_adjustment	WHERE a.kode_adjustment = '$kode_adjustment' ORDER BY ai.row_order")->result();
	}

	public function get_adjustment_location_by_kode_departemen($kode_departemen)
	{
		return $this->db->query("SELECT adjustment_location  FROM departemen WHERE kode = '$kode_departemen'");
	}
/*
	public function get_qty_stock_location_by_kode($kode_produk, $kode_lokasi)
	{
		return $this->db->query("SELECT SUM() FROM departemen WHERE nama = '$nama'");
	}
*/
	public function get_list_uom_select2_by_prod($name)
	{
		return $this->db->query("SELECT id, nama, nama, short
								FROM  uom 
								WHERE short LIKE '%$name%' ORDER BY id   ")->result_array();
	}

	public function update_adjustment($kode_adjustment,$note,$type_adjustment)
	{
		return $this->db->query("UPDATE adjustment set note = '$note', id_type_adjustment = '$type_adjustment' WHERE kode_adjustment = '$kode_adjustment'");
	}


	public function get_cek_qty_stock_quant_by_kode($quant_id)
	{
		return $this->db->query("SELECT * FROM stock_quant where quant_id = '$quant_id'");
	}


	public function update_status_adjustment($kode_adjustment, $status)
	{
		return $this->db->query("UPDATE adjustment set status = '$status' where kode_adjustment = '$kode_adjustment' ");
	}

	public function update_nama_user_adjustment($kode_adjustment, $nama_user)
	{
		return $this->db->query("UPDATE adjustment set nama_user = '$nama_user'  where kode_adjustment = '$kode_adjustment' ");
	}

	public function update_create_date_adjustment($kode_adjustment, $tanggal)
	{
		return $this->db->query("UPDATE adjustment set create_date = '$tanggal'  where kode_adjustment = '$kode_adjustment' ");
	}

	public function update_batal_adjustment($kode_adjustment, $status)
	{
		return $this->db->query("UPDATE adjustment set status = '$status' where kode_adjustment = '$kode_adjustment' ");
	}

	public function get_stock_quant_by_quant_id($quant_id)
	{
		$this->db->where('quant_id', $quant_id);
		return $this->db->get("stock_quant");
	}

	public function get_kodeMO_by_quant_id($quant_id, $kode_adjustment)
	{		
		$this->db->SELECT('kode_lokasi');
		$this->db->FROM('adjustment');
		$this->db->WHERE('kode_adjustment',$kode_adjustment);
		$qry = $this->db->get();
		$result = $qry->row_array();

		$this->db->SELECT('mp.kode');
		$this->db->FROM('mrp_production_fg_hasil mpfg');
		$this->db->JOIN('mrp_production mp','mpfg.kode = mp.kode','INNER');
		$this->db->WHERE('mpfg.quant_id',$quant_id);
		$this->db->WHERE_IN('mp.destination_location',$result['kode_lokasi']);
		return $this->db->get();
	}

	public function get_adjustment_items_by_row($kode_adjustment,$row_order)
	{
		return $this->db->query("SELECT adji.quant_id, adji.kode_produk, adji.lot, adji.uom, adji.qty_data, adji.qty_adjustment, adji.uom2, adji.qty_data2, adji.qty_adjustment2, adji.move_id, mp.nama_produk
								FROM adjustment_items  as adji
								INNER JOIN mst_produk as mp ON mp.kode_produk = adji.kode_produk
								WHERE adji.kode_adjustment = '$kode_adjustment' AND adji.row_order = '$row_order' ");
	}

	public function get_list_type_adjustment($where_field = null,$view = null)
	{
		if(isset($view) AND isset($where_field)){
			$this->db->where($where_field,$view);
		}
		$this->db->order_by("id","asc");
		$result = $this->db->get('mst_type_adjustment');
		return $result->result();
		
	}

	public function get_type_adjustment_by_kode($id)
	{
		$this->db->where('id',$id);
		$result = $this->db->get('mst_type_adjustment');
		return $result->row();
	}

	public function cek_quant_id_in_picklist_by_kode($quant_id,$lot)
	{
		$this->db->where('barcode_id',$lot);
		$this->db->where('quant_id',$quant_id);
		$this->db->where_not_in('valid','cancel');
		$result = $this->db->get('picklist_detail');
		return $result->row();
	}

}