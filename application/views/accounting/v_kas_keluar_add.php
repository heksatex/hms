<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">

                </section>
                <section class="content">
                    <div class="box">
                        <form class="form-horizontal" method="POST" name="form-acc-kasadd" id="form-acc-kasadd" action="<?= base_url() ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title">Bukti Kas Keluar</h3>
                            </div>
                            <div class="box-body">

                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">No ACC (Kredit)</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2" name="no_acc" required>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Kepada</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2" name="partner" required>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Untuk Transaksi</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="transaksi" id="lain_lain" class="form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Tanggal</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="date" name="tanggal" id="tanggal" class="form-control" required/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Lain-Lain</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="lain_lain" id="lain_lain" class="form-control"/>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <button type="button" class="btn btn-default btn-sm btn-add-item-fpt"><span class="glyphicon glyphicon-th-list"></span> FPT</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="box-footer">
                                <div class="col-md-12 table-responsive over">
                                    <table class="table table-condesed table-hover rlstable  over" width="100%" id="kaskeluar-detail" >
                                        <thead>                          

                                        <th class="style no">No.</th>
                                        <th class="style" width="200px">Uraian</th>
                                        <th class="style" width="50px">No ACC (Debit)</th>
                                        <th class="style" style="width:100px; text-align: right;" >Kurs</th>
                                        <th class="style" width="80px">Curr</th>
                                        <th class="style text-right" width="100px">Nominal</th>

                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>
                                                    <button class="btn btn-success btn-sm btn-add-item"><i class="fa fa-plus-circle"></i></button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
        <template class="kaskeluar-tmplt">
            <tr>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                </td>
                <td>
                    <input type="text" name="uraian[]" class="form-control" required/>
                </td>
                <td>

                </td>
                <td>
                    <input type="text" name="kurs[]" class="form-control" required/>
                </td>
                <td>
                    <input type="text" name="curr[]" class="form-control" required/>
                </td>
                <td>
                    <input type="text" name="nominal[]" class="form-control" required/>
                </td>
            </tr>
        </template>

        <footer class="main-footer">
            <?php $this->load->view("admin/_partials/modal.php") ?>
            <?php $this->load->view("admin/_partials/js.php") ?>
        </footer>

        <script>
            $(function () {
                var no = 0;
                $(".btn-add-item").on("click", function (e) {
                    e.preventDefault();
                    no += 1;
                    var tmplt = $("template.kaskeluar-tmplt");
                    var isi_tmplt = tmplt.html();
                    $("#kaskeluar-detail tbody").append(isi_tmplt);
                });
                
                $("#kaskeluar-detail").on("click", ".btn-rmv-item", function () {
                    $(this).closest("tr").remove();
                });

                $(".btn-add-item-fpt").on("click", function (e) {
                    e.preventDefault();
                    $("#view_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text("List FPT");
                });


            });
        </script>
    </body>
</html>