<style>
    .over thead th {
        position: sticky;
        top: 0;
    }
</style>

<div class="col-md-6 col-xs-12">
    <div class="table-responsive over" id="tablesdata_">
        <table class="table table-condesed table-hover rlstable  over" width="100%">
            <thead>
                <tr>
                    <th class="style" width="10px">No</th>
                    <th class="style"width="20px">BAL ID</th>
                    <th class="style" width="20px">Description</th>
                    <th class="style" width="10px">PCS</th>
                    <th class="style" width="10px">QTY</th>

                </tr>
            </thead>
            <tbody>
                <?php
                $no_bulk = array();
                $total_pcs = 0;
                $total_qty = 0;
                foreach ($data as $key => $value) {
                    $no_bulk[$value->no_bulk] = 1;
                    $total_pcs += $value->jumlah_qty;
                    $total_qty += $value->total_qty;
                    ?>
                    <tr>
                        <td>
                            <?= ($key + 1) ?>
                        </td>
                        <td class="bolded list">
                            <?= ($value->no_bulk) ?>
                        </td>
                        <td class="bolded list">
                            <?= $value->corak_remark . " - " . $value->warna_remark ?>
                        </td>
                        <td class="bolded list">
                            <?= ($value->jumlah_qty) ?>
                        </td>
                        <td class="bolded list">
                            <?= ($value->total_qty) ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<div class="col-md-6 col-xs-12" style="background-color: #f4f4f4">
    <div class="row">
        <div class="col-md-12">
            <div class="row status_bal">
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-label">BULK</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-label">PCS Item</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-label">QTY Item</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="col-md-12 col-xs-12" style="background-color: yellow;">
                            <label class="bolded" id="bal_aktif" ><?= $bulk ?></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12 col-xs-12">
                            <label class="bolded" id="bal_aktif_qty" style="background-color: yellow;"><?= (empty($bulk)) ? 0 : $total_pcs ?></label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12 col-xs-12">
                            <label class="bolded" id="bal_aktif_qty" style="background-color: yellow;"><?= (empty($bulk)) ? 0 : $total_qty ?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" style="padding-top: 50px;">
        <div class="col-md-12">
            <div class="row count">
                <div class="form-group">
                    <div class="col-md-4 col-xs-12">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-label">Total Bulk</label>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-label">Total PCS</label>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="col-md-12 col-xs-12">
                            <label class="form-label">Total QTY</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-4 col-xs-12">
                        <div class="col-md-12 col-xs-12">
                            <label class="bolded"><?= $totalan->total_bulk ?? 0 ?></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="col-md-12 col-xs-12">
                            <label class="bolded"><?= $totalan->jumlah_qty ?? 0 ?></label>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="col-md-12 col-xs-12">
                            <label class="bolded"><?= $totalan->total_qty ?? 0 ?></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>