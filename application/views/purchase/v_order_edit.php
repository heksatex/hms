<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link href="<?= base_url('dist/css/light-box.css') ?>" rel="stylesheet">
        <style>
            .totalan {
                font-size: 14px;
            }
            .prio-urgent {
                color: red;
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
                        $data["navigation_page"]=true;
                        $data["next_page"]=$next_page ?? "";
                        $data["prev_page"]=$prev_page ?? "";
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
                                        if (in_array($user->level, ["Super Administrator", "Direksi"])) {
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
                                                    <?php
                                                    if ($po->status === 'draft') {
                                                        ?>
                                                        <select class="form-control input-sm select2" name="supplier" id="supplier" required>
                                                            <option value='<?= $po->supplier ?>' selected><?= $po->supp ?></option>
                                                        </select>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <span><?= $po->supp ?></span>
                                                        <input type="hidden" value="<?= $po->supplier ?>">
                                                        <?php
                                                    }
                                                    ?>

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
                                <button type="submit" id="form-cfq-submit" style="display: none"></button>
                                <div class="col-md-12 table-responsive over">
                                    <ul class="nav nav-tabs " >
                                        <li class="active"><a href="#tab_1" data-toggle="tab">Produk</a></li>
                                        <!--<li><a href="#tab_2" data-toggle="tab">RFQ & BID</a></li>-->
                                    </ul>
                                    <div class="tab-content"><br>
                                        <div class="tab-pane active" id="tab_1">
                                            <div class="col-md-3 col-xs-12">
                                                <div class="pull-left">
                                                    <?php if ($po->status === "draft") { ?>
                                                        <button class="btn btn-success btn-sm btn-add_item" type="button">Tambahkan Item</button>
                                                    <?php } ?>
                                                </div>

                                            </div>
                                            <br>
                                            <div class="col-md-12">
                                                <table class="table table-condesed table-hover rlstable  over" style="width:100%">
                                                    <thead>
                                                    <th class="style" width="10px">#</th>
                                                    <th class="style" width="10px">No</th>
                                                    <th class="style" style="width:10%">Kode CFB</th>
                                                    <th class="style" style="width:10%" >Produk</th>
                                                    <th class="style" style="width:10%">Deskripsi</th>
                                                    <th class="style" style="width:10%">Schedule Date</th>
                                                    <th class="style"style="width:15%" >Qty / Uom Beli</th>
                                                    <td class="style text-right" style="width:13%">Harga Satuan Beli</td>
                                                    <td class="style text-right" style="width:10%">Tax</td>
                                                    <td class="style" >Reff Note</td>
                                                    <!--<td class="style text-right" width="20px">Diskon</td>-->
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $getTax = new $this->m_global;
                                                        $getTax->setTables("tax");
                                                        $no = 0;
                                                        $amountTaxes = 0;
                                                        $nilaiDppLain = 0;
                                                        foreach ($po_items as $key => $value) {
                                                            $no += 1;
                                                            $total = ($value->qty_beli * $value->harga_per_uom_beli);
                                                            $totals += $total;
                                                            $diskon = ($value->diskon ?? 0);
                                                            $diskons += $diskon;
                                                            $taxe = 0;
                                                            if ($setting !== null && $value->dpp_tax === "1") {
                                                                $taxe += ((($total - $diskon) * 11) / 12) * $value->amount_tax;
                                                                $nilaiDppLain += ((($total - $diskon) * 11) / 12);
                                                            } else {
                                                                $taxe += ($total - $diskon) * $value->amount_tax;
                                                            }
                                                            if ($value->amount_tax > 0) {
                                                                $amountTaxes = $value->amount_tax;
                                                            }
                                                            if ($value->tax_lain_id !== "0") {
                                                                $dataTax = $getTax->setWhereIn("id", explode(",", $value->tax_lain_id), true)->setSelects(["amount,dpp"])->setOrder(["id"])->getData();
                                                                foreach ($dataTax as $kkk => $data) {
                                                                    if ($setting !== null && $data->dpp === "1") {
                                                                        $taxe += ((($total - $diskon) * 11) / 12) * $data->amount;
                                                                        continue;
                                                                    }
                                                                    $taxe += ($total - $diskon) * $data->amount;
                                                                }
                                                            }
                                                            $taxes += $taxe;
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <?php
                                                                    if (count($po_items) > 1) {
                                                                        echo ($po->status === "draft") ? "<button type='button' class='btn btn-danger btn-sm delete_item' data-ids='{$value->id}'><fa class='fa fa-trash'></fa></button>" : '';
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?= $no ?>
                                                                </td>
                                                                <td>
                                                                    <?= ($value->kode_cfb === "") ? "" : $value->kode_cfb ?>
                                                                </td>
                                                                <td class="<?= ($value->pritoritas === 'Urgent') ? 'prio-urgent' : '' ?>">
                                                                    <?php
                                                                    $image = "/upload/product/" . $value->kode_produk . ".jpg";
                                                                    $imageThumb = "/upload/product/thumb-" . $value->kode_produk . ".jpg";
                                                                    if (is_file(FCPATH . $image)) {
                                                                        ?>
                                                                        <a href="<?= base_url($image) ?>" class="pop-image">
                                                                            <img src="<?= is_file(FCPATH . $imageThumb) ? base_url($imageThumb) : base_url($image) ?>" height="30">
                                                                        </a>
                                                                    <?php } ?>
                                                                    <?= "[{$value->kode_produk }] {$value->nama_produk }" ?>
                                                                </td>
                                                                <td >
                                                                    <div class="form-group" style="width:100%">
                                                                        <input class="form-control pull-right input-sm" name="deskripsi[<?= $value->id ?>]" <?= ($po->status === 'draft') ? '' : 'disabled' ?>
                                                                               value="<?= htmlentities($value->deskripsi) ?>">

                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <?= $value->schedule_date ?>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        <div class="input-group">
                                                                            <div class="input-group-addon"><?= number_format($value->qty_beli, 2) ?> </div>
                                                                            <input type="hidden" name="qty_beli[<?= $value->id ?>]" value="<?= $value->qty_beli ?>">
                                                                            <input type="hidden" name="uom_jual[<?= $value->id ?>]" value="<?= $value->uom ?>">
                                                                            <select class="form-control uom_beli input-xs uom_beli_data_<?= $key ?>" data-uom="<?= $value->uom ?>" style="width: 70%" data-row="<?= $key ?>"
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
                                                                    <div class="form-group" style="width: 100%" >
                                                                        <?php if ($po->no_value === "1") { ?>
                                                                            <input class="form-control text-right pull-right input-sm harga_satuan harga_satuan_<?= $key ?>" name="harga[<?= $value->id ?>]" readonly
                                                                                   value="0">
                                                                               <?php } else { ?>
                                                                            <input class="form-control text-right pull-right input-sm harga_satuan harga_satuan_<?= $key ?>" name="harga[<?= $value->id ?>]" <?= ($po->status === 'draft') ? '' : 'disabled' ?>
                                                                                   value="<?= $value->harga_per_uom_beli > 0 ? (float) $value->harga_per_uom_beli : 0 ?>" data-row="<?= $key ?>" required>
                                                                               <?php } ?>

                                                                        <small class="form-text text-muted note_harga_<?= $key ?>">
                                                                            <?= number_format(($value->harga_per_uom_beli > 0 ? (float) $value->harga_per_uom_beli : 0), 2, ".", ",") ?>
                                                                        </small>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group text-right">
                                                                        <input type="hidden" class="amount_tax_<?= $key ?>" name="amount_tax[<?= $value->id ?>]" value="<?= $value->amount_tax ?>">
                                                                        <input type="hidden" class="dpp_tax_<?= $key ?>" name="dpp_tax[<?= $value->id ?>]" value="<?= $value->dpp_tax ?>">
                                                                        <input type="hidden" class="tax_lain_id_<?= $key ?>" name="tax_lain_id[<?= $value->id ?>]" value="<?= $value->tax_lain_id ?>">
                                                                        <?php if ($po->no_value === "1") { ?>
                                                                            <select style="width: 100%" class="form-control tax tax<?= $key ?> input-xs"  data-row="<?= $key ?>" 
                                                                                    name="tax[<?= $value->id ?>]"  disabled>
                                                                                <option></option>
                                                                            </select>
                                                                        <?php } else { ?>

                                                                            <select style="width: 100%" class="form-control tax tax<?= $key ?> input-xs"  data-row="<?= $key ?>" 
                                                                                    name="tax[<?= $value->id ?>]"  <?= ($po->status === 'draft') ? '' : 'disabled' ?>>
                                                                                <option></option>
                                                                                <?php
                                                                                foreach ($tax as $key => $taxs) {
                                                                                    ?>
                                                                                    <option value='<?= $taxs->id ?>' data-tax_lain_id="<?= $taxs->tax_lain_id ?>" data-dpp_tax="<?= $taxs->dpp ?>" data-nilai_tax="<?= $taxs->amount ?>" <?= ($taxs->id === $value->tax_id) ? 'selected' : '' ?>><?= $taxs->nama ?></option>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        <?php } ?>
                                                                        <input type="hidden" class="form-control pull-right input-sm" name="diskon[<?= $value->id ?>]"value="0" readonly>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-text">
                                                                        <?= $value->reff_note ?>
                                                                    </div>
                                                                </td>
                <!--                                                    <td>
                                                        <div class="form-group">
                                                                <?php if ($po->no_value === "1") { ?>
                                                                                                                                                            <input type="text" class="form-control pull-right input-sm" name="diskon[<?= $value->id ?>]" style="width: 70%" value="0" readonly>
                                                                <?php } else { ?>
                                                                                                                                                            <input class="form-control pull-right input-sm" name="diskon[<?= $value->id ?>]" <?= ($po->status === 'draft') ? '' : 'disabled' ?>
                                                                                                                                                                   style="width: 70%" value="<?= $value->diskon > 0 ? $value->diskon : 0 ?>"  required>
                                                                <?php } ?>
                                                        </div>
                                                    </td>-->
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
                                                                <td colspan="2" class="style text-center totalan"> 
                                                                    <strong><?= $po->symbol ?> <?= number_format($totals, 4) ?>
                                                                    </strong></td>
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
                                                        } else {
                                                            if ($po->nilai_currency !== null) {
                                                                ?> 
                                                                <tr>    
                                                                    <td colspan="8" class="style text-right">Subtotal 1</td>
                                                                    <td colspan="2" class="style text-center totalan"> 
                                                                        <strong><?= $po->symbol ?> <?= number_format($totals, 4) ?>
                                                                        </strong></td>
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
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <input type="hidden" name="totals" id="totals" value="<?= ($totals - $diskons) + $taxes ?>">
                                        </div>
                                    </div>



                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
            <?php $this->load->view("admin/_partials/js.php") ?>
            <script src="<?= base_url("dist/js/light-box.min.js") ?>"></script>
            <footer class="main-footer">
                <?php
                $this->load->view("admin/_partials/modal.php");
                $this->load->view("admin/_partials/footer_new.php");
                ?>
            </footer>
        </div>
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

                $("#supplier").select2({
                    allowClear: true,
                    placeholder: "Supplier",
                    ajax: {
                        url: "<?= site_url('purchase/requestforquotation/get_supp') ?>",
                        data: function (params) {
                            var query = {
                                search: params.term
                            }
                            return query;
                        },
                        processResults: function (data) {
                            return {
                                results: data.data
                            };
                        }
                    }
                });
                var uomStock = "0";
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
                                ke: uomStock
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
                    var dpp_tax = $(".tax" + row + " :selected").data().dpp_tax;
                    var tax_lain = $(".tax" + row + " :selected").data().tax_lain_id;
                    $(".dpp_tax_" + row).val(dpp_tax);
                    $(".amount_tax_" + row).val(selectedSelect2OptionSource);
                    $(".tax_lain_id_" + row).val(tax_lain);
                });

                $(".tax").on("change", function () {
                    var row = $(this).attr("data-row");
                    $(".amount_tax_" + row).val("0");
                    $(".dpp_tax_" + row).val("1");
                    $(".tax_lain_id_" + row).val("0");
                });

                $(".uom_beli").on("select2:open", function () {
                    var row = $(this).attr("data-uom");
                    uomStock = row;
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

                $(".harga_satuan").on("input", function () {
                    var row = $(this).attr("data-row");
                    var number = $(".harga_satuan_" + row).val();
                    var formatVal = new Intl.NumberFormat(["ban", "id"], {maximumSignificantDigits: 3}).format(number);
                    $(".note_harga_" + row).html(formatVal);
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
                                if (status === "cancel") {
                                    location.href = "<?= site_url('purchase/requestforquotation') ?>";
                                } else
                                {
                                    location.reload();
                                }
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
                                items: "<?php count($po_items) ?>",
                                totals: 0,
                                default_total: 1
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

                $(".delete_item").off("click").unbind("click").on("click", function () {
                    var ids = $(this).data("ids");
                    confirmRequest("Request For Quotation", "Hapus Item ", function () {
                        $.ajax({
                            url: "<?= base_url('purchase/requestforquotation/delete_item/' . $id) ?>",
                            type: "POST",
                            beforeSend: function (xhr) {
                                please_wait(function () {});
                            },
                            data: {
                                ids: ids,
                                dpplain: $("#dpplain").val()
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

                $(".btn-add_item").on('click', function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text('Add Item');
                    $.post("<?= base_url('purchase/requestforquotation/tambahkan_item/') ?>", {"id": "<?= $id ?>"}, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);
                            $("#btn-tambah").html("Tambahkan");
                        }, 1000);
                    });
                });
            });

        </script>
    </body>
</html>