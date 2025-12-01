
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
        <style>

            #TH{
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            #style_space {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap !important;
                background: #F0F0F0;
                border-top: 2px solid #ddd !important;
                border-bottom: 2px solid #ddd !important;
            }
            table, th, td {
                border: 1px solid black;
            }
            #font-wei{
                font-weight: bold !important;
            }
        </style>
    </head>
    <body>
        <div class="col-sm-4">

        </div>
        <div class="col-sm-4">
            <div  style="text-align: center;font-size: 10px; line-height: 0.2;">

                <p id="font-wei" >PT. HEKSATEX INDAH</p>
                <p id="font-wei">UMUR PIUTANG (AGING)</p>
                <br>
                <br>
                <p id="font-wei">Per Tgl. <?=tgl_indo(date('d-m-Y'));?></p>
            </div>
        </div>
        <div class="col-sm-4">

        </div>
        
        <div class="col-sm-12">
            <table cellspacing="0" style="font-size: 12px; width: 100%;border: 1px solid black;">
                <thead>
                    <tr>
                        <th id="TH">No</th>
                        <th id="TH">Customer</th>
                        <th id="TH" style="text-align: right;">Total Piutang</th>
                        <?php
                        foreach ($head as $key => $value) {
                            ?>
                            <th style="width: 150px;text-align: right;">
                                <?= $value ?>
                            </th>
                            <?php
                        }
                        ?>  
                    </tr>

                </thead>
                <tbody>
                    <?php
                    $no = 0;
                    $totalPiutang = 0;
                    $piutang_bulan_ini = 0;
                    $piutang_bulan_1 = 0;
                    $piutang_bulan_2 = 0;
                    $piutang_bulan_3 = 0;
                    $piutang_lebih_dari_3_bulan = 0;

                    foreach ($body as $key => $value) {
                        $no++;
                        $totalPiutang += $value->total_piutang;
                        $piutang_bulan_ini += $value->piutang_bulan_ini;
                        $piutang_bulan_1 += $value->piutang_bulan_1;
                        $piutang_bulan_2 += $value->piutang_bulan_2;
                        $piutang_bulan_3 += $value->piutang_bulan_3;
                        $piutang_lebih_dari_3_bulan += $value->piutang_lebih_dari_3_bulan;
                        ?>
                        <tr>
                            <td id="#style_space"><?= $no ?></td>
                            <td id="#style_space"><?= $value->partner_nama ?></td>
                            <td id="#style_space" style="text-align: right;"><?= number_format($value->total_piutang, 2) ?></td>
                            <td id="#style_space" style="text-align: right;"><?= number_format($value->piutang_bulan_ini, 2) ?></td>
                            <td id="#style_space" style="text-align: right;"><?= number_format($value->piutang_bulan_1, 2) ?></td>
                            <td id="#style_space" style="text-align: right;"><?= number_format($value->piutang_bulan_2, 2) ?></td>
                            <td id="#style_space" style="text-align: right;"><?= number_format($value->piutang_bulan_3, 2) ?></td>
                            <td id="#style_space" style="text-align: right;"><?= number_format($value->piutang_lebih_dari_3_bulan, 2) ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </body>
</html>


