<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <?php $this->load->view("admin/_partials/js.php") ?>
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
                <?php
                $this->load->view("admin/_partials/sidebar.php");
                ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header" >
                    <div id ="status_bar">
                        <?php
//                        $data['jen_status'] = $picklist->status;
//                        $this->load->view("admin/_partials/statusbar.php", $data)
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">
                        <?php
                        $this->load->view("warehouse/v_do_add", $picklist);
                        ?>
                        <div class="row">
                            <div class="col-md-12 table-responsive over">
                                <table class="table table-condesed table-hover rlstable  over" width="100%" id="delivery-item" >
                                    <thead>                          
                                        <tr>

                                            <th class="style" width="10px">No</th>
                                            <?php if ((int) $picklist->type_bulk_id === 1) { ?>
                                                <th class="style">BAL ID</th>
                                            <?php } ?>
                                            <th class="style">Deskripsi</th>
                                            <th class="style">Corak PO Buyer</th>
                                            <th class="style">Warna</th>
                                            <th class="style">Total LOT / PCS</th>
                                            <th class="style">Total QTY</th>
                                            <th class="style">Satuan</th>
                                            <th class="style">#</th>

                                        </tr>
                                    </thead>
                                </table>
                            </div>

                        </div>
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>

                <?php
                $this->load->view("admin/_partials/footer.php");
                ?>
            </footer>
        </div>
        <script>
//            $(function () {
            const table = $("#delivery-item").DataTable({
                "iDisplayLength": 10,
                "processing": true,
                "serverSide": true,
                "order": [],
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "ajax": {
                    "url": "<?= base_url('warehouse/deliveryorder/list_data_detail/' . $picklist->type_bulk_id) ?>",
                    "type": "POST",
                    "data": function (d) {
                        d.pl = $('#picklist').val();
                        d.bulk = $('#bal').val();
//                        d.not_in = $("#remove_item").val();
                        d.not_in = JSON.stringify(listRemoveItem);
                    }
                },
                "columnDefs": [
                    {
                        "targets": [0],
                        "orderable": false
                    }
                ],
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        "text": '<i class="fa fa-list"> <span>Detail Item</span>',
                        "className": "btn btn-default detail-data",
                        "action": function (e, dt, node, config) {
                            e.preventDefault();
                            $("#tambah_data").modal({
                                show: true,
                                backdrop: 'static'
                            });
                            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                            $('.modal-title').text('List Detail Item');
                            $.post("<?= base_url('warehouse/deliveryorder/list_detail_view_add') ?>",
                                    {
                                        bulk: $('#bal').val(),
                                        pl: $('#picklist').val(),
                                        not_in: JSON.stringify(listRemoveItem),
                                        type: '<?= $picklist->type_bulk_id ?>'
                                    },
                                    function (data) {
                                        setTimeout(function () {
                                            $(".tambah_data").html(data.data);
                                        }, 1000);
                                    });
                        }
                    }
                ]
            });
//            });
        </script>
    </body>
</html>