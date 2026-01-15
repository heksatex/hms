<!doctype html>
<html lang="en">
    <!--<meta charset="UTF-8">-->
    <head>
        <style>
            .header {
                width: 100%;
                height: 80px;
                display: block;
            }
            #row:after {
                content: "";
                display: table;
                clear: both;
            }

            #column {
                float: left;
                width: 33.33%;
                text-align: left;
                height: 30px;
            }
            #column-5 {
                float: left;
                width: 20%;
                text-align: left;
                height: 30px;
            }
            th,td {
                border-bottom: 1px solid black;
                border-top: 1px solid black;
            }
            th,td{
                padding: 2px;
            }
            tfoot td {
                 border: 0px solid black;
            }
        </style>
    </head>
    <body style="padding:  0 30px 0 30px;font-size: 10px;">
        <div id="row">
            <div id="column">
                <p><?= "Tanggal : ".date("d-m-Y",strtotime($head->tanggal)) ?></p>
            </div>
            <div id="column">

            </div>
            <div id="column" style="margin-left: 10px">
                <p><?= "No : {$head->no_gk}" ?></p>
            </div>
        </div>
        <div id="row">
            <div id="column">

            </div>
            <div id="column" style="line-height: 0.5;">
                <h2><strong>BUKTI GIRO KELUAR</strong></h2>
                <p><?= $head->nama_coa ?></p>
                <p><?= "No Acc (Kredit) : {$head->kode_coa}" ?></p>
            </div>
            <div id="column" style="line-height: 0.5;margin-left: 10px;margin-top: 5px">
                <p><?= "Dari : {$head->partner_nama}" ?></p>
                <p><?= "LAIN-LAIN :{$head->lain2}" ?></p>
            </div>
        </div>
        <div id="row"style="line-height: 0.5;">
            <p>Untuk Transaksi : <?= $head->transinfo ?></p>
            <table cellspacing="0" style="width: 100%;border-collapse: collapse; border-bottom: 1px solid black;">
                <tr>
                    <th style="width: 20px">No</th>
                    <th style="width: 100px;text-align: left;">Bank</th>
                    <th style="width: 100px;text-align: left;">No Rek</th>
                    <th style="width: 80px;text-align: left;">No/Cek BG</th>
                    <th style="width: 100px;text-align: left;">Tgl Jt</th>
                    <th style="width: 50px;text-align: left;">Tgl Cair</th>
                    <th style="width: 90px;text-align: left;">No Acc(Debet)</th>
                    <th style="width: 100px;text-align: right;">Kurs</th>
                    <th style="width: 50px;text-align: left;">Curr</th>;
                    <th style="width: 120px;text-align: right;">Nominal</th>
                </tr>
                <tbody>
                    <?php
                    $totals = 0;
                    foreach ($detail as $key => $value) {
                        $totals += $value->nominal;
                        ?>
                    <tr>
                        <td>
                            <?= $key + 1 ?>
                        </td>
                        <td>
                            <?= $value->bank ?>
                        </td>
                        <td>
                            <?= $value->no_rek ?>
                        </td>
                        <td>
                            <?= $value->no_bg ?>
                        </td>
                        <td><?= date("d-m-Y", strtotime($value->tgl_jt)) ?></td>
                        <td></td>
                        <td>
                            <?= $value->kode_coa ?>
                        </td>
                        <td style="text-align: right">
                            <?= number_format($value->kurs, 2) ?>
                        </td>
                        <td>
                            <?= "{$value->curr}" ?>
                        </td>
                        <td style="text-align: right">
                            <?= number_format($value->nominal, 2) ?>
                        </td>
                    </tr>
                        <?php
                    }
                    ?>
                </tbody>
                <tfoot style="border: 0px">
                    <tr>
                        <td style="border: 0px">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td style="text-align: center"><strong>Total</strong></td>
                         <td style="text-align: right">
                            <strong><?= number_format($totals, 2) ?></strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div id="row" style="padding-bottom: 10px;">
            
        </div>
        <div id="row" >
            <div id="column-5" style="text-align: center;">
               Diinput oleh : 
               <br>
               <br>
               <br>
               <p>(_____________)</p>
            </div>
            <div id="column-5" style="text-align: center;">
                Diterima Oleh : 
               <br>
               <br>
               <br>
               <p>(_____________)</p>
            </div>
            <div id="column-5" style="text-align: center;">
               Disetujui oleh : 
               <br>
               <br>
               <br>
               <p>(_____________)</p>
            </div>
            <div id="column-5" style="text-align: center;">
               Mengetahui : 
               <br>
               <br>
               <br>
               <p>(_____________)</p>
            </div>
            <div id="column-5" style="text-align: center;">
               Dikeluarkan oleh : 
               <br>
               <br>
               <br>
               <p>(_____________)</p>
            </div>
        </div>
    </body>
</html>