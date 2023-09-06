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
                            <h3 class="box-title">Form Edit - <?=$wa->wa_group?></h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" name="form-wa-group" id="form-wa-group">
                                <div class="form-group">                  
                                    <div class="col-md-12" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-6 col-xs-12">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>WA Group</label></div>
                                                <div class="col-xs-6">
                                                    <input type="text" class="form-control input-sm" name="wa_group" value="<?=$wa->wa_group?>" required/>
                                                    <input type="hidden" class="form-control input-sm" name="_method" value="PUT" required/>
                                                </div>
                                                <button type="submit" id="form_simpan" style="display: block"></button>
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


                const formWaGroup = document.forms.namedItem("form-wa-group");
                formWaGroup.addEventListener(
                        "submit",
                        (event) => {
                    const formData = new FormData($('#form-wa-group')[0]);
                    please_wait(function () {});
                    formData.append('id',"<?=$id?>");
                    const request = new XMLHttpRequest();
                    request.open("POST", "<?php echo base_url('setting/wa_group/update') ?>", true);
                    request.onload = (progress) => {
                        var data = JSON.parse(request.responseText);
                        if (request.status === 200) {
                            alert_modal_warning(data.message);
                            window.location.replace('<?php echo base_url('setting/wa_group') ?>');
                        }
                        unblockUI(function () {
                            setTimeout(function () {
                                alert_notify(data.icon, data.message, data.type, function () {});
                            }, 1000);
                        });
                    };
                    request.send(formData);
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