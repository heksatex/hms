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
<?php
if (isset($data["debit"][0])) {
    ?>
    <tr>
        <td>
            1
        </td>
        <td>
            KAS BESAR
        </td>
        <td>
            <?= $data["debit"][0]->km_kode_coa ?? "" ?>
        </td>
        <td class="text-right">
            <?= number_format(($data["debit"][0]->nominals ?? 0), 2) ?>
        </td>
        <td>
        </td>
    </tr>
    <?php
}
?>



<?php
$no = 1;
$totalKredit = 0;
foreach ($data["kredit"] as $key => $value) {
    $totalKredit += $value->nominals;
    ?>
    <tr>
        <td></td>
        <td>&nbsp;<?= $value->nama ?></td>
        <td><?= $value->kode_coa ?></td>
        <td></td>
        <td class="text-right">
            <?= number_format($value->nominals, 2) ?>
        </td>
    </tr>

    <?php
}
if ($totalKredit > 0) {
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
        <td class="text-right">
            <?= number_format(($data["debit"][0]->nominals ?? 0), 2) ?>
        </td>
        <td class="text-right">
            <?= number_format($totalKredit, 2) ?>
        </td>
    </tr>
    <?php
}
?>