<!DOCTYPE html>

<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            #btn-cancel {
                display: none;
            }
        </style>
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
                <?php
                $this->load->view("admin/_partials/sidebar.php");
                ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header" >
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Tambah</h3>
                        </div>
                            <form  class="form-horizontal" method="POST" name="form-jurnal" id="form-jurnal" action="<?= base_url('purchase/jurnalentries/simpan/') ?>">
                        <div class="box-body">
                            <button type="submit" style="display: none;" id="form-jurnal-submit"></button>
                            <div class="col-md-6 col-xs-12">
                                <div class="field-group">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Jurnal</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2 jurnal" name="jurnal" style="width: 100%" required>
                                                    <option></option>
                                                    <?php
                                                    foreach ($jurnal as $key => $value) {
                                                        ?>
                                                        <option value="<?= $value->kode ?>"><?= "{$value->nama}" ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Tanggal</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group tgl-def-format">
                                                    <input type="text" name="tanggal" id="tanggal" class="form-control input-sm" value="<?= date("Y-m-d") ?>" required/>
                                                    <span class="input-group-addon"><i class="fa fa-calendar"><span></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Periode</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm periode" name="periode" style="width: 100%">
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-12">
                                <div class="field-group">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Origin</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" class="form-control input-sm origin" name="origin">
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Reff Note</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <textarea name="reffnote" class="form-control reffnote"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
        <footer class="main-footer">
            <?php
            $this->load->view("admin/_partials/js.php")
            ?>
        </footer>  
        <script>
            $(document).ready(function () {
                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
                });
            });
            $(function () {
                $(".select2").select2({
                    placeholder: "Pilih",
                    allowClear: true
                });

                $(".periode").select2({
                    placeholder: "Pilih",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>purchase/jurnalentries/get_periode",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.periode,
                                    text: item.periode
                                });
                            });
                            return {
                                results: results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                        }
                    }
                });
                
                const form = document.forms.namedItem("form-jurnal");
                form.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-jurnal").then(
                            response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200)
                                    window.location.replace(response.data.url);
                            }
                    ).catch(err => {
                        unblockUI(function () {});
                        alert_modal_warning("Hubungi Dept IT");
                    });
                    event.preventDefault();
                },
                        false
                        );

                $("#btn-simpan").off("click").unbind("click").on("click", function () {
                        $("#form-jurnal-submit").trigger("click");
                });
                
            });
        </script>
    </body>
</html>