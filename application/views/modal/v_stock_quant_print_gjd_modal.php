
<form class="form-horizontal">
    <div class="form-group">
        <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Desain Barcode2</label></div>
                <div class="col-xs-8 col-md-8">
                    <select class="form-control input-sm select2" name="desain_barcode" id="desain_barcode" >
                        <option value=""></option>
                        <?php foreach ($desain_barcode as $row) {?>
                             <option value='<?php echo $row->kode_desain; ?>'><?php echo $row->kode_desain;?></option>
                         <?php  }?>
                    </select> 
                </div>                                    
            </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Kode K3L</label></div>
                <div class="col-xs-8 col-md-8">
                    <select class="form-control input-sm select2" name="k3l" id="k3l" >
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
    $('.select2').select2({
        allowClear: true,
        placeholder: 'Pilih',
        width:'100%'

    });


    // $("#btn-print-barcode").unbind( "click" );
    $("#btn-print-barcode").off("click").on("click",function(e) {
       
        let quant_id            = "<?php echo $quant_id; ?>";
        let k3l             = $('#k3l').val();
        let desain_barcode  = $('#desain_barcode').val();

        if(desain_barcode.length === 0){
            alert_notify('fa fa-warning', 'Desain Barcode Harus dipilih !', 'danger', function() {});
            $('#desain_barcode').select2('focus');
        }else if(k3l.length === 0){
            alert_notify('fa fa-warning', 'K3L Harus dipilih !', 'danger', function() {});
            $('#k3l').select2('focus');
        }else{

            var btn_load = $(this);
            btn_load.button('loading');
            please_wait(function() {});
            $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '<?php echo base_url('warehouse/stockquants/print_barcode_gjd')?>',
                    beforeSend: function(e) {
                        if (e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }
                    },
                    data: { quant_id:quant_id, k3l:k3l, desain_barcode:desain_barcode,
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
                            // var err = JSON.parse(xhr.responseText);
                            alert(xhr.responseText);
                        }else{
                            alert(xhr.responseText);
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
