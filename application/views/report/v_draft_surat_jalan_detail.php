<style>

    .text-kanan{
        text-align: right;
    }
    .head {
        display: none;
    }
</style>
<table class="table table-condesed table-hover rlstable  over" width="100%" id="draftsuratjalan">
    <thead>
        <tr class="head">
            <td>
                No SJ : 
            </td>
            <td style="font-weight: 800;">
                <?= $picklist->no_sj ?? "" ?>
            </td>
            <td>
                Customer : 
            </td>
            <td>
                <?= $picklist->nama ?? "" ?>
            </td>
        </tr>
        <tr class="head">
            <td>
                Pack : 
            </td>
            <td>
                <?= $picklist->no ?? "" ?>
            </td>
            <td>
                Alamat : 
            </td>
            <td>
                <?= $picklist->alamat ?? "" ?>
            </td>
        </tr>
        <tr class="head">
            <td>
                Tanggal : 
            </td>
            <td>
                <?= $picklist->tanggal_input ?? "" ?>
            </td>
            <td>
                Note : 
            </td>
            <td>
                <?= $picklist->keterangan ?? "" ?>
            </td>
        </tr>
        <tr class="head">
            
        </tr>
        <tr>
            <th>No Urut</th>
            <th>CTN No.</th>
            <th>Design No.</th>
            <th>Color</th>
            <th>F1</th>
            <th>F2</th>
            <th>F3</th>
            <th>F4</th>
            <th>F5</th>
            <th>F6</th>
            <th>F7</th>
            <th>F8</th>
            <th>F9</th>
            <th>F10</th>
            <th>Total PCS</th>
            <th>Total Qty</th>
            <th>UOM</th>
            <th>N.W[KGS]</th>
            <th>G.W[KGS]</th>
        </tr>

    </thead>
    <tbody>
        <?php
        $no = 0;
        $jml_qty = 0;
        $total_qty = 0;
        $id = null;
        $satuan = '';
        $nourut = 0;
        $total_net = 0;
        $total_groos = 0;
        $tempBulk = null;
        foreach ($picklist_detail as $key => $value) {
            $no++;
            $jml_qty += $value->jml_qty;
            $total_qty += $value->total_qty;
            $detailQty = $this->m_PicklistDetail->detailReportQty(['valid !=' => 'cancel', 'corak_remark' => $value->corak_remark, 'warna_remark' => $value->warna_remark, 'uom' => $value->uom, 'no_pl' => $value->no_pl]);
            $perpage = 10;
            $totalData = count($detailQty);
            $totalPage = ceil($totalData / $perpage);

            for ($nn = 0; $nn < $totalPage; $nn++) {
                $page = $nn * $perpage;
                $satuan = $detailQty[0]->uom;
                $tempID = $value->warna_remark . $value->corak_remark . $value->uom;
                $showNoUrut = "";
                $showNet = "";
                $showGross = "";
                if ($tempBulk !== $value->no_bulk) {
                    $nourut++;
                    $total_net += $value->net_weight;
                    $total_groos += $value->gross_weight;

                    $showGross = $value->gross_weight;
                    $showNet = $value->net_weight;
                    $showNoUrut = $nourut;
                }
                ?>
                <tr>
                    <td class="text-kanan"><?= ($picklist->type_bulk_id === "1") ? $showNoUrut : $no ?></td>
                    <td class="text-kanan"><?= ($tempBulk === $value->no_bulk) ? '' : $value->no_bulk ?></td>
                    <td style="text-align: left;"><?= ($id === $tempID) ? '' : str_replace('|', ' ', $value->corak_remark . ' ' . $value->lebar_jadi . ' ' . $value->uom_lebar_jadi) ?></td>
                    <td class="text-kanan"><?= ($id === $tempID) ? '' : str_replace('|', ' ', $value->warna_remark) ?></td>

                    <td class="text-kanan"><?= isset($detailQty[$page + 0]) ? (float) $detailQty[$page + 0]->qty : "" ?></td>
                    <td class="text-kanan"><?= isset($detailQty[$page + 1]) ? (float) $detailQty[$page + 1]->qty : "" ?></td>
                    <td class="text-kanan"><?= isset($detailQty[$page + 2]) ? (float) $detailQty[$page + 2]->qty : "" ?></td>
                    <td class="text-kanan"><?= isset($detailQty[$page + 3]) ? (float) $detailQty[$page + 3]->qty : "" ?></td>
                    <td class="text-kanan"><?= isset($detailQty[$page + 4]) ? (float) $detailQty[$page + 4]->qty : "" ?></td>
                    <td class="text-kanan"><?= isset($detailQty[$page + 5]) ? (float) $detailQty[$page + 5]->qty : "" ?></td>
                    <td class="text-kanan"><?= isset($detailQty[$page + 6]) ? (float) $detailQty[$page + 6]->qty : "" ?></td>
                    <td class="text-kanan"><?= isset($detailQty[$page + 7]) ? (float) $detailQty[$page + 7]->qty : "" ?></td>
                    <td class="text-kanan"><?= isset($detailQty[$page + 8]) ? (float) $detailQty[$page + 8]->qty : "" ?></td>
                    <td class="text-kanan"><?= isset($detailQty[$page + 9]) ? (float) $detailQty[$page + 9]->qty : "" ?></td>

                    <td class="text-kanan"><?= ($id === $tempID) ? '' : $value->jml_qty ?></td>
                    <td class="text-kanan"><?= ($id === $tempID) ? '' : $value->total_qty ?></td>
                    <td class="text-kanan"><?= ($id === $tempID) ? '' : $satuan ?></td>
                    <td class="text-kanan"><?= $showNet ?></td>
                    <td class="text-kanan"><?= $showGross ?></td>
                </tr>
                <?php
                $id = $tempID;
                $tempBulk = $value->no_bulk;
            }
        }
        ?>
        <tr></tr>
        <tr>
            <td></td>
            <td></td>
            <td>TOTAL : <?= $nourut ?> CARTONS</td>
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
            <td class="text-kanan"><?= $jml_qty ?></td>
            <td class="text-kanan"><?= $total_qty ?></td>
            <td></td>
            <td class="text-kanan"><?= $total_net ?></td>
            <td class="text-kanan"><?= $total_groos ?></td>
        </tr>
    </tbody>
</table>