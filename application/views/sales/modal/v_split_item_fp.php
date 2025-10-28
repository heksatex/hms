<tr style="background-color:#FFA500">
    <td style="width:70px;">
        <button type="button" data-toggle="tooltip" data-placement="top" title="Cancel" class="cancel-split btn-sm" onclick="cancelSplit(this)"><i style="color: black;" class="fa fa-trash"></i></button>
        <button type="button" data-toggle="tooltip" data-placement="top" title="Save" class="save-split btn btn-success btn-sm" onclick="saveSplit()"><i style="color: black;" class="fa fa-save"></i></button> 
    </td>
    <td>
        <input class="form-control input-sm" value="<?= $data->uraian ?>" id="uraian_split" readonly>
    </td>
    <td>
        <input class="form-control input-sm" value="<?= $data->warna ?>" id="warna_split" readonly>
    </td>
    <td>
        <input class="form-control input-sm" value="<?= $data->no_po ?>" id="no_po_split">
    </td>
    <td>
        <input class="form-control input-sm text-right" value="<?= $data->qty_lot ?>" id="qty_lot_split">
    </td>
    <td>
        <select class="form-control input-sm" style="width:100%" id="uom_lot_split">
            <?php
            foreach ($uomLot as $keys => $uoml) {
                ?>
                <option value="<?= $keys ?>" <?= ($keys === $data->lot) ? "selected" : "" ?> ><?= $uoml ?></option>
                <?php
            }
            ?>
        </select>
    </td>
    <td>
        <input class="form-control input-sm text-right" value="<?= $data->qty ?>" id="qty_split">
    </td>
    <td>
        <select class="form-control input-sm select2-coa" style="width:100%" id="no_acc_split">
            <option></option>
            <?php
            if ($data->no_acc !== "") {
                ?>
                <option value="<?= $data->no_acc ?>" selected><?= $data->coa_nama ?></option>
                <?php
            }
            ?>
        </select>
    </td>
    <td>
        <input id="ids" type="hidden" value="<?= $data->id ?>">
        <input class="form-control input-sm text-right" value="<?= number_format($data->harga,2,".",",") ?>" readonly>
    </td>
</tr>