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
    <td></td>
    <td>
        &nbsp;
    </td>
    <td>

    </td>
    <td>
    </td>
    <td>
    </td>
</tr>

<?php
$no = 1;
$totalKredit = 0;
foreach ($data["debit"] as $key => $value) {
    $totalKredit += $value->nominals;
    ?>
    <tr>
        <td><?= ($key === 0) ? "1" : "" ?></td>
        <td><?= $value->nama ?></td>
        <td><?= $value->kode_coa ?></td>
        <td class="text-right">
            <?= number_format($value->nominals, 2) ?>
        </td>
        <td></td>
    </tr>

    <?php
}
if (isset($data["kredit"][0])) {
    ?>
    <tr>
        <td>

        </td>
        <td>
            &nbsp;KAS BESAR
        </td>
        <td>
            <?= $data["kredit"][0]->km_kode_coa ?? "" ?>
        </td>
        <td>
        </td>
        <td class="text-right">
            <?= number_format(($data["kredit"][0]->nominals ?? 0), 2) ?>
        </td>
    </tr>
    <?php
}
?>
<?php
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
            <?= number_format(($data["kredit"][0]->nominals ?? 0), 2) ?>
        </td>
        <td class="text-right">
            <?= number_format($totalKredit, 2) ?>
        </td>
    </tr>
    <?php
}
?>