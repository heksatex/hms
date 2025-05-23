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
                        $getTax = new $this->m_global;
                        $getTax->setTables("tax");
                        $dataPajak = [];
                        $subtotal1 = 0;
                        $totalDiskon = 0;
                        $totalTax = 0;
                        $pajakLain = [];
                        foreach ($invDetail as $key => $value) {
                            $taxe = 0;
                            $base = 0;
                            $jumlah = $value->harga_satuan * $value->qty_beli;
                            $subtotal1 += $jumlah;
                            $totalDiskon += $value->diskon;
                            if ($setting !== null && $value->dpp_tax === "1") {
                                $base = ((($jumlah - $value->diskon) * 11) / 12);
                                $taxe += $base * $value->amount_tax;
                            } else {
                                $base = ($jumlah - $value->diskon);
                                $taxe += $base * $value->amount_tax;
                            }
                            if (isset($dataPajak[$value->pajak_ket])) {
                                $dataPajak[$value->pajak_ket]["base"] += $base;
                                $dataPajak[$value->pajak_ket]["nominal"] += $taxe;
                            } else {
                                $dataPajak[$value->pajak_ket] = [
                                    "nama" => $value->pajak,
                                    "ket" => $value->pajak_ket,
                                    "base" => $base,
                                    "nominal" => $taxe
                                ];
                            }

                            if ($value->tax_lain_id !== "0") {
                                $dataTax = $getTax->setWhereIn("id", explode(",", $value->tax_lain_id), true)->setOrder(["id"])->getData();
                                foreach ($dataTax as $kkk => $datass) {
                                    $taxx = 0;
                                    $bases = 0;
                                    if ($setting !== null && $datass->dpp === "1") {
                                        $bases = ((($jumlah - $value->diskon) * 11) / 12);
                                        $taxx += $bases * $datass->amount;
                                    } else {
                                        $bases = ($jumlah - $value->diskon);
                                        $taxx += $bases * $datass->amount;
                                    }
                                    $taxe += $taxx;
                                    if (isset($dataPajak[$datass->ket])) {
                                        $dataPajak[$datass->ket]["base"] += $bases;
                                        $dataPajak[$datass->ket]["nominal"] += $taxx;
                                    } else {
                                        $dataPajak[$datass->ket] = [
                                            "nama" => $datass->nama,
                                            "ket" => $datass->ket,
                                            "base" => $bases,
                                            "nominal" => $taxx
                                        ];
                                    }
                                }
                            }

                            $totalTax += $taxe;
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
                            <td style="border-top: 1px solid black;"><strong>Subtotal</strong></td>
                            <td colspan="2" style="border-top: 1px solid black;text-align: right;"><?= number_format($subtotal1, 2) ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border-bottom: 1px solid black;"><strong>Diskon</strong></td>
                            <td colspan="2" style="border-bottom: 1px solid black;text-align: right;"><?= number_format($totalDiskon, 2) ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><strong>Subtotal 2</strong></td>
                            <td colspan="2" style="text-align: right;"><?= number_format($subtotal2, 2) ?></td>
                        </tr>
                        <?php if ($setting !== null) {
                            ?>
                            <tr> 
                                <td></td>
                                <td></td>
                                <td style="border-bottom: 1px solid black">DPP Nilai Lain</td>
                                <td colspan="2" style="border-bottom: 1px solid black;text-align: right;"><?= number_format(((($subtotal1 - $totalDiskon) * 11) / 12) * $inv->nilai_matauang, 2) ?></td>

                            </tr>
                        <?php }
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border-bottom: 1px solid black">Taxes</td>
                            <td colspan="2" style="border-bottom: 1px solid black;text-align: right;"><?= number_format($totalTax, 2) ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td style="border-bottom: 1px solid black;font-weight: 600"><strong>Total</strong></td>
                            <td colspan="2" style="border-bottom: 1px solid black;text-align: right;"><?= number_format($subtotal2 + $totalTax, 2) ?></td>
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
                        <table style="width: 100%;font-size: 12px;">
                            <thead >
                                <tr>
                                    <th style="text-align: left;border-bottom: 1px solid black">Tax</th>
                                    <th style="border-bottom: 1px solid black">Base</th>
                                    <th style="border-bottom: 1px solid black">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($totalTax > 0) { 
                                    foreach ($dataPajak as $k => $v) {
                                        $v = (object)$v;
                                        ?>
                                        <tr>
                                            <td>
                                                <?= $v->ket ?>
                                            </td>
                                            <td style="text-align: right">
                                                IDR <?= number_format(($v->base * $inv->nilai_matauang), 4) ?>
                                            </td>
                                            <td style="text-align: right">
                                                IDR <?= number_format(($v->nominal * $inv->nilai_matauang), 4) ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
                <?php
            }
            ?>
        </div>
    </body>
</html>