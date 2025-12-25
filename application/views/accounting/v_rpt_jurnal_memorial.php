<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
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
            #tabelMemorial{
                max-height: 100vh
            }

            .ws{
                white-space: nowrap;
            }

            #pagination {
                display: inline-block;
                padding-left: 0;
                border-radius: 4px;
                /*padding-top: 5px;*/

            }

            #pagination>a, #pagination>strong {
                position: relative;
                float: left;
                padding: 4px 8px;
                margin-left: -1px;
                line-height: 1.42857143;
                color: #337ab7;
                text-decoration: none;
                background-color: #fff;
                border: 1px solid #ddd;
            }
            table tbody tr td {
                padding: 0px 5px 0px 5px !important;
            }
            .td-nominal {
                width: 140px;
            }
            .td-no-perk {
                width: 80px;
            }
            .td-perkiraan-global {
                width :200px;
            }

        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php $this->load->view("admin/_partials/topbar.php") ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-jm" id="form-jm" action="<?= base_url('accounting/jurnalmemorial/export') ?>">
                                <div class="col-md-8" style="padding-right: 0px !important;">

                                    <div class="form-group" >
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Periode</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="periode" id="periode" value="<?= date("Y-m-d"); ?>" class="form-control input-sm"/>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Jurnal Memorial</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="hidden" name="jurnal_nm" id="jurnal_nm"/>
                                                <select class="form-control select2" name="jurnal" id="jurnal" style="width: 100%" required>
                                                    <option></option>
                                                    <?php foreach ($jurnal as $key => $value) {
                                                        ?>
                                                        <option value="<?= $key ?>"><?= $value ?></option>
                                                    <?php }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="checkbox-inline">
                                                    <input  name="filter" type="radio" value="global" class="form control filter-global" checked>&nbsp;<strong>Global</strong></label>
                                            </div>
                                            <div class="col-xs-4">
                                                <label class="checkbox-inline filter-kredit">
                                                    <input  name="filter" type="radio" value="detail" class="form control" >&nbsp;<strong class="filter-kredit-text">Rekap Kredit</strong></label>
                                            </div>
                                            <div class="col-xs-4">
                                                <label class="checkbox-inline filter-debet">
                                                    <input  name="filter" type="radio" value="detail_2" class="form control" >&nbsp;<strong>Rekap Debet</strong></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="search" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                                    <button type="submit" class="btn btn-sm btn-default" name="btn-excel" id="export" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o"  style="color:green"></i> Excel</button>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-12 table-responsive example1 divListviewHead">
                                    <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                        <table id="tabelMemorial" class="table table-condesed table-hover detail" border="1">
                                            <thead id="thead">
                                                <tr>
<!--                                                    <th class="style bb ws no" >No</th>
                                                    <th class="style bb ws" >Periode</th>
                                                    <th class="style bb ws" >Tanggal Dibuat</th>
                                                    <th class="style bb ws" >No Bukti</th>
                                                    <th class="style bb ws" >Origin</th>
                                                    <th class="style bb ws" >Kode ACC</th>
                                                    <th class="style bb ws" >Nama ACC</th>
                                                    <th class="style bb ws" >Kerangan</th>
                                                    <th class="style bb ws" >Reff Note</th>
                                                    <th class="style bb ws" >Partner</th>
                                                    <th class="style bb ws text-right" >Debit</th>
                                                    <th class="style bb ws text-right" >Credit</th>-->
                                                </tr>
                                            </thead>

                                            <tbody id="tBody" class="ws">

                                            </tbody>
                                        </table>
                                    </div>
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
            var NoDetailDebit = ["pen_kb", "peng_kb","pen_kv", "peng_kv"];
            var NoDetailKredit = ["peng_kb","peng_kv"];
            $(function () {
                var cek = 0;
                $('input[name="periode"]').daterangepicker({
                    endDate: moment().endOf('month'),
                    startDate: moment().startOf('month'),
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

                $(".select2").select2({
                    allowClear: true,
                    placeholder: "Pilih"
                });

                $('input[name="filter"]').on("change", function () {
//                    filters();
//                    search();
                });
                const filters = (() => {
                    $.ajax({
                        url: "<?= base_url('accounting/jurnalmemorial/jm') ?>",
                        type: "GET",
                        data: {
                            jm: $("#jurnal").val(),
                            filter: $('input[name="filter"]:checked').val()
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        complete: function (sq) {
                            unblockUI(function () {}, 100);
                        },
                        success: ((data) => {
                            $("#thead").html(data.data);
                        })
                    });
                });

                $("#jurnal").on("select2:select", function () {
                    if (NoDetailDebit.includes($("#jurnal").val())) {
                        $(".filter-debet").hide();
                    } else {
                        $(".filter-debet").show();
                    }

                    if (NoDetailKredit.includes($("#jurnal").val())) {
                        $(".filter-kredit-text").html("Rekap Debet");
                    } else {
                        $(".filter-kredit-text").html("Rekap Kredit");
                    }

//                    filters();
//                    search();
                });

                const search = (() => {
                    cek++;
                    $.ajax({
                        url: "<?= base_url('accounting/jurnalmemorial/search/') ?>",
                        type: "POST",
                        data: {
                            periode: $("#periode").val(),
                            tanggal_dibuat: $("#tanggal_dibuat").val(),
                            jurnal: $("#jurnal").val(),
                            detail: $("#detail").is(":checked") ? 1 : 0,
                            jurnal_nm: $("#jurnal :selected").text(),
                            filter: $("input[name='filter']:checked").val()
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
                            var msg = "Tidak Ada Rekapitulasi 2.";
                            if (sq.responseJSON?.message !== undefined)
                                msg = sq.responseJSON.message;
                            alert_notify("fa fa-error", msg, "danger", function () {
                                $("#tBody").html("");
                            });
                        }
                    });
                });

                $("#search").on("click", function () {
                    filters();
                    search();
                });

                const formrd = document.forms.namedItem("form-jm");
                formrd.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-jm").then(
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
            });
        </script>
    </body>
</html>