
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
    </head>

    <body class="hold-transition skin-black fixed sidebar-mini">
        <!-- Site wrapper -->
        <div class="wrapper">

            <!-- main -header -->
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php
                $data['deptid'] = $list->dept_id;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>

            <!-- Menu Side Bar -->
            <aside class="main-sidebar">
                <?php
                $this->load->view("admin/_partials/sidebar.php");
                ?>
            </aside>

            <!-- Content Wrapper-->
            <div class="content-wrapper">
                <!-- Content Header (Status - Bar) -->
                <section class="content-header" >
                    <div id ="status_bar">
                        <?php
                        $data['jen_status'] = $list->status;
                        $data['deptid'] = $list->dept_id;
                        $this->load->view("admin/_partials/statusbar.php", $data);
                        ?>
                    </div>
                </section>

                <!-- Main content -->
                <section class="content">
                    <!--  box content -->
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"><b><?php echo $list->kode; ?></b></h3>
                            <div class="image pull-right text-right">
                                <a href="<?php echo base_url('warehouse/penerimaanbarang/edit_barcode/' . encrypt_url($list->kode)); ?>" data-toggle="tooltip" title="Scan Mode"> 
                                    <img src="<?php echo base_url('dist/img/barcode-scan-icon.jpg'); ?>" style="width: 50%; height: auto; text-align: right;">
                                </a>
                            </div>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal">
                                <div class="form-group">                  
                                    <div class="col-md-12" >
                                        <div id="alert"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group"> 

                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Kode</label></div>
                                            <div class="col-xs-8">
                                                <input type="text" class="form-control input-sm" name="kode" id="kode" value="<?php echo $list->kode; ?>" readonly="readonly"/>
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type='text' class="form-control input-sm" name="tgl" id="tgl" value="<?php echo $list->tanggal; ?>" readonly="readonly" />
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Origin </label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type='text' class="form-control input-sm" name="origin" id="origin" value="<?php echo $list->origin; ?>" readonly="readonly" />
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>No SJ </label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type='text' class="form-control input-sm" name="no_sj" id="no_sj" value="<?php echo $list->no_sj; ?>" <?= ($list->status === 'ready') ? '' : 'readonly' ?> />
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Reff Picking </label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <textarea class="form-control input-sm" name="reff_pick" id="reff_pick" readonly="readonly" ><?php echo $list->reff_picking; ?></textarea>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                                <div class="col-md-6" >
                                    <div class="form-group">   

                                        <div class="col-md-12 col-xs-12" >
                                            <div class="col-xs-4"><label>Tanggal Kirim </label></div>
                                            <div class="col-xs-8 col-md-8">
                                                <div id="tgl_btn">
                                                    <?php
                                                    if ($list->status == 'draft' or $list->status == 'ready') {
                                                        $tgl_kirim = date('Y-m-d H:i:s');
                                                        ?>
                                                        <?php
                                                    } else {
                                                        $tgl_kirim = $list->tanggal_transaksi;
                                                    }
                                                    ?>
                                                    <input type='text' class="form-control input-sm" name="tgl_transaksi" id="tgl_transaksi"   value="<?php echo $tgl_kirim ?>" readonly="readonly" />
                                                </div>
                                            </div>                                    
                                        </div>

                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Tanggal Jatuh Tempo </label></div>
                                            <div class="col-xs-8">
                                                <input type='text' class="form-control input-sm" name="tgl_jt" id="tgl_jt"  readonly="readonly"   value="<?php echo $list->tanggal_jt; ?>"/>

                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Tanggal SJ </label></div>
                                            <div class="col-xs-8">
                                                <input type='datetime-local' class="form-control input-sm" name="tgl_sj" id="tgl_sj"  <?= ($list->status === 'ready') ? '' : 'readonly' ?>   value="<?php echo $list->tanggal_sj; ?>"/>
                                            </div>                                    
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Supplier</label></div>
                                            <div class="col-xs-8">
                                                <select class="form-control info_supp" name="supplier" id="supplier" disabled>
                                                    <option></option>
                                                    <?php
                                                    if (!empty($list->nama_partner)) {
                                                        echo "<option value='{$list->partner_id}' selected>{$list->nama_partner}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>

                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4"><label>Reff Note </label></div>
                                            <div id="ta" class="col-xs-8">
                                                <textarea class="form-control input-sm" name="note" id="note" ><?php echo $list->reff_note; ?></textarea>
                                            </div>                                    
                                        </div>

                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- Custom Tabs -->
                                        <div class="">
                                            <ul class="nav nav-tabs">
                                                <li class="active"><a href="#tab_1" data-toggle="tab">Products</a></li>
                                                <li><a href="#tab_2" data-toggle="tab">Details</a></li>
                                                <li><a href="#tab_3" data-toggle="tab">Stock Move</a></li>
                                            </ul>
                                            <div class="tab-content"><br>
                                                <div class="tab-pane active" id="tab_1">
                                                    <!-- Tabel  -->
                                                    <div class="col-md-12 table-responsive">
                                                        <table class="table table-condesed table-hover rlstable" width="100%" id ="table_prod">
                                                            <label>Products</label>
                                                            <tr>
                                                                <th class="style no">No.</th>
                                                                <th class="style" style="width: 120px;">Kode Product</th>
                                                                <th class="style">Product</th>
                                                                <th class="style" style="text-align: right;">Qty</th>
                                                                <th class="style">uom</th>
                                                                <th class="style">Tersedia</th>
                                                                <th class="style">Status</th>
                                                            </tr>
                                                            <tbody>
                                                                <?php
                                                                foreach ($items as $row) {

                                                                    if ($row->sum_qty > $row->qty)
                                                                        $color = "red";
                                                                    else if ($row->sum_qty < $row->qty)
                                                                        $color = 'blue';
                                                                    else
                                                                        $color = "black";
                                                                    ?>  
                                                                    <tr class="num">
                                                                        <td></td>
                                                                        <td><?php echo $row->kode_produk; ?></td>
                                                                        <td>
                                                                            <?php
                                                                            if ($smove['method'] != "GRG|OUT" OR $row->status_barang == 'done') {
                                                                                echo $row->nama_produk;
                                                                            } else {
                                                                                ?>
                                                                                <a href="javascript:void(0)" onclick="tambah('<?php echo htmlentities($row->nama_produk); ?>', '<?php echo $row->kode_produk ?>', '<?php echo $list->move_id ?>')"><?php echo $row->nama_produk ?></a>
                                                                            <?php } ?>
                                                                            <?php
                                                                            if ($row->status_barang == 'done' or $row->status_barang == 'cancel' OR $akses_menu == 0) {
                                                                                echo "";
                                                                            } else {
                                                                                ?>
                                                                                <a href="javascript:void(0)" onclick="tambah_quant('<?php echo htmlentities($row->nama_produk); ?>', '<?php echo $row->kode_produk ?>', '<?php echo $list->move_id ?>', '<?php echo $row->origin_prod ?>')" data-toggle="tooltip" title="Tambah Quant"> 
                                                                                    <span class="glyphicon  glyphicon-share"></span></a>
                                                                            <?php } ?>
                                                                        </td>
                                                                        <td align="right"><?php echo number_format($row->qty, 2) ?></td>
                                                                        <td><?php echo $row->uom ?></td>
                                                                        <td style="color:<?php echo $color; ?>"><?php echo $row->sum_qty ?></td>
                                                                        <td><?php
                                                                            if ($row->status_barang == 'cancel')
                                                                                echo 'Batal';
                                                                            else
                                                                                echo $row->status_barang;
                                                                            ?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- Tabel  -->
                                                </div>

                                                <div class="tab-pane" id="tab_2">
                                                    <!-- Tabel  -->
                                                    <div class="col-md-12 table-responsive">
                                                        <table class="table table-condesed table-hover rlstable" width="100%" id ="table_items">
                                                            <label>Details Product</label>
                                                            <tr>
                                                                <th class="style no">No.</th>
                                                                <th class="style" style="width: 120px;">Kode Product</th>
                                                                <th class="style">Product</th>
                                                                <th class="style">lot</th>
                                                                <th class="style" style="text-align: right;">Qty</th>
                                                                <th class="style">uom</th>
                                                                <th class="style" style="text-align: right;">Qty2</th>
                                                                <th class="style">uom2 </th>
                                                                <th class="style">Grade </th>
                                                                <?php if ($show_lebar['show_lebar'] == 'true') { ?>
                                                                    <th class="style" style="text-align: right;">Lbr.Greige</th>
                                                                    <th class="style" style="text-align: right;">Lbr.Jadi</th>
                                                                <?php } ?>
                                                                <th class="style">Reff Note</th>
                                                                <th class="style">Status</th>
                                                                <th class="style">Quant Id</th>
                                                                <th class="style no"></th>
                                                            </tr>
                                                            <tbody>
                                                                <?php
                                                                foreach ($smi as $row) {
                                                                    ?>
                                                                    <tr class="num">
                                                                        <td></td>
                                                                        <td><?php echo $row->kode_produk ?></td>
                                                                        <td><?php echo $row->nama_produk ?></td>
                                                                        <td><?php echo $row->lot ?></td>
                                                                        <td align="right"><?php echo number_format($row->qty, 2) ?></td>
                                                                        <td><?php echo $row->uom ?></td>
                                                                        <td align="right"><?php echo number_format($row->qty2, 2) ?></td>
                                                                        <td><?php echo $row->uom2 ?></td>
                                                                        <td><?php echo $row->nama_grade ?></td>
                                                                        <?php if ($show_lebar['show_lebar'] == 'true') { ?>
                                                                            <td align="right"><?php echo $row->lebar_greige . ' ' . $row->uom_lebar_greige; ?></td>
                                                                            <td align="right"><?php echo $row->lebar_jadi . ' ' . $row->uom_lebar_jadi; ?></td>
                                                                        <?php } ?>
                                                                        <td><?php echo $row->reff_note ?></td>
                                                                        <td><?php
                                                                            if ($row->status == 'cancel')
                                                                                echo 'Batal';
                                                                            else
                                                                                echo $row->status;
                                                                            ?></td>
                                                                        <td><?php echo $row->quant_id ?></td>
                                                                        <td class="no" align="center" >
                                                                            <?php if ($akses_menu > 0 AND $show_delete == true) { ?>
                                                                                <a onclick="hapus('<?php echo $list->kode ?>', '<?php echo $list->move_id ?>', '<?php echo $row->kode_produk ?>', '<?php echo htmlentities($row->nama_produk) ?>', '<?php echo $row->quant_id ?>', '<?php echo $row->row_order ?>', '<?php echo $row->status ?>')"  href="javascript:void(0)"><i class="fa fa-trash" style="color: red"></i> 
                                                                                </a>
                                                                            <?php } ?>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- Tabel  -->
                                                </div>

                                                <div class="tab-pane" id="tab_3">
                                                    <div class="col-md-12"><label>Informasi Stock Move</label></div>
                                                    <div class="col-md-6">
                                                        <div class="form-group"> 
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4"><label>Move Id</label></div>
                                                                <div class="col-xs-8">
                                                                    <?php echo ": " . $smove['move_id']; ?>
                                                                </div>                                    
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4"><label>Create Date </label></div>
                                                                <div class="col-xs-8 col-md-8">
                                                                    <?php echo ": " . $smove['create_date']; ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4"><label>Origin </label></div>
                                                                <div class="col-xs-8 col-md-8">
                                                                    <?php echo ": " . $smove['origin']; ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4"><label>Method</label></div>
                                                                <div class="col-xs-8 col-md-8">
                                                                    <?php echo ": " . $smove['method']; ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group"> 
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4"><label>Lokasi Dari</label></div>
                                                                <div class="col-xs-8">
                                                                    <?php echo ": " . $smove['lokasi_dari']; ?>
                                                                </div>                                    
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4"><label>Lokasi Tujuan </label></div>
                                                                <div class="col-xs-8 col-md-8">
                                                                    <?php echo ": " . $smove['lokasi_tujuan']; ?>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4"><label>Status </label></div>
                                                                <div class="col-xs-8 col-md-8">
                                                                    <?php
                                                                    if ($smove['status'] == 'cancel')
                                                                        echo ': Batal';
                                                                    else
                                                                        echo ': ' . $smove['status'];
                                                                    ?>
                                                                    <input type="hidden" name="status" id="status" value="<?php echo $smove['status'] ?>">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-4">
                                                                    <label>
                                                                        <?php
                                                                        if ($type_mo == 'colouring') {
                                                                            if (!empty($mo['nama'])) {
                                                                                echo 'MG ' . $mo['nama'];
                                                                            } else {
                                                                                echo 'MG';
                                                                            }
                                                                        } else {
                                                                            if (!empty($mo['nama'])) {
                                                                                echo 'MO ' . $mo['nama'];
                                                                            } else {
                                                                                echo 'MO';
                                                                            }
                                                                        }
                                                                        ?>
                                                                    </label>
                                                                </div>
                                                                <div class="col-xs-8 col-md-8">
                                                                    <?php
                                                                    if (!empty($mo['kode'])) {
                                                                        echo ": " . $mo['kode'];
                                                                    } else {
                                                                        echo ": -";
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- /.tab-pane -->

                                            </div>
                                            <!-- /.tab-content -->
                                        </div>
                                        <!-- nav-tabs-custom -->
                                    </div>
                                    <!-- /.col -->
                                </div>

                            </form>
                        </div>
                        <!-- /.box-body -->
                    </div>
                    <!-- /.box -->
                </section>
                <!-- /.content -->
            </div>
            <!-- /.content-wrapper -->

            <footer class="main-footer">
                <div id="foot">
                    <?php $this->load->view("admin/_partials/footer.php") ?>
                </div>
            </footer>
            <!-- Load Partial Modal -->
            <?php $this->load->view("admin/_partials/modal.php") ?>

        </div>
        <!--/. Site wrapper -->
        <?php $this->load->view("admin/_partials/js.php") ?>


        <script type="text/javascript">

            $(function () {


                $(".info_supp").select2({
                    placeholder: "Supplier",
                    allowClear: true,
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
            });

            //untuk merefresh data
            function refresh_div_in()
            {
                $("#status_bar").load(location.href + " #status_bar");
                $("#table_items").load(location.href + " #table_items");
                $("#table_prod").load(location.href + " #table_prod");
                $("#tab_3").load(location.href + " #tab_3");
                $("#foot").load(location.href + " #foot");
                $("#tgl_btn").load(location.href + " #tgl_btn");
            }


            //klik button simpan
            $("#btn-simpan").unbind("click");
            $('#btn-simpan').click(function () {

                $('#btn-simpan').button('loading');
                var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
                please_wait(function () {});
                var move_id = '<?php echo $smove['move_id']; ?>';
                var baseUrl = '<?php echo base_url(); ?>';
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '<?php echo base_url('warehouse/penerimaanbarang/simpan') ?>',
                    beforeSend: function (e) {
                        if (e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }
                    },
                    data: {
                        move_id: move_id,
                        kode: $('#kode').val(),
                        tgl_transaksi: $('#tgl_transaksi').val(),
                        reff_note: $('#note').val(),
                        deptid: deptid,
                        no_sj: $("#no_sj").val(),
                        tgl_sj: $("#tgl_sj").val()
                    }, success: function (data) {
                        if (data.sesi == "habis") {
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location = baseUrl;//replace ke halaman login
                        } else if (data.status == "failed") {
                            //jika ada form belum keisi
                            unblockUI(function () {
                                setTimeout(function () {
                                    alert_notify(data.icon, data.message, data.type, function () {});
                                }, 1000);
                            });
                            document.getElementById(data.field).focus();//focus ke field yang belum keisi
                            refresh_div_in();
                            $('#btn-simpan').button('reset');
                        } else if (data.status == "ada") {
                            alert_modal_warning(data.message);
                            unblockUI(function () {});
                            refresh_div_in();
                            $('#btn-simpan').button('reset');
                        } else {
                            //jika berhasil disimpan/diubah
                            unblockUI(function () {
                                setTimeout(function () {
                                    alert_notify(data.icon, data.message, data.type, function () {});
                                }, 1000);
                            });
                            refresh_div_in();
                            $('#btn-simpan').button('reset');
                        }

                    }, error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.responseText);
                        setTimeout($.unblockUI, 1000);
                        unblockUI(function () {});
                        refresh_div_in();
                        $('#btn-simpan').button('reset');
                    }
                });
            });


            //untuk tambah details dari tabel stock quant
            function tambah_quant(nama_produk, kode_produk, move_id, origin_prod)
            {
                $("#tambah_data").modal({
                    show: true,
                    backdrop: 'static'
                })
                var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
                $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $('.modal-title').text('Tambah Details Product Qty');
                $.post('<?php echo site_url() ?>warehouse/penerimaanbarang/tambah_data_details_quant_penerimaan',
                        {nama_produk: nama_produk, kode_produk: kode_produk, move_id: move_id, deptid: deptid, origin: $("#origin").val(), origin_prod: origin_prod},
                        function (html) {
                            setTimeout(function () {
                                $(".tambah_data").html(html);
                            }, 2000);
                        }
                );
            }

            //untuk hapus details item
            function hapus(kode, move_id, kode_produk, nama_produk, quant_id, row_order, status)
            {
                var baseUrl = '<?php echo base_url(); ?>';
                var deptid = "<?php echo $list->dept_id; ?>";//parsing data id dept untuk log history
                var status_head = "<?php echo $list->status ?>";

                if (status_head == 'done' || status == 'done') {
                    alert_modal_warning('Maaf, Data Tidak Bisa dihapus !');
                } else {
                    bootbox.dialog({
                        message: "Apakah Anda ingin menghapus data ?",
                        title: "<i class='glyphicon glyphicon-trash'></i> Delete !",
                        buttons: {
                            danger: {
                                label: "Yes ",
                                className: "btn-primary btn-sm",
                                callback: function () {
                                    please_wait(function () {});
                                    $.ajax({
                                        dataType: "json",
                                        type: 'POST',
                                        url: "<?php echo site_url('warehouse/penerimaanbarang/hapus_details_items') ?>",
                                        data: {kode: kode, move_id: move_id, kode_produk: kode_produk, nama_produk: nama_produk, quant_id: quant_id, row_order: row_order, deptid: deptid},
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.responseText);
                                            unblockUI(function () {})
                                        }
                                    })
                                            .done(function (response) {
                                                if (response.sesi == 'habis') {
                                                    alert_modal_warning(response.message);
                                                    window.location = baseUrl;//replace ke halaman login
                                                    unblockUI(function () {})
                                                } else if (response.status == 'failed') {
                                                    alert_modal_warning(response.message);
                                                    refresh_div_in();
                                                    unblockUI(function () {})
                                                } else {
                                                    refresh_div_in();
                                                    unblockUI(function () {
                                                        setTimeout(function () {
                                                            alert_notify(data.icon, data.message, data.type, function () {});
                                                        }, 1000);
                                                    });
                                                }
                                            })
                                }
                            },
                            success: {
                                label: "No",
                                className: "btn-default  btn-sm",
                                callback: function () {
                                    $('.bootbox').modal('hide');
                                }
                            }
                        }
                    });
                }
            }


            //klik button cek stock
            $("#btn-stok").unbind("click");
            $('#btn-stok').click(function () {
                var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
                $('#btn-stok').button('loading');
                please_wait(function () {});
                var move_id = '<?php echo $smove['move_id']; ?>';
                var baseUrl = '<?php echo base_url(); ?>';
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '<?php echo base_url('warehouse/penerimaanbarang/cek_stok') ?>',
                    beforeSend: function (e) {
                        if (e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }
                    },
                    data: {move_id: move_id, kode: $('#kode').val(), deptid: deptid, origin: $('#origin').val()
                    }, success: function (data) {
                        if (data.sesi == "habis") {
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location = baseUrl;//replace ke halaman login
                        } else if (data.status == "failed") {
                            alert_modal_warning(data.message);
                            unblockUI(function () {});
                            refresh_div_in();
                            $('#btn-stok').button('reset');
                            if (data.status_kurang == "yes") {
                                alert_notify(data.icon2, data.message2, data.type2, function () {});
                            }
                        } else {

                            if (data.terpenuhi == "yes") {
                                unblockUI(function () {});
                            } else {
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify(data.icon, data.message, data.type, function () {});
                                    }, 1000);
                                });
                            }
                            refresh_div_in();
                            $('#btn-stok').button('reset');
                        }

                    }, error: function (xhr, ajaxOptions, thrownError) {
                        alert(xhr.responseText);
                        setTimeout($.unblockUI, 1000);
                        unblockUI(function () {});
                        refresh_div_in();
                        $('#btn-stok').button('reset');
                    }
                });
            });



            //untuk aksi kirim barang
            $(document).on('click', '#btn-kirim', function (e) {
                var move_id = '<?php echo $smove['move_id']; ?>';
                var status = $('#status').val();
                var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history
                var method = '<?php echo $smove['method']; ?>';
                if (status == 'cancel') {
                    var message = 'Maaf, Data Tidak bisa Dikirim, Data Sudah dibatalkan !';
                    alert_modal_warning(message);
                } else if (status == 'done') {
                    var message = 'Maaf, Data Sudah Terkirim !';
                    alert_modal_warning(message);
                } else if (status == 'draft') {
                    var message = "Maaf, Product Belum ready !";
                    alert_modal_warning(message);

                } else {
                    bootbox.dialog({
                        message: "Anda yakin ingin mengirim ?",
                        title: "<i class='glyphicon glyphicon-send'></i> Send !",
                        buttons: {
                            danger: {
                                label: "Yes ",
                                className: "btn-primary btn-sm",
                                callback: function () {
                                    please_wait(function () {});
                                    $('#btn-kirim').button('loading');
                                    $.ajax({
                                        type: 'POST',
                                        dataType: 'json',
                                        url: "<?php echo site_url('warehouse/penerimaanbarang/kirim_barang') ?>",
                                        data: {kode: $('#kode').val(), move_id: move_id, deptid: deptid, origin: $('#origin').val(), method: method, mode: "list"},
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.responseText);
                                            $('#btn-kirim').button('reset');
                                            unblockUI(function () {});
                                            refresh_div_in();
                                        }
                                    })
                                            .done(function (response) {
                                                if (response.sesi == 'habis') {//jika session habis
                                                    alert_modal_warning(response.message);
                                                    window.location.replace('../index');
                                                } else if (response.status == 'draft' || response.status == 'ada' || response.status == 'not_valid') {
                                                    //jika ada item masih draft/status sudah terkirim/lokasi lot tidak valid
                                                    unblockUI(function () {});
                                                    alert_modal_warning(response.message);
                                                    refresh_div_in();
                                                    $('#btn-kirim').button('reset');
                                                } else {
                                                    if (response.backorder == "yes") {
                                                        alert_modal_warning(response.message2);
                                                    }
                                                    unblockUI(function () {
                                                        setTimeout(function () {
                                                            alert_notify(response.icon, response.message, response.type, function () {});
                                                        }, 1000);
                                                    });
                                                    refresh_div_in();
                                                    $('#btn-kirim').button('reset');
                                                }

                                            })
                                }
                            },
                            success: {
                                label: "No",
                                className: "btn-default  btn-sm",
                                callback: function () {
                                    $('.bootbox').modal('hide');
                                }
                            }
                        }
                    });
                }
            });


            //klik button batal penerimaan barang
            $("#btn-cancel").unbind("click");
            $(document).on('click', '#btn-cancel', function (e) {
                var move_id = '<?php echo $smove['move_id']; ?>';
                var status = $('#status').val();
                var method = '<?php echo $smove['method']; ?>';
                var deptid = "<?php echo $list->dept_id; ?>"//parsing data id dept untuk log history

                if (status == 'cancel') {
                    var message = 'Maaf, Data Sudah dibatalkan !';
                    alert_modal_warning(message);

                } else if (status == 'done') {
                    var message = 'Maaf, Data tidak bisa dibatalkan, Data Sudah Terkirim !';
                    alert_modal_warning(message);

                } else {
                    bootbox.dialog({
                        message: "Anda yakin ingin membatalkan Penerimaan Barang ?",
                        title: "<i class='glyphicon glyphicon-trash'></i> Cancel !",
                        buttons: {
                            danger: {
                                label: "Yes ",
                                className: "btn-primary btn-sm",
                                callback: function () {
                                    please_wait(function () {});
                                    $('#btn-cancel').button('loading');
                                    $.ajax({
                                        type: 'POST',
                                        dataType: 'json',
                                        url: "<?php echo site_url('warehouse/penerimaanbarang/batal_penerimaan_barang') ?>",
                                        data: {kode: $('#kode').val(), move_id: move_id, deptid: deptid},
                                        error: function (xhr, ajaxOptions, thrownError) {
                                            alert(xhr.responseText);
                                            $('#btn-cancel').button('reset');
                                            unblockUI(function () {});
                                            refresh_div_in();
                                        }
                                    })
                                            .done(function (response) {
                                                if (response.sesi == 'habis') {//jika session habis
                                                    alert_modal_warning(response.message);
                                                    window.location.replace('../index');
                                                } else if (response.status == 'failed') {

                                                    unblockUI(function () {});
                                                    alert_modal_warning(response.message);
                                                    refresh_div_in();
                                                    $('#btn-cancel').button('reset');
                                                } else {

                                                    unblockUI(function () {
                                                        setTimeout(function () {
                                                            alert_notify(response.icon, response.message, response.type, function () {});
                                                        }, 1000);
                                                    });
                                                    refresh_div_in();
                                                    $('#btn-cancel').button('reset');
                                                }
                                            })
                                }
                            },
                            success: {
                                label: "No",
                                className: "btn-default  btn-sm",
                                callback: function () {
                                    $('.bootbox').modal('hide');
                                }
                            }
                        }
                    });
                }
            });

            //modal mode print
            $(document).on('click', '#btn-print', function (e) {
                e.preventDefault();
                var kode = "<?php echo $list->kode; ?>";
                var departemen = "<?php echo $list->dept_id; ?>";
                var status = $("#status").val();

                if (kode == "") {
                    alert_modal_warning('Kode Penerimaan Barang Kosong !');
                } else if (status != 'done') {
                    alert_modal_warning('Print Penerimaan Barang Hanya bisa di Print saat statusnya "Terkirim" ! ');
                } else {
                    var url = '<?php echo base_url() ?>warehouse/penerimaanbarang/print_penerimaan_barang';
                    //          window.open(url+'?kode='+ kode+'&&departemen='+ departemen,'_blank');
                    if (departemen === "RCV") {
                        $.ajax({
                            type: "get",
                            url: url+"_rcv",
                            data: {
                                kode: kode,
                                departemen: departemen
                            },
                            beforeSend: function () {
                                please_wait(function () {});
                            },
                            success: function (data) {
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify('fa fa-check', data.message, 'success', function () {});
                                    }, 500);
                                });
                            }, error: function (req, error) {
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                                    }, 500);
                                });
                            }
                        });
                    } else {
                        window.open(url+'?kode='+ kode+'&&departemen='+ departemen,'_blank');
                    }

                }
            });


        </script>


    </body>
</html>
