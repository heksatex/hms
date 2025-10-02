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
            #tabelMemorial.detail tr > *:nth-child(3),#tabelMemorial.detail tr > *:nth-child(4),
            #tabelMemorial.detail tr > *:nth-child(5),#tabelMemorial.detail tr > *:nth-child(8),
            #tabelMemorial.detail tr > *:nth-child(9),#tabelMemorial.detail tr > *:nth-child(10){
                display: none;
            }

            table tbody tr td {
                padding: 0px 5px 0px 5px !important;
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
                            <form class="form-horizontal" method="POST" name="form-jm" id="form-jm" action="<?= base_url('report/rekapentries/export') ?>">
                                <div class="col-md-8" style="padding-right: 0px !important;">
                                    <div class="form-group tanggal_dibuat" style="display: none;">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Tanggal Dibuat</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="tanggal_dibuat" id="tanggal_dibuat" value="<?= $date ?>" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group periode" >
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Periode</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control select2" name="periode" id="periode" style="width: 100%">
                                                    <?php
                                                    $periodeNow = date("Y/m");
                                                    foreach ($periode as $k => $val) {
                                                        ?>
                                                        <option value="<?= $val->periode ?>"  <?= ($periodeNow === $val->periode) ? "selected" : "" ?> ><?= $val->periode ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" >
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">

                                            </div>
                                            <div class="col-xs-8">
                                                <label class="btn btn-default">
                                                    <input type="radio" value="0" class="filter" name="filter" checked/> Dengan Periode
                                                </label> 
                                                <label class="btn btn-default">
                                                    <input type="radio" class="filter" name="filter" value="1" /> Dengan Tanggal Dibuat
                                                </label> 
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Tipe JU</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="hidden" name="jurnal_nm" id="jurnal_nm"/>
                                                <select class="form-control select2" name="jurnal" id="jurnal" style="width: 100%" required>
                                                    <option></option>
                                                    <?php foreach ($jurnal as $key => $value) {
                                                        ?>
                                                        <option value="<?= $value->kode ?>"><?= $value->nama ?></option>
                                                    <?php }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">

                                            </div>
                                            <div class="col-xs-8">
                                                <label class="checkbox-inline">
                                                    <input id="detail" name="detail" type="checkbox" value="1"><strong>Detail</strong></label>
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
                                            <tr>
                                                <th class="style bb ws no">No</th>
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
                                                <th class="style bb ws text-right" >Credit</th>
                                            </tr>
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
            $(function () {

                $(".filter").on("click", function () {
                    if ($(this).val() === "1") {
                        $(".periode").hide();
                        $(".tanggal_dibuat").show();
                        return;
                    }
                    $(".periode").show();
                    $(".tanggal_dibuat").hide();
                });
                var cek = 0;
                $('input[name="tanggal_dibuat"]').daterangepicker({
                    endDate: moment().endOf('month'),
                    startDate: moment().startOf('month'),
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });

                $(".select2").select2({
                    allowClear: true,
                    placeholder: "Pilih"
                });

                $("#detail").on("change", function () {
                    $("#tabelMemorial").toggleClass("detail");
                    if (cek > 0) {
                        $("#search").trigger("click");
                    }
                });

                $("#jurnal").on("select2:select", function () {
                    $("#jurnal_nm").val($("#jurnal :selected").text());
                });

                $("#search").on("click", function () {
                    cek++;
                    $.ajax({
                        url: "<?= base_url('report/rekapentries/search/') ?>",
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
                            alert_notify(sq.responseJSON?.icon, sq.responseJSON?.message, sq.responseJSON?.type, function () {

                            });
                        }
                    });
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