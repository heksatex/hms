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
                        <form name="input" class="form-horizontal" role="form" method="POST">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-md-4 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                                        <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                                            <label style="cursor:pointer;">
                                                <i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i>
                                                Advanced Search
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="panel panel-default" style="margin-bottom: 5px;">
                                    <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced">
                                        <div class="panel-body" style="padding: 5px">
                                            <div class="form-group col-md-12">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-5"><label><input type="checkbox" name="checkTgl" id="checkTgl"> Tgl. dibuat</label></div>
                                                        <div class="col-md-7">
                                                            <div class='input-group date'>
                                                                <input type="text" class="form-control input-sm" name="tgldari" id="tgldari" readonly="">
                                                                <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class=" col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>s/d</label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <div class='input-group date'>
                                                                <input type="text" class="form-control input-sm" name="tglsampai" id='tglsampai' readonly="">
                                                                <span class="input-group-addon">
                                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-12" style="margin-bottom:0px">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>Customer</label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <input type="text" class="form-control input-sm" name="partner" id="partner">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-5">
                                                            <label>Status </label>
                                                        </div>
                                                        <div class="col-md-7">
                                                            <select type="text" class="form-control input-sm" name="status" id="status">
                                                                <option value='all'>All</option>
                                                                <option value="draft">Draft</option>
                                                                <option value="done">Done</option>
                                                                <option value="cancel">Batal</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <div class="col-xs-8" style="padding-top:0px">
                                                            <button type="button" id="btn-filter" name="submit" class="btn btn-default btn-sm" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Proses</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="col-xs-12 table-responsive">
                            <table id="tbl-pelunasan-piutang" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th class="no">No</th>
                                        <th>No Pelunasan</th>
                                        <th>Tanggal dibuat</th>
                                        <th>Customer</th>
                                        <th>Total Pelunasan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php $this->load->view("admin/_partials/js.php") ?>
    <script>
        $(function() {
            $('#advancedSearch').on('shown.bs.collapse', function() {
                $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
            });

            //* Hide collapse advanced search
            $('#advancedSearch').on('hidden.bs.collapse', function() {
                $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
            });
            // const table = $("#tbl-pelunasan-piutang").DataTable({});
            const table = $('#tbl-pelunasan-piutang').DataTable({
                "iDisplayLength": 50,
                "processing": true,
                "serverSide": true,
                "order": [],
                "scrollX": true,
                "scrollY": "calc(101vh - 250px)",
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "ajax": {
                    "url": "<?php echo site_url('accounting/pelunasanpiutang/get_data') ?>",
                    "type": "POST",
                    "data": function(data) {
                        var check = 0;
                        if ($("#checkTgl").is(":checked") == true) {
                            check = 1;
                        }
                        data.partner = $('#partner').val();
                        data.status = $('#status').val();
                        data.checkTgl = check;
                        data.tgldari = $('#tgldari').val();
                        data.tglsampai = $('#tglsampai').val();
                    },
                },
                "columnDefs": [{
                        "targets": [0],
                        "orderable": false
                    },
                    {
                        "targets": [4],
                        "class": "text-right"
                    }
                ],
            });

            $('#btn-filter').click(function() { //button filter event click
                $('#btn-filter').button('loading');
                table.ajax.reload(function() {
                    $('#btn-filter').button('reset');
                });
            });

            $('#checkTgl').on('change', function() {

                if (this.checked) {

                    // ENABLE
                    $('#tgldari, #tglsampai')
                        .prop('disabled', false)
                        .prop('readonly', false);

                    // DESTROY dulu (penting)
                    if ($('#tgldari').data('DateTimePicker')) {
                        $('#tgldari').data('DateTimePicker').destroy();
                    }
                    if ($('#tglsampai').data('DateTimePicker')) {
                        $('#tglsampai').data('DateTimePicker').destroy();
                    }

                    // INIT ULANG + DEFAULT
                    $('#tgldari').datetimepicker({
                        defaultDate: moment().startOf('month'),
                        format: 'D-MMMM-YYYY',
                        ignoreReadonly: true
                    });

                    $('#tglsampai').datetimepicker({
                        defaultDate: moment(),
                        format: 'D-MMMM-YYYY',
                        ignoreReadonly: true
                    });

                } else {

                    // CLEAR VALUE
                    $('#tgldari, #tglsampai').val('');

                    // CLEAR PICKER
                    if ($('#tgldari').data('DateTimePicker')) {
                        $('#tgldari').data('DateTimePicker').clear();
                    }
                    if ($('#tglsampai').data('DateTimePicker')) {
                        $('#tglsampai').data('DateTimePicker').clear();
                    }

                    // DISABLE
                    $('#tgldari, #tglsampai')
                        .prop('disabled', true)
                        .prop('readonly', true);
                }
            });

        });
    </script>
</body>

</html>