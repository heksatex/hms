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

            /*            #tabelDelivery.hides tr > *:nth-child(14),#tabelDelivery.hides tr > *:nth-child(15){
                            display: none;
                        }
                        #tabelDelivery.hide_intrn tr > *:nth-child(13){
                            display: none;
                        }*/

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
                            <h3 class="box-title"><b>Report Analisa Kain Cacat</b></h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-rd" id="form-rd" action="<?= base_url('report/analisacacatkain/export') ?>">
                                <div class="col-md-6" style="padding-right: 0px !important;">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Periode HPH</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="periode" id="periode" value="<?= $date ?>" class="form-control" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Jenis Kain</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <select name="jenis_kain[]" class="form-control" id="jenis_kain" multiple required>

                                                    <?php foreach ($jenis_kain as $key => $row) { ?>
                                                        <option value='<?= $row->id; ?>'><?= $row->nama_jenis_kain; ?></option>
                                                    <?php }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
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
                                                        if ($this->session->userdata('nama')['sales_group'] === $value->kode_sales_group) {
                                                            echo '<option value="' . $value->kode_sales_group . '" selected>' . $value->nama_sales_group . '</option>';
                                                        } else {
                                                            echo '<option value="' . $value->kode_sales_group . '">' . $value->nama_sales_group . '</option>';
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
                                            <input id="detail" name="detail" type="checkbox" value="1"><strong>Detail</strong></label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <select name="detail_group" class="form-control" id="detail_group" style="display: none;">
                                                    <option value="kp">KP</option>
                                                    <option value="barcode">Bacode</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" style="padding-right: 0px !important;">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <button class="btn btn-success btn-sm" type="submit">Export</button>
                                                <button class="btn btn-warning btn-sm" type="reset" id="reset">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <script type="text/javascript">
            $(function () {
                $('input[name="periode"]').daterangepicker({
                    endDate: moment().startOf('day'),
                    startDate: moment().startOf('day').add(-1, 'week'),
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });
                
                $("#detail").on("change", function () {
                    if (!$(this).prop('checked')) {
                        $("#detail_group").hide();
                        $("#detail_group").val("kp").trigger("change");
                        return;
                    }
                    $("#detail_group").show();
                });

                $("#marketing").select2({
                    placeholder: "Pilih Marketing",
                    allowClear: true
                });

                $("#jenis_kain").select2({
                    placeholder: "Pilih Jenis Kain",
                    allowClear: true
                });

                $("#reset").on("click", function () {
                    $('#jenis_kain').val(null).trigger('change');
                    $("#tBody").html("");
                    $("#tBody2").html("");
                    $("#detail_group").hide();
                });

                const formrd = document.forms.namedItem("form-rd");
                formrd.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-rd").then(
                            response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {

                                    });
                                }, 100);
                                if (response.status === 200) {
//                                    $("#tBody").html(response.data.data);
//                                    $("#tBody2").html(response.data.datas);
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
