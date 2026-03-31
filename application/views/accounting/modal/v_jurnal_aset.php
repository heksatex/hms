<div class="box">
    <div class="box-header with-border">

    </div>
    <form  class="form-horizontal" method="POST" name="form-jurnal" id="form-jurnal" action="<?= base_url("accounting/asettetap/update_jurnal/{$id}") ?>">
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
                                <input type="hidden" name="jurnal" value="<?= $jurnal->kode ?>">
                                <span><?= $jurnal->nama_jurnal ?? "" ?></span>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4">
                                <label class="form-label required">Tanggal</label>
                            </div>
                            <div class="col-xs-8 col-md-8">
                                <div class="input-group tgl-def-format">
                                    <input type="text" name="tanggal" id="tanggal" class="form-control input-sm" value="<?= $jurnal->tanggal_dibuat ?>" required <?= ($jurnal->status !== 'unposted') ? 'readonly' : '' ?>/>
                                    <span class="input-group-addon"><i class="fa fa-calendar"><span></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4">
                                <label class="form-label required">Periode</label>
                            </div>
                            <div class="col-xs-8 col-md-8 text-uppercase">
                                <select class="form-control input-sm periode" name="periode" required <?= ($jurnal->status !== 'unposted') ? 'disabled' : '' ?>> 
                                    <option value="<?= $jurnal->periode ?>" selected><?= $jurnal->periode ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12">
                    <div class="form-group">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4">
                                <label class="form-label">Origin</label>
                            </div>
                            <div class="col-xs-8 col-md-8 text-uppercase">
                                <input type="text" value="<?= $jurnal->origin ?>" class="form-control input-sm" name="origin" readonly>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4">
                                <label class="form-label">Reff Note</label>
                            </div>
                            <div class="col-xs-8 col-md-8 text-uppercase">
                                <textarea class="form-control" id="reff_note" name="reff_note" <?= ($jurnal->status !== 'unposted') ? 'disabled' : '' ?>><?= $jurnal->reff_note ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <div class="col-xs-12">
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
                                        <span class="input-group-addon" style="border:none;"><?= ($keys + 1) ?></span>
                                    </td>
                                    <td><input type="text" class="form-control input-sm nama" value="<?= $value->nama ?>" name="nama[]" <?= ($jurnal->status !== 'unposted') ? 'readonly' : '' ?>></td>
                                    <td><input type="text" class="form-control input-sm reffnote_item" value="<?= $value->reff_note ?>" name="reffnote_item[]" <?= ($jurnal->status !== 'unposted') ? 'readonly' : '' ?>></td>
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
                                                    <!--<option value="<?= $value->kode_coa ?? "" ?>"><?= $value->kode_coa ?? "" ?></option>-->
                                                    <?php
                                                    foreach ($coas as $key => $values) {
                                                        ?>
                                                        <option value="<?= $values->kode_coa ?>" <?= ($value->kode_coa === $values->kode_coa) ? "selected" : "" ?>><?= "{$values->kode_coa}" ?></option>
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
                                            <input pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' data-row="<?= $keys ?>" class="form-control text-right input-sm newline nominal_d nominal_d_<?= $keys ?>" style="width: 120px" name="debet[]" type="text" <?= ($jurnal->status !== 'unposted') ? 'disabled' : '' ?>
                                                   value="<?= ($jurnal->status === 'unposted') ? number_format($value->nominal, 2, ".", ",") : number_format($value->nominal, 2, ".", ",") ?>">
                                        </td>
                                        <td>
                                            <input pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' data-row="<?= $keys ?>" class="form-control text-right input-sm newline nominal_k nominal_k_<?= $keys ?>" style="width: 120px" name="kredit[]" type="text" <?= ($jurnal->status !== 'unposted') ? 'disabled' : '' ?>
                                                   value="0.00">
                                        </td>
                                        <?php
                                    } else {
                                        $totalKredit += $value->nominal;
                                        ?>
                                        <td>
                                            <input pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' data-row="<?= $keys ?>" class="form-control text-right input-sm newline nominal_d nominal_d_<?= $keys ?>" style="width: 120px" name="debet[]" type="text" <?= ($jurnal->status !== 'unposted') ? 'disabled' : '' ?>
                                                   value="0.00">
                                        </td>
                                        <td>
                                            <input pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' data-row="<?= $keys ?>" class="form-control text-right input-sm newline nominal_k nominal_k_<?= $keys ?>" style="width: 120px" name="kredit[]" type="text" <?= ($jurnal->status !== 'unposted') ? 'disabled' : '' ?>
                                                   value="<?= ($jurnal->status === 'unposted') ? number_format($value->nominal, 2, ".", ",") : number_format($value->nominal, 2, ".", ",") ?>">
                                        </td>
                                        <?php
                                    }
                                    ?>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td>
                                    <button class="btn btn-success btn-sm btn-add-item" type="submit"
                                            style=" <?= ($jurnal->status === 'unposted') ? '' : 'display:none' ?>"
                                            >Simpan</button>
                                </td>
                                <td>
                                    <button class="btn btn-danger btn-sm btn-cancel-item" type="button"
                                            style=" <?= ($jurnal->status === 'cancel') ? 'display:none' : '' ?>"
                                            >Cancel Jurnal</button>
                                </td>
                                <td class="text-center"><strong>Balance</strong></td>
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
    </form>
    <?php
    $this->load->view("admin/_partials/js.php")
    ?>
</div>
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


    const form = document.forms.namedItem("form-jurnal");
    form.addEventListener(
            "submit",
            (event) => {
        please_wait(function () {});
        request("form-jurnal").then(
                response => {
                    unblockUI(function () {
                        alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                        $(".total_debit").val(response.data.debet);
                        $(".total_kredit").val(response.data.kredit);
                    }, 100);
//                    if (response.status === 200)
//                        location.reload();
                }
        ).catch(err => {
            unblockUI(function () {});
            alert_modal_warning("Hubungi Dept IT");
        });
        event.preventDefault();
    },
            false
            );

    setNominalCurrency();
    $(".select22").select2({
        placeHolder: "Pilih"
    });
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

    $(".btn-cancel-item").on("click", function () {
        confirmRequest("Jurnal Entries", "Batalkan Jurnal ? ", function () {
            $.ajax({
                url: "<?= base_url("accounting/asettetap/update_status_jurnal/{$id}") ?>",
                type: "POST",
                data: {
                    jurnal: "<?= $jurnal->kode ?>",
                    status: "cancel"
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
</script>