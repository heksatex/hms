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
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                            <div class="col-xs-12 table-responsive">
                                <table id="tbl-acc-periode" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="style">
                                                No
                                            </th>
                                            <th>
                                                Tahun
                                            </th>
                                            <th>
                                                Periode ACC
                                            </th>
                                            <th>
                                                Status
                                            </th>
                                            <th>
                                                #
                                            </th>
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
        <script>
            $(function () {
                const table = $('#tbl-acc-periode').DataTable({
                    "iDisplayLength": 10,
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
                        "url": "<?php echo site_url('accounting/periode/list_data') ?>",
                        "type": "POST"
                    }
                    , "columnDefs": [
                        {
                            "targets": [0,4],
                            "orderable": false
                        }
                    ],
                });
            });
        </script>
    </body>
</html>