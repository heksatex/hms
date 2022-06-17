<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_joblistgobout extends CI_Model
{
	
	//var $table 		  = 'pengiriman_barang';
	var $column_order = array(null, 'kode', 'tanggal', 'pb.origin', 'reff_picking', 'nama_status');
	var $column_search= array( 'kode', 'tanggal', 'pb.origin', 'reff_picking', 'nama_status');
	var $order  	  = array('tanggal' => 'asc');


	public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
		$this->load->model('_module');
	}

	private function _get_datatables_query()
	{	
		//add custom filter here
        if($this->input->post('kode'))
        {
            $this->db->like('kode', $this->input->post('kode'));
        }
        if($this->input->post('status'))
        {
            $this->db->like('pb.status', $this->input->post('status'));
        }
        if($this->input->post('origin'))
        {
            $this->db->like('pb.origin', $this->input->post('origin'));
        }
        if($this->input->post('reff_picking'))
        {
            $this->db->like('reff_picking', $this->input->post('reff_picking'));
        }

		//$this->db->from($this->table);
		$this->db->select("pb.kode, pb.tanggal, pb.origin,  pb.reff_picking, pb.status, pb.reff_note, pb.move_id, mmss.nama_status, sm.method");
		$this->db->from("pengiriman_barang pb");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pb.status", "inner");
		$this->db->join("stock_move sm", "sm.move_id=pb.move_id", "inner");

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
		$this->db->select("pb.kode, pb.tanggal, pb.origin, pb.origin, pb.reff_picking, pb.status, pb.reff_note");
		$this->db->from("pengiriman_barang pb");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pb.status", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->db->where('dept_id',$id_dept);
		return $this->db->count_all_results();
	}

}