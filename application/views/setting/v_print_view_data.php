<div class="row">
    <div class="col-md-12 table-responsive over">
        <table class="table table-condesed table-hover rlstable  over" width="100%">
            <thead>
            <th class="style" width="10px">No</th>
            <th class="style" width="20px">Printer Alias</th>
            <th class="style" width="20px">Printer Share</th>
            <th class="style" width="20px">Alamat</th>
            <th class="style" width="20px">#</th>
            </thead>
            <tbody>
                <?php
                foreach ($printer as $key => $value) {
                    ?>
                    <tr>
                        <td><?= ($key + 1) ?></td>
                        <td><?= $value->alias_printer ?></td>
                        <td><?= $value->nama_printer_share ?></td>
                        <td><?= $value->ip_share ?></td>
                        <td>
                            <?php
                            if (json_encode($value) === $priterDefault) {
                                ?>
                                <span class="lbl btn-success btn-xs">Default</span>
                                <?php
                            } else {
                                ?>
                                <button class="btn btn-default btn-xs set_default" data-printer='<?= json_encode($value) ?>'>Set Default</button>
                                <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(function () {
        $(".set_default").on("click", function () {
            var printer = $(this).data('printer');
            confirmRequest("Set Pinter", "Pilih Printer Sebagai Default ? ", function () {
                please_wait(function () {});
                $.ajax({
                    type: "POST",
                    url: "<?= base_url('setting/printershare/set') ?>",
                    data: {
                        data: JSON.stringify(printer)
                    },
                    success: function (data) {
                        $("#modal_printer").modal("hide");
                        unblockUI(function () {}, 500);
                    }, error: function (req, error) {
                        unblockUI(function () {
                            setTimeout(function () {
                                alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                            }, 500);
                        });
                    }
                });
            });
        });
    });
</script>