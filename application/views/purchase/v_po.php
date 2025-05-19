<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .cancelPL{
                color: red;
            }
            #btn-tambah {
                display: none;
            }
        </style>
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
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                            <div class="col-md-12">
                                <div class="col-md-12 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;cursor:pointer;">
                                    <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                                        <label>
                                            <i class="showAdvanced glyphicon glyphicon-triangle-bottom">&nbsp;</i>Filter
                                        </label>
                                    </div>
                                </div>

                            </div>
                            <br>
                            <br>
                            <div class="col-md-12">
                                <div class="panel panel-default" style="margin-bottom: 0px;">
                                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                                        <div class="panel-body" style="padding: 5px">
                                            <div class="col-md-6 col-xs-12">
                                                <div class="form-group">
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4">
                                                            <label class="form-label">Nama Produk</label>
                                                        </div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <input class="form-control input-sm" name="nama_produk" id="nama_produk" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4">
                                                            <label class="form-label">Status</label>
                                                        </div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <select name="status" class="form-control select2" id="status" multiple style="width: 100%">
                                                                <option value="purchase_confirmed" selected>Purchase Confirmed</option>
                                                                <option value="done" selected>Done</option>
                                                                <option value="cancel">Cancel</option>
                                                                <option value="exception" selected>Exception</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <div class="col-md-2 col-xs-12">
                                            <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="search" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Filter </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 table-responsive">
                            <table id="tbl-po" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="no">No</th>
                                        <th>No PO</th>
                                        <th>Supplier</th>
                                        <th>Tanggal Order</th>
                                        <th>Tanggal Dokumen</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
            </div>
        </section>
    </div>
</div>
<?php $this->load->view("admin/_partials/js.php") ?>
<script>
    $(function () {

        //* Show collapse advanced search
        $('#advancedSearch').on('shown.bs.collapse', function () {
            $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
        });

        //* Hide collapse advanced search
        $('#advancedSearch').on('hidden.bs.collapse', function () {
            $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
        });

        $(".select2").select2({
            allowClear: true,
            placeholder: "Pilih"
        });


        const table = $("#tbl-po").DataTable({
            "iDisplayLength": 50,
            "processing": true,
            "serverSide": true,
            "order": [],
            "scrollX":true,
            "scrollY":"calc(85vh - 250px)",
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?php echo site_url('purchase/purchaseorder/list_data') ?>",
                "type": "POST",
                "data": function (d) {
                    d.nama_produk = $("#nama_produk").val();
                    d.status = $("#status").val();
                    d.level = "<?= $user->level ?>";
                }
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false
                }
            ],
            "createdRow": function (row, data, dataIndex) {
                if (data[6].toLowerCase() === "cancel") {
                    $(row).addClass('cancelPL');
                }
            }
        });
        $("#search").on("click", function () {
            table.ajax.reload();
        });

    });
</script>
</body>
</html>