<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
        <style>
            .cancel{
                color: red;
            }
            .done{
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
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-body">
                            <div class="row">
                                <form name="input" id="input" class="form-horizontal" role="form" method="POST" action="<?= base_url('warehouse/konversiuom/save') ?>">
                                    <div class="col-md-8">
                                        <div class="fields-group">
                                            <div class="form-group">
                                                <label for="tanggal" class="col-sm-2 required control-label">Dari UOM Beli</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                                        <input type="text" name="dari" class="form-control" id="dari" required>
                                                        <input type="hidden" name="posisi" id="posisi">
                                                        <input type="hidden" id="ids" name="ids">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="tanggal" class="col-sm-2 required control-label">Ke UOM Stok</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                                        <select name="ke" id="ke" class="form-control select2" required>
                                                            <option></option>
                                                            <?php
                                                            foreach ($uom as $key => $value) {
                                                                echo "<option value='" . $value->short . "'>" . $value->short . "</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="tanggal" class="col-sm-2 required control-label">Nilai Konversi</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                                        <input type="text" name="nilai" class="form-control" id="nilai" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="tanggal" class="col-sm-2 control-label">Penyebut</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                                        <input type="text" name="penyebut" id="penyebut" class="form-control" id="nilai" value="1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="tanggal" class="col-sm-2 control-label">Pembilang</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                                        <input type="text" name="pembilang" id="pembilang" class="form-control" id="nilai" value="1">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="tanggal" class="col-sm-2 required control-label">Catatan</label>
                                                <div class="col-sm-8">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><i class="fa fa-pencil"></i></span>
                                                        <input type="text" name="catatan" class="form-control" id="catatan" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="tanggal" class="col-sm-2 control-label">Perhitungan Konversi</label>
                                                <div class="col-sm-8">
                                                    <label class="btn btn-default">
                                                        <input type="radio"  name="konversi_aktif" id="konv_nilai" value="0" checked/> Dengan Nilai
                                                    </label> 
                                                    <label class="btn btn-default">
                                                        <input type="radio"  name="konversi_aktif" id="konv_pembanding" value="1" /> Dengan Pembanding
                                                    </label> 
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-sm btn-default" name="btn-save" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                            Simpan <i class="fa fa-save"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" name="btn-clear" id="btn-clear" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Batal</button>
                                    </div>

                                </form>
                            </div>
                            <br>
                            <br>
                            <div class="col-xs-12 table-responsive">
                                <table id="konversi" class="table">
                                    <thead>
                                        <tr>
                                            <th class="no">No</th>
                                            <th>Dari</th>
                                            <th>Ke</th>
                                            <th>Nilai</th>
                                            <th>Penyebut</th>
                                            <th>Pembilang</th>
                                            <th>Perhitungan</th>
                                            <th>Catatan</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <footer class="main-footer">
            <?php $this->load->view("admin/_partials/footer.php") ?>
        </footer>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script>
            $(function () {
                $(".select2").select2({
                    allowClear: true,
                    placeholder: "pilih"
                });
                const formKonversi = document.forms.namedItem("input");
                formKonversi.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("input").then(
                            response => {
                                alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                if (response.status === 200) {
                                    $("#btn-clear").trigger("click");
                                    table.ajax.reload();
                                }
                            }
                    ).catch().finally(() => {
                        $("#btn-clear").trigger("click");
                        unblockUI(function () {});
                    });
                    event.preventDefault();
                },
                        false
                        );
                const table = $('#konversi').DataTable({
                    "iDisplayLength": 50,
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "stateSave": false,
                    "ajax": {
                        "url": "<?= base_url('warehouse/konversiuom/get_data') ?>",
                        "type": "POST"
                    },
                    "columnDefs": [
                        {
                            "targets": [0, 4],
                            "orderable": false
                        }
                    ],
                    "fnDrawCallback": function () {
                        $(".edit_item").on('click', function () {
                            var data = $(this).data();
                            console.log(data);
                            $("#posisi").val("edit");
                            $("#ids").val(data.id);
                            $("#ke").val(data.ke).trigger("change");
                            $("#dari").val(data.dari);
                            $("#nilai").val(data.nilai);
                            $("#catatan").val(data.catatan);
                            $("#penyebut").val(data.penyebut);
                            $("#pembilang").val(data.pembilang);
                            if(data.konversi_aktif === 0) {
                                $("#konv_nilai").prop("checked", true);
                            }
                            else {
                                $("#konv_pembanding").prop("checked", true);
                            }
                        });
                    }
                });

                $("#btn-clear").on("click", function () {
                    $("#posisi").val("");
                    $("#ids").val("");
                    $("#input").trigger('reset');
                });
            });
        </script>
    </body>
</html>