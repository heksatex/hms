<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class m_Pickliststockquant extends CI_Model {

    var $table = 'stock_quant';
//    var $column_order = array(null, 'create_date', 'kode_produk', 'nama_produk', 'lot', 'qty', 'qty2', 'lokasi', 'reff_note', 'reserve_move');
    protected $column_order = array(null, 'stock_quant.create_date', 'stock_quant.kode_produk', 'stock_quant.nama_produk', 'lot', 'qty', 'qty2', 'lokasi', 'lokasi_fisik', 'reserve_move');
    var $column_search = array('stock_quant.create_date', 'stock_quant.kode_produk', 'stock_quant.nama_produk'
        , 'stock_quant.lot', 'stock_quant.qty', 'stock_quant.qty2', 'stock_quant.lokasi', 'stock_quant.reff_note', 'stock_quant.reserve_move', 'stock_quant.warna_remark', 'stock_quant.corak_remark');
    var $order = array('stock_quant.create_date' => 'desc');

    public function __construct() {
        parent::__construct();
        $this->load->database('default', TRUE);
    }

    private function _get_datatables_query() {

        $this->db->from($this->table);

        $i = 0;

        foreach ($this->column_search as $item) { // loop column 
            if ($_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all($condition = [], $in = []) {
        $this->db->from($this->table);
        $this->db->where_not_in('lokasi_fisik', ['PORT', 'XPD']);
        $this->db->where('lokasi', 'GJD/STOCK');
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        foreach ($in as $key => $value) {
            $this->db->where_in($key, $value);
        }
        return $this->db->count_all_results();
    }

    private function _getDataItemPicklist($condition, array $in = []) {
        $this->column_order = array(null, $this->table . '.lot', $this->table . '.kode_produk', $this->table . '.nama_produk', $this->table . '.corak_remark', $this->table . '.warna_remark', $this->table . '.qty', $this->table . '.qty2', 'lokasi_fisik');
        $this->_get_datatables_query();
//        $this->db->where("not exists ( select null from picklist_detail d where d.barcode_id = " . $this->table . ".lot )", "", false);
//        $this->db->join('mrp_satuan as ms', 'ms.quant_id = ' . $this->table . '.quant_id', 'left');
        $this->db->join('mst_produk as mp', 'mp.kode_produk = ' . $this->table . '.kode_produk');
        $this->db->where_not_in($this->table . '.lokasi_fisik', ['PORT', 'XPD']);
        $this->db->where($this->table . '.lokasi', 'GJD/STOCK');
        $this->db->where('id_category', 21);
        if (!is_null($condition) || count($condition) > 0) {
            $this->db->where($condition);
        }
        foreach ($in as $key => $value) {
            $this->db->where_in($key, $value);
        }
    }

    public function getDataItemPicklist(array $condition = [], array $in = [], $joinItemPicklist = false) {
        $select = "";
        $this->_getDataItemPicklist($condition, $in);

        $select .= $this->table . '.lot as barcode,' . $this->table . '.kode_produk,' . $this->table . '.nama_produk,' . $this->table . '.corak_remark,' . $this->table . '.lebar_jadi,'
                . $this->table . '.uom_lebar_jadi,' . $this->table . '.warna_remark,' . $this->table . '.quant_id,qty_jual,uom_jual,qty2_jual,uom2_jual,' . $this->table . '.lokasi_fisik,lokasi,reserve_origin,reserve_move';
//                . ' ms.qty,ms.uom';
        if ($joinItemPicklist) {
            $this->db->join("picklist_detail pd", "pd.barcode_id = " . $this->table . ".lot", 'left');
            $select .= ",pd.no_pl";
        }
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);


        return $this->db->select($select)->get()->result();
    }

    public function getDataItemPicklistScan(array $condition,$joinItemPicklist = false) {
        $select = "";
        $select .= $this->table . '.lot as barcode,' . $this->table . '.kode_produk,' . $this->table . '.nama_produk,' . $this->table . '.corak_remark,' . $this->table . '.lebar_jadi,'
                . $this->table . '.uom_lebar_jadi,' . $this->table . '.warna_remark,' . $this->table . '.quant_id,qty_jual,uom_jual,qty2_jual,uom2_jual,' . $this->table . '.lokasi_fisik,lokasi,reserve_origin,reserve_move';
        $this->db->from($this->table);
        $this->db->where($condition);
        if ($joinItemPicklist) {
            $this->db->join("picklist_detail pd", "pd.barcode_id = " . $this->table . ".lot", 'left');
            $select .= ",pd.no_pl";
        }
        $this->db->where_not_in($this->table . '.lokasi_fisik', ['PORT', 'XPD']);
        $this->db->where($this->table . '.lokasi', 'GJD/STOCK');
        return $this->db->select($select)->get()->row();
    }
    
    

    public function getDataItemPicklistScanDetail($condition, $joinItemPicklist = false) {
        $select = "id_category,nama_category,";
        $select .= $this->table . '.lot as barcode,' . $this->table . '.kode_produk,' . $this->table . '.nama_produk,' . $this->table . '.corak_remark,' . $this->table . '.lebar_jadi,'
                . $this->table . '.uom_lebar_jadi,' . $this->table . '.warna_remark,' . $this->table . '.quant_id,qty_jual,uom_jual,qty2_jual,uom2_jual,' . $this->table . '.lokasi_fisik,lokasi,reserve_origin,reserve_move';
        $this->db->from($this->table);
        $this->db->where($condition);
        if ($joinItemPicklist) {
            $this->db->join("picklist_detail pd", "pd.barcode_id = " . $this->table . ".lot", 'left');
            $select .= ",pd.no_pl";
        }
//        $this->db->join('mrp_satuan as ms', 'ms.quant_id = ' . $this->table . '.quant_id', 'left');
//        $this->db->where("not exists ( select null from picklist_detail d where d.barcode_id = " . $this->table . ".lot )", "", false);
        $this->db->join('mst_produk as mp', 'mp.kode_produk = ' . $this->table . '.kode_produk');
        $this->db->join('mst_category as mc', 'mc.id = mp.id_category', 'left');
//        $this->db->where_not_in($this->table . '.lokasi_fisik', ['PORT', 'XPD']);
//        $this->db->where($this->table . '.lokasi', 'GJD/STOCK');
//        $this->db->where('id_category', 21);
        return $this->db->select($select)->get()->row();
    }

    function count_filteredItemPicklist(array $condition = null, $in = []) {
        $this->_getDataItemPicklist($condition, $in);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function checkItemAvailable(array $condition) {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where($condition);
        $this->db->where_not_in($this->table . '.lokasi_fisik', ['PORT', 'XPD']);
        $this->db->where($this->table . '.lokasi', 'GJD/STOCK');
        $query = $this->db->get();
        return $query->row();
    }
}
