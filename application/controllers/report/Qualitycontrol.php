<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');
/**
 * 
 */
class Qualitycontrol extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("_module");//load model global
		$this->load->model("m_qualityControl");

	}

   
	public function index()
	{	
        $id_dept	     ='RQC';
        $data['id_dept'] = $id_dept;

		$this->load->view('report/v_quality_control', $data);
	}

	public function get_departement_select2()
	{
		$nama  = addslashes($this->input->post('nama'));
   		$callback = $this->m_qualityControl->get_list_departement_select2($nama);
        echo json_encode($callback);
	}


	public function loadData()
	{
		$tgldari   = date('Y-m-d', strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d', strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('id_dept');

		date_default_timezone_set('Asia/Jakarta');

		$mrpRecord= [];
		$dataMesin = [];
		$jmlhari    = 7;
		$tglawal    = date('Y-m-d 07:00:00', strtotime($tgldari));
		$tglakhir    = date('Y-m-d 06:59:59', strtotime('+1 days', strtotime($tglsampai)));

		$tgl1       = new DateTime($tglawal);
		$tgl2       = new DateTime($tglakhir);

		$diff_hari   = date_diff($tgl1, $tgl2);
		$jmlhari     = $diff_hari->format('%d');
		$eff         = 0;

		// get list mesin by id_dept
		$get_mesin = $this->m_qualityControl->get_list_mesin($id_dept);
		foreach($get_mesin as $rmc){

			// get mrp_production
			$get_mrp = $this->m_qualityControl->get_list_produk_by_tgl($rmc->mc_id,$id_dept,$tgldari,$tglsampai,$jmlhari);
			foreach($get_mrp as $row){

				if($row->target_periode > 0){
					$eff = (($row->tot_mtr/$row->target_periode)*100)/$row->tot_mo;
					if($eff>100){
						$eff = 100;
					}
				}

				// tot hph per periode
				$mrpRecord[] = array(
					'nama_mesin' => $row->nama_mesin,
					'nama_produk'=> $row->nama_produk,
					'hph_mtr'    => number_format($row->tot_mtr,2),
					'hph_kg'     => number_format($row->tot_kg,2),
					'hph_gl'     => ($row->tot_gl),
					'efisisensi' => round($eff,2),
					'grade_A'   => ($row->grade_A),
					'grade_B'   => ($row->grade_B),
					'grade_C'   => ($row->grade_C),
					 );
					 
			}

			$dataMesin[] = array('nama_mesin' => $rmc->nama_mesin,
								 'mrp' => $mrpRecord,
								 'hph_mtr'=> 0,
								 'hph_kg'=> 0,
								 'hph_gl'=> 0,
								 'efisisensi'=> 0,
								 'grade_A'   => 0,
								 'grade_B'   => 0,
								 'grade_C'   => 0,
								);
			$mrpRecord = [];

		}

		$callback = array('sucess'=>'Yes', 'record'=>$dataMesin );

		echo json_encode($callback);

	}


	function export_excel()
    {

    	$this->load->library('excel');
		$tgldari   = date('Y-m-d',strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d',strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('departemen');
		$dept      = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();

		$object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

    	// SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Quality Control [QC]');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:N1');
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

    	$table_head_columns = array('No','Mesin','Product/Corak','Standar Mtr', 'Standar Kg','RPM', 'Total Produksi','Mtr','Kg','Gl', 'Efisiensi (%)', 'Grade','A', 'B','C','Keterangan');

    	$column = 0;
    	$merge  = TRUE;
        $columns= '';
        $count_merge = 0;
    	foreach ($table_head_columns as $field) {

    		if($column < 6 or $column == 10 OR $column == 15){
				if($column == 15){
					$count_merge = 2;
				}
    			$columns = $column-$count_merge;
	    		$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 5, $field);  
    	        $object->getActiveSheet()->mergeCellsByColumnAndRow($columns, 5, $columns, 6);
    		}
			
    		if(($column >= 6 AND $column <= 9) OR ($column >= 11 AND $column <= 14 )){
                if($column == 11 ){$merge = TRUE;}

                if($merge == TRUE){
					$columns = $column-$count_merge;
                    $object->getActiveSheet()->setCellValueByColumnAndRow($columns, 5, $field);  
                    if($column == 6){
                        $object->getActiveSheet()->mergeCells('G5:I5');// merge cell Total Produksi
                    }else if($column ==11 ){
                        $object->getActiveSheet()->mergeCells('K5:M5');// merge cell grade
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
        $index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N');
        $loop = 1;
       
       	foreach ($index_header as $val) {

       		// set border
            $object->getActiveSheet()->getStyle($val.'5')->applyFromArray($styleArray);
            $object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);

            if($loop <= 3){ // index A, B, C
                $object->getSheet(0)->getColumnDimension($val)->setAutoSize(true);
            }elseif(($loop >= 3 AND $loop <= 8) OR ($loop >= 9 AND $loop <=12)){ // index D, E, F, G, H, I, K, L, M
                $object->getSheet(0)->getColumnDimension($val)->setWidth(9);
            }elseif($loop == 9 OR $loop == 14){ // index J, N
                $object->getSheet(0)->getColumnDimension($val)->setWidth(15);
            }

            // midle center
       		$object->getActiveSheet()->getStyle($val.'5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
            $object->getActiveSheet()->getStyle($val.'6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER );

            $loop++;
       	}

       	// set wrap text index d-J
        $object->getActiveSheet()->getStyle('D5:J'.$object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);


    	// tbody
        $rowCount = 7;
    	$num 	   = 1;
		$tglawal   = date('Y-m-d 07:00:00', strtotime($tgldari));
		$tglakhir  = date('Y-m-d 07:00:00', strtotime('+1 days', strtotime($tglsampai)));
		$tgl1       = new DateTime($tglawal);
		$tgl2       = new DateTime($tglakhir);
		$diff_hari   = date_diff($tgl1, $tgl2);
		$jmlhari     = $diff_hari->format('%d');
		$eff         = 0;

		// get list mesin by id_dept
		$get_mesin = $this->m_qualityControl->get_list_mesin($id_dept);
		foreach($get_mesin as $rmc){

			// get mrp_production
			$get_mrp = $this->m_qualityControl->get_list_produk_by_tgl($rmc->mc_id,$id_dept,$tgldari,$tglsampai,$jmlhari);

			

			if(count($get_mrp) == 0){ // cek array getm_mrp jika == 0
				$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $rmc->nama_mesin);
				$object->getActiveSheet()->SetCellValue('G'.$rowCount,'0');
				$object->getActiveSheet()->SetCellValue('H'.$rowCount,'0');
				$object->getActiveSheet()->SetCellValue('I'.$rowCount,'0');
				$object->getActiveSheet()->SetCellValue('J'.$rowCount,'0');
				$object->getActiveSheet()->SetCellValue('K'.$rowCount,'0');
				$object->getActiveSheet()->SetCellValue('L'.$rowCount,'0');
				$object->getActiveSheet()->SetCellValue('M'.$rowCount,'0');
				$rowCount++;
			}else{
				foreach($get_mrp  as $row){

					if($row->target_periode > 0){
						$eff = (($row->tot_mtr/$row->target_periode)*100)/$row->tot_mo;
						if($eff > 100){
							$eff = 100;
						}
					}
					$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
					$object->getActiveSheet()->SetCellValue('B'.$rowCount, $row->nama_mesin);
					$object->getActiveSheet()->SetCellValue('C'.$rowCount, $row->nama_produk);
					$object->getActiveSheet()->SetCellValue('G'.$rowCount, $row->tot_mtr);
					$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row->tot_kg);
					$object->getActiveSheet()->SetCellValue('I'.$rowCount, $row->tot_gl);
					$object->getActiveSheet()->SetCellValue('J'.$rowCount, round($eff,2));
					$object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->grade_A);
					$object->getActiveSheet()->SetCellValue('L'.$rowCount, $row->grade_B);
					$object->getActiveSheet()->SetCellValue('M'.$rowCount, $row->grade_C);

					$rowCount++;
				}
			}

		}

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
        header('Content-Disposition: attachment;filename=Laporan QC '.$dept['nama'].' .xls '); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache
        $object->save('php://output');	
    }

}