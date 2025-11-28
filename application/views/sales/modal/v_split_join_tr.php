<?php
foreach ($data as $key => $value) {
    ?>
    <tr class="tr-<?= $value->id ?>">
        <td style="width: 50px">
            <a class="btn-rmv-item split-item" data-toggle="tooltip" data-ids="<?= $value->id ?>" data-original-title="Split"><i class="fa fa-copy"></i>&nbsp;</a>
            <input type="checkbox" class="btn-rmv-item join-item" data-toggle="tooltip" data-original-title="Join" value="<?= $value->id ?>">
            <input type="hidden" value="<?= $value->id ?>" name="detail_id[]">
        </td>
        <td>
            <input class="form-control input-sm  uraian edited-read uraian_<?= $value->id ?>" value="<?= $value->uraian ?>" name="uraian[]">
        </td>
        <td>
            <input class="form-control input-sm  warna edited-read warna_<?= $value->id ?>" value="<?= $value->warna ?>" name="warna[]">
        </td>
        <td>
            <textarea class="form-control no_po edited-read no_po_<?= $value->id ?>"  name="nopo[]"><?= $value->no_po ?></textarea>

        </td>
        <td class="text-right">
            <input type="text" name="qtylot[]" value="<?= "{$value->qty_lot}" ?>" 
                   class="form-control edited-read input-sm text-right qty-lot qty-lot_<?= $value->id ?>"/>
        </td>
        <td>
            <select class="form-control input-sm edited uomlot uomlot_<?= $value->id ?>" style="width:100%" name="uomlot[]">
                <?php
                foreach ($uomLot as $keys => $uoml) {
                    ?>
                    <option value="<?= $keys ?>" <?= ($keys === $value->lot) ? "selected" : "" ?> ><?= $uoml ?></option>
                    <?php
                }
                ?>
            </select>
        </td>
        <td class="text-right">
            <input type="text" name="qty[]" value="<?= "{$value->qty}" ?>" 
                   class="form-control input-sm edited-read text-right qty qty_<?= $value->id ?>"/>
        </td>
        <td>
            <select class="form-control input-sm edited uom uom_<?= $value->id ?>" style="width:100%" name="uom[]">
                <?php
                foreach ($uom as $keys => $uoms) {
                    ?>
                    <option value="<?= $uoms->short ?>" <?= ($uoms->short === $value->uom) ? "selected" : "" ?> ><?= $uoms->short ?></option>
                    <?php
                }
                ?>
            </select>
        </td>
        <td>
            <select class="form-control input-sm select2-coa edited noacc noacc_<?= $value->id ?>" style="width:100%" name="noacc[]">
                <option></option>
                <?php
                if ($value->no_acc !== "") {
                    ?>
                    <option value="<?= $value->no_acc ?>" selected><?= $value->no_acc ?></option>
                    <?php
                }
                ?>
            </select>
        </td>
        <td>
            <input type="text" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' name="harga[]" value="<?= number_format($value->harga, 4, ".", ",") ?>" 
                   class="form-control input-sm text-right edited-read harga harga_<?= $value->id ?>"/>
        </td>
        <td>
            <input type="text" pattern="^\d{1,3}(,\d{3})*(\.\d+)?$" data-type='currency' name="jumlah[]" value="<?= number_format($value->jumlah, 4, ".", ",") ?>" 
                   class="form-control input-sm text-right jumlah jumlah_<?= $value->id ?>" readonly/>
        </td>
    </tr>
    <?php
}
?>
