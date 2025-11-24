<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');


class Bukubesarpembantupiutangdetail extends MY_Controller
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
        $data['id_dept'] = 'RBBPPD';
        $this->load->view('report/v_bukubesar_pembantu_piutang_detail', $data);
    }

    function loadData()
    {
        try {
            //code...
            $partner     = $this->input->post('partner'); 
            $tgldari     = date('Y-m-d H:i:s', strtotime($this->input->post('tgldari'))); 
            $tglsampai   = date("Y-m-d 23:59:59", strtotime($this->input->post('tglsampai'))); 
            $checkhidden = $this->input->post('checkhidden'); 
            $currency    = $this->input->post('currency'); // all, valas, rp
         
            $data = $this->proses_data($checkhidden,$tgldari,$tglsampai,$partner, $currency);
            $callback = array('status' => 'success', 'message' =>'berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'record'=> $data);
            $this->output->set_status_header(200)
                            ->set_content_type('application/json', 'utf-8')
                            ->set_output(json_encode($callback));

        } catch (Exception $ex) {
                $this->output->set_status_header(($ex->getCode()) ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }

    }

      function proses_data($checkhidden,$tgl_dari,$tgl_sampai,$partner,$currency)
    {
        $where_partner = [];
        if(!empty($partner)){
            $where_partner = ['p.id'=>$partner];
        }
        
        $data = $this->m_bukubesarpembantupiutang->get_list_bukubesar_detail($tgl_dari, $tgl_sampai, $checkhidden, $where_partner,$currency);

        $tmp_data_akun     = array();
        $tmp_data_akun_isi = array();
        $debit    = 0;
        $credit   = 0;
        $saldo_awal = 0;
        $saldo_akhir = 0;

        foreach ($data as $datas) {

            $saldo_awal        = floatval($datas->saldo_awal_final);
     
            
            $where2 = ['id_partner' => $datas->id];
            $data2 = $this->m_bukubesarpembantupiutang->get_list_bukubesar_detail_by_kode($tgl_dari,$tgl_sampai,$where2,$currency);
            foreach($data2 as $datas2){

                $debit  = floatval($datas2->debit);
                $credit = floatval($datas2->credit);
              
                $saldo_akhir =  $saldo_awal + ($debit) - ($credit);

                $tmp_data_akun_isi[] = array(
                    'tanggal'   => date("Y-m-d",strtotime($datas2->tgl)),
                    'id_bukti'  => $datas2->id_bukti,
                    'no_bukti'  => $datas2->no_bukti,
                    'uraian' => $datas2->uraian,
                    'debit' => $debit,
                    'credit' => $credit,
                    'saldo_akhir' => ($saldo_akhir),
                    'id_bukti_ecr' =>encrypt_url($datas2->id_bukti_ecr),
                    'no_bukti_ecr' =>encrypt_url($datas2->no_bukti),
                    'link'  => $datas2->link
                );
                $saldo_awal = $saldo_akhir;
            }
            
            $tmp_data_akun[]= array(
                'id'=>$datas->id,
                'nama_partner'=>$datas->nama,
                'saldo_awal' => floatval($datas->saldo_awal_final),
                'saldo_awal_debit' => 0,
                'saldo_awal_credit' => 0,
                'tmp_data_isi'=>$tmp_data_akun_isi
            );

            $debit    = 0;
            $credit   = 0;
            $where2   = [];
            $tmp_data_akun_isi = [];
            $saldo_akhir = 0;
        }

        return $tmp_data_akun;
    }

    function export_excel()
    {
        try {
            //code...
            $this->load->library('excel');


	        $arr_filter = $this->input->post('arr_filter');

            $tgl_dari   = $arr_filter[0]['tgldari'] ?? '';
            $tgl_sampai = $arr_filter[0]['tglsampai'] ?? '';
            $checkhidden= $arr_filter[0]['checkhidden'] ?? '';
            $partner    = $arr_filter[0]['partner'] ?? '';
            $currency   = $arr_filter[0]['currency'] ?? '';
            $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgl_dari));
            $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tgl_sampai));


            ob_start();
            $object = new PHPExcel();
            
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('DETAIL');
            $activeSheet = $object->getActiveSheet();
            // $getSheet  = $object->getSheet(0);
            $activeSheet->setShowGridlines(false);

            $rowCount = 1;
            $loop     = 1;

            $periode = tgl_indo(date('d-m-Y',strtotime($tgl_dari))) .' - '.tgl_indo(date('d-m-Y',strtotime($tgl_sampai)));
            // set Judul
            $sheet->SetCellValue('A'.$rowCount, 'PT. HEKSATEX INDAH');
            $sheet->mergeCells('A'.$rowCount.':G'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // set Judul
            $rowCount++;
            $sheet->SetCellValue('A'.$rowCount, 'BUKU BESAR PEMBANTU PIUTANG DETAIL');
            $sheet->mergeCells('A'.$rowCount.':G'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // set periode
            $rowCount = 3;
            $sheet->SetCellValue('A'.$rowCount, $periode );
            $sheet->mergeCells('A'.$rowCount.':G'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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

            $styleArrayColor = array(
                'font'  => array(
                    'bold'  => true,
                    // 'color' => array('rgb' => 'FFFFFF'),
                ),
                'fill' => array(
                        'type' => PHPExcel_Style_Fill::FILL_SOLID,
                        'color' => array('rgb' => 'D3D3D3' )
                ),
                'alignment' => array(
                    // 'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                )
            );	
            

            $data = $this->proses_data($checkhidden,$tgl_dari,$tgl_sampai,$partner,$currency);
            $rowCount = 6;
            $num      = 1;
            $total_credit  = 0;
            $total_debit   = 0;
            $nama_partner  = '';
            foreach($data as $datas){
                $nama_partner = $datas['nama_partner'];
                // nama acc
                $activeSheet->SetCellValue('A'.$rowCount, ' Customer :  ' . $datas['nama_partner']);
                $activeSheet->mergeCells('A'.$rowCount.':G'.$rowCount);
                $activeSheet->getStyle("A".$rowCount.":G".$rowCount)->getFont()->setBold(true);
                $activeSheet->getRowDimension($rowCount)->setRowHeight(24); // height acc
				$object->getActiveSheet()->getStyle("A".$rowCount.":G".$rowCount)->applyFromArray($styleArrayColor);


                $rowCount++;
                // thead
                $this->create_thead($rowCount, $activeSheet,$activeSheet);
                $activeSheet->getStyle("A".$rowCount.":G".$rowCount)->getFont()->setBold(true);

                // saldo Awal
                $rowCount++;
                $activeSheet->SetCellValue('A'.$rowCount, ($num++));
                $activeSheet->mergeCells('B'.$rowCount.':C'.$rowCount);
				$activeSheet->SetCellValue('D'.$rowCount, 'Saldo Awal');
				$activeSheet->SetCellValue('F'.$rowCount, 0.00);
				$activeSheet->SetCellValue('F'.$rowCount, 0.00);
				$activeSheet->SetCellValue('G'.$rowCount, $datas['saldo_awal']);
				$activeSheet->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');

                $rowCount++;
                $saldo_akhir = $datas['saldo_awal'];

                foreach($datas['tmp_data_isi'] as $datas2){
                    	$activeSheet->SetCellValue('A'.$rowCount, ($num));
                    	$activeSheet->SetCellValue('B'.$rowCount, $datas2['tanggal']);
                    	$activeSheet->SetCellValue('C'.$rowCount, $datas2['no_bukti']);
                    	$activeSheet->SetCellValue('D'.$rowCount, $datas2['uraian']);
                    	$activeSheet->SetCellValue('E'.$rowCount, $datas2['debit']);
                    	$activeSheet->SetCellValue('F'.$rowCount, $datas2['credit']);
                    	$activeSheet->SetCellValue('G'.$rowCount, $datas2['saldo_akhir']);

				        $activeSheet->getStyle('E'.$rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
				        $activeSheet->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
				        $activeSheet->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');

                    $num++;
                    $rowCount++;
                    $total_debit = $total_debit + $datas2['debit'];
                    $total_credit = $total_credit + $datas2['credit'];
                    $saldo_akhir = $datas2['saldo_akhir'];
                }
                $num=1;

                // summary
                $activeSheet->SetCellValue('D'.$rowCount, 'Total : ');
                // $activeSheet->mergeCells('A'.$rowCount.':D'.$rowCount);
                $activeSheet->SetCellValue('E'.$rowCount, $total_debit);
                $activeSheet->SetCellValue('F'.$rowCount, $total_credit);
                $activeSheet->SetCellValue('G'.$rowCount, $saldo_akhir);
				$activeSheet->getStyle('E'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
				$activeSheet->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
				$activeSheet->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle("A".$rowCount.":G".$rowCount)->applyFromArray($styleArrayColor);
                $rowCount++;


                $total_credit  = 0;
                $total_debit   = 0;
                $saldo_akhir   = 0;
                $rowCount=$rowCount+2;;
                
            }

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
            $object->save('php://output');
    
            $xlsData = ob_get_contents();
            ob_end_clean();
            $nama_partner = ($partner != '' || $partner != null)? $nama_partner : '';
            $name_file ='Buku Besar Pembantu Piutang Detail  '.$nama_partner.' Periode '.$periode.'.xlsx';
    
            $response =  array(
                'op'        => 'ok',
                'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
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
        $table_head_columns  = array('No', 'Tanggal', 'No Bukti', 'Keterangan', 'Debit', 'Credit', 'Saldo');
        $column = 0;
        foreach ($table_head_columns as $field) {
            $activeSheet->setCellValueByColumnAndRow($column, $rowCount, $field);
            $column++;
        }

        // set width and border
        $index_header = array('A', 'B', 'C', 'D', 'E', 'F', 'G');
        $loop = 0;
        foreach ($index_header as $val) {


            if ($loop == 0) {
                $getSheet->getColumnDimension($val)->setAutoSize(true); // index A
            } else if ($loop ==  1) {
                $getSheet->getColumnDimension($val)->setWidth(10); // index B
            } else if ($loop == 2) {
                $getSheet->getColumnDimension($val)->setWidth(20); // index C
            } else if ($loop == 3) {
                $getSheet->getColumnDimension($val)->setWidth(35); // index D
            } else if ($loop > 4) {
                $getSheet->getColumnDimension($val)->setWidth(20); // index E -> G
                $getSheet->getStyle($val.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT );
            }

            $loop++;
        }

        return;
    }


}