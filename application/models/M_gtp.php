<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of M_cbf
 *
 * @author RONI
 */
class M_gtp extends CI_Model {

    //put your code here
    protected $orders = [];
    protected $search = [];
    protected $order = [];
    protected $table = "goods_to_push";
    protected $wheres = [];
    protected $wheresRaw = [];
    protected $selects = [];
    protected $groups = [];
    protected $joins = ["table" => [],
        "kondisi" => [],
        "posisi" => []
    ];

    public function __construct() {
        
    }

    public function setOrders(array $orders) {
        $this->orders = $orders;
        return $this;
    }

    public function setSearch(array $search) {
        $this->search = $search;
        return $this;
    }

    public function setOrder(array $order) {
        $this->order = $order;
        return $this;
    }

    public function setWheres(array $wheres, $clearBefore = false) {
        if ($clearBefore) {
            $this->wheres = [];
        }
        $this->wheres = array_merge($this->wheres, $wheres);
        return $this;
    }

    public function setSelects(array $selects) {
        $this->selects = $selects;
        return $this;
    }

    public function setTables(string $table) {
        $this->table = $table;
        return $this;
    }

    public function setJoins(string $table, string $kondisi, $posisi = "inner") {
        $this->joins["table"][] = $table;
        $this->joins["kondisi"][] = $kondisi;
        $this->joins["posisi"][] = $posisi;
        return $this;
    }

    public function setGroups(array $group) {
        $this->groups = array_merge($this->groups, $group);
        return $this;
    }

    public function setWhereRaw(string $where) {
        $this->wheresRaw[] = $where;
        return $this;
    }

    protected function _get_datatables_query() {
        $this->db->from($this->table);
        $this->db->select(implode(",", $this->selects));
        foreach ($this->joins["table"] as $key => $value) {
            $this->db->join($value, $this->joins["kondisi"][$key], $this->joins["posisi"][$key]);
        }
        if (count($this->wheres) > 0)
            $this->db->where($this->wheres);

        if (count($this->wheresRaw) > 0) {
            foreach ($this->wheresRaw as $key => $value) {
                $this->db->where($value, null, false);
            }
        }

        foreach ($this->search as $key => $value) {
            if ($_POST['search']['value']) {
                if ($key === 0) {
                    $this->db->group_start();
                    $this->db->like($value, $_POST['search']['value']);
                } else {
                    $this->db->or_like($value, $_POST['search']['value']);
                }

                if (count($this->search) - 1 === $key)
                    $this->db->group_end();
            }
        }
        if (isset($_POST['order'])) {
            $this->db->order_by($this->orders[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function getData() {
        $this->_get_datatables_query();
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);


        if (count($this->groups) > 0) {
            $this->db->group_by($this->groups);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function getDataCountFiltered() {
        $this->_get_datatables_query();
        if (count($this->groups) > 0) {
            $this->db->group_by($this->groups);
        }
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getDataCountAll() {
        $this->db->from($this->table);
        if (count($this->wheres) > 0)
            $this->db->where($this->wheres);

        if (count($this->groups) > 0) {
            $this->db->group_by($this->groups);
        }
        if (count($this->wheresRaw) > 0) {
            foreach ($this->wheresRaw as $key => $value) {
                $this->db->where($value, null, false);
            }
        }
        return $this->db->count_all_results();
    }

    public function getDetail() {
        $this->db->from($this->table);
        if (count($this->wheres) > 0) {
            $this->db->where($this->wheres);
        }
        foreach ($this->joins["table"] as $key => $value) {
            $this->db->join($value, $this->joins["kondisi"][$key], $this->joins["posisi"][$key]);
        }
        $result = $this->db->select(implode(",", $this->selects))->get();
        return $result->row();
    }

    public function update(array $data) {
        $this->db->set($data);
        if (count($this->wheres) > 0) {
            $this->db->where($this->wheres);
        }
        if (count($this->wheresRaw) > 0) {
            foreach ($this->wheresRaw as $key => $value) {
                $this->db->where($value, null, false);
            }
        }
        $this->db->update($this->table);
    }
}
