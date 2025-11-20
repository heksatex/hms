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
                        <h3 class="box-title">Form Tambah</h3>
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
                                    <!-- <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                                        <div class="col-xs-8">
                                            <input type="text" class="form-control input-sm" name="tanggal_dibuat" id="tanggal_dibuat" readonly="readonly" value="<?php echo date("Y-m-d H:i:s"); ?>" />
                                        </div>
                                    </div> -->

                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4"><label>Tanggal Transaksi</label></div>
                                        <div class="col-xs-8 col-md-8">
                                            <div class='input-group date' id='tgl_transaksi'>
                                                <input type='text' class="form-control input-sm" name="tanggal_transaksi" id="tanggal_transaksi"/>
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
                                            <select class="form-control input-sm" name="partner" id="partner"  style="width:100% !important"></select>
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
                                                <table class="table table-condesed table-hover rlstable over" width="100%" id="table_batch"border="1">
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
                                                            <th class="style bb" style="min-width:65px;">#</th>
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
                                                            <button class="btn btn-default btn-sm" id="btn-retur" name="btn-retur"><i class='fa fa-exchange' style='color: red'></i> Retur (<span id='tret'>0</span>)</button>
                                                            <button class="btn btn-default btn-sm" id="btn-koreksi-valas" name="btn-koreksi-valas"><i class='fa fa-exchange' style='color: purple'></i> Koreksi Kurs Bulan </button>
                                                        </div>
                                                    </div>
                                                </div>
                                                <table class="table table-condesed table-hover rlstable over" width="100%" id="table_batch_items"border="1">
                                                    <thead>
                                                        <tr>
                                                            <th class="style bb no">No.</th>
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
                                                            <td colspan='13'>Tidak Ada Data</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
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

            $("#partner").on('change', function(e) {
                var id = $(this).val();
                if (id) {
                    get_total_by_partner(id);
                } else {
                    get_total_by_partner('No');
                }
            });


            function get_total_by_partner(partner) {

                $("#tinv").html('<li class="fa fa-spinner fa-spin"></i>');
                $("#tbk").html('<li class="fa fa-spinner fa-spin"></i>');
                $("#tum").html('<li class="fa fa-spinner fa-spin"></i>');
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

            $("#btn-simpan").unbind("click");
            $('#btn-simpan').click(function() {

                var kode = $('#kode').val();
                var partner = $('#partner').val();

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
                            tgl_transaksi : $("#tanggal_transaksi").val()
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





        })
    </script>
</body>

</html>