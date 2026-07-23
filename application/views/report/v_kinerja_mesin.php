<!doctype html>
<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link rel="stylesheet" type="text/css" href="<?= base_url('plugins/daterangepicker/daterangepicker.css'); ?>" />
        <style>
            /* Premium Card Design */
            .chart-card {
                border-radius: 8px;
                padding: 5px;
                border: 1px solid var(--border-color);
                box-shadow: 0 1px 3px rgba(0,0,0,0.02);
                margin-bottom: 24px;
            }

            #shift_comparison_chart {
                width: 100%;
                height: 100%;
                min-height: 300px;
                display: block;
            }
            .mono {
                font-family: 'JetBrains Mono', monospace;
                font-weight: 600;
            }

        </style>
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
                </section>
                <section class="content">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title">Report Kinerja Mesin</h3>
                        </div>
                        <div class="box-body">
                            <form class="form-horizontal" method="POST" name="form-rd" id="form-rd" action="<?= base_url('report/kinerja/export') ?>">
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
                                        <div class="col-md-12 col-xs-12">
                                            <div class="col-xs-4">
                                                <label class="form-label">Mesin</label>
                                            </div>
                                            <div class="col-xs-8 col-md-8">
                                                <select class="form-control select2 mesin" style="width: 100%" name="mesin">
                                                    <option value="">ALL</option>
                                                    <?php
                                                    foreach ($mesin as $key => $value) {
                                                        ?>
                                                        <option value="<?= $value->devid_esp ?>"><?= $value->nama_mesin ?></option>
                                                        <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 col-xs-12">
                                    <!--                                    <div class="form-group">
                                                                            <div class="col-md-12 col-xs-12">
                                                                                <div class="col-xs-6">
                                                                                    <button class="btn btn-success" type="button" id="search"><i class="fa fa-refresh"></i> Cari </button>
                                                                                </div>
                                                                            </div>
                                                                        </div>-->
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
                                                <th class="text-start">Shift Name</th>
                                                <th>Running (Hrs)</th>
                                                <th>No Response (Hrs)</th>
                                                <th>Ganti Benang (Hrs)</th>
                                                <th>Putus/Problem (Hrs)</th>
                                                <th>No Order (Hrs)</th>
                                                <th>Total Capacity</th>
                                                <th>% Utilization</th>
                                            </tr>
                                        </thead>
                                        <tbody id="table-body"></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                </section>
            </div>
        </div>
        <?php $this->load->view("admin/_partials/js.php"); ?>
        <script type="text/javascript" src="<?= base_url('plugins/daterangepicker/daterangepicker.js'); ?>"></script>
        <script src="<?= base_url('dist/js/echarts.min.js'); ?>"></script>
        <script>
            let myChart;
            const  asDataGrafik = (() => {
                return $.ajax({
                    type: "post",
                    data: {
                        tanggal: $("#tanggal").val(),
                        mesin: $(".mesin").val()
                    },
                    url: "<?php echo base_url(); ?>report/kinerjamesin/get_grafiks",
                    complete: function (jqXHR, textStatus) {
                        unblockUI(function () {}, 200);
                    }
                });
            });
            var totals = {
                "pagi": {
                    "running": 0,
                    "noresp": 0,
                    "benang": 0,
                    "problem": 0,
                    "noorder": 0,
                    "total": 0,
                    "efficiency": 0
                },
                "siang": {
                    "running": 0,
                    "noresp": 0,
                    "benang": 0,
                    "problem": 0,
                    "noorder": 0,
                    "total": 0,
                    "efficiency": 0
                },
                "malam": {
                    "running": 0,
                    "noresp": 0,
                    "benang": 0,
                    "problem": 0,
                    "noorder": 0,
                    "total": 0,
                    "efficiency": 0
                }
            };
            async function renderChart() {
                await asDataGrafik().then((res) => {
                    var dt = res.data;
                    dt.forEach((sd, idx) => {
                        totals[sd.shift_range]["running"] += parseInt(sd.running) / 60;
                        totals[sd.shift_range]["noresp"] += parseInt(sd.noresp) / 60;
                        totals[sd.shift_range]["benang"] += parseInt(sd.benang) / 60;
                        totals[sd.shift_range]["problem"] += parseInt(sd.problem) / 60;
                        totals[sd.shift_range]["noorder"] += parseInt(sd.noorder) / 60;
                        totals[sd.shift_range]["total"] += parseInt(sd.total_log) / 60;
                        totals[sd.shift_range]["efficiency"] = (totals[sd.shift_range]["running"] / (totals[sd.shift_range]["total"] - totals[sd.shift_range]["noorder"]) * 100);
                        if (isNaN(totals[sd.shift_range]["efficiency"]))
                            totals[sd.shift_range]["efficiency"] = 0;
                    });
                });

                renderTable();
                let mesin = $(".mesin :selected").text();
                const options = {
                    title: {
                        text: 'Grafik Perbandingan Distribusi Jam Kerja Antar Shift (Kategori Terpisah)' + (mesin === 'ALL' ? '' : ` Mesin : ${mesin}`),
                        left: 'center',
                        textStyle: {fontSize: 14, fontWeight: 'normal', color: '#495057'}
                    },
                    tooltip: {
                        trigger: 'axis',
                        axisPointer: {type: 'shadow'},
                        backgroundColor: 'rgba(255, 255, 255, 0.95)',
                        borderWidth: 1,
                        borderColor: '#e2e8f0',
                        textStyle: {color: '#1e293b', fontSize: 12}
                    },
                    legend: {
                        data: ['Running', 'No Response', 'Ganti Benang', 'Putus/Problem', 'No Order'],
                        bottom: '0%',
                        icon: 'rect',
                        itemWidth: 12,
                        itemHeight: 12,
                        itemGap: 24,
                        textStyle: {color: '#64748b', fontSize: 12}
                    },
                    grid: {top: '60px', left: '2%', right: '2%', bottom: '50px', containLabel: true},
                    xAxis: {
                        type: 'category',
                        data: ['Shift Pagi', 'Shift Siang', 'Shift Malam'],
                        axisLine: {lineStyle: {color: '#cbd5e1'}},
                        axisLabel: {color: '#64748b', fontSize: 12, margin: 12}
                    },
                    yAxis: {
                        type: 'value',
                        axisLine: {show: false},
                        axisLabel: {color: '#94a3b8', fontSize: 11},
                        splitLine: {lineStyle: {type: 'solid', color: '#f1f5f9'}}
                    },
                    series: [
                        {
                            name: 'Running', type: 'bar',
                            // Menghapus properti 'stack' agar grafik berdiri sendiri secara berdampingan (Grouped)
                            itemStyle: {color: '#2ecc71', borderRadius: [4, 4, 0, 0]},
                            label: {show: true, position: 'top', formatter: p => p.value > 0 ? p.value.toFixed(0) : '', textStyle: {fontSize: 10, color: '#64748b'}},
                            data: [totals["pagi"].running, totals["siang"].running, totals["malam"].running]
                        },
                        {
                            name: 'No Response', type: 'bar',
                            itemStyle: {color: '#e74c3c', borderRadius: [4, 4, 0, 0]},
                            label: {show: true, position: 'top', formatter: p => p.value > 0 ? p.value.toFixed(0) : '', textStyle: {fontSize: 10, color: '#64748b'}},
                            data: [totals["pagi"].noresp, totals["siang"].noresp, totals["malam"].noresp]
                        },
                        {
                            name: 'Ganti Benang', type: 'bar',
                            itemStyle: {color: '#3498db', borderRadius: [4, 4, 0, 0]},
                            label: {show: true, position: 'top', formatter: p => p.value > 0 ? p.value.toFixed(0) : '', textStyle: {fontSize: 10, color: '#64748b'}},
                            data: [totals["pagi"].benang, totals["siang"].benang, totals["malam"].benang]
                        },
                        {
                            name: 'Putus/Problem', type: 'bar',
                            itemStyle: {color: '#f1c40f', borderRadius: [4, 4, 0, 0]},
                            label: {show: true, position: 'top', formatter: p => p.value > 0 ? p.value.toFixed(0) : '', textStyle: {fontSize: 10, color: '#64748b'}},
                            data: [totals["pagi"].problem, totals["siang"].problem, totals["malam"].problem]
                        },
                        {
                            name: 'No Order', type: 'bar',
                            itemStyle: {color: '#2c3e50', borderRadius: [4, 4, 0, 0]},
                            label: {show: true, position: 'top', formatter: p => p.value > 0 ? p.value.toFixed(0) : '', textStyle: {fontSize: 10, color: '#64748b'}},
                            data: [totals["pagi"].noorder, totals["siang"].noorder, totals["malam"].noorder]
                        }
                    ]
                };
                myChart.setOption(options, true);
            }

            var dataShift = {};

            function renderTable() {
                const tbody = document.getElementById('table-body');
                let htmlRows = '';
                ["pagi", "siang", "malam"].forEach(shift => {
                    let s = totals[shift];

                    var textHours = structuredClone(s);
                    textHours.running = converMinute(s.running);
                    textHours.noresp = converMinute(s.noresp);
                    textHours.benang = converMinute(s.benang);
                    textHours.problem = converMinute(s.problem);
                    textHours.noorder = converMinute(s.noorder);
                    textHours.efficiency = (s.efficiency == undefined) ? 0 : s.efficiency;
                    htmlRows += `
                <tr>
                    <td class="fw-semibold text-start text-dark" style="border-left: 4px solid var(--text-muted);">Shift ${shift}</td>
                    <td class="mono text-success">${textHours.running}</td>
                    <td class="mono text-danger">${textHours.noresp}</td>
                    <td class="mono text-primary">${textHours.benang}</td>
                    <td class="mono text-warning fw-semibold">${textHours.problem}</td>
                    <td class="mono text-dark">${textHours.noorder}</td>
                    <td class="mono fw-semibold text-secondary">${s.total.themeFormat()}</td>
                    <td class="mono fw-bold text-dark">${textHours.efficiency.toFixed(2)}%</td>
                </tr>
            `;

                });
                tbody.innerHTML = htmlRows;
            }

            Number.prototype.themeFormat = function () {
                return this.toFixed(1).replace(/\d(?=(\d{3})+\.)/g, '$&,');
            };

            $(function () {

                const applyReport = (() => {
                    totals = {
                        "pagi": {
                            "running": 0,
                            "noresp": 0,
                            "benang": 0,
                            "problem": 0,
                            "noorder": 0,
                            "total": 0
                        },
                        "siang": {
                            "running": 0,
                            "noresp": 0,
                            "benang": 0,
                            "problem": 0,
                            "noorder": 0,
                            "total": 0
                        },
                        "malam": {
                            "running": 0,
                            "noresp": 0,
                            "benang": 0,
                            "problem": 0,
                            "noorder": 0,
                            "total": 0
                        }
                    };
                    renderChart();
                });
                $(".select2").select2({
                    allowClear: true,
                    placeholder: "All"
                });
                $(".mesin").on('change', function (e) {
                    applyReport();
                });
                $('#tanggal').daterangepicker({
                    endDate: moment().startOf('day'),
                    startDate: moment().startOf('month'),
                    minYear: 2026,
                    maxDate: new Date(),
                    minDate: "2026/04/01",
                    locale: {
                        format: 'YYYY-MM-DD'
                    }
                });
                $('#tanggal').on('apply.daterangepicker', function (ev, picker) {
                    applyReport();
                });
                const chartDom = document.getElementById('shift_comparison_chart');
                myChart = echarts.init(chartDom);
                renderChart();

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
                            tbl: totals,
                            mesin: $(".mesin").val()
                        },
                        url: "<?php echo base_url(); ?>report/kinerjamesin/export",
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
            }
            );

            const converMinute = ((minute) => {
                var hsl = minute;

                return hsl.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                var value = minute;
                var units = {
//                    "day": 24 * 60,
                    "hour": 60,
                    "min": 1
                };

                var result = [];


                for (var name in units) {
                    var p = Math.floor(value / units[name]);
                    if (p == 1)
                        result.push(" " + p + " " + name);
                    if (p >= 2)
                        result.push(" " + p + " " + name + "s");
                    value %= units[name];
                }

                return result;


            });
        </script>
    </body>
</html>