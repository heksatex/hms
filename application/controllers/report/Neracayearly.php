<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Neracayearly extends MY_Controller
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
        $id_dept        = 'RKNY';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/v_neraca_yearly', $data);
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
                $data = $this->proses_neraca_yearly();
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


    public function proses_neraca_yearly($filter_manual = null)
    {
        // 1. Pengaturan Filter (Disederhanakan tanpa bulan)
        if (empty($filter_manual)) {
            $tahun_dari    = $this->input->post('tahun_dari');
            $tahun_sampai  = $this->input->post('tahun_sampai');
            $levels        = $this->input->post('level');
            $hide_empty    = $this->input->post('checkhidden');
        } else {
            $tahun_dari    = $filter_manual[0]['tahun_dari'];
            $tahun_sampai  = $filter_manual[0]['tahun_sampai'];
            $levels        = $filter_manual[0]['level'] ?? [];
            $hide_empty    = $filter_manual[0]['checkhidden'] ?? false;
        }

        $max_level_selected = !empty($levels) ? max(array_map('intval', $levels)) : 5;
        $hide_empty = ($hide_empty === 'true' || $hide_empty === true || $hide_empty === '1');

        // 2. Buat Daftar Periode Kolom (Per Tahun)
        $period_list = [];
        for ($t = (int)$tahun_dari; $t <= (int)$tahun_sampai; $t++) {
            $period_list[] = [
                'thn' => $t,
                'key' => "year_" . $t
            ];
        }

        // 3. Ambil Mutasi & Saldo Awal (Gunakan model yang disesuaikan untuk yearly)
        // Asumsi: Model get_list_neraca_yearly melakukan SUM mutasi per tahun
        $transaksi = $this->m_neraca->get_list_neraca_yearly($tahun_dari, $tahun_sampai, $period_list)->result_array();

        $saldo_map = [];
        foreach ($transaksi as $t) {
            $saldo_map[$t['kode_coa']] = $t;
        }

        // 4. Ambil COA Neraca
        $all_coa = $this->db->query("SELECT kode_coa, nama, level, parent, saldo_normal 
                                 FROM acc_coa WHERE LEFT(kode_coa,1) <= '3' 
                                 ORDER BY kode_coa ASC")->result_array();

        // 5. Helper Kalkulasi Saldo Kumulatif per Tahun
        $get_yearly_balance = function ($kode_coa_parent, $parent_saldo_normal) use ($saldo_map, $period_list) {
            $yearly_debit_basis = [];
            foreach ($period_list as $p) {
                $yearly_debit_basis[$p['key']] = 0;
            }

            foreach ($saldo_map as $kode_tr => $val) {
                if (strpos($kode_tr, $kode_coa_parent) === 0) {

                    // Saldo Awal adalah saldo sebelum 'tahun_dari'
                    $current_sa = (float)($val['saldo_awal_finish'] ?? 0);
                    $current_normal = $val['saldo_normal'];

                    // Standarisasi ke basis Debit
                    $running_raw = ($current_normal == 'C') ? -$current_sa : $current_sa;

                    foreach ($period_list as $p) {
                        $key = $p['key'];
                        $mutasi_tahun_ini = (float)($val[$key] ?? 0);

                        // Neraca bersifat kumulatif (Saldo awal + mutasi tahun ini)
                        $running_raw += $mutasi_tahun_ini;
                        $yearly_debit_basis[$key] += $running_raw;
                    }
                }
            }

            // Konversi ke Saldo Normal Parent
            $final_res = [];
            foreach ($yearly_debit_basis as $key => $total_debit_basis) {
                $final_res[$key] = ($parent_saldo_normal == 'C') ? -$total_debit_basis : $total_debit_basis;
            }
            return $final_res;
        };

        // 6. Processing Data Results (Sama dengan monthly, ganti variabel 'monthly' jadi 'yearly')
        $results = [];
        $stack = [];
        $total_aset_yearly = array_fill(0, count($period_list), 0);
        $total_pasiva_yearly = array_fill(0, count($period_list), 0);
        $total_selisih_yearly = array_fill(0, count($period_list), 0);

        foreach ($all_coa as $index => $coa) {
            $balances = $get_yearly_balance($coa['kode_coa'], $coa['saldo_normal']);
            $prefix = substr($coa['kode_coa'], 0, 1);
            $next_coa = $all_coa[$index + 1] ?? null;

            if ($coa['level'] == 1) {
                $idx = 0;
                foreach ($balances as $val) {
                    if ($prefix == '1') {
                        $total_aset_yearly[$idx] += $val;
                    } else {
                        $total_pasiva_yearly[$idx] += $val;
                    }
                    $total_selisih_yearly[$idx] = $total_aset_yearly[$idx] - $total_pasiva_yearly[$idx];
                    $idx++;
                }
            }

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
                    "yearly"   => ($coa['level'] == $max_level_selected || $coa['level'] == 5) ? array_values($balances) : array_fill(0, count($period_list), null),
                    "tipe"     => "row"
                ];

                if ($coa['level'] < $max_level_selected) {
                    array_push($stack, [
                        "nama"   => "TOTAL " . $coa['nama'],
                        "level"  => $coa['level'],
                        "yearly" => array_values($balances)
                    ]);
                }
            }

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
            "total_aset" => $total_aset_yearly,
            "total_pasiva" => $total_pasiva_yearly,
            "total_selisih" => $total_selisih_yearly,
            "period_count" => count($period_list)
        ];
    }

    public function export_excel_yearly()
    {
        try {
            $this->load->library('excel');

            $arr_filter = $this->input->post('arr_filter');
            if (empty($arr_filter)) throw new Exception("Filter kosong.");

            // Ambil data menggunakan proses yearly
            $data_report = $this->proses_neraca_yearly($arr_filter);
            $records = $data_report['record'] ?? [];
            $total_aset = $data_report['total_aset'] ?? [];
            $total_pasiva = $data_report['total_pasiva'] ?? [];
            $total_selisih = $data_report['total_selisih'] ?? [];
            $period_count = $data_report['period_count'] ?? 1;

            if (empty($records)) throw new Exception("Data tidak ditemukan.");

            $tahun_dari = (int)$arr_filter[0]['tahun_dari'];
            $tahun_sampai = (int)$arr_filter[0]['tahun_sampai'];
            $periode_text = $tahun_dari . ($tahun_dari != $tahun_sampai ? " - " . $tahun_sampai : "");

            ob_start();
            $object = new PHPExcel();
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Neraca Tahunan');

            // --- HEADER LAPORAN ---
            $last_col_index = 2 + (int) $period_count;
            $last_col_letter = PHPExcel_Cell::stringFromColumnIndex($last_col_index);

            $sheet->setCellValue('A1', 'PT. HEKSATEX INDAH');
            $sheet->setCellValue('A2', 'LAPORAN NERACA (TAHUNAN)');
            $sheet->setCellValue('A3', "Periode: Tahun {$periode_text}");

            $sheet->mergeCells("A1:{$last_col_letter}1");
            $sheet->mergeCells("A2:{$last_col_letter}2");
            $sheet->mergeCells("A3:{$last_col_letter}3");
            $sheet->getStyle("A1:A3")->getFont()->setBold(true);

            $sheet->setShowGridlines(false);

            // Lebar Kolom Statis
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(18);
            $sheet->getColumnDimension('C')->setWidth(40);

            // --- TABLE HEAD (TAHUNAN) ---
            $sheet->setCellValue('A5', 'No');
            $sheet->setCellValue('B5', 'Kode Acc');
            $sheet->setCellValue('C5', 'Nama Acc');

            $col_idx = 3;
            for ($t = $tahun_dari; $t <= $tahun_sampai; $t++) {
                $cell_ref = PHPExcel_Cell::stringFromColumnIndex($col_idx);
                $sheet->setCellValue($cell_ref . '5', 'TAHUN ' . $t);
                $sheet->getColumnDimension($cell_ref)->setWidth(20);
                $col_idx++;
            }

            $sheet->getStyle("A5:{$last_col_letter}5")->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'D3D3D3']],
                'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
            ]);

            // --- ISI DATA ---
            $rowCount = 6;
            $no = 1;
            foreach ($records as $val) {

                $indentStr = str_repeat('    ', $val['level'] - 1);

                $sheet->setCellValue('A' . $rowCount, $no++);
                $sheet->setCellValueExplicit('B' . $rowCount, $val['kode_acc'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowCount, $indentStr . $val['nama_acc']);

                $col_data_idx = 3;
                // Di yearly, variabelnya adalah 'yearly' bukan 'monthly'
                foreach ($val['yearly'] as $saldo) {
                    $cell_ref = PHPExcel_Cell::stringFromColumnIndex($col_data_idx);
                    if ($saldo !== null) {
                        $sheet->setCellValue($cell_ref . $rowCount, $saldo);
                        $sheet->getStyle($cell_ref . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                    }
                    $col_data_idx++;
                }

                // --- STYLE WARNA ---
                $color = '000000';
                $isBold = false;
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
                } else if ($val['tipe'] == "total") {
                    $isBold = true;
                }

                $styleArray = [
                    'font' => [
                        'color' => ['rgb' => $color],
                        'bold'  => $isBold,
                        'italic' => ($val['tipe'] == "total")
                    ]
                ];

                if ($val['tipe'] == "total") {
                    $sheet->getStyle("B$rowCount:{$last_col_letter}$rowCount")->applyFromArray([
                        'borders' => ['top' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
                    ]);
                }

                $sheet->getStyle("A$rowCount:$last_col_letter$rowCount")->applyFromArray($styleArray);
                $rowCount++;

                // Spacer Akhir (Setelah Total Level 1)
                if ($val['tipe'] == 'total' && $val['level'] == 1) {
                    $rowCount++;
                }
            }

            // --- FOOTER (Grand Total) ---
            $rowCount++;
            $renderFooter = function ($sheet, $row, $label, $data, $last_col_letter) {
                $sheet->setCellValue('A' . $row, $label);
                $sheet->mergeCells("A$row:C$row");
                $sheet->getStyle("A$row:$last_col_letter$row")->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID],
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

            $renderFooter($sheet, $rowCount++, "TOTAL ASSET", $total_aset, $last_col_letter);
            $renderFooter($sheet, $rowCount++, "TOTAL KEWAJIBAN & MODAL", $total_pasiva, $last_col_letter);
            $renderFooter($sheet, $rowCount++, "SELISIH", $total_selisih, $last_col_letter);

            // --- OUTPUT ---
            $writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            die(json_encode([
                'status' => 'success',
                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData),
                'filename' => 'Neraca Tahunan ' . $periode_text . '.xlsx'
            ]));
        } catch (Exception $ex) {
            die(json_encode(['status' => 'failed', 'message' => $ex->getMessage()]));
        }
    }
}
