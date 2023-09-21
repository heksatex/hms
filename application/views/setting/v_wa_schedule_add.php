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
                            <h3 class="box-title">Form Tambah</h3>
                        </div>
                        <div class="box-body">
                            <form  method="post" class="form-horizontal" name="form-wa-schedule" id="form-wa-schedule" action="<?= base_url('setting/wa_schedule/simpan') ?>">
                                <div class="form-group">                  
                                    <div class="col-md-12" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-6 col-xs-12">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required">Waktu Kirim</label></div>
                                                <div class="col-xs-6">
                                                    <input type="text" class="form-control input-sm time" name="waktu_kirim"  required/>
                                                </div>
                                                <button type="submit" id="form_simpan" style="display: none"></button>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>Group</label></div>
                                                <div class="col-xs-6">
                                                    <select class="form-control input-sm select2" name="group[]" multiple>
                                                        <?php
                                                        foreach ($group as $key => $value) {
                                                            echo "<option value='$value->id'>$value->wa_group</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required">Setiap (Hari)</label></div>
                                                <div class="col-xs-6">
                                                    <select class="form-control input-sm select2" name="hari[]" id="byhari" multiple>
                                                        <?php
                                                        foreach ($days as $key => $value) {
                                                            echo "<option value='$key'>$value</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required">Setiap (Tanggal)</label></div>
                                                <div class="col-xs-6">
                                                    <select class="form-control input-sm select2" name="tanggal[]" id="bytanggal" multiple>
                                                        <?php
                                                        for ($i = 1; $i <= 31; $i++) {
                                                            echo "<option value='$i'>$i</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required" >Pesan</label></div>
                                                <div class="col-xs-6">
                                                    <textarea type="text" class="form-control input-sm" name="pesan" required></textarea>
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
                $(".time").datetimepicker({
                    format: 'HH:mm:ss',
                });
                $('.select2').select2();
                $('#bytanggal').on("select2:selecting", function (e) {
                    $('#byhari').val(null).trigger('change');
                });
                
                $('#byhari').on("select2:selecting", function (e) {
                    $('#bytanggal').val(null).trigger('change');
                });
                
                const formschedule = document.forms.namedItem("form-wa-schedule");
                formschedule.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-wa-schedule").then(
                            response => {
                                if (response.status === 200)
                                    window.location.replace('<?php echo base_url('setting/wa_schedule') ?>');

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