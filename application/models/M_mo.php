<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_mo extends CI_Model
{
	
	//var $table 		  = 'mrp_production';
	//var $table2 	  = 'mesin';
	var $column_order = array(null, 'kode', 'tanggal', 'nama_produk', 'qty', 'uom', 'reff_note', 'responsible','nama_status');
	var $column_search= array( 'kode', 'tanggal', 'nama_produk', 'qty','uom', 'reff_note', 'responsible', 'nama_status');
	var $order  	  = array('kode' => 'desc');

	var $table2  	    = 'stock_quant';
	var $column_order2  = array(null, 'nama_produk', 'lot', 'qty', 'qty2', 'reff_note');
	var $column_search2 = array('nama_produk', 'lot', 'qty', 'qty2', 'reff_note');
	var $order2  	    = array('create_date' => 'asc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	private function _get_datatables_query()
	{
		//$this->db->from('mrp_production');
		//$this->db->JOIN('mrp_production', 'mrp_production.md_id = mesin.mc_id');

		$this->db->select("mp.kode, mp.tanggal, mp.nama_produk, mp.qty, mp.uom, mp.status, mmss.nama_status, mp.reff_note, mp.responsible");
		$this->db->from("mrp_production mp");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=mp.status", "inner");

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
		$this->db->select("mp.kode, mp.tanggal, mp.nama_produk, mp.qty, mp.uom, mp.status, mmss.nama_status");
		$this->db->from("mrp_production mp");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=mp.status", "inner");
		$this->db->where('dept_id',$id_dept);
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		return $this->db->count_all_results();
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

	function get_datatables2($kode_produk,$lokasi)
	{
		$this->_get_datatables2_query();
		$this->db->where('kode_produk',$kode_produk );
		$this->db->where('lokasi', $lokasi);
		$this->db->where('reserve_move','');
		$this->db->where_not_in('qty','0');
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2($kode_produk,$lokasi)
	{
		$this->db->where('kode_produk',$kode_produk );
		$this->db->where('reserve_move','');
		$this->db->where('lokasi', $lokasi);
		$this->db->where_not_in('qty','0');
		$this->_get_datatables2_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($kode_produk,$lokasi)
	{
		$this->db->where('kode_produk',$kode_produk );
		$this->db->where('reserve_move','');
		$this->db->where('lokasi', $lokasi);
		$this->db->where_not_in('qty','0');
		$this->db->from($this->table2);
		return $this->db->count_all_results();
	}


	public function get_data_by_code($kode)
	{
		$query = $this->db->query("SELECT * FROM mrp_production where kode = '".$kode."' ");
		return $query->row();
	}

	public function get_list_bahan_baku($kode)
	{
		return $this->db->query("SELECT rm.status,rm.kode_produk,rm.nama_produk,rm.qty,rm.uom,rm.kode,rm.row_order,rm.origin_prod,rm.move_id, mp.type, 
									(SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = rm.move_id And smi.origin_prod = rm.origin_prod AND smi.status = 'ready' ) as sum_qty,
									(SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = rm.move_id And smi.origin_prod = rm.origin_prod AND smi.status = 'done' ) as sum_qty_done,
									(SELECT bi.note FROM bom_items bi WHERE bi.kode_produk = rm.kode_produk AND bi.row_order = rm.row_order AND bi.kode_bom = mrp.kode_bom ) as reff
								FROM mrp_production_rm_target rm 
								INNER JOIN mst_produk mp ON mp.kode_produk = rm.kode_produk 
								INNER JOIN mrp_production mrp ON mrp.kode = rm.kode
								WHERE rm.kode = '".$kode."' AND mp.id_category NOT IN ('11','12') AND rm.status NOT IN ('done') ORDER BY rm.row_order")->result();
		
	}

	public function get_list_bahan_baku_stok($kode)
	{
		return $this->db->query("SELECT rm.kode_produk, rm.nama_produk,rm.qty,rm.uom,rm.kode,rm.row_order,rm.origin_prod,
			(SELECT sum(smi.qty) FROM stock_move_items smi 	WHERE  smi.move_id = rm.move_id And smi.origin_prod = rm.origin_prod) as sum_qty
								 FROM mrp_production_rm_target rm							
								 RIGHT JOIN mst_produk mp ON mp.kode_produk = rm.kode_produk
								 WHERE rm.kode = '".$kode."' AND mp.id_category NOT IN ('11','12') AND mp.type IN ('stockable') AND rm.status NOT IN ('done')  ORDER BY rm.row_order")->result();
		
	}


	public function get_list_bahan_baku_cons($kode,$status)
	{
		return $this->db->query("SELECT rm.kode_produk, rm.nama_produk,rm.qty,rm.uom,rm.kode,rm.row_order,rm.origin_prod,rm.move_id	
								 FROM mrp_production_rm_target rm							
								 RIGHT JOIN mst_produk mp ON mp.kode_produk = rm.kode_produk
								 WHERE rm.status = '".$status."' AND rm.kode = '".$kode."' AND mp.id_category NOT IN ('11','12')AND mp.type IN ('consumable') ORDER BY rm.row_order")->result();
		
	}

	public function cek_status_produk_kain($kode)
	{
		return $this->db->query("SELECT sm.status
								 FROM mrp_production_rm_target rm
								 INNER JOIN stock_move sm ON rm.move_id = sm.move_id 
								 WHERE rm.kode = '".$kode."' AND sm.status in ('ready')");
	}

	public function get_list_bahan_baku_hasil($kode,$kode_produk)
	{
		return $this->db->query("SELECT rm.kode, rm.move_id, rm.quant_id, rm.kode_produk, rm.nama_produk, rm.lot, rm.qty, rm.uom, rm.origin_prod, smi.qty2, smi.uom2
								FROM mrp_production_rm_hasil rm
								INNER JOIN stock_move_items smi ON rm.quant_id = smi.quant_id AND rm.move_id = smi.move_id
								WHERE rm.kode = '".$kode."' AND rm.kode_produk = '".$kode_produk."' ORDER BY rm.row_order")->result();
	}

	public function get_list_bahan_baku_hasil_group($kode)
	{
		return $this->db->query("SELECT rm.kode, rm.move_id, rm.kode_produk, rm.nama_produk, sum(rm.qty) as tot_qty, rm.uom, rm.origin_prod,rm.quant_id, sum(smi.qty2) as tot_qty2, smi.uom2
								FROM mrp_production_rm_hasil rm
								INNER JOIN stock_move_items smi ON smi.quant_id = rm.quant_id AND smi.move_id = rm.move_id
								WHERE rm.kode = '".$kode."' 
								GROUP BY rm.kode_produk ORDER BY rm.kode_produk ")->result();
	}

	public function get_list_barang_jadi($kode)
	{
		return $this->db->query("SELECT fg.kode_produk,fg.nama_produk,fg.qty, fg.uom,
								(SELECT sum(fgh.qty) FROM mrp_production_fg_hasil fgh WHERE fgh.kode_produk = fg.kode_produk AND fg.kode = fgh.kode ) as sum_fg_hasil
								FROM mrp_production_fg_target fg WHERE fg.kode = '".$kode."' AND fg.status NOT IN ('done') ORDER BY fg.row_order")->result();
	}

	public function get_list_barang_jadi_hasil($kode,$lokasi_waste)
	{
		return $this->db->query("SELECT fg.kode, fg.move_id, fg.quant_id, fg.kode_produk, fg.kode_produk, fg.nama_produk, 
										fg.lot, fg.nama_grade, fg.qty, fg.uom, fg.row_order, sq.reff_note, fg.qty2, fg.uom2, fg.lebar_greige, fg.uom_lebar_greige, fg.lebar_jadi, fg.uom_lebar_jadi
								FROM mrp_production_fg_hasil fg 
								INNER JOIN stock_quant sq ON fg.quant_id =  sq.quant_id
								WHERE fg.kode = '".$kode."' AND fg.lokasi NOT IN ('".$lokasi_waste."') ORDER BY fg.row_order")->result();
	}

	public function get_list_barang_jadi_hasil_waste($kode, $lokasi_waste)
	{
		return $this->db->query("SELECT fg.kode, fg.move_id, fg.quant_id, fg.kode_produk, fg.kode_produk, fg.nama_produk, 
										fg.lot, fg.nama_grade, fg.qty, fg.uom, fg.row_order, sq.reff_note, fg.qty2, fg.uom2
								FROM mrp_production_fg_hasil fg 
								INNER JOIN stock_quant sq ON fg.quant_id =  sq.quant_id
								WHERE fg.kode = '".$kode."' AND fg.lokasi IN ('".$lokasi_waste."') ORDER BY fg.row_order")->result();	
	}

	public function save_rm($kode, $product, $qty, $uom)
	{
		$row = $this->db->query("SELECT max(row_order) row FROM mrp_production_rm_target WHERE kode = '$kode' ")->row_array();
		$ro  =$row['row'] + 1;
		return $this->db->query("INSERT INTO mrp_production_rm_target (kode, nama_produk, qty, uom,row_order) 
								  values ('$kode','$product','$qty','$uom','$ro')");	
	}

	public function delete_rm($kode, $row_order)
	{
		return $this->db->query("DELETE FROM mrp_production_rm_target WHERE kode = '".$kode."' AND row_order = '".$row_order."'");
	}

	public function get_total_fg($kode)
	{
		$query = $this->db->query("SELECT sum(qty) as total_qty FROM mrp_production_fg_hasil WHERE kode = '".$kode."'");
		return $query->row();
	}

	public function get_jml_mesin()
	{
		return $this->db->query("SELECT count(mc_id) as jml FROM mesin ")->row_array();
	}

	public function get_mesin()
	{
		return $this->db->query("SELECT * FROM mesin ORDER by row_order asc ")->result();
	}

	public function get_data_by_mesin($mesin, $dari,  $sampai,  $product, $deptid)
	{
		$where ="mc_id = '".$mesin."' ";
		if(!empty($dari))
		{
			$where = $where . " AND tanggal >= '".$dari."' AND tanggal <= '".$sampai."'";
		}
		if (!empty($product))
		{
			$where = $where . " AND  product LIKE '%".$product."%' ";
		}

		return $this->db->query("SELECT * FROM mrp_production WHERE " .$where. " AND dept_id = '$deptid' order BY tanggal asc")->result();
	}

   public function get_berat_by_kode($kode)
   {
   		return $this->db->query("SELECT sum(qty2) jml_qty2 FROM stock_move_items smi 
   								INNER JOIN mrp_production_rm_target rm ON rm.move_id = smi.move_id
   								WHERE rm.kode = '$kode'");
   }

   public function get_dyeing_stuff($kode)
   {
     	return $this->db->query("SELECT rm.nama_produk, rm.qty, rm.uom,  wi.qty qty_asli, smp.status
								FROM mrp_production_rm_target rm 
								INNER JOIN mrp_production m ON rm.kode = m.kode 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								INNER JOIN warna_items wi ON m.kode_warna = wi.kode_warna AND wi.kode_produk = rm.kode_produk 
								INNER JOIN stock_move_produk smp ON smp.move_id = rm.move_id AND smp.kode_produk = rm.kode_produk 
								WHERE rm.kode = '$kode' AND mp.category IN ('11','12') AND wi.type_obat = 'DYE' 
								order by rm.row_order")->result();
   }

   public function get_aux($kode)
   {
   		return $this->db->query("SELECT rm.nama_produk, rm.qty, rm.uom,  wi.qty qty_asli, smp.status
								FROM mrp_production_rm_target rm 
								INNER JOIN mrp_production m ON rm.kode = m.kode 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								INNER JOIN warna_items wi ON m.kode_warna = wi.kode_warna AND wi.kode_produk = rm.kode_produk 
								INNER JOIN stock_move_produk smp ON smp.move_id = rm.move_id AND smp.kode_produk = rm.kode_produk 
								WHERE rm.kode = '$kode' AND mp.category IN ('11','12') AND wi.type_obat = 'AUX' 
								order by rm.row_order")->result();
   }

    public function get_route_warna($route)
	{
		return $this->db->query("SELECT * FROM  mrp_route 					
								WHERE nama_route = '$route' ORDER BY row_order ")->result();
	}

	public function get_warna_items_by_warna($warna)
	{
		return $this->db->query("SELECT * FROM warna_items WHERE kode_warna = '$warna' order by type_obat,row_order")->result();
	}


	public function cek_status_warna($warna)
	{
		return $this->db->query("SELECT * FROM warna WHERE kode_warna = '$warna' AND status in ('ready','requested','done')");
	}

	public function get_warna_by_kode($kode)
	{
		return $this->db->query("SELECT kode_warna FROM mrp_production WHERE kode = '$kode'");
	}

	public function cek_origin_di_stock_move($origin)
	{
		return $this->db->query("SELECT origin FROM stock_move WHERE origin = '$origin'");
	}
	
	public function get_list_mesin($dept_id)
	{
		return $this->db->query("SELECT * FROM mesin WHERE dept_id = '$dept_id'  AND status_aktif = 't' order by row_order ")->result();
	}

	public function get_nama_mesin_by_kode($mc_id)
	{
		return $this->db->query("SELECT * FROM mesin where mc_id = '$mc_id' ");
	}

	public function update_mo($kode,$berat,$air,$start,$finish,$reff_note,$mesin,$qty1_std,$qty2_std,$lot_prefix,$lot_prefix_waste,$target_efisiensi,$lebar_greige,$uom_lebar_greige,$lebar_jadi,$uom_lebar_jadi,$type_production)
	{
		return $this->db->query("UPDATE mrp_production set berat = '$berat', air = '$air', start_time = '$start', 
														   finish_time = '$finish',reff_note = '$reff_note', 
														   mc_id = '$mesin', qty1_std = '$qty1_std', 
														   qty2_std = '$qty2_std',lot_prefix = '$lot_prefix', 
														   lot_prefix_waste = '$lot_prefix_waste', 
														   target_efisiensi = '$target_efisiensi',
														   lebar_greige = '$lebar_greige',
														   uom_lebar_greige = '$uom_lebar_greige',
														   lebar_jadi = '$lebar_jadi',
														   uom_lebar_jadi = '$uom_lebar_jadi',
														   type_production = '$type_production'
														WHERE kode = '$kode' ");
	}

	public function get_row_order_rm_target($kode)
	{
		return $this->db->query("SELECT max(row_order) row FROM mrp_production_rm_target WHERE kode = '$kode' ");
	}

	public function get_row_order_fg_hasil($kode)
	{
		return $this->db->query("SELECT max(row_order) row FROM mrp_production_fg_hasil WHERE kode = '$kode' ");
	}

	public function get_row_order_rm_hasil($kode)
	{
		return $this->db->query("SELECT max(row_order) row FROM mrp_production_rm_hasil WHERE kode = '$kode' ");
	}


	public function get_row_order_rekam_cacat($kode,$lot)
	{
		return $this->db->query("SELECT max(row_order) row FROM mrp_production_cacat WHERE kode = '$kode' AND lot = '$lot'");
	}

	public function get_berat_air_by_kode($kode)
	{
		return $this->db->query("SELECT air,berat from mrp_production WHERE kode = '$kode'");
	}

	public function save_obat($sql)
	{
		return $this->db->query("INSERT INTO mrp_production_rm_target (kode,move_id,kode_produk,nama_produk,qty,uom,row_order) 
								values $sql ");
	} 

	public function cek_berat_air($kode)
	{
		return $this->db->query("SELECT air, berat FROM mrp_production where kode = '$kode' ");
	}

	public function cek_type_mo_by_dept_id($id_dept)
	{
		return $this->db->query("SELECT type_mo FROM departemen WHERE kode = '$id_dept'");
	}

	public function get_nama_bom_by_kode($kode_bom)
	{
		return $this->db->query("SELECT nama_bom FROM bom WHERE kode_bom = '$kode_bom' ");
	}

	public function cek_status_mrp_production($kode, $status)
	{
		if(empty($status)){
			return $this->db->query("SELECT status FROM mrp_production WHERE kode = '$kode'");
		}else{
			return $this->db->query("SELECT status FROM mrp_production WHERE kode = '$kode' AND status = '$status'");
		}
	}

	public function cek_status_barang_mrp_production_rm_target($kode, $status, $status2)
	{
		return $this->db->query("SELECT status FROm mrp_production_rm_target where kode = '$kode' AND status IN ('$status', '$status2') ");
	}
/*
	public function cek_status_barang_mrp_production_rm_target_stockable($kode, $status)
	{
		return $this->db->query("SELECT rm.status FROm mrp_production_rm_target rm 
												  INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk 
												  where rm.kode = '$kode' AND rm.status = '$status' AND mp.type = 'stockable'");
	}
*/
	public function update_status_mrp_production($kode,$status)
	{
		return $this->db->query("UPDATE mrp_production SET status ='$status' WHERE kode = '$kode'");
	}

	public function get_move_id_rm_target_by_kode($kode)
	{
		return $this->db->query("SELECT DISTINCT(move_id) as move_id from mrp_production_rm_target WHERE kode = '$kode'");
	}

	public function get_move_id_fg_target_by_kode($kode)
	{
		return $this->db->query("SELECT DISTINCT(move_id) as move_id from mrp_production_fg_target WHERE kode = '$kode'");
	}

    public function get_location_by_mo($kode)
	{
		return $this->db->query("SELECT source_location, destination_location From mrp_production where kode = '$kode'");
	}

	public function get_view_quant_by_kode($move_id,$origin_prod)
	{
		return $this->db->query("SELECT smi.move_id, smi.quant_id, smi.kode_produk, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, 
			 smi.qty2, smi.uom2, smi.`status`, smi.origin_prod, smi.row_order, smi.qty2, smi.uom2, sq.reff_note
								FROM stock_move_items smi
								INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id
								WHERE smi.move_id = '$move_id' AND smi.origin_prod = '$origin_prod' AND smi.status = 'ready' ORDER BY row_order")->result();
	}

	public function update_status_stock_move_produk_mo($move_id, $origin_prod, $status)
	{
		return $this->db->query("UPDATE stock_move_produk SET status = '$status' WHERE move_id = '$move_id' AND origin_prod = '$origin_prod'");
	}

	public function update_status_mrp_production_rm_target($kode, $origin_prod, $status)
	{
		return $this->db->query("UPDATE mrp_production_rm_target SET status = '$status' WHERE kode = '$kode' AND origin_prod = '$origin_prod'");
	}

	public function get_konsumsi_bahan($move_id,$status)
	{
		return $this->db->query("SELECT smi.move_id, smi.quant_id,smi.kode_produk, smi.nama_produk, 
								smi.lot, smi.qty, smi.uom,smi.origin_prod,smi.qty2,smi.uom2, rm.qty as qty_rm, sq.reff_note,sq.nama_grade,mp.type,
								(SELECT count(kode_produk) as jml_prod FROM stock_move_items smi2 WHERE 
									smi2.kode_produk = smi.kode_produk AND smi2.move_id = '$move_id' AND smi2.status = '$status') as jml_produk,
								smi.lebar_greige, smi.uom_lebar_greige, smi.lebar_jadi, smi.uom_lebar_jadi, sq.sales_order, sq.sales_group
								FROM stock_move_items smi
								INNER JOIN mrp_production_rm_target rm ON smi.origin_prod = rm.origin_prod AND rm.move_id = smi.move_id
								INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								where rm.move_id = '$move_id' AND smi.status = '$status' order by smi.nama_produk,smi.lot ")->result();
	
	}

	public function simpan_mrp_production_fg_hasil_batch($sql)
	{
		return $this->db->query("INSERT INTO mrp_production_fg_hasil (kode,move_id,quant_id,create_date,kode_produk,nama_produk,lot,nama_grade,qty,uom,qty2,uom2,lokasi,nama_user,row_order,lebar_greige,uom_lebar_greige,lebar_jadi,uom_lebar_jadi,sales_order,sales_group) values $sql");
	}

	public function simpan_mrp_production_rm_hasil_batch($sql)
	{
		return $this->db->query("INSERT INTO mrp_production_rm_hasil (kode,move_id,kode_produk,nama_produk,lot,qty,uom,origin_prod,row_order,quant_id) values $sql");
	}

	function cek_length_counter_lot_by_dept_id($dept_id)
	{
		$query 	= $this->db->query("SELECT * FROM mrp_counter_lot where dept_id = '$dept_id' ")->row_array();
		$rs     = $query['length_counter'];

		if(empty($rs)){
			$length 	= "3";
			$dgt_nol 	= "000";
			$dgt_nol_jv = "00";
		}else{
			$length  = $rs;
			$dgt_nol = '';
			$dgt_nol_jv = '';

			for($i=0;$i<$rs;$i++){
				$dgt_nol .= "0";
			}

			for($i=1;$i<$rs;$i++){
				$dgt_nol_jv .= "0";
			}
		}

		$data["length"] 	= $length;
		$data["dgt_nol"] 	= $dgt_nol;
		$data["dgt_nol_jv"] = $dgt_nol_jv;// buat javascript

		return $data;
	}

	public function get_counter_by_lot_prefix($lot_prefix,$dept_id)
	{
		/*
		return $this->db->query("SELECT count(lot) as jml_lot FROM stock_move_items WHERE lot LIKE '%$lot_prefix%' AND move_id = '$move_id'");
		*/
		$rs 	= $this->cek_length_counter_lot_by_dept_id($dept_id);
		$dgt_nol= $rs['dgt_nol'];
		$length = $rs['length'];
		
		$result = $this->db->query("SELECT lot FROM stock_quant  WHERE lot LIKE '$lot_prefix%'  ORDER BY RIGHT(lot,$length) DESC LIMIT 1 ");

		if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=substr($row->lot,-$length)+1;
        }else{
            $dgt="1";
        }
        $dgt=substr($dgt_nol . $dgt,-$length);            
        return $dgt;

	}

	public function get_counter_by_lot_prefix_waste($lot_prefix_waste,$lokasi_waste)
	{
		return $this->db->query("SELECT count(lot) as jml_lot FROM stock_quant WHERE lot LIKE '$lot_prefix_waste%' AND lokasi = '$lokasi_waste'");
	}
	
	public function cek_lot_stock_quant($lot)
	{
		return $this->db->query("SELECT lot from stock_quant WHERE lot = '$lot' and (lokasi NOT LIKE '%ADJ%' OR lokasi NOT LIKE '%Waste%') ");
	}

	public function cek_lot_stock_quant_waste($lot,$lokasi_waste)
	{
		return $this->db->query("SELECT lot from stock_quant WHERE lot = '$lot' and lokasi = '$lokasi_waste' ");
	}

	public function cek_qty_stock_move_items_by_produk($move_id,$origin_prod,$status)
	{
		return $this->db->query("SELECT sum(qty) as jml_qty FROM stock_move_items WHERE move_id = '$move_id' AND origin_prod = '$origin_prod' AND status = '$status' ");
	}

	public function get_list_cacat($deptid)
	{
		return $this->db->query("SELECT kode_cacat, nama_cacat, CONCAT(kode_cacat,' ', nama_cacat) as kode_nama FROM mst_cacat WHERE  dept_id = '$deptid'")->result_array();
	}

	
	public function simpan_rekam_cacat_lot($sql)
	{
		return $this->db->query("INSERT INTO mrp_production_cacat (kode,quant_id,create_date,lot,dept_id,point_cacat,kode_cacat,row_order,nama_user) values $sql ");
	}

	public function get_list_rekam_cacat($kode,$lot,$quant_id)
	{
		return $this->db->query("SELECT mpc.point_cacat, mpc.kode,mpc.quant_id,mpc.lot, mpc.dept_id, mpc.row_order, mc.kode_cacat, mc.nama_cacat, concat(mc.kode_cacat,' ',mc.nama_cacat) as kode_nama
								  FROM mrp_production_cacat mpc 
								  INNER JOIN mst_cacat mc ON (mpc.kode_cacat = mc.kode_cacat AND mpc.dept_id = mc.dept_id) where kode = '$kode' and lot = '$lot' AND quant_id = '$quant_id' ORDER BY mpc.lot, mpc.row_order ")->result();
	}

	public function hapus_rekam_cacat_lot($kode,$quant_id,$lot,$row_order)
	{
		return $this->db->query("DELETE FROM mrp_production_cacat WHERE kode = '$kode' AND quant_id = '$quant_id' AND lot = '$lot' AND row_order = '$row_order' ");
	}

	public function get_qty_mrp_production_fg_target($kode)
	{
		return $this->db->query("SELECT move_id,qty FROM mrp_production_fg_target WHERE kode = '$kode'");
	}

	public function get_qty_mrp_production_fg_hasil($kode)
	{
		return $this->db->query("SELECT sum(qty) as sum_qty FROM mrp_production_fg_hasil WHERE kode = '$kode'");
	}

	public function update_status_mrp_production_fg_target($kode,$status)
	{
		return $this->db->query("UPDATE mrp_production_fg_target SET status = '$status'  where kode = '$kode' ");
	}

	public function get_location_waste_by_deptid($dept_id)
	{
		return $this->db->query("SELECT waste_location FROM departemen WHERE kode = '$dept_id'");
	}

	public function cek_status_barang_mrp_production_rm_target_done($kode,$status,$status2)
	{
		return $this->db->query("SELECT status FROM mrp_production_rm_target WHERE kode = '$kode' AND status NOT IN ('$status','$status2') ");
	}


	public function cek_no_mesin_mrp_production_by_kode($kode)
	{
		return $this->db->query("SELECT mc_id FROM mrp_production where kode = '$kode'");
	}

	//blm dipake
	public function get_list_mrp_production_rm_target_by_move_id($move_id)
	{
		return $this->db->query("SELECT * FROM mrp_production_rm_target where move_id = '$move_id' order by row_order");
	}


	public function get_origin_prod_mrp_production_by_kode($move_id,$kode_produk)
	{
		return $this->db->query("SELECT * FROM mrp_production_rm_target where move_id = '$move_id' AND kode_produk = '$kode_produk' order by row_order ");
	}
	
	public function get_kode_mrp_production_rm_target_by_move_id($move_id)
	{
		return $this->db->query("SELECT distinct(kode) FROM mrp_production_rm_target WHERE move_id = '$move_id' ");
	}

	public function get_list_waste_bahan_baku_by_move_id($move_id)
	{
		return $this->db->query("SELECT smi.kode_produk, smi.nama_produk
								FROM stock_move_items smi
								INNER JOIN mrp_production_rm_target rm ON smi.origin_prod = rm.origin_prod AND rm.move_id = smi.move_id
								INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								where rm.move_id = '$move_id' AND mp.type = 'stockable' 
								GROUP BY smi.kode_produk
								order by smi.nama_produk,smi.lot");
	}

	public function get_list_lot_waste_by_kode($move_id,$kode_produk)
	{
		return $this->db->query("SELECT smi.kode_produk, smi.nama_produk,smi.lot
								FROM stock_move_items smi
								INNER JOIN mrp_production_rm_target rm ON smi.origin_prod = rm.origin_prod AND rm.move_id = smi.move_id
								INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								where rm.move_id = '$move_id' AND mp.type = 'stockable'  AND smi.kode_produk = '$kode_produk'
								GROUP BY lot
								order by smi.nama_produk,smi.lot");
	}

	public function get_nama_produk_waste_by_kode($kode_produk)
	{
		return $this->db->query("SELECT nama_produk,uom,uom_2 FROM mst_produk where kode_produk = '$kode_produk'");
	}

	public function get_smi_produk_by_kode($move_id,$origin_prod,$status)
	{
		return $this->db->query("SELECT * FROM stock_move_items WHERE move_id = '$move_id' AND origin_prod = '$origin_prod' AND status = '$status' order by row_order desc");
	}

	public function get_origin_prod_pengiriman_barang_by_kode($kode, $kode_produk)
	{
		return $this->db->query("SELECT origin_prod FROM pengiriman_barang_items WHERE kode = '$kode' AND kode_produk = '$kode_produk' ");
	}

	public function get_origin_prod_penerimaan_barang_by_kode($kode, $kode_produk)
	{
		return $this->db->query("SELECT origin_prod FROM penerimaan_barang_items WHERE kode = '$kode' AND kode_produk = '$kode_produk' ");
	}

	public function get_origin_prod_mrp_production_by_kode_mrp($kode, $kode_produk)
	{
		return $this->db->query("SELECT origin_prod FROM mrp_production_rm_target WHERE kode = '$kode' AND kode_produk = '$kode_produk' ORDER BY row_order ");
	}

	public function get_data_fg_hasil_by_kode($kode,$lot)
	{
		return $this->db->query("SELECT mp.create_date,mp.kode_produk, mp.nama_produk, mp.lot, mp.qty, mp.uom, mp.qty2, mp.uom2, sq.reff_note, mph.reff_note as note_head
								FROM mrp_production_fg_hasil mp
								LEFT JOIN stock_quant sq ON mp.quant_id = sq.quant_id
								LEFT Join mrp_production mph ON mp.kode = mph.kode
								where mp.kode = '$kode' AND mp.lot = '$lot' ");
	}

	public function get_mesin_by_mo($kode)
	{
		return $this->db->query("SELECT a.mc_id, b.nama_mesin FROM mrp_production a 
								INNER JOIN mesin b ON a.mc_id = b.mc_id
								WHERE a.kode = '$kode' ");
	}

	public function get_origin_mo_by_kode($kode)
	{
		$result =  $this->db->query("SELECT origin FROm mrp_production where kode = '$kode' ")->row_array();
		return $result['origin'];

	}

	public function get_reff_picking_pengiriman_by_kode($lot,$method,$origin)
	{
		$result = $this->db->query("SELECT pb.kode, pb.reff_picking
							FROM stock_move_items smi
							INNER JOIN stock_move sm ON smi.move_id = sm.move_id 
							INNER JOIN pengiriman_barang pb ON sm.move_id = pb.move_id
							WHERE smi.lot = '$lot' AND sm.method = '$method' AND sm.origin = '$origin'")->row_array();
		return $result['reff_picking'];

	}

	public function cek_validasi_double_lot_by_dept($dept_id)
	{
		$result =  $this->db->query("SELECT validasi_double_lot FROM departemen WHERE kode = '$dept_id' ")->row_array();
		return $result['validasi_double_lot'];
	}

	public function get_lebar_produk_by_kode($kode)
	{
		$result = $this->db->query("SELECT lebar_greige, uom_lebar_greige, lebar_jadi, uom_lebar_jadi FROM mrp_production WHERE kode = '$kode' ");
		return $result->row();
	}

	public function no_mesin_by_mc_id($mc_id)
	{
		$result = $this->db->query("SELECT no_mesin FROM mesin where mc_id = '$mc_id'")->row_array();
		return $result['no_mesin'];
	}


}