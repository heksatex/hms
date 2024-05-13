<table class="table">
    <thead>
    <th>No</th>
    <th>Bulk</th>
    <th>Corak -  Remark</th>
    <th>Qty</th>
</thead>
<tbody>
    <?php
    foreach ($data as $key => $value) {
        ?>
        <tr>
            <td><?= $key+1?></td>
            <td><?= $value->no_bulk ?></td>
            <td><?= $value->corak_remark .' - '.$value->corak_remark ?></td>
            <td><?= $value->qty ?></td>
        </tr>
        <?php
    }
    ?>
</tbody>
</table>