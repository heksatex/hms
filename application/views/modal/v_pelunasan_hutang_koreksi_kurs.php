<form class="form-horizontal" id="form_koreksi_kurs" name="form_koreksi_kurs">
    <div class="row">
        <div class="form-group">
            <div class="col-md-12">
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Tanggal</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm" name="tanggal" id="tanggal" value="<?php echo date("Y-m-d", strtotime($get_head->tanggal_transaksi)); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Uraian</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm" name="uraian" id="uraian"/>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Curr</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <select class="form-control input-sm" name="currency" id="currency">
                            <option value=''>Pilih</option>
                            <?php 
                                foreach($get_curr as $gc) {
                                    echo "<option value='".$gc->id."'>".$gc->currency."</option>";
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Kurs</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm text-right formatAngka" name="kurs" id="kurs" data-decimal="4"  >
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Total Valas</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm text-right formatAngka" name="value_valas" id="value_valas" data-decimal="2" />
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>


<script type="text/javascript">
    
    $("#currency").select2({});

    // btn tambah
    $("#btn-tambah").unbind("click");
    $("#btn-tambah").off("click").on("click", function(e) {
        e.preventDefault();

        let no_pelunasan = "<?php echo $no_pelunasan; ?>";
        var tanggal      = $("#tanggal").val();
        var uraian       = $("#uraian").val();
        var currency     = $("#currency").val();
        var kurs = unformatNumber($("#kurs").val());
        var value_valas = unformatNumber($("#value_valas").val());

        $('#btn-tambah').button('loading');
        please_wait(function() {});
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '<?php echo base_url('accounting/pelunasanhutang/save_koreksi_kurs') ?>',
            beforeSend: function(e) {
                if (e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            data: {
                no_pelunasan: no_pelunasan,
                tanggal: tanggal,
                uraian: uraian,
                currency: currency,
                kurs: kurs,
                value_valas: value_valas,
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
                    unblockUI(function() {
                        setTimeout(function() {
                            alert_notify(data.icon, data.message, data.type, function() {}, 1000);
                            $('#tambah_data').modal('hide');
                        });
                    });
                }
                $('#btn-tambah').button('reset');
            },
            error: function(xhr, ajaxOptions, thrownError) {
                unblockUI(function() {});
                $('#btn-tambah').button('reset');
                if (xhr.status == 401) {
                    var err = JSON.parse(xhr.responseText);
                    alert(err.message);
                } else {
                    alert(xhr.responseText)
                }
            }
        });
    });
</script>