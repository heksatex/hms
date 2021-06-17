<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_orderPlanning extends CI_Model
{
	//var $table 		  = 'sales_contract';
	var $column_order = array(null, 'sales_order', 'create_date', 'buyer_code','nama_status');
	var $column_search= array('sales_order',  'create_date', 'buyer_code','nama_status');
	var $order  	  = array('create_date' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
		$this->load->model('_module');
	}

	private function _get_datatables_query()
	{	


	    $this->db->select("sc.sales_order,sc.create_date,sc.buyer_code,sc.status, mmss.nama_status");
		$this->db->from("sales_contract sc");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=sc.status", "inner");
		
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
		$this->db->where_not_in("sc.status", 'draft');
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($mmss)
	{
		$this->_get_datatables_query();
		$this->db->where("mmss.main_menu_sub_kode",$mmss);
		$this->db->where_not_in("sc.status", 'draft');
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($mmss)
	{
		//$this->db->from($this->table);
		$this->db->select("sc.sales_order,sc.create_date,sc.buyer_code,sc.status, mmss.nama_status");
		$this->db->from("sales_contract sc");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=sc.status", "inner");
		$this->db->where("mmss.main_menu_sub_kode",$mmss);
		$this->db->where_not_in("sc.status", 'draft');
		return $this->db->count_all_results();
	}

	public function get_data_detail($sales_order,$row_order)
	{
		$query = $this->db->query("SELECT * FROM sales_contract_items WHERE sales_order = '$sales_order' AND row_order= '$row_order'");
		return $query->row();
	}

	public function save_due_date($sales_order,$row_order,$due_date)
	{
		return $this->db->query("UPDATE sales_contract_items SET due_date = '$due_date' WHERE sales_order = '$sales_order' AND row_order ='$row_order' ");
	}

	public function cek_due_date_sales_conctract_items_by_kode($sales_order)
	{
		return $this->db->query("SELECT * FROM sales_contract_items WHERE sales_order = '$sales_order' AND due_date is NULL ");
	}
}