<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            #btn-cancel,#btn-simpan,#btn-print {
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
                #btn-print {
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
                        <form class="form-horizontal" method="POST" name="form-faktur-penjualan" id="form-faktur-penjualan" action="<?= base_url("accounting/fakturpenjualan/update/{$id}") ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= ($datas->no_faktur === "") ? "" : "No Faktur {$datas->no_faktur}" ?></h3>
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
                                                <div class="col-xs-4"><label class="form-label">No Faktur</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="no_faktur" class="form-control input-sm no_faktur edited-read" value="<?= $datas->no_faktur ?>" readonly/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">No Faktur Pajak</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="no_faktur_pajak" id="no_faktur_pajak" class="form-control input-sm no_faktur_pajak edited-read" <?= $datas->no_faktur_pajak ?> readonly/>
                                                </div>
                                            </div>
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
                                                    <caption>
                                                        <button type="button" class="btn btn-default btn-sm btn-rmv-item btn-split"style="display: none;" >Join Item</button>
                                                    </caption>
                                                    <thead>
                                                    <th class="style" style="width: 15px">No.</th>
                                                    <th class="style" style="width: 150px">Uraian</th>
                                                    <th class="style" style="width: 100px">Warna</th>
                                                    <th class="style" style="width: 100px">No PO</th>
                                                    <th class="style" style="width: 60px;text-align: right">QTY/LOT</th>
                                                    <th class="style" style="width: 60px;">UOM LOT</th>
                                                    <th class="style text-right" style="width: 75px">QTY</th>
                                                    <th class="style" style="width: 100px">No ACC</th>
                                                    <th class="style text-right" style="width: 120px">Harga</th>
                                                    <th class="style text-right" style="width: 120px">Jumlah</th>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $no = 0;
                                                        $subTotal = 0;
                                                        foreach ($detail as $key => $value) {
                                                            $no += 1;
                                                            ?>
                                                            <tr>
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
                                                                    <input class="form-control input-sm  no_po edited-read no_po_<?= $key ?>" value="<?= $value->no_po ?>" name="nopo[]" readonly> 
                                                                </td>
                                                                <td class="text-right">
                                                                    <input type="text" name="qtylot[]" value="<?= "{$value->qty_lot}" ?>" 
                                                                           class="form-control input-sm text-right qty-lot qty-lot_<?= $key ?>" readonly/>
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
                                                                    <input value="<?= $value->qty ?>" type="hidden" name="qty[]">
                                                                    <?= "{$value->qty} {$value->uom}" ?>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control input-sm select2-coa edited noacc noacc_<?= $key ?>" style="width:100%" name="noacc[]" disabled>
                                                                        <option></option>
                                                                        <?php
                                                                        if ($value->no_acc !== "") {
                                                                            ?>
                                                                            <option value="<?= $value->no_acc ?>" selected><?= $value->coa_nama ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' name="harga[]" value="<?= number_format($value->harga, 2, ".", ",") ?>" 
                                                                           class="form-control input-sm text-right edited-read harga harga_<?= $key ?>" readonly/>
                                                                </td>
                                                                <td>
                                                                    <input type="text" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' name="jumlah[]" value="<?= number_format($value->jumlah, 2, ".", ",") ?>" 
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
                                                                <td colspan="8"></td>
                                                                <td class="text-right"><strong>Subtotal</strong></td>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format($subTotal, 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7"></td>
                                                                <td colspan="2">
                                                                    <div class="col-xs-4">
                                                                        <select class="form-control input-sm select2 pull-right edited" name="tipediskon" disabled>
                                                                            <option value=""></option>
                                                                            <option value="%" <?= ($datas->tipe_diskon === "%") ? "selected" : "" ?>>%</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-xs-4">
                                                                        <input class="form-control input-sm text-right edited-read" name="nominaldiskon" value="<?= $datas->nominal_diskon ?>" type="text" readonly>
                                                                    </div>
                                                                    <div class="col-xs-4 text-right">
                                                                        <span class=""><strong>Diskon</strong></span>
                                                                    </div>

                                                                </td>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format($datas->diskon, 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8"></td>
                                                                <td class="text-right"><strong>DPP Nilai Lain</strong></td>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format($datas->dpp_lain, 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="7"></td>
                                                                <td colspan="2" class="text-right">

                                                                    <div class="col-xs-6">
                                                                        <select class="form-control input-sm select2 pull-right edited" name="tax" id="tax" disabled>
                                                                            <option value=""></option>
                                                                            <?php
                                                                            foreach ($taxs as $k => $tax) {
                                                                                ?>
                                                                                <option data-val="<?= $tax->amount ?>" value="<?= $tax->id ?>" <?= ($tax->id === $datas->tax_id) ? "selected" : "" ?> ><?= $tax->nama ?></option>
                                                                                <?php
                                                                            }
                                                                            ?>
                                                                        </select>
                                                                    </div>
                                                                    <div class="col-xs-6">
                                                                        <input type="hidden" value="<?= $datas->tax_value ?>" name="tax_value" id="tax_value">
                                                                        <span class=""><strong>Ppn</strong></span>
                                                                    </div>

                                                                </td>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format($datas->ppn, 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="8"></td>
                                                                <td class="text-right"><strong>Total</strong></td>
                                                                <td><input readonly class="form-control input-sm text-right" value="<?= number_format(($datas->grand_total - $datas->diskon), 2, ".", ",") ?>"></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
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
                                                                <!--<strong>:&nbsp;<?= $jurnal ? date("Y-m-d", strtotime($jurnal->tanggal_dibuat)) : "" ?></strong>-->
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
<script>
<?php
if ($datas->status == 'confirm') {
    ?>
        $("#btn-confirm").html("Cancel").toggleClass("btn-danger");
    <?php
}
?>
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
    $(function () {
        setNominalCurrency();
        $(".select2").select2();
        $("#btn-edit").on("click", function (e) {
            e.preventDefault();
            $("#btn-cancel").show();
            $(this).hide();
            $(".edited-read").removeAttr("readonly");
            $(".edited").removeAttr("disabled");
            $(".btn-rmv-item").show();
            $("#btn-confirm").hide();
            $("#btn-simpan").show();
            $(".select2").select2({placeholder: "Pilih",
                allowClear: true});
            setCoaItem();
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
        });
        $("#btn-simpan").on("click", function (e) {
            e.preventDefault();
            $(".btn-save").trigger("click");
        });

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



        $(".split-item").unbind("click").off("click").on("click", function () {
            $(".btn-rmv-item").hide();
            $("#btn-simpan").hide();
            $("#btn-cancel").hide();
            const tt = $(this);
            var ids = $(this).data("ids");
            $.ajax({
                url: "<?= base_url('accounting/fakturpenjualan/split/' . $id) ?>",
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

        $(".btn-split").unbind("click").off("click").on("click", function () {
            var val = [];
            $(".join-item:checked").each(function (i) {
                val[i] = $(this).val();
            });
            if (val.length < 1)
                return;

            confirmRequest("Faktur Penjualan", "Join Item Dipilih ? ", function () {

                $.ajax({
                    url: "<?= base_url('accounting/fakturpenjualan/join/' . $id) ?>",
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
    const saveSplit = (() => {
        confirmRequest("Faktur Penjualan", "Simpan Split Item? ", function () {
            $.ajax({
                url: "<?= base_url('accounting/fakturpenjualan/save_split/' . $id) ?>",
                type: "POST",
                data: {
                    ids: $("#ids").val(),
                    qty: $("#qty_split").val(),
                    qty_lot: $("#qty_lot_split").val(),
                    no_acc: $("#no_acc_split").val(),
                    uom_lot: $("#uom_lot_split").val()
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