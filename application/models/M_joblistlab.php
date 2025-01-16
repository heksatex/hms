<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_joblistlab extends CI_Model
{
	
    var $column_order = array(null, 'jb.nama_sales_group', 'jb.no_ow',  'jb.tanggal_ow', 'scl.status','jb.nama_produk','jb.nama_warna',  'tot_qty1', 'jb.gramasi', 'jb.nama_handling', 'jb.nama', 'jb.lebar_jadi','w.status', 'jb.reff_note','jb.delivery_date ','jb.status_resep','jb.tanggal_selesai_resep',null);
	var $column_search= array('');
	var $order  	  = array('jb.tanggal_buat' => 'asc');

    function _query()
    {
        if($this->input->post('sales_group'))
        {
            $this->db->like('jb.kode_sales_group', $this->input->post('sales_group'));
        }

        if($this->input->post('ow'))
        {
            $this->db->like('jb.no_ow', $this->input->post('ow'));
        }

        if($this->input->post('produk'))
        {
            $this->db->like('jb.nama_produk', $this->input->post('produk'));
        }

        if($this->input->post('warna'))
        {
            $this->db->like('jb.nama_warna', $this->input->post('warna'));
        }

        if($this->input->post('status_ow'))
        {
            $this->db->like('scl.status', $this->input->post('status_ow'));
        }

        if($this->input->post('status_dti'))
        {
            $this->db->like('w.status', $this->input->post('status_dti'));
        }

        if($this->input->post('status_resep'))
        {
            $this->db->like('jb.status_resep', $this->input->post('status_resep'));
        }


        $ip         = $this->input->ip_address();

        // if($this->input->post('check_stock') == 'true'){
            $this->db->select(" '$ip' as ip, jb.*,  scl.`status` as status_ow, ms.nama_status as status_dti,
            (select IFNULL(sum(qty),0) FROM stock_quant WHERE lokasi = 'GRG/Stock' AND kode_produk = jb.kode_produk ) as tot_qty1");
        // }else{
            // $this->db->select(" '$ip' as ip, jb.*,  scl.`status` as status_ow, w.status as status_dti ");
        // }

        $this->db->from("job_list_lab jb");
        $this->db->join("sales_color_line scl", "jb.id_sales_color_line = scl.id", "inner");
        $this->db->join("warna w", "jb.warna_id = w.id", "inner");
        $this->db->join("mst_status ms","ms.kode = w.status","inner");
    }
    
	private function _get_datatables_query()
	{	

        //add custom filter here
       
       
        $this->_query();
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
        $this->_query();
		return $this->db->count_all_results();
	}

    public function get_data_joblist_lab_by_id($id)
    {
        $this->db->where('id',$id);
        $query = $this->db->get('job_list_lab');
        return $query->row();
    }

    public function update_joblistlab_by_id($id,$data)
    {
        $this->db->where('id', $id);
        $this->db->update('job_list_lab', $data);        
    }


    public function get_data_excel()
    {
        $this->_query();
        $query = $this->db->get();
		return $query->result();
    }



}