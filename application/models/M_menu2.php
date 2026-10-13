<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_menu2 extends CI_Model
{
    /* =========================================================
     * MAIN MENU
     * ========================================================= */

    public function get_main_menu()
    {
        return $this->db
            ->order_by('row_order', 'ASC')
            ->get('main_menu')
            ->result_array();
    }

    public function get_main_menu_by_kode($kode)
    {
        return $this->db
            ->where('kode', $kode)
            ->get('main_menu')
            ->row_array();
    }

    public function insert_main_menu($data)
    {
        return $this->db->insert('main_menu', $data);
    }

    public function update_main_menu($kode, $data)
    {
        return $this->db
            ->where('kode', $kode)
            ->update('main_menu', $data);
    }

    public function delete_main_menu($kode)
    {
        return $this->db
            ->where('kode', $kode)
            ->delete('main_menu');
    }


    /* =========================================================
     * MAIN MENU REL
     * ========================================================= */

    public function get_rel_by_main_menu($main_menu_kode)
    {
        return $this->db
            ->where('main_menu_kode', $main_menu_kode)
            ->get('main_menu_rel')
            ->result_array();
    }

    public function get_rel_by_sub_menu($sub_kode)
    {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->get('main_menu_rel')
            ->row_array();
    }

    public function insert_rel($data)
    {
        return $this->db->insert('main_menu_rel', $data);
    }

    public function delete_rel_by_sub($sub_kode)
    {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->delete('main_menu_rel');
    }


    /* =========================================================
     * SUB MENU
     * ========================================================= */

    public function get_sub_menu_by_kode($kode)
    {
        return $this->db
            ->where('kode', $kode)
            ->get('main_menu_sub')
            ->row_array();
    }

    public function get_sub_menu_by_main($main_kode)
    {
        $this->db->select('main_menu_sub.*');
        $this->db->from('main_menu_sub');

        $this->db->join(
            'main_menu_rel',
            'main_menu_rel.main_menu_sub_kode = main_menu_sub.kode',
            'inner'
        );

        $this->db->where(
            'main_menu_rel.main_menu_kode',
            $main_kode
        );

        // Hanya level pertama
        $this->db->where(
            'main_menu_sub.is_menu_sub',
            ''
        );

        $this->db->order_by(
            'main_menu_sub.row_order',
            'ASC'
        );

        return $this->db->get()->result_array();
    }

    public function get_child_menu($parent_kode)
    {
        return $this->db
            ->where('is_menu_sub', $parent_kode)
            ->order_by('row_order', 'ASC')
            ->get('main_menu_sub')
            ->result_array();
    }

    public function get_child_count($parent_kode)
    {
        return $this->db
            ->where('is_menu_sub', $parent_kode)
            ->count_all_results('main_menu_sub');
    }

    public function insert_sub_menu($data)
    {
        return $this->db->insert(
            'main_menu_sub',
            $data
        );
    }

    public function update_sub_menu($kode, $data)
    {
        return $this->db
            ->where('kode', $kode)
            ->update(
                'main_menu_sub',
                $data
            );
    }

    public function delete_sub_menu($kode)
    {
        return $this->db
            ->where('kode', $kode)
            ->delete('main_menu_sub');
    }


    /* =========================================================
     * CHECK DATA
     * ========================================================= */

    public function sub_exists($kode)
    {
        return $this->db
            ->where('kode', $kode)
            ->count_all_results('main_menu_sub') > 0;
    }

    public function main_exists($kode)
    {
        return $this->db
            ->where('kode', $kode)
            ->count_all_results('main_menu') > 0;
    }

    public function has_child($kode)
    {
        return $this->db
            ->where('is_menu_sub', $kode)
            ->count_all_results('main_menu_sub') > 0;
    }

    public function has_button($kode)
    {
        return $this->db
            ->where('main_menu_sub_kode', $kode)
            ->count_all_results('main_menu_sub_button') > 0;
    }

    public function has_status($kode)
    {
        return $this->db
            ->where('main_menu_sub_kode', $kode)
            ->count_all_results('main_menu_sub_status') > 0;
    }

    public function has_sub_menu($main_kode)
    {
        $this->db->select('main_menu_sub.kode');

        $this->db->from('main_menu_sub');

        $this->db->join(
            'main_menu_rel',
            'main_menu_rel.main_menu_sub_kode = main_menu_sub.kode',
            'inner'
        );

        $this->db->where(
            'main_menu_rel.main_menu_kode',
            $main_kode
        );

        return $this->db->count_all_results() > 0;
    }


    /* =========================================================
     * TREE
     * ========================================================= */

   public function get_menu_tree()
    {
        $main_menu = $this->get_main_menu();

        foreach ($main_menu as &$main) {

            $main['sub_menu'] =
                $this->get_sub_menu_by_main($main['kode']);

            foreach ($main['sub_menu'] as &$sub) {

                $sub['child_menu'] =
                    $this->get_child_menu($sub['kode']);

                $sub['button_count'] =
                    count($this->get_buttons_by_sub($sub['kode']));

                $sub['status_count'] =
                    count($this->get_status_by_sub($sub['kode']));

                foreach ($sub['child_menu'] as &$child) {

                    $child['button_count'] =
                        count(
                            $this->get_buttons_by_sub(
                                $child['kode']
                            )
                        );

                    $child['status_count'] =
                        count(
                            $this->get_status_by_sub(
                                $child['kode']
                            )
                        );
                }
            }
        }

        return $main_menu;
    }

    public function get_main_menu_by_sub($sub_kode)
    {
        $this->db->select('main_menu.*');
        $this->db->from('main_menu');
        $this->db->join(
            'main_menu_rel',
            'main_menu_rel.main_menu_kode = main_menu.kode',
            'inner'
        );
        $this->db->where(
            'main_menu_rel.main_menu_sub_kode',
            $sub_kode
        );

        return $this->db->get()->row_array();
    }

    public function parent_has_child($parent_kode)
    {
        return $this->db
            ->where('is_menu_sub', $parent_kode)
            ->count_all_results('main_menu_sub') > 0;
    }

    public function update_parent_status($kode)
    {
        $has_child = $this->has_child($kode);

        return $this->update_sub_menu($kode, [
            'parent' => $has_child ? 'true' : ''
        ]);
    }

    public function get_next_sub_menu_code()
    {
        $this->db->select('kode');
        $this->db->from('main_menu_sub');
        $this->db->like('kode', 'mms', 'after');
        $this->db->order_by('CAST(SUBSTRING(kode , 4) AS UNSIGNED)', 'DESC', FALSE);
        $this->db->limit(1);

        $row = $this->db->get()->row_array();

        if (empty($row)) {
            return 'mms1';
        }

        $last_number = (int) substr($row['kode'], 3);

        return 'mms' . ($last_number + 1);
    }

    /* =========================================================
    * BUTTON MENU
    * ========================================================= */

    public function get_buttons_by_sub($sub_kode)
    {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->order_by('row_order', 'ASC')
            ->get('main_menu_sub_button')
            ->result_array();
    }


    public function get_button($sub_kode, $kategori_button, $jenis_button)
    {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->where('kategori_button', $kategori_button)
            ->where('jenis_button', $jenis_button)
            ->get('main_menu_sub_button')
            ->row_array();
    }


    public function insert_button($data)
    {
        return $this->db->insert('main_menu_sub_button',$data);
    }


    public function update_button($sub_kode,$kategori_button,$jenis_button,$data) {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->where('kategori_button', $kategori_button)
            ->where('jenis_button', $jenis_button)
            ->update(
                'main_menu_sub_button',
                $data
            );
    }


    public function delete_button($sub_kode,$kategori_button,$jenis_button) {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->where('kategori_button', $kategori_button)
            ->where('jenis_button', $jenis_button)
            ->delete('main_menu_sub_button');
    }


    public function button_exists($sub_kode,$kategori_button,$jenis_button) {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->where('kategori_button', $kategori_button)
            ->where('jenis_button', $jenis_button)
            ->count_all_results('main_menu_sub_button') > 0;
    }


    public function get_next_button_order($sub_kode){
        $this->db->select_max('row_order')->where('main_menu_sub_kode', $sub_kode);
        $row = $this->db
            ->get('main_menu_sub_button')
            ->row_array();

        return ((int)($row['row_order'] ?? 0)) + 1;
    }


    /* =========================================================
    * STATUS MENU
    * ========================================================= */

    public function get_status_by_sub($sub_kode){
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->order_by('row_order', 'ASC')
            ->get('main_menu_sub_status')
            ->result_array();
    }


    public function get_status($sub_kode,$jenis_status) {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->where('jenis_status', $jenis_status)
            ->get('main_menu_sub_status')
            ->row_array();
    }


    public function insert_status($data){
        return $this->db->insert('main_menu_sub_status',$data);
    }


    public function update_status($sub_kode,$jenis_status,$data) {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->where('jenis_status', $jenis_status)
            ->update('main_menu_sub_status',$data);
    }


    public function delete_status($sub_kode,$jenis_status) {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->where('jenis_status', $jenis_status)
            ->delete('main_menu_sub_status');
    }


    public function status_exists($sub_kode,$jenis_status) {
        return $this->db
            ->where('main_menu_sub_kode', $sub_kode)
            ->where('jenis_status', $jenis_status)
            ->count_all_results(
                'main_menu_sub_status'
            ) > 0;
    }


    public function get_next_status_order($sub_kode)
    {
        $this->db->select_max('row_order')
            ->where('main_menu_sub_kode',$sub_kode);

        $row = $this->db->get('main_menu_sub_status')->row_array();
        return ((int)($row['row_order'] ?? 0)) + 1;
    }

    public function update_sub_menu_order($kode, $row_order)
    {
        return $this->db
            ->where('kode', $kode)
            ->update('main_menu_sub', [
                'row_order' => (int)$row_order
            ]);
    }

    public function has_user_priv($kode)
    {
        return $this->db
            ->where('main_menu_sub_kode', $kode)
            // ->where('action', 1)
            ->count_all_results('user_priv') > 0;
    }
}