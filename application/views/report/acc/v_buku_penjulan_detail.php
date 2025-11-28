<?php
$no = 0;
foreach ($data as $key => $value) {
    $no++;
    $harga = $value->harga * $value->kurs;
    $dpp = $value->jumlah * $value->kurs;
    $pajak = $value->pajak * $value->kurs;
    $diskon = $value->diskon * $value->kurs;
    ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $value->no_faktur_internal ?></td>
        <td><?= $value->no_sj ?></td>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->uraian ?></td>
        <td><?= $value->partner_nama ?></td>
        <td><?= "{$value->qty} {$value->uom}" ?></td>
        <td><?= "{$value->nama_curr}" ?></td>
        <td><?= number_format($value->kurs, 2) ?></td>
        <td style="text-align: right;"><?= number_format($harga, 4) ?></td>
        <td style="text-align: right;"><?= number_format($dpp, 2) ?></td>
        <td style="text-align: right;"><?= number_format($diskon, 2) ?></td>
        <td style="text-align: right;"><?= number_format($pajak, 2) ?></td>
        <td><?= "{$value->no_faktur_pajak}" ?></td>
    </tr>
    <?php
}
?>