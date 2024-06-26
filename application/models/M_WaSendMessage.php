<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_WaSendMessage extends CI_Model {

    var $column_order = array(null, null, 'touser', 'togroup', 'status', 'created_at');
    var $column_search = array('touser', 'togroup', 'status');
    var $order = ['created_at' => 'desc'];
    var $table = "wa_send_message";

    protected function getDataQuery() {
        $this->db->select('*');
        $this->db->from($this->table);

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

    public function getDataByID($id) {
        return $this->db->query('select * from ' . $this->table . ' where id = ' . $id)->row();
    }

    public function getCountDataFiltered() {
        $this->getDataQuery();
        $query = $this->db->get();
        return $query->num_rows();
        ;
    }

    public function getCountAllData() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function getUser($cari = null) {
        $this->db->select('nama,telepon_wa,dept');
        $this->db->from('user');
        if (!is_null($cari)) {
            $this->db->or_like('nama', $cari);
            $this->db->or_like('telepon_wa', $cari);
            $this->db->or_like('dept', $cari);
        }
        $this->db->where("telepon_wa !=", "");
        $this->db->limit(15);
        $query = $this->db->get();
        return $query->result();
    }

    public function save($message, array $to) {
        $data = array_merge(array(
            'message' => $message,
            'status' => 2,
            'created_at' => date('Y-m-d H:i:s')
                ), $to);
        $this->db->insert($this->table, $data);
        return is_array($this->db->error());
    }

    public function getData() {
        try {
            $this->getDataQuery();
            if ($_POST['length'] != -1)
                $this->db->limit($_POST['length'], $_POST['start']);
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function update(array $condition, array $data) {
        $this->db->set($data);
        $this->db->where($condition);
        $this->db->update($this->table);
    }
}
