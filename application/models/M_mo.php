<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_mo extends CI_Model
{
	
	//var $table 		  = 'mrp_production';
	//var $table2 	  = 'mesin';
	var $column_order = array(null, 'kode', 'tanggal','origin', 'nama_produk', 'qty', 'uom', 'reff_note', 'responsible','nama_status');
	var $column_search= array( 'kode', 'tanggal','origin', 'nama_produk', 'qty','uom', 'reff_note', 'responsible', 'nama_status');
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

		$this->db->select("mp.kode, mp.tanggal, mp.origin, mp.nama_produk, mp.qty, mp.uom, mp.status, mmss.nama_status, mp.reff_note, mp.responsible");
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
		if(isset($_POST["length"]) && $_POST["length"] != -1)
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
		$this->db->where('(qty != 0 or qty2 != 0)');
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
		// $this->db->where_not_in('qty','0');
		$this->db->where('(qty != 0 or qty2 != 0)');
		$this->_get_datatables2_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($kode_produk,$lokasi)
	{
		$this->db->where('kode_produk',$kode_produk );
		$this->db->where('reserve_move','');
		$this->db->where('lokasi', $lokasi);
		// $this->db->where_not_in('qty','0');		
		$this->db->where('(qty != 0 or qty2 != 0)');
		$this->db->from($this->table2);
		return $this->db->count_all_results();
	}


	public function get_data_by_code($kode)
	{
		$query = $this->db->query("SELECT mrp.kode, mrp.tanggal, mrp.origin, mrp.kode_produk, mrp.nama_produk, mrp.qty, mrp.uom, mrp.reff_note,mrp.id_warna, mrp.tanggal_jt, mrp.kode_bom, mrp.start_time, mrp.finish_time, mrp.source_location, mrp.air, mrp.berat, mrp.dept_id, mrp.mc_id, mrp.status, mrp.responsible, mrp.qty1_std, mrp.qty2_std, mrp.lot_prefix, mrp.lot_prefix_waste, mrp.target_efisiensi,mrp.lebar_greige, mrp.uom_lebar_greige, mrp.lebar_jadi, mrp.uom_lebar_jadi, mrp.type_production, mrp.id_handling, mrp.alasan, hd.nama_handling, w.nama_warna, w.kode_warna, wv.notes_varian as notes_varian, mrp.program, mrp.gramasi, wv.id as id_warna_varian, wv.nama_varian
								  FROM mrp_production mrp 
								  LEFT join  mst_handling hd ON mrp.id_handling = hd.id 
								  LEFT JOIN warna w ON mrp.id_warna = w.id
								  LEFT JOIN warna_varian wv ON w.id = wv.id_warna AND wv.id = mrp.id_warna_varian
								  where mrp.kode = '".$kode."' ");
		return $query->row();
	}

	public function get_list_bahan_baku($kode)
	{
		return $this->db->query("SELECT rm.status,rm.kode_produk,rm.nama_produk,rm.qty,rm.uom,rm.kode,rm.row_order,rm.origin_prod,rm.move_id, mp.type, 
									(SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = rm.move_id And smi.origin_prod = rm.origin_prod AND smi.status = 'ready' ) as sum_qty,
									(SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = rm.move_id And smi.origin_prod = rm.origin_prod AND smi.status = 'done' ) as sum_qty_done,
									(SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = rm.move_id And smi.origin_prod = rm.origin_prod AND smi.status = 'cancel' ) as sum_qty_cancel,
									(SELECT bi.note FROM bom_items bi WHERE bi.kode_produk = rm.kode_produk AND bi.row_order = rm.row_order AND bi.kode_bom = mrp.kode_bom ) as reff
								FROM mrp_production_rm_target rm 
								INNER JOIN mst_produk mp ON mp.kode_produk = rm.kode_produk 
								INNER JOIN mrp_production mrp ON mrp.kode = rm.kode
								WHERE rm.kode = '".$kode."' AND mp.id_category NOT IN ('11','12') AND rm.status NOT IN ('done') AND rm.additional = 'f'  ORDER BY rm.row_order")->result();
		
	}

	public function get_list_bahan_baku_stok($kode,$move_id)
	{
		return $this->db->query("SELECT rm.kode_produk, rm.nama_produk,rm.qty,rm.uom,rm.kode,rm.row_order,rm.origin_prod,rm.move_id,
			(SELECT sum(smi.qty) FROM stock_move_items smi 	WHERE  smi.move_id = rm.move_id And smi.origin_prod = rm.origin_prod) as sum_qty
								 FROM mrp_production_rm_target rm							
								 RIGHT JOIN mst_produk mp ON mp.kode_produk = rm.kode_produk
								 WHERE rm.kode = '".$kode."' AND rm.move_id = '".$move_id."' AND mp.id_category NOT IN ('11','12') AND mp.type IN ('stockable') AND rm.status NOT IN ('done') AND rm.move_id != '' ORDER BY rm.row_order")->result();
		
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
		return $this->db->query("SELECT rm.status
								FROM mrp_production_rm_target rm
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								INNER JOIN mst_category cat ON mp.id_category = cat.id
								WHERE rm.kode = '".$kode."' AND rm.status in ('ready','done')  AND cat.nama_category LIKE '%kain%'");
	}

	public function get_list_bahan_baku_hasil($kode,$kode_produk,$additional)
	{
		return $this->db->query("SELECT rm.kode, rm.move_id, rm.quant_id, rm.kode_produk, rm.nama_produk, rm.lot, rm.qty, rm.uom, rm.origin_prod, smi.qty2, smi.uom2
								FROM mrp_production_rm_hasil rm
								INNER JOIN stock_move_items smi ON rm.quant_id = smi.quant_id AND rm.move_id = smi.move_id
								WHERE rm.kode = '".$kode."' AND rm.kode_produk = '".$kode_produk."' AND rm.additional = '".$additional."' ORDER BY rm.row_order")->result();
	}

	function get_qty_kg_all_by_kode($kode)
	{
		$query = $this->db->query("SELECT rmh.kode,  round(sum(if(smi.uom = 'kg', smi.qty, if(smi.uom = 'gr',smi.qty/1000, smi.qty2) )),2) as kg	
								FROM mrp_production_rm_hasil as rmh
								INNER JOIN stock_move_items as smi ON smi.move_id = rmh.move_id AND rmh.quant_id = smi.quant_id 
								WHERE rmh.kode = '$kode' 
								GROUP BY rmh.kode
								")->row();
		if(empty($query->kg)){
			$kg = 0;
			return $kg;
		}else{
			return $query->kg;
		}
	}

	public function get_list_bahan_baku_hasil_group($kode,$additional)
	{
		$qty_kg_all = $this->get_qty_kg_all_by_kode($kode);
		return $this->db->query("SELECT rmh.kode, rmh.kode_produk, rmh.nama_produk, sum(rmh.qty) as tot_qty, rmh.uom, sum(smi.qty2) as tot_qty2, smi.uom2,
								round((sum(if(smi.uom = 'kg', smi.qty, if(smi.uom = 'gr',smi.qty/1000, smi.qty2) )) / $qty_kg_all * 100),2) persen_kg
								FROM mrp_production_rm_hasil as rmh
								INNER JOIN stock_move_items as smi ON smi.move_id = rmh.move_id AND rmh.quant_id = smi.quant_id 
								WHERE rmh.kode = '$kode' AND rmh.additional = '$additional'
								GROUP BY rmh.move_id, rmh.kode_produk")->result();
	}

	public function get_list_barang_jadi($kode)
	{
		return $this->db->query("SELECT fg.kode_produk,fg.nama_produk,fg.qty, fg.uom,
								(SELECT sum(fgh.qty) FROM mrp_production_fg_hasil fgh WHERE fgh.kode_produk = fg.kode_produk AND fg.kode = fgh.kode ) as sum_fg_hasil
								FROM mrp_production_fg_target fg WHERE fg.kode = '".$kode."' AND fg.status NOT IN ('done') ORDER BY fg.row_order")->result();
	}

	public function get_list_barang_jadi_hasil($kode,$lokasi_waste)
	{
		// tipe adjustment
		// 1=Koreksi MO, 2=Koreksi Salah INput User
		return $this->db->query("SELECT fg.kode, fg.move_id, fg.quant_id, fg.kode_produk, fg.kode_produk, fg.nama_produk, 
										fg.lot, fg.nama_grade, fg.qty, fg.uom, fg.row_order, sq.reff_note, fg.qty2, fg.uom2, fg.lebar_greige, fg.uom_lebar_greige, fg.lebar_jadi, fg.uom_lebar_jadi,(SELECT lot FROM adjustment_items adji 
									INNER JOIN adjustment adj ON adji.kode_adjustment = adj.kode_adjustment
									where adj.status = 'done' AND adji.quant_id = fg.quant_id AND adj.id_type_adjustment IN ('1','2') limit 1 ) as lot_adj, mrpin.lot as lot_asal
								FROM mrp_production_fg_hasil fg 
								INNER JOIN stock_quant sq ON fg.quant_id =  sq.quant_id
								LEFT JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
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

	public function save_rm($kode,$kode_produk,$produk,$qty,$uom,$reff,$status,$origin_prod,$additional,$row)
	{
		return $this->db->query("INSERT INTO mrp_production_rm_target (kode, move_id, kode_produk, nama_produk, qty, uom, reff_note, status, origin_prod, additional, row_order) values ('$kode','','$kode_produk','$produk','$qty','$uom','$reff','$status','$origin_prod','$additional','$row')");	
	}
	

	public function get_row_order_rm_add($kode)
	{
		$row 		= $this->db->query("SELECT max(row_order) row FROM mrp_production_rm_target WHERE kode = '$kode' AND move_id = '' AND additional = 't' ")->row_array();
		return $row['row'] + 1;
	}

	public function update_rm($kode,$kode_produk,$produk,$qty,$uom,$reff,$origin_prod,$additional,$row_order)
	{
		return $this->db->query("UPDATE mrp_production_rm_target SET kode_produk = '$kode_produk', nama_produk = '$produk', qty = '$qty', uom = '$uom', reff_note = '$reff', origin_prod = '$origin_prod' 
								WHERE kode = '$kode' AND row_order = '$row_order' AND  additional = '$additional' AND move_id = '' ");	
	}

	public function delete_rm($kode, $origin_prod,$row_order)
	{
		return $this->db->query("DELETE FROM mrp_production_rm_target WHERE kode = '".$kode."' AND row_order = '".$row_order."' AND origin_prod = '".$origin_prod."' AND  additional = 't' AND move_id = '' ");
	}

	public function get_total_fg($kode)
	{
		$query = $this->db->query("SELECT IFNULL(sum(fg.qty),0) as total_qty 
									FROM mrp_production_fg_hasil fg
									INNER JOIN mrp_production mrp ON fg.kode = mrp.kode AND fg.kode_produk = mrp.kode_produk WHERE mrp.kode = '".$kode."'");
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
     	return $this->db->query("SELECT rm.kode_produk, rm.nama_produk, rm.qty, rm.uom,  rm.status, rm.persen, rm.reff_note
								FROM mrp_production_rm_target rm 
								INNER JOIN mrp_production m ON rm.kode = m.kode 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk				
								WHERE rm.kode = '$kode' AND mp.id_category IN ('12') AND rm.additional = 'f'
								order by rm.row_order")->result();
   	}

   	public function get_dyeing_stuff_additional($kode)
   	{
     	return $this->db->query("SELECT rm.kode_produk, rm.nama_produk, rm.qty, rm.uom,  rm.status, rm.persen, rm.reff_note, rm.row_order, rm.origin_prod, rm.move_id
								FROM mrp_production_rm_target rm 
								INNER JOIN mrp_production m ON rm.kode = m.kode 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk				
								WHERE rm.kode = '$kode' AND mp.id_category IN ('12') AND rm.additional = 't'
								order by rm.move_id desc, rm.row_order asc")->result();
   	}

   	public function get_aux($kode)
   	{
   		return $this->db->query("SELECT rm.kode_produk, rm.nama_produk, rm.qty, rm.uom,  rm.status,  rm.persen, rm.reff_note
								FROM mrp_production_rm_target rm 
								INNER JOIN mrp_production m ON rm.kode = m.kode 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								WHERE rm.kode = '$kode' AND mp.id_category IN ('11') AND rm.additional = 'f'
								order by rm.row_order")->result();
   	}

   	public function get_aux_additional($kode)
   	{
   		return $this->db->query("SELECT rm.kode_produk, rm.nama_produk, rm.qty, rm.uom,  rm.status,  rm.persen, rm.reff_note, rm.row_order, rm.origin_prod, rm.move_id
								FROM mrp_production_rm_target rm 
								INNER JOIN mrp_production m ON rm.kode = m.kode 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								WHERE rm.kode = '$kode' AND mp.id_category IN ('11') AND rm.additional = 't'
								order by rm.move_id desc, rm.row_order asc")->result();
   	}

   	public function get_data_rm_target_additional_by_kode($kode,$origin_prod,$row_order)
   	{
		return $this->db->query("SELECT rm.kode, rm.kode_produk, rm.nama_produk, rm.qty, rm.uom, rm.reff_note
								FROM mrp_production_rm_target  rm
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								WHERE rm.kode = '$kode' AND rm.origin_prod = '$origin_prod' AND rm.row_order = '$row_order' AND rm.additional = 't' AND rm.move_id = '' ");						
   	}

   	public function get_data_rm_target_additional_by_kode_all($kode,$category)
   	{
		if(!empty($category)){
			$kategori_produk = ' AND '.$category;
		}else{
			$kategori_produk = '';
		}
     	return $this->db->query("SELECT rm.kode_produk, rm.nama_produk, rm.qty, rm.uom,  rm.status, rm.persen, rm.reff_note, rm.row_order, rm.origin_prod
								FROM mrp_production_rm_target rm 
								INNER JOIN mrp_production m ON rm.kode = m.kode 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk				
								WHERE rm.kode = '$kode' $kategori_produk AND rm.additional = 't' AND rm.move_id = ''
								order by rm.row_order")->result();
   	}

    public function get_route_warna($route)
	{
		return $this->db->query("SELECT * FROM  mrp_route 					
								WHERE nama_route = '$route' ORDER BY row_order ")->result();
	}

	public function get_warna_items_by_warna($warna,$varian)
	{
		return $this->db->query("SELECT * FROM warna_items WHERE id_warna = '$warna' AND id_warna_varian = '$varian' order by type_obat,row_order")->result();
	}


	public function cek_status_warna($warna)
	{
		return $this->db->query("SELECT * FROM warna WHERE id = '$warna' AND status in ('ready','requested','done')");
	}

	public function get_warna_by_kode($kode)
	{
		return $this->db->query("SELECT id_warna FROM mrp_production WHERE kode = '$kode'");
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

	public function update_mo($kode,$berat,$air,$start,$finish,$reff_note,$mesin,$qty1_std,$qty2_std,$lot_prefix,$lot_prefix_waste,$target_efisiensi,$lebar_greige,$uom_lebar_greige,$lebar_jadi,$uom_lebar_jadi,$type_production,$handling,$gramasi,$program,$alsan)
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
														   type_production = '$type_production',
														   id_handling = '$handling',
														   gramasi  = '$gramasi',
														   program  = '$program',
														   alasan = '$alsan'
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
		return $this->db->query("INSERT INTO mrp_production_rm_target (kode,move_id,kode_produk,nama_produk,qty,uom,row_order,origin_prod,status,persen,reff_note) 
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
		// get move id rm yg category produk nya tidak 11(aux) dan 12 (DYE)
		return $this->db->query("SELECT DISTINCT(rm.move_id) as move_id 
								FROM mrp_production_rm_target as rm 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk 
								WHERE rm.kode = '$kode' AND mp.id_category NOT IN ('11','12') AND rm.move_id != '' 
								ORDER BY mid(rm.move_id,3,(length(rm.move_id))-2) asc" );
	}

	public function get_move_id_rm_target_obat_by_kode($kode)
	{
		// get move id rm yg category produk nya 11(aux) dan 12 (DYE)
		return $this->db->query("SELECT DISTINCT(rm.move_id) as move_id 
								FROM mrp_production_rm_target as rm 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk 
								WHERE rm.kode = '$kode' AND mp.id_category IN ('11','12')
								ORDER BY mid(rm.move_id,3,(length(rm.move_id))-2) asc " );
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
								WHERE smi.move_id = '$move_id' AND smi.origin_prod = '$origin_prod' AND smi.status in ('ready','cancel') ORDER BY row_order")->result();
	}

	public function update_status_stock_move_produk_mo($move_id, $origin_prod, $status)
	{
		return $this->db->query("UPDATE stock_move_produk SET status = '$status' WHERE move_id = '$move_id' AND origin_prod = '$origin_prod'");
	}

	public function update_status_mrp_production_rm_target($kode, $origin_prod, $status, $move_id)
	{
		return $this->db->query("UPDATE mrp_production_rm_target SET status = '$status' WHERE kode = '$kode' AND origin_prod = '$origin_prod' AND move_id = '$move_id'");
	}

	public function get_konsumsi_bahan($kode,$status)
	{
		return $this->db->query("SELECT smi.move_id, smi.quant_id,smi.kode_produk, smi.nama_produk, 
								smi.lot, smi.qty, smi.uom,smi.origin_prod,smi.qty2,smi.uom2, rm.qty as qty_rm,rm.additional, sq.reff_note,sq.nama_grade,mp.type,
								(SELECT count(kode_produk) as jml_prod FROM stock_move_items smi2 WHERE 
									smi2.kode_produk = smi.kode_produk AND smi2.move_id = smi.move_id AND smi2.origin_prod = smi.origin_prod AND smi2.status = '$status' ) as jml_produk,
								smi.lebar_greige, smi.uom_lebar_greige, smi.lebar_jadi, smi.uom_lebar_jadi, sq.sales_order, sq.sales_group
								FROM stock_move_items smi
								INNER JOIN mrp_production_rm_target rm ON smi.origin_prod = rm.origin_prod AND rm.move_id = smi.move_id
								INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								where rm.kode = '$kode' AND smi.status = '$status' AND mp.id_category NOT IN ('11','12') order by smi.nama_produk,smi.lot ")->result();
	
	}

	public function simpan_mrp_production_fg_hasil_batch($sql)
	{
		return $this->db->query("INSERT INTO mrp_production_fg_hasil (kode,move_id,quant_id,create_date,kode_produk,nama_produk,lot,nama_grade,qty,uom,qty2,uom2,lokasi,nama_user,row_order,lebar_greige,uom_lebar_greige,lebar_jadi,uom_lebar_jadi,sales_order,sales_group) values $sql");
	}

	public function simpan_mrp_production_rm_hasil_batch($sql)
	{
		return $this->db->query("INSERT INTO mrp_production_rm_hasil (kode,move_id,kode_produk,nama_produk,lot,qty,uom,origin_prod,row_order,quant_id,additional) values $sql");
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
		
		//$result = $this->db->query("SELECT lot FROM stock_quant  WHERE lot LIKE '$lot_prefix%'  ORDER BY RIGHT(lot,$length) DESC LIMIT 1 ");
		$result  = $this->db->query("SELECT lot, MID(lot,length('$lot_prefix')+1,length(lot)-length('$lot_prefix'))  as last
									FROM stock_quant WHERE lot LIKE '$lot_prefix%' 
									ORDER BY length(left(last ,$length)) DESC, last DESC limit 1");

		if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=$row->last+1;
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
		return $this->db->query("SELECT sum(qty) as jml_qty, sum(qty2) as jml_qty2 FROM stock_move_items WHERE move_id = '$move_id' AND origin_prod = '$origin_prod' AND status = '$status' ");
	}

	public function cek_qty_waste_by_produk($kode,$kode_produk)
	{
		return $this->db->query("SELECT sum(qty) as jml_qty, sum(qty2) as jml_qty2 FROM mrp_production_fg_hasil WHERE kode = '$kode' AND kode_produk = '$kode_produk' ");
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
		return $this->db->query("SELECT sum(fg.qty) as sum_qty 
								FROM mrp_production_fg_hasil fg
								INNER JOIN mrp_production mrp ON fg.kode = mrp.kode AND fg.kode_produk = mrp.kode_produk AND mrp.kode = '$kode'");
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

	public function get_list_waste_bahan_baku_by_move_id($kode,$params)
	{
		return $this->db->query("SELECT smi.kode_produk, smi.nama_produk
								FROM stock_move_items smi
								INNER JOIN mrp_production_rm_target rm ON smi.origin_prod = rm.origin_prod AND rm.move_id = smi.move_id
								INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id 
								where rm.kode = '$kode' AND smi.nama_produk LIKE '%$params%'
								GROUP BY smi.kode_produk
								order by smi.nama_produk,smi.lot");
	}

	public function get_list_waste_barang_jadi($kode,$params)
	{
		return $this->db->query("SELECT kode_produk, nama_produk
								FROM mrp_production_fg_target
								where kode = '$kode' AND nama_produk LIKE '%$params%'
								order by nama_produk");
	}

	public function get_list_lot_waste_by_kode($kode,$kode_produk,$params)
	{
		return $this->db->query("SELECT smi.kode_produk, smi.nama_produk,smi.lot
								FROM stock_move_items smi
								INNER JOIN mrp_production_rm_target rm ON smi.origin_prod = rm.origin_prod AND rm.move_id = smi.move_id
								INNER JOIN stock_quant sq ON smi.quant_id = sq.quant_id 
								where rm.kode = '$kode'  AND smi.kode_produk = '$kode_produk' AND smi.lot LIKE '%$params%'
								GROUP BY lot
								order by smi.nama_produk,smi.lot");
	}

	public function get_nama_produk_waste_by_kode($kode_produk)
	{
		return $this->db->query("SELECT nama_produk,uom,uom_2 FROM mst_produk where kode_produk = '$kode_produk' AND kode_produk != ''");
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

	public function get_data_fg_hasil_by_kode($kode,$quant_id)
	{
		return $this->db->query("SELECT mp.create_date,mp.kode_produk, mp.nama_produk, mp.lot, mp.qty, mp.uom, mp.qty2, mp.uom2, sq.reff_note, mph.reff_note as note_head, sq.nama_grade
								FROM mrp_production_fg_hasil mp
								LEFT JOIN stock_quant sq ON mp.quant_id = sq.quant_id
								LEFT Join mrp_production mph ON mp.kode = mph.kode
								where mp.kode = '$kode' AND mp.quant_id = '$quant_id' ");
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

	public function get_nama_warna_by_id($id_warna)
	{
		$result = $this->db->query("SELECT nama_warna FROM warna where id = '$id_warna'")->row_array();
		return $result['nama_warna'];
	}


	public function get_qty2_smi_kain_by_kode($move_id)
    {
   		return $this->db->query("SELECT sum(smi.qty2) jml_qty2
   								FROM stock_move_items as smi
								INNER JOIN mst_produk as mp ON smi.kode_produk = mp.kode_produk
								INNER JOIN mst_category  as mc ON mp.id_category = mc.id
								WHERE smi.move_id = '$move_id' AND mc.nama_category LIKE '%kain%'");
    }

	public function get_list_varian_by_id($id_warna)
	{
		return $this->db->query("SELECT id, id_warna, nama_varian FROM warna_varian where id_warna = '$id_warna' ORDER BY id asc")->result();
	}

	public function cek_mesin_by_dept_id($dept_id)
	{
		return $this->db->query("SELECT mc_id,nama_mesin FROM mesin where dept_id = '$dept_id'");
	}

	public function cek_mesin_by_mrp($kode)
	{
		return $this->db->query("SELECT mc_id FROM mrp_production where kode = '$kode'");
	}

	public function cek_rm_target_additional($kode)
	{
		return $this->db->query("SELECT rm.kode, rm.kode_produk, rm.nama_produk, rm.qty, rm.uom, rm.reff_note
								FROM mrp_production_rm_target  rm
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
								WHERE rm.kode = '$kode' AND rm.additional = 't' AND rm.move_id = '' ");		

	}

	public function cek_mrp_production_fg_hasil($kode)
	{
		return $this->db->query("SELECT kode FROM mrp_production_fg_hasil WHERE kode = '$kode'");
	}

	public function cek_move_id_rm_additional_by_kode($kode,$origin_prod,$row_order)
	{
		return $this->db->query("SELECT move_id FROM mrp_production_rm_target WHERE kode = '$kode' AND origin_prod = '$origin_prod' AND row_order = '$row_order'");
	}

	public function get_list_move_id_rm_obat_by_kode($kode,$additional)
	{
		if(!empty($additional)){
			$add = " AND rm.additional = '".$additional."' ";
		}else{
			$add = '';
		}

		return $this->db->query("SELECT rm.move_id, rm.additional
								FROM mrp_production_rm_target as rm 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk 
								WHERE rm.kode = '$kode' AND move_id != ''  AND mp.id_category IN ('11','12') $add
								GROUP BY move_id
								ORDER BY mid(move_id,3,(length(move_id))-2) asc");
	}

	public function get_list_move_id_rm_by_kode($kode,$additional)
	{
		if(!empty($additional)){
			$add = " AND rm.additional = '".$additional."' ";
		}else{
			$add = '';
		}

		return $this->db->query("SELECT rm.move_id, rm.additional
								FROM mrp_production_rm_target as rm 
								INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk 
								WHERE rm.kode = '$kode' AND move_id != ''  AND mp.id_category NOT IN ('11','12') $add
								GROUP BY move_id
								ORDER BY mid(move_id,3,(length(move_id))-2) asc");
	}

	public function get_list_rm_target_obat_by_move($kode,$move_id,$type)
	{
		if(!empty($type)){
			$type_obat = " AND mp.id_category IN ('".$type."') ";
		}else{
			$type_obat = " AND mp.id_category IN ('11','12')  ";
		}

		return $this->db->query("SELECT rm.kode_produk, rm.nama_produk, rm.qty, rm.uom,  rm.status,  rm.persen, rm.reff_note, rm.row_order, rm.origin_prod
							 FROM mrp_production_rm_target rm 
							 INNER JOIN mrp_production m ON rm.kode = m.kode 
							 INNER JOIN mst_produk mp ON rm.kode_produk = mp.kode_produk
							 WHERE rm.kode = '$kode' AND move_id = '$move_id' $type_obat
							 order by rm.row_order asc")->result();
	}

	public function get_sum_smi_rm_target_by_kode($move_id)
	{
		return $this->db->query("SELECT sum(qty) as tot_qty, uom, sum(qty2) as tot_qty2, uom2, count(lot) as tot_gl FROM stock_move_items where move_id = '$move_id'");
	}

	public function get_no_greige_out_by_origin($origin)
	{
		$query =  $this->db->query("SELECT move_id, (select kode FROM pengiriman_barang where move_id = sm.move_id) as kode_out
								FROM stock_move sm
								where sm.origin  = '$origin' AND sm.method = 'GRG|OUT' 
								ORDER BY sm.row_order asc LIMIT 1 ")->row_array();	
		return 	$query['kode_out'];
	}

	public function get_list_bahan_baku_additional($kode)
	{
		return $this->db->query("SELECT rm.status,rm.kode_produk,rm.nama_produk,rm.qty,rm.uom,rm.kode,rm.row_order,rm.origin_prod,rm.move_id, mp.type, rm.reff_note,
									(SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = rm.move_id And smi.origin_prod = rm.origin_prod AND smi.status = 'ready' ) as sum_qty,
									(SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = rm.move_id And smi.origin_prod = rm.origin_prod AND smi.status = 'done' ) as sum_qty_done,
									(SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = rm.move_id And smi.origin_prod = rm.origin_prod AND smi.status = 'cancel' ) as sum_qty_cancel
								FROM mrp_production_rm_target rm 
								INNER JOIN mst_produk mp ON mp.kode_produk = rm.kode_produk 
								INNER JOIN mrp_production mrp ON mrp.kode = rm.kode
								WHERE rm.kode = '".$kode."' AND mp.id_category NOT IN ('11','12') AND rm.status NOT IN ('done') AND rm.additional = 't' 
								ORDER BY mid(rm.move_id,3,(length(rm.move_id))-2) asc, rm.row_order asc")->result();
		
	}

	public function get_list_produk_rm($name)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom, uom_2 
								FROM  mst_produk 	
								WHERE CONCAT(kode_produk,nama_produk)  LIKE '%$name%'  and type = 'stockable' AND status_produk = 't' AND id_category NOT IN ('11','12')  
								ORDER BY bom,nama_produk LIMIT 100  ")->result_array();
	}

	public function get_produk_additonal_by_id($kode_produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom, uom_2 FROM  mst_produk WHERE kode_produk = '$kode_produk' AND type = 'stockable' ");
	}
/*
	public function get_stock_move_items_by_kode($move_id,$quant_id,$kode_produk,$row_order){
		return $this->db->query("SELECT quant_id, move_id, kode_produk, nama_produk, lot, qty, uom, qty2, uom2, origin_prod, status
								FROM stock_move_items 
								WHERE move_id = '$move_id' AND quant_id = '$quant_id' AND kode_produk = '$kode_produk' AND row_order = '$row_order' ");

	}
*/
	public function get_additional_true_false_by_kode($kode,$move_id,$kode_produk,$origin_prod)
	{
		$result = $this->db->query("SELECT additional FROM mrp_production_rm_target 
									WHERE kode = '$kode' AND move_id = '$move_id' AND kode_produk = '$kode_produk' AND origin_prod = '$origin_prod' ")->row_array();
		return $result['additional'];
	}

	public function get_qty_rm_by_kode($kode,$move_id,$kode_produk,$origin_prod)
	{
		$result = $this->db->query("SELECT qty FROM mrp_production_rm_target 
									WHERE kode = '$kode' AND move_id = '$move_id' AND kode_produk = '$kode_produk' AND origin_prod = '$origin_prod' ")->row_array();
		return $result['qty'];
	}

	public function get_data_bom($kode_bom)
	{
		return $this->db->query("SELECT b.kode_bom, b.tanggal, b.nama_bom, b.kode_produk, b.nama_produk, b.qty, b.uom, b.status_bom, sat.nama_status
								FROM bom b
								LEFT JOIN mst_status sat ON b.status_bom = sat.kode 
								WHERE b.kode_bom = '".$kode_bom."'");
	}

	public function get_bom_by_kode_produk($kode_produk)
	{
		return $this->db->query("SELECT b.kode_bom, b.kode_produk, b.qty, bi.qty as qty_item
								FROM bom b
								INNER JOIN bom_items bi ON b.kode_bom = bi.kode_bom
								where b.kode_produk = '$kode_produk' AND b.status_bom = 't' 
								ORDER BY b.tanggal desc
								LIMIT 1 ");
	}

	public function get_list_mrp_production_fg_hasil_cons_no_by_kode($kode)
	{
		return $this->db->query("SELECT fg.kode, fg.move_id, fg.quant_id, fg.kode_produk, fg.nama_produk, fg.lot, fg.nama_grade, fg.qty, fg.uom, fg.qty2, fg.uom2, 
								fg.consume, fg.row_order,(SELECT lot FROM adjustment_items adji 
									INNER JOIN adjustment adj ON adji.kode_adjustment = adj.kode_adjustment
									where adj.status = 'done' AND adji.quant_id = fg.quant_id limit 1 ) as lot_adj
								from mrp_production_fg_hasil fg
								where fg.kode = '$kode'
								and fg.lokasi like '%stock%' and fg.consume = 'no'
								order by fg.row_order asc")->result();
	}

	public function get_sum_qty_rm_done($kode, $status)
	{
		return $this->db->query("SELECT sum(mtr) as mtr, sum(kg) as kg
							from (
							SELECT smi.kode_produk, smi.nama_produk, 
										sum(if(mp.uom = 'Mtr',smi.qty,'')) as mtr,
										sum(if(mp.uom = 'kg', smi.qty, smi.qty2)) as kg
							from mrp_production_rm_hasil rm
							INNER JOIN stock_move_items smi ON rm.quant_id = smi.quant_id AND rm.move_id = smi.move_id
							INNER JOIN mst_produk mp ON smi.kode_produk = mp.kode_produk
							WHERE rm.kode ='$kode' AND smi.status = '$status'
							GROUP BY smi.kode_produk
							) as gp");
	}


	public function get_sum_qty_rm_ready($kode, $status)
	{
		return $this->db->query("SELECT sum(mtr) as mtr, sum(kg) as kg
							from (
								SELECT smi.kode_produk, smi.nama_produk, 
										sum(if(mp.uom = 'Mtr',smi.qty,'')) as mtr,
										sum(if(mp.uom = 'kg', smi.qty, smi.qty2)) as kg
							from mrp_production_rm_target rm
							INNER JOIN stock_move_items smi ON rm.origin_prod = smi.origin_prod AND rm.move_id = smi.move_id AND rm.kode_produk = smi.kode_produk
							INNER JOIN mst_produk mp ON smi.kode_produk = mp.kode_produk
							WHERE rm.kode ='$kode' AND smi.status = '$status'
							GROUP BY smi.kode_produk
							) as gp");
	}

	public function get_sum_qty_rm_waste($kode,$status)
	{
		return $this->db->query("SELECT sum(mtr) as mtr, sum(kg) as kg
							from (
							SELECT fg.kode, smi.kode_produk, smi.nama_produk, 
										sum(if(mp.uom = 'Mtr',smi.qty,'')) as mtr,
										sum(if(mp.uom = 'kg', smi.qty, smi.qty2)) as kg
							from mrp_production_fg_target  fgt
							INNER JOIN mrp_production_fg_hasil fg ON fg.kode = fgt.kode AND fg.kode_produk != fgt.kode_produk
							INNER JOIN stock_move_items smi ON fg.quant_id = smi.quant_id AND fg.move_id = smi.move_id
							INNER JOIN mst_produk mp ON smi.kode_produk = mp.kode_produk
							WHERE fg.kode ='$kode' AND smi.status = '$status'   AND lokasi LIKE '%waste%' AND fg.lot LIKE 'F|%'
							GROUP BY smi.kode_produk
							) as gp");
	}


	public function get_sum_qty_fg($kode, $consume)
	{
		return $this->db->query("SELECT sum(mtr) as mtr, sum(kg) as kg
							from (
							SELECT smi.kode_produk, smi.nama_produk, 
										sum(if(mp.uom = 'Mtr',smi.qty,'')) as mtr,
										sum(if(mp.uom = 'kg', smi.qty, smi.qty2)) as kg
							from mrp_production_fg_target  fgt
							INNER JOIN mrp_production_fg_hasil fg ON fg.kode = fgt.kode AND fg.kode_produk = fgt.kode_produk
							INNER JOIN stock_move_items smi ON fg.quant_id = smi.quant_id AND fg.move_id = smi.move_id
							INNER JOIN mst_produk mp ON smi.kode_produk = mp.kode_produk
							WHERE fg.kode ='$kode' AND smi.status = 'done' and fg.consume = '$consume'  AND fg.lokasi LIKE '%stock%'
							GROUP BY smi.kode_produk
							) as gp");
	}

	public function get_sum_qty_fg_produce($kode)
	{
		return $this->db->query("SELECT sum(mtr) as mtr, sum(kg) as kg
							from (
							SELECT smi.kode_produk, smi.nama_produk, 
										sum(if(mp.uom = 'Mtr',smi.qty,'')) as mtr,
										sum(if(mp.uom = 'kg', smi.qty, smi.qty2)) as kg
							from mrp_production_fg_target  fgt
							INNER JOIN mrp_production_fg_hasil fg ON fg.kode = fgt.kode AND fg.kode_produk = fgt.kode_produk
							INNER JOIN stock_move_items smi ON fg.quant_id = smi.quant_id AND fg.move_id = smi.move_id
							INNER JOIN mst_produk mp ON smi.kode_produk = mp.kode_produk
							WHERE fg.kode ='$kode' AND smi.status = 'done'  AND fg.lokasi LIKE '%stock%'
							GROUP BY smi.kode_produk
							) as gp");
	}


	public function get_sum_qty_fg_waste($kode)
	{
		return $this->db->query("SELECT sum(mtr) as mtr, sum(kg) as kg
							from (
							SELECT smi.kode_produk, smi.nama_produk, 
										sum(if(mp.uom = 'Mtr',smi.qty,'')) as mtr,
										sum(if(mp.uom = 'kg', smi.qty, smi.qty2)) as kg
							from mrp_production_fg_target  fgt
							INNER JOIN mrp_production_fg_hasil fg ON fg.kode = fgt.kode AND fg.kode_produk = fgt.kode_produk
							INNER JOIN stock_move_items smi ON fg.quant_id = smi.quant_id AND fg.move_id = smi.move_id
							INNER JOIN mst_produk mp ON smi.kode_produk = mp.kode_produk
							WHERE fg.kode ='$kode' AND smi.status = 'done' AND fg.lokasi LIKE '%waste%' AND fg.lot LIKE 'F|%'
							GROUP BY smi.kode_produk
							) as gp");
	}

	public function get_sum_qty_fg_adj($kode)
	{
		// tipe adjustment
		// 1=Koreksi MO, 2=Koreksi Salah INput User
		return $this->db->query("SELECT sum(mtr) as mtr, sum(kg) as kg
							from (
							SELECT smi.kode_produk, smi.nama_produk, 
										sum(if(mp.uom = 'Mtr',smi.qty,'')) as mtr,
										sum(if(mp.uom = 'kg', smi.qty, smi.qty2)) as kg
							from mrp_production_fg_target  fgt
							INNER JOIN mrp_production_fg_hasil fg ON fg.kode = fgt.kode AND fg.kode_produk = fgt.kode_produk
							INNER JOIN adjustment_items adji ON fg.quant_id = adji.quant_id
							INNER JOIN adjustment adj ON adji.kode_adjustment = adj.kode_adjustment
							INNER JOIN stock_move_items smi ON adji.quant_id = smi.quant_id AND adji.move_id = smi.move_id
							INNER JOIN mst_produk mp ON smi.kode_produk = mp.kode_produk
							WHERE fg.kode ='$kode' AND smi.status = 'done'  AND fg.lokasi LIKE '%stock%' AND adj.status = 'done' AND adj.id_type_adjustment IN ('1','2')
							GROUP BY smi.kode_produk
							) as gp");
	}

	public  function get_qty_target_mrp($kode)
	{
		return $this->db->query("SELECT qty FROM mrp_production WHERE kode = '$kode'");
	}

	public function get_data_mrp_fg_hasil_by_quant($kode,$quant_id)
	{
		$this->db->select("kode, create_date, kode_produk, nama_produk, lot, nama_grade, qty, uom, qty2, uom2, lokasi, consume ");
		$this->db->from("mrp_production_fg_hasil");
		$this->db->where('kode',$kode);
		$this->db->where('quant_id',$quant_id);
		$query = $this->db->get();
		return $query->row();
	}

	public function cek_lokasi_by_quant($quant_id)
	{
		$this->db->select('lokasi');
		$this->db->from('stock_quant');
		$this->db->where('quant_id',$quant_id);
		$query = $this->db->get();
		return $query->row();
	}

	public function cek_quant_acc_stock_move_items_by_kode($thn,$bln,$deptid,$type,$kode,$quant_id)
	{	
		$where = array('periode_th'=>$thn, 'periode_bln'=>$bln, 'dept_id_mutasi'=>$deptid, 'type'=>$type, 'kode_transaksi' => $kode, 'quant_id' => $quant_id);
		$this->db->select('kode_transaksi, kode_produk, nama_produk');
		$this->db->from('acc_stock_move_items');
		$this->db->where($where);
		$query  = $this->db->get();
		return $query;
	}

	public function cek_route_after_produce_by_origin($origin,$move_id)
	{
		$this->db->select('move_id, method');
		$this->db->from('stock_move');
		$this->db->where('origin', $origin);
		$this->db->like('source_move',$move_id);
		$this->db->where('status', 'ready');
		$this->db->order_by('cast(mid(move_id,3,(length(move_id))-2) as unsigned) asc' );
		$query = $this->db->get();
		return $query;
	}

	public function get_kode_pengiriman_barang_by_move_id($move_id)
	{
		$this->db->select('kode');
		$this->db->from('pengiriman_barang');
		$this->db->where('move_id', $move_id);
		$query = $this->db->get();
		$result = $query->row();
		return $result->kode;
	}


	public function get_kode_penerimaan_barang_by_move_id($move_id)
	{
		$this->db->select('kode');
		$this->db->from('penerimaan_barang');
		$this->db->where('move_id', $move_id);
		$query = $this->db->get();
		$result = $query->row();
		return $result->kode;
	}

	public function cek_lot_adj_by_quant($quant_id)
	{
		$this->db->select("adji.quant_id");
		$this->db->from('adjustment_items adji');
		$this->db->join("adjustment adj", "adj.kode_adjustment = adji.kode_adjustment", "inner");
		$this->db->where("adj.status","done");
		$this->db->where("adji.quant_id",$quant_id);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function update_alasan_hold_mrp_production($kode,$alasan,$status)
	{
		$this->db->query("UPDATE mrp_production set alasan = '$alasan', status_before_hold = '$status' WHERE kode ='$kode' ");
	}

	public function get_status_before_hold($kode)
	{
		return $this->db->query("SELECT status_before_hold FROM mrp_production where kode = '$kode'");
	}

	public function simpan_done_mo($kode,$deptid,$rm_done_mtr,$rm_done_kg,$fg_prod_mtr,$fg_prod_kg,$fg_waste_mtr,$fg_waste_kg,$fg_adj_mtr,$fg_adj_kg,$status)
	{	
		$tgl = date('Y-m-d H:i:s');
		return $this->db->query("INSERT INTO mrp_production_done (kode,tanggal,dept_id,con_mtr,con_kg,prod_mtr,prod_kg, waste_mtr,waste_kg,adj_mtr,adj_kg,status) 
								values ('$kode','$tgl','$deptid','$rm_done_mtr','$rm_done_kg','$fg_prod_mtr','$fg_prod_kg','$fg_waste_mtr','$fg_waste_kg','$fg_adj_mtr','$fg_adj_kg','$status') ");

	}

	public function cek_mrp_inlet_by_quant_id($quant_id,$status)
	{
		$this->db->where_not_in('status',$status);
		$this->db->where('quant_id',$quant_id);
		$result = $this->db->get('mrp_inlet');
		return $result->num_rows();
	}

	public function cek_mrp_cacat_by_quant($kode,$quant_id)
	{
		$this->db->select("quant_id");
		$this->db->from("mrp_production_cacat");
		$this->db->where('kode',$kode);
		$this->db->where('quant_id',$quant_id);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function cek_qty_smi_by_kode($move_id,$quant_id,$lot) 
	{

		$this->db->where('move_id',$move_id);
		$this->db->where('quant_id',$quant_id);
		$this->db->where('lot',$lot);
		$this->db->where('status','ready');
		$query = $this->db->get("stock_move_items");
		return $query->row();
	}


	public function get_data_lot_mrp_fg_hasil_by_lot($kode,$lot) 
	{
		$this->db->where('lot',$lot);
		$this->db->where('kode',$kode);
		$query = $this->db->get('mrp_production_fg_hasil');
		return $query->row();
	}


}