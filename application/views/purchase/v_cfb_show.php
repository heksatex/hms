<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style type="text/css">
            table.table td .add {
                display: none;
            }
            .width-btn {
                width: 54px !important;
            }
            table.table td .cancel {
                display: none;
                color : red;
                margin: 10 0px;
                min-width:  24px;
            }
            .cancelPL{
                color: red;
            }

            #btn-cancel {
                display: none;
            }

            @media screen and (min-width: 768px) {
                .over {
                    overflow-x: visible !important;
                }
            }

            /*
            @media screen and (max-width: 767px) {
              .over {
               overflow-y: scroll !important; 
              }
            }
            */



            <?php
            if ($datas->status === "confirm") {
                ?>
                #btn-cancel {
                    display: inline-block;
                }
                <?php
            }
            ?>
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini" id="block-page">
        <div class="wrapper" >
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
            <div class="content-wrapper" >
                <section class="content-header"  >
                    <div id ="status_bar">
                        <?php
                        $data['jen_status'] = $datas->status;
                        $this->load->view("admin/_partials/statusbar.php", $data);
                        ?>
                    </div>
                </section>

                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><b><?= $datas->kode_cfb; ?></b></h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal">
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Kode CFB</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" readonly="readonly" value="<?php echo $datas->kode_cfb ?>" />
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Create Date</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" readonly="readonly" value="<?php echo $datas->create_date ?>" />
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Schedule Date</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" readonly="readonly" value="<?php echo $datas->schedule_date ?>" />
                                            </div>                                    
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Sales Order</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" readonly="readonly" value="<?php echo $datas->sales_order ?>" />
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Priority</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" readonly="readonly" value="<?php echo $datas->priority ?>" />
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Warehouse</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" readonly="readonly" value="<?php echo $datas->nama_warehouse ?>" />
                                            </div>                                    
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="">
                                        <ul class="nav nav-tabs " >
                                            <li class="active"><a href="#tab_1" data-toggle="tab">Procurements Lines</a></li>
                                        </ul>
                                        <div class="tab-content over"><br>
                                            <div class="tab-pane active" id="tab_1">
                                                <div class="col-md-12 table-responsive over">
                                                    <table class="table table-condesed table-hover rlstable  over" width="100%" id="cfb-detail" >
                                                        <thead>                          
                                                            <tr>
                                                                <th class="style no">No.</th>
                                                                <th class="style" width="200px">Kode Product</th>
                                                                <th class="style" width="150px">Product</th>
                                                                <th class="style" style="width:100px; text-align: right;" >Qty</th>
                                                                <th class="style" width="80px">Uom</th>
                                                                <th class="style" width="200px">Reff Notes</th>
                                                                <th class="style" width="60px">Status</th>
                                                            </tr>
                                                        </thead>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>
                <?php $this->load->view("admin/_partials/js.php") ?>
                <?php $this->load->view("admin/_partials/footer_new.php") ?>
            </footer>
        </div>
        <script>
            $(function () {
                const table = $("#cfb-detail").DataTable({
                    "iDisplayLength": 50,
                    "processing": true,
                    "serverSide": true,
                    "order": [],

                    "paging": false,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false,
                    "stateSave": false,
                    "ajax": {
                        "url": "<?= site_url('purchase/callforbids/list_data_detail/' . $id) ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 5],
                            "orderable": false
                        }
                    ],
                    "createdRow": function (row, data, dataIndex) {
                        if (data[6] === "cancel") {
                            $(row).addClass('cancelPL');
                        }
                    },
                    "aoColumns": [
                        null,
                        null,
                        null,
                        {"sClass": "text-right"},
                        null,
                        null,
                        null
                    ]
                });

                $("#btn-cancel").off("click").on("click", function () {
                    confirmRequest("Call For Bids", "Batalkan status Confirm ? ", function () {
                        $.ajax({
                            url: "<?php echo site_url('purchase/callforbids/update_status') ?>",
                            type: "POST",
                            data: {
                                ids: "<?= $datas->ids ?>",
                                status: "draft",
                                before_status: "confirm"
                            },
                            beforeSend: function (xhr) {
                                please_wait(function () {});
                            },
                            success: function (data) {
                                alert_notify(data.icon, data.message, data.type, function () {});
                                location.reload();
                            },
                            error: function (err) {
                                unblockUI(function () {}, 100);
                                alert_notify("fa fa-warning", err.responseJSON.message, "danger", function () {});
                            },
                            complete: function (jqXHR, textStatus) {
                                unblockUI(function () {}, 100);
                            }
                        });
                    });
                })
            });
        </script>
    </body>
</html>