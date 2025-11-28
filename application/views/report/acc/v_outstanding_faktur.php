<!DOCTYPE html>
<html lang="en">

    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
        <style type="text/css">
            h3 {
                display: block !important;
                text-align: center !important;
            }

            .divListviewHead table {
                display: block;
                height: calc(101vh - 250px);
                overflow-x: auto;
            }

            table tbody tr td {
                padding: 0px 5px 0px 5px !important;
            }

            .style_space {
                white-space: nowrap !important;
                /* font-weight: 700; */
                background: #F0F0F0;
                border-top: 2px solid #ddd !important;
                border-bottom: 2px solid #ddd !important;
            }

            .ket-acc {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 300px;
                /* Sesuaikan dengan kebutuhan */
            }

            .resizable .resizer:hover {
                background-color: rgba(0, 0, 0, 0.1);
            }

            .resizable {
                position: relative;
            }

            .resizable .resizer {
                position: absolute;
                top: 0;
                right: 0;
                width: 5px;
                cursor: col-resize;
                user-select: none;
                height: 100%;
            }

            table th,
            table td {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
        </style>
    </head>

    <body class="hold-transition skin-black fixed sidebar-mini">
        <!-- Site wrapper -->
        <div class="wrapper">
            <!-- main -header -->
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php $this->load->view("admin/_partials/topbar.php") ?>
            </header>

            <!-- Menu Side Bar -->
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>

            <!-- Content Wrapper-->
            <div class="content-wrapper">
                <!-- Content Header (Status - Bar) -->
                <section class="content-header">
                </section>

                <!-- Main content -->
                <section class="content">
                    <!--  box content -->
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><b>Outstanding Faktur</b></h3>
                        </div>
                        <div class="box-body">

                            <form name="form-search" class="form-horizontal form-search" role="form" action="<?= base_url('report/outstandingfaktur/export') ?>" method="post" id="form-search">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="col-sm-12 col-md-12">
                                            <div class="col-md-2">
                                                <label>Supplier</label>
                                            </div>
                                            <div class="col-sm-* col-md-8 col-lg-8">
                                                <select class="form-control input-sm" name="customer" id="customer">
                                                    <?php
                                                    if (!empty($partner)) {
                                                        ?>
                                                        <option value="<?= $partner->id ?>" selected><?= $partner->nama ?></option>
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
                                        <div class="col-md-12">
                                            <div class="col-md-12">
                                                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                                                <button class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                                                <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-pdf" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-pdf-o" style="color:red"></i> PDF</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>

                            <!-- table -->
                            <div class="box-body">
                                <div class="col-sm-12 table-responsive">
                                    <div class="table_scroll">
                                        <div class="table_scroll_head">
                                            <div class="divListviewHead">
                                                <table id="tblosfp" class="table table-condesed table-hover" border="">
                                                    <thead>
                                                        <tr>
                                                            <th class="style bb no">No. </th>
                                                            <th class='style bb' style="min-width: 50px; width:105px;">No Faktur</th>
                                                            <th class='style bb' style="min-width: 105px; width:105px;">No SJ</th>
                                                            <th class='style bb' style="min-width: 105px; width:105px;">Tanggal</th>
                                                            <th class='style bb text-right' style="min-width: 150px; width:100px;">Total Piutang (Rp)</th>
                                                            <th class='style bb text-right' style="min-width: 150px; width:100px;">Sisa Piutang (Rp)</th>
                                                            <th class='style bb text-right' style="min-width: 150px; width:100px;">Total Piutang (Valas)</th>
                                                            <th class='style bb text-right' style="min-width: 150px; width:100px;">Sisa Piutang (Valas)</th>
                                                            <th class='style bb text-right' style="min-width: 100px; width:100px;">Payment Term (Hari)</th>
                                                            <th class='style bb text-right' style="min-width: 100px; width:100px;">Umur (Hari)</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan="10">Tidak ada Data</td>
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
                            </div>


                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

        </div>

        <?php $this->load->view("admin/_partials/js.php"); ?>

        <div id="load_modal">
            <!-- Load Partial Modal -->
            <?php $this->load->view("admin/_partials/modal.php") ?>
        </div>

        <script>
            $(function () {
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

                $("#btn-generate").on("click", function () {
                    $.ajax({
                        type: "post",
                        url: "<?php echo base_url(); ?>report/outstandingfaktur/generate",
                        data: {
                            customer: $("#customer").val()
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        success: ((data) => {
                            $("#tblosfp tbody").html(data.data);
                            unblockUI(function () {}, 100);
                        }),
                    });
                });
                $("#btn-pdf").on("click", function () {
                    $.ajax({
                        type: "post",
                        url: "<?php echo base_url(); ?>report/outstandingfaktur/pdf",
                        data: {
                            customer: $("#customer").val()
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        success: ((data) => {
                            window.open(data.url, '_blank').focus();
                        }),
                        error: function (req, error) {
                            unblockUI(function () {
                                setTimeout(function () {
                                    alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                }, 500);
                            });
                        },
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {}, 200);
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
        <?php
        if (!empty($partner)) {
            ?>
            <script>
                $(function () {
                    $("#btn-generate").trigger("click");
                });
            </script>
            <?php
        }
        ?>
    </body>
</html>