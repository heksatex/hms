<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
 
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
                                            <th>Customer</th>   
                                            <th>Tanggal Dibuat</th>
                                            <th>Jenis</th>
                                            <th>Type Bulk</th>
                                            <th>Marketing</th>
                                            <th>Keterangan</th>
                                            <th>Total Lot</th>
                                            <th>Realisasi Lot ( % )</th>
                                            <th>Validasi Lot ( % )</th>
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
                    "stateSave": true,
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
                            "targets": [0, 7,8,9,10],
                            "orderable": false
                        }
                    ]
                });
            });
        </script>
    </body>
</html>