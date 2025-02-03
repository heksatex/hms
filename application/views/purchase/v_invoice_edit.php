<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link href="<?= base_url('dist/css/popup_img.css') ?>" rel="stylesheet">
        <style>
            #btn-print{
                display : none
            }
            <?php
            if ($inv->status === "cancel") {
                ?>
                #btn-simpan,#btn-cancel,#btn-approve{
                    display : none
                }

                <?php
            }
            if ($inv->status === "done") {
                ?>
                #btn-simpan,#btn-approve{
                    display : none
                }
                #btn-print{
                    display : inline
                }
                <?php
            }
            ?>
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
                <?php
                $this->load->view("admin/_partials/sidebar.php");
                ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header" >
                    <div id ="status_bar">
                        <?php
                        $data['jen_status'] = $inv->status;
                        $this->load->view("admin/_partials/statusbar.php", $data);
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">PO Supplier &nbsp;<strong> <?= $inv->no_invoice ?? "" ?> </strong></h3>
                            <div class="pull-right text-right" id="btn-header">

                            </div>
                        </div>
                        <form  class="form-horizontal" method="POST" name="form-inv" id="form-inv" action="<?= base_url('purchase/invoice/update/' . $id) ?>">
                            <div class="box-body">
                                <div class="col-xs-12">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Supplier</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $inv->supplier ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">No PO</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $inv->no_po ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Order Date</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= date("l, d M Y H:i:s", strtotime($inv->order_date)) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Mata Uang</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $inv->mata_uang ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">PO Supplier</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="text" class="form-control pull-right input-sm" name="no_invoice_supp" value="<?= $inv->no_invoice_supp ?>" <?= ($inv->status === 'draft') ? '' : "readonly" ?>> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tgl Invoice Supplier</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="datetime-local" class="form-control pull-right input-sm" name="tanggal_invoice_supp" value="<?= $inv->tanggal_invoice_supp ?>" <?= ($inv->status === 'draft') ? '' : "readonly" ?>> 
                                                </div>
                                                <button type="submit" id="form-inv-submit" style="display: none"></button>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">No SJ Supplier</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="text" class="form-control pull-right input-sm" name="no_sj_supp" value="<?= $inv->no_sj_supp ?>" <?= ($inv->status === 'draft') ? '' : "readonly" ?>> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="col-xs-12">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab_1" data-toggle="tab">Item</a></li>
                                        <!--<li><a href="#tab_2" data-toggle="tab">RFQ & BID</a></li>-->
                                    </ul>
                                    <div class="tab-content"><br>
                                        <div class="tab-pane active" id="tab_1">
                                            <div class="table-responsive over">
                                                <table id="tbl-inv" class="table table-condesed table-hover rlstable  over">
                                                    <thead>
                                                        <tr>
                                                            <th class="no">#</th>
                                                            <th>Produk</th>
                                                            <th>Deskripsi</th>
                                                            <th>Reff Note</th>
                                                            <th>Account</th>
                                                            <th>Qty Beli</th>
                                                            <th>UOM</th>
                                                            <th>Harga Satuan</th>
                                                            <th>Tax</th>
                                                            <th>Jumlah</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        if (count($invDetail) > 0) {
                                                            $dataPajak = [];
                                                            $jumlah = 0;
                                                            $subtotal1 = 0;
                                                            $totalDiskon = 0;
                                                            $totalTax = 0;
                                                            foreach ($invDetail as $key => $value) {
                                                                if (count($dataPajak) < 1) {
                                                                    $dataPajak["ket"] = $value->pajak_ket;
                                                                }
                                                                $jumlah = $value->harga_satuan * $value->qty_beli;
                                                                $subtotal1 += $jumlah;
                                                                $totalDiskon += $value->diskon;
                                                                $tax = ($jumlah - $value->diskon) * $value->amount;
                                                                $totalTax += $tax;
                                                                ?>
                                                                <tr>
                                                                    <td><?= $key + 1 ?></td>
                                                                    <td><?= $value->kode_produk . " - " . $value->nama_produk ?></td>
                                                                    <td><?= $value->deskripsi ?></td>
                                                                    <td><?= $value->reff_note ?></td>
                                                                    <td><?= $value->kode_coa . " " . $value->nama_coa ?></td>
                                                                    <td><?= number_format($value->qty_beli, 2) ?></td>
                                                                    <td><?= $value->uom_beli ?></td>
                                                                    <td>
                                                                        <div class="form-group">
                                                                            <input class="form-control pull-right input-sm" name="harga_satuan[<?= $value->id ?>]" <?= ($inv->status === 'draft') ? '' : 'disabled' ?>
                                                                                   style="width: 70%" value="<?= $value->harga_satuan > 0 ? (float) $value->harga_satuan : 0 ?>" required>

                                                                        </div>
                                                                    </td>
                                                                    <td><?= $value->pajak_ket ?></td>
                                                                    <td><?= $inv->symbol ?> <?= number_format($jumlah, 2) ?></td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            $subtotal2 = $subtotal1 - $totalDiskon;
                                                            ?>

                                                        </tbody>
                                                    </table>
                                                    <div class="table-responsive over">
                                                        <div class="col-xs-12 col-md-8">
                                                            <table class="table table-condesed table-hover rlstable over">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="font-weight: 600;">
                                                                            Tax
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>
                                                                            No
                                                                        </td>
                                                                        <td>
                                                                            Keterangan
                                                                        </td>
                                                                        <td>
                                                                            Tax Account
                                                                        </td>
                                                                        <td>
                                                                            Base
                                                                        </td>
                                                                        <td>
                                                                            Jumlah
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td>1</td>
                                                                        <td><?= $dataPajak["ket"] ?? "" ?></td>
                                                                        <td></td>
                                                                        <td><?= $inv->symbol ?> <?= number_format($subtotal2, 2) ?></td>
                                                                        <td><?= $inv->symbol ?> <?= number_format($totalTax, 2) ?></td>
                                                                    </tr>
                                                                <?php }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="col-xs-12 col-md-4">
                                                        <table class="table table-condesed table-hover rlstable  over">
                                                            <tr>
                                                                <td class="text-right"><strong>Subtotal 1</strong></td>
                                                                <td><?= $inv->symbol ?> <?= number_format($subtotal1, 2) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-right">Diskon</td>
                                                                <td><?= $inv->symbol ?> <?= number_format($totalDiskon, 2) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-right"><strong>Subtotal 2</strong></td>
                                                                <td><?= $inv->symbol ?> <?= number_format($subtotal2, 2) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-right">Tax</td>
                                                                <td><?= $inv->symbol ?> <?= number_format($totalTax, 2) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-right"><strong>Total</strong></td>
                                                                <td><?= $inv->symbol ?> <?= number_format($subtotal2 + $totalTax, 2) ?></td>
                                                            </tr>
                                                        </table>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <div id="foot">
                    <?php
                    $data['kode'] = decrypt_url($id);
                    $data['mms'] = $mms->kode;
                    $this->load->view("admin/_partials/footer.php", $data);
                    ?>
                </div>
            </footer>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(document).ready(function () {
                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
                });


            });

            $(function () {

                const form = document.forms.namedItem("form-inv");
                form.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-inv").then(
                            response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200)
                                    location.reload();

                            }
                    ).catch(err => {
                        unblockUI(function () {});
                        alert_modal_warning("Hubungi Dept IT");
                    });
                    event.preventDefault();
                },
                        false
                        );

                $("#btn-simpan").off("click").unbind("click").on("click", function () {
                    confirmRequest("Purchase Order", "Update Purchase Invoice ? ", function () {
                        $("#form-inv-submit").trigger("click");
                    });
                });

                $("#btn-approve").off("click").unbind("click").on("click", function () {
                    confirmRequest("Purchase Order", "Approve Purchase Invoice ? ", function () {
                        please_wait(function () {});
                        $.ajax({
                            url: "<?= base_url('purchase/invoice/update_status/') ?>",
                            type: "POST",
                            data: {
                                id: "<?= $id ?>",
                                status: "done",
                                jurnal: "<?= $inv->journal ?>",
                                inv: "<?= $inv->no_invoice ?>"
                            },
                            error: function (req, error) {
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                    }, 500);
                                });
                            }, success: function (data) {
                                location.reload();
                            }
                        });
                    });
                });

                $("#btn-cancel").off("click").unbind("click").on("click", function () {
                    confirmRequest("Purchase Order", "Cancel Purchase Invoice ? ", function () {
                        please_wait(function () {});
                        $.ajax({
                            url: "<?= base_url('purchase/invoice/update_status/') ?>",
                            type: "POST",
                            data: {
                                id: "<?= $id ?>",
                                status: "cancel"
                            },
                            error: function (req, error) {
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                    }, 500);
                                });
                            }, success: function (data) {
                                location.reload();
                            }
                        });
                    });
                });

                $("#btn-print").off("click").unbind("click").on("click", function () {
                    $.ajax({
                        url: "<?= base_url('purchase/invoice/print/' . $id) ?>",
                        type: "POST",
                        beforeSend: function (xhr) {
                            please_wait(function () {});
                        },
                        success: function (data) {
                            unblockUI(function () {});
                            window.open(data.url, "_blank").focus();

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
        </script>
    </body>
</html>