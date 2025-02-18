<table class="table">
    <thead>
    <th>
        Invoice
    </th>
    <th>
        Inv Supplier
    </th>
    <th>
        Tanggal Inv Supplier
    </th>
    <th>
        No SJ Supplier
    </th>
    <th>
        Status
    </th>
</thead>
<tbody>
    <?php
    foreach ($inv as $key => $value) {
        ?>
        <tr>
            <td>
                <a href="<?= site_url("purchase/invoice/edit/").encrypt_url($value->id) ?>" target="_blank" ><?= $value->no_invoice ?></a>
            </td>
            <td>
                <?= $value->no_invoice_supp ?>
            </td>
            <td>
                <?= $value->tanggal_invoice_supp ?>
            </td>
            <td>
                <?= $value->no_sj_supp ?>
            </td>
            <td>
                <?= $value->status ?>
            </td>
        </tr>
        <?php
    }
    ?>
</tbody>
</table>