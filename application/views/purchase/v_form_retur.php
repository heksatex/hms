
            <form class="form-horizontal" id="form-retur" name="form-retur" method="POST" action="<?php echo base_url('purchase/purchaseorder/retur') ?>">
                <table class="table table-condesed table-hover">
                    <thead>
                    <th>
                        No
                    </th>
                    <th>
                        Produk
                    </th>
                    <th style="text-align: right;">
                        Qty
                    </th>
                    <th>
                        Uom
                    </th>
                    </thead>
                    <tbody>
                        <?php
                        $no = 0;
                        foreach ($data as $key => $value) {
                            $no++;
                            ?>
                            <tr>
                                <td>
                                    <?= $no ?>
                                </td>
                                <td>
                                    <?= $value->kode_produk . " " . $value->nama_produk ?>
                                </td>
                                <td style="text-align: right;" class="pull-right">
                                    <input type="text" class="form-control" name="qty_beli[]" required>
                                    <input type="hidden" name="item[]" value="<?= $value->id ?>"
                                </td>
                                <td>
                                    <?= $value->uom_beli ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>
                                <input type="hidden" name="ids" value="<?= $id ?>">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
<script>
    $(function () {
        const formretur = document.forms.namedItem("form-retur");
        formretur.addEventListener(
                "submit",
                (event) => {
            please_wait(function () {});
            request("form-retur").then(
                    response => {
                        alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                        if (response.status === 200) {
                            location.reload();
                        }
                    }).catch(e => {

            }).finally(() => {
                unblockUI(function () {});
            });
            event.preventDefault();
        },
                false
                );
    })
</script>