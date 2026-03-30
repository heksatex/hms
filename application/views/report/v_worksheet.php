<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
    <style type="text/css">
        h3 {
            display: block !important;
            text-align: center !important;
        }

        .divListviewHead table {
            display: block;
            height: calc(96vh - 250px);
            overflow-x: auto;
        }

        table tbody tr td {
            padding: 0px 5px 0px 5px !important;
        }

        .bold {
            font-weight: bold;
        }

        @media (max-width: 990px) {
            .form-group {
                padding-left: 15px;
                padding-right: 15px;
            }

            #btn-generate,
            #btn-excel {
                display: block;
                width: 100%;
                margin-top: 8px;
            }
        }

        /*
    .btn-setTgl {
      height: 22px;
      min-width: 40px;
    }
    */
    </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- main -header -->
        <header class="main-header">
            <?php $this->load->view("admin/_partials/main-menu.php") ?>
            <?php $this->load->view("admin/_partials/topbar.php") ?>
        </header>

        <!-- Menu Side Bar -->
        <aside class="main-sidebar">
            <?php $this->load->view("admin/_partials/sidebar.php") ?>
        </aside>

        <!-- Content Wrapper-->
        <div class="content-wrapper">
            <!-- Content Header (Status - Bar) -->
            <section class="content-header">
            </section>

            <!-- Main content -->
            <section class="content">
                <!--  box content -->
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b>Worksheet</b></h3>
                    </div>
                    <div class="box-body">

                        <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <label>Periode </label>
                                        </div>
                                        <div class="col-md-4">
                                            <div class='input-group'>
                                                <input type="text" class="form-control input-sm" name="tgldari" id="tgldari" required="">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <label>s/d</label>
                                        </div>
                                        <div class="col-md-4">
                                            <div class='input-group'>
                                                <input type="text" class="form-control input-sm" name="tglsampai" id="tglsampai" required="">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <label> </label>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="checkbox" name="hidden_check" id="hidden_check" checked>
                                            Sembuyikan Data Kosong
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-refresh"></i> Generate</button>
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                                </div>
                            </div>

                        </form>

                        <div class="box-body">
                            <div class="col-sm-12 table-responsive">
                                <div class="table_scroll">
                                    <div class="table_scroll_head">
                                        <div class="divListviewHead">
                                            <table id="example1" class="table table-condesed table-hover" border="1">
                                                <thead>
                                                    <tr>
                                                        <th class="style bb no">No. </th>
                                                        <th class='style bb' style="min-width: 5px">Kode CoA</th>
                                                        <th class='style bb' style="min-width: 200px">Nama Acc</th>
                                                        <th class='style bb' style="min-width: 120px">Saldo Awal (D)</th>
                                                        <th class='style bb' style="min-width: 120px">Saldo Awal (C)</th>
                                                        <th class='style bb' style="min-width: 120px">Mutasi (D)</th>
                                                        <th class='style bb' style="min-width: 120px">Mutasi (C)</th>
                                                        <th class='style bb' style="min-width: 120px">N.Percobaan (D)</th>
                                                        <th class='style bb' style="min-width: 120px">N.Percobaan (C)</th>
                                                        <th class='style bb' style="min-width: 120px">Neraca (D)</th>
                                                        <th class='style bb' style="min-width: 120px">Neraca (C)</th>
                                                        <th class='style bb' style="min-width: 120px">Rugi Laba (D)</th>
                                                        <th class='style bb' style="min-width: 120px">Rugi Laba (C)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="13">Tidak ada Data</td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div id="example1_processing" class="table_processing" style="display: none; z-index:5;">
                                                Processing...
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

    </div>

    <?php $this->load->view("admin/_partials/js.php"); ?>

    <div id="load_modal">
        <!-- Load Partial Modal -->
        <?php $this->load->view("admin/_partials/modal.php") ?>
    </div>

    <script type="text/javascript">
        // set date tgldari
        $('#tgldari').datetimepicker({
            // defaultDate: new Date().toLocaleString('en-US', {
            //     timeZone: 'Asia/Jakarta'
            // }),
            defaultDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1), // ⬅️ tanggal 1 bulan ini
            format: 'D-MMMM-YYYY',
            ignoreReadonly: true,
            // maxDate: new Date()
        });

        // set date tglsampai
        $('#tglsampai').datetimepicker({
            defaultDate: (function() {
                const now = new Date(
                    new Date().toLocaleString('en-US', {
                        timeZone: 'Asia/Jakarta'
                    })
                );

                // set ke akhir bulan
                return new Date(now.getFullYear(), now.getMonth() + 1, 0);
            })(),
            format: 'D-MMMM-YYYY',
            ignoreReadonly: true
        });


        var arr_filter = [];


        // btn generate
        $("#btn-generate").on('click', function() {

            var tgldari = $('#tgldari').val();
            var tglsampai = $('#tglsampai').val();
            var this_btn = $(this);

            var tgldari_2 = $('#tgldari').data("DateTimePicker").date();
            var tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();

            if (tgldari == '' || tglsampai == '') {
                alert_modal_warning('Periode Tanggal Harus diisi !');

            } else if (tglsampai_2 < tgldari_2) {
                alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
            } else {
                arr_filter = [];
                process_bukubesar(this_btn);

            }
        });


        function formatNumber(n) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(n);
        }

        function process_bukubesar(this_btn) {
            var tgldari = $('#tgldari').val();
            var tglsampai = $('#tglsampai').val();
            var check_hidden = $("#hidden_check").is(':checked');

            $("#example1_processing").css('display', ''); // show loading
            this_btn.button('loading');
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?php echo site_url('report/worksheet/loadData') ?>",
                data: {
                    tgldari: tgldari,
                    tglsampai: tglsampai,
                    checkhidden: check_hidden
                },
                success: function(data) {

                    if (data.status == 'failed') {
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type, function() {});
                            }, 1000);
                        });
                        this_btn.button('reset');
                    } else {
                        $("#example1 tbody").remove();
                        let no = 1;
                        let empty = true;
                        let s_awal_debit = 0;
                        let s_awal_credit = 0;
                        let mutasi_debit = 0;
                        let mutasi_credit = 0;
                        let n_percobaan_debit = 0;
                        let n_percobaan_credit = 0;
                        let neraca_debit = 0;
                        let neraca_credit = 0;
                        let rugi_laba_debit = 0;
                        let rugi_laba_credit = 0;
                        let s_akhir = 0;
                        let tbody = $("<tbody />");

                        arr_filter.push({
                            tgldari: tgldari,
                            tglsampai: tglsampai,
                            checkhidden: check_hidden
                        });

                        $.each(data.record.data, function(key, value) {

                            empty = false;

                            func2 = "view_detail2('" + value.kode_acc + "')";
                            var tr = $("<tr>").append(
                                $("<td>").html(no),
                                $("<td align=''>").text(value.kode_acc),
                                $("<td align=''>").text(value.nama_acc),
                                $("<td align='right'>").text(formatNumber(value.saldo_awal_debit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.saldo_awal_credit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.mutasi_debit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.mutasi_credit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.n_percobaan_debit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.n_percobaan_credit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.neraca_debit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.neraca_credit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.rugi_laba_debit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.rugi_laba_credit.toFixed(2))),
                            );

                            tbody.append(tr);
                            no++;
                            s_awal_debit = s_awal_debit + value.saldo_awal_debit;
                            s_awal_credit = s_awal_credit + value.saldo_awal_credit;
                            mutasi_debit = mutasi_debit + value.mutasi_debit;
                            mutasi_credit = mutasi_credit + value.mutasi_credit;
                            n_percobaan_debit = n_percobaan_debit + value.n_percobaan_debit;
                            n_percobaan_credit = n_percobaan_credit + value.n_percobaan_credit;
                            neraca_debit = neraca_debit + value.neraca_debit;
                            neraca_credit = neraca_credit + value.neraca_credit;
                            rugi_laba_debit = rugi_laba_debit + value.rugi_laba_debit;
                            rugi_laba_credit = rugi_laba_credit + value.rugi_laba_credit;
                        });

                        if (empty == true) {
                            var tr = $("<tr>").append($("<td colspan='13'>").text('Tidak ada Data'));
                            tbody.append(tr);
                        } else {
                            // tbody.append("<tr><td colspan='13'>&nbsp</td></tr>");
                            // =======================
                            // HITUNG LABA / RUGI
                            // =======================

                            let selisih_rl = rugi_laba_credit - rugi_laba_debit;
                            let selisih_neraca = neraca_credit - neraca_debit;
                            let rugi = 0;
                            let laba = 0;
                            let posisi_rl_debit = 0;
                            let posisi_rl_credit = 0;
                            let posisi_neraca_debit = 0;
                            let posisi_neraca_credit = 0;

                            if (selisih_rl > 0) {
                                laba = selisih_rl;
                            } else if (selisih_rl < 0) {
                                rugi = Math.abs(selisih_rl);
                            }

                            if (rugi_laba_credit > rugi_laba_debit) {
                                posisi_rl_debit = selisih_rl;
                                posisi_rl_credit = 0;
                            } else if (rugi_laba_credit < rugi_laba_debit) {
                                posisi_rl_debit = 0;
                                posisi_rl_credit = Math.abs(selisih_rl);
                            }


                            if (neraca_credit > neraca_debit) {
                                posisi_neraca_debit = selisih_neraca;
                                posisi_neraca_credit = 0;
                            } else if (neraca_credit < neraca_debit) {
                                posisi_neraca_debit = 0;
                                posisi_neraca_credit = Math.abs(selisih_neraca);
                            }

                            // =======================
                            // TOTAL SEBELUM SELISIH
                            // =======================

                            let tr_total = $("<tr>").append(
                                $("<td colspan='3' align='right' class='bold'>").text('Total :'),
                                $("<td align='right' class='bold'>").text(formatNumber(s_awal_debit.toFixed(2))),
                                $("<td align='right' class='bold'>").text(formatNumber(s_awal_credit.toFixed(2))),
                                $("<td align='right' class='bold'>").text(formatNumber(mutasi_debit.toFixed(2))),
                                $("<td align='right' class='bold'>").text(formatNumber(mutasi_credit.toFixed(2))),
                                $("<td align='right' class='bold'>").text(formatNumber(n_percobaan_debit.toFixed(2))),
                                $("<td align='right' class='bold'>").text(formatNumber(n_percobaan_credit.toFixed(2))),
                                $("<td align='right' class='bold'>").text(formatNumber(neraca_debit.toFixed(2))),
                                $("<td align='right' class='bold'>").text(formatNumber(neraca_credit.toFixed(2))),
                                $("<td align='right' class='bold'>").text(formatNumber(rugi_laba_debit.toFixed(2))),
                                $("<td align='right' class='bold'>").text(formatNumber(rugi_laba_credit.toFixed(2)))
                            );

                            tbody.append(tr_total);


                            // =======================
                            // BARIS LABA / RUGI
                            // =======================
                            let caption_lr = '';
                            if (laba > 0) {
                                caption_lr = 'LABA';
                            } else if (rugi > 0) {
                                caption_lr = 'RUGI';
                            }


                            tbody.append(
                                $("<tr>").append(
                                    $("<td colspan='9' align='right'><b> " + caption_lr + " </b></td>"),
                                    $("<td align='right'><b>" + formatNumber(posisi_neraca_debit.toFixed(2)) + "</b></td>"),
                                    $("<td align='right'><b>" + formatNumber(posisi_neraca_credit.toFixed(2)) + "</b></td>"),
                                    $("<td align='right'><b>" + formatNumber(posisi_rl_debit.toFixed(2)) + "</b></td>"),
                                    $("<td align='right'><b>" + formatNumber(posisi_rl_credit.toFixed(2)) + "</b></td>")
                                )
                            );

                            // =======================
                            // TOTAL AKHIR (BALANCE)
                            // =======================

                            let total_neraca_debit = neraca_debit;
                            let total_neraca_credit = neraca_credit;

                            let total_rl_debit = rugi_laba_debit;
                            let total_rl_credit = rugi_laba_credit;

                            total_neraca_credit += posisi_neraca_credit;
                            total_neraca_debit += posisi_neraca_debit;

                            total_rl_debit += posisi_rl_debit;
                            total_rl_credit += posisi_rl_credit;


                            tbody.append(
                                $("<tr>").append(
                                    $("<td colspan='9' align='right'><b>Total :</b></td>"),
                                    $("<td align='right'><b>" + formatNumber(total_neraca_debit.toFixed(2)) + "</b></td>"),
                                    $("<td align='right'><b>" + formatNumber(total_neraca_credit.toFixed(2)) + "</b></td>"),
                                    $("<td align='right'><b>" + formatNumber(total_rl_debit.toFixed(2)) + "</b></td>"),
                                    $("<td align='right'><b>" + formatNumber(total_rl_credit.toFixed(2)) + "</b></td>")
                                )
                            );
                            tr3 = $("<tr>").append(
                                $("<td colspan='9'>").text(''),
                            );
                            tbody.append(tr3);
                        }

                        $("#example1").append(tbody); // append parents

                        this_btn.button('reset');
                    }
                    $("#example1_processing").css('display', 'none'); // hidden loading

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert(jqXHR.responseText);
                    $("#example1_processing").css('display', 'none'); // hidden loading
                    this_btn.button('reset');
                }
            });

        }


        function view_detail2(kode_coa) {
            var arrStr = encodeURIComponent(JSON.stringify(arr_filter));

            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu !');
            } else {
                var url = '<?php echo base_url() ?>report/bukubesar/detail';
                window.open(url + '?coa=' + kode_coa + '&&params=' + arrStr, '_blank');
            }
        }


        // klik btn excel
        $('#btn-excel').click(function() {

            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu !');
            } else {

                $.ajax({
                    "type": 'POST',
                    "url": "<?php echo site_url('report/worksheet/export_excel') ?>",
                    "data": {
                        arr_filter: arr_filter
                    },
                    "dataType": 'json',
                    beforeSend: function() {
                        $('#btn-excel').button('loading');
                    },
                    error: function() {
                        alert('Error Export Excel');
                        $('#btn-excel').button('reset');
                    }
                }).done(function(data) {
                    if (data.status == "failed") {
                        alert_notify(data.icon, data.message, data.type, function() {});
                    } else {
                        var $a = $("<a>");
                        $a.attr("href", data.file);
                        $("body").append($a);
                        $a.attr("download", data.filename);
                        $a[0].click();
                        $a.remove();
                    }
                    $('#btn-excel').button('reset');
                });
            }
        });
    </script>

</body>

</html>