<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_mutasi extends CI_Model
{
    public function acc_dept_mutasi_by_kode($dept_id)
    {
        return $this->db->query("SELECT dept_id, seq, dept_id_dari, dept_id_tujuan, type, step
                                FROM acc_dept_mutasi
                                WHERE dept_id = '$dept_id' 
                                ORDER BY step, seq");
    }

    public function acc_mutasi_gdb_by_kode($tahun, $bulan, $field)
    {
        return $this->db->query("SELECT $field
                                FROM acc_mutasi_gdb
                                WHERE periode_th = '$tahun'  AND periode_bln = '$bulan'
                                ORDER BY nama_produk");
    }

}