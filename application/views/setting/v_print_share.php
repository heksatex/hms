<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
        <style>
            .cancel{
                color: red;
            }
            .done{
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
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                            <div class="row">
                                <form name="input" id="input" class="form-horizontal" role="form" method="POST" action="<?= base_url('setting/printershare/save') ?>">
                                    <div class="col-md-8">
                                        <div class="fields-group">
                                            <div class="form-group">
                                                <label for="tanggal" class="col-sm-2 required control-label">Nama Printer Share</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                                        <input type="text" name="nama_printer_share" class="form-control" id="nama_printer_share" required>
                                                        <input type="hidden" name="posisi" id="posisi">
                                                        <input type="hidden" id="ids" name="ids">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="tanggal" class="col-sm-2 required control-label">IP Printer Share</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                                        <input type="text" name="ip_share" class="form-control" id="ip_share" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-sm btn-default" name="btn-save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                            Simpan <i class="fa fa-save"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" name="btn-clear" id="btn-clear" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Batal</button>
                                    </div>
                                </form>
                            </div>
                            <br>
                            <br>
                            <div class="col-xs-12 table-responsive">
                                <table id="printshare" class="table">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>Nama Printer</th>
                                            <th>IP Printer</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <footer class="main-footer">
            <?php $this->load->view("admin/_partials/footer.php") ?>
        </footer>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {
                
                const form = document.forms.namedItem("input");
                form.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("input").then(
                            response => {
                                alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                if (response.status === 200) {
                                    $("#btn-clear").trigger("click");
                                    table.ajax.reload();
                                }
                            }
                    ).catch().finally(() => {
                        $("#btn-clear").trigger("click");
                        unblockUI(function () {});
                    });
                    event.preventDefault();
                },
                        false
                        );
                
                const table = $('#printshare').DataTable({
                    "iDisplayLength": 50,
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "stateSave": false,
                    "ajax": {
                        "url": "<?= base_url('setting/printershare/get_data') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0],
                            "orderable": false
                        }
                    ],
                    "fnDrawCallback": function () {
                        $(".edit_item").on('click', function () {
                            var data = $(this).data();
                            $("#posisi").val("edit");
                            $("#ids").val(data.id);
                            $("#nama_printer_share").val(data.print);
                            $("#ip_share").val(data.ip);
                        });
                    }
                });

                $("#btn-clear").on("click", function () {
                    $("#posisi").val("");
                    $("#ids").val("");
                    $("#input").trigger('reset');
                });
                
            });
        </script>
    </body>
</html>