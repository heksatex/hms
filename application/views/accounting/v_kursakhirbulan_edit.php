<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
            #btn-confirm{
                display: none;
            }
        </style>
        <?php
        $this->load->view("admin/_partials/head.php");

        if ($datas->status === 'confirm') {
            ?>
            <style>
                #btn-simpan,#btn-confirm{
                    display: none;
                }
            </style>
            <?php
        }
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
                        $data['jen_status'] = $datas->status;
                        $this->load->view("admin/_partials/statusbar-new.php", $data);
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">
                        <form class="form-horizontal" method="POST" name="form-kurs" id="form-kurs" action="<?= base_url("accounting/kursakhirbulan/update/{$id}") ?>">
                            <input type="hidden" name="ids" value="<?= $datas->id ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title"><?= $datas->no ?> </h3>
                            </div>
                            <div class="box-body">
                                <div class="col-md-8col-xs-12">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="field-group">
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label required">Bulan</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input name="bulan" id="bulan" class="form-control input-sm" type="month" value="<?= $datas->bulan ?>" <?= ($datas->status === "confirm") ? "readonly" : "" ?>>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label required">Currency</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input type="hidden" name="symcurr" id="symcurr">
                                                        <select class="form-control input-sm currency" name="currency" id="currency" style="width: 100%" <?= ($datas->status === "confirm") ? "disabled" : "" ?>>
                                                            <option value="<?= $datas->curr ?>" selected><?= $datas->curr ?></option>
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
                                                    <div class="col-xs-4"><label class="form-label required">Kurs</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input name="kurs" id="kurs" <?= ($datas->status === "confirm") ? "readonly" : "" ?>
                                                               class="form-control input-sm" type="text" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' value="<?= number_format($datas->kurs, 2, ".", ",") ?>">
                                                        <button type="submit" class="btn-simpan" style="display: none;"></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer bf">

                            </div>
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
            const preview = (() => {
                $.ajax({
                    url: "<?= base_url("accounting/kursakhirbulan/generate") ?>",
                    data: {
                        bulan: $("#bulan").val(),
                        currency: $("#currency").val(),
                        kurs: $("#kurs").val(),
                    },
                    type: "POST",
                    beforeSend: function (xhr) {
//                        please_wait(function () {
//
//                        });
                    },
                    success: function (data) {
                        $(".bf").html(data.data);
                        $("#btn-simpan").hide();
                        if (data.count > 0)
                            $("#btn-simpan").show();
                    },
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {});

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert_notify("fa fa-warning", jqXHR?.responseJSON?.message, "danger", function () {}, 500);
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
            $(function () {

                $("#currency").select2({
                    placeholder: "Pilih Currency",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/kursakhirbulan/get_currency",
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
                                    id: item.nama,
                                    text: item.nama,
                                    symbol: item.symbol
                                });
                            });
                            return {
                                results: results
                            };
                        }
                    }
                });

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

                $("#btn-simpan").on("click", function (e) {
                    e.preventDefault();
                    $(".btn-simpan").trigger("click");
                });


                const formdo = document.forms.namedItem("form-kurs");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-kurs").then(
                            async response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200) {
                                    window.location.reload();
                                }
                            }
                    );
                    event.preventDefault();
                },
                        false
                        );

                $("#btn-confirm").unbind("click").off("click").on("click", function () {
                    confirmRequest("Kurs Akhir Bulan", "Confirm Kurs Akhir Bulan ? ", function () {
                        $.ajax({
                            url: "<?= base_url("accounting/kursakhirbulan/confirm/{$id}") ?>",

                            type: "POST",
                            beforeSend: function (xhr) {
                                please_wait(function () {

                                });
                            },
                            success: function (data) {
                                window.location.reload();
                            },
                            complete: function (jqXHR, textStatus) {
                                unblockUI(function () {});

                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                alert_notify("fa fa-warning", jqXHR?.responseJSON?.message, "danger", function () {}, 500);
                            }
                        });
                    });
                });
            });
        </script>
        <?php
        if ($datas->status === "draft") {
            ?>
            <script>
                preview();
            </script>
            <?php
        }
        ?>
    </body>
</html>