<!DOCTYPE html>
<html lang="en">

<head>
    <?php $this->load->view("admin/_partials/head.php") ?>
    <style>
        .bb {
            border-bottom: 2px solid #ddd !important;
        }

        button[id="btn-simpan"] {
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

        #table-resume td:nth-child(1),
        #table-resume td:nth-child(2),
        #table-resume td:nth-child(3),
        #table-resume td:nth-child(4),
        #table-resume td:nth-child(5),
        #table-resume td:nth-child(7) {
            /* kolom tombol */
            width: 150px;
            min-width: 150px;
            max-width: 150px;
            white-space: nowrap;
        }

        #table-resume td:nth-child(6) {
            min-width: 200px;
            /* sesuaikan lebar */
            max-width: 250px;
            white-space: nowrap;
            /* agar select + small tetap sejajar */
        }

        .coa-info {
            display: block;
            min-height: 20px;
            /* tinggi tetap */
            line-height: 1.2;
            overflow: hidden;
            /* jangan biarkan mempengaruhi layout */
        }

        .coa-info small {
            display: block;
        }

        .warna-hutang {
            color:red
        }

        .warna-pelunasan {
            color:green
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
                                            <div class='input-group date' id='tgl_transaksi'>
                                                <input type='text' class="form-control input-sm" name="tanggal_transaksi" id="tanggal_transaksi" value="<?php echo date("Y-m-d", strtotime($list->tanggal_transaksi)); ?>" disabled />
                                                <span class="input-group-addon">
                                                    <span class="fa fa-calendar" disabled="true"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </div>

                            <!-- <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Supplier</label></div>
                                        <div class="col-xs-8">
                                            <input type="text" class="form-control input-sm" name="partner" id="partner" value="<?php echo $list->partner_nama; ?>" readonly="readonly">
                                        </div>
                                    </div>
                                </div>
                            </div> -->

                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Supplier</label></div>
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
                                                        <div class="col-md-4 col-lg-2">
                                                            <label>Invoice Akan dilunasi</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <button class="btn btn-default btn-sm <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'hidden' : ''; ?>" id="btn-inv" name="btn-inv" <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'disabled' : ''; ?>><i class='fa fa-file-text' style='color: orange'></i> Invoice (<span id='tinv'>0</span>)</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-condesed table-hover rlstable over" width="100%" id="table_invoice">
                                                    <thead>
                                                        <tr>
                                                            <th class="style bb no">No.</th>
                                                            <th class="style bb">No Inv</th>
                                                            <th class="style bb">Origin</th>
                                                            <th class="style bb">Tanggal</th>
                                                            <th class="style bb">Curr</th>
                                                            <th class="style bb text-right">Kurs</th>
                                                            <th class="style bb text-right">Total Utang (Rp)</th>
                                                            <th class="style bb text-right">Total Utang (Valas)</th>
                                                            <th class="style bb text-right">Sisa Utang (Rp)</th>
                                                            <th class="style bb text-right">Sisa Utang (Valas)</th>
                                                            <th class="style bb text-right">Pelunasan (Rp)</th>
                                                            <th class="style bb text-right">Pelunasan (Valas)</th>
                                                            <th class="style bb">Status Bayar</th>
                                                            <th class="style bb" style="min-width:65px;">#</th>
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
                                                        <div class="col-md-4 col-lg-2">
                                                            <label>Pelunasan</label>
                                                        </div>
                                                        <div class="col-md-7 col-lg-4">
                                                            <button class="btn btn-sm btn-default <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'hidden' : ''; ?>" id="btn-kas-bank" name="btn-kas-bank" <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'disabled' : ''; ?>><i class='fa fa-bank' style='color: green'></i> Kas Bank (<span id='tbk'>0</span>)</button>
                                                            <button class="btn btn-sm btn-default  <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'hidden' : ''; ?>" id="btn-uang-muka" name="btn-uang-muka" <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'disabled' : ''; ?>><i class='fa fa-money' style='color: blue'></i> Uang Muka (<span id='tum'>0</span>)</button>
                                                            <button class="btn btn-sm btn-default <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'hidden' : ''; ?>" id="btn-retur" name="btn-retur" <?php echo ($list->status == 'cancel' || $list->status == 'done') ? 'disabled' : ''; ?>><i class='fa fa-exchange' style='color: red'></i> Retur (<span id='tret'>0</span>)</button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-condesed table-hover rlstable over" width="100%" id="table_pelunasan">
                                                    <thead>
                                                        <tr>
                                                            <th class="style bb no">No.</th>
                                                            <th class="style bb">Metode</th>
                                                            <th class="style bb nowrap">No Bukti</th>
                                                            <th class="style bb">Tanggal</th>
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
                                            <div class="col-md-12 table-responsive over" >
                                                <div class="row" style="margin-bottom:5px;">
                                                    <div class="col-md-12">
                                                        <div class="col-md-2">
                                                            <label>Info</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-condesed table-hover rlstable over" width="100%" id="table-resume">
                                                    <thead>
                                                        <tr>
                                                            <th class="style bb no"></th>
                                                            <th class="style bb text-right">Total Hutang</th>
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

            get_total_by_partner("<?php echo $list->partner_id; ?>");

            $('#tgl_transaksi').datetimepicker({
                format: 'YYYY-MM-DD',
                ignoreReadonly: true,
                defaultDate: new Date()
            });


            $(document).on('click', '#btn-edit', function(e) {

                $("#btn-simpan").show(); //tampilkan btn-simpan
                $("#btn-edit").hide(); //sembuyikan btn-edit
                $("#btn-confirm").hide(); //sembuyikan btn-confirm
                $('#partner').prop('disabled', false);

                $("#btn-cancel").attr('id', 'btn-cancel-edit'); // ubah id btn-cancel jadi btn-cancel-edit
                $('#tanggal_transaksi').attr('disabled', false).attr('id', 'tanggal_transaksi');

                $('#btn-inv').prop('disabled', true);
                $('#btn-kas-bank').prop('disabled', true);
                $('#btn-uang-muka').prop('disabled', true);
                $('#btn-retur').prop('disabled', true);

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
                $('#btn-retur').prop('disabled', false);

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
                $("#tret").html('<li class="fa fa-spinner fa-spin"></i>');

                var partner = id;

                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "<?php echo base_url('accounting/pelunasanhutang/get_total_by_partner') ?>",
                    data: {
                        partner: partner
                    },
                    success: function(data) {

                        $("#tinv").html(data.total.total_invoice)
                        $("#tbk").html(data.total.total_kas_bank)
                        $("#tum").html(data.total.total_uang_muka)
                        $("#tret").html(data.total.total_retur)

                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(xhr.responseText);
                    }
                });
            }



            //select 2 supplier
            $('#partner').select2({
                allowClear: true,
                placeholder: "Select Supplier",
                ajax: {
                    dataType: 'JSON',
                    type: "POST",
                    url: "<?php echo base_url(); ?>accounting/pelunasanhutang/get_list_supplier",
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
                    url: baseUrl + 'accounting/pelunasanhutang/simpan',
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
                        $('#btn-retur').prop('disabled', false);

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
                            console.log(xhr)
                        }
                    }
                });
            }

            $('#btn-simpan').off('click').on('click', function() {
                if (!$('#partner').val()) {
                    alert_notify('fa fa-warning', 'Supplier Harus diisi !', 'danger');
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
                                url: '<?php echo base_url('accounting/pelunasanhutang/confirm_pelunasan_hutang') ?>',
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
                                url: '<?php echo base_url('accounting/pelunasanhutang/cancel_pelunasan_hutang') ?>',
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
                                    // var err = JSON.parse(xhr.responseText);
                                    // if (xhr.status == 401) {
                                    //     alert(err.message);
                                    // } else {
                                    //     alert(err.message);
                                    //     // alert("Gagal membatalkan !")
                                    // }

                                    let msg = xhr.responseJSON?.message || 'Terjadi kesalahan tidak diketahui';
                                    alert_notify('fa fa-warning', msg, 'danger', function() {});
                                    console.error('AJAX Error:', xhr);
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
                $('.modal-title').text('List Invoice');
                $.post("<?= base_url('accounting/pelunasanhutang/get_view_invoice') ?>", {
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
                $.post("<?= base_url('accounting/pelunasanhutang/get_view_kas_bank') ?>", {
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
                $.post("<?= base_url('accounting/pelunasanhutang/get_view_kas_bank') ?>", {
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


            $("#btn-retur").on("click", function(e) {
                e.preventDefault();
                $("#tambah_data").modal({
                    show: true,
                    backdrop: 'static'
                });
                $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $('.modal-title').text('List Retur');
                $("#tambah_data").removeClass('modal fade lebar_mode').addClass('modal fade lebar');
                $.post("<?= base_url('accounting/pelunasanhutang/get_view_retur') ?>", {
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

                $.post("<?= base_url('accounting/pelunasanhutang/get_view_edit_distribusi') ?>", {
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


            $(document).on("click", ".btn-delete-invoice", function(e) {
                let id = $(this).attr('data-id');
                let no_inv = $(this).attr('data-inv');
                delete_invoice(this, id, no_inv)
            });

            function delete_invoice(btn, id, no_inv) {

                var no_pelunasan = "<?php echo $list->no_pelunasan; ?>";

                bootbox.confirm({
                    message: "Apakah Anda ingin menghapus data Invoice " + no_inv + " ?",
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
                                url: '<?php echo base_url('accounting/pelunasanhutang/delete_pelunasan_hutang_invoice') ?>',
                                dataType: 'JSON',
                                data: {
                                    no_pelunasan: no_pelunasan,
                                    id: id,
                                    no_inv: no_inv,
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
                                url: '<?php echo base_url('accounting/pelunasanhutang/delete_pelunasan_hutang_metode') ?>',
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
                    url: "<?php echo site_url('accounting/pelunasanhutang/loadData') ?>",
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

                            // render cell koreksi (select + small + tombol)
                            let koreksiCell = renderKoreksiCell(value, status);
                            var tr = $("<tr>").append(
                                $("<td style='font-weight:bold;'>").text(value.tipe_currency),
                                $("<td class='text-right warna-hutang'>").text(formatNumber(value.total_hutang)),
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
                        initSelectKoreksi();
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
                    url: "<?php echo site_url('accounting/pelunasanhutang/loadData') ?>",
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
                        });

                        if (empty == true) {
                            var tr = $("<tr>").append($("<td colspan='9'>").text('Tidak ada Data'));
                            tbody.append(tr);
                        } else {
                            var trfoot = $("<tr class='style_total'>").append(
                                $("<td colspan='6' class='text-right'>").text('Total'),
                                $("<td class='text-right'>").text(formatNumber(total_rp)),
                                $("<td class='text-right'>").text(formatNumber(total_valas)),
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

            function loadInvoice() {

                $("#example1_processing").css('display', ''); // show loading
                var id = "<?php echo $list->id; ?>";
                var no_pelunasan = "<?php echo $list->no_pelunasan; ?>";
                $.ajax({
                    type: "POST",
                    dataType: "JSON",
                    url: "<?php echo site_url('accounting/pelunasanhutang/loadData') ?>",
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
                        let sum_total_hutang_rp = 0.00;
                        let sum_total_hutang_valas = 0.00;
                        let sum_sisa_hutang_rp = 0.00;
                        let sum_sisa_hutang_valas = 0.00;
                        let status = data.head.status;
                        $.each(data.record, function(key, value) {

                            empty = false;
                            if (status == 'draft') {
                                btn = '<button class="btn btn-primary btn-xs btn-distribusi" name="btn-distribusi" data-toggle="tootlip" title="Distribusi" data-id="' + value.id + '" data-inv="' + value.no_invoice + '"><i class="fa fa-edit"></i></button>  <button class="btn btn-danger btn-xs btn-delete-invoice" name="btn-delete-invoice" data-toggle="tooltip" title="Hapus" data-id="' + value.id + '" data-inv="' + value.no_invoice + '"><i class="fa fa-trash"></i></button>';
                            } else {
                                btn = '';
                            }

                            let statusHtml = $("<span>").addClass(value.status_color).text(value.status_text);

                            var tr = $("<tr>").append(
                                $("<td style=''>").text(no),
                                $("<td style=''>").text(value.no_invoice),
                                $("<td style=''>").text(value.origin),
                                $("<td style=''>").text(value.tanggal_invoice),
                                $("<td style=''>").text(value.currency),
                                $("<td class='text-right'>").text(value.kurs),
                                $("<td class='text-right'>").text(formatNumber(value.total_hutang_rp)),
                                $("<td class='text-right'>").text(formatNumber(value.total_hutang_valas)),
                                $("<td class='text-right'>").text(formatNumber(value.sisa_hutang_rp)),
                                $("<td class='text-right'>").text(formatNumber(value.sisa_hutang_valas)),
                                $("<td class='text-right'>").text(formatNumber(value.pelunasan_rp)),
                                $("<td class='text-right'>").text(formatNumber(value.pelunasan_valas)),
                                $("<td class='text-center'>").append(statusHtml),
                                $("<td class=''>").html(btn),
                            );

                            sum_pelunasan_rp = sum_pelunasan_rp + parseFloat(value.pelunasan_rp);
                            sum_pelunasan_valas = sum_pelunasan_valas + parseFloat(value.pelunasan_valas);

                            sum_total_hutang_rp = sum_total_hutang_rp + parseFloat(value.total_hutang_rp);
                            sum_total_hutang_valas = sum_total_hutang_valas + parseFloat(value.total_hutang_valas);
                            sum_sisa_hutang_rp = sum_sisa_hutang_rp + parseFloat(value.sisa_hutang_rp);
                            sum_sisa_hutang_valas = sum_sisa_hutang_valas + parseFloat(value.sisa_hutang_valas);
                            tbody.append(tr);
                            no++;
                        });

                        if (empty == true) {
                            var tr = $("<tr>").append($("<td colspan='14'>").text('Tidak ada Data'));
                            tbody.append(tr);
                        } else {
                            var trfoot = $("<tr class='style_total'>").append(
                                $("<td colspan='6' class='text-right'>").text('Total'),
                                $("<td class='text-right'>").text(formatNumber(sum_total_hutang_rp)),
                                $("<td class='text-right'>").text(formatNumber(sum_total_hutang_valas)),
                                $("<td class='text-right warna-hutang'>").text(formatNumber(sum_sisa_hutang_rp)),
                                $("<td class='text-right warna-hutang'>").text(formatNumber(sum_sisa_hutang_valas)),
                                $("<td class='text-right warna-pelunasan'>").text(formatNumber(sum_pelunasan_rp)),
                                $("<td class='text-right warna-pelunasan'>").text(formatNumber(sum_pelunasan_valas)),
                                $("<td colspan='2'>").html('&nbsp'),
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
                        url: "<?php echo base_url(); ?>accounting/pelunasanhutang/get_list_koreksi_select2",
                        data: function(params) {
                            return {
                                name: params.term // keyword pencarian
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
                // let gt_nm = getKoreksiOptionById(value.koreksi);
                let koreksiId = value.koreksi; // nilai default dari database
                let koreksiNama = value.koreksi_text;
                let hasCoa = value.koreksi_get_coa;

                // bikin select
                let $select = $('<select>', {

                    class: 'form-control input-sm select-koreksi',
                    name: 'koreksi',
                    style: 'width:100% !important;',
                    'data-id': value.id,
                    'data-default': koreksiId
                });

                if (koreksiId && koreksiNama) {
                    let option = new Option(koreksiNama, koreksiId, true, true);
                    $(option).data('get_coa', value.koreksi_get_coa);
                    $select.append(option);
                }

                // tempat info COA
                let $coaInfo = $('<div class="coa-info"><small></small></div>');
                if (status == 'draft') {
                    $wrapper = $('<div>').append($select).append($coaInfo);
                } else {
                    $wrapper = $('<div>').append(koreksiNama).append($coaInfo);
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
                        loadCoaInfo(selectedId, value.id, $coaInfo.find('small'));
                    } else if(hasCoa =='false'){
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


            function loadCoaInfo(koreksiId, summaryId, $target) {
                if (!koreksiId) {
                    $target.text("");
                    return;
                }

                $.ajax({
                    url: "<?php echo site_url('accounting/pelunasanhutang/getCoaByKoreksi') ?>",
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
                let jenis_koreksi = $(this).attr("data-tipe");
                let id_summary = $(this).attr("data-summary");;
                let nama_koreksi = $(this).attr('data-nm-koreksi');
                koreksi(id_summary, jenis_koreksi, nama_koreksi)
            });

            $(document).on("click", ".btn-hapus-koreksi", function(e) {
                let jenis_koreksi = $(this).attr("data-tipe");
                let id_summary = $(this).attr("data-summary");;
                // let nama_koreksi = $(this).attr('data-nm-koreksi');
                let no_pelunasan ="<?= $list->no_pelunasan; ?>";
                $.ajax({
                    url: "<?php echo site_url('accounting/pelunasanhutang/delete_koreksi') ?>",
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
                let no_pelunasan ="<?= $list->no_pelunasan; ?>";
                $.ajax({
                    url: "<?php echo site_url('accounting/pelunasanhutang/save_koreksi2') ?>",
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


            function koreksi(id_summary, jenis_koreksi, nama_koreksi) {

                $("#tambah_data").modal({
                    show: true,
                    backdrop: "static"
                });

                $("#tambah_data").removeClass("modal fade lebar").addClass("modal fade lebar_mode");
                $("#tambah_data .modal-dialog .modal-content .modal-body").addClass("add_batch");

                $(".tambah_data").html(
                    '<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>'
                );
                $(".modal-title").html("Koreksi <b>" + nama_koreksi + "</b>");

                $.post("<?= base_url('accounting/pelunasanhutang/get_view_koreksi') ?>", {
                    no_pelunasan: "<?= $list->no_pelunasan; ?>",
                    id: id_summary,
                    jenis_koreksi: jenis_koreksi
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


        })
    </script>
</body>

</html>