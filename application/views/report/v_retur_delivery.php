<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <style type="text/css">

            h3{
                display: block !important;
                text-align: center !important;
            }

            .divListviewHead table  {
                display: block;
                height: calc( 100vh - 250px );
                overflow-x: auto;
            }
            #tabelRetur{
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

            #tabelDelivery.hides tr > *:nth-child(13),#tabelDelivery.hides tr > *:nth-child(14){
                display: none;
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
                            <h3 class="box-title"><b>Report Retur / Batal</b></h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-rrd" id="form-rrd" action="<?= base_url('report/retur/export') ?>">
                                <div class="col-md-8" style="padding-right: 0px !important;">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Periode Batal / Retur</label>
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
                                                    <div>Total Data : <span id="total_record">0</span></div>
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

                                                <div class="col-md-4" >
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Marketing</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select name="marketing" class="form-control" id="marketing">
                                                                    <option value="">All</option>
                                                                    <?php
                                                                    foreach ($sales as $key => $value) {
                                                                        if ($this->session->userdata('nama')['sales_group'] === $value->kode) {
                                                                            echo '<option value="' . $value->kode . '" selected>' . $value->nama . '</option>';
                                                                        } else {
                                                                            echo '<option value="' . $value->kode . '">' . $value->nama . '</option>';
                                                                        }
                                                                    }
                                                                    ?>
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
                                                                <select name="status" class="form-control" id="status">
                                                                    <option value="retur_batal">Retur dan Batal</option>
                                                                    <option value="retur">Retur</option>
                                                                    <option value="cancel">Batal</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Order BY</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select name="order" class="form-control" id="order">
                                                                    <option value="no_sj">No SJ</option>
                                                                    <option value="nama">Customer</option>
                                                                    <option value="jenis_jual">Type</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Rekap</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select name="rekap" id="rekap" class="form-control">
                                                                    <!--<option value="global" disabled>Global</option>-->
                                                                    <!--<option value="detail" disabled>Detail</option>-->
                                                                    <option value="barcode" selected>Barcode</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <label class="col-sm-2 checkbox-inline">
                                                                    <input id="summary" name="summary" type="checkbox" value="1">Summary</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--               <div class="form-group">
                                                                  <div class="col-md-12 col-xs-12">
                                                                      <div class="col-xs-4">
                                                                          <label class="form-label">Qty HPH</label>
                                                                      </div>
                                                                      <div class="col-xs-8 col-md-8">
                                                                          <label class="col-sm-2 checkbox-inline">
                                                                              <input id="qtyHph" name="qtyhph" type="checkbox" value="1">Tampilkan</label>
                                                                      </div>
                                                                  </div>
                                                              </div>-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-12 table-responsive example1 divListviewHead">
                                    <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                        <table id="tabelRetur" class="table table-condesed table-hover" border="1">
                                            <tr>
                                                <th  class="style bb ws no" >No</th>
                                                <th class="style bb ws">DO</th>
                                                <th class="style bb ws">No SJ</th>
                                                <th class="style bb ws">Tanggal dibuat</th>
                                                <th class="style bb ws">Tanggal Kirim</th>
                                                <th class="style bb ws">Status</th>
                                                <th class="style bb ws"><span id="tbl_status" class="text-capitalize">tanggal Retur</span></th>
                                                <th class="style bb ws">Type</th>
                                                <th class="style bb ws">No Picklist</th>
                                                <th class="style bb ws">Buyer</th>
                                                <th class="style bb ws">Alamat</th>
                                                <th class="style bb ws">Corak</th>
                                                <th class="style bb ws">Lebar</th>
                                                <th class="style bb ws">Warna</th>
                                                <th class="style bb ws text-right">QTY 1 [HPH]</th>
                                                <th class="style bb ws text-right">QTY 2 [HPH]</th>
                                                <th class="style bb ws text-right">QTY 1 [JUAL]</th>
                                                <th class="style bb ws text-right">QTY 2 [JUAL]</th>
                                                <th class="style bb ws">LOT</th>
                                                <th class="style bb ws">Catatan</th>
                                                <th class="style bb ws">Marketing</th>
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
        <script type="text/javascript">
            //* Show collapse advanced search
            $('#advancedSearch').on('shown.bs.collapse', function () {
                $(".showAdvanced").removeClass("glyphicon-triangle-bottom").addClass("glyphicon-triangle-top");
            });

            //* Hide collapse advanced search
            $('#advancedSearch').on('hidden.bs.collapse', function () {
                $(".showAdvanced").removeClass("glyphicon-triangle-top").addClass("glyphicon-triangle-bottom");
            });
            // disable enter
            $(window).keydown(function (event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                    return false;
                }
            });
            $(function () {
                $("#tabelRetur").toggleClass("hides");
                $('input[name="periode"]').daterangepicker({
                    endDate: moment().startOf('day'),
                    startDate: moment().startOf('day').add(-1, 'week'),
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });
                $("#qtyHph").on("change", function () {
                    $("#tabelRetur").toggleClass("hides");
                });

                const loadData = ((page) => {
                    $.ajax({
                        url: "<?= base_url('report/retur/search') ?>",
                        type: "POST",
                        data: {
                            periode: $("#periode").val(),
                            rekap: $("#rekap").find(":selected").val(),
                            order: $("#order").find(":selected").val(),
                            status: $("#status").find(":selected").val(),
                            marketing: $("#marketing").find(":selected").val(),
                            summary: $("#summary").is(":checked") ? 1 : 0
                        },
                        beforeSend: function (xhr) {
                            $("#tbl_status").html("tanggal " + $("#status").find(":selected").text());
                            please_wait((() => {

                            }));
                        },
                        success: ((data) => {
                            $("#tBody").html(data.data.data);
//                            $("#pagination").html(data.data.pagination);
                            $("#total_record").html(data.data["total"]);
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

                const formrd = document.forms.namedItem("form-rrd");
                formrd.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-rrd").then(
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