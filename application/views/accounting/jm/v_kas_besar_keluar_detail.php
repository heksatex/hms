<?php
$total = 0;
$kredits = $data["debit"];
foreach ($kredits as $key => $value) {
    $total += $value->nominals;
    ?>
    <tr>
        <td><?= $value->tanggal ?></td>
        <td><?= $value->no_bukti ?></td>
        <td><?= $value->uraian ?></td>
        <td><?= $value->partner ?></td>
        <td class="text-right">
            <?= number_format($value->nominals, 2) ?>
        </td>
        <td><?= $value->kode_coa ?></td>
        <td><?= $value->nama ?></td>
        <td class="text-right">
            <?= number_format($value->nominals, 2) ?>
        </td>
    </tr>
    <?php
    if (isset($kredits[$key + 1])) {
        if ($value->kode_coa !== $kredits[$key + 1]->kode_coa) {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right">
                    <?= number_format($total, 2) ?>
                </td>
                <td></td>
                <td><?= "{$value->nama} Total" ?></td>
                <td class="text-right">
                    <?= number_format($total, 2) ?>
                </td>
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
            <td class="text-right">
                <?= number_format($total, 2) ?>
            </td>
            <td></td>
            <td><?= "{$value->nama} Total" ?></td>
            <td class="text-right">
                <?= number_format($total, 2) ?>
            </td>
        </tr>
        <?php
    }
}
?>