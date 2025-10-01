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
    <td>
    </td>
</tr>
<?php
$totalDebit = 0;
$totalKredit = 0;
foreach ($data["bank_debit"] as $key => $value) {
    $totalDebit += $value->nominals;
    ?>
    <tr>
        <td><?= ($key === 0) ? "1" : "" ?></td>
        <td><?= $value->nama ?></td>
        <td><?= $value->kode_coa ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td></td>
    </tr>
    <?php
}
foreach ($data["bank_kredit"] as $key => $value) {
    $totalKredit += $value->nominals;
    ?>
    <tr>
        <td></td>
        <td>&nbsp;<?= $value->nama_bmd ?></td>
        <td><?= $value->kode_coa_bmd ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>

    </tr>
    <?php
}
?>
<tr>
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
</tr>
<?php
foreach ($data["giro_debit"] as $key => $value) {
    $totalDebit += $value->nominals;
    ?>
    <tr>
        <td><?= ($key === 0) ? "2" : "" ?></td>
        <td><?= $value->nama ?></td>
        <td><?= $value->kode_coa ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td></td>
    </tr>
    <?php
}
foreach ($data["giro_kredit"] as $key => $value) {
    $totalKredit += $value->nominals;
    ?>
    <tr>
        <td></td>
        <td>&nbsp;<?= $value->nama_gmd ?></td>
        <td><?= $value->kode_coa_gmd ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>

    </tr>
    <?php
}
if ($totalDebit > 0) {
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