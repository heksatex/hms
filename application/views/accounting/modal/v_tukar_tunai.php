<div class="row">
    <div class="col-md-12 table-responsive over">
        <table id="tbl-list-fpt" class="table table-condesed table-hover rlstable over" width="100%">
            <thead>
                <tr>
                    <th class="no">No</th>
                    <th>No GK</th>
                    <th>Supplier</th>
                    <th>Tanggal JT</th>
                    <th>Nominal</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function () {
        const table = $("#tbl-list-fpt").DataTable({
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
            "stateSave": false,
            "ajax": {
                "url": "<?php echo site_url('accounting/kasmasuk/list_tarik_tunai') ?>",
                "type": "POST",
                "data": function (d) {
                    d.giro= "<?= $giro ?>";
                }
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false
                },
                {
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                },
                {
                    'targets': 4,
                    "className": "text-right"
                }
            ]
        });

        $('input', table.cells().nodes()).prop('checked', false);

        $("#btn-tambah").unbind("click").off("click").on('click', function () {
            var rows_selected = table.column(0).checkboxes.selected();
            const data = new Promise((resolve, reject) => {
                let dt = [];
                $.each(rows_selected, function (index, rowId) {
                    dt.push(rowId);
                });
                resolve(dt);
            });
            data.then((rsp) => {
                addToTable(rsp);
            });
            $('#tambah_data').modal("hide");

        });
    });
</script>