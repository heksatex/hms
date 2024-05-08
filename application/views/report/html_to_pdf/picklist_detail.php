<!doctype html>
<html>

    <head>
        <meta charset="UTF-8">
        <style>


            table {
                background: #fff;
                border-collapse: collapse;

                width: 100%;
                text-align: center;
            }
            table, thead, tbody, tfoot, tr, td, th {
                text-align: center;
                margin: auto;
                border: 0.5px solid #dedede;
            }
            body {
                size: a4;
            }
            .header {
                width: 100%;
                height: 80px;
                display: block;
            }
            .main {
                width: 100%;
                height: 7%;
                display: block;
                padding-bottom: 12px;
            }
            .footer {
                display: block;
            }
            .footer div {
                float: right;
                padding: 10px;
                width: 20%;
                font-size: 12px;
                text-align: center;
            }
            .caption_table {
                /*height: 80px;*/
                display: block;
                font-size: 12px;
            }
            table{
            }
            table thead{
                font-size: 12px;
            }
            table tfoot{
                font-size: 12px;
                font-weight: bold;
            }
            table tbody {
                font-size: 11px;
            }
            #tableHeader {
                background: #fff;
                border-collapse: collapse;

                width: 100%;
                padding-bottom: 2px;
            }
            #tableHeader tr td{
                text-align: left;
                vertical-align:top;
            }

        </style>
    </head>
    <body>

        <div class="header">
            <div style="float: left; width: 25%;">
                <img style="width: 30%" src="<?= $logo ?>" >
                <strong><span style="margin: auto;font-size: 10px;">PT HEKSATEX INDAH</span></strong>
            </div>
            <div style="float: right; width: 25%">
                <img style="width: 100%;height: 35%" src="<?= $barcode ?>" ><br>
                <div style="float: left; font-size: 12px">
                    <span><?= $picklist->bulk ?></span><br>
                    <span><?= $picklist->jenis_jual ?></span>
                </div>
                <div style="float: right; font-size: 12px">
                    <span><?= $nopl ?? "" ?></span><br>
                    <span>SC</span>
                </div>
            </div>
            <div style="position: fixed;
                 background-color: white;
                 top: 1%;
                 margin-left: 40%;
                 width: 25%;">
                <strong><u>PICK LIST (PL)</u></strong><br>
                <span style="font-size: 12px"><?= date("d F Y", strtotime($picklist->tanggal_input)) ?></span>

            </div>
        </div>

        <div class="">
            <table id="tableHeader">
                <tr>
                    <td style="width: 50%;">
                        <p><strong>Kepada</strong> <b><?= $picklist->nama ?></b></p>
                        <p><strong>Alamat</strong> <span><?= $picklist->alamat ?></span></p>
                    </td>
                    <td style="width: 50%; overflow: auto;"> 
                        <p><strong>Catatan</strong></p>
                        <?= nl2br($picklist->keterangan); ?>
                    </td>
                </tr>
            </table>

        </div>
        <div>
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">No</th>
                        <th rowspan="2">Corak Design</th>
                        <th rowspan="2">Warna</th>
                        <th colspan="10">Rincian Qty/Pcs/GL</th>
                        <th rowspan="2">GL/PCS</th>
                        <th rowspan="2">Total QTY</th>
                    </tr>
                    <tr>
                        <td>1</td>
                        <td>2</td>
                        <td>3</td>
                        <td>4</td>
                        <td>5</td>
                        <td>6</td>
                        <td>7</td>
                        <td>8</td>
                        <td>9</td>
                        <td>10</td>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 0;
                    $jml_qty = 0;
                    $total_qty = 0;
//                $table = ['corak' => '', 'warna' => ''];
                    $id = null;
                    $satuan = '';
                    foreach ($picklist_detail as $key => $value) {
                        $no++;
                        $jml_qty += $value->jml_qty;
                        $total_qty += $value->total_qty;
                        $detailQty = $this->m_PicklistDetail->detailReportQty(['valid !='=>'cancel','corak_remark' => $value->corak_remark, 'warna_remark' => $value->warna_remark, 'uom' => $value->uom, 'no_pl' => $value->no_pl]);
                        $perpage = 10;
                        $totalData = count($detailQty);
                        $totalPage = ceil($totalData / $perpage);
                        for ($nn = 0; $nn < $totalPage; $nn++) {
                            $page = $nn * $perpage;
                            $satuan = $detailQty[0]->uom;
                            $tempID = $value->warna_remark . $value->corak_remark . $value->uom;
                            ?>
                            <tr>

                                <td><?= ($id === $tempID) ? '' : $no ?></td>
                                <td style="text-align: left;"><?= ($id === $tempID) ? '' : str_replace('|', ' ', $value->corak_remark.' '.$value->lebar_jadi.' '.$value->uom_lebar_jadi) ?></td>
                                <td style="text-align: left;"><?= ($id === $tempID) ? '' : str_replace('|', ' ', $value->warna_remark) ?></td>

                                <td><?= isset($detailQty[$page + 0]) ? (float) $detailQty[$page + 0]->qty : "" ?></td>
                                <td><?= isset($detailQty[$page + 1]) ? (float) $detailQty[$page + 1]->qty : "" ?></td>
                                <td><?= isset($detailQty[$page + 2]) ? (float) $detailQty[$page + 2]->qty : "" ?></td>
                                <td><?= isset($detailQty[$page + 3]) ? (float) $detailQty[$page + 3]->qty : "" ?></td>
                                <td><?= isset($detailQty[$page + 4]) ? (float) $detailQty[$page + 4]->qty : "" ?></td>
                                <td><?= isset($detailQty[$page + 5]) ? (float) $detailQty[$page + 5]->qty : "" ?></td>
                                <td><?= isset($detailQty[$page + 6]) ? (float) $detailQty[$page + 6]->qty : "" ?></td>
                                <td><?= isset($detailQty[$page + 7]) ? (float) $detailQty[$page + 7]->qty : "" ?></td>
                                <td><?= isset($detailQty[$page + 8]) ? (float) $detailQty[$page + 8]->qty : "" ?></td>
                                <td><?= isset($detailQty[$page + 9]) ? (float) $detailQty[$page + 9]->qty : "" ?></td>


                                <td><?= ($id === $tempID) ? '' : $value->jml_qty ?></td>
                                <td style="text-align: right;"><?= ($id === $tempID) ? '' : number_format($value->total_qty, 2, ".", ",") . ' ' . $satuan ?></td>
                            </tr>
                            <?php
                            $id = $tempID;
                        }
                    }
                    ?>

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="13"></td>
                        <td><?= $jml_qty ?></td>
                        <td><?= number_format($total_qty, 2, ".", ",") ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="footer">
            <div>
                <span><strong>Dibuat Oleh,</strong></span>
                <span style="margin-top: 50px; display: block; font-weight: bold;">[<?= $picklist->nama_user ?>]</span>
            </div>

            <div>
                <span><strong>Mengetahui,</strong></span>
                <span style="margin-top: 50px; display: block; font-weight: bold;">[<?= $picklist->sales ?>]</span>
            </div>
        </div>

    </body>
</html>