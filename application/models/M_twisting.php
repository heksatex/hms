<?php if (!defined('BASEPATH')) exit ('No direct Script Acces Allowed');

/**
 * 
 */
class M_twisting extends CI_Model
{
	
	public  function getAll()
	{
		return $this->db->get('products')->result();

	}

	public function validation($mode)
	{
		$this->load->library('form_validation');

		if($mode == "save"){
			$this->form_validation->set_rules('nama', 'Nama', 'required');
			$this->form_validation->set_rules('price','Price','numeric');
			$this->form_validation->set_rules('description','Description','required');

			if($this->form_validation->run())
				return true;
			else
				return false;
		}
	}

	public function save()
	{
		$data = array(
				"product_id"=>$this->input->uniqid(),
			    "nama" => $this->input->post('nama'),
			    "price"=> $this->input->post('price'),
			    "image"=> $this->input->post('image'),
			    "description"=>$this->input->post('description')
		);
		$this->db->insert('products', $data);
	}

	public function edit($id)
	{
		$data = array(
				"product_id" => $this->input->post('product_id'),
			    "nama" => $this->input->post('nama'),
			    "price"=> $this->input->post('price'),
			    "image"=> $this->input->post('image'),
			    "description"=>$this->input->post('description')
		);
		$this->db->where('product_id', $id);
		$this->db->update('products', $data);
	}

	public function delete($id)
	{
		$this->db->where('product_id', $id);
		$this->db->delete('products');
	}

}



?>