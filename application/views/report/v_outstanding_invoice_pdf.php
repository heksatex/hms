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
            font-size: 11px;
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

<body>
    <div class="header">
        <b>PT. HEKSATEX INDAH</b><br>
        <strong>OUTSTANDING INVOICE</strong>
        <div class="kop2">
            <strong style="padding-top:20px;"><?php echo "Per Tgl. ".$periode; ?></strong><br>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th class="style">No. </th>
                <th class='style' style="min-width: 5px">Supplier</th>
                <th class='style' style="min-width: 100px">Invoice</th>
                <th class='style' style="min-width: 100px">PO</th>
                <th class='style' style="min-width: 100px">Receiving</th>
                <th class='style' style="min-width: 80px">Tanggal</th>

                <th class='style text-right' style="min-width: 150px">Total Hutang</th>
                <th class='style text-right' style="min-width: 150px">Sisa Hutang</th>
                <th class='style text-right' style="min-width: 80px">Umur (Hari)</th>
            </tr>
        </thead>
        <tbody>
            <?php
          
            $total_hutang = 0;
            $total_sisa_hutang = 0;
            foreach ($list as $rows) {

            ?>
                <tr>
                    <td colspan='9' class='bold'><?php echo $rows['nama_partner'] ?></td>
                </tr>
            <?php
                $no = 1;
                $total_hutang = 0;
                $total_sisa_hutang = 0;
                foreach($rows['tmp_data_items'] as $items) {
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <td ></td>
                    <td ><?php echo $items['no_invoice'] ?></td>
                    <td ><?php echo $items['no_po'] ?></td>
                    <td ><?php echo $items['origin'] ?></td>
                    <td ><?php echo $items['tanggal'] ?></td>
                    <td class='text-right'><?php echo number_format($items['hutang_rp'],2) ?></td>
                    <td class='text-right'><?php echo number_format($items['sisa_hutang_rp'],2) ?></td>
                    <td class='text-right'><?php echo $items['hari'] ?></td>
                </tr>

            <?php
                    $total_hutang = $total_hutang + $items['hutang_rp'];
                    $total_sisa_hutang = $total_sisa_hutang + $items['sisa_hutang_rp'];
               }
            ?>
                <tr>
                    <td class="bold text-right"colspan="6">Total : </td>
                    <td class="text-right"><?php echo number_format($total_hutang, 2); ?></td>
                    <td class="text-right"> <?php echo number_format($total_sisa_hutang, 2); ?></td>
                </tr>
            <?php
            }
            ?>
           
        </tbody>
    </table>
<!-- 
    <htmlpagefooter name="page-footer">
        <div style="text-align: center; font-size: 10px;">
            Halaman {PAGE_NUM} dari {PAGE_COUNT}
        </div>
    </htmlpagefooter> -->
</html>