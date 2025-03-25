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
            #btn-cancel{
                display : none
            }


            .no{
                width: 0.5% !important;
            }
            .td-produk {
                width: 3% !important;
            }
            .td-note{
                width: 3% !important;
            }
            .td-coa {
                width: 3% !important;
            }
            .td-beli{
                width: 2% !important;
            }
            .td-uom {
                width: 1% !important;
            }
            .td-harga{
                width: 2% !important;
            }
            .td-tax{
                width: 2% !important;
            }
            .td-aksi{
                width: 1% !important;
            }
            .td-diskon {
                width: 2% !important;
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
                            <h3 class="box-title"><strong> <?= $inv->no_inv_retur ?? "" ?> </strong></h3>
                            <div class="pull-right text-right" id="btn-header">

                            </div>
                        </div>
                        <form  class="form-horizontal" method="POST" name="form-invr" id="form-invr" action="<?= base_url('purchase/debitnote/update/' . $id) ?>">
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
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">Kurs</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="text" class="form-control pull-right input-sm" name="nilai_matauang" value="<?= number_format($inv->nilai_matauang, 0, '', '') ?>" <?= ($inv->status === 'draft') ? '' : "readonly" ?> required>
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
                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Invoice Supplier</label>
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
                                                <button type="submit" id="form-invr-submit" style="display: none"></button>
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
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tgl SJ</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="datetime-local" class="form-control pull-right input-sm" name="tanggal_sj" value="<?= $inv->tanggal_sj ?>" <?= ($inv->status === 'draft') ? '' : "readonly" ?>> 
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Origin</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="text" class="form-control pull-right input-sm" name="origin" value="<?= $inv->origin ?>" readonly> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="col-xs-12">
                                    <div class="table-responsive over">
                                        <table id="tbl-inv" class="table table-condesed table-hover rlstable  over" style="min-width: 150%">
                                            <thead>
                                                <tr>
                                                    <th class="no">#</th>
                                                    <th>Produk</th>
                                                    <!--<th>Deskripsi</th>-->
                                                    <th>Reff Note</th>
                                                    <th>Account</th>
                                                    <th>Qty Retur</th>
                                                    <th>UOM</th>
                                                    <th>Harga Satuan</th>
                                                    <th>Tax</th>
                                                    <th>Diskon</th>
                                                    <!--<th>#</th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (count($invDetail) > 0) {
                                                    $dataPajak = [];
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
                                                        if ($setting !== null) {
                                                            $totalTax += ((($jumlah - $value->diskon) * 11) / 12) * $value->amount_tax;
                                                        } else {
                                                            $totalTax += ($jumlah - $value->diskon) * $value->amount_tax;
                                                        }
                                                        ?>
                                                        <tr>
                                                            <td class="no"><?= $key + 1 ?></td>
                                                            <td class="td-produk"><?= $value->kode_produk . " - " . $value->nama_produk ?></td>
                                                            <!--<td class="td-deskripsi" ><?= $value->deskripsi ?></td>-->
                                                            <td class="td-note"><?= $value->reff_note ?></td>
                                                            <td class="td-coa">
                                                                <div class="form-group">
                                                                    <select class="form-control kode_coa input-xs kode_coa_data_<?= $key ?>" style="width: 100% !important;" data-row="<?= $key ?>"
                                                                            name="kode_coa[<?= $value->id ?>]" <?= ($inv->status === 'draft') ? '' : 'disabled' ?>>
                                                                        <option></option>
                                                                        <?php
                                                                        if (!is_null($value->kode_coa)) {
                                                                            ?>
                                                                            <option value="<?= $value->kode_coa ?>" selected ><?= $value->kode_coa . " - " . $value->nama_coa ?></option>   
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>

                                                                </div>
                                                            </td>
                                                            <td class="td-beli" >
                                                                <input type="hidden"  class="form-control input-sm qty_<?= $value->id ?>" name="qty_beli[<?= $value->id ?>]" value="<?= $value->qty_beli ?>" readonly>
                                                                <input type="text"  class="form-control input-sm" value="<?= number_format($value->qty_beli, 2) ?>" disabled>
                                                            </td>
                                                            <td class="td-uom"><?= $value->uom_beli ?></td>
                                                            <td class="td-harga">
                                                                <div class="form-group">
                                                                    <input class="form-control input-sm" name="harga_satuan[<?= $value->id ?>]" type="hidden"
                                                                           value="<?= $value->harga_satuan > 0 ? (float) $value->harga_satuan : 0 ?>">

                                                                    <input class="form-control input-sm"  type="text"
                                                                           value="<?= $value->harga_satuan > 0 ? number_format((float) $value->harga_satuan, 4) : 0 ?>" disabled>

                                                                </div>
                                                            </td>
                                                            <td class="td-tax">
                                                                <div class="form-group ">
                                                                    <input type="hidden" class="amount_tax_<?= $key ?>" name="amount_tax[<?= $value->id ?>]" value="<?= $value->amount_tax ?>">
                                                                    <input type="hidden" class="form-control" name="tax[<?= $value->id ?>]" value="<?= $value->tax_id ?>">
                                                                    <select style="width: 90%" class="form-control tax tax<?= $key ?> input-xs"  data-row="<?= $key ?>" 
                                                                            name="tax_[<?= $value->id ?>]"  disabled>
                                                                        <option></option>
                                                                        <?php
                                                                        foreach ($taxss as $key => $taxs) {
                                                                            ?>
                                                                            <option value='<?= $taxs->id ?>' data-nilai_tax="<?= $taxs->amount ?>" <?= ($taxs->id === $value->tax_id) ? 'selected' : '' ?>><?= $taxs->nama ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td class="td-diskon">
                                                                <input type="hidden" class="form-control" name="diskon[<?= $value->id ?>]" value="<?= $value->diskon ?>">
                                                                <input type="text" class="form-control" value="<?= $value->diskon ?>" disabled>
                                                            </td>
        <!--                                                                    <td class="td-aksi">
                                                                <a class="add_duplicate" data-id="<?= $value->id ?>" data-toggle="tooltip" data-placement="top" title="Split"><i class="fa fa-copy"></i></a>
                                                            </td>-->
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
                                                            <?php if ($totalTax > 0) { ?>
                                                                <tr>
                                                                    <td>1</td>
                                                                    <td><?= $dataPajak["ket"] ?? "" ?></td>
                                                                    <td>1193.05 - Pajak Dibayar Muka PPN</td>
                                                                    <td>IDR <?php
                                                                        if ($setting !== null) {
                                                                            print( number_format(((($subtotal1 - $totalDiskon) * 11) / 12) * $inv->nilai_matauang, 4));
                                                                        } else {
                                                                            print(number_format(($subtotal2 * $inv->nilai_matauang), 4));
                                                                        }
                                                                        ?>
                                                                    </td>
                                                                    <td>IDR <?= number_format(($totalTax * $inv->nilai_matauang), 4) ?></td>
                                                                </tr>
                                                            <?php } ?>

                                                        <?php }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="col-xs-12 col-md-4">
                                                <table class="table table-condesed table-hover rlstable  over">
                                                    <tr>
                                                        <td class="text-right"><strong>Subtotal 1</strong></td>
                                                        <td>IDR <?= number_format(($subtotal1 * $inv->nilai_matauang), 4) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right">Diskon</td>
                                                        <td>IDR <?= number_format(($totalDiskon * $inv->nilai_matauang), 4) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right"><strong>Subtotal 2</strong></td>
                                                        <td>IDR<?= number_format(($subtotal2 * $inv->nilai_matauang), 4) ?></td>
                                                    </tr>
                                                    <?php if ($setting !== null) {
                                                        ?>
                                                        <tr>    
                                                            <td class="style text-right">DPP Nilai Lain</td>
                                                            <td class="style totalan"> 
                                                                <input name="dpplain" type="hidden" value="1">
                                                                <strong>IDR<?= number_format(((($subtotal1 - $totalDiskon) * 11) / 12) * $inv->nilai_matauang, 4) ?>
                                                                </strong>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                    <tr>
                                                        <td class="text-right">Tax</td>
                                                        <td><?= $inv->symbol ?> <?= number_format(($totalTax * $inv->nilai_matauang), 4) ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="text-right"><strong>Total</strong></td>
                                                        <td><?= $inv->symbol ?> <?= number_format(($subtotal2 + $totalTax) * $inv->nilai_matauang, 4) ?></td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    </form>
                                </div>
                                </section>
                            </div>
                    </div>
                    <footer class="main-footer">
                        <?php
                        $this->load->view("admin/_partials/footer.php");
                        ?>
                    </footer>
                    <?php $this->load->view("admin/_partials/js.php") ?>
                    <script>
                        $(document).ready(function () {
                            $(window).keydown(function (event) {
                                if (event.keyCode === 13) {
                                    event.preventDefault();
                                    return false;
                                }
                            });

                            $(".tax").select2({
                                allowClear: true,
                                placeholder: "Pajak",

                            });
                            $('.kode_coa').select2({
                                allowClear: true,
                                placeholder: "PIlih Coa",
                                ajax: {
                                    dataType: 'JSON',
                                    type: "POST",
                                    url: "<?= base_url("purchase/jurnalentries/getcoa"); ?>",
                                    delay: 250,
                                    data: function (params) {
                                        return{
                                            search: params.term
                                        };
                                    },
                                    processResults: function (data) {
                                        var results = [];
                                        $.each(data.data, function (index, item) {
                                            results.push({
                                                id: item.kode_coa,
                                                text: item.kode_coa + " - " + item.nama
                                            });
                                        });
                                        return {
                                            results: results
                                        };
                                    },
                                    error: function (xhr, ajaxOptions, thrownError) {
                                    }
                                }
                            });
                            const form = document.forms.namedItem("form-invr");
                            form.addEventListener(
                                    "submit",
                                    (event) => {
                                please_wait(function () {});
                                request("form-invr").then(
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
                                    $("#form-invr-submit").trigger("click");
                                });
                            });

                            $("#btn-approve").off("click").unbind("click").on("click", function () {
                                confirmRequest("Purchase Order", "Approve Debit Note Retur ? ", function () {
                                    please_wait(function () {});
                                    $.ajax({
                                        url: "<?= base_url('purchase/debitnote/update_status/') ?>",
                                        type: "POST",
                                        data: {
                                            id: "<?= $id ?>",
                                            status: "done",
                                            jurnal: "<?= $inv->journal ?>",
                                            inv: "<?= $inv->no_inv_retur ?>",
                                            origin: "<?= $inv->origin ?>"
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
                                confirmRequest("Purchase Order", "Cancel Debit Note Retur ? ", function () {
                                    please_wait(function () {});
                                    $.ajax({
                                        url: "<?= base_url('purchase/debitnote/update_status/') ?>",
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
                                    url: "<?= base_url('purchase/debitnote/print/' . $id) ?>",
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