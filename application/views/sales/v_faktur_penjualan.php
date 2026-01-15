<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <style>
            .btn-export {

            }
        </style>
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
                                                                <div class="col-xs-3"><label class="form-label">Tipe Penjualan</label></div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <select class="form-control input-sm select2 tipe" id="tipe" style="width: 100%">
                                                                        <option value=""></option>
                                                                        <?php
                                                                        foreach ($tipe as $key => $value) {
                                                                            ?>
                                                                            <option value="<?= $key ?>"><?= $value ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-3">
                                                                    <label class="form-label">Marketing</label>
                                                                </div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <select class="form-control select2" style="width: 100%" name="marketing" id="marketing">

                                                                        <option></option>
                                                                        <?php
                                                                        foreach ($sales as $key => $value) {
                                                                            ?>
                                                                            <option value="<?= $value->kode_sales_group ?>"><?= $value->nama_sales_group ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select> 
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 col-md-12">
                                                                <div class="col-xs-3">
                                                                    <label>Customer</label>
                                                                </div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <select class="form-control select2 input-sm" name="customer" id="customer"></select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
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
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <table id="tbl-faktur" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>No Faktur</th>
                                            <th>No Faktur Pajak</th>
                                            <th>Tanggal</th>
                                            <th>No SJ</th>
                                            <th>Tipe</th>
                                            <th>Marketing</th>
                                            <th>Customer</th>
                                            <th class="text-right">Dpp</th>
                                            <th class="text-right">Ppn</th>
                                            <th class="text-right">Total</th>
                                            <th>Pelunasan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="hide export-data"></button>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <script>
            var tanggal = "";
            $(function () {
                const table = $("#tbl-faktur").DataTable({
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
                        "url": "<?php echo site_url('sales/fakturpenjualan/list_data') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.tanggal = tanggal;
                            d.marketing = $("#marketing").val();
                            d.tipe = $("#tipe").val();
                            d.customer = $("#customer").val();
                        }
                    },
                    columnDefs: [
                        {
                            "targets": [0, 10, 11, 12],
                            "orderable": false
                        },
                        {
                            targets: [8,9,10],
                            createdCell: function(td, cellData, rowData, row, col) {
                                 $(td).addClass('text-right');
                            }
                        }
                    ],
                    dom: 'Bfrtip',
                    buttons: [
                        'pageLength',
                        {
                            "text": '<i class="fa fa-print"> <span>Export</span>',
                            "className": "btn-export btn-sm",
                            "action": function (e, dt, node, config) {
                                $(".export-data").trigger("click");
                            }
                        },
                                // Other buttons like 'copy', 'csv', 'excel', 'pdf', 'print'
                    ]
                });
                $(".export-data").on("click", function () {
                    $.ajax({
                        type: "post",
                        url: "<?php echo base_url(); ?>sales/fakturpenjualan/export",
                        data: {
                            tanggal: tanggal,
                            marketing: $("#marketing").val(),
                            tipe: $("#tipe").val(),
                            customer: $("#customer").val()
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        error: function (req, error) {
                            unblockUI(function () {
                                setTimeout(function () {
                                    alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                }, 500);
                            });
                        },
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {}, 200);
                        },
                        success: ((data) => {
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = data.data;
                            a.download = data.text_name;
                            document.body.appendChild(a);
                            a.click();
                        })
                    });
                });
                $(".select2").select2({
                    placeholder: "Pilih",
                    allowClear: true
                });
                $("#reset").on("click", function (e) {
                    e.preventDefault();
                    tanggal = "";
                    $(".reset").trigger("click");
                    $(".select2").val('').trigger('change');
                    table.ajax.reload();
                });

                $("#search").on("click", function (e) {
                    e.preventDefault();
                    tanggal = $("#tanggal").val();
                    table.ajax.reload();
                });

                //* Show collapse advanced search
                $('#advancedSearch').on('shown.bs.collapse', function () {
                    $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
                });

                //* Hide collapse advanced search
                $('#advancedSearch').on('hidden.bs.collapse', function () {
                    $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
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
//                        '1..P': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                });

                $('#customer').select2({
                    allowClear: true,
                    placeholder: "Select Customer",
                    width: '100%',
                    ajax: {
                        dataType: 'JSON',
                        type: "get",
                        url: "<?php echo base_url(); ?>accounting/kaskeluar/get_partner",
                        data: function (params) {
                            return{
                                search: params.term,
                                jenis: "customer"
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.id,
                                    text: item.nama
                                });
                            });
                            return {
                                results: results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                        }
                    }
                });


            });
        </script>
    </body>
</html>