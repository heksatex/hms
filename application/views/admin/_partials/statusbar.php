<?php
 $bar  = $this->m_menu->status_bar($this->uri->segment(2))->row_array();//mengambil status bar aktif atau tidak
 	if($bar['status_bar'] == '1'){
 		?>
 		<div class="box" style="background: #C0C0C0;">
		  <div class="wizard1">
		  	<?php 
		  	  if(!empty($deptid)){//$deptid di dapat dari parsing view yang view nya sama aja (manufacturing/warehouse)
			  	$list = $this->m_menu->jenis_status_bar_deptid($this->uri->segment(2),$deptid);//untuk menampilkan jenis status bar
		  	  }else{
			  	$list = $this->m_menu->jenis_status_bar($this->uri->segment(2));//untuk menampilkan jenis status bar
		  	  }
			  if(!empty($jen_status)){//cek apakah ada status berdasarkan kode  co, jen_status[status] didapat dari view
			  	$jenis_status = $jen_status;
			  }else{
			  	$jenis_status = '';
			  }
	 		  foreach ($list as $row) {
	 		  	if($row->jenis_status ==  $jenis_status ){
	 		  		$a      = "current";
	 		  		$active = "badge badge-inverse  current";
	 		  	}else{
	 		  		$a      = "hidden-xs";
	 		  		$active = "badge  hidden-xs";
	 		  	}
		  	?>
			    <a class="<?php echo $a; ?>"><span class="<?php echo $active; ?>"><?php echo $row->nama_status?></span></a>
			    <!--a class="current "><span class="badge badge-inverse  current">Produksi</span> </a-->
		  	<?php 
		     }
		    ?>
		  </div>
		</div>
 	<?php 
 	}
?>