<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');
/**
 * 
 */
class M_listOW extends CI_Model
{
	
	var $column_order = array(null, 'scl.sales_order', 'msg.nama_sales_group', 'scl.ow',  'scl.tanggal_ow', 'scl.status','scl.nama_produk','w.nama_warna',  'scl.qty', 'tot_qty1', 'scl.gramasi', 'hdl.nama_handling', 'rc.nama', 'scl.lebar_jadi','ms.nama_status', 'scl.piece_info','scl.reff_notes','sc.delivery_date ','co.kode_co',null);
	var $column_search= array('scl.sales_order', 'scl.ow', 'scl.tanggal_ow', 'scl.nama_produk', 'w.nama_warna','scl.qty', 'co.kode_co',  'ms.nama_status','msg.nama_sales_group', 'scl.piece_info','scl.reff_notes', 'scl.gramasi', 'hdl.nama_handling', 'rc.nama', 'scl.lebar_jadi','sc.delivery_date ');
	var $order  	  = array('scl.tanggal_ow' => 'asc');

    
	private function _get_datatables_query()
	{	

        //add custom filter here
        if($this->input->post('sc'))
        {
            $this->db->like('scl.sales_order', $this->input->post('sc'));
        }

        if($this->input->post('sales_group'))
        {
            $this->db->like('msg.kode_sales_group', $this->input->post('sales_group'));
        }

        if($this->input->post('ow'))
        {
            $this->db->like('scl.ow', $this->input->post('ow'));
        }

        if($this->input->post('produk'))
        {
            $this->db->like('scl.nama_produk', $this->input->post('produk'));
        }

        if($this->input->post('warna'))
        {
            $this->db->like('w.nama_warna', $this->input->post('warna'));
        }

        if($this->input->post('status_ow'))
        {
            $this->db->like('scl.status', $this->input->post('status_ow'));
        }

        if($this->input->post('no_ow'))
        {
            if($this->input->post('no_ow') == 't'){
                $this->db->where('scl.ow <>"" ');
            }else if($this->input->post('no_ow') == 'f'){
                $this->db->where('scl.ow = "" ');
            }
        }

        if($this->input->post('tgl_dari'))
        {       
            $tgl_dari  = date('Y-m-d 00:00:00',strtotime($this->input->post('tgl_dari')));
            $this->db->where('scl.tanggal_ow >=', $tgl_dari);
        }

        if($this->input->post('tgl_sampai'))
        {       
            $tgl_sampai  = date('Y-m-d 23:59:59',strtotime($this->input->post('tgl_sampai')));
            $this->db->where('scl.tanggal_ow <=', $tgl_sampai);
        }

        $this->db->select(" scl.sales_order, scl.ow, msg.nama_sales_group, scl.tanggal_ow, scl.nama_produk, scl.qty, scl.uom, w.nama_warna,id_warna,ms.nama_status,
        (select IFNULL(sum(qty),0) FROM stock_quant WHERE lokasi = 'GRG/Stock' AND kode_produk = scl.kode_produk ) as tot_qty1, scl.status as status_scl,
        co.kode_co, scl.piece_info, scl.lebar_jadi, scl.uom_lebar_jadi, scl.reff_notes, scl.gramasi, hdl.nama_handling, rc.nama as nama_route, sc.delivery_date ");
        $this->db->from("sales_color_line scl");
        $this->db->join("sales_contract sc", "sc.sales_order = scl.sales_order", "inner");
        $this->db->join("mst_sales_group msg", "sc.sales_group = msg.kode_sales_group", "inner");
        $this->db->join("warna w", "scl.id_warna = w.id", "inner");
        $this->db->join("mst_handling hdl", "hdl.id = scl.id_handling", "inner");
        $this->db->join("route_co rc", "rc.kode = scl.route_co", "inner");
        $this->db->join("mst_status ms", "w.status  = ms.kode", "inner");
        $this->db->join("(SELECT co.kode_co, co.kode_sc, cod.ow FROM color_order co 
             INNER JOIN color_order_detail as cod ON cod.kode_co = co.kode_co GROUP BY cod.ow)  co", "scl.sales_order = co.kode_sc AND co.ow = scl.ow", "left");

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
		$this->db->select(" scl.sales_order, scl.ow, msg.nama_sales_group, scl.tanggal_ow, scl.nama_produk, scl.qty, scl.uom, w.nama_warna,id_warna,ms.nama_status,
        (select IFNULL(sum(qty),0) FROM stock_quant WHERE lokasi = 'GRG/Stock' AND kode_produk = scl.kode_produk ) as tot_qty1,
        co.kode_co, scl.piece_info, scl.lebar_jadi, scl.uom_lebar_jadi, scl.reff_notes, scl.gramasi, hdl.nama_handling, rc.nama as nama_route");
        $this->db->from("sales_color_line scl");
        $this->db->join("sales_contract sc", "sc.sales_order = scl.sales_order", "inner");
        $this->db->join("mst_sales_group msg", "sc.sales_group = msg.kode_sales_group", "inner");
        $this->db->join("warna w", "scl.id_warna = w.id", "inner");
        $this->db->join("mst_handling hdl", "hdl.id = scl.id_handling", "inner");
        $this->db->join("route_co rc", "rc.kode = scl.route_co", "inner");
        $this->db->join("mst_status ms", "w.status  = ms.kode", "inner");
        $this->db->join("(SELECT co.kode_co, co.kode_sc, cod.ow FROM color_order co 
             INNER JOIN color_order_detail as cod ON cod.kode_co = co.kode_co GROUP BY cod.ow)  co", "scl.sales_order = co.kode_sc AND co.ow = scl.ow", "left");

        if($this->input->post('sc'))
        {
            $this->db->like('scl.sales_order', $this->input->post('sc'));
        }

        if($this->input->post('sales_group'))
        {
            $this->db->like('msg.kode_sales_group', $this->input->post('sales_group'));
        }

        if($this->input->post('ow'))
        {
            $this->db->like('scl.ow', $this->input->post('ow'));
        }

        if($this->input->post('produk'))
        {
            $this->db->like('scl.nama_produk', $this->input->post('produk'));
        }

        if($this->input->post('warna'))
        {
            $this->db->like('w.nama_warna', $this->input->post('warna'));
        }
        if($this->input->post('tgl_dari'))
        {       
            $tgl_dari  = date('Y-m-d 00:00:00',strtotime($this->input->post('tgl_dari')));
            $this->db->where('scl.tanggal_ow >=', $tgl_dari);
        }

        if($this->input->post('tgl_sampai'))
        {       
            $tgl_sampai  = date('Y-m-d 23:59:59',strtotime($this->input->post('tgl_sampai')));
            $this->db->where('scl.tanggal_ow <=', $tgl_sampai);
        }
        if($this->input->post('status_ow'))
        {
            $this->db->like('scl.status', $this->input->post('status_ow'));
        }

        if($this->input->post('no_ow'))
        {
            if($this->input->post('no_ow') == 't'){
                $this->db->where('scl.ow <>"" ');
            }else if($this->input->post('no_ow') == 'f'){
                $this->db->where('scl.ow = "" ');
            }
        }

		return $this->db->count_all_results();
	}

    public function get_sales_color_line_by_kode($sales_order,$ow)
    {
        return $this->db->query("SELECT scl.nama_produk, scl.qty, scl.uom, scl.id_warna, w.nama_warna
                                FROM sales_color_line as scl 
                                INNER JOIN warna as w ON scl.id_warna = w.id
                                Where sales_order = '$sales_order' AND ow = '$ow'");
    }

    public function get_color_order_detail_by_OW($kode_co,$ow,$sales_order)
    {
        return $this->db->query("SELECT cod.qty, cod.uom, cod.nama_produk, cod.status, ms.nama_status, cod.row_order, b.nama as route_co,
                                    (SELECT method 
                                    FROM stock_move 
                                    where origin = CONCAT('$sales_order','|',cod.kode_co,'|',cod.row_order,'|','$ow')  AND status = 'done' AND method not LIKE '%CON%'
                                    GROUP by method
                                    order by row_order desc
                                    LIMIT 1 ) as last_method
                                FROM color_order_detail cod 
                                INNER JOIN mst_status ms ON cod.status = ms.kode
                                LEFT JOIN route_co b ON cod.route_co = b.kode
                                where cod.kode_co ='$kode_co' AND cod.ow = '$ow' ORDER BY cod.tanggal asc");
    }

    public function get_route_by_origin($origin)
	{
		return $this->db->query("SELECT distinct method FROM stock_move where origin = '$origin'")->result();
	}

    public function get_route_by_origin_method($origin,$method)
	{
		
		return $this->db->query("SELECT move_id, method FROM stock_move where origin = '$origin' AND method = '$method' ")->result();
	}

    public function get_detail_pengiriman($move_id)
	{
		return $this->db->query("SELECT a.kode, a.tanggal, a.origin, a.status, a.reff_note, a.lokasi_tujuan, b.nama as departemen, a.tanggal_transaksi,
                                 ms.nama_status
								 FROM pengiriman_barang a
								 INNER JOIN departemen b ON a.dept_id = b.kode
                                 INNER JOIN mst_status ms ON ms.kode = a.status
								 where a.move_id = '$move_id'  ORDER BY a.tanggal, a.kode asc")->row();
	}

    public function get_detail_penerimaan($move_id)
	{
		return $this->db->query("SELECT a.kode, a.tanggal, a.origin, a.status, a.reff_note, a.lokasi_tujuan, b.nama as departemen,a.tanggal_transaksi,							                      ms.nama_status
								 FROM penerimaan_barang a
								 INNER JOIN departemen b ON a.dept_id = b.kode
                                 INNER JOIN mst_status ms ON ms.kode = a.status
								 where a.move_id = '$move_id'  ORDER BY a.tanggal, a.kode asc")->row();
	}

    public function get_detail_items_mo($move_id)
	{
		return $this->db->query("SELECT mrp.kode, mrp.tanggal,mrp.dept_id,mrp.status,mrp.reff_note,mrp.origin, ms.nama_status,mrp.finish_time, mrp.start_time,
                                d.nama as departemen, mrp.qty as qty_target                                
                                FROM mrp_production mrp
                                INNER JOIN departemen d ON mrp.dept_id = d.kode 
                                INNER JOIN mrp_production_fg_target fg ON fg.kode = mrp.kode
                                INNER JOIN mst_status ms ON ms.kode = mrp.status
								 where fg.move_id = '$move_id'  ORDER BY mrp.tanggal, mrp.kode desc")->row();
	}


    public function get_detail_penerimaan_barang_items($kode)
	{
		return $this->db->query(" SELECT pb.kode, b.nama as departemen, pb.lokasi_tujuan, pbi.nama_produk, pbi.qty as qty_target, pbi.status_barang, ms.nama_status,
                                    (SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = pb.move_id AND smi.kode_produk = pbi.kode_produk) as qty_tersedia
                                FROM penerimaan_barang as pb
                                INNER JOIN penerimaan_barang_items as pbi ON pb.kode = pbi.kode
                                INNER JOIN departemen b ON pb.dept_id = b.kode
                                INNER JOIN mst_status as ms ON pbi.status_barang = ms.kode
                                where pb.kode = '$kode' ")->result();
	}

    public function get_detail_pengiriman_barang_items($kode)
	{
		return $this->db->query(" SELECT pb.kode, b.nama as departemen, pb.lokasi_tujuan, pbi.nama_produk, pbi.qty as qty_target, pbi.status_barang, ms.nama_status,
                                    (SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = pb.move_id AND smi.kode_produk = pbi.kode_produk) as qty_tersedia
                                FROM pengiriman_barang as pb
                                INNER JOIN pengiriman_barang_items as pbi ON pb.kode = pbi.kode
                                INNER JOIN departemen b ON pb.dept_id = b.kode
                                INNER JOIN mst_status as ms ON pbi.status_barang = ms.kode
                                where pb.kode = '$kode' ")->result();
	}

    public function get_detail_mrp_fg_target_items($kode)
    {
        return $this->db->query("SELECT fg.kode, b.nama as departemen,  fg.nama_produk, fg.qty as qty_target, fg.status, ms.nama_status,
                                (SELECT sum(smi.qty) FROM stock_move_items smi WHERE smi.move_id = fg.move_id AND smi.kode_produk = fg.kode_produk) as qty_tersedia
                                FROM mrp_production as mrp
                                INNER JOIN mrp_production_fg_target as fg ON fg.kode = mrp.kode
                                INNER JOIN departemen b ON mrp.dept_id = b.kode
                                INNER JOIN mst_status as ms ON fg.status = ms.kode
                                where fg.kode = '$kode'")->result();
    }

    public function get_list_ow_by_kode($tgldari,$tglsampai,$sc,$sales_group,$ow,$produk,$warna,$status_ow,$no_ow)
    {

        
        //add custom filter here
        if(!empty($sc))
        {
            $this->db->like('scl.sales_order', $sc);
        }

        if(!empty($sales_group))
        {
            $this->db->like('msg.kode_sales_group', $sales_group);
        }

        if(!empty($ow))
        {
            $this->db->like('scl.ow', $ow);
        }

        if(!empty($produk))
        {
            $this->db->like('scl.nama_produk', $produk);
        }

        if(!empty($warna))
        {
            $this->db->like('w.nama_warna', $warna);
        }

        if(!empty($tgldari))
        {       
            $tgl_dari  = date('Y-m-d 00:00:00',strtotime($tgldari));
            $this->db->where('scl.tanggal_ow >=', $tgl_dari);
        }

        if(!empty($tglsampai))
        {       
            $tgl_sampai  = date('Y-m-d 23:59:59',strtotime($tglsampai));
            $this->db->where('scl.tanggal_ow <=', $tgl_sampai);
        }

        if(!empty($status_ow))
        {
            $this->db->like('scl.status', $this->input->post('status_ow'));
        }

        if(!empty($no_ow))
        {
            if($this->input->post('no_ow') == 't'){
                $this->db->where('scl.ow <>"" ');
            }else if($this->input->post('no_ow') == 'f'){
                $this->db->where('scl.ow = "" ');
            }
        }

        $this->db->select(" scl.sales_order, scl.ow, msg.nama_sales_group, scl.tanggal_ow, scl.nama_produk, scl.qty, scl.uom, w.nama_warna,id_warna,ms.nama_status,
        (select IFNULL(sum(qty),0) FROM stock_quant WHERE lokasi = 'GRG/Stock' AND kode_produk = scl.kode_produk ) as tot_qty1, scl.status as status_scl,
        co.kode_co, scl.piece_info, scl.lebar_jadi, scl.uom_lebar_jadi, scl.reff_notes, scl.gramasi, hdl.nama_handling, rc.nama as nama_route, sc.delivery_date");
        $this->db->from("sales_color_line scl");
        $this->db->join("sales_contract sc", "sc.sales_order = scl.sales_order", "inner");
        $this->db->join("mst_sales_group msg", "sc.sales_group = msg.kode_sales_group", "inner");
        $this->db->join("warna w", "scl.id_warna = w.id", "inner");
        $this->db->join("mst_handling hdl", "hdl.id = scl.id_handling", "inner");
        $this->db->join("route_co rc", "rc.kode = scl.route_co", "inner");
        $this->db->join("mst_status ms", "w.status  = ms.kode", "inner");
        $this->db->join("(SELECT co.kode_co, co.kode_sc, cod.ow FROM color_order co 
             INNER JOIN color_order_detail as cod ON cod.kode_co = co.kode_co GROUP BY cod.ow)  co", "scl.sales_order = co.kode_sc AND co.ow = scl.ow", "left");

        $this->db->order_by("scl.tanggal_ow"," ASC");
		$query = $this->db->get();
		return $query->result();
    }


}