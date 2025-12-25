<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');


class Mutasipenjualan extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();
        // $this->load->model('_module');
        $this->load->model('m_mutasipenjualan');
    }

    public function index()
    {
        $data['id_dept'] = 'RMP';
        $this->load->view('report/v_mutasi_penjualan', $data);
    }


    function loadData()
    {
        try {
            //code...
            $filter = $this->_collectFilter();

            $data       = $this->proses_data();
            $callback   = array('status' => 'success', 'message' => 'berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'record' => $data);
            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($callback));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('status'  => 'failed', 'message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }



    private function _collectFilter()
    {
        $post = $this->input->post();

        return [
            'check_tgl'  => !empty($post['check_tgl']), // true / false
            'partner'    => !empty($post['partner'])    ? $post['partner']    : null,
            'tgldari'    => !empty($post['tgldari'])    ? $this->_formatDate($post['tgldari']) : null,
            'tglsampai'  => !empty($post['tglsampai'])  ? $this->_formatDate($post['tglsampai']) : null,
            'no_faktur'  => !empty($post['no_faktur'])  ? $post['no_faktur']  : null,
            'no_sj'      => !empty($post['no_sj'])      ? $post['no_sj']      : null,
            'tipe'       => !empty($post['tipe'])       ? $post['tipe']       : null,
            'lunas'      => !empty($post['status_lunas'])       ? $post['status_lunas']       : 0,
        ];
    }


    private function _formatDate($date)
    {
        // dari: 18-December-2025 → 2025-12-18
        return date('Y-m-d', strtotime($date));
    }


    function proses_data()
    {
        $filter = $this->_collectFilter();

        // =========================
        // WHERE DASAR
        // =========================
        $where = [
            'fak.status' => 'confirm',
            // 'fak.lunas'  => 0
        ];

        // =========================
        // FILTER NON ARRAY
        // =========================
        if ($filter['tipe'] != 'all') {
            $where['fak.tipe'] = $filter['tipe'];
        }

        if ($filter['lunas'] != 'all') {
            $where['fak.lunas'] = $filter['lunas'];
        }

        // =========================
        // TANGGAL
        // =========================
        $date_filter = [];
        if ($filter['check_tgl']) {
            if (!empty($filter['tgldari'])) {
                $date_filter['from'] = $filter['tgldari'];
            }
            if (!empty($filter['tglsampai'])) {
                $date_filter['to'] = $filter['tglsampai'];
            }
        }

        // =========================
        // TEXT FILTER
        // =========================
        $like = [];
        if (!empty($filter['no_faktur'])) {
            $like['fak.no_faktur_internal'] = $filter['no_faktur'];
        }
        if (!empty($filter['no_sj'])) {
            $like['fak.no_sj'] = $filter['no_sj'];
        }

        // =========================
        // PARTNER (ARRAY / SINGLE)
        // =========================
        $partner = $filter['partner'];

        // =========================
        // HEAD DATA
        // =========================
        $head = $this->m_mutasipenjualan->get_group_partner($where, $partner, $date_filter, $like);

        if (empty($head)) {
            return [];
        }

        // =========================
        // DETAIL
        // =========================
        $result = [];
        $sisa   = 0;
        foreach ($head as $row) {

            $detail = $this->m_mutasipenjualan->get_detail_by_partner($date_filter, $row->partner_id, $where, $like);
            $items = [];
            $sisa   = 0;

            foreach ($detail as $d) {
                if($d->lunas == 1 || $d->lunas == '1'){
                    $sisa = 0;
                } else {
                    $sisa   = (float) $d->total_piutang - (float) $d->total_pelunasan -  (float) $d->total_retur - (float) $d->total_diskon;
                }
                $items[] = [
                    'tgl_faktur'         => date('Y-m-d', strtotime($d->tanggal)),
                    'no_faktur'          => $d->no_faktur,
                    'no_sj'              => $d->no_sj,
                    'tipe'               => ucfirst($d->tipe),
                    'dpp_piutang'        => (float) $d->dpp_piutang,
                    'ppn_piutang'        => (float) $d->ppn_piutang,
                    'total_piutang'      => (float) $d->total_piutang,
                    'no_pelunasan'       => $d->no_pelunasan,
                    'tgl_pelunasan'      => $d->tanggal_pelunasan,
                    'no_bukti_pelunasan' => $d->no_bukti_pelunasan,
                    'total_pelunasan'    => (float) $d->total_pelunasan,
                    'tgl_retur'          => $d->tanggal_retur,
                    'no_bukti_retur'     => $d->no_bukti_retur,
                    'dpp_retur'          => (float) $d->dpp_retur,
                    'ppn_retur'          => (float) $d->ppn_retur,
                    'total_retur'        => (float) $d->total_retur,
                    'dpp_diskon'         => (float) $d->dpp_diskon,
                    'ppn_diskon'         => (float) $d->ppn_diskon,
                    'total_diskon'       => (float) $d->total_diskon,
                    'sisa'               => $sisa,
                    'lunas'              => ($d->lunas === "1" || $d->lunas === 0) ? "Lunas" : "Belum Lunas"


                ];
            }

            $result[] = [
                'partner_id'     => $row->partner_id,
                'nama_partner'   => $row->nama_partner,
                'tmp_data_items' => $items
            ];
        }

        return $result;
    }


    function export_excel()
{
    try {

        $this->load->library('excel');
        ob_start();

        $excel = new PHPExcel();
        $sheet = $excel->setActiveSheetIndex(0);
        $sheet->setTitle('Mutasi Penjualan');

        /* =======================
         * JUDUL
         * ======================= */
        $sheet->setCellValue('A1', 'PT. HEKSATEX INDAH');
        $sheet->mergeCells('A1:U1');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        $sheet->setCellValue('A2', 'MUTASI PENJUALAN');
        $sheet->mergeCells('A2:U2');
        $sheet->getStyle('A2')->getFont()->setBold(true);
        $sheet->getStyle('A2')->getAlignment()->setHorizontal('center');

        /* =======================
         * STYLE HEADER
         * ======================= */
        $styleHeader = [
            'font' => ['bold' => true],
            'fill' => [
                'type'  => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => ['rgb' => 'D3D3D3']
            ],
            'alignment' => [
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]
            ]
        ];

        /* =======================
         * HEADER CONFIG
         * ======================= */
        $headerRow1 = 5;
        $headerRow2 = 6;

        $headers = [
            ['label' => 'No',         'rowspan' => 2, 'width' => 5],
            ['label' => 'Tgl Faktur', 'rowspan' => 2, 'width' => 15],
            ['label' => 'No Faktur',  'rowspan' => 2, 'width' => 20],
            ['label' => 'No SJ',      'rowspan' => 2, 'width' => 20],
            ['label' => 'Tipe',       'rowspan' => 2, 'width' => 10],

            ['label' => 'Penjualan', 'colspan' => 3, 'sub' => ['DPP', 'PPN', 'Total']],
            ['label' => 'Pelunasan', 'colspan' => 4, 'sub' => ['Tgl', 'No Pelunasan', 'No Bukti', 'Total']],
            ['label' => 'Retur',     'colspan' => 5, 'sub' => ['Tgl', 'No Bukti', 'DPP', 'PPN', 'Total']],
            ['label' => 'Diskon',    'colspan' => 3, 'sub' => ['DPP', 'PPN', 'Total']],

            ['label' => 'Sisa Piutang', 'rowspan' => 2, 'width' => 18],
            ['label' => 'Status',       'rowspan' => 2, 'width' => 10],

        ];

        /* =======================
         * DRAW HEADER
         * ======================= */
        $colIndex = 0;
        foreach ($headers as $h) {
            $startCol = PHPExcel_Cell::stringFromColumnIndex($colIndex);
            if (isset($h['colspan'])) {
                $endCol = PHPExcel_Cell::stringFromColumnIndex($colIndex + $h['colspan'] - 1);
                $sheet->mergeCells("$startCol$headerRow1:$endCol$headerRow1");
                $colIndex += $h['colspan'];
            } else {
                $sheet->mergeCells("$startCol$headerRow1:$startCol$headerRow2");
                $colIndex++;
            }
            $sheet->setCellValue("$startCol$headerRow1", $h['label']);
        }

        $colIndex = 0;
        foreach ($headers as $h) {
            if (isset($h['rowspan'])) {
                $colIndex++;
                continue;
            }
            foreach ($h['sub'] as $sub) {
                $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);
                $sheet->setCellValue($col . $headerRow2, $sub);
                $colIndex++;
            }
        }

        $lastCol = PHPExcel_Cell::stringFromColumnIndex($colIndex - 1);
        $sheet->getStyle("A{$headerRow1}:{$lastCol}{$headerRow2}")
            ->applyFromArray($styleHeader);

        /* =======================
         * BODY MAP
         * ======================= */
        $bodyMap = [
            'no',
            'tgl_faktur',
            'no_faktur',
            'no_sj',
            'tipe',
            ['dpp_piutang', 'ppn_piutang', 'total_piutang'],
            ['tgl_pelunasan', 'no_pelunasan', 'no_bukti_pelunasan', 'total_pelunasan'],
            ['tgl_retur', 'no_bukti_retur', 'dpp_retur', 'ppn_retur', 'total_retur'],
            ['dpp_diskon', 'ppn_diskon', 'total_diskon'],
            'sisa',
            'lunas'
        ];

        /* =======================
         * DATA
         * ======================= */
        $rowCount = 7;
        $data = $this->proses_data();

        foreach ($data as $head) {

            // JUDUL PARTNER
            $sheet->setCellValue("A{$rowCount}", $head['nama_partner']);
            $sheet->mergeCells("A{$rowCount}:{$lastCol}{$rowCount}");
            $sheet->getStyle("A{$rowCount}")->getFont()->setBold(true);
            $rowCount++;

            $no = 1;

            // INIT TOTAL PARTNER
            $sum = [
                'dpp_piutang' => 0, 'ppn_piutang' => 0, 'total_piutang' => 0,
                'total_pelunasan' => 0,
                'dpp_retur' => 0, 'ppn_retur' => 0, 'total_retur' => 0,
                'dpp_diskon' => 0, 'ppn_diskon' => 0, 'total_diskon' => 0,
                'sisa' => 0
            ];

            foreach ($head['tmp_data_items'] as $row) {

                $colIndex = 0;

                foreach ($bodyMap as $map) {

                    if (is_string($map)) {

                        $value = ($map === 'no') ? $no : ($row[$map] ?? '');
                        $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);
                        $sheet->setCellValue($col . $rowCount, $value);

                        // FORMAT ANGKA (KECUALI NO)
                        if (is_numeric($value) && $map !== 'no') {
                            $sheet->getStyle($col . $rowCount)
                                ->getNumberFormat()
                                ->setFormatCode('#,##0.00');
                        }

                        $colIndex++;
                        continue;
                    }

                    foreach ($map as $field) {
                        $value = $row[$field] ?? '';
                        $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);
                        $sheet->setCellValue($col . $rowCount, $value);

                        if (is_numeric($value)) {
                            $sheet->getStyle($col . $rowCount)
                                ->getNumberFormat()
                                ->setFormatCode('#,##0.00');
                        }

                        // HITUNG TOTAL
                        if (isset($sum[$field])) {
                            $sum[$field] += (float)$value;
                        }

                        $colIndex++;
                    }
                }

                $no++;
                $rowCount++;
            }

            /* =======================
             * TOTAL PER PARTNER
             * ======================= */
            $sheet->setCellValue("A{$rowCount}", 'TOTAL ' . strtoupper($head['nama_partner']));
            $sheet->mergeCells("A{$rowCount}:E{$rowCount}");
            $sheet->getStyle("A{$rowCount}")->getFont()->setBold(true);

            $totalCols = [
                'dpp_piutang', 'ppn_piutang', 'total_piutang',
                null, null, null, 'total_pelunasan',
                null, null, 'dpp_retur', 'ppn_retur', 'total_retur',
                'dpp_diskon', 'ppn_diskon', 'total_diskon',
                'sisa'
            ];

            $colIndex = 5;
            foreach ($totalCols as $key) {
                $col = PHPExcel_Cell::stringFromColumnIndex($colIndex);
                if ($key && isset($sum[$key])) {
                    $sheet->setCellValue($col . $rowCount, $sum[$key]);
                    $sheet->getStyle($col . $rowCount)
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                    $sheet->getStyle($col . $rowCount)->getFont()->setBold(true);
                }
                $colIndex++;
            }

            $rowCount += 2;
        }

        $sheet->freezePane('A7');

        /* =======================
         * OUTPUT
         * ======================= */
        $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $writer->save('php://output');

        $xlsData = ob_get_contents();
        ob_end_clean();

        echo json_encode([
            'op'       => 'ok',
            'file'     => "data:application/vnd.ms-excel;base64," . base64_encode($xlsData),
            'filename' => 'Mutasi Penjualan.xlsx'
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            'op' => 'error',
            'message' => $e->getMessage()
        ]);
    }
}

}
