<?php
$no = 0;
$grandTotal = 0;
$total = 0;
foreach ($data as $key => $value) {
    $grandTotal += $value->nominal;
    $total += $value->nominal;
    $no++;
    $qty = explode("/ ", $value->nama);
    $qtys = (count($qty) > 1) ? end($qty) : "";
    ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $value->no_faktur_internal ?></td>
        <td><?= $value->no_sj ?></td>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->nama ?></td>
        <td><?= $value->partner_nama ?></td>
        <td><?= "{$value->kode_coa} - {$value->coa}" ?></td>
        <td><?= "{$value->kode_mua}" ?></td>
        <td><?= "{$value->kurs}" ?></td>
        <td style="text-align: right;"><?= $qtys ?></td>
        <td style="text-align: right;"><?= number_format($value->nominal, 2) ?></td>

    </tr>
    <?php
    if (isset($data[$key + 1])) {
        if ($value->kode_coa !== $data[$key + 1]->kode_coa) {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-bold"><?= "Total  {$value->coa}" ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-bold" style="text-align: right;"><?= number_format($total, 2) ?></td>

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
            <td></td>
            <td></td>
            <td class="text-bold"><?= "Total  {$value->coa}" ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-bold" style="text-align: right;"><?= number_format($total, 2) ?></td>

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
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right"></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-bold">Grand Total</td>
        <td></td>
        <td></td>
        <td></td>
        <td class="text-right text-bold"><?= number_format($grandTotal, 2) ?></td>
    </tr>
    <?php
}
?>