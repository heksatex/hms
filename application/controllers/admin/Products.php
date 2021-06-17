<?php 
defined('BASEPATH') OR exit('No Direct Script Acces Allowed');

/**
 * 
 */
class Products extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("product_model");
		$this->load->library("form_validation");
	}

	public function index()
	{
	   if ($this->session->userdata('status')=="login") {
		    $data["products"] = $this->product_model->getAll();
			$this->load->view("admin/product/list", $data);
	    } else {
	        // user belum login
	        redirect(base_url());
	    }
	 
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

		$this->load->view("admin/product/new_form");
	}

	public function edit($id = null)
	{
		if(!isset($id)) redirect('admin/products');

		$product 	= $this->product_model;
		$validation = $this->form_validation;
		$validation->set_rules($product->rules());

		if($validation->run()){
			$product->update();
			$this->session->set_flashdata('success', 'Berhasil Disimpan E');
		}

		$data["product"] = $product->getById($id);
		if(!$data["product"]) show_404();

		$this->load->view("admin/product/edit_form", $data);
	}

	public function delete($id = null)
	{
		if(!isset($id)) show_404();

		if($this->product_model->delete($id))
			redirect(site_url('admin/products'));
	}

}


?>