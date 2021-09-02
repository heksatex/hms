<?php defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Efisiensi extends MY_Controller
{
    public function __construct()
    {
        parent:: __construct();
        $this->is_loggedin();//cek apakah user sudah login
        $this->load->model('_module');
        $this->load->model('m_efisiensi');
            
    }


    public function index()
    {
    	$data['id_dept'] = 'EFI';
    	$this->load->view('report/v_efisiensi', $data);
    }

    public function get_departement_select2()
	{
		$nama  = addslashes($this->input->post('nama'));
   		$callback = $this->m_efisiensi->get_list_departement_select2($nama);
        echo json_encode($callback);
	}

    function loadData()
    {
    	$tgldari   = date('Y-m-d',strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d',strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('id_dept');


		$dataRecord= [];
		$mrpRecord= [];
		$dataMesin = [];
		$sc = '';
		$get_hph_pagi = '';
		$get_hph_siang = '';
		$get_hph_malam = '';
		$ef_per_hari   = 0;
		$ef_pagi       = 0;
		$ef_siang      = 0;
		$ef_malam      = 0;
		$av_per_hari   = 0;
		$av_pagi    = 0;
		$av_siang   = 0;
		$av_malam   = 0;

		while ($tgldari <= $tglsampai) {

			
			$jml_child = 0;
			$sum_ef_per_hari = 0;
			$sum_ef_pagi  = 0;
			$sum_ef_siang = 0;
			$sum_ef_malam  = 0;
			$target_efisiensi = 0;

			// get list mesin by id_dept
			$get_mesin = $this->m_efisiensi->get_list_mesin($id_dept);

			foreach($get_mesin as $rmc){

				// get mrp_production
				$get_mrp = $this->m_efisiensi->get_list_mrp_by_tgl($rmc->mc_id,$id_dept,$tgldari);
										
				// foreach MO
				foreach ($get_mrp as $row) {


					// epxplode origin
					$exp = explode('|', $row->origin);
					$no  = 1;
					foreach ($exp as $exps) {
						if($no == 1){
							$sc = trim($exps);
						}
						$no++;
					}

					$target_efisiensi = $row->target_efisiensi*24;
					// hph per shit
					$get_hph_pagi = $this->m_efisiensi->get_list_hph_by_date($row->kode,$tgldari,$row->kode_produk,'pagi');
					$get_hph_siang = $this->m_efisiensi->get_list_hph_by_date($row->kode,$tgldari,$row->kode_produk,'siang');
					$get_hph_malam = $this->m_efisiensi->get_list_hph_by_date($row->kode,$tgldari,$row->kode_produk,'malam');
					$hph_per_hari  = $get_hph_pagi+$get_hph_siang+$get_hph_malam;

					// efisiensi
					if($hph_per_hari > 0 AND $target_efisiensi > 0 ){
						$ef_per_hari  = ($hph_per_hari/$target_efisiensi)*100;
						if($ef_per_hari > 100){
							$ef_per_hari = 100;
						}

						if($get_hph_pagi > 0){
							$ef_pagi      = $get_hph_pagi/($target_efisiensi/3)*100;
							if($ef_pagi > 100){
								$ef_pagi = 100;
							}
						}

						if($get_hph_siang > 0){
							$ef_siang     = $get_hph_siang/($target_efisiensi/3)*100;
							if($ef_siang > 100){
								$ef_siang = 100;
							}
						}

						if($get_hph_malam > 0){
							$ef_malam     = $get_hph_malam/($target_efisiensi/3)*100;
							if($ef_malam > 100){
								$ef_malam = 100;
							}
						}
					}

					if($hph_per_hari   > 0){
						$jml_child++;

						$sum_ef_per_hari = $sum_ef_per_hari + $ef_per_hari; 
						$sum_ef_pagi     = $sum_ef_pagi + $ef_pagi;
						$sum_ef_siang    = $sum_ef_siang+ $ef_siang;
						$sum_ef_malam    = $sum_ef_malam + $ef_malam;

							// $tgl = date('Y-m-d',strtotime($row->tanggal));
						// child 
						$mrpRecord[] = array('tgl' => $tgldari,
											'kode'=> $row->kode,
											'nama_mesin' => $row->nama_mesin,
											'sc'         => $sc,
											'nama_produk'=> $row->nama_produk,
											'efisiensi'  => number_format($target_efisiensi,2),
											'hph_per_hari'=> number_format($hph_per_hari,2),
											'hph_pagi'   => number_format($get_hph_pagi,2),
											'hph_siang'  => number_format($get_hph_siang,2),
											'hph_malam'  => number_format($get_hph_malam,2),
											'ef_per_hari'=> number_format($ef_per_hari,2),
											'ef_pagi'	  => number_format($ef_pagi,2),
											'ef_siang'	  => number_format($ef_siang,2),
											'ef_malam'	  => number_format($ef_malam,2)
											);
					}
					
					$sc = '';
					$hph_per_hari = 0;
					$ef_per_hari   = 0;				
					$ef_pagi       = 0;
					$ef_siang      = 0;
					$ef_malam      = 0;
					$target_efisiensi = 0;
				}

				$dataMesin[] =  array('tgl' => $tgldari, 
									    'nama_mesin' => $rmc->nama_mesin,
									    'mrp'=> $mrpRecord,
									    'efisiensi'  => 0,
										'hph_per_hari'=> 0,
										'hph_pagi'   => 0,
										'hph_siang'  => 0,
										'hph_malam'  => 0,
										'ef_per_hari'=> 0,
										'ef_pagi'	 => 0,
										'ef_siang'	 => 0,
										'ef_malam'	 => 0
									);
				if(empty($mrpRecord)){
					$jml_child++;
				}

				$mrpRecord = [];


			}
			
			if($sum_ef_per_hari > 0){
				$av_per_hari = $sum_ef_per_hari / $jml_child; // average hari
				if($av_per_hari > 100){
					$av_per_hari = 100;
				}

				if($sum_ef_pagi > 0 ){
					$av_pagi = $sum_ef_pagi / $jml_child; // average pagi
					if($av_pagi > 100){
						$av_pagi = 100;
					}
				}
				if($sum_ef_siang > 0 ){
					$av_siang = $sum_ef_siang / $jml_child; // average siang
					if($av_siang > 100){
						$av_siang = 100;
					}
				}
				if($sum_ef_malam > 0 ){
					$av_malam = $sum_ef_malam / $jml_child; // average malam
					if($av_malam > 100){
						$av_malam = 100;
					}
				}
			}

			// parents
			$dataRecord[] = array('tgl'     => $tgldari, 
								  'mesin'   => $dataMesin, 
								  'av_hari' => number_format($av_per_hari,2),
								  'av_pagi' => number_format($av_pagi,2),
								  'av_siang' => number_format($av_siang,2),
								  'av_malam' => number_format($av_malam,2),
								   );

			$dataMesin = [];

			

			$tgldari = date('Y-m-d',strtotime('+1 days',strtotime($tgldari)));

			$av_per_hari= 0;
			$av_pagi    = 0;
			$av_siang   = 0;
			$av_malam   = 0;
			

		}

		//$allcount           = $this->m_efisiensi->getRecordCountHPH($where);
		//$allcount  = 10;
        //$total_record       = 'Total Data : '. number_format($allcount);

		$callback = array('record' => $dataRecord,);

		echo json_encode($callback);
    }


    function export_excel()
    {

    	$this->load->library('excel');
		$tgldari   = date('Y-m-d',strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d',strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('departemen');
		$dept    = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();

		$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Efisiensi');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');
		//$object->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		// set Departemen
 		$object->getActiveSheet()->SetCellValue('A2', 'Departemen');
		$object->getActiveSheet()->mergeCells('A2:B2');
 		$object->getActiveSheet()->SetCellValue('C2', ': '.$dept['nama']);
		$object->getActiveSheet()->mergeCells('C2:D2');


		// set periode
 		$object->getActiveSheet()->SetCellValue('A3', 'Periode');
		$object->getActiveSheet()->mergeCells('A3:B3');
 		$object->getActiveSheet()->SetCellValue('C3', ': '.tgl_indo(date('d-m-Y',strtotime($tgldari))).' - '.tgl_indo(date('d-m-Y',strtotime($tglsampai)) ));
		$object->getActiveSheet()->mergeCells('C3:F3');

 		//bold huruf
		$object->getActiveSheet()->getStyle("A1:O6")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

    	$table_head_columns = array('No','Tanggal','Mesin','MO','SC','Nama Produk','Target Efisiensi (Qty/Hari)', 'HPH','Hari','Pagi','Siang','Malam','Efisiensi Produksi (%)','Hari','Pagi','Siang','Malam');

    	$column = 0;
    	$merge  = TRUE;
        $columns= '';
        $count_merge = 0;
    	foreach ($table_head_columns as $field) {

    		if($column < 7){
    			$columns = $column;
	    		$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 5, $field);  
    	        $object->getActiveSheet()->mergeCellsByColumnAndRow($columns, 5, $columns, 6);
    		}

    		if(($column >= 7 AND $column <= 11) OR ($column >= 12 AND $column <=16 )){
                if($column == 12 ){$merge = TRUE;}

                if($merge == TRUE){
					$columns = $column-$count_merge;
                    $object->getActiveSheet()->setCellValueByColumnAndRow($columns, 5, $field);  
                    if($column == 7){
                        $object->getActiveSheet()->mergeCells('H5:K5');// merge cell HPH
                    }else if($column == 12){
                        $object->getActiveSheet()->mergeCells('L5:O5');// merge cell efisiensi %
                    }
                   $count_merge++;

                }elseif($merge == FALSE){
 					$columns = $column-$count_merge;
                    $object->getActiveSheet()->setCellValueByColumnAndRow($columns, 6, $field);    
 						
                }
                $merge = FALSE;
    		}

    		$column++;
    	}


    	// set column header
        $index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
        $loop = 1;
       
       	foreach ($index_header as $val) {

       		// set border
            $object->getActiveSheet()->getStyle($val.'5')->applyFromArray($styleArray);
            $object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);

            if($loop <= 2){ // index A, B, D
                $object->getSheet(0)->getColumnDimension($val)->setAutoSize(true);
            }elseif($loop == 3){ // index C
                $object->getSheet(0)->getColumnDimension($val)->setWidth(38);
            }elseif($loop == 5 OR ($loop >= 8 AND $loop<= 15 )){// index E, H-O
                $object->getSheet(0)->getColumnDimension($val)->setWidth(9);
            }elseif($loop == 6){ // index F
                $object->getSheet(0)->getColumnDimension($val)->setWidth(28);
            }else if($loop == 7){ // index G
                $object->getSheet(0)->getColumnDimension($val)->setWidth(15);
            }


            // midle center
       		$object->getActiveSheet()->getStyle($val.'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );

            $loop++;
       	}

       	// set wrap text index G
        $object->getActiveSheet()->getStyle('G5:K'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);


    	// tbody
        $rowCount = 7;
    	$mrpRecord = [];
		$dataMesin = [];
    	$sc = '';
		$get_hph_pagi = '';
		$get_hph_siang = '';
		$get_hph_malam = '';
		$ef_per_hari   = 0;
		$ef_pagi       = 0;
		$ef_siang      = 0;
		$ef_malam      = 0;
		$av_per_hari   = 0;
		$av_pagi    = 0;
		$av_siang   = 0;
		$av_malam   = 0;
		$num = 1;

		while ($tgldari <= $tglsampai) {

			// get list mesin by id_dept
			$get_mesin = $this->m_efisiensi->get_list_mesin($id_dept);
			$jml_child = 0;
			$sum_ef_per_hari = 0;
			$sum_ef_pagi  = 0;
			$sum_ef_siang = 0;
			$sum_ef_malam  = 0;

			foreach($get_mesin as $rmc){

				// get mrp_production
				$get_mrp = $this->m_efisiensi->get_list_mrp_by_tgl($rmc->mc_id,$id_dept,$tgldari);
				
				foreach ($get_mrp as $row) {


					// epxplode origin
					$exp = explode('|', $row->origin);
					$no  = 1;
					foreach ($exp as $exps) {
						if($no == 1){
							$sc = trim($exps);
						}
						$no++;
					}

					$target_efisiensi = $row->target_efisiensi*24;
					// hph per shit
					$get_hph_pagi = $this->m_efisiensi->get_list_hph_by_date($row->kode,$tgldari,$row->kode_produk,'pagi');
					$get_hph_siang = $this->m_efisiensi->get_list_hph_by_date($row->kode,$tgldari,$row->kode_produk,'siang');
					$get_hph_malam = $this->m_efisiensi->get_list_hph_by_date($row->kode,$tgldari,$row->kode_produk,'malam');
					$hph_per_hari  = $get_hph_pagi+$get_hph_siang+$get_hph_malam;

					// efisiensi
					if($hph_per_hari > 0 AND $target_efisiensi > 0 ){
						$ef_per_hari  = ($hph_per_hari/$target_efisiensi)*100;
						if($ef_per_hari > 100){
							$ef_per_hari = 100;
						}

						if($get_hph_pagi > 0){
							$ef_pagi      = $get_hph_pagi/($target_efisiensi/3)*100;
							if($ef_pagi > 100){
								$ef_pagi = 100;
							}
						}

						if($get_hph_siang > 0){
							$ef_siang     = $get_hph_siang/($target_efisiensi/3)*100;
							if($ef_siang > 100){
								$ef_siang = 100;
							}
						}

						if($get_hph_malam > 0){
							$ef_malam     = $get_hph_malam/($target_efisiensi/3)*100;
							if($ef_malam > 100){
								$ef_malam = 100;
							}
						}
					}


					if($hph_per_hari   > 0){
						$jml_child++;

						$sum_ef_per_hari = $sum_ef_per_hari + $ef_per_hari; 
						$sum_ef_pagi     = $sum_ef_pagi + $ef_pagi;
						$sum_ef_siang    = $sum_ef_siang+ $ef_siang;
						$sum_ef_malam    = $sum_ef_malam + $ef_malam;

							// $tgl = date('Y-m-d',strtotime($row->tanggal));
						// child 
						$mrpRecord[] = array('tgl' => $tgldari,
											'kode'=> $row->kode,
											'nama_mesin' => $row->nama_mesin,
											'sc'         => $sc,
											'nama_produk'=> $row->nama_produk,
											'efisiensi'  => ($target_efisiensi),
											'hph_per_hari'=> round($hph_per_hari,2),
											'hph_pagi'   => round($get_hph_pagi,2),
											'hph_siang'  => round($get_hph_siang,2),
											'hph_malam'  => round($get_hph_malam,2),
											'ef_per_hari'=> round($ef_per_hari,2),
											'ef_pagi'	  => round($ef_pagi,2),
											'ef_siang'	  => round($ef_siang,2),
											'ef_malam'	  => round($ef_malam,2)
											);
					}

					$sc = '';
					$ef_per_hari   = 0;				
					$ef_pagi       = 0;
					$ef_siang      = 0;
					$ef_malam      = 0;
					

				}

				$dataMesin[] =  array('tgl' => $tgldari, 
									'nama_mesin' => $rmc->nama_mesin,
									'mrp'=> $mrpRecord,
									'efisiensi'  => 0,
									'hph_per_hari'=> 0,
									'hph_pagi'   => 0,
									'hph_siang'  => 0,
									'hph_malam'  => 0,
									'ef_per_hari'=> 0,
									'ef_pagi'	 => 0,
									'ef_siang'	 => 0,
									'ef_malam'	 => 0
								);
				if(empty($mrpRecord)){
					$jml_child++;
				}

				$mrpRecord = [];

			}

			if($sum_ef_per_hari > 0){
				$av_per_hari = $sum_ef_per_hari / $jml_child; // average hari
				if($av_per_hari > 100){
					$av_per_hari = 100;
				}
				if($sum_ef_pagi > 0 ){
					$av_pagi = $sum_ef_pagi / $jml_child; // average pagi
					if($av_pagi > 100){
						$av_pagi = 100;
					}
				}
				if($sum_ef_siang > 0 ){
					$av_siang = $sum_ef_siang / $jml_child; // average siang
					if($av_siang > 100){
						$av_siang = 100;
					}
				}
				if($sum_ef_malam > 0 ){
					$av_malam = $sum_ef_malam / $jml_child; // average malam
					if($av_malam > 100){
						$av_malam = 100;
					}
				}
			}

			$object->getActiveSheet()->getStyle("A".$rowCount.":O".$rowCount)->getFont()->setBold(true);
			// parent
			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $tgldari);
            $object->getActiveSheet()->SetCellValue('L'.$rowCount, round($av_per_hari,2));
            $object->getActiveSheet()->SetCellValue('M'.$rowCount, round($av_pagi,2));
            $object->getActiveSheet()->SetCellValue('N'.$rowCount, round($av_siang,2));
            $object->getActiveSheet()->SetCellValue('O'.$rowCount, round($av_malam,2));

            // set align center 
            $object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $rowCount++;

            // child
            foreach ($dataMesin as $val) {	
				

				$object->getActiveSheet()->getStyle("A".$rowCount.":O".$rowCount)->getFont()->setBold(FALSE);

				

				if(!empty($val['mrp'])){

					foreach($val['mrp'] as $val2){
						$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val['tgl']);
						$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val['nama_mesin']);
						$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val2['kode']);
						$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val2['sc']);
						$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val2['nama_produk']);
						$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val2['efisiensi']);
						$object->getActiveSheet()->SetCellValue('H'.$rowCount, ($val2['hph_per_hari']));
						$object->getActiveSheet()->SetCellValue('I'.$rowCount, ($val2['hph_pagi']));
						$object->getActiveSheet()->SetCellValue('J'.$rowCount, ($val2['hph_siang']));
						$object->getActiveSheet()->SetCellValue('K'.$rowCount, ($val2['hph_malam']));
						$object->getActiveSheet()->SetCellValue('L'.$rowCount, ($val2['ef_per_hari']));
						$object->getActiveSheet()->SetCellValue('M'.$rowCount, ($val2['ef_pagi']));
						$object->getActiveSheet()->SetCellValue('N'.$rowCount, ($val2['ef_siang']));
						$object->getActiveSheet()->SetCellValue('O'.$rowCount, ($val2['ef_malam']));

						// set align center
						$object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$object->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$object->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$object->getActiveSheet()->getStyle('G'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

						$rowCount++;
					}
				}else{
						$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val['tgl']);
						$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val['nama_mesin']);		
						$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val['efisiensi']);
						$object->getActiveSheet()->SetCellValue('H'.$rowCount, ($val['hph_per_hari']));
						$object->getActiveSheet()->SetCellValue('I'.$rowCount, ($val['hph_pagi']));
						$object->getActiveSheet()->SetCellValue('J'.$rowCount, ($val['hph_siang']));
						$object->getActiveSheet()->SetCellValue('K'.$rowCount, ($val['hph_malam']));
						$object->getActiveSheet()->SetCellValue('L'.$rowCount, ($val['ef_per_hari']));
						$object->getActiveSheet()->SetCellValue('M'.$rowCount, ($val['ef_pagi']));
						$object->getActiveSheet()->SetCellValue('N'.$rowCount, ($val['ef_siang']));
						$object->getActiveSheet()->SetCellValue('O'.$rowCount, ($val['ef_malam']));

						// set align center
						$object->getActiveSheet()->getStyle('B'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$object->getActiveSheet()->getStyle('D'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$object->getActiveSheet()->getStyle('E'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$object->getActiveSheet()->getStyle('G'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

						$rowCount++;
				}

            	

            }

			$tgldari = date('Y-m-d',strtotime('+1 days',strtotime($tgldari)));

			$av_per_hari= 0;
			$av_pagi    = 0;
			$av_siang   = 0;
			$av_malam   = 0;
			$dataMesin = [];
			$num++;

		}// end looping tgldari , tglsampai

		// set border
		$rowCount2  = 7;
		while ($rowCount2 <= $rowCount-1 ) {

			foreach ($index_header as $val) {
	        	$object->getActiveSheet()->getStyle($val.''.$rowCount2)->applyFromArray($styleArray);
			}

			$rowCount2++;
		}

    	$object = PHPExcel_IOFactory::createWriter($object, 'Excel5');  
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename=Laporan Efisiensi '.$dept['nama'].' .xls '); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');	
    }
}