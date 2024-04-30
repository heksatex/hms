<div class="row">
    <div class="col-md-8 col-xs-12">
        <div class="table-responsive over">
            <form class="form-horizontal" method="POST" name="form-net-gross" id="form-net-gross" action="<?= base_url('warehouse/bulk/net_gross') ?>">
                <input type="hidden" name="pl" value="<?= $pl ?>">
                <table class="table table-condesed table-hover rlstable  over" width="100%">
                    <thead>
                    <th>Bal ID</th>
                    <th>Total PCS</th>
                    <th>Total QTY</th>
                    <th>Net Weight</th>
                    <th>Gross Weight</th>
                    <th><input type="checkbox" class="rumus"> Terapkan Rumus</th>
                    </thead>
                    <tbody>
                    <button type="submit" id="btn_form_net_gross" style="display: none"></button>
                    <?php foreach ($data as $key => $value) {
                        ?>
                        <tr>
                            <td><?= $value->no_bulk ?></td>
                            <td><?= $value->jumlah_qty ?></td>
                            <td><?= $value->total_qty ?></td>
                            <td> <input type='text' oninput="isNumb(event,this)" name="net[<?= $value->no_bulk ?>][]" data-id="<?= $value->no_bulk ?>" class="form-control input-sm nw net-<?= $value->no_bulk ?>" value="<?= $value->net_weight ?>"/></td>
                            <td> <input type='text' oninput="isNumb(event,this)" name="gross[<?= $value->no_bulk ?>][]" data-id="<?= $value->no_bulk ?>" class="form-control input-sm gw weight-<?= $value->no_bulk ?>" value="<?= $value->gross_weight ?>"/></td>
                            <!--<td><input type="checkbox" class="crumus-<?= $value->no_bulk ?>" data-id="<?= $value->no_bulk ?>" > </td>-->
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
        $(".rumus").on("change", function () {
            var rumus = $(this);
            let bulk = rumus.attr("data-id");
            let checked = rumus.prop('checked');
            if (checked) {
                $(".nw").prop('readonly', true);
                return;
            }
            $(".nw").prop('readonly', false);
        });


        $(".gw").on("input", function () {
            let bulk = $(this).attr("data-id");
            let checked = $(".rumus").prop('checked');
            if (checked) {
                let gw = $(this).val();
                let net = parseFloat(gw) - 4;
                if (isNaN(net) || net < 0) {
                    $(".net-" + bulk).val(0);
                    return;
                }
                $(".net-" + bulk).val(net.toFixed(2));
            }
        });

        var $inputs = $(".gw");
        $inputs.keyup(function (event) {

            if (event.key === "Enter") {
                var index = $inputs.index(this);
                $inputs.eq(index + 1).focus();
            }
            event.preventDefault();
        });

        $(".gw").on("keypress", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
            }
        });
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
    })
            ;


</script>