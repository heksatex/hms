<?php

defined("BASEPATH") or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of m_stock_move
 *
 * @author RONI
 */
class M_stock_move extends CI_Model {

    //put your code here
    protected $table = "stock_move";
    protected $order = array(null, "move_id", "create_date", "origin", "source_move", "method", "lokasi_dari", "lokasi_tujuan", "status");
    protected $search = array("move_id", "create_date", "origin", "source_move", "method", "lokasi_dari", "lokasi_tujuan");
    protected $query = null;

    private function _get_datatables_query() {
        $this->db->from($this->table);

        $i = 0;

        foreach ($this->search as $item) { // loop column 
            if ($_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    public function sm_get_datatables(array $condition) {
        $this->_get_datatables_query();
        $this->db->select("*");
        $this->db->group_by('move_id');
        $this->db->where($condition);

        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    public function sm_count_filtered(array $condition) {
        $this->_get_datatables_query();
        $this->db->group_by('move_id');
        $this->db->select("*");
        $this->db->where($condition);
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function sm_count_all(array $condition) {
        $this->_get_datatables_query();
        $this->db->where($condition);
        return $this->db->count_all_results();
    }
}
