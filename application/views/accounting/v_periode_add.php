<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
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
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    <?php $this->load->view("admin/_partials/statusbar.php") ?>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Tambah</h3>

                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-acc-periode" id="form-acc-periode" action="<?= base_url('accounting/periode/save') ?>">
                                <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Tahun Fiskal</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2" name="tahun_fiskal" id="tahun_fiskal" required>
                                                    <?php
                                                    $date = date("Y");
                                                    $date--;
                                                    $dates = date("Y");
                                                    for ($date; $date <= ($dates + 3); $date++) {
                                                        ?>
                                                        <<option value="<?= $date ?>"><?= $date ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <button type="submit" id="btn_form_simpan"> Simpan </button>
                                            <input type="hidden" name="list" id="list" required>
                                            <div class="col-xs-4"><button type="button" class="btn btn-primary btn-sm" id="btn-gen">Generate</button></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6">
                                                <label class="form-label">Start Date</label>
                                            </div>
                                            <div class="col-xs-6">
                                                <input type="text" id="startDate" readonly>
                                            </div>
                                            <div class="col-xs-6">
                                                <label class="form-label">End Date</label>
                                            </div>
                                            <div class="col-xs-6">
                                                <input type="text" id="endDate" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12" id="tabel_periode">

                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {

                $("#btn-gen").on("click", function () {
                    $.ajax({
                        url: "<?= base_url('accounting/periode/generate') ?>",
                        type: "POST",
                        data: {
                            tahun: $("#tahun_fiskal").val()
                        },
                        success: function (data) {
                            $("#tabel_periode").html(data.data);
                            $("#startDate").val(data.start);
                            $("#endDate").val(data.end);
                            $("#list").val(data.list);
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
                const form = document.forms.namedItem("form-acc-periode");
                form.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-acc-periode").then(
                            response => {
                                if (response.status === 200)
                                    window.location.replace('<?php echo base_url('accounting/periode/') ?>');

                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                    }, 500);
                                });

                            }).catch(err => {
                        unblockUI(function () {
                            setTimeout(function () {
                                alert_notify('fa fa-close', err?.responseJSON?.message, 'danger', function () {});
                            }, 500);
                        });
                    });
                    event.preventDefault();

                },
                        false
                        );

            })
        </script>
    </body>
</html>