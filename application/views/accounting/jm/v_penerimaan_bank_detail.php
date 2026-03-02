<?php
$totalBankKredit = 0;
$totalBankValas = 0;
$grandTotal = 0;
$grandTotalValas = 0;
$bank = $data["bank_kredit"];
foreach ($bank as $key => $value) {
    $totalBankKredit += $value->nominals;
    $totalBankValas += $value->valas;
    $grandTotal += $value->nominals;
    $grandTotalValas += $value->valas;
    ?>
    <tr>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->no_bm ?></td>
        <td title="<?= $value->uraian ?>"><?= substr($value->uraian, 0,50) ?></td>
        <td title="<?= $value->partner ?>"><?= substr($value->partner, 0,38) ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td class="text-right"><?= number_format($value->kurs, 2) ?></td>
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
                <td class="text-right"><?= number_format($totalBankValas, 2) ?></td>
                <td class="text-right"></td>
                <td class="text-right"><?= number_format($totalBankKredit, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama_bmd} Total" ?></td>
                <td class="text-right"><?= number_format($totalBankKredit, 2) ?></td>
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
            $totalBankKredit = 0;
            $totalBankValas = 0;
        }
    } else {
        ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><?= number_format($totalBankValas, 2) ?></td>
            <td class="text-right"></td>
            <td class="text-right"><?= number_format($totalBankKredit, 2) ?></td>
            <td></td>
            <td><?= "{$value->nama_bmd} Total" ?></td>
            <td class="text-right"><?= number_format($totalBankKredit, 2) ?></td>
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
$totalGiroKredit = 0;
$totalGiroValas = 0;
$giro = $data["giro_kredit"];
foreach ($giro as $key => $value) {
    $totalGiroKredit += $value->nominals;
    $totalGiroValas += $value->valas;
    $grandTotal += $value->nominals;
    $grandTotalValas += $value->valas;
    ?>
    <tr>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->no_gm ?></td>
        <td title="<?= $value->uraian ?>"><?= substr($value->uraian, 0,50) ?></td>
        <td title="<?= $value->partner ?>"><?= substr($value->partner, 0,38) ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td class="text-right"><?= number_format($value->kurs, 2) ?></td>
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
                <td class="text-right"><?= number_format($totalGiroValas, 2) ?></td>
                <td class="text-right"></td>
                <td class="text-right"><?= number_format($totalGiroKredit, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama_gmd} Total" ?></td>
                <td class="text-right"><?= number_format($totalGiroKredit, 2) ?></td>
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
            $totalGiroKredit = 0;
            $totalGiroValas = 0;
        }
    } else {
        ?>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"><?= number_format($totalGiroValas, 2) ?></td>
            <td class="text-right"></td>
            <td class="text-right"><?= number_format($totalGiroKredit, 2) ?></td>
            <td></td>
            <td><?= "{$value->nama_gmd} Total" ?></td>
            <td class="text-right"><?= number_format($totalGiroKredit, 2) ?></td>
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
