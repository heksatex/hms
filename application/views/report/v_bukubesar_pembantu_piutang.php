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

        .style_space {
            white-space: nowrap !important;
            /* font-weight: 700; */
            background: #F0F0F0;
            border-top: 2px solid #ddd !important;
            border-bottom: 2px solid #ddd !important;
        }

        .smarttip {
            cursor: pointer;
        }

        #globalTooltip {
            position: fixed;
            background: #000;
            color: #fff;
            padding: 6px 10px;
            border-radius: 4px;
            font-size: 11px;
            z-index: 999999;
            max-width: 220px;
            white-space: normal;
            display: none;
        }

        .depo {
            background: #729fe9ff;
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
                        <h3 class="box-title"><b>Buku Besar Pembantu Piutang</b></h3>
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
                                            <label>Currency</label>
                                        </div>
                                        <div class="col-md-4 currency-radio-wrapper">
                                            <div class="form-inline">
                                                <label class="radio-inline">
                                                    <input type="radio" name="currency" value="valas"> Valas
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="currency" value="rp" checked> Rp
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="hidden_check" id="hidden_check" checked>
                                            Sembuyikan Data Kosong
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                                        <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                                        <button type="button" class="btn btn-sm btn-default" name="btn-pdf" id="btn-pdf" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-pdf-o" style="color:red"></i> PDF</button>
                                    </div>
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
                                                        <th class="style bb no" rowspan="2">No. </th>
                                                        <th class='style bb' style="min-width: 200px" rowspan="2">Customer</th>
                                                        <th class='style bb' style="min-width: 150px" rowspan="2">Saldo Awal</th>
                                                        <!-- <th class='style bb' style="min-width: 90px; white-space: wrap;" rowspan="2"> Uang Muka (Outstanding)</th> -->
                                                        <th class='style bb text-center' style="min-width: 150px" colspan="3">Piutang</th>
                                                        <th class='style bb' style="min-width: 150px" rowspan="2">Pelunasan</th>
                                                        <th class='style bb text-center' style="min-width: 150px" colspan="3">Retur</th>
                                                        <th class='style bb text-center' style="min-width: 150px" colspan="3">Diskon</th>
                                                        <th class='style bb text-center' style="min-width: 150px" colspan="2">Uang Muka</th>
                                                        <th class='style bb' style="min-width: 100px" rowspan="2">Koreksi</th>
                                                        <th class='style bb' style="min-width: 100px" rowspan="2">Refund</th>
                                                        <th class='style bb' style="min-width: 150px" rowspan="2">
                                                            <span class="smarttip"
                                                                data-tip="Saldo Akhir = Saldo Awal + Piutang Total - Pelunasan - Retur Total - Diskon Total - Uang Muka + Koreksi + Refund">
                                                                &#x2757; Saldo Akhir
                                                            </span>
                                                        </th>
                                                        <th class='depo bb text-center ' style="min-width: 150px" colspan="2">Deposit </th>
                                                    </tr>
                                                    <tr>
                                                        <th class='style bb' style="min-width: 50px">DPP</th>
                                                        <th class='style bb' style="min-width: 50px">PPN</th>
                                                        <th class='style bb' style="min-width: 50px">Total</th>
                                                        <th class='style bb' style="min-width: 50px">DPP</th>
                                                        <th class='style bb' style="min-width: 50px">PPN</th>
                                                        <th class='style bb' style="min-width: 50px">Total</th>
                                                        <th class='style bb' style="min-width: 50px">DPP</th>
                                                        <th class='style bb' style="min-width: 50px">PPN</th>
                                                        <th class='style bb' style="min-width: 50px">Total</th>
                                                        <th class='style bb' style="min-width: 50px">Baru </th>
                                                        <th class='style bb' style="min-width: 50px">Pelunasan</th>
                                                        <th class=' bb depo' style="min-width: 50px">Baru </th>
                                                        <th class=' bb depo' style="min-width: 50px">Pelunasan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="20">Tidak ada Data</td>
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

    <script type="text/javascript">
        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
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

            const tgldari = $('#tgldari').val();
            const tglsampai = $('#tglsampai').val();
            const this_btn = $(this);
            const selectedCurrency = $('input[name="currency"]:checked').val();

            if (!tgldari || !tglsampai) {
                alert_modal_warning('Periode Tanggal Harus diisi !');
                return;
            }

            if (!selectedCurrency) {
                alert_modal_warning('Currency Harus dipilih !');
                return;
            }

            // Convert string → Date()
            const dariDate = moment(tgldari, "D-MMMM-YYYY").toDate();
            const sampaiDate = moment(tglsampai, "D-MMMM-YYYY").toDate();

            // Validasi logika tanggal
            if (sampaiDate < dariDate) {
                alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
                return;
            }

            // Lanjut proses
            arr_filter = [];
            process_bukubesar(this_btn);

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
            var selectedCurrency = $('input[name="currency"]:checked').val();

            let slowProcessWarning = setTimeout(function() {
                please_wait(function() {});
            }, 5000); // 5 detik

            $("#example1_processing").css('display', ''); // show loading
            this_btn.button('loading');
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?php echo site_url('report/bukubesarpembantupiutang/loadData') ?>",
                data: {
                    tgldari: tgldari,
                    tglsampai: tglsampai,
                    checkhidden: check_hidden,
                    currency: selectedCurrency
                },
                success: function(data) {
                    clearTimeout(slowProcessWarning);
                    unblockUI(function() {});

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
                        let s_awal = 0;
                        let um_baru = 0;
                        let um_pelunasan = 0;
                        let depo_baru = 0;
                        let depo_pelunasan = 0;
                        let piutang_dpp = 0;
                        let piutang_ppn = 0;
                        let piutang_total = 0;
                        let pelunasan = 0;
                        let retur_dpp = 0;
                        let retur_ppn = 0;
                        let retur_total = 0;
                        let diskon_dpp = 0;
                        let diskon_ppn = 0;
                        let diskon_total = 0;
                        let uang_muka = 0;
                        let koreksi = 0;
                        let refund = 0;
                        let s_akhir = 0;
                        let tbody = $("<tbody />");

                        arr_filter.push({
                            tgldari: tgldari,
                            tglsampai: tglsampai,
                            checkhidden: check_hidden,
                            currency: selectedCurrency
                        });

                        $.each(data.record, function(key, value) {

                            empty = false;

                            var tr = $("<tr>").append(
                                $("<td colspan='20' class='text-left'>").html('<b> ' + value.gol_nama + '</b>'),
                            );
                            tbody.append(tr);

                            no = 1;
                            s_awal = 0;
                            um_baru = 0;
                            um_pelunasan = 0;
                            depo_baru = 0;
                            depo_pelunasan = 0;
                            piutang_dpp = 0;
                            piutang_ppn = 0;
                            piutang_total = 0;
                            pelunasan = 0;
                            retur_dpp = 0;
                            retur_ppn = 0;
                            retur_total = 0;
                            diskon_dpp = 0;
                            diskon_ppn = 0;
                            diskon_total = 0;
                            uang_muka = 0;
                            koreksi = 0;
                            refund = 0;
                            s_akhir = 0;

                            $.each(value.tmp_data, function(key, value) {

                                func2 = "view_detail2('" + value.id_partner + "')";
                                var tr2 = $("<tr>").append(
                                    $("<td class='no'>").html(no),
                                    $("<td>").html("<a href='javascript:void(0)' onclick=\"" + func2 + "\">" + value.nama_partner + "</a>"),
                                    $("<td align='right'>").text(formatNumber(value.saldo_awal.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.dpp_piutang.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.ppn_piutang.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.total_piutang_dpp_ppn.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.pelunasan.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.dpp_retur.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.ppn_retur.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.total_retur_dpp_ppn.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.dpp_diskon.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.ppn_diskon.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.total_diskon_dpp_ppn.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.um_baru.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.um_pelunasan.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.koreksi.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.refund.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.saldo_akhir.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.depo_baru.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value.depo_pelunasan.toFixed(2))),
                                );

                                tbody.append(tr2);
                                no++;
                                s_awal = s_awal + value.saldo_awal;
                                um_baru = um_baru + value.um_baru;
                                um_pelunasan = um_pelunasan + value.um_pelunasan;
                                depo_baru = depo_baru + value.depo_baru;
                                depo_pelunasan = depo_pelunasan + value.depo_pelunasan;
                                piutang_dpp = piutang_dpp + value.dpp_piutang;
                                piutang_ppn = piutang_ppn + value.ppn_piutang;
                                piutang_total = piutang_total + value.total_piutang_dpp_ppn;
                                pelunasan = pelunasan + value.pelunasan;
                                retur_dpp = retur_dpp + value.dpp_retur;
                                retur_ppn = retur_ppn + value.ppn_retur;
                                retur_total = retur_total + value.total_retur_dpp_ppn;
                                uang_muka = uang_muka + value.uang_muka;
                                diskon_dpp = diskon_dpp + value.dpp_diskon;
                                diskon_ppn = diskon_ppn + value.ppn_diskon;
                                diskon_total = diskon_total + value.total_diskon_dpp_ppn;
                                koreksi = koreksi + value.koreksi;
                                refund = refund + value.refund;
                                s_akhir = s_akhir + value.saldo_akhir;
                            });

                            tr3 = $("<tr>").append(
                                $("<td class='style_space text-right' colspan='2'>").html('<b>Total ' + value.gol_nama + ':<b>'),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(s_awal.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(piutang_dpp.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(piutang_ppn.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(piutang_total.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(pelunasan.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(retur_dpp.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(retur_ppn.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(retur_total.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(diskon_dpp.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(diskon_ppn.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(diskon_total.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(um_baru.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(um_pelunasan.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(koreksi.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(refund.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(s_akhir.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(depo_baru.toFixed(2))),
                                $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(depo_pelunasan.toFixed(2))),
                            );
                            tbody.append(tr3);
                            tbody.append("<tr><td colspan='20'>&nbsp</td></tr>");

                        });

                        if (empty == true) {
                            var tr = $("<tr>").append($("<td colspan='9'>").text('Tidak ada Data'));
                            tbody.append(tr);
                        } else {

                        }

                        $("#example1").append(tbody); // append parents

                        this_btn.button('reset');
                    }
                    $("#example1_processing").css('display', 'none'); // hidden loading

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert(jqXHR.responseText);
                    clearTimeout(slowProcessWarning);
                    unblockUI(function() {});
                    $("#example1_processing").css('display', 'none'); // hidden loading
                    this_btn.button('reset');
                }
            });

        }


        function view_detail2(partner) {
            var arrStr = encodeURIComponent(JSON.stringify(arr_filter));

            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu !');
            } else {
                var url = '<?php echo base_url() ?>report/bukubesarpembantupiutangdetail';
                window.open(url + '?partner=' + partner + '&&params=' + arrStr, '_blank');
            }
        }


        // klik btn excel
        $('#btn-excel').click(function() {

            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu !');
            } else {

                $.ajax({
                    "type": 'POST',
                    "url": "<?php echo site_url('report/bukubesarpembantupiutang/export_excel') ?>",
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

        // klik btn print  pdf
        $(document).on('click', "#btn-pdf", function(e) {

            var arrStr = encodeURIComponent(JSON.stringify(arr_filter));
            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu !');
            } else {
                var url = '<?php echo base_url() ?>report/bukubesarpembantupiutang/export_pdf';
                window.open(url + '?params=' + arrStr, '_blank');
            }

        });
        if ($("#globalTooltip").length === 0) {
            $("body").append('<div id="globalTooltip"></div>');
        }

        $(document).on("mouseenter touchstart", ".smarttip", function() {
            var tip = $(this).data("tip");
            var rect = this.getBoundingClientRect();
            var tt = $("#globalTooltip");

            tt.text(tip).show();

            // hitung posisi awal (top centered)
            var tooltipWidth = tt.outerWidth();
            var left = rect.left + (rect.width / 2) - (tooltipWidth / 2);
            var top = rect.top - tt.outerHeight() - 10; // muncul di atas elemen

            // auto adjust jika mepet kanan
            if (left + tooltipWidth > window.innerWidth - 10) {
                left = window.innerWidth - tooltipWidth - 10;
            }
            // auto adjust jika mepet kiri
            if (left < 10) left = 10;

            // jika tidak muat di atas, pindah ke bawah
            if (top < 10) {
                top = rect.bottom + 10;
            }

            tt.css({
                left: left + "px",
                top: top + "px"
            });
        });

        // hide
        $(document).on("mouseleave", ".smarttip", function() {
            $("#globalTooltip").hide();
        });

        $(document).on("touchstart click", function(e) {
            if (!$(e.target).closest(".smarttip").length) {
                $("#globalTooltip").hide();
            }
        });
    </script>

</body>

</html>