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

    public function acc_mutasi_by_kode2($table,$tahun, $bulan, $field,$where)
    {
        return $this->db->query("SELECT $field
                                FROM $table mut
                                INNER JOIN mst_category cat ON mut.id_category = cat.id
                                WHERE mut.periode_th = '$tahun'  AND mut.periode_bln = '$bulan' $where
                                ORDER BY mut.id_category, mut.nama_produk");
    }

    public function acc_mutasi_by_kode($table,$tahun, $bulan, $field,$where)
    {
        return $this->db->query("SELECT $field
                                FROM $table
                                WHERE periode_th = '$tahun'  AND periode_bln = '$bulan' $where
                                ORDER BY nama_produk");
    }

    public function acc_mutasi_detail_by_kode($table,$tahun,$bulan,$where,$record,$recordPerPage)
    {
        return $this->db->query("SELECT m.posisi_mutasi, m.dept_id_mutasi, m.dept_id_dari, m.dept_id_tujuan, m.type, m.kode_transaksi, m.tanggal_transaksi, m.kode_produk, m.nama_produk, m.id_category, m.lot, m.qty, m.uom, m.qty2, m.uom2, m.qty_opname, m.uom_opname, m.origin, m.source_move, m.method, m.reff_picking, m.sc, m.sales_group, m.mo, cat.nama_category,  IF(ISNULL(m.sales_group) or m.sales_group = '', m.sales_group, msg.nama_sales_group) AS nama_sales_group, m.reff_note
                                FROM $table as m
                                LEFT JOIN mst_category as cat ON m.id_category = cat.id
                                LEFT JOIN mst_sales_group as msg ON msg.kode_sales_group = m.sales_group
                                WHERE m.periode_th = '$tahun'  AND m.periode_bln = '$bulan' $where
                                ORDER BY  m.posisi_mutasi asc, m.dept_id_tujuan asc, m.dept_id_dari asc, m.tanggal_transaksi asc, m.nama_produk asc
                                LIMIT $record, $recordPerPage");
    }

    public function acc_mutasi_detail_by_kode_no_limit($table,$tahun,$bulan,$where)
    {
        return $this->db->query("SELECT m.posisi_mutasi, m.dept_id_mutasi, m.dept_id_dari, m.dept_id_tujuan, m.type, m.kode_transaksi, m.tanggal_transaksi, m.kode_produk, m.nama_produk, m.id_category, m.lot, m.qty, m.uom, m.qty2, m.uom2, m.qty_opname, m.uom_opname, m.origin, m.source_move, m.method, m.reff_picking, m.sc, m.sales_group, m.mo, cat.nama_category,  IF(ISNULL(m.sales_group) or m.sales_group = '', m.sales_group, msg.nama_sales_group) AS nama_sales_group, m.reff_note
                                FROM $table as m
                                LEFT JOIN mst_category as cat ON m.id_category = cat.id
                                LEFT JOIN mst_sales_group as msg ON msg.kode_sales_group = m.sales_group
                                WHERE m.periode_th = '$tahun'  AND m.periode_bln = '$bulan' $where
                                ORDER BY  m.posisi_mutasi asc, m.dept_id_tujuan asc, m.dept_id_dari asc, m.tanggal_transaksi asc, m.nama_produk asc
                                ");
    }

    public function get_total_adjustment_in($table,$tahun,$bulan)
    {
        return $this->db->query("SELECT cat.nama_category, sum(mut.adj_in_lot) as total_lot_in, sum(mut.adj_in_qty1) as total_qty1_in, adj_in_qty1_uom, sum(mut.adj_in_qty2) as total_qty2_in, adj_in_qty2_uom
                            FROM $table as mut
                            INNER JOIN mst_category as cat ON cat.id = mut.id_category
                            WHERE periode_th = '$tahun'  AND periode_bln = '$bulan'  AND (mut.adj_in_qty1 <> 0 or mut.adj_in_qty2 <> 0  )
                            GROUP BY id_category
                                ");
    }

    public function get_total_adjustment_out($table,$tahun,$bulan)
    {
        return $this->db->query("SELECT cat.nama_category, sum(mut.adj_out_lot) as total_lot_out, sum(mut.adj_out_qty1) as total_qty1_out, adj_out_qty1_uom, sum(mut.adj_out_qty2) as total_qty2_out, adj_out_qty2_uom
                            FROM $table as mut
                            INNER JOIN mst_category as cat ON cat.id = mut.id_category
                            WHERE periode_th = '$tahun'  AND periode_bln = '$bulan'  AND (mut.adj_out_qty1 <> 0 or  mut.adj_out_qty2 <> 0 )
                            GROUP BY id_category
                                ");
    }

}