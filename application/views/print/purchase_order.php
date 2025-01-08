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
        </div>
        <hr style="border: 2px solid black">
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
            <div id="columnmd" style="text-align: left;">
                <strong>Shipping Address&nbsp;:</strong>
            </div>
            <div id="columnmd" style="text-align: left;font-size: 12px;">
                <?= $po->alamat_kirim ?>
            </div>
        </div>
        <div id="row">
            <div id="columnmd" style="text-align: left;">
                <h2>Purchase Order Confirmation No <?= $po->no_po ?></h2>
            </div>
        </div>
        <div id="row">
            <div id="columnmd">
                <div id="row" style="margin-top: -30px;font-size: 12px;" >
                    <div id="column" style="text-align: left">
                        <p><strong>Our Order Reference : </strong></p>
                        <p><?= $po->no_po ?></p>
                    </div>
                    <div id="column" style="text-align: left">
                        <p><strong>Order Date : </strong></p>
                        <p><?= $po->order_date ?></p>
                    </div>
                    <div id="column" style="text-align: left">
                        <p><strong>Validated By : </strong></p>
                        <p><?= $po->validated_by ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="row">
            <table style="width: 100%;font-size: 12px;">
                <thead>
                    <tr>
                        <th style="text-align: left;border-bottom: 1px solid black">Description</th>
                        <th style="border-bottom: 1px solid black">Taxes</th>
                        <th style="border-bottom: 1px solid black">Date Req</th>
                        <th style="border-bottom: 1px solid black">Qty</th>
                        <th style="text-align: right;border-bottom: 1px solid black">Unit Price</th>
                        <th style="text-align: right;border-bottom: 1px solid black">Discount</th>
                        <th style="text-align: right;border-bottom: 1px solid black">Net Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $dataPajak = [];
                    $jumlah = 0;
                    $subtotal1 = 0;
                    $totalDiskon = 0;
                    $totalTax = 0;
                    foreach ($po_items as $key => $value) {
                        $jumlah = $value->harga_per_uom_beli * $value->qty_beli;
                        $subtotal1 += $jumlah;
                        $totalDiskon += $value->diskon;
                        $tax = ($jumlah - $value->diskon) * $value->amount_tax;
                        $totalTax += $tax;
                        ?>
                        <tr>
                            <td>
                                [<?= $value->kode_produk ?>] <?= $value->nama_produk ?>
                            </td>
                            <td><?= $value->tax_name ?></td>
                            <td><?= $po->order_date ?></td>
                            <td style="text-align: center;">
                                <?= number_format($value->qty_beli, 2) . " " . $value->uom_beli ?>
                            </td>
                            <td style="text-align: right;"><?= number_format($value->harga_per_uom_beli, 2)?></td>
                            <td style="text-align: right;"><?= number_format($value->diskon,2) ." ".$po->matauang ?></td>
                            <td style="text-align: right;"><?= number_format($jumlah, 2) ." ".$po->matauang?></td>
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
                        <td></td>
                        <td></td>
                        <td style="text-align: right;border-top: 1px solid black;"><strong>Total Without Taxes</strong></td>
                        <td style="text-align: right;"><?= number_format($subtotal1,2) ." ".$po->matauang?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;border-top: 1px solid black;">Discount</td>
                        <td style="text-align: right;"><?= number_format($totalDiskon,2) ." ".$po->matauang?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;border-top: 1px solid grey;">Taxes</td>
                        <td style="text-align: right;"><?= number_format($totalTax,2) ." ".$po->matauang?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: right;border-top: 1px solid black;"><strong>Total</strong></td>
                        <td style="text-align: right;"><?= number_format($subtotal2 + $totalTax,2) ." ".$po->matauang?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </body>
</html>