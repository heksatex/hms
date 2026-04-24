<div class="box-header with-border">
    <h4 class="box-title">Preview</h4>
</div>

<div class="col-md-12">
    <table class="table table-condesed table-hover rlstable  over" width="100%">
        <caption>Jurnal</caption>
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
                        <?= ($selisih > 0) ? $coa_sk->value : $coa_skr->value ?>
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
    <table class="table table-condesed table-hover rlstable  over" width="100%">
        <caption>Kas, Bank, Giro</caption>
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
    <table class="table table-condesed table-hover rlstable  over" id="tbl-um" width="100%">
        <caption>Uang Muka Penjualan</caption>
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
        <th class="text-right">
            Selisih
        </th>
        </thead>
        <tbody>
            <?php
            foreach ($umpen as $key => $value) {
                $oldKurs = ($value->kurs_akhir > 0) ? $value->kurs_akhir : $value->kurs;
                $nominals = number_format($value->nominal, 2);
                $selisih = ($value->nominal * $kurs) - ($value->nominal * $oldKurs);
                ?>
                <tr data-tt-id="<?= "k{$value->no}" ?>" data-tt-parent-id="<?= $key + 1 ?>">
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
                    <td class="text-right">
                        <?= number_format($selisih, 2) ?>
                    </td>
                </tr>
                <?php
                if ($selisih == 0) {
                    continue;
                }
                $coaD = ($selisih > 0) ? "8241.01" : $value->kode_coa;
                $coaC = ($selisih > 0) ? $value->kode_coa : "8141.01";
                ?>
                <tr data-tt-id="<?= "k{$value->no}1" ?>" data-tt-parent-id="<?= "k{$value->no}" ?>">
                    <th></th>
                    <th>Nama</th>
                    <th colspan="2">COA</th>
                    <th class="text-right">D</th>
                    <th class="text-right">C</th>
                </tr>
                <tr data-tt-id="<?= "k{$value->no}1" ?>" data-tt-parent-id="<?= "k{$value->no}" ?>">
                    <td></td>
                    <td>Jurnal UM <?= $value->no ?></td>
                    <td colspan="2">
                        <select class="form-control input-sm select22 jurnal_um" style="width:80%"
                                data-posisi="D" data-jenis="uangmuka" data-menu="<?= $value->nama_menu ?>"
                                data-ids = "<?= $value->ids ?>" data-saldo="<?=$value->nominal?>" data-no="<?= $value->no ?>" data-selisih="<?= $selisih ?>">
                            <option value=""></option>
                                    <?php
                                    foreach ($coas as $keys => $values) {
                                        ?>
                                <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $coaD) ? "selected" : "" ?>><?= "{$values->kode_coa} {$values->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="text-right"><?= number_format(abs($selisih), 2) ?></td>
                    <td class="text-right">0</td>
                </tr>
                <tr data-tt-id="<?= "k{$value->no}1" ?>" data-tt-parent-id="<?= "k{$value->no}" ?>">
                    <td></td>
                    <td>Jurnal UM <?= $value->no ?></td>
                    <td colspan="2">
                        <select class="form-control input-sm select22 jurnal_um" style="width:80%"
                                data-posisi="C" data-jenis="uangmuka" data-no="<?= $value->no ?>" data-selisih="<?= $selisih ?>">
                            <option value=""></option>
                                    <?php
                                    foreach ($coas as $keys => $values) {
                                        ?>
                                <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $coaC) ? "selected" : "" ?>><?= "{$values->kode_coa} {$values->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="text-right">0</td>
                    <td class="text-right"><?= number_format(abs($selisih), 2) ?></td>
                </tr>
            <?php }
            ?>
        </tbody>
    </table>
</div>

<div class="col-md-12">
    <table class="table table-condesed table-hover rlstable  over" id="tbl-um" width="100%">
        <caption>Uang Muka Pembelian</caption>
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
        <th class="text-right">
            Selisih
        </th>
        </thead>
        <tbody>
            <?php
            foreach ($umpem as $key => $value) {
                $oldKurs = ($value->kurs_akhir > 0) ? $value->kurs_akhir : $value->kurs;
                $nominals = number_format($value->nominal, 2);
                $selisih = ($value->nominal * $kurs) - ($value->nominal * $oldKurs);
                ?>
                <tr data-tt-id="<?= "k{$value->no}" ?>" data-tt-parent-id="<?= $key + 1 ?>">
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
                    <td class="text-right">
                        <?= number_format($selisih, 2) ?>
                    </td>
                </tr>
                <?php
                if ($selisih == 0) {
                    continue;
                }
                $coaD = ($selisih > 0) ? "8241.01" : $value->kode_coa;
                $coaC = ($selisih > 0) ? $value->kode_coa : "8141.01";
                ?>
                <tr data-tt-id="<?= "k{$value->no}1" ?>" data-tt-parent-id="<?= "k{$value->no}" ?>">
                    <th></th>
                    <th>Nama</th>
                    <th colspan="2">COA</th>
                    <th class="text-right">D</th>
                    <th class="text-right">C</th>
                </tr>
                <tr data-tt-id="<?= "k{$value->no}1" ?>" data-tt-parent-id="<?= "k{$value->no}" ?>">
                    <td></td>
                    <td>Jurnal UM <?= $value->no ?></td>
                    <td colspan="2">
                        <select class="form-control input-sm select22 jurnal_um" style="width:80%"
                                data-posisi="D" data-jenis="uangmuka" data-menu="<?= $value->nama_menu ?>"
                                data-ids = "<?= $value->ids ?>" data-saldo="<?=$value->nominal?>" data-no="<?= $value->no ?>" data-selisih="<?= $selisih ?>">
                            <option value=""></option>
                                    <?php
                                    foreach ($coas as $keys => $values) {
                                        ?>
                                <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $coaD) ? "selected" : "" ?>><?= "{$values->kode_coa} {$values->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="text-right"><?= number_format(abs($selisih), 2) ?></td>
                    <td class="text-right">0</td>
                </tr>
                <tr data-tt-id="<?= "k{$value->no}1" ?>" data-tt-parent-id="<?= "k{$value->no}" ?>">
                    <td></td>
                    <td>Jurnal UM <?= $value->no ?></td>
                    <td colspan="2">
                        <select class="form-control input-sm select22 jurnal_um" style="width:80%"
                                data-posisi="C" data-jenis="uangmuka" data-no="<?= $value->no ?>" data-selisih="<?= $selisih ?>">
                            <option value=""></option>
                                    <?php
                                    foreach ($coas as $keys => $values) {
                                        ?>
                                <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $coaC) ? "selected" : "" ?>><?= "{$values->kode_coa} {$values->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="text-right">0</td>
                    <td class="text-right"><?= number_format(abs($selisih), 2) ?></td>
                </tr>
            <?php }
            ?>
        </tbody>
    </table>
</div>

<div class="col-md-12">
    <table class="table table-condesed table-hover rlstable  over" id="tbl-dp" width="100%">
        <caption>Pelunasan Piutang (Deposit)</caption>
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
        <th class="text-right">
            Selisih
        </th>
        </thead>
        <tbody>
            <?php
            foreach ($deposit as $key => $value) {
                $oldKurs = ($value->kurs_akhir > 0) ? $value->kurs_akhir : $value->kurs;
                $saldo = $value->total_piutang - $value->total_pelunasan;
                $selisih = ($saldo * $kurs) - ($saldo * $oldKurs);
//                $saldo = abs($saldo);
                ?>
                <tr data-tt-id="<?= "k{$value->no_pelunasan}" ?>" data-tt-parent-id="">
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
                    <td class="text-right">
                        <?= number_format($selisih, 2) ?>
                    </td>
                </tr>
                <?php
                if ($selisih == 0) {
                    continue;
                }
                $coaD = ($selisih > 0) ? "8241.01" : "1163.01";
                $coaC = ($selisih > 0) ? "1163.01" : "8141.01";
                ?>
                <tr data-tt-id="<?= "k{$value->no_pelunasan}1" ?>" data-tt-parent-id="<?= "" ?>">
                    <th>Nama</th>
                    <th colspan="2">COA</th>
                    <th class="text-right">D</th>
                    <th class="text-right">C</th>
                </tr>
                <tr data-tt-id="<?= "k{$value->no_pelunasan}1" ?>" data-tt-parent-id="<?= "k{$value->no_pelunasan}" ?>">
                    <td>Jurnal Deposit <?= $value->no_pelunasan ?></td>
                    <td colspan="2">
                        <select class="form-control input-sm select22 jurnal_dep" style="width:80%" data-menu="pelunasan_piutang"
                                data-posisi="D" data-jenis="deposit" data-saldo="<?=$value->total_pelunasan?>" data-no="<?= $value->no_pelunasan ?>" data-selisih="<?= $selisih ?>">
                            <option value=""></option>
                                    <?php
                                    foreach ($coas as $keys => $values) {
                                        ?>
                                <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $coaD) ? "selected" : "" ?>><?= "{$values->kode_coa} {$values->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="text-right"><?= number_format(abs($selisih), 2) ?></td>
                    <td class="text-right">0</td>
                </tr>
                <tr data-tt-id="<?= "k{$value->no_pelunasan}1" ?>" data-tt-parent-id="<?= "k{$value->no_pelunasan}" ?>">
                    <td>Jurnal Deposit <?= $value->no_pelunasan ?></td>
                    <td colspan="2">
                        <select class="form-control input-sm select22 jurnal_dep" style="width:80%"
                                data-posisi="C" data-jenis="deposit" data-no="<?= $value->no_pelunasan ?>" data-selisih="<?= $selisih ?>">
                            <option value=""></option>
                                    <?php
                                    foreach ($coas as $keys => $values) {
                                        ?>
                                <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $coaC) ? "selected" : "" ?>><?= "{$values->kode_coa} {$values->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="text-right">0</td>
                    <td class="text-right"><?= number_format(abs($selisih), 2) ?></td>
                </tr>
            <?php }
            ?>
        </tbody>
    </table>
</div>
<div class="col-md-12">
    <table class="table table-condesed table-hover rlstable  over" id="tbl-rtr" width="100%">
        <caption>Retur Penjualan</caption>
        <thead>
        <th class="no">
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
        <th class="text-right">
            Selisih
        </th>
        </thead>
        <tbody>
            <?php
            foreach ($retur_pen as $key => $value) {
                $oldKurs = ($value->kurs_akhir > 0) ? $value->kurs_akhir : $value->kurs;
                $selisih = ($value->final_total * $kurs) - ($value->final_total * $oldKurs);
                ?>
                <tr  data-tt-id="<?= "k{$value->no_retur}" ?>" data-tt-parent-id="">
                    <td>
                        <?= $value->no_retur ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($oldKurs, 2) ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($oldKurs * $value->final_total, 2) ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($kurs, 2) ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($value->final_total * $kurs, 2) ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($selisih, 2) ?>
                    </td>
                </tr>

                <?php
                if ($selisih == 0) {
                    continue;
                }
                $coaD = ($selisih > 0) ? $value->coa_retur : "8241.01";
                $coaC = ($selisih > 0) ? "8141.01" : $value->coa_retur;
                ?>

                <tr data-tt-id="<?= "k{$value->no_retur}1" ?>" data-tt-parent-id="<?= "k{$value->no_retur}" ?>">
                    <th colspan="2">Nama</th>
                    <th colspan="2">COA</th>
                    <th class="text-right">D</th>
                    <th class="text-right">C</th>
                </tr>
                <tr data-tt-id="<?= "k{$value->no_retur}1" ?>" data-tt-parent-id="<?= "k{$value->no_retur}" ?>">
                    <td colspan="2">Jurnal Retur Penjualan <?= $value->no_retur ?></td>
                    <td colspan="2">
                        <select class="form-control input-sm select22 jurnal_rtr" style="width:80%" data-menu="retur_penjualan"
                                data-posisi="D" data-ids="<?= $value->id ?>" data-jenis="retur" data-no="<?= $value->no_retur ?>"
                                data-selisih="<?= $selisih ?>" data-saldo="<?=$value->final_total?>">
                            <option value=""></option>
                            <?php
                            foreach ($coas as $keys => $values) {
                                ?>
                                <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $coaD) ? "selected" : "" ?>><?= "{$values->kode_coa} {$values->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="text-right"><?= number_format(abs($selisih), 2) ?></td>
                    <td class="text-right">0</td>
                </tr>
                <tr data-tt-id="<?= "k{$value->no_retur}1" ?>" data-tt-parent-id="<?= "k{$value->no_retur}" ?>">
                    <td colspan="2">Jurnal Retur Penjualan <?= $value->no_retur ?></td>
                    <td colspan="2">
                        <select class="form-control input-sm select22 jurnal_rtr" style="width:80%"
                                data-posisi="C" data-ids="<?= $value->id ?>" data-jenis="retur" data-no="<?= $value->no_retur ?>"
                                data-selisih="<?= $selisih ?>" >
                            <option value=""></option>
                            <?php
                            foreach ($coas as $keys => $values) {
                                ?>
                                <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $coaC) ? "selected" : "" ?>><?= "{$values->kode_coa} {$values->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="text-right">0</td>
                    <td class="text-right"><?= number_format(abs($selisih), 2) ?></td>
                </tr>


                <?php
            }
            ?>
        </tbody>
    </table>
</div>

<div class="col-md-12">
    <table class="table table-condesed table-hover rlstable  over" id="tbl-rtr" width="100%">
        <caption>Retur Pembelian</caption>
        <thead>
        <th class="no">
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
        <th class="text-right">
            Selisih
        </th>
        </thead>
        <tbody>
            <?php
            foreach ($retur_pem as $key => $value) {
                $oldKurs = ($value->kurs_akhir > 0) ? $value->kurs_akhir : $value->nilai_matauang;
                $selisih = ($value->total_valas * $kurs) - ($value->total_valas * $oldKurs);
                ?>
                <tr  data-tt-id="<?= "k{$value->no_inv_retur}" ?>" data-tt-parent-id="">
                    <td>
                        <?= $value->no_inv_retur ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($oldKurs, 2) ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($oldKurs * $value->total_valas, 2) ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($kurs, 2) ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($value->total_valas * $kurs, 2) ?>
                    </td>
                    <td class="text-right">
                        <?= number_format($selisih, 2) ?>
                    </td>
                </tr>

                <?php
                if ($selisih == 0) {
                    continue;
                }
                $coaD = ($selisih > 0) ? $value->coa_piutang_dagang : "8241.01";
                $coaC = ($selisih > 0) ? "8141.01" : $value->coa_piutang_dagang;
                ?>

                <tr data-tt-id="<?= "k{$value->no_inv_retur}1" ?>" data-tt-parent-id="<?= "k{$value->no_inv_retur}" ?>">
                    <th colspan="2">Nama</th>
                    <th colspan="2">COA</th>
                    <th class="text-right">D</th>
                    <th class="text-right">C</th>
                </tr>
                <tr data-tt-id="<?= "k{$value->no_inv_retur}1" ?>" data-tt-parent-id="<?= "k{$value->no_inv_retur}" ?>">
                    <td colspan="2">Jurnal Retur Pembelian <?= $value->no_inv_retur ?></td>
                    <td colspan="2">
                        <select class="form-control input-sm select22 jurnal_rtr" style="width:80%" data-menu="retur_pembelian"
                                data-posisi="D" data-ids="<?= $value->id ?>" data-jenis="retur" data-no="<?= $value->no_inv_retur ?>"
                                data-selisih="<?= $selisih ?>" data-saldo="<?=$value->total_valas?>">
                            <option value=""></option>
                            <?php
                            foreach ($coas as $keys => $values) {
                                ?>
                                <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $coaD) ? "selected" : "" ?>><?= "{$values->kode_coa} {$values->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="text-right"><?= number_format(abs($selisih), 2) ?></td>
                    <td class="text-right">0</td>
                </tr>
                <tr data-tt-id="<?= "k{$value->no_inv_retur}1" ?>" data-tt-parent-id="<?= "k{$value->no_inv_retur}" ?>">
                    <td colspan="2">Jurnal Retur Pembelian <?= $value->no_inv_retur ?></td>
                    <td colspan="2">
                        <select class="form-control input-sm select22 jurnal_rtr" style="width:80%"
                                data-posisi="C" data-ids="<?= $value->id ?>" data-jenis="retur" data-no="<?= $value->no_inv_retur ?>" data-selisih="<?= $selisih ?>">
                            <option value=""></option>
                            <?php
                            foreach ($coas as $keys => $values) {
                                ?>
                                <option value="<?= $values->kode_coa ?>" <?= ($values->kode_coa === $coaC) ? "selected" : "" ?>><?= "{$values->kode_coa} {$values->nama}" ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </td>
                    <td class="text-right">0</td>
                    <td class="text-right"><?= number_format(abs($selisih), 2) ?></td>
                </tr>


                <?php
            }
            ?>
        </tbody>
    </table>
</div>