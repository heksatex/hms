<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_inlet extends CI_Model
{   
    var $table        = 'mrp_inlet';
    var $column_order = array(null, 'lot', 'tanggal', 'kode_mrp', 'nama_sales_group','nama_produk', 'corak_remark', 'warna_remark','lebar_jadi', 'desain_barcode', 'nama_status',null);
	var $column_search= array('lot', 'tanggal', 'kode_mrp', 'nama_sales_group','nama_produk', 'corak_remark', 'warna_remark','lebar_jadi', 'desain_barcode','nama_status');
	var $order  	  = array('tanggal' => 'desc');
	
	private function _get_datatables_query()
	{
		if($this->input->post('lot')){
    		$this->db->like('in.lot',$this->input->post('lot'));
        }
		if($this->input->post('sales_group')){
			$this->db->where('in.sales_group',$this->input->post('sales_group'));
		}
		if($this->input->post('mg')){
			$this->db->like('in.kode_mrp',$this->input->post('mg'));
		}
		if($this->input->post('nama_produk')){
    		$this->db->like('in.nama_produk',$this->input->post('nama_produk'));
        }
		if($this->input->post('corak_remark')){
    		$this->db->like('in.corak_remark',$this->input->post('corak_remark'));
        }
		if($this->input->post('warna_remark')){
    		$this->db->like('in.warna_remark',$this->input->post('warna_remark'));
        }
		if($this->input->post('status')){
    		$this->db->where('in.status',$this->input->post('status'));
        }

		// $this->db->from($this->table);
		$this->db->SELECT("in.id, in.lot, in.tanggal, in.kode_mrp, in.sales_group, in.nama_produk, in.corak_remark, in.warna_remark, in.lebar_jadi,in.uom_lebar_jadi, in.desain_barcode, ms.nama_status, msg.nama_sales_group, in.status");
		$this->db->FROM("mrp_inlet in");
		$this->db->JOIN("mst_status ms","ms.kode = in.status", "LEFT");
		$this->db->JOIN("mst_sales_group msg","msg.kode_sales_group = in.sales_group", "LEFT");

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
		// $this->db->from($this->table);
		$this->db->SELECT("in.lot, in.tanggal, in.kode_mrp, in.origin, in.nama_produk, in.corak_remark, in.warna_remark, in.lebar_jadi, in.desain_barcode, ms.nama_status");
		$this->db->FROM("mrp_inlet in");
		$this->db->JOIN("mst_status ms","ms.kode = in.status", "LEFT");
		return $this->db->count_all_results();
	} 

    function get_data_lot()
    {
        $this->db->where("sq.lokasi","GJD/Stock");
        $this->db->SELECT("mrps.kode, sq.quant_id, sq.kode_produk, sq.nama_produk, sq.lot, mp.id_jenis_kain, mjk.nama_jenis_kain, w.nama_warna, mp.lebar_jadi, mp.uom_lebar_jadi, mrps.origin, mrps.gramasi, sm.method, mrps.sales_group, mrps.nama_sales_group, mp.id_category, sq.lokasi_fisik, mrps.origin_prod");
        $this->db->FROM("stock_quant sq");
        $this->db->JOIN("mst_produk mp","sq.kode_produk = mp.kode_produk", "INNER");
        $this->db->JOIN("mst_jenis_kain mjk","mp.id_jenis_kain = mjk.id", "LEFT");
        $this->db->JOIN("(SELECT mrp.kode, mrp.origin, mrp.gramasi, rmt.move_id, mrp.id_warna, sc.sales_group, msg.nama_sales_group, rmt.origin_prod
                            FROM mrp_production mrp
                            INNER JOIN mrp_production_rm_target rmt ON mrp.kode = rmt.kode
							LEFT JOIN sales_contract sc ON SUBSTRING_INDEX(mrp.origin,'|',1) = sc.sales_order
							LEFT JOIN mst_sales_group msg ON sc.sales_group = msg.kode_sales_group
                            WHERE mrp.dept_id = 'GJD') as mrps ","ON sq.reserve_move = mrps.move_id", "LEFT");
        $this->db->JOIN("stock_move sm","mrps.move_id = sm.move_id","LEFT");
        $this->db->JOIN("warna w","mrps.id_warna = w.id","LEFT");
       
    }

    function get_data_by_lot($lot)
    {   
        $this->get_data_lot();
        $this->db->where("sq.lot",$lot);
        $query = $this->db->get();
        return $query->row();
    }

    function get_count_data_data_by_lot($lot)
    {
        $this->get_data_lot();
        $this->db->where("sq.lot",$lot);
        $query = $this->db->get();
        return $query->num_rows();
    }

    function get_data_inlet_by_lot($lot)
    {
        $this->db->where('lot',$lot);
        return $this->db->get('mrp_inlet');
    }


	function get_data_inlet_by_lot_status($lot,$status)
    {
        $this->db->where_in('status',$status);
        $this->db->where('lot',$lot);
        return $this->db->get('mrp_inlet');
    }

	function get_data_inlet_by_id($id)
    {
        $this->db->where('in.id',$id);
		$this->db->SELECT('in.id, in.quant_id,  in.lot, in.kode_mrp, in.tanggal, in.sales_group, in.kode_produk, in.nama_produk, in.corak_remark,in.warna_remark, in.mc_id, in.operator, in.benang, in.id_quality, mq.nama as nama_quality, in.desain_barcode, in.kode_k3l, mkk.jenis_kain as nama_k3l, in.lebar_jadi, in.uom_lebar_jadi, in.status, mjk.nama_jenis_kain, m.nama_mesin, w.nama_warna, in.berat, in.gramasi, in.id_jenis_kain, in.status, msg.nama_sales_group, ms.nama_status, in.status, in.qty,in.uom, in.qty2,in.uom2');
		$this->db->FROM('mrp_inlet in');
		$this->db->JOIN("mrp_production mrp", "in.kode_mrp = mrp.kode", "LEFT");
		$this->db->JOIN("warna w", "mrp.id_warna = w.id", "LEFT");
		$this->db->JOIN("mesin m", "m.mc_id = in.mc_id","LEFT");
		$this->db->JOIN("mst_quality mq", "mq.id = in.id_quality","LEFT");
		$this->db->JOIN("mst_jenis_kain mjk","mjk.id = in.id_jenis_kain", "LEFT" );
		$this->db->JOIN("mst_kode_k3l mkk","mkk.kode = in.kode_k3l", "LEFT" );
		$this->db->JOIN("mst_sales_group msg","msg.kode_sales_group = in.sales_group", "LEFT" );
		$this->db->JOIN("mst_status ms","ms.kode = in.status", "LEFT" );
		$result = $this->db->get();
		return $result->row();
    }

    function save_data_inlet($data_inlet)
    {
        $this->db->insert('mrp_inlet', $data_inlet);
    }

	function update_data_inlet($id,$data_inlet)
	{
		$this->db->where('id', $id);
		$this->db->update('mrp_inlet', $data_inlet);
	}

	function delete_data_inlet($id)
	{
		$this->db->where('id',$id);
		$this->db->delete('mrp_inlet');
	}

    public function cek_valid_barcode_by_dept($lot)
	{
        $this->db->where('lot', $lot);
        $this->db->where('lokasi', 'GJD/Stock');
        return $this->db->get('stock_quant');
    }
	
	function cek_status_inlet_by_id($id)
	{
		$this->db->where('id',$id);
		$this->db->SELECT('status,lot,kode_mrp,quant_id');
		$this->db->FROM("mrp_inlet");
		$result = $this->db->get();
		return $result->row();
	}

	public function get_last_id_mrp_inlet_id() 
	{
        $last_no = $this->db->query("SELECT max(id) as nom FROM mrp_inlet");
		$result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
	}


	public function get_list_status_by_menu($sub_menu,$dept_id)
	{
		$this->db->WHERE("mms.inisial_class",$sub_menu);
		$this->db->WHERE("mms.dept_id",$dept_id);
		$this->db->SELECT('mmss.jenis_status, mmss.nama_status');
		$this->db->FROM("main_menu_sub_status mmss");
		$this->db->JOIN("main_menu_sub mms","mmss.main_menu_sub_kode = mms.kode","INNER");
		$this->db->ORDER_BY("mmss.row_order","ASC");
		$query = $this->db->get();
		return $query->result();
	}

	function cek_mrp_rm_hasil_by_lot($kode_mrp,$lot)
	{
		$this->db->where("kode",$kode_mrp);
		$this->db->where("lot",$lot);
		return $this->db->get('mrp_production_rm_hasil');
	}

	public function update_data_lokasi_fisik_lot($quant_id, $data_update, $where_smi)
	{
		$this->db->where('quant_id', $quant_id);
		$this->db->update('stock_quant', $data_update);
		$this->db->where($where_smi);
		$this->db->update('stock_move_items', $data_update);
	}


	// var $table        = 'mrp_inlet';
    var $column_order2 = array(null,  'fg.create_date', 'fg.kode_produk', 'fg.nama_produk','sq.corak_remark', 'sq.warna_remark', 'fg.lot','sq.nama_grade', 'fg.qty', 'fg.qty2','sq.qty_jual', 'sq.qty2_jual', 'sq.lebar_jadi',   'sq.lokasi', 'fg.nama_user');
	var $column_search2= array('fg.lot', 'fg.create_date', 'sq.corak_remark', 'sq.warna_remark', 'fg.qty', 'fg.qty2','sq.qty_jual', 'sq.qty2_jual', 'sq.lebar_jadi',  'sq.nama_grade', 'fg.nama_user','sq.lokasi');
	var $order2  	  = array('sq.create_date' => 'desc');
	
	private function _get_datatables_query2()
	{
	
		$this->db->SELECT("in.id, sq.quant_id, fg.create_date, fg.kode_produk, fg.nama_produk, fg.lot, fg.nama_grade, fg.qty, fg.uom, fg.qty2, fg.uom2, fg.nama_user,sq.qty_jual,sq.uom_jual, sq.qty2_jual, sq.uom2_jual, sq.lokasi, sq.lebar_jadi, sq.uom_lebar_jadi, sq.corak_remark, sq.warna_remark ");
		$this->db->FROM("mrp_inlet in");
		$this->db->JOIN("mrp_production_fg_hasil fg","fg.kode = in.kode_mrp AND in.id = fg.id_inlet" , "INNER");
		$this->db->JOIN("stock_quant sq","sq.quant_id = fg.quant_id", "INNER");

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

	function get_datatables2($id)
	{
		$this->db->where('in.id',$id);
		$this->_get_datatables_query2();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2($id)
	{
		$this->db->where('in.id',$id);
		$this->_get_datatables_query2();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($id)
	{
		// $this->db->from($this->table);
		$this->db->where('in.id',$id);
		$this->db->SELECT("in.id, fg.kode_produk, fg.nama_produk, fg.lot, fg.nama_grade, fg.qty, fg.uom, fg.qty2, fg.uom2, fg.nama_user, sq.lokasi, sq.lebar_jadi, sq.uom_lebar_jadi, sq.corak_remark, sq.warna_remark ");
		$this->db->FROM("mrp_inlet in");
		$this->db->JOIN("mrp_production_fg_hasil fg","fg.kode = in.kode_mrp AND in.id = fg.id_inlet" , "INNER");
		$this->db->JOIN("stock_quant sq","sq.quant_id = fg.quant_id", "INNER");
		return $this->db->count_all_results();
	} 


	function get_data_lot_hph_by_kode($id_inlet,$quant_id)
	{
		$this->db->where('in.id',$id_inlet);
		$this->db->where('sq.quant_id',$quant_id);
		$this->db->SELECT("in.id, in.kode_mrp, fg.quant_id, fg.kode_produk, fg.nama_produk, fg.lot, fg.nama_grade, fg.qty, fg.uom, fg.qty2, fg.uom2, fg.nama_user, sq.lokasi, sq.lebar_jadi, sq.uom_lebar_jadi, sq.corak_remark, sq.warna_remark, sq.qty_jual,sq.uom_jual, sq.qty2_jual, sq.uom2_jual, sq.lokasi, sq.lebar_jadi, sq.uom_lebar_jadi");
		$this->db->FROM("mrp_inlet in");
		$this->db->JOIN("mrp_production_fg_hasil fg","fg.kode = in.kode_mrp AND in.id = fg.id_inlet" , "INNER");
		$this->db->JOIN("stock_quant sq","sq.quant_id = fg.quant_id", "INNER");
		$result = $this->db->get();
		return $result->row();
	}

	function get_list_uom_by_lot($kode_mrp,$quant_id)
	{
		$this->db->where('kode',$kode_mrp);
		$this->db->where('quant_id',$quant_id);
		$this->db->order_by('row_order','asc');
		$query = $this->db->get('mrp_satuan');
		return $query->result();
		
	}

	public function get_list_uom_select2_by_prod($name)
	{
		return $this->db->query("SELECT id, nama, nama, short
								FROM  uom 
								WHERE short LIKE '%$name%' ORDER BY id   ")->result_array();
	}

	function cek_barcode_in_picklist($quant_id,$lot)
	{
		$this->db->where('quant_id',$quant_id);
		$this->db->where('barcode_id',$lot);
		$query = $this->db->get('picklist_detail');
		return $query;
		
	}

	function update_date_stock_quant($data_update,$quant_id)
	{
		try{
			$this->db->where('quant_id', $quant_id);
			$this->db->update('stock_quant', $data_update);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}

	function update_data_mrp_fg_hasil($data_update,$kode,$quant_id)
	{
		try{
			$this->db->where('quant_id', $quant_id);
			$this->db->where('kode', $kode);
			$this->db->update('mrp_production_fg_hasil', $data_update);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}

	function update_data_stock_move_items($data_update,$quant_id)
	{
		try{
			$this->db->where('quant_id', $quant_id);
			$this->db->update('stock_move_items', $data_update);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}

	public function get_row_order_mrp_satuan_by_kode($kode,$quant_id) {
        $last_no = $this->db->query("SELECT max(row_order) as nom FROM mrp_satuan where kode = '$kode' AND quant_id = $quant_id");
        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
    }

	function update_data_mrp_satuan_batch($data_update,$data_where)
	{
		try{
			$this->db->where($data_where);
			$this->db->update_batch('mrp_satuan', $data_update,'uom');
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}


	function save_mrp_satuan_batch($data_satuan)
    {   
        $this->db->insert_batch('mrp_satuan', $data_satuan);
		$db_error = $this->db->error();
        return is_array($db_error['code']);
    }

	function get_total_hph_by_lot($id)
	{
		$query = $this->db->query("SELECT inm.qty, inm.qty2,  
				(SELECT sum(fg.qty) as total_qty FROM mrp_production_fg_hasil fg WHERE inm.kode_mrp =  fg.kode AND inm.id = fg.id_inlet) as hasil_qty,
				(SELECT sum(fg.qty2) as total_qty2 FROM mrp_production_fg_hasil fg WHERE inm.kode_mrp =  fg.kode AND inm.id = fg.id_inlet) as hasil_qty2,
				(SELECT sum(smi.qty) as total_qty FROM mrp_production_rm_target rm 
					INNER JOIN stock_move_items smi ON smi.move_id = rm.move_id AND smi.status = 'ready'
					WHERE smi.quant_id = inm.quant_id AND inm.kode_mrp = rm.kode AND inm.status != 'cancel') as qty_ready,
				(SELECT sum(smi.qty2) as total_qty FROM mrp_production_rm_target rm 
					INNER JOIN stock_move_items smi ON smi.move_id = rm.move_id AND smi.status = 'ready'
					WHERE smi.quant_id = inm.quant_id AND inm.kode_mrp = rm.kode AND inm.status != 'cancel' ) as qty2_ready
				FROM mrp_inlet inm
				WHERE inm.id = '$id'");
		return $query->row();
	}

	function get_total_hph_by_grade($id)
	{
		$query = $this->db->query("SELECT nama_grade, sum(qty) as total_qty, sum(qty2) as total_qty2, count(lot) as total_pcs
									FROM mrp_production_fg_hasil 
									WHERE id_inlet = '$id'
									GROUP BY nama_grade");
		return $query->result();
	}


}