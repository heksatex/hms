
<!DOCTYPE html>
<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
        <div class="wrapper">
            <header class="main-header">
                <?php $this->load->view("admin/_partials/main-menu.php") ?>
                <?php
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header">
                    <?php $this->load->view("admin/_partials/statusbar.php") ?>
                </section>

                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Edit Supplier</h3>

                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" name="form-supp" id="form-supp" method="POST" action="<?= base_url("purchase/partner/save/{$ids}") ?>">
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Name</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">

                                                    <input class="form-control" type="text" name="name" value="<?= $partner->nama ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Phone</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" type="text" name="phone" value="<?= $partner->phone ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Mobile</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" type="text" name="mobile" value="<?= $partner->mobile ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Invoice Street</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <textarea type="text" class="form-control input-sm"name="invoice_street" rows="2" cols="27" ><?= $partner->invoice_street ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Invoice Country</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <select class="form-control invoice_country" style="width: 100%" name="invoice_country"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Invoice State</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <select class="form-control invoice_state" style="width: 100%" name="invoice_state"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">invoice City</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" type="text"  name="invoice_city" value="<?= $partner->invoice_city ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">invoice Zip</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" type="text"  name="invoice_zip" value="<?= $partner->invoice_zip ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 col-md-6">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Contact Person</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" name="contact_person" value="<?= $partner->contact_person ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Email</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" type="email" name="email" value="<?= $partner->email ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Fax</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" type="text" name="fax" value="<?= $partner->fax ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Delivery Street</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <textarea type="text" class="form-control input-sm" name="delivery_street" rows="2" cols="27"> <?= $partner->delivery_street ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Delivery Country</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <select class="form-control delivery_country" style="width: 100%" name="delivery_country"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Delivery State</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <select class="form-control delivery_state" style="width: 100%" name="delivery_state"></select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Delivery City</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" type="text"  name="delivery_city" value="<?= $partner->delivery_city ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Delivery Zip</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <div class="input-group">
                                                    <input class="form-control" type="text"  name="delivery_zip" value="<?= $partner->delivery_zip ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                        </div>
                        <input type="hidden" value="<?= $partner->customer ?>" name="customer">
                        <input type="hidden" value="<?= $partner->supplier ?>" name="supplier">
                        <button type="submit" id="form-supp-submit" style="display: none"></button>
                        </form>
                    </div>
            </div>
        </section>
    </div>
    <footer class="main-footer">
        <?php $this->load->view("admin/_partials/modal.php") ?>
        <div id="foot">
            <?php
            $data['kode'] = $partner->id;
            $data['mms'] = $mms->kode;
            $this->load->view("admin/_partials/footer.php", $data)
            ?>
        </div>
    </footer>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $(window).keydown(function (event) {
            if (event.keyCode === 13) {
                event.preventDefault();
                return false;
            }
        });

    });

    $('.invoice_country').select2({
        allowClear: true,
        placeholder: "Select Country",
        ajax: {
            dataType: 'JSON',
            type: "POST",
            url: "<?php echo base_url(); ?>sales/partner/get_country_select2",
            //delay : 250,
            data: function (params) {
                return{
                    name: params.term
                };
            },
            processResults: function (data) {
                var results = [];
                $.each(data, function (index, item) {
                    results.push({
                        id: item.id,
                        text: item.name
                    });
                });
                return {
                    results: results
                };
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //alert('Error data');
                //alert(xhr.responseText);
            }
        }
    });

    $(".invoice_country").change(function () {
        $(".invoice_state").html('');
        //select 2 invoice state
        $('.invoice_state').select2({
            allowClear: true,
            placeholder: "Select State",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo base_url(); ?>sales/partner/get_states_select2",
                //delay : 250,
                data: function (params) {
                    return{
                        id: $(".invoice_country").val(),
                        name: params.term
                    };
                },
                processResults: function (data) {
                    var results = [];
                    $.each(data, function (index, item) {
                        results.push({
                            id: item.id,
                            text: item.name
                        });
                    });
                    return {
                        results: results
                    };
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    //alert('Error data');
                    //alert(xhr.responseText);
                }
            }
        });

    });


    var id_country = '<?php echo $inv_id_country ?>'; // id country
    var name_country = '<?php echo $inv_nm_country ?>'; // nama country

    //untuk event selected select2 invoice country
    var $newOption = $("<option></option>").val(id_country).text(name_country);
    $(".invoice_country").empty().append($newOption).trigger('change');

    var id_state = '<?php echo $inv_id_state ?>'; // id state
    var name_state = '<?php echo $inv_nm_state ?>'; // nama state

    //untuk event selected select2 invoice country
    var $newOption = $("<option></option>").val(id_state).text(name_state);
    $(".invoice_state").empty().append($newOption).trigger('change');



    $('.delivery_country').select2({
        allowClear: true,
        placeholder: "Select Country",
        ajax: {
            dataType: 'JSON',
            type: "POST",
            url: "<?php echo base_url(); ?>sales/partner/get_country_select2",
            //delay : 250,
            data: function (params) {
                return{
                    name: params.term,
                };
            },
            processResults: function (data) {
                var results = [];
                $.each(data, function (index, item) {
                    results.push({
                        id: item.id,
                        text: item.name
                    });
                });
                return {
                    results: results
                };
            },
            error: function (xhr, ajaxOptions, thrownError) {
                //alert('Error data');
                //alert(xhr.responseText);
            }
        }
    });

    $(".delivery_country").change(function () {
        $(".delivery_state").html('');
        //select 2 delivery state
        $('.delivery_state').select2({
            allowClear: true,
            placeholder: "Select State",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo base_url(); ?>sales/partner/get_states_select2",
                //delay : 250,
                data: function (params) {
                    return{
                        id: $(".delivery_country").val(),
                        name: params.term
                    };
                },
                processResults: function (data) {
                    var results = [];
                    $.each(data, function (index, item) {
                        results.push({
                            id: item.id,
                            text: item.name
                        });
                    });
                    return {
                        results: results
                    };
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    //alert('Error data');
                    //alert(xhr.responseText);
                }
            }
        });

    });


    var id_country = '<?php echo $dv_id_country ?>'; // id country
    var name_country = '<?php echo $dv_nm_country ?>'; // nama country

    //untuk event selected select2 delivery country
    var $newOption = $("<option></option>").val(id_country).text(name_country);
    $(".delivery_country").empty().append($newOption).trigger('change');

    var id_state = '<?php echo $dv_id_state ?>'; // id state
    var name_state = '<?php echo $dv_nm_state ?>'; // nama state

    //untuk event selected select2 delivery country
    var $newOption = $("<option></option>").val(id_state).text(name_state);
    $(".delivery_state").empty().append($newOption).trigger('change');



    $("#btn-simpan").off("click").unbind("click").on("click", function () {
//                    confirmRequest("Purchase Order", "Update Purchase Order ? ", function () {
        $("#form-supp-submit").trigger("click");
//                    });
    });

    const form = document.forms.namedItem("form-supp");
    form.addEventListener(
            "submit",
            (event) => {
        please_wait(function () {});
        request("form-supp").then(
                response => {
                    unblockUI(function () {
                        alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                    }, 100);
                    if (response.status === 200) {
                        location.reload();
                    }
                }).catch(err => {
            unblockUI(function () {});
            alert_modal_warning("Hubungi Dept IT");
        })
        event.preventDefault();
    },
            false
            );


</script>

</body>
</html>