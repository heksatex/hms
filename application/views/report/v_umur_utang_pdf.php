<!doctype html>

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            /* margin: 20px; */
            /* rapat, bisa diubah ke 0 jika benar-benar ingin full page */
            margin: 30px 20px 30px 20px;
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
            font-size: 11px;
            margin-top: 10px;
            text-align: center;
        }
        table,
        th,
        td {
            border: 1px solid #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        thead {
            background-color: #CCC;
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

        .no {
            width: 5px;
            text-align: center;
        }
        .supplier {
            width: 120px;
            word-wrap: break-word;
        }
        .angka {
            width: 80px;
            text-align: right;
        }
    </style>
    <?php
        function limit_text($text, $max = 20) {
            return (strlen($text) > $max) ? substr($text, 0, $max) . '...' : $text;
    }
    ?>

</head>

<body>
    <div class="header">
        <b>PT. HEKSATEX INDAH</b><br>
        <strong>UMUR UTANG (AGING)</strong>
        <div class="kop2">
            <strong style="padding-top:20px;"><?php echo "Per Tgl. ".$periode; ?></strong><br>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <?php foreach ($header as $idx => $heads): ?>
                    <?php
                    // Tentukan lebar tiap kolom
                    if ($idx === 0) {
                        $class = 'no';
                    } elseif ($idx === 1) {
                        $class = 'supplier';
                    } else {
                        $class = 'angka';
                    }

                    // Tambahkan class text-right untuk kolom angka (misalnya mulai dari index ke-2)
                    // $class = ($idx >= 2) ? 'text-right' : '';
                    ?>
                    <th class="<?= $class ?>">
                        <?= htmlspecialchars($heads) ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_hutang           = 0;
            $total_hutang_bulan_ini = 0;
            $total_hutang_bulan_1   = 0;
            $total_hutang_bulan_2   = 0;
            $total_hutang_bulan_3   = 0;
            $total_hutang_lebih_dari_3   = 0;
            foreach ($items as $items) {
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td ><?php echo htmlspecialchars(limit_text($items['nama_partner'], 20));  ?></td>
                    <td class='text-right'><?php echo number_format($items['total_hutang'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['hutang_bulan_ini'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['hutang_bulan_1'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['hutang_bulan_2'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['hutang_bulan_3'], 2); ?></td>
                    <td class='text-right'><?php echo number_format($items['hutang_lebih_dari_3_bulan'], 2); ?></td>
                </tr>
            <?php
                $total_hutang           = $total_hutang + $items['total_hutang'];
                $total_hutang_bulan_ini = $total_hutang_bulan_ini + $items['hutang_bulan_ini'];
                $total_hutang_bulan_1   = $total_hutang_bulan_1 + $items['hutang_bulan_1'];
                $total_hutang_bulan_2   = $total_hutang_bulan_2 + $items['hutang_bulan_2'];
                $total_hutang_bulan_3   = $total_hutang_bulan_3 + $items['hutang_bulan_3'];
                $total_hutang_lebih_dari_3   = $total_hutang_lebih_dari_3 + $items['hutang_lebih_dari_3_bulan'];
            }
            ?>
            <tr>
                <td class="bold text-right" colspan="2">Total : </td>
                <td class='text-right'><?php echo number_format($total_hutang, 2); ?></td>
                <td class='text-right'><?php echo number_format($total_hutang_bulan_ini, 2); ?></td>
                <td class='text-right'><?php echo number_format($total_hutang_bulan_1, 2); ?></td>
                <td class='text-right'><?php echo number_format($total_hutang_bulan_2, 2); ?></td>
                <td class='text-right'><?php echo number_format($total_hutang_bulan_3, 2); ?></td>
                <td class='text-right'><?php echo number_format($total_hutang_lebih_dari_3, 2); ?></td>
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