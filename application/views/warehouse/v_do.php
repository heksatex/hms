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
                            <div class="col-md-12 table-responsive over">
                                <table class="table table-condesed table-hover rlstable  over" width="100%" id="delivery" >
                                    <thead>                          
                                        <tr>
                                            <th class="style" width="10px">No</th>
                                            <th class="style">No DO</th>
                                            <th class="style">Surat Jalan</th>
                                            <th class="style">Picklist</th>
                                            <th class="style">Tipe</th>
                                            <th class="style">Tanggal Kirim</th>
                                            <th class="style">Buyer</th>
                                            <th class="style">Marketing</th>
                                            <th class="style">Status</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <div id="shwmdl" style="display: none">
            <div style="padding-top: 0px; text-align: center">

                <button type="button" id="btn-tambah" class="btn btn-success btn-sm"><i class="fa fa-plus"></i> Tambah</button>

            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <footer class="main-footer">
            <?php $this->load->view("admin/_partials/modal.php") ?>
        </footer>
        <script>
            $(function () {

                const dataTable = $("#delivery").DataTable({
                    "iDisplayLength": 50,
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "ajax": {
                        "url": "<?= base_url('warehouse/deliveryorder/data') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 6, 7],
                            "orderable": false
                        }
                    ]
                });

                $("#btnShow").html("");
                $("#btnShow").html($("#shwmdl").html());

                $("#btn-tambah").on("click", function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text('Pilih Picklist');
                    $.get("<?= base_url('warehouse/deliveryorder/data_picklist') ?>", {

                    }, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);

                        }, 1000);
                        $(".modal-footer #btn-tambah").hide();
                    });
                });
            });
        </script>
    </body>
</html>