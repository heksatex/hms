<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_productionOrder extends CI_Model
{
	var $column_order = array(null, 'kode_prod', 'create_date','schedule_date','sales_order', 'priority','nama','notes','nama_status');
	var $column_search= array('kode_prod', 'create_date', 'schedule_date', 'sales_order', 'priority','nama','notes','nama_status');
	var $order  	  = array('create_date' => 'desc');

	var $table2        = 'sales_contract';
	var $column_order2 = array(null, 'sales_order', 'create_date', 'buyer_code','status');
	var $column_search2= array('sales_order',  'create_date', 'buyer_code','status');
	var $order2    	  = array('create_date' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
		$this->load->model('_module');
	}

	private function _get_datatables_query()
	{	


	    $this->db->select("prd.kode_prod,prd.create_date,prd.schedule_date,prd.sales_order,prd.priority,prd.warehouse, prd.notes, prd.status, mmss.nama_status, d.nama as nama_dept");
		$this->db->from("production_order prd");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=prd.status", "inner");
		$this->db->join("departemen d", "d.kode=prd.warehouse", "inner");
		
		//$this->db->from($this->table);

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
		$this->db->select("prd.kode_prod,prd.create_date,prd.schedule_date,prd.sales_order,prd.priority,prd.warehouse, prd.notes, prd.status, mmss.nama_status,d.nama as nama_dept");
		$this->db->from("production_order prd");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=prd.status", "inner");
		$this->db->join("departemen d", "d.kode=prd.warehouse", "inner");
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
		$this->db->where("status", 'waiting_color');
		$this->db->where("order_production", 'true');
		$this->db->where('sales_order NOT IN (SELECT sales_order FROM production_order)', NULL, FALSE);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2()
	{
		$this->_get_datatables2_query();
		$this->db->where("status", 'waiting_color');
		$this->db->where("order_production", 'true');
		$this->db->where('sales_order NOT IN (SELECT sales_order FROM production_order)', NULL, FALSE);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2()
	{
		$this->db->from($this->table2);
		$this->db->where("status", 'waiting_color');
		$this->db->where("order_production", 'true');
		$this->db->where('sales_order NOT IN (SELECT sales_order FROM production_order)', NULL, FALSE);
		return $this->db->count_all_results();
	}

	public function kode_prod()
	{
		$last_no = $this->db->query("SELECT mid(kode_prod,3,(length(kode_prod))-2) as 'nom' 
						 from production_order where left(kode_prod,2)='PD'
						 order by cast(mid(kode_prod,3,(length(kode_prod))-2) as unsigned) desc LIMIT 1  ");
		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		$kode = 'PD'.$no;
		return $kode;
	}

	public function simpan($kode, $tgl, $schedule_date, $note, $sales_order, $priority, $warehouse, $status)
	{
		return $this->db->query("INSERT INTO production_order (kode_prod,create_date,schedule_date,sales_order,priority,warehouse,notes,status) values ('$kode','$tgl','$schedule_date','$sales_order','$priority','$warehouse','$note','$status')");
	}

	public function ubah($kode_prod, $tgl, $note, $sales_order, $priority, $warehouse)
	{
		return $this->db->query("UPDATE production_order SET schedule_date = '$tgl', notes = '$note', sales_order = '$sales_order', 
															 priority = '$priority', warehouse ='$warehouse'
														 WHERE kode_prod =  '$kode_prod' ");
	}

	public function get_data_by_code($kode_prod)
	{
		$query = $this->db->query("SELECT * FROM production_order where kode_prod = '".$kode_prod."' ");
		return $query->row();
	}

	public function get_data_detail_by_code($kode_prod)
	{
		$query = $this->db->query("SELECT pd.kode_prod,pd.kode_produk, pd.nama_produk, pd.kode_bom, 
									pd.schedule_date, pd.qty, pd.uom, pd.reff_notes, pd.`status`, pd.row_order, 
       								b.kode_bom as kodebom, b.nama_bom       

								FROM production_order_items pd
								LEFT JOIN bom b ON pd.kode_bom = b.kode_bom
								where pd.kode_prod = '".$kode_prod."' ORDER BY pd.row_order");
		return $query->result();
	}

	public function get_list_produk_by_so($name,$sales_order)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  sales_contract_items 
								WHERE nama_produk LIKE '%$name%' AND sales_order = '$sales_order' LIMIT 10")->result_array();
	}

	public function get_produk_byid_so($kode_produk,$sales_order)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom,qty 
								FROM  sales_contract_items 
								WHERE kode_produk = '$kode_produk' AND sales_order = '$sales_order'");
	}

	public function get_bom_by_nama_produk($nama_produk){
		return $this->db->query("SELECT kode_bom, nama_bom FROM bom WHERE nama_produk = '$nama_produk' ");
	}

	public function get_list_bom_by_nama_produk($nama_bom,$nama_produk)
	{
		return $this->db->query("SELECT kode_bom, nama_bom 
								FROM  bom 
								WHERE nama_bom LIKE '%$nama_bom%' AND nama_produk = '$nama_produk' LIMIT 10")->result_array();
	}

	public function get_nama_bom_by_kode_bom($kode_bom)
	{
		return $this->db->query("SELECT kode_bom, nama_bom FROM bom WHERE kode_bom  = '$kode_bom' ");
	}

	public function get_row_order_production_order_items($kode_prod)
	{
		return $this->db->query("SELECT row_order  FROM production_order_items WHERE kode_prod = '$kode_prod' order by row_order desc");
	}

	public function save_production_order_items($kode_prod,$kode_produk,$produk,$kode_bom,$tgl,$qty,$uom,$reff,$status,$row_order)
	{
		return $this->db->query("INSERT INTO production_order_items (kode_prod,kode_produk,nama_produk,kode_bom,schedule_date,qty,uom,reff_notes,status,row_order) values ('$kode_prod','$kode_produk','$produk','$kode_bom','$tgl','$qty','$uom','$reff','$status','$row_order')");	
	}

	public function update_production_order_items($kode_prod,$tgl,$kode_produk,$kode_bom,$qty,$reff,$row_order)
	{
		return $this->db->query("UPDATE production_order_items SET schedule_date = '$tgl', qty = '$qty', reff_notes = '$reff',
																	kode_bom = '$kode_bom' 
																WHERE kode_prod = '$kode_prod' AND kode_produk = '$kode_produk' AND row_order = '$row_order'");
	}

	public function delete_production_order_items($kode_prod,$row_order)
	{
		return $this->db->query("DELETE FROM production_order_items WHERE kode_prod = '$kode_prod' AND row_order = '$row_order'");
	}

	public function update_status_production_order_items($kode_prod,$row,$status)
	{
		return $this->db->query("UPDATE production_order_items SET status = '$status' WHERE kode_prod = '$kode_prod' AND row_order = '$row'");
	}

	public function cek_status_production_order_items($kode_prod,$status)
	{
		return $this->db->query("SELECT * FROM production_order_items WHERE kode_prod = '$kode_prod' $status ");
		
		/*
		if(!empty($status)){

		}else{
			return $this->db->query("SELECT * FROM production_order_items WHERE kode_prod = '$kode_prod'");
		}
		*/
	}

	public function update_status_production_order($kode_prod,$status)
	{
		return $this->db->query("UPDATE production_order set status = '$status' WHERE kode_prod = '$kode_prod'");
	}

	public function cek_status_production_order_items_by_row($kode_prod,$kode_produk,$row_order)
	{
		return $this->db->query("SELECT kode_prod, kode_produk, status FROM production_order_items where kode_prod = '$kode_prod' AND kode_produk = '$kode_produk' AND row_order = '$row_order' ");
	}

	public function cek_warehouse_production_order_by_kode($kode)
	{
		return $this->db->query("SELECT warehouse FROM production_order WHERE kode_prod ='$kode'");
	}

	public function cek_production_order_by_sales_order($sales_order)
	{
		return $this->db->query("SELECT * FROM production_order WHERE sales_order = '$sales_order'");
	}

	public function cek_nama_produk_by_kode($kode_produk){
		return $this->db->query("SELECT nama_produk FROM mst_produk WHERE kode_produk = '$kode_produk' ");
	}


}