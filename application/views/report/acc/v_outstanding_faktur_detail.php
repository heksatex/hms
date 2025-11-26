<?php
foreach ($data as $key => $value) {
    ?>
    <tr>
        <td colspan="9"> <strong>Supplier :</strong> <?= $key ?> </td>
    </tr>
    <?php
    $no = 1;
    $total_piutang_rp = 0;
    $piutang_rp = 0;
    $total_piutang_valas = 0;
    $piutang_valas = 0;
    foreach ($value as $keys => $values) {
        $total_piutang_rp += $values->total_piutang_rp;
        $piutang_rp += $values->piutang_rp;
        $total_piutang_valas += $values->total_piutang_valas;
        $piutang_valas += $values->piutang_valas;
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= $values->no_faktur_internal ?></td>
            <td><?= $values->no_sj ?></td>
            <td><?= $values->tanggal ?></td>
            <td class='text-right' ><?= number_format($values->total_piutang_rp, 2) ?></td>
            <td class='text-right' ><?= number_format($values->piutang_rp, 2) ?></td>
            <td class='text-right' ><?= number_format($values->total_piutang_valas, 2) ?></td>
            <td class='text-right' ><?= number_format($values->piutang_valas, 2) ?></td>
            <td class='text-right' ><?= $values->hari ?></td>
        </tr>
        <?php
        $no++;
    }
    ?>
        <tr>
            <td colspan="4" class="text-right style_space" ><b>Total :</b></td>
             <td class="text-right style_space" ><b><?= number_format($total_piutang_rp, 2) ?></b></td>
             <td class="text-right style_space" ><b><?= number_format($piutang_rp, 2) ?></b></td>
             <td class="text-right style_space" ><b><?= number_format($total_piutang_valas, 2) ?></b></td>
             <td class="text-right style_space" ><b><?= number_format($piutang_valas, 2) ?></b></td>
             <td class="style_space" ><b</b></td>
        </tr>
        <?php
}
?>