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
                        <h3 class="box-title"><b>Buku Besar</b></h3>
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
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-pdf" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-pdf-o" style="color:red"></i> PDF</button>
                                </div>
                            </div>

                        </form>

                        <div class="box-body">
                            <div class="col-sm-12 table-responsive">
                                <div class="table_scroll">
                                    <div class="table_scroll_head">
                                        <div class="divListviewHead">
                                            <table id="example1" class="table table-condesed table-hover" border="0">
                                                <thead>
                                                    <tr>
                                                        <th class="style bb no">No. </th>
                                                        <th class='style bb' style="min-width: 5px">Kode Acc</th>
                                                        <th class='style bb' style="min-width: 200px">Nama Acc</th>
                                                        <th class='style bb' style="min-width: 10px">Saldo Normal</th>
                                                        <th class='style bb' style="min-width: 150px">Saldo Awal</th>
                                                        <th class='style bb' style="min-width: 150px">Debit</th>
                                                        <th class='style bb' style=" min-width: 150px">Credit</th>
                                                        <th class='style bb' style="min-width: 150px">Saldo Akhir</th>
                                                        <!-- <th class='style bb' style=" min-width: 100px; text-align:right">dcr</th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="8">Tidak ada Data</td>
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
            defaultDate: new Date().toLocaleString('en-US', {
                timeZone: 'Asia/Jakarta'
            }),
            format: 'D-MMMM-YYYY',
            ignoreReadonly: true,
            maxDate: new Date()
        });

        // set date tglsampai
        $('#tglsampai').datetimepicker({
            defaultDate: new Date().toLocaleString('en-US', {
                timeZone: 'Asia/Jakarta'
            }),
            format: 'D-MMMM-YYYY',
            ignoreReadonly: true,
            maxDate: new Date(),
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

            $("#example1_processing").css('display', ''); // show loading
            this_btn.button('loading');
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?php echo site_url('report/bukubesar/loadData') ?>",
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
                        let debit = 0;
                        let credit = 0;
                        let s_awal = 0;
                        let tbody = $("<tbody />");

                        arr_filter.push({
                            tgldari: tgldari,
                            tglsampai: tglsampai,
                            checkhidden: check_hidden
                        });

                        $.each(data.record, function(key, value) {

                            empty = false;

                            // func = "view_detail('" + value.kode_acc + "')";
                            func2 = "view_detail2('" + value.kode_acc + "')";
                            // func2 = "<?php echo base_url() ?>report/bukubesar/detail";
                            var tr = $("<tr>").append(
                                $("<td>").html(no),
                                // $("<td>").html('<a href="javascript:void(0)" onclick=' + func + '>' + value.kode_acc + '</a>'),
                                // $('<td align="" onclick=' + func + ' style="cursor:pointer;">').text(value.kode_acc),
                                $("<td align=''>").text(value.kode_acc),
                                $("<td>").html('<a  href="javascript:void(0)" onclick=' + func2 + ' ">' + value.nama_acc + '</a>'),
                                $("<td align=''>").text(value.saldo_normal),
                                $("<td align='right'>").text(formatNumber(value.saldo_awal.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.debit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.credit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.saldo_akhir.toFixed(2))),
                            );

                            tbody.append(tr);
                            no++;
                            s_awal = s_awal + value.saldo_awal;
                            debit = debit + value.debit;
                            credit = credit + value.credit;
                        });

                        if (empty == true) {
                            var tr = $("<tr>").append($("<td colspan='8'>").text('Tidak ada Data'));
                            tbody.append(tr);
                        } else {
                            tbody.append("<tr><td colspan='8'>&nbsp</td></tr>");
                            tr2 = $("<tr>").append(
                                $("<td colspan='4'>").text(''),
                                // $("<td align='right'>").text(formatNumber(s_awal.toFixed(2))),
                                $("<td align='right'>").text(''),
                                $("<td align='right'>").text(formatNumber(debit.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(credit.toFixed(2))),
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
                window.open(url + '?coa='+ kode_coa +'&&params=' + arrStr, '_blank');
            }
        }


        // klik btn excel
        $('#btn-excel').click(function() {

            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu !');
            } else {

                $.ajax({
                    "type": 'POST',
                    "url": "<?php echo site_url('report/bukubesar/export_excel') ?>",
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
                var url = '<?php echo base_url() ?>report/bukubesar/export_pdf';
                window.open(url + '?params=' + arrStr, '_blank');
            }

        });
    </script>

</body>

</html>