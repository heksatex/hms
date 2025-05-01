<tr>
    <td>
        <?= $data->kode_produk ." - ".$data->nama_produk ?>
    </td>
    <td>
        <?= $data->deskripsi ?>
    </td>
    <td>
        <?= $data->reff_note ?>
    </td>
    <td>
        <?= $data->account." ".$data->acc_nama ?>
    </td>
    <td>
        <input id="qty_dup" type="text" class="form-controll angka">
    </td>
    <td>
        <?= $data->uom_beli ?>
    </td>
    <td>
        <?= $data->tax_nama ?>
    </td>
    <td>
        <?= $data->diskon ?>
    </td>
    <td>
        <a><i class="fa fa-save"></i></a> &nbsp;
        <a><i class="fa fa-reset"></i></a>
    </td>
</tr>