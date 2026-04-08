<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */

class M_neraca extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('periodesaldo');
    }

    function query_entries($tgldari, $tglsampai)
    {
        if (isset($tgldari) and isset($tglsampai)) {
            $tgl_dari = date("Y-m-d H:i:s", strtotime($tgldari));
            $tglsampai = date("Y-m-d 23:59:59", strtotime($tglsampai));
            $this->db->where('je.tanggal_dibuat >=', $tgl_dari);
            $this->db->where('je.tanggal_dibuat <=', $tglsampai);
        }
        $this->db->where('je.status', 'posted');
        $this->db->select("jei.posisi, jei.kode_coa,  IFNULL(SUM(CASE WHEN jei.posisi = 'D' THEN jei.nominal ELSE 0 END),0) AS total_debit,   IFNULL(SUM(CASE WHEN jei.posisi = 'C' THEN jei.nominal ELSE 0 END),0) AS total_credit");
        $this->db->from("acc_jurnal_entries_items jei");
        $this->db->join("acc_jurnal_entries je", 'je.kode = jei.kode');
        $this->db->group_by('jei.kode_coa');
        $query = $this->db->get_compiled_select();
        return $query;
    }


    public function get_list_neraca_standar($tglsampai, array $where = [])
    {

        // $subquery_credit = $this->get_saldo_sblm($tgldari, 'C');

        $start     = $this->periodesaldo->get_start_periode();
        $tgldari   = date("Y-m-d 00:00:00", strtotime($start)); // example 2025-01-01 00:00:00 by table setting

        // get saldo debit / credit yang berjalan
        $entries = $this->query_entries($tgldari, $tglsampai);

        if (count($where) > 0) {
            $this->db->where($where);
        }

        $this->db->select(" coa.kode_coa, coa.nama as nama_coa,coa.saldo_normal,saldo_awal,
                            COALESCE(jr.total_debit, 0) as total_debit,
                            COALESCE(jr.total_credit, 0) as total_credit,
                            ");
        $this->db->from('acc_coa coa');
        $this->db->join("({$entries}) as jr ", "jr.kode_coa = coa.kode_coa", "left");
        $this->db->order_by('coa.kode_coa');
        $query = $this->db->get();
        return $query;
    }


    function query_entries_2($tgldari, $tglsampai)
    {
        if (isset($tgldari) and isset($tglsampai)) {
            $tgl_dari = date("Y-m-d H:i:s", strtotime($tgldari));
            $tglsampai = date("Y-m-d 23:59:59", strtotime($tglsampai));
            $this->db->where('je.tanggal_dibuat >=', $tgl_dari);
            $this->db->where('je.tanggal_dibuat <=', $tglsampai);
        }
        $this->db->where('je.status', 'posted');
        $this->db->select("jei.posisi, jei.kode_coa,  
                        IFNULL(SUM(CASE WHEN jei.posisi = 'D' THEN jei.nominal ELSE 0 END),0) AS total_debit,  
                        IFNULL(SUM(CASE WHEN jei.posisi = 'C' THEN jei.nominal ELSE 0 END),0) AS total_credit");
        $this->db->from("acc_jurnal_entries_items jei");
        $this->db->join("acc_jurnal_entries je", 'je.kode = jei.kode');
        $this->db->group_by('jei.kode_coa');
        $query = $this->db->get_compiled_select();
        return $query;
    }

    public function get_total_saldo_akhir_posisi_by_coa(array $where = [], $posisi)
    {
        if (count($where) > 0) {
            $this->db->where($where);
        }

        $this->db->select('jei.kode_coa, SUM(jei.nominal) as total_' . $posisi);
        $this->db->from('acc_jurnal_entries je');
        $this->db->join('acc_jurnal_entries_items jei', 'jei.kode = je.kode');
        $this->db->group_by('jei.kode_coa');
        return $this->db->get_compiled_select();
    }

    public function get_saldo_sblm($tgldari, $posisi)
    {
        $start      = $this->periodesaldo->get_start_periode();
        $tgl_dari   = date("Y-m-d 00:00:00", strtotime($start));
        $tgl_sampai = date("Y-m-d 23:59:59", strtotime("-1 day", strtotime($tgldari)));

        $kata_posisi = ($posisi == 'D') ? 'debit' : 'credit';

        // Cek: Jika ternyata tgl_sampai < tgl_dari, berarti tidak ada mutasi jurnal 
        // sebelum periode ini yang perlu dihitung (saldo awal murni dari coa.saldo_awal)
        if (strtotime($tgl_sampai) < strtotime($tgl_dari)) {
            // Kita buat subquery dummy yang mengembalikan 0 agar join tidak error/kosong
            return "SELECT kode_coa, 0 as total_$kata_posisi FROM acc_jurnal_entries_items GROUP BY kode_coa";
        }

        $tmp_where = [
            'je.tanggal_dibuat >= ' => $tgl_dari,
            'je.tanggal_dibuat <= ' => $tgl_sampai,
            'je.status' => 'posted',
            'jei.posisi' => $posisi
        ];

        return $this->get_total_saldo_akhir_posisi_by_coa($tmp_where, $kata_posisi);
    }

    public function get_list_neraca_monthly($start_dt, $end_dt, $period_list)
    {
        // Ambil awal periode sistem (misal saldo awal tahun)
        $start_system = $this->periodesaldo->get_start_periode();
        $tgl_dari_sistem = date("Y-m-d 00:00:00", strtotime($start_system));

        $tgl_cut_off = date("Y-m", strtotime($start_system));

        // Tanggal filter dari UI
        $tgl_dari_filter   = $start_dt->format("Y-m-d 00:00:00");
        $tgl_sampai_filter = $end_dt->format("Y-m-t 23:59:59"); // Sampai akhir bulan

        $tgl_dari_filter2  = $start_dt->format("Y-m");

        // 1. Subquery untuk Saldo Sebelum Filter (untuk dapat Saldo Awal)
        $subquery_debit = $this->get_saldo_sblm($tgl_dari_filter, 'D');
        $subquery_credit = $this->get_saldo_sblm($tgl_dari_filter, 'C');

        // 2. Siapkan Dynamic Select untuk tiap bulan dalam range
        $case_months = "";
        foreach ($period_list as $p) {
            // Logika: Jika saldo normal D (D-C), jika C (C-D)
            // Kita hitung pergerakan NET per bulan
            $case_months .= ", SUM(CASE WHEN YEAR(je.tanggal_dibuat) = {$p['thn']} AND MONTH(je.tanggal_dibuat) = {$p['bln']} 
                            THEN (CASE WHEN jei.posisi='D' THEN jei.nominal ELSE -jei.nominal END) 
                            ELSE 0 END) AS {$p['key']}";
        }

        // 3. Subquery Pergerakan Bulanan (Joined dengan Jurnal)
        // Kita buat subquery terpisah agar Group By tidak merusak Join COA
        $this->db->select("jei.kode_coa $case_months");
        $this->db->from("acc_jurnal_entries_items jei");
        $this->db->join("acc_jurnal_entries je", "je.kode = jei.kode");
        $this->db->where("je.status", "posted");
        $this->db->where("je.tanggal_dibuat <=", $tgl_sampai_filter);
        $this->db->group_by("jei.kode_coa");
        $subquery_monthly = $this->db->get_compiled_select();

        // 4. Main Query: Join COA dengan semua Subquery
        $this->db->select("
                coa.kode_coa, 
                coa.nama as nama_coa, 
                coa.saldo_normal, 
                coa.level,
                coa.parent,
                coa.saldo_awal as saldo_awal_database,
                (CASE 
                    WHEN coa.saldo_normal = 'D' THEN 
                        (coa.saldo_awal + COALESCE(debit_sbl.total_debit, 0) - COALESCE(credit_sbl.total_credit, 0))
                    WHEN coa.saldo_normal = 'C' THEN
                        ( coa.saldo_awal + COALESCE(credit_sbl.total_credit, 0) - COALESCE(debit_sbl.total_debit, 0))
                    ELSE coa.saldo_awal 
                END) as saldo_awal_finish
            ");

        // Tambahkan kolom kolom bulan ke select utama
        foreach ($period_list as $p) {
            $this->db->select("COALESCE(monthly.{$p['key']}, 0) as {$p['key']}");
        }

        $this->db->from('acc_coa coa');
        $this->db->join("($subquery_debit) as debit_sbl", "debit_sbl.kode_coa = coa.kode_coa", "left");
        $this->db->join("($subquery_credit) as credit_sbl", "credit_sbl.kode_coa = coa.kode_coa", "left");
        $this->db->join("($subquery_monthly) as monthly", "monthly.kode_coa = coa.kode_coa", "left");

        // Filter hanya akun Neraca (1, 2, 3)
        $this->db->where("LEFT(coa.kode_coa, 1) <=", "3");
        $this->db->order_by('coa.kode_coa', 'ASC');

        return $this->db->get();
    }


    public function get_list_neraca_yearly($tahun_dari, $tahun_sampai, $period_list)
    {
        // 1. Pengaturan Tanggal Filter
        // Awal tahun (1 Januari) sampai akhir tahun (31 Desember)
        $tgl_dari_filter   = "$tahun_dari-01-01 00:00:00";
        $tgl_sampai_filter = "$tahun_sampai-12-31 23:59:59";

        // 2. Subquery Saldo Sebelum Filter (untuk dapat Saldo Awal)
        // Fungsi get_saldo_sblm biasanya menerima parameter tanggal awal filter
        $subquery_debit = $this->get_saldo_sblm($tgl_dari_filter, 'D');
        $subquery_credit = $this->get_saldo_sblm($tgl_dari_filter, 'C');

        // 3. Siapkan Dynamic Select untuk tiap tahun dalam range
        $case_years = "";
        foreach ($period_list as $p) {
            // Logika: Kita hanya mengecek YEAR, tidak perlu MONTH lagi
            $case_years .= ", SUM(CASE WHEN YEAR(je.tanggal_dibuat) = {$p['thn']} 
                        THEN (CASE WHEN jei.posisi='D' THEN jei.nominal ELSE -jei.nominal END) 
                        ELSE 0 END) AS {$p['key']}";
        }

        // 4. Subquery Pergerakan Tahunan (Joined dengan Jurnal)
        $this->db->select("jei.kode_coa $case_years");
        $this->db->from("acc_jurnal_entries_items jei");
        $this->db->join("acc_jurnal_entries je", "je.kode = jei.kode");
        $this->db->where("je.status", "posted");
        $this->db->where("je.tanggal_dibuat <=", $tgl_sampai_filter);
        $this->db->group_by("jei.kode_coa");
        $subquery_yearly = $this->db->get_compiled_select();

        $start_system = $this->periodesaldo->get_start_periode();
        $tgl_cut_off = date("Y", strtotime($start_system));

        $tahun_dari  = date("Y", strtotime($tgl_dari_filter));


        // 5. Main Query: Join COA dengan semua Subquery
        $this->db->select("
            coa.kode_coa, 
            coa.nama as nama_coa, 
            coa.saldo_normal, 
            coa.level,
            coa.parent,
            coa.saldo_awal as saldo_awal_database,
            (CASE 
                WHEN coa.saldo_normal = 'D' THEN 
                    (coa.saldo_awal + COALESCE(debit_sbl.total_debit, 0) - COALESCE(credit_sbl.total_credit, 0))
                WHEN coa.saldo_normal = 'C' THEN
                    (coa.saldo_awal + COALESCE(credit_sbl.total_credit, 0) - COALESCE(debit_sbl.total_debit, 0))
                ELSE coa.saldo_awal 
            END) as saldo_awal_finish
        ");

        // Tambahkan kolom tahun ke select utama
        foreach ($period_list as $p) {
            $this->db->select("COALESCE(yearly.{$p['key']}, 0) as {$p['key']}");
        }

        $this->db->from('acc_coa coa');
        $this->db->join("($subquery_debit) as debit_sbl", "debit_sbl.kode_coa = coa.kode_coa", "left");
        $this->db->join("($subquery_credit) as credit_sbl", "credit_sbl.kode_coa = coa.kode_coa", "left");
        $this->db->join("($subquery_yearly) as yearly", "yearly.kode_coa = coa.kode_coa", "left");

        // Filter hanya akun Neraca (1, 2, 3)
        $this->db->where("LEFT(coa.kode_coa, 1) <=", "3");
        $this->db->order_by('coa.kode_coa', 'ASC');

        return $this->db->get();
    }
}
