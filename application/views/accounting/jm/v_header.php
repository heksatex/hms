<?php

switch (true) {
    case ($header === "pen_kb_global" || $header === "peng_kb_global" || $header === "pen_g_global"
            || $header === "peng_g_global" || $header === "pel_p_global" || $header === "pel_h_global" || $header === "pemb_global"):
        ?>
        <tr>
            <th class="style bb ws no" >No</th>
            <th class="style bb ws" >Nama Perkiraan</th>
            <th class="style bb ws td-no-perk">No Perk</th>
            <th class="style bb ws text-right td-nominal" >Debet</th>
            <th class="style bb ws text-right td-nominal" >Kredit</th>
        </tr>
        <?php

        break;
    case ($header === "pen_kb_detail" || $header === "pen_g_detail" || $header === "peng_g_detail" || $header === "pel_h_detail" || $header === "pel_p_detail"
            || $header === "pemb_detail"):
        ?>
        <tr>
            <th class="style bb ws no" >Tanggal</th>
            <th class="style bb ws" style="width: 120px">No Bukti</th>
            <th class="style bb ws" >Uraian</th>
            <th class="style bb ws" >Dari</th>
            <th class="style bb ws text-right td-nominal" >Nominal</th>
            <th class="style bb ws" style="width: 80px">No Perk</th>
            <th class="style bb ws" >Per Posisi Kedit</th>
            <th class="style bb ws text-right td-nominal" >Jumlah</th>
        </tr>
        <?php

        break;

    case ($header === "pen_g_detail_2" || $header === "peng_g_detail_2" || $header === "peng_kb_detail" || $header === "pel_p_detail_2" || $header === "pel_h_detail_2"
            || $header === "pemb_detail_2"):
        ?>
        <tr>
            <th class="style bb ws no">Tanggal</th>
            <th class="style bb ws" style="width: 120px">No Bukti</th>
            <th class="style bb ws">Uraian</th>
            <th class="style bb ws">Dari</th>
            <th class="style bb ws text-right td-nominal" >Nominal</th>
            <th class="style bb ws" style="width: 80px">No Perk</th>
            <th class="style bb ws">Per Posisi Debet</th>
            <th class="style bb ws text-right td-nominal">Jumlah</th>
        </tr>
        <?php

        break;

    case ($header === "pen_b_global" || $header === "peng_b_global"  || $header === "pen_kv_global" || $header === "peng_kv_global"):
        ?>
        <tr>
            <th class="style bb ws no" >No</th>
            <th class="style bb ws no" >Nama Perkiraan</th>
            <th class="style bb ws" style="width: 80px">No Perk</th>
            <th class="style bb ws text-right td-nominal" >Valas</th>
            <th class="style bb ws text-right td-nominal" >Debet</th>
            <th class="style bb ws text-right td-nominal" >Kredit</th>
        </tr>
        <?php

        break;
    case ($header === "pen_b_detail" || $header === "peng_b_detail" || $header === "pen_kv_detail" || $header === "peng_kv_detail" ):
        ?>
        <tr>
            <th class="style bb ws no" >Tanggal</th>
            <th class="style bb ws" style="width: 120px">No Bukti</th>
            <th class="style bb ws" >Uraian</th>
            <th class="style bb ws" >Dari</th>
            <th class="style bb ws text-right td-nominal" >Valas</th>
            <th class="style bb ws text-right" >Kurs</th>
            <th class="style bb ws text-right td-nominal" >Nominal</th>
            <th class="style bb ws" style="width: 80px">No Perk</th>
            <th class="style bb ws" >Per Posisi Kredit</th>
            <th class="style bb ws text-right td-nominal" >Jumlah</th>
        </tr>
        <?php

        break;

    case ($header === "pen_b_detail_2" || $header === "peng_b_detail_2" || $header === "peng_kv_detail"):
        ?>
        <tr>
            <th class="style bb ws no" >Tanggal</th>
            <th class="style bb ws" style="width: 120px">No Bukti</th>
            <th class="style bb ws" >Uraian</th>
            <th class="style bb ws" >Dari</th>
            <th class="style bb ws text-right td-nominal" >Valas</th>
            <th class="style bb ws text-right" >Kurs</th>
            <th class="style bb ws text-right td-nominal" >Nominal</th>
            <th class="style bb ws" style="width: 80px">No Perk</th>
            <th class="style bb ws" >Per Posisi Debet</th>
            <th class="style bb ws text-right td-nominal" >Jumlah</th>
        </tr>
        <?php

        break;

    default:
        break;
}