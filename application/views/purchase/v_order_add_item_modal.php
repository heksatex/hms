<style>
    .cancelPL{
        color: red;
    }
</style>
<div class="row">
    <div class="col-md-12 table-responsive over">
        <table id="rfq-add-item" class="table table-condesed table-hover rlstable over" width="100%">
            <thead>
                <tr>
                    <th class="no">#</th>
                    <th>Kode CFB</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>QTY</th>
                    <th>SC</th>
                    <th>Priority</th>
                    <th>Departement Tujuan</th>
                    <th>Create Date</th>
                    <th>Status</th>
                    <th>Note</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function () {
        const dTable = $('#rfq-add-item').DataTable({
            "iDisplayLength": 50,
            "processing": true,
            "serverSide": true,
            "order": [],
            "scrollX": true,
            "scrollY": "400",
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?php echo site_url('purchase/callforbids/list_data') ?>",
                "type": "POST",
                "data": function (d) {
                    d.depth = "";
                    d.depth_name = "";
                    d.kode = "";
                    d.prio = "";
                    d.status = "";
                },
                dataSrc: function (data) {
                    for (var i = 0, ien = data.data.length; i < ien; i++) {
                        const parser = new DOMParser();
                        const txt = parser.parseFromString(data.data[i][1], 'text/html');
//                        console.log(txt.body.textContent);
                        data.data[i][1] = txt.body.textContent;
                    }
                    return data.data;
                }
            },
            "columnDefs": [
                {
                    "targets": [0, 10],
                    "orderable": false
                },
                {
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                }
            ],
            "createdRow": function (row, data, dataIndex) {
                if (data[6].toLowerCase() === "urgent") {
                    $(row).addClass('cancelPL');
                }
            }
        });

        $("#btn-tambah").unbind("click").off("click").on('click', function (e) {
            $("#btn-tambah").button("loading");
            var rows_selected = dTable.column(0).checkboxes.selected();
            const data = new Promise((resolve, reject) => {
                let dt = {ids: [], data: []};
                $.each(rows_selected, function (index, rowId) {
                    var splt = rowId.split("|^");
                    dt.ids.push(splt[0]);
                    dt.data.push(rowId);
                });
                resolve(dt);
            });
            e.preventDefault();
            data.then((rsp) => {
                $.ajax({
                    url: "<?= base_url('purchase/requestforquotation/add_item_rfq/'.$id) ?>",
                    type: "POST",
                    data: {
                        data: JSON.stringify(rsp)
                    },
                    success: function (data) {
                        location.reload();
                    },
                    error: function (err) {
                        alert_notify("fa fa-warning", err.responseJSON.message, "danger", function () {});
                    }
                });
            }).catch(e => {
                alert_notify("fa fa-warning", e.message, "danger", function () {});
            });
        });
    });
</script>