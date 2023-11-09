<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .miniBarProgress {
                height: 100%;
                position: absolute;
                top: 0rem;
                left: 0rem;
            }
            .miniBar {
                height: 0.5rem;
                border: 1px solid #8a898a;
                position: relative;
                width: -webkit-calc(100% - 2rem);
                width: -moz-calc(100% - 2rem);
                width: calc(100% - 2rem);
                margin-right: 0.5rem;
            }

            /* Tooltip text */
            .miniBarProgress .tooltiptext {
                visibility: hidden;
                width: 120px;
                bottom: 100%;
                left: 50%;
                margin-left: -60px;
                background-color: black;
                color: #fff;
                text-align: center;
                padding: 5px 0;
                border-radius: 6px;

                /* Position the tooltip text - see examples below! */
                position: absolute;
                z-index: 1;
            }

            /* Show the tooltip text when you mouse over the tooltip container */
            .miniBarProgress:hover .tooltiptext {
                visibility: visible;
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
                <!-- Main content -->
                <section class="content">
                    <!--  box content -->
                    <div class="box">
                        <div class="box-body">
                            <div class="col-xs-12 table-responsive">
                                <table id="picklist" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>No Picklist</th>                  
                                            <th>Tanggal Dibuat</th>
                                            <th>Jenis</th>
                                            <th>Type Bulk</th>
                                            <th>Keterangan</th>
                                            <th>Sales</th>
                                            <th>Status Item %</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {
                const table = $('#picklist').DataTable({
                    "iDisplayLength": 50,
                    "processing": true,
                    "serverSide": true,
                    "order": [],

                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "ajax": {
                        "url": "<?php echo site_url('warehouse/picklistrealisasi/data') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.submenu = "<?= $submenu ?>";
                        }
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 5],
                            "orderable": false
                        }
                    ]
                });
            });
        </script>
    </body>
</html>