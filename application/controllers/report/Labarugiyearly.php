<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Labarugiyearly extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
    }

    public function index()
    {
        $id_dept        = 'RKLRY';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/v_laba_rugi_yearly', $data);
    }

    public function loadData()
    {
        $validation = [
            ['field' => 'tahun_dari', 'label' => 'Tahun Dari', 'rules' => 'required|numeric'],
            ['field' => 'tahun_sampai', 'label' => 'Tahun Sampai', 'rules' => 'required|numeric']
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
                // Panggil proses data khusus yearly
                $data = $this->proses_data_yearly();
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


    public function proses_data_yearly($filter_manual = null)
    {
        if (empty($filter_manual)) {
            $tahun_dari   = (int)$this->input->post('tahun_dari');
            $tahun_sampai = (int)$this->input->post('tahun_sampai');
            $levels       = $this->input->post('level');
            $hide_empty   = $this->input->post('hidden_check'); // Sesuaikan dengan name di HTML
        } else {
            $tahun_dari   = (int)$filter_manual[0]['tahun_dari'];
            $tahun_sampai = (int)$filter_manual[0]['tahun_sampai'];
            $levels       = $filter_manual[0]['level'] ?? [];
            $hide_empty   = $filter_manual[0]['checkhidden'] ?? false;
        }

        $hide_empty = ($hide_empty === 'true' || $hide_empty === true || $hide_empty === '1' || $hide_empty === 'on');

        // 1. Query Saldo PIVOT Berdasarkan Tahun
        $case_years = "";
        for ($t = $tahun_dari; $t <= $tahun_sampai; $t++) {
            $case_years .= "SUM(CASE WHEN YEAR(je.tanggal_dibuat) = $t THEN (CASE WHEN jei.posisi='D' THEN jei.nominal ELSE -jei.nominal END) ELSE 0 END) AS tahun_$t, ";
        }
        $case_years = rtrim($case_years, ", ");

        $sql_saldo = "
        SELECT jei.kode_coa, $case_years
        FROM acc_jurnal_entries je
        INNER JOIN acc_jurnal_entries_items jei ON je.kode = jei.kode
        WHERE je.status = 'posted' 
        AND YEAR(je.tanggal_dibuat) BETWEEN $tahun_dari AND $tahun_sampai
        GROUP BY jei.kode_coa";

        $transaksi = $this->db->query($sql_saldo)->result_array();

        $saldo_map = [];
        foreach ($transaksi as $tr) {
            $saldo_map[$tr['kode_coa']] = $tr;
        }

        // 2. Ambil COA (Kepala 4 ke atas untuk Laba Rugi)
        $all_coa = $this->db->query("SELECT kode_coa, nama, level, parent, saldo_normal 
                                 FROM acc_coa WHERE LEFT(kode_coa,1) >= '4' 
                                 ORDER BY kode_coa ASC")->result_array();

        // Helper untuk menjumlahkan saldo tahunan
        $get_yearly_balance = function ($kode_coa, $saldo_normal) use ($saldo_map, $tahun_dari, $tahun_sampai) {
            $yearly_res = [];
            for ($t = $tahun_dari; $t <= $tahun_sampai; $t++) {
                $yearly_res[$t] = 0;
            }

            foreach ($saldo_map as $kode_tr => $val) {
                if (strpos($kode_tr, $kode_coa) === 0) {
                    for ($t = $tahun_dari; $t <= $tahun_sampai; $t++) {
                        $amt = (float)$val["tahun_$t"];
                        // Jika saldo normal D (Beban), maka D-C (positif). Jika C (Pendapatan), maka C-D (dibalik).
                        $yearly_res[$t] += ($saldo_normal == 'D') ? $amt : -$amt;
                    }
                }
            }
            return $yearly_res;
        };

        $results = [];
        $laba_bersih_yearly = [];
        for ($t = $tahun_dari; $t <= $tahun_sampai; $t++) {
            $laba_bersih_yearly[$t] = 0;
        }

        $stack = [];

        foreach ($all_coa as $index => $coa) {
            $yearly_balances = $get_yearly_balance($coa['kode_coa'], $coa['saldo_normal']);

            // Akumulasi Laba Bersih per tahun (Hanya Level 1 agar tidak double count)
            if ($coa['level'] == 1) {
                $prefix = substr($coa['kode_coa'], 0, 1);
                foreach ($yearly_balances as $t => $val) {
                    if ($prefix == '4') {
                        $laba_bersih_yearly[$t] += $val; // Pendapatan menambah laba
                    } else {
                        $laba_bersih_yearly[$t] -= $val; // Beban mengurangi laba
                    }
                }
            }

            $next_coa = $all_coa[$index + 1] ?? null;

            // Cek visibilitas
            $is_empty = array_sum($yearly_balances) == 0;
            $is_visible = (empty($levels) || in_array($coa['level'], $levels)) && !($hide_empty && $is_empty);

            if ($is_visible) {
                $results[] = [
                    "kode_acc" => $coa['kode_coa'],
                    "nama_acc" => $coa['nama'],
                    "level"    => (int)$coa['level'],
                    "yearly"   => ($coa['level'] > 4) ? $yearly_balances : array_fill($tahun_dari, ($tahun_sampai - $tahun_dari + 1), null),
                    "tipe"     => "row"
                ];

                if ($coa['level'] < 5) {
                    array_push($stack, [
                        "nama"    => "TOTAL " . $coa['nama'],
                        "level"   => $coa['level'],
                        "yearly"  => $yearly_balances
                    ]);
                }
            }

            // Pop stack untuk Totaling
            while (!empty($stack) && ($next_coa == null || $next_coa['level'] <= end($stack)['level'])) {
                $last_stack = array_pop($stack);
                $results[] = [
                    "kode_acc" => "",
                    "nama_acc" => $last_stack['nama'],
                    "level"    => (int)$last_stack['level'],
                    "yearly"   => $last_stack['yearly'],
                    "tipe"     => "total"
                ];
            }
        }

        return [
            "record" => $results,
            "laba_bersih_yearly" => $laba_bersih_yearly,
            "tahun_range" => range($tahun_dari, $tahun_sampai) // Untuk header table di frontend
        ];
    }

    public function export_excel()
    {
        try {
            $this->load->library('excel');
            $arr_filter = $this->input->post('arr_filter');

            // Mengambil data menggunakan fungsi proses_data yang sama dengan AJAX loadData
            $data_report = $this->proses_data_yearly($arr_filter);

            // Sesuai dengan struktur JSON di AJAX: data.record.record
            $records      = $data_report['record'] ?? [];
            $laba_bersih  = $data_report['laba_bersih_yearly'] ?? [];

            if (empty($records)) {
                throw new Exception("Data tidak ditemukan untuk periode tersebut.");
            }

            $tahun_dari   = (int)$arr_filter[0]['tahun_dari'];
            $tahun_sampai = (int)$arr_filter[0]['tahun_sampai'];
            $periode      = $tahun_dari . ' - ' . $tahun_sampai;

            ob_start();
            $object = new PHPExcel();
            $sheet  = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Laba Rugi Tahunan');

            // --- 1. HEADER LAPORAN ---
            $sheet->setCellValue('A1', 'PT. HEKSATEX INDAH');
            $sheet->setCellValue('A2', 'LABA RUGI (YEARLY)');
            $sheet->setCellValue('A3', 'Periode: ' . $periode);
            $sheet->getStyle("A1:A3")->getFont()->setBold(true);
            $sheet->setShowGridlines(false);

            // --- 2. TABLE HEAD ---
            $sheet->setCellValue('A5', 'No');
            $sheet->setCellValue('B5', 'Kode Acc');
            $sheet->setCellValue('C5', 'Nama Acc');

            $column = 3; // Mulai dari kolom D (index 3)
            for ($th = $tahun_dari; $th <= $tahun_sampai; $th++) {
                $sheet->setCellValueByColumnAndRow($column, 5, $th);
                $sheet->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($column))->setWidth(15);
                $column++;
            }
            $last_col_idx = $column - 1;
            $last_col_letter = PHPExcel_Cell::stringFromColumnIndex($last_col_idx);

            // Style Header Tabel (D3D3D3 sesuai standar Anda)
            $sheet->getStyle("A5:{$last_col_letter}5")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'D3D3D3']],
                'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
            ]);

            // --- 3. ISI DATA ---
            $rowCount = 6;
            $no = 1;

            // Identifikasi urutan level untuk indentasi (seperti di JS)
            $allLevels = array_column($records, 'level');
            $sortedLevels = array_values(array_unique($allLevels));
            sort($sortedLevels);

            foreach ($records as $val) {
                $levelIndex = array_search($val['level'], $sortedLevels);
                $indentStr  = str_repeat('    ', $levelIndex);
                $nama_acc   = ($val['tipe'] == 'total') ? "TOTAL " . $val['nama_acc'] : $val['nama_acc'];

                // --- LOGIKA NOMOR (NO) ---
                // Sesuai permintaan: No muncul di baris data & total, tapi tidak di spacer
                $sheet->setCellValue('A' . $rowCount, $no++);

                $sheet->setCellValueExplicit('B' . $rowCount, $val['kode_acc'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowCount, $indentStr . $nama_acc);

                // Looping Saldo Tahunan (Sesuai key value.yearly[th] di AJAX)
                $col_saldo = 3;
                for ($th = $tahun_dari; $th <= $tahun_sampai; $th++) {
                    $saldo = $val['yearly'][$th] ?? null;
                    if ($saldo !== null) {
                        $sheet->setCellValueByColumnAndRow($col_saldo, $rowCount, $saldo);
                    }
                    $col_saldo++;
                }

                // --- STYLING WARNA LEVEL ---
                $color = '000000';
                if ($val['level'] == 1)      $color = '437333'; // Hijau
                else if ($val['level'] == 2) $color = 'E78D2D'; // Oranye
                else if ($val['level'] == 3) $color = '2F5FB3'; // Biru
                else if ($val['level'] == 4) $color = 'D42459'; // Pink

                $styleRow = [
                    'font' => [
                        'color' => ['rgb' => $color],
                        'bold'  => ($val['level'] < 5 || $val['tipe'] == 'total'),
                        'italic' => ($val['tipe'] == 'total')
                    ]
                ];

                // Style Khusus Baris Total
                if ($val['tipe'] == "total") {
                    $sheet->getStyle("B{$rowCount}:{$last_col_letter}{$rowCount}")->applyFromArray([
                        'borders' => ['top' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
                    ]);
                }

                $sheet->getStyle("A{$rowCount}:{$last_col_letter}{$rowCount}")->applyFromArray($styleRow);
                $first_data_col = PHPExcel_Cell::stringFromColumnIndex(3);
                $sheet->getStyle("{$first_data_col}{$rowCount}:{$last_col_letter}{$rowCount}")
                    ->getNumberFormat()->setFormatCode('#,##0.00');

                $rowCount++;

                // --- LOGIKA SPACER (Baris Kosong) ---
                // Sesuai JS: if (value.tipe == "total" && levelIndex === 0)
                if ($val['tipe'] == "total" && $levelIndex === 0) {
                    // Majukan rowCount tanpa mengisi Cell A, sehingga No tetap kosong di baris ini
                    $rowCount++;
                }
            }

            // --- 4. BARIS LABA BERSIH ---
            $sheet->setCellValue('A' . $rowCount, 'LABA / RUGI BERSIH');
            $sheet->mergeCells("A{$rowCount}:C{$rowCount}");
            $sheet->getStyle("A{$rowCount}")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $col_laba = 3;
            for ($th = $tahun_dari; $th <= $tahun_sampai; $th++) {
                $laba_val = $laba_bersih[$th] ?? 0;
                $sheet->setCellValueByColumnAndRow($col_laba, $rowCount, $laba_val);
                $col_laba++;
            }

            $sheet->getStyle("A{$rowCount}:{$last_col_letter}{$rowCount}")->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'F4F4F4']]
            ]);
            $sheet->getStyle("{$first_data_col}{$rowCount}:{$last_col_letter}{$rowCount}")
                ->getNumberFormat()->setFormatCode('#,##0.00');

            // Autosize
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setAutoSize(true);
            $sheet->getColumnDimension('C')->setAutoSize(true);

            $writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            die(json_encode([
                'status'   => 'success',
                'file'     => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData),
                'filename' => "Laba Rugi Tahunan {$periode}.xlsx"
            ]));
        } catch (Exception $ex) {
            die(json_encode(['status' => 'failed', 'message' => $ex->getMessage()]));
        }
    }
}
