<?php
if(!defined('BASEPATH')) exit('No dirext script acces allowed');
require_once('PHPExcel/IOFactory.php');


Class IOFactory extends PHPExcel_IOFactory
{
	public function __construct()
	{	
		parent:: __construct();
	}
}
