<!DOCTYPE html>
<html>

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
                            <table id="tbl-list-outsanding" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="no">No</th>
                                        <th>No Bukti</th>
                                        <th>Supplier</th>
                                        <th>CoA</th>
                                        <th>Tanggal</th>
                                        <th>uraian</th>
                                        <th>Curr</th>
                                        <th>Kurs</th>
                                        <th>Total (Rp)</th>
                                        <th>Total (Valas)</th>
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
        var tanggal = "";
        $(function() {
            const table = $("#tbl-list-outsanding").DataTable({
                "iDisplayLength": 50,
                "processing": true,
                "serverSide": true,
                "order": [],
                "scrollX": true,
                "scrollY": "calc(100vh - 250px)",
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "stateSave": false,
                "ajax": {
                    "url": "<?php echo site_url('accounting/outstandingkasbankpiutang/list_data_kas_bank') ?>",
                    "type": "POST",
                },
                "columnDefs": [{
                        "targets": [0],
                        "orderable": false
                    },
                    {
                        'targets': [ 7, 8, 9],
                        "className": "text-right"
                    }
                ]
            });



        });
    </script>
</body>

</html>