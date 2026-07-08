<?php
$kredits = 0;
$debets = 0;
$kreditsV = 0;
$debetsV = 0;
$temp = "";
$noUrut = 0;
$saldos = floatval($saldo->saldo_awal_final);
$saldosV = floatval($saldo->saldo_valas_final);
?>
<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td> <strong>Saldo Awal</strong></td>
    <td></td>
    <td class="text-right"><?= number_format(0, 2) ?></td>
    <td class="text-right"><?= number_format(0, 2) ?></td>
    <td class="text-right"><?= number_format($saldosV, 2) ?></td>
    <td class="text-right"><?= number_format(0, 2) ?></td>
    <td class="text-right"><?= number_format(0, 2) ?></td>
    <td class="text-right"><?= number_format($saldos, 2) ?></td>

</tr>
<?php
foreach ($data as $key => $value) {
    $partner = ($value->partner_nama === "") ? "[{$value->lain2}] " : "[{$value->partner_nama}] ";
    $showUrut = "";
    $shw = false;
    if ($value->no_bukti !== $temp) {
        $noUrut++;
        $showUrut = $noUrut;
        $shw = true;
    }
    $debet = 0;
    $kredit = 0;
    $debetV = 0;
    $kreditV = 0;
    if ($value->posisi === "D") {
        $debet = $value->nominal * $value->kurs;
        $debets += $debet;

        $debetV = $value->nominal;
        $debetsV += $debetV;
    } else {
        $kredit = $value->nominal * $value->kurs;
        $kredits += $kredit;

        $kreditV = $value->nominal;
        $kreditsV += $kreditV;
    }
    $saldos += ($debet - $kredit);
    $saldosV += ($debetV - $kreditV);
    ?>
    <tr>
        <td><?= $showUrut ?></td>
        <td><?= ($shw) ? $value->tanggal : "" ?></td>
        <td><?= ($shw) ? $value->no_bukti : "" ?></td>
        <td title="<?= $partner . $value->uraian ?>"><?= substr(($partner . $value->uraian), 0, 55) ?></td>
        <td><?= $value->coa ?></td>
        <td class="text-right"><?= number_format($value->kurs ?? 0, 2) ?></td>
        <td class="text-right"><?= number_format($debetV, 2) ?></td>
        <td class="text-right"><?= number_format($kreditV, 2) ?></td>
        <td class="text-right"><?= number_format($saldosV, 2) ?></td>
        <td class="text-right"><?= number_format($debet, 2) ?></td>
        <td class="text-right"><?= number_format($kredit, 2) ?></td>
        <td class="text-right"><?= number_format($saldos, 2) ?></td>
    </tr>
    <?php
    $temp = $value->no_bukti;
}
if (count($data) > 0) {
    ?>
    <tr>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td> <strong>Saldo Akhir</strong></td>
        <td></td>
        <td class="text-right"><?= number_format($debetsV, 2) ?></td>
        <td class="text-right"><?= number_format($kreditsV, 2) ?></td>
        <td class="text-right"><?= number_format($saldosV, 2) ?></td>
        <td class="text-right"><?= number_format($debets, 2) ?></td>
        <td class="text-right"><?= number_format($kredits, 2) ?></td>
        <td class="text-right"><?= number_format($saldos, 2) ?></td>
    </tr>
    <?php
}
?>