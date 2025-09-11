<!DOCTYPE html>
<html>
    <head>
        <style>
            #btn-cancel,#btn-simpan,#btn-print {
                display: none;
            }
            .select2-container--focus{
                border:  1px solid #66afe9;
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
                        <form class="form-horizontal" method="POST" name="form-acc-bankmasuk" id="form-acc-bankmasuk" action="<?= base_url("accounting/bankmasuk/update/{$id}") ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title"><strong><?= $datas->no_bm ?></strong></h3>
                                <div class="pull-right text-right" id="btn-header">
                                    <?php
                                    if ($datas->status == 'cancel') {
                                        ?>
                                        <button class="btn btn-primary btn-sm" type="button" id="btn-draft" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                            Simpan Sebagai Draft
                                        </button>
                                    <?php }
                                    ?>
                                </div>
                                <input type="hidden" value="<?= $datas->id ?>" name="ids">
                            </div>
                            <div class="box-body">
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">No ACC (Debet)</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2 no_acc edited" name="no_acc" style="width: 100%;" disabled>
                                                        <option value=""></option>
                                                        <?php
                                                        foreach ($coa as $key => $value) {
                                                            ?>
                                                            <option value="<?= $value->kode_coa ?>" <?= ($datas->kode_coa === $value->kode_coa ) ? 'selected' : '' ?>><?= "({$value->kode_coa}) - {$value->nama}" ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Kepada</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <!--<input type="hidden" name="partner" value="<?= $datas->partner_id ?>">-->
                                                    <input type="hidden" name="partner_name" id="partner_name"  value="<?= $datas->partner_nama ?>"/>
                                                    <select class="form-control select2 partner edited" name="partner" id="partner" style="width: 100%;" disabled>
                                                        <option value="<?= $datas->partner_id ?>"><?= $datas->partner_nama ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Untuk Transaksi</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="transaksi" id="transaksi" class="form-control input-sm edited-read" value="<?= $datas->transinfo ?>" readonly/>
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
                                                        <input type="text" name="tanggal" id="tanggal" class="form-control input-sm edited-read" value="<?= date("Y-m-d", strtotime($datas->tanggal)) ?>" required readonly/>
                                                        <span class="input-group-addon"><i class="fa fa-calendar"><span></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Lain-Lain</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="lain_lain" id="lain_lain" class="form-control input-sm edited-read" value="<?= $datas->lain2 ?>" readonly/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Jenis Transaksi</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2 edited" name="jenis_transaksi" style="width: 100%" disabled>
                                                        <?php foreach ($jenis_transaksi as $key => $value) {
                                                            ?>
                                                            <option value="<?= $key ?>" <?= ($key === $datas->jenis_transaksi) ? "selected" : "" ?>><?= $value ?></option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <!--<button type="button" class="btn btn-default btn-sm btn-add-item-tf" style="display: none"><span class="glyphicon glyphicon-transfer"></span>&nbsp; Dari Pindah Dana</button>-->
                                                    <button type="button" class="btn btn-default btn-sm btn-add-item-bg" style="display: none"><span class="glyphicon glyphicon-book"></span>&nbsp; Dari Bukti Giro</button>
                                                </div>

                                                <div class="col-xs-8 col-md-8">
                                                    <div class="col-xs-4">
                                                        <button type="submit" class="btn btn-default btn-sm btn-save" style="display: none" ><span class="glyphicon glyphicon-save"></span> Simpan </button>

                                                    </div>

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
                                                <table class="table table-condesed table-hover rlstable" width="100%" id="bankmasuk-detail" style="min-width: 105%">
                                                    <thead>
                                                    <th class="style" style="width: 5px">No.</th>
                                                    <th class="style" style="width: 150px">Uraian</th>
                                                    <th class="style" style="width: 130px">Bank</th>
                                                    <th class="style" style="width: 120px">No Rek</th>
                                                    <th class="style" style="width: 120px">No.Cek/BG</th>
                                                    <th class="style" style="width: 140px">Tgl Cair</th>
                                                    <th class="style" style="width: 100px">No.Acc(Kredit)</th>
                                                    <th class="style" style="width: 80px;text-align: right;" >Kurs</th>
                                                    <th class="style" style="">Curr</th>
                                                    <th class="style text-right" style="max-width: 150px">Nominal</th>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($data_detail as $key => $value) {
                                                            ?>
                                                            <tr> 
                                                                <td>
                                                                    <div class="input-group">
                                                                        <span class="input-group-addon" style="border:none;"><?= ($key + 1) ?></span>
                                                                        <button type="button" class="btn btn-danger btn-sm btn-rmv-item" style="display: none;"><i class="fa fa-close"></i></button>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="uraian[]" class="form-control uraian edited-read input-sm" value="<?= $value->uraian ?? "" ?>" readonly/>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="bank[]" class="form-control bank edited-read input-sm" value="<?= $value->bank ?>" required readonly/>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="norek[]" class="form-control norek edited-read input-sm" value="<?= $value->no_rek ?>" required readonly/>
                                                                </td>
                                                                <td>
                                                                    <input type="text" name="nobg[]" class="form-control nobg edited-read input-sm" value="<?= $value->no_bg ?>" readonly/>
                                                                </td>
                                                                <td>
                                                                    <div class="input-group tgl-def-format">
                                                                        <input type="text" name="tglcair[]" class="form-control tglcair edited-read input-sm" value="<?= date("Y-m-d", strtotime($value->tgl_cair)) ?>" required readonly/>
                                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control input-sm select2-coa edited" style="width:100%" name="kode_coa[]" required disabled>
                                                                        <?php
                                                                        foreach ($coas as $key => $values) {
                                                                            ?>
                                                                            <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $value->kode_coa) ? 'selected' : '' ?>><?= "{$values->kode_coa}" ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>

                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control fkurs input-sm text-right" value="<?= number_format($value->kurs, 2) ?>" disabled>
                                                                    <input type="text" name="kurs[]" style="display: none;" value="<?= $value->kurs ?>" class="form-control input-sm text-right kurs edited-read" required readonly/>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control input-sm select2 select2-curr edited" style="width:100%" name="curr[]" required disabled>
                                                                        <option value="<?= $value->currency_id ?>" selected><?= $value->curr ?></option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control fnominal input-sm text-right" value="<?= number_format($value->nominal, 2) ?>" disabled>
                                                                    <input type="text" name="nominal[]" value="<?= $value->nominal ?>" style="display: none;" class="form-control input-sm text-right nominal edited-read" required readonly/>
                                                                    <input type="hidden" class="form-control" name="giro_masuk_detail[]" value="<?= $value->giro_masuk_detail_id ?>">
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <td>
                                                                <button class="btn btn-success btn-sm btn-add-item" style="display: none"><i class="fa fa-plus-circle"></i></button>
                                                            </td>
                                                            <td colspan="8" class="text-right text-bold total-nominal">

                                                            </td>
                                                            <td class="text-bold">
                                                                <input type="text" class="form-control input-sm text-right ftotal_nominal" value="<?= number_format($datas->total_rp, 2) ?>" readonly/>
                                                                <input type="text" name="total_nominal" class="form-control input-sm text-right total_nominal" style="display : none" value="<?= $datas->total_rp ?>" readonly/>
                                                            </td>
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
                                                                <strong>:&nbsp;<a href="<?= base_url("purchase/jurnalentries/edit/" . encrypt_url(($jurnal->kode ?? ""))) ?>" target="_blank"><?= $jurnal->kode ?? "" ?></a></strong>
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
                            <input type="hidden" name="head" value='<?= json_encode($datas, true) ?>'>
                            <input type="hidden" name="detail" value='<?= json_encode($data_detail, true) ?>'>
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
        <template class="bankmasuk-tmplt">
            <tr class="list-new">
                <td>
                    <div class="input-group">
                        <span class="input-group-addon nourut:nourut" style="border: none;"></span>
                        <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                    </div>
                </td>
                <td>
                    <input type="text" name="uraian[]" class="form-control uraian:nourut input-sm"/>
                </td>
                <td>
                    <input type="text" name="bank[]" class="form-control bank bank:nourut input-sm" required/>
                </td>
                <td>
                    <input type="text" name="norek[]" class="form-control norek norek:nourut input-sm" required/>
                </td>
                <td>
                    <input type="text" name="nobg[]" class="form-control input-sm"/>
                </td>
                <td>
                    <div class="input-group tgl-def-format">
                        <input type="text" name="tglcair[]" class="form-control tglcair:nourut input-sm" value="<?= date("Y-m-d") ?>"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </td>
                <td>
                    <select class="form-control input-sm select2-coa" style="width:100%" name="kode_coa[]" required>
                        <option value=""></option>
                        <?php
                        foreach ($coas as $key => $value) {
                            ?>
                            <option value="<?= $value->kode_coa ?>"><?= "{$value->kode_coa}" ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="kurs[]" value="1.00" class="form-control input-sm text-right" required/>
                </td>
                <td>
                    <select class="form-control input-sm select2 select2-curr" style="width:100%" name="curr[]" required>
                        <option value="1" selected>IDR</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="nominal[]" class="form-control input-sm nominal text-right" value="0" required/>
                    <input type="hidden" name="giro_masuk_detail[]" class="form-control"/>
                </td>
            </tr>
        </template>

        <template class="bankmasuk-tmplt-add">
            <tr class="list-new">
                <td>
                    <div class="input-group">
                        <span class="input-group-addon nourut:nourut" style="border: none;"></span>
                        <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                    </div>
                </td>
                <td>
                    <input type="text" name="uraian[]" class="form-control uraian:nourut input-sm" value=""/>
                </td>
                <td>
                    <input type="text" name="bank[]" class="form-control bank:nourut input-sm" value="" required/>
                </td>
                <td>
                    <input type="text" name="norek[]" class="form-control norek:nourut input-sm" value="" required/>
                </td>
                <td>
                    <input type="text" name="nobg[]" class="form-control nobg:nourut input-sm" value=""/>
                </td>
                <td>
                    <div class="input-group tgl-def-format">
                        <input type="text" name="tglcair[]" class="form-control tglcair:nourut input-sm" value="<?= date("Y-m-d") ?>"/>
                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    </div>
                </td>
                <td>
                    <select class="form-control input-sm select2-coa coa_:nourut" style="width:100%" name="kode_coa[]" required>
                        <option value=""></option>
                        <?php
                        foreach ($coas as $key => $value) {
                            ?>
                            <option value="<?= $value->kode_coa ?>"><?= "{$value->kode_coa}" ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="kurs[]" value="1.00" class="form-control kurs:nourut input-sm text-right" required/>
                </td>
                <td>
                    <select class="form-control input-sm select2 select2-curr curr_:nourut" style="width:100%" name="curr[]" required>
                        <option value="1" selected>IDR</option>
                    </select>
                </td>
                <td>
                    <input type="text" name="nominal[]" class="form-control input-sm text-right nominal:nourut" value="" required/>
                    <input type="hidden" name="giro_masuk_detail[]" class="form-control gmd:nourut" value="0"/>
                </td>
            </tr>
        </template>

        <script>
<?php
if ($datas->status == 'confirm') {
    ?>
                $("#btn-confirm").html("Cancel").toggleClass("btn-danger");
    <?php
}
?>
            var no = <?= count($data_detail) ?>;
            var edit = false;
            const setCurr = (() => {
                $(".select2-curr").select2({
                    placeholder: "Pilih",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/kaskeluar/get_currency",
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
                                    text: item.currency
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
            var pindahDana = [];
            var buktigiro = [];

            const lainInput = ((textbox, callback = function() {}) => {
                ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(function (event) {
                    textbox.addEventListener(event, function (e) {
//                        if (this.value !== "")
                        callback();
                    });
                });
            });

            $(function () {
                lainInput(document.getElementById("lain_lain"), function () {
                    if ($("#partner_name").val() !== "") {
                        $("#partner_name").val("");
                        $("#partner").val(null).trigger("change");
                    }

                });

                const updateStatus = ((statuss) => {
                    $.ajax({
                        url: "<?= base_url("accounting/bankmasuk/update_status/{$id}") ?>",
                        data: {status: statuss.trim()},
                        type: "POST",
                        beforeSend: function (xhr) {
                            please_wait(function () {

                            });
                        },
                        success: function (data) {
                            if (data.pin) {
                                async function abb(status) {
                                    await inputPin("<?= base_url("setting/user/check_pin") ?>", function () {
                                        updateStatus(status);
                                    });
                                }
                                abb(statuss);
                            } else {
                                window.location.reload();
                            }
                        },
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {});

                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert_notify("fa fa-warning", jqXHR?.responseJSON?.message, "danger", function () {}, 500);
                        }
                    });
                });
                $("#btn-draft").unbind("click").off("click").on("click", function (e) {
                    e.preventDefault();
                    confirmRequest("Bank Masuk", "Simpan Kembali Sebagai Draft ? ", (() => {
                        updateStatus("draft");
                    }));
                });
                $("#btn-confirm").unbind("click").off("click").on("click", function (e) {
                    e.preventDefault();
                    var text = $(this).html();
                    var statuss = text.replace(/(<([^>]+)>)/ig, "");
                    confirmRequest("Bank Masuk", statuss + " Data ? ", (() => {
                        updateStatus(statuss);
                    }));

                });

                $("#btn-print").on("click", function (e) {
                    e.preventDefault();
                    $.ajax({
                        url: "<?= base_url('accounting/bankmasuk/print') ?>",
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

                $(".btn-add-item-tf").on("click", function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text("Pindah Dana");
                    $.post("<?= base_url('accounting/bankmasuk/get_view_pindah_dana') ?>", {no: pindahDana}, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);
                            $("#btn-tambah").html("Tambahkan");
                        }, 1000);
                    });
                });

                $(".btn-add-item-bg").on("click", function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text("Cari Giro Masuk");
                    $.post("<?= base_url('accounting/bankmasuk/get_view_bukti_giro') ?>", {no: buktigiro}, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);
                            $("#btn-tambah").html("Tambahkan");
                        }, 1000);
                    });
                });


                const formdo = document.forms.namedItem("form-acc-bankmasuk");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    $(".total-nominal").trigger("click");
                    please_wait(function () {});
                    request("form-acc-bankmasuk").then(
                            async response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200) {
                                    if (response.data.pin) {
                                        await inputPin("<?= base_url("setting/user/check_pin") ?>", function () {
                                            $(".btn-save").trigger("click");
                                        });
                                    } else {
                                        window.location.replace(response.data.url);
                                    }
                                }
//                                    
                            }
                    ).finally(() => {
                        unblockUI(function () {});
                    });
                    event.preventDefault();
                },
                        false

                        );
                $(".select2").select2();
                $("#btn-simpan").on("click", function (e) {
                    e.preventDefault();
                    $(".btn-save").trigger("click");
                });

                $(".btn-add-item").on("click", function (e) {
                    e.preventDefault();
                    no += 1;
                    var tmplt = $("template.bankmasuk-tmplt");
                    var isi_tmplt = tmplt.html().replace(/:nourut/g, no);
                    $("#bankmasuk-detail tbody").append(isi_tmplt);
                    $(".select2-coa").select2();
                    setCurr();
                    $(".nominal").on("blur", function () {
                        calculateTotal();
                    });
                    setBank(no);
                    var tglHeader = $("#tanggal").val();
                    $(".tglcair" + no).val(tglHeader);
                    setTglFormatDef(".tgl-def-format");
                    $(".nominal").keyup(function (ev) {
                        if (ev.keyCode === 13) {
                            $(".btn-add-item").trigger("click");
                        }
                    });
                    $(".uraian" + no).focus();
                    $(".nominal").keyup(function (ev) {
                        if (ev.keyCode === 13) {
                            $(".btn-add-item").trigger("click");
                        }
                    });
                    $(".nourut" + no).html(no);
                });

                const setBank = ((no) => {
                    var ttt = $(".no_acc").find(":selected");
                    if (edit) {
                        var acc = ttt.text();
                        const texts = acc.split(" - ");
                        $(".bank" + no).val(texts?.[1]);
                        $(".norek" + no).val(texts?.[2]);

                    }
                });

                $("#btn-edit").on("click", function (e) {
                    e.preventDefault();
                    $(".edited-read").removeAttr("readonly");
                    $(".edited").removeAttr("disabled");
                    setCurr();
                    $(this).hide();
                    $("#btn-cancel").show();
                    $(".no-urut").hide();
                    $(".btn-rmv-item").show();
                    $(".btn-add-item").show();
                    $("#btn-simpan").show();
                    $(".btn-add-item-tf").show();
                    $(".btn-add-item-bg").show();
                    $(".fnominal").hide();
                    $(".fkurs").hide();
                    $(".nominal").show();
                    $(".kurs").show();
                    $(".ftotal_nominal").hide();
                    $(".total_nominal").show();
                    $(".select2-coa").select2();
                    $("#btn-confirm").hide();
                    edit = true;
                });

                $("#btn-cancel").on("click", function (e) {
                    e.preventDefault();
                    $('#form-acc-bankmasuk').trigger("reset");
                    $(this).hide();
                    $("#btn-edit").show();
                    $(".edited-read").attr("readonly", "readonly");
                    $(".edited").attr("disabled", "disabled");
                    $(".no-urut").show();
                    $(".btn-rmv-item").hide();
                    $(".btn-add-item").hide();
                    $("#btn-simpan").hide();
                    $(".btn-add-item-bg").hide();
                    $(".btn-add-item-tf").hide();
                    $(".fnominal").show();
                    $(".fkurs").show();
                    $(".nominal").hide();
                    $(".kurs").hide();
                    $(".list-new").remove();
                    $(".ftotal_nominal").show();
                    $(".total_nominal").hide();
                    $("#btn-confirm").show();
                    edit = false;
                });

                $("#bankmasuk-detail").on("click", ".btn-rmv-item", function () {
                    $(this).closest("tr").remove();
                    calculateTotal();
                });

                const calculateTotal = (() => {
                    var total = 0;
                    const elements = document.querySelectorAll('.nominal');

                    $.each(elements, function (idx, nomina) {
                        let ttl = $(nomina).val();
                        total += parseInt(ttl);
                    });
                    if (total === NaN) {
                        $(".total_nominal").val();
                        return;
                    }

                    $(".total_nominal").val(total);
                });
                $(".total-nominal").on("click", function () {
                    calculateTotal();
                });
                $("#partner").select2({
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

                $(".partner").on("change", function () {
                    var ttt = $(".partner").find(":selected");
                    $("#lain_lain").val("");
                    $("#partner_name").val(ttt.text());
                });

//                $(".no_acc").on("change", function () {
//                    setBank();
//                });

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

            });

            $(document).ready(function () {
                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
                });
            });

            const addToTable = ((data, url) => {
                $.ajax({
                    url: "<?= base_url('accounting/bankmasuk/') ?>" + url,
                    type: "POST",
                    data: {
                        no: data
                    },
                    beforeSend: function (xhr) {
                        please_wait(function () {});
                    },
                    success: function (data) {
                        if (url === "pd") {
//                            $.each(data.data, function (idx, row) {
//                                no += 1;
//                                if (!pindahDana.includes(row.id))
//                                    pindahDana.push(row.id);
//                                var tmplt = $("template.bankmasuk-tmplt-add");
//                                var isi_tmplt = tmplt.html().replace(/:bank/g, row.bank).replace(/:no-/g, no)
//                                        .replace(/:norek/g, row.no_rek).replace(/:nobg/g, row.no_bg)
//                                        .replace(/:kurs/g, row.kurs).replace(/:rprp/g, row.nominal).replace(/:gmdid/g, row.id);
//                                $("#bankmasuk-detail tbody").append(isi_tmplt);
//                                $(".coa_" + no).val(row.kode_coa).trigger("change");
//                            });
                        } else {
                            $.each(data.data, function (idx, row) {
                                no += 1;
                                if (!buktigiro.includes(row.id))
                                    buktigiro.push(row.id);
                                var tmplt = $("template.bankmasuk-tmplt-add");
                                var isi_tmplt = tmplt.html().replace(/:nourut/g, no);

                                $("#bankmasuk-detail tbody").append(isi_tmplt);
                                var tglHeader = $("#tanggal").val();
                                $(".tglcair" + no).val(tglHeader);

                                $(".coa_" + no).val(row.kode_coa).trigger("change");
                                $(".bank" + no).val(row.bank);
                                $(".norek" + no).val(row.no_rek);
                                $(".nobg" + no).val(row.no_bg);
                                $(".kurs" + no).val(row.kurs);
                                $(".nominal" + no).val(row.nominal);
                                $(".gmd" + no).val(row.id);
                                $(".nourut" + no).html(no);
                            });
                        }
                    },
                    complete: function (jqXHR, textStatus) {
                        setTglFormatDef(".tgl-def-format");
                        unblockUI(function () {
                            setCurr();
                            $(".total-nominal").trigger("click");
                        }, 100);

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert_notify("fa fa-warning", jqXHR?.responseJSON?.message, "danger", function () {}, 500);
                    }
                });
            });
        </script>
    </body>
</html>