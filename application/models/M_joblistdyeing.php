<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_joblistdyeing extends CI_Model
{
	
	//var $table 		  = 'pengiriman_barang';
	var $column_order = array(null, 'mrp.kode', 'mrp.tanggal', 'mrp.origin', 'mrp.nama_produk','','', 'mmss.nama_status');
	var $column_search= array( 'mrp.kode', 'mrp.tanggal', 'mrp.origin', 'mrp.nama_produk', 'mmss.nama_status');
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
            $this->db->like('mrp.kode', $this->input->post('kode'));
        }
        if($this->input->post('status'))
        {
            $this->db->like('mrp.status', $this->input->post('status'));
        }
        if($this->input->post('origin'))
        {
            $this->db->like('mrp.origin', $this->input->post('origin'));
        }
        if($this->input->post('produk'))
        {
            $this->db->like('mrp.nama_produk', $this->input->post('produk'));
        }

		//$this->db->from($this->table);
		$this->db->select(" mrp.kode, mrp.tanggal, mrp.origin, mrp.kode_produk, mrp.nama_produk, mrp.qty, mrp.uom, mrp.qty1_std, mrp.qty2_std, mrp.lot_prefix, mrp.lot_prefix_waste, mrp.status, mmss.nama_status ");
		$this->db->from("mrp_production mrp");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=mrp.status", "inner");

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
		$this->db->where_in('mrp.status',array('ready','draft'));
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($id_dept,$mmss)
	{
		$this->db->where('dept_id',$id_dept);
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->db->where_in('mrp.status',array('ready','draft'));
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_dept,$mmss)
	{	
		//$this->db->from($this->table);
		$this->db->select("mrp.kode, mrp.tanggal, mrp.origin, mrp.kode_produk, mrp.nama_produk, mrp.status, mmss.nama_status");
		$this->db->from("mrp_production mrp");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=mrp.status", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->db->where('dept_id',$id_dept);
		$this->db->where_in('mrp.status',array('ready','draft'));
		return $this->db->count_all_results();
	}



	public function cek_item_rm_target($kode,$category)
	{
		return $this->db->query("SELECT rmt.kode, rmt.kode_produk, rmt.nama_produk, rmt.status, rmt.origin_prod,
								(SELECT count(nama_produk) as jml FROM stock_move_items smi WHERE smi.move_id = rmt.move_id AND smi.origin_prod = rmt.origin_prod LIMIT 1) as jml
					FROM mrp_production_rm_target rmt
					INNER JOIN mst_produk mp ON rmt.kode_produk = mp.kode_produk
					INNER JOIN mst_category mc ON mp.id_category = mc.id
					WHERE rmt.kode = '".$kode."' $category ")->result();

	}


	public function get_link_kain_by_kode($origin,$method)
	{
		return $this->db->query("SELECT sm.move_id, inb.kode, inb.status
									FROM stock_move as  sm
									INNER JOIN penerimaan_barang inb ON sm.move_id = inb.move_id
									where sm.method = '$method' AND sm.origin = '$origin' 
									ORDER BY sm.create_date desc
									LIMIT 1");
	}

	

}