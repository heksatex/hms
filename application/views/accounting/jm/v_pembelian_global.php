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
foreach ($data["debit"] as $key => $value) {
    $totalDebit += $value->nominals;
    ?>
    <tr>
        <td><?= ($key === 0) ? "1" : "" ?></td>
        <td><?= $value->nama_coa ?></td>
        <td><?= $value->kode_coa ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td></td>
    </tr>
    <?php
}
foreach ($data["kredit"] as $key => $value) {
    $totalKredit += $value->nominals;
    ?>
    <tr>
        <td></td>
        <td>&nbsp;<?= $value->nama_coa ?></td>
        <td><?= $value->kode_coa ?></td>
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
</tr>
<?php
if ($totalDebit > 0) {
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