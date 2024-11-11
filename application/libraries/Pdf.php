<?php

class Pdf
{
	function __construct(){
		include_once APPPATH .'/third_party/pdf/code128.php'; 
		include_once APPPATH .'/third_party/pdf/pagegroup.php'; 
	}
}

?>