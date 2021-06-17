<div class="row">
	<div class="col-md-2"></div>
	<div class="col-md-8">
		<div class="box-footer box-comments">
			<?php 

			if(!empty($kode)){// parsing parameter dari View/body
				$kode_ = $kode;
				$mms_  = $mms;
				$list = $this->m_menu->log_history_new($kode_,$mms_); // sementara  mungkin nanti d update
			}else{

				if(!empty($this->uri->segment(4))){//cek apakah ada kode 
				  	$kode_decrypt = $this->uri->segment(4);
	      			$kode_  = decrypt_url($kode_decrypt);
					$list = $this->m_menu->log_history($kode_);
				}else{
					$kode_ = '';
				}
			}
			/*
			*/
			//$list = $this->m_menu->log_history($this->uri->segment(2), $kode);

			if($kode_ != ''){
				foreach ($list as $row) {
				?>
			    <div class="box-comment">
			        <!-- User image -->
			        <img src="<?php echo base_url('dist/img/profile_default.png') ?>" class="user-image" alt="User Image">
			        <div class="comment-text">
			            <span class="username">
			               <?php echo  $row->nama_user;?>  <?php echo "| ". $row->jenis_log;?>  
			            <span class="text-muted pull-right"><?php echo  date("d M Y H:i:s", strtotime($row->datelog));?></span>
			            </span>
			             <?php echo  $row->note;?>
			        </div>
			    </div>
			   <?php
				}
			}
			?>
		</div>
	</div>
	<div class="col-md-2"></div>
</div>