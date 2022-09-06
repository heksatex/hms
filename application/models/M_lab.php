<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_lab extends CI_Model
{
	//var $table 		  = 'warna';
	var $column_order = array(null, 'nama_warna', 'tanggal', 'status','nama_sales_group','tot_varian', null,'notes');
	var $column_search= array('nama_warna', 'tanggal', 'status', 'notes','nama_sales_group');
	var $order  	  = array('tanggal' => 'desc');

	var $column_order2 = array(null, 'mrp.kode', 'mrp.tanggal', 'mc.nama_mesin','wv.nama_varian', 'ms.nama_status', 'mrp.origin');
	var $column_search2= array('mrp.kode', 'mrp.tanggal', 'mc.nama_mesin', 'wv.nama_varian','ms.nama_status','mrp.origin');
	var $order2  	  = array('mrp.tanggal' => 'desc');

	var $column_order3 = array(null,  'msg.nama_sales_group', 'scl.sales_order', 'scl.ow', 'scl.tanggal_ow', 'scl.nama_produk','ms.nama_status');
	var $column_search3= array('msg.nama_sales_group', 'scl.sales_order', 'scl.ow', 'scl.tanggal_ow', 'scl.nama_produk','ms.nama_status');
	var $order3  	  = array('scl.tanggal_ow' => 'desc');

	private function _get_datatables_query()
	{

		if($this->input->post('warna'))
        {
            $this->db->like('w.nama_warna', $this->input->post('warna'));
        }
        if($this->input->post('status'))
        {
            $this->db->like('w.status', $this->input->post('status'));
        }
        if($this->input->post('notes'))
        {
            $this->db->like('w.notes', $this->input->post('notes'));
        }
        if($this->input->post('marketing'))
        {
            $this->db->like('msg.kode_sales_group', $this->input->post('marketing'));
        }
		
		//$this->db->from($this->table);
		$this->db->select("w.id,w.nama_warna,w.tanggal,w.status,w.notes, mmss.nama_status, msg.nama_sales_group, 
						(SELECT count(id) as tot_var FROM warna_varian WHERE id_warna = w.id) as tot_varian");
		$this->db->from("warna w");
		$this->db->join("mst_sales_group msg", "msg.kode_sales_group=w.sales_group", "left");
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
		$this->db->select("w.nama_warna,w.tanggal,w.status,w.notes, mmss.nama_status,(SELECT count(id) as tot_var FROM warna_varian WHERE id_warna = w.id) as tot_varian");
		$this->db->from("warna w");
		$this->db->join("mst_sales_group msg", "msg.kode_sales_group=w.sales_group", "left");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=w.status", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		return $this->db->count_all_results();
	}

	private function _get_datatables_query2()
	{
		
		$this->db->select("w.id, w.nama_warna, mrp.kode, mrp.dept_id, mrp.status, mrp.tanggal, mc.nama_mesin, ms.nama_status, mrp.origin, wv.nama_varian");
		$this->db->from("warna w");
		$this->db->join("mrp_production mrp", "w.id=mrp.id_warna", "inner");
		$this->db->join("warna_varian wv", "wv.id = mrp.id_warna_varian","left");
		$this->db->join("mst_status ms", "mrp.status=ms.kode", "inner");
		$this->db->join("mesin mc", "mrp.mc_id=mc.mc_id", "left");

		$i = 0;
	
		foreach ($this->column_search2 as $item) // loop column 
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

				if(count($this->column_search2) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order2[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order2;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables2($id_warna,$dept_id)
	{
		$this->_get_datatables_query2();
		$this->db->where("w.id", $id_warna);
		$this->db->where("mrp.dept_id", $dept_id);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2($id_warna,$dept_id)
	{
		$this->_get_datatables_query2();
		$this->db->where("w.id", $id_warna);
		$this->db->where("mrp.dept_id", $dept_id);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($id_warna,$dept_id)
	{
		//$this->db->from($this->table);
		$this->db->select("w.id, w.nama_warna, mrp.kode, mrp.dept_id, mrp.status, mrp.tanggal, mc.nama_mesin, ms.nama_status, mrp.origin");
		$this->db->from("warna w");
		$this->db->join("mrp_production mrp", "w.id=mrp.id_warna", "inner");
		$this->db->join("warna_varian wv", "wv.id = mrp.id_warna_varian","left");
		$this->db->join("mst_status ms", "mrp.status=ms.kode", "inner");
		$this->db->join("mesin mc", "mrp.mc_id=mc.mc_id", "left");
		$this->db->where("w.id", $id_warna);
		$this->db->where("mrp.dept_id", $dept_id);
		return $this->db->count_all_results();
	}

	private function _get_datatables_query3()
	{
		
		$this->db->select("scl.sales_order, scl.ow, scl.tanggal_ow, scl.status, scl.nama_produk,msg.nama_sales_group, ms.nama_status");
		$this->db->from("sales_color_line scl");
		$this->db->join("sales_contract sc", "scl.sales_order = sc.sales_order", "inner");
		$this->db->join("mst_sales_group msg", "sc.sales_group = msg.kode_sales_group","inner");
		$this->db->join("mst_status ms", "scl.status = ms.kode", "inner");

		$i = 0;
	
		foreach ($this->column_search3 as $item) // loop column 
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

				if(count($this->column_search3) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order3[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order))
		{
			$order = $this->order3;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables3($id_warna)
	{
		$this->_get_datatables_query3();
		$this->db->where("scl.id_warna", $id_warna);
		$this->db->where("scl.ow <>'' ");
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered3($id_warna)
	{
		$this->_get_datatables_query3();
		$this->db->where("scl.id_warna", $id_warna);
		$this->db->where("scl.ow <>'' ");
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all3($id_warna)
	{
		$this->db->select("scl.sales_order, scl.ow, scl.tanggal_ow, scl.status, scl.nama_produk,msg.nama_sales_group, ms.nama_status");
		$this->db->from("sales_color_line scl");
		$this->db->join("sales_contract sc", "scl.sales_order = sc.sales_order", "inner");
		$this->db->join("mst_sales_group msg", "sc.sales_group = msg.kode_sales_group","inner");
		$this->db->join("mst_status ms", "scl.status = ms.kode", "inner");
		$this->db->where("scl.id_warna", $id_warna);
		$this->db->where("scl.ow <>'' ");
		return $this->db->count_all_results();
	}

	public function cek_color_by_color($warna)
	{
		return $this->db->query("SELECT nama_warna FROM warna where nama_warna = '$warna'");
	}

	public function save_color($warna,$tanggal,$notes,$status,$kode_warna,$sales_group)
	{
		return $this->db->query("INSERT INTO warna (nama_warna,tanggal,notes,status,kode_warna,sales_group) VALUES ('$warna','$tanggal','$notes','$status','$kode_warna','$sales_group')");
	}

	public function get_data_color_by_code($id_warna)
	{
		return $this->db->query("SELECT * FROM warna where id = '$id_warna' ")->row();
	}

	public function get_data_dye_aux_by_code($id_warna,$tipe_obat)
	{
		return $this->db->query("SELECT * FROM warna_items where id_warna = '$id_warna' AND type_obat = '$tipe_obat' ")->result();
	}

	public function get_data_dye_aux_varians_by_code($id_warna,$tipe_obat,$varians)
	{
		return $this->db->query("SELECT * FROM warna_items where id_warna = '$id_warna' AND type_obat = '$tipe_obat' AND id_warna_varian = '$varians' Order by row_order  ")->result();
	}

	public function update_color($id_warna,$notes,$kode_warna,$sales_group)
	{
		return $this->db->query("UPDATE warna SET notes = '$notes', kode_warna = '$kode_warna', sales_group = '$sales_group' WHERE id = '$id_warna' ");
	}

	public function save_dye_aux($id_warna,$kode_produk,$nama_produk,$qty,$uom,$reff_note,$tipe_obat,$id_warna_varian)
	{

		$row = $this->db->query("SELECT max(row_order) as ro FROM warna_items WHERE id_warna = '$id_warna' AND type_obat = '$tipe_obat' AND id_warna_varian = '$id_warna_varian'")->row_array();
		$row_order  = $row['ro'] + 1;

		return $this->db->query("INSERT INTO warna_items (id_warna,type_obat,kode_produk,nama_produk,qty,uom,reff_note,row_order,id_warna_varian) VALUES 
								('$id_warna','$tipe_obat','$kode_produk','$nama_produk','$qty','$uom','$reff_note','$row_order','$id_warna_varian')");
	}

	public function update_dye_aux($id_warna,$kode_produk,$nama_produk,$kode_produk_before,$qty,$uom,$reff_note,$tipe_obat,$row_order,$id_warna_varian)
	{
		return $this->db->query("UPDATE warna_items SET kode_produk = '$kode_produk', nama_produk = '$nama_produk', qty = '$qty', uom = '$uom', reff_note = '$reff_note' WHERE id_warna = '$id_warna' AND kode_produk = '$kode_produk_before' AND type_obat = '$tipe_obat' AND row_order = '$row_order' AND id_warna_varian  ='$id_warna_varian' ");
	}

	public function delete_dye_aux($id_warna,$row_order,$type_obat,$id_warna_varian)
	{
		return $this->db->query("DELETE FROM warna_items WHERE id_warna = '$id_warna' AND type_obat = '$type_obat' AND row_order = '$row_order' AND id_warna_Varian = '$id_warna_varian'");
	}

	public function get_list_dye_by_name($name,$tipe)
	{
		$id_category = $this->cek_id_category_by_nama($tipe);
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE (nama_produk LIKE '%$name%' OR kode_produk LIKE '%$name%') AND id_category IN ('".$id_category."') AND status_produk = 't' LIMIT 100")->result_array();
	}

	public function get_data_dye_by_kode($kode_produk,$tipe)
	{
		$id_category = $this->cek_id_category_by_nama($tipe);
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE kode_produk = '$kode_produk' AND id_category IN ('".$id_category."') ");
	}

	public function get_list_aux_by_name($name,$tipe)
	{
		$id_category = $this->cek_id_category_by_nama($tipe);
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE (nama_produk LIKE '%$name%' OR kode_produk LIKE '%$name%') AND id_category IN ('".$id_category."') AND status_produk = 't'  LIMIT 100")->result_array();
	}

	public function get_data_aux_by_kode($kode_produk,$tipe)
	{
		$id_category = $this->cek_id_category_by_nama($tipe);
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE kode_produk = '$kode_produk' AND id_category IN ('".$id_category."') ");
	}

	public function update_status_warna($id_warna,$status)
	{
		return $this->db->query("UPDATE warna SET status  = '$status' WHERE id = '$id_warna'");
	}

	public function cek_prod($id_warna,$kode_produk,$id_warna_varian)
	{
		return $this->db->query("SELECT nama_produk FROM warna_items WHERE id_warna = '$id_warna' AND kode_produk = '$kode_produk' AND id_warna_varian = '$id_warna_varian'");
	}

	// new Query
	public function cek_id_category_by_nama($nama_category)
	{
		$cek  = $this->db->query("SELECT id FROM mst_category WHERE nama_category = '$nama_category' ")->row_array();
		return $cek['id'];
	}

	public function get_last_id_warna()
	{
		$last_no =  $this->db->query("SELECT max(id) as nom FROM warna");

		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		return $no;
	}

	public function cek_item_dye_aux_by_id_warna($id_warna)
	{
		return $this->db->query("SELECT * FROM warna_items where id_warna = '$id_warna' ");
	}

	public function get_list_uom_select2_by_prod($name)
	{
		return $this->db->query("SELECT id, nama, nama, short
								FROM  uom 
								WHERE short LIKE '%$name%' ORDER BY id   ")->result_array();
	}

	public function get_produk_by_kode($kode_produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE kode_produk = '$kode_produk' ");
	}

	public function get_warna_items_by_id($id_warna,$kode_produk,$row_order)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, qty, uom, reff_note, row_order, type_obat, id_warna
								FROM warna_items 
								WHERE id_warna = '$id_warna' AND kode_produk = '$kode_produk' AND row_order = '$row_order'");
	}

	public function get_dye_aux_row($id_warna,$kode_produk,$row_order,$tipe_obat,$id_warna_varian)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, qty, uom, reff_note, row_order, type_obat, id_warna
								FROM warna_items 
								WHERE id_warna = '$id_warna' AND kode_produk = '$kode_produk' AND row_order = '$row_order' AND type_obat = '$tipe_obat' AND id_warna_varian = '$id_warna_varian'");
	}

	public function get_list_varian_warna_by_id($id_warna)
	{
		return $this->db->query("SELECT id,id_warna,nama_varian FROM warna_varian WHERE id_warna = '$id_warna' ORDER BY nama_varian asc")->result();
	}

	public function get_first_varian_by_id($id_warna)
	{
		$query =  $this->db->query("SELECT id FROM warna_varian where id_warna  = '$id_warna' ORDER by id asc LIMIT 1")->row_array();
		return $query['id'];
	}

	public function get_nama_varian_by_id($id)
	{
		$query = $this->db->query("SELECT nama_varian FROM warna_varian WHERE id = '$id'")->row_array();
		return $query['nama_varian'];
	}

	public function get_items_dti_by_varian($id_warna,$type_obat,$id_varian)
	{
		return $this->db->query("SELECT id_warna, id_warna_varian, type_obat, kode_produk, nama_produk, qty, uom, reff_note, row_order
								FROM warna_items 
								WHERE id_warna = '$id_warna' AND type_obat = '$type_obat' 
								AND id_warna_varian = '$id_varian' ORDER BY row_order ")->result();
	}

	public function get_last_varian_by_id($id_warna)
	{
		$query  =  $this->db->query("SELECT nama_varian FROM warna_varian where id_warna = '$id_warna' ORDER BY id DESC LIMIT 1")->row_array();
		return $query['nama_varian'];
	}	

	public function save_new_varian_by_id_warna($new_varian,$id_warna,$notes_varian)
	{
		$this->db->query("INSERT INTO warna_varian (id_warna,nama_varian,notes_varian) value ('$id_warna','$new_varian','$notes_varian') ");
	}

	public function get_id_new_varian_by_kode($id_warna,$varian)
	{
		$query = $this->db->query("SELECT id FROM warna_varian where id_warna = '$id_warna' AND nama_varian = '$varian'")->row();
		return $query->id;
	}

	public function simpan_warna_items_batch($sql)
	{
		return $this->db->query("INSERT INTO warna_items (id_warna,id_warna_varian,type_obat,kode_produk,nama_produk,qty,uom,reff_note,row_order) values $sql");
	}

	public function get_note_varian_by_id($id_varian)
	{
		$query = $this->db->query("SELECT notes_varian FROM warna_varian where id= '$id_varian'")->row();
		return $query->notes_varian;
	}

	public function get_note_varian_last_by_id($id_warna)
	{
		$query = $this->db->query("SELECT notes_varian FROM warna_varian where id_warna= '$id_warna' ORDER BY id DESC LIMIT 1")->row();
		return $query->notes_varian;
	}

	public function update_note_varian_by_varian($id_warna,$id_varian,$notes_varian)
	{
		$this->db->query("UPDATE warna_varian SET notes_varian  = '$notes_varian' WHERE id = '$id_varian' AND id_warna = $id_warna");
	}

	public function delete_warna_item_by_kode($id_warna,$id_varian)
	{
		$this->db->query("DELETE FROM warna_items WHERE id_warna = '$id_warna' AND id_warna_varian = '$id_varian' ");
	}

	public function cek_status_dti_by_id($id_warna)
	{
		$query = $this->db->query("SELECT status FROM warna where id = '$id_warna'")->row();
		return $query->status;
	}

	public function cek_dti_in_mrp_production($id_warna)
	{
		return $this->db->query("SELECT id_warna FROM mrp_production WHERe id_warna = '$id_warna'")->num_rows();
	}
}