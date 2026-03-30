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
                        <h3 class="box-title"><b>Laba Rugi (Standar)</b></h3>
                    </div>
                    <div class="box-body">

                        <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <label>Periode </label>
                                        </div>
                                        <div class="col-md-4">
                                            <div class='input-group'>
                                                <input type="text" class="form-control input-sm" name="tgldari" id="tgldari" required="">
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <label>s/d</label>
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
                            <div class="col-md-3">
                                <button type="button" class="btn btn-sm btn-default btn-block" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                    <i class="fa fa-refresh"></i> Generate 
                                </button>
                                <button type="button" class="btn btn-sm btn-default btn-block" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">
                                    <i class="fa fa-file-excel-o" style="color:green"></i> Export Excel
                                </button>
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
                                                        <th class='style bb' style="min-width: 120px">Saldo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="13">Tidak ada Data</td>
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
                return new Date(now.getFullYear(), now.getMonth() + 1, 0);
            })(),
            format: 'D-MMMM-YYYY',
            ignoreReadonly: true
        });


        var arr_filter = [];


        // btn generate
        $("#btn-generate").on('click', function() {

            var tgldari = $('#tgldari').val();
            var tglsampai = $('#tglsampai').val();
            var this_btn = $(this);

            var tgldari_2 = $('#tgldari').data("DateTimePicker").date();
            var tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();

            if (tgldari == '' || tglsampai == '') {
                alert_modal_warning('Periode Tanggal Harus diisi !');

            } else if (tglsampai_2 < tgldari_2) {
                alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
            } else {
                arr_filter = [];
                proses_laba_rugi(this_btn);

            }
        });


        function formatNumber(n) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(n);
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

            var tgldari = $('#tgldari').val();
            var tglsampai = $('#tglsampai').val();
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
                url: "<?php echo site_url('report/labarugistandar/loadData') ?>",
                data: {
                    tgldari: tgldari,
                    tglsampai: tglsampai,
                    checkhidden: check_hidden,
                    level: level
                },
                success: function(data) {
                    $("#example1 tbody").remove();
                    let tbody = $("<tbody />");

                    arr_filter = [{
                        tgldari: tgldari,
                        tglsampai: tglsampai,
                        level: level,
                        checkhidden: check_hidden
                    }];

                    // --- 1. CARI LEVEL TERKECIL YANG ADA DI DATA ---
                    // Ini agar jika user pilih level 2-5, maka level 2 jadi patokan spasi
                    let listLevel = data.record.record.map(item => item.level);
                    // let minLevel = Math.min(...listLevel);
                    let allLevels = data.record.record.map(item => item.level);
                    let sortedLevels = [...new Set(allLevels)].sort((a, b) => a - b);

                    $.each(data.record.record, function(key, value) {
                        // let indent = (value.level - 1) * 20;
                        let levelIndex = sortedLevels.indexOf(value.level);
                        let dynamicIndent = levelIndex * 20;
                        let tr = $("<tr>");

                        // 2. Styling Berdasarkan Level
                        if (value.level == 1) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#437333"
                            });
                        } else if (value.level == 2) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#e78d2d"
                            });
                        } else if (value.level == 3) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#2f5fb3"
                            });
                        } else if (value.level == 4) {
                            tr.css({
                                "font-weight": "bold",
                                "color": "#d42459"
                            });
                        }

                        // 3. Styling Khusus Total & Indentasi Nama
                        let nama_display = value.nama_acc;
                        if (value.tipe == "total") {
                            nama_display = "<i>" + value.nama_acc + "</i>";
                            tr.css({
                                "background-color": "#fdfdfd",
                                "border-top": "1px double #ccc",
                                "font-style": "italic"
                            });
                        }

                        let saldo_display = (value.saldo !== null) ? formatNumber(value.saldo) : "";
                        

                        tr.append(
                            $("<td>").text(key + 1),
                            $("<td style='width:50px;'>").text(value.kode_acc),
                            $("<td style='width: 200px'>").html("<span style='padding-left:" + dynamicIndent + "px'>" + nama_display + "</span>"),
                            $("<td style='width: 100px; min-width: 100px;' align='right'>").text(saldo_display)
                        );

                        tbody.append(tr);

                        // --- 4. INSERT JARAK DINAMIS ---
                        // Spasi hanya muncul jika ini Total dari level paling atas yang dipilih (index 0)
                        if (value.tipe == "total" && levelIndex === 0) {
                            let spacer = $("<tr>").append(
                                $("<td colspan='4' style='height: 30px; border:none;'>").html("&nbsp;")
                            );
                            tbody.append(spacer);
                        }
                    });

                    // 5. Baris Laba Bersih
                    let tr_laba = $("<tr style='background:#f4f4f4; font-weight:bold;'>").append(
                        $("<td colspan='3' align='center'>").text("LABA / RUGI BERSIH"),
                        $("<td align='right'>").text(formatNumber(data.record.laba_bersih))
                    );
                    tbody.append(tr_laba);

                    $("#example1").append(tbody);
                    $("#example1_processing").hide();
                    this_btn.button('reset');
                },
                error: function(jqXHR) {

                    // alert(jqXHR.responseText);
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
                url: "<?php echo site_url('report/labarugistandar/export_excel') ?>",
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