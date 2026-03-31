<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            
            #btn-done,#btn-cancel{
                display: none;
            }
        </style>
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
                        <form class="form-horizontal" method="POST" name="form-aset" id="form-aset" action="<?= base_url("{$class}/asettetap/simpan") ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title">Aset Tetap</h3> 
                            </div>
                            <div class="box-body">
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Nama</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="nama" id="nama"  class="form-control input-sm" required/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Tanggal Beli</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <div class="input-group tgl-def-format">
                                                        <input type="text" name="tanggal_beli" id="tanggal_beli" class="form-control input-sm" value="<?= date("Y-m-d") ?>" required/>
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Tanggal Pakai</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <div class="input-group tgl-def-format">
                                                        <input type="text" name="tanggal_pakai" id="tanggal_pakai" class="form-control input-sm" value="<?= date("Y-m-d") ?>" required/>
                                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Harga</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="harga" id="harga"  class="form-control input-sm harga" required
                                                           pattern="^-?\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' class="form-control input-sm text-right"/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">QTY</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="qty" id="qty"  class="form-control input-sm qty" required
                                                           class="form-control input-sm text-right"/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Nilai Sisa</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="nilai_sisa" id="nilai_sisa"  class="form-control input-sm nilai_sisa" value="0"
                                                           pattern="^-?\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' class="form-control input-sm text-right"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <div class="col-md-12">
                                    <ul class="nav nav-tabs ">
                                        <li class="active"><a href="#tab_1" data-toggle="tab">Setting</a></li>
                                    </ul>
                                    <div class="tab-content"></br>
                                        <div class="tab-pane active" id="tab_1" >
                                            <div class="col-md-6 col-xs-12">
                                                <div class="field-group">
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4"><label class="form-label required">Kategori</label></div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select class="form-control input-sm kategori" name="kategori" required>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4"><label class="form-label required">Kelompok</label></div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select class="form-control input-sm kelompok" name="kelompok" required>
                                                                </select>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4"><label class="form-label required">M. Penyusutan</label></div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <label class="btn btn-secondary">
                                                                    <input type="radio" name="m_penyusutan" value="tarif_garis_lurus" class="penyusutan" checked> Garis Lurus
                                                                </label>
                                                                <label class="btn btn-secondary">
                                                                    <input type="radio" name="m_penyusutan" value="tarif_saldo_menurun" class="penyusutan"> Saldo Menurun
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4"><label class="form-label required">Umur Asset</label></div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <div class="input-group">
                                                                    <select class="form-control input-sm umur" name="umur">
                                                                    </select>
                                                                    <span class="input-group-addon">Tahun</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6 col-xs-12">
                                                <div class="field-group">
                                                    <div class="form-group">
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4"><label class="form-label required">Tarif</label></div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <div class="input-group">
                                                                    <select class="form-control input-sm tarif" name="tarif">
                                                                    </select>
                                                                    <span class="input-group-addon">%</span>
                                                                </div>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4"><label class="form-label">Akun Aset</label></div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select class="form-control input-sm akun_aset" name="akun_aset">
                                                                </select>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4"><label class="form-label">Akun Akumulasi</label></div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select class="form-control input-sm akun_akum" name="akun_akum">
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12 col-xs-12">
                                                            <div class="col-xs-4"><label class="form-label">Akun Penyusutan</label></div>
                                                            <div class="col-xs-8 col-md-8">
                                                                <select class="form-control input-sm akun_penyusutan" name="akun_penyusutan">
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-default btn-sm btn-save hide"><span class="glyphicon glyphicon-save"></span> Simpan </button>
                                                    </div>
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
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>
                <?php $this->load->view("admin/_partials/js.php") ?>
            </footer>
        </div>
        <script>
            const rstObject = ((data) => {
                var results = [];
                $.each(data, function (index, item) {
                    results.push({
                        id: item.kategori,
                        text: item.kategori
                    });
                });

                return results;
            });

            const setCoa = ((klas) => {
                $(klas).select2({
                    placeholder: "Pilih Coa",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/kaskeluar/get_coa",
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
                                    text: item.nama,
                                    children: [{
                                            id: item.kode_coa,
                                            text: item.kode_coa
                                        }]
                                });
                            });
                            return {
                                results: results
                            };
                        }
                    }
                });
            });


            $(function () {
                $("input[data-type='currency']").on({
                    keyup: function () {
                        formatCurrency($(this));
                    },
                    drop: function () {
                        formatCurrency($(this));
                    },
                    blur: function () {
                        formatCurrency($(this), "blur");
                    }
                });
                $(".kategori").select2({
                    placeholder: "Pilih",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/asettetap/kategori_kelompok",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term,
                                param: ""
                            };
                        },
                        processResults: function (data) {
                            var results = rstObject(data.data);
                            return {
                                results: results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                        }
                    }
                });
                $(".kategori").on("change", function () {
                    $(".kelompok").val(null).trigger('change');
                    $(".umur").val(null).trigger('change');
                    $(".tarif").val(null).trigger('change');
                });

                $(".kelompok").select2({
                    placeholder: "Pilih",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/asettetap/kategori_kelompok",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term,
                                param: encodeURIComponent("kategori='" + $(".kategori").val() + "'")
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.kelompok,
                                    text: item.kelompok
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
                $(".kelompok").on("change", function () {
                    $(".umur").val(null).trigger('change');
                    $(".tarif").val(null).trigger('change');
                });

                $(".penyusutan").on("change", function () {
                    $(".umur").val(null).trigger('change');
                    $(".tarif").val(null).trigger('change');
                });

                $(".tarif").select2({
                    placeholder: "Pilih",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/asettetap/kategori_kelompok",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term,
                                param: encodeURIComponent("kategori='" + $(".kategori").val() + "' and kelompok='" + $(".kelompok").val() + "' and umur_tahun='" + $(".umur").val() + "'")
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                var key = $(".penyusutan:checked").val();
                                results.push({
                                    id: item[key],
                                    text: item[key]
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


                $(".umur").select2({
                    placeholder: "Pilih",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/asettetap/kategori_kelompok",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term,
                                param: encodeURIComponent("kategori='" + $(".kategori").val() + "' and kelompok='" + $(".kelompok").val() + "'")
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.umur_tahun,
                                    text: item.umur_tahun
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

                $(".umur").on("change", function () {
                    $(".tarif").val(null).trigger('change');
                });

                setCoa(".akun_aset");
                setCoa(".akun_penyusutan");
                setCoa(".akun_akum");

                $("#btn-simpan").on("click", function (e) {
                    e.preventDefault();
                    $(".btn-save").trigger("click");
                });

                const formdo = document.forms.namedItem("form-aset");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-aset").then(
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

            });
        </script>
    </body>
</html>