<div class="col-md-12">
    <ul class="nav nav-tabs " >
        <li class="active"><a href="#tab_1" data-toggle="tab">Picklist Item</a></li>
    </ul>
    <div class="tab-content over"><br>
        <div class="tab-pane active" id="tab_1">
            <div class="col-md-12 table-responsive over">
                <table class="table table-condesed table-hover rlstable  over" width="100%" id="item_picklist" >
                    <thead>                          
                        <tr>
                            <th class="style" width="10px">No</th>
                            <th class="style" >Barcode</th>
                            <th class="style">Corak Remark</th>
                            <th class="style">Warna Remark</th>
                            <th class="style" style="width:80px;" >Qty 1</th>
                            <th class="style" width="80px">Qty 2</th>
                            <th class="style" >Lokasi Fisik</th>
                            <th class="style" >Status</th>
                            <th class="style">Validasi Di</th>
                            <th class="style">#</th>
                        </tr>
                    </thead>
                    <tfoot id="tfooter">
                        <tr>
                        <tr style="display: none;">
                            <td colspan="8">
                                <a class="add-new-scan"><i class="fa fa-plus"></i> Tambah Data dengan Scan</a>
                            </td>
                            <td colspan="8">
                                <a  class="add-new-manual"><i class="fa fa-plus"></i> Tambah Data dengan Manual</a>
                            </td>
                        </tr>
                        </tr>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(function () {
        const table = $("#item_picklist").DataTable({
            "iDisplayLength": 25,
            "processing": true,
            "serverSide": true,
            "order": [],
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "ajax": {
                "url": "<?= base_url('warehouse/picklist/list_item') ?>",
                "type": "POST",
                "data": function (d) {
                    d.filter = "<?= $pl ?>";
                },
                "dataSrc": function (data) {
                    if (data.data.length < 1) {
                        $(".header-status").hide();
                    }
                    return data.data;
                }
            },
            "columnDefs": [
                {
                    "targets": [0, 7, 8, 9],
                    "orderable": false
                }
            ],
            "dom": 'Bfrtip',
            "buttons": [
                {
                    "text": '<i class="fa fa-plus"> <span>Tambah Data Scan</span>',
                    "className": "btn btn-default",
                    "action": function (e, dt, node, config) {
                        $(".add-new-scan").trigger("click");
                    }
                },
                {
                    "text": '<i class="fa fa-plus"> <span>Tambah Data Manual</span>',
                    "className": "btn btn-default",
                    "action": function (e, dt, node, config) {
                        $(".add-new-manual").trigger("click");
                    }
                }
            ],
            "fnDrawCallback": function () {
                $(".status_item").on('click', function () {
                    please_wait(function () {});
                    $.ajax({
                        "url": "<?= base_url('warehouse/picklist/delete_item') ?>",
                        "type": "POST",
                        "data": {
                            id: $(this).attr("data-id"),
                            pl: $(this).attr("data-pl")
                        },
                        "success": function (data) {
                            location.reload();
                        },
                        "error": function (xhr, ajaxOptions, thrownError) {
                            let data = JSON.parse(xhr.responseText);
                            unblockUI(function () {
                                setTimeout(function () {
                                    alert_notify(data.icon, data.message, data.type, function () {});
                                }, 1000);
                            });
                        }
                    });
                });
            }
        });
    });


    $(".add-new-manual").on('click', function (e) {
        e.preventDefault();
        $("#tambah_data").modal({
            show: true,
            backdrop: 'static'
        });
        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('Add Item Manual');
        $.post("<?= base_url('warehouse/picklist/item_manual/') ?>", {pl: "<?= $picklist->no ?>", ids: "<?= encrypt_url($picklist->id) ?>"}, function (data) {
            setTimeout(function () {
                $(".tambah_data").html(data.data);
                $("#btn-tambah").html("Tambahkan");
            }, 1000);
        });
    });

    $(".add-new-scan").on('click', function (e) {
        e.preventDefault();
        $("#tambah_data").modal({
            show: true,
            backdrop: 'static'
        });
//        $("#btn-tambah").hide();
        $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
        $('.modal-title').text('Add Item Scan');
        $.post("<?= base_url('warehouse/picklist/item_scan/') ?>", {pl: "<?= $picklist->no ?>", ids: "<?= encrypt_url($picklist->id) ?>"}, function (data) {
            setTimeout(function () {
                $(".tambah_data").html(data.data);
                $("#btn-tambah").html("Tambahkan");
            }, 1000);
        });
    });

    const addItem = function (data) {
        $.ajax({
            "type": "POST",
            "url": "<?= base_url('warehouse/picklist/add_item') ?>",
            beforeSend: function (e) {
                if (e && e.overrideMimeType) {
                    e.overrideMimeType("application/json;charset=UTF-8");
                }
            },
            "data": {
                "pl": "<?= $picklist->no ?? '' ?>",
                "ids": "<?= encrypt_url($picklist->id) ?>",
                "item": data
            }, "success": function (data) {
                location.reload();
            },
            "error": function (xhr, ajaxOptions, thrownError) {
                let data = JSON.parse(xhr.responseText);
                unblockUI(function () {
                    setTimeout(function () {
                        alert_notify(data.icon, data.message, data.type, function () {});
                    }, 1000);
                });
            }
        });
    }


</script>