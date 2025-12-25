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
    <td>
    </td>
    <td>
    </td>
</tr>
<?php
$totalDebit = 0;
$totalKredit = 0;
$giro = $data["giro_debit"] ?? [];
foreach ($giro as $key => $value) {
    $totalDebit += $value->nominals;
    ?>
    <tr>
        <td><?= ($key === 0) ? "1" : "" ?></td>
        <td><?= $value->nama_gkd ?></td>
        <td><?= $value->kode_coa_gkd ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td></td>
    </tr>
    <?php
}
$giro = $data["giro_kredit"] ?? [];
foreach ($giro as $key => $value) {
    $totalKredit += $value->nominals;
    ?>
    <tr>
        <td></td>
        <td>&nbsp;<?= $value->nama ?></td>
        <td><?= $value->kode_coa ?></td>
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
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right"><?= number_format($totalDebit, 2) ?></td>
        <td class="text-right"><?= number_format($totalKredit, 2) ?></td>

    </tr>
    <?php
}
?>