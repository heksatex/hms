<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_koreksi extends CI_Model
{ 
	var $column_order = array(null, 'kode_koreksi', 'tanggal_dibuat', 'tanggal_transaksi','note', 'note');
	var $column_search= array( 'kode_koreksi', 'tanggal_dibuat', 'tanggal_transaksi','note', 'note');
	var $order  	  = array('tanggal_dibuat' => 'desc');

    
	public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
	}


    private function _get_datatables_query()
	{

		$this->db->select("km.kode_koreksi, km.tanggal_dibuat, km.tanggal_transaksi, km.note, km.nama_user, km.status,  mmss.nama_status");
		$this->db->from("koreksi_mundur km");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=km.status", "inner");

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
		if(isset($_POST["length"]) && $_POST["length"] != -1)
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
		$this->db->select("km.kode_koreksi, km.tanggal_dibuat, km.tanggal_transaksi, km.note, km.nama_user, km.status,  mmss.nama_status");
		$this->db->from("koreksi_mundur km");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=km.status", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		return $this->db->count_all_results();
	}

	public function get_kode_koreksi() {
        $result = $this->db->query("SELECT kode_koreksi FROM koreksi_mundur WHERE month(tanggal_dibuat)='" . date("m") . "' AND year(tanggal_dibuat)='" . date("Y") . "' ORDER BY RIGHT(kode_koreksi,4) DESC LIMIT 1");
        if ($result->num_rows() > 0) {
            $row = $result->row();
            $dgt = substr($row->kode_koreksi, -4) + 1;
        } else {
            $dgt = "1";
        }
        return $dgt;
    }


    function get_data_koreksi_by_kode($kode)
    {
        $this->db->where('kode_koreksi',$kode);
        $query = $this->db->get('koreksi_mundur');
        return $query->row();
    }

    function save_data_koreksi($data_koreksi)
    {
        $this->db->insert('koreksi_mundur', $data_koreksi);
    }

	function update_data_koreksi($kode_koreksi,$data_koreksi)
	{
		$this->db->where('kode_koreksi', $kode_koreksi);
		$this->db->update('koreksi_mundur', $data_koreksi);
		return $this->db->affected_rows();

	}

	function update_data_koreksi_mutasi($kode_koreksi,$id,$data_koreksi)
	{
		$this->db->where('kode_koreksi', $kode_koreksi);
		$this->db->where('id', $id);
		$this->db->update('koreksi_mutasi', $data_koreksi);
		return $this->db->affected_rows();
	}

	public function get_list_departement($params) {
        return $this->db->query("SELECT kode,nama FROM departemen WHERE show_dept = 'true' AND nama LIKE '%".$params."%' ORDER BY nama  ")->result();
    }

	function get_list_mrp_production($departemen,$params)
	{
		$this->db->like('kode',$params);
		$this->db->where('dept_id',$departemen);
		$this->db->select('kode');
		$this->db->from('mrp_production');
		$this->db->limit(100);
		$query = $this->db->get();
		return $query->result();
	}

	function get_list_pengiriman_barang($departemen,$params)
	{
		$this->db->like('kode',$params);
		$this->db->where('dept_id',$departemen);
		$this->db->where('status','done');
		$this->db->select('kode');
		$this->db->from('pengiriman_barang');
		$this->db->limit(100);
		$query = $this->db->get();
		return $query->result();
	}

	function get_list_penerimaan_barang($departemen,$params)
	{
		$this->db->like('kode',$params);
		$this->db->where('dept_id',$departemen);
		$this->db->where('status','done');
		$this->db->select('kode');
		$this->db->from('penerimaan_barang');
		$this->db->limit(100);
		$query = $this->db->get();
		return $query->result();
	}
	

	var $column_order2  = array(null, 'smi.kode_produk', 'smi.nama_produk', 'smi.lot', 'smi.qty', 'smi.qty2', 'sq.nama_grade', 'sq.reff_note', 'smi.status');
	var $column_search2 = array('smi.kode_produk', 'smi.nama_produk', 'smi.lot', 'smi.qty', 'smi.qty2', 'sq.nama_grade', 'sq.reff_note', 'smi.status');
	var $order2  	    = array('smi.tanggal_transaksi' => 'asc');


	function get_query($koreksi_apa,$tipe)
	{
		if($koreksi_apa == 'mo'){
			if($tipe == 'con'){
				$this->db->select('a.kode, a.dept_id, rm.nama_produk as target_produk, rm.qty as target_qty, 
					smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, sq.nama_grade, sq.reff_note, sq.quant_id, smi.move_id');
				$this->db->from('mrp_production a');
				$this->db->join('mrp_production_rm_target as rm ', "a.kode = rm.kode", "inner");
				$this->db->join('stock_move_items as smi', "rm.move_id = smi.move_id AND rm.origin_prod = smi.origin_prod","inner");
				$this->db->join('stock_quant as sq', "smi.quant_id = sq.quant_id", "inner");
			}else{
				$this->db->select('a.kode, a.dept_id, fg.nama_produk as target_produk, fg.qty as target_qty, 
					smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, sq.nama_grade, sq.reff_note, sq.quant_id, smi.move_id');
				$this->db->from('mrp_production a');
				$this->db->join('mrp_production_fg_target as fg ', "a.kode = fg.kode", "inner");
				// $this->db->join('mrp_production_fg_hasil fgh','a.kode = fgh.kode','inner');
				$this->db->join('stock_move_items as smi', "fg.move_id = smi.move_id","inner");
				$this->db->join('stock_quant as sq', "smi.quant_id = sq.quant_id", "inner");
				// $this->db->where_in('sq.nama_grade', array('A','B','C'));
				$this->db->not_like('sq.lokasi', 'waste');
			}
		}else if($koreksi_apa == 'out'){

			$this->db->select('a.kode, a.dept_id, pbi.nama_produk as target_produk, pbi.qty as target_qty, 
				smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, sq.nama_grade, sq.reff_note, sq.quant_id, smi.move_id');
			$this->db->from('pengiriman_barang a');
			$this->db->join('pengiriman_barang_items as pbi ', "a.kode = pbi.kode", "inner");
			$this->db->join('stock_move_items as smi', "a.move_id = smi.move_id","inner");
			$this->db->join('stock_quant as sq', "smi.quant_id = sq.quant_id", "inner");
			$this->db->where('a.status','done');
		}else{

			$this->db->select('a.kode, a.dept_id, pbi.nama_produk as target_produk, pbi.qty as target_qty, 
				smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, sq.nama_grade, sq.reff_note, sq.quant_id, smi.move_id');
			$this->db->from('penerimaan_barang a');
			$this->db->join('penerimaan_barang_items as pbi ', "a.kode = pbi.kode", "inner");
			$this->db->join('stock_move_items as smi', "a.move_id = smi.move_id","inner");
			$this->db->join('stock_quant as sq', "smi.quant_id = sq.quant_id", "inner");
			$this->db->where('a.status','done');

		}

		return $this;
	}


	private function _get_datatables2_query($koreksi_apa,$tipe)
	{
		$this->get_query($koreksi_apa,$tipe);
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

	
    function where(array $condition) {
        foreach ($condition as $key => $value) {
            if (is_array($value)) {
                $this->db->where($key, [$value]);
            } else {
                $this->db->where($key, $value);
            }
        }
        return $this;
    }

	function get_datatables2(array $condition,$koreksi_apa,$tipe)
	{
		$this->_get_datatables2_query($koreksi_apa,$tipe);
		if(!empty($condition)){
			$this->where($condition);
		}
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2(array $condition,$koreksi_apa,$tipe)
	{
		if(!empty($condition)){
			$this->where($condition);
		}
		$this->_get_datatables2_query($koreksi_apa,$tipe);
		$query = $this->db->get();
		return $query->num_rows();
	}
	

	public function count_all2(array $condition,$koreksi_apa,$tipe)
	{
		if(!empty($condition)){
			$this->where($condition);
		}
		$this->get_query($koreksi_apa,$tipe);
		return $this->db->count_all_results();
	}

	var $column_order3  = array(null,'kmb.no_batch','kmb.koreksi','d.nama','kmb.kode_transaksi','kmb.tipe','kmb.kode_produk', 'kmb.nama_produk', 'kmb.koreksi_qty1', 'kmb.koreksi_qty2', 'kmb.status');
	var $column_search3 = array('kmb.no_batch','kmb.koreksi','d.nama','kmb.kode_transaksi','kmb.tipe','kmb.kode_produk', 'kmb.nama_produk', 'kmb.koreksi_qty1', 'kmb.koreksi_qty2', 'kmb.status');
	var $order3  	    = array('kmb.row_order' => 'asc');


	function get_query3()
	{
		$this->db->select('km.kode_koreksi, kmb.no_batch, kmb.koreksi, kmb.tipe, kmb.dept_id, kmb.kode_transaksi, kmb.kode_produk, kmb.nama_produk, kmb.koreksi_qty1, kmb.koreksi_qty2, kmb.status, kmb.row_order, d.nama as nama_departemen, kmb.koreksi_lebih_kurang');
		$this->db->from('koreksi_mundur km');
		$this->db->join('koreksi_mundur_batch as kmb ', "km.kode_koreksi = kmb.kode_koreksi", "inner");
		$this->db->join('departemen as d', "d.kode = kmb.dept_id","left");
	}

	private function _get_datatables3_query()
	{
		$this->get_query3();
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


	function get_datatables3(array $condition)
	{
		$this->_get_datatables3_query();
		if(!empty($condition)){
			$this->where($condition);
		}
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered3(array $condition)
	{
		if(!empty($condition)){
			$this->where($condition);
		}
		$this->_get_datatables3_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all3(array $condition)
	{
		if(!empty($condition)){
			$this->where($condition);
		}
		$this->get_query3();
		return $this->db->count_all_results();
	}

	var $column_order4  = array(null,'no_batch','kode_produk','nama_produk','grade','lot','qty','qty2','qty_move','qty2_move');
	var $column_search4 = array('no_batch','kode_produk','nama_produk','grade','lot','qty','qty2','qty_move','qty2_move');
	var $order4  	    = array('no_batch' => 'asc','row_order' => 'asc');
	var $table          = 'koreksi_mundur_batch_items';

	private function _get_datatables4_query()
	{
		// $this->get_query3();
		$this->db->from($this->table);
		$i = 0;
	
		foreach ($this->column_search4 as $item) // loop column 
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

				if(count($this->column_search4) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order4[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order4))
		{
			$order = $this->order4;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}


	function get_datatables4(array $condition)
	{
		$this->_get_datatables4_query();
		if(!empty($condition)){
			$this->where($condition);
		}
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered4(array $condition)
	{
		if(!empty($condition)){
			$this->where($condition);
		}
		$this->db->from($this->table);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all4(array $condition)
	{
		if(!empty($condition)){
			$this->where($condition);
		}
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}


	var $column_order5  = array(null,'d.nama','kmut.tahun','kmut.bln','kmut.no_batch','kmut.tanggal_proses_mutasi','kmut.status');
	var $column_search5 = array('d.nama','kmut.tahun','kmut.bln','kmut.no_batch','kmut.tanggal_proses_mutasi','kmut.status');
	var $order5  	    = array('kmut.tahun asc', 'kmut.bln asc');


	function get_query5()
	{
		$this->db->select('km.kode_koreksi, kmut.no_batch,kmut.bln, kmut.tahun,kmut.no_batch, kmut.tanggal_proses_mutasi,  kmut.dept_id, kmut.status, kmut.id, d.nama as nama_departemen');
		$this->db->from('koreksi_mundur km');
		$this->db->join('koreksi_mutasi as kmut ', "km.kode_koreksi = kmut.kode_koreksi", "inner");
		$this->db->join('departemen as d', "d.kode = kmut.dept_id","left");
	}

	private function _get_datatables5_query()
	{
		$this->get_query5();
		$i = 0;
	
		foreach ($this->column_search5 as $item) // loop column 
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

				if(count($this->column_search5) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order5[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order5))
		{
			$order = $this->order5;
			foreach($order as $value){
				 $this->db->order_by($value);
				// $this->db->order_by(key($order), $order[key($order)]);
			}
		}
	}


	function get_datatables5(array $condition)
	{
		$this->_get_datatables5_query();
		if(!empty($condition)){
			$this->where($condition);
		}
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered5(array $condition)
	{
		if(!empty($condition)){
			$this->where($condition);
		}
		$this->_get_datatables5_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all5(array $condition)
	{
		if(!empty($condition)){
			$this->where($condition);
		}
		$this->get_query5();
		return $this->db->count_all_results();
	}

	public function get_stock_move_items_by_kode($move_id,$quant_id)
	{
		$this->db->where('smi.move_id',$move_id);
		$this->db->where('smi.quant_id',$quant_id);
		$this->db->select('smi.move_id, smi.quant_id, smi.tanggal_transaksi, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, sq.nama_grade, smi.lot');
		$this->db->from('stock_move_items as smi');
		$this->db->join('stock_quant as sq',"smi.quant_id = sq.quant_id","inner");
		$result = $this->db->get();
		return $result->row();
	}

	public function get_row_order_by_kode($kode_koreksi)
	{
		$this->db->where('kode_koreksi',$kode_koreksi);
		$this->db->select('max(row_order) as nom');
		$this->db->from('koreksi_mundur_batch');
		$this->db->order_by('row_order', 'asc');
		$last_no = $this->db->get();

	    $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
	}


	function insert_data_batch($data)
    {   
		try{
			$this->db->insert_batch('koreksi_mundur_batch', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
    }


	function insert_data_batch_items($data)
    {   
		try{
			$this->db->insert_batch('koreksi_mundur_batch_items', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

	function cek_lot_quant_input_by_kode($kode_koreksi,$quant_id,$move_id)
	{
		$this->db->where('quant_id', $quant_id);
		$this->db->where('kode_koreksi', $kode_koreksi);
		$this->db->where('move_id', $move_id);
		$result = $this->db->get('koreksi_mundur_batch_items');
		return $result->num_rows();
	}

	function get_data_koreksi_batch_by_kode($kode_koreksi,$batch,$row)
	{
		$this->db->where('kode_koreksi',$kode_koreksi);
		$this->db->where('no_batch',$batch);
		$this->db->where('row_order',$row);
		$this->db->select('kmb.*, d.nama as nama_departemen');
		$this->db->from('koreksi_mundur_batch as kmb');
		$this->db->join("departemen as d", "kmb.dept_id = d.kode", "left");
		$result = $this->db->get();
		return $result->row();
	}


	function get_data_koreksi_batch_items_by_kode($kode_koreksi,$batch)
	{
		$this->db->where('kode_koreksi',$kode_koreksi);
		$this->db->where('no_batch',$batch);
		$result = $this->db->get('koreksi_mundur_batch_items');
		return $result;
	}

	function get_data_koreksi_batch_items_by_kode_group($kode_koreksi,$batch)
	{
		$this->db->where('km.kode_koreksi',$kode_koreksi);
		$this->db->where('km.no_batch',$batch);
		$this->db->select('km.koreksi_lebih_kurang, kmbs.kode_produk,kmbs.nama_produk, kmbs.lot, sum(kmbs.qty_move) as tot_qty_move, sum(kmbs.qty2_move) as tot_qty2_move');
		$this->db->from('koreksi_mundur_batch km ');
		$this->db->join('koreksi_mundur_batch_items kmbs', 'km.no_batch = kmbs.no_batch','inner');
		$this->db->group_by('kmbs.lot');
		$result = $this->db->get();
		return $result->result();
	}

	
	function delete_data_batch($kode_koreksi,$batch,$row)
	{
		try{
			$data = array('kode_koreksi' =>$kode_koreksi, 'no_batch'=>$batch, 'row_order'=>$row);
            $this->db->delete('koreksi_mundur_batch', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}

	function delete_data_batch_items($kode_koreksi,$batch)
	{
		try{
			$data = array('kode_koreksi' =>$kode_koreksi, 'no_batch'=>$batch,);
            $this->db->delete('koreksi_mundur_batch_items', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}


	function cek_data_koreksi_batch_by_kode($kode_koreksi,$batch)
	{
		$this->db->where('kode_koreksi',$kode_koreksi);
		$this->db->where('no_batch',$batch);
		$this->db->select('kmb.*, (SELECT sum(qty) as total_qty1 FROM koreksi_mundur_batch_items as a WHERE no_batch = kmb.no_batch AND kode_koreksi = kmb.kode_koreksi ) as total_qty1,
(SELECT sum(qty2) as total_qty2 FROM koreksi_mundur_batch_items as b WHERE no_batch = kmb.no_batch AND kode_koreksi = kmb.kode_koreksi ) as total_qty2');
		$this->db->from('koreksi_mundur_batch kmb');
		$result = $this->db->get();
		return $result->row();
	}

	function cek_proses_mutasi_by_kode($kode_koreksi,$id)
	{
		$this->db->where('kode_koreksi',$kode_koreksi);
		$this->db->where('id',$id);
		$result = $this->db->get('koreksi_mutasi');
		return $result->row();
	}


	function cek_acc_stock_move_items($dept_id,$type,$quant_id,$lot,$kode_transaksi)
	{
		$this->db->where('dept_id_mutasi',$dept_id);
		$this->db->where('type',$type);
		$this->db->where('kode_transaksi',$kode_transaksi);
		$this->db->where('lot',$lot);
		$this->db->where('quant_id',$quant_id);
		$result = $this->db->get('acc_stock_move_items');
		return $result->row();
	}


	function update_stock_move_items($data_update)
    {
        $this->db->update_batch("stock_move_items",$data_update,'quant_id');
        return $this->db->affected_rows();
    }

	// function update_acc_stock_move_items($data_update)
    // {
    //     $this->db->update_batch("acc_stock_move_items",$data_update,array('dept_id_mutasi','type','kode_transaksi','quant_id','lot'));
    //     return $this->db->affected_rows();
    // }

	function insert_koreksi_mutasi($data)
	{
 		$this->db->insert_batch('koreksi_mutasi', $data);
	}

	function cek_acc_stock_quant_eom($quant_id,$lokasi)
	{
		$this->db->where('quant_id',$quant_id);
		$this->db->where('lokasi',$lokasi);
		$result = $this->db->get('acc_stock_quant_eom');
		return $result->result();
	}


	function cek_koreksi_mutasi_by_batch($kode_koreksi,$dept_id,$periode_th,$periode_bln,$status)
	{
		$this->db->where('kode_koreksi', $kode_koreksi);
		$this->db->where('dept_id', $dept_id);
		$this->db->where('tahun', $periode_th);
		$this->db->where('bln', $periode_bln);
		$this->db->where('status', $status);
		$result = $this->db->get('koreksi_mutasi');
		return $result->row();
	}

	function update_koreksi_mutasi($data_update)
    {
        $this->db->update_batch("koreksi_mutasi",$data_update,'id');
        return $this->db->affected_rows();
    }

	function update_status_batch($kode,$batch,$status)
	{
		try{
			$data_update = array('status'=>$status);
			$this->db->where('kode_koreksi', $kode);
			// $this->db->where('row_order', $row);
			$this->db->where('no_batch', $batch);
			$this->db->update('koreksi_mundur_batch', $data_update);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
	}


	function update_koreksi_batch_items($data_update,$kode_koreksi,$no_bath)
    {	
		$this->db->where('kode_koreksi',$kode_koreksi);
		$this->db->where('no_batch',$no_bath);
        $this->db->update_batch("koreksi_mundur_batch_items",$data_update,'row_order');
        return $this->db->affected_rows();
    }

	function get_koreksi_batch_by_kode($kode_koreksi,$status)
	{
		if(!empty($status)){
			$this->db->where('status',$status);
		}
		$this->db->where('kode_koreksi',$kode_koreksi);
		$result = $this->db->get('koreksi_mundur_batch');
		return $result->row();
	}

	function get_koreksi_mutasi_by_kode($kode_koreksi,$status)
	{
		$this->db->where('kode_koreksi',$kode_koreksi);
		$this->db->where('status',$status);
		$result = $this->db->get('koreksi_mutasi');
		return $result->row();
	}

	function get_data_eom_by_produk($thn,$bln,$kode_produk,$nama_produk,$lot,$lokasi)
	{
		$this->db->where('year(tanggal)',$thn);
		$this->db->where('month(tanggal)',$bln);
		$this->db->where('kode_produk',$kode_produk);
		$this->db->where('nama_produk',$nama_produk);
		$this->db->where('lot',$lot);
		$this->db->where('lokasi',$lokasi);
		$result = $this->db->get('acc_stock_quant_eom');
		return $result->result();
	}

	function cek_lot_proses_koreksi_mundur($lot,$kode_koreksi)
	{
		$this->db->where('lot',$lot);
		$this->db->where_not_in('km.kode_koreksi',$kode_koreksi);
		$this->db->where_not_in('km.status',array('cancel','done'));
		$this->db->select('km.kode_koreksi, km.status, kmbi.lot');
		$this->db->from('koreksi_mundur km');
		$this->db->join('koreksi_mundur_batch_items kmbi','km.kode_koreksi = kmbi.kode_koreksi','inner');
		$result = $this->db->get();
		return $result->row();
	}

	function cek_lot_update_eom($dept_id,$lot,$thn,$bln,$kode)
	{
		$this->db->where('kmbc.kode_koreksi',$kode);
		$this->db->where('kmbc.dept_id ',$dept_id);
		$this->db->where('kmbic.lot ',$lot);
		$this->db->where('kmc.dept_id ',$dept_id);
		$this->db->where('kmc.tahun ',$thn);
		$this->db->where('kmc.bln ',$bln);
		$this->db->select('kmc.*');
		$this->db->from('koreksi_mundur_batch kmbc');
		$this->db->join('koreksi_mundur_batch_items kmbic','kmbc.kode_koreksi = kmbic.kode_koreksi','inner');
		$this->db->join('koreksi_mutasi kmc',"kmbc.kode_koreksi = kmc.kode_koreksi AND kmc.no_batch LIKE CONCAT('%',kmbc.no_batch,'(',kmbc.koreksi,')','%') ",'inner');
		$this->db->group_by('kmc.id');
		$result = $this->db->get();
		return $result->row();

	}

}