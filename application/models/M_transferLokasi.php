<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_transferLokasi extends CI_Model
{

	var $column_order = array(null, 'a.kode_tl','a.tanggal_dibuat','d.nama', 'a.lokasi_tujuan', 'a.total_lot', 'a.note','mmss.nama_status', 'a.nama_user' );
	var $column_search= array( 'a.kode_tl','a.tanggal_dibuat','d.nama', 'a.lokasi_tujuan', 'a.total_lot', 'a.note','mmss.nama_status', 'a.nama_user' );
	var $order  	  = array('a.tanggal_dibuat' => 'desc');

	private function _get_datatables_query()
	{	

		//add custom filter here
        if($this->input->post('kode'))
        {
            $this->db->like('kode_tl', $this->input->post('kode'));
        }
        if($this->input->post('status'))
        {
            $this->db->like('status', $this->input->post('status'));
        }
        if($this->input->post('note'))
        {
            $this->db->like('note', $this->input->post('note'));
        }
        if($this->input->post('dept_id'))
        {
            $this->db->like('dept_id', $this->input->post('dept_id'));
        }


		$this->db->select("a.kode_tl, a.tanggal_dibuat, a.lokasi_tujuan, a.note, a.status,a.total_lot, d.kode, d.nama as departemen, mmss.nama_status, a.nama_user" );
		$this->db->from("transfer_lokasi a");		
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");
		$this->db->JOIN("main_menu_sub_status mmss", "mmss.jenis_status=a.status", "INNER");

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
		if($_POST['length'] != -1)
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
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
		$this->db->select("a.kode_tl, 'a.tanggal_dibuat', a.total_lot, a.lokasi_tujuan, a.note, a.status, d.kode, d.nama as departemen, mmss.nama_status" );
		$this->db->from("transfer_lokasi a");		
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=a.status", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		return $this->db->count_all_results();
	}

	var $column_order2 = array(null, 'kode_produk','nama_produk','corak_remark','warna_remark','lokasi_asal','lot','qty','qty2','qty_jual','qty2_jual',null);
	var $column_search2= array('kode_produk','nama_produk','lokasi_asal','lot','qty','uom','qty2','uom2','qty_jual','qty2_jual','corak_remark','warna_remark');
	var $order2  	  = array('row_order' => 'desc');
	var $table2       = 'transfer_lokasi_items';

	private function _get_datatables_query2()
	{

		$this->db->SELECT("tl.status, tli.*");
		$this->db->FROM('transfer_lokasi tl');
		$this->db->JOIN('transfer_lokasi_items tli', 'tl.kode_tl = tli.kode_tl', "INNER");

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

	function get_datatables2($kode)
	{
		$this->db->where("tl.kode_tl", $kode);
		$this->_get_datatables_query2();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2($kode)
	{
		$this->_get_datatables_query2();
		$this->db->where("tl.kode_tl", $kode);
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2($kode)
	{
		$this->db->where("tl.kode_tl", $kode);
		$this->db->SELECT("tl.status, tli.*");
		$this->db->FROM('transfer_lokasi tl');
		$this->db->JOIN('transfer_lokasi_items tli', 'tl.kode_tl = tli.kode_tl', "INNER");
		return $this->db->count_all_results();
	}

	public function cek_status_aktif_lokasi_by_kode($dept_id,$lokasi)
	{	
		$this->db->where('dept_id',$dept_id);
		$this->db->where('kode_lokasi',$lokasi);
		return $this->db->get('mst_lokasi');
	}

	public function valid_lokasi_by_dept($dept_id,$lokasi_tujuan)
	{
		$query =  $this->db->query("SELECT * FROM mst_lokasi Where dept_id = '$dept_id' AND kode_lokasi = '$lokasi_tujuan' AND kode_lokasi NOT IN (SELECT kode_lokasi FROM mst_lokasi WHERE nama_lokasi = 'default_greige_out' OR nama_lokasi = 'default_adjustment') ");
		return $query->data_seek(0);
	}

	public function is_valid_lokasi_tujuan_by_kode($kode_tl,$lokasi_tujuan)
	{
		$this->db->where('kode_tl',$kode_tl);
		$this->db->where('lokasi_tujuan',$lokasi_tujuan);
		$query = $this->db->get('transfer_lokasi');
		return $query->data_seek(0);
	}

	public function verified_barcode_by_dept($lokasi_stock,$barcode_id)
	{
		$this->db->where('lokasi',$lokasi_stock);
		$this->db->where('lot',$barcode_id);    
		$query = $this->db->get('stock_quant');
		return $query->row();
	}

	public function is_valid_lokasi_barcode_by_dept($lokasi_stock,$barcode_id)
	{
		$lokasi_in_valid = array('ADJ','GOUT');
		$this->db->where_not_in('lokasi_fisik',$lokasi_in_valid);
		$this->db->where('lokasi',$lokasi_stock);
		$this->db->where('lot',$barcode_id);
		$query = $this->db->get('stock_quant');
		//return $query->data_seek(0);
        return $query->row_array();
	}

	public function cek_transfer_lokasi_items_by_kode($kode_tl)
	{
		$this->db->where('kode_tl',$kode_tl);
		$query = $this->db->get('transfer_lokasi_items');
		return $query->data_seek(0);
	}

	public function get_kode_tl()
	{
		$kode="TL".date("y") .  date("m");
        $result=$this->db->query("SELECT kode_tl FROM transfer_lokasi WHERE month(tanggal_dibuat)='" . date("m") . "' AND year(tanggal_dibuat)='" . date("Y") . "' ORDER BY RIGHT(kode_tl,6) DESC LIMIT 1");
        if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=substr($row->kode_tl,-6)+1;
        }else{
            $dgt="1";
        }
        $dgt=substr("000000" . $dgt,-6);            
        $kode_tl=$kode . $dgt;
        return $kode_tl;
	}


	public function save_transfer_lokasi($kode,$tgl,$note,$dept_id,$lokasi_dari,$lokasi_tujuan,$nama_user,$status)
	{
		return $this->db->query("INSERT INTO transfer_lokasi (kode_tl,tanggal_dibuat,dept_id,lokasi_dari,lokasi_tujuan,note,status,nama_user) values ('$kode','$tgl','$dept_id','$lokasi_dari','$lokasi_tujuan','$note','$status','$nama_user') ");
	}

	public function update_transfer_lokasi($kode_tl,$lokasi_dari,$lokasi_tujuan,$note)
	{
		return $this->db->query("UPDATE transfer_lokasi SET lokasi_dari = '$lokasi_dari', lokasi_tujuan = '$lokasi_tujuan', note = '$note' WHERE kode_tl  = '$kode_tl'");
	}

	public function get_transfer_lokasi_by_kode($kode_tl)
	{
		
		$this->db->select("a.kode_tl, a.tanggal_dibuat,a.tanggal_transfer, a.total_lot, a.lokasi_tujuan, a.note, a.status, a.dept_id, d.kode, d.nama as departemen, a.lokasi_dari" );
		$this->db->from("transfer_lokasi a");		
		$this->db->JOIN("departemen d","a.dept_id=d.kode","INNER");
		$this->db->where("a.kode_tl", $kode_tl);
		$query  = $this->db->get();
		return $query->row();
	}

	public function get_transfer_lokasi_items_by_kode($kode_tl)
	{
		$this->db->where('kode_tl',$kode_tl);
		$this->db->order_by('row_order','asc');
		$query  = $this->db->get('transfer_lokasi_items');
		return $query->result();
	}

	public function get_list_stock_quant_by_kode($lokasi,$barcode_id)
	{
		$this->db->where('lokasi',$lokasi);
		$this->db->where('lot',$barcode_id);
		$this->db->order_by('quant_id','asc');
		$query  = $this->db->get('stock_quant');
		return $query->result();
	}

	public function get_list_stock_quant_by_kode2($lokasi,$lokasi_fisik,$barcode_id)
	{
		$this->db->where('lokasi_fisik',$lokasi_fisik);
		$this->db->where('lokasi',$lokasi);
		$this->db->where('lot',$barcode_id);
		$this->db->order_by('quant_id','asc');
		$query  = $this->db->get('stock_quant');
		return $query->result();
	}


	public function get_row_order_transfer_lokasi_item_by_kode($kode_tl)
	{
		$last_no =  $this->db->query("SELECT max(row_order) as nom FROM transfer_lokasi_items where kode_tl = '$kode_tl'");

		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		return $no;
	}


	public function save_transfer_lokasi_items($kode_tl,$quant_id,$kode_produk,$nama_produk,$lokasi_asal,$lot,$qty,$uom,$qty2,$uom2,$qty_jual,$uom_jual,$qty2_jual,$uom2_jual,$ro,$corak_remark,$warna_remark)
	{
		$data = array(
						'kode_tl' 		=> $kode_tl,
						'quant_id'	 	=> $quant_id,
						'kode_produk'	=> $kode_produk,
						'nama_produk'	=> $nama_produk,
						'lokasi_asal'   => $lokasi_asal,
						'corak_remark'   => $corak_remark,
						'warna_remark'   => $warna_remark,
						'lot'			=> $lot,
						'qty'           => $qty,
						'uom'           => $uom,
                        'qty2'          => $qty2,
						'uom2'          => $uom2,
						'qty_jual'      => $qty_jual,
						'uom_jual'      => $uom_jual,
						'qty2_jual'     => $qty2_jual,
						'uom2_jual'     => $uom2_jual,
						'row_order'     => $ro


					);
		return $this->db->insert('transfer_lokasi_items',$data);
	}

	public function delete_transfer_lokasi_items($kode_tl,$quant_id,$row_order)
	{
		$data = array(
						'kode_tl'   => $kode_tl,
						'quant_id'		=> $quant_id,
						'row_order' => $row_order,
					);
		return $this->db->delete('transfer_lokasi_items',$data);

	}

	public function cek_barcode_transfer_lokasi_items_by_kode($kode_tl,$quant_id,$row_order)
	{
		$this->db->where('kode_tl',$kode_tl);
		$this->db->where('quant_id',$quant_id);
		$this->db->where('row_order', $row_order);
		return $this->db->get('transfer_lokasi_items');

	}

	public function cek_transfer_lokasi_items_by_barcode($kode_tl,$barcode_id)
	{
		$this->db->where('kode_tl',$kode_tl);
		$this->db->where('lot', $barcode_id);
		return $this->db->get('transfer_lokasi_items');
	}

	public function update_status_transfer_lokasi($kode_tl,$status)
	{
		$this->db->set('status', $status);
		$this->db->where('kode_tl', $kode_tl);
		return $this->db->update('transfer_lokasi');

	}

	public function is_same_valid_lokasi_asal_by_quant_id($quant_id)
	{
		$this->db->where('quant_id', $quant_id);
		return $this->db->get('stock_quant');
	}

	public function get_jml_items_transfer_lokasi_by_kode($kode_tl)
	{
		$this->db->WHERE('kode_tl',$kode_tl);
		$result = $this->db->get('transfer_lokasi_items');
		return $result->num_rows();	
	}
	
	public function update_jml_items_transfer_lokasi_by_kode($kode_tl,$count)
	{
		$this->db->set('total_lot', $count);
		$this->db->where('kode_tl', $kode_tl);
		return $this->db->update('transfer_lokasi');
	}

	public function cek_transfer_lokasi_by_user($user,$status_tl)
	{
		return $this->db->query("SELECT kode_tl, status FROM transfer_lokasi WHERE nama_user = '$user' AND status $status_tl");
	}

	public function cek_barcode_in_transfer_lokasi_by_status($status, $barcode_id)
	{
		$this->db->where('a.status', $status);
		$this->db->where('b.lot', $barcode_id);
		$this->db->select('a.kode_tl, a.status');
		$this->db->from('transfer_lokasi a');
		$this->db->JOIN('transfer_lokasi_items b', 'a.kode_tl=b.kode_tl','INNER');
		$result  = $this->db->get();
		return $result->num_rows();
	}	

	public function get_list_kode_tl_by_barcode($status,$barcode_id)
	{
		$this->db->where('a.status', $status);
		$this->db->where('b.lot', $barcode_id);
		$this->db->select('a.kode_tl, a.status');
		$this->db->from('transfer_lokasi a');
		$this->db->JOIN('transfer_lokasi_items b', 'a.kode_tl=b.kode_tl','INNER');
		$result  = $this->db->get();
		return $result->result();;
	}

	public function cek_stock_qunt_by_barcode($lot)
	{
		$this->db->where('lot', $lot);
		$result = $this->db->get('stock_quant');
		return $result->row();
	}


}