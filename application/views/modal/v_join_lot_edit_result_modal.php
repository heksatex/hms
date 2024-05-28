
<form class="form-horizontal" id="form_edit" name="form_edit_batch">
	<div class="form-group">
		<div class="col-md-12">
            <div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Kode Produk</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text"  class="form-control input-sm"  name="kode_produk" id="kode_produk" value="<?php echo $data_join->kode_produk; ?>" readonly>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Nama Produk</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk" value="<?php echo htmlentities($data_join->nama_produk); ?>" readonly>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Corak Remark</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="corak_remark" id="corak_remark" value="<?php echo $data_join->corak_remark;?>"/>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Warna Remark</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="warna_remark" id="warna_remark"  value="<?php echo $data_join->warna_remark;?>"/>
				</div>
			</div>
            <div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Lot</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="lot" id="lot"  value="<?php echo $data_join->lot;?>" readonly/>
				</div>
			</div>
            <div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Grade</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="grade" id="grade"  value="<?php echo $data_join->grade;?>" readonly/>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty" id="qty" data-decimal="2" oninput="enforceNumberValidation(this)"  value="<?php echo $data_join->qty;?>" readonly/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
                    <input type="text" class="form-control input-sm" name="uom_qty" id="uom_qty" readonly  value="<?php echo $data_join->uom;?>">
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty2 </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty2" id="qty2" data-decimal="2" oninput="enforceNumberValidation(this)"  value="<?php echo $data_join->qty2;?>" readonly/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
                    <input type="text" class="form-control input-sm" name="uom_qty2" id="uom_qty2" readonly  value="<?php echo $data_join->uom2;?>">
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty Jual </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty_jual" id="qty_jual" data-decimal="2" oninput="enforceNumberValidation(this)"  value="<?php echo $data_join->qty_jual;?>"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="uom_qty_jual" id="uom_qty_jual" style="width:100% !important;"></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty2 Jual </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty2_jual" id="qty2_jual" data-decimal="2" oninput="enforceNumberValidation(this)"  value="<?php echo $data_join->qty2_jual;?>"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="uom_qty2_jual" id="uom_qty2_jual" style="width:100% !important;"></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Lebar Jadi </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="lebar_jadi" id="lebar_jadi"  value="<?php echo $data_join->lebar_jadi;?>"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="uom_lebar_jadi" id="uom_lebar_jadi" style="width:100% !important;"></select>
				</div>
			</div>
		</div>		
	</div>
</form>


<script type="text/javascript">
   

    var $newOption = $("<option></option>").val("<?php echo $data_join->uom_jual?>").text("<?php echo $data_join->uom_jual?>");
    $("#uom_qty_jual").empty().append($newOption).trigger('change');
    var $newOption = $("<option></option>").val("<?php echo $data_join->uom2_jual?>").text("<?php echo $data_join->uom2_jual?>");
    $("#uom_qty2_jual").empty().append($newOption).trigger('change');
    var $newOption = $("<option></option>").val("<?php echo $data_join->uom_lebar_jadi?>").text("<?php echo $data_join->uom_lebar_jadi?>");
    $("#uom_lebar_jadi").empty().append($newOption).trigger('change');
	
	// validasi decimal/ onlynumber
	function enforceNumberValidation(ele) {
            if ($(ele).data('decimal') != null) {
                // found valid rule for decimal
                var decimal = parseInt($(ele).data('decimal')) || 0;
                var val = $(ele).val();
                if (decimal > 0) {
					var value   = ele.value;
					var numbers = value.replace(/[^0-9.-]/g, '');
                    var splitVal = numbers.split('.');
                    if (splitVal.length == 2 && splitVal[1].length > decimal) {
                        // user entered invalid input
						splitVal0  = splitVal[0].replace(/[^0-9.-]/g, '');
						splitVal1  = splitVal[1].substr(0, decimal).replace(/[^0-9]/g, "");
                        $(ele).val(splitVal0 + '.' + splitVal1);
                    }else{
						$(ele).val(numbers)
					}
                } else if (decimal == 0) {

					var value = ele.value;
					var numbers = value.replace(/[^0-9]/g, "");
					ele.value = numbers;
                }
            }
    }

   

	//select 2 uom
	$(".uom").select2({
        allowClear: true,
        placeholder: "",
        ajax:{
            dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>manufacturing/inlet/get_uom_select2",
            data : function(params){
                return{
                    prod:params.term,
                };
            }, 
            processResults:function(data){
                var results = [];
                $.each(data, function(index,item){
                    results.push({
                        id:item.short,
                        text:item.short
                    });
                });
                return {
                    results:results
                };
            },
            error: function (xhr, ajaxOptions, thrownError){
                //alert('Error data');
                console.log(xhr.responseText);
            }
        }
    });
    

	// btn tambah
	$("#btn-tambah").unbind( "click" );
  	$("#btn-tambah").off("click").on("click",function(e) {
        e.preventDefault();

        let kode_produk    	= $('#kode_produk').val();
        let corak_remark   	= $('#corak_remark').val();
        let warna_remark  	= $('#warna_remark').val();
        let qty_jual	   	= $('#qty_jual').val();
        let uom_qty_jual	= $('#uom_qty_jual').val();
        let qty2_jual	   	= $('#qty2_jual').val();
        let uom_qty2_jual	= $('#uom_qty2_jual').val();
		let lebar_jadi		= $('#lebar_jadi').val();
		let uom_lebar_jadi	= $('#uom_lebar_jadi').val();
        let lot             = "<?php echo $data_join->lot; ?>";
        var quant_id        = "<?php echo $data_join->quant_id; ?>"

        if (kode_produk.length === 0) {
            alert_notify('fa fa-warning', 'Produk Kosong !', 'danger', function() {});
            $('#nama_produk').focus();
        }else if (lot.length === 0) {
            alert_notify('fa fa-warning', 'Lot Harus diisi !', 'danger', function() {});
            $('#lot').focus();
        }else if (corak_remark.length === 0) {
            alert_notify('fa fa-warning', 'Corak Remark diisi !', 'danger', function() {});
            $('#corak_remark').focus();
        } else if (warna_remark.length === 0) {
            alert_notify('fa fa-warning', 'Warna Remark Harus diisi !', 'danger', function() {});
            $('#warna_remark').focus();
		} else if (qty_jual.length === 0) {
            alert_notify('fa fa-warning', 'Qty Jual Harus diisi !', 'danger', function() {});
			$('#qty_jual').focus();
		} else if (qty_jual.length > 0 && uom_qty_jual == null) {
            alert_notify('fa fa-warning', 'Uom Qty Jual Harus dipilih !', 'danger', function() {});
			$('#uom_qty_jual').select2('focus');
		} else if (qty2_jual.length > 0 && uom_qty2_jual == null) {
            alert_notify('fa fa-warning', 'Uom Qty2 Jual Harus dipilih !', 'danger', function() {});
			$('#uom_qty2_jual').select2('focus');
		} else if (lebar_jadi.length === 0) {
            alert_notify('fa fa-warning', 'Lebar Jadi Harus diisi !', 'danger', function() {});
			$('#lebar_jadi').focus();
		} else if (uom_lebar_jadi == null) {
            alert_notify('fa fa-warning', 'Uom Lebar Jadi Harus dpilih !', 'danger', function() {});
			$('#uom_lebar_jadi').select2('focus');
        } else {
            $('#btn-tambah').button('loading');
            please_wait(function() {});
            $.ajax({
                type: "POST",
                dataType: "json",
                url: '<?php echo base_url('warehouse/joinlot/save_join_lot_result')?>',
                beforeSend: function(e) {
                    if (e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                data: {
                    kode        : "<?php echo $kode?>",
                    kode_produk : kode_produk,
                    corak_remark: corak_remark,
                    warna_remark: warna_remark,
					qty_jual	: qty_jual,
					uom_qty_jual: uom_qty_jual,
					qty2_jual	: qty2_jual,
					uom_qty2_jual: uom_qty2_jual,
					lebar_jadi	: lebar_jadi,
					uom_lebar_jadi : uom_lebar_jadi,
					lot: lot,
					quant_id: quant_id,
                },
                success: function(data) {
                    if (data.status == 'failed') {
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type,
                            function() {});
                            }, 1000);
                        });
                        if(data.field){
                            $('#' + data.field).focus();
                        }
                    } else {
                        unblockUI( function() {
                            setTimeout(function() { 
                                alert_notify(data.icon,data.message,data.type, function(){},1000); 
								$('#tambah_data').modal('hide');
                            });
                        });
                    }
                    $('#btn-tambah').button('reset');
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    unblockUI(function() {});
                    $('#btn-tambah').button('reset');
                    if(xhr.status == 401){
                        var err = JSON.parse(xhr.responseText);
                        alert(err.message);
                    }else{
                        alert("Error Simpan Data !")
                    }                   
                }
            });
        }
    });
         
</script>