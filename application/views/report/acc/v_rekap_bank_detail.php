<?php
$total = 0;
foreach ($data as $key => $value) {
    $total += $value->nominal;
    ?>
    <tr>
        <td><?= $value->no_bk ?? $value->no_bm ?></td>
        <td class="text-capitalize"><?= $bank ?></td>
        <td><?= date("Y-m-d", strtotime($value->tanggal)) ?></td>
        <td><?= ($value->partner_nama === "") ? $value->lain2 : $value->partner_nama ?></td>
        <td><?= ($value->uraian === "") ? $value->transinfo : $value->uraian ?></td>
        <td class="text-right"><?= number_format($value->nominal, 2) ?></td>
    </tr>

    <?php
}
if ($total > 0) {
    ?>
    <tr>
        <td colspan="4"></td>
        <td> <strong>Total</strong></td>
        <td class="text-right"><?= number_format($total, 2) ?></td>
    </tr>
    <?php
}
?>