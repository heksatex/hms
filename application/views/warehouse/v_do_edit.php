<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
<?php if ($do->status === 'cancel') { ?>
                #btn-cancel{
                    display: none;
                }
<?php } ?>
        </style>
        <?php $this->load->view("admin/_partials/js.php") ?>
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
                <?php
                $this->load->view("admin/_partials/sidebar.php");
                ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header" >
                    <div id ="status_bar">
                        <?php
                        $data['jen_status'] = $do->status;
                        $this->load->view("admin/_partials/statusbar.php", $data)
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">
                        <?php
                        $this->load->view("warehouse/v_do_add", $picklist);
                        ?>
                        <div class="row">
                            <div class="col-md-12 table-responsive over"  style="margin-top: 20px;">
                                <table class="table table-condesed table-hover rlstable  over" width="100%" id="list-item">
                                    <thead>                          
                                        <tr>

                                            <th class="style" width="10px">No</th>
                                            <?php if ((int) $picklist->type_bulk_id === 1) { ?>
                                                <th class="style" width="35px">Bulk</th>
                                            <?php } ?>
                                            <th class="style">Deskripsi</th>
                                            <th class="style">Warna</th>
                                            <th class="style">Total LOT / PCS</th>
                                            <th class="style">Total QTY</th>
                                            <th class="style">Satuan</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>

                <?php
                $this->load->view("admin/_partials/footer.php");
                ?>
            </footer>
        </div>

        <div style="display: none;" id="pilihan-print">
            <div class="row">
                <div class="col-md-4">
                    <button class="btn btn-default btn-sm print-sj" type="button" data-print="sj"><i class="fa fa-print"></i> SJ</button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-default btn-sm print-sj" type="button" data-print="sje"><i class="fa fa-print"></i> SJE</button>
                </div>
                <div class="col-md-4">
                    <button class="btn btn-default btn-sm print-sj" type="button" data-print="pl"><i class="fa fa-print"></i> PL</button>
                </div>
            </div>
        </div>
        <script>
//            $(function () {
            const table = $("#list-item").DataTable({
                "iDisplayLength": 10,
                "processing": true,
                "serverSide": true,
                "order": [],
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "ajax": {
                    "url": "<?= base_url('warehouse/deliveryorder/get_list_data') ?>",
                    "type": "POST",
                    "data": function (d) {
                        d.pl = "<?= $picklist->no ?>";
                        d.id = "<?= $id ?>";
                        d.bulk = "<?= $picklist->type_bulk_id ?>";
                    }
                },
                "columnDefs": [
                    {
                        "targets": [0],
                        "orderable": false
                    }
                ],
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        "text": '<i class="fa fa-list"> <span>Detail Item</span>',
                        "className": "btn btn-default detail-data",
                        "action": function (e, dt, node, config) {
                            e.preventDefault();
                            $("#tambah_data").modal({
                                show: true,
                                backdrop: 'static'
                            });
                            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                            $('.modal-title').text('List Detail Item');
                            $.post("<?= base_url('warehouse/deliveryorder/list_detail_view') ?>", {id: "<?= $id ?>", pl: "<?= $picklist->no ?>", doid: "<?= $do->id ?>"}, function (data) {
                                setTimeout(function () {
                                    $(".tambah_data").html(data.data);
                                }, 1000);
                            });
                        }
                    },
                    {
                        "text": '<i class="fa fa-trash"> <span>Item Retur</span>',
                        "className": "btn btn-default",
                        "action": function (e) {
                            e.preventDefault();
                            $("#tambah_data").modal({
                                show: true,
                                backdrop: 'static'
                            });
                            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                            $('.modal-title').text('Retur Item');
                            $.post("<?= base_url('warehouse/deliveryorder/show_form_retur') ?>",
                                    {
                                        "do": "<?= $do->no ?>",
                                        "no_sj": "<?= $do->no_sj ?>",
                                        "doid": "<?= $do->id ?>",
                                        "status": "<?= $do->status ?>"
                                    }, function (data) {
                                setTimeout(function () {
                                    $(".tambah_data").html(data.data);
//                                    $("#form-retur-scan").hide();
                                }, 1000);
                            });
                        }
                    }, {
                        "text": '<i class="fa fa-book"> <span>Stock Move</span>',
                        "className": "btn btn-default",
                        "action": function (e) {
                            e.preventDefault();
                            $("#tambah_data").modal({
                                show: true,
                                backdrop: 'static'
                            });
                            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                            $('.modal-title').text('Stock Move');
                            $.post("<?= base_url('warehouse/deliveryorder/info_stock_move') ?>",
                                    {
                                        "nodo": "<?= $do->no ?>",
                                        "nosj": "<?= $do->no_sj ?>"
                                    }, function (data) {
                                setTimeout(function () {
                                    $(".tambah_data").html(data.data);
                                }, 1000);
                            });
                        }
                    }
                ]
            });
//            });

            $(function () {

                $("#tanggal_dokumen").datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss',
                    date: new Date(<?= strtotime($do->tanggal_dokumen) ?> * 1000)
                });

                $("#btn-simpan").on("click", function () {
                    $.post("<?= base_url('warehouse/deliveryorder/update_note/') ?>",
                            {
                                note: $("#ket").val(),
                                doid: "<?= $do->no ?>",
                                dok_date: $("#tanggal_dokumen").val()
                            }
                    , function (response) {
                        location.reload();
//                        alert_notify(response.icon, response.message, response.type, function () {});
                    }
                    ).fail(function (err) {
                        let response = err.responseJSON;
                        alert_notify(response.icon, response.message, response.type, function () {});
                    });
                });
                $("#delivery-item").on("click", function () {
                    confirmRequest("Delivery", "Delivery Kembali ?", function () {
                        please_wait(function () {});
                        $.ajax({
                            url: "<?= base_url('warehouse/deliveryorder/delivery') ?>",
                            type: "post",
                            data: {
                                nodo: "<?= $do->no ?>"
                            },
                            success: function (data) {
                                location.href = "<?= base_url('warehouse/deliveryorder/edit/') ?>" + data.data;
                            },
                            error: function (req, error) {
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                    }, 500);
                                });
                            }
                        });
                    });
                });
                $("#btn-cancel").on("click", function () {
                    confirmRequest("Batalkan Delivery", "Batalkan No Delivery Order <strong><?= $do->no ?></strong>", function () {
                        please_wait(function () {});
                        $.ajax({
                            url: "<?= base_url('warehouse/deliveryorder/batal_do') ?>",
                            type: "post",
                            data: {
                                pl: "<?= $do->no_picklist ?>",
                                nodo: "<?= $do->no ?>"
                            },
                            success: function (data) {
                                location.reload();
                            },
                            error: function (req, error) {
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                    }, 500);
                                });
                            }
                        });

                    });
                });
                $("#btn-print").on('click', function (e) {
                    e.preventDefault();
                    $("#print_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text('Pilihan Mode Print');
                    $(".print_data").html($("#pilihan-print").html());
                    $(".print-sj").on("click", function () {
                        $.post("<?= base_url('warehouse/deliveryorder/print_sj/') ?>",
                                {
                                    "print_mode": $(this).attr("data-print"),
                                    id: "<?= $id ?>"
                                }, function (response) {
                            let url = response.data;
                            var win = window.open(url, "width=1000,height=700");
//                            win.document.write(`<iframe name="contentwin" src="${url}"frameborder="0" height="100%" width="100%"></iframe>`);
                            setTimeout(function () {
                                win.document.close();
                                win.print();
                                win.close();
                            }, 500);
                        }
                        );

                    });
                });

                $("#return-item").on("click", function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text('Retur Item');
                    $.post("<?= base_url('warehouse/deliveryorder/show_form_retur') ?>",
                            {
                                "do": "<?= $do->no ?>",
                                "no_sj": "<?= $do->no_sj ?>",
                                "doid": "<?= $do->id ?>",
                                "status": "<?= $do->status ?>"
                            }, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);
                        }, 1000);
                    });
                });
            }
            );
        </script>
    </body>
</html>