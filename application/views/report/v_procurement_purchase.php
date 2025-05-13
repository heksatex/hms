<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />

    <style type="text/css">
        h3 {
            display: block !important;
            text-align: center !important;
        }

        .divListviewHead table {
            display: block;
            height: calc(95vh - 250px);
            overflow-x: auto;
        }

        .ws {
            white-space: nowrap;
        }
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
                        <h3 class="box-title"><b>Procurement Purchase</b></h3>
                    </div>
                    <div class="box-body">

                        <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <label>Tanggal buat </label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="periode" id="periode" value="<?= $date ?>" class="form-control input-sm" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <label>Departemen </label>
                                        </div>
                                        <div class="col-md-8">
                                            <select type="text" class="form-control input-sm" name="departemen" id="departemen" required="">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-4">
                                            <label>
                                                <div id='total_record'>Total Data : 0</div>
                                            </label>
                                        </div>
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
                            <div class="col-md-4">
                                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                                <button type="button" class="btn btn-sm btn-default" name="btn-excel" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                            </div>
                            <br>
                            <div class="col-md-12">
                                <div class="panel panel-default" style="margin-bottom: 0px;">
                                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced">
                                        <div class="panel-body" style="padding: 5px">
                                            <div class="form-group col-md-12" style="margin-bottom:0px">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>Kode PP </label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control input-sm" name="kode_pp" id="kode_pp">
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>Type </label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <select type="text" class="form-control input-sm select2" name="type" id="type" style="width:100% !important">
                                                                <option value="">-- Pilih Type --</option>
                                                                <option value="mto">Make To Order</option>
                                                                <option value="pengiriman">Pengiriman</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>Nama Produk </label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control input-sm" name="nama_produk" id="nama_produk">
                                                        </div>
                                                    </div>
                                                    <div class="form-group col-md">
                                                        <div class="col-md-5">
                                                            <label>Status </label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <select type="text" class="form-control input-sm" name="status" id="status">
                                                                <option>All</option>
                                                                <option value='generated'>Generated</option>
                                                                <option value='confirm'>Confirm</option>
                                                                <option value='cfb'>CFB</option>
                                                                <option value='po'>PO</option>
                                                                <option value='fpt'>FPT</option>
                                                                <option value='cancel'>Cancel</option>
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

                        <!-- table -->
                        <div class="box-body">
                            <div class="col-xs-12 table-responsive example1 divListviewHead">
                                <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                    <table id="example1" class="table table-condesed table-hover" border="0">
                                        <thead>
                                            <tr>
                                                <th class="style bb ws no">No. </th>
                                                <th class='style bb ws'>Kode PP</th>
                                                <th class='style bb ws'>Tgl dibuat</th>
                                                <th class='style bb ws'>Type</th>
                                                <th class='style bb ws'>Sales Order</th>
                                                <th class='style bb ws'>Production Order</th>
                                                <th class='style bb ws'>Departemen</th>
                                                <th class='style bb ws'>Priority</th>
                                                <th class='style bb ws'>Kode Produk</th>
                                                <th class='style bb ws' style="min-width: 150px">Nama Produk</th>
                                                <th class='style bb ws' style="min-width: 100px">Schedule date</th>
                                                <th class='style bb ws text-right'>Qty Beli</th>
                                                <th class='style bb ws text-right'>Qty </th>
                                                <th class='style bb ws'>Notes</th>
                                                <th class='style bb ws'>Status</th>
                                                <th class='style bb ws'>Kode CFB</th>
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
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

    </div>

    <?php $this->load->view("admin/_partials/js.php") ?>
    <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>

    <script type="text/javascript">
        //* Show collapse advanced search
        $('#advancedSearch').on('shown.bs.collapse', function() {
            $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
        });

        //* Hide collapse advanced search
        $('#advancedSearch').on('hidden.bs.collapse', function() {
            $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
        });

        $('input[name="periode"]').daterangepicker({
            endDate: moment().endOf('month'),
            startDate: moment().startOf('month'),
            locale: {
                format: 'YYYY-MM-DD'
            }
        });


        // disable enter
        $(window).keydown(function(event) {
            if (event.keyCode == 13) {
                event.preventDefault();
                return false;
            }
        });

        // btn excel
        $('#btn-excel').click(function() {

            periode = $('#periode').val();
            kode_pp = $('#kode_pp').val();
            nama_produk = $('#nama_produk').val();
            departemen = $('#departemen').val();
            type = $('#type').val();
            status = $("#status").val();

            if (periode == '') {
                alert_modal_warning('Tanggal buat Harus diisi !');
            } else if (departemen == '' || departemen == null) {

            } else {
                $.ajax({
                    "type": 'POST',
                    "url": "<?php echo site_url('report/procurementpurchase/export_excel') ?>",
                    "data": {
                        periode: periode,
                        kode_pp: kode_pp,
                        nama_produk: nama_produk,
                        departemen: departemen,
                        type: type,
                        status: status,
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
                        alert_modal_warning(data.message);
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


        // btn generate
        $("#btn-generate").on('click', function() {

            periode = $('#periode').val();
            kode_pp = $('#kode_pp').val();
            nama_produk = $('#nama_produk').val();
            departemen = $('#departemen').val();
            type = $('#type').val();
            status = $("#status").val();

            if (periode == '') {
                alert_modal_warning('Tanggal buat Harus diisi !');
            } else if (departemen == '' || departemen == null) {
                alert_modal_warning('Departemen Harus diisi !');
            } else {

                $("#example1_processing").css('display', ''); // show loading processing in table
                $('#btn-generate').button('loading');
                $("#example1 tbody").remove();
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "<?php echo site_url('report/procurementpurchase/loadData') ?>",
                    data: {
                        periode: periode,
                        kode_pp: kode_pp,
                        nama_produk: nama_produk,
                        departemen: departemen,
                        type: type,
                        status: status,
                    },
                    success: function(data) {

                        if (data.status == 'failed') {
                            $('#total_record').html('Total Data : 0');
                            alert_modal_warning(data.message);
                        } else {

                            $('#total_record').html(data.total_record);

                            let tbody = $("<tbody />");
                            let no = 1;
                            let empty = true;

                            $.each(data.record, function(key, value) {
                                empty = false;
                                var tr = $("<tr>").append(
                                    $("<td>").text(no++),
                                    $("<td style='min-width:120px;'>").text(value.kode_pp),
                                    $("<td >").text(value.tgl_buat),
                                    $("<td style='min-width:100px;'>").text(value.type),
                                    $("<td>").text(value.sales_order),
                                    $("<td>").text(value.kode_prod),
                                    $("<td>").text(value.departemen),
                                    $("<td>").text(value.priority),
                                    $("<td>").text(value.kode_produk),
                                    $("<td>").text(value.nama_produk),
                                    $("<td>").text(value.schedule_date),
                                    $("<td style='min-width:100px; text-align:right;'>").text(value.qty_beli),
                                    $("<td style='min-width:100px; text-align:right;'>").text(value.qty),
                                    $("<td style='min-width:150px;'>").text(value.notes),
                                    $("<td>").text(value.status),
                                    $("<td style='min-width:130px;'>").text(value.kode_cfb),
                                );
                                tbody.append(tr);
                            });
                            if (empty == true) {
                                var tr = $("<tr>").append($("<td colspan='16' >").text('Tidak ada Data'));
                                tbody.append(tr);
                            }
                            $("#example1").append(tbody);
                        }

                        $('#btn-generate').button('reset');
                        $("#example1_processing").css('display', 'none'); // hidden loading processing in table

                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        //alert('error data');
                        $("#example1_processing").css('display', 'none'); // hidden loading processing in table
                        $('#btn-generate').button('reset');
                    }
                });

            }
        });


        //select 2 Departement
        $('#departemen').select2({
            allowClear: true,
            placeholder: "Select Departemen",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo base_url(); ?>report/pengirimanharian/get_departement_select2",
                //delay : 250,
                data: function(params) {
                    return {
                        nama: params.term,
                    };
                },
                processResults: function(data) {
                    var results = [];
                    $.each(data, function(index, item) {
                        results.push({
                            id: item.kode,
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
    </script>

</body>

</html>