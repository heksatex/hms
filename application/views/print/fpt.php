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
                <div style="margin: 10px;
                     padding: 10px;
                     min-height: 200px;" >
                    <img style="height:50px;
                         float:left;
                         margin:0 20px 10px 0;" src="http://10.10.0.8/hms_staging_2/dist/img/static/heksatex.jpg">
                    <p style="line-height:1.5;font-size: 10px">
                        <span>PT. Heksatex Indah</span><br/>
                        <span>Jl. Nanjung KM 2</span><br/>
                        <span>Cimahi 40533</span><br/>
                        <span>Indonesia</span>
                    </p>
                </div>
        </div>
        <hr style="border: 2px solid black">
        <div id="row">
            <div id="columnmd">

            </div>
            <div id="columnmd"  style="text-align: left;font-size: 11px;">
                <p style="line-height: 1.5"><strong><?= $po->supp ?></strong></p>
                <p><?= $po->alamat_kirim ?></p>
            </div>
        </div>
        <div id="row" style="font-size: 15px;">
            <p>Form Pembelian Tunai <?= $po->no_po ?></p>
        </div>

        <!--        <div id="row">
                    <div id="columnmd">-->
        <div id="row" style="font-size: 12px;" >
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
        <!--            </div>
                </div>-->
        <div id="row">
            <table style="width: 100%;font-size: 12px;">
                <thead>
                    <tr>
                        <th style="text-align: left;border-bottom: 1px solid black">Description</th>
                        <th style="border-bottom: 1px solid black">Taxes</th>
                        <th style="border-bottom: 1px solid black">Date Req</th>
                        <th style="border-bottom: 1px solid black">Qty</th>
                        <th style="text-align: right;border-bottom: 1px solid black">Unit Price&nbsp;</th>
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
                    $nilaiDppLain = 0;
                    foreach ($po_items as $key => $value) {
                        $jumlah = $value->harga_per_uom_beli * $value->qty_beli;
                        $subtotal1 += $jumlah;
                        $totalDiskon += $value->diskon;
//                        $tax = ($jumlah - $value->diskon) * $value->amount_tax;
//                        $totalTax += $tax;
                        if ($setting !== null) {
                            $totalTax += ((($jumlah - $value->diskon) * 11) / 12) * $value->amount_tax;
                            $nilaiDppLain += ((($jumlah - $value->diskon) * 11) / 12);
                        } else {
                            $totalTax += ($jumlah - $value->diskon) * $value->amount_tax;
                        }
                        ?>
                        <tr>
                            <td>
                                <?= $value->deskripsi ?>
                            </td>
                            <td><?= $value->tax_name ?></td>
                            <td style="text-align: center;"><?= $value->schedule_date ?? "" ?></td>
                            <td style="text-align: center;">
                                <?= number_format($value->qty_beli, 2) . " " . $value->uom_beli ?>
                            </td>
                            <td style="text-align: right;"><?= number_format($value->harga_per_uom_beli, 2) ?>&nbsp;</td>
                            <td style="text-align: right;"><?= number_format($value->diskon, 2) . " " . $po->matauang ?></td>
                            <td style="text-align: right;">&nbsp;<?= number_format($jumlah, 2) . " " . $po->matauang ?></td>
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
                        <td colspan="2" style="text-align: right;border-top: 1px solid black;"><strong>Total Without Taxes</strong></td>
                        <td style="text-align: right;"><?= number_format($subtotal1, 2) . " " . $po->matauang ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;border-top: 1px solid black;">Discount</td>
                        <td style="text-align: right;"><?= number_format($totalDiskon, 2) . " " . $po->matauang ?></td>
                    </tr>
                    <?php
                    if ($setting !== null) {
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td colspan="2" style="text-align: right;border-top: 1px solid grey;">DPP Nilai Lain</td>
                            <td style="text-align: right;"><?= number_format($nilaiDppLain, 2) . " " . $po->matauang ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;border-top: 1px solid grey;">Taxes</td>
                        <td style="text-align: right;"><?= number_format($totalTax, 2) . " " . $po->matauang ?></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td colspan="2" style="text-align: right;border-top: 1px solid black;"><strong>Total</strong></td>
                        <td style="text-align: right;"><?= number_format($subtotal2 + $totalTax, 2) . " " . $po->matauang ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div id="row" style="margin-top: 50px">
            <span style="font-size: 11px;font-style: oblique;">
                <?= nl2br($po->foot_note) ?>
            </span>

        </div>
    </body>
</html>