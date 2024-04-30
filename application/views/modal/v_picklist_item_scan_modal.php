<style>
    #picklist-item-scan_filter {
        display: none;
    }
</style>
<div class="row">
    <div class="col-md-6">
        <div class="row">
            <form class="form-horizontal" method="POST" name="form-picklist-scan" id="form-picklist-scan" action="<?= base_url('warehouse/picklist/add_list_item_scan') ?>">
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4">Scan</div>
                    <div class="col-xs-8">
                        <input class="form-control input-sm" placeholder="Scan" name="search" id="searchdata" required autocomplete="off">
                        <label class="text-sm text-info">Tekan F2 Untuk Kembali ke Scan Barcode</label>
                        <button type="submit" id="form_submit" style="display: none;"></button>
                    </div>
                </div>
            </form>

        </div>
        <div class="col-md-12 col-xs-12">

        </div>
    </div>
    <div class="col-md-6">

    </div>
</div>
<div class="row">
    <div class="col-md-12 table-responsive over">
        <table id="picklist-item-scan" class="table table-condesed table-hover rlstable  over" width="100%">
            <thead>
                <tr>
                    <th class="no"></th>
                    <th class="style">Barcode</th>
                    <th class="style">Kode Produk</th>
                    <th class="style">Nama Produk</th>
                    <th class="style">Corak Remark</th>
                    <th class="style">Warna Remark</th>
                    <th class="style">Qty Jual</th>
                    <th class="style">Qty 2 Jual</th>
                    <th class="style">Lokasi Fisik</th>
                    <th class="style">Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script>
    $(document).keydown(function (e) {
        if (e.which === 113) {
            $("#searchdata").focus();
        }
    });
    $(function () {
        $("#searchdata").focus();
        $("#filter").select2({
            allowClear: true,
            placeholder: 'Filter'
        });
        const dTable = $('#picklist-item-scan').DataTable({
            "lengthChange": false,
            columnDefs: [
                {
                    data: null,
                    defaultContent: '<button type="button" class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>',
                    targets: -1
                }
            ]
        });
        dTable.on('click', 'button', function () {
            var row = $(this).parents('tr');
            dTable.row(row).remove().draw();

        });
//        $("#searchdata").keypress(function (e) {
////            if (e.which === 13) {
////                $("#form_submit").trigger("click");
//////                dTable.search($(this).val()).draw();
////            }
//
//        });
        var no = 1;
        const addRow = function (datas) {
            datas["status"] = "realisasi";
            dTable.row.add(
                    [
                        no,
                        datas["barcode"],
                        datas["kode_produk"],
                        datas["nama_produk"],
                        datas["corak_remark"],
                        datas["warna_remark"],
                        changeCondition(datas["qty_jual"] + " " + datas["uom_jual"], "null", "0", true),
                        changeCondition(datas["qty2_jual"] + " " + datas["uom2_jual"], "null", "0", true),
                        datas["lokasi_fisik"],
                        datas["quant_id"] //JSON.stringify(datas)
                    ]).draw(false);
            no++;
        };

        const formpicklistscan = document.forms.namedItem("form-picklist-scan");
//        KP/1023/JAC/651/001

        async function checkTable(event) {
            let data = false;
            event.preventDefault();
            await searchArray(dTable.rows().data(), 1, $("#searchdata").val()).then(
                    resp => {
                        if (resp.length > 0) {
                            data = true;
                        }
                    }
            );
            return data;
        }

        formpicklistscan.addEventListener(
                "submit",
                async (event) => {
            please_wait(function () {});

            try {
                let status = await checkTable(event);
                if (!status) {
                    request("form-picklist-scan").then(
                            response => {
                                if (response.status === 200) {
                                    var data = response.data.data[0];
                                    addRow(data);
                                }
                                unblockUI(function () {
                                }, 50);
                                alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                            }
                    ).catch(err => {
                        unblockUI(function () {});
                        alert_modal_warning("Hubungi Dept IT");
                    });
                } else {
                    alert_notify('fa fa-check', 'Item sudah ada dalam list', 'warning', function () {});
                    unblockUI(function () {
                    }, 50);
                }
            } catch (e) {

            } finally {
                $("#searchdata").val("");
                $("#searchdata").focus();
            }
            event.preventDefault();
        },
                false
                );

        $("#btn-tambah").off('click').on('click', function () {
            const data = new Promise((resolve, reject) => {
                let dt = [];
                $.each(dTable.rows().data(), function (idx, val) {
                    dt.push(val[9]);
                });
                resolve(dt);
            });

            data.then(rsp => {
                addItem(JSON.stringify(rsp), "", dTable, "realisasi");
            });

        });

    });
</script>