<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_sales extends CI_Model
{

	//var $table 		  = 'sales_contract';
	var $column_order = array(null, 'sales_order', 'create_date', 'customer_name', 'nama_sales_group','status');
	var $column_search= array('sc.sales_order', 'create_date', 'customer_name', 'nama_sales_group','status','nama_produk');
	var $order  	  = array('create_date' => 'desc');

	var $table2 		= 'partner';
	var $column_order2  = array(null, 'nama', 'buyer_code', 'invoice_street','delivery_street');
	var $column_search2 = array('nama', 'buyer_code', 'invoice_street','delivery_street');
	var $order2  	    = array('create_date' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
		$this->load->model('_module');
	}

	private function _get_datatables_query()
	{	


	    $this->db->select("sc.sales_order,sc.create_date,sc.customer_name,sc.sales_group,sg.nama_sales_group,sc.status, mmss.nama_status");
		$this->db->from("sales_contract sc");
		$this->db->join("sales_contract_items sci", "sci.sales_order=sc.sales_order", "left");
		$this->db->JOIN("mst_sales_group sg", "sc.sales_group=sg.kode_sales_group","INNER");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=sc.status", "left");
		$this->db->group_by('sc.sales_order');
		
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

	function get_datatables($mmss,$sales_group)
	{
		$this->_get_datatables_query();
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		if($sales_group != 'MKT005'){// Administrator
			$this->db->where("sc.sales_group", $sales_group);
		}
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered($mmss,$sales_group)
	{
		$this->_get_datatables_query();
		$this->db->where("mmss.main_menu_sub_kode",$mmss);
		if($sales_group != 'MKT005' ){// Administrator
			$this->db->where("sc.sales_group", $sales_group);
		}
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all($mmss,$sales_group)
	{
		//$this->db->from($this->table);
		$this->db->select("sc.sales_order,sc.create_date,sc.customer_name,sc.sales_group,sg.nama_sales_group,sc.status, mmss.nama_status");
		$this->db->from("sales_contract sc");
		$this->db->join("sales_contract_items sci", "sci.sales_order=sc.sales_order", "left");
		$this->db->JOIN("mst_sales_group sg", "sc.sales_group=sg.kode_sales_group","INNER");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=sc.status", "left");
		$this->db->group_by('sc.sales_order');
		$this->db->where("mmss.main_menu_sub_kode",$mmss);
		if($sales_group != 'MKT005' ){// administrator
			$this->db->where("sc.sales_group", $sales_group);
		}
		return $this->db->count_all_results();
	}

	public function cek_sales_group_by_username($username)
	{
		return $this->db->query("SELECT u.sales_group 
								FROM user u 
								INNER JOIN mst_sales_group mst ON u.sales_group = mst.kode_sales_group
								where u.username = '$username'");
		//return $this->db->query("SELECT sales_group FROM user where username = '$username'");
	} 

	public function get_list_departement()
	{
		return $this->db->query("SELECT kode,nama FROM departemen ORDER BY nama ")->result();
	}

	public function get_list_currency()
	{
		return $this->db->query("SELECT id,nama FROM currency ORDER BY create_date  ")->result();
	}

	public function get_list_incoterm()
	{
		return $this->db->query("SELECT id,nama FROM mst_incoterm order by id")->result();
	}

	public function get_list_paymentterm()
	{
		return $this->db->query("SELECT id,nama FROM mst_paymentterm order by id")->result();
	}

	public function get_list_tax()
	{
		return $this->db->query("SELECT * FROM tax WHERE type_inv = 'sale'  order by id")->result();
	}

	public function get_list_uom_select2_by_prod($name)
	{
		return $this->db->query("SELECT id, nama, nama, short
								FROM  uom 
								WHERE short LIKE '%$name%' ORDER BY id   ")->result_array();
	}


	private function _get_datatables2_query()
	{
		
		//$this->db->from($this->table2);

		$this->db->SELECT("p.id, p.nama, p.buyer_code, p.invoice_street, p.invoice_city, ps.name as invoice_state, p.invoice_zip, pc.name as invoice_country, p.delivery_street, p.delivery_city, ps.name as delivery_state, ps.name as delivery_country, p.delivery_zip");
		$this->db->FROM("partner p ");
		$this->db->JOIN("partner_country pc", "p.invoice_country = pc.id","LEFT");
		$this->db->JOIN("partner_states ps", "p.invoice_state = ps.id","LEFT");

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
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2()
	{
		//$this->db->from($this->table2);
		$this->db->SELECT("p.id, p.nama, p.buyer_code, p.invoice_street, p.invoice_city, ps.name as invoice_state, p.invoice_zip, pc.name as invoice_country, p.delivery_street, p.delivery_city, ps.name as delivery_state, ps.name as delivery_country, p.delivery_zip");
		$this->db->FROM("partner p ");
		$this->db->JOIN("partner_country pc", "p.invoice_country = pc.id","LEFT");
		$this->db->JOIN("partner_states ps", "p.invoice_state = ps.id","LEFT");
		return $this->db->count_all_results();
	}


	public function get_kode_sales_order()
	{
		$last_no = $this->db->query("SELECT mid(sales_order,3,(length(sales_order))-2) as 'nom' 
						 from sales_contract where left(sales_order,2)='SC'
						 order by cast(mid(sales_order,3,(length(sales_order))-2) as unsigned) desc LIMIT 1  ");
		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		$kode = 'SC'.$no;
		return $kode;
	}

	public function get_sales_group_by_user($username)
	{
		return $this->db->query("SELECT sales_group FROM user WHERE username = '$username' LIMIT 1");
	}

	public function get_symbol_currency_by_nama($nama)
	{
		return $this->db->query("SELECT nama,symbol FROM currency where nama = '$nama' ORDER BY create_date LIMIT 1");
	}

	public function simpan($sales_order, $tgl, $cust_id, $customer, $invoice_address, $delivery_address, $buyer_code, $type, $reference, $warehouse, $currency_nama, $symbol, $delivery_date, $time_ship, $order_production, $sales_group, $status,$note_head)
	{
		$query = $this->db->query("INSERT INTO sales_contract (sales_order, create_date, customer_id,customer_name, invoice_address, delivery_address, buyer_code, order_type, reference, warehouse, currency_nama, currency_symbol, delivery_date, time_shipment, order_production, sales_group, status,note_head) 
								 values ('$sales_order', '$tgl', '$cust_id', '$customer', '$invoice_address', '$delivery_address', '$buyer_code', '$type', '$reference', '$warehouse','$currency_nama', '$symbol', '$delivery_date', '$time_ship', '$order_production', '$sales_group', '$status','$note_head')");
		return $query;
	}

	public function get_data_by_kode($sales_order)
	{
		$query = $this->db->query("SELECT sc.id,sc.sales_order, sc.create_date, sc.customer_id, sc.customer_name, sc.invoice_address, sc.delivery_address, sc.buyer_code, sc.order_type, sc.reference, sc.warehouse, sc.currency_nama, sc.currency_symbol, sc.delivery_date, sc.time_shipment, sc.order_production, sc.sales_group, sc.status, sc.untaxed_value, sc.tax_value, sc.total_value, sc.incoterm_id, sc.paymentterm_id, sc.destination, sc.bank, sc.clause, sc.note, sc.note_head,  mst.nama_sales_group 
								FROM sales_contract sc 
								INNER JOIN mst_sales_group mst ON sc.sales_group = mst.kode_sales_group
								WHERE sc.sales_order = '$sales_order'");
		return $query->row();
	}

	public function ubah($sales_order, $reference, $warehouse, $currency, $symbol, $delivery_date, $time_ship, $note_head, $incoterm, $paymentterm, $destination, $bank, $clause, $note)
	{
		$query = $this->db->query("UPDATE sales_contract SET reference = '$reference',  warehouse = '$warehouse',
														  currency_nama = '$currency', currency_symbol = '$symbol',
														  delivery_date = '$delivery_date', time_shipment = '$time_ship',
														  incoterm_id = '$incoterm', paymentterm_id = '$paymentterm', 
														  destination = '$destination', bank = '$bank', clause = '$clause', 
														  note = '$note', note_head = '$note_head'
								    WHERE sales_order = '$sales_order' ");
		return $query;
	}

	public function cek_status_sales_contract($sales_order,$status)
	{
 		return $this->db->query("SELECT sales_order FROM sales_contract WHERE sales_order = '$sales_order' AND $status");
	}

	public function get_data_detail_by_kode($sales_order)
	{
		return $this->db->query("SELECT sci.sales_order, sci.due_date,sci.kode_produk,sci.nama_produk,sci.price,
								sci.qty,sci.uom,sci.roll_info,sci.tax_id, sci.description,sci.tax_nama,sci.row_order,
								tax.amount
								FROM sales_contract_items sci 
								LEFT JOIN tax tax ON sci.tax_id = tax.id
								WHERE sales_order = '$sales_order' ORDER BY row_order")->result();
	}

	public function get_list_produk_by_name($name)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  mst_produk 
								WHERE nama_produk LIKE '%$name%' AND (nama_produk LIKE '%(Tricot)%' or nama_produk LIKE '%(Inspecting)%' or nama_produk LIKE '%(Knitting)%') AND status_produk = 't' LIMIT 50")->result_array();
	}
	

	public function get_list_taxes_by_name($name)
	{
		return $this->db->query("SELECT id,nama 
								FROM  tax 
								WHERE nama LIKE '%$name%' AND type_inv = 'sale' order by id")->result_array();
	}

	public function get_produk_byid($kode_produk)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom, lebar_jadi, uom_lebar_jadi
								FROM  mst_produk 
								WHERE kode_produk = '$kode_produk'");
	}


	public function save_contract_lines_detail($sales_order,$kode_produk,$prod,$desc,$qty,$uom,$roll,$price,$tax_id,$tax_nama,$row_order)
	{
		return $this->db->query("INSERT INTO sales_contract_items (sales_order,kode_produk,nama_produk,description,price,qty,uom,roll_info,tax_id,tax_nama,row_order)
			values ('$sales_order','$kode_produk','$prod','$desc','$price','$qty','$uom','$roll','$tax_id','$tax_nama','$row_order')");
	}

	public function get_row_order_sales_contract_items($sales_order)
	{
		return $this->db->query("SELECT row_order   FROM sales_contract_items WHERE sales_order = '$sales_order' order by row_order desc");
	}

	public function update_contract_lines_detail($kode,$kode_prod,$prod,$desc,$qty,$uom,$roll,$price,$tax_id,$tax_nama,$row_order)
	{
		return $this->db->query("UPDATE sales_contract_items SET kode_produk = '$kode_prod', nama_produk = '$prod', description = '$desc', qty = '$qty', tax_id = '$tax_id', tax_nama = '$tax_nama',
																uom = '$uom', roll_info = '$roll', price = '$price'
								WHERE sales_order = '$kode' AND row_order = '$row_order' ");
	}

	public function get_data_tax_by_kode($kode)
	{
		return $this->db->query("SELECT id,nama FROM tax WHERE id = '$kode'");
	}

	public function delete_contract_lines_detail($sales_order,$row_order)
	{
		return $this->db->query("DELETE FROM sales_contract_items WHERE sales_order = '$sales_order' AND row_order = '$row_order'");
	}

	public function get_total_untaxed($sales_order)
	{
		return $this->db->query("SELECT sum(qty*price) as total_untaxed, sum(sci.qty*sci.price*tax.amount) as total_tax
								FROM sales_contract_items sci 
								LEFT JOIN tax tax ON sci.tax_id = tax.id WHERE sci.sales_order = '$sales_order'");
	}

	public function update_total_sales_contract($sales_order,$untaxed_value,$tax_value,$total)
	{
		return $this->db->query("UPDATE sales_contract SET untaxed_value =  '$untaxed_value', tax_value= '$tax_value',total_value= '$total'
								 WHERE sales_order = '$sales_order'");
	}

	public function get_data_paymentterm_by_kode($kode)
	{
		return $this->db->query("SELECT * from mst_paymentterm WHERE id = '$kode'")->row();
	}

	public function get_data_customer_by_kode($kode)
	{
		return $this->db->query("SELECT * FROM partner where id = '$kode' ")->row();
	}


	public function cek_sales_contract_items_by_kode($sales_order)
	{
		return $query  = $this->db->query("SELECT * FROM sales_contract_items WHERE sales_order = '$sales_order'");
	}

	public function update_status_sales_contract($sales_order,$status)
	{
		return $this->db->query("UPDATE sales_contract SET status = '$status' WHERE sales_order = '$sales_order' ");
	}

	public function get_ppn_by_sc($sales_order)
	{
		return $this->db->query("SELECT sci.tax_id, sci.tax_nama, t.ket 
								FROM sales_contract_items as sci INNER JOIN tax  as t ON sci.tax_id = t.id
								where sci.sales_order = '$sales_order' 
								LIMIT 1");
	}

	public function get_partner_states_by_kode($id)
	{
		return $this->db->query("SELECT * FROM partner_states WHERE id = '$id'");
	}

	public function get_partner_country_by_kode($id)
	{
		return $this->db->query("SELECT * FROM partner_country WHERE id = '$id'");
	}

	
	/* >> Approve Color  */
	
	public function cek_sales_color_line_by_kode($sales_order)
	{
		return $query  = $this->db->query("SELECT * FROM sales_color_line WHERE sales_order = '$sales_order'");
	}

	public function update_is_approve_color_lines($sales_order,$is_approve)
	{
		return $this->db->query("UPDATE sales_color_line SET is_approve = '$is_approve' WHERE sales_order = '$sales_order' AND is_approve IS NULL ");
	}

	public function cek_color_lines_is_approve_null($sales_order)
	{
		$query =  $this->db->query("SELECT is_approve FROM sales_color_line WHERE sales_order = '$sales_order' AND is_approve IS NULL ");
		return $query->num_rows();
	}


	/* Approve COlor << */



	/* >> COLOR LINES */


	public function no_OW()
	{
		$kode= "OW".date("y") .  date("m");
        $result=$this->db->query("SELECT ow FROM sales_color_line WHERE month(tanggal_ow)='" . date("m") . "' AND year(tanggal_ow)='" . date("Y") . "' ORDER BY RIGHT(ow,4) DESC LIMIT 1");
        if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=substr($row->ow,-4)+1;
        }else{
            $dgt="1";
        }
        $dgt=substr("0000" . $dgt,-4);            
        $ow =$kode . $dgt;
        return $ow;
	}

	public function get_data_color_line_by_kode($sales_order)
	{
		return $this->db->query("SELECT a.kode_produk, a.nama_produk, a.sales_order, a.description, a.id_warna, a.color_alias_name, a.qty, 
		a.uom, a.piece_info, a.row_order, a.is_approve,a.ow,b.nama_warna,a.id_handling,c.nama_handling,a.lebar_jadi, a.uom_lebar_jadi, a.gramasi, a.status, a.route_co, a.reff_notes, rc.nama as nama_route_co
							FROM sales_color_line a
							LEFT JOIN warna b ON a.id_warna = b.id
							LEFT JOIN mst_handling c ON a.id_handling = c.id
							LEFT JOIN route_co rc ON a.route_co = rc.kode
							WHERE a.sales_order = '$sales_order' ORDER BY a.row_order ")->result();
	}

	public function get_list_produk_color_by_name($kode,$name)
	{
		return $this->db->query("SELECT kode_produk, nama_produk, uom 
								FROM  sales_contract_items  
								WHERE sales_order = '$kode' AND nama_produk LIKE '%$name%' GROUP BY kode_produk LIMIT 10")->result_array();
	}

	public function get_list_color_by_name($name)
	{
		return $this->db->query("SELECT * FROM warna WHERE status NOT IN ('cancel') AND nama_warna LIKE '%$name%'  ORDER BY tanggal desc LIMIT 200")->result_array();
	}


	public function get_row_order_sales_color_lines($sales_order)
	{
		return $this->db->query("SELECT row_order  FROM sales_color_line WHERE sales_order = '$sales_order' order by row_order desc LIMIT 1");
	}

    public function save_color_lines($date,$kode_produk,$prod,$sales_order,$desc,$color,$color_name,$qty,$uom,$piece_info,$row_order,$gramasi,$handling,$lebar_jadi,$uom_lebar_jadi,$route_co,$reff_note)
	{
		return $this->db->query("INSERT INTO sales_color_line (create_date,kode_produk,nama_produk,description,sales_order,id_warna,color_alias_name,qty,uom,piece_info,row_order,id_handling,gramasi,lebar_jadi,uom_lebar_jadi,route_co,reff_notes,tanggal_ow)
			values ('$date','$kode_produk','$prod','$desc','$sales_order','$color','$color_name','$qty','$uom','$piece_info','$row_order','$handling','$gramasi','$lebar_jadi','$uom_lebar_jadi','$route_co','$reff_note','$date')");
	}

	public function delete_color_lines_detail($sales_order,$row_order)
	{
		return $this->db->query("DELETE FROM sales_color_line WHERE sales_order = '$sales_order' AND row_order = '$row_order'");
	}

	public function check_is_approve($sales_order,$row_order)
	{
		return $this->db->query("SELECT is_approve FROM sales_color_line WHERE sales_order = '$sales_order' AND row_order = '$row_order' ");
	}

    public function update_color_lines_detail($kode,$desc,$color,$color_name,$qty,$piece_info,$row_order,$handling,$gramasi,$lebar_jadi,$uom_lebar_jadi,$route_co,$reff_note)
	{
		return $this->db->query("UPDATE sales_color_line SET description = '$desc', id_warna = '$color', color_alias_name = '$color_name', 
																qty = '$qty', piece_info = '$piece_info', id_handling = '$handling', lebar_jadi = '$lebar_jadi', gramasi = '$gramasi',uom_lebar_jadi = '$uom_lebar_jadi', route_co = '$route_co', reff_notes = '$reff_note'
								WHERE sales_order = '$kode' AND row_order = '$row_order' ");
	}

	/*
	public function check_details_color_lines($sales_order,$kode_produk,$kode_warna)
	{
		return $this->db->query("SELECT sales_order FROM sales_color_line WHERE sales_order = '$sales_order' AND kode_produk = '$kode_produk' AND kode_warna = '$kode_warna'");
	}
	*/

	public function cek_item_color_lines_by_kode($sales_order,$row_order)
	{
		return $this->db->query("SELECT sales_order,ow FROM sales_color_line WHERE sales_order = '$sales_order' AND row_order = '$row_order' ");
	}

	public function simpan_no_ow_sales_color_line($kode,$row_order,$ow,$tgl)
	{
		return $this->db->query("UPDATE sales_color_line SET ow = '$ow', tanggal_ow = '$tgl' WHERE sales_order = '$kode' and row_order = '$row_order' ");
	}

	public function update_status_color_line_by_row($sales_order,$row_order,$value,$ow)
	{
		return $this->db->query("UPDATE sales_color_line SET status = '$value' WHERE sales_order = '$sales_order' and row_order = '$row_order' AND ow = '$ow' ");
	}

	public function cek_qty_contract_lines_by_produk($sales_order,$kode_produk)
	{
		$query =  $this->db->query("SELECT sum(qty) as tot_qty  FROM sales_contract_items WHERE sales_order = '$sales_order' AND kode_produk = '$kode_produk' GROUP BY kode_produk ")->row_array();
		return $query['tot_qty'];
	}

	public function cek_qty_color_lines_by_produk($sales_order,$kode_produk)
	{
		$query =  $this->db->query("SELECT sum(qty) as tot_qty  FROM sales_color_line WHERE sales_order = '$sales_order' AND kode_produk = '$kode_produk' AND status = 't' GROUP BY kode_produk ")->row_array();
		return $query['tot_qty'];
	}

	/* COLOR LINES << */

}