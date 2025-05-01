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
            #tabelDelivery{
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
                            <h3 class="box-title"><b>Report Purchase Order</b></h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-po" id="form-po" action="<?= base_url('report/purchaseorder/export') ?>">
                                <div class="col-md-8" style="padding-right: 0px !important;">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Tanggal Order</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="periode" id="periode" value="<?= $date ?>" class="form-control" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="col-md-4">
                                                <label>
                                                    <!--<div>Total Data : <span id="total_record">0</span></div>-->
                                                </label>
                                            </div>
                                            <div class="col-md-4 panel-heading" role="tab" id="advanced" style="padding:0px 0px 0px 15px;  ">
                                                <div data-toggle="collapse" href="#advancedSearch" aria-expanded="false" aria-controls="advancedSearch" class='collapsed'>
                                                    <label style="cursor:pointer;">
                                                        <i class="showAdvanced glyphicon glyphicon-triangle-bottom"></i>
                                                        Advanced 
                                                    </label>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="search" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                                    <button type="submit" class="btn btn-sm btn-default" name="btn-excel" id="export" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o"  style="color:green"></i> Excel</button>
                                </div>
                                <div class="col-md-4">
                                    <div class="pull-right text-right">
                                        <div id='pagination'></div>
                                    </div>
                                </div>
                                <br>
                                <br>
                                <div class="col-md-12">
                                    <div class="panel panel-default" style="margin-bottom: 0px;">
                                        <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                                            <div class="panel-body" style="padding: 5px">
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Supplier</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select class="form-control"  name="supplier[]" id="supplier" style="width: 100%" multiple>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Jenis</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select class="form-control select2"  name="jenis" id="jenis" style="width: 100%">
                                                                    <option></option>
                                                                    <option value="rfq">PO</option>
                                                                    <option value="fpt">FPT</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xs-6">
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Gudang</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select class="form-control select2"  name="warehouse[]" id="warehouse" style="width: 100%" multiple>
                                                                    <?php foreach ($warehouse as $key => $value) {
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
                                                                <label class="form-label">Group By</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select class="form-control select2"  name="group" id="group" style="width: 100%">
                                                                    <option></option>
                                                                    <option value="pod.warehouse">Gudang</option>
                                                                    <option value="po.supplier">Supplier</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-12 table-responsive example1 divListviewHead">
                                    <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                        <table id="tabelpo" class="table table-condesed table-hover" border="1">
                                            <tr>
                                                <th class="style bb ws no" >No</th>
                                                <th class="style bb ws no" >No PO</th>
                                                <th class="style bb ws no" >Supplier</th>
                                                <th class="style bb ws no" >Gudang</th>
                                                <th class="style bb ws no" >Order Date</th>
                                                <th class="style bb ws no" >Jenis</th>
                                                <th class="style bb ws no" >Produk</th>
                                                <th class="style bb ws no" >Qty Beli</th>
                                                <th class="style bb ws no" >Mata Uang</th>
                                                <th class="style bb ws no" >Kurs</th>
                                                <th class="style bb ws no" >Harga perQty</th>
                                                <th class="style bb ws no" >Diskon</th>
                                                <th class="style bb ws no" >Pajak</th>
                                                <th class="style bb ws no" >Subtotal</th>
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
                $('#advancedSearch').on('shown.bs.collapse', function () {
                    $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
                });

                //* Hide collapse advanced search
                $('#advancedSearch').on('hidden.bs.collapse', function () {
                    $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
                });

                $('input[name="periode"]').daterangepicker({
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

                $("#supplier").select2({
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

                const loadData = ((page) => {
                    $.ajax({
                        url: "<?= base_url('report/purchaseorder/search/') ?>" + page,
                        type: "POST",
                        data: {
                            periode: $("#periode").val(),
                            supplier: $("#supplier").val(),
                            warehouse: $("#warehouse").val(),
                            jenis: $("#jenis").find(":selected").val(),
                            group: $("#group").find(":selected").val()

                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        success: ((data) => {
                            $("#tBody").html(data.data.data);
                            unblockUI(function () {}, 100);
//                            $("#pagination").html(data.data.pagination);
//                            $("#total_record").html(data.data.paging["total"]);
//                            $(".paging-report").on("click", function () {
//                                var pg = $(this).attr("data-ci-pagination-page");
//                                getPage(pg);
//                            });
                        }),
                        complete: function (sq) {
                            unblockUI(function () {}, 100);
                        }
                    });
                });

                $("#search").on("click", function () {
                    $("#page").val(1);
                    var page = $("#page").val();
                    loadData(page);
                });

                const formrd = document.forms.namedItem("form-po");
                formrd.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-po").then(
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