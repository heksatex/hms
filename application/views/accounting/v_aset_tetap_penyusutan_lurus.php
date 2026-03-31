<table id="tbl-penyu" class="table table-striped">
    <thead>
        <tr>
            <th class="no">Tahun</th>
            <th>Penyusutan (Tahun)</th>
            <th>Tgl Penyusutan</th>
            <th>Penyusutan (Bulan)</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $temp = 0;
        $totalTahunan = 0;
        $totalBulanan = 0;
        foreach ($data as $key => $value) {
            $tahun = "";
            $nomTahun = "";
            $totalBulanan +=$value->bulan_penyu;
            if ($temp !== $value->tahun){
                $tahun = $value->tahun;
                $nomTahun =  number_format($value->tahun_penyu,2);
                $totalTahunan += $value->tahun_penyu;
            }
            ?>
        <tr>
            <td>
                <?= $tahun ?>
            </td>
            <td>
                <?= $nomTahun ?>
            </td>
            <td>
                <?= $value->tanggal ?>
            </td>
            <td>
                <?= number_format($value->bulan_penyu,2) ?>
            </td>
        </tr>
        <?php
        $temp = $value->tahun;
        }
        ?>
    </tbody>
    <tfoot>
        <tr>
            <th><strong>Total Penyusutan</strong></th>
            <th><?= number_format($totalTahunan,2) ?></th>
            <th></th>
            <th><?= number_format($totalBulanan,2) ?></th>
        </tr>
    </tfoot>
</table>
<script>
    const table = $("#tbl-penyu").DataTable({
        iDisplayLength: 12,
        ordering: false,
        lengthChange: false
    });
</script>