<html lang="en">
    <head>
        <style>
            #btn-edit,#btn-cancel,#btn-print {
                display: none;
            }
        </style>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <?php $this->load->view("admin/_partials/js.php") ?>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php
                $this->load->view("admin/_partials/main-menu.php");
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data);
                ?>
            </header>
            <aside class="main-sidebar">
                <?php
                $this->load->view("admin/_partials/sidebar.php");
                ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header" >
                    <section class="content">
                        <div class="box">
                            <div class="box-header with-border">

                                <div class="pull-right text-right" id="btn-header">
                                </div>
                                <h3 class="box-title">Form Add  <strong> </strong></h3>
                            </div>
                            <div class="box-body">
                                <div class="col-md-6 col-xs-12">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">No Picklist</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <span><?= $picklist->no ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Sales</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <span><?= $picklist->sales ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Tipe Bulk</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <span><?= $picklist->bulk ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="row">
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">Customer</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <span><?= $picklist->nama ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">Jenis Jual</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <span style="text-transform: uppercase"><?= $picklist->jenis_jual ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">Alamat</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <span style="text-transform: uppercase"><?= $picklist->alamat ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <form class="form-horizontal" method="POST" name="form-do" id="form-do" action="<?= base_url('warehouse/deliveryorder/save') ?>">
                                        <div class="col-md-6 col-xs-12">
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label required">No SJ</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <div class="input-group">
                                                            <select class="form-control" name="no_sj_jenis" id="no_sj_jenis" required>
                                                                <?php
                                                                if ($picklist->jenis_jual === 'lokal') {
                                                                    ?>
                                                                    <option value="SJ/HI/07">SJ/HI/07</option>
                                                                    <option value="SAMPLE/HI">SAMPLE/HI</option>
                                                                    <option value="SJM/HI/07">SJM/HI/07</option>
                                                                    <option value="MAKLOON/HI">MAKLOON/HI</option>

                                                                    <?php
                                                                } else if ($picklist->jenis_jual === 'export') {
                                                                    ?>
                                                                    <option value="SJ/HI/03">SJ/HI/03</option>
                                                                    <?php
                                                                } else {
                                                                    ?>
                                                                    <option value="SJ/HI/P/00">SJ/HI/P/00</option>
                                                                    <?php
                                                                }
                                                                ?>

                                                            </select>
                                                            <span class="input-group-addon"><a href="#" class="check-antrian">Antrian No SJ</a></span>
                                                        </div>

                                                        <input type="hidden" name="pl" id="picklist" value="<?= $picklist->no ?>">
                                                        <input type='hidden' name="bal" id="bal"/>
                                                        <input type="hidden" name="tipe" id="tipe" value="<?= $picklist->type_bulk_id ?>">
                                                        <input type="hidden" name="remove_item" id="remove_item">
                                                        <button type="submit" id="form-do-submit" style="display: none;"></button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label required">Tanggal Dokumen</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input class="form-control" name="tanggal_dokumen" id="tanggal_dokumen" required <?= (in_array($user->level, ["Entry Data", ""]) ? "readonly" : "") ?> >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-xs-12">
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label">REV</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <select class="form-control" name="rev">
                                                            <option selected></option>
                                                            <option>1</option>
                                                            <option>2</option>
                                                            <option>3</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label" >Note Picklist</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <textarea type="text" class="form-control input-sm resize-ta" rows="8" id="ket" name="ket"><?= $picklist->keterangan ?></textarea>
                                                    </div>                                    
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                </section>
            </div>
        </div>
        <footer class="main-footer">
            <?php $this->load->view("admin/_partials/modal.php") ?>

            <?php
            $this->load->view("admin/_partials/footer.php");
            ?>
        </footer>
    </body>
</html>

<script>
    $(function () {
        $("#tanggal_dokumen").datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss',
            date: new Date()
                    //                    maxDate: new Date()
        });
        $("#btn-simpan").unbind("click").off("click").on("click", function () {
            $("#form-do-submit").trigger('click');
        });
        $(".check-antrian").on("click", function (e) {
            var sj = $("#no_sj_jenis").val();
            e.preventDefault();
            $("#print_data").modal({
                show: true,
                backdrop: 'static'
            });
            $(".print_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
            $('.modal-title').text('List Antring ' + sj);
            $.post("<?= base_url('warehouse/deliveryorder/antrian_sj/') ?>",
                    {
                        sj: sj,
                        tanggal_dokumen: $("#tanggal_dokumen").val()
                    }, function (response) {
                        $(".print_data").html(response.data);
            }
            );
        });
        const formdo = document.forms.namedItem("form-do");
        formdo.addEventListener(
                "submit",
                (event) => {
            please_wait(function () {});
            request("form-do").then(
                    response => {
                        unblockUI(function () {
                            alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                        }, 100);
                        if (response.status === 200)
                            window.location.replace('<?php echo base_url('warehouse/deliveryorder/edit/') ?>' + response.data.data);
                    }
            ).catch(err => {
                unblockUI(function () {});
                alert_modal_warning("Hubungi Dept IT");
            }
            );
            event.preventDefault();
        },
                false
                );
    }
    )
</script>
