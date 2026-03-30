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
            ['field' => 'tahun', 'label' => 'Tahun', 'rules' => 'required'],
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
            $tahun        = $this->input->post('tahun');
            $bulan_dari   = (int)$this->input->post('bulan_dari');
            $bulan_sampai = (int)$this->input->post('bulan_sampai');
            $levels       = $this->input->post('level');
            $hide_empty   = $this->input->post('checkhidden');
        } else {
            $tahun        = $filter_manual[0]['tahun'];
            $bulan_dari   = (int)$filter_manual[0]['bulan_dari'];
            $bulan_sampai = (int)$filter_manual[0]['bulan_sampai'];
            $levels       = $filter_manual[0]['level'] ?? [];
            $hide_empty   = $filter_manual[0]['checkhidden'] ?? false;
        }

        $hide_empty = ($hide_empty === 'true' || $hide_empty === true || $hide_empty === '1');

        // 1. Query Saldo PIVOT (Jan s/d Des)
        $case_months = "";
        for ($i = $bulan_dari; $i <= $bulan_sampai; $i++) {
            $case_months .= "SUM(CASE WHEN MONTH(je.tanggal_dibuat) = $i THEN (CASE WHEN jei.posisi='D' THEN jei.nominal ELSE -jei.nominal END) ELSE 0 END) AS bulan_$i, ";
        }
        $case_months = rtrim($case_months, ", ");

        $sql_saldo = "
        SELECT jei.kode_coa, $case_months
        FROM acc_jurnal_entries je
        INNER JOIN acc_jurnal_entries_items jei ON je.kode = jei.kode
        WHERE je.status = 'posted' AND YEAR(je.tanggal_dibuat) = '$tahun'
        GROUP BY jei.kode_coa";

        $transaksi = $this->db->query($sql_saldo)->result_array();

        $saldo_map = [];
        foreach ($transaksi as $t) {
            $saldo_map[$t['kode_coa']] = $t;
        }

        // 2. Ambil COA
        $all_coa = $this->db->query("SELECT kode_coa, nama, level, parent, saldo_normal 
                                 FROM acc_coa WHERE LEFT(kode_coa,1) >= '4' 
                                 ORDER BY kode_coa ASC")->result_array();

        // Helper untuk menjumlahkan saldo bulanan (termasuk anak-anaknya)
        $get_monthly_balance = function ($kode_coa, $saldo_normal) use ($saldo_map, $bulan_dari, $bulan_sampai) {
            $monthly_res = array_fill($bulan_dari, ($bulan_sampai - $bulan_dari + 1), 0);
            foreach ($saldo_map as $kode_tr => $val) {
                if (strpos($kode_tr, $kode_coa) === 0) {
                    for ($m = $bulan_dari; $m <= $bulan_sampai; $m++) {
                        $amt = (float)$val["bulan_$m"];
                        // Logika: Jika saldo normal D, maka D-C. Jika C, maka C-D.
                        // Di query SQL Anda: D = nominal, C = -nominal.
                        // Jadi: Jika D => tetap $amt. Jika C => -$amt.
                        if ($saldo_normal == 'D') {
                            $monthly_res[$m] += $amt;
                        } else {
                            $monthly_res[$m] += -$amt;
                        }
                    }
                }
            }
            return $monthly_res;
        };

        $results = [];
        $laba_bersih_monthly = array_fill($bulan_dari, ($bulan_sampai - $bulan_dari + 1), 0);
        $stack = [];

        foreach ($all_coa as $index => $coa) {
            $monthly_balances = $get_monthly_balance($coa['kode_coa'], $coa['saldo_normal']);

            // Akumulasi Laba Bersih per bulan
            if ($coa['level'] == 1) {
                $prefix = substr($coa['kode_coa'], 0, 1);
                foreach ($monthly_balances as $m => $val) {
                    if ($prefix == '4') {
                        // Jika Pendapatan, maka menambah laba
                        $laba_bersih_monthly[$m] += $val;
                    } else {
                        // Jika Beban (kepala 5 keatas), maka mengurangi laba
                        $laba_bersih_monthly[$m] -= $val;
                    }
                }
            }

            $next_coa = $all_coa[$index + 1] ?? null;

            // Cek visibilitas (jika ada salah satu bulan yang tidak nol)
            $is_empty = array_sum($monthly_balances) == 0;
            $is_visible = (empty($levels) || in_array($coa['level'], $levels)) && !($hide_empty && $is_empty);

            if ($is_visible) {
                $results[] = [
                    "kode_acc" => $coa['kode_coa'],
                    "nama_acc" => $coa['nama'],
                    "level"    => (int)$coa['level'],
                    "monthly"  => ($coa['level'] > 4) ? $monthly_balances : array_fill($bulan_dari, count($monthly_balances), null),
                    "tipe"     => "row"
                ];

                if ($coa['level'] < 5) {
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

            // Pastikan memanggil proses_data_monthly
            $data_report = $this->proses_data_monthly($arr_filter);
            $records = $data_report['record'];
            $laba_bersih_m = $data_report['laba_bersih_monthly'];

            if (empty($records)) {
                throw new Exception("Data tidak ditemukan untuk periode tersebut.");
            }

            // Ambil filter untuk judul & periode
            $tahun = $arr_filter[0]['tahun'];
            $bulan_dari = (int)$arr_filter[0]['bulan_dari'];
            $bulan_sampai = (int)$arr_filter[0]['bulan_sampai'];
            $nama_bulan_indo = get_bulan_indo();

            $periode = $nama_bulan_indo[$bulan_dari] . ' - ' . $nama_bulan_indo[$bulan_sampai] . ' ' . $tahun;

            ob_start();
            $object = new PHPExcel();
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Laba Rugi Bulanan');

            // --- HEADER LAPORAN ---
            $sheet->setCellValue('A1', 'PT. HEKSATEX INDAH');
            $sheet->setCellValue('A2', 'LABA RUGI (MONTHLY)');
            $sheet->setCellValue('A3', 'Periode: ' . $periode);
            $sheet->mergeCells('A1:D1');
            $sheet->mergeCells('A2:D2');
            $sheet->mergeCells('A3:D3');
            $object->getActiveSheet()->getStyle("A1:A3")->getFont()->setBold(true);

            // Hilangkan Gridlines agar bersih
            $sheet->setShowGridlines(false);

            // --- TABLE HEAD ---
            $table_head_statis = array('No', 'Kode Acc', 'Nama Acc');
            $column = 0;
            foreach ($table_head_statis as $field) {
                $sheet->setCellValueByColumnAndRow($column, 5, $field);
                $column++;
            }

            // Tambah Header Bulan Dinamis mulai dari kolom ke-4 (Indeks 3)
            for ($i = $bulan_dari; $i <= $bulan_sampai; $i++) {
                $sheet->setCellValueByColumnAndRow($column, 5, $nama_bulan_indo[$i] .' ' .$tahun );
                $column++;
            }

            $last_col_index = $column - 1;
            $last_col_letter = PHPExcel_Cell::stringFromColumnIndex($last_col_index);

            // Style Header Tabel (Warna D3D3D3 sesuai standar Anda)
            $sheet->getStyle("A5:{$last_col_letter}5")->applyFromArray([
                'font' => array('bold' => true),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'D3D3D3')
                ),
                'borders' => array(
                    'bottom' => array('style' => PHPExcel_Style_Border::BORDER_THIN)
                )
            ]);

            // --- ISI DATA ---
            $rowCount = 6;
            $no = 1;
            $uniqueLevels = array_unique(array_column($records, 'level'));
            sort($uniqueLevels);

            foreach ($records as $val) {
                $levelOrder = array_search($val['level'], $uniqueLevels);
                $indentStr = str_repeat('    ', $levelOrder);
                $nama_acc = ($val['tipe'] == 'total') ? "TOTAL " . $val['nama_acc'] : $val['nama_acc'];

                // Kolom Statis
                $sheet->setCellValue('A' . $rowCount, $no++);
                $sheet->setCellValueExplicit('B' . $rowCount, $val['kode_acc'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowCount, $indentStr . $nama_acc);

                // Kolom Saldo Dinamis (Looping Bulan)
                $col_saldo = 3;
                for ($m = $bulan_dari; $m <= $bulan_sampai; $m++) {
                    $saldo = $val['monthly'][$m];
                    // Tampilkan angka jika baris total atau level detail (>=4)
                    if ($val['tipe'] == 'total' || $val['level'] >= 4) {
                        $sheet->setCellValueByColumnAndRow($col_saldo, $rowCount, $saldo);
                    }
                    $col_saldo++;
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
                    $start_total_col = PHPExcel_Cell::stringFromColumnIndex(1); // Kolom B
                    $sheet->getStyle($start_total_col . $rowCount . ':' . $last_col_letter . $rowCount)->applyFromArray([
                        'borders' => [
                            'top' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
                        ]
                    ]);
                }

                $sheet->getStyle("A{$rowCount}:{$last_col_letter}{$rowCount}")->applyFromArray($styleRow);

                // Format Angka untuk semua kolom saldo
                $first_data_col = PHPExcel_Cell::stringFromColumnIndex(3);
                $sheet->getStyle("{$first_data_col}{$rowCount}:{$last_col_letter}{$rowCount}")
                    ->getNumberFormat()->setFormatCode('#,##0.00');

                $rowCount++;

                // Spasi Dinamis
                if ($val['tipe'] == 'total' && $levelOrder === 0) {
                    $rowCount++;
                }
            }

            // --- BARIS LABA BERSIH ---
            $sheet->setCellValue('A' . $rowCount, 'LABA / RUGI BERSIH');
            $sheet->mergeCells("A{$rowCount}:C{$rowCount}");

            $col_laba = 3;
            for ($m = $bulan_dari; $m <= $bulan_sampai; $m++) {
                $sheet->setCellValueByColumnAndRow($col_laba, $rowCount, $laba_bersih_m[$m]);
                $col_laba++;
            }

            $sheet->getStyle("A{$rowCount}:{$last_col_letter}{$rowCount}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'F4F4F4']]
            ]);
            $sheet->getStyle(PHPExcel_Cell::stringFromColumnIndex(3) . $rowCount . ':' . $last_col_letter . $rowCount)
                ->getNumberFormat()->setFormatCode('#,##0.00');

            // Autosize kolom A, B, C
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);

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
