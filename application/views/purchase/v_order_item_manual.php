<tr>
    <td style="width: 200px">
        <div class="form-group">
            <select class="form-control input-sm select2 kod_pro kod_pro_<?= $index ?>" name="kod_pro[]" data-row="<?= $index ?>" style="width: 80%" required>
            </select>
            <input type="hidden" class="nm_pro_<?= $index ?>" name="nm_pro[]">
        </div>
    </td>
    <td style="width: 60px">
        <div class="form-group">
            <input type="text" class="form-control input-sm qty_<?= $index ?>" name="qty[]" required readonly>
        </div>
    </td>
    <td style="width: 80px">
        <div class="form-group">
            <input type="text" class="form-control input-sm uom_<?= $index ?>" name="uom[]" required readonly>
<!--            <select class="form-control input-sm select2 uom"  data-row="<?= $index ?>" style="width: 70%"  name="uom[]" required>
                <option></option>
            <?php
            foreach ($uom_jual as $key => $value) {
                ?>
                                            <option value="<?= $value->short ?>"><?= $value->short ?></option>
                <?php
            }
            ?>
            </select>-->
        </div>
    </td>
    <td style="width: 60px">
        <div class="form-group">
            <input class="form-control input-sm qty_beli_<?= $index ?>" type="text" name="qty_beli[]" required>
        </div>
    </td>
    <td style="width: 80px">
        <div class="form-group">
<!--            <select class="form-control input-sm select2 uom_beli uom_beli_data_<?= $index ?>" data-row="<?= $index ?>" style="width: 70%" name="id_konversiuom[]" disabled>
            </select>-->
            <input type="hidden" name="id_konversiuom[]" class="id_konversiuom_<?= $index ?>">
            <input type="text" class="nama_uom_<?= $index ?> form-control input-sm" name="uom_beli[]" required readonly>
            <input type="hidden" name="prio[]" value="normal">
            <input type="hidden" name="nilai[]" class="nilai nilai_<?= $index ?>">
            <br>
            <small class="form-text text-muted note_uom_beli note_uom_beli_<?= $index ?>">

            </small>
        </div>
    </td>
    <td style="width: 80px">
          <input type="datetime-local" class="form-control schedule_date_<?= $index ?>" name="schedule_date[]">
    </td>
    <td style="width: 100px">
        <select class="form-control input-sm select2" name="warehouse[]" style="width: 100%" id="warehouse" required>
            <?php foreach ($warehouse as $key => $value) {
                ?>
                <option value="<?= $value->kode ?>"><?= $value->nama ?></option>
            <?php }
            ?>
        </select>
    </td>
    <td>
        <textarea name="reff_note[]" class="form-control reff_note_<?= $index ?>" rows="2" cols="10"></textarea>
    </td>
    <td style="width: 20px">
        &nbsp;
        <button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip"><i class="fa fa-trash"></i></button>
        <input type="hidden" name="cfb[]">
        <input type="hidden" class="form-control input-sm" name="harga[]" required>
    </td>
</tr>
<script>
    $(function () {
        $(document).unbind("click").off("click").on("click", ".batal", function () {
            $(this).closest("tr").remove();
        });

        $("#warehouse").select2({
            allowClear: true,
            placeholder: "Gudang Tujuan"
        });

        $(".uom").select2({
            allowClear: true,
            placeholder: "Satuan Stok"
        });
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
                        ke: $(".uom_<?= $index ?>").val()
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
                    alert_notify("fa fa-warning", xhr.responseJSON.message, "danger", function () {}, 500);
                }
            }
        });

        $(".kod_pro").on("select2:select", function () {
            var row = $(this).attr("data-row");
            var selectedSelect2OptionSource = $(".kod_pro_" + row + " :selected").data().data.uom;
            var dari = $(".kod_pro_" + row + " :selected").data().data.uom_beli;
            var dari_id = $(".kod_pro_" + row + " :selected").data().data.uom_beli_id;
            var nilai = $(".kod_pro_" + row + " :selected").data().data.nilai;

            $(".uom_" + row).val(selectedSelect2OptionSource);
            if (dari === "") {
                dari = selectedSelect2OptionSource;
            }
            $(".nama_uom_" + row).val(dari);
            $(".id_konversiuom_" + row).val(dari_id);
            $(".note_uom_beli_" + row).html("1 : " + nilai);
            $(".nilai_" + row).val(nilai);

            var text = $(".kod_pro_" + row + " :selected").data().data.name;
            $(".nm_pro_" + row).val(text);
        });

        $(".kod_pro").on("change", function () {
            var row = $(this).attr("data-row");
            $(".nm_pro_" + row).val("");
            $(".uom_" + row).val("");
            $(".nama_uom_" + row).val("");
            $(".id_konversiuom_" + row).val("");
            $(".note_uom_beli_" + row).html("");
            $(".nilai_" + row).val("");
//            $(".uom_beli_data_"+ row).val('').trigger('change');
        });

//        $(".uom_beli").on("select2:select", function () {
//            var row = $(this).attr("data-row");
//            var selectedSelect2OptionSource = $(".uom_beli_data_" + row + " :selected").data().data.catatan;
//            $(".note_uom_beli_" + row).html(selectedSelect2OptionSource);
//            var text = $(".uom_beli_data_" + row + " :selected").text();
//            text = text.text.trim();
//            var ttext = text.split(" | ");
//            $(".nama_uom_" + row).val(ttext[0]);
//        });

//        $(".uom_beli").on("change", function () {
//            var row = $(this).attr("data-row");
//            $(".note_uom_beli_" + row).html("");
//            $(".nama_uom_" + row).val("");
//        });

        $(".kod_pro").select2({
            allowClear: true,
            placeholder: "Produk",
            ajax: {
                url: "<?= site_url('purchase/requestforquotation/get_produk') ?>",
                data: function (params) {
                    var query = {
                        search: params.term
                    };
                    return query;
                },
                processResults: function (data) {
                    var results = [];
                    $.each(data.data, function (index, item) {
                        results.push({
                            id: item.kode_produk,
                            text: item.kode_produk + " | " + item.nama_produk,
                            uom: item.uom,
                            uom_beli: item.dari,
                            uom_beli_id: item.dari_id,
                            nilai: item.nilai,
                            name: item.nama_produk
                        });
                    });
                    return {
                        results: results
                    };
                }
            }
        });

    })
</script>