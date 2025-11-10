<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');


class Bukubesardetail extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();
        $this->load->model('_module');
        $this->load->model('m_bukubesar');
        $this->load->library('periodesaldo');
    }

    public function index()
    {
        $data['id_dept'] = 'ACCBBD';
        $this->load->view('accounting/v_bukubesardetail', $data);
    }


    public function get_list_coa()
	{
		$nama  = addslashes($this->input->post('nama'));
   		$callback = $this->m_bukubesar->get_list_coa_select2($nama);
        echo json_encode($callback);
	}


    function loadData()
    {
        try {
            //code...
            $coa         = $this->input->post('coa'); 
            $tgldari     = date('Y-m-d H:i:s', strtotime($this->input->post('tgldari'))); 
            $tglsampai   = date("Y-m-d 23:59:59", strtotime($this->input->post('tglsampai'))); 
            $checkhidden = $this->input->post('checkhidden'); 
            $where  = ["je.status"=>"posted",'je.tanggal_dibuat >= '=>$tgldari,'je.tanggal_dibuat <= '=>$tglsampai];
            // if(!empty($coa)){
            //     $where = array_merge($where,array("coa.kode_coa"=>$coa));
            // }
            $data = $this->proses_data($checkhidden,$where,$tgldari,$tglsampai,$coa);
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

    function proses_data($checkhidden,$where = [],$tgl_dari,$tgl_sampai,$coa)
    {
        $where_coa = [];
        if(!empty($coa)){
            $where_coa = ['coa.kode_coa'=>$coa];
        }
        
        $data = $this->m_bukubesar->get_list_bukubesar_detail_coa($tgl_dari,$tgl_sampai,$where_coa,$checkhidden);
        $tmp_data_akun     = array();
        $tmp_data_akun_isi = array();
        $debit    = 0;
        $credit   = 0;
        $saldo_awal = 0;
        $saldo_akhir = 0;
        foreach ($data as $datas) {

            $saldo_awal        = floatval($datas->saldo_awal_final);
            $saldo_awal_debit  = floatval($datas->total_debit_sbl);
            $saldo_awal_credit = floatval($datas->total_credit_sbl);
            
            // get list_ledger_detail_by_coa
            $where2 = ['coa.kode_coa' => $datas->kode_coa];
            $where  = array_merge($where , $where2);
            $data2 = $this->m_bukubesar->get_list_bukubesar_detail_by_coa($where);
            foreach($data2 as $datas2){

                $debit  = floatval($datas2->total_debit);
                $credit = floatval($datas2->total_credit);

                if($datas->saldo_normal =='D'){
                    // $saldo_awal = floatval($datas->saldo_awal) + $get_debit - $get_credit;
                    $saldo_akhir =  $saldo_awal + floatval($debit) - floatval($credit);
                } else {
                    // $saldo_awal = floatval($datas->saldo_awal) + $get_credit - $get_debit ;
                    $saldo_akhir =  $saldo_awal + floatval($credit) - floatval($debit);
                }

                $tmp_data_akun_isi[] = array(
                    'tanggal' => date("Y-m-d",strtotime($datas2->tanggal)),
                    'kode_entries_encr' => encrypt_url($datas2->kode_entries),
                    'kode_entries' => $datas2->kode_entries,
                    'origin' => $datas2->origin,
                    'keterangan' => $datas2->keterangan,
                    'debit' => $debit,
                    'credit' => $credit,
                    'saldo_akhir' => ($saldo_akhir)
                );
                $saldo_awal = $saldo_akhir;
            }
            $tmp_data_akun[]= array(
                'kode_acc'=>$datas->kode_coa,
                'nama_acc'=>$datas->nama_coa,
                'saldo_awal' => floatval($datas->saldo_awal_final),
                'saldo_normal' => $datas->saldo_normal,
                'saldo_awal_debit' => floatval($saldo_awal_debit),
                'saldo_awal_credit' => floatval($saldo_awal_credit),
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
            $coa        = $arr_filter[0]['coa'] ?? '';
            $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgl_dari));
            $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tgl_sampai));

            $where  = ["je.status"=>"posted",'je.tanggal_dibuat >= '=>$tgl_dari,'je.tanggal_dibuat <= '=>$tgl_sampai];
            // if(!empty($coa)){
            //     $where = array_merge($where,array("coa.kode_coa"=>$coa));
            // }

            ob_start();
            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);


            $object->createSheet();
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
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // set Judul
            $rowCount++;
            $sheet->SetCellValue('A'.$rowCount, 'BUKU BESAR DETAIL');
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            // set periode
            $rowCount = 3;
            $sheet->SetCellValue('A'.$rowCount, $periode );
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            //bold huruf
            $activeSheet->getStyle("A1:H5")->getFont()->setBold(true);

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
            

            $data = $this->proses_data($checkhidden,$where,$tgl_dari,$tgl_sampai,$coa);
            $rowCount = 6;
            $num      = 1;
            $total_credit  = 0;
            $total_debit   = 0;
            foreach($data as $datas){

                // nama acc
                $activeSheet->SetCellValue('A'.$rowCount, $datas['kode_acc'] . ' - ' . $datas['nama_acc']);
                $activeSheet->mergeCells('A'.$rowCount.':H'.$rowCount);
                $activeSheet->getStyle("A".$rowCount.":H".$rowCount)->getFont()->setBold(true);
                $activeSheet->getRowDimension($rowCount)->setRowHeight(24); // height acc
				$object->getActiveSheet()->getStyle("A".$rowCount.":H".$rowCount)->applyFromArray($styleArrayColor);


                $rowCount++;
                // thead
                $this->create_thead($rowCount, $activeSheet,$activeSheet);
                $activeSheet->getStyle("A".$rowCount.":H".$rowCount)->getFont()->setBold(true);

                // saldo Awal
                $rowCount++;
                $activeSheet->SetCellValue('A'.$rowCount, ($num++));
				$activeSheet->SetCellValue('B'.$rowCount, 'Saldo Awal');
                $activeSheet->mergeCells('B'.$rowCount.':E'.$rowCount);
				$activeSheet->SetCellValue('F'.$rowCount, 0.00);
				$activeSheet->SetCellValue('G'.$rowCount, 0.00);
				$activeSheet->SetCellValue('H'.$rowCount, $datas['saldo_awal']);
				$activeSheet->getStyle('H'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');

                $rowCount++;
                $saldo_akhir = $datas['saldo_awal'];

                foreach($datas['tmp_data_isi'] as $datas2){
                    	$activeSheet->SetCellValue('A'.$rowCount, ($num));
                    	$activeSheet->SetCellValue('B'.$rowCount, $datas2['tanggal']);
                    	$activeSheet->SetCellValue('C'.$rowCount, $datas2['kode_entries']);
                    	$activeSheet->SetCellValue('D'.$rowCount, $datas2['origin']);
                    	$activeSheet->SetCellValue('E'.$rowCount, $datas2['keterangan']);
                    	$activeSheet->SetCellValue('F'.$rowCount, $datas2['debit']);
                    	$activeSheet->SetCellValue('G'.$rowCount, $datas2['credit']);
                    	$activeSheet->SetCellValue('H'.$rowCount, $datas2['saldo_akhir']);

				        $activeSheet->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
				        $activeSheet->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
				        $activeSheet->getStyle('H'.$rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');

                    $num++;
                    $rowCount++;
                    $total_debit = $total_debit + $datas2['debit'];
                    $total_credit = $total_credit + $datas2['credit'];
                    $saldo_akhir = $datas2['saldo_akhir'];
                }
                $num=1;

                // summary
				$activeSheet->SetCellValue('A'.$rowCount, 'Saldo Awal : ');
                $activeSheet->mergeCells('A'.$rowCount.':B'.$rowCount);
				$activeSheet->SetCellValue('C'.$rowCount, $datas['saldo_awal']); // Saldo Awal
                $activeSheet->SetCellValue('E'.$rowCount, 'Total : ');
                $activeSheet->SetCellValue('F'.$rowCount, $total_debit);
                $activeSheet->SetCellValue('G'.$rowCount, $total_credit);
				$activeSheet->getStyle('C'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
				$activeSheet->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
				$activeSheet->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $object->getActiveSheet()->getStyle("A".$rowCount.":H".$rowCount)->applyFromArray($styleArrayColor);
                $rowCount++;

                $activeSheet->SetCellValue('A'.$rowCount, 'Saldo Akhir :');
                $activeSheet->mergeCells('A'.$rowCount.':B'.$rowCount);
                $activeSheet->SetCellValue('C'.$rowCount, $saldo_akhir);
				$activeSheet->getStyle('C'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
                $activeSheet->SetCellValue('E'.$rowCount, 'Mutasi : ');
                $mutasi_saldo  = $saldo_akhir - $datas['saldo_awal'];
                $activeSheet->SetCellValue('F'.$rowCount, $mutasi_saldo);
				$activeSheet->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');

                $object->getActiveSheet()->getStyle("A".$rowCount.":H".$rowCount)->applyFromArray($styleArrayColor);
                

                $total_credit  = 0;
                $total_debit   = 0;
                $saldo_akhir   = 0;
                $rowCount=$rowCount+2;;
                
            }

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
            $object->save('php://output');
    
            $xlsData = ob_get_contents();
            ob_end_clean();

            $name_file ='Buku Besar Detail Periode '.$periode.'.xlsx';
    
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
        $table_head_columns  = array('No', 'Tanggal', 'Kode Entries', 'Origin', 'Keterangan', 'Debit', 'Credit', 'Saldo');
        $column = 0;
        foreach ($table_head_columns as $field) {
            $activeSheet->setCellValueByColumnAndRow($column, $rowCount, $field);
            $column++;
        }

        // set width and border
        $index_header = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H');
        $loop = 0;
        foreach ($index_header as $val) {

            // $activeSheet->getStyle($val . '3')->applyFromArray($styleArrayColor);

            if ($loop == 0) {
                $getSheet->getColumnDimension($val)->setAutoSize(true); // index A
            } else if ($loop ==  1) {
                $getSheet->getColumnDimension($val)->setWidth(10); // index B
            } else if ($loop == 2) {
                $getSheet->getColumnDimension($val)->setWidth(20); // index C
            } else if ($loop == 3 or $loop == 4) {
                $getSheet->getColumnDimension($val)->setWidth(35); // index D/E
            } else if ($loop > 4) {
                $getSheet->getColumnDimension($val)->setWidth(20); // index F -> G
                $getSheet->getStyle($val.''.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT );
                
            }

            $loop++;
        }

        return;
    }

    public function export_pdf() {
     
        $this->load->library('dompdflib');
        $data_arr  = json_decode($this->input->get('params'),true);  
    
        $tgl_dari   = '';
        $tgl_sampai = '';
        $checkhidden = '';
        $coa       = '';
        foreach($data_arr as $rows){
            $tgl_dari = $rows['tgldari'];
            $tgl_sampai = $rows['tglsampai'];
            $checkhidden = $rows['checkhidden'];
            $coa        = $rows['coa'] ?? '';
        }

        $tgldari     =  date('Y-m-d H:i:s', strtotime($tgl_dari)); 
        $tglsampai   = date("Y-m-d 23:59:59", strtotime($tgl_sampai)); 

        $where  = ["je.status" => "posted", 'je.tanggal_dibuat >= ' => $tgldari, 'je.tanggal_dibuat <= ' => $tglsampai];
        // if (!empty($coa)) {
        //     $where = array_merge($where, array("coa.kode_coa" => $coa));
        // }
        $data = $this->proses_data($checkhidden,$where,$tgldari,$tglsampai,$coa);

        $data['list'] = $data;
        $data['tgl_dari']   = tgl_indo(date('d-m-Y',strtotime($tgl_dari)));
        $data['tgl_sampai'] = tgl_indo(date('d-m-Y',strtotime($tgl_sampai)));
        $cnt = $this->load->view('accounting/v_bukubesardetail_pdf', $data, true);
        $this->dompdflib->generate($cnt);
    }

}