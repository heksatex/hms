<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_doneMO extends CI_Model
{

    function get_data_mrp_by_kode($dept,$tgl_dari,$tgl_sampai) // done MO realtime
    {
        return $this->db->query("SELECT mrp.kode, mrp.tanggal, d.nama as departemen, mrp.status
                        FROM mrp_production mrp
                        LEFT JOIN departemen d ON mrp.dept_id = d.kode
                        WHERE mrp.dept_id = '$dept' AND mrp.tanggal >= '$tgl_dari' AND mrp.tanggal <= '$tgl_sampai' AND mrp.status = 'done' ")->result();
    }

    function get_data_mrp_done_by_kode($dept,$tgl_dari,$tgl_sampai)
    {
        return $this->db->query("SELECT d.nama as departemen, mrp.kode, mrp.tanggal, mrp.dept_id, mrp.con_mtr, mrp.con_kg, mrp.prod_mtr, mrp.prod_kg, mrp.waste_mtr, mrp.waste_kg, mrp.adj_mtr, mrp.adj_kg, mrp.status
                                FROM mrp_production_done mrp 
                                INNER JOIN departemen d ON mrp.dept_id = d.kode 
                                WHERE mrp.tanggal >= '$tgl_dari' AND mrp.tanggal <= '$tgl_sampai' ")->result();
    }

}