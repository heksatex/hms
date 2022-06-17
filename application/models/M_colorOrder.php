<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_colorOrder extends CI_Model
{

	//var $table 		  = 'color_order';
	var $column_order = array(null, 'kode_co', 'tanggal', 'kode_sc','buyer_code', 'status', 'notes');
	var $column_search= array('kode_co', 'tanggal', 'kode_sc','buyer_code', 'status', 'notes');
	var $order  	  = array('kode_co' => 'desc');

	var $column_order2  = array(null, 'a.sales_order', 'a.buyer_code', 'msg.nama_sales_group');
	var $column_search2 = array('a.sales_order', 'a.buyer_code', 'msg.nama_sales_group');
	var $order2  	    = array('a.create_date' => 'desc');

	//var $table3 	    = 'sales_color_line';
	var $column_order3  = array(null, 'ow','tanggal_ow','nama_produk', 'kode_warna', 'qty', 'uom', 'lebar_jadi', 'nama_handling','gramasi', 'nama_route','piece_info','reff_notes');
	var $column_search3 = array('ow','tanggal_ow','nama_produk', 'kode_warna', 'qty', 'uom','lebar_jadi', 'nama_handling', 'gramasi', 'piece_info', 'nama_route', 'reff_notes');
	var $order3  	    = array('tanggal_ow' => 'asc');


	public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
		$this->load->model('_module');
		$this->db2 = $this->load->database('odoo',TRUE);
	}

	private function _get_datatables_query()
	{

		$this->db->select("co.kode_co,co.tanggal,co.kode_sc,co.buyer_code,co.status, co.notes, mmss.nama_status");
		$this->db->from("color_order co");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=co.status", "inner");
		//$this->db->from($this->table);

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
		$this->db->select("co.kode_co,co.tanggal,co.kode_sc,co.buyer_code,co.status, co.notes, mmss.nama_status");
		$this->db->from("color_order co");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=co.status", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		return $this->db->count_all_results();
	}

	public function simpan($kode_co, $kode_sc, $buyer_code, $tgl_sj, $note, $tgl, $route)
	{
		$query = $this->db->query("INSERT INTO color_order (kode_co, kode_sc, buyer_code, tanggal_sj, notes, tanggal, route, status) 
								 values ('$kode_co', '$kode_sc', '$buyer_code', '$tgl_sj', '$note', '$tgl', '$route', 'draft')");
		return $query;
	}

	/*
	public function hapus($kode_co)
	{
		return $this->db->delete('color_order', array("kode_co" => $kode_co));
	}
	*/


	public function kode_co()
	{	
		$last_no = $this->db->query("SELECT mid(kode_co,3,(length(kode_co))-2) as 'nom' 
						 from color_order where left(kode_co,2)='CO'
						 order by cast(mid(kode_co,3,(length(kode_co))-2) as unsigned) desc LIMIT 1  ");
		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		$kode = 'CO'.$no;
		return $kode;
	}

	public function ubah($kode_co, $kode_sc, $buyer_code, $tgl_sj, $note, $tgl, $route)
	{
		$query = $this->db->query("UPDATE color_order SET kode_sc = '$kode_sc',  buyer_code = '$buyer_code',
														  tanggal_sj = '$tgl_sj', notes = '$note', 
														  route = '$route'
								    WHERE kode_co = '$kode_co' ");
		return $query;
	}

	public function get_data_by_code($kode_co)
	{
		$query = $this->db->query("SELECT * FROM color_order where kode_co = '".$kode_co."' ");
		return $query->row();
	}

	public function get_data_detail_by_code($kode_co)
	{
		$query = $this->db->query("SELECT a.kode_co, a.ow, a.kode_produk, a.nama_produk,  a.qty, a.uom, a.reff_notes, a.status, a.row_order, 
												 b.kode as kode_route,b.nama as route_co,
												 a.id_warna, c.nama_warna, c.kode_warna, a.id_handling, d.nama_handling, a.lebar_jadi, a.uom_lebar_jadi, a.gramasi, a.reff_notes_mkt
									FROM color_order_detail a 
									LEFT JOIN route_co b ON a.route_co = b.kode
									LEFT JOIN warna c ON a.id_warna = c.id
									LEFT JOIN mst_handling d ON d.id = a.id_handling
									where a.kode_co = '".$kode_co."' ORDER BY row_order");
		return $query->result();
	}


	public function ubah_status_color_order($kode_co, $status)
	{
		return $this->db->query("UPDATE color_order SET status = '".$status."'
								 WHERE  kode_co = '".$kode_co."'");
	}

	public function ubah_status_color_order_details($kode_co, $status)
	{
		return $this->db->query("UPDATE color_order_detail SET status = '".$status."'
								 WHERE  kode_co = '".$kode_co."'");
	}

	
	public function get_list_route_co()
	{
		return $this->db->query("SELECT kode, nama FROM route_co ORDER BY kode ")->result();
	}

	public function get_nama_route_by_kode($kode)
	{
		return $this->db->query("SELECT nama FROM route_co WHERE kode  = '$kode'");
	}

	private function _get_datatables2_query()
	{
		$this->db->distinct();
		$this->db->select(" a.sales_order, a.buyer_code, a.sales_group, msg.nama_sales_group");
		$this->db->from("sales_contract a");
		$this->db->join("sales_color_line b","a.sales_order = b.sales_order","inner");
		$this->db->join("mst_sales_group msg","a.sales_group = msg.kode_sales_group","INNER");
	
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

	function get_datatables2()
	{
		$this->_get_datatables2_query();
		$this->db->where("a.status", 'waiting_color');
		$this->db->where_not_in("b.ow", '');
		$this->db->where('a.sales_order NOT IN (SELECT kode_sc FROM color_order)', NULL, FALSE);

		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2()
	{
		$this->_get_datatables2_query();
		$this->db->where("a.status", 'waiting_color');
		$this->db->where_not_in("b.ow", '');
		$this->db->where('a.sales_order NOT IN (SELECT kode_sc FROM color_order)', NULL, FALSE);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2()
	{
	  	$this->db->distinct();
		$this->db->select(" a.sales_order, a.buyer_code, a.sales_group, msg.nama_sales_group");
		$this->db->from("sales_contract a");
		$this->db->join("sales_color_line b","a.sales_order = b.sales_order","inner");
		$this->db->join("mst_sales_group msg","a.sales_group = msg.kode_sales_group","INNER");
		$this->db->where("a.status", 'waiting_color');
		$this->db->where_not_in("b.ow", '');
		$this->db->where('a.sales_order NOT IN (SELECT kode_sc FROM color_order)', NULL, FALSE);
		return $this->db->count_all_results();
	}


	private function _get_datatables3_query()
	{
		
		//$this->db->from($this->table3);
		$this->db->select("a.kode_produk, a.nama_produk, a.id_warna, a.qty, a.uom, a.piece_info, a.row_order, a.ow, a.tanggal_ow, b.nama_warna, a.lebar_jadi,a.uom_lebar_jadi, a.id_handling, c.nama_handling, a.gramasi, a.route_co, rc.nama as nama_route, a.reff_notes");
		$this->db->from("sales_color_line a");
		$this->db->JOIN("warna b", "a.id_warna = b.id", "LEFT");
		$this->db->JOIN("mst_handling c", "a.id_handling =  c.id", "LEFT");
		$this->db->JOIN("route_co rc","rc.kode = a.route_co","LEFT");

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

	function get_datatables3($sales_order)
	{
		$this->db->where("sales_order", $sales_order);
		$this->db->where_not_in("ow", '');
		$this->_get_datatables3_query();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered3($sales_order)
	{
		$this->db->where("sales_order", $sales_order);
		$this->db->where_not_in("ow", '');
		$this->_get_datatables3_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all3($sales_order)
	{
		$this->db->where("sales_order", $sales_order);
		$this->db->where_not_in("ow", '');
		$this->_get_datatables3_query();
		return $this->db->count_all_results();
	}

	public function list_detail_color($so)
	{
		
		return $this->db2->query("SELECT scli.product_id prod_id, pp.name_template as product, pc.id colorid, 
									pc.name as color, scl.qty, pu.name as uom, scl.piece_info as reff
								FROM sale_order so
								INNER JOIN sale_color_line scl ON so.id=scl.order_id
								INNER JOIN product_color pc ON scl.color_id=pc.id
								INNER JOIN product_uom pu ON scl.product_uom_id=pu.id
								INNER JOIN sale_contract_line scli ON scli.id =  scl.contract_line_id
								INNER JOIN product_product pp ON scli.product_id = pp.id
								WHERE so.name='$so'")->result();

	}

	public function simpan_color_detail($kode_co,$ow,$kode_produk,$nama_produk,$id_warna,$qty,$uom,$reff,$status,$row_order,$route_co,$id_handling,$gramasi,$lebar_jadi,$uom_lebar_jadi,$reff_mkt)
	{
		$tgl = date('Y-m-d H:i:s');
		return $this->db->query("INSERT INTO color_order_detail (kode_co, tanggal, ow, kode_produk, nama_produk, id_warna, qty, uom, reff_notes, status, row_order, route_co,id_handling,gramasi,lebar_jadi,uom_lebar_jadi,reff_notes_mkt) 
								values ('$kode_co','$tgl','$ow','$kode_produk','$nama_produk','$id_warna','$qty','$uom','$reff','$status','$row_order','$route_co','$id_handling','$gramasi','$lebar_jadi','$uom_lebar_jadi','$reff_mkt') ");
	}

	public function update_one_is_approve_color_lines($sales_order, $id_warna, $is_approve, $row_order)
	{

		return $this->db->query("UPDATE sales_color_line SET is_approve = '$is_approve' WHERE sales_order = '$sales_order' AND id_warna = '$id_warna' AND row_order = '$row_order' ");
	}

	public function get_row_order_color_detail($kode_co)
	{
		return $this->db->query("SELECT row_order as jml  FROM color_order_detail WHERE kode_co = '$kode_co' order by row_order desc");
	}

	public function hapus_color_detail($kode_co, $row_order)
	{
		return $this->db->query("DELETE FROM color_order_detail WHERE kode_co = '$kode_co' AND row_order = '$row_order' ");
	}

	public function get_color_detail_by_id($kode_co, $row_order)
	{
		return $this->db->query("SELECT a.kode_co, a.kode_produk, a.nama_produk,  a.qty, a.uom, a.reff_notes, a.status, a.row_order, a.ow, a.route_co, a.lebar_jadi, a.uom_lebar_jadi, a.id_handling, a.id_warna, b.nama_warna, a.gramasi
								 FROM color_order_detail a 
								 LEFT JOIN warna b ON a.id_warna = b.id 
								 WHERE a.kode_co = '$kode_co' AND a.row_order = '$row_order' ");
	}

	public function ubah_color_detail($kode_co, $route_co, $row_order, $qty, $reff, $handling, $lebar_jadi, $uom_lebar_jadi,$gramasi)
	{
		return $this->db->query("UPDATE color_order_detail SET qty =  '$qty', reff_notes='$reff', route_co = '$route_co', id_handling = '$handling', lebar_jadi = '$lebar_jadi', uom_lebar_jadi = '$uom_lebar_jadi', gramasi = '$gramasi'
								 WHERE kode_co  = '$kode_co' AND row_order = '$row_order'");
	}


	public function get_route_product($route)
	{
		return $this->db->query("SELECT *
								FROM mrp_route mr								
								WHERE mr.nama_route = '$route' ORDER BY row_order asc ")->result();
	}

	public function get_nama_dept_by_kode($kode)
	{
		return $this->db->query("SELECT * FROM departemen  WHERE kode = '$kode'");
	}

	public function cek_nama_product($produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom, uom_2 FROM mst_produk where nama_produk = '$produk'");
	}

	public function simpan_product_batch($sql)
	{
		return $this->db->query("INSERT INTO mst_produk (kode_produk, nama_produk, create_date, lebar_jadi, uom_lebar_jadi, id_category, bom, type, uom, uom_2, status_produk) values $sql ");
	}

	public function get_kode_product()
	{
		$last_no = $this->db->query("SELECT mid(kode_produk,3,(length(kode_produk))-2) as 'nom' 
						 from mst_produk where left(kode_produk,2)='MF'
						 order by cast(mid(kode_produk,3,(length(kode_produk))-2) as unsigned) desc LIMIT 1  ");
		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom ;
		}
		//$kode = 'MF'.$no;
		return $no;
	}

	public function lock_tabel($table)
	{
		 $this->db->query("LOCK TABLES $table ");
	}

	public function unlock_tabel()
	{
		 $this->db->query("UNLOCK TABLES");
	}

	public function update_kode_product_color_detail_batch($sql)
	{
		return $this->db->query("$sql");
	}

	public function cek_bom($kode_produk)
	{
		return $this->db->query("SELECT kode_produk,kode_bom,qty FROM bom WHERE kode_produk  = '$kode_produk'");
	}

	public function get_kode_bom()
	{
		$last_no = $this->db->query("SELECT mid(kode_bom,3,(length(kode_bom))-2) as 'nom' 
						 from bom where left(kode_bom,2)='BM'
						 order by cast(mid(kode_bom,3,(length(kode_bom))-2) as unsigned) desc LIMIT 1  ");
		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		//$kode = 'BM'.$no;
		return $no;
		
	}

	public function simpan_bom_batch($sql)
	{
		return $this->db->query("INSERT INTO bom (kode_bom,tanggal,nama_bom,kode_produk,nama_produk,qty,uom) values $sql ");
	}

	public function simpan_bom_items_batch($sql)
	{
		return $this->db->query("INSERT INTO bom_items(kode_bom,kode_produk,nama_produk,qty,uom,row_order) values $sql");
	}

	public function get_nama_produk_by_kode($kode_produk)
	{
		return $this->db->query("SELECT nama_produk FROM mst_produk WHERE kode_produk = '$kode_produk'");
	}

	public function get_kode_stock_move()
	{
		$last_no = $this->db->query("SELECT mid(move_id,3,(length(move_id))-2) as 'nom' 
						 from stock_move where left(move_id,2)='SM'
						 order by cast(mid(move_id,3,(length(move_id))-2) as unsigned) desc LIMIT 1  ");
		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		//$kode = 'SM'.$no;
		return $no;
		
	}

	public function create_stock_move_batch($sql)
	{
		return $this->db->query("INSERT INTO stock_move (move_id,create_date,origin,method,lokasi_dari,lokasi_tujuan,status,row_order,source_move) values $sql " );

	}

	public function create_stock_move_produk_batch($sql)
	{
		return $this->db->query("INSERT INTO stock_move_produk (move_id,kode_produk,nama_produk,qty,uom,status,row_order,origin_prod) 
								values $sql ");
	}

	public function get_kode_penerimaan($deptid)
	{
		$kode=$deptid."/IN/".date("y") .  date("m");
        $result=$this->db->query("SELECT kode FROM penerimaan_barang WHERE month(tanggal)='" . date("m") . "' AND year(tanggal)='" . date("Y") . "'  AND kode LIKE'%".$deptid."%' ORDER BY RIGHT(kode,5) DESC LIMIT 1");
        if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=substr($row->kode,-5)+1;
        }else{
            $dgt="1";
        }
        //$dgt=substr("00000" . $dgt,-5);            
        //$kode_in=$kode . $dgt;
        return $dgt;
	}

	public function simpan_penerimaan_batch($sql)
	{
		return $this->db->query("INSERT INTO penerimaan_barang (kode,tanggal,tanggal_transaksi,tanggal_jt,reff_note,status,dept_id,origin,move_id,reff_picking,lokasi_dari,lokasi_tujuan)  values $sql ");
	}

	public function simpan_penerimaan_items_batch($sql)
	{
		return $this->db->query("INSERT INTO penerimaan_barang_items  (kode,kode_produk,nama_produk,qty,uom,status_barang,row_order) 
								values $sql ");
	}

	public function get_kode_pengiriman($deptid)
	{
		$kode=$deptid."/OUT/".date("y") .  date("m");
        $result=$this->db->query("SELECT kode FROM pengiriman_barang WHERE month(tanggal)='" . date("m") . "' AND year(tanggal)='" . date("Y") . "' AND kode LIKE'%".$deptid."%'ORDER BY RIGHT(kode,5) DESC LIMIT 1");
        if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=substr($row->kode,-5)+1;
        }else{
            $dgt="1";
        }
        //$dgt=substr("00000" . $dgt,-5);            
        $kode_out=$kode . $dgt;
        return $dgt;
	}

	public function simpan_pengiriman_batch($sql)
	{
		return $this->db->query("INSERT INTO pengiriman_barang (kode,tanggal,tanggal_transaksi,tanggal_jt,reff_note,status,dept_id,origin,move_id,lokasi_dari,lokasi_tujuan)  VALUES $sql");
	}

	public function simpan_pengiriman_items_batch($sql)
	{
			return $this->db->query("INSERT INTO pengiriman_barang_items(kode,kode_produk,nama_produk,qty,uom,status_barang,row_order,origin_prod) 
								values $sql ");
	}

	public function update_reff_picking_pengiriman_batch($sql)
	{
		return $this->db->query(" $sql ");
	}

	public function get_kode_mo()
	{
		$kode="MG".date("y") .  date("m");
        $result=$this->db->query("SELECT kode FROM mrp_production WHERE month(tanggal)='" . date("m") . "' AND year(tanggal)='" . date("Y") . "' AND kode LIKE '%MG%' ORDER BY RIGHT(kode,5) DESC LIMIT 1");
        if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=substr($row->kode,-5)+1;
        }else{
            $dgt="1";
        }
        //$dgt=substr("00000" . $dgt,-5);            
        //$mo=$kode . $dgt;
        return $dgt;
	}

	public function simpan_mrp_production_batch($sql)
	{
		return $this->db->query("INSERT INTO mrp_production (kode,tanggal,origin,kode_produk,nama_produk,qty,uom,tanggal_jt,reff_note,kode_bom,start_time,finish_time,source_location,destination_location,dept_id,status,id_warna,responsible,id_handling,lebar_jadi,uom_lebar_jadi,gramasi) values $sql ");
	}

	public function simpan_mrp_production_rm_target_batch($sql)
	{
		return $this->db->query("INSERT INTO mrp_production_rm_target (kode,move_id,kode_produk,nama_produk,qty,uom,row_order,origin_prod,status) values $sql");
	}

	public function simpan_mrp_production_fg_target_batch($sql)
	{
		return $this->db->query("INSERT INTO mrp_production_fg_target (kode,move_id,kode_produk,nama_produk,qty,uom,row_order,status) values $sql");
	}

	public function get_stock_move_by_oirigin($origin)
	{
		return $this->db->query("SELECT move_id FROM stock_move where origin = '$origin' order by create_date")->result();
	}
	
	public function get_penerimaan_barang_by_origin($origin)
	{
		return $this->db->query("SELECT kode FROM penerimaan_barang WHERE origin = '$origin' order by tanggal")->result();
	}

	public function get_pengiriman_barang_by_origin($origin)
	{
		return $this->db->query("SELECT kode FROM pengiriman_barang WHERE origin = '$origin' order by tanggal")->result();
	}

	public function get_mrp_production_by_origin($origin)
	{
		return $this->db->query("SELECT kode FROM mrp_production WHERE origin = '$origin' order by tanggal")->result();
	}

	public function cek_color_details_by_kode($kode_co)
	{
		return $this->db->query("SELECT kode_co FROM color_order_detail WHERE kode_co = '$kode_co' GROUP BY kode_co");
	}

	public function cek_warna_by_id_warna($id_warna)
	{
		return $this->db->query("SELECT * FROM warna WHERE id = '$id_warna'");
	}

	/*
	public function simpan_warna_batch($sql)
	{
		return $this->db->query("INSERT INTO warna (kode_warna,status,tanggal) values $sql");
	}
	*/


	// new query

	public function get_sales_color_line_by_kode($so,$row_order)
	{
		return $this->db->query("SELECT * FROM sales_color_line WHERE sales_order ='$so' AND row_order = '$row_order' ");
	}

	public function get_default_route_co_by_kode($kode_co)
	{
		return $this->db->query("SELECT route FROM color_order WHERE kode_co = '$kode_co' ");
	}


	public function cek_status_color_order_details_by_row($kode_co, $row_order)
	{
		$this->db->where('kode_co',$kode_co);
		$this->db->where('row_order',$row_order);
		return $this->db->get('color_order_detail');
	}

	public function get_color_order_details_by_row($kode_co, $row_order)
	{
		$this->db->select('a.kode_sc, b.kode_co, b.tanggal, b.ow, b.kode_produk, b.nama_produk, b.id_warna, b.qty, b.uom, b.reff_notes, b.route_co, b.row_order, b.id_handling, b.lebar_jadi, b.uom_lebar_jadi, b.gramasi ');
		$this->db->from('color_order_detail b');
		$this->db->join('color_order a','b.kode_co = a.kode_co', 'inner');
		$this->db->where('b.kode_co',$kode_co);
		$this->db->where('b.row_order',$row_order);
		$query =  $this->db->get();
		return $query->result();
	}

	public function cek_route_color_order($route_co)
	{
		$this->db->where('nama_route',$route_co);
		return $this->db->get('mrp_route');
	}

	public function get_kategori_produk_by_dept($dept_id)
	{
		$this->db->where('dept_id',$dept_id);
		return $this->db->get('mst_category');

	}

	public function update_status_color_order_items($kode_co,$row_order,$status)
	{
		return $this->db->query("UPDATE color_order_detail SET status = '$status'  WHERE kode_co = '$kode_co' AND row_order = '$row_order' ");
	}

	public function cek_status_color_order_items($kode_co,$status)
	{
		return $this->db->query("SELECT * FROM color_order_detail WHERE kode_co = '$kode_co' $status ");

	}



}
