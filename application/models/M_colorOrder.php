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

	var $column_order2  = array(null, 'sales_order', 'buyer_code', 'sales_group');
	var $column_search2 = array('sales_order', 'buyer_code', 'sales_group');
	var $order2  	    = array('create_date' => 'desc');

	var $column_order3  = array(null, 'sales_order', 'kode_warna', 'qty', 'uom', 'piece_info');
	var $column_search3 = array('sales_order', 'kode_warna', 'qty', 'uom', 'piece_info');
	var $order3  	    = array('create_date' => 'desc');


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

	public function simpan($kode_co, $kode_sc, $buyer_code, $tgl_sj, $note, $tgl, $route, $lbr_jadi, $handling)
	{
		$query = $this->db->query("INSERT INTO color_order (kode_co, kode_sc, buyer_code, tanggal_sj, notes, tanggal, route, lebar_jadi, handling, status) 
								 values ('$kode_co', '$kode_sc', '$buyer_code', '$tgl_sj', '$note', '$tgl', '$route', '$lbr_jadi', '$handling','draft')");
		return $query;
	}

	public function hapus($kode_co)
	{
		return $this->db->delete('color_order', array("kode_co" => $kode_co));
	}


	public function kode_co()
	{	
		$no="CO";
        $result=$this->db->query("SELECT kode_co FROM color_order ORDER BY RIGHT(kode_co,5) DESC LIMIT 1");
        if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=substr($row->kode_co,-5)+1;
        }else{
            $dgt="1";
        }
        $dgt=substr("00000" . $dgt,-5);            
        $kode_co=$no . $dgt;

		return $kode_co;
	}

	public function ubah($kode_co, $kode_sc, $buyer_code, $tgl_sj, $note, $tgl, $route, $lbr_jadi, $handling)
	{
		$query = $this->db->query("UPDATE color_order SET kode_sc = '$kode_sc',  buyer_code = '$buyer_code',
														  tanggal_sj = '$tgl_sj', notes = '$note', 
														  route = '$route', 
														  lebar_jadi = '$lbr_jadi', handling='$handling'
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
		$query = $this->db->query("SELECT * FROM color_order_detail where kode_co = '".$kode_co."' ORDER BY row_order");
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

	public function get_list_handling()
	{
		return $this->db->query("SELECT nama FROM handling ORDER BY nama ")->result();
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
		
		$this->db->select("sales_order, buyer_code, sales_group");
		$this->db->from("sales_contract");
		$this->db->where("status", 'product_generated');

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
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2()
	{
		$this->_get_datatables2_query();
		$this->db->where("status", 'product_generated');
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2()
	{
	  	$this->db->select("sales_order, buyer_code, sales_group");
		$this->db->from("sales_contract");
		$this->db->where("status", 'product_generated');
		return $this->db->count_all_results();
	}


	private function _get_datatables3_query()
	{
		
		$this->db->select("kode_produk, nama_produk, kode_warna, qty, uom, piece_info");
		$this->db->from("sales_color_line");

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

				if(count($this->column_search2) - 1 == $i) //last loop
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
		$this->_get_datatables3_query();
		if($_POST['length'] != -1)
		$this->db->where("sales_order", $sales_order);
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered3($sales_order)
	{
		$this->_get_datatables3_query();
		$this->db->where("sales_order", $sales_order);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all3($sales_order)
	{
	  	$this->db->select("kode_produk, nama_produk, kode_warna, qty, uom, piece_info");
		$this->db->from("sales_color_line");
		$this->db->where("sales_order", $sales_order);
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

	public function simpan_color_detail($kode_co,$kode_produk,$nama_produk,$kode_warna,$qty,$uom,$reff,$status,$row_order)
	{
		$tgl = date('Y-m-d H:i:s');
		return $this->db->query("INSERT INTO color_order_detail (kode_co, tanggal, kode_produk, nama_produk, kode_warna, qty, uom, reff_notes, status, row_order) 
								values ('$kode_co','$tgl','$kode_produk','$nama_produk','$kode_warna','$qty','$uom','$reff','$status','$row_order') ");
	}

	public function update_one_is_approve_color_lines($sales_order, $kode_warna, $is_approve)
	{

		return $this->db->query("UPDATE sales_color_line SET is_approve = '$is_approve' WHERE sales_order = '$sales_order' AND kode_warna = '$kode_warna'");
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
		return $this->db->query("SELECT * FROM color_order_detail WHERE kode_co = '$kode_co' AND row_order = '$row_order' ");
	}

	public function ubah_color_detail($kode_co, $row_order, $qty, $reff)
	{
		return $this->db->query("UPDATE color_order_detail SET qty =  '$qty', reff_notes='$reff' 
								WHERE kode_co  = '$kode_co' AND row_order = '$row_order'");
	}

	public function get_color_order($kode_co)
	{
	    return $this->db->query("SELECT nama_produk,kode_warna,row_order,qty,reff_notes FROM color_order_detail WHERE kode_co = '$kode_co' GROUP BY nama_produk")->result();
	}

	public function get_detail_color_order($kode_co)
	{
		return $this->db->query("SELECT cod.kode_produk, cod.nama_produk, cod.kode_warna, cod.uom,co.route, cod.row_order, cod.qty,cod.reff_notes,cod.status
								FROM color_order_detail cod 
								INNER JOIN color_order co ON cod.kode_co = co.kode_co 
								WHERE cod.kode_co = '$kode_co' ORDER BY row_order")->result();
	}

	public function get_route_product($route)
	{
		return $this->db->query("SELECT *
								FROM mrp_route mr								
								WHERE mr.nama_route = '$route' ORDER BY row_order ")->result();
	}

	public function get_nama_dept_by_kode($kode)
	{
		return $this->db->query("SELECT d.nama FROM departemen d WHERE kode = '$kode'");
	}

	public function cek_nama_product($produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk FROM mst_produk where nama_produk = '$produk'");
	}

	public function simpan_product_batch($sql)
	{
		return $this->db->query("INSERT INTO mst_produk (kode_produk, nama_produk, create_date, lebar) values $sql ");
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
     		$no   = (int)$result->nom + 1;
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
		return $this->db->query("SELECT kode_produk FROM bom WHERE kode_produk  = '$kode_produk'");
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
		return $this->db->query("INSERT INTO bom (kode_bom,nama_bom,kode_produk,nama_produk,qty,uom) values $sql ");
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
		return $this->db->query("INSERT INTO stock_move_produk (move_id,kode_produk,nama_produk,qty,uom,status,row_order) 
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
			return $this->db->query("INSERT INTO pengiriman_barang_items  (kode,kode_produk,nama_produk,qty,uom,status_barang,row_order) 
								values $sql ");
	}

	public function update_reff_picking_pengiriman_batch($sql)
	{
		return $this->db->query(" $sql ");
	}

	public function get_kode_mo()
	{
		$kode="MO".date("y") .  date("m");
        $result=$this->db->query("SELECT kode FROM mrp_production WHERE month(tanggal)='" . date("m") . "' AND year(tanggal)='" . date("Y") . "' ORDER BY RIGHT(kode,5) DESC LIMIT 1");
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
		return $this->db->query("INSERT INTO mrp_production (kode,tanggal,origin,kode_produk,nama_produk,qty,uom,tanggal_jt,reff_note,kode_bom,start_time,finish_time,source_location,destination_location,dept_id,status,kode_warna) values $sql ");
	}

	public function simpan_mrp_production_rm_target_batch($sql)
	{
		return $this->db->query("INSERT INTO mrp_production_rm_target (kode,move_id,kode_produk,nama_produk,qty,uom,row_order) values $sql");
	}

	public function simpan_mrp_production_fg_target_batch($sql)
	{
		return $this->db->query("INSERT INTO mrp_production_fg_target (kode,move_id,kode_produk,nama_produk,qty,uom,row_order) values $sql");
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

	public function get_color_order_group_by_warna($kode_co)
	{
	    return $this->db->query("SELECT nama_produk,kode_warna,row_order,qty,reff_notes FROM color_order_detail WHERE kode_co = '$kode_co' GROUP BY kode_warna")->result();
	}

	public function cek_kode_warna($warna)
	{
		return $this->db->query("SELECT kode_warna FROM warna WHERE kode_warna = '$warna'");
	}

	public function simpan_warna_batch($sql)
	{
		return $this->db->query("INSERT INTO warna (kode_warna,status,tanggal) values $sql");
	}
}
