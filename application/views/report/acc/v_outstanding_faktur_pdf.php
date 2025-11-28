

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url('dist/css/tableScroll.css') ?>">
        <style type="text/css">
            h3 {
                display: block !important;
                text-align: center !important;
            }

            table tbody tr td {
                padding: 0px 5px 0px 5px !important;
            }

            .style_space {
                white-space: nowrap !important;
                /* font-weight: 700; */
                background: #F0F0F0;
                border-top: 2px solid #ddd !important;
                border-bottom: 2px solid #ddd !important;
            }

            .ket-acc {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                max-width: 300px;
                /* Sesuaikan dengan kebutuhan */
            }

            .resizable .resizer:hover {
                background-color: rgba(0, 0, 0, 0.1);
            }

            .resizable {
                position: relative;
            }

            .resizable .resizer {
                position: absolute;
                top: 0;
                right: 0;
                width: 5px;
                cursor: col-resize;
                user-select: none;
                height: 100%;
            }

            table th,
            table td {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            #font-wei{
                font-weight: bold !important;
            }
        </style>
    </head>
    <body>
        <div class="col-sm-4">

        </div>
        <div class="col-sm-4">
            <div  style="text-align: center;font-size: 10px; line-height: 0.2;">

                <p id="font-wei" >PT. HEKSATEX INDAH</p>
                <p id="font-wei">OUTSTANDING FAKTUR</p>
                <br>
                <br>
                <p id="font-wei">PERTANGGAL <?=tgl_indo(date('d-m-Y'));?></p>
            </div>
        </div>
        <div class="col-sm-4">

        </div>
        <div class="col-sm-12">
            <table id="tblosfp" style="font-size: 12px; width: 100%;border: 1px solid black;">
                <thead>
                    <tr>
                        <th class="style bb no">No. </th>
                        <th class='style bb' style="min-width: 50px; width:105px;">No Faktur</th>
                        <th class='style bb' style="min-width: 105px; width:105px;">No SJ</th>
                        <th class='style bb' style="min-width: 105px; width:105px;">Tanggal</th>
                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Total Piutang (Rp)</th>
                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Puitang (Rp)</th>
                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Total Piutang (Valas)</th>
                        <th class='style bb text-right' style="min-width: 150px; width:100px;">Piutang (Valas)</th>
                        <th class='style bb text-right' style="min-width: 100px; width:100px;">Payment Term (Hari)</th>
                        <th class='style bb text-right' style="min-width: 100px; width:100px;">Umur (Hari)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data as $key => $value) {
                        ?>
                        <tr>
                            <td colspan="10" id="font-wei">Supplier : <?= $key ?></td>
                        </tr>
                        <?php
                        $no = 1;
                        $total_piutang_rp = 0;
                        $piutang_rp = 0;
                        $total_piutang_valas = 0;
                        $piutang_valas = 0;
                        foreach ($value as $keys => $values) {
                            $total_piutang_rp += $values->total_piutang_rp;
                            $piutang_rp += $values->piutang_rp;
                            $total_piutang_valas += $values->total_piutang_valas;
                            $piutang_valas += $values->piutang_valas;
                            ?>
                            <tr>
                                <td><?= $no ?></td>
                                <td><?= $values->no_faktur_internal ?></td>
                                <td><?= $values->no_sj ?></td>
                                <td><?= $values->tanggal ?></td>
                                <td class='text-right' ><?= number_format($values->total_piutang_rp, 2) ?></td>
                                <td class='text-right' ><?= number_format($values->piutang_rp, 2) ?></td>
                                <td class='text-right' ><?= number_format($values->total_piutang_valas, 2) ?></td>
                                <td class='text-right' ><?= number_format($values->piutang_valas, 2) ?></td>
                                <td class='text-right' ><?= $values->payment_term ?></td>
                                <td class='text-right' ><?= $values->hari ?></td>
                            </tr>
                            <?php
                            $no++;
                        }
                        ?>
                        <tr>
                            <td colspan="4" class="text-right style_space" id="font-wei" ><b>Total :</b></td>
                            <td class="text-right style_space" id="font-wei" ><b><?= number_format($total_piutang_rp, 2) ?></b></td>
                            <td class="text-right style_space" id="font-wei" ><b><?= number_format($piutang_rp, 2) ?></b></td>
                            <td class="text-right style_space"  id="font-wei"><b><?= number_format($total_piutang_valas, 2) ?></b></td>
                            <td class="text-right style_space" id="font-wei"><b><?= number_format($piutang_valas, 2) ?></b></td>
                            <td class="style_space" ><b></b></td>
                            <td class="style_space" ><b></b></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </body>
</html>
