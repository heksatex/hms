!<!doctype html>
<html lang="en">
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
                            <h3 class="box-title">Form Draft Surat Jalan</h3>

                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-dsj" id="form-dsj" action="<?= base_url('report/draftsuratjalan/checking') ?>">
                                <div class="col-md-4 col-xs-12">
                                    <div class="row">
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label required">No Picklist</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type="text" name="no_pl" id="no_pl" class="form-control" required>
                                                    <label class="text-sm text-info">Tekan F2 Untuk Kembali Cari Picklist</label>
                                                </div>
                                            </div>

                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">No. SJ</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <span id="nosj"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Pack</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="pack"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Tanggal</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="tgl_pl"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Customer</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="buyer"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Alamat</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="buyer_addr"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Note</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span id="note"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6" style="padding-bottom: 10%;">
                                                <button class="btn btn-success" type="submit"><i class="fa fa-refresh"></i> Cari </button>
                                            </div>
                                        </div>
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6">
                                                <button class="btn btn-success" id="export_excel" type="button"><i class="fa fa-file"></i> Excel </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive over" id="result">

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script src="https://cdn.rawgit.com/rainabba/jquery-table2excel/1.1.0/dist/jquery.table2excel.min.js"></script>
        <script>
            $(document).keydown(function (e) {
                if (e.which === 113) {
                    $("#no_pl").focus();
                }

            });
            $(function () {
                $("#no_pl").focus();
                $("#export_excel").hide();
                const formdsj = document.forms.namedItem("form-dsj");
                formdsj.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-dsj").then(
                            response => {
                                $("#result").html("");
                                alert_notify(response.data.icon, response.data.message, response.data.type, function () {
                                    $("#export_excel").hide();
                                });
                                if (response.status === 200) {
                                    $("#export_excel").show();
                                    $("#no_pl").val("");

                                    const {picklist, detail} = response.data.data;

                                    $("#pack").html(picklist?.no);
                                    $("#nosj").html(picklist?.no_sj);
                                    $("#tgl_pl").html(picklist?.tanggal_input);
                                    $("#buyer").html(picklist?.nama);
                                    $("#buyer_addr").html(picklist?.alamat);
                                    $("#note").html(picklist?.keterangan);
                                    $("#result").append(detail);
//                                    console.log(detail);
                                    return;
                                }

                                $("#pack").html("");
                                $("#nosj").html("");
                                $("#tgl_pl").html("");
                                $("#buyer").html("");
                                $("#buyer_addr").html("");
                                $("#note").html("");
                            }
                    ).catch().finally(() => {

                        unblockUI(function () {}, 100);
                    });
                    event.preventDefault();
                },
                        false
                        );
                $("#export_excel").on("click", function () {
                    please_wait(function () {});
                    $.ajax({
                        url: "<?= base_url('report/draftsuratjalan/export') ?>",
                        type: "POST",
                        data: {
                            no_pl: $("#pack").html()
                        },
                        success: function (data) {
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = data.data;
                            a.download = data.text_name;
                            document.body.appendChild(a);
                            a.click();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            alert_notify(jqXHR.responseJSON.icon, jqXHR.responseJSON.message, jqXHR.responseJSON.type, function () {});
//                            alert(jqXHR);
                        },
                        complete: function (dt) {

                            unblockUI(function () {}, 100);
                        }
                    });
//                    $("#draftsuratjalan").table2excel({
//                        name: $("#pack").html(),
//                        filename: "draft_surat_jalan_pl_" + $("#pack").html(),
//                        fileext: ".xlsx"
//                    });
                });
            });
        </script>
    </body>
</html>