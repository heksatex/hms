<div class="box-header with-border">
    <h4 class="box-title">Preview</h4>
</div>
<div class="col-md-12">
    <caption>Jurnal</caption>
    <table class="table table-condesed table-hover rlstable  over" width="100%">
        <thead>
        <th class="no">
            No
        </th>
        <th>
            Nama
        </th>
        <th>
            Coa
        </th>
        <th class="text-right">
            D
        </th>
        <th class="text-right">
            C
        </th>
        </thead>
        <tbody>
            <?php
            $no = 0;

            foreach ($coa as $k => $value) {
                $saldoAwalValas = floatval($value->saldo_valas_final);
                $totalDebitValas = floatval($value->total_debit_valas);
                $totalCreditValas = floatval($value->total_credit_valas);

                $saldoAwal = floatval($value->saldo_awal_final);
                $totalDebit = floatval($value->total_debit);
                $totalCredit = floatval($value->total_credit);
                if ($value->saldo_normal == 'D') {
                    $saldoAkhirValas = $saldoAwalValas + $totalDebitValas - $totalCreditValas;
                    $saldoAkhir = $saldoAwal + $totalDebit - $totalCredit;
                } else {
                    $saldoAkhirValas = $saldoAwalValas + $totalCreditValas - $totalDebitValas;
                    $saldoAkhir = $saldoAwal + $totalCredit - $totalDebit;
                }

                $selisih = ($saldoAkhirValas * $kurs) - $saldoAkhir;
                $nominal = abs($selisih);
                if ($saldoAkhirValas <= 0) {
                    continue;
                }
                $no += 1;
                $nama = "Kurs Akhir Bulan (Saldo Valas : " . number_format($saldoAkhirValas, 2) . " {$curr}, Saldo Rp " . number_format($saldoAkhir, 2) . " Kurs : " . number_format($kurs, 2) . ")";
                ?>
                <tr>
                    <td>
                        <?= $no ?>
                    </td>
                    <td>
                        <?= $nama ?>
                    </td>
                    <td title="<?= $value->nama_coa ?>">
                        <?= $value->kode_coa ?>
                    </td>

                    <td class="text-right">
                        <?= ($selisih > 0) ? number_format($nominal, 2) : "0.00" ?>
                    </td>
                    <td class="text-right">
                        <?= ($selisih < 0) ? number_format($nominal, 2) : "0.00" ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $no += 1 ?>
                    </td>
                    <td>
                        <?= $nama ?>
                    </td>
                    <td>
                        <?= $coa_sk->value ?>
                    </td>
                    <td class="text-right">
                        <?= ($selisih > 0) ? "0.00" : number_format($nominal, 2) ?>
                    </td>
                    <td class="text-right">
                        <?= ($selisih > 0) ? number_format($nominal, 2) : "0.00" ?>
                    </td>
                </tr>
                <?php
            }

            if ($no > 0) {
                ?>
            <style>
                #btn-confirm{
                    display: inline;
                }
            </style>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
<div class="col-md-12">
    <caption>Kas</caption>
    <table class="table table-condesed table-hover rlstable  over" width="100%">
        <thead>
        <th class="no">
            No
        </th>
        <th>
            Nama
        </th>
        <th class="text-right">
            Kurs
        </th>
        <th class="text-right">
            Jumlah
        </th>
        <th class="text-right">
            Kurs Baru
        </th>
        <th class="text-right">
            Jumlah Baru
        </th>
        </thead>
        <tbody>
            <?php
            foreach ($kas as $key => $value) {
                $oldKurs = ($value->kurs_akhir > 0) ? $value->kurs_akhir : $value->kurs;
                $nominals = number_format($value->nominal, 2);
                ?>
                <tr>
                    <td>
    <?= $key + 1 ?>
                    </td>
                    <td>
    <?= "{$value->nama_menu} {$value->no} ({$nominals} {$curr})" ?>
                    </td>
                    <td class="text-right">
    <?= number_format($oldKurs, 2) ?>
                    </td>
                    <td class="text-right">
    <?= number_format($value->nominal * $oldKurs, 2) ?>
                    </td>
                    <td class="text-right">
    <?= number_format($kurs, 2) ?>
                    </td>
                    <td class="text-right">
    <?= number_format($value->nominal * $kurs, 2) ?>
                    </td>
                </tr>
            <?php }
            ?>
        </tbody>
    </table>
</div>

<div class="col-md-12">
    <caption>Pelunasan</caption>
    <table class="table table-condesed table-hover rlstable  over" width="100%">
        <thead>
        <th>
            No
        </th>
        <th class="text-right">
            Kurs
        </th>
        <th class="text-right">
            Jumlah
        </th>
        <th class="text-right">
            Kurs Baru
        </th>
        <th class="text-right">
            Jumlah Baru
        </th>
        </thead>
        <tbody>
            <?php
            foreach ($deposit as $key => $value) {
                $oldKurs = ($value->kurs_akhir > 0) ? $value->kurs_akhir : $value->kurs;
                $saldo = $value->total_piutang - $value->total_pelunasan;
//                $saldo = abs($saldo);
                ?>
                <tr>
                    <td>
                        <?= "{$value->no_pelunasan} -  {$value->partner_nama} ({$saldo}) {$curr}" ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($oldKurs, 2) ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($oldKurs * abs($saldo), 2) ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($kurs, 2) ?>
                    </td>
                    <td class="text-right">
    <?= number_format(abs($saldo) * $kurs, 2) ?>
                    </td>

                </tr>
<?php }
?>
        </tbody>
    </table>
</div>