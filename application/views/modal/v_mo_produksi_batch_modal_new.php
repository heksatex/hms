<?php 
	if(!empty($row_lot)){//jika lot prefix nya tidak kosong maka dikasih counter
		 $counter =$row_lot;
	}else{
		$counter =$row_lot;
	}

	if(!empty($row_lot_waste)){//jika lot prefix waste nya tidak kosong maka dikasih counter
		$counter_waste =$row_lot_waste;
	}else{
		$counter_waste =0;
	}

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
	<input type="hidden" id="txtkode_produk" value="<?= $kode_produk ?>">
	<input type="hidden" id="txtproduct" value="<?= htmlentities($product) ?>">
	<div class="pb-wrapper">
		<!-- ========================================= -->
		<!-- HEADER -->
		<!-- ========================================= -->
		<div class="pb-header">
			<div class="row">
				<div class="col-md-5">
					<div class="pb-mo"> <?= strtoupper($kode); ?> 
						<div class="pb-machine">
							<i class="fa fa-industry"></i> <?= strtoupper($mesin); ?>
						</div>
					</div>
					<div class="pb-product"> <?= strtoupper($product); ?> </div>
					
				</div>
				<div class="col-md-7">
					<div class="pb-summary-inline">
						<div class="pb-summary-item">
							<span>TARGET</span>
							<strong> <?= number_format($qty_prod,2) ?> </strong>
						</div>
						<div class="pb-summary-item">
							<span>PRODUKSI</span>
							<strong id="pb_total_qty">0.00</strong>
						</div>
						<div class="pb-summary-item">
							<span>SISA</span>
							<strong id="pb_sisa_qty"> <?= number_format($sisa_qty,2) ?> </strong>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- ========================================= -->
		<!-- SUMMARY -->
		<!-- ========================================= -->
		
		<!-- ========================================= -->
		<!-- WORKSPACE -->
		<!-- ========================================= -->
		<div class="row pb-workspace">
			<!-- ===================================================== -->
			<!-- INPUT PRODUKSI -->
			<!-- ===================================================== -->
			<div class="col-lg-4 col-md-4 col-sm-12">
				<div class="pb-card pb-card-left">
				<div class="pb-card-header">
					<i class="fa fa-industry"></i> INPUT PRODUKSI
				</div>
				<div class="pb-card-body pb-input-body">
					<!-- Beam -->
					<div class="pb-form-group">
						<label class="pb-label"> Beam <span class="text-danger">*</span>
						</label>
						<select class="form-control pb-input pb-select2" id="pb_beam">
							<option value="">Pilih Beam</option>
						</select>
					</div>
					<!-- Lot -->
					<div class="pb-form-group">
						<label class="pb-label">Lot</label>
						<input type="text" id="pb_lot" class="form-control pb-input">
					</div>
					<!-- Grade -->
					<div class="pb-form-group">
						<label class="pb-label">Grade</label>
						<select id="pb_grade" class="form-control pb-select2">
							<option value="">Pilih Grade</option> 
							<?php foreach($list_grade as $row): ?>
							<option value="<?= $row->nama_grade ?>" <?= $row->nama_grade=='A'?'selected':'' ?>>
								<?= $row->nama_grade ?>
							</option>
							<?php endforeach; ?>
						</select>
					</div>
					<!-- Qty -->
					<div class="row">
						<div class="col-xs-6">
							<div class="pb-form-group">
							<label class="pb-label">Qty</label>
							<div class="input-group">
								<input type="text" id="pb_qty1" class="form-control pb-input formatAngka text-right" data-decimal="2" value="<?= $qty1_std ?>">
								<span class="input-group-addon"> <?= $uom_1 ?> </span>
							</div>
							</div>
						</div>
						<div class="col-xs-6">
							<div class="pb-form-group">
							<label class="pb-label">Qty2</label>
							<div class="input-group">
								<input type="text" id="pb_qty2" class="form-control pb-input formatAngka text-right"  data-decimal="2" value="<?= $qty2_std ?>">
								<span class="input-group-addon"> <?= $uom_2 ?> </span>
							</div>
							</div>
						</div>
					</div>
					<!-- UA UI W -->
					<div class="row">
						<div class="col-xs-4">
							<div class="pb-form-group">
							<label class="pb-label">UA</label>
							<input type="number" id="pb_ua" class="form-control pb-input">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="pb-form-group">
							<label class="pb-label">UI</label>
							<input type="number" id="pb_ui" class="form-control pb-input">
							</div>
						</div>
						<div class="col-xs-4">
							<div class="pb-form-group">
							<label class="pb-label">W</label>
							<input type="number" id="pb_w" class="form-control pb-input">
							</div>
						</div>
					</div>
					<!-- Reff -->
					<div class="pb-form-group">
						<label class="pb-label"> Reff Note </label>
						<textarea rows="3" id="pb_reff_note" class="form-control"></textarea>
					</div>
				</div>
				<!-- Footer -->
				<div class="pb-card-footer">
					<button type="button" class="btn pb-btn btn-block" id="btnTambahProduksi">
					<i class="fa fa-plus-circle"></i> Tambah Lot </button>
				</div>
				</div>
			</div>
			<!-- ===================================================== -->
			<!-- DAFTAR PRODUKSI -->
			<!-- ===================================================== -->
			<div class="col-lg-8 col-md-8 col-sm-12">
				<div class="pb-card">
				<div class="pb-card-header">
					<i class="fa fa-list"></i> DAFTAR PRODUKSI
				</div>
				<div class="pb-card-body pb-table-wrapper">
					<table class="table table-hover table-bordered pb-table" id="tabel_produksi">
					<thead>
						<tr>
						<th style="width:45px">No</th>
						<th style="width:250px">Lot</th>
						<th style="width:70px">Grade</th>
						<th style="width:95px">Qty1</th>
						<th style="width:95px">Qty2</th>
						<th style="width:80px">Reff Note</th>
						<th style="width:75px">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<tr class="pb-empty">
						<td colspan="7" class="text-center">
							 Belum ada data produksi 
						</td>
						</tr>
					</tbody>
					</table>
				</div>
				<!-- Summary -->
				<div class="pb-summary">
					<div class="row">
						<div class="col-xs-4">
							<strong>Total Lot</strong>
							<h4 id="pb_total_lot">0</h4>
						</div>
						<div class="col-xs-4">
							<strong>Total Qty1</strong>
							<h4>
								<span id="pb_total_qty1">0</span> <?= $uom_1 ?>
							</h4>
						</div>
						<div class="col-xs-4"> 
							<strong>Total Qty2</strong>
							<h4>
								<span id="pb_total_qty2">0</span> <?= $uom_2 ?>
							</h4>
						</div>
					</div>
				</div>
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

</style>

<script type="text/javascript">

	$("#tambah_data .modal-dialog .modal-content .modal-footer").html('<div class="col-md-8"></div><div class="col-md-4"><div class="col-xs-12"><button type="button" id="btn-tambah-produksi-batch" class="btn btn-primary btn-sm"> Simpan</button> <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button></div></div>');
	
	/*********************************
	  START SCRTIPT TABEL PRODUCT LOT
	**********************************/

	//load modal panggil funtion total
	// total();
	// panggil function cek_rm untuk disable enable button salin bahan baku
	cek_rm();

	

	//untuk mentotalkan qty inputan
	// function total(){	
	// 	var qty = 0;
	// 	var qty = document.getElementsByName('txtqty[]');
	// 	inx_qty  = qty.length-1;
	// 	var tot_qty = 0;
	// 	var qty_isi = 0;

	// 	for(var i=0; i<=inx_qty; i++){
	// 		if(qty[i].value!=''){
	// 			qty_isi = qty[i].value;
	// 		}else{
	// 			qty_isi = 0;
	// 		}
	// 		tot_qty = tot_qty + parseFloat(qty_isi);
	// 	}

	// 	// document.getElementById('in_qty').value = tot_qty;

	// 	var qty_inp  = $('#in_qty').val();
	// 	var qty_prod = $('#qty_prod').val();
	// 	var qty_sisa = $('#qty_sisa').val();// sisa awal

	// 	var sisa_target = qty_sisa - tot_qty;// sisa target setelah ada inputan
	// 	var uom_target  = "<?php echo $uom_qty_sisa; ?>";

	// 	document.getElementById('sisa_target').value = sisa_target.toFixed(2)+" "+uom_target;
	// 	document.getElementById('txtQty_sisa').value = sisa_target.toFixed(2)+" "+uom_target;
	// 	if(sisa_target < 0){
	// 		$('#sisa_target').addClass('error_target');
	// 		$('#txtQty_sisa').addClass('error_target');
	// 	}else{
	// 		$('#sisa_target').removeClass('error_target');
	// 		$('#txtQty_sisa').removeClass('error_target');
	// 	}

	// 	if(qty_prod != '0'){
	// 		$('.qty_konsum').each(function(index,item){		
	// 			///hitung  = parseFloat(qty_prod) + parseFloat(qty_inp);
	// 			var qty_smi= $(this).parents("tr").eq(0).find(".qty_smi").val();
	// 			var qty_rm = $(this).parents("tr").eq(0).find(".qty_rm").val();
	// 			var jml_produk = $(this).parents("tr").eq(0).find(".jml_produk").val();
	// 			var hitung = (qty_rm/qty_prod)*qty_inp;
	// 			//var sum_con = (hitung/qty_rm)*qty_smi;//hitung qty dikonsumsi
	// 			var sum_con = (hitung/jml_produk);//hitung qty dikonsumsi
	// 			if(sum_con > qty_smi){
	// 				$(item).eq(0).val(qty_smi);
	// 			}else{
	// 				var sum_con_fix = sum_con.toFixed(2);				
	// 				$(item).eq(0).val(sum_con_fix);
	// 			}
	// 		});
	// 	}
	// }

	function btn_copy(){// enable /disable  btn-copy
		var items = false;
		$(".produk").each(function(index, element) {
			items = true;
		});

		if(items == false){
			$("#btn-copy").prop('disabled',false);
			cek_rm();
		}

	}

	// cek rm 
	function cek_rm(){
		var items = false;
		$('.qty_konsum').each(function(index,item){
			items = true;
		});

		if(items == false){
			$("#btn-copy").prop('disabled',true);
		}

	}

	//hapus row 
	function delRow(r){	  
	    var i = r.parentNode.parentNode.rowIndex;
	  	document.getElementById("tabel_produksi").deleteRow(i);
	  	total();
		btn_copy(); // enable btn-copy
	}

	//fungsi panggil tambah_baris() ketika enter di qty
	function enter(e){
		if(e.keyCode === 13){
	        e.preventDefault(); 
	        tambah_baris(); //panggil fungsi tambah baris
	    }
	}

	//validasi qty
	function validAngka(a){
	    // if(!/^[0-9.]+$/.test(a.value)){
	    //     a.value = a.value.substring(0,a.value.length-1000);
	    // }
		let tmp_char = '';
		let len      = a.value.length;
		let text     = a.value;
		for(let i = 0; i < len; i++){
			if(len > 1){
				var char = text.substring(i,i+1);
			}else{
				var char = text;
			}
			if(/^[0-9.]+$/.test(char)){
				char1 = char; 
				tmp_char += char1;
	    	}else{
				char.replace(/[^0-9.-]/, '')
			}
		}
		// alert(tmp_char);
		a.value = "";
		a.value = tmp_char;

	    total();
	}

  	//set counter produksi lot 
	var last_counter =parseInt('<?php echo $counter;?>');

	//untuk tambah baris
	function tambah_baris(){
		var lot = document.getElementsByName('txtlot[]');
		var inx_lot = lot.length-1;
	
		var tambah = true;

		//cek lot apa ada yg kosong
		$('.txtlot').each(function(index,value){
			if($(value).val()==''){
		   	  alert('Lot tidak boleh kosong !');
		   	  $(value).addClass('error'); 
		   	  tambah = false;
			}else{
			  $(value).removeClass('error'); 
			}
		});	

		//cek lot apa ada yg kosong
		$('.txtqty').each(function(index,value){
			if($(value).val()==''){
		   	  alert('Qty tidak boleh kosong !');
		      $(value).addClass('error'); 
		   	  tambah = false;
			}else{
			  $(value).removeClass('error'); 
			}
		});	
		
		//cek baris tabel apa kosong ?
		var lenRow = $('#tabel_produksi tbody tr [add="manual"]').length;
	    if (lenRow < 1) {
	       //set counter product lot
	       last_counter =parseInt('<?php echo $counter;?>');	      
	    }

		dgt_nol_jv  = '<?php echo $dgt_nol_jv; ?>'; // ex 000
		length 		= '<?php echo $length; ?>'; //ex -4

		if(tambah){
			last_counter1 = ((dgt_nol_jv + last_counter).slice(length));
			var lot_prefix   = '<?php echo $lot_prefix;?>';
			var lot_prefix_next = '';
		 
			if(lot_prefix != ''){
				var lot_prefix_next =lot_prefix+''+last_counter1;
			}
			//counter + 1
		    last_counter += 1;
		
		    html='<tr class="num">'
		    + '<td></td>'
		    //+ '<td></td>'
		    + '<td class="width-200"><input add="manual" type="hidden" name="txtkode_produk" id="txtkode_produk"  class="form-control input-sm"  readonly="readonly" value="<?php echo ($kode_produk) ?>"><input type="hidden" name="txtproduct[]" id="txtproduct"  class="form-control input-sm produk"  readonly="readonly" data-toggle="tooltip" title="<?php echo htmlentities($product) ?>" value="<?php echo htmlentities($product) ?>">'
		 
		    +  '<input type="text" name="txtlot[]"  id="txtlot" class="form-control input-sm txtlot width-200" value="'+lot_prefix_next+'" onkeypress="enter(event);"></td>'
		    + '<td class="width-120"><select class="form-control input-sm grade width-100" name="grade" id="grade"><option value=""> Pilih Grade </option><?php foreach($list_grade as $row){ echo "<option>".$row->nama_grade."</option>";}?></select></td>'
		    + '<td class="width-100"><input type="text" name="txtqty[]"  id="txtqty" class="form-control input-sm width-80 txtqty" value="<?php echo $qty1_std;?>"   onkeypress="enter(event);" onkeyup="validAngka(this)" data-decimal="2" oninput="enforceNumberValidation(this)"></td>'
		    + '<td ><input type="text" name="txtuom[]"  id="txtuom" class="form-control input-sm width-50" value="<?php echo $uom_1;?>"  readonly="readonly"></td>'
		    + '<td class="width-100"><input type="text" name="txtqty2" id="txtqty2" class="form-control input-sm width-80" value="<?php echo $qty2_std;?>"  onkeypress="enter(event);" onkeyup="validAngka(this)" data-decimal="2" oninput="enforceNumberValidation(this)"></td>'
		    + '<td><input type="text" name="txtuom2"  id="txtuom2" class="form-control input-sm width-50" value="<?php echo $uom_2?>"  readonly="readonly"></td>'
			<?php if($show_lebar['show_lebar'] == 'true'){?>
			+ '<td><input type="text" name="txtlebar_greige" id="txtlebar_greige" class="form-control input-sm width-80" value="<?php echo $lbr_produk->lebar_greige;?>"  onkeypress="enter(event);"></td>'
		    + '<td><select type="text" name="txtuom_lebar_greige"  id="txtuom_lebar_greige" class="form-control input-sm width-80" ><option value=""></option><?php foreach($uom as $row){ if($row->short == $lbr_produk->uom_lebar_greige){ echo "<option selected value=".$row->short.">".$row->short."</option>"; }else{ echo  "<option value=".$row->short.">".$row->short."</option>"; }}?></select></td>'

			+ '<td ><input type="text" name="txtlebar_jadi" id="txtlebar_jadi" class="form-control input-sm width-80" value="<?php echo $lbr_produk->lebar_jadi;?>"  onkeypress="enter(event);"></td>'
		    + '<td><select type="text" name="txtuom_lebar_jadi"  id="txtuom_lebar_jadi" class="form-control input-sm width-80" ><option value=""></option><?php foreach($uom as $row){ if($row->short == $lbr_produk->uom_lebar_jadi){ echo "<option selected value=".$row->short.">".$row->short."</option>"; }else{ echo  "<option value=".$row->short.">".$row->short."</option>"; }}?></select></td>'
			<?php }?>
		    + '<td><input type="text" name="reff_note" id="reff_note" class="form-control input-sm width-150" onkeypress="enter(event);"/></td>'
		    + '<td><a onclick="delRow(this);"  href="javascript:void(0)"  data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a></td>'
		    + '</tr>';
		    $('#tabel_produksi tbody').append(html);
			var prod = $('#txtproduct').val();
			$('[name="txtproduct[]"]').val(prod);
	        lot[inx_lot+1].focus();
			total();

			//cek untuk lot double
			var sama = false;
			var arr3 = [];
			var sama_2 = false;
			$(".txtlot").each(function(){
			    var value = $(this).val();
				var thiss   = this;
			    if (arr3.indexOf(value) == -1){
				    arr3.push(value);
			        $(this).removeClass("error");
					sama_2 = false;
			    }else{
			        $(this).addClass("error");
					sama   = true;
					sama_2 = true;
			    }

				// validasi kp double
				cek_double_lot(value, thiss, sama_2);
				
			});	

			if(sama==true){
				alert('Lot ada yang sama !');
			}
		}

		// validasi kp double key up
		$('.txtlot').on('keyup', function(){
			var txtlot   = $(this).val();
			var thiss    = this;
			cek_double_lot(txtlot,thiss,'');
		});	

	}

	function cek_double_lot(txtlot,thiss,sama_2){

		var kode     = '<?php echo $kode?>';//kode MO
		$.ajax({
				dataType : 'JSON',
				type     : 'POST',
				url      : '<?php echo site_url('manufacturing/mO/cek_input_lot_double') ?>',
				data     : {kode : kode, txtlot :txtlot},
				success : function(data){
					if(data.double == true){
						alert(data.message);
						$(thiss).addClass("error");
					}else if(sama_2 == true){
						$(thiss).addClass("error");
					}else{
						$(thiss).removeClass("error");
					}
				},error : function (jqXHR, textStatus, errorThrown){
					alert(jqXHR.responseText);
					alert('error data');
				}
		});
		return;
	}


	/*********************************
	  END SCRTIPT TABEL PRODUCT LOT
	**********************************/

	/*****************************
	   START SCRTIPT TABEL WASTE
	******************************/

	// hapus row tabel waste
	function delRow_waste(r){		
	  	var i = r.parentNode.parentNode.rowIndex;
		document.getElementById("tabel_produksi_waste").deleteRow(i);
	}

	//fungsi panggil tambah_baris() ketika enter di qty
	function enter_waste(e){
		if(e.keyCode === 13){
	        e.preventDefault(); 
	        tambah_baris_waste(); //panggil fungsi tambah baris
	    }
	}

	//validasi qty
	function validAngka2(a){
	    // if(!/^[0-9.]+$/.test(a.value)){
	    //     a.value = a.value.substring(0,a.value.length-1000);
	    // }
		let tmp_char = '';
		let len      = a.value.length;
		let text     = a.value;
		for(let i = 0; i < len; i++){
			if(len > 1){
				var char = text.substring(i,i+1);
			}else{
				var char = text;
			}
			if(/^[0-9.]+$/.test(char)){
				char1 = char; 
				tmp_char += char1;
	    	}else{
				char.replace(/[^0-9.-]/, '')
			}
		}
		// alert(tmp_char);
		a.value = "";
		a.value = tmp_char;
	}

	//validasi qty
	function validAngka_waste(a){
	    if(!/^[0-9.]+$/.test(a.value)){
	        a.value = a.value.substring(0,a.value.length-1000);
	    }
	}

	// validasi decimal
	function enforceNumberValidation(ele) {
            if ($(ele).data('decimal') != null) {
                // found valid rule for decimal
                var decimal = parseInt($(ele).data('decimal')) || 0;
                var val = $(ele).val();
                if (decimal > 0) {
                    var splitVal = val.split('.');
                    if (splitVal.length == 2 && splitVal[1].length > decimal) {
                        // user entered invalid input
                        $(ele).val(splitVal[0] + '.' + splitVal[1].substr(0, decimal));
                    }
                } else if (decimal == 0) {
                    // do not allow decimal place
                    var splitVal = val.split('.');
                    if (splitVal.length > 1) {
                        // user entered invalid input
                        $(ele).val(splitVal[0]); // always trim everything after '.'
                    }
                }
            }
    }

	var last_counter_waste = parseInt("<?php echo $counter_waste;?>");

	//untuk tambah baris waste
	function tambah_baris_waste(){
		var lot = document.getElementsByName('wtxtlot');
		var inx_lot = lot.length-1;
		var tambah = true;

		//cek Product apa Kosong ?
		$('.wproduk').each(function (index,value) {
			if($(value).val()==null){
				$($(value).data('select2').$container).addClass('error');
				alert('Product Waste tidak boleh Kosong !');
				tambah = false;
			}else{
				$($(value).data('select2').$container).removeClass('error');
			}
		});


		//cek lot apa ada yg kosong
		$('.wtxtlot').each(function(index,value){

			if($(value).data('select2')){
				
				if($(value).data('select2').val()==null){
	   	  		    $($(value).data('select2').$container).addClass('error');
					alert('Lot Waste tidak boleh kosong !');
					tambah = false;
				}else{
					$($(value).data('select2').$container).removeClass('error');
				}
				
			}else{

				if($(value).val()==''){
			   	  $(value).addClass('error'); 
			   	  tambah = false;
			   	  alert('Lot Waste tidak boleh kosong !');
				}else{
				  $(value).removeClass('error'); 
				}
			}

		});

		//cek apa baris di tabel waste kosong ?
		var lenRow = $('#tabel_produksi_waste tbody tr ').length;
	    if (lenRow < 1) {
	       //set counter product waste
	        last_counter_waste =  parseInt("<?php echo $counter_waste;?>");	     
	    }
				
		if(tambah){
				
			last_counter_waste1 = (("00" + last_counter_waste).slice(-3));
			var lot_prefix   = '<?php echo $lot_prefix_waste;?>';
			var lot_prefix_next = '';
			if(lot_prefix){
				lot_prefix_next =lot_prefix+''+last_counter_waste1;
			}
			last_counter_waste +=1;

		    html='<tr class="num">'
		    + '<td></td>'
		    + '<td width="150px">'
		      +'<select type="text" class="form-control input-sm width-200 wproduk" name="wtxtproduct" id="wtxtproduct"></select>'
		      +'<input type="hidden" name="wtxtnameproduct" id="wtxtnameproduct"  class="form-control input-sm wnameproduct"  readonly="readonly"></td>'

		    + '<td style="min-width:180px !important;"><input type="text" name="wtxtlot"  id="wtxtlot" class="form-control input-sm width-160 wtxtlot" value="'+lot_prefix_next+'" onkeypress="enter_waste(event);"></td>'
		    + '<td></td>'
		    + '<td><input type="text" name="wtxtqty"  id="wtxtqty" class="form-control input-sm width-80 wtxtqty" onkeypress="enter_waste(event);" onkeyup="validAngka_waste(this)"></td>'
		    + '<td><input type="text" name="wtxtuom"  id="wtxtuom" class="form-control input-sm width-80 wtxtuom" value="<?php echo $uom_1;?>"  readonly="readonly"></td>'
		    + '<td><input type="text" name="wtxtqty2" id="wtxtqty2" class="form-control input-sm width-80" onkeypress="enter_waste(event);" onkeyup="validAngka_waste(this)"></td>'
		    + '<td><input type="text" name="wtxtuom2"  id="wtxtuom2" class="form-control input-sm width-80 wtxtuom2" value="<?php echo $uom_2?>"  readonly="readonly"></td>'
		    + '<td><input type="text" name="wreff_note" id="wreff_note" class="form-control input-sm width-150 " onkeypress="enter_waste(event);"/></td>'
		    + '<td><a onclick="delRow_waste(this);"  href="javascript:void(0)"  data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a></td>'
		    + '</tr>';
		    $('#tabel_produksi_waste tbody').append(html);
			//var prod = $('#wtxtproduct').val();
			//$('[name="wtxtproduct"]').val(prod);
	        lot[inx_lot+1].focus();
			total();

			//alert($(this).parent().index());
			
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
		   	  		    sama = true;
					}
				}else{
					var isi = $(this).val();
				    if (arr3.indexOf(isi) == -1){
					    arr3.push(isi);
				        $(this).removeClass("error");
				    }else{
				        $(this).addClass("error");
						sama = true;
				    }
				}

			});	


			if(sama==true){
				alert('Lot Waste ada yang sama !');
			}
			

			//select 2 list waste barangjad / bahan baku
	        $('.wproduk ').select2({
	          allowClear: true,
	          placeholder: "",
			  //dropdownParent: $('#tambah_data'),
	          ajax:{
	                dataType: 'JSON',
	                type : "POST",
	                url : "<?php echo base_url();?>manufacturing/mO/get_list_produk_waste",
	                //delay : 250,
	                data : function(params){
	                  return{
	                    prod:params.term,
	                    kode: '<?php echo $kode?>',// kode MO
	                    //kode_produk : '<?php echo $kode_produk ?>', // kode_produk jadi
	                    //nama_produk : '<?php echo ($product)?>', //nama Produk jadi
 	                  };
	                }, 
	                processResults:function(data){
	                  var results = [];
					  var kode_produk = '<?php echo $kode_produk ?>';
					  var nama_produk = '<?php echo $product ?>';
					  results  = [{id:kode_produk, text:nama_produk}];
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


	        $(".wproduk").change(function(){

	        	kode_produk = '<?php echo $kode_produk ?>'; // kode_produk jadi
	        	kode_produk_select =  $(this).parents("tr").find("#wtxtproduct").val();
    			var rowIndex = this.parentNode.parentNode.rowIndex;
	        	var $row     = $(this).parents("tr");
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

    				},error  :function(xhr, ajaxOptions, thrownError){
    					//alert('error data');
    					//alert(xhr.responseText);
    				}
    			});

	        	if(kode_produk != kode_produk_select){
	        	   var replace = '<select id="wtxtlot" name="wtxtlot" class="form-control input-sm wtxtlot"></select>';
    			   $('#tabel_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(3) ').html(replace);
				   var waste  = '<div style="min-width:70px !important"><div><input type="radio" class="waste" id="waste" name="waste'+rowIndex+'[]" value="D"> <label for="Data"> Data</label></div><div><input type="radio"  class="waste" id="waste"  name="waste'+rowIndex+'[]" value="F" checked="checked"> <label for="fisik"> Fisik</label></div><div>'
    			   $('#tabel_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(4) ').html(waste);

	        		// untuk select lot bahan baku
		            $('#tabel_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(3) .wtxtlot').select2({
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
			                    kode_produk : kode_produk_select
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
			                  alert('Error data');
			                  alert(xhr.responseText);
			                }
			          }
			        });
			        
	        	}

	        	if(kode_produk == kode_produk_select || kode_produk_select == null){
	        	  var replace = '<input type="text" name="wtxtlot"  id="wtxtlot" class="form-control input-sm wtxtlot" value="'+lot_prefix_next+'" onkeypress="enter_waste(event);">';
    			   $('#tabel_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(3) ').html(replace);
    			   $('#tabel_produksi_waste tbody tr:nth-child('+rowIndex+') td:nth-child(4) ').html('<input type="hidden" id="waste" >');

	        	}

        	});
		}
	}

	/*****************************
	   end SCRTIPT TABEL WASTE
	******************************/


	function copy_rm(){

		$(document).one("click","#btn-copy",function(e) {
			var row_order_rm = 1;

			$('.qty_konsum').each(function(index,item){
				type 		= $(item).parents("tr").find("#type").val();
				if ($(item).val()!=="" && type == 'stockable') {
					kode_produk = $("#txtkode_produk").val();
					nama_produk = "<?php echo htmlentities($product) ?>";
					qty_smi     = $(item).parents("tr").find('#qty_smi').val();
					uom     	= $(item).parents("tr").find('#uom').val();
					lot     	= $(item).parents("tr").find('#lot').val();
					qty2        = $(item).parents("tr").find("#qty2").val();
					uom2        = $(item).parents("tr").find("#uom2").val();		
					grade_foreach 	= $(item).parents("tr").find("#grade_foreach").val();				
					lbr_greige  	= $(item).parents("tr").find("#lbr_greige").val();		
					uom_lbr_greige  = $(item).parents("tr").find("#uom_lbr_greige").val();
					lbr_jadi  		= $(item).parents("tr").find("#lbr_jadi").val();		
					uom_lbr_jadi  	= $(item).parents("tr").find("#uom_lbr_jadi").val();
					
					row = "grg"+row_order_rm;
					row2 = "gjd"+row_order_rm;

					var level 			= '<?php echo $level; ?>';
					var cek_dept 		= '<?php echo $cek_dept; ?>';
					var copy_bahan_baku = '<?php echo $copy_bahan_baku; ?>';
					var type_mo			= '<?php echo $type_mo['type_mo']; ?>';
					if((cek_dept.includes("PPIC") !== false || level !== 'Entry Data' || type_mo == 'knitting') && (copy_bahan_baku == 'true') ){
						del_row_show = '<a onclick="delRow(this);"  href="javascript:void(0)"  data-toggle="tooltip" title="Hapus Data"><i class="fa fa-trash" style="color: red"></i> </a>';
					}else{
						del_row_show = '';
					}
				
					html='<tr class="num">'
						+ '<td></td>'
						//+ '<td></td>'
						+ '<td><input type="hidden" name="txtkode_produk" id="txtkode_produk"  class="form-control input-sm"  readonly="readonly" value="'+kode_produk+'"><input type="hidden" name="txtproduct[]" id="txtproduct"  class="form-control input-sm produk"  readonly="readonly" value="'+nama_produk+'">'
						+ '<input type="text" name="txtlot[]"  id="txtlot" class="form-control input-sm txtlot width-150" value="'+lot+'" onkeypress="enter(event);"></td>'
						+ '<td><select class="form-control input-sm grade width-100" name="grade" id="grade"><option value=""> Pilih Grade </option>'+grade_foreach+'</select></td>'
						+ '<td><input type="text" name="txtqty[]"  id="txtqty" class="form-control input-sm txtqty width-80" value="'+qty_smi+'"   onkeypress="enter(event);" onkeyup="validAngka(this)" data-decimal="2" oninput="enforceNumberValidation(this)"></td>'
						+ '<td><input type="text" name="txtuom[]"  id="txtuom" class="form-control input-sm width-50" value="'+uom+'"  readonly="readonly"></td>'
						+ '<td><input type="text" name="txtqty2" id="txtqty2" class="form-control input-sm width-80" value="'+qty2+'"  onkeypress="enter(event);" onkeyup="validAngka(this)" data-decimal="2" oninput="enforceNumberValidation(this)"></td>'
						+ '<td><input type="text" name="txtuom2"  id="txtuom2" class="form-control input-sm width-50" value="'+uom2+'"   readonly="readonly"></td>'
						<?php if($show_lebar['show_lebar'] == 'true'){?>
						+ '<td><input type="text" name="txtlebar_greige" id="txtlebar_greige" class="form-control input-sm width-80" value="'+lbr_greige+'"  onkeypress="enter(event);"></td>'
						+ '<td><select type="text" name="txtuom_lebar_greige"  id="txtuom_lebar_greige" class="form-control input-sm width-80 '+row+'" ><option value=""></option><?php foreach($uom as $row){ echo  "<option value=".$row->short.">".$row->short."</option>"; }?></select></td>'

						+ '<td><input type="text" name="txtlebar_jadi" id="txtlebar_jadi" class="form-control input-sm width-80" value="'+lbr_jadi+'"  onkeypress="enter(event);"></td>'
						+ '<td><select type="text" name="txtuom_lebar_jadi"  id="txtuom_lebar_jadi" class="form-control input-sm width-80 '+row2+'" ><option value=""></option><?php foreach($uom as $row){ echo  "<option value=".$row->short.">".$row->short."</option>"; }?></select></td>'
						<?php }?>
						+ '<td><input type="text" name="reff_note" id="reff_note" class="form-control input-sm width-150" onkeypress="enter(event);"/></td>'
						+ '<td>'+del_row_show+'</td>'
						+ '</tr>';
						$('#tabel_produksi tbody').append(html);			
						
						var $option = $("<option selected></option>").val(uom_lbr_greige).text(uom_lbr_greige);
            			$('.'+row).append($option).trigger('change');

						var $option2 = $("<option selected></option>").val(uom_lbr_jadi).text(uom_lbr_jadi);
            			$('.'+row2).append($option2).trigger('change');

						row_order_rm++;
						
				}
								
			});

			var in_qty =0;
			$('.qty_konsum').each(function(index,item){
				qty_smi     = $(item).parents("tr").find('#qty_smi').val();
				//set value qty dikonsumsi
				in_qty = in_qty + parseFloat(qty_smi);
				$(item).eq(0).val(qty_smi);
			});
			$('#in_qty').val(in_qty);

			//alert ketik salin konsumsi bahan selesai
			alert_notify('fa fa-check','Salin Konsumsi Bahan berhasil !','success',function(){});
			$("#btn-copy").prop('disabled',true);
			//total();

			//cek untuk lot double
			$(".txtlot").each(function(){
				var value = $(this).val();
				var thiss = this;
				// validasi kp double
				cek_double_lot(value, thiss,'');
			});

			// validasi kp double key up
			$('.txtlot').on('keyup', function(){
				var txtlot   = $(this).val();
				var thiss    = this;
				cek_double_lot(txtlot,thiss,'');
			});	
			
		});
		
	}

	

	//simpan data 
	$("#btn-tambah-produksi-batch1").unbind( "click" );
	$("#btn-tambah-produksi-batch1").off("click").on("click",function(e) {
	//$("#btn-tambah-produksi-batch").one("click",function(e) {

		e.preventDefault();
		var deptid = '<?php echo $deptid?>';//dept id untuk log history
		var kode   = '<?php echo $kode?>';//kode MO
		var origin_mo = '<?php echo $origin_mo ?>';
		var valid  = true;
		var show_lebar = '<?php echo $show_lebar['show_lebar'];?>';

		//cek lot apa ada yg kosong
		$('.txtlot').each(function(index,value){
			if($(value).val()==''){
		   	  alert('lot tidak boleh kosong !');
		   	  $(value).addClass('error'); 
		   	  valid = false;
			}else{
			  $(value).removeClass('error'); 
			}
		});	

		//cek qty apa ada yg kosong
		$('.txtqty').each(function(index,value){
			if($(value).val()==''){
		   	  alert('qty tidak boleh kosong !');
		      $(value).addClass('error'); 
		   	  valid = false;
			}else{
			  $(value).removeClass('error'); 
			}
		});	


		// > WASTE DETAILS

		var wproduk = false;
		var wtxtlot = false;

		//cek Product apa Kosong ?
		$('.wproduk').each(function (index,value) {
			if($(value).val()==null){
				$($(value).data('select2').$container).addClass('error');
				alert('Product Waste tidak boleh Kosong !');
				valid = false;
			}else{
				$($(value).data('select2').$container).removeClass('error');
			}
		});


		//cek lot apa ada yg kosong
		$('.wtxtlot').each(function(index,value){

			if($(value).data('select2')){
				
				if($(value).data('select2').val()==null){
	   	  		    $($(value).data('select2').$container).addClass('error');
					
					valid = false;
					wproduk = true;
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
			alert('Lot Waste tidak boleh kosong !');
	 	}

	 	if(wtxtlot){
	 		alert('Lot Waste tidak boleh kosong !');
	 	}

		// < WASTE DETAILS

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
        	var qty_sisa = $('#qty_sisa').val();
			var i = 0;
			var konsumsi_bahan = false;
			var hasil_produksi = false;
			var produk_waste   = false;
			var lbr_greige     = '';
			var uom_lbr_greige = '';
			var lbr_jadi       = '';
			var uom_lbr_jadi   = '';

				var arr = new Array();
				
				$(".produk").each(function(index, element) {
					if ($(element).val()!=="") {
						if(show_lebar == 'true'){
							lbr_greige 		= $(element).parents("tr").find('#txtlebar_greige').val();
							uom_lbr_greige  = $(element).parents("tr").find('#txtuom_lebar_greige').val();
							lbr_jadi    	= $(element).parents("tr").find('#txtlebar_jadi').val();
							uom_lbr_jadi    = $(element).parents("tr").find('#txtuom_lebar_jadi').val();
						}
						arr.push({
							//0 : no++,
							kode 		: $("#txtkode").val(),
							kode_produk :$(element).parents("tr").find("#txtkode_produk").val(),
							nama_produk :$(element).val(),
							lot 		:$(element).parents("tr").find("#txtlot").val(),
							qty 		:$(element).parents("tr").find("#txtqty").val(),
							uom 		:$(element).parents("tr").find("#txtuom").val(),
							qty2 		:$(element).parents("tr").find("#txtqty2").val(),
							uom2 		:$(element).parents("tr").find("#txtuom2").val(),
							reff_note 	:$(element).parents("tr").find("#reff_note").val(),
							grade 		:$(element).parents("tr").find("#grade").val(),					
							lbr_greige 		: lbr_greige,
							uom_lbr_greige  : uom_lbr_greige,
							lbr_jadi    	: lbr_jadi,
							uom_lbr_jadi   	: uom_lbr_jadi,
						});
					    hasil_produksi = true;
					}
				}); 
			
				var arr2 = new Array();
				$('.qty_konsum').each(function(index,item){
					if ($(item).val()!=="") {
						arr2.push({
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
							grade 		: $(item).parents("tr").find("#grade").val(),
							lbr_greige  : $(item).parents("tr").find('#lbr_greige').val(),
							uom_lbr_greige  : $(item).parents("tr").find('#uom_lbr_greige').val(),
							lbr_jadi    : $(item).parents("tr").find('#lbr_jadi').val(),
							uom_lbr_jadi  : $(item).parents("tr").find('#uom_lbr_jadi').val(),
							sales_order :$(item).parents("tr").find("#sales_order").val(),
							sales_group :$(item).parents("tr").find("#sales_group").val(),
						});				
					
						//alert (JSON.stringify(arr2));
						konsumsi_bahan = true;
					}
				});		

				var arr5 = [];
				$(".wnameproduct").each(function(index, element) {
					find_waste = $(element).parents("tr").find(".waste:checked").val();
					if(find_waste == undefined){
						checked_waste = '';
					}else{
						checked_waste = $(element).parents("tr").find(".waste:checked").val();
					}
					if ($(element).val()!=="") {
						arr5.push({
							kode_produk:$(element).parents("tr").find("#wtxtproduct").val(),
							nama_produk:$(element).val(),
							lot  :$(element).parents("tr").find("#wtxtlot").val(),
							waste:checked_waste,
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
        			
			/*
			if(konsumsi_bahan==false && produk_waste == false){	
				alert_modal_warning('Maaf, Konsumsi Bahan Kosong !');
			}else 
			*/
			if(hasil_produksi==false &&  produk_waste == false){
				alert('Maaf, Produk Lot / Waste tidak boleh Kosong !');
			}else{	
				please_wait(function(){});
			    $('#btn-tambah-produksi-batch').button('loading');
			    $.ajax({
			        dataType: "JSON",
			        url : '<?php echo site_url('manufacturing/mO/save_produksi_batch_modal') ?>',
			        type: "POST",
			        data: { deptid 		: deptid, 
							origin_mo 	: origin_mo,  
							kode 		: kode,
							kode_produk :$("#txtkode_produk").val(), 
							data_fg 	: JSON.stringify(arr), 
							data_rm 	: JSON.stringify(arr2), 
							data_waste 	: JSON.stringify(arr5)
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
			              $('#btn-tambah-produksi-batch').button('reset');
			              alert_modal_warning(data.message);
						  unblockUI( function(){});
			           	}else{
			              //jika berhasil disimpan
			              $("#status_bar").load(location.href + " #status_bar");
			              $("#tab_1").load(location.href + " #tab_1");
			              $("#tab_2").load(location.href + " #tab_2");             
			              $("#foot").load(location.href + " #foot");
	                 	  $('#tambah_data').modal('hide');
			              $('#btn-tambah-produksi-batch').button('reset');
			              if(data.double == 'yes'){
			              	alert_modal_warning(data.message2);
			              }
			              //console.log(data.sql);
			              unblockUI( function(){
							setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){});}, 1000);
						  });
						  
			            }
						$("#tambah_data .modal-dialog .modal-content .modal-body").removeClass('produksi_rm_batch'); 
			            
			        },error: function (jqXHR, textStatus, errorThrown){
						unblockUI(function() {});
	  		            $('#btn-tambah-produksi-batch').button('reset');
						if(jqXHR.status == 401){
							var err = JSON.parse(jqXHR.responseText);
							alert(err.message);
						}else{
							alert("Error Simpan Produksi Batch !");
						}    
			        }
			    });
			}
		}// if valid true
		
		e.stopImmediatePropagation();
	});
	
	
	/* ===========================
	Code baru
	=========================== */
	function getProduksiData() {
		return {
			beam: $('#pb_beam').val(),
			beam_text: $('#pb_beam option:selected').text(),
			lot: $('#pb_lot').val(),
			grade: $('#pb_grade').val(),
			ui: $('#pb_ui').val(),
			qty1: unformatNumber($('#pb_qty1').val()),
			qty2: unformatNumber($('#pb_qty2').val()),
			ua: $('#pb_ua').val(),
			w: $('#pb_w').val(),
			reff: $('#pb_reff_note').val()
		};
	}

	function validateProduksi(data) {
		if (data.beam == "") {
			alert_notify("fa fa-warning","Silahkan pilih Beam", "danger", function(){});
			$("#pb_beam").focus();
			return false;
		}
		if (data.lot == "") {
			alert_notify("fa fa-warning","Lot belum diisi", "danger", function(){});
			$("#pb_lot").focus();
			return false;
		}
		if (data.grade == "") {
			alert_notify("fa fa-warning","Silahkan pilih Grade", "danger", function(){});
			$("#pb_grade").focus();
			return false;
		}
		if (data.qty1 == "") {
			alert_notify("fa fa-warning","Qty1 belum diisi", "danger", function(){});
			$("#pb_qty1").focus();
			return false;
		}
		if (data.qty2 == "") {
			alert_notify("fa fa-warning","Qty2 belum diisi", "danger", function(){});
			$("#pb_qty2").focus();
			return false;
		}
	
		return true;
	}

	function tambahProduksi() {
		var data = getProduksiData();
		if (!validateProduksi(data)) return;
		appendProduksiRow(data);
		// clearProduksiForm();
		updateSummary();
	}

	$("#btnTambahProduksi").unbind( "click" );
	// $("#btnTambahProduksi").off("click").on("click",function(e) {
	$("#btnTambahProduksi").click(function(){

		var data = getProduksiData();

		if(!validateProduksi(data))
			return;

		var success = false;

		if(editRow){
			success = updateProduksiRow(editRow,data);
		}else{
			success = appendProduksiRow(data);
		}

		if(success){
			resetInputProduksi();
		}

	});

	

	$("#pb_grade").select2({	
		width: "100%",
		dropdownParent: $("#tambah_data"),
		placeholder: "Pilih Grade",
		allowClear: true,
	});

	$("#pb_beam").select2({
		width: "100%",
		dropdownParent: $("#tambah_data"),
		placeholder: "Cari Beam...",
		minimumInputLength: 1,
		allowClear: true,
		ajax: {
			url: "<?= site_url('manufacturing/MO/getBeamSelect2')?>",
			dataType: "json",
			delay: 250,
			data: function(params) {
				return {
					search: params.term,
				};
			},
			processResults: function(data) {
				return {
					results: data
				};
			},
			cache: true
		}
	});

	$("#pb_beam").on("select2:select",function(e){

		var lot_prefix   = '<?php echo $lot_prefix;?>';
	  	var item = e.params.data;

	 	$("#pb_lot").val(lot_prefix + item.id);
		$("#pb_ui").val(item.ui);

	});

	// clear beam
	$('#pb_beam').on('select2:clear', function(){
		clearBeamInfo();
	});

	// unselect (untuk jaga-jaga jika event ini terpanggil)
	$('#pb_beam').on('select2:unselect', function(){
		clearBeamInfo();
	});

	function clearBeamInfo(){
		$('#pb_lot').val('');
		$('#pb_ui').val('');
	}

	function resetInputProduksi(){
		$('#pb_beam').val(null).trigger('change');
		$('#pb_lot').val('');
		$('#pb_grade').val('A').trigger('change');
		$('#pb_ui').val('');
		$('#pb_qty1').val(<?= $qty1_std ?>);
		$('#pb_qty2').val('<?= $qty2_std ?>');
		$('#pb_ua').val('');
		$('#pb_w').val('');
		$('#pb_reff_note').val('');
	}


	$(document).on("click",".pb-delete",function(){
		$(this).closest("tr").remove();
		refreshNo();
		updateSummary();
		if($("#tabel_produksi tbody tr").length==0){
			$("#tabel_produksi tbody").html(`
				<tr class="pb-empty">
					<td colspan="12" align="center">
						Belum ada data produksi
					</td>
				</tr>
			`);
		}
	});

	function refreshNo(){
		$("#tabel_produksi tbody tr").each(function(i){
			$(this).find(".pb-no").text(i+1);
		});
	}

	const numberFormatter = new Intl.NumberFormat('en-US', {
		minimumFractionDigits: 2,
		maximumFractionDigits: 2
	});
	
	function appendProduksiRow(data)
	{
		var dupRow = isDuplicateLot(data.lot);
		if (dupRow) {
			alert_notify("fa fa-warning","Lot sudah ada di daftar produksi.",'danger',function(){});
			dupRow.addClass("pb-row-error");
			$(".pb-table-wrapper").animate({
				scrollTop: dupRow.position().top + $(".pb-table-wrapper").scrollTop() - 50
			}, 300);
			setTimeout(function() {
				dupRow.removeClass("pb-row-error");
			}, 3000);
			$("#pb_lot").focus().select();
			return false;
		}
		// Hilangkan row kosong
		$("#tabel_produksi tbody .pb-empty").remove();
		var reff_note_concat = 'UA:'+data.ua+' UI:'+data.ui+' W:'+data.w+' '+data.reff;
		var html = `
		<tr>
			<td class="pb-no text-center"></td>
			<!-- Lot -->
			<td>
				${data.lot}
				<input type="hidden" name="beam[]" value="${data.beam}">
				<input type="hidden" name="txtlot[]" value="${data.lot}">
			</td>
			<!-- Grade -->
			<td>
				${data.grade}
				<input type="hidden" name="grade[]" value="${data.grade}">
			</td>

			<!-- Qty1 -->
			<td class="text-right">
				${numberFormatter.format(data.qty1)+ ' ' +`<?= $uom_1 ?>`}
				<input type="hidden" name="txtqty[]" value="${data.qty1}">
				<input type="hidden" name="txtuom[]" value="<?= $uom_1 ?>">
			</td>
			<!-- Qty2 -->
			<td class="text-right">
				${numberFormatter.format(data.qty2)+ ' ' +`<?= $uom_2 ?>`}
				<input type="hidden" name="txtqty2[]" value="${data.qty2}">
				<input type="hidden" name="txtuom2[]" value="<?= $uom_2 ?>">
			</td>

			<?php if($show_lebar['show_lebar']=='true'){ ?>
			<!-- Lebar Greige -->
			<td class="text-right">
				<?= $lbr_produk->lebar_greige ?>
				<input type="hidden"
					name="txtlebar_greige[]"
					value="<?= $lbr_produk->lebar_greige ?>">
			</td>

			<!-- Uom Lebar Greige -->
			<td class="text-center">
				<?= $lbr_produk->uom_lebar_greige ?>
				<input type="hidden"
					name="txtuom_lebar_greige[]"
					value="<?= $lbr_produk->uom_lebar_greige ?>">
			</td>

			<!-- Lebar Jadi -->
			<td class="text-right">
				<?= $lbr_produk->lebar_jadi ?>
				<input type="hidden"
					name="txtlebar_jadi[]"
					value="<?= $lbr_produk->lebar_jadi ?>">
			</td>

			<!-- Uom Lebar Jadi -->
			<td class="text-center">
				<?= $lbr_produk->uom_lebar_jadi ?>
				<input type="hidden"
					name="txtuom_lebar_jadi[]"
					value="<?= $lbr_produk->uom_lebar_jadi ?>">
			</td>

			<?php } ?>

			<!-- Reff -->
			<td>
				<small class="text-muted">
					UA : ${data.ua}
					UI : ${data.ui}
					W : ${data.w}
					&nbsp;
				</small>
				${data.reff}
				<input type="hidden"name="reff_note[]"value="${data.reff}">
				<input type="hidden" name="reff_note_concat[]" value="${reff_note_concat}">
				<input type="hidden" name="ua[]" value="${data.ua}">
				<input type="hidden" name="ui[]" value="${data.ui}">
				<input type="hidden" name="w[]" value="${data.w}">

			</td>
			<!-- Action -->
			<td class="text-center">
				<button type="button" class="btn btn-xs btn-primary pb-edit" title="Edit">
					<i class="fa fa-pencil"></i>
				</button>
				<button type="button" class="btn btn-xs btn-danger pb-delete" title="Hapus">
					<i class="fa fa-trash"></i>
				</button>
			</td>
		</tr>
		`;

		$("#tabel_produksi tbody").append(html);

		refreshNo();
		updateSummary();

		var $newRow = $("#tabel_produksi tbody tr:last");

		// Scroll ke row terakhir
		$(".pb-table-wrapper").animate({
			scrollTop: $(".pb-table-wrapper")[0].scrollHeight
		}, 300);

		// Highlight
		$newRow.addClass("pb-row-focus");

		setTimeout(function () {
			$newRow.removeClass("pb-row-focus");
		}, 1200);
		return true;
		
	}

	


	var targetQty = <?= (float)$qty_prod ?>;
	var sisaTarget = <?= (float)$sisa_qty ?>;
	updateSummary();

	function updateSummary() {
		var totalLot = 0;
		var totalQty1 = 0;
		var totalQty2 = 0;
		$("#tabel_produksi tbody tr").not(".pb-empty").each(function() {
			totalLot++;
			totalQty1 += Number($(this).find('input[name="txtqty[]"]').val()) || 0;
			totalQty2 += Number($(this).find('input[name="txtqty2[]"]').val()) || 0;
		});
		var sisaQty = sisaTarget - totalQty1;
		// if (sisaQty < 0) {
		// 	sisaQty = 0;
		// }
		
		// Summary tabel
		$("#pb_total_lot").text(totalLot);
		$("#pb_total_qty1").text(numberFormatter.format(totalQty1));
		$("#pb_total_qty2").text(numberFormatter.format(totalQty2));
		// Header summary
		$("#pb_total_qty").text(numberFormatter.format(totalQty1));
		$("#pb_sisa_qty").text(numberFormatter.format(sisaQty));

		$("#pb_sisa_qty").text(numberFormatter.format(sisaQty)).removeClass("text-success text-primary text-danger");

		if(totalQty1 > sisaQty){
			$("#pb_sisa_qty").addClass("text-danger");
		}else if(sisaQty == 0){
			$("#pb_sisa_qty").addClass("text-success");
		}else{
			$("#pb_sisa_qty").addClass("text-primary");
		}
	}


	var editRow = null;
	$(".pb-edit").unbind( "click" );
	// $(".pb-edit").on("click", function(){
	// $(document).on("click",".pb-edit",function(){
	$(document).off("click", ".pb-edit").on("click", ".pb-edit", function(){
		editRow = $(this).closest("tr");
		// editRow.find("input").each(function () {
		// 	console.log(this.name, this.value);
		// });
		loadEdit(editRow);
	});

	function loadEdit(row) {
		editRow = row;
		$("#pb_lot").val(row.find('input[name="txtlot[]"]').val());
		$("#pb_grade").val(row.find('input[name="grade[]"]').val()).trigger("change");
		$("#pb_beam").val(row.find('input[name="beam[]"]').val()).trigger("change");
		$("#pb_qty1").val(row.find('input[name="txtqty[]"]').val());
		$("#pb_qty2").val(row.find('input[name="txtqty2[]"]').val());
		$("#pb_reff_note").val(row.find('input[name="reff_note[]"]').val());
		$("#pb_ua").val(row.find('input[name="ua[]"]').val());
		$("#pb_ui").val(row.find('input[name="ui[]"]').val());
		$("#pb_w").val(row.find('input[name="w[]"]').val());
		//  Kembalikan semua action terlebih dahulu
		restoreAllAction();


		// Simpan action asli
		var actionCell = row.find("td:last");
		actionCell.data("html", actionCell.html());

		// Ganti dengan badge
		actionCell.html(
			'<span class="label label-warning">' +
				'<i class="fa fa-pencil"></i> Editing' +
			'</span>'
		);
		$("#btnTambahProduksi").removeClass("btn-success").addClass("btn-warning").html('<i class="fa fa-save"></i> Update Lot');
	}

	function restoreAllAction()
	{
		$("#tabel_produksi tbody tr").each(function(){
			var cell = $(this).find("td:last");
			if(cell.data("html")){
				cell.html(cell.data("html"));
				cell.removeData("html");
			}
		});
	}


	function updateProduksiRow(row,data){
		var dupRow = isDuplicateLot(data.lot, row);
		if (dupRow) {
			alert_notify("fa fa-warning","Lot sudah ada di daftar produksi.","danger",function(){});
			dupRow.addClass("pb-row-error");
			$(".pb-table-wrapper").animate({
				scrollTop: dupRow.position().top + $(".pb-table-wrapper").scrollTop() - 50
			}, 300);
			$("#pb_lot").focus().select();
			setTimeout(function() {
				dupRow.removeClass("pb-row-error");
			}, 3000);
			return false;
		}
		row.find("td:eq(1)").html(
			data.lot+
			'<input type="hidden" name="beam[]" value="'+data.beam+'">'+
			'<input type="hidden" name="txtlot[]" value="'+data.lot+'">'
		);

		row.find("td:eq(2)").html(
			data.grade+
			'<input type="hidden" name="grade[]" value="'+data.grade+'">'
		);

		row.find("td:eq(3)").html(
			numberFormatter.format(data.qty1)+' <?= $uom_1 ?>'+
			'<input type="hidden" name="txtqty[]" value="'+data.qty1+'">'+
			'<input type="hidden" name="txtuom[]" value="<?= $uom_1 ?>">'
		);

		row.find("td:eq(4)").html(
			numberFormatter.format(data.qty2)+' <?= $uom_2 ?>'+
			'<input type="hidden" name="txtqty2[]" value="'+data.qty2+'">'+
			'<input type="hidden" name="txtuom2[]" value="<?= $uom_2 ?>">'
		);

		reff_note_concat = 'UA:'+data.ua+' UI:'+data.ui+' W:'+data.w+' '+data.reff;
		row.find("td:eq(5)").html(
			'<small class="text-muted">'+
			'UA : '+data.ua+
			'&nbsp;&nbsp;UI : '+data.ui+
			'&nbsp;&nbsp;W : '+data.w+
			'</small><br>'+
			data.reff+

			'<input type="hidden" name="reff_note[]" value="'+data.reff+'">'+
			'<input type="hidden" name="reff_note_concat[]" value="'+reff_note_concat+'">'+
			'<input type="hidden" name="ua[]" value="'+data.ua+'">'+
			'<input type="hidden" name="ui[]" value="'+data.ui+'">'+
			'<input type="hidden" name="w[]" value="'+data.w+'">'
		);

		updateSummary();
		resetInput();
		return true;
	}

	function resetInput() {
		editRow = null;
		restoreAllAction();
		$("#btnTambahProduksi").removeClass("btn-warning").addClass("btn-success").html('<i class="fa fa-plus-circle"></i> Tambah Lot');
		$("#pb_beam").val(null).trigger("change");
		$("#pb_grade").val("A").trigger("change");
		$("#pb_lot").val("");
		$("#pb_qty1").val("<?= $qty1_std ?>");
		$("#pb_qty2").val("<?= $qty2_std ?>");
		$("#pb_reff_note").val("");
		$("#pb_ua").val("");
		$("#pb_ui").val("");
		$("#pb_w").val("");
	}

	function validateSaveProduksi() {
		var valid = true;
		// Bersihkan error sebelumnya
		$("#tabel_produksi tbody tr").removeClass("pb-row-error");
		$("#tabel_produksi tbody tr").not(".pb-empty").each(function() {
			var row = $(this);
			var lot = $.trim(row.find('[name="txtlot[]"]').val());
			var qty = $.trim(row.find('[name="txtqty[]"]').val());
			if (lot == "" || qty == "") {
				valid = false;
				row.addClass("pb-row-error");
				// hilang setelah 3 detik
				setTimeout(function() {
					row.removeClass("pb-row-error");
				}, 3000);
			}
		});
		if (!valid) {
			alert_notify("fa fa-warning","Masih ada data produksi yang belum lengkap.", "danger", function(){});
		}
		return valid;
	}

	function getProduksiFG() {
		var arr = [];
		$("#tabel_produksi tbody tr").not(".pb-empty").each(function() {
			var row = $(this);
			arr.push({
				kode: "<?= $kode ?>",
				kode_produk: $("#txtkode_produk").val(),
				nama_produk: $("#txtproduct").val(),
				lot: row.find('[name="txtlot[]"]').val(),
				qty: row.find('[name="txtqty[]"]').val(),
				uom: row.find('[name="txtuom[]"]').val(),
				qty2: row.find('[name="txtqty2[]"]').val(),
				uom2: row.find('[name="txtuom2[]"]').val(),
				grade: row.find('[name="grade[]"]').val(),
				reff_note: row.find('[name="reff_note_concat[]"]').val(),
				lbr_greige: row.find('[name="txtlebar_greige[]"]').val() || "",
				uom_lbr_greige: row.find('[name="txtuom_lebar_greige[]"]').val() || "",
				lbr_jadi: row.find('[name="txtlebar_jadi[]"]').val() || "",
				uom_lbr_jadi: row.find('[name="txtuom_lebar_jadi[]"]').val() || ""
			});
		});
		return arr;
	}

	function isDuplicateLot(lot, currentRow = null) {
		var duplicateRow = null;
		$("#tabel_produksi tbody tr").not(".pb-empty").each(function() {
			if (currentRow && $(this)[0] === currentRow[0]) {
				return;
			}
			if ($.trim($(this).find('[name="txtlot[]"]').val()) === $.trim(lot)) {
				duplicateRow = $(this);
				return false;
			}
		});
		return duplicateRow;
	}

	function getRMData()
	{
		var arr=[];

		// copy paste isi arr2 lama

		return arr;
	}

	function getWasteData()
	{
		var arr=[];

		// copy paste isi arr5 lama

		return arr;
	}

	function saveProduksi() {
		var deptid = "<?= $deptid ?>";
		var kode = "<?= $kode ?>";
		var origin_mo = "<?= $origin_mo ?>";
		var arrFG = getProduksiFG();
		var arrRM = getRMData(); // fungsi lama
		var arrWaste = getWasteData(); // fungsi lama
		if (arrFG.length == 0 && arrWaste.length == 0) {
			alert("Produk Lot / Waste tidak boleh kosong.");
			return;
		}
		please_wait(function() {});
		$("#btn-tambah-produksi-batch").button("loading");
		$.ajax({
			url: "<?= site_url('manufacturing/mO/save_produksi_batch_modal') ?>",
			type: "POST",
			dataType: "JSON",
			data: {
				deptid: deptid,
				origin_mo: origin_mo,
				kode: kode,
				kode_produk: $("#txtkode_produk").val(),
				data_fg: JSON.stringify(arrFG),
				data_rm: JSON.stringify(arrRM),
				data_waste: JSON.stringify(arrWaste)
			},
			success: function(res) {
				$("#btn-tambah-produksi-batch").button("reset");
				unblockUI(function() {});
				if (res.sesi == "habis") {
					alert_modal_warning(res.message);
					window.location.replace("../index");
					return;
				}
				if (res.status == "failed") {
					alert_modal_warning(res.message);
					return;
				}
				$("#status_bar").load(location.href + " #status_bar");
				$("#tab_1").load(location.href + " #tab_1");
				$("#tab_2").load(location.href + " #tab_2");
				$("#foot").load(location.href + " #foot");
				$("#tambah_data").modal("hide");
				if (res.double == "yes") {
					alert_modal_warning(res.message2);
				}
				alert_notify(res.icon, res.message, res.type, function(){});
			},
			error: function(jqXHR) {
				unblockUI(function() {});
				$("#btn-tambah-produksi-batch").button("reset");
				alert("Error Simpan Produksi Batch");
			}
		});
	}

	$("#btn-tambah-produksi-batch").unbind( "click" );
	$("#btn-tambah-produksi-batch").off("click").on("click", function(e){

		e.preventDefault();

		if(!validateSaveProduksi()){
			return;
		}
		saveProduksi();
	});

	

</script>