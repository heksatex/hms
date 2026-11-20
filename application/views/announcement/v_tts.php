<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu-new.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar-new.php", $data)
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
                            <div class="col-xs-12 table-responsive">
                                <table id="tbl-tts" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>Text</th>
                                            <th>Pengulangan</th>
                                            <th>Display</th>
                                            <th>Jenis Suara</th>
                                            <th>Created At</th>
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
        <script>
            $(function () {
                const table = $("#tbl-tts").DataTable({
                    "iDisplayLength": 50,
                    "searchDelay": 500,
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
                        "url": "<?php echo site_url("announcement/tts/list") ?>",
                        "type": "POST",
                    },
                    columnDefs: [
                        {
                            "targets": [0, 6],
                            "orderable": false
                        }
                    ],
                    "createdRow": function (row, data, dataIndex) {
                        $(row).addClass('text-capitalize');
                    }
                });
            })
        </script>
    </body>
</html>