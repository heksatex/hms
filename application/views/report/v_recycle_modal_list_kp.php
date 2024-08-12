<div class="row">
    <div class="col-md-12 table-responsive over">
        <table id="list-kp" class="table table-condesed table-hover rlstable over" width="100%">
            <thead>
            <th> # </th>
            <td> KP / LOT</td>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function () {
        const dTable = $('#list-kp').DataTable({
            "iDisplayLength": 50,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "processing": true,
            "serverSide": true,
            "order": [[1, 'asc']],
            "stateSave": false,
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?= site_url('report/recycle/data_list_kp') ?>",
                "type": "POST",
                "data": function (d) {
                    d.mo = "<?= $mo ?>";
                }
            },
            columnDefs: [
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
            ],
            select: {
                'style': 'multi'
            }
            
        });
        $("#btn-tambah").unbind("click").off("click").on('click', function () {
            
            var rows_selected = dTable.column(0).checkboxes.selected();
            console.log(rows_selected);
            const data = new Promise((resolve, reject) => {
                let dt = [];
                $.each(rows_selected, function (index, rowId) {
                    let text = rowId.split("/");
                    if (text.length > 0) {
                        dt.push(rowId);
                    }
                });
                resolve(dt);
            });
            data.then((rsp) => {
                kp = rsp;
            }).catch(e => {
            }).finally(() => {
                $("#tambah_data").modal('toggle');
            });
        });
    })
</script>