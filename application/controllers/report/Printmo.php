<?php defined('BASEPATH') OR EXIT ('No Direct Script Acces Allowed');
/**
 * 
 */
class Printmo extends MY_Controller
{
	
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
        $this->load->model('m_printMO');
        $this->load->library('Pdf');//load library pdf
		$this->load->model('_module');
		
	}

	public function index()
	{	
        $data['id_dept']='PMO';
		$this->load->view('report/v_print_mo', $data);
	}

	public function report_harian()
	{

		$dept_id = $this->input->get('departemen');
		$mo      = $this->input->get('mo');

		$dept    = $this->_module->get_nama_dept_by_kode($dept_id)->row_array();
		$head    = $this->m_printMO->get_mrp_production_by_kode($mo)->row_array();


        if($dept_id == 'TWS'){ // if departemen TWS

            $nama_dept = strtoupper($dept['nama']);
            $pdf = new PDF_Code128('P','mm','A4');

            $pdf->SetMargins(0,0,0);
            $pdf->SetAutoPageBreak(False);
            $pdf->AddPage();

            $pdf->setTitle($nama_dept);
            $pdf->SetFont('Arial','B',10,'C');
            $pdf->Cell(0,23,'LAPORAN HARIAN '.$nama_dept,0,0,'C');

            $pdf->SetFont('Arial','',15,'C');
            $pdf->setXY(10,8);
            $pdf->Multicell(30,8,$head['nama_mesin'],1,'C');

            $pdf->setXY(150,8);
            $pdf->Multicell(50,8,$head['kode'],1,'C');

            $pdf->SetFont('Arial','',8,'C');
            $pdf->setXY(150,16);    
            $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
            $pdf->Multicell(50,4, 'Tgl.Cetak : '. $tgl_now, 0,'R');


            $pdf->SetFont('Arial','B',9,'C');

            // caption kiri
            $pdf->setXY(10,20);
            $pdf->Multicell(15,4,'Tgl.MO ',0,'L');

            $pdf->setXY(10,25);
            $pdf->Multicell(15,4,'Origin ',0,'L');

            $pdf->setXY(24, 20);
            $pdf->Multicell(5, 4, ':', 0, 'L');
            $pdf->setXY(24, 25);
            $pdf->Multicell(5, 4, ':', 0, 'L');
            
            // isi kiri
            $pdf->SetFont('Arial','',8,'C');

            $pdf->setXY(25,20);
            $pdf->Multicell(40,4,tgl_indo(date('d-m-Y H:i:s',strtotime($head['tanggal']))),0,'L');

            $pdf->setXY(25,25);
            $pdf->Multicell(40,4,$head['origin'],0,'L');


            $pdf->SetFont('Arial','B',9,'C');

            // caption tengah
            $pdf->setXY(65,20);
            $pdf->Multicell(15,4,'Product ',0,'L');
            $pdf->setXY(65,25);
            $pdf->Multicell(15,4,'Qty ',0,'L');

            $pdf->setXY(79, 20);
            $pdf->Multicell(5, 4, ':', 0, 'L');
            $pdf->setXY(79, 25);
            $pdf->Multicell(5, 4, ':', 0, 'L');
    
            // isi tengah
            $pdf->SetFont('Arial','',9,'C');

            $pdf->setXY(80,20);
            $pdf->Multicell(61,4,$head['nama_produk'],0,'L');

            $pdf->setXY(80,25);
            $pdf->Multicell(40,4,$head['qty'].' '.$head['uom'],0,'L');

            $pdf->SetFont('Arial','B',9,'C');

            // caption kanan
            $pdf->setXY(140,20);
            $pdf->Multicell(15,4,'TPM ',0,'L');
            $pdf->setXY(140,25);
            $pdf->Multicell(15,4,'Panjang ',0,'L');
 
            $pdf->setXY(155, 20);
            $pdf->Multicell(5, 4, ':', 0, 'L');
            $pdf->setXY(155, 25);
            $pdf->Multicell(5, 4, ':', 0, 'L');

            // isi kanan
            $pdf->SetFont('Arial','',9,'C');

            // explode reff note
            $ex2 = explode('|', $head['reff_note']);
            $a   = 0;
            $tpm = '';
            foreach ($ex2 as $exs2) {
                if($a == 1){ // tpm
                    $tpm = trim($exs2);
                }
                $a++;
            }

            $pdf->setXY(156,20);
            $pdf->Multicell(61,4,$tpm,0,'L');
  

            // body
            $pdf->SetFont('Arial','B',9,'C');

            $setx_no  = 10;
            $setx_tgl = 25;
            $setx_lot = 70;
            $setx_qty = 115;
            $setx_ket = 155;
         
            $capt_no = 'No';
            $capt_tgl= 'Tgl. Jam';
            $capt_lot= 'Lot';
            $capt_qty= 'Qty';
            $capt_ket= 'Keterangan';


            $sety_header = 30;

            for($i=0; $i<3; $i++){

                    // header table
                    $pdf->setXY(10, $sety_header);
                    $pdf->Multicell(60, 5, 'SHIFT OPERATOR', 1, 'L');
                    $pdf->setXY(70, $sety_header);
                    $pdf->Multicell(130, 5, '', 1, 'L');

                    $capt_no = 'No';
                    $capt_tgl= 'Tgl. Jam';
                    $capt_lot= 'Lot';
                    $capt_qty= 'Qty';
                    $capt_ket= 'Keterangan';

                    for($a=0; $a<=5; $a++){

                        if($a>0){
                            // set caption
                            $capt_no  = '';
                            $capt_tgl = '';
                            $capt_lot = '';
                            $capt_qty = '';
                            $capt_ket = '';
                        }
                        $pdf->setXY($setx_no,$sety_header+5);
                        $pdf->Multicell(15, 5, $capt_no, 1, 'C');
                        $pdf->setXY($setx_tgl,$sety_header+5);
                        $pdf->Multicell(45, 5, $capt_tgl, 1, 'C');
                        $pdf->setXY($setx_lot,$sety_header+5);
                        $pdf->Multicell(45, 5, $capt_lot, 1, 'C');
                        $pdf->setXY($setx_qty,$sety_header+5);
                        $pdf->Multicell(40, 5, $capt_qty, 1, 'C');
                        $pdf->setXY($setx_ket,$sety_header+5);
                        $pdf->Multicell(45, 5, $capt_ket, 1, 'C');
                        $sety_header = $sety_header+5;
                    }
                    $sety_header = $sety_header+10;

            }



            $pdf->Output();

        }else if($dept_id == 'WRD'){ // if departemen WRD

    	   	$nama_dept = strtoupper($dept['nama']);
    		$pdf = new PDF_Code128('l','mm','A4');

    		$pdf->SetMargins(0,0,0);
            $pdf->SetAutoPageBreak(False);
            $pdf->AddPage();

            $pdf->setTitle($nama_dept);
            $pdf->SetFont('Arial','B',10,'C');
            $pdf->Cell(0,23,'LAPORAN HARIAN '.$nama_dept,0,0,'C');


            $pdf->SetFont('Arial','',15,'C');
            $pdf->setXY(10,8);
            $pdf->Multicell(30,8,$head['nama_mesin'],1,'C');

            $pdf->setXY(227,8);
            $pdf->Multicell(60,8,$head['kode'],1,'C');

            $pdf->SetFont('Arial','',8,'C');
            $pdf->setXY(227,16);
            $pdf->Multicell(60,4, 'Nomor : FRM/011/WD/HPH/001', 1, 'C');

            $pdf->SetFont('Arial','',8,'C');
    		$pdf->setXY(227,21);	
            $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
            $pdf->Multicell(60,4, 'Tgl.Cetak : '. $tgl_now, 0,'R');

            $pdf->SetFont('Arial','B',7,'C');
            // Caption kiri
            $pdf->setXY(10,25);
            $pdf->Multicell(20, 4, 'Tgl. MO ', 0, 'L');

    		$pdf->setXY(10,29);
            $pdf->Multicell(20, 4, 'Product ', 0, 'L');

            $pdf->setXY(10,33);
            $pdf->Multicell(20, 4, 'Origin ', 0, 'L');

            $pdf->setXY(10,37);
            $pdf->Multicell(20, 4, 'Reff Note ', 0, 'L');

            $pdf->setXY(29, 25);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(29, 29);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(29, 33);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(29, 37);
            $pdf->Multicell(20, 4, ':', 0, 'L');


            $pdf->SetFont('Arial','',7,'C');
            // isi kiri
            $pdf->setXY(30,25);
            $pdf->Multicell(50, 4, tgl_indo(date('d-m-Y H:i:s',strtotime($head['tanggal']))), 0, 'L');

            $pdf->setXY(30,29);
            $pdf->Multicell(70, 4, $head['nama_produk'], 0, 'L');

            $pdf->setXY(30,33);
            $pdf->Multicell(50, 4, $head['origin'], 0, 'L');

            $pdf->setXY(30,37);
            $pdf->Multicell(70, 4, $head['reff_note'], 0, 'L');

            // EXPLODE REFF NOTE
            $ex    = explode('|', $head['reff_note']);
            $i=0;
            $mo_knitting    = '';
            $mc_knitting    = '';
            $corak          = '';
            $gb             = '';
            $jml_beam       = '';
            $lembar         = '';
            $pjg            = '';
            $jns_benang     = '';

            foreach($ex as $exs){

                if($i == 1){
                    $mo_knitting    = trim($exs);
                }
                if($i == 2){
                    $mc_knitting    = trim($exs);
                }
                
                if($i == 4){
                    $gb             = trim($exs);
                }
                if($i == 5){
                    $jml_beam       = trim($exs);
                }
                if($i == 6){
                    $lembar         = trim($exs);
                }
                if($i == 7){
                    $pjg            = trim($exs);
                }
                if($i==8){
                    $jns_benang    = trim($exs);
                }
                $i++;
            }


            $pdf->SetFont('Arial','B',7,'C');

            // caption tengah 
            $pdf->setXY(110, 25);
            $pdf->Multicell(30, 4, 'Mesin Knitting ', 0, 'L');

            $pdf->setXY(110, 29);
            $pdf->Multicell(30, 4, 'Jenis Benang  ', 0, 'L' );

            $pdf->setXY(110, 33);
            $pdf->Multicell(30, 4, 'Jumlah Lembar ', 0, 'L');

            $pdf->setXY(110, 37);
            $pdf->Multicell(30, 4, 'Jumlah Beam  ', 0, 'L');


            $pdf->setXY(130, 25);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(130, 29);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(130, 33);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(130, 37);
            $pdf->Multicell(20, 4, ':', 0, 'L');

            $pdf->SetFont('Arial','',7,'C');
            // isi caption tengah
            $pdf->setXY(131, 25);
            $pdf->Multicell(30, 4, $mc_knitting, 0, 'L');
            $pdf->setXY(131, 29);
            $pdf->Multicell(30, 4, $jns_benang, 0, 'L');
            $pdf->setXY(131, 33);
            $pdf->Multicell(30, 4, $lembar, 0, 'L');
            $pdf->setXY(131, 37);
            $pdf->Multicell(30, 4, $jml_beam, 0, 'L');

            $pdf->SetFont('Arial','B',7,'C');
            // caption kanan
            $pdf->setXY(200, 25);
            $pdf->Multicell(35, 4, 'Tanggal Produksi ', 0, 'L');

            $pdf->setXY(200, 29);
            $pdf->Multicell(35, 4, 'Kec.Mesin/Speed ', 0, 'L');

            $pdf->setXY(200, 33);
            $pdf->Multicell(35, 4, 'GB  ', 0, 'L');

            $pdf->setXY(200, 37);
            $pdf->Multicell(35, 4, 'Order(meter) ', 0, 'L');

            $pdf->setXY(200, 41);
            $pdf->Multicell(35, 4, 'Slowert/Tension ', 0, 'L');

            $pdf->setXY(223, 25);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(223, 29);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(223, 33);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(223, 37);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(223, 41);
            $pdf->Multicell(20, 4, ':', 0, 'L');

            $pdf->SetFont('Arial','',7,'C');
            // isi kanan
            $pdf->setXY(224, 33);
            $pdf->Multicell(20, 4, $gb, 0, 'L');

            // header table
            $pdf->setXY(10, 48);
            $pdf->Multicell(7, 10, 'No', 1, 'C');

            $pdf->setXY(17, 48);
            $pdf->Multicell(12, 10, ' ', 1, 'C');
            $pdf->setXY(17, 50);
            $pdf->Multicell(12, 3, 'Seri Beam', 0, 'C');


            $pdf->setXY(29, 48);
            $pdf->Multicell(17, 10, '', 1, 'C');
            $pdf->setXY(29, 50);
            $pdf->Multicell(17, 3, 'Nama Operator', 0, 'C');

            $pdf->setXY(46, 48);
            $pdf->Multicell(13, 10, 'Shift', 1, 'C');

            $pdf->setXY(59, 48);
            $pdf->Multicell(13, 10, '', 1, 'C');
            $pdf->setXY(59, 50);
            $pdf->Multicell(13, 3, 'Amplas "v" ', 0, 'C');

            $pdf->setXY(72, 48);
            $pdf->Multicell(13, 10, '', 1, 'C');
            $pdf->setXY(72, 50);
            $pdf->Multicell(13, 3, 'Bersih Kering "v" ', 0, 'C');

            $pdf->setXY(85, 48);
            $pdf->Multicell(12, 10, '', 1, 'C');
            $pdf->setXY(85, 50);
            $pdf->Multicell(12, 3, 'Slub Detector ', 0, 'C');

            $pdf->setXY(97,48);
            $pdf->Multicell(22, 5, 'Waktu Produksi ', 1, 'C');
            $pdf->setXY(97, 53);
            $pdf->Multicell(11, 5, 'Mulai ', 1, 'C');
            $pdf->setXY(108, 53);
            $pdf->Multicell(11, 5, 'Selesai ', 1, 'C');

    	    $pdf->setXY(119,48);
            $pdf->Multicell(22, 5, 'Hasil Produksi ', 1, 'C');
            $pdf->setXY(119, 53);
            $pdf->Multicell(11, 5, 'Mulai ', 1, 'C');
            $pdf->setXY(130, 53);
            $pdf->Multicell(11, 5, 'Selesai ', 1, 'C');


            $pdf->setXY(141, 48);
            $pdf->Multicell(35, 10, 'Cacat ', 1, 'C');

            $pdf->setXY(176, 48);
            $pdf->Multicell(10, 10, 'Grade ', 1, 'C');

     		$pdf->setXY(186, 48);
            $pdf->Multicell(15, 10, 'Hardness ', 1, 'C');

            $pdf->setXY(201, 48);
            $pdf->Multicell(15, 10, 'UA ', 1, 'C');

            $pdf->setXY(216, 48);
            $pdf->Multicell(15, 10, 'UI ', 1, 'C');

            $pdf->setXY(231, 48);
            $pdf->Multicell(15, 10, 'Winding ', 1, 'C');

            $pdf->setXY(246, 48);
            $pdf->Multicell(13, 10, 'Berat ', 1, 'C');

            $pdf->setXY(259, 48);
            $pdf->Multicell(28, 10, 'Keterangan Downtime ', 1, 'C');

            //Looping No
            $i = 1;
            $Y = 58;
            for($i; $i<=15; $i++) {

            	$pdf->setXY(10,$Y);
            	$pdf->Multicell(7, 8, $i, 1, 'C');
            	$Y = $Y+8;
            }

            //looping seri beam 
            $i = 1;
            $Y = 58;
            $X = 17;
            for($i; $i<=18; $i++) { // kesamping

            	if($i == 1 || $i==6){	
            		$width = 12;
            	}elseif($i == 2){
            		$width = 17;
            	}elseif($i == 3 || $i == 4 || $i == 5|| $i == 17){
            		$width = 13;
            	}elseif($i == 7 || $i == 8 || $i == 9 || $i == 10){
            		$width = 11;
            	}elseif($i == 11){
            		$width = 35;
            	}elseif($i == 12){
            		$width = 10;
            	}elseif ($i == 13 || $i == 14 || $i == 15 || $i == 16) {
            		$width = 15;
            	}elseif($i== 18){
            		$width = 28;
            	}

            	$a = 1;
            	$Y = 58;
            	for($a; $a<=15; $a++){ // kebawah
            		$pdf->setXY($X,$Y);
            		$pdf->Multicell($width, 8, '', 1, 'C');
            		$Y = $Y + 8;
            	}

            	$X = $X + $width;

            }


            $pdf->SetFont('Arial','',6,'C');
    		$pdf->setXY(10,180);
    		$pdf->Multicell(165, 10, '' , 1);

    		// keterangan Cacat
    		$pdf->setXY(10,180);
    		$pdf->Multicell(50, 4, 'Keterangan Cacat '.$dept['nama'].' :', 0, 'L');
    		$y  = 183;
    		$x  = 10;
    		$loop = 1;

    		$list  = $this->m_printMO->get_list_cacat_by_dept($dept_id);
    		foreach ($list as $row) {

    			if($loop == 3){
    				$y  = 183;
    				$x  = $x+30;
    				$loop = 1;
    			}

    			$pdf->setXY($x,$y);
    			$pdf->Multicell(30, 4, $row->kode_cacat.' : '.$row->nama_cacat, 0, 'L');
    			$y = $y+3;

    			$loop++;
    		}

    		$pdf->setXY(10,190);
    		$pdf->Multicell(165, 10, '', 1);

    		$pdf->setXY(10, 190);
    		$pdf->Multicell(185, 5, 'STANDAR OPERASIONAL WARPING DASAR', 0, 'L');

    		$pdf->setXY(10, 193);
    		$pdf->Multicell(185, 5, '1. Amplas terlebih dahulu sebelum dibersihkan (bila pinggiran beam KASAR) ', 0, 'L');

    		$pdf->setXY(10, 196);
    		$pdf->Multicell(185, 5, '2. Selalu dibersihkan dengan menggunakan sabun dan kemudian dilap hingga KERING ', 0, 'L');

    		// ttd disetujui
    		$pdf->setXY(180,180);
    		$pdf->Multicell(50, 4, 'Mengetahui, ', 0, 'L');


    		$pdf->setXY(180, 183);
    		$pdf->Multicell(23, 4, 'Kepala Shift A', 0, 'C');
    		$pdf->setXY(180, 195);
    		$pdf->Multicell(23, 4, '( ', 0, 'L');
    		$pdf->setXY(180, 195);
    		$pdf->Multicell(23, 4, ' )', 0, 'R');

    		$pdf->setXY(210, 183);
    		$pdf->Multicell(23, 4, 'Kepala Shift B', 0, 'C');
    		$pdf->setXY(210, 195);
    		$pdf->Multicell(23, 4, '( ', 0, 'L');
    		$pdf->setXY(210, 195);
    		$pdf->Multicell(23, 4, ' )', 0, 'R');


    		$pdf->setXY(240, 183);
    		$pdf->Multicell(23, 4, 'Kepala Shift C', 0, 'C');
    		$pdf->setXY(240, 195);
    		$pdf->Multicell(23, 4, '( ', 0, 'L');
    		$pdf->setXY(240, 195);
    		$pdf->Multicell(23, 4, ' )', 0, 'R');

    	//	$tgl_now = tgl_indo(date('d-m-Y'));
           //$fileName = 'LAPORAN HARIAN ' . $nama_dept . ' - '.$tgl_now.' .pdf';
    		$pdf->Output();

    	

        }else if($dept_id == 'TRI'){ // if departemen Tricot
            
            $nama_dept = strtoupper($dept['nama']);
            $pdf = new PDF_Code128('l','mm','A4');

            $pdf->SetMargins(0,0,0);
            $pdf->SetAutoPageBreak(False);
            $pdf->AddPage();

            $pdf->setTitle($nama_dept);
            $pdf->SetFont('Arial','B',10,'C');
            $pdf->Cell(0,23,'LAPORAN HASIL PRODUKSI ',0,0,'C');


            $mtr_gl         = '';
            $lbr_jadi       = '';
            $lbr_greige     = '';
            $pcs            = '';
            $stitch         = '';
            $rpm            = '';
            $benang         = '';
            $target_per_shift = '';

            // explode reff note
            $ex2 = explode('|', $head['reff_note']);
            $a   = 0;
            foreach ($ex2 as $exs2) {
                if($a == 6){ // mtr/gl
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $mtr_gl = trim($exps);
                        $b++;
                    }
                }
                if($a == 7 ){// l.greige
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $lbr_greige = trim($exps);
                        $b++;
                    }
                }
                if($a == 8){ // l.jadi
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $lbr_jadi = trim($exps);
                        $b++;
                    }
                }
                if($a == 9){ // pcs
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $pcs = trim($exps);
                        $b++;
                    }
                }
                if($a == 11){ // stitch
                    $exp = explode('=', $exs2);
                     $b   = 1;
                    foreach ($exp as $exps) {
                        $stitch = trim($exps);
                        $b++;
                    }
                    $stitch = trim($exp[1]);
                }
                if($a == 13){ // rpm
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $rpm = trim($exps);
                        $b++;
                    }
                }
                if($a == 14){ // benang
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $benang = trim($exps);
                        $b++;
                    }
                }
                
                $a++;
            } 


            $pdf->SetFont('Arial','',15,'C');
            $pdf->setXY(10,8);
            $pdf->Multicell(95,8,$head['nama_mesin'],1,'C');

            $pdf->setXY(225,8);
            $pdf->Multicell(60,8,$head['kode'],1,'C');

            $pdf->SetFont('Arial','',8,'C');
            $pdf->setXY(225,16);
            $pdf->Multicell(60,4, 'Nomor : FRM/0220/NMB/TC/005', 1, 'C');

            $pdf->SetFont('Arial','B',7,'C');
            $pdf->setXY(194,21);    
            $pdf->Multicell(60,4, 'Tgl.Produksi : ', 0,'R');
            // Caption kiri
            $pdf->setXY(10,25);
            $pdf->Multicell(25, 4, 'Tgl. MO ', 0, 'L');

            $pdf->setXY(10,29);
            $pdf->Multicell(23, 4, 'Product ', 0, 'L');

            $pdf->setXY(10,33);
            $pdf->Multicell(25, 4, 'Origin ', 0, 'L');


            $pdf->setXY(32, 25);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(32, 29);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(32, 33);
            $pdf->Multicell(3, 4, ':', 0, 'L');
           

            $pdf->SetFont('Arial','',7,'C');
            // isi kiri
            $pdf->setXY(33,25);
            $pdf->Multicell(50, 4, tgl_indo(date('d-m-Y H:i:s', strtotime($head['tanggal']))), 0, 'L');

            $pdf->setXY(33,29);
            $pdf->Multicell(58, 4, $head['nama_produk'], 0, 'L');

            $pdf->setXY(33,33);
            $pdf->Multicell(50, 4, $head['origin'], 0, 'L');


            $pdf->SetFont('Arial','B',7,'C');

             // caption tengah 
            $pdf->setXY(90, 25); 
            $pdf->Multicell(25, 4, 'Lot Prefix ', 0, 'L');

            $pdf->setXY(90,29);
            $pdf->Multicell(25, 4, 'Benang ', 0, 'L');

            $pdf->setXY(90,33);
            $pdf->Multicell(25, 4, 'Lebar Greige / Jadi ', 0, 'L');

            $pdf->setXY(115, 25);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(115, 29);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(115, 33);
            $pdf->Multicell(3, 4, ':', 0, 'L');


            // isi tengah
            $pdf->SetFont('Arial','',7,'C');
            $pdf->setXY(116, 25);
            $pdf->Multicell(40, 4, $head['lot_prefix'], 0, 'L');
            $pdf->setXY(116, 29);
            $pdf->Multicell(30, 4, '', 0, 'L');
            $pdf->setXY(116, 33);
            $pdf->Multicell(40, 4, $lbr_greige.' / '.$lbr_jadi, 0, 'L');


            // caption tengah 2
            $pdf->SetFont('Arial','B',7,'C');
            $pdf->setXY(156, 25);
            $pdf->Multicell(30, 4, 'RPM ', 0, 'L');

            $pdf->setXY(156, 29);
            $pdf->Multicell(30, 4, 'Stitch ', 0, 'L' );

            $pdf->setXY(156, 33);
            $pdf->Multicell(30, 4, 'PCS ', 0, 'L');
            
            $pdf->setXY(170, 25);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(170, 29);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(170, 33);
            $pdf->Multicell(3, 4, ':', 0, 'L');

            $pdf->SetFont('Arial','',7,'C');
            // isi tengah 2
            $pdf->setXY(171, 25);
            $pdf->Multicell(30, 4, $rpm, 0, 'L');
            $pdf->setXY(171, 29);
            $pdf->Multicell(30, 4, $stitch, 0, 'L');
            $pdf->setXY(171, 33);
            $pdf->Multicell(30, 4, $pcs, 0, 'L');

            $pdf->SetFont('Arial','B',7,'C');

            // caption kanan
            $pdf->setXY(200, 25);
            $pdf->Multicell(35, 4, 'QTY Order ', 0, 'L');

            $pdf->setXY(200, 29);
            $pdf->Multicell(35, 4, 'Panjang / Gig ', 0, 'L');

            $pdf->setXY(200, 33);
            $pdf->Multicell(35, 4, 'Target / Shift ', 0, 'L');

            $pdf->setXY(220, 25);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(220, 29);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(220, 33);
            $pdf->Multicell(3, 4, ':', 0, 'L'); 

            // isi kanan 
            $pdf->SetFont('Arial','',7,'C');
            $pdf->setXY(221, 25);
            $pdf->Multicell(30, 4, $head['qty'].' '.$head['uom'], 0, 'L');
            $pdf->setXY(221, 29);
            $pdf->Multicell(30, 4, $mtr_gl, 0, 'L');
            $pdf->setXY(221, 33);
            $pdf->Multicell(30, 4, $head['target_efisiensi']*8, 0, 'L');


            // header tabel
            $pdf->SetFont('Arial','B',7,'C');

            $pdf->setXY(10, 38);
            $pdf->Multicell(18, 10, '', 1, 'C');
            $pdf->setXY(10, 41);
            $pdf->Multicell(18, 3, 'Operator / Shift', 0, 'C'); 

            $pdf->setXY(28, 38);
            $pdf->Multicell(19, 10, '', 1, 'C');
            $pdf->setXY(28, 41);
            $pdf->Multicell(19, 3, 'Start/Hasil', 0, 'C');
/*
            $pdf->setXY(47, 38);
            $pdf->Multicell(15, 10, '', 1, 'C');
            $pdf->setXY(47, 41);
            $pdf->Multicell(15, 3, 'Counter', 0, 'C');
*/
            $pdf->setXY(47, 38);
            $pdf->Multicell(71, 10, '', 1, 'C');
            //$pdf->setXY(66, 48);
            //$pdf->Multicell(48, 5, 'KP ('.$head['lot_prefix'].'        ) / PCS', 1, 'C');

            $pdf->setXY(47, 41);
            $pdf->Multicell(71, 5, 'JENIS CACAT', 0, 'C');

            $pdf->setXY(118, 38);
            $pdf->Multicell(26, 5, 'Posisi Cacat', 1, 'C');
            $pdf->setXY(118, 43);
            $pdf->Multicell(13, 5, 'GB', 1, 'C');
            $pdf->setXY(131, 43);
            $pdf->Multicell(13, 5, 'Blok', 1, 'C');

            $pdf->setXY(144, 38);
            $pdf->Multicell(15, 5, 'Berat', 1, 'C');
            $pdf->setXY(144, 43);
            $pdf->Multicell(15, 5, '(Kg)', 1, 'C');

            $pdf->setXY(159, 38);
            $pdf->Multicell(15, 10, '', 1, 'C');
            $pdf->setXY(159, 41);
            $pdf->Multicell(15, 3, 'Grade', 0, 'C');

            /*
            $pdf->setXY(174, 38);
            $pdf->Multicell(15, 10, '', 1, 'C');
            $pdf->setXY(174, 41);
            $pdf->Multicell(15, 3, 'EFF(%)', 0, 'C');
            */

            $pdf->setXY(174, 38);
            $pdf->Multicell(65, 10, '', 1, 'C');
            $pdf->setXY(174, 41);
            $pdf->Multicell(65, 3, 'Keterangan', 0, 'C');

            $pdf->setXY(239, 38);
            $pdf->Multicell(45, 10, '', 1, 'C');
            $pdf->setXY(239, 38);
            $pdf->Multicell(45, 5, 'Cek Press Beam', 0, 'C');

            $pdf->setXY(239, 43);
            $pdf->Multicell(15, 5, 'Jam', 1, 'C');

            $pdf->setXY(254, 43);
            $pdf->Multicell(15, 5, '(v)', 1, 'C');

            $pdf->setXY(269, 43);
            $pdf->Multicell(15, 5, 'Ttd', 1, 'C');


            $x = 10;
            $y = 48;
            $width = 0;

            // looping ke samping
            for($i=1; $i<= 16; $i++){

                if($i == 1 ){
                    $width = 18;
                }elseif($i == 2){
                    $width = 19;
                }elseif($i== 3 || $i == 4 || $i ==  5 || $i == 6 || $i == 7 || $i == 8){
                    $width = 11.83;                    
                }elseif($i == 11 || $i == 12 || $i == 14 || $i == 15 || $i == 16){
                    $width = 15;
                }elseif($i == 9 || $i == 10 ){
                    $width = 13;
                }elseif($i == 13){
                    $width = 65;
                }

                $a = 1;
                $y = 48;
                for($a; $a<=27; $a++){ // looping kebawah
                    $pdf->setXY($x, $y);
                    $pdf->Multicell($width, 5, '', 1,'C');
                    $y = $y + 5;
                }
                $x = $x + $width;

            }

            $y + 8;

            $pdf->setXY(10, $y);
            $pdf->Multicell(13, 5, 'Catatan : ', 0, 'L');

            $pdf->SetFont('Arial','',7,'C');

            $pdf->setXY(24, $y);
            $pdf->Multicell(110, 5, '1. Isi setiap Kolom dengan BENAR, BERTANYA jika tidak mengerti cara mengisi form ini. ', 0, 'L');

            $pdf->setXY(24, $y+5);
            $pdf->Multicell(110, 5, '2. Kolom START/HASIL diisi terlebih dahulu. (Start, Finish, dan Hasil/Pendapatan, Produksi) ', 0, 'L');

            $pdf->setXY(24, $y+10);
            $pdf->Multicell(110, 5, '3. Jika terdapat CACAT, isi kolom Counter, Jenis Cacat Kain dan Posisi Cacat. ', 0, 'L');

            $pdf->setXY(24, $y+15);
            $pdf->Multicell(110, 5, '4. Untuk Pengecekan press beam di kerjakan setiap awal masuk dan sesudah istirahat', 0, 'L');

            $pdf->setXY(140, $y);
            $pdf->Multicell(110, 5, 'CATATAN & KETERANGAN KHUSUS : ');

            $pdf->setXY(140, $y+5);
            $pdf->Multicell(145, 15, '', 1);

            $pdf->SetFont('Arial','',8,'C');
            $pdf->setXY(225,$y+21);    
            $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
            $pdf->Multicell(60,4, 'Tgl.Cetak : '.$tgl_now, 0,'R');

            $pdf->Output();

        }else if($dept_id == 'WRP'){  // if departement Warping Panjang

            $nama_dept = strtoupper($dept['nama']);
            $pdf = new PDF_Code128('p','mm','A4');

            $pdf->SetMargins(0,0,0);
            $pdf->SetAutoPageBreak(False);
            $pdf->AddPage();

            $pdf->setTitle($nama_dept);
            $pdf->SetFont('Arial','B',10,'C');
            $pdf->Cell(0,23,'LAPORAN HARIAN '.$nama_dept,0,0,'C');

            $pdf->SetFont('Arial','',15,'C');
            $pdf->setXY(10,8);
            $pdf->Multicell(30,8,$head['nama_mesin'],1,'C');

            $pdf->setXY(150,8);
            $pdf->Multicell(50,8,$head['kode'],1,'C');

            $pdf->SetFont('Arial','',8,'C');
            $pdf->setXY(140,17);    
            $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
            $pdf->Multicell(60,4, 'Tgl.Cetak : '. $tgl_now, 0,'R');


            $pdf->SetFont('Arial','B',7,'C');
            // Caption kiri
            $pdf->setXY(10,22);
            $pdf->Multicell(20, 4, 'Tgl. MO ', 0, 'L');

            $pdf->setXY(10,26);
            $pdf->Multicell(20, 4, 'Product ', 0, 'L');

            $pdf->setXY(10,33);
            $pdf->Multicell(20, 4, 'Origin ', 0, 'L');

            $pdf->setXY(29, 22);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(29, 26);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(29, 33);
            $pdf->Multicell(20, 4, ':', 0, 'L');
        

            $pdf->SetFont('Arial','',7,'C');
            // isi kiri
            $pdf->setXY(30,22);
            $pdf->Multicell(50, 4, tgl_indo(date('d-m-Y H:i:s',strtotime($head['tanggal']))), 0, 'L');

            $pdf->setXY(30,26);
            $pdf->Multicell(60, 4, $head['nama_produk'], 0, 'L');

            $pdf->setXY(30,33);
            $pdf->Multicell(50, 4, $head['origin'], 0, 'L');


            // EXPLODE REFF NOTE
            $ex    = explode('|', $head['reff_note']);
            $i=0;
            $mo_knitting    = '';
            $mc_knitting    = '';
            $corak          = '';
            $jns_benang     = '';

            foreach($ex as $exs){

                if($i == 1){
                    $mo_knitting    = trim($exs);
                }
                if($i == 2){
                    $mc_knitting    = trim($exs);
                }
                if($i == 3 ){
                    $corak    = trim($exs);
                }
                if($i == 4){
                    $jns_benang    = trim($exs);
                }


                $i++;
            }



            $pdf->SetFont('Arial','B',7,'C');

            // caption tengah 
            $pdf->setXY(93, 22);
            $pdf->Multicell(30, 4, 'MO Knitting ', 0, 'L');

            $pdf->setXY(93, 26);
            $pdf->Multicell(30, 4, 'MC Knitting ', 0, 'L' );

            $pdf->setXY(112, 22);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(112, 26);
            $pdf->Multicell(20, 4, ':', 0, 'L');

            $pdf->SetFont('Arial','',7,'C');
            // isi caption tengah
            $pdf->setXY(113, 22);
            $pdf->Multicell(20, 4, $mo_knitting, 0, 'L');
            $pdf->setXY(113, 26);
            $pdf->Multicell(20, 4, $mc_knitting, 0, 'L');

            $pdf->SetFont('Arial','B',7,'C');
            // caption tengah 
            $pdf->setXY(135, 22);
            $pdf->Multicell(30, 4, 'Corak ', 0, 'L');

            $pdf->setXY(135, 26);
            $pdf->Multicell(30, 4, 'Jenis Benang ', 0, 'L' );

            $pdf->setXY(155, 22);
            $pdf->Multicell(20, 4, ':', 0, 'L');
            $pdf->setXY(155, 26);
            $pdf->Multicell(20, 4, ':', 0, 'L');

            $pdf->SetFont('Arial','',7,'C');
            // isi caption tengah
            $pdf->setXY(156, 22);
            $pdf->Multicell(20, 4, $corak, 0, 'L');
            $pdf->setXY(156, 26);
            $pdf->Multicell(40, 4, $jns_benang, 0, 'L');

            $x1 = 38; // untuk header
            $x2 = 43; // untuk tbody

            // table 1
            for($a=1; $a<4; $a++) {

                $pdf->SetFont('Arial','B',8,'C');
                $pdf->setXY(10,$x1);
                $pdf->Cell(10, 5, 'No', 1, 0, 'C');
                $pdf->Cell(30, 5, 'Jumlah Lembar', 1, 0, 'C');
                $pdf->Cell(20, 5, 'Putaran', 1, 0, 'C');
                $pdf->Cell(30, 5, 'Jam Mulai', 1, 0, 'C');
                $pdf->Cell(30, 5, 'Jam Selesai', 1, 0, 'C');
                $pdf->Cell(18, 5, 'Kg', 1, 0, 'C');
                $pdf->Cell(23, 5, 'UMC', 1, 0, 'C');
                $pdf->Cell(30, 5, 'Keterangan', 1, 0, 'C');

                $array_length_column = array(10,30,20,30,30,18,23,30);
                $x = $x2;
                $y = 10;
                for($i=1; $i<=5; $i++) {
                    # code...
                    foreach ($array_length_column as $length) {
                        # code...
                        $pdf->setXY($y, $x);
                        $pdf->Multicell($length, 5, '', 1,'C');
                        $y=$length+$y;
                        //$y = $y + 5;
                    }
                    $x=$x+5;
                    $y=10;

                    if($i==5){
                        $pdf->setXY($y, $x);
                        $pdf->Multicell(40, 5, 'SHIFT/OPERATOR', 1,'L');
                        $pdf->setXY($y+40, $x);
                        $pdf->Multicell(151, 5, '', 1,'C');
                    }
                }
                $x1 = $x1 + 37;
                $x2 = $x2 + 37 ;
            }

            $pdf->Output();

            // end if departement warping panjang
            
        }else if($dept_id == 'JAC'){ // if departement jacquard
       
            $nama_dept = strtoupper($dept['nama']);
            $pdf = new PDF_Code128('l','mm','A4');

            $pdf->SetMargins(0,0,0);
            $pdf->SetAutoPageBreak(False);
            $pdf->AddPage();

            $pdf->setTitle($nama_dept);
            $pdf->SetFont('Arial','B',10,'C');
            $pdf->Cell(0,23,'LAPORAN HASIL PRODUKSI HARIAN ',0,0,'C');

            // explode reff note
            $ex2 = explode('|', $head['reff_note']);
            $a   = 0;
            $benang = '';
            $lbr_greige = '';
            $lbr_jadi = '';
            $stitch  = '';
            $rpm     = '';
            $pcs     = '';
            $target_jam = '';
            $target_shift = '';
            $panjang      = '';
            foreach ($ex2 as $exs2) {
                if($a == 4){ // benang
                    //$benang = trim($exs2);
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $benang = trim($exps);
                        $b++;
                    }
                }

                if($a == 8){ // pjg / mtr per GL
                   
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $panjang = trim($exps);
                        $b++;
                    }
                }

                if($a == 9){ // l.grey
                   
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $lbr_greige = trim($exps);
                        $b++;
                    }
                }

                if($a == 10){ // l.jadi
                   
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $lbr_jadi = trim($exps);
                        $b++;
                    }
                }

                if($a == 11){ // pcs
                   
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $pcs = trim($exps);
                        $b++;
                    }
                }

                if($a == 13){ // stitch
                   
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $stitch = trim($exps);
                        $b++;
                    }
                }

                if($a == 15){ // rpm
                   
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $rpm = trim($exps);
                        $b++;
                    }
                }

                if($a == 16){ // target/2jam
                   
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $target_jam = trim($exps);
                        $b++;
                    }
                }

                if($a == 17){ // target/shift
                   
                    $exp = explode('=', $exs2);
                    $b   = 1;
                    foreach ($exp as $exps) {
                        $target_shift = trim($exps);
                        $b++;
                    }
                }
                $a++;
            }

            $pdf->SetFont('Arial','',15,'C');
            $pdf->setXY(10,8);
            $pdf->Multicell(95,8,$head['nama_mesin'],1,'C');

            $pdf->setXY(225,8);
            $pdf->Multicell(60,8,$head['kode'],1,'C');

            $pdf->SetFont('Arial','B',7,'C');
            $pdf->setXY(188,18);    
            $pdf->Multicell(60,4, 'Tgl.Produksi : ', 0,'R');
            // Caption kiri
            $pdf->setXY(10,25);
            $pdf->Multicell(25, 4, 'Tgl. MO ', 0, 'L');

            $pdf->setXY(10,29);
            $pdf->Multicell(23, 4, 'Product ', 0, 'L');

            $pdf->setXY(10,33);
            $pdf->Multicell(25, 4, 'Origin ', 0, 'L');


            $pdf->setXY(32, 25);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(32, 29);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(32, 33);
            $pdf->Multicell(3, 4, ':', 0, 'L');

            $pdf->SetFont('Arial','',7,'C');
            // isi kiri
            $pdf->setXY(33,25);
            $pdf->Multicell(50, 4, tgl_indo(date('d-m-Y H:i:s', strtotime($head['tanggal']))), 0, 'L');

            $pdf->setXY(33,29);
            $pdf->Multicell(58, 4, $head['nama_produk'], 0, 'L');

            $pdf->setXY(33,33);
            $pdf->Multicell(50, 4, $head['origin'], 0, 'L');


            $pdf->SetFont('Arial','B',7,'C');

             // caption tengah 
            $pdf->setXY(90, 25); 
            $pdf->Multicell(25, 4, 'Benang ', 0, 'L');

            $pdf->setXY(90,29);
            $pdf->Multicell(25, 4, 'Lebar Greige / Jadi ', 0, 'L');

            $pdf->setXY(90,33);
            $pdf->Multicell(25, 4, 'Panjang ', 0, 'L');

            $pdf->setXY(115, 25);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(115, 29);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(115, 33);
            $pdf->Multicell(3, 4, ':', 0, 'L');


            // isi tengah
            $pdf->SetFont('Arial','',7,'C');
            $pdf->setXY(116, 25);
            $pdf->Multicell(40, 4, $benang, 0, 'L');
            $pdf->setXY(116, 29);
            $pdf->Multicell(30, 4, $lbr_greige.' / '.$lbr_jadi, 0, 'L');
            $pdf->setXY(116, 33);
            $pdf->Multicell(40, 4, $panjang, 0, 'L');


            // caption tengah 2
            $pdf->SetFont('Arial','B',7,'C');
            $pdf->setXY(156, 25);
            $pdf->Multicell(30, 4, 'RPM ', 0, 'L');

            $pdf->setXY(156, 29);
            $pdf->Multicell(30, 4, 'Stitch ', 0, 'L' );

            $pdf->setXY(156, 33);
            $pdf->Multicell(30, 4, 'PCS ', 0, 'L');
            
            $pdf->setXY(170, 25);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(170, 29);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(170, 33);
            $pdf->Multicell(3, 4, ':', 0, 'L');

            $pdf->SetFont('Arial','',7,'C');
            // isi tengah 2
            $pdf->setXY(171, 25);
            $pdf->Multicell(30, 4, $rpm, 0, 'L');
            $pdf->setXY(171, 29);
            $pdf->Multicell(30, 4, $stitch, 0, 'L');
            $pdf->setXY(171, 33);
            $pdf->Multicell(30, 4, $pcs, 0, 'L');

            $pdf->SetFont('Arial','B',7,'C');

            // caption kanan
            $pdf->setXY(200, 25);
            $pdf->Multicell(35, 4, 'Target / 2 Jam ', 0, 'L');
            $pdf->setXY(200, 29);
            $pdf->Multicell(35, 4, 'Target / Shift ', 0, 'L');

            $pdf->setXY(220, 25);
            $pdf->Multicell(3, 4, ':', 0, 'L');
            $pdf->setXY(220, 29);
            $pdf->Multicell(3, 4, ':', 0, 'L');

            // isi kanan 
            $pdf->SetFont('Arial','',7,'C');
            $pdf->setXY(221, 25);
            $pdf->Multicell(30, 4, $target_jam, 0, 'L');
            $pdf->setXY(221, 29);
            $pdf->Multicell(30, 4, $target_shift, 0, 'L');
            
            // header tabel
            $pdf->SetFont('Arial','B',7,'C');

            $pdf->setXY(10, 38);
            $pdf->Multicell(18, 10, '', 1, 'C');
            $pdf->setXY(10, 41);
            $pdf->Multicell(18, 3, 'Operator / Shift', 0, 'C'); 

            $pdf->setXY(28, 38);
            $pdf->Multicell(19, 10, '', 1, 'C');
            $pdf->setXY(28, 41);
            $pdf->Multicell(19, 3, 'Meter/Panel', 0, 'C');
           
            $pdf->setXY(47, 38);
            $pdf->Multicell(71, 10, '', 1, 'C');
          
            $pdf->setXY(47, 41);
            $pdf->Multicell(71, 5, 'JENIS CACAT', 0, 'C');

            $pdf->setXY(118, 38);
            $pdf->Multicell(26, 5, 'Posisi Cacat', 1, 'C');
            $pdf->setXY(118, 43);
            $pdf->Multicell(13, 5, 'GB', 1, 'C');
            $pdf->setXY(131, 43);
            $pdf->Multicell(13, 5, 'Blok', 1, 'C');

            $pdf->setXY(144, 38);
            $pdf->Multicell(15, 5, 'Berat', 1, 'C');
            $pdf->setXY(144, 43);
            $pdf->Multicell(15, 5, '(Kg)', 1, 'C');

            $pdf->setXY(159, 38);
            $pdf->Multicell(15, 10, '', 1, 'C');
            $pdf->setXY(159, 41);
            $pdf->Multicell(15, 3, 'Grade', 0, 'C');

            $pdf->setXY(174, 38);
            $pdf->Multicell(65, 10, '', 1, 'C');
            $pdf->setXY(174, 41);
            $pdf->Multicell(65, 3, 'Masalah', 0, 'C');

            $pdf->setXY(239, 38);
            $pdf->Multicell(45, 10, '', 1, 'C');
            $pdf->setXY(239, 41);
            $pdf->Multicell(45, 5, 'Action / Keterangan', 0, 'C');

            $x = 10;
            $y = 48;
            $width = 0;

            // looping ke samping
            for($i=1; $i<= 14; $i++){
                if($i == 1 ){
                    $width = 18;
                }elseif($i == 2){
                    $width = 19;
                }elseif($i== 3 || $i == 4 || $i ==  5 || $i == 6 || $i == 7 || $i == 8){
                    $width = 11.83;                    
                }elseif($i == 11 || $i == 12){
                    $width = 15;
                }elseif($i == 9 || $i == 10 ){
                    $width = 13;
                }elseif($i == 14){
                    $width = 45;
                }elseif($i == 13){
                    $width = 65;
                }

                $a = 1;
                $y = 48;
                for($a; $a<=27; $a++){ // looping kebawah
                    $pdf->setXY($x, $y);
                    $pdf->Multicell($width, 5, '', 1,'C');
                    $y = $y + 5;
                }
                $x = $x + $width;

            }

            $y + 8;

            $pdf->setXY(10, $y);
            $pdf->Multicell(13, 5, 'Catatan : ', 0, 'L');

            $pdf->SetFont('Arial','',7,'C');

            $pdf->setXY(24, $y);
            $pdf->Multicell(110, 5, '1. Isi setiap Kolom dengan BENAR, BERTANYA jika tidak mengerti cara mengisi form ini. ', 0, 'L');

            $pdf->setXY(24, $y+5);
            $pdf->Multicell(110, 5, '2. Kolom START/HASIL diisi terlebih dahulu. (Start, Finish, dan Hasil/Pendapatan, Produksi) ', 0, 'L');

            $pdf->setXY(24, $y+10);
            $pdf->Multicell(110, 5, '3. Jika terdapat CACAT, isi kolom Counter, Jenis Cacat Kain dan Posisi Cacat. ', 0, 'L');

            $pdf->setXY(140, $y);
            $pdf->Multicell(110, 5, 'CATATAN & KETERANGAN KHUSUS : ');

            $pdf->setXY(140, $y+5);
            $pdf->Multicell(145, 15, '', 1);

            $pdf->SetFont('Arial','',8,'C');
            $pdf->setXY(225,$y+21);    
            $tgl_now = tgl_indo(date('d-m-Y H:i:s'));
            $pdf->Multicell(60,4, 'Tgl.Cetak : '.$tgl_now, 0,'R');
        
            $pdf->Output();
            // end if departement jacquard
        }else{
            echo 'Report Harian Departemen '.$dept['nama'].' belum tersedia !';
        }


    }


}