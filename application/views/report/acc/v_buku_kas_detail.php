<?php
$kredits = 0;
$debets = 0;
$saldos = 0;
$temp = "";
$noUrut = 0;
$sld = 0;
if (count($data) > 0) {
    $saldos = floatval($saldo->saldo_awal_final);
    ?>
    <tr>
        <td colspan="3"></td>
        <td> <strong>Saldo Awal</strong></td>
        <td></td>
        <td class="text-right"><?= number_format(0, 2) ?></td>
        <td class="text-right"><?= number_format(0, 2) ?></td>
        <?php if ($valas) { ?>
            <td class="text-right"><?= number_format(0, 2) ?></td>
            <td class="text-right"><?= number_format(0, 2) ?></td>
        <?php } ?>
        <td class="text-right"><?= number_format($saldos, 2) ?></td>
    </tr>
    <?php
}

$valass = ["usd" => ["debit" => 0, "kredit" => 0], "euro" => ["debit" => 0, "kredit" => 0]];
foreach ($data as $key => $value) {
    $partner = ($value->partner_nama === "") ? "[{$value->lain2}] " : "[{$value->partner_nama}] ";
    $showUrut = "";
    $shw = false;
    if ($value->no_bukti !== $temp) {
        $noUrut++;
        $showUrut = $noUrut;
        $shw = true;
    }
    $saldo = 0;
    $debet = 0;
    $kredit = 0;
    if ($value->posisi === "D") {
        $debet = $value->nominal;
        $debets += $debet;
    } else {
        $kredit = $value->nominal;
        $kredits += $kredit;
    }
    $saldos += ($debet - $kredit) * $value->kurs;
    ?>
    <tr>
        <td><?= $showUrut ?></td>
        <td><?= ($shw) ? $value->tanggal : "" ?></td>
        <td><?= ($shw) ? $value->no_bukti : "" ?></td>
        <td title="<?= $partner . $value->uraian ?>"><?= substr(($partner . $value->uraian), 0, 55) ?></td>
        <td><?= $value->coa ?></td>
        <?php if (!$valas) { ?>
            <td class="text-right"><?= number_format($debet, 2) ?></td>
            <td class="text-right"><?= number_format($kredit, 2) ?></td>
            <?php
        } else {
            if (strtolower($value->nama_curr) === "usd") {
                $valass["usd"]["debit"] += $debet;
                $valass["usd"]["kredit"] += $kredit;
                ?>
                <td class="text-right"><?= number_format($debet, 2) ?></td>
                <td class="text-right"><?= number_format($kredit, 2) ?></td>
                <td class="text-right"><?= number_format(0, 2) ?></td>
                <td class="text-right"><?= number_format(0, 2) ?></td>
                <?php
            } else {
                $valass["euro"]["debit"] += $debet;
                $valass["euro"]["kredit"] += $kredit;
                ?>
                <td class="text-right"><?= number_format(0, 2) ?></td>
                <td class="text-right"><?= number_format(0, 2) ?></td>
                <td class="text-right"><?= number_format($debet, 2) ?></td>
                <td class="text-right"><?= number_format($kredit, 2) ?></td>
                <?php
            }
        }
        ?>
        <td class="text-right"><?= number_format($saldos, 2) ?></td>
    </tr>

    <?php
    $temp = $value->no_bukti;
}
if (count($data) > 0) {
    ?>
    <tr>
        <td colspan="3"></td>
        <td> <strong>Saldo Akhir</strong></td>
        <td></td>
        <?php if (!$valas) { ?>
            <td class="text-right"><?= number_format($debets, 2) ?></td>
            <td class="text-right"><?= number_format($kredits, 2) ?></td>
            <?php
        } else {
            if (strtolower($value->nama_curr) === "usd") {
                ?>
                <td class="text-right"><?= number_format($valass["usd"]["debit"], 2) ?></td>
                <td class="text-right"><?= number_format($valass["usd"]["kredit"], 2) ?></td>
                <td class="text-right"><?= number_format(0, 2) ?></td>
                <td class="text-right"><?= number_format(0, 2) ?></td>
                <?php
            } else {
                ?>
                <td class="text-right"><?= number_format(0, 2) ?></td>
                <td class="text-right"><?= number_format(0, 2) ?></td>
                <td class="text-right"><?= number_format($valass["euro"]["debit"], 2) ?></td>
                <td class="text-right"><?= number_format($valass["euro"]["kredit"], 2) ?></td>
                <?php
            }
        }
        ?>
        <td class="text-right"><?= number_format($saldos, 2) ?></td>
    </tr>
    <?php
}
?>