<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_outlet extends CI_Model
{   

    function get_data_inlet()
    {
        $status = array('draft','process');
        $this->db->WHERE_IN('mrpin.status',$status);
        // $this->db->WHERE("sq.lokasi","GJD/Stock");
        // $this->db->WHERE("sq.lokasi_fisik","INLET");
        $this->db->SELECT('mrpin.id, mrpin.quant_id, mrpin.lot, mrpin.id, ms.nama_mesin, ms.mc_id');
        $this->db->FROM('mrp_inlet mrpin');
        // $this->db->JOIN('stock_quant sq ','mrpin.quant_id = sq.quant_id', 'INNER');
        $this->db->JOIN("mesin ms ", 'mrpin.mc_id = ms.mc_id', "left");             
    }


    function get_list_lot_inlet()
    {
        $this->get_data_inlet();
        return $result = $this->db->get();
    }

    function get_list_lot_inlet_by_lot($lot)
    {
        $this->get_data_inlet();
        $this->db->order_by("mrpin.tanggal", "asc");
        $this->db->like('mrpin.lot',$lot);
        $this->db->limit('50');
        return $result = $this->db->get();
    }


    var $column_order = array(null, 'sq.kode_produk', 'sq.nama_produk','sq.lot', 'sq.qty','sq.qty2','sq.qty_opname');
	var $column_search= array('sq.kode_produk', 'sq.nama_produk','sq.lot', 'sq.qty','sq.qty2','sq.qty_opname');
	var $order  	  = array('sq.move_date' => 'asc');

    private function get_list_lot_belum_inlet_query()
    {
        $status_inlet = $this->get_list_mrp_inlet_by_status();
        if(!empty($status_inlet)){
            $this->db->WHERE_NOT_IN("sq.quant_id",$status_inlet);
        }
        $this->db->WHERE("sq.lokasi","GJD/Stock");
        $this->db->WHERE("mp.id_category != 21");// 21 = Kain Hasil Gudang Jadi
        $this->db->WHERE("sq.lokasi_fisik","");
        $this->db->SELECT("sq.move_date, sq.quant_id, sq.kode_produk, sq.lot, sq.nama_produk, sq.qty, sq.uom, sq.qty2, sq.uom2, sq.qty_opname, sq.uom_opname");
        $this->db->FROM("stock_quant sq");
        $this->db->JOIN("mst_produk mp", "mp.kode_produk = sq.kode_produk","INNER");
        // $this->db->JOIN("(SELECT mrp.kode, rmt.move_id
        //                     FROM mrp_production mrp
        //                     INNER JOIN mrp_production_rm_target rmt ON mrp.kode = rmt.kode
        //                     WHERE mrp.dept_id = 'GJD') as mrps", "mrps.move_id = sq.reserve_move", "INNER");
    }
	
    private function get_list_lot_belum_inlet_query_filter()
    {

        $this->get_list_lot_belum_inlet_query();

        $i = 0;
	
        foreach ($this->column_search as $item){ // loop column 
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
    }

    function get_list_lot_belum_inlet()
    {   
        $this->get_list_lot_belum_inlet_query();
        return $this->db->get();
    }

    function get_list_lot_belum_inlet_2()
    {   
        $this->get_list_lot_belum_inlet_query_filter();
        if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
        $query =  $this->db->get();
		return $query->result();

    }

    function count_all()
    {
        $this->get_list_lot_belum_inlet_query_filter();
		return $this->db->count_all_results();
    }

    function count_filtered()
    {
        $this->get_list_lot_belum_inlet_query_filter();
        $query = $this->db->get();
		return $query->num_rows();
    }

    
    var $column_order2 = array(null, 'fg.create_date', 'fg.kode_produk', 'fg.nama_produk', 'sq.corak_remark','sq.warna_remark', 'fg.lot', 'sq.nama_grade', 'fg.qty','fg.qty2','sq.qty_jual','sq.qty2_jual','sq.lebar_jadi','sq.lokasi','sq.lokasi_fisik','fg.nama_user');
	var $column_search2= array('fg.create_date', 'fg.kode_produk', 'fg.nama_produk', 'sq.corak_remark','sq.warna_remark', 'fg.lot', 'sq.nama_grade', 'fg.qty','fg.qty2','sq.qty_jual','sq.qty2_jual','sq.lebar_jadi','sq.lokasi','sq.lokasi_fisik','fg.nama_user');
	var $order2  	  = array('fg.create_date' => 'asc');

    private function get_list_hph()
    {

        $this->db->SELECT("fg.create_date, fg.kode_produk, fg.nama_produk, fg.lot, fg.nama_grade, fg.qty, fg.uom, fg.qty2, fg.uom2, fg.lokasi, fg.lebar_jadi, fg.uom_lebar_jadi,  fg.nama_user, sq.qty_jual, sq.uom_jual, sq.qty2_jual, sq.uom2_jual, sq.lokasi_fisik, sq.corak_remark, sq.warna_remark, sq.lokasi");
        $this->db->FROM("mrp_inlet inp");
        $this->db->JOIN("mrp_production_fg_hasil fg", "inp.kode_mrp = fg.kode AND inp.id = fg.id_inlet","INNER");
        $this->db->JOIN("stock_quant sq", "fg.quant_id = sq.quant_id", "INNER");

        $i = 0;
	
        foreach ($this->column_search2 as $item){ // loop column 
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
    }

    function get_list_detail_hph($id)
    {   
        $this->get_list_hph();
        $this->db->WHERE("inp.id",$id);
        if($_POST['length'] != -1)
		$this->db->limit($_POST['length'], $_POST['start']);
        $query =  $this->db->get();
		return $query->result();
    }

    function count_all_hph($id)
    {
        $this->db->WHERE("inp.id",$id);
        $this->get_list_hph();
		return $this->db->count_all_results();
    }

    function count_filtered_hph($id)
    {
        $this->db->WHERE("inp.id",$id);
        $this->get_list_hph();
        $query = $this->db->get();
		return $query->num_rows();
    }
    

    function get_list_mrp_inlet_by_status()
    {
        $status = array('draft','process', 'done');
        $this->db->WHERE_IN("status",$status);
        $this->db->SELECT('quant_id');
        $this->db->FROM("mrp_inlet");
        $result = $this->db->get();

        $rs     = [];
        foreach($result->result() as $results){
                $rs = array($results->quant_id);
        }

        return $rs;
    }

   


    function get_mrp_production_rm_target_by_lot($kode,$quant_id,$lot)
    {
        $this->db->WHERE("rmt.kode",$kode);
        $this->db->WHERE("smi.quant_id",$quant_id);
        $this->db->WHERE("smi.lot",$lot);
        $this->db->SELECT("rmt.kode, rmt.move_id,smi.quant_id, smi.kode_produk, smi.nama_produk, smi.lot,  smi.qty, smi.uom, smi.qty2, smi.uom2, smi.origin_prod");
        $this->db->FROM("mrp_production_rm_target rmt");
        $this->db->JOIN("stock_move_items smi", "smi.move_id = rmt.move_id And smi.origin_prod = rmt.origin_prod AND smi.status = 'ready'", "INNER");
        return $this->db->get();

    }


    function get_mrp_production_fg_hasil_by_kode($kode,$id_inlet)
    {
        $this->db->WHERE("kode",$kode);
        // $this->db->WHERE("lokasi",$lokasi);
        $this->db->WHERE("id_inlet",$id_inlet);
        $this->db->SELECT("kode, sum(qty) as total_qty, sum(qty2) as total_qty2, count(lot) as jml_lot");
        $this->db->FROM("mrp_production_fg_hasil ");
        $this->db->group_by('id_inlet');
        return $this->db->get();
    }


    function get_list_uom_jual()
    {
        $this->db->where("jual",'yes');
        $query = $this->db->get('uom');
        return $query->result();
        
    }

    public function get_list_uom_konversi()
    {
        $query = $this->db->get('uom_konversi');
        return $query->result();
    }

    function get_mrp_production_by_kode($kode)
    {
        $this->db->where("kode",$kode);
        $this->db->SELECT("kode,origin,kode_produk,nama_produk,qty,uom,status, dept_id, lebar_greige, uom_lebar_greige");
        $this->db->FROM("mrp_production");
        $query = $this->db->get();
        return $query->row();
        
    }


    // function get_move_id_rm_target_by_produk($kode,$kode_produk)
	// {
	// 	// get move id rm yg category produk nya tidak 11(aux) dan 12 (DYE)
    //     $this->db->WHERE_NOT_IN("mp.id_category",array(11,12));
    //     $this->db->WHERE("rm.move_id != ''");
    //     $this->db->WHERE("rm.kode",$kode);
    //     $this->db->WHERE("rm.kode_produk",$kode_produk);
    //     $this->db->SELECT("DISTINCT(rm.move_id) as move_id ");
    //     $this->db->FROM("mrp_production_rm_target rm");
    //     $this->db->JOIN("mst_produk mp","rm.kode_produk = mp.kode_produk","INNER");
    //     $this->db->ORDER_BY("rm.row_order", "ASC");
    //     return $this->db->get();

	// }

    // function cek_reserve_lot_to_mrp($kode,$quant_id,$lot)
    // {
    //     $this->db->WHERE("rm.kode",$kode);
    //     $this->db->WHERE("smi.quant_id",$quant_id);
    //     $this->db->WHERE("smi.lot",$lot);
    //     $this->db->SELECT("rm.kode, rm.move_id, rm.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.origin_prod  ");
    //     $this->db->FROM("mrp_production_rm_target rm");
    //     $this->db->JOIN("stock_move_items smi", "rm.move_id = smi.move_id", "INNER");
    //     $this->db->JOIN("stock_quant sq ", "smi.quant_id = sq.quant_id","INNER");
    //     return $this->db->get();
    // }

    public function get_list_remark_by_grade()
    {   
        $this->db->order_by("id","asc");
        $query = $this->db->get('mst_remark_by_grade');
        return $query->result();

    }


    public function get_row_order_mrp_satuan_by_kode($kode) 
    {
        $last_no = $this->db->query("SELECT max(row_order) as nom FROM mrp_satuan where kode = '$kode'");
        $result = $last_no->row();
        if (empty($result->nom)) {
            $no = 1;
        } else {
            $no = (int) $result->nom + 1;
        }
        return $no;
    }

    function cek_stock_move_items_by_kode($kode,$quant_id,$lot)
    {
        $this->db->where("rm.kode",$kode);
        $this->db->where("smi.quant_id",$quant_id);
        $this->db->WHERE("smi.lot",$lot);
        $this->db->select("smi.move_id, smi.quant_id, smi.kode_produk, smi.nama_produk, smi.lot, smi.qty, smi.uom, smi.qty2, smi.uom2, smi.`status`, smi.origin_prod, smi.row_order, sq.reff_note, smi.origin_prod, sq.nama_grade, smi.lokasi_fisik, smi.lebar_greige, smi.uom_lebar_greige, smi.lebar_jadi, smi.uom_lebar_jadi, rm.qty as qty_rm_target");
        $this->db->FROM("mrp_production_rm_target rm");
        $this->db->join("stock_move_items smi","rm.move_id = smi.move_id", "INNER");
        $this->db->join("stock_quant sq ", "smi.quant_id = sq.quant_id","INNER");
        $this->db->order_by("smi.row_order","asc");
        return $this->db->get();
    }


    function save_mrp_production_fg_hasil_batch($data_fg_hasil)
    {   
        $this->db->insert_batch('mrp_production_fg_hasil', $data_fg_hasil);
    }


    function save_stock_quant_batch($data_stockquant)
    {   
        $this->db->insert_batch('stock_quant', $data_stockquant);
        return is_array($this->db->error());
    }


    function save_stock_move_items_batch($data_stockmoveitems)
    {   
        $this->db->insert_batch('stock_move_items', $data_stockmoveitems);
    }

    function save_mrp_satuan_batch($data_satuan)
    {   
        $this->db->insert_batch('mrp_satuan', $data_satuan);
    }

    function save_mrp_rm_hasil_batch($data_rm_hasil)
    {   
        $this->db->insert_batch('mrp_production_rm_hasil', $data_rm_hasil);
    }

    function update_stock_quant_by_kode($data_update_stock)
    {
        $this->db->update_batch("stock_quant",$data_update_stock,'quant_id');
        return $this->db->affected_rows();
    }

    function update_data_in_table_by_kode($table,$data_update_smi,$data_where)
    {
        $this->db->where($data_where);
        $this->db->update($table,$data_update_smi);
        return $this->db->affected_rows();
    }  

    function cek_lot_inlet_by_kode($id_inlet)
    {
        $this->db->where("mrpin.id",$id_inlet);
        $this->db->SELECT("mrp.kode");
        $this->db->FROM("mrp_inlet mrpin");
        $this->db->JOIN("mrp_production mrp", "mrp.kode = mrpin.kode_mrp ","INNER");
        $result = $this->db->get();
        return $result->num_rows();
       
    }


}