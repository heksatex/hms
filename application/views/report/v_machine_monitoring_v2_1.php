<html lang="en">
    <head>
        <?php $this->load->view("admin/_partials/head.php") ?>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&family=JetBrains+Mono:wght@700&display=swap" rel="stylesheet">
        <link rel="stylesheet" type="text/css" href="<?= base_url('dist/css/slider.css'); ?>" />
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
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

            .live-bar {
                background: var(--card-light);
                border-bottom: 1px solid #dee2e6;
                padding: 8px 25px;
                box-shadow: 0 2px 4px rgba(0,0,0,0.05);
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
                padding: 15px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.03);
            }
            .oee-value {
                font-size: 3.2rem;
                font-weight: 900;
                line-height: 1;
                color: var(--primary-text);
            }

            .counter-pill {
                background: #f8f9fa;
                border-radius: 8px;
                padding: 8px 15px;
                border-left: 4px solid #dee2e6;
                text-align: center;
            }
            .c-num {
                font-family: 'JetBrains Mono', monospace;
                font-size: 2rem;
                font-weight: 800;
                line-height: 1;
            }
            .c-lab {
                font-size: 0.90rem;
                font-weight: 700;
                color: var(--muted-text);
                margin-top: 2px;
            }

            .br-run {
                border-color: var(--status-green);
                color: var(--status-green);
            }
            .br-creel {
                border-color: #ffc107;
                color: #856404;
            }
            .br-down {
                border-color: #dc3545;
                color: #842029;
            }

            #trend_chart {
                height: 150px;
                width: 100%;
            }
            .chart-card {
                background-color: var(--card-light);
                border-radius: 12px;
                margin: 0 12px;
                padding: 20px;
                border: 1px solid #dee2e6;
                box-shadow: 0 4px 6px rgba(0,0,0,0.03);
            }

            #scroll-container {
                height: calc(100vh - 250px);
                overflow-y: hidden;
                position: relative;
            }
            #scroll-containers {
                height: calc(120vh - 250px);
                overflow-y: hidden;
                position: relative;
            }

            #timeline_tricot {
                width: 100%;
                height: 1600px;
            }

            .legend-pill {
                padding: 4px 12px;
                border-radius: 5px;
                font-size: 1rem;
                font-weight: 700;
                margin-left: 8px;
                border: 1px solid rgba(0,0,0,0.05);
            }
            h5 {
                color: var(--muted-text);
                font-weight: 800;
                font-size: 0.85rem;
                margin-bottom: 15px;
            }

            .google-visualization-tooltip {
                background-color: #ffffff !important;
                border: 1px solid #dee2e6 !important;
                box-shadow: 0 4px 10px rgba(0,0,0,0.1) !important;
                border-radius: 5px !important;
                padding: 8px !important;
                color: #212529 !important;
                font-family: 'Inter' !important;
            }
            .d-flex{
                display: flex;
            }
            .justify-content-between {
                justify-content: space-between;
            }
            .align-items-center{
                align-items: center;
            }
            .mb-3{
                margin-bottom: 1rem;
            }

            @keyframes placeHolderShimmer{
                0%{
                    background-position: -468px 0
                }
                100%{
                    background-position: 468px 0
                }
            }
            .linear-background {
                animation-duration: 1s;
                animation-fill-mode: forwards;
                animation-iteration-count: infinite;
                animation-name: placeHolderShimmer;
                animation-timing-function: linear;
                background: #f6f7f8;
                background: linear-gradient(to right, #eeeeee 8%, #dddddd 18%, #eeeeee 33%);
                background-size: 1000px 104px;

                position: relative;
                overflow: hidden;
            }

            .autoplay-progress {
                position: absolute;
                top: 0px;
                z-index: 10;
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                color: var(--swiper-theme-color);
            }

            .autoplay-progress svg {
                --progress: 0;
                position: absolute;
                left: 0;
                top: 0px;
                z-index: 10;
                stroke-width: 2px;
                stroke: var(--swiper-theme-color);
                fill: none;
                stroke-dashoffset: calc(140% * (1 - var(--progress)));
                stroke-dasharray: 140%;
                /* transform: rotate(-90deg); */
            }

            .card {
                box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
                transition: 0.3s;
                width: 9%;
                float: left;
                height: 16em;
                margin: 5px 5px 5px 5px;
            }

            /*            .card:hover {
                            box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2);
                        }*/

            .container {
                padding: 8px 8px;
                width: 100%;
                text-align: center;
            }
            @media screen and (max-width:1200px) {
                .card {
                    width: 10%;
                }
            }
            @media screen and (max-width:1000px) {
                .card {
                    width: 12%;
                }
            }
            img.center {
                display: block;
                margin-left: auto;
                margin-right: auto;
                width: 50%;
            }
            .durasi-text {
                font-size: 11px;
            }

        </style>
        <meta http-equiv="cache-control" content="no-cache">
    </head>
    <body class="hold-transition skin-black fixed sidebar-mini sidebar-collapse">
        <div class="wrapper">
            <div class="box">
                <div class="box-header">
                    <div class="live-bar d-flex justify-content-between align-items-center">

                        <div class="brand">HMS CONTROL PANEL</div>
                        <div class="timestamp" id="realtime-clock">LOADING...</div>
                        <select class="mesin-select2" id="mesin">
                            <option value=""></option>
                            <?php
                            foreach ($allMesin as $key => $value) {
                                ?>
                                <option value="<?= $value->dept_id ?>" <?= ($value->dept_id === $dept) ? "selected" : "" ?>><?= $value->nama ?></option>
                                <?php
                            }
                            ?>
                        </select>

                    </div>

                </div>
                <div class="box-body">
                    <div class="swiper mySwiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="container-fluid">
                                    <div class="summary-wrapper">
                                        <div class="row align-items-center">
                                            <div class="col-md-6 border-end border-light">
                                                <div class="row align-items-center">
                                                    <!--                                                    <div class="col-md-4 text-center">
                                                                                                            <div class="small fw-bold text-muted text-uppercase mb-1" style="font-size: 0.55rem;">Today's OEE</div>
                                                                                                            <div class="oee-value">84.2%</div>
                                                                                                        </div>-->

                                                    <div class="d-flex">
                                                        <?php
                                                        foreach ($status as $key => $value) {
                                                            ?>
                                                            <div class="counter-pill" style="color: <?= $value['warna'] ?>;border-color: <?= $value['warna'] ?>">
                                                                <div class="c-num stt-<?= $key ?>"><?= $value["jumlah"] ?></div><div class="c-lab"><?= $value["stt"] ?></div>

                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="col-md-6 ps-4">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <!--<div class="small fw-bold text-muted text-uppercase" style="font-size: 0.6rem;">30-Day OEE Trend Analysis</div>-->
                                                    <!--<div class="small fw-bold" style="color: var(--accent-blue); font-size: 0.7rem;">MTD AVG: 81.5%</div>-->
                                                </div>
                                                <div id="trend_chart" class="linear-background"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="chart-card">
                                        <div class="d-flex justify-content-between align-items-center mb-3"
                                             <div class="pull-left">
                                                <h5 class="text-uppercase m-0">Live Utilization Timeline (<span id="unit">0</span>) Units</h5>
                                                <div class="d-flex">
                                                    <?php foreach ($status as $key => $value) {
                                                        ?>
                                                        <div class="legend-pill" style="background:<?= $value['warna'] ?>; color:#ffffff"><?= $value["stt"] ?></div>
                                                    <?php }
                                                    ?>
                                                </div>
                                            </div>
                                            <div id="scroll-container">
                                                <div id="timeline_tricot"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="col-md-12">
                                        <div id="scroll-containers">
                                            <?php
                                            $exist = [];
                                            $sumRed = 0;
                                            $sumYel = 0;
                                            $sumGr = 0;
                                            foreach ($mesin as $key => $value) {
                                                $durasis = $durasi["d{$value->devid}"];
                                                $logo = "mark_success";
                                                $ttlDwn = $value->downtime / $value->total;
                                                $ttlUp = $value->uptime / $value->total;
                                                $value->state = $durasis->state;
                                                $border = $status[1]["warna"];
                                                if ($value->state != 1 && $durasis->total_down <= 10) {
                                                    $logo = "mark_warning";
                                                    $border = $status[$value->state]["warna"];
                                                    $sumYel += 1;
                                                } else if ($value->state != 1 && $durasis->total_down >= 11) {
                                                    $logo = "mark_danger";
                                                    $border = $status[$value->state]["warna"];
                                                    $sumRed += 1;
                                                } else {
                                                    $sumGr += 1;
                                                }
                                                ?>
                                                <!--<div class="col-lg-2 col-md-4 col-xs-6">-->
                                                <div class="card card-<?= "d{$value->devid}" ?>" data-durasi_up="<?= $durasis->total_up ?>" data-durasi_down="<?= $durasis->total_down ?>"
                                                     data-state="<?= $value->state ?>" data-totaldown = "<?= $ttlDwn ?>" data-total="<?= $value->total ?>" data-uptime="<?= $ttlUp ?>" data-downtime="<?= $ttlDwn ?>"
                                                     style="border: 1px solid <?= $border ?>;">
                                                    <div class="container" style="height: 25%;">
                                                        <b><?= "{$value->nama_mesin}" ?></b>
                                                    </div>
                                                    <img class="img-<?= "d{$value->devid}" ?> center statuss" data-status="<?= $logo ?>" src="<?= base_url("dist/img/{$logo}.png") ?>" alt="Avatar">
                                                    <div class="container" style="word-wrap: break-word;">
                                                        <p title="Downtime Shift Sekarang">Downtime : <span class="down-<?= "d{$value->devid}" ?>"><?= round(($ttlDwn / $value->total) * 100) ?></span> %</p>
                                                        <span class="durasi-text durasi-<?= "d{$value->devid}" ?>"><?= ($value->state == 0) ? "Running : {$durasis->total_up_text}" : "Stop : {$durasis->total_down_text}" ?></span>
                                                    </div>
                                                </div>

                                                <!--</div>-->
                                            <?php }
                                            ?>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
            <div class="autoplay-progress">
                <svg width="100%" height="1px">
                <line x1="0" y1="0" x2="100%" y2="0" />
                </svg>
                <span></span>
            </div>
            <?php $this->load->view("admin/_partials/js.php") ?>

            <script type="text/javascript" src="<?= base_url('dist/js/sliders.js') ?>"></script>
            <script type="text/javascript">
                var countMesin = parseInt("<?= $count_mesin ?>", 10);
                const progressCircle = document.querySelector('.autoplay-progress svg');
                var swiper = new Swiper('.mySwiper', {
                    spaceBetween: 30,
                    centeredSlides: true,
                    autoplay: {
                        delay: 25000,
                        disableOnInteraction: false
                    },
                    on: {
                        autoplayTimeLeft(s, time, progress) {
                            progressCircle.style.setProperty('--progress', 1 - progress);
                        },
                    }
                });



                var datass, charts, mesin = [], view;
                const optionsChart = {
                    timeline: {
                        groupByRowLabel: true,
                        colorByRowLabel: true,
                        rowLabelStyle: {fontName: 'Inter', fontSize: 13, color: '#212529', bold: true},
                        barLabelStyle: {fontName: 'Inter', fontSize: 9, color: '#1288FD'}
                    },
                    hAxis: {
                        minValue: moment().subtract(24, 'h').toDate(),
                        maxValue: moment().toDate(),
                        format: "HH:MM"
                    },
                    colors: JSON.parse('<?= $warnaStatus ?>'),
                    backgroundColor: '#ffffff'
                };
                function updateClock() {
                    const now = new Date();
                    const options = {weekday: 'short', day: 'numeric', month: 'short'};
                    document.getElementById('realtime-clock').textContent =
                            `${now.toLocaleDateString('id-ID', options).toUpperCase()} | ${now.toLocaleTimeString('id-ID', {hour12: false})}`;
                }
                setInterval(updateClock, 1000);
                updateClock();
                let mac = null;
                google.charts.load('current', {'packages': ['timeline', 'corechart']});
                google.charts.setOnLoadCallback(function () {
                    const dataTable = new google.visualization.DataTable();
                    const container = document.getElementById('timeline_tricot');
                    const chart = new google.visualization.Timeline(container);
                    datass = dataTable;
                    view = new google.visualization.DataView(datass);
                    charts = chart;
                    drawCharts(mac, dataTable, chart);
                });
                function drawCharts(mac, dataTable, chart) {

                    drawTimeline(mac, dataTable, chart);
                    drawTrendChart();
                    if (countMesin > 10)
                        initAutoScroll();
                    initAutoScrolls();
                }
                const  asData = ((mac) => {
                    return $.ajax({
                        type: "post",
                        data: {
                            dept: $("#mesin").val()
                        },
                        url: "<?php echo base_url(); ?>report/machinemonitoringv2/get_items",
                        //                    beforeSend: function (xhr) {
                        //                        please_wait((() => {
                        //
                        //                        }));
                        //                    },
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {}, 200);
                        }
                    });
                });
                const updateData = (() => {
                    return $.ajax({
                        type: "post",
                        url: "<?php echo base_url(); ?>report/machinemonitoringv2/ins_timeline",

                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {}, 200);
                        }
                    });
                });
                async function drawTimeline(mac, dataTable, chart) {
                    let rows = [];
                    let count = 0;
                    let nama_mesin = "";
                    await asData(mac).then((res) => {
                        var dt = res.data;
                        dt.forEach((sd, idx) => {
                            if (nama_mesin !== sd.nama_mesin)
                                count += 1;
                            rows.push([sd.nama_mesin, "", sd.warna_status, moment(sd.start).toDate(), moment(sd.end).toDate()]);
                            nama_mesin = sd.nama_mesin;
                        });

                    });

                    dataTable.addColumn({type: 'string', id: 'Machine'});
                    dataTable.addColumn({type: 'string', id: 'Status'});
                    dataTable.addColumn({type: 'string', role: 'style'});
                    dataTable.addColumn({type: 'date', id: 'Start'});
                    dataTable.addColumn({type: 'date', id: 'End'});
                    dataTable.addRows(rows);
                    $("#unit").html(count);
                    chart.draw(dataTable, optionsChart);
                }

                const  asDataGrafik = (() => {
                    return $.ajax({
                        type: "post",
                        data: {
                            dept: $("#mesin").val()
                        },
                        url: "<?php echo base_url(); ?>report/machinemonitoringv2/get_grafiks",
                        //                    beforeSend: function (xhr) {
                        //                        please_wait((() => {
                        //
                        //                        }));
                        //                    },
                        complete: function (jqXHR, textStatus) {
                            unblockUI(function () {}, 200);
                        }
                    });
                });

                async function drawTrendChart() {
                    const data = new google.visualization.DataTable();
                    data.addColumn('string', 'Tanggal');
                    data.addColumn('number', 'Running %');
                    data.addColumn('number', 'No Response %');
                    data.addColumn('number', 'Ganti Benang %');
                    data.addColumn('number', 'Putus / Problem %');
                    data.addColumn('number', 'No Order / Tunggu Benang %');
                    let trendRows = [];
                    await asDataGrafik().then((res) => {
                        var dt = res.data;
                        dt.forEach((sd, idx) => {
                            let dateLabel = moment(sd.tgl).format("DD MMM");
                            let running = ((sd.running / sd.total) / sd.total * 100);
                            let no_response = ((sd.no_response / sd.total) / sd.total * 100);
                            let ganti_benang = ((sd.ganti_benang / sd.total) / sd.total * 100);
                            let problem = ((sd.problem / sd.total) / sd.total * 100);
                            let no_order = ((sd.no_order / sd.total) / sd.total * 100);
                            trendRows.push([dateLabel, parseFloat(running.toFixed(1)), parseFloat(no_response.toFixed(1)),
                                parseFloat(ganti_benang.toFixed(1)), parseFloat(problem.toFixed(1)), parseFloat(no_order.toFixed(1))]);
                        });
                    });
                    data.addRows(trendRows);
                    elChart = document.getElementById('trend_chart');
                    const chart = new google.visualization.AreaChart(elChart);
                    chart.draw(data, optionsChart);
                    elChart.classList.remove("linear-background");
                }

                function initAutoScroll(datass = null, chart = null) {
                    const scrollContainer = document.getElementById('scroll-container');
                    let isPaused = false;
                    let isAtTop = true;
                    scrollContainer.addEventListener('mouseenter', () => isPaused = true);
                    scrollContainer.addEventListener('mouseleave', () => isPaused = false);
                    setInterval(() => {
                        if (!isPaused) {
                            if (isAtTop)
                                return;
                            scrollContainer.scrollTop += 1;
                            if (scrollContainer.scrollTop >= (scrollContainer.scrollHeight - scrollContainer.offsetHeight)) {
                                isPaused = true;
                                setTimeout(() => {
                                    scrollContainer.scrollTop = 0;
                                    isAtTop = true;
                                    isPaused = false;
                                    setTimeout(() => {
                                        isAtTop = false;
                                    }, 3000);
                                }, 2000);
                            }
                        }
                    }, 50);
                    setTimeout(() => {
                        isAtTop = false;
                    }, 3000);
                }
                function initAutoScrolls() {
                    const scrollContainer = document.getElementById('scroll-containers');
                    let isPaused = false;
                    let isAtTop = true;
                    scrollContainer.addEventListener('mouseenter', () => isPaused = true);
                    scrollContainer.addEventListener('mouseleave', () => isPaused = false);
                    setInterval(() => {
                        if (!isPaused) {
                            if (isAtTop)
                                return;
                            scrollContainer.scrollTop += 1;
                            if (scrollContainer.scrollTop >= (scrollContainer.scrollHeight - scrollContainer.offsetHeight)) {
                                isPaused = true;
                                setTimeout(() => {
                                    scrollContainer.scrollTop = 0;
                                    isAtTop = true;
                                    isPaused = false;
                                    setTimeout(() => {
                                        isAtTop = false;
                                    }, 3000);
                                }, 2000);
                            }
                        }
                    }, 50);
                    setTimeout(() => {
                        isAtTop = false;
                    }, 3000);
                }
                var loop = 0;
                const  updateContent = (async() => {
                    loop = 0;
                    var rw = [];
                    var rr = null;
                    await updateData();
                    await asData(null).then((res) => {
                        var dt = res.data;
                        dt.forEach((sd, idx) => {
                            rw.push([sd.nama_mesin, "", sd.warna_status, moment(sd.start).toDate(), moment(sd.end).toDate()]);
                        });
                    });
                    if (rw.length > 0) {
                        datass.removeRows(0, datass.getNumberOfRows());
                        datass.addRows(rw);
                        optionsChart.sortColumn = 1;
                        optionsChart.hAxis.minValue = moment().subtract(24, 'h').toDate();
                        optionsChart.hAxis.maxValue = moment().toDate();
                        charts.draw(datass, optionsChart);
                        //                    var view = new google.visualization.DataView(datass);
                        //                    view.setColumns([0, 1, 2, 3, 4]);
                        //                    view.setRows(view.getFilteredRows([{column: 3, minValue: rr}]));
                        //                    
                        //                    charts.draw(view, optionsChart);
                    }
                });
                var ipSocket = "<?= $ip_socket ?>";
                const socket = new WebSocket(`${ipSocket}`);
                socket.onopen = function () {
                    console.log("Connected to server");
                };
                //sampai 45;
                socket.onmessage = async function (event) {
                    loop += 1;
                    var data = JSON.parse(event.data);
                    if (data["version"] != undefined && data["version"] == 2) {
                        updateContents(data["data"]);
                        if (loop > 35)
                            await updateContent();
                    }

                };


                const updateSummary = (() => {
                    const myElements = document.querySelectorAll('.statuss');
                    var data = {
                        mark_success: 0,
                        mark_warning: 0,
                        mark_danger: 0
                    };
                    myElements.forEach(element => {
                        var ee = element.getAttribute("data-status");
                        data[ee] += 1;
                    });
                    Object.keys(data).forEach(key => {
                        $(`.sum-${key}`).attr("data-val", data[key]);
                        $(`.sum-${key}`).html(data[key]);
                        //                    console.log(key, data[key]); // Prints "name Jean-Luc Picard" then "rank Captain"
                    });
                    var dt = JSON.parse('<?= $state ?>');
                    dataMesin.forEach(el => {
                        dt[el.state] += 1;
                    });
                    Object.keys(dt).forEach(key => {
                        $(`.stt-${key}`).html(dt[key]);
                    });
                });

                const converMinute = ((minute) => {
                    var value = minute;
                    var units = {
                        "day": 24 * 60,
                        "hour": 60,
                        "min": 1
                    };

                    var result = [];

                    if (minute >= 1440) {
                        const rtf = new Intl.RelativeTimeFormat("en", {numeric: "auto"});
                        result.push(rtf.format(0 - Math.floor(minute / 1440), "day"));
                    } else {
                        for (var name in units) {
                            var p = Math.floor(value / units[name]);
                            if (p == 1)
                                result.push(" " + p + " " + name);
                            if (p >= 2)
                                result.push(" " + p + " " + name + "s");
                            value %= units[name];
                        }
                    }
                    return result;


                });


                var times = "<?= $times ?>";
                var dataMesin = [];
                const updateContents = ((data) => {
                    var stts = JSON.parse('<?= json_encode($status) ?>');
                    var base = "<?= base_url("dist/img/") ?>";
                    $.each(data, function (index, val) {
                        var datas = $(`.card-d${val.devid}`).data();
                        if (datas) {
                            if (dataMesin[`${val.devid}`] === undefined) {
                                dataMesin[`${val.devid}`] = {
                                    state: parseInt(datas.state),
                                    total: parseInt(datas.total),
                                    totaldown: parseInt(datas.downtime),
                                    durasi_down: datas.durasi_down,
                                    uptime: datas.uptime,
                                    durasi_up: datas.durasi_up
                                };
                            }
                            var stt = datas.state;
                            dataMesin[`${val.devid}`].state = parseInt(val.state);
                            dataMesin[`${val.devid}`].total += 1;
                            dataMesin[`${val.devid}`].totaldown += 1;
                            var animasi = false;
                            var dtt = "";
                            var border = stts[val.state]["warna"];;
                            var status = "mark_success";
                            var logo = base + "/mark_success.png";
                            switch (true) {
                                case (parseInt(val.state) != 1 && dataMesin[`${val.devid}`].durasi_down <= 9):
                                    dataMesin[`${val.devid}`].downtime += 1;
                                    dataMesin[`${val.devid}`].durasi_down += 1;
                                    logo = base + "mark_warning.png";
                                    status = "mark_warning";
                                    dataMesin[`${val.devid}`].uptime = 0;
                                    dataMesin[`${val.devid}`].durasi_up = 0;
                                    animasi = (dataMesin[`${val.devid}`].durasi_down <= 1) ? true : false;
                                    var dt = converMinute(dataMesin[`${val.devid}`].durasi_down);
                                    dtt = "Stop : " + dt.join();
//                                    border = stt[dataMesin[`${val.devid}`].state]["warna"];
                                    break;
                                case (parseInt(val.state) != 1 && dataMesin[`${val.devid}`].durasi_down >= 10) :
                                    logo = base + "mark_danger.png";
                                    status = "mark_danger";
                                    dataMesin[`${val.devid}`].downtime += 1;
                                    dataMesin[`${val.devid}`].durasi_down += 1;
                                    dataMesin[`${val.devid}`].uptime = 0;
                                    dataMesin[`${val.devid}`].durasi_up = 0;
                                    animasi = (dataMesin[`${val.devid}`].durasi_down <= 11) ? true : false;
                                    var dt = converMinute(dataMesin[`${val.devid}`].durasi_down);
                                    dtt = "Stop : " + dt.join();
//                                    border = "red";
                                    break;
                                default:
                                    dataMesin[`${val.devid}`].uptime += 1;
                                    dataMesin[`${val.devid}`].durasi_up += 1;
                                    dataMesin[`${val.devid}`].downtime = 0;
                                    dataMesin[`${val.devid}`].durasi_down = 0;
                                    status = "mark_success";
                                    animasi = (stt != val.state) ? true : false;
                                    var dt = converMinute(dataMesin[`${val.devid}`].durasi_up);
                                    dtt = "Running : " + dt.join();
                                    break;
                            }


                            if (animasi) {
                                var image = $(`.img-d${val.devid}`);

                                image.fadeOut("fast", function () {
                                    $(`.img-d${val.devid}`).attr("src", logo);
                                    $(`.img-d${val.devid}`).attr("data-status", status);
                                    image.fadeIn('fast');
                                });
                                $(`.card-d${val.devid}`).css("border", `1px solid ${border}`);
                            }
                            $(`.durasi-d${val.devid}`).html(dtt);

                            //                        $(`.card-d${val.devid}p${val.port}`).attr("data-total", datas.total);
                            //                        $(`.card-d${val.devid}p${val.port}`).attr("data-downtime", datas.downtime);
                            //                        $(`.card-d${val.devid}p${val.port}`).attr("data-durasi_down", datas.durasi_down);
                            //                        $(`.card-d${val.devid}p${val.port}`).attr("data-durasi_up", datas.durasi_up);
                            var ttlDwn = (dataMesin[`${val.devid}`].totaldown / dataMesin[`${val.devid}`].total) * 100;
                            $(`.down-d${val.devid}`).html(ttlDwn.toFixed(0));
                        }

                    });
                    updateSummary();
                });



                $(function () {
                    $(".mesin-select2").select2({
                        allowClear: true,
                        placeholder: "Filter Departemen"
                    });

                    $("#mesin").on("change", function () {
                        var dept_id = $(this).val();
                        var dept_nama = $("#mesin :selected").text();
                        location.href = "<?= base_url("report/machinemonitoringv2") ?>" + `?dept=${dept_id}&nm=${dept_nama}`;
                    });

                    $(".sum-mark_danger").attr("data-val",<?= $sumRed ?>);
                    $(".sum-mark_danger").html("<?= $sumRed ?>");

                    $(".sum-mark_warning").attr("data-val",<?= $sumYel ?>);
                    $(".sum-mark_warning").html("<?= $sumYel ?>");

                    $(".sum-mark_success").attr("data-val",<?= $sumGr ?>);
                    $(".sum-mark_success").html("<?= $sumGr ?>");


                })
            </script>
    </body>
</html>