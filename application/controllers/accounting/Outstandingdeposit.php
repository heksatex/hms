<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');

class Outstandingdeposit extends MY_Controller
{

    //put your code here
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model("_module"); //load modul global
        $this->load->model("m_outstandingdeposit");
    }


    public function index()
    {
        $data['id_dept'] = 'ACCODEP';
        $this->load->view('accounting/v_outstanding_deposit', $data);
    }

    public function list_data_deposit()
    {
        try {

            if (isset($_POST['start']) && isset($_POST['draw'])) {
                $list = $this->m_outstandingdeposit->get_datatables();
                $data = array();
                $no = $_POST['start'];
                foreach ($list as $field) {
                    $no++;
                    $row = array();
                    $row[] = $no;
                    $row[] = $field->no_pelunasan;
                    $row[] = $field->partner_nama;
                    $row[] = date('Y-m-d', strtotime($field->tanggal_transaksi));
                    $row[] = $field->currency;
                    $row[] = $field->kurs;
                    $row[] = number_format($field->total_rp, 2);
                    $row[] = number_format($field->total_valas, 2);
                    $row[] = $field->id;
                    $data[] = $row;
                }

                $output = array(
                    "draw" => $_POST['draw'],
                    "recordsTotal" => $this->m_outstandingdeposit->count_all(),
                    "recordsFiltered" => $this->m_outstandingdeposit->count_filtered(),
                    "data" => $data,
                );
                //output dalam format JSON
                echo json_encode($output);
            } else {
                die();
            }

            exit();
        } catch (Exception $ex) {
            echo json_encode(array(
                "draw" => $_POST['draw'],
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ));
        }
    }


    public function nonaktif()
    {
        $id  = $this->input->post('id');
        $no  = $this->input->post('no_pelunasan');

        if (!$id || !$no) {
            echo json_encode([
                'status' => false,
                'message' => 'Parameter tidak lengkap'
            ]);
            return;
        }

        $this->db->where('id', $id)
            ->where('no_pelunasan', $no)
            ->update('acc_pelunasan_piutang_summary_koreksi', [
                'lunas' => 3,
            ]);

        echo json_encode(['status' => true]);
    }

    public function export_excel_deposit()
    {
        try {
            // 1. Load Library
            $this->load->library('excel');

            // 2. Ambil parameter search dari AJAX
            $search = $this->input->post('search');

            // 3. Ambil data dari model
            // Pastikan nama model sesuai (m_outstandingdeposit)
            $records = $this->m_outstandingdeposit->get_all_deposit($search);

            if (empty($records)) {
                throw new Exception("Data tidak ditemukan atau filter kosong.");
            }

            ob_start();
            $object = new PHPExcel();
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Outstanding Deposit');

            // --- STYLING ---
            $styleHeader = [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER],
                'borders' => ['allborders' => ['style' => PHPExcel_Style_Border::BORDER_THIN]],
                'fill' => ['type' => PHPExcel_Style_Fill::FILL_SOLID, 'color' => ['rgb' => 'EAEAEA']]
            ];

            // --- HEADER LAPORAN ---
            $sheet->setCellValue('A1', 'DAFTAR OUTSTANDING DEPOSIT');
            $sheet->mergeCells('A1:H1');
            $sheet->getStyle("A1")->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle("A1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // --- TABLE HEAD ---
            $headers = ['No', 'No Pelunasan', 'Customer', 'Tanggal', 'Curr', 'Kurs', 'Total (Rp)', 'Total (Valas)'];
            $col = 0;
            foreach ($headers as $h) {
                $sheet->setCellValueByColumnAndRow($col, 3, $h);
                $col++;
            }
            $sheet->getStyle('A3:H3')->applyFromArray($styleHeader);

            // --- ISI DATA ---
            $rowIdx = 4;
            $no = 1;
            foreach ($records as $row) {
                $sheet->setCellValue('A' . $rowIdx, $no++);

                // Kolom B: No Pelunasan (String agar tidak jadi scientific notation)
                $sheet->setCellValueExplicit('B' . $rowIdx, $row->no_pelunasan, PHPExcel_Cell_DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowIdx, $row->partner_nama);
                $sheet->setCellValue('D' . $rowIdx, $row->tanggal_transaksi);
                $sheet->setCellValue('E' . $rowIdx, $row->currency);

                // Kolom F, G, H: Angka
                $sheet->setCellValue('F' . $rowIdx, $row->kurs);
                $sheet->setCellValue('G' . $rowIdx, $row->total_rp);
                $sheet->setCellValue('H' . $rowIdx, $row->total_valas);

                // Format angka ribuan (comma separated)
                $sheet->getStyle('F' . $rowIdx . ':H' . $rowIdx)->getNumberFormat()->setFormatCode('#,##0.00');

                $rowIdx++;
            }

            // Auto width kolom
            foreach (range('A', 'H') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            // --- GENERATE ---
            $writer = PHPExcel_IOFactory::createWriter($object, 'Excel2007');
            $writer->save('php://output');
            $xlsData = ob_get_contents();
            ob_end_clean();

            die(json_encode([
                'status' => 'success',
                'file' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsData),
                'filename' => 'Outstanding Deposit.xlsx'
            ]));
        } catch (Exception $ex) {
            // Jika error, kirim pesan ke client
            if (ob_get_length()) ob_end_clean();
            die(json_encode(['status' => 'failed', 'message' => $ex->getMessage()]));
        }
    }
}
