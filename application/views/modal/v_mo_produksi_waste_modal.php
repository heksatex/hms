<?php 

	if(empty($konsumsi)){
		$item_rm = false;
	}else{
		$item_rm = true;
	}

	if($sisa_qty < 0){
		$error_target = 'error_target';
	}else{
		$error_target = '';
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
	<div class="col-md-6">
		<div class="form-group">
			<div class="col-md-12">
				<div class="col-md-12 col-xs-12">
					<div class="col-xs-4"><label>Target Qty</label></div>
					<div class="col-xs-8">
						<input type="text" name="txtQty" id="txtQty" class="form-control input-lg  <?php echo $error_target; ?>" value="<?php echo $sisa_qty.' '.$uom_qty_sisa ?>" readonly="readonly" />
						<input type="hidden" name="qty_sisa" id="qty_sisa" class="form-control input-sm" value="<?php echo $sisa_qty?>" readonly="readonly" />
						<input type="hidden" name="txtkode" id="txtkode" class="form-control input-sm" value="<?php echo $kode ?>"  />		
						<input type="hidden" name="txtkode_produk" id="txtkode_produk" class="form-control input-sm" value="<?php echo $kode_produk ?>"  />		
						<input type="hidden" name="qty_prod" id="qty_prod" class="form-control input-sm" value="<?php echo $qty_prod ?>" />
					</div>  
				</div>
			</div>		
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<div class="col-md-12 col-xs-12">
					<div class="col-xs-4"><label>Mau Waste Apa ?</label></div>
					<div class="col-xs-4">
						<input type="radio" id="rm" name="waste?[]" value="rm"> Bahan Baku 
					</div> 
					<div class="col-xs-4">
						<input type="radio" id="fg" name="waste?[]" value="fg"> Barang Jadi 
					</div>  
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<div class="col-md-12 col-xs-12">
					<div class="col-xs-4"><label>Jenis Waste Apa?</label></div>
					<div class="col-xs-4">
						<input type="radio" id="data" name="jenis?[]" value="d"> Data 
					</div> 
					<div class="col-xs-4">
						<input type="radio" id="fisik" name="jenis?[]" value="f"> Fisik
					</div>  
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12 col-xs-12">
				<div class="col-md-12">
					<span id="ket_waste" style="font-size:12px" ></span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			<div class="col-md-12">
				<div class="col-md-12 col-xs-12">
					<div class="col-xs-4"><label>Qty1 Waste</label></div>
					<div class="col-xs-8">
						<input type="text" name="in_qty1" id="in_qty1" class="form-control input-sm" readonly="readonly" />
						<input type="hidden" name="in_qty11" id="in_qty11" class="form-control input-sm" readonly="readonly" />
					</div>  
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<div class="col-md-12 col-xs-12">
					<div class="col-xs-4"><label>Qty2 Waste</label></div>
					<div class="col-xs-8">
						<input type="text" name="in_qty2" id="in_qty2" class="form-control input-sm" readonly="readonly" />
						<input type="hidden" name="in_qty22" id="in_qty22" class="form-control input-sm" readonly="readonly" />
					</div>  
				</div>
			</div>
		</div>
	</div>


	<div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->           
            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1_modal" data-toggle="tab">Waste Details</a></li>
            </ul>
            <div class="tab-content"><br>
                <div class="tab-pane active" id="tab_1_modal">
                	<div class="col-xs-12 table-responsive">
                	<table class="table table-condesed table-hover table-responsive rlstable" id="tbl_produksi_waste"> 
						<thead>
						    <tr>
								<th class="style no">No.</th>
							    <th class="style" style="width: 200px;">Product</th>
								<th class="style" style="width: 180px;">Lot</th>
								<th class="style" ></th>
								<th class="style" style="width: 100px;">Qty</th>
								<th class="style" style="width: 65px;">uom</th>
								<th class="style" style="width: 100px;">Qty2</th>
								<th class="style" style="width: 65px;">uom2</th>
								<th class="style">Reff Note</th>
								<th class="style"></th>
							</tr>
						</thead>
				 	    <tbody>
						</tbody>							
					    <tfoot>
							<tr>
				                <td colspan="4">
				                    <a href="#" onclick="add_new_row_waste()"><i class="fa fa-plus"></i> Tambah Data</a>
				                </td>
				            </tr>
				        </tfoot>				        	
					</table>
				    </div>
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
	            <label style="font-size: 15px; color: #5F9EA0">Konsumsi Bahan</label>
		    </div>
		</div>		
	</div>	
	<div class="row">
		<div class="col-md-12">		
			<div class="col-xs-12 table-responsive">	
		    <table class="table table-condesed table-hover table-responsive rlstable" id="tabel_konsumsi"> 
				<thead>
				    <tr>
						<th class="style no">No.</th>
					    <th class="style">Kode Produk</th>
					    <th class="style">Product</th>
						<th class="style">Lot</th>
						<th class="style">Grade</th>
						<th class="style" style="text-align: right;">Qty</th>
						<th class="style">uom</th>
						<th class="style">Qty dikonsumsi</th>
						<th class="style" style="text-align: right;">Qty2</th>
						<th class="style">uom2</th>
						<th class="style">Qty2 dikonsumsi</th>
					</tr>
				</thead>
		 	    <tbody>
		 	    	<?php 
		 	    	$i = 1;
					$row_materials = false;
					$total_qty1 = 0;
					$total_qty2 = 0;
		 	    	foreach ($konsumsi as $row) {
						$row_materials = true;
		 	    	?>
					<tr>
						<td><?php echo $i;?></td>
						<td><?php echo $row->kode_produk;?></td>
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
							<input type="hidden" name="grade"  id="grade" class="form-control input-sm grade" value="<?php echo $row->nama_grade;?>">
							<input type="hidden" name="grade_foreach"  id="grade_foreach" class="form-control input-sm grade_foreach" value="<?php foreach($list_grade as $list){  if($list->nama_grade == $row->nama_grade) {echo "<option selected>".$list->nama_grade."</option>"; }else{echo "<option>".$list->nama_grade."</option>";}}?>">
							<input type="hidden" name="grade"  id="type" class="form-control input-sm type" value="<?php echo $row->type ?>">
							<input type="hidden" name="lbr_greige"  id="lbr_greige" class="form-control input-sm lbr_greige" value="<?php echo $row->lebar_greige ?>">
							<input type="hidden" name="uom_lbr_greige"  id="uom_lbr_greige" class="form-control input-sm uom_lbr_greige" value="<?php echo $row->uom_lebar_greige ?>">
							<input type="hidden" name="lbr_jadi"  id="lbr_jadi" class="form-control input-sm lbr_jadi" value="<?php echo $row->lebar_jadi ?>">
							<input type="hidden" name="uom_lbr_jadi"  id="uom_lbr_jadi" class="form-control input-sm uom_lbr_jadi" value="<?php echo $row->uom_lebar_jadi ?>">
							<input type="hidden" name="sales_order"  id="sales_order" class="form-control input-sm sales_order" value="<?php echo $row->sales_order ?>">
							<input type="hidden" name="sales_group"  id="sales_group" class="form-control input-sm sales_group" value="<?php echo $row->sales_group ?>">

						</td>
						<td align="right"><?php echo $row->qty2?></td>
						<td><?php echo $row->uom2;?></td>
						<td><input type="text" name="qty2_konsum"  id="qty2_konsum" class="form-control input-sm qty2_konsum" data-index="<?php echo $i;?>"></td>						
					</tr>
					<?php
					 $i++; 
					 $total_qty1 = $total_qty1 + $row->qty;
					 $total_qty2 = $total_qty2 + $row->qty2;
					}
					$total_lot = $i-1;
					if($row_materials == false){
						echo "<tr>";
						echo "<td colspan='11' align='center'>Tidak Ada Data";
						echo "</td>";
						echo "</tr>";
					}else{
						echo "<tr>";
						echo "<td colspan='3' align='center'><b>Total</b></td>";
						echo "<td><b>".$total_lot." Lot</b></td>";
						echo "<td></td>";
						echo "<td align='right'><b>".number_format($total_qty1,2)." </b></td>";
						echo "<td></td>";
						echo "<td><input type='text' class='form-control input-sm' name='total_qty1_konsum' id='total_qty1_konsum' readonly></td>";
						echo "<td align='right'><b>".number_format($total_qty2,2)." </b></td>";
						echo "<td></td>";
						echo "<td><input type='text' class='form-control input-sm' name='total_qty2_konsum' id='total_qty2_konsum' readonly></td>";
						echo "<td></td>";
						echo "</tr>";
					}
					?>
				</tbody>
			</table>
		    </div>
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
	.error_target{
		border: 1px solid red;
		color : red
	}
	/*switch poisition button*/
/* 	.modal-footer button {
		float:right;
		margin-left: 10px;
	} */
</style>

<script>



	$("#tambah_data .modal-dialog .modal-content .modal-footer").html('<button type="button" id="btn-produksi-waste" class="btn btn-primary btn-sm"> Simpan</button> <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>');


	$("input[name='waste?[]']").unbind();
	$("input[name='waste?[]']").off("change").on("change", function(){
		// alert(this.value);
		var lenBody = $('#tbl_produksi_waste tbody tr ').length;
		now_check = this.value;
		
		if(lenBody > 0){
			bootbox.confirm({
				message: "Jika <b>Mau waste Apa</b> diganti, maka data yang akan di waste akan dihapus !!",
				title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
				buttons: {
					confirm: {
						label: 'Yes',
						className: 'btn-primary btn-sm'
					},
					cancel: {
						label: 'No',
						className: 'btn-default btn-sm'
					},
				},
				callback: function (result) {
					if(result == true){
						$("#tbl_produksi_waste tbody tr").remove(); // remove all row
						checkTampil('waste');
						$('#in_qty1').val('');
						$('#in_qty11').val('');
						$('#in_qty2').val('');
						$('#in_qty22').val('');
					}else{
						if(now_check == 'rm'){
							$('#fg').prop("checked", true);
						}else{
							$('#rm').prop("checked", true);
						}

					}
				}
			});
		}else{
			checkTampil('waste');
		}
		
	});

	// validasi rm
	$("input[name='jenis?[]']").unbind();
	$("input[name='jenis?[]']").off("change").on("change", function(){
		var lenBody = $('#tbl_produksi_waste tbody tr ').length;
		now_check = this.value;

		radio_waste = get_radio_waste();

		if(radio_waste != ''){

			if(lenBody > 0){

				bootbox.confirm({
						message: "Jika <b>Jenis waste</b> diganti, maka data yang akan di waste akan dihapus !!",
						title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
						buttons: {
							confirm: {
								label: 'Yes',
								className: 'btn-primary btn-sm'
							},
							cancel: {
								label: 'No',
								className: 'btn-default btn-sm'
							},
						},
						callback: function (result) {
							if(result == true){
								$("#tbl_produksi_waste tbody tr").remove(); // remove all row
								checkTampil('jenis');
								$('#in_qty1').val('');
								$('#in_qty11').val('');
								$('#in_qty2').val('');
								$('#in_qty22').val('');
							}else{
								if(now_check == 'd'){
									$('#fisik').prop("checked", true);
								}else{
									$('#data').prop("checked", true);
								}

							}
						}
				});
			}else{
				checkTampil('jenis');
			}
		}
	
	});

	// if change qty konsum condtion waste=fg and jenis=f
	$(".qty_konsum").on("keyup", function(){

		radio_waste = get_radio_waste();
		radio_jenis = get_radio_jenis();
		if(radio_waste == 'fg' && radio_jenis == 'f'){
			var qty2_konsum_new = 0;
			var qty2			= $(this).parents("tr").eq(0).find(".qty2").val();
			var qty_smi			= $(this).parents("tr").eq(0).find(".qty_smi").val();
			var qty_konsum 		= $(this).val();

			if(parseFloat(qty_konsum) > qty_smi){
				qty_konsum_ = qty_smi;
				alert_bootbox("Qty Dikonsumsi tidak boleh lebih dari Qty");
				$(this).val(0);
			}else{
				qty_konsum_ = qty_konsum;
			}
			// total qty 2 baru setalh qty1 dirubah
			hitung_qty2_new = (qty2/qty_smi)*qty_konsum_;
			if(hitung_qty2_new > qty2 ){
				hitung_fix      = qty2;
			}else{
				hitung_fix      = hitung_qty2_new.toFixed(2);
			}
			$(this).parents("tr").eq(0).find(".qty2_konsum").val(hitung_fix);

			// total bahan baku yg akan dikonsumsi
			var total_qty1_konsum = 0;
			$('.qty_konsum').each(function(index,item){		
				var qty1   = $(item).val();
				if(qty1!= ''){
					qty1   = $(item).val();
				}else{
					qty1   = 0;
				}
				total_qty1_konsum = total_qty1_konsum+parseFloat(qty1);
			});
			document.getElementById('total_qty1_konsum').value = formatNumber(total_qty1_konsum.toFixed(2));
		}
		
	});

	// if change qty konsum condtion waste=fg and jenis=f
	$(".qty2_konsum").on("keyup", function(){
		radio_waste = get_radio_waste();
		radio_jenis = get_radio_jenis();
		if(radio_waste == 'fg' && radio_jenis == 'f'){
			var qty2			= $(this).parents("tr").eq(0).find(".qty2").val();
			var qty2_konsum 	= $(this).val();
			var total_qty2_konsum = 0;
			if(parseFloat(qty2_konsum) > qty2){
				alert_bootbox("Qty Dikonsumsi tidak boleh lebih dari Qty");
				$(this).val(0);
			}

			$('.qty2_konsum').each(function(index,item){		
				var qty2   = $(item).val();
				if(qty2!= ''){
					qty2   = $(item).val();
				}else{
					qty2   = 0;
				}
				total_qty2_konsum = total_qty2_konsum+parseFloat(qty2);
			});
			document.getElementById('total_qty2_konsum').value = formatNumber(total_qty2_konsum.toFixed(2));
		}
	});

	function get_qty_by_bom(position,kode_produk,qty2){

		radio_waste = get_radio_waste();
		radio_jenis = get_radio_jenis();

		if(radio_waste == 'rm' && radio_jenis == 'f'){

			// hitung mtr berdasrkan input
			var $row 		= $(position).parents("tr").eq(0);
			$.ajax({
					dataType : 'JSON',
					type     : 'POST',
					url      : '<?php echo base_url();?>manufacturing/mO/get_qty_by_produk',
					data     : {kode_produk:kode_produk, qty2:qty2 },
					success  :function(data){
						$row.find('.wtxtqty').val(data.qty);
						total_waste('','');
					},error  :function(xhr, ajaxOptions, thrownError){
						//alert('error data');
						//alert(xhr.responseText);
					}
			});
		}
	}

	function get_radio_waste(){

		var radio_waste= $('input[name="waste?[]"]').map(function(e, i) {
					if(this.checked == true){
						return i.value;
					}
		}).get();

		return radio_waste;
	}

	function get_radio_jenis(){

		var radio_jenis= $('input[name="jenis?[]"]').map(function(e, i) {
					if(this.checked == true){
						return i.value;
					}
		}).get();

		return radio_jenis;
	}

	function formatNumber(n) {
		return new Intl.NumberFormat('en-US').format(n);
	}


	function checkTampil(show){
		var radio_waste= get_radio_waste();
		var radio_jenis= get_radio_jenis();
		var qty_konsum = false;

      	if(show == 'waste'){
			if(radio_waste == 'rm'){
				$('input[name="jenis?[]"]').prop("disabled", true);
				$('#fisik').prop("checked", true);
				ket_waste = " <b> <font style=color:red> *Waste Fisik  Bahan Baku,  </font> jika masih terdapat bahan baku maka akan terpotong/split oleh sistem ketika di Simpan !</b>";
				$("#ket_waste").html(ket_waste);
				$('.qty_konsum').each(function(index,item){
					qty_konsum = true;
					$(this).parents("tr").eq(0).find(".qty_konsum").val('');
					$(this).parents("tr").eq(0).find(".qty_konsum").attr('readonly',true);
					$(this).parents("tr").eq(0).find(".qty2_konsum").val('');
					$(this).parents("tr").eq(0).find(".qty2_konsum").attr('readonly',true);
					$(this).parents("tr").eq(0).find(".qty_konsum").removeClass('error');
					$(this).parents("tr").eq(0).find(".qty2_konsum").removeClass('error');
				});
			}else{ // fg
				$('input[name="jenis?[]"]').prop("checked", false);
				$('#fisik').prop("disabled", false).attr('id','fisik');
				$('#data').prop("disabled", false).attr('id','data');
				$("#ket_waste").html('');
				$('.qty_konsum').each(function(index,item){
					qty_konsum = true;
					$(this).parents("tr").eq(0).find(".qty_konsum").val('');
					$(this).parents("tr").eq(0).find(".qty2_konsum").val('');
					$(this).parents("tr").eq(0).find(".qty_konsum").removeClass('error');
					$(this).parents("tr").eq(0).find(".qty2_konsum").removeClass('error');
				});
			}
			if(qty_konsum){
				document.getElementById('total_qty1_konsum').value = '';
				document.getElementById('total_qty2_konsum').value = '';
			}
		}else{

			var disable = false;

			if(radio_waste == 'fg'){
				if(radio_jenis == 'd'){// data
					value = '';
					disable = true;
					value2  = '';
					ket_waste = " <b> <font style=color:red> *Waste Data  Barang Jadi,  </font> tidak bisa mengkonsumsi Bahan Baku !</b>";
					$("#ket_waste").html(ket_waste);
				}else{
					value 	= '';// rencana dihitungin ketika ada qty1 
					value2  = '';
					disable = false;
					$("#ket_waste").html('');
				}
			}else{

				$("#ket_waste").html('');
				value 	= ''; 
				disable = true;
				value2  = '';

			}

			$('.qty_konsum').each(function(index,item){
				qty_konsum = true;
				if(disable == true){
					$(this).parents("tr").eq(0).find(".qty_konsum").val(value)
					$(this).parents("tr").eq(0).find(".qty_konsum").attr('readonly',true);
					$(this).parents("tr").eq(0).find(".qty2_konsum").val(value2)
					$(this).parents("tr").eq(0).find(".qty2_konsum").attr('readonly',true);				
				}else{
					$(this).parents("tr").eq(0).find(".qty_konsum").val(value);
					$(this).parents("tr").eq(0).find(".qty_konsum").attr('readonly',false);
					$(this).parents("tr").eq(0).find(".qty2_konsum").val(value2)
					$(this).parents("tr").eq(0).find(".qty2_konsum").attr('readonly',false);
				}
							
			});
			if(qty_konsum){
				document.getElementById('total_qty1_konsum').value = '';
				document.getElementById('total_qty2_konsum').value = '';
			}

		}

	}

	// hapus row tabel waste
	function delRow_waste(r){
		// get data before delete
		kode_produk_del  = $(r).parents("tr").eq(0).find(".wproduk").val();
		lot_del 		 = $(r).parents("tr").eq(0).find(".wtxtlot").val();
		
		var radio_waste= get_radio_waste();
		var radio_jenis= get_radio_jenis();
		
		var i = r.parentNode.parentNode.rowIndex;
		document.getElementById("tbl_produksi_waste").deleteRow(i);

		if(radio_waste == 'rm'){
			if(radio_jenis == "f"){
				total_waste2(kode_produk_del,lot_del);// untuk fungsi delrow 
			}else{
				total_waste(r,'');
			}
		}else{
			total_waste(r,'');
		}
		
	}

	//fungsi panggil tambah_baris() ketika enter di qty
	function enter_waste(e){
		if(e.keyCode === 13){
	        e.preventDefault(); 
	        add_new_row_waste(); //panggil fungsi tambah baris
	    }
	}

	//validasi qty
	function validAngka2(a){
	    if(!/^[0-9.]+$/.test(a.value)){
	        a.value = a.value.substring(0,a.value.length-1000);
	    }
	}

	//validasi qty
	function validAngka_waste(a,name){
	    if(!/^[0-9.]+$/.test(a.value)){
	        a.value = a.value.substring(0,a.value.length-1000);
	    }
		total_waste(a,name);
	}

	function total_waste2(kode_produk, lot){
		var radio_waste	= get_radio_waste();
		var radio_jenis	= get_radio_jenis();

		if(radio_waste == 'rm'){

			//kosongkan value qty1,qty2
			$('.qty_konsum').each(function(index,value){
				kode_produk_cek = $(this).parents("tr").eq(0).find(".kode_produk").val();
				lot_rm_cek 		= $(this).parents("tr").eq(0).find(".lot").val();
				if(kode_produk == kode_produk_cek && lot == lot_rm_cek ){
					$(value).val('');
					$(this).parents("tr").eq(0).find(".qty2_konsum").val('');
				}
			})

			total_input_waste();
			total_rm_waste();
		}


	}

	function total_input_waste(){

		var qty = 0;
		var qty = document.getElementsByName('wtxtqty');
		inx_qty  = qty.length-1;
		var tot_in_qty = 0;
		var qty_isi	   = 0;
		for(var i=0; i<=inx_qty; i++){
			if(qty[i].value!=''){
				qty_isi = qty[i].value;
			}else{
				qty_isi = 0;
			}
			tot_in_qty = tot_in_qty + parseFloat(qty_isi);
		}	

		document.getElementById('in_qty1').value = tot_in_qty.toFixed(2);
		document.getElementById('in_qty11').value = tot_in_qty.toFixed(2);

		var qty2     = document.getElementsByName('wtxtqty2');
		inx_qty2 	 = qty2.length-1;
		var tot_in_qty2 = 0;
		var qty2_isi 	= 0;
		for(var i=0; i<=inx_qty; i++){
			if(qty2[i].value!=''){
				qty2_isi = qty2[i].value;
			}else{
				qty2_isi = 0;
			}
			tot_in_qty2 = tot_in_qty2 + parseFloat(qty2_isi);
		}

		document.getElementById('in_qty2').value = tot_in_qty2.toFixed(2);
		document.getElementById('in_qty22').value = tot_in_qty2.toFixed(2);
		document.getElementById('in_qty1').value = formatNumber(tot_in_qty.toFixed(2));
		document.getElementById('in_qty2').value = formatNumber(tot_in_qty2.toFixed(2));
	}

	function total_rm_waste(){

		var total_qty1_konsum = 0;
		var total_qty2_konsum = 0;
		var qty_konsum_show   = false;
		$('.qty_konsum').each(function(index,item){	
			qty_konsum_show = true;
			qty_konsum 		= $(item).val();
			if(qty_konsum != '')	{
				qty_konsum 		= $(item).val();
			}else{
				qty_konsum 		= 0;
			}

			var qty2_konsum		= $(this).parents("tr").eq(0).find(".qty2_konsum").val();
			if(qty2_konsum != '')	{
				qty2_konsum 		= $(this).parents("tr").eq(0).find(".qty2_konsum").val();
			}else{
				qty2_konsum 		= 0;
			}

			total_qty1_konsum = total_qty1_konsum + parseFloat(qty_konsum);
			total_qty2_konsum = total_qty2_konsum + parseFloat(qty2_konsum);
		});
		if(qty_konsum_show){
			document.getElementById('total_qty1_konsum').value = formatNumber(total_qty1_konsum.toFixed(2));
			document.getElementById('total_qty2_konsum').value = formatNumber(total_qty2_konsum.toFixed(2));
		}

	}


	function total_waste(get,name){

		var radio_waste= get_radio_waste();
		var radio_jenis= get_radio_jenis();

		if(radio_waste == 'fg'){

			total_input_waste();

			var qty_inp  = $('#in_qty11').val();
			var qty_prod = $('#qty_prod').val();
			var qty_konsum_show   = false;
			if(qty_prod != '0' && radio_jenis == 'f' ){
				var total_qty1_konsum = 0;
				var total_qty2_konsum = 0;
				$('.qty_konsum').each(function(index,item){	
					qty_konsum_show = true;	
					var qty_konsum 	= $(item).val();
					var qty_smi		= $(this).parents("tr").eq(0).find(".qty_smi").val();
					var qty_rm 		= $(this).parents("tr").eq(0).find(".qty_rm").val();
					var jml_produk 	= $(this).parents("tr").eq(0).find(".jml_produk").val();
					var qty2 		= $(this).parents("tr").eq(0).find(".qty2").val();
					var hitung 		= (qty_rm/qty_prod)*qty_inp;
					//var sum_con = (hitung/qty_rm)*qty_smi;//hitung qty dikonsumsi
					var sum_con 	= (hitung/jml_produk);//hitung qty dikonsumsi
					// alert(sum_con)
					if(sum_con > qty_smi){
						$(item).eq(0).val(qty_smi);
						if(qty_smi == '' || qty_smi == 0){
							hitung2 = 0;	
						}else{
							hitung2 =  (qty2/qty_smi)*qty_smi;
						}
						hitung2_fix = hitung2.toFixed(2);
						$(this).parents("tr").eq(0).find(".qty2_konsum").val(hitung2_fix);
						tot_qty1 = qty_smi;
						tot_qty2 = hitung2_fix;
					}else{
						var sum_con_fix = sum_con.toFixed(2);				
						// var sum_con_fix = sum_con;		
						$(item).eq(0).val(sum_con_fix);
						if(qty_smi == '' || qty_smi == 0){
							hitung2 = 0;	
						}else{
							hitung2 =  (qty2/qty_smi)*sum_con_fix;
						}
						hitung2_fix = hitung2.toFixed(2);
						$(this).parents("tr").eq(0).find(".qty2_konsum").val(hitung2_fix);
						//  console.log("qty2 = ("+qty2+"/"+qty_smi+")*"+sum_con_fix);

						tot_qty1 = sum_con_fix;
						tot_qty2 = hitung2_fix;
					}
					total_qty1_konsum = total_qty1_konsum + parseFloat(tot_qty1);
					total_qty2_konsum = total_qty2_konsum + parseFloat(tot_qty2);
				});
				if(qty_konsum_show){
					document.getElementById('total_qty1_konsum').value = formatNumber(total_qty1_konsum.toFixed(2));
					document.getElementById('total_qty2_konsum').value = formatNumber(total_qty2_konsum.toFixed(2));
				}
			}
		

		}else{ // rm

			total_input_waste();

			kode_produk_value  = $(get).parents("tr").eq(0).find(".wproduk").val();
			qty_value  		   = $(get).parents("tr").eq(0).find(".wtxtqty2").val();

			if(name=="wtxtqty2"){// jika yg diubah nya qty2
				get_qty_by_bom(get,kode_produk_value,qty_value);
			}

			if(radio_jenis == 'f'){

				var arr_tmp_lot 	= [];
				var arr_tmp_lot_2 	= [];
				var waste_produk  	= false;

				// masih bug ketika lot yang akan di waste double
				$('.wtxtlot').each(function(index,value){
					waste_produk  = true;
					if($(this).data('select2')){
						lot_waste = $(value).data('select2').val();
					}else{
						lot_waste = $(value).val();
					}
					kode_produk_waste = $(this).parents("tr").eq(0).find(".wproduk").val();
					qty1_waste_input = $(this).parents("tr").eq(0).find(".wtxtqty").val();
					// alert(kode_produk_waste);

					if(qty1_waste_input != ''){
						arr_tmp_lot.push({kode_produk_waste:kode_produk_waste, lot_waste:lot_waste, qty1_waste:qty1_waste_input});
					}

					var arr_tmp_lot_rm= [];
					var sisa          = false;
					var loop 		  = 1;
					$('.qty_konsum').each(function(index,item){		
						qty_konsum = $(item).val();
						// console.log("cek awal "+loop+" =  "+qty_konsum);
						
						if(qty_konsum == ""){
							qty_konsum = 0;
						}else{
							qty_konsum = parseFloat($(item).val());
						}
						kode_produk = $(this).parents("tr").eq(0).find(".kode_produk").val();
						lot_rm 	= $(this).parents("tr").eq(0).find(".lot").val();
						qty1_smi = $(this).parents("tr").eq(0).find(".qty_smi").val();

						if(kode_produk_waste == kode_produk){

							if(lot_waste == lot_rm){ // jika lot yg akan di waste = lot konsumsi bahan baku
								qty1_waste_tmp = 0;
								$.each(arr_tmp_lot, function(index,isi){// cek jika terdapat lot yang akan diwaste nya sama 
									if(arr_tmp_lot[index].kode_produk_waste == kode_produk && arr_tmp_lot[index].lot_waste == lot_rm){

										$.each(arr_tmp_lot_rm, function(index,isi){
											if(arr_tmp_lot_rm[index].kode_produk_waste == kode_produk && arr_tmp_lot_rm[index].lot_waste == lot_rm){
												if(sisa == true){
													qty1_waste_tmp = qty1_waste_tmp + parseFloat(arr_tmp_lot_rm[index].qty1_waste_sisa);
												}else{
													qty1_waste_tmp = parseFloat(arr_tmp_lot_rm[index].qty1_waste_sisa);
												}

											}else{
												qty1_waste_tmp = qty1_waste_tmp + parseFloat(arr_tmp_lot[index].qty1_waste);
											}
										});

										if(arr_tmp_lot_rm.length == 0){
											if(sisa == true){
												qty1_waste_tmp = 0;
											}else{
												qty1_waste_tmp = qty1_waste_tmp + parseFloat(arr_tmp_lot[index].qty1_waste);
											}
										}
									}
									// alert('masuk');
								});

								if(parseFloat(qty1_waste_tmp) > parseFloat(qty1_smi)){ //split
									qty_konsum_new = qty1_waste_tmp-qty1_smi;
									if(qty1_waste_tmp > parseFloat(qty1_smi) ){
										$(item).eq(0).val(qty1_smi);
										qty1_sisa = qty1_waste_tmp - qty1_smi;
										// console.log("cek LOOP if "+loop+" =  "+qty1_sisa+" -> "+qty_konsum);
									}else{
										$(item).eq(0).val((qty_konsum_new.toFixed(2)));
										qty1_sisa = qty1_waste_tmp - qty_konsum_new;
										// console.log("cek LOOP else "+loop+" =  "+qty_konsum_new+" -> "+qty_konsum);
									}
									arr_tmp_lot_rm.push({kode_produk_waste:kode_produk_waste, lot_waste:lot_waste, qty1_waste_sisa:qty1_sisa});
								}else{
									if(parseFloat(qty1_waste_tmp) > parseFloat(qty1_smi) ){
										$(item).eq(0).val(qty1_smi);
									}else{
										sisa = true;
										$(item).eq(0).val((qty1_waste_tmp.toFixed(2)));
										arr_tmp_lot_rm = [];
									}
									// console.log("cek2 LOOP "+loop+" =  "+qty1_waste_tmp);
								}
								loop++;
								
							}

						}

					});

					qty2_waste_input = $(this).parents("tr").eq(0).find(".wtxtqty2").val();
					if(qty2_waste_input != ''){
						arr_tmp_lot_2.push({kode_produk_waste:kode_produk_waste, lot_waste:lot_waste, qty2_waste:qty2_waste_input});
					}
					var arr_tmp_lot_rm_qty2	= [];
					var sisa_qty2 			= false;

					$('.qty2_konsum').each(function(index,item){	
						qty2_konsum = $(item).val();
						if(qty2_konsum == ""){
							qty2_konsum = 0;
						}else{
							qty2_konsum = parseFloat($(item).val());
						}
						kode_produk = $(this).parents("tr").eq(0).find(".kode_produk").val();
						lot_rm 		= $(this).parents("tr").eq(0).find(".lot").val();
						qty2_smi 	= $(this).parents("tr").eq(0).find(".qty2").val();

						if(kode_produk_waste == kode_produk){
							if(lot_waste == lot_rm){ // jika lot yg akan di waste = lot konsumsi bahan baku

								qty2_waste_tmp = 0;
								$.each(arr_tmp_lot_2, function(index,isi){// cek jika terdapat lot yang akan diwaste nya sama 
									if(arr_tmp_lot_2[index].kode_produk_waste == kode_produk && arr_tmp_lot_2[index].lot_waste == lot_rm){

										$.each(arr_tmp_lot_rm_qty2, function(index,isi){
											if(arr_tmp_lot_rm_qty2[index].kode_produk_waste == kode_produk && arr_tmp_lot_rm_qty2[index].lot_waste == lot_rm){
												if(sisa_qty2 == true){
													qty2_waste_tmp = qty2_waste_tmp + parseFloat(arr_tmp_lot_rm_qty2[index].qty2_waste_sisa);
												}else{
													qty2_waste_tmp = parseFloat(arr_tmp_lot_rm_qty2[index].qty2_waste_sisa);
												}

											}else{
												qty2_waste_tmp = qty2_waste_tmp + parseFloat(arr_tmp_lot_2[index].qty2_waste);
											}
										});
										if(arr_tmp_lot_rm_qty2.length == 0){
											if(sisa_qty2 == true){
												qty2_waste_tmp = 0;
											}else{
												qty2_waste_tmp = qty2_waste_tmp + parseFloat(arr_tmp_lot_2[index].qty2_waste);
											}
										}
									}
								});
								// alert("qty2_waste = "+qty2_waste_tmp)
								
								if(parseFloat(qty2_waste_tmp) > parseFloat(qty2_smi)){ //split
									qty2_konsum_new = (qty2_waste_tmp)-qty2_smi;
									if(qty2_waste_tmp > parseFloat(qty2_smi) ){
										$(item).eq(0).val(qty2_smi);
										qty2_sisa = qty2_waste_tmp - qty2_smi;
									}else{
										$(item).eq(0).val((qty2_konsum_new.toFixed(2)));
										qty2_sisa = qty2_waste_tmp - qty2_konsum_new;
									}
									// alert(qty2_sisa)
									arr_tmp_lot_rm_qty2.push({kode_produk_waste:kode_produk_waste, lot_waste:lot_waste, qty2_waste_sisa:qty2_sisa});
								}else{
									if(parseFloat(qty2_waste_tmp) > parseFloat(qty2_smi) ){
										$(item).eq(0).val(qty2_smi);
									}else{
										sisa_qty2 = true;
										$(item).eq(0).val((qty2_waste_tmp.toFixed(2)));
									}

								}
							}
						}

					});
				});

				if(waste_produk == false){ // clear consume if wasteproduk empty
					$('.qty_konsum').each(function(index,item){	
						$(item).val('');//clear 
					});
				}
			}
			total_rm_waste()

		}
	}


	var tmp_last_counter   = [];

	function add_new_row_waste(){

		var radio_waste= $('input[name="waste?[]"]').map(function(e, i) {
				if(this.checked == true){
					return i.value;
				}
		}).get();

		var radio_jenis= $('input[name="jenis?[]"]').map(function(e, i) {
				if(this.checked == true){
					return i.value;
				}
		}).get();

		if(radio_waste == ""){
			alert_bootbox('Isi terlebih dahulu Mau Waste Apa ? Bahan Baku atau Barang Jadi ');
		}else if(radio_jenis == ""){
			alert_bootbox('Isi terlebih dahulu Jenis Waste Apa ? Data atau Fisik ');
		}else{

		var lot = document.getElementsByName('wtxtlot');
		var inx_lot = lot.length-1;
		var tambah = true;

		//cek Product apa Kosong ?
		$('.wproduk').each(function (index,value) {
			if($(value).val()==null){
				$($(value).data('select2').$container).addClass('error');
				alert_bootbox('Product Waste tidak boleh Kosong !');
				tambah = false;
			}else{
				$($(value).data('select2').$container).removeClass('error');
			}
		});


		//cek lot apa ada yg kosong
		$('.wtxtlot').each(function(index,value){
			if($(value).data('select2')){
				if($(value).val()==null){
					$($(value).data('select2').$container).addClass('error');
					alert_bootbox('Lot Waste tidak boleh kosong !');
					tambah = false;
				}else{
					$($(value).data('select2').$container).removeClass('error');
				}
			}else{
				if($(value).val()==''){
			   	  	$(value).addClass('error'); 
			   	  	tambah = false;
			   	  	alert_bootbox('Lot Waste tidak boleh kosong !');
				}else{
				  	$(value).removeClass('error'); 
				}
			}

		});

		//cek qty apa ada yg kosong
		$('.wtxtqty').each(function(index,value){
			wtxtqty  = $(this).val();
			wtxtqty2 = $(this).parents("tr").eq(0).find(".wtxtqty2").val();
			if((wtxtqty == '' || wtxtqty == 0) && ( wtxtqty2 == '' || wtxtqty2 == 0)) {
				alert_bootbox('Qty atau Qty2 tidak boleh kosong !');
				$(value).addClass('error'); 
				$(this).parents("tr").eq(0).find(".wtxtqty2").addClass('error'); 
				tambah = false;
			}else{
				$(value).removeClass('error'); 
				$(this).parents("tr").eq(0).find(".wtxtqty2").removeClass('error'); 
			}
		});	

		//cek untuk lot double
		var sama = false;
		var arr3 = [];
			
		$(".wtxtlot").each(function(){

			if($(this).data('select2')){
				var isi = $(this).data('select2').val();
				if(arr3.indexOf(isi) == -1){
				    arr3.push(isi);
					$($(this).data('select2').$container).removeClass('error');
				}else{
		  	  		$($(this).data('select2').$container).addClass('error');
		  	  		sama   = true;
					tambah = false;
				}
			}else{
				var isi = $(this).val();
			    if (arr3.indexOf(isi) == -1){
				    arr3.push(isi);
			        $(this).removeClass("error");
			    }else{
			        $(this).addClass("error");
					sama 	= true;
					tambah  = false;
			    }
			}

		});	

		if(sama==true){
			alert_bootbox('Lot Waste ada yang sama !');
			tambah  = false;
		}
				
		if(tambah){
				
			// last_counter_waste1 = (("00" + last_counter_waste).slice(-3));
			// var lot_prefix   	= '<?php echo $lot_prefix_waste;?>';
			// var lot_prefix_next = '';
			// if(lot_prefix && radio_waste == 'fg'){
			// 	lot_prefix_next =lot_prefix+''+last_counter_waste1;
			// }else{
			// 	lot_prefix_next ='';
			// }
			
			// last_counter_waste +=1;
			wtxtqty = "'wtxtqty'";
			wtxtqty2 = "'wtxtqty2'";

		    html='<tr class="num">'
		    + '<td></td>'
		    + '<td width="150px">'
		    +'<select type="text" class="form-control input-sm width-200 wproduk" name="wtxtproduct" id="wtxtproduct"></select>'
		    +'<input type="hidden" name="wtxtnameproduct" id="wtxtnameproduct"  class="form-control input-sm wnameproduct"  readonly="readonly"></td>'

		    + '<td style="min-width:180px !important;"><input type="text" name="wtxtlot"  id="wtxtlot" class="form-control input-sm width-160 wtxtlot"  onkeypress="enter_waste(event);" ></td>'
		    + '<td></td>'
		    + '<td><input type="text" name="wtxtqty"  id="wtxtqty" class="form-control input-sm width-80 wtxtqty" onkeypress="enter_waste(event);" onkeyup="validAngka_waste(this,'+wtxtqty+')"></td>'
		    + '<td><input type="text" name="wtxtuom"  id="wtxtuom" class="form-control input-sm width-80 wtxtuom"   readonly="readonly"></td>'
		    + '<td><input type="text" name="wtxtqty2" id="wtxtqty2" class="form-control input-sm width-80 wtxtqty2" onkeypress="enter_waste(event);"  onkeyup="validAngka_waste(this,'+wtxtqty2+')"></td>'
		    + '<td><input type="text" name="wtxtuom2"  id="wtxtuom2" class="form-control input-sm width-80 wtxtuom2"   readonly="readonly"></td>'
		    + '<td><input type="text" name="wreff_note" id="wreff_note" class="form-control input-sm width-150 " onkeypress="enter_waste(event);"/></td>'
		    + '<td><a onclick="delRow_waste(this);"  href="javascript:void(0)"  data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a></td>'
		    + '</tr>';
		    $('#tbl_produksi_waste tbody').append(html);
			//var prod = $('#wtxtproduct').val();
	        lot[inx_lot+1].focus();
			// total_waste(inx_lot+1);

			
			if(radio_waste == 'rm'){
				$link_wproduk = 'manufacturing/mO/get_list_produk_waste'; // rm
			}else{
				$link_wproduk = 'manufacturing/mO/get_list_produk_waste_fg'; //fg
			}
			
			$('.wproduk').select2({
				allowClear: true,
				placeholder: "",
				ajax:{
						dataType: 'JSON',
						type : "POST",
						url : "<?php echo base_url();?>"+$link_wproduk,
						//delay : 250,
						data : function(params){
							return{
								prod:params.term,
								kode: '<?php echo $kode?>',// kode MO
							};
						}, 
						processResults:function(data){
							var results = [];
							$.each(data, function(index,item){
								results.push({
									id:item.kode_produk,
									text:item.nama_produk
								});
							});
							return {
								results:results
							};
						},
						error: function (xhr, ajaxOptions, thrownError){
							//alert('Error data');
							//alert(xhr.responseText);
						}
				}
			});

			// event ketika produk di clear
			$('.wproduk').on('select2:unselecting', function (e) {
				var oldProduk = $(this).val();
				var oldLot    = $(this).parents("tr").find("#wtxtlot").val(); 
				$('.qty_konsum').each(function(index,item){		
						lot_rm 			= $(this).parents("tr").eq(0).find(".lot").val();
						kode_produk_rm 	= $(this).parents("tr").eq(0).find(".kode_produk").val();
						if(oldProduk == kode_produk_rm){
							if(oldLot == lot_rm){ // jika lot yg akan di waste = lot konsumsi bahan baku
								$(item).eq(0).val('');// clear qty konsum
								$(this).parents("tr").eq(0).find(".qty2_konsum").val(''); // clear qty2 konsum
							}
						}
				});
				total_input_waste();
				total_rm_waste();
			});

			// get produk before select
			$('.wproduk').on('select2:selecting', function (e) {
				var oldProduk = $(this).val();
				var oldLot    = $(this).parents("tr").find("#wtxtlot").val(); 
				$('.qty_konsum').each(function(index,item){		
						lot_rm 			= $(this).parents("tr").eq(0).find(".lot").val();
						kode_produk_rm 	= $(this).parents("tr").eq(0).find(".kode_produk").val();
						if(oldProduk == kode_produk_rm){
							if(oldLot == lot_rm){ // jika lot yg akan di waste = lot konsumsi bahan baku
								$(item).eq(0).val('');// clear qty konsum
								$(this).parents("tr").eq(0).find(".qty2_konsum").val(''); // clear qty2 konsum
							}
						}
				});
				total_input_waste();
				total_rm_waste();
			});

			
			$(".wproduk").change(function(){
				
				kode_produk = '<?php echo $kode_produk ?>'; // kode_produk jadi
				kode_produk_select =  $(this).parents("tr").eq(0).find("#wtxtproduct").val();
				var rowIndex = this.parentNode.parentNode.rowIndex;
				var $row     = $(this).parents("tr").eq(0);
				//get nama produk waste by kode produk
				$.ajax({
					dataType : 'JSON',
					type     : 'POST',
					url      : '<?php echo base_url();?>manufacturing/mO/get_nama_produk_waste',
					data     : {kode_produk:kode_produk_select},
					success  :function(data){
							$row.find('.wnameproduct').val(data.nama_produk);
							$row.find('.wtxtuom').val(data.uom_1);
							$row.find('.wtxtuom2').val(data.uom_2);
							$row.find('.wtxtqty').val('');
							$row.find('.wtxtqty2').val('');
							total_input_waste();
							total_rm_waste();

					},error  :function(xhr, ajaxOptions, thrownError){
						//alert('error data');
						//alert(xhr.responseText);
					}
				});

				
				if(radio_waste == 'rm'){
					
					var replace = '<select id="wtxtlot" name="wtxtlot" class="form-control input-sm wtxtlot"></select>';
					$('#tbl_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(3) ').html(replace);

					// untuk select lot bahan baku
					$('#tbl_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(3) .wtxtlot').select2({

						//dropdownParent: $("#tambah_data"),
						allowClear: true,
						placeholder: "",
						//tags: true,
						ajax:{
								dataType: 'JSON',
								type : "POST",
								url  : "<?php echo base_url();?>manufacturing/mO/get_list_lot_waste_by_produk",
								data : function(params){
									return{
										prod:params.term,
										kode: '<?php echo $kode?>',// kode MO
										kode_produk :$(this).parents("tr").eq(0).find("#wtxtproduct").val(),
									};
								}, 
								processResults:function(data){
									var results = [];
									$.each(data, function(index,item){
										results.push({
											id:item.lot,
											text:item.lot
										});
									});
									return {
										results:results
									};
								},
								error: function (xhr, ajaxOptions, thrownError){
									// alert('Error data');
									// alert(xhr.responseText);
								}
						}
					});

					$(".wtxtlot").on('change', function () {
						if($(this).data('select2')){
							$(this).parents("tr").eq(0).find(".wtxtqty").val('');
							$(this).parents("tr").eq(0).find(".wtxtqty2").val('');
							// cek apa terdapat lot rm yang sama 
							var sama 		= false;
							var arr3 		= [];
							var clear_lot 	= $(this);
								
							$(".wtxtlot").each(function(){

								if($(this).data('select2')){
									var isi = $(this).data('select2').val();
									if(arr3.indexOf(isi) == -1){
										arr3.push(isi);
										$($(this).data('select2').$container).removeClass('error');
									}else{
										$($(this).data('select2').$container).addClass('error');
										sama   = true;
										clear_lot.val("").trigger('change');
									}
								}
							});	

							if(sama==true){
								alert_bootbox('Lot Sudah pernah dipilih !');
							}
							total_input_waste();
							total_rm_waste();
						}
					});

					// event ketika lot di clear
					$('.wtxtlot').on('select2:unselecting', function (e) {
						var oldLot 		 = $(this).val();
						var oldProduk    = $(this).parents("tr").find("#wtxtproduct").val(); 
						$('.qty_konsum').each(function(index,item){		
								lot_rm 			= $(this).parents("tr").eq(0).find(".lot").val();
								kode_produk_rm 	= $(this).parents("tr").eq(0).find(".kode_produk").val();
								if(oldProduk == kode_produk_rm){
									if(oldLot == lot_rm){ // jika lot yg akan di waste = lot konsumsi bahan baku
										$(item).eq(0).val('');// clear qty konsum
										$(this).parents("tr").eq(0).find(".qty2_konsum").val(''); // clear qty2 konsum
									}
								}
						});
					});

					// get lot sebelumnya ketika akan dipilih
					$('.wtxtlot').on('select2:selecting', function (e) {
						var oldLot 		 = $(this).val();
						var oldProduk    = $(this).parents("tr").find("#wtxtproduct").val(); 
						$('.qty_konsum').each(function(index,item){		
								lot_rm 			= $(this).parents("tr").eq(0).find(".lot").val();
								kode_produk_rm 	= $(this).parents("tr").eq(0).find(".kode_produk").val();
								if(oldProduk == kode_produk_rm){
									if(oldLot == lot_rm){ // jika lot yg akan di waste = lot konsumsi bahan baku
										$(item).eq(0).val('');// clear qty konsum
										$(this).parents("tr").eq(0).find(".qty2_konsum").val(''); // clear qty2 konsum
									}
								}
						});
					});

				}else{
					// var replace = '<input type="text" name="wtxtlot"  id="wtxtlot" class="form-control input-sm wtxtlot" value="'+lot_prefix_next+'" onkeypress="enter_waste(event);">';
					// $('#tabel_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(3) ').html(replace);

					if(radio_waste == 'fg'){
						var deptid 				= "<?php echo $deptid?>";
						var lot_prefix_waste   	= "<?php echo $lot_prefix_waste;?>";
						if(radio_jenis == 'f'){
							lot_prefix_post  = 'F|'+lot_prefix_waste;
						}else{
							lot_prefix_post  = 'D|'+lot_prefix_waste;
						}

						if(lot_prefix_waste != ''){
							// var lot_prefix_post   	= '<?php echo $lot_prefix_waste;?>';
							var lenRow = $('#tbl_produksi_waste tbody tr ').length;
							if(lenRow <= 1){
								tmp_last_counter    = [];
								$.ajax({
										dataType : 'JSON',
										type     : 'POST',
										url      : '<?php echo base_url();?>manufacturing/mO/get_last_lot_prefix_waste_by_lot',
										data     : {deptid:deptid, lot_prefix:lot_prefix_post},
										success  :function(data){
											lot_counter_waste    = data.counter;
											tmp_last_counter.push(data.counter);
											last_counter_waste1  = (("00" + data.counter).slice(-3));
											lot_prefix_next 	 = lot_prefix_post+''+last_counter_waste1;
											$('#tbl_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(3)').find('.wtxtlot').val(lot_prefix_next);
					
										},error  :function(xhr, ajaxOptions, thrownError){
											alert_bootbox('error data');
											//alert(xhr.responseText);
										}
								});
								// last_counter_waste = lot_counter_waste
							}else{
								if(tmp_last_counter.length>0){
									last_counter  		= parseInt(tmp_last_counter[0]) + 1;
									last_counter_waste1 = (("00" + last_counter).slice(-3));
								}else{
									last_counter  		= 1;
									last_counter_waste1 = "001";
								}
								tmp_last_counter    = [];
								lot_prefix_next 	= lot_prefix_post+''+last_counter_waste1;
								$('#tbl_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(3)').find('.wtxtlot').val(lot_prefix_next);
								tmp_last_counter.push(last_counter)

							}
						}else{
							if(radio_jenis == 'f'){
								lot_prefix_next  = 'F|';
							}else{
								lot_prefix_next  = 'D|';
							}
							$('#tbl_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(3)').find('.wtxtlot').val(lot_prefix_next);

						}

					}

				}

			});
			
			
		}
		
		}

	}


	$("#btn-produksi-waste").off("click").on("click",function(e) {
		
			e.preventDefault();
			
			var deptid 		= '<?php echo $deptid?>';//dept id untuk log history
			var kode   		= '<?php echo $kode?>';//kode MO
			var origin_mo 	= '<?php echo $origin_mo ?>';
			var valid 		= true;
			
			// > WASTE DETAILS
			
			var wproduk = false;
			var wtxtlot = false;
			var wproduk_empty = true;
			var qty_waste_empty = false;
			var radio_waste= get_radio_waste();
			var radio_jenis= get_radio_jenis();
	
			if(radio_waste == ''){
				alert_bootbox('Isi terlebih dahulu Mau Waste Apa ? Bahan Baku atau Barang Jadi ');
				valid = false;
			}
	
			if(radio_jenis == ''){
				alert_bootbox('Isi terlebih dahulu Jenis Waste Apa ? Data atau Fisik ');
				valid = false;
			}
	
			//cek Product apa Kosong ?
			$('.wproduk').each(function (index,value) {
				wproduk_empty = false;
				if($(value).val()==null){
					$($(value).data('select2').$container).addClass('error');
					valid = false;
					wproduk = true
				}else{
					$($(value).data('select2').$container).removeClass('error');
				}
			});
	
			if(wproduk_empty == true ){
				alert_bootbox('Tambahkan terlebih dahulu Product yang akan di Waste !  ');
				valid = false;
			}
	
	
			//cek lot apa ada yg kosong
			$('.wtxtlot').each(function(index,value){
	
				if($(value).data('select2')){
					
					if($(value).data('select2').val()==null){
		   	  		    $($(value).data('select2').$container).addClass('error');
						valid = false;
				   	  	wtxtlot = true
					}else{
						$($(value).data('select2').$container).removeClass('error');
					}
					
				}else{
	
					if($(value).val()==''){
				   	  $(value).addClass('error'); 
				   	  valid = false;
				   	  wtxtlot = true
					}else{
					  $(value).removeClass('error'); 
					}
				}
	
			});
	
		 	if(wproduk){
				alert_bootbox('Product Waste tidak boleh Kosong !');
		 	}
	
		 	if(wtxtlot){
		 		alert_bootbox('Lot Waste tidak boleh kosong !');
		 	}
	
			//cek qty apa ada yg kosong
			$('.wtxtqty').each(function(index,value){
				var wtxtqty2 = $(this).parents("tr").eq(0).find(".wtxtqty2").val();
				if(($(value).val()=='' && wtxtqty2 == '') ||($(value).val()==0 && wtxtqty2 == 0) ){
					$(value).addClass('error'); 
					$(this).parents("tr").eq(0).find(".wtxtqty2").addClass('error'); 
					qty_waste_empty = true;
					valid = false;
				}else{
					$(this).parents("tr").eq(0).find(".wtxtqty2").removeClass('error'); 
				  	$(value).removeClass('error'); 
				}
			});	
	
			
			if(qty_waste_empty == true){
				alert_bootbox('Qty atau Qty2 tidak boleh kosong !');
			}
	
			// < WASTE DETAILS
	
			//cek apa qty dikonsumsi melebihi qty stock move items
			var cek_qty = false;
			// var empty_qty = false;
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
			});
	
			if(cek_qty){//jiika qty_dikonsumsi melebihi qty 
				alert_bootbox("Qty dikonsumsi tidak boleh Melebihi Qty !");	
			}
			
			if(radio_waste == 'rm'){

				if(radio_jenis == 'f'){
					var qty_waste = true;
					var waste_rm_valid = true;
					var waste_rm_lot_valid = true;
					var lot_same = true;
					var same     = false;
					var add_error = false;
					$(".wnameproduct").each(function(index, element) {
						if ($(element).val()!=="") {
							kode_produk  = $(element).parents("tr").find("#wtxtproduct").val(),
							lot  = $(element).parents("tr").find("#wtxtlot").val();							
							qty	 = $(element).parents("tr").find("#wtxtqty").val();
							qty2 = $(element).parents("tr").find("#wtxtqty2").val();

							qty_konsum  = '';
							qty2_konsum = '';
							rm_rows  	= false;
							same        = false;
							arr_tmp_lot = [];

							$('.qty_konsum').each(function(index,item){
								rm_rows 		= true;
								kode_produk_rm 	= $(item).parents("tr").find('#kode_produk').val();
								lot_rm  		= $(item).parents("tr").find('#lot').val();
								qty_rm  		= $(item).parents("tr").find('#qty_smi');
								qty_rm_val 		= qty_rm.val();
								qty2_rm 		= $(item).parents("tr").find('#qty2');
								qty2_rm_val 	= qty2_rm.val();
								qty_konsum 		= $(item).parents("tr").find('#qty_konsum');
								qty2_konsum 	= $(item).parents("tr").find('#qty2_konsum');

								if(kode_produk == kode_produk_rm && lot == lot_rm ){
									qty_  = qty ;
									qty2_ = qty2 ;
									$.each(arr_tmp_lot, function(index,isi){
										if(arr_tmp_lot[index].kode_produk == kode_produk_rm && arr_tmp_lot[index].lot == lot_rm){
											qty_  = (arr_tmp_lot[index].qty1);
											qty2_ =  (arr_tmp_lot[index].qty2);

											if(parseFloat(qty_) > parseFloat(qty_rm_val) || parseFloat(qty2_) > parseFloat(qty2_rm_val) ){
												qty_waste  = false;
											}else{
												qty_waste  = true;
											}
										}
									});
									if(parseFloat(qty_) > parseFloat(qty_rm_val) || parseFloat(qty2_) > parseFloat(qty2_rm_val) ){
										qty_waste  = false;
										if(parseFloat(qty_) > parseFloat(qty_rm_val)  ){
											qty1_sisa  = parseFloat(qty_) - parseFloat(qty_rm_val);
										}else{
											qty1_sisa  = 0;
										}
										if(parseFloat(qty2_) > parseFloat(qty2_rm_val)  ){
											qty2_sisa  = parseFloat(qty2_) - parseFloat(qty2_rm_val);
										}else{
											qty2_sisa  = 0;
										}
										arr_tmp_lot.push({kode_produk:kode_produk, lot:lot, qty1:qty1_sisa.toFixed(2), qty2:qty2_sisa.toFixed(2)});
									}

									same = true;
								}else{
									lot_same = false;
								}

							});

							if(rm_rows == true){
								if(qty_waste == false){
									$(element).parents("tr").find("#wtxtqty").addClass('error');
									$(element).parents("tr").find("#wtxtqty2").addClass('error');
									
									add_error = true;
									valid = false;
									waste_rm_valid = false;
									qty_waste = true;
								}else{
									qty_waste = true;
									add_error = false;
									$(element).parents("tr").find("#wtxtqty").removeClass('error');
									$(element).parents("tr").find("#wtxtqty2").removeClass('error');

								}
								
								if(add_error == false){
									if(lot_same == false && same == false){
										$(element).parents("tr").find("#wtxtqty").addClass('error');
										$(element).parents("tr").find("#wtxtqty2").addClass('error');
										valid = false;
										waste_rm_lot_valid = false;
										lot_same = true;
									}else{
										lot_same  = true;
										$(element).parents("tr").find("#wtxtqty").removeClass('error');
										$(element).parents("tr").find("#wtxtqty2").removeClass('error');
									}
								}
							}else{
								valid = false;
								waste_rm_lot_valid = false;
							}

						}
					});

					if(waste_rm_valid == false){
						alert_bootbox("Qty Waste Bahan Baku Fisik tidak boleh lebih dari Qty yg dikonsumsi ! ")
					}

					if(waste_rm_lot_valid == false){
						alert_bootbox("Produk atau Lot  Konsumsi Bahan Baku tidak ada  ! ")
					}
					// alert(rm_rows)
				}

			}

			if(wproduk_empty == false ){
				if(radio_waste == 'fg'){
					if(radio_jenis == 'f'){
						// list waste fg
						var cek_rm = false;
						$(".wnameproduct").each(function(index, element) {
							if ($(element).val()!=="") {
								qty1 = $(element).parents("tr").find("#wtxtqty").val();
								if(qty1 !== "" && qty1 != 0 && qty1 != 0.00 ) {
									cek_rm = true;
								}
							}
						});

						if(cek_rm == true){
							var rm = false;
							$('.qty_konsum').each(function(index,item){
								rm = true;
							});
							if(rm == false){// jika rm tidak ada
								valid = false;
								alert_bootbox("Waste Barang Jadi Fisik harus terdapat Konsumsi Bahan Baku  !!");	
							}
						}
						
					}
				}
			}
	
		    if(valid){
		    
				var i = 0;
				var konsumsi_bahan = false;
				var produk_waste   = false;
	
				var arr = new Array();
				$(".wnameproduct").each(function(index, element) {
					
					if ($(element).val()!=="") {
						arr.push({
							kode_produk:$(element).parents("tr").find("#wtxtproduct").val(),
							nama_produk:$(element).val(),
							lot  :$(element).parents("tr").find("#wtxtlot").val(),							
							qty  :$(element).parents("tr").find("#wtxtqty").val(),
							uom  :$(element).parents("tr").find("#wtxtuom").val(),
							qty2 :$(element).parents("tr").find("#wtxtqty2").val(),
							uom2 :$(element).parents("tr").find("#wtxtuom2").val(),
							reff_note :$(element).parents("tr").find("#wreff_note").val(),
							kode 	  :$("#txtkode").val(),
						});
						produk_waste = true;
					}
				});
					
				var arr2 = new Array();
				var input_rm = false;
				$('.qty_konsum').each(function(index,item){

					qty          = $(item).parents("tr").find('#qty_smi').val();
					qty_konsum   = $(item).parents("tr").find('#qty_konsum').val();

					qty2         = $(item).parents("tr").find("#qty2").val();
					qty2_konsum  = $(item).parents("tr").find('#qty2_konsum').val();

					qty2_row     = $(item).parents("tr").find('#qty2_konsum');
					if(parseFloat(qty) == parseFloat(qty_konsum)){
						if(parseFloat(qty2) > parseFloat(qty2_konsum)){
							input_rm = true
							$(item).addClass('error');
							qty2_row.addClass('error');
						}else{
							$(item).removeClass('error');
							qty2_row.removeClass('error');
						}
					}else{
						$(item).removeClass('error');
						qty2_row.removeClass('error');
					}

					if ((qty_konsum != "" || qty2_konsum != "") && ( qty_konsum != 0 || qty2_konsum ) && (qty_konsum != 0.00  || qty2_konsum != 0.00)) {
						arr2.push({
							kode 		: $("#txtkode").val(),
							qty_konsum  : $(item).parents("tr").find('#qty_konsum').val(),
							qty2_konsum : $(item).parents("tr").find('#qty2_konsum').val(),
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
							grade 		: $(item).parents("tr").find("#grade").val(),
							lbr_greige  : $(item).parents("tr").find('#lbr_greige').val(),
							uom_lbr_greige  : $(item).parents("tr").find('#uom_lbr_greige').val(),
							lbr_jadi    : $(item).parents("tr").find('#lbr_jadi').val(),
							uom_lbr_jadi  : $(item).parents("tr").find('#uom_lbr_jadi').val(),
							sales_order :$(item).parents("tr").find("#sales_order").val(),
							sales_group :$(item).parents("tr").find("#sales_group").val(),
						});				
					
						// alert (JSON.stringify(arr2));
						konsumsi_bahan = true;
					}
				});		
			
				if(produk_waste == false){
					alert_bootbox('Maaf, Produk yang akan di Waste masih Kosong !');
				}else if(input_rm == true){	
					alert_bootbox('Maaf, Inputan Konsumsi Bahan baku tidak Valid !');
				}else{
					please_wait(function(){});
				    $('#btn-produksi-waste').button('loading');
				    $.ajax({
				        dataType: "JSON",
				        url : '<?php echo site_url('manufacturing/mO/save_produksi_waste_modal') ?>',
				        type: "POST",
				        data: { deptid 		: deptid, 
								origin_mo 	: origin_mo,  
								kode 		: kode,
								waste       : radio_waste,
								jenis_waste : radio_jenis,
								kode_produk :$("#txtkode_produk").val(), 
								data_waste 	: JSON.stringify(arr),
								data_rm 	: JSON.stringify(arr2), 
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
				              $('#btn-produksi-waste').button('reset');
				              alert_bootbox(data.message);
							  unblockUI( function(){});
							  if(data.close == 'yes'){
								$('#tambah_data').modal('hide');
							  }
				           	}else{
				              //jika berhasil disimpan
				              $("#status_bar").load(location.href + " #status_bar");
				              $("#tab_1").load(location.href + " #tab_1");
				              $("#tab_2").load(location.href + " #tab_2");             
				              $("#foot").load(location.href + " #foot");
		                 	  $('#tambah_data').modal('hide');
				              $('#btn-produksi-waste').button('reset');
				              if(data.double == 'yes'){
				              	alert_modal_warning(data.message2);
				              }
				              //console.log(data.sql);
				              unblockUI( function(){
								setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
							  });
				            }
							$("#tambah_data .modal-dialog .modal-content .modal-body").removeClass('waste_produksi'); 
							
				        },error: function (jqXHR, textStatus, errorThrown){
						    unblockUI( function(){});
  				            $('#btn-produksi-waste').button('reset');
							if(jqXHR.status == 401){
								var err = JSON.parse(jqXHR.responseText);
								alert(err.message);
							}else{
								alert("Error Simpan Waste !");
							}   
				        }
				    });
				}
			}// if valid true
			
			e.stopImmediatePropagation();
	});



</script>
