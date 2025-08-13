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
            /*            .td-deskripsi{
                            width: 7% !important;
                        }*/


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
                            <h3 class="box-title"><strong> <?= $inv->no_invoice ?? "" ?> </strong></h3>
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
                                                    <input class="form-control" value="<?= $inv->supplier ?>" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Origin</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                  <input class="form-control" value="<?= $inv->origin ?>" disabled>
                                                    <input type="hidden" class="form-control pull-right input-sm" name="origin" value="<?= $inv->origin ?>" readonly> 
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tgl dibuat</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input class="form-control" value="<?= date("Y-m-d H:i:s", strtotime($inv->created_at)) ?>" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">No PO</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input class="form-control" value="<?= $inv->no_po ?>" disabled>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">Periode ACC</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <?php
                                                    if ($inv->status !== "draft") {
                                                        ?>
                                                        <span><?= $inv->periode ?></span>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <select class="form-control select2" name="periode" id="periode">
                                                            <?php
                                                            $periodeNow = $inv->periode ?? date("Y/m", strtotime($inv->created_at));
                                                            $selected = "selected";

                                                            foreach ($periode as $kk => $val) {
                                                                ?>
                                                                <option value="<?= $val->periode ?>" <?= ($periodeNow === $val->periode) ? "selected" : "" ?> ><?= $val->periode ?></option>
                                                                <?php
                                                            }
                                                            ?>

                                                        </select>
                                                        <?php
                                                    }
                                                    ?>

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
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tgl Invoice Supplier</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="datetime-local" class="form-control pull-right input-sm" name="tanggal_invoice_supp" value="<?= $inv->tanggal_invoice_supp ?>" <?= ($inv->status === 'draft') ? '' : "readonly" ?>> 
                                                </div>
                                                <button type="submit" id="form-inv-submit" style="display: none"></button>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">No SJ Supplier</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="text" class="form-control pull-right input-sm" name="no_sj_supp" value="<?= $inv->no_sj_supp ?>" <?= ($inv->status === 'draft') ? '' : "readonly" ?>> 
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tgl SJ</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="datetime-local" class="form-control pull-right input-sm" name="tanggal_sj" value="<?= $inv->tanggal_sj ?>" <?= ($inv->status === 'draft') ? '' : "readonly" ?>> 
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">Kurs</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="text" class="form-control pull-right input-sm" name="nilai_matauang" value="<?= number_format($inv->nilai_matauang, 0, '', '') ?>" <?= ($inv->status === 'draft') ? '' : "readonly" ?>>
                                                </div>
                                            </div>
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
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="col-xs-12">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab_1" data-toggle="tab">Item</a></li>
                                        <!--<li><a href="#tab_2" data-toggle="tab">Origin</a></li>-->
                                    </ul>
                                    <div class="tab-content"><br>
                                        <!--<div class="tab-pane" id="tab_2"></div>-->
                                        <div class="tab-pane active" id="tab_1">
                                            <div class="table-responsive over">
                                                <table id="tbl-inv" class="table table-condesed table-hover rlstable  over" style="min-width: 150%">
                                                    <thead>
                                                    <th class="no">#</th>
                                                    <th class="no">No</th>
                                                    <th>Produk</th>
                                                    <!--<th>Deskripsi</th>-->
                                                    <th>Reff Note</th>
                                                    <th>Account</th>
                                                    <th>Qty Beli</th>
                                                    <th>UOM</th>
                                                    <th>Harga Satuan</th>
                                                    <th>Tax</th>
                                                    <th>Diskon</th>
                                                    <!--<th>#</th>-->
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        if (count($invDetail) > 0) {
                                                            $getTax = new $this->m_global;
                                                            $getTax->setTables("tax");
                                                            $dataPajak = [];
                                                            $subtotal1 = 0;
                                                            $totalDiskon = 0;
                                                            $totalTax = 0;
                                                            $pajakLain = [];
                                                            foreach ($invDetail as $key => $value) {
                                                                $taxe = 0;
                                                                $base = 0;
                                                                $jumlah = $value->harga_satuan * $value->qty_beli;
                                                                $subtotal1 += $jumlah;
                                                                $totalDiskon += $value->diskon;
                                                                if ($setting !== null && $value->dpp_tax === "1") {
                                                                    $base = ((($jumlah - $value->diskon) * 11) / 12);
                                                                    $taxe += $base * $value->amount_tax;
                                                                } else {
                                                                    $base = ($jumlah - $value->diskon);
                                                                    $taxe += $base * $value->amount_tax;
                                                                }

                                                                if ($value->tax_id !== "0") {

                                                                    if (isset($dataPajak[$value->pajak_ket])) {
                                                                        $dataPajak[$value->pajak_ket]["base"] += $base;
                                                                        $dataPajak[$value->pajak_ket]["nominal"] += $taxe;
                                                                    } else {
                                                                        $dataPajak[$value->pajak_ket] = [
                                                                            "nama" => $value->pajak,
                                                                            "ket" => $value->pajak_ket,
                                                                            "base" => $base,
                                                                            "nominal" => $taxe
                                                                        ];
                                                                    }
                                                                    if ($value->tax_lain_id !== "0") {
                                                                        $dataTax = $getTax->setWhereIn("id", explode(",", $value->tax_lain_id), true)->setOrder(["id"])->getData();
                                                                        foreach ($dataTax as $kkk => $datass) {
                                                                            $taxx = 0;
                                                                            $bases = 0;
                                                                            if ($setting !== null && $datass->dpp === "1") {
                                                                                $bases = ((($jumlah - $value->diskon) * 11) / 12);
                                                                                $taxx += $bases * $datass->amount;
                                                                            } else {
                                                                                $bases = ($jumlah - $value->diskon);
                                                                                $taxx += $bases * $datass->amount;
                                                                            }
                                                                            $taxe += $taxx;
                                                                            if (isset($dataPajak[$datass->ket])) {
                                                                                $dataPajak[$datass->ket]["base"] += $bases;
                                                                                $dataPajak[$datass->ket]["nominal"] += $taxx;
                                                                            } else {
                                                                                $dataPajak[$datass->ket] = [
                                                                                    "nama" => $datass->nama,
                                                                                    "ket" => $datass->ket,
                                                                                    "base" => $bases,
                                                                                    "nominal" => $taxx
                                                                                ];
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                                $totalTax += $taxe;
                                                                ?>
                                                                <tr>
                                                                    <td class="no">
                                                                        <?php
                                                                        if ($inv->status === 'draft') {
                                                                            ?>
                                                                            <a class="split-item" data-ids="<?= $value->id ?>" data-produk="<?= $value->kode_produk . " - " . $value->nama_produk ?>"><i class="fa fa-copy">&nbsp</i>Split</a>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </td>
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
                                                                            <input type="hidden" class="tax_lain_id_<?= $key ?>" name="tax_lain_id[<?= $value->id ?>]" value="<?= $value->tax_lain_id ?>">
                                                                            <input type="hidden" class="form-control" name="tax[<?= $value->id ?>]" value="<?= $value->tax_id ?>">
                                                                            <select style="width: 90%" class="form-control tax tax<?= $key ?> input-xs"  data-row="<?= $key ?>" 
                                                                                    name="tax_[<?= $value->id ?>]"  disabled>
                                                                                <option></option>
                                                                                <?php
                                                                                foreach ($taxss as $key => $taxs) {
                                                                                    ?>
                                                                                    <option value='<?= $taxs->id ?>' data-tax_lain_id="<?= $taxs->tax_lain_id ?>" data-nilai_tax="<?= $taxs->amount ?>" <?= ($taxs->id === $value->tax_id) ? 'selected' : '' ?>><?= $taxs->nama ?></option>
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
                                                                    <?php
                                                                    $noo = 0;
                                                                    if ($totalTax > 0) {
                                                                        foreach ($dataPajak as $k => $v) {
                                                                            $v = (object) $v;
                                                                            $noo++;
                                                                            ?>
                                                                            <tr>
                                                                                <td>
                                                                                    <?= $noo ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?= $v->ket ?>
                                                                                </td>
                                                                                <td>

                                                                                </td>
                                                                                <td>
                                                                                    <?= $inv->symbol ?> <?= number_format(($v->base), 4) ?>
                                                                                </td>
                                                                                <td>
                                                                                    <?= $inv->symbol ?> <?= number_format(($v->nominal), 4) ?>
                                                                                </td>
                                                                            </tr>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>

                                                                <?php }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <div class="col-xs-12 col-md-4">
                                                        <table class="table table-condesed table-hover rlstable  over">
                                                            <tr>
                                                                <td class="text-right"><strong>Subtotal 1</strong></td>
                                                                <td><?= $inv->symbol ?> <?= number_format(($subtotal1), 4) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-right">Diskon</td>
                                                                <td><?= $inv->symbol ?> <?= number_format(($totalDiskon), 4) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-right"><strong>Subtotal 2</strong></td>
                                                                <td><?= $inv->symbol ?> <?= number_format(($subtotal2), 4) ?></td>
                                                            </tr>
                                                            <?php if ($setting !== null) {
                                                                ?>
                                                                <tr>    
                                                                    <td class="style text-right">DPP Nilai Lain</td>
                                                                    <td class="style totalan"> 
                                                                        <input name="dpplain" type="hidden" value="1">
                                                                        <strong><?= $inv->symbol ?> <?= number_format(((($subtotal1 - $totalDiskon) * 11) / 12), 4) ?>
                                                                        </strong>
                                                                    </td>
                                                                </tr>
                                                            <?php }
                                                            ?>
                                                            <tr>
                                                                <td class="text-right">Tax</td>
                                                                <td><?= $inv->symbol ?> <?= number_format(($totalTax), 4) ?></td>
                                                            </tr>
                                                            <tr>
                                                                <td class="text-right"><strong>Total</strong></td>
                                                                <td><?= $inv->symbol ?> <?= number_format(($subtotal2 + $totalTax), 4) ?></td>
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
                    $this->load->view("admin/_partials/footer_new.php", $data);
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

            var counterSplit = 0;
            $(function () {

                $(".select2").select2({
                    allowClear: true,
                    placeholder: "Pilih"
                });
                var editable = false;

                $(".add_duplicate").off("click").on("click", function () {
                    if (editable) {
                        return;
                    }
                    var ids = $(this).data("id");
                    $(".qty_" + ids).removeAttr("readonly");
                    editable = true;
                    $.post("<?= site_url("purchase/invoice/duplicate"); ?>", {"ids": ids}, function () {

                    });
                });

                $(".tax").select2({
                    allowClear: true,
                    placeholder: "Pajak",

                });

                $(".tax").on("select2:select", function () {
                    var row = $(this).attr("data-row");
                    var selectedSelect2OptionSource = $(".tax" + row + " :selected").data().nilai_tax;
                    var tax_lain = $(".tax" + row + " :selected").data().tax_lain_id;
                    $(".amount_tax_" + row).val(selectedSelect2OptionSource);
                    $(".tax_lain_id_" + row).val(tax_lain);
                });

                $(".tax").on("change", function () {
                    var row = $(this).attr("data-row");
                    $(".amount_tax_" + row).val("0");
                    $(".tax_lain_id_" + row).val("0");
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
                            //alert('Error data');
                            //alert(xhr.responseText);
                        }
                    }
                });

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
                    confirmRequest("Invoice", "Approve Purchase Invoice ? ", function () {
                        please_wait(function () {});
                        $.ajax({
                            url: "<?= base_url('purchase/invoice/update_status/') ?>",
                            type: "POST",
                            data: {
                                id: "<?= $id ?>",
                                status: "done",
                                jurnal: "<?= $inv->journal ?>",
                                inv: "<?= $inv->no_invoice ?>",
                                origin: "<?= $inv->origin ?>",
                                periode: "<?= $inv->periode ?>"
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
                    confirmRequest("Invoice", "Cancel Purchase Invoice ? ", function () {
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

                $(".split-item").unbind("click").off("click").on("click", function () {
                    const tt = $(this);
                    const data = $(this).data();
                    $.ajax({
                        url: "<?= base_url('purchase/invoice/split/' . $id) ?>",
                        type: "POST",
                        data: data,
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
                            var counterSplit = $("#tbl-inv tbody tr").length - 2;
                            newRow.insertAfter(tt.parents().closest('tr'));
                            counterSplit++;
                            $(".split-item").hide();

                        },
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {}, 200);
                        }
                    });

                });


            });

            const cancelSplit = ((e) => {
                $(".split-item").show();
                counterSplit--;
                $(e).closest("tr").remove();
            });
            const saveSplit = (() => {
                confirmRequest("Invoice", "Simpan Split Produk? ", function () {
                    $.ajax({
                        url: "<?= base_url('purchase/invoice/save_split/' . $id) ?>",
                        type: "POST",
                        data: {
                            ids: $("#ids").val(),
                            qty: $("#qty_dup").val()
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
                })
            });
        </script>
    </body>
</html>