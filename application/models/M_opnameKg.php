<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_opnameKg extends CI_Model
{
    
	var $column_order = array(null, 'a.kode_opname','a.tanggal','d.nama', 'a.lokasi_fisik', 'a.nama_produk', 'a.lot', 'a.qty_opname');
	var $column_search= array( 'a.kode_opname','a.tanggal','d.nama', 'a.lokasi_fisik', 'a.nama_produk', 'a.lot', 'a.qty_opname' );
	var $order  	  = array('a.tanggal' => 'desc');

	private function _get_datatables_query()
	{	

		//add custom filter here
        if($this->input->post('dept_id'))
        {
            $this->db->like('dept_id', $this->input->post('dept_id'));
        }
		if($this->input->post('lokasi_fisik'))
        {
            $this->db->like('lokasi_fisik', $this->input->post('lokasi_fisik'));
        }
		if($this->input->post('nama_produk'))
        {
            $this->db->like('nama_produk', $this->input->post('nama_produk'));
        }
		if($this->input->post('lot'))
        {
            $this->db->like('lot', $this->input->post('lot'));
        }


		$this->db->select("a.kode_opname, a.tanggal,a.quant_id,  a.dept_id, a.lokasi_fisik, a.kode_produk, a.nama_produk, a.lot, a.qty_opname, a.uom_opname, a.nama_user,  d.nama as departemen" );
		$this->db->from("opname_kg a");		
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");

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
		$this->db->select("a.kode_opname, a.quant_id,  a.tanggal, a.dept_id, a.lokasi_fisik, a.kode_produk, a.nama_produk, a.lot, a.qty_opname, a.uom_opname, a.nama_user,  d.nama as departemen" );
		$this->db->from("opname_kg a");		
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");

		return $this->db->count_all_results();
	}

	public function get_kode_opname()
	{
		$kode="OPN".date("y") .  date("m");
        $result=$this->db->query("SELECT kode_opname FROM opname_kg WHERE month(tanggal)='" . date("m") . "' AND year(tanggal)='" . date("Y") . "' ORDER BY RIGHT(kode_opname,6) DESC LIMIT 1");
        if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=substr($row->kode_opname,-6)+1;
        }else{
            $dgt="1";
        }
        $dgt=substr("000000" . $dgt,-6);            
        $kode_opname=$kode . $dgt;
        return $kode_opname;
	}

	public function get_data_opname_by_kode($kode)
	{
		return $this->db->query("SELECT a.kode_opname, a.quant_id,  a.tanggal, a.dept_id, a.lokasi_fisik, a.kode_produk, a.nama_produk, a.lot, a.qty_opname, a.uom_opname, a.nama_user,  d.nama as departemen, a.qty, a.uom, a.qty2, a.uom2
								FROM opname_kg a
								INNER JOIN departemen d ON a.dept_id = d.kode
								WHERE a.kode_opname = '$kode' ")->row();
	}

    public function get_lokasi_stock_by_dept($dept_id)
    {
        $result = $this->db->query("SELECT stock_location FROM departemen WHERE kode = '$dept_id' ")->row_array();
        return $result['stock_location'];
    }

    public function get_data_stock_quant_by_kode($lokasi, $lot)
    {
        return $this->db->query("SELECT quant_id, kode_produk, nama_produk, lot, qty, uom, qty2, uom2, lokasi_fisik FROM stock_quant WHERE lokasi = '$lokasi' AND lot = '$lot' ")->row();

    }

	public function is_valid_lokasi_barcode_by_dept($lokasi_stock,$barcode_id)
	{
		$this->db->where('lokasi',$lokasi_stock);
		$this->db->where('lot',$barcode_id);
		$query = $this->db->get('stock_quant');
		//return $query->data_seek(0);
        return $query->row_array();
	}

	public function verified_barcode_by_dept($barcode_id)
	{
		$this->db->where('lot',$barcode_id);    
		$query = $this->db->get('stock_quant');
		return $query->row();
	}

	public function save_opname_kg($kode, $tanggal, $quant_id, $kode_produk, $nama_produk, $lot, $qty_opname, $uom_opname,$qty, $uom_qty, $qty2, $uom_qty2, $dept_id, $lokasi_fisik, $nama_user)
	{
		$this->db->query("INSERT INTO opname_kg (kode_opname,tanggal,quant_id,kode_produk,nama_produk, lot, qty_opname, uom_opname, qty, uom, qty2, uom2, dept_id, lokasi_fisik, nama_user) values ('$kode','$tanggal','$quant_id','$kode_produk','$nama_produk','$lot','$qty_opname','$uom_opname', '$qty', '$uom_qty', '$qty2', '$uom_qty2', '$dept_id','$lokasi_fisik','$nama_user') ");
	}

	public function update_qty_opname_stock_quant_by_quant_id($quant_id,$qty_opname,$uom_opname)
	{
		$this->db->query("UPDATE stock_quant SET qty_opname = '$qty_opname', uom_opname = '$uom_opname' WHERE quant_id = '$quant_id' ");
	}
}