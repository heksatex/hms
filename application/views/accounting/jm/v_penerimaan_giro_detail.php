<?php
$grandTotal = 0;
$giro = $data["giro_debit"] ?? [];
$total = 0;
foreach ($giro as $key => $value) {
    $grandTotal += $value->nominals;
    $total += $value->nominals;
    ?>
    <tr>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->no_gm ?></td>
        <td title="<?= $value->uraian ?>"><?= substr($value->uraian, 0, 50) ?></td>
        <td><?= $value->partner ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td><?= $value->kode_coa ?></td>
        <td><?= $value->nama ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
    </tr>
    <?php
    if (isset($giro[$key + 1])) {
        if ($value->kode_coa !== $giro[$key + 1]->kode_coa) {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><?= number_format($total, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama} Total" ?></td>
                <td class="text-right"><?= number_format($total, 2) ?></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php
            $total = 0;
        }
    } else {
        ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><?= number_format($total, 2) ?></td>
            <td></td>
            <td><?= "{$value->nama} Total" ?></td>
            <td class="text-right"><?= number_format($total, 2) ?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <?php
    }
}
?>
<?php
if ($grandTotal > 0) {
    ?>
    <tr>
        <td>&nbsp;</td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right"></td>
        <td></td>
        <td></td>
        <td class="text-right"></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right"><?= number_format($grandTotal, 2) ?></td>
        <td></td>
        <td></td>
        <td class="text-right"><?= number_format($grandTotal, 2) ?></td>
    </tr>
    <?php
}