<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .cancelPL{
                color: red;
            }
            .donePL{
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
                <!-- Main content -->
                <section class="content">
                    <!--  box content -->
                    <div class="box">
                        <div class="box-body">
                            <div class="col-xs-12 table-responsive">
                                <table id="picklist" class="table">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>No Picklist</th>
                                            <th>Customer</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Jenis</th>
                                            <th>Type Bulk</th>
                                            <th>Marketing</th>
                                            <th>Keterangan</th>
                                            <th>Status</th>
                                            <th>Pcs</th>
                                            <th>Total Qty</th>
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
                    "stateSave": true,
                    "ajax": {
                        "url": "<?php echo site_url('warehouse/picklist/data') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 7, 9, 10],
                            "orderable": false
                        }
                    ],
                    "createdRow": function (row, data, dataIndex) {
                        if (data[8] === "Cancel") {
                            $(row).addClass('cancelPL');
                        } else if (data[8] === "Done") {
                            $(row).addClass('donePL');
                        }
                    }
                });
            });
        </script>
    </body>
</html>