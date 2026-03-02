<?php
$totalKasKredit = 0;
$totalKasValas = 0;
$grandTotal = 0;
$grandTotalValas = 0;
$kas = $data["kas_kredit"];
foreach ($kas as $key => $value) {
    $totalKasKredit += $value->nominals;
    $totalKasValas += $value->valas;
    $grandTotal += $value->nominals;
    $grandTotalValas += $value->valas;
    ?>
    <tr>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->no_kk ?></td>
        <td title="<?= $value->uraian ?>"><?= substr($value->uraian, 0,50) ?></td>
        <td title="<?= $value->partner ?>"><?= substr($value->partner, 0,38) ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td class="text-right"><?= number_format($value->kurs, 2) ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td><?= $value->kode_coa_kkd ?></td>
        <td><?= $value->nama_kkd ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
    </tr>
    <?php
    if (isset($kas[$key + 1])) {
        if ($value->kode_coa_kkd !== $kas[$key + 1]->kode_coa_kkd) {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"><?= number_format($totalKasValas, 2) ?></td>
                <td class="text-right"></td>
                <td class="text-right"><?= number_format($totalKasKredit, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama_kkd} Total" ?></td>
                <td class="text-right"><?= number_format($totalKasKredit, 2) ?></td>
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
                <td></td>
                <td></td>
            </tr>
            <?php
            $totalKasKredit = 0;
            $totalKasValas = 0;
        }
    } else {
        ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><?= number_format($totalKasValas, 2) ?></td>
            <td class="text-right"></td>
            <td class="text-right"><?= number_format($totalKasKredit, 2) ?></td>
            <td></td>
            <td><?= "{$value->nama_kkd} Total" ?></td>
            <td class="text-right"><?= number_format($totalKasKredit, 2) ?></td>
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
    <td></td>
    <td></td>
</tr>
<?php
if ($grandTotal > 0) {
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
        <td></td>
        <td></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right"><?= number_format($grandTotalValas, 2) ?></td>
        <td class="text-right"></td>
        <td class="text-right"><?= number_format($grandTotal, 2) ?></td>
        <td></td>
        <td><?= "Grand Total" ?></td>
        <td class="text-right"><?= number_format($grandTotal, 2) ?></td>
    </tr>
    <?php
}
