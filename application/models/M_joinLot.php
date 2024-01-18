<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_joinLot extends CI_Model
{
    var $column_order = array(null, 'a.kode_join','a.tanggal_buat','a.tanggal_transaksi','d.nama', 'jml_join', 'a.note', 'a.status' );
	var $column_search= array( 'a.kode_join','a.tanggal_buat','a.tanggal_transaksi','d.nama', 'a.note', 'a.status' );
	var $order  	  = array('a.tanggal_buat' => 'desc');

	private function _get_datatables_query()
	{	
		//add custom filter here
        if($this->input->post('dept_id'))
        {
            $this->db->like('dept_id', $this->input->post('dept_id'));
        }
        if($this->input->post('note'))
        {
            $this->db->like('note', $this->input->post('note'));
        }

		$this->db->select("a.kode_join, a.tanggal_buat, a.tanggal_transaksi, a.dept_id,note, a.nama_user,  d.nama as departemen, a.status, b.nama_status, (SELECT count(kode_join) as total FROM join_lot_items WHERE kode_join = a.kode_join ) as jml_join" );
		$this->db->from("join_lot a");		
		$this->db->JOIN("mst_status b","a.status = b.kode","INNER");
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
        $this->db->select("a.kode_join, a.tanggal_buat, a.tanggal_transaksi, a.dept_id,note, a.nama_user,  d.nama as departemen, a.status,  b.nama_status, (SELECT count(kode_join) as total FROM join_lot_items WHERE kode_join = a.kode_join ) as jml_join" );
		$this->db->from("join_lot a");		
		$this->db->JOIN("mst_status b","b.kode = a.status","INNER");
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");

		return $this->db->count_all_results();
	}   


    function get_data_join_lot_by_kode($id)
	{
		$this->db->where('kode_join',$id);
        $this->db->SELECT('j.*, d.nama as departemen, msg.nama_sales_group');
        $this->db->FROM('join_lot j');
        $this->db->JOIN('departemen d ',"j.dept_id = d.kode","INNER");
        $this->db->JOIN('mst_sales_group msg ',"msg.kode_sales_group = j.sales_group","LEFT");
		$result = $this->db->get();
		return $result->row();
	}

	function get_data_join_lot_items_by_kode($id)
	{
		$this->db->where('jli.kode_join',$id);
		$this->db->SELECT('jli.*, msg.nama_sales_group');
		$this->db->FROM("join_lot_items as jli");
		$this->db->JOIN("mst_sales_group msg","jli.sales_group = msg.kode_sales_group","left");
		$this->db->order_by('jli.row_order','asc');
		$result = $this->db->get();
		return $result->result();
	}

    function update_join_lot_by_kode($data_update,$kode)
    {
        $this->db->where('kode_join',$kode);
        $this->db->update('join_lot',$data_update);
    }

    function insert_data_join_lot($data_insert)
	{
		try{
			$this->db->insert('join_lot', $data_insert);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }

	}

    function get_stock_quant_by_lot($lot,$lokasi=null)
    {
        $this->db->where('lot',$lot);
        if(!empty($lokasi)){
            $this->db->where('lokasi',$lokasi);
        }
        return $this->db->get('stock_quant')->row();
    }

	function get_stock_quant_by_id($quant_id,$lokasi=null)
    {
        $this->db->where('quant_id',$quant_id);
        if(!empty($lokasi)){
            $this->db->where('lokasi',$lokasi);
        }
        return $this->db->get('stock_quant')->row();
    }

    function insert_data_join_lot_items($data_insert)
    {
        try{
			$this->db->insert_batch('join_lot_items', $data_insert);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

	// var $table2  	    = 'stock_quant';
	var $column_order2    = array(null, 'sq.kode_produk', 'sq.nama_produk', 'corark_remark', 'warna_remark', 'sq.lot', 'sq.qty', 'sq.qty2', 'sq.qty_jual', 'sq.qty2_jual','sq.nama_grade', 'sq.lebar_jadi', 'nama_sales_group',  'sq.reserve_move');
	var $column_search2   = array('sq.kode_produk','sq.nama_produk', 'corak_remark','warna_remark','sq.lot', 'sq.qty', 'sq.qty2', 'sq.qty_jual', 'sq.qty2_jual', 'sq.nama_grade','sq.lebar_jadi', 'nama_sales_group', 'sq.reserve_move');
	var $order2  	    = array('sq.move_date' => 'desc');
	

    private function _get_datatables2_query()
	{
		$this->db->SELECT("sq.quant_id, sq.kode_produk, sq.nama_produk, sq.lot, sq.qty, sq.uom, sq.qty2, sq.uom2, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual, sq.reff_note, sq.reserve_move, sq.lokasi_fisik, sq.nama_grade, msg.nama_sales_group, sq.lebar_jadi, sq.uom_lebar_jadi, sq.corak_remark, sq.warna_remark");
		$this->db->FROM("mrp_production_fg_hasil fg");
		$this->db->JOIN("mrp_production mrp","fg.kode = mrp.kode","INNER");
		$this->db->JOIN("stock_quant sq","fg.quant_id = sq.quant_id","INNER");
		$this->db->JOIN("mst_sales_group msg","sq.sales_group = msg.kode_sales_group","LEFT");
		
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
		$this->db->where('sq.lokasi', $kode_lokasi);
		$this->db->where_not_in('fg.lokasi', "GJD/Waste");
		$this->db->where('mrp.dept_id',"GJD");
		
		if(isset($_POST["length"]) && $_POST["length"] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2($kode_lokasi)
	{
		$this->db->where('sq.lokasi', $kode_lokasi);
		$this->db->where_not_in('fg.lokasi', "GJD/Waste");
		$this->db->where('mrp.dept_id',"GJD");
		$this->_get_datatables2_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($kode_lokasi)
	{
		$this->_get_datatables2_query();
		$this->db->where('sq.lokasi', $kode_lokasi);
		$this->db->where_not_in('fg.lokasi', "GJD/Waste");
		$this->db->where('mrp.dept_id',"GJD");
		return $this->db->count_all_results();
	}

	public function get_row_order_join_lot_by_kode($kode) 
    {
        $last_no = $this->db->query("SELECT max(row_order) as nom FROM join_lot_items where kode_join = '$kode'");
        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
    }

	public function cek_lot_join_by_kode($kode,$lot)
	{
		$this->db->where('kode_join',$kode);
		$this->db->where('lot',$lot);
		$result = $this->db->get('join_lot_items');
		return $result;

	}


	public function cek_lot_join_by_quant($kode,$quant_id,$row_order)
	{
		$this->db->where('kode_join',$kode);
		$this->db->where('quant_id',$quant_id);
		$this->db->where('row_order',$row_order);
		$result = $this->db->get('join_lot_items');
		return $result;

	}
	
	public function delete_join_lot_items_by_kode($kode,$quant_id,$row_order)
	{
		$this->db->where('kode_join',$kode);
		$this->db->where('quant_id',$quant_id);
		$this->db->where('row_order',$row_order);
		$result = $this->db->delete('join_lot_items');
		return $result;
	}


	public function simpan_adjustment_batch($data_adj)
    {
		$this->db->insert_batch('adjustment', $data_adj);
        return is_array($this->db->error());
    }

    public function simpan_adjustment_items_batch($data_adj_items)
	{
		$this->db->insert_batch('adjustment_items', $data_adj_items);
        return is_array($this->db->error());
	}

	function update_stock_quant_by_kode($data_update_stock)
    {
        $this->db->update_batch("stock_quant",$data_update_stock,'quant_id');
        return $this->db->affected_rows();
    }
}