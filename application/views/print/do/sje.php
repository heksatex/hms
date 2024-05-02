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

    <body>
        <div class="container">
            <div class="row">
                <div class="col-xs-4">
                    <img style="width: 30%" src="<?= base_url('dist/img/static/heksatex_c.jpg') ?>" >
                    <strong><span style="margin: auto;
                                  font-size: 14px;">PT HEKSATEX INDAH</span></strong>
                </div>
                <div class="col-xs-4">
                    <div class="title" style="text-align: center;">
                        <span>
                            surat jalan
                        </span>
                    </div>

                </div>
                <div class="col-xs-4 item-right" style="margin-top: 10px;" >

                    <div class="form-group">
                        <div class="col-xs-12">
                            <div class="row">
                                <label class="form-label nosjprint" style="font-weight: 600;">No&nbsp;: <?= $base->no_sj ?></label>
                                <!--                                <div class="col-xs-2">
                                                                    <label class="form-label">No</label>
                                                                </div>
                                                                <div class="col-xs-10" style="margin-left: -10px;">
                                                                    <label class="form-label nosjprint" style="font-weight: 600;">: <?= $base->no_sj ?></label>
                                                                </div>-->

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="row">
                                    <label class="form-label" style="font-size: 14px; font-weight: 600;">No&nbsp;: <?= date("d-M-Y") ?></label>
                                    <!--                                    <div class="col-xs-4">
                                                                            <label class="form-label">Tanggal</label>
                                                                        </div>
                                                                        <div class="col-xs-8">
                                                                            <label class="form-label" style="font-size: 15px; font-weight: 600;">: <?= date("d-M-Y") ?></label>
                                                                        </div>-->
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <table class="table header-table" style="width:100%">
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
                            <td rowspan="3" style="font-size: 10px;" id="catatan"><?= nl2br($base->note) ?> </td>
                        </tr>
                        <tr>
                            <td class="row-1" style="font-size: 15px;">SC</td>
                            <td style="font-size: 10px;"><?= $base->sc ?? "" ?></td>
                        </tr>
                        <tr>
                            <?php if ((int) $base->type_bulk_id === 1) { ?>
                                <td class="row-1" style="font-size: 15px; width:15%">Total BAL / BULK</td>
                                <td style="font-size: 10px;"><?= $count_bulk ?? 0 ?></td>
                            <?php } ?>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="row">
                <table class="table" style="width:100%">
                    <thead>
                        <tr style="text-align: center;">
                            <td class="row-1">No</td>
                            <td  style="font-weight: 600;">Deskripsi</td>
                            <td  style="width: 150px;
                                 font-weight: 600;
                                 text-align: right;">Jumlah</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total_jumlah = 0;
                        foreach ($data as $key => $value) {
                            $total_jumlah += $value->total_qty;
                            ?>
                            <tr>
                                <td style="text-align: center;">
                                    <?= $key + 1 ?>
                                </td>
                                <td >
                                    <?= $value->corak_remark . ' ' . $value->warna_remark ?>
                                </td>
                                <td style="text-align: right;">
                                    <?= $value->total_qty . " " . $value->uom ?>
                                </td>
                            </tr>

                        <?php } ?>
                        <tr>
                            <td></td>
                            <td  style="text-align: center;font-weight: 600;">TOTAL</td>
                            <td  style="text-align: right;"><?= $total_jumlah ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="footer">
                    <div class="divs" style="font-weight: 600;">
                        <p style="text-align: center;">Penerima,</p>
                        <span style="text-align: center;">( ..................... )</span>
                    </div>
                    <div style="font-weight: 600;
                         height: 50px;">
                        <div style="
                             font-size: 12px;
                             text-align: center;">
                            <p>No Mobil / Container : ...............................</p>
                        </div>
                    </div>
                    <div class="divs" style="font-weight: 600;">
                        <p>Hormat Kami,</p>
                        <span style="text-align: center;">( ...................... )</span>
                    </div>
                </div>
            </div>
    </body>
</html>