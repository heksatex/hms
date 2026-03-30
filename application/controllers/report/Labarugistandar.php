<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Labarugistandar extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model('m_worksheet');
    }

    public function index()
    {
        $id_dept        = 'RKLRS';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/v_laba_rugi_standar', $data);
    }


    public function loadData()
    {
        $validation = [
            [
                'field' => 'tgldari',
                'label' => 'Periode Dari',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih !',
                ]
            ],
            [
                'field' => 'tglsampai',
                'label' => 'Periode Sampai',
                'rules' => ['required'],
                'errors' => [
                    'required' => '{field} Harus dipilih !',
                ]
            ],
        ];

        try {
            $callback  = array();
            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                // throw new \Exception(array_values($this->form_validation->error_array())[0], 500);
                $callback = array('status' => 'failed', 'field' => 'periode', 'message' => array_values($this->form_validation->error_array())[0], 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else {

                $tgldari    = $this->input->post('tgldari');
                $tglsampai  = $this->input->post('tglsampai');
                $checkhidden = $this->input->post('checkhidden');
                $level       = $this->input->post('level');

                $data = $this->proses_data();
                $callback = array('status' => 'success', 'message' => 'berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'record' => $data);
            }

            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($callback));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    public function proses_datax($tgldari, $tglsampai, $level, $checkhidden)
    {

        $tgldari   = date('Y-m-d', strtotime($tgldari));
        $tglsampai = date('Y-m-d', strtotime($tglsampai));

        $sql = "
        SELECT 
            c.kode_coa,
            c.nama,
            c.level,
            c.parent,
            c.saldo_normal,

            COALESCE(SUM(
                CASE 
                    WHEN c.saldo_normal='D' AND i.posisi='D' THEN i.nominal
                    WHEN c.saldo_normal='D' AND i.posisi='C' THEN -i.nominal
                    WHEN c.saldo_normal='C' AND i.posisi='C' THEN i.nominal
                    WHEN c.saldo_normal='C' AND i.posisi='D' THEN -i.nominal
                END
            ),0) saldo

        FROM acc_coa c

        LEFT JOIN acc_jurnal_entries_items i 
            ON i.kode_coa = c.kode_coa

        LEFT JOIN acc_jurnal_entries j
            ON j.kode = i.kode
            AND j.status='posted'
            AND DATE(j.tanggal_dibuat) BETWEEN '$tgldari' AND '$tglsampai'

        WHERE LEFT(c.kode_coa,1) IN ('4','5')

        GROUP BY c.kode_coa
        ORDER BY c.kode_coa
        ";

        $rows = $this->db->query($sql)->result_array();

        $map = [];
        $data = [];

        foreach ($rows as $r) {

            $map[$r['kode_coa']] = $r;
            $map[$r['kode_coa']]['saldo_total'] = $r['saldo'];
        }

        /*
        ======================
        HITUNG SUBTOTAL PARENT
        ======================
        */

        foreach ($map as $kode => $row) {

            $parent = $row['parent'];

            while ($parent != "" && isset($map[$parent])) {

                $map[$parent]['saldo_total'] += $row['saldo'];

                $parent = $map[$parent]['parent'];
            }
        }

        /*
        ======================
        DETEKSI CHILD
        ======================
        */

        foreach ($map as $kode => $row) {

            $has_child = 0;

            foreach ($map as $child) {

                if ($child['parent'] == $kode) {
                    $has_child = 1;
                    break;
                }
            }

            $map[$kode]['has_child'] = $has_child;
        }

        /*
        ======================
        HITUNG LABA BERSIH
        ======================
        */

        $total_pendapatan = 0;
        $total_beban = 0;

        foreach ($map as $row) {

            if (substr($row['kode_coa'], 0, 1) == "4") {
                $total_pendapatan += $row['saldo_total'];
            }

            if (substr($row['kode_coa'], 0, 1) == "5") {
                $total_beban += $row['saldo_total'];
            }
        }

        $laba_bersih = $total_pendapatan - $total_beban;

        /*
        ======================
        FILTER LEVEL & HIDDEN
        ======================
        */

        foreach ($map as $row) {

            if ($checkhidden && $row['saldo_total'] == 0) {
                continue;
            }

            if (!empty($level)) {
                if (!in_array($row['level'], $level)) {
                    continue;
                }
            }

            $data[] = [
                "kode_acc" => $row['kode_coa'],
                "nama_acc" => $row['nama'],
                "level" => $row['level'],
                "parent" => $row['parent'],
                "has_child" => $row['has_child'],
                "saldo" => $row['saldo_total']
            ];
        }

        /*
        ======================
        TAMBAH LABA BERSIH
        ======================
        */

        $data[] = [
            "kode_acc" => "",
            "nama_acc" => "LABA BERSIH",
            "level" => 1,
            "parent" => "",
            "has_child" => 0,
            "saldo" => $laba_bersih
        ];

        return [
            "data" => $data
        ];
    }

    public function proses_dataxx($tgldari, $tglsampai, $level, $checkhidden)
    {

        $tgldari   = date('Y-m-d', strtotime($tgldari));
        $tglsampai = date('Y-m-d', strtotime($tglsampai));

        $sql = "
        SELECT 
            c.kode_coa,
            c.nama,
            c.level,
            c.parent,
            c.saldo_normal,

            COALESCE(SUM(
                CASE 
                    WHEN c.saldo_normal='D' AND i.posisi='D' THEN i.nominal
                    WHEN c.saldo_normal='D' AND i.posisi='C' THEN -i.nominal
                    WHEN c.saldo_normal='C' AND i.posisi='C' THEN i.nominal
                    WHEN c.saldo_normal='C' AND i.posisi='D' THEN -i.nominal
                END
            ),0) saldo

        FROM acc_coa c

        LEFT JOIN acc_jurnal_entries_items i 
            ON i.kode_coa = c.kode_coa

        LEFT JOIN acc_jurnal_entries j
            ON j.kode = i.kode
            AND j.status='posted'
            AND DATE(j.tanggal_dibuat) BETWEEN '$tgldari' AND '$tglsampai'

        WHERE LEFT(c.kode_coa,1) IN ('4','5')

        GROUP BY c.kode_coa
        ORDER BY c.kode_coa
        ";

        $rows = $this->db->query($sql)->result_array();

        $map = [];
        $tree = [];

        /*
        ======================
        BUILD MAP + TREE
        ======================
        */

        foreach ($rows as $r) {

            $r['saldo_total'] = $r['saldo'];

            $map[$r['kode_coa']] = $r;

            $tree[$r['parent']][] = $r['kode_coa'];
        }

        /*
        ======================
        HITUNG SALDO PARENT
        ======================
        */

        foreach ($map as $kode => $row) {

            $parent = $row['parent'];

            while ($parent != "" && isset($map[$parent])) {

                $map[$parent]['saldo_total'] += $row['saldo'];

                $parent = $map[$parent]['parent'];
            }
        }

        /*
        ======================
        BUILD TREE REPORT
        ======================
        */

        $data = [];

        $build = function ($parent) use (&$build, &$tree, &$map, &$data, $level, $checkhidden) {

            if (!isset($tree[$parent])) return;

            foreach ($tree[$parent] as $kode) {

                $row = $map[$kode];

                if ($checkhidden && $row['saldo_total'] == 0) continue;

                if (!empty($level) && !in_array($row['level'], $level)) continue;

                $has_child = isset($tree[$kode]) ? 1 : 0;

                $data[] = [
                    "kode_acc" => $row['kode_coa'],
                    "nama_acc" => $row['nama'],
                    "level" => $row['level'],
                    "parent" => $row['parent'],
                    "has_child" => $has_child,
                    "saldo" => $row['saldo_total'],
                    "tipe" => "akun"
                ];

                /*
            ======================
            CHILD LOOP
            ======================
            */

                $build($kode);

                /*
            ======================
            SUBTOTAL
            ======================
            */

                if ($has_child) {

                    $data[] = [
                        "kode_acc" => "",
                        "nama_acc" => $row['nama'] . " TOTAL",
                        "level" => $row['level'],
                        "parent" => $row['parent'],
                        "has_child" => 0,
                        "saldo" => $row['saldo_total'],
                        "tipe" => "subtotal"
                    ];
                }
            }
        };

        $build('');

        /*
        ======================
        HITUNG LABA BERSIH
        ======================
        */

        $total_pendapatan = 0;
        $total_beban = 0;

        foreach ($map as $row) {

            if (substr($row['kode_coa'], 0, 1) == '4')
                $total_pendapatan += $row['saldo_total'];

            if (substr($row['kode_coa'], 0, 1) == '5')
                $total_beban += $row['saldo_total'];
        }

        $laba_bersih = $total_pendapatan - $total_beban;

        $data[] = [
            "kode_acc" => "",
            "nama_acc" => "LABA BERSIH",
            "level" => 1,
            "parent" => "",
            "has_child" => 0,
            "saldo" => $laba_bersih,
            "tipe" => "laba"
        ];

        return [
            "data" => $data
        ];
    }

    // public function proses_data()
    // {
    //     $tgldari   = $this->input->post('tgldari');
    //     $tglsampai = $this->input->post('tglsampai');

    //     $tgldari   = date('Y-m-d', strtotime($tgldari)) . " 00:00:00";
    //     $tglsampai = date('Y-m-d', strtotime($tglsampai)) . " 23:59:59";

    //     /*
    //     =========================
    //     QUERY SALDO
    //     =========================
    //     */

    //     $sql = "
    //         SELECT 
    //             coa.kode_coa,
    //             coa.nama AS nama_coa,
    //             coa.saldo_normal,
    //             coa.level,
    //             coa.parent,

    //             IFNULL(
    //                 CASE
    //                     WHEN coa.saldo_normal = 'D' THEN je.total_debit - je.total_credit
    //                     WHEN coa.saldo_normal = 'C' THEN je.total_credit - je.total_debit
    //                 END
    //             ,0) AS saldo

    //         FROM acc_coa coa

    //         LEFT JOIN (

    //             SELECT 
    //                 jei.kode_coa,

    //                 SUM(CASE WHEN jei.posisi='D' THEN jei.nominal ELSE 0 END) AS total_debit,
    //                 SUM(CASE WHEN jei.posisi='C' THEN jei.nominal ELSE 0 END) AS total_credit

    //             FROM acc_jurnal_entries je

    //             INNER JOIN acc_jurnal_entries_items jei 
    //                 ON je.kode = jei.kode

    //             WHERE je.status = 'posted'
    //             AND je.tanggal_dibuat BETWEEN '$tgldari' AND '$tglsampai'

    //             GROUP BY jei.kode_coa

    //         ) je ON je.kode_coa = coa.kode_coa

    //         WHERE LEFT(coa.kode_coa,1) IN ('4','5')

    //         ORDER BY coa.kode_coa ASC
    //     ";

    //     $rows = $this->db->query($sql)->result_array();

    //     /*
    //     =========================
    //     BUILD TREE (parent -> child)
    //     =========================
    //     */

    //     $tree = [];

    //     foreach ($rows as $r) {
    //         $parent = $r['parent'] ?? '';
    //         $tree[$parent][] = $r;
    //     }

    //     /*
    //     =========================
    //     DETEKSI CHILD
    //     =========================
    //     */

    //     $has_child_map = [];

    //     foreach ($rows as $r) {
    //         if (!empty($r['parent'])) {
    //             $has_child_map[$r['parent']] = 1;
    //         }
    //     }

    //     /*
    //     =========================
    //     FUNCTION RECURSIVE
    //     =========================
    //     */

    //     $data = [];

    //     $buildTree = function ($parent) use (&$buildTree, &$tree, &$has_child_map, &$data) {

    //         if (!isset($tree[$parent])) return;

    //         foreach ($tree[$parent] as $row) {

    //             $data[] = [
    //                 "kode_acc" => $row['kode_coa'],
    //                 "nama_acc" => $row['nama_coa'],
    //                 "level" => (int)$row['level'],
    //                 "parent" => $row['parent'],
    //                 "has_child" => isset($has_child_map[$row['kode_coa']]) ? 1 : 0,
    //                 "saldo" => $row['saldo']
    //             ];

    //             // 🔁 recursive ke child
    //             $buildTree($row['kode_coa']);
    //         }
    //     };

    //     /*
    //     =========================
    //     PANGGIL ROOT
    //     =========================
    //     */

    //     // kalau parent root = kosong
    //     $buildTree(0);

    //     /*
    //       =========================
    //     OPTIONAL: LABA BERSIH
    //     =========================
    //     */

    //     $total_pendapatan = 0;
    //     $total_beban = 0;

    //     foreach ($rows as $r) {

    //         if (substr($r['kode_coa'], 0, 1) == "4") {
    //             $total_pendapatan += $r['saldo'];
    //         }

    //         if (substr($r['kode_coa'], 0, 1) == "5") {
    //             $total_beban += $r['saldo'];
    //         }
    //     }

    //     $laba_bersih = $total_pendapatan - $total_beban;

    //     $data[] = [
    //         "kode_acc" => "",
    //         "nama_acc" => "LABA BERSIH",
    //         "level" => 1,
    //         "parent" => "",
    //         "has_child" => 0,
    //         "saldo" => $laba_bersih
    //     ];

    //     /*
    //     =========================
    //     RETURN
    //     =========================
    //     */

    //     return [
    //         "record" => $data
    //     ];
    // }

    public function proses_dataxxx()
    {
        $tgldari    = $this->input->post('tgldari');
        $tglsampai  = $this->input->post('tglsampai');
        $levels     = $this->input->post('level'); // Array level [3,4,5]
        $hide_empty = $this->input->post('checkhidden'); // true/false

        $tgldari   = date('Y-m-d', strtotime($tgldari)) . " 00:00:00";
        $tglsampai = date('Y-m-d', strtotime($tglsampai)) . " 23:59:59";

        // 1. Ambil SEMUA COA kategori 4 & 5
        $all_coa = $this->db->query("SELECT kode_coa, nama, level, parent, saldo_normal 
                                 FROM acc_coa 
                                 WHERE LEFT(kode_coa,1) IN ('4','5') 
                                 ORDER BY kode_coa ASC")->result_array();

        // 2. Ambil saldo transaksi HANYA untuk akun leaf (paling bawah)
        $sql_saldo = "
        SELECT jei.kode_coa,
               SUM(CASE WHEN jei.posisi='D' THEN jei.nominal ELSE 0 END) AS total_debit,
               SUM(CASE WHEN jei.posisi='C' THEN jei.nominal ELSE 0 END) AS total_credit
        FROM acc_jurnal_entries je
        INNER JOIN acc_jurnal_entries_items jei ON je.kode = jei.kode
        WHERE je.status = 'posted' AND je.tanggal_dibuat BETWEEN '$tgldari' AND '$tglsampai'
        GROUP BY jei.kode_coa";
        $transaksi = $this->db->query($sql_saldo)->result_array();

        // Mapping transaksi ke array key-value
        $saldo_map = [];
        foreach ($transaksi as $t) {
            $saldo_map[$t['kode_coa']] = $t;
        }

        // 3. Fungsi hitung saldo roll-up (Induk = Sum Anak)
        $final_data = [];
        $get_balance = function ($kode_coa, $saldo_normal) use ($saldo_map) {
            $total = 0;
            foreach ($saldo_map as $kode => $val) {
                // Jika kode transaksi diawali dengan kode COA induk (hirarki)
                if (strpos($kode, $kode_coa) === 0) {
                    if ($saldo_normal == 'D') {
                        $total += ($val['total_debit'] - $val['total_credit']);
                    } else {
                        $total += ($val['total_credit'] - $val['total_debit']);
                    }
                }
            }
            return $total;
        };

        // 4. Bangun Struktur Hirarki (Flat List dengan baris TOTAL)
        $results = [];
        $total_pendapatan = 0;
        $total_beban = 0;

        foreach ($all_coa as $coa) {
            $saldo = $get_balance($coa['kode_coa'], $coa['saldo_normal']);

            // Filter Sembunyikan Data Kosong
            if ($hide_empty == "true" && $saldo == 0) continue;

            // Filter Level (Jika level tidak dicentang, lewati)
            if (!empty($levels) && !in_array($coa['level'], $levels)) continue;

            // Tambahkan Baris Header/Akun
            $results[] = [
                "kode_acc"  => $coa['kode_coa'],
                "nama_acc"  => $coa['nama'],
                "level"     => (int)$coa['level'],
                "saldo"     => ($coa['level'] >= 4) ? $saldo : null, // Induk level atas biasanya header saja
                "tipe"      => "row"
            ];

            // Jika level 3 atau 4, kita siapkan baris TOTAL di bawahnya (sesuai Gambar 1)
            // Cek apakah ada COA lain yang merupakan anak dari ini, jika iya, nanti buatkan total
            // Note: Logika sederhana untuk Gambar 1 biasanya baris total muncul setelah loop anak selesai
            // Namun untuk mempermudah, kita bisa tambahkan baris total jika COA ini punya anak
        }

        // Hitung Laba Bersih untuk footer
        foreach ($all_coa as $coa) {
            if ($coa['level'] == 1) { // Hitung dari level tertinggi agar akurat
                $s = $get_balance($coa['kode_coa'], $coa['saldo_normal']);
                if (substr($coa['kode_coa'], 0, 1) == "4") $total_pendapatan += $s;
                if (substr($coa['kode_coa'], 0, 1) == "5") $total_beban += $s;
            }
        }

        // var_dump($results);
        // die;


        return $results;

        // return $this->output
        //     ->set_content_type('application/json')
        //     ->set_output(json_encode([
        //         "record" => $results,
        //         "laba_bersih" => $total_pendapatan - $total_beban
        //     ]));
    }

    public function proses_data_g()
    {
        $tgldari    = $this->input->post('tgldari');
        $tglsampai  = $this->input->post('tglsampai');
        $levels     = $this->input->post('level'); // Array [1,2,3,4,5]
        $hide_empty = $this->input->post('checkhidden'); // "true"/"false"

        $tgldari   = date('Y-m-d', strtotime($tgldari)) . " 00:00:00";
        $tglsampai = date('Y-m-d', strtotime($tglsampai)) . " 23:59:59";

        // 1. Ambil SEMUA transaksi ujung (leaf)
        $sql_saldo = "
        SELECT jei.kode_coa,
               SUM(CASE WHEN jei.posisi='D' THEN jei.nominal ELSE 0 END) AS total_debit,
               SUM(CASE WHEN jei.posisi='C' THEN jei.nominal ELSE 0 END) AS total_credit
        FROM acc_jurnal_entries je
        INNER JOIN acc_jurnal_entries_items jei ON je.kode = jei.kode
        WHERE je.status = 'posted' AND je.tanggal_dibuat BETWEEN '$tgldari' AND '$tglsampai'
        GROUP BY jei.kode_coa";
        $transaksi = $this->db->query($sql_saldo)->result_array();

        $saldo_map = [];
        foreach ($transaksi as $t) {
            $saldo_map[$t['kode_coa']] = $t;
        }

        // 2. Ambil struktur COA
        $all_coa = $this->db->query("SELECT kode_coa, nama, level, parent, saldo_normal 
                                 FROM acc_coa 
                                 WHERE LEFT(kode_coa,1) IN ('4','5') 
                                 ORDER BY kode_coa ASC")->result_array();

        // 3. Fungsi hitung saldo roll-up
        $get_balance = function ($kode_coa, $saldo_normal) use ($saldo_map) {
            $total = 0;
            foreach ($saldo_map as $kode_tr => $val) {
                if (strpos($kode_tr, $kode_coa) === 0) {
                    if ($saldo_normal == 'D') {
                        $total += ($val['total_debit'] - $val['total_credit']);
                    } else {
                        $total += ($val['total_credit'] - $val['total_debit']);
                    }
                }
            }
            return $total;
        };

        $results = [];
        $total_pendapatan = 0;
        $total_beban = 0;

        foreach ($all_coa as $coa) {
            // Hitung saldo dulu untuk semua level
            $saldo = $get_balance($coa['kode_coa'], $coa['saldo_normal']);

            // Hitung Laba Bersih (berdasarkan level 1)
            if ($coa['level'] == 1) {
                if (substr($coa['kode_coa'], 0, 1) == "4") $total_pendapatan += $saldo;
                if (substr($coa['kode_coa'], 0, 1) == "5") $total_beban += $saldo;
            }

            // --- FILTER TAMPILAN ---
            if (!empty($levels) && !in_array($coa['level'], $levels)) continue;
            if ($hide_empty == "true" && $saldo == 0) continue;

            $results[] = [
                "kode_acc"  => $coa['kode_coa'],
                "nama_acc"  => $coa['nama'],
                "level"     => (int)$coa['level'],
                "parent"    => $coa['parent'],
                "saldo"     => $saldo,
                "tipe"      => "row"
            ];
        }

        return [
            "record" => $results,
            "laba_bersih" => $total_pendapatan - $total_beban
        ];
    }


    public function proses_data_fix1()
    {
        $tgldari    = $this->input->post('tgldari');
        $tglsampai  = $this->input->post('tglsampai');
        $levels     = $this->input->post('level'); // Misal: ["4", "5"]
        $hide_empty = $this->input->post('checkhidden');

        $tgldari   = date('Y-m-d', strtotime($tgldari)) . " 00:00:00";
        $tglsampai = date('Y-m-d', strtotime($tglsampai)) . " 23:59:59";

        // 1. Ambil SEMUA transaksi (Flat)
        $sql_saldo = "
        SELECT jei.kode_coa,
               SUM(CASE WHEN jei.posisi='D' THEN jei.nominal ELSE 0 END) AS total_debit,
               SUM(CASE WHEN jei.posisi='C' THEN jei.nominal ELSE 0 END) AS total_credit
        FROM acc_jurnal_entries je
        INNER JOIN acc_jurnal_entries_items jei ON je.kode = jei.kode
        WHERE je.status = 'posted' AND je.tanggal_dibuat BETWEEN '$tgldari' AND '$tglsampai'
        GROUP BY jei.kode_coa";
        $transaksi = $this->db->query($sql_saldo)->result_array();

        $saldo_map = [];
        foreach ($transaksi as $t) {
            $saldo_map[$t['kode_coa']] = $t;
        }

        // 2. Ambil SEMUA COA (4 & 5)
        $all_coa = $this->db->query("SELECT kode_coa, nama, level, parent, saldo_normal 
                                 FROM acc_coa 
                                 WHERE LEFT(CAST(kode_coa AS CHAR), 1) >= 4 
                                 ORDER BY kode_coa ASC")->result_array();

        // 3. Buat Map untuk mendeteksi apakah suatu COA punya anak (untuk icon toggle ▶)
        $has_child_map = [];
        foreach ($all_coa as $c) {
            if (!empty($c['parent'])) {
                $has_child_map[$c['parent']] = true;
            }
        }

        // 4. Fungsi hitung saldo roll-up
        $get_balance = function ($kode_coa, $saldo_normal) use ($saldo_map) {
            $total = 0;
            foreach ($saldo_map as $kode_tr => $val) {
                if (strpos($kode_tr, $kode_coa) === 0) {
                    if ($saldo_normal == 'D') {
                        $total += ($val['total_debit'] - $val['total_credit']);
                    } else {
                        $total += ($val['total_credit'] - $val['total_debit']);
                    }
                }
            }
            return $total;
        };

        $results = [];
        $total_pendapatan = 0;
        $total_beban = 0;

        foreach ($all_coa as $coa) {
            // Hitung saldo dulu (untuk keperluan Laba Bersih & Filter Kosong)
            $saldo = $get_balance($coa['kode_coa'], $coa['saldo_normal']);

            // Akumulasi Laba Bersih (selalu hitung dari level 1)
            if ($coa['level'] == 1) {
                if ((int) substr($coa['kode_coa'], 0, 1) == 4) $total_pendapatan += $saldo;
                if ((int) substr($coa['kode_coa'], 0, 1) >= 5) $total_beban += $saldo;
            }

            // --- FILTER DISPLAY ---
            // Jika user ceklis level 4 & 5, maka level 1, 2, 3 akan di-skip di sini
            if (!empty($levels) && !in_array($coa['level'], $levels)) {
                continue;
            }

            if ($hide_empty == "true" && $saldo == 0) {
                continue;
            }

            $results[] = [
                "kode_acc"  => $coa['kode_coa'],
                "nama_acc"  => $coa['nama'],
                "level"     => (int)$coa['level'],
                "parent"    => $coa['parent'],
                "has_child" => isset($has_child_map[$coa['kode_coa']]) ? 1 : 0,
                "saldo"     => $saldo
            ];
        }

        return [
            "record" => $results,
            "laba_bersih" => $total_pendapatan - $total_beban
        ];
    }


    public function proses_data($filter_manual = null)
    {

        // Jika ada filter manual (dari excel), gunakan itu. Jika tidak, ambil dari POST (dari loaddata).
        if (empty($filter_manual)) {
            // Jalur AJAX loaddata
            $tgldari    = $this->input->post('tgldari');
            $tglsampai  = $this->input->post('tglsampai');
            $levels     = $this->input->post('level'); // Pastikan ini array [1, 2, 3]
            $hide_empty = $this->input->post('checkhidden');
        } else {
            // Jalur Export Excel (mengambil dari arr_filter[0])
            $tgldari    = $filter_manual[0]['tgldari'] ?? '';
            $tglsampai  = $filter_manual[0]['tglsampai'] ?? '';
            $levels     = $filter_manual[0]['level'] ?? [];
            $hide_empty = $filter_manual[0]['checkhidden'] ?? false;
        }

        $hide_empty = ($hide_empty === 'true' || $hide_empty === true || $hide_empty === '1');

        $tgldari   = date('Y-m-d', strtotime($tgldari)) . " 00:00:00";
        $tglsampai = date('Y-m-d', strtotime($tglsampai)) . " 23:59:59";

        // 1. Ambil SEMUA transaksi
        $sql_saldo = "
        SELECT jei.kode_coa,
               SUM(CASE WHEN jei.posisi='D' THEN jei.nominal ELSE 0 END) AS total_debit,
               SUM(CASE WHEN jei.posisi='C' THEN jei.nominal ELSE 0 END) AS total_credit
        FROM acc_jurnal_entries je
        INNER JOIN acc_jurnal_entries_items jei ON je.kode = jei.kode
        WHERE je.status = 'posted' AND je.tanggal_dibuat BETWEEN '$tgldari' AND '$tglsampai'
        GROUP BY jei.kode_coa";
        $transaksi = $this->db->query($sql_saldo)->result_array();

        $saldo_map = [];
        foreach ($transaksi as $t) {
            $saldo_map[$t['kode_coa']] = $t;
        }

        // 2. Ambil SEMUA COA (Pendapatan & Beban)
        $all_coa = $this->db->query("SELECT kode_coa, nama, level, parent, saldo_normal 
                                 FROM acc_coa 
                                 WHERE LEFT(kode_coa,1) >= '4' 
                                 ORDER BY kode_coa ASC")->result_array();

        $get_balance = function ($kode_coa, $saldo_normal) use ($saldo_map) {
            $total = 0;
            foreach ($saldo_map as $kode_tr => $val) {
                if (strpos($kode_tr, $kode_coa) === 0) {
                    if ($saldo_normal == 'D') {
                        $total += ($val['total_debit'] - $val['total_credit']);
                    } else {
                        $total += ($val['total_credit'] - $val['total_debit']);
                    }
                }
            }
            return $total;
        };

        $results = [];
        $total_pendapatan = 0;
        $total_beban = 0;
        $stack = []; // Untuk menyimpan antrean baris Total

        foreach ($all_coa as $index => $coa) {
            $saldo = $get_balance($coa['kode_coa'], $coa['saldo_normal']);

            // Akumulasi Laba Bersih
            if ($coa['level'] == 1) {
                $prefix = substr($coa['kode_coa'], 0, 1);
                if ($prefix == '4') $total_pendapatan += $saldo;
                else if ($prefix >= '5') $total_beban += $saldo;
            }

            // Cek apakah akun selanjutnya adalah level yang lebih tinggi (kembali ke induk)
            // Atau apakah ini adalah data terakhir
            $next_coa = isset($all_coa[$index + 1]) ? $all_coa[$index + 1] : null;

            // --- FILTER DISPLAY ---
            $is_visible = (empty($levels) || in_array($coa['level'], $levels)) && !($hide_empty == "true" && $saldo == 0);

            if ($is_visible) {
                // Tambahkan baris Header/Akun (Saldo dikosongkan untuk Level < 4 sesuai request Anda)
                $results[] = [
                    "kode_acc"  => $coa['kode_coa'],
                    "nama_acc"  => $coa['nama'],
                    "level"     => (int)$coa['level'],
                    "saldo"     => ($coa['level'] > 4) ? $saldo : null, // Saldo hanya muncul di leaf/detail
                    "tipe"      => "row"
                ];

                // Simpan ke stack untuk baris Total nanti
                // Kita hanya ingin baris Total untuk level 1, 2, 3, 4
                if ($coa['level'] < 5) {
                    array_push($stack, [
                        "nama"  => "TOTAL " . $coa['nama'],
                        "level" => $coa['level'],
                        "saldo" => $saldo
                    ]);
                }
            }

            // LOGIKA INSERT TOTAL:
            // Jika akun berikutnya levelnya lebih kecil (misal skrg level 3, bsk level 2) 
            // atau sudah habis, maka keluarkan isi stack yang sesuai.
            while (!empty($stack) && ($next_coa == null || $next_coa['level'] <= end($stack)['level'])) {
                $last_stack = array_pop($stack);

                // Tambahkan baris Total ke hasil
                $results[] = [
                    "kode_acc"  => "",
                    "nama_acc"  => $last_stack['nama'],
                    "level"     => (int)$last_stack['level'],
                    "saldo"     => $last_stack['saldo'],
                    "tipe"      => "total" // Penanda untuk CSS di Frontend
                ];
            }
        }

        return [
            "record" => $results,
            "laba_bersih" => $total_pendapatan - $total_beban
        ];
    }



    public function export_excel()
    {
        try {
            $this->load->library('excel');

            $arr_filter = $this->input->post('arr_filter');

            $data_report = $this->proses_data($arr_filter);
            $records = $data_report['record'];
            $laba_bersih = $data_report['laba_bersih'];


            if (empty($records)) {
                throw new Exception("Data tidak ditemukan untuk periode tersebut.");
            }

            // Ambil filter untuk judul & periode
            $arr_filter = $this->input->post('arr_filter');
            $tgl_dari   = $arr_filter[0]['tgldari'] ?? '';
            $tgl_sampai = $arr_filter[0]['tglsampai'] ?? '';
            $periode = tgl_indo(date('d-m-Y', strtotime($tgl_dari))) . ' - ' . tgl_indo(date('d-m-Y', strtotime($tgl_sampai)));

            ob_start();
            $object = new PHPExcel();
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Laba Rugi');

            // --- HEADER LAPORAN ---
            $sheet->setCellValue('A1', 'PT. HEKSATEX INDAH');
            $sheet->setCellValue('A2', 'LABA RUGI (STANDAR)');
            $sheet->setCellValue('A3', 'Periode: ' . $periode);
            $sheet->mergeCells('A1:D1');
            $sheet->mergeCells('A2:D2');
            $sheet->mergeCells('A3:D3');
            $object->getActiveSheet()->getStyle("A1:A3")->getFont()->setBold(true);

            $object->getSheet(0)->getColumnDimension('A')->setWidth(5);   // No
            $object->getSheet(0)->getColumnDimension('B')->setWidth(15);  // Kode
            $object->getSheet(0)->getColumnDimension('C')->setWidth(40);  // Nama
            $object->getSheet(0)->getColumnDimension('D')->setWidth(20);  // Saldo

            // HILANGKAN GRIDLINES
            $sheet->setShowGridlines(false);

            // --- TABLE HEAD ---
            $table_head = array('No', 'Kode Acc', 'Nama Acc', 'Saldo');
            $column = 0;
            foreach ($table_head as $field) {
                $sheet->setCellValueByColumnAndRow($column, 5, $field);
                $column++;
            }

            // Style Header Tabel
            $sheet->getStyle('A5:D5')->applyFromArray([
                'font' => array('bold' => true),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'D3D3D3')
                ),
                'borders' => array(
                    'allborders' => array('style' => PHPExcel_Style_Border::BORDER_NONE), // Pastikan semua border mati dulu
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN) // Opsional: Beri garis bawah saja agar rapi
                )
            ]);



            // --- ISI DATA ---
            $rowCount = 6;
            $no = 1;



            // Cari Min Level untuk Spasi (agar dinamis seperti di JS)
            // $levels = array_column($records, 'level');
            // cari ututan level
            $uniqueLevels = array_unique(array_column($records, 'level'));
            sort($uniqueLevels); // Urutkan [1, 3, 5]
            // $minLevel = !empty($levels) ? min($levels) : 1;

            foreach ($records as $val) {
                // Cari urutan ke berapa level ini dalam daftar yang dipilih
                $levelOrder = array_search($val['level'], $uniqueLevels);
                $indentStr = str_repeat('    ', $levelOrder); // Indentasi berdasarkan urutan
                // Indentasi Nama Acc (menggunakan spasi manual di Excel)
                // $indentStr = str_repeat('    ', ($val['level'] - 1));
                $nama_acc = ($val['tipe'] == 'total') ? "TOTAL " . $val['nama_acc'] : $val['nama_acc'];

                // Isi Kolom
                // No hanya muncul di baris akun transaksi (Level 5) atau baris Header Utama
                $sheet->setCellValue('A' . $rowCount, ($val['tipe'] == 'row' && ($val['level'] == 5 || empty($val['saldo']))) ? $no++ : '');
                $sheet->setCellValueExplicit('B' . $rowCount, $val['kode_acc'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowCount, $indentStr . $nama_acc);

                // Logika Saldo: Hanya tampil di baris TOTAL atau Level 5
                if ($val['tipe'] == 'total' || $val['level'] == 5) {
                    $sheet->setCellValue('D' . $rowCount, $val['saldo']);
                }

                // --- STYLING WARNA PER LEVEL ---
                $color = '000000';
                if ($val['level'] == 1) $color = '437333';
                else if ($val['level'] == 2) $color = 'E78D2D';
                else if ($val['level'] == 3) $color = '2F5FB3';
                else if ($val['level'] == 4) $color = 'D42459';

                $styleRow = [
                    'font' => [
                        'color' => ['rgb' => $color],
                        'bold'  => ($val['level'] < 5 || $val['tipe'] == 'total'),
                        'italic' => ($val['tipe'] == 'total')
                    ]
                ];

                // Garis pembatas untuk baris Total
                if ($val['tipe'] == "total") {
                    $sheet->getStyle('B' . $rowCount . ':D' . $rowCount)->applyFromArray([
                        'borders' => [
                            'top' => [
                                'style' => PHPExcel_Style_Border::BORDER_THIN, // Garis biasa (single)
                                'color' => ['rgb' => '000000']
                            ]
                        ],
                        'font' => [
                            'italic' => true,
                            'bold' => true
                        ]
                    ]);
                }

                $sheet->getStyle('A' . $rowCount . ':D' . $rowCount)->applyFromArray($styleRow);
                $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00'); // Format angka ribuan

                $rowCount++;

                // --- LOGIKA JARAK (SPACER) ---
                if ($val['tipe'] == 'total' && $levelOrder === 0) {
                    $rowCount++; // Tambah 1 baris kosong
                }
            }

            // --- BARIS LABA BERSIH ---
            $sheet->setCellValue('A' . $rowCount, 'LABA / RUGI BERSIH');
            $sheet->mergeCells('A' . $rowCount . ':C' . $rowCount);
            $sheet->setCellValue('D' . $rowCount, $laba_bersih);
            $sheet->getStyle('A' . $rowCount . ':D' . $rowCount)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'F4F4F4']]
            ]);
            $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');

            // Autosize kolom agar rapi
            foreach (range('A', 'D') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $response =  array(
                'status'   => 'success',
                'file'     => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData),
                'filename' => 'Laba Rugi Standar   ' . $periode . '.xlsx'
            );

            die(json_encode($response));
        } catch (Exception $ex) {
            die(json_encode(['status' => 'failed', 'message' => $ex->getMessage()]));
        }
    }
}
