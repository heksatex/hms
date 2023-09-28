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
                            <h3 class="box-title">Form Edit</h3>
                        </div>
                        <div class="box-body">
                            <form  method="post" class="form-horizontal" name="form-wa-schedule" id="form-wa-schedule" action="<?= base_url('setting/wa_schedule/update') ?>">
                                <div class="form-group">                  
                                    <div class="col-md-12" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-6 col-xs-12">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required">Nama</label></div>
                                                <div class="col-xs-6">
                                                    <input type="text" class="form-control input-sm" name="nama" value="<?= $datas->nama ?>" required/>

                                                </div>

                                                <button type="submit" id="form_simpan" style="display: none"></button>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required">Waktu Kirim</label></div>
                                                <div class="col-xs-6">
                                                    <input type="text" class="form-control input-sm time" name="waktu_kirim" value="<?= $datas->send_time ?>" required/>
                                                    <input type="hidden" name="waktu_kirim_sblm" value="<?= $datas->send_time ?>"/>
                                                    <input type="hidden" name="id" value="<?= $id ?>">
                                                </div>

                                                <button type="submit" id="form_simpan" style="display: none"></button>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required">Group</label></div>
                                                <div class="col-xs-6">
                                                    <select class="form-control input-sm select2" name="group[]" required multiple>
                                                        <?php
                                                        foreach ($group as $key => $value) {
                                                            $seleced = '';
                                                            if (!is_null($datas->groupid)) {
                                                                $seleced = in_array($value->id, $datas->groupid) ? 'selected' : '';
                                                            }
                                                            echo "<option value='$value->id' $seleced>$value->wa_group</option>";
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
                                                            $seleced = '';
                                                            if (!is_null($datas->day)) {
                                                                $seleced = in_array($key, $datas->day) ? 'selected' : '';
                                                            }
                                                            echo "<option value='$key' $seleced>$value</option>";
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
                                                            $seleced = '';
                                                            if (!is_null($datas->day)) {
                                                                $seleced = in_array($i, $datas->day) ? 'selected' : '';
                                                            }
                                                            echo "<option value='$i' $seleced>$i</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required">Setiap (Custom)</label></div>
                                                <div class="col-xs-6">
                                                    <select class="form-control input-sm select2" name="custom[]" id="bycustom" multiple>

                                                        <?php
                                                        foreach ($customSchedule as $key => $value) {
                                                            $seleced = '';
                                                            if (!is_null($datas->day)) {
                                                                $seleced = in_array($key, $datas->day) ? 'selected' : '';
                                                            }
                                                            echo "<option value='$key' $seleced>$value</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required" >Pesan</label></div>
                                                <div class="col-xs-6">
                                                    <textarea class="form-control input-sm resize-ta" id="pesan" name="pesan" required><?= $datas->message ?></textarea>
                                                </div>                                    
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label">Template Footer</label></div>
                                                <div class="col-xs-6">
                                                    <select class="form-control input-sm select2" name="footer">
                                                        <?php
                                                        foreach ($template_footer as $key => $value) {
                                                            $seleced = '';
                                                            if (!is_null($datas->footer_nama)) {
                                                                $seleced = in_array($value->nama, $datas->footer_nama) ? 'selected' : '';
                                                            }
                                                            echo "<option value='$value->nama' $seleced>$value->nama</option>";
                                                        }
                                                        ?>
                                                    </select>
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
                $('.select2').select2({
                    allowClear: true,
                    placeholder: 'Pilih',
                });
                $(".time").datetimepicker({
                    format: 'HH:mm:ss'
                });

                $('#bytanggal').on("select2:selecting", function (e) {
                    $('#byhari').val(null).trigger('change');
                    $('#bycustom').val(null).trigger('change');
                });

                $('#byhari').on("select2:selecting", function (e) {
                    $('#bytanggal').val(null).trigger('change');
                    $('#bycustom').val(null).trigger('change');
                });
                $('#bycustom').on("select2:selecting", function (e) {
                    $('#bytanggal').val(null).trigger('change');
                    $('#byhari').val(null).trigger('change');
                });

                $('#pesan').keyup();
                const formschedule = document.forms.namedItem("form-wa-schedule");
                formschedule.addEventListener(
                        "submit",
                        (event) => {
                    const formData = new FormData($('#form-wa-schedule')[0]);
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