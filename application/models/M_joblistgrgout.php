<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_joblistgrgout extends CI_Model
{
	
	//var $table 		  = 'pengiriman_barang';
	var $column_order = array(null, 'pb.kode', 'tanggal', 'pb.origin', 'reff_picking', 'pbi.nama_produk',  'target_mtr', 'mtr', 'kg', 'gl', 'nama_status','nama_warna');
	var $column_search= array( 'pb.kode', 'tanggal', 'pb.origin', 'reff_picking', 'pbi.nama_produk', 'pbi.qty', 'nama_status');
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
            $this->db->like('pb.kode', $this->input->post('kode'));
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
        if($this->input->post('produk'))
        {
            $this->db->like('pbi.nama_produk', $this->input->post('produk'));
        }



		//$this->db->from($this->table);
		$this->db->select("pb.kode, pb.tanggal, pb.origin,  pb.reff_picking, pb.status, pb.reff_note, pb.move_id, mmss.nama_status, sm.method, pbi.nama_produk, pbi.qty as target_mtr, (SELECT IFNULL(sum(smi.qty),0 )  FROM stock_move_items smi WHERE smi.move_id = pb.move_id AND smi.kode_produk = pbi.kode_produk AND smi.origin_prod = pbi.origin_prod) as mtr, (SELECT IFNULL(sum(smi.qty2),0 )  FROM stock_move_items smi WHERE smi.move_id = pb.move_id AND smi.kode_produk = pbi.kode_produk AND smi.origin_prod = pbi.origin_prod) as kg,(SELECT count(nama_produk) FROM stock_move_items smi WHERE smi.move_id = pb.move_id AND smi.kode_produk = pbi.kode_produk AND smi.origin_prod = pbi.origin_prod) as gl, (SELECT w.nama_warna FROM color_order_detail as cod 
		INNER JOIN warna as w ON cod.id_warna = w.id
			WHERE cod.kode_co = SUBSTRING_INDEX(SUBSTRING_INDEX(pb.origin,'|',2),'|',-1) AND cod.row_order  = SUBSTRING_INDEX(SUBSTRING_INDEX(pb.origin,'|',3),'|',-1)) as nama_warna");
		$this->db->from("pengiriman_barang pb");
		$this->db->join("pengiriman_barang_items pbi", "pbi.kode=pb.kode", "left");
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
		$this->db->where_in('dept_id',$id_dept);
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->db->where_in('pb.status',array('ready','draft'));
		if(isset($_POST["length"]) && $_POST["length"] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($id_dept,$mmss)
	{
		$this->db->where_in('dept_id',$id_dept);
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->db->where_in('pb.status',array('ready','draft'));
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_dept,$mmss)
	{	
		//$this->db->from($this->table);
		$this->db->select("pb.kode, pb.tanggal, pb.origin,  pb.reff_picking, pb.status, pb.reff_note, pb.move_id, mmss.nama_status, sm.method, pbi.nama_produk, pbi.qty as target_mtr, (SELECT IFNULL(0,sum(qty)) FROM stock_move_items smi WHERE smi.move_id = pb.move_id AND smi.kode_produk = pbi.kode_produk AND smi.origin_prod = pbi.origin_prod) as mtr, (SELECT IFNULL(0,sum(qty2)) FROM stock_move_items smi WHERE smi.move_id = pb.move_id AND smi.kode_produk = pbi.kode_produk AND smi.origin_prod = pbi.origin_prod) as kg,(SELECT count(nama_produk) FROM stock_move_items smi WHERE smi.move_id = pb.move_id AND smi.kode_produk = pbi.kode_produk AND smi.origin_prod = pbi.origin_prod) as gl, (SELECT w.nama_warna FROM color_order_detail as cod 
		INNER JOIN warna as w ON cod.id_warna = w.id
			WHERE cod.kode_co = SUBSTRING_INDEX(SUBSTRING_INDEX(pb.origin,'|',2),'|',-1) AND cod.row_order  = SUBSTRING_INDEX(SUBSTRING_INDEX(pb.origin,'|',3),'|',-1)) as nama_warna");
		$this->db->from("pengiriman_barang pb");
		$this->db->join("pengiriman_barang_items pbi", "pbi.kode=pb.kode", "left");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pb.status", "inner");
		$this->db->join("stock_move sm", "sm.move_id=pb.move_id", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->db->where_in('dept_id',$id_dept);
		$this->db->where_in('pb.status',array('ready','draft'));
		return $this->db->count_all_results();
	}

}