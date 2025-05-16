<?php
$no = 1;
$total_group = 0;
foreach ($data as $key => $value) {
    $harga = $value->harga_per_uom_beli;
    $diskon =  $value->diskon;
    $subsubtotal = $value->total;
    $pajak = $value->pajak;
//    if($dpp !== null){
//         $pajak = (($subsubtotal * 11) / 12) *  $value->amount_tax;
//    }
//    else{
//        $pajak = $subsubtotal * $value->amount_tax;
//    }
    
    $total_group += $subsubtotal;
    ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $value->po_no_po ?></td>
        <td><?= $value->nama_supp ?></td>
        <td><?= $value->gudang ?></td>
        <td><?= $value->order_date ?></td>
        <td><?= $value->jenis ?></td>
        <td><?= "[{$value->kode_produk}] {$value->nama_produk}" ?></td>
        <td><?= number_format($value->qty_beli, 2) . " " . $value->uom_beli ?></td>
        <!--<td><?= $value->nama_curr ?></td>-->
        <td><?= $value->nilai_currency ?></td>
        <td><?= number_format($value->harga_per_uom_beli, 2) ?></td>
        <td><?= number_format($value->diskon, 2) ?></td>
        <td><?= number_format($pajak, 2) ?></td>
        <td><?= number_format(($subsubtotal), 2) ?></td>
    </tr>
    <?php
    if ($group !== "") {
        $cek1 = (array) $value;
        $cek2 = (array) ($data[$key + 1] ?? [$group=>"--"]);
        if ($cek1[$group] !== $cek2[$group]) {
            ?>
            <tr>
                <td colspan="13" style="text-align: right"><strong>Total</strong></td>
                <td><?= number_format($total_group, 2) ?></td>
            </tr>        
            <?php
            $total_group = 0;
        }
    }
    $no++;
}