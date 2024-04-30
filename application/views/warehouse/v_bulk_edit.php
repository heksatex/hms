<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            #statusbulk {
                color: whitesmoke;
                background-color: red;
                text-align: center;
                font-size: 150%;
                font-weight: 400;
            }
            .row{
                padding: 5px;
            }
            .bolded {
                font-weight:bold;
                font-size: 100%;
                letter-spacing: 3px;
            }
            #btn-simpan{
                display: none;
            }
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data);
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">BULK</strong></h3>
                            <div class="pull-right text-right" id="btn-header">
                                <button class="btn btn-primary btn-sm" id="btn-add-bulk" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Tambah Bulk / BAL</button>
                            </div>
                        </div>
                        <div class="box-body">

                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <form class="form-horizontal" method="POST" name="form-bulk-edit" id="form-bulk-edit" action="<?= base_url('warehouse/bulk/update') ?>">
                                        <button type="submit" id="btn_form_bulk_edit" style="display: none"></button>
                                        <div class="form-group">
                                            <div class="col-md-8 col-xs-12">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">No Picklist</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input type='text' class="form-control input-sm" readonly value="<?= $picklist->no ?>"  />
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">Buyer</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input type='text' class="form-control input-sm" readonly value="<?= $picklist->nama ?>"  />
                                                    </div>
                                                </div>

                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">Marketing</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input type='text' class="form-control input-sm" readonly value="<?= $picklist->sales ?>"  />
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-12">
                                    <div class="table-responsive over">
                                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="summary_bulk">
                                            <thead>
                                                <tr>
                                                    <th class="style" width="10px">No</th>
                                                    <th class="style"width="20px">BAL ID</th>
                                                    <th class="style" width="10px">PCS</th>
                                                    <th class="style" width="10px">QTY</th>
                                                    <th class="style" width="10px">#</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view("admin/_partials/js.php") ?>
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>

                <?php
//                $this->load->view("admin/_partials/footer.php");
                ?>
            </footer>
        </div>
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
        <script>
            $(function () {

                const sumTable = $("#summary_bulk").DataTable({
                    "iDisplayLength": 25,
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": false,
                    "info": true,
                    "autoWidth": true,
                    "ajax": {
                        "url": "<?= base_url('warehouse/bulk/data_bulking') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.no_pl = "<?= $picklist->no ?>";
                        },
                        "dataSrc": function (data) {
                            if (data.data.length < 1) {
                                $(".header-status").hide();
                            }
                            return data.data;
                        }
                    },
                    "columnDefs": [
                        {
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
                            $(".print_data").html($("#pilihan-print").html());
                            let bulk = $(this).attr("data-id");
                            let pl = "<?= $picklist->no ?>";
                            $(".print-bulk").on('click', function () {
                                $.post("<?= base_url('warehouse/bulk/print_bulk/') ?>",
                                        {
                                            "pl": pl,
                                            "bulk": bulk,
                                            "print_mode": $(this).attr("data-print"),
                                            "type": "bulk"
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
                $(sumTable.column(1).nodes()).addClass('bolded');

                const formsearch = document.forms.namedItem("form-bulk-edit");
                formsearch.addEventListener(
                        "submit",
                        async(event) => {
                    please_wait(function () {});
                    try {

                    } catch (e) {

                    } finally {
                        unblockUI(function () {});
                    }
                    event.preventDefault();
                },
                        false
                        );

                $("#btn-add-bulk").on('click', function (e) {
                    e.preventDefault();
                    bootbox.prompt({
                        title: "Tambah Bulk",
                        message: "Total BAL yang Akan dibuat?",
                        inputType: 'number',
                        buttons: {
                            cancel: {
                                label: '<i class="fa fa-times"></i> Tidak'
                            },
                            confirm: {
                                label: '<i class="fa fa-check"></i> YA'
                            }
                        },
                        callback: function (result) {
                            if (result > 0) {
                                addBulk(result);
                            }

                        }
                    });
//                    confirmRequest("Tambah Data", "Tambah Data Bulk / BAL ", () => {
//                        addBulk();
//                    });
                });

                const addBulk = function (totals) {
                    please_wait(function () {});
                    $.ajax({
                        "url": "<?= base_url('warehouse/bulk/save_add_bulk') ?>",
                        "type": "POST",
                        "data": {
                            pl: "<?= $picklist->no ?>",
                            total: totals
                        },
                        "success": function (data) {
                            sumTable.search("").draw(false);
                            unblockUI(function () {
                                alert_notify(data.icon, data.message, data.type, function () {});
                            }, 100);
                        },
                        "error": function (xhr, ajaxOptions, thrownError) {
                            let data = JSON.parse(xhr.responseText);
                            unblockUI(function () {
                                alert_notify(data.icon, data.message, data.type, function () {});
                            }, 100);
                        }
                    });
                };
            });

            const print_voucher = function () {

                var win = window.open();
                win.document.write($("#printed").html());
                setTimeout(function () {
                    win.document.close();
                    win.print();
                    win.close();
                }, 200);

            };
        </script>
    </body>
</html>