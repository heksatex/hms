<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            #btn-cancel,#btn-confirm,#btn-edit,#btn-print {
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
                        <form class="form-horizontal" method="POST" name="form-faktur-penjualan" id="form-faktur-penjualan" action="<?= base_url("sales/fakturpenjualan/simpan") ?>">
                            <button class="btn btn-default btn-sm btn-save hide" type="submit"> Simpan </button>
                            <div class="box-header with-border">
                                <h3 class="box-title">Retur Penjualan</h3>
                            </div>
                            <div class="box-body">
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Tipe Penjualan</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2 tipe" name="tipe" id="tipe" style="width: 100%" required>
                                                        <?php
                                                        foreach ($tipe as $key => $value) {
                                                            ?>
                                                            <option value="<?= $key ?>"><?= $value ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">No SJ</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <div class="input-group">
                                                        <input type="text" name="no_sj" id="no_sj" class="form-control input-sm no_sj clear-tipe" required/>
                                                        <span class="input-group-addon get-no-sj" title="Cari No SJ"><i class="fa fa-search"><span></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">PO. Cust</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <textarea  class="form-control input-sm po_cust clear-tipe" id="po_cust" name="po_cust"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Marketing</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="hidden" class="form-control input-sm marketing_kode clear-tipe" id="marketing_kode" name="marketing_kode">
                                                    <input type="text" class="form-control input-sm marketing_nama clear-tipe" id="marketing_nama" name="marketing_nama" readonly>
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
                                                    <div class="input-group tgl-def-format">
                                                        <input type="text" name="tanggal" id="tanggal" class="form-control input-sm" value="<?= date("Y-m-d") ?>" required/>
                                                        <span class="input-group-addon"><i class="fa fa-calendar"><span></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Customer</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="hidden" class="form-control input-sm customer clear-tipe" id="customer" name="customer">
                                                    <input type="text" class="form-control input-sm customer_nama clear-tipe" id="customer_nama" name="customer_nama" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">No Retur Internal</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="no_retur_internal" id="no_retur_internal" class="form-control input-sm no_retur_internal"/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">No Faktur Pajak</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="no_faktur_pajak" id="no_faktur_pajak" class="form-control input-sm no_faktur_pajak"/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Kurs</label></div>
                                                <div class="col-xs-4">
                                                    <select name="kurs" id="kurs" class="form-control input-sm kurs" required>
                                                        <option value="1" selected>IDR</option>
                                                        <?php foreach ($curr as $key => $values) {
                                                            ?>
                                                            <option value="<?= $values->id ?>"><?= $values->currency ?></option>
                                                        <?php }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-xs-4">
                                                    <input type="text" name="kurs_nominal" id="kurs_nominal" value="1.00" class="form-control input-sm kurs_nominal" required/>
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
            $(document).ready(function () {
                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
                });
            });
            $(function () {
                $("#tipe").on("change", function () {
                    $(".clear-tipe").val("");
                });
                setTglFormatDef(".tgl-def-format");
                $(".tipe").select2({
                    placeholder: "Pilih Tipe",
                    allowClear: true
                });
                $(".kurs").select2({
                    placeholder: "Pilih",
                    allowClear: true
                });

                $("#btn-simpan").on("click", function (e) {
                    e.preventDefault();
                    $(".btn-save").trigger("click");
                });

                $(".get-no-sj").on("click", function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text("List SJ");
                    $("#btn-tambah").html("Pilih");
                    var tipee = $("#tipe").val();
                    $.post("<?= base_url('sales/returpenjualan/get_view_sj') ?>", {tipe: tipee}, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);
                            $("#btn-tambah").hide();

                        }, 1000);
                    });
                });

                const formdo = document.forms.namedItem("form-faktur-penjualan");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-faktur-penjualan").then(
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

            const addTotable = ((nosj) => {
                $.ajax({
                    url: "<?= base_url('sales/returpenjualan/addsj') ?>",
                    type: "POST",
                    data: {
                        no: nosj
                    },
                    beforeSend: function (xhr) {
                        please_wait(function () {});
                    },
                    success: function (data) {
                        $("#po_cust").val(data.data.keterangan);
                        $("#no_sj").val(nosj);
                        $("#marketing_kode").val(data.data.sales_kode);
                        $("#marketing_nama").val(data.data.sales_nama);
                        $("#customer").val(data.data.customer_id);
                        $("#customer_nama").val(data.data.customer);
                    },
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {
                        }, 100);
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        unblockUI(function () {
                            alert_notify("fa fa-warning", jqXHR?.responseJSON?.message, "danger", function () {}, 500);
                        }, 100);

                    }
                });
            });
        </script>
    </body>
</html>