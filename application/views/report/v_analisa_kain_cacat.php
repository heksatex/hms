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
                                <div class="col-md-8" style="padding-right: 0px !important;">
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
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <button class="btn btn-success btn-sm" type="submit">Export</button>
                                                <button class="btn btn-warning btn-sm" type="reset" id="reset">Reset</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
<!--                            <div class="row">
                                <div class="col-md-12 table-responsive example1 divListviewHead">
                                    <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                        <table id="tabelDelivery" class="table table-condesed table-hover" border="1">
                                            <tr>
                                                <th class="style bb ws">Produk</th>
                                                <th class="style bb ws">Total HPH GJD</th>
                                                <th class="style bb ws">total : grade A kain gjd</th>
                                                <th class="style bb ws">total :
                                                    - grade A kain jadi
                                                    cacat dyeing (D)
                                                    finishing (F)</th>
                                                <th class="style bb ws">persen :
                                                    - Σ A PRODUKSI thd Σ    </th>
                                                <th class="style bb ws">total :
                                                    - grade A kain jadi
                                                    cacat Prodduksi (T)
                                                    finishing (F)</th>
                                                <th class="style bb ws">persen :
                                                    - Σ A DYEING thd Σ</th>
                                                <th class="style bb ws">total :
                                                    - grade A kain jadi
                                                    -cacat produksi (T)
                                                    -cacat dyeing (D)</th>
                                                <th class="style bb ws">persen :
                                                    - Σ A FINISHING thd Σ   </th>
                                            </tr>
                                            <tbody id="tBody" class="ws">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 table-responsive example1 divListviewHead">
                                    <div role="region" aria-labelledby="HeadersCol" tabindex="0" class="rowheaders">
                                        <table id="tabelDelivery" class="table table-condesed table-hover" border="1">
                                            <tr>
                                                <th class="style bb ws">master cacat</th>
                                                <th class="style bb ws">Jumlah Point Cacat Grade B Prod</th>
                                                <th class="style bb ws">Jumlah Point Cacat Grade B Dye</th>
                                                <th class="style bb ws">Jumlah Point Cacat Grade B Fin</th>
                                                <th class="style bb ws">Persen B</th>
                                                <th class="style bb ws">Jumlah Point Cacat Grade C Pot , BS</th>
                                                <th class="style bb ws">Jumlah Point Cacat BS Dying</th>
                                                <th class="style bb ws">Jumlah Point Cacat BS Fin</th>
                                                <th class="style bb ws">Persen C</th>
                                            </tr>
                                            <tbody id="tBody2" class="ws">

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>-->
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

                $("#jenis_kain").select2({
                    placeholder: "Pilih Jenis Kain",
                    allowClear: true
                });

                $("#reset").on("click", function () {
                    $('#jenis_kain').val(null).trigger('change');
                    $("#tBody").html("");
                    $("#tBody2").html("");
                });

                const formrd = document.forms.namedItem("form-rd");
                formrd.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-rd").then(
                            response => {
                                alert_notify(response.data.icon, response.data.message, response.data.type, function () {

                                });
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
