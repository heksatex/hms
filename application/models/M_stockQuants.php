<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_stockQuants extends CI_Model {

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

    function get_datatables() {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
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

    public function get_list_stock_quant_grouping($groupBy, $where) {
        return $this->db->query("SELECT $groupBy as nama_field, concat($groupBy,' (',count(*),')') as grouping, sum(qty) as 'tqty'  from stock_quant $where group by $groupBy ")->result();
    }

    public function get_list_stock_quant_by($where, $rowno, $recordPerPage, $kolom_order, $order) {
        return $this->db->query("SELECT * from stock_quant $where $kolom_order $order LIMIT $rowno, $recordPerPage")->result();
    }

    public function get_list_stock_quant_noLimit($where, $kolom_order, $order) {
        return $this->db->query("SELECT * from stock_quant $where $kolom_order $order")->result();
    }

    public function get_list_stock_quant_limit($where) {
        return $this->db->query("SELECT * from stock_quant $where")->result();
    }

    public function getRecord($rowno, $rowperpage, $filterAdvanced) {

        $query = $this->db->query("SELECT * FROM stock_quant $filterAdvanced ORDER BY create_date desc LIMIT $rowno, $rowperpage  ");
        return $query->result_array();
    }

    public function getRecordCount($where) {

        $query = $this->db->query("SELECT count(*) as allcount FROM stock_quant $where ");
        $result = $query->result_array();
        return $result[0]['allcount'];
    }

    public function get_record_count_grouping_by($filterAdvanced) {

        $query = $this->db->query("SELECT * FROM stock_quant $filterAdvanced GROUP BY kode_produk ");
        $result = $query->result_array();
        $jml = 0;
        foreach ($result as $val) {
            $jml = $jml + 1;
        }

        return $jml;
    }

    public function get_stock_quant_by_kode($quant_id) {
        $query = $this->db->query("SELECT quant_id, create_date, move_date, kode_produk, nama_produk, lot, nama_grade, qty, uom, qty2, uom2, lokasi, lokasi_fisik, lebar_greige, uom_lebar_greige, lebar_jadi,uom_lebar_jadi, reff_note, reserve_move, reserve_origin, (datediff(now(), move_date) ) as umur, qty_opname, uom_opname, corak_remark, warna_remark, qty_jual, uom_jual, qty2_jual, uom2_jual FROM stock_quant WHERE quant_id = '$quant_id'");
        return $query->row();
    }

    public function update_stockquants($quant_id, $nama_grade, $reff_note, $lebar_greige, $uom_lebar_greige, $lebar_jadi, $uom_lebar_jadi, $corak_remark,$warna_remark, $qty_jual, $uom_jual, $qty2_jual, $uom2_jual) {
        $this->db->query("UPDATE stock_quant SET  nama_grade = '$nama_grade', reff_note = '$reff_note', lebar_greige = '$lebar_greige', uom_lebar_greige = '$uom_lebar_greige', lebar_jadi = '$lebar_jadi', uom_lebar_jadi = '$uom_lebar_jadi', corak_remark = '$corak_remark', warna_remark = '$warna_remark', qty_jual = '$qty_jual', uom_jual = '$uom_jual', qty2_jual = '$qty2_jual', uom2_jual = '$uom2_jual' WHERE quant_id = '$quant_id' ");

        $this->db->query("UPDATE mrp_production_fg_hasil SET  nama_grade = '$nama_grade',lebar_greige = '$lebar_greige', uom_lebar_greige = '$uom_lebar_greige', lebar_jadi = '$lebar_jadi', uom_lebar_jadi = '$uom_lebar_jadi'  WHERE quant_id = '$quant_id' ");

        $this->db->query("UPDATE stock_move_items SET lebar_greige = '$lebar_greige', uom_lebar_greige = '$uom_lebar_greige', lebar_jadi = '$lebar_jadi', uom_lebar_jadi = '$uom_lebar_jadi'  WHERE quant_id = '$quant_id' ");
    }

    public function get_kategori_produk_by_produk($kode_produk){
        $this->db->where('mp.kode_produk',$kode_produk);
        $this->db->SELECT("mp.id_category, cat.nama_category");
        $this->db->FROM("mst_produk mp");
        $this->db->JOIN("mst_category cat","mp.id_category = cat.id");
        $result = $this->db->get();
        return $result->row();
    }
    
}
