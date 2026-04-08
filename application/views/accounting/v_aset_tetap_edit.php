<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php"); ?>
        <style>
            #btn-done,#btn-simpan,.btn-generate,.btn-posted,.btn-unposted,#btn-cancel{
                display: none !important;
            }
            <?php if ($datas->status === "done") {
                ?>
                #btn-cancel,.btn-posted,.btn-unposted{
                    display: inline !important;
                }

                <?php
            }
            if ($datas->status === "draft") {
                ?>
                #btn-done,#btn-simpan,.btn-generate{
                    display: inline !important;
                }
                <?php
            }
            ?>
            .list-item-div {
                display: list-item; /* Makes the div behave like an <li> */
                list-style-type: disc; /* Optional: specifies the marker type (e.g., disc, circle, square, or an image) */
                margin-left: 20px; /* Optional: adds indentation */
                line-height: 1.2
            }
            .btn-posted {
                background-color: #00a65a !important;
            }
            .btn-unposted {
                background-color: #ffcc00 !important;
            }
            .addons {
                padding: 1px 6px;
                border-color: transparent !important;
                background-color:transparent !important;
            }
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse">
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
                <div id ="status_bar">
                    <?php
                    $data['jen_status'] = $datas->status;
                    $this->load->view("admin/_partials/statusbar-new.php", $data);
                    ?>
                </div>
            </section>
            <section class="content">
                <div class="box">
                    <form class="form-horizontal" method="POST" name="form-aset" id="form-aset" action="<?= base_url("accounting/asettetap/update/{$id}") ?>">
                        <div class="box-header with-border">
                            <h3 class="box-title"><strong><?= $datas->no_aset ?></strong></h3>
                        </div>
                        <div class="box-body">
                            <div class="col-md-6 col-xs-12">
                                <div class="field-group">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Nama</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="nama" id="nama"  class="form-control input-sm" required value="<?= $datas->nama ?>" <?= ($datas->status === "draft") ? "" : "readonly" ?>/>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Tanggal Beli</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group tgl-def-format">
                                                    <input type="text" name="tanggal_beli" id="tanggal_beli" class="form-control input-sm" value="<?= $datas->tanggal_beli ?>" required <?= ($datas->status === "draft") ? "" : "readonly" ?>/>
                                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Tanggal Pakai</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group tgl-def-format">
                                                    <input type="text" name="tanggal_pakai" id="tanggal_pakai" class="form-control input-sm" value="<?= $datas->tanggal_pakai ?>" required <?= ($datas->status === "draft") ? "" : "readonly" ?>/>
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
                                                <input type="text" name="harga" id="harga"  class="form-control input-sm harga" required value="<?= number_format($datas->harga, 2) ?>"
                                                       pattern="^-?\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' class="form-control input-sm text-right" <?= ($datas->status === "draft") ? "" : "readonly" ?>/>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label class="form-label required">Nilai Sisa</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="nilai_sisa" id="nilai_sisa"  class="form-control input-sm nilai_sisa" value="<?= number_format($datas->nilai_sisa, 2) ?>"
                                                       pattern="^-?\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' class="form-control input-sm text-right" <?= ($datas->status === "draft") ? "" : "readonly" ?>/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box-footer">
                            <div class="box-footer">
                                <ul class="nav nav-tabs ">
                                    <li class="<?= ($datas->status === 'draft') ? 'active' : "" ?>"><a href="#tab_1" data-toggle="tab">Setting</a></li>
                                    <li class="<?= ($datas->status !== 'draft') ? 'active' : "" ?>"><a href="#tab_2" data-toggle="tab">Tabel Penyusutan</a></li>
                                </ul>
                                <div class="tab-content"></br>
                                    <div class="tab-pane <?= ($datas->status === 'draft') ? 'active' : "" ?>" id="tab_1" >
                                        <div class="col-md-6 col-xs-12">
                                            <div class="field-group">
                                                <div class="form-group">
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label class="form-label required">Kategori</label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <select class="form-control input-sm kategori" name="kategori" required <?= ($datas->status === "draft") ? "" : "disabled" ?>>
                                                                <option value="<?= $datas->kategori ?>" selected><?= $datas->kategori ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label class="form-label required">Kelompok</label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <select class="form-control input-sm kelompok" name="kelompok" required <?= ($datas->status === "draft") ? "" : "disabled" ?>>
                                                                <option value="<?= $datas->kelompok ?>" selected><?= $datas->kelompok ?></option>
                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label class="form-label required">M. Penyusutan</label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <label class="btn btn-secondary">
                                                                <input type="radio" name="m_penyusutan" value="tarif_garis_lurus" class="penyusutan" <?= ($datas->metode === 'tarif_garis_lurus') ? "checked" : "" ?> <?= ($datas->status === "draft") ? "" : "disabled" ?>> Garis Lurus
                                                            </label>
                                                            <label class="btn btn-secondary">
                                                                <input type="radio" name="m_penyusutan" value="tarif_saldo_menurun" class="penyusutan" <?= ($datas->metode === 'tarif_saldo_menurun') ? "checked" : "" ?> <?= ($datas->status === "draft") ? "" : "disabled" ?>> Saldo Menurun
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label class="form-label required">Umur Asset</label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <div class="input-group">
                                                                <input class="form-control input-sm umur" name="umur" value="<?= $datas->umur_aset ?>" readonly>
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
                                                                <input class="form-control input-sm tarif" name="tarif" value="<?= $datas->tarif_penyusutan ?>" readonly>
                                                                <span class="input-group-addon">%</span>
                                                            </div>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label class="form-label">Akun Aset</label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <select class="form-control input-sm akun_aset" name="akun_aset" <?= ($datas->status === "draft") ? "" : "disabled" ?>>
                                                                <option value="<?= $datas->akun_asset ?>" selected><?= $datas->akun_asset ?></option>
                                                            </select>

                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label class="form-label">Akun Akumulasi</label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <select class="form-control input-sm akun_akum" name="akun_akum" <?= ($datas->status === "draft") ? "" : "disabled" ?>>
                                                                <option value="<?= $datas->akun_akum_penyusutan ?>" selected><?= $datas->akun_akum_penyusutan ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label class="form-label">Akun Penyusutan</label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <select class="form-control input-sm akun_penyusutan" name="akun_penyusutan" <?= ($datas->status === "draft") ? "" : "disabled" ?>>
                                                                <option value="<?= $datas->akun_bbn_penyusutan ?>" selected><?= $datas->akun_bbn_penyusutan ?></option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <button class="btn btn-default btn-sm btn-save hide"><span class="glyphicon glyphicon-save"></span> Simpan </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane <?= ($datas->status !== 'draft') ? 'active' : "" ?>" id="tab_2" >
                                        <div class="field-group">
                                            <button type="button" class="btn btn-success btn-sm btn-generate">Generate</button>
                                        </div>
                                        <div class="col-xs-12 table-responsive tbl_lurus">
                                            <?php
                                            if ($jurnals && $datas->status !== 'draft') {
                                                if ($datas->metode === 'tarif_saldo_menurun') {
                                                    ?>
                                                    <div class="col-xs-12 col-md-4">
                                                        <?php
                                                        foreach (json_decode($datas->text_generate) as $key => $value) {
                                                            ?>
                                                            <h4>Tahun <?= ($key == ($datas->umur_aset - 1)) ? ($key + 1) . " (Tahun terakhir) " : ($key + 1) ?></h4>

                                                            <?= $value->text ?>

                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                    <div class="col-xs-12 col-md-8">
                                                        <table id="tbl-penyu-sm" class="table table-striped">
                                                            <thead>
                                                                <tr>
                                                                    <th><input type="checkbox" class="check_all"></th>
                                                                    <th>No</th>
                                                                    <th class="no">No Jurnal</th>
                                                                    <th>Ref Note</th>
                                                                    <th>Tgl Penyusutan</th>
                                                                    <th  class="text-right" style="text-align: right">Penyusutan (Bulan)</th>
                                                                    <th>Status Jurnal</th>
                                                                    <th>#</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php
                                                                $no = 0;
                                                                $totalPenyusutan = 0;
                                                                foreach ($jurnals as $key => $value) {
                                                                    $no += 1;
                                                                    $jrnl = str_replace("/", "_", $value->no_jurnal);
                                                                    $totalPenyusutan += ($value->status_jurnal === "cancel") ? 0 : $value->penyusutan_bulan;
                                                                    ?>
                                                                    <tr>
                                                                        <td>
                                                                            <input type="checkbox" class="check_post" name="check_post" value="<?= $value->no_jurnal ?>">
                                                                        </td>
                                                                        <td>
                                                                            <?= $no ?>
                                                                        </td>
                                                                        <td>
                                                                            <?= $value->no_jurnal ?> &nbsp;<span class="open-modal" data-jurnal="<?= $value->no_jurnal ?>">
                                                                                <i class="fa fa-external-link" style="color:blue;cursor: pointer;"></i>
                                                                            </span>
                                                                        </td>
                                                                        <td> <?= $value->reff_note ?></td>
                                                                        <td><?= $value->penyusutan_tgl ?></td>
                                                                        <td  class="text-right" style="text-align: right"><?= number_format($value->penyusutan_bulan, 2) ?></td>
                                                                        <td class="sts-jurnal-<?= $jrnl ?>"><?= $value->status_jurnal ?></td>
                                                                        <td>
                                                                            <?php
                                                                            if ($value->status_jurnal !== "cancel") {
                                                                                ?>
                                                                                <button type="button" class="btn btn-success btn-sm btn-status btn-status-p-<?= $jrnl ?> <?= ($value->status_jurnal === "posted" ? "hide" : "") ?>" data-toggle="tooltip" data-status="posted" title="Posted" data-jurnal="<?= $value->no_jurnal ?>">
                                                                                    <i class="fa fa-check"></i></button>

                                                                                <button type="button"  class="btn btn-danger btn-sm btn-status btn-status-up-<?= $jrnl ?> <?= ($value->status_jurnal === "unposted" ? "hide" : "") ?>" data-toggle="tooltip" data-status="unposted" title="Unposted" data-jurnal="<?= $value->no_jurnal ?>">
                                                                                    <i class="fa fa-close"></i></button>
                                                                            <?php } ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </tbody>
                                                            <tfoot>
                                                                <tr>

                                                                </tr>
                                                                <tr>
                                                                    <td colspan="3">

                                                                    </td>
                                                                    <td>
                                                                        Total Penyusutan
                                                                    </td>
                                                                    <td class="text-right">
                                                                        <?= number_format(round($totalPenyusutan, 2), 2) ?>
                                                                    </td>
                                                                </tr>
                                                            </tfoot>
                                                        </table>
                                                    </div>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <table id="tbl-penyu-sl" class="table table-striped">
                                                        <thead>
                                                            <tr>
                                                                <th><input type="checkbox" class="check_all"></th>
                                                                <th>No</th>
                                                                <th class="no">No Jurnal</th>
                                                                <th>Reff note</th>
                                                                <th>Tgl Penyusutan</th>
                                                                <th  class="text-right" style="text-align: right">Penyusutan (Bulan)</th>
                                                                <th>Status Jurnal</th>
                                                                <th>#</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $temp = 0;
                                                            $totalTahunan = 0;
                                                            $totalBulanan = 0;
                                                            $totalPenyusutan = 0;
                                                            $no = 0;
                                                            foreach ($jurnals as $key => $value) {
                                                                $no += 1;
                                                                $totalPenyusutan += ($value->status_jurnal === "cancel") ? 0 : $value->penyusutan_bulan;
                                                                $jrnl = str_replace("/", "_", $value->no_jurnal);
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <input type="checkbox" class="check_post" name="check_post" value="<?= $value->no_jurnal ?>">
                                                                    </td>
                                                                    <td>
                                                                        <?= $no ?>
                                                                    </td>
                                                                    <td>
                                                                        <?= $value->no_jurnal ?> &nbsp;<span class="open-modal" data-jurnal="<?= $value->no_jurnal ?>">
                                                                            <i class="fa fa-external-link" style="color:blue;cursor: pointer;"></i>
                                                                        </span>
                                                                    </td>
                                                                    <td> <?= $value->reff_note ?></td>
                                                                    <td>
                                                                        <?= $value->penyusutan_tgl ?>
                                                                    </td>
                                                                    <td class="text-right" style="text-align: right">
                                                                        <?= number_format($value->penyusutan_bulan, 2) ?>
                                                                    </td>
                                                                    <td class="sts-jurnal-<?= $jrnl ?>"><?= $value->status_jurnal ?></td>
                                                                    <td>
                                                                        <?php
                                                                        if ($value->status_jurnal !== "cancel") {
                                                                            ?>
                                                                            <button type="button" class="btn btn-success btn-sm btn-status btn-status-p-<?= $jrnl ?> <?= ($value->status_jurnal === "posted" ? "hide" : "") ?>" data-toggle="tooltip" data-status="posted" title="Posted" data-jurnal="<?= $value->no_jurnal ?>">
                                                                                <i class="fa fa-check"></i></button>

                                                                            <button type="button"  class="btn btn-danger btn-sm btn-status btn-status-up-<?= $jrnl ?> <?= ($value->status_jurnal === "unposted" ? "hide" : "") ?>" data-toggle="tooltip" data-status="unposted" title="Unposted" data-jurnal="<?= $value->no_jurnal ?>">
                                                                                <i class="fa fa-close"></i></button>
                                                                        <?php } ?>
                                                                    </td>
                                                                </tr>
                                                                <?php
                                                            }
                                                            ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr>

                                                            </tr>
                                                            <tr>
                                                                <td colspan="3">

                                                                </td>
                                                                <td>
                                                                    Total Penyusutan
                                                                </td>
                                                                <td class="text-right">
                                                                    <?= number_format(round($totalPenyusutan, 2), 2) ?>
                                                                </td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
                <button style="display: none" class="posted-data"></button>
                <button style="display: none" class="unposted-data"></button>
            </section>

        </div>
        <footer class="main-footer">
            <?php $this->load->view("admin/_partials/modal.php") ?>
            <?php $this->load->view("admin/_partials/js.php") ?>
            <?php $this->load->view("admin/_partials/footer_new.php"); ?>
        </footer>
        <script>
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
                $("#tbl-penyu-sm").DataTable({
                    iDisplayLength: 12,
                    ordering: false,
                    searching: false,
                    lengthChange: true,
                    lengthMenu: [[12, 50, -1], [12, 50, "All"]],
                    dom: "<'row'<'col-sm-4'l><'col-sm-8'p>><'row'<'col-sm-8'B><'col-sm-4'i>>",
                    buttons: [
                        {
                            "text": '<i class="fa fa-check"> <span>Posted Checked</span>',
                            "className": "btn-posted btn-sm",
                            "action": function (e, dt, node, config) {
                                $(".posted-data").trigger("click");
                            }
                        },
                        {
                            "text": '<i class="fa fa-check"> <span>unPosted Checkeds</span>',
                            "className": "btn-unposted btn-sm",
                            "action": function (e, dt, node, config) {
                                $(".unposted-data").trigger("click");
                            }
                        }
                    ],
                    "fnDrawCallback": function () {
                        $(".open-modal").on("click", function () {
                            var jurnal = $(this).data("jurnal");
                            $("#tambah_data").modal({
                                show: true,
                                backdrop: 'static'
                            });
                            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                            $('.modal-title').text(`Edit Jurnal ${jurnal}`);
                            $.post("<?= base_url("accounting/asettetap/show_jurnal/{$id}") ?>", {jurnal: jurnal}, function (data) {
                                setTimeout(function () {
                                    $(".tambah_data").html(data.data);
                                    $("#btn-tambah").hide();
                                }, 1000);
                            });
                        });
                        $(".btn-status").on("click", function () {
                            var datas = $(this).data();
                            $.ajax({
                                url: "<?= base_url("accounting/asettetap/update_status_jurnal/{$id}") ?>",
                                dataType: 'JSON',
                                type: "post",
                                data: {
                                    jurnal: datas.jurnal,
                                    status: datas.status
                                },
                                beforeSend: function (xhr) {
                                    please_wait((() => {

                                    }));
                                },
                                complete: function (jqXHR, textStatus) {
                                    unblockUI(function () {}, 500);
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                },
                                success: ((data) => {
                                    $(this).addClass("hide");
                                    var jrnl = datas.jurnal.replace(/\//g, "_");
                                    $(".sts-jurnal-" + jrnl).html(datas.status);
                                    if (datas.status === "posted")
                                        $(`.btn-status-up-${jrnl}`).removeClass("hide");
                                    else
                                        $(`.btn-status-p-${jrnl}`).removeClass("hide");

                                })
                            });
                        });
                    }
                });
                $("#tbl-penyu-sl").DataTable({
                    iDisplayLength: 12,
                    ordering: false,
                    lengthChange: true,
                    lengthMenu: [[12, 50, -1], [12, 50, "All"]],
                    dom: "<'row'<'col-sm-4'l><'col-sm-8'p>><'row'<'col-sm-8'B><'col-sm-4'i>>",
                    buttons: [
                        {
                            "text": '<i class="fa fa-check"> <span>Posted Checked</span>',
                            "className": "btn-posted btn-sm",
                            "action": function (e, dt, node, config) {
                                $(".posted-data").trigger("click");
                            }
                        },
                        {
                            "text": '<i class="fa fa-check"> <span>unPosted Checked</span>',
                            "className": "btn-unposted btn-sm",
                            "action": function (e, dt, node, config) {
                                $(".unposted-data").trigger("click");
                            }
                        }
                    ],
                    "fnDrawCallback": function () {
                        $(".open-modal").on("click", function () {
                            var jurnal = $(this).data("jurnal");
                            $("#tambah_data").modal({
                                show: true,
                                backdrop: 'static'
                            });
                            $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                            $('.modal-title').text(`Edit Jurnal ${jurnal}`);
                            $.post("<?= base_url("accounting/asettetap/show_jurnal/{$id}") ?>", {jurnal: jurnal}, function (data) {
                                setTimeout(function () {
                                    $(".tambah_data").html(data.data);
                                    $("#btn-tambah").hide();
                                }, 1000);
                            });
                        });
                        $(".btn-status").on("click", function () {
                            var datas = $(this).data();
                            $.ajax({
                                url: "<?= base_url("accounting/asettetap/update_status_jurnal/{$id}") ?>",
                                dataType: 'JSON',
                                type: "post",
                                data: {
                                    jurnal: datas.jurnal,
                                    status: datas.status
                                },
                                beforeSend: function (xhr) {
                                    please_wait((() => {

                                    }));
                                },
                                complete: function (jqXHR, textStatus) {
                                    unblockUI(function () {}, 500);
                                },
                                error: function (xhr, ajaxOptions, thrownError) {
                                },
                                success: ((data) => {
                                    $(this).addClass("hide");
                                    var jrnl = datas.jurnal.replace(/\//g, "_");
                                    $(".sts-jurnal-" + jrnl).html(datas.status);
                                    if (datas.status === "posted")
                                        $(`.btn-status-up-${jrnl}`).removeClass("hide");
                                    else
                                        $(`.btn-status-p-${jrnl}`).removeClass("hide");

                                })
                            });
                        });
                    }

                });

                setCoa(".akun_aset");
                setCoa(".akun_penyusutan");
                setCoa(".akun_akum");
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
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.kategori,
                                    text: item.kategori
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
                $(".kategori").on("change", function () {
                    $(".kelompok").val(null).trigger('change');
                    $(".umur").val("");
                    $(".tarif").val("");
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
                    $(".umur").val("");
                    $(".tarif").val("");
                    setUmurAset();
                });
                $(".penyusutan").on("change", function () {
                    $(".umur").val("");
                    $(".tarif").val("");
                    setUmurAset();
                });
                const setUmurAset = (() => {
                    $.ajax({
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/asettetap/kategori_kelompok",
                        data: {
                            param: encodeURIComponent("kategori='" + $(".kategori").val() + "' and kelompok='" + $(".kelompok").val() + "'")
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            $(".umur").val("");
                            $(".tarif").val("");
                        },
                        success: ((data) => {
                            var key = $(".penyusutan:checked").val();
                            var umur = (data.data[0] && data.data[0]['umur_tahun']) === undefined ? "" : data.data[0]['umur_tahun'];
                            var tarif = (data.data[0] && data.data[0][key]) === undefined ? "" : data.data[0][key];
                            $(".umur").val(umur);
                            $(".tarif").val(tarif);
                        })

                    });
                });

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
                                    window.location.reload();
                            }
                    );
                    event.preventDefault();
                },
                        false
                        );
                $(".btn-generate").on("click", function () {
                    $.ajax({
                        url: "<?= base_url("accounting/asettetap/generate/{$id}") ?>",
                        dataType: 'JSON',
                        type: "post",
                        data: {
                            metode: $(".penyusutan:checked").val(),
                            tgl_pakai: $("#tanggal_pakai").val(),
                            harga: $("#harga").val(),
                            sisa: $("#nilai_sisa").val(),
                            tarif: $(".tarif").val(),
                            umur: $(".umur").val()
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {}, 500);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                        },
                        success: ((data) => {
                            $(".tbl_lurus").html(data.data);
                        })
                    });
                });
                $("#btn-done").on("click", function () {
                    $.ajax({
                        url: "<?= base_url("accounting/asettetap/confirm/{$id}") ?>",
                        dataType: 'JSON',
                        type: "post",
                        data: {
                            metode: $(".penyusutan:checked").val(),
                            tgl_pakai: $("#tanggal_pakai").val(),
                            harga: $("#harga").val(),
                            sisa: $("#nilai_sisa").val(),
                            tarif: $(".tarif").val(),
                            umur: $(".umur").val()
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {}, 500);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            alert_notify(xhr.responseJSON.icon, xhr.responseJSON.message, xhr.responseJSON.type, function () {});
                        },
                        success: ((data) => {
                            window.location.reload();
                        })
                    });
                }
                );

                const postedData = ((status) => {
                    $.ajax({
                        url: "<?= base_url("accounting/asettetap/update_status_jurnals/{$id}") ?>",
                        dataType: 'JSON',
                        type: "post",
                        data: {
                            status: status
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {}, 500);
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                        },
                        success: ((data) => {
                            window.location.reload();
                        })
                    });
                });
                $(".posted-data").on("click", function () {
//                var array = $.map($('input[name="check_post"]:checked'), function(c){return c.value; })
//                console.log(array);
                    confirmRequest("Jurnal Entries", "Posted Semua Jurnal ?", function () {
                        postedData("posted");
                    });
                });
                $(".unposted-data").on("click", function () {
                    confirmRequest("Jurnal Entries", "unPosted Semua Jurnal ?", function () {
                        postedData("unposted");
                    });
                });
                $("#btn-cancel").on("click", function () {
                    confirmRequest("Aset Tetap", "Batalkan Aset Tetap ? ", function () {
                        $.ajax({
                            url: "<?= base_url("accounting/asettetap/cancel/{$id}") ?>",
                            dataType: 'JSON',
                            type: "post",
                            beforeSend: function (xhr) {
                                please_wait((() => {

                                }));
                            },
                            complete: function (jqXHR, textStatus) {
                                unblockUI(function () {}, 500);
                            },
                            error: function (xhr, ajaxOptions, thrownError) {
                            },
                            success: ((data) => {
                                window.location.reload();
                            })
                        });
                    });
                });

                $('.check_all').on('change', function () {
                console.log($(this).is(':checked'));
                    if ($(this).is(':checked')) {
                        $(".check_post").attr("checked","checked");
                    } else {
                         $(".check_post").removeAttr("checked");
                    }
                });

            }
            );
        </script>
    </body>
</html>