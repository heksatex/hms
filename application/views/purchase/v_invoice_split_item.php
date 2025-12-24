<tr style="background-color:#FFA500">
    <td>
        <button type="button" data-toggle="tooltip" data-placement="top" title="Cancel" class="cancel-split btn-sm" onclick="cancelSplit(this)"><i style="color: black;" class="fa fa-trash"></i></button>
                </td>
                <td>
                    <button type="button" data-toggle="tooltip" data-placement="top" title="Save" class="save-split btn btn-success btn-sm" onclick="saveSplit()"><i style="color: black;" class="fa fa-save"></i></button> 
                </td>
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
                    <?= number_format($data->harga_satuan, 4) ?>
                </td>
                <td>
                    <?= $data->tax_nama ?>
                </td>
                <td>
                    <?= $data->diskon ?>
                </td>
                </tr>