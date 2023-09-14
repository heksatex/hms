
<!DOCTYPE html>
<html lang="en">
    <head>

        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" href="<?php echo base_url('dist/css/bootstrap-switch.min.css') ?>">

    </head>

    <body class="hold-transition skin-black fixed sidebar-mini">
        <!-- Site wrapper -->
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
                </section>

                <!-- Main content -->
                <section class="content">
                    <!--  box content -->
                    <div class="box">
                        <div class="box-body">
                            <div class="col-xs-12 table-responsive">
                                <table id="example1" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>Nama User</th>
                                            <th>Login</th>
                                            <th>Level</th>                  
                                            <th>Departemen</th> 
                                            <th>Telepon / WA</th>
                                            <th>Aktif</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->


            <?php $this->load->view("admin/_partials/modal.php") ?>
        </div>

        <?php $this->load->view("admin/_partials/js.php") ?>
        <script src="<?php echo base_url('dist/js/bootstrap-switch.min.js') ?>"></script>
        <script>

            var table;
            $(document).ready(function () {
                //datatables
                table = $('#example1').DataTable({
                    "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
                            "<'row'<'col-sm-12'tr>>" +
                            "<'row'<'col-sm-5'><'col-sm-7'p>>",
                    "aLengthMenu": [[50, 100, 1000, -1], [50, 100, 1000, "All"]],
                    "iDisplayLength": 50,
                    "processing": true,
                    "serverSide": true,
                    "order": [],

                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,

                    "ajax": {
                        "url": "<?php echo site_url('setting/user/get_data') ?>",
                        "type": "POST"
                    },

                    "columnDefs": [
                        {
                            "targets": [0, 6],
                            "orderable": false,
                        },
                    ],
                    "fnDrawCallback": function (setting, json) {
                        $('.switch_aktif').bootstrapSwitch({
                            onColor: 'primary',
                            offColor: 'danger',
                            onSwitchChange: function (event, state) {
                                $(event.target).closest('.bootstrap-switch').next().val(state ? 'on' : 'off').change();
                                var uclass = $(event.target).attr('class');
                                var valUpdate = state ? 1 : 0
                                $.ajax({
                                    type: "POST",
                                    dataType: "json",
                                    url: '<?php echo base_url('setting/user/set_aktif') ?>',
                                    beforeSend: function (e) {
                                        if (e && e.overrideMimeType) {
                                            e.overrideMimeType("application/json;charset=UTF-8");
                                        }
                                    },
                                    data: {
                                        users: uclass,
                                        aktif: valUpdate
                                    }, success: function (data) {

                                        if (data.status == "failed") {
                                            //jika ada form belum keiisi
                                            unblockUI(function () {
                                                setTimeout(function () {
                                                    alert_notify(data.icon, data.message, data.type, function () {});
                                                }, 1000);
                                                $("." + uclass).trigger('switchChange');
                                            });

                                        } else {
                                            //jika berhasil disimpan/diubah
                                            unblockUI(function () {
                                                setTimeout(function () {
                                                    alert_notify(data.icon, data.message, data.type, function () {});
                                                }, 1000);

                                                $("." + uclass).val(valUpdate)
                                            });
                                        }

                                    }, error: function (xhr, ajaxOptions, thrownError) {

                                        if (xhr.status === 401) {
                                            loginFunc('<?php echo base_url('login/aksi_login'); ?>');
                                        }
                                        unblockUI(function () {});
                                        $("." + uclass).trigger('switchChange');
                                    }
                                });
//                                $("."+test).trigger('switchChange')
                            }
                        });

                    }
                });



            });

        </script>

    </body>
</html>
