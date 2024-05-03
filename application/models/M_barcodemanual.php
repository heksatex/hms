<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class m_barcodemanual extends CI_Model
{   
    var $column_order = array(null, 'mrp.kode', 'mrp.tanggal_buat', 'mrp.tanggal_transaksi', 'msg.nama_sales_group', 'mta.name_type', 'mrp.tot_batch','mrp.kode_adjustment',  'mrp.notes', 'mrp.nama_user', 'nama_status');
	var $column_search= array('mrp.kode', 'mrp.tanggal_buat','mrp.tanggal_transaksi', 'msg.nama_sales_group', 'mta.name_type','mrp.tot_batch', 'mrp.kode_adjustment', 'mrp.notes', 'mrp.nama_user', 'nama_status', 'mmbi.lot');
	var $order  	  = array('mrp.tanggal_buat' => 'desc');

    protected $db_debug;

    public function __construct() {
        $this->db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
    }

    private function _get_datatables_query()
	{
		if($this->input->post('pb')){
    		$this->db->like('mrp.kode',$this->input->post('pb'));
        }
		if($this->input->post('sales_group')){
			$this->db->where('mrp.sales_group',$this->input->post('sales_group'));
		}
		if($this->input->post('status')){
    		$this->db->where('mrp.status',$this->input->post('status'));
        }
		if($this->input->post('lot')){
    		$this->db->like('mmbi.lot',$this->input->post('lot'));
        }

		$this->db->SELECT("mrp.kode, mrp.tanggal_buat, mrp.tanggal_transaksi, mrp.sales_group, msg.nama_sales_group, mrp.kode_adjustment, mrp.tot_batch, mrp.notes, mrp.nama_user, mrp.status, ms.nama_status, mrp.id_type_adjustment, mta.name_type, mmbi.lot");
		$this->db->FROM("mrp_manual mrp");
		$this->db->JOIN("mrp_manual_batch_items mmbi","mrp.kode = mmbi.kode", "LEFT");
		$this->db->JOIN("mst_status ms","ms.kode = mrp.status", "LEFT");
		$this->db->JOIN("mst_sales_group msg","msg.kode_sales_group = mrp.sales_group", "LEFT");
		$this->db->JOIN("mst_type_adjustment mta","mta.id = mrp.id_type_adjustment", "LEFT");
		$this->db->group_by('mrp.kode');

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
		$this->db->SELECT("mrp.kode, mrp.tanggal_buat, mrp.sales_group, mst.nama_sales_group, mrp.tot_batch, mrp.notes, mrp.nama_user, mrp.status, ms.nama_status");
		$this->db->FROM("mrp_manual mrp");
		$this->db->JOIN("mst_status ms","ms.kode = mrp.status", "LEFT");
		$this->db->JOIN("mst_sales_group msg","msg.kode_sales_group = mrp.sales_group", "LEFT");
		return $this->db->count_all_results();
	} 

	var $column_order2 = array(null, 'mmb.nama_produk', 'mmb.corak_remark', 'mmb.warna_remark','q.nama','mmb.grade','mmb.jml_pcs','mmb.qty','mmb.qty2','mmb.qty_jual','mmb.qty2_jual','mmb.lebar_jadi','mmb.kode_k3l',null);
	var $column_search2= array('mmb.nama_produk', 'mmb.kode_produk', 'mmb.corak_remark', 'mmb.warna_remark', 'q.nama','mmb.grade','mmb.jml_pcs','mmb.qty','mmb.uom','mmb.qty2','mmb.uom2','mmb.qty_jual','mmb.uom_jual','mmb.qty2_jual','mmb.uom2_jual','mmb.lebar_jadi','mmb.uom_lebar_jadi','mmb.kode_k3l');
	var $order2  	  = array('mmb.row_order' => 'asc');

	private function _get_datatables_query2()
	{

		$this->db->SELECT('mb.status, mmb.*, q.nama as nama_quality');
		$this->db->FROM('mrp_manual mb');
		$this->db->JOIN('mrp_manual_batch mmb', 'mb.kode = mmb.kode', "INNER");
		$this->db->JOIN("mst_quality as q", "mmb.id_quality = q.id",'LEFT');

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

	function get_list_mrp_manual_batch($kode)
	{
		$this->db->where('mmb.kode',$kode);
		$this->_get_datatables_query2();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}


	function count_filtered2($kode)
	{
		$this->db->where('mmb.kode',$kode);
		$this->_get_datatables_query2();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($kode)
	{
		$this->db->where('mmb.kode',$kode);
		$this->db->FROM('mrp_manual mb');
		$this->db->JOIN('mrp_manual_batch mmb', 'mb.kode = mmb.kode', "INNER");
		$this->db->JOIN("mst_quality as q", "mmb.id_quality = q.id",'LEFT');
		return $this->db->count_all_results();
	} 

	var $table3        = 'mrp_manual_batch_items';
	var $column_order3 = array(null, 'nama_produk', 'corak_remark', 'warna_remark', 'grade' ,'lot' ,'qty','qty2', 'qty_jual', 'qty2_jual', 'lebar_jadi', null);
	var $column_search3= array('nama_produk', 'kode_produk', 'corak_remark', 'warna_remark', 'grade' ,'qty','uom','qty2','uom2','qty_jual','uom_jual','qty2_jual','uom2_jual','lebar_jadi','uom_lebar_jadi');
	var $order3  	  = array('row_order' => 'asc');

	private function _get_datatables_query3()
	{

		$this->db->from($this->table3);

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
		else if(isset($this->order3))
		{
			$order = $this->order3;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}


	function get_list_mrp_manual_batch_items($kode)
	{
		$this->db->where('kode',$kode);
		$this->_get_datatables_query3();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}


	function count_filtered3($kode)
	{
		$this->db->where('kode',$kode);
		$this->_get_datatables_query3();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all3($kode)
	{
		$this->db->where('kode',$kode);
		$this->db->from($this->table3);
		return $this->db->count_all_results();
	} 


    function insert_data_barcode_manual($data)
    {
        try{
            $this->db->insert('mrp_manual', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

	function insert_data_barcode_manual_batch($data)
	{
		try{
            $this->db->insert('mrp_manual_batch', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}

    function get_data_mrp_manual_by_id($id)
    {
        $this->db->where('kode',$id);
        $result = $this->db->get('mrp_manual');
        return $result->row();
    }

	function get_data_mrp_manual_batch_by_id($id)
    {
        $this->db->where('mmb.kode',$id);
		$this->db->order_by('mmb.row_order','asc');
		$this->db->SELECT('mmb.*, q.nama as nama_quality');
		$this->db->FROM('mrp_manual_batch mmb');
		$this->db->JOIN("mst_quality as q", "mmb.id_quality = q.id",'LEFT');
		$result= $this->db->get();
        return $result->result();
    }

	function get_data_mrp_manual_batch_items_by_id($id)
	{
		$this->db->where('kode',$id);
		$result = $this->db->get('mrp_manual_batch_items');
		return $result->result();
	}

	public function get_list_produk_gudang_jadi($name)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom, uom_2, lebar_jadi, uom_lebar_jadi
									FROM  mst_produk 
									WHERE CONCAT(kode_produk,nama_produk)  LIKE '%$name%' AND status_produk = 't' AND id_category = '21' ORDER BY create_date desc LIMIT 100  ")->result();
	}

    function update_data_barcode_manual($kode,$data)
	{
		$this->db->where('kode', $kode);
		$this->db->update('mrp_manual', $data);
        return $this->db->affected_rows();
	}

	function get_row_order_mrp_batch_by_kode($kode)
	{
		$last_no = $this->db->query("SELECT max(row_order) as nom FROM mrp_manual_batch where kode = '$kode'");
        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
	}

	function update_total_batch($kode)
	{	
		$jml = $this->get_total_batch_by_kode($kode);
		if(empty($jml)){
			$status = 'draft';
		}else{
			$status = 'process';
		}
		$data = array('tot_batch' => $jml, 'tanggal_transaksi' => date('Y-m-d H:i:s'), 'status'=>$status);
		$this->db->where('kode', $kode);
		$this->db->update('mrp_manual', $data);
	}
	
	function get_total_batch_by_kode($kode)
	{
		$this->db->WHERE('kode',$kode);
		$this->db->SELECT('count(kode) as jml');
		$this->db->FROM('mrp_manual_batch');
		$query = $this->db->get();
		$result= $query->row();
		if(empty($result->jml)){
			$jml = 0;
		}else{
			$jml = (int) $result->jml;
		}
		return $jml;
	}

	function get_data_mrp_manual_batch_by_row($kode,$row)
	{
		$this->db->where('mmb.row_order',$row);
		$this->db->where('mmb.kode',$kode);
		$this->db->order_by('mmb.row_order','asc');
		$this->db->SELECT('mmb.*, q.nama as nama_quality');
		$this->db->FROM('mrp_manual_batch mmb');
		$this->db->JOIN("mst_quality as q", "mmb.id_quality = q.id",'LEFT');
		$result = $this->db->get();
		return $result;
	}

	function delete_data_barcode_manual_batch($kode,$row)
	{
		try{
			$data = array('kode' =>$kode, 'row_order'=>$row);
            $this->db->delete('mrp_manual_batch', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}

	function update_data_barcode_manual_batch($data_update, $kode = null )
	{
		$this->db->where('kode', $kode);
		$this->db->update_batch('mrp_manual_batch', $data_update, 'row_order');
        return $this->db->affected_rows();
	}

	function insert_data_barcode_manual_batch_items($data)
	{
		try{
			$this->db->insert_batch('mrp_manual_batch_items', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }

	}

	function insert_data_stock_quant_barcode_manual($data)
    {   
		try{
			$this->db->insert_batch('stock_quant', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

	function insert_data_adj_barcode_manual($data)
	{
		try{
			$this->db->insert_batch('adjustment', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}

	function insert_data_adj_items_barcode_manual($data)
	{
		try{
			$this->db->insert_batch('adjustment_items', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}

	function insert_data_stock_move_barcode_manual($table,$data)
	{
		try{
			$this->db->insert_batch($table, $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}

	function get_data_print_by_kode($kode,$quant_id)
	{
		$this->db->WHERE('mbi.kode',$kode);
		$this->db->WHERE('mbi.quant_id',$quant_id);
		$this->db->SELECT('mbi.kode, mbi.tanggal_buat, mbi.lot, mbi.corak_remark, mbi.warna_remark, mbi.qty, mbi.uom, mbi.qty2, mbi.uom2, mbi.qty_jual, mbi.uom_jual, mbi.qty2_jual, mbi.uom2_jual, mbi.lebar_jadi, mbi.uom_lebar_jadi, mb.kode_k3l ');
		$this->db->FROM("mrp_manual_batch mb");
		$this->db->JOIN("mrp_manual_batch_items mbi","mb.kode = mb.kode AND mb.no_batch = mbi.no_batch AND mb.kode_produk = mbi.kode_produk"  ,"INNER" );
		$query = $this->db->get();
        return $query->row();
	}

    public function __destruct() {
        $this->db->db_debug = $this->db_debug;
    }
}