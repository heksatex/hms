<form class="form-horizontal" id="form_edit_faktur" name="form_edit_faktur">
    <div class="row">
        <div class="form-group">
            <div class="col-md-12">
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>No Faktur</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm" name="no_faktur" id="no_faktur" value="<?php echo htmlentities($get_data->no_faktur); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>No SJ</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm" name="no_sj" id="no_sj" value="<?php echo $get_data->no_sj; ?>" readonly />
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Tanggal</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm" name="tanggal" id="tanggal" value="<?php echo date("Y-m-d", strtotime($get_data->tanggal_faktur)); ?>" readonly />
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Curr</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm" name="currency" id="currency" value="<?php echo $get_data->currency; ?>" readonly />
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Kurs</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm text-right" name="kurs" id="kurs" value="<?php echo number_format($get_data->kurs, 4); ?>" readonly />
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Sisa Piutang (Rp)</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm text-right" name="sisa_piutang_rp" id="sisa_piutang_rp" value="<?php echo number_format($get_data->sisa_piutang_rp, 2); ?>" readonly />
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Sisa Piutang (Valas)</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm text-right" name="sisa_piutang_valas" id="sisa_piutang_valas" value="<?php echo number_format($get_data->sisa_piutang_valas, 2); ?>" readonly />
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Pelunasan (Rp) </label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm text-right formatAngka" name="pelunasan_rp" id="pelunasan_rp" data-decimal="2" value="<?php echo $get_data->pelunasan_rp; ?>"  />
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Pelunasan (Valas) </label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm text-right formatAngka" name="pelunasan_valas" id="pelunasan_valas" data-decimal="2" value="<?php echo $get_data->pelunasan_valas; ?>" />
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Status Bayar</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <select class="form-control input-sm" name="status_bayar" id="status_bayar"> 
                            <?php
                                echo '<option></option>';
                                foreach($statusBayar as $sb) {
                                    if($sb['id'] == $get_data->status_bayar){
                                        echo '<option value="'.$sb['id'].'" selected>'.$sb['text'].'</option>';
                                    } else {
                                        echo '<option value="'.$sb['id'].'">'.$sb['text'].'</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>


<script type="text/javascript">
    
    // btn tambah
    $("#btn-tambah").unbind("click");
    $("#btn-tambah").off("click").on("click", function(e) {
        e.preventDefault();

        let no_pelunasan = "<?php echo $get_data->no_pelunasan; ?>";
        let no_faktur   = "<?php echo ($get_data->no_faktur); ?>"
        var id = "<?php echo $id_phi; ?>" // pelunasan_piutang_faktur
        var pelunasan_rp = unformatNumber($("#pelunasan_rp").val());
        var pelunasan_valas = unformatNumber($("#pelunasan_valas").val());

        $('#btn-tambah').button('loading');
        please_wait(function() {});
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '<?php echo base_url('accounting/pelunasanpiutang/update_pelunasan_faktur') ?>',
            beforeSend: function(e) {
                if (e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            data: {
                no_pelunasan: no_pelunasan,
                id: id,
                pelunasan_rp: pelunasan_rp,
                pelunasan_valas: pelunasan_valas,
                no_faktur : no_faktur,
                status_bayar : $("#status_bayar").val()
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