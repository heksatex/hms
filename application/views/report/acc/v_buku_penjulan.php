<!doctype html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <style type="text/css">
           h3 {
            display: block !important;
            text-align: center !important;
        }

        .divListviewHead table {
            display: block;
            height: calc(97vh - 250px);
            overflow-x: auto;
        }

        .ws {
            white-space: nowrap;
        }
        table tbody tr td {
                padding: 0px 5px 0px 5px !important;
            }
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini" >
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
                        <div class="box-header with-border">
                            <h3 class="box-title"><b>Buku Penjualan</b></h3>
                        </div>
                        <div class="box-body">
                            <form id="form-search" name="form-search" class="form-horizontal form-search" action="<?= base_url('report/bukupenjualan/export') ?>" method="post">
                                <div class="col-md-8" style="padding-right: 0px !important;">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="form-group">
                                                <div class="col-md-6 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">Tanggal</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input type="text" class="form-control" name="tanggal" id="tanggal">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">Posisi</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <select class="form-control input-sm select2" name="posisi" id="posisi">
                                                            <option value="d">Debet</option>
                                                            <option value="c">Kredit</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12">
                                            <div class="col-md-4">
                                                <label>
                                                    <div id='total_record'>Total Data : <span id="totalData">0</span></div>
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
                                <br>
                                <br>
                                <div class="col-md-12">
                                    <div class="panel panel-default" style="margin-bottom: 0px;">
                                        <div id="advancedSearch" class="panel-collapse collapse" role="tabpanel" aria-labelledby="advanced" >
                                            <div class="panel-body" style="padding: 5px">
                                                <div class="form-group">
                                                    <div class="col-md-6">
<!--                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Department</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select type="text" class="form-control input-sm" name="departemen" id="departemen"></select>
                                                            </div>
                                                        </div>-->

                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4">
                                                                <label class="form-label">Customer</label>
                                                            </div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select type="text" class="form-control input-sm" name="customer" id="customer"> </select>
                                                            </div>
                                                        </div>

                                                    </div>
                                                    <div class="col-md-6">

                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-3">
                                                                <label class="form-label">Uraian</label>
                                                            </div>
                                                            <div class="col-xs-9 col-md-9">
                                                                <input type="text" class="form-control" name="uraian" id="uraian">
                                                            </div>
                                                        </div>
<!--                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-3">
                                                                <label class="form-label">Faktur pajak</label>
                                                            </div>
                                                            <div class="col-xs-9 col-md-9">
                                                               <select class="form-control input-sm" name="faktur" id="faktur">
                                                                   <option value=''>All</option>
                                                                <option value='ada'>Ada</option>
                                                                <option value='tidak'>Tidak</option>
                                                            </select>
                                                            </div>
                                                        </div>-->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            
                            <div class="col-xs-12 table-responsive example1 divListviewHead">
                                <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                    <table id="tbl-penjualan" class="table table-condesed table-hover" border="0">
                                        <thead>
                                            <tr>
                                                <th class="style bb ws no">No. </th>
                                                <th class='style bb ws'>No Faktur</th>
                                                <th class='style bb ws'>No SJ</th>
                                                <th class='style bb ws' style="min-width: 100px">Tgl dibuat</th>
                                                <th class='style bb ws' style="min-width: 150px">Uraian</th>
                                                <th class='style bb ws'>Customer</th>
                                                <th class='style bb ws'>COA</th>
                                                <th class='style bb ws'>Curr</th>
                                                <th class='style bb ws'>Kurs</th>
                                                <th class='style bb ws text-right'>Qty</th>
                                                <th class='style bb ws text-right'>Total Nominal</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tBody">
                                            <tr>
                                                <td colspan="17">Tidak ada Data</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div id="example1_processing" class="table_processing" style="display: none; z-index:5;">
                                        Processing...
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

                //select 2 Departement
                $('#departemen').select2({
                    allowClear: true,
                    placeholder: "Select Departemen",
                    width: '100%',
                    ajax: {
                        dataType: 'JSON',
                        type: "POST",
                        url: "<?php echo base_url(); ?>report/pengirimanharian/get_departement_select2",
                        //delay : 250,
                        data: function (params) {
                            return {
                                nama: params.term,
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data, function (index, item) {
                                results.push({
                                    id: item.kode,
                                    text: item.nama
                                });
                            });
                            return {
                                results: results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            //alert('Error data');
                            //alert(xhr.responseText);
                        }
                    }
                });
                $('#customer').select2({
                    allowClear: true,
                    placeholder: "Select Customer",
                    width: '100%',
                    ajax: {
                        dataType: 'JSON',
                        type: "get",
                        url: "<?php echo base_url(); ?>accounting/kaskeluar/get_partner",
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
                            //alert('Error data');
                            //alert(xhr.responseText);
                        }
                    }
                });
                
                $("#search").on("click", function () {
                    $.ajax({
                        url: "<?= base_url('report/bukupenjualan/search') ?>",
                        type: "POST",
                        data: {
                            tanggal: $("#tanggal").val(),
                            customer: $("#customer").val(),
                            uraian: $("#uraian").val(),
                            faktur: $("#faktur").val(),
                            posisi:$("#posisi").val()
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        success: ((data) => {
                            $("#tBody").html(data.data);
                            $("#totalData").html(data.jumlah);
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
            });
        </script>
    </body>
</html>