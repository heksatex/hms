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
    <div class="form-group"  id="view_quant">
		 <div class="col-md-12 table-responsive">
		    <table class="table table-condesed table-hover table-responsive rlstable" id="table_view_quant">
 				<thead>
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
				</thead>
		        <tbody id="tbody_view_quant">
		            <?php
		            $total_qty = 0;
		            $total_qty2= 0;
		            $i =1;
					$item_empty= TRUE;
		            foreach ($quant as $row) {
						$item_empty = FALSE;
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
							<td style="min-width:50px">
								<?php 	
								if( $akses_menu > 0){
								?>
									<input type="checkbox" class="checkItem" value="<?php echo $row->quant_id?>" data-valuetwo="<?php echo $row->row_order?>" data-valuetree="<?php echo $row->lot?>" data-toggle="tooltip" title="Pilih Waste Data">
									<?php if((($row->status == 'ready' AND $type_mo['type_mo']!='colouring' ) OR $level == 'Super Administrator') ){?>
										<a onclick="hapus_quant_mo('<?php  echo $row->move_id ?>', '<?php  echo ($row->quant_id) ?>', '<?php  echo ($row->origin_prod) ?>', '<?php  echo ($row->row_order) ?>',  '<?php  echo ($row->status) ?>')"  href="javascript:void(0)" data-toggle="tooltip" title="Hapus Data" style="padding-left:5px;"><i class="fa fa-trash" style="color: red"></i> </a>

								<?php }
								} 
								?>
							</td>
						</tr>
						<?php 
						$total_qty += $row->qty;
						$total_qty2 += $row->qty2;
		            }
					if($item_empty == TRUE){
						echo '<tr><td colspan=11" align="center">Tidak ada Data</tr>';
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
			<div class="example1_processing_quant table_processing" style="display: none">
                 Processing...
			</div>
		</div>
	</div>
</form>

<script type="text/javascript">

	akses_menu = "<?php echo $akses_menu; ?>";

	if(akses_menu > 0){
		$("#view_data .modal-dialog .modal-content .modal-footer").html('<button type="button" id="btn-waste-data" class="btn btn-primary btn-sm"> Habis Diproduksi</button> <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>');
	}

	$("#btn-waste-data").off("click").on("click",function(e) {
		//$("#btn-waste").unbind("click");
		e.preventDefault();
		var message      = 'Silahkan pilih Product/Lot terlebih dahulu !';
     	var myCheckboxes = new Array();
		var deptid 		 = "<?php echo $deptid; ?>";//parsing data id dept untuk log history
	    var kode   		 = "<?php echo $kode; ?>";//kode MO untuk log history
	    var move_id   	 = "<?php echo $move_id; ?>";
	    var origin_prod  = "<?php echo $origin_prod; ?>";
	    var kode_produk  = "<?php echo $kode_produk; ?>";

        $(".checkItem:checked").each(function() {
			value2  = $(this).attr('data-valuetwo');
			value3  = $(this).attr('data-valuetree');
            myCheckboxes.push({
							"quant_id" : $(this).val(),
							"row_order": value2,
							"lot"      : value3
						});
        });
		
        countchek = myCheckboxes.length;
		if(countchek == 0){
			alert_modal_warning(message);
		}else{
			bootbox.confirm({
			message: "Apakah Anda yakin bahan baku ini Habis diproduksi ?",
			title: "<i class='glyphicon glyphicon-trash'></i> Habis Diproduksi !",
			buttons: {
					confirm: {
						label: 'Yes',
						className: 'btn-primary btn-sm'
					},
					cancel: {
						label: 'No',
						className: 'btn-default btn-sm'
					},
			},callback: function (result) {
					if(result == true){
						please_wait(function(){});
						$('#btn-waste-data').button('loading');
						$.ajax({
							type: "POST",
							url :'<?php echo base_url('manufacturing/mO/waste_details_items')?>',
							dataType: 'JSON',
							data    : { kode 		: kode, 
										deptid      : deptid,
										checkbox    : JSON.stringify(myCheckboxes),
										origin_prod : origin_prod,
										move_id 	: move_id,
										kode_produk : kode_produk,
									},
							success: function(data){
								if(data.sesi=='habis'){
									//alert jika session habis
									alert_modal_warning(data.message);
									window.location.replace('../index');
									$('#btn-waste-data').button('reset');
									unblockUI( function(){});
								}else if(data.status == 'failed'){
									//var pesan = "Lot "+data.lot+ " Sudah diinput !"       
									alert_modal_warning(data.message);
									$('#btn-waste-data').button('reset');
									unblockUI( function(){});
								}else{
									$("#tab_1").load(location.href + " #tab_1");
									$("#tab_2").load(location.href + " #tab_2");
									$("#tab_2").load(location.href + " #tab_2");
									$("#status_bar").load(location.href + " #status_bar");
									$("#foot").load(location.href + " #foot");
									$('#view_data').modal('hide');
									$('#btn-tambah').button('reset');
									unblockUI( function(){
										setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
									});
								}
							},error: function (xhr, ajaxOptions, thrownError) {
								alert(xhr.responseText);
								$('#btn-waste-data').button('reset');
								unblockUI( function(){});
							}
						});
					}else{
					}
			}
		});
		
		}
	});

	//hapus quant mo
	function hapus_quant_mo(move_id,quant_id,origin_prod,row_order,status) {
		
		var baseUrl ='<?php echo base_url(); ?>';
	    var deptid = "<?php echo $deptid; ?>";//parsing data id dept untuk log history
	    var kode   = "<?php echo $kode; ?>";//kode MO untuk log history
		var kode_produk = "<?php echo $kode_produk; ?>"

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
						  please_wait(function(){});
	                      $.ajax({
	                            dataType: "json",
	                            type: 'POST',
	                            url : "<?php echo site_url('manufacturing/mO/hapus_details_items_mo')?>",
	                            data : {kode:kode, kode_produk:kode_produk, move_id:move_id, quant_id:quant_id, row_order:row_order,deptid:deptid, origin_prod:origin_prod },
	                           success: function(data){
									if(data.sesi == "habis"){
										//alert jika session habis
										alert_modal_warning(data.message);
										window.location = baseUrl;//replace ke halaman login
									}else if(data.status == "failed"){
										alert_modal_warning(data.message);
										unblockUI( function(){});
									}else{
										//$("#tab_1").load(location.href + " #tab_1");
										//$("#tab_2").load(location.href + " #tab_2"); 
										//$("#status_bar").load(location.href + " #status_bar");
										//$("#foot").load(location.href + " #foot");  
										//$('#view_data').modal('hide');  
										unblockUI( function(){
											setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
										});
										reloadBodyView();  
									}
								},error: function (xhr, ajaxOptions, thrownError) { 
	                            	alert(xhr.responseText);
									unblockUI( function(){});
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

	//$(document).ready( function(){
		
	function reloadBodyView(){
		var move_id   	 = "<?php echo $move_id; ?>";
	    var origin_prod  = "<?php echo $origin_prod; ?>";
		var type_mo      = "<?php echo $type_mo['type_mo'] ?>";
		var akses_menu   = "<?php echo $akses_menu ?>";
		var level        = "<?php echo $level?>";
		$.ajax({
			type	: "POST",
			dataType: "json",
			url 	:'<?php echo base_url('manufacturing/mO/get_body_view_quant_mo')?>',
				beforeSend: function(e) {
					if(e && e.overrideMimeType) {
						e.overrideMimeType("application/json;charset=UTF-8");
					}
					$("#table_view_quant tbody").remove();
					$(".example1_processing_quant").css('display','block');
				},
			data: {move_id:move_id, origin_prod:origin_prod},
			success: function(data){
				if(data.sesi == "habis"){
					//alert jika session habis
					alert_modal_warning(data.message);
					window.location = baseUrl;//replace ke halaman login
				}else{
					$(".example1_processing_quant").css('display','none');
					var no    = 1;
					var empty = true;
					var tbody = $("<tbody />");
					var checkbox = '';
					$.each(data.items, function(key, value) {
						empty = false;
						if((value.status == 'ready' && type_mo != 'colouring' && akses_menu > 0) || level == 'Super Administrator'){
							funct     = "hapus_quant_mo('"+value.move_id+"', '"+value.quant_id+"', '"+value.origin_prod+"', '"+value.row_order+"',  '"+value.status+"')";
							checkbox  = '<a onclick="'+funct+'"  href="javascript:void(0)" data-toggle="tooltip" title="Hapus Data" style="padding-left:5px;"><i class="fa fa-trash" style="color: red"></i> </a>';
						}else{
							checkbox= '';
						}
						var tr = $("<tr>").append(
                          $("<td>").text(no++),
                          $("<td>").text(value.quant_id),
                          $("<td>").text(value.kode_produk),
                          $("<td>").text(value.nama_produk),
                          $("<td>").text(value.lot),
                          $("<td style='text-align: right;'>").text(value.qty),
                          $("<td>").text(value.uom),
                          $("<td style='text-align: right;'>").text(value.qty2),
                          $("<td>").text(value.uom2),
                          $("<td>").text(value.reff_note),
                          $("<td style='min-width:50px'>").html('<input type="checkbox" class="checkItem" value="'+value.quant_id+'" data-valuetwo="'+value.row_order+'" data-valuetree="'+value.lot+'" data-toggle="tooltip" title="Pilih Waste Data"> '+checkbox	),
                        );
                       	tbody.append(tr);
                    });

					if(empty == true){
					  var tr = $("<tr>").append($("<td colspan='11' align='center'>").text('Tidak ada Data'));
                      tbody.append(tr);
					}

					tr = $("<tr>").append(
							$("<th colspan='4'> ").text(''),
							$("<th> ").text('Total Qty'),
							$("<th style='text-align: right'> ").text(data.total_qty),
							$("<th> ").text(''),
							$("<th style='text-align: right'> ").text(data.total_qty2),
							$("<th>").text(''),
						);
					tbody.append(tr);
					$("#table_view_quant").append(tbody);
				
				}

			},error: function (xhr, ajaxOptions, thrownError) { 
				alert(xhr.responseText);
				alert('error');
			}
     	});

	}
	//});


</script>
