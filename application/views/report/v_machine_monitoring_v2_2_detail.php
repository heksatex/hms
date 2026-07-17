<!DOCTYPE html>
<html lang="id">
    <head>
        <title>HMS - <?= $departmen->nama ?? "" ?> OEE Dashboard Detail</title>
        <link href="<?= base_url('dist/css/bs5/bs5.css'); ?>" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <!-- Menggunakan ECharts CDN -->
        <script src="<?= base_url('dist/js/echarts.min.js'); ?>"></script>
        <style>
            :root {
                --bg-light: #f4f7fa;
                --card-light: #ffffff;
                --primary-text: #212529;
                --muted-text: #6c757d;
                --accent-blue: #0d6efd;
                --status-green: #198754;
            }

            body {
                background-color: var(--bg-light);
                font-family: 'Inter', sans-serif;
                color: var(--primary-text);
                overflow: hidden;
            }
            .timestamp {
                font-family: 'JetBrains Mono', monospace;
                color: var(--accent-blue);
                font-size: 1.1rem;
            }
            .brand {
                font-size: 1rem;
                letter-spacing: 2px;
                font-weight: 800;
                color: var(--muted-text);
            }
            .summary-wrapper {
                background-color: var(--card-light);
                border-radius: 12px;
                border: 1px solid #dee2e6;
                margin: 12px;
                padding: 5px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.03);
                font-size: 0.9rem;
            }

            #graph {
                height: 200px;
                width: 100%;
            }
        </style>

    </head>
    <body>
        <div class="live-bar d-flex justify-content-between align-items-center">
            <div class="brand"><?= $departmen->nama ?? "" ?> - MACHINE MONITORING</div>
            <div class="timestamp" id="realtime-clock">LOADING...</div>
        </div>
        <div class="container-fluid">
            <div class="summary-wrapper">
                <div class="row align-items-center">
                    <div class="col-md-6 border-end border-light">
                        <div class="form-group row">
                            <div class="col-md-2">
                                <label for="col-form-label">Date</label>
                            </div>
                            <div class="col-md-6">
                                <input type="input" class="form-control" id="date" name="date" value="<?= $date ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 border-end border-light">
                        <div class="form-group row">
                            <div class="col-md-2">
                                <label for="col-form-label">Mesin</label>
                            </div>
                            <div class="col-md-8">
                                <select name="mesin" id="mesin" class="form-control">
                                    <option></option>
                                    <?php
                                    foreach ($mesin as $key => $value) {
                                        ?>
                                        <option value="<?= $value->devid_esp ?>" <?= $value->nama_mesin == $msn ? 'selected' : '' ?>><?= $value->nama_mesin ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="summary-wrapper">
                <div class="row align-items-center">
                    <div class="col-md-12 border-end border-light">
                        <div id="graph">
                            asdas
                        </div>
                    </div>
                </div>
                <div class="row align-items-center">
                    <div class="col-md-12 border-end border-light">
                        <table class="table table-condesed table-hover" id="tbl">
                            <thead>
                            <th>
                                No
                            </th>
                            <th>
                                Mesin
                            </th>
                            <th>
                                Running
                            </th>
                            <th>
                                No Respon
                            </th>
                            <th>
                                Ganti Benang
                            </th>
                            <th>
                                Putus / Problem
                            </th>
                            <th>
                                No Order
                            </th>
                            </thead>
                            <tbody>

                            </tbody>
                        </table> 
                    </div>
                </div>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php") ?>
        <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <script>
            function updateClock() {
                const now = new Date();
                const options = {weekday: 'short', day: 'numeric', month: 'short'};
                document.getElementById('realtime-clock').textContent =
                        `${now.toLocaleDateString('id-ID', options).toUpperCase()} | ${now.toLocaleTimeString('id-ID', {hour12: false})}`;
            }

            $('#date').daterangepicker({
//                    autoUpdateInput: false,
                startDate: moment($('#date').val(), "YYYY-MM-DD", true),
                endDate: moment($('#date').val(), "YYYY-MM-DD", true),
                locale: {
                    format: 'YYYY-MM-DD'
                },
                ranges: {
                    'H': [moment(), moment()],
                    '1..H': [moment().startOf('month'), moment()],
                    '1..31': [moment().startOf('month'), moment().endOf('month')],
                    '1..P': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            });
            const getGraph = (() => {
                return $.ajax({
                    type: "post",
                    data: {
                        date: $("#date").val(),
                        mesin: $("#mesin").val()
                    },
                    url: "<?php echo base_url(); ?>report/machinemonitoringv2/get_graph",
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });
            });
//            google.charts.load('current', {'packages': ['corechart']});
//            google.charts.setOnLoadCallback(drawCharts);
//            function drawCharts() {
//                drawGraph();
//            }
            const drawGraph = (async() => {
                var data, mesin, dt;
                await getGraph().then((res) => {
                    dt = res.date;
                    data = res.data;
                    mesin = res.mesin;
//                    dt.forEach((sd, idx) => {

//            });
                });

                const chartDom = document.getElementById('graph');
                const myChart = echarts.init(chartDom);
                var option = {
                    title: {
                        text: 'Stacked Line'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data: mesin
                    },
                    grid: {
                        left: '3%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    toolbox: {
                        feature: {
                            saveAsImage: {}
                        }
                    },
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: dt
                    },
                    yAxis: {
                        type: 'value'
                    },
                    series: data
                };

                myChart.setOption(option);
            }
            );

            $(function () {
                drawGraph();
                setInterval(updateClock, 1000);
                updateClock();
                const table = $("#tbl").DataTable({
                    "iDisplayLength": 50,
                    "processing": true,
                    "serverSide": true,
                    "order": [],
                    "scrollX": true,
                    "scrollY": "calc(85vh - 250px)",
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": false,
                    "info": true,
                    "autoWidth": false,
                    "ajax": {
                        "url": "<?= base_url("report/machinemonitoringv2/detail_table") ?>",
                        "type": "POST",
                        "data": function (d) {
                            d.date = $("#date").val();
                            d.mesin = $("#mesin").val();
                        }
                    },
                    columnDefs: [
                        {
                            "targets": [0],
                            "orderable": false
                        }
                    ]
                });

                $('#date').on('apply.daterangepicker', function (ev, picker) {
                    table.ajax.reload();
                    drawGraph();
                });

                $("#mesin").on("change", function () {
                    table.ajax.reload();
                    drawGraph();
                });
            });

        </script>
    </body>
</html>