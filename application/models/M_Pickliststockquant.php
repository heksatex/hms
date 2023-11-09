<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class m_Pickliststockquant extends CI_Model {

    var $table = 'stock_quant';
//    var $column_order = array(null, 'create_date', 'kode_produk', 'nama_produk', 'lot', 'qty', 'qty2', 'lokasi', 'reff_note', 'reserve_move');
    var $column_order = array(null, 'stock_quant.create_date', 'stock_quant.kode_produk', 'stock_quant.nama_produk', 'lot', 'qty', 'qty2', 'lokasi', 'reff_note', 'reserve_move');
    var $column_search = array('stock_quant.create_date', 'stock_quant.kode_produk', 'stock_quant.nama_produk'
        , 'lot', 'qty', 'qty2', 'lokasi', 'reff_note', 'reserve_move', 'stock_quant.warna_remark', 'stock_quant.corak_remark');
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

    public function count_all() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    private function _getDataItemPicklist($condition, array $in = []) {
        $this->column_order = array(null, $this->table . '.create_date', $this->table . '.kode_produk', $this->table . '.nama_produk', 'lot', 'qty', 'qty2', 'lokasi', 'reff_note', 'reserve_move');
        $this->_get_datatables_query();
        $this->db->where("not exists ( select null from picklist_detail d where d.barcode_id = " . $this->table . ".lot )", "", false);
//        $this->db->join('mrp_satuan as ms', 'ms.quant_id = ' . $this->table . '.quant_id', 'left');
        $this->db->join('mst_produk as mp', 'mp.kode_produk = ' . $this->table . '.kode_produk');
        $this->db->where_not_in('lokasi_fisik', ['PORT', 'XPD']);
        $this->db->where('lokasi', 'GJD/STOCK');
        $this->db->where('id_category', 21);
        if (!is_null($condition) || count($condition) > 0) {
            $this->db->where($condition);
        }
        foreach ($in as $key => $value) {
            $this->db->where_in($key, $value);
        }
    }

    public function getDataItemPicklist(array $condition = [], array $in = []) {

        $this->_getDataItemPicklist($condition, $in);
        $select = $this->table . '.lot as barcode,' . $this->table . '.kode_produk,' . $this->table . '.nama_produk,' . $this->table . '.corak_remark,' . $this->table . '.lebar_jadi,'
                . $this->table . '.uom_lebar_jadi,' . $this->table . '.warna_remark,' . $this->table . '.quant_id,qty_jual,uom_jual,qty2_jual,uom2_jual,lokasi_fisik,lokasi,reserve_origin,reserve_move';
//                . ' ms.qty,ms.uom';

        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);


        return $this->db->select($select)->get()->result();
    }

    public function getDataItemPicklistScan($condition) {
        $select = $this->table . '.lot as barcode,' . $this->table . '.kode_produk,' . $this->table . '.nama_produk,' . $this->table . '.corak_remark,' . $this->table . '.lebar_jadi,'
                . $this->table . '.uom_lebar_jadi,' . $this->table . '.warna_remark,' . $this->table . '.quant_id,qty_jual,uom_jual,qty2_jual,uom2_jual,lokasi_fisik,lokasi,reserve_origin,reserve_move';
        $this->db->from($this->table);
//        $this->db->join('mrp_satuan as ms', 'ms.quant_id = ' . $this->table . '.quant_id', 'left');
        $this->db->where("not exists ( select null from picklist_detail d where d.barcode_id = " . $this->table . ".lot )", "", false);
        $this->db->where($condition);
        $this->db->join('mst_produk as mp', 'mp.kode_produk = ' . $this->table . '.kode_produk');
        $this->db->where_not_in('lokasi_fisik', ['PORT', 'XPD']);
        $this->db->where('lokasi', 'GJD/STOCK');
        $this->db->where('id_category', 21);
        return $this->db->select($select)->get()->result();
    }

    function count_filteredItemPicklist(array $condition = null) {
        $this->_getDataItemPicklist($condition);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function checkItemAvailable($quantId) {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where(['quant_id' => $quantId]);
        $this->db->where_not_in('lokasi_fisik', ['PORT', 'XPD']);
        $this->db->where('lokasi', 'GJD/STOCK');
        $query = $this->db->get();
        return $query->row();
    }
}
