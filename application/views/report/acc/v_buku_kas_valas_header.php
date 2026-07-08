<?php

if ($valas) {
    ?>


    <tr>
        <th  class="style bb ws no" >No</th>
        <th class="style bb ws">Tanggal</th>
        <th class="style bb ws">No Bukti</th>
        <th class="style bb ws">Uraian</th>
        <th class="style bb ws">No Acc</th>
        <th class="style bb ws">Kurs</th>
        <th class="style bb ws text-right">Debet</th>
        <th class="style bb ws text-right">Kredit</th>
        <th class="style bb ws text-right">Saldo</th>
        <th class="style bb ws text-right">Debet Rp</th>
        <th class="style bb ws text-right">Kredit Rp</th>
        <th class="style bb ws text-right">Saldo Rp</th>
    </tr>
    <?php

} else {
    ?>
    <tr>
        <th  class="style bb ws no" >No</th>
        <th class="style bb ws">Tanggal</th>
        <th class="style bb ws">No Bukti</th>
        <th class="style bb ws">Uraian</th>
        <th class="style bb ws">No Acc</th>
        <th class="style bb ws text-right">Debet</th>
        <th class="style bb ws text-right">Kredit</th>
        <th class="style bb ws text-right">Saldo</th>
    </tr>
    <?php

}
?>