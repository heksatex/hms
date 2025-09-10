<!DOCTYPE html>
<html>

<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
    <style>
        .bs-glyphicons {
            padding-left: 0;
            padding-bottom: 1px;
            margin-bottom: 20px;
            list-style: none;
            overflow: hidden;
        }

        .bs-glyphicons li {
            float: left;
            width: 25%;
            height: 50px;
            padding: 10px;
            margin: 0 -1px -1px 0;
            font-size: 15px;
            line-height: 1.4;
            text-align: elft;
            border: 1px solid #ddd;
        }

        .bs-glyphicons .glyphicon {
            margin-top: 5px;
            margin-bottom: 10px;
            font-size: 20px;
        }

        .bs-glyphicons .glyphicon-class {
            display: block;
            text-align: left;
            word-wrap: break-word;
            /* Help out IE10+ with class names */
        }

        .bs-glyphicons li:hover {
            background-color: rgba(86, 61, 124, .1);
        }

        .child-container {
            padding-left: 10px;
        }

        @media (min-width: 768px) {
            .bs-glyphicons li {
                width: 50%;
            }
        }
    </style>

</head>

<body class="hold-transition skin-black fixed sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">

        <!-- main -header -->
        <header class="main-header">
            <?php $this->load->view("admin/_partials/main-menu.php") ?>
            <?php
            $data['deptid'] = $id_dept;
            $this->load->view("admin/_partials/topbar.php", $data)
            ?>
        </header>

        <!-- Menu Side Bar -->
        <aside class="main-sidebar">
            <?php $this->load->view("admin/_partials/sidebar.php") ?>
        </aside>

        <!-- Content Wrapper-->
        <div class="content-wrapper">
            <!-- Content Header (Status - Bar) -->
            <section class="content-header">
                <?php $this->load->view("admin/_partials/statusbar.php") ?>
            </section>

            <!-- Main content -->
            <section class="content">

                <!--  box content -->
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b>Form Edit - <?php echo $user->username; ?></b></h3>
                    </div>
                    <div class="box-body">
                        <form class="form-horizontal">

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div id="alert"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12">
                                    <div class="col-md-6 col-xs-12">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6"><label>Nama User </label></div>
                                            <div class="col-xs-6">
                                                <input type="text" class="form-control input-sm" name="namauser" id="namauser" value="<?php echo htmlentities($user->nama) ?>" />
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6"><label>Login </label></div>
                                            <div class="col-xs-6">
                                                <input type="text" class="form-control input-sm" name="login" id="login" value="<?php echo htmlentities($user->username) ?>" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6"><label>Telepon WA </label></div>
                                            <div class="col-xs-6">
                                                <input type="text" class="form-control input-sm" name="telepon_wa" id="telepon_wa" value="<?= htmlentities($user->telepon_wa) ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6"><label>Departemen</label></div>
                                            <div class="col-xs-6">
                                                <select type="text" class="form-control input-sm" name="departemen" id="departemen" style="width:100% !important">
                                                    <option value="">-- Pilih Departemen --</option>
                                                    <?php
                                                    foreach ($departemen as $val) {
                                                        if ($val->kode == $user->dept) {
                                                            echo "<option selected value='" . $val->kode . "' >" . $val->nama_departemen . "</option>";
                                                        } else {
                                                            echo "<option value='" . $val->kode . "' >" . $val->nama_departemen . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6"><label>Masking Procurement Purchase </label></div>
                                            <div class="col-xs-6">
                                                <select class="form-control input-sm select2" name="masking_propur[]" id="masking_propur" style="width:100% !important" multiple>
                                                    <?php foreach ($departemen as $row) { ?>
                                                        <option value='<?php echo $row->kode; ?>' <?= (in_array($row->kode, $masking_propur)) ? "selected" : "" ?>><?php echo $row->nama_departemen; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 col-xs-12">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6"><label>Level</label></div>
                                            <div class="col-xs-6">
                                                <select type="text" class="form-control input-sm" name="level" id="level" style="width:100% !important">
                                                    <option value="">-- Pilih Level --</option>
                                                    <?php
                                                    foreach ($level_akses as $val) {
                                                        if ($val->nama_level == $user->level) {
                                                            echo "<option selected>" . $val->nama_level . "</option>";
                                                        } else {
                                                            echo "<option >" . $val->nama_level . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6"><label>Sales Group</label></div>
                                            <div class="col-xs-6">
                                                <select type="text" class="form-control input-sm" name="sales_group" id="sales_group" style="width:100% !important">
                                                    <option value="">-- Pilih Sales Group --</option>
                                                    <?php
                                                    foreach ($mst_sales_group as $val) {
                                                        if ($val->kode_sales_group == $user->sales_group) {
                                                            echo "<option selected value='" . $val->kode_sales_group . "'>" . $val->nama_sales_group . "</option>";
                                                        } else {
                                                            echo "<option value='" . $val->kode_sales_group . "'>" . $val->nama_sales_group . "</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6"><label>Tanggal Dibuat </label></div>
                                            <div class="col-xs-6">
                                                <div class='input-group date' id='tanggaldibuat'>
                                                    <input type='text' class="form-control input-sm" name="tgldibuat" id="tgldibuat" readonly="readonly" value="<?php echo $user->create_date ?>" />
                                                    <span class="input-group-addon">
                                                        <span class="glyphicon glyphicon-calendar"></span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6"><label>Masking Produk </label></div>
                                            <div class="col-xs-6">
                                                <select class="form-control input-sm select2" name="masking[]" id="masking" style="width:100% !important" multiple>
                                                    <?php foreach ($category as $row) { ?>
                                                        <option value='<?php echo $row->id; ?>' <?= (in_array($row->id, $masking)) ? "selected" : "" ?>><?php echo $row->nama_category; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </form>

                        <div class="row">
                            <div class="col-md-12">
                                <!-- Custom Tabs -->
                                <div class="">
                                    <ul class="nav nav-tabs">
                                        <li class="active"><a href="#tab_1" data-toggle="tab">Hak Akses</a></li>
                                    </ul>
                                    <div class="tab-content"><br>

                                        <!-- tab1 Hak Akses -->
                                        <div class="tab-pane active" id="tab_1">
                                            <div class="col-md-12">
                                                <form class="form-horizontal">

                                                    <?php

                                                    foreach ($list_menu as $lm) {
                                                    ?>
                                                        <div class="col-md-12">
                                                            <p class="text-light-blue"><strong><?= $lm->nama; ?></strong></p>
                                                        </div>

                                                        <?php

                                                        $list_sub_menu = $this->m_user->get_list_menu_by_link_menu($lm->inisial_class);
                                                        $count_total_sub_menu = $this->m_user->get_jml_list_menu_by_link_menu($lm->inisial_class);

                                                        // set jml baris
                                                        $jml_kolom = $count_total_sub_menu / 2;
                                                        $jml_baris = intval($jml_kolom);
                                                        $count = 1;
                                                        $tambah_kolom = TRUE;
                                                        foreach ($list_sub_menu as $val) {

                                                            if ($count == 1) {

                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                            } else if ($count > $jml_baris and $tambah_kolom == TRUE) {
                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                                $tambah_kolom = FALSE;
                                                            }

                                                            $kode = $val->kode . ',';
                                                            $nama = $val->nama;
                                                        ?>
                                                            <div class="col-md-12 col-xs-12">
                                                                <?php
                                                                $margin_ = '';
                                                                $col_xs_ = 'col-xs-4';
                                                                $padding_ = '';
                                                                if ($val->is_menu_sub != '') {
                                                                    $margin_ = 'style=margin-left:10px;';
                                                                    $col_xs_ = 'col-xs-3';
                                                                    $padding_ = 'style="padding-left:5px;"';
                                                                }
                                                                $checked = '';
                                                                if (strpos($priv, $kode) == true) {
                                                                    $checked = 'checked';
                                                                }
                                                                ?>

                                                                <div class="col-xs-8" <?php echo $margin_; ?>><?php echo $nama; ?></div>
                                                                <div class="<?php echo $col_xs_; ?>" <?php echo $padding_; ?>>
                                                                    <!-- <?php if (strpos($priv, $kode) == true) { ?>
                                                                        <input type="checkbox" name="chk[]" value="<?php echo $val->kode; ?>" checked="checked">
                                                                    <?php } else { ?>
                                                                        <input type="checkbox" name="chk[]" value="<?php echo $val->kode; ?>">
                                                                    <?php } ?> -->

                                                                    <?php
                                                                    if ($val->parent == true) {
                                                                    ?>
                                                                        <input type="checkbox" name="chk[]" class="parentMenu" value="<?php echo $val->kode; ?>" data-toggle="tooltip" title=" akan ter Ceklis Setelah Pilih List lain /  Child" <?php echo $checked; ?>>
                                                                         <button type="button" class="btn btn-xs btn-default toggle-child"
                                                                                data-toggle="tooltip" title="Tampilkan/Sembunyikan Child"
                                                                                data-target="#child-<?= $val->kode; ?>">
                                                                        <span class="glyphicon glyphicon-chevron-down"></span>
                                                                        </button>
                                                                    <?php

                                                                    } else if ($val->is_menu_sub != '') {
                                                                    ?>
                                                                        <!-- <input type="checkbox" name="chk[]" class='childSubMenu' parent="<?php echo $val->is_menu_sub; ?>" value="<?php echo $val->kode; ?>" <?php echo $checked; ?> > -->
                                                                    <?php
                                                                    } else {
                                                                    ?>
                                                                        <input type="checkbox" name="chk[]" value="<?php echo $val->kode; ?>" <?php echo $checked; ?>>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <?php

                                                            if ($val->parent == 'true') { ?>
                                                                <div id="child-<?= $val->kode; ?>" class="child-container" style="margin-left: 20px; margin-top: 5px;">
                                                            <?php
                                                                $same = false;
                                                                $get_child = $this->m_user->get_child_by_parent($lm->inisial_class, $val->kode);
                                                                foreach ($get_child as $gc) {

                                                                    $checked2 = '';
                                                                    if (strpos($priv, $gc->kode) == true) {
                                                                        $checked2 = 'checked';
                                                                    }
                                                            ?>
                                                                    <div class="col-xs-8" style=margin-left:30px;><?php echo $gc->nama; ?></div>
                                                                    <div class="col-xs-3" style="padding-left:5px;">
                                                                        <input type="checkbox" name="chk[]" class='childSubMenu' parent="<?php echo $gc->is_menu_sub; ?>" value="<?php echo $gc->kode; ?>" <?php echo $checked2; ?>>
                                                                    </div>
                                                        <?php
                                                                }
                                                                echo '</div>';
                                                            }

                                                            if ($count == $jml_baris) {
                                                                echo '</div>';
                                                                echo '</div>';
                                                            }

                                                            $count++;
                                                        }
                                                        // penutup div col-md-6, dan form-group
                                                        echo '</div>';
                                                        echo '</div>';
                                                        ?>
                                                    <?php
                                                    }
                                                    // end foreach listmenu

                                                    ?>
                                                </form>

                                            </div>
                                        </div>
                                        <!-- tab1 Info Produk -->

                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->

        <footer class="main-footer">
            <?php $this->load->view("admin/_partials/modal.php") ?>
            <div id="foot">
                <?php
                $data['kode'] = $user->username;
                $data['mms'] = $mms->kode;
                $this->load->view("admin/_partials/footer.php", $data)
                ?>
            </div>
        </footer>

    </div>

    <?php $this->load->view("admin/_partials/js.php") ?>

    <script type="text/javascript">
        window.onload = function() { //hidden button
            $('#btn-generate').hide();
            $('#btn-cancel').hide();
            $('#btn-print').hide();
        }

        $(document).ready(function() {

            // Sembunyikan semua child di awal
            $('.child-container').hide();

            // Toggle child container
            $('.toggle-child').on('click', function () {
                const target = $(this).data('target');
                const $icon = $(this).find('.glyphicon');

                $(target).slideToggle(150);

                // Ganti ikon
                if ($icon.hasClass('glyphicon-chevron-down')) {
                $icon.removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-right');
                } else {
                $icon.removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-down');
                }
            });


            // Saat childSubMenu berubah
            $('.childSubMenu').on('change', function() {
                const parentId = $(this).attr('parent');
                const $parent = $('input.parentMenu[value="' + parentId + '"]');
                const $allChild = $('input.childSubMenu[parent="' + parentId + '"]');

                // Cek apakah masih ada minimal satu child yang dicentang
                const anyChecked = $allChild.filter(':checked').length > 0;

                // Kalau tidak ada satupun yang dicentang, uncheck parent
                // Kalau ada minimal satu, centang parent
                $parent.prop('checked', anyChecked);
            });


            // (Opsional) Saat parent dicentang/uncheck, semua child ikut
            $('.parentMenu').on('change', function() {
                const parentId = $(this).val();
                const checked = $(this).is(':checked');
                $('input.childSubMenu[parent="' + parentId + '"]').prop('checked', checked);
            });
        });



        //generate chk yg checked apa saja
        function gen_chk_akses() {
            var arr = $.map($('input:checkbox:checked'), function(e, i) {
                return e.value;
            });
            return arr;
        }

        //klik button simpan
        $('#btn-simpan').click(function() {
            $('#btn-simpan').button('loading');
            var arr_chk_akses = gen_chk_akses();
            arr_chk_akses = arr_chk_akses.join(',');

            if (arr_chk_akses.length == 0) {
                alert_modal_warning('Pilih Hak Akses Minimal 1 !');
            } else {
                please_wait(function() {});
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '<?php echo base_url('setting/user/simpan') ?>',
                    beforeSend: function(e) {
                        if (e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }
                    },
                    data: {
                        namauser: $('#namauser').val(),
                        login: $('#login').val(),
                        telepon_wa: $('#telepon_wa').val(),
                        tanggaldibuat: $('#tgldibuat').val(),
                        arrchkakses: arr_chk_akses,
                        departemen: $('#departemen').val(),
                        sales_group: $('#sales_group').val(),
                        level: $('#level').val(),
                        status: 'edit',
                        masking: $("#masking").val(),
                        masking_propur: $("#masking_propur").val()

                    },
                    success: function(data) {
                        if (data.sesi == "habis") {
                            //alert jika session habis
                            alert_modal_warning(data.message);
                            window.location.replace('index');
                        } else if (data.status == "failed") {
                            //jika ada form belum keiisi
                            $('#btn-simpan').button('reset');
                            unblockUI(function() {
                                setTimeout(function() {
                                    alert_notify(data.icon, data.message, data.type);
                                }, 1000);
                            });
                            document.getElementById(data.field).focus();

                        } else {
                            //jika berhasil disimpan/diubah
                            unblockUI(function() {
                                setTimeout(function() {
                                    alert_notify(data.icon, data.message, data.type);
                                }, 1000);
                            });
                            $('#btn-simpan').button('reset');
                        }
                        $("#foot").load(location.href + " #foot");

                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.responseText);
                        unblockUI(function() {});
                        $('#btn-simpan').button('reset');
                    }
                });
            }
        });

        $(function() {
            $(".select2").select2({
                allowClear: true,
                placeholder: "Pilih"
            });
        });
    </script>


</body>

</html>