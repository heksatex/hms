<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            #btn-edit,#btn-cancel,#btn-print,.btn-save,#btn-confirm {
                display: none;
            }
            .select2-container--focus{
                border:  1px solid #66afe9;
            }
        </style>
        <?php $this->load->view("accounting/_v_style_group_select2.php") ?>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu-new.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar-new.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">

                </section>
                <section class="content">
                    <div class="box">
                        <form class="form-horizontal" method="POST" name="form-acc-bankkeluar" id="form-acc-bankkeluar" action="<?= base_url("accounting/bankkeluar/simpan") ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title">Bukti Bank Keluar</h3>
                            </div>
                            <div class="box-body">
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">No ACC (Kredit)</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2 no_acc" name="no_acc" style="width: 100%">
                                                        <option value=""></option>
                                                        <?php
                                                        foreach ($coa as $key => $value) {
                                                            ?>
                                                            <option value="<?= $value->kode_coa ?>"><?= "({$value->kode_coa}) - {$value->nama}" ?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Kepada</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="hidden" name="partner_name" id="partner_name"  class="form-control"/>
                                                    <select class="form-control input-sm select2 partner" name="partner" id="partner" style="width: 100%">

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Untuk Transaksi</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="transaksi" id="transaksi" class="form-control input-sm"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">Tanggal</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <div class="input-group tgl-def-format">
                                                        <input type="text" name="tanggal" id="tanggal" class="form-control input-sm" value="<?= date("Y-m-d") ?>" required/>
                                                        <span class="input-group-addon"><i class="fa fa-calendar"><span></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Lain-Lain</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="lain_lain" id="lain_lain" class="form-control input-sm"/>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">Jenis Transaksi</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2" name="jenis_transaksi" style="width: 100%">
                                                        <option></option>
                                                        <option value="transfer" selected>Transfer</option>
                                                        <option value="inkaso">Inkaso</option>
                                                        <option value="kliring">Kliring</option>
                                                        <option value="lain-lain">Lain - Lain</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <button type="button" class="btn btn-default btn-sm btn-add-item-bg"><span class="glyphicon glyphicon-book"></span>&nbsp; Dari Bukti Giro</button>
                                                </div>
                                                <div class="col-xs-4">
                                                    <button class="btn btn-default btn-sm btn-save"><span class="glyphicon glyphicon-save"></span> Simpan </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="box-footer">
                                <div class="col-md-12 table-responsive over">
                                    <table class="table table-condesed table-hover rlstable" width="100%" id="bankkeluar-detail" style="min-width: 105%">
                                        <thead>
                                        <th class="style" style="width: 2%">No.</th>
                                        <th class="style" style="width: 12%">Uraian</th>
                                        <th class="style" style="width: 10%">Bank</th>
                                        <th class="style" style="width: 10%">No Rek</th>
                                        <th class="style" style="width: 10%">No.Cek/BG</th>
                                        <th class="style" style="width: 12%">Tgl JT</th>
                                        <th class="style" style="width: 12%">Tgl Cair</th>
                                        <th class="style" style="width: 9%">No.Acc(Debet)</th>
                                        <th class="style" style="width: 5%; text-align: right;" >Kurs</th>
                                        <th class="style" style="width: 8%">Curr</th>
                                        <th class="style text-right" style="width: 15%">Nominal</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-success btn-sm btn-add-item"><i class="fa fa-plus-circle"></i></button>
                                                </td>
                                                <td colspan="9" class="text-right text-bold total-nominal">

                                                </td>
                                                <td class="text-bold">
                                                    <input type="text" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' name="total_nominal" id="total_nominal" class="form-control input-sm text-right" value="0" readonly/>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
            <template class="bankkeluar-tmplt">
                <tr>
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon nourut:nourut" style="border: none;"></span>
                            <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="uraian[]" class="form-control uraian:nourut input-sm"/>
                    </td>
                    <td>
                        <input type="text" name="bank[]" class="form-control bank:nourut input-sm" required/>
                    </td>
                    <td>
                        <input type="text" name="norek[]" class="form-control norek:nourut input-sm" required/>
                    </td>
                    <td>
                        <input type="text" name="nobg[]" class="form-control input-sm"/>
                    </td>
                    <td>
                        <div class="input-group tgl-def-format">
                            <input type="text" name="tgljt[]" class="form-control tgljt:nourut input-sm" value="<?= date("Y-m-d") ?>"/>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </td>
                    <td>
                        <div class="input-group tgl-def-format">
                            <input type="text" name="tglcair[]" class="form-control tglcair:nourut input-sm" value="<?= date("Y-m-d") ?>"/>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </td>
                    <td>
                        <select class="form-control input-sm select2-coa" style="width:100%" name="kode_coa[]" required>
                            <option value=""></option>
                            <?php
                            foreach ($coas as $key => $value) {
                                ?>
                                <option value="<?= $value->kode_coa ?>"><?= "{$value->kode_coa} - {$value->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" value="1.00" name="kurs[]" class="form-control input-sm text-right" required/>
                    </td>
                    <td>
                        <select class="form-control input-sm select2 select2-curr" style="width:100%" name="curr[]" required>
                            <option value="1" selected>IDR</option>

                        </select>
                    </td>
                    <td>
                        <input type="text" name="nominal[]" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' class="form-control input-sm nominal nominal:nourut text-right" value="0" required/>
                        <input type="hidden" name="giro_keluar_detail[]" class="form-control"/>
                    </td>
                </tr>
            </template>

            <template class="bankkeluar-tmplt-add">
                <tr class="list-new">
                    <td>
                        <div class="input-group">
                            <span class="input-group-addon nourut:nourut" style="border: none;"></span>
                            <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                        </div>
                    </td>
                    <td>
                        <input type="text" name="uraian[]" class="form-control uraian:nourut input-sm" value=""/>
                    </td>
                    <td>
                        <input type="text" name="bank[]" class="form-control input-sm bank:nourut" value="" required/>
                    </td>
                    <td>
                        <input type="text" name="norek[]" class="form-control input-sm norek:nourut" value="" required/>
                    </td>
                    <td>
                        <input type="text" name="nobg[]" class="form-control input-sm nobg:nourut" value=""/>
                    </td>
                    <td>
                        <div class="input-group tgl-def-format">
                            <input type="text" name="tgljt[]" class="form-control input-sm tgl:nourut" value="<?= date("Y-m-d") ?>"/>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </td>
                    <td>
                        <div class="input-group tgl-def-format">
                            <input type="text" name="tglcair[]" class="form-control input-sm tglcair:nourut" value="<?= date("Y-m-d") ?>"/>
                            <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                        </div>
                    </td>
                    <td>
                        <select class="form-control input-sm coa_:nourut" style="width:100%" name="kode_coa[]" required>
                            <option value=""></option>

                        </select>
                    </td>
                    <td>
                        <input type="text" name="kurs[]" value="1.00" class="form-control input-sm kurs:nourut text-right" required/>
                    </td>
                    <td>
                        <select class="form-control input-sm select2 select2-curr curr_:nourut" style="width:100%" name="curr[]" required>
                            <option value="1" selected>IDR</option>
                            <?php foreach ($curr as $key => $values) {
                                ?>
                                <option value="<?= $values->id ?>"><?= $values->currency ?></option>
                            <?php }
                            ?>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="nominal[]" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' class="form-control input-sm nominal text-right nominal:nourut" value="0" required/>
                        <input type="hidden" name="giro_keluar_detail[]" class="form-control gkd:nourut" value="0"/>
                    </td>
                </tr>
            </template>

            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>
                <?php $this->load->view("admin/_partials/js.php") ?>
            </footer>
        </div>
        <script>
            $(document).ready(function () {
                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
                });
            });
            var no = 0;

            const setCoaItem = ((klas = "select2-coa") => {
                $("." + klas).select2({
                    placeholder: "Pilih Coa",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/kaskeluar/get_coa",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    text: item.nama,
                                    children: [{
                                            id: item.kode_coa,
                                            text: item.kode_coa
                                        }]
                                });
                            });
                            return {
                                results: results
                            };
                        }
                    }
                });
            });

            const calculateTotal = (() => {
                var total = 0;
                const elements = document.querySelectorAll('.nominal');
                $.each(elements, function (idx, nomina) {
                    let ttl = $(nomina).val();
                    total += parseInt(ttl.replace(/,/g, ""));
                });
                if (total === NaN) {
                    $("#total_nominal").val();
                    return;
                }

                $("#total_nominal").val(total);
            });

            const setCurr = (() => {
                $(".select2-curr").select2({
                    placeholder: "Pilih",
                    allowClear: true,
//                    ajax: {
//                        dataType: 'JSON',
//                        type: "GET",
//                        url: "<?php echo base_url(); ?>accounting/kaskeluar/get_currency",
//                        delay: 250,
//                        data: function (params) {
//                            return{
//                                search: params.term
//                            };
//                        },
//                        processResults: function (data) {
//                            var results = [];
//                            $.each(data.data, function (index, item) {
//                                results.push({
//                                    id: item.id,
//                                    text: item.currency
//                                });
//                            });
//                            return {
//                                results: results
//                            };
//                        },
//                        error: function (xhr, ajaxOptions, thrownError) {
//                        }
//                    }
                });
            });
            var buktigiro = [];

            const lainInput = ((textbox, callback = function() {}) => {
                ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(function (event) {
                    textbox.addEventListener(event, function (e) {
                        //                        if (this.value !== "")
                        callback();
                    });
                });
            });

            const setNominalCurrency = (() => {
                $("input[data-type='currency']").on({
                    keyup: function () {
                        formatCurrency($(this));
                    },
                    drop: function () {
                        formatCurrency($(this));
                    },
                    blur: function () {
                        formatCurrency($(this), "blur");
                    }
                });
            });

            $(function () {
                const setBank = ((nourut) => {
                    var ttt = $(".no_acc").find(":selected");
                    var acc = ttt.text();
                    if (acc !== "") {
                        const texts = acc.split(" - ");
                        $(".bank" + nourut).val(texts?.[1]);
                        $(".norek" + nourut).val(texts?.[2]);

                    }
                });
                lainInput(document.getElementById("lain_lain"), function () {
                    if ($("#partner_name").val() !== "") {
                        $("#partner_name").val("");
                        $("#partner").val(null).trigger("change");
                    }
                });

                $("#btn-simpan").on("click", function (e) {
                    e.preventDefault();
                    $(".btn-save").trigger("click");
                });

                $(".btn-add-item-bg").on("click", function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text("Bukti Giro");
                    $.post("<?= base_url('accounting/bankkeluar/get_view_bukti_giro') ?>", {no: buktigiro}, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);
                            $("#btn-tambah").html("Tambahkan");
                        }, 1000);
                    });
                });

                $(".no_acc").select2({
                    allowClear: true,
                    placeholder: "Pilih"
                });

                $(".btn-add-item").on("click", function (e) {
                    no += 1;
                    e.preventDefault();
                    var tmplt = $("template.bankkeluar-tmplt");
                    var isi_tmplt = tmplt.html().replace(/:nourut/g, no);
                    $("#bankkeluar-detail tbody").append(isi_tmplt);
                    setCoaItem();
                    setCurr();
                    setTglFormatDef(".tgl-def-format");
                    $(".nominal").on("blur", function () {
                        calculateTotal();
                    });
                    setBank(no);
                    var tglHeader = $("#tanggal").val();
                    $(".tglcair" + no).val(tglHeader);
                    $(".tgljt" + no).val(tglHeader);

                    $(".nominal" + no).keyup(function (ev) {
                        if (ev.keyCode === 13) {
                            $(".btn-add-item").trigger("click");
                        }
                    });
                    $(".uraian" + no).focus();
                    $(".nourut" + no).html(no);
                    setNominalCurrency();
                });

                $("#bankkeluar-detail").on("click", ".btn-rmv-item", function () {
                    $(this).closest("tr").remove();
                });



                $(".partner").on("change", function () {
                    var ttt = $(".partner").find(":selected");
                    $("#partner_name").val(ttt.text());
                    $("#lain_lain").val("");
                });

                $("#partner").select2({
                    placeholder: "Pilih",
                    allowClear: true,
                    ajax: {
                        dataType: 'JSON',
                        type: "GET",
                        url: "<?php echo base_url(); ?>accounting/kaskeluar/get_partner",
                        delay: 250,
                        data: function (params) {
                            return{
                                search: params.term,
                                jenis: "supplier"
                            };
                        },
                        processResults: function (data) {
                            var results = [];
                            $.each(data.data, function (index, item) {
                                results.push({
                                    id: item.id,
                                    text: item.nama
                                });
                            });
                            return {
                                results: results
                            };
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                        }
                    }
                });

                $(".total-nominal").on("click", function () {
                    calculateTotal();
                });

                const formdo = document.forms.namedItem("form-acc-bankkeluar");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    $(".total-nominal").trigger("click");
                    please_wait(function () {});
                    request("form-acc-bankkeluar").then(
                            response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200)
                                    window.location.replace(response.data.url);
                            }
                    );
                    event.preventDefault();
                },
                        false
                        );

            });

            const addToTable = ((data, url) => {
                $.ajax({
                    url: "<?= base_url('accounting/bankkeluar/') ?>" + url,
                    type: "POST",
                    data: {
                        no: data
                    },
                    beforeSend: function (xhr) {
                        please_wait(function () {});
                    },
                    success: function (data) {
                        $.each(data.data, function (idx, row) {
                            no += 1;
                            if (!buktigiro.includes(row.id))
                                buktigiro.push(row.id);
                            var tmplt = $("template.bankkeluar-tmplt-add");
                            var isi_tmplt = tmplt.html().replace(/:no-/g, no)
                                    .replace(/:nourut/g, no);
                            $("#bankkeluar-detail tbody").append(isi_tmplt);
                            $(".bank" + no).val(row.bank);
                            $(".norek" + no).val(row.no_rek);
                            $(".nobg" + no).val(row.no_bg);
                            $(".tgljt" + no).val(row.tgl_jt);
                            $(".tglcair" + no).val(row.tgl_cair);
                            $(".kurs" + no).val(row.kurs);
                            $(".nominal" + no).val(Intl.NumberFormat("en-US", {minimumFractionDigits: 2, maximumFractionDigits: 2}).format(row.nominal));
                            $(".gkd" + no).val(row.id);
                            $(".nourut" + no).html(no);
                            setCoaItem("coa_" + no);
                            $(".coa_" + no).select2("trigger", "select", {
                                data: {id: row.kode_coa, text: row.kode_coa}
                            });
                            $("#transaksi").val(row.transinfo);
                            $(".nominal" + no).on("blur", function () {
                                calculateTotal();
                            });
                            $(".nominal" + no).keyup(function (ev) {
                                if (ev.keyCode === 13) {
                                    $(".btn-add-item").trigger("click");
                                }
                            });
                            if (row.partner_nama !== "") {
                                $("#lain_lain").val("");
                                $("#partner").select2("trigger", "select", {
                                    data: {id: row.partner_id, text: row.partner_nama}
                                });
                            } else {
                                $('#partner').val(null).trigger('change');
                                $("#lain_lain").val(row.lain);
                            }
                        });
                        setTglFormatDef(".tgl-def-format");
                    },
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {
                            setCurr();
                            $(".total-nominal").trigger("click");
                            setNominalCurrency();
                        }, 100);

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert_notify("fa fa-warning", jqXHR?.responseJSON?.message, "danger", function () {}, 500);
                    }
                });
            });

            $(document).on('focus', '.select2', function (e) {
                if (e.originalEvent) {
                    var s2element = $(this).siblings('select');
                    s2element.select2('open');

                    // Set focus back to select2 element on closing.
                    s2element.on('select2:closing', function (e) {
                        s2element.select2('focus');
                    });
                }
            });
        </script>
    </body>
</html>