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

            .hide {
                display: none;
            }
            .btn-data-table{
                font-family: "inherit"
            }
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
                        <div class="box-header with-border"  style="background-color: <?= (!$do->notifikasi && $do->status !== "cancel" ) ? "yellow" : "transfarent" ?>;">
                            <h3 class="box-title">Form Edit <strong> </strong></h3>
                            <div class="pull-right text-right" id="btn-header">
                                <?php
                                if (!$do->notifikasi && $do->status !== "cancel") {
                                    ?>
                                    <button class="btn btn-success btn-sm" id="send-broadcast" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-whatsapp">&nbsp; Broadcast DO</i>
                                    </button>
                                <?php }
                                ?>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="col-md-6 col-xs-12">
                                <div class="row">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">No Picklist</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span><?= $picklist->no ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Marketing</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span><?= $picklist->sales ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Tipe Bulk</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span><?= $picklist->bulk ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Customer</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <span><?= $picklist->nama ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Jenis Jual</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <span style="text-transform: uppercase"><?= $picklist->jenis_jual ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Alamat</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <span style="text-transform: uppercase"><?= $picklist->alamat ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">No Delivery Order</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <strong><?= $do->no ?></strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">No Surat Jalan</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <strong><?= $do->no_sj ?></strong>
                                            </div>
                                        </div>
                                    </div>


                                    <?php if ($picklist->type_bulk_id === "1" && $do->status === 'draft') { ?>
                                        <div class="form-group">
                                            <form class="form-horizontal" method="POST" name="form-check-bal" id="form-check-bal" action="<?= base_url('warehouse/deliveryorder/check_bal') ?>">
                                                <div class="col-xs-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label required">Barcode BAL</label>
                                                            </div>
                                                            <div class="col-xs-8">
                                                                <input type='text' name="search" id="search" class="form-control input-lg" required autofocus/>
                                                                <input type="hidden" name="type" value="bal">
                                                                <input type="hidden" name="picklist" id="picklist" value="<?= $picklist->no ?>">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    <?php }
                                    ?>


                                </div>

                                <div class="col-md-6 col-xs-12">
                                    <div class="row">
                                        <form class="form-horizontal" method="POST" name="form-do" id="form-do" action="<?= base_url('warehouse/deliveryorder/update') ?>">
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">Tanggal dibuat</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <strong><?= date("Y-m-d H:i:s", strtotime($do->tanggal_buat)) ?></strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label required">Tanggal Dokumen</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input class="form-control" name="dok_date" value="<?= date("D, d M Y H:i:s", strtotime($do->tanggal_dokumen)) ?>"
                                                               id="tanggal_dokumen" required <?= (in_array($user->level, ["Entry Data", ""]) ? "readonly" : (($do->status === "draft") ? "" : "readonly")) ?> >
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label" >Note Picklist</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <textarea type="text" class="form-control input-sm resize-ta" rows="8" id="ket" name="note"><?= $do->note ?></textarea>
                                                        <input type="hidden" value="<?= $do->no ?>" name="doid">
                                                        <button type="submit" style="display: none;" id="btn-submit-edit"></button>
                                                    </div>                                    
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </div>


                            </div>

                        </div>
                        <div class="row">

                            <?php if ($do->status === 'draft') { ?>
                                <div class="col-md-12 table-responsive over">
                                    <?php if ((int) $picklist->type_bulk_id === 2) { ?>
                                        <p><label>Total Item : <span><?= $total_detail->total_item ?? 0 ?></span></label></p>
                                        <p><label>Total Qty : <span><?= $total_detail->total_qty ?? 0 ?></span></label></p>
                                    <?php } else { ?>
                                        <p><label>Total Item : <span id="total_item">0</span></label></p>
                                        <p><label>Total Qty : <span id="total_qty">0</span></label></p>
                                    <?php } ?>

                                    <table class="table table-condesed table-hover rlstable  over" width="100%" id="delivery-item" >
                                        <thead>                          
                                            <tr>

                                                <th class="style" width="10px">No</th>
                                                <?php if ((int) $picklist->type_bulk_id === 1) { ?>
                                                    <th class="style">BAL ID</th>
                                                <?php } ?>
                                                <th class="style">Deskripsi</th>
                                                <th class="style">Corak PO Buyer</th>
                                                <th class="style">Warna</th>
                                                <th class="style">Total LOT / PCS</th>
                                                <th class="style">Total QTY</th>
                                                <th class="style">Satuan</th>
                                                <th class="style">#</th>

                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            <?php } else { ?>
                                <div class="col-md-12 table-responsive over"  style="margin-top: 20px;">
                                    <p><label>Total Item : <span><?= $total_detail->total_item ?? 0 ?></span></label></p>
                                    <p><label>Total Qty : <span><?= $total_detail->total_qty ?? 0 ?></span></label></p>
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
                            <?php } ?>
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

        <?php
        if ($do->status === 'draft') {
            if ((int) $picklist->type_bulk_id === 1) {
                ?>
                <script>
                    $(function () {
                        const updateItemTotal = function (pl, bal) {
                            $.ajax({
                                url: "<?= base_url('warehouse/deliveryorder/get_total_item') ?>",
                                type: "POST",
                                data: {
                                    pl: pl,
                                    bal: bal
                                },
                                success: function (data, textStatus, jqXHR) {
                                    $("#total_item").html(data?.data?.jumlah_qty);
                                    $("#total_qty").html(data?.data?.total_qty);
                                },
                            });
                        }

                        const formcheckbal = document.forms.namedItem("form-check-bal");
                        formcheckbal.addEventListener(
                                "submit",
                                (event) => {
                            please_wait(function () {});
                            request("form-check-bal").then(
                                    response => {
                                        unblockUI(function () {
                                            alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                        }, 100);
                                        if (response.status === 200) {
                                            listBulk.push($("#search").val());
                                            $("#search").val("");
                                            table.search("").draw(false);
                                            updateItemTotal($("#picklist").val(), JSON.stringify(listBulk));

                                        }
                                    }
                            ).catch(err => {
                                unblockUI(function () {});
                                alert_modal_warning("Hubungi Dept IT");
                            });
                            event.preventDefault();
                        },
                                false
                                );
                    });
                </script>
                <?php
            }
            ?>
            <script>

                var listBulk = [];
                $(function () {
                    $("#btn-edit").hide();
                    //                    $("#btn-cancel").hide();
                    $("#btn-print").hide();

                    $("#btn-cancel").on("click", function () {
                        confirmRequest("Delivery", "Batalkan Draft Delivery Order ?", function () {
                            please_wait(function () {});
                            $.ajax({
                                url: "<?= base_url('warehouse/deliveryorder/batal_do_draft') ?>",
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
                    $("#btn-kirim").on("click", function () {
                        confirmRequest("Delivery", "Kirim delivery order ?", function () {
                            please_wait(function () {});
                            $.ajax({
                                url: "<?= base_url('warehouse/deliveryorder/add_item') ?>",
                                type: "POST",
                                data: {
                                    nodo: "<?= $do->no ?>",
                                    type_bulk: "<?= $picklist->type_bulk_id ?>",
                                    bulks: JSON.stringify(listBulk)
                                },
                                success: function (data, textStatus, jqXHR) {
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
                });

                const table = $("#delivery-item").DataTable({
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
                        "url": "<?= base_url('warehouse/deliveryorder/list_data_detail/' . $picklist->type_bulk_id) ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.pl = "<?= $picklist->no ?>";
                            d.bulk = JSON.stringify(listBulk);
                            d.not_in = JSON.stringify([]);
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
                            "className": "btn btn-default detail-data btn-data-table",
                            "action": function (e, dt, node, config) {
                                e.preventDefault();
                                $("#tambah_data").modal({
                                    show: true,
                                    backdrop: 'static'
                                });
                                $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                                $('.modal-title').text('List Detail Item');
                                $.post("<?= base_url('warehouse/deliveryorder/list_detail_view_add') ?>",
                                        {
                                            bulk: JSON.stringify(listBulk),
                                            pl: "<?= $picklist->no ?>",
                                            not_in: JSON.stringify([]),
                                            type: '<?= $picklist->type_bulk_id ?>'
                                        },
                                        function (data) {
                                            setTimeout(function () {
                                                $(".tambah_data").html(data.data);
                                            }, 1000);
                                        });
                            }
                        }
                    ]
                });

            </script>
        <?php } else { ?>
            <script>
                $(function () {
                    $("#btn-edit").hide();
                    //                    $("#btn-cancel").hide();
                    //                    $("#btn-print").hide();
                    $("#btn-kirim").hide();
                    var bal = [];
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
                                "className": "btn btn-default detail-data btn-data-table",
                                "action": function (e, dt, node, config) {
                                    e.preventDefault();
                                    $("#tambah_data").modal({
                                        show: true,
                                        backdrop: 'static'
                                    });
                                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                                    $('.modal-title').text('List Detail Item');
                                    $.post("<?= base_url('warehouse/deliveryorder/list_detail_view') ?>",
                                            {
                                                id: "<?= $id ?>",
                                                pl: "<?= $picklist->no ?>",
                                                doid: "<?= $do->id ?>",
                                                type: "<?= $picklist->type_bulk_id ?>"
                                            }, function (data) {
                                        setTimeout(function () {
                                            $(".tambah_data").html(data.data);
                                        }, 1000);
                                    });
                                }
                            },
                            {
                                "text": '<i class="fa fa-trash"> <span>Item Retur</span>',
                                "className": "btn btn-default btn-data-table",
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
                                "className": "btn btn-default btn-data-table",
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

                                        $("#btn-tambah").hide();

                                    });
                                }
                            }
                        ]
                    });



                    const formedit = document.forms.namedItem("form-do");
                    formedit.addEventListener(
                            "submit",
                            (event) => {
                        please_wait(function () {});
                        request("form-do").then(
                                response => {
                                    unblockUI(function () {
                                        alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                    }, 100);
                                    if (response.status === 200) {
                                        location.reload();
                                    }
                                }
                        ).catch(err => {
                            unblockUI(function () {});
                            alert_modal_warning("Hubungi Dept IT");
                        });
                        event.preventDefault();
                    },
                            false
                            );

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


                });
            </script>
        <?php } if ($do->status === 'cancel') {
            ?>
            <script>
                $(function () {
                    $("#btn-simpan").hide();
                })
            </script>
        <?php }
        ?>

        <script>

            $(function () {
                $("#tanggal_dokumen").datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss',
                    date: new Date(parseInt("<?= strtotime($do->tanggal_dokumen ?? date('Y-m-d H:i:s')) ?>") * 1000)
                });

                $("#btn-simpan").on('click', function () {
                    confirmRequest("Peringatan", "Ubah Data Delivery Order", function () {
                        $("#btn-submit-edit").trigger('click');
                    });
                });

                $("#send-broadcast").on("click", function () {
                    confirmRequest("Broadcast", "Kirim Data DO ke whatsapp ? ",
                            function () {
                                please_wait(function () {});
                                $.ajax({
                                    url: "<?= base_url('warehouse/deliveryorder/broadcast') ?>",
                                    type: "POST",
                                    data: {
                                        "do": "<?= $do->no ?>"
                                    },
                                    success: function (data) {
                                        location.reload();
                                    },
                                    complete: function (jqXHR, textStatus) {
                                        unblockUI(function () {}, 100);
                                    }
                                });

                            });
                });

            });
        </script>
    </body>
</html>