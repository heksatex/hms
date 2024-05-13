<div class="col-md-12 table-responsive over">
    <table class="table table-condesed table-hover rlstable  over" width="100%" id="list_detail_item" >
        <thead>
            <tr>
                <th class="style" width="10px">No</th>
                <th class="style">BAL</th>
                <th class="style">Barcode</th>
                <th class="style">Corak Remark</th>
                <th class="style">Warna Remark</th>
                <th class="style" style="width:80px;" >Qty 1</th>
            </tr>
        </thead>
    </table>
</div>
<script>
    $(function () {
        const table = $("#list_detail_item").DataTable({
            "iDisplayLength": 25,
            "processing": true,
            "serverSide": true,
            "order": [],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": false,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?= base_url('warehouse/deliveryorder/list_detail_bulk') ?>",
                "type": "POST",
                "data": function (d) {
                    d.pl = "<?= $pl ?? "" ?>";
                }
            },
            "columnDefs": [
                {
                    "targets": [0, 5],
                    "orderable": false
                }
            ]

        });
    })
</script>