 <form class="form-horizontal">
 	<div class="row">                  
        <div class="col-xs-12 col-md-12">
            <div class="col-xs-5 col-md-3">
                <label>Kode Produk</label>                            
            </div>            
            <div class="col-xs-7 col-md-9"> 
              <label>:</label>                            
              <?php echo $kode_produk; ?>                      
            </div>
            <div class="col-xs-5 col-md-3">                          
                <label>Product </label>
            </div>
            <div class="col-xs-7 col-md-9"> 
                <label>:</label>                            
                <?php echo $nama_produk; ?>
            </div>
        </div>
    </div>
    <div class="form-group"  id="table_view_quant">
		 <div class="col-md-12 table-responsive">
		    <table class="table table-condesed table-hover table-responsive rlstable">
		        <tr>
		            <th class="style no">No.</th>
		            <th class="style">Quant Id</th>
		            <th class="style">Kode Produk</th>
		            <th class="style">Product</th>
		            <th class="style">Lot</th>
		            <th class="style" style="text-align: right;">Qty</th>
		            <th class="style">uom</th>
		            <th class="style" style="text-align: right;">Qty2</th>
		            <th class="style">uom2</th>
		            <th class="style">Reff Note</th>
		            <th class="style no"></th>
		        </tr>
		        <tbody>
		            <?php
		            $total_qty = 0;
		            $total_qty2= 0;
		            $i =1;
		            foreach ($quant as $row) {
		            ?>
		            <tr>
		                <td><?php echo $i++.'.';?></td>
		                <td><?php echo $row->quant_id?></td>
		                <td><?php echo $row->kode_produk?></td>
		                <td><?php echo $row->nama_produk?></td>
		                <td><?php echo $row->lot?></td>
		                <td style="text-align: right;"><?php echo number_format($row->qty,2)?></td>
		                <td><?php echo $row->uom?></td>
		                <td style="text-align: right;"><?php echo number_format($row->qty2,2)?></td>
		                <td><?php echo $row->uom2?></td>
		                <td><?php echo $row->reff_note?></td>
		                <td>
		                	<?php if($row->status == 'ready' AND $type_mo['type_mo']!='colouring' AND $akses_menu > 0){?>
		                	<a onclick="hapus_quant_mo('<?php  echo $row->move_id ?>', '<?php  echo ($row->quant_id) ?>', '<?php  echo ($row->origin_prod) ?>', '<?php  echo ($row->row_order) ?>',  '<?php  echo ($row->status) ?>')"  href="javascript:void(0)" data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a>
		                	<?php }?>
		                </td>
		            </tr>
		            <?php 
		            $total_qty += $row->qty;
		            $total_qty2 += $row->qty2;
		             }
		            ?>
		           	<tr>
		           		<th colspan="4" ></th>
		           		<th>Total Qty</th>
		           		<th style="text-align: right"><?php echo number_format($total_qty,2)?></th>
		           		<td></td>
		           		<th style="text-align: right"><?php echo number_format($total_qty2,2)?></th>
		           		<td></td>
		           	</tr>
		        </tbody>
		    </table>
		</div>
	</div>
</form>

<script type="text/javascript">
	//hapus quant mo
	function hapus_quant_mo(move_id,quant_id,origin_prod,row_order,status) {
		
		var baseUrl ='<?php echo base_url(); ?>';
	    var deptid = "<?php echo $deptid; ?>";//parsing data id dept untuk log history
	    var kode   = "<?php echo $kode; ?>";//kode MO untuk log history

	    if(status == 'done'){
	        alert_modal_warning('Maaf Product tidak bisa dihapus, Product sudah di produksi !');
	    }else{
	      bootbox.dialog({
	          message: "Apakah Anda ingin menghapus data ?",
	          title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
	          buttons: {
	            danger: {
	                label    : "Yes ",
	                className: "btn-primary btn-sm",
	                callback : function() {
	                      $.ajax({
	                            dataType: "json",
	                            type: 'POST',
	                            url : "<?php echo site_url('manufacturing/mO/hapus_details_items_mo')?>",
	                            data : {kode : kode, move_id : move_id, quant_id : quant_id, row_order:row_order,deptid:deptid, origin_prod : origin_prod },
	                            error: function (xhr, ajaxOptions, thrownError) { 
	                            alert(xhr.responseText);
	                            }
	                      })
	                      .done(function(response){
	                        if(response.status == 'failed'){
	                          alert_modal_warning(response.message);
	                          window.location = baseUrl;//replace ke halaman login
	                        }else{
	                          $("#table_view_quant").load(location.href + " #table_view_quant");
	                          $("#tab_1").load(location.href + " #tab_1");
             				  $("#tab_2").load(location.href + " #tab_2"); 
	                          $("#status_bar").load(location.href + " #status_bar");
	                          $("#foot").load(location.href + " #foot");  
	                          $('#view_data').modal('hide');                 
	                          alert_notify(response.icon,response.message,response.type,function(){});
	                        }
	                      })
	                }
	            },
	            success: {
	                  label    : "No",
	                  className: "btn-default  btn-sm",
	                  callback : function() {
	                  $('.bootbox').modal('hide');
	                  }
	            }
	          }
	      });
	    }

	}

</script>
