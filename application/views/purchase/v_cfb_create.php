
<form class="form-horizontal" method="POST" name="form-po" id="form-po" action="<?= base_url('purchase/requestforquotation/save') ?>">
    <div class="col-md-6 col-xs-12">
        <div class="field-group">
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="col-xs-4"><label class="form-label required">Supplier</label></div>
                    <div class="col-xs-8 col-md-8">
                        <select class="form-control input-sm select2" name="supplier" id="supplier" required>

                            <?php
                            $s = explode(":", $supp);
                            if (isset($s[1]) && $s[0] !== "null") {
                                echo "<option value='{$s[0]}' selected>{$s[1]}</option>";
                            } else {
                                echo " <option></option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php if ($jenis === "RFQ") { ?>
                <div class="form-group">
                    <div class="col-xs-12">
                        <div class="col-xs-4"><label class="form-label required">Tipe</label></div>
                        <div class="col-xs-8 col-md-8">
                            <select class="form-control input-sm select2" name="no_value" id="no_value" required>
                                <option value="0" selected>Value</option>
                                <option value="1">No Value</option>
                            </select>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="col-md-6 col-xs-12">
        <div class="field-group">
            <!--                <div class="form-group">
                                <div class="col-xs-12">
                                    <div class="col-xs-4"><label class="form-label required">Tanggal Order</label></div>
                                    <div class="col-xs-8 col-md-8">
                                        <input type="date" class="form-control input-sm" name="order_date" value="<?= ($tanggal === "") ? date("Y-m-d") : $tanggal ?>" required>
                                    </div>
                                </div>
                            </div>-->
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="col-xs-4"><label class="form-label" >Note</label></div>
                    <div class="col-xs-8 col-md-8">
                        <textarea type="text" class="form-control input-sm resize-ta" id="note" name="note"><?= $note ?? "" ?></textarea>
                    </div>                                    
                </div>
                <input type="hidden" id="jenis" name="jenis" value="RFQ">
                <button type="submit" id="btn_form_simpan" style="display: none"></button>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-xs-12">
            <table class="table">
                <thead>
                <th>
                    Kode CFB 
                </th>
                <th>
                    Kode Produk
                </th>
                <th>
                    Nama Produk
                </th>
                <th>
                    Qty
                </th>
                <th>
                    Qty Beli
                </th>
                <th>
                    Satuan Beli
                </th>
                </thead>
                <tbody>
                    <?php
                    foreach ($item as $key => $datas) {
//                            $datas = explode("#", $value);
//                            if (count($datas) > 1) {
                        $kodecfb = explode(".", $datas[1]);
                        ?>
                        <tr>
                            <td><input type="hidden" name="cfb[]" value="<?= $datas[1] ?>"><?= $kodecfb[0] ?></td>
                            <td><input type="hidden" name="kod_pro[]" value="<?= $datas[2] ?>"><?= $datas[2] ?></td>
                            <td><input type="hidden" name="nm_pro[]" value="<?= htmlentities($datas[3]) ?>"><?= $datas[3] ?>
                                <input type="hidden" name="reff_note[]" value="<?= htmlentities($datas[13]) ?>">
                            </td>
                            <td>
                                <input type="hidden" name="qty[]" value="<?= $datas[4] ?>">
                                <input type="hidden" class="uom_list_<?= $key ?>" name="uom[]" value="<?= $datas[5] ?>">
                                <input type="hidden" name="id_cfb[]" value="<?= $datas[0] ?>">
                                <input type="hidden" name="prio[]" value="<?= $datas[7] ?>">
                                <input type="hidden" name="harga[]" value="<?= $datas[8] ?>">
                                <input type="hidden" name="warehouse[]" value="<?= $datas[14] ?? "" ?>">
                                <input type="hidden" name="schedule_date[]" value="<?= $datas[15] ?? "" ?>">
                                <?= $datas[4] . " " . $datas[5] ?>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input class="form-control input-sm" name="qty_beli[]" value="<?= $datas[12] ?? 0 ?>" required>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <select class="form-control uom_beli uom_beli_data_<?= $key ?>" style="width: 80%" data-uom="<?= $datas[5] ?>" data-row="<?= $key ?>" name="id_konversiuom[]" required>
                                        <option></option>
                                        <?php
                                        if ($datas[9] !== null) {
                                            ?>
                                            <option value="<?= $datas[6] ?>" selected> <?= $datas[9] ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <input type="hidden" name="uom_beli[]" class="nama_uom_<?= $key ?>" value="">
                                    <br>
                                    <small class="form-text text-muted note_uom_beli_<?= $key ?>">
                                        <?= $datas[11] ?? "" ?>
                                    </small>
                                </div>
                            </td>
                        </tr>
                        <?php
//                            }
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>
</form>
<script>
    $(function () {
        var uomStock = "0";
        $(".uom_beli").select2({
            allowClear: true,
            placeholder: "Satuan Beli",
            ajax: {
                dataType: 'JSON',
                type: "GET",
                url: "<?php echo base_url(); ?>warehouse/produk/get_uom_beli",
                delay: 250,
                data: function (params) {
                    return{
                        nama: params.term,
                        ke: uomStock
                    };
                },
                processResults: function (data) {
                    var results = [];
                    $.each(data.data, function (index, item) {
                        results.push({
                            id: item.id,
                            text: item.text,
                            catatan: item.catatan
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

        $(".uom_beli").on("select2:open", function () {
            var row = $(this).attr("data-uom");
            uomStock = row;
        })

        $(".uom_beli").on("select2:select", function () {
            var row = $(this).attr("data-row");
            var selectedSelect2OptionSource = $(".uom_beli_data_" + row + " :selected").data().data.catatan;
            $(".note_uom_beli_" + row).html(selectedSelect2OptionSource);
            var text = $(".uom_beli_data_" + row + " :selected").text();
            $(".nama_uom_" + row).val(text.trim());
        });

        $(".uom_beli").on("change", function () {
            var row = $(this).attr("data-row");
            $(".note_uom_beli_" + row).html("");
            $(".nama_uom_" + row).val("");
        });


        $("#supplier").select2({
            allowClear: true,
            placeholder: "Supplier",
            ajax: {
                url: "<?= site_url('purchase/requestforquotation/get_supp') ?>",
                data: function (params) {
                    var query = {
                        search: params.term
                    }
                    return query;
                },
                processResults: function (data) {
                    return {
                        results: data.data
                    };
                }
            }
        });
//        $("#prioritas").select2({
//            allowClear: true,
//            placeholder: "Prioritas"
//        });
        $("#btn-tambah").unbind("click").off("click").on('click', function () {
            $("#btn_form_simpan").trigger("click");
        });
        const formdo = document.forms.namedItem("form-po");
        formdo.addEventListener(
                "submit",
                (event) => {
            please_wait(function () {});
            request("form-po").then(
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
</script>