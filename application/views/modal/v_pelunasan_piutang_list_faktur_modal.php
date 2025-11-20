<div class="row">
    <div class="col-md-12 table-responsive over">
        <table id="tbl-list-faktur" class="table table-condesed table-hover rlstable over" width="100%">
            <thead>
                <tr>
                    <th class="no"></th>
                    <th class="no">No</th>
                    <th>No Faktur</th>
                    <th>No SJ</th>
                    <th>Tanggal</th>
                    <th>Curr</th>
                    <th>Kurs</th>
                    <th>Total Piutang (Rp)</th>
                    <th>Total Piutang (Valas)</th>
                    <th>Sisa Piutang (Rp)</th>
                    <th>Sisa Piutang (Valas)</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function() {
        const table = $("#tbl-list-faktur").DataTable({
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
                "url": "<?php echo site_url('accounting/pelunasanpiutang/list_data_faktur') ?>",
                "type": "POST",
                "data": function(d) {
                    d.no_pelunasan = "<?= $no_pelunasan; ?>";
                    d.partner = "<?= $partner; ?>";
                }
            },
            "columnDefs": [{
                    "targets": [0,1],
                    "orderable": false
                },
                {
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                },
                {
                    'targets': [5, 6, 7, 8, 9,10],
                    "className": "text-right"
                }
            ]
        });

        $('input', table.cells().nodes()).prop('checked', false);

        $("#btn-tambah").unbind("click").off("click").on('click', function() {
            var rows_selected = table.column(0).checkboxes.selected();
            const data = new Promise((resolve, reject) => {
                let dt = [];
                $.each(rows_selected, function(index, rowId) {
                    dt.push(rowId);
                });
                resolve(dt);
            });
            data.then((rsp) => {
                addToTable(rsp);
            });
            $('#tambah_data').modal("hide");

        });


        $("#btn-tambah").unbind("click").off("click").on('click', function(e) {
            var rows_selected = table.column(0).checkboxes.selected();
            var rows_selected_arr = new Array();
            var message = 'Silahkan pilih data terlebih dahulu !';
           
            $.each(rows_selected, function(index, rowId) {
                rows_selected_arr.push(rowId);
            });

            countchek = rows_selected_arr.length;

            if (rows_selected_arr == '') {
                alert_modal_warning(message);
            } else {
                $('#btn-tambah').button('loading');
                $.ajax({
                    type: "POST",
                    url: '<?php echo base_url('accounting/pelunasanpiutang/save_detail_faktur') ?>',
                    dataType: 'JSON',
                    data: {
                        arr_data: rows_selected_arr,
                        no_pelunasan: "<?= $no_pelunasan; ?>",
                    },
                    success: function(data) {
                        if (data.status == 'failed') {
                            alert_notify(data.icon, data.message, data.type, function() {});
                            $('#btn-tambah').button('reset');
                        } else {
                            // $("#tab_1").load(location.href + " #tab_1");
                            $("#status_bar").load(location.href + " #status_bar");
                            // $("#foot").load(location.href + " #foot");
                            $('#tambah_data').modal('hide');
                            $('#btn-tambah').button('reset');
                            if (data.msg2 == 'Yes') {
                                alert_modal_warning(data.message2);
                            }
                            alert_notify(data.icon, data.message, data.type, function() {});
                            // loadLog(); 
                        }

                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        $('#btn-tambah').button('reset');
                        if (xhr.status == 401) {
                            var err = JSON.parse(xhr.responseText);
                            alert(err.message);
                        } else {
                            alert("Error Simpan Data!")
                        }
                    }
                });
            }

            return false;
        });
    });
</script>