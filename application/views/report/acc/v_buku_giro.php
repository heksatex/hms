<!doctype html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <style type="text/css">
            .bolden{
                font-family:"Arial Black"
            }
            h3{
                display: block !important;
                text-align: center !important;
            }

            .divListviewHead table  {
                display: block;
                height: calc( 100vh - 250px );
                overflow-x: auto;
            }
            #tblgiro{
                max-height: 100vh
            }

            .ws{
                white-space: nowrap;
            }

        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse" >
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu-new.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar-new.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                            <form id="form-search" name="form-search" class="form-horizontal form-search" action="<?= base_url('report/bukugiro/export') ?>" method="post">
                                <div class="col-md-8" style="padding-right: 0px !important;">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="form-group">
                                                <div class="col-md-6 col-xs-12">
                                                    <div class="col-xs-3">
                                                        <label class="form-label">Bank</label>
                                                    </div>
                                                    <div class="col-xs-9 col-md-9">
                                                        <select class="form-control input-sm select2 no_acc" name="kode_coa" id="kode_coa">
                                                            <option value=""></option>
                                                            <?php
                                                            foreach ($coa as $key => $value) {
                                                                ?>
                                                                <option value="<?= $value->kode_coa ?>"><?= "({$value->kode_coa}) - {$value->nama}" ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">Periode</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input type="text" class="form-control" name="tanggal" id="tanggal">
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="search" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                                    <button type="submit" class="btn btn-sm btn-default" name="btn-excel" id="export" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o"  style="color:green"></i> Excel</button>
                                </div>
                                <br>
                                <br>

                            </form>
                            <div class="col-md-12 table-responsive divListviewHead">
                                <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                    <table id="tblgiro" class="table table-condesed table-hover" border="1">
                                        <tr>
                                            <th  class="style bb ws no" >No</th>
                                            <th class="style bb ws">Tanggal</th>
                                            <th class="style bb ws">No Bukti</th>
                                            <th class="style bb ws">Uraian</th>
                                            <th class="style bb ws">No BBK</th>
                                            <th class="style bb ws text-right">Debet</th>
                                            <th class="style bb ws text-right">Kredit</th>
                                            <th class="style bb ws text-right">Saldo</th>
                                        </tr>
                                        <tbody id="tBody" class="ws">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <script>
            $(function () {
                $(".select2").select2({
                    placeholder: "Pilih Bank",
                    allowClear: true
                });
                $('#tanggal').daterangepicker({
//                    autoUpdateInput: false,
                    endDate: moment().startOf('day'),
                    startDate: moment().startOf('day').add(-1, 'week'),
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    ranges: {
                        'H': [moment(), moment()],
                        '1..H': [moment().startOf('month'), moment()],
                        '1..31': [moment().startOf('month'), moment().endOf('month')],
                        '1..P': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    }
                });

                $("#search").on("click", function () {
                    $.ajax({
                        url: "<?= base_url('report/bukugiro/search') ?>",
                        type: "POST",
                        data: {
                            tanggal: $("#tanggal").val(),
                            kode_coa: $("#kode_coa").val()
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        success: ((data) => {
                            $("#tBody").html(data.data);
                            unblockUI(function () {}, 100);
                        }),
                        complete: function (sq) {
                            unblockUI(function () {}, 100);
                        },
                        error: function (sq) {
                            alert_notify(sq.responseJSON?.icon, sq.responseJSON?.message, sq.responseJSON?.type, function () {

                            });
                        }
                    });
                });
                const formrd = document.forms.namedItem("form-search");
                formrd.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-search").then(
                            response => {
                                alert_notify(response.data.icon, response.data.message, response.data.type, function () {

                                });
                                if (response.status === 200) {
                                    const a = document.createElement('a');
                                    a.style.display = 'none';
                                    a.href = response.data.data;
                                    a.download = response.data.text_name;
                                    document.body.appendChild(a);
                                    a.click();
                                }
                            }
                    ).catch().finally(() => {
                        unblockUI(function () {}, 100);
                    });
                    event.preventDefault();
                },
                        false
                        );
            })
        </script>
    </body>
</html>