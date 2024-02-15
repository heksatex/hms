<?php if ($section === "EDIT") { ?>
    <style>

        #btn-edit{
            display: none;
        }
        #return-item{
            display: none;
        }
    </style>
    <?php
} else {
    ?>
    <style>
        #btn-edit{
            display: none;
        }
        #btn-cancel{
            display: none;
        }
        #btn-print{
            display: none;
        }
    </style>
<?php }
?>

<div class="box-header with-border">

    <?php if ($section === "EDIT") { ?>
        <h3 class="box-title">Form Edit <strong> </strong></h3>
        <div class="pull-right text-right" id="btn-header">
            <?php if ($do->status === "cancel") { ?>
                                                            <!--<button class="btn btn-primary btn-sm" id="delivery-item" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Delivery</button>-->
                <button class="btn btn-danger btn-sm" id="return-item" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Retur Item</button>
            <?php } else {
                ?>
                <button class="btn btn-danger btn-sm" id="return-item" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Retur Item</button>
            <?php }
            ?>
        </div>
        <?php
    } else {
        ?>
        <div class="pull-right text-right" id="btn-header">
            <button class="btn btn-primary btn-sm" id="remove-item" data-loading-text="<i class='fa fa-spinner fa-spin '></i> processing...">Hapus Item</button>
        </div>
        <h3 class="box-title">Form Add  <strong> </strong></h3>
    <?php }
    ?>
</div>
<div class="box-body">
    <div class="col-md-6 col-xs-12">
        <div class="row">
            <div class="form-group">
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4">
                        <label class="form-label">No Picklist</label>
                    </div>
                    <div class="col-xs-8 col-md-8">
                        <span><?= $picklist->no ?></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4">
                        <label class="form-label">Sales</label>
                    </div>
                    <div class="col-xs-8 col-md-8">
                        <span><?= $picklist->sales ?></span>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-12 col-xs-12">
                    <div class="col-xs-4">
                        <label class="form-label">Tipe</label>
                    </div>
                    <div class="col-xs-8 col-md-8">
                        <span><?= $picklist->bulk ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="row">
                <div class="form-group">
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4">
                            <label class="form-label">Customer</label>
                        </div>
                        <div class="col-xs-8 col-md-8">
                            <span><?= $picklist->nama ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4">
                            <label class="form-label">Tipe Jual</label>
                        </div>
                        <div class="col-xs-8 col-md-8">
                            <span style="text-transform: uppercase"><?= $picklist->jenis_jual ?></span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4">
                            <label class="form-label">Alamat</label>
                        </div>
                        <div class="col-xs-8 col-md-8">
                            <span style="text-transform: uppercase"><?= $picklist->alamat ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php if ($section === "EDIT") { ?>
        <div class="row">
            <div class="col-md-6 col-xs-12">
                <div class="form-group">
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4">
                            <label class="form-label required">No Delivery Order</label>
                        </div>
                        <div class="col-xs-8 col-md-8">
                            <strong><?= $do->no ?></strong>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 col-xs-12">
                        <div class="col-xs-4">
                            <label class="form-label required">No Surat Jalan</label>
                        </div>
                        <div class="col-xs-8 col-md-8">
                            <strong><?= $do->no_sj ?></strong>
                        </div>
                    </div>
                </div>

            </div>

            <div class="col-md-6 col-xs-12">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4">
                                <label class="form-label required">Tanggal dibuat</label>
                            </div>
                            <div class="col-xs-8 col-md-8">
                                <strong><?= date("Y-m-d H:i:s", strtotime($do->tanggal_buat)) ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4">
                                <label class="form-label required">Tanggal Dokumen</label>
                            </div>
                            <div class="col-xs-8 col-md-8">
                                <input class="form-control" name="tanggal_dokumen" value="<?= date("D, d M Y H:i:s", strtotime($do->tanggal_dokumen)) ?>"
                                       id="tanggal_dokumen" required <?= (in_array($user->level, ["Entry Data", ""]) ? "readonly" : "") ?> >
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label class="form-label" >Note Picklist</label></div>
                            <div class="col-xs-8 col-md-8">
                                <textarea type="text" class="form-control input-sm resize-ta" rows="8" id="ket" name="ket"><?= $do->note ?></textarea>
                            </div>                                    
                        </div>
                    </div>
                </div>

            </div>


        </div>
    <?php } else if ($section === "ADD") { ?>
        <div class="row">
            <form class="form-horizontal" method="POST" name="form-do" id="form-do" action="<?= base_url('warehouse/deliveryorder/' . ($section === "ADD" ? "save" : "update")) ?>">
                <div class="col-md-6 col-xs-12">
                    <div class="form-group">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4">
                                <label class="form-label required">No SJ</label>
                            </div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control" name="no_sj_jenis" required>
                                    <?php
                                    if ($picklist->jenis_jual === 'lokal') {
                                        ?>
                                        <option value="SJ/HI/07">SJ/HI/07</option>
                                        <option value="SAMPLE/HI">SAMPLE/HI</option>
                                        <option value="SJM/HI/07">SJM/HI/07</option>
                                        <option value="MAKLOON/HI">MAKLOON/HI</option>
                                        <option value="SJ/HI/P/00">SJ/HI/P/00</option>
                                        <?php
                                    } else {
                                        ?>
                                        <option value="SJ/HI/03">SJ/HI/03</option>
                                        <?php
                                    }
                                    ?>

                                </select>
                                <input type="hidden" name="pl" id="picklist" value="<?= $picklist->no ?>">
                                <input type='hidden' name="bal" id="bal"/>
                                <input type="hidden" name="tipe" id="tipe" value="<?= $picklist->type_bulk_id ?>">
                                <input type="hidden" name="remove_item" id="remove_item">
                                <button type="submit" id="form-do-submit" style="display: none;"></button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4">
                                <label class="form-label required">Tanggal Dokumen</label>
                            </div>
                            <div class="col-xs-8 col-md-8">
                                <input class="form-control" name="tanggal_dokumen" id="tanggal_dokumen" required <?= (in_array($user->level, ["Entry Data", ""]) ? "readonly" : "") ?> >
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12">
                    <div class="form-group">
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4">
                                <label class="form-label">REV</label>
                            </div>
                            <div class="col-xs-8 col-md-8">
                                <select class="form-control" name="rev">
                                    <option selected></option>
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 col-xs-12">
                            <div class="col-xs-4"><label class="form-label" >Note Picklist</label></div>
                            <div class="col-xs-8 col-md-8">
                                <textarea type="text" class="form-control input-sm resize-ta" rows="8" id="ket" name="ket"><?= $picklist->keterangan ?></textarea>
                            </div>                                    
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php if ($picklist->type_bulk_id === "1") { ?>
            <div class="row">
                <form class="form-horizontal" method="POST" name="form-check-bal" id="form-check-bal" action="<?= base_url('warehouse/deliveryorder/check_bal') ?>">
                    <div class="col-md-6 col-xs-12">
                        <div class="form-group">
                            <div class="col-md-12 col-xs-12">
                                <div class="col-xs-4">
                                    <label class="form-label required">Barcode BAL</label>
                                </div>
                                <div class="col-xs-8">
                                    <input type='text' name="search" id="search" class="form-control input-lg" required autofocus/>
                                    <input type="hidden" name="type" value="bal">
                                    <input type="hidden" name="picklist" id="picklist" value="<?= $picklist->no ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <script>
                $(function () {
                    //                    const table = $("#delivery-item").DataTable();
                    $("#search").focus();
                    var valid = [];
                    const formcheckbal = document.forms.namedItem("form-check-bal");
                    formcheckbal.addEventListener(
                            "submit",
                            (event) => {
                        please_wait(function () {});
                        request("form-check-bal").then(
                                response => {
                                    unblockUI(function () {
                                        alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                    }, 100);
                                    if (response.status === 200) {
                                        valid.push($("#search").val());
                                        $("#search").val("");
                                        $("#bal").val(JSON.stringify(valid));
                                        table.search("").draw(false);
                                    }
                                }
                        ).catch(err => {
                            unblockUI(function () {});
                            alert_modal_warning("Hubungi Dept IT");
                        });
                        event.preventDefault();
                    },
                            false
                            );
                });
            </script>
        <?php } ?>

        <script>
            var listRemoveItem = [];
            $(function () {
                $("#tanggal_dokumen").datetimepicker({
                    format: 'YYYY-MM-DD HH:mm:ss',
                    date: new Date()
                            //                    maxDate: new Date()
                });
                $("#remove-item").on("click", function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $.post("<?= base_url('warehouse/deliveryorder/get_table_list_remove') ?>",
                            {
                                "pl": "<?= $picklist->no ?>"
                            }
                    , function (response) {
                        $('.modal-title').text('Daftar Barcode dikeluarkan');
                        $(".tambah_data").html(response);
                        $("#r_search").focus();
                        $("#btn-tambah").hide();
                        $('#tambah_data').on('hidden.bs.modal', function () {
                            table.search("").draw(false);
                        });
                    });


                });
                $("#btn-simpan").on("click", function () {
                    $("#form-do-submit").trigger('click');
                });
                const formdo = document.forms.namedItem("form-do");
                formdo.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    $("#remove_item").val(JSON.stringify(listRemoveItem));
                    request("form-do").then(
                            response => {
                                unblockUI(function () {
                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                }, 100);
                                if (response.status === 200)
                                    window.location.replace('<?php echo base_url('warehouse/deliveryorder/edit/') ?>' + response.data.data);
                            }
                    ).catch(err => {
                        unblockUI(function () {});
                        alert_modal_warning("Hubungi Dept IT");
                    }
                    );
                    event.preventDefault();
                },
                        false
                        );
            }
            )
        </script>
    <?php } ?>
</div>
