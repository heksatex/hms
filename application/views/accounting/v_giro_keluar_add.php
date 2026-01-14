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
    <body class="hold-transition skin-black fixed sidebar-mini">
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
                        <form class="form-horizontal" method="POST" name="form-acc-girokeluar" id="form-acc-girokeluar" action="<?= base_url("accounting/girokeluar/simpan") ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title">Bukti Giro Keluar <span id="no"></h3>
                            </div>
                            <div class="box-body">
                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label">No ACC (Kredit)</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2 no_acc" name="no_acc">
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
                                                    <select class="form-control input-sm select2 partner" name="partner" id="partner">

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
                                                <div class="col-xs-4"><label class="form-label">Transaksi intern</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <select class="form-control input-sm select2 trx_intern" name="trx_intern" id="trx_intern">
                                                        <option></option>
                                                         <option value="tarik_tunai">Tarik Tunai</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
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
                                    <table class="table table-condesed table-hover rlstable  over" width="100%" id="girokeluar-detail" >
                                        <thead>             
                                        <th class="style no">No.</th>
                                        <th class="style" width="100">Bank</th>
                                        <th class="style" width="100px">No Rek</th>
                                        <th class="style" width="100px">No.Cek/BG</th>
                                        <th class="style" width="130px">Tgl JT</th>
                                        <th class="style" width="100px">No.Acc(Debet)</th>
                                        <th class="style" style="width:80px; text-align: right;" >Kurs</th>
                                        <th class="style" width="80px">Curr</th>
                                        <th class="style text-right" width="100px">Nominal</th>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td>
                                                    <button type="button" class="btn btn-success btn-sm btn-add-item"><i class="fa fa-plus-circle"></i></button>
                                                </td>
                                                <td colspan="7" class="text-right text-bold total-nominal">

                                                </td>
                                                <td class="text-bold">
                                                    <input type="text" name="total_nominal" id="total_nominal" class="form-control input-sm text-right"  value="0" readonly/>
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
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>
                <?php $this->load->view("admin/_partials/js.php") ?>
            </footer>
        </div>
        <template class="girokeluar-tmplt">
            <tr>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon nourut:nourut" style="border: none;"></span>
                        <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                    </div>
                </td>
                <td>
                    <input type="text" name="bank[]" class="form-control bank:nourut input-sm" required/>
                </td>
                <td>
                    <input type="text" name="norek[]" class="form-control input-sm"/>
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
                    <select class="form-control input-sm select2-coa" style="width:100%" name="kode_coa[]" required>
                        <option value=""></option>
                    </select>
                </td>
                <td>
                    <input type="text" name="kurs[]" value="1.00" class="form-control input-sm text-right" required/>
                </td>
                <td>
                    <select class="form-control input-sm select2 select2-curr" style="width:100%" name="curr[]" required>
                        <option value="1" selected>IDR</option>
                        <?php foreach ($curr as $key => $values) {
                            ?>
                            <option value="<?= $values->id ?>"><?= $values->currency ?></option>
                        <?php }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="nominal[]" pattern="^-?\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' class="form-control nominal nominal:nourut input-sm text-right" value="0"  required/>
                </td>
            </tr>
        </template>
        <script>
            $(document).ready(function () {
                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
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

            var no = 0;
            var transaksi = [];
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
            const lainInput = ((textbox, callback = function() {}) => {
                ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout"].forEach(function (event) {
                    textbox.addEventListener(event, function (e) {
//                        if (this.value !== "")
                        callback();
                    });
                });
            });
            $(function () {
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

                $(".no_acc").select2({
                    allowClear: true,
                    placeholder: "Pilih"
                });
                const calculateTotal = (() => {
                    var total = 0;
                    const elements = document.querySelectorAll('.nominal');
                    $.each(elements, function (idx, nomina) {
                        let ttl = $(nomina).val();
                        total += parseFloat(ttl.replace(/,/g, ""));
                    });
                    if (total === NaN) {
                        $("#total_nominal").val();
                        return;
                    }

                   $("#total_nominal").val(total);                formatCurrency($("#total_nominal"),"blur");
                });

                $(".btn-add-item").on("click", function (e) {
                    e.preventDefault();
                    no += 1;
                    var tmplt = $("template.girokeluar-tmplt");
                    var isi_tmplt = tmplt.html().replace(/:nourut/g, no);
                    $("#girokeluar-detail tbody").append(isi_tmplt);
                    setCoaItem();
                    setCurr();
                    setTglFormatDef(".tgl-def-format");
                    $(".nominal").on("blur", function () {
                        calculateTotal();
                    });
                    var tglHeader = $("#tanggal").val();
                    $(".tgljt" + no).val(tglHeader);
                    $(".nominal" + no).keyup(function (ev) {
                        if (ev.keyCode === 13) {
                            $(".btn-add-item").trigger("click");
                        }
                    });
                    $(".bank" + no).focus();
                    $(".nourut" + no).html(no);
                    setNominalCurrency();
                });

                $(".partner").on("change", function () {
                    var ttt = $(".partner").find(":selected");
                    $("#partner_name").val(ttt.text());
                    $("#lain_lain").val("");
                });

                $(".total-nominal").on("click", function () {
                    calculateTotal();
                });

                $("#girokeluar-detail").on("click", ".btn-rmv-item", function () {
                    $(this).closest("tr").remove();
                    calculateTotal();
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

                const formdo = document.forms.namedItem("form-acc-girokeluar");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    $(".total-nominal").trigger("click");
                    $("#trx").val(transaksi.join(","));
                    please_wait(function () {});
                    request("form-acc-girokeluar").then(
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

                const previewNo = ((tgl) => {
                    $.post("<?= base_url('accounting/bankkeluar/preview_no') ?>", {kode: "BGKH", tanggal: tgl}, function (data) {
                        $("#no").html(data.data);
                    });
                });

                previewNo($("#tanggal").val());
                $("#tanggal").on("blur", function () {
                    previewNo($("#tanggal").val());
                });

            });

        </script>
    </body>
</html>