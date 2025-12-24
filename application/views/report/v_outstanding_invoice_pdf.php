<!doctype html>
<head>
    <meta charset="UTF-8">
    <style>
        @page {
            /* margin: 20px; */
            /* rapat, bisa diubah ke 0 jika benar-benar ingin full page */
            margin: 80px 20px 30px 20px;
            header: page-header;
            footer: page-footer;
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
            top: -60px;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        th:nth-child(1){
            width: 5px;
        }
        th:nth-child(2){
            width: 100px;
        }
        th:nth-child(3){
            width: 80px;
        }
        th:nth-child(90){
            width: 100px;
        }

        th:nth-child(5){
            width: 55px;
        }
        th:nth-child(10){
            width: 40px;
        }
        
        th:nth-child(6),
        th:nth-child(7),
        th:nth-child(8),
        th:nth-child(9){
            width: 85px;
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
    <table border="1">
        <thead>
            <tr>
                <th>No. </th>
                <!-- <th class='style' style="width: 60px">Supplier</th> -->
                <th>Invoice</th>
                <th>PO</th>
                <th>Receiving</th>
                <th>Tanggal</th>
                <th>Total Hutang (Rp)</th>
                <th>Sisa Hutang (Rp)</th>
                <th>Total Hutang (Valas)</th>
                <th>Sisa Hutang (Valas)</th>
                <th>Umur (Hari)</th>
            </tr>
        </thead>
        <tbody>
            <?php
          
            $total_hutang = 0;
            $total_sisa_hutang = 0;
            foreach ($list as $rows) {

            ?>
                <tr>
                    <td colspan='10' class='bold'>Suppplier : <?php echo $rows['nama_partner'] ?></td>
                </tr>
            <?php
                $no = 1;
                $total_hutang = 0;
                $total_sisa_hutang = 0;
                $total_hutang_valas = 0;
                $total_sisa_hutang_valas = 0;
                foreach($rows['tmp_data_items'] as $items) {
            ?>
                <tr>
                    <td><?php echo $no++; ?></td>
                    <!-- <td ></td>  -->
                    <td ><?php echo $items['no_invoice'] ?></td>
                    <td ><?php echo $items['no_po'] ?></td>
                    <td ><?php echo $items['origin'] ?></td>
                    <td ><?php echo $items['tanggal'] ?></td>
                    <td class='text-right'><?php echo number_format($items['hutang_rp'],2) ?></td>
                    <td class='text-right'><?php echo number_format($items['sisa_hutang_rp'],2) ?></td>
                    <td class='text-right'><?php echo number_format($items['hutang_valas'],2) ?></td>   
                    <td class='text-right'><?php echo number_format($items['sisa_hutang_valas'],2) ?></td>
                    <td class='text-right'><?php echo $items['hari'] ?></td>
                </tr>

            <?php
                    $total_hutang = $total_hutang + $items['hutang_rp'];
                    $total_sisa_hutang = $total_sisa_hutang + $items['sisa_hutang_rp'];
                    $total_hutang_valas = $total_hutang_valas + $items['hutang_valas'];
                    $total_sisa_hutang_valas = $total_sisa_hutang_valas + $items['sisa_hutang_valas'];
               }
            ?>
                <tr>
                    <td class="bold text-right"colspan="5">Total : </td>
                    <td class="text-right"><?php echo number_format($total_hutang, 2); ?></td>
                    <td class="text-right"> <?php echo number_format($total_sisa_hutang, 2); ?></td>
                    <td class="text-right"><?php echo number_format($total_hutang_valas, 2); ?></td>
                    <td class="text-right"> <?php echo number_format($total_sisa_hutang_valas, 2); ?></td>
                    <td></td>
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