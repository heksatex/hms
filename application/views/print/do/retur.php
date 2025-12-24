<!doctype html>
<html lang="en">
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
        </style>
    </head>
    <body style="padding:  0 30px 0 30px;font-size: 10px;">
        <div id="row">
            <div id="column">
            </div>
            <div id="column" style="text-align: center;">
                <h2>RETUR DELIVERY ORDER</h2>
            </div>
            <div id="column">
            </div>
        </div>
        <div id="row">
            <div id="column-news-1">
                <h2><?= $data[0]->no_sj ?? "" ?></h2>
            </div>
        </div>
        <div id="row" >
            <div id="column-news-1">
                <div id="row" >
                    <strong>No DO</strong>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= ": {$data[0]->no}" ?>
                </div>
                <div id="row">
                    <strong>Picklist</strong>
                    &nbsp;&nbsp;&nbsp;&nbsp;<?= " : {$data[0]->no_pl}" ?>
                </div>
            </div>
            <div id="column-news-2">

            </div>
        </div>
        <div id="row" style="padding-top: 20px;">
            <table cellspacing="0" style="font-size: 12px; width: 100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th class="no">No</th>
                        <th class="style">Barcode</th>
                        <th class="style">Kode Produk</th>
                        <th class="style">Nama Produk</th>
                        <th class="style">Corak Remark</th>
                        <th class="style">Warna Remark</th>
                        <th class="style">Qty</th>
                        <th class="style">Qty 2</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 0;
                    foreach ($data as $key => $value) {
                        $no++;
                        ?>
                        <tr>
                            <td>
                                <?= $no ?>
                            </td>
                            <td>
                                <?= $value->barcode_id ?>
                            </td>
                            <td>
                                <?= $value->kode_produk ?>
                            </td>
                            <td>
                                <?= $value->nama_produk ?>
                            </td>
                            <td>
                                <?= $value->corak_remark ?>
                            </td>
                            <td>
                                <?= $value->warna_remark ?>
                            </td>
                            <td>
                                <?= ($value->qty_jual ?? 0) . ' ' . $value->uom_jual ?>
                            </td>
                            <td>
                                <?= ($value->qty2_jual ?? 0) . ' ' . $value->uom2_jual ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="row" style="padding-top: 20px;">
            <div id="column">
            </div>
            <div id="column" style="text-align: right;">
                <span><strong>Yang Menyetujui,</strong></span>
            </div>
            <div id="column" style="text-align: center;">
                <span><strong>Yang Membuat,</strong></span>
            </div>
        </div>
        <div id="row" style="padding-top: 20px;">

            <div id="column">
            </div>
            <div id="column" style="text-align: right;">
                <span>.....................&nbsp;&nbsp;&nbsp;</span>
            </div>
            <div id="column" style="text-align: center;">
                <span><?= $user["nama"] ?></span>
            </div>
        </div>
    </body>
</html>