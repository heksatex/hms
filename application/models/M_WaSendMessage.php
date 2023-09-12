<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_WaSendMessage extends CI_Model {

    var $column_order = array(null, 'touser', 'togroup', 'created_at', 'status');
    var $column_search = array('touser', 'togroup', 'status');
    var $order = array('created_at', 'desc');
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
}
