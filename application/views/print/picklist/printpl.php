<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="<?php echo base_url('bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('dist/fa/css/font-awesome.min.css') ?>">
    <style>
        *{
            margin-top: 5px;
        }
        .containerExpanded {
            -webkit-column-count: 2;
            -webkit-column-gap: 2;
        }
        .wrapped {
            font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
            -webkit-column-break-inside: avoid;
            font-size: 11px;
        }
        .blokasi{
            text-align: right;
            background-color: #adacac !important;
        }
        table {
            font-size: 13px;
        }
        .tdc {
            padding-left: 10px;
        }
        tfoot{
            border-top: solid 1px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-xs-4">
                <table style="width: 100%">
                    <tr>
                        <td class="blokasi">Total Lokasi</td>
                        <td class="tdc"><?= count($data) ?></td>
                    </tr>
                    <tr>
                        <td class="blokasi">Total Pick</td>
                        <td class="tdc"><?= $total ?></td>
                    </tr>
                </table>
            </div>

            <div class="col-xs-4">
                <div style="text-align: center; font-size: 150%;font-weight: 600;text-decoration: underline;">
                    PICK LIST
                </div>
            </div>

            <div class="col-xs-4">
                <table style="width: 100%">
                    <tr>
                        <td class="blokasi">No.</td>
                        <td class="tdc"><?= $pl ?? "" ?></td>
                    </tr>
                    <tr>
                        <td class="blokasi">Print Date</td>
                        <td class="tdc"><?= date("d-M-y") ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row">
            <div class="containerExpanded">
                <?php
                foreach ($data as $keys => $values) {
                    $totalpick = 0;
                    ?>
                    <div class="wrapped">


                        <table style="width: 100%" class="content-table">
                            <caption>
                                <strong>Lokasi</strong> : <?= $keys ?>
                            </caption>
                            <thead>
                            <th>No.</th>
                            <th>Barcode ID</th>
                            <th>Corak</th>
                            <th>Warna</th>
                            <th>Quantity</th>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($values as $key => $value) {
                                    $totalpick++;
                                    ?>
                                    <tr>
                                        <td>
                                            <?= $key + 1 ?>
                                        </td>
                                        <td>
                                            <?= $value->barcode_id ?>
                                        </td>
                                        <td>
                                            <?= substr($value->corak_remark, 0, 10) ?>
                                        </td>
                                        <td>
                                            <?= substr($value->warna_remark, 0, 10) ?>
                                        </td>
                                        <td>
                                            <?= $value->qty ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <table style="border-top: solid 1px;width: 100%;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="text-align: right">
                                <strong>Lokasi <?= $keys ?></strong>
                            </td>
                            <td style="text-align: center">
                                <strong>Total Pick <?= $totalpick ?></strong>
                            </td>
                        </table>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">

            </div>
            <div class="col-xs-4">
                <div style="text-align: center;font-weight: 500; margin-top: 40%;">
                    Yang Memerintahkan
                </div>

                <div style="text-align: center;font-weight: 400; margin-top: 25%;">
                    ( <?= $this->session->userdata('nama')["nama"] ?? "" ?> )
                </div>
            </div>
        </div>
    </div>
</body>