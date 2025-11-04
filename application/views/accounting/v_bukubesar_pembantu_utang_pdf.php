<!doctype html>

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            /* margin: 20px; */
            /* rapat, bisa diubah ke 0 jika benar-benar ingin full page */
            margin: 30px 15px 30px 15px;
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
        .bold {
            font-weight: bold;
        }
    </style>

</head>
<?php
function limit_text($text, $max = 15)
{
    return (strlen($text) > $max) ? substr($text, 0, $max) . '...' : $text;
}
?>

<body>
    <div class="header">
        <b>PT. HEKSATEX INDAH</b><br>
        <strong>BUKU BESAR PEMBANTU UTANG</strong>
        <div class="kop2">
            <strong style="padding-top:20px;"><?php echo ($tgl_dari) . ' s.d ' . ($tgl_sampai); ?></strong><br>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th class="style" style="width: 5px">No. </th>
                <th class='style' style="width: 110px">Supplier</th>
                <th class='style' style="width: 80px">Saldo Awal</th>
                <th class='style' style="width: 80px">Utang</th>
                <th class='style' style="width: 80px">Pelunasan</th>
                <th class='style' style="width: 80px">Retur</th>
                <th class='style' style="width: 80px">Uang Muka</th>
                <th class='style' style="width: 80px">Koreksi</th>
                <th class='style' style="width: 80px">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_hutang           = 0;
            $utang      = 0;
            $pelunasan  = 0;
            $retur      = 0;
            $uang_muka  = 0;
            $koreksi    = 0;
            $s_awal     = 0;
            $s_akhir   = 0;
            foreach ($list as $items) {
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars(limit_text($items['nama_partner'], 15));  ?></td>
                    <td class='text-right'><?php echo number_format($items['saldo_awal'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['utang'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['pelunasan'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['retur'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['uang_muka'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['koreksi'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['saldo_akhir'], 2); ?></td>
                </tr>
            <?php
                $s_awal  = $s_awal + $items['saldo_awal'];
                $utang = $utang + $items['utang'];
                $pelunasan   = $pelunasan + $items['pelunasan'];
                $retur   = $retur + $items['retur'];
                $uang_muka   = $uang_muka + $items['uang_muka'];
                $koreksi   = $koreksi + $items['koreksi'];
                $s_akhir   = $s_akhir + $items['saldo_akhir'];
            }
            ?>
            <tr>
                <td class="bold text-right" colspan="2">Total : </td>
                <td class='bold text-right'><?php echo number_format($s_awal, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($utang, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($pelunasan, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($retur, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($uang_muka, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($koreksi, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($s_akhir, 2); ?></td>
            </tr>
        </tbody>
    </table>

    </html>