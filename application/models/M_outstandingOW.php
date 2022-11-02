<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_outstandingOW extends CI_Model
{

    var $column_order = array(null, 'scl.sales_order', 'msg.nama_sales_group', 'scl.ow',  'scl.tanggal_ow', 'scl.status','scl.nama_produk','w.nama_warna',  'scl.qty');
	var $column_search= array('scl.sales_order', 'msg.nama_sales_group', 'scl.ow',  'scl.tanggal_ow', 'scl.status','scl.nama_produk','w.nama_warna',  'scl.qty');
	var $order  	  = array('scl.tanggal_ow' => 'asc');

    
	private function _get_datatables_query()
	{	

        //add custom filter here
        if($this->input->post('sc'))
        {
            $this->db->like('scl.sales_order', $this->input->post('sc'));
        }

        if($this->input->post('sales_group'))
        {
            $this->db->like('msg.kode_sales_group', $this->input->post('sales_group'));
        }

        if($this->input->post('ow'))
        {
            $this->db->like('scl.ow', $this->input->post('ow'));
        }

        if($this->input->post('produk'))
        {
            $this->db->like('scl.nama_produk', $this->input->post('produk'));
        }

        if($this->input->post('warna'))
        {
            $this->db->like('w.nama_warna', $this->input->post('warna'));
        }

        $this->db->select(" scl.sales_order, scl.ow, msg.nama_sales_group, scl.tanggal_ow, scl.nama_produk, scl.qty, scl.uom, w.nama_warna,scl.id_warna, scl.piece_info, scl.lebar_jadi, scl.uom_lebar_jadi, scl.reff_notes, scl.gramasi, scl.status as status_scl ");
        $this->db->from("sales_color_line scl");
        $this->db->join("sales_contract sc", "sc.sales_order = scl.sales_order", "inner");
        $this->db->join("mst_sales_group msg", "sc.sales_group = msg.kode_sales_group", "inner");
        $this->db->join("warna w", "scl.id_warna = w.id", "inner");

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
        $this->db->where("scl.ow not in (SELECT SUBSTRING_INDEX(origin,'|',-1) as ow FROM pengiriman_barang where  dept_id= 'GRG' and status IN ('done') )");
        $status_scl = array('t','ng');
		$this->db->where_in("scl.status", $status_scl);
        $this->db->where("scl.ow <> ''");
        $this->db->group_by("scl.ow");
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
        $this->db->where("scl.ow not in (SELECT SUBSTRING_INDEX(origin,'|',-1) as ow FROM pengiriman_barang where  dept_id= 'GRG' and status IN ('done') )");
        $status_scl = array('t','ng');
		$this->db->where_in("scl.status", $status_scl);
        $this->db->where("scl.ow <> ''");
        $this->db->group_by("scl.ow");
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{	
        $this->db->select(" scl.sales_order, scl.ow, msg.nama_sales_group, scl.tanggal_ow, scl.nama_produk, scl.qty, scl.uom, w.nama_warna,scl.id_warna, scl.piece_info, scl.lebar_jadi, scl.uom_lebar_jadi, scl.reff_notes, scl.gramasi, scl.status as status_scl ");
        $this->db->from("sales_color_line scl");
        $this->db->join("sales_contract sc", "sc.sales_order = scl.sales_order", "inner");
        $this->db->join("mst_sales_group msg", "sc.sales_group = msg.kode_sales_group", "inner");
        $this->db->join("warna w", "scl.id_warna = w.id", "inner");


        if($this->input->post('sc'))
        {
            $this->db->like('scl.sales_order', $this->input->post('sc'));
        }

        if($this->input->post('sales_group'))
        {
            $this->db->like('msg.kode_sales_group', $this->input->post('sales_group'));
        }

        if($this->input->post('ow'))
        {
            $this->db->like('scl.ow', $this->input->post('ow'));
        }

        if($this->input->post('produk'))
        {
            $this->db->like('scl.nama_produk', $this->input->post('produk'));
        }

        if($this->input->post('warna'))
        {
            $this->db->like('w.nama_warna', $this->input->post('warna'));
        }
        $status_scl = array('t','ng');
		$this->db->where_in("scl.status", $status_scl);
        $this->db->where("scl.ow not in (SELECT SUBSTRING_INDEX(origin,'|',-1) as ow FROM pengiriman_barang where  dept_id= 'GRG' and status IN ('done') )");
        $this->db->where("scl.ow <> ''");
        $this->db->group_by("scl.ow");
       
		return $this->db->count_all_results();
	}

    public function get_list_ow_by_kode($sc,$sales_group,$ow,$produk,$warna)
    {
        
        //add custom filter here
        if(!empty($sc))
        {
            $this->db->like('scl.sales_order', $sc);
        }

        if(!empty($sales_group))
        {
            $this->db->like('msg.kode_sales_group', $sales_group);
        }

        if(!empty($ow))
        {
            $this->db->like('scl.ow', $ow);
        }

        if(!empty($produk))
        {
            $this->db->like('scl.nama_produk', $produk);
        }

        if(!empty($warna))
        {
            $this->db->like('w.nama_warna', $warna);
        }

        $this->db->select(" scl.sales_order, scl.ow, msg.nama_sales_group, scl.tanggal_ow, scl.nama_produk, scl.qty, scl.uom, w.nama_warna,scl.id_warna, scl.piece_info, scl.lebar_jadi, scl.uom_lebar_jadi, scl.reff_notes, scl.gramasi, scl.status as status_scl ");
        $this->db->from("sales_color_line scl");
        $this->db->join("sales_contract sc", "sc.sales_order = scl.sales_order", "inner");
        $this->db->join("mst_sales_group msg", "sc.sales_group = msg.kode_sales_group", "inner");
        $this->db->join("warna w", "scl.id_warna = w.id", "inner");

        $status_scl = array('t','ng');
		$this->db->where_in("scl.status", $status_scl);
        $this->db->where("scl.ow not in (SELECT SUBSTRING_INDEX(origin,'|',-1) as ow FROM pengiriman_barang where  dept_id= 'GRG' and status IN ('done') )");
        $this->db->where("scl.ow <> ''");
        $this->db->group_by("scl.ow");
        $this->db->order_by("scl.tanggal_ow"," ASC");
		$query = $this->db->get();
		return $query->result();
    }

}