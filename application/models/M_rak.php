<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_rak extends CI_Model
{
	var $column_order = array(null, 'd.nama','a.kode_rak', 'a.nama_rak', 'a.status_aktif', 'a.panah');
	var $column_search= array('d.nama','a.kode_rak', 'a.nama_rak');
	var $order  	  = array('d.nama' => 'asc');

	private function _get_datatables_query()
	{
		$this->db->select("a.id, a.kode_rak, a.nama_rak, IF(a.status_aktif='t', 'Aktif', 'Tidak Aktif') as status_aktif, IF(a.panah='1', 'Atas', 'Bawah') as arah_panah, d.kode, d.nama as departemen" );
		$this->db->from("mst_rak a");		
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");


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
		$this->db->select("SELECT a.kode_rak, a.nama_rak, IF(a.status_aktif='t', 'Aktif', 'Tidak Aktif') as status_aktif, IF(a.panah='0', 'Atas', 'Bawah') as arah_panah, d.kode, d.nama as departemen" );
		$this->db->from("mst_rak a");		
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");		
		return $this->db->count_all_results();
	}

	public function cek_kode_rak_by_kode($kode_rak,$dept_id)
	{
		return $this->db->query("SELECT * FROM mst_rak WHERE kode_rak = '$kode_rak' AND dept_id = '$dept_id' ");
	}

	public function save_rak($last_id,$dept_id,$kode_rak,$nama_rak,$aisle,$bay,$slot,$panah,$status)
	{
		return $this->db->query("INSERT INTO mst_rak(id,dept_id,kode_rak,nama_rak,aisle,bay,slot,panah,status_aktif) values ('$last_id','$dept_id','$kode_rak','$nama_rak','$aisle','$bay','$slot','$panah','$status') ");
	}

	public function update_rak($last_id,$panah,$status)
	{
		return $this->db->query("UPDATE  mst_rak SET panah = '$panah', status_aktif ='$status' WHERE id = '$last_id'  ");
	}

	public function get_last_id_mst_rak()
	{
		$last_no =  $this->db->query("SELECT max(id) as nom FROM mst_rak");

		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		return $no;
	}


	public function get_mst_rak_by_kode($id)
	{
		$this->db->select('a.id, a.kode_rak, a.nama_rak, a.aisle, a.bay, a.slot, a.panah, a.status_aktif, a.dept_id, b.nama, ');
		$this->db->FROM('mst_rak a');
		$this->db->JOIN("departemen b","a.dept_id=b.kode","INNER");
		$this->db->where('a.id', $id);
		$query  = $this->db->get();
		return $query->row();

	}
}