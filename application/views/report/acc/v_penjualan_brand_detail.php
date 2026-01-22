<?php
$no = 0;
$total = 0;
$temp = "";
foreach ($data as $key => $value) {
    $no += 1;
    $total += $value->jumlah * $value->kurs_nominal;
    $item = ($value->warna !== "") ? "{$value->uraian} / {$value->warna}" : "{$value->uraian}";
    $sj = "";
    $inv = "";
    $vat = "";
    if ($value->no_sj !== $temp) {
        $sj = $value->no_sj;
        $inv = $value->no_faktur_internal;
        $vat = $value->no_faktur_pajak;
    }
    ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= date("Y-m-d", strtotime($value->tanggal_dokumen)) ?></td>
        <td><?= $sj ?></td>
        <td><?= $value->no_po ?></td>
        <td><?= $inv ?></td>
        <td><?= $vat ?></td>
        <td><?= $value->tanggal ?></td>
        <td><?= $item ?></td>
        <td><?= "{$value->qty} {$value->uom}" ?> </td>
        <td class="text-right"><?= number_format($value->harga, 2) ?></td>
        <td class="text-right"><?= number_format($value->kurs_nominal, 2) ?></td>
        <td><?= $value->curr ?></td>
        <td class="text-right"><?= number_format($value->jumlah * $value->kurs_nominal, 2) ?></td>
    </tr>
    <?php
    $temp = $value->no_sj;
}
if ($total > 0) {
    ?>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td></td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>Total (Rp)</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td class="text-right"><?= number_format($total, 2) ?></td>
    </tr>
    <?php
}
?>