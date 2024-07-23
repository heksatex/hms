<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .btn-data-table{
                font-family: "inherit"
            }
            .form-check-label{
                font-size: 80%;
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
                    <div id ="status_bar">
                        <?php
                        $data['jen_status'] = $picklist->status;
                        $this->load->view("admin/_partials/statusbar.php", $data)
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border" style="background-color: <?= $picklist->notifikasi ? "transfarent" : "yellow" ?>;">
                            <h3 class="box-title">Form Edit <strong> <?= $picklist->no ?> </strong></h3>
                            <div class="pull-right text-right" id="btn-header">
                                <?php
                                if (!$picklist->notifikasi) {
                                    ?>
                                    <button class="btn btn-success btn-sm btn-data-table" id="send-broadcast" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                        <i class="fa fa-whatsapp">&nbsp; Broadcast PL</i>
                                    </button>
                                <?php }
                                ?>

                                <button class="btn btn-default btn-sm btn-data-table" id="cetak-lokasi-item" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                    <i class="fa fa-print">&nbsp; Cetak Lokasi Item</i>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-picklist" id="form-picklist" action="<?= base_url('warehouse/picklist/update') ?>">
                                <button type="submit" id="btn_form_edit" style="display: none"></button>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Tanggal Input</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type='text' class="form-control input-sm" readonly value="<?= $picklist->tanggal_input ?>"  />
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Tipe Bulk</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2" name="bulk" required>
                                                    <option></option>
                                                    <?php
                                                    foreach ($bulk as $key => $value) {
                                                        $selected = ($value->id === $picklist->type_bulk_id) ? "selected" : "";
                                                        echo '<option value="' . $value->id . '" ' . $selected . '>' . $value->name . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Marketing</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2" name="sales" required>
                                                    <option></option>
                                                    <?php
                                                    foreach ($sales as $key => $value) {
                                                        $selected = ($value->kode === $picklist->sales_kode) ? "selected" : "";
                                                        echo '<option value="' . $value->kode . '" ' . $selected . '>' . $value->nama . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Jenis Jual</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2" name="jenis_jual" required>
                                                    <option></option>
                                                    <option value="export" <?= ($picklist->jenis_jual === "export") ? 'selected' : '' ?>>EXPORT</option>
                                                    <option value="lokal" <?= ($picklist->jenis_jual === "lokal") ? 'selected' : '' ?>>LOKAL</option>
                                                    <option value="lain-lain" <?= ($picklist->jenis_jual === "lain-lain") ? 'selected' : '' ?>>Lain-Lain</option>
                                                </select>
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label" >Keterangan</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <textarea type="text" class="form-control input-sm resize-ta" id="ket" name="ket"><?= $picklist->keterangan ?></textarea>
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label" >SC</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <textarea type="text" class="form-control input-sm resize-ta" id="sc" name="sc"><?= $picklist->sc ?></textarea>
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Customer</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm select2" name="customer" id="customer" required>
                                                    <option value="<?= $picklist->customer_id ?>" selected><?= $picklist->nama ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label></label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <!--<textarea type="text" class="form-control input-sm resize-ta" name="alamat" id="alamat" readonly><?= $picklist->alamat ?></textarea>-->
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="alamat" id="delivery" value="<?= $picklist->alamat ?>"
                                                           <?= $picklist->alamat_kirim === $picklist->alamat ? 'checked' : '' ?> >
                                                    <label class="form-check-label" for="delivery" id="lbl_delivery">
                                                        Alamat Pengiriman : <?= $picklist->alamat ?>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="alamat" value="<?= $picklist->alamat_invoice ?>" id="invoice"
                                                           <?= $picklist->alamat_kirim === $picklist->alamat_invoice ? 'checked' : '' ?> >
                                                    <label class="form-check-label" for="invoice" id="lbl_invoice">
                                                        Alamat Invoice : <?= $picklist->alamat_invoice ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <input type="hidden" value="<?= $ids ?>" name="ids">
                                            <input type="hidden" value="<?= $picklist->no ?>" name="no_pl">
                                            <input type="hidden" value="<?= json_encode($picklist) ?>" name="existsing">
                                        </div>

                                    </div>
                                    <div class="col-md-6 col-xs-12">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Total PCS Item</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="total_pcs"><?= $picklist->pcs_qty ?? 0 ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Total QTY Item</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="total_qty"><?= $picklist->tot_qty ?? 0 ?></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Refresh Totalan</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <button id="refresh-total" type="button"><i class="fa fa-refresh"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </form>
                            <?php $this->load->view("admin/_partials/js.php") ?>
                            <div class="row">
                                <?php
                                $this->load->view('warehouse/v_picklist_item', ["pl" => $picklist->no, 'no_sj' => $picklist->no_sj, 'sj_status' => $picklist->sj_status]);
                                ?>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>

                <?php
                $this->load->view("admin/_partials/footer.php");
                ?>
            </footer>
        </div>

        <script>

            const updateTotal = ((refresh = 0) => {
                $.ajax({
                    url: "<?= base_url('warehouse/picklist/get_total') ?>",
                    type: "post",
                    data: {
                        pl: "<?= $picklist->no ?>",
                        refresh: refresh
                    },
                    success: function (data) {
//                        location.reload();
                        $("#total_pcs").html(data?.pcs_qty);
                        $("#total_qty").html(data?.tot_qty);
                    },
                    error: function (req, error) {
                        unblockUI(function () {
                            setTimeout(function () {
                                alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                            }, 500);
                        });
                    }
                });
            });
            $(function () {

                $("#refresh-total").off("click").on("click", function () {
                    updateTotal(1);
                });

                $("#cetak-lokasi-item").on("click", function () {
                    let url = "<?= base_url("warehouse/picklist/lokasi_fisik?no=$picklist->no") ?>";
                    var win = window.open(url, "width=1000,height=700");
                    setTimeout(function () {
                        win.document.close();
                        win.print();
                        win.close();
                    }, 500);
                });
                $("#send-broadcast").on("click", function () {
                    confirmRequest("Broadcast", "Kirim Data PL ke whatsapp ? ",
                            function () {
                                please_wait(function () {});
                                $.ajax({
                                    url: "<?= base_url('warehouse/picklist/broadcast') ?>",
                                    type: "POST",
                                    data: {
                                        "pl": "<?= $picklist->no ?>"
                                    },
                                    success: function (data) {
                                        location.reload();
                                    },
                                    complete: function (jqXHR, textStatus) {
                                        unblockUI(function () {}, 100);
                                    }
                                });

                            });

                });
            });
            $('.select2').select2({
                allowClear: true,
                placeholder: 'Pilih'
            });
//            $("#btn-simpan").attr("disabled", true);
            $('.select2').on('change', function () {
                $("#btn-simpan").removeAttr("disabled");
            });
            $('.input-sm').on('change', function () {
                $("#btn-simpan").removeAttr("disabled");
            });
            const checkStatus = function () {
                if ("<?= $picklist->status ?>" === 'cancel') {
                    $("#btnShow").hide();
                } else if ("<?= $picklist->status ?>" === 'done') {
                    $("#btn-simpan").hide();
                }
            };
            $("#customer").select2({
                allowClear: true,
                placeholder: 'Pilih',
                ajax: {
                    url: "<?= base_url('warehouse/picklist/get_cust') ?>",
                    delay: 250,
                    type: "POST",
                    data: function (params) {
                        var query = {
                            search: params.term
                        };

                        return query;
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(JSON.parse(data), function (obj) {
                                return {
                                    id: obj.id,
                                    text: obj.text,
                                    address: obj.alamat,
                                    inv_address: obj.alamat_invoice
                                };
                            })
                        };
                    }
                }
            });
            $("#customer").on('select2:select', function (e) {
//                $("#alamat").val($("#customer :selected").data().data.address);
                setAddres($("#customer :selected").data().data.inv_address, $("#customer :selected").data().data.address);
            });
            $("#customer").on('select2:unselect', function (e) {
                setAddres("", "");
            });

            const setAddres = ((inv, address) => {
                $("#lbl_invoice").html("Alamat Invoice : " + inv);
                $("#lbl_delivery").html("Alamat Pengiriman : " + address);
                $("#delivery").val(address);
                $("#invoice").val(inv);
            });


            $("#btn-simpan").on('click', function () {
                confirmRequest("Picklist", "Ubah data picklist ? ", (() => {
                    if ("<?= $picklist->no_sj ?>" !== "") {
                        alert_notify('fa fa-close', "Picklist sudah masuk Delivery Order", 'danger', function () {});
                        return false;
                    }
                    $("#btn_form_edit").trigger("click");
                }));

            });
            $("#btn-print").on('click', function () {
                window.open('<?= base_url('warehouse/picklist/print?nopl=' . $picklist->no) ?>', '_blank');
            });
            const formpicklist = document.forms.namedItem("form-picklist");

            formpicklist.addEventListener(
                    "submit",
                    (event) => {
                please_wait(function () {});
                request("form-picklist").then(
                        response => {
                            if (response.status === 200)
                                window.location.replace('<?= base_url('warehouse/picklist/edit/') ?>' + response.data.data);


                        }).catch(err => {
                    unblockUI(function () {});
                    alert_modal_warning("Hubungi Dept IT");
                });
                event.preventDefault();
            },
                    false
                    );

            $("#btn-cancel").on('click', function () {
                confirmRequest("Batal Picklist", "Batalkan No Picklist <?= $picklist->no ?>", function () {
                    please_wait(function () {});
                    $.ajax({
                        url: "<?= base_url('warehouse/picklist/batal_picklist') ?>",
                        type: "post",
                        data: {
                            pl: "<?= $picklist->no ?>"
                        },
                        success: function (data) {
                            location.reload();
                        },
                        error: function (req, error) {
                            unblockUI(function () {
                                setTimeout(function () {
                                    alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                }, 500);
                            });
                        }
                    });
                });
            });
            checkStatus();
        </script>
    </body>
</html>