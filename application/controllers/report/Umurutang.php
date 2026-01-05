<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Umurutang extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model('m_outstandinginvoice');
    }

    public function index()
    {
        $id_dept        = 'RUU';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/v_umur_utang', $data);
    }


    function loadData()
    {
        try {
            //code...
            $partner    = $this->input->post('partner');
            $data       = $this->proses_data($partner);
            $callback   = array('status' => 'success', 'message' => 'berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'header' => array_merge(['No', 'Supplier', 'Total Hutang'], $data[0]), 'record' => $data[1]);
            $this->output->set_status_header(200)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode($callback));
        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
    }

    function proses_data($partner)
    {
        // Nama-nama bulan
        $bulanNames = [
            "Januari",
            "Februari",
            "Maret",
            "April",
            "Mei",
            "Juni",
            "Juli",
            "Agustus",
            "September",
            "Oktober",
            "November",
            "Desember"
        ];

        $bulanSekarang = date('n'); // 1-12
        $tahunSekarang = date('Y');

        // Buat array nama bulan + tahun mundur 4 bulan ke belakang
        $bulanLabels = [];
        for ($i = 0; $i < 4; $i++) {
            $bulanIndex = $bulanSekarang - $i;
            $tahun = $tahunSekarang;

            // Kalau mundur ke tahun sebelumnya
            if ($bulanIndex <= 0) {
                $bulanIndex += 12;
                $tahun -= 1;
            }

            $bulanLabels[] = $bulanNames[$bulanIndex - 1] . " " . $tahun;
        }

        // Label terakhir untuk “lebih dari 3 bulan”
        $bulanIndexLebih3 = $bulanSekarang - 3;
        $tahunLebih3 = $tahunSekarang;
        if ($bulanIndexLebih3 <= 0) {
            $bulanIndexLebih3 += 12;
            $tahunLebih3 -= 1;
        }
        $bulanLabels[] = "> " . $bulanNames[$bulanIndexLebih3 - 1] . " " . $tahunLebih3;

        // Ambil data supplier
        $where_partner = ['inv.id_supplier' => $partner];
        $where_params = ["inv.status" => "done", "inv.lunas " => 0];
        $result = (!empty($partner)) ? array_merge($where_partner, $where_params) : $where_params;

        $tmp_data_items = [];
        $data = $this->m_outstandinginvoice->get_list_aging_utang_supplier($result);

        foreach ($data as $datas) {
            $tmp_data_items[] = array(
                'id_partner'        => $datas->id_supplier,
                'nama_partner'      => $datas->nama_partner,
                'total_hutang'      => (float) $datas->total_hutang,
                'hutang_bulan_ini'  => (float) $datas->hutang_bulan_ini,
                'hutang_bulan_1'    => (float) $datas->hutang_bulan_1,
                'hutang_bulan_2'    => (float) $datas->hutang_bulan_2,
                'hutang_bulan_3'    => (float) $datas->hutang_bulan_3,
                'hutang_lebih_dari_3_bulan' => (float) $datas->hutang_lebih_dari_3_bulan,
            );
        }

        return array($bulanLabels, $tmp_data_items);
    }

    function export_excel()
    {
        try {
            //code...
            $this->load->library('excel');
            $partner = $this->input->post('partner');
            $tgl_now = date("Y-m-d");

            ob_start();
            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);


            // $object->createSheet();
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Umur Utang');
            $activeSheet = $object->getActiveSheet();
            // $getSheet  = $object->getSheet(0);
            $activeSheet->setShowGridlines(false);

            $rowCount = 1;
            $loop     = 1;

            $periode = tgl_indo(date('d-m-Y', strtotime($tgl_now)));
            // set Judul
            $sheet->SetCellValue('A' . $rowCount, 'PT. HEKSATEX INDAH');
            $sheet->mergeCells('A' . $rowCount . ':I' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // set Judul
            $rowCount++;
            $sheet->SetCellValue('A' . $rowCount, 'UMUR UTANG (AGING)');
            $sheet->mergeCells('A' . $rowCount . ':I' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // set periode
            $rowCount = 3;
            $sheet->SetCellValue('A' . $rowCount, 'Per Tgl. ' . $periode);
            $sheet->mergeCells('A' . $rowCount . ':I' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            //bold huruf
            $activeSheet->getStyle("A1:I5")->getFont()->setBold(true);

            // Border 
            $styleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );

            $data = $this->proses_data($partner);
            $rowCount = 6;
            $num      = 1;

            // thead
            $this->create_thead($rowCount, $activeSheet, $activeSheet, $data[0]);

            $total_hutang           = 0;
            $total_hutang_bulan_ini = 0;
            $total_hutang_bulan_1   = 0;
            $total_hutang_bulan_2   = 0;
            $total_hutang_bulan_3   = 0;
            $total_hutang_lebih_dari_3   = 0;
            $rowCount++;

            foreach ($data[1] as $datas) {
                $activeSheet->SetCellValue('A' . $rowCount, ($num));
                $activeSheet->SetCellValue('B' . $rowCount, $datas['nama_partner']);
                $activeSheet->SetCellValue('C' . $rowCount, $datas['total_hutang']);
                $activeSheet->SetCellValue('D' . $rowCount, $datas['hutang_bulan_ini']);
                $activeSheet->SetCellValue('E' . $rowCount, $datas['hutang_bulan_1']);
                $activeSheet->SetCellValue('F' . $rowCount, $datas['hutang_bulan_2']);
                $activeSheet->SetCellValue('G' . $rowCount, $datas['hutang_bulan_3']);
                $activeSheet->SetCellValue('H' . $rowCount, $datas['hutang_lebih_dari_3_bulan']);

                $activeSheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
                $activeSheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
                $activeSheet->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
                $activeSheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
                $activeSheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
                $activeSheet->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');

                $num++;
                $rowCount++;
                $total_hutang           = $total_hutang + $datas['total_hutang'];
                $total_hutang_bulan_ini = $total_hutang_bulan_ini + $datas['hutang_bulan_ini'];
                $total_hutang_bulan_1   = $total_hutang_bulan_1 + $datas['hutang_bulan_1'];
                $total_hutang_bulan_2   = $total_hutang_bulan_2 + $datas['hutang_bulan_2'];
                $total_hutang_bulan_3   = $total_hutang_bulan_3 + $datas['hutang_bulan_3'];
                $total_hutang_lebih_dari_3   = $total_hutang_lebih_dari_3 + $datas['hutang_lebih_dari_3_bulan'];
            }

            $num = 1;

            // summary
            $activeSheet->SetCellValue('A' . $rowCount, 'Total : ');
            $activeSheet->mergeCells('A' . $rowCount . ':B' . $rowCount);
            $activeSheet->getStyle("A" . $rowCount . ":I" . $rowCount)->getFont()->setBold(true);
            $activeSheet->getStyle("A" . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $activeSheet->SetCellValue('C' . $rowCount, $total_hutang);
            $activeSheet->SetCellValue('D' . $rowCount, $total_hutang_bulan_ini);
            $activeSheet->SetCellValue('E' . $rowCount, $total_hutang_bulan_1);
            $activeSheet->SetCellValue('F' . $rowCount, $total_hutang_bulan_2);
            $activeSheet->SetCellValue('G' . $rowCount, $total_hutang_bulan_3);
            $activeSheet->SetCellValue('H' . $rowCount, $total_hutang_lebih_dari_3);

            $activeSheet->getStyle('C' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
            $activeSheet->getStyle('D' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
            $activeSheet->getStyle('E' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
            $activeSheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
            $activeSheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
            $activeSheet->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');


            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $object->save('php://output');

            $xlsData = ob_get_contents();
            ob_end_clean();

            $name_file = 'Umur Utang Per Tanggal ' . $periode . '.xlsx';

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

    function create_thead($rowCount, $activeSheet, $getSheet, $data)
    {

        $styleArrayColor = array(
            'font'  => array(
                'bold'  => true,
                // 'color' => array('rgb' => 'FFFFFF'),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'D3D3D3')
            ),
            'alignment' => array(
                // 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $data_bulan = $data; // hutang_bulan_ini, hutang_bulan_1,hutang_bulan_2, hutang_bulan_3, hutang_lebih_dari_3_bulan
        $table_head_columns  = array_merge(['No', 'Supplier', 'Total Hutang'], $data_bulan);

        $column = 0;
        foreach ($table_head_columns as $field) {
            $activeSheet->setCellValueByColumnAndRow($column, $rowCount, $field);
            $column++;
        }

        // set width and border
        $index_header = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
        $loop = 0;
        foreach ($index_header as $val) {

            if ($loop == 0) {
                $getSheet->getColumnDimension($val)->setAutoSize(true); // index A
            } else if ($loop ==  1) {
                $getSheet->getColumnDimension($val)->setWidth(25); // index B
            } else if ($loop >= 2) {
                $getSheet->getColumnDimension($val)->setWidth(20); // index ->C
                $getSheet->getStyle($val . '' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            }

            $loop++;
        }
        $getSheet->getRowDimension($rowCount)->setRowHeight(24); // height acc
        $getSheet->getStyle("A" . $rowCount . ":H" . $rowCount)->applyFromArray($styleArrayColor);


        return;
    }


    public function export_pdf1()
    {

        $this->load->library('dompdflib');
        $data_arr  = json_decode($this->input->get('params'), true);
        $tgl_now   = date("Y-m-d");
        $partner   = '';
        foreach ($data_arr as $rows) {
            $partner = $rows['partner'];
        }

        $data1 = $this->proses_data($partner);

        $data['header'] = array_merge(['No', 'Supplier', 'Total Hutang'], $data1[0]);
        $data['items']  = $data1[1];
        $data['periode'] = tgl_indo(date('d-m-Y', strtotime($tgl_now)));
        $cnt = $this->load->view('report/v_umur_utang_pdf', $data, true);
        $this->dompdflib->generate($cnt);
    }


    public function export_pdf()
    {
        $partner = $this->input->get('partner');

        // =============================
        // AMBIL DATA
        // =============================
        list($bulanLabels, $items) = $this->proses_data($partner);

        $this->load->library('Pdf');
        $periode = tgl_indo(date('d-m-Y'));

        // =============================
        // PAGE SETUP
        // =============================
        $LEFT = 5;
        $TOP  = 30;

        $PAGE_BOTTOM = 285;
        $FOOTER_Y    = -15;

        // TOTAL WIDTH = 194 mm
        $w = [
            6,    // No
            48,   // Supplier
            26,   // Total
            23,   // Bulan Ini
            23,   // 1 Bulan
            23,   // 2 Bulan
            23,   // 3 Bulan
            26    // > 3 Bulan
        ];

        $pdf = new Pdf('P', 'mm', 'A4');
        $pdf->AliasNbPages();
        $pdf->SetMargins($LEFT, $TOP, $LEFT);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        // =============================
        // HEADER
        // =============================
        $renderHeader = function () use ($pdf, $LEFT, $periode) {
            $pdf->SetY(8);
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 6, 'PT. HEKSATEX INDAH', 0, 1, 'C');

            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 6, 'UMUR UTANG (AGING)', 0, 1, 'C');

            $pdf->SetFont('Arial', '', 10);
            $pdf->Cell(0, 6, 'Per Tgl. ' . $periode, 0, 1, 'C');

            $pdf->Ln(4);
            $pdf->SetX($LEFT);
        };

        // =============================
        // FOOTER
        // =============================
        $renderFooter = function () use ($pdf, $FOOTER_Y) {
            $pdf->SetY($FOOTER_Y);
            $pdf->SetFont('Arial', '', 7);
            $pdf->Cell(0, 8, 'Halaman ' . $pdf->PageNo() . ' dari {nb}', 0, 0, 'C');
        };

        // =============================
        // TABLE HEADER
        // =============================
        $renderTableHeader = function () use ($pdf, $w, $LEFT, $bulanLabels) {

            $headers = array_merge(
                ['No', 'Supplier', 'Total'],
                $bulanLabels
            );

            $pdf->SetX($LEFT);
            $pdf->SetFont('Arial', 'B', 8);
            $pdf->SetFillColor(220, 220, 220);

            foreach ($headers as $i => $txt) {
                $align = ($i >= 2) ? 'R' : 'C';
                $pdf->Cell($w[$i], 7, $txt, 1, 0, $align, true);
            }
            $pdf->Ln();
            $pdf->SetFont('Arial', '', 7);
        };

        // =============================
        // UTIL
        // =============================
        $nbLines = function ($width, $text) use ($pdf) {
            return max(1, $pdf->NbLines($width, $text));
        };

        $checkPageBreak = function ($h) use (
            $pdf,
            $PAGE_BOTTOM,
            $renderHeader,
            $renderTableHeader,
            $renderFooter
        ) {
            if ($pdf->GetY() + $h > $PAGE_BOTTOM) {
                $renderFooter();
                $pdf->AddPage();
                $renderHeader();
                $renderTableHeader();
            }
        };

        // =============================
        // DRAW ROW
        // =============================
        $drawRow = function ($row) use ($pdf, $w, $LEFT, $checkPageBreak, $nbLines) {

            $h = $nbLines($w[1], $row[1]) * 6;
            $checkPageBreak($h);

            $x = $LEFT;
            $y = $pdf->GetY();

            foreach ($w as $width) {
                $pdf->Rect($x, $y, $width, $h);
                $x += $width;
            }

            $x = $LEFT;
            foreach ($row as $i => $txt) {

                $pdf->SetXY($x, $y);

                if ($i === 1) {
                    $pdf->MultiCell($w[$i], 6, $txt, 0, 'L');
                } else {
                    $align = ($i >= 2) ? 'R' : 'C';
                    $pdf->Cell($w[$i], $h, $txt, 0, 0, $align);
                }
                $x += $w[$i];
            }
            $pdf->SetY($y + $h);
        };

        // =============================
        // RENDER
        // =============================
        $renderHeader();
        $renderTableHeader();

        $no = 1;
        $total = array_fill(0, 6, 0);

        foreach ($items as $row) {

            $drawRow([
                $no++,
                $row['nama_partner'],
                number_format($row['total_hutang'], 2),
                number_format($row['hutang_bulan_ini'], 2),
                number_format($row['hutang_bulan_1'], 2),
                number_format($row['hutang_bulan_2'], 2),
                number_format($row['hutang_bulan_3'], 2),
                number_format($row['hutang_lebih_dari_3_bulan'], 2),
            ]);

            $total[0] += $row['total_hutang'];
            $total[1] += $row['hutang_bulan_ini'];
            $total[2] += $row['hutang_bulan_1'];
            $total[3] += $row['hutang_bulan_2'];
            $total[4] += $row['hutang_bulan_3'];
            $total[5] += $row['hutang_lebih_dari_3_bulan'];
        }

        // =============================
        // TOTAL
        // =============================
        $checkPageBreak(8);
        $pdf->SetX($LEFT);
        $pdf->SetFont('Arial', 'B', 7);

        $pdf->Cell($w[0] + $w[1], 7, 'Total :', 1, 0, 'R');
        for ($i = 0; $i < 6; $i++) {
            $pdf->Cell($w[$i + 2], 7, number_format($total[$i], 2), 1, 0, 'R');
        }
        $pdf->Ln();

        $renderFooter();
        $pdf->Output('I', 'Aging_Umur_Utang.pdf');
    }
}
