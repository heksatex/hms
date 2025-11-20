<form class="form-horizontal" id="form_view_do" name="form_view_do">
    <div class="row">
        <div class="form-group">
            <div class="col-md-6">
                <div class="form-group">

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>No Picklist</label></div>
                        <div class="col-xs-8">
                            <input type="text" class="form-control input-sm"
                                value="<?= $do->no_picklist ?? '-' ?>" readonly />
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Marketing</label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm"
                                value="<?= $picklist->sales ?? '-' ?>" readonly />
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Tipe Bulk</label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm"
                                value="<?= $picklist->bulk ?? '-' ?>" readonly />
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>No Delivery Order</label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm"
                                value="<?= $do->no ?? '-' ?>" readonly />
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>No Surat Jalan</label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm"
                                value="<?= $do->no_sj ?? '-' ?>" readonly />
                        </div>
                    </div>

                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Customer</label></div>
                        <div class="col-xs-8">
                            <input type="text" class="form-control input-sm"
                                value="<?= $picklist->nama ?? '-' ?>" readonly />
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Jenis Jual</label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm"
                                value="<?= $picklist->jenis_jual ?? '-' ?>" readonly />
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Tanggal Sistem</label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm"
                                value="<?= isset($do->tanggal_buat) ? date("Y-m-d H:i:s", strtotime($do->tanggal_buat)) : '-' ?>" readonly />
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Tanggal Dokumen</label></div>
                        <div class="col-xs-8 col-md-8">
                            <input type='text' class="form-control input-sm"
                                value="<?= isset($do->tanggal_dokumen) ? date("Y-m-d H:i:s", strtotime($do->tanggal_dokumen)) : '-' ?>" readonly />
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4"><label>Note Picklist</label></div>
                        <div class="col-xs-8 col-md-8">
                            <textarea class="form-control" readonly><?= $do->note ?? '-' ?></textarea>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    <!-- TAB Items -->
    <div class="row">
        <div class="col-md-12">

            <ul class="nav nav-tabs">
                <li class="active"><a href="#tab_1" data-toggle="tab">Items</a></li>
            </ul>

            <div class="tab-content"><br>
                <div class="tab-pane active" id="tab_1">
                    <div class="col-md-12 table-responsive">
                        <table class="table table-condesed table-hover rlstable" id="list-items">
                            <thead>

                                <tr>
                                    <th class="style" width="10px">No</th>
                                    <?php if ((int) $picklist->type_bulk_id === 1) { ?>
                                        <th class="style" width="35px">Bulk</th>
                                    <?php } ?>
                                    <th class="style">Deskripsi</th>
                                    <th class="style">Warna</th>
                                    <th class="style">Total LOT / PCS</th>
                                    <th class="style">Total QTY</th>
                                    <th class="style">Satuan</th>
                                </tr>
                            </thead>
                        </table>

                    </div>

                </div>
            </div>

        </div>
    </div>

</form>


<script>
    $(function() {
        const table = $("#list-items").DataTable({
            "iDisplayLength": 10,
            "processing": true,
            "serverSide": true,
            "order": [],
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "ajax": {
                "url": "<?= base_url('warehouse/deliveryorder/get_list_data') ?>",
                "type": "POST",
                "data": function(d) {
                    d.pl = "<?= $picklist->no ?>";
                    d.id = "<?= encrypt_url($do->no) ?>";
                    d.bulk = "<?= $picklist->type_bulk_id ?>";
                }
            },
            "columnDefs": [{
                "targets": [0],
                "orderable": false
            }],
            "error": function(xhr) {
                console.error("Error dari server:", xhr.responseText);
            }
        });
    });
</script>