<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_splitLot extends CI_Model
{

    var $column_order = array(null, 'a.kode_split','a.tanggal','d.nama', 'a.kode_produk', 'a.nama_produk', 'a.lot', 'a.qty', 'a.qty2', 'jml_split', 'a.note' );
	var $column_search= array('a.kode_split','a.tanggal','d.nama', 'a.kode_produk', 'a.nama_produk', 'a.lot', 'a.qty', 'a.qty2', 'a.note' );
	var $order  	  = array('a.tanggal' => 'desc');

    var $table2  	    = 'stock_quant';
	var $column_order2  = array(null, 'kode_produk', 'nama_produk', 'lot', 'qty', 'qty2', 'nama_grade', 'reff_note', 'reserve_move');
	var $column_search2 = array('kode_produk','nama_produk', 'lot', 'qty', 'qty2', 'nama_grade', 'reff_note', 'reserve_move');
	var $order2  	    = array('move_date' => 'desc');

	private function _get_datatables_query()
	{	

		//add custom filter here
        if($this->input->post('dept_id'))
        {
            $this->db->like('dept_id', $this->input->post('dept_id'));
        }
		if($this->input->post('nama_produk'))
        {
            $this->db->like('nama_produk', $this->input->post('nama_produk'));
        }
		if($this->input->post('lot'))
        {
            $this->db->like('lot', $this->input->post('lot'));
        }
        if($this->input->post('note'))
        {
            $this->db->like('note', $this->input->post('note'));
        }


		$this->db->select("a.kode_split, a.tanggal, a.quant_id,  a.dept_id, a.kode_produk, a.nama_produk, a.lot, a.qty, a.uom,  a.qty2, a.uom2, a.note, a.nama_user,  d.nama as departemen, (SELECT count(kode_split) as total FROM split_items WHERE kode_split = a.kode_split ) as jml_split" );
		$this->db->from("split a");		
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
        $this->db->select("a.kode_split, a.tanggal, a.quant_id,  a.dept_id, a.kode_produk, a.nama_produk, a.lot, a.qty, a.uom,  a.qty2, a.uom2, a.note, a.nama_user,  d.nama as departemen, (SELECT count(kode_split) as total FROM split_items WHERE kode_split = a.kode_split ) as jml_split" );
		$this->db->from("split a");		
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");

		return $this->db->count_all_results();
	}


    public function get_kode_split()
	{
		$kode="SPL".date("y") .  date("m");
        $result=$this->db->query("SELECT kode_split FROM split WHERE month(tanggal)='" . date("m") . "' AND year(tanggal)='" . date("Y") . "' ORDER BY RIGHT(kode_split,6) DESC LIMIT 1");
        if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=substr($row->kode_split,-6)+1;
        }else{
            $dgt="1";
        }
        $dgt=substr("000000" . $dgt,-6);            
        $kode_split=$kode . $dgt;
        return $kode_split;
	}



    private function _get_datatables2_query()
	{
		$this->db->from($this->table2);

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
		else if(isset($this->order2))
		{
			$order = $this->order2;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables2($kode_lokasi)
	{
		$this->_get_datatables2_query();		
		$this->db->where('lokasi', $kode_lokasi);
		if(isset($_POST["length"]) && $_POST["length"] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2($kode_lokasi)
	{
		$this->db->where('lokasi', $kode_lokasi);
		$this->_get_datatables2_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($kode_lokasi)
	{
		$this->db->where('lokasi', $kode_lokasi);
		$this->db->from($this->table2);
		return $this->db->count_all_results();
	}

    public function save_splitlot($kode_split,$tgl,$departemen,$quant_id,$kode_produk,$nama_produk,$lot,$qty,$uom_qty,$qty2,$uom_qty2,$note,$nama_user)
    {
        $this->db->query("INSERT INTO split (kode_split,tanggal,dept_id,quant_id,kode_produk,nama_produk,lot,qty,uom,qty2,uom2,note,nama_user) values ('$kode_split','$tgl','$departemen','$quant_id','$kode_produk','$nama_produk','$lot','$qty','$uom_qty','$qty2','$uom_qty2','$note','$nama_user')") ;
    }

    public function save_split_items_batch($sql)
    {
        $this->db->query("INSERT INTO split_items (kode_split, quant_id_baru, qty, uom, qty2, uom2, lot_baru, row_order) values $sql ");
    }

    public function get_data_split_by_kode($kode)
    {
        return $this->db->query("SELECT s.kode_split,s.tanggal,s.dept_id,s.quant_id,s.kode_produk,s.nama_produk,s.lot,s.qty,s.uom,s.qty2,s.uom2,s.note,s.nama_user, d.nama as nama_departemen
                        FROM split s
                        LEFT JOIN departemen d ON s.dept_id = d.kode
                        WHERE s.kode_split = '$kode' ")->row(); 
    }

    public function get_data_split_items_by_kode($kode)
    {
        return $this->db->query("SELECT kode_split, qty, uom, qty2, uom2, lot_baru, row_order
                        FROM split_items 
                        WHERE kode_split = '$kode' ")->result(); 
    }


	public function simpan_adjustment_batch($sql)
    {
        return $this->db->query("INSERT INTO adjustment (kode_adjustment,create_date,lokasi_adjustment,kode_lokasi,note,status,nama_user) values $sql ");
    }

    public function simpan_adjustment_items_batch($sql)
	{
		return $this->db->query("INSERT INTO adjustment_items (kode_adjustment, quant_id, kode_produk, lot, uom, qty_data, qty_adjustment, uom2, qty_data2, qty_adjustment2, move_id, qty_move, qty2_move, row_order) values $sql ");
	}


}