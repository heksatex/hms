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
                                                <form class="form-horizontal form-search">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-3">
                                                                    <label class="form-label">Tanggal Pakai</label>
                                                                </div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <input type="text" class="form-control input-sm" name="tanggal_pakai" id="tanggal_pakai">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-3">
                                                                    <label class="form-label">No Aset</label>
                                                                </div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <input type="text" class="form-control input-sm" name="no_aset" id="no_aset">
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
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="col-xs-12 table-responsive">
                                <table id="tbl-bk" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>No Aset</th>
                                            <th>Nama</th>
                                            <th>Tanggal Beli</th>
                                            <th>Tanggal Pakai</th>
                                            <th>Harga</th>
                                            <th>Kategori</th>
                                            <th>Kelempok</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view("admin/_partials/js.php") ?>
            <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        </div>
        <script>
            $(function () {
                $("#btn-tambah").on("click", function () {
                    window.location.href = "<?php echo site_url("{$class}/asettetap/add") ?>";
                });
                //* Show collapse advanced search
                $('#advancedSearch').on('shown.bs.collapse', function () {
                    $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
                });

                //* Hide collapse advanced search
                $('#advancedSearch').on('hidden.bs.collapse', function () {
                    $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
                });
                const table = $("#tbl-bk").DataTable({
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
                        "url": "<?php echo site_url("{$class}/asettetap/list_data") ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.tanggal = $("#tanggal_pakai").val();
                            d.no_aset = $("#no_aset").val();
                        }
                    },
                    columnDefs: [
                        {
                            "targets": [0, 8],
                            "orderable": false
                        }
                    ]
                });

                $('#tanggal_pakai').daterangepicker({
                    endDate: moment().startOf('day'),
                    startDate: moment().startOf('day').add(-1, 'week'),
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    ranges: {
                        'H': [moment(), moment()],
                        'H-1': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'H-7': [moment().subtract(6, 'days'), moment()],
                        '1..H': [moment().startOf('month'), moment().endOf('month')],
                        '1..P': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                });

                $('#tanggal_pakai').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });
                $("#reset").on("click", function (e) {
                    e.preventDefault();
//                    tanggal = "";
                    $(".reset").trigger("click");
                    table.ajax.reload();
                });
                $("#search").on("click",function(){
                    table.ajax.reload();
                });
            })
        </script>
    </body>
</html>