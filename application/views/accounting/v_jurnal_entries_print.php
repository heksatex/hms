<!doctype html>
<html lang="en">
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
            #column-news-2 {
                float: left;
                width: 50%;
                text-align: left;
            }
            #column-news-1 {
                float: left;
                width: 50%;
                text-align: left;
            }
            th,td {
                border: 1px solid black;
                border-collapse: collapse;
                padding: 3px;
            }
        </style>
    </head>

    <body style="padding:  0 30px 0 30px;font-size: 10px;">
        <div id="row">
            <div id="column-news-1">
                <h2><?= $jurnal->kode ?></h2>
            </div>
        </div>
        <div id="row" >
            <div id="column-news-1">
                <div id="row" >
                    <strong>Jurnal</strong>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= ": {$jurnal->jurnal_nama}" ?>
                </div>
                <div id="row">
                    <strong>Tanggal</strong>
                    &nbsp;&nbsp;&nbsp;&nbsp;<?= " : {$jurnal->tanggal_dibuat}" ?>
                </div>
                <?php if ($jurnal->origin !== "") { ?>
                    <div id="row" >
                        <strong>Periode</strong>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= " : {$jurnal->periode}" ?>
                    </div>
                <?php } ?>

            </div>


            <div id="column-news-2">
                <?php if ($jurnal->origin !== "") { ?>
                    <div id="row">
                        <strong>Origin</strong>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?= ": {$jurnal->origin}" ?>
                    </div>
                <?php } ?>
                <div id="row">
                    <strong>Reff Note</strong>
                    <span>&nbsp;&nbsp;<?= ": {$jurnal->reff_note}" ?></span>
                </div>
            </div>

        </div>
        <div id="row" >
            <table cellspacing="0" style="font-size: 12px; width: 100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th class="no" style="width: 20px;">#</th>
                        <th style="width: 150px;">Nama</th>
                        <th style="width: 150px;">Reff Note</th>
                        <th style="width: 150px;">Partner</th>
                        <th style="width: 120px;">Account</th>
                        <th style="width: 120px; text-align: right;">Debit</th>
                        <th style="width: 120px; text-align: right;">Credit</th>
                        <th style="width: 100px; text-align: right;">Kurs</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalDebit = 0;
                    $totalKredit = 0;
                    foreach ($detail as $key => $value) {
                        ?>
                        <tr>
                            <td>
                                <?= ($key + 1) ?>
                            </td>
                            <td>
                                <?= $value->nama ?>
                            </td>
                            <td>
                                <?= $value->reff_note ?>
                            </td>
                            <td>
                                <?= $value->supplier ?? '' ?>
                            </td>
                            <td><?= "{$value->kode_coa}" ?></td>
                            <?php
                            if (strtolower($value->posisi) === "d") {
                                $totalDebit += $value->nominal;
                                ?>
                                <td style="text-align: right;">
                                    <?= number_format($value->nominal, 2) ?>
                                </td>
                                <td style="text-align: right">
                                    0
                                </td>
                                <?php
                            } else {
                                $totalKredit += $value->nominal;
                                ?>
                                <td style="text-align: right">
                                    0
                                </td>
                                <td style="text-align: right">
                                    <?= number_format($value->nominal, 2) ?>
                                </td>
                                <?php
                            }
                            ?>
                            <td style="text-align: right">
                                <?= number_format($value->kurs, 2) ?>
                            </td>
                            <td>
                                <?= $value->kode_mua ?>
                            </td>
                        </tr>
                    <?php } ?>

                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" style="text-align: center;"><strong>Balance</strong></td>
                        <td style="text-align: right">
                            <strong><?= number_format($totalDebit, 2) ?></strong>
                        </td>
                        <td style="text-align: right;">
                         <strong>   <?= number_format($totalKredit, 2) ?></strong>
                        </td>
                    </tr>
                </tfoot>
            </table>

        </div>

    </body>
</html>