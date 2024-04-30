<div class="row">
    <div class="col-md-12 table-responsive over">
        <table id="info_stock_move" class="table table-condesed table-hover rlstable  over" width="100%">
            <thead>
                <tr>
                    <th class="no"></th>
                    <th class="style">Move ID</th>
                    <th class="style">Create Date</th>
                    <th class="style">Origin</th>
                    <th class="style">Method</th>
                    <th class="style">Dari Lokasi</th>
                    <th class="style">Tujuan Lokasi</th>
                    <th class="style">Status</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function () {
        const tbl_stk_moves = $("#info_stock_move").DataTable({
            "iDisplayLength": 50,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "processing": true,
            "serverSide": true,
            "order": [[2, 'desc']],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?= site_url('warehouse/helpers/history_stock_move_data_table') ?>",
                "type": "POST",
                "data": function (d) {
                    d.condition = '<?= $condition ?>';
                }
            },
        });
    });
</script>