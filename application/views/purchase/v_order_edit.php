<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link href="<?= base_url('dist/css/light-box.css') ?>" rel="stylesheet">
        <style>
            .totalan {
                font-size: 14px;
            }
            <?php
            switch ($po->status) {
                case "draft":
                    ?>
                    #btn-approve {
                        display: none;
                    }
                    <?php
                    break;
                case "rfq":
                    ?>
                    #btn-simpan {
                        display: none;
                    }
                    <?php
                    break;
                case "waiting_approval":
                    ?>
                    #btn-simpan {
                        display: none;
                    }
                    <?php
                    break;
                case "purchase_confirmed":
                    ?>
                    #btn-simpan {
                        display: none;
                    }
                    #btn-cancel {
                        display: none;
                    }
                    <?php
                    break;
                case "done":
                    ?>
                    #btn-simpan {
                        display: none;
                    }
                    #btn-cancel {
                        display: none;
                    }
                    <?php
                    break;
                case "cancel":
                    ?>

                    #btn-simpan {
                        display: none;
                    }
                    #btn-cancel {
                        display: none;
                    }
                    <?php
                    break;
                default:
                    break;
            }
            ?>
            #btn-approve {
                display: none;
            }

            .tbl-catatan {
                font-size: 11px
            }

            .image-container {
                /* Center the image */
                align-items: center;
                display: flex;
                justify-content: center;

                /* Misc */
                border: 1px solid #cbd5e0;
                overflow: hidden;
                width: 100%;
            }
            .range-wrapper {
                /* Center the content */
                align-items: center;
                display: flex;
                justify-content: center;

                /* Misc */
                padding-top: 2rem;
            }
            .range-container {
                /* Content is centered horizontally */
                align-items: center;
                display: flex;

                /* Size */
                height: 1.5rem;
                width: 16rem;

                /* Misc */
                margin: 0 .25rem;
            }
            .left {
                /* Width based on the current value */
                height: 2px;

                /* Colors */
                background-color: rgba(0, 0, 0, .3);
            }
            .knob {
                /* Size */
                height: 1.5rem;
                width: 1.5rem;

                /* Rounded border */
                border-radius: 9999px;

                /* Colors */
                background-color: rgba(0, 0, 0, .3);
                cursor: pointer;
            }
            .right {
                /* Take the remaining width */
                flex: 1;
                height: 2px;

                /* Colors */
                background-color: rgba(0, 0, 0, .3);
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
                        $data['jen_status'] = $po->status;
                        $this->load->view("admin/_partials/statusbar.php", $data);
                        $statuss = [
                            'draft' => [
                                'value' => "confirm_order",
                                'text' => "Confirm Order"
                            ],
                            'rfq' => [
                                'value' => "confirm_rfq",
                                'text' => "Confirm Order RFQ"
                            ],
                            'waiting_approval' => [
                                'value' => "approval",
                                'text' => "Confirm Purchase"
                            ],
//                            "purchase_confirmed" => [
//                                'value' => "done",
//                                'text' => "Done"
//                            ]
                        ];
                        $totals = 0.00;
                        $diskons = 0.00;
                        $taxes = 0.00;
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">No PO &nbsp;<strong> <?= $po->no_po ?> </strong></h3>
                            <div class="pull-right text-right" id="btn-header">
                                <?php if (!in_array($po->status, ["cancel", "done", "purchase_confirmed"])) { ?>
                                    <?php
                                    if (($po->status === 'waiting_approval')) {
                                        if (in_array($user->level, ["Super Administrator", "Administrator"])) {
                                            ?>
                                            <button class="btn btn-success btn-sm" id="btn-update-status" data-status="<?= $statuss[$po->status]["value"] ?? "-" ?>"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                                <i class="fa fa-check">&nbsp; <?= $statuss[$po->status]["text"] ?? "-" ?></i>
                                            </button>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <button class="btn btn-success btn-sm" id="btn-update-status" data-status="<?= $statuss[$po->status]["value"] ?? "-" ?>"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                            <i class="fa fa-check">&nbsp; <?= $statuss[$po->status]["text"] ?? "-" ?></i>
                                        </button>
                                        <?php
                                    }
                                }
                                ?>
                            </div>

                        </div>
                        <form  class="form-horizontal" method="POST" name="form-cfq" id="form-cfq" action="<?= base_url('purchase/requestforquotation/update/' . $id) ?>">
                            <div class="box-body">
                                <div class="col-md-6 col-xs-12">
                                    <div class="row">
                                        <!--                                        <div class="form-group">
                                                                                    <div class="col-md-12 col-xs-12">
                                                                                        <div class="col-xs-4">
                                                                                            <label class="form-label">Prioritas</label>
                                                                                        </div>
                                                                                        <div class="col-xs-8 col-md-8 text-uppercase">
                                                                                            <span><?= $po->prioritas ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>-->
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Supplier</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $po->supp ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">Mata Uang</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <div class="input-group">
                                                        <!--<div class="input-group-addon"><i class="fa fa-dollar"></i></div>-->
                                                        <select class="form-control currency"  name="currency" id="currency"  required <?= ($po->status === 'draft') ? '' : 'disabled' ?> >
                                                            <option></option>
                                                            <?php
                                                            foreach ($kurs as $key => $kur) {
                                                                ?>
                                                                <option value="<?= $kur->id ?>" data-kurs="<?= $kur->kurs ?>" <?= ($kur->id === $po->currency) ? 'selected' : '' ?>><?= $kur->currency ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                        <input type="hidden" id="nilai_currency" name="nilai_currency" value="<?= $po->nilai_currency ?>" required>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">Value </label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <div class="input-group">
                                                        <!--<div class="input-group-addon"><i class="fa fa-dollar"></i></div>-->
                                                        <select class="form-control no_value"  name="no_values" id="no_values"  disabled >
                                                            <option value="0">Value</option>
                                                            <option value="1" <?= ($po->no_value === "1") ? 'selected' : '' ?>>No Value</option>
                                                        </select>
                                                        <input type="hidden" class="form-control"  name="no_value" id="no_value" value="<?= $po->no_value ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tanggal order</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= ( $po->order_date === null ) ? "" : date("l, d M Y H:i:s", strtotime($po->order_date)) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tanggal Dokumen</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= date("l, d M Y H:i:s", strtotime($po->create_date)) ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Note</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <textarea class="form-control" id="note" name="note"><?= $po->note ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="row">
                                    <div class="col-md-12 table-responsive over">
                                        <ul class="nav nav-tabs " >
                                            <li class="active"><a href="#tab_1" data-toggle="tab">Produk</a></li>
                                            <!--<li><a href="#tab_2" data-toggle="tab">RFQ & BID</a></li>-->
                                        </ul>

                                        <button type="submit" id="form-cfq-submit" style="display: none"></button>
                                        <table class="table table-condesed table-hover rlstable  over" width="100%">
                                            <thead>
                                            <th class="style" width="10px">No</th>
                                            <th class="style" width="20px">Kode CFB</th>
                                            <th class="style" width="20px">Kode Produk</th>
                                            <th class="style" width="20px">Nama Produk</th>
                                            <th class="style" width="20px">Deskripsi</th>
                                            <th class="style" width="20px">Qty / Uom</th>
                                            <th class="style" width="20px">Qty / Uom Beli</th>
                                            <td class="style text-right" width="20px">Harga Satuan Beli</td>
                                            <td class="style text-right" width="20px">Tax</td>
                                            <td class="style text-right" width="20px">Diskon</td>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $no = 0;
                                                foreach ($po_items as $key => $value) {
                                                    $no += 1;
                                                    $total = ($value->qty_beli * ($value->harga_per_uom_beli * $po->nilai_currency));
                                                    $totals += $total;
                                                    $diskon = (($value->diskon ?? 0) * $po->nilai_currency);
                                                    $diskons += $diskon;
                                                    $taxes += ($total - $diskon) * $value->amount_tax;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <?= $no ?>
                                                        </td>
                                                        <td>
                                                            <?= ($value->kode_cfb === "") ? "" : ($value->kode_pp . " - " . $value->kode_cfb) ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $image = "/upload/product/" . $value->kode_produk . ".jpg";
                                                            $imageThumb = "/upload/product/thumb-" . $value->kode_produk . ".jpg";
                                                            if (is_file(FCPATH . $image)) {
                                                                ?>
                                                                <a class="zoom" data-image="<?= $image ?>">
                                                                    <img src="<?= is_file(FCPATH . $imageThumb) ? base_url($imageThumb) : base_url($image) ?>" height="30">
                                                                </a>
                                                            <?php } ?>
                                                            <?= $value->kode_produk ?>
                                                        </td>
                                                        <td>
                                                            <?= $value->nama_produk ?>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input class="form-control pull-right input-sm" name="deskripsi[<?= $value->id ?>]" <?= ($po->status === 'draft') ? '' : 'disabled' ?>
                                                                       value="<?= $value->deskripsi?>">

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?= $value->qty . " " . $value->uom ?>
                                                        </td>
                                                        <td style="width: 15%">
                                                            <div class="form-group">
                                                                <div class="input-group">
                                                                    <div class="input-group-addon"><?= $value->qty_beli ?></div>
                                                                    <input type="hidden" name="uom_jual[<?= $value->id ?>]" value="<?= $value->uom ?>">
                                                                    <select class="form-control uom_beli input-xs uom_beli_data_<?= $key ?>" style="width: 70%" data-row="<?= $key ?>"
                                                                            name="id_konversiuom[<?= $value->id ?>]"  required <?= ($po->status === 'draft') ? '' : 'disabled' ?>>
                                                                        <option></option>
                                                                        <?php
                                                                        if (!is_null($value->id_konversiuom)) {
                                                                            ?>
                                                                            <option value="<?= $value->id_konversiuom ?>" data-catatan="<?= $value->catatan_nk ?>" selected><?= $value->dari ?></option>   
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <input type="hidden" class="" name="uom_beli[<?= $value->id ?>]" value="<?= $value->dari ?>">
                                                                </div>

                                                                <small class="form-text text-muted note_uom_beli_<?= $key ?>">
                                                                    <?= $value->catatan_nk ?? "" ?>
                                                                </small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input class="form-control pull-right input-sm" name="harga[<?= $value->id ?>]" <?= ($po->status === 'draft') ? '' : 'disabled' ?>
                                                                       style="width: 70%" value="<?= $value->harga_per_uom_beli > 0 ? (float) $value->harga_per_uom_beli : 0 ?>" required>

                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group text-right">
                                                                <select style="width: 70%" class="form-control tax input-xs"  name="tax[<?= $value->id ?>]"  <?= ($po->status === 'draft') ? '' : 'disabled' ?>>
                                                                    <option></option>
                                                                    <?php
                                                                    foreach ($tax as $key => $taxs) {
                                                                        ?>
                                                                        <option value='<?= $taxs->id ?>' <?= ($taxs->id === $value->tax_id) ? 'selected' : '' ?>><?= $taxs->nama ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-group">
                                                                <input class="form-control pull-right input-sm" name="diskon[<?= $value->id ?>]" <?= ($po->status === 'draft') ? '' : 'disabled' ?>
                                                                       style="width: 70%" value="<?= $value->diskon > 0 ? $value->diskon : 0 ?>" required>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                    if (!empty($value->catatan)) {
                                                        $catatan = explode("#", $value->catatan);
                                                        foreach ($catatan as $keys => $catt) {
                                                            ?>
                                                            <tr>
                                                                <td class="text-right tbl-catatan"><?= $no . "." . ($keys + 1) ?></td>
                                                                <td class="tbl-catatan" colspan="8" style="vertical-align: top; color:red;">
                                                                    <?= $catt ?>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                }
                                                if (strtolower($po->status) !== "draft") {
                                                    ?>
                                                    <tr>    
                                                        <td colspan="8" class="style text-right">Total</td>
                                                        <td class="style text-center totalan"> 
                                                            <strong>Rp. <?= number_format($totals, 2) ?>
                                                            </strong></td>
                                                    </tr>
                                                    <tr>    
                                                        <td colspan="8" class="style text-right">Discount</td>
                                                        <td class="style text-center totalan"> 
                                                            <strong>Rp. <?= number_format($diskons, 2) ?>
                                                            </strong></td>
                                                    </tr>
                                                    <tr>    
                                                        <td colspan="8" class="style text-right">Subtotal</td>
                                                        <td class="style text-center totalan"> 
                                                            <strong>Rp. <?= number_format(($totals - $diskons), 2) ?>
                                                            </strong></td>
                                                    </tr>
                                                    <tr>    
                                                        <td colspan="8" class="style text-right">Taxes</td>
                                                        <td class="style text-center totalan"> 
                                                            <strong>Rp. <?= number_format($taxes, 2) ?>
                                                            </strong></td>
                                                    </tr>

                                                    <tr>    
                                                        <td colspan="8" class="style text-right">Total</td>
                                                        <td class="style text-center totalan"> 
                                                            <strong>Rp. <?= number_format(($totals - $diskons) + $taxes, 2) ?>
                                                            </strong></td>
                                                    </tr>

                                                    <?php
                                                } else {
                                                    if ($po->nilai_currency !== null) {
                                                        ?> 
                                                        <tr>    
                                                            <td colspan="8" class="style text-right">Total</td>
                                                            <td class="style text-center totalan"> 
                                                                <strong>Rp. <?= number_format($totals, 2) ?>
                                                                </strong></td>
                                                        </tr>
                                                        <tr>    
                                                            <td colspan="8" class="style text-right">Discount</td>
                                                            <td class="style text-center totalan"> 
                                                                <strong>Rp. <?= number_format($diskons, 2) ?>
                                                                </strong></td>
                                                        </tr>
                                                        <tr>    
                                                            <td colspan="8" class="style text-right">Subtotal</td>
                                                            <td class="style text-center totalan"> 
                                                                <strong>Rp. <?= number_format(($totals - $diskons), 2) ?>
                                                                </strong></td>
                                                        </tr>
                                                        <tr>    
                                                            <td colspan="8" class="style text-right">Taxes</td>
                                                            <td class="style text-center totalan"> 
                                                                <strong>Rp. <?= number_format($taxes, 2) ?>
                                                                </strong></td>
                                                        </tr>

                                                        <tr>    
                                                            <td colspan="8" class="style text-right">Total</td>
                                                            <td class="style text-center totalan"> 
                                                                <strong>Rp. <?= number_format(($totals - $diskons) + $taxes, 2) ?>
                                                                </strong></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                        <input type="hidden" name="totals" id="totals" value="<?= ($totals - $diskons) + $taxes ?>">

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <?php
                $this->load->view("admin/_partials/modal.php");
                $this->load->view("admin/_partials/footer.php");
                ?>
                <script src="<?= base_url("dist/js/draggable.js") ?>"></script>
            </footer>
        </div>
        <?php
        $image = base_url("upload/product/default.jpg");
        $image_prod = $produk->kode_produk ?? "";
        if (is_file(FCPATH . "upload/product/{$image_prod}.jpg")) {
            $image = base_url("upload/product/{$image_prod}.jpg");
        }
        ?>
        <div class="modal fade" id="view_datas" role="dialog">
            <div class="modal-dialog" >
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <form class="form-horizontal">
                        <div class="range-wrapper">
                            <div class="select-none">10%</div>
                            <div class="range-container">
                                <div class="left"></div>
                                <div class="knob" id="knob"></div>
                                <div class="right"></div></div>
                            <div class="select-none">200%</div>
                        </div>
                        <div class="modal-body">
                            <div class=image-container>
                                <img id="img-plus" class="img-plus" src="<?= $image ?>">
                            </div>

                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
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

                $(".uom_beli").select2({
                    allowClear: true,
                    placeholder: "Satuan Beli",
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>warehouse/produk/get_uom_beli",
                        delay: 250,
                        data: function (params) {
                            return{
                                nama: params.term,
                                ke: 0
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.id,
                                    text: item.text,
                                    catatan: item.catatan
                                });
                            });
                            return {
                                results: results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert_notify("fa fa-warning", xhr.responseJSON.message, "danger", function () {}, 500);
                            //alert('Error data');
                            //alert(xhr.responseText);
                        }
                    }
                });

                $(".uom_beli").on("select2:select", function () {
                    var row = $(this).attr("data-row");
                    var selectedSelect2OptionSource = $(".uom_beli_data_" + row + " :selected").data().data.catatan;
                    $(".note_uom_beli_" + row).html(selectedSelect2OptionSource);
                    var text = $(".uom_beli_data_" + row + " :selected").text();
                    $(".nama_uom_" + row).val(text.trim());
                });

                $(".uom_beli").on("change", function () {
                    var row = $(this).attr("data-row");
                    $(".note_uom_beli_" + row).html("");
                    $(".nama_uom_" + row).val("");
                });



                $("#btn-simpan").off("click").unbind("click").on("click", function () {
                    $("#form-cfq-submit").trigger("click");
                });

                $(".currency").select2({
                    allowClear: true,
                    placeholder: "Kurs"

                });

                $(".tax").select2({
                    allowClear: true,
                    placeholder: "Pajak",

                });

                $("#currency").on("select2:select", function () {
                    $("#nilai_currency").val($("#currency :selected").attr("data-kurs"));
                });

                const form = document.forms.namedItem("form-cfq");
                form.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-cfq").then(
                            response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200) {
                                    location.reload();
                                }
                            }).catch(err => {
                        unblockUI(function () {});
                        alert_modal_warning("Hubungi Dept IT");
                    });
                    event.preventDefault();
                },
                        false
                        );

                const updateStatus = ((status) => {
                    $.ajax({
                        url: "<?= base_url('purchase/requestforquotation/update_status/' . $id) ?>",
                        type: "POST",
                        data: {
                            status: status,
                            totals: $("#totals").val()
                        },
                        error: function (req, error) {
                            unblockUI(function () {
                                setTimeout(function () {
                                    alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                }, 500);
                            });
                        },
                        success: function (data) {
                            if (data.redirect !== "") {
                                location.href = data.redirect;
                            }
                            location.reload();
                        }

                    });
                });
                $("#btn-update-status").off("click").unbind("click").on("click", function () {
                    var status = $(this).data("status");
                    confirmRequest("Request For Quotation", "Update Status Request For Quotation ? ", function () {
                        please_wait(function () {});
                        updateStatus(status);
                    });
                });
                $("#btn-cancel").off("click").unbind("click").on("click", function () {
                    var status = "cancel";
                    confirmRequest("Request For Quotation", "Batalkan Request For Quotation ? ", function () {
                        please_wait(function () {});
                        updateStatus(status);
                    });
                });

                $(".zoom").on("click", function () {
                    var images = "<?= base_url() ?>" + $(this).data("image");
                    $(".img-plus").attr("src", images);
                    //                        $("figure.zoom").attr("style", "background-image: url(" + image + " ); background-position: 7.8% 40.2135%;")
                    $("#view_datas").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    const image = document.getElementById('img-plus');
                    const knob = document.getElementById('knob');
                    const leftSide = knob.previousElementSibling;
                    const rightSide = knob.nextElementSibling;

                    // The current position of mouse
                    let x = 0;
                    let y = 0;
                    let leftWidth = 0;

                    const minScale = 0.1;
                    const maxScale = 2;
                    const step = (maxScale - minScale) / 100;

                    // Create new image element
                    const cloneImage = new Image();
                    cloneImage.addEventListener('load', function (e) {
                        // Get the natural size
                        const width = e.target.naturalWidth;
                        const height = e.target.naturalHeight;

                        // Set the size for image
                        image.style.width = `${width}px`;
                        image.style.height = `${height}px`;
                        const scale = image.parentNode.getBoundingClientRect().width / width;
                        image.style.transform = `scale(${scale}, ${scale})`;

                        leftSide.style.width = `${(scale - minScale) / step}%`;
                    });
                    cloneImage.src = image.src;
                    const mouseDownHandler = function (e) {
                        x = e.clientX;
                        y = e.clientY;
                        leftWidth = leftSide.getBoundingClientRect().width;
                        document.addEventListener('mousemove', mouseMoveHandler);
                        document.addEventListener('mouseup', mouseUpHandler);
                    };

//                        cloneImage.style.transform = "scale(0.2425, 0.2425)";
                    const mouseMoveHandler = function (e) {
                        const dx = e.clientX - x;
                        const dy = e.clientY - y;
                        const containerWidth = knob.parentNode.getBoundingClientRect().width;
                        let newLeftWidth = (leftWidth + dx) * 100 / containerWidth;
                        newLeftWidth = Math.max(newLeftWidth, 0);
                        newLeftWidth = Math.min(newLeftWidth, 100);

                        leftSide.style.width = `${newLeftWidth}%`;

                        leftSide.style.userSelect = 'none';
                        leftSide.style.pointerEvents = 'none';

                        rightSide.style.userSelect = 'none';
                        rightSide.style.pointerEvents = 'none';

                        const scale = minScale + (newLeftWidth * step);
                        image.style.transform = `scale(${scale}, ${scale})`;
                    };

                    // Triggered when user drops the knob
                    const mouseUpHandler = function () {
                        leftSide.style.removeProperty('user-select');
                        leftSide.style.removeProperty('pointer-events');

                        rightSide.style.removeProperty('user-select');
                        rightSide.style.removeProperty('pointer-events');

                        // Remove the handlers of `mousemove` and `mouseup`
                        document.removeEventListener('mousemove', mouseMoveHandler);
                        document.removeEventListener('mouseup', mouseUpHandler);
                    };
                    knob.addEventListener('mousedown', mouseDownHandler);
                    $("#img-plus").draggable();
                });


            });

        </script>
    </body>
</html>