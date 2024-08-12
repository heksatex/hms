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
            #tblRecycle{
                max-height: 100vh
            }

            .align-bottom {
                vertical-align: bottom !important
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
                            <h3 class="box-title"><b>Report Recycle</b></h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-rd" id="form-rd" action="<?= base_url('report/recycle/export') ?>">
                                <div class="col-md-8" style="padding-right: 0px !important;">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">MO</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <select name="mo" class="form-control" id="mo">
                                                    <option value="">Pilih MO</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Corak</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="corak" id="corak" class="form-control corak"/>
                                                <input type="hidden" name="kp" id="kp" class="form-control kp"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">KP</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8 option-kp">
                                                <div class="option-kp-child">
                                                    <select name="kps[]" class="form-control " id="kps" multiple>
                                                        <option value="">Pilih KP</option>
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
                                <div class="col-xs-12">
                                    <div class="pull-left">Total Record : <span id="total_record"></span></div> 
                                    <div id='pagination' class="pull-right"></div> 
                                </div>
                                <div class="col-md-12 table-responsive example1 divListviewHead">

                                    <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                        <table id="tblRecycle" class="table table-condesed table-hover" border="1">

                                            <tr>

                                                <th colspan="12" class="style bb ws text-center align-bottom">

                                                </th>
                                                <th colspan="15" class="style bb ws text-center align-bottom">
                                                    Tricot
                                                </th>
                                                <th colspan="60" class="style bb ws text-center align-bottom">
                                                    GREIGE
                                                </th>
                                                <th colspan="18" class="style bb ws text-center align-bottom">
                                                    DYEING FINISHING
                                                </th>
                                                <th colspan="42" class="style bb ws text-center align-bottom">
                                                    INSPECTING2
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    GUDANG JADI
                                                </th>
                                            </tr>
                                            <tr>
                                                <th colspan="12" class="style bb ws text-center align-bottom">

                                                </th>
                                                <th colspan="3" class="style bb ws text-center align-bottom">

                                                </th>

                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Hasil Produksi TRI
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke GRG
                                                </th>
                                                <!--greige-->
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari TRI 
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari DYE 
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari SET
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari PAD
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke DYE
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke SET
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke PAD
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke BRS
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke INS2  
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke GJD
                                                </th>
                                                <!--greige-->
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari GRG 
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke INS2
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke GRG
                                                </th>

                                                <!--INSPECTING 2-->
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari GRG 
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari SET
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari PAD
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari FIN
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari FBR
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke GJD
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Pengiriman ke GRG
                                                </th>
                                                <th colspan="6" class="style bb ws text-center align-bottom">
                                                    Penerimaan dari INS2
                                                </th>
                                            </tr>
                                            <tr> 

                                                <th class="style bb ws no align-bottom">No</th>
                                                <th  class="style bb ws align-bottom">LOT</th>
                                                <th class="style bb ws align-bottom">QTY1 (Mtr)</th>
                                                <th  class="style bb ws align-bottom">Uom1</th>
                                                <th class="style bb ws align-bottom">QTY2 (Mtr)</th>
                                                <th  class="style bb ws align-bottom">Uom2</th>
                                                <th  class="style bb ws align-bottom">GO</th>
                                                <th  class="style bb ws align-bottom">Route</th>
                                                <th  class="style bb ws align-bottom">Nama Produk</th>
                                                <th  class="style bb ws align-bottom">Warna</th>
                                                <th  class="style bb ws align-bottom">Parent</th>
                                                <th  class="style bb ws text-center align-bottom">Jenis Kain</th>
                                                <th  class="style bb ws align-bottom">
                                                    MO Tricot
                                                </th>
                                                <th  class="style bb ws align-bottom">
                                                    Kode Produk 
                                                    Kain Tricot
                                                </th>
                                                <th  class="style bb ws align-bottom">
                                                    Nama Produk Kain Tricot
                                                </th>
                                                <!--tricot-->
                                                <th class="style bb ws align-bottom">
                                                    Tanggal HPH
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>
                                                <!--GREIGE-->
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <!--DYEING FINISHING-->
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <!--INSPECTING 2-->
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>

                                                <!--GUDANGJADI-->
                                                <th class="style bb ws align-bottom align-bottom">
                                                    Tanggal
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    LOT
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 1
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 1 
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Qty 2
                                                </th>
                                                <th class="style bb ws align-bottom">
                                                    Uom 2
                                                </th>
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
            <template class="option-select">
                <div class="option-kp-child">
                    <select class="form-control" id="kps" name="_name_" multiple>
                        <option value="">Pilih KP</option>
                    </select>
                </div>
            </template>
            <template class="option-table">
                <div class="option-kp-child">
                    <button type="button" class="btn btn-default btn-sm btn-pilih-kp"> Pilih KP / LOT</button>
                    <span>0</span> KP/LOT Terpilih
                </div>
            </template>

            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>

            </footer>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script data-exec-on-popstate>
            var kp = [];
            $(function () {
                $("#mo").select2({
                    placeholder: '-Pilih MO-',
                    allowClear: true,
                    tags: "true",
                    ajax: {
                        url: "<?= base_url('report/recycle/get_list_mo') ?>",
                        dataType: 'json',
                        delay: 250,
                        processResults: function (data) {
                            return {
                                results: data
                            };
                        }
                    }
                });


                $("#kps").on("change", function () {
                    kp = $(this).val();
                });
                $("#mo").on("change", function () {
                    $("#kp").val("").trigger('change');
                    var valMo = $(this).val();
                    $(".option-kp").find(".option-kp-child").remove();
                    if (valMo === "") {
                        var mainTemplate = $('template.option-select');
                        var $newElement = mainTemplate.html().replace(/_name_/g, "kps[]");
                        $(".option-kp").html($newElement);
                        loadKp();
                    } else {
                        var mainTemplate = $('template.option-table');
                        var $newElement = mainTemplate.html();
                        $(".option-kp").html($newElement);
                        loadListKp();
                    }

                    kp = [];
                });
                $(".corak").on("change", function () {
                    $("#kp").val("").trigger('change');
                });

                const loadListKp = (() => {
                    $(".btn-pilih-kp").on("click", function (e) {
                        e.preventDefault();
                        $("#tambah_data").modal({
                            show: true,
                            backdrop: 'static'
                        });
                        $('#tambah_data').on('hidden.bs.modal', function () {
                            $(".option-kp-child span").html(kp.length);
                        });
                        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                        $('.modal-title').text('Pilih List KP');
                        $.post("<?= base_url('report/recycle/show_list_kp') ?>", {mo: $("#mo").find(":selected").val()}, function (data) {
                            setTimeout(function () {
                                var hhtml = data.data.replace("lebar", "lebar_mode");
                                $(".tambah_data").html(hhtml);
                                $("#btn-tambah").html("Ok");
//                                $(".modal-footer").prepend('<button type="button" name="btn-submit" id="btn-submit" data-loading-text="<i class=`fa fa-spinner fa-spin `></i> processing..."class="btn btn-primary btn-sm">Simpan</button>');
                            }, 500);
                        });
                    });
                });

                const loadKp = (() => {
                    $("#kps").select2({
                        placeholder: '-Pilih KP-',
                        allowClear: true,
                        tags: "true",
                        ajax: {
                            url: "<?= base_url('report/recycle/get_list_kp') ?>",
                            dataType: 'json',
                            delay: 250,
                            data: function (params) {
                                var q = {
                                    q: params.term,
                                    corak: $("#corak").val(),
                                    mo: $("#mo").find(":selected").val(),
                                };

                                return q;
                            },
                            processResults: function (data) {
                                return {
                                    results: data
                                };
                            }
                        }
                    });
                });
                loadKp();

                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
                });

                const formrd = document.forms.namedItem("form-rd");
                formrd.addEventListener("submit", (event) => {
                    $("#kp").val(JSON.stringify(kp));
                    please_wait(function () {});
                    request("form-rd").then(
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
                }, false);

                const loadData = ((page) => {
                    $.ajax({
                        url: "<?= base_url('report/recycle/search/') ?>" + page,
                        type: "POST",
                        data: {
                            kp: JSON.stringify(kp),
                            mo: $("#mo").find(":selected").val(),
                            corak: $("#corak").val(),
                            page: page
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        success: ((data) => {
                            $("#tBody").html(data.data.data);
                            $("#pagination").html(data.data.pagination);
                            $("#total_record").html(data.data.total);
                        }),
                        error: ((error) => {
                            alert_notify(error.responseJSON.icon, error.responseJSON.message, error.responseJSON.type, function () {

                            });
                        }),
                        complete: function (sq) {
                            unblockUI(function () {}, 100);
                        }
                    });
                });


                $("#search").on("click", function () {
                    loadData(0);
                });

                $('#pagination').on('click', 'a', function (e) {
                    e.preventDefault();
                    var pageno = $(this).attr('data-ci-pagination-page');
                    loadData(pageno);
                });

            });
        </script>

    </body>
</html>