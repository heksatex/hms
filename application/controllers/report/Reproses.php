<?php defined('BASEPATH') or exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Reproses extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->is_loggedin(); //cek apakah user sudah login
        $this->load->model('m_reportReproses');
        $this->load->model('m_reproses');
    }

    public function index()
    {
        $data['id_dept'] = 'RREPRO';
        $data['list_jenis'] = $this->m_reproses->get_list_type();
        $this->load->view('report/v_reproses', $data);
    }

    function loadData()
    {

        $tgldari        = $this->input->post('tgldari');
        $tglsampai      = $this->input->post('tglsampai');
        
        $where_date     = '';
        $loop           = 1;

        // cari selisih periode tangal
        $diff = strtotime($tglsampai) - strtotime($tgldari);
        $hasil = floor($diff / (60 * 60 * 24));

        // cek tgl dari dan tgl sampai
        if (strtotime($tglsampai) < strtotime($tgldari)) {
            $callback = array('status' => 'failed', 'message' => 'Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !', 'icon' => 'fa fa-warning', 'type' => 'danger');

        } else {

            $tgldari = date('Y-m-d H:i:s', strtotime($tgldari));
            $tglsampai = date('Y-m-d H:i:s', strtotime($tglsampai));

            $dataRecord = [];
         
            $items = $this->m_reportReproses->get_list_reproses_by_kode($tgldari,$tglsampai);
            foreach ($items as $val) {
                $dataRecord[] = array(
                    'kode'          => $val->kode_reproses,
                    'nama_jenis'    => $val->nama_jenis,
                    'tgl'           => $val->tanggal,
                    'sub_parent'    => $val->nama_sub_parent,
                    'kode_produk'   => $val->kode_produk,
                    'nama_produk'   => $val->nama_produk,
                    'lot_baru'      => $val->lot_new,
                    'gl'            => 1,
                    'qty1'          => $val->qty,
                    'uom1'          => $val->uom,
                    'qty2'          => $val->qty2,
                    'uom2'          => $val->uom2,
                    'lot_asal'      => $val->lot,
                    'lokasi_asal'   => $val->lokasi_asal,
                    'note'          => $val->note,
                    'nama_user'     => $val->nama_user,
                );

                $allcount = $this->m_reportReproses->get_count_record_reproses($tgldari,$tglsampai);
                $total_record = 'Total Data : ' . number_format($allcount);

                $callback = array('record' => $dataRecord, 'total_record' => $total_record);
            }



        }

        echo json_encode($callback);

    }

    function export_excel()
    {

        $this->load->library('excel');
		ob_start();

        $tgldari   = $this->input->post('tgldari');
		$tglsampai = $this->input->post('tglsampai');

        $tgldari_2   = date('Y-m-d H:i:s', strtotime($tgldari));
        $tglsampai_2 = date('Y-m-d H:i:s', strtotime($tglsampai));

        $object = new PHPExcel();
    	$object->setActiveSheetIndex(0);

        // SET JUDUL
 		$object->getActiveSheet()->SetCellValue('A1', 'Laporan Reproses');
 		$object->getActiveSheet()->getStyle('A1')->getAlignment()->setIndent(1);
		$object->getActiveSheet()->mergeCells('A1:L1');

        // set periode
 		$object->getActiveSheet()->SetCellValue('A3', 'Periode');
        $object->getActiveSheet()->mergeCells('A3:B3');
        $object->getActiveSheet()->SetCellValue('C3', ': '.tgl_indo(date('d-m-Y',strtotime($tgldari))).' - '.tgl_indo(date('d-m-Y',strtotime($tglsampai) )));
        $object->getActiveSheet()->mergeCells('C3:F3');

        $total_record = $this->m_reportReproses->get_count_record_reproses($tgldari_2,$tglsampai_2);

         // set periode
 		$object->getActiveSheet()->SetCellValue('A4', 'Total Data');
        $object->getActiveSheet()->mergeCells('A4:B4');
        $object->getActiveSheet()->SetCellValue('C4', ': '.$total_record);
        $object->getActiveSheet()->mergeCells('C4:F4');

        //bold huruf
		$object->getActiveSheet()->getStyle("A1:U6")->getFont()->setBold(true);

		// Border 
		$styleArray = array(
			  'borders' => array(
			    'allborders' => array(
			      'style' => PHPExcel_Style_Border::BORDER_THIN
			    )
			  )
		);	

        // header table
    	$table_head_columns  = array('No', 'Kode Reproses', 'Jenis', 'Tgl', 'Sub Parent', 'Kode Produk', 'Nama Produk', 'Lot Baru', 'Gl', 'Qty1', 'Uom1','Qty2', 'Uom2', 'Lot Asal', 'Lokasi Asal','Note','Nama User');

        $column = 0;
		foreach ($table_head_columns as $judul) {
			# code...
			$object->getActiveSheet()->setCellValueByColumnAndRow($column, 6, $judul);  
			$column++;
		}

        // set border
		// $index_header = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S');
		// $loop = 0;
		// foreach ($index_header as $val) {
		// 	$object->getActiveSheet()->getStyle($val.'6')->applyFromArray($styleArray);
	    // }

        $num        = 1;
		$rowCount   = 7;
        $total_gl   = 0;
        $total_qty  = 0;
        $total_qty2 = 0;
        $total_gl_all   = 0;
        $total_qty_all  = 0;
        $total_qty2_all = 0;
        $tmp_id_jenis = '';
		$list  	  = $this->m_reportReproses->get_list_reproses_by_kode($tgldari_2,$tglsampai_2);
		foreach ($list as $val) {

			# code...
			$object->getActiveSheet()->SetCellValue('A'.$rowCount, ($num++));
			$object->getActiveSheet()->SetCellValue('B'.$rowCount, $val->kode_reproses);
			$object->getActiveSheet()->SetCellValue('C'.$rowCount, $val->nama_jenis);
			$object->getActiveSheet()->SetCellValue('D'.$rowCount, $val->tanggal);
			$object->getActiveSheet()->SetCellValue('E'.$rowCount, $val->nama_sub_parent);
			$object->getActiveSheet()->SetCellValue('F'.$rowCount, $val->kode_produk);
			$object->getActiveSheet()->SetCellValue('G'.$rowCount, $val->nama_produk);
			$object->getActiveSheet()->SetCellValue('H'.$rowCount, $val->lot_new);
			$object->getActiveSheet()->SetCellValue('I'.$rowCount, 1);
			$object->getActiveSheet()->SetCellValue('J'.$rowCount, $val->qty);
			$object->getActiveSheet()->SetCellValue('K'.$rowCount, $val->uom);
			$object->getActiveSheet()->SetCellValue('L'.$rowCount, $val->qty2);
			$object->getActiveSheet()->SetCellValue('M'.$rowCount, $val->uom2);
			$object->getActiveSheet()->SetCellValue('N'.$rowCount, $val->lot);
			$object->getActiveSheet()->SetCellValue('O'.$rowCount, $val->lokasi_asal);
			$object->getActiveSheet()->SetCellValue('P'.$rowCount, $val->note);
			$object->getActiveSheet()->SetCellValue('Q'.$rowCount, $val->nama_user);

            $total_gl   = $total_gl + 1;
            $total_qty  = $total_qty + $val->qty;
            $total_qty2 = $total_qty2 + $val->qty2;

		    $rowCount++;

            $total_gl_all   = $total_gl_all + 1;
            $total_qty_all  = $total_qty_all + $val->qty;
            $total_qty2_all =  $total_qty2_all + $val->qty2;

            if($tmp_id_jenis != '' AND $tmp_id_jenis != $val->id_jenis){
                $object->getActiveSheet()->mergeCells('A'.$rowCount.':G'.$rowCount);
                $object->getActiveSheet()->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $object->getActiveSheet()->SetCellValue('A'.$rowCount, "Total Sub Parent : ".$val->nama_sub_parent);
                $object->getActiveSheet()->SetCellValue('J'.$rowCount, ($total_qty));
                $object->getActiveSheet()->SetCellValue('L'.$rowCount, ($total_qty2));
                $object->getActiveSheet()->SetCellValue('I'.$rowCount, ($total_gl));
                $object->getActiveSheet()->getStyle("A".$rowCount.":O".$rowCount)->getFont()->setBold(true);
		        $rowCount++;
                $total_gl   = 0;
                $total_qty  = 0;
                $total_qty2 = 0;
            }
            $tmp_id_jenis = $val->id_jenis;
            $nama_sub_parent = $val->nama_sub_parent;

           
		}

        $object->getActiveSheet()->mergeCells('A'.$rowCount.':G'.$rowCount);
        $object->getActiveSheet()->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $object->getActiveSheet()->SetCellValue('A'.$rowCount, "Total Sub Parent : ".$nama_sub_parent);
        $object->getActiveSheet()->SetCellValue('I'.$rowCount, ($total_gl));
        $object->getActiveSheet()->SetCellValue('J'.$rowCount, ($total_qty));
        $object->getActiveSheet()->SetCellValue('L'.$rowCount, ($total_qty2));
        $object->getActiveSheet()->getStyle("A".$rowCount.":O".$rowCount)->getFont()->setBold(true);

        $rowCount++;
        // GRAND TOTAL
        $object->getActiveSheet()->mergeCells('A'.$rowCount.':G'.$rowCount);
        $object->getActiveSheet()->getStyle('A'.$rowCount)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
        $object->getActiveSheet()->SetCellValue('A'.$rowCount, "Grand Total");
        $object->getActiveSheet()->SetCellValue('I'.$rowCount, ($total_gl_all));
        $object->getActiveSheet()->SetCellValue('J'.$rowCount, ($total_qty_all));
        $object->getActiveSheet()->SetCellValue('L'.$rowCount, ($total_qty2_all));
        $object->getActiveSheet()->getStyle("A".$rowCount.":O".$rowCount)->getFont()->setBold(true);

			
		$object = PHPExcel_IOFactory::createWriter($object, 'Excel2007');  
		$object->save('php://output');
		$xlsData = ob_get_contents();
		ob_end_clean();

		$response =  array(
				'op'        => 'ok',
				'file'      => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData),
				'filename'  => "Reproses.xlsx"
		);
		
		die(json_encode($response));

    }

}