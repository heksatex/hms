<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_pengirimanBarang extends CI_Model
{
	
	//var $table 		  = 'pengiriman_barang';
	var $column_order = array(null, 'kode', 'tanggal', 'tanggal_transaksi', 'origin','lokasi_tujuan', 'reff_picking','reff_note', 'nama_status');
	var $column_search= array( 'kode', 'tanggal', 'tanggal_transaksi', 'origin', 'lokasi_tujuan', 'reff_picking','reff_note', 'nama_status');
	var $order  	  = array('kode' => 'desc');

	var $table2  	    = 'stock_kain_greige';
	var $column_order2  = array(null, 'corak', 'barcode_id', 'panjang', 'berat');
	var $column_search2 = array('corak', 'barcode_id', 'panjang', 'berat');
	var $order2  	    = array('barcode_id' => 'asc');
	var $banned         = array('ADJ','GOUT','TRB','TRD','JAC');

	var $table3  	    = 'stock_quant';
	var $column_order3  = array(null, 'kode_produk','nama_produk', 'lot', 'qty', 'qty2', 'nama_grade','lokasi_fisik','reff_note');
	var $column_search3 = array('kode_produk','nama_produk', 'lot', 'qty', 'qty2', 'nama_grade', 'lokasi_fisik','reff_note');
	var $order3  	    = array('create_date' => 'asc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
		$this->load->model('_module');
	}

	private function _get_datatables_query()
	{	
		  //add custom filter here
        if($this->input->post('kode'))
        {
            $this->db->like('kode', $this->input->post('kode'));
        }
        if($this->input->post('status'))
        {
            $this->db->like('status', $this->input->post('status'));
        }
        if($this->input->post('reff'))
        {
            $this->db->like('reff_note', $this->input->post('reff'));
        }
        if($this->input->post('reff_picking'))
        {
            $this->db->like('reff_picking', $this->input->post('reff_picking'));
        }

		//$this->db->from($this->table);
		$this->db->select("pb.kode, pb.tanggal, pb.tanggal_transaksi, pb.tanggal_jt, pb.lokasi_tujuan, pb.reff_picking, pb.status, pb.reff_note, mmss.nama_status, pb.origin");
		$this->db->from("pengiriman_barang pb");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pb.status", "inner");

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

	function get_datatables($id_dept,$mmss)
	{
		$this->_get_datatables_query();
		$this->db->where('dept_id',$id_dept);
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($id_dept,$mmss)
	{
		$this->db->where('dept_id',$id_dept);
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($id_dept,$mmss)
	{	
		//$this->db->from($this->table);
		$this->db->select("pb.kode, pb.tanggal, pb.tanggal_transaksi, pb.tanggal_jt, pb.lokasi_tujuan, pb.reff_picking, pb.status, pb.reff_note, mmss.nama_status");
		$this->db->from("pengiriman_barang pb");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=pb.status", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->db->where('dept_id',$id_dept);
		return $this->db->count_all_results();
	}


	public function get_data_by_code($kode)
	{
		$query = $this->db->query("SELECT * FROM pengiriman_barang where kode = '".$kode."' ");
		return $query->row();
	}

	public function get_data_by_code_print($kode,$dept_id)
	{
		$query = $this->db->query("SELECT * FROM pengiriman_barang where kode = '".$kode."' AND dept_id = '".$dept_id."' ");
		return $query->row();
	}


	public function get_list_pengiriman_barang($kode)
	{
		return $this->db->query("SELECT pbi.lot,pbi.nama_produk, pbi.qty, pbi.kode_produk, pbi.nama_produk, pbi.uom, pbi.qty, pbi.status_barang, pbi.origin_prod, pbi.row_order,
								(SELECT IFNULL(sum(smi.qty),'') FROM stock_move_items smi 	WHERE  smi.move_id = pb.move_id And smi.kode_produk = pbi.kode_produk AND smi.origin_prod = pbi.origin_prod ) as sum_qty
								FROM pengiriman_barang_items pbi
							    INNER JOIN pengiriman_barang pb ON pbi.kode = pb.kode
								WHERE pbi.kode = '$kode' ORDER BY row_order")->result();
	}

	public function get_list_pengiriman_barang_print($kode,$dept_id)
	{
		return $this->db->query("SELECT pbi.lot,pbi.nama_produk, pbi.qty, pbi.kode_produk, pbi.nama_produk, pbi.uom, pbi.qty, pbi.status_barang, pbi.origin_prod, pbi.row_order,
								(SELECT sum(smi.qty) FROM stock_move_items smi 	WHERE  smi.move_id = pb.move_id And smi.kode_produk = pbi.kode_produk AND smi.origin_prod = pbi.origin_prod ) as sum_qty
								FROM pengiriman_barang_items pbi
							    INNER JOIN pengiriman_barang pb ON pbi.kode = pb.kode
								WHERE pbi.kode = '$kode' AND pb.dept_id = '$dept_id' ORDER BY row_order")->result();
	}

	public function get_stock_move_by_kode($kode)
	{
		return $this->db->query("SELECT sm.move_id, sm.create_date,sm.origin, sm.method, sm.lokasi_dari, sm.lokasi_tujuan, sm.status
								 FROM stock_move sm
								 INNER JOIN pengiriman_barang pb ON sm.move_id = pb.move_id 
								 WHERE pb.kode = '$kode' ");
	}

	public function get_data_stock_by_prod($nama_produk)
	{
		return $this->db->query("SELECT corak, barcode_id, panjang, berat, sat_brt  FROM stock_kain_greige WHERE corak LIKE '%".$nama_produk."%' ORDER BY barcode_id ")->result();
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

	function get_datatables2($corak)
	{
		$this->_get_datatables2_query();
		$this->db->like('corak',$corak );
		$this->db->where_not_in('kode_lokasi', $this->banned);
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2($corak)
	{
		$this->db->like('corak',$corak );
		$this->db->where_not_in('kode_lokasi', $this->banned);
		$this->_get_datatables2_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($corak)
	{
		$this->db->like('corak',$corak );
		$this->db->where_not_in('kode_lokasi', $this->banned);
		$this->db->from($this->table2);
		return $this->db->count_all_results();
	}

	private function _get_datatables3_query()
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

	function get_datatables3($kode,$kode_produk,$lokasi,$origin,$dept_id)
	{

		$this->_get_datatables3_query();
		$this->db->where('kode_produk',$kode_produk );
		$this->db->where('lokasi',$lokasi );
		$this->db->where('reserve_move','');
		//cek type departement
		$cek_dept = $this->_module->cek_departement_by_kode($dept_id)->row_array();
		$cek_type = $this->cek_type_created($kode)->row_array();
		if($cek_dept['type_dept'] == 'manufaktur' AND $cek_type['type_created'] == 0){
			//$this->db->where('reserve_origin',$origin);	
			$where = "( reserve_origin='".$origin."' OR reserve_origin LIKE '%MTS%' )";
			$this->db->where($where);
		}
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function cek_type_created($kode)
	{
		return $this->db->query("SELECT type_created FROM pengiriman_barang Where kode = '$kode'");
	}


	function count_filtered3($kode,$kode_produk,$lokasi,$origin,$dept_id)
	{
		$this->db->where('kode_produk',$kode_produk );
		$this->db->where('lokasi',$lokasi );
		$this->db->where('reserve_move','');
		//cek type departement	
		$cek_dept = $this->_module->cek_departement_by_kode($dept_id)->row_array();
		$cek_type = $this->cek_type_created($kode)->row_array();
		if($cek_dept['type_dept'] == 'manufaktur' AND $cek_type['type_created'] == 0){
			$this->db->where('reserve_origin',$origin);	
		}		
		$this->_get_datatables3_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all3($kode,$kode_produk,$lokasi,$origin,$dept_id)
	{
		$this->db->where('kode_produk',$kode_produk );
		$this->db->where('lokasi',$lokasi );
		$this->db->where('reserve_move','');
		//cek type departement
		$cek_dept = $this->_module->cek_departement_by_kode($dept_id)->row_array();
		$cek_type = $this->cek_type_created($kode)->row_array();
		if($cek_dept['type_dept'] == 'manufaktur' AND $cek_type['type_created'] == 0){
			$this->db->where('reserve_origin',$origin);	
		}
		$this->db->from($this->table3);
		return $this->db->count_all_results();
	}

	public function lock_tabel($table)
	{
		 $this->db->query("LOCK TABLES $table ");
	}

	public function get_last_quant_id()
	{
		$last_no =  $this->db->query("SELECT max(quant_id) as nom FROM stock_quant");

		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		return $no;
	}


	public function get_stock_move_items_by_kode($kode)
	{
		return $this->db->query("SELECT smi.quant_id, smi.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, smi.row_order,smi.origin_prod, sq.reff_note, smi.lebar_greige, smi.uom_lebar_greige, smi.lebar_jadi, smi.uom_lebar_jadi, smi.lokasi_fisik, tmp.valid,sq.nama_grade
								 FROM stock_move_items smi 
								 INNER JOIN pengiriman_barang pb ON smi.move_id = pb.move_id
								 INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
								 LEFT JOIN pengiriman_barang_tmp tmp ON pb.kode = tmp.kode AND smi.lot = tmp.lot
								 Where pb.kode = '$kode' 
								 ORDER BY smi.row_order")->result();
	}

	public function get_stock_move_items_by_kode_print($kode,$dept_id)
	{
		return $this->db->query("SELECT smi.quant_id, smi.move_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status, smi.row_order,smi.origin_prod, sq.reff_note, smi.lebar_jadi, smi.uom_lebar_jadi, smi.lebar_greige, smi.uom_lebar_greige, sq.nama_grade
								 FROM stock_move_items smi 
								 INNER JOIN pengiriman_barang pb ON smi.move_id = pb.move_id
								 INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
								 Where pb.kode = '$kode' AND pb.dept_id = '$dept_id'
								 ORDER BY smi.row_order")->result();
	}

	public function cek_stock_move_items($lot)
	{
		return $this->db->query("SELECT lot FROM stock_move_items WHERE lot = '$lot' GROUP BY lot");
	}

	public function update_status_pengiriman_barang($kode,$status)
	{
		return $this->db->query("UPDATE pengiriman_barang SET status = '$status' WHERE kode = '$kode'");
	}

	public function update_tgl_kirim_pengiriman_barang($kode,$tgl)
	{
		return $this->db->query("UPDATE pengiriman_barang SET tanggal_transaksi = '$tgl' WHERE kode = '$kode'");
	}

	public function update_status_pengiriman_barang_items($kode,$kode_produk,$status)
	{
		return $this->db->query("UPDATE pengiriman_barang_items SET status_barang = '$status' WHERE kode = '$kode' AND kode_produk = '$kode_produk' ");
	}

	public function update_status_pengiriman_barang_items_origin_prod($kode,$kode_produk,$status,$origin_prod)
	{
		return $this->db->query("UPDATE pengiriman_barang_items SET status_barang = '$status' WHERE kode = '$kode' AND kode_produk = '$kode_produk' AND origin_prod = '$origin_prod'");
	}

	public function update_status_pengiriman_barang_items_stock($kode,$status)
	{
		return $this->db->query("UPDATE pengiriman_barang_items SET status_barang = '$status' WHERE kode = '$kode'");
	}

	public function cek_status_barang_pengiriman_barang_items($kode,$status)
	{
		return $this->db->query("SELECT status_barang FROm pengiriman_barang_items where kode = '$kode' AND status_barang = '$status'");
	}

	public function cek_produk_stock_move_items($move_id,$nama_produk)
	{
		return $this->db->query("SELECT nama_produk FROM stock_move_items WHERE move_id = '$move_id' and nama_produk = '$nama_produk' GROUP BY nama_produk");
	}

	public function update_pengiriman_barang($kode,$reff_note)
	{
		return $this->db->query("UPDATE pengiriman_barang set reff_note = '$reff_note' WHERE kode = '$kode'");
	}

	public function cek_status_pengiriman_barang($kode)
	{
		return $this->db->query("SELECT status FROM pengiriman_barang WHERE kode = '$kode'");
	}

	public function get_location_by_move_id($move_id)
	{
		return $this->db->query("SELECT lokasi_dari, lokasi_tujuan From stock_move where move_id = '$move_id'");
	}

	public function update_status_pengiriman_barang_items_full($kode,$status)
	{
		return $this->db->query("UPDATE pengiriman_barang_items SET status_barang = '$status' WHERE kode = '$kode' ");
	}

	public function get_kode_penerimaan_by_move_id($move_id)
	{
		return $this->db->query("SELECT kode FROM penerimaan_barang WHERE move_id = '$move_id'");
	}
	
	public function cek_status_barang($kode)
	{
		return $this->db->query("SELECT status FROM pengiriman_barang where kode = '$kode'");
	}

	public function get_move_id_by_kode($kode)
	{
		return $this->db->query("SELECT pb.move_id,  pb.status, pb.dept_id, sm.method,pb.origin
								 FROM stock_move sm
								 INNER JOIN pengiriman_barang pb ON sm.move_id = pb.move_id 
								 WHERE pb.kode = '$kode'");
	}

	/*
	public function cek_valid_lokasi_lot_by_move_id($move_id)
	{
		return $this->db->query("SELECT move_id,lot,kode_lokasi FROM stock_kain_greige skg 
								 INNER JOIN stock_move_items smi ON skg.barcode_id = smi.lot
								 WHERE smi.move_id = '$move_id' AND kode_lokasi IN ('ADJ','GOUT','TRB','TRD','JAC')");
	}
	*/	

    public function get_kode_mo_pengiriman_barang_by_move_id($move_id)
	{	
		return $this->db->query("SELECT sm.move_id, rm.move_id,rm.kode
								FROM stock_move sm
								INNER JOIN mrp_production_rm_target rm ON sm.source_move =rm.move_id
								WHERE sm.move_id = '$move_id' LIMIT 1");
	}

	public function get_qty2_by_kode($move_id)
    {
   		return $this->db->query("SELECT sum(qty2) jml_qty2 FROM stock_move_items 
   								WHERE move_id = '$move_id'");
    }

    public function get_list_pengiriman_barang_items($kode)
    {
   	    return $this->db->query("SELECT * FROM pengiriman_barang_items WHERE kode = '$kode'")->result();
	}
	
	public function get_smi_produk_out_by_kode($move_id, $kode_produk)
    {
    	return $this->db->query("SELECT * FROM stock_move_items WHERE move_id = '$move_id' AND kode_produk = '$kode_produk' ORDER By row_order desc");
	}

	public function get_smi_produk_out_by_kode_origin($move_id, $kode_produk,$origin_prod)
    {
    	return $this->db->query("SELECT * FROM stock_move_items WHERE move_id = '$move_id' AND kode_produk = '$kode_produk' AND origin_prod = '$origin_prod' ORDER By row_order desc");
	}
	
	public function get_origin_prod_mrp_production_by_kode($move_id,$kode_produk)
	{
		return $this->db->query("SELECT * FROM mrp_production_rm_target where move_id = '$move_id' AND kode_produk = '$kode_produk' order by row_order ");
	}

	public function get_list_produk_pengirimanbarang_by_stock($nama_produk,$lokasi)
	{
		return $this->db->query("SELECT sq.kode_produk, sq.nama_produk, sq.uom
								FROM stock_quant sq								
								WHERE CONCAT(sq.kode_produk,sq.nama_produk)  LIKE '%$nama_produk%' AND sq.lokasi = '$lokasi' AND sq.reserve_move = '' AND sq.kode_produk IN (SELECT kode_produk FROM mst_produk WHERE type='stockable' AND status_produk='t') GROUP BY sq.kode_produk LIMIT 50  ")->result_array();
	}

	public function get_produk_pengiriman_barang_by_kode_produk($kode_produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE kode_produk = '$kode_produk' AND type = 'stockable' ");
	}

	public function get_row_order_pengiriman_barang_items($kode)
	{
		return $this->db->query("SELECT row_order FROM pengiriman_barang_items WHERE kode = '$kode' order by row_order desc LIMIT 1");
	}


	public function get_move_id_pengiriman_barang_by_kode($kode)
	{
		return $this->db->query("SELECT move_id FROM pengiriman_barang WHERE kode = '".$kode."' ");
	}


	public function cek_status_product_pengiriman_barang_items_by_row($kode,$kode_produk,$row_order)
	{
		return $this->db->query("SELECT * FROM  pengiriman_barang_items where kode = '$kode' AND kode_produk = '$kode_produk' AND row_order = '$row_order'");
	}


	public function update_pengiriman_barang_items_by_kode($kode,$kode_produk,$qty,$row)
	{
		return $this->db->query("UPDATE pengiriman_barang_items SET qty = '$qty' WHERE kode = '$kode' AND kode_produk = '$kode_produk' AND row_order = '$row' ");
	}

	public function update_stock_move_produk_by_kode($move_id,$kode_produk,$qty,$row)
	{
		return $this->db->query("UPDATE stock_move_produk SET qty = '$qty' WHERE kode_produk = '$kode_produk' AND row_order = '$row' ");
	}
  	
  	public function cek_produk_pengiriman_barang_items($kode,$kode_produk)
  	{
  		return $this->db->query("SELECT * FROM pengiriman_barang_items Where kode = '$kode' AND kode_produk = '$kode_produk' ");
  	}

  	public function cek_details_items_pengiriman_barang_by_produk($move_id,$kode_produk,$origin_prod)
  	{
  		return $this->db->query("SELECT * FROM stock_move_items where move_id = '$move_id' AND kode_produk = '$kode_produk' AND origin_prod = '$origin_prod'");
  	}

  	public function hapus_produk_pengirim_barang_dan_stock_move_produk_by_kode($move_id,$kode,$kode_produk,$row_order)
  	{
  		$this->db->query("DELETE FROM pengiriman_barang_items WHERE kode = '$kode' AND kode_produk = '$kode_produk' AND row_order = '$row_order' ");
  		$this->db->query("DELETE FROM stock_move_produk WHERE move_id = '$move_id' AND kode_produk = '$kode_produk' AND row_order = '$row_order' ");
  	}

	public function get_origin_prod_penerimaan_barang_by_kode($kode, $kode_produk)
	{
		return $this->db->query("SELECT origin_prod FROM penerimaan_barang_items WHERE kode = '$kode' AND kode_produk = '$kode_produk' ");
	}

	public function get_move_id_by_method_origin($method,$origin,$status,$status2)
	{
		return $this->db->query("SELECT move_id FROM stock_move WHERE method = '$method' AND origin = '$origin' AND status NOT IN ('$status','$status2') ");
	}


	public function simpan_pengiriman_barang_tmp($kode,$quant_id,$move_id,$kode_produk,$lot,$valid,$tgl)
	{
		return $this->db->query("INSERT INTO pengiriman_barang_tmp (kode,quant_id,move_id,kode_produk,lot,valid,valid_date) VALUES ('$kode','$quant_id','$move_id','$kode_produk','$lot','$valid','$tgl')");
	}

	public function get_count_valid_scan_by_kode($kode)
	{
		$this->db->SELECT('kode');
		$this->db->FROM('pengiriman_barang_tmp');
		$this->db->where('kode', $kode);
		$this->db->where('valid', 't');
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function get_count_all_scan_by_kode($move_id)
	{
		$this->db->SELECT('move_id');
		$this->db->FROM('stock_move_items');
		$this->db->where('move_id', $move_id);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function cek_scan_by_lot($kode,$lot)
	{
		return $this->db->query("SELECT lot FROM pengiriman_barang_tmp WHERE kode = '$kode' AND lot = '$lot'");
	}

	public function get_list_stock_move_items_by_lot($move_id,$lot,$status)
	{
		return $this->db->query("SELECT * FROM stock_move_items where move_id = '$move_id' AND lot = '$lot' AND status = '$status'")->result();
	}

	public function cek_pengiriman_barang_tmp_by_kode($kode)
	{
		$this->db->where('kode', $kode);
		$query = $this->db->get('pengiriman_barang_tmp');
		return $query->num_rows();
	}

	public function get_nama_warna_by_origin($kode_co, $row_order)
	{
		return $this->db->query("SELECT cod.id_warna, w.id, w.nama_warna
								FROM  color_order_detail cod
								INNER JOIN warna w ON cod.id_warna = w.id 
								Where kode_co ='".$kode_co."' AND row_order = '".$row_order."'");
	}

	public function get_route_by_origin($origin)
	{
		return $this->db->query("SELECT distinct method FROM stock_move where origin = '$origin' order by row_order asc")->result();
	}

	public function get_route_by_origin_method($origin,$method)
	{
		
		return $this->db->query("SELECT move_id, method FROM stock_move where origin = '$origin' AND method = '$method' order by create_date asc")->result();
	}

	public function get_kode_out_by_move_id($move_id)
	{
		$query = $this->db->query("SELECT kode FROM pengiriman_barang where move_id = '$move_id'")->row_array();
		return $query['kode'];
	}

	public function get_kode_in_by_move_id($move_id)
	{
		$query = $this->db->query("SELECT kode FROM penerimaan_barang where move_id = '$move_id'")->row_array();
		return $query['kode'];
	}

	public function get_kode_mrp_by_move_id($move_id)
	{
		$query = $this->db->query("SELECT distinct kode FROM mrp_production_rm_target where move_id = '$move_id'")->row_array();
		return $query['kode'];
	}

	public function get_kode_mrp_production_rm_target_by_move_id($move_id)
	{
		return $this->db->query("SELECT distinct(kode) FROM mrp_production_rm_target WHERE move_id = '$move_id' ");
	}


	public function get_type_mo_dept_id_mrp_production_by_kode($kode_mo)
	{
		$query = $this->db->query("SELECT mrp.dept_id, dept.type_mo
									FROM mrp_production mrp 
									INNER JOIN departemen dept ON mrp.dept_id =dept.kode 
									where mrp.kode in ($kode_mo) ")->row_array();
		return $query;
	}

	public function cek_mrp_production_rm_target_by_kode($kode_mo)
	{
		return $this->db->query("SELECT status FROM mrp_production_rm_target where kode in ($kode_mo) AND status IN ('draft','cancel') ");
	}

	public function get_quality_control_by_kode($kode,$dept_id)
	{
		return $this->db->query("SELECT qc.dept_id, qc.qc_1, qc.qc_2
								FROM pengiriman_barang as pb
								INNER JOIN quality_control as qc ON pb.dept_id = qc.dept_id
								WHERE qc.dept_id = '$dept_id' AND pb.kode = '$kode'");
	}

	public function update_quality_control($kode,$qc_ke,$value)
	{
		return $this->db->query("UPDATE pengiriman_barang set $qc_ke = '$value' WHERE kode = '$kode'");
	}

	public function get_nama_qc_by_dept($dept_id,$qc_ke)
	{
		return $this->db->query("SELECT $qc_ke as qc FROM quality_control WHERE dept_id = '$dept_id' ");
	}

	public function cek_quality_control_by_dept($dept_id)
	{
		$this->db->select('dept_id, qc_1, qc_2');
		$this->db->where('dept_id',$dept_id);
		$query = $this->db->get('quality_control');
		return $query;
	}


	public function cek_qc_pengiriman_barang_departemen_by_kode($kode,$qc_ke)
	{
		return $this->db->query("SELECT $qc_ke as qc FROM pengiriman_barang WHERE kode = '$kode'");
	}

	public function cek_qc_item_by_dept($dept_id,$qc_ke)
	{
		return $this->db->query("SELECT $qc_ke as qc FROM quality_control WHERE dept_id = '$dept_id'");
	}

	public function cek_stock_move_items_pengiriman_barang_by_move_id($move_id)
	{
		$this->db->where('move_id', $move_id);
		$query = $this->db->get('stock_move_items');
		return $query->num_rows();
	}

	public function get_warna_by_co($kode_co,$row_order)
	{
		return $this->db->query("SELECT w.nama_warna 
								FROM color_order_detail as cod 
								INNER JOIN warna as w ON cod.id_warna = w.id
								WHERE cod.kode_co = '$kode_co' AND cod.row_order  = '$row_order' ");
	}

	public function cek_jml_produk_sama_pengiriman_barang_by_kode($kode,$kode_produk)
	{
		return $this->db->query("SELECT count(kode_produk) as tot 
								FROM pengiriman_barang_items   
								where kode = '$kode' AND  kode_produk = '$kode_produk'
								GROUP BY kode_produk
								having COUNT(kode_produk) > 1 ");
	}

	public function get_qty_produk_pengiriman_by_kode_origin($kode,$kode_produk,$origin_prod)
	{
		$query =  $this->db->query("SELECT qty FROM pengiriman_barang_items where kode = '$kode' AND kode_produk = '$kode_produk' AND origin_prod = '$origin_prod'")->row_array()
		;
		return $query['qty'];
	}

	public function get_qty_produk_pengiriman_by_kode($kode,$kode_produk)
	{
		$query =  $this->db->query("SELECT qty FROM pengiriman_barang_items where kode = '$kode' AND kode_produk = '$kode_produk'")->row_array()
		;
		return $query['qty'];
	}

	public function delete_pengiriman_barang_tmp($kode,$move_id,$quant_id)
	{
		$this->db->query("DELETE FROM pengiriman_barang_tmp WHERE kode = '$kode' AND move_id = '$move_id' AND quant_id = '$quant_id' ");
	}
	

	public function get_lokasi_tujuan_out_by_dept($dept_id,$params)
	{
		return $this->db->query("SELECT stock_location as lokasi FROM departemen where kode = '$dept_id' AND stock_location lIKE '%$params%'
						UNION
						SELECT stock2_location as lokasi FROM departemen where kode = '$dept_id' AND stock2_location lIKE '%$params%'
						ORDER BY lokasi")->result_array();
	}


	public function cek_produk_mrp_production_rm_target($kode,$kode_produk) 
	{
		$this->db->where('kode',$kode);
		$this->db->where('kode_produk',$kode_produk);
		return $this->db->get('mrp_production_rm_target');
	}

	public function cek_produk_penerimaan_barang_items($kode,$kode_produk)
  	{
  		return $this->db->query("SELECT * FROM penerimaan_barang_items Where kode = '$kode' AND kode_produk = '$kode_produk' ");
  	}

  
}


?>