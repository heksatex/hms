<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_stockMoves extends CI_Model
{
	
	//var $table 		  = 'stock_move';
	var $column_order = array(null, 'sm.create_date', 'sm.move_id', 'sm.origin', 'sm.lokasi_dari', 'sm.lokasi_tujuan', 'smp.kode_produk','smp.nama_produk','smi.lot','smi.qty','smi.uom','smi.qty2','smi.uom2','smi.status');
	var $column_search= array( 'sm.create_date', 'sm.move_id', 'sm.origin', 'sm.lokasi_dari', 'sm.lokasi_tujuan', 'smp.kode_produk','smp.nama_produk','smi.lot','smi.qty','smi.uom','smi.qty2','smi.uom2','smi.status');
	var $order  	  = array('create_date' => 'desc');

	private function _get_datatables_query()
	{		

		//$this->db->from($this->table);
		
		$this->db->select("sm.create_date, sm.move_id, sm.origin, sm.lokasi_dari, sm.lokasi_tujuan, COALESCE (
							(SELECT kode FROM pengiriman_barang WHERE move_id = sm.move_id), 
							(SELECT kode FROM penerimaan_barang WHERE move_id = sm.move_id),
							(SELECT DISTINCT kode FROM mrp_production_rm_target WHERE move_id = sm.move_id),
							(SELECT DISTINCT kode FROM mrp_production_fg_target WHERE move_id = sm.move_id)
							) as 'picking',
						smp.kode_produk, smp.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status");
		$this->db->from("stock_move AS sm");
		$this->db->join("stock_move_produk smp", "sm.move_id = smp.move_id", "inner" );
		$this->db->join("stock_move_items smi", "smi ON sm.move_id = smi.move_id AND smi.kode_produk = smp.kode_produk AND smi.origin_prod = smp.origin_prod", "inner");

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
		
		$this->db->select("sm.create_date, sm.move_id, sm.origin, sm.lokasi_dari, sm.lokasi_tujuan, COALESCE (
							(SELECT kode FROM pengiriman_barang WHERE move_id = sm.move_id), 
							(SELECT kode FROM penerimaan_barang WHERE move_id = sm.move_id),
							(SELECT DISTINCT kode FROM mrp_production_rm_target WHERE move_id = sm.move_id),
							(SELECT DISTINCT kode FROM mrp_production_fg_target WHERE move_id = sm.move_id)
							) as 'picking',
						smp.kode_produk, smp.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status");
		$this->db->from("stock_move AS sm");
		$this->db->join("stock_move_produk smp", "sm.move_id = smp.move_id", "inner" );
		$this->db->join("stock_move_items smi", "smi ON sm.move_id = smi.move_id AND smi.kode_produk = smp.kode_produk AND smi.origin_prod = smp.origin_prod", "inner");	
		return $this->db->count_all_results();
	}


	// > new
	public function getRecord_sm($rowno,$rowperpage)
	{
		
		$query  = $this->db->query("SELECT sm.create_date, sm.move_id, sm.origin, sm.lokasi_dari, sm.lokasi_tujuan, COALESCE (
									(SELECT kode FROM pengiriman_barang WHERE move_id = sm.move_id), 
									(SELECT kode FROM penerimaan_barang WHERE move_id = sm.move_id),
									(SELECT DISTINCT kode FROM mrp_production_rm_target WHERE move_id = sm.move_id),
									(SELECT DISTINCT kode FROM mrp_production_fg_target WHERE move_id = sm.move_id)
									) as 'picking',smp.kode_produk, smp.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status
								FROM stock_move AS sm
								INNER JOIN stock_move_produk smp ON sm.move_id = smp.move_id
								INNER JOIN stock_move_items smi ON sm.move_id = smi.move_id AND smi.kode_produk = smp.kode_produk AND smi.origin_prod = smp.origin_prod
								ORDER BY sm.create_date desc LIMIT $rowno, $rowperpage  ");

		return $query->result_array();
	}


	public function getRecordCount_sm($where)
	{
		
      	$query  = $this->db->query("SELECT count(*) as allcount
								FROM stock_move AS sm
								INNER JOIN stock_move_produk smp ON sm.move_id = smp.move_id
								INNER JOIN stock_move_items smi ON sm.move_id = smi.move_id AND smi.kode_produk = smp.kode_produk AND smi.origin_prod = smp.origin_prod
								INNER JOIN 
									(SELECT kode, move_id FROM pengiriman_barang 
									UNION SELECT kode, move_id FROM penerimaan_barang
									UNION SELECT DISTINCT kode, move_id FROM mrp_production_rm_target
									UNION SELECT DISTINCT kode, move_id FROM mrp_production_fg_target
									UNION SELECT kode_adjustment, move_id FROM adjustment_items ) picking ON picking.move_id = sm.move_id
								LEFT JOIN stock_quant sq ON smi.quant_id = sq.quant_id								
								$where
								ORDER BY sm.create_date desc");
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
	}

	public function get_list_stock_moves_by($where,$rowno,$recordPerPage,$kolom_order,$order)
	{

		$query = $this->db->query("SELECT sm.create_date as tgl_sm, smi.tanggal_transaksi, sm.move_id, sm.origin, sm.lokasi_dari, sm.lokasi_tujuan, picking.kode, smp.kode_produk, smp.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status
								FROM stock_move AS sm
								INNER JOIN stock_move_produk smp ON sm.move_id = smp.move_id
								INNER JOIN stock_move_items smi ON sm.move_id = smi.move_id AND smi.kode_produk = smp.kode_produk AND smi.origin_prod = smp.origin_prod
								INNER JOIN 
									(SELECT kode, move_id FROM pengiriman_barang 
									UNION SELECT kode, move_id FROM penerimaan_barang
									UNION SELECT DISTINCT kode, move_id FROM mrp_production_rm_target
									UNION SELECT DISTINCT kode, move_id FROM mrp_production_fg_target
									UNION SELECT kode_adjustment, move_id FROM adjustment_items) picking ON picking.move_id = sm.move_id
								LEFT JOIN stock_quant sq ON smi.quant_id = sq.quant_id
								$where
								$kolom_order $order
								LIMIT $rowno, $recordPerPage ");

		return $query->result();
	}


	public function get_list_stock_moves_by_noLimit($where,$kolom_order,$order)
	{

		$query = $this->db->query("SELECT sm.create_date as tgl_sm, smi.tanggal_transaksi, sm.move_id, sm.origin, sm.lokasi_dari, sm.lokasi_tujuan, picking.kode, smp.kode_produk, smp.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status
								FROM stock_move AS sm
								INNER JOIN stock_move_produk smp ON sm.move_id = smp.move_id
								INNER JOIN stock_move_items smi ON sm.move_id = smi.move_id AND smi.kode_produk = smp.kode_produk AND smi.origin_prod = smp.origin_prod
								INNER JOIN 
									(SELECT kode, move_id FROM pengiriman_barang 
									UNION SELECT kode, move_id FROM penerimaan_barang
									UNION SELECT DISTINCT kode, move_id FROM mrp_production_rm_target
									UNION SELECT DISTINCT kode, move_id FROM mrp_production_fg_target
									UNION SELECT kode_adjustment, move_id FROM adjustment_items) picking ON picking.move_id = sm.move_id
								LEFT JOIN stock_quant sq ON smi.quant_id = sq.quant_id
								$where $kolom_order $order ");

		return $query->result();
	}

	public function get_list_stock_moves_by_Limit($where)
	{

		$query = $this->db->query("SELECT sm.create_date as tgl_sm, smi.tanggal_transaksi , sm.move_id, sm.origin, sm.lokasi_dari, sm.lokasi_tujuan, picking.kode, smp.kode_produk, smp.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.status
								FROM stock_move AS sm
								INNER JOIN stock_move_produk smp ON sm.move_id = smp.move_id
								INNER JOIN stock_move_items smi ON sm.move_id = smi.move_id AND smi.kode_produk = smp.kode_produk AND smi.origin_prod = smp.origin_prod
								INNER JOIN 
									(SELECT kode, move_id FROM pengiriman_barang 
									UNION SELECT kode, move_id FROM penerimaan_barang
									UNION SELECT DISTINCT kode, move_id FROM mrp_production_rm_target
									UNION SELECT DISTINCT kode, move_id FROM mrp_production_fg_target
									UNION SELECT kode_adjustment, move_id FROM adjustment_items) picking ON picking.move_id = sm.move_id
								LEFT JOIN stock_quant sq ON smi.quant_id = sq.quant_id
								$where ");

		return $query->result();
	}

	public function get_list_stock_moves_grouping($groupBy,$where)
	{
		
		$query  = $this->db->query("SELECT $groupBy as nama_field , concat($groupBy,' (',count(*),')') as grouping, 
									sum(smi.qty) as 'tqty' 
								FROM stock_move AS sm
								INNER JOIN stock_move_produk smp ON sm.move_id = smp.move_id
								INNER JOIN stock_move_items smi ON sm.move_id = smi.move_id AND smi.kode_produk = smp.kode_produk AND smi.origin_prod = smp.origin_prod
								INNER JOIN 
									(SELECT kode, move_id FROM pengiriman_barang 
									UNION SELECT kode, move_id FROM penerimaan_barang
									UNION SELECT DISTINCT kode, move_id FROM mrp_production_rm_target
									UNION SELECT DISTINCT kode, move_id FROM mrp_production_fg_target
									UNION SELECT kode_adjustment, move_id FROM adjustment_items) picking ON picking.move_id = sm.move_id
								LEFT JOIN stock_quant sq ON smi.quant_id = sq.quant_id
								$where
								Group by $groupBy
								ORDER BY sm.create_date desc  ");

		return $query->result();
	}



	// < new

}

?>