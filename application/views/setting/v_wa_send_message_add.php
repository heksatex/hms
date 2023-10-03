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
                            <form  method="post" class="form-horizontal" name="form-wa-message" id="form-wa-message" action="<?= base_url('setting/wa_send_message/kirim') ?>">
                                <div class="form-group">                  
                                    <div class="col-md-12" >
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-6 col-xs-12">

                                            <button type="submit" id="form_simpan" style="display: none"></button>

                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required">Group</label></div>
                                                <div class="col-xs-6">
                                                    <select class="form-control input-sm select2" name="group[]" required multiple>
                                                        <?php
                                                        foreach ($group as $key => $value) {
                                                            echo "<option value='$value->wa_group'>$value->wa_group</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label">Mention</label></div>
                                                <div class="col-xs-6">
                                                    <select class="form-control input-sm" id="list_user" name="mention[]" multiple>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label required" >Pesan</label></div>
                                                <div class="col-xs-6">
                                                    <textarea type="text" class="form-control input-sm resize-ta" id="pesan" name="pesan" required></textarea>
                                                </div>                                    
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label class="form-label">Template Footer</label></div>
                                                <div class="col-xs-6">
                                                    <select class="form-control input-sm select2" name="footer">
                                                        <option></option>
                                                        <?php
                                                        foreach ($template_footer as $key => $value) {
                                                            echo "<option value='$value->nama'>$value->nama</option>";
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
                $("#list_user").select2({
                    ajax: {
                        url: "<?= base_url('setting/wa_send_message/get_user') ?>",
                        delay: 250,
                        type: "POST",
                        data: function (params) {
                            var query = {
                                search: params.term,
                            }

                            return query;
                        },
                        processResults: function (data) {
                            return {
                                results: JSON.parse(data)
                            };
                        }
                    }
                });
                $('#pesan').keyup();
                const formschedule = document.forms.namedItem("form-wa-message");
                formschedule.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-wa-message").then(
                            response => {
                                if (response.status === 200)
                                    window.location.replace('<?php echo base_url('setting/wa_send_message') ?>');

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