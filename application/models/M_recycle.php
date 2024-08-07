<?php

defined("BASEPATH") or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of M_recycle
 *
 * @author RONI
 */
class M_recycle extends CI_Model {

    //put your code here

    protected $where = [];
    protected $whereIn = [];
    protected $select = "*";
    protected $lot = [];

//    protected $group = "";

    protected function _query() {
        $smiLot = (count($this->lot) > 0) ? "and smi.lot in('" . implode("','", $this->lot) . "') " : " ";
        $this->db->from("mrp_production mp");
        $this->db->join("mrp_production_fg_hasil mph", "mp.kode = mph.kode");
//        $this->db->join("("
//                . "select smi.lot,cod.nama_warna,prod.*,pb.*,cod.nama_route from pengiriman_barang pb "
//                . "join pengiriman_barang_items pbi on  (pb.kode = pbi.kode and pbi.status_barang = 'done') "
//                . "join stock_move sm on (sm.move_id = pb.move_id and sm.status = 'done') "
//                . "join stock_move_items smi on smi.move_id = sm.move_id "
//                . "join ("
//                . "select mp.kode_produk,mp.nama_produk,mpp.nama as produk_parent,mjk.nama_jenis_kain from mst_produk mp "
//                . "join mst_produk_parent mpp on mp.id_parent = mpp.id "
//                . "join mst_jenis_kain mjk on mjk.id = mp.id_jenis_kain"
//                . ") prod on prod.kode_produk = pbi.kode_produk "
//                . "left join ("
//                . "select cod.*,warna.nama_warna,ro.nama as nama_route from color_order_detail cod "
//                . "join warna on warna.id = cod.id_warna "
//                . "join route_co ro on cod.route_co = ro.kode "
//                . ") cod "
//                . "on (cod.kode_co = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(pb.origin,'|',3) ,'|',-2),'|',1) and "
//                . "cod.row_order = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(pb.origin,'|',3) ,'|',-2),'|',-1))"
//                . "where pb.dept_id = 'GRG' and pb.status = 'done' " . $smiLot
//                . ") p", "on p.lot = mph.lot");

        $this->db->group_by("mph.lot");
    }

    public function getGo(array $where, string $select) {
        $this->db->select($select);
        $this->db->from("pengiriman_barang pb");
        $this->db->join("pengiriman_barang_items pbi", "(pb.kode = pbi.kode and pbi.status_barang = 'done')");
        $this->db->join("stock_move sm", "(sm.move_id = pb.move_id and sm.status = 'done')");
        $this->db->join("stock_move_items smi", "smi.move_id = sm.move_id");
        $this->db->join("(select mp.kode_produk,mp.nama_produk,mpp.nama as produk_parent,mjk.nama_jenis_kain from mst_produk mp "
                . "join mst_produk_parent mpp on mp.id_parent = mpp.id "
                . "join mst_jenis_kain mjk on mjk.id = mp.id_jenis_kain) prod", "prod.kode_produk = pbi.kode_produk");
        $this->db->join("("
                . "select cod.*,warna.nama_warna,ro.nama as nama_route from color_order_detail cod "
                . "join warna on warna.id = cod.id_warna "
                . "join route_co ro on cod.route_co = ro.kode "
                . ") cod", "(cod.kode_co = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(pb.origin,'|',3) ,'|',-2),'|',1)"
                . "and cod.row_order = SUBSTRING_INDEX(SUBSTRING_INDEX(SUBSTRING_INDEX(pb.origin,'|',3) ,'|',-2),'|',-1))", "left");
        $this->db->where($where);
        $awal = "( select * from (" . $this->db->get_compiled_select();
        $awal .= ' UNION SELECT "N/A" ) alias limit 1)';
        return $awal;
    }

    public function result($lot = []) {
        $this->lot = $lot;
        $this->_query();
        $this->db->select($this->select);
        if (count($this->where) > 0) {
            $this->db->where($this->where);
        }

        foreach ($this->whereIn as $key => $value) {
            $this->db->where_in($key, $value);
        }

        if (isset($_POST['length'])) {
            if ($_POST['length'] != -1)
                $this->db->limit($_POST['length'], $_POST['start']);
        }

        $query = $this->db->get();
        return $query->result();
    }

    public function resultCount() {
        $this->_query();
        $this->db->select($this->select);
        if (count($this->where) > 0) {
            $this->db->where($this->where);
        }

        foreach ($this->whereIn as $key => $value) {
            $this->db->where_in($key, $value);
        }

        $query = $this->db->get();
        return $query->num_rows();
    }

    public function setWhere($where = []) {
        $this->where = array_merge($this->where, $where);
        return $this;
    }

    public function setWhereIn($whereIn = []) {
        $this->whereIn = array_merge($this->whereIn, $whereIn);
        return $this;
    }

    public function setSelect($select = "*") {
        $this->select = $select;
        return $this;
    }

    public function getListMo($prefix, $select = "*") {
        $this->db->from("mrp_production");
        $this->db->select($select);
        $this->db->where(["kode LIKE" => "%" . $prefix . "%", 'status' => 'done']);
        $this->db->limit(50);
        $query = $this->db->get();
        return $query->result();
    }

    public function getListKP(array $where, $select = "*") {
        $this->db->from("mrp_production_fg_hasil mph");
        $this->db->join("mrp_production mp", "mp.kode = mph.kode");
        $this->db->select($select);
        $this->db->where($where);
        $this->db->group_by("mph.lot");
        $this->db->order_by("create_date desc");
        $this->db->limit(50);
        $query = $this->db->get();
        return $query->result();
    }

    public function detail(array $where, $raw = false) {
        $this->db->from("acc_stock_move_items");
        foreach ($where as $key => $value) {
            if (is_array($value)) {
                if ($value["type"] === "in") {
                    $this->db->where_in($key, $value['data']);
                } else {
                    $this->db->where_not_in($key, $value['data']);
                }
            } else {
                $this->db->where($key, $value);
            }
        }

        $this->db->select("concat(GROUP_CONCAT(kode_transaksi SEPARATOR ' '),'#',kode_produk,'#',replace(nama_produk,'\"',';'),'#',tanggal_transaksi,'#',count(lot),'#',sum(qty),'#',uom,'#',sum(qty2),'#',uom2) as dt ");
        if (!$raw) {
            $query = $this->db->get();
            return $query->row();
        }
        $awal = "( select * from (" . $this->db->get_compiled_select();
        $awal .= ' UNION SELECT "N/A" ) alias limit 1)';
        return $awal;
    }

    public function getDetail(string $query) {
        $querys = $this->db->query($query);
        return $querys->result_array();
    }

    protected function _getDataKp() {
        $this->db->from("mrp_production_fg_hasil mph");
        $this->db->join("mrp_production mp", "mp.kode = mph.kode");
        $columnSearch = [];
        $orders = [null, "mph.lot"];
        foreach ($columnSearch as $key => $value) {
            if ($_POST['search']['value']) {
                if ($key === 0) {
                    $this->db->group_start();
                    $this->db->like($value, $_POST['search']['value']);
                } else {
                    $this->db->or_like($value, $_POST['search']['value']);
                }

                if (count($this->column_search) === ($key + 1)) {
                    $this->db->group_end();
                }
            }
        }
        if (isset($_POST['order'])) {
            $this->db->order_by($orders[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($orders)) {
            $order = $orders;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function getDataKp(array $where) {
        $this->_getDataKp();
        $this->db->where($where);
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCountDataKpFiltered(array $condition) {
        $this->_getDataKp();
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getCountAllDataKp(array $condition) {
        $this->db->from("mrp_production_fg_hasil mph");
        $this->db->join("mrp_production mp", "mp.kode = mph.kode");
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        return $this->db->count_all_results();
    }
}
