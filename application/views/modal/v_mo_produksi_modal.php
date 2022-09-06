<?php 

	if(!empty($lot_prefix)){
		if(!empty($row_lot)){//jika lot prefix nya tidak kosong maka dikasih counter
			$counter =$row_lot;
			//$counter= substr("000".$row_lot,-3);   
		}else{
			$counter = $row_lot;
			//$counter= substr("000".$row_lot,-3);   
		}
	}else{
		$counter = '';
	}


?>
<form class="form-horizontal" id="form_produksi" name="form_produksi">
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
	            <div class="col-xs-12"><label  style="font-size: 15px; color: #5F9EA0">Hasil Produksi</label></div>
		    </div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
	            <div class="col-xs-4"><label>Target Qty</label></div>
	            <div class="col-xs-8">
		           	<input type="text" name="txtQty" id="txtQty" class="form-control input-sm" value="<?php echo $sisa_qty.' '.$uom_qty_sisa ?>" readonly="readonly" />
				 	<input type="hidden" name="txtkode" id="txtkode" class="form-control input-sm" value="<?php echo $kode ?>"  />		
				 	<input type="hidden" name="txtkode_produk" id="txtkode_produk" class="form-control input-sm" value="<?php echo $kode_produk ?>"  />		
				 	<input type="hidden" name="qty_prod" id="qty_prod" class="form-control input-sm" value="<?php echo $qty_prod ?>"  />
	            </div>  
		    </div>
		</div>
		<div class="col-md-6">
		    <div class="col-md-12 col-xs-12">
		   		<div class="col-xs-4"><label>Qty Inputan</label></div>
				<div class="col-xs-8">
				    <input type="text" name="in_qty" id="in_qty" class="form-control input-sm" readonly="readonly" />
				</div>  
		    </div>
		</div>
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Product</label></div>
				<div class="col-xs-8">
					<input type="text" name="txtproduct" id="txtproduct" class="form-control input-sm produk"  readonly="readonly"  value="<?php echo htmlentities($product) ?>"  data-toggle="tooltip" title="<?php echo htmlentities($product); ?>"/>			
				</div>
			</div>
		</div>		
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Lot</label></div>
				<div class="col-xs-8">
					<input type="text" name="txtlot" id="txtlot" class="form-control input-sm txtlot" value="<?php echo $lot_prefix.''.$counter?>"/>		
				</div>
			</div>
		</div>		
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Grade</label></div>
				<div class="col-xs-8">
					<select class="form-control input-sm grade" name="grade" id="grade"><option value="">-- Pilih Grade --</option><?php foreach($list_grade as $row){ echo "<option>".$row->nama_grade."</option>";}?></select>		
				</div>
			</div>
		</div>		
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Qty</label></div>
				<div class="col-xs-5">
					<input type="text" name="txtqty" id="txtqty" class="form-control input-sm txtqty" value="<?php echo $qty1_std?>"  onkeyup="validAngka(this)" />						
				</div>
				<div class="col-xs-3">
					<input type="text" name="txtuom" id="txtuom" class="form-control input-sm" value="<?php echo $uom_1?>" readonly="readonly" />
				</div>
			</div>
		</div>		
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Qty2</label></div>
				<div class="col-xs-5">
					<input type="text" name="txtqty2" id="txtqty2" class="form-control input-sm" value="<?php echo $qty2_std?>"   />			
				</div>
				<div class="col-xs-3">
					<input type="text" name="txtuom2" id="txtuom2" class="form-control input-sm" value="<?php echo $uom_2?>" readonly="readonly" />
				</div>
			</div>
		</div>		
	</div>
	<?php if($show_lebar['show_lebar'] == 'true'){?>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Lebar Greige</label></div>
				<div class="col-xs-5">
					<input type="text" name="lebar_greige" id="lebar_greige" class="form-control input-sm" value="<?php echo $lbr_produk->lebar_greige?>"   />			
				</div>
				<div class="col-xs-3">
					<select class="form-control input-sm" name="uom_lebar_greige" id="uom_lebar_greige" >
                    	<option value=""></option>
                            <?php foreach ($uom as $row) {
                                    if($row->short == $lbr_produk->uom_lebar_greige){
                                    	echo "<option selected value='".$row->short."'>".$row->short."</option>";
                                	}else{
                                      echo "<option value='".$row->short."'>".$row->short."</option>";
                                	}
                                }
                         ?>
                    </select>
				</div>
			</div>
		</div>		
	</div>
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Lebar Jadi</label></div>
				<div class="col-xs-5">
					<input type="text" name="lebar_jadi" id="lebar_jadi" class="form-control input-sm" value="<?php echo $lbr_produk->lebar_jadi?>"   />			
				</div>
				<div class="col-xs-3">
					<select class="form-control input-sm" name="uom_lebar_jadi" id="uom_lebar_jadi" >
                    	<option value=""></option>
                            <?php foreach ($uom as $row) {
                                    if($row->short == $lbr_produk->uom_lebar_jadi){
                                    	echo "<option selected value='".$row->short."'>".$row->short."</option>";
                                	}else{
                                      echo "<option value='".$row->short."'>".$row->short."</option>";
                                	}
                                }
                         ?>
                    </select>
				</div>
			</div>
		</div>		
	</div>
	<?php }?>

	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
				<div class="col-xs-4"><label>Reff Note</label></div>
				<div class="col-xs-8">
					<input type="text" name="reff_note" id="reff_note" class="form-control input-sm" "/>	
				</div>
			</div>
		</div>		
	</div>
</form>

<hr style="border: 1px solid ">
<form class="form-horizontal" id="konsumsi_bahan" name="form_konsumsi">
	<div class="form-group">
		<div class="col-md-6">
			<div class="col-md-12 col-xs-12">
	            <div class="col-xs-12"><label style="font-size: 15px; color: #5F9EA0">Konsumsi Bahan</label></div>
		    </div>
		</div>
	</div>	
	<div class="row">
		<div class="col-md-12 table-responsive">			
		    <table class="table table-condesed table-hover table-responsive rlstable" id="tabel_konsumsi"> 
				<thead>
				    <tr>
						<th class="style no">No.</th>
					    <th class="style">Product</th>
						<th class="style">Lot</th>
						<th class="style">Grade</th>
						<th class="style" style="text-align: right;">Qty</th>
						<th class="style">uom</th>
						<th class="style">Qty dikonsumsi</th>
					</tr>
				</thead>
		 	    <tbody>
		 	    	<?php 
		 	    	$i = 1;
		 	    	foreach ($konsumsi as $row) {

		 	    	?>
					<tr>
						<td><?php echo $i;?></td>
						<td><?php echo $row->nama_produk;?></td>
						<td><?php echo $row->lot;?></td>
						<td><?php echo $row->nama_grade;?></td>
						<td align="right"><?php echo number_format($row->qty,2)?></td>
						<td><?php echo $row->uom;?></td>
						<td>
							<input type="text" name="qty_konsum"  id="qty_konsum" class="form-control input-sm qty_konsum" data-index="<?php echo $i;?>" onkeyup="validAngka2(this)">
							<input type="hidden" name="jml_produk"  id="jml_produk" class="form-control input-sm jml_produk" value="<?php echo $row->jml_produk ?>">
							<input type="hidden" name="qty_rm"  id="qty_rm" class="form-control input-sm qty_rm" value="<?php echo $row->qty_rm ?>">
							<input type="hidden" name="quant_id"  id="quant_id" class="form-control input-sm quant_id" value="<?php echo $row->quant_id ?>">
							<input type="hidden" name="move_id"  id="move_id" class="form-control input-sm move_id" value="<?php echo $row->move_id ?>">
							<input type="hidden" name="additional"  id="additional" class="form-control input-sm additional" value="<?php echo $row->additional ?>">
							<input type="hidden" name="origin_prod"  id="origin_prod" class="form-control input-sm origin_prod" value="<?php echo $row->origin_prod ?>">
							<input type="hidden" name="kode_produk"  id="kode_produk" class="form-control input-sm kode_produk" value="<?php echo $row->kode_produk ?>">
							<input type="hidden" name="nama_produk"  id="nama_produk" class="form-control input-sm nama_produk" value="<?php echo htmlentities($row->nama_produk) ?>">
							<input type="hidden" name="qty_smi"  id="qty_smi" class="form-control input-sm qty_smi" value="<?php echo $row->qty ?>">
							<input type="hidden" name="uom"  id="uom" class="form-control input-sm uom" value="<?php echo $row->uom ?>">
							<input type="hidden" name="lot"  id="lot" class="form-control input-sm lot" value="<?php echo htmlentities($row->lot) ?>">
							<input type="hidden" name="qty2"  id="qty2" class="form-control input-sm qty2" value="<?php echo $row->qty2 ?>">
							<input type="hidden" name="uom2"  id="uom2" class="form-control input-sm uom2" value="<?php echo $row->uom2 ?>">
							<input type="hidden" name="reff_note"  id="reff_note" class="form-control input-sm reff_note" value="<?php echo $row->reff_note ?>">
							<input type="hidden" name="lbr_greige"  id="lbr_greige" class="form-control input-sm lbr_greige" value="<?php echo $row->lebar_greige ?>">
							<input type="hidden" name="uom_lbr_greige"  id="uom_lbr_greige" class="form-control input-sm uom_lbr_greige" value="<?php echo $row->uom_lebar_greige ?>">
							<input type="hidden" name="lbr_jadi"  id="lbr_jadi" class="form-control input-sm lbr_jadi" value="<?php echo $row->lebar_jadi ?>">
							<input type="hidden" name="uom_lbr_jadi"  id="uom_lbr_jadi" class="form-control input-sm uom_lbr_jadi" value="<?php echo $row->uom_lebar_jadi ?>">
							<input type="hidden" name="grade"  id="grade" class="form-control input-sm grade" value="<?php echo $row->nama_grade ?>">
							<input type="hidden" name="sales_order"  id="sales_order" class="form-control input-sm sales_order" value="<?php echo $row->sales_order ?>">
							<input type="hidden" name="sales_group"  id="sales_group" class="form-control input-sm sales_group" value="<?php echo $row->sales_group ?>">

						</td>
					</tr>
					<?php
					 $i++; 
					}
					?>
				</tbody>
			</table>
		</div>		
	</div>
</form>
<style type="text/css">
	.error{
		border:  1px solid red;
	}  
	.error2{
		border:  1px solid red;
	}  
</style>

<script type="text/javascript">
	
	//load modal panggil funtion total
	total();

	//validasi qty
	function validAngka(a){
	    if(!/^[0-9.]+$/.test(a.value)){
	        a.value = a.value.substring(0,a.value.length-1000);
	    }
	    total();
	}

	//validasi qty
	function validAngka2(a){
	    if(!/^[0-9.]+$/.test(a.value)){
	        a.value = a.value.substring(0,a.value.length-1000);
	    }
	}

	// validasi kp double
	$('#txtlot').on('keyup', function(){

		//alert('masuk');	
		var txtlot   = $('#txtlot').val();
		var kode     = $('#txtkode').val();

		$.ajax({
				dataType : 'JSON',
				type     : 'POST',
			    url      : '<?php echo site_url('manufacturing/mO/cek_input_lot_double') ?>',
				data     : {kode : kode, txtlot :txtlot},
				success : function(data){
					if(data.double == true){
						alert(data.message);
						setTimeout(function(){$('#txtlot').focus();}, 2); // focus in txtlot
					}
				},error : function (jqXHR, textStatus, errorThrown){
					alert(jqXHR.responseText);
					alert('error data');
				}
		});

	});	

	//untuk mentotalkan qty inputan
	function total(){	
		var qty = 0;
		var qty = document.getElementsByName('txtqty');
		inx_qty  = qty.length-1;
		var tot_qty = 0;
		var qty_isi = 0;

		for(var i=0; i<=inx_qty; i++){
			if(qty[i].value!=''){
				qty_isi = qty[i].value;
			}else{
				qty_isi = 0;
			}
			tot_qty = tot_qty + parseFloat(qty_isi);
		}	
		document.getElementById('in_qty').value = tot_qty;
		
		var qty_inp  = $('#in_qty').val();
		var qty_prod = $('#qty_prod').val();
		if(qty_prod != '0'){
			$('.qty_konsum').each(function(index,item){		
				//hitung  = parseFloat(qty_prod) + parseFloat(qty_inp);				
				var qty_smi= $(this).parents("tr").eq(0).find(".qty_smi").val();			
				var qty_rm = $(this).parents("tr").eq(0).find(".qty_rm").val();
				var jml_produk = $(this).parents("tr").eq(0).find(".jml_produk").val();
				var hitung = (qty_rm/qty_prod)*qty_inp;
				//var sum_con = (hitung/qty_rm)*qty_smi;//hitung qty dikonsumsi
				var sum_con = (hitung/jml_produk);//hitung qty dikonsumsi
				if(sum_con > qty_smi){
					$(item).eq(0).val(qty_smi);
				}else{					
					var sum_con_fix = sum_con.toFixed(2);				
					$(item).eq(0).val(sum_con_fix);
				}				
			});
		}
	}


	//simpan data 
	//$("#btn-tambah").unbind( "click" );
	//$("#btn-tambah").off("click").on("click",function(e) {
	$("#btn-tambah").one("click",function(e) {
		e.preventDefault();
		var deptid = '<?php echo $deptid?>';//dept id untuk log history
		var kode   = '<?php echo $kode?>';//kode MO
		var origin_mo 	= '<?php echo $origin_mo ?>';
		var show_lebar  = '<?php echo $show_lebar['show_lebar']?>';
		var valid  		= true;

		var lebar_greige 	 = "";
		var uom_lebar_greige = "";
		var lebar_jadi 	 	 = "";
		var uom_lebar_jadi   = "";
		
		//*-- barang Hasil Produksi *--\\
		var kode_produk = $("#txtkode_produk").val();
		var nama_produk = $("#txtproduct").val();
		var lot  = $("#txtlot").val();
		var qty  = $("#txtqty").val();
		var uom  = $("#txtuom").val();
		var qty2 = $("#txtqty2").val();
		var uom2 = $("#txtuom2").val();
		var reff_note = $("#reff_note").val();
		var grade = $("#grade").val();
		if(show_lebar == 'true'){
			var lebar_greige 	 = $("#lebar_greige").val();
			var uom_lebar_greige = $("#uom_lebar_greige").val();
			var lebar_jadi 	 	 = $("#lebar_jadi").val();
			var uom_lebar_jadi   = $("#uom_lebar_jadi").val();
		}

		if(kode_produk == ''|| nama_produk == ''){
			alert('Produk tidak Boleh kosong !');
			valid = false;
		}

		//cek lot apa ada yg kosong
		$('.txtlot').each(function(index,value){
			if($(value).val()==''){
		   	  alert('Lot tidak boleh kosong !');		   	  
		   	  $(value).addClass('error'); 
		   	  valid = false;
			}else{
			  $(value).removeClass('error'); 
			}
		});	

		//cek qty apa ada yg kosong
		$('.txtqty').each(function(index,value){
			if($(value).val()==''){
		   	  alert('Qty tidak boleh kosong !');
		      $(value).addClass('error'); 
		   	  valid = false;
			}else{
			  $(value).removeClass('error'); 
			}
		});	

		//cek apa qty dikonsumsi melebihi qty stock move items
		var cek_qty = false;
		var empty_qty = false;
		$('.qty_konsum').each(function(index,item){
			qty_konsum  = $(item).parents("tr").find('#qty_konsum').val();
			qty_smi     = $(item).parents("tr").find('#qty_smi').val();

			if(parseFloat(qty_konsum)>parseFloat(qty_smi)){
			    $(item).addClass('error');
				valid = false;
				cek_qty = true;
			}else{
				$(item).removeClass('error');				
			}

			if(qty_konsum== ''){
 				$(item).addClass('error2');
 				valid = false;
				empty_qty = true;
			}else{
				$(item).removeClass('error2');	
			}

		});

		if(cek_qty){//jiika qty_dikonsumsi melebihi qty 
			alert("Qty dikonsumsi tidak boleh Melebihi Qty !");	
		}

		if(empty_qty){//jiika qty_dikonsumsi kosong
			alert("Qty dikonsumsi tidak boleh kosong !");	
		}

        if(valid){			
			var konsumsi_bahan = false;
			//var hasil_produksi = true;		
     		var arr = new Array();			
			$('.qty_konsum').each(function(index,item){
				if ($(item).val()!=="") {
					
					arr.push({
						kode 		: $("#txtkode").val(),
						qty_konsum  : $(item).parents("tr").find('#qty_konsum').val(),
						quant_id 	: $(item).parents("tr").find('#quant_id').val(),
						move_id 	: $(item).parents("tr").find('#move_id').val(),
						additional 	: $(item).parents("tr").find('#additional').val(),
						kode_produk : $(item).parents("tr").find('#kode_produk').val(),
						nama_produk : $(item).parents("tr").find('#nama_produk').val(),
						qty_smi     : $(item).parents("tr").find('#qty_smi').val(),
						uom     	: $(item).parents("tr").find('#uom').val(),
						lot     	: $(item).parents("tr").find('#lot').val(),
						origin_prod : $(item).parents("tr").find('#origin_prod').val(),
						qty2        : $(item).parents("tr").find("#qty2").val(),
						uom2        : $(item).parents("tr").find("#uom2").val(),
						reff_note   : $(item).parents("tr").find("#reff_note").val(),
						qty_rm      : $(item).parents("tr").find('#qty_rm').val(),
						grade       : $(item).parents("tr").find('#grade').val(),
						lbr_greige  : $(item).parents("tr").find('#lbr_greige').val(),
						uom_lbr_greige  : $(item).parents("tr").find('#uom_lbr_greige').val(),
						lbr_jadi    : $(item).parents("tr").find('#lbr_jadi').val(),
						uom_lbr_jadi  : $(item).parents("tr").find('#uom_lbr_jadi').val(),
						sales_order  : $(item).parents("tr").find('#sales_order').val(),
						sales_group  : $(item).parents("tr").find('#sales_group').val(),
					});				
					
					/*
					var ar = $("#txtkode").val()+',|^|'+$(item).parents("tr").find('#qty_konsum').val()+',|^|'+$(item).parents("tr").find('#quant_id').val()+',|^|'+$(item).parents("tr").find('#move_id').val()+',|^|'+$(item).parents("tr").find('#kode_produk').val()+',|^|'+$(item).parents("tr").find('#nama_produk').val()+',|^|'+$(item).parents("tr").find('#qty_smi').val()+',|^|'+$(item).parents("tr").find('#uom').val()+',|^|'+$(item).parents("tr").find('#lot').val()+',|^|'+$(item).parents("tr").find('#origin_prod').val()+',|^|'+$(item).parents("tr").find("#qty2").val()+',|^|'+$(item).parents("tr").find("#uom2").val()+',|^|'+$(item).parents("tr").find("#reff_note").val()+',|^|'+$(item).parents("tr").find('#qty_rm').val()+',|^|'+$(item).parents("tr").find("#grade").val();
						arr.push(ar);
					*/
					konsumsi_bahan = true;
				}
			});		

			//alert(JSON.stringify(arr));
			/*
			if(konsumsi_bahan == false ){
				alert_modal_warning('Maaf, Konsumsi Bahan Kosong !');
			}
			*/ 

			if(kode_produk == '' || nama_produk == '' || lot == ''){
				alert('Maaf, Produk Lot / Hasil Produksi Masih Kosong !');
			}else{
				please_wait(function(){});
			    $('#btn-tambah').button('loading');
			    $.ajax({
			        dataType: "JSON",
			        url : '<?php echo site_url('manufacturing/mO/save_produksi_modal') ?>',
			        type: "POST",
			        data: {data_rm   : JSON.stringify(arr), 
			        	   deptid 	 : deptid, 
			        	   origin_mo : origin_mo, 
			        	   kode 	 : kode,
			        	   kode_produk: kode_produk,
			        	   nama_produk: nama_produk,
			        	   lot        : lot,
			        	   qty 		  : qty,
			        	   uom        : uom,
			        	   qty2       : qty2,
			        	   uom2       : uom2,
			        	   reff_note  : reff_note,
			        	   grade      : grade,
			        	   lebar_greige 	: lebar_greige,
			        	   uom_lebar_greige : uom_lebar_greige,
						   lebar_jadi 	    : lebar_jadi,
			        	   uom_lebar_jadi 	: uom_lebar_jadi,
						},
			        success: function(data){

			        	if(data.sesi == "habis"){
			              //alert jika session habis
			              alert_modal_warning(data.message);
			              window.location.replace('../index');
						  unblockUI( function(){});
						}else if(data.status == "failed"){
			              $("#status_bar").load(location.href + " #status_bar");
			              $("#tab_1").load(location.href + " #tab_1");
			              $("#tab_2").load(location.href + " #tab_2");             
			              $("#foot").load(location.href + " #foot");
			              $('#btn-tambah').button('reset');
			              alert(data.message);
						  unblockUI( function(){});
			           	}else{
			              //jika berhasil disimpan
			              $("#status_bar").load(location.href + " #status_bar");
			              $("#tab_1").load(location.href + " #tab_1");
			              $("#tab_2").load(location.href + " #tab_2");             
			              $("#foot").load(location.href + " #foot");
			              $("tambah_data").html('Menyimpan Data....');
	                 	  $('#tambah_data').modal('hide');
			              $('#btn-tambah').button('reset');
			              if(data.double == 'yes'){
			              	alert_modal_warning(data.message2);
			              }
			              unblockUI( function(){
							setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
						  });
			            }

						$("#tambah_data .modal-dialog .modal-content .modal-body").removeClass('produksi_rm');
			            
			        },error: function (jqXHR, textStatus, errorThrown){
			          alert(jqXHR.responseText);
			          $('#btn-tambah').button('reset');
					  unblockUI( function(){});
			        }
			    });
			    
			}
		  
		}// if valid true
		e.stopImmediatePropagation();
	});
	  
</script>