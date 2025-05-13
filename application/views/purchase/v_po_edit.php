<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link href="<?= base_url('dist/css/popup_img.css') ?>" rel="stylesheet">
        <style>

            .tbl-catatan {
                font-size: 11px
            }
            .totalan {
                font-size: 14px;
            }
            #btn-approve {
                display: none;
            }
            <?php if (!in_array($po->status, ['purchase_confirmed', 'done'])) { ?>

                #btn-simpan {
                    display: none;
                }
                #btn-cancel {
                    display: none;
                }
                #btn-print {
                    display: none;
                }
            <?php } ?>

            .bs-glyphicons {
                padding-left: 0;
                padding-bottom: 1px;
                margin-bottom: 20px;
                list-style: none;
                overflow: hidden;
            }

            .bs-glyphicons li {
                float: left;
                width: 25%;
                height: 50px;
                padding: 10px;
                margin: 0 -1px -1px 0;
                font-size: 12px;
                line-height: 1.4;
                text-align: left;
                border: 1px solid #ddd;
            }

            .bs-glyphicons .glyphicon {
                margin-top: 5px;
                margin-bottom: 10px;
                font-size: 20px;
            }

            .bs-glyphicons .glyphicon-class {
                display: inline-block;
                text-align: center;
                word-wrap: break-word; /* Help out IE10+ with class names */
            }

            .bs-glyphicons li:hover {
                background-color: rgba(86, 61, 124, .1);
            }

            @media (min-width: 768px) {
                .bs-glyphicons li {
                    width: 50%;
                }
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

    </head>

    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <div class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </div>
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
                                <?php
                                if (in_array($po->status, ["purchase_confirmed", "done", "exception"])) {
                                    if ($po->edited_status === null) {
                                        if (in_array(strtolower($user->level), ["super administrator", 'supervisor'])) {
                                            ?>
                                            <button class="btn btn-default btn-sm request_edit" data-status="approve"> Request Edit </button>
                                            <?php
                                        }
                                    } else {
                                        if (in_array(strtolower($user->level), ["super administrator", 'supervisor'])) {
                                            if ($po->edited_status === "request") {
                                                ?>
                                                <button class="btn btn-primary btn-sm approve_edit" data-status="approve"> Approve Edit Harga </button>
                                                <?php
                                            } else {
                                                ?>
                                                <span class="label label-warning text-black text-uppercase"><?= str_replace("_", " ", $po->edited_status) ?> Edit Harga</span>
                                                <?php
                                            }
                                            ?>

                                            <?php
                                        } else {
                                            ?>
                                            <span class="label label-warning text-black text-uppercase"><?= str_replace("_", " ", $po->edited_status) ?> Edit Harga</span>
                                            <?php
                                        }
                                        ?>
                                        <br>
                                        <span class="label label-danger"><?= $po->alasan ?></span>
                                        <!--<button class="btn btn-primary btn-sm request_edit" data-status="cancel"> Cancel Request </button>-->
                                        <?php
                                        if ($po->edited_status === "approve") {
                                            ?>
                                            <style>
                                                #btn-print {
                                                    display:none;
                                                }
                                                #btn-approve,#btn-simpan {
                                                    display:inline-block
                                                }
                                            </style>
                                            <?php
                                        }
                                    }
                                    ?>

                                <?php } ?>
                            </div>

                        </div>
                        <form  class="form-horizontal" method="POST" name="form-cfq" id="form-cfq" action="<?= base_url('purchase/purchaseorder/update/' . $id) ?>">
                            <input type="hidden" name="default_total" id="default_total" value="<?= $default_total ?>" >
                            <div class="box-body">
                                <div class="col-md-8 col-xs-12">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="row">
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
                                                    <div class="col-xs-4 col-md-4 text-uppercase">
                                                        <div class="input-group">
                                                            <select class="form-control currency"  name="currency" id="currency"  required <?= ($po->status === 'purchase_confirmed') ? 'disabled' : 'disabled' ?> >
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
                                                    <!--                                                    <div class="col-xs-4">
                                                                                                            <label class="form-label">Kurs</label> <span id="nilaiKurs"><?= $po->nilai_currency ?? 1.00 ?></span>
                                                                                                        </div>-->
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label required">Tipe</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8 text-uppercase">
                                                        <div class="input-group">
                                                            <!--<div class="input-group-addon"><i class="fa fa-dollar"></i></div>-->
                                                            <select class="form-control no_value"  name="no_value" id="no_value"  required <?= ($po->status === 'draft') ? '' : 'disabled' ?> >
                                                                <option value="0">Value</option>
                                                                <option value="1" <?= ($po->no_value === "1") ? 'selected' : '' ?>>No Value</option>
                                                            </select>
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
                                                        <textarea class="form-control" id="note" name="note" readonly><?= $po->note ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-xs-12">
                                    <ul class="bs-glyphicons">
                                        <li class="pointer shipment">
                                            <span class="glyphicon glyphicon-transfer"></span>            
                                            <span class="glyphicon-class"></strong> In Shipment</span>
                                        </li>      
                                        <?php if ($po->no_value === "0") { ?>
                                            <li class="pointer invoice">
                                                <span class="glyphicon glyphicon-list-alt"></span>
                                                <span class="glyphicon-class"><strong id="invoice"></strong> Invoice</span>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>

                            </div>
                            <div class="box-footer">
                                <button type="submit" id="form-cfq-submit" style="display: none"></button>
                                <div class="col-md-12 table-responsive over">
                                    <ul class="nav nav-tabs " >
                                        <li class="active"><a href="#tab_1" data-toggle="tab">Produk</a></li>
                                        <li><a href="#tab_2" data-toggle="tab">Retur</a></li>
                                    </ul>
                                    <div class="tab-content"><br>

                                        <div class="tab-pane" id="tab_2">
                                            <div class="col-md-3 col-xs-12">
                                                <div class="pull-left">
                                                    <?php if (!in_array($po->status, ['exception', 'cancel'])) { ?>
                                                        <button class="btn btn-default btn-sm btn-cancel_retur" type="button">Batalkan Retur</button>
                                                    <?php } ?>
                                                </div>

                                            </div>
                                            <br>
                                            <div class="col-md-12">
                                                <table class="table table-condesed table-hover rlstable  over" width="100%">
                                                    <thead>
                                                    <th class="style" width="10px"></th>
                                                    <th class="style" width="10px">No</th>
                                                    <th class="style">Produk</th>
                                                    <th class="style">Deskripsi</th>
                                                    <th class="style">Qty Beli Retur</th>
                                                    <th class="style">Tanggal Retur</th>
                                                    <th class="style" >Status</th>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $noo = 0;
                                                        $countStDraft = 0;
                                                        foreach ($po_retur as $key => $value) {
                                                            $noo++;
                                                            if ($value->status === 'draft') {
                                                                $countStDraft++;
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <?php if ($value->status === 'draft') { ?>
                                                                        <input type="checkbox" class="data-id-retur" name="checklist_retur[]" value="<?= $value->id ?>">
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td><?= $noo ?></td>
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
                                                                    <?= "[{$value->kode_produk}] {$value->nama_produk}" ?>
                                                                </td>
                                                                <td>
                                                                    <?= $value->deskripsi ?>
                                                                </td>
                                                                <td>
                                                                    <?= "{$value->qty_beli_retur} {$value->uom_beli_retur}" ?>
                                                                </td>
                                                                <td><?= date("l, d M Y H:i:s", strtotime($value->retur_date)) ?></td>
                                                                <td><?= $value->status ?></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        if ($countStDraft > 0) {
                                                            ?>
                                                            <tr>
                                                                <td colspan="4"></td>
                                                                <td>
                                                                    <button class="btn btn-primary btn-sm btn-confirmasi-retur" type="button" data-ids="<?= $po->no_po ?>" >Konfirmasi Retur</button>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <div class="tab-pane active" id="tab_1">
                                            <div class="col-md-3 col-xs-12">
                                                <div class="pull-left">
                                                    <?php if (!in_array($po->status, ['exception', 'cancel'])) { ?>
                                                        <button class="btn btn-danger btn-sm btn-retur" type="button">Retur</button>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="col-md-12">
                                                <table class="table table-condesed table-hover rlstable  over" style="width:100%">
                                                    <thead>
                                                    <th class="style" width="5px">
                                                        <input type="checkbox" class="check-all-retur" id="checkall">
                                                    </th>
                                                    <th class="style" width="10px">No</th>
                                                    <th class="style" style="width:10%">Kode CFB</th>
                                                    <th class="style" style="width:10%" >Produk</th>
                                                    <th class="style" style="width:15%">Deskripsi</th>
                                                    <th class="style" style="width:10%">Schedule Date</th>
                                                    <th class="style"style="width:15%" >Qty / Uom Beli</th>
                                                    <td class="style text-right" style="width:15%">Harga Satuan Beli</td>
                                                    <td class="style text-right" >Tax</td>
                                                    <td class="style" >Reff Note</td>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $no = 0;
                                                        $amountTaxes = 0;
                                                        $nilaiDppLain = 0;
                                                        foreach ($po_items as $key => $value) {
                                                            $no += 1;
                                                            $total = ($value->qty_beli * $value->harga_per_uom_beli);
                                                            $totals += $total;
                                                            $diskon = (($value->diskon ?? 0));
                                                            $diskons += $diskon;
                                                            if ($setting !== null) {
                                                                $taxes += ((($total - $diskon) * 11) / 12) * $value->amount_tax;
                                                                $nilaiDppLain += ((($total - $diskon) * 11) / 12);
                                                            } else {
                                                                $taxes += ($total - $diskon) * $value->amount_tax;
                                                            }
                                                            if ($value->amount_tax > 0) {
                                                                $amountTaxes = $value->amount_tax;
                                                            }
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <?php if ($value->status !== 'retur') { ?>
                                                                        <input type="checkbox" class="check-retur" name="checklist[]" value="<?= $value->id ?>">
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </td>
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
                                                                    <?= "[{$value->kode_produk}] {$value->nama_produk}" ?>
                                                                </td>
                                                                <td>
                                                                    <?= $value->deskripsi ?>
                                                                </td>
                                                                <td>
                                                                    <?= $value->schedule_date ?>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon"><?= $value->qty_beli ?></div>
                                                                            <input type="hidden" name="uom_jual[<?= $value->id ?>]" value="<?= $value->uom ?>">
                                                                            <input type="hidden" name="qty_beli[<?= $value->id ?>]" value="<?= $value->qty_beli ?>">
                                                                            <input type="hidden" name="id_konversiuom[<?= $value->id ?>]"  value="<?= $value->id_konversiuom ?>">
                                                                            <input type="hidden" class="amount_tax_<?= $key ?>" name="amount_tax[<?= $value->id ?>]" value="<?= $value->amount_tax ?>">
                                                                            <input type="hidden" class="dpp_tax_<?= $key ?>" name="dpp_tax[<?= $value->id ?>]" value="<?= $value->dpp_tax ?>">
                                                                            <select class="form-control uom_beli input-xs uom_beli_data_<?= $key ?>" style="width: 70%" data-row="<?= $key ?>" disabled>
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
                                                                        <input class="form-control pull-right input-sm" name="harga[<?= $value->id ?>]" <?= ($po->status === 'exception' && (!in_array($value->status, ["cancel", "retur"]))) ? '' : 'readonly' ?>
                                                                               value="<?= $value->harga_per_uom_beli > 0 ? (float) $value->harga_per_uom_beli : 0 ?>" required>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group text-right">
                                                                        <input  name="tax[<?= $value->id ?>]" type="hidden" value="<?= $value->tax_id ?>">
                                                                        <select  class="form-control tax tax<?= $key ?> input-xs" name="tax[<?= $value->id ?>]" data-row="<?= $key ?>" <?= ($po->status === 'exception' && (!in_array($value->status, ["cancel", "retur"]))) ? '' : 'disabled' ?>  > 
                                                                            <option></option>
                                                                            <?php
                                                                            foreach ($tax as $key => $taxs) {
                                                                                ?>
                                                                                <option data-dpp_tax="<?= $taxs->dpp ?>" data-nilai_tax="<?= $taxs->amount ?>" value='<?= $taxs->id . "|" . $taxs->amount ?>' <?= ($taxs->id === $value->tax_id) ? 'selected' : '' ?>><?= $taxs->nama ?></option>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <input type="hidden" class="form-control pull-right input-sm" name="diskon[<?= $value->id ?>]"
                                                                           value="<?= $value->diskon > 0 ? $value->diskon : 0 ?>" >
                                                                </td>
                                                                <td>
                                                                    <div class="form-text">
                                                                        <?= $value->reff_note ?>
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
                                                                        <td class="tbl-catatan" colspan="6" style="vertical-align: top; color:red;">
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
                                                                <td colspan="2" class="style text-center totalan"> 
                                                                    <strong><?= $po->symbol ?> <?= number_format($totals, 4) ?>
                                                                    </strong>
                                                                </td>
                                                            </tr>
                                                            <tr>    
                                                                <td colspan="8" class="style text-right">Discount</td>
                                                                <td colspan="2" class="style text-center totalan"> 
                                                                    <strong><?= $po->symbol ?> <?= number_format($diskons, 4) ?>
                                                                    </strong></td>
                                                            </tr>
                                                            <tr>    
                                                                <td colspan="8" class="style text-right">Subtotal 2</td>
                                                                <td colspan="2" class="style text-center totalan"> 
                                                                    <strong><?= $po->symbol ?> <?= number_format(($totals - $diskons), 4) ?>
                                                                    </strong></td>
                                                            </tr>
                                                            <?php if ($setting !== null) {
                                                                ?>
                                                                <tr>    
                                                                    <td colspan="8" class="style text-right">DPP Nilai Lain</td>
                                                                    <td colspan="2" class="style text-center totalan"> 
                                                                        <input name="dpplain" type="hidden" value="1">
                                                                        <strong><?= $po->symbol ?> <?= number_format($nilaiDppLain, 4) ?>
                                                                        </strong>
                                                                    </td>
                                                                </tr>
                                                            <?php }
                                                            ?>
                                                            <tr>    
                                                                <td colspan="8" class="style text-right">Taxes</td>
                                                                <td colspan="2" class="style text-center totalan"> 
                                                                    <strong><?= $po->symbol ?> <?= number_format($taxes, 4) ?>
                                                                    </strong></td>
                                                            </tr>

                                                            <tr>    
                                                                <td colspan="8" class="style text-right">Total</td>
                                                                <td colspan="2" class="style text-center totalan"> 
                                                                    <strong><?= $po->symbol ?> <?= number_format(($totals - $diskons) + $taxes, 4) ?>
                                                                    </strong></td>
                                                            </tr>

                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

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
                $this->load->view("admin/_partials/footer_new.php");
                ?>
                <?php $this->load->view("admin/_partials/js.php") ?>
                <script src="<?= base_url("dist/js/light-box.min.js") ?>"></script>
            </footer>
        </div>
        <?php
        if ($po->status !== 'cancel') {
            if ($po->edited_status === "approve") {
                ?>
                <script>
                    $("#btn-approve").html("Done");
                </script>

                <?php
            }
            ?>
            <script>
                $(function () {

                    $(".btn-cancel_retur").click(function () {
                        var getlist = $(".data-id-retur:checked").map(function () {
                            return $(this).val();
                        });
                        var list = getlist.get();
                        if (list.length < 1) {
                            alert_notify('fa fa-close', "Pilih item yang akan batal retur", 'danger', function () {});
                            return;
                        }
                        confirmRequest("Retur Purchase Order", "Batalkan Permintaan Retur ? ", function () {
                            $.ajax({
                                url: "<?= base_url('purchase/purchaseorder/update_retur_status') ?>",
                                type: "POST",
                                data: {
                                    items: list,
                                    po: "<?= $po->no_po ?>"
                                },
                                beforeSend: function (xhr) {
                                    please_wait(function () {});
                                },
                                success: function (data) {
                                    unblockUI(function () {});
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

                    $(".check-all-retur").click(function () {
                        $('.check-retur').not(this).prop('checked', this.checked);
                    });

                    $(".btn-retur").click(function (e) {
                        var getlist = $(".check-retur:checked").map(function () {
                            return $(this).val();
                        });
                        var list = getlist.get();
                        if (list.length < 1) {
                            alert_notify('fa fa-close', "Pilih item yang akan di retur", 'danger', function () {});
                            return;
                        }
                        $("#view_data").modal({
                            show: true,
                            backdrop: 'static'
                        });
                        $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                        $('.modal-title').text('Retur Pembelian');
                        $.post("<?= base_url('purchase/purchaseorder/get_view_retur/') ?>", {
                            items: list,
                            ids: "<?= $id ?>"
                        }, function (data) {
                            setTimeout(function () {
                                $(".view_body").html(data.data);
                            }, 1000);
                        });
                    });


                    $(".approve_edit").off("click").unbind("click").on("click", function () {
                        var datastatus = $(this).data("status");
                        confirmRequest("Purchase Order", datastatus.toUpperCase() + " Edit PO ? ", function () {
                            $.ajax({
                                type: "POST",
                                url: "<?= base_url('purchase/purchaseorder/request_edit/') ?>",
                                data: {
                                    ids: "<?= $id ?>",
                                    status: datastatus
                                },
                                beforeSend: function (xhr) {
                                    please_wait(function () {});
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
                    $(".request_edit").off("click").unbind("click").on("click", function () {
                        var datastatus = $(this).data("status");
                        bootbox.prompt({
                            title: "Alasan Request Edit",
                            centerVertical: true,
                            callback: function (result) {
                                if (!result)
                                    return;

                                $.ajax({
                                    type: "POST",
                                    url: "<?= base_url('purchase/purchaseorder/request_edit/') ?>",
                                    data: {
                                        ids: "<?= $id ?>",
                                        status: datastatus,
                                        alasan: result
                                    },
                                    beforeSend: function (xhr) {
                                        please_wait(function () {});
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

                            }
                        });
                    });

                    $(".shipment").off("click").unbind("click").on("click", function () {
                        $("#view_data").modal({
                            show: true,
                            backdrop: 'static'
                        });
                        $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                        $('.modal-title').text('Shipping');
                        $.post("<?= base_url('purchase/purchaseorder/get_shipment/' . $id) ?>", {}, function (data) {
                            $(".view_body").html(data.data);
                        });
                    });
                    $(".invoice").off("click").unbind("click").on("click", function () {
                        $("#view_data").modal({
                            show: true,
                            backdrop: 'static'
                        });
                        $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                        $('.modal-title').text('Invoice');
                        $.post("<?= base_url('purchase/purchaseorder/get_invoice/' . $id) ?>", {}, function (data) {
                            $(".view_body").html(data.data);
                        });
                    });
                    const getRcv = (() => {
                        $.ajax({
                            type: "POST",
                            url: "<?= base_url('purchase/purchaseorder/get_rcv/' . $id) ?>",
                            success: function (data) {
                                $("#invoice").html(data.in_inv);
                            }
                        });
                    });
                    getRcv();
                });
            </script>
            <?php
        }
        ?>
        <script>

            $(document).ready(function () {
//                $(window).keydown(function (event) {
//                    if (event.keyCode === 13) {
//                        event.preventDefault();
//                        return false;
//                    }
//                });
                $(".pop-image").magnificPopup({
                    type: 'image'
                });

            });
            $(function () {

                $("#btn-print").off("click").unbind("click").on("click", function () {
                    $.ajax({
                        url: "<?= base_url('purchase/purchaseorder/print') ?>",
                        type: "POST",
                        data: {
                            id: "<?= $id ?>",
                            form: "purchase_order"
                        },
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
                $(".currency").select2({
                    allowClear: true,
                    placeholder: "Kurs"

                });

                $(".tax").select2({
                    allowClear: true,
                    placeholder: "Pajak"

                });

                $(".tax").on("select2:select", function () {
                    var row = $(this).attr("data-row");
                    var selectedSelect2OptionSource = $(".tax" + row + " :selected").data().nilai_tax;
                    var dpp_tax = $(".tax" + row + " :selected").data().dpp_tax;
                    $(".dpp_tax_" + row).val(dpp_tax);
                    $(".amount_tax_" + row).val(selectedSelect2OptionSource);
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
                                    if (response.data.status === 'waiting_approval') {
                                        window.location.replace("<?= base_url('purchase/purchaseorder') ?>");
                                    } else {
                                        location.reload();
                                    }

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
                        url: "<?= base_url('purchase/purchaseorder/update_status/' . $id) ?>",
                        type: "POST",
                        data: {
                            status: status,
                            totals: $("#totals").val(),
                            item: "<?= count($po_items) ?>",
                            default_total: $("#default_total").val()
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
                $("#btn-simpan").off("click").unbind("click").on("click", function () {
//                    confirmRequest("Purchase Order", "Update Purchase Order ? ", function () {
                    $("#form-cfq-submit").trigger("click");
//                    });
                });
                $("#btn-approve").off("click").unbind("click").on("click", function () {
                    var status = "<?= ($po->status === 'exception') ? 'purchase_confirmed' : 'done' ?>";

//                    confirmRequest("Purchase Order", "Done Purchase Order ? ", function () {
                    please_wait(function () {});
                    updateStatus(status);
//                    });
                });
                $("#btn-cancel").off("click").unbind("click").on("click", function () {
                    var status = "cancel";
                    confirmRequest("Purchase Order", "Cancel Purchase Order ? ", function () {
                        please_wait(function () {});
                        updateStatus(status);
                    });
                });

                $(".btn-confirmasi-retur").off("click").unbind("click").on("click", function () {
                    var ids = $(this).data("ids");
                    confirmRequest("Purchase Order", "Konfirmasi Retur Produk ? ", function () {
                        $.ajax({
                            url: "<?= base_url('purchase/purchaseorder/confirm_retur/') ?>",
                            type: "POST",
                            data: {
                                ids: ids
                            },
                            beforeSend: function (xhr) {
                                please_wait(function () {});
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