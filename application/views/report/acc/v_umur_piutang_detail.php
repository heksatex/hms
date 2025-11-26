<thead>
<th style="width: 1%;">
    No
</th>
<th style="width: 80px;">
    Customer
</th>
<th class="text-right" style="width: 150px;">
    Total Piutang
</th>
<?php
foreach ($head as $key => $value) {
    ?>
    <th class="text-right" style="width: 150px;">
        <?= $value ?>
    </th>
    <?php
}
?>
</thead>
<tbody>
    <?php
    $no = 0;
    $totalPiutang = 0;
    $piutang_bulan_ini = 0;
    $piutang_bulan_1 = 0;
    $piutang_bulan_2 = 0;
    $piutang_bulan_3 = 0;
    $piutang_lebih_dari_3_bulan = 0;
    
    foreach ($body as $key => $value) {
        $no++;
        $totalPiutang += $value->total_piutang;
        $piutang_bulan_ini += $value->piutang_bulan_ini;
        $piutang_bulan_1 += $value->piutang_bulan_1;
        $piutang_bulan_2 += $value->piutang_bulan_2;
        $piutang_bulan_3 += $value->piutang_bulan_3;
        $piutang_lebih_dari_3_bulan += $value->piutang_lebih_dari_3_bulan;
        ?>
        <tr>
            <td>
                <?= $no ?>
            </td>
            <td>
                <a href=" <?=base_url("report/outstandingfaktur?partner={$value->partner_id}"); ?>" target="_blank"><?= $value->partner_nama ?> </a>  
            </td>
            <td class="text-right">
                <?= number_format($value->total_piutang, 2) ?>
            </td>
            <td class="text-right">
                <?= number_format($value->piutang_bulan_ini, 2) ?>
            </td>
            <td class="text-right">
                <?= number_format($value->piutang_bulan_1, 2) ?>
            </td>
            <td class="text-right">
                <?= number_format($value->piutang_bulan_2, 2) ?>
            </td>
            <td class="text-right">
                <?= number_format($value->piutang_bulan_3, 2) ?>
            </td>
            <td class="text-right">
                <?= number_format($value->piutang_lebih_dari_3_bulan, 2) ?>
            </td>
        </tr>
        <?php
    }
    ?>
</tbody>
<?php
if($no > 0){
    ?>
<tfoot>
<td colspan='2' class='style_space text-right'><b>Total :</b></td>
<td class='style_space text-right'><b><?= number_format($totalPiutang,2) ?></b></td>
<td class='style_space text-right'><b><?= number_format($piutang_bulan_ini,2) ?></b></td>
<td class='style_space text-right'><b><?= number_format($piutang_bulan_1,2) ?></b></td>
<td class='style_space text-right'><b><?= number_format($piutang_bulan_2,2) ?></b></td>
<td class='style_space text-right'><b><?= number_format($piutang_bulan_3,2) ?></b></td>
<td class='style_space text-right'><b><?= number_format($piutang_lebih_dari_3_bulan,2) ?></b></td>
</tfoot>
<?php
}
?>