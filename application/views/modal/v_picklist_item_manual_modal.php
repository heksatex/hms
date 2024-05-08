<style>
    #picklist-item-manual_filter {
        display: none;
    }
    .fr{
        padding-bottom: 1%;
    }
</style>
<div class="row">
    <div class="form-group">
        <div class="col-md-6">
            <div class="row">
                <form id="form-search">

                    <div class="col-md-12 col-xs-12 fr">
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
                    <div class="col-md-12 col-xs-12 fr">

                        <div class="col-xs-4">Filter</div>
                        <div class="col-xs-8">
                            <div class="row">
                                <div class="field-form-filter" id="field-form-filter">

                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12 fr">
                        <div class="col-xs-4"></div>
                        <div class="col-xs-8">
                            <div class="row">
                                <div class="col-md-6 col-xs-6">
                                    <button class="dt-button btn btn-default add-filter" type="button">
                                        <span>
                                            <i class="fa fa-plus"> <span>Filter</span>
                                            </i>

                                        </span>
                                    </button>
                                </div>
                                <div class="col-md-6 col-xs-6">
                                    <button type="button" class="btn btn-success search-data">
                                        <i class="fa fa-search"> <span>Search</span> </i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--                <div class="col-md-12 col-xs-12 fr">
                                        <div class="col-xs-4">Search</div>
                                        <div class="col-xs-8">
                                            <input class="form-control input-sm" placeholder="Search" id="searchdata">
                                        </div>
                                    </div>-->
                </form>
            </div>
            <div class="col-md-12 col-xs-12">

            </div>
        </div>
        <div class="col-md-6">

        </div>
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
                    <th>Corak Remark</th>
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

<template class="tmplt-form-filter">
    <div class="form-group-filter">
        <div class="col-md-5 col-xs-5">
            <select class="form-control input-sm filter" name="filter[new___LA_KEY__][filter]">
                <option value="kode_produk">Kode Produk</option>
                <option value="nama_produk">Nama Produk</option>
                <option value="corak_remark">Corak Remark</option>
                <option value="warna_remark">Warna Remark</option>
                <option value="lokasi_fisik">Lokasi Fisik</option>
            </select>
        </div>
        <div class="col-md-5 col-xs-5">
            <input class="form-control input-sm filter-search" placeholder="Search" name="filter[new___LA_KEY__][search]">
            <!--<input type="hidden" name="filter[new___LA_KEY__][remove]" value="0" class="filter-remove">-->
        </div>
        <div class="col-md-2 col-xs-2">
            <button class="btn btn-danger remove-filter" type="button">
                <i class="fa fa-minus"></i>
            </button>
        </div>
    </div>

</template>
<script>
    $(function () {
        var index = 0;
        $(".search-data").on("click", function () {
            fform = [];
            var form = document.getElementById("form-search");
            let frm = new FormData(form);
            frm.forEach(function (val, key) {
                let oob = {};
                oob[key] = val;
                fform.push(oob);
            });
            dTable.search("").draw();
        });
        var fform = [];
        $(".add-filter").on("click", function () {
            var tmpl = $("template.tmplt-form-filter");
            index++;
            var template = tmpl.html().replace(/__LA_KEY__/g, index);
            $('.field-form-filter').append(template);
            return false;
        });
        $("#field-form-filter").on('click', '.remove-filter', function () {
            var first_input_filter = $(this).closest('.form-group-filter').find('input[name]:first').attr('name');
            if (first_input_filter.match('filter\\[new_')) {
                $(this).closest('.form-group-filter').remove();
            } else {
                $(this).closest('.form-group-filter').hide();
//                $(this).closest('.has-many-databentuk-form').find('.filter-remove').val(1);
            }
            return false;
        });
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
//                    d.filter = $("#filter").find(":selected").val();
                    d.marketing = $("#marketing").val();
                    d.form = fform;
                }
            },
            "columnDefs": [
                {
                    "targets": [11],
                    "orderable": false
                },
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
//        $("#marketing").on('change', function () {
//            dTable.search($("#searchdata").val()).draw();
//        });
//        $("#filter").on('change', function () {
//            dTable.search($("#searchdata").val()).draw();
//        });
//        $("#searchdata").keyup(function () {
//            dTable.search($(this).val()).draw();
//        });
        $('input', dTable.cells().nodes()).prop('checked', false);
        $("#btn-tambah").unbind("click").off("click").on('click', function () {
            $("#btn-tambah").button("loading");
            var rows_selected = dTable.column(0).checkboxes.selected();
            const data = new Promise((resolve, reject) => {
                let dt = [];
                let pl = null;
                let bcd = "";
                $.each(rows_selected, function (index, rowId) {
                    let text = rowId.split("-");
                    if (text.length > 0) {
                        if (text[2] !== '')
                            throw new Error("Barcode " + text[1] + " duplicate pada PickList " + text[2]);
                        dt.push(text[0]);
                    }
                });
                resolve(dt);
            });
            $("#btn-tambah").button("reset");
            $("#btn-tambah").show();
            data.then((rsp) => {
                addItem(JSON.stringify(rsp), "", dTable, 'draft');
            }).catch(e => {
                addItem(JSON.stringify([]), e.message);
            }).finally(() => {
                $("#btn-tambah").button("reset");
            });
        });

        $("#picklist-item-manual").unbind("click").off("click").on('click', 'button', function () {

            var row = $(this).closest('tr');
            var data = dTable.row(row).data();
            let text = data[0].split("-");
            addItem((JSON.stringify([text[0]])), "", dTable, "draft");
        });
    });
</script>