<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');
/**
 * 
 */
class Cacat extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('m_cacat');
        $this->load->library('Pdf');//load library pdf
		$this->load->model('_module');
	}

	public function index()
	{	
        $data['id_dept']='CCT';
		$this->load->view('report/v_cacat', $data);
	}

	public function get_departement_select2()
	{
		$nama  = addslashes($this->input->post('nama'));
   		$callback = $this->m_cacat->get_list_departement_select2($nama);
        echo json_encode($callback);
	}

	public function get_mrp_select2()
	{
		$mo       = addslashes($this->input->post('mo'));
		$dept_id  = addslashes($this->input->post('dept_id'));
   		$callback = $this->m_cacat->get_list_mrp_select2($dept_id,$mo);
        echo json_encode($callback);
	}

	public function get_lot_select2()
	{
		$mo     = addslashes($this->input->post('mo'));
		$lot    = addslashes($this->input->post('lot'));
		$callback = $this->m_cacat->get_list_lot_select2($mo,$lot);
		echo json_encode($callback);
	}

	public function report_cacat()
	{

		$dept_id = $this->input->get('departemen');
		$mo      = $this->input->get('mo');
		$lot     = $this->input->get('lot');

		$dept    = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
		$head    = $this->m_cacat->get_mrp_production_by_kode($mo)->row_array();

		if($dept_id == 'WRD'){


			$nama_dept = strtoupper($dept['nama']);
			$pdf = new PDF_Code128('P','mm','A4');
	        //$pdf = new PDF_Code128('P','mm',array(210,148.5));

	  		$pdf->SetMargins(0,0,0);
	        $pdf->SetAutoPageBreak(False);
	        $pdf->AddPage();
	        $pdf->setTitle('Laporan Cacat : '.$nama_dept);

	        $pdf->SetFont('Arial','B',9,'C');
	        $pdf->Cell(0,10,'LAPORAN CACAT BEAM DEPARTEMEN '.$nama_dept,0,0,'C');

	        $pdf->SetFont('Arial','',7,'C');

			$pdf->setXY(160,3);
	        $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
	        $pdf->Multicell(50,4, 'Tgl.Cetak : '.$tgl_now, 0,'C');

	        $pdf->SetFont('Arial','B',9,'C');

	        // caption kiri
	        $pdf->setXY(5,10);
	        $pdf->Multicell(15,4,'MO ',0,'L');

	        $pdf->setXY(5,13);
	        $pdf->Multicell(15,4,'Origin ',0,'L');

	        $pdf->setXY(5,16);
	        $pdf->Multicell(15,4,'Product ',0,'L');

	        $pdf->setXY(19, 10);
            $pdf->Multicell(5, 4, ':', 0, 'L');
            $pdf->setXY(19, 13);
            $pdf->Multicell(5, 4, ':', 0, 'L');
            $pdf->setXY(19, 16);
            $pdf->Multicell(5, 4, ':', 0, 'L');
	       	
	        // isi kiri
	        $pdf->SetFont('Arial','',8,'C');

	        $pdf->setXY(20,10);
	        $pdf->Multicell(40,4,$mo,0,'L');

	        $pdf->setXY(20,13);
	        $pdf->Multicell(40,4,$head['origin'],0,'L');

	        $pdf->setXY(20,16);
	        $pdf->Multicell(70,4,$head['nama_produk'],0,'L');


	        //caption tengah
	        $pdf->SetFont('Arial','B',9,'C');

	        $pdf->setXY(90,10);
	        $pdf->Multicell(35,4,'No.Mesin ',0,'L');

	        $pdf->setXY(108, 10);
            $pdf->Multicell(5, 4, ':', 0, 'L');

	        // isi tengah
	        $pdf->SetFont('Arial','',8,'C');
	        $pdf->setXY(110,10);
	        $pdf->Multicell(20,4,$head['nama_mesin'],0,'L');


	        // caption kanan
	        $pdf->SetFont('Arial','B',9,'C');
	        $pdf->setXY(130,10);
	        $pdf->Multicell(17,4,'Reff Note ',0,'L');

	        $pdf->setXY(148, 10);
            $pdf->Multicell(5, 4, ':', 0, 'L');

	        // isi kanan
	        $pdf->SetFont('Arial','',8,'C');
	        $pdf->setXY(150,10);
	        $pdf->Multicell(55,3,$head['reff_note'],0,'L');

	       
	        // Header table
	        $pdf->SetFont('Arial','B',9,'C');

	        $pdf->setXY(5,21);
			$pdf->Cell(20, 5, 'Beam Ke -', 1, 0, '');
			$pdf->Cell(18, 5, '1', 1, 0, 'C');
			$pdf->Cell(18, 5, '2', 1, 0, 'C');
			$pdf->Cell(18, 5, '3', 1, 0, 'C');
			$pdf->Cell(18, 5, '4', 1, 0, 'C');
			$pdf->Cell(18, 5, '5', 1, 0, 'C');
			$pdf->Cell(18, 5, '6', 1, 0, 'C');
			$pdf->Cell(18, 5, '7', 1, 0, 'C');
			$pdf->Cell(18, 5, '8', 1, 0, 'C');
			$pdf->Cell(18, 5, '9', 1, 0, 'C');
			$pdf->Cell(18, 5, '10', 1, 0, 'C');

	        $pdf->setXY(5,26);
			$pdf->Multicell(20, 6, 'Lot', 1, 'L');

			$pdf->setXY(5,32);
			$pdf->Multicell(20, 4, 'Panjang', 1, 'L');

			$pdf->setXY(5,36);
			$pdf->Multicell(20, 9, 'Reff Note', 1, 'L');

			$pdf->setXY(5,45);
			$pdf->Multicell(20, 3, 'Tgl. Produksi', 1, 'L');

			$pdf->setXY(5,51);
			$pdf->Multicell(20, 56, '', 1, 'C');

			$pdf->setXY(5,70);
			$pdf->Multicell(20, 5, 'Winding - Jenis Cacat', 0, 'C');

			$pdf->setXY(5,107);
			$pdf->Multicell(20, 4, 'Total Cacat', 1, 'L');

			$pdf->setXY(5,111);
			$pdf->Multicell(20, 4, 'Grade', 1, 'L');

	        $pdf->SetFont('Arial','',8,'C');

			// LOT yang telah dipilih
			$lot_ex = explode(',', $lot);
			$x      = 25;
			$xx     = 25;
			$x2     = 25;
			$x3     = 25;
			$x4     = 25;

			// set ukuran y winding - jenis cacat
			$y4     = 51;
			$y4_2   = 51;
			$y4_3   = 51;
			$y4_4   = 51;
			$y4_5   = 51;
			$y4_6   = 51;
			$y4_7   = 51;
			$y4_8   = 51;
			$y4_9   = 51;
			$y4_10  = 51;

			$loop   = 1;
			$loop_1 = 1;
			$loop_2 = 1;
			$loop_3 = 1;
			$loop_4 = 1;
			$loop_5 = 1;
			$loop_6 = 1;
			$loop_7 = 1;
			$loop_8 = 1;
			$loop_9 = 1;
			$loop_10= 1;
			foreach ($lot_ex as $val ) {


				$items = $this->m_cacat->get_item_by_kode($mo,$val)->row_array();

				$pdf->setXY($x,26);
				$pdf->Multicell(18, 6, '', 1, 'L'); // lot
		        $pdf->setXY($x,26);
				$pdf->Multicell(18, 3, $items['lot'], 0, 'L'); // lot
				$x = $x+18;
				/*
				$length = strlen($items['reff_note']);
				if($length < 30){
					$height = 3;
				}else{
					$height = 3;
				}
				*/

			    $pdf->setXY($xx,32);
				$pdf->Multicell(18,4, number_format($items['qty'],2), 1, 'L'); //qty
				$xx = $xx + 18;


			    $pdf->setXY($x2,36);
				$pdf->Multicell(18,9, '', 1, 'L'); // reff note
			    $pdf->setXY($x2,36);
				$pdf->Multicell(18,3, $items['reff_note'], 0, 'L'); // reff note
				$x2 = $x2+18;

				$pdf->setXY($x3,45);
				$pdf->Multicell(18, 3, date('Y/m/d H:i:s', strtotime($items['create_date'])), 1, 'L'); // create date
				$x3 = $x3+18;

				$cacat = $this->m_cacat->get_mrp_production_cacat_by_kode($mo, $val);
				
				if($loop == 1){ // beam ke - 1 

					foreach ($cacat as $row) {
						
						// Beam Ke - 1
						$pdf->setXY($x4,$y4);
						$pdf->Multicell(18, 4, $row->point_cacat.' '.$row->kode_cacat, 1, 'L');
						$y4 = $y4 + 4;
						$loop_1 = $loop_1 + 1;
					}

					// total Cacat
					$pdf->setXY(25,107);
					$pdf->Multicell(18, 4, $loop_1-1, 1, 'C');

					// Grade
					$pdf->setXY(25,111);
					$pdf->Multicell(18, 4, $items['nama_grade'], 1, 'C');

				}else if($loop == 2){// beam ke - 2

					foreach ($cacat as $row) {
						$pdf->setXY(43,$y4_2);
						$pdf->Multicell(18, 4, $row->point_cacat.' '.$row->kode_cacat, 1, 'L');
						$y4_2 = $y4_2 + 4;
						$loop_2 = $loop_2 + 1;
					}

					// total Cacat
					$pdf->setXY(43,107);
					$pdf->Multicell(18, 4, $loop_2-1, 1, 'C');

					// Grade
					$pdf->setXY(43,111);
					$pdf->Multicell(18, 4, $items['nama_grade'], 1, 'C');

				}else if($loop == 3){ // beam ke - 3

					foreach ($cacat as $row ) {
						$pdf->setXY(61,$y4_3);
						$pdf->Multicell(18, 4, $row->point_cacat.' '.$row->kode_cacat, 1, 'L');
						$y4_3 = $y4_3 + 4;
						$loop_3 = $loop_3 + 1;
					}

					// total Cacat
					$pdf->setXY(61, 107);
					$pdf->Multicell(18, 4, $loop_3-1, 1, 'C');

					// Grade
					$pdf->setXY(61, 111);
					$pdf->Multicell(18, 4, $items['nama_grade'], 1, 'C');

				}else if($loop == 4){ // beam ke - 4

					foreach ($cacat as $row ) {
						$pdf->setXY(79,$y4_4);
						$pdf->Multicell(18, 4, $row->point_cacat.' '.$row->kode_cacat, 1, 'L');
						$y4_4 = $y4_4 + 4;
						$loop_4 = $loop_4 + 1;
					}

					// total Cacat
					$pdf->setXY(79, 107);
					$pdf->Multicell(18, 4, $loop_4-1, 1, 'C');

					// Grade
					$pdf->setXY(79, 111);
					$pdf->Multicell(18, 4, $items['nama_grade'], 1, 'C');

				}else if($loop == 5){ // beam ke - 5

					foreach ($cacat as $row ) {
						$pdf->setXY(97,$y4_5);
						$pdf->Multicell(18, 4, $row->point_cacat.' '.$row->kode_cacat, 1, 'L');
						$y4_5 = $y4_5 + 4;
						$loop_5 = $loop_5 + 1;
					}

					// total Cacat
					$pdf->setXY(97, 107);
					$pdf->Multicell(18, 4, $loop_5-1, 1, 'C');

					// Grade
					$pdf->setXY(97, 111);
					$pdf->Multicell(18, 4, $items['nama_grade'], 1, 'C');

				}else if($loop == 6){ // beam ke - 6

					foreach ($cacat as $row ) {
						$pdf->setXY(115,$y4_6);
						$pdf->Multicell(18, 4, $row->point_cacat.' '.$row->kode_cacat, 1, 'L');
						$y4_6 = $y4_6 + 4;
						$loop_6 = $loop_6 + 1;
					}

					// total Cacat
					$pdf->setXY(115, 107);
					$pdf->Multicell(18, 4, $loop_6-1, 1, 'C');

					// Grade
					$pdf->setXY(115, 111);
					$pdf->Multicell(18, 4, $items['nama_grade'], 1, 'C');

				}else if($loop == 7){ // beam ke - 7

					foreach ($cacat as $row ) {
						$pdf->setXY(133,$y4_7);
						$pdf->Multicell(18, 4, $row->point_cacat.' '.$row->kode_cacat, 1, 'L');
						$y4_7 = $y4_7 + 4;
						$loop_7 = $loop_7 + 1;
					}

					// total Cacat
					$pdf->setXY(133, 107);
					$pdf->Multicell(18, 4, $loop_7-1, 1, 'C');

					// Grade
					$pdf->setXY(133, 111);
					$pdf->Multicell(18, 4, $items['nama_grade'], 1, 'C');

				}else if($loop == 8){ // beam ke - 8

					foreach ($cacat as $row ) {
						$pdf->setXY(151,$y4_8);
						$pdf->Multicell(18, 4, $row->point_cacat.' '.$row->kode_cacat, 1, 'L');
						$y4_8 = $y4_8 + 4;
						$loop_8 = $loop_8 + 1;
					}

					// total Cacat
					$pdf->setXY(151, 107);
					$pdf->Multicell(18, 4, $loop_8-1, 1, 'C');

					// Grade
					$pdf->setXY(151, 111);
					$pdf->Multicell(18, 4, $items['nama_grade'], 1, 'C');

				}else if($loop == 9){ // beam ke - 9

					foreach ($cacat as $row ) {
						$pdf->setXY(169,$y4_9);
						$pdf->Multicell(18, 4, $row->point_cacat.' '.$row->kode_cacat, 1, 'L');
						$y4_9 = $y4_9 + 4;
						$loop_9 = $loop_9 + 1;
					}

					// total Cacat
					$pdf->setXY(169, 107);
					$pdf->Multicell(18, 4, $loop_9-1, 1, 'C');

					// Grade
					$pdf->setXY(169, 111);
					$pdf->Multicell(18, 4, $items['nama_grade'], 1, 'C');

				}else if($loop == 10){ // beam ke - 10

					foreach ($cacat as $row ) {
						$pdf->setXY(187,$y4_10);
						$pdf->Multicell(18, 4, $row->point_cacat.' '.$row->kode_cacat, 1, 'L');
						$y4_10 = $y4_10 + 4;
						$loop_10 = $loop_10 + 1;
					}

					// total Cacat
					$pdf->setXY(187, 107);
					$pdf->Multicell(18, 4, $loop_10-1, 1, 'C');

					// Grade
					$pdf->setXY(187, 111);
					$pdf->Multicell(18, 4, $items['nama_grade'], 1, 'C');

				}


				$loop++;
				if($loop == 11){
					break;
				}




			}// end loop isi


			if($loop <= 11){
				for($i = $loop; $i<=10; $i++){
					$pdf->setXY($x,26);
					$pdf->Multicell(18, 6, '', 1, 'L'); // lot 
					$x = $x+18;	

					$pdf->setXY($xx,32);
					$pdf->Multicell(18, 4, '', 1, 'L'); // qty
					$xx = $xx+18;
					
			  	    $pdf->setXY($x2,36);
					$pdf->Multicell(18, 9, '', 1, 'L'); // reff note
					$x2 = $x2+18;

					$pdf->setXY($x3,45);
					$pdf->Multicell(18, 6, '', 1, 'L'); // create date
					$x3 = $x3+18;


				}
				
				if($loop_1 <= 14){ // beam ke - 1
					for($a = $loop_1; $a<=14; $a++){
						$pdf->setXY($x4,$y4);
						$pdf->Multicell(18, 4, '', 1, 'L');
						$y4 = $y4 + 4;
					}

					// total Cacat
					$pdf->setXY(25, 107);
					$pdf->Multicell(18, 4, '', 1, 'C');

					// Grade
					$pdf->setXY(25, 111);
					$pdf->Multicell(18, 4, '', 1, 'C');

				}

				if($loop_2 <= 14){ // beam ke - 2
					for($a = $loop_2; $a<=14; $a++){
						$pdf->setXY(43,$y4_2);
						$pdf->Multicell(18, 4, '', 1, 'L');
						$y4_2 = $y4_2 + 4;
					}

					// total Cacat
					$pdf->setXY(43, 107);
					$pdf->Multicell(18, 4, '', 1, 'C');

					// Grade
					$pdf->setXY(43, 111);
					$pdf->Multicell(18, 4, '', 1, 'C');
				}
				

				if($loop_3 <= 14){ // beam ke - 3
					for($a = $loop_3; $a<=14; $a++){
						$pdf->setXY(61,$y4_3);
						$pdf->Multicell(18, 4, '', 1, 'L');
						$y4_3 = $y4_3 + 4;
					}

					// total Cacat
					$pdf->setXY(61, 107);
					$pdf->Multicell(18, 4, '', 1, 'C');

					// Grade
					$pdf->setXY(61, 111);
					$pdf->Multicell(18, 4, '', 1, 'C');
				}

				if($loop_4 <= 14){ // beam ke - 4
					for($a = $loop_4; $a<=14; $a++){
						$pdf->setXY(79,$y4_4);
						$pdf->Multicell(18, 4, '', 1, 'L');
						$y4_4 = $y4_4 + 4;
					}

					// total Cacat
					$pdf->setXY(79, 107);
					$pdf->Multicell(18, 4, '', 1, 'C');

					// Grade
					$pdf->setXY(79, 111);
					$pdf->Multicell(18, 4, '', 1, 'C');

				}

				if($loop_5 <= 14){ // beam ke - 5
					for($a = $loop_5; $a<=14; $a++){
						$pdf->setXY(97,$y4_5);
						$pdf->Multicell(18, 4, '', 1, 'L');
						$y4_5 = $y4_5 + 4;
					}

					// total Cacat
					$pdf->setXY(97, 107);
					$pdf->Multicell(18, 4, '', 1, 'C');

					// Grade
					$pdf->setXY(97, 111);
					$pdf->Multicell(18, 4, '', 1, 'C');
				}

				if($loop_6 <= 14){ // beam ke - 6
					for($a = $loop_6; $a<=14; $a++){
						$pdf->setXY(115,$y4_6);
						$pdf->Multicell(18, 4, '', 1, 'L');
						$y4_6 = $y4_6 + 4;
					}

					// total Cacat
					$pdf->setXY(115, 107);
					$pdf->Multicell(18, 4, '', 1, 'C');

					// Grade
					$pdf->setXY(115, 111);
					$pdf->Multicell(18, 4, '', 1, 'C');
				}

				if($loop_7 <= 14){ // beam ke - 7
					for($a = $loop_7; $a<=14; $a++){
						$pdf->setXY(133,$y4_7);
						$pdf->Multicell(18, 4, '', 1, 'L');
						$y4_7 = $y4_7 + 4;
					}

					// total Cacat
					$pdf->setXY(133, 107);
					$pdf->Multicell(18, 4, '', 1, 'C');

					// Grade
					$pdf->setXY(133, 111);
					$pdf->Multicell(18, 4, '', 1, 'C');
				}

				if($loop_8 <= 14){ // beam ke - 8
					for($a = $loop_8; $a<=14; $a++){
						$pdf->setXY(151,$y4_8);
						$pdf->Multicell(18, 4, '', 1, 'L');
						$y4_8 = $y4_8 + 4;
					}

					// total Cacat
					$pdf->setXY(151, 107);
					$pdf->Multicell(18, 4, '', 1, 'C');

					// Grade
					$pdf->setXY(151, 111);
					$pdf->Multicell(18, 4, '', 1, 'C');
				}

				if($loop_9 <= 14){ // beam ke - 9
					for($a = $loop_9; $a<=14; $a++){
						$pdf->setXY(169,$y4_9);
						$pdf->Multicell(18, 4, '', 1, 'L');
						$y4_9 = $y4_9 + 4;
					}

					// total Cacat
					$pdf->setXY(169, 107);
					$pdf->Multicell(18, 4, '', 1, 'C');

					// Grade
					$pdf->setXY(169, 111);
					$pdf->Multicell(18, 4, '', 1, 'C');
				}

				if($loop_10 <= 14){ // beam ke - 10
					for($a = $loop_10; $a<=14; $a++){
						$pdf->setXY(187,$y4_10);
						$pdf->Multicell(18, 4, '', 1, 'L');
						$y4_10 = $y4_10 + 4;
					}

					// total Cacat
					$pdf->setXY(187, 107);
					$pdf->Multicell(18, 4, '', 1, 'C');

					// Grade
					$pdf->setXY(187, 111);
					$pdf->Multicell(18, 4, '', 1, 'C');
				}
			}


	        $pdf->SetFont('Arial','',9,'C');

			// keterangan Cacat
			$pdf->setXY(5,117);
			$pdf->Multicell(50, 4, 'Keterangan Cacat '.$dept['nama'].' :', 0, 'L');
			$y  = 120;
			$x  = 5;
			$loop = 1;

			$list  = $this->m_cacat->get_list_cacat_by_dept($dept_id);
			foreach ($list as $row) {

				if($loop == 3){
					$y  = 120;
					$x  = $x+43;
					$loop = 1;
				}

				$pdf->setXY($x,$y);
				$pdf->Multicell(50, 4, $row->kode_cacat.' : '.$row->nama_cacat, 0, 'L');
				$y = $y+3;

				$loop++;
			}

			// ttd disetujui
			$pdf->setXY(5,127);
			$pdf->Multicell(50, 4, 'Disetujui Untuk Naik Mesin : ', 0, 'L');


			$pdf->setXY(5, 135);
			$pdf->Multicell(23, 4, '( ', 0, 'L');
			$pdf->setXY(5, 135);
			$pdf->Multicell(23, 4, ' )', 0, 'R');
			$pdf->setXY(5, 139);
			$pdf->Multicell(23, 4, 'OP W.Dasar', 0, 'C');


			$pdf->setXY(35, 135);
			$pdf->Multicell(23, 4, '( ', 0, 'L');
			$pdf->setXY(35, 135);
			$pdf->Multicell(23, 4, ' )', 0, 'R');
			$pdf->setXY(35, 139);
			$pdf->Multicell(23, 4, 'K.Shift W.Dasar', 0, 'C');


			$pdf->setXY(65, 135);
			$pdf->Multicell(23, 4, '( ', 0, 'L');
			$pdf->setXY(65, 135);
			$pdf->Multicell(23, 4, ' )', 0, 'R');
			$pdf->setXY(65, 139);
			$pdf->Multicell(23, 4, 'K.Shift W.Tricot', 0, 'C');


			$pdf->setXY(95, 135);
			$pdf->Multicell(23, 4, '( ', 0, 'L');
			$pdf->setXY(95, 135);
			$pdf->Multicell(23, 4, ' )', 0, 'R');
			$pdf->setXY(95, 139);
			$pdf->Multicell(23, 4, 'QC Tricot', 0, 'C');


			// keterangan QC

			$pdf->setXY(134,127);
			$pdf->Multicell(70, 4, 'Keterangan QC ',0, 'L');
			

			$pdf->setXY(134,127);
			$pdf->Multicell(70, 15, '',1, 'L');

	        $pdf->Output();
	        
	    }else{

	    }

	}

}