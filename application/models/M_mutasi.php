<?php defined("BASEPATH") or exit  ('No Direct Script Acces Allowed');

/**
 * 
 */
class M_mutasi extends CI_Model
{
    public function acc_dept_mutasi_by_kode($dept_id,$step)
    {   
        $where_step = '';
        if(!empty($step)){
            $where_step = " AND step = '$step' ";
        }
        return $this->db->query("SELECT dept_id, seq, dept_id_dari, dept_id_tujuan, type, step
                                FROM acc_dept_mutasi
                                WHERE dept_id = '$dept_id' $where_step
                                ORDER BY step, seq");
    }

    public function acc_mutasi_by_kode($table,$tahun, $bulan, $field)
    {
        return $this->db->query("SELECT $field
                                FROM $table
                                WHERE periode_th = '$tahun'  AND periode_bln = '$bulan'
                                ORDER BY nama_produk");
    }

}