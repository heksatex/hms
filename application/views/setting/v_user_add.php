<!DOCTYPE html>
<html>
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
                            <h3 class="box-title">Form Tambah</h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal">

                                <div class="form-group">                  
                                    <div class="col-md-12" >
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-6 col-xs-12">                  
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>Nama User </label></div>
                                                <div class="col-xs-6">
                                                    <input type="text" class="form-control input-sm" name="namauser" id="namauser"/>
                                                </div>                                                        
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>Login </label></div>
                                                <div class="col-xs-6">
                                                    <input type="text" class="form-control input-sm" name="login" id="login">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>Telepon WA </label></div>
                                                <div class="col-xs-6">
                                                    <input type="text" class="form-control input-sm" name="telepon_wa" id="telepon_wa">
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>Departemen</label></div>
                                                <div class="col-xs-6">
                                                    <select type="text" class="form-control input-sm" name="departemen" id="departemen"  style="width:100% !important">  
                                                        <option value="">-- Pilih Departemen  --</option>
                                                        <?php
                                                        foreach ($departemen as $val) {
                                                            echo "<option value='" . $val->kode . "' >" . $val->nama_departemen . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>                                                        
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>Masking Procurement Purchase </label></div>
                                                <div class="col-xs-6">
                                                    <select  class="form-control input-sm select2" name="masking_propur[]" id="masking_propur"  style="width:100% !important" multiple>
                                                        <?php foreach ($departemen as $row) { ?>
                                                        <option value='<?php echo $row->kode; ?>'><?php echo $row->nama_departemen; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="col-md-6 col-xs-12"> 
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>Level</label></div>
                                                <div class="col-xs-6">
                                                    <select type="text" class="form-control input-sm" name="level" id="level"  style="width:100% !important">  
                                                        <option value="">-- Pilih Level  --</option>
                                                        <?php
                                                        foreach ($level_akses as $val) {
                                                            echo "<option >" . $val->nama_level . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>                                                        
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>Sales Group</label></div>
                                                <div class="col-xs-6">
                                                    <select type="text" class="form-control input-sm" name="sales_group" id="sales_group"  style="width:100% !important">                                       
                                                        <option value="">-- Pilih Sales Group --</option>
                                                        <?php
                                                        foreach ($mst_sales_group as $val) {
                                                            echo "<option value='" . $val->kode_sales_group . "'>" . $val->nama_sales_group . "</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>                                                        
                                            </div>

                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>Tanggal Dibuat </label></div>
                                                <div class="col-xs-6">
                                                    <div class='input-group date' id='tanggaldibuat' >
                                                        <input type='text' class="form-control input-sm" name="tgldibuat" id="tgldibuat" readonly="readonly" />
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-6"><label>Masking Produk </label></div>
                                                <div class="col-xs-6">
                                                    <select  class="form-control input-sm select2" name="masking[]" id="masking"  style="width:100% !important" multiple>
                                                        <?php foreach ($category as $row) { ?>
                                                            <option value='<?php echo $row->id; ?>'><?php echo $row->nama_category; ?></option>
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

                                                        <!-- sales -->
                                                        <div class="col-md-12">
                                                            <p class="text-light-blue"><strong>Sales</strong></p>
                                                        </div>

                                                        <?php
                                                        // set jml baris
                                                        $jml_kolom = $count_sales / 2;
                                                        $jml_baris = intval($jml_kolom);
                                                        $count = 1;
                                                        $tambah_kolom = TRUE;
                                                        foreach ($sales as $val) {

                                                            if ($count == 1) {

                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                            } else if ($count > $jml_baris AND $tambah_kolom == TRUE) {
                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                                $tambah_kolom = FALSE;
                                                            }

                                                            $kode = $val->kode . ',';
                                                            $nama = $val->nama;
                                                            ?>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-8"><?php echo $nama; ?></div>
                                                                <div class="col-xs-4">                                                                
                                                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                                                </div>               
                                                            </div>
                                                            <?php
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
                                                        
                                                        <!-- sales -->
                                                        <div class="col-md-12">
                                                            <p class="text-light-blue"><strong>Purchases</strong></p>
                                                        </div>

                                                        <?php
                                                        // set jml baris
                                                        $jml_kolom = $count_purchases / 2;
                                                        $jml_baris = intval($jml_kolom);
                                                        $count = 1;
                                                        $tambah_kolom = TRUE;
                                                        foreach ($purchases as $val) {

                                                            if ($count == 1) {

                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                            } else if ($count > $jml_baris AND $tambah_kolom == TRUE) {
                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                                $tambah_kolom = FALSE;
                                                            }

                                                            $kode = $val->kode . ',';
                                                            $nama = $val->nama;
                                                            ?>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-8"><?php echo $nama; ?></div>
                                                                <div class="col-xs-4">                                                                
                                                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                                                </div>               
                                                            </div>
                                                            <?php
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


                                                        <!-- ppic -->
                                                        <div class="col-md-12">
                                                            <p class="text-light-blue"><strong>PPIC</strong></p>
                                                        </div>

                                                        <?php
                                                        // set jml baris
                                                        $jml_kolom = $count_ppic / 2;
                                                        $jml_baris = intval($jml_kolom);
                                                        $count = 1;
                                                        $tambah_kolom = TRUE;
                                                        foreach ($ppic as $val) {

                                                            if ($count == 1) {

                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                            } else if ($count > $jml_baris AND $tambah_kolom == TRUE) {
                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                                $tambah_kolom = FALSE;
                                                            }

                                                            $kode = $val->kode . ',';
                                                            $nama = $val->nama;
                                                            ?>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-8"><?php echo $nama; ?></div>
                                                                <div class="col-xs-4">                                                                
                                                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                                                </div>               
                                                            </div>
                                                            <?php
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


                                                        <!-- manufacturing -->
                                                        <div class="col-md-12">
                                                            <p class="text-light-blue"><strong>Manufacturing</strong></p>
                                                        </div>

                                                        <?php
// set jml baris
                                                        $jml_kolom = $count_mo / 2;
                                                        $jml_baris = intval($jml_kolom);
                                                        $count = 1;
                                                        $tambah_kolom = TRUE;
                                                        foreach ($mo as $val) {

                                                            if ($count == 1) {

                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                            } else if ($count > $jml_baris AND $tambah_kolom == TRUE) {
                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                                $tambah_kolom = FALSE;
                                                            }

                                                            $kode = $val->kode . ',';
                                                            $nama = $val->nama;
                                                            ?>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-8"><?php echo $nama; ?></div>
                                                                <div class="col-xs-4">                                                                
                                                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                                                </div>               
                                                            </div>
                                                            <?php
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

                                                        <!-- warehouse -->
                                                        <div class="col-md-12">
                                                            <p class="text-light-blue"><strong>Warehouse</strong></p>
                                                        </div>

                                                        <?php
// set jml baris
                                                        $jml_kolom = $count_warehouse / 2;
                                                        $jml_baris = intval($jml_kolom);
                                                        $count = 1;
                                                        $tambah_kolom = TRUE;
                                                        foreach ($warehouse as $val) {

                                                            if ($count == 1) {
                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                            } else if ($count > $jml_baris AND $tambah_kolom == TRUE) {
                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                                $tambah_kolom = FALSE;
                                                            }

                                                            $kode = $val->kode . ',';
                                                            $nama = $val->nama;
                                                            ?>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-8"><?php echo $nama; ?></div>
                                                                <div class="col-xs-4">                                                                
                                                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                                                </div>               
                                                            </div>
                                                            <?php
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

                                                        <!-- lab -->
                                                        <div class="col-md-12">
                                                            <p class="text-light-blue"><strong>Lab</strong></p>
                                                        </div>

                                                        <?php
// set jml baris
                                                        $jml_kolom = $count_lab / 2;
                                                        $jml_baris = intval($jml_kolom);
                                                        $count = 1;
                                                        $tambah_kolom = TRUE;
                                                        foreach ($lab as $val) {

                                                            if ($count == 1) {

                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                            } else if ($count > $jml_baris AND $tambah_kolom == TRUE) {
                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                                $tambah_kolom = FALSE;
                                                            }

                                                            $kode = $val->kode . ',';
                                                            $nama = $val->nama;
                                                            ?>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-8"><?php echo $nama; ?></div>
                                                                <div class="col-xs-4">                                                                

                                                                    <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">

                                                                </div>               
                                                            </div>
                                                            <?php
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


                                                        <!-- report -->
                                                        <div class="col-md-12">
                                                            <p class="text-light-blue"><strong>Report</strong></p>
                                                        </div>

                                                        <?php
// set jml baris
                                                        $jml_kolom = $count_report / 2;
                                                        $jml_baris = intval($jml_kolom);
                                                        $count = 1;
                                                        $tambah_kolom = TRUE;
                                                        foreach ($report as $val) {

                                                            if ($count == 1) {

                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                            } else if ($count > $jml_baris AND $tambah_kolom == TRUE) {
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
                                                                if ($val->is_menu_sub == 'mms89') {
                                                                    $margin_ = 'style=margin-left:10px;';
                                                                    $col_xs_ = 'col-xs-3';
                                                                    $padding_ = 'style="padding-left:5px;"';
                                                                }
                                                                ?>
                                                                <div class="col-xs-8" <?php echo $margin_; ?> ><?php echo $nama; ?></div>
                                                                <div class="<?php echo $col_xs_; ?>" <?php echo $padding_; ?> >        
                                                                    <?php
                                                                    if ($val->kode == 'mms89') {
                                                                        ?>
                                                                        <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>" disabled="disabled" data-toggle="tooltip" title="Job List akan ter Ceklis Setelah Pilih Job List lain /  Child" >
                                                                    <?php } else if ($val->is_menu_sub == 'mms89') {
                                                                        ?>
                                                                        <input type="checkbox" name="chk[]" class='joblist' value="<?php echo $val->kode; ?>" >
                                                                    <?php } else {
                                                                        ?>
                                                                        <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>               
                                                            </div>
                                                            <?php
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


                                                        <!-- setting -->
                                                        <div class="col-md-12">
                                                            <p class="text-light-blue"><strong>Setting</strong></p>
                                                        </div>
                                                        <?php
// set jml baris
                                                        $jml_kolom = $count_setting / 2;
                                                        $jml_baris = intval($jml_kolom);
                                                        $count = 1;
                                                        $tambah_kolom = TRUE;
                                                        foreach ($setting as $val) {

                                                            if ($count == 1) {

                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                            } else if ($count > $jml_baris AND $tambah_kolom == TRUE) {
                                                                echo '<div class="col-md-6">';
                                                                echo '<div class="form-group">';
                                                                $tambah_kolom = FALSE;
                                                            }

                                                            $kode = $val->kode . ',';
                                                            $nama = $val->nama;
                                                            ?>
                                                            <div class="col-md-12 col-xs-12">
                                                                <div class="col-xs-8"><?php echo $nama; ?></div>
                                                                <div class="col-xs-4">         
                                                                    <?php
                                                                    if ($val->kode == 'mms91') { // default add user(open ganti password)
                                                                        ?>
                                                                        <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>" checked="checked">
                                                                    <?php } else {
                                                                        ?>
                                                                        <input type="checkbox" name="chk[]"  value="<?php echo $val->kode; ?>">
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </div>               
                                                            </div>
                                                            <?php
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
                <?php $this->load->view("admin/_partials/footer.php") ?>
            </footer>

        </div>

        <?php $this->load->view("admin/_partials/js.php") ?>

        <script type="text/javascript">

            window.onload = function () {//hidden button
                $('#btn-generate').hide();
                $('#btn-cancel').hide();
                $('#btn-print').hide();
            }

            //set tgl buat
            var datenow = new Date();
            datenow.setMonth(datenow.getMonth());
            $('#tanggaldibuat').datetimepicker({
                defaultDate: datenow,
                format: 'YYYY-MM-DD HH:mm:ss',
                ignoreReadonly: true,
            });

            //generate chk yg checked apa saja
            function gen_chk_akses() {
                var arr = $.map($('input:checkbox:checked'), function (e, i) {
                    return e.value;
                });
                return arr;
            }


            // checkbox Joblist
            $('.joblist').change(function () {

                var joblist_check = false
                $.map($('.joblist'), function (e, i) {
                    checked = $('input[class="joblist"]').is(':checked');
                    if (checked) {
                        //value = $('input[class="joblist"]:checked').val();
                        //alert(value);
                        joblist_check = true;
                    }
                });

                if (joblist_check == true) {
                    $('input[value="mms89"]').prop('checked', true);
                } else {
                    $('input[value="mms89"]').prop('checked', false);
                }

            });

            //klik button simpan
            $('#btn-simpan').click(function () {
                var arr_chk_akses = gen_chk_akses();
                arr_chk_akses = arr_chk_akses.join(',');
                if (arr_chk_akses.length == 0) {
                    alert_modal_warning('Pilih Hak Akses Minimal 1 !');
                } else {
                    $('#btn-simpan').button('loading');
                    please_wait(function () {});
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: '<?php echo base_url('setting/user/simpan') ?>',
                        beforeSend: function (e) {
                            if (e && e.overrideMimeType) {
                                e.overrideMimeType("application/json;charset=UTF-8");
                            }
                        },
                        data: {
                            namauser: $('#namauser').val(),
                            login: $('#login').val(),
                            telepon_wa: $('#telepon_wa').val(),
                            tanggaldibuat: $('#tgldibuat').val(),
                            departemen: $('#departemen').val(),
                            sales_group: $('#sales_group').val(),
                            level: $('#level').val(),
                            arrchkakses: arr_chk_akses,
                            status: 'tambah',
                            kategori: $("#kategori").val(),
                            masking:$("#masking").val(),
                            masking_propur: $("#masking_propur").val()

                        }, success: function (data) {
                            if (data.sesi == "habis") {
                                //alert jika session habis
                                alert_modal_warning(data.message);
                                window.location.replace('index');
                            } else if (data.status == "failed") {
                                //jika ada form belum keiisi
                                $('#btn-simpan').button('reset');
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify(data.icon, data.message, data.type, function () {});
                                    }, 1000);
                                });
                                document.getElementById(data.field).focus();
                            } else {
                                //jika berhasil disimpan/diubah
                                unblockUI(function () {
                                    setTimeout(function () {
                                        alert_notify(data.icon, data.message, data.type, function () {
                                            window.location.replace('edit/' + data.isi);
                                        }, 1000);
                                    });
                                });
                                $('#btn-simpan').button('reset');
                            }

                        }, error: function (xhr, ajaxOptions, thrownError) {
                            alert(xhr.responseText);
                            unblockUI(function () {});
                            $('#btn-simpan').button('reset');
                        }
                    });

                }
            });

            $(function () {
                $(".select2").select2({
                    allowClear: true,
                    placeholder: "Pilih"
                });
            });

        </script>


    </body>
</html>
