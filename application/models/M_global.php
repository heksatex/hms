<?php

defined('BASEPATH') OR EXIT('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of M_global
 *
 * @author RONI
 */
class M_global extends CI_Model {

    //put your code here
    protected $orders = [];
    protected $search = [];
    protected $order = [];
    protected $group = [];
    protected $table = "";
    protected $wheres = [];
    protected $selects = [];
    protected $joins = ["table" => [],
        "kondisi" => [],
        "posisi" => []
    ];
    protected $whereIn = [];
    protected $wheresRaw = [];

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

    public function setWheres(array $wheres) {
        $this->wheres = array_merge($this->wheres, $wheres);
        return $this;
    }

    public function setWhereRaw(string $where) {
        $this->wheresRaw[] = $where;
        return $this;
    }

    public function setWhereIn(string $sa, array $in) {
        $this->whereIn[$sa] = $in;
        return $this;
    }

    public function setSelects(array $selects) {
        $this->selects = array_merge($this->selects, $selects);
        return $this;
    }
    public function setGroups(array $groups) {
        $this->group = array_merge($this->group, $groups);
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

    protected function _get_datatables_query() {
        $this->db->from($this->table);
        $this->db->select(implode(",", $this->selects));
        foreach ($this->joins["table"] as $key => $value) {
            $this->db->join($value, $this->joins["kondisi"][$key], $this->joins["posisi"][$key]);
        }
        if (count($this->wheres) > 0) {
            $this->db->where($this->wheres);
        }
        if (count($this->wheresRaw) > 0) {
            foreach ($this->wheresRaw as $key => $value) {
                $this->db->where($value, null, false);
            }
        }

        if (count($this->whereIn) > 0) {
            foreach ($this->whereIn as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        
        if(count($this->group) > 0) {
            $this->db->group_by($this->group);
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
//            $order = $this->order;
//            $this->db->order_by(key($order), $order[key($order)]);
            foreach ($this->order as $key => $value) {
                if(gettype($key) === "integer"){
                    $this->db->order_by($value,"asc");
                } else {
                    $this->db->order_by($key,$value);
                }
            }
        }
    }

    public function getData() {
        $this->_get_datatables_query();
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);

        $query = $this->db->get();
        return $query->result();
    }

    public function getDataCountFiltered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getDataCountAll() {
        $this->db->from($this->table);
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
        if (count($this->whereIn) > 0) {
            foreach ($this->whereIn as $key => $value) {
                $this->db->where_in($key, $value);
            }
        }
        if(count($this->group) > 0) {
            $this->db->group_by($this->group);
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
        if (count($this->wheresRaw) > 0) {
            foreach ($this->wheresRaw as $key => $value) {
                $this->db->where($value, null, false);
            }
        }
        if(count($this->group) > 0) {
            $this->db->group_by($this->group);
        }

        $result = $this->db->select(implode(",", $this->selects))->get();
        return $result->row();
    }

    public function getSuppliers(array $where) {
        $this->db->from("partner");
        $this->db->select("id, nama as text");
        $this->db->limit(25);
        $this->db->where($where);

        $query = $this->db->get();
        return $query->result();
    }

    public function save(array $data) {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id() ?? null;
    }

    public function saveBatch(array $data) {
        $this->db->insert_batch($this->table, $data);
    }

    public function updateBatch(array $data, string $index) {
        $this->db->update_batch($this->table, $data, $index);
    }

    public function update(array $data) {
        try {
            if (count($this->wheres) > 0) {
                $this->db->where($this->wheres);
            }
            if (count($this->wheresRaw) > 0) {
                foreach ($this->wheresRaw as $key => $value) {
                    $this->db->where($value, null, false);
                }
            }
            if (count($this->whereIn) > 0) {
                foreach ($this->whereIn as $key => $value) {
                    $this->db->where_in($key, $value);
                }
            }
            $this->db->set($data);
            $this->db->update($this->table);
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function query(array $query) {
        try {
            foreach ($query as $key => $value) {
                $this->db->query($value);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
}
