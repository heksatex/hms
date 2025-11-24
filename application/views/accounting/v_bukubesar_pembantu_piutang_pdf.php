<!doctype html>

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            /* margin: 20px; */
            /* rapat, bisa diubah ke 0 jika benar-benar ingin full page */
            margin: 25px 10px 20px 10px;
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

        table th, table td {
            font-size: 7px !important;
            padding: 2px 2px !important;
            word-wrap: break-word;
            white-space: normal;

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
        <strong>BUKU BESAR PEMBANTU PIUTANG</strong>
        <div class="kop2">
            <strong style="padding-top:20px;"><?php echo ($tgl_dari) . ' s.d ' . ($tgl_sampai); ?></strong><br>
        </div>
    </div>
    <table border>
        <thead>
            <tr>
                <th class="style" style="width: 15px">No. </th>
                <th class='style' style="width: 60px">Customer</th>
                <th class='style' style="width: 70px">Saldo Awal</th>
                <th class='style' style="width: 60px">Piutang DPP</th>
                <th class='style' style="width: 60px">Piutang PPN</th>
                <th class='style' style="width: 60px">Piutang Total</th>
                <th class='style' style="width: 60px">Pelunasan</th>
                <th class='style' style="width: 60px">Retur DPP</th>
                <th class='style' style="width: 60px">Retur PPN</th>
                <th class='style' style="width: 60px">Retur Total</th>
                <th class='style' style="width: 60px">Diskon DPP</th>
                <th class='style' style="width: 60px">Diskon PPN</th>
                <th class='style' style="width: 60px">Diskon Total</th>
                <th class='style' style="width: 60px">Uang Muka</th>
                <th class='style' style="width: 60px">Koreksi</th>
                <th class='style' style="width: 70px">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $piutang_dpp      = 0;
            $piutang_ppn      = 0;
            $piutang_total    = 0;
            $pelunasan  = 0;
            $retur_dpp      = 0;
            $retur_ppn      = 0;
            $retur_total    = 0;
            $diskon_dpp      = 0;
            $diskon_ppn      = 0;
            $diskon_total    = 0;
            $uang_muka  = 0;
            $koreksi    = 0;
            $s_awal     = 0;
            $s_akhir   = 0;
            foreach ($list as $items) {
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td><?php echo htmlspecialchars(limit_text($items['nama_partner'], 10));  ?></td>
                    <td class='text-right'><?php echo number_format($items['saldo_awal'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['dpp_piutang'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['ppn_piutang'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['total_piutang_dpp_ppn'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['pelunasan'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['dpp_retur'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['ppn_retur'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['total_retur_dpp_ppn'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['dpp_diskon'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['ppn_diskon'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['total_diskon_dpp_ppn'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['uang_muka'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['koreksi'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['saldo_akhir'], 2); ?></td>
                </tr>
            <?php
                $s_awal  = $s_awal + $items['saldo_awal'];
                $piutang_dpp = $piutang_dpp + $items['dpp_piutang'];
                $piutang_ppn = $piutang_ppn + $items['ppn_piutang'];
                $piutang_total = $piutang_total + $items['total_piutang_dpp_ppn'];
                $pelunasan   = $pelunasan + $items['pelunasan'];
                $retur_dpp = $retur_dpp + $items['dpp_retur'];
                $retur_ppn = $retur_ppn + $items['ppn_retur'];
                $retur_total = $retur_total + $items['total_retur_dpp_ppn'];
                $diskon_dpp = $diskon_dpp + $items['dpp_diskon'];
                $diskon_ppn = $diskon_ppn + $items['ppn_diskon'];
                $diskon_total = $diskon_total + $items['total_diskon_dpp_ppn'];
                $uang_muka   = $uang_muka + $items['uang_muka'];
                $koreksi   = $koreksi + $items['koreksi'];
                $s_akhir   = $s_akhir + $items['saldo_akhir'];
            }
            ?>
            <tr>
                <td class="bold text-right" colspan="2">Total : </td>
                <td class='bold text-right'><?php echo number_format($s_awal, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($piutang_dpp, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($piutang_ppn, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($piutang_total, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($pelunasan, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($retur_dpp, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($retur_ppn, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($retur_total, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($diskon_dpp, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($diskon_ppn, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($diskon_total, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($uang_muka, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($koreksi, 2); ?></td>
                <td class='bold text-right'><?php echo number_format($s_akhir, 2); ?></td>
            </tr>
        </tbody>
    </table>

    </html>