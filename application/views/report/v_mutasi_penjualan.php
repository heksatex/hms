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
                        <h3 class="box-title"><b>Mutasi Penjualan</b></h3>
                    </div>
                    <div class="box-body">

                        <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="col-md-2">
                                            <label>Customer</label>
                                        </div>
                                        <div class="col-sm-* col-md-8 col-lg-8">
                                            <select class="form-control input-sm" name="partner" id="partner"></select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                                            <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                                            <button type="button" class="btn btn-sm btn-default" name="btn-pdf" id="btn-pdf" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-pdf-o" style="color:red"></i> PDF</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="col-md-4 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                                            <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                                                <label style="cursor:pointer;">
                                                    <i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i>
                                                    Advanced
                                                </label>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>


                            <br>
                            <div class="col-md-12">
                                <div class="panel panel-default" style="margin-bottom: 5px;">
                                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced">
                                        <div class="panel-body" style="padding: 5px">
                                            <div class="form-group col-md-12">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-5"><label><input type="checkbox" name="checkTgl" id="checkTgl"> Tgl. Faktur</label></div>
                                                        <div class="col-md-7">
                                                            <div class='input-group date'>
                                                                <input type="text" class="form-control input-sm" name="tgldari" id="tgldari" readonly="">
                                                                <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class=" col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>s/d</label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class='input-group date'>
                                                                <input type="text" class="form-control input-sm" name="tglsampai" id='tglsampai' readonly="">
                                                                <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-12" style="margin-bottom:0px">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>No Faktur</label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control input-sm" name="no_faktur" id="no_faktur">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>No SJ</label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control input-sm" name="no_sj" id="no_sj">

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>Tipe </label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <select type="text" class="form-control input-sm" name="tipe" id="tipe">
                                                                <option value='all'>All</option>
                                                                <option value="lokal">Lokal</option>
                                                                <option value="makloon">Makloon</option>
                                                                <option value="ekspor">Ekspor</option>
                                                                <option value="lain-lain">Lain-Lain</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>Status Lunas </label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <select type="text" class="form-control input-sm" name="status_lunas" id="status_lunas">
                                                                <option value='all'>All</option>
                                                                <option value="1">Lunas</option>
                                                                <option value="0">Belum Lunas</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                                        <th class='style bb' style="min-width: 50px" rowspan="2">Tgl Faktur</th>
                                                        <th class='style bb' style="min-width: 100px" rowspan="2">No Faktur</th>
                                                        <th class='style bb' style="min-width: 100px" rowspan="2">NO SJ</th>
                                                        <th class='style bb' style="min-width: 50px" rowspan="2">Tipe</th>
                                                        <th class='style bb text-center' style="min-width: 150px" colspan="3">Penjualan</th>
                                                        <th class='style bb text-center' style="min-width: 150px" colspan="4">Pelunasan</th>
                                                        <th class='style bb text-center' style="min-width: 250px" colspan="5">Retur</th>
                                                        <th class='style bb text-center' style="min-width: 150px" colspan="3">Diskon</th>
                                                        <!-- <th class='style bb' style="min-width: 100px" rowspan="2">Koreksi</th> -->
                                                        <th class='style bb' style="min-width: 120px" rowspan="2">
                                                            <span class="smarttip"
                                                                data-tip="Sisa Piutang = Penjualan  - Pelunasan Total - Retur Total - Diskon Total ">
                                                                &#x2757; Sisa Piutang
                                                            </span>
                                                    </tr>
                                                    <tr>
                                                        <th class='style bb' style="min-width: 50px">DPP</th>
                                                        <th class='style bb' style="min-width: 50px">PPN</th>
                                                        <th class='style bb' style="min-width: 50px">Total</th>

                                                        <th class='style bb' style="max-width: 50px;">Tgl</th>
                                                        <th class='style bb' style="min-width: 50px">No.Pelunasan</th>
                                                        <th class='style bb' style="min-width: 50px">No.Bukti</th>
                                                        <th class='style bb' style="min-width: 50px">Total</th>

                                                        <th class='style bb' style="min-width: 50px">Tgl</th>
                                                        <th class='style bb' style="min-width: 50px">No.Bukti</th>
                                                        <th class='style bb' style="min-width: 50px">DPP</th>
                                                        <th class='style bb' style="min-width: 50px">PPN</th>
                                                        <th class='style bb' style="min-width: 50px">Total</th>

                                                        <th class='style bb' style="min-width: 50px">DPP</th>
                                                        <th class='style bb' style="min-width: 50px">PPN</th>
                                                        <th class='style bb' style="min-width: 50px">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="22">Tidak ada Data</td>
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
        $('#advancedSearch').on('shown.bs.collapse', function() {
            $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
        });

        //* Hide collapse advanced search
        $('#advancedSearch').on('hidden.bs.collapse', function() {
            $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
        });

        //select 2 customer
        $('#partner').select2({
            allowClear: true,
            placeholder: "Select Customer",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo base_url(); ?>accounting/pelunasanpiutang/get_list_customer",
                data: function(params) {
                    return {
                        name: params.term,
                    };
                },
                processResults: function(data) {
                    var results = [];
                    $.each(data, function(index, item) {
                        results.push({
                            id: item.id,
                            text: item.nama
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

        $('#checkTgl').on('change', function() {

            if (this.checked) {

                // ENABLE
                $('#tgldari, #tglsampai')
                    .prop('disabled', false)
                    .prop('readonly', false);

                // DESTROY dulu (penting)
                if ($('#tgldari').data('DateTimePicker')) {
                    $('#tgldari').data('DateTimePicker').destroy();
                }
                if ($('#tglsampai').data('DateTimePicker')) {
                    $('#tglsampai').data('DateTimePicker').destroy();
                }

                // INIT ULANG + DEFAULT
                $('#tgldari').datetimepicker({
                    defaultDate: moment().startOf('month'),
                    format: 'D-MMMM-YYYY',
                    ignoreReadonly: true
                });

                $('#tglsampai').datetimepicker({
                    defaultDate: moment(),
                    format: 'D-MMMM-YYYY',
                    ignoreReadonly: true
                });

            } else {

                // CLEAR VALUE
                $('#tgldari, #tglsampai').val('');

                // CLEAR PICKER
                if ($('#tgldari').data('DateTimePicker')) {
                    $('#tgldari').data('DateTimePicker').clear();
                }
                if ($('#tglsampai').data('DateTimePicker')) {
                    $('#tglsampai').data('DateTimePicker').clear();
                }

                // DISABLE
                $('#tgldari, #tglsampai')
                    .prop('disabled', true)
                    .prop('readonly', true);
            }
        });


        $(function() {
            $('[data-toggle="tooltip"]').tooltip()
        })
        // set date tgldari
        // $('#tgldari').datetimepicker({
        //     // defaultDate: new Date().toLocaleString('en-US', {
        //     //     timeZone: 'Asia/Jakarta'
        //     // }),
        //     defaultDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1), // ⬅️ tanggal 1 bulan ini
        //     format: 'D-MMMM-YYYY',
        //     ignoreReadonly: true,
        //     // maxDate: new Date()
        // });

        // // set date tglsampai
        // $('#tglsampai').datetimepicker({
        //     defaultDate: new Date().toLocaleString('en-US', {
        //         timeZone: 'Asia/Jakarta'
        //     }),
        //     format: 'D-MMMM-YYYY',
        //     ignoreReadonly: true,
        //     // maxDate: new Date(),
        //     //minDate : 
        //     //maxDate: new Date(),
        //     //startDate: StartDate,
        // });



        function collectFilter() {
    
            let filter = {};

            // customer
            filter.partner = $('#partner').val() || null;

            // tanggal
            let check_tgl = $('#checkTgl').is(':checked');
            filter.check_tgl = check_tgl ? 'true' : 'false';
            if (check_tgl) {
                filter.tgldari   = $('#tgldari').val();
                filter.tglsampai = $('#tglsampai').val();
            } else {
                filter.tgldari   = null;
                filter.tglsampai = null;
            }


            // advanced
            let no_faktur = $('#no_faktur').val();
            let no_sj = $('#no_sj').val();
            let tipe = $('#tipe').val();
            let status_lunas = $('#status_lunas').val();

            if (no_faktur) filter.no_faktur = no_faktur;
            if (no_sj) filter.no_sj = no_sj;
            if (tipe) filter.tipe = tipe;
            if (status_lunas) filter.status_lunas = status_lunas;

            return filter;
        }



        // btn generate
        $("#btn-generate").on('click', function() {

            const this_btn = $(this);
            const filter = collectFilter();

            // validasi tanggal jika dicentang
            if ($('#checkTgl').is(':checked')) {
                if (!filter.tgldari || !filter.tglsampai) {
                    alert_modal_warning('Periode Tanggal Harus diisi !');
                    return;
                }

                const dariDate = moment(filter.tgldari, "D-MMMM-YYYY").toDate();
                const sampaiDate = moment(filter.tglsampai, "D-MMMM-YYYY").toDate();

                if (sampaiDate < dariDate) {
                    alert_modal_warning('Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
                    return;
                }
            }

            arr_filter = filter; // simpan untuk excel / pdf
            process_mutasi(this_btn, filter);
        });



        function formatNumber(n) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(n);
        }

        function process_mutasi(this_btn, filter) {

            $("#example1_processing").show();
            this_btn.button('loading');

            let slowProcessWarning = setTimeout(function() {
                please_wait(function() {});
            }, 5000); // 5 detik

            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?php echo site_url('report/mutasipenjualan/loadData') ?>",
                data: filter,
                success: function(data) {
                    clearTimeout(slowProcessWarning);
                    $("#example1_processing").hide();
                    this_btn.button('reset');

                    if (data.status === 'failed') {
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type, function() {});
                            }, 1000);
                        });
                        return;
                    }

                    // render table (punya kamu sudah OK, tinggal pakai)
                    renderTable(data.record);
                },
                error: function(xhr) {
                    $("#example1_processing").hide();
                    clearTimeout(slowProcessWarning);
                    unblockUI(function() {});
                    $("#example1_processing").hide();
                    this_btn.button('reset');
                    alert(xhr.responseText);
                }
            });
        }


        function renderTable(data) {

            $("#example1 tbody").remove();
            let no = 1;
            let empty = true;
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
            let sisa = 0;
            let tbody = $("<tbody />");

            $.each(data, function(key, value) {

                empty = false;

                var tr = $("<tr>").append(
                    $("<td colspan='21' class='text-left'>").html('<b> ' + value.nama_partner + '</b>'),
                );
                tbody.append(tr);

                no = 1;
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
                sisa = 0;

                $.each(value.tmp_data_items, function(key, value) {

                    // func2 = "view_detail2('" + value.id_partner + "')";
                    var tr2 = $("<tr>").append(
                        $("<td class='no'>").html(no),
                        $("<td>").text(value.tgl_faktur),
                        $("<td>").text(value.no_faktur),
                        $("<td>").text(value.no_sj),
                        $("<td>").text(value.tipe),

                        $("<td align='right'>").text(formatNumber(value.dpp_piutang.toFixed(2))),
                        $("<td align='right'>").text(formatNumber(value.ppn_piutang.toFixed(2))),
                        $("<td align='right'>").text(formatNumber(value.total_piutang.toFixed(2))),
                        $("<td style='max-width:80px;'>").text(value.tgl_pelunasan),
                        $("<td style='max-width:100px;'>").text(value.no_pelunasan),
                        $("<td style='max-width:100px;'>").text(value.no_bukti_pelunasan),
                        $("<td align='right'>").text(formatNumber(value.total_pelunasan.toFixed(2))),
                        $("<td style='max-width:80px;'>").text(value.tgl_retur),
                        $("<td style='max-width:100px;'>").text(value.no_bukti_retur),
                        $("<td align='right'>").text(formatNumber(value.dpp_retur.toFixed(2))),
                        $("<td align='right'>").text(formatNumber(value.ppn_retur.toFixed(2))),
                        $("<td align='right'>").text(formatNumber(value.total_retur.toFixed(2))),
                        $("<td align='right'>").text(formatNumber(value.dpp_diskon.toFixed(2))),
                        $("<td align='right'>").text(formatNumber(value.ppn_diskon.toFixed(2))),
                        $("<td align='right'>").text(formatNumber(value.total_diskon.toFixed(2))),

                        // $("<td align='right'>").text(0),

                        $("<td align='right'>").text(formatNumber(value.sisa.toFixed(2))),


                    );

                    tbody.append(tr2);
                    no++;
                    piutang_dpp = piutang_dpp + value.dpp_piutang;
                    piutang_ppn = piutang_ppn + value.ppn_piutang;
                    piutang_total = piutang_total + value.total_piutang;
                    pelunasan = pelunasan + value.total_pelunasan;
                    retur_dpp = retur_dpp + value.dpp_retur;
                    retur_ppn = retur_ppn + value.ppn_retur;
                    retur_total = retur_total + value.total_retur;
                    diskon_dpp = diskon_dpp + value.dpp_diskon;
                    diskon_ppn = diskon_ppn + value.ppn_diskon;
                    diskon_total = diskon_total + value.total_diskon;
                    sisa = sisa + value.sisa;

                });

                tr3 = $("<tr>").append(
                    $("<td class='style_space text-right' colspan='5'>").html('<b>Total ' + value.nama_partner + ':<b>'),
                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(piutang_dpp.toFixed(2))),
                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(piutang_ppn.toFixed(2))),
                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(piutang_total.toFixed(2))),
                    $("<td class='style_space text-right' >").text(''),
                    $("<td class='style_space text-right' >").text(''),
                    $("<td class='style_space text-right' >").text(''),
                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(pelunasan.toFixed(2))),
                    $("<td class='style_space text-right'> ").text(''),
                    $("<td class='style_space text-right'> ").text(''),
                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(retur_dpp.toFixed(2))),
                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(retur_ppn.toFixed(2))),
                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(retur_total.toFixed(2))),

                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(diskon_dpp.toFixed(2))),
                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(diskon_ppn.toFixed(2))),
                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(diskon_total.toFixed(2))),
                    // $("<td class='style_space text-right'> ").text(''),
                    $("<td class='style_space text-right' style='font-weight:bold;'>").text(formatNumber(sisa.toFixed(2))),
                );
                tbody.append(tr3);
                tbody.append("<tr><td colspan='21'>&nbsp</td></tr>");

            });

            if (empty == true) {
                var tr = $("<tr>").append($("<td colspan='21'>").text('Tidak ada Data'));
                tbody.append(tr);
            } else {

            }

            $("#example1").append(tbody); // append parents

            return;
        }


        // function view_detail2(partner) {
        //     var arrStr = encodeURIComponent(JSON.stringify(arr_filter));

        //     if (arr_filter.length == 0) {
        //         alert_modal_warning('Generate Data terlebih dahulu !');
        //     } else {
        //         var url = '<?php echo base_url() ?>report/bukubesarpembantupiutangdetail';
        //         window.open(url + '?partner=' + partner + '&&params=' + arrStr, '_blank');
        //     }
        // }


        // // klik btn excel
        // $('#btn-excel').click(function() {

        //     if (arr_filter.length == 0) {
        //         alert_modal_warning('Generate Data terlebih dahulu !');
        //     } else {

        //         $.ajax({
        //             "type": 'POST',
        //             "url": "<?php echo site_url('report/bukubesarpembantupiutang/export_excel') ?>",
        //             "data": {
        //                 arr_filter: arr_filter
        //             },
        //             "dataType": 'json',
        //             beforeSend: function() {
        //                 $('#btn-excel').button('loading');
        //             },
        //             error: function() {
        //                 alert('Error Export Excel');
        //                 $('#btn-excel').button('reset');
        //             }
        //         }).done(function(data) {
        //             if (data.status == "failed") {
        //                 alert_notify(data.icon, data.message, data.type, function() {});
        //             } else {
        //                 var $a = $("<a>");
        //                 $a.attr("href", data.file);
        //                 $("body").append($a);
        //                 $a.attr("download", data.filename);
        //                 $a[0].click();
        //                 $a.remove();
        //             }
        //             $('#btn-excel').button('reset');
        //         });
        //     }
        // });

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