<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_printlabel extends CI_Model
{
    var $column_order = array(null, 'seri_beam', 'ui', 'type_beam', 'berat_kosong');
	var $column_search= array('seri_beam', 'ui',  'berat_kosong');
	var $order  	  = array('id' => 'asc');

    var $table        = "mst_beam_dasar";

	private function _get_datatables_query()
	{	
        $this->db->from($this->table);

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
		if(isset($_POST["length"]) && $_POST["length"] != -1)
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
        $this->db->from($this->table);
		return $this->db->count_all_results();
	}   


	public function get_mst_beam_dasar(array $where = [])
	{

		if($where) {
			$this->db->where($where);
		}
		$this->db->order_by('id', 'asc');
		$result  = $this->db->get('mst_beam_dasar');
		return $result;
	}

}