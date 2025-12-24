<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Outstandinginvoice extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model('m_outstandinginvoice');
    }

    public function index()
    {
        $id_dept        = 'ROUTSINV';
        $data['id_dept'] = $id_dept;
        $this->load->view('report/v_outstanding_invoice', $data);
    }


    function loadData()
    {
        try {
            //code...
            $partner    = $this->input->post('partner');
            $data       = $this->proses_data($partner);
            $callback   = array('status' => 'success', 'message' => 'berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'record' => $data);
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
        $where_partner = ['inv.id_supplier' => $partner];
        $where_params = ["inv.status" => "done", "inv.lunas " => 0];
        $result = (!empty($partner)) ? array_merge($where_partner, $where_params) : $where_params;

        $data = $this->m_outstandinginvoice->get_list_invoice_group_partner($result);
        $tmp_data_head   = array();
        $tmp_data_items  = array();
        foreach ($data as $datas) {

            // get_list_invoice_by_partner
            $where2 = ['inv.id_supplier' => $datas->id_supplier];
            $where  = array_merge($where_params, $where2);
            $data2 = $this->m_outstandinginvoice->get_list_invoice_by_partner($where);
            foreach ($data2 as $datas2) {

                $tmp_data_items[] = array(
                    'nama_partner'  => $datas2->nama_partner,
                    'no_invoice'    => $datas2->no_invoice,
                    'no_po'         => $datas2->no_po,
                    'origin'        => $datas2->origin,
                    'tanggal'       => date("Y-m-d", strtotime($datas2->order_date)),
                    'hari'          => $datas2->hari,
                    'currency'      => $datas2->currency,
                    'kurs'          => $datas2->nilai_matauang,
                    'status'        => $datas2->status,
                    'hutang_rp'     => (float) $datas2->hutang_rp,
                    'sisa_hutang_rp' => (float) $datas2->sisa_hutang_rp,
                    'hutang_valas'  => (float) $datas2->hutang_valas,
                    'sisa_hutang_valas' => (float) $datas2->sisa_hutang_valas,
                    'hari'          => $datas2->hari
                );
            }
            $tmp_data_head[] = array(
                'partner_id' => $datas->id_supplier,
                'nama_partner' => $datas->nama_partner,
                'tmp_data_items' => $tmp_data_items
            );


            $where2   = [];
            $tmp_data_items = [];
        }

        return $tmp_data_head;
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
            $sheet->setTitle('Outstanding Invoice');
            $activeSheet = $object->getActiveSheet();
            // $getSheet  = $object->getSheet(0);
            $activeSheet->setShowGridlines(false);

            $rowCount = 1;
            $loop     = 1;

            $periode = tgl_indo(date('d-m-Y', strtotime($tgl_now)));
            // set Judul
            $sheet->SetCellValue('A' . $rowCount, 'PT. HEKSATEX INDAH');
            $sheet->mergeCells('A' . $rowCount . ':K' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // set Judul
            $rowCount++;
            $sheet->SetCellValue('A' . $rowCount, 'OUTSTANDING INVOICE');
            $sheet->mergeCells('A' . $rowCount . ':K' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // set periode
            $rowCount = 3;
            $sheet->SetCellValue('A' . $rowCount, 'Per Tgl. ' . $periode);
            $sheet->mergeCells('A' . $rowCount . ':K' . $rowCount);
            $sheet->getStyle('A' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            //bold huruf
            $activeSheet->getStyle("A1:K5")->getFont()->setBold(true);

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
            $this->create_thead($rowCount, $activeSheet, $activeSheet);
            $total_hutang_rp        = 0;
            $total_sisa_hutang_rp   = 0;
            $total_hutang_valas       = 0;
            $total_sisa_hutang_valas  = 0;
            $rowCount++;
            foreach ($data as $datas) {

                // nama partner
                $activeSheet->SetCellValue('A' . $rowCount, $datas['nama_partner']);
                $activeSheet->mergeCells('A' . $rowCount . ':k' . $rowCount);
                $activeSheet->getStyle("A" . $rowCount . ":K" . $rowCount)->getFont()->setBold(true);
                $rowCount++;

                foreach ($datas['tmp_data_items'] as $datas2) {
                    $activeSheet->SetCellValue('A' . $rowCount, ($num));
                    $activeSheet->SetCellValue('B' . $rowCount, '');
                    $activeSheet->SetCellValue('C' . $rowCount, $datas2['no_invoice']);
                    $activeSheet->SetCellValue('D' . $rowCount, $datas2['no_po']);
                    $activeSheet->SetCellValue('E' . $rowCount, $datas2['origin']);
                    $activeSheet->SetCellValue('F' . $rowCount, date("Y-m-d", strtotime($datas2['tanggal'])));
                    $activeSheet->SetCellValue('G' . $rowCount, $datas2['hutang_rp']);
                    $activeSheet->SetCellValue('H' . $rowCount, $datas2['sisa_hutang_rp']);
                    $activeSheet->SetCellValue('i' . $rowCount, $datas2['hutang_valas']);
                    $activeSheet->SetCellValue('J' . $rowCount, $datas2['sisa_hutang_valas']);
                    $activeSheet->SetCellValue('K' . $rowCount, $datas2['hari']);

                    $activeSheet->getStyle('g' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
                    $activeSheet->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
                    $activeSheet->getStyle('I' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
                    $activeSheet->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');

                    $num++;
                    $rowCount++;
                    $total_hutang_rp = $total_hutang_rp + $datas2['hutang_rp'];
                    $total_sisa_hutang_rp = $total_sisa_hutang_rp + $datas2['sisa_hutang_rp'];
                    $total_hutang_valas = $total_hutang_valas + $datas2['hutang_valas'];
                    $total_sisa_hutang_valas = $total_sisa_hutang_valas + $datas2['sisa_hutang_valas'];
                }
                $num = 1;

                // summary
                $activeSheet->SetCellValue('A' . $rowCount, 'Total : ');
                $activeSheet->mergeCells('A' . $rowCount . ':F' . $rowCount);
                $activeSheet->getStyle("A" . $rowCount . ":K" . $rowCount)->getFont()->setBold(true);
                $activeSheet->getStyle("A" . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $activeSheet->SetCellValue('G' . $rowCount, $total_hutang_rp);
                $activeSheet->SetCellValue('H' . $rowCount, $total_sisa_hutang_rp);
                $activeSheet->SetCellValue('I' . $rowCount, $total_hutang_valas);
                $activeSheet->SetCellValue('J' . $rowCount, $total_sisa_hutang_valas);

                $activeSheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $activeSheet->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $activeSheet->getStyle('I' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $activeSheet->getStyle('J' . $rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                // $object->getActiveSheet()->getStyle("A" . $rowCount . ":I" . $rowCount)->applyFromArray($styleArrayColor);
                $rowCount++;

                $total_hutang_rp  = 0;
                $total_sisa_hutang_rp   = 0;
                $total_hutang_valas       = 0;
                $total_sisa_hutang_valas  = 0;
                $rowCount = $rowCount;;
            }

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $object->save('php://output');

            $xlsData = ob_get_contents();
            ob_end_clean();

            $name_file = 'Outstanding Invoice Per Tanggal ' . $periode . '.xlsx';

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


    function create_thead($rowCount, $activeSheet, $getSheet)
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

        $table_head_columns  = array('No', 'Supplier', 'Invoice', 'PO', 'Receiving', 'Tanggal', 'Total Hutang (Rp)', 'Sisa Hutang (Rp)', 'Total Hutang (Valas)' , 'Sisa Hutang (Valas)', 'Umur (Hari)');
        $column = 0;
        foreach ($table_head_columns as $field) {
            $activeSheet->setCellValueByColumnAndRow($column, $rowCount, $field);
            $column++;
        }

        // set width and border
        $index_header = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I','J','K');
        $loop = 0;
        foreach ($index_header as $val) {

            // $activeSheet->getStyle($val . '3')->applyFromArray($styleArrayColor);

            if ($loop == 0) {
                $getSheet->getColumnDimension($val)->setAutoSize(true); // index A
            } else if ($loop ==  1) {
                $getSheet->getColumnDimension($val)->setWidth(10); // index B
            } else if ($loop == 2 OR $loop == 3 or $loop == 4) {
                $getSheet->getColumnDimension($val)->setWidth(15); // index C/D/E
            } else if ( $loop == 5) {
                $getSheet->getColumnDimension($val)->setWidth(14); // index F
            } else if ($loop > 5) {
                $getSheet->getColumnDimension($val)->setWidth(18); // index -> G-J
                $getSheet->getStyle($val . '' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            } else if ( $loop == 10) {
                $getSheet->getColumnDimension($val)->setWidth(14); // index K
                $getSheet->getStyle($val . '' . $rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            }

            $loop++;
        }
        $getSheet->getRowDimension($rowCount)->setRowHeight(24); // height acc
        $getSheet->getStyle("A" . $rowCount . ":K" . $rowCount)->applyFromArray($styleArrayColor);


        return;
    }

    public function export_pdf() {
     
        $this->load->library('dompdflib');
        $data_arr  = json_decode($this->input->get('params'),true);  
        $tgl_now   = date("Y-m-d");
        $partner   = '';
        foreach($data_arr as $rows){
            $partner = $rows['partner'];
        }

        $data = $this->proses_data($partner);

        $data['list'] = $data;
        $data['periode'] = tgl_indo(date('d-m-Y', strtotime($tgl_now)));
        $cnt = $this->load->view('report/v_outstanding_invoice_pdf', $data, true);
        $this->dompdflib->generate($cnt);
    }
}
