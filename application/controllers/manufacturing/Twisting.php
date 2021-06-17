<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Twisting extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model("m_twisting");

	}

	public function index()
	{
	   	$data["products"] = $this->m_twisting->getAll();
		$this->load->view("manufacturing/v_twisting",$data);
	}

    public function add()
	{
		$product 	= $this->product_model;
		$validation = $this->form_validation;
		$validation->set_rules($product->rules());

		if($validation->run()){
			$product->save();
			$this->session->set_flashdata('success', 'Berhasil Disimpan');
		}
		$this->load->view("manufacturing/v_twisting_new_form");
	}

	public function simpan()
	{
		if($this->m_twisting->validation("save")){
			$this->m_twisting->save();

			$html = $this->load->view('manufacturing/v_twisting', array('model'=>$this->m_twisting->v_twisting_tabel()), true);
			$callback = array (
				'status' => 'sukses',
				'pesan'  => 'Data Berhasil Disimpan',
				'html'   => $html
			);
		}else{
			$callback = array (
				'status' => 'gagal',
				'pesan'  => validation_errors()
			);
		}

		echo json_encode($callback);
	}

	public function ubah($id)
	{
		if($this->m_twisting->validation("update")){
			$this->m_twisting->edit($id);

			$html = $this->load->view('manufacturing/v_twisting', array('model'=>$this->m_twisting->v_twisting_tabel()), true);
			$callback = array (
				'status' => 'sukses',
				'pesan'  => 'Data Berhasil diubah',
				'html'   => $html
			);
		}else{
			$callback = array (
				'status' => 'gagal',
				'pesan'  => validation_errors()
			);
		}

		echo json_encode($callback);
	}


	public function hapus($id)
	{
		$this->m_twisting->save();

		$html = $this->load->view('manufacturing/v_twisting', array('model'=>$this->m_twisting->v_twisting_tabel()), true);
		$callback = array (
			'status' => 'sukses',
			'pesan'  => 'Data Berhasil Disimpan',
			'html'   => $html
		);
		
		echo json_encode($callback);
	}


	
}


?>