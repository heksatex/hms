<div class="col-md-12 table-responsive over">
    <table class="table table-condesed table-hover rlstable list_detail_item over" width="100%" id="list_detail_item" >
        <thead>
            <tr>
                <th class="style" width="10px">No</th>
                <?php if ((int) $type === 1) { ?>
                    <th class="style">BAL ID</th>
                <?php } ?>
                <th class="style">Barcode</th>
                <th class="style">Corak Remark</th>
                <th class="style">Warna Remark</th>
                <th class="style">Lebar Kain Jadi</th>
                <th class="style" style="width:80px;" >Qty 1</th>
                <!--<th class="style">#</th>-->
            </tr>
        </thead>
    </table>
</div>
<script>
    $(function () {
        $("#btn-tambah").hide();
        const table = $(".list_detail_item").DataTable({
            "iDisplayLength": 25,
            "processing": true,
            "serverSide": true,
            "order": [],
            "paging": true,
            "lengthChange": false,
            "searching": false,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?= base_url('warehouse/deliveryorder/list_detail') ?>",
                "type": "POST",
                "data": function (d) {
                    d.filter = "<?= $id ?? "" ?>";
                    d.pl = "<?= $pl ?? "" ?>";
                    d.doid = "<?= $doid ?? "" ?>";
                    d.form = "<?= $form ?? "" ?>";
                    d.bulk = '<?= $bulk ?? "" ?>';
                    d.not_in = '<?= $not_in ?? "" ?>';
                    d.tipe = '<?= $type ?? "" ?>';
                }
            },
            "columnDefs": [
                {
                    "targets": [0, 5],
                    "orderable": false
                }
            ],
            "fnDrawCallback": function () {
                $(".status_item").on('click', function () {
                    const e = this;
                    confirmRequest("Hapus Item", "Keluarkan Item Dari PL ? ", () => {
                        delete_item(e, table);
                    });
                });
            }

        });

        const delete_item = function (e, table = null) {
            please_wait(function () {});

            $.ajax({
                "url": "<?= base_url('warehouse/deliveryorder/delete_item_pl') ?>",
                "type": "POST",
                "data": {
                    id: $(e).attr("data-id"),
                    pl: $(e).attr("data-pl")
                },
                "success": function (data) {
                    table.search("").draw(false);
                    unblockUI(function () {
                        alert_notify(data.icon, data.message, data.type, function () {});
                    }, 50);
                },
                "error": function (xhr, ajaxOptions, thrownError) {
                    let data = JSON.parse(xhr.responseText);
                    unblockUI(function () {
                        alert_notify(data.icon, data.message, data.type, function () {});
                    }, 50);
                }
            });
        };
    });

</script>