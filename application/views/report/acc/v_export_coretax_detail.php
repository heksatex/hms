<?php
$no = 0;
foreach ($data as $key => $value) {
    $no += 1;
    $dpplainDikon = ($value->diskon * 11 / 12);
    ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $value->no_sj ?></td>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->uraian ?></td>
        <td class="text-right"><?= number_format(($value->harga * $value->kurs_nominal), 2) ?></td>
        <td ><?= "{$value->qty} {$value->uom}" ?></td>
        <td class="text-right"><?= number_format(($value->diskon * $value->kurs_nominal), 2) ?></td>
        <td class="text-right"><?= number_format((($value->jumlah - $value->diskon) * $value->kurs_nominal), 2) ?></td>
        <td class="text-right"><?= number_format(($value->dpp_lain -  $dpplainDikon) * $value->kurs_nominal, 2) ?></td>
        <td class="text-right"><?= ($value->tax_value * 100) ?></td>
        <td class="text-right"><?= number_format(($value->pajak -  $value->diskon_ppn)  * $value->kurs_nominal, 2) ?></td>
    </tr>
    <?php
}
?>