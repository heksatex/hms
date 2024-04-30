<table class="table table-condesed table-hover rlstable  over" width="100%" id="delivery_global">
    <thead>                          
        <tr>

            <th class="style" width="10px">No</th>
            <th class="style">DO</th>
            <th class="style">No SJ</th>
            <th class="style">Tanggal</th>
            <th class="style">Type</th>
            <th class="style">No Picklist</th>
            <th class="style">Buyer</th>
            <th class="style">Alamat</th>
            <th class="style">Corak</th>
            <th class="style">Warna</th>
            <th class="style">Qty</th>
        </tr>
    </thead>
</table>

<script>
    $(function () {
        const tableGlobal = $("#delivery_global").DataTable({
            "iDisplayLength": 10,
            "processing": true,
            "serverSide": true,
            "order": [],
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "ajax": {
                "url": "<?= base_url('report/delivery/search') ?>",
                "type": "POST",
                "data": function (d) {
                    d.periode = $("#periode").val();
                }
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false
                }
            ]
        });
    })
</script>