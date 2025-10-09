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
        <td><?= $value->nama_bkd ?></td>
        <td><?= $value->kode_coa_bkd ?></td>
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
        <td>&nbsp;<?= $value->nama ?></td>
        <td><?= $value->kode_coa ?></td>
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
        <td><?= $value->nama_gkd ?></td>
        <td><?= $value->kode_coa_gkd ?></td>
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
        <td>&nbsp;<?= $value->nama ?></td>
        <td><?= $value->kode_coa ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>

    </tr>
    <?php
}
if(($totalDebit + $totalKredit) > 0)
{
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
    <td>&nbsp;</td>
    <td></td>
    <td></td>
    <td></td>
    <td class="text-right"><?= number_format($totalDebit, 2) ?></td>
    <td class="text-right"><?= number_format($totalKredit, 2) ?></td>
</tr>
<?php 
}
?>