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
                                  font-size: 12px;">PT HEKSATEX INDAH</span></strong>
                </div>
                <div class="col-xs-3">
                    <div class="title" style="text-align: end;">
                        <span>
                            surat jalan
                        </span>
                    </div>

                </div>
                <div class="col-xs-5 item-right" style="margin-top: 10px;">
                    <div class="row">
                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="col-xs-2">
                                    <label class="form-label">No.</label>
                                </div>
                                <div class="col-xs-2">
                                    <label class="form-label">:</label>
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-label nosjprint"><?= $base->no_sj ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-xs-12">
                                <div class="col-xs-2">
                                    <label class="form-label">Tanggal</label>
                                </div>
                                <div class="col-xs-2">
                                    <label class="form-label">:</label>
                                </div>
                                <div class="col-xs-6">
                                    <label class="form-label" style="font-size: 15px;"><?= date("d-M-Y") ?></label>
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
                                width: 10%">Kepada Alamat</td>
                            <td rowspan="2" style="width: 45%"><?= $base->alamat ?></td>
                            <td style="font-weight: 600;
                                font-size: 13px;
                                width: 45%">Catatan</td>
                        </tr>
                        <tr>
                            <td rowspan="2" style="font-size: 10px;" id="catatan"><?= nl2br($base->note) ?> </td>
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
                        <tr style="text-align: center;">
                            <?php if ((int) $base->type_bulk_id === 1) { ?>
                                <td class="row-1">BAL ID</td>
                            <?php } ?>
                            <td  style="font-weight: 600">Deskripsi</td>
                            <td style="width: 150px;
                                font-weight: 600">Qty/Pcs/GI</td>
                            <td  style="width: 150px;
                                 font-weight: 600;
                                 text-align: right;">Jumlah</td>
                        </tr>
                    </thead>
                    <?php if ((int) $base->type_bulk_id === 1) { ?>
                        <tbody>
                            <?php
                            $total_pcs = 0;
                            $total_jumlah = 0;
                            foreach ($data as $key => $value) {
                                $total_jumlah += $value->total_qty;
                                $total_pcs += $value->jumlah_qty;
                                ?>
                                <tr>

                                    <td>
                                        <?= $value->bulk_no_bulk ?>
                                    </td>
                                    <td>
                                        <div class="deskripsi">
                                            <?= $value->corak_remark . ' ' . $value->warna_remark ?>
                                        </div>
                                        <hr>
                                        <span><strong>Total BAL </strong> <?= $value->bulk_no_bulk ?></span>
                                    </td>
                                    <td style="text-align: right;
                                        font-weight: 600;">
                                        <div class="deskripsi" style="justify-content: end;">
                                            <?= $value->jumlah_qty ?>
                                        </div>
                                        <hr>
                                        <span style="text-align: right"><?= $value->jumlah_qty ?></span>
                                    </td>
                                    <td style="text-align: right;
                                        font-weight: 600;">
                                        <div class="deskripsi" style="justify-content: end;">
                                            <?= $value->total_qty . " " . $value->uom ?>
                                        </div>
                                        <hr>
                                        <span style="text-align: right"><?= $value->total_qty . " " . $value->uom ?></span>
                                    </td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    <?php } else { ?>
                        <tbody>
                            <?php
                            $total_pcs = 0;
                            $total_jumlah = 0;
                            foreach ($data as $key => $value) {
                                $total_jumlah += $value->total_qty;
                                $total_pcs += $value->jumlah_qty;
                                ?>
                                <tr>
                                    <td>
                                        <div class="deskripsi">
                                            <?= $value->corak_remark . ' ' . $value->warna_remark ?>
                                        </div>
                                    <td style="text-align: right;
                                        font-weight: 600;">
                                        <div class="deskripsi" style="justify-content: end;">
                                            <?= $value->jumlah_qty ?>
                                        </div>
                                    </td>
                                    <td style="text-align: right;
                                        font-weight: 600;">
                                        <div class="deskripsi" style="justify-content: end;">
                                            <?= $value->total_qty . " " . $value->uom ?>
                                        </div>
                                    </td>
                                </tr>

                            <?php } ?>
                        </tbody>
                    <?php } ?>
                </table>
                <table class="table" style="width:100%;
                       margin-top: -30px;
                       border: none !important ">
                    <tr>
                        <td class="total_pcs">
                            <div class="deskripsi" style="justify-content: end;
                                 font-weight: 600;">
                                 <?= $total_pcs ?>
                            </div>
                        </td>
                        <td class="total_total">
                            <div class="deskripsi" style="justify-content: end;
                                 font-weight: 600;">
                                 <?= $total_jumlah ?>
                            </div>

                        </td>
                    </tr>
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
                        <div style="border: solid 1px black;
                             font-size: 10px;
                             text-align: center;">
                            <p>Pengaduan-pengaduan / Claim melebihi</p>
                            <p>7(tujuh) hari dan tanggal pengiriman barang</p>
                            <p>tersebut diatas TIDAK diterima</p>
                        </div>
                    </div>
                    <div class="divs" style="font-weight: 600;">
                        <p>Hormat Kami,</p>
                        <span style="text-align: center;">( ...................... )</span>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>