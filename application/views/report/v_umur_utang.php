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
            height: calc(101vh - 250px);
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
                        <h3 class="box-title"><b>Umur Utang (Aging)</b></h3>
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
                                                    <tr id='tableHeader'>
                                                        <th class="style bb no">No. </th>
                                                        <th class='style bb' style="min-width: 80px; width:80px;">Supplier</th>
                                                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Total utang</th>
                                                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Bulan Ini</th>
                                                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Bulan 1</th>
                                                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Bulan 2</th>
                                                        <th class='style bb text-right' style="min-width: 150px; width:100px;">BUlan 3</th>
                                                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Bulan >3 </th>
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
            proses_aging(this_btn);
        });


        function formatNumber(n) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(n);
        }

        function proses_aging(this_btn) {
            var partner = $('#partner').val();
            let slowProcessWarning = setTimeout(function() {
                please_wait(function(){});
            }, 5000); // 5 detik

            $("#example1_processing").css('display', ''); // show loading
            this_btn.button('loading');
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?php echo site_url('report/umurutang/loadData') ?>",
                data: {
                    partner: partner
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

                        $("#example1 thead #tableHeader").empty();
                        $("#example1 tbody").empty();

                        const headerWidths = ["1%", "80px", "150px", "150px", "150px", "150px", "150px", "150px", ]; // px untuk setiap kolom
                        const headerRow = data.header.map((h, i) => `<th class="style bb ${i > 1 ? 'text-right' : 'text-left'}" style="min-width:${headerWidths[i]}; width:100px;">${h}</th>`).join('');
                        $("#tableHeader").html(headerRow);

                        let tbody = $("<tbody />");

                        if (data.record.length === 0) {
                            tbody.append("<tr><td colspan='" + data.header.length + "' class='text-left'>Tidak ada data</td></tr>");
                        }

                        let total_hutang = 0;
                        let total_hutang_bulan_ini = 0;
                        let total_hutang_bulan_1 = 0;
                        let total_hutang_bulan_2 = 0;
                        let total_hutang_bulan_3 = 0;
                        let total_hutang_lebih_dari_3_bulan = 0;
                        $.each(data.record, function(key, value) {

                            var tr = $("<tr>").append(
                                $("<td>").text(key + 1),
                                $("<td align=''>").html(`<a href="<?php echo site_url('report/outstandinginvoice?id_partner=') ?>${value.id_partner}" target="_blank">${value.nama_partner}</a>`),
                                $("<td align='right'>").text(formatNumber(value.total_hutang.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.hutang_bulan_ini.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.hutang_bulan_1.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.hutang_bulan_2.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.hutang_bulan_3.toFixed(2))),
                                $("<td align='right'>").text(formatNumber(value.hutang_lebih_dari_3_bulan.toFixed(2))),
                            );
                            tbody.append(tr);
                            total_hutang = total_hutang + value.total_hutang;
                            total_hutang_bulan_ini = total_hutang_bulan_ini + value.hutang_bulan_ini;
                            total_hutang_bulan_1 = total_hutang_bulan_1 + value.hutang_bulan_1;
                            total_hutang_bulan_2 = total_hutang_bulan_2 + value.hutang_bulan_2;
                            total_hutang_bulan_3 = total_hutang_bulan_3 + value.hutang_bulan_3;
                            total_hutang_lebih_dari_3_bulan = total_hutang_lebih_dari_3_bulan + value.hutang_lebih_dari_3_bulan;

                        });

                        if(data.record.length > 0 ){
                            var tr4 = $("<tr>").append(
                                $("<td colspan='2' class='style_space text-right'>").html('<b>Total : </b>'),
                                $("<td class='style_space text-right'>").html('<b>' + formatNumber(total_hutang.toFixed(2)) + '</b>'),
                                $("<td class='style_space text-right'>").html('<b>' + formatNumber(total_hutang_bulan_ini.toFixed(2)) + '</b>'),
                                $("<td class='style_space text-right'>").html('<b>' + formatNumber(total_hutang_bulan_1.toFixed(2)) + '</b>'),
                                $("<td class='style_space text-right'>").html('<b>' + formatNumber(total_hutang_bulan_2.toFixed(2)) + '</b>'),
                                $("<td class='style_space text-right'>").html('<b>' + formatNumber(total_hutang_bulan_3.toFixed(2)) + '</b>'),
                                $("<td class='style_space text-right'>").html('<b>' + formatNumber(total_hutang_lebih_dari_3_bulan.toFixed(2)) + '</b>'),
                            );
                            tbody.append(tr4);
                        }

                        $("#example1").append(tbody); // append parents

                        this_btn.button('reset');
                    }
                    $("#example1_processing").css('display', 'none'); // hidden loading

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    clearTimeout(slowProcessWarning);
                    unblockUI(function () { });
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
                "url": "<?php echo site_url('report/umurutang/export_excel') ?>",
                "data": {
                    partner: partner
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
                var url = '<?php echo base_url() ?>report/umurutang/export_pdf';
                window.open(url + '?params=' + arrStr, '_blank');
            }

        });
    </script>

</body>

</html>