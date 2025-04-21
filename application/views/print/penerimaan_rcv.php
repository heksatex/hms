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
                font-size: 12px;
                /* Should be removed. Only for demonstration */
            }
            #columnmd {
                float: left;
                width: 50%;
                padding-top: 10px;
                text-align: center;
                font-size: 12px;
                /* Should be removed. Only for demonstration */
            }
            .text-left{
                text-align: left
            }

            table thead tr th{
                border-bottom: 1px solid #000000;
                text-align: left;
            }
        </style>
    </head>

    <body>
        <div id="row">
            <div id="column">
            </div>
            <div id="column">
                <h4>BUKTI TERIMA BARANG (BTB)</h4>
            </div>
            <div id="column">
            </div>
        </div>
        <div id="row">
            <div id="columnmd">
                <div id="row">
                    <div id="columnmd" style="text-align: left">
                        Kode
                    </div>
                    <div id="columnmd" style="text-align: left">
                        :&nbsp;<?= $head->kode ?>
                    </div>
                </div>
                <div id="row">
                    <div id="columnmd" style="text-align: left">
                        Tgl.Terima
                    </div>
                    <div id="columnmd" style="text-align: left">
                        :&nbsp;<?= $head->tanggal_transaksi ?>
                    </div>
                </div>
                <div id="row">
                    <div id="columnmd" style="text-align: left">
                        Origin
                    </div>
                    <div id="columnmd" style="text-align: left">
                        :&nbsp;<?= $head->origin ?>
                    </div>
                </div>
                <div id="row">
                    <div id="columnmd" style="text-align: left">
                        Tgl.Dibuat
                    </div>
                    <div id="columnmd" style="text-align: left">
                        :&nbsp;<?= $head->tanggal ?>
                    </div>
                </div>

            </div>
            <div id="columnmd">
                <div id="row">
                    <div id="columnmd" style="text-align: left">
                        No.SJ
                    </div>
                    <div id="columnmd" style="text-align: left">
                        :&nbsp;<?= $head->no_sj ?>
                    </div>
                </div>
                <div id="row">
                    <div id="columnmd" style="text-align: left">
                        Tgl.SJ
                    </div>
                    <div id="columnmd" style="text-align: left">
                        :&nbsp;<?= $head->tanggal_sj ?>
                    </div>
                </div>
                <div id="row">
                    <div id="columnmd" style="text-align: left">
                        Supplier
                    </div>
                    <div id="columnmd" style="text-align: left">
                        :&nbsp;<?= $head->nama_partner ?>
                    </div>
                </div>
                <div id="row">
                    <div id="columnmd" style="text-align: left">
                    </div>
                    <div id="columnmd" style="text-align: left" >
                        <?= $head->alamat ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="row">
            <table style="width: 100%; padding-top: 10px;font-size: 12px;">
                <thead>
                    <tr>
                        <th>
                            No
                        </th>
                        <th>
                            Kode Produk
                        </th>
                        <th>
                            Nama Produk
                        </th>
                        <th>
                            Lot
                        </th>
                        <th>
                            Qty
                        </th>
                        <th>
                            Uom
                        </th>
                        <th>
                            REff Note
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $kode_pp = "";
                    foreach ($item as $key => $value) {
                        $kode_pp = $value->kode_pp;
                        ?>
                        <tr>
                            <td>
                                <?= ($key + 1) ?>
                            </td>
                            <td>
                                <?= substr($value->kode_produk,0,11) ?>
                            </td>
                            <td>
                                <?= substr($value->nama_produk,0,18) ?>
                            </td>
                            <td>
                                <?= substr($value->lot,0,20) ?>
                            </td>
                            <td>
                                <?= $value->qty ?>
                            </td>
                            <td>
                                <?= $value->uom ?>
                            </td>
                            <td>
                                <?= substr($value->reff_note,0,15) ?>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div id="row">
            <div id="columnmd" style="text-align: left">
                <div id="row">
                    <p>Kode PP :</p>
                    <p><?= $kode_pp ?></p>
                </div>
            </div>
          
            <div id="columnmd">
                <div style="text-align: right">
                    <p>Tgl.Cetak&nbsp;:<?= date("Y-m-d H:i:s") ?></p>
                </div>
                <div id="row">
                    <div id="column">
                        <p>Pembelian</p>
                    </div>
                    <div id="column">
                        <p>Gudang</p>
                    </div>
                    <div id="column">
                        <p>Receiving</p>
                    </div>
                </div>
                <br>
                <div id="row" style="font-size: 12px">
                    <div id="column">
                        <p>(____________)</p>
                    </div>
                    <div id="column">
                        <p>(_____________)</p>
                    </div>
                    <div id="column">
                        <p>(<?= $users["nama"] ?? "" ?>)</p>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>