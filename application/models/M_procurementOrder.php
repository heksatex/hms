<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_procurementOrder extends CI_Model
{
	var $column_order = array(null, 'kode_proc', 'create_date','schedule_date','ms.nama_status','show_sales_order','sales_order', 'priority','nama','notes','mmss.nama_status');
	var $column_search= array('kode_proc', 'create_date', 'ms.nama_status','show_sales_order','schedule_date', 'sales_order', 'priority','nama','notes','mmss.nama_status');
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


	    $this->db->select("prc.kode_proc,prc.create_date,prc.schedule_date,prc.sales_order,prc.priority,prc.warehouse, prc.notes, prc.status, mmss.nama_status, d.nama as nama_dept, prc.show_sales_order, ms.nama_status as type");
		$this->db->from("procurement_order prc");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=prc.status", "inner");
		$this->db->join("mst_status ms", "ms.kode=prc.type", "inner");
		$this->db->join("departemen d", "d.kode=prc.warehouse", "inner");
		

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
		$this->db->select("prc.kode_prod,prc.create_date,prc.schedule_date,prc.sales_order,prc.priority,prc.warehouse, prc.notes, prc.status, d.nama as nama_dept, mmss.nama_status");
		$this->db->from("procurement_order prc");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=prc.status", "inner");
		$this->db->join("departemen d", "d.kode=prc.warehouse", "inner");
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


	public function get_kode_proc()
	{
		$last_no = $this->db->query("SELECT mid(kode_proc,3,(length(kode_proc))-2) as 'nom' 
						 from procurement_order where left(kode_proc,2)='PC'
						 order by cast(mid(kode_proc,3,(length(kode_proc))-2) as unsigned) desc LIMIT 1  ");
		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		$kode = 'PC'.$no;
		return $kode;
	}

	public function simpan($kode, $tgl, $schedule_date, $note, $sales_order, $kode_prod, $priority, $warehouse, $status,$type,$show_sc)
	{
		return $this->db->query("INSERT INTO procurement_order (kode_proc,create_date,schedule_date,sales_order,kode_prod,priority,warehouse,notes,status,type,show_sales_order) values ('$kode','$tgl','$schedule_date','$sales_order','$kode_prod','$priority','$warehouse','$note','$status','$type','$show_sc')");
	}

	public function ubah($kode_proc, $tgl, $note, $priority, $warehouse)
	{
		return $this->db->query("UPDATE procurement_order SET schedule_date = '$tgl', notes = '$note', 
															 priority = '$priority', warehouse ='$warehouse'
														 WHERE kode_proc =  '$kode_proc' ");
	}

	public function get_data_by_code($kode_proc)
	{
		$query = $this->db->query("SELECT * FROM procurement_order where kode_proc = '".$kode_proc."' ");
		return $query->row();
	}

	public function get_data_detail_by_code($kode_proc)
	{
		$query = $this->db->query("SELECT * FROM procurement_order_items where kode_proc = '".$kode_proc."' ORDER BY row_order");
		return $query->result();
	}

	public function get_list_produk_procurement_order($name)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom
								FROM  mst_produk 
								WHERE CONCAT(kode_produk,nama_produk)  LIKE '%$name%'  and type = 'stockable' AND status_produk = 't' ORDER BY bom,nama_produk LIMIT 50  ")->result_array();
	}

	public function get_produk_procurement_order_byid($kode_produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE kode_produk = '$kode_produk' AND type = 'stockable' ");
	}

	public function update_procurement_order_items($kode_proc,$tgl,$qty,$reff,$row_order)
	{
		return $this->db->query("UPDATE procurement_order_items SET schedule_date = '$tgl', qty = '$qty', reff_notes = '$reff' 
																WHERE kode_proc = '$kode_proc' AND row_order = '$row_order'");
	}

	public function get_row_order_procurement_order_items($kode_proc)
	{
		return $this->db->query("SELECT row_order  FROM procurement_order_items WHERE kode_proc = '$kode_proc' order by row_order desc");
	}

	public function save_procurement_order_items($kode_proc,$kode_produk,$produk,$tgl,$qty,$uom,$reff,$status,$row_order)
	{
		return $this->db->query("INSERT INTO procurement_order_items (kode_proc,kode_produk,nama_produk,schedule_date,qty,uom,reff_notes,status,row_order) values ('$kode_proc','$kode_produk','$produk','$tgl','$qty','$uom','$reff','$status','$row_order')");	
	}

	public function cek_status_procurement_order_items($kode_proc,$status)
	{
		return $this->db->query("SELECT * FROM procurement_order_items WHERE kode_proc = '$kode_proc' $status");
		/*
		if(!empty($status)){
		}else{
			return $this->db->query("SELECT * FROM procurement_order_items WHERE kode_proc = '$kode_proc' ");
		}
		*/
	}

    public function update_status_procurement_order($kode_proc,$status)
	{
		return $this->db->query("UPDATE procurement_order set status = '$status' WHERE kode_proc = '$kode_proc'");
	}

	public function delete_procurement_order_items($kode_proc,$row_order)
	{
		return $this->db->query("DELETE FROM procurement_order_items WHERE kode_proc = '$kode_proc' AND row_order = '$row_order'");
	}

	public function update_status_procurement_order_items($kode_proc,$row,$status)
	{
		return $this->db->query("UPDATE procurement_order_items SET status = '$status' WHERE kode_proc = '$kode_proc' AND row_order = '$row'");
	}

	public function cek_status_procurement_order_items_by_row($kode_proc,$kode_produk,$row_order)
	{
		return $this->db->query("SELECT kode_proc, kode_produk, status FROM procurement_order_items where kode_proc = '$kode_proc' AND kode_produk = '$kode_produk' AND row_order = '$row_order' ");
	}

	public function cek_warehouse_procurement_order_by_kode($kode_proc)
	{
		return $this->db->query("SELECT warehouse FROM procurement_order WHERE kode_proc ='$kode_proc'");
	}

	public function get_data_items_by_row($kode,$row_order)
	{
		return $this->db->query("SELECT kode_proc,kode_produk,nama_produk,schedule_date, qty, uom,reff_notes, status,row_order FROM procurement_order_items where kode_proc = '$kode' AND row_order = '$row_order' ");
	}

	public function cek_type_procurement_order_by_kode($kode)
	{
		$result =  $this->db->query("SELECT type FROM procurement_order WHERE kode_proc = '$kode'")->row();
		return $result->type;
	}

	public function cek_show_sales_order_by_kode($kode)
	{
		$result =  $this->db->query("SELECT show_sales_order FROM procurement_order WHERE kode_proc = '$kode'")->row();
		return $result->show_sales_order;
	}

	public function cek_mrp_production_rm_target($kode,$status)
	{
		$this->db->where('kode',$kode);
		$this->db->where('status',$status);
		$query = $this->db->get('mrp_production_rm_target');
		return $query;
		
	}

}