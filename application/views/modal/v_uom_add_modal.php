<form class="form-horizontal" id="create_uom" name="create_uom">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Nama</label></div>
                    <div class="col-xs-8">
                        <input type="text" name="nama" id="nama" class="form-control input-sm">
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Short</label></div>
                    <div class="col-xs-8">
                        <input type="text" name="short" id="short" class="form-control input-sm">
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Jenis</label></div>
                    <div class="col-xs-8">
                        <select class="form-control input-sm" name="jenis" id="jenis">
                            <option value=""></option>
                            <?php
                            $arr_jenis = array('panjang', 'berat', 'waktu', 'unit');
                            $i = 0;
                            foreach ($arr_jenis as $val1) {
                                echo '<option>' . $val1 . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label>Jual</label></div>
                    <div class="col-xs-8">
                        <?php $arr_n = array('yes', 'no'); ?>
                        <select class="form-control input-sm" name="jual" id="jual">
                            <option value=''></option>
                            <?php
                            foreach ($arr_n as $val2) {
                                echo '<option >' . $val2 . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4"><label for="">Beli</label></div>
                    <div class="col-xs-8">
                        <?php $arr_n = array('yes', 'no'); ?>
                        <select class="form-control input-sm" name="beli" id="beli">
                            <option value=''></option>
                            <?php
                            foreach ($arr_n as $val3) {
                                echo '<option >' . $val3 . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

</form>


<script>
    $("#btn-tambah-uom").off("click").on("click", function(e) {
        var nama = $("#nama").val();
        var short = $("#short").val();
        var jenis = $("#jenis").val()
        var jual = $("#jual").val()
        var beli = $("#beli").val();

        if (nama.length == 0) {
            alert_notify('fa fa-warning', 'Nama Harus Diisi !', 'danger', function() {});
        } else if (short.length == 0) {
            alert_notify('fa fa-warning', 'Short Harus Diisi !', 'danger', function() {});
        } else if (jenis.length == 0) {
            alert_notify('fa fa-warning', 'Nama Harus Diisi !', 'danger', function() {});
        } else if (jual.length == 0) {
            alert_notify('fa fa-warning', 'Jual Harus Diisi !', 'danger', function() {});
        } else if (beli.length == 0) {
            alert_notify('fa fa-warning', 'Beli Harus Diisi !', 'danger', function() {});
        } else {
            $("#btn-tambah-uom").button('loading');
            please_wait(function() {});
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?php echo base_url('warehouse/uom/simpan') ?>",
                beforeSend: function(e) {
                    if (e && e.ovverideMimeType) {
                        e.overrideimeType("application/json;charset=UTF-8");
                    }
                },
                data: {
                    id: '',
                    nama: nama,
                    short: short,
                    jenis: jenis,
                    jual: jual,
                    beli: beli
                },
                success: function(data) {
                    if (data.sesi == "habis") {
                        //alert jika session habis
                        alert_modal_warning(data.message);
                        window.location.replace('index');
                    } else if (data.status == "failed") {
                        //jika ada form belum keiisi
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type, function() {});
                            }, 1000);
                        });
                        // document.getElementById(data.field).focus();
                        $("#btn-tambah-uom").button('reset');
                    } else {
                        //jika berhasil disimpan/diubah
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type, function() {});
                            }, 1000);
                        });
                        $('#tambah_data').modal('hide');
                    }
                    $('#btn-tambah-uom').button('reset');

                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(xhr.responseText);
                    unblockUI(function() {});
                    $('#btn-tambah-uom').button('reset');
                }
            });
        }

    });
</script>