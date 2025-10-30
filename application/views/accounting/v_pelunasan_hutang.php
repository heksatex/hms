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
                            <table id="tbl-pelunasan-hutang" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="no">No</th>
                                        <th>No Pelunasan</th>
                                        <th>Tanggal dibuat</th>
                                        <th>Supplier</th>
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
        $(function() {
            // const table = $("#tbl-pelunasan-hutang").DataTable({});
            const table = $('#tbl-pelunasan-hutang').DataTable({
                "iDisplayLength": 50,
                "processing": true,
                "serverSide": true,
                "order": [],
                "scrollX": true,
                "scrollY": "calc(101vh - 250px)",
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "ajax": {
                    "url": "<?php echo site_url('accounting/pelunasanhutang/get_data') ?>",
                    "type": "POST"
                },
                "columnDefs": [{
                    "targets": [0],
                    "orderable": false
                }],
            });
        });
    </script>
</body>

</html>