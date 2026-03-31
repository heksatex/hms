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

        .month-header {
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
                        <h3 class="box-title"><b>Laba Rugi (Monthly)</b></h3>
                    </div>
                    <div class="box-body">

                        <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <label>Periode</label>
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-control input-sm" name="tahun" id="tahun">
                                                <?php
                                                $thn_skr = date('Y');
                                                for ($x = $thn_skr; $x >= 2020; $x--) {
                                                    echo "<option value='$x'>$x</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control input-sm" name="bulan_dari" id="bulan_dari">
                                                <?php
                                                $list_bulan = get_bulan_indo();
                                                foreach ($list_bulan as $key => $val): ?>
                                                    <option value="<?= $key ?>"><?= $val ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <label>s/d</label>
                                        </div>
                                        <div class="col-md-3">
                                            <select class="form-control input-sm" name="bulan_sampai" id="bulan_sampai">
                                                <?php
                                                for ($i = 1; $i <= 12; $i++) {
                                                    $selected = ($i == date('n')) ? 'selected' : '';

                                                    // Buat format tanggal dummy "01-bulan-tahun" agar bisa di-explode oleh helper tgl_indo
                                                    // Kita gunakan str_pad agar bulan 1 jadi 01, bulan 2 jadi 02, dst.
                                                    $tgl_dummy = "01-" . str_pad($i, 2, "0", STR_PAD_LEFT) . "-" . date('Y');

                                                    // Panggil tgl_indo, lalu ambil bagian tengahnya saja (Nama Bulannya)
                                                    $hasil_tgl = tgl_indo($tgl_dummy);
                                                    $pecah_hasil = explode(' ', $hasil_tgl);
                                                    $nama_bln = $pecah_hasil[1]; // Index 1 adalah nama bulan

                                                    echo "<option value='$i' $selected>$nama_bln</option>";
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
                                                <i class="fa fa-refresh fa-spin"></i> Memproses Data...
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
            var tahun = $('#tahun').val();
            var bulan_dari = parseInt($('#bulan_dari').val());
            var bulan_sampai = parseInt($('#bulan_sampai').val());

            // 1. Validasi: Pastikan Tahun terisi (biasanya dropdown selalu ada isi)
            if (tahun == '') {
                alert_modal_warning('Tahun harus dipilih!');
                return false;
            }

            // 2. Validasi: Bulan Dari tidak boleh lebih besar dari Bulan Sampai
            if (bulan_dari > bulan_sampai) {
                alert_modal_warning('Maaf, Bulan Sampai tidak boleh kurang dari Bulan Dari!');
                return false;
            }

            // 3. Jalankan Proses
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
            var tahun = $('#tahun').val();
            var bulan_dari = parseInt($('#bulan_dari').val());
            var bulan_sampai = parseInt($('#bulan_sampai').val());
            var check_hidden = $("#hidden_check").is(':checked');

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
                url: "<?php echo site_url('report/labarugimonthly/loadData') ?>", // Arahkan ke function baru
                data: {
                    tahun: tahun,
                    bulan_dari: bulan_dari,
                    bulan_sampai: bulan_sampai,
                    checkhidden: check_hidden,
                    level: level
                },
                success: function(data) {
                    $("#example1 tbody").remove();
                    let tbody = $("<tbody />");

                    // Simpan filter untuk keperluan Excel nanti
                    arr_filter = [{
                        tahun: tahun,
                        bulan_dari: bulan_dari,
                        bulan_sampai: bulan_sampai,
                        level: level,
                        checkhidden: check_hidden
                    }];

                    // --- 1. RENDER HEADER BULAN DINAMIS ---
                    $(".month-header").remove();
                    // const namaBulanIndo = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                    const namaBulanIndo = <?php echo json_encode(get_bulan_indo()); ?>;
                    const tahun_pilih = $('#tahun').val();

                    for (let i = bulan_dari; i <= bulan_sampai; i++) {
                        console.log(i)
                        var header_text = namaBulanIndo[i] + " " + tahun_pilih;
                        $("#header-row").append("<th class='month-header text-right style bb ' style='width: 120px;'>" + header_text + "</th>");
                    }

                    // --- 2. HITUNG INDENTASI ---
                    let allLevels = data.record.record.map(item => item.level);
                    let sortedLevels = [...new Set(allLevels)].sort((a, b) => a - b);

                    // --- 3. LOOPING DATA ---
                    $.each(data.record.record, function(key, value) {
                        let levelIndex = sortedLevels.indexOf(value.level);
                        let dynamicIndent = levelIndex * 20;
                        let tr = $("<tr>");

                        // Styling Level (Warna & Tebal)
                        if (value.level == 1) tr.css({
                            "font-weight": "bold",
                            "color": "#437333"
                        });
                        else if (value.level == 2) tr.css({
                            "font-weight": "bold",
                            "color": "#e78d2d"
                        });
                        else if (value.level == 3) tr.css({
                            "font-weight": "bold",
                            "color": "#2f5fb3"
                        });
                        else if (value.level == 4) tr.css({
                            "font-weight": "bold",
                            "color": "#d42459"
                        });

                        if (value.tipe == "total") {
                            tr.css({
                                "background-color": "#fdfdfd",
                                "border-top": "1px double #ccc",
                                "font-style": "italic"
                            });
                        }

                        // Kolom Standar (No, Kode, Nama)
                        tr.append($("<td>").text(key + 1));
                        tr.append($("<td>").text(value.kode_acc));
                        tr.append($("<td>").html(
                            "<span style='padding-left:" + dynamicIndent + "px; display:inline-block; white-space:nowrap;'>" +
                            (value.tipe == 'total' ? "<i>" + value.nama_acc + "</i>" : value.nama_acc) +
                            "</span>"
                        ));

                        // --- 4. LOOPING SALDO PER BULAN ---
                        for (let m = bulan_dari; m <= bulan_sampai; m++) {
                            let saldo = value.monthly[m];
                            let display = (saldo !== null) ? formatNumber(saldo) : "";
                            tr.append($("<td align='right'>").text(display));
                        }

                        tbody.append(tr);

                        // Spacer Jarak Dinamis
                        if (value.tipe == "total" && levelIndex === 0) {
                            let totalCol = (bulan_sampai - bulan_dari) + 4;
                            tbody.append("<tr><td colspan='" + totalCol + "' style='height:30px; border:none;'>&nbsp;</td></tr>");
                        }
                    });

                    // --- 5. BARIS LABA BERSIH (FOOTER) ---
                    let tr_laba = $("<tr style='background:#f4f4f4; font-weight:bold;'>");
                    tr_laba.append($("<td colspan='3' align='center'>").text("LABA / RUGI BERSIH"));

                    for (let m = bulan_dari; m <= bulan_sampai; m++) {
                        let laba_m = data.record.laba_bersih_monthly[m] || 0;
                        tr_laba.append($("<td align='right'>").text(formatNumber(laba_m)));
                    }
                    tbody.append(tr_laba);

                    $("#example1").append(tbody);
                    $("#example1_processing").hide();
                    this_btn.button('reset');
                },
                error: function(jqXHR) {
                    console.log(jqXHR.responseText);
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
                url: "<?php echo site_url('report/labarugimonthly/export_excel') ?>",
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