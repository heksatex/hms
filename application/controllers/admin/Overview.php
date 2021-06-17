<?php

class Overview extends MY_Controller {
    public function __construct()
    {
		parent::__construct();
		$this->is_loggedin();//cek apakah user sudah login

	}

	public function index()
	{
		
	    $this->load->view("admin/overview");
	 
	}
}

?>