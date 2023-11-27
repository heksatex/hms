<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_bulk extends CI_Model {

    protected $table = "bulk";
    protected $column_order = array(null, 'no_bulk', 'tanggal_buat', 'user');
    protected $order = ['tanggal_buat' => 'desc'];
    protected $fillable = "";

    public function __construct() {
        parent::__construct();
        $this->column_order = array(null, $this->table . '.no_bulk', $this->table . '.tanggal_buat', $this->table . '.user');
        $this->order = [$this->table . '.tanggal_buat' => 'desc'];
        $this->fillable = "$this->table.no_bulk, $this->table.tanggal_buat, $this->table.user";
    }

    protected function _getDataQuery() {
        $this->db->from($this->table);
        foreach ($this->column_search as $key => $value) {
            if ($_POST['search']['value']) {
                if ($key === 0) {
                    $this->db->group_start();
                    $this->db->like($value, $_POST['search']['value']);
                } else {
                    $this->db->or_like($value, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 === $key)
                    $this->db->group_end();
            }
        }
        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function getData(array $condition = []) {
        $this->_getDataQuery();
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function getCountDataFiltered(array $condition = []) {
        $this->_getDataQuery();
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getCountAllData(array $condition = []) {
        $this->db->from($this->table);
        if (count($condition) > 0) {
            $this->db->where($condition);
        }
        return $this->db->count_all_results();
    }

    public function save(array $data) {
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
}
