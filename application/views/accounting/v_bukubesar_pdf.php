<!doctype html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            /* margin: 20px; */
            /* rapat, bisa diubah ke 0 jika benar-benar ingin full page */
            margin: 30px 30px 30px 30px;
            header: page-header;
            footer: page-footer;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
            margin-top: 37px;
        }

        .kop {
            text-align: left;
            line-height: 1.5;
        }

        .kop b {
            font-size: 14px;
        }

        .kop2 {
            text-align: left;
            line-height: 1.5;
            font-size: 12px;
            margin-top: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        thead {
            background-color: rgba(156, 153, 153, 1)
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
            position: fixed;
            top: -20px;
            left: 0;
            right: 0;
            height: 60px;
            font-size: 12px;
            text-align: center;
        }
    </style>

</head>

<body>
    <div class="header">
        <b>PT. HEKSATEX INDAH</b><br>
        <strong>BUKU BESAR (TRIAL BALANCE)</strong>
        <div class="kop2">
            <strong style="padding-top:20px;"><?php echo ($tgl_dari) . ' s.d ' . ($tgl_sampai); ?></strong><br>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th class="style">No. </th>
                <th class='style' style="min-width: 5px">Kode Acc</th>
                <th class='style' style="min-width: 200px">Nama Acc</th>
                <th class='style' style="min-width: 10px">Normal</th>
                <th class='style' style="min-width: 150px">Saldo Awal</th>
                <th class='style' style="min-width: 150px">Debit</th>
                <th class='style' style="min-width: 150px">Credit</th>
                <th class='style' style="min-width: 150px">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $debit = 0;
            $credit = 0;
            foreach ($list as $rows) {

            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo $rows['kode_acc'] ?></td>
                    <td style="min-width: 200px !important"><?php echo $rows['nama_acc'] ?></td>
                    <td><?php echo $rows['saldo_normal'] ?></td>
                    <td class="text-right"><?php echo number_format($rows['saldo_awal'], 2) ?></td>
                    <td class="text-right"><?php echo number_format($rows['debit'], 2) ?></td>
                    <td class="text-right"><?php echo number_format($rows['credit'], 2) ?></td>
                    <td class="text-right"><?php echo number_format($rows['saldo_akhir'], 2) ?></td>

                </tr>
            <?php
                $credit = $credit + $rows['credit'];
                $debit = $debit + $rows['debit'];
            }
            ?>
            <tr>
                <td colspan="5"></td>
                <td class="text-right"><?php echo number_format($credit, 2); ?></td>
                <td class="text-right"> <?php echo number_format($debit, 2); ?></td>
            </tr>
        </tbody>
    </table>
<!-- 
    <htmlpagefooter name="page-footer">
        <div style="text-align: center; font-size: 10px;">
            Halaman {PAGE_NUM} dari {PAGE_COUNT}
        </div>
    </htmlpagefooter> -->
</html>