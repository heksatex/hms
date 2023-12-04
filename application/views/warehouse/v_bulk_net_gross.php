<div class="row">
    <div class="col-md-8 col-xs-12">
        <div class="table-responsive over">
            <form class="form-horizontal" method="POST" name="form-net-gross" id="form-net-gross" action="<?= base_url('warehouse/bulk/net_gross') ?>">

                <table class="table table-condesed table-hover rlstable  over" width="100%">
                    <thead>
                    <th>Bal ID</th>
                    <th>Total PCS</th>
                    <th>Total QTY</th>
                    <th>Net Weight</th>
                    <th>Gross Weight</th>
                    </thead>
                    <tbody>
                    <button type="submit" id="btn_form_net_gross" style="display: none"></button>
                    <?php foreach ($data as $key => $value) {
                        ?>
                        <tr>
                            <td><?= $value->no_bulk ?></td>
                            <td><?= $value->jumlah_qty ?></td>
                            <td><?= $value->total_qty ?></td>
                            <td> <input type='text' name="net[<?= $value->no_bulk ?>][]" class="form-control input-sm" value="<?= $value->net_weight ?>"/></td>
                            <td> <input type='text' name="gross[<?= $value->no_bulk ?>][]" class="form-control input-sm" value="<?= $value->gross_weight ?>"/></td>
                        </tr>

                    <?php } ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>
<script>
    $(function () {
        const formnetgross = document.forms.namedItem("form-net-gross");
        formnetgross.addEventListener(
                "submit",
                async(event) => {
            please_wait(function () {});
            try {
                request("form-net-gross").then(
                        response => {
                            unblockUI(function () {
                                alert_notify(response.data.icon, '<span class="notify">' + response.data.message + '<strong>', response.data.type, function () {});
                            }, 50);
//                            if (response.status === 200) {
//                                
//                            }
                        });
            } catch (er) {

            } finally {
                unblockUI(function () {}, 50);
            }
            event.preventDefault();
        },
                false
                );
    });


</script>