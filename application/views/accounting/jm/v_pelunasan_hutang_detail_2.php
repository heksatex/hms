<?php
$grandTotal = 0;
$bank = $data["bank_debit"] ?? [];
$total = 0;
foreach ($bank as $key => $value) {
    $grandTotal += $value->nominals;
    $total += $value->nominals;
    ?>
    <tr>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->no_bk ?></td>
        <td title="<?= $value->uraian ?>"><?= substr($value->uraian, 0, 50) ?></td>
        <td><?= $value->partner ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td><?= $value->kode_coa_bkd ?></td>
        <td><?= $value->nama_bkd ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
    </tr>
    <?php
    if (isset($bank[$key + 1])) {
        if ($value->kode_coa_bkd !== $bank[$key + 1]->kode_coa_bkd) {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><?= number_format($total, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama_bkd} Total" ?></td>
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
            <td><?= "{$value->nama_bkd} Total" ?></td>
            <td class="text-right"><?= number_format($total, 2) ?></td>
        </tr>
        <?php
    }
}
?>
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
$giro = $data["giro_debit"] ?? [];
foreach ($giro as $key => $value) {
    $grandTotal += $value->nominals;
    $total += $value->nominals;
    ?>
    <tr>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->no_gk ?></td>
        <td title="<?= $value->uraian ?>"><?= substr($value->uraian, 0, 50) ?></td>
        <td><?= $value->partner ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td><?= $value->kode_coa_gkd ?></td>
        <td><?= $value->nama_gkd ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
    </tr>
    <?php
    if (isset($giro[$key + 1])) {
        if ($value->kode_coa_gkd !== $giro[$key + 1]->kode_coa_gkd) {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><?= number_format($total, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama_gkd} Total" ?></td>
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
            <td><?= "{$value->nama_gkd} Total" ?></td>
            <td class="text-right"><?= number_format($total, 2) ?></td>
        </tr>
        <?php
    }
}
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