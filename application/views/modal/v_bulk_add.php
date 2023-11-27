<div class="row">
    <div class="col-md-6">
        <button class="btn btn-success btn-sm" id="add-bulk"><i class="fa fa-plus"></i> Tambah Bulk / BAL</button>
    </div>
</div>
<div class="row">
    <div class="col-md-12 table-responsive over">
        <table id="table_bulk" class="table table-condesed table-hover rlstable over" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>BAL ID</th>
                    <th>No Picklist</th>
                    <th>Tanggal Buat</th>
                    <th>User</th>
                    <th>#</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function () {
        const dTable = $('#table_bulk').DataTable({
            "searching": false,
            "paging": false
        });

        $("#add-bulk").on('click', function (e) {
            confirmRequest("Tambah Data", "Tambah Data Bulk / BAL ", () => {
                addBulk();
            });
        });

        const addBulk = function () {
            please_wait(function () {});
            $.ajax({
                "url": "<?= base_url('warehouse/bulk/save_add_bulk') ?>",
                "type": "POST",
                "data": {
                    pl: "<?= $pl ?>"
                },
                "success": function (data) {
                    dTable.search("").draw(false);
                    unblockUI(function () {
                        alert_notify(data.icon, data.message, data.type, function () {});
                    }, 100);
                },
                "error": function (xhr, ajaxOptions, thrownError) {
                    let data = JSON.parse(xhr.responseText);
                    unblockUI(function () {
                        alert_notify(data.icon, data.message, data.type, function () {});
                    }, 100);
                }
            });
        }
    });
</script>