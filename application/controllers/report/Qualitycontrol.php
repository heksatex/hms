<?php
defined('BASEPATH') or exit('No Direct Script Acces Allowed');
/**
 * 
 */
class Qualitycontrol extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin(); //cek apakah user sudah login
		$this->load->model("_module"); //load model global
		$this->load->model("m_qualityControl");
	}

	public function index()
	{
		$id_dept	     = 'RQC';
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

		$mrpRecord = [];
		$dataMesin = [];
		$dataHari  = [];
		$dataEfHari  = [];
		$jmlhari    = 0;
		$tglawal    = date('Y-m-d 00:00:00', strtotime($tgldari));
		// $tglakhir    = date('Y-m-d 07:00:00', strtotime('+1 days', strtotime($tglsampai)));
		$tglakhir    = date('Y-m-d 23:59:59', strtotime($tglsampai));

		$tgl1       = new DateTime($tglawal);
		$tgl2       = new DateTime($tglakhir);

		$diff_hari   = date_diff($tgl1, $tgl2);
		$jmlhari     = $diff_hari->format('%d');
		$eff         = 0;

		// penjabaran periode tgl dari, tgl sampai 
		//$periode = $this->penjabaran_periode_hari($tgldari,$tglsampai);
		//$dataEfHari[] = $periode;

		// get list mesin by id_dept
		$get_mesin = $this->m_qualityControl->get_list_mesin($id_dept);
		foreach ($get_mesin as $rmc) {

			// get mrp_production
			$get_mrp = $this->m_qualityControl->get_list_produk_by_tgl($rmc->mc_id, $id_dept, $tgldari, $tglsampai, $jmlhari);
			foreach ($get_mrp as $row) {

				// total mtr setelah dikurang tot_qty adj
				$tot_mtr = $row->tot_mtr + ($row->tot_qty_adj);

				if ($row->target_periode > 0) {
					$eff = (($tot_mtr / $row->target_periode) * 100) / $row->tot_mo;
					/*
					if($eff>100){
						$eff = 100;
					}
					*/
				}

				$tgldari_loop = $tgldari;
				$tglsampai_loop = $tglsampai;
				$dataHari       = [];
				$ef_hari        = 0;

				// looping tgldari <= tglsampai untuk get ef perhari
				while ($tgldari_loop <= $tglsampai_loop) {

					// get eff perhari
					$ef_result = $this->m_qualityControl->get_ef_hari_by_produk($row->mc_id, $id_dept, $tgldari_loop, $row->kode_produk);
					/*
					if($ef_result['ef']>100){
						$ef = 100;
					}else{
						$ef = $ef_result['ef'];
					}
					*/

					if ($ef_result['target_efisiensi'] > 0) {
						$ef_hari = ($ef_result['tot_mtr'] + ($ef_result['tot_qty_adj'])) / ($ef_result['target_efisiensi'] * 24) * 100;
					}

					$dataEfHari[] = array(
						'tgl' 		=> $tgldari_loop,
						'efisiensi'     => round($ef_hari, 2)
					);
					$dataHari[]  = array('tgl' => $tgldari_loop);
					$tgldari_loop = date('Y-m-d', strtotime('+1 days', strtotime($tgldari_loop)));
					$ef_hari  = 0;
				}

				// total gulung setelah dikurang tot gl adj
				$tot_gl = $row->tot_gl - $row->tot_gl_adj;

				// tot hph per periode
				$mrpRecord[] = array(
					'nama_mesin' => $row->nama_mesin,
					'nama_produk' => $row->nama_produk,
					'hph_mtr'    => number_format($tot_mtr, 2),
					'hph_kg'     => number_format($row->tot_kg, 2),
					'hph_gl'     => $tot_gl,
					'efisisensi' => round($eff, 2),
					'grade_A'   => ($row->grade_A),
					'grade_B'   => ($row->grade_B),
					'grade_C'   => ($row->grade_C),
					'dataEfHari'  => $dataEfHari
				);

				$dataEfHari = [];
			}

			if(empty($dataHari)){
				$dataHari[] = array('tgl' => $tgldari);
			}

			$dataMesin[] = array(
				'nama_mesin' => $rmc->nama_mesin,
				'mrp' => $mrpRecord,
				'hph_mtr' => 0,
				'hph_kg' => 0,
				'hph_gl' => 0,
				'efisisensi' => 0,
				'grade_A'   => 0,
				'grade_B'   => 0,
				'grade_C'   => 0,
			);
			$mrpRecord = [];
		}

		$callback = array('sucess' => 'Yes', 'record' => $dataMesin, 'jmlHari' => $jmlhari+1, 'dataHari' => $dataHari);

		echo json_encode($callback);
	}


	function penjabaran_periode_hari($tgldari, $tglsampai)
	{
		$data[] = '';
		while ($tgldari <= $tglsampai) {
			$data[] = array('tgl' => '1');
			$tgldari = date('Y-m-d', strtotime('+1 days', strtotime($tgldari)));
		}

		return $data;
	}


	function export_excel()
	{

		$tgldari   = date('Y-m-d', strtotime($this->input->post('tgldari')));
		$tglsampai = date('Y-m-d', strtotime($this->input->post('tglsampai')));
		$id_dept   = $this->input->post('id_dept');
		$dept      = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();

		$this->load->library('excel');
        ob_start();
        
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
		$object->getActiveSheet()->SetCellValue('C2', ': ' . $dept['nama']);
		$object->getActiveSheet()->mergeCells('C2:D2');


		// set periode
		$object->getActiveSheet()->SetCellValue('A3', 'Periode');
		$object->getActiveSheet()->mergeCells('A3:B3');
		$object->getActiveSheet()->SetCellValue('C3', ': ' . tgl_indo(date('d-m-Y', strtotime($tgldari))) . ' - ' . tgl_indo(date('d-m-Y', strtotime($tglsampai))));
		$object->getActiveSheet()->mergeCells('C3:F3');

		//bold huruf
		$object->getActiveSheet()->getStyle("A1:N6")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			'borders' => array(
				'allborders' => array(
					'style' => PHPExcel_Style_Border::BORDER_THIN
				)
			)
		);

		$table_head_columns = array('No', 'Mesin', 'Product/Corak', 'Standar Mtr', 'Standar Kg', 'RPM', 'Total Produksi', 'Qty1', 'Qty2', 'Pcs', 'Efisiensi (%)', 'Grade', 'A', 'B', 'C', 'Keterangan');

		$column = 0;
		$merge  = TRUE;
		$columns = '';
		$count_merge = 0;
		foreach ($table_head_columns as $field) {

			if ($column < 6 or $column == 10 or $column == 15) {
				if ($column == 15) {
					$count_merge = 2;
				}
				$columns = $column - $count_merge;
				$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 5, $field);
				$object->getActiveSheet()->mergeCellsByColumnAndRow($columns, 5, $columns, 6);
			}

			if (($column >= 6 and $column <= 9) or ($column >= 11 and $column <= 14)) {
				if ($column == 11) {
					$merge = TRUE;
				}

				if ($merge == TRUE) {
					$columns = $column - $count_merge;
					$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 5, $field);
					if ($column == 6) {
						$object->getActiveSheet()->mergeCells('G5:I5'); // merge cell Total Produksi
					} else if ($column == 11) {
						$object->getActiveSheet()->mergeCells('K5:M5'); // merge cell grade
					}
					$count_merge++;
				} elseif ($merge == FALSE) {
					$columns = $column - $count_merge;
					$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 6, $field);
				}
				$merge = FALSE;
			}
			$column++;
		}


		// set column header
		$index_header = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N');
		$index_header2 = array('O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AW', 'AX', 'AY', 'AZ');
		$loop = 1;

		foreach ($index_header as $val) {

			// set border
			$object->getActiveSheet()->getStyle($val . '5')->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle($val . '6')->applyFromArray($styleArray);

			if ($loop <= 3) { // index A, B, C
				$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true);
			} elseif (($loop >= 3 and $loop <= 8) or ($loop >= 9 and $loop <= 12)) { // index D, E, F, G, H, I, K, L, M
				$object->getSheet(0)->getColumnDimension($val)->setWidth(9);
			} elseif ($loop == 9 or $loop == 14) { // index J, N
				$object->getSheet(0)->getColumnDimension($val)->setWidth(15);
			}

			// midle center
			$object->getActiveSheet()->getStyle($val . '5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$object->getActiveSheet()->getStyle($val . '5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
			$object->getActiveSheet()->getStyle($val . '6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$object->getActiveSheet()->getStyle($val . '6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

			$loop++;
		}

		// set wrap text index d-J
		$object->getActiveSheet()->getStyle('D5:J' . $object->getActiveSheet()->getHighestRow())->getAlignment()->setWrapText(true);


		$tglawal   = date('Y-m-d 00:00:00', strtotime($tgldari));
		$tglakhir  = date('Y-m-d 23:59:59',  strtotime($tglsampai));
		// $tglakhir  = date('Y-m-d 07:00:00', strtotime('+1 days', strtotime($tglsampai)));
		$tgl1       = new DateTime($tglawal);
		$tgl2       = new DateTime($tglakhir);
		$diff_hari  = date_diff($tgl1, $tgl2);
		$jmlhari    = $diff_hari->format('%d');


		// buat array dataHari untuk nama kolom tanggal di excel
		$tgldari_loop = $tgldari;
		$tglsampai_loop = $tglsampai;
		while ($tgldari_loop <= $tglsampai_loop) {
			$dataHari[]  = array('tgl' => $tgldari_loop);
			$tgldari_loop = date('Y-m-d', strtotime('+1 days', strtotime($tgldari_loop)));
		}

		// bvuat kolom tanggal berdasarakan dataHari
		$head_column2 = array(' Efisiensi/Tanggal(%)');
		foreach ($head_column2 as $val) {
			$columns = $column - 2;
			$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 5, $val);

			foreach ($dataHari as $val2) {
				$object->getActiveSheet()->setCellValueByColumnAndRow($columns, 6, $val2['tgl']);
				$columns++;
			}
		}

		/// buat border untuk header ef/tanggal(%)
		$hari   = $jmlhari+1;
		foreach ($index_header2 as $val) {
			// set border
			$object->getActiveSheet()->getStyle($val . '5')->applyFromArray($styleArray);
			$object->getActiveSheet()->getStyle($val . '6')->applyFromArray($styleArray);
			// size kolom
			$object->getSheet(0)->getColumnDimension($val)->setAutoSize(true);
			//bold huruf
			$object->getActiveSheet()->getStyle($val . '5:' . $val . '6')->getFont()->setBold(true);
			// align center
			$object->getActiveSheet()->getStyle($val . '5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$object->getActiveSheet()->getStyle($val . '6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			$hari--;
			if ($hari == 0) {
				break;
			}
		}

		// merge cell untuk header ef/tanggal(%)
		$object->getActiveSheet()->mergeCells('O5:' . $val . '5'); // merge cell 


		// tbody
		$rowCount = 7;
		$num 	   = 1;
		$eff        = 0;

		// get list mesin by id_dept
		$get_mesin = $this->m_qualityControl->get_list_mesin($id_dept);
		foreach ($get_mesin as $rmc) {

			// get mrp_production
			$get_mrp = $this->m_qualityControl->get_list_produk_by_tgl($rmc->mc_id, $id_dept, $tgldari, $tglsampai, $jmlhari);


			if (count($get_mrp) == 0) { // cek array getm_mrp jika == 0
				$object->getActiveSheet()->SetCellValue('A' . $rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B' . $rowCount, $rmc->nama_mesin);
				$object->getActiveSheet()->SetCellValue('G' . $rowCount, '0');
				$object->getActiveSheet()->SetCellValue('H' . $rowCount, '0');
				$object->getActiveSheet()->SetCellValue('I' . $rowCount, '0');
				$object->getActiveSheet()->SetCellValue('J' . $rowCount, '0');
				$object->getActiveSheet()->SetCellValue('K' . $rowCount, '0');
				$object->getActiveSheet()->SetCellValue('L' . $rowCount, '0');
				$object->getActiveSheet()->SetCellValue('M' . $rowCount, '0');

				// looping ef =0 berdasarkan tgl
				$hari   = $jmlhari;
				foreach ($index_header2 as $idx_tgl) {
					$object->getActiveSheet()->SetCellValue($idx_tgl . $rowCount, 0);
					$hari--;
					if ($hari == 0) {
						break;
					}
				}

				$rowCount++;
			} else {
				foreach ($get_mrp  as $row) {

					$tot_mtr = $row->tot_mtr + ($row->tot_qty_adj);
					if ($row->target_periode > 0) {
						$eff = (($tot_mtr / $row->target_periode) * 100) / $row->tot_mo;
					}
					// total gulung setelah dikurang tot gl adj
					$tot_gl = $row->tot_gl - $row->tot_gl_adj;

					$object->getActiveSheet()->SetCellValue('A' . $rowCount, ($num++));
					$object->getActiveSheet()->SetCellValue('B' . $rowCount, $row->nama_mesin);
					$object->getActiveSheet()->SetCellValue('C' . $rowCount, $row->nama_produk);
					$object->getActiveSheet()->SetCellValue('G' . $rowCount, $tot_mtr);
					$object->getActiveSheet()->SetCellValue('H' . $rowCount, $row->tot_kg);
					$object->getActiveSheet()->SetCellValue('I' . $rowCount, $tot_gl);
					$object->getActiveSheet()->SetCellValue('J' . $rowCount, round($eff, 2));
					$object->getActiveSheet()->SetCellValue('K' . $rowCount, $row->grade_A);
					$object->getActiveSheet()->SetCellValue('L' . $rowCount, $row->grade_B);
					$object->getActiveSheet()->SetCellValue('M' . $rowCount, $row->grade_C);

					$tgldari_loop2   = $tgldari;
					$hari            = $jmlhari;
					$ef_hari         = 0;
					foreach ($index_header2 as $idx_tgl) {

						// get eff perhari
						$ef_result = $this->m_qualityControl->get_ef_hari_by_produk($row->mc_id, $id_dept, $tgldari_loop2, $row->kode_produk);
						if ($ef_result['target_efisiensi'] > 0) {
							$ef_hari = ($ef_result['tot_mtr'] + ($ef_result['tot_qty_adj'])) / ($ef_result['target_efisiensi'] * 24) * 100;
						}
						/*
						if($ef_result['ef']>100){
							$ef = 100;
						}else{
							$ef = $ef_result['ef'];
						}
						*/
						$object->getActiveSheet()->SetCellValue($idx_tgl . $rowCount, round($ef_hari, 2));

						$tgldari_loop2 = date('Y-m-d', strtotime('+1 days', strtotime($tgldari_loop2)));

						$hari--;
						if ($hari == 0) {
							break;
						}
						$ef_hari         = 0;
					}
					$rowCount++;
				}
			}
		}

		// set border
		$rowCount2  = 7;
		while ($rowCount2 <= $rowCount - 1) {

			foreach ($index_header as $val) {
				$object->getActiveSheet()->getStyle($val . '' . $rowCount2)->applyFromArray($styleArray);
			}
			$hari            = $jmlhari;
			foreach ($index_header2 as $val2) {
				$object->getActiveSheet()->getStyle($val2 . '' . $rowCount2)->applyFromArray($styleArray);
				if ($hari == 0) {
					break;
				}
				$hari--;
			}

			$rowCount2++;
		}

		$object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');

		$xlsData = ob_get_contents();
		ob_end_clean();

		$response =  array(
				'op'        => 'ok',
				'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
				'filename'  => 'Laporan QC ' . $dept['nama'] . ' .xlsx'
		);

		die(json_encode($response));
	}
}
