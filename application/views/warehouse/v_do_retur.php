
<div class="row">
    <div class="col-md-6">
        <div class="row">
            <form class="form-horizontal" method="POST" name="form-retur-scan" id="form-retur-scan" action="<?= base_url('warehouse/deliveryorder/check_item') ?>">
                <?php
                if ($status === 'done') {
                    ?>
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4">Scan</div>
                        <div class="col-xs-8">
                            <input class="form-control input-sm" placeholder="Scan" name="search" id="searchdata" required>
                            <input type="hidden" name="do" value="<?= $do ?>">
                            <label class="text-sm text-info">Tekan F2 Untuk Kembali ke Scan Barcode</label>
                            <button type="submit" id="form_submit" style="display: none;"></button>
                        </div>
                    </div>
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><button type="button" class="btn btn-sm btn-primary btn-print-retur"><i class="fa fa-print"></i> &nbsp; Print</button></div>
                    </div>
                <?php } ?>
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
        <table id="retur-item-scan" class="table table-condesed table-hover rlstable  over" width="100%">
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
        $("#btn-tambah").show();
        $("#searchdata").focus();
        const tableReturn = $("#retur-item-scan").DataTable({
            "lengthChange": false,
            "fnRowCallback": function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                if (aData[8] !== "") {
                    $("td", nRow).css('background-color', '#87f542');
                }
            }
        });


        var no = 1;
        const addRow = function (datas) {
            tableReturn.row.add(
                    [
                        no,
                        datas["barcode_id"],
                        datas["kode_produk"],
                        datas["nama_produk"],
                        datas["corak_remark"],
                        datas["warna_remark"],
                        changeCondition(datas["qty"] + " " + datas["uom"], "null", "0", true),
                        changeCondition(datas["qty2"] + " " + datas["uom2"], "null", "0", true),
                        JSON.stringify([datas["no_pl"], datas["barcode_id"], datas["quant_id"]])
                    ]
                    ).draw(false);
            no++;
        };

        const formreturscan = document.forms.namedItem("form-retur-scan");


        async function checkTable(event) {
            let data = false;
            event.preventDefault();
            await searchArray(tableReturn.rows().data(), 1, $("#searchdata").val()).then(
                    resp => {
                        if (resp.length > 0) {
                            data = true;
                        }
                    }
            );
            return data;
        }

        formreturscan.addEventListener(
                "submit",
                async (event) => {
            please_wait(function () {});

            try {
                let status = await checkTable(event);
                if (!status) {
                    request("form-retur-scan").then(
                            response => {
                                if (response.status === 200) {
                                    var data = response.data.data;
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
        const getDataRetur = new Promise((resolve, reject) => {
            $.ajax({
                "type": "POST",
                "url": "<?= base_url('warehouse/deliveryorder/data_retur') ?>",
                beforeSend: function (e) {
                    if (e && e.overrideMimeType) {
                        e.overrideMimeType("application/json;charset=UTF-8");
                    }
                },
                "data": {
                    "do": "<?= $do ?>",
                    "doid": "<?= $doid ?>"
                },
                "success": function (data) {
                    resolve(data);
                },
                "error": function (xhr, ajaxOptions, thrownError) {
                    resolve([]);
                }
            });
        });

        getDataRetur.then((rsp) => {
            if (rsp.length > 0) {
                no = rsp.length + 1;
            }
            tableReturn.rows.add(rsp).draw(false);
        });
        $("#btn-tambah").off('click').on('click', function () {
            const data = new Promise((resolve, reject) => {
                let dt = [];
                var datas = tableReturn.rows().data();
                $.each(datas, function (idx, val) {
                    if (val[8] !== "") {
                        dt.push(val[8]);
                    }

                });
                resolve(dt);
            });
            data.then((rsp) => {
                if (rsp.length < 1) {
                    alert_notify("fa fa-danger", "Belum ada data untuk diretur", "danger", function () {});
                    return;
                }
                please_wait(function () {});
                $.ajax({
                    "type": "POST",
                    "url": "<?= base_url('warehouse/deliveryorder/retur_item') ?>",
                    beforeSend: function (e) {
                        if (e && e.overrideMimeType) {
                            e.overrideMimeType("application/json;charset=UTF-8");
                        }
                    },
                    "data": {
                        "do": "<?= $do ?>",
                        "data": JSON.stringify(rsp),
                        "no_sj": "<?= $no_sj ?>",
                        "doid": "<?= $doid ?>"
                    },
                    "success": function (data) {
                        unblockUI(function () {
                            tableReturn.clear().draw();
                            alert_notify(data.icon, data.message, data.type, function () {});
                        }, 100);
                    },
                    "error": function (xhr, ajaxOptions, thrownError) {
                        let data = JSON.parse(xhr.responseText);
                        unblockUI(function () {
                            alert_notify(data.icon, data.message, data.type, function () {});
                        }, 50);
                    }
                });
            }).catch(er => {
                alert_notify("fa fa-danger", er.message, "danger", function () {});
            });
        });

        $(".btn-print-retur").on("click", function () {
            $.ajax({
                type: "POST",
                url: "<?= base_url('warehouse/deliveryorder/print_retur') ?>",
                beforeSend: function (e) {
                    please_wait(function () {});
                },
                data: {
                    "do": "<?= $do ?>",
                    "doid": "<?= $doid ?>"
                },
                success: function (data) {
                    unblockUI(function () {});
                    window.open(data.url, "_blank").focus();

                },
                error: function (req, error) {
                    unblockUI(function () {
                        setTimeout(function () {
                            alert_notify('fa fa-close', req?.responseJSON?.message, 'danger', function () {});
                        }, 500);
                    });
                }
            });
        });

    });
</script>