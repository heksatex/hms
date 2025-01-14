<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .cancelPL{
                color: red;
            }
            .donePL{
                color: green;
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
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    <?php $this->load->view("admin/_partials/statusbar.php") ?>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Tambah</h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" id="form-cfq" name="form-cfq" action="<?= base_url("purchase/requestforquotation/save") ?>">
                                <button type="submit" id="btnSubmit" style="display: none"></button>
                                <input type="hidden" name="jenis" value="<?= $jenis ?>">
                                <input type="hidden" name="cfb_manual" value="1">
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Supplier</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2" name="supplier" id="supplier" required>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!--                                        <div class="form-group">
                                                                                    <div class="col-xs-12">
                                                                                        <div class="col-xs-4"><label class="form-label required">Perioritas</label></div>
                                                                                        <div class="col-xs-8 col-md-8">
                                                                                            <select class="form-control input-sm select2" name="prioritas" id="prioritas">
                                                                                                <option></option>
                                                                                                <option value="urgent">Urgent</option>
                                                                                                <option value="normal">Normal</option>
                                                                                            </select>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>-->
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <!--                                        <div class="form-group">
                                                                                    <div class="col-xs-12">
                                                                                        <div class="col-xs-4"><label class="form-label required">Tanggal Order</label></div>
                                                                                        <div class="col-xs-8 col-md-8">
                                                                                            <input type="date" class="form-control input-sm" name="order_date" id="order_date" value="<?= date("Y-m-d") ?>" required>
                                                                                            <input type="hidden" name="jenis" value="<?= $jenis ?>">
                                                                                            <input type="hidden" name="cfb_manual" value="1">
                                                                                        </div>
                                                                                    </div>
                                                                                </div>-->
                                        <div class="form-group">
                                            <div class="col-xs-12">
                                                <div class="col-xs-4"><label class="form-label" >Note</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <textarea type="text" class="form-control input-sm resize-ta" id="note" name="note"></textarea>
                                                </div>                                    
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-xs-12 table-responsive">
                                    <div class="field-group">
                                        <table class="table table-condesed table-hover table-responsive rlstable" id="tbl-manual">
                                            <thead>
                                            <th>
                                                Produk
                                            </th>
                                            <th>QTY</th>
                                            <th>Satuan</th>
                                            <th>QTY Beli</th>
                                            <th>Satuan Beli</th>
                                            <th>Prioritas</th>
                                            <th>#</th>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td>
                                                        <a class="add-new"><i class="fa fa-plus"></i> Tambah Data</a>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view("admin/_partials/modal.php") ?>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {
                $("#btn-approve").hide();
                $("#btn-cancel").hide();
                $("#supplier").select2({
                    allowClear: true,
                    placeholder: "Supplier",
                    ajax: {
                        url: "<?= site_url('purchase/requestforquotation/get_supp') ?>",
                        data: function (params) {
                            var query = {
                                search: params.term
                            }
                            return query;
                        },
                        processResults: function (data) {
                            return {
                                results: data.data
                            };
                        }
                    }
                });
                var index = 0;
                $(".add-new").off('click').on("click", function () {
                    var url_item = "<?= base_url('purchase/requestforquotation/add_item') ?>";
                    $.post(url_item, {index: index}, function (success) {
                        $('#tbl-manual tbody').append(success.data);
                        index++;
//                        $(".add-new").hide();
                    });
                });

                $("#prioritas").select2({
                    allowClear: true,
                    placeholder: "Prioritas"
                });
                $("#btn-simpan").off("click").on("click", function (e) {
                    $("#btnSubmit").trigger("click");
                });

                const form = document.forms.namedItem("form-cfq");
                form.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-cfq").then(
                            response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200) {
                                    window.location.replace(response.data.url);

                                }
                            }).catch(err => {
                        unblockUI(function () {});
                        alert_modal_warning("Hubungi Dept IT");
                    });
                    event.preventDefault();
                },
                        false
                        );
            });
        </script>
    </body>
</html>