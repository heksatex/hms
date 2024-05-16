<?php
$no = 1;
$tempid = "";
$sumDef = array(
    'total_qty' => (float) 0,
    'total_qty2' => (float) 0,
    'total_qty_jual' => (float) 0,
    'total_qty2_jual' => (float) 0,
    'total_lot' => 0,
);
$sumUomDef = array(
    'uom' => "",
    'uom2' => "",
    'uom_jual' => "",
    'uom2_jual' => "",
);
$sum = $sumDef;
$sumUom = $sumUomDef;
foreach ($list as $key => $value) {
    $sum["total_qty"] += $value->total_qty;
    $sum["total_qty2"] += $value->total_qty2;
    $sum["total_qty_jual"] += $value->total_qty_jual;
    $sum["total_qty2_jual"] += $value->total_qty2_jual;
    $sumUom["uom"] = $value->uom;
    $sumUom["uom2"] = $value->uom2;
    $sumUom["uom_jual"] = $value->uom_jual;
    $sumUom["uom2_jual"] = $value->uom2_jual;
    if ($rekap !== "barcode") {
        $sum["total_lot"] += $value->total_lot;
    } else {
        $sum["total_lot"] = $value->total_lot;
    }
    ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $value->no ?></td>
        <td><?= $value->no_sj ?></td>
        <td><?= $value->tanggal_buat ?></td>
        <td><?= $value->tanggal_dokumen ?></td>
        <td><?= strtoupper($value->jenis_jual) ?></td>
        <td><?= $value->no_picklist ?></td>
        <td><?= $value->nama ?></td>
        <td><?= substr($value->alamat, 0, 50) . ' ...' ?></td>
        <td><?= ($rekap === "global") ? "" : $value->corak_remark ?></td>
        <td><?= ($rekap === "global") ? "" : $value->warna_remark ?></td>
        <td class="text-right"><?= number_format($value->total_qty, 2) . ' ' . $value->uom ?></td>
        <td  class="text-right"><?= number_format($value->total_qty2, 2) . ' ' . $value->uom2 ?></td>
        <td  class="text-right"><?= number_format($value->total_qty_jual, 2) . ' ' . $value->uom_jual ?></td>
        <td  class="text-right"><?= number_format($value->total_qty2_jual, 2) . ' ' . $value->uom2_jual ?></td>
        <td class="text-right"><?= $value->total_lot ?></td>
        <td><?= substr($value->note, 0, 50) ?></td>
        <td><?= $value->marketing ?></td>
    </tr>
    <?php
    if ($summary === "1") {
        if (isset($list[$key + 1])) {
            if ($value->no_sj !== $list[$key + 1]->no_sj) {
                ?>
                <tr>
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
                    <td  class="text-right text-bold">SUM : <?= $value->no_sj ?></td>
                    <td  class="text-right"><?= number_format($sum["total_qty"], 2) . ' ' . $sumUom["uom"] ?></td>
                    <td  class="text-right"><?= number_format($sum["total_qty2"], 2) . ' ' . $sumUom["uom2"] ?></td>
                    <td  class="text-right"><?= number_format($sum["total_qty_jual"], 2) . ' ' . $sumUom["uom_jual"] ?></td>
                    <td  class="text-right"><?= number_format($sum["total_qty2_jual"], 2) . ' ' . $sumUom["uom2_jual"] ?></td>
                    <td class="text-right"><?= $sum["total_lot"] ?></td>
                    <td><?= substr($value->note, 0, 50) ?></td>
                    <td><?= $value->marketing ?></td>
                </tr>
                <tr>
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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php
                $sum = $sumDef;
                $sumUom = $sumUomDef;
            }
            ?>

            <?php
        } else {
            ?>
            <tr>
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
                <td class="text-right text-bold">SUM : <?= $value->no_sj ?></td>
                <td  class="text-right"><?= number_format($sum["total_qty"], 2) . ' ' . $sumUom["uom"] ?></td>
                <td  class="text-right"><?= number_format($sum["total_qty2"], 2) . ' ' . $sumUom["uom2"] ?></td>
                <td class="text-right"><?= number_format($sum["total_qty_jual"], 2) . ' ' . $sumUom["uom_jual"] ?></td>
                <td class="text-right"><?= number_format($sum["total_qty2_jual"], 2) . ' ' . $sumUom["uom2_jual"] ?></td>
                <td class="text-right"><?= $sum["total_lot"] ?></td>
                <td><?= substr($value->note, 0, 50) ?></td>
                <td><?= $value->marketing ?></td>
            </tr>
            <?php
        }
        ?>

        <?php
    }
    $tempid = $value->no_sj;
    $no++;
}
?>