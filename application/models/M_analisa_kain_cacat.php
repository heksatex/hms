<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of M_analisa_kain_cacat
 *
 * @author RONI
 */
class M_analisa_kain_cacat extends CI_Model {

    //put your code here
    public function __construct() {
        parent::__construct();
    }

    protected $where = [];
    protected $whereIn = [];
    protected $select = "*";
    protected $groupBy = [];

    protected function _data() {
        $this->db->from("mrp_production mrpp");
        $this->db->join("mrp_production_fg_hasil mrppfghs", "(mrppfghs.kode = mrpp.kode and mrppfghs.lokasi LIKE '%Stock')");
        $this->db->join("mst_produk mp", "mp.kode_produk = mrpp.kode_produk");
        $this->db->join("mst_produk_parent mpsp", "mpsp.id = mp.id_parent");
    }

    public function setWheres(array $where) {
        $this->where = array_merge($this->where, $where);
        return $this;
    }

    public function setGroup(string $group) {
        $this->groupBy = array_merge($this->groupBy, [$group]);
        return $this;
    }

    public function setSelect(string $select) {
        $this->select = $select;
        return $this;
    }

    public function setWhereIn(array $whereIn) {
        $this->whereIn = $whereIn;
        return $this;
    }

    public function getData() {
        $this->_data();
        $this->db->where($this->where);
        if (count($this->groupBy) > 0) {
            $this->db->group_by(implode(",", $this->groupBy));
        }
        foreach ($this->whereIn as $key => $value) {
            $this->db->where_in($key, $value);
        }

        $query = $this->db->select($this->select)->get();
        return $query->result();
    }

    public function detailTableAwal(array $whereIn, string $as = "table_1", $join = false, $select = "sum(qty) as total_qty", $table1 = "mrp_production_fg_hasil", $tableJoin = "mrp_production_cacat") {
        $this->db->from("{$table1} mpfh");
        $vll = "";
        if ($join) {
            $this->db->join("(SELECT mrrp.all_cacat,mrp.* from {$tableJoin} mrp "
            . " join("
                    . " SELECT kode,GROUP_CONCAT(mrp2.kode_cacat) as all_cacat from {$tableJoin} mrp2 where mrp2.kode in -val- group by mrp2.kode "
                    . ") as mrrp on mrrp.kode = mrp.kode ) mpc","mpc.quant_id = mpfh.quant_id");
//            $this->db->join("{$tableJoin} mpc", "mpc.quant_id = mpfh.quant_id");
        }

        foreach ($whereIn as $key => $value) {

            if (is_array($value)) {
                switch ($value["type"]) {
                    case "in":
                        $this->db->where_in($key, $value['data']);
                        $vll = "('".implode("','",$value['data'])."')";
                        break;
                    case "raw":
                        $this->db->where($value['data']);
                        break;
                    default:
                        $this->db->where_not_in($key, $value['data']);
                        break;
                }
            } else {
                $this->db->where($key, $value);
            }
        }
        $this->db->select($select);
        $awal = "(" . $this->db->get_compiled_select() . ") {$as}";
//        $awal = "( select * from (" . $this->db->get_compiled_select();
//        $awal .= ' UNION SELECT "0" ) alias limit 1)';
        $awal = strtr($awal, ["-val-"=>$vll]);
        return $awal;
    }

    public function getQuery(string $query, $return = true) {
        $querys = $this->db->query($query);
        if ($return)
            return $querys->result_array();
    }

    // table2

    public function getDataCacat() {
        $this->db->from("mst_cacat mc");
        $this->db->select("nama_cacat,group_concat(kode_cacat) as kc");
        $this->db->group_by("nama_cacat");
        $this->db->order_by("nama_cacat asc");
        $this->db->where("dept_id", "GJD");
        return $this->db->get()->result();
    }

    public function getQueryTable2(array $whereIn, string $as = "table_1", $select = "sum(qty) as total_qty", $joinStock = false,
            $table1 = "mrp_production_fg_hasil", $table2 = "mrp_production_cacat", $table3 = "mst_produk", $table4 = "stock_quant") {
        $this->db->from("{$table1} mpfh");
        $this->db->join("{$table2} mpc", "(mpc.quant_id = mpfh.quant_id and mpc.dept_id = 'GJD')");
        $this->db->join("{$table3} mp", "mp.kode_produk = mpfh.kode_produk");
        if ($joinStock) {
            $this->db->join("{$table4} sq", "sq.quant_id = mpfh.quant_id");
        }
        foreach ($whereIn as $key => $value) {
            if (is_array($value)) {
                switch ($value["type"]) {
                    case "in":
                        $this->db->where_in($key, $value['data']);
                        break;
                    case "raw":
                        $this->db->where($value['data']);
                        break;
                    default:
                        $this->db->where_not_in($key, $value['data']);
                        break;
                }
            } else {
                $this->db->where($key, $value);
            }
        }
        $this->db->select($select);
        $awal = "(" . $this->db->get_compiled_select() . ") {$as}";
        return $awal;
    }
}
