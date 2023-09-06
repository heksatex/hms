<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_WaGroup extends CI_Model {

    var $column_order = array(null, 'wg.wa_group', 'wg.created_at');
    var $column_search = array('wg.wa_group');
    var $order = array('wg.wa_group', 'asc');
    var $table = "wa_group";

    protected function getDataQuery() {
        $this->db->select('*');
        $this->db->from('wa_group as wg');

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

    public function simpan($wagroup) {
        $this->db->insert($this->table, array(
            'wa_group' => $wagroup,
            'created_at' => date('Y-m-d H:i:s')
        ));
    }
    
    public function update($id,$group) {
        $this->db->set('wa_group',$group);
        $this->db->where('id',$id);
        $this->db->update($this->table);
        
        return is_array($this->db->error());
    }
    
    public function getDataByID($id) {
       return $this->db->query('select * from '.$this->table.' where id = '.$id)->row();
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
