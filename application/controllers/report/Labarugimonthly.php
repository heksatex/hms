<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Labarugimonthly extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
    }

    public function index()
    {
        $id_dept        = 'RKLRM';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/v_laba_rugi_monthly', $data);
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
                $data = $this->proses_data_monthly();
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


    public function proses_data_monthly($filter_manual = null)
    {
        if (empty($filter_manual)) {
            $tahun_dari   = $this->input->post('tahun_dari');
            $tahun_sampai = $this->input->post('tahun_sampai');
            $bulan_dari   = (int)$this->input->post('bulan_dari');
            $bulan_sampai = (int)$this->input->post('bulan_sampai');
            $levels       = $this->input->post('level');
            $hide_empty   = $this->input->post('checkhidden');
        } else {
            $tahun_dari   = $filter_manual[0]['tahun_dari'];
            $tahun_sampai = $filter_manual[0]['tahun_sampai'];
            $bulan_dari   = (int)$filter_manual[0]['bulan_dari'];
            $bulan_sampai = (int)$filter_manual[0]['bulan_sampai'];
            $levels       = $filter_manual[0]['level'] ?? [];
            $hide_empty   = $filter_manual[0]['checkhidden'] ?? false;
        }

        $max_level_selected = !empty($levels) ? max(array_map('intval', $levels)) : 5;
        $hide_empty = ($hide_empty === 'true' || $hide_empty === true || $hide_empty === '1');

        // --- 1. MEMBUAT LIST PERIODE (Bulan & Tahun) ---
        $periodes = [];
        $start = new DateTime("$tahun_dari-$bulan_dari-01");
        $end   = new DateTime("$tahun_sampai-$bulan_sampai-01");
        $interval = new DateInterval('P1M');
        $daterange = new DatePeriod($start, $interval, $end->modify('+1 month'));

        foreach ($daterange as $date) {
            $periodes[] = [
                'm' => $date->format('n'),
                't' => $date->format('Y'),
                'key' => $date->format('Y-n') // Format "2026-4"
            ];
        }

        // --- 2. QUERY SALDO PIVOT DINAMIS ---
        $case_months = "";
        foreach ($periodes as $p) {
            $m = $p['m'];
            $t = $p['t'];
            $keyAlias = "col_" . $p['key']; // Alias kolom tidak boleh pakai "-"
            $case_months .= "SUM(CASE WHEN MONTH(je.tanggal_dibuat) = $m AND YEAR(je.tanggal_dibuat) = $t THEN (CASE WHEN jei.posisi='C' THEN jei.nominal ELSE -jei.nominal END) ELSE 0 END) AS `$keyAlias`, ";
        }
        $case_months = rtrim($case_months, ", ");

        $sql_saldo = "
        SELECT jei.kode_coa, $case_months
        FROM acc_jurnal_entries je
        INNER JOIN acc_jurnal_entries_items jei ON je.kode = jei.kode
        WHERE je.status = 'posted' 
        AND je.tanggal_dibuat BETWEEN '{$start->format('Y-m-01')}' AND '{$end->format('Y-m-t')}'
        GROUP BY jei.kode_coa";

        $transaksi = $this->db->query($sql_saldo)->result_array();

        $saldo_map = [];
        foreach ($transaksi as $t) {
            $saldo_map[$t['kode_coa']] = $t;
        }

        // --- 3. AMBIL COA ---
        $all_coa = $this->db->query("SELECT kode_coa, nama, level, parent, saldo_normal 
                                 FROM acc_coa WHERE LEFT(kode_coa,1) >= '4' 
                                 ORDER BY kode_coa ASC")->result_array();

        // Helper Saldo Bulanan
        $get_monthly_balance = function ($kode_coa) use ($saldo_map, $periodes) {
            $monthly_res = [];
            foreach ($periodes as $p) {
                $monthly_res[$p['key']] = 0; // Inisialisasi format "2026-4" => 0
            }

            foreach ($saldo_map as $kode_tr => $val) {
                if (strpos($kode_tr, $kode_coa) === 0) {
                    foreach ($periodes as $p) {
                        $keyAlias = "col_" . $p['key'];
                        $monthly_res[$p['key']] += (float)$val[$keyAlias];
                    }
                }
            }
            return $monthly_res;
        };

        $results = [];
        $laba_bersih_monthly = [];
        foreach ($periodes as $p) {
            $laba_bersih_monthly[$p['key']] = 0;
        }

        $stack = [];

        foreach ($all_coa as $index => $coa) {
            $monthly_balances = $get_monthly_balance($coa['kode_coa']);

            // Akumulasi Laba Bersih (Hanya Level 1 agar tidak double count)
            if ($coa['level'] == 1) {
                foreach ($monthly_balances as $keyDate => $val) {
                    $laba_bersih_monthly[$keyDate] += $val;
                }
            }

            $next_coa = $all_coa[$index + 1] ?? null;

            $has_value = false;
            foreach ($monthly_balances as $v) {
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
                    "monthly"  => ($coa['level'] == $max_level_selected || $coa['level'] == 5) ? $monthly_balances : array_fill_keys(array_column($periodes, 'key'), null),
                    "tipe"     => "row"
                ];

                if ($coa['level'] < $max_level_selected) {
                    array_push($stack, [
                        "nama"    => "TOTAL " . $coa['nama'],
                        "level"   => $coa['level'],
                        "monthly" => $monthly_balances
                    ]);
                }
            }

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
            "laba_bersih_monthly" => $laba_bersih_monthly
        ];
    }

    public function export_excel()
    {
        try {
            $this->load->library('excel');
            $arr_filter = $this->input->post('arr_filter');
            if (empty($arr_filter)) throw new Exception("Filter kosong.");

            // 1. Ambil Data (Gunakan fungsi proses tahunan yang baru)
            $data_report = $this->proses_data_monthly($arr_filter);
            $records = $data_report['record'];
            $laba_bersih_m = $data_report['laba_bersih_monthly'];

            if (empty($records)) {
                throw new Exception("Data tidak ditemukan untuk periode tersebut.");
            }

            // 2. Ambil Filter (Sesuaikan key lintas tahun)
            $thn_dari = $arr_filter[0]['tahun_dari'];
            $thn_sampai = $arr_filter[0]['tahun_sampai'];
            $bln_dari = (int)$arr_filter[0]['bulan_dari'];
            $bln_sampai = (int)$arr_filter[0]['bulan_sampai'];
            $nama_bulan_indo = get_bulan_indo();

            // Objek DateTime untuk iterasi lintas tahun yang aman
            $startDate = new DateTime("$thn_dari-$bln_dari-01");
            $endDate   = new DateTime("$thn_sampai-$bln_sampai-01");

            $periode = $nama_bulan_indo[$bln_dari] . ' ' . $thn_dari . ' - ' . $nama_bulan_indo[$bln_sampai] . ' ' . $thn_sampai;

            ob_start();
            $object = new PHPExcel();
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Laba Rugi Bulanan');

            // --- HEADER LAPORAN (Sesuaikan Merge) ---
            // Hitung last column letter dulu untuk merge
            $col_count = 3; // No, Kode, Nama
            $curr = clone $startDate;
            while ($curr <= $endDate) {
                $col_count++;
                $curr->modify('+1 month');
            }
            $last_col_letter = PHPExcel_Cell::stringFromColumnIndex($col_count - 1);

            $sheet->setCellValue('A1', 'PT. HEKSATEX INDAH');
            $sheet->setCellValue('A2', 'LABA RUGI (MONTHLY)');
            $sheet->setCellValue('A3', 'Periode: ' . $periode);
            $sheet->mergeCells("A1:{$last_col_letter}1");
            $sheet->mergeCells("A2:{$last_col_letter}2");
            $sheet->mergeCells("A3:{$last_col_letter}3");
            $sheet->getStyle("A1:A3")->getFont()->setBold(true);

            // Hilangkan Gridlines agar bersih
            $sheet->setShowGridlines(false);

            // Lebar Kolom Statis (Sesuai Monthly)
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(18);
            $sheet->getColumnDimension('C')->setWidth(40);

            // --- TABLE HEAD ---
            $table_head_statis = array('No', 'Kode Acc', 'Nama Acc');
            $column = 0;
            foreach ($table_head_statis as $field) {
                $sheet->setCellValueByColumnAndRow($column, 5, $field);
                $column++;
            }

            // Header Bulan Dinamis (Gunakan Iterasi Date)
            $curr = clone $startDate;
            while ($curr <= $endDate) {
                $m = (int)$curr->format('n');
                $t = $curr->format('Y');
                $header_text = $nama_bulan_indo[$m] . ' ' . $t;
                $cell_ref = PHPExcel_Cell::stringFromColumnIndex($column);
                $sheet->setCellValue($cell_ref . '5', $header_text);
                $sheet->getColumnDimension($cell_ref)->setWidth(18); // Lebar kolom saldo
                $column++;
                $curr->modify('+1 month');
            }

            // Style Header Tabel (Sesuai Monthly: D3D3D3)
            $sheet->getStyle("A5:{$last_col_letter}5")->applyFromArray([
                'font' => array('bold' => true),
                'alignment' => array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER),
                'fill' => array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => array('rgb' => 'D3D3D3')),
                'borders' => array('bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN))
            ]);

            // --- ISI DATA ---
            $rowCount = 6;
            $no = 1;

            // Ambil list level unik untuk indentasi dinamis
            $uniqueLevels = array_unique(array_column($records, 'level'));
            sort($uniqueLevels);

            foreach ($records as $val) {
                $levelOrder = array_search($val['level'], $uniqueLevels);
                $indentStr = str_repeat('    ', $levelOrder); // 4 spasi

                // Kolom Statis
                $sheet->setCellValue('A' . $rowCount, $no++);
                $sheet->setCellValueExplicit('B' . $rowCount, $val['kode_acc'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowCount, $indentStr . $val['nama_acc']);

                // Kolom Saldo Dinamis (Gunakan Iterasi Date & Key Tahun-Bulan)
                $col_saldo = 3;
                $curr = clone $startDate;
                while ($curr <= $endDate) {
                    $keyDate = $curr->format('Y-n'); // Format: 2026-4
                    $saldo = isset($val['monthly'][$keyDate]) ? $val['monthly'][$keyDate] : null;

                    // Tampilkan angka hanya jika baris Total atau Level detail (sesuai backend)
                    if ($saldo !== null) {
                        $sheet->setCellValueByColumnAndRow($col_saldo, $rowCount, $saldo);
                        $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_saldo) . $rowCount)
                            ->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                    $col_saldo++;
                    $curr->modify('+1 month');
                }

                // --- STYLING WARNA PER LEVEL (Sesuai Monthly) ---
                $color = '000000';
                $isBold = false;
                if ($val['level'] == 1) {
                    $color = '437333';
                    $isBold = true;
                } // Hijau
                else if ($val['level'] == 2) {
                    $color = 'E78D2D';
                    $isBold = true;
                } // Oranye
                else if ($val['level'] == 3) {
                    $color = '2F5FB3';
                    $isBold = true;
                } // Biru
                else if ($val['level'] == 4) {
                    $color = 'D42459';
                    $isBold = true;
                } // Pink/Pink
                else if ($val['tipe'] == "total") {
                    $isBold = true;
                }

                $styleRow = [
                    'font' => [
                        'color' => ['rgb' => $color],
                        'bold'  => $isBold,
                        'italic' => ($val['tipe'] == "total")
                    ]
                ];

                // Garis pembatas (Border Top Double) untuk baris Total (Sesuai Monthly)
                if ($val['tipe'] == "total") {
                    $styleRow['fill'] = ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'FDFDFD']];
                    $sheet->getStyle("B$rowCount:{$last_col_letter}$rowCount")->applyFromArray([
                        'borders' => [
                            // Gunakan  agar identik dengan CSS Monthly
                            'top' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
                        ]
                    ]);
                }

                $sheet->getStyle("A$rowCount:{$last_col_letter}$rowCount")->applyFromArray($styleRow);

                $rowCount++;

                // Spasi Dinamis (Setelah Total Level 1)
                if ($val['tipe'] == 'total' && $levelOrder === 0) {
                    $rowCount++;
                }
            }

            // --- BARIS LABA BERSIH (FOOTER) ---
            $rowCount++; // Jarak sebelum footer
            $sheet->setCellValue('A' . $rowCount, 'LABA / RUGI BERSIH');
            $sheet->mergeCells("A{$rowCount}:C{$rowCount}");
            // $sheet->getStyle("A$rowCount")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $col_laba = 3;
            $curr = clone $startDate;
            while ($curr <= $endDate) {
                $keyDate = $curr->format('Y-n');
                $laba_m = isset($laba_bersih_m[$keyDate]) ? $laba_bersih_m[$keyDate] : 0;
                $sheet->setCellValueByColumnAndRow($col_laba, $rowCount, $laba_m);

                // Format angka untuk laba bersih
                $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex($col_laba) . $rowCount)
                    ->getNumberFormat()->setFormatCode('#,##0.00');

                $col_laba++;
                $curr->modify('+1 month');
            }

            // Style Footer Laba Bersih (Sesuai Monthly: Bold & BG F4F4F4)
            $sheet->getStyle("A{$rowCount}:{$last_col_letter}{$rowCount}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID], 
                'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
            ]);

            // --- OUTPUT ---
            $writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            die(json_encode([
                'status' => 'success',
                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData),
                'filename' => 'Laba Rugi Bulanan ' . $periode . '.xlsx'
            ]));
        } catch (Exception $ex) {
            die(json_encode(['status' => 'failed', 'message' => $ex->getMessage()]));
        }
    }

  
}
