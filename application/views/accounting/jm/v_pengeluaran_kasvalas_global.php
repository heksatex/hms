<tr>
    <td></td>
    <td>
        JURNAL <?= $jurnal ?? "" ?> 
    </td>
    <td>
    </td>
    <td>
    </td>
    <td>
    </td>
</tr>

<tr>
    <td></td>
    <td>
        <?= $periode ?>
    </td>
    <td>
    </td>
    <td>
    <td></td>
</td>
<td>
</td>
</tr>
<tr>
    <td>&nbsp;</td>
    <td>
    </td>
    <td>
    </td>
    <td></td>
    <td>
    </td>
    <td>
    </td>
</tr>
<?php
$totalDebit = 0;
$totalKredit = 0;
$kas = $data["kas_debit"] ?? [];
foreach ($kas as $key => $value) {
    $totalDebit += $value->nominals;
    ?>
    <tr>
        <td><?= ($key === 0) ? "1" : "" ?></td>
        <td><?= $value->nama_kkd ?></td>
        <td><?= $value->kode_coa_kkd ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td></td>
    </tr>
    <?php
}
$kas = $data["kas_kredit"] ?? [];
foreach ($kas as $key => $value) {
    $totalKredit += $value->nominals;
    ?>
    <tr>
        <td></td> 
        <td>&nbsp;<?= $value->nama ?></td>
        <td><?= $value->kode_coa ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>

    </tr>
    <?php
}
if (($totalDebit + $totalKredit) > 0) {
    ?>
    <tr>
        <td>&nbsp;</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right"><?= number_format($totalDebit, 2) ?></td>
        <td class="text-right"><?= number_format($totalKredit, 2) ?></td>

    </tr>
    <?php
}
?>