<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .print-ballid{
                position: relative;
            }
            .print-ballid .tooltiptext {
                visibility: hidden;
                width: 120px;
                background-color: black;
                color: #fff;
                text-align: center;
                border-radius: 6px;
                padding: 5px 0;

                /* Position the tooltip */
                position: absolute;
                z-index: 1;
                bottom: 150%;
                left: 50%;
                margin-left: -60px
            }

            .print-ballid:hover .tooltiptext {
                visibility: visible;
            }
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data);
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
                                            <th>Keterangan</th>
                                            <th>Marketing</th>
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
        </div>
        <?php $this->load->view("admin/_partials/modal.php") ?>
        <div style="display: none;" id="pilihan-print">
            <div class="row">
                <div class="col-md-3 col-xs-6">
                    <button class="btn btn-default btn-sm print-bulk" type="button" data-print="s"><i class="fa fa-print"></i> S</button>
                </div>
                <div class="col-md-3 col-xs-6">
                    <button class="btn btn-default btn-sm print-bulk" type="button" data-print="t"><i class="fa fa-print"></i> T</button>
                </div>
                <div class="col-md-3 col-xs-6">
                    <button class="btn btn-default btn-sm print-bulk" type="button" data-print="u"><i class="fa fa-print"></i> U</button>
                </div>
                <div class="col-md-3 col-xs-6">
                    <button class="btn btn-default btn-sm print-bulk" type="button" data-print="v"><i class="fa fa-print"></i> V</button>
                </div>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {
                const table = $('#picklist').DataTable({
                    "iDisplayLength": 25,
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
                        "url": "<?php echo site_url('warehouse/bulk/data') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 5, 7],
                            "orderable": false
                        }
                    ],
                    "fnDrawCallback": function () {
                        $(".print-ballid").on("click", function (e) {
                            e.preventDefault();
                            $("#print_data").modal({
                                show: true,
                                backdrop: 'static'
                            });
                            $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                            $('.modal-title').text('Pilihan Mode Print ' + $(this).attr("data-id"));
                            let pl = $(this).attr("data-id");
                            $(".print_data").html($("#pilihan-print").html());
                            $(".print-bulk").on('click', function () {
                                $.post("<?= base_url('warehouse/bulk/print_bulk/') ?>",
                                        {
                                            "pl": pl,
                                            "print_mode": $(this).attr("data-print")
                                        }
                                , function (response) {
                                    var divp = document.getElementById('printed');
                                    divp.innerHTML = response.data;
                                    print_voucher();
                                });
                            });

                        });
                    }
                });
            });
            const print_voucher = function () {

                var win = window.open();
                win.document.write($("#printed").html());
                setTimeout(function () {
                    win.document.close();
                    win.print();
                    win.close();
                }, 500);


            };
        </script>
    </body>
</html>