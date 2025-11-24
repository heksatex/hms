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
                                            <label> </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="hidden_check" id="hidden_check" checked>
                                            Sembuyikan Data Kosong
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                                    <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                                    <button type="button" class="btn btn-sm btn-default" name="btn-pdf" id="btn-pdf" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-pdf-o" style="color:red"></i> PDF</button>
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
                                                        <th class='style bb text-center' style="min-width: 150px" colspan="3">Piutang</th>
                                                        <th class='style bb' style="min-width: 150px" rowspan="2">Pelunasan</th>
                                                        <th class='style bb text-center' style="min-width: 150px" colspan="3">Retur</th>
                                                        <th class='style bb text-center' style="min-width: 150px" colspan="3">Diskon</th>
                                                        <th class='style bb' style="min-width: 150px" rowspan="2">Uang Muka</th>
                                                        <th class='style bb' style="min-width: 100px" rowspan="2">Koreksi</th>
                                                        <th class='style bb' style="min-width: 150px" rowspan="2">Saldo Akhir</th>
                                                    </tr>
                                                    <tr>
                                                        <th class='style bb' style="min-width: 50px" >DPP</th>
                                                        <th class='style bb' style="min-width: 50px" >PPN</th>
                                                        <th class='style bb' style="min-width: 50px" >Total</th>
                                                        <th class='style bb' style="min-width: 50px" >DPP</th>
                                                        <th class='style bb' style="min-width: 50px" >PPN</th>
                                                        <th class='style bb' style="min-width: 50px" >Total</th>
                                                        <th class='style bb' style="min-width: 50px" >DPP</th>
                                                        <th class='style bb' style="min-width: 50px" >PPN</th>
                                                        <th class='style bb' style="min-width: 50px" >Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="16">Tidak ada Data</td>
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
            defaultDate: new Date().toLocaleString('en-US', {
                timeZone: 'Asia/Jakarta'
            }),
            format: 'D-MMMM-YYYY',
            ignoreReadonly: true,
            // maxDate: new Date(),
            //minDate : 
            //maxDate: new Date(),
            //startDate: StartDate,
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

            let slowProcessWarning = setTimeout(function() {
                please_wait(function(){});
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
                    checkhidden: check_hidden
                },
                success: function(data) {
                    clearTimeout(slowProcessWarning);
                    unblockUI(function () { });

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
                        let s_akhir = 0;
                        let tbody = $("<tbody />");

                        arr_filter.push({
                            tgldari: tgldari,
                            tglsampai: tglsampai,
                            checkhidden: check_hidden,
                            currency:'rp'
                        });

                        $.each(data.record, function(key, value) {

                            empty = false;

                            func2 = "view_detail2('" + value.id_partner + "')";
                            var tr = $("<tr>").append(
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
                                $("<td align='right'>").text(formatNumber(value.uang_muka.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.koreksi.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.saldo_akhir.toFixed(2))),
                            );

                            tbody.append(tr);
                            no++;
                            s_awal = s_awal + value.saldo_awal;
                            piutang_dpp  = piutang_dpp + value.dpp_piutang;
                            piutang_ppn  = piutang_ppn + value.ppn_piutang;
                            piutang_total  = piutang_total + value.total_piutang_dpp_ppn;
                            pelunasan   = pelunasan + value.pelunasan;
                            retur_dpp  = retur_dpp + value.dpp_retur;
                            retur_ppn  = retur_ppn + value.ppn_retur;
                            retur_total  = retur_total + value.total_retur_dpp_ppn;
                            uang_muka   = uang_muka + value.uang_muka;
                            diskon_dpp  = diskon_dpp + value.dpp_diskon;
                            diskon_ppn  = diskon_ppn + value.ppn_diskon;
                            diskon_total  = diskon_total + value.total_diskon_dpp_ppn;
                            koreksi   = koreksi + value.koreksi;
                            s_akhir = s_akhir + value.saldo_akhir;
                        });

                        if (empty == true) {
                            var tr = $("<tr>").append($("<td colspan='9'>").text('Tidak ada Data'));
                            tbody.append(tr);
                        } else {
                            tbody.append("<tr><td colspan='16'>&nbsp</td></tr>");
                            tr2 = $("<tr>").append(
                                $("<td align='right' colspan='2'>").html('<b>Total :<b>'),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(s_awal.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(piutang_dpp.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(piutang_ppn.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(piutang_total.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(pelunasan.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(retur_dpp.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(retur_ppn.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(retur_total.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(diskon_dpp.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(diskon_ppn.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(diskon_total.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(uang_muka.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(koreksi.toFixed(2))),
                                $("<td align='right' style='font-weight:bold;'>").text(formatNumber(s_akhir.toFixed(2))),
                            );
                            tbody.append(tr2);
                        }

                        $("#example1").append(tbody); // append parents

                        this_btn.button('reset');
                    }
                    $("#example1_processing").css('display', 'none'); // hidden loading

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert(jqXHR.responseText);
                    clearTimeout(slowProcessWarning);
                    unblockUI(function () { });
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
                window.open(url + '?partner='+ partner +'&&params=' + arrStr, '_blank');
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
    </script>

</body>

</html>