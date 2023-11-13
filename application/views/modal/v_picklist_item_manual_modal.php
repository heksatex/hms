<style>
    .dataTables_filter {
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
                    <input class="form-control input-sm" placeholder="Search" id="search">
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
        <table id="picklist-item" class="table table-condesed table-hover rlstable  over" width="100%">
            <thead>
                <tr>
                    <th class="no"></th>
                    <th>Barcode</th>
                    <th>Kode Produk</th>
                    <th>Nama Produk</th>
                    <th>Corak Warna</th>
                    <th>Warna Remark</th>
                    <th>Qty Jual</th>
                    <th>Qty Jual 2</th>
                    <th>Lokasi Fisik</th>
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
        const dTable = $('#picklist-item').DataTable({
            "iDisplayLength": 50,
            "processing": true,
            "serverSide": true,
            "order": [],

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
                }
            ],
            'select': {
                'style': 'multi'
            }
        });
        $("#marketing").on('change', function () {
            dTable.search($("#search").val()).draw();
        });
        $("#filter").on('change', function () {
            dTable.search($("#search").val()).draw();
        });
        $("#search").keyup(function () {
            dTable.search($(this).val()).draw();
        });
        $('input', dTable.cells().nodes()).prop('checked', false);
        $("#btn-tambah").off("click").on('click', function () {
            var rows_selected = dTable.column(0).checkboxes.selected();
            const data = new Promise((resolve, reject) => {
                let dt = [];
                $.each(rows_selected, function (index, rowId) {
                    var datas = dTable.rows([rowId - 1]).data();
                    $.each(datas, function (idx, val) {
                        dt.push(val[9]);
                    });
                });
                resolve(dt);
            });
            data.then(rsp => {
                addItem(JSON.stringify(rsp));
            });

//            $('#tambah_data').modal('hide');
        });
    });
</script>