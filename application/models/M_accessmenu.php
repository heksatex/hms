<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

class M_accessmenu extends CI_Model {

    protected $table = 'mst_access_menu';

    public function __construct() {
        parent::__construct();
    }

    public function getDetail(array $condition) {
        $this->db->from($this->table);
        $this->db->where($condition);
        return $this->db->select('*')->get()->row();
    }
}
