<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_lokasi extends CI_Model
{
	var $column_order = array(null, 'd.nama','a.kode_lokasi', 'a.nama', 'ms.nama_status', 'a.panah');
	var $column_search= array('d.nama','a.kode_lokasi', 'a.nama','ms.nama_status');
	var $order  	  = array('a.id' => 'asc');

	private function _get_datatables_query()
	{

        if($this->input->post('departemen'))
        {
    		$this->db->where('d.kode',$this->input->post('departemen'));
        }
        if($this->input->post('nama_lokasi'))
        {
            $this->db->like('a.nama', $this->input->post('nama_lokasi'));
        }
        if($this->input->post('arah_panah') == '0' OR $this->input->post('arah_panah') == '1' )
        {
            $this->db->where('a.panah', $this->input->post('arah_panah'));
        }
        if($this->input->post('status') )
        {
    		$this->db->where('a.status_aktif',$this->input->post('status'));
        }


		$this->db->select("a.id, a.kode_lokasi, a.nama as nama_lokasi, ms.nama_status, IF(a.panah='1', 'Atas', 'Bawah') as arah_panah, d.kode, d.nama as departemen" );
		$this->db->from("mst_lokasi a");		
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");
        $this->db->JOIN("mst_status ms","a.status_aktif = ms.kode","INNER" );

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
		$this->db->select("a.id, a.kode_lokasi, a.nama as nama_lokasi, ms.nama_status, IF(a.panah='1', 'Atas', 'Bawah') as arah_panah, d.kode, d.nama as departemen" );
		$this->db->from("mst_lokasi a");		
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");
        $this->db->JOIN("mst_status ms","a.status_aktif = ms.kode","INNER" );	
		return $this->db->count_all_results();
	}

	public function cek_kode_lokasi_by_kode($kode_lokasi,$dept_id)
	{
		return $this->db->query("SELECT * FROM mst_lokasi WHERE kode_lokasi = '$kode_lokasi' AND dept_id = '$dept_id' ");
	}

	public function save_lokasi($last_id,$dept_id,$kode_lokasi,$nama_lokasi,$aisle,$bay,$slot,$panah,$status)
	{
		return $this->db->query("INSERT INTO mst_lokasi(id,dept_id,kode_lokasi,nama_lokasi,nama,aisle,bay,slot,panah,status_aktif) values ('$last_id','$dept_id','$kode_lokasi','rack','$nama_lokasi','$aisle','$bay','$slot','$panah','$status') ");
	}

	public function update_lokasi($last_id,$panah,$status)
	{
		return $this->db->query("UPDATE  mst_lokasi SET panah = '$panah', status_aktif ='$status' WHERE id = '$last_id'  ");
	}

	public function get_last_id_mst_lokasi()
	{
		$last_no =  $this->db->query("SELECT max(id) as nom FROM mst_lokasi");

		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		return $no;
	}


	public function get_mst_lokasi_by_kode($id)
	{
		$this->db->select('a.id, a.kode_lokasi, a.nama, a.aisle, a.bay, a.slot, a.panah, a.status_aktif, a.dept_id, b.nama, ');
		$this->db->FROM('mst_lokasi a');
		$this->db->JOIN("departemen b","a.dept_id=b.kode","INNER");
		$this->db->where('a.id', $id);
		$query  = $this->db->get();
		return $query;

	}

    public function get_list_departement_select2($nama)
	{
		return $this->db->query("SELECT kode,nama FROM departemen  WHERE nama LIKE '%$nama%' ORDER BY nama ")->result();
	}

}