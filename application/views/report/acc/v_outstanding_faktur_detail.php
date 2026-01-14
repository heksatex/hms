<?php
$deteRange = 0;
$dateNow = date("Y-m-d");
foreach ($data as $key => $value) {
    ?>
    <tr>
        <td colspan="10"> <strong>CUSTOMER :</strong> <?= $key ?> </td>
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
        $tanggalJatuTempo = date("Y-m-d", strtotime("{$values->tanggal}+ {$values->payment_term} days"));
        $dt1 = date_create($tanggalJatuTempo);
        $dt2 = date_create($dateNow);
        $diffs = date_diff($dt1, $dt2);
        $diff = (int) $diffs->format("%a");
        $labelClass = "";
        $labelStatus = "";
        if ($values->total_piutang_rp > 0) {
            if ($values->total_piutang_rp === $values->piutang_rp) {
                $labelStatus = "Unpaid";
            } else {
                $labelStatus = "Partially paid";
            }
            $df = $values->payment_term - $values->hari;
            if ($df <= 1) {
                $labelClass = "label label-danger";
            } else if ($df > 1 && $df <= 7) {
                $labelClass = "label label-warning";
            }
        }
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= $values->no_faktur_internal ?></td>
            <td><?= $values->no_sj ?></td>
            <td><?= $values->tanggal ?></td>
            <td class='text-right' ><?= "{$values->hari}" ?></td>
            <td class='text-right' ><?= $values->payment_term ?></td>
            <td class='text-right' ><span class="<?= $labelClass ?>"><?= "{$labelStatus}" ?></span></td>
            <td class='text-right' ><?= number_format($values->total_piutang_rp, 2) ?></td>
            <td class='text-right' ><?= number_format($values->piutang_rp, 2) ?></td>
            <td class='text-right' ><?= number_format($values->total_piutang_valas, 2) ?></td>
            <td class='text-right' ><?= number_format($values->piutang_valas, 2) ?></td>
        </tr>
        <?php
        $no++;
    }
    ?>
    <tr>
        <td colspan="7" class="text-right style_space" ><b>Total :</b></td>
        <td class="text-right style_space" ><b><?= number_format($total_piutang_rp, 2) ?></b></td>
        <td class="text-right style_space" ><b><?= number_format($piutang_rp, 2) ?></b></td>
        <td class="text-right style_space" ><b><?= number_format($total_piutang_valas, 2) ?></b></td>
        <td class="text-right style_space" ><b><?= number_format($piutang_valas, 2) ?></b></td>
    </tr>
    <?php
}
?>