<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Worksheet extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model('m_worksheet');
    }

    public function index()
    {
        $id_dept        = 'RKWS';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/v_worksheet', $data);
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

                $data = $this->proses_data($tgldari, $tglsampai, $checkhidden);
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


    function proses_data1($tgldari, $tglsampai, $checkhidden, $where = [])
    {

        $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgldari));
        $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tglsampai));

        $data = $this->m_worksheet->get_list_worksheet($tgl_dari, $tgl_sampai, $checkhidden, $where);
        $tmp_data = array();
        $debit    = 0;
        $credit   = 0;
        $saldo_awal = 0;
        $saldo_akhir = 0;
        $n_percobaan_debit = 0;
        $n_percobaan_credit = 0;
        $neraca_credit   = 0;
        $neraca_debit   = 0;
        $rugi_laba_credit   = 0;
        $rugi_laba_debit   = 0;
        foreach ($data as $datas) {

            $total_debit = floatval($datas->total_debit);
            $total_credit = floatval($datas->total_credit);

            $saldo_awal_debit    = floatval($datas->saldo_awal_debit);
            $saldo_awal_credit   = floatval($datas->saldo_awal_credit);

            // if($datas->saldo_normal == 'D'){
            //     $saldo_akhir = $saldo_awal + $total_debit - $total_credit; 
            // } else {
            //     $saldo_akhir = $saldo_awal + $total_credit -$total_debit; 
            // }
            if ($datas->saldo_normal == 'D') {
                $n_percobaan_debit = $saldo_awal_debit + $total_debit - $total_credit;
            }
            if ($datas->saldo_normal == 'C') {
                $n_percobaan_credit = $saldo_awal_credit + $total_credit -  $total_debit;
            }

            $kepala_depan = (int) substr($datas->kode_coa, 0, 1);

            if ($kepala_depan === 1 || $kepala_depan === 2 || $kepala_depan === 3) {
                if ($datas->saldo_normal == 'D') {
                    $neraca_debit = $n_percobaan_debit;
                }
                if ($datas->saldo_normal == 'C') {
                    $neraca_credit = $n_percobaan_credit;
                }
            }

            if ($kepala_depan >= 4) {
                if ($datas->saldo_normal == 'D') {
                    $rugi_laba_debit = $n_percobaan_debit;
                }
                if ($datas->saldo_normal == 'C') {
                    $rugi_laba_credit = $n_percobaan_credit;
                }
            }


            $tmp_data[] = array(
                'kode_acc' => $datas->kode_coa,
                'nama_acc' => $datas->nama_coa,
                'saldo_normal' => $datas->saldo_normal,
                'saldo_awal_debit' => $saldo_awal_debit,
                'saldo_awal_credit' => $saldo_awal_credit,
                'mutasi_debit' => $total_debit,
                'mutasi_credit' => $total_credit,
                'n_percobaan_debit' => $n_percobaan_debit,
                'n_percobaan_credit' => $n_percobaan_credit,
                'neraca_debit'      => $neraca_debit,
                'neraca_credit'      => $neraca_credit,
                'rugi_laba_debit'      => $rugi_laba_debit,
                'rugi_laba_credit'      => $rugi_laba_credit,

            );
            $debit    = 0;
            $credit   = 0;
            $saldo_awal = 0;
            $n_percobaan_debit = 0;
            $n_percobaan_credit = 0;
            $neraca_credit   = 0;
            $neraca_debit   = 0;
            $rugi_laba_credit   = 0;
            $rugi_laba_debit   = 0;
            // }   
        }

        return $tmp_data;
    }

    // function proses_data($tgldari, $tglsampai, $checkhidden, $where = [])
    // {

    //     $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgldari));
    //     $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tglsampai));

    //     $data = $this->m_worksheet->get_list_worksheet($tgl_dari, $tgl_sampai, $checkhidden, $where);

    //     $tmp_data = [];

    //     foreach ($data as $datas) {

    //         $total_debit  = floatval($datas->total_debit);
    //         $total_credit = floatval($datas->total_credit);

    //         $saldo_awal_debit  = floatval($datas->saldo_awal_debit);
    //         $saldo_awal_credit = floatval($datas->saldo_awal_credit);

    //         $saldo_normal = $datas->saldo_normal;

    //         // ============================
    //         // NERACA PERCOBAAN
    //         // ============================

    //         $saldo_np = 0;

    //         if ($saldo_normal == 'D') {
    //             $saldo_np = $saldo_awal_debit + $total_debit - $total_credit;
    //         } else {
    //             $saldo_np = $saldo_awal_credit + $total_credit - $total_debit;
    //         }

    //         $n_percobaan_debit = 0;
    //         $n_percobaan_credit = 0;

    //         if ($saldo_np >= 0) {

    //             if ($saldo_normal == 'D') {
    //                 $n_percobaan_debit = $saldo_np;
    //             } else {
    //                 $n_percobaan_credit = $saldo_np;
    //             }
    //         } else {

    //             if ($saldo_normal == 'D') {
    //                 $n_percobaan_credit = abs($saldo_np);
    //             } else {
    //                 $n_percobaan_debit = abs($saldo_np);
    //             }
    //         }

    //         // ============================
    //         // CEK GOLONGAN AKUN
    //         // ============================

    //         $kepala_depan = (int) substr($datas->kode_coa, 0, 1);

    //         $neraca_debit = 0;
    //         $neraca_credit = 0;

    //         $rugi_laba_debit = 0;
    //         $rugi_laba_credit = 0;

    //         // ============================
    //         // NERACA (AKUN 1,2,3)
    //         // ============================

    //         if ($kepala_depan <= 3) {

    //             $neraca_debit  = $n_percobaan_debit;
    //             $neraca_credit = $n_percobaan_credit;
    //         }

    //         // ============================
    //         // LABA RUGI (AKUN 4,5,6)
    //         // ============================

    //         if ($kepala_depan >= 4) {

    //             $rugi_laba_debit  = $n_percobaan_debit;
    //             $rugi_laba_credit = $n_percobaan_credit;
    //         }

    //         // ============================
    //         // SIMPAN DATA
    //         // ============================

    //         $tmp_data[] = [

    //             'kode_acc' => $datas->kode_coa,
    //             'nama_acc' => $datas->nama_coa,

    //             'saldo_normal' => $saldo_normal,

    //             'saldo_awal_debit' => $saldo_awal_debit,
    //             'saldo_awal_credit' => $saldo_awal_credit,

    //             'mutasi_debit' => $total_debit,
    //             'mutasi_credit' => $total_credit,

    //             'n_percobaan_debit' => $n_percobaan_debit,
    //             'n_percobaan_credit' => $n_percobaan_credit,

    //             'neraca_debit' => $neraca_debit,
    //             'neraca_credit' => $neraca_credit,

    //             'rugi_laba_debit' => $rugi_laba_debit,
    //             'rugi_laba_credit' => $rugi_laba_credit,

    //         ];
    //     }

    //     return $tmp_data;
    // }

    function prosesk_data($tgldari, $tglsampai, $checkhidden, $where = [])
    {

        $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgldari));
        $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tglsampai));

        $data = $this->m_worksheet->get_list_worksheet($tgl_dari, $tgl_sampai, $checkhidden, $where);

        $tmp_data = array();

        foreach ($data as $datas) {

            $total_debit  = floatval($datas->total_debit);
            $total_credit = floatval($datas->total_credit);

            $saldo_awal_debit  = floatval($datas->saldo_awal_debit);
            $saldo_awal_credit = floatval($datas->saldo_awal_credit);

            $n_percobaan_debit  = 0;
            $n_percobaan_credit = 0;

            $neraca_debit  = 0;
            $neraca_credit = 0;

            $rugi_laba_debit  = 0;
            $rugi_laba_credit = 0;

            // =============================
            // HITUNG NERACA PERCOBAAN
            // =============================

            if ($datas->saldo_normal == 'D') {
                $saldo = $saldo_awal_debit + $total_debit - $total_credit;
            } else {
                $saldo = $saldo_awal_credit + $total_credit - $total_debit;
            }

            // Tentukan posisi debit / credit
            if ($saldo >= 0) {

                if ($datas->saldo_normal == 'D') {
                    $n_percobaan_debit = $saldo;
                } else {
                    $n_percobaan_credit = $saldo;
                }
            } else {

                if ($datas->saldo_normal == 'D') {
                    $n_percobaan_credit = abs($saldo);
                } else {
                    $n_percobaan_debit = abs($saldo);
                }
            }

            // =============================
            // CEK GOLONGAN COA
            // =============================

            $kepala_depan = (int) substr($datas->kode_coa, 0, 1);

            // =============================
            // NERACA (1,2,3)
            // =============================

            if ($kepala_depan <= 3) {

                $neraca_debit  = $n_percobaan_debit;
                $neraca_credit = $n_percobaan_credit;
            }

            // =============================
            // LABA RUGI (4,5,6,dst)
            // =============================

            if ($kepala_depan >= 4) {

                $rugi_laba_debit  = $n_percobaan_debit;
                $rugi_laba_credit = $n_percobaan_credit;
            }

            // =============================
            // SIMPAN DATA
            // =============================

            $tmp_data[] = array(
                'kode_acc' => $datas->kode_coa,
                'nama_acc' => $datas->nama_coa,
                'saldo_normal' => $datas->saldo_normal,

                'saldo_awal_debit' => $saldo_awal_debit,
                'saldo_awal_credit' => $saldo_awal_credit,

                'mutasi_debit' => $total_debit,
                'mutasi_credit' => $total_credit,

                'n_percobaan_debit' => $n_percobaan_debit,
                'n_percobaan_credit' => $n_percobaan_credit,

                'neraca_debit' => $neraca_debit,
                'neraca_credit' => $neraca_credit,

                'rugi_laba_debit' => $rugi_laba_debit,
                'rugi_laba_credit' => $rugi_laba_credit,
            );
        }

        return $tmp_data;
    }

    function proses_data($tgldari, $tglsampai, $checkhidden, $where = [])
    {

        $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgldari));
        $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tglsampai));

        $data = $this->m_worksheet->get_list_worksheet($tgl_dari, $tgl_sampai, $checkhidden, $where);

        $tmp_data = array();
        $tmp_data2 = array();

        $total_rl_debit = 0;
        $total_rl_credit = 0;

        $total_neraca_debit = 0;
        $total_neraca_credit = 0;

        foreach ($data as $datas) {

            $total_debit = floatval($datas->total_debit);
            $total_credit = floatval($datas->total_credit);

            $saldo_awal_debit  = floatval($datas->saldo_awal_debit);
            $saldo_awal_credit = floatval($datas->saldo_awal_credit);

            $n_percobaan_debit = 0;
            $n_percobaan_credit = 0;

            $neraca_debit = 0;
            $neraca_credit = 0;

            $rugi_laba_debit = 0;
            $rugi_laba_credit = 0;

            // =========================
            // HITUNG NERACA PERCOBAAN
            // =========================

            if ($datas->saldo_normal == 'D') {
                $saldo = $saldo_awal_debit + $total_debit - $total_credit;
            } else {
                $saldo = $saldo_awal_credit + $total_credit - $total_debit;
            }


            if ($datas->saldo_normal == 'D') {
                $n_percobaan_debit = $saldo;
            } else {
                $n_percobaan_credit = $saldo;
            }

            // =========================
            // CEK GOLONGAN COA
            // =========================

            $kepala_depan = (int) substr($datas->kode_coa, 0, 1);

            // NERACA
            if ($kepala_depan <= 3) {

                $neraca_debit = $n_percobaan_debit;
                $neraca_credit = $n_percobaan_credit;

                $total_neraca_debit += $neraca_debit;
                $total_neraca_credit += $neraca_credit;
            }

            // LABA RUGI
            if ($kepala_depan >= 4) {

                $rugi_laba_debit = $n_percobaan_debit;
                $rugi_laba_credit = $n_percobaan_credit;

                $total_rl_debit += $rugi_laba_debit;
                $total_rl_credit += $rugi_laba_credit;
            }

            $tmp_data[] = array(
                'kode_acc' => $datas->kode_coa,
                'nama_acc' => $datas->nama_coa,
                'saldo_normal' => $datas->saldo_normal,

                'saldo_awal_debit' => $saldo_awal_debit,
                'saldo_awal_credit' => $saldo_awal_credit,

                'mutasi_debit' => $total_debit,
                'mutasi_credit' => $total_credit,

                'n_percobaan_debit' => $n_percobaan_debit,
                'n_percobaan_credit' => $n_percobaan_credit,

                'neraca_debit' => $neraca_debit,
                'neraca_credit' => $neraca_credit,

                'rugi_laba_debit' => $rugi_laba_debit,
                'rugi_laba_credit' => $rugi_laba_credit
            );
        }

        // =========================
        // HITUNG LABA / RUGI
        // =========================

        $laba = $total_rl_credit - $total_rl_debit;
        $rugi = $total_rl_debit - $total_rl_credit;

        if ($laba > 0) {
            $total_neraca_debit += $laba;
        }

        if ($rugi > 0) {
            $total_neraca_credit += $rugi;
        }

        return $tmp_data2 =  [
            'data' => $tmp_data,
            'total_rl_debit' => $total_rl_debit,
            'total_rl_credit' => $total_rl_credit,
            'total_neraca_debit' => $total_neraca_debit,
            'total_neraca_credit' => $total_neraca_credit,
            'laba' => $laba > 0 ? $laba : 0,
            'rugi' => $rugi > 0 ? $rugi : 0
        ];
    }


    function export_excel()
    {
        try {
            //code...
            $this->load->library('excel');


            $arr_filter = $this->input->post('arr_filter');

            $tgl_dari   = $arr_filter[0]['tgldari'] ?? '';
            $tgl_sampai = $arr_filter[0]['tglsampai'] ?? '';
            $checkhidden = $arr_filter[0]['checkhidden'] ?? '';
            $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgl_dari));
            $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tgl_sampai));

            ob_start();
            $object = new PHPExcel();

            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Global');

            $rowCount = 1;
            $loop     = 1;

            $periode = tgl_indo(date('d-m-Y', strtotime($tgl_dari))) . ' - ' . tgl_indo(date('d-m-Y', strtotime($tgl_sampai)));

            // SET JUDUL
            $sheet->SetCellValue('A' . $rowCount, 'PT. HEKSATEX INDAH');
            $sheet->mergeCells('A' . $rowCount . ':h' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $rowCount++;

            $sheet->SetCellValue('A' . $rowCount, 'WORKSHEET');
            $sheet->mergeCells('A' . $rowCount . ':H' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $rowCount++;

            // set periode
            $sheet->SetCellValue('A' . $rowCount, '' . $periode);
            $sheet->mergeCells('A' . $rowCount . ':H' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            //bold huruf
            $object->getActiveSheet()->getStyle("A1:J3")->getFont()->setBold(true);

            $styleArrayColor = array(
                'font'  => array(
                    'bold'  => true,
                    // 'color' => array('rgb' => 'FFFFFF'),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'D3D3D3')
                ),
            );

            $table_head_columns  = array('No', 'Kode CoA', 'Nama CoA', 'Saldo Awal (D)', 'Saldo Awal (C)', 'Mutasi (D)', 'Mutasi (C)', 'N.Percobaan (D)', 'N.Percobaan (C)', 'Neraca (D)', 'Neraca (C)', 'Rugi Laba (D)', 'Rugi Laba (C)');
            $column = 0;
            foreach ($table_head_columns as $field) {
                $object->getActiveSheet()->setCellValueByColumnAndRow($column, 5, $field);
                $column++;
            }


            // set width and border
            $index_header = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M');
            $loop = 0;
            foreach ($index_header as $val) {

                $object->getActiveSheet()->getStyle($val . '5')->applyFromArray($styleArrayColor);

                if ($loop == 0) {
                    $object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A
                } else if ($loop ==  1) {
                    $object->getSheet(0)->getColumnDimension($val)->setWidth(10); // index B
                } else if ($loop ==  2) {
                    $object->getSheet(0)->getColumnDimension($val)->setWidth(35); // index C
                } else if ($loop > 1) {
                    $object->getSheet(0)->getColumnDimension($val)->setWidth(18); // index D -> I
                }
                $loop++;
            }

            $data = $this->proses_data($tgl_dari, $tgl_sampai, $checkhidden);
            $saldo_awal_debit = 0;
            $saldo_awal_credit = 0;
            $mutasi_debit = 0;
            $mutasi_credit = 0;
            $n_percobaan_debit = 0;
            $n_percobaan_credit = 0;
            $neraca_debit = 0;
            $neraca_credit = 0;
            $rugi_laba_debit = 0;
            $rugi_laba_credit = 0;
            $num  = 1;
            $rowCount = $rowCount + 3;

            foreach ($data['data'] as $row) {

                $object->getActiveSheet()->SetCellValue('A' . $rowCount, ($num++));
                $object->getActiveSheet()->SetCellValue('B' . $rowCount, $row['kode_acc']);
                $object->getActiveSheet()->SetCellValue('C' . $rowCount, $row['nama_acc']);
                // $object->getActiveSheet()->SetCellValue('D' . $rowCount, $row['saldo_normal']);
                $object->getActiveSheet()->SetCellValue('D' . $rowCount, $row['saldo_awal_debit']);
                $object->getActiveSheet()->SetCellValue('E' . $rowCount, $row['saldo_awal_credit']);
                $object->getActiveSheet()->SetCellValue('F' . $rowCount, $row['mutasi_debit']);
                $object->getActiveSheet()->SetCellValue('G' . $rowCount, $row['mutasi_credit']);
                $object->getActiveSheet()->SetCellValue('H' . $rowCount, $row['n_percobaan_debit']);
                $object->getActiveSheet()->SetCellValue('I' . $rowCount, $row['n_percobaan_credit']);
                $object->getActiveSheet()->SetCellValue('J' . $rowCount, $row['neraca_debit']);
                $object->getActiveSheet()->SetCellValue('K' . $rowCount, $row['neraca_credit']);
                $object->getActiveSheet()->SetCellValue('L' . $rowCount, $row['rugi_laba_debit']);
                $object->getActiveSheet()->SetCellValue('M' . $rowCount, $row['rugi_laba_credit']);


                $object->getActiveSheet()->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('I' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('K' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('L' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');

                $saldo_awal_debit = $saldo_awal_debit + $row['saldo_awal_debit'];
                $saldo_awal_credit = $saldo_awal_credit + $row['saldo_awal_credit'];
                $mutasi_debit = $mutasi_debit + $row['mutasi_debit'];
                $mutasi_credit = $mutasi_credit + $row['mutasi_credit'];
                $n_percobaan_debit = $n_percobaan_debit + $row['n_percobaan_debit'];
                $n_percobaan_credit = $n_percobaan_credit + $row['n_percobaan_credit'];
                $neraca_debit = $neraca_debit + $row['neraca_debit'];
                $neraca_credit = $neraca_credit + $row['neraca_credit'];
                $rugi_laba_debit = $rugi_laba_debit + $row['rugi_laba_debit'];
                $rugi_laba_credit = $rugi_laba_credit + $row['rugi_laba_credit'];

                $rowCount++;
            }

            $selisih_neraca = $neraca_credit - $neraca_debit;
            $selisih_rl     = $rugi_laba_credit - $rugi_laba_debit;
            $laba           = 0;
            $rugi           = 0;
            $posisi_rl_credit = 0;
            $posisi_rl_debit = 0;
            $posisi_neraca_credit = 0;
            $posisi_neraca_debit = 0;


            if ($selisih_rl > 0) {
                $laba  = $selisih_rl;
            } else if ($selisih_rl < 0) {
                $rugi  = abs($selisih_rl);
            }

            if ($rugi_laba_credit > $rugi_laba_debit) {
                $posisi_rl_debit  = $selisih_rl;
                $posisi_rl_credit = 0;
            } else if ($rugi_laba_credit < $rugi_laba_debit) {
                $posisi_rl_debit  = 0;
                $posisi_rl_credit = abs($selisih_rl);
            }

            if ($neraca_credit > $neraca_debit) {
                $posisi_neraca_debit  = $selisih_neraca;
                $posisi_neraca_credit = 0;
            } else if ($neraca_credit < $neraca_debit) {
                $posisi_neraca_debit  = 0;
                $posisi_neraca_credit = abs($selisih_neraca);
            }



            $object->getActiveSheet()->getStyle("A" . $rowCount . ":M" . $rowCount)->getFont()->setBold(true);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $object->getActiveSheet()->SetCellValue('A' . $rowCount, 'Total :');
            $sheet->mergeCells('A' . $rowCount . ':c' . $rowCount);
            // $object->getActiveSheet()->SetCellValue('E'.$rowCount, $s_awal);
            $object->getActiveSheet()->SetCellValue('D' . $rowCount, $saldo_awal_debit);
            $object->getActiveSheet()->SetCellValue('E' . $rowCount, $saldo_awal_credit);
            $object->getActiveSheet()->SetCellValue('F' . $rowCount, $mutasi_debit);
            $object->getActiveSheet()->SetCellValue('G' . $rowCount, $mutasi_credit);
            $object->getActiveSheet()->SetCellValue('H' . $rowCount, $n_percobaan_debit);
            $object->getActiveSheet()->SetCellValue('I' . $rowCount, $n_percobaan_credit);
            $object->getActiveSheet()->SetCellValue('J' . $rowCount, $neraca_debit);
            $object->getActiveSheet()->SetCellValue('K' . $rowCount, $neraca_credit);
            $object->getActiveSheet()->SetCellValue('L' . $rowCount, $rugi_laba_debit);
            $object->getActiveSheet()->SetCellValue('M' . $rowCount, $rugi_laba_credit);

            // $object->getActiveSheet()->getStyle('E'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('I' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('K' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('L' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');


            // =======================
            // BARIS LABA / RUGI
            // =======================
            $caption_lr =  '';
            if ($laba > 0) {
                $caption_lr = 'LABA';
            } else if ($rugi > 0) {
                $caption_lr = 'RUGI';
            }

            $rowCount++;
            $object->getActiveSheet()->getStyle("A" . $rowCount . ":M" . $rowCount)->getFont()->setBold(true);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $object->getActiveSheet()->SetCellValue('A' . $rowCount, $caption_lr);
            $sheet->mergeCells('A' . $rowCount . ':I' . $rowCount);
            // $object->getActiveSheet()->SetCellValue('E'.$rowCount, $s_awal);
            $object->getActiveSheet()->SetCellValue('J' . $rowCount, $posisi_neraca_debit);
            $object->getActiveSheet()->SetCellValue('K' . $rowCount, $posisi_neraca_credit);
            $object->getActiveSheet()->SetCellValue('L' . $rowCount, $posisi_rl_debit);
            $object->getActiveSheet()->SetCellValue('M' . $rowCount, $posisi_rl_credit);

            $object->getActiveSheet()->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('K' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('L' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');

            // =======================
            // TOTAL AKHIR (BALANCE)
            // =======================

            $total_neraca_debit = $neraca_debit;
            $total_neraca_credit = $neraca_credit;
            $total_rl_debit = $rugi_laba_debit;
            $total_rl_credit = $rugi_laba_credit;

            $total_neraca_credit += $posisi_neraca_credit;
            $total_neraca_debit += $posisi_neraca_debit;
            $total_rl_debit += $posisi_rl_debit;
            $total_rl_credit += $posisi_rl_credit;

            $rowCount++;
            $object->getActiveSheet()->getStyle("A" . $rowCount . ":M" . $rowCount)->getFont()->setBold(true);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $object->getActiveSheet()->SetCellValue('A' . $rowCount, "Total :");
            $sheet->mergeCells('A' . $rowCount . ':I' . $rowCount);
            // $object->getActiveSheet()->SetCellValue('E'.$rowCount, $s_awal);
            $object->getActiveSheet()->SetCellValue('J' . $rowCount, $total_neraca_debit);
            $object->getActiveSheet()->SetCellValue('K' . $rowCount, $total_neraca_debit);
            $object->getActiveSheet()->SetCellValue('L' . $rowCount, $total_rl_debit);
            $object->getActiveSheet()->SetCellValue('M' . $rowCount, $total_rl_credit);
            
            $object->getActiveSheet()->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('K' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('L' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');


            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $object->save('php://output');

            $xlsData = ob_get_contents();
            ob_end_clean();

            $name_file = 'Worksheet ' . $periode . '.xlsx';

            $response =  array(
                'op'        => 'ok',
                'file'      => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData),
                'filename'  => $name_file
            );

            die(json_encode($response));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }
}
