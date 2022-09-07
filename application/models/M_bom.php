<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_bom extends CI_Model
{

	var $table 		  = 'bom';
	var $column_order = array(null, 'kode_bom','nama_bom','kode_produk','nama_produk',  'qty', 'uom');
	var $column_search= array(  'kode_bom','nama_bom','kode_produk','nama_produk',  'qty', 'uom');
	var $order  	  = array('nama_bom' => 'asc');

	private function _get_datatables_query()
	{		

		$this->db->from($this->table);
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
		
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}


	public function get_list_produk_select2_by_prod($name)
	{
		/*
		return $this->db->query("SELECT kode_produk, nama_produk, uom
								FROM  mst_produk 
								WHERE nama_produk LIKE '%$name%' ORDER BY nama_produk LIMIT 50  ")->result_array();
		*/
		return $this->db->query("SELECT kode_produk, nama_produk, uom
									FROM  mst_produk 
									WHERE CONCAT(kode_produk,nama_produk)  LIKE '%$name%' AND status_produk = 't' ORDER BY bom,nama_produk LIMIT 500  ")->result_array();
	}

	public function get_list_uom_select2_by_prod($name)
	{
		return $this->db->query("SELECT id, nama, nama, short
								FROM  uom 
								WHERE short LIKE '%$name%' ORDER BY id   ")->result_array();
	}

	public function cek_bom_by_kode_produk($kode_produk,$nama_bom)
	{
		return $this->db->query("SELECT * FROM bom WHERE kode_produk = '$kode_produk' AND nama_bom = '$nama_bom'");
	}


	public function get_produk_by_kode($kode_produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE kode_produk = '$kode_produk' ");
	}

	public function save_bom($kode_bom,$tanggal,$nama_bom,$kode_produk,$nama_produk,$qty,$uom)
	{
		$this->db->query("INSERT INTO bom (kode_bom,tanggal,nama_bom,kode_produk,nama_produk,qty,uom) VALUES ('$kode_bom','$tanggal','$nama_bom','$kode_produk','$nama_produk','$qty','$uom')");
	}

	public function update_bom($kode_bom,$nama_bom,$kode_produk,$nama_produk,$qty,$uom)
	{
		$this->db->query("UPDATE bom SET nama_bom = '$nama_bom', kode_produk = '$kode_produk', 
										 nama_produk = '$nama_produk', qty = '$qty', uom = '$uom' 
									WHERE kode_bom = '$kode_bom'");
	}

	public function get_list_bom($kode_bom)
	{
		return $this->db->query("SELECT * FROM bom where kode_bom = '$kode_bom'")->row();
	}

	public function get_list_bom_items($kode_bom)
	{
		return $this->db->query("SELECT * FROM bom_items where kode_bom = '$kode_bom'")->result();
	}

	public function get_last_row_order_bom_items_by_kode($kode_bom)
	{
		$last_no = $this->db->query("SELECT row_order FROM bom_items where kode_bom = '$kode_bom' ORDER BY row_order desc LIMIT 1  ");
		$result = $last_no->row();
		if(empty($result->row_order)){
			$no   = 1;
		}else{
     		$no   = (int)$result->row_order + 1;
		}
		return $no;
	}

	public function save_bom_items($kode_bom,$kode_produk,$nama_produk,$qty,$uom,$note,$row_order)
	{
		$this->db->query("INSERT INTO bom_items (kode_bom,kode_produk,nama_produk,qty,uom,note,row_order) VALUES ('$kode_bom','$kode_produk','$nama_produk','$qty','$uom','$note','$row_order' )");
	}

	public function cek_bom_items_by_row($kode_bom,$kode_produk,$row_order)
	{
		return $this->db->query("SELECT * FROM bom_items where kode_bom = '$kode_bom' AND kode_produk = '$kode_produk' AND row_order = '$row_order'");
	}

	public function update_bom_items($kode_bom,$kode_produk,$nama_produk,$qty,$uom,$note,$row_order)
	{
		$this->db->query("UPDATE bom_items SET kode_produk = '$kode_produk', nama_produk = '$nama_produk', qty = '$qty', uom = '$uom', note = '$note'
											WHere kode_bom = '$kode_bom' AND row_order = '$row_order' ");
	}

	public function delete_bom_items($kode_bom,$kode_produk,$row_order)
	{
		$this->db->query("DELETE FROM bom_items where kode_bom = '$kode_bom' AND kode_produk = '$kode_produk' AND row_order = '$row_order'");
	}

}