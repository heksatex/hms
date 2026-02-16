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
                            <div class="form-group">
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
                                                <form id="form-search" class="form-horizontal form-search">
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-3">
                                                                    <label class="form-label">Kode</label>
                                                                </div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <input type="text" class="form-control" name="kode" id="kode">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-3"><label class="form-label">Jurnal</label></div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <select class="form-control input-sm select2 jurnal" id="jurnal" name="jurnal" style="width: 100%">
                                                                        <option value=""></option>
                                                                        <?php
                                                                        foreach ($jurnal as $key => $value) {
                                                                            ?>
                                                                            <option value="<?= $value->kode ?>"><?= $value->nama ?></option>
                                                                            <?php
                                                                        }
                                                                        ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="col-sm-12 col-md-12">
                                                                <div class="col-xs-3">
                                                                    <label>Status</label>
                                                                </div>
                                                                <div class="col-xs-9 col-md-9">
                                                                    <select class="form-control select2 input-sm" name="status" id="status" style="width: 100%">
                                                                        <option value=""></option>
                                                                        <option value="unposted">Unposted</option>
                                                                        <option value="posted">Posted</option>
                                                                        <option value="cancel">Cancel</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <div class="col-md-12 col-xs-12">
                                                                <button type="button" class="btn btn-success btn-sm" id="search"><i class="fa fa-search"></i> Cari</button>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <div class="col-md-12 col-xs-12">
                                                                <button type="button" class="btn btn-warning btn-sm" id="reset">Reset</button>
                                                                <button type="reset" class="btn btn-warning btn-sm reset hide"></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-xs-12 table-responsive">
                                <table id="tbl-jurnal" class="table">
                                    <thead>
                                        <tr>
                                            <th class="no">#</th>
                                            <th>Kode</th>
                                            <th>Jurnal</th>
                                            <th>Tanggal dibuat</th>
                                            <!--<th>Tanggal Posting</th>-->
                                            <th>Periode</th>
                                            <th>Origin</th>
                                            <th>Reff Note</th>
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

                $("#btn-tambah").on("click", function () {
                    window.location.href = "<?php echo site_url("{$class}/jurnalentries/add") ?>";
                });

                $(".select2").select2({
                    allowClear: true,
                    placeholder: "Tipe Jurnal"
                });
                const table = $('#tbl-jurnal').DataTable({
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
                        "url": "<?php echo site_url($class.'/jurnalentries/data') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.kode = $("#kode").val();
                            d.jurnal = $("#jurnal").val();
                            d.status = $("#status").val();
                        }
                    },
                    "columnDefs": [
                        {
                            "targets": [0],
                            "orderable": false
                        }
                    ]
                });
                //* Show collapse advanced search
                $('#advancedSearch').on('shown.bs.collapse', function () {
                    $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
                });

                //* Hide collapse advanced search
                $('#advancedSearch').on('hidden.bs.collapse', function () {
                    $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
                });

                $("#reset").on("click", function (e) {
                    e.preventDefault();
                    $(".reset").trigger("click");
                    $(".select2").val('').trigger('change');
                    table.ajax.reload();
                });
                $("#search").on("click", function (e) {
                    e.preventDefault();
                    table.ajax.reload();
                });

            });
        </script>
    </body>
</html>