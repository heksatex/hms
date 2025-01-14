<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .cancelPL{
                color: red;
            }
            .donePL{
                color: green;
            }
            .confirm-as{
                background-color: green !important;
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
                            <div class="col-xs-12 table-responsive">
                                <table id="tbl-cfb" class="table">
                                    <thead>
                                        <tr>
                                            <th class="no">#</th>
                                            <th>Kode CFB</th>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>QTY</th>
                                            <th>SO</th>
                                            <th>Priority</th>
                                            <th>Departement Tujuan</th>
                                            <th>Create Date</th>
                                            <th>Status</th>
                                            <th>Note</th>
                                        </tr>
                                    </thead>
                                    <tfoot id="tfooter">
                                        <tr style="display: none;">
                                            <td colspan="8">
                                                <a class="add-rfq" data-request=""><i class="fa fa-plus"></i></a>
                                                <a class="add-fpt" data-request=""><i class="fa fa-plus"></i></a>
                                                <a class="confirm-order"></a>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </section>    
            </div>
            <?php $this->load->view("admin/_partials/modal.php") ?>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {
                const table = $('#tbl-cfb').DataTable({
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
                        "url": "<?php echo site_url('purchase/callforbids/list_data') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 10],
                            "orderable": false
                        },
                        {
                            'targets': 0,
                            'checkboxes': {
                                'selectRow': true
                            }
                        }
                    ],
                    "createdRow": function (row, data, dataIndex) {
                        if (data[6].toLowerCase() === "urgent") {
                            $(row).addClass('cancelPL');
                        }
                    },
                    dom: 'Bfrtip',
                    "buttons": [
                {
                            "text": 'Confirm Order',
                            "className": "btn btn-success confirm-as",
                            "action": function (e, dt, node, config) {
                                $(".confirm-order").trigger("click");
                            }
                        },
                        {
                            "text": 'Add RFQ',
                            "className": "btn btn-success add-rfq-btn",
                            "action": function (e, dt, node, config) {
                                document.getElementsByClassName("add-rfq")[0].setAttribute("data-request", "rfq");
                                $(".add-rfq").trigger("click");
                            }
                        }
                        , {
                            "text": 'Add FPT',
                            "className": "btn btn-success add-fpt-btn",
                            "action": function (e, dt, node, config) {
                                document.getElementsByClassName("add-fpt")[0].setAttribute("data-request", "fpt");
                                $(".add-fpt").trigger("click");
                            }
                        }
                        
                    ]
                });
                $(".confirm-order").on("click", function (e) {
                    e.preventDefault();
                    var rows_selected = table.column(0).checkboxes.selected();
                    if (rows_selected.length < 1) {
                        alert_notify("fa fa-warning", "Pilihan Item masih kosong", "danger", function () {});
                        return;
                    }
                    confirmRequest("Call For Bid", "Konfirmasi Permintaan Pesanan ? ", function () {
//                        please_wait(function () {});

                        const dataStatus = new Promise((resolve, reject) => {
                            let dt = [];
                            $.each(rows_selected, function (index, rowId) {
                                var splt = rowId.split("#");
                                if ("draft" !== splt[splt.length - 1]) {
                                    throw new Error("Kode Produk <strong>" + splt[2] + "</strong> Tidak Dalam Status Draft");
                                }
                                dt.push(splt[0]);
                            });
                            resolve(dt);
                        });

                        dataStatus.then((rsp) => {
//                            table.ajax.reload();
                            $.ajax({
                                url: "<?php echo site_url('purchase/callforbids/update_status') ?>",
                                type: "POST",
                                data: {
                                    ids: rsp,
                                    status: "confirm"
                                },
                                success: function (data) {
                                    alert_notify(data.icon, data.message, data.type, function () {});
                                    table.ajax.reload();
                                },
                                error: function (err) {
                                    alert_notify("fa fa-warning", err.responseJSON.message, "danger", function () {});
                                }
                            });
                        }).catch(e => {
                            alert_notify("fa fa-warning", e.message, "danger", function () {});
                        });

                    });
                });

                $(".add-rfq").on("click", function (e) {
                    var rows_selected = table.column(0).checkboxes.selected();
                    if (rows_selected.length < 1) {
                        alert_notify("fa fa-warning", "Pilihan Item masih kosong", "danger", function () {});
                        return;
                    }
                    $(".add-rfq-btn").button("loading");
                    const data = new Promise((resolve, reject) => {
                        let dt = [];
                        $.each(rows_selected, function (index, rowId) {
                            var splt = rowId.split("#");
                            if ("confirm" !== splt[splt.length - 1]) {
                                throw new Error("Kode Produk <strong>" + splt[2] + "</strong> Tidak Dalam Status confirm");
                            }
                            dt.push(rowId);
                        });
                        resolve(dt);
                    });
                    e.preventDefault();
                    data.then((rsp) => {
                        $("#tambah_data").modal({
                            show: true,
                            backdrop: 'static'
                        });
                        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                        $('.modal-title').text('Create RFQ');
                        $.post("<?= base_url('purchase/callforbids/create_rfq/') ?>", {data: JSON.stringify(rsp), jenis: "RFQ"}, function (data) {
                            setTimeout(function () {
                                $(".tambah_data").html(data.data);
                                $("#btn-tambah").html("Tambahkan");
                            }, 500);
                        });
                    }).catch(e => {
                        alert_notify("fa fa-warning", e.message, "danger", function () {});
                    });
                    $(".add-rfq-btn").button("reset");
                });

                $(".add-fpt").on("click", function (e) {
                    var rows_selected = table.column(0).checkboxes.selected();
                    if (rows_selected.length < 1) {
                        alert_notify("fa fa-warning", "Pilihan Item masih kosong", "danger", function () {});
                        return;
                    }
                    $(".add-fpt-btn").button("loading");
                    const data = new Promise((resolve, reject) => {
                        let dt = [];
                        $.each(rows_selected, function (index, rowId) {
                            var splt = rowId.split("#");
                            if ("confirm" !== splt[splt.length - 1]) {
                                throw new Error("Kode Produk <strong>" + splt[2] + "</strong> Tidak Dalam Status confirm");
                            }
                            dt.push(rowId);
                        });
                        resolve(dt);
                    });
                    e.preventDefault();
                    data.then((rsp) => {
                        $("#tambah_data").modal({
                            show: true,
                            backdrop: 'static'
                        });
                        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                        $('.modal-title').text('Create FPT');
                        $.post("<?= base_url('purchase/callforbids/create_rfq/') ?>", {data: JSON.stringify(rsp), jenis: "FPT"}, function (data) {
                            setTimeout(function () {
                                $(".tambah_data").html(data.data);
                                $("#btn-tambah").html("Tambahkan");
                                $("#jenis").val("FPT");
                            }, 500);
                        });
                    }).catch(e => {
                        alert_notify("fa fa-warning", e.message, "danger", function () {});
                    });
                    $(".add-fpt-btn").button("reset");
                });

            });

        </script>
    </body>
</html>