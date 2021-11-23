<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class HPHjacquard extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
        $this->load->model('m_HPHjacquard');
        $this->load->model('m_produksiJacquard');
	}


	public function index()
	{
		$id_dept        = 'HPHJAC';
        $data['id_dept']= $id_dept;
		$data['mesin']  = $this->_module->get_list_mesin_report('jac');
		$this->load->view('report/v_hph_jacquard', $data);
	}


    function loadData()
	{

		$tgldari   = $this->input->post('tgldari');
		$tglsampai = $this->input->post('tglsampai');
		$mo        = $this->input->post('mo');
		$corak     = $this->input->post('corak');
		$mc        = $this->input->post('mc');
		$lot       = $this->input->post('lot');
		$user      = $this->input->post('user');
		$jenis     = $this->input->post('jenis');
		$shift_arr = $this->input->post('shift');// array shift pagi/siang/malam
		$id_dept   = 'JAC';
		$where_date = '';
		$loop       = 1;
		$condition_OR = '';
        	
       	// cari selisih periode tangal
        $diff    = strtotime($tglsampai) - strtotime($tgldari);
        $hasil   = floor($diff / (60 * 60 * 24));
      
		// cek tgl dari dan tgl sampai
		if(strtotime($tglsampai) < strtotime($tgldari) ){
			$callback = array('status' => 'failed', 'message' => 'Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !', 'icon' =>'fa fa-warning', 'type' => 'danger'  );  

		}else if(count($shift_arr) > 0 AND $hasil > 30){ // cek maksimal 30 hari  jika shift di ceklis 
			$callback = array('status' => 'failed', 'message' => 'Maaf, Jika Shift di Ceklist (v) maka Periode Tanggal tidak boleh lebih dari 30 hari !', 'icon' =>'fa fa-warning', 'type' => 'danger'  ); 

		}else{

			if(count($shift_arr) > 0){

				$tgldari    = date('Y-m-d', strtotime($tgldari));
				$tglsampai  = date('Y-m-d', strtotime($tglsampai));
				$i = 0;
				while($i<=30){

					$tgldari_    = strtotime($tgldari);
					$tglsampai_  = strtotime($tglsampai);

					foreach ($shift_arr as $val) {
						if($loop > 1){
							$condition_OR = ' OR ';
						}
						# code...
						if($val == 'Pagi'){
							$jam_dari    = '07:00:00';
							$jam_sampai  = '14:59:59';
						}else if($val == 'Siang'){
							$jam_dari    = '15:00:00';
							$jam_sampai  = '22:59:59';
						}else if($val == 'Malam'){
							$jam_dari    = '23:00:00';
							$jam_sampai  = '06:59:59';
						}

						if($val == 'Malam'){
							$tglsampai_2 = date('Y-m-d', strtotime('+1 day',$tgldari_));
						}else{
							$tglsampai_2 = $tgldari;
						}

						$tgldari_2 = $tgldari;

						$where_date .= $condition_OR." ( mpfg.create_date >='".$tgldari_2." ".$jam_dari."' AND mpfg.create_date <='".$tglsampai_2." ".$jam_sampai."' ) ";
						$loop++;
					}
		

					if($tgldari_ == $tglsampai_){
						break;
					}else{
						if($loop == 2){
							$where_date = $where_date.' OR ';
						}
						$tgldari = date('Y-m-d', strtotime('+1 day',$tgldari_));
					}

					$loop = 1;
					$i++;
				}

				if(count($shift_arr) == 1){
					$where_date = rtrim($where_date, ' OR ');
				}

				$where_date = '( '.$where_date.' )';

			}else{
				$tgldari    = date('Y-m-d H:i:s', strtotime($tgldari));
				$tglsampai  = date('Y-m-d H:i:s', strtotime($tglsampai));

				$where_date  = "( mpfg.create_date >= '".$tgldari."' AND mpfg.create_date <= '".$tglsampai."') ";
			}


			// get location by jenis (HPH=stock, Waste)
			$cek = $this->_module->get_nama_dept_by_kode($id_dept)->row_array();

			if($jenis == 'HPH'){
				$where_jenis = "AND mpfg.lokasi = '".$cek['stock_location']."' ";
			}else if($jenis == 'Waste'){
				$where_jenis = "AND mpfg.lokasi = '".$cek['waste_location']."' ";
			}else{
				$where_jenis = '';
			}

			if(!empty($mo)){
				$where_mo  = "AND mpfg.kode LIKE '%".addslashes($mo)."%' ";
			}else{
				$where_mo  = '';
			}

			if(!empty($mc)){
				$where_mc  = "AND ms.mc_id = '".addslashes($mc)."' ";
			}else{
				$where_mc  = '';
			}

			if(!empty($lot)){
				$where_lot  = "AND mpfg.lot LIKE '%".addslashes($lot)."%' ";
			}else{
				$where_lot  = '';
			}

			if(!empty($corak)){
				$where_corak  = "AND mpfg.nama_produk LIKE '%".addslashes($corak)."%' ";
			}else{
				$where_corak  = '';
			}

			if(!empty($user)){
				$where_user  = "AND mpfg.nama_user LIKE '%".addslashes($user)."%' ";
			}else{
				$where_user  = '';
			}

			$dataRecord= [];

			$lbr_jadi       = '';
	        $lbr_greige     = '';
	        $stitch         = '';
	        $rpm            = '';

			$where     = "WHERE mp.dept_id = '".$id_dept."' AND ".$where_date." ".$where_mc." ".$where_lot." ".$where_corak." ".$where_user." ".$where_jenis." ".$where_mo." ";

			$items = $this->m_HPHjacquard->get_list_HPH_jacquard_by_kode($where);
			foreach ($items as $val) {

				// explode origin 
				$exp   = explode('|', $val->origin);
				$no    = 0;
				foreach ($exp as $exps) {
					if($no == 0){
						$sc  = trim($exps);
						$mkt = $this->m_produksiJacquard->get_marketing_by_kode($sc);
					}
					$no++;
				}

				// explode reff_note
				$exp2  = explode('|', $val->reff_note);
				$a     = 0;
				foreach ($exp2 as $exps2) {
					# code...
					if($a == 7 ){// l.greige
	                    $ex2 = explode('=', $exps2);
	                    $lbr_greige = trim($ex2[1]);
	                }
	                if($a == 8){ // l.jadi
	                    $ex2 = explode('=', $exps2);
	                    $lbr_jadi = trim($ex2[1]);
	                }

	                if($a == 11){ // stitch
	                    $ex2 = explode('=', $exps2);
	                    $stitch = trim($ex2[1]);
	                }
	                if($a == 13){ // rpm
	                    $ex2 = explode('=', $exps2);
	                    $rpm = trim($ex2[1]);
	                }
	                $a++;
				}


				$dataRecord[] = array('kode' => $val->kode,
									  'nama_mesin' => $val->nama_mesin,
									  'sc'     => $sc,
									  'tgl_hph'    => $val->tgl_hph,
									  'kode_produk'=> $val->kode_produk,
									  'nama_produk'=> $val->nama_produk,
									  'lot'        => $val->lot,
									  'qty1'       => $val->qty,
									  'uom1'	   => $val->uom,
									  'qty2'	   => $val->qty2,
									  'uom2'       => $val->uom2,
									  'grade'      => $val->nama_grade,
									 // 'lbr_greige' => $lbr_greige,
									 // 'lbr_jadi'   => $lbr_jadi,
									  //'rpm'        => $rpm,
									  //'stitch'     => $stitch,
									  'marketing'  => $mkt,
									  'nama_user'  => $val->nama_user,
									  'reff_note'  => $val->reff_note_sq,
									  'lokasi'     => $val->lokasi
									);
				$lbr_jadi       = '';
		        $lbr_greige     = '';
		        $stitch         = '';
		        $rpm            = '';
			}

			$allcount           = $this->m_HPHjacquard->get_record_hph_jacquard($where);
	        $total_record       = 'Total Data : '. number_format($allcount);

			$callback = array('record' => $dataRecord, 'total_record' => $total_record);

		} //else if validasi

		echo json_encode($callback);
	}





}