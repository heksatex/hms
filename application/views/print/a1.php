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
                $data['deptid'] = 'MWT';
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
                            <h3 class="box-title">Form Tambah</h3>
                        </div>
                        <div class="box-body">
                            <form  method="get" class="form-horizontal" name="form-wa-template" id="form-wa-template" action="<?= base_url('setting/wa_template/test') ?>">
                                <div class="form-group">                  
                                    <div class="col-md-12" >
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="col-md-6 col-xs-12">

                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-8">
                                                        <button type="submit" id="print" class="btn btn-success" >print</button>
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
                     <?php $this->load->view("admin/_partials/js.php") ?>
        </div>
        <script>


            $(function () {
                function print_voucher() {

                    var win = window.open();
                    win.document.write($("#printed").html());
                    win.document.close();
                    win.print();
                    win.close();
                    $("#nama").focus();

                }
                const formWaGroup = document.forms.namedItem("form-wa-template");
                formWaGroup.addEventListener(
                        "submit",
                        (event) => {
                    const formData = new FormData($('#form-wa-template')[0]);
                    please_wait(function () {});
                    request("form-wa-template").then(
                            response => {
                                if (response.status === 200) {
                                    var divp = document.getElementById('printed');
                                    divp.innerHTML = response.data.data;
                                    print_voucher();
                                }


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
            })


        </script>

    </body>
</html>