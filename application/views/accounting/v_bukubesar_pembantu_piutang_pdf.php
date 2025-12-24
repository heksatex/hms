<!doctype html>

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            /* margin: 20px; */
            /* rapat, bisa diubah ke 0 jika benar-benar ingin full page */
            margin: 80px 30px 30px 30px;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
            /* margin-top: 37px; */
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

        /* 
        th:nth-child(1) {
            width: 15px;
        }

        th:nth-child(2) {
            width: 80px;
        }

        th:nth-child(3),
        th:nth-child(17) {
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
        th:nth-child(15),
        th:nth-child(16) {
            width: 60px;
        } */


        .text-right {
            text-align: right;
        }

        .header {
            position: fixed;
            top: -60px;
            left: 0;
            right: 0;
            height: 60px;
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

<?php
$headers = [
    ['label' => 'No',          'rowspan' => 2],
    ['label' => 'Customer',    'rowspan' => 2],
    ['label' => 'Saldo Awal',  'rowspan' => 2],

    ['label' => 'Piutang',   'colspan' => 3],
    ['label' => 'Pelunasan', 'rowspan' => 2],

    ['label' => 'Retur',     'colspan' => 3],
    ['label' => 'Diskon',    'colspan' => 3],

    ['label' => 'Uang Muka', 'colspan' => 2],
    ['label' => 'Koreksi',   'rowspan' => 2],
    ['label' => 'Refund',   'rowspan' => 2],

    ['label' => 'Saldo Akhir', 'rowspan' => 2],
    ['label' => 'Deposit',   'colspan' => 2],
];

$subHeaders = [
    'Piutang'   => ['DPP', 'PPN', 'Total'],
    'Retur'     => ['DPP', 'PPN', 'Total'],
    'Diskon'    => ['DPP', 'PPN', 'Total'],
    'Uang Muka' => ['Baru', 'Pelunasan'],
    'Deposit'   => ['Baru', 'Pelunasan'],
];
?>

<?php
$bodyMap = [
    'no',
    'nama_partner',
    'saldo_awal',

    ['dpp_piutang', 'ppn_piutang', 'total_piutang_dpp_ppn'],
    'pelunasan',

    ['dpp_retur', 'ppn_retur', 'total_retur_dpp_ppn'],
    ['dpp_diskon', 'ppn_diskon', 'total_diskon_dpp_ppn'],

    ['um_baru', 'um_pelunasan'],
    'koreksi',
    'refund',

    'saldo_akhir',
    ['depo_baru', 'depo_pelunasan'],
];

// hitung total kolom (buat colspan golongan)
$totalCols = 0;
foreach ($bodyMap as $m) {
    $totalCols += is_array($m) ? count($m) : 1;
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
            <colgroup>
                <col style="width:3%">
                <col style="width:14%">
                <col style="width:7%">

                <col style="width:7%">
                <col style="width:6%">
                <col style="width:6%">
                <col style="width:6%">

                <col style="width:6%">

                <col style="width:6%">
                <col style="width:6%">
                <col style="width:6%">

                <col style="width:6%">
                <col style="width:6%">
                <col style="width:6%">
                <col style="width:6%">
                <col style="width:6%">
                <col style="width:6%">

                <col style="width:6%">
                <col style="width:6%">
                <col style="width:7%">
            </colgroup>
            <tr>
                <?php foreach ($headers as $h): ?>
                    <th
                        <?php if (!empty($h['rowspan'])): ?>rowspan="<?= $h['rowspan'] ?>" <?php endif; ?>
                        <?php if (!empty($h['colspan'])): ?>colspan="<?= $h['colspan'] ?>" <?php endif; ?>
                        style="text-align:center">
                        <?= $h['label'] ?>
                    </th>
                <?php endforeach; ?>
            </tr>

            <tr>
                <?php
                foreach ($headers as $h) {
                    if (!empty($h['colspan'])) {
                        foreach ($subHeaders[$h['label']] as $sub) {
                            echo "<th style='text-align:center'>{$sub}</th>";
                        }
                    }
                }
                ?>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($list as $head): ?>

                <tr>
                    <td colspan="<?= $totalCols ?>" class="bold">
                        <?= htmlspecialchars($head['gol_nama']) ?>
                    </td>
                </tr>

                <?php
                $no = 1;
                $totals = [];
                ?>

                <?php foreach ($head['tmp_data'] as $row): ?>
                    <tr>
                        <?php
                        foreach ($bodyMap as $map) {

                            // === KOLOM TUNGGAL ===
                            if (is_string($map)) {

                                if ($map === 'no') {
                                    echo "<td>{$no}</td>";
                                    continue;
                                }

                                if ($map === 'nama_partner') {
                                    echo "<td>" . htmlspecialchars($row[$map]) . "</td>";
                                    continue;
                                }

                                $value = $row[$map] ?? 0;
                                echo "<td class='text-right'>" . number_format($value, 2) . "</td>";

                                $totals[$map] = ($totals[$map] ?? 0) + (float)$value;
                                continue;
                            }

                            // === KOLOM GROUP (ARRAY) ===
                            if (is_array($map)) {
                                foreach ($map as $field) {
                                    $value = $row[$field] ?? 0;

                                    echo "<td class='text-right'>" . number_format($value, 2) . "</td>";

                                    $totals[$field] = ($totals[$field] ?? 0) + (float)$value;
                                }
                            }
                        }
                        ?>
                    </tr>
                <?php $no++;
                endforeach; ?>


                <!-- TOTAL PER GOLONGAN -->
                <tr class="bold">
                    <?php
                    foreach ($bodyMap as $map) {

                        if ($map === 'no') {
                            echo "<td colspan='2' class='text-right'>Total {$head['gol_nama']} :</td>";
                            continue;
                        }

                        if ($map === 'nama_partner') {
                            continue;
                        }

                        if (is_string($map)) {
                            echo "<td class='text-right'>" . number_format($totals[$map] ?? 0, 2) . "</td>";
                            continue;
                        }

                        if (is_array($map)) {
                            foreach ($map as $field) {
                                echo "<td class='text-right'>" . number_format($totals[$field] ?? 0, 2) . "</td>";
                            }
                        }
                    }
                    ?>
                </tr>


            <?php endforeach; ?>
        </tbody>

    </table>

    </html>