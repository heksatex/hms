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
                        <h3 class="box-title">Form Tambah</h3>

                    </div>
                    <div class="box-body">
                        <form class="form-horizontal" name="form-supp" id="form-supp" method="POST" action="<?= base_url('purchase/partner/save') ?>">
                            <div class="col-xs-12 col-md-6">
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4">
                                            <label class="form-label required">Name</label>
                                        </div>
                                        <div class="col-xs-8 col-md-8">
                                            <div class="input-group">
                                                <input class="form-control" type="text" name="name" required>
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
                                                <input class="form-control" type="text" name="phone">
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
                                                <input class="form-control" type="text" name="mobile">
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
                                                <textarea type="text" class="form-control input-sm" name="invoice_street" rows="2" cols="27"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4">
                                            <label class="form-label">Invoice Country</label>
                                        </div>
                                        <div class="col-xs-8 col-sm-4 col-md-8 col-lg-4">
                                            <!-- <div class="input-group"> -->
                                                <select class="form-control invoice_country" style="width: 100%" name="invoice_country"></select>
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4">
                                            <label class="form-label">Invoice State</label>
                                        </div>
                                        <div class="col-xs-8 col-sm-4 col-md-8 col-lg-4">
                                            <!-- <div class="input-group"> -->
                                                <select class="form-control invoice_state" style="width: 100%" name="invoice_state"></select>
                                            <!-- </div> -->
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
                                                <input class="form-control" type="text" name="invoice_city">
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
                                                <input class="form-control" type="text" name="invoice_zip">
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
                                                <input class="form-control" name="contact_person">
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
                                                <input class="form-control" type="email" name="email">
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
                                                <input class="form-control" type="text" name="fax">
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
                                                <textarea type="text" class="form-control input-sm" name="delivery_street" rows="2" cols="27"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4">
                                            <label class="form-label">Delivery Country</label>
                                        </div>
                                        <div class="col-xs-8 col-sm-4 col-md-8 col-lg-4">
                                            <!-- <div class="input-group"> -->
                                            <select class="form-control delivery_country" style="width: 100%" name="delivery_country"></select>
                                            <!-- </div> -->
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4">
                                            <label class="form-label">Delivery State</label>
                                        </div>
                                        <div class="col-xs-8 col-sm-4 col-md-8 col-lg-4">
                                            <!-- <div class="input-group"> -->
                                                <select class="form-control delivery_state" style="width: 100%" name="delivery_state"></select>
                                            <!-- </div> -->
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
                                                <input class="form-control" type="text" name="delivery_city">
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
                                                <input class="form-control" type="text" name="delivery_zip">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4">
                                            <label class="form-label">Saldo Awal Utang</label>
                                        </div>
                                        <div class="col-xs-8 col-md-8">
                                            <div class="input-group">
                                                <input class="form-control text-right formatAngka" data-decimal="2" type="text" name="saldo_awal_utang">
                                            </div>
                                        </div>
                                    </div>
                                </div> -->
                            </div>

                    </div>
                    <input type="hidden" value="0" name="customer">
                    <input type="hidden" value="1" name="supplier">
                    <button type="submit" id="form-supp-submit" style="display: none"></button>
                    </form>
                </div>
        </div>
        </section>
    </div>
    </div>
    <?php $this->load->view("admin/_partials/js.php") ?>
    <script src="<?php echo site_url('dist/js/formatAdded.js') ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $(window).keydown(function(event) {
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
                data: function(params) {
                    return {
                        name: params.term
                    };
                },
                processResults: function(data) {
                    var results = [];
                    $.each(data, function(index, item) {
                        results.push({
                            id: item.id,
                            text: item.name
                        });
                    });
                    return {
                        results: results
                    };
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    //alert('Error data');
                    //alert(xhr.responseText);
                }
            }
        });

        $(".invoice_country").change(function() {
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
                    data: function(params) {
                        return {
                            id: $(".invoice_country").val(),
                            name: params.term
                        };
                    },
                    processResults: function(data) {
                        var results = [];
                        $.each(data, function(index, item) {
                            results.push({
                                id: item.id,
                                text: item.name
                            });
                        });
                        return {
                            results: results
                        };
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        //alert('Error data');
                        //alert(xhr.responseText);
                    }
                }
            });

        });

        $('.delivery_country').select2({
            allowClear: true,
            placeholder: "Select Country",
            ajax: {
                dataType: 'JSON',
                type: "POST",
                url: "<?php echo base_url(); ?>sales/partner/get_country_select2",
                //delay : 250,
                data: function(params) {
                    return {
                        name: params.term,
                    };
                },
                processResults: function(data) {
                    var results = [];
                    $.each(data, function(index, item) {
                        results.push({
                            id: item.id,
                            text: item.name
                        });
                    });
                    return {
                        results: results
                    };
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    //alert('Error data');
                    //alert(xhr.responseText);
                }
            }
        });

        $(".delivery_country").change(function() {
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
                    data: function(params) {
                        return {
                            id: $(".delivery_country").val(),
                            name: params.term
                        };
                    },
                    processResults: function(data) {
                        var results = [];
                        $.each(data, function(index, item) {
                            results.push({
                                id: item.id,
                                text: item.name
                            });
                        });
                        return {
                            results: results
                        };
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        //alert('Error data');
                        //alert(xhr.responseText);
                    }
                }
            });

        });

        $("#btn-simpan").off("click").unbind("click").on("click", function() {
            //                    confirmRequest("Purchase Order", "Update Purchase Order ? ", function () {
            $("#form-supp-submit").trigger("click");
            //                    });
        });

        const form = document.forms.namedItem("form-supp");
        form.addEventListener(
            "submit",
            (event) => {
                const saldoInput = document.querySelector("input[name='saldo_awal_utang']");
                if (saldoInput) {
                    saldoInput.value = unformatNumber(saldoInput.value);
                }
                please_wait(function() {});
                request("form-supp").then(
                    response => {
                        unblockUI(function() {
                            alert_notify(response.data.icon, response.data.message, response.data.type, function() {});
                        }, 100);
                        if (response.status === 200) {
                            window.location.replace("<?= base_url('purchase/partner/edit/') ?>" + response.data.id);
                        }
                    }).catch(err => {
                    unblockUI(function() {});
                    alert_modal_warning("Hubungi Dept IT");
                })
                event.preventDefault();
            },
            false
        );
    </script>

</body>

</html>