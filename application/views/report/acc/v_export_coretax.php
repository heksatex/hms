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
                            <form class="form-horizontal" method="POST" name="form-jm" id="form-jm" action="<?= base_url('accounting/exportcoretax/export') ?>">
                                <div class="col-md-8" style="padding-right: 0px !important;">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="form-group">
                                            <div class="col-md-6 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">Periode</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" class="form-control" name="periode" id="periode">
                                                </div>
                                            </div>
                                            <div class="col-md-6 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">Customer</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2 customer" name="customer" id="customer" style="width: 100%">

                                                    </select>
                                                </div>
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
                                        <table id="tblcoretax" class="table table-condesed table-hover detail" border="1" style="min-width: 115%; padding: 0 0 0 0 !important;">
                                            <tr>
                                                <th class="style bb ws no text-center">No</th>
                                                <th class="style bb ws text-center" style="width: 120px;">No SJ</th>
                                                <th class="style bb ws" style="width: 80px;">Tanggal</th>
                                                <th class="style bb ws text-center" >uraian</th>
                                                <th class="style bb ws text-center" >Harga</th>
                                                <th class="style bb ws text-center" style="width: 80px;">Qty</th>
                                                <th class="style bb ws text-center" style="width: 100px;">Diskon</th>
                                                <th class="style bb ws text-center" >DPP</th>
                                                <th class="style bb ws text-center"style="width: 120px;">DPP Lain</th>
                                                <th class="style bb ws text-center"style="width: 20px;">Tarif PPN</th>
                                                <th class="style bb ws text-center">PPN</th>
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
                $('#periode').daterangepicker({
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

                $("#customer").select2({
                    placeholder: "Pilih",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/kaskeluar/get_partner",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term,
                                jenis: "customer"
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.id,
                                    text: item.nama
                                });
                            });
                            return {
                                results: results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                        }
                    }
                });

                $("#search").on("click", function () {
                    $.ajax({
                        url: "<?= base_url('accounting/exportcoretax/search/') ?>",
                        type: "POST",
                        data: {
                            customer: $("#customer").val(),
                            periode: $("#periode").val()
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