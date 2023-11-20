<!DOCTYPE html>
<html lang="en">
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
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Edit <strong> <?= $picklist->no ?> </strong></h3>
                            
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
                                            <div class="col-xs-4"><label class="form-label required">Sales</label></div>
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
                                            <div class="col-xs-4"><label class="form-label required">Customer</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control input-sm" name="customer" id="customer" required>
                                                    <option value="<?= $picklist->customer_id ?>" selected><?= $picklist->nama ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label></label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <textarea type="text" class="form-control input-sm resize-ta" name="alamat" id="alamat" readonly><?= $picklist->alamat ?></textarea>
                                            </div>
                                            <input type="hidden" value="<?= $ids ?>" name="ids">
                                            <input type="hidden" value="<?= $picklist->no ?>" name="no_pl">
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php $this->load->view("admin/_partials/js.php") ?>
                            <div class="row">
                                <?php
                                if ($picklist->status !== "cancel")
                                    $this->load->view('warehouse/v_picklist_item', ["pl" => $picklist->no]);
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
            const checkStatus = function () {
                if ("<?= $picklist->status ?>" === 'cancel') {
                    $("#btnShow").hide();
                }
            };
            $('.select2').select2({
                allowClear: true,
                placeholder: 'Pilih'
            });
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
                                    address: obj.alamat
                                };
                            })
                        };
                    }
                }
            });
            $("#customer").on('select2:select', function (e) {
                $("#alamat").val($("#customer :selected").data().data.address);
            });
            $("#customer").on('select2:unselect', function (e) {
                $("#alamat").val("");
            });

            $("#btn-simpan").on('click', function () {
                $("#btn_form_edit").trigger("click");
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
            $("#btn-confirm").on('click', function () {
                please_wait(function () {});
                $.ajax({
                    url: "<?= base_url('warehouse/picklist/update_status') ?>",
                    type: "post",
                    data: {
                        pl: "<?= $picklist->no ?>",
                        status: $(this).attr("data-value")
                    },
                    success: function (data) {
                        location.reload();
                    },
                    error: function (req, error) {
                        unblockUI(function () {
                            setTimeout(function () {
                                alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                            }, 1000);
                        });
                    }
                });
            });
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