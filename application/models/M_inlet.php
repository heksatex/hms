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


}