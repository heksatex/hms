<style>
    /* Dropdown */
    .select2-results__option {
        white-space: nowrap !important;
        overflow-x: auto !important;
        text-overflow: clip !important;
        -webkit-overflow-scrolling: touch;
    }

    /* Selected element */
    .select2-selection__rendered {
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }

    /* Buat dropdown melebar sesuai isi */
    .select2-container--open .select2-dropdown {
        width: auto !important;
        /* biarkan melebar */
        min-width: 200px;
        /* kasih batas minimal (opsional) */
        max-width: none !important;
        /* hilangkan batas maksimum */
    }


    /* Agar tampilan dropdown tidak melebar terlalu jauh di HP */
    @media screen and (max-width: 768px) {
        .select2-container--open .select2-dropdown {
            width: 100% !important;
            max-width: 100% !important;
            overflow-x: auto !important;
        }
    }

    .select2-container {
        width: 100% !important;
    }

    /* Field error */
    .input-error,
    .select2-error .select2-selection {
        border: 2px solid #e74c3c !important;
    }

    tfoot td {
        background: #f7f7f7;
        font-weight: bold;
    }
</style>
<form class="form-horizontal" id="form_edit_koreksi" name="form_edit_koreksi">

    <!-- === FORM ATAS === -->
    <div class="container-fluid">

        <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Keterangan</label>
            <div class="col-lg-4 col-md-8 col-12">
                <input type="text" class="form-control input-sm" name="keterangan" id="keterangan"
                    value="<?php echo $get_sum->keterangan; ?>" readonly>
            </div>
        </div>

        <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Selisih</label>
            <div class="col-lg-4 col-md-8 col-12">
                <input type="text" class="form-control input-sm text-right" name="selisih" id="selisih"
                    value="<?php echo number_format($get_sum->selisih ?? 0, 2); ?>" readonly>
            </div>
        </div>

        <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Pilih</label>
            <div class="col-lg-4 col-md-8 col-12">
                <label><input type="radio" name="mode_koreksi" value="normal" checked> Normal</label>
                &nbsp;&nbsp;
                <label><input type="radio" name="mode_koreksi" value="split"> Split</label>
            </div>
        </div>

    </div>

    <!-- === NORMAL MODE === -->
    <div id="row-normal" class="container-fluid">
        <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Koreksi</label>
            <div class="col-lg-4 col-md-8 col-12">
                <select name="koreksi[]" class="form-control input-sm koreksi_normal select2" id='koreksi_normal' style="width:100%"></select>
            </div>
        </div>

        <div class="row form-group" id="checkbox_alat_pelunasan_normal" style="display:none">
            <label class="col-lg-4 col-md-4 col-12"></label>
            <div class="col-lg-4 col-md-8 col-12">
                <input type="checkbox" id="alat_pelunasan_normal" class="alat-pelunasan-normal">
                Alat Pelunasan
            </div>
        </div>

        <div class="row form-group">
            <div class="col-lg-4 col-md-4 col-12"></div>
            <div class="col-lg-4 col-md-8 col-12">
                <button type="button" class="btn btn-default btn-xs" id="get_coa_def">Get COA Default</button>
            </div>
        </div>


        <div class="row form-group" id="wrap_faktur" style="display:none">
            <label class="col-lg-4 col-md-4 col-12">Faktur</label>
            <div class="col-lg-4 col-md-8 col-12">
                <select id="faktur" class="form-control select2" style="width:100%">
                    <option value="">-- Pilih Faktur --</option>
                    <!-- nanti akan terisi via ajax/load manual -->
                </select>
            </div>
        </div>


        <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Debit</label>
            <div class="col-lg-4 col-md-8 col-12" id="html_debit">
                <select class="form-control input-sm select-coa" data-posisi="D" name="debit" id="debit"></select>
            </div>
        </div>

        <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Credit</label>
            <div class="col-lg-4 col-md-8 col-12" id="html_credit">
                <select class="form-control input-sm select-coa" data-posisi="C" name="credit" id="credit"></select>
            </div>
        </div>
    </div>

    <!-- === SPLIT MODE === -->
    <div id="row-split" class="container-fluid" style="display:none">

        <!-- <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">CoA</label>
            <div class="col-lg-4 col-md-8 col-12">
                <select class="form-control input-sm select-coa" name="CoA_head" id="CoA_head"></select>
            </div>
        </div> -->

        <!-- <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Target Nominal</label>
            <div class="col-lg-4 col-md-8 col-12"> -->
        <!-- <input type="hidden" class="form-control input-sm text-right formatAngka" name="nominal_head" id="nominal_head" data-decimal="2" readonly> -->
        <!-- </div>
        </div> -->
        <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Nominal (Jurnal)</label>
            <div class="col-lg-1 col-md-6 col-12">
                <input type="text" class="form-control input-sm" name="posisi_credit" id="posisi_credit" value="C" readonly >
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <input type="text" class="form-control input-sm text-right formatAngka" name="nominal_credit" id="nominal_credit" data-decimal="2" readonly>
            </div>
        </div>
        <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Nominal (Jurnal)</label>
            <div class="col-lg-1 col-md-6 col-12">
                <input type="text" class="form-control input-sm" name="posisi_debit" value="D"  id="posisi_debit" readonly>
            </div>
            <div class="col-lg-3 col-md-6 col-12">
                <input type="text" class="form-control input-sm text-right formatAngka" name="nominal_debit" id="nominal_debit" data-decimal="2" readonly>
            </div>
        </div>



        <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Nominal (Non Jurnal)</label>
            <div class="col-lg-4 col-md-8 col-12">
                <input type="text" class="form-control input-sm text-right formatAngka" name="nominal_non" id="nominal_non" data-decimal="2" readonly>
            </div>
        </div>

        <!-- <div class="row form-group">
            <label class="col-lg-4 col-md-4 col-12">Total Nominal</label>
            <div class="col-lg-4 col-md-8 col-12">
                <input type="text" class="form-control input-sm text-right formatAngka" name="total_nominal_head" id="total_nominal_head" data-decimal="2" readonly>
            </div>
        </div> -->

        <label><b>Koreksi</b></label>
        <div class="table-responsive">
            <table class="table table-condesed table-hover rlstable " id="tabel-koreksi-head">
                <thead>
                    <tr>
                        <th class="style bb" style="width:25%">COA</th>
                        <th class="style bb" style="width:25%">Nominal</th>
                        <th class="style bb" style="width:10%">Posisi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><select class="form-control input-sm select-coa" name="CoA_head" id="CoA_head"></select></td>
                        <td><input type="text" class="form-control input-sm text-right formatAngka" name="nominal_head" id="nominal_head" data-decimal="2" readonly></td>
                        <td><input type="text" class="form-control input-sm" name="posisi_head" id="posisi_head" readonly></td>
                    </tr>
                </tbody>
            </table>
        </div>


        <label><b>Detail Koreksi</b></label>
        <div class="table-responsive">
            <table class="table table-condesed table-hover rlstable " id="tabel-koreksi">
                <thead>
                    <tr>
                        <th class="style bb" style="width:5%">No</th>
                        <th class="style bb" style="width:25%">Koreksi</th>
                        <th class="style bb" style="width:25%">COA</th>
                        <th class="style bb" style="width:25%">Nominal</th>
                        <th class="style bb" style="width:10%">Alat Pelunasan</th>
                        <th class="style bb" style="width:10%">Posisi</th>
                        <th class="style bb" style="width:10%">#</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-right"><b>Total:</b></td>
                        <td class="text-right">
                            <b><span id="total_nominal">0.00</span></b>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <td class="text-right"><b>Sisa:</b></td>
                        <td class="text-right">
                            <b><span id="sisa_nominal">0.00</span></b>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <button type="button" class="btn btn-success btn-xs" id="addRow">+ Tambah Baris</button>

</form>



<script type="text/javascript">
    $(document).on('focus', '.select2', function(e) {
        if (e.originalEvent) {
            var s2element = $(this).siblings('select');
            s2element.select2('open');

            // Set focus back to select2 element on closing.
            s2element.on('select2:closing', function(e) {
                s2element.select2('focus');
            });
        }
    });

    // $("#row-split").css("display","none");
    // $("#row-normal").css("display","none");

    // Auto hide/show berdasarkan radio
    function applyMode() {
        let mode = $("input[name='mode_koreksi']:checked").val();
        $("#row-normal").toggle(mode == "normal");
        $("#row-split").toggle(mode == "split");
    }
    $(document).on("change", "input[name='mode_koreksi']", applyMode);

    // panggil saat modal dibuka
    // $(document).ready(applyMode);

    $(document).ready(function() {
        // syncKoreksiOptions(); // supaya koreksi normal punya opsi sama persis
        applyMode(); // tetap pakai script switch normal/split
        let tipe = `<?php echo $tipe_koreksi; ?>`;
        let currency = `<?php echo $tipe_currency; ?>`;
        $('#koreksi_normal').select2({
            allowClear: true,
            placeholder: "Pilih Koreksi",
            ajax: {
                url: "<?php echo base_url(); ?>accounting/pelunasanpiutang/get_list_koreksi_select2",
                type: "POST",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        name: params.term || "",
                        tipe: tipe,
                        currency: currency
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: item.kode,
                            text: item.nama_koreksi,
                            get_coa: item.get_coa // true/false
                        }))
                    };
                }
            }
        });
    });


    // evenet head coa get default
    $(document).on('select2:select', '#koreksi_normal', function(e) {
        let get_coa = e.params.data.get_coa; // true/false
        let debit = $("#debit");
        let credit = $("#credit");
        let html_credit = $("#html_credit");
        let html_debit = $("#html_debit");
        let get_default = $("#get_coa_def");

        $("#koreksi_normal").attr("data-get-coa", get_coa);

        if (get_coa === 'false') {
            // HAPUS select & ganti input text kosong
            get_default.prop("disabled", true);
            html_debit.html(`<select type="text" name="debit[]" class="form-control input-sm" disabled><select>`);
            html_credit.html(`<select type="text" name="credit[]" class="form-control input-sm" disabled><select>`);
        } else {
            get_default.prop("disabled", false);
            // Kembalikan menjadi select2 (load ulang)
            html_debit.html(` <select class="form-control input-sm select-coa" data-posisi="D" name="debit" id="debit"></select>`);
            html_credit.html(` <select class="form-control input-sm select-coa" data-posisi="C" name="credit" id="credit"></select>`);
            let debit_new = $("#debit");
            let credit_new = $("#credit");
            initSelectCoa2(debit_new); // load lagi select2
            initSelectCoa2(credit_new); // load lagi select2
        }
    });

    $("#koreksi_normal").on("change", function() {
        let data = $(this).select2("data")[0];

        // cek indikator, sesuaikan dengan struktur datamu
        if (data && (data.id == 'lebih_bayar' || data.text.includes("lebih bayar"))) {
            $("#wrap_faktur").show();
            initSelectFaktur($("#faktur"))
        } else {
            $("#wrap_faktur").hide();
            $("#faktur").val(null).trigger("change");
        }

        let checkbox = $("#alat_pelunasan_normal");

        if (data.id === "deposit") {
            $("#checkbox_alat_pelunasan_normal").show();
            checkbox.prop("disabled", false); // aktif
        } else {
            $("#checkbox_alat_pelunasan_normal").hide();
            checkbox.prop("checked", false); // auto uncheck
            checkbox.prop("disabled", true); // nonaktif
        }

    });



    $(document).on('select2:clear select2:unselect', '#koreksi_normal', function() {
        let html_credit = $("#html_credit");
        let html_debit = $("#html_debit");
        html_debit.html(` <select class="form-control input-sm select-coa" data-posisi="D" name="debit" id="debit"></select>`);
        html_credit.html(` <select class="form-control input-sm select-coa" data-posisi="C" name="credit" id="credit"></select>`);
        let debit_new = $("#debit");
        let credit_new = $("#credit");
        initSelectCoa2(debit_new); // load lagi select2
        initSelectCoa2(credit_new); // load lagi select2
    });


    function initSelectFaktur(target) {
        let no_pelunasan = "<?php echo $no_pelunasan; ?>";
        target.select2({
            placeholder: "Pilih Faktur",
            allowClear: true,
            ajax: {
                url: "<?php echo base_url(); ?>accounting/pelunasanpiutang/get_list_faktur",
                type: "POST",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term || "",
                        no_pelunasan: no_pelunasan
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(v => ({
                            id: v.faktur_id,
                            text: v.no_faktur
                        }))
                    }
                }
            }
        });
    }


    function initSelectCoa2(target) {

        target.select2({
            allowClear: true,
            placeholder: "Pilih",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo base_url(); ?>accounting/pelunasanhutang/get_list_coa",
                data: function(params) {
                    return {
                        name: params.term,
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

        return

    }

    $(document).off('click', '#get_coa_def').on('click', '#get_coa_def', function() {
        // let row     = $(this).closest('tr');
        let koreksi = $("#koreksi_normal");
        // let posisi  = row.find('.posisi_item').val();
        let no_pelunasan = "<?php echo $no_pelunasan; ?>";
        let id_summary = "<?php echo ($id_summary); ?>";
        let put_debit = $("#debit");
        let put_credit = $("#credit");

        // koreksi.find('.select2-error').removeClass('select2-error');

        // Koreksi wajib
        if (!koreksi.val()) {
            koreksi.addClass("input-error").focus();
            koreksi.next('.select2').addClass("select2-error");
            alert_notify('fa fa-warning', 'Koreksi harus dipilih terlebih dahulu.', 'danger', function() {});
            return false;
        }

        koreksi.next('.select2').removeClass("select2-error");

        get_coa_default(put_debit, koreksi.val(), 'D', no_pelunasan, id_summary);
        get_coa_default(put_credit, koreksi.val(), 'C', no_pelunasan, id_summary);

    });


    function get_coa_default(put, koreksi, posisi, no_pelunasan, id_summary) {
        $.ajax({
            url: "<?php echo base_url(); ?>accounting/pelunasanpiutang/get_default_coa",
            type: "POST",
            dataType: "json",
            data: {
                koreksi: koreksi,
                posisi: posisi,
                no_pelunasan: no_pelunasan,
                id_summary: id_summary
            },
            success: function(res) {

                if (res.status === true) {
                    // isi ke select2 coa
                    let select = put;
                    let option = new Option(res.kode_coa + ' - ' + res.nama_coa, res.kode_coa, true, true);
                    select.append(option).trigger('change');
                } else {
                    alert("Default COA tidak ditemukan.");
                }

            },
            error: function(xhr) {
                alert("Terjadi kesalahan mengambil default COA");
            }
        });
        return;
    }


    let kd_coa_head = `<?php echo isset($coa_head['kode_coa']) ? $coa_head['kode_coa'] : ''; ?>`;
    let nm_coa_head = `<?php echo isset($coa_head['nama_coa']) ? $coa_head['nama_coa'] : ''; ?>`;
    let $selecthead = $("#CoA_head");
    let posisi_head = `<?php echo isset($coa_head['posisi']) ? $coa_head['posisi'] : ''; ?>`;
    let posisi_item = `<?php echo isset($coa_head['posisi_item']) ? $coa_head['posisi_item'] : '' ?>`;

    if (kd_coa_head && nm_coa_head) {
        nm_modif = kd_coa_head + ' - ' + nm_coa_head;
        let option = new Option(nm_modif, kd_coa_head, true, true);
        $selecthead.append(option);
    }

    if (posisi_head) {
        $("#posisi_head").val(posisi_head);
    }

    nominal_head = parseFloat(Math.abs(`<?php echo $get_sum->selisih; ?>`));


    $("#nominal_head").val(nominal_head);
    // $("#sisa_nominal").html($("#nominal_head").val());
    hitungTotal();
    // $(document).ready(function() {
    // initSelectCoa();
    // });

    $('#addRow').on('click', function() {
        let rows = $("#tabel-koreksi tbody tr").length;
        let lastRow = $("#tabel-koreksi tbody tr:last");

        if (rows > 0) {
            if (!validateRow(lastRow)) return;
        }

        let row = $(`
            <tr class='num'>
                <td></td>
                <td>
                    <select name="koreksi[]" class="form-control input-sm koreksi-select select2" style="width:100%"></select>
                </td>

                <td>
                    <div class="input-group">
                        <select name="coa[]" class="form-control coa-select select2" data-tipe="coa" style="width:100%">  </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn  btn-sm get-default-coa" title="Get Default COA">
                                <i class="fa fa-share"></i>
                            </button>
                        </span>
                   </div>
                </td>

                <td><input type="text" name="nominal[]" class="form-control nominal-input text-right formatAngka input-sm"  data-decimal="2"></td>
                <td class="text-center">
                    <input type="checkbox" name="alat_pelunasan[]" class="alat-pelunasan" disabled>
                </td>

              
                <td>
                    <select name="posisi_item[]" class="form-control input-sm posisi_item">
                        <option value="D">D</option>
                        <option value="C">C</option>
                    </select>
                </td>

                <td class="text-center">
                    <button type="button" class="btn btn-danger btn-xs removeRow">x</button>
                </td>
            </tr>`);

        $('#tabel-koreksi tbody').append(row);
        if (posisi_item === 'D' || posisi_item === 'C') {
            row.find('.posisi_item').val(posisi_item);
        }

        initSelectKoreksi();
        // initSelectCoa();
        // initSelectKoreksi(row.find(".koreksi-select"));
        initSelectCoa(row.find(".coa-select"));
        bindFormatAngka(row[0]);
        hitungTotal();
    });


    function validateRow(row) {
        let koreksi = row.find('.koreksi-select');
        let nominal = row.find('.nominal-input');
        let coa = row.find('.coa-select');
        let getCoaReq = row.attr("data-get-coa");

        // Reset error style dulu
        row.find('input, select').removeClass('input-error');
        row.find('.select2-error').removeClass('select2-error');

        // Koreksi wajib
        if (!koreksi.val()) {
            koreksi.addClass("input-error").focus();
            koreksi.next('.select2').addClass("select2-error");
            alert_notify('fa fa-warning', 'Koreksi harus dipilih terlebih dahulu.', 'danger', function() {});
            return false;
        }

        if (row.find('.faktur-select').length && !row.find('.faktur-select').val()) {
            row.find('.faktur-select').addClass("input-error").focus();
            row.find('.faktur-select').next('.select2').addClass("select2-error");
            alert_notify('fa fa-warning', 'Faktur wajib dipilih untuk koreksi lebih bayar', 'danger', function() {});
            return false;
        }

        // Nominal wajib & valid
        let nominalVal = parseFloat(nominal.val() || 0);
        if (!nominalVal || nominalVal <= 0) {
            nominal.addClass("input-error").focus();
            alert_notify('fa fa-warning', 'Nominal harus diisi dan lebih dari 0', 'danger', function() {});
            return false;
        }

        // COA wajib jika select aktif
        if (getCoaReq == "true" && !coa.val()) {
            coa.addClass("input-error").focus();
            coa.next('.select2').addClass("select2-error");
            alert_notify('fa fa-warning', 'Silakan pilih COA dahulu', 'danger', function() {});
            return false;
        }
        // console.log(row);
        return true;
    }



    function initSelectKoreksi() {
        let tipe = `<?php echo $tipe_koreksi; ?>`;
        let currency = `<?php echo $tipe_currency; ?>`;
        $('.koreksi-select').select2({
            allowClear: true,
            placeholder: "Pilih Koreksi",
            ajax: {
                url: "<?php echo base_url(); ?>accounting/pelunasanpiutang/get_list_koreksi_select2",
                type: "POST",
                dataType: "json",
                delay: 250,
                data: function(params) {
                    return {
                        name: params.term || "",
                        tipe: tipe,
                        currency: currency
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(item => ({
                            id: item.kode,
                            text: item.nama_koreksi,
                            get_coa: item.get_coa, // true/false,
                            additional: {
                                get_coa: item.get_coa
                            }
                        }))
                    };
                }
            },
            templateSelection: function(item) {
                if (item.additional) {
                    $(item.element).attr("data-get-coa", item.additional.get_coa);
                }
                return item.text;
            },
            templateResult: function(item) {
                if (item.additional) {
                    $(item.element).attr("data-get-coa", item.additional.get_coa);
                }
                return item.text;
            }
        });
    }



    function initSelectCoa($el) {

        // let $el = $(".coa-select");
        $el.select2({
            allowClear: true,
            placeholder: "Pilih CoA",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo base_url(); ?>accounting/pelunasanhutang/get_list_coa",
                data: function(params) {
                    return {
                        name: params.term,
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
                error: function(xhr, ajaxOptions, thrownError) {}
            }
        });
    }




    // Event ketika koreksi dipilih
    $(document).on('select2:select', '.koreksi-select', function(e) {
        let get_coa = e.params.data.get_coa; // true/false
        let koreksi_id = e.params.data.id;
        let row = $(this).closest('tr');
        let coaField = row.find('td:eq(2)'); // kolom COA
        // let posisiField = row.find('td:eq(5)'); // kolom posisi
        let checkbox = row.find('.alat-pelunasan');
        let setposisiField = row.find('td:eq(5)');
        let posisiField = row.find('.posisi_item');
        // simpan data get_coa
        row.attr("data-get-coa", get_coa);
        // alert(get_coa);

        if (get_coa === 'false' && koreksi_id == 'lebih_bayar') {

            coaField.html(`
                    <select name="faktur[]" class="form-control faktur-select select2" style="width:100%"></select>
                `);

            initSelectFaktur(row.find('.faktur-select')); // jalankan ajax load faktur
            return;
        }


        if (koreksi_id == 'deposit') {
            checkbox.prop("disabled", false);
        } else {
            checkbox.prop("checked", false).prop("disabled", true);
        }

        if (get_coa === 'false') {
            // HAPUS select & ganti input text kosong
            coaField.html(`<select type="text" name="coa-select[]" class="form-control input-sm coa-select" disabled><select>`);
            setposisiField.html('<input type="text"  name="posisi_item[]" class="form-control input-sm posisi_item" readonly>');
            posisiField.val('');
        } else {
            // Kembalikan menjadi select2 (load ulang)
            setposisiField.html(`<select name="posisi_item[]" class="form-control input-sm posisi_item">
                                    <option value="D">D</option>
                                    <option value="C">C</option>
                                </select>`);
            posisiField.val(posisi_item);
            coaField.html(`<div class="input-group">
                        <select name="coa[]" class="form-control coa-select" data-tipe="coa" style="width:100%">  </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn  btn-sm get-default-coa" title="Get Default COA">
                                <i class="fa fa-share"></i>
                            </button>
                        </span>
                   </div>`);

            initSelectCoa(row.find(".coa-select")); // load lagi select2
        }
        hitungTotal();
    });


    $(document).on('select2:clear select2:unselect', '.koreksi-select', function() {
        let row = $(this).closest('tr');
        let checkbox = row.find('.alat-pelunasan');
        let coaField = row.find('td:eq(2)'); // kolom COA
        row.find(".coa-select").val(null).trigger("change");
        coaField.html(`<div class="input-group">
                        <select name="coa[]" class="form-control coa-select" data-tipe="coa" style="width:100%">  </select>
                        <span class="input-group-btn">
                            <button type="button" class="btn  btn-sm get-default-coa" title="Get Default COA">
                                <i class="fa fa-share"></i>
                            </button>
                        </span>
                   </div>`);
        checkbox.prop("checked", false).prop("disabled", true);
        initSelectCoa(row.find(".coa-select")); // load lagi select2
    });

    // $(".get-default-coa").unbind("click");
    $(document).off('click', '.get-default-coa').on('click', '.get-default-coa', function() {
        let row = $(this).closest('tr');
        let koreksi = row.find('.koreksi-select').val();
        let posisi = row.find('.posisi_item').val();
        let no_pelunasan = "<?php echo $no_pelunasan; ?>";
        let id_summary = "<?php echo ($id_summary); ?>";

        if (!koreksi) {
            alert("Pilih koreksi dulu.");
            return;
        }

        $.ajax({
            url: "<?php echo base_url(); ?>accounting/pelunasanpiutang/get_default_coa",
            type: "POST",
            dataType: "json",
            data: {
                koreksi: koreksi,
                posisi: posisi,
                no_pelunasan: no_pelunasan,
                id_summary: id_summary
            },
            success: function(res) {

                if (res.status === true) {
                    // isi ke select2 coa
                    let select = row.find('.coa-select');
                    let option = new Option(res.kode_coa + ' - ' + res.nama_coa, res.kode_coa, true, true);
                    select.append(option).trigger('change');
                } else {
                    alert("Default COA tidak ditemukan.");
                }

            },
            error: function(xhr) {
                alert("Terjadi kesalahan mengambil default COA");
            }
        });
    });




    //select 2 coa
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


    // function hitungTotal() {

    //     let total_jurnal = 0; // get_coa = true
    //     let total_non = 0; // get_coa = false
    //     let total = 0;
    //     let total_posisi1 = 0;
    //     let total_posisi2 = 0;
    //     let head_val = $("#nominal_head").val();
    //     let head2_val = $("#nominal_head2").val();
    //     let head = parseFloat(unformatNumber(head_val)) || 0;
    //     let head2 = parseFloat(unformatNumber(head2_val)) || 0;

    //     $("#tabel-koreksi tbody tr").each(function() {

    //         let row = $(this);
    //         let nominal = parseFloat(unformatNumber(row.find(".nominal-input").val())) || 0;
    //         let posisi = row.find(".posisi_item").val();

    //         let dataKoreksi = row.find(".koreksi-select").select2("data")[0];
    //         let get_coa = dataKoreksi?.get_coa;

    //         // Fallback untuk baris lama
    //         if (get_coa === undefined) {
    //             get_coa = row.find(".koreksi-select option:selected").data("get-coa") || "false";
    //         }

    //         if (get_coa === "true" || get_coa === true) {
    //             if (posisi === 'D') {
    //                 (head_val === 'D') ? total_posisi1 += nominal: total_posisi2 += nominal;
    //             } else { // C
    //                 (head_val === 'C') ? total_posisi1 += nominal: total_posisi2 += nominal;
    //             }
    //             // total_jurnal += nominal;
    //         } else {
    //             total_non += nominal;
    //         }

    //         total += nominal;

    //         // console.log("get_coa:", get_coa, " nominal:", nominal);
    //     });

    //     let sisa = head - total;

    //     // Tampilkan total
    //     $("#nominal_head").text(
    //         total.toLocaleString("en-US", {
    //             minimumFractionDigits: 2,
    //             maximumFractionDigits: 2
    //         })
    //     );


    //     // TAMPILKAN HASILNYA
    //     $("#nominal_jurnal").val(
    //         total_posisi1.toLocaleString("en-US", {
    //             minimumFractionDigits: 2,
    //             maximumFractionDigits: 2
    //         })
    //     );

    //     $("#nominal_jurnal2").val(
    //         total_posisi2.toLocaleString("en-US", {
    //             minimumFractionDigits: 2,
    //             maximumFractionDigits: 2
    //         })
    //     );

    //     $("#nominal_non").val(
    //         total_non.toLocaleString("en-US", {
    //             minimumFractionDigits: 2,
    //             maximumFractionDigits: 2
    //         })
    //     );

    //     // Tampilkan sisa
    //     $("#sisa_nominal").text(
    //         sisa.toLocaleString("en-US", {
    //             minimumFractionDigits: 2,
    //             maximumFractionDigits: 2
    //         })
    //     );

    //     // Opsional → beri warna jika minus atau pas
    //     if (sisa < 0) {
    //         $("#sisa_nominal").css("color", "red");
    //     } else if (sisa === 0) {
    //         $("#sisa_nominal").css("color", "green");
    //     } else {
    //         $("#sisa_nominal").css("color", "black");
    //     }
    // }

    function hitungTotal() {

        let total_jurnal_1 = 0; // sesuai posisi_head
        let total_jurnal_2 = 0; // sesuai posisi_head2
        let total_non = 0;
        let total_all = 0;

        let posisi_credit = $("#posisi_credit").val(); // contoh: C
        let posisi_debit  = $("#posisi_debit").val(); // contoh: D
        let head_posisi   = $("#posisi_head").val() // D/C
        let head = parseFloat(unformatNumber($("#nominal_head").val())) || 0;

        $("#tabel-koreksi tbody tr").each(function() {

            let row = $(this);
            let nominal = parseFloat(unformatNumber(row.find(".nominal-input").val())) || 0;
            let posisi_item = row.find(".posisi_item").val();

            if (nominal <= 0) return;

            total_all += nominal;

            let dataKoreksi = row.find(".koreksi-select").select2("data")[0];
            let get_coa = dataKoreksi?.get_coa;

            // fallback legacy
            if (get_coa === undefined) {
                get_coa = row.find(".koreksi-select option:selected").data("get-coa") || "false";
            }

            if (get_coa === true || get_coa === "true") {

                if (posisi_item === posisi_credit) {
                    total_jurnal_1 += nominal;
                } else if (posisi_item === posisi_debit) {
                    total_jurnal_2 += nominal;
                }

             

            } else {
                total_non += nominal;
            }
        });

        if (head_posisi === posisi_credit) {
            total_jurnal_1 += head;
        } else if (head_posisi === posisi_debit) {
            total_jurnal_2 += head;
        }

        let sisa = head - total_all;
        

        // // ====== SET VALUE ======
        // $("#nominal_jurnal").val(formatNumber(total_jurnal_1));
        // $("#nominal_jurnal2").val(formatNumber(total_jurnal_2));
        // $("#nominal_non").val(formatNumber(total_non));

        // $("#total_nominal").text(formatNumber(total_all));
        // $("#sisa_nominal").text(formatNumber(sisa));

        //     // TAMPILKAN HASILNYA
        $("#nominal_credit").val(
            total_jurnal_1.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        );

        $("#nominal_debit").val(
            total_jurnal_2.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        );

        $("#nominal_non").val(
            total_non.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        );

        // Tampilkan sisa
        $("#sisa_nominal").text(
            sisa.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        );

        // ====== WARNA SISA ======
        if (sisa < 0) {
            $("#sisa_nominal").css("color", "red");
        } else if (sisa === 0) {
            $("#sisa_nominal").css("color", "green");
        } else {
            $("#sisa_nominal").css("color", "black");
        }
    }

    $(document).on('change', '.posisi_item', function() {
        hitungTotal();
    });


    function hitungTotal1() {
        let total = 0;

        // Loop semua nominal detail
        $("input[name='nominal[]']").each(function() {
            let raw = unformatNumber($(this).val());
            let val = parseFloat(raw) || 0;
            total += val;
        });

        // Nominal head (pastikan mengambil tanpa format juga)
        let head = parseFloat(unformatNumber($("#nominal_head").val())) || 0;

        // Hitung sisa
        let sisa = head - total;

        // Tampilkan total
        $("#total_nominal").text(
            total.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        );

        // Tampilkan sisa
        $("#sisa_nominal").text(
            sisa.toLocaleString("en-US", {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            })
        );

        // Opsional → beri warna jika minus atau pas
        if (sisa < 0) {
            $("#sisa_nominal").css("color", "red");
        } else if (sisa === 0) {
            $("#sisa_nominal").css("color", "green");
        } else {
            $("#sisa_nominal").css("color", "black");
        }
    }



    $(document).on("keyup change", "input[name='nominal[]']", function() {
        hitungTotal();
    });

    $(document).on("click", ".removeRow", function() {
        $(this).closest("tr").remove();
        hitungTotal();
    });


    // btn tambah
    $("#btn-tambah").unbind("click");
    $("#btn-tambah").off("click").on("click", function(e) {
        e.preventDefault();

        let no_pelunasan = "<?php echo $no_pelunasan; ?>";
        let id_summary = "<?php echo ($id_summary); ?>";
        let mode = $("input[name='mode_koreksi']:checked").val(); // "normal" / "koreksi"
        // console.log(mode);

        if (!mode) {
            alert_notify('fa fa-warning', 'Mode Koreksi Normal atau Split belum dipilih !', 'danger', function() {});
        } else {

            if (mode === 'normal') {

                let koreksi = $("#koreksi_normal");
                let data_koreksi = koreksi.select2("data")[0]; // ambil objek select2
                let debit = $("#debit");
                let credit = $("#credit");
                let faktur = $("#faktur");

                // Validasi koreksi dipilih
                if (!koreksi.val()) {
                    koreksi.next('.select2').addClass("select2-error");
                    koreksi.select2("open");
                    return alert_notify('fa fa-warning', 'Koreksi harus dipilih terlebih dahulu !', 'danger', function() {});
                } else {
                    koreksi.next(".select2").removeClass("select2-error");
                }

                if (koreksi.val() === 'lebih_bayar' && !faktur.val()) {
                    faktur.next('.select2').addClass("select2-error");
                    faktur.select2("open");
                    return alert_notify('fa fa-warning', 'faktur harus dipilih terlebih dahulu !', 'danger', function() {});
                } else {
                    faktur.next(".select2").removeClass("select2-error");
                }

                // Cek apakah COA wajib
                let get_coa = data_koreksi.get_coa; // value harus sudah dikirim dari select2 ajax

                if (get_coa == 'true') {

                    // debit wajib isi
                    if (!debit.val()) {
                        debit.next('.select2').addClass("select2-error");
                        debit.select2("open");
                        return alert_notify('fa fa-warning', 'Debit harus diisi !', 'danger', function() {});
                    } else {
                        debit.next(".select2").removeClass("select2-error");
                    }

                    // credit wajib isi
                    if (!credit.val()) {
                        credit.next('.select2').addClass("select2-error");
                        credit.select2("open");
                        return alert_notify('fa fa-warning', 'Credit harus diisi !', 'danger', function() {});
                    } else {
                        credit.next(".select2").removeClass("select2-error");
                    }
                }

                // return;
            } else if (mode === 'split') {


                let valid = true;
                let pesan = "";
                let rows = $("#tabel-koreksi tbody tr").length;
                let coa_head = $("#CoA_head");
                let posisi_head = $("#posisi_head");
                let nominal_head = $("#nominal_head");
                let nominal_head_val = parseFloat(unformatNumber($("#nominal_head").val())) || 0;

                if (!coa_head.val()) {
                    coa_head.next('.select2').addClass("select2-error");
                    coa_head.select2("open");
                    return alert_notify('fa fa-warning', 'CoA harus dipilih terlebih dahulu !', 'danger', function() {});
                }

                if (!posisi_head.val()) {
                    posisi_head.focus();
                    return alert_notify('fa fa-warning', 'Posisi tidak boleh kosong !', 'danger', function() {});
                }

                if (!nominal_head_val || nominal_head_val <= 0) {
                    nominal_head.focus();
                    return alert_notify('fa fa-warning', 'Nominal tidak boleh kosong !', 'danger', function() {});
                }

                if (rows === 0) {
                    alert_notify('fa fa-warning', 'Belum ada baris koreksi ditambahkan!', 'danger', function() {});
                    return;
                }

                let total_items = 0;
                $("input[name='nominal[]']").each(function() {
                    total_items += parseFloat(unformatNumber($(this).val())) || 0;
                });




                $("#tabel-koreksi tbody tr").each(function(index) {
                    let row = $(this);

                    let koreksi = row.find(".koreksi-select");
                    let coa = row.find(".coa-select");
                    let nominal = row.find(".nominal-input");
                    let posisi = row.find(".posisi_item");
                    let faktur = row.find(".faktur-select");

                    // ambil atribut get_coa dari data select2
                    let dataKoreksi = koreksi.select2('data')[0];
                    let get_coa = dataKoreksi ? dataKoreksi.get_coa : null; // "true"/"false"
                    // console.log(dataKoreksi);
                    // let get_coa     = dataKoreksi.get_coa ?? null; 
                    // --------------- VALIDASI -----------------

                    // Koreksi wajib ada
                    if (!koreksi.val()) {
                        pesan = `Baris ${index+1}: Koreksi belum dipilih!`;
                        valid = false;
                        row.find(".koreksi-select").next('.select2').addClass("select2-error");
                        row.find(".koreksi-select").focus();
                        return false;
                    } else {
                        row.find(".koreksi-select").next('.select2').removeClass("select2-error");
                    }

                    // Jika get_coa = true → COA wajib
                    // alert(coa.val());
                    // alert(get_coa);
                    if ((get_coa === "true" || get_coa === true) && (!coa.val() || coa.val() === "" || coa.val() === null)) {
                        alert('masuk');
                        pesan = `Baris ${index+1}: COA harus dipilih`;
                        valid = false;
                        row.find(".coa-select").next('.select2').addClass("select2-error");
                        row.find(".coa-select").focus();
                        return false;
                    } else {
                        row.find(".coa-select").next('.select2').removeClass("select2-error");
                    }

                    // Nominal wajib ada dan harus angka > 0
                    if (!nominal.val() || parseFloat(nominal.val()) <= 0) {
                        pesan = `Baris ${index+1}: Nominal harus lebih dari 0`;
                        valid = false;
                        row.find(".nominal-input").addClass("input-error");
                        row.find(".nominal-input").focus();
                        return false;
                    } else {
                        row.find(".nominal-input").removeClass("input-error");
                    }

                    if (koreksi.val() === 'lebih_bayar' && (!faktur.val() || faktur.val() === null || faktur.val() === "")) {
                        pesan = `Baris ${index+1}: Faktur harus dipilih`;
                        valid = false;
                        row.find(".faktur-select").next('.select2').addClass("select2-error");
                        row.find(".faktur-select").focus();
                        return false;
                    } else {
                        row.find(".faktur-select").removeClass("select2-error");
                    }

                });


                if (!valid) {
                    alert_notify('fa fa-warning', pesan, 'danger', function() {});
                    return;
                }

                if (nominal_head_val !== total_items) {
                    // alert_notify('fa fa-warning', 'Nominal tidak sama !', 'danger', function() {});
                    // return;
                }


            } else {
                alert_notify('fa fa-warning', 'Mode Koreksi tidak valid !', 'danger', function() {});
                return;
            }


            // =================== SIMPAN DATA ====================
            $('#btn-tambah').button('loading');
            please_wait(function() {});

            let payload = {
                no_pelunasan: no_pelunasan,
                id_summary: id_summary,
                mode: mode
            };

            // ----------------- MODE NORMAL ----------------- //
            if (mode === 'normal') {
                payload.koreksi = $("#koreksi_normal").val();
                payload.debit = $("#debit").val();
                payload.credit = $("#credit").val();
                payload.faktur = $("#faktur").val();
                payload.checkox_alat = $("#alat_pelunasan_normal").is(":disabled") ? false : $("#alat_pelunasan_normal").is(":checked");
            }

            // ----------------- MODE SPLIT ----------------- //
            if (mode === 'split') {


                let detail = [];

                $("#tabel-koreksi tbody tr").each(function() {
                    let row = $(this);
                    // let koreksiVal = row.find(".koreksi-select").length ? (row.find(".koreksi-select").val() || "") : "";
                    // let coaVal = row.find(".coa-select").length ? (row.find(".coa-select").val() || "") : "";
                    // let nominalVal = row.find(".nominal-input").length ? (row.find(".nominal-input").val() || "") : "";
                    // let posisiVal = row.find(".posisi_item").length ? (row.find(".posisi_item").val() || "") : "";
                    // let fakturVal = row.find(".faktur-select").length ? (row.find(".faktur-select").val() || "") : "";
                    detail.push({
                        koreksi: row.find(".koreksi-select").val(),
                        coa: row.find(".coa-select").val(),
                        nominal: parseFloat(unformatNumber(row.find(".nominal-input").val())) || 0,
                        posisi: row.find(".posisi_item").val(),
                        faktur: row.find(".faktur-select").val(),
                        checkbox_alat: row.find(".alat-pelunasan").is(":enabled") && row.find(".alat-pelunasan").is(":checked") ? "true" : "false" // kalau enabled baru cek centang

                    });
                });

                payload.coa_head = $("#CoA_head").val();
                payload.posisi_head = $("#posisi_head").val();
                payload.nominal_head = parseFloat(unformatNumber($("#nominal_head").val())) || 0;
                payload.nominal_credit = parseFloat(unformatNumber($("#nominal_credit").val())) || 0;
                payload.nominal_debit = parseFloat(unformatNumber($("#nominal_debit").val())) || 0;
                payload.nominal_non = parseFloat(unformatNumber($("#nominal_non").val())) || 0;
                payload.detail = JSON.stringify(detail); // <-- KIRIM ARRAY SPLIT KE BACKEND
            }


            // ================= AJAX POST ================
            $.ajax({
                type: "POST",
                url: "<?php echo base_url('accounting/pelunasanpiutang/save_koreksi') ?>",
                data: payload,
                dataType: "json",
                success: function(data) {
                    unblockUI(function() {
                        setTimeout(function() {
                            alert_notify(data.icon, data.message, data.type, function() {}, 1000);
                            if (data.type === 'success') $('#tambah_data').modal('hide');
                        });
                    });
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



        }
    });
</script>