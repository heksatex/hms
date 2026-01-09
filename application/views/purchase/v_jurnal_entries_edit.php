<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link href="<?= base_url('dist/css/popup_img.css') ?>" rel="stylesheet">
        <style>
            #btn-cancel{
                display: none;
            }
            <?php if ($jurnal->status === "posted" || $jurnal->status === "cancel") {
                ?>
                #btn-simpan{
                    display: none;
                }
                <?php
            }
            if ($jurnal->status === "cancel") {
                ?>
                .btn-sm{
                    display: none;
                }
                <?php
            }
            if ($jurnal->status === "posted" && $jurnal->origin === "") {
                ?>

                #btn-cancel{
                    display: inline;
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
                $listJurnal = ["PB" => "Pembelian"];
                ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header" >
                    <div id ="status_bar">
                        <?php
                        $data['jen_status'] = $jurnal->status;
                        $this->load->view("admin/_partials/statusbar.php", $data);
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><strong> <?= $jurnal->kode ?? "" ?> </strong></h3>
                            <div class="pull-right text-right" id="btn-header">
                                <?php if ($jurnal->status === "unposted" && count($detail) > 0) { ?>
                                    <button class="btn btn-success btn-sm" id="btn-update-status" data-status="posted"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-check">&nbsp;Post</i>
                                    </button>
                                    <?php
                                }
                                if ($jurnal->status === "posted") {
                                    ?>
                                    <button class="btn btn-success btn-sm" id="btn-print" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-print">&nbsp;Print</i>
                                    </button>
                                    <?php
                                }
                                if ($jurnal->status === "unposted" && $jurnal->origin === "") {
                                    ?>
                                    <button class="btn btn-primary btn-sm" id="btn-import" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-file">&nbsp;Import </i>
                                    </button>
                                    <button class="btn btn-success btn-sm" id="btn-download-temp" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-download">&nbsp;Template </i>
                                    </button>

                                    <?php
                                }
                                ?>
                            </div>
                        </div>
                        <form  class="form-horizontal" method="POST" name="form-jurnal" id="form-jurnal" action="<?= base_url('purchase/jurnalentries/update/' . $id) ?>">
                            <button type="submit" style="display: none;" id="form-jurnal-submit"></button>
                            <div class="box-body">
                                <div class="col-xs-12">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Jurnal</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $jurnal->nama_jurnal ?? "" ?></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tanggal Dibuat</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $jurnal->tanggal_dibuat ?></span>
                                                </div>
                                            </div>
                                            <?php if ($jurnal->origin !== "") { ?>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label required">Periode</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8 text-uppercase">
                                                        <select class="form-control input-sm periode" name="periode" style="width: 100%" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?> required>
                                                            <option value="<?= $jurnal->periode ?>" selected><?= $jurnal->periode ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xs-12">

                                        <div class="form-group">
                                            <?php if ($jurnal->origin !== "") { ?>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">Origin</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8 text-uppercase">
                                                        <input type="text" value="<?= $jurnal->origin ?>" class="form-control input-sm" name="origin" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Reff Note</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <textarea class="form-control" id="reff_note" name="reff_note" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>><?= $jurnal->reff_note ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="colxs-12">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab_1" data-toggle="tab">Item</a></li>
                                        <!--<li><a href="#tab_2" data-toggle="tab">RFQ & BID</a></li>-->
                                    </ul>
                                    <div class="tab-content"><br>
                                        <div class="tab-pane active" id="tab_1">
                                            <div class="table-responsive over">
                                                <table id="tbl-jurnal" class="table">
                                                    <thead>
                                                        <tr>
                                                            <th class="no" style="width: 20px;">#</th>
                                                            <th style="width: 150px;">Nama</th>
                                                            <th style="width: 150px;">Reff Note</th>
                                                            <th style="width: 150px;">Partner</th>
                                                            <th style="width: 100px;">Account</th>
                                                            <th style="width: 120px;">Debit</th>
                                                            <th style="width: 120px;">Credit</th>
                                                            <th style="width: 100px;">Kurs</th>
                                                            <th style="width: 90px;">#</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        $totalDebit = 0;
                                                        $totalKredit = 0;
                                                        foreach ($detail as $keys => $value) {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon" style="border:none;"><?= ($keys + 1) ?></span>
                                                                        <?php if ($jurnal->status === "unposted") { ?>
                                                                            <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                                                                        <?php } ?>
                                                                    </div>
                                                                </td>
                                                                <td><input type="text" class="form-control input-sm nama" value="<?= $value->nama ?>" name="nama[]" <?= ($jurnal->status === 'unposted') ? '' : 'readonly' ?>></td>
                                                                <td><input type="text" class="form-control input-sm reffnote_item" value="<?= $value->reff_note ?>" name="reffnote_item[]" <?= ($jurnal->status === 'unposted') ? '' : 'readonly' ?>></td>
                                                                <td><?php
                                                                    if ($jurnal->status === 'unposted') {
                                                                        ?>
                                                                        <div class="form-group">
                                                                            <select class="form-control input-sm partner" style="width: 100%" name="partner[]">
                                                                                <option value="<?= $value->supplier_id ?? '' ?>"><?= $value->supplier ?? '' ?></option>
                                                                            </select>
                                                                        </div>
                                                                        <?php
                                                                    } else {
                                                                        print_r($value->supplier);
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <td>
                                                                    <?php if ($jurnal->status === 'unposted') {
                                                                        ?>
                                                                        <div class="form-group">
                                                                            <select class="form-control input-sm select22" style="width:100%" name="kode_coa[]" required>
                                                                                <option value="<?= $value->kode_coa ?? "" ?>"><?= $value->kode_coa ?? "" ?></option>
                                                                                <?php
                                                                                foreach ($coas as $key => $values) {
                                                                                    ?>
                                                                                    <option value="<?= $values->kode_coa ?>"><?= "{$values->kode_coa}" ?></option>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                        </div>
                                                                        <?php
                                                                    } else {
                                                                        print_r($value->kode_coa . " " . $value->account);
                                                                    }
                                                                    ?>
                                                                </td>
                                                                <?php
                                                                if (strtolower($value->posisi) === "d") {
                                                                    $totalDebit += $value->nominal;
                                                                    ?>
                                                                    <td>
                                                                        <input pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' data-row="<?= $keys ?>" class="form-control text-right input-sm newline nominal_d nominal_d_<?= $keys ?>" style="width: 120px" name="debet[]" type="text" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>
                                                                               value="<?= ($jurnal->status === 'unposted') ? number_format($value->nominal, 2, ".", ",") : number_format($value->nominal, 2, ".", ",") ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' data-row="<?= $keys ?>" class="form-control text-right input-sm newline nominal_k nominal_k_<?= $keys ?>" style="width: 120px" name="kredit[]" type="text" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>
                                                                               value="0.00">
                                                                    </td>
                                                                    <?php
                                                                } else {
                                                                    $totalKredit += $value->nominal;
                                                                    ?>
                                                                    <td>
                                                                        <input pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' data-row="<?= $keys ?>" class="form-control text-right input-sm newline nominal_d nominal_d_<?= $keys ?>" style="width: 120px" name="debet[]" type="text" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>
                                                                               value="0.00">
                                                                    </td>
                                                                    <td>
                                                                        <input pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' data-row="<?= $keys ?>" class="form-control text-right input-sm newline nominal_k nominal_k_<?= $keys ?>" style="width: 120px" name="kredit[]" type="text" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>
                                                                               value="<?= ($jurnal->status === 'unposted') ? number_format($value->nominal, 2, ".", ",") : number_format($value->nominal, 2, ".", ",") ?>">
                                                                    </td>
                                                                    <?php
                                                                }
                                                                ?>

                                                                <td> <input type="text" name="kurs[]" style="width: 80px" 
                                                                            value="<?= ($jurnal->status === 'unposted') ? number_format($value->kurs, 2, ".", "") : number_format($value->kurs, 2) ?>" 
                                                                            class="form-control input-sm text-right kurs edited-read newline" required <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>/>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control input-sm select2-curr" style="width:100%" name="curr[]" 
                                                                            required <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>>
                                                                        <option value="<?= $value->kode_mua ?>" selected><?= $value->kode_mua ?></option>
                                                                        <?php foreach ($curr as $key => $values) {
                                                                            ?>
                                                                            <option value="<?= $values->currency ?>"><?= $values->currency ?></option>
                                                                        <?php }
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td><button class="btn btn-success btn-sm btn-add-item" type="button"
                                                                        style=" <?= ($jurnal->status === 'unposted') ? '' : 'display:none' ?>"
                                                                        ><i class="fa fa-plus-circle"></i></button></td>
                                                            <td colspan="2" class="text-center"><strong>Balance</strong></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td><strong><input type="text" readonly class="form-control input-sm total_debit text-right" value="<?= number_format($totalDebit, 2) ?>" ></strong></td>
                                                            <td><strong><input type="text" readonly class="form-control input-sm total_kredit text-right" value="<?= number_format($totalKredit, 2) ?>" ></strong></td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
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
            $this->load->view("admin/_partials/footer_new.php");
            $this->load->view("admin/_partials/modal.php");
            ?>
        </footer>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <template>
            <tr>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon nourut:nourut" style="border: none;"></span>
                        <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                    </div>
                </td>
                <td><input type="text" class="form-control input-sm nama nama_:nourut" value="" name="nama[]"></td>
                <td><input type="text" class="form-control input-sm reffnote_item" value="" name="reffnote_item[]"></td>
                <td>
                    <div class="form-group">
                        <select class="form-control input-sm partner" style="width: 100%" name="partner[]">
                        </select>
                    </div>
                </td>
                <td>
                    <div class="form-group">
                        <select class="form-control input-sm kode_coa" style="width:100%" name="kode_coa[]" required>
                            <?php
                            foreach ($coas as $key => $values) {
                                ?>
                                <option value="<?= $values->kode_coa ?>"><?= "{$values->kode_coa}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                </td>
                <td>
                    <input data-row=":nourut" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' class="form-control input-sm nominal_d nominal_d_:nourut newline_:nourut text-right" style="width: 120px" name="debet[]" type="text" value="0" required>
                </td>
                <td>
                    <input data-row=":nourut" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' class="form-control text-right input-sm nominal_k newline_:nourut nominal_k_:nourut" style="width: 120px" name="kredit[]" type="text" value="0" required>
                </td>
                <td> 
                    <input type="text" name="kurs[]" style="width: 80px" value="1.00" class="form-control input-sm text-right kurs newline_:nourut edited-read" required />
                </td>
                <td>
                    <select class="form-control input-sm select2-curr newline_:nourut" style="width:100%" name="curr[]" 
                            required >
                                <?php foreach ($curr as $key => $values) {
                                    ?>
                            <option value="<?= $values->currency ?>"><?= $values->currency ?></option>
                        <?php }
                        ?>
                    </select>
                </td>
            </tr>
        </template>
        <script>
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
            const lainInput = ((textbox, callback = function(e) {}) => {
                if (textbox.length > 0) {
                    ["input"].forEach(function (event) {
                        textbox[0].addEventListener(event, function () {
                            var row = this.getAttribute("data-row");
                            callback(row);
                        });
                    });
            }
            });
            var no = <?= count($detail) ?>;
            $(function () {
                setNominalCurrency();
                $("#tbl-jurnal").on("click", ".btn-rmv-item", function () {
                    $(this).closest("tr").remove();
                    calculateTotal();
                });

                const calculateTotal = (() => {
                    var totalDebet = 0;
                    var totalKredit = 0;
                    const debet = document.querySelectorAll('.nominal_d');
                    const kredit = document.querySelectorAll('.nominal_k');

                    $.each(debet, function (idx, nomina) {
                        let ttl = $(nomina).val().replace(/,/g, "");
                        totalDebet += parseFloat(ttl);
                    });
                    $.each(kredit, function (idx, nomina) {
                        let ttl = $(nomina).val().replace(/,/g, "");
                        totalKredit += parseFloat(ttl);
                    });
                    if (totalDebet === NaN) {
                        totalDebet = 0;
                    }
                    if (totalKredit === NaN) {
                        totalKredit = 0;
                    }

                    $(".total_debit").val(totalDebet);
                    $(".total_kredit").val(totalKredit);
                });

                lainInput(document.getElementsByClassName("nominal_d"), ((row) => {
                    $(".nominal_k_" + row).val(0);
                    calculateTotal();
                }));
                lainInput(document.getElementsByClassName("nominal_k"), ((row) => {
                    $(".nominal_d_" + row).val(0);
                    calculateTotal();
                }));

                $("#tbl-jurnal").on("click", ".btn-rmv-item", function () {
                    $(this).closest("tr").remove();
                    calculateTotal();
                });

                $(".newline").keyup(function (ev) {
                    if (ev.keyCode === 13) {
                        $(".btn-add-item").trigger("click");
                    }
                });

                $(".btn-add-item").on("click", function (e) {
                    e.preventDefault();
                    no += 1;
                    var tmplt = $("template");
                    var isi_tmplt = tmplt.html().replace(/:nourut/g, no);
                    $("#tbl-jurnal tbody").append(isi_tmplt);
                    $(".nourut" + no).html(no);
                    $(".newline_" + no).keyup(function (ev) {
                        if (ev.keyCode === 13) {
                            $(".btn-add-item").trigger("click");
                        }
                    });
                    setCoa();
                    setCurr();
                    setPartner();

                    lainInput(document.getElementsByClassName("nominal_d_" + no), ((no) => {
                        $(".nominal_k_" + no).val(0);
                        calculateTotal();
                    }));
                    lainInput(document.getElementsByClassName("nominal_k_" + no), ((no) => {
                        $(".nominal_d_" + no).val(0);
                        calculateTotal();
                    }));
                    $(".nama_" + no).focus();
                    setNominalCurrency();
                });
                const setCurr = (() => {
                    $(".select2-curr").select2({
                        placeHolder: "Pilih",
                        allowClear: true
                    });
                });
                setCurr();
                $(".periode").select2({
                    placeholder: "Pilih",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>purchase/jurnalentries/get_periode",
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
                                    id: item.periode,
                                    text: item.periode
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
                //select 2 akun coa
                const setCoa = (() => {
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
                });
                setCoa();

                const form = document.forms.namedItem("form-jurnal");
                form.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-jurnal").then(
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
                    calculateTotal();
                    confirmRequest("Jurnal Entries", "Update Jurnal ? ", function () {
                        $("#form-jurnal-submit").trigger("click");
                    });
                });
                $("#btn-cancel").off("click").unbind("click").on("click", function () {
                    calculateTotal();
                    confirmRequest("Jurnal Entries", "Cancel Jurnal ? ", function () {
                        updateStatus();
                    });
                });
                $("#btn-update-status").off("click").unbind("click").on("click", function () {
                    calculateTotal();
                    confirmRequest("Jurnal Entries", "Posted Jurnal ? ", function () {
                        updateStatus("posted");

                    });
                });

                const updateStatus = ((status = "cancel") => {
                    $.ajax({
                        url: "<?= base_url("purchase/jurnalentries/update_status"); ?>",
                        type: "POST",
                        data: {
                            ids: "<?= $id ?>",
                            status: status,
                            debit: $(".total_debit").val(),
                            kredit: $(".total_kredit").val()
                        },
                        beforeSend: function (xhr) {
                            please_wait(function () {});
                        }, success: function (data) {
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
                $(".select22").select2({
                    placeholde: "Pilih",
                    allowClear: true
                });
                const setPartner = (() => {
                    $(".partner").select2({
                        placeholder: "Pilih",
                        allowClear: true,
                        ajax: {
                            dataType: 'JSON',
                            type: "GET",
                            url: "<?php echo base_url(); ?>accounting/kaskeluar/get_partner",
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
                                        id: item.id,
                                        text: item.nama
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
                });
                setPartner();

                $("#btn-print").on("click", function () {
                    $.ajax({
                        url: "<?= base_url("purchase/jurnalentries/print"); ?>",
                        type: "POST",
                        data: {
                            ids: "<?= $id ?>"
                        },
                        beforeSend: function (xhr) {
                            please_wait(function () {});
                        }, success: function (data) {
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

                $("#btn-download-temp").on("click", function () {
                    $.ajax({
                        url: "<?= base_url("purchase/jurnalentries/download_template/{$id}"); ?>",
                        type: "POST",
                        beforeSend: function (xhr) {
                            please_wait(function () {});
                        }, success: function (data) {
                            unblockUI(function () {});
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = data.url;
                            a.download = data.text_name;
                            document.body.appendChild(a);
                            a.click();
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

                $("#btn-import").on("click", function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><input type="file" id="file_upload" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"></center>');
                    $('.modal-title').text("Import Entries");
                    $("#btn-tambah").on("click", function (es) {
                        var file_data = $('#file_upload').prop('files')[0];
                        if (file_data === undefined) {
                            setTimeout(function () {
                                alert_notify('fa fa-close', "File Belum dipilih", 'danger', function () {});
                            }, 500);
                            return;
                        }
                        var form_data = new FormData();
                        form_data.append('file', file_data);
                        $.ajax({
                            url: "<?= base_url("purchase/jurnalentries/upload/{$id}"); ?>",
                            data: form_data,
                            type: 'post',
                            dataType: "json",
                            processData: false,
                            contentType: false,
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
                }
                );

            });
            $(document).ready(function () {
                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
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

        </script>
    </body>
</html>