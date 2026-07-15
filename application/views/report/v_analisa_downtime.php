<!doctype html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <style>
            /* Premium Card Design */
            .chart-card {
                border-radius: 12px;
                padding: 25px;
                border: 1px solid #dee2e6;
                box-shadow: 0 4px 6px rgba(0,0,0,0.03);
                margin-bottom: 25px;
                align-content: center;
            }

            #shift_comparison_chart {
                width: 100%;
                height: 420px;
                min-height: 420px;
                display: block;
            }
            .mono {
                font-family: 'JetBrains Mono', monospace;
                font-weight: 600;
            }
        </style>
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
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Analisa Downtime</h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-rd" id="form-rd" action="<?= base_url('report/analisadowntime/search') ?>">
                                <div class="col-md-8 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label required">Tanggal</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <input type="text" name="tanggal" id="tanggal" value="" class="form-control" required/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-6">
                                                <input type="hidden" name="dept" id="dept" value="wrd" class="form-control"/>
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
                        </div>
                        <div class="box-footer">
                            <div class="row">
                                <div class="chart-card">
                                    <div id="shift_comparison_chart"></div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12 table-responsive">
                                    <table class="table table-hover align-middle text-center" id="downtime-table">
                                        <thead>
                                            <tr>
                                                <th class="text-start">Tanggal</th>
                                                <th>Running (Hrs)</th>
                                                <th>No Response (Hrs)</th>
                                                <th>Ganti Benang (Hrs)</th>
                                                <th>Putus/Problem (Hrs)</th>
                                                <th>No Order (Hrs)</th>
                                                <th>Total Capacity</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                        <tfoot></tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php"); ?>
        <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>
        <script>
            let myChart;
            const  asDataGrafik = (() => {
                return $.ajax({
                    type: "POST",
                    data: {
                        tanggal: $("#tanggal").val()
                    },
                    url: "<?php echo base_url(); ?>report/analisadowntime/get_grafiks",
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });
            });
            Number.prototype.themeFormat = function () {
                return this.toFixed(1).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            };
            var filteredRun = [], filteredNoResp = [], filteredGanti = [], filteredProb = [], filterNoOrder = [], filteredOff = [], activeDates = [], cps = [];
            async function renderChart() {
                // Arrays tampungan filter terpilih
                activeDates = [];
                filteredRun = [];
                filteredNoResp = [];
                filteredGanti = [];
                filteredProb = [];
                filterNoOrder = [];
                filteredOff = [];
                cps = [];
                let tRun = 0, tNoResp = 0, tGanti = 0, tProb = 0, tNoOrd = 0, toff = 0, totals = 0;
                let currentCapacitys = 0, currentCapacity = 0;
                let htmlRows = '';
                await asDataGrafik().then((res) => {
                    var dt = res.data;
                    dt.forEach((sd, idx) => {
                        let total = parseInt(sd.total_log) / parseInt(sd.count_mesin) / 60;
                        let run = parseInt(sd.running) / 60;
                        let noResp = parseInt(sd.noresp) / 60;
                        let ganti = parseInt(sd.benang) / 60;
                        let prob = parseInt(sd.problem) / 60;
                        let order = parseInt(sd.noorder) / 60;
                        currentCapacity = parseInt(sd.count_mesin) * 24;
                        let off = currentCapacity - (run + noResp + ganti + prob);
                        if (off < 0)
                            off = 0;

                        activeDates.push(sd.dt);
                        filteredRun.push(run.themeFormat());
                        filteredNoResp.push(noResp.themeFormat());
                        filteredGanti.push(ganti.themeFormat());
                        filteredProb.push(prob.themeFormat());
                        filterNoOrder.push(order.themeFormat());
                        filteredOff.push(off.themeFormat());
                        cps.push(currentCapacity);

                        tRun += run;
                        tNoResp += noResp;
                        tGanti += ganti;
                        tProb += prob;
                        tNoOrd += order;
                        toff += off;
                        totals += total;
                        currentCapacitys += currentCapacity;
                        htmlRows += `
                    <tr>
                        <td class="fw-semibold">${sd.dt}</td>
                        <td class="mono text-success">${run.themeFormat()}</td>
                        <td class="mono text-danger">${noResp.themeFormat()}</td>
                        <td class="mono text-primary">${ganti.themeFormat()}</td>
                        <td class="mono text-warning fw-semibold">${prob.themeFormat()}</td>
                        <td class="mono text-dark">${off.themeFormat()}</td>
                        <td class="mono text-secondary">${currentCapacity.themeFormat()}</td>
                    </tr>
                `;
                    });
                });
                $("#downtime-table tbody").html(htmlRows);
                htmlRows = `
                    <tr>
                        <td>TOTAL MTD</td>
                        <td class="mono text-success">${tRun.themeFormat()}</td>
                        <td class="mono text-danger">${tNoResp.themeFormat()}</td>
                        <td class="mono text-primary">${tGanti.themeFormat()}</td>
                        <td class="mono text-warning fw-semibold">${tProb.themeFormat()}</td>
                            <td class="mono text-dark">${toff.themeFormat()}</td>
                        <td class="mono text-secondary">${currentCapacitys.themeFormat()}</td>
                    </tr>
                `;
                $("#downtime-table tfoot").html(htmlRows);
                let labelInterval = 0;
                if (activeDates.length > 31)
                    labelInterval = 2;
                if (activeDates.length > 60)
                    labelInterval = 5;
                // Terapkan ke Grafik ECharts


                myChart.setOption({
                    xAxis: {data: activeDates, axisLabel: {interval: labelInterval}},
                    yAxis: {max: currentCapacity},
                    series: [
                        {data: filteredRun},
                        {data: filteredNoResp},
                        {data: filteredGanti},
                        {data: filteredProb},
                        {data: filteredOff}
                    ]
                });

            }

            $(function () {
                $('#tanggal').daterangepicker({
                    endDate: moment().startOf('day'),
                    startDate: moment().startOf('day').add(-1, 'week'),
                    minYear: 2026,
                    maxDate: new Date(),
                    minDate: "2026/04/01",
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });

                const chartDom = document.getElementById('shift_comparison_chart');
                myChart = echarts.init(chartDom);
                renderChart();
                const initialOption = {
                    tooltip: {trigger: 'axis', axisPointer: {type: 'shadow'}},
                    legend: {data: ['Running', 'No Response', 'Ganti Benang', 'Putus/Problem', 'No Order'], bottom: '0%'},
                    grid: {top: '30px', left: '1%', right: '1%', bottom: '40px', containLabel: true},
                    xAxis: {type: 'category', axisLabel: {color: '#6c757d', fontSize: 9, rotate: 45}},
                    yAxis: {type: 'value', name: 'Durasi (Jam)', splitLine: {lineStyle: {type: 'dashed', color: '#EBEBEB'}}},
                    series: [
                        {name: 'Running', type: 'bar', stack: 'wd_date_stack', itemStyle: {color: '#198754'}},
                        {name: 'No Response', type: 'bar', stack: 'wd_date_stack', itemStyle: {color: '#dc3545'}},
                        {name: 'Ganti Benang', type: 'bar', stack: 'wd_date_stack', itemStyle: {color: '#0d6efd'}},
                        {name: 'Putus/Problem', type: 'bar', stack: 'wd_date_stack', itemStyle: {color: '#ffc107'}},
                        {name: 'No Order', type: 'bar', stack: 'wd_date_stack', itemStyle: {color: '#212529'}}
                    ]
                };
                myChart.setOption(initialOption);
                window.addEventListener('resize', () => {
                    if (myChart)
                        myChart.resize();
                });

                $("#search").on("click", function () {
                    renderChart();
                });

                $("#export").on("click", function () {
                    var imgData = myChart.getDataURL({
                        type: 'png',
                        pixelRatio: 2, // Higher quality
                        backgroundColor: '#fff'
                    });
                    $.ajax({
                        type: "post",
                        data: {
                            img: imgData,
                            tanggal: $("#tanggal").val(),
                            tbl: {
                                tgl: activeDates,
                                run: filteredRun,
                                noResp: filteredNoResp,
                                benang: filteredGanti,
                                problem: filteredProb,
                                off: filteredOff,
                                cps: cps
                            }
                        },
                        url: "<?php echo base_url(); ?>report/analisadowntime/export",
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {}, 200);
                        },
                        beforeSend: function (xhr) {
                            please_wait((() => {

                            }));
                        },
                        success: ((data) => {
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = data.data;
                            a.download = data.text_name;
                            document.body.appendChild(a);
                            a.click();
                        })

                    });

                });
            })

        </script>
    </body>
</head>