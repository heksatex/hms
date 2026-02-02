<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu-new.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar-new.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">

                </section>
                <section class="content">
                    <div class="box">
                        <form class="form-horizontal" method="POST" name="form-coa" id="form-coa" action="<?= base_url("accounting/coa/simpan") ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title">Tambah COA</h3> 
                                <div class="box-body">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="field-group">
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label">COA Level 1</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <select class="form-control input-sm level_1" name="level_1" style="width: 100%">
                                                            <option></option>
                                                            <?php
                                                            foreach ($coa as $key => $value) {
                                                                ?>
                                                                <option value="<?= $value->kode_coa ?>"><?= "{$value->nama}" ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label">COA Level 2</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <select class="form-control input-sm level_2" name="level_2" style="width: 100%">

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label">COA Level 3</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <select class="form-control input-sm level_3" name="level_3" style="width: 100%">

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label">COA Level 4</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <select class="form-control input-sm level_4" name="level_4" style="width: 100%">

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
                                                    <div class="col-xs-4"><label class="form-label required">Kode COA</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input type="text" name="kode_coa" id="kode_coa" class="form-control input-sm"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label required">Nama COA</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input type="text" name="nama_coa" id="nama_coa" class="form-control input-sm"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4"><label class="form-label required">Saldo Normal</label></div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <label class="btn btn-secondary">
                                                            <input type="radio" name="saldo_normal" value="D" checked> Debet
                                                        </label>
                                                        <label class="btn btn-secondary">
                                                            <input type="radio" name="saldo_normal" value="C"> Credit
                                                        </label>
                                                    </div>
                                                </div>
                                                <button class="btn btn-default btn-sm btn-save hide"><span class="glyphicon glyphicon-save"></span> Simpan </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/js.php") ?>
            </footer>
        </div>
        <script>
            const select2 = function (kelas_1, kelas_2) {
                $(kelas_2).select2({
                    allowClear: true,
                    placeholder: "Pilih",
                    dataType: 'JSON',
                    ajax: {
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/coa/get_coa",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term,
                                parent: $(kelas_1).val()
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.kode_coa,
                                    text: item.nama
                                });
                            });
                            return {
                                results: results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                        }
                    },

                });
            };

            const clear = ((level, ke = 1) => {
                for (var i = (ke + 1); i <= 4; i++) {
                    $(level + i).val(null).trigger('change');
            }
            });

            $(function () {
                $(".level_1").select2({
                    allowClear: false,
                    placeholder: "Pilih"
                });
                select2(".level_1", ".level_2");
                select2(".level_2", ".level_3");
                select2(".level_3", ".level_4");
                $(".level_1").on("change", function () {
                    clear(".level_", 1);
                });
                $(".level_2").on("change", function () {
                    clear(".level_", 2);
                });
                $(".level_3").on("change", function () {
                    clear(".level_", 3);
                });
                const formdo = document.forms.namedItem("form-coa");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-coa").then(
                            response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200)
                                    window.location.replace(response.data.url);
                            }
                    );
                    event.preventDefault();
                },
                        false
                        );
                $("#btn-simpan").on("click", function (e) {
                    e.preventDefault();
                    $(".btn-save").trigger("click");
                });
            });
        </script>
    </body>
</html>