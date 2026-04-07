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
                        <h3 class="box-title"><b>Neraca (Standar)</b></h3>
                    </div>
                    <div class="box-body">

                        <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <label>Per Tgl </label>
                                        </div>
                                        <div class="col-md-4">
                                            <div class='input-group'>
                                                <input type="text" class="form-control input-sm" name="tglsampai" id="tglsampai" required="">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <label>Opsi</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label>
                                                <input type="checkbox" name="hidden_check" id="hidden_check" checked>
                                            </label>
                                            Sembuyikan Data Kosong
                                        </div>
                                        <div class="col-md-2">
                                            <label> Show Level </label>
                                        </div>
                                        <div class="col-md-4">
                                            <label><input type="checkbox" class="level-1" value="1" name="level[]"> 1</label>
                                            <label><input type="checkbox" class="level-2" value="2" name="level[]"> 2</label>
                                            <label><input type="checkbox" class="level-3" value="3" name="level[]"> 3</label>
                                            <label><input type="checkbox" class="level-4" value="4" name="level[]" checked> 4</label>
                                            <label><input type="checkbox" class="level-5" value="5" name="level[]" checked> 5</label>
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
                                            <table id="example1" class="table table-condesed table-hover" border="1">
                                                <thead>
                                                    <tr>
                                                        <th class="style bb no">No. </th>
                                                        <th class='style bb' style="max-width: 50px">Kode CoA</th>
                                                        <th class='style bb' style="min-width: 200px">Nama Acc</th>
                                                        <th class='style bb' style="min-width: 120px">Nilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="8">Tidak ada Data</td>
                                                    </tr>
                                                </tbody>
                                            </table>

                                            <div id="example1_processing" class="table_processing" style="display: none; z-index:5;">
                                                Processing...
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
                return now;
            })(),
            format: 'D-MMMM-YYYY',
            ignoreReadonly: true
        });


        var arr_filter = [];


        // btn generate
        $("#btn-generate").on('click', function() {

            var tglsampai = $('#tglsampai').val();
            var this_btn = $(this);
            var tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();

            if (tglsampai == '') {
                alert_modal_warning('Tanggal Harus diisi !');
                return;
            } else {
                arr_filter = [];
                proses_neraca(this_btn);

            }
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


        function proses_neraca(this_btn) {

            var tglsampai = $('#tglsampai').val();
            // var tglsampai = $('#tglsampai').val();
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
                url: "<?php echo site_url('report/neracastandar/loadData') ?>",
                data: {
                    tglsampai: tglsampai,
                    checkhidden: check_hidden,
                    level: level
                },
                success: function(data) {
                    $("#example1 tbody").remove();
                    let tbody = $("<tbody />");

                    arr_filter = [{
                        tglsampai: tglsampai,
                        level: level,
                        checkhidden: check_hidden
                    }];

                    // 1. Ambil list level unik dan urutkan untuk indentasi dinamis
                    let allLevels = data.record.record.map(item => item.level);
                    let sortedLevels = [...new Set(allLevels)].sort((a, b) => a - b);

                    $.each(data.record.record, function(key, value) {
                        let levelIndex = sortedLevels.indexOf(value.level);
                        let dynamicIndent = levelIndex * 20;
                        let tr = $("<tr class='coa-row'>");

                        // --- 2. JAGA WARNA ASLI ANDA ---
                        if (value.level == 1) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#437333" // Warna Hijau asli Anda
                            }).addClass("bold");
                        } else if (value.level == 2) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#e78d2d" // Warna Oranye asli Anda
                            }).addClass("bold");
                        } else if (value.level == 3) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#2f5fb3" // Warna Biru asli Anda
                            }).addClass("bold");
                        } else if (value.level == 4) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#d42459" // Warna Merah asli Anda
                            }).addClass("bold");
                        }

                        // --- 3. JAGA STYLING TOTAL ASLI ANDA ---
                        let nama_display = value.nama_acc;
                        if (value.tipe == "total") {
                            nama_display = "<i>" + value.nama_acc + "</i>";
                            tr.css({
                                // "background-color": "#fdfdfd", // Background asli Anda
                                "border-top": "1px double #ccc", // Border asli Anda
                                "font-style": "italic"
                            });
                        }

                        // Saldo sekarang tampil di level terakhir karena perubahan backend
                        let saldo_display = (value.saldo !== null) ? formatNumber(value.saldo) : "";

                        tr.append(
                            $("<td>").text(key + 1),
                            $("<td style='width:50px;'>").text(value.kode_acc),
                            $("<td style='width: 300px'>").html("<span style='padding-left:" + dynamicIndent + "px'>" + nama_display + "</span>"),
                            $("<td style='width: 150px;' align='right'>").text(saldo_display)
                        );

                        tbody.append(tr);

                        // --- 4. INSERT JARAK DINAMIS SESUAI LOGIKA BARU ---
                        // Jarak (spacer) hanya muncul jika ini Total dari level paling atas yang dipilih (index 0)
                        // Dan hanya muncul jika itu baris TOTAL, bukan akun detail.
                        if (value.tipe == "total" && levelIndex === 0) {
                            let spacer = $("<tr>").append(
                                $("<td colspan='4' style='height: 30px; border:none;'>").html("&nbsp;")
                            );
                            tbody.append(spacer);
                        }
                    });

                    // --- 5. TAMPILAN TOTAL AKHIR NERACA (BALANCE CHECK) ---

                    // Baris Total 1. ASSET
                    tbody.append($("<tr style='font-weight:bold; background:#f4f4f4;'>").append(
                        $("<td colspan='3' align='right'>").text("TOTAL ASSET"),
                        $("<td align='right'>").text(formatNumber(data.record.total_aset))
                    ));

                    // Baris Total 2 & 3. KEWAJIBAN & MODAL (PASIVA)
                    // Di backend, total_pasiva sudah menjumlahkan kepala 2 dan 3
                    tbody.append($("<tr style='font-weight:bold; background:#f4f4f4;'>").append(
                        $("<td colspan='3' align='right'>").text("TOTAL KEWAJIBAN & MODAL"),
                        $("<td align='right'>").text(formatNumber(data.record.total_pasiva))
                    ));

                    // --- 6. LOGIKA AUDIT BALANCE ---
                    let totalAset = parseFloat(data.record.total_aset);
                    let totalPasiva = parseFloat(data.record.total_pasiva);
                    let selisih = totalAset - totalPasiva;

                    if (Math.abs(selisih) > 0.1) {
                        // Jika tidak balance
                        tbody.append($("<tr style='font-weight:bold; background:#f4f4f4;'>").append(
                            $("<td colspan='3' align='right'>").text("SELISIH"),
                            $("<td align='right'>").text(formatNumber(selisih))
                        ));
                    } else {
                        // Jika balance
                        tbody.append($("<tr style='font-weight:bold; background:#f4f4f4;'>").append(
                            $("<td colspan='4' align='center'>").html("<i class='fa fa-check-circle'></i> NERACA SEIMBANG (BALANCE)")
                        ));
                    }

                    $("#example1").append(tbody);
                    $("#example1_processing").hide();
                    this_btn.button('reset');
                },
                error: function(jqXHR) {

                    alert("Error Generate Data !");
                    console.log(jqXHR.responseText);

                    $("#example1_processing").hide();
                    this_btn.button('reset');

                }
            });

        }

        $('#btn-excel').click(function() {
            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu!');
                return;
            }

            $.ajax({
                type: 'POST',
                url: "<?php echo site_url('report/neracastandar/export_excel') ?>",
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