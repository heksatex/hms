<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view("admin/_partials/head.php") ?>

</head>

<body class="hold-transition skin-black fixed sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- main -header -->
        <header class="main-header">
            <?php $this->load->view("admin/_partials/main-menu.php") ?>
            <?php
            $data['deptid']     = $id_dept;
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
                                        <th>Nama</th>
                                        <th>Short</th>
                                        <th>Jenis</th>
                                        <th>Jual</th>
                                        <th>Beli</th>
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

    <script type="text/javascript">
        var table;
        $(document).ready(function() {

            //datatables
            table = $('#example1').DataTable({
                // "stateSave": true,
                "dom": "<'row'<'col-sm-4'l><'col-sm-5'i><'col-sm-3'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-5'><'col-sm-7'p>>",
                "aLengthMenu": [
                    [50, 100, 1000, -1],
                    [50, 100, 1000, "All"]
                ],
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
                    "url": "<?php echo site_url('warehouse/uom/get_data') ?>",
                    "type": "POST",
                    // "data": function(data) {
                    //     data.departemen = $('#departemen').val();
                    //     data.nama_lokasi = $('#nama_lokasi').val();
                    //     data.arah_panah = $('#arah_panah').val();
                    //     data.status = $('#status').val();
                    // },
                },

                "columnDefs": [{
                        "targets": [0],
                        "orderable": false,
                    },
                    {
                        "targets": 2,
                        render: function(data, type, full, meta) {
                            return "<div class='text-wrap width-100'>" + data + "</div>";
                        }
                    },
                ],

            });

            $('#btn-filter').click(function() { //button filter event click
                $('#btn-filter').button('loading');
                table.ajax.reload(function() {
                    $('#btn-filter').button('reset');
                }); //just reload table
            });

            $('#nama_lokasi').keydown(function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                    $('#btn-filter').button('loading');
                    table.ajax.reload(function() {
                        $('#btn-filter').button('reset');
                    });
                }
            });

        });

        function view_uom(id) {
            $("#edit_data2").modal({
                show: true,
                backdrop: 'static'
            });
            $(".edit_data2").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $(".modal-title").text('View Uom');
            $.post('<?php echo site_url() ?>warehouse/uom/view_uom', {
                    id: id
                },
                function(html) {
                    setTimeout(function() {
                        $(".edit_data2").html(html);
                    }, 1000);
                }
            );
        }


        $("#btn-tambah").on("click", function(e) {
            $("#tambah_data").modal({
                show: true,
                backdrop: 'static'
            });
            $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('id', "btn-tambah-uom");
            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $('.modal-title').text('Add Uom');
            $.post('<?php echo site_url() ?>warehouse/uom/add_uom',
                function(html) {
                    setTimeout(function() {
                        $(".tambah_data").html(html);
                    }, 1000);
                }
            );
        });

        $(".modal").on('hidden.bs.modal', function() {
            $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah-uom").attr('id', "btn-tambah");
            //$("#tambah_data .modal-dialog .modal-content .tambah_data").html('');
            table.ajax.reload(function() {});
        });
    </script>

</body>

</html>