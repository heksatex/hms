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
                case "exception":
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
//                        $statuss = [
//                            'draft' => [
//                                'value' => "confirm_order",
//                                'text' => "Confirm Order"
//                            ],
//                            'rfq' => [
//                                'value' => "confirm_rfq",
//                                'text' => "Confirm Order RFQ"
//                            ],
//                            'waiting_approval' => [
//                                'value' => "approval",
//                                'text' => "Confirm Purchase"
//                            ]
//                        ];
                        $statuss = [
                            'draft' => [
                                'value' => "confirm_order",
                                'text' => "Confirm Order"
                            ],
                            'waiting_approval' => [
                                'value' => "approval",
                                'text' => "Confirm Purchase"
                            ]
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
                            <h3 class="box-title">&nbsp;<strong> <?= $po->no_po ?> </strong></h3>
                            <div class="pull-right text-right" id="btn-header">
                                <?php if (!in_array($po->status, ["cancel", "done", "purchase_confirmed"])) { ?>
                                    <?php
                                    if (($po->status === 'waiting_approval')) {
                                        if (in_array($user->level, ["Super Administrator", "Administrator", "Direksi"])) {
                                            ?>
                                            <button class="btn btn-success btn-sm" id="btn-update-status" data-status="<?= $statuss[$po->status]["value"] ?? "-" ?>"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                                <i class="fa fa-check">&nbsp; <?= $statuss[$po->status]["text"] ?? "-" ?></i>
                                            </button>
                                            <?php
                                        }
                                    } else if ($po->status === 'exception') {
                                        if ($po->poe_status === "waiting_approve") {
                                            ?> 
                                            <button class="btn btn-success btn-sm" id="exc-update-status" data-status="approve"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                                <i class="fa fa-check">Approve Exception</i>
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
                                                            <?php
                                                            if ($po->currency === null) {
                                                                ?>
                                                                <option value="1" data-kurs="1" selected>IDR</option>
                                                                <?php
                                                            }
                                                            ?>
                                                            <?php
                                                            foreach ($kurs as $key => $kur) {
                                                                ?>
                                                                <option value="<?= $kur->id ?>" data-kurs="<?= $kur->kurs ?>" <?= ($kur->id === $po->currency) ? 'selected' : '' ?>><?= $kur->currency ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>

                                                    </div>
                                                </div>
                                                <input type="hidden" class="form-control input-sm" id="nilai_currency" name="nilai_currency" value="<?= ( $po->nilai_currency < 1) ? 1.00 : $po->nilai_currency ?>">
                                            </div>
                                        </div>
                                        <!--                                        <div class="form-group">
                                                                                    <div class="col-md-12 col-xs-12">
                                                                                        <div class="col-xs-4">
                                                                                            <label class="form-label required">Kurs</label>
                                                                                        </div>
                                                                                        <div class="col-xs-8 col-md-4 text-uppercase">
                                                                                            <input type="text" class="form-control input-sm" id="nilai_currency" name="nilai_currency" value="<?= ( $po->nilai_currency < 1) ? 1.00 : $po->nilai_currency ?>" 
                                                                                                   required <?= ($po->status === 'draft') ? '' : 'readonly' ?>>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>-->
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">Tipe</label>
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
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Foot Note (Print)</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <textarea class="form-control" id="foot_note" name="foot_note"><?= $po->foot_note ?></textarea>
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
                                                    <?php
                                                    if ($po->status === "draft") {
                                                        ?>
                                                        <input type="datetime-local" class="form-control" name="order_date" id="order_date" value="<?= $po->order_date ?>">
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <span><?= ( $po->order_date === null ) ? "" : date("l, d M Y H:i:s", strtotime($po->order_date)) ?></span>
                                                        <?php
                                                    }
                                                    ?>

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
                                            $amountTaxes = 0;
                                            foreach ($po_items as $key => $value) {
                                                $no += 1;
                                                $total = ($value->qty_beli * $value->harga_per_uom_beli);
                                                $totals += $total;
                                                $diskon = ($value->diskon ?? 0);
                                                $diskons += $diskon;
                                                if ($setting !== null) {
                                                    $taxes += ((($total - $diskon) * 11) / 12) * $value->amount_tax;
                                                } else {
                                                    $taxes += ($total - $diskon) * $value->amount_tax;
                                                }
                                                if ($value->amount_tax > 0) {
                                                    $amountTaxes = $value->amount_tax;
                                                }
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?= $no ?>
                                                    </td>
                                                    <td>
                                                        <?= ($value->kode_cfb === "") ? "" : $value->kode_cfb ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        $image = "/upload/product/" . $value->kode_produk . ".jpg";
                                                        $imageThumb = "/upload/product/thumb-" . $value->kode_produk . ".jpg";
                                                        if (is_file(FCPATH . $image)) {
                                                            ?>
                                                            <a href="<?= base_url($image) ?>" class="pop-image">
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
                                                                   value="<?= htmlentities($value->deskripsi) ?>">

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?= $value->qty . " " . $value->uom ?>
                                                    </td>
                                                    <td style="width: 15%">
                                                        <div class="form-group">
                                                            <div class="input-group">
                                                                <div class="input-group-addon"><?= $value->qty_beli ?></div>
                                                                <input type="hidden" name="qty_beli[<?= $value->id ?>]" value="<?= $value->qty_beli ?>">
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
                                                            <?php if ($po->no_value === "1") { ?>
                                                                <input class="form-control pull-right input-sm" name="harga[<?= $value->id ?>]" readonly
                                                                       style="width: 70%" value="0">
                                                                   <?php } else { ?>
                                                                <input class="form-control pull-right input-sm" name="harga[<?= $value->id ?>]" <?= ($po->status === 'draft') ? '' : 'disabled' ?>
                                                                       style="width: 70%" value="<?= $value->harga_per_uom_beli > 0 ? (float) $value->harga_per_uom_beli : 0 ?>" required>
                                                                   <?php } ?>

                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group text-right">
                                                            <input type="hidden" class="amount_tax_<?= $key ?>" name="amount_tax[<?= $value->id ?>]" value="<?= $value->amount_tax ?>">
                                                            <?php if ($po->no_value === "1") { ?>
                                                                <select style="width: 70%" class="form-control tax tax<?= $key ?> input-xs"  data-row="<?= $key ?>" 
                                                                        name="tax[<?= $value->id ?>]"  disabled>
                                                                    <option></option>
                                                                </select>
                                                            <?php } else { ?>

                                                                <select style="width: 70%" class="form-control tax tax<?= $key ?> input-xs"  data-row="<?= $key ?>" 
                                                                        name="tax[<?= $value->id ?>]"  <?= ($po->status === 'draft') ? '' : 'disabled' ?>>
                                                                    <option></option>
                                                                    <?php
                                                                    foreach ($tax as $key => $taxs) {
                                                                        ?>
                                                                        <option value='<?= $taxs->id ?>' data-nilai_tax="<?= $taxs->amount ?>" <?= ($taxs->id === $value->tax_id) ? 'selected' : '' ?>><?= $taxs->nama ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                            <?php } ?>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="form-group">
                                                            <?php if ($po->no_value === "1") { ?>
                                                                <input type="text" class="form-control pull-right input-sm" name="diskon[<?= $value->id ?>]" style="width: 70%" value="0" readonly>
                                                            <?php } else { ?>
                                                                <input class="form-control pull-right input-sm" name="diskon[<?= $value->id ?>]" <?= ($po->status === 'draft') ? '' : 'disabled' ?>
                                                                       style="width: 70%" value="<?= $value->diskon > 0 ? $value->diskon : 0 ?>"  required>
                                                                   <?php } ?>
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
                                                    <td colspan="8" class="style text-right">Subtotal 1</td>
                                                    <td class="style text-center totalan"> 
                                                        <strong><?= $po->symbol ?> <?= number_format($totals, 4) ?>
                                                        </strong></td>
                                                </tr>
                                                <tr>    
                                                    <td colspan="8" class="style text-right">Discount</td>
                                                    <td class="style text-center totalan"> 
                                                        <strong><?= $po->symbol ?> <?= number_format($diskons, 4) ?>
                                                        </strong></td>
                                                </tr>
                                                <tr>    
                                                    <td colspan="8" class="style text-right">Subtotal 2</td>
                                                    <td class="style text-center totalan"> 
                                                        <strong><?= $po->symbol ?> <?= number_format(($totals - $diskons), 4) ?>
                                                        </strong></td>
                                                </tr>

                                                <?php if ($setting !== null) {
                                                    ?>
                                                    <tr>    
                                                        <td colspan="8" class="style text-right">DPP Nilai Lain</td>
                                                        <td class="style text-center totalan"> 
                                                            <input name="dpplain" type="hidden" value="1">
                                                            <strong><?= $po->symbol ?> <?= number_format((($totals - $diskons) * 11) / 12, 4) ?>
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                <?php }
                                                ?>
                                                <tr>    
                                                    <td colspan="8" class="style text-right">Taxes</td>
                                                    <td class="style text-center totalan"> 
                                                        <strong><?= $po->symbol ?> <?= number_format($taxes, 4) ?>
                                                        </strong></td>
                                                </tr>

                                                <tr>    
                                                    <td colspan="8" class="style text-right">Total</td>
                                                    <td class="style text-center totalan"> 
                                                        <strong><?= $po->symbol ?> <?= number_format(($totals - $diskons) + $taxes, 4) ?>
                                                        </strong></td>
                                                </tr>

                                                <?php
                                            } else {
                                                if ($po->nilai_currency !== null) {
                                                    ?> 
                                                    <tr>    
                                                        <td colspan="8" class="style text-right">Subtotal 1</td>
                                                        <td class="style text-center totalan"> 
                                                            <strong><?= $po->symbol ?> <?= number_format($totals, 4) ?>
                                                            </strong></td>
                                                    </tr>
                                                    <tr>    
                                                        <td colspan="8" class="style text-right">Discount</td>
                                                        <td class="style text-center totalan"> 
                                                            <strong><?= $po->symbol ?> <?= number_format($diskons, 4) ?>
                                                            </strong></td>
                                                    </tr>

                                                    <tr>    
                                                        <td colspan="8" class="style text-right">Subtotal 2</td>
                                                        <td class="style text-center totalan"> 
                                                            <strong><?= $po->symbol ?> <?= number_format(($totals - $diskons), 4) ?>
                                                            </strong></td>
                                                    </tr>
                                                    <?php if ($setting !== null) {
                                                        ?>
                                                        <tr>    
                                                            <td colspan="8" class="style text-right">DPP Nilai Lain</td>
                                                            <td class="style text-center totalan"> 
                                                                <input name="dpplain" type="hidden" value="1">
                                                                <strong><?= $po->symbol ?> <?= number_format((($totals - $diskons) * 11) / 12, 4) ?>
                                                                </strong>
                                                            </td>
                                                        </tr>
                                                    <?php }
                                                    ?>
                                                    <tr>    
                                                        <td colspan="8" class="style text-right">Taxes</td>
                                                        <td class="style text-center totalan"> 
                                                            <strong><?= $po->symbol ?> <?= number_format($taxes, 4) ?>
                                                            </strong></td>
                                                    </tr>

                                                    <tr>    
                                                        <td colspan="8" class="style text-right">Total</td>
                                                        <td class="style text-center totalan"> 
                                                            <strong><?= $po->symbol ?> <?= number_format(($totals - $diskons) + $taxes, 4) ?>
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
                        </form>
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <?php
                $this->load->view("admin/_partials/modal.php");
                $this->load->view("admin/_partials/footer.php");
                ?>
                <script src="<?= base_url("dist/js/light-box.min.js") ?>"></script>
            </footer>
        </div>
        <script>

            $(document).ready(function () {
                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
                });
                $(".pop-image").magnificPopup({
                    type: 'image'
                });

            });
            $(function () {

                $(".uom_beli").select2({
                    allowClear: true,
                    placeholder: "Satuan Beli",
                    ajax: {
                        dataType: "JSON",
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


                $(".tax").on("select2:select", function () {
                    var row = $(this).attr("data-row");
                    var selectedSelect2OptionSource = $(".tax" + row + " :selected").data().nilai_tax;
                    $(".amount_tax_" + row).val(selectedSelect2OptionSource);
                });

                $(".tax").on("change", function () {
                    var row = $(this).attr("data-row");
                    $(".amount_tax_" + row).val("0");
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

                $(".currency").on("select2:select", function () {
                    var selectedSelect2OptionSource = $(this).find(':selected').data('kurs');
                    $("#nilaiKurs").html(selectedSelect2OptionSource);
                });

                $(".tax").select2({
                    allowClear: true,
                    placeholder: "Pajak"

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
                                location.href = "<?= site_url('purchase/purchaseorder/edit') ?>/" + data.redirect;
                            } else {
                                location.reload();
                            }
                        }

                    });
                });
                $("#btn-update-status").off("click").unbind("click").on("click", function () {
                    var status = $(this).data("status");
//                    confirmRequest("Request For Quotation", "Update Status Request For Quotation ? ", function () {
//                        please_wait(function () {});
//                        updateStatus(status);
//                    });
                    updateStatus(status);
                });
                $("#btn-cancel").off("click").unbind("click").on("click", function () {
                    var status = "cancel";
                    confirmRequest("Request For Quotation", "Batalkan Request For Quotation ? ", function () {
                        please_wait(function () {});
                        updateStatus(status);
                    });
                });
                $("#exc-update-status").off("click").unbind("click").on("click", function () {
                    var status = "exception";
                    confirmRequest("Request For Quotation", "Approve perubahan Harga PO ? ", function () {
                        $.ajax({
                            url: "<?= base_url('purchase/purchaseorder/update_status/' . $id) ?>",
                            type: "POST",
                            beforeSend: function (xhr) {
                                please_wait(function () {});

                            },
                            data: {
                                status: "purchase_confirmed",
                                items : "<?php count($po_items) ?>",
                                totals: 0,
                                default_total:1
                            },
                            error: function (req, error) {
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                    }, 500);
                                });
                            },
                            success: function (data) {
                                location.reload();
                            }

                        });
                    });
                });
            });

        </script>
    </body>
</html>