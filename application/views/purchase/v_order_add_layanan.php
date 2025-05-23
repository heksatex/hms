
<tr>
    <td>
        <button type="button" class="btn btn-success btn-xs save-layanan width-btn" title="Simpan" data-toggle="tooltip"><i class="fa fa-save"></i></button>
    </td>
    <td>
        <button type="button" class="btn btn-danger btn-xs batal width-btn" title="Batal" data-toggle="tooltip"><i class="fa fa-trash"></i></button>
    </td>
    <td>
        <div class="form-group">
            <select class="form-control input-sm lay_kod_pro lay_kod_pro_<?= $index ?>" name="lay_kod_pro" id="lay_kod_pro" data-row="<?= $index ?>" style="width: 80%" required>
            </select>
            <input type="hidden" class="lay_nm_pro_<?= $index ?>" name="lay_nm_pro" id="lay_nm_pro">
            <input type="hidden" class="lay_warehouse_<?= $index ?>" name="lay_warehouse" id="lay_warehouse">
        </div>
    </td>
    <td>
        <div class="form-group">
            <input type="text" class="form-control input-sm lay_deskripsi_<?= $index ?>" name="lay_deskripsi" id="lay_deskripsi">
        </div>
    </td>
    <td>
        <input type="datetime-local" style="width: 70%" class="form-control lay_schedule_date_<?= $index ?>" name="lay_schedule_date" id="lay_schedule_date" value="<?= date("Y-m-d H:i:s") ?>">
    </td>
    <td>
        <div class="form-group">
            <div class="input-group" style="">
                <input type="text"  style="width: 70px;" class="form-control input-sm lay_qty_<?= $index ?>" name="lay_qty" id="lay_qty" required>
                <div class="input-group-addon" style="width: 60px;"><span class="lay_uom_beli_text_<?= $index ?>"></span></div>
            </div>
        </div>
        <input type="hidden" class="form-control input-sm lay_uom_beli lay_uom_beli_data_<?= $index ?>" name="lay_uom_beli" id="lay_uom_beli" readonly required>
        <input type="hidden" class="form-control input-sm lay_uom lay_uom_data_<?= $index ?>" name="lay_uom" id="lay_uom" readonly required>
        <input type="hidden" class="form-control input-sm lay_id_konversiuom lay_id_konversiuom_data_<?= $index ?>" name="lay_id_konversiuom" id="lay_id_konversiuom">
        <input type="hidden" class="form-control input-sm lay_nilai_konversiuom lay_nilai_konversiuom_data_<?= $index ?>" name="lay_nilai_konversiuom" id="lay_nilai_konversiuom" value="1">
    </td>
    <td>
        <div class="form-group">
            <input type="text" class="form-control input-sm"  name="lay_harga" id="lay_harga" required>
        </div>

    </td>
    <td>
        <div class="form-group text-right">
            <select class="form-control lay_tax lay_tax_<?= $index ?> input-xs"  style="width: 70%" data-row="<?= $index ?>" 
                    name="lay_tax" id="lay_tax">
                <option></option>
                <?php
                foreach ($taxss as $key => $taxs) {
                    ?>
                    <option value='<?= $taxs->id ?>' data-dpp_tax="<?= $taxs->dpp ?>" data-nilai_tax="<?= $taxs->amount ?>"><?= $taxs->nama ?></option>
                    <?php
                }
                ?>
            </select>
            <input type="hidden" class="form-control lay_amount_tax_<?= $index ?>"  name="lay_amount_tax" id="lay_amount_tax" value="0">
            <input type="hidden" class="form-control lay_dpp_tax_<?= $index ?>"  name="lay_dpp_tax" id="lay_dpp_tax" value="1">
        </div>
    </td>
    <td>
        <textarea name="lay_reff_note" id="lay_reff_note" class="form-control lay_reff_note_<?= $index ?>" rows="2" cols="10"></textarea>
    </td>
</tr>
<script>
    $(function () {



        $(".lay_kod_pro").select2({
            allowClear: true,
            placeholder: "Produk",
            ajax: {
                url: "<?= site_url('purchase/fpt/get_produk_layanan') ?>",
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
                            name: item.nama_produk,
                            warehouse: item.wrhs
                        });
                    });
                    return {
                        results: results
                    };
                }
            }
        });

        $(".lay_kod_pro").on("select2:select", function () {
            var row = $(this).attr("data-row");
            var nama_pro = $(".lay_kod_pro_" + row + " :selected").data().data.name;
            var warehouse = $(".lay_kod_pro_" + row + " :selected").data().data.warehouse;
            var uombeli = $(".lay_kod_pro_" + row + " :selected").data().data.uom_beli;
            var uombeliid = $(".lay_kod_pro_" + row + " :selected").data().data.uom_beli_id;
            var uom = $(".lay_kod_pro_" + row + " :selected").data().data.uom;
            var nilai = $(".lay_kod_pro_" + row + " :selected").data().data.nilai;

            $(".lay_nm_pro_" + row).val(nama_pro);
            $(".lay_deskripsi_" + row).val(nama_pro);
            $(".lay_warehouse_" + row).val(warehouse);
            $(".lay_uom_beli_data_" + row).val(uombeli);
            $(".lay_uom_data_" + row).val(uom);
            $(".lay_id_konversiuom_data_" + row).val(uombeliid);
            $(".lay_uom_beli_text_" + row).html(uombeli);
            $(".lay_nilai_konversiuom_data_" + row).val(nilai);
        });

        $(".lay_kod_pro").on("change", function () {
            var row = $(this).attr("data-row");
            $(".lay_nm_pro_" + row).val("");
            $(".lay_warehouse_" + row).val("");
            $(".lay_deskripsi_" + row).val("");
            $(".lay_uom_beli_data_" + row).val("");
            $(".lay_uom_data_" + row).val("");
            $(".lay_id_konversiuom_data_" + row).val("");
            $(".lay_uom_beli_text_" + row).html("");
            $(".lay_nilai_konversiuom_data_" + row).val(1);
        });


        $(".save-layanan").unbind("click").off("click").on("click", function () {
            const data = {
                kode_produk: $("#lay_kod_pro").val(),
                nama_produk: $("#lay_nm_pro").val(),
                warehouse: $("#lay_warehouse").val(),
                deskripsi: $("#lay_deskripsi").val(),
                schedule_date: $("#lay_schedule_date").val(),
                qty_beli: $("#lay_qty").val(),
                uom_qty_beli: $("#lay_uom_beli").val(),
                id_konversi: $("#lay_id_konversiuom").val(),
                uom: $("#lay_uom").val(),
                nilai: $("#lay_nilai_konversiuom").val(),
                tax: $("#lay_tax").val(),
                reff_note: $("#lay_reff_note").val(),
                amount_tax:$("#lay_amount_tax").val(),
                harga:$("#lay_harga").val(),
                dpp_tax:$("#lay_dpp_tax").val(),
                po:"<?= $po ?>"
            };
            saveLayanan(data);
        });

        $(".lay_tax").on("select2:select", function () {
            var row = $(this).attr("data-row");
            var tax = $(".lay_tax_" + row + " :selected").data().nilai_tax;
            var dpptax = $(".lay_tax_" + row + " :selected").data().dpp_tax;
            $(".lay_amount_tax_" + row).val(tax);
            $(".lay_dpp_tax_" + row).val(dpptax);
        });

        $(".lay_tax").on("change", function () {
            var row = $(this).attr("data-row");
            $(".lay_amount_tax_" + row).val("0");
            $(".lay_dpp_tax_" + row).val("1");

        });

        $(".batal").unbind("click").off("click").on("click", function () {
            $(this).closest("tr").remove();
            $(".add-layanan").show();
        });

        $(".lay_tax").select2({
            allowClear: true,
            placeholder: "Pilih"
        });
    });
</script>