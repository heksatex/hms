<form class="form-horizontal">
 	<div class="row">                  
        <div class="col-xs-12 col-md-5">
            <div class="col-xs-5 col-md-4">
                <label>Kode Produk</label>                            
            </div>            
            <div class="col-xs-7 col-md-8"> 
              <label>:</label>                            
              <?php echo $kode_produk; ?>                      
            </div>
            <div class="col-xs-5 col-md-4">                          
                <label>Nama Produk </label>
            </div>
            <div class="col-xs-7 col-md-8"> 
                <label>:</label>                            
                <?php echo $nama_produk; ?>
            </div>
        </div>
        <div class="col-xs-12 col-md-5">
            <div class="col-xs-5 col-md-4">
                <label>Kode BOM</label>                            
            </div>            
            <div class="col-xs-7 col-md-8"> 
              <label>:</label>                            
              <?php echo $kode_bom; ?>                      
            </div>
            <div class="col-xs-5 col-md-4">                          
                <label>Nama BOM </label>
            </div>
            <div class="col-xs-7 col-md-8"> 
                <label>:</label>                            
                <?php echo $nama_bom; ?>
            </div>
        </div>
        <div class="col-xs-12 col-md-2" style="text-align: right">
        	<button type="button"  class="btn btn-xs btn-danger" name="btn-cancel-waste" id="btn-cancel-waste" data-toggle="tooltip" title="Batal Produk Waste">Batal Waste</button>
        </div>
    </div>

    <div class="form-group">
		<div class="col-md-12 col-xs-12">
			<div class="col-xs-12"><label style="font-size: 15px;">Penerimaan Barang (IN)</label></div>
		</div>
	</div>
    <div class="form-group"  id="table_1">
		 <div class="col-md-12 table-responsive">
		     <table class="table table-condesed table-hover table-responsive rlstable">
		        <tr>
		            <th class="style no">No.</th>
		            <th class="style">Kode</th>
		            <th class="style">Tanggal Dibuat</th>
		            <th class="style">Origin</th>
		            <th class="style">Lokasi Tujuan</th>
		            <th class="style">Departemen</th>
		            <th class="style" style="text-align: right;">Qty Target</th>
		            <th class="style" style="text-align: right;">Tersedia</th>
		            <th class="style">Reff Note</th>
		            <th class="style">Status</th>
		        </tr>
		        <tbody>
		            <?php
		            $i =1;
		            foreach ($penerimaan as $row) {
		            	$kode_encrypt = encrypt_url($row->kode);
		            	if($row->qty_target > $row->qty_tersedia){
		            		$color = 'blue';
		            	}elseif($row->qty_tersedia > $row->qty_target){
		            		$color = 'red';
		            	}else{
		            		$color = '';
		            	}
		            ?>
		            <tr>
		                <td><?php echo $i++.'.';?></td>
		                <td><?php echo '<a href="'.base_url('warehouse/penerimaanbarang/edit/'.$kode_encrypt).'" target="_blank">'.$row->kode.'</a>';?></td>
		                <td><?php echo $row->tanggal?></td>
		                <td><?php echo $row->origin?></td>
		                <td><?php echo $row->lokasi_tujuan?></td>
		                <td><?php echo $row->departemen?></td>
		                <td style="text-align: right;"><?php echo number_format($row->qty_target,2)?></td>
		                <td style="color:<?php echo $color;?>; text-align: right;" ><?php echo number_format($row->qty_tersedia,2)?></td>
		                <td><?php echo $row->reff_note?></td>
		                <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
		               
		            </tr>
		            <?php 
		             }
		            ?>
		        
		        </tbody>
		    </table>
		</div>
	</div>
	<hr style="border: 1px solid ">

	<div class="form-group">
		<div class="col-md-12 col-xs-12">
	        <div class="col-xs-12"><label style="font-size: 15px;">Manufacturing Order (MO)</label></div>
	   </div>
	</div>
	<div class="form-group"  id="table_2">
		 <div class="col-md-12 table-responsive">
		    <table class="table table-condesed table-hover table-responsive rlstable">
		        <tr>
		            <th class="style no">No.</th>
		            <th class="style">Kode</th>
		            <th class="style">Tanggal Dibuat</th>
		            <th class="style">Origin</th>
		            <th class="style">Departemen</th>
		            <th class="style" style="text-align: right;">Qty Target</th>
		            <th class="style" style="text-align: right;">Tersedia</th>
		            <th class="style">Reff Note</th>
		            <th class="style">Status</th>
		        </tr>
		        <tbody>
		            <?php
		            $i =1;
		            $color = '';
		            foreach ($mo as $row) {
		            	$kode_encrypt = encrypt_url($row->kode);
		            	if($row->qty_target > $row->qty_tersedia){
		            		$color = 'blue';
		            	}elseif($row->qty_tersedia > $row->qty_target){
		            		$color = 'red';
		            	}else{
		            		$color = '';
		            	}
		            ?>
		            <tr>
		                <td><?php echo $i++.'.';?></td>
		                <td><?php echo '<a href="'.base_url('manufacturing/mO/edit/'.$kode_encrypt).'" target="_blank">'.$row->kode.'</a>';?></td>
		                <td><?php echo $row->tanggal?></td>
		                <td><?php echo $row->origin?></td>
		                <td><?php echo $row->departemen?></td>
		             	<td style="text-align: right;"><?php echo number_format($row->qty_target,2)?></td>
		                <td style="color:<?php echo $color;?>; text-align: right;" ><?php echo number_format($row->qty_tersedia,2)?>
		                <td><?php echo $row->reff_note?></td>
		                <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
		            </tr>
		            <?php 
		             }
		            ?>
		        
		        </tbody>
		    </table>
		</div>
	</div>
	<hr style="border: 1px solid ">

	<div class="form-group">
		<div class="col-md-12 col-xs-12">
	        <div class="col-xs-12"><label style="font-size: 15px;">Pengiriman Barang (OUT)</label></div>
		</div>
	</div>
	<div class="form-group"  id="table_3">
		 <div class="col-md-12 table-responsive">
		    <table class="table table-condesed table-hover table-responsive rlstable">
		        <tr>
		            <th class="style no">No.</th>
		            <th class="style">Kode</th>
		            <th class="style">Tanggal Dibuat</th>
		            <th class="style">Origin</th>
		            <th class="style">Lokasi Tujuan</th>
		            <th class="style">Departemen</th>
		            <th class="style" style="text-align: right;">Qty Target</th>
		            <th class="style" style="text-align: right;">Tersedia</th>
		            <th class="style">Reff Note</th>
		            <th class="style">Status</th>
		        </tr>
		        <tbody>
		            <?php
		            $i =1;
		            foreach ($pengiriman as $row) {
		            	$kode_encrypt = encrypt_url($row->kode);
		            	if($row->qty_target > $row->qty_tersedia){
		            		$color = 'blue';
		            	}elseif($row->qty_tersedia > $row->qty_target){
		            		$color = 'red';
		            	}else{
		            		$color = '';
		            	}
		            ?>
		            <tr>
		                <td><?php echo $i++.'.';?></td>
		                <td><?php echo '<a href="'.base_url('warehouse/pengirimanbarang/edit/'.$kode_encrypt).'" target="_blank">'.$row->kode.'</a>';?></td>
		                <td><?php echo $row->tanggal?></td>
		                <td><?php echo $row->origin?></td>
		                <td><?php echo $row->lokasi_tujuan?></td>
		                <td><?php echo $row->departemen?></td>
		                <td style="text-align: right;"><?php echo number_format($row->qty_target,2)?></td>
		                <td style="color:<?php echo $color;?>; text-align: right;" ><?php echo number_format($row->qty_tersedia,2)?>
		                <td><?php echo $row->reff_note?></td>
		               <td><?php if($row->status == 'cancel') echo 'Batal';  else echo $row->status;?></td>
		               
		            </tr>
		            <?php 
		             }
		            ?>
		        
		        </tbody>
		    </table>
		</div>
	</div>
</form>

<script type="text/javascript">

	// batal items
	$("#btn-cancel-waste").off("click").on("click",function(e) {
    	//e.preventDefault();
    	
     	var kode  =  "<?php echo $kode; ?>";
      	var kode_produk  = "<?php echo $kode_produk?>";
      	var row_order    = "<?php echo $row_order ?>";
      	var origin 		 = "<?php echo $origin ?>";

        bootbox.dialog({
        message: "Apakah Anda ingin membatalkan Produk Waste ?",
        title: "<i class='fa fa-warning'></i> Batal Produk Waste !",
        buttons: {
          danger: {
              label    : "Yes ",
              className: "btn-primary btn-sm",
              callback : function() {
                please_wait(function(){});
                $.ajax({
                  dataType: "JSON",
                  url : '<?php echo site_url('ppic/productionorder/batal_waste_production_order') ?>',
                  type: "POST",
                  data: {kode:kode, kode_produk:kode_produk, row_order:row_order, origin:origin  },
                  success: function(data){
                    if(data.sesi=='habis'){
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('../index');
                    }else if(data.status == 'failed'){
                        unblockUI( function(){});
                        //alert(data.message);
                        alert_modal_warning(data.message);
                    }else{
                        unblockUI( function() {
                          setTimeout(function() { alert_notify(data.icon,data.message,data.type); }, 1000);
                        });
                  		$('#view_data').modal('hide');
                     
                    }
                    // agar bisa scroll di modal setelah process
                    $('.bootbox.modal').on('hidden.bs.modal', function () {
					  if($(".modal").hasClass('in')){
						$('body').addClass('modal-open');
					  }
					});

                  },
                  error: function (xhr, ajaxOptions, thrownError){
                    unblockUI( function(){});
                    alert('Error data');
                    alert(xhr.responseText);

                  }
                });
              }

          },
          success: {
                label    : "No",
                className: "btn-default  btn-sm",
                callback : function() {
                  $('.bootbox').modal('hide');
                }
          }
        },
        });

        $('.bootbox.modal').on('hidden.bs.modal', function () {
		  if($(".modal").hasClass('in')){
			$('body').addClass('modal-open');
		  }
		});
        e.stopPropagation();
        return false;
    }); 



</script>
