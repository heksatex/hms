<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_produk extends CI_Model
{
	var $column_order = array(null, 'kode_produk', 'nama_produk', 'create_date', 'uom','uom_2','nama_category', 'route_produksi', 'type','nama_status');
	var $column_search= array('kode_produk', 'nama_produk',  'create_date', 'uom','uom_2','nama_category', 'route_produksi', 'type','nama_status');
	var $order  	  = array('nama_produk' => 'asc');

	var $table2 		= 'bom';
	var $column_order2 	= array(null, 'kode_bom','nama_bom','kode_produk','nama_produk',  'qty', 'uom');
	var $column_search2	= array('kode_bom','nama_bom','kode_produk','nama_produk',  'qty', 'uom');
	var $order2	 	  	= array('nama_bom' => 'asc');

	var $column_order3  = array(null, 'mp.kode', 'tanggal','nama', 'nama_produk', 'qty', 'uom', 'reff_note', 'nama_status');
	var $column_search3= array( 'mp.kode', 'tanggal','nama', 'nama_produk', 'qty','uom', 'reff_note', 'nama_status');
	var $order3	       = array('mp.kode' => 'desc');

	private function _get_datatables_query()
	{
		$this->db->select("p.kode_produk,p.nama_produk,p.create_date,p.uom,p.uom_2,c.nama_category,p.route_produksi,p.type, nama_status");
		$this->db->from("mst_produk p");		
		$this->db->JOIN("mst_category c","p.id_category=c.id","LEFT");
		$this->db->JOIN("mst_status s","p.status_produk=s.kode","LEFT");

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
		$this->db->select("p.kode_produk,p.nama_produk,p.create_date,p.uom,p.uom_2,c.nama_category,p.route_produksi,p.type");
		$this->db->from("mst_produk p");		
		$this->db->JOIN("mst_category c","p.id_category=c.id","LEFT");	
		$this->db->JOIN("mst_status s","p.status_produk=s.kode","LEFT");
		return $this->db->count_all_results();
	}

	public function get_list_coa($coa)
	{
		return $this->db->query("SELECT kode_coa, CONCAT(kode_coa,' | ',nama) as nama_coa
								FROM  coa
								WHERE kode_coa LIKE '%$coa%' OR nama LIKE '%$coa%'
								ORDER BY kode_coa LIMIT 10")->result_array();
	}

	public function get_produk_by_kode($kodeproduk)
	{
		return $this->db->query("SELECT * FROM mst_produk where kode_produk = '$kodeproduk' ")->row();
	}

	public function get_list_uom()
	{
		return $this->db->query("SELECT short FROM uom ORDER BY short")->result();
	}

	public function get_list_category()
	{
		return $this->db->query("SELECT id, nama_category FROM mst_category ORDER BY nama_category")->result();
	}

	public function get_list_route()
	{
		return $this->db->query("SELECT nama_route FROM mst_route ORDER BY nama_route")->result();
	}

	public function cek_produk_by_nama($kodeproduk,$namaproduk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk FROM mst_produk where kode_produk != '$kodeproduk' AND nama_produk = '$namaproduk'");
	}

	public function cek_produk_by_kode($kodeproduk)
	{
		return $this->db->query("SELECT kode_produk,nama_produk FROM mst_produk where kode_produk = '$kodeproduk'");
	}

	public function update_produk($kode_produk,$nama_produk,$uom,$uom_2,$route_produksi,$type,$dapat_dibeli,$dapat_dijual,$id_category,$note,$bom,$lebargreige,$lebarjadi,$statusproduk)
	{
		return $this->db->query("UPDATE mst_produk set nama_produk = '$nama_produk', uom = '$uom',uom_2 = '$uom_2', route_produksi = '$route_produksi', type = '$type', dapat_dibeli = '$dapat_dibeli', dapat_dijual = '$dapat_dijual', id_category = '$id_category', note = '$note', bom = '$bom', lebar_greige = '$lebargreige' ,lebar_jadi = '$lebarjadi', status_produk = '$statusproduk'  WHERE kode_produk = '$kode_produk' ");
	}

	public function get_nama_category_by_id($id)
	{
		return $this->db->query("SELECT id,nama_category FROM mst_category WHERE id = '$id'");
	}

	public function save_produk($kode_produk,$nama_produk,$uom,$uom_2,$create_date,$route_produksi,$type,$dapat_dibeli,$dapat_dijual,$id_category,$note,$bom, $lebargreige,$lebarjadi,$statusproduk)
	{
		return $this->db->query("INSERT INTO mst_produk(kode_produk,nama_produk,uom,uom_2,create_date,route_produksi,type,dapat_dibeli,dapat_dijual,id_category,note,bom,lebar_greige,lebar_jadi,status_produk) VALUES ('$kode_produk','$nama_produk','$uom','$uom_2','$create_date','$route_produksi','$type','$dapat_dibeli','$dapat_dijual','$id_category','$note', '$bom','$lebargreige','$lebarjadi','$statusproduk')");
	}

	public function get_qty_onhand($kodeproduk)
	{
		return $this->db->query("SELECT SUM(qty) AS 'qty_onhand' FROM stock_quant WHERE kode_produk = '$kodeproduk' and lokasi NOT LIKE '%Virtual%'")->row();
	}

	public function get_jml_moves($kodeproduk)
	{
		return $this->db->query("SELECT COUNT(DISTINCT(sm.move_id)) as 'jml_moves' FROM stock_move sm INNER JOIN stock_move_produk smp ON sm.move_id=smp.move_id WHERE smp.kode_produk = '$kodeproduk' AND smp.status<>'cancel'")->row();
	}

	public function get_jml_bom($kodeproduk)
	{
		return $this->db->query("SELECT COUNT(kode_produk) as 'jml_bom' FROM bom WHERE kode_produk = '$kodeproduk'")->row();
	}

	public function get_jml_mo($kodeproduk)
	{
		return $this->db->query("SELECT COUNT(kode) as 'jml_mo' FROM mrp_production WHERE kode_produk = '$kodeproduk' ")->row();
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

	function get_datatables2($kode_produk)
	{
		$this->_get_datatables2_query();	
		$this->db->where('kode_produk',$kode_produk);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2($kode_produk)
	{
		$this->_get_datatables2_query();
		$this->db->where('kode_produk',$kode_produk);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($kode_produk)
	{
		$this->db->where('kode_produk',$kode_produk);
		$this->db->from($this->table2);
		return $this->db->count_all_results();
	}


	private function _get_datatables3_query()
	{
		$this->db->select("mp.kode, mp.tanggal, mp.nama_produk, mp.qty, mp.uom, mp.status,  mp.reff_note, mp.dept_id, d.nama as departemen, s.nama_status");
		$this->db->from("mrp_production mp");
		$this->db->join("departemen d", "d.kode=mp.dept_id", "inner");
		$this->db->JOIN("mst_status s","mp.status=s.kode","LEFT");

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

	function get_datatables3($kode_produk)
	{
		$this->_get_datatables3_query();	
		$this->db->where('kode_produk',$kode_produk);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered3($kode_produk)
	{
		$this->db->where('mp.kode_produk',$kode_produk);
		$this->_get_datatables3_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all3($kode_produk)
	{
		//$this->db->from($this->table);
		$this->db->select("mp.kode, mp.tanggal, mp.nama_produk, mp.qty, mp.uom, mp.status");
		$this->db->from("mrp_production mp");
		$this->db->join("departemen d", "d.kode=mp.dept_id", "inner");
		$this->db->JOIN("mst_status s","mp.status=s.kode","LEFT");
		$this->db->where("mp.kode_produk", $kode_produk);
		return $this->db->count_all_results();
	}

}