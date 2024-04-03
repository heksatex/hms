<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        <link rel="shortcut icon"  href="<?php echo base_url('dist/img/favicon_heksa.ico') ?>">
        <!-- Bootstrap 3.3.6 -->
        <link rel="stylesheet" href="<?php echo base_url('bootstrap/css/bootstrap.min.css') ?>">
        <link rel="stylesheet" href="<?php echo base_url('dist/fa/css/font-awesome.min.css') ?>">
        <?php $this->load->view("print/do/css.php") ?>
    </head>

    <body >
        <div class="container">
            <div class="row">
                <div class="col-xs-4">
                    <img style="width: 30%" src="<?= base_url('dist/img/static/heksatex_c.jpg') ?>" >
                    <strong><span style="margin: auto;
                                  font-size: 14px;">PT HEKSATEX INDAH</span></strong>
                </div>
                <div class="col-xs-4">
                    <div class="title" style="text-align: center">
                        <span>
                            packing list (pl)
                        </span>
                    </div>

                </div>
                <div class="col-xs-4 item-right" style="margin-top: 10px;" >

                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-xs-2">
                                    <label class="form-label">No</label>
                                </div>
                                <div class="col-xs-10">
                                    <label class="form-label nosjprint" style="font-weight: 500;">: <?= $base->no_sj ?></label>
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-4">
                                        <label class="form-label">Tanggal</label>
                                    </div>
                                    <div class="col-xs-8">
                                        <label class="form-label" style="font-size: 15px; font-weight: 500;">: <?= date("d-M-Y") ?></label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <table class="table table-bordered header-table" style="width:100%">
                    <thead>
                        <tr>
                            <td rowspan="2" class="row-1" style="font-size: 15px;
                                width: 10%">
                                <p>Kepada</p>
                                <p>Alamat</p>

                            </td>
                            <td rowspan="2" style="width: 45%">
                                <p><?= $base->nama ?></p>
                                <p><?= $base->alamat ?></p>
                            </td>
                            <td style="font-weight: 600;
                                font-size: 13px;
                                width: 45%">Catatan</td>
                        </tr>
                        <tr>
                            <td rowspan="2" style="font-size: 12px;" id="catatan"><?= nl2br($base->note) ?> </td>
                        </tr>
                        <tr>
                            <td class="row-1" style="font-size: 15px;">SC</td>
                            <td style="font-size: 10px;"><?= $base->sc ?? "" ?></td>
                        </tr>

                    </thead>
                </table>
            </div>
            <div class="row">
                <table class="table table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 10px;">No</th>
                            <?php if ((int) $base->type_bulk_id === 1) { ?>
                                <th rowspan="2" style="width: 25px;">BAL ID</th>
                            <?php } ?>
                            <th rowspan="2" style="width: 210px;">Corak Design</th>
                            <th rowspan="2">Warna</th>
                            <th colspan="10">Rincian Qty/Pcs/GL</th>
                            <th rowspan="2" style="width: 20px;">GL/PCS</th>
                            <th rowspan="2" style="width: 120px;">Total QTY</th>
                        </tr>
                        <tr style="text-align: center;">
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

                    <?php if ((int) $base->type_bulk_id === 1) { ?>
                        <tbody>
                            <?php
                            $no = 0;
                            $jml_qty = 0;
                            $total_qty = 0;
                            $id = null;
                            $satuan = '';
                            $bulk = null;
                            $sub_jml_qty = 0;
                            $sub_total_qty = 0;
                            foreach ($data as $key => $value) {
                                $no++;
                                $jml_qty += $value->jumlah_qty;
                                $total_qty += $value->total_qty;
//                                log_message('error', $value->jumlah_qty . " - " . $sub_jml_qty);
                                $detailQty = $this->m_deliveryorderdetail->detailReportQty([
                                    'bulk_no_bulk' => $value->bulk_no_bulk, 'corak_remark' => $value->corak_remark, 'warna_remark' => $value->warna_remark, 'uom' => $value->uom, 'no_pl' => $base->no_picklist
                                        ], true);
                                $perpage = 10;
                                $totalData = count($detailQty);
                                $totalPage = ceil($totalData / $perpage);
                                if (!is_null($bulk)) {
                                    if ($bulk !== $value->bulk_no_bulk) {
                                        ?>
                                        <tr style="text-align: center;">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td><?= $sub_jml_qty ?? 0 ?></td>
                                            <td><?= ($sub_total_qty ?? 0) . ' ' . $satuan ?></td>
                                        </tr>
                                        <?php
                                        $sub_total_qty = 0;
                                        $sub_jml_qty = 0;
                                    }
                                }

                                $sub_jml_qty += $value->jumlah_qty;
                                $sub_total_qty += $value->total_qty;
                                for ($nn = 0; $nn < $totalPage; $nn++) {
                                    $page = $nn * $perpage;
                                    $satuan = $detailQty[0]->uom;
                                    $tempID = $value->warna_remark . $value->corak_remark . $value->uom;
                                    $tempBulk = $value->bulk_no_bulk;
                                    ?>
                                    <tr style="text-align: center;">

                                        <td><?= ($bulk === $tempBulk) ? '' : $no ?></td>
                                        <td><?= ($bulk === $tempBulk) ? '' : $value->bulk_no_bulk ?></td>
                                        <td style="text-align: left;"><?= str_replace('|', ' ', $value->corak_remark . ' ' . $value->lebar_jadi . ' ' . $value->uom_lebar_jadi) ?></td>
                                        <td style="text-align: left;"><?= str_replace('|', ' ', $value->warna_remark) ?></td>

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


                                        <td style=" text-align: center;"><?= $value->jumlah_qty ?></td>
                                        <td style="text-align: center;"><?= $value->total_qty . ' ' . $satuan ?></td>
                                    </tr>
                                    <?php
                                    $id = $tempID;
                                    $bulk = $tempBulk;
                                }
                            }
                            ?>
                            <tr style="text-align: center;">
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><?= $sub_jml_qty ?? 0 ?></td>
                                <td><?= ($sub_total_qty ?? 0) . ' ' . $satuan ?></td>
                            </tr>
                        </tbody>
                        <?php
                    } else {
                        ?>
                        <tbody>
                            <?php
                            $no = 0;
                            $jml_qty = 0;
                            $total_qty = 0;
                            $id = null;
                            $satuan = '';
                            foreach ($data as $key => $value) {
                                $no++;
                                $jml_qty += $value->jumlah_qty;
                                $total_qty += $value->total_qty;
                                $detailQty = $this->m_deliveryorderdetail->detailReportQty(['corak_remark' => $value->corak_remark, 'warna_remark' => $value->warna_remark, 'uom' => $value->uom, 'no_pl' => $base->no_picklist]);
                                $perpage = 10;
                                $totalData = count($detailQty);
                                $totalPage = ceil($totalData / $perpage);

                                for ($nn = 0; $nn < $totalPage; $nn++) {
                                    $page = $nn * $perpage;
                                    $satuan = $detailQty[0]->uom;
                                    $tempID = $value->warna_remark . $value->corak_remark . $value->uom;
                                    ?>
                                    <tr style="text-align: center;">

                                        <td><?= ($id === $tempID) ? '' : $no ?></td>
                                        <td style="text-align: left;"><?= ($id === $tempID) ? '' : str_replace('|', ' ', $value->corak_remark . ' ' . $value->lebar_jadi . ' ' . $value->uom_lebar_jadi) ?></td>
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


                                        <td><?= ($id === $tempID) ? '' : $value->jumlah_qty ?></td>
                                        <td style="text-align: right;"><?= ($id === $tempID) ? '' : $value->total_qty . ' ' . $satuan ?></td>
                                    </tr>
                                    <?php
                                    $id = $tempID;
                                }
                            }
                            ?>
                        </tbody>
                    <?php } ?>
                </table>
                <div class="col-xs-4">
                    <p><strong>Sub - Total GL/PCS :</strong> <?= $jml_qty ?> </p>
                    <p><strong>Sub - Total Qty :</strong> <?= $total_qty ?> </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div style="display: flex;
                         flex-direction: row-reverse;
                         justify-content: flex-start;">
                        <div style="padding-left: 50px;">
                            <p style="padding-bottom: 50px;">Dibuat Oleh,</p>

                            <span style="text-align: center;">( ...................... )</span>
                        </div>
                        <div>
                            <p style="padding-bottom: 50px;">Mengetahui,</p>
                            <span style="text-align: center; font-weight: 600;">( KABAG )</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </body>
</html>