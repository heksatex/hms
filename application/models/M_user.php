<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class M_user extends CI_Model {

    var $column_order = array(null, 'u.nama', 'u.username', 'u.level', 'd.nama_departemen', 'u.telepon_wa');
    var $column_search = array('u.nama', 'u.username', 'u.level', 'd.nama_departemen', 'u.telepon_wa');
    var $order = array('u.nama' => 'asc');

    private function _get_datatables_query() {
        $this->db->select("u.nama,u.username,u.level, u.dept, d.nama_departemen,u.telepon_wa,u.aktif");
        $this->db->from("user as u");
        $this->db->JOIN("mst_departemen_all as d", "d.kode = u.dept", "LEFT");

        $i = 0;

        foreach ($this->column_search as $item) { // loop column 
            if ($_POST['search']['value']) { // if datatable send POST for search
                if ($i === 0) { // first loop
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }

                if (count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }

        if (isset($_POST['order'])) { // here order processing
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }
    }

    function get_datatables() {
        $this->_get_datatables_query();
        if ($_POST['length'] != -1)
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    public function count_all() {
        $this->db->select("u.nama,u.username,u.level, u.dept, d.nama_departemen");
        $this->db->from("user as u");
        $this->db->JOIN("mst_departemen_all as d", "d.kode = u.dept", "LEFT");
        return $this->db->count_all_results();
    }

    public function cek_user_by_nama($namauser) {
        return $this->db->query("SELECT nama FROM user where nama = '$namauser'");
    }

    public function cek_user_by_login($login) {
        return $this->db->query("SELECT username FROM user where username = '$login'");
    }

    public function save_user($username, $password, $nama, $create_date, $departemen, $level, $sales_group, $telepon_wa) {
        return $this->db->query("INSERT INTO user(username,password,nama,create_date,dept,level,sales_group,telepon_wa,aktif) "
                        . "VALUES ('$username','$password','$nama','$create_date','$departemen','$level','$sales_group','$telepon_wa','0')");
    }

    public function set_aktif_user($username, $val) {
        $this->db->set('aktif', $val);
        $this->db->where('username', $username);
        $this->db->update('user');
        return is_array($this->db->error());
    }

    public function update_user($username, $nama, $departemen, $level, $sales_group, $telepon_wa) {
        return $this->db->query("UPDATE user set nama = '$nama' , dept = '$departemen', level='$level', sales_group='$sales_group',telepon_wa='$telepon_wa' WHERE username = '$username' ");
    }

    public function delete_user_priv($username) {
        $this->db->query("DELETE FROM user_priv WHERE username = '$username'");
    }

    public function save_user_priv($username, $main_menu_sub_kode, $action) {
        return $this->db->query("INSERT INTO user_priv(username,main_menu_kode,main_menu_sub_kode,action) VALUES ('$username',(SELECT main_menu_kode FROM main_menu_rel WHERE main_menu_sub_kode = '$main_menu_sub_kode'),'$main_menu_sub_kode','$action')");
    }

    public function get_user_by_username($username) {
        return $this->db->query("SELECT * FROM user where username = '$username' ")->row();
    }

    public function get_user_by_dept(array $kodeDept) {
        $this->db->from('user');
        $this->db->where_in('dept', $kodeDept);
        $query = $this->db->get();
        return $query->result();
    }

    public function get_priv_by_username($username) {
        return $this->db->query("SELECT main_menu_sub_kode FROM user_priv where username = '$username' ")->result();
    }

    public function get_list_menu_by_link_menu($link_menu) {
        return $this->db->query("SELECT * FROM main_menu_sub WHERE link_menu LIKE '$link_menu%' ORDER BY row_order asc ")->result();
    }

    public function get_jml_list_menu_by_link_menu($link_menu) {
        $query = $this->db->query("SELECT count(kode) as jml FROM main_menu_sub WHERE link_menu LIKE '$link_menu%'  ");
        $result = $query->row();
        return $result->jml;
    }

    public function get_nama_departemen_all_by_kode($kode) {
        $this->db->where('kode', $kode);
        $query = $this->db->get('mst_departemen_all');
        $result = $query->row_array();
        return $result['nama_departemen'];
    }
}
