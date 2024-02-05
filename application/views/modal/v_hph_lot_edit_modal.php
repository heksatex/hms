
<form class="form-horizontal" id="form_edit_hph_lot" name="form_edit_hph_lot">
	<div class="form-group">
		<div class="col-md-12" style="padding:0px;">
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Lot</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="lot" id="lot" value="<?php echo $data_hph_lot->lot;?>" readonly>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Corak Remark</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="mcorak_remark" id="mcorak_remark" <?php echo ($data_hph_lot->nama_grade=='F')? 'readonly' : '' ?> value="<?php echo $data_hph_lot->corak_remark;?>" >
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Warna Remark</label></div>
				<div class="col-12 col-md-12 col-lg-8">
                    <input type="text" class="form-control input-sm" name="mwarna_remark" id="mwarna_remark" <?php echo ($data_hph_lot->nama_grade=='F')? 'readonly' : '' ?>  value="<?php echo $data_hph_lot->warna_remark;?>" >
				</div>
			</div>		
		
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty [HPH] </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty" id="qty" value="<?php echo $data_hph_lot->qty;?>" readonly>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="uom_qty" id="uom_qty" style="width:100% !important;" disabled></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty2 [HPH]</label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty2" id="qty2" value="<?php echo $data_hph_lot->qty2;?>" readonly>
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="uom_qty2" id="uom_qty2" style="width:100% !important;" disabled></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty Jual </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty_jual" id="qty_jual" data-decimal="2" oninput="enforceNumberValidation(this)"  <?php echo ($data_hph_lot->nama_grade=='F')? 'readonly' : '' ?>  value="<?php echo $data_hph_lot->qty_jual;?>">
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="uom_qty_jual" id="uom_qty_jual" style="width:100% !important;" <?php echo ($data_hph_lot->nama_grade=='F')? 'disabled' : '' ?>></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Qty2 Jual </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="qty2_jual" id="qty2_jual" data-decimal="2" oninput="enforceNumberValidation(this)" <?php echo ($data_hph_lot->nama_grade=='F')? 'readonly' : '' ?>  value="<?php echo $data_hph_lot->qty2_jual;?>">
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="uom_qty2_jual" id="uom_qty2_jual" style="width:100% !important;" <?php echo ($data_hph_lot->nama_grade=='F')? 'disabled' : '' ?>></select>
				</div>
			</div>
			<div class="col-md-12 col-xs-12">
				<div class="col-12 col-md-12 col-lg-4"><label>Lebar Jadi </label></div>
				<div class="col-12 col-md-12 col-lg-4">
					<input type="text" class="form-control input-sm text-right" name="mlebar_jadi" id="mlebar_jadi" <?php echo ($data_hph_lot->nama_grade=='F')? 'readonly' : '' ?>  value="<?php echo $data_hph_lot->lebar_jadi;?>">
				</div>
				<div class="col-12 col-md-12 col-lg-4">
					<select type="text" class="form-control input-sm select2 uom" name="muom_lebar_jadi" id="muom_lebar_jadi" style="width:100% !important;" <?php echo ($data_hph_lot->nama_grade=='F')? 'disabled' : '' ?> ></select>
				</div>
			</div>
            <div class="col-md-12 col-xs-12">
                <div class="col-12 col-md-12 col-lg-4"><label>Desain Barcode</label></div>
                <div class="col-12 col-md-12 col-lg-8">
                            <select class="form-control input-sm select2" name="mdesain_barcode" id="mdesain_barcode" >
                                <option value=""></option>
                                  <?php foreach ($desain_barcode as $row) {?>
                                    <option value='<?php echo $row->kode_desain; ?>'><?php echo $row->kode_desain;?></option>
                                <?php  }?>
                            </select> 
                </div> 
            </div>
		</div>		
        <div class="col-md-12">
            <div class="col-lg-12 col-xs-12 col-md-12 table-responsive">
                <label>List Uom Jual yang Tersedia</label>
                <table class="table table-condesed table-hover rlstable" width="100%" id="table_list_uom">
                    <thead>
                        <tr>
                            <th class="style bb no">No</th>
                            <th class="style bb text-right">Qty Jual</th>
                            <th class="style bb">Uom</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach($list_uom_ready as $lur){
                                echo '<tr class="num">';
                                echo '<td ></td>';
                                echo '<td class="text-right">'.$lur->qty.'</td>';
                                echo '<td>'.$lur->uom.'</td>';
                                echo '</tr>';
                            }
                            if(empty($list_uom_ready)){
                                echo '<tr>';
                                echo '<td colspan="3">Tidak ada data</td>';
                                echo '</tr>';
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
	</div>
	
</form>


<script type="text/javascript">

    $('.select2').select2({
        allowClear: true,
        placeholder: 'Pilih',
        width:'100%'

    });

    $("#uom_qty_jual").on('select2:unselect', function (e) {
        $("#qty_jual").val('');
    });

    $("#uom_qty2_jual").on('select2:unselect', function (e) {
        $("#qty2_jual").val('');
    });

    $("#uom_qty_jual").on('select2:select', function (e) {
        uom = $(this).val();
        get_value_uom(uom,'#qty_jual');
    });

    $("#uom_qty2_jual").on('select2:select', function (e) {
        uom = $(this).val();
        get_value_uom(uom,'#qty2_jual');
    });
   

    function get_value_uom(uom,id){
        arr_uom = <?php echo $list_uom_lot;?>;
        $.each(arr_uom, function(index,item){
            if(item.uom == uom){
                $(id).val(item.qty);
                return false;
            }else{
                $(id).val('');
            }
        });
        if(arr_uom.length === 0){
            $(id).val('');
        }
    }

    var $newOption = $("<option></option>").val("<?php echo $data_hph_lot->uom?>").text("<?php echo $data_hph_lot->uom?>");
    $("#uom_qty").empty().append($newOption).trigger('change');
    var $newOption = $("<option></option>").val("<?php echo $data_hph_lot->uom2?>").text("<?php echo $data_hph_lot->uom2?>");
    $("#uom_qty2").empty().append($newOption).trigger('change');
    var $newOption = $("<option></option>").val("<?php echo $data_hph_lot->uom_jual?>").text("<?php echo $data_hph_lot->uom_jual?>");
    $("#uom_qty_jual").empty().append($newOption).trigger('change');
    var $newOption = $("<option></option>").val("<?php echo $data_hph_lot->uom2_jual?>").text("<?php echo $data_hph_lot->uom2_jual?>");
    $("#uom_qty2_jual").empty().append($newOption).trigger('change');
    var $newOption = $("<option></option>").val("<?php echo $data_hph_lot->uom_lebar_jadi?>").text("<?php echo $data_hph_lot->uom_lebar_jadi?>");
    $("#muom_lebar_jadi").empty().append($newOption).trigger('change');
    
        
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

        let corak_remark   	= $('#mcorak_remark').val();
        let warna_remark  	= $('#mwarna_remark').val();
        let qty_jual	   	= $('#qty_jual').val();
        let uom_qty_jual	= $('#uom_qty_jual').val();
        let qty2_jual	   	= $('#qty2_jual').val();
        let uom_qty2_jual	= $('#uom_qty2_jual').val();
		let lebar_jadi		= $('#mlebar_jadi').val();
		let uom_lebar_jadi	= $('#muom_lebar_jadi').val();
        let kode            = "<?php echo $data_hph_lot->id; ?>";
        let quant_id        = "<?php echo $data_hph_lot->quant_id; ?>";
        let lot             = "<?php echo $data_hph_lot->lot; ?>"; 
        let nama_grade      = "<?php echo $data_hph_lot->nama_grade;?>";

        if(nama_grade === "F"){
            alert_notify('fa fa-warning', 'Grade F tidak bisa Edit HPH !', 'danger', function() {});
        }else if (corak_remark.length === 0) {
            alert_notify('fa fa-warning', 'Corak Remark Harus dipilih !', 'danger', function() {});
            $('#mcorak_remark').focus();
        } else if (warna_remark.length === 0 && nama_grade != 'C') {
            alert_notify('fa fa-warning', 'Warna Remark Harus diisi !', 'danger', function() {});
            $('#mwarna_remark').focus();
		} else if (qty_jual.length === 0) {
            alert_notify('fa fa-warning', 'Qty Jual Harus diisi !', 'danger', function() {});
			$('#qty_jual').focus();
		} else if (qty_jual.length > 0 && uom_qty_jual == null) {
            alert_notify('fa fa-warning', 'Uom Qty Jual Harus dpilih !', 'danger', function() {});
			$('#uom_qty_jual').select2('focus');
		} else if (qty2_jual.length > 0 && uom_qty2_jual == null) {
            alert_notify('fa fa-warning', 'Uom Qty2 Jual Harus dpilih !', 'danger', function() {});
			$('#uom_qty2_jual').select2('focus');
		} else if (lebar_jadi.length === 0 && nama_grade != 'C') {
            alert_notify('fa fa-warning', 'Lebar_jadi Harus diisi !', 'danger', function() {});
			$('#lebar_jadi').focus();
		} else if (uom_lebar_jadi == null && nama_grade != 'C') {
            alert_notify('fa fa-warning', 'Uom Lebar Jadi Harus dpilih !', 'danger', function() {});
			$('#uom_lebar_jadi').select2('focus');
        } else {
            $('#btn-tambah').button('loading');
            please_wait(function() {});
            $.ajax({
                type: "POST",
                dataType: "json",
                url: '<?php echo base_url('manufacturing/inlet/save_edit_hph_lot')?>',
                beforeSend: function(e) {
                    if (e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                data: {
                    kode        : kode,
                    quant_id    : quant_id,
                    lot         : lot,
                    corak_remark: corak_remark,
                    warna_remark: warna_remark,
					qty_jual	: qty_jual,
					uom_qty_jual: uom_qty_jual,
					qty2_jual	: qty2_jual,
					uom_qty2_jual: uom_qty2_jual,
					lebar_jadi	: lebar_jadi,
					uom_lebar_jadi : uom_lebar_jadi,
                },
                success: function(data) {
                    if (data.status == 'failed') {
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type,
                            function() {});
                            }, 1000);
                        });
                    } else {
                        unblockUI( function() {
                            setTimeout(function() { 
                                alert_notify(data.icon,data.message,data.type, function(){},1000); 
								// $('#tambah_data').modal('hide');
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

	$("#btn-print").unbind( "click" );
    $("#btn-print").off("click").on("click",function(e) {
       
        let kode            = "<?php echo $data_hph_lot->id; ?>";
        let quant_id        = "<?php echo $data_hph_lot->quant_id; ?>";
        let lot             = "<?php echo $data_hph_lot->lot; ?>";
        let desain_barcode  = $('#mdesain_barcode').val();

        if(desain_barcode.length === 0){
            alert_notify('fa fa-warning', 'Desain Barcode Harus dipilih !', 'danger', function() {});
            $('#mdesain_barcode').select2('focus');
        }else{

            var btn_load = $(this);
            btn_load.button('loading');
            please_wait(function() {});
            $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '<?php echo base_url('manufacturing/inlet/reprint_barcode_hph')?>',
                    beforeSend: function(e) {
                        if (e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }
                    },
                    data: { kode:kode, quant_id:quant_id, lot:lot, desain_barcode:desain_barcode,
                    },
                    success: function(data) {
                        if (data.status == 'failed') {
                            unblockUI(function() {
                                setTimeout(function() {
                                    alert_notify(data.icon, data.message, data.type,
                                function() {});
                                }, 1000);
                            });
                        } else {
                            var divp = document.getElementById('printed');
                            divp.innerHTML = data.data_print;
                            unblockUI( function() {
                                    setTimeout(function() { alert_notify(data.icon,data.message,data.type,function(){ 
                                            print_voucher();
                                    });},1000); 
                            });
                        }
                    btn_load.button('reset');
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        unblockUI(function() {});
                        btn_load.button('reset');
                        if(xhr.status == 401){
                            var err = JSON.parse(xhr.responseText);
                            alert(err.message);
                        }else{
                            alert("Error print Data!")
                        }                   
                    }
            });
        }
    });
    
    // load new page print
    function print_voucher() {
        var win = window.open();
        win.document.write($("#printed").html());
        win.document.close();
        setTimeout(function(){ win.print(); win.close();}, 200);
    }
	  
</script>