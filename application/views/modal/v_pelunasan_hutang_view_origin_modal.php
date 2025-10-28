<form class="form-horizontal" id="form_edit_invoice" name="form_edit_invoice">
    <div class="row">
        <div class="form-group">
            <div class="col-md-6">
                <div class="form-group">
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Kode</label></div>
                        <div class="col-xs-8">
                            <input type="text" class="form-control input-sm" name="kode" id="kode" value="<?php echo $header->kode; ?>" readonly="readonly" />
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Tanggal dibuat</label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm" name="tgl" id="tgl" value="<?php echo $header->tanggal; ?>" readonly="readonly" />
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Origin </label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm" name="origin" id="origin" value="<?php echo $header->origin; ?>" readonly="readonly" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Tanggal Kirim </label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm" name="tgl_transaksi" id="tgl_transaksi" value="<?php echo $header->tanggal_transaksi ?>" readonly="readonly" />
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>No SJ</label></div>
                        <div class="col-xs-8">
                            <input type='text' class="form-control input-sm" name="no_sj" id="no_sj" value="<?php echo $header->no_sj; ?>" readonly />
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Tanggal SJ</label></div>
                        <div class="col-xs-8">
                            <input type='text' class="form-control input-sm" name="tgl_sj" id="tgl_sj" value="<?php echo $header->tanggal_sj; ?>" readonly />
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Reff Note </label></div>
                        <div id="ta" class="col-xs-8">
                            <textarea class="form-control input-sm" name="note" id="note" readonly><?php echo $header->reff_note; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-12">
            <!-- Custom Tabs -->
            <div class="">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Products</a></li>
                </ul>
                <div class="tab-content"><br>
                    <div class="tab-pane active" id="tab_1">
                        <!-- Tabel  -->
                        <div class="col-md-12 table-responsive">
                            <table class="table table-condesed table-hover rlstable" width="100%" id="table_prod">
                                <label>Products</label>
                                <tr>
                                    <th class="style no">No.</th>
                                    <th class="style" style="width: 120px;">Kode Product</th>
                                    <th class="style">Product</th>
                                    <th class="style" style="text-align: right;">Qty</th>
                                    <th class="style">uom</th>
                                    <th class="style" style="text-align: right;">Tersedia</th>
                                    <th class="style">Kode PP</th>
                                    <th class="style" style="text-align: right;">Qty Beli</th>
                                    <th class="style">uom Beli</th>
                                    <th class="style" style="text-align: right;">Tersedia Qty Beli</th>
                                    <th class="style">Reff Note</th>
                                    <th class="style">Status</th>
                                </tr>
                                <tbody>
                                    <?php
                                    foreach ($items as $row) {

                                        if ($row->sum_qty > $row->qty)
                                            $color = "red";
                                        else if ($row->sum_qty < $row->qty)
                                            $color = 'blue';
                                        else
                                            $color = "black";
                                        if ($row->konversi_aktif == '1') {
                                            $qty_beli_tersedia = ($row->pembilang / $row->penyebut) * $row->sum_qty;
                                        } else {
                                            if ($row->nilai > 0) {
                                                $qty_beli_tersedia = $row->sum_qty / $row->nilai;
                                            } else {
                                                $qty_beli_tersedia = 0;
                                            }
                                        }
                                    ?>
                                        <tr class="num">
                                            <td></td>
                                            <td><?php echo $row->kode_produk; ?></td>
                                            <td><?php echo $row->nama_produk; ?></td>
                                            <td align="right"><?php echo number_format($row->qty, 2) ?></td>
                                            <td><?php echo $row->uom ?></td>
                                            <td align="right" style="color:<?php echo $color; ?>"><?php echo (!empty($row->sum_qty)) ? number_format($row->sum_qty, 2) : ''; ?></td>
                                            <td><?= $row->kode_pp ?></td>
                                            <td align="right"><?= number_format($row->qty_beli, 2) ?></td>
                                            <td><?= $row->uom_beli ?></td>
                                            <td align="right"><?= number_format($qty_beli_tersedia, 2) ?></td>
                                            <td>
                                                <?= nl2br($row->reff_note ?? "") ?>
                                            </td>
                                            <td><?php
                                                if ($row->status_barang == 'cancel') echo 'Batal';
                                                else echo $row->status_barang;
                                                ?>
                                            </td>
                                        </tr>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- Tabel  -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>