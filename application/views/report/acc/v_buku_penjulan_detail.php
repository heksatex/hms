<?php
$no = 0;
$grandTotal = 0;
$total = 0;
$totalHarga = 0;
if ($posisi !== "bks") {

    foreach ($data as $key => $value) {
        $sjs = explode("/", $value->no_sj);
        if (in_array($sjs[0], ["SJM", "SAMPLE"])) {
            $value->nominal = 0;
            $value->harga = 0;
        }
        $harga = ($value->harga * $value->qty) * $value->kurs;
        $total += $value->nominal;
        $totalHarga += $harga;
        $grandTotal += ($value->qty) ? $harga : $value->nominal;
        $no++;
        $qty = explode("/ ", $value->nama);
        $qtys = (count($qty) > 1) ? end($qty) : "";
        $nama = (count($qty) > 1) ? "{$value->uraian} {$value->warna}" : "";
        $totalanItem = ($value->qty) ? number_format($harga, 2) : number_format($value->nominal, 2);
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= $value->no_faktur_internal ?></td>
            <td><?= $value->no_inv_ekspor ?></td>
            <td><?= $value->no_sj ?></td>
            <td><?= $value->no_faktur_pajak ?></td>
            <td><?= $value->tanggal ?></td>
            <td><?= $nama ?></td>
            <td><?= $value->partner_nama ?></td>
            <td><?= "{$value->kode_coa} - {$value->coa}" ?></td>
            <td><?= $value->jenis_ppn ?></td>
            <td><?= $value->kode_mua ?></td>
            <td class="text-right"><?= number_format($value->kurs, 2) ?></td>
            <td style="text-align: right;"><?= ($value->qty) ? number_format($value->qty, 2) . " {$value->uom}" : $qtys ?></td>
            <td style="text-align: right;"><?= ($value->harga > 0) ? number_format($value->harga, 2) : $totalanItem ?></td>
            <td style="text-align: right;"><?= $totalanItem ?></td>

        </tr>
        <?php
        if (isset($data[$key + 1])) {
            if ($value->kode_coa !== $data[$key + 1]->kode_coa) {
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-bold"><?= "Total {$value->coa}" ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-bold" style="text-align: right;"><?= ($value->qty) ? number_format($totalHarga, 2) : number_format($total, 2) ?></td>

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
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php
                $total = 0;
                $totalHarga = 0;
            }
        } else {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-bold"><?= "Total {$value->coa}" ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-bold" style="text-align: right;"><?= ($value->qty) ? number_format($totalHarga, 2) : number_format($total, 2) ?></td>

            </tr>
            <?php
        }
    }
    if ($grandTotal > 0) {
        ?>
        <tr>
            <td>
                &nbsp;
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-bold"><?= "Grand Total " ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right;" class="text-bold"><?= number_format($grandTotal, 2) ?></td>
        </tr>
        <?php
    }
} else {
    $totalHargaValas = 0;
    $totalPpn = 0;
    $GrandtotalRp = 0;
    foreach ($data as $key => $value) {
        $no++;
        $sjs = explode("/", $value->no_sj);
        if (in_array($sjs[0], ["SJM", "SAMPLE"])) {
            $value->nominal = 0;
            $value->harga = 0;
        }
        $JumlahRp = ($value->qty * $value->harga) * $value->kurs;
        $ppn = $value->pajak * $value->kurs;
        $totalPpn += $ppn;
        $GrandtotalRp += $JumlahRp;
        $qty = explode("/ ", $value->nama);
        $qtys = (count($qty) > 1) ? end($qty) : "";
        $nama = (count($qty) > 1) ? "{$value->uraian}" : "";
        $nama .= ($value->warna === "") ? "" : "/{$value->warna}";
        $totalanItem = ($value->qty) ? number_format($JumlahRp, 2) : number_format($value->nominal, 2);
        $hargaRp = 0;
        $hargaValas = 0;
        $TotalRp = $JumlahRp + $ppn;
        $JumlahValas = 0;
        if ($value->kurs > 1) {
            $hargaValas = $value->harga;
            $JumlahValas = $value->qty * $value->harga;
            $totalHargaValas += $JumlahValas;
        } else {
            $hargaRp = $value->harga;
        }
        $totalHarga += $JumlahRp;
        $grandTotal += $TotalRp;
        $total += $TotalRp;
        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= $value->no_faktur_internal ?></td>
            <td><?= $value->no_inv_ekspor ?></td>
            <td><?= $value->no_sj ?></td>
            <td><?= $value->no_faktur_pajak ?></td>
            <td><?= $value->tanggal ?></td>
            <td><?= $nama ?></td>
            <td><?= $value->partner_nama ?></td>
            <td><?= "{$value->kode_coa} - {$value->coa}" ?></td>
            <td><?= $value->jenis_ppn ?></td>
            <td><?= $value->kode_mua ?></td>
            <td style="text-align: right;"><?= ($value->qty) ? number_format($value->qty, 2) . " {$value->uom}" : $qtys ?></td>
            <td class="text-right"><?= number_format($value->kurs, 2) ?></td>
            <td class="text-right"><?= number_format($hargaValas, 2) ?></td>
            <td class="text-right"><?= number_format($hargaRp, 2) ?></td>
            <td style="text-align: right;"><?= number_format($JumlahValas, 2) ?></td>
            <td style="text-align: right;"><?= number_format($JumlahRp, 2) ?></td>
            <td style="text-align: right;"><?= number_format($ppn, 2) ?></td>
            <td style="text-align: right;"><?= number_format($TotalRp, 2) ?></td>
        </tr>
        <?php
        if (isset($data[$key + 1])) {
            if ($value->kode_coa !== $data[$key + 1]->kode_coa) {
                ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?= "Total {$value->coa}" ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right"></td>
                    <td style="text-align: right;"></td>
                    <td style="text-align: right;"></td>
                    <td style="text-align: right;"><?= number_format($totalHargaValas, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($totalHarga, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($totalPpn, 2) ?></td>
                    <td style="text-align: right;"><?= number_format($total, 2) ?></td>
                </tr>
                <?php
                $total = 0;
                $totalHarga = 0;
                $totalHargaValas = 0;
                $JumlahValas = 0;
                $JumlahRp = 0;
                $totalPpn = 0;
            }
        } else {
            ?>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td><?= "Total {$value->coa}" ?></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="text-right"></td>
                <td style="text-align: right;"></td>
                <td style="text-align: right;"></td>
                <td style="text-align: right;"><?= number_format($totalHargaValas, 2) ?></td>
                <td style="text-align: right;"><?= number_format($totalHarga, 2) ?></td>
                <td style="text-align: right;"><?= number_format($totalPpn, 2) ?></td>
                <td style="text-align: right;"><?= number_format($total, 2) ?></td>
            </tr>
            <?php
        }
    }
    if ($GrandtotalRp > 0) {
        ?>
        <tr>
            <td>
                &nbsp;
            </td>
        </tr>
        <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-bold"><?= "Grand Total Rp" ?></td>
            <td></td>
            <td></td>
            <td></td>
            <td class="text-right"></td>
            <td style="text-align: right;"></td>
            <td style="text-align: right;"></td>
            <td style="text-align: right;"></td>
            <td style="text-align: right;"></td>
            <td style="text-align: right;"></td>
            <td style="text-align: right;" class="text-bold"><?= number_format($GrandtotalRp, 2) ?></td>
        </tr>
        <?php
    }
}
?>