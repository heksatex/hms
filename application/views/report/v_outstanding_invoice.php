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

        .ket-acc {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
            /* Sesuaikan dengan kebutuhan */
        }

        .resizable .resizer:hover {
            background-color: rgba(0, 0, 0, 0.1);
        }

        .resizable {
            position: relative;
        }

        .resizable .resizer {
            position: absolute;
            top: 0;
            right: 0;
            width: 5px;
            cursor: col-resize;
            user-select: none;
            height: 100%;
        }

        table th,
        table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                        <h3 class="box-title"><b>Outstanding Invoice</b></h3>
                    </div>
                    <div class="box-body">

                        <form name="input" class="form-horizontal" role="form" method="POST" id="frm_form_search">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="col-sm-12 col-md-12">
                                        <div class="col-md-2">
                                            <label>Supplier</label>
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
                                            <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                                            <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-pdf" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-pdf-o" style="color:red"></i> PDF</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>

                        <!-- table -->
                        <div class="box-body">
                            <div class="col-sm-12 table-responsive">
                                <div class="table_scroll">
                                    <div class="table_scroll_head">
                                        <div class="divListviewHead">
                                            <table id="example1" class="table table-condesed table-hover" border="0">
                                                <thead>
                                                    <tr>
                                                        <th class="style bb no">No. </th>
                                                        <th class='style bb' style="min-width: 80px; width:80px;">Supplier</th>
                                                        <th class='style bb' style="min-width: 50px; width:105px;">Invoice</th>
                                                        <th class='style bb' style="min-width: 105px; width:105px;">PO</th>
                                                        <th class='style bb' style="min-width: 105px; width:105px;">Receiving</th>
                                                        <th class='style bb' style="min-width: 105px; width:105px;">Tanggal</th>
                                                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Total Hutang</th>
                                                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Sisa Hutang</th>
                                                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Umur (Hari)</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="9">Tidak ada Data</td>
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
        //select 2 supplier
        $('#partner').select2({
            allowClear: true,
            placeholder: "Select Supplier",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo base_url(); ?>accounting/pelunasanhutang/get_list_supplier",
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


        // btn generate
        $("#btn-generate").on('click', function() {
            var this_btn = $(this);
            proses_outstanding(this_btn);
        });


        function formatNumber(n) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(n);
        }

        function proses_outstanding(this_btn) {
            var partner = $('#partner').val();

            $("#example1_processing").css('display', ''); // show loading
            this_btn.button('loading');
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?php echo site_url('report/outstandinginvoice/loadData') ?>",
                data: {
                    partner: partner
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
                        let s_akhir = 0;
                        let tbody = $("<tbody />");

                        $.each(data.record, function(key, value) {

                            empty = false;
                            var tr = $("<tr>").append(
                                // $("<td>").text(''),
                                $("<td colspan='5' class='text-left'>").html('<b>Supplier : </b> ' + value.nama_partner),
                                $("<td colspan='2'>").text(''),
                            );
                            tbody.append(tr);

                            no = 1
                            acc = '';
                            debit = 0;
                            credit = 0;
                            total_hutang_rp = 0;
                            total_sisa_hutang = 0;
                            $.each(value.tmp_data_items, function(key, value2) {
                                var tr3 = $("<tr>").append(
                                    $("<td>").html(no++),
                                    $("<td align=''>").html('&nbsp'),
                                    $("<td align=''>").text(value2.no_invoice),
                                    $("<td align=''>").text(value2.no_po),
                                    $("<td align=''>").text(value2.origin),
                                    $("<td align=''>").text(value2.tanggal),
                                    $("<td align='right'>").text(formatNumber(value2.hutang_rp.toFixed(2))),
                                    $("<td align='right'>").text(formatNumber(value2.sisa_hutang_rp.toFixed(2))),
                                    $("<td align='right'>").text(value2.hari),
                                );
                                tbody.append(tr3);
                                total_hutang_rp = total_hutang_rp + value2.hutang_rp;
                                total_sisa_hutang = total_sisa_hutang + value2.sisa_hutang_rp;
                            });

                            no = 1;

                            var tr4 = $("<tr>").append(
                                $("<td colspan='5' class='style_space'>").text(''),
                                $("<td class='style_space text-right'>").html('<b>Total : </b>'),
                                $("<td class='style_space text-right'>").html('<b>' + formatNumber(total_hutang_rp.toFixed(2)) + '</b>'),
                                $("<td class='style_space text-right'>").html('<b>' + formatNumber(total_sisa_hutang.toFixed(2)) + '</b>'),
                                $("<td class='style_space'>").text(''),
                            );
                            tbody.append(tr4);
                        });

                        if (empty == true) {
                            var tr = $("<tr>").append($("<td colspan='9'>").text('Tidak ada Data'));
                            tbody.append(tr);
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


        // klik btn excel
        $('#btn-excel').click(function() {
            var partner = $('#partner').val();
            $.ajax({
                "type": 'POST',
                "url": "<?php echo site_url('report/outstandinginvoice/export_excel') ?>",
                "data": {partner: partner},
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
        });
        
        var arr_filter = [];
        // klik btn print  pdf
        $(document).on('click', "#btn-pdf", function(e) {

            var partner = $('#partner').val();
            arr_filter.push({partner: partner});
            var arrStr = encodeURIComponent(JSON.stringify(arr_filter));
            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu !');
            } else {
                var url = '<?php echo base_url() ?>report/outstandinginvoice/export_pdf';
                window.open(url + '?params=' + arrStr, '_blank');
            }

        });
    </script>

</body>

</html>