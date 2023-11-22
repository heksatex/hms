<style>
    #picklist-item-manual_filter {
        display: none;
    }
</style>
<div class="row">
    <div class="col-md-6">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4">Marketing</div>
                <div class="col-xs-8">
                    <select class="form-control input-sm" id="marketing" name="marketing[]" multiple>
                        <option value="">Marketing</option>
                        <?php
                        foreach ($sales as $key => $value) {
                            echo '<option value="' . $value->kode . '">' . $value->nama . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="col-md-12 col-xs-12">

                <div class="col-xs-4">Filter</div>
                <div class="col-xs-8">
                    <select class="form-control input-sm" id="filter">
                        <option value="">Filter</option>
                        <option value="kode_produk">Kode Produk</option>
                        <option value="nama_produk">Nama Produk</option>
                        <option value="corak_remark">Corak Warna</option>
                        <option value="warna_remark">Warna</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12 col-xs-12">
                <div class="col-xs-4">Search</div>
                <div class="col-xs-8">
                    <input class="form-control input-sm" placeholder="Search" id="searchdata">
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xs-12">

        </div>
    </div>
    <div class="col-md-6">

    </div>
</div>
<div class="row">
    <div class="col-md-12 table-responsive over">
        <table id="picklist-item-manual" class="table table-condesed table-hover rlstable over" width="100%">
            <thead>
                <tr>
                    <th></th>
                    <th>Barcode</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Corak Warna</th>
                    <th>Warna Remark</th>
                    <th>Qty Jual</th>
                    <th>Qty Jual 2</th>
                    <th>Lokasi Fisik</th>
                    <th></th>
                    <th></th>
                    <th>#</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(function () {
        $("#filter").select2({
            allowClear: true,
            placeholder: 'Filter'
        });
        $("#marketing").select2({
            allowClear: true,
            placeholder: 'Marketing'
        });
        const dTable = $('#picklist-item-manual').DataTable({
            "iDisplayLength": 50,
            "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
            "processing": true,
            "serverSide": true,
            "order": [[8, 'desc']],
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?= site_url('warehouse/picklist/add_list_item') ?>",
                "type": "POST",
                "data": function (d) {
                    d.filter = $("#filter").find(":selected").val();
                    d.marketing = $("#marketing").val();
                }
            },
            "columnDefs": [
                {
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    }
                },
                {
                    "targets": 9,
                    "visible": false,
                    "searchable": false
                },
                {
                    'targets': 10,
                    "visible": false,
                    "searchable": false
                }
            ],
            'select': {
                'style': 'multi'
            }
        });
        $("#marketing").on('change', function () {
            dTable.search($("#searchdata").val()).draw();
        });
        $("#filter").on('change', function () {
            dTable.search($("#searchdata").val()).draw();
        });
        $("#searchdata").keyup(function () {
            dTable.search($(this).val()).draw();
        });
        $('input', dTable.cells().nodes()).prop('checked', false);
        $("#btn-tambah").off("click").on('click', function () {
            var rows_selected = dTable.column(0).checkboxes.selected();
            const data = new Promise((resolve, reject) => {
                let dt = [];
                let pl = null;
                let bcd = "";
                $.each(rows_selected, function (index, rowId) {
                    var datas = dTable.rows([rowId - 1]).data();
                    $.each(datas, function (idx, val) {
                        if (val[10] !== null) {
                            pl = val[10];
                            bcd = val[1];
                            return false;
                        }


                        dt.push(val[9]);
                    });
                    if (pl !== null)
                        throw new Error("Barcode " + bcd + " duplicate pada PickList " + pl);
                });
                resolve(dt);
            });
            data.then((rsp) => {
                addItem(JSON.stringify(rsp), "", dTable);
            }).catch(e => {
                addItem(JSON.stringify([]), e.message);
            });
//            $('#tambah_data').modal('hide');
        });
//        $("#picklist-item").on('change', 'input[type="checkbox"]', function () {
//            var row = $(this).closest('tr');
//            var data = dTable.row(row).data();
//        });
//
//        $('input[type="checkbox"]').on('change', function () {
//            console.log("masuk");
//        });

        $("#picklist-item-manual").on('click', 'button', function () {

            var row = $(this).closest('tr');
            var data = dTable.row(row).data();
            addItem((JSON.stringify([data[9]])), "", dTable);
        });
    });
</script>