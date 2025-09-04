<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_reproses extends CI_Model
{

	var $column_order = array(null, 'kode_reproses', 'tanggal', 'nama_jenis','note', 'nama_status');
	var $column_search= array('kode_reproses', 'tanggal', 'nama_jenis','note', 'nama_status');
	var $order  	  = array('tanggal' => 'desc');

    // var $table2  	    = 'stock_quant';
	var $column_order2  = array(null, 'sq.kode_produk', 'sq.nama_produk', 'sq.lot', 'sq.qty', 'sq.qty2','sq.lokasi', 'sq.reff_note', 'sq.reserve_move');
	var $column_search2 = array('sq.kode_produk','sq.nama_produk', 'sq.lot', 'sq.qty', 'sq.qty2','sq.lokasi', 'sq.reff_note', 'sq.reserve_move');
	var $order2  	    = array('sq.create_date' => 'asc');


    public function __construct()
	{
		parent::__construct();
		$this->load->database('default', TRUE);
	}

	private function _get_datatables_query()
	{

		$this->db->select(" re.kode_reproses, re.tanggal, re.id_jenis, re.note, re.status, re.nama_user, mmss.nama_status, rej.nama_jenis");
		$this->db->from("reproses re");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=re.status", "inner");
        $this->db->join("reproses_jenis  rej","re.id_jenis = rej.id", "inner");

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
		if(isset($_POST["length"]) && $_POST["length"] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

    function count_filtered($mmss)
	{
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		$this->_get_datatables_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

    public function count_all($mmss)
	{
		$this->db->select(" re.kode_reproses, re.tanggal, re.id_jenis, re.note, re.status, re.nama_user, mmss.nama_status, rej.nama_jenis");
		$this->db->from("reproses re");
		$this->db->join("main_menu_sub_status mmss", "mmss.jenis_status=re.status", "inner");
        $this->db->join("reproses_jenis  rej","re.id_jenis = rej.id", "inner");
		$this->db->where("mmss.main_menu_sub_kode", $mmss);
		return $this->db->count_all_results();
	}


    private function _get_datatables2_query()
	{

        $this->db->select("sq.quant_id, sq.create_date, sq.kode_produk, sq.nama_produk, sq.lot, sq.qty, sq.uom, sq.qty2, sq.uom2, sq.lokasi, sq.reserve_move, sq.reff_note");
        $this->db->from("stock_quant sq");
        $this->db->join("mst_produk mp","sq.kode_produk = mp.kode_produk");
        $this->db->join("mst_category cat", "mp.id_category = cat.id");

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

	function dept_reproses()
	{
		$dept = array("DYE/Stock","FIN/Stock","INS2/Stock","GJD/Stock",'DYE-R/Stock','FIN-R/Stock','INS2-R/Stock');
		return $dept;
	}

    function get_datatables2()
	{
        $lokasi = $this->dept_reproses();
        $this->db->like("cat.nama_category","Kain Hasil");
		$this->db->where_in('sq.lokasi', $lokasi);
		$this->_get_datatables2_query();		
		if(isset($_POST["length"]) && $_POST["length"] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2()
	{ 
		$lokasi = $this->dept_reproses();
        $this->db->like("cat.nama_category","Kain Hasil");
		$this->db->where_in('sq.lokasi', $lokasi);
		$this->_get_datatables2_query();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2()
	{
        $lokasi = $this->dept_reproses();
        $this->db->select("sq.quant_id, sq.create_date, sq.kode_produk, sq.nama_produk, sq.lot, sq.qty, sq.uom, sq.qty2, sq.uom2, sq.lokasi, sq.reserve_move, sq.reff_note");
        $this->db->from("stock_quant sq");
        $this->db->join("mst_produk mp","sq.kode_produk = mp.kode_produk");
        $this->db->join("mst_category cat", "mp.id_category = cat.id");
        $this->db->like("cat.nama_category","Kain Hasil");
		$this->db->where_in('sq.lokasi', $lokasi);
		return $this->db->count_all_results();
	}

	function get_list_type()
	{
		return $this->db->query("SELECT id, nama_jenis FROM reproses_jenis ORDER BY id ")->result();
	}

    function get_jenis_reproses_by_id($id)
    {
        $result =  $this->db->query("SELECT nama_jenis FROM reproses_jenis WHERE id = '$id' ")->row();
        if(!empty($result)){
            return $result->nama_jenis;
        }
        return;
    }

    public function cek_reproses_by_kode($kode)
	{
		return $this->db->query("SELECT kode_reproses FROM reproses where kode_reproses = '$kode'");
	}

    public function cek_status_reproses($kode, $status)
	{
		if(empty($status)){
			return $this->db->query("SELECT status FROM reproses WHERE kode_reproses = '$kode'");
		}else{
			return $this->db->query("SELECT status FROM reproses WHERE kode_reproses = '$kode' AND status = '$status'");
		}
	}


    public function get_kode_reproses()
	{
       	$result=$this->db->query("SELECT kode_reproses FROM reproses WHERE month(tanggal)='" . date("m") . "' AND year(tanggal)='" . date("Y") . "' ORDER BY RIGHT(kode_reproses,4) DESC LIMIT 1");
        if ($result->num_rows()>0){
            $row=$result->row();
            $dgt=substr($row->kode_reproses,-4)+1;
        }else{
            $dgt="1";
        }
        return $dgt;
	}

    public function simpan_reproses($kode,$tanggal,$jenis,$note,$status,$nama_user)
    {   
        return $this->db->query("INSERT INTO reproses (kode_reproses,tanggal,id_jenis,note,nama_user,status) values ('$kode','$tanggal','$jenis','$note','$nama_user','$status') ");
    }

    public function update_reproses($kode,$note)
    {
        return $this->db->query("UPDATE reproses SET note = '$note' WHERE kode_reproses = '$kode'");
    }

    public function get_reproses_by_kode($kode)
	{
		$query = $this->db->query("SELECT re.kode_reproses, re.tanggal, re.note, re.status, re.id_jenis, rej.nama_jenis, rej.inisial
                                FROM reproses re
                                INNER JOIN reproses_jenis rej ON re.id_jenis = rej.id
                                where re.kode_reproses = '".$kode."' ");
		return $query->row();
	}

    public function get_reproses_detail_by_code($kode)
    {
        $query = $this->db->query("SELECT rei.kode_reproses, rei.quant_id, rei.kode_produk, mp.nama_produk, rei.lot, rei.qty, rei.uom, rei.qty2, rei.uom2, rei.lokasi_asal, rei.quant_id_new, rei.lot_new, rei.row_order
                                FROM reproses_items rei
                                INNER JOIN mst_produk mp ON rei.kode_produk = mp.kode_produk
                                WHERE rei.kode_reproses = '".$kode."'" );
		return $query->result();
                                
    }


    public function get_row_order_reproses_items_by_kode($kode)
	{
		$last_no =  $this->db->query("SELECT max(row_order) as nom FROM reproses_items where kode_reproses = '$kode'");
		$result = $last_no->row();
		if(empty($result->nom)){
			$no   = 1;
		}else{
     		$no   = (int)$result->nom + 1;
		}
		return $no;
	}


    public function cek_quant_reproses_items($kode, $quant_id)
	{
		return $this->db->query("SELECT kode_reproses FROM reproses_items WHERE kode_reproses = '$kode' AND quant_id = '$quant_id'");
	}

    public function simpan_reproses_items_batch($sql)
	{
		return $this->db->query("INSERT INTO reproses_items (kode_reproses, quant_id, kode_produk, lot, uom, qty, uom2, qty2, lokasi_asal, row_order) values $sql ");
	}

    public function get_reproses_items_by_row($kode,$row_order)
    {
		return $this->db->query("SELECT rei.quant_id, rei.kode_produk, mp.nama_produk, rei.lot, rei.uom, rei.qty, rei.uom2, rei.qty2
								FROM reproses_items  as rei
								INNER JOIN mst_produk as mp ON mp.kode_produk = rei.kode_produk
								WHERE rei.kode_reproses = '$kode' AND rei.row_order = '$row_order' ");
	}

    public function delete_reproses_items($kode,$row_order)
	{
		return $this->db->query("DELETE FROM reproses_items WHERE kode_reproses = '$kode' AND row_order = '$row_order'");
	}


    public function update_batal_reproses($kode, $status)
	{
		return $this->db->query("UPDATE reproses set status = '$status' where kode_reproses = '$kode' ");
	}

    public function simpan_adjustment_batch($sql)
    {
        return $this->db->query("INSERT INTO adjustment (kode_adjustment,create_date,lokasi_adjustment,kode_lokasi,note,status,nama_user,id_type_adjustment) values $sql ");
    }

    public function simpan_adjustment_items_batch($sql)
	{
		return $this->db->query("INSERT INTO adjustment_items (kode_adjustment, quant_id, kode_produk, lot, uom, qty_data, qty_adjustment, uom2, qty_data2, qty_adjustment2, move_id, qty_move, qty2_move, row_order) values $sql ");
	}


}