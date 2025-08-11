<!doctype html>
<html lang="en">
    <meta charset="UTF-8">
    <head>
        <style>
            .header {
                width: 100%;
                height: 80px;
                display: block;
            }
            #row:after {
                content: "";
                display: table;
                clear: both;
            }

            #column {
                float: left;
                width: 33.33%;
                height: 60px;
                text-align: center;
                /* Should be removed. Only for demonstration */
            }
        </style>
    </head>
    <body>
        <?php
        foreach ($data as $keys => $values) {
            ?>
            <div id="row">
                <div id="column">
                    <h4>Data &nbsp;:&nbsp;<?= $date ?></h4>
                </div>
                <div id="column" style="padding-top: 40px;">
                    <h2>GOODS To PUSH</h2>
                </div> 
                <div id="column">
                    <div style="width: 150px;margin: auto;">
                        <h4 style="border: 2px solid black;"><?= $keys ?></h4>
                    </div>
                </div>
            </div>
            <?php
            foreach ($values as $key => $value) {
                if (strpos($key, "Greige") !== false) {
                    $go = false;
                } else {
                    $go = true;
                }
                ?>
                <h4><?= str_replace("_", " ", $key) ?></h4>
                <table cellspacing="0" style="font-size: 10px; width: 100%">
                    <thead>
                        <tr>
                            <th style="width: 10px;"></th>
                            <th style="text-align: left">CORAK</th>
                            <?php
                            if ($go)
                                echo '<th style="width: 30px;text-align: center;">WARNA</th>';
                            ?> 

                            <th style="width: 30px;text-align: center">LOT</th>
                            <th style="width: 35px;text-align: center">QTY1</th>
                            <th style="width: 20px;text-align: left">UOM1</th>
                            <th style="width: 35px;text-align: center">QTY2</th>
                            <th style="width: 20px;text-align: left">UOM2</th>
                            <th style="width: 90px;text-align: center">LEBAR</th>
                            <th style="text-align: left">BUYER</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 0;
                        foreach ($value as $k => $datas) {
                            $no++;
                            ?>
                            <tr>
                                <td style="text-align: center"><?= $no ?></td>
                                <td style="text-align: left">
                                    <?php
                                    if ($datas->lokasi === "GJD/Stock") {
                                        $lbrjd = explode(" ", $datas->lebar_jadi);
                                        array_pop($lbrjd);
                                        $lbrjd = implode(" ", $lbrjd);
                                        ?>
                                        <a href="<?= getIpPubic("hms/report/marketing/stockbyproductitems?id=" . urlencode($datas->corak) . "&lebar_jadi=" . urlencode($lbrjd) . "&uom=" . urlencode($datas->uom) . "&product=" . urlencode($datas->corak) . "&cmbMarketing=All") ?>"
                                           target="_blank"><?= substr($datas->corak, 0, 25) ?></a>
                                           <?php
                                       } else {
                                           ?>
                                           <?= substr($datas->corak, 0, 25) ?>
                                           <?php
                                       }
                                       ?>

                                </td>
                                <?php
                                if ($go)
                                    echo "<td style='text-align: right'>{$datas->total_warna}</td>";
                                ?>
                                <td style="text-align: right;padding-right: 5px"><?= $datas->total_data ?></td>
                                <td style="text-align: right"><?= $datas->total_qty ?></td>
                                <td style="text-align: left"><?= $datas->uom ?></td>
                                <td style="text-align: right"><?= $datas->total_qty2 ?></td>
                                <td style="text-align: left"><?= $datas->uom2 ?></td>
                                <td style="text-align: right"><?= $datas->lebar_jadi ?></td>
                                <td style="text-align: left;padding-left: 15px"><?= substr($datas->customer_name, 0, 20) ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }
        }
        ?>

    </body>
</html>