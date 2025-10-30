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
            max-height: calc(100vh - 250px);
            overflow-x: auto;
        }

        table tbody tr td {
            padding: 0px 5px 0px 5px !important;
        }

        .ket-acc1 {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
            /* Sesuaikan dengan kebutuhan */
        }

        .resizable {
            position: relative;
        }

        .resizable .resizer {
            position: absolute;
            top: 0;
            right: 0;
            width: 5px;
            cursor: col-resize;
            user-select: none;
            height: 100%;
        }

        table th,
        table td {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ket-acc {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .btn.active {
            background-color: #646465ff;
            border-color: #7e7e7eff;
            color: white
        }

        .btn.disabled {
            pointer-events: none;
        }

        .style_space {
            white-space: nowrap !important;
            /* font-weight: 700; */
            background: #F0F0F0;
            border-top: 2px solid #ddd !important;
            border-bottom: 2px solid #ddd !important;
        }

        .resizable .resizer:hover {
            background-color: rgba(0, 0, 0, 0.1);
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
                        <h3 class="box-title"><b>Buku Besar Detail</b></h3>
                    </div>
                    <div class="box-body">

                        <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-12">
                                            <p><strong><?php echo $coa->kode_coa . ' - ' . $coa->nama; ?></strong></p>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-6">
                                            <p style="margin-bottom:1px;"><strong>Periode : </p>
                                            <p> <?php echo $tgl_dari . ' - ' . $tgl_sampai; ?></strong> </p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Saldo Normal : <?php echo $coa->saldo_normal; ?></strong></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <div class="col-md-12">&nbsp</div>
                                    <div class="col-md-12">
                                        <button type="button" class="btn btn-default pilih-btn active disabled" name="btn-normal" id="btn-normal" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..." tabindex="-1" aria-disabled="true"> Normal</button>
                                        <button type="button" class="btn btn-default pilih-btn" name="btn-debit" id="btn-debit" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Lawan Debit</button>
                                        <button type="button" class="btn btn-default pilih-btn" name="btn-credit" id="btn-credit" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Lawan Credit</button>
                                        <button type="button" class="btn btn-default" name="btn-generate" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>

                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- table -->
                        <div class="box-body">
                            <div class="col-sm-12 table-responsive">
                                <div class="table_scroll">
                                    <div class="table_scroll_head">
                                        <div class="divListviewHead">
                                            <table id="example1" class="table table-condesed table-hover" border="0">
                                                <thead>
                                                    <tr>
                                                        <th class="style bb no resizable">No. </th>
                                                        <th class='style bb resizable' style="min-width: 80px; width:80px;">Tanggal</th>
                                                        <th class='style bb resizable' style="min-width: 105px; width:105px;">Kode Entries</th>
                                                        <th class='style bb resizable' style="min-width: 100px; max-width: 220px; width:100px;">Origin</th>
                                                        <th class='style bb resizable' style="min-width: 200px">Keterangan</th>
                                                        <th class='style bb resizable' style="min-width: 150px; width:100px;" id="posisi">Debit</th>
                                                        <th class='style bb resizable' style="min-width: 150px; width:100px;" id="lawan">Credit</th>
                                                        <th class='style bb resizable' style="min-width: 150px; width:100px;">Saldo</th>
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

    <!-- <?php $this->load->view("admin/_partials/js.php"); ?> -->

    <div id="load_modal">
        <!-- Load Partial Modal -->
        <?php $this->load->view("admin/_partials/modal.php") ?>
    </div>

    <script type="text/javascript">
        document.querySelectorAll("th.resizable").forEach(function(th) {
            const resizer = document.createElement("div");
            resizer.classList.add("resizer");
            th.appendChild(resizer);

            let startX, startWidth;
            resizer.addEventListener("mousedown", function(e) {
                startX = e.pageX;
                startWidth = th.offsetWidth;

                const index = Array.from(th.parentNode.children).indexOf(th);

                function onMouseMove(e) {
                    const newWidth = startWidth + (e.pageX - startX);
                    th.style.width = newWidth + "px";

                    // resize all corresponding <td> in this column
                    document.querySelectorAll(`#example1 tbody tr`).forEach(row => {
                        if (row.children[index]) {
                            row.children[index].style.width = newWidth + "px";
                        }
                    });
                }

                function onMouseUp() {
                    document.removeEventListener("mousemove", onMouseMove);
                    document.removeEventListener("mouseup", onMouseUp);
                }

                document.addEventListener("mousemove", onMouseMove);
                document.addEventListener("mouseup", onMouseUp);
            });
        });

        const buttons = document.querySelectorAll('.pilih-btn');

        buttons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Reset semua tombol: hapus class 'active', 'disabled dan attribute 'disabled'
                buttons.forEach(b => {
                    b.classList.remove('active');
                    b.classList.remove('disabled');
                    b.removeAttribute('disabled');
                });

                // Aktifkan tombol yang diklik
                this.classList.add('active');
                this.classList.add('disabled');
                this.setAttribute('disabled', true);
            });
        });


        var arr_filter = [];


        // btn lawan debit
        $("#btn-debit").on('click', function() {
            var this_btn = $(this);
            const kolomPosisi = document.getElementById('posisi');
            const kolomLawan = document.getElementById('lawan');
            kolomPosisi.textContent = 'Debit';
            kolomLawan.textContent = 'Credit (Lawan)';
            process_bukubesar_detail('D', this_btn);
        });

        // btn lawan credit
        $("#btn-credit").on('click', function() {
            var this_btn = $(this);
            const kolomPosisi = document.getElementById('posisi');
            const kolomLawan = document.getElementById('lawan');
            kolomPosisi.textContent = 'Credit';
            kolomLawan.textContent = 'Debit (Lawan)';
            process_bukubesar_detail('C', this_btn);
        });

        // btn lawan credit
        $("#btn-normal").on('click', function() {
            var this_btn = $(this);
            const kolomPosisi = document.getElementById('posisi');
            const kolomLawan = document.getElementById('lawan');
            kolomPosisi.textContent = 'Debit';
            kolomLawan.textContent = 'Credit';
            process_bukubesar_detail('N', this_btn);
        });


        function formatNumber(n) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(n);
        }

        process_bukubesar_detail('N', '');

        function process_bukubesar_detail(view, btn) {
            arr_filter = [];
            var tgldari = "<?php echo $tgl_dari; ?>";
            var tglsampai = "<?php echo $tgl_sampai; ?>";
            var coa = "<?php echo $coa->kode_coa; ?>";
            var checkhidden = "<?php echo $checkhidden; ?>";

            $("#example1_processing").css('display', ''); // show loading
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?php echo site_url('report/bukubesar/loadDataDetail') ?>",
                data: {
                    tgldari: tgldari,
                    tglsampai: tglsampai,
                    coa: coa,
                    checkhidden: checkhidden,
                    view: view
                },
                success: function(data) {

                    if (data.status == 'failed') {
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type, function() {});
                            }, 1000);
                        });
                    } else {

                        arr_filter.push({
                            tgldari: tgldari,
                            tglsampai: tglsampai,
                            coa: coa,
                            checkhidden: checkhidden,
                            view: view
                        });

                        if (view == 'N') {

                            $("#example1 tbody").remove();
                            let no = 1;
                            let empty = true;
                            let debit = 0;
                            let credit = 0;
                            let s_awal = 0;
                            let tbody = $("<tbody />");

                            $.each(data.record, function(key, value) {

                                empty = false;
                                // var tr = $("<tr>").append(
                                //     // $("<td>").text(''),
                                //     $("<td colspan=2 class='text-center'>").html('<b>No. ACC: </b> ' + value.kode_acc),
                                //     $("<td class='text-left' colspan=2>").html('<b>Nama ACC : </b>' + value.nama_acc),
                                //     // $("<td class='text-left'>").text(value.nama_acc),
                                //     $("<td align=''>").html('<b>Saldo Normal : </b> ' + value.saldo_normal),
                                //     $("<td colspan='2'>").text(''),
                                // );
                                // tbody.append(tr);

                                var tr2 = $("<tr>").append(
                                    $("<td colspan=4>").text(no),
                                    $("<td>").html('SALDO AWAL'),
                                    $("<td align='right'>").text(0.00),
                                    $("<td align='right'>").text(0.00),
                                    $("<td align='right'>").text(formatNumber(value.saldo_awal.toFixed(2))),
                                );

                                tbody.append(tr2);
                                no = 2
                                acc = '';
                                debit = 0;
                                credit = 0;
                                s_akhir = value.saldo_awal;
                                $.each(value.tmp_data_isi, function(key, value2) {
                                    var tr3 = $("<tr>").append(
                                        $("<td>").html(no++),
                                        $("<td style='max-width:10px'>").text(value2.tanggal),
                                        $("<td style='max-width:20px'>").text(value2.kode_entries),
                                        $("<td style=''>").text(value2.origin),
                                        $("<td class='ket-acc'>").text(value2.keterangan),
                                        $("<td align='right'>").text(formatNumber(value2.debit.toFixed(2))),
                                        $("<td align='right'>").text(formatNumber(value2.credit.toFixed(2))),
                                        $("<td align='right'>").text(formatNumber(value2.saldo_akhir.toFixed(2))),
                                    );
                                    tbody.append(tr3);
                                    debit = debit + value2.debit;
                                    credit = credit + value2.credit;
                                    s_akhir = value2.saldo_akhir;
                                });

                                no = 1;

                                var tr4 = $("<tr>").append(
                                    $("<td colspan='4' class='style_space'>").text(''),
                                    $("<td class='style_space text-right'>").html('<b>Total : ' + value.kode_acc + '</b>'),
                                    $("<td class='style_space text-right'>").html('<b>' + formatNumber(debit.toFixed(2)) + '</b>'),
                                    $("<td class='style_space text-right'>").html('<b>' + formatNumber(credit.toFixed(2)) + '</b>'),
                                    $("<td class='style_space text-right'>").html('<b>' + formatNumber(s_akhir.toFixed(2)) + '</b>'),
                                );
                                tbody.append(tr4);
                            });

                            if (empty == true) {
                                var tr = $("<tr>").append($("<td colspan='8'>").text('Tidak ada Data'));
                                tbody.append(tr);
                            }

                            $("#example1").append(tbody); // append parents
                        } else {
                            create_tbody_lawan(view, data.record);
                        }

                    }
                    $("#example1_processing").css('display', 'none'); // hidden loading

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert(jqXHR.responseText);
                    $("#example1_processing").css('display', 'none'); // hidden loading
                }
            });

        }


        function create_tbody_lawan(view, dataRecord) {

            $("#example1 tbody").remove();
            let no = 1;
            let empty = true;
            let total_debit_or_credit = 0;
            let total_nominal = 0;
            let s_awal = 0;
            let tbody = $("<tbody />");
            let tmp_kode_entries = '';

            $.each(dataRecord, function(key, value) {

                empty = false;

                debit = 0;
                credit = 0;
                tanggal = value.tanggal;
                kode_entries = value.kode_entries;
                origin = value.origin;
                keterangan = value.keterangan;
                debit_or_credit = (value.debit_or_credit === '') ? '' : formatNumber(value.debit_or_credit.toFixed(2));
                lawan = value.lawan;
                nominal = (isNaN(value.nominal)) ? '0' : formatNumber(value.nominal.toFixed(2));
                // nominal = value.nominal;

                if (tmp_kode_entries != kode_entries && no != 1) {
                    var tr = $("<tr>").append($("<td colspan='8' class='style_space'>").html('&nbsp'));
                    tbody.append(tr);
                    no = 1;
                }

                if (tmp_kode_entries != '') {
                    tmp_kode_entries = kode_entries;
                }

                var tr = $("<tr>").append(
                    $("<td>").html(no++),
                    $("<td style='max-width:10px'>").text(tanggal),
                    $("<td style='max-width:20px'>").text(kode_entries),
                    $("<td align=''>").text(origin),
                    $("<td class='ket-acc'>").text(keterangan),
                    $("<td align='right'>").text((debit_or_credit)),
                    $("<td align='left'>").text(lawan),
                    $("<td align='right'>").text((nominal)),
                );
                tbody.append(tr);


                total_debit_or_credit = total_debit_or_credit + ((value.debit_or_credit === '') ? 0 : value.debit_or_credit);
                total_nominal = total_nominal + value.nominal;
                // s_akhir = value2.saldo_akhir;
                // console.log(no);

            });



            if (empty == true) {
                var tr = $("<tr>").append($("<td colspan='8'>").text('Tidak ada Data'));
                tbody.append(tr);
            } else {
                var tr = $("<tr>").append($("<td class='style_space' colspan='8'>").html("&nbsp"));
                tbody.append(tr);
                var tr4 = $("<tr>").append(
                    $("<td colspan='5' class=''>").text(''),
                    $("<td class=' text-right'>").html('<b>' + formatNumber(total_debit_or_credit.toFixed(2)) + '</b>'),
                    $("<td class=' text-right'>").html(''),
                    $("<td class=' text-right'>").html('<b>' + formatNumber(total_nominal.toFixed(2)) + '</b>'),
                );
                tbody.append(tr4);
            }
            $("#example1").append(tbody); // append parents


        }

        // klik btn excel
        $('#btn-excel').click(function() {

            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu !');
            } else {
                $.ajax({
                    "type": 'POST',
                    "url": "<?php echo site_url('report/bukubesar/export_excel_detail') ?>",
                    "data": {
                        arr_filter: arr_filter
                    },
                    "dataType": 'json',
                    beforeSend: function() {
                        $('#btn-excel').button('loading');
                    },
                    error: function() {
                        alert('Error Export Excel');
                        $('#btn-excel').button('reset');
                    }
                }).done(function(data) {
                    if (data.status == "failed") {
                        alert_notify(data.icon, data.message, data.type, function() {});
                    } else {
                        var $a = $("<a>");
                        $a.attr("href", data.file);
                        $("body").append($a);
                        $a.attr("download", data.filename);
                        $a[0].click();
                        $a.remove();
                    }
                    $('#btn-excel').button('reset');
                });
            }
        });
    </script>

</body>

</html>