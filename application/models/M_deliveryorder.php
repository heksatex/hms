<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of M_Deliveryorder
 *
 * @author RONI
 */
class M_deliveryorder extends CI_Model {

    protected $table = "delivery_order";
    protected $tableSJ = "delivery_order_sj";
    protected $column_order = [null, 'delivery_order.no', 'no_sj', 'no_picklist'];
    protected $order = ['tanggal_buat' => 'desc'];
    protected $column_search = array('no_sj', 'delivery_order.no', 'no_picklist', 'pr.nama');

    public function __construct() {
        parent::__construct();
    }

    protected function getDataQuery() {
        $this->db->from($this->table);
        $this->db->join("picklist p", 'p.no = delivery_order.no_picklist');
        $this->db->join('partner pr', 'pr.id = p.customer_id', 'left');
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

    public function getData($conditon = []) {
        $this->getDataQuery();
        $this->db->select("delivery_order.*,pr.nama as buyer");
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        if (count($conditon) > 0)
            $this->db->where($conditon);

        $query = $this->db->get();
        return $query->result();
    }

    public function getDataCountFiltered($condition = []) {
        $this->getDataQuery();
        if (count($condition) > 0)
            $this->db->where($condition);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getDataCountAll(array $condition = []) {
        $this->db->from($this->table);
        if (count($condition) > 0)
            $this->db->where($condition);
        return $this->db->count_all_results();
    }

    public function checkNoSJ(array $condition) {
        $this->db->from($this->tableSJ);
        $this->db->where($condition);
        $this->db->limit(1);
        return $this->db->select("*")->get()->row();
    }

    public function deleteNoSJ($condition) {
        $this->db->delete($this->tableSJ, $condition);
    }

    public function insertNoSJ(array $value) {
        $this->db->insert($this->tableSJ, $value);
        return $this->db->insert_id() ?? null;
    }

    public function insert(array $value) {
        $this->db->insert($this->table, $value);
        return $this->db->insert_id() ?? null;
    }

    public function update(array $data, array $condition) {
        $this->db->set($data);
        $this->db->where($condition);
        $this->db->update($this->table);
    }

    public function getDataDetail(array $condition, $join = false, $select = "a.*") {
        $this->db->from($this->table . " a");
        if ($join) {
            $this->db->join("picklist p", "p.no = a.no_picklist");
            $this->db->join('partner as pn', 'pn.id = p.customer_id', 'left');
        }
        $this->db->where($condition);
        $result = $this->db->select($select)->get();
        return $result->row();
    }

    public function insertDoMove(array $value) {
        $this->db->insert('deliveryorder_stock_move', $value);
        return $this->db->insert_id() ?? null;
    }
}
