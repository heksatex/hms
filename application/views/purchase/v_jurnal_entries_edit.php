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
                                        <i class="fa fa-check">&nbsp;Posted</i>
                                    </button>
                                <?php } ?>
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
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tanggal Dibuat</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <span><?= $jurnal->tanggal_dibuat ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
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
                                        </div>
                                        <!--                                        <div class="form-group">
                                                                                    <div class="col-md-12 col-xs-12">
                                                                                        <div class="col-xs-4">
                                                                                            <label class="form-label">Tanggal Posting</label>
                                                                                        </div>
                                                                                        <div class="col-xs-8 col-md-8 text-uppercase">
                                                                                            <span><?= $jurnal->tanggal_posting ?? "" ?></span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>-->
                                    </div>
                                    <div class="col-md-6 col-xs-12">

                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Origin</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8 text-uppercase">
                                                    <input type="text" value="<?= $jurnal->origin ?>" class="form-control input-sm" name="origin" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
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
                                                                        <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
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
                                                                        <input data-row="<?= $keys ?>" class="form-control text-right input-sm nominal_d nominal_d_<?= $keys ?>" style="width: 120px" name="debet[]" type="text" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>
                                                                               value="<?= ($jurnal->status === 'unposted') ? number_format($value->nominal, 2, ".", "") : number_format($value->nominal, 2) ?>">
                                                                    </td>
                                                                    <td>
                                                                        <input data-row="<?= $keys ?>" class="form-control text-right input-sm nominal_k nominal_k_<?= $keys ?>" style="width: 120px" name="kredit[]" type="text" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>
                                                                               value="0">
                                                                    </td>
                                                                    <?php
                                                                } else {
                                                                    $totalKredit += $value->nominal;
                                                                    ?>
                                                                    <td>
                                                                        <input data-row="<?= $keys ?>" class="form-control text-right input-sm nominal_d nominal_d_<?= $keys ?>" style="width: 120px" name="debet[]" type="text" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>
                                                                               value="0">
                                                                    </td>
                                                                    <td>
                                                                        <input data-row="<?= $keys ?>" class="form-control text-right input-sm nominal_k nominal_k_<?= $keys ?>" style="width: 120px" name="kredit[]" type="text" <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>
                                                                               value="<?= ($jurnal->status === 'unposted') ? number_format($value->nominal, 2, ".", "") : number_format($value->nominal, 2) ?>">
                                                                    </td>
                                                                    <?php
                                                                }
                                                                ?>

                                                                <td> <input type="text" name="kurs[]" style="width: 80px" 
                                                                            value="<?= ($jurnal->status === 'unposted') ? number_format($value->kurs, 2, ".", "") : number_format($value->kurs, 2) ?>" 
                                                                            class="form-control input-sm text-right kurs edited-read" required <?= ($jurnal->status === 'unposted') ? '' : 'disabled' ?>/>
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
                <td><input type="text" class="form-control input-sm nama" value="" name="nama[]"></td>
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
                    <input data-row=":nourut" class="form-control input-sm nominal_d nominal_d_:nourut text-right" style="width: 120px" name="debet[]" type="text" value="0" required>
                </td>
                <td>
                    <input data-row=":nourut" class="form-control text-right input-sm nominal_k nominal_k_:nourut" style="width: 120px" name="kredit[]" type="text" value="0" required>
                </td>
                <td> 
                    <input type="text" name="kurs[]" style="width: 80px" value="1.00" class="form-control input-sm text-right kurs edited-read" required />
                </td>
                <td>
                    <select class="form-control input-sm select2-curr" style="width:100%" name="curr[]" 
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
                        let ttl = $(nomina).val();
                        totalDebet += parseInt(ttl);
                    });
                    $.each(kredit, function (idx, nomina) {
                        let ttl = $(nomina).val();
                        totalKredit += parseInt(ttl);
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

                $(".btn-add-item").on("click", function (e) {
                    e.preventDefault();
                    no += 1;
                    var tmplt = $("template");
                    var isi_tmplt = tmplt.html().replace(/:nourut/g, no);
                    $("#tbl-jurnal tbody").append(isi_tmplt);
                    $(".nourut" + no).html(no);
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

            });
        </script>
    </body>
</html>