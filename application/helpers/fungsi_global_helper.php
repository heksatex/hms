<?php if (!defined("BASEPATH")) exit("No direct script access allowed");
 	
	function tgl_indo($tanggal){
	   $bulan = array (
				1 =>   'Januari',
				'Februari',
				'Maret',
				'April',
				'Mei',
				'Juni',
				'Juli',
				'Agustus',
				'September',
				'Oktober',
				'November',
				'Desember'
	    );
		$pecahkan = explode('-', $tanggal);
			
		// variabel pecahkan 0 = tanggal
		// variabel pecahkan 1 = bulan
		// variabel pecahkan 2 = tahun
		 
		return $pecahkan[0] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[2];
    }

    function tgl_indo2($tanggal){
	   $bulan = array (
				1 =>   'Jan',
				'Feb',
				'Mar',
				'Apr',
				'Mei',
				'Jun',
				'Jul',
				'Ags',
				'Sep',
				'Okt',
				'Nov',
				'Des'
	    );
		$pecahkan = explode('-', $tanggal);
			
		// variabel pecahkan 0 = tanggal
		// variabel pecahkan 1 = bulan
		// variabel pecahkan 2 = tahun
		 
		return $pecahkan[0] . '-' . $bulan[ (int)$pecahkan[1] ] . '-' . $pecahkan[2];
    }

   	function tgl_eng($tanggal){
	   $bulan = array (
				1 =>   'January',
				'February',
				'March',
				'April',
				'May',
				'June',
				'July',
				'August',
				'September',
				'October',
				'November',
				'December'
	    );
		$pecahkan = explode('-', $tanggal);
			
		// variabel pecahkan 0 = tanggal
		// variabel pecahkan 1 = bulan
		// variabel pecahkan 2 = tahun
		 
		return $pecahkan[0] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[2];
    }


	function bln_indo($tanggal){
		$bulan = array (
				1 =>   'Januari',
				'Februari',
				'Maret',
				'April',
				'Mei',
				'Juni',
				'Juli',
				'Agustus',
				'September',
				'Oktober',
				'November',
				'Desember'
		);
		$pecahkan = explode('-', $tanggal);
			 
		return $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[2];
	 }
 



   
?>