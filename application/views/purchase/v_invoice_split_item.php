<tr style="background-color:#e063da">
    <td>
        <a data-toggle="tooltip" data-placement="top" title="Save" class="save-split" onclick="saveSplit()"><i style="color: black;" class="fa fa-save"></i></a> &nbsp;
        <a data-toggle="tooltip" data-placement="top" title="Cancel" class="cancel-split" onclick="cancelSplit(this)"><i style="color: black;" class="fa fa-undo"></i></a>
    </td>
    <td></td>
    <td>
        <strong><?= $data->kode_produk . " - " . $data->nama_produk ?></strong>
    </td>
    <td>
        <?= $data->reff_note ?>
    </td>
    <td>
        
    </td>
    <td>
        <input id="qty_dup" type="text" class="form-control">
        <input id="id" type="hidden" value="<?= $id ?>">
        <input id="ids" type="hidden" value="<?= $data->id ?>">
    </td>
    <td>
        <?= $data->uom_beli ?>
    </td>
    <td>
        <?= number_format($data->harga_satuan,4)?>
    </td>
    <td>
        <?= $data->tax_nama ?>
    </td>
    <td>
        <?= $data->diskon ?>
    </td>
</tr>