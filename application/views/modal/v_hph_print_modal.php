
<form class="form-horizontal">
    <div class="form-group">
        <div class="col-md-12 col-xs-12">
            <div class="col-xs-4"><label>Desain Barcode</label></div>
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

</form>

<script type="text/javascript">
    $('.select2').select2({
        allowClear: true,
        placeholder: 'Pilih',
        width:'100%'

    });

    // });
    // print
    $("#btn-print-modal").unbind( "click" );
    $("#btn-print-modal").off("click").on("click",function(e) {

        let desain_barcode  = $('#desain_barcode').val();
        let data_arr = [];

        <?php foreach($data_print as $a){ ?>
            data_arr.push('<?=$a?>');
        <?php } ?>

        
        if (data_arr.length === 0) {
            alert_notify('fa fa-warning', 'Pilih LOT terlebih dahulu yang akan di print !', 'danger', function() {});
        }else  if (desain_barcode.length === 0) {
            alert_notify('fa fa-warning', 'Desain Barcode Harus dipilih !', 'danger', function() {});
            $('#desain_barcode').select2('focus');
        } else {
            var btn_load = $(this);
            btn_load.button('loading');
            please_wait(function() {});
            $.ajax({
                type: "POST",
                dataType: "json",
                url: '<?php echo base_url('manufacturing/inlet/print_barcode_modal')?>',
                beforeSend: function(e) {
                    if (e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                data: {
                    id_inlet        : "<?php echo $id_inlet ?>",
                    data            :data_arr,
                    desain_barcode  : desain_barcode,
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
                    console.log(xhr);
                    if(xhr.status == 401){
                        // var err = JSON.parse(xhr.responseText);
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
