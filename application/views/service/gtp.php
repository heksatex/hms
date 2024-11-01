<!doctype html>
<html>
    <head>
        <meta charset="UTF-8">
        <style>
            #tableHeader {
                background: #fff;
                border-collapse: collapse;

                width: 100%;
                padding-bottom: 2px;
            }
            #tableHeader tr td{
                text-align: left;
                vertical-align:top;
            }

            #ul_header {
                list-style-type: none;
            }

            #ul_data{
                list-style-type: none;
            }
            #ul_data > li {
                text-indent: -5px;
            }

            #ul_data > li:before {
                content: "-";
                text-indent: -5px;
            }

            .grid-container {
                display: grid;
                grid-template-columns: repeat(5, 11%);
            }
            .grid-item {
                padding: 2px;
            }

            #row:after {
                content: "";
                display: table;
                clear: both;
            }

            #column {
                float: left;
                width: 33.33%;
                padding: 10px;
                height: 100px;
                text-align: center;
                /* Should be removed. Only for demonstration */
            }

            body {
                size: a4;
            }

            table{
            }
            table thead{
                font-size: 12px;
            }
            table tfoot{
                font-size: 12px;
                font-weight: bold;
            }
            table tbody {
                font-size: 11px;
            }

        </style>
    </head>

    <body >

        <?php
        $temp = "";
        foreach ($data as $keys => $values) {
            ?>
            <div id="row">
                <div id="column">
                    <h4><?= date("Y-M-d") ?></h4>
                </div>
                <div id="column" style="padding-top: 40px;">
                    <h2>Good To Push</h2>
                </div>
                <div id="column">
                    <div style="width: 150px;margin: auto;">
                        <h2 style="border: 2px #000 solid"><?= $keys ?></h2>
                    </div>
                </div>
            </div>
            <ul style="list-style-type: none;">
                <?php
                foreach ($values as $key => $value) {
                    ?> 

                    <li>
                        <h4><?= str_replace("_", " ", $key) ?></h4>
                        <table  cellspacing="0" cellpadding="0" style="font-size: 9px;">
                            <thead>
                            <th style="width: 15px;"></th>
                            <th style="width: 100px;text-align: left">CORAK</th>
                            <th style="width: 40px;text-align: left">WARNA</th>
                            <th style="width: 30px;text-align: left">LOT</th>
                            <th style="width: 30px;text-align: left">QTY1</th>
                            <th style="width: 30px;text-align: left">UOM1</th>
                            <th style="width: 30px;text-align: left">QTY2</th>
                            <th style="width: 30px;text-align: left">UOM2</th>
                            <th style="width: 60px;text-align: left">LEBAR</th>
                            <th style="width: 100px;text-align: left">BUYER</th>
                            </thead>
                            <tbody>
                                <?php
                                $no = 0;
                                foreach ($value as $k => $datas) {
                                    $no++;
                                    ?>
                                    <tr>
                                        <td style="text-align: right"><?= $no ?></td>
                                        <td style="text-align: left"><?= $datas->corak ?? "" ?></td>
                                        <td style="text-align: right"><?= $datas->total_warna ?? 0 ?></td>
                                        <td style="text-align: right"><?= $datas->total_data ?></td>
                                        <td style="text-align: right"><?= $datas->total_qty ?></td>
                                        <td style="text-align: left"><?= $datas->uom ?></td>
                                        <td style="text-align: right"><?= $datas->total_qty2 ?></td>
                                        <td style="text-align: left"><?= $datas->uom2 ?></td>
                                        <td style="text-align: right"><?= $datas->lebar_jadi ?></td>
                                        <td style="text-align: left;padding-left: 15px"><?= substr($datas->customer_name, 0, 50) ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </li>



                    <?php
                }
            }
            ?>
        </ul>
    </body>
</html>