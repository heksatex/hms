<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            #statusbulk {
                color: whitesmoke;
                background-color: red;
                text-align: center;
                font-size: 150%;
                font-weight: 400;
            }
            .row{
                padding: 5px;
            }
            .bolded {
                font-weight:bold;
                font-size: 100%;
                letter-spacing: 3px;
            }
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse">
        <div class="wrapper">
            <header class="main-header">
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">bulk</strong></h3>
                            <div class="pull-right text-right" id="btn-header">
                                <button class="btn btn-primary btn-sm" id="btn-add-bulk" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Tambah Bulk / BAL</button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">No Picklist</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="no_pl"><?= $picklist->no ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Buyer</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="no_pl"><?= $picklist->nama ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--                            <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <div class="col-md-12" id="statusbulk">
                                                                        BAL Not Exist
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>-->
                            <div class="row">
                                <div class="col-md-6 col-xs-12">
                                    <form class="form-horizontal" method="POST" name="form-validasi" id="form-validasi" action="<?= base_url('warehouse/bulk/update') ?>">
                                        <button type="submit" id="btn_form_validasi" style="display: none"></button>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">Scan Barcode / BULK ID</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type='text' name="search" id="search" class="form-control input-lg scan-text" required/>
                                                    <label class="text-sm text-info">Tekan F2 Untuk Kembali ke Scan</label>
                                                    <input type='hidden' name="pl" id="pl" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">BULK</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <h2 id="posisi_bulk">-</h2>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-8 col-xs-12">
                                    <div class="table-responsive over">
                                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="summary_bulk">
                                            <thead>
                                                <tr>
                                                    <th class="style" width="10px">No</th>
                                                    <th class="style"width="20px">BAL ID</th>
                                                    <th class="style" width="20px">Description</th>
                                                    <th class="style" width="10px">PCS</th>
                                                    <th class="style" width="10px">QTY</th>
                                                    <th class="style" width="10px">UOM</th>

                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view("admin/_partials/js.php") ?>
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>

                <?php
//                $this->load->view("admin/_partials/footer.php");
                ?>
            </footer>
        </div>
        <script>
            $(document).keydown(function (e) {
                if (e.which === 113) {
                    $("#search").focus();
                }
                checkInput(e, "*", {"*scan*": function () {
                        $("#search").focus();
                    }});

            });
            $(function () {
                $("#search").focus();

                const sumTable = $("#summary_bulk").DataTable({
                    "searching": false,
                    "paging": false
                });
                $(sumTable.column(1).nodes()).addClass('bolded');

                $("#btn-add-bulk").on('click', function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text('Tambah Data BAL');
                    $.post("<?= base_url('warehouse/bulk/add_bulk/') ?>", {pl: "<?= $picklist->no ?>"}, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);
                            $("#btn-tambah").html("Tambahkan");
                        }, 1000);
                    });
                });
            });
        </script>
    </body>
</html>