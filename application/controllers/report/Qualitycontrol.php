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

		$mrpRecord= [];
		$dataMesin = [];
		$get_hph_pagi = '';
		$get_hph_siang = '';
		$get_hph_malam = '';
		$get_hph_grade_A  = '';
		$get_hph_grade_B  = '';
		$get_hph_grade_C  = '';

		// get list mesin by id_dept
		$get_mesin = $this->m_qualityControl->get_list_mesin($id_dept);
		foreach($get_mesin as $rmc){

			// get mrp_production
			$get_mrp = $this->m_qualityControl->get_list_mrp_by_tgl($rmc->mc_id,$id_dept,$tgldari);

			foreach($get_mrp as $row){

				// hph per shift
				$get_hph_pagi  = $this->m_qualityControl->get_list_hph_qc_by_date($row->kode,$tgldari,'pagi');
				$get_hph_siang = $this->m_qualityControl->get_list_hph_qc_by_date($row->kode,$tgldari,'siang');
				$get_hph_malam = $this->m_qualityControl->get_list_hph_qc_by_date($row->kode,$tgldari,'malam');
				$hph_per_hari_mtr  = $get_hph_pagi['tot_qty']+$get_hph_siang['tot_qty']+$get_hph_malam['tot_qty'];
				$hph_per_hari_kg   = $get_hph_pagi['tot_kg']+$get_hph_siang['tot_kg']+$get_hph_malam['tot_kg'];
				$hph_per_hari_gl   = $get_hph_pagi['tot_gl']+$get_hph_siang['tot_gl']+$get_hph_malam['tot_gl'];

				// get list hph grade
				$get_hph_grade_A = $this->m_qualityControl->get_list_hph_grade_by_date($row->kode,$tgldari,'A');
				$get_hph_grade_B = $this->m_qualityControl->get_list_hph_grade_by_date($row->kode,$tgldari,'B');
				$get_hph_grade_C = $this->m_qualityControl->get_list_hph_grade_by_date($row->kode,$tgldari,'C');

				$mrpRecord[] = array('kode' => $row->kode,
									 'nama_mesin' => $row->nama_mesin,
									 'nama_produk'=> $row->nama_produk,
									 'hph_per_hari_mtr' => number_format($hph_per_hari_mtr,2),
									 'hph_per_hari_kg'  => number_format($hph_per_hari_kg,2),
									 'hph_per_hari_gl'  => number_format($hph_per_hari_gl,2),
									 'efisisensi'       => number_format(($hph_per_hari_mtr/$row->target_efisiensi)*100,2),
									 'grade_A'   => ($get_hph_grade_A),
									 'grade_B'   => ($get_hph_grade_B),
									 'grade_C'   => ($get_hph_grade_C),
									  );
			}

			$dataMesin[] = array('nama_mesin' => $rmc->nama_mesin,
								 'mrp' => $mrpRecord,
								 'hph_per_hari_mtr'=> 0,
								 'hph_per_hari_kg'=> 0,
								 'hph_per_hari_gl'=> 0,
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

}