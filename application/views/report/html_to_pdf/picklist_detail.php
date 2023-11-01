<body>
    <div class="header">
        <div style="float: left; width: 25%;">
            <img style="width: 30%" src="<?= $logo ?>" >
            <strong><span style="margin: auto;font-size: 10px;">PT HEKSATEX INDAH</span></strong>
        </div>
        <div style="float: right; width: 25%">
            <img style="width: 100%;height: 35%" src="<?= $barcode ?>" ><br>
            <div style="float: left; font-size: 10px">
                <span><?= $picklist->bulk ?></span><br>
                <span><?= $picklist->jenis_jual ?></span>
            </div>
            <div style="float: right; font-size: 10px">
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

    <div class="main">
        <div class="caption_table">
            <div style="float: left; width: 49%; border: 1px solid black;padding-right: 1px;">
                <p><strong>Kepada</strong> <?= $picklist->nama ?></p>
                <p><strong>Alamat</strong> <span><?= $picklist->alamat ?></span></p>
            </div>
            <div style="float: right; width:49%; border: 1px solid black;padding-left: 1px;">
                <p><strong>Catatan</strong></p>
                <p><?= $picklist->keterangan ?></p>
            </div>
        </div>

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
                    $detailQty = $this->m_PicklistDetail->detailReportQty(['corak_remark' => $value->corak_remark, 'warna_remark' => $value->warna_remark]);
                    $perpage = 10;
                    
                    $totalPage = ceil(count($detailQty) / $perpage);
                    for ($nn = 0; $nn < $totalPage; $nn++) {
                        $page = $nn * $perpage;
                        $satuan = $detailQty[0]->uom;
                        ?>
                        <tr style="border: 1px solid black;">

                            <td><?= ($id === $value->warna_remark.$value->corak_remark) ? '' : $no ?></td>
                            <td><?= ($id === $value->warna_remark.$value->corak_remark) ? '' : $value->corak_remark ?></td>
                            <td><?= ($id === $value->warna_remark.$value->corak_remark) ? '' : $value->warna_remark ?></td>

                            <td><?= isset($detailQty[$page + 0]) ? (int) $detailQty[$page + 0]->qty : "" ?></td>
                            <td><?= isset($detailQty[$page + 1]) ? (int) $detailQty[$page + 1]->qty : "" ?></td>
                            <td><?= isset($detailQty[$page + 2]) ? (int) $detailQty[$page + 2]->qty : "" ?></td>
                            <td><?= isset($detailQty[$page + 3]) ? (int) $detailQty[$page + 3]->qty : "" ?></td>
                            <td><?= isset($detailQty[$page + 4]) ? (int) $detailQty[$page + 4]->qty : "" ?></td>
                            <td><?= isset($detailQty[$page + 5]) ? (int) $detailQty[$page + 5]->qty : "" ?></td>
                            <td><?= isset($detailQty[$page + 6]) ? (int) $detailQty[$page + 6]->qty : "" ?></td>
                            <td><?= isset($detailQty[$page + 7]) ? (int) $detailQty[$page + 7]->qty : "" ?></td>
                            <td><?= isset($detailQty[$page + 8]) ? (int) $detailQty[$page + 8]->qty : "" ?></td>
                            <td><?= isset($detailQty[$page + 9]) ? (int) $detailQty[$page + 9]->qty : "" ?></td>


                            <td><?= ($id === $value->warna_remark.$value->corak_remark) ? '' : $value->jml_qty ?></td>
                            <td><?= ($id === $value->warna_remark.$value->corak_remark) ? '' : $value->total_qty.' '.$satuan ?></td>
                        </tr>
                        <?php
                        $id = $value->warna_remark.$value->corak_remark;
                    }
                }
                ?>

            </tbody>
            <tfoot>
                <tr>
                    <td colspan="13"></td>
                    <td  style="border: 1px solid black;"><?= $jml_qty ?></td>
                    <td  style="border: 1px solid black;"><?= $total_qty.' '.$satuan ?></td>
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
            <span style="margin-top: 50px; display: block; font-weight: bold;">[<?= $picklist->sales_kode ?>]</span>
        </div>
    </div>

</body>
<style type="text/css">
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
        width: 100%;
        border: 1px solid black;
        text-align: center;
    }
    table thead{
        font-size: 12px;
    }
    table tfoot{
        font-size: 12px;
        font-weight: bold;
    }
    table tbody {
        border: 1px solid black;
        font-size: 11px;
    }

</style>