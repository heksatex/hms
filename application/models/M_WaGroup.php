<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_WaGroup extends CI_Model {


    var $column_order = array(null, 'wg.wa_group', 'kode','wg.created_at');
    var $column_search = array('wg.wa_group');
    var $order = array('wg.wa_group', 'asc');
    var $table = "wa_group";

    protected function _getDataQuery() {
        $this->db->select('wg.*,b.kode');
        $this->db->from('wa_group as wg');
        $this->db->join('(select wa_group_id, GROUP_CONCAT(department_kode) as kode from wa_group_departemen GROUP BY wa_group_id) as b', 'b.wa_group_id = wg.id', 'LEFT');
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
        $this->_getDataQuery();
        $query = $this->db->get();
        return $query->num_rows();
        ;
    }

    public function getCountAllData() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    public function simpan($wagroup) {
        $this->db->insert($this->table, array(
            'wa_group' => $wagroup,
            'created_at' => date('Y-m-d H:i:s')
        ));
        return $this->db->insert_id();
    }

    public function update($id, $group) {
        $this->db->set('wa_group', $group);
        $this->db->where('id', $id);
        $this->db->update($this->table);

        return is_array($this->db->error());
    }

    public function getDataByID($id) {

        $this->db->from('wa_group as wg');
        $this->db->where('wg.id', $id);
        //$this->db->query('select wg.*,kode from ' . $this->table . ' as wg where wg.id = ' . $id);
        $this->db->join('(select wa_group_id, GROUP_CONCAT(department_kode) as kode from wa_group_departemen GROUP BY wa_group_id) as b', 'b.wa_group_id = wg.id', 'LEFT');
        return $this->db->select('wg.*,b.kode')->get()->row();
    }

    public function getDataByNama($group) {
        $this->db->from('wa_group');
        $this->db->where('wa_group', $group);
        return $this->db->select('*')->get()->row();
    }

    public function getDataByDepth(array $kodedepth) {
        $this->db->from('wa_group as a');
        $this->db->join('wa_group_departemen as b','a.id = b.wa_group_id');
        $this->db->where_in('department_kode', $kodedepth);
        return $this->db->select('*')->get()->result();
    }

    public function getData() {
        try {
            $this->_getDataQuery();
            if ($_POST['length'] != -1)
                $this->db->limit($_POST['length'], $_POST['start']);
            $query = $this->db->get();
            return $query->result();
        } catch (Exception $ex) {
            throw $ex;
        }
    }

    public function deleteWaDepartmen($groupid) {
        $this->db->query("DELETE FROM wa_group_departemen WHERE wa_group_id = $groupid");
    }

    public function addWaDepartment($groupid, $kodeDept) {
        $this->db->insert('wa_group_departemen', array(
            'wa_group_id' => $groupid,
            'department_kode' => $kodeDept
        ));
    }
    
    public function getDataQuery() {
        return $this->db->query('select id,wa_group from wa_group')->result();
    }

    public function startTransaction() {
        $this->db->trans_begin();
    }

    public function finishTransaction() {
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return true;
        }
    }

}
