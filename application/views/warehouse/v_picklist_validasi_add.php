<html>
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            #btn-cancel {
                display: none;
            }
            .notification {
                background: #f44336;
                color: white;
                font-family: 'PT Sans';
                font-size: 18px;
                padding: 8px;
                text-align: center;
                width: 100%;
            }
            .notify{
                font-weight: 400;
                font-size: 200%;
            }
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse">
        <div class="wrapper">
            <header class="main-header">
                <?php
                $this->load->view("admin/_partials/main-menu.php");
                if (!isset($access->status) || !$access->status) {
                    echo '<div class="notification"> User atau PC tidak diijinkan melakukan validasi <i class="fa fa-close" aria-hidden="true"></i></div>';
                }
                ?>

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
                    <?php // $this->load->view("admin/_partials/statusbar.php")  ?>
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Validasi Picklist <strong><?= str_replace("_", " ", $access->permission ?? "") ?></strong></h3>
                        </div>
                        <div class="box-body">
                            <?php if (isset($access->status) && $access->status) { ?>
                                <div class="row">
                                    <div class="col-md-6 col-xs-12">
                                        <form class="form-horizontal" method="POST" name="form-validasi" id="form-validasi" action="<?= base_url('warehouse/picklistvalidasi/update') ?>">
                                            <button type="submit" id="btn_form_validasi" style="display: none"></button>
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-4">
                                                        <label class="form-label required">Scan Barcode / No PL</label>
                                                    </div>
                                                    <div class="col-xs-8 col-md-8">
                                                        <input type='text' name="search" id="search" class="form-control input-lg scan-text" required autocomplete="off"/>
                                                        <label class="text-sm text-info">Tekan F2 Untuk Kembali ke Scan</label>
                                                        <input type='hidden' name="pl" id="pl" value=""/>
                                                        <input type="hidden" name="access" value="<?= $access->permission ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                    </div>
                                    <div class="col-md-6 col-xs-12" id="checkFocus">

                                    </div>
                                </div>

                                <div class="col-md-6 col-xs-12">

                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">No Picklist</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="no_pl"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Tanggal Picklist</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="tgl_picklist"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Marketing</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="sales"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Customer</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="cust"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Jenis Jual</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="jj"></span>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-6 col-xs-12">
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-8">
                                                        <label class="form-label">Total LOT</label>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <span id="totalLot"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-8">
                                                        <label class="form-label">Belum Valid</label>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <span id="invalid"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-8">
                                                        <label class="form-label">Scan Valid</label>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <span id="scanValid"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-12 col-xs-12">
                                                    <div class="col-xs-8">
                                                        <label class="form-label">Scan Invalid</label>
                                                    </div>
                                                    <div class="col-xs-4">
                                                        <span id="scanInvalid"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--                                            <div class="form-group">
                                                                                            <div class="col-md-12 col-xs-12">
                                                                                                <select class="form-control" aria-label="Default select example">
                                                                                                    <option value="all" selected>Filter Status Barcode</option>
                                                                                                    <option value="1">One</option>
                                                                                                    <option value="2">Two</option>
                                                                                                    <option value="3">Three</option>
                                                                                                </select>
                                                                                            </div>
                                                                                        </div>-->
                                        </div>
                                        <div class="col-md-6 col-xs-12 table-responsive over">
                                            <div class="table-responsive over" style="max-height:200px;"  id="show_error">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs " >
                                    <li class="active"><a href="#tab_1" data-toggle="tab">Picklist Item Validasi</a></li>
                                </ul>
                                <div class="tab-content over"><br>
                                    <div class="tab-pane active" id="tab_1">
                                        <div class="col-md-12 table-responsive over">
                                            <table class="table table-condesed table-hover rlstable  over" width="100%" id="item_realisai" >
                                                <thead>                          
                                                    <tr>
                                                        <th class="style" width="10px">No</th>
                                                        <th class="style">Barcode</th>
                                                        <th class="style">Corak Remark</th>
                                                        <th class="style">Warna Remark</th>
                                                        <th class="style" style="width:80px;" >Qty 1</th>
                                                        <th class="style" width="80px">Qty 2</th>
                                                        <th class="style" >Lokasi Fisik</th>
                                                        <th class="style" >Status</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view("admin/_partials/js.php") ?>
        </div>
        <script>
            function check(barcode) {
                $.ajax({
                    url: "<?= base_url('warehouse/picklistvalidasi/check_error') ?>",
                    type: "POST",
                    data: {
                        barcode: barcode,
                        pl: $("#pl").val()
                    },
                    beforeSend: function (xhr) {
                        please_wait(function () {});
                    },
                    success: function (data) {
                        $("#show_err_" + barcode).html(data.message);
                    },
                    error: function (err) {

                    },
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 50);
                    }
                });
            }

            $(document).keydown(function (e) {
                if (e.which === 113) {
                    $("#search").focus();
                }

            });
            const getSearch = document.getElementById("search");
            getSearch.addEventListener("focus", (event) => {
                $("#checkFocus").css({"background-color": "green", 'font-weight': "400", "font-size": "150%", "text-align": "center", "color": "white"});
                $("#checkFocus").html("Scan Barcode Sudah Siap");
            });
            getSearch.addEventListener("blur", (event) => {
                $("#checkFocus").css({"background-color": "red", 'font-weight': "400", "font-size": "150%", "text-align": "center", "color": "white"});
                $("#checkFocus").html("Scan Barcode Tidak Siap");
            }
            );
            $(function () {
                var error_barcode = [];

                const loadError = function (barcode) {
                    $.post("<?= base_url('warehouse/picklistvalidasi/show_error/') ?>",
                            {
                                "barcode": JSON.stringify(barcode)
                            }
                    , function (response) {
                        var divp = document.getElementById('show_error');
                        divp.innerHTML = response.data;
                    });
                }
                $("#search").focus();
                $("#btn-validasi").hide();
                var audio = new Audio("<?= base_url('dist/error.wav') ?>");
                audio.volume = 1.0;
                const table = $("#item_realisai").DataTable({
                    "iDisplayLength": 10,
                    "aLengthMenu": [[10, 50, 100, 1000], [10, 50, 100, 1000]],
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "ajax": {
                        "url": "<?= base_url('warehouse/picklistvalidasi/data_detail') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.filter = $("#pl").val();
                        }
                    },
                    "columnDefs": [
                        {
                            "targets": [0],
                            "orderable": false
                        }
                    ]
                });

////                $("#search").keypress(function (e) {
//                if (e.which === 13) {
//                    $("#btn_form_validasi").trigger("click");
//                }
//
//            });
                async function checkTable(event) {
                    let data = false;
                    event.preventDefault();
                    await searchArray(table.rows().data(), 1, $("#search").val()).then(
                            resp => {
                                if (resp.length > 0) {
                                    data = true;
                                }
                            }
                    );
                    return data;
                }
                var dataPicklist = null;
                var dataValid = 0;
                var dataInvalid = 0;
                var urutTable = 0;
                var totalLot = 0;
                const formvalidasi = document.forms.namedItem("form-validasi");
                const addDataTable = function (data) {
                    urutTable++;
                    table.row.add([
                        urutTable,
                        data.barcode_id,
                        data.corak_remark,
                        data.warna_remark,
                        data.corak_remark,
                        data.qty + " " + data.uom,
                        data.lokasi_fisik,
                        "Validasi"
                    ]).draw(false);
                };
                formvalidasi.addEventListener(
                        "submit",
                        async(event) => {
                    please_wait(function () {});
                    try {
                        let status = false;//await checkTable(event);
                        if (!status) {

                            request("form-validasi").then(
                                    response => {
                                        alert_notify(response.data.icon, '<span class="notify">' + response.data.message + '<strong>', response.data.type, function () {});
                                        unblockUI(function () {
//                                            setTimeout(function () {
//                                                alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
//                                            }, 250);
                                        }, 50);
                                        if (response.status === 200) {
//                                                table.search($('#search').val()).draw();
//                                                setDataChart();
                                            if (response?.data?.picklist !== null) {
                                                dataPicklist = response.data.picklist;
                                                $("#pl").val(dataPicklist.no);
                                                dataValid = dataPicklist.total_validasi;
                                                totalLot = dataPicklist.total_lot;
                                                dataInvalid = 0;
                                                table.clear().draw();
                                                $("#no_pl").html(dataPicklist.no);
                                                $("#sales").html(dataPicklist.sales);
                                                $("#cust").html(dataPicklist.nama);
                                                $("#jj").html(dataPicklist.jenis_jual);
                                                $("#tgl_picklist").html(dataPicklist.tanggal_input);
                                                $("#totalLot").html(dataPicklist.total_lot);
                                                $("#scanValid").html(dataPicklist.total_validasi);
                                                $("#scanInvalid").html(dataInvalid);
                                                $("#invalid").html(dataPicklist.total_lot - dataPicklist.total_validasi);
                                                table.search("").draw(false);
                                                error_barcode = [];
                                                loadError(error_barcode);
                                                return;
                                            }
                                            if (typeof response?.data?.item === "object") {
                                                dataValid++;
                                                var item = response?.data?.item;
                                                addDataTable(item);
                                                $("#scanValid").html(dataValid);
                                                $("#invalid").html(totalLot - dataValid);
                                            }
                                            return;
                                        }
                                        if (response.status === 500) {

                                            if (response?.data?.error_code > 0) {
                                                if (response?.data?.error_code === 11) {
                                                    error_barcode.push({
                                                        barcode: response?.data?.barcode,
                                                        message: response?.data?.message
                                                    });
                                                } else if (response?.data?.error_code === 12) {
                                                    error_barcode.push({
                                                        barcode: response?.data?.barcode,
                                                        message: ""
                                                    });
                                                }
                                                dataInvalid++;
                                                $("#scanInvalid").html(dataInvalid);
                                                loadError(error_barcode);

                                            }
                                        }
                                        audio.play();
                                    }

                            ).catch(e => {
                                console.log(e);
                            });
                        } else {
                            alert_notify('fa fa-check', 'Item double scan', 'warning', function () {});
                            unblockUI(function () {
//                                setTimeout(function () {
//                                    alert_notify('fa fa-check', 'Item double scan', 'warning', function () {});
//                                }, 1000);
                            }, 50);
                        }
                    } catch (e) {
                        unblockUI(function () {}, 50);
                        alert_modal_warning("Hubungi Dept IT");
                    } finally {
                        $("#search").val("");
                        $("#search").focus();
                    }
                    event.preventDefault();
                },
                        false
                        );

            });

        </script>
    </body>
</html>