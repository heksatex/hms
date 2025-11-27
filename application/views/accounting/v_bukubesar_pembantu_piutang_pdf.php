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
            /* background-color: rgba(156, 153, 153, 1) */
            background-color: #CCC;
        }

        th {
            border-top: 1px solid #000000ff;
            text-align: left;
        }

        table th,
        table td {
            font-size: 10px !important;
            padding: 2px 2px !important;
            word-wrap: break-word;
            white-space: normal;
        }

        table,
        th,
        td {
            border: 1px solid #000;
        }

        th:nth-child(1) {
            width: 15px;
        }

        th:nth-child(2) {
            width: 80px;
        }

        th:nth-child(3),
        th:nth-child(16) {
            width: 70px;
        }

        th:nth-child(4),
        th:nth-child(5),
        th:nth-child(6),
        th:nth-child(7),
        th:nth-child(8),
        th:nth-child(9),
        th:nth-child(10),
        th:nth-child(11),
        th:nth-child(12),
        th:nth-child(13),
        th:nth-child(14),
        th:nth-child(15) {
            width: 60px;
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
    <table>
        <thead>
            <tr>
                <th>No. </th>
                <th>Customer</th>
                <th>Saldo Awal</th>
                <th>Piutang DPP</th>
                <th>Piutang PPN</th>
                <th>Piutang Total</th>
                <th>Pelunasan</th>
                <th>Retur DPP</th>
                <th>Retur PPN</th>
                <th>Retur Total</th>
                <th>Diskon DPP</th>
                <th>Diskon PPN</th>
                <th>Diskon Total</th>
                <th>Uang Muka</th>
                <th>Koreksi</th>
                <th>Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($list as $head) {
            ?>
                <tr>
                    <td colspan="16" class="bold"><?php echo $head['gol_nama'] ?></td>
                </tr>
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
                foreach ($head['tmp_data'] as $items) {
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars(limit_text($items['nama_partner'], 18));  ?></td>
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
                    <td class="bold text-right" colspan="2">Total <?php echo $head['gol_nama'] . ' :'; ?></td>
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
            <?php

            }
            ?>
        </tbody>
    </table>

    </html>