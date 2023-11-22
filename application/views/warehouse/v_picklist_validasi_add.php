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
        </style>
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini">
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
                            <h3 class="box-title">Form Validasi Picklist</strong></h3>
                        </div>
                        <div class="box-body">
                            <?php if (isset($access->status) && $access->status) { ?>
                                <div class="col-md-6 col-xs-12">
                                    <form class="form-horizontal" method="POST" name="form-validasi" id="form-validasi" action="<?= base_url('warehouse/picklistvalidasi/update') ?>">
                                        <button type="submit" id="btn_form_validasi" style="display: none"></button>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">Scan Barcode / No PL</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type='text' name="search" id="search" class="form-control input-sm scan-text" required/>
                                                    <label class="text-sm text-info">Tekan F2 Untuk Kembali ke Scan</label>
                                                    <input type='hidden' name="pl" id="pl" value=""/>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
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
                                                <label class="form-label">Sales</label>
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
                            <?php } ?>
                            <div class="col-md-6 col-xs-12">
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4">
                                            <label class="form-label">Total LOT</label>
                                        </div>
                                        <div class="col-xs-8 col-md-8">
                                            <span id="totalLot"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4">
                                            <label class="form-label"></label>
                                        </div>
                                        <div class="col-xs-8 col-md-8">
                                            <span></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4">
                                            <label class="form-label">Scan Valid</label>
                                        </div>
                                        <div class="col-xs-8 col-md-8">
                                            <span id="scanValid"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12 col-xs-12">
                                        <div class="col-xs-4">
                                            <label class="form-label">Scan Invalid</label>
                                        </div>
                                        <div class="col-xs-8 col-md-8">
                                            <span id="scanInvalid"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
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
            $(document).keydown(function (e) {
                if (e.which === 113) {
                    $("#search").focus();
                }
            });
            $(function () {
                $("#search").focus();
                $("#btn-validasi").hide();
                var nopl = "";
                var audio = new Audio("<?= base_url('dist/error.wav') ?>");
                audio.volume = 1.0;
                const table = $("#item_realisai").DataTable({
                    "iDisplayLength": 10,
                    "order": [],
                    "paging": true,
                    "lengthChange": false,
                    "searching": true,
                    "ordering": true,
                    "info": true,
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
//                    please_wait(function () {});
            try {
            let status = await checkTable(event);
                    if (!status) {

            request("form-validasi").then(
                    response => {
                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
//                                        unblockUI(function () {
//                                            setTimeout(function () {
//                                                alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
//                                            }, 250);
//                                        });
                            if (response.status === 200) {
//                                                table.search($('#search').val()).draw();
//                                                setDataChart();
                    if (response?.data?.picklist !== null) {
                    dataPicklist = response.data.picklist;
                            $("#pl").val(dataPicklist.no);
                            dataValid = 0;
                            dataInvalid = 0;
                            table.clear().draw();
                            $("#no_pl").html(dataPicklist.no);
                            $("#sales").html(dataPicklist.sales_kode);
                            $("#cust").html(dataPicklist.nama);
                            $("#jj").html(dataPicklist.jenis_jual);
                            $("#tgl_picklist").html(dataPicklist.tanggal_input);
                            $("#totalLot").html(dataPicklist.total_lot);
                            $("#scanValid").html(dataPicklist.total_realisasi);
                            $("#scanInvalid").html(dataInvalid);
                            return;
                    }
                    if (typeof response?.data?.item === "object") {
                    dataValid++;
                            var item = response?.data?.item;
                            addDataTable(item);
                            $("#scanValid").html(dataValid);
                    }
                    return;
                    }
                    if (response.status === 500) {
                    if (response?.data?.error_code >= 0) {
                    dataInvalid++;
                            $("#scanInvalid").html(dataInvalid);
                    }
                    }
                    audio.play();
                    }

            ).catch(e => {
            console.log(e);
            });
            } else {
            alert_notify('fa fa-check', 'Item double scan', 'warning', function () {});
//                            unblockUI(function () {
//                                setTimeout(function () {
//                                    alert_notify('fa fa-check', 'Item double scan', 'warning', function () {});
//                                }, 1000);
//                            });
            }
            } catch (e) {
//                        unblockUI(function () {});
            alert_modal_warning("Hubungi Dept IT");
            } finally {
            $("#search").val("");
                    $("#search").focus();
            }
            event.preventDefault();
            },
                    false
                    );
            }
            );

        </script>
    </body>
</html>