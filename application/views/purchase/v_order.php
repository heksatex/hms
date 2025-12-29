<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .wApp{
                color: blue;
            }
            .donePL{
                color: green;
            }
            .class_exception{
                color: #8f0461;
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
                                <div class="col-md-8 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;cursor:pointer;">
                                    <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                                        <label>
                                            <i class="showAdvanced glyphicon glyphicon-triangle-bottom">&nbsp;</i>Filter
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <?php
                                    if (strtolower($level) === 'direksi') {
                                        ?>
                                        <style>
                                            #btn-tambah {
                                                display:none;
                                            }
                                        </style>
                                        <div class="pull-right text-right">
                                            <button class="btn btn-success btn-sm" id="btn-update-status"  data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                                <i class="fa fa-check">Approve Selected</i>
                                            </button>
                                        </div>
                                        <?php
                                    }
                                    ?>
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
                                                            <select name="status" class="form-control select2" id="status" style="width: 100%" multiple>
                                                                <option></option>
                                                                <option value="draft">Draft</option>
                                                                <option value="waiting_approval">Waiting Approval</option>
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
                                            <th></th>
                                            <th>No PO</th>
                                            <th>Supplier</th>
                                            <th>Tanggal Dokumen</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Note</th>
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
<?php
if (strtolower($level) === 'direksi') {
    ?>
                    const table = $("#tbl-po").DataTable({
                        "iDisplayLength": 50,
                        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
                        "processing": true,
                        "serverSide": true,
                        "stateSave": false,
                        "scrollX": true,
                        "scrollY": "calc(85vh - 250px)",
                        "paging": true,
                        "lengthChange": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "autoWidth": false,
                        "ajax": {
                            "url": "<?php echo site_url('purchase/requestforquotation/list_data') ?>",
                            "type": "POST",
                            "data": function (d) {
                                d.jenis = "RFQ";
                                d.nama_produk = $("#nama_produk").val();
                                d.status = $("#status").val();
                            }
                        },
                        "columnDefs": [
                            {
                                "targets": [0, 4, 6],
                                "orderable": false
                            },
                            {
                                'targets': 0,
                                'checkboxes': {
                                    'selectRow': true
                                }
                            },
                        ],
                        'select': {
                            'style': 'multi'
                        },
                        "createdRow": function (row, data, dataIndex) {
                            if (data[5].toLowerCase() === "waiting approval") {
                                $(row).addClass('wApp');
                            } else if (data[7].toLowerCase() === "waiting_approve") {
                                $(row).addClass('class_exception');
                            }
                        }
                    }
                    );
    <?php
} else {
    ?>
                    const table = $("#tbl-po").DataTable({
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
                        "stateSave": true,
                        "ajax": {
                            "url": "<?php echo site_url('purchase/requestforquotation/list_data') ?>",
                            "type": "POST",
                            "data": function (d) {
                                d.jenis = "RFQ";
                                d.nama_produk = $("#nama_produk").val();
                                d.status = $("#status").val();
                            }
                        },
                        "columnDefs": [
                            {
                                "targets": [0, 4, 6],
                                "orderable": false
                            }
                        ],
                        "createdRow": function (row, data, dataIndex) {
                            if (data[5].toLowerCase() === "waiting approval") {
                                $(row).addClass('wApp');
                            } else if (data[7].toLowerCase() === "waiting_approve") {
                                $(row).addClass('class_exception');
                            }
                        }
                    });
    <?php
}
?>


                $("#search").on("click", function () {
                    table.ajax.reload();
                });

                $("#btn-update-status").unbind("click").off("click").on("click", function (e) {
                    e.preventDefault();
                    var rows_selected = table.column(0).checkboxes.selected();
                    if (rows_selected.length < 1) {
                        alert_notify("fa fa-warning", "Pilihan Item masih kosong", "danger", function () {});
                        return;
                    }

                    confirmRequest("RFQ", "Tandai Approve RFQ ? ", function () {
                        const dataStatus = new Promise((resolve, reject) => {
                            let dt = [];
                            $.each(rows_selected, function (index, rowId) {
                                var splt = rowId.split("|");
                                if (splt[2] === 'waiting_approval') {
                                    dt.push(
                                            {
                                                no_po: splt[1],
                                                status: "approval"
                                            }
                                    );
                                } else if (splt[2] === 'exception' && splt[3] === 'waiting_approve') {
                                    dt.push(
                                            {
                                                no_po: splt[1],
                                                status: "exception"
                                            }
                                    );
                                }
                            });
                            resolve(dt);
                        });
                        dataStatus.then((rsp) => {

                            rsp.forEach(async (dt) => {
                                please_wait(function () {});
                                if (dt.status === "approval") {
                                    await updateStatusWA(dt.no_po, "approval");
                                } else {
                                    await updateStatusExcWA(dt.no_po, "exception");
                                }

                                location.reload();
                            });
                        });
                    });
                });

                const updateStatusWA = async(po, status) => {
                    $.ajax({
                        url: "<?= base_url('purchase/requestforquotation/update_status/') ?>" + po,
                        type: "POST",
                        data: {
                            status: status
                        }
                    });
                };
                const updateStatusExcWA = async(po, status) => {
                    $.ajax({
                        url: "<?= base_url('purchase/purchaseorder/update_status/') ?>" + po,
                        type: "POST",
                        data: {
                            status: "purchase_confirmed",
                            items: 0,
                            totals: 0,
                            default_total: 1
                        }

                    });
                };
            }
            );
        </script>
    </body>
</html>