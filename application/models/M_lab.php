<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_lab extends CI_Model
{
	//var $table 		  = 'warna';
	var $column_order = array(null, 'kode_warna', 'tanggal', 'status', 'notes');
	var $column_search= array('kode_warna', 'tanggal', 'status', 'notes');
	var $order  	  = array('tanggal' => 'desc');

	private function _get_datatables_query()
	{
		
		//$this->db->from($this->table);
		$this->db->select("w.kode_warna,w.tanggal,w.status,w.notes, mmss.nama_status");
		$this->db->from("warna w");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=w.status", "inner");

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
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($mmss)
	{
		$this->_get_datatables_query();
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($mmss)
	{
		//$this->db->from($this->table);
		$this->db->select("w.kode_warna,w.tanggal,w.status,w.notes, mmss.nama_status");
		$this->db->from("warna w");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=w.status", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		return $this->db->count_all_results();
	}

	public function cek_color_by_color($warna)
	{
		return $this->db->query("SELECT kode_warna FROM warna where kode_warna = '$warna'");
	}

	public function save_color($warna,$tanggal,$notes,$status)
	{
		return $this->db->query("INSERT INTO warna (kode_warna,tanggal,notes,status) VALUES ('$warna','$tanggal','$notes','$status')");
	}

	public function get_data_color_by_code($warna)
	{
		return $this->db->query("SELECT * FROM warna where kode_warna = '$warna' ")->row();
	}

	public function get_data_dye_aux_by_code($warna,$tipe_obat)
	{
		return $this->db->query("SELECT * FROM warna_items where kode_warna = '$warna' AND type_obat = '$tipe_obat' ")->result();
	}

	public function update_color($warna,$notes)
	{
		return $this->db->query("UPDATE warna SET notes = '$notes' WHERE kode_warna = '$warna'");
	}

	public function save_dye_aux($warna,$kode_produk,$nama_produk,$qty,$uom,$reff_note,$tipe_obat)
	{

		$row = $this->db->query("SELECT max(row_order) as ro FROM warna_items WHERE kode_warna = '$warna' AND type_obat = '$tipe_obat'")->row_array();
		$row_order  = $row['ro'] + 1;

		return $this->db->query("INSERT INTO warna_items (kode_warna,type_obat,kode_produk,nama_produk,qty,uom,reff_note,row_order) VALUES 
								('$warna','$tipe_obat','$kode_produk','$nama_produk','$qty','$uom','$reff_note','$row_order')");
	}

	public function delete_dye_aux($warna,$row_order,$type_obat)
	{
		return $this->db->query("DELETE FROM warna_items WHERE kode_warna = '$warna' AND type_obat = '$type_obat' AND row_order = '$row_order'");
	}

	public function get_list_dye_by_name($name)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE nama_produk LIKE '%$name%' AND kategori IN ('DYE') LIMIT 10")->result_array();
	}

	public function get_data_dye_by_kode($kode_produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE kode_produk = '$kode_produk' AND kategori IN ('DYE') ");
	}

		public function get_list_aux_by_name($name)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE nama_produk LIKE '%$name%' AND kategori IN ('AUX') LIMIT 10")->result_array();
	}

	public function get_data_aux_by_kode($kode_produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE kode_produk = '$kode_produk' AND kategori IN ('AUX') ");
	}

	public function update_status_warna($warna,$status)
	{
		return $this->db->query("UPDATE warna SET status  = '$status' WHERE kode_warna = '$warna'");
	}

	public function cek_prod($kode_warna,$kode_produk)
	{
		return $this->db->query("SELECT nama_produk FROM warna_items WHERE kode_warna = '$kode_warna' AND kode_produk = '$kode_produk'");
	}

	
}