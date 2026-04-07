<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Neracastandar extends MY_Controller
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
        $id_dept        = 'RKNS';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/v_neraca_standar', $data);
    }


    public function loadData()
    {
        $validation = [
            [
                'field' => 'tglsampai',
                'label' => 'Tanggal',
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
                $data = $this->proses_neraca();
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


    public function proses_neraca($filter_manual = null)
    {
        // 1. Pengaturan Filter
        if (empty($filter_manual)) {
            $tgl_sampai  = $this->input->post('tglsampai');
            $levels      = $this->input->post('level');
            $hide_empty  = $this->input->post('checkhidden');
        } else {
            $tgl_sampai  = $filter_manual[0]['tglsampai'] ?? '';
            $levels      = $filter_manual[0]['level'] ?? [];
            $hide_empty  = $filter_manual[0]['checkhidden'] ?? false;
        }

        $max_level_selected = !empty($levels) ? max(array_map('intval', $levels)) : 5;
        $hide_empty = ($hide_empty === 'true' || $hide_empty === true || $hide_empty === '1');
        $tgl_sampai = date('Y-m-d', strtotime($tgl_sampai)) . " 23:59:59";

        // 2. Ambil Transaksi & Map Saldo
        // Menggunakan method model yang sudah ada (memastikan saldo_awal ikut terhitung)
        $transaksi = $this->m_neraca->get_list_neraca_standar($tgl_sampai, ['coa.level'=> 5])->result_array();

        $saldo_map = [];
        foreach ($transaksi as $t) {
            $saldo_map[$t['kode_coa']] = $t;
        }

        // 3. Ambil COA Neraca (Kepala 1, 2, 3)
        $all_coa = $this->db->query("SELECT kode_coa, nama, level, parent, saldo_normal 
                                 FROM acc_coa 
                                 WHERE LEFT(kode_coa,1) <= '3' 
                                 ORDER BY kode_coa ASC")->result_array();

        // 4. Helper Kalkulasi Saldo (Sesuai Saldo Normal)
        $get_balance = function ($kode_coa, $saldo_normal) use ($saldo_map) {
            $total = 0;
            foreach ($saldo_map as $kode_tr => $val) {
                // Cek apakah transaksi ini milik COA ini atau anak-anaknya (Prefix match)
                if (strpos($kode_tr, $kode_coa) === 0) {
                    $d  = (float)($val['total_debit'] ?? 0);
                    $c  = (float)($val['total_credit'] ?? 0);
                    $sa = (float)($val['saldo_awal'] ?? 0);

                    // ambil saldo normal berdasarkan akun
                    if ($val['saldo_normal'] == 'D') {
                        $total += $sa + ($d - $c);
                    } else {
                        $total += -$sa + ($d - $c);
                    }
                }
            }
            // set saldo normal sesuai parent
            return ($saldo_normal === 'C') ? -$total : $total;
        };

        $results = [];
        $stack = [];
        $total_aset = 0;
        $total_pasiva = 0;

        foreach ($all_coa as $index => $coa) {
            // Hitung saldo COA ini (termasuk akumulasi anak-anaknya)
            $saldo = $get_balance($coa['kode_coa'], $coa['saldo_normal']);
            $prefix = substr($coa['kode_coa'], 0, 1);

            // --- HITUNG GRAND TOTAL (Level 1) ---
            if ($coa['level'] == 1) {
                if ($prefix == '1') {
                    $total_aset += $saldo;
                } elseif ($prefix == '2' || $prefix == '3') {
                    $total_pasiva += $saldo;
                }
            }

            $next_coa = isset($all_coa[$index + 1]) ? $all_coa[$index + 1] : null;

            // --- FILTER DISPLAY ---
            $is_visible = (empty($levels) || in_array($coa['level'], $levels)) && !($hide_empty && round($saldo, 2) == 0);

            if ($is_visible) {
                $results[] = [
                    "kode_acc"  => $coa['kode_coa'],
                    "nama_acc"  => $coa['nama'],
                    "level"     => (int)$coa['level'],
                    // Tampilkan saldo hanya jika level terpilih (atau level detail paling bawah)
                    "saldo"     => ($coa['level'] == $max_level_selected || $coa['level'] == 5) ? $saldo : null,
                    "tipe"      => "row"
                ];

                // Simpan ke stack untuk baris "TOTAL [Nama Parent]"
                if ($coa['level'] < $max_level_selected) {
                    array_push($stack, [
                        "nama"    => "TOTAL " . $coa['nama'],
                        "level"   => $coa['level'],
                        "saldo"   => $saldo
                    ]);
                }
            }

            // --- GENERATE TOTAL ROW (Sub-Total) ---
            while (!empty($stack) && ($next_coa == null || $next_coa['level'] <= end($stack)['level'])) {
                $last_stack = array_pop($stack);
                $results[] = [
                    "kode_acc"  => "",
                    "nama_acc"  => $last_stack['nama'],
                    "level"     => (int)$last_stack['level'],
                    "saldo"     => $last_stack['saldo'],
                    "tipe"      => "total"
                ];
            }
        }

        return [
            "record" => $results,
            "total_aset" => $total_aset,
            "total_pasiva" => $total_pasiva,
            "tanggal_neraca" => date('d-m-Y', strtotime($tgl_sampai))
        ];
    }


    public function export_excel()
    {
        try {
            $this->load->library('excel');

            // 1. Ambil Filter & Data
            $arr_filter = $this->input->post('arr_filter');
            $data_report = $this->proses_neraca($arr_filter); // Pastikan panggil proses_neraca

            $records = $data_report['record'];
            $total_aset = $data_report['total_aset'];
            $total_pasiva = $data_report['total_pasiva'];

            $tgl_neraca = $data_report['tanggal_neraca'];
            $tgl_neraca = tgl_indo(date('d-m-Y', strtotime($data_report['tanggal_neraca'])));

            if (empty($records)) {
                throw new Exception("Data tidak ditemukan untuk periode tersebut.");
            }

            ob_start();
            $object = new PHPExcel();
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Neraca Standar');

            // --- HEADER LAPORAN ---
            $sheet->setCellValue('A1', 'PT. HEKSATEX INDAH');
            $sheet->setCellValue('A2', 'NERACA (STANDAR)');
            $sheet->setCellValue('A3', 'Per Tanggal: ' . $tgl_neraca);
            $sheet->mergeCells('A1:D1');
            $sheet->mergeCells('A2:D2');
            $sheet->mergeCells('A3:D3');
            $object->getActiveSheet()->getStyle("A1:A3")->getFont()->setBold(true);

            // Pengaturan Lebar Kolom
            $sheet->getColumnDimension('A')->setWidth(5);
            $sheet->getColumnDimension('B')->setWidth(15);
            $sheet->getColumnDimension('C')->setWidth(45);
            $sheet->getColumnDimension('D')->setWidth(20);

            $sheet->setShowGridlines(false);

            // --- TABLE HEAD ---
            $table_head = array('No', 'Kode Acc', 'Nama Acc', 'Saldo');
            $column = 0;
            foreach ($table_head as $field) {
                $sheet->setCellValueByColumnAndRow($column, 5, $field);
                $column++;
            }

            $sheet->getStyle('A5:D5')->applyFromArray([
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

                // Kolom No, Kode, Nama
                $sheet->setCellValue('A' . $rowCount, ($val['tipe'] == 'row' && ($val['level'] == 5 || empty($val['saldo']))) ? $no++ : '');
                $sheet->setCellValueExplicit('B' . $rowCount, $val['kode_acc'], PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowCount, $indentStr . $val['nama_acc']);

                // Saldo
                if ($val['tipe'] == 'total' || $val['level'] == 5 || !empty($val['saldo'])) {
                    $sheet->setCellValue('D' . $rowCount, $val['saldo']);
                }

                // Styling Warna (Konsisten dengan View)
                $color = '000000';
                if ($val['level'] == 1) $color = '437333'; // Hijau Asset
                else if ($val['level'] == 2) $color = 'E78D2D'; // Oranye Kewajiban
                else if ($val['level'] == 3) $color = '2F5FB3'; // Biru Modal
                else if ($val['level'] == 4) $color = 'D42459'; // Merah

                $styleRow = [
                    'font' => [
                        'color' => ['rgb' => $color],
                        'bold'  => ($val['level'] < 5 || $val['tipe'] == 'total'),
                        'italic' => ($val['tipe'] == 'total')
                    ]
                ];

                if ($val['tipe'] == "total") {
                    $sheet->getStyle('B' . $rowCount . ':D' . $rowCount)->applyFromArray([
                        'borders' => ['top' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
                    ]);
                }

                $sheet->getStyle('A' . $rowCount . ':D' . $rowCount)->applyFromArray($styleRow);
                $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');

                $rowCount++;

                // Spacer Grup Besar
                if ($val['tipe'] == 'total' && $levelOrder === 0) {
                    $rowCount++;
                }
            }

            // --- FOOTER TOTAL AKHIR & BALANCE CHECK ---
            $rowCount++;

            // Total Asset
            $sheet->setCellValue('A' . $rowCount, 'TOTAL ASSET');
            $sheet->mergeCells('A' . $rowCount . ':C' . $rowCount);
            $sheet->setCellValue('D' . $rowCount, $total_aset);
            $sheet->getStyle('A' . $rowCount . ':D' . $rowCount)->applyFromArray([
                'font' => ['bold' => true,],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID]
            ]);
            $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $rowCount++;

            // Total Kewajiban & Modal
            $sheet->setCellValue('A' . $rowCount, 'TOTAL KEWAJIBAN & MODAL');
            $sheet->mergeCells('A' . $rowCount . ':C' . $rowCount);
            $sheet->setCellValue('D' . $rowCount, $total_pasiva);
            $sheet->getStyle('A' . $rowCount . ':D' . $rowCount)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID]
            ]);
            $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $rowCount++;

            // Status Balance
            $selisih = round($total_aset, 2) - round($total_pasiva, 2);
            if (abs($selisih) > 0.1) {
                $sheet->setCellValue('A' . $rowCount, 'SELISIH');
                $sheet->mergeCells('A' . $rowCount . ':C' . $rowCount);
                $sheet->setCellValue('D' . $rowCount, $selisih);
                $sheet->getStyle('A' . $rowCount . ':D' . $rowCount)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID]
                ]);
            } else {
                $sheet->setCellValue('A' . $rowCount, 'NERACA SEIMBANG (BALANCE)');
                $sheet->mergeCells('A' . $rowCount . ':D' . $rowCount);
                $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A' . $rowCount . ':D' . $rowCount)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID]
                ]);
            }
            $sheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');


            $writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            $response = array(
                'status'   => 'success',
                'file'     => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData),
                'filename' => 'Neraca Standar Per ' . $tgl_neraca . '.xlsx'
            );

            die(json_encode($response));
        } catch (Exception $ex) {
            die(json_encode(['status' => 'failed', 'message' => $ex->getMessage()]));
        }
    }
}
