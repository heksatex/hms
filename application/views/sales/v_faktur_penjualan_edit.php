<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            #btn-cancel,#btn-simpan,#btn-print,#btn-print-pdf,#btn-update-fp {
                display: none;
            }
            .select2-container--focus{
                border:  1px solid #66afe9;
            }

            tfoot td {
                padding: 1px 1px 1px 10px !important;
            }
        </style>
        <?php
        $this->load->view("admin/_partials/head.php");
        if ($datas->status == 'cancel') {
            ?>
            <style>
                #btn-edit ,#btn-confirm{
                    display: none;
                }
            </style>
            <?php
        } else if ($datas->status == 'confirm') {
            ?>
            <style>
                #btn-edit{
                    display: none;
                }
                #btn-print,#btn-print-pdf,#btn-update-fp {
                    display:inline;
                }
            </style>
            <?php
        }
        if (count($detail) < 1) {
            ?>
            <style>
                #btn-confirm{
                    display: none;
                }
            </style>
            <?php
        }
        ?>
        <?php $this->load->view("accounting/_v_style_group_select2.php") ?>
    </head>

    <body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu-new.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar-new.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    <div id ="status_bar">
                        <?php
                        $data['jen_status'] = $datas->status;
                        $this->load->view("admin/_partials/statusbar-new.php", $data);
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">
                        <form class="form-horizontal" method="POST" name="form-faktur-penjualan" id="form-faktur-penjualan" action="<?= base_url("sales/fakturpenjualan/update/{$id}") ?>">
                            <input type="hidden" name="ids" value="<?= $datas->id ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= ($datas->no_faktur_internal === "") ? "" : "No Faktur <strong>{$datas->no_faktur_internal}</strong>" ?></h3>
                                <div class="pull-right text-right" id="btn-header">
                                    <?php
                                    if ($datas->status == 'cancel') {
                                        ?>
                                        <button class="btn btn-primary btn-sm" type="button" id="btn-draft" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                            Simpan Sebagai Draft
                                        </button>
                                    <?php }
                                    ?>
                                    <button class="btn btn-primary btn-sm" type="button" id="btn-print-pdf" data-ids="<?= $id ?>" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-print"></i>&nbsp;Print PDF
                                    </button>
                                    <button class="btn btn-default btn-sm" type="button" id="btn-update-fp" data-ids="<?= $id ?>" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        </i>&nbsp;Update Faktur Pajak
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Tipe Penjualan</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2 tipe edited" name="tipe" id="tipe" style="width: 100%" required disabled>
                                                        <?php
                                                        foreach ($tipe as $key => $value) {
                                                            ?>
                                                            <option value="<?= $key ?>" <?= ($key === $datas->tipe) ? "selected" : '' ?>><?= $value ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">No SJ</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <div class="input-group">
                                                        <input type="text" name="no_sj" id="no_sj" class="form-control input-sm no_sj clear-tipe" value="<?= $datas->no_sj ?>" required readonly/>
                                                        <input type="hidden" name="no_sj_old" id="no_sj_old" class="form-control input-sm no_sj_old" value="<?= $datas->no_sj ?>"/>
                                                        <span class="input-group-addon get-no-sj" title="Cari No SJ"><i class="fa fa-search"><span></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">PO. Cust</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <textarea class="form-control input-sm po_cust clear-tipe edited-read" id="po_cust" name="po_cust" readonly><?= $datas->po_cust ?></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Marketing</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="hidden" class="form-control input-sm marketing_kode clear-tipe" id="marketing_kode" name="marketing_kode" value="<?= $datas->marketing_kode ?>">
                                                    <input type="text" class="form-control input-sm marketing_nama clear-tipe" id="marketing_nama" name="marketing_nama" value="<?= $datas->marketing_nama ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Jenis Ppn</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2 tipe edited" name="jenis_ppn" id="jenis_ppn" style="width: 100%" disabled>
                                                        <?php
                                                        foreach ($jenisppn as $key => $value) {
                                                            ?>
                                                            <option value="<?= $key ?>" <?= ($key === $datas->jenis_ppn) ? "selected" : '' ?>><?= $value ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Tanggal</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <div class="input-group tgl-def-format">
                                                        <input type="text" name="tanggal" id="tanggal" class="form-control input-sm edited-read" value="<?= $datas->tanggal ?>" required readonly/>
                                                        <span class="input-group-addon"><i class="fa fa-calendar"><span></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Customer</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="hidden" class="form-control input-sm customer clear-tipe" id="customer" name="customer" value="<?= $datas->partner_id ?>">
                                                    <input type="text" class="form-control input-sm customer_nama clear-tipe" id="customer_nama" name="customer_nama" value="<?= $datas->partner_nama ?>" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">No Faktur Internal</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="no_faktur_internal" class="form-control input-sm no_faktur_internal edited-read" value="<?= $datas->no_faktur_internal ?>" readonly/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">No Faktur Pajak</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="no_faktur_pajak" id="no_faktur_pajak" class="form-control input-sm no_faktur_pajak edited-read" value="<?= $datas->no_faktur_pajak ?>"
                                                           <?= ($datas->status !== 'confirm') ? 'readonly' : "" ?>/>
                                                </div>
                                            </div>
                                            <?php if ($datas->tipe === "ekspor") { ?>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label">No Inv Ekspor</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input type="text" name="no_inv_ekspor" id="no_inv_ekspor" class="form-control input-sm no_inv_ekspor edited-read" value="<?= $datas->no_inv_ekspor ?>" readonly
                                                               <?= ($datas->status !== 'confirm') ? 'readonly' : "" ?>/>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Kurs</label></div>
                                                <div class="col-xs-4">
                                                    <select name="kurs" id="kurs" class="form-control input-sm kurs edited" required disabled>
                                                        <?php foreach ($curr as $key => $values) {
                                                            ?>
                                                            <option value="<?= $values->id ?>" <?= ($values->id === $datas->kurs) ? "selected" : '' ?>><?= $values->currency ?></option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-xs-4">
                                                    <input type="text" name="kurs_nominal" id="kurs_nominal" value="<?= $datas->kurs_nominal ?>" class="form-control input-sm kurs_nominal edited-read" required readonly/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs " >
                                        <li class="active"><a href="#tab_1" data-toggle="tab">Detail</a></li>
                                        <li><a href="#tab_2" data-toggle="tab">Jurnal</a></li>
                                    </ul>
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab_1">
                                            <div class="table-responsive over">
                                                <table class="table table-condesed table-hover rlstable" id="fpenjualan" style="min-width: 105%; padding: 0 0 0 0 !important;">
                                                    <caption><?php if ($datas->status === 'draft' && $datas->dari_sj === "0") { ?>

                                                            <button type="button" class="btn btn-success btn-sm btn-rmv-item btn-add-item" style="display: none;" title="Tambah Data"><i class="fa fa-plus-circle"></i></button>
                                                        <?php } ?>

                                                        <button type="button" class="btn btn-primary btn-sm btn-rmv-item btn-split" style="display: none;" >Join Item</button>
                                                        <?php if ($datas->dari_sj === "0") { ?>

                                                            <button type="button" class="btn btn-danger btn-sm btn-rmv-item btn-delete-item" style="display: none;" >Delete Item</button>
                                                        <?php } ?>


                                                    </caption>
                                                    <thead>
                                                    <th class="style" style="width: 15px">No. <input type="checkbox" class="btn-rmv-item join-item-check" style="display: none;" data-toggle="tooltip" data-original-title="Check All"></th>
                                                    <th class="style" style="width: 150px">Uraian</th>
                                                    <th class="style" style="width: 100px">Warna</th>
                                                    <th class="style" style="width: 100px">No PO</th>
                                                    <th class="style" style="width: 60px;text-align: right">QTY/LOT</th>
                                                    <th class="style" style="width: 60px;">UOM LOT</th>
                                                    <th class="style text-right" style="width: 60px">QTY</th>
                                                    <th class="style" style="width: 60px">Uom</th>
                                                    <th class="style" style="width: 100px">No ACC</th>
                                                    <th class="style text-right" style="width: 100px">Harga</th>
                                                    <th class="style text-right" style="width: 120px">Jumlah</th>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $no = 0;
                                                        $subTotal = 0;
                                                        foreach ($detail as $key => $value) {
                                                            $no += 1;
                                                            ?>
                                                            <tr class="tr-<?= $value->id ?>">
                                                                <td style="width: 50px">
    <!--                                                                        <div class="col-xs-4"><span class="input-group-addon" style="border:none;"><?= $no ?></span></div>
                                                                        <div class="col-xs-4"><button class="btn btn-warning btn-sm btn-rmv-item" style="display: none;"><i class="fa fa-copy"></i></button></div>
                                                                        <div class="col-xs-4">&nbsp;<input class="btn join-item btn-rmv-item" style="display: none;" type="checkbox"> </div>-->
                                                                    <a><?= $no ?>&nbsp;</a>
                                                                    <a class="btn-rmv-item split-item" style="display: none;" data-toggle="tooltip" style="color: #FFC107;" data-ids="<?= $value->id ?>" data-original-title="Split"><i class="fa fa-copy"></i>&nbsp;</a>
                                                                    <input type="checkbox" class="btn-rmv-item join-item" style="display: none;" data-toggle="tooltip" data-original-title="Join" value="<?= $value->id ?>">
                                                                    <input type="hidden" value="<?= $value->id ?>" name="detail_id[]">
                                                                </td>
                                                                <td>
                                                                    <input class="form-control input-sm  uraian edited-read uraian_<?= $key ?>" value="<?= $value->uraian ?>" name="uraian[]" readonly>
                                                                </td>
                                                                <td>
                                                                    <input class="form-control input-sm  warna edited-read warna_<?= $key ?>" value="<?= $value->warna ?>" name="warna[]" readonly>
                                                                </td>
                                                                <td>
                                                                    <!--<input class="form-control input-sm  no_po edited-read no_po_<?= $key ?>" value="<?= $value->no_po ?>" name="nopo[]" readonly>--> 
                                                                    <textarea class="form-control no_po edited-read no_po_<?= $key ?>"  name="nopo[]" readonly><?= $value->no_po ?></textarea>

                                                                </td>
                                                                <td class="text-right">
                                                                    <input type="text" name="qtylot[]" value="<?= "{$value->qty_lot}" ?>" 
                                                                           class="form-control edited-read input-sm text-right qty-lot qty-lot_<?= $key ?>" readonly/>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control input-sm edited uomlot uomlot_<?= $key ?>" style="width:100%" name="uomlot[]" disabled>
                                                                        <?php
                                                                        foreach ($uomLot as $keys => $uoml) {
                                                                            ?>
                                                                            <option value="<?= $keys ?>" <?= ($keys === $value->lot) ? "selected" : "" ?> ><?= $uoml ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-right">
                                                                    <input type="text" name="qty[]" value="<?= "{$value->qty}" ?>" 
                                                                           class="form-control input-sm edited-read text-right qty qty_<?= $key ?>" readonly/>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control input-sm edited uom uom_<?= $key ?>" style="width:100%" name="uom[]" disabled>
                                                                        <?php
                                                                        foreach ($uom as $keys => $uoms) {
                                                                            ?>
                                                                            <option value="<?= $uoms->short ?>" <?= ($uoms->short === $value->uom) ? "selected" : "" ?> ><?= $uoms->short ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control input-sm select2-coa edited noacc noacc_<?= $key ?>" style="width:100%" name="noacc[]" disabled>
                                                                        <option></option>
                                                                        <?php
                                                                        if ($value->no_acc !== "") {
                                                                            ?>
                                                                            <option value="<?= $value->no_acc ?>" selected><?= $value->no_acc ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' name="harga[]" value="<?= number_format($value->harga, 4, ".", ",") ?>" 
                                                                           class="form-control input-sm text-right edited-read harga harga_<?= $key ?>" readonly/>
                                                                </td>
                                                                <td>
                                                                    <input type="text" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' name="jumlah[]" value="<?= number_format($value->jumlah, 4, ".", ",") ?>" 
                                                                           class="form-control input-sm text-right jumlah jumlah_<?= $key ?>" readonly/>
                                                                </td>
                                                            </tr> 
                                                            <?php
                                                            $subTotal += $value->jumlah;
                                                        }
                                                        ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <?php
                                                        if (count($detail) > 0) {
                                                            ?>
                                                            <tr>
                                                                <td colspan="9"></td>
                                                                <td class="text-right"><strong>Subtotal</strong></td>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format(round($datas->grand_total * $datas->kurs_nominal), 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8"></td>
                                                                <td>

                                                                    <div class="input-group">
                                                                        <span class="input-group-addon">
                                                                            %
                                                                        </span>
                                                                        <input class="form-control input-sm text-right edited-read"  name="nominaldiskon" value="<?= $datas->nominal_diskon ?>" type="text" readonly>
                                                                    </div>
                                                                    <select class="form-control input-sm hide" name="tipediskon">
                                                                        <option value="%" <?= ($datas->tipe_diskon === "%") ? "selected" : "" ?>>%</option>
                                                                    </select>
                                                                </td>
                                                                <td class="text-right"><strong>Diskon</strong></td>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format(round($datas->diskon * $datas->kurs_nominal), 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="9"></td>
                                                                <td class="text-right"><strong>Subtotal 2</strong></td>
                                                                <?php
                                                                $subtotal2 = (round($datas->grand_total * $datas->kurs_nominal) - round($datas->diskon * $datas->kurs_nominal));
                                                                ?>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format($subtotal2, 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="9"></td>
                                                                <td class="text-right"><strong>DPP Nilai Lain</strong></td>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format(round($datas->dpp_lain * $datas->kurs_nominal), 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8"></td>
                                                                <td class="pull-right">

                                                                    <select class="form-control input-sm select2 edited" style="width: 100%" name="tax" id="tax" disabled>
                                                                        <option value=""></option>
                                                                        <?php
                                                                        foreach ($taxs as $k => $tax) {
                                                                            ?>
                                                                            <option data-val="<?= $tax->amount ?>" value="<?= $tax->id ?>" <?= ($tax->id === $datas->tax_id) ? "selected" : "" ?> ><?= $tax->nama ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                    <input type="hidden" value="<?= $datas->tax_value ?>" name="tax_value" id="tax_value">


                                                                </td>
                                                                <td class="text-right"><strong>Ppn</strong></td>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format(round($datas->ppn * $datas->kurs_nominal), 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <tr>
                                                                <td>Foot Note</td>
                                                                <td>
                                                                    <textarea class="form-control footnote edited-read"  name="footnote" readonly><?= $datas->foot_note ?? "" ?></textarea>
                                                                </td>
                                                                <td colspan="5"></td>
                                                                <td class="text-right">
                                                                    *Payment Term
                                                                </td>
                                                                <td class="pull-right">
                                                                    <select class="form-control input-sm edited" style="width: 100%" title="Payment Term" name="payment_term" id="payment_term" disabled>
                                                                        <option value=""></option>
                                                                        <?php
                                                                        foreach ($payment_term as $k => $term) {
                                                                            $term = (object) $term;
                                                                            ?>
                                                                            <option value="<?= $term->kode ?>" <?= ($term->kode == $datas->payment_term) ? "selected" : "" ?> ><?= $term->nama ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td class="text-right"><strong>Total</strong></td>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format(round($datas->final_total * $datas->kurs_nominal), 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        <tr>

                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane" id="tab_2">
                                            <div class="row">
                                                <div class="col-md-6 col-xs-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">No Jurnal</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <strong>:&nbsp;<a href="<?= base_url("accounting/jurnalentries/edit/" . encrypt_url(($jurnal->kode ?? ""))) ?>" target="_blank"><?= $jurnal->kode ?? "" ?></a></strong>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Periode</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <strong>:&nbsp;<?= $jurnal->periode ?? "" ?></strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-xs-12">
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Tanggal</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <strong>:&nbsp;<?= $jurnal ? date("Y-m-d", strtotime($jurnal->tanggal_dibuat)) : "" ?></strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <button type="submit" class="btn btn-default btn-sm btn-save" style="display: none">Simpan </button>
                    </form>

            </div>
        </section>
    </div>
    <footer class="main-footer">
        <?php $this->load->view("admin/_partials/modal.php") ?>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <?php
        if (in_array($user->level, ["Super Administrator"])) {
            $this->load->view("admin/_partials/footer_new.php");
        }
        ?>
    </footer>
</div>
<template>
    <tr>
        <td style="width: 50px">
            <a class="btn-simpan-item" data-toggle="tooltip" style="color: #FFC107;" data-original-title="Simpan"><i class="fa fa-save"></i>&nbsp;&nbsp;</a>
            <a class="btn-cancel-item" data-toggle="tooltip" style="color: red;" data-original-title="Batal"><i class="fa fa-close"></i></a>
        </td>
        <td>
            <input class="form-control input-sm  uraian uraian_:no"  id="uraian">
        </td>
        <td>
            <input class="form-control input-sm  warna warna_:no" id="warna">
        </td>
        <td>
            <input class="form-control input-sm  no_po no_po_:no" id="nopo"> 
        </td>
        <td class="text-right">
            <input type="text" id="qtylot" class="form-control input-sm text-right qty-lot qty-lot_:no" />
        </td>
        <td>
            <select class="form-control input-sm temp-select2 uomlot uomlot_:no" style="width:100%" id="uomlot">
                <?php
                foreach ($uomLot as $keys => $uoml) {
                    ?>
                    <option value="<?= $keys ?>"><?= $uoml ?></option>
                    <?php
                }
                ?>
            </select>
        </td>
        <td class="">
            <input type="text" id="qty"  class="form-control input-sm text-right qty qty_:no">
            <select class="form-control input-sm temp-select2 uom uom_:no" style="width:100%" id="uom">

            </select>
        </td>
        <td>
            <select class="form-control input-sm edited noacc noacc_:no" style="width:100%" id="noacc">

            </select>
        </td>
        <td>
            <input type="text" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' id="harga"
                   class="form-control input-sm text-right edited-read harga harga_:no"/>
        </td>
        <td>

        </td>
    </tr>
</template>
<script>
    var editing = false;
<?php
if ($datas->status == 'confirm') {
    ?>
        $("#btn-confirm").html("Cancel").toggleClass("btn-danger");
    <?php
}
?>
    var no = <?= count($detail) ?>;

    $(document).ready(function () {
        $(".input-sm").keydown(function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                return false;
            }
        });
    });

    const setCoaItem = ((klas = "select2-coa") => {
        $("." + klas).select2({
            placeholder: "Pilih Coa",
            allowClear: true,
            ajax: {
                dataType: 'JSON',
                type: "GET",
                url: "<?php echo base_url(); ?>accounting/kaskeluar/get_coa",
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
                            text: item.nama,
                            children: [{
                                    id: item.kode_coa,
                                    text: item.kode_coa
                                }]
                        });
                    });
                    return {
                        results: results
                    };
                }
            }
        });
    });
    const setUomItem = ((klas = "select2-uom") => {
        $("." + klas).select2({
            placeholder: "Pilih UOM",
            allowClear: true,
            ajax: {
                dataType: 'JSON',
                type: "GET",
                url: "<?php echo base_url(); ?>sales/fakturpenjualan/get_satuan",
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
                            id: item.short,
                            text: item.nama
                        });
                    });
                    return {
                        results: results
                    };
                }
            }
        });
    });
    const setNominalCurrency = (() => {
        $("input[data-type='currency']").on({
            keyup: function () {
                formatCurrency($(this));
            },
            drop: function () {
                formatCurrency($(this));
            },
            blur: function () {
                formatCurrency($(this), "blur");
            }
        });
    });
    const setPterm = (() => {
        $("#payment_term").select2({
            placeholder: "Payment Term",
            allowClear: true
        });
    });
    $(document).on('focus', '.select2', function (e) {
        if (e.originalEvent) {
            var s2element = $(this).siblings('select');
            s2element.select2('open');

            // Set focus back to select2 element on closing.
            s2element.on('select2:closing', function (e) {
                s2element.select2('focus');
            });
        }
    });

    $(function () {
        setNominalCurrency();

        $("#btn-edit").on("click", function (e) {
            e.preventDefault();
            $("#btn-cancel").show();
            $(this).hide();
            $(".edited-read").removeAttr("readonly");
            $(".edited").removeAttr("disabled");
            $(".btn-rmv-item").show();
            $("#btn-confirm").hide();
            $("#btn-simpan").show();
            $(".select2").select2();
            $(".select2").select2({placeholder: "Pilih",
                allowClear: true});
            setCoaItem();
            setPterm();
            editing = true;
        });
        $("#btn-cancel").on("click", function (e) {
            e.preventDefault();
            $("#btn-edit").show();
            $(this).hide();
            $(".edited-read").attr("readonly", "readonly");
            $(".edited").attr("disabled", "disabled");
            $(".btn-rmv-item").hide();
            $("#btn-confirm").show();
            $("#btn-simpan").hide();
            editing = false;
        });
        $("#btn-simpan").on("click", function (e) {
            e.preventDefault();
            $(".btn-save").trigger("click");
        });

        $("#btn-confirm").on("click", function (e) {
            e.preventDefault();
            var text = $(this).html();
            var statuss = text.replace(/(<([^>]+)>)/ig, "");
            confirmRequest("Faktur Penjualan", statuss + " Data ? ", function () {
                updateStatus(statuss);
            });
        });

        $("#btn-draft").unbind("click").off("click").on("click", function (e) {
            e.preventDefault();
            confirmRequest("Faktur Penjualan", "Simpan Kembali Sebagai Draft ? ", (() => {
                updateStatus("draft");
            }));
        });

        const updateStatus = ((status) => {
            $.ajax({
                url: "<?= base_url('sales/fakturpenjualan/update_status/' . $id) ?>",
                type: "POST",
                data: {status: status.trim()},
                beforeSend: function (xhr) {
                    please_wait(function () {});
                },
                error: function (req, error) {
                    unblockUI(function () {
                        setTimeout(function () {
                            alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                        }, 500);
                    });
                }, success: function (data) {
                    location.reload();
                },
                complete: function (jqXHR, textStatus) {
                    unblockUI(function () {}, 200);
                }
            });
        })

        const formdo = document.forms.namedItem("form-faktur-penjualan");
        formdo.addEventListener(
                "submit",
                (event) => {
            please_wait(function () {});
            request("form-faktur-penjualan").then(
                    response => {
                        unblockUI(function () {
                            alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                        }, 100);
                        if (response.status === 200)
                            window.location.reload(true);
                    }
            );
            event.preventDefault();
        },
                false
                );

        $("#tax").on("select2:select", function () {
            var selectedSelect2OptionSource = $(this).find(':selected').data('val');
            $("#tax_value").val(selectedSelect2OptionSource);
        });
        $("#tax").on("change", function () {
            $("#tax_value").val("0.0000");
        });

        $("#btn-update-fp").unbind("click").off("click").on("click", function () {
            $.ajax({
                url: "<?= base_url('sales/fakturpenjualan/update_faktur/' . $id) ?>",
                type: "POST",
                data: {
                    pajak: $("#no_faktur_pajak").val()
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
                complete: function (jqXHR, textStatus) {
                    unblockUI(function () {}, 200);
                }, success: function (data) {
                    location.reload();
                }
            });
        });

        $(".split-item").unbind("click").off("click").on("click", function () {
            $(".btn-rmv-item").hide();
            $("#btn-simpan").hide();
            $("#btn-cancel").hide();
            const tt = $(this);
            var ids = $(this).data("ids");
            $.ajax({
                url: "<?= base_url('sales/fakturpenjualan/split/' . $id) ?>",
                type: "POST",
                data: {ids: ids},
                beforeSend: function (xhr) {
                    please_wait(function () {});
                },
                error: function (req, error) {
                    unblockUI(function () {
                        setTimeout(function () {
                            alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                        }, 500);
                    });
                }, success: function (data) {
                    var html = data.data;
                    var newRow = $(html);
                    var counterSplit = $("#fpenjualan tbody tr").length - 2;
                    newRow.insertAfter(tt.parents().closest('tr'));
                    counterSplit++;
                    $(".split-item").hide();
                    setCoaItem();
                },
                complete: function (jqXHR, textStatus) {
                    unblockUI(function () {}, 200);
                }
            });

        });

        $(".btn-delete-item").unbind("click").off("click").on("click", function () {
            var val = [];
            $(".join-item:checked").each(function (i) {
                val[i] = $(this).val();
            });
            if (val.length < 1)
                return;

            confirmRequest("Faktur Penjualan", "Delete Item Dipilih ? ", function () {
                $.ajax({
                    url: "<?= base_url('sales/fakturpenjualan/delete_item/' . $id) ?>",
                    type: "POST",
                    data: {ids: val.join()},
                    beforeSend: function (xhr) {
                        please_wait(function () {});
                    },
                    error: function (req, error) {
                        unblockUI(function () {
                            setTimeout(function () {
                                alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                            }, 500);
                        });
                    }, success: function (data) {
                        location.reload();
                    },
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });
            });
        });

        $(".btn-split").unbind("click").off("click").on("click", function () {
            var val = [];
            var trIndex = [];
            $(".join-item:checked").each(function (i) {
                val[i] = $(this).val();
                trIndex.push($("#fpenjualan tbody tr").index($(this).closest('tr')));
            });
            if (val.length < 2)
                return;

            trIndex.sort((a, b) => b - a);
            confirmRequest("Faktur Penjualan", "Join Item Dipilih ? ", function () {
                $.ajax({
                    url: "<?= base_url('sales/fakturpenjualan/join/' . $id) ?>",
                    type: "POST",
                    data: {ids: val.join()},
                    beforeSend: function (xhr) {
                        please_wait(function () {});
                    },
                    error: function (req, error) {
                        unblockUI(function () {
                            setTimeout(function () {
                                alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                            }, 500);
                        });
                    }, success: function (data) {
                        $('#fpenjualan tbody tr:last').after(data.data);
                        $.each(trIndex, function (index, value) {
                            $('#fpenjualan tbody tr').eq(value).remove();
                        });

                    },
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });

            });
        });

        $(".get-no-sj").on("click", function (e) {
            e.preventDefault();
            if (!editing)
                return;
            $("#tambah_data").modal({
                show: true,
                backdrop: 'static'
            });
            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $('.modal-title').text("List SJ");
            $("#btn-tambah").html("Pilih");
            var tipee = $("#tipe").val();
            $.post("<?= base_url('sales/fakturpenjualan/get_view_sj') ?>", {tipe: tipee}, function (data) {
                setTimeout(function () {
                    $(".tambah_data").html(data.data);
                    $("#btn-tambah").hide();

                }, 1000);
            });
        });

        $(".btn-add-item").on("click", function (e) {
            e.preventDefault();
            $(this).hide();
            no += 1;
            var tmplt = $("template");
            var isi_tmplt = tmplt.html().replace(/:no/g, no);
            $("#fpenjualan tbody").append(isi_tmplt);
            $(".temp-select2").select2({
                placeholder: "Pilih",
                allowClear: true
            });
            setCoaItem("noacc_" + no);
            setNominalCurrency();
            setUomItem("uom_" + no);

            $(".btn-cancel-item").on("click", function (e) {
                $(this).closest("tr").remove();
                $(".btn-add-item").show();
            });

            $(".btn-simpan-item").off("click").unbind("click").on("click", function (e) {
                confirmRequest("Faktur Penjualan", "Simpan Item Baru ? ", function () {
                    $.ajax({
                        url: "<?= base_url('sales/fakturpenjualan/save_item/' . $id) ?>",
                        type: "POST",
                        data: {
                            uraian: $("#uraian").val(),
                            warna: $("#warna").val(),
                            no_po: $("#nopo").val(),
                            qty_lot: $("#qtylot").val(),
                            uom_lot: $("#uomlot :selected").val(),
                            uom: $("#uom").val(),
                            qty: $("#qty").val(),
                            no_acc: $("#noacc :selected").val(),
                            harga: $("#harga").val()
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
                        }, success: function (data) {
                            location.reload();
                        },
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {});
                        }
                    });
                });
            });

        });

        $("#btn-print").on("click", function (e) {
            e.preventDefault();
            $.ajax({
                url: "<?= base_url('sales/fakturpenjualan/print') ?>",
                type: "POST",
                data: {
                    no: "<?= $id ?>"
                },
                beforeSend: function (xhr) {
                    please_wait(function () {});
                },
                success: function (data) {
                    alert_notify(data.icon, data.message, data.type, function () {}, 500);
                },
                complete: function (jqXHR, textStatus) {
                    unblockUI(function () {});

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    alert_notify("fa fa-warning", jqXHR?.responseJSON?.message, "danger", function () {}, 500);
                }
            });
        });

        $("#btn-print-pdf").off("click").unbind("click").on("click", function () {
            $.ajax({
                url: "<?= base_url('sales/fakturpenjualan/print_pdf/') ?>",
                type: "POST",
                data: {
                    id: "<?= $id ?>"
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

        $(".join-item-check").click(function () {
            $('.join-item').not(this).prop('checked', this.checked);
        });

    });
    var counterSplit = 0;
    const cancelSplit = ((e) => {
        $(".split-item").show();
        counterSplit--;
        $(e).closest("tr").remove();
        $(".btn-rmv-item").show();
        $("#btn-simpan").show();
        $("#btn-cancel").show();
    });
    const saveSplit = ((e) => {
        var trIndex = $("#fpenjualan tbody tr").index($(e).closest('tr'));
        confirmRequest("Faktur Penjualan", "Simpan Split Item? ", function () {
            var ids = $("#ids").val();
            $.ajax({
                url: "<?= base_url('sales/fakturpenjualan/save_split/' . $id) ?>",
                type: "POST",
                data: {
                    ids: ids,
                    qty: $("#qty_split").val(),
                    qty_lot: $("#qty_lot_split").val(),
                    no_acc: $("#no_acc_split").val(),
                    uom_lot: $("#uom_lot_split").val()
                },
                beforeSend: function (xhr) {
//                    cancelSplit(e);
                    please_wait(function () {});
                },
                error: function (req, error) {
                    unblockUI(function () {
                        setTimeout(function () {
                            alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                        }, 500);
                    });
                }, success: function (data) {
                    $('#fpenjualan tbody tr').eq(trIndex - 1).after(data.data);
                    $('#fpenjualan tbody tr').eq(trIndex - 1).remove();
                    cancelSplit(e);

                },
                complete: function (jqXHR, textStatus) {
                    unblockUI(function () {});
                }
            });
        });
    });

    const addTotable = ((nosj) => {
        $.ajax({
            url: "<?= base_url('sales/fakturpenjualan/addsj') ?>",
            type: "POST",
            data: {
                no: nosj
            },
            beforeSend: function (xhr) {
                please_wait(function () {});
            },
            success: function (data) {
                $("#po_cust").val(data.data.keterangan);
                $("#no_sj").val(nosj);
                $("#marketing_kode").val(data.data.sales_kode);
                $("#marketing_nama").val(data.data.sales_nama);
                $("#customer").val(data.data.customer_id);
                $("#customer_nama").val(data.data.customer);
                $("#tanggal").val(data.data.tanggal_dokumen);

            },
            complete: function (jqXHR, textStatus) {
                unblockUI(function () {
                    $("#fpenjualan tbody").remove();
                    $("#fpenjualan tfoot").remove();
                }, 100);
            },
            error: function (jqXHR, textStatus, errorThrown) {
                unblockUI(function () {
                    alert_notify("fa fa-warning", jqXHR?.responseJSON?.message, "danger", function () {}, 500);
                }, 100);

            }
        });
    });

</script>
</body>
</html>