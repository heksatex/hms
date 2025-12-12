<div class="box-header with-border">
    <h4 class="box-title">Preview</h4>
</div>
<div class="col-md-12">
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

                $selisih = ($value->saldo_valas_final * $kurs) - $value->saldo_rp_final;
                $nominal = abs($selisih);
                if ($value->saldo_valas_final <= 0 || $nominal === (double) 0) {
                    continue;
                }
                $no += 1;
                $nama = "Kurs Akhir Bulan (Saldo : " . number_format($value->saldo_valas_final, 2) . " {$curr} Kurs : " . number_format($kurs, 2) . ")";
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
            ?>
        </tbody>
    </table>
</div>