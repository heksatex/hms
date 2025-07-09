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
                        <!--<form class="form-horizontal" method="POST" name="form-acc-bankmasuk" id="form-acc-bankmasuk" action="<?= base_url() ?>">-->
                        <div class="box-header with-border">
                            <h3 class="box-title">Bukti Bank Masuk</h3>
                        </div>
                        <div class="box-body">
                            <div class="col-md-4 col-xs-12">
                                <div class="field-group">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">No ACC (Debet)</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2" name="no_acc" required>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Dari</label></div>
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
                                                <input type="text" name="transaksi" id="transaksi" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-12">
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
                                            <div class="col-xs-4"><label class="form-label required">Jenis Transaksi</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2" name="partner" required>

                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-xs-12">
                                <div class="field-group">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <button type="button" class="btn btn-default btn-sm btn-add-item-tf"><span class="glyphicon glyphicon-transfer"></span>&nbsp; Dari Pindah Dana</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">

                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <button type="button" class="btn btn-default btn-sm btn-add-item-giro"><span class="glyphicon glyphicon-book"></span>&nbsp; Dari Bukti Giro</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="col-md-12 table-responsive over">
                                <table class="table table-condesed table-hover rlstable  over" width="100%" id="bankmasuk-detail" >
                                    <thead>                          

                                    <th class="style no">No.</th>
                                    <th class="style">Bank</th>
                                    <th class="style" >No Rek</th>
                                    <th class="style" >No Cek/BG</th>
                                    <th class="style" >Tgl Cair</th>
                                    <th class="style">No ACC (Kredit)</th>
                                    <th class="style text-right">Kurs</th>
                                    <th class="style">Curr</th>
                                    <th class="style text-right">Nominal</th>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>
                                                <button type="button" class="btn btn-success btn-sm btn-add-item"><i class="fa fa-plus-circle"></i></button>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!--</form>-->
                    </div>
                </section>
            </div>
            <template class="bankmasuk-tmplt">
                <tr>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                    </td>
                    <td>
                        <input type="text" name="bank[]" class="form-control" required/>
                    </td>
                    <td>
                        <input type="text" name="no_rek[]" class="form-control" required/>
                    </td>
                    <td>
                        <input type="text" name="no_cek_bg[]" class="form-control"/>
                    </td>
                    <td>
                        <input type="date" name="tgl_cair[]" class="form-control"/>
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
        </div>
        <script>
            $(function () {
                $(".btn-add-item").on("click", function (e) {
                    e.preventDefault();
                    var tmplt = $("template.bankmasuk-tmplt");
                    var isi_tmplt = tmplt.html();
                    $("#bankmasuk-detail tbody").append(isi_tmplt);
                });
                
                $("#bankmasuk-detail").on("click", ".btn-rmv-item", function () {
                    $(this).closest("tr").remove();
                });
            });
        </script>
    </body>
</html>