<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
    <style>
        .bb {
            border-bottom: 2px solid #ddd !important;
        }

        button[id="btn-edit"],
        button[id="btn-confirm"],
        button[id="btn-duplicate"],
        button[id="btn-cancel"] {
            /*untuk hidden button simpan/cancel di top bar MO*/
            display: none;
        }


        #table-resume th:nth-child(1),
        #table-resume th:nth-child(8) {
            width: 5%;
        }

        #table-resume th:nth-child(2),
        #table-resume th:nth-child(3),
        #table-resume th:nth-child(4),
        #table-resume th:nth-child(5),
        #table-resume th:nth-child(6) {
            width: 8%;
        }

        #table-resume th:nth-child(7) {
            width: 10%;
        }


        #table-resume th {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 1366px) {
            #table-resume th {
                font-size: 11px;
                padding: 4px;
            }
        }


        #table-resume td {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .style_total {
            white-space: nowrap !important;
            background: #F0F0F0;
            border-top: 2px solid #ddd !important;
            border-bottom: 1px solid #ddd !important;
            font-weight: bold;

        }

        .style_total td {
            padding: 0px 5px 0px 5px !important;
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
                <?php $this->load->view("admin/_partials/statusbar.php") ?>
            </section>
            <section class="content">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Form Tambah (Duplicate)</h3>
                    </div>
                    <div class="box-body">
                        <form class="form-horizontal" name="form-acc-periode" id="form-acc-periode">
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>No Pelunasan</label></div>
                                        <div class="col-xs-8">
                                            <input type="text" class="form-control input-sm" name="kode" id="kode" readonly="readonly" />
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Tanggal Transaksi</label></div>
                                        <div class="col-xs-8 col-md-8">
                                            <div class='input-group date' id='tgl_transaksi'>
                                                <input type='text' class="form-control input-sm" name="tanggal_transaksi" id="tanggal_transaksi" value="<?php echo $list->tanggal_transaksi; ?>" />
                                                <span class="input-group-addon">
                                                    <span class="fa fa-calendar" disabled="true"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Customer</label></div>
                                        <div class="col-xs-8">
                                            <input type="hidden" class="form-control input-sm" name="partner" id="partner" value="<?php echo $list->partner_id; ?>" readonly>
                                            <input type="text" class="form-control input-sm" name="nama_partner" id="nama_partner" value="<?php echo $list->partner_nama; ?>" readonly>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>


                        <div class="row">
                            <div class="col-md-12">
                                <!-- Custom Tabs -->
                                <div class="">
                                    <ul class="nav nav-tabs ">
                                        <li class="active"><a href="#tab_1" data-toggle="tab">Detail</a></li>
                                        <li class=""><a href="#tab_2" data-toggle="tab">Jurnal</a></li>
                                    </ul>
                                    <div class="tab-content over"><br>
                                        <div class="tab-pane active" id="tab_1">
                                            <!-- Tabel  -->
                                            <div class="col-md-12 table-responsive over">
                                                <div class="row" style="margin-bottom:5px;">
                                                    <div class="col-md-12">
                                                        <div class="col-md-4 col-lg-3">
                                                            <label>Faktur Akan dilunasi</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button class="btn btn-default btn-sm" id="btn-inv" name="btn-inv"><i class='fa fa-file-text' style='color: orange'></i> Faktur (<span id='tinv'>0</span>)</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-condesed table-hover rlstable over" width="100%" id="table_invoice" border="1">
                                                    <thead>
                                                        <tr>
                                                            <th class="style bb no">No.</th>
                                                            <th class="style bb">No Faktur</th>
                                                            <th class="style bb">No SJ</th>
                                                            <th class="style bb">Tanggal</th>
                                                            <th class="style bb">Curr</th>
                                                            <th class="style bb text-right">Kurs</th>
                                                            <th class="style bb text-right">Total Utang (Rp)</th>
                                                            <th class="style bb text-right">Total Utang (Valas)</th>
                                                            <th class="style bb text-right">Sisa Utang (Rp)</th>
                                                            <th class="style bb text-right">Sisa Utang (Valas)</th>
                                                            <th class="style bb text-right">Pelunasan (Rp)</th>
                                                            <th class="style bb text-right">Pelunasan (Valas)</th>
                                                            <th class="style bb">#</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan='13'>Tidak Ada Data</td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>
                                                    </tfoot>
                                                </table>
                                                <div id="example1_processing" class="table_processing" style="display: none; z-index:5;">
                                                    Processing...
                                                </div>
                                            </div>

                                            <!-- Tabel  -->
                                            <div class="col-md-12 table-responsive over">
                                                <div class="row" style="margin-bottom:5px;">
                                                    <div class="col-md-12">
                                                        <div class="col-md-2 col-lg-3">
                                                            <label>Pelunasan</label>
                                                        </div>
                                                        <div class="col-md-10 col-lg-9">
                                                            <button class="btn btn-default btn-sm" id="btn-kas-bank" name="btn-kas-bank"><i class='fa fa-bank' style='color: green'></i> Kas Bank (<span id='tbk'>0</span>)</button>
                                                            <button class="btn btn-default btn-sm" id="btn-uang-muka" name="btn-uang-muka"><i class='fa fa-money' style='color: blue'></i> Uang Muka (<span id='tum'>0</span>)</button>
                                                            <button class="btn btn-default btn-sm" id="btn-deposit" name="btn-deposit"><i class='fa fa-money' style='color: blue'></i> Deposit (<span id='tdep'>0</span>)</button>
                                                            <button class="btn btn-default btn-sm" id="btn-retur" name="btn-retur"><i class='fa fa-exchange' style='color: red'></i> Retur (<span id='tret'>0</span>)</button>
                                                            <button class="btn btn-default btn-sm" id="btn-koreksi-valas" name="btn-koreksi-valas"><i class='fa fa-exchange' style='color: purple'></i> Koreksi Kurs Bulan </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-condesed table-hover rlstable over" width="100%" id="table_pelunasan" border="1">
                                                    <thead>
                                                        <tr>
                                                            <th class="style bb no">No.</th>
                                                            <th class="style bb">Metode</th>
                                                            <th class="style bb nowrap">No Bukti</th>
                                                            <th class="style bb">Tanggal</th>
                                                            <th class="style bb">Uraian</th>
                                                            <th class="style bb">Curr</th>
                                                            <th class="style bb text-right">Kurs</th>
                                                            <th class="style bb text-right">Total (Rp)</th>
                                                            <th class="style bb text-right">Total (Valas)</th>
                                                            <th class="style bb no">#</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan='9'>Tidak Ada Data</td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot>

                                                    </tfoot>
                                                </table>
                                                <div id="example2_processing" class="table_processing" style="display: none; z-index:5;">
                                                    Processing...
                                                </div>
                                            </div>
                                            <!-- Tabel  -->

                                            <!-- Tabel  -->
                                            <div class="col-md-12 table-responsive over">
                                                <div class="row" style="margin-bottom:5px;">
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <label>Summary</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-condesed table-hover rlstable over" width="100%" id="table-resume" border="1">
                                                    <thead>
                                                        <tr>
                                                            <th class="style bb no"></th>
                                                            <th class="style bb text-right">Total Hutang</th>
                                                            <th class="style bb text-right">Total Koreksi Kurs</th>
                                                            <th class="style bb text-right">Total Pelunasan</th>
                                                            <th class="style bb">Keterangan</th>
                                                            <th class="style bb text-right">Selisih</th>
                                                            <th class="style bb "></th>
                                                            <th class="style bb ">Koreksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan='7'>Tidak Ada Data</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- Tabel  -->
                                        </div>
                                        <div class="tab-pane" id="tab_2">
                                            <div class="col-md-12"><label>Informasi Jurnal</label></div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label>No Jurnal</label></div>
                                                        <div class="col-xs-8">
                                                            <?php echo ": " ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label>Periode </label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <?php echo ": " ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label>Tanggal </label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <?php echo ": " ?>
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

                    </div>
                </div>
            </section>
        </div>
    </div>
    <?php $this->load->view("admin/_partials/js.php") ?>
    <script>
        $(function() {

            $('#tgl_transaksi').datetimepicker({
                format: 'YYYY-MM-DD',
                ignoreReadonly: true,
                defaultDate: new Date()
            });

            var id = $("#partner").val();
            get_total_by_partner(id);

            function get_total_by_partner(partner) {

                $("#tinv").html('<li class="fa fa-spinner fa-spin"></i>');
                $("#tbk").html('<li class="fa fa-spinner fa-spin"></i>');
                $("#tum").html('<li class="fa fa-spinner fa-spin"></i>');
                $("#tdep").html('<li class="fa fa-spinner fa-spin"></i>');
                $("#tret").html('<li class="fa fa-spinner fa-spin"></i>');

                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "<?php echo base_url('accounting/pelunasanpiutang/get_total_by_partner') ?>",
                    data: {
                        partner: partner
                    },
                    success: function(data) {

                        $("#tinv").html(data.total.total_invoice)
                        $("#tbk").html(data.total.total_kas_bank)
                        $("#tum").html(data.total.total_uang_muka)
                        $("#tret").html(data.total.total_retur)
                        $("#tdep").html(data.total.total_deposit)

                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.responseText);
                    }
                });
            }

            //select 2 Customer
            // $('#partner').select2({
            //     allowClear: true,
            //     placeholder: "Select Customer",
            //     ajax: {
            //         dataType: 'JSON',
            //         type: "POST",
            //         url: "<?php echo base_url(); ?>accounting/pelunasanpiutang/get_list_customer",
            //         data: function(params) {
            //             return {
            //                 name: params.term,
            //             };
            //         },
            //         processResults: function(data) {
            //             var results = [];
            //             $.each(data, function(index, item) {
            //                 results.push({
            //                     id: item.id,
            //                     text: item.nama
            //                 });
            //             });
            //             return {
            //                 results: results
            //             };
            //         },
            //         error: function(xhr, ajaxOptions, thrownError) {
            //             //alert('Error data');
            //             //alert(xhr.responseText);
            //         }
            //     }
            // });

            function getInvoiceData(){

                let invoices = [];
                $("#table_invoice tbody tr").each(function(){
                    let btn = $(this).find(".btn-delete-invoice");
                    if(btn.length == 0) return;
                    invoices.push({
                        id: btn.data("id"),
                        no_faktur: btn.data("inv")
                    });
                });
                return invoices;
            }

            function getPelunasanData(){

                let pelunasan = [];
                $("#table_pelunasan tbody tr").each(function(){
                    let btn = $(this).find(".btn-delete-metode");
                    if(btn.length == 0) return;
                    pelunasan.push({
                        id: btn.data("id"),
                        no_bukti: btn.data("bukti")
                    });
                });
                return pelunasan;
            }

            $("#btn-simpan").unbind("click");
            $('#btn-simpan').click(function() {

                var kode = $('#kode').val();
                var partner = $('#partner').val();
                let invoice = getInvoiceData();
                let pelunasan = getPelunasanData();

                if (partner == '' || partner == null) {
                    alert_notify('fa fa-warning', 'Customer Harus diisi !', 'danger', function() {});
                } else {

                    $('#btn-simpan').button('loading');
                    please_wait(function() {});
                    var baseUrl = '<?php echo base_url(); ?>';
                    $.ajax({
                        type: "POST",
                        dataType: "json",
                        url: '<?php echo base_url('accounting/pelunasanpiutang/simpan') ?>',
                        data: {
                            kode: '',
                            partner: $('#partner').val(),
                            tgl_transaksi: $("#tanggal_transaksi").val(),
                            invoice: invoice,
                            pelunasan: pelunasan,
                            duplicate:'true',
                            no_pelunasan_sblm : `<?php echo $list->no_pelunasan; ?>`
                        },
                        success: function(data) {
                            if (data.sesi == "habis") {
                                //alert jika session habis
                                alert_modal_warning(data.message);
                                window.location.href = baseUrl; //replace ke halaman login
                            } else if (data.status == "failed") {
                                //jika ada form belum keisi
                                unblockUI(function() {
                                    setTimeout(function() {
                                        alert_notify(data.icon, data.message, data.type, function() {});
                                    }, 1000);
                                });
                                $('#btn-simpan').button('reset');
                            } else if (data.status == "ada") {
                                alert_modal_warning(data.message);
                                unblockUI(function() {});
                                $('#btn-simpan').button('reset');
                            } else {
                                //jika berhasil disimpan
                                // $('#kode').val(data.isi);
                                unblockUI(function() {
                                    setTimeout(function() {
                                        alert_notify(data.icon, data.message, data.type, function() {

                                            window.location.replace('edit/' + data.isi);
                                        }, 1000);
                                    });
                                });

                                $('#btn-simpan').button('reset');
                            }

                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(xhr.responseText);
                            unblockUI(function() {});
                            $('#btn-simpan').button('reset');
                        }
                    });

                }


            });

            function formatNumber(n) {
                return new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(n);
            }

            loadInvoice();
            loadPelunasan();

            function loadInvoice() {

                $("#example1_processing").css('display', ''); // show loading
                var id = "<?php echo $list->id; ?>";
                var no_pelunasan = "<?php echo $list->no_pelunasan; ?>";
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "<?php echo site_url('accounting/pelunasanpiutang/loadData') ?>",
                    data: {
                        id: id,
                        no_pelunasan: no_pelunasan,
                        load: 'invoice'
                    },
                    success: function(data) {


                        $("#table_invoice tbody").remove();
                        let no = 1;
                        let empty = true;
                        let tbody = $("<tbody />");
                        let tfoot = $("<tfoot />");
                        let sum_pelunasan_rp = 0.00;
                        let sum_pelunasan_valas = 0.00;
                        let sum_total_piutang_rp = 0.00;
                        let sum_total_piutang_valas = 0.00;
                        let sum_sisa_piutang_rp = 0.00;
                        let sum_sisa_piutang_valas = 0.00;
                        let status = data.head.status;
                        $.each(data.record, function(key, value) {

                            empty = false;

                            btn = '<button class="btn btn-danger btn-xs btn-delete-invoice" name="btn-delete-invoice" data-toggle="tooltip" title="Hapus" data-id="' + value.id + '" data-inv="' + value.no_faktur + '"><i class="fa fa-trash"></i></button>';

                            // let statusHtml = $("<span>").addClass(value.status_color).text(value.status_text);
                            // let statusLebih = $("<span>").addClass(value.status_color_lebih).text(value.status_text_lebih);

                            var tr = $("<tr>").append(
                                $("<td style=''>").text(no),
                                $("<td style=''>").text(value.no_faktur),
                                // $("<td style=''>").html('<a href="#" class="detail-sj" data-sj="' + value.no_sj + '">' + value.no_sj + '</a>'),
                                $("<td style=''>").text(value.no_sj),
                                $("<td style=''>").text(value.tanggal_faktur),
                                $("<td style=''>").text(value.currency),
                                $("<td class='text-right'>").text(value.kurs),
                                $("<td class='text-right'>").text(formatNumber(value.total_piutang_rp)),
                                $("<td class='text-right'>").text(formatNumber(value.total_piutang_valas)),
                                $("<td class='text-right'>").text(formatNumber(value.sisa_piutang_rp)),
                                $("<td class='text-right'>").text(formatNumber(value.sisa_piutang_valas)),
                                $("<td class='text-right'>").text(formatNumber(value.pelunasan_rp)),
                                $("<td class='text-right'>").text(formatNumber(value.pelunasan_valas)),
                                // $("<td class='text-center'>").append(statusLebih),
                                $("<td class=''>").html(btn),
                            );

                            sum_pelunasan_rp = sum_pelunasan_rp + parseFloat(value.pelunasan_rp);
                            sum_pelunasan_valas = sum_pelunasan_valas + parseFloat(value.pelunasan_valas);

                            sum_total_piutang_rp = sum_total_piutang_rp + parseFloat(value.total_piutang_rp);
                            sum_total_piutang_valas = sum_total_piutang_valas + parseFloat(value.total_piutang_valas);
                            sum_sisa_piutang_rp = sum_sisa_piutang_rp + parseFloat(value.sisa_piutang_rp);
                            sum_sisa_piutang_valas = sum_sisa_piutang_valas + parseFloat(value.sisa_piutang_valas);
                            tbody.append(tr);
                            no++;
                        });

                        if (empty == true) {
                            var tr = $("<tr>").append($("<td colspan='15'>").text('Tidak ada Data'));
                            tbody.append(tr);
                        } else {
                            var trfoot = $("<tr class='style_total'>").append(
                                $("<td colspan='6' class='text-right'>").text('Total'),
                                $("<td class='text-right'>").text(formatNumber(sum_total_piutang_rp)),
                                $("<td class='text-right'>").text(formatNumber(sum_total_piutang_valas)),
                                $("<td class='text-right warna-piutang'>").text(formatNumber(sum_sisa_piutang_rp)),
                                $("<td class='text-right warna-piutang'>").text(formatNumber(sum_sisa_piutang_valas)),
                                $("<td class='text-right'>").text(formatNumber(sum_pelunasan_rp)),
                                $("<td class='text-right'>").text(formatNumber(sum_pelunasan_valas)),
                                $("<td colspan='3'>").html('&nbsp'),
                            );

                        }
                        $("#table_invoice").append(tbody); // append parents
                        $("#table_invoice tfoot").append(trfoot);
                        $("#example1_processing").css('display', 'none'); // hidden loading


                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        $("#example1_processing").css('display', 'none'); // hidden loading
                    }
                });

            }

            function loadPelunasan() {

                $("#example2_processing").css('display', ''); // show loading
                var id = "<?php echo $list->id; ?>";
                var no_pelunasan = "<?php echo $list->no_pelunasan; ?>";
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "<?php echo site_url('accounting/pelunasanpiutang/loadData') ?>",
                    data: {
                        id: id,
                        no_pelunasan: no_pelunasan,
                        load: 'pelunasan'
                    },
                    success: function(data) {

                        $("#table_pelunasan tbody").remove();
                        let no = 1;
                        let empty = true;
                        let tbody = $("<tbody />");
                        let tfoot = $("<tfoot />");
                        let total_rp = 0.00;
                        let total_valas = 0.00;
                        let status = data.head.status;
                        let $metode = '';
                        let $color = 'warna-pelunasan';
                        $.each(data.record, function(key, value) {

                            empty = false;
                            btn = '<button class="btn btn-danger btn-xs btn-delete-metode" name="btn-delete-metode" data-toggle="tooltip" title="Hapus" data-id="' + value.id + '" data-bukti="' + value.no_bukti + '"><i class="fa fa-trash"></i></button>';

                            var tr = $("<tr>").append(
                                $("<td style=''>").text(no),
                                $("<td style=''>").text(value.metode_text),
                                $("<td style=''>").text(value.no_bukti),
                                $("<td style=''>").text(value.tanggal_bukti),
                                $("<td style=''>").text(value.uraian),
                                $("<td style=''>").text(value.currency),
                                $("<td class='text-right'>").text(value.kurs),
                                $("<td class='text-right'>").text(formatNumber(value.total_rp)),
                                $("<td class='text-right'>").text(formatNumber(value.total_valas)),
                                $("<td class=''>").html(btn),
                            );

                            total_rp = total_rp + parseFloat(value.total_rp);
                            total_valas = total_valas + parseFloat(value.total_valas);
                            tbody.append(tr);
                            no++;

                            if (value.tipe === 'koreksi') {
                                $color = "warna-koreksi";
                            }

                        });

                        if (empty == true) {
                            var tr = $("<tr>").append($("<td colspan='9'>").text('Tidak ada Data'));
                            tbody.append(tr);
                        } else {
                            var trfoot = $("<tr class='style_total'>").append(
                                $("<td colspan='7' class='text-right'>").text('Total'),
                                $("<td class='text-right " + $color + " '>").text(formatNumber(total_rp)),
                                $("<td class='text-right " + $color + " '>").text(formatNumber(total_valas)),
                                $("<td colspan=''>").html('&nbsp'),
                            );

                        }
                        $("#table_pelunasan").append(tbody); // append parents
                        $("#table_pelunasan tfoot").append(trfoot);
                        $("#example2_processing").css('display', 'none'); // hidden loading


                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        $("#example2_processing").css('display', 'none'); // hidden loading
                    }
                });

            }



            $(document).on("click", ".btn-delete-invoice", function() {

                let row = $(this).closest("tr");

                if (confirm("Hapus invoice dari list ?")) {
                    row.remove();
                    renumberTable("#table_invoice");
                    recalcInvoiceTable();
                }

            });

            $(document).on("click", ".btn-delete-metode", function() {
                if (!confirm("Hapus pelunasan ini ?")) return;
                $(this).closest("tr").remove();
                recalcPelunasanTable();
            });

            function renumberTable(table) {
                $(table).find("tbody tr").each(function(index) {
                    $(this).find("td:first").text(index + 1);
                });
            }


            function recalcInvoiceTable() {

                let rows = $("#table_invoice tbody tr");

                if (rows.length === 0) {
                    $("#table_invoice tbody").html(
                        "<tr><td colspan='13'>Tidak Ada Data</td></tr>"
                    );

                    $("#table_invoice tfoot").remove();
                    return;
                }

                let sum_total_piutang_rp = 0;
                let sum_total_piutang_valas = 0;
                let sum_sisa_piutang_rp = 0;
                let sum_sisa_piutang_valas = 0;
                let sum_pelunasan_rp = 0;
                let sum_pelunasan_valas = 0;

                let no = 1;

                $("#table_invoice tbody tr").each(function() {

                    if ($(this).find("td").length < 12) return;

                    $(this).find("td:eq(0)").text(no++);

                    let total_piutang_rp = parseFloat($(this).find("td:eq(6)").text().replace(/,/g, '')) || 0;
                    let total_piutang_valas = parseFloat($(this).find("td:eq(7)").text().replace(/,/g, '')) || 0;
                    let sisa_piutang_rp = parseFloat($(this).find("td:eq(8)").text().replace(/,/g, '')) || 0;
                    let sisa_piutang_valas = parseFloat($(this).find("td:eq(9)").text().replace(/,/g, '')) || 0;
                    let pelunasan_rp = parseFloat($(this).find("td:eq(10)").text().replace(/,/g, '')) || 0;
                    let pelunasan_valas = parseFloat($(this).find("td:eq(11)").text().replace(/,/g, '')) || 0;

                    sum_total_piutang_rp += total_piutang_rp;
                    sum_total_piutang_valas += total_piutang_valas;
                    sum_sisa_piutang_rp += sisa_piutang_rp;
                    sum_sisa_piutang_valas += sisa_piutang_valas;
                    sum_pelunasan_rp += pelunasan_rp;
                    sum_pelunasan_valas += pelunasan_valas;

                });

                let tfoot = `
                <tfoot>
                    <tr class="style_total">
                        <td colspan="6" class="text-right">Total</td>
                        <td class="text-right">${formatNumber(sum_total_piutang_rp)}</td>
                        <td class="text-right">${formatNumber(sum_total_piutang_valas)}</td>
                        <td class="text-right">${formatNumber(sum_sisa_piutang_rp)}</td>
                        <td class="text-right">${formatNumber(sum_sisa_piutang_valas)}</td>
                        <td class="text-right">${formatNumber(sum_pelunasan_rp)}</td>
                        <td class="text-right">${formatNumber(sum_pelunasan_valas)}</td>
                        <td></td>
                    </tr>
                </tfoot>
                `;

                if ($("#table_invoice tfoot").length) {
                    $("#table_invoice tfoot").replaceWith(tfoot);
                } else {
                    $("#table_invoice").append(tfoot);
                }
            }


            function recalcPelunasanTable() {

                let rows = $("#table_pelunasan tbody tr");

                if (rows.length === 0) {
                    $("#table_pelunasan tbody").html(
                        "<tr><td colspan='10'>Tidak Ada Data</td></tr>"
                    );

                    $("#table_pelunasan tfoot").remove();
                    return;
                }

                let total_rp = 0;
                let total_valas = 0;
                let no = 1;

                $("#table_pelunasan tbody tr").each(function() {

                    if ($(this).find("td").length < 9) return;

                    $(this).find("td:eq(0)").text(no++);

                    let rp = parseFloat($(this).find("td:eq(7)").text().replace(/,/g, '')) || 0;
                    let valas = parseFloat($(this).find("td:eq(8)").text().replace(/,/g, '')) || 0;

                    total_rp += rp;
                    total_valas += valas;

                });

                let tfoot = `
                <tfoot>
                    <tr class="style_total">
                        <td colspan="7" class="text-right">Total</td>
                        <td class="text-right">${formatNumber(total_rp)}</td>
                        <td class="text-right">${formatNumber(total_valas)}</td>
                        <td></td>
                    </tr>
                </tfoot>
                `;

                if ($("#table_pelunasan tfoot").length) {
                    $("#table_pelunasan tfoot").replaceWith(tfoot);
                } else {
                    $("#table_pelunasan").append(tfoot);
                }

            }



        })
    </script>
</body>

</html>