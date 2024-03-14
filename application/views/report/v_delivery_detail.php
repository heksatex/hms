<table class="table table-condesed table-hover rlstable  over" width="100%" id="delivery_detail">
    <thead>                          
        <tr>

            <th class="style" width="10px">No</th>
            <th class="style">Deskripsi</th>
            <th class="style">Warna</th>
            <th class="style">Total QTY</th>
            <th class="style">Total LOT / PCS</th>
            <th class="style">Satuan</th>
        </tr>
    </thead>
</table>

<script>
    $(function () {
        const tableGlobal = $("#delivery_detail").DataTable({
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