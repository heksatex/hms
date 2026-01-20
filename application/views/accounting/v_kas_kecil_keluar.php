<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu-new.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar-new.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                           <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-md-12 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;cursor:pointer;">
                                        <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                                            <label>
                                                <i class="showAdvanced glyphicon glyphicon-triangle-bottom">&nbsp;</i>Filter
                                            </label>
                                        </div>
                                    </div>

                                </div>
                                <br>
                                <br>
                                <div class="col-md-12">
                                    <div class="panel panel-default" style="margin-bottom: 0px;">
                                        <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                                            <div class="panel-body" style="padding: 5px">
                                                <form id="form-search" class="form-horizontal form-search">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-3">
                                                                    <label class="form-label">Tanggal</label>
                                                                </div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <input type="text" class="form-control" name="tanggal" id="tanggal">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-3">
                                                                    <label class="form-label">No Bukti</label>
                                                                </div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <input type="text" class="form-control" name="no_bukti" id="no_bukti">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-3">
                                                                    <label class="form-label">Supplier</label>
                                                                </div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <input type="text" class="form-control" name="customer" id="customer">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-3">
                                                                    <label class="form-label">Uraian</label>
                                                                </div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <input type="text" class="form-control" name="uraian" id="uraian">
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="col-md-12 col-xs-12">
                                                                    <button type="button" class="btn btn-success btn-sm" id="search"><i class="fa fa-search"></i> Cari</button>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <div class="col-md-12 col-xs-12">
                                                                    <button type="button" class="btn btn-warning btn-sm" id="reset">Reset</button>
                                                                    <button type="reset" class="btn btn-warning btn-sm reset hide"></button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <div class="col-md-12 col-xs-12">
                                                                    <button type="button" class="btn btn-default btn-sm" id="export"><i class="fa fa-file-excel-o" style="color: green;"></i> Ekspor </button>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <table id="tbl-kk" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>No Bukti</th>
                                            <th>Supplier</th>
                                            <th>Tanggal</th>
                                            <th>No ACC (Kredit)</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <script>
            var tanggal = "";
            $(function () {
                const table = $("#tbl-kk").DataTable({
                    "iDisplayLength": 50,
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "scrollX": true,
                    "scrollY": "calc(85vh - 250px)",
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "ajax": {
                        "url": "<?php echo site_url('accounting/kaskecilkeluar/list_data') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.tanggal = tanggal;
                            d.customer = $("#customer").val();
                            d.no_bukti = $("#no_bukti").val();
                            d.uraian = $("#uraian").val();
                        }
                    },
                    columnDefs: [
                        {
                            "targets": [0,6],
                            "orderable": false
                        }
                    ]
                });
                $("#reset").on("click", function (e) {
                    e.preventDefault();
                    tanggal = "";
                    $(".reset").trigger("click");
                    table.ajax.reload();
                });

                $("#search").on("click", function (e) {
                    e.preventDefault();
                    tanggal = $("#tanggal").val();
                    table.ajax.reload();
                });

                $("#export").on("click", function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: "<?= base_url('accounting/kaskecilkeluar/ekspor/') ?>",
                        type: "POST",
                        data: {
                            tanggal: $("#tanggal").val(),
                            customer: $("#customer").val(),
                            no_bukti: $("#no_bukti").val(),
                            uraian: $("#uraian").val()
                        },
                        beforeSend: function (xhr) {
                            please_wait(function () {});
                        },
                        success: function (data) {
                            unblockUI(function () {});
//                            window.open(data.url, "_blank").focus();
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = data.data;
                            a.download = data.text_name;
                            document.body.appendChild(a);
                            a.click();

                        },
                        error: function (req, error) {
                            unblockUI(function () {
                                setTimeout(function () {
                                    alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                }, 500);
                            });
                        }
                    });
                });
                
                $('#tanggal').daterangepicker({
//                    autoUpdateInput: false,
                    endDate: moment().startOf('day'),
                    startDate: moment().startOf('day').add(-1, 'week'),
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    ranges: {
                        'H': [moment(), moment()],
                        'H-1': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'H-7': [moment().subtract(6, 'days'), moment()],
//                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        '1..H': [moment().startOf('month'), moment().endOf('month')],
                        '1..P': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                });
                
            });
        </script>
    </body>
</html>