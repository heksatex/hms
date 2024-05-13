<table class="table">
    <thead>
    <th>No</th>
    <th>Bulk</th>
    <th>Barcode</th>
    <th>Corak -  Remark</th>
    <th>Lebar Jadi</th>
    <th>Qty</th>
</thead>
<tbody>
    <?php
    foreach ($data as $key => $value) {
        ?>
        <tr>
            <td><?= $key+1?></td>
            <td><?= $value->no_bulk ?></td>
            <td><?= $value->barcode_id ?></td>
            <td><?= $value->corak_remark .' - '.$value->corak_remark ?></td>
            <td><?= $value->lebar_jadi.' '.$value->uom_lebar_jadi ?></td>
            <td><?= $value->qty.' '.$value->uom ?></td>
        </tr>
        <?php
    }
    ?>
</tbody>
</table>