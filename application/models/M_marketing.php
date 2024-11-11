<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class m_marketing extends CI_Model
{   

	protected $lokasi 	= 'GJD/Stock';
	protected $category = '21';
	protected $lokasi_fisik = '6Z.01.Z';


    var $column_order = array(null, 'sq.corak_remark','sq.lebar_jadi','gl','qty1','sq.uom_jual');
	var $column_search= array('sq.corak_remark','sq.lebar_jadi','sq.qty_jual','sq.uom_jual');
	var $order  	  = array('sq.corak_remark' => 'asc');

    private function get_query()
    {
        if($this->input->post('product')){
    		$this->db->like('corak_remark',$this->input->post('product'));
        }

        if($this->input->post('color')){
    		$this->db->like('sq.warna_remark',$this->input->post('color'));
        }

        if($this->input->post('marketing') != 'All'){
    		$this->db->where('sq.sales_group',$this->input->post('marketing'));
        }

		$this->db->SELECT("sq.corak_remark, sq.lebar_jadi, sq.uom_lebar_jadi, count(sq.lot) as gl, sum(sq.qty_jual) as qty1, sq.uom_jual");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("mst_produk mp","sq.kode_produk = mp.kode_produk","INNER");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE('mp.id_category',$this->category);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);
		$this->db->group_by('sq.corak_remark');
		$this->db->group_by('sq.lebar_jadi');
		$this->db->group_by('sq.uom_jual');

        return;
    }

    private function _get_datatables_query()
	{
		
        $this->get_query();

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
		$this->get_query();
		return $this->db->count_all_results();
	} 

    public function count_all_no_group()
	{

		if($this->input->post('product')){
    		$this->db->like('corak_remark',$this->input->post('product'));
        }

        if($this->input->post('color')){
    		$this->db->like('sq.warna_remark',$this->input->post('color'));
        }

        if($this->input->post('marketing') != 'All'){
    		$this->db->where('sq.sales_group',$this->input->post('marketing'));
        }

		$this->db->SELECT("sq.corak_remark, sq.lebar_jadi, sq.uom_lebar_jadi, count(sq.lot) as gl, sum(sq.qty_jual) as qty1, sq.uom_jual");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("mst_produk mp","sq.kode_produk = mp.kode_produk","INNER");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE('mp.id_category',$this->category);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);
		return $this->db->count_all_results();
	} 

	var $column_order2 = array(null, 'sq.lot','sq.corak_remark','sq.warna_remark','sq.lebar_jadi','sq.qty_jual','sq.qty2_jual','sq.lokasi_fisik','kp_lot.lot', 'sq.sales_order', 'pl.no_pl', 'umur');
	var $column_search2= array('sq.lot','sq.warna_remark','sq.corak_remark','sq.lebar_jadi','sq.qty_jual','sq.lokasi_fisik', 'kp_lot.lot', 'sq.sales_order' ,'pl.no_pl');
	var $order2  	  = array('sq.lot' => 'asc');

	function get_query_items()
	{
		if($this->input->post('product')){
    		$this->db->where('sq.corak_remark',$this->input->post('product'));
        }

        if($this->input->post('color')){
    		$this->db->like('sq.warna_remark',$this->input->post('color'));
        }

		if($this->input->post('lebar_jadi')){
    		$this->db->where('sq.lebar_jadi',$this->input->post('lebar_jadi'));
        }

		if($this->input->post('uom_jual')){
    		$this->db->where('sq.uom_jual',$this->input->post('uom_jual'));
        }

        if($this->input->post('marketing') != 'All'){
    		$this->db->where('sq.sales_group',$this->input->post('marketing'));
        }


		$this->db->SELECT("sq.lot, sq.warna_remark, sq.corak_remark, sq.lebar_jadi, sq.uom_lebar_jadi, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual, sq.lokasi_fisik, sq.sales_order,
							kp_lot.lot as lot_asal, pl.no_pl, (datediff(now(), sq.create_date) ) as umur  ");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("
						(SELECT spl.lot, spli.quant_id_baru as quant_id FROM split spl INNER JOIN split_items spli ON spl.kode_split = spli.kode_split
						UNION SELECT (SELECT GROUP_CONCAT(lot) as lot FROM join_lot_items where kode_join = jl.kode_join) as lot, jl.quant_id
											FROM join_lot jl	WHERE jl.status = 'done' 
						UNION SELECT mrpin.lot, fg.quant_id FROM mrp_production_fg_hasil fg INNER JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
						UNION SELECT lot, quant_id FROM stock_kain_jadi_migrasi )  kp_lot", "kp_lot.quant_id = sq.quant_id","LEFT" );
		$this->db->JOIN("(SELECT no_pl, quant_id FROM picklist_detail where valid NOT IN ('cancel') )  pl", "pl.quant_id = sq.quant_id", "LEFT");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);

		return;
	}

	private function _get_datatables_query2()
	{
		
		$this->get_query_items();

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

	function get_datatables2_excel()
	{
		$this->get_query_items();
		$query = $this->db->get();
		return $query->result();
	}

	function get_datatables2()
	{
		$this->_get_datatables_query2();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered2()
	{
		$this->_get_datatables_query2();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all2()
	{
		$this->get_query_items();
		return $this->db->count_all_results();
	} 


	var $column_order3 = array(null, 'sq.lot','sq.corak_remark','sq.warna_remark','sq.lebar_jadi','sq.qty_jual','sq.qty2_jual','sq.lokasi_fisik','kp_lot.lot', 'sq.sales_order','pl.no_pl');
	var $column_search3= array('sq.lot','sq.warna_remark','sq.corak_remark','sq.lebar_jadi','sq.qty_jual','sq.lokasi_fisik','kp_lot.lot', 'sq.sales_order','pl.no_pl');
	var $order3  	  = array('sq.lot' => 'asc');

	function get_query_items3()
	{
		if($this->input->post('lokasi')){
    		$this->db->where('sq.lokasi_fisik',$this->input->post('lokasi'));
        }

		$this->db->SELECT("sq.lot, sq.warna_remark, sq.corak_remark, sq.lebar_jadi, sq.uom_lebar_jadi, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual, sq.lokasi_fisik, sq.sales_order,
							kp_lot.lot as lot_asal, pl.no_pl ");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("
						(SELECT spl.lot, spli.quant_id_baru as quant_id FROM split spl INNER JOIN split_items spli ON spl.kode_split = spli.kode_split
						UNION SELECT (SELECT GROUP_CONCAT(lot) as lot FROM join_lot_items where kode_join = jl.kode_join) as lot, jl.quant_id
											FROM join_lot jl	WHERE jl.status = 'done' 
						UNION SELECT mrpin.lot, fg.quant_id FROM mrp_production_fg_hasil fg INNER JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
						UNION SELECT lot, quant_id FROM stock_kain_jadi_migrasi )  kp_lot", "kp_lot.quant_id = sq.quant_id","LEFT" );
		$this->db->JOIN("(SELECT no_pl, quant_id FROM picklist_detail where valid NOT IN ('cancel') )  pl", "pl.quant_id = sq.quant_id", "LEFT");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);

		return;
	}

	private function _get_datatables_query3()
	{
		
		$this->get_query_items3();

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

	function get_datatables3_excel()
	{
		$this->get_query_items3();
		$query = $this->db->get();
		return $query->result();
	}

	function get_datatables3()
	{
		$this->_get_datatables_query3();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered3()
	{
		$this->_get_datatables_query3();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all3()
	{
		$this->get_query_items3();
		return $this->db->count_all_results();
	} 


	var $column_order4 = array(null, 'sq.corak_remark','sq.lebar_jadi','gl','qty1','sq.uom_jual');
	var $column_search4= array('sq.corak_remark','sq.lebar_jadi','sq.qty_jual','sq.uom_jual');
	var $order4  	  = array('sq.corak_remark' => 'asc');

    private function get_query4()
    {
        if($this->input->post('product')){
    		$this->db->like('sq.corak_remark',$this->input->post('product'));
        }

        if($this->input->post('color')){
    		$this->db->like('sq.warna_remark',$this->input->post('color'));
        }

        if($this->input->post('marketing') != 'All'){
    		$this->db->where('sq.sales_group',$this->input->post('marketing'));
        }

		if($this->input->post('grade') != 'All'){
    		$this->db->where('sq.nama_grade',$this->input->post('grade'));
        }

		$tgl_sekarang = date('Y-m-d');
        $tgl_sebelum = date('Y-m-d', strtotime('-3 month', strtotime($tgl_sekarang)));

		if($this->input->post('expired') != 'All'){
			if($this->input->post('expired') == 'Ya'){
				$this->db->where("STR_TO_DATE(sq.create_date,'%Y-%m-%d') <= ",$tgl_sebelum);
			}else{
				$this->db->where("STR_TO_DATE(sq.create_date,'%Y-%m-%d') >= ",$tgl_sebelum);
				$this->db->where("STR_TO_DATE(sq.create_date,'%Y-%m-%d') <= ",$tgl_sekarang);
			}
        }

		$this->db->SELECT("sq.corak_remark, sq.lebar_jadi, sq.uom_lebar_jadi, count(sq.lot) as gl, sum(sq.qty_jual) as qty1, sq.uom_jual, ");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("mst_produk mp","sq.kode_produk = mp.kode_produk","INNER");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE('mp.id_category',$this->category);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);
		$this->db->group_by('sq.corak_remark');
		$this->db->group_by('sq.lebar_jadi');
		$this->db->group_by('sq.uom_jual');

        return;
    }

    private function _get_datatables_query4()
	{
		
        $this->get_query4();

        $i = 0;
		foreach ($this->column_search4 as $item) // loop column 
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

				if(count($this->column_search4) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order4[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order4))
		{
			$order = $this->order4;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables4()
	{
		$this->_get_datatables_query4();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered4()
	{
		$this->_get_datatables_query4();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all4()
	{
		$this->get_query4();
		return $this->db->count_all_results();
	} 

    public function count_all_no_group4()
	{

		if($this->input->post('product')){
    		$this->db->like('sq.corak_remark',$this->input->post('product'));
        }

        if($this->input->post('color')){
    		$this->db->like('sq.warna_remark',$this->input->post('color'));
        }

        if($this->input->post('marketing') != 'All'){
    		$this->db->where('sq.sales_group',$this->input->post('marketing'));
        }

		if($this->input->post('grade') != 'All'){
    		$this->db->where('sq.nama_grade',$this->input->post('grade'));
        }

		$tgl_sekarang = date('Y-m-d');
        $tgl_sebelum = date('Y-m-d', strtotime('-3 month', strtotime($tgl_sekarang)));

		if($this->input->post('expired') != 'All'){
			if($this->input->post('expired') == 'Ya'){
				$this->db->where("STR_TO_DATE(sq.create_date,'%Y-%m-%d') <= ",$tgl_sebelum);
			}else{
				$this->db->where("STR_TO_DATE(sq.create_date,'%Y-%m-%d') >= ",$tgl_sebelum);
				$this->db->where("STR_TO_DATE(sq.create_date,'%Y-%m-%d') <= ",$tgl_sekarang);
			}
        }

		$this->db->SELECT("sq.corak_remark, sq.lebar_jadi, sq.uom_lebar_jadi, count(sq.lot) as gl, sum(sq.qty_jual) as qty1, sq.uom_jual, ");
		$this->db->FROM("stock_quant sq");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);
		return $this->db->count_all_results();
	} 



	var $column_order5 = array(null, 'sq.create_date','sq.lot','sq.corak_remark','sq.warna_remark','sq.lebar_jadi','sq.qty_jual','sq.qty2_jual','sq.lokasi_fisik','kp_lot.lot', 'sq.sales_order','pl.no_pl','umur',null);
	var $column_search5= array('sq.lot','sq.warna_remark','sq.corak_remark','sq.lebar_jadi','sq.qty_jual','sq.lokasi_fisik','kp_lot.lot', 'sq.sales_order', 'pl.no_pl');
	var $order5  	  = array('sq.lot' => 'asc');

	function get_query_items5()
	{
		if($this->input->post('product')){
    		$this->db->where('sq.corak_remark',$this->input->post('product'));
        }

        if($this->input->post('color')){
    		$this->db->like('sq.warna_remark',$this->input->post('color'));
        }

        if($this->input->post('marketing') != 'All'){
    		$this->db->where('sq.sales_group',$this->input->post('marketing'));
        }

		if($this->input->post('grade') != 'All'){
    		$this->db->where('sq.nama_grade',$this->input->post('grade'));
        }


		if($this->input->post('uom_jual')){
    		$this->db->where('sq.uom_jual',$this->input->post('uom_jual'));
        }

		if($this->input->post('lebar_jadi')){
    		$this->db->where('sq.lebar_jadi',$this->input->post('lebar_jadi'));
        }


		$tgl_sekarang = date('Y-m-d');
        $tgl_sebelum = date('Y-m-d', strtotime('-3 month', strtotime($tgl_sekarang)));

		if($this->input->post('expired') != 'All'){
			if($this->input->post('expired') == 'Ya'){
				$this->db->where("STR_TO_DATE(sq.create_date,'%Y-%m-%d') <= ",$tgl_sebelum);
			}else{
				$this->db->where("STR_TO_DATE(sq.create_date,'%Y-%m-%d') >= ",$tgl_sebelum);
				$this->db->where("STR_TO_DATE(sq.create_date,'%Y-%m-%d') <= ",$tgl_sekarang);
			}
        }


		$this->db->SELECT("sq.create_date,sq.lot, sq.warna_remark, sq.corak_remark, sq.lebar_jadi, sq.uom_lebar_jadi, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual, sq.lokasi_fisik, sq.sales_order,
							kp_lot.lot as lot_asal, pl.no_pl,(datediff(now(), sq.create_date) ) as umur ");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("
						(SELECT spl.lot, spli.quant_id_baru as quant_id FROM split spl INNER JOIN split_items spli ON spl.kode_split = spli.kode_split
						UNION SELECT (SELECT GROUP_CONCAT(lot) as lot FROM join_lot_items where kode_join = jl.kode_join) as lot, jl.quant_id
											FROM join_lot jl	WHERE jl.status = 'done' 
						UNION SELECT mrpin.lot, fg.quant_id FROM mrp_production_fg_hasil fg INNER JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
						UNION SELECT lot, quant_id FROM stock_kain_jadi_migrasi )  kp_lot", "kp_lot.quant_id = sq.quant_id","LEFT" );
		$this->db->JOIN("(SELECT no_pl, quant_id FROM picklist_detail where valid NOT IN ('cancel') )  pl", "pl.quant_id = sq.quant_id", "LEFT");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);

		return;
	}

	private function _get_datatables_query5()
	{
		
		$this->get_query_items5();

        $i = 0;
		foreach ($this->column_search5 as $item) // loop column 
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

				if(count($this->column_search5) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order5[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order5))
		{
			$order = $this->order5;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables5_excel()
	{
		$this->get_query_items5();
		$query = $this->db->get();
		return $query->result();
	}

	function get_datatables5()
	{
		$this->_get_datatables_query5();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered5()
	{
		$this->_get_datatables_query5();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all5()
	{
		$this->get_query_items5();
		return $this->db->count_all_results();
	} 


	var $column_order6 = array(null, 'sq.corak_remark','gl');
	var $column_search6= array('sq.corak_remark');
	var $order6  	  = array('sq.corak_remark' => 'asc');

    private function get_query6()
    {
        if($this->input->post('product')){
    		$this->db->like('sq.corak_remark',$this->input->post('product'));
        }

		$this->db->SELECT("sq.corak_remark, count(sq.lot) as gl");
		$this->db->FROM("stock_quant sq");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);
		$this->db->group_by('sq.corak_remark');

        return;
    }

    private function _get_datatables_query6()
	{
		
        $this->get_query6();

        $i = 0;
		foreach ($this->column_search6 as $item) // loop column 
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

				if(count($this->column_search6) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order6[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order6))
		{
			$order = $this->order6;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables6()
	{
		$this->_get_datatables_query6();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered6()
	{
		$this->_get_datatables_query6();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all6()
	{
		$this->get_query6();
		return $this->db->count_all_results();
	} 

    public function count_all_no_group6()
	{

		if($this->input->post('product')){
    		$this->db->like('sq.corak_remark',$this->input->post('product'));
        }

		$this->db->SELECT("sq.corak_remark, count(sq.lot) as gl");
		$this->db->FROM("stock_quant sq");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);
		return $this->db->count_all_results();
	} 


	var $column_order7 = array(null, 'warna_remark','gl');
	var $column_search7= array('warna_remark');
	var $order7  	  = array('warna_remark' => 'asc');

    private function get_query7()
    {
        if($this->input->post('product')){
    		$this->db->WHERE('sq.corak_remark',$this->input->post('product'));
        }

		$this->db->SELECT("sq.warna_remark, count(sq.lot) as gl");
		$this->db->FROM("stock_quant sq");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);
		$this->db->group_by('sq.warna_remark');

        return;
    }

    private function _get_datatables_query7()
	{
		
        $this->get_query7();

        $i = 0;
		foreach ($this->column_search7 as $item) // loop column 
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

				if(count($this->column_search7) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order7[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order7))
		{
			$order = $this->order7;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables7()
	{
		$this->_get_datatables_query7();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered7()
	{
		$this->_get_datatables_query7();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all7()
	{
		$this->get_query7();
		return $this->db->count_all_results();
	} 

    public function count_all_no_group7()
	{

		if($this->input->post('product')){
    		$this->db->WHERE('sq.corak_remark',$this->input->post('product'));
        }

		$this->db->SELECT("sq.warna_remark, count(sq.lot) as gl");
		$this->db->FROM("stock_quant sq");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);
		return $this->db->count_all_results();
	} 


	var $column_order8 = array(null, 'sq.lot','corak_remark','warna_remark','sq.lebar_jadi','sq.qty_jual','qty_jual','lokasi_fisik','kp_lot.lot', 'sq.sales_order','pl.no_pl');
	var $column_search8= array('sq.lot','warna_remark','corak_remark','sq.lebar_jadi','sq.qty_jual','lokasi_fisik','kp_lot.lot', 'sq.sales_order','pl.no_pl');
	var $order8  	  = array('sq.lot' => 'asc');

	function get_query_items8()
	{
		if($this->input->post('product')){
    		$this->db->where('sq.corak_remark',$this->input->post('product'));
        }

        if($this->input->post('color')){
    		$this->db->like('sq.warna_remark',$this->input->post('color'));
        }

		$this->db->SELECT("sq.lot, sq.warna_remark, sq.corak_remark, sq.lebar_jadi, sq.uom_lebar_jadi, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual, sq.lokasi_fisik, sq.sales_order,
							kp_lot.lot as lot_asal, pl.no_pl ");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("
						(SELECT spl.lot, spli.quant_id_baru as quant_id FROM split spl INNER JOIN split_items spli ON spl.kode_split = spli.kode_split
						UNION SELECT (SELECT GROUP_CONCAT(lot) as lot FROM join_lot_items where kode_join = jl.kode_join) as lot, jl.quant_id
											FROM join_lot jl	WHERE jl.status = 'done' 
						UNION SELECT mrpin.lot, fg.quant_id FROM mrp_production_fg_hasil fg INNER JOIN mrp_inlet mrpin ON fg.id_inlet = mrpin.id
						UNION SELECT lot, quant_id FROM stock_kain_jadi_migrasi )  kp_lot", "kp_lot.quant_id = sq.quant_id","LEFT" );
		$this->db->JOIN("(SELECT no_pl, quant_id FROM picklist_detail where valid NOT IN ('cancel') )  pl", "pl.quant_id = sq.quant_id", "LEFT");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->lokasi_fisik);

		return;
	}

	private function _get_datatables_query8()
	{
		
		$this->get_query_items8();

        $i = 0;
		foreach ($this->column_search8 as $item) // loop column 
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

				if(count($this->column_search8) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order8[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order8))
		{
			$order = $this->order8;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables8_excel()
	{
		$this->get_query_items8();
		$query = $this->db->get();
		return $query->result();
	}

	function get_datatables8()
	{
		$this->_get_datatables_query8();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered8()
	{
		$this->_get_datatables_query8();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all8()
	{
		$this->get_query_items8();
		return $this->db->count_all_results();
	} 


	public function get_list_mst_sales_group()
	{
		$this->db->order_by('kode_sales_group','asc');
		$this->db->where('view','1');
		$query = $this->db->get('mst_sales_group');
		return $query->result();
	}

	public function get_data_stock_by_mkt($tgldari,$tglsampai,$mkt)
	{
		$this->db->order_by('tanggal','asc');
		$this->db->where("tanggal >= '".$tgldari."'");
		$this->db->where("tanggal <= '".$tglsampai."'");
		$this->db->where("mkt",$mkt);
		$query = $this->db->get('stock_history_gjd');
		return $query->result();
	}



	var $column_order9 = array(null, 'tanggal','hen','mei','ts','vi','al');
	var $column_search9= array('tanggal','hen','mei','ts','vi','al');
	var $order9  	  = array('tanggal' => 'asc');

	function get_query_items9()
	{
		if($this->input->post('tgldari')){
			$tgldari = date("Y-m-d H:i:s", strtotime($this->input->post('tgldari')));
    		$this->db->where('tanggal >= "'.$tgldari.'"');
        }

        if($this->input->post('tglsampai')){
			$tglsampai = date("Y-m-d 23:59:59", strtotime($this->input->post('tglsampai')));
    		$this->db->where('tanggal <= "'.$tglsampai.'"');
        }

		// $this->db->SELECT("sq.lot, sq.warna_remark, sq.corak_remark, sq.lebar_jadi, sq.uom_lebar_jadi, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual, sq.lokasi_fisik");
		// $this->db->FROM("stock_quant sq");
		// $this->db->JOIN("mst_produk mp","sq.kode_produk = mp.kode_produk","INNER");
        // $this->db->WHERE("sq.lokasi",$this->lokasi);
		// $this->db->WHERE('mp.id_category',$this->category);

		$this->db->SELECT("tanggal,sum(NMBB) AS hen, 
               sum(NMBL) AS mei, sum(TMBX) AS ts, 
               sum(TMBL) AS vi,
               sum(TMBX+TMBL+NMBL+NMBB) AS al");
		$this->db->FROM("(SELECT tanggal, 
                          if(mkt='NMBB', l_stock,0) AS NMBB,
                          if(mkt='NMBL', l_stock,0) AS NMBL,
                          if(mkt='TMBX', l_stock,0) AS TMBX,
                          if(mkt='TMBL', l_stock,0) AS TMBL
        				from stock_history_gjd) AS inti");
		$this->db->group_by('tanggal');

		return;
	}

	private function _get_datatables_query9()
	{
		
		$this->get_query_items9();

        $i = 0;
		foreach ($this->column_search9 as $item) // loop column 
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

				if(count($this->column_search9) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order9[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order9))
		{
			$order = $this->order9;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables9()
	{
		$this->_get_datatables_query9();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered9()
	{
		$this->_get_datatables_query9();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all9()
	{
		$this->get_query_items9();
		return $this->db->count_all_results();
	} 


	function query_9_excel()
	{
		$this->get_query_items9();
		$query = $this->db->get();
		return $query->result();
	}


	var $column_order10 = array(null, 'sq.corak_remark','total_warna','sq.lebar_jadi','total_qty_jual','total_qty2_jual','gl');
	var $column_search10= array('sq.corak_remark','sq.lebar_jadi','sq.uom_jual','sq.uom2_jual');
	var $order10  	  = array('sq.corak_remark' => 'asc');
	var $f_jenis_kain  = array(1,2,3,4);
	var $f_nama_grade = array('A');
	var $f_corak_remark = array('B GRADE','TALI','B-','B GRIDE','BIGRET','BS','B BRADE','B GBRADE','BGRADE','G-GRADE','GRADE','POTONGAN','MIX','TANPA CORAK','PROOF','SAMPLE','PROF');
	var $f_corak_remark_af = array('P');
	var $f_lokasi_fisik = array('XPD','PORT','6Z.01.Z','GJD 4');

    private function get_query_10()
    {

		if($this->input->post('search_field')){
			$cmbSearch = $this->input->post('cmbSearch');
			$cmbOperator = $this->input->post('cmbOperator');
			$search = $this->input->post('search_field');
    		$this->db->having('sum(qty_jual) '.$cmbOperator.' '.$search);
        }

		$this->db->SELECT("sq.corak_remark, COUNT(DISTINCT(sq.warna_remark)) as total_warna, sq.lebar_jadi, sq.uom_lebar_jadi, CONCAT(sq.lebar_jadi,' ',sq.uom_lebar_jadi) as lebar_jadi_merge, sum(qty_jual) as total_qty_jual, sq.uom_jual, sum(qty2_jual) as total_qty2_jual, sq.uom2_jual, count(sq.lot) as gl");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("mst_produk mp","sq.kode_produk = mp.kode_produk","INNER");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE('mp.id_category',$this->category);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->f_lokasi_fisik);
		$this->db->WHERE('datediff(now(), sq.create_date) > 90');
		$this->db->WHERE_IN('mp.id_jenis_kain',$this->f_jenis_kain);
		$this->db->WHERE_IN('sq.nama_grade',$this->f_nama_grade);

		foreach ($this->f_corak_remark as $value) {
			$this->db->not_like("sq.corak_remark", $value);
        }

		foreach ($this->f_corak_remark_af as $value) {
			$this->db->not_like("sq.corak_remark", $value, "after");
        }
		$this->db->group_by('sq.corak_remark');
		$this->db->group_by("CONCAT(trim(sq.lebar_jadi),' ',trim(sq.uom_lebar_jadi))");
		$this->db->group_by('sq.uom_jual');
		$this->db->group_by('sq.uom2_jual');


        return;
    }

    private function _get_datatables_query10()
	{
		
        $this->get_query_10();

        $i = 0;
		foreach ($this->column_search10 as $item) // loop column 
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

				if(count($this->column_search10) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order10[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order10))
		{
			$order = $this->order10;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables10()
	{
		$this->_get_datatables_query10();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered10()
	{
		$this->_get_datatables_query10();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all10()
	{
		$this->get_query_10();
		return $this->db->count_all_results();
	} 


	 public function count_all_no_group10()
	{

		$this->get_query_10();
		$result = $this->db->get_compiled_select();
		// $result = $this->db->get();

		$this->db->select('sum(gl) as total');
		$this->db->from(' ('.$result.') as a');
		$query = $this->db->get();
		$result2 = $query->row();
		return $result2->total ?? 0;
	} 


	function get_datatables10_excel()
	{
		$this->get_query_10();
		$query = $this->db->get();
		return $query->result();
	}

	var $column_order11 = array(null, 'sq.corak_remark','sq.warna_remark','sq.lebar_jadi','total_qty_jual','total_qty2_jual','gl');
	var $column_search11= array('sq.corak_remark','sq.warna_remark','sq.lebar_jadi','sq.uom_jual','sq.uom2_jual');
	var $order11  	  = array('sq.corak_remark' => 'asc','sq.warna_remark' => 'asc');

	private function get_query_11()
    {	
		if($this->input->post('product')){
    		$this->db->where('sq.corak_remark',$this->input->post('product'));
        }

		if($this->input->post('lebar_jadi')){
    		$this->db->where('sq.lebar_jadi',$this->input->post('lebar_jadi'));
        }

		if($this->input->post('uom_lebar_jadi')){
    		$this->db->where('sq.uom_lebar_jadi',$this->input->post('uom_lebar_jadi'));
        }

		if($this->input->post('uom_jual')){
    		$this->db->where('sq.uom_jual',$this->input->post('uom_jual'));
        }

		if($this->input->post('uom2_jual')){
    		$this->db->where('sq.uom2_jual',$this->input->post('uom2_jual'));
        }

		$this->db->SELECT("sq.corak_remark, sq.warna_remark,  sq.lebar_jadi, sq.uom_lebar_jadi, CONCAT(sq.lebar_jadi,' ',sq.uom_lebar_jadi) as lebar_jadi_merge,  sum(qty_jual) as total_qty_jual, sq.uom_jual, sum(qty2_jual) as total_qty2_jual, sq.uom2_jual,  count(sq.lot) as gl");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("mst_produk mp","sq.kode_produk = mp.kode_produk","INNER");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE('mp.id_category',$this->category);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->f_lokasi_fisik);
		$this->db->WHERE('datediff(now(), sq.create_date) > 90');
		$this->db->WHERE_IN('mp.id_jenis_kain',$this->f_jenis_kain);
		$this->db->WHERE_IN('sq.nama_grade',$this->f_nama_grade);

		foreach ($this->f_corak_remark as $value) {
			$this->db->not_like("sq.corak_remark", $value);
        }
		foreach ($this->f_corak_remark_af as $value) {
			$this->db->not_like("sq.corak_remark", $value, "after");
        }
		$this->db->group_by('sq.corak_remark');
		$this->db->group_by('sq.warna_remark');
		$this->db->group_by("CONCAT(trim(sq.lebar_jadi),' ',trim(sq.uom_lebar_jadi))");
		$this->db->group_by('sq.uom_jual');
		$this->db->group_by('sq.uom2_jual');

        return;
    }

    private function _get_datatables_query11()
	{
		
        $this->get_query_11();

        $i = 0;
		foreach ($this->column_search11 as $item) // loop column 
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

				if(count($this->column_search11) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order11[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order11))
		{
			$order = $this->order11;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables11()
	{
		$this->_get_datatables_query11();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered11()
	{
		$this->_get_datatables_query11();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all11()
	{
		$this->get_query_11();
		return $this->db->count_all_results();
	} 


	public function count_all_no_group11()
	{

		if($this->input->post('product')){
    		$this->db->where('sq.corak_remark',$this->input->post('product'));
        }
		if($this->input->post('lebar_jadi')){
    		$this->db->where('sq.lebar_jadi',$this->input->post('lebar_jadi'));
        }

		if($this->input->post('uom_lebar_jadi')){
    		$this->db->where('sq.uom_lebar_jadi',$this->input->post('uom_lebar_jadi'));
        }

		if($this->input->post('uom_jual')){
    		$this->db->where('sq.uom_jual',$this->input->post('uom_jual'));
        }

		if($this->input->post('uom2_jual')){
    		$this->db->where('sq.uom2_jual',$this->input->post('uom2_jual'));
        }

		$this->db->SELECT("sq.corak_remark, sq.warna_remark, sq.uom_jual, count(sq.lot) as gl");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("mst_produk mp","sq.kode_produk = mp.kode_produk","INNER");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE('mp.id_category',$this->category);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->f_lokasi_fisik);
		$this->db->WHERE('datediff(now(), sq.create_date) > 90');
		$this->db->WHERE_IN('mp.id_jenis_kain',$this->f_jenis_kain);
		$this->db->WHERE_IN('sq.nama_grade',$this->f_nama_grade);

		foreach ($this->f_corak_remark as $value) {
			$this->db->not_like("sq.corak_remark", $value);
        }
		foreach ($this->f_corak_remark_af as $value) {
			$this->db->not_like("sq.corak_remark", $value, "after");
        }

		return $this->db->count_all_results();
	} 


	var $column_order12 = array(null,'sq.create_date', 'sq.lot','sq.corak_remark','sq.warna_remark','sq.lebar_jadi','sq.qty_jual','sq.qty2_jual','sq.lokasi_fisik','umur');
	var $column_search12= array('sq.create_date','sq.lot','sq.corak_remark','sq.warna_remark','sq.lebar_jadi','sq.qty_jual','sq.qty2_jual','sq.lokasi_fisik');
	var $order12  	  = array('sq.corak_remark' => 'asc','sq.warna_remark' => 'asc');

	private function get_query_12()
    {	
		if($this->input->post('product')){
    		$this->db->where('sq.corak_remark',$this->input->post('product'));
        }

		if($this->input->post('color')){
    		$this->db->where('sq.warna_remark',$this->input->post('color'));
        }

		if($this->input->post('lebar_jadi')){
    		$this->db->where('sq.lebar_jadi',$this->input->post('lebar_jadi'));
        }

		if($this->input->post('uom_lebar_jadi')){
    		$this->db->where('sq.uom_lebar_jadi',$this->input->post('uom_lebar_jadi'));
        }

		if($this->input->post('uom_jual')){
    		$this->db->where('sq.uom_jual',$this->input->post('uom_jual'));
        }

		if($this->input->post('uom2_jual')){
    		$this->db->where('sq.uom2_jual',$this->input->post('uom2_jual'));
        }

		$this->db->SELECT("sq.create_date, sq.lot, sq.corak_remark, sq.warna_remark, sq.lebar_jadi, sq.uom_lebar_jadi, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual, sq.lokasi_fisik, (datediff(now(), sq.create_date) ) as umur ");
		$this->db->FROM("stock_quant sq");
		$this->db->JOIN("mst_produk mp","sq.kode_produk = mp.kode_produk","INNER");
        $this->db->WHERE("sq.lokasi",$this->lokasi);
		$this->db->WHERE('mp.id_category',$this->category);
		$this->db->WHERE_NOT_IN('sq.lokasi_fisik',$this->f_lokasi_fisik);
		$this->db->WHERE('datediff(now(), sq.create_date) > 90');
		$this->db->WHERE_IN('mp.id_jenis_kain',$this->f_jenis_kain);
		$this->db->WHERE_IN('sq.nama_grade',$this->f_nama_grade);

		foreach ($this->f_corak_remark as $value) {
			$this->db->not_like("sq.corak_remark", $value);
        }
		foreach ($this->f_corak_remark_af as $value) {
			$this->db->not_like("sq.corak_remark", $value, "after");
        }

        return;
    }

    private function _get_datatables_query12()
	{
		
        $this->get_query_12();

        $i = 0;
		foreach ($this->column_search12 as $item) // loop column 
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

				if(count($this->column_search12) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order12[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order12))
		{
			$order = $this->order12;
			$this->db->order_by(key($order), $order[key($order)]);
		}
	}

	function get_datatables12()
	{
		$this->_get_datatables_query12();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered12()
	{
		$this->_get_datatables_query12();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all12()
	{
		$this->get_query_12();
		return $this->db->count_all_results();
	} 


	public function count_all_no_group12()
	{
		$this->get_query_12();
		return $this->db->count_all_results();
	} 

	function get_datatables12_excel()
	{
		$this->get_query_12();
		$query = $this->db->get();
		return $query->result();
	}


	var $column_order13 = array(null,'cat_id','corak','warna','lebar_Jadi','qty_jual','qty2_jual','jumlah_lot');
	var $column_search13= array('cat_id','corak','warna','lebar_Jadi','qty_jual','qty2_jual','jumlah_lot');
	var $order13  	  = array('cat_id' => 'asc','corak' => 'asc','warna' => 'asc');
	var $table13      = "ready_goods_history";

	function get_last_date_history()
	{
		// get last tgl
		$this->db->SELECT('tanggal');
		$this->db->from('ready_goods_history');
		$this->db->group_by('date_format(tanggal, "%Y-%m-%d")');
		$this->db->order_by('date_format(tanggal, "%Y-%m-%d") desc');
		$this->db->limit(1);
		$query = $this->db->get();
		$resultq = $query->row();
		return	$resultq->tanggal ?? '';

	}

	private function get_query_13()
    {	
		
		$tanggal_history = $this->get_last_date_history();
		
		$this->db->SELECT('*');
		$this->db->from($this->table13);
		$this->db->where('tanggal',$tanggal_history);
        return;
    }

    private function _get_datatables_query13()
	{
		
        $this->get_query_13();

        $i = 0;
		foreach ($this->column_search13 as $item) // loop column 
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

				if(count($this->column_search13) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order13[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order13))
		{
			$order = $this->order13;
			foreach($order as $key => $value){
				$this->db->order_by($key, $value);
			}
		}
	}

	function get_datatables13()
	{
		$this->_get_datatables_query13();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered13()
	{
		$this->_get_datatables_query13();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all13()
	{
		$this->get_query_13();
		return $this->db->count_all_results();
	} 

	public function count_all_no_group13()
	{
		$this->get_query_13();
		return $this->db->count_all_results();
	} 


	public function get_data_ready_goods_category()
	{
		$this->get_query_13();
		$query = $this->db->get();
		return $query->result();
	}

	public function get_query_13_print($category)
    {	
		
		$tanggal_history = $this->get_query_13();
		$this->db->where('cat_id',$category);
		$query = $this->db->get();
		return $query->result();
    }


	function get_last_date_history_2()
	{
		// get last tgl
		$this->db->SELECT('tanggal');
		$this->db->from('ready_goods_history');
		$this->db->group_by('date_format(tanggal, "%Y-%m-%d")');
		$this->db->order_by('date_format(tanggal, "%Y-%m-%d") desc');
		$this->db->limit(2,1);
		$query = $this->db->get();
		$resultq = $query->row();
		return	$resultq->tanggal ?? '';
	}

	function get_data_all_13($tanggal)
	{
		$this->db->SELECT('*');
		$this->db->from($this->table13);
		$this->db->where('tanggal',$tanggal);
		$this->db->order_by('cat_id','asc');
		$this->db->order_by('corak','asc');
		$this->db->order_by('warna','asc');
		$query = $this->db->get();
		return $query->result();
	}


	
    function insert_data_last_date($data)
    {
        try{
            $this->db->insert_batch('ready_goods_history_changed', $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        }catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

	function delete_table()
	{
 		$this->db->from('ready_goods_history_changed');
		$this->db->truncate();
	}

	function cek_data_in_table($corak,$warna,$lebar_jadi,$uom_lebar_jadi)
	{
		$this->db->where('corak',$corak);
		$this->db->where('warna',$warna);
		$this->db->where('lebar_Jadi',$lebar_jadi);
		$this->db->where('uom_lebar_jadi',$uom_lebar_jadi);
 		$this->db->from('ready_goods_history_changed');
		$query = $this->db->get();
		return $query->row();

	}

	function update_table_changed($data,$corak,$warna,$lebar_jadi,$uom_lebar_jadi)
	{
		$this->db->where('corak',$corak);
		$this->db->where('warna',$warna);
		$this->db->where('lebar_Jadi',$lebar_jadi);
		$this->db->where('uom_lebar_jadi',$uom_lebar_jadi);
		$this->db->update('ready_goods_history_changed',$data);
	}

	function insert_table_changed($data) 
	{
        $this->db->insert('ready_goods_history_changed', $data);
	}


	function get_data_table_changed_all($where)
	{
		foreach($where as $key => $value){
			if (is_array($value)) {
                $this->db->where($key, [$value]);
            } else {
                $this->db->where($key, $value);
            }
		}
		$this->db->select('*');
		$this->db->from('ready_goods_history_changed');
		$query = $this->db->get();
		return $query->result();
	}

	function update_table_changed2($id,$data)
	{	
		$this->db->where('id',$id);
		$this->db->update('ready_goods_history_changed',$data);
	}


	
	var $column_order14 = array(null,'cat_id','corak','warna','lebar_Jadi');
	var $column_search14= array('cat_id','corak','warna','lebar_Jadi');
	var $order14  	  = array('cat_id' => 'asc','corak' => 'asc','warna' => 'asc');
	var $table14      = "ready_goods_history_changed";


	private function get_query_14()
    {	
		// remove
		$this->db->select('id,cat_id,corak, warna, lebar_Jadi, uom_lebar_jadi, action');
		$this->db->from($this->table14);
		$this->db->where('action','REMOVE');
		$query1 = $this->db->get_compiled_select();

		$this->db->select('id, cat_id_last as cat_id, corak_last as corak, warna_last as warna, lebar_Jadi_last as lebar_Jadi, uom_lebar_jadi_last as uom_lebar_jadi, action');
		$this->db->from($this->table14);
		$this->db->where('action','ADD');
		$query2 = $this->db->get_compiled_select();
		
		$this->db->SELECT('*');
		$this->db->FROM('('.$query1 . ' UNION ' . $query2 .' ) as unionTable');
		
        return;
    }

    private function _get_datatables_query14()
	{
		
        $this->get_query_14();

        $i = 0;
		foreach ($this->column_search14 as $item) // loop column 
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

				if(count($this->column_search14) - 1 == $i) //last loop
					$this->db->group_end(); //close bracket
			}
			$i++;
		}
		
		if(isset($_POST['order'])) // here order processing
		{
			$this->db->order_by($this->column_order14[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
		} 
		else if(isset($this->order14))
		{
			$order = $this->order14;
			foreach($order as $key => $value){
				$this->db->order_by($key, $value);
			}
		}
	}

	function get_datatables14()
	{
		$this->_get_datatables_query14();
		if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
		$query = $this->db->get();
		return $query->result();
	}

	function count_filtered14()
	{
		$this->_get_datatables_query14();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all14()
	{
		$this->get_query_14();
		return $this->db->count_all_results();
	} 

	public function get_data_changed_all($id)
	{
		$this->get_query_14();
		$this->db->where('id',$id);
		$query = $this->db->get();
		return $query->row();
	}

	public function goods_to_push()
	{
		$this->db->where('date(report_date) = "2024-11-04"');
		$this->db->where('lokasi', 'GRG/Stock');
		$this->db->like('corak','J-','after');
		$this->db->where('category','90d');
		// $this->db->where('customer_name <> ""');
		$this->db->select('corak');
		$this->db->from('goods_to_push');
		$this->db->order_by('corak','asc');
		$query = $this->db->get();
		return $query->result();
	}
}
