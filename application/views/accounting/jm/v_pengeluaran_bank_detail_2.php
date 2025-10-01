<?php
$totalBankKredit = 0;
$totalBankValas = 0;
$grandTotal = 0;
$grandTotalValas = 0;
$bank = $data["bank_debit"];
foreach ($bank as $key => $value) {
    $totalBankKredit += $value->nominals;
    $totalBankValas += $value->valas;
    $grandTotal += $value->nominals;
    $grandTotalValas += $value->valas;
    ?>
    <tr>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->no_bk ?></td>
        <td title="<?= $value->uraian ?>"><?= substr($value->uraian, 0,50) ?></td>
        <td><?= $value->partner ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td class="text-right"><?= number_format($value->kurs, 2) ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
        <td><?= $value->kode_coa ?></td>
        <td><?= $value->nama ?></td>
        <td class="text-right"><?= number_format($value->nominals, 2) ?></td>
    </tr>
    <?php
    if (isset($bank[$key + 1])) {
        if ($value->kode_coa !== $bank[$key + 1]->kode_coa) {
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
                <td><?= "{$value->nama} Total" ?></td>
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
            <td><?= "{$value->nama} Total" ?></td>
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
$totalGiroKredit += 0;
$totalGiroValas += 0;
$giro = $data["giro_debit"];
foreach ($giro as $key => $value) {
    $totalGiroKredit += $value->nominals;
    $totalGiroValas += $value->valas;
    $grandTotal += $value->nominals;
    $grandTotalValas += $value->valas;
    ?>
    <tr>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->no_gk ?></td>
        <td><?= $value->uraian ?></td>
        <td><?= $value->partner ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td class="text-right"><?= number_format($value->kurs, 2) ?></td>
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
                <td class="text-right"><?= number_format($totalGiroValas, 2) ?></td>
                <td class="text-right"></td>
                <td class="text-right"><?= number_format($totalGiroKredit, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama} Total" ?></td>
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
            <td><?= "{$value->nama} Total" ?></td>
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
