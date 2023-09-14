<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_WaScheduleMessage extends CI_Model {

    var $column_order = array(null, 'wsm.message', 'groupname', 'day', 'wsm.send_time');
    var $column_search = array('wsm.message');
    var $order = array('wsm.id', 'desc');
    var $table = "wa_schedule_message";

    protected function getDataQuery() {
        $this->db->select('wsm.*,day,groupid,groupname');
        $this->db->from($this->table . ' as wsm');
        $this->db->join('(select wa_schedule_message_id, GROUP_CONCAT(day) as day from wa_schedule_message_days GROUP BY wa_schedule_message_id) as b', 'b.wa_schedule_message_id = wsm.id', 'LEFT');
        $this->db->join('(select wa_schedule_message_id, GROUP_CONCAT(wa_group_id) as groupid,GROUP_CONCAT(d.wa_group) as groupname from wa_schedule_message_group as c '
                . ' join wa_group as d on d.id = c.wa_group_id GROUP BY wa_schedule_message_id) d', 'b.wa_schedule_message_id = wsm.id', 'LEFT');
        $this->db->group_by('wsm.id');
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

    public function simpan($pesan, $waktu_kirim) {
        $this->db->insert($this->table, array(
            'message' => addslashes($pesan),
            'send_time' => addslashes($waktu_kirim),
            'created_at' => date('Y-m-d H:i:s')
        ));
        return $this->db->insert_id() ?? null;
    }

    public function update($id, $pesan, $waktu_kirim) {
        $this->db->where('id', $id);
        $this->db->set('message', $pesan);
        $this->db->set('send_time', $waktu_kirim);
        $this->db->update($this->table);
        return is_array($this->db->error());
    }

    public function simpanGroup($id, $groupid) {
        $this->db->insert('wa_schedule_message_group', array(
            'wa_schedule_message_id' => $id,
            'wa_group_id' => $groupid
        ));
        return is_array($this->db->error());
    }

    public function simpanDays($id, $day) {
        $this->db->insert('wa_schedule_message_days', array(
            'wa_schedule_message_id' => $id,
            'day' => $day
        ));
        return is_array($this->db->error());
    }

    public function deleteSchedule($id) {
        $this->db->query('delete from wa_schedule_message where id =' . $id);
    }

    public function deleteDays($id) {
        $this->db->query('delete from wa_schedule_message_days where wa_schedule_message_id =' . $id);
    }

    public function deleteGroup($id) {
        $this->db->query('delete from wa_schedule_message_group where wa_schedule_message_id =' . $id);
    }

    public function getDataByID($id) {

        $this->db->from($this->table . ' as wsm');
        $this->db->join('(select wa_schedule_message_id, GROUP_CONCAT(day) as day from wa_schedule_message_days GROUP BY wa_schedule_message_id) as b', 'b.wa_schedule_message_id = wsm.id', 'LEFT');
        $this->db->join('(select wa_schedule_message_id, GROUP_CONCAT(wa_group_id) as groupid,GROUP_CONCAT(d.wa_group) as groupname from wa_schedule_message_group as c '
                . ' join wa_group as d on d.id = c.wa_group_id GROUP BY wa_schedule_message_id) d', 'b.wa_schedule_message_id = wsm.id', 'LEFT');
        $this->db->where('wsm.id', $id);
        $this->db->group_by('wsm.id');
        return $this->db->select('wsm.*,day,groupid')->get()->row();
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
