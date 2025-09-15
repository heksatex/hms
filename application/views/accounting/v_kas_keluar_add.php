<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            #btn-edit,#btn-cancel,#btn-print,.btn-save {
                display: none;
            }
            .select2-container--focus{
                border:  1px solid #66afe9;
            }
        </style>
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
                        <form class="form-horizontal" method="POST" name="form-acc-kasadd" id="form-acc-kasadd" action="<?= base_url("accounting/kaskeluar/simpan") ?>">
                            <div class="box-header with-border">
                                <h3 class="box-title">Bukti Kas Keluar</h3>
                            </div>
                            <div class="box-body">

                                <div class="col-md-6 col-xs-12">
                                    <div class="field-group">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4"><label class="form-label required">No ACC (Kredit)</label></div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input name="coa_name" readonly id="coa_name" class="hide">
                                                    <select class="form-control input-sm select2 no_acc" name="no_acc" required>
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
                                                <div class="col-xs-4">
                                                    <button type="button" class="btn btn-default btn-sm btn-add-item-fpt"><span class="glyphicon glyphicon-th-list"></span> FPT</button>
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
                                    <table class="table table-condesed table-hover rlstable  over" width="100%" id="kaskeluar-detail" >
                                        <thead>                          

                                        <th class="style no">No.</th>
                                        <th class="style" width="200px">Uraian</th>
                                        <th class="style" width="50px">No ACC (Debet)</th>
                                        <th class="style" style="width:20px; text-align: right;" >Kurs</th>
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
                                                <td colspan="4" class="text-right text-bold total-nominal">

                                                </td>
                                                <td class="text-bold">
                                                    <input type="text" name="total_nominal" id="total_nominal" class="form-control text-right" readonly/>
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
        </div>
        <template class="kaskeluar-tmplt">
            <tr>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon nourut:nourut" style="border: none;"></span>
                        <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                    </div>
                </td>
                <td>
                    <input type="text" name="uraian[]" class="form-control uraian:nourut input-sm" required/>
                </td>
                <td>
                    <select class="form-control input-sm select2-coa" style="width:100%" name="kode_coa[]" required>
                        <option value=""></option>
                        <?php
                        foreach ($coas as $key => $value) {
                            ?>
                            <option value="<?= $value->kode_coa ?>"><?= "{$value->kode_coa}" ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="kurs[]" value="1.00" class="form-control input-sm" required/>
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
                    <input type="text" name="nominal[]" class="form-control nominal text-right input-sm" value="0" required/>
                    <input type="hidden"  value="0" name="po_detail[]" class="form-control"/>
                </td>
            </tr>
        </template>

        <template class="kaskeluar-tmplt-fpt">
            <tr>
                <td>
                    <div class="input-group">
                        <span class="input-group-addon nourut:nourut" style="border: none;"></span>
                        <button type="button" class="btn btn-danger btn-sm btn-rmv-item"><i class="fa fa-close"></i></button>
                    </div>
                </td>
                <td>
                    <input type="text" name="uraian[]" value=":uraian" class="form-control uraian uraian:nourut input-sm" required/>
                    <input class="fpt fpt:nourut" type="hidden">
                </td>
                <td>
                    <select class="form-control input-sm select2-coa select2-coa:nourut" style="width:100%" name="kode_coa[]" required>
                        <option value=""></option>
                        <?php
                        foreach ($coas as $key => $value) {
                            ?>
                            <option value="<?= $value->kode_coa ?>"><?= "{$value->kode_coa}" ?></option>
                            <?php
                        }
                        ?>
                    </select>
                </td>
                <td>
                    <input type="text" name="kurs[]" value="1.00" class="form-control input-sm kurs:nourut" required/>
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
                    <input type="text"  value="0" name="nominal[]" class="form-control nominal nominal:nourut text-right input-sm" required/>
                    <input type="hidden"  value="0" name="po_detail[]" class="form-control po:nourut"/>
                </td>
            </tr>
        </template>

        <footer class="main-footer">
            <?php $this->load->view("admin/_partials/modal.php") ?>
            <?php $this->load->view("admin/_partials/js.php") ?>
        </footer>

        <script>
            var no = 0;
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

                $(".select2").select2();
                $(".btn-add-item").on("click", function (e) {
                    e.preventDefault();
                    no += 1;
                    var tmplt = $("template.kaskeluar-tmplt");
                    var isi_tmplt = tmplt.html().replace(/:nourut/g, no);
                    $("#kaskeluar-detail tbody").append(isi_tmplt);
                    $(".select2-coa").select2();
                    setCurr();
                    $(".nominal").on("blur", function () {
                        calculateTotal();
                    });
                    $(".nominal").keyup(function (ev) {
                        if (ev.keyCode === 13) {
                            $(".btn-add-item").trigger("click");
                        }
                    });
                    $(".uraian" + no).focus();
                    $(".nourut" + no).html(no);
                });

                $("#kaskeluar-detail").on("click", ".btn-rmv-item", function () {
                    $(this).closest("tr").remove();
                    calculateTotal();
                    gentransaksi();
                });

                $(".partner").on("change", function () {
                    var ttt = $(".partner").find(":selected");
                    $("#lain_lain").val("");
                    $("#partner_name").val(ttt.text());
                });
                $(".total-nominal").on("click", function () {
                    calculateTotal();
                });

                $(".no_acc").on("change", function () {
                    var txt = $(".no_acc option:selected").text();
                    var txtt = txt.split(' - ');
                    $("#coa_name").val(txtt.at(-1));
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

                const formdo = document.forms.namedItem("form-acc-kasadd");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    $(".total-nominal").trigger("click");
                    request("form-acc-kasadd").then(
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

                $(".btn-add-item-fpt").on('click', function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text('List FPT');
                    var trx = $("#transaksi").val();
                    $.post("<?= base_url('accounting/kaskeluar/get_view_fpt') ?>", {trx: trx}, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);
                            $("#btn-tambah").html("Tambahkan");
                        }, 1000);
                    });
                    $('#tambah_data').on('hidden.bs.modal', function () {

                    });
                });

                const calculateTotal = (() => {
                    var total = 0;
                    const elements = document.querySelectorAll('.nominal');

                    $.each(elements, function (idx, nomina) {
                        let ttl = $(nomina).val();
                        total += parseInt(ttl);
                    });
                    if (total === NaN) {
                        $("#total_nominal").val();
                        return;
                    }

                    $("#total_nominal").val(total);
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

            });

            var transaksi = [];
            const gentransaksi = (() => {
                transaksi = [];
                $('.fpt').each(function () {
                    if (!transaksi.includes(this.value))
                        transaksi.push(this.value);
                });
                $("#transaksi").val(transaksi.join(","));
            });
            const addToTable = ((data) => {
                $.ajax({
                    url: "<?= base_url('accounting/kaskeluar/add_data_from_fpt') ?>",
                    type: "POST",
                    data: {
                        no: data
                    },
                    beforeSend: function (xhr) {
                        please_wait(function () {});
                    },
                    success: function (data) {
                        $.each(data.data, function (idx, row) {
                            var notes = "";
                            if (row.reff_note !== "")
                                notes = " - " + row.reff_note;
                            no += 1;
                            var tmplt = $("template.kaskeluar-tmplt-fpt");
                            var isi_tmplt = tmplt.html().replace(/:nourut/g, no);
                            $("#kaskeluar-detail tbody").append(isi_tmplt);
                            $(".po" + no).val(row.id);
                            $(".uraian" + no).val(row.deskripsi + notes);
                            $(".nominal" + no).val(row.total);
                            $(".kurs" + no).val(row.nilai_currency);
                            $(".nourut" + no).html(no);
                            $(".fpt" + no).val(row.no_po);
                            gentransaksi();
                        });
                    },
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {
                            setCurr();
                            $(".total-nominal").trigger("click");
                            $(".select2-coa").select2();
                        }, 100);

                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        alert_notify("fa fa-warning", jqXHR?.responseJSON?.message, "danger", function () {}, 500);
                    }
                });
            });

            $(document).ready(function () {
                $(window).keydown(function (event) {
                    if (event.keyCode === 13) {
                        event.preventDefault();
                        return false;
                    }
                });
            });
        </script>
    </body>
</html>