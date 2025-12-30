<?php
$no = 0;
$grandTotal = 0;
$total = 0;
$totalHarga = 0;
foreach ($data as $key => $value) {
    $total += $value->nominal;
    $harga = ($value->harga * $value->qty) * $value->kurs;
    $totalHarga += $harga;
    $grandTotal += ($value->qty) ? $harga : $value->nominal;
    $no++;
    $qty = explode("/ ", $value->nama);
    $qtys = (count($qty) > 1) ? end($qty) : "";
    $nama = (count($qty) > 1) ? $qty[0] : "";
    $totalanItem = ($value->qty) ? number_format($harga, 2) : number_format($value->nominal, 2);
    ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $value->no_faktur_internal ?></td>
        <td><?= $value->no_sj ?></td>
        <td><?= $value->tanggal ?></td>
        <td><?= $nama ?></td>
        <td><?= $value->partner_nama ?></td>
        <td><?= "{$value->kode_coa} - {$value->coa}" ?></td>
        <td><?= $value->jenis_ppn ?></td>
        <td><?= $value->kode_mua ?></td>
        <td class="text-right"><?= number_format($value->kurs,2) ?></td>
        <td style="text-align: right;"><?= ($value->qty) ? number_format($value->qty, 2) . " {$value->uom}" : $qtys ?></td>
        <td style="text-align: right;"><?= ($value->harga > 0) ? number_format($value->harga, 2) : $totalanItem ?></td>
        <td style="text-align: right;"><?= $totalanItem ?></td>

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
                <td class="text-bold"><?= "Total {$value->coa}" ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-bold" style="text-align: right;"><?= ($value->qty) ? number_format($totalHarga, 2) : number_format($total, 2) ?></td>

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
                <td></td>
                <td></td>
            </tr>
            <?php
            $total = 0;
            $totalHarga = 0;
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
            <td class="text-bold"><?= "Total {$value->coa}" ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-bold" style="text-align: right;"><?= ($value->qty) ? number_format($totalHarga, 2) : number_format($total, 2) ?></td>

        </tr>
        <?php
    }
}
?>