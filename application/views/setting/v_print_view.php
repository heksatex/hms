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
                        <div class="box-header with-border">
                            <h3 class="box-title">List Printer Share</strong></h3>
                            <div class="pull-right text-right" id="btn-header">
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12 table-responsive over">
                                    <table class="table table-condesed table-hover rlstable  over" width="100%">
                                        <thead>
                                        <th class="style" width="10px">No</th>
                                        <th class="style" width="20px">Printer Alias</th>
                                        <th class="style" width="20px">Printer Share</th>
                                        <th class="style" width="20px">Alamat</th>
                                        <th class="style" width="20px">#</th>
                                        </thead>
                                        <tbody>
                                            <?php
                                            foreach ($printer as $key => $value) {
                                                ?>
                                                <tr>
                                                    <td><?= ($key + 1) ?></td>
                                                    <td><?= $value->alias_printer ?></td>
                                                    <td><?= $value->nama_printer_share ?></td>
                                                    <td><?= $value->ip_share ?></td>
                                                    <td>
                                                        <?php
                                                        if (json_encode($value) === $priterDefault) {
                                                            ?>
                                                            <button class="btn btn-success btn-xs">Default</button>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <button class="btn btn-default btn-xs set_default" data-printer='<?= json_encode($value) ?>'>Set Default</button>
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <footer class="main-footer">
            <?php $this->load->view("admin/_partials/footer.php") ?>
            <?php $this->load->view("admin/_partials/js.php") ?>
        </footer>
        <script>
            $(function () {
                $(".set_default").on("click", function () {
                    var printer = $(this).data('printer');
                    confirmRequest("Set Pinter", "Pilih Printer Sebagai Default ? ", function () {
                        please_wait(function () {});
                        $.ajax({
                            type: "POST",
                            url: "<?= base_url('setting/printershare/set') ?>",
                            data: {
                                data: JSON.stringify(printer)
                            },
                            success: function (data) {
                                location.reload();
                            }, error: function (req, error) {
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                    }, 500);
                                });
                            }
                        });
                    });
                });
            });
        </script>
    </body>
</html>