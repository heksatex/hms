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

        // Cari tahu level tertinggi yang dipilih (misal: 4)
        $max_level_selected = !empty($levels) ? max(array_map('intval', $levels)) : 5;

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
                    // if ($saldo_normal == 'D') {
                    //     $total += ($val['total_debit'] - $val['total_credit']);
                    // } else {
                    //     }
                        $total += ($val['total_credit'] - $val['total_debit']);
                }
            }
            return $total;
        };

        $results = [];
        $laba_bersih = 0;
        $total_kredit_group = 0;
        $total_debit_group = 0;
        $stack = []; // Untuk menyimpan antrean baris Total

        foreach ($all_coa as $index => $coa) {
            $saldo = $get_balance($coa['kode_coa'], $coa['saldo_normal']);

            // Akumulasi Laba Bersih
            if ($coa['level'] == 1) {
                // if ($coa['saldo_normal'] == 'C') {
                //     $total_kredit_group += $saldo;
                // } else {
                //     $total_debit_group += $saldo;
                // }
                $laba_bersih += $saldo;
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
                    // "saldo"     => ($coa['level'] > 4) ? $saldo : null, // Saldo hanya muncul di leaf/detail
                    // Jika ini adalah level terakhir yang dipilih user, tampilkan saldonya di baris ini
                    "saldo"     => ($coa['level'] == $max_level_selected || $coa['level'] == 5) ? $saldo : null,
                    "tipe"      => "row"
                ];

                // LOGIKA STACK TOTAL:
                // Hanya buat total jika level akun saat ini LEBIH KECIL dari level maksimal yang dipilih
                // Contoh: Jika user pilih sampai lvl 4, maka lvl 1, 2, dan 3 yang punya baris TOTAL.
                if ($coa['level'] < $max_level_selected) {
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
            "laba_bersih" => $laba_bersih
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
                $nama_acc = ($val['tipe'] == 'total') ? "" . $val['nama_acc'] : $val['nama_acc'];

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
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID],
                'borders' => ['bottom' => ['style' => PHPExcel_Style_Border::BORDER_THIN]]
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
