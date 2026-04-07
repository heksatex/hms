<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
    <style type="text/css">
        h3 {
            display: block !important;
            text-align: center !important;
        }

        .divListviewHead table {
            display: block;
            height: calc(96vh - 250px);
            overflow-x: auto;
        }

        table tbody tr td {
            padding: 0px 5px 0px 5px !important;
        }

        .bold {
            font-weight: bold;
        }

        .toggle-coa {
            font-weight: bold;
            color: #444;
        }

        .coa-row:hover {
            background: #fafafa;
        }

        .level-1 {
            accent-color: #437333;
        }

        .level-2 {
            accent-color: #e78d2d;
        }

        .level-3 {
            accent-color: #2f5fb3;
        }

        .level-4 {
            accent-color: #d42459;
        }

        .level-5 {
            accent-color: #000000;
        }


        #example1 thead th {
            background-color: #f4f4f4;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }

        @media (max-width: 990px) {
            .form-group {
                padding-left: 15px;
                padding-right: 15px;
            }

            #btn-generate,
            #btn-excel {
                display: block;
                width: 100%;
                margin-top: 8px;
            }
        }

        .year-header {
            /* background-color: #e9ecef !important; */
            /* color: #333; */
            min-width: 120px;
        }

        .table_scroll {
            overflow-x: auto;
            /* Memastikan scrollbar muncul jika kolom banyak */
        }


        /*
    .btn-setTgl {
      height: 22px;
      min-width: 40px;
    }
    */
    </style>
</head>

<body class="hold-transition skin-black fixed sidebar-mini">
    <!-- Site wrapper -->
    <div class="wrapper">
        <!-- main -header -->
        <header class="main-header">
            <?php $this->load->view("admin/_partials/main-menu.php") ?>
            <?php $this->load->view("admin/_partials/topbar.php") ?>
        </header>

        <!-- Menu Side Bar -->
        <aside class="main-sidebar">
            <?php $this->load->view("admin/_partials/sidebar.php") ?>
        </aside>

        <!-- Content Wrapper-->
        <div class="content-wrapper">
            <!-- Content Header (Status - Bar) -->
            <section class="content-header">
            </section>

            <!-- Main content -->
            <section class="content">
                <!--  box content -->
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b>Laba Rugi (Yearly)</b></h3>
                    </div>
                    <div class="box-body">

                        <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <label>Periode Tahun</label>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control input-sm" name="tahun_dari" id="tahun_dari">
                                                <?php
                                                $thn_skr = date('Y');
                                                for ($x = $thn_skr; $x >= 2020; $x--) {
                                                    // Default pilih 3 tahun kebelakang untuk perbandingan
                                                    $selected = ($x == $thn_skr - 2) ? 'selected' : '';
                                                    echo "<option value='$x' $selected>$x</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <label style="margin-top: 5px;">s/d</label>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control input-sm" name="tahun_sampai" id="tahun_sampai">
                                                <?php
                                                for ($x = $thn_skr; $x >= 2020; $x--) {
                                                    $selected = ($x == $thn_skr) ? 'selected' : '';
                                                    echo "<option value='$x' $selected>$x</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <label>Opsi</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label style="font-weight: normal; margin-top: 5px;">
                                                <input type="checkbox" name="hidden_check" id="hidden_check" checked>
                                                Sembunyikan Data Kosong
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <label style="margin-right: 10px;">Level:</label>
                                            <label class="checkbox-inline"><input type="checkbox" class="level-1" value="1" name="level[]"> 1</label>
                                            <label class="checkbox-inline"><input type="checkbox" class="level-2" value="2" name="level[]"> 2</label>
                                            <label class="checkbox-inline"><input type="checkbox" class="level-3" value="3" name="level[]"> 3</label>
                                            <label class="checkbox-inline"><input type="checkbox" class="level-4" value="4" name="level[]" checked> 4</label>
                                            <label class="checkbox-inline"><input type="checkbox" class="level-5" value="5" name="level[]" checked> 5</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-12 col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-refresh"></i> Generate</button>
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                                </div>
                            </div>
                        </form>

                        <div class="box-body">
                            <div class="col-sm-12 table-responsive">
                                <div class="table_scroll">
                                    <div class="table_scroll_head">
                                        <div class="divListviewHead">
                                            <table id="example1" class="table table-condensed table-hover" border="1">
                                                <thead>
                                                    <tr id="header-row">
                                                        <th class="style bb no" style="width: 50px;">No.</th>
                                                        <th class="style bb" style="width: 100px;">Kode CoA</th>
                                                        <th class="style bb" style="min-width: 250px;">Nama Acc</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="20" align="center">Tidak ada Data</td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div id="example1_processing" class="table_processing" style="display: none; z-index:5;">
                                                <i class="fa fa-refresh fa-spin"></i> Processing...
                                            </div>
                                        </div>
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

    </div>

    <?php $this->load->view("admin/_partials/js.php"); ?>

    <div id="load_modal">
        <!-- Load Partial Modal -->
        <?php $this->load->view("admin/_partials/modal.php") ?>
    </div>

    <script type="text/javascript">
        // set date tgldari
        $('#tgldari').datetimepicker({
            // defaultDate: new Date().toLocaleString('en-US', {
            //     timeZone: 'Asia/Jakarta'
            // }),
            defaultDate: new Date(new Date().getFullYear(), new Date().getMonth(), 1), // ⬅️ tanggal 1 bulan ini
            format: 'D-MMMM-YYYY',
            ignoreReadonly: true,
            // maxDate: new Date()
        });

        // set date tglsampai
        $('#tglsampai').datetimepicker({
            defaultDate: (function() {
                const now = new Date(
                    new Date().toLocaleString('en-US', {
                        timeZone: 'Asia/Jakarta'
                    })
                );

                // set ke akhir bulan
                return new Date(now.getFullYear(), now.getMonth() + 1, 0);
            })(),
            format: 'D-MMMM-YYYY',
            ignoreReadonly: true
        });


        var arr_filter = [];


        // btn generate
        $("#btn-generate").on('click', function() {
            var this_btn = $(this);

            // Ambil value dari filter baru
            var tahun_dari = parseInt($('#tahun_dari').val());
            var tahun_sampai = parseInt($('#tahun_sampai').val());

            // Validasi: Tahun Dari tidak boleh lebih besar dari Tahun Sampai
            if (tahun_dari > tahun_sampai) {
                alert_modal_warning('Maaf, Tahun Sampai tidak boleh kurang dari Tahun Dari!');
                return false;
            }

            //  Jalankan Proses
            arr_filter = []; // Reset filter global

            // Panggil fungsi AJAX yang sudah kita rombak tadi
            proses_laba_rugi(this_btn);
        });


        function formatNumber(n) {
            if (n === null || n === undefined) return "";

            let val = parseFloat(n);
            let formatted = Math.abs(val).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            // Jika minus, bungkus dengan kurung
            return val < 0 ? "(" + formatted + ")" : formatted;
        }


        function hideChilds(parentKode) {
            $("#example1 tbody tr").each(function() {
                if ($(this).data("parent") == parentKode) {
                    let childKode = $(this).data("kode");
                    $(this).hide();
                    // recursive hide
                    hideChilds(childKode);
                    // reset icon jadi tutup
                    $(this).find(".toggle").text("▶");
                }
            });
        }

        function showChilds(parentKode) {
            $("#example1 tbody tr").each(function() {
                if ($(this).data("parent") == parentKode) {
                    $(this).show();
                }
            });
        }

        $(document).on("click", ".toggle", function() {

            let kode = $(this).data("kode");
            let isOpen = $(this).text() == "▼";

            if (isOpen) {

                // ======================
                // COLLAPSE (hide semua turunan)
                // ======================

                hideChilds(kode);

                $(this).text("▶");

            } else {

                // ======================
                // EXPAND (show child langsung saja)
                // ======================

                showChilds(kode);

                $(this).text("▼");
            }

        });


        function proses_laba_rugi(this_btn) {
            var tahun_dari = parseInt($('#tahun_dari').val());
            var tahun_sampai = parseInt($('#tahun_sampai').val());
            var check_hidden = $("#hidden_check").is(':checked');

            // Validasi sederhana agar tidak crash jika tahun terbalik
            if (tahun_dari > tahun_sampai) {
                alert("Tahun dari tidak boleh lebih besar dari tahun sampai!");
                return;
            }

            var level = [];
            $("input[name='level[]']:checked").each(function() {
                level.push($(this).val());
            });

            if (level.length === 0) {
                alert_modal_warning("Pilih minimal satu level!");
                return false;
            }

            $("#example1_processing").show();
            this_btn.button('loading');

            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?php echo site_url('report/labarugiyearly/loadData') ?>",
                data: {
                    tahun_dari: tahun_dari,
                    tahun_sampai: tahun_sampai,
                    hidden_check: check_hidden, // Sesuaikan dengan key di controller
                    level: level
                },
                success: function(data) {
                    $("#example1 tbody").remove();
                    let tbody = $("<tbody />");

                    // Simpan filter untuk keperluan Excel (Yearly)
                    arr_filter = [{
                        tahun_dari: tahun_dari,
                        tahun_sampai: tahun_sampai,
                        level: level,
                        checkhidden: check_hidden
                    }];

                    // --- 1. RENDER HEADER TAHUN DINAMIS ---
                    $(".year-header").remove();
                    for (let th = tahun_dari; th <= tahun_sampai; th++) {
                        $("#header-row").append("<th class='year-header text-right style bb' style='width: 120px;'>" + th + "</th>");
                    }

                    // --- 2. HITUNG INDENTASI ---
                    let allLevels = data.record.record.map(item => item.level);
                    let sortedLevels = [...new Set(allLevels)].sort((a, b) => a - b);

                    // --- 3. LOOPING DATA ---
                    $.each(data.record.record, function(key, value) {
                        let levelIndex = sortedLevels.indexOf(value.level);
                        let dynamicIndent = levelIndex * 20;
                        let tr = $("<tr>");

                        // --- STYLING LEVEL (Sesuai Laporan Bulanan Anda) ---
                        if (value.level == 1) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#437333"
                            }); // Hijau
                        } else if (value.level == 2) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#e78d2d"
                            }); // Oranye
                        } else if (value.level == 3) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#2f5fb3"
                            }); // Biru
                        } else if (value.level == 4) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#d42459"
                            }); // Pink
                        }

                        // Styling Baris Total
                        if (value.tipe == "total") {
                            tr.css({
                                "background-color": "#fdfdfd",
                                "border-top": "1px double #ccc",
                                "font-style": "italic"
                            });
                        }

                        // Kolom Standar
                        tr.append($("<td>").text(key + 1));
                        tr.append($("<td>").text(value.kode_acc));
                        tr.append($("<td>").html(
                            "<span style='padding-left:" + dynamicIndent + "px; display:inline-block; white-space:nowrap;'>" +
                            (value.tipe == 'total' ? "<i>" + value.nama_acc + "</i>" : value.nama_acc) +
                            "</span>"
                        ));

                        // --- 4. LOOPING SALDO PER TAHUN ---
                        for (let th = tahun_dari; th <= tahun_sampai; th++) {
                            let saldo = value.yearly[th];
                            let display = (saldo !== null) ? formatNumber(saldo) : "";
                            tr.append($("<td align='right'>").text(display));
                        }

                        tbody.append(tr);

                        // Spacer jika baris total adalah level teratas (Level 1)
                        if (value.tipe == "total" && levelIndex === 0) {
                            let totalCol = (tahun_sampai - tahun_dari) + 4;
                            tbody.append("<tr><td colspan='" + totalCol + "' style='height:30px; border:none;'>&nbsp;</td></tr>");
                        }
                    });

                    // --- 5. BARIS LABA BERSIH (FOOTER) ---
                    let tr_laba = $("<tr style='background:#f4f4f4; font-weight:bold;'>");
                    tr_laba.append($("<td colspan='3' align='center'>").text("LABA / RUGI BERSIH"));

                    for (let th = tahun_dari; th <= tahun_sampai; th++) {
                        let laba_y = data.record.laba_bersih_yearly[th] || 0;
                        tr_laba.append($("<td align='right'>").text(formatNumber(laba_y)));
                    }
                    tbody.append(tr_laba);

                    $("#example1").append(tbody);
                    $("#example1_processing").hide();
                    this_btn.button('reset');
                },
                error: function(xhr) {
                    alert("Terjadi kesalahan saat memuat data.");
                    $("#example1_processing").hide();
                    this_btn.button('reset');
                }
            });
        }

        $('#btn-excel').click(function() {
            if (arr_filter.length == 0) {
                alert('Generate Data terlebih dahulu!');
                return;
            }

            $.ajax({
                type: 'POST',
                url: "<?php echo site_url('report/labarugiyearly/export_excel') ?>",
                data: {
                    arr_filter: arr_filter
                },
                dataType: 'json',
                beforeSend: function() {
                    $('#btn-excel').button('loading');
                },
                error: function() {
                    alert('Error Export Excel');
                    $('#btn-excel').button('reset');
                },
                success: function(data) {
                    if (data.status == "success") {
                        var $a = $("<a>");
                        $a.attr("href", data.file);
                        $a.attr("download", data.filename);
                        $("body").append($a);
                        $a[0].click();
                        $a.remove();
                    } else {
                        alert(data.message);
                    }
                    $('#btn-excel').button('reset');
                }
            });
        });
    </script>

</body>

</html>