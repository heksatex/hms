<!doctype html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
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
                            <h3 class="box-title">Form Report Delivery</h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-rd" id="form-rd" action="<?= base_url('report/delivery/export') ?>">
                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Periode Kirim</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="periode" id="periode" value="<?= $date ?>" class="form-control" required/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Customer</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="customer" id="customer" value="" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Corak</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="corak" id="corak" value="" class="form-control"/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Marketing</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <select name="marketing" class="form-control" id="marketing">
                                                    <option value="">All</option>
                                                    <?php
                                                    foreach ($sales as $key => $value) {
                                                        if ($this->session->userdata('nama')['sales_group'] === $value->kode) {
                                                            echo '<option value="' . $value->kode . '" selected>' . $value->nama . '</option>';
                                                        } else {
                                                            echo '<option value="' . $value->kode . '">' . $value->nama . '</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Order BY</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <select name="order" class="form-control" id="order">
                                                    <option value="no_sj">No SJ</option>
                                                    <option value="nama">Customer</option>
                                                    <option value="jenis_jual">Type</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <!--                                    <div class="form-group">
                                                                            <div class="col-md-12 col-xs-12">
                                                                                <div class="col-xs-4">
                                                                                    <label class="form-label">Summary</label>
                                                                                </div>
                                                                                <div class="col-xs-8 col-md-8">
                                                                                    <input type="checkbox" name="summary" id="summary" class="form-check-input"/>
                                                                                </div>
                                                                            </div>
                                                                        </div>-->
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Rekap</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <select name="rekap" id="rekap" class="form-control">
                                                    <option value="global">Global</option>
                                                    <option value="corak">Corak</option>
                                                    <option value="detail">Barcode</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6">
                                                <button class="btn btn-success" type="button" id="search"><i class="fa fa-refresh"></i> Cari </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6">
                                                <button class="btn btn-success" type="button"  id="export"><i class="fa fa-file"></i> Excel </button>
                                                <button class="hide" type="submit" id="submit"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="table-responsive over" id="result">
                                        <table class="table table-condesed table-hover rlstable  over" width="100%" id="delivery_global">
                                            <thead>                          
                                                <tr>
                                                    <th class="style" width="8px">No</th>
                                                    <th class="style">DO</th>
                                                    <th class="style">No SJ</th>
                                                    <th class="style">Tanggal dibuat</th>
                                                    <th class="style">Tanggal Kirim</th>
                                                    <th class="style">Type</th>
                                                    <th class="style">No Picklist</th>
                                                    <th class="style">Buyer</th>
                                                    <th class="style">Alamat</th>
                                                    <th class="style">Corak</th>
                                                    <th class="style">Warna</th>
                                                    <th class="style">Qty</th>
                                                    <th class="style">Qty 2</th>
                                                    <th class="style">Qty Jual</th>
                                                    <th class="style">Qty 2 Jual</th>
                                                    <th class="style">LOT</th>
                                                    <th class="style">Catatan</th>
                                                    <th class="style">Marketing</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php"); ?>
        <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <script>
            $(function () {
                $('input[name="periode"]').daterangepicker({
                    endDate: moment().startOf('day'),
                    startDate: moment().startOf('day').add(-1, 'week'),
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });

                const formrd = document.forms.namedItem("form-rd");
                formrd.addEventListener(
                        "submit",
                        (event) => {
                    please_wait(function () {});
                    request("form-rd").then(
                            response => {
                                alert_notify(response.data.icon, response.data.message, response.data.type, function () {

                                });
                                if (response.status === 200) {
                                    const a = document.createElement('a');
                                    a.style.display = 'none';
                                    a.href = response.data.data;
                                    a.download = response.data.text_name;
                                    document.body.appendChild(a);
                                    a.click();
                                }
                            }
                    ).catch().finally(() => {

                        unblockUI(function () {}, 100);
                    });
                    event.preventDefault();
                },
                        false
                        );

                $("#export").on("click", function () {
                    $("#submit").trigger('click');
                });

                const tableGlobal = $("#delivery_global").DataTable({
                    "iDisplayLength": 10,
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "paging": true,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": false,
                    "info": true,
                    "ajax": {
                        "url": "<?= base_url('report/delivery/search') ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.periode = $("#periode").val();
//                            d.summary = $("#summary").is(":checked") ? 1 : 0;
                            d.customer = $("#customer").val();
                            d.rekap = $("#rekap").find(":selected").val();
                            d.corak = $("#corak").val();
                            d.order = $("#order").find(":selected").val();
                            d.marketing = $("#marketing").find(":selected").val();
                        }
                    },
                    "columnDefs": [
                        {
                            "targets": [0],
                            "orderable": false
                        }
                    ]
                });

                $("#search").on("click", function () {
//                    tableGlobal.search("").draw(false);
                    tableGlobal.ajax.reload();
                });
            })
        </script>
    </body>
</html>