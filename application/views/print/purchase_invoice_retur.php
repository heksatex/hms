<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Nota Retur <?= $inv->no_inv_retur ?></title>
        <style>
            * {
                /*box-sizing: border-box;*/
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                font-size: 11.5px;
            }

            body {
                padding: 20px;
                display: flex;
                justify-content: center;
            }

            .nota-container {
                background-color: #fff;
                width: 650px;
                min-height: 750px;
                /*padding: 15px;*/
                border: 2px solid #000;
                position: relative;
            }

            /* HEADER STYLE */
            .header {
                text-align: center;
                margin-bottom: 15px;
                border: 2px solid black;
            }

            .header h2 {
                font-size: 15px;
                font-weight: bold;
                text-decoration: underline;
            }

            .header p {
                font-size: 12px;
                line-height: 2px;
            }

            .header .sub-faktur {
                font-size: 11px;
            }

            /* IDENTITAS STYLE */
            .table-identitas {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15px;
            }

            .table-identitas td {
                padding: 2px 0;
                vertical-align: top;
                line-height: 1.3;
            }

            .section-title {
                font-weight: bold;
                text-decoration: underline;
            }

            /* TABEL BARANG STYLE */
            .table-barang {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #000;
            }

            .table-barang th {
                border: 1px solid #000;
                padding: 6px 4px;
                font-weight: bold;
                text-align: center;
                text-transform: uppercase;
            }

            .table-barang td {
                border-left: 1px solid #000;
                border-right: 1px solid #000;
                height: 42px; /* Membuat kolom kosong memanjang ke bawah */
                padding: 4px 6px;
            }

            /* Memastikan garis horizontal penutup tabel hanya di baris paling bawah */
            .table-barang tbody tr:last-child td {
                border-bottom: 1px solid #000;
            }

            .text-center {
                text-align: center;
            }
            .text-right {
                text-align: right;
            }
            .valign-top {
                vertical-align: top;
            }

            .row-barang {
                display: flex;
                justify-content: space-between;
            }

            .row-harga {
                display: flex;
                justify-content: space-between;
            }

            /* TOTAL & PAJAK STYLE */
            .table-total {
                width: 100%;
                border-collapse: collapse;
                border-left: 1px solid #000;
                border-right: 1px solid #000;
                border-bottom: 1px solid #000;
            }

            .table-total td {
                border-bottom: 1px solid #000;
                padding: 4px 6px;
            }

            .table-total tr:last-child td {
                border-bottom: none;
            }

            /* TANDA TANGAN STYLE */
            .ttd-section {
                float: right;
                width: 250px;
                text-align: center;
                margin-top: 25px;
                margin-right: 20px;
                position: relative;
            }

            .stempel-box {
                height: 65px;
                position: relative;
            }

            /* Simulasi bayangan stempel/tanda tangan ungu seperti di gambar */
            .stempel-text {
                position: absolute;
                top: 15px;
                left: 40px;
                border: 2px dashed rgba(0, 32, 196, 0.4);
                color: rgba(0, 32, 196, 0.5);
                padding: 4px 8px;
                font-weight: bold;
                font-size: 10px;
                transform: rotate(-5deg);
                border-radius: 4px;
            }

            .nama-penandatangan {
                margin-top: 5px;
            }

            /* FOOTER LEMBAR STYLE */

            footer {
                position: fixed;
                bottom: 0;
                left: 0;
                width: 100%;
                border: 2px solid black;
                line-height: 0.5;
                font-size: 10px;
                font-weight: 600;
            }
        </style>
    </head>
    <body>

        <div class="nota-container">
            <!-- HEADER -->
            <div class="header">
                <h2>NOTA RETUR</h2>
                <p>Nomor: <?= $inv->no_inv_retur ?></p>
                <p class="sub-faktur">(Atas Faktur Pajak Nomor: <?= $inv->no_fp ?> Tanggal: <?= ($inv->tanggal_fp == "0000-00-00") ? "-" : $inv->tanggal_fp ?>)</p>
            </div>

            <!-- IDENTITAS PEMBELI & PENJUAL -->
            <table class="table-identitas">
                <tr>
                    <td colspan="3" class="section-title">Pembeli BKP</td>
                </tr>
                <tr>
                    <td style="width: 15%;">Nama</td>
                    <td style="width: 2%;">:</td>
                    <td>PT. HEKSATEX INDAH</td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><?= $alamat->value ?></td>
                </tr>
                <tr>
                    <td>NPWP</td>
                    <td>:</td>
                    <td><?= $npwp->value ?></td>
                </tr>
                <tr>
                    <td colspan="3" class="section-title" style="padding-top: 10px;">Kepada Penjual</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td>:</td>
                    <td><?= $inv->supplier ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><?= $inv->delivery_street ?>,<br><?= $inv->delivery_city ?></td>
                </tr>
                <tr>
                    <td>NPWP</td>
                    <td>:</td>
                    <td><?= $inv->npwp ?></td>
                </tr>
            </table>

            <!-- TABEL BARANG -->
            <table class="table-barang">
                <thead>
                    <tr>
                        <th style="width: 6%;">No.</th>
                        <th style="width: 44%;">NAMA BARANG /<br>JASA KENA PAJAK</th>
                        <th style="width: 25%;">HARGA<br>SATUAN</th>
                        <th style="width: 25%;">JUMLAH</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Baris 1 (Isi Data) -->
                    <?php
                    $totals = 0;
                    foreach ($invDetail as $key => $value) {
                        $total = $value->harga_satuan * $value->qty_beli;
                        $totals = $total;
                        ?>
                        <tr>
                            <td class="text-center valign-top"><?= $key + 1 ?></td>
                            <td class="valign-top">
                                <div class="row-barang">
                                    <span><?= $value->nama_produk ?></span>
                                    <span><?= number_format($value->qty_beli, 2) ?> &nbsp;&nbsp;&nbsp;&nbsp; <?= $value->uom_beli ?></span>
                                </div>
                            </td>
                            <td class="valign-top">
                                <div class="row-harga">
                                    <span><?= $inv->mata_uang ?></span>
                                    <span><?= number_format($value->harga_satuan, 2) ?></span>
                                </div>
                            </td>
                            $
                            <td class="valign-top text-right"><?= number_format($total, 2) ?></td>
                        </tr>
                        <?php
                    }
                    ?>

                    <!-- Baris Kosong 2 -->
<!--                    <tr>
                        <td class="text-center">2</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>-->

                </tbody>
            </table>

            <!-- TOTAL & PAJAK -->
            <table class="table-total">
                <tr>
                    <td style="width: 70%; text-align: left;">Jumlah Harga BKP yang Dikembalikan</td>
                    <td style="width: 5%; border-left: 1px solid black;"></td>
                    <td style="width: 25%; text-align: right;"></td>
                </tr>
                <tr>
                    <td style="text-align: left; font-weight: bold;">Dasar Pengenaan Pajak</td>
                    <td style="border-left: 1px solid black; padding-left: 5px;"><?= $inv->mata_uang ?></td>
                    <td style="text-align: right; font-weight: bold;"><?= number_format($inv->total - $inv->total_tax, 2) ?></td>
                </tr>
                <tr>
                    <td style="text-align: left;">Pajak Pertambahan Nilai yang diminta kembali</td>
                    <td style="border-left: 1px solid black; padding-left: 5px;"><?= $inv->mata_uang ?></td>
                    <td style="text-align: right;"><?= number_format($inv->total_tax, 2) ?></td>
                </tr>
            </table>

            <!-- TANDA TANGAN -->
            <div class="ttd-section">
                <p>Cimahi, <?= date("d F Y", strtotime($inv->created_at)) ?></p>
                <p style="font-weight: bold; margin-top: 2px;">PT. HEKSATEX INDAH</p>

                <!-- Area Tanda Tangan & Stempel Fake -->
                <div class="stempel-box">
                    <!-- Teks simulasi cap stempel -->

                </div>

                <p class="nama-penandatangan">( <?= $users ?> )</p>
            </div>

            <footer>
                <p>Lembar ke-1: untuk PKP Penjual</p>
                <p>Lembar ke-2: untuk Pembeli</p>
            </footer>

            <!-- FOOTER LEMBAR -->


        </div>

    </body>
</html>