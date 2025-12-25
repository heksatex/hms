<?php

if ($valas) {
    ?>
    <tr>
        <th  class="style bb ws no" rowspan="2" style="vertical-align : middle;text-align:center;">No</th>
        <th class="style bb ws" rowspan="2" style="vertical-align : middle;text-align:center;">Tanggal</th>
        <th class="style bb ws" rowspan="2" style="vertical-align : middle;text-align:center;">No Bukti</th>
        <th class="style bb ws" rowspan="2" style="vertical-align : middle;text-align:center;">Uraian</th>
        <th class="style bb ws" rowspan="2" style="vertical-align : middle;text-align:center;">No Acc</th>
        <th class="style bb ws text-center" colspan="2">USD</th>
        <th class="style bb ws text-center" colspan="2">EURO</th>
        <th class="style bb ws text-right" rowspan="2" style="vertical-align : middle;text-align:center;">Saldo</th>
    </tr>
    <tr>
        <th class="style bb ws text-right">Debet</th>
        <th class="style bb ws text-right">Kredit</th>
        <th class="style bb ws text-right">Debet</th>
        <th class="style bb ws text-right">Kredit</th>
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