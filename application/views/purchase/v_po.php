<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .cancelPL{
                color: red;
            }
            #btn-tambah {
                display: none;
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
                            <div class="col-xs-12 table-responsive">
                                <table id="tbl-po" class="table">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>No PO</th>
                                            <th>Supplier</th>
                                            <th>Order Date</th>
                                            <th>Create Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {
                const table = $("#tbl-po").DataTable({
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
                        "url": "<?php echo site_url('purchase/purchaseorder/list_data') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0],
                            "orderable": false
                        }
                    ],
                    "createdRow": function (row, data, dataIndex) {
                        if (data[6].toLowerCase() === "cancel") {
                            $(row).addClass('cancelPL');
                        }
                    }
                });
            });
        </script>
    </body>
</html>