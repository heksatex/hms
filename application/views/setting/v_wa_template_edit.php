<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <!-- main -header -->
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <!-- Menu Side Bar -->
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <!-- Content Wrapper-->
            <div class="content-wrapper">
                <!-- Content Header (Status - Bar) -->
                <section class="content-header">
                    <?php $this->load->view("admin/_partials/statusbar.php") ?>
                </section>
                <!-- Main content -->
                <section class="content">
                    <!--  box content -->
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Edit - <?= $template->nama ?></h3>
                        </div>
                        <div class="box-body">
                            <form  method="post" class="form-horizontal" name="form-wa-template" id="form-wa-template" action="<?=base_url('setting/wa_template/update')?>">
                                <div class="form-group">                  
                                    <div class="col-md-12" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-6 col-xs-12">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required" >Nama</label></div>
                                                <div class="col-xs-6">
                                                    <input type="text" class="form-control input-sm" name="nama" placeholder="Nama Template" value="<?= $template->nama ?>" required/>
                                                    <small class="form-text text-muted text-sm">
                                                        Hanya alphanumeric
                                                    </small>
                                                </div>
                                                <input type="hidden" name="id" value="<?= $id ?>">
                                                <button type="submit" id="form_simpan" style="display: none"></button>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required" >Template</label></div>
                                                <div class="col-xs-8">
                                                    <textarea type="text" class="form-control input-sm" name="template" required><?= $template->template ?></textarea>
                                                    <small class="form-text text-muted text-sm">
                                                        {text} untuk diganti dengan value dinamis <strong>Contoh</strong>: No {sku} sudah tersedia.
                                                    </small>
                                                </div>                                    
                                            </div>
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


                const formWaGroup = document.forms.namedItem("form-wa-template");
                formWaGroup.addEventListener(
                        "submit",
                        (event) => {
                    const formData = new FormData($('#form-wa-template')[0]);
                    please_wait(function () {});
                    request("form-wa-template").then(
                            response => {
                                if (response.status === 200)
                                    window.location.replace('<?php echo base_url('setting/wa_template') ?>');

                                if (response.status === 401) {
                                    loginFunc('<?php echo base_url('login/aksi_login'); ?>');
                                    return
                                }
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                    }, 1000);
                                });
                            }).catch(err => {
                        unblockUI(function () {});
                        alert_modal_warning("Hubungi Dept IT");
                    });
                    event.preventDefault();
                },
                        false
                        );

                $("#btn-simpan").on('click', function () {
                    $("#form_simpan").trigger("click");

                });

            })
        </script>
    </body>
</html>