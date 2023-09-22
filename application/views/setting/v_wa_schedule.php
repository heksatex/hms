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

            <!-- Menu Side Bar -->
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
                                <table id="tableWaTemplate" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>Nama</th>
                                            <th>Pesan</th>
                                            <th>Group</th>
                                            <th>Setiap</th>
                                            <th>Waktu Kirim</th>
                                            <th>#</th>
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
            <?php $this->load->view("admin/_partials/modal.php") ?>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>

        <script>
            $(function () {
                const table = $('#tableWaTemplate').DataTable({
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
                        "url": "<?php echo site_url('setting/wa_schedule/get_data') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 2, 3, 5],
                            "orderable": false,
                        }
                    ]
                });
                $('#tableWaTemplate tbody').on('click', '.btn-delete-doc', function () {
                    let dataid = $(this).data('id');
                    deleteDocument('<?= site_url('setting/wa_schedule/delete') ?>', {id: dataid})
                            .then(resp => {
                                location.reload();
                            }).catch(err => {
                        let error = err.responseJSON;
                        alert_notify(error.icon, error.message, error.type, function () {});
                    });
                });
            })
        </script>
    </body>
</html>