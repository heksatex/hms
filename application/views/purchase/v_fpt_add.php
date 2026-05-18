<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .cancelPL{
                color: red;
            }
            .donePL{
                color: green;
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
                    <?php $this->load->view("admin/_partials/statusbar.php") ?>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Tambah</h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal">
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Supplier</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2" name="supp" id="supp">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!--                                        <div class="form-group">
                                                                                    <div class="col-xs-12">
                                                                                        <div class="col-xs-4"><label class="form-label required">Perioritas</label></div>
                                                                                        <div class="col-xs-8 col-md-8">
                                                                                            <select class="form-control input-sm select2" name="prioritas" id="prioritas">
                                                                                                <option></option>
                                                                                                <option value="urgent">Urgent</option>
                                                                                                <option value="normal">Normal</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>-->
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
<!--                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Tanggal Order</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="date" class="form-control input-sm" name="order_date" id="order_date" required>
                                                </div>
                                            </div>
                                        </div>-->
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Note</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <textarea type="text" class="form-control input-sm resize-ta" id="note" name="note"></textarea>
                                                </div>                                    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 table-responsive">
                                        <table id="tbl-cfb" class="table">
                                            <thead>
                                                <tr>
                                                    <th class="no">#</th>
                                                    <th>Kode CFB</th>
                                                    <th>Kode Produk</th>
                                                    <th>Nama Produk</th>
                                                    <th>QTY</th>
                                                    <th>SO</th>
                                                    <th>Schedule Date</th>
                                                    <th>Departement Tujuan</th>
                                                    <th>Schedule Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                            </tbody>
                                            <tfoot id="tfooter">
                                                <tr>
                                                <tr style="display: none;">
                                                    <td colspan="8">
                                                        <a class="add-rfq"><i class="fa fa-plus"></i></a>
                                                    </td>
                                                </tr>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view("admin/_partials/modal.php") ?>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {
                $("#btn-approve").hide();
                $("#btn-cancel").hide();
                $("#supp").select2({
                    allowClear: true,
                    placeholder: "Supplier",
                    ajax: {
                        url: "<?= site_url('purchase/requestforquotation/get_supp') ?>",
                        data: function (params) {
                            var query = {
                                search: params.term
                            };
                            return query;
                        },
                        processResults: function (data) {
                            return {
                                results: data.data
                            };
                        }
                    }
                });
                $("#prioritas").select2({
                    allowClear: true,
                    placeholder: "Prioritas"
                });
                const table = $('#tbl-cfb').DataTable({
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
                        "url": "<?php echo site_url('purchase/callforbids/list_data') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0],
                            "orderable": false
                        },
                        {
                            'targets': 0,
                            'checkboxes': {
                                'selectRow': true
                            }
                        }
                    ],
                    "createdRow": function (row, data, dataIndex) {
                        if (data[6].toLowerCase() === "urgent") {
                            $(row).addClass('cancelPL');
                        }
                    }
                });

                $("#btn-simpan").on("click", function (e) {
                    var rows_selected = table.column(0).checkboxes.selected();
                    if (rows_selected.length < 1) {
                        alert_notify("fa fa-warning", "Pilihan Item masih kosong", "danger", function () {});
                        return;
                    }
                    $("#btn-simpan").button("loading");
                    const data = new Promise((resolve, reject) => {
                        let dt = [];
                        $.each(rows_selected, function (index, rowId) {
                            var splt = rowId.split("#");
                            if ("confirm" !== splt[splt.length - 1]) {
                                throw new Error("Kode Produk <strong>" + splt[2] + "</strong> Tidak Dalam Status confirm");
                            }
                            dt.push(rowId);
                        });
                        resolve(dt);
                    });
                    e.preventDefault();
                    data.then((rsp) => {
                        $("#tambah_data").modal({
                            show: true,
                            backdrop: 'static'
                        });
                        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                        $('.modal-title').text('Create FPT');
                        $.post("<?= base_url('purchase/callforbids/create_rfq/') ?>",
                                {
                                    data: JSON.stringify(rsp),
                                    note: $("#note").val(),
                                    prio: $("#prioritas").val(),
                                    tgl: $("#order_date").val(),
                                    supp: $("#supp").val() + ":" + $("#supp").text()

                                }, function (data) {
                            setTimeout(function () {
                                $(".tambah_data").html(data.data);
                                $("#btn-tambah").html("Tambahkan");
                                $("#jenis").val("FPT");
                            }, 500);
                        });
                    }).catch(e => {
                        alert_notify("fa fa-warning", e.message, "danger", function () {});
                    });
                    $("#btn-simpan").button("reset");
                });
            });
        </script>
    </body>
</html>