<style>
   
</style>
<div class="col-xs-12 col-md-6">
    <?php
    foreach ($data as $key => $value) {
        ?>
        <h4>Tahun <?= ($key == ($umur - 1)) ? ($key + 1) . " (Tahun terakhir) " : ($key + 1) ?></h4>

        <?= $value->text ?>

        <?php
    }
    ?>
</div>
<div class="col-xs-12 col-md-6">
    <table id="tbl-penyu" class="table table-striped">
        <thead>
            <tr>
                <th>Nilai Buku Awal</th>
                <th>Penyusutan (Tahun)</th>
                <th>Nilai Buku Akhir</th>
                <th>Tgl Penyusutan</th>
                <th>Penyusutan (Bulan)</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $temp = 0;
            foreach ($list as $key => $value) {
                $awal = "";
                $tahun = "";
                $akhir = "";
                if ($temp !== $value->awal) {
                    $awal = number_format($value->awal, 2);
                    $tahun = number_format($value->tahunan, 2);
                    $akhir = number_format($value->akhir, 2);
                }
                ?>
                <tr>
                    <td><?= $awal ?></td>
                    <td><?= $tahun ?></td>
                    <td><?= $akhir ?></td>
                    <td><?= $value->tanggal ?></td>
                    <td><?= number_format($value->bulanan, 2) ?></td>
                </tr>
                <?php
                $temp = $value->awal;
            }
            ?>
        </tbody>
    </table>
</div>
<script>
    const table = $("#tbl-penyu").DataTable({
        iDisplayLength: 12,
        ordering: false,
        searching: false,
        lengthChange: false,
        dom: "<'row'<'col-sm-12'p>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-5'i>>",
    });
</script>
