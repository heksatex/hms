
<form class="form-horizontal" id="form_add_batch" name="form_add_batch">
	<div class="form-group">
		<div class="col-md-12">
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Nama Produk</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <select type="text" class="form-control input-sm select2 " name="kode_produk" id="kode_produk" style="width:100% !important;" ></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Corak Remark</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="corak_remark" id="corak_remark"/>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Warna Remark</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="warna_remark" id="warna_remark"/>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Quality </label></div>
				<div class="col-12 col-md-12 col-lg-8">
					<select type="text" class="form-control input-sm select2 " name="quality" id="quality" style="width:100% !important;" ></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Jml Pcs </label></div>
				<div class="col-12 col-md-12 col-lg-8">
					<input type="number" class="form-control input-sm" name="jml_pcs" id="jml_pcs" data-decimal="0" oninput="enforceNumberValidation(this)" style="width:100% !important;"/>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty" id="qty" data-decimal="2" oninput="enforceNumberValidation(this)"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<!-- <select type="text" class="form-control input-sm select2 uom" name="uom_qty" id="uom_qty"  style="width:100% !important;" ></select> -->
                    <input type="text" class="form-control input-sm" name="uom_qty" id="uom_qty" readonly>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty2 </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty2" id="qty2" data-decimal="2" oninput="enforceNumberValidation(this)"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<!-- <select type="text" class="form-control input-sm select2 uom" name="uom_qty2" id="uom_qty2" style="width:100% !important;" ></select> -->
                    <input type="text" class="form-control input-sm" name="uom_qty2" id="uom_qty2" readonly>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty Jual </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty_jual" id="qty_jual" data-decimal="2" oninput="enforceNumberValidation(this)"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="uom_qty_jual" id="uom_qty_jual" style="width:100% !important;" ></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty2 Jual </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty2_jual" id="qty2_jual" data-decimal="2" oninput="enforceNumberValidation(this)"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="uom_qty2_jual" id="uom_qty2_jual" style="width:100% !important;" ></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Lebar Jadi </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="lebar_jadi" id="lebar_jadi"/>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="uom_lebar_jadi" id="uom_lebar_jadi" style="width:100% !important;" ></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
                <div class="col-12 col-md-12 col-lg-4"><label>Kode K3L</label></div>
                <div class="col-12 col-md-12 col-lg-8">
                	<select class="form-control input-sm select2" name="k3l" id="k3l" style="width:100% !important;">
                        <option value=""></option>
                        <?php foreach ($kode_k3l as $row) {?>
                            <option value='<?php echo $row->kode; ?>'><?php echo $row->kode;?></option>
                        <?php  }?>
                    </select> 
                </div>                                    
            </div>
		</div>		
	</div>
</form>


<script type="text/javascript">

	$('#k3l').select2({
        allowClear: true,
        placeholder: '',
    });
	
	
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

	//select 2 product
	$("#kode_produk").select2({
		allowClear: true,
        placeholder: "",
        ajax:{
        	dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>manufacturing/barcodemanual/get_produk_select_mrp_manual_batch",
            //delay : 250,
			data: function (params) {
                        var query = {
                            prod: params.term
                        };

                        return query;
            },
            processResults: function (data) {
					var results = [];

				    $.each(data, function(index,item){
				        results.push({
				                id:item.kode_produk,
				                text:'['+item.kode_produk+'] '+item.nama_produk,
								uom : item.uom,
								uom2 : item.uom_2
				        });
				    });
				    return {
				        results:results
				    };
            },
            error: function (xhr, ajaxOptions, thrownError){
                console.log(xhr.responseText);
          	}
        }
    });

	$("#kode_produk").on('select2:select', function (e) {
		var uom = $("#kode_produk :selected").data().data.uom;
		var uom2 = $("#kode_produk :selected").data().data.uom2;
		var $newOption = $("<option></option>").val(uom).text(uom);
        // $("#uom_qty").empty().append($newOption).trigger('change');
		// var $newOption2 = $("<option></option>").val(uom2).text(uom2);
        // $("#uom_qty2").empty().append($newOption2).trigger('change');
        $("#uom_qty").val(uom);
        $("#uom_qty2").val(uom2);
    });

    $("#kode_produk").on('select2:unselect', function (e) {
        // $("#uom_qty").empty().append('<option></option>').trigger('change');
        // $("#uom_qty2").empty().append('<option></option>').trigger('change');
        $("#uom_qty").val('');
        $("#uom_qty2").val('');
    });

	//select 2 product
	$("#quality").select2({
		allowClear: true,
        placeholder: "",
        ajax:{
        	dataType: 'JSON',
            type : "POST",
            url : "<?php echo base_url();?>manufacturing/barcodemanual/get_list_quality_select2",
            //delay : 250,
            data : function(params){
            	return{
                    prod:params.term
                };
        	}, 
            processResults:function(data){
                var results = [];

                $.each(data, function(index,item){
                    results.push({
                            id:item.id,
                            text : item.nama
                    });
                });
                return {
                    results:results
                };
            },
            error: function (xhr, ajaxOptions, thrownError){
                console.log(xhr.responseText);
          	}
        }
    });

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
        let quality  		= $('#quality').val();
        let jml_pcs		    = $('#jml_pcs').val();
        let qty		    	= $('#qty').val();
        let uom		    	= $('#uom_qty').val();
        let qty2	    	= $('#qty2').val();
        let uom2	    	= $('#uom_qty2').val();
        let qty_jual	   	= $('#qty_jual').val();
        let uom_qty_jual	= $('#uom_qty_jual').val();
        let qty2_jual	   	= $('#qty2_jual').val();
        let uom_qty2_jual	= $('#uom_qty2_jual').val();
		let lebar_jadi		= $('#lebar_jadi').val();
		let uom_lebar_jadi	= $('#uom_lebar_jadi').val();
		let k3l				= $('#k3l').val();

        if (kode_produk == null) {
            alert_notify('fa fa-warning', 'Kode Produk Harus dipilih !', 'danger', function() {});
            $('#kode_produk').select2('focus');
        }else if (corak_remark.length === 0) {
            alert_notify('fa fa-warning', 'Corak Remark Harus dipilih !', 'danger', function() {});
            $('#corak_remark').focus();
        } else if (warna_remark.length === 0) {
            alert_notify('fa fa-warning', 'Warna Remark Harus diisi !', 'danger', function() {});
            $('#warna_remark').focus();
		} else if (jml_pcs.length === 0) {
            alert_notify('fa fa-warning', 'Jml Pcs Minimal 1 !', 'danger', function() {});
            $('#jml_pcs').focus();
		} else if (qty.length === 0) {
            alert_notify('fa fa-warning', 'Qty Harus diisi !', 'danger', function() {});
            $('#qty').focus();
		} else if (uom == null) {
            alert_notify('fa fa-warning', 'Uom Harus dpilih !', 'danger', function() {});
			$('#uom_qty').select2('focus');
		} else if (qty2.length === 0) {
            alert_notify('fa fa-warning', 'Qty2 Harus diisi !', 'danger', function() {});
            $('#qty2').focus();
		} else if (uom2 == null) {
            alert_notify('fa fa-warning', 'Uom2 Harus dpilih !', 'danger', function() {});
			$('#uom_qty2').select2('focus');
		} else if (qty_jual.length === 0) {
            alert_notify('fa fa-warning', 'Qty Jual Harus diisi !', 'danger', function() {});
			$('#qty_jual').focus();
		} else if (qty_jual.length > 0 && uom_qty_jual == null) {
            alert_notify('fa fa-warning', 'Uom Qty Jual Harus dpilih !', 'danger', function() {});
			$('#uom_qty_jual').select2('focus');
		} else if (qty2_jual.length > 0 && uom_qty2_jual == null) {
            alert_notify('fa fa-warning', 'Uom Qty2 Jual Harus dpilih !', 'danger', function() {});
			$('#uom_qty2_jual').select2('focus');
		} else if (lebar_jadi.length === 0) {
            alert_notify('fa fa-warning', 'Lebar_jadi Harus diisi !', 'danger', function() {});
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
                url: '<?php echo base_url('manufacturing/barcodemanual/save_mrp_batch')?>',
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
					jml_pcs     : jml_pcs,
					quality		: quality,
					qty			: qty,
					uom			: uom,
					qty2		: qty2,
					uom2		: uom2,
					qty_jual	: qty_jual,
					uom_qty_jual: uom_qty_jual,
					qty2_jual	: qty2_jual,
					uom_qty2_jual: uom_qty2_jual,
					lebar_jadi	: lebar_jadi,
					uom_lebar_jadi : uom_lebar_jadi,
					kode_k3l: k3l,
					row:""
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