<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');


class Bukubesarpembantupiutang extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();
        // $this->load->model("m_global");
        $this->load->model('_module');
        $this->load->model('m_bukubesarpembantupiutang');
    }

    public function index()
    {
        $data['id_dept'] = 'RBBPP';
        $this->load->view('report/v_bukubesar_pembantu_piutang', $data);
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
                $currency    = $this->input->post('currency'); //  valas, rp

                $data = $this->proses_data($tgldari, $tglsampai, $checkhidden, $currency);
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



    function proses_data($tgldari, $tglsampai, $checkhidden, $currency)
    {

        $tgl_dari   = date('Y-m-d 00:00:00', strtotime($tgldari));
        $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tglsampai));
        $tmp_gol   = [];
        $tmp_list_gol  = [];
        $list_gol = $this->m_bukubesarpembantupiutang->get_list_golongan();
        foreach ($list_gol as $gol) {

            $where      = ['p.customer' => 1, 'p.gol' => $gol->id];
            $result = $this->proses_data2($tgl_dari, $tgl_sampai, $checkhidden, $where, [], $currency);
            if ($result) {
                $tmp_gol[] = array(
                    'gol_id' => $gol->id,
                    'gol_nama' => $gol->golnama,
                    'tmp_data' => $result
                );
            }
            // $tmp_data = [];
            $tmp_list_gol[] = $gol->id;
        }

        $where          = ['p.customer' => 1,];
        $where_not_in   = ['p.gol' => $tmp_list_gol];
        $result2 = $this->proses_data2($tgl_dari, $tgl_sampai, $checkhidden, $where, $where_not_in, $currency);
        if ($result2) {
            $tmp_gol[] = array(
                'gol_id' => 0,
                'gol_nama' => 'KOSONG',
                'tmp_data' => $result2
            );
        }

        return $tmp_gol;
    }

    function proses_data2($tgl_dari, $tgl_sampai, $checkhidden, $where, $not_in = [], $currency)
    {
        $data       = $this->m_bukubesarpembantupiutang->get_list_bukubesar($tgl_dari, $tgl_sampai, $checkhidden, $where, $not_in, $currency);
        $tmp_data = array();
        $debit    = 0;
        $credit   = 0;
        $saldo_awal = 0;
        $saldo_akhir = 0;
        foreach ($data as $datas) {

            $saldo_awal    = floatval($datas->saldo_awal_final);
            $total_piutang   = (float) round($datas->total_piutang_dpp_ppn, 2);
            $total_pelunasan   = (float) $datas->total_pelunasan;
            $total_retur   = (float) round($datas->total_retur_dpp_ppn, 2);
            $total_uang_muka   = (float) $datas->total_uang_muka;
            $total_koreksi   = (float) $datas->total_koreksi;
            $total_diskon  = (float) round($datas->total_diskon_dpp_ppn, 2);
            $total_kas_um  = (float) round($datas->total_kas_um, 2);
            $total_depo_baru  = (float) round($datas->total_deposit, 2);
            $total_depo_pelunasan  = (float) round($datas->total_deposit_pel, 2);
            $total_refund   = (float) round($datas->total_refund, 2);
            $saldo_akhir   = round($saldo_awal - $total_kas_um + $total_piutang -  $total_pelunasan - $total_retur - $total_diskon - $total_uang_muka + ($total_koreksi) +  $total_refund, 2);
            $tmp_data[] = array(
                'id_partner'  => $datas->id,
                'nama_partner' => $datas->nama,
                'saldo_awal'  => $saldo_awal,
                'piutang'     => $total_piutang,
                'dpp_piutang' => (float) round($datas->dpp_piutang, 2),
                'ppn_piutang' => (float) round($datas->ppn_piutang, 2),
                'total_piutang_dpp_ppn' => (float) round($datas->total_piutang_dpp_ppn, 2),
                'pelunasan'   => $total_pelunasan,
                'retur'       => $total_retur,
                'dpp_retur' => (float) round($datas->dpp_retur, 2),
                'ppn_retur' => (float) round($datas->ppn_retur, 2),
                'total_retur_dpp_ppn' => (float) round($datas->total_retur_dpp_ppn, 2),
                'dpp_diskon' => (float) round($datas->dpp_diskon, 2),
                'ppn_diskon' => (float) round($datas->ppn_diskon, 2),
                'total_diskon_dpp_ppn' => $total_diskon,
                'koreksi'     => $total_koreksi,
                'saldo_akhir' => $saldo_akhir,
                'um_baru'   => $total_kas_um,
                'um_pelunasan' => $total_uang_muka,
                'depo_baru'   => $total_depo_baru,
                'depo_pelunasan' => $total_depo_pelunasan,
                'refund' => $total_refund
            );
            $debit    = 0;
            $credit   = 0;
            // $saldo_awal = 0;
            // $saldo_akhir = 0;
            // }   
        }
        return $tmp_data;
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
            $currency    = $arr_filter[0]['currency'] ?? '';
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

            $sheet->SetCellValue('A' . $rowCount, 'BUKU BESAR PEMBANTU PIUTANG');
            $sheet->mergeCells('A' . $rowCount . ':H' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $rowCount++;

            // set periode
            $sheet->SetCellValue('A' . $rowCount, '' . $periode);
            $sheet->mergeCells('A' . $rowCount . ':H' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


            //bold huruf
            $object->getActiveSheet()->getStyle("A1:J3")->getFont()->setBold(true);

            // Border 
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );

            $styleArrayColor = array(
                'font'  => array(
                    'bold'  => true,
                    // 'color' => array('rgb' => 'FFFFFF'),
                ),
                'fill' => array(
                    'type' => PHPExcel_Style_Fill::FILL_SOLID,
                    'color' => array('rgb' => 'D3D3D3')
                ),
                // 'borders' => array(
                //     'allborders' => array(
                //     'style' => PHPExcel_Style_Border::BORDER_THIN
                //     )
                // )
            );

            // $table_head_columns  = array('No', 'Customer', 'Saldo Awal', 'Uang Mukas (Outstanding)', 'Piutang DPP', 'Piutang PPN', 'Piutang Total', 'Pelunasan', 'Retur DPP', 'Retur PPN', 'Retur Total', 'Diskon DPP', 'Diskon PPN', 'Diskon Total', 'Uang Muka', 'Koreksi', 'Saldo Akhir');
            // $column = 0;
            // foreach ($table_head_columns as $field) {
            //     $object->getActiveSheet()->setCellValueByColumnAndRow($column, 5, $field);
            //     $column++;
            // }

            // // set width and border
            // $index_header = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P','Q');
            // $loop = 0;
            // foreach ($index_header as $val) {

            //     $object->getActiveSheet()->getStyle($val . '5')->applyFromArray($styleArrayColor);

            //     if ($loop == 0) {
            //         $object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A
            //     } else if ($loop ==  1) {
            //         $object->getSheet(0)->getColumnDimension($val)->setWidth(35); // index B
            //     } else if ($loop > 1) {
            //         $object->getSheet(0)->getColumnDimension($val)->setWidth(20); // index C -> I
            //     }
            //     $loop++;
            // }





            // $data = $this->proses_data($tgl_dari, $tgl_sampai, $checkhidden, $currency);
            // $rowCount = $rowCount + 3;
            // foreach ($data as $head) {

            //     // nama golongan
            //     $object->getActiveSheet()->SetCellValue('A' . $rowCount, ''.$head['gol_nama']);
            //     $object->getActiveSheet()->mergeCells('A' . $rowCount . ':P' . $rowCount);
            //     $object->getActiveSheet()->getStyle("A" . $rowCount . ":P" . $rowCount)->getFont()->setBold(true);
            //     $rowCount++;


            //     $s_awal = 0;
            //     $kas_um = 0;
            //     $piutang_ppn = 0;
            //     $piutang_dpp = 0;
            //     $piutang_total = 0;
            //     $pelunasan = 0;
            //     $retur_ppn = 0;
            //     $retur_dpp = 0;
            //     $retur_total = 0;
            //     $diskon_ppn = 0;
            //     $diskon_dpp = 0;
            //     $diskon_total = 0;
            //     $uang_muka = 0;
            //     $koreksi = 0;
            //     $saldo_akhir = 0;
            //     $num  = 1;
            //     foreach ($head['tmp_data'] as $row) {
            //         $object->getActiveSheet()->SetCellValue('A' . $rowCount, ($num++));
            //         $object->getActiveSheet()->SetCellValue('B' . $rowCount, $row['nama_partner']);
            //         $object->getActiveSheet()->SetCellValue('C' . $rowCount, $row['saldo_awal']);
            //         $object->getActiveSheet()->SetCellValue('D' . $rowCount, $row['total_kas_um']);
            //         $object->getActiveSheet()->SetCellValue('E' . $rowCount, $row['dpp_piutang']);
            //         $object->getActiveSheet()->SetCellValue('F' . $rowCount, $row['ppn_piutang']);
            //         $object->getActiveSheet()->SetCellValue('G' . $rowCount, $row['total_piutang_dpp_ppn']);
            //         $object->getActiveSheet()->SetCellValue('H' . $rowCount, $row['pelunasan']);
            //         $object->getActiveSheet()->SetCellValue('I' . $rowCount, $row['dpp_retur']);
            //         $object->getActiveSheet()->SetCellValue('J' . $rowCount, $row['ppn_retur']);
            //         $object->getActiveSheet()->SetCellValue('K' . $rowCount, $row['total_retur_dpp_ppn']);
            //         $object->getActiveSheet()->SetCellValue('L' . $rowCount, $row['dpp_diskon']);
            //         $object->getActiveSheet()->SetCellValue('M' . $rowCount, $row['ppn_diskon']);
            //         $object->getActiveSheet()->SetCellValue('N' . $rowCount, $row['total_diskon_dpp_ppn']);
            //         $object->getActiveSheet()->SetCellValue('O' . $rowCount, $row['uang_muka']);
            //         $object->getActiveSheet()->SetCellValue('P' . $rowCount, $row['koreksi']);
            //         $object->getActiveSheet()->SetCellValue('Q' . $rowCount, $row['saldo_akhir']);

            //         $object->getActiveSheet()->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('I' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('K' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('L' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('N' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //         $object->getActiveSheet()->getStyle('Q' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');


            //         $s_awal = $s_awal + $row['saldo_awal'];
            //         $kas_um = $kas_um + $row['total_kas_um'];
            //         $piutang_ppn = $piutang_ppn + $row['ppn_piutang'];
            //         $piutang_dpp = $piutang_dpp + $row['dpp_piutang'];
            //         $piutang_total = $piutang_total + $row['total_piutang_dpp_ppn'];
            //         $pelunasan = $pelunasan + $row['pelunasan'];
            //         $retur_ppn = $retur_ppn + $row['ppn_retur'];
            //         $retur_dpp = $retur_dpp + $row['dpp_retur'];
            //         $retur_total = $retur_total + $row['total_retur_dpp_ppn'];
            //         $diskon_ppn = $diskon_ppn + $row['ppn_diskon'];
            //         $diskon_dpp = $diskon_dpp + $row['dpp_diskon'];
            //         $diskon_total = $diskon_total + $row['total_diskon_dpp_ppn'];
            //         $uang_muka = $uang_muka + $row['uang_muka'];
            //         $koreksi = $koreksi + $row['koreksi'];
            //         $saldo_akhir =  $saldo_akhir + $row['saldo_akhir'];
            //         $rowCount++;
            //     }

            //     $object->getActiveSheet()->SetCellValue('A' . $rowCount, 'Total : '.$head['gol_nama']);
            //     $sheet->getStyle("A" . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            //     $sheet->mergeCells('A' . $rowCount . ':B' . $rowCount);
            //     // $object->getActiveSheet()->SetCellValue('E'.$rowCount, $s_awal);
            //     $object->getActiveSheet()->SetCellValue('c' . $rowCount, $s_awal);
            //     $object->getActiveSheet()->SetCellValue('D' . $rowCount, $kas_um);
            //     $object->getActiveSheet()->SetCellValue('E' . $rowCount, $piutang_dpp);
            //     $object->getActiveSheet()->SetCellValue('F' . $rowCount, $piutang_ppn);
            //     $object->getActiveSheet()->SetCellValue('G' . $rowCount, $piutang_total);
            //     $object->getActiveSheet()->SetCellValue('H' . $rowCount, $pelunasan);
            //     $object->getActiveSheet()->SetCellValue('I' . $rowCount, $retur_dpp);
            //     $object->getActiveSheet()->SetCellValue('J' . $rowCount, $retur_ppn);
            //     $object->getActiveSheet()->SetCellValue('K' . $rowCount, $retur_total);
            //     $object->getActiveSheet()->SetCellValue('L' . $rowCount, $diskon_dpp);
            //     $object->getActiveSheet()->SetCellValue('M' . $rowCount, $diskon_ppn);
            //     $object->getActiveSheet()->SetCellValue('N' . $rowCount, $diskon_total);
            //     $object->getActiveSheet()->SetCellValue('O' . $rowCount, $uang_muka);
            //     $object->getActiveSheet()->SetCellValue('P' . $rowCount, $koreksi);
            //     $object->getActiveSheet()->SetCellValue('Q' . $rowCount, $saldo_akhir);

            //     // $object->getActiveSheet()->getStyle('E'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('I' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('K' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('L' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('M' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('N' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('O' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('P' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle('Q' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            //     $object->getActiveSheet()->getStyle("A" . $rowCount . ":Q" . $rowCount)->getFont()->setBold(true);
            //     $rowCount++;
            // }

            $headerRow1 = 5;
            $headerRow2 = 6;

            $headers = [
                ['label'=>'No',          'rowspan'=>2, 'width'=>'auto'],
                ['label'=>'Customer',    'rowspan'=>2, 'width'=>35],
                ['label'=>'Saldo Awal',  'rowspan'=>2, 'width'=>20],

                ['label'=>'Piutang',     'colspan'=>3, 'sub'=>['DPP','PPN','Total'], 'width'=>15],
                ['label'=>'Pelunasan',   'rowspan'=>2, 'width'=>20],

                ['label'=>'Retur',       'colspan'=>3, 'sub'=>['DPP','PPN','Total'], 'width'=>15],
                ['label'=>'Diskon',      'colspan'=>3, 'sub'=>['DPP','PPN','Total'], 'width'=>15],

                ['label'=>'Uang Muka',   'colspan'=>2, 'sub'=>['Baru','Pelunasan'], 'width'=>15],
                ['label'=>'Koreksi',     'rowspan'=>2, 'width'=>20],
                ['label'=>'Refund',      'rowspan'=>2, 'width'=>20],

                ['label'=>'Saldo Akhir', 'rowspan'=>2, 'width'=>20],
                ['label'=>'Deposit',     'colspan'=>2, 'sub'=>['Baru','Pelunasan'], 'width'=>15],
            ];


            $colIndex = 0;

            foreach ($headers as $h) {

                $startCol = PHPExcel_Cell::stringFromColumnIndex($colIndex);

                if (isset($h['colspan'])) {
                    $endCol = PHPExcel_Cell::stringFromColumnIndex($colIndex + $h['colspan'] - 1);
                    $sheet->mergeCells("$startCol$headerRow1:$endCol$headerRow1");
                    $colIndex += $h['colspan'];
                } else {
                    // rowspan = 2
                    $sheet->mergeCells("$startCol$headerRow1:$startCol$headerRow2");
                    $colIndex++;
                }

                $sheet->setCellValue("$startCol$headerRow1", $h['label']);
            }

            $colIndex = 0;

            foreach ($headers as $h) {

                // Kolom rowspan (No, Customer, Pelunasan, Koreksi, Saldo Akhir)
                if (isset($h['rowspan'])) {
                    $colIndex++;        // ❗ kolom tetap TERPAKAI
                    continue;           // ❗ tapi tidak boleh ditulis sub-header
                }

                // Kolom yang punya sub header
                foreach ($h['sub'] as $subLabel) {
                    $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);
                    $sheet->setCellValue($col.$headerRow2, $subLabel);
                    $colIndex++;
                }
            }

            $colIndex = 0;

            foreach ($headers as $h) {

                // HEADER ROWSPAN (1 kolom)
                if (isset($h['rowspan'])) {

                    $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);

                    if ($h['width'] === 'auto') {
                        $sheet->getColumnDimension($col)->setAutoSize(true);
                    } else {
                        $sheet->getColumnDimension($col)->setWidth($h['width']);
                    }

                    $colIndex++;
                    continue;
                }

                // HEADER COLSPAN (banyak sub kolom)
                foreach ($h['sub'] as $_) {

                    $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);

                    $sheet->getColumnDimension($col)->setWidth($h['width']);

                    $colIndex++;
                }
            }


            $sheet->getStyle("A5:" . PHPExcel_Cell::stringFromColumnIndex($colIndex - 1) . "6")
                ->applyFromArray($styleArrayColor);

            $sheet->getStyle("A5:" . PHPExcel_Cell::stringFromColumnIndex($colIndex - 1) . "6")
                ->getAlignment()
                ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER)
                ->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $rowCount = 7;

            $bodyMap = [
                'no',
                'nama_partner',
                'saldo_awal',

                ['dpp_piutang','ppn_piutang','total_piutang_dpp_ppn'],
                'pelunasan',

                ['dpp_retur','ppn_retur','total_retur_dpp_ppn'],
                ['dpp_diskon','ppn_diskon','total_diskon_dpp_ppn'],

                ['um_baru','um_pelunasan'],
                'koreksi',
                'refund',

                'saldo_akhir',
                ['depo_baru','depo_pelunasan'],
            ];

            $totalCols = 0;
            foreach ($bodyMap as $m) {
                $totalCols += is_array($m) ? count($m) : 1;
            }
            $lastCol = PHPExcel_Cell::stringFromColumnIndex($totalCols - 1);


            $data = $this->proses_data($tgl_dari, $tgl_sampai, $checkhidden, $currency);

            foreach ($data as $head) {

                /* ===============================
                * BARIS GOLONGAN
                * =============================== */
                $sheet->setCellValue('A'.$rowCount, $head['gol_nama']);
                $sheet->mergeCells("A{$rowCount}:{$lastCol}{$rowCount}");
                $sheet->getStyle("A{$rowCount}:{$lastCol}{$rowCount}")
                    ->getFont()->setBold(true);
                $rowCount++;

                /* ===============================
                * INIT TOTAL
                * =============================== */
                $totals = [];
                $num = 1;

                /* ===============================
                * BODY DATA
                * =============================== */
                foreach ($head['tmp_data'] as $row) {

                    $colIndex = 0;

                    foreach ($bodyMap as $map) {

                        // FIELD TUNGGAL
                        if (is_string($map)) {

                            $value = ($map === 'no') ? $num : ($row[$map] ?? 0);

                            $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);
                            $sheet->setCellValue($col.$rowCount, $value);

                            if ($map !== 'no' && $map !== 'nama_partner') {
                                $sheet->getStyle($col.$rowCount)
                                    ->getNumberFormat()
                                    ->setFormatCode('#,##0.00');
                            }

                            if ($map !== 'no' && $map !== 'nama_partner') {
                                $totals[$map] = ($totals[$map] ?? 0) + (float)$value;
                            }

                            $colIndex++;
                            continue;
                        }

                        // FIELD GROUP
                        foreach ($map as $field) {

                            $value = $row[$field] ?? 0;
                            $col   = PHPExcel_Cell::stringFromColumnIndex($colIndex);

                            $sheet->setCellValue($col.$rowCount, $value);
                            $sheet->getStyle($col.$rowCount)
                                ->getNumberFormat()
                                ->setFormatCode('#,##0.00');

                            $totals[$field] = ($totals[$field] ?? 0) + (float)$value;

                            $colIndex++;
                        }
                    }

                    $num++;
                    $rowCount++;
                }

                /* ===============================
                * TOTAL PER GOLONGAN
                * =============================== */
                $sheet->setCellValue("A{$rowCount}", 'Total : '.$head['gol_nama']);
                $sheet->mergeCells("A{$rowCount}:B{$rowCount}");
                $sheet->getStyle("A{$rowCount}")
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $colIndex = 2; // mulai dari Saldo Awal

                foreach ($bodyMap as $map) {

                    if ($map === 'no' || $map === 'nama_partner') {
                        continue;
                    }

                    if (is_string($map)) {
                        $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);
                        $sheet->setCellValue($col.$rowCount, $totals[$map] ?? 0);
                        $sheet->getStyle($col.$rowCount)
                            ->getNumberFormat()
                            ->setFormatCode('#,##0.00');
                        $colIndex++;
                        continue;
                    }

                    foreach ($map as $field) {
                        $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);
                        $sheet->setCellValue($col.$rowCount, $totals[$field] ?? 0);
                        $sheet->getStyle($col.$rowCount)
                            ->getNumberFormat()
                            ->setFormatCode('#,##0.00');
                        $colIndex++;
                    }
                }

                $sheet->getStyle("A{$rowCount}:{$lastCol}{$rowCount}")
                    ->getFont()->setBold(true);

                $rowCount++;
            }
            $sheet->freezePane('A7');

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $object->save('php://output');

            $xlsData = ob_get_contents();
            ob_end_clean();

            $name_file = 'Buku Besar Pembantu Piutang ' . $periode . '.xlsx';

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

    public function export_pdf()
    {

        $this->load->library('dompdflib');
        $data_arr  = json_decode($this->input->get('params'), true);

        $tgl_dari   = '';
        $tgl_sampai = '';
        $checkhidden = '';
        $currency = '';
        foreach ($data_arr as $rows) {
            $tgl_dari = $rows['tgldari'];
            $tgl_sampai = $rows['tglsampai'];
            $checkhidden = $rows['checkhidden'];
            $currency = $rows['currency'];
        }

        $data = $this->proses_data($tgl_dari, $tgl_sampai, $checkhidden, $currency);

        $data['list'] = $data;
        $data['tgl_dari']   = tgl_indo(date('d-m-Y', strtotime($tgl_dari)));
        $data['tgl_sampai'] = tgl_indo(date('d-m-Y', strtotime($tgl_sampai)));
        $cnt = $this->load->view('accounting/v_bukubesar_pembantu_piutang_pdf', $data, true);
        $this->dompdflib->generate($cnt, 'Buku Besar Pembantu Piutang', 0, 'A3', 'Landscape');
    }
}
