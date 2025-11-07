<div class="row">
    <div class="col-md-12 table-responsive over">
        <table id="tbl-list-sj" class="table table-condesed table-hover rlstable over" width="100%">
            <thead>
                <tr>
                    <th class="style no">No</th>
                    <th class="style">Surat Jalan</th>
                    <th class="style">Picklist</th>
                    <th class="style">Buyer</th>
                    <th class="style">Marketing</th>
                    <th class="style">#</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function () {
        const table = $("#tbl-list-sj").DataTable({
            "iDisplayLength": 50,
            "processing": true,
            "serverSide": true,
            "order": [],
            "scrollX": true,
            "scrollY": "calc(85vh - 250px)",
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "select": {
                style: 'single'
            },
            "ajax": {
                "url": "<?php echo site_url('sales/returpenjualan/list_sj') ?>",
                "type": "POST",
                "data": function (d) {
                    d.tipe = '<?= $tipe ?>';
                }
            },

            "columnDefs": [
                {
                    "targets": [0, 5],
                    "orderable": false
                }
            ],
                "fnDrawCallback":function(){
                    $(".pilih-sj").on("click",function(){
                        addTotable($(this).data("sj"));
                        $('#tambah_data').modal("hide");
                    });
                }
        });
    });
</script>