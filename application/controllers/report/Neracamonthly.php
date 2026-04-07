<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Neracamonthly extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->library('periodesaldo');
        $this->load->model('m_neraca');
    }

    public function index()
    {
        $id_dept        = 'RKNM';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/v_neraca_monthly', $data);
    }

    public function loadData()
    {
        $validation = [
            ['field' => 'tahun_dari', 'label' => 'Tahun Dari', 'rules' => 'required'],
            ['field' => 'tahun_sampai', 'label' => 'Tahun Sampai', 'rules' => 'required'],
            ['field' => 'bulan_dari', 'label' => 'Bulan Dari', 'rules' => 'required'],
            ['field' => 'bulan_sampai', 'label' => 'Bulan Sampai', 'rules' => 'required']
        ];

        try {
            $this->form_validation->set_rules($validation);
            if ($this->form_validation->run() == FALSE) {
                $callback = [
                    'status' => 'failed',
                    'message' => array_values($this->form_validation->error_array())[0],
                    'icon' => 'fa fa-warning',
                    'type' => 'danger'
                ];
            } else {
                // Panggil proses data khusus monthly
                $data = $this->proses_neraca_monthly();
                $callback = [
                    'status' => 'success',
                    'message' => 'berhasil',
                    'icon' => 'fa fa-check',
                    'type' => 'success',
                    'record' => $data
                ];
            }

            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($callback));
        } catch (Exception $ex) {
            $this->output->set_status_header(500)
                ->set_output(json_encode(['message' => $ex->getMessage()]));
        }
    }

    public function proses_neraca_monthly($filter_manual = null)
    {
        // 1. Pengaturan Filter
        if (empty($filter_manual)) {
            $tahun_dari    = $this->input->post('tahun_dari');
            $tahun_sampai  = $this->input->post('tahun_sampai');
            $bulan_dari    = (int)$this->input->post('bulan_dari');
            $bulan_sampai  = (int)$this->input->post('bulan_sampai');
            $levels        = $this->input->post('level');
            $hide_empty    = $this->input->post('checkhidden');
        } else {
            $tahun_dari    = $filter_manual[0]['tahun_dari'];
            $tahun_sampai  = $filter_manual[0]['tahun_sampai'];
            $bulan_dari    = (int)$filter_manual[0]['bulan_dari'];
            $bulan_sampai  = (int)$filter_manual[0]['bulan_sampai'];
            $levels        = $filter_manual[0]['level'] ?? [];
            $hide_empty    = $filter_manual[0]['checkhidden'] ?? false;
        }

        $max_level_selected = !empty($levels) ? max(array_map('intval', $levels)) : 5;
        $hide_empty = ($hide_empty === 'true' || $hide_empty === true || $hide_empty === '1');

        // 2. Buat Daftar Periode Kolom
        $period_list = [];
        $start_dt = new DateTime("$tahun_dari-$bulan_dari-01");
        $end_dt   = new DateTime("$tahun_sampai-$bulan_sampai-01");
        $interval = DateInterval::createFromDateString('1 month');
        $period   = new DatePeriod($start_dt, $interval, $end_dt->modify('+1 month'));

        foreach ($period as $dt) {
            $period_list[] = [
                'thn' => $dt->format("Y"),
                'bln' => (int)$dt->format("m"),
                'key' => "col_" . $dt->format("Y_m")
            ];
        }

        // 3. Query Saldo (Saldo Awal + Mutasi per Bulan)
        $case_months = "";
        foreach ($period_list as $p) {
            $case_months .= "SUM(CASE WHEN YEAR(je.tanggal_dibuat) = {$p['thn']} AND MONTH(je.tanggal_dibuat) = {$p['bln']} THEN (CASE WHEN jei.posisi='D' THEN jei.nominal ELSE -jei.nominal END) ELSE 0 END) AS {$p['key']}, ";
        }
        $case_months = rtrim($case_months, ", ");

        $tgl_awal_filter = $start_dt->format('Y-m-d');
        $sql_saldo = "
        SELECT jei.kode_coa, 
        SUM(CASE WHEN je.tanggal_dibuat < '$tgl_awal_filter' THEN (CASE WHEN jei.posisi='D' THEN jei.nominal ELSE -jei.nominal END) ELSE 0 END) AS saldo_awal,
        $case_months
        FROM acc_jurnal_entries je
        INNER JOIN acc_jurnal_entries_items jei ON je.kode = jei.kode
        WHERE je.status = 'posted' 
        AND je.tanggal_dibuat <= '" . $end_dt->format('Y-m-t') . "'
        GROUP BY jei.kode_coa";

        // $transaksi = $this->db->query($sql_saldo)->result_array();
        $transaksi = $this->m_neraca->get_list_neraca_monthly($start_dt, $end_dt,  $period_list)->result_array();
        $saldo_map = [];
        foreach ($transaksi as $t) {
            $saldo_map[$t['kode_coa']] = $t;
        }

        // 4. Ambil COA Neraca (Kepala 1, 2, 3)
        $all_coa = $this->db->query("SELECT kode_coa, nama, level, parent, saldo_normal 
                                 FROM acc_coa WHERE LEFT(kode_coa,1) <= '3' 
                                 ORDER BY kode_coa ASC")->result_array();

        // 5. Helper Kalkulasi Saldo Kumulatif per Kolom
        // $get_monthly_balance = function ($kode_coa, $saldo_normal) use ($saldo_map, $period_list) {
        //     $monthly_res = [];
        //     foreach ($period_list as $p) $monthly_res[$p['key']] = 0;

        //     foreach ($saldo_map as $kode_tr => $val) {
        //         if (strpos($kode_tr, $kode_coa) === 0) {
        //             $running_total = (float)$val['saldo_awal_finish'];
        //             foreach ($period_list as $p) {
        //                 $key = $p['key'];
        //                 $running_total += (float)($val[$key] ?? 0);

        //                 // Normalisasi Saldo Normal (Debit untuk Aset, Kredit untuk Liabilitas/Ekuitas)
        //                 $amt = ($val['saldo_normal'] == 'C') ? -$running_total : $running_total;
        //                 $monthly_res[$key] += $amt;
        //             }
        //         }
        //     }

        //     return ($saldo_normal == 'C') ? - $monthly_res : $monthly_res;
        // };

        $get_monthly_balance = function ($kode_coa_parent, $parent_saldo_normal) use ($saldo_map, $period_list) {
            // 1. Inisialisasi hasil per kolom bulan dalam basis Debit
            $monthly_debit_basis = [];
            foreach ($period_list as $p) {
                $monthly_debit_basis[$p['key']] = 0;
            }

            // 2. Iterasi map transaksi
            foreach ($saldo_map as $kode_tr => $val) {
                // Cek apakah akun ini adalah bagian dari parent (prefix match)
                if (strpos($kode_tr, $kode_coa_parent) === 0) {

                    // Ambil Saldo Awal Finish yang sudah dihitung di SQL (sudah termasuk SA DB + Jurnal Lalu)
                    // Kita standarisasi ke basis Debit berdasarkan saldo_normal akun spesifik tersebut
                    $current_sa = (float)($val['saldo_awal_finish'] ?? 0);
                    $current_normal = $val['saldo_normal'];

                    $running_raw = ($current_normal == 'C') ? -$current_sa : $current_sa;

                    foreach ($period_list as $p) {
                        $key = $p['key'];
                        // Ambil mutasi bulanan (di model kita sudah set: Debit - Kredit)
                        $mutasi_bulan_ini = (float)($val[$key] ?? 0);

                        // Akumulasikan ke running total (masih dalam basis Debit)
                        $running_raw += $mutasi_bulan_ini;

                        // Tambahkan hasil running total akun ini ke total kolektif parent
                        $monthly_debit_basis[$key] += $running_raw;
                    }
                }
            }

            // 3. Konversi hasil akhir kolektif sesuai Saldo Normal PARENT
            $final_res = [];
            foreach ($monthly_debit_basis as $key => $total_debit_basis) {
                if ($parent_saldo_normal == 'C') {
                    // Jika Parent (misal Kewajiban), balikkan agar saldo positif
                    $final_res[$key] = -$total_debit_basis;
                } else {
                    // Jika Parent (misal Aset), biarkan dalam basis Debit
                    $final_res[$key] = $total_debit_basis;
                }
            }

            return $final_res;
        };

        $results = [];
        $stack = [];
        $total_aset_monthly = array_fill(0, count($period_list), 0);
        $total_pasiva_monthly = array_fill(0, count($period_list), 0);
        $total_selisih_monthly = array_fill(0, count($period_list), 0);

        foreach ($all_coa as $index => $coa) {
            $balances = $get_monthly_balance($coa['kode_coa'], $coa['saldo_normal']);
            $prefix = substr($coa['kode_coa'], 0, 1);
            $next_coa = $all_coa[$index + 1] ?? null;

            // Hitung Total Aset (1) & Total Pasiva (2 & 3) hanya di Level 1
            if ($coa['level'] == 1) {
                $idx = 0;
                foreach ($balances as $val) {
                    if ($prefix == '1') {
                        $total_aset_monthly[$idx] += $val;
                    } else {
                        $total_pasiva_monthly[$idx] += $val;
                    }
                    $total_selisih_monthly[$idx] = $total_aset_monthly[$idx] - $total_pasiva_monthly[$idx];
                    $idx++;
                }
            }

            // Filter Tampilan
            $has_value = false;
            foreach ($balances as $v) {
                if (round($v, 2) != 0) {
                    $has_value = true;
                    break;
                }
            }
            $is_visible = (empty($levels) || in_array($coa['level'], $levels)) && !($hide_empty && !$has_value);

            if ($is_visible) {
                $results[] = [
                    "kode_acc" => $coa['kode_coa'],
                    "nama_acc" => $coa['nama'],
                    "level"    => (int)$coa['level'],
                    "monthly"  => ($coa['level'] == $max_level_selected || $coa['level'] == 5) ? array_values($balances) : array_fill(0, count($period_list), null),
                    "tipe"     => "row"
                ];

                if ($coa['level'] < $max_level_selected) {
                    array_push($stack, [
                        "nama"    => "TOTAL " . $coa['nama'],
                        "level"   => $coa['level'],
                        "monthly" => array_values($balances)
                    ]);
                }
            }

            // Baris Total per Group
            while (!empty($stack) && ($next_coa == null || $next_coa['level'] <= end($stack)['level'])) {
                $last_stack = array_pop($stack);
                $results[] = [
                    "kode_acc" => "",
                    "nama_acc" => $last_stack['nama'],
                    "level"    => (int)$last_stack['level'],
                    "monthly"  => $last_stack['monthly'],
                    "tipe"     => "total"
                ];
            }
        }

        return [
            "record" => $results,
            "total_aset" => $total_aset_monthly,
            "total_pasiva" => $total_pasiva_monthly,
            "total_selisih" => $total_selisih_monthly,
            "period_count" => count($period_list)
        ];
    }

    public function export_excel_monthly()
    {
        try {
            $this->load->library('excel');

            $arr_filter = $this->input->post('arr_filter');
            if (empty($arr_filter)) throw new Exception("Filter kosong.");

            $data_report = $this->proses_neraca_monthly($arr_filter);
            $records = $data_report['record'] ?? [];
            $total_aset = $data_report['total_aset'] ?? [];
            $total_pasiva = $data_report['total_pasiva'] ?? [];
            $total_selisih = $data_report['total_selisih'] ?? [];
            $period_count = $data_report['period_count'] ?? 1;

            $nama_bulan = get_bulan_indo();

            if (empty($records)) throw new Exception("Data tidak ditemukan.");

            ob_start();
            $object = new PHPExcel();
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Neraca Bulanan');

            // --- PREPARASI DATA PERIODE ---
            $tahun_dari = (int)$arr_filter[0]['tahun_dari'];
            $tahun_sampai = (int)$arr_filter[0]['tahun_sampai'];
            $bulan_dari = (int)$arr_filter[0]['bulan_dari'];
            $bulan_sampai = (int)$arr_filter[0]['bulan_sampai'];

            $periode_text = ($nama_bulan[$bulan_dari]) . " " . $tahun_dari . " - " . ($nama_bulan[$bulan_sampai]) . " " . $tahun_sampai;

            // --- HEADER LAPORAN ---
            $last_col_index = 2 + (int) $period_count;
            $last_col_letter = PHPExcel_Cell::stringFromColumnIndex($last_col_index);

            $sheet->setCellValue('A1', 'PT. HEKSATEX INDAH');
            $sheet->setCellValue('A2', 'LAPORAN NERACA (BULANAN)');
            $sheet->setCellValue('A3', "Periode: {$periode_text}");

            $sheet->mergeCells("A1:{$last_col_letter}1");
            $sheet->mergeCells("A2:{$last_col_letter}2");
            $sheet->mergeCells("A3:{$last_col_letter}3");

            $sheet->getStyle("A1:A3")->getFont()->setBold(true);
            // $sheet->getStyle("A1:{$last_col_letter}3")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // Hilangkan Gridlines agar bersih
            $sheet->setShowGridlines(false);

            // Lebar Kolom
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(18);
            $sheet->getColumnDimension('C')->setWidth(40);

            // --- TABLE HEAD ---
            $sheet->setCellValue('A5', 'No');
            $sheet->setCellValue('B5', 'Kode Acc');
            $sheet->setCellValue('C5', 'Nama Acc');

            $col_idx = 3;
            for ($t = $tahun_dari; $t <= $tahun_sampai; $t++) {
                $startM = ($t == $tahun_dari) ? $bulan_dari : 1;
                $endM = ($t == $tahun_sampai) ? $bulan_sampai : 12;
                for ($m = $startM; $m <= $endM; $m++) {
                    $cell_ref = PHPExcel_Cell::stringFromColumnIndex($col_idx);
                    $sheet->setCellValue($cell_ref . '5', ($nama_bulan[$m] ?? $m) . ' ' . $t);
                    $sheet->getColumnDimension($cell_ref)->setWidth(18);
                    $col_idx++;
                }
            }

            $sheet->getStyle("A5:{$last_col_letter}5")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'D3D3D3']],
                'borders' => [
                    // 'top'    => ['style' => PHPExcel_Style_Border::BORDER_THIN],
                    'bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
                ]
            ]);

            // --- ISI DATA ---
            // --- ISI DATA ---
            $rowCount = 6;
            $no = 1;
            foreach ($records as $val) {
                // 1. Indentasi
                $indentStr = str_repeat('    ', $val['level'] - 1);

                // 2. Isi Identitas Akun
                // Jika baris total, biasanya No tidak perlu diisi
                $sheet->setCellValue('A' . $rowCount, ($val['kode_acc'] != "" ? $no++ : ""));
                $sheet->setCellValueExplicit('B' . $rowCount, $val['kode_acc'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowCount, $indentStr . $val['nama_acc']);

                // 3. Isi Nilai (Loop Kolom Periode)
                $col_data_idx = 3;
                // Gunakan 'yearly' jika ini fungsi tahunan, atau 'monthly' jika bulanan
                $data_values = isset($val['yearly']) ? $val['yearly'] : $val['monthly'];

                foreach ($data_values as $saldo) {
                    $cell_ref = PHPExcel_Cell::stringFromColumnIndex($col_data_idx);
                    if ($saldo !== null) {
                        $sheet->setCellValue($cell_ref . $rowCount, $saldo);
                        $sheet->getStyle($cell_ref . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                    $col_data_idx++;
                }

                // 4. Penentuan Style (Warna & Bold)
                $color = '000000';
                $isBold = false;
                $isItalic = false;

                if ($val['level'] == 1) {
                    $color = '437333';
                    $isBold = true;
                } else if ($val['level'] == 2) {
                    $color = 'E78D2D';
                    $isBold = true;
                } else if ($val['level'] == 3) {
                    $color = '2F5FB3';
                    $isBold = true;
                } else if ($val['level'] == 4) {
                    $color = 'D42459';
                    $isBold = true;
                } else if ($val['level'] == 5) {
                    $isBold = ($val['tipe'] == "total");
                }

                if ($val['tipe'] == "total") {
                    $isItalic = true;
                    // Tambahkan border top double atau thin
                    $sheet->getStyle("B$rowCount:{$last_col_letter}$rowCount")->applyFromArray([
                        'borders' => ['top' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
                    ]);
                }

                // Apply Style ke seluruh baris
                $sheet->getStyle("A$rowCount:$last_col_letter$rowCount")->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => $color],
                        'bold'  => $isBold,
                        'italic' => $isItalic
                    ]
                ]);

                // 5. MANAJEMEN BARIS (ROW CONTROL)
                $rowCount++; // Pindah ke baris berikutnya untuk data selanjutnya

                // Tambahkan spacer 1 baris HANYA setelah TOTAL Level 1 (Selesai Blok Aset, Kewajiban, atau Modal)
                if ($val['tipe'] == 'total' && $val['level'] == 1) {
                    $rowCount++;
                }
            }

            // --- FOOTER (Grand Total) ---
            $rowCount++;
            $renderFooter = function ($sheet, $row, $label, $data, $color, $last_col_letter) {
                $sheet->setCellValue('A' . $row, $label);
                $sheet->mergeCells("A$row:C$row");
                $sheet->getStyle("A$row:$last_col_letter$row")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => $color]],
                    'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID,],
                    'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
                ]);
                $c_idx = 3;
                foreach ($data as $v) {
                    $c_ref = PHPExcel_Cell::stringFromColumnIndex($c_idx);
                    $sheet->setCellValue($c_ref . $row, $v);
                    $sheet->getStyle($c_ref . $row)->getNumberFormat()->setFormatCode('#,##0.00');
                    $c_idx++;
                }
            };

            $renderFooter($sheet, $rowCount++, "TOTAL ASSET", $total_aset, "", $last_col_letter);
            $renderFooter($sheet, $rowCount++, "TOTAL KEWAJIBAN & MODAL", $total_pasiva, "", $last_col_letter);
            $renderFooter($sheet, $rowCount++, "SELISIH", $total_selisih, "", $last_col_letter);

            // --- OUTPUT ---
            $writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            die(json_encode([
                'status' => 'success',
                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData),
                'filename' => 'Neraca Bulanan ' . $periode_text . '.xlsx'
            ]));
        } catch (Exception $ex) {
            die(json_encode(['status' => 'failed', 'message' => $ex->getMessage()]));
        }
    }
}
