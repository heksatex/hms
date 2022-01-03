<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */

class M_procurementPurchase extends CI_Model
{
	var $column_order = array(null, 'kode_pp', 'create_date','schedule_date','sales_order', 'priority','nama','notes','nama_status');
	var $column_search= array('kode_pp', 'create_date', 'schedule_date', 'sales_order', 'priority','nama','notes','nama_status');
	var $order  	  = array('create_date' => 'desc');

	var $table2        = 'production_order';
	var $column_order2 = array(null, 'kode_prod', 'create_date', 'sales_order','priority');
	var $column_search2= array('kode_prod', 'create_date', 'sales_order','priority');
	var $order2    	  = array('create_date' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
		$this->load->model('_module');
	}

	private function _get_datatables_query()
	{	

	    $this->db->select("pp.kode_pp,pp.create_date,pp.schedule_date,pp.sales_order,pp.priority,pp.warehouse, pp.notes, pp.status, mmss.nama_status, d.nama as nama_dept");
		$this->db->from("procurement_purchase pp");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pp.status", "inner");
		$this->db->join("departemen d", "d.kode=pp.warehouse", "inner");
		

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

	function get_datatables($mmss)
	{
		$this->_get_datatables_query();
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($mmss)
	{
		$this->_get_datatables_query();
		$this->db->where("mmss.main_menu_sub_kode",$mmss);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($mmss)
	{
		//$this->db->from($this->table);
		$this->db->select("pp.kode_pp,pp.create_date,pp.schedule_date,pp.sales_order,pp.priority,pp.warehouse, pp.notes, pp.status, mmss.nama_status, d.nama as nama_dept");
		$this->db->from("procurement_purchase pp");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pp.status", "inner");
		$this->db->join("departemen d", "d.kode=pp.warehouse", "inner");
		$this->db->where("mmss.main_menu_sub_kode",$mmss);
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

	function get_datatables2()
	{
		$this->_get_datatables2_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2()
	{
		$this->_get_datatables2_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2()
	{
		$this->db->from($this->table2);
		return $this->db->count_all_results();
	}


    public function get_kode_pp()
	{
		$last_no = $this->db->query("SELECT mid(kode_pp,3,(length(kode_pp))-2) as 'nom' 
						 from procurement_purchase where left(kode_pp,2)='PP'
						 order by cast(mid(kode_pp,3,(length(kode_pp))-2) as unsigned) desc LIMIT 1  ");
		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		$kode = 'PP'.$no;
		return $kode;
	}


    public function get_kode_cfb()
	{
		$last_no = $this->db->query("SELECT mid(kode_cfb,3,(length(kode_cfb))-2) as 'nom' 
						 from cfb where left(kode_cfb,2)='TE'
						 order by cast(mid(kode_cfb,3,(length(kode_cfb))-2) as unsigned) desc LIMIT 1  ");
		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		$kode = 'TE'.$no;
		return $kode;
	}

	public function simpan($kode, $tgl, $schedule_date, $note, $sales_order, $kode_prod, $priority, $warehouse, $status)
	{
		return $this->db->query("INSERT INTO procurement_purchase (kode_pp,create_date,schedule_date,sales_order,kode_prod,priority,warehouse,notes,status) values ('$kode','$tgl','$schedule_date','$sales_order','$kode_prod','$priority','$warehouse','$note','$status')");
	}

	public function ubah($kode_pp, $tgl, $note, $priority, $warehouse)
	{
		return $this->db->query("UPDATE procurement_purchase SET schedule_date = '$tgl', notes = '$note', 
															 priority = '$priority', warehouse ='$warehouse'
														 WHERE kode_pp =  '$kode_pp' ");
	}

	public function get_data_by_code($kode_pp)
	{
		$query = $this->db->query("SELECT * FROM procurement_purchase where kode_pp = '".$kode_pp."' ");
		return $query->row();
	}

	public function get_data_detail_by_code($kode_pp)
	{
		$query = $this->db->query("SELECT * FROM procurement_purchase_items where kode_pp = '".$kode_pp."' ORDER BY row_order");
		return $query->result();
	}

	public function get_list_produk_procurement_purchase($name)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom
								FROM  mst_produk 
								WHERE CONCAT(kode_produk,nama_produk)  LIKE '%$name%' and type = 'stockable' AND status_produk = 't' ORDER BY bom,nama_produk LIMIT 50")->result_array();

	}

	public function get_produk_procurement_purchase_byid($kode_produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE kode_produk = '$kode_produk' AND type = 'stockable' ");
	}

	public function get_row_order_procurement_purchase_items($kode_pp)
	{
		return $this->db->query("SELECT row_order  FROM procurement_purchase_items WHERE kode_pp = '$kode_pp' order by row_order desc");
	}

	public function save_procurement_purchase_items($kode_pp,$kode_produk,$produk,$tgl,$qty,$uom,$reff,$status,$row_order)
	{
		return $this->db->query("INSERT INTO procurement_purchase_items (kode_pp,kode_produk,nama_produk,schedule_date,qty,uom,reff_notes,status,row_order) values ('$kode_pp','$kode_produk','$produk','$tgl','$qty','$uom','$reff','$status','$row_order')");	
	}

	public function cek_status_procurement_purchase_items($kode_pp,$status)
	{
		return $this->db->query("SELECT * FROM procurement_purchase_items WHERE kode_pp = '$kode_pp' $status ");
		/*
		if(!empty($status)){
		}else{
			return $this->db->query("SELECT * FROM procurement_purchase_items WHERE kode_pp = '$kode_pp' ");
		}
		*/
	}

	public function update_status_procurement_purchase($kode_pp,$status)
	{
		return $this->db->query("UPDATE procurement_purchase set status = '$status' WHERE kode_pp = '$kode_pp'");
	}

	public function update_status_procurement_purchase_items($kode_pp,$status)
	{
		return $this->db->query("UPDATE procurement_purchase_items set status = '$status' WHERE kode_pp = '$kode_pp'");
	}

	public function delete_procurement_purchase_items($kode_pp,$row_order)
	{
		return $this->db->query("DELETE FROM procurement_purchase_items WHERE kode_pp = '$kode_pp' AND row_order = '$row_order'");
	}

	public function update_procurement_purchase_items($kode_pp,$tgl,$qty,$reff,$row_order)
	{
		return $this->db->query("UPDATE procurement_purchase_items SET schedule_date = '$tgl', qty = '$qty', reff_notes = '$reff' 
																WHERE kode_pp = '$kode_pp' AND row_order = '$row_order'");
	}

	public function save_cfb_items_batch($sql)
	{
		return $this->db->query("INSERT INTO cfb_items (kode_cfb,kode_produk,nama_produk,schedule_date,qty,uom,status,reff_notes,row_order) values $sql " );

	}

	public function save_cfb_batch($sql)
	{
		return $this->db->query(" $sql ");
	}

	public function cek_produk_by_kode($kode,$kode_produk)
	{
		return $this->db->query("SELECT kode_produk FROM procurement_purchase_items where kode_pp = '$kode' and kode_produk = '$kode_produk' ");
	}

	public function cek_status_procurement_purchase_items_by_row($kode_pp,$kode_produk,$row_order)
	{
		return $this->db->query("SELECT kode_pp, kode_produk, status FROM procurement_purchase_items where kode_pp = '$kode_pp' AND row_order = '$row_order' ");
	}


	public function cek_warehouse_procurement_purchase_order_by_kode($kode_pp)
	{
		return $this->db->query("SELECT warehouse FROM procurement_purchase WHERE kode_pp ='$kode_pp'");
	}

	public function get_cfb_by_kode($kode_pp,$kode_prod,$sales_order)
	{
		return $this->db->query("SELECT * FROM cfb WHERE sales_order = '$sales_order' AND kode_prod = '$kode_prod' AND kode_pp ='$kode_pp' ");
	}

	public function get_list_cfb_by_kode($kode_pp,$kode_prod,$sales_order)
	{
		return $this->db->query("SELECT a.kode_cfb, a.create_date, a.schedule_date, a.sales_order, a.kode_prod, a.kode_pp, a.priority, a.warehouse, a.notes, a.status, d.nama as nama_dept
		   						FROM cfb a
		   						INNER JOIN departemen  d ON a.warehouse = d.kode 
		   						WHERE a.sales_order = '$sales_order' AND a.kode_prod = '$kode_prod' AND a.kode_pp ='$kode_pp'")->result();
	}

}