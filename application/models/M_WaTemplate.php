<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_WaTemplate extends CI_Model {

    var $column_order = array(null, 'wt.nama', 'wt.template', 'wt.created_at');
    var $column_search = array('wt.nama');
    var $order = ['wt.created_at' => 'desc'];
    var $table = "wa_template";

    protected function getDataQuery() {
        $this->db->select('*');
        $this->db->from('wa_template as wt');

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

    public function simpan($nama, $template) {
        $this->db->insert($this->table, array(
            'nama' => $nama,
            'template' => $template,
            'created_at' => date('Y-m-d H:i:s')
        ));
        return is_array($this->db->error());
    }

    public function getDataByID($id) {
        return $this->db->query('select * from ' . $this->table . ' where id = ' . $id)->row();
    }

    public function getDataByName($name) {
        return $this->db->query('select * from ' . $this->table . ' where nama = "' . addslashes($name) . '"')->row();
    }

    public function update(array $where, array $set) {
        $this->db->where($where);
        $this->db->update($this->table, $set);

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
