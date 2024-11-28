<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <style>
            .ws{
                white-space: nowrap;
            }
            .divListviewHead table  {
                display: block;
                height: calc( 100vh - 250px );
                overflow-x: auto;
            }
            #tabelGTP{
                max-height: 100vh
            }
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php $this->load->view("admin/_partials/topbar.php") ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><b>Report Goods To Push</b></h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-gtp" id="form-gtp" action="<?= base_url('report/goodstopush/search') ?>">
                                <div class="col-md-8" style="padding-right: 0px !important;">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-md-3 col-xs-6">
                                                <label class="form-label required" id="label_filter_tanggal">Report Date</label>
                                            </div>
                                            <div class="col-md-3 col-xs-6">
                                                <select class="form-control select2" name="report_date" id="report_date" required>
                                                    <option></option>
                                                    <?php foreach ($dates as $key => $value) {?>
                                                    <<option value="<?=$value->dt ?>"><?=$value->dt ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 col-xs-6">
                                                <label class="form-label">Lokasi</label>
                                            </div>
                                            <div class="col-md-3 col-xs-6">
                                                <select name="lokasi" id="lokasi" class="form-control select2">
                                                    <option value=""></option>
                                                    <option value="GJD/Stock">GJD/Stock</option>
                                                    <option value="GRG/Stock">GRG/Stock</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-md-3 col-xs-6">
                                                <label class="form-label" id="label_filter_tanggal">Sales</label>
                                            </div>
                                            <div class="col-md-3 col-xs-6">
                                                <select name="sales" id="sales" class="form-control select2">
                                                    <option></option>
                                                    <?php
                                                    foreach ($sales as $key => $value) {
                                                        ?>
                                                        <option value="<?= $value->nama_sales_group ?>"><?= $value->nama_sales_group ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-sm btn-default" name="btn-search" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-search"  style="color:green"></i> Search</button>
                                </div>
                                <br>
                                <br>
                                <!--                                <div class="col-md-12">
                                                                    <div class="panel panel-default" style="margin-bottom: 0px;">
                                                                        <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                                                                            <div class="panel-body" style="padding: 5px">
                                                                                <div class="col-md-8 col-xs-8">
                                                                                    <div class="form-group">
                                                                                        <div class="col-xs-4">
                                                                                            <label class="form-label" id="label_filter_tanggal">Sales</label>
                                                                                        </div>
                                                                                        <div class="col-xs-8 col-md-8">
                                
                                                                                            <select name="sales" class="form-control select2" style="width: 80%">
                                                                                                <option></option>
                                <?php
                                foreach ($sales as $key => $value) {
                                    ?>
                                                                                                                                                <option value="<?= $value->nama_sales_group ?>"><?= $value->nama_sales_group ?></option>
                                    <?php
                                }
                                ?>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>-->
                            </form>
                            <div class="row">
                                <div class="col-md-12 table-responsive over">
                                    <table id="gtp" class="table table-condesed table-hover rlstable over" width="100%">
                                        <thead>
                                            <tr>
                                                <th></th>
                                                <th>Corak</th>
                                                <th>Kategori</th>
                                                <th>Jml Warna</th>
                                                <th>Jml LOT</th>
                                                <th>Qty / Uom</th>
                                                <th>Qty2 / Uom2</th>
                                                <th>Lebar</th>
                                                <th>Buyer</th>
                                                <th>Lokasi</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/modal.php") ?>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {
                const tableGtp = $("#gtp").DataTable({
                    "iDisplayLength": 25,
                    "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    "processing": true,
                    "serverSide": true,
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "ajax": {
                        "url": "<?= site_url('report/goodstopush/data') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.report_date = $("#report_date").val();
                            d.sales = $("#sales").val();
                            d.lokasi = $("#lokasi").val();
                        }
                    },
                    "columnDefs": [
                        {
                            "targets": [0],
                            "orderable": false
                        }
                    ],
                    "fnDrawCallback": function () {
                        $(".detail").on("click", function () {
                            var data = $(this).data();
                            $("#view_data").modal({
                                show: true,
                                backdrop: 'static'
                            });
                            $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                            $('.modal-title').text('Details');
                            $.post("<?= base_url('report/goodstopush/details/') ?>", data, function (datas) {
                                setTimeout(function () {
                                    $(".view_body").html(datas.content);
                                }, 500);
                            });
                        });
                    }
                });
                $(".detail").on("click", function () {
                    var data = $(this).data();
                    $("#view_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text('Details');
                    $.post("<?= base_url('report/goodstopush/details/') ?>", data, function (datas) {
                        setTimeout(function () {
                            $(".view_body").html(datas.content);
                        }, 500);
                    });
                });
                $(".select2").select2({
                    allowClear: true,
                    placeholder: "Pilih"
                });
                const formrd = document.forms.namedItem("form-gtp");
                formrd.addEventListener(
                        "submit",
                        (event) => {
                    tableGtp.ajax.reload();
                    event.preventDefault();
                },
                        false
                        );
            });
        </script>
    </body>
</html>