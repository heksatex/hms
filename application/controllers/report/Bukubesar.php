<?php

defined('BASEPATH') or exit('No Direct Script Acces Allowed');


class Bukubesar extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin();
        // $this->load->model("m_global");
        $this->load->model('_module');
        $this->load->model('m_bukubesar');
        $this->load->library('periodesaldo');
    }

    public function index()
    {
        $data['id_dept'] = 'ACCBB';
        $this->load->view('accounting/v_bukubesar', $data);
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
                $callback = array('status' => 'failed', 'field' =>'periode', 'message' => array_values($this->form_validation->error_array())[0], 'icon' => 'fa fa-warning', 'type' => 'danger');
            } else {

                $tgldari    = $this->input->post('tgldari');
                $tglsampai  = $this->input->post('tglsampai');
                $checkhidden= $this->input->post('checkhidden');

                $data = $this->proses_data($tgldari,$tglsampai,$checkhidden);
                $callback = array('status' => 'success', 'message' =>'berhasil', 'icon' => 'fa fa-check', 'type' => 'success', 'record'=> $data);

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


    // public function view_detail_bukubesar_modal()
    // {
    // 	$kode_coa   = $this->input->post('kode_coa');
    // 	$arr_filter = $this->input->post('arr_filter');
    //     $data['tgl_dari'] = $arr_filter[0]['tgldari'] ?? '';
    //     $data['tgl_sampai'] = $arr_filter[0]['tglsampai'] ?? '';
    //     return $this->load->view('modal/v_bukubesar_detail_modal', $data);
    // }


    public function detail()
    {
        $data_arr  = json_decode($this->input->get('params'),true);  
        $coa       = $this->input->get('coa');  
        
       
        $tgl_dari   = '';
        $tgl_sampai = '';
        $checkhidden = '';
        foreach($data_arr as $rows){
            $tgl_dari =  date('Y-m-d H:i:s', strtotime($rows['tgldari']));
            $tgl_sampai = date("Y-m-d H:i:s", strtotime($rows['tglsampai']));
            $checkhidden = $rows['checkhidden'];
        }
        $data['coa']  = $this->m_bukubesar->get_coa_by_kode($coa);
        $data['tgl_dari']   = tgl_eng(date('d-m-Y',strtotime($tgl_dari)));
        $data['tgl_sampai'] = tgl_eng(date('d-m-Y',strtotime($tgl_sampai)));
        $data['checkhidden'] = $checkhidden;
        $data['id_dept'] = 'ACCBB';
        return $this->load->view('accounting/v_bukubesar_detail', $data);
    }

    function loadDataDetail()
    {
        try {
            //code...
            $coa         = $this->input->post('coa'); 
            $tgldari     =  date('Y-m-d H:i:s', strtotime($this->input->post('tgldari'))); 
            $tglsampai   = date("Y-m-d 23:59:59", strtotime($this->input->post('tglsampai'))); 
            $checkhidden = $this->input->post('checkhidden'); 
            $view        = $this->input->post('view'); // saldo normal N, lawa debit D, lawan credit = C

            // $where  = ['coa.kode_coa'=>$coa];
            $where  = ["je.status"=>"posted",'je.tanggal_dibuat >= '=>$tgldari,'je.tanggal_dibuat <= '=>$tglsampai];
            if($view == 'N'){
                $data = $this->proses_data2($tgldari,$tglsampai,$coa,$where);
            } else {
                $data = $this->proses_data3($where,$coa,$view);
            }
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

    // function get_start_periode()
    // {
    //     $where = ['setting_name'=>"start_periode_acc", 'status'=>1];
    //     $periode = $this->m_bukubesar->get_setting_start_periode_acc($where);
    //     return $periode;
    // }


    // function get_debit($tgl_sampai,$kode_coa)
    // {
    //     $start = $this->periodesaldo->get_start_periode();
    //     $tgl_dari = date("Y-m-d 00:00:00", strtotime($start)); // example 202501-01 00:00:00
    //     $tgl_sampai = date("Y-m-d 23:59:59", strtotime("-1 day",  strtotime($tgl_sampai))); // tgl smpai - 1

    //     $where = ['je.tanggal_dibuat >='=> $tgl_dari, 'je.tanggal_dibuat <=' => $tgl_sampai, 'coa.kode_coa' => $kode_coa, 'jei.posisi'=> 'D'];
    //     $result = $this->m_bukubesar->get_total_nominal_posisi_by_coa($where);
    //     return $result->nominal ?? 0;
        
    // }

    //  function get_credit($tgl_sampai,$kode_coa)
    // {
    //     $start = $this->periodesaldo->get_start_periode();
    //     $tgl_dari = date("Y-m-d 00:00:00", strtotime($start)); // example 202501-01 00:00:00
    //     $tgl_sampai = date("Y-m-d 23:59:59", strtotime("-1 day",  strtotime($tgl_sampai))); // tgl smpai - 1

    //     $where = ['je.tanggal_dibuat >='=> $tgl_dari, 'je.tanggal_dibuat <=' => $tgl_sampai, 'coa.kode_coa' => $kode_coa, 'jei.posisi'=> 'C'];
    //     $result = $this->m_bukubesar->get_total_nominal_posisi_by_coa($where);
    //     return $result->nominal ?? 0;

    // }

    function proses_data($tgldari,$tglsampai,$checkhidden, $where = [] ){

        $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgldari));
        $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tglsampai));

        $data = $this->m_bukubesar->get_list_bukubesar($tgl_dari,$tgl_sampai,$checkhidden,$where);
        $tmp_data = array();
        $debit    = 0;
        $credit   = 0;
        $saldo_awal = 0;
        $saldo_akhir = 0;
        foreach($data as $datas){
            
            $total_debit = floatval($datas->total_debit);
            $total_credit = floatval($datas->total_credit);

            $saldo_awal    = floatval($datas->saldo_awal_final);

            if($datas->saldo_normal == 'D'){
                $saldo_akhir = $saldo_awal + $total_debit - $total_credit; 
            } else {
                $saldo_akhir = $saldo_awal + $total_credit -$total_debit; 
            }
            $tmp_data[]= array(
                            'kode_acc'=>$datas->kode_coa,
                            'nama_acc'=>$datas->nama_coa,
                            'saldo_normal'=>$datas->saldo_normal,
                            'saldo_awal'=>$saldo_awal,
                            'debit'=>$total_debit,
                            'credit'=>$total_credit,
                            'saldo_akhir' => floatval($saldo_akhir)

            );
            $debit    = 0;
            $credit   = 0;
            $saldo_awal = 0;
        // }   
        }

        return $tmp_data;
    }


   
    function proses_data2($tgl_dari,$tgl_sampai, $coa, $where = [])
    {
        $where_coa = ['coa.kode_coa'=>$coa];
        $data = $this->m_bukubesar->get_list_bukubesar_detail_coa($tgl_dari,$tgl_sampai,$where_coa);
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


    function proses_data3($where = [], $coa, $view = 'D')
    {
        $where_coa = ["coa.kode_coa"=>$coa, "jei.posisi"=>$view];
        $where     = array_merge($where, $where_coa);
        $data = $this->m_bukubesar->get_list_bukubesar_detail_lawan_by_coa($where,$view);

        $tmp_data_akun     = array();
        $tmp_data_akun_isi = array();
        
        if($view == 'D'){
            $lawan = 'C';
        }else{
            $lawan = 'D';
        }
        $loop = 0;
        foreach ($data as $datas) {

            $where2 = ['coa.kode_coa <>' => $datas->kode_coa, 'jei.kode '=>$datas->kode_entries];
            $data2 = $this->m_bukubesar->get_list_bukubesar_detail_lawan_by_coa($where2,$lawan);

            // get list_ledger_detail_by_coa_lawan
            $loop2 = 1;
            foreach($data2 as $datas2){
              
                if($loop2 == 1){
                    $tmp_data_akun_isi[] = array(
                        'tanggal' => date("Y-m-d", strtotime($datas->tanggal)),
                        'kode_entries_encr' => encrypt_url($datas->kode_entries),
                        'kode_entries' => $datas->kode_entries,
                        'origin' => $datas->origin,
                        'keterangan' => $datas->keterangan,
                        'debit_or_credit' => floatval($datas->total), // debit or credit
                        'lawan' => $datas2->kode_coa." - ".$datas2->nama_coa,
                        'nominal' => floatval($datas2->total)
                    );

                 
                } else {

                    $tmp_data_akun_isi[] = array(
                        'tanggal' => '',
                        'kode_entries' => '',
                        'origin' => '',
                        'keterangan' =>'',
                        'debit_or_credit' => '', // debit or credit
                        'lawan' => $datas2->kode_coa." - ".$datas2->nama_coa,
                        'nominal' => floatval($datas2->total)
                    );
                }

                $loop2++;

            }

            $where2   = [];
          
            $loop++;
        }

        return $tmp_data_akun_isi;
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
            $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgl_dari));
            $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tgl_sampai));

            ob_start();
            $object = new PHPExcel();
            // $object->setActiveSheetIndex(0);


            // $object->createSheet();
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('Global');

            $rowCount = 1;
            $loop     = 1;

            $periode = tgl_indo(date('d-m-Y',strtotime($tgl_dari))) .' - '.tgl_indo(date('d-m-Y',strtotime($tgl_sampai)));

            // SET JUDUL
            $sheet->SetCellValue('A'.$rowCount, 'PT. HEKSATEX INDAH');
            $sheet->mergeCells('A'.$rowCount.':h'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $rowCount++;

            $sheet->SetCellValue('A'.$rowCount, 'BUKU BESAR GLOBAL');
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $rowCount++;

            // set periode
            $sheet->SetCellValue('A'.$rowCount, ''.$periode );
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


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

            $table_head_columns  = array('No', 'Kode Acc','Nama Acc','Saldo Normal','Saldo Awal','Debit','Credit','Saldo Akhir');
			$column = 0;
			foreach ($table_head_columns as $field) {
				$object->getActiveSheet()->setCellValueByColumnAndRow($column, 5, $field);  
				$column++;
			}
	
			// set width and border
			$index_header = array('A','B','C','D','E','F','G','H');
			$loop = 0;
			foreach ($index_header as $val) {

				$object->getActiveSheet()->getStyle($val.'5')->applyFromArray($styleArrayColor);

				if($loop == 0){
					$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true); // index A
                }else if($loop ==  1){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(10); // index B
				}else if($loop == 2 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(35); // index C
				}else if( $loop > 2 ){
					$object->getSheet(0)->getColumnDimension($val)->setWidth(20); // index D -> G
				}
	
				$loop++;
			}
            
            $data = $this->proses_data($tgl_dari,$tgl_sampai,$checkhidden);
            $s_awal = 0;
            $s_akhir = 0;
            $debit = 0;
            $credit = 0;
            $num  = 1;
            $rowCount = $rowCount + 3;
            foreach($data as $row){
				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $row['kode_acc']);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $row['nama_acc']);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $row['saldo_normal']);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row['saldo_awal']);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $row['debit']);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount, $row['credit']);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row['saldo_akhir']);

				$object->getActiveSheet()->getStyle('E'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
				$object->getActiveSheet()->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
				$object->getActiveSheet()->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
				$object->getActiveSheet()->getStyle('H'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');

                $s_akhir = $s_akhir + $row['saldo_akhir'];
                $s_awal = $s_awal + $row['saldo_awal'];
                $debit = $debit + $row['debit'];
                $credit = $credit + $row['credit'];
                $rowCount++;
            }

            $rowCount++;
			$object->getActiveSheet()->SetCellValue('A'.$rowCount, '');
            $sheet->mergeCells('A'.$rowCount.':D'.$rowCount);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $s_awal);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $debit);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $credit);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $s_akhir);
			
            $object->getActiveSheet()->getStyle('E'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('F'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('G'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');
            $object->getActiveSheet()->getStyle('H'.$rowCount)->getNumberFormat()->setFormatCode('#,##0.00');


            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
            $object->save('php://output');
    
            $xlsData = ob_get_contents();
            ob_end_clean();

            $name_file ='Buku Besar Global Periode '.$periode.'.xlsx';
    
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


    function export_excel_detail()
    {
        try {
            //code...
	        $arr_filter = $this->input->post('arr_filter');
            $view       = $arr_filter[0]['view'] ?? '';
            if($view == 'N'){
               $this->export_excel_detail_normal($arr_filter);
            } else {
                // $views = 'Lawan Credit';
                $this->export_excel_detail_lawan($arr_filter);
            }

        } catch (Exception $ex) {
            $this->output->set_status_header(($ex->getCode()) ?? 500)
                ->set_content_type('application/json', 'utf-8')
                ->set_output(json_encode(array('message' => $ex->getMessage(), 'icon' => 'fa fa-warning', 'type' => 'danger')));
        }
     
    }
    

    function export_excel_detail_normal($arr_filter)
    {
        try {
            //code...
            $this->load->library('excel');


            $tgl_dari   = $arr_filter[0]['tgldari'] ?? '';
            $tgl_sampai = $arr_filter[0]['tglsampai'] ?? '';
            $checkhidden= $arr_filter[0]['checkhidden'] ?? '';
            $coa        = $arr_filter[0]['coa'] ?? '';
            $view       = $arr_filter[0]['view'] ?? '';
            $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgl_dari));
            $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tgl_sampai));

            $gc  = $this->m_bukubesar->get_coa_by_kode($coa);

            if($view == 'N'){
                $views = 'Normal';
            } else if($view == 'D'){
                $views = 'Lawan Debit';
            } else {
                $views = 'Lawan Credit';
            }

            $where  = ["je.status"=>"posted",'je.tanggal_dibuat >= '=>$tgl_dari,'je.tanggal_dibuat <= '=>$tgl_sampai];
            // if(!empty($coa)){
            //     $where = array_merge($where,array("coa.kode_coa"=>$coa));
            // }

            ob_start();
            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);


            // $object->createSheet();
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
            $rowCount++;

            // set Judul
            $sheet->SetCellValue('A'.$rowCount, 'BUKU BESAR DETAIL');
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $rowCount++;


            // set periode
            $sheet->SetCellValue('A'.$rowCount, $periode );
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $rowCount = 4;
            $sheet->SetCellValue('A'.$rowCount, $gc->kode_coa.' - '.$gc->nama.' - '.$views);
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
            

            $data = $this->proses_data2($tgl_dari,$tgl_sampai,$coa,$where);
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
                $this->create_thead($rowCount, $activeSheet,$activeSheet,$view);
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

            $name_file ='Buku Besar Detail '.$views.' Periode '.$periode.'.xlsx';
    
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


     function export_excel_detail_lawan($arr_filter)
    {
        try {
            //code...
            $this->load->library('excel');

            $tgl_dari   = $arr_filter[0]['tgldari'] ?? '';
            $tgl_sampai = $arr_filter[0]['tglsampai'] ?? '';
            $checkhidden= $arr_filter[0]['checkhidden'] ?? '';
            $coa        = $arr_filter[0]['coa'] ?? '';
            $view       = $arr_filter[0]['view'] ?? '';
            $tgl_dari   = date('Y-m-d H:i:s', strtotime($tgl_dari));
            $tgl_sampai = date('Y-m-d 23:59:59', strtotime($tgl_sampai));

            $gc  = $this->m_bukubesar->get_coa_by_kode($coa);

            if($view == 'N'){
                $views = 'Normal';
            } else if($view == 'D'){
                $views = 'Lawan Debit';
            } else {
                $views = 'Lawan Credit';
            }

            $where  = ["je.status"=>"posted",'je.tanggal_dibuat >= '=>$tgl_dari,'je.tanggal_dibuat <= '=>$tgl_sampai];
           

            ob_start();
            $object = new PHPExcel();
            $object->setActiveSheetIndex(0);


            $object->createSheet();
            $sheet = $object->setActiveSheetIndex(0);
            $sheet->setTitle('DETAIL');
            $activeSheet = $object->getActiveSheet();
            // $getSheet  = $object->getSheet(0);
            $activeSheet->setShowGridlines(true);

            $rowCount = 1;
            $loop     = 1;

            $periode = tgl_indo(date('d-m-Y',strtotime($tgl_dari))) .' - '.tgl_indo(date('d-m-Y',strtotime($tgl_sampai)));
            // set Judul
            $sheet->SetCellValue('A'.$rowCount, 'PT. HEKSATEX INDAH');
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $rowCount++;

            // set Judul
            $sheet->SetCellValue('A'.$rowCount, 'BUKU BESAR DETAIL');
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $rowCount++;


            // set periode
            $sheet->SetCellValue('A'.$rowCount, $periode );
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $rowCount = 4;
            $sheet->SetCellValue('A'.$rowCount, $gc->kode_coa.' - '.$gc->nama.' - '.$views);
            $sheet->mergeCells('A'.$rowCount.':H'.$rowCount);
            $sheet->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            //bold huruf
            $activeSheet->getStyle("A1:H6")->getFont()->setBold(true);

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
            
            $this->create_thead(6, $activeSheet,$activeSheet,$view);

            $data = $this->proses_data3($where,$coa,$view);
            $rowCount = 7;
            $num      = 1;
            $tmp_kode_entries = '';
            $total_nominal = 0;
            foreach ($data as $datas) {

                if($tmp_kode_entries != $datas['kode_entries'] && $num != 1){   
                   $num = 1;
                }

                if($tmp_kode_entries != "" ){
                    $tmp_kode_entries = $datas['kode_entries'];
                }

                $activeSheet->SetCellValue('A' . $rowCount, ($num));
                $activeSheet->SetCellValue('B' . $rowCount, $datas['tanggal']);
                $activeSheet->SetCellValue('C' . $rowCount, $datas['kode_entries']);
                $activeSheet->SetCellValue('D' . $rowCount, $datas['origin']);
                $activeSheet->SetCellValue('E' . $rowCount, $datas['keterangan']);
                $activeSheet->SetCellValue('F' . $rowCount, $datas['debit_or_credit']);
                $activeSheet->SetCellValue('G' . $rowCount, $datas['lawan']);
                $activeSheet->SetCellValue('H' . $rowCount, $datas['nominal']);

                $activeSheet->getStyle('F' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
                $activeSheet->getStyle('G' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');
                $activeSheet->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');

                $num++;
                $rowCount++;
                $total_nominal = $total_nominal + $datas['nominal'];
            }

            $activeSheet->SetCellValue('F' . $rowCount, 0.00);
            $activeSheet->SetCellValue('H' . $rowCount, $total_nominal);
            $activeSheet->getStyle('H' . $rowCount)->getNumberFormat()->setFormatCode('#,##0. 00');

            $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
            $object->save('php://output');
    
            $xlsData = ob_get_contents();
            ob_end_clean();

            $name_file ='Buku Besar Detail '.$views.' Periode '.$periode.'.xlsx';
    
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

    function create_thead($rowCount, $activeSheet, $getSheet,$view)
    {
        if($view == 'N'){
            $table_head_columns  = array('No', 'Tanggal', 'Kode Entries', 'Origin', 'Keterangan', 'Debit', 'Credit', 'Saldo');
        } else if ($view == 'D'){
            $table_head_columns  = array('No', 'Tanggal', 'Kode Entries', 'Origin', 'Keterangan', 'Debit', 'Credit (Lawan)', 'Saldo');
        } else {
            $table_head_columns  = array('No', 'Tanggal', 'Kode Entries', 'Origin', 'Keterangan', 'Credit', 'Debit (Lawan)', 'Saldo');
        }
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
        foreach($data_arr as $rows){
            $tgl_dari = $rows['tgldari'];
            $tgl_sampai = $rows['tglsampai'];
            $checkhidden = $rows['checkhidden'];
        }

        $data = $this->proses_data($tgl_dari,$tgl_sampai,$checkhidden);

        $data['list'] = $data;
        $data['tgl_dari']   = tgl_indo(date('d-m-Y',strtotime($tgl_dari)));
        $data['tgl_sampai'] = tgl_indo(date('d-m-Y',strtotime($tgl_sampai)));
        $cnt = $this->load->view('accounting/v_bukubesar_pdf', $data, true);
        $this->dompdflib->generate($cnt);
    }
}
