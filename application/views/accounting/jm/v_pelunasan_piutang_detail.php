<?php
$grandTotal = 0;
$bank = $data["bank_kredit"] ?? [];
$total = 0;
foreach ($bank as $key => $value) {
    $grandTotal += $value->nominals;
    $total += $value->nominals;
    ?>
    <tr>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->no_bm ?></td>
        <td title="<?= $value->uraian ?>"><?= substr($value->uraian, 0, 50) ?></td>
        <td><?= $value->partner ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td><?= $value->kode_coa_bmd ?></td>
        <td><?= $value->nama_bmd ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
    </tr>
    <?php
    if (isset($bank[$key + 1])) {
        if ($value->kode_coa_bmd !== $bank[$key + 1]->kode_coa_bmd) {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><?= number_format($total, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama_bmd} Total" ?></td>
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
            <td><?= "{$value->nama_bmd} Total" ?></td>
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
        <td><?= $value->no_gm ?></td>
        <td title="<?= $value->uraian ?>"><?= substr($value->uraian, 0, 50) ?></td>
        <td><?= $value->partner ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td><?= $value->kode_coa_gmd ?></td>
        <td><?= $value->nama_gmd ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
    </tr>
    <?php
    if (isset($giro[$key + 1])) {
        if ($value->kode_coa_gmd !== $giro[$key + 1]->kode_coa_gmd) {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><?= number_format($total, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama_gmd} Total" ?></td>
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
            <td><?= "{$value->nama_gmd} Total" ?></td>
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