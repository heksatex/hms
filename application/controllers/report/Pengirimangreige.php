<?php defined('BASEPATH') or exit ('No Direct Script Acces Allowed');

/**
 * 
 */
class Pengirimangreige extends MY_Controller
{
	public function __construct()
	{
		parent:: __construct();
		$this->is_loggedin();//cek apakah user sudah login
		$this->load->model('_module');
	}

    public function index()
	{
		$id_dept        = 'ROUTGRG';
        $data['id_dept']= $id_dept;
		$this->load->view('report/v_pengiriman_greige', $data);
	}

}