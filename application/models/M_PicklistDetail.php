<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of M_PicklistDetail
 *
 * @author RONI
 */
class M_PicklistDetail extends CI_Model {

    protected $db_debug;

    public function __construct() {
        $this->db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
    }

    //put your code here
    protected $table = "picklist_detail";
    var $column_order = array(null, 'barcode_id', 'quant_id', 'barcode_id', 'kode_produk', 'nama_produk', 'tanggal_masuk');
    var $order = ['tanggal_masuk' => 'desc'];
    var $column_search = array('barcode_id', 'quant_id', 'barcode_id', 'kode_produk', 'nama_produk');

    public function insertItem(array $data) {
        try {
            $this->db->insert($this->table, $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    protected function _getDataItem() {
        $this->db->select("a.*,ms.nama_status as valid");
        $this->db->join('mst_status as ms', 'ms.kode = a.valid', 'left');
        $this->db->from($this->table . ' a');
        foreach ($this->column_search as $key => $value) {
            if ($_POST["search"]["value"]) {
                $this->db->or_like($value, $_POST["search"]["value"]);
            }
        }
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function getData(array $condition = null) {
        $this->_getDataItem();
        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCountDataFiltered(array $condition = null) {
        $this->_getDataItem();
        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getCountAllData(array $condition = null) {
        $this->db->from($this->table);
        if (!is_null($condition)) {
            $this->db->where($condition);
        }
        return $this->db->count_all_results();
    }

    public function detailReport($condition) {
        $this->db->from($this->table);
        $this->db->where($condition);
        $this->db->select('no_pl,kode_produk,nama_produk,warna_remark,corak_remark,sales_order, count(qty) as jml_qty, sum(qty) as total_qty');
        $this->db->group_by(array("corak_remark", "warna_remark"));
        return $this->db->get()->result();
//        $this->db->join('(select no_pl,kode_produk,nama_produk,warna_remark,'
//                . 'corak_remark,sales_order, count(qty) as jml_qty, sum(qty) as total_qty) as b',
//                'b.no_pl=' . $this->table . '.no_pl', 'left');
    }

    public function detailReportQty($condition) {
        $this->db->from($this->table);
        $this->db->where($condition);
        $this->db->select('qty,uom');
        return $this->db->get()->result();
    }

    public function __destruct() {
        $this->db->db_debug = $this->db_debug;
    }
}
