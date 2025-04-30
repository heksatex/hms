<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of M_konversiuom
 *
 * @author RONI
 */
class M_konversiuom extends CI_Model {

    //put your code here
    protected $table = "nilai_konversi";
    protected $columnOrder = [null, "dari", "ke", "nilai"];
    protected $columnSearch = ["dari", "ke", "nilai","catatan"];
    protected $order = ['id' => 'desc'];
    protected $select = "*";
    protected $where = [];

    public function __construct() {
        parent::__construct();
    }

    protected function getDataQuery() {
        $this->db->from($this->table);
        foreach ($this->columnSearch as $key => $value) {
            if (isset($_POST['search']) && $_POST['search']['value']) {
                if ($key === 0) {
                    $this->db->group_start();
                    $this->db->like($value, $_POST['search']['value']);
                } else {
                    $this->db->or_like($value, $_POST['search']['value']);
                }

                if (count($this->columnSearch) - 1 === $key)
                    $this->db->group_end();
            }
        }

        if (isset($_POST['order'])) {
            $this->db->order_by($this->columnOrder[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function selects(string $select) {
        $this->select = $select;
        return $this;
    }

    public function wheres(array $where) {
        $this->where = $where;
        return $this;
    }

    public function getData() {
        $this->getDataQuery();
        $this->db->select($this->select);
        if (isset($_POST['length']) && $_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        if (count($this->where) > 0)
            $this->db->where($this->where);

        $query = $this->db->get();
        return $query->result();
    }

    public function getDataCountFiltered() {
        $this->getDataQuery();
        if (count($this->where) > 0)
            $this->db->where($this->where);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function getDataCountAll() {
        $this->db->from($this->table);
        if (count($this->where) > 0)
            $this->db->where($this->where);
        return $this->db->count_all_results();
    }

    public function save(array $data) {
        try {
            $this->db->insert($this->table, $data);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                log_message('error', json_encode($data));
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function getDetail() {
        $this->db->from($this->table);
        if (count($this->where) > 0)
            $this->db->where($this->where);

        $result = $this->db->select($this->select)->get();
        return $result->row();
    }

    public function update(array $data) {
        try {
            $this->db->set($data);
            $this->db->where($this->where);
            $this->db->update($this->table);
            $db_error = $this->db->error();
            if ($db_error['code'] > 0) {
                log_message('error', json_encode($data));
                throw new Exception($db_error['message']);
            }
            return "";
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
}
