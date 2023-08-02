<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class DoneMO extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("m_mo");//load query" di model m_mo
        $this->load->model("_module");
        $this->load->model("m_doneMO");
	}


    public function index()
	{
		$id_dept        = 'DMO';
        $data['id_dept']= $id_dept;
		$this->load->view('report/v_done_mo', $data);
	}


    function loadData()
	{
		$tgldari     = date('Y-m-d H:i:s', strtotime($this->input->post('tgldari')));
		$tglsampai   = date('Y-m-d H:i:s', strtotime($this->input->post('tglsampai')));
		$departemen  = addslashes($this->input->post('departemen'));
        $dataRecord  = [];

        $total_qty1  = 0;
        $total_qty2  = 0;
        $total       = 0;
        $get   = $this->m_doneMO->get_data_mrp_done_by_kode($departemen,$tgldari,$tglsampai);
		foreach($get as $row){
                // $rm_done            = $this->m_mo->get_sum_qty_rm_done($row->kode,'done')->row();
                // $fg_prod            = $this->m_mo->get_sum_qty_fg_produce($row->kode)->row();
                // $fg_waste           = $this->m_mo->get_sum_qty_fg_waste($row->kode)->row();
                $fg_adj_real           = $this->m_mo->get_sum_qty_fg_adj($row->kode)->row();

                $total_qty1         = ($row->prod_mtr + $row->waste_mtr ) - $row->adj_mtr ;
                $total_qty2         = ($row->prod_kg + $row->waste_kg ) - $row->adj_kg; 

				$dataRecord[] = array('kode' 		=> $row->kode,
									'tanggal'       => $row->tanggal,
									'departemen' 	=> $row->departemen,
									'cons_qty1'		=> number_format($row->con_mtr,2),
									'cons_qty2' 	=> number_format($row->con_kg,2),
									'prod_qty1'	    => number_format($row->prod_mtr,2),
									'prod_qty2'	    => number_format($row->prod_kg,2),
									'waste_qty1'	=> number_format($row->waste_mtr,2),
									'waste_qty2'	=> number_format($row->waste_kg,2),
									'adj_qty1'		=> number_format($row->adj_mtr,2),
									'adj_qty2'		=> number_format($row->adj_kg,2),
									'total_qty1'	=> number_format($total_qty1,2),
									'total_qty2'    => number_format($total_qty2,2),
									'adj_ril_qty1'  => number_format($fg_adj_real->mtr,2),
									'adj_ril_qty2'  => number_format($fg_adj_real->kg,2),
									'status'        => $row->status

				);
				$total++;
		}

        $callback = array('record' => $dataRecord, 'total_record' => 'Total Data : '.number_format($total));
        echo json_encode($callback);

    }

    function export_excel()
    {
        $this->load->library('excel');
		ob_start();
		$tgldari     = date('Y-m-d H:i:s', strtotime($this->input->post('tgldari')));
		$tglsampai   = date('Y-m-d H:i:s', strtotime($this->input->post('tglsampai')));
		$departemen  = addslashes($this->input->post('departemen'));
		// $dataRecord  = [];

		$dept    = $this->_module->get_nama_dept_by_kode($departemen)->row_array();

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

        // SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan MO Done');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:H1');

		// set Periode tgl
		$object->getActiveSheet()->SetCellValue('A2', 'Tanggal');
		$object->getActiveSheet()->mergeCells('A2:B2');
 		$object->getActiveSheet()->SetCellValue('C2', ': '.tgl_indo(date('d-m-Y H:i:s',strtotime($tgldari))).' - '.tgl_indo(date('d-m-Y H:i:s',strtotime($tglsampai)) ));
		$object->getActiveSheet()->mergeCells('C2:H2');

		// set Departemen
 		$object->getActiveSheet()->SetCellValue('A3', 'Departemen');
		$object->getActiveSheet()->mergeCells('A3:B3');
 		$object->getActiveSheet()->SetCellValue('C3', ': '.$dept['nama']);
		$object->getActiveSheet()->mergeCells('C3:D3');

        //bold huruf
		$object->getActiveSheet()->getStyle("A1:Q8")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			    'borders' => array(
                    'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
			    )
		);	

        // judul tabel
        $object->getActiveSheet()->setCellValueByColumnAndRow(0, 6, 'No');  //A
        $object->getActiveSheet()->mergeCells('A6:A8');  
        $object->getActiveSheet()->getStyle('A6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('A6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $object->getActiveSheet()->setCellValueByColumnAndRow(1, 6, 'MO'); // B 
        $object->getActiveSheet()->mergeCells('B6:B8');  
        $object->getActiveSheet()->getStyle('B6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('B6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $object->getActiveSheet()->setCellValueByColumnAndRow(2, 6, 'Tgl.MO');   // C
        $object->getActiveSheet()->mergeCells('C6:C8');  
        $object->getActiveSheet()->getStyle('C6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('C6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $object->getActiveSheet()->setCellValueByColumnAndRow(3, 6, 'Departemen');   // D
        $object->getActiveSheet()->mergeCells('D6:D8');  
        $object->getActiveSheet()->getStyle('D6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('D6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        

        $object->getActiveSheet()->setCellValueByColumnAndRow(4, 6, 'Consume Bahan Baku');   // E
        $object->getActiveSheet()->mergeCells('E6:F7');  
        $object->getActiveSheet()->getStyle('E6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('E6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $object->getActiveSheet()->setCellValueByColumnAndRow(4, 8, 'Mtr');   // E
        $object->getActiveSheet()->setCellValueByColumnAndRow(5, 8, 'Kg');   // F

        $object->getActiveSheet()->setCellValueByColumnAndRow(6, 6, 'Barang Jadi');   // G
        $object->getActiveSheet()->mergeCells('G6:P6');  
        $object->getActiveSheet()->getStyle('G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $object->getActiveSheet()->setCellValueByColumnAndRow(6, 7, 'Produce');   // G
        $object->getActiveSheet()->mergeCells('G7:H7');  
        $object->getActiveSheet()->getStyle('G7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('G7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->setCellValueByColumnAndRow(6, 8, 'Mtr');   // G
        $object->getActiveSheet()->setCellValueByColumnAndRow(7, 8, 'Kg');   // H

        $object->getActiveSheet()->setCellValueByColumnAndRow(8, 7, 'Waste');   // I
        $object->getActiveSheet()->mergeCells('I7:J7');  
        $object->getActiveSheet()->getStyle('I7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('I7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->setCellValueByColumnAndRow(8, 8, 'Mtr');   // I
        $object->getActiveSheet()->setCellValueByColumnAndRow(9, 8, 'Kg');   // J

        $object->getActiveSheet()->setCellValueByColumnAndRow(10, 7, 'Adjustment');   // K
        $object->getActiveSheet()->mergeCells('K7:L7');  
        $object->getActiveSheet()->getStyle('K7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('K7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->setCellValueByColumnAndRow(10, 8, 'Mtr');   // K
        $object->getActiveSheet()->setCellValueByColumnAndRow(11, 8, 'Kg');   // L

        $object->getActiveSheet()->setCellValueByColumnAndRow(12, 7, 'Total');   // M
        $object->getActiveSheet()->mergeCells('M7:N7');  
        $object->getActiveSheet()->getStyle('M7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('M7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->setCellValueByColumnAndRow(12, 8, 'Mtr');   // M
        $object->getActiveSheet()->setCellValueByColumnAndRow(13, 8, 'Kg');   // N

        $object->getActiveSheet()->setCellValueByColumnAndRow(14, 7, 'Adjustment Terbaru');   // O
        $object->getActiveSheet()->mergeCells('O7:P7');  
        $object->getActiveSheet()->getStyle('O7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('O7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $object->getActiveSheet()->setCellValueByColumnAndRow(14, 8, 'Mtr');   // O
        $object->getActiveSheet()->setCellValueByColumnAndRow(15, 8, 'Kg');   // P

        $object->getActiveSheet()->setCellValueByColumnAndRow(16, 6, 'Status');   // Q
        $object->getActiveSheet()->mergeCells('Q6:Q8');  
        $object->getActiveSheet()->getStyle('Q6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $object->getActiveSheet()->getStyle('Q6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        // Body
        $total_qty1  = 0;
        $total_qty2  = 0;
        $num          = 1;
        $rowCount = 9;
        $get   = $this->m_doneMO->get_data_mrp_done_by_kode($departemen,$tgldari,$tglsampai);
		foreach($get as $row){

                
                $fg_adj_real        = $this->m_mo->get_sum_qty_fg_adj($row->kode)->row();

                $total_qty1         = ($row->prod_mtr + $row->waste_mtr ) - $row->adj_mtr;
                $total_qty2         = ($row->prod_kg + $row->waste_kg ) - $row->adj_kg; 

                $object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
				$object->getActiveSheet()->SetCellValue('B'.$rowCount, $row->kode);
				$object->getActiveSheet()->SetCellValue('C'.$rowCount, $row->tanggal);
				$object->getActiveSheet()->SetCellValue('D'.$rowCount, $row->departemen);
				$object->getActiveSheet()->SetCellValue('E'.$rowCount, $row->con_mtr);
				$object->getActiveSheet()->SetCellValue('F'.$rowCount, $row->con_kg);
                $object->getActiveSheet()->SetCellValue('G'.$rowCount, $row->prod_mtr);
				$object->getActiveSheet()->SetCellValue('H'.$rowCount, $row->prod_kg);
                $object->getActiveSheet()->SetCellValue('I'.$rowCount, $row->waste_mtr);
				$object->getActiveSheet()->SetCellValue('J'.$rowCount, $row->waste_kg);
                $object->getActiveSheet()->SetCellValue('K'.$rowCount, $row->adj_mtr);
				$object->getActiveSheet()->SetCellValue('L'.$rowCount, $row->adj_kg);
                $object->getActiveSheet()->SetCellValue('M'.$rowCount, $total_qty1);
				$object->getActiveSheet()->SetCellValue('N'.$rowCount, $total_qty2);
                $object->getActiveSheet()->SetCellValue('O'.$rowCount, $fg_adj_real->mtr);
				$object->getActiveSheet()->SetCellValue('P'.$rowCount, $fg_adj_real->kg);

				$object->getActiveSheet()->SetCellValue('Q'.$rowCount, $row->status);

                $rowCount++;
		}


        $object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();
		$name_file ='Done MO '.$dept['nama'].'.xlsx';
		$response =  array(
			'op'        => 'ok',
			'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
			'filename'  => $name_file
		);
		
		die(json_encode($response));
    }


}