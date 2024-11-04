<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
        <style>
            .ws{
                white-space: nowrap;
            }
            .divListviewHead table  {
                display: block;
                height: calc( 100vh - 250px );
                overflow-x: auto;
            }
            #tabelGTP{
                max-height: 100vh
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
                            <h3 class="box-title"><b>Report Goods To Push</b></h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-gtp" id="form-gtp" action="<?= base_url('report/goodstopush/search') ?>">
                                <div class="col-md-8" style="padding-right: 0px !important;">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label" id="label_filter_tanggal">Sales</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">

                                                <select name="sales" class="form-control select2">
                                                    <option></option>
                                                    <?php
                                                    foreach ($sales as $key => $value) {
                                                        ?>
                                                        <option value="<?= $value->nama_sales_group ?>"><?= $value->nama_sales_group ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-sm btn-default" name="btn-search" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-search"  style="color:green"></i> Search</button>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-12 table-responsive example1 divListviewHead">
                                    <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                        <table id="tabelGTP" class="table table-condesed table-hover" border="1">
                                            <tr>
                                                <th  class="style bb ws no" >No</th>
                                                <th class="style bb ws">Rerport Date</th>
                                                <th class="style bb ws">Corak</th>
                                                <th class="style bb ws">Sales</th>
                                                <th class="style bb ws">Customer</th>
                                            </tr>
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
        <script>
            $(function () {
                $(".select2").select2({
                    allowClear: true,
                    placeholder: "Sales"
                });
            });
        </script>
    </body>
</html>