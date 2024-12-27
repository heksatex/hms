<!doctype html>
<html>
    <head>
        <style>
            #row:after {
                content: "";
                display: table;
                clear: both;
            }
            #column {
                float: left;
                width: 33.33%;
                padding-top: 10px;
                text-align: center;
                /* Should be removed. Only for demonstration */
            }
            #columnmd {
                float: left;
                width: 50%;
                padding-top: 10px;
                text-align: center;
                /* Should be removed. Only for demonstration */
            }
        </style>
    </head>
    <body>
        <div id="row">
            <div id="column" style="text-align: left" >
                <img style="height: 50px" src="http://10.10.0.8/hms_staging_2/dist/img/static/heksatex.jpg">
            </div>
            <div id="column">
            </div>
            <div id="column">
            </div>
            <hr style="border: 2px solid black">
        </div>
        <div id="row">
            <div id="column" style="text-align: left;font-size: 10px;margin-top: -20px">
                <p>PT. Heksatex Indah</p>
                <p>Jl. Nanjung KM 2</p>
                <p>Cimahi 40216</p>
                <p>Indonesia</p>
                <hr style="border: 2px solid black">
            </div>
        </div>
        <div id="row">
            <div id="column">
            </div>
            <div id="column">
            </div>
            <div id="column" style="text-align: left;font-size: 10px;margin-top: -20px">
                <p><?= $inv->supplier ?></p>
                <p><?= $inv->delivery_street ?></p>
                <p><?= $inv->delivery_city ?></p>
            </div>
        </div>
        <div id="row">
            <div id="column" style="text-align: left;">
                <h2>Supplier Invoice</h2>
            </div>
        </div>
        <div id="row">
            <div id="columnmd">
                <div id="row" style="margin-top: -30px;font-size: 12px;" >
                    <div id="column" style="text-align: left">
                        <p><strong>Description : </strong></p>
                        <p><?= $inv->no_po ?? "" ?></p>
                    </div>
                    <div id="column" style="text-align: left">
                        <p><strong>Source : </strong></p>
                        <p><?= $inv->no_po ?? "" ?></p>
                    </div>
                    <div id="column" style="text-align: left">
                        <p><strong>Reference : </strong></p>
                        <p><?= $inv->no_po ?? "" ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="row">
            <table style="width: 100%;font-size: 12px;">
                <thead >
                    <tr>
                        <th style="text-align: left;border-bottom: 1px solid black">Description</th>
                        <th style="border-bottom: 1px solid black">Quantity</th>
                        <th style="border-bottom: 1px solid black">Unit Price</th>
                        <th style="border-bottom: 1px solid black">Taxes</th>
                        <th style="text-align: right;border-bottom: 1px solid black">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($invDetail) > 0) {
                        $dataPajak = [];
                        $jumlah = 0;
                        $subtotal1 = 0;
                        $totalDiskon = 0;
                        $totalTax = 0;
                        foreach ($invDetail as $key => $value) {
                            if (count($dataPajak) < 1) {
                                $dataPajak["ket"] = $value->pajak_ket;
                            }
                            $jumlah = $value->harga_satuan * $value->qty_beli;
                            $subtotal1 += $jumlah;
                            $totalDiskon += $value->diskon;
                            $tax = ($jumlah - $value->diskon) * $value->amount;
                            $totalTax += $tax;
                            ?>
                            <tr>
                                <td>
                                    [<?= $value->kode_produk ?>] <?= $value->nama_produk ?>
                                </td>
                                <td style="text-align: center;">
                                    <?= number_format($value->qty_beli, 2) . " " . $value->uom_beli ?>
                                </td>
                                <td style="text-align: center;"><?= number_format($value->harga_satuan, 2) ?></td>
                                <td style="text-align: center;"><?= $value->pajak_ket ?></td>
                                <td style="text-align: right;"><?= number_format($jumlah, 2) ?></td>
                            </tr>
                            <?php
                        }
                        $subtotal2 = $subtotal1 - $totalDiskon;
                        ?>
                        <tr>
                            <td style="height: 20px"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border-top: 1px solid black;"><strong>Subtotal</strong></td>
                            <td style="border-top: 1px solid black;text-align: right;"><?= number_format($subtotal1, 2) ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border-bottom: 1px solid black;"><strong>Diskon</strong></td>
                            <td style="border-bottom: 1px solid black;text-align: right;"><?= number_format($totalDiskon, 2) ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><strong>Subtotal</strong></td>
                            <td style="text-align: right;"><?= number_format($subtotal2, 2) ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border-bottom: 1px solid black">Taxes</td>
                            <td style="border-bottom: 1px solid black;text-align: right;"><?= number_format($totalTax, 2) ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border-bottom: 1px solid black;font-weight: 600"><strong>Total</strong></td>
                            <td style="border-bottom: 1px solid black;text-align: right;"><?= number_format($subtotal2 + $totalTax, 2) ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
            <?php
            if (count($invDetail) > 0) {
                ?>
                <div id="row">
                    <div id="columnmd">
                        <div id="row" style="margin-top: -30px;font-size: 12px;" >
                            <div id="column" style="text-align: left">
                                <p style="font-weight: 600; border-bottom: 1px solid black"><strong>Tax</strong></p>
                                <p><?= $dataPajak["ket"] ?? "" ?></p>
                            </div>
                            <div id="column" style="text-align: right">
                                <p style="font-weight: 600;border-bottom: 1px solid black"><strong>Base</strong></p>
                                <p><?= number_format($subtotal2, 2) ?></p>
                            </div>
                            <div id="column" style="text-align: right">
                                <p style="font-weight: 600;border-bottom: 1px solid black"><strong>Amount</strong></p>
                                <p><?= number_format($totalTax, 2) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </body>
</html>