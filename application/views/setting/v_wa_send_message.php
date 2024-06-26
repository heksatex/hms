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
                                <table id="tableWaSend" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>Message</th>
                                            <th>User</th>
                                            <th>Group</th>
                                            <th>Status</th>
                                            <th>Created At</th>               
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
                const table = $('#tableWaSend').DataTable({
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
                        "url": "<?php echo site_url('setting/wa_send_message/get_data') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 1],
                            "orderable": false
                        },
                        {
                            "targets": 1,
                            render: function (data, type, full, meta) {
                                return "<div class='text-wrap width-400'>" + data + "</div>";
                            }
                        }
                    ],
                    "fnDrawCallback": function () {
                        $(".resend").off("click").on("click", function () {
                            const e = this;
                            confirmRequest("Kirim Pesan", "Kirim Ulang Pesan ? ", () => {
                                $.ajax({
                                    url: "<?php echo site_url('setting/wa_send_message/resend') ?>",
                                    type: "POST",
                                    data: {
                                        id: $(e).attr("data-id")
                                    },
                                    beforeSend: function () {
                                        please_wait(function () {});
                                    },
                                    success: function (data) {
                                        location.reload();
                                    },
                                    complete: function (jqXHR, textStatus) {
                                        unblockUI(function () {});
                                    }
                                });
                            });
                        });

                    }
                });
            })
        </script>
    </body>
</html>