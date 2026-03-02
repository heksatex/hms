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
        <td title="<?= $value->partner ?>"><?= substr($value->partner, 0,38) ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td class="text-right"><?= number_format($value->kurs, 2) ?></td>
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
                <td class="text-right"><?= number_format($totalBankValas, 2) ?></td>
                <td class="text-right"></td>
                <td class="text-right"><?= number_format($totalBankKredit, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama_bkd} Total" ?></td>
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
            <td><?= "{$value->nama_bkd} Total" ?></td>
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
        <td title="<?= $value->uraian ?>"><?= substr($value->uraian, 0,50) ?></td>
        <td title="<?= $value->partner ?>"><?= substr($value->partner, 0,38) ?></td>
        <td class="text-right"><?= number_format($value->valas, 2) ?></td>
        <td class="text-right"><?= number_format($value->kurs, 2) ?></td>
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
                <td class="text-right"><?= number_format($totalGiroValas, 2) ?></td>
                <td class="text-right"></td>
                <td class="text-right"><?= number_format($totalGiroKredit, 2) ?></td>
                <td></td>
                <td><?= "{$value->nama_gkd} Total" ?></td>
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
            <td><?= "{$value->nama_gkd} Total" ?></td>
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
