<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
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
                                                            <label class="form-label">Supplier</label>
                                                        </div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <select name="supplier" class="form-control select2" id="supplier" style="width: 100%" multiple>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4">
                                                            <label class="form-label">Status</label>
                                                        </div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <select name="status" class="form-control select2" id="status" style="width: 100%">
                                                                <option></option>
                                                                <option value="draft">Draft</option>
                                                                <option value="done">Done</option>
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
                                <table id="tbl-invs" class="table">
                                    <thead>
                                        <tr>
                                            <th class="style">#</th>
                                            <th>Invoice</th>
                                            <th>Supplier</th>
                                            <th>No Inv Supp</th>
                                            <th>TGL Inv Supp</th>
                                            <th>No SJ Supplier</th>
                                            <th>No PO</th>
                                            <th>Tgl dibuat</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
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

                const table = $('#tbl-invs').DataTable({
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
                        "url": "<?php echo site_url('purchase/invoice/data') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.supplier = $("#supplier").val();
                            d.status = $("#status").val();
                        }
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 8],
                            "orderable": false
                        }
                    ]

                });

                $("#search").on("click", function () {
                    table.ajax.reload();
                });

                $(".select2").select2({
                    allowClear: true,
                    placeholder: "pilih"
                });

                $("#supplier").select2({
                    allowClear: true,
                    placeholder: "Supplier",
                    ajax: {
                        url: "<?= site_url('purchase/requestforquotation/get_supp') ?>",
                        data: function (params) {
                            var query = {
                                search: params.term
                            }
                            return query;
                        },
                        processResults: function (data) {
                            return {
                                results: data.data
                            };
                        }
                    }
                });

            })
        </script>
    </body>
</html>