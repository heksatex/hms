<?php
if(!defined('BASEPATH')) exit('No direct script acces allowed');
require_once('PHPExcel.php');
//require_once APPPATH . "/third_party/PHPExcel.php";

Class Excel extends PHPExcel
{
	public function __construct()
	{	
		parent:: __construct();
	}
}
