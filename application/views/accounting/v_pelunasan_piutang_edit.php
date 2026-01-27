<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
    <style>
        .bb {
            border-bottom: 2px solid #ddd !important;
        }

        button[id="btn-simpan"],
        button[id="btn-confirm"],
        button[id="btn-cancel"],
        button[id="btn-edit"] {
            display: none;
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

        .btn-outline-success {
            color: #28a745;
            /* hijau */
            border: 1px solid #28a745;
            background: transparent;
        }

        .btn-outline-success:hover {
            background: #28a745;
            color: #fff;
        }

        .text-dark {
            color: #000 !important;
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

        .coa-info {
            display: block;
            min-height: 20px;
            /* tinggi tetap */
            line-height: 1.2;
            overflow: hidden;
            margin-top: 3px;
            padding: 4px;
            background: #f7f7f7;
            border-radius: 4px;
            /* jangan biarkan mempengaruhi layout */
        }

        .coa-info small {
            display: block;
        }

        .coa-info small hr {
            margin: 4px 0;
            border-top: 1px dashed #ccc;
        }

        .warna-piutang {
            color: red
        }

        .warna-pelunasan {
            color: green
        }

        .warna-koreksi {
            color: purple
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
            <!-- Content Header (Status - Bar) -->
            <section class="content-header">
                <div id="status_bar">
                    <?php
                    $data['jen_status'] = $list->status;
                    $data['deptid'] = $id_dept;
                    $this->load->view("admin/_partials/statusbar.php", $data);
                    ?>
                </div>
            </section>
            <section class="content">
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><b><?php echo $list->no_pelunasan; ?></b></h3>
                    </div>
                    <div class="box-body">
                        <form class="form-horizontal" name="form-acc-periode" id="form-acc-periode">
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Kode</label></div>
                                        <div class="col-xs-8">
                                            <input type="text" class="form-control input-sm" name="kode" id="kode" readonly="readonly" value="<?php echo $list->no_pelunasan; ?>" />
                                        </div>
                                    </div>

                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Tanggal Transaksi</label></div>
                                        <div class="col-xs-8 col-md-8">
                                            <div id='tgl_transaksi'>
                                                <input type='text' class="form-control input-sm" name="tanggal_transaksi" id="tanggal_transaksi" value="<?php echo date("Y-m-d", strtotime($list->tanggal_transaksi)); ?>" disabled />
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <!-- <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Customer</label></div>
                                        <div class="col-xs-8">
                                            <input type="text" class="form-control input-sm" name="partner" id="partner" value="<?php echo $list->partner_nama; ?>" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Customer</label></div>
                                        <div class="col-xs-8">
                                            <select class="form-control input-sm" name="partner" id="partner" disabled></select>
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
                                                        <div class="col-md-3 col-lg-3">
                                                            <label>Faktur Akan dilunasi</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button class="btn btn-default btn-sm <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'hidden' : ''; ?>" id="btn-inv" name="btn-inv" <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'disabled' : ''; ?>><i class='fa fa-file-text' style='color: orange'></i> Faktur (<span id='tinv'>0</span>)</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-condesed table-hover rlstable over" width="100%" id="table_invoice" border="1">
                                                    <thead>
                                                        <tr>
                                                            <th class="style bb no">No.</th>
                                                            <th class="style bb" style="width:100px;">No Faktur</th>
                                                            <th class="style bb" style="width:130px;">No SJ</th>
                                                            <th class="style bb">Tanggal</th>
                                                            <th class="style bb">Curr</th>
                                                            <th class="style bb text-right">Kurs</th>
                                                            <th class="style bb text-right">Total Piutang (Rp)</th>
                                                            <th class="style bb text-right">Total Piutang (Valas)</th>
                                                            <th class="style bb text-right">Sisa Piutang (Rp)</th>
                                                            <th class="style bb text-right">Sisa Piutang (Valas)</th>
                                                            <th class="style bb text-right">Pelunasan (Rp)</th>
                                                            <th class="style bb text-right">Pelunasan (Valas)</th>
                                                            <th class="style bb " style="max-width:60px;">Status</th>
                                                            <!-- <th class="style bb " style="max-width:50px;">Lebih</th> -->
                                                            <th class="style bb" style="width:70px;">#</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td colspan='14'>Tidak Ada Data</td>
                                                        </tr>

                                                    </tbody>
                                                    <tfoot>
                                                    </tfoot>
                                                </table>
                                                <div id="example2_processing" class="table_processing" style="display: none; z-index:5;">
                                                    Processing...
                                                </div>
                                            </div>

                                            <hr style="border: 1px solid #ccc; margin: 30px 15px 20px 15px;">


                                            <!-- Tabel  -->
                                            <div class="col-md-12 table-responsive over">
                                                <div class="row" style="margin-bottom:5px;">
                                                    <div class="col-md-12">
                                                        <div class="col-md-3 col-lg-3">
                                                            <label>Pelunasan</label>
                                                        </div>
                                                        <div class="col-md-9 col-lg-7">
                                                            <button class="btn btn-sm btn-default <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'hidden' : ''; ?>" id="btn-kas-bank" name="btn-kas-bank" <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'disabled' : ''; ?>><i class='fa fa-bank' style='color: green'></i> Kas Bank (<span id='tbk'>0</span>)</button>
                                                            <button class="btn btn-sm btn-default  <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'hidden' : ''; ?>" id="btn-uang-muka" name="btn-uang-muka" <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'disabled' : ''; ?>><i class='fa fa-money' style='color: blue'></i> Uang Muka (<span id='tum'>0</span>)</button>
                                                            <button class="btn btn-sm btn-default  <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'hidden' : ''; ?>" id="btn-deposit" name="btn-deposit" <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'disabled' : ''; ?>><i class='fa fa-money' style='color: blue'></i> Deposit (<span id='tdep'>0</span>)</button>
                                                            <button class="btn btn-sm btn-default <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'hidden' : ''; ?>" id="btn-retur" name="btn-retur" <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'disabled' : ''; ?>><i class='fa fa-exchange' style='color: red'></i> Retur (<span id='tret'>0</span>)</button>
                                                            <button class="btn btn-sm btn-default <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'hidden' : ''; ?>" id="btn-koreksi-valas" name="btn-koreksi-valas" <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'disabled' : ''; ?>><i class='fa fa-exchange' style='color: purple'></i> Koreksi Kurs Bulan</button>
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
                                                            <th class="style bb no"></th>
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

                                            <hr style="border: 1px solid #ccc; margin: 30px 15px 20px 15px;">

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
                                                            <th class="style bb text-right">Total Piutang</th>
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
                                                <div id="example3_processing" class="table_processing" style="display: none; z-index:5;">
                                                    Processing...
                                                </div>
                                            </div>
                                            <!-- Tabel  -->

                                        </div>
                                        <!-- /.tab-pane -->

                                        <div class="tab-pane" id="tab_2">
                                            <div class="col-md-12"><label>Informasi Jurnal</label></div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label>No Jurnal</label></div>
                                                        <div class="col-xs-8">
                                                            <?php $link = site_url('accounting/jurnalentries/edit/' . encrypt_url($list_jurnal->kode)) ?>

                                                            <?php echo ($list_jurnal->kode) ? ': <a href="' . $link . '" target="_blank">' . $list_jurnal->kode . '</a>' : ''; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label>Periode </label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <?php echo ": " . $list_jurnal->periode; ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-xs-4"><label>Tanggal </label></div>
                                                        <div class="col-xs-8 col-md-8">
                                                            <?php echo ($list_jurnal->tanggal_dibuat) ? ": " . date('Y-m-d', strtotime($list_jurnal->tanggal_dibuat)) : ' :  '; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    <footer class="main-footer">
        <div id="foot">
            <?php $this->load->view("admin/_partials/footer_new.php"); ?>
        </div>
    </footer>
    <?php $this->load->view("admin/_partials/modal.php") ?>
    <?php $this->load->view("admin/_partials/js.php") ?>
    <script src="<?php echo site_url('dist/js/formatAdded.js') ?>"></script>
    <script>
        $(function() {

            $(document).on('focus', '.select2', function(e) {
                if (e.originalEvent) {
                    var s2element = $(this).siblings('select');
                    s2element.select2('open');

                    // Set focus back to select2 element on closing.
                    s2element.on('select2:closing', function(e) {
                        s2element.select2('focus');
                    });
                }
            });

            get_total_by_partner("<?php echo $list->partner_id; ?>");

            $('#tgl_transaksi').datetimepicker({
                format: 'YYYY-MM-DD',
                ignoreReadonly: true,
                defaultDate: new Date()
            });

            var status = `<?php echo $list->status; ?>`;
            if (status == 'cancel') {
                $("#btn-cancel").hide();
                $("#btn-edit").hide();
                $("#btn-confirm").hide();
            } else if (status == 'done') {
                $("#btn-cancel").show();
            } else { // draft
                $("#btn-cancel").show();
                $("#btn-edit").show();
                $("#btn-confirm").show();
            }


            $(document).on('click', '#btn-edit', function(e) {

                $("#btn-simpan").show(); //tampilkan btn-simpan
                $("#btn-edit").hide(); //sembuyikan btn-edit
                $("#btn-confirm").hide(); //sembuyikan btn-confirm
                $('#partner').prop('disabled', false);
                $('.select-koreksi').prop('disabled', true);

                $("#btn-cancel").attr('id', 'btn-cancel-edit'); // ubah id btn-cancel jadi btn-cancel-edit
                // $('#tanggal_transaksi').attr('disabled', false).attr('id', 'tanggal_transaksi');

                $('#btn-inv').prop('disabled', true);
                $('#btn-kas-bank').prop('disabled', true);
                $('#btn-uang-muka').prop('disabled', true);
                $('#btn-deposit').prop('disabled', true);
                $('#btn-retur').prop('disabled', true);
                $('#btn-koreksi-valas').prop('disabled', true);

                $('.btn-delete-metode').prop('disabled', true);
                $('.btn-distribusi').prop('disabled', true);
                $('.btn-delete-invoice').prop('disabled', true);

                $('.btn-koreksi').prop('disabled', true);
                $('.btn-tambah-koreksi').prop('disabled', true);
                $('.btn-hapus-koreksi').prop('disabled', true);

            });


            $("#btn-cancel-edit").unbind("click");
            $(document).on('click', '#btn-cancel-edit', function(e) {

                $("#btn-simpan").hide(); //sembuyikan btn-simpan
                $("#btn-edit").show(); //tampilkan btn-edit
                $("#btn-confirm").show(); //tampilkan btn-confirm
                $('#partner').prop('disabled', true);
                $('.select-koreksi').prop('disabled', false);

                $("#btn-cancel-edit").attr('id', 'btn-cancel'); // ubah id btn-cancel-edit jadi btn-cancel
                $('#tanggal_transaksi').attr('disabled', true);

                var partner_id = `<?php echo $list->partner_id; ?>`;
                var partner_name = `<?php echo $list->partner_nama; ?>`;

                var defaultOption = new Option(partner_name, partner_id, true, true);
                $('#partner').append(defaultOption).trigger('change');
                $('#partner').attr('disabled', true).attr('id', 'partner');

                $('#btn-inv').prop('disabled', false);
                $('#btn-kas-bank').prop('disabled', false);
                $('#btn-uang-muka').prop('disabled', false);
                $('#btn-deposit').prop('disabled', false);
                $('#btn-retur').prop('disabled', false);
                $('#btn-koreksi-valas').prop('disabled', false);

                $('.btn-delete-metode').prop('disabled', false);
                $('.btn-distribusi').prop('disabled', false);
                $('.btn-delete-invoice').prop('disabled', false);

                $('.btn-tambah-koreksi').prop('disabled', false);
                $('.btn-hapus-koreksi').prop('disabled', false);
                $('.btn-koreksi').prop('disabled', false);
            });

            $("#partner").on('change', function(e) {
                var id = $(this).val();
                if (id) {
                    get_total_by_partner(id);
                } else {
                    get_total_by_partner('No');
                }
            });


            function get_total_by_partner(id) {

                $("#tinv").html('<li class="fa fa-spinner fa-spin"></i>');
                $("#tbk").html('<li class="fa fa-spinner fa-spin"></i>');
                $("#tum").html('<li class="fa fa-spinner fa-spin"></i>');
                $("#tdep").html('<li class="fa fa-spinner fa-spin"></i>');
                $("#tret").html('<li class="fa fa-spinner fa-spin"></i>');

                var partner = id;

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
                        $("#tdep").html(data.total.total_deposit)
                        $("#tret").html(data.total.total_retur)

                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.responseText);
                    }
                });
            }



            //select 2 Customer
            $('#partner').select2({
                allowClear: true,
                placeholder: "Select Customer",
                ajax: {
                    dataType: 'JSON',
                    type: "POST",
                    url: "<?php echo base_url(); ?>accounting/pelunasanpiutang/get_list_customer",
                    data: function(params) {
                        return {
                            name: params.term,
                        };
                    },
                    processResults: function(data) {
                        var results = [];
                        $.each(data, function(index, item) {
                            results.push({
                                id: item.id,
                                text: item.nama
                            });
                        });
                        return {
                            results: results
                        };
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        //alert('Error data');
                        //alert(xhr.responseText);
                    }
                }
            });

            var partner_id = `<?php echo $list->partner_id; ?>`;
            var partner_name = `<?php echo $list->partner_nama; ?>`;
            var tgl_transaksi = `<?php echo date("Y-m-d", strtotime($list->tanggal_transaksi)); ?>`;

            var defaultOption = new Option(partner_name, partner_id, true, true);
            $('#partner').append(defaultOption).trigger('change');

            function simpanData(params) {
                $('#btn-simpan').button('loading');
                please_wait(function() {});

                var baseUrl = '<?php echo base_url(); ?>';

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: baseUrl + 'accounting/pelunasanpiutang/simpan',
                    data: {
                        kode: $('#kode').val(),
                        partner: $('#partner').val(),
                        tgl_transaksi: $('#tanggal_transaksi').val(),
                        retry: params
                    },
                    success: function(data) {
                        unblockUI(function() {});
                        if (data.status == 'failed') {
                            bootbox.confirm({
                                message: data.message + "<br><br>Apakah Anda yakin ingin mengubah data ini?",
                                title: "<i class='fa fa-warning text-danger'></i> Konfirmasi Ulang Simpan Data",
                                buttons: {
                                    confirm: {
                                        label: 'Ya, Ubah ',
                                        className: 'btn-primary btn-sm'
                                    },
                                    cancel: {
                                        label: 'Tidak',
                                        className: 'btn-default btn-sm'
                                    }
                                },
                                callback: function(result) {
                                    if (result) {
                                        simpanData(true); // kirim ulang
                                    } else {
                                        alert_notify('fa fa-info', 'Perubahan dibatalkan', 'info', function() {});
                                        var defaultOption = new Option(partner_name, partner_id, true, true);
                                        $('#partner').append(defaultOption).trigger('change');
                                        $("#tanggal_transaksi").val(tgl_transaksi);
                                    }
                                }
                            });
                        } else {
                            alert_notify(data.icon, data.message, data.type, function() {});
                        }
                        $('#btn-simpan').button('reset');
                        refresh();

                        $("#btn-simpan").hide(); //sembuyikan btn-simpan
                        $("#btn-edit").show(); //tampilkan btn-edit
                        $("#btn-confirm").show(); //tampilkan btn-confirm
                        $('#partner').prop('disabled', true);
                        $('#tanggal_transaksi').attr('disabled', true);


                        $('#btn-inv').prop('disabled', false);
                        $('#btn-kas-bank').prop('disabled', false);
                        $('#btn-uang-muka').prop('disabled', false);
                        $('#btn-deposit').prop('disabled', false);
                        $('#btn-retur').prop('disabled', false);
                        $('#btn-koreksi-valas').prop('disabled', false);

                        $('.btn-delete-metode').prop('disabled', false);
                        $('.btn-distribusi').prop('disabled', false);
                        $('.btn-delete-invoice').prop('disabled', false);

                    },
                    error: function(xhr) {
                        unblockUI(function() {});
                        $('#btn-simpan').button('reset');

                        let msg = xhr.responseJSON?.message || 'Terjadi kesalahan tidak diketahui';

                        if (xhr.status === 422) {
                            alert_notify('fa fa-warning', msg, 'danger', function() {}); // validasi
                        } else if (xhr.status === 404) {
                            alert_notify('fa fa-warning', 'Data tidak ditemukan', 'danger', function() {});
                        } else if (xhr.status === 401) {
                            alert_modal_warning('Sesi Anda telah habis, silakan login ulang', function() {});
                            // window.location.href = baseUrl;
                        } else {
                            alert_notify('fa fa-warning', msg, 'danger', function() {});
                            // console.log(xhr)
                        }
                    }
                });
            }

            $('#btn-simpan').off('click').on('click', function() {
                if (!$('#partner').val()) {
                    alert_notify('fa fa-warning', 'Customer Harus diisi !', 'danger');
                    return;
                }
                simpanData(false);
            });



            $('#btn-confirm').off('click').on('click', function() {

                var no_pelunasan = "<?php echo $list->no_pelunasan; ?>";
                var btn_load = $(this);

                bootbox.confirm({
                    message: "Apakah Anda ingin mengkormasi Data Pelunasan ini  ?",
                    title: "<i class='fa fa-warning' ></i> Konfirmasi Pelunasan !",
                    buttons: {
                        confirm: {
                            label: 'Yes',
                            className: 'btn-primary btn-sm'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-default btn-sm'
                        },
                    },
                    callback: function(result) {

                        if (result == true) {
                            btn_load.button('loading');
                            please_wait(function() {});
                            $.ajax({
                                type: "POST",
                                url: '<?php echo base_url('accounting/pelunasanpiutang/confirm_pelunasan_piutang') ?>',
                                dataType: 'JSON',
                                data: {
                                    no_pelunasan: no_pelunasan,
                                },
                                success: function(data) {
                                    if (data.status == 'failed') {
                                        alert_modal_warning(data.message);
                                        unblockUI(function() {});
                                        btn_load.button('reset');
                                    } else {
                                        btn_load.button('reset');
                                        unblockUI(function() {
                                            setTimeout(function() {
                                                alert_notify(data.icon, data.message, data.type, function() {});
                                            }, 1000);
                                        });
                                        refresh();

                                    }

                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    unblockUI(function() {});
                                    btn_load.button('reset');

                                    let msg = xhr.responseJSON?.message || 'Terjadi kesalahan tidak diketahui';

                                    if (xhr.status === 422) {
                                        alert_notify('fa fa-warning', msg, 'danger', function() {}); // validasi
                                    } else if (xhr.status === 404) {
                                        alert_notify('fa fa-warning', 'Data tidak ditemukan', 'danger', function() {});
                                    } else if (xhr.status === 401) {
                                        alert_modal_warning('Sesi Anda telah habis, silakan login ulang', function() {});
                                        window.location.href = baseUrl;
                                    } else {
                                        alert_notify('fa fa-warning', msg, 'danger', function() {});
                                    }
                                }
                            });

                        }
                    }
                });


            });



            $("#btn-cancel").unbind("click");
            $(document).on('click', '#btn-cancel', function(e) {

                var no_pelunasan = "<?php echo $list->no_pelunasan; ?>";
                var btn_load = $(this);
                bootbox.confirm({
                    message: "Apakah Anda ingin membatalkan Data Pelunasan ini  ?",
                    title: "<i class='fa fa-warning' ></i> Batal Pelunasan !",
                    buttons: {
                        confirm: {
                            label: 'Yes',
                            className: 'btn-primary btn-sm'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-default btn-sm'
                        },
                    },
                    callback: function(result) {

                        if (result == true) {
                            btn_load.button('loading');
                            please_wait(function() {});
                            $.ajax({
                                type: "POST",
                                url: '<?php echo base_url('accounting/pelunasanpiutang/cancel_pelunasan_piutang') ?>',
                                dataType: 'JSON',
                                data: {
                                    no_pelunasan: no_pelunasan,
                                },
                                success: function(data) {
                                    if (data.status == 'failed') {
                                        alert_modal_warning(data.message);
                                        unblockUI(function() {});
                                        btn_load.button('reset');
                                    } else {
                                        btn_load.button('reset');
                                        unblockUI(function() {
                                            setTimeout(function() {
                                                alert_notify(data.icon, data.message, data.type, function() {});
                                            }, 1000);
                                        });
                                        refresh();
                                        $("#btn-edit").hide();
                                        $("#btn-confirm").hide();
                                        $("#btn-cancel").hide();
                                    }

                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    unblockUI(function() {});
                                    btn_load.button('reset');
                                    // var err = JSON.parse(xhr.responseText);
                                    // if (xhr.status == 401) {
                                    //     alert(err.message);
                                    // } else {
                                    //     alert(err.message);
                                    //     // alert("Gagal membatalkan !")
                                    // }

                                    let msg = xhr.responseJSON?.message || 'Terjadi kesalahan tidak diketahui';
                                    alert_notify('fa fa-warning', msg, 'danger', function() {});
                                    // console.error('AJAX Error:', xhr);
                                }
                            });

                        }
                    }
                });

            });

            $("#btn-inv").on("click", function(e) {
                e.preventDefault();
                $("#tambah_data").modal({
                    show: true,
                    backdrop: 'static'
                });
                $("#tambah_data").removeClass('modal fade lebar_mode').addClass('modal fade lebar');
                $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $('.modal-title').text('List Faktur');
                $.post("<?= base_url('accounting/pelunasanpiutang/get_view_faktur') ?>", {
                    no_pelunasan: "<?php echo $list->no_pelunasan; ?>",
                    partner: $("#partner").val()
                }, function(data) {
                    setTimeout(function() {
                        $(".tambah_data").html(data.data);
                        $("#btn-tambah").html("Tambahkan");
                    }, 1000);
                });
                $('#tambah_data').on('hidden.bs.modal', function() {

                });
            });


            $("#btn-kas-bank").on("click", function(e) {
                e.preventDefault();
                $("#tambah_data").modal({
                    show: true,
                    backdrop: 'static'
                });
                $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $('.modal-title').text('List Kas Bank');
                $("#tambah_data").removeClass('modal fade lebar_mode').addClass('modal fade lebar');
                $.post("<?= base_url('accounting/pelunasanpiutang/get_view_kas_bank') ?>", {
                    no_pelunasan: "<?php echo $list->no_pelunasan; ?>",
                    partner: $("#partner").val(),
                    type: 'kas',
                }, function(data) {
                    setTimeout(function() {
                        $(".tambah_data").html(data.data);
                        $("#btn-tambah").html("Tambahkan");
                    }, 1000);
                    // }).done(function(html){
                    //    $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
                });
                $('#tambah_data').on('hidden.bs.modal', function() {

                });
            });


            $("#btn-uang-muka").on("click", function(e) {
                e.preventDefault();
                $("#tambah_data").modal({
                    show: true,
                    backdrop: 'static'
                });
                $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $('.modal-title').text('List Uang Muka');
                $("#tambah_data").removeClass('modal fade lebar_mode').addClass('modal fade lebar');
                $.post("<?= base_url('accounting/pelunasanpiutang/get_view_kas_bank') ?>", {
                    no_pelunasan: "<?php echo $list->no_pelunasan; ?>",
                    partner: $("#partner").val(),
                    type: 'um',
                }, function(data) {
                    setTimeout(function() {
                        $(".tambah_data").html(data.data);
                        $("#btn-tambah").html("Tambahkan");
                    }, 1000);
                    // }).done(function(html){
                    //    $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
                });
                $('#tambah_data').on('hidden.bs.modal', function() {

                });
            });


            $("#btn-deposit").on("click", function(e) {
                e.preventDefault();
                $("#tambah_data").modal({
                    show: true,
                    backdrop: 'static'
                });
                $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $('.modal-title').text('List Deposit');
                $("#tambah_data").removeClass('modal fade lebar_mode').addClass('modal fade lebar');
                $.post("<?= base_url('accounting/pelunasanpiutang/get_view_kas_bank') ?>", {
                    no_pelunasan: "<?php echo $list->no_pelunasan; ?>",
                    partner: $("#partner").val(),
                    type: 'depo',
                }, function(data) {
                    setTimeout(function() {
                        $(".tambah_data").html(data.data);
                        $("#btn-tambah").html("Tambahkan");
                    }, 1000);
                    // }).done(function(html){
                    //    $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
                });
                $('#tambah_data').on('hidden.bs.modal', function() {

                });
            });


            $("#btn-retur").on("click", function(e) {
                e.preventDefault();
                $("#tambah_data").modal({
                    show: true,
                    backdrop: 'static'
                });
                $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $('.modal-title').text('List Retur');
                $("#tambah_data").removeClass('modal fade lebar_mode').addClass('modal fade lebar');
                $.post("<?= base_url('accounting/pelunasanpiutang/get_view_retur') ?>", {
                    no_pelunasan: "<?php echo $list->no_pelunasan; ?>",
                    partner: $("#partner").val()
                }, function(data) {
                    setTimeout(function() {
                        $(".tambah_data").html(data.data);
                        $("#btn-tambah").html("Tambahkan");
                    }, 1000);
                    // }).done(function(html){
                    //    $("#tambah_data .modal-dialog .modal-content .modal-footer #btn-tambah").attr('disabled',false);
                });
                $('#tambah_data').on('hidden.bs.modal', function() {

                });
            });


            $(document).on("click", ".btn-distribusi", function(e) {
                let id = $(this).attr("data-id");
                let no_inv = $(this).attr("data-inv");
                distribusi_invoice(id, no_inv)
            });


            function distribusi_invoice(id, no_inv) {

                $("#tambah_data").modal({
                    show: true,
                    backdrop: "static"
                });

                $("#tambah_data").removeClass("modal fade lebar").addClass("modal fade lebar_mode");
                $("#tambah_data .modal-dialog .modal-content .modal-body").addClass("add_batch");

                $(".tambah_data").html(
                    '<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>'
                );
                $(".modal-title").html("Edit Distribusi Pelunasan <b>" + no_inv + "</b>");

                $.post("<?= base_url('accounting/pelunasanpiutang/get_view_edit_distribusi') ?>", {
                    no_pelunasan: "<?= $list->no_pelunasan; ?>",
                    partner: "<?= $list->partner_id; ?>",
                    id: id
                }, function(data) {
                    setTimeout(function() {
                        $(".tambah_data").html(data.data);
                        $("#btn-tambah").html("Simpan");

                        // ⬇️ re-bind format angka ke elemen hasil ajax
                        bindFormatAngka(document.querySelector("#tambah_data"));
                    }, 1000);
                });
                $('#tambah_data').on('hidden.bs.modal', function() {
                    // optional: reset isi modal supaya fresh setiap buka
                    $(".tambah_data").html("");
                });

            }

            $(document).on("click", "#btn-koreksi-valas", function(e) {
                koreksi_kurs()
            });


            function koreksi_kurs() {

                $("#tambah_data").modal({
                    show: true,
                    backdrop: "static"
                });

                $("#tambah_data").removeClass("modal fade lebar").addClass("modal fade lebar_mode");
                $("#tambah_data .modal-dialog .modal-content .modal-body").addClass("add_batch");

                $(".tambah_data").html(
                    '<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>'
                );
                $(".modal-title").html("Koreksi Kurs Bulan ");

                $.post("<?= base_url('accounting/pelunasanpiutang/get_view_koreksi_kurs') ?>", {
                    no_pelunasan: "<?= $list->no_pelunasan; ?>",
                    partner: "<?= $list->partner_id; ?>",
                }, function(data) {
                    setTimeout(function() {
                        $(".tambah_data").html(data.data);
                        $("#btn-tambah").html("Simpan");

                        // ⬇️ re-bind format angka ke elemen hasil ajax
                        bindFormatAngka(document.querySelector("#tambah_data"));
                    }, 1000);
                });
                $('#tambah_data').on('hidden.bs.modal', function() {
                    // optional: reset isi modal supaya fresh setiap buka
                    $(".tambah_data").html("");
                });

            }


            $(document).on("click", ".btn-delete-invoice", function(e) {
                let id = $(this).attr('data-id');
                let no_inv = $(this).attr('data-inv');
                delete_invoice(this, id, no_inv)
            });

            function delete_invoice(btn, id, no_inv) {

                var no_pelunasan = "<?php echo $list->no_pelunasan; ?>";

                bootbox.confirm({
                    message: "Apakah Anda ingin menghapus data Faktur " + no_inv + " ?",
                    title: "<i class='glyphicon glyphicon-trash' style='color: red'></i> Delete !",
                    buttons: {
                        confirm: {
                            label: 'Yes',
                            className: 'btn-primary btn-sm'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-default btn-sm'
                        },
                    },
                    callback: function(result) {

                        if (result == true) {
                            var btn_load = $(btn);
                            btn_load.button('loading');
                            please_wait(function() {});
                            $.ajax({
                                type: "POST",
                                url: '<?php echo base_url('accounting/pelunasanpiutang/delete_pelunasan_piutang_faktur') ?>',
                                dataType: 'JSON',
                                data: {
                                    no_pelunasan: no_pelunasan,
                                    id: id,
                                    no_faktur: no_inv,
                                },
                                success: function(data) {
                                    if (data.status == 'failed') {
                                        alert_modal_warning(data.message);
                                        unblockUI(function() {});
                                        btn_load.button('reset');
                                    } else {
                                        btn_load.button('reset');
                                        unblockUI(function() {
                                            setTimeout(function() {
                                                alert_notify(data.icon, data.message, data.type, function() {});
                                            }, 1000);
                                        });
                                        refresh();
                                    }

                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    unblockUI(function() {});
                                    btn_load.button('reset');
                                    if (xhr.status == 401) {
                                        var err = JSON.parse(xhr.responseText);
                                        alert(err.message);
                                    } else {
                                        alert("Error Hapus Data!")
                                    }
                                }
                            });

                        }
                    }
                });
            }


            $(document).on("click", ".btn-delete-metode", function(e) {
                let id = $(this).attr('data-id');
                let no_bukti = $(this).attr('data-bukti');
                delete_metode(this, id, no_bukti);
            });

            function delete_metode(btn, id, no_bukti) {
                var no_pelunasan = "<?php echo $list->no_pelunasan; ?>";
                bootbox.confirm({
                    message: "Apakah Anda ingin menghapus data Metode Pelunasan " + no_bukti + " ?",
                    title: "<i class='glyphicon glyphicon-trash' style='color: red'></i> Delete !",
                    buttons: {
                        confirm: {
                            label: 'Yes',
                            className: 'btn-primary btn-sm'
                        },
                        cancel: {
                            label: 'No',
                            className: 'btn-default btn-sm'
                        },
                    },
                    callback: function(result) {

                        if (result == true) {
                            var btn_load = $(btn);
                            btn_load.button('loading');
                            please_wait(function() {});
                            $.ajax({
                                type: "POST",
                                url: '<?php echo base_url('accounting/pelunasanpiutang/delete_pelunasan_piutang_metode') ?>',
                                dataType: 'JSON',
                                data: {
                                    no_pelunasan: no_pelunasan,
                                    id: id,
                                    no_bukti: no_bukti,
                                },
                                success: function(data) {
                                    if (data.status == 'failed') {
                                        alert_modal_warning(data.message);
                                        unblockUI(function() {});
                                        btn_load.button('reset');
                                    } else {
                                        btn_load.button('reset');
                                        unblockUI(function() {
                                            setTimeout(function() {
                                                alert_notify(data.icon, data.message, data.type, function() {});
                                            }, 1000);
                                        });
                                        refresh();
                                    }

                                },
                                error: function(xhr, ajaxOptions, thrownError) {
                                    unblockUI(function() {});
                                    btn_load.button('reset');
                                    if (xhr.status == 401) {
                                        var err = JSON.parse(xhr.responseText);
                                        alert(err.message);
                                    } else {
                                        alert("Error Hapus Data!")
                                    }
                                }
                            });

                        }
                    }
                });
            }


            function formatNumber(n) {
                return new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(n);
            }

            loadResume();
            loadPelunasan();
            loadInvoice();

            /*    var statusBayar = [{
                       id: 'belum_bayar',
                       text: 'Belum Bayar',
                       color: 'label label-danger'
                   },
                   {
                       id: 'partial',
                       text: 'Partial',
                       color: 'label label-warning text-dark'
                   },
                   {
                       id: 'lunas',
                       text: 'Lunas',
                       color: 'label label-success'
                   },
               ];

               function getstatusBayarById(id) {
                   return statusBayar.find(o => o.id === id) || null;
               } */

            function loadResume() {

                $("#example3_processing").css('display', ''); // show loading
                var id = "<?php echo $list->id; ?>";
                var no_pelunasan = "<?php echo $list->no_pelunasan; ?>";
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "<?php echo site_url('accounting/pelunasanpiutang/loadData') ?>",
                    data: {
                        id: id,
                        no_pelunasan: no_pelunasan,
                        load: 'resume'
                    },
                    success: function(data) {

                        $("#table-resume tbody").remove();
                        let no = 1;
                        let empty = true;
                        let tbody = $("<tbody />");
                        let status = data.head.status;
                        $.each(data.record, function(key, value) {

                            empty = false;
                            if (value.tipe_currency === 'Valas') {
                                $mu = (value.currency != '') ? value.tipe_currency + `<br><div><small>${value.currency} - ${formatNumber(value.kurs)}</small></div>` : value.tipe_currency;
                            } else {
                                $mu = value.tipe_currency;
                            }

                            // render cell koreksi (select + small + tombol)
                            let koreksiCell = renderKoreksiCell(value, status);
                            var tr = $("<tr>").append(
                                $("<td style='font-weight:bold;'>").html($mu),
                                $("<td class='text-right warna-piutang'>").text(formatNumber(value.total_piutang)),
                                $("<td class='text-right warna-koreksi'>").text(formatNumber(value.total_koreksi)),
                                $("<td class='text-right warna-pelunasan'>").text(formatNumber(value.total_pelunasan)),
                                $("<td style=''>").text(value.keterangan),
                                $("<td class='text-right'>").text(formatNumber(value.selisih)),
                                $("<td>").append(koreksiCell.wrapper),
                                koreksiCell.button
                            );

                            tbody.append(tr);
                            no++;

                        });

                        if (empty == true) {
                            var tr = $("<tr>").append($("<td colspan='7'>").text('Tidak ada Data'));
                            tbody.append(tr);
                        }

                        $("#table-resume").append(tbody); // append parents
                        $("#example3_processing").css('display', 'none'); // hidden loading
                        // panggil select2 untuk elemen baru
                        // initSelectKoreksi();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        $("#example3_processing").css('display', 'none'); // hidden loading
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
                            if (status == 'draft') {
                                btn = '<button class="btn btn-danger btn-xs btn-delete-metode" name="btn-delete-metode" data-toggle="tooltip" title="Hapus" data-id="' + value.id + '" data-bukti="' + value.no_bukti + '"><i class="fa fa-trash"></i></button>';
                            } else {
                                btn = '';
                            }

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
                        $("#table_pelunasan").append(trfoot);
                        $("#example2_processing").css('display', 'none'); // hidden loading


                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        $("#example2_processing").css('display', 'none'); // hidden loading
                    }
                });

            }

            function


            loadInvoice() {

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
                            if (status == 'draft') {
                                btn = '<button class="btn btn-primary btn-xs btn-distribusi" name="btn-distribusi" data-toggle="tootlip" title="Distribusi" data-id="' + value.id + '" data-inv="' + value.no_faktur + '"><i class="fa fa-edit"></i></button>  <button class="btn btn-danger btn-xs btn-delete-invoice" name="btn-delete-invoice" data-toggle="tooltip" title="Hapus" data-id="' + value.id + '" data-inv="' + value.no_faktur + '"><i class="fa fa-trash"></i></button>';
                            } else {
                                btn = '';
                            }

                            let statusHtml = $("<span>").addClass(value.status_color).text(value.status_text);
                            let statusLebih = $("<span>").addClass(value.status_color_lebih).text(value.status_text_lebih);

                            var tr = $("<tr>").append(
                                $("<td style=''>").text(no),
                                $("<td style=''>").text(value.no_faktur),
                                $("<td style=''>").html('<a href="#" class="detail-sj" data-sj="' + value.no_sj + '">' + value.no_sj + '</a>'),
                                $("<td style=''>").text(value.tanggal_faktur),
                                $("<td style=''>").text(value.currency),
                                $("<td class='text-right'>").text(value.kurs),
                                $("<td class='text-right'>").text(formatNumber(value.total_piutang_rp)),
                                $("<td class='text-right'>").text(formatNumber(value.total_piutang_valas)),
                                $("<td class='text-right'>").text(formatNumber(value.sisa_piutang_rp)),
                                $("<td class='text-right'>").text(formatNumber(value.sisa_piutang_valas)),
                                $("<td class='text-right'>").text(formatNumber(value.pelunasan_rp)),
                                $("<td class='text-right'>").text(formatNumber(value.pelunasan_valas)),
                                $("<td class='text-center'>").append(statusHtml),
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
                        $("#table_invoice").append(trfoot);
                        $("#example1_processing").css('display', 'none'); // hidden loading


                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                        $("#example1_processing").css('display', 'none'); // hidden loading
                    }
                });

            }

            $(".modal").on('hidden.bs.modal', function() {
                refresh();
                $("#tambah_data").toggleClass("lebar lebar_mode");
            });


            function refresh() {
                $("#status_bar").load(location.href + " #status_bar");
                loadResume();
                loadPelunasan();
                loadInvoice();
                loadLog();
                $("#tab_2").load(location.href + " #tab_2");
            }

            var koreksiOptions = [{
                    id: 'bayar_rupiah',
                    text: 'Bayar Rupiah'
                },
                {
                    id: 'kurang_bayar',
                    text: 'Kurang Bayar'
                },
                {
                    id: 'pembulatan',
                    text: 'Pembulatan'
                },
                {
                    id: 'selisih_kurs',
                    text: 'Selisih Kurs'
                }
            ];

            // function getKoreksiOptionById(id) {
            //     return koreksiOptions.find(o => o.id === id) || null;
            // }


            function initSelectKoreksi() {

                // $('.select-koreksi').select2({
                //     allowClear: true,
                //     placeholder: "Pilih Jenis Koreksi",
                //     data: koreksiOptions   // ambil dari variabel global
                // }).val(null).trigger("change");;

                $('.select-koreksi').select2({
                    allowClear: true,
                    placeholder: "Pilih Jenis Koreksi",
                    ajax: {
                        dataType: 'json',
                        type: "POST",
                        url: "<?php echo base_url(); ?>accounting/pelunasanpiutang/get_list_koreksi_select2",
                        data: function(params) {
                            // ambil currency dari atribut data-currency di elemen select
                            let currency = $(this).data('currency') || '';
                            return {
                                name: params.term, // keyword pencarian
                                tipe_currency: currency, // filter tambahan
                                tipe: $(this).attr('data-tipe')
                            };
                        },
                        processResults: function(data) {
                            return {
                                results: data.map(item => ({
                                    id: item.kode,
                                    text: item.nama_koreksi,
                                    get_coa: item.get_coa // true/false
                                }))
                            };
                        },
                    }
                });

                $('.select-koreksi').on('change', function() {
                    let selectedVal = $(this).val(); // tipe yang dipilih user
                    let defaultVal = $(this).data('default'); // tipe default dari database
                    let $tdButton = $(this).closest('td').next('td'); // kolom tombol
                    let $coaInfo = $(this).siblings('.coa-info'); // div small COA
                    let id = $(this).data('id');

                    let selectedData = $(this).select2('data')[0] || {};
                    let hasCoa = selectedData.get_coa ?? $(this).find('option:selected').data('get_coa');
                    let koreksiNama = selectedData.text ?? $(this).find('option:selected').text();

                    // console.log("get_coa:", hasCoa);

                    // update tombol
                    $tdButton.empty();
                    if (hasCoa === 'true' || hasCoa === true) {
                        // $tdButton.html('<button type="button" class="btn btn-sm btn-default btn-koreksi" data-tipe="' + selectedVal + '" data-summary="' + id + '" data-nm-koreksi="' + koreksiNama + '"><i class="fa fa-edit"></i></button>');
                        let btnKoreksi = `
                            <button type="button" class="btn btn-xs btn-primary btn-koreksi" 
                                data-tipe="${selectedVal}" 
                                data-summary="${id}" 
                                data-nm-koreksi="${koreksiNama}">
                                <i class="fa fa-edit"></i>
                            </button>`;

                        // Jika default dari database cocok, tambahkan tombol hapus
                        if (selectedVal === defaultVal && defaultVal !== '') {
                            btnKoreksi += `
                                <button type="button" class="btn btn-xs btn-danger btn-hapus-koreksi" 
                                    data-tipe="${selectedVal}" 
                                    data-summary="${id}" 
                                    title="Hapus Koreksi">
                                    <i class="fa fa-trash"></i>
                                </button>`;
                        }

                        $tdButton.html(btnKoreksi);
                    } else {

                        let btnKoreksi = `
                            <button type="button" class="btn btn-xs btn-success btn-tambah-koreksi" 
                                data-tipe="${selectedVal}" 
                                data-summary="${id}" 
                                data-nm-koreksi="${koreksiNama}">
                                <i class="fa fa-save"></i>
                            </button>`;

                        if (selectedVal === defaultVal && defaultVal !== '') {
                            btnKoreksi = `
                                <button type="button" class="btn btn-xs btn-danger btn-hapus-koreksi" 
                                    data-tipe="${selectedVal}" 
                                    data-summary="${id}" 
                                    title="Hapus Koreksi">
                                    <i class="fa fa-trash"></i>
                                </button>`;
                        }
                        $tdButton.html(btnKoreksi);

                    }

                    // console.log(selectedData);
                    // alert(hasCoa)
                    // update small: hanya tampil jika sama dengan default DB
                    if (selectedVal === defaultVal) {
                        $coaInfo.show();
                        let summaryId = $(this).data('id');

                        loadCoaInfo(selectedVal, summaryId, $coaInfo.find('small'));
                    } else {
                        $coaInfo.hide();
                        $coaInfo.find('small').text('');
                    }
                });

            }


            function renderKoreksiCell(value, status) {
                let keterangan = value.keterangan;
                let coaList = value.coa_list || [];
                let koreksiNama = value.koreksi_text;
                let mode = value.mode;
                let tipe = (keterangan == 'Uang Muka') ? 'um' : 'koreksi';

                let $tdButton = $('<td>');
                let $wrapper = $('<div>');
                let $coaInfo = $('<div class="coa-info"><small style="white-space:normal;"></small></div>');

                // ====================== Button Control ===========================
                function updateCoaButton() {
                    $tdButton.empty();

                    if (coaList.length > 0) { // Ada COA -> tampilkan edit & hapus
                        $tdButton.html(`
                            <button class="btn btn-xs btn-primary btn-koreksi" 
                                    data-tipe="${tipe}" data-summary="${value.id}" data-currency="${value.tipe_currency}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button class="btn btn-xs btn-danger btn-hapus-koreksi"
                                    data-tipe="${tipe}" data-summary="${value.id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        `);
                        $coaInfo.show();

                    } else if (coaList.length == 0 && value.selisih != 0) { // Tidak ada COA tapi ada selisih -> hanya edit
                        $tdButton.html(`
                            <button class="btn btn-xs btn-primary btn-koreksi" 
                                    data-tipe="${tipe}" data-summary="${value.id}" data-currency="${value.tipe_currency}">
                                <i class="fa fa-edit"></i>
                            </button>
                        `);
                        $coaInfo.hide();

                    } else { // tidak ada tindakan
                        $coaInfo.hide();
                    }
                }

                // ====================== Mode NORMAL ===========================
                if (mode == 'normal') {
                    if (value.alat_pelunasan === 'true') {
                        $alat = " <br> Alat Pelunasan : Ya";
                        $wrapper.append(koreksiNama).append($alat);
                    } else {
                        $wrapper.append(koreksiNama).append($coaInfo);
                    }
                    loadCoaInfo(value.id, $coaInfo.find('small')); // ambil text via ajax/func lama

                    // ====================== Mode SPLIT ============================
                } else if (mode == 'split') {

                    let content = "";
                    $.each(coaList, function(index, item) {
                        content += `<b>${item.head == 'true' ? 'Head' : 'Item  : ' +item.koreksi_text}</b><br>
                        ${item.head == 'true' && item.posisi != '' ? '' : item.posisi}
                        
                        ${item.faktur_id != 0 ? 'No Faktur :  '+item.no_faktur :  item.kode_coa + ' - '+ item.nama_coa}<br>
                        ${item.head == 'true' ? '' : 'Nominal  : ' +formatNumber(item.nominal)}
                        ${item.alat_pelunasan ==='true' ? '<br> Alat Pelunasan : Ya ' : ""}<br>
                        <hr>`;
                    });

                    $coaInfo.find('small').html(content);
                    $wrapper.append($coaInfo);
                }

                if (status == 'draft') {
                    updateCoaButton();
                }


                return {
                    wrapper: $wrapper,
                    button: $tdButton
                };
            }


            function renderKoreksiCell1(value, status) {
                // let gt_nm = getKoreksiOptionById(value.koreksi);
                let koreksiId = value.koreksi; // nilai default dari database
                let koreksiNama = value.koreksi_text;
                let hasCoa = value.koreksi_get_coa;
                let keterangan = value.keterangan;

                let tipe = (keterangan == 'Uang Muka') ? 'um' : 'koreksi';

                // bikin select
                let $select = $('<select>', {

                    class: 'form-control input-sm select-koreksi',
                    name: 'koreksi',
                    style: 'width:100% !important;',
                    'data-tipe': tipe,
                    'data-id': value.id,
                    'data-default': koreksiId,
                    'data-currency': value.tipe_currency
                });

                if (koreksiId && koreksiNama) {
                    let option = new Option(koreksiNama, koreksiId, true, true);
                    $(option).data('get_coa', value.koreksi_get_coa);
                    $select.append(option);
                }

                let $coaInfo = $('<div class="coa-info"><small style="white-space:normal;"></small></div>');
                if (keterangan.length === 0 || (keterangan === 'Uang Muka' && value.tipe_currency == 'Valas')) {
                    $wrapper = $('<div>').append('').append($coaInfo);
                } else {
                    // tempat info COA
                    if (status == 'draft') {
                        $wrapper = $('<div>').append($select).append($coaInfo);
                    } else {
                        $wrapper = $('<div>').append(koreksiNama).append($coaInfo);
                    }
                }

                // tombol aksi
                let $tdButton = $('<td>');

                // fungsi render COA dan tombol jika default valid
                function updateCoaButton(selectedId) {
                    $tdButton.empty();
                    if ((hasCoa == "true") && selectedId === koreksiId) {
                        // $tdButton.html('<button type="button" class="btn btn-sm btn-default btn-koreksi" data-tipe="' + koreksiId + '" data-summary="' + value.id + '" data-nm-koreksi="' + koreksiNama + '"><i class="fa fa-edit"></i></button>');
                        $tdButton.html(`
                            <button type="button" class="btn btn-xs btn-primary btn-koreksi" 
                                    data-tipe="${koreksiId}" 
                                    data-summary="${value.id}" 
                                    data-nm-koreksi="${koreksiNama}">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-xs btn-danger btn-hapus-koreksi"
                                    data-tipe="${koreksiId}" 
                                    data-summary="${value.id}" >
                                <i class="fa fa-trash"></i>
                            </button>
                        `);
                        $coaInfo.show();
                        if (value.tipe_currency == 'Rp') {
                            loadCoaInfo(selectedId, value.id, $coaInfo.find('small'));
                        }
                    } else if (hasCoa == 'false') {
                        $tdButton.html(`                           
                            <button type="button" class="btn btn-xs btn-danger btn-hapus-koreksi"
                                    data-tipe="${koreksiId}" 
                                    data-summary="${value.id}" >
                                <i class="fa fa-trash"></i>
                            </button>
                        `);
                        $coaInfo.hide();
                    } else {
                        $coaInfo.hide();
                    }
                }

                // initial render sesuai data database
                if (status == 'draft') {
                    updateCoaButton(koreksiId);
                }

                if (status == 'done') {
                    $coaInfo.show();
                    if (value.tipe_currency == 'Rp') {
                        loadCoaInfo(koreksiId, value.id, $coaInfo.find('small'));
                    }
                }

                // event onchange
                $select.on('change', function() {
                    let selectedData = $(this).select2('data')[0] || {};
                    let val = $(this).val();
                    hasCoa = selectedData.get_coa ?? $(this).find('option:selected').data('get_coa');
                    updateCoaButton(val);
                });

                return {
                    wrapper: $wrapper,
                    button: $tdButton
                };
            }


            function loadCoaInfo(summaryId, $target) {

                $.ajax({
                    url: "<?php echo site_url('accounting/pelunasanpiutang/getCoaByKoreksi') ?>",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        summary_id: summaryId
                    },
                    success: function(res) {
                        // console.log("COA response:", res); // debug
                        if (res && (res.coa_debit || res.coa_credit)) {
                            $target.html(
                                `Debit: ${res.coa_debit ?? '-'}<br>Credit: ${res.coa_credit ?? '-'}`
                            );
                        } else {
                            $target.text("");
                        }
                    },
                    error: function(xhr) {
                        // console.log("Error get COA:", xhr.responseText);
                        $target.text("");
                    }
                });
            }


            $(document).on("click", ".btn-koreksi", function(e) {
                let id_summary = $(this).attr("data-summary");;
                let tipe_currency = $(this).attr('data-currency');
                let tipe_koreksi = $(this).attr('data-tipe');
                koreksi(id_summary, tipe_currency, tipe_koreksi);
            });

            $(document).on("click", ".btn-hapus-koreksi", function(e) {
                let jenis_koreksi = $(this).attr("data-tipe");
                let id_summary = $(this).attr("data-summary");;
                // let nama_koreksi = $(this).attr('data-nm-koreksi');
                let no_pelunasan = "<?= $list->no_pelunasan; ?>";
                $.ajax({
                    url: "<?php echo site_url('accounting/pelunasanpiutang/delete_koreksi') ?>",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id_summary: id_summary,
                        jenis_koreksi: jenis_koreksi,
                        no_pelunasan: no_pelunasan,

                    },
                    success: function(res) {
                        // console.log("COA response:", res); // debug
                        // alert('berhasil');
                        refresh();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                    }
                });
            });


            $(document).on("click", ".btn-tambah-koreksi", function(e) {
                let jenis_koreksi = $(this).attr("data-tipe");
                let id_summary = $(this).attr("data-summary");;
                // let nama_koreksi = $(this).attr('data-nm-koreksi');
                let no_pelunasan = "<?= $list->no_pelunasan; ?>";
                $.ajax({
                    url: "<?php echo site_url('accounting/pelunasanpiutang/save_koreksi2') ?>",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id_summary: id_summary,
                        jenis_koreksi: jenis_koreksi,
                        no_pelunasan: no_pelunasan,

                    },
                    success: function(res) {
                        // console.log("COA response:", res); // debug
                        // alert('berhasil');
                        refresh();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert(jqXHR.responseText);
                    }
                });
            });


            function koreksi(id_summary, tipe_currency, tipe_koreksi) {

                $("#tambah_data").modal({
                    show: true,
                    backdrop: "static"
                });

                // $("#tambah_data").removeClass("modal fade lebar").addClass("modal fade lebar_mode");
                $("#tambah_data").removeClass('modal fade lebar_mode').addClass('modal fade lebar');
                $("#tambah_data .modal-dialog .modal-content .modal-body").addClass("add_batch");

                $(".tambah_data").html(
                    '<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>'
                );
                $(".modal-title").html("Koreksi <b>" + tipe_currency + "</b>");

                $.post("<?= base_url('accounting/pelunasanpiutang/get_view_koreksi') ?>", {
                    no_pelunasan: "<?= $list->no_pelunasan; ?>",
                    id: id_summary,
                    tipe_currency: tipe_currency,
                    tipe_koreksi: tipe_koreksi
                }, function(data) {
                    setTimeout(function() {
                        $(".tambah_data").html(data.data);
                        $("#btn-tambah").html("Simpan");

                        // ⬇️ re-bind format angka ke elemen hasil ajax
                        bindFormatAngka(document.querySelector("#tambah_data"));
                    }, 1000);
                });
                $('#tambah_data').on('hidden.bs.modal', function() {
                    // optional: reset isi modal supaya fresh setiap buka
                    $(".tambah_data").html("");
                });

            }

            $('#tambah_data').on('shown.bs.modal', function() {
                $(this).find('select.select2, select.select-coa, select.coa-select').each(function() {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2({
                            width: '100%',
                            dropdownParent: $('#tambah_data')
                        });
                    }
                });
            });

            $(document).on("click", ".detail-sj", function(e) {
                e.preventDefault();

                let data_sj = $(this).data("sj");
                detail_sj(data_sj);
            });

            function detail_sj(sj) {
                // tampilkan modal
                const $modal = $("#view_data");

                $modal.modal({
                    show: true,
                    backdrop: "static",
                });

                // tampilkan loading
                $("#view_data .view_body").html(`
                    <center>
                        <h5>
                            <img src="<?= base_url('dist/img/ajax-loader.gif') ?>" /><br>
                            Please Wait...
                        </h5>
                    </center>
                `);

                $(".modal-title").html(`No SJ : <b>${sj}</b>`);

                // ambil data via AJAX POST
                $.post("<?= base_url('accounting/pelunasanpiutang/get_view_sj') ?>", {
                        no_pelunasan: "<?= $list->no_pelunasan; ?>",
                        sj: sj,
                    })
                    .done(function(data) {
                        // tampilkan isi modal
                        setTimeout(function() {
                            $("#view_data .view_body").html(data.data || "<p>Tidak ada data.</p>");
                            // kalau kamu punya fungsi format angka:
                        }, 500);
                    })
                    .fail(function(xhr) {
                        $("#view_data .view_body").html(
                            "<p>" + (xhr.responseJSON?.error ?? "Terjadi kesalahan saat memuat data.") + "</p>"
                        );
                        console.error(xhr.responseText);
                    });


                // reset isi modal saat ditutup, tapi pastikan tidak mendaftarkan event berkali-kali
                $modal.off("hidden.bs.modal").on("hidden.bs.modal", function() {
                    $(".view_data").empty();
                });
            }


        })
    </script>
</body>

</html>