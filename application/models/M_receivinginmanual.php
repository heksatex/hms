<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_receivinginmanual extends CI_Model
{
	
	var $table 		  = 'penerimaan_barang_m';
	var $column_order = array(null, 'kode', 'tanggal', 'creation_date', 'source_document', 'lokasi_tujuan', 'note','status');
	var $column_search= array(  'kode', 'tanggal', 'creation_date', 'source_document', 'lokasi_tujuan', 'note','status');
	var $order  	  = array('tanggal' => 'desc');

	public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
		$this->db2 = $this->load->database('odoo',TRUE);
	}

	private function _get_datatables_query()
	{		

		$this->db->from($this->table);
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
		
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}


	public function get_list_receiving_by_kode($no_receiving)
	{
	    return $this->db2->query("SELECT 					   
	    							stock_picking.id,
								    stock_picking.origin,
								    stock_picking.date,
								    stock_picking.name,
								    stock_picking.state,	
								    product_template.create_date,							    
								   	COALESCE(product_product.default_code,product_product.default_code) as default_code,
								    product_product.name_template,
									stock_production_lot.name as lot,
								    stock_move.product_uom_qty as qty,
								    product_uom.name as uom,
								    stock_move.state,
								    product_template.type,
									product_category.name as route,
									stock_picking.note_purc
								     FROM 
								    stock_picking 
								    INNER JOIN stock_move ON stock_move.picking_id = stock_picking.id 
								    INNER JOIN product_product ON stock_move.product_id = product_product.id 
								    INNER JOIN product_uom ON stock_move.product_uom = product_uom.id
									INNER JOIN product_template ON product_template.id = product_product.product_tmpl_id	
									INNER JOIN product_category ON product_template.categ_id = product_category.id
									LEFT JOIN stock_pack_operation ON stock_pack_operation.picking_id = stock_picking.id
									LEFT JOIN stock_production_lot ON stock_production_lot.id = stock_pack_operation.lot_id
								     WHERE
								    stock_picking.name = '$no_receiving' ORDER BY product_product.name_template");

	}


	public function get_produk_by_nama($nama_produk)
	{
		return $this->db->query("SELECT * FROM mst_produk where nama_produk = '$nama_produk' ");
	}

	public function get_uom_by_uom_odoo($uom_odoo)
	{
		return $this->db->query("SELECT uom FROM z_trans_uom WHERE uom_odoo = '$uom_odoo' ");
	}


	public function get_route_by_category_odoo($category_odoo)
	{
		return $this->db->query("SELECT route_produksi FROM z_trans_category WHERE category_odoo = '$category_odoo'");
	}

	public function get_penerimaan_barang_m_by_kode($kode)
	{
		return $this->db->query("SELECT * FROM penerimaan_barang_m where kode = '$kode' ");
	}

	public function get_penerimaan_barang_m_items_by_kode($kode)
	{
		return $this->db->query("SELECT * FROM penerimaan_barang_m_items where kode = '$kode' ORDER BY row_order")->result();
	}

	public function save_penerimaan_manual($sql)
	{	
		return $this->db->query("INSERT INTO penerimaan_barang_m (kode,tanggal,creation_date,source_document,lokasi_tujuan,note, status) values $sql ");
	}

	public function save_penerimaan_manual_items_batch($sql)
	{
		return $this->db->query("INSERT INTO penerimaan_barang_m_items (kode,kode_produk,nama_produk,lot,qty,uom,status,row_order) values $sql ");
	}

	public function save_produk_manual_batch($sql)
	{
		return $this->db->query("INSERT INTO mst_produk (kode_produk,nama_produk,uom,create_date,route_produksi,type) VALUES $sql ");
	}
}


?>