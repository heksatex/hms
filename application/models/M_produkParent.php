<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_produkParent extends CI_Model
{
	var $column_order = array(null, 'nama', 'tanggal', 'child', 'nama_status');
	var $column_search= array('tanggal','nama', 'nama_status');
	var $order  	  = array('tanggal' => 'desc');

	private function _get_datatables_query()
	{

		if($this->input->post('nama_parent'))
        {
    		$this->db->like('p.nama',$this->input->post('nama_parent'));
        }
		if($this->input->post('status'))
        {
    		$this->db->where('p.status_parent',$this->input->post('status'));
        }

		$this->db->select("p.id,p.nama, p.tanggal, count(id_parent) as child, ms.nama_status, p.status_parent ");
		$this->db->from("mst_produk_parent p");		
		$this->db->JOIN("mst_status ms","ms.kode = p.status_parent","LEFT");
		$this->db->JOIN("mst_produk mp","p.id = mp.id_parent","LEFT");
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
        $this->db->group_by("p.id");
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered()
	{
		$this->_get_datatables_query();
        $this->db->group_by("p.id");
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all()
	{
		$this->db->select("p.id,p.nama, p.tanggal, count(id_parent) as child, ms.nama_status, p.status_parent ");
		$this->db->from("mst_produk_parent p");		
		$this->db->JOIN("mst_status ms","ms.kode = p.status_parent","LEFT");
		$this->db->JOIN("mst_produk mp","p.id = mp.id_parent","LEFT");
        $this->db->group_by("p.id");
		return $this->db->count_all_results();
	}

    public function get_last_id_parent()
	{
		$last_no =  $this->db->query("SELECT max(id) as nom FROM mst_produk_parent");

		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		return $no;
	}

    public function cek_nama_parent_by_nama($nama)
    {
        return $this->db->query("SELECT id, nama FROM mst_produk_parent WHERE nama = '$nama'");
    }

	public function cek_nama_parent_by_nama_id($nama,$id)
    {
        return $this->db->query("SELECT id, nama FROM mst_produk_parent WHERE nama = '$nama' and id != '$id'");
    }

    public function save_product_parent($nama)
    {
        $tanggal     = date("Y-m-d H:i:s");
        $this->db->query("INSERT INTO mst_produk_parent (tanggal,nama) values ('".$tanggal."','".$nama."')");
    }

    public function update_product_parent_by_id($id_parent,$nama,$status)
    {
        $this->db->query("UPDATE mst_produk_parent SET nama = '$nama', status_parent = '$status' WHERE id = '$id_parent' ");
    }

    public function get_data_parent_by_id($id_parent)
    {
        return $this->db->query("SELECT mpp.id, mpp.tanggal, mpp.nama, mpp.status_parent, ms.nama_status 
								FROM mst_produk_parent  mpp
								LEFT JOIN mst_status ms ON mpp.status_parent = ms.kode
								WHERE mpp.id = '$id_parent'");
    }

    public function get_list_child_by_parent($id_parent)
    {
        return $this->db->query("SELECT mp.id, mp.kode_produk, mp.nama_produk, mp.uom, mp.uom_2, mp.create_date, mp.status_produk, cat.nama_category, sat.nama_status
                        FROM mst_produk mp
                        INNER JOIN mst_category cat ON mp.id_category = cat.id
                        INNER JOIN mst_status sat ON sat.kode = mp.status_produk
                        WHERE mp.id_parent = '$id_parent' ");
    }

	public function get_jml_child_by_id_parent($id)
	{
		return $this->db->query("SELECT count(id_parent) as jml FROM mst_produk WHERE id_parent = '$id' group by id_parent")->num_rows();
	}

	public function get_list_product_parent_by_kode($post_nama_parent,$post_status,$post_filterAll)
	{

		if($post_nama_parent){
			$this->db->where("p.nama",$post_nama_parent);
        }

		if($post_status){
			$this->db->where("p.status_parent",$post_status);
        }

		if($post_filterAll){
			$this->db->group_start(); 
			$this->db->or_like("p.nama", $post_filterAll);
			$this->db->or_like("ms.nama_status", $post_filterAll);
			$this->db->group_end(); 
		}


		$this->db->select("p.id,p.nama, p.tanggal, count(id_parent) as child, ms.nama_status, p.status_parent ");
		$this->db->from("mst_produk_parent p");		
		$this->db->JOIN("mst_status ms","ms.kode = p.status_parent","LEFT");
		$this->db->JOIN("mst_produk mp","p.id = mp.id_parent","LEFT");
		$this->db->order_by("p.tanggal","desc");
		$this->db->group_by("p.id");
		$query = $this->db->get();
		return $query->result();

	}
}