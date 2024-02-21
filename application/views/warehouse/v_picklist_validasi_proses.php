<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <style>
            .miniBarProgress {
                height: 100%;
                position: absolute;
                top: 0rem;
                left: 0rem;
            }
            .miniBar {
                height: 0.5rem;
                border: 1px solid #8a898a;
                position: relative;
                width: -webkit-calc(100% - 2rem);
                width: -moz-calc(100% - 2rem);
                width: calc(100% - 2rem);
                margin-right: 0.5rem;
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
                <?php
                $this->load->view("admin/_partials/sidebar.php");
                ?>
            </aside>
            <div class="content-wrapper">
                <section class="content-header" >
                    <div id ="status_bar">
                        <?php
                        $data['jen_status'] = $picklist->status;
                        $this->load->view("admin/_partials/statusbar.php", $data)
                        ?>
                    </div>
                </section>
                <section class="content">
                    <div class="box">

                        <div class="box-header with-border">
                            <h3 class="box-title">Validasi No <strong> <?= $picklist->no ?> </strong></h3>
                        </div>
                        <div class="box-body">
                            <?php if (isset($access->status) && $access->status) { ?>
                                <div class="col-md-6 col-xs-12">
                                    <form class="form-horizontal" method="POST" name="form-realisasi" id="form-realisasi" action="<?= base_url('warehouse/picklistvalidasi/update') ?>">
                                        <button type="submit" id="btn_form_realisasi" style="display: none"></button>
                                        <div class="form-group">
                                            <div class="col-md-12 col-xs-12">
                                                <div class="col-xs-4">
                                                    <label class="form-label">Scan Barcode</label>
                                                </div>
                                                <div class="col-xs-8 col-md-8">
                                                    <input type='text' name="search" id="search" class="form-control input-sm scan-text"/>
                                                    <label class="text-sm text-info">Tekan F2 Untuk Kembali ke Scan Barcode</label>
                                                    <input type='hidden' name="pl"  value="<?= $picklist->no ?>"/>
                                                    <input type="hidden" name="valid_date" value="<?= date('Y-m-d H:i:S') ?>">
                                                    <input type="hidden" name="on_menu" id="on_menu" value='validasi'>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
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
                                                <label class="form-label">Jenis Jual</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span><?= strtoupper($picklist->jenis_jual) ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Note</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <span><?= $picklist->keterangan ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="col-md-6 col-xs-12">
                                <div id="itemChart"></div>
                            </div>
                        </div>
                        <?php $this->load->view("admin/_partials/js.php") ?>
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="nav nav-tabs " >
                                    <li class="active"><a href="#tab_1" data-toggle="tab">Picklist Item</a></li>
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
            <footer class="main-footer">

                <?php
                if (isset($access->status) && $access->status) {
                    $this->load->view("admin/_partials/modal.php");
                }
                ?>
            </footer>
        </div>
        <script>
            $(document).keydown(function (e) {
                if (e.which === 113) {
                    $("#search").focus();
                }
            });
            $(function () {
                var audio = new Audio("<?= base_url('dist/error.wav') ?>");
                audio.volume = 1.0;
                const table = $("#item_realisai").DataTable({
                    "iDisplayLength": 10,
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
                        "url": "<?= base_url('warehouse/picklistrealisasi/data_detail') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.filter = "<?= $picklist->no ?>";
                        }
                    },
                    "columnDefs": [
                        {
                            "targets": [0],
                            "orderable": false
                        }
                    ]
                });
                $('.scan-text').focus();
                const options = {
                    series: [],
                    labels: ['Draft', 'Realisasi', 'Validasi'],
                    colors: ['#fcca03', '#0000FF', '#00CC00'],
                    tooltip: {
                        enabled: true,
                        theme: 'light'
                    },
                    chart: {
                        height: 300,
                        type: "donut"
                    },
                    noData: {
                        text: 'Loading...'
                    },
                    dataLabels: {
                        enabled: false
                    },
                    fill: {
                        colors: ['#fcca03', '#0000FF', '#00CC00']
                    }
                };
                
                var chart = new ApexCharts(document.querySelector("#itemChart"), options);
                
                chart.render();
                
                const setDataChart = function () {
                    $.ajax({
                        "url": "<?= base_url('warehouse/picklistrealisasi/persentase') ?>",
                        "type": "POST",
                        beforeSend: function (e) {
                            if (e && e.overrideMimeType) {
                                e.overrideMimeType("application/json;charset=UTF-8");
                            }
                        },
                        "data": {
                            "pl": "<?= $picklist->no ?? '' ?>"
                        },
                        "success": function (resp) {
                            chart.updateSeries(resp);
                        }
                    });
                };
                setDataChart();
                
                
                $("#search").keypress(function (e) {
                    if (e.which === 13) {
                        $("#btn_form_realisasi").trigger("click");
                    }
                    
                });
                const formrealisasi = document.forms.namedItem("form-realisasi");
                formrealisasi.addEventListener(
                        "submit",
                        event => {
                            please_wait(function () {});
                            try {
                                request("form-realisasi").then(
                                        response => {
                                            unblockUI(function () {
                                                setTimeout(function () {
                                                    alert_notify(response.data.icon, response.data.message, response.data.type, function () {});
                                                }, 500);
                                            });
                                            if (response.status === 200) {
                                                table.search($('#search').val()).draw();
                                                setDataChart();
                                                return;
                                                
                                            }
                                            audio.play();
                                            
                                        }
                                
                                ).catch(e => {
                                    
                                });
                                
                            } catch (e) {
                                unblockUI(function () {});
                                alert_modal_warning("Hubungi Dept IT");
                            } finally {
                                $("#search").val("");
                                $("#search").focus();
                                
                            }
                            event.preventDefault();
                        },
                        false
                        );
                
                $("#btn-cancel").on('click', function (e) {
                    e.preventDefault();
                    $("#tambah_data").modal({
                        show: true,
                        backdrop: 'static'
                    });
                    $('.modal-title').text('Cancel Item');
                    $(".tambah_data").html(`<?= $view_cancel ?>`);
                    $("#btn-tambah").hide();
                    
                    $("#search_item_cancel").keypress(function (e) {
                        if (e.keyCode === 13) {
                            please_wait(function () {});
                            $.ajax({
                                "url": "<?= base_url('warehouse/picklistrealisasi/update_status') ?>",
                                "type": "POST",
                                beforeSend: function (e) {
                                    if (e && e.overrideMimeType) {
                                        e.overrideMimeType("application/json;charset=UTF-8");
                                    }
                                },
                                data: {
                                    "pl": "<?= $picklist->no ?? '' ?>",
                                    'barcode': $("#search_item_cancel").val(),
                                    'on_menu': $("#on_menu").val()
                                },
                                success: function (response) {
                                    unblockUI(function () {
                                        setTimeout(function () {
                                            alert_notify(response.icon, response.message, response.type, function () {});
                                        }, 500);
                                    });
                                    table.search("").draw();
                                    setDataChart();
                                },
                                error: function (response) {
                                    unblockUI(function () {
                                        setTimeout(function () {
                                            alert_notify(response?.responseJSON?.icon, response?.responseJSON?.message, response?.responseJSON?.type, function () {});
                                        }, 1000);
                                    });
                                }
                            });
                            
                        }
                    });
                    $(document).keydown(function (e) {
                        if (e.which === 113) {
                            $("#search_item_cancel").focus();
                        }
                    });
                    
                    
                });
                
                
            });
            
            
        </script>
    </body>
</html>