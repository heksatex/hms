<form class="form-horizontal" id="form_edit_invoice" name="form_edit_invoice">
    <div class="row">
        <div class="form-group">
            <div class="col-md-12">
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Selisih</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <input type="text" class="form-control input-sm text-right" name="selisih" id="selisih" value="<?php echo number_format($get_sum->selisih ?? 0, 2); ?>" readonly>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Debit</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <select class="form-control input-sm select-coa" data-posisi="D" name="debit" id="debit"></select>
                        <!-- <small id="infoDebit" class="form-text text-muted" style="display:block; text-align:right;">&nbsp</small> -->
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4"><label>Credit</label></div>
                    <div class="col-12 col-md-12 col-lg-8">
                        <select class="form-control input-sm select-coa" data-posisi="C" name="credit" id="credit"></select>
                        <!-- <small id="infoCredit" class="form-text text-muted" style="display:block; text-align:right;">&nbsp</small> -->
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div class="col-12 col-md-12 col-lg-4">
                        <button type="button" class="btn btn-xs btn-default" id="get_coa_def" name="get_coa_def">get coa default</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>


<script type="text/javascript">
    let kd_coa_debit = `<?php echo isset($get_coa_debit->kode_coa) ? $get_coa_debit->kode_coa : ''; ?>`;
    let nm_coa_debit = `<?php echo isset($get_coa_debit->nama_coa) ? $get_coa_debit->nama_coa : ''; ?>`;
    let $selectDebit = $("#debit");
    let $selectCredit = $("#credit");

    if (kd_coa_debit && nm_coa_debit) {
        nm_modif = kd_coa_debit + ' - ' + nm_coa_debit;
        let option = new Option(nm_modif, kd_coa_debit, true, true);
        $selectDebit.append(option);
    }

    let kd_coa_credit = `<?php echo isset($get_coa_credit->kode_coa) ? $get_coa_credit->kode_coa : ''; ?>`;
    let nm_coa_credit = `<?php echo isset($get_coa_credit->nama_coa) ? $get_coa_credit->nama_coa : ''; ?>`;
    let $selectcredit = $("#credit");

    if (kd_coa_credit && nm_coa_credit) {
        nm_modif = kd_coa_credit + ' - ' + nm_coa_credit;
        let option = new Option(nm_modif, kd_coa_credit, true, true);
        $selectCredit.append(option);
    }

    //select 2 supplier
    $('.select-coa').each(function() {
        let $el = $(this); // simpan referensi elemen
        $el.select2({
            allowClear: true,
            placeholder: "Pilih",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo base_url(); ?>accounting/pelunasanhutang/get_list_coa",
                data: function(params) {
                    return {
                        name: params.term,
                        jenis_koreksi: "<?php echo $jenis_koreksi; ?>",
                        id: "<?php echo $id_summary; ?>",
                        no_pelunasan: "<?php echo $no_pelunasan; ?>",
                        posisi: $el.attr('data-posisi')
                    };
                },
                processResults: function(data) {
                    var results = [];
                    $.each(data, function(index, item) {
                        results.push({
                            id: item.kode_coa,
                            text: item.kode_coa + ' - ' + item.nama,
                            info: item.nama
                        });
                    });
                    return {
                        results: results
                    };
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    //alert('Error data');
                    //alert(xhr.responseText);
                }
            }
        });
    });

    // $('.select-coa').on('select2:select', function(e) {
    //     let posisi = $(this).attr('data-posisi');
    //     let targetId = (posisi === 'D') ? '#infoDebit' : '#infoCredit';
    //     let data = e.params.data;
    //     $(targetId).html(data.info);
    // });

    // $('.select-coa').on('select2:unselect', function(e) {
    //     let posisi = $(this).data('posisi');
    //     let targetId = (posisi === 'D') ? '#infoDebit' : '#infoCredit';
    //     $(targetId).html('&nbsp'); // kosongkan teks kecil
    // });


    $("#get_coa_def").unbind("click");
    $("#get_coa_def").off("click").on("click", function(e) {

        let kode_coa_default_credit = `<?php echo isset($coa_default_credit['kode_coa']) ? $coa_default_credit['kode_coa'] : ''; ?>`;
        let nama_coa_default_credit = `<?php echo isset($coa_default_credit['nama_coa']) ? $coa_default_credit['nama_coa'] : ''; ?>`;

        nm_modif = kode_coa_default_credit + ' - ' + nama_coa_default_credit;
        option = new Option(nm_modif, kode_coa_default_credit, true, true);
        $selectCredit.append(option);

        let kode_coa_default_debit = `<?php echo isset($coa_default_debit['kode_coa']) ? $coa_default_debit['kode_coa'] : ''; ?>`;
        let nama_coa_default_debit = `<?php echo isset($coa_default_debit['nama_coa']) ? $coa_default_debit['nama_coa'] : ''; ?>`;

        nm_modif = kode_coa_default_debit + ' - ' + nama_coa_default_debit;
        option = new Option(nm_modif, kode_coa_default_debit, true, true);
        $selectDebit.append(option);
        
    });


    // btn tambah
    $("#btn-tambah").unbind("click");
    $("#btn-tambah").off("click").on("click", function(e) {
        e.preventDefault();

        let no_pelunasan = "<?php echo $no_pelunasan; ?>";
        let id_summary = "<?php echo ($id_summary); ?>";
        let jenis_koreksi = "<?php echo $jenis_koreksi; ?>";

        $('#btn-tambah').button('loading');
        please_wait(function() {});
        $.ajax({
            type: "POST",
            dataType: "json",
            url: '<?php echo base_url('accounting/pelunasanhutang/save_koreksi') ?>',
            beforeSend: function(e) {
                if (e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            data: {
                no_pelunasan: no_pelunasan,
                id_summary: id_summary,
                jenis_koreksi: jenis_koreksi,
                debit: $("#debit").val(),
                credit: $("#credit").val()

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