<form class="form-horizontal" id="form_edit_batch" name="form_edit_batch">
	<div class="form-group">
		<div class="col-md-12">
            <div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Lot Baru</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="lot_new" id="lot_new" value="<?php echo $data_items->lot_baru;?>" readonly/>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Corak Remark</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="m_corak_remark" id="m_corak_remark" value="<?php echo $data_items->corak_remark;?>"/>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Warna Remark</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="m_warna_remark" id="m_warna_remark"  value="<?php echo $data_items->warna_remark;?>"/>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="m_qty" id="m_qty" value="<?php echo $data_items->qty;?>" readonly/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
                    <input type="text" class="form-control input-sm" name="m_uom_qty" id="m_uom_qty" readonly  value="<?php echo $data_items->uom;?>">
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty2 </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="m_qty2" id="m_qty2" value="<?php echo $data_items->qty2;?>" readonly/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
                    <input type="text" class="form-control input-sm" name="m_uom_qty2" id="m_uom_qty2" readonly  value="<?php echo $data_items->uom2;?>">
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty Jual </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="m_qty_jual" id="m_qty_jual" data-decimal="2" oninput="enforceNumberValidation(this)"  value="<?php echo $data_items->qty_jual;?>"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="m_uom_qty_jual" id="m_uom_qty_jual" style="width:100% !important;"></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty2 Jual </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="m_qty2_jual" id="m_qty2_jual" data-decimal="2" oninput="enforceNumberValidation(this)"  value="<?php echo $data_items->qty2_jual;?>"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="m_uom_qty2_jual" id="m_uom_qty2_jual" style="width:100% !important;"></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Lebar Jadi </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="m_lebar_jadi" id="m_lebar_jadi"  value="<?php echo $data_items->lebar_jadi;?>"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="m_uom_lebar_jadi" id="m_uom_lebar_jadi" style="width:100% !important;"></select>
				</div>
			</div>
		</div>		
	</div>
</form>


<script type="text/javascript">
   

    var $newOption = $("<option></option>").val("<?php echo $data_items->uom_jual?>").text("<?php echo $data_items->uom_jual?>");
    $("#m_uom_qty_jual").empty().append($newOption).trigger('change');
    var $newOption = $("<option></option>").val("<?php echo $data_items->uom2_jual?>").text("<?php echo $data_items->uom2_jual?>");
    $("#m_uom_qty2_jual").empty().append($newOption).trigger('change');
    var $newOption = $("<option></option>").val("<?php echo $data_items->uom_lebar_jadi?>").text("<?php echo $data_items->uom_lebar_jadi?>");
    $("#m_uom_lebar_jadi").empty().append($newOption).trigger('change');
	
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

        let lot_new         = "<?php echo $data_items->lot_baru;?>";
        let corak_remark   	= $('#m_corak_remark').val();
        let warna_remark  	= $('#m_warna_remark').val();
        let qty_jual	   	= $('#m_qty_jual').val();
        let uom_qty_jual	= $('#m_uom_qty_jual').val();
        let qty2_jual	   	= $('#m_qty2_jual').val();
        let uom_qty2_jual	= $('#m_uom_qty2_jual').val();
		let lebar_jadi		= $('#m_lebar_jadi').val();
		let uom_lebar_jadi	= $('#m_uom_lebar_jadi').val();

        if (lot_new == null) {
            alert_notify('fa fa-warning', 'Lot baru Kosong !', 'danger', function() {});
            $('#lot_new').focus();
        }else if (corak_remark.length === 0) {
            alert_notify('fa fa-warning', 'Corak Remark Harus diisi !', 'danger', function() {});
            $('#corak_remark').focus();
		} else if (qty_jual.length === 0) {
            alert_notify('fa fa-warning', 'Qty Jual Harus diisi !', 'danger', function() {});
			$('#qty_jual').focus();
		} else if (qty_jual.length > 0 && uom_qty_jual == null) {
            alert_notify('fa fa-warning', 'Uom Qty Jual Harus dipilih !', 'danger', function() {});
			$('#uom_qty_jual').select2('focus');
		} else if (qty2_jual.length > 0 && uom_qty2_jual == null) {
            alert_notify('fa fa-warning', 'Uom Qty2 Jual Harus dipilih !', 'danger', function() {});
			$('#uom_qty2_jual').select2('focus');
        } else {
            $('#btn-tambah').button('loading');
            please_wait(function() {});
            $.ajax({
                type: "POST",
                dataType: "json",
                url: '<?php echo base_url('warehouse/splitlot/save_split_items')?>',
                beforeSend: function(e) {
                    if (e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                data: {
                    kode        : "<?php echo $kode?>",
                    corak_remark: corak_remark,
                    warna_remark: warna_remark,
					qty_jual	: qty_jual,
					uom_qty_jual: uom_qty_jual,
					qty2_jual	: qty2_jual,
					uom_qty2_jual: uom_qty2_jual,
					lebar_jadi	: lebar_jadi,
					uom_lebar_jadi : uom_lebar_jadi,
					lot_new: lot_new,
                    quant_id : "<?php echo $data_items->quant_id_baru;?>"
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
                                $("#tab_1").load(location.href + " #tab_1");
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