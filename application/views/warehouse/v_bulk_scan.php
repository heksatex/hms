<!DOCTYPE html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            #statusbulk {
                color: whitesmoke;
                background-color: red;
                text-align: center;
                font-size: 150%;
                font-weight: 400;
            }
            #posisibulk {
                color: white;
                background-color: red;
                text-align: center;
                font-size: 6rem;
                font-weight: 400;
                height: 10vh;
            }
            .row{
                padding-bottom: 5px;
            }
            .bolded {
                font-weight:bold;
                /*font-size: 100%;*/
                letter-spacing: 2px;
            }
            .list{
                font-size: 150%;
            }

            .count {
                font-weight:bold;
                font-size: 150%;
                letter-spacing: 2px;
            }
            .status_bal{
                font-weight:bold;
                font-size: 200%;
                letter-spacing: 2px;
                /*float: right;*/
                text-align: end;
            }

            #tablesdata_{
                height:50vh;
            }
            #no_pl{
                font-size: 200%;
                letter-spacing: 1px;
                font-weight: 400;
                background-color: yellow;
            }
            #buyer{
                font-size: 200%;
                letter-spacing: 1px;
                font-weight: 400;
                background-color: yellow;
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
                $data['deptid'] = $id_dept;
                $this->load->view("admin/_partials/topbar.php", $data)
                ?>
            </header>
            <aside class="main-sidebar">
                <?php $this->load->view("admin/_partials/sidebar.php") ?>
            </aside>
            <div class="content-wrapper">
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Bulking</strong></h3>
                        </div>
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12 col-xs-12">
                                    <div class="row">
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
                                                    <label class="form-label">Buyer</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <span id="buyer"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <!--                                        <div class="col-md-6 col-xs-12">
                                                                                    <div class="form-group">
                                                                                        <div class="col-md-12" id="statusbulk">
                                        
                                                                                        </div>
                                                                                    </div>
                                                                                </div>-->
                                        <div class="col-md-12 col-xs-12">
                                            <div class="form-group">
                                                <div class="col-md-12" id="posisibulk">
                                                    -
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-xs-12">
                                            <form class="form-horizontal" method="POST" name="form-search" id="form-search" action="<?= base_url('warehouse/bulk/bulking') ?>">
                                                <button type="submit" id="btn_form_validasi" style="display: none"></button>
                                                <div class="form-group">
                                                    <div class="col-md-12 col-xs-12">
                                                        <div class="col-md-4 col-xs-12">
                                                            <label class="form-label required">Scan Barcode</label>
                                                        </div>
                                                        <div class="col-xs-12 col-md-8">
                                                            <input type='text' name="search" id="search" class="form-control input-lg scan-text" required autocomplete="off"/>
                                                            <label class="text-sm text-info"></label>
                                                            <input type='hidden' name="pl" id="pl" value=""/>
                                                            <input type='hidden' name="doid" id="doid" value=""/>
                                                            <input type="hidden" name="status" id="status" value=""/>
                                                            <input type="hidden" name="no_bulk" id="no_bulk" value=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <!--                                        <div class="col-md-6 col-xs-12">
                                                                                    <div class="row">
                                                                                        <div class="col-xs-6">
                                                                                            <div class="form-group">
                                                                                                <div class="col-xs-8">
                                                                                                    <label class="label-pill">Total LOT</label>
                                                                                                </div>
                                                                                                <div class="col-xs-4">
                                                                                                    <span id="total-lot">0</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <div class="col-xs-8">
                                                                                                    <label class="label-pill">Scan Valid</label>
                                                                                                </div>
                                                                                                <div class="col-xs-4">
                                                                                                    <span id="scan-valid">0</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        
                                                                                        <div class="col-xs-6">
                                                                                            <div class="form-group">
                                                                                                <div class="col-xs-8">
                                                                                                    <label class="label-pill">Belum Scan</label>
                                                                                                </div>
                                                                                                <div class="col-xs-4">
                                                                                                    <span id="not-scan">0</span>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="form-group">
                                                                                                <div class="col-xs-8">
                                                                                                    <label class="label-danger text-aqua">Scan Invalid</label>
                                                                                                </div>
                                                                                                <div class="col-xs-4">
                                                                                                    <span id="scan-invalid">0</span>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        
                                                                                    </div>
                                        
                                                                                </div>-->
                                        <!--                                        <div class="col-md-6 col-xs-12">
                                                                                    <div class="form-group">
                                                                                        <div class="col-md-12 col-xs-12">
                                                                                            <div style="text-align:center;">
                                                                                                <label class="form-label">BULK ID</label>
                                                                                                <h2 id="posisi_bulk" style="letter-spacing: 2px; font-weight: 600;">-</h2>
                                                                                            </div>
                                        
                                        
                                        
                                                                                        </div>
                                                                                    </div>
                                                                                </div>-->
                                    </div>
                                </div>
                                <!--                                <div class="col-md-4 col-xs-12"  id="tablesdata">
                                
                                                                </div>-->
                            </div>
                            <div class="row" id="tablesdata">

                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view("admin/_partials/js.php") ?>
            <footer class="main-footer">
                <?php $this->load->view("admin/_partials/modal.php") ?>
            </footer>
        </div>
        <script>
            var statusScan = "-";

            const checkStatusScan = function () {
                statusScan = "Silahkan Scan Barcode PL";
                switch (true) {
                    case ($("#pl").val() !== "" && $("#no_bulk").val() !== "") :
                        statusScan = "Silahkan Scan Barcode Item";
                        $("#status").val("item");
                        break;
                    case ($("#pl").val() !== "" && $("#no_bulk").val() === "") :
                        statusScan = "Silahkan Scan Barcode Bulk";
                        $("#status").val("bulk");
                        break;
                    case ($("#pl").val() === "" && $("#no_bulk").val() !== "") :
                        statusScan = "Silahkan Scan Barcode Picklist";
                        $("#status").val("pl");
                        break;
                }
                $("#search").blur();
                $("#search").focus();
            };

            const checkStatusCancel = function () {
                $("#status").val("cancel");
                statusScan = "Silahkan Scan Barcode Untuk dibatalkan";
                $("#search").blur();
                $("#search").focus();
            };

            const checkStatusPL = function () {
                $("#status").val("pl");
//                        $("#posisibulk").html("Silahkan Scan Barcode Picklist");
                statusScan = "Silahkan Scan Barcode Picklist";
                $("#search").blur();
                $("#pl").val("");
                $("#no_pl").html("");
                $("#buyer").html("");
                $("#tablesdata").html("");
                $("#no_bulk").val("");
                $("#bal_aktif").html("");
                $("#search").focus();
            };
            const checkStatusBulk = function () {
                statusScan = "Silahkan Scan Barcode Bulk";

                $("#search").blur();
                $("#status").val("bulk");
                $("#bal_aktif").html("-");
                $("#search").focus();
            };
            const checkDetail = ((pl, bulk) => {
                please_wait(() => {
                });
                $("#view_data").modal({
                    show: true,
                    backdrop: 'static'
                });
                $(".view_body").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                $('.modal-title').text("Detail List Item Bulk " + bulk );
                $.post('<?php echo site_url() ?>warehouse/bulk/show_view_detail',
                        {
                            "bulk": bulk,
                            "pl": pl

                        },
                        function (html) {
                            setTimeout(function () {
                                $(".view_body").html(html);
                            }, 1000);
                        }
                );
                unblockUI(() => {
                }, 50);

            });
            const hideDetail = (() => {
                $("#view_data").modal('hide');
            });
            $('#view_data').on('hidden.bs.modal', function () {

                $("#search").val("");
                $("#search").focus();
            });
            $(document).keydown(function (e) {
                checkInput(e, "=", {
                    "=scan=": function () {
                        checkStatusScan();
//                        $("#status").val("scan");

                    },
                    "=cancel=": function () {
                        checkStatusCancel();
                    },
                    "=pl=": function () {
                        checkStatusPL();
                    },
                    "=bulk=": function () {
                        checkStatusBulk();
                    },
                    "=detail=": function () {
                        checkDetail($("#pl").val(), $("#no_bulk").val());
                    },
                    "=hidedetail=": (() => {
                        hideDetail();
                    })
                });
            });


            const getSearch = document.getElementById("search");
            getSearch.addEventListener("focus", (event) => {
                $("#posisibulk").css({"background-color": "green", "color": "white"});
                $("#posisibulk").html(statusScan);
            });
            getSearch.addEventListener("blur", (event) => {
                $("#posisibulk").css({"background-color": "red", "color": "white"});
                $("#posisibulk").html("Silahkan Scan barcode 'SCAN' ");
            });

            const loadSummary = async function (pl, bl = "") {
                $.ajax({
                    url: "<?= base_url('warehouse/bulk/bulking_data') ?>",
                    type: "POST",
                    data: {
                        pl: pl,
                        bulk: bl
                    },
                    success: function (response) {
                        $("#tablesdata").html(response);
                    }
                });
            };


            $(function () {
                $("#btn-simpan").hide();
                $("#status").val("pl");
                statusScan = "Silahkan Scan Barcode Picklist";
                $("#search").focus();

                $("#btn-simpan").unbind("click").off("click").on('click', function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $(".tambah_data").html('<center><h5><img src="<?php echo base_url('dist/img/ajax-loader.gif') ?> "/><br>Please Wait...</h5></center>');
                    $('.modal-title').text('Tambah Net / Gross Weight');
                    $.post("<?= base_url('warehouse/bulk/show_net_gross/') ?>", {pl: $("#pl").val()}, function (data) {
                        setTimeout(function () {
                            $(".tambah_data").html(data.data);
                            $("#btn-tambah").html("Simpan");
                        }, 1000);
                    });
                    $("#btn-tambah").unbind("click").off("click").on("click", function () {
                        $("#btn_form_net_gross").trigger("click");
                    });
                });

                const formsearch = document.forms.namedItem("form-search");
                formsearch.addEventListener(
                        "submit",
                        async(event) => {
                    please_wait(function () {});
                    try {
                        let search = $("#search").val();
                        if (search.charAt(0) === "=" || search.charAt(0) === "[") {

                            $("#search").val("");
                            throw new Error("");
                        }
                        let checkFirstString = search.slice(0, 2);
                        checkFirstString = checkFirstString.toLowerCase();
                        switch (checkFirstString) {
                            case "pl":
                                checkStatusPL();
                                break;
                            case "bl":
                                checkStatusBulk();
                                break;
                        }
                        request("form-search").then(
                                async (response) => {

                            unblockUI(function () {
                                alert_notify(response.data.icon, '<span class="notify">' + response.data.message + '<strong>', response.data.type, function () {});
                            }, 50);
                            if (response.status === 200) {
                                var data = null;
                                switch (response.data.status) {
                                    case "pl":
                                        data = response.data?.data;
                                        $("#pl").val(data.no);
                                        $("#no_pl").html(data.no);
                                        $("#buyer").html(data.nama);
                                        $("#doid").val(data.no_sj);
                                        $("#status").val("bulk");
                                        statusScan = "Silahkan Scan Barcode Bulk";
                                        $("#btn-simpan").show();
                                        $("#btn-simpan").html("Gross Weight");
                                        $("#search").blur();
                                        $("#search").focus();
                                        await loadSummary(data.no);
                                        break;
                                    case "bulk":
                                        data = response.data?.data;
                                        $("#no_bulk").val(data.no_bulk);
                                        $("#bal_aktif_qty").html(data.total_item);
                                        $("#status").val("item");
                                        $("#bal_aktif").html(data.no_bulk);
                                        statusScan = "Silahkan Scan Barcode Item";
                                        $("#search").blur();
                                        $("#search").focus();
                                        await loadSummary($("#pl").val(), data.no_bulk);
                                        break;
                                    case "cancel":
                                        await loadSummary($("#pl").val());
                                        break;
                                    case "item":
                                        await loadSummary($("#pl").val(), $("#no_bulk").val());
                                        break;
                                }
                            }

                        });
                    } catch (e) {

                    } finally {
                        $("#search").val("");
                        unblockUI(function () {}, 50);
                    }
                    event.preventDefault();
                },
                        false
                        );
            });
            const isNumb = (event, element) => {
                event.target.setCustomValidity('');
                const patt = /^\d+\.{0,1}\d{0,2}$/;
                let value = event.target.value;
                if (!patt.test(value)) {
                    event.target.reportValidity();
                    element.setAttribute("maxlength", value.length);
                } else
                {
                    element.removeAttribute("maxlength")
                }
                if (value.length === 0) {
                    element.removeAttribute("maxlength");
                }
            }
        </script>
    </body>
</html>