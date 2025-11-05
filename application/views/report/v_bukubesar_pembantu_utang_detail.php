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

        .style_space {
            white-space: nowrap !important;
            /* font-weight: 700; */
            background: #F0F0F0;
            border-top: 2px solid #ddd !important;
            border-bottom: 2px solid #ddd !important;
        }

        .ket-acc {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 300px;
            /* Sesuaikan dengan kebutuhan */
        }

        .resizable .resizer:hover {
            background-color: rgba(0, 0, 0, 0.1);
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

        .currency-radio-wrapper {
            display: table;
            height: 100%;
        }

        .currency-radio-wrapper .form-inline {
            display: table-cell;
            vertical-align: middle;
            padding-top: 0;
        }

        .currency-radio-wrapper .form-inline .radio-inline {
            padding-top: 0px;
        }
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
                        <h3 class="box-title"><b>Buku Besar Pembantu Utang Detail</b></h3>
                    </div>
                    <div class="box-body">

                        <form name="input" class="form-horizontal" role="form" method="POST" id="frm_periode">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <label>Supplier</label>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="form-control input-sm" name="partner" id="partner"></select>
                                        </div>
                                        <div class="col-md-2">
                                            <label>Currency</label>
                                        </div>
                                        <div class="col-md-4 currency-radio-wrapper">
                                            <div class="form-inline">
                                                <!-- <label class="radio-inline">
                                                    <input type="radio" name="currency" value="all" checked> All
                                                </label> -->
                                                <label class="radio-inline">
                                                    <input type="radio" name="currency" value="valas"> Valas
                                                </label>
                                                <label class="radio-inline">
                                                    <input type="radio" name="currency" value="rp" checked> Rp
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
                                            <label> </label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="checkbox" name="hidden_check" id="hidden_check" checked>
                                            Sembuyikan Data Kosong
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-generate" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> Generate</button>
                                    <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-excel" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-excel-o" style="color:green"></i> Excel</button>
                                    <!-- <button type="button" class="btn btn-sm btn-default" name="btn-generate" id="btn-pdf" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing..."> <i class="fa fa-file-pdf-o" style="color:red"></i> PDF</button> -->
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
                                                        <th class="style bb no">No. </th>
                                                        <th class='style bb' style="min-width: 80px; width:80px;">Tanggal</th>
                                                        <th class='style bb' style="min-width: 105px; width:105px;">No Bukti</th>
                                                        <!-- <th class='style bb' style="min-width: 100px; max-width: 220px; width:100px;">Uraian</th> -->
                                                        <th class='style bb' style="min-width: 200px">Uraian</th>
                                                        <th class='style bb' style="min-width: 150px; width:100px;">Debit</th>
                                                        <th class='style bb' style="min-width: 150px; width:100px;">Credit</th>
                                                        <th class='style bb' style="min-width: 150px; width:100px;">Saldo</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="7">Tidak ada Data</td>
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
            defaultDate: new Date().toLocaleString('en-US', {
                timeZone: 'Asia/Jakarta'
            }),
            format: 'D-MMMM-YYYY',
            ignoreReadonly: true,
            // maxDate: new Date(),
            //minDate : 
            //maxDate: new Date(),
            //startDate: StartDate,
        });

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


        var arr_filter = [];


        // btn generate
        $("#btn-generate").on('click', function() {

            var tgldari = $('#tgldari').val();
            var tglsampai = $('#tglsampai').val();
            var this_btn = $(this);

            var tgldari_2 = $('#tgldari').data("DateTimePicker").date();
            var tglsampai_2 = $('#tglsampai').data("DateTimePicker").date();
            var selectedCurrency = $('input[name="currency"]:checked').val();


            if (tgldari == '' || tglsampai == '') {
                alert_modal_warning('Periode Tanggal Harus diisi !');
            } else if (selectedCurrency == '' || selectedCurrency === 'undefined') {
                alert_modal_warning('Currency Harus dipilih !');
            } else if (tglsampai_2 < tgldari_2) {
                alert_modal_warning('Maaf, Tanggal Sampai tidak boleh kurang dari Tanggal Dari !');
            } else {
                arr_filter = [];
                process_bukubesar(this_btn);

            }
        });



        function formatNumber(n) {
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(n);
        }

        function process_bukubesar(this_btn) {
            var tgldari = $('#tgldari').val();
            var tglsampai = $('#tglsampai').val();
            var check_hidden = $("#hidden_check").is(':checked');
            var partner = $('#partner').val();
            var selectedCurrency = $('input[name="currency"]:checked').val();

            let slowProcessWarning = setTimeout(function() {
                please_wait(function(){});
            }, 5000); // 5 detik

            $("#example1_processing").css('display', ''); // show loading
            this_btn.button('loading');
            $.ajax({
                type: "POST",
                dataType: "JSON",
                url: "<?php echo site_url('report/bukubesarpembantuutangdetail/loadData') ?>",
                data: {
                    tgldari: tgldari,
                    tglsampai: tglsampai,
                    checkhidden: check_hidden,
                    partner: partner,
                    currency: selectedCurrency
                },
                success: function(data) {
                    clearTimeout(slowProcessWarning);
                    unblockUI(function () { });
                    if (data.status == 'failed') {
                        unblockUI(function() {
                            setTimeout(function() {
                                alert_notify(data.icon, data.message, data.type, function() {});
                            }, 1000);
                        });
                        this_btn.button('reset');
                    } else {
                        $("#example1 tbody").remove();
                        let no = 1;
                        let empty = true;
                        let debit = 0;
                        let credit = 0;
                        let s_awal = 0;
                        let s_akhir = 0;
                        let tbody = $("<tbody />");

                        arr_filter.push({
                            tgldari: tgldari,
                            tglsampai: tglsampai,
                            checkhidden: check_hidden,
                            partner: partner,
                            currency: selectedCurrency
                        });

                        $.each(data.record, function(key, value) {

                            empty = false;
                            var tr = $("<tr>").append(
                                // $("<td>").text(''),
                                $("<td class='text-left' colspan='4'>").html('<b>Supplier : </b>' + value.nama_partner),
                                $("<td colspan='3'>").text(''),
                            );
                            tbody.append(tr);

                            var tr2 = $("<tr>").append(
                                $("<td colspan=3>").text(no),
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


                                let linkUrl = '#';
                                if (value2.link === 'plh') {
                                    linkUrl = "<?php echo site_url('accounting/pelunasanhutang/edit/') ?>" + value2.no_bukti_ecr;
                                } else if (value2.link === 'inv') {
                                    linkUrl = "<?php echo site_url('purchase/invoice/edit/') ?>" + value2.id_bukti_ecr;
                                } else {
                                    linkUrl = "<?php echo site_url('purchase/debitnote/edit/') ?>" + value2.no_bukti_ecr;
                                }

                                // Buat kolom No Bukti dengan link (buka tab baru)
                                let noBuktiHtml = value2.no_bukti;
                                if (value2.link === 'plh' || value2.link === 'invr' || value2.link === 'inv') {
                                    noBuktiHtml = `<a href="${linkUrl}" target="_blank">${value2.no_bukti}</a>`;
                                }

                                var tr3 = $("<tr>").append(
                                    $("<td>").html(no++),
                                    $("<td align=''>").text(value2.tanggal),
                                    $("<td align=''>").html(noBuktiHtml),
                                    $("<td class='ket-acc'>").text(value2.uraian),
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
                                $("<td colspan='3' class='style_space'>").text(''),
                                $("<td class='style_space text-right'>").html('<b>Total : ' + value.nama_partner + '</b>'),
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

                        this_btn.button('reset');
                    }
                    $("#example1_processing").css('display', 'none'); // hidden loading

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert(jqXHR.responseText);
                    clearTimeout(slowProcessWarning);
                    unblockUI(function () { });
                    $("#example1_processing").css('display', 'none'); // hidden loading
                    this_btn.button('reset');
                }
            });

        }


        // ambil parameter dari URL
        function getUrlParameter(name) {
            name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
            var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
            var results = regex.exec(location.search);
            return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
        }

        $(document).ready(function() {
            const partner = getUrlParameter('partner'); // <— ganti dari id_partner ke partner
            const params = getUrlParameter('params');

            if (partner && params) {
                // decode JSON filter yang dikirim
                const filter = JSON.parse(params);

                // ambil parameter filter
                const tgldari = filter[0].tgldari;
                const tglsampai = filter[0].tglsampai;
                const checkhidden = filter[0].checkhidden;
                const currency = filter[0].currency;

                // 🔹 Set nilai ke form
                $('#tgldari').val(tgldari);
                $('#tglsampai').val(tglsampai);

                // set radio currency
                if (currency) {
                    $('input[name="currency"][value="' + currency + '"]').prop('checked', true);
                }

                // set checkbox
                $('#hidden_check').prop('checked', checkhidden === true || checkhidden === 'true');

                // ambil data supplier
                $.ajax({
                    url: "<?php echo base_url(); ?>accounting/pelunasanhutang/get_supplier_by_id",
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        id: partner
                    },
                    success: function(partnerData) {
                        if (partnerData) {
                            var option = new Option(partnerData.nama, partnerData.id, true, true);
                            $('#partner').append(option).trigger('change');

                            // setelah supplier terisi, panggil proses detail
                            process_bukubesar($('#btn-generate'));
                        }
                    }
                });
            }
        });

        // klik btn excel
        $('#btn-excel').click(function() {

            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu !');
            } else {

                $.ajax({
                    "type": 'POST',
                    "url": "<?php echo site_url('report/bukubesarpembantuutangdetail/export_excel') ?>",
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

        // klik btn print  pdf
        $(document).on('click', "#btn-pdf", function(e) {

            var arrStr = encodeURIComponent(JSON.stringify(arr_filter));
            if (arr_filter.length == 0) {
                alert_modal_warning('Generate Data terlebih dahulu !');
            } else {
                var url = '<?php echo base_url() ?>report/bukubesarpembantuutangdetail/export_pdf';
                window.open(url + '?params=' + arrStr, '_blank');
            }

        });
    </script>

</body>

</html>