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
        </style>
    </head>
    <body style="padding:  0 30px 0 30px;font-size: 10px;">
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
                    <div id="column-news-1">
                        <h2>FAKTUR PENJUALAN</h2>
                    </div>
                </div>
                <div id="row" >
                    <div id="column-news-1" style="width: 60%;" >
                        <strong>No Faktur</strong>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= ": {$head->no_faktur_internal}" ?>
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
                    Bandung, <?= $head->tanggal ?>
                </div>
                <div id="row">
                    <div id="column" style="padding-top: 20px;">
                        Kepada Yth., 
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
                    <th rowspan="2" style="width: 200px">Jenis Barang / Uraian</th>
                    <th colspan="2">Quantity</th>
                    <th rowspan="2" style="width: 120px;">Harga Satuan</th>
                    <th rowspan="2" style="width: 150px;">Jumlah</th>
                </tr>
                <tr>
                    <th style="width: 100px">
                        Gul/PCS
                    </th>
                    <th style="width: 100px">
                        Satuan
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                $dpp = number_format(($head->grand_total - $head->diskon) * 11 / 12, 2, ".", ",");
                $subtotal = 0;
                $totalQty = 0;
                $totalQtyLot = 0;
                $uomLot = "";
                $uom = "";

                foreach ($detail as $key => $value) {
                    $subtotal += $value->jumlah;
                    $warna = ($value->warna === "") ? "" : " / {$value->warna}";
                    $totalQty += $value->qty;
                    $totalQtyLot += $value->qty_lot;
                    $uomLot = $value->lot;
                    $uom = $value->uom;
                    ?>
                    <tr>
                        <td style="text-align: center;">
                            <?= ($key + 1) ?>
                        </td>
                        <td>
                            <?= $value->uraian . $warna ?>
                        </td>
                        <td style="text-align: right"><?= "{$value->qty_lot} {$value->lot}" ?></td>
                        <td style="text-align: right"><?= "{$value->qty} {$value->uom}" ?></td>
                        <td style="text-align: right"><?= "Rp. " . number_format($value->harga, 2) ?></td>
                        <td style="text-align: right"><?= "Rp. " . number_format($value->jumlah, 2) ?></td>
                    </tr>
                    <?php
                    if ($value->no_po !== "") {
                        ?>
                        <tr>
                            <td>
                            </td>
                            <td>
                                <?= $value->no_po ?>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>
                            </td>
                            <td>  
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
                <tr>
                    <td colspan="2" style="text-align: right"> <strong>Total : </strong></td>
                    <td style="text-align: right"> <?= number_format($totalQtyLot, 2) . " {$uomLot}" ?></td>
                    <td style="text-align: right"><?= number_format($totalQty, 2) . " {$uom}" ?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td rowspan="5" colspan="4">
                        Terbilang : <?= Kwitansi(round($head->final_total)) ?> Rupiah
                    </td>
                    <td style="font-weight: bold;">
                        Subtotal
                    </td>
                    <td style="text-align: right">
                        <?= "Rp. " . number_format(round($head->grand_total), 2, ".", ",") ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">
                        Dpp Nilai Lain
                    </td>
                    <?php
                    $subtotal2 = (round($head->grand_total) - round($head->diskon));
                    ?>
                    <td style="text-align: right">
                        <?= "Rp. " . number_format($head->dpp_lain, 2, ".", ",") ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">
                        Discount
                    </td>
                    <td style="text-align: right">
                        <?= "Rp. " . number_format($head->diskon, 2, ".", ",") ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">
                        Ppn
                    </td>
                    <td style="text-align: right">
                        <?= "Rp. " . number_format(round($head->ppn), 2, ".", ","); ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">
                        TOTAL
                    </td>
                    <td style="text-align: right">
                        <?= "Rp. " . number_format(($head->final_total), 2, ".", ",") ?>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div id="row">
            <div id="column">

            </div>
        </div>
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