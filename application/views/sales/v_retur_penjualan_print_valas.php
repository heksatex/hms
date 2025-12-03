<!doctype html>
<html lang="en">
    <!--<meta charset="UTF-8">-->
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
                text-align: left;
                height: 30px;
            }
            #column-news-2 {
                float: left;
                width: 30%;
                text-align: left;
            }
            #column-news-1 {
                float: left;
                width: 70%;
                text-align: left;
            }
            th,td {
                border: 1px solid black;
                border-collapse: collapse;
            }
            #alamat {
                max-width: 50px;
                font-size: 12px;
            }
        </style>
    </head>
    <body style="padding:  0 30px 0 30px; font-size: 10px;">
        <div id="row">
            <div id="column-news-1">
                <img src="<?= base_url("dist/img/logo_fp.jpg") ?>" alt="alt"/>
                </br>
                <div id="row">
                    <div id="column">
                        <p id="alamat"><?= $alamat->value ?></p>
                        <p>NPWP : <?= $npwp->value ?></p>
                    </div> 
                </div>

                <div id="row">
                    <div id="column-news-1" style=" height: 30px">
                        <h2>RETUR PENJUALAN</h2>
                    </div>
                </div>
                <div id="row" >
                    <div id="column">
                        <strong>No Faktur</strong>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= ": {$head->no_retur}" ?>
                    </div>
                    <div id="column" style="text-align: right;">
                       
                    </div>
                    <div id="column">
                        
                    </div>
                </div>
                <div id="row" >
                    <div id="column-news-1">
                        <strong>No Surat Jalan</strong>
                        <?= ": {$head->no_sj}" ?>
                    </div>
                </div>
            </div>
            <div id="column-news-2">
                <div style="padding-top: 20px;">
                    Bandung, <?= date("d-m-Y") ?>
                </div>
                <div id="row">
                    <div id="column" style="padding-top: 20px;">
                        Dari., 
                    </div>
                </div>
                <div id="row">
                    <p style="font-weight: bold;"><?= $head->partner_nama ?></p>
                    <p><?= "Alamat 1 : {$head->alamat}" ?></p>
                </div>
            </div>
        </div>
        <table cellspacing="0" style="font-size: 12px; width: 100%;border: 1px solid black;border-collapse: collapse;">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 30px">No</th>
                    <th rowspan="2" style="width: 250px">Jenis Barang / Uraian</th>
                    <th colspan="2">Quantity</th>
                    <th colspan="2">Harga Satuan</th>
                    <th colspan="2">Jumlah</th>
                </tr>
                <tr>
                    <th style="width: 120px">
                        Gul/PCS
                    </th>
                    <th style="width: 120px">
                        Satuan
                    </th>
                    <th style="width: 120px">
                        <?= $curr->nama ?>
                    </th>
                    <th style="width: 120px">
                        IDR
                    </th>
                    <th style="width: 120px">
                        <?= $curr->nama ?>
                    </th>
                    <th style="width: 120px">
                        IDR
                    </th>
                </tr>
                <tr>

                </tr>
            </thead>
            <tbody>
                <?php
                $subtotal = 0;
                $subtotalValas = 0;
                $totalQty = 0;
                $totalQtyLot = 0;
                $uomLot = "";
                $uom = "";
                foreach ($detail as $key => $value) {
                    $subtotal += ($value->jumlah * $head->kurs_nominal);
                    $subtotalValas += $value->jumlah;
                    $totalQty += $value->qty;
                    $totalQtyLot += $value->qty_lot;
                    $uomLot = $value->lot;
                    $uom = $value->uom;
                    $warna = ($value->warna === "") ? "" : " / {$value->warna}";
                    ?>
                    <tr>
                        <td style="text-align: center;">
                            <?= ($key + 1) ?>
                        </td>
                        <td>
                            <?= $value->uraian.$warna ?>
                        </td>
                        <td style="text-align: center"><?= "{$value->qty_lot} {$value->lot}" ?></td>
                        <td style="text-align: center"><?= "{$value->qty} {$value->uom}" ?></td>
                        <td style="text-align: right"><?= number_format($value->harga, 4) ?></td>
                        <td style="text-align: right"><?= number_format($value->harga * $head->kurs_nominal, 2) ?></td>
                        <td style="text-align: right"><?= number_format($value->jumlah, 4) ?></td>
                        <td style="text-align: right"><?= number_format($value->jumlah * $head->kurs_nominal, 2) ?></td>
                    </tr>
                    <?php
                }
                $totals = explode(".", round($head->final_total,2));
                $terbilang = Kwitansi($totals[0]);
                $terbilang2 = "";
                if (isset($totals[1])) {
                    if ($totals[1] > 0) {
                        $terbilang2 .= " Koma";
                        $terbilang2 .= KwitansiDesimal($totals[1]);
                    }
                }
                ?>
                <tr>
                    <td colspan="2"></td>
                    <td style="text-align: center"> <?= "{$totalQtyLot} {$uomLot}" ?></td>
                    <td style="text-align: center"><?= "{$totalQty} {$uom}" ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td rowspan="4" colspan="5">
                        <p><?= "(*)Kurs : Rp. {$head->kurs_nominal}" ?></p>
                        Terbilang : <?= $terbilang.$terbilang2 ?> <?= $curr->ket ?>
                    </td>
                    <td style="font-weight: bold;">
                        Subtotal
                    </td>
                    <td style="text-align: right">
                        <?= $curr->symbol ?>&nbsp;&nbsp;<?= number_format($subtotalValas, 2) ?>
                    </td>
                    <td style="text-align: right">
                        <?= number_format($head->grand_total * $head->kurs_nominal, 2) ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">
                        Discount
                    </td>
                    <td style="text-align: right">
                        <?= $curr->symbol ?>&nbsp;&nbsp;<?= number_format($head->diskon, 2, ".", ",") ?>
                    </td>
                    <td style="text-align: right">
                        <?= number_format($head->diskon * $head->kurs_nominal, 2, ".", ",") ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">
                        <?= "Ppn " ?>
                    </td>
                    <td style="text-align: right">
                        <?= $curr->symbol ?>&nbsp;&nbsp;<?= number_format($head->ppn - $head->diskon_ppn, 2, ".", ",") ?>
                    </td>
                    <td style="text-align: right">
                        <?= number_format(($head->ppn - $head->diskon_ppn) * $head->kurs_nominal, 2, ".", ","); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">
                        TOTAL
                    </td>
                    <td style="text-align: right">
                        <?= $curr->symbol ?>&nbsp;&nbsp;<?= number_format(($head->final_total), 2, ".", ",") ?>
                    </td>
                    <td style="text-align: right">
                        <?= number_format(($head->final_total * $head->kurs_nominal), 2, ".", ",") ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        </br>
        <div id="row">
            <div id="column" style="text-align: left">
                No. Rekening : <?= $head->foot_note ?>
            </div>
        </div>
        </br>
        <div id="row">
            <div id="column" style="text-align: center">
                Penerima : 
            </div>
            <div id="column" style="padding-top: 20px;text-align: center;font-weight: bold;">
                Pengaduan/Klaim melebihi 7 hari dari tanggal pengiriman barang,tidak akan kami layani.
            </div> 
            <div id="column">
                <div  style="text-align: center">
                    Hormat Kami : 
                </div>
            </div>
        </div>
        <div id="row">
            <div id="column" style="text-align: center;padding-top: 30px;">
                (_____________) 
            </div>
            <div id="column" ></div>
            <div id="column" style="text-align: center;padding-top: 30px;">
                (_____________) 
            </div>
        </div>
    </body>
</html>