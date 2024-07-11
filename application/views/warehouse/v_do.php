<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <style>
            .cancel{
                color: red;
            }
            .done{
                color: green;
            }
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                            <div class="col-xs-12" style="padding-right: 0px !important;">
                                <div class="col-md-4 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                                    <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                                        <label style="cursor:pointer;">
                                            <i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i>
                                            Advanced Filter 
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <br>
                            <div class="col-xs-12">
                                <div class="panel panel-default" style="margin-bottom: 0px;">
                                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                                        <form class="panel-body" style="padding: 5px" id="form-filter">
                                            <div class="col-xs-8">
                                                <div class="col-xs-8" >
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Customer</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <input type="text" name="customer_text" id="customer_text" class="form-control customer_text"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-8">
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Tanggal Kirim</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <input type="text" name="tanggal_kirim" id="tanggal_kirim" value="" class="form-control tanggal_kirim"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-xs-4">
                                                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="search" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                                    Cari <i class="fa fa-search"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" name="btn-clear" id="btn-clear"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Clear</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-12 table-responsive over">
                                <table class="table table-condesed table-hover rlstable  over" width="100%" id="delivery" >
                                    <thead>                          
                                        <tr>
                                            <th class="style" width="10px">No</th>
                                            <th class="style">No DO</th>
                                            <th class="style">Surat Jalan</th>
                                            <th class="style">Picklist</th>
                                            <th class="style">Tipe</th>
                                            <th class="style">Tanggal Kirim</th>
                                            <th class="style">Buyer</th>
                                            <th class="style">Marketing</th>
                                            <th class="style">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div id="shwmdl" style="display: none">
            <div style="padding-top: 0px; text-align: center">

                <button type="button" id="btn-tambah" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah</button>

            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <footer class="main-footer">
            <?php $this->load->view("admin/_partials/modal.php") ?>
        </footer>
        <script>
            $(function () {
                $('#advancedSearch').on('shown.bs.collapse', function () {
                    $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
                });
                //* Hide collapse advanced search
                $('#advancedSearch').on('hidden.bs.collapse', function () {
                    $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
                });
                $('input[name="tanggal_kirim"]').daterangepicker({
                    autoUpdateInput: false,
                    locale: {
                        cancelLabel: 'Clear',
                        format: 'YYYY-MM-DD'
                    }
                });

                $('input[name="tanggal_kirim"]').on('apply.daterangepicker', function (ev, picker) {
                    $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
                });

                $('input[name="tanggal_kirim"]').on('cancel.daterangepicker', function (ev, picker) {
                    $(this).val('');
                });

                $("#btn-clear").click(function () {
                    $('#form-filter').trigger("reset");
                    dataTable.draw();
                });


                const dataTable = $("#delivery").DataTable({
                    "iDisplayLength": 50,
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "stateSave": false,
                    "ajax": {
                        "url": "<?= base_url('warehouse/deliveryorder/data') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.customer = $("#customer_text").val();
                            d.tanggal_kirim = $("#tanggal_kirim").val();
                        }
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 6, 7],
                            "orderable": false
                        }
                    ],
                    "createdRow": function (row, data, dataIndex) {
                        if (data[8] === "cancel") {
                            $(row).addClass('cancel');
                        } else if (data[8] === "done") {
                            $(row).addClass('done');
                        }
                    }
                });
                $("#search").on("click", function () {
                    dataTable.draw();
                });
                $("#btnShow").html("");
                $("#btnShow").html($("#shwmdl").html());
                $("#btn-tambah").unbind("click").off("click").on("click", function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text('Pilih Picklist');
                    $.get("<?= base_url('warehouse/deliveryorder/data_picklist') ?>", {

                    }, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);
                        }, 1000);
                        $(".modal-footer #btn-tambah").hide();
                    });
                });
            });
        </script>
    </body>
</html>