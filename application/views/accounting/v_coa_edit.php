<!DOCTYPE html>
<html>
    <head>
        <?php
        $this->load->view("admin/_partials/head.php");
        ?>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
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
                        $data['jen_status'] = $detail->status;
                        $this->load->view("admin/_partials/statusbar-new.php", $data);
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">
                        <form class="form-horizontal" method="POST" name="form-coa" id="form-coa" action="<?= base_url("accounting/coa/update/{$id}") ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title">Edit COA</h3>
                                <div class="pull-right">
                                    <label class="btn btn-secondary">
                                        <input type="radio" name="status" value="naktif" <?= ($detail->status === "naktif") ? "checked" : "" ?>> Non Aktif
                                    </label>
                                    <label class="btn btn-secondary">
                                        <input type="radio" name="status" value="aktif" <?= ($detail->status === "aktif") ? "checked" : "" ?>> Aktif
                                    </label>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Kode COA </label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="kode_coa" id="kode_coa" class="form-control input-sm" value="<?= $detail->kode_coa ?>" readonly/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Nama COA </label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="nama_coa" id="nama_coa" class="form-control input-sm" value="<?= $detail->nama ?>"/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Parent</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm parent" name="parent" style="width: 100%">
                                                        <option value="<?= $detail->kc ?? 0 ?>" selected><?= "{$detail->nc}" ?></option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Saldo Normal</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <label class="btn btn-secondary">
                                                        <input type="radio" name="saldo_normal" value="D" <?= ($detail->saldo_normal === "D") ? "checked" : "" ?>> Debet
                                                    </label>
                                                    <label class="btn btn-secondary">
                                                        <input type="radio" name="saldo_normal" value="C" <?= ($detail->saldo_normal === "C") ? "checked" : "" ?>> Credit
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Saldo Awal IDR</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="saldo_awal" value="<?= number_format($detail->saldo_awal, 2, ".", ",") ?>" pattern="^-?\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency'
                                                           class="form-control input-sm text-right" required/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Saldo Awal Valas</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="saldo_valas" value="<?= number_format($detail->saldo_valas, 2, ".", ",") ?>" pattern="^-?\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency'
                                                           class="form-control input-sm text-right" required/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Jenis Transaksi </label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm curr" name="jenis_trans" style="width: 100%">
                                                        <option></option>
                                                        <?php
                                                        foreach ($jenis_trans as $key => $value) {
                                                            ?>
                                                            <option value="<?= $key ?>" <?= ($detail->jenis_transaksi === $key) ? 'selected' : '' ?>><?= $value ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Currency</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm curr" name="curr" style="width: 100%">
                                                        <option></option>
                                                        <?php
                                                        foreach ($curr as $key => $value) {
                                                            ?>
                                                            <option value="<?= $value->currency ?>" <?= ($value->currency === $detail->curr) ? "selected" : "" ?> ><?= "{$value->currency}" ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-default btn-sm btn-save" style="display: none" ><span class="glyphicon glyphicon-save"></span> Simpan </button>
                        </form>
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/js.php") ?>
                <?php $this->load->view("admin/_partials/footer_new.php", ["kode" => $detail->kode_coa, "mms" => $mms->kode]); ?>
            </footer>
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
            $(function () {
                setNominalCurrency();
                $(".parent").select2({
                    allowClear: false,
                    placeholder: "Pilih",
                    dataType: 'JSON',
                    ajax: {
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/coa/get_level",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term,
                                level: "<?= $detail->acc_level ?>"
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.kode_coa,
                                    text: item.nama
                                });
                            });
                            return {
                                results: results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                        }
                    },

                });

                $("#btn-simpan").on("click", function (e) {
                    e.preventDefault();
                    $(".btn-save").trigger("click");
                });
                const formdo = document.forms.namedItem("form-coa");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-coa").then(
                            async response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200)
                                    window.location.reload();
                            }
                    ).finally(() => {
                        unblockUI(function () {});
                    });
                    event.preventDefault();
                },
                        false

                        );
            });
        </script>
    </body>
</html>