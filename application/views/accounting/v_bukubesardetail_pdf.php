<!doctype html>

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            /* margin: 20px; */
            /* rapat, bisa diubah ke 0 jika benar-benar ingin full page */
            margin: 30px 10px 20px 10px;
            /* header: page-header; */
            /* footer: page-footer; */
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
            margin-top: 37px;
        }

        .kop {
            text-align: center;
            line-height: 1.5;
        }

        .kop b {
            font-size: 14px;
        }

        .kop2 {
            text-align: center;
            line-height: 1.5;
            font-size: 12px;
            margin-top: 10px;
        }

        table {
            width: 100%;
            /* width: 760px; */
            border-collapse: collapse;
            margin-top: 5px;
            table-layout: fixed;
        }

        thead {
            background-color: rgba(187, 187, 187, 1)
        }

        tfoot {
            background-color: rgba(187, 187, 187, 1);
            font-weight: bold;
        }

        .tfoot-last-row td {
            border-bottom: 1px solid black;
        }

        th {
            border-top: 1px solid #000000ff;
            text-align: left;
        }

        th,
        td {
            font-size: 10px;
        }


        .text-right {
            text-align: right;
        }

        .header {
            text-align: center;
            position: fixed;
            top: -20px;
            left: 0;
            right: 0;
            height: 60px;
            font-size: 12px;
        }

        .ket-acc {
            width: 150px;
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: inline-block;
        }

         /* Lebar kolom fix */
        /* .col-no { width: 20px; }
        .col-tanggal { max-width: 70px; }
        .col-entries { width:80px; max-width: 90px; }
        .col-origin { width:10px; max-width: 140px; }
        .col-ket { width: 200px; }
        .col-debit,
        .col-credit,
        .col-saldo { max-width: 90px; } */

         .col-no { width: 3%; }
        .col-tanggal { width: 8%; }
        .col-entries { width:11%}
        .col-origin {width:18%; }
        .col-ket { width: 30%; }
        .col-debit,
        .col-credit,
        .col-saldo {  width: 11%; }
    </style>

</head>

<body>
    <div class="header">
        <b>PT. HEKSATEX INDAH</b><br>
        <strong>BUKU BESAR DETAIL</strong>
        <div class="kop2">
            <strong style="padding-top:20px;">Periode : <?php echo ($tgl_dari) . ' s.d ' . ($tgl_sampai); ?></strong><br>
        </div>
    </div>

    <?php
    $total_credit = 0;
    $total_debit  = 0;
    $saldo_akhir  = 0;
    foreach ($list as $datas) {
        $num = 1;
    ?>

        <table border="0">
            <thead>
                <tr>
                    <th colspan='8'><?php echo $datas['kode_acc'] . ' - ' . $datas['nama_acc'] ?></th>
                </tr>
                <tr>
                    <th class="style col-no">No. </th>
                    <th class='style col-tanggal'>Tanggal</th>
                    <th class='style col-entries'>Kode Entries</th>
                    <th class='style col-origin'>Origin</th>
                    <th class='style col-ket'>Keterangan</th>
                    <th class='style col-debit'>Debit</th>
                    <th class='style col-credit'>Credit</th>
                    <th class='style col-saldo'>Saldo</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $num; ?></td>
                    <td colspan='4'>Saldo Awal</td>
                    <td class="text-right">0.00</td>
                    <td class="text-right">0.00</td>
                    <td class="text-right"><?php echo number_format($datas['saldo_awal'], 2); ?></td>
                </tr>
                <?php
                foreach ($datas['tmp_data_isi'] as $datas2) {
                    $num++;
                ?>
                    <tr>
                        <td><?php echo $num; ?></td>
                        <td style=""><?php echo $datas2['tanggal']; ?></td>
                        <td style=""><?php echo $datas2['kode_entries']; ?></td>
                        <td> 
                            <?php
                            $origin = $datas2['origin'];
                            echo strlen($origin) > 24 ? substr($origin, 0, 24) . '...' : $origin;
                            ?></td>
                        <td class="ket">
                            <?php
                            $keterangan = $datas2['keterangan'];
                            echo strlen($keterangan) > 35 ? substr($keterangan, 0, 35) . '...' : $keterangan;
                            ?>
                        </td>
                        <td class="text-right"><?php echo number_format($datas2['debit'], 2); ?></td>
                        <td class="text-right"><?php echo number_format($datas2['credit'], 2); ?></td>
                        <td class="text-right"><?php echo number_format($datas2['saldo_akhir'], 2); ?></td>
                    </tr>
                <?php
                    $total_credit = $total_credit + $datas2['credit'];
                    $total_debit = $total_debit + $datas2['debit'];
                    $saldo_akhir = $datas2['saldo_akhir'];
                }
                ?>


            </tbody>
            <tfoot>
                <tr>
                    <td colspan='2'>Saldo Awal : </td>
                    <td class="text-right"><?php echo number_format($datas['saldo_awal'], 2); ?></td>
                    <td></td>
                    <td>Total :</td>
                    <td class="text-right"><?php echo number_format($total_debit, 2); ?></td>
                    <td class="text-right"><?php echo number_format($total_credit, 2); ?></td>
                    <td></td>
                </tr>
                <tr class='tfoot-last-row'>
                    <td colspan='2'>Saldo Akhir :</td>
                    <td class="text-right"><?php echo number_format($saldo_akhir, 2); ?></td>
                    <td></td>
                    <td>Mutasi :</td>
                    <td class="text-right"><?php echo number_format($saldo_akhir - $datas['saldo_awal'], 2); ?></td>
                    <td colspan='2'></td>
                </tr>
            </tfoot>


        </table>

        <p></p>

    <?php

        $total_credit = 0;
        $total_debit  = 0;
        $saldo_akhir  = 0;
    }
    ?>

</body>

</html>